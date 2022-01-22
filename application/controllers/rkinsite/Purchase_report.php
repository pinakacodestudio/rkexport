<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_report extends Admin_Controller {

    public $viewData = array(); 
    function __construct(){ 
        parent::__construct();
        $this->load->model('Purchase_report_model', 'Purchase_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Purchase_report');
    }
    public function index() {
        $this->viewData['title'] = "Purchase Report";
        $this->viewData['module'] = "report/Purchase_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("purchase-report", "pages/purchase_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
}