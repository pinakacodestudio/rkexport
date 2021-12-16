<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alert_report extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu','Alert_report');
        $this->load->model('Alert_report_model',"Alert_report");
    }  
    public function index(){
        
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "Alert Report";
        $this->viewData['module'] = "report/Alert_report";
        
        $this->viewData['vehicledata'] = $this->Alert_report->getVehicleDataByAlertReportData();
 
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Alert Report','View alert report.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("alert_report", "pages/alert_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $list = $this->Alert_report->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
          
            $vehiclename = "-";
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#servicetab" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $vehiclename;
            $row[] = $datarow->partname;
            $row[] = $datarow->serialnumber;
            $row[] = numberFormat($datarow->currentkmhr,2,',');
            $row[] = numberFormat($datarow->alertkmhr,2,',');  

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Alert_report->count_all(),
                        "recordsFiltered" => $this->Alert_report->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exporttoexcelAlertReport(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Alert Report','Export to excel alert report.');
        }
        $exportdata = $this->Alert_report->exportAlertReport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            $row->partname,
                            $row->serialnumber,
                            numberFormat($row->currentkmhr,2,','),
                            numberFormat($row->alertkmhr,2,',')
                        );
        }
        $result = array_merge($data);   
        $headings = array('Sr. No.','Vehicle Name','Part Name','Serial No.','Current Km/hr','Alert Km/hr'); 
        $this->general_model->exporttoexcel($result,"A1:N1","Alert Report",$headings,"Alert-Report.xls",array('E','F'));
    }

    function exportToPDFAlertReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Alert Report','Export to PDF alert report.');
        }

        $PostData['reportdata'] = $this->Alert_report->exportAlertReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Alert';

 
        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/PDFAlertReportFormate', $PostData,true);

        $this->general_model->exportToPDF("Alert-Report.pdf",$header,$html);
    }

    public function printAlertReport(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Alert Report','Expire Print alert report.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Alert_report->exportAlertReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Alert';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintAlertReportFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
