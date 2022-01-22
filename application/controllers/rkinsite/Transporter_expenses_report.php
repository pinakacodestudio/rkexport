<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transporter_expenses_report extends Admin_Controller {

    public $viewData = array(); 
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Transporter_expenses_report');
        $this->load->model('Transporter_expenses_report_model', 'Transporter_expenses_report');
    }
    public function index() {
        $this->viewData['title'] = "Transporter Expenses Report";
        $this->viewData['module'] = "report/Transporter_expenses_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("transporter-expenses-report", "pages/transporter_expenses_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}