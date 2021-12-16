<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Machine_detail extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Machine_detail');
        $this->load->model('Machine_model', 'Machine');
    }

    public function index() {
        $this->viewData['title'] = "View Machine Details";
        $this->viewData['module'] = "machine/View_machine_details";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['machinelist'] = $this->Machine->getMachineList();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("view_machine_details", "pages/view_machine_details.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}?>