<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Coupon_code_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Coupon_code_report_model', 'Coupon_code_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Coupon_code_report');
    }
    public function index() {
        $this->viewData['title'] = "Coupon Code Report";
        $this->viewData['module'] = "report/Coupon_code_report";
        
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

        $this->admin_headerlib->add_plugin("datatables","datatables/fixedColumns.dataTables.min.css");
        $this->admin_headerlib->add_javascript_plugins("datatables","datatables/dataTables.fixedColumns.min.js");

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Coupon_code_report", "pages/coupon_code_report.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
  
    public function getcouponcodedata(){
        $PostData = $this->input->post();
        //print_r($PostData);
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';
        $status = (!empty($PostData['status']))?implode(',',$PostData['status']):'';
        $datetype = $PostData['datetype'];

        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);

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
        $req['COLUMNS'][] = array('title'=>Member_label,"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Total Purchase',"sortable"=>true,"class"=>"text-right");
        
		/* foreach($memberdata as $memberrow){
			$req['COLUMNS'][] = array('title'=>$memberrow['name'],"sortable"=>true);
        } */
        foreach ($Date as $daterow) {
            $req['COLUMNS'][] = array('title'=>$this->general_model->displaydate($daterow,$dateformat),"sortable"=>true,"class"=>"text-right");
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $purchasedata = $this->Purchase_report->getcouponcodedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($purchasedata, 'date');

        
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
                    $searchdatedata = array_intersect_key($purchasedata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalpurchase'],2,'.','');
                        $data[]= $searchdatedata[$searchkeys[0]]['totalpurchase'];
                    }else{
                        $formateddata[] = $data[] = 0;
                    }
                }else{
                    $formateddata[] = $data[] = 0;
                }
            }

			$req['DATA'][] = array_merge(array(++$index,ucwords($memberrow['name']),number_format(array_sum($data),2,'.','')),$data);

        }
		
		echo json_encode($req);
    }

    public function exportpurchasereport(){
        
        $PostData = $this->input->get();
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';
        $status = ($PostData['status']!='')?$PostData['status']:'';
        $datetype = $PostData['datetype'];

        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);

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

        $headings = array_merge(array('Sr.No.',Member_label,'Total Purchase'),$headings);
        
        $memberid = implode(',',array_column($memberdata,'id'));
        $purchasedata = $this->Purchase_report->getcouponcodedata($startdate,$enddate,$channelid,$memberid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($purchasedata, 'date');

        
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
                    $searchdatedata = array_intersect_key($purchasedata, array_flip($keys));

                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                    
                    if(!empty($searchkeys)){
                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalpurchase'],2,'.',',');
                        $data[]= (int)($searchdatedata[$searchkeys[0]]['totalpurchase']);
                    }else{
                        $formateddata[]= '0';
                    }
                }else{
                    $formateddata[]= '0';
                }
                
            }

			$result[] = array_merge(array(++$index,ucwords($memberrow['name']),number_format(array_sum($data),2,'.',',')),$formateddata);

        } 

        $this->general_model->exporttoexcel($result,"A1:DD1","Purchase Report",$headings,"PurchaseReport.xls","C:ZZ");
    }
}