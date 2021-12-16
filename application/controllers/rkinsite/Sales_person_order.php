<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_person_order  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_order');
        $this->load->model('Sales_person_order_model', 'Sales_person_order');
        $this->load->model('User_model', 'User');
    }
    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_order');
        $this->viewData['title'] = "Sales Person Order";
        $this->viewData['module'] = "sales_person_order/Sales_person_order";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Sales Person Order','View sales person order.');
        }
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales_person_order", "pages/sales_person_order.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }
    public function listing() {
        
        $list = $this->Sales_person_order->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $view = "";
            $channellabel="";
            if($datarow->buyerchannelid!=0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }
            $status = "";
            if($datarow->status==0){
                $status = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
            }else if($datarow->status==1){
                $status = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';
            }else if($datarow->status==2){
                $status = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }else if($datarow->status==3){
                $status = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised">Partially</button>';
            }

            if($datarow->remarks!=""){
                $remarks = '<span id="orderremarks'.$datarow->id.'" style="display:none;">'.$datarow->remarks.'</span><a href="javascript:void(0)" onclick="viewreason('.$datarow->id.')">View</a>';
            }else{
                $remarks = "";
            }
            
            if($datarow->salespersonid!=0){
                $commissionamounttext = numberFormat($datarow->commissionamount,2,'.',',');
            }
            $commissionamount = number_format($datarow->commissionamount,2,'.','');
            $commissiondata = $this->Sales_person_order->getSalesPersonProductCommission($datarow->id);
            if(!empty($commissiondata)){
                $str="";
                foreach($commissiondata as $comm){
                    $commissionamount += number_format($comm['commissionamount'],2,'.','');
                    $str .= '<p>'.ucwords($comm['salesperson'])." - ".CURRENCY_CODE." ".numberFormat($comm['commissionamount'],2,'.',',')."</p>";
                }
                $commissionamounttext = '<a title="Commission" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$str.'">'.numberFormat($commissionamount,2,'.',',').'</a>';
            }
            
            $row[] = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->id.'" title="View Order" target="_blank">'.$datarow->orderid.'</a>';
            $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->buyerid.'" title="'.ucwords($datarow->buyername).'" target="_blank">'.$channellabel." ".ucwords($datarow->buyername).' ('.$datarow->buyercode.')</a>';
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = numberFormat($datarow->netamount,2,'.',',');
            // $row[] = $commissionamounttext;
            $row[] = ($datarow->salespersonid!=0)?ucwords($datarow->salespersonname):"-";
            $row[] = $status;
            $row[] = $remarks;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Sales_person_order->count_all(),
                        "recordsFiltered" => $this->Sales_person_order->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
}

?>