<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Stock_report_model', 'Stock_report');
        $this->load->model("Channel_model","Channel"); 
        $this->viewData = $this->getChannelSettings('submenu', 'Stock_report');
    }
    public function index() {
        $this->viewData['title'] = "Stock Report";
        $this->viewData['module'] = "report/Stock_report";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Stock_report", "pages/stock_report.js");

        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $this->load->model("Product_combination_model","Product_combination");
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $channeldata = $this->Channel->getChannelList();
        $list = $this->Stock_report->get_datatables();
        // print_r($list); exit;
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $varianthtml = '';
            $productname = '';
            
            $channellabel = '';
            if($datarow->isuniversal==0 && $datarow->variantid!=''){
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
            }

            $row[] = ++$counter;

            if($datarow->memberid != 0){
                $key = array_search($datarow->memberchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->memberid){
                    $row[] = $channellabel.ucwords($datarow->membername).' ('.$datarow->membercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->memberid.'" target="_blank" title="'.$datarow->membername.'">'.ucwords($datarow->membername).' ('.$datarow->membercode.')'."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = $productname;
            $row[] = "<span class='pull-right'>".$datarow->openingstock."</span>";
            $row[] = "<span class='pull-right'>".$datarow->closingstock."</span>";
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Stock_report->count_all(),
                        "recordsFiltered" => $this->Stock_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function exportstockreport(){
        
        $stockdata = $this->Stock_report->exportstockreport();
        $data = array();
        foreach ($stockdata as $datarow) {         
            
            if($datarow->memberid != 0){
                $membername = $datarow->membername.' ('.$datarow->membercode.')';
            }else{
                $membername = 'COMPANY';
            }
            
            $data[] = array($membername,$datarow->productname,$datarow->openingstock,$datarow->closingstock);
            
        }
        $headings = array('Member Name','Product Name','Opening Stock','Closing Stock'); 

        $this->general_model->exporttoexcel($data,"A1:AA1","Stock Report",$headings,"StockReport.xls","");
        
    }
    public function getproduct(){
        $PostData = $this->input->post();
        if($PostData['channelid']==0){
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }else{
            $channelid = $PostData['channelid'];
            $memberid = $PostData['memberid'];
        }
        $data = $this->Member->getMemberProductsByMemberID($channelid,$memberid);
        echo json_encode($data);
    }
    public function exporttopdfstockreport()
    {
        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Stock_report->exportstockreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Productstockreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Product-Stock-Report.pdf";
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
    public function printstockreport()
    {
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Stock_report->exportstockreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Productstockreportformat", $PostData, true);

        echo json_encode($html);
    }
}