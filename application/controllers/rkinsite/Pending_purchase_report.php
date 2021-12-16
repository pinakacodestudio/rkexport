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
        

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}