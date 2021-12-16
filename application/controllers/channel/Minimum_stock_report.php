<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Minimum_stock_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Minimum_stock_report');
        $this->load->model('Minimum_stock_report_model', 'Minimum_stock_report');
    }
    public function index() {
        $this->viewData['title'] = "Minimum Stock Report";
        $this->viewData['module'] = "report/Minimum_stock_report";
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("minimum_stock_report", "pages/minimum_stock_report.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $this->load->model("Product_combination_model","Product_combination");
        $list = $this->Minimum_stock_report->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
           
            $varianthtml = "";
            $row[] = ++$counter;
            if($datarow->isuniversal==0 && $datarow->variantid!=''){
               
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->productid,$datarow->variantid);

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
                $row[] = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.$datarow->productname.'</a>';
            }else{
                $row[] = $datarow->productname;
            }
            $row[] = ($datarow->sku!=''?$datarow->sku:'-');
            $row[] = $datarow->minimumstocklimit;
            $row[] = $datarow->stock;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Minimum_stock_report->count_all(),
                        "recordsFiltered" => $this->Minimum_stock_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function exportToExcelStockReport(){
        
        $exportdata = $this->Minimum_stock_report->exportMinimumStockReport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            $data[] = array(++$srno,
                            $row->productname,
                            ($row->sku!=''?$row->sku:'-'),
                            $row->minimumstocklimit,
                            $row->stock
                        );
        }
        
        $headings = array('Sr. No.','Product Name','SKU','Minimum Stock Limit','Current Stock'); 
        $this->general_model->exporttoexcel($data,"A1:N1","Minimum Stock Report",$headings,"Minimum-Stock-Report.xls",array("D","E"));
    }
    function exportToPDFStockReport() {
        
        $PostData['reportdata'] = $this->Minimum_stock_report->exportMinimumStockReport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Minimumstockreportformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Minimum-Stock-Report.pdf";
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

    public function printStockReport(){
        $PostData = $this->input->post();
        
        $PostData['reportdata'] =$this->Minimum_stock_report->exportMinimumStockReport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        
        $html['content'] = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html['content'] .= $this->load->view(ADMINFOLDER."report/Minimumstockreportformatforpdf.php",$PostData,true);
        echo json_encode($html); 
    }
}