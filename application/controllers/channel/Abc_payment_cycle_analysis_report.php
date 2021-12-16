<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Abc_payment_cycle_analysis_report extends Channel_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Abc_payment_cycle_analysis_report');
        $this->load->model("Abc_payment_cycle_analysis_report_model","Abc_payment_cycle_analysis_report");
        $this->load->model("Channel_model","Channel");
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "ABC Payment Cycle Analysis Report";
        $this->viewData['module'] = "report/Abc_payment_cycle_analysis_report";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Abc_payment_cycle_analysis_report->getChannelListOnABCPAymentReport($MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Abc_payment_cycle_analysis_report","pages/abc_payment_cycle_analysis_report.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Abc_payment_cycle_analysis_report->get_datatables();
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $totalaveragepaymentcycle = (!empty($list))?array_sum(array_column($list, "averagepaymentcycle")):0;

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $varianthtml = "";
            $row[] = ++$counter;
            
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            $channellabel="";
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            $row[] = '<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->id.'" title="'.ucwords($datarow->name).'" target="_blank">'.$channellabel." ".ucwords($datarow->name).' ('.$datarow->membercode.')</a>';

            $row[] = $datarow->totalinvoice;
            $row[] = numberFormat($datarow->invoiceamount,2,',');
            $row[] = $datarow->averagepaymentcycle;

            $cumulativeshare = ($totalaveragepaymentcycle>0)?(100 - ($datarow->averagepaymentcycle * 100) / $totalaveragepaymentcycle):0;
            $row[] = number_format($cumulativeshare,2,'.','')."%";

            if($cumulativeshare >= $_REQUEST['classA']){
                $row[] = "A";
            }else if($cumulativeshare <= $_REQUEST['classC']){
                $row[] = "C";
            }else{
                $row[] = "B";
            }
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Abc_payment_cycle_analysis_report->count_all(),
                        "recordsFiltered" => $this->Abc_payment_cycle_analysis_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function getMemberOnPaymentCycleReportByChannel(){
        
        $PostData = $this->input->post();
        $sellermemberid = $this->session->userdata(base_url().'MEMBERID');
        $memberdata = $this->Abc_payment_cycle_analysis_report->getMemberOnPaymentCycleReportByChannel($PostData['channelid'],$sellermemberid);
        
        echo json_encode($memberdata);
    }
    public function exportToExcelABCPaymentCycleReport(){
        
        $exportdata = $this->Abc_payment_cycle_analysis_report->exportABCPaymentCycleReport();
        
        $data = $color = array();
        $srno = 0;
        $cell = 2;
        $totalaveragepaymentcycle = (!empty($exportdata))?array_sum(array_column($exportdata, "averagepaymentcycle")):0;
        foreach ($exportdata as $row) {         
            
            $cumulativeshare = 100 - ($row->averagepaymentcycle * 100) / $totalaveragepaymentcycle;
            if($cumulativeshare >= $_REQUEST['classA']){
                $class = "A";
                if(!empty($color['A'])){
                    $color['A']['end'] = $cell;
                }else{
                    $color['A'] = array("start"=>$cell,"end"=>$cell,"color"=>"a9d18e");
                }
            }else if($cumulativeshare <= $_REQUEST['classC']){
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
                            $row->channel,
                            ucwords($row->name)." (".$row->membercode.")",
                            $row->totalinvoice,
                            numberFormat($row->invoiceamount,2,','),
                            $row->averagepaymentcycle,
                            number_format($cumulativeshare,2,'.','')."%",
                            $class
                        );


            $cell++;
        }
        $colorarray = array();
        foreach($color as $k=>$clr){
            $colorarray["A".$clr['start'].":H".$clr['end']] = $clr['color'];
            
        }
        
        $headings = array('Sr. No.','Channel',Member_label,'Total Invoice','Invoice Amount','Avg. Payment Cycle Days','Cumulative Share','Class'); 
        $this->general_model->exporttoexcel($data,"A1:I1","ABC Payment Cycle Report",$headings,"ABC-Payment-Cycle-Report.xls",array("D:G"),'',$colorarray);
    }

    function exportToPDFABCPaymentCycleReport() {
        
        $PostData['classA'] = $_REQUEST['classA'];
        $PostData['classB'] = $_REQUEST['classB'];
        $PostData['classC'] = $_REQUEST['classC'];
        $PostData['reportdata'] = $this->Abc_payment_cycle_analysis_report->exportABCPaymentCycleReport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Abcpaymentcycleformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "ABC-Payment-Cycle-Analysis-Report.pdf";
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

    public function printabcpaymentcyclereport()
    {

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Abc_payment_cycle_analysis_report->exportABCPaymentCycleReport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Abcpaymentcycleformatforpdf", $PostData, true);

        echo json_encode($html);
    }
}
