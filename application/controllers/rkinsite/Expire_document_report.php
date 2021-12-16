<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expire_document_report extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu','Expire_document_report');
        $this->load->model('Expire_document_report_model',"Expire_document_report");
    }  
    public function index(){

        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "Upcoming Expire Document Report";
        $this->viewData['module'] = "report/Expire_document_report";

        $this->viewData['vehicledata'] = $this->Expire_document_report->getVehicleDataByExpiredData();
        
        $this->viewData['documenttypedata'] = $this->Expire_document_report->getDocumentTypeByExpiredData();
        $this->viewData['partydata'] = $this->Expire_document_report->getPartyByExpiredData();
 
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Expire Document Report','View Expire Document Report.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("expire_document_report", "pages/expire_document_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $list = $this->Expire_document_report->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
          
            $partyname = $vehiclename = "-";
            if($datarow->partyname!=""){
                $partyname = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->partyid.'#documentdetails" target="_blank">'.$datarow->partyname.'</a>';
            }
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#documenttab" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $vehiclename;
            $row[] = $partyname;
            $row[] = $datarow->documenttype;
            $row[] = $datarow->documentnumber;
            $row[] = ($datarow->fromdate!="0000-00-00")?$this->general_model->displaydate($datarow->fromdate):"-";  
            $row[] = ($datarow->duedate!="0000-00-00")?$this->general_model->displaydate($datarow->duedate):"-";  
            //$row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $datarow->days;  

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Expire_document_report->count_all(),
                        "recordsFiltered" => $this->Expire_document_report->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exporttoexceldocumentreport(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Document Report','Export to excel Expire Document Report.');
        }
        $exportdata = $this->Expire_document_report->exportdocumentreport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            $row->partyname,
                            $row->documenttype,
                            $row->documentnumber,
                            ($row->fromdate!='0000-00-00'?$this->general_model->displaydate($row->fromdate):'-'),
                            ($row->duedate!='0000-00-00'?$this->general_model->displaydate($row->duedate):'-'),
                            //($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                            $row->days
                        );
        }
        $result = array_merge($data);   
        $headings = array('Sr. No.','Vehicle Name','Party Name','Document Type','Document No.','Register Date','Due Date','Days'); 
        $this->general_model->exporttoexcel($result,"A1:AA1","Expire Document Report",$headings,"Expire-Document-Report.xls",'I');
    }

    function exportToPDFDocumentReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Document Report','Export to PDF expire document.');
        }

        $PostData['reportdata'] = $this->Expire_document_report->exportdocumentreport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Documents';

 
        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Expiredocumentreportforpdf', $PostData,true);

        $this->general_model->exportToPDF("Expire-Document-Report.pdf",$header,$html);
    }

    public function printExpireDocumentDetails(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenu']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Document Details','Expire Print Document Details.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Expire_document_report->exportdocumentreport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Documents';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireDocumentDetailFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
