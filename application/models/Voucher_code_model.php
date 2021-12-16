<?php

class Voucher_code_model extends Common_model {

	//put your code here
	public $_table = tbl_voucher;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'v.name','membername','v.vouchercode','v.discountvalue','v.minbillamount','v.status','v.startdate','v.createddate'); //set column field database for datatable orderable
	public $column_search = array('v.vouchercode','v.discountvalue','v.name','v.startdate','v.createddate'); //set column field database for datatable searchable 
	public $order = array('v.id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}
	function checkCoupon($couponcode){
		$arrSessionDetails = $this->session->userdata;
		$query = $this->readdb->select("vc.id as vouchercodeid,vc.vouchercode,v.isuniversal,v.maximumusage,v.noofcustomerused,v.discounttype,v.discountvalue,'2' as vouchercodetype,
									(SELECT COUNT(vcu.id) FROM ".tbl_vouchercodeused." as vcu WHERE vcu.vouchercodeid=vc.id) as totalused,
									(SELECT COUNT(vcu.id) FROM ".tbl_vouchercodeused." as vcu WHERE vcu.vouchercodeid=vc.id AND customerid=".$arrSessionDetails[base_url().'MEMBER_ID'].") as totalusedbycurrentuser")
							->from(tbl_vouchercode." as vc")
							->join($this->_table." as v","v.id=vc.voucherid AND (v.expireddate>=DATE(NOW()) OR v.expireddate='0000-00-00')","INNER")
							->where("vc.vouchercode='".$couponcode."' AND vc.status=1")
							->get();

		return $query->row_array();
		
	}

	//LISTING DATA
	function _get_datatables_query(){

		$channelid = (!empty($_REQUEST['channelid']))?$_REQUEST['channelid']:'';
		$memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:'';
		$usertype = isset($_REQUEST['usertype'])?$_REQUEST['usertype']:0;
		
		$this->readdb->select("v.id,v.channelid,v.vouchercode,v.discountvalue,v.discounttype,v.status,(select count(id) from ".tbl_vouchercodeused." where voucherid=v.id) as usestatus,v.name,v.startdate,v.enddate,minbillamount,v.createddate,(IF(v.memberid!=0,(SELECT name FROM ".tbl_member." WHERE id=v.memberid),'-')) as membername,type,addedby");
		$this->readdb->from($this->_table." as v");

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		$where = '';
		if($usertype==1){

			if(!is_null($MEMBERID)){
				$where .= "(memberid = ".$MEMBERID." OR addedby=".$MEMBERID." OR (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.") AND status=1) OR (FIND_IN_SET('".$channelid."', channelid)>0 AND (memberid=".$memberid." OR memberid=0) AND status=1) )";

				if(!empty($channelid)){ 
					$where .= " AND (FIND_IN_SET('".$channelid."', channelid)>0)";
				}
				if($memberid!=0){ 
					$where .= " AND memberid = ".$memberid;
				}
				$this->readdb->where($where);
			}else{
				if(!empty($channelid)){ 
					$findchannel=array();
					foreach($channelid as $channelid){
						$findchannel[] = "(FIND_IN_SET('".$channelid."', channelid)>0)";
					}
					if(!empty($memberid)){
						$where .= " AND (FIND_IN_SET(memberid, '".implode(",", $memberid)."')>0)";
					}
					$where =  "(".implode(" OR ", $findchannel).")".$where;
					$this->readdb->where($where);
				}
			}
		}else{

			$this->readdb->where("(FIND_IN_SET('".$channelid."', v.channelid)>0 AND (v.memberid=".$memberid." OR v.memberid=0))");
			
			/* if($memberid!=''){
				if(is_array($memberid)){
					$memberid = implode(",",$memberid);
					$this->readdb->where("memberid IN(".$memberid.")");
	
					if(is_array($channelid)){
						$channelid = implode(",",$channelid);
						$this->readdb->where("v.channelid IN (".$channelid.")");
					}
				}else{
					$this->readdb->where("(channelid IN (SELECT channelid FROM ".tbl_member." WHERE id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.")))");
				}
			}else{
				if(!is_null($MEMBERID)) {
					$this->readdb->where("(channelid IN (SELECT channelid FROM ".tbl_member." WHERE id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.")))");
					
				}
				$where = '';
				if($channelid != 0){
					if(is_array($channelid)){
						$channelid = implode(",",$channelid);
					}
					$this->readdb->where("v.channelid IN (".$channelid.")");
				}
			} */
		}
		
		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->readdb->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->readdb->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		//$this->readdb->get();
		return $this->readdb->count_all_results();
	}
}
