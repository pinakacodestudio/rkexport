<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Balance_report');
        $this->load->model('Balance_report_model', 'Balance_report');
    }
    public function index() {
        $this->viewData['title'] = "Balance Report";
        $this->viewData['module'] = "report/Balance_report";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,"memberchannel");

        $this->channel_headerlib->add_plugin("datatables","datatables/fixedColumns.dataTables.min.css");
        $this->channel_headerlib->add_javascript_plugins("datatables","datatables/dataTables.fixedColumns.min.js");

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Balance_report", "pages/balance_report.js");

        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->Balance_report->get_datatables();
        // echo $this->db->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $index => $datarow) { 

            $row = array();
            
            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $link = CHANNEL_URL.'member/member-detail/'.$datarow->memberid;
                $member = $channellabel.'<a href="'.$link.'" target="_blank" title="'.ucwords($datarow->membername).'">'.ucwords($datarow->membername).' ('.$datarow->membercode.')'."</a>";
            }else{
                $member = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = ++$index;
            $row[] = $member;
            $row[] = $this->general_model->displaydate($this->general_model->getCurrentDate());
            $row[] = number_format($datarow->closingbalance,2,'.',',');
            $data[] = $row;
            
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Balance_report->count_all(),
                        "recordsFiltered" => $this->Balance_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function exportbalancereport(){

        $reportdata = $this->Balance_report->exportbalancereport();
        $result = array();
        $headings = array('Sr. No.','Receiver '.Member_label,'Balance Date','Total Amount');
        foreach ($reportdata as $index=>$datarow) {

            $membername = ($datarow['channelid'] != 0)?ucwords($datarow['membername']).' ('.$datarow['membercode'].')':'COMPANY';
          
            $row = array();
            $row[] = ++$index;
            $row[] = $membername;
            $row[] = $this->general_model->displaydate($this->general_model->getCurrentDate());
            $row[] = numberFormat($datarow['closingbalance'],2,',');
            $result[] = $row;
        }
        $this->general_model->exporttoexcel($result,"A1:DD1","Balance Report",$headings,"BalanceReport.xls","D");
    }

    public function exporttopdfbalancereport() {
        
        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Balance_report->exportbalancereport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Balancereportformat', $PostData,true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Balance-Report.pdf";
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

    public function printbalancereport() {
        
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Balance_report->exportbalancereport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html .= $this->load->view(ADMINFOLDER."report/Balancereportformat",$PostData,true);

        echo json_encode($html); 
    }
}