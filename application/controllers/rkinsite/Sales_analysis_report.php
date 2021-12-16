<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_analysis_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_analysis_report');
        $this->load->model('Sales_analysis_report_model', 'Sales_analysis_report');
    }
    public function index() {
        $this->viewData['title'] = "Sales Analysis Report";
        $this->viewData['module'] = "report/Sales_analysis_report";
        
        $this->load->model('User_model', 'User');
        $where=array();
        
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && in_array($this->session->userdata[base_url().'ADMINUSERTYPE'], explode(',',$this->viewData['submenuvisibility']['submenuviewalldata'])) === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Sales Analysis Report','View sales analysis report.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales_analysis_report", "pages/sales_analysis_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function getsalesanalysisreportdata(){
        
        $PostData = $this->input->post();
        $employee = (!empty($PostData['employee']))?implode(',',$PostData['employee']):'';
        $product = (!empty($PostData['product']))?implode(',',$PostData['product']):'';
        $fromdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $todate = $this->general_model->convertdate($_REQUEST['enddate']);

        $sessiondata = array();
        $arrSessionDetails = $this->session->userdata;
        if(isset($PostData['startdate'])){
            if(isset($arrSessionDetails["salesanalysisfromdatefilter"])){
              if($arrSessionDetails["salesanalysisfromdatefilter"] != $PostData['startdate']){
                $sessiondata["salesanalysisfromdatefilter"] = $PostData['startdate'];
              }
            }else{
              $sessiondata["salesanalysisfromdatefilter"] = $PostData['startdate'];
            }
        }
        if(isset($PostData['enddate'])){
            if(isset($arrSessionDetails["salesanalysistodatefilter"])){
              if($arrSessionDetails["salesanalysistodatefilter"] != $PostData['enddate']){
                $sessiondata["salesanalysistodatefilter"] = $PostData['enddate'];
              }
            }else{
              $sessiondata["salesanalysistodatefilter"] = $PostData['enddate'];
            }
        }
        if(!empty($employee)){
            if(isset($arrSessionDetails["salesanalysisemployeefilter"])){
              if($arrSessionDetails["salesanalysisemployeefilter"] != $employee){
                $sessiondata["salesanalysisemployeefilter"] = $employee;
              }
            }else{
              $sessiondata["salesanalysisemployeefilter"] = $employee;
            }
        }else{
            $sessiondata["salesanalysisemployeefilter"] = "";
        }
        if(!empty($product)){
            if(isset($arrSessionDetails["salesanalysisproductfilter"])){
              if($arrSessionDetails["salesanalysisproductfilter"] != $product){
                $sessiondata["salesanalysisproductfilter"] = $product;
              }
            }else{
              $sessiondata["salesanalysisproductfilter"] = $product;
            }
        }else{
            $sessiondata["salesanalysisproductfilter"] = "";
        }
        if (!empty($sessiondata)) {
            $this->session->set_userdata($sessiondata);
        }

        $req = array();
        $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Date',"sortable"=>true);
        if(!empty($employee)){
            $req['COLUMNS'][] = array('title'=>'Employee',"sortable"=>true);
        }
        if(!empty($product)){
            $req['COLUMNS'][] = array('title'=>'Product',"sortable"=>true);
        }
        $req['COLUMNS'][] = array('title'=>'Total Sales ('.CURRENCY_CODE.')',"sortable"=>true,"class"=>"text-right");
        
		$salesdata = $this->Sales_analysis_report->getSalesAnalysisReportdata($fromdate,$todate,$employee,$product);
        
        $data = array();
        $counter = 0;       
        foreach ($salesdata as $datarow) {
            
            $row = array();
            $row[] = ++$counter;            
            $row[] = $this->general_model->displaydate($datarow->date);
            if(!empty($employee)){
            $row[] = ucwords($datarow->employee);          
            } 
            if(!empty($product)){
            $row[] = $datarow->product;
            }
            $row[] = numberFormat($datarow->totalsales,2,','); 
            $data[] = $row;

        }
        $req['DATA'] = $data;
        
        
		echo json_encode($req);
    }

    public function exporttoexcelsalesanalysisreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Sales Analysis Report','Export to excel sales analysis report.');
        }
        $PostData = $this->input->get();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $product = (!empty($PostData['product']))?$PostData['product']:'';
        $fromdate = $this->general_model->convertdate($PostData['fromdate']);
        $todate = $this->general_model->convertdate($PostData['todate']);
        $exportdata = $this->Sales_analysis_report->getSalesAnalysisReportdata($fromdate,$todate,$employee,$product);
        // print_r($exportdata); exit;
        
        $headings = $data = array();
        $counter = 0;       
        foreach ($exportdata as $datarow) {
            
            $row = array();
            $row[] = ++$counter;            
            $row[] = $this->general_model->displaydate($datarow->date);
            if(!empty($employee)){
            $row[] = ucwords($datarow->employee);          
            } 
            if(!empty($product)){
            $row[] = $datarow->product;
            }
            $row[] = numberFormat($datarow->totalsales,2,','); 
            $data[] = $row;

        }
        $headings[] = 'Sr. No.';
        $headings[] = 'Date';
        if(!empty($employee)){
            $headings[] = 'Employee';
        } 
        if(!empty($product)){
            $headings[] = 'Product';
        }
        $headings[] = 'Total Sales ('.CURRENCY_CODE.')';
        if(count($headings)==3){
            $align = 'C';
        }else if(count($headings)==4){
            $align = 'D';
        }else{
            $align = 'E';
        }
        $this->general_model->exporttoexcel($data,"A1:N1","Sales Analysis Report",$headings,"Sales-Analysis-Report.xls",$align);
    }
   
    function exporttopdfsalesanalysisreport() {
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Sales Analysis Report','Export to PDF sales analysis report.');
        }

        $PostData = $this->input->get();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $product = (!empty($PostData['product']))?$PostData['product']:'';
        $fromdate = $this->general_model->convertdate($PostData['fromdate']);
        $todate = $this->general_model->convertdate($PostData['todate']);
        $PostData['reportdata'] = $this->Sales_analysis_report->getSalesAnalysisReportdata($fromdate,$todate,$employee,$product);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        
        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Salesanalysisformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Sales-Analysis-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   40, // margin top
                   15, // margin bottom
                    3, // margin header
                    10); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
       
        ob_start();
        ob_end_clean();
        
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "D");
    }

    public function printsalesanalysisreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Sales Analysis Report', 'Print sales analysis report.');
        }
        $PostData = $this->input->post();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $product = (!empty($PostData['product']))?$PostData['product']:'';
        $fromdate = $this->general_model->convertdate($PostData['fromdate']);
        $todate = $this->general_model->convertdate($PostData['todate']);
        
        $PostData['reportdata'] = $this->Sales_analysis_report->getSalesAnalysisReportdata($fromdate,$todate,$employee,$product);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Salesanalysisformatforpdf", $PostData, true);

        echo json_encode($html);
    }
}