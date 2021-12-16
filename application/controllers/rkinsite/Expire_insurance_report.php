<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expire_insurance_report extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu','Expire_insurance_report');
        $this->load->model('Expire_insurance_report_model',"Expire_insurance");
		
    }  
    public function index(){

        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "Upcoming Expire Insurance Report";
        $this->viewData['module'] = "report/Expire_insurance_report";

        $this->viewData['insurancecompanydata'] = $this->Expire_insurance->getInsurancecompanyDataByExpiredData();

        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Expire Insurance Report','View Expire Insurance Report.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("expire_insurance_report", "pages/expire_insurance_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $list = $this->Expire_insurance->get_datatables();
        
        $data = array();       
       
        foreach ($list as $datarow) {         
            $row = array();
          
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'insurancetab" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = $vehiclename;
            $row[] = $datarow->companyname;
            $row[] = $datarow->policyno;
            $row[] = ($datarow->fromdate!="0000-00-00")?$this->general_model->displaydate($datarow->fromdate):"-";  
            $row[] = ($datarow->todate!="0000-00-00")?$this->general_model->displaydate($datarow->todate):"-";  
            $row[] = number_format(($datarow->amount), 2, '.', ',');
            //$row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $datarow->days;

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Expire_insurance->count_all(),
                        "recordsFiltered" => $this->Expire_insurance->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exportToExcelExpireinsuranceReport(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Insurance Report','Export to excel expire insurance Report.');
        }
        $exportdata = $this->Expire_insurance->exportInsuranceReport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         

            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            $row->companyname,
                            $row->policyno,
                            ($row->fromdate!='0000-00-00'?$this->general_model->displaydate($row->fromdate):'-'),
                            ($row->duedate!='0000-00-00'?$this->general_model->displaydate($row->duedate):'-'),
                            numberFormat($row->amount,2,','),
                            //($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                            $row->days
                        );
        }
        $result = array_merge($data);   
        $headings = array('Sr. No.','Vehicle Name','Insurance company','Policy No.','Register Date','Due Date','Amount','Days'); 
        $this->general_model->exporttoexcel($result,"A1:AA1","Expire Insurance Report",$headings,"Expire-Insurance-Report.xls",array('G','I'));
    }

    public function exportToPDFInsuranceReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Insurance Report','Export to PDF expire insurance.');
        }

        $PostData['reportdata'] = $this->Expire_insurance->exportInsuranceReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Insurances';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Expireinsurancereportforpdf', $PostData,true);
        
        $this->general_model->exportToPDF("Expire-Insurance-Report.pdf",$header,$html);
    }

    function printExpireInsuranceReport(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Insurance Report','Print expire insurance.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Expire_insurance->exportInsuranceReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Upcoming Expired Insurances';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintExpireInsuranceFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
