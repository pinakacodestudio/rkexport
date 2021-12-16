<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_return_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Credit_note_report_model', 'Sales_return_report');
        $this->load->model("Channel_model","Channel");
        $this->viewData = $this->getChannelSettings('submenu', 'Sales_return_report');
    }
    public function index() {
        $this->viewData['title'] = "Sales Return Report";
        $this->viewData['module'] = "report/Credit_note_report";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Sales_return_report", "pages/credit_note_report.js");

        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function getcreditnotedata(){
        $PostData = $this->input->post();
        //print_r($PostData);
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'';
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';
        $status = (!empty($PostData['status']))?implode(',',$PostData['status']):'';
        $datetype = $PostData['datetype'];

        $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid,"");
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }
        
        $req = array();
        $req['COLUMNS'][] = array('title'=>'Sr.No.',"sortable"=>true,"class"=>"width8");
        $req['COLUMNS'][] = array('title'=>Member_label,"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>"Date","sortable"=>true,"class"=>"width15");
        $req['COLUMNS'][] = array('title'=>'Total Sales Return',"sortable"=>true,"class"=>"text-right width15");
        
		/* foreach($memberdata as $memberrow){
			$req['COLUMNS'][] = array('title'=>$memberrow['name'],"sortable"=>true);
        } */
        /* foreach ($Date as $daterow) {
            $req['COLUMNS'][] = array('title'=>$this->general_model->displaydate($daterow,$dateformat),"sortable"=>true,"class"=>"text-right");
        } */
        $memberid = implode(',',array_column($memberdata,'id'));
        $creditnotedata = $this->Sales_return_report->getcreditnotedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($creditnotedata, 'date');

        
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company","channelid"=>0));
        }
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        // print_r($memberdata); exit;
        foreach ($Date as $index=>$daterow) {
            foreach ($memberdata as $memberrow) {
                
                if($memberrow['channelid']!=0){
                    $channellabel = "";
                    $key = array_search($memberrow['channelid'], array_column($channeldata, 'id'));
                    if(!empty($channeldata) && isset($channeldata[$key])){
                        $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                    }
                    $membername = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$memberrow['id'].'" title="'.ucwords($memberrow['name']).'" target="_blank">'.ucwords($memberrow['name']).'</a>';
                }else{
                    $membername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
                }
            
                if (in_array($daterow, $datearray)) {

                    //check date on date array
                    $keys = array_keys($datearray, $daterow);
            
                    //get array from search key
                    $searchdatedata = array_intersect_key($creditnotedata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $totalsalesreturn = $searchdatedata[$searchkeys[0]]['totalsalesreturn'];
                    }else{
                        $totalsalesreturn = '0.00';
                    }
                }else{
                    $totalsalesreturn = '0.00';
                }
                $req['DATA'][] = array(++$index,$membername,$this->general_model->displaydate($daterow,$dateformat),number_format($totalsalesreturn,2,'.',','));
            }

        }
		
		echo json_encode($req);
    }
   
    public function exportcreditnotereport(){

        $PostData = $this->input->get();
        //print_r($PostData); exit;
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';
        $status = ($PostData['status']!='')?$PostData['status']:'';
        $datetype = $PostData['datetype'];

        $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid);
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }

        $headings = array_merge(array('Sr.No.',Member_label,'Total Sales Return'),$headings);
        
        $memberid = implode(',',array_column($memberdata,'id'));
        $creditnotedata = $this->Sales_return_report->getcreditnotedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($creditnotedata, 'date');

        
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company"));
        }
        foreach ($memberdata as $index=>$memberrow) {
            $formateddata = $data = array();
            foreach ($Date as $daterow) {

                if (in_array($daterow, $datearray)) {

                    //check date on date array
                    $keys = array_keys($datearray, $daterow);
            
                    //get array from search key
                    $searchdatedata = array_intersect_key($creditnotedata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalsalesreturn'],2,'.',',');
                        $data[]= $searchdatedata[$searchkeys[0]]['totalsalesreturn'];
                    }else{
                        $formateddata[]= '0.00';
                    }
                }else{
                    $formateddata[]= '0.00';
                }
            }

			$result[] = array_merge(array(++$index,ucwords($memberrow['name']),number_format(array_sum($data),2,'.',',')),$formateddata);

        }
        
        $this->general_model->exporttoexcel($result,"A1:DD1","Credit Note Report",$headings,"CreditNoteReport.xls","C:ZZ");
        
    }

    public function exporttopdfsalesreturnreport() {
        
        $PostData = $this->input->get();
        // print_r($PostData); exit;
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = (!empty($PostData['memberid']))?$PostData['memberid']:'';
        $status = ($PostData['status']!='')?$PostData['status']:'';
        $datetype = $PostData['datetype'];

       $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid);
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $creditnotedata = $this->Sales_return_report->getcreditnotedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        
        $datearray = array_column($creditnotedata, 'date');

        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company","channelid"=>0));
        }

        $result = array();
        
        foreach ($Date as $index=>$daterow) {
            foreach ($memberdata as $memberrow) {
                
                if($memberrow['channelid']!=0){
                    $membername = $memberrow['name'];
                }else{
                    $membername = 'COMPANY';
                }
                
                if (in_array($daterow, $datearray)) {

                    //check date on date array
                    $keys = array_keys($datearray, $daterow);
            
                    //get array from search key
                    $searchdatedata = array_intersect_key($creditnotedata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $totalsalesreturn= $searchdatedata[$searchkeys[0]]['totalsalesreturn'];
                    }else{
                        $totalsalesreturn = '0.00';
                    }
                }else{
                    $totalsalesreturn = '0.00';
                }

                $result[] = array("membername"=>$membername,"date"=>$this->general_model->displaydate($daterow,$dateformat),"salesreturn"=>number_format($totalsalesreturn,2,'.',''));
            }


        }
        $PostData['reportdata'] = $result;
        
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Salesreturnreportformat', $PostData,true);
        // echo $header.$html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Sales-Return-Report.pdf";
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

    public function printsalesreturnreport() {
        
        $PostData = $this->input->post();
        // echo "<pre>"; print_r($PostData); exit;
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = (!empty($PostData['memberid']))?$PostData['memberid']:'';
        $status = ($PostData['status']!='')?$PostData['status']:'';
        $datetype = $PostData['datetype'];

        $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid);
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $creditnotedata = $this->Sales_return_report->getcreditnotedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        
        $datearray = array_column($creditnotedata, 'date');

        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company","channelid"=>0));
        }
        $result = array();
        
        foreach ($Date as $index=>$daterow) {
            foreach ($memberdata as $memberrow) {
                
                if($memberrow['channelid']!=0){
                    $membername = $memberrow['name'];
                }else{
                    $membername = 'COMPANY';
                }
                
                if (in_array($daterow, $datearray)) {

                    //check date on date array
                    $keys = array_keys($datearray, $daterow);
            
                    //get array from search key
                    $searchdatedata = array_intersect_key($creditnotedata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $totalsalesreturn= $searchdatedata[$searchkeys[0]]['totalsalesreturn'];
                    }else{
                        $totalsalesreturn = '0.00';
                    }
                }else{
                    $totalsalesreturn = '0.00';
                }

                $result[] = array("membername"=>$membername,"date"=>$this->general_model->displaydate($daterow,$dateformat),"salesreturn"=>number_format($totalsalesreturn,2,'.',''));
            }
        }

        $PostData['reportdata'] = $result;
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html .= $this->load->view(ADMINFOLDER."report/Salesreturnreportformat",$PostData,true);

        echo json_encode($html); 
    }
}