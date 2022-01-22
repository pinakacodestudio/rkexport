<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_return_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Credit_note_report_model', 'Sales_return_report');
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_return_report');
    }
    public function index() {
        $this->viewData['title'] = "Sales Return Report";
        $this->viewData['module'] = "report/Credit_note_report";
          $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales-return-report", "pages/Sales_return_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
   
}