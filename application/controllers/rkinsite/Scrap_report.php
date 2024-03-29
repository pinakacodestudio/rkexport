<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Scrap_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Scrap_report');
        $this->load->model('Scrap_report_model', 'Scrap_report');
    }
    public function index() {
        $this->viewData['title'] = "Scrap Report";
        $this->viewData['module'] = "report/Scrap_report";
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("scrap-report", "pages/scrap_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
  
}