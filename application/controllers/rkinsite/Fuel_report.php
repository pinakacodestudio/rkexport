<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fuel_report extends Admin_Controller{

    public $viewData = array();
    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('thirdlevelsubmenu', 'Fuel_report');
        $this->load->model('Fuel_report_model', 'Fuel_report');
    }

    public function index() {

        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        $this->viewData['title'] = "Fuel Report";
        $this->viewData['module'] = "report/Fuel_report";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Fuel','View fuel.');
        }
        $this->admin_headerlib->add_javascript("fuel", "pages/fuel_report.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() { 

        $list = $this->Fuel_report->get_datatables();
        
        $data = array();       
       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $vehiclename ='-';
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->id.'#fueltab" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $vehiclename;
            $row[] = (isset($this->Fueltype[$datarow->fueltype])?$this->Fueltype[$datarow->fueltype]:'-');
            $row[] = $datarow->fuelratetypename;
            $row[] = numberFormat($datarow->totalcost,2,',');
            $row[] = numberFormat($datarow->totalliter,2,',');
            $row[] = numberFormat($datarow->total,2,',');
            $row[] = numberFormat($datarow->averagecost,2,',');

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Fuel_report->count_all(),
                        "recordsFiltered" => $this->Fuel_report->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function exportFuelReportexcel(){
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Expire Document Report','Export to excel Expire Document Report.');
        }
        $exportdata = $this->Fuel_report->exportFuelReport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {       
            
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            (isset($this->Fueltype[$row->fueltype])?$this->Fueltype[$row->fueltype]:'-'),
                            $row->fuelratetypename,
                            numberFormat($row->totalcost,2,','),
                            numberFormat($row->totalliter,2,','),
                            numberFormat($row->total,2,','),
                            numberFormat($row->averagecost,2,',')
                        );
        }
        $result = array_merge($data);   
        $headings = array('Sr. No.','Vehicle Name','Fuel Type','Fuel Rate Type','Total Expences','Total Liter','Total Km / hr','Per Km / Hr Cost'); 
        $this->general_model->exporttoexcel($result,"A1:N1","Fuel Report",$headings,"Fuel-Report.xls",array('E','F','G'));
    }

    public function exportToPDFFuelReport() {
        
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Fuel Report','Export to PDF fuel report.');
        }

        $PostData['reportdata'] = $this->Fuel_report->exportFuelReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Fuel';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Fuelreportforpdf', $PostData,true);

        $this->general_model->exportToPDF("Fuel-Report.pdf",$header,$html);
    }

    public function printFuelReport(){
        $this->checkAdminAccessModule('thirdlevelsubmenu','view',$this->viewData['thirdlevelsubmenuvisibility']);
        if($this->viewData['thirdlevelsubmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Fuel Report','Expire Print fuel report.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Fuel_report->exportFuelReport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Fuel';
        
        $html['content'] = $this->load->view(ADMINFOLDER."report/PrintFuelReportFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
?>