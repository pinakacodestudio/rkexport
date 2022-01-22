<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Courier_expenses_report extends Admin_Controller {

    public $viewData = array(); 
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Courier_expenses_report');
        $this->load->model('Courier_expenses_report_model', 'Courier_expenses_report');
    }
    public function index() {
        $this->viewData['title'] = "Courier Expenses Report";
        $this->viewData['module'] = "report/Courier_expenses_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("courier-expenses-report", "pages/courier_expenses_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}