<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Minimum_stock_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Minimum_stock_report');
        $this->load->model('Minimum_stock_report_model', 'Minimum_stock_report');
    }
    public function index() {
        $this->viewData['title'] = "Minimum Stock Report";
        $this->viewData['module'] = "report/Minimum_stock_report";
        
       
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}