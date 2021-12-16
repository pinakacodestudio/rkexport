<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_dashboard extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Vehicle_dashboard');
        $this->load->model('Vehicle_dashboard_model',"Dashboard");
		
    }  
    public function index(){

        $this->viewData['title'] = "Vehicle Dashboard";
        $this->viewData['module'] = "vehicle_dashboard/Vehicle_dashboard";
        $this->viewData['size'] = $this->Dashboard->folderSize("uploaded/".CLIENT_FOLDER);
        //count for tile
        $this->viewData['partycount'] = $this->Dashboard->getPartyCount();
        $this->viewData['vehiclecount'] = $this->Dashboard->getVehicleCount();
        $this->viewData['sitecount'] = $this->Dashboard->getSiteCount();
        $this->viewData['alertdatacount'] = $this->Dashboard->getAlertServicePartCount();

        $this->viewData['alertpartsdata'] = $this->Dashboard->getAlertServicePartsData();

        $this->load->model('Party_model','Party');
        $this->viewData['drivercount'] = count($this->Party->getActiveParty('driver'));
        $this->viewData['garagecount'] = count($this->Party->getActiveParty('garage'));
        $this->viewData['ownercount'] = count($this->Party->getActiveParty('owner'));
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Vehicle Dashboard','View vehicle dashboard.');
        }
        $this->admin_headerlib->add_javascript("Dashboard", "pages/vehicle_dashboard.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing(){

		$PostData = $this->input->post();
        // print_r($PostData);exit;
        $days = $PostData['days'];

        $vehicleregistrationdata = $this->Dashboard->getExpiredVehicleRegistrationData($days);
        $insurancedata = $this->Dashboard->getExpiredInsuranceData($days);
        $documentdata = $this->Dashboard->getExpiredDocumentData($days);
        $partsdata = $this->Dashboard->getExpiredPartsData($days);
        $EMIdata = $this->Dashboard->getVehicleEMIData($days);

        $output = array(
            "vehicleregistrationdata" => $vehicleregistrationdata,
            "insurancedata" => $insurancedata,
            "documentdata" => $documentdata,
            "partsdata" => $partsdata,
            "emidata" => $EMIdata,
        );
        echo json_encode($output);
    }
    function printexpiredvehicleregistration(){
        
        $PostData = $this->input->post();
        $days = $PostData['days'];
        
        $PostData['reportdata'] = $this->Dashboard->getExpiredVehicleRegistrationData($days,1);
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Vehicle Registration';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireVehicleRegistrationFormate.php",$PostData,true);
        echo json_encode($html); 
    }

    function printexpiredinsurance(){
        
        $PostData = $this->input->post();
        $days = $PostData['days'];
        
        $PostData['reportdata'] = $this->Dashboard->getExpiredInsuranceData($days,1);
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Insurances';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireInsuranceFormate.php",$PostData,true);
        echo json_encode($html); 
    }

    function printexpireddocument(){
        $PostData = $this->input->post();
        $days = $PostData['days'];
        
        $PostData['reportdata'] = $this->Dashboard->getExpiredDocumentData($days,1);
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Insurances';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireVehicleRegistrationFormate.php",$PostData,true);
        echo json_encode($html); 
    }

    function printservicepartdata(){
        $PostData = $this->input->post();
        $days = $PostData['days'];
        
        $PostData['reportdata'] = $this->Dashboard->getExpiredPartsData($days,1);
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Service Parts';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireServicePartFormate.php",$PostData,true);
        echo json_encode($html); 
    }
    function printservicepartalertdata(){
        
        $PostData['reportdata'] = $this->Dashboard->getAlertServicePartsData(1);
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Alert';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintAlertReportFormate.php",$PostData,true);
        echo json_encode($html); 
    }
    function printemireminderdata(){
        $PostData = $this->input->post();
        $days = $PostData['days'];
        
        $PostData['reportdata'] = $this->Dashboard->getVehicleEMIData($days,1);
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Vehicle EMI Report';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintVehicleEMIReport.php",$PostData,true);
        echo json_encode($html); 
    }

    
}
