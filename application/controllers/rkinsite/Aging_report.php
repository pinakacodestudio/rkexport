<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aging_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Aging_report');
        $this->load->model('Non_moving_product_report_model', 'Aging_report');
    }
    public function index() {
        $this->viewData['title'] = "Aging Report";
        $this->viewData['module'] = "report/Non_moving_product_report";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Aging Report','View aging report.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("non_moving_product_report", "pages/non_moving_product_report.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->Aging_report->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        
        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = $trlink = '';

            $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" title="View Product" target="_blank">'.$datarow->productname.'</a>';

            $row[] = $datarow->serial_number;
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = $productname;
            $row[] = $datarow->producttypename;
            $row[] = $datarow->qty;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Aging_report->count_all(),
                        "recordsFiltered" => $this->Aging_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function get_aging_report_data(){
        $PostData = $this->input->post();
        //print_r($PostData);
        $intervallength = $PostData['intervallength'];
        $intervalcount = $PostData['intervalcount'];
        $producttype = (isset($PostData['producttype']) && $PostData['producttype']!="")?implode(",",$PostData['producttype']):'';
        
        $counter=0;
        $req = array();
        
        $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true,"class"=>"width5");
        $req['COLUMNS'][] = array('title'=>'Product Name',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Product Type',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Current Stock',"sortable"=>true,"class"=>"text-right");
        
        $start_days = $end_days = 0;
        $startdatearray = $enddatearray = array();
        
        for($i=1; $i<=$intervalcount; $i++) {

            if($i==1){
                $length_title = $start_days.'-'.$intervallength;
                
                $startdatearray[] = $this->general_model->getCurrentDate();
                $enddatearray[] = date("Y-m-d",strtotime("-".($intervallength-1)." days"));
                
                // $start_days += ($intervallength + 1);
                // $end_days = $start_days + ($intervallength - 1);
                $end_days = $intervallength;

            }else if($i==$intervalcount){

                $start_days = ($end_days + 1);
                
                $length_title = $start_days.'+';
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = "";

            }else{
                $start_days = ($end_days + 1);
                $end_days = $end_days + $intervallength;

                $length_title = $start_days.'-'.$end_days; 
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = date("Y-m-d",strtotime("-".($end_days-1)." days"));
                
            }            

            $req['COLUMNS'][] = array(
                'title'=>$length_title,
                "sortable"=>true,
                "class"=>"text-right"
            );


        }

        $reportdata = $this->Aging_report->get_aging_report_data($producttype,$startdatearray,$enddatearray);
        
        foreach ($reportdata as $index=>$row) {
            $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$row['productid'].'" title="View Product" target="_blank">'.$row['productname'].'</a>';

            $stock = array();
            if(!empty($startdatearray)){
                foreach($startdatearray as $k=>$startdate){
                    $stock[] = $row['currentstock'.($k+1)];
                }
            }

            $req['DATA'][] = array_merge(array(++$counter,
                $productname,
                $row['producttypename'],
                $row['currentstock']
            ),$stock);
        }
		
		echo json_encode($req);
    }
    public function exporttoexcelnonmovingproductreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Aging Report','Export to excel aging report.');
        }

        $intervallength = $_REQUEST['intervallength'];
        $intervalcount = $_REQUEST['intervalcount'];
        $producttype = (isset($_REQUEST['producttype']) && $_REQUEST['producttype']!="")?$_REQUEST['producttype']:'';

        $start_days = $end_days = 0;
        $header = $startdatearray = $enddatearray = array();
       
        for($i=1; $i<=$intervalcount; $i++) {

            if($i==1){
                $length_title = $start_days.'-'.$intervallength;
                
                $startdatearray[] = $this->general_model->getCurrentDate();
                $enddatearray[] = date("Y-m-d",strtotime("-".($intervallength-1)." days"));
                
                $end_days = $intervallength;

            }else if($i==$intervalcount){

                $start_days = ($end_days + 1);
                
                $length_title = $start_days.'+';
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = "";

            }else{
                $start_days = ($end_days + 1);
                $end_days = $end_days + $intervallength;

                $length_title = $start_days.'-'.$end_days; 
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = date("Y-m-d",strtotime("-".($end_days-1)." days"));
                
            }            

            $header[] = $length_title;


        }
        
        $reportdata = $this->Aging_report->get_aging_report_data($producttype,$startdatearray,$enddatearray);
        $data = array();
        foreach ($reportdata as $i=>$datarow) {         
            
            $stock = array();
            if(!empty($startdatearray)){
                foreach($startdatearray as $k=>$startdate){
                    $stock[] = $datarow['currentstock'.($k+1)];
                }
            }

            $data[] = array_merge(
                            array(++$i,
                                $datarow['productname'],
                                $datarow['producttypename'],
                                $datarow['currentstock']
                            ),
                            $stock
                        );
            
            
        }
        
        $headings = array_merge(array('Sr. No.','Product Name','Product Type','Current Stock'),$header); 

        $this->general_model->exporttoexcel($data,"A1:S1","Aging Report",$headings,"AgingReport.xls","D:S");
        
    }
    public function exporttopdfnonmovingproductreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Aging Report', 'Export to PDF aging report.');
        }

        $PostData = $this->input->get();
        $intervallength = $PostData['intervallength'];
        $intervalcount = $PostData['intervalcount'];
        $producttype = (isset($PostData['producttype']) && $PostData['producttype']!="")?$PostData['producttype']:'';

        $start_days = $end_days = 0;
        $header = $startdatearray = $enddatearray = array();
       
        for($i=1; $i<=$intervalcount; $i++) {

            if($i==1){
                $length_title = $start_days.'-'.$intervallength;
                
                $startdatearray[] = $this->general_model->getCurrentDate();
                $enddatearray[] = date("Y-m-d",strtotime("-".($intervallength-1)." days"));
                
                $end_days = $intervallength;

            }else if($i==$intervalcount){

                $start_days = ($end_days + 1);
                
                $length_title = $start_days.'+';
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = "";

            }else{
                $start_days = ($end_days + 1);
                $end_days = $end_days + $intervallength;

                $length_title = $start_days.'-'.$end_days; 
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = date("Y-m-d",strtotime("-".($end_days-1)." days"));
                
            }            

            $header[] = $length_title;


        }
        $PostData['header'] = $header;
        $PostData['reportdata'] = $this->Aging_report->get_aging_report_data($producttype,$startdatearray,$enddatearray);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Nonmovingproductreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Aging-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage(
            'L', // L - landscape, P - portrait 
            '',
            '',
            '',
            '',
            10, // margin_left
            10, // margin right
            40, // margin top
            15, // margin bottom
            3, // margin header
            10
        ); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL . 'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL . 'styles.css'); // external css
        $pdf->WriteHTML($stylesheet, 1);
        $pdf->WriteHTML($stylesheet2, 1);
        $pdf->SetHTMLHeader($header, '', true);
        $pdf->WriteHTML($html, 0);

        ob_start();
        ob_end_clean();

        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "D");
    }
    public function printnonmovingproductreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Aging Report', 'Print aging report.');
        }
        $PostData = $this->input->post();

        $intervallength = $PostData['intervallength'];
        $intervalcount = $PostData['intervalcount'];
        $producttype = (isset($PostData['producttype']) && $PostData['producttype']!="")?implode(",",$PostData['producttype']):'';
       /*  $producttype = $producttype!=""?(is_array($producttype)?implode(",",$_REQUEST['producttype']):$producttype):''; */
        
        
        $start_days = $end_days = 0;
        $header = $startdatearray = $enddatearray = array();
       
        for($i=1; $i<=$intervalcount; $i++) {

            if($i==1){
                $length_title = $start_days.'-'.$intervallength;
                
                $startdatearray[] = $this->general_model->getCurrentDate();
                $enddatearray[] = date("Y-m-d",strtotime("-".($intervallength-1)." days"));
                
                $end_days = $intervallength;

            }else if($i==$intervalcount){

                $start_days = ($end_days + 1);
                
                $length_title = $start_days.'+';
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = "";

            }else{
                $start_days = ($end_days + 1);
                $end_days = $end_days + $intervallength;

                $length_title = $start_days.'-'.$end_days; 
                
                $startdatearray[] = date("Y-m-d",strtotime("-".($start_days-1)." days"));
                $enddatearray[] = date("Y-m-d",strtotime("-".($end_days-1)." days"));
                
            }            

            $header[] = $length_title;


        }
        $PostData['header'] = $header;
        $PostData['reportdata'] = $this->Aging_report->get_aging_report_data($producttype,$startdatearray,$enddatearray);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Nonmovingproductreportformat", $PostData, true);

        echo json_encode($html);
    }
}