<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Transaction_model', 'Transaction');
        $this->viewData = $this->getAdminSettings('submenu', 'Transaction');
    }
    public function index() {
        $this->viewData['title'] = "Transaction";
        $this->viewData['module'] = "transaction/Transaction";
        
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Transaction','View transaction.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Transaction", "pages/transaction.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $list = $this->Transaction->get_datatables();
        // echo $this->db->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $paymenttype = '';
            $channellabel = '';
            
            $finalprice = numberFormat(($datarow->payableamount), 2, ',');
            $transcationcharge = numberFormat($datarow->transcationcharge, 2, ',');

            $row[] = ++$counter;

            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" target="_blank" title="'.ucwords($datarow->membername).'">'.ucwords($datarow->membername).' ('.$datarow->membercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" target="_blank" title="'.$datarow->ordernumber.'">'.$datarow->ordernumber."</a>";
            $row[] = $datarow->transactionid;
            
            if($datarow->paymenttype==1){
                $paymenttype = "COD";
            }else if($datarow->paymenttype==2){
               
                $paymenttype = isset($this->Paymentgatewaytype[$datarow->paymentgetwayid]) ? ucwords($this->Paymentgatewaytype[$datarow->paymentgetwayid]) : '-';
               
            }else if($datarow->paymenttype==3){
                $paymenttype = "Advance Payment";
            }else if($datarow->paymenttype==4){
                $paymenttype = "Partial Payment";
            }else if($datarow->paymenttype==5){
                $paymenttype = "Debit";
            }  
            
            $row[] = $paymenttype;
            
            if($datarow->transactionproof!=''){
                $actions .= '<a href="'.ORDER_INSTALLMENT.$datarow->transactionproof.'" class="'.download_class.'" title="'.download_title.'" download>'.download_text.'</a>'; 
            }
            $row[] = $transcationcharge;
            
            $row[] =  $finalprice;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Transaction->count_all(),
                        "recordsFiltered" => $this->Transaction->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
}