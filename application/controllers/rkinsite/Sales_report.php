<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Sales_report_model', 'Sales_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_report');
    }
    public function index() {
        $this->viewData['title'] = "Sales Report";
        $this->viewData['module'] = "report/Sales_report";
        
       

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
}