<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_person_tracking  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_tracking');
        $this->load->model('Sales_person_tracking_model', 'Sales_person_tracking');
        $this->load->model('User_model', 'User');
    }
    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_tracking');
        $this->viewData['title'] = "Sales Person Tracking";
        $this->viewData['module'] = "sales_person_tracking/Sales_person_tracking";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Sales Person Tracking','View sales person tracking.');
        }
        
        /* 
        $this->viewData['trackroutedata'] = $this->Sales_person_tracking->getSalesPersonRoute();
  
        $this->load->model('Track_route_task_model', 'Track_route_task');
        $this->viewData['taskdata'] = $this->Track_route_task->getTrackRouteTaskByFollowup(16);
        $this->viewData['mapdata'] = $this->Track_route_task->getMapPoints($this->viewData['trackroutedata']); */
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales_person_tracking", "pages/sales_person_tracking.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function track_sales_person() {
        
        $PostData = $this->input->post();
        $employeeid = $PostData['employeeid'];
        $vehicleid = isset($PostData['vehicleid'])?$PostData['vehicleid']:0;
        $routeid = $PostData['routeid'];
        $date = $this->general_model->convertdate($PostData['date']);

        $routelocationdata = $this->Sales_person_tracking->getSalesPersonRoute($employeeid,$vehicleid,$routeid,$date);
        $mapdata = $this->Sales_person_tracking->getMapPoints($routelocationdata);

        echo json_encode($mapdata);
    }

}

?>