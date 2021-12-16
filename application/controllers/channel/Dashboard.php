<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('mainmenu','Dashboard');
		$this->load->model('Dashboard_model',"Dashboard");
		
	}
	public function index(){
        
        $this->viewData['title'] = "Dashboard";
        $this->viewData['module'] = "Dashboard";
        
        //Count Box

        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        $where = '';
        
        $this->load->model("Member_model","Member");
        $where = "id in(SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.")";
        $this->Member->_where = $where;
        $data = $this->Member->CountRecords();   
        $this->viewData['membercount'] = $data;

        $this->load->model("Product_model","Product");
        $where = "status=1 AND id in(SELECT productid FROM ".tbl_memberproduct." WHERE memberid=".$MEMBERID.")";
        $this->Product->_where = $where;
        $data = $this->Product->CountRecords();
        $this->viewData['productcount'] = $data;  
        
        $this->load->model("Order_model","Order");
        $where = "status=1 AND (DATE(createddate) >= curdate() - interval 1 month) AND (((memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR memberid=0) AND (addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) OR (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid=".$MEMBERID.") AND (memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR sellermemberid=0) AND (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) AND addedby!=0";
        $this->Order->_where = $where;
        $data = $this->Order->CountRecords();
        $this->viewData['ordercompletedcount'] = $data;    
        
        $where = "status=2 AND (DATE(createddate) >= curdate() - interval 1 month) AND (((memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR memberid=0) AND (addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) OR (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid=".$MEMBERID.") AND (memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR sellermemberid=0) AND (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) AND addedby!=0";
        $this->Order->_where = $where;
        $data = $this->Order->CountRecords(); 
        $this->viewData['ordercancelledcount'] = $data; 
        
        
        $this->load->model("Quotation_model","Quotation");
        $where = "(DATE(createddate) >= curdate() - interval 1 month) AND (((memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR memberid=0) AND (addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) OR (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid=".$MEMBERID.") AND (memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR sellermemberid=0) AND (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) AND addedby!=0";
        $this->Quotation->_where = $where;
        $data = $this->Quotation->CountRecords();
        $this->viewData['quotationcount'] = $data;
        
        $where = "(DATE(createddate) >= curdate() - interval 1 month)";
        if(STOCK_CALCULATION==0){
            $where .= " AND o.status!=2 AND o.isdelete=0 AND o.sellermemberid=".$MEMBERID;
        }else{
            $where .= " AND status!=2 AND sellermemberid=".$MEMBERID;
        }
        
        $amountdata = $this->Dashboard->getTotalSales($where);    
        if(!empty($amountdata['totalsales'])){
            $data = $amountdata['totalsales'];
        }else{
            $data = 0;	
        }
        $this->viewData['totalsalescount'] = $data; 

        //echo $data;exit;

        //Chart 
        $this->viewData['saleschartdata'] =  $this->saleschart();
        $this->viewData['orderchartdata'] =  $this->dashboard_process();

        //chartbox data
        $this->viewData['saleschartboxcount'] =  $this->getsaleschartbox();
        $this->viewData['orderschartboxcount'] =  $this->getorderschartbox();

		$this->load->model('Order_model','Order');
		$this->viewData['recentorder'] = $this->Order->recentorder();
		$this->load->model('Quotation_model','Quotation');
		$this->viewData['recentquotation'] = $this->Quotation->recentquotation();
		$this->load->model('Feedback_model','Feedback');
        $this->viewData['feedbackoftheday'] = $this->Feedback->recentfeedback();
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

		$this->channel_headerlib->add_javascript("highcharts", "pages/highcharts.js");
        $this->channel_headerlib->add_javascript("drilldown", "pages/drilldown.js");
		$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->channel_headerlib->add_javascript("Dashboard", "pages/dashboard.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function dashboard_process()
    {
        $PostData = $this->input->post();
        if (isset($PostData['fromdate']) && isset($PostData['todate'])) {
            $startdate=$this->general_model->convertdate($PostData['fromdate']);
            $enddate=$this->general_model->convertdate($PostData['todate']);
            $flag = 0;
        }else{
            $startdate = date("Y-m-d",strtotime("-3 month"));
            $enddate = date("Y-m-d");
            $flag = 1;
        }
			$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
			
            $orderchart = $this->Dashboard->getOrderChart($startdate,$enddate,$MEMBERID);
            $orderchartmonth=array();
            $orderchartdrilldata=array();
            foreach ($orderchart as $oc) {
                if(isset($orderchartmonth[date("m-Y",strtotime($oc['createddate']))])){
                    $orderchartmonth[date("m-Y",strtotime($oc['createddate']))]=$orderchartmonth[date("m-Y",strtotime($oc['createddate']))]+$oc['countorder'];    
                }else{
                    $orderchartmonth[date("m-Y",strtotime($oc['createddate']))]=$oc['countorder'];
                }
                $orderchartmonth[date("m-Y",strtotime($oc['createddate']))."|dates"][] = array($oc['createddate'],(int)$oc['countorder']);
            }

            $orderchartdata=array();
            foreach ($orderchartmonth as $ocmkey=>$ocm) {
                if(count(explode("|",$ocmkey))>1){
                    $orderchartdrilldata[]=array("name"=>explode("|",$ocmkey)[0],"id"=>explode("|",$ocmkey)[0],"data"=>$ocm);
                }else{
                    $orderchartdata[]=array("y"=>(int)$ocm,"name"=>$ocmkey,"drilldown"=>$ocmkey);
                }
            }
            $customerstatuschart =array();
            $customerstatuswise=array();
            foreach ($customerstatuschart as $cd) {
                $customerstatuswise[]=array("y"=>(int)$cd['countcustomer'],"name"=>$cd['customer_status']);
            }
            $chartdata = array("orderchartdrilldata"=>$orderchartdrilldata,"orderchartdata"=>$orderchartdata);
            if($flag==1){
                return json_encode($chartdata);
            }else{
                echo json_encode($chartdata);
            }
        
    }
	public function saleschart()
    {
        $PostData = $this->input->post();
            if (isset($PostData['fromdate']) && isset($PostData['todate'])) {
                $startdate=$this->general_model->convertdate($PostData['fromdate']);
                $enddate=$this->general_model->convertdate($PostData['todate']);
                $flag = 0;
            }else{
                $startdate = date("Y-m-d",strtotime("-3 month"));
                $enddate = date("Y-m-d");
                $flag = 1;
            }
			$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
            $orderchart = $this->Dashboard->getSalesChart($startdate,$enddate,$MEMBERID);
            $orderchartmonth=array();
            $orderchartdrilldata=array();
            foreach ($orderchart as $oc) {
                
                if(isset($orderchartmonth[date("m-Y",strtotime($oc['createddate']))])){
                    $orderchartmonth[date("m-Y",strtotime($oc['createddate']))]=$orderchartmonth[date("m-Y",strtotime($oc['createddate']))]+$oc['totalsales'];    
                }else{
                    $orderchartmonth[date("m-Y",strtotime($oc['createddate']))]=$oc['totalsales'];
                }
                $orderchartmonth[date("m-Y",strtotime($oc['createddate']))."|dates"][] = array($oc['createddate'],(int)$oc['totalsales']);
            }

            $orderchartdata=array();
            foreach ($orderchartmonth as $ocmkey=>$ocm) {
                if(count(explode("|",$ocmkey))>1){
                    $orderchartdrilldata[]=array("name"=>explode("|",$ocmkey)[0],"id"=>explode("|",$ocmkey)[0],"data"=>$ocm);
                }else{
                    $orderchartdata[]=array("y"=>(int)$ocm,"name"=>$ocmkey,"drilldown"=>$ocmkey);
                }
            }
            $chartdata = array("saleschartdrilldata"=>$orderchartdrilldata,"saleschartdata"=>$orderchartdata);
            if($flag==1){
                return json_encode($chartdata);
            }else{
                echo json_encode($chartdata);
            }
        
    }
	public function getcounts()
    {
        $PostData = $this->input->post();
        $where = '0=0';
		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		if(isset($PostData['duration'])){
            if($PostData['duration']=="1"){
                $where .= " AND (DATE(createddate) >= curdate()  - interval 1 month)";
            }else if($PostData['duration']=="2"){
                $where .= " AND (DATE(createddate) >= curdate()  - interval 3 month)";
            }else if($PostData['duration']=="3"){
                $where .= " AND (DATE(createddate) >= curdate()  - interval 6 month)";
            }else if($PostData['duration']=="4"){
                $where .= " AND (DATE(createddate) >= curdate()  - interval 12 month)";
            }else if($PostData['duration']=="6"){
                $where .= " AND (DATE(createddate) >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY)";
            }
        }else{
            $where .= " AND (DATE(createddate) >= curdate()  - interval 1 month)";
        }
        if(isset($PostData['counttype'])){ 
			
			if($PostData['counttype']=="ordercompleted" || $PostData['counttype']=="ordercancelled" || $PostData['counttype']=="quotation"){
                $where .= " AND (((memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR memberid=0) AND (addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) OR (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid=".$MEMBERID.") AND (memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR sellermemberid=0) AND (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) AND addedby!=0";
			}
            if($PostData['counttype']=="member"){
                $this->load->model("Member_model","Member");
                $where .= " AND id in(select submemberid from ".tbl_membermapping." where mainmemberid=".$MEMBERID.")";
                $this->Member->_where = $where;
                $data = $this->Member->CountRecords();
		   
			}else if($PostData['counttype']=="product"){
				$this->load->model("Product_model","Product");
                $where .= " AND status=1 AND id IN (SELECT productid FROM ".tbl_memberproduct." WHERE memberid=".$MEMBERID.")";
				$this->Product->_where = $where;
                $data = $this->Product->CountRecords();    
			
			}else if($PostData['counttype']=="ordercompleted"){
                $this->load->model("Order_model","Order");
                $where .= " AND status=1";
				$this->Order->_where = $where;
				$data = $this->Order->CountRecords();    
				
			}else if($PostData['counttype']=="ordercancelled"){
                $this->load->model("Order_model","Order");
                $where .= " AND status=2";
				$this->Order->_where = $where;
                $data = $this->Order->CountRecords();    
		
			}else if($PostData['counttype']=="quotation"){
				$this->load->model("Quotation_model","Quotation");
				$this->Quotation->_where = $where;
                $data = $this->Quotation->CountRecords();    
		
			}else if($PostData['counttype']=="totalsales"){
                $this->load->model("Order_model","Order");
                
                if(STOCK_CALCULATION==0){
                    $where .= " AND o.status!=2 AND o.isdelete=0 AND o.sellermemberid=".$MEMBERID;
                }else{
                    $where .= " AND status!=2 AND sellermemberid=".$MEMBERID;
                }
				// $where["(o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.sellermemberid = ".$MEMBERID." ) AND (o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.memberid=0) AND (o.addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.addedby=".$MEMBERID.") AND o.addedby!=0"] = null;
				
                $amountdata = $this->Dashboard->getTotalSales($where);    
                if(!empty($amountdata['totalsales'])){
                	$data = $amountdata['totalsales'];
                }else{
                	$data = 0;	
                }
            }
        }
        if(isset($data)){
        	$data = nice_number($data);
            echo $data;
        }
    }

    //Chart Box
    function getsaleschartbox(){
        $PostData = $this->input->post();
        if (isset($PostData['startdate']) && isset($PostData['startdate'])) {
            $startdate = $this->general_model->convertdate($PostData['startdate']);
            $enddate = $this->general_model->convertdate($PostData['enddate']);
            $flag = 0;
        }else{
            $startdate = date("Y-m-d",strtotime("-3 month"));
            $enddate = date("Y-m-d");
            $flag = 1;
        }

            $salesdata = $this->Dashboard->getMembersaleschartbox($startdate,$enddate);
            $data['salescount'] = nice_number($salesdata['salescount']);
            $data['salesaverage'] = nice_number(($salesdata['months']>0?$salesdata['salescount']/$salesdata['months']:$salesdata['salescount']));

            if($flag==1){
                return ($data);
            }else{
                echo json_encode($data);
            }
       

    }
    function getorderschartbox(){
        $PostData = $this->input->post();
        if (isset($PostData['startdate']) && isset($PostData['startdate'])) {
            $startdate = $this->general_model->convertdate($PostData['startdate']);
            $enddate = $this->general_model->convertdate($PostData['enddate']);
            $flag = 0;
        }else{
            $startdate = date("Y-m-d",strtotime("-3 month"));
            $enddate = date("Y-m-d");
            $flag = 1;
        }

        $ordersdata = $this->Dashboard->getMemberorderschartbox($startdate,$enddate);
        $data['orderscount'] = nice_number($ordersdata['orderscount']);
        $data['ordersaverage'] = nice_number($ordersdata['orderscount']/$ordersdata['months']);
     
        if($flag==1){
            return ($data);
        }else{
            echo json_encode($data);
        }

    }
}
