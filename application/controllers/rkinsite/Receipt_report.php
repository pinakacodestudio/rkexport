<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Receipt_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Receipt_report_model', 'Receipt_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Receipt_report');
    }
    public function index() {
        $this->viewData['title'] = "Receipt Report";
        $this->viewData['module'] = "report/Receipt_report";
        
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales_report", "pages/receipt_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    
}