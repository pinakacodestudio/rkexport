<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Cancelled_orders_report extends Admin_Controller {

    public $viewData = array(); 
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Cancelled_orders_report');
        $this->load->model('Cancelled_orders_report_model', 'Cancelled_orders_report');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Cancelled Orders Report";
        $this->viewData['module'] = "report/Cancelled_orders_report";
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("cancelled-orders-report", "pages/cancelled_orders_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
    
}