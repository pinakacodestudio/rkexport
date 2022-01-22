<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gstr1_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){ 
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Gstr1_report');
        $this->load->model('Gstr1_report_model', 'Gstr1_report');
    }
    public function index() {
        $this->viewData['title'] = "GSTR1 Report";
        $this->viewData['module'] = "report/Gstr1_report";
          $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("gstr1-report", "pages/gstr1_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}