<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Target_offer_model extends Common_model {
	
	public $_table = tbl_offer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('o.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'o.name','membername','sellername','o.targetvalue','targetstatus','o.startdate','offerstatus','o.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('o.name','(IFNULL((SELECT name FROM '.tbl_member.' WHERE id=omm.memberid),""))','(IFNULL((SELECT membercode FROM '.tbl_member.' WHERE id=omm.memberid),""))',"(IFNULL((SELECT CONCAT(name,' (',membercode,')') FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid)),''))",'o.targetvalue','o.createddate');

	function __construct() {
		parent::__construct();
	}

	function getMemberRewardPoint($offerid,$memberid){

		$query = $this->readdb->select("point,rate")
					->from(tbl_rewardpointhistory." as rph")
					->where("rph.id IN (SELECT redeemrewardpointhistoryid FROM ".tbl_offerparticipants." WHERE offerid=".$offerid." AND memberid=".$memberid." AND status=1)")
					->get();

		if($query->num_rows() == 1){
			return $query->row_array();
		}else {
			return array();
		}
	}
	function getAssignGiftProductByOfferId($offerid,$memberid){
	
		$query = $this->readdb->select('agp.id,agp.offerid,agp.productvariantid,
		pp.productid,
		CONCAT((SELECT name FROM '.tbl_product.' WHERE id=pp.productid)," ",IFNULL(
			(SELECT CONCAT("[",GROUP_CONCAT(v.value),"]") 
			FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"")) as productname,
		agp.quantity,
		IFNULL((SELECT filename from '.tbl_productimage.' WHERE productid=pp.productid LIMIT 1),"'.PRODUCTDEFAULTIMAGE.'") as image

		')
					->from(tbl_assigngiftproduct." as agp")
					->join(tbl_productprices." as pp","pp.id=agp.productvariantid","INNER")
					->where('agp.offerid="'.$offerid.'" AND agp.memberid="'.$memberid.'"')
					->get();
		
		if($query->num_rows() > 0){
			return $query->result_array();
		}else {
			return array();
		}
	}

	function getGiftProductByOfferId($offerid,$memberid) {
	
		$query = $this->readdb->select('op.id,op.offerid,pp.productid,op.productvariantid,
		CONCAT((SELECT name FROM '.tbl_product.' WHERE id=pp.productid)," ",IFNULL(
			(SELECT CONCAT("[",GROUP_CONCAT(v.value),"]") 
			FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"")) as productname,

			IFNULL(agp.id,"") as assigngiftproductid,
			
			(op.quantity-IFNULL(agp.quantity,0)) as quantity,

			IFNULL((SELECT filename from '.tbl_productimage.' WHERE productid=pp.productid LIMIT 1),"'.PRODUCTDEFAULTIMAGE.'") as image

		')
					->from(tbl_offerproduct." as op")
					->join(tbl_productprices." as pp","pp.id=op.productvariantid","INNER")
					->join(tbl_assigngiftproduct." as agp","agp.offerid=op.offerid AND agp.memberid='".$memberid."' AND agp.productvariantid=op.productvariantid","LEFT")
					->where('op.offerid="'.$offerid.'" AND IF(op.quantity-IFNULL(agp.quantity,0)>0,1,0)=1')
					->get();
		
		if($query->num_rows() > 0){
			return $query->result_array();
		}else {
			return array();
		}
	
	}
	function getTargetOfferDataInAPI($memberid,$fromdate,$todate,$receiverchannelid,$receivermemberid,$offerid,$counter){

		$limit=10;
		$startdate = $this->general_model->convertdate($fromdate);
		$enddate = $this->general_model->convertdate($todate);

		$this->readdb->select("o.id,o.name,o.description,o.startdate,o.enddate,o.status,o.type,
			o.createddate,o.usertype,o.addedby,o.channelid,(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid) as sellermemberid,
			IFNULL(omm.memberid,0) as memberid,
			IFNULL((SELECT name FROM ".tbl_member." WHERE id=omm.memberid),'') as membername,
			IFNULL((SELECT membercode FROM ".tbl_member." WHERE id=omm.memberid),'') as membercode,

			IFNULL((SELECT channelid FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid)),'') as sellerchannelid,
			IFNULL((SELECT name FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid)),'') as sellername,
			IFNULL((SELECT membercode FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid)),'') as sellercode,

			o.targetvalue,o.rewardvalue,o.rewardtype,
		
			IFNULL((SELECT SUM(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0)) FROM ".tbl_invoice." as i WHERE i.status=1 AND i.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid) AND i.memberid=omm.memberid AND i.invoicedate >= date(op.createddate) AND i.invoicedate <= IF(o.enddate!='0000-00-00',o.enddate,CURDATE())),0) as targetstatus,
		
			@totalofferqty:=IFNULL((SELECT SUM(quantity) FROM ".tbl_offerproduct." WHERE offerid = o.id),0) as totalofferqty,
			@totalassignqty:=IFNULL((SELECT SUM(quantity) FROM ".tbl_assigngiftproduct." WHERE offerid = o.id AND memberid=omm.memberid),0) as totalassignqty,

			IFNULL((SELECT count(id) FROM ".tbl_offerproduct." WHERE offerid = o.id),0) as countgiftproduct,

			IF(IFNULL((SELECT count(id) FROM ".tbl_creditnote." WHERE sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid) AND buyermemberid=omm.memberid AND offerid=o.id AND creditnotetype=1),0)>0,1,0) as iscn,

			CASE 
				WHEN (@totalofferqty=@totalassignqty AND @totalofferqty>0) THEN 'Completed'
				WHEN (@totalassignqty < @totalofferqty AND @totalassignqty>0) THEN 'Partially Completed'
				ELSE 'Pending'
			END as offerstatus
		");

		$this->readdb->from($this->_table." as o");
		$this->readdb->join(tbl_offerparticipants." as op","op.offerid=o.id AND op.status=1","INNER");
		$this->readdb->join(tbl_offermembermapping." as omm","omm.offerid=o.id","LEFT");
		
		$this->readdb->where("o.type = 4 AND o.status = 1");
		$this->readdb->where("(date(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."')");
		
		$this->readdb->where("((o.channelid=0 AND o.usertype=0) OR omm.memberid='".$memberid."' OR omm.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid='".$memberid."'))");
		
		if($offerid!=0){
			$this->readdb->where("o.id='".$offerid."'");
		}
		if($receiverchannelid!=0){
			$this->readdb->where("o.channelid='".$receiverchannelid."'");
		}
		if($receivermemberid!=0){
			$this->readdb->where("omm.memberid='".$receivermemberid."'");
		}
		$this->readdb->order_by("o.id DESC");
		if($counter != -1){
			$this->readdb->limit($limit,$counter);
		} 
		$query = $this->readdb->get();

		return $query->result_array();
	}
	function _get_datatables_query(){
		
		$filtermemberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
		$filterchannelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
		$offerid = isset($_REQUEST['offerid'])?$_REQUEST['offerid']:0;
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$sellermemberid = (!is_null($memberid)?$memberid:0);

		$this->readdb->select("o.id,o.name,o.description,o.startdate,o.enddate,o.status,o.type,
			o.createddate,o.usertype,o.addedby,o.channelid,(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid) as sellermemberid,
			IFNULL(omm.memberid,0) as memberid,
			IFNULL((SELECT name FROM ".tbl_member." WHERE id=omm.memberid),'') as membername,
			IFNULL((SELECT membercode FROM ".tbl_member." WHERE id=omm.memberid),'') as membercode,

			IFNULL((SELECT channelid FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid)),'') as sellerchannelid,
			IFNULL((SELECT name FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid)),'') as sellername,
			IFNULL((SELECT membercode FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid)),'') as sellercode,

			o.targetvalue,o.rewardvalue,o.rewardtype,
		
			IFNULL((SELECT SUM(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0)) FROM ".tbl_invoice." as i WHERE i.status=1 AND i.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid) AND i.memberid=omm.memberid AND i.invoicedate >= date(op.createddate) AND i.invoicedate <= IF(o.enddate!='0000-00-00',o.enddate,CURDATE())),0) as targetstatus,
		
			@totalofferqty:=IFNULL((SELECT SUM(quantity) FROM ".tbl_offerproduct." WHERE offerid = o.id),0) as totalofferqty,
			@totalassignqty:=IFNULL((SELECT SUM(quantity) FROM ".tbl_assigngiftproduct." WHERE offerid = o.id AND memberid=omm.memberid),0) as totalassignqty,

			IFNULL((SELECT count(id) FROM ".tbl_offerproduct." WHERE offerid = o.id),0) as countgiftproduct,

			IF(IFNULL((SELECT count(id) FROM ".tbl_creditnote." WHERE sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid) AND buyermemberid=omm.memberid AND offerid=o.id AND creditnotetype=1),0)>0,1,0) as iscn,

			CASE 
				WHEN (@totalofferqty=@totalassignqty AND @totalofferqty>0) THEN 'Completed'
				WHEN (@totalassignqty < @totalofferqty AND @totalassignqty>0) THEN 'Partially Completed'
				ELSE 'Pending'
			END as offerstatus
		");

		$this->readdb->from($this->_table." as o");
		$this->readdb->join(tbl_offerparticipants." as op","op.offerid=o.id AND op.status=1","INNER");
		$this->readdb->join(tbl_offermembermapping." as omm","omm.offerid=o.id","LEFT");
		
		$this->readdb->where("o.type = 4 AND o.status = 1");
		$this->readdb->where("(date(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."')");
		
		//$this->readdb->where("(IF(IFNULL((SELECT SUM(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0)) FROM ".tbl_invoice." as i WHERE i.status=1 AND i.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=omm.memberid) AND i.memberid=omm.memberid AND i.invoicedate >= date(op.createddate) AND i.invoicedate <= IF(o.enddate!='0000-00-00',o.enddate,CURDATE())),0)>=o.targetvalue,1,0)=1)");

		if(!is_null($memberid)){
			$this->readdb->where("((o.channelid=0 AND o.usertype=0) OR omm.memberid='".$memberid."' OR omm.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid='".$memberid."'))");
		}
		if($offerid!=0){
			$this->readdb->where("o.id='".$offerid."'");
		}
		if($filterchannelid!=0){
			$this->readdb->where("o.channelid='".$filterchannelid."'");
		}
		if($filtermemberid!=0){
			$this->readdb->where("omm.memberid='".$filtermemberid."'");
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
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
			$order = $this->_order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			// echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}
}