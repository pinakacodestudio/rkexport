<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_return_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){ 
        parent::__construct(); 
        $this->viewData = $this->getAdminSettings('submenu', 'Purchase_return_report');
        $this->load->model('Purchase_return_report_model', 'Purchase_return_report');
        
    }
    public function index() { 
        $this->viewData['title'] = "Purchase Return Report";
        $this->viewData['module'] = "report/Purchase_return_report";
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("purchase_return_report", "pages/purchase_return_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}