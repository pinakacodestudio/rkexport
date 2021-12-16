<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_ledger_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Product_ledger_report_model', 'Product_ledger_report');
        $this->viewData = $this->getAdminSettings('submenu', 'Product_ledger_report');
    }
    public function index() {
        $this->viewData['title'] = "Product Ledger Report";
        $this->viewData['module'] = "report/Product_ledger_report";
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getAllProductList();

        $this->viewData['memberdata'] = $this->Product_ledger_report->getMemberList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Ledger Report','View product ledger report.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("product_ledger_report", "pages/product_ledger_report.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $this->load->model("Product_combination_model","Product_combination");
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->Product_ledger_report->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        
        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = $trlink = '';

            if($datarow->type == "Sales Order"){
                $trlink = ADMIN_URL.'order/view-order/'.$datarow->transactionid;
            }else if($datarow->type == "Sales Invoice"){
                $trlink = ADMIN_URL.'invoice/view-invoice/'.$datarow->transactionid;
            }else if($datarow->type == "Sales Return"){
                $trlink = ADMIN_URL.'credit-note/view-credit-note/'.$datarow->transactionid;
            }else if($datarow->type == "Purchase Order"){
                $trlink = ADMIN_URL.'purchase-order/view-purchase-order/'.$datarow->transactionid;
            }else if($datarow->type == "Purchase Invoice"){
                $trlink = ADMIN_URL.'purchase-invoice/view-purchase-invoice/'.$datarow->transactionid;
            }else if($datarow->type == "Purchase Return"){
                $trlink = ADMIN_URL.'purchase-credit-note/view-purchase-credit-note/'.$datarow->transactionid;
            }else if($datarow->type == "Out Process"){
                $trlink = ADMIN_URL.'product-process/view-product-process/'.$datarow->transactionid;
            }
            $transactionno = '<a href="'.$trlink.'" target="_blank" title="'.$datarow->transactionid.'">'.$datarow->transactionno."</a>"; 

            if($datarow->memberid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($datarow->type == "Sales Order" || $datarow->type == "Sales Invoice" || $datarow->type == "Sales Return"){
                    $link = ADMIN_URL.'member/member-detail/'.$datarow->memberid;
                }else{
                    $link = ADMIN_URL.'vendor/vendor-detail/'.$datarow->memberid;
                }
                $membername = $channellabel.'<a href="'.$link.'" target="_blank" title="'.$datarow->membername.'">'.$datarow->membername.' ('.$datarow->membercode.')'."</a>";
            }else{
                $membername = '-';
            }

            $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" title="View Product" target="_blank">'.$datarow->productname.'</a>';

            $row[] = ++$counter;
            $row[] = $this->general_model->displaydate($datarow->transactiondate);
            $row[] = $transactionno;
            $row[] = $membername;
            $row[] = $productname;
            $row[] = $datarow->inqty;
            $row[] = $datarow->outqty;
            $row[] = $datarow->type;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_ledger_report->count_all(),
                        "recordsFiltered" => $this->Product_ledger_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function exporttoexcelproductledgerreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Product Ledger Report','Export to excel product ledger report.');
        }

        $stockdata = $this->Product_ledger_report->exportreportdata();
        $data = array();
        foreach ($stockdata as $datarow) {         
            
            if($datarow->memberid != 0){
                $membername = $datarow->membername.' ('.$datarow->membercode.')';
            }else{
                $membername = '-';
            }
            $data[] = array(
                $this->general_model->displaydate($datarow->transactiondate),
                $datarow->transactionno,
                $membername,
                $datarow->productname,
                $datarow->inqty,
                $datarow->outqty,
                $datarow->type,
            );
            
        }
        
        $headings = array('Transaction Date','Transaction No.',Member_label.' / Vendor','Product Name','In Qty','Out Qty','Transaction Type'); 

        $this->general_model->exporttoexcel($data,"A1:H1","Product Ledger Report",$headings,"ProductLedgerReport.xls","E:F");
        
    }
    public function exporttopdfproductledgerreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product Ledger Report', 'Export to PDF product ledger report.');
        }

        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Product_ledger_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Productledgerreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Product-Ledger-Report.pdf";
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
    public function printproductledgerreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product Ledger Report', 'Print product ledger report.');
        }
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Product_ledger_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Productledgerreportformat", $PostData, true);

        echo json_encode($html);
    }
}