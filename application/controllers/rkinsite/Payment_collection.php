<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_collection  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Payment_collection');
        $this->load->model('Payment_collection_model', 'Payment_collection');
        $this->load->model('User_model', 'User');
    }
    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Payment_collection');
        $this->viewData['title'] = "Payment Collection";
        $this->viewData['module'] = "payment_collection/Payment_collection";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        
        $this->viewData['memberdata'] = $this->Payment_collection->getPaymentCollectedMemberList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Payment Collection','View payment collection.');
        }
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("payment_collection", "pages/payment_collection.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }
    public function listing() {
        
        $list = $this->Payment_collection->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $view = "";
            $channellabel="";
            if($datarow->channelid!=0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
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
            }
            $method = $this->Bankmethod[$datarow->method];


            $row[] = '<a href="'.ADMIN_URL.'invoice/view-invoice/'.$datarow->invoiceid.'" title="View Invoice" target="_blank">'.$datarow->invoiceno.'</a>';
            $row[] = ucwords($datarow->employeename);
            $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.$channellabel." ".ucwords($datarow->membername).' ('.$datarow->membercode.')</a>';
            $row[] = numberFormat($datarow->collectedamount,2,'.',',');
            $row[] = numberFormat($datarow->invoiceamount,2,'.',',');
            $row[] = $method;
            $row[] = $datarow->paymentreceiptno;
            $row[] = $this->general_model->displaydate($datarow->transactiondate);
            $row[] = $status;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Payment_collection->count_all(),
                        "recordsFiltered" => $this->Payment_collection->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
}

?>