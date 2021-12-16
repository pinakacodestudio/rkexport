<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation_to_order_conversion extends Channel_Controller 
{
    public $viewData = array();
    function __construct(){
        parent::__construct();

        $this->viewData = $this->getChannelSettings('submenu', 'Quotation_to_order_conversion');
        $this->load->model("Channel_model","Channel");
        $this->load->model('Quotation_to_order_conversion_model','Quotation_to_order_conversion');
        
    }
    public function index(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Quotation to order conversion";
        $this->viewData['module'] = "quotation_to_order_conversion/Quotation_to_order_conversion";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Quotation_to_order_conversion","pages/quotation_to_order_conversion.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing(){
       
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $list = $this->Quotation_to_order_conversion->get_datatables();
        
       
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = "";
            
            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }
            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->id.'" title="'.ucwords($datarow->name).'">'.ucwords($datarow->name).' ('.$datarow->membercode.')</a>';
            $row[] = $datarow->noofquotation;
            $row[] = $datarow->noofconverted;
            $row[] = $datarow->conversionrate;
            $row[] = number_format($datarow->netquotation,2,".",',');
            //$row[] = $datarow->netquotation;
            $data[] = $row;
        }
        $output = array(
            "recordsTotal" => $this->Quotation_to_order_conversion->count_all(),
            "recordsFiltered" => $this->Quotation_to_order_conversion->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function exporttoexcelquotationtoorderconversionreport()
    {
        $exportdata = $this->Quotation_to_order_conversion->exporttoexcelquotationtoorderconversionreport();
       
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row){
            if($row->channelid != 0){
                $name = ucwords($row->name);
            }else{
                $name = 'COMPANY';
            }
            $data[] = array(++$srno,$name,
                            $row->membercode,
                            $row->noofquotation,
                            $row->noofconverted,
                            $row->conversionrate,
                            number_format($row->netquotation,2,".",','));
                           //$this->general_model->displaydatetime($row->createddate));
        }
        $headings = array('Sr. No.',Member_label.' Name','No of Quotation','No. of Converted','Conversion Rate','Net Quotation'); 
        $this->general_model->exporttoexcel($data,"A1:F1","Quotation to Order Conversion",$headings,"Quotation-to-Order-Conversion.xls","G");
        
    }
    function exporttopdfquotationtoorderconversionreport() {
        
        $PostData['reportdata'] = $this->Quotation_to_order_conversion->exporttoexcelquotationtoorderconversionreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Quotationtoorderconversionreportformatforpdf', $PostData,true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Quotation-to-Order-Conversion.pdf";
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
    public function printquotationtoorderconversionreport(){
        
        $PostData = $this->input->post();
        
        $PostData['reportdata'] =$this->Quotation_to_order_conversion->exporttoexcelquotationtoorderconversionreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        
        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html .= $this->load->view(ADMINFOLDER."report/Quotationtoorderconversionreportformatforpdf.php",$PostData,true);

        echo json_encode($html); 
    }
}