<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emi_report extends Admin_Controller {

    public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu','Emi_report');
        $this->load->model('Emi_report_model',"Emi_report");
    }  
    public function index(){
        
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "EMI Report";
        $this->viewData['module'] = "report/EMI_report";
        
        $this->viewData['vehicledata'] = $this->Emi_report->getVehicleDataByEMI();

        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'EMI Report','View alert report.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("emi_report", "pages/emi_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $list = $this->Emi_report->get_datatables();
        
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
            $row[] = number_format($datarow->loanamount,2,'.',',');
            $row[] = $datarow->noofinstallment;
            $row[] = number_format($datarow->installmentamount,2,'.',',');
            $row[] = $this->general_model->displaydate($datarow->installmentdate);
            $row[] = ($datarow->paymentstatus==1)?'<span class="label label-success">Paid</span>':'<span class="label label-warning">Pending</span>';

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Emi_report->count_all(),
                        "recordsFiltered" => $this->Emi_report->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exporttoexcelEMIReport(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'EMI Report','Export to excel EMI report.');
        }
        $exportdata = $this->Emi_report->exportEMIReport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            number_format($row->loanamount,2,'.',','),
                            $row->noofinstallment,
                            number_format($row->installmentamount,2,'.',','),
                            $this->general_model->displaydate($row->installmentdate),
                            ($row->paymentstatus==1)?'Paid':'Pending',
                        );
        }
        $result = array_merge($data);   
        $headings = array('Sr. No.','Vehicle Name','Loan Amount','No. of Installment','Installment Amount ('.CURRENCY_CODE.')','Installment Date','Payment Status'); 
        $this->general_model->exporttoexcel($result,"A1:AA1","EMI Report",$headings,"Vehicle-Installment-reports.xls",'c');
    }

    function exportToPDFEMIReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'EMI Report','Export to PDF EMI report.');
        }

        $PostData['reportdata'] = $this->Emi_report->exportEMIReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'EMI Report';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/PDFEMIReport', $PostData,true);

        $this->general_model->exportToPDF("Vehicle-Installment-reports.pdf",$header,$html);
    }

    public function printEMIReport(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'EMI Report','Expire Print vehicle EMI report.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Emi_report->exportEMIReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'EMI Report';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintEMIReport.php",$PostData,true);
        echo json_encode($html); 
    }
}
