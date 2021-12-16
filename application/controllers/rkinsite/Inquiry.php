<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inquiry extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();    

        $this->viewData = $this->getAdminSettings('submenu', 'Inquiry');
    }
    
    public function index() {
      $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "Inquiry";
        $this->viewData['module'] = "inquiry/Inquiry";

       
        $this->load->model('Followup_statuses_model', 'Followup_statuses');
        $this->viewData['followupstatusesdata'] = $this->Followup_statuses->getActiveFollowupstatus();

        $this->load->model('Followup_type_model', 'Followup_type');
        $this->viewData['followuptypedata'] = $this->Followup_type->getActiveFollowtype();
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("Inquiry","pages/inquiry.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function view_daily_followup(){
      $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "View Machine Details";
      $this->viewData['module'] = "daily_followup/View_daily_followup";
     
      
      
      $this->admin_headerlib->add_javascript("Daily_followup","pages/view_daily_followup.js");

      //$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
      $this->admin_headerlib->add_javascript_plugins("form-daterangepicker", "form-daterangepicker/daterangepicker.js");
      $this->admin_headerlib->add_plugin("form-daterangepicker", "form-daterangepicker/daterangepicker.css");
      $this->admin_headerlib->add_plugin("form-daterangepicker", "form-daterangepicker/moment.min.js");

      $this->load->view(ADMINFOLDER.'template',$this->viewData);

  }

  
}