<?php

class Advance_payment_model extends Common_model {
//put your code here
	public $_table = tbl_orders;
	public $_fields = "*";
	public $_where = array();
    public $_except_fields = array();
    public $_order = 'id DESC';
    public $_detatableorder = array('createddate'=>'DESC');
	public $column_order = array(null,); //set column field database for datatable orderable
	public $column_search = array(); //set column field database for datatable searchable 
	//public $order = array('m.name' => 'ASC');

	function __construct() {
		parent::__construct();
    }

    function getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status){
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        // $channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:'';
        // $memberid = (isset($_REQUEST['memberid']))?$_REQUEST['memberid']:'';
        
        
        if($datetype==1){
            $this->readdb->select("DATE(o.orderdate) as date");
        }else{
            $this->readdb->select("DATE_FORMAT(o.orderdate,'%Y-%m') as date");
        }

        $this->readdb->select("o.memberid,o.sellermemberid,SUM(o.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as advancepayment");
        $this->readdb->from($this->_table." as o");

        $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid");

        
        $this->readdb->where("o.paymenttype=3");

        if($datetype==1){
            $this->readdb->where("(DATE(o.orderdate) BETWEEN '".$startdate."' AND '".$enddate."')");
        }else{
            $this->readdb->where("(DATE(o.orderdate) BETWEEN '".$startdate."' AND '".$enddate."')");
        }

        if($status!=''){
            $this->readdb->where("(FIND_IN_SET(o.status,'".$status."')>0)");
        }

        if($channelid!='0'){
            $this->readdb->where("buyer.channelid=".$channelid); //Filter seller channel
            if(!empty($memberid)){
                $this->readdb->where("buyer.id IN (".$memberid.")"); //Filter seller member
            }
        }
        
        if(!is_null($MEMBERID)){
            $this->readdb->where("o.sellermemberid='".$MEMBERID."'");
        }else{
            $this->readdb->where("o.sellermemberid=0");    
        }

        if($datetype==1){
            $this->readdb->group_by("DATE(o.orderdate)");
        }else{
            $this->readdb->group_by("DATE_FORMAT(o.orderdate,'%Y-%m')");
            
        }

        $this->readdb->group_by("o.memberid");
        $query = $this->readdb->get();
        //print_r($query);exit;
        //echo $this->readdb->last_query(); exit;
        return $query->result_array();
    }
}
