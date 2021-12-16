<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends Admin_Controller {

	public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('mainmenu','Dashboard');
        $this->load->model('Dashboard_model',"Dashboard");
		
	}
	public function index(){

        $this->viewData['size'] = $this->Dashboard->folderSize("uploaded/".CLIENT_FOLDER);
        //echo $size;exit;

        if($this->viewData['mainmenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Dashboard','View Dashboard');
        }
        // $this->checkAdminAccessModule('mainmenu','view',$this->viewData['mainmenuvisibility']);
		$this->viewData['title'] = "Dashboard";
        $this->viewData['module'] = "Dashboard";

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
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
            
            $orderchart = $this->Dashboard->getOrderChart($startdate,$enddate);
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
            //$where = array("(DATE(createddate) BETWEEN '".$startdate."' AND '".$enddate."')"=>null,"status!=" => 2);
            $orderchart = $this->Dashboard->getSalesChart($startdate,$enddate);
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
            //print_r($orderchartmonth);exit;
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
            /* if($PostData['counttype']=="customer"){
                $this->load->model("Customer_model","Customer");
                $this->Customer->_where = $where;
                $data = $this->Customer->CountRecords();    
            }else  */if($PostData['counttype']=="member"){
                $this->load->model("Member_model","Member");
                // $where['reportingto!=']=0;
                $this->Member->_where = $where;
                $data = $this->Member->CountRecords();    
            }else if($PostData['counttype']=="product"){
                $this->load->model("Product_model","Product");
                $this->Product->_where = $where;
                $data = $this->Product->CountRecords();    
            }else if($PostData['counttype']=="ordercompleted"){
                $this->load->model("Order_model","Order");
                $where .= (!empty($where))?" AND status=1":"status=1";
                if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                    $where .= " AND ((addedby=".$this->session->userdata(base_url().'ADMINID')." AND type=0) or salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
                }
                $this->Order->_where = $where;
                $data = $this->Order->CountRecords();    
            }else if($PostData['counttype']=="ordercancelled"){
                $this->load->model("Order_model","Order");
                $where .= (!empty($where))?" AND status=2":"status=2";
                if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                    $where .= " AND ((addedby=".$this->session->userdata(base_url().'ADMINID')." AND type=0) or salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
                }
                $this->Order->_where = $where;
                $data = $this->Order->CountRecords();    
            }else if($PostData['counttype']=="quotation"){
                $this->load->model("Quotation_model","Quotation");
                if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                    $where .= " AND ((addedby=".$this->session->userdata(base_url().'ADMINID')." AND type=0) or salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
                }
                $this->Quotation->_where = $where;
                $data = $this->Quotation->CountRecords();    
            }else if($PostData['counttype']=="totalsales"){
                $this->load->model("Order_model","Order");
                //$where["o.status!="] = 2;
                
                if(STOCK_CALCULATION==0){
                    $where .= " AND o.status!=2 AND o.isdelete=0 AND o.sellermemberid=0";
                }else{
                    $where .= " AND status!=2 AND sellermemberid=0";
                }
                
                //$where["o.sellermemberid="] = 0;
                /* $this->Order->_where = $where;
                $this->Order->_fields = "SUM(amount)as amountsum,SUM(taxamount)as taxsum"; */
                //print_r($where);exit;
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

            $salesdata = $this->Dashboard->getsaleschartbox($startdate,$enddate,0);
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

        $ordersdata = $this->Dashboard->getorderschartbox($startdate,$enddate);
        
        $data['orderscount'] = nice_number($ordersdata['orderscount']);
        $data['ordersaverage'] = nice_number($ordersdata['orderscount']/$ordersdata['months']);
     
        if($flag==1){
            return ($data);
        }else{
            echo json_encode($data);
        }

    }
}
