<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gstr2_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Gstr2_report');
        $this->load->model('Gstr2_report_model', 'Gstr2_report');
    } 
    public function index() {
        $this->viewData['title'] = "GSTR2 Report";
        $this->viewData['module'] = "report/Gstr2_report";
        
       
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}