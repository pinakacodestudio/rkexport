<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Sales_report_model', 'Sales_report');
        $this->load->model("Channel_model","Channel");
        $this->viewData = $this->getChannelSettings('submenu', 'Sales_report');
    }
    public function index() {
        $this->viewData['title'] = "Sales Report";
        $this->viewData['module'] = "report/Sales_report";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Sales_report", "pages/sales_report.js");

        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function getsalesdata(){
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
        $req['COLUMNS'][] = array('title'=>Member_label.' Name',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Total Sales',"sortable"=>true,"class"=>"text-right");
        
		/* foreach($memberdata as $memberrow){
			$req['COLUMNS'][] = array('title'=>$memberrow['name'],"sortable"=>true);
        } */
        foreach ($Date as $daterow) {
            $req['COLUMNS'][] = array('title'=>$this->general_model->displaydate($daterow,$dateformat),"sortable"=>true,"class"=>"text-right");
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $salesdata = $this->Sales_report->getsalesdata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($salesdata, 'date');

        
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
                    $searchdatedata = array_intersect_key($salesdata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalsales'],2,'.',',');
                        $data[]= $searchdatedata[$searchkeys[0]]['totalsales'];
                    }else{
                        $formateddata[]=$data[]= '0.00';
                    }
                }else{
                    $formateddata[]=$data[]= '0.00';
                }
            }

			$req['DATA'][] = array_merge(array(++$index,ucwords($memberrow['name']),number_format(array_sum($data),2,'.',',')),$formateddata);

        }
		
		echo json_encode($req);
    }
   
    public function exportsalesreport(){

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
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }

        $headings = array_merge(array('Sr.No.',Member_label.' Name','Total Sales'),$headings);
        
        $memberid = implode(',',array_column($memberdata,'id'));
        $salesdata = $this->Sales_report->getsalesdata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($salesdata, 'date');

        
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
                    $searchdatedata = array_intersect_key($salesdata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalsales'],2,'.',',');
                        $data[]= $searchdatedata[$searchkeys[0]]['totalsales'];
                    }else{
                        $formateddata[]= '0';
                    }
                }else{
                    $formateddata[]= '0';
                }
            }

			$result[] = array_merge(array(++$index,ucwords($memberrow['name']),number_format(array_sum($data),2,'.',',')),$formateddata);

        }
        
        $this->general_model->exporttoexcel($result,"A1:DD1","Sales Report",$headings,"SalesReport.xls","C:ZZ");
        
    }

    public function exporttopdfsalesreport() {
        
        $PostData = $this->input->get();
        //print_r($PostData);
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
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }

        $memberid = implode(',',array_column($memberdata,'id'));
        $salesdata = $this->Sales_report->getsalesdata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($salesdata, 'date');

        
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
                    $searchdatedata = array_intersect_key($salesdata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalsales'],2,'.','');
                        $data[]= (int)($searchdatedata[$searchkeys[0]]['totalsales']);
                    }else{
                        $formateddata[]= '0';
                    }
                }else{
                    $formateddata[]= '0';
                }
                
            }

			$result[] = array_merge(array("membername"=>ucwords($memberrow['name']),"total"=>number_format(array_sum($data),2,'.','')),array("datewisesales"=>$formateddata));

        }

        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;
        // echo "<pre>"; print_r($PostData['reportdata']); exit;
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Salesreportformatforpdf', $PostData,true);
        // echo $header.$html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Sales-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage('L', // L - landscape, P - portrait 
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

    public function printsalesreport() {
        
        $PostData = $this->input->post();
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
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }

        $memberid = implode(',',array_column($memberdata,'id'));
        $salesdata = $this->Sales_report->getsalesdata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($salesdata, 'date');
        
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
                    $searchdatedata = array_intersect_key($salesdata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalsales'],2,'.','');
                        $data[]= (int)($searchdatedata[$searchkeys[0]]['totalsales']);
                    }else{
                        $formateddata[]= '0';
                    }
                }else{
                    $formateddata[]= '0';
                }
                
            }

			$result[] = array_merge(array("membername"=>ucwords($memberrow['name']),"total"=>number_format(array_sum($data),2,'.','')),array("datewisesales"=>$formateddata));

        }

        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html['content'] = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        
        $html['content'] .= $this->load->view(ADMINFOLDER."report/Salesreportformatforpdf",$PostData,true);
        echo json_encode($html); 
    }
}