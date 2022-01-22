<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pending_purchase_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Pending_purchase_report_model', 'Pending_purchase_report');
        $this->viewData = $this->getAdminSettings('submenu', 'Pending_purchase_report');
    }
    public function index() {
        $this->viewData['title'] = "Pending Purchase Report";
        $this->viewData['module'] = "report/Pending_purchase_report";
          $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("pending-purchase-report", "pages/pending_purchase_report.js"); 
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}