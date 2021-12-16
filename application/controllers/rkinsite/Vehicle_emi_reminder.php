<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle_emi_reminder extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu','Vehicle_emi_reminder');
        $this->load->model('Vehicle_emi_reminder_model',"Vehicle_emi_reminder");
    }  
    public function index(){
        
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "Vehicle EMI Report";
        $this->viewData['module'] = "report/Vehicle_EMI_report";
        
        $this->viewData['vehicledata'] = $this->Vehicle_emi_reminder->getVehicleDataByEMI();

        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Vehicle EMI Report','View alert report.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("vehicle_emi_reminder", "pages/vehicle_emi_reminder.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $list = $this->Vehicle_emi_reminder->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
          
            $vehiclename = "-";
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#emiremindertab" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $vehiclename;
            $row[] = $datarow->installmentamount;
            $row[] = $this->general_model->displaydate($datarow->installmentdate);
            $row[] = $datarow->days;

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_emi_reminder->count_all(),
                        "recordsFiltered" => $this->Vehicle_emi_reminder->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exporttoexcelAlertReport(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vehicle EMI Report','Export to excel vehicle EMI report.');
        }
        $exportdata = $this->Vehicle_emi_reminder->exportEMIReport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            $row->installmentamount,
                            $this->general_model->displaydate($row->installmentdate),
                            $row->days,
                        );
        }
        $result = array_merge($data);   
        $headings = array('Sr. No.','Vehicle Name','Installment Amount ('.CURRENCY_CODE.')','Installment Date','Days'); 
        $this->general_model->exporttoexcel($result,"A1:AA1","Vehicle EMI Report",$headings,"Vehicle-Installment-reports.xls",'c');
    }

    function exportToPDFAlertReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vehicle EMI Report','Export to PDF vehicle EMI report.');
        }

        $PostData['reportdata'] = $this->Vehicle_emi_reminder->exportEMIReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Vehicle EMI Report';

 
        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/PDFVehicleEMIReport', $PostData,true);

        $this->general_model->exportToPDF("Vehicle-Installment-reports.pdf",$header,$html);
    }

    public function printAlertReport(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vehicle EMI Report','Expire Print vehicle EMI report.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Vehicle_emi_reminder->exportEMIReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Vehicle EMI Report';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintVehicleEMIReport.php",$PostData,true);
        echo json_encode($html); 
    }
}
