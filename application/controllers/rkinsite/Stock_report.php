<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_report extends Admin_Controller {

    public $viewData = array(); 
    function __construct(){ 
        parent::__construct();
        $this->load->model('Stock_report_model', 'Stock_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Stock_report');
    }
    public function index() {
        $this->viewData['title'] = "Stock Report";
        $this->viewData['module'] = "report/Stock_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("stock-report", "pages/stock_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}