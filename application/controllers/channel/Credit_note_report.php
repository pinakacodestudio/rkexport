<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_note_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Credit_note_report_model', 'Credit_note_report');
        $this->load->model("Channel_model","Channel");
        $this->viewData = $this->getChannelSettings('submenu', 'Credit_note_report');
    }
    public function index() {
        $this->viewData['title'] = "Credit Note Report";
        $this->viewData['module'] = "report/Credit_note_report";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Credit_note_report", "pages/credit_note_report.js");

        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function getcreditnotedata(){
        $PostData = $this->input->post();
        //print_r($PostData);
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'0';
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
        $req['COLUMNS'][] = array('title'=>'Sr.No.',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Member',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Total Sales Return',"sortable"=>true,"class"=>"text-right");
        
		/* foreach($memberdata as $memberrow){
			$req['COLUMNS'][] = array('title'=>$memberrow['name'],"sortable"=>true);
        } */
        foreach ($Date as $daterow) {
            $req['COLUMNS'][] = array('title'=>$this->general_model->displaydate($daterow,$dateformat),"sortable"=>true,"class"=>"text-right");
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $creditnotedata = $this->Credit_note_report->getcreditnotedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
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

			$req['DATA'][] = array_merge(array(++$index,ucwords($memberrow['name']),number_format(array_sum($data),2,'.',',')),$formateddata);

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

        $headings = array_merge(array('Sr.No.','Member','Total Sales Return'),$headings);
        
        $memberid = implode(',',array_column($memberdata,'id'));
        $creditnotedata = $this->Credit_note_report->getcreditnotedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
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
}