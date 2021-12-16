<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_person_history  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_history');
        $this->load->model('Sales_person_history_model', 'Sales_person_history');
        $this->load->model('User_model', 'User');
    }
    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_history');
        $this->viewData['title'] = "Sales Person History";
        $this->viewData['module'] = "sales_person_history/Sales_person_history";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->viewData['routedata'] = $this->Sales_person_history->getRouteBySalesPersonRoute();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Sales Person History','View sales person history.');
        }
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales_person_history", "pages/sales_person_history.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function listing() {
        
        $list = $this->Sales_person_history->get_datatables();
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $Action = "";
            
            $status = "";
            if($datarow->status==0){
                $status = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
            }else if($datarow->status==1){
                $status = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';
            }else if($datarow->status==2){
                $status = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }
            $Action .= '<a class="'.viewmap_class.'" href="'.ADMIN_URL.'sales-person-history/view-sales-person-history/'. $datarow->id.'/'.'" title="'.viewmap_title.'" target="_blank">'.viewmap_text.'</a>';
            
            $row[] = $datarow->employeename;
            $row[] = $datarow->routename;
            $row[] = $datarow->vehiclename;
            $row[] = $this->general_model->displaydate($datarow->startdatetime);
            $row[] = numberFormat($datarow->collection,2,'.',',');
            $row[] = numberFormat($datarow->loosmoney,2,'.',',');
            $row[] = $status;
            $row[] = $Action;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Sales_person_history->count_all(),
                        "recordsFiltered" => $this->Sales_person_history->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function view_sales_person_history($salespersonrouteid) {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Sales Person History";
        $this->viewData['module'] = "sales_person_history/View_sales_person_history";
        
        $salespersonroutedata = $this->Sales_person_history->getSalesPersonHistoryDataById($salespersonrouteid);

        if(empty($salespersonroutedata)){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $this->load->model('Assigned_route_model', 'Assigned_route');
        $this->viewData['memberdata'] = $this->Assigned_route->getMembersByAssignedRoute($salespersonroutedata['assignedrouteid']);
        
        $this->viewData['salespersonroutedata'] = $salespersonroutedata;

        $this->admin_headerlib->add_javascript("view_sales_person_history", "pages/view_sales_person_history.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function getSalesPersonHistoryByMember() {
        
        $PostData = $this->input->post();
        $salespersonrouteid = $PostData['salespersonrouteid'];
        $memberid = $PostData['memberid'];
        
        $this->load->model('Sales_person_tracking_model', 'Sales_person_tracking');
        $routelocationdata = $this->Sales_person_tracking->getSalesPersonRouteByID($salespersonrouteid);
        $mapdata = $this->Sales_person_history->getMapPoints($routelocationdata,$memberid);

        echo json_encode($mapdata);
    }
}

?>