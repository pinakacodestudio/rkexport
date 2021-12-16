<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Points_history_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Reward_point_history_model', 'Reward_point_history');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Points_history_report');
    }
    public function index() {
        $this->viewData['title'] = "Points History Report";
        $this->viewData['module'] = "report/Points_history_report";
        
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Points History','View point history.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Points_history_report", "pages/points_history_report.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $list = $this->Reward_point_history->get_datatables();
        // echo $this->db->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $creditpoints = $debitpoints = 0;
            
            $row[] = ++$counter;

            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.$datarow->sellername.'">'.$datarow->sellername.' ('.$datarow->sellercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.$datarow->buyername.' ('.$datarow->buyercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

           // $row[] = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" target="_blank" title="'.$datarow->ordernumber.'">'.$datarow->ordernumber."</a>";
           if ($datarow->type==1) {
               $debitpoints = $datarow->point;
            }else{
                $creditpoints = $datarow->point;
           }

           $row[] = '<span class="pull-right">'.$creditpoints.'</span>';
           $row[] = '<span class="pull-right">'.$debitpoints.'</span>';
           $row[] = $datarow->pointstatus;
           $row[] = '<span class="pull-right">'.$datarow->closingpoint.'</span>';
           $row[] = $this->Pointtransactiontype[$datarow->transactiontype];

           if(!empty($datarow->orderid)){
            $detail = $datarow->detail.'<br><br><b>Order ID: </b><a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" target="_blank" title="'.$datarow->ordernumber.'">'.$datarow->ordernumber."</a>";
           }else{
            $detail = $datarow->detail;
           }
           $row[] = $detail;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            
            
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Reward_point_history->count_all(),
                        "recordsFiltered" => $this->Reward_point_history->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    function exportpointshistoryreport(){
        
        $salesdata = $this->Reward_point_history->exportpointshistoryreport();
        
        $data = array();
        $totalcreditpoints = $totaldebitpoints = $totalcreditamount = $totaldebitamount = 0;

        foreach ($salesdata as $datarow) {         
            
            $creditpoints = $debitpoints = $creditamount = $debitamount = 0;
            $rate = $datarow->rate;

            if($datarow->sellerchannelid != 0){
                $sellername = $datarow->sellername.' ('.$datarow->sellercode.')';
            }else{
                $sellername = 'COMPANY';
            }
            if($datarow->buyerchannelid != 0){
                $buyername = $datarow->buyername.' ('.$datarow->buyercode.')';
            }else{
                $buyername = 'COMPANY';
            }

            if ($datarow->type==1) {
                $debitpoints = $datarow->point;
                $debitamount = $debitpoints * $rate;
            }else{
                $creditpoints = $datarow->point;
                $creditamount = $creditpoints * $rate;
            }
            
            $data[] = array($sellername,$buyername,
                            $creditpoints,
                            $debitpoints,
                            $datarow->pointstatus,
                            $this->Pointtransactiontype[$datarow->transactiontype],
                            $datarow->detail,
                            $this->general_model->displaydatetime($datarow->createddate));
            
            //$totalrate = $totalrate + $rate;
            /*$totalcreditpoints = $totalcreditpoints + $creditpoints;
            $totaldebitpoints = $totaldebitpoints + $debitpoints;

            $totalcreditamount = $totalcreditamount + $creditamount;
            $totaldebitamount = $totaldebitamount + $debitamount;*/
            
        }
        //$data[] = array('','','Total',$totalcreditpoints,$totalcreditamount,$totaldebitpoints,$totaldebitamount,'','','');
        
        $headings = array('Seller Name','Buyer Name','Credit Points','Debit Points','Point Status','Transaction Type','Detail','Entry Date'); 
        
        $this->general_model->exporttoexcel($data,"A1:J1","Point History Report",$headings,"Point-History-Report.xls");
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $module = ($this->uri->segment(2)=='points-history-report'?'Points History':Member_label);
            $this->general_model->addActionLog(0,$module,'Export to excel point history report.');
        }
    }

    public function exporttopdfpointshistoryreport()
    {

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $module = ($this->uri->segment(2)=='points-history-report'?'Points History':Member_label);
            $this->general_model->addActionLog(0,$module,'Export to PDF point history report.');
        }

        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Reward_point_history->exportpointshistoryreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Pointhistoryreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Point-History-Report.pdf";
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
    public function printpointshistoryreport()
    {

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $module = ($this->uri->segment(2)=='points-history-report'?'Points History':Member_label);
            $this->general_model->addActionLog(0,$module,'Print point history report.');
        }
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Reward_point_history->exportpointshistoryreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Pointhistoryreportformat", $PostData, true);

        echo json_encode($html);
    }
}