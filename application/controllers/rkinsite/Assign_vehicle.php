<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assign_vehicle extends Admin_Controller
{

    public $viewData = array();
    function __construct()
    {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Assign_vehicle');
        $this->load->model('Assign_vehicle_model', 'Assign_vehicle');
    }
    public function index() {
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Assign Vehicle";
        $this->viewData['module'] = "assign_vehicle/Assign_vehicle";
        
        $this->viewData['assignvehicledata'] = $this->Assign_vehicle->getAssignVehicleData();
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Assign Vehicle','View assign vehicle.');
        }

        $this->load->model('Site_model', 'Site');
        $this->viewData['sitedata'] = $this->Site->getActiveSiteData();
        
        $this->admin_headerlib->add_javascript("assign_vehicle", "pages/assign_vehicle.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");    
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_assign_vehicle() {

        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Assign Vehicle";
        $this->viewData['module'] = "assign_vehicle/Add_assign_vehicle";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
        
        $this->load->model('Site_model', 'Site');
        $this->viewData['sitedata'] = $this->Site->getActiveSiteData();
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_assign_vehicle", "pages/add_assign_vehicle.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function edit_assign_vehicle($id) {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Assign Vehicle";
        $this->viewData['module'] = "assign_vehicle/Add_assign_vehicle";
        $this->viewData['action'] = "1"; //Edit

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Site_model', 'Site');
        $this->viewData['sitedata'] = $this->Site->getActiveSiteData();

        $this->viewData['assignvehicledata'] = $this->Assign_vehicle->getAssignVehicleDataByID($id);
        if (empty($this->viewData['assignvehicledata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_assign_vehicle", "pages/add_assign_vehicle.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function assign_vehicle_add() {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $siteid = $PostData['siteid'];
        $date = $PostData['assignvehicledate'];
        if(!is_array($PostData['vehicleid'])){
            $vehicles = array($PostData['vehicleid']);
        }else{
            $vehicles = $PostData['vehicleid'];
        }
        foreach ($vehicles as $key => $value) {
            $vehicleid = $PostData['vehicleid'][$key];
            $this->Assign_vehicle->_where = ("vehicleid=" . $vehicleid . " AND siteid=" . $siteid);
            $Count = $this->Assign_vehicle->CountRecords();

            if ($Count == 0) {
                $insertdata[] = array(
                    "vehicleid" => $vehicleid,
                    "siteid" => $siteid,
                    "date" => ($date != "" ? $this->general_model->convertdate($date) : ""),
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
            }
        }
        if (!empty($insertdata)) {
            $this->Assign_vehicle->add_batch($insertdata);
            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(1, 'Assign vehicle', 'Add new assign vehicle.');
            }
        }
        echo 1;
    }

    public function update_assign_vehicle() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $assignvehicleid = $PostData['id'];
        $vehicleid = $PostData['vehicleid'];
        $siteid = $PostData['siteid'];
        $date = $PostData['assignvehicledate'];

        $this->Assign_vehicle->_where = array("id<>"=>$assignvehicleid,"vehicleid"=>$vehicleid,"siteid"=>$siteid);
        $Count = $this->Assign_vehicle->CountRecords();

        if ($Count == 0) {
            $updatedata = array(
                "vehicleid" => $vehicleid,
                "siteid" => $siteid,
                "date" => ($date != "" ? $this->general_model->convertdate($date) : ""),
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $this->Assign_vehicle->_where = array("id" => $assignvehicleid);
            $Edit = $this->Assign_vehicle->Edit($updatedata);
            if ($Edit) {
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Assign Vehicle', 'Edit assign vehicle.');
                }
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2;
        }
    }

    public function check_assign_vehicle_use() {
        
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach ($ids as $row) {
            /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE 
                    id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
            if($query->num_rows() > 0){
                $count++;
            } */
        }
        echo $count;
    }

    public function delete_mul_assign_vehicle() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        
        $count = 0;
        foreach ($ids as $row) {

            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(3, 'Assign Vehicle', 'Delete assign vehicle.');
            }
            $this->Assign_vehicle->Delete(array("id" => $row));
        }
    }

    public function exportToExcelAssignVehicle(){
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Assign Vehicle','Export to excel Assign Vehicle.');
        }
        $exportdata = $this->Assign_vehicle->getAssignVehicleDataForExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->sitename!=''?$row->sitename:'-'),
                            ($row->date!='0000-00-00'?$this->general_model->displaydate($row->date):'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                            
                        );
        }  
        $headings = array('Sr. No.','Vehicle Name','Site Name','Date','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Assign Vehicle",$headings,"Assign-Vehicle.xls");

    }
    
    public function exportToPDFAssignVehicle(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Assign Vehicle','Export to PDF Assign Vehicle.');
        }

        $PostData['reportdata'] = $this->Assign_vehicle->getAssignVehicleDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Assign Vehicle';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'assign_vehicle/AssignVehiclePDFFormat', $PostData,true);

        $this->general_model->exportToPDF("Assign-Vehicle.pdf",$header,$html);
    }
    
    public function printAssignVehicle(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Assign Vehicle','Print Assign Vehicle.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Assign_vehicle->getAssignVehicleDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Assign Vehicle';

        $html['content'] = $this->load->view(ADMINFOLDER."assign_vehicle/AssignVehiclePrintFormat.php",$PostData,true);
        echo json_encode($html); 
    }

    public function Transfer_Assign_vehicle(){
        $PostData = $this->input->post();
        $vehicleid = $PostData['vehicleid'];
        
        $assignvehicledata = $this->Assign_vehicle->getAssignVehicleDataByID($vehicleid);
        echo json_encode($assignvehicledata);
    }
}
