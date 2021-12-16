<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Abc_inventory_analysis extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Abc_inventory_analysis');
        $this->load->model('Abc_inventory_analysis_model', 'Abc_inventory_analysis');
    }
    public function index() {
        $this->viewData['title'] = "ABC Inventory Analysis";
        $this->viewData['module'] = "report/Abc_inventory_analysis";
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("abc_inventory_analysis", "pages/abc_inventory_analysis.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $this->load->model("Product_combination_model","Product_combination");
        $list = $this->Abc_inventory_analysis->get_datatables();
        
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
            if(number_format($datarow->minprice,2,'.','') == number_format($datarow->maxprice,2,'.','')){
                $price = numberFormat($datarow->minprice, 2, ',');
            }else{
                $price = numberFormat($datarow->minprice, 2, ',')." - ".numberFormat($datarow->maxprice, 2, ',');
            }
            $row[] = $price;
            $row[] = $datarow->sold;
            $row[] = number_format($datarow->cumulativeshare,2,'.','')."%";

            if($datarow->cumulativeshare >= $_REQUEST['classA']){
                $row[] = "A";
            }else if($datarow->cumulativeshare <= $_REQUEST['classC']){
                $row[] = "C";
            }else{
                $row[] = "B";
            }
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Abc_inventory_analysis->count_all(),
                        "recordsFiltered" => $this->Abc_inventory_analysis->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function exportToExcelABCInventoryReport(){
        
        $exportdata = $this->Abc_inventory_analysis->exportABCInventoryReport();
        
        $data = $color = array();
        $srno = 0;
        $cell = 2;
        foreach ($exportdata as $row) {         
            
            if($row->cumulativeshare >= $_REQUEST['classA']){
                $class = "A";
                if(!empty($color['A'])){
                    $color['A']['end'] = $cell;
                }else{
                    $color['A'] = array("start"=>$cell,"end"=>$cell,"color"=>"a9d18e");
                }
            }else if($row->cumulativeshare <= $_REQUEST['classC']){
                $class = "C";
                if(!empty($color['C'])){
                    $color['C']['end'] = $cell;
                }else{
                    $color['C'] = array("start"=>$cell,"end"=>$cell,"color"=>"e2f0d9");
                }
            }else{
                $class = "B";
                if(!empty($color['B'])){
                    $color['B']['end'] = $cell;
                }else{
                    $color['B'] = array("start"=>$cell,"end"=>$cell,"color"=>"c5e0b4");
                }
            }

            $data[] = array(++$srno,
                            $row->productname,
                            ($row->sku!=''?$row->sku:'-'),
                            numberFormat($row->price,2,','),
                            $row->sold,
                            number_format($row->cumulativeshare,2,'.','')."%",
                            $class
                        );


            $cell++;
        }
        $colorarray = array();
        foreach($color as $k=>$clr){
            $colorarray["A".$clr['start'].":G".$clr['end']] = $clr['color'];
            
        }
        
        $headings = array('Sr. No.','Product Name','SKU','Price','Sold','Cumulative Share','Class'); 
        $this->general_model->exporttoexcel($data,"A1:G1","ABC Inventory Analysis Report",$headings,"ABC-Inventory-Analysis-Report.xls",array("D:F"),'',$colorarray);
    }
    public function exportToPDFABCInventoryReport() {

        $PostData['classA'] = $_REQUEST['classA'];
        $PostData['classB'] = $_REQUEST['classB'];
        $PostData['classC'] = $_REQUEST['classC'];
        $PostData['reportdata'] = $this->Abc_inventory_analysis->exportABCInventoryReport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Abcreportformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "ABC-Inventory-Analysis-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   40, // margin top
                   15, // margin bottom
                    3, // margin header
                    10); // margin footer
        
        ini_set("pcre.backtrack_limit", "5000000");
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300);
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
    public function printABCInventoryReport() {
        
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Abc_inventory_analysis->exportABCInventoryReport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html.=$this->load->view(ADMINFOLDER . 'report/Abcreportformatforpdf', $PostData,true);
        
        echo json_encode($html); 
    }
}