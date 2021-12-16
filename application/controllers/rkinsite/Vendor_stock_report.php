<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_stock_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Vendor_stock_report');
        $this->load->model('Vendor_stock_report_model', 'Vendor_stock_report');
    }

    public function index() {
        $this->viewData['title'] = "Vendor Stock Report";
        $this->viewData['module'] = "report/Vendor_stock_report";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model('Product_process_model', 'Product_process');
        $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOnRawMaterialStock();
        $this->viewData['vendordata'] = $this->Vendor_stock_report->getVendorOnProcess();
        $this->viewData['productdata'] = $this->Vendor_stock_report->getOutProductOnProcess();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Vendor Stock Report','View vendor stock report.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("vendor_stock_report", "pages/vendor_stock_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $list = $this->Vendor_stock_report->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        $totalqty = $totalamount = 0;
        $totalrows = $this->Vendor_stock_report->get_datatables(1);

        foreach($totalrows as $total){
            $totalqty += number_format($total->totalqty,2,'.','');
            $totalamount += number_format($total->totalamount,2,'.','');
        }

        foreach ($list as $datarow) {         
            $row = array();
            
            if($datarow->buyerchannelid!=0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                $channellabel="";
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $buyername = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->buyerid.'" title="'.$datarow->buyername.'" target="_blank">'.$channellabel." ".$datarow->buyername.'</a>';
            }else{
                $buyername = "-";
            }
            if($datarow->vendorchannelid!=0){
                $key = array_search($datarow->vendorchannelid, array_column($channeldata, 'id'));
                $channellabel="";
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $vendorname = '<a href="'.ADMIN_URL.'vendor/vendor-detail/'.$datarow->vendorid.'" title="'.$datarow->vendorname.'" target="_blank">'.$channellabel." ".$datarow->vendorname.'</a>';
            }else{
                $vendorname = "-";
            }

            $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" title="View Product" target="_blank">'.$datarow->productname.'</a>';
            
            $row[] = ++$counter;
            $row[] = $datarow->jobcard;
            $row[] = $datarow->jobname;
            $row[] = $datarow->batchno;
            $row[] = $vendorname;
            $row[] = $productname;
            $row[] = numberFormat(($datarow->averageprice!=''?$datarow->averageprice:0),2,',');
            $row[] = numberFormat(($datarow->totalqty!=''?$datarow->totalqty:0),2,',');
            $row[] = numberFormat(($datarow->totalamount!=''?$datarow->totalamount:0),2,',');
            $row[] = ($datarow->transactiondate!="")?$this->general_model->displaydate($datarow->transactiondate):"";  
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor_stock_report->count_all(),
                        "recordsFiltered" => $this->Vendor_stock_report->count_filtered(),
                        "data" => $data,
                        "totalqty" => $totalqty,
                        "totalamount" => $totalamount
                    );
        echo json_encode($output);
    }
 
    public function getProductByCategoryId(){
        $PostData = $this->input->post();
        
        $productdata = $this->Process_group->getProductByCategoryIdOnProcessGroup($PostData['categoryid']);
        echo json_encode($productdata);
    }

    public function exporttoexcelvendorstockreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vendor Stock Report','Export to excel vendor stock report.');
        }

        $stockdata = $this->Vendor_stock_report->exportreportdata();
        $data = array();
        $srno=1;

        $totalqty = $totalamount = 0;
        foreach ($stockdata as $datarow) {         
            
            $totalqty += number_format($datarow->totalqty,2,'.',''); 
            $totalamount += number_format($datarow->totalamount,2,'.',''); 
                
            $data[] = array($srno++,
                $datarow->productname,
                numberFormat($datarow->averageprice,2,','),
                $datarow->totalqty,
                numberFormat($datarow->totalamount,2,',')
            );
            
        }
        $data[] = array("","","Total",numberFormat($totalqty,2,','),
                numberFormat($totalamount,2,',')
            );
        
        $headings = array('Sr. No.','Product Name','Price ('.CURRENCY_CODE.')','Qty','Total Amount ('.CURRENCY_CODE.')'); 

        $this->general_model->exporttoexcel($data,"A1:E1","Vendor Stock Report",$headings,"Vendorstockreport.xls","C:E",1);
        
    }
    public function exporttopdfvendorstockreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Vendor Stock Report', 'Export to PDF vendor stock report.');
        }

        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Vendor_stock_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Vendorstockreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Vendor-stock-report.pdf";
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
    public function printvendorstockreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Vendor Stock Report', 'Print vendor stock report.');
        }
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Vendor_stock_report->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Vendorstockreportformat", $PostData, true);

        echo json_encode($html);
    }
    
}?>