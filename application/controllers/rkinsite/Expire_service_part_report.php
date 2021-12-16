<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expire_service_part_report extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu','Expire_service_part_report');
        $this->load->model('Expire_service_part_model',"Expire_service_part");
		
    }  
    public function index(){

        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "Upcoming Expire Service part Report";
        $this->viewData['module'] = "report/Expire_service_part_report";

        $this->load->model('Document_type_model','Document_type');
        $this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();
        
        $this->viewData['vehicledata'] = $this->Expire_service_part->getVehicleDataByExpiredData();

        $this->viewData['partydata'] = $this->Expire_service_part->getGaregeDataByExpiredData();

        $this->load->model('Service_type_model','Service_type');
        $this->viewData['servicetype'] = $this->Service_type->getactiveservicetype();

        // if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
        //     $this->general_model->addActionLog(4,'GSTR2 Report','View GSTR2 Report.');
        // }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("expire_service_part_report", "pages/expire_service_part_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
         
    }

    public function listing() { 
        
        $list = $this->Expire_service_part->get_datatables();
        
        $data = array();
       
        foreach ($list as $datarow) {         
            $row = array();
          
            $partyname=$vehiclename = "-";
            if($datarow->partyname!=""){
                $partyname = '<a href="'.ADMIN_URL .'party/view-party/'. $datarow->garageid .'#personaldetails" target="_blank">'.$datarow->partyname;
            }
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL .'vehicle/view-vehicle/'. $datarow->vehicleid .'#servicetab" target="_blank">'.$datarow->vehiclename;
            }
            $row[] = $vehiclename;
            $row[] = $datarow->partname;
            $row[] = $partyname;
            $row[] = $datarow->serialnumber;
            $row[] = ($datarow->warrantyenddate!="0000-00-00")?$this->general_model->displaydate($datarow->warrantyenddate):"-";  
            $row[] = ($datarow->duedate!="0000-00-00")?$this->general_model->displaydate($datarow->duedate):"-";  
            $row[] = numberFormat($datarow->amount,2,',');
            //$row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $datarow->days;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Expire_service_part->count_all(),
                        "recordsFiltered" => $this->Expire_service_part->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exporttoexcelServicePartReport(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Service Part Report','Export to excel expire service part Report.');
        }
        $exportdata = $this->Expire_service_part->exportServicePartReport();
        
        $data = array();
        $srno = 0;
        // $totalvalue=0;
        foreach ($exportdata as $row) {         
            
            // $totalvalue+=$row->amount;
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            $row->partname,
                            $row->partyname,
                            ($row->warrantyenddate!='0000-00-00'?$this->general_model->displaydate($row->warrantyenddate):'-'),
                            ($row->duedate!='0000-00-00'?$this->general_model->displaydate($row->duedate):'-'),
                            numberFormat($row->amount!=''?$row->amount:'-',2,','),
                            //($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                            $row->days
                        );
        }
        // $result = array_merge($data);
  
        $headings = array('Sr. No.','Vehicle Name','Part Name','Garage Name','Warranty End Date','Due Date','Amount','Days'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Expire Service Part Report",$headings,"Expire-Service-Part-Report.xls",'G');
    }

    function exportToPDFServicePartReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Service Part Report','Export to PDF expire service part.');
        }

        $PostData['reportdata'] = $this->Expire_service_part->exportServicePartReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Service Parts';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Expireservicepartreportforpdf', $PostData,true);
        
        $this->general_model->exportToPDF("Expire-Service-Part-Report.pdf",$header,$html);

    }

    function printExpireServicePartReport(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Service Part Report','Expire Print Expire Service Part Report.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Expire_service_part->exportServicePartReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Service Parts';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireServicePartFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
