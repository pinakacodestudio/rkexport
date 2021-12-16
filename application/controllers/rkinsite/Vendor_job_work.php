<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_job_work extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Vendor_job_work');
        $this->load->model('Vendor_job_work_model', 'Vendor_job_work');
    }

    public function index() {
        $this->viewData['title'] = "Vendor Job Work";
        $this->viewData['module'] = "report/Vendor_balance";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['vendordata'] = $this->Vendor_job_work->getVendorOnProcess();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Vendor Job Work','View vendor balance.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("vendor_balance", "pages/vendor_balance.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function report($vendorid) {
        $this->viewData['title'] = "Vendor Job Work";
        $this->viewData['module'] = "report/Vendor_job_work";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model('Product_process_model', 'Product_process');
        $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOnRawMaterialStock();
        $this->viewData['vendordata'] = $this->Vendor_job_work->getVendorOnProcess();
        $this->viewData['productdata'] = $this->Vendor_job_work->getOutProductOnProcess();
        $this->viewData['memberid'] = $vendorid;

        $this->load->model("Member_model","Member");
        $vendordata = $this->Member->getMemberDataByID($vendorid);

        if($vendorid=="" || empty($vendordata)){
            redirect(ADMINFOLDER."pagenotfound");
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){ 
            $this->general_model->addActionLog(4,'Vendor Job Work','View vendor job work.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("vendor_job_work", "pages/vendor_job_work.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function vendor_listing() { 
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $list = $this->Vendor_job_work->get_datatables_vendor();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            
            $key = array_search($datarow->vendorchannelid, array_column($channeldata, 'id'));
            $channellabel="";
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            $vendorname = '<a href="'.ADMIN_URL.'vendor-job-work/report/'.$datarow->vendorid.'" title="'.$datarow->vendorname.'" target="_blank">'.$channellabel." ".$datarow->vendorname.'</a>';
            
            $row[] = ++$counter;
            $row[] = $vendorname;
            $row[] = numberFormat($datarow->openingstock,2,',');
            $row[] = numberFormat($datarow->closingstock,2,',');
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor_job_work->count_all_vendor(),
                        "recordsFiltered" => $this->Vendor_job_work->count_filtered_vendor(),
                        "data" => $data
                    );
        echo json_encode($output);
    }

    public function listing() { 
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();

        $list = $this->Vendor_job_work->get_datatables();
        $openingbalance = $this->Vendor_job_work->getOpeningBalance();
        $closingbalance = $this->Vendor_job_work->getColsingBalance();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            
            /* if($datarow->vendorchannelid!=0){
                $key = array_search($datarow->vendorchannelid, array_column($channeldata, 'id'));
                $channellabel="";
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $vendorname = '<a href="'.ADMIN_URL.'vendor/vendor-detail/'.$datarow->vendorid.'" title="'.$datarow->vendorname.'" target="_blank">'.$channellabel." ".$datarow->vendorname.'</a>';
            }else{
                $vendorname = "-";
            } */

            if($datarow->orderid!=0){
                $orderID = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" title="View Order" target="_blank">'.$datarow->ordernumber.'</a>';
            }else{
                $orderID = "-";
            }
            $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" title="View Product" target="_blank">'.$datarow->productname.'</a>';
            
            $row[] = ++$counter;
            $row[] = $datarow->jobcard;
            $row[] = $datarow->jobname;
            $row[] = $datarow->batchno;
            $row[] = $orderID;
            $row[] = $productname;
            $row[] = numberFormat(($datarow->inqty!=''?$datarow->inqty:0),2,',');
            $row[] = numberFormat(($datarow->outqty!=''?$datarow->outqty:0),2,',');
            $row[] = numberFormat(($datarow->rejectqty!=''?$datarow->rejectqty:0),2,',');
            $row[] = numberFormat(($datarow->wastageqty!=''?$datarow->wastageqty:0),2,',');
            $row[] = numberFormat(($datarow->lostqty!=''?$datarow->lostqty:0),2,',');
            $row[] = numberFormat($datarow->balanceqty,2,',');
            $row[] = ($datarow->transactiondate!="")?$this->general_model->displaydate($datarow->transactiondate):"";  
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vendor_job_work->count_all(),
                        "recordsFiltered" => $this->Vendor_job_work->count_filtered(),
                        "data" => $data,
                        "openingbalance" => $openingbalance,
                        "closingbalance" => $closingbalance,
                    );
        echo json_encode($output);
    }
 
    public function getProductByCategoryId(){
        $PostData = $this->input->post();
        
        $productdata = $this->Process_group->getProductByCategoryIdOnProcessGroup($PostData['categoryid']);
        echo json_encode($productdata);
    }
    public function exporttoexcelvendorjobworkreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vendor Job Work','Export to excel vendor job work report.');
        }
        $vendorid = $_REQUEST['vendorid'];
        $startdate = $_REQUEST['startdate'];
        $enddate = $_REQUEST['enddate'];

        $this->load->model("Member_model","Member");
        $memberdata = $this->Member->getMemberDataByID($vendorid);

        $date = $startdate.' to '.$enddate;
        $openingbalance = $this->Vendor_job_work->getOpeningBalance();
        $closingbalance = $this->Vendor_job_work->getColsingBalance();

        $reportdata = $this->Vendor_job_work->exportreportdata();
        $data = array();
        $srno=1;

        $data[] = array("Opening Qty","","","","","","","","","","","",numberFormat(($openingbalance!=''?$openingbalance:0),2,','));

        foreach ($reportdata as $datarow) {         
            
            $data[] = array($srno++,
                
                $datarow->jobcard,
                $datarow->jobname,
                $datarow->batchno,
                ($datarow->ordernumber!=""?$datarow->ordernumber:"-"),
                $datarow->vendorname,
                $datarow->productname,
                numberFormat(($datarow->inqty!=''?$datarow->inqty:0),2,','),
                numberFormat(($datarow->outqty!=''?$datarow->outqty:0),2,','),
                numberFormat(($datarow->rejectqty!=''?$datarow->rejectqty:0),2,','),
                numberFormat(($datarow->wastageqty!=''?$datarow->wastageqty:0),2,','),
                numberFormat(($datarow->lostqty!=''?$datarow->lostqty:0),2,','),
                numberFormat(($datarow->balanceqty!=''?$datarow->balanceqty:0),2,','),
                ($datarow->transactiondate!="")?$this->general_model->displaydate($datarow->transactiondate):""
            );
            
        }
        $data[] = array("Closing Qty","","","","","","","","","","","",numberFormat(($closingbalance!=''?$closingbalance:0),2,','));
        /* $data[] = array("","","Total",numberFormat($totalqty,2,','),
                numberFormat($totalamount,2,',')
            ); */
        
        $headings = array('Sr. No.','Job Card','Job Name','Batch No.','OrderID','Vendor Name','Product Name','In Qty','Out Qty','Rejection Qty','Wastage Qty','Lost Qty','Balance Qty','Transaction Date'); 

        // $this->general_model->exporttoexcel($data,"A1:Q1","Vendor Job Work",$headings,"Vendorjobworkreport.xls","H:M");

        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getDefaultStyle()->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->excel->getActiveSheet()->getStyle("H:M")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle("A1:N1")->getFont()->setBold(true);
        $this->excel->getActiveSheet()->mergeCells('A2:L2');

        //name the worksheet
        $this->excel->getActiveSheet()->setTitle("Vendor Job Work");
        
        $col = 'A';
        foreach($headings as $cell) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $this->excel->getActiveSheet()->setCellValue($col.'1',$cell);
            $col++;
        }
        
        $this->excel->getActiveSheet()->fromArray($data, null, 'A2');

        $this->excel->getActiveSheet()->getStyle("A2")->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle("M2")->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle("M2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('A2:N2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('#d20048');
        $this->excel->getActiveSheet()->getStyle('A2:N2')->getFont()->getColor()->setRGB('FFFFFF');

        $highestRow = $this->excel->setActiveSheetIndex(0)->getHighestRow();
        $this->excel->getActiveSheet()->getStyle("A".$highestRow)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle("M".$highestRow)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle("M".$highestRow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $this->excel->getActiveSheet()->getStyle('A'.$highestRow.':N'.$highestRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('#d20048');
        $this->excel->getActiveSheet()->getStyle('A'.$highestRow.':N'.$highestRow)->getFont()->getColor()->setRGB('FFFFFF');


        ob_end_clean();

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="Vendorjobworkreport.xls"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        
        //force user to download the Excel file without writing it to server's HD
        ob_end_clean();
        ob_start();
        $objWriter->save('php://output');
        
    }
    public function exporttopdfvendorjobworkreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Vendor Job Work', 'Export to PDF vendor job work report.');
        }

        $PostData = $this->input->get();
        $vendorid = $_REQUEST['vendorid'];
        $startdate = $_REQUEST['startdate'];
        $enddate = $_REQUEST['enddate'];

        $this->load->model("Member_model","Member");
        $PostData['memberdata'] = $this->Member->getMemberDataByID($vendorid);

        $PostData['date'] = $startdate.' to '.$enddate;
        $PostData['openingbalance'] = $this->Vendor_job_work->getOpeningBalance();
        $PostData['closingbalance'] = $this->Vendor_job_work->getColsingBalance();

        $PostData['reportdata'] = $this->Vendor_job_work->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Vendorjobworkreportformat', $PostData, true);
        // echo $header.$html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Vendor-job-work-report.pdf";
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
    public function printvendorjobworkreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Vendor Job Work', 'Print vendor job work report.');
        }
        $PostData = $this->input->post();
        $vendorid = $_REQUEST['vendorid'];
        $startdate = $_REQUEST['startdate'];
        $enddate = $_REQUEST['enddate'];

        $this->load->model("Member_model","Member");
        $PostData['memberdata'] = $this->Member->getMemberDataByID($vendorid);

        $PostData['date'] = $startdate.' to '.$enddate;
        $PostData['openingbalance'] = $this->Vendor_job_work->getOpeningBalance();
        $PostData['closingbalance'] = $this->Vendor_job_work->getColsingBalance();

        $PostData['reportdata'] = $this->Vendor_job_work->exportreportdata();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Vendorjobworkreportformat", $PostData, true);

        echo json_encode($html);
    }
}?>