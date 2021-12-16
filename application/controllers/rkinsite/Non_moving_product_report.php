<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Non_moving_product_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Non_moving_product_report_model', 'Non_moving_product_report');
        $this->viewData = $this->getAdminSettings('submenu', 'Non_moving_product_report');
    }
    public function index() {
        $this->viewData['title'] = "Non-Moving Product Report";
        $this->viewData['module'] = "report/Non_moving_product_report";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Non Moving Product Report','View non-moving product report.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("non_moving_product_report", "pages/non_moving_product_report.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->Non_moving_product_report->get_datatables();
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
                        "recordsTotal" => $this->Non_moving_product_report->count_all(),
                        "recordsFiltered" => $this->Non_moving_product_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function exporttoexcelnonmovingproductreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Non Moving Product Report','Export to excel non-moving product report.');
        }

        $reportdata = $this->Non_moving_product_report->exportreportdata();
        $data = array();
        foreach ($reportdata as $i=>$datarow) {         
            
           
            $data[] = array(++$i,
                $this->general_model->displaydate($datarow->orderdate),
                $datarow->productname,
                $datarow->producttypename,
                $datarow->qty
            );
            
        }
        
        $headings = array('Sr. No.','Order Date','Product Name','Product Type','Quantity'); 

        $this->general_model->exporttoexcel($data,"A1:E1","Non Moving Product Report",$headings,"NonMovingProductReport.xls","E");
        
    }
    public function exporttopdfnonmovingproductreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Non Moving Product Report', 'Export to PDF non-moving product report.');
        }

        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Non_moving_product_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Nonmovingproductreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Non-Moving-Product-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage(
            '', // L - landscape, P - portrait 
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
            $this->general_model->addActionLog(0, 'Non Moving Product Report', 'Print non-moving product report.');
        }
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Non_moving_product_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Nonmovingproductreportformat", $PostData, true);

        echo json_encode($html);
    }
}