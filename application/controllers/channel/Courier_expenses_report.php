<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Courier_expenses_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Courier_expenses_report');
        $this->load->model('Side_navigation_model');
        $this->load->model('Courier_expenses_report_model', 'Courier_expenses_report');
    }
    public function index() {
        $this->viewData['title'] = "Courier Expenses Report";
        $this->viewData['module'] = "report/Courier_expenses_report";
        
        $this->load->model('City_model', 'City');
        $this->viewData['citydata'] = $this->City->getCourierExpensesCity();

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model('Courier_company_model', 'Courier_company');
        $this->viewData['couriercompanylist'] = $this->Courier_company->getActiveCouriercompanyListBYCorierExpense($CHANNELID,$MEMBERID);
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Courier_expenses", "pages/courier_expenses_report.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $list = $this->Courier_expenses_report->get_datatables();
        // echo $this->readdb->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $Courierexpenses) {         
            $row = array();
            $channellabel = "";

            if($Courierexpenses->buyerchannelid != 0){
                $key = array_search($Courierexpenses->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }
            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$Courierexpenses->memberid.'" title="'.ucwords($Courierexpenses->membername).'">'.ucwords($Courierexpenses->membername).' ('.$Courierexpenses->buyercode.')'.'</a>';
            
            $row[] = '<a href="'.CHANNEL_URL.'invoice/view-invoice/'.$Courierexpenses->invoiceid.'" title="View Invoice" target="_blank">'.$Courierexpenses->invoiceno.'</a>';
            $row[] = ucwords($Courierexpenses->companyname);
            $row[] = $Courierexpenses->trackingcode;
            $row[] = number_format($Courierexpenses->shippingamount,2,'.',',');
            $row[] = $this->general_model->displaydatetime($Courierexpenses->createddate);

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Courier_expenses_report->count_all(),
                        "recordsFiltered" => $this->Courier_expenses_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    function exporttoexcelcourierexpensesreport(){
        
        $exportdata = $this->Courier_expenses_report->exportcourierexpensesreport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            if($row->buyerchannelid != 0){
                $buyername = $row->membername.' ('.$row->buyercode.')';
            }else{
                $buyername = 'COMPANY';
            }
            
            $data[] = array(++$srno,$buyername,
                            $row->invoiceno,
                            ucwords($row->companyname),
                            $row->trackingcode,
                            numberFormat($row->shippingamount,2,','),
                            $this->general_model->displaydatetime($row->createddate));
        }
        
        $headings = array('Sr. No.','Buyer Name','Invoice No.','Company Name','Tracking No.','Expenses','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:G1","Courier Expenses Report",$headings,"Courier-Expenses-Report.xls","F");
    }
    public function exporttopdfcourierexpensesreport()
    {
        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Courier_expenses_report->exportcourierexpensesreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Courierexpensesreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Courier-Expenses-Report.pdf";
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
    public function printcourierexpensesreport()
    {
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Courier_expenses_report->exportcourierexpensesreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Courierexpensesreportformat", $PostData, true);

        echo json_encode($html);
    }
}