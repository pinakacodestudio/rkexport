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
        $this->viewData['title'] = "Cancelled Orders Report";
        $this->viewData['module'] = "report/Cancelled_orders_report";
        
       

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
    
}