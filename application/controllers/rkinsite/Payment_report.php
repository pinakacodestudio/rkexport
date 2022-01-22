<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Payment_report extends Admin_Controller { 

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Payment_report_model', 'Payment_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Payment_report');
    }
    public function index() {
        $this->viewData['title'] = "Payment Report";
        $this->viewData['module'] = "report/Payment_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("payment-report", "pages/payment_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}