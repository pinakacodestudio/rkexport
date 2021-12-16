<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_transaction_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Product_transaction_report_model', 'Product_transaction_report');
        $this->viewData = $this->getAdminSettings('submenu', 'Product_transaction_report');
    }
    public function index() {
        $this->viewData['title'] = "Product Transaction Report";
        $this->viewData['module'] = "report/Product_transaction_report";
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getAllProductList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Transaction Report','View product transaction report.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("product_transaction_report", "pages/product_transaction_report.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $this->load->model("Product_combination_model","Product_combination");
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->Product_transaction_report->get_datatables();
        //echo $this->db->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        
        foreach ($list as $datarow) {         
            $row = array();
            $varianthtml = '';
            $producttype = '';
            
            $channellabel = $trlink = '';

            $trlink = '';
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
            if($datarow->type == "Stock General Voucher (Increment)" || $datarow->type == "Stock General Voucher (Decrement)"){
                $transactionno = $datarow->transactionno; 
            }else{
                $transactionno = '<a href="'.$trlink.'" target="_blank" title="'.$datarow->transactionid.'">'.$datarow->transactionno."</a>"; 
            }

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

            /* if($datarow->isuniversal==0 && $datarow->variantid!=''){
               
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->id,$datarow->variantid);

                if(!empty($variantdata)){
                    $varianthtml .= "<div class='row' style=''>";
                    foreach($variantdata as $variant){
                        $varianthtml .= "<div class='col-md-12 p-n'>";
                        $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                        $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                        $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                        $varianthtml .= "</div>";
                    }
                    $varianthtml .= "</div>";
                }
                $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.$datarow->productname.'</a>';
            }else{
                $productname = $datarow->productname;
            } */
            $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" title="View Product" target="_blank">'.$datarow->productname.'</a>';

            if($datarow->producttype==0){
                $producttype = "Regular";
            }else if($datarow->producttype==1){
                $producttype = "Offer";
            }else if($datarow->producttype==2){
                $producttype = "Raw Material";
            }else if($datarow->producttype==3){
                $producttype = "Semi-Finish";
            }

            $row[] = $datarow->serial_number;
            $row[] = $this->general_model->displaydate($datarow->transactiondate);
            $row[] = $transactionno;
            $row[] = $membername;
            $row[] = $productname;
            $row[] = $datarow->quantity;
            $row[] = numberFormat($datarow->price,2,',');
            $row[] = $datarow->type;
            $row[] = $producttype;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_transaction_report->count_all(),
                        "recordsFiltered" => $this->Product_transaction_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function exporttoexcelproducttransactionreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Product Transaction Report','Export to excel product transaction report.');
        }

        $stockdata = $this->Product_transaction_report->exportreportdata();
        $data = array();
        foreach ($stockdata as $datarow) {         
            
            if($datarow->memberid != 0){
                $membername = $datarow->membername.' ('.$datarow->membercode.')';
            }else{
                $membername = '-';
            }
            $producttype = '';
            if($datarow->producttype==0){
                $producttype = "Regular";
            }else if($datarow->producttype==1){
                $producttype = "Offer";
            }else if($datarow->producttype==2){
                $producttype = "Raw";
            }else if($datarow->producttype==3){
                $producttype = "Semi-Finish";
            }

            $data[] = array(
                $this->general_model->displaydate($datarow->transactiondate),
                $datarow->transactionno,
                $membername,
                $datarow->productname,
                $datarow->quantity,
                numberFormat($datarow->price,2,','),
                $datarow->type,
                $producttype
            );
            
        }
        
        $headings = array('Transaction Date','Transaction No.',Member_label.' / Vendor','Product Name','Qty','Per Qty Rate ('.CURRENCY_CODE.')','Transaction Type','Product Type'); 

        $this->general_model->exporttoexcel($data,"A1:H1","Product Transaction Report",$headings,"ProductTransactionReport.xls","E:F");
        
    }
    public function exporttopdfproducttransactionreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product Transaction Report', 'Export to PDF product transaction report.');
        }

        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Product_transaction_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Producttransactionreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Product-Transaction-Report.pdf";
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
    public function printproducttransactionreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product Transaction Report', 'Print product transaction report.');
        }
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Product_transaction_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Producttransactionreportformat", $PostData, true);

        echo json_encode($html);
    }
}