<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Abc_inventory_analysis extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Abc_inventory_analysis');
        $this->load->model("Abc_inventory_analysis_model","Abc_inventory_analysis");
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "ABC Inventory Analysis";
        $this->viewData['module'] = "abc_inventory_analysis/Abc_inventory_analysis";

       
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}