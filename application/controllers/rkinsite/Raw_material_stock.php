<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Raw_material_stock extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Raw_material_stock');
        $this->load->model('Raw_material_stock_model', 'Raw_material_stock');
    }

    public function index() {
        $this->viewData['title'] = "Raw Material Stock";
        $this->viewData['module'] = "raw_material_stock/Raw_material_stock";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model('Product_process_model', 'Product_process');
        $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOnRawMaterialStock();
        $this->viewData['vendordata'] = $this->Raw_material_stock->getVendorOnProcess();
        $this->viewData['productdata'] = $this->Raw_material_stock->getOutProductOnProcess();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Raw Material Stock','View raw material stock.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("raw_material_stock", "pages/raw_material_stock.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $this->load->model('Stock_report_model','Stock');
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $list = $this->Raw_material_stock->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
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

            if($datarow->orderid!=0){
                $orderID = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" title="View Order" target="_blank">'.$datarow->ordernumber.'</a>';
            }else{
                $orderID = "-";
            }
            $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" title="View Product" target="_blank">'.$datarow->productname.'</a>';
            
            if(MANAGE_DECIMAL_QTY==1){
                $stock = number_format($datarow->currentstock,2,'.','');
                $inprocessstock = number_format($datarow->inprocessstock,2,'.','');
                $actualstock = number_format($datarow->actualstock,2,'.','');
            }else{
                $stock = (int)$datarow->currentstock;
                $inprocessstock = (int)$datarow->inprocessstock;
                $actualstock = (int)($datarow->actualstock);
            }

            $row[] = ++$counter;
            $row[] = $datarow->jobcard;
            $row[] = $datarow->jobname;
            $row[] = $datarow->batchno;
            $row[] = $orderID;
            $row[] = $buyername;
            $row[] = $vendorname;
            $row[] = $productname;
            $row[] = $stock;
            $row[] = $inprocessstock;
            $row[] = $actualstock;
            $row[] = $this->general_model->displaydate($datarow->transactiondate);  
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Raw_material_stock->count_all(),
                        "recordsFiltered" => $this->Raw_material_stock->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function getProductByCategoryId(){
        $PostData = $this->input->post();
        
        $productdata = $this->Process_group->getProductByCategoryIdOnProcessGroup($PostData['categoryid']);
        echo json_encode($productdata);
    }

    function exporttoexcelrawmaterialstockreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Raw Material Stock','Export to excel raw material stock report.');
        }
        $exportdata = $this->Raw_material_stock->exportreport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            if($row->buyerchannelid != 0){
                $buyername = $row->buyername.' ('.$row->buyercode.')';
            }else{
                $buyername = 'COMPANY';
            }
            if($row->vendorchannelid != 0){
                $vendorname = $row->vendorname;
            }else{
                $vendorname = '-';
            }
            if(MANAGE_DECIMAL_QTY==1){
                $stock = number_format($row->currentstock,2,'.','');
                $inprocessstock = number_format($row->inprocessstock,2,'.','');
                $actualstock = number_format($row->actualstock,2,'.','');
            }else{
                $stock = (int)$row->currentstock;
                $inprocessstock = (int)$row->inprocessstock;
                $actualstock = (int)($row->actualstock);
            }

            $data[] = array(++$srno,
                            $row->jobcard,
                            $row->jobname,
                            $row->batchno,
                            ($row->orderid!=0)?$row->ordernumber:"-",
                            $buyername,$vendorname,
                            $row->productname,
                            $stock,
                            $inprocessstock,
                            $actualstock,
                            $this->general_model->displaydate($row->transactiondate)
                        );
        }
        
        $headings = array('Sr. No.','Job Card','Job Name','Batch No.','OrderID','Buyer Name','Vendor Name','Product Name','Full Stock','In Process Stock','Actual Stock','Transaction Date'); 
        $this->general_model->exporttoexcel($data,"A1:L1","Raw Material Stock",$headings,"Raw-Material-Stock.xls");
    }

    function exportToPDFRawMaterialStockReport() {
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Raw Material Stock','Export to PDF raw material stock report.');
        }

        $PostData['reportdata'] = $this->Raw_material_stock->exportreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'raw_material_stock/Rawmaterialstockformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Raw-Material-Stock.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage('L', // L - landscape, P - portrait 
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
    
}?>