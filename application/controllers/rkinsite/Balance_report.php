<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance_report extends Admin_Controller { 

    public $viewData = array();
    function __construct(){
        parent::__construct(); 
        $this->load->model('Balance_report_model', 'Balance_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Balance_report');
    }
    public function index() {
        $this->viewData['title'] = "Balance Report";
        $this->viewData['module'] = "report/Balance_report";
          $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("balance-report", "pages/balance_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

}