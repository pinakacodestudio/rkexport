<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expire_vehicle_registration_report extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu','Expire_vehicle_registration_report');
        $this->load->model('Expire_vehicle_registration_report_model',"Expire_vehicle_registration");
		
    }  
    public function index(){

        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "Upcoming Expire Vehicle Registration Report";
        $this->viewData['module'] = "report/Expire_vehicle_registration_report";

        $this->viewData['companydata'] = $this->Expire_vehicle_registration->getVehicleCompanyByExpiredData();

		$this->viewData['ownerdata'] = $this->Expire_vehicle_registration->getPartyByExpiredData('owner');

        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Expire Vehicle Registration Report','View Expire vehicle registration Report.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("expire_vehicle_registration_report", "pages/expire_vehicle_registration_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $list = $this->Expire_vehicle_registration->get_datatables();
        
        $data = array();       
       
        foreach ($list as $datarow) {         
            $row = array();
            $vehiclename = $ownername='-';
            if($datarow->ownername!=""){
                $ownername = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->ownerid.'#personaldetails" target="_blank">'.$datarow->ownername.'</a>';
            }
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->id.'#vehicledetails" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = $vehiclename;
            $row[] = $datarow->vehicleno;
            $row[] = $datarow->vehicletypename;
            $row[] = $ownername;
            $row[] = $datarow->ownercontactno;
            $row[] = ($datarow->duedateofregistration!="0000-00-00")?$this->general_model->displaydate($datarow->duedateofregistration):"-";  
            //$row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $datarow->days;

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Expire_vehicle_registration->count_all(),
                        "recordsFiltered" => $this->Expire_vehicle_registration->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exportVehicleRegistrationReport(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Vehicle Registration Report','Export to excel expire vehicle registration Report.');
        }
        $exportdata = $this->Expire_vehicle_registration->exportVehicleRegistrationReport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         

            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            $row->vehicleno,
                            $row->vehicletypename,
                            $row->ownername,
                            $row->ownercontactno,
                            ($row->duedateofregistration!='0000-00-00'?$this->general_model->displaydate($row->duedateofregistration):'-'),
                            //($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                            $row->days
                        );
        }
        $result = array_merge($data);   
        $headings = array('Sr. No.','Vehicle Name','Vehicle No.','Vehicle Type','Party Name','Contact No.','Due Date of Registration','Days'); 
        $this->general_model->exporttoexcel($result,"A1:AA1","Expire Vehicle Registration",$headings,"Expire-Vehicle-Registration-Report.xls","I");
    }

    public function exportToPDFVehicleRegistrationReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Vehicle Registration Report','Export to PDF expire vehicle registration.');
        }

        $PostData['reportdata'] = $this->Expire_vehicle_registration->exportVehicleRegistrationReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Vehicle Registrations';


        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Expirevehicleregistrationreportforpdf', $PostData,true);
        
        $this->general_model->exportToPDF("Expire-Vehicle-Registration-Report.pdf",$header,$html);
        
    }

    public function printExpireVehicleRegistrationReport(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Vehicle Registration','Expire PrintvVehicle registration.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Expire_vehicle_registration->exportVehicleRegistrationReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Vehicle Registrations';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireVehicleRegistrationFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
