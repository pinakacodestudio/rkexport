<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashback_offer_model extends Common_model {
    
	public $_table = tbl_cashbackoffer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('co.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'channel','co.name','co.startdate','co.enddate');

	//set column field database for datatable searchable 
	public $column_search = array('co.name','((IFNULL((SELECT name FROM '.tbl_channel.' WHERE id=co.channelid),"All Channel & '.Member_label.'")))','co.description','co.startdate','co.enddate');

	function __construct() {
		parent::__construct();
	}

	function getCashbackOfferProductsByOfferID($cashbackofferid){
		
		$query = $this->readdb->select("copm.id,copm.productid,copm.priceid,copm.earnpoints,
				CONCAT(copm.productname,' ',
					IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=copm.priceid),'')) as productname
       			")
							->from(tbl_cashbackofferproductmapping." as copm")
							->where("cashbackofferid='".$cashbackofferid."'")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function  get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			// echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}

	function _get_datatables_query(){
		
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$channelid = $this->session->userdata(base_url().'CHANNELID');

		$filtermemberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
		$filterchannelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:"";

		$this->readdb->select('co.id,co.name,co.description,co.startdate,co.enddate,co.status,co.createddate,co.usertype,co.addedby,co.channelid,IFNULL((SELECT name FROM '.tbl_channel.' WHERE id=co.channelid),"All Channel & '.Member_label.'") as channel');
		$this->readdb->from($this->_table." as co");
		
		if(!is_null($memberid)){
			$this->readdb->where("((co.channelid=0 AND co.usertype=0) OR co.id IN (SELECT cashbackofferid FROM ".tbl_cashbackoffermembermapping." WHERE memberid='".$memberid."') OR (co.usertype=1 AND co.addedby='".$memberid."'))");
		}
		if($filterchannelid!=""){
			$this->readdb->where("co.channelid='".$filterchannelid."'");
		}
		if($filtermemberid!=0){
			$this->readdb->where("co.id IN (SELECT cashbackofferid FROM ".tbl_cashbackoffermembermapping." WHERE memberid='".$filtermemberid."')");
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

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function getCashbackOfferDetailsByID($id) {
		
		$query = $this->readdb->select("co.id,co.name,co.description,co.startdate,co.enddate,co.minbillamount,co.createddate,co.shortdescription,co.status,co.channelid,co.usertype,
					
				IFNULL((SELECT GROUP_CONCAT(comm.memberid) FROM ".tbl_cashbackoffermembermapping." as comm WHERE comm.cashbackofferid=co.id),0) as memberid,
				co.addedby
			")
				->from($this->_table." as co")
				->where('co.id='.$id)
				->get();
		
		if($query->num_rows() == 1){
			return $query->row_array();
		}else {
			return array();
		}
	}

	function getCashbackOfferDataByID($cashbackofferid) {
		
		$query = $this->readdb->select("co.id,co.name,co.description,co.startdate,co.enddate,co.status,co.minbillamount,
					co.channelid,co.shortdescription,
					IFNULL((SELECT GROUP_CONCAT(comm.memberid) FROM ".tbl_cashbackoffermembermapping." as comm WHERE comm.cashbackofferid=co.id),0) as memberid
				")
				
			->from($this->_table." as co")
			->where('co.id='.$cashbackofferid)
			->get();
		
		if($query->num_rows() == 1){
			return $query->row_array();
		}else {
			return array();
		}
	}
	
	function noOfMemberUsedOffer($offerid) {
		
		$query = $this->readdb->select('orders.memberid')
						->from(tbl_offerproduct." as offerp")
						->join(tbl_orderproducts." as op","op.offerproductid=offerp.id","INNER")
						->join(tbl_orders." as orders","orders.id=op.orderid AND orders.approved=1 AND orders.isdelete=0","INNER")
						->where('offerp.offerid='.$offerid)
						->group_by('orders.memberid')
						->get();
		return $query->num_rows();
		
	}
	function getOfferCombinations($offerid,$productid,$productvariantid) {
		
		$where = "";
		if($productvariantid!=""){
			$where = " AND IFNULL((SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE FIND_IN_SET('".$productvariantid."',productvariantid) > 0 AND offerid = oc.offerid AND offercombinationid=oc.id LIMIT 1),0) = 1";
		}else{
			$where = " AND (SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE (SELECT productid FROM ".tbl_productprices." WHERE id IN (productvariantid)) = '".$productid."' AND offerid = oc.offerid AND offercombinationid=oc.id LIMIT 1)=1";
			//(SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE FIND_IN_SET(productvariantid,(SELECT GROUP_CONCAT(id) FROM ".tbl_productprices." WHERE productid=".$productid.")) > 0 AND offerid = oc.offerid AND offercombinationid=oc.id LIMIT 1) = 1
		}
		$query = $this->readdb->select('oc.id,oc.offerid,oc.multiplication,(SELECT brandid FROM '.tbl_product.' WHERE id IN (SELECT productid FROM '.tbl_productprices.' WHERE id = (SELECT productvariantid FROM '.tbl_offerpurchasedproduct.' WHERE offerid=oc.offerid AND offercombinationid=oc.id LIMIT 1))) as brandid,(SELECT minimumpurchaseamount FROM '.tbl_offer.' WHERE id = oc.offerid) as minpurchaseamount')
						->from(tbl_offercombination." as oc")
						->where('oc.offerid='.$offerid.$where)
						->get();
		
		if($query->num_rows() > 0){
			return $query->result_array();
		}else {
			return array();
		}
	}
	

	function getOfferProductByType($offerid,$offercombinationid,$type) {
	
		$query = $this->readdb->select('op.id,pp.productid,op.productvariantid,op.quantity,op.discounttype,op.discountvalue')
								->from(tbl_offerproduct." as op")
                                ->join(tbl_productprices." as pp","pp.id=op.productvariantid","INNER")
                                ->where("op.offerid='".$offerid."' AND op.offercombinationid=".$offercombinationid." AND op.type=".$type)
								->get();
		
		return $query->result_array();
	}

	function getMemberListByOfferChannelID($cashbackofferid,$channelid){
		
		$query = $this->readdb->select('m.id,m.name,m.membercode')
				->from(tbl_member." as m")
				->where('m.channelid = '.$channelid.' AND m.id IN (SELECT memberid FROM '.tbl_cashbackoffermembermapping.' WHERE cashbackofferid='.$cashbackofferid.')')
				->get();

		if($query->num_rows() > 0){
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getOffer($userid,$counter){
		$limit=10;
		
		$this->readdb->select('o.id as offerid,o.name as offername,o.shortdescription,o.description as offerdescription,o.useractivationrequired as displayactivationbtn,
								IFNULL((SELECT op.status FROM '.tbl_offerparticipants.' as op WHERE op.memberid='.$userid.' AND op.offerid=o.id),0) as currentstatus,
								IFNULL((SELECT op.membernotes FROM '.tbl_offerparticipants.' as op WHERE op.memberid='.$userid.' AND op.offerid=o.id),"") as membernotes');
		$this->readdb->from($this->_table." as o");
		$this->readdb->where("((o.channelid=0 AND o.usertype=0) OR o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$userid."'))");
		//$this->readdb->where("IF(".$offermodule."=0,0=1,((o.memberid=0 AND o.usertype=0) OR FIND_IN_SET(".$userid.",o.memberid)>0 OR (o.memberid=0 AND o.usertype=1 AND (o.addedby IN (select sellermemberid from ".tbl_orders." where memberid=".$userid.") OR o.addedby in(select mainmemberid from ".tbl_membermapping." where submemberid=".$userid.")))) AND o.type = 1 AND o.id NOT IN (SELECT offerid FROM ".tbl_offerparticipants." WHERE memberid=".$userid."))")
		// $this->readdb->where("IF(".$offermodule."=0,0=1,((o.memberid=0 AND o.usertype=0) OR FIND_IN_SET(".$userid.",o.memberid)>0 OR (o.memberid=0 AND o.usertype=1 AND (o.addedby IN (select sellermemberid from ".tbl_orders." where memberid=".$userid.") OR o.addedby in(select mainmemberid from ".tbl_membermapping." where submemberid=".$userid.")))) AND o.type = 1)");
		$this->readdb->where("o.type=1");
		$this->readdb->where("((o.channelid=0 AND o.usertype=0) OR o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$userid."') OR (o.usertype=1 AND o.addedby in(select mainmemberid from ".tbl_membermapping." where submemberid=".$userid.")))");
		$this->readdb->order_by("o.id DESC");
		if($counter != -1){
			$this->readdb->limit($limit,$counter);
		}   
		$query = $this->readdb->get();
		$offerdata = $query->result_array();

		$data=array();
		if(!empty($offerdata)) {
			foreach($offerdata as $offer){

				$offerimagedata['images'] = $this->readdb->select('oi.id,oi.filename')
						->from(tbl_offerimage." as oi")
						->where('oi.offerid='.$offer['offerid'])
						->order_by("oi.priority ASC")
						->get()->result_array();
		
				$data[] = array_merge($offer, $offerimagedata);
			}
		}
		return $data;
	}
	function getOfferProductsInOrder($memberid,$productid,$productvariantid){
		
		$this->readdb->select('o.id as offerid, o.name as offername,o.shortdescription,o.description,o.maximumusage,o.noofcustomerused,
						IFNULL((SELECT filename FROM '.tbl_offerimage.' WHERE offerid=o.id LIMIT 1),"") as offerimage,o.minbillamount,o.offertype,o.minimumpurchaseamount,
						IFNULL((SELECT COUNT(offerp.id) FROM '.tbl_offerproduct.' as offerp INNER JOIN '.tbl_orderproducts.' as op ON op.offerproductid=offerp.id INNER JOIN '.tbl_orders.' as orders ON orders.id=op.orderid AND orders.approved=1 AND orders.isdelete=0 WHERE offerp.offerid=o.id),0) as used
					');
					
		$this->readdb->from($this->_table." as o");
		$this->readdb->where("o.status=1 AND o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."' AND offerid=o.id)
						AND type != 1
						AND ((CURRENT_DATE() BETWEEN o.startdate AND o.enddate) OR o.startdate='0000-00-00' OR o.enddate='0000-00-00') AND 

						(SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE (SELECT productid FROM ".tbl_productprices." WHERE id IN (productvariantid)) = '".$productid."' AND offerid = o.id LIMIT 1)=1");
		
		if($productvariantid!=""){
			$this->readdb->where("(SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE FIND_IN_SET('".$productvariantid."',productvariantid) > 0 AND offerid = o.id LIMIT 1)= 1");
		}
		$this->readdb->order_by("o.id DESC");
		$query = $this->readdb->get();

		//(SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE FIND_IN_SET(productvariantid,(SELECT GROUP_CONCAT(id) FROM ".tbl_productprices." WHERE productid=".$productid.")) > 0 AND offerid = o.id LIMIT 1) = 1
		// echo $this->readdb->last_query(); exit;
		if($query->num_rows() > 0){
			return $query->result_array();
		}else {
			return array();
		}
	}
	function verifyoffer($offerpurchasedproductid){
		$this->readdb->select('opp.offerid,opp.quantity,(SELECT minbillamount FROM '.tbl_offer.' WHERE id=opp.offerid) as minbillamount');
		$this->readdb->from(tbl_offerpurchasedproduct." as opp");
		$this->readdb->where("opp.id=".$offerpurchasedproductid);
		$query = $this->readdb->get();
		
		if($query->num_rows() == 1){
			return $query->row_array();
		}else {
			return array();
		}
	}
	function getOfferDataByProductorVariant($memberid,$productid,$productvariantid)
	{
		$json=array();
		$this->load->model('Offer_purchased_product_model', 'Offer_purchased_product');
		$this->load->model('Offer_product_model', 'Offer_product');
		$this->load->model('Channel_model', 'Channel');

        $channeldata = $this->Channel->getMemberChannelData($memberid);
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		
        $offerdata = $this->getOfferProductsInOrder($memberid,$productid,$productvariantid);
        if(!empty($offerdata)){
            foreach($offerdata as $offer){
                $termscondition = "";
                if($offer['description']!=""){
					//$termscondition = "<a href='javascript:void(0)' data-content='".str_replace("'","",$offer['description'])."' data-original-title='Terms & Conditions' data-toggle='popover' data-placement='bottom'>*Terms & Conditions</a>";
					
					$termscondition = $offer['description'];
				}
				$noofmemberusedoffer = $this->Offer->noOfMemberUsedOffer($offer['offerid']);
				$offerimages = $this->Offer->getOfferImages($offer['offerid']);

                $combinationarr=array();                
                $combinationdata = $this->Offer->getOfferCombinations($offer['offerid'],$productid,$productvariantid);
                if(!empty($combinationdata)){
                    foreach($combinationdata as $combination){

                        $purchaseproduct = $this->Offer_purchased_product->getPurchaseProduct($offer['offerid'],$combination['id']);
                        $offerproduct = $this->Offer_product->getOfferProducts($offer['offerid'],$combination['id'],$memberid,$currentsellerid);

                        $combinationarr[] = array(
                                                "id"=>$combination['id'],
												"multiplication"=>$combination['multiplication'],
												"brandid"=>$combination['brandid'],
												"minpurchaseamount"=>$combination['minpurchaseamount'],
                                                "purchaseproduct"=>$purchaseproduct,
                                                "offerproduct"=>$offerproduct,
                                            );
                    }
                }

                $json[] = array('offerid'=>$offer['offerid'],
								'offername'=>$offer['offername'],
								'minbillamount'=>$offer['minbillamount'],
								'maximumusage'=>$offer['maximumusage'],
                                'used'=>$offer['used'],
                                'noofmembersused'=>$offer['noofcustomerused'],
                                'noofmemberusedoffer'=>$noofmemberusedoffer,
								'offertype'=>$offer['offertype'],
                                'termscondition'=>$termscondition,
                                'offerimage'=>$offerimages,
                                'shortdescription'=>$offer['shortdescription'],
                                'combination'=>$combinationarr
                            ); 
            }
		}
		
		return $json;
	}
	function getMyOffer($userid,$counter){
		$limit=10;
		
		$this->readdb->select('o.id as offerid,o.name as offername,o.shortdescription,o.description as offerdescription,o.useractivationrequired as displayactivationbtn,
								op.status as currentstatus,op.membernotes');
		$this->readdb->from($this->_table." as o");
		$this->readdb->join(tbl_offerparticipants." as op","op.offerid=o.id AND op.status=1","INNER");
		$this->readdb->where("op.memberid=".$userid." AND o.type=1 AND op.status=1");
		$this->readdb->order_by("o.id DESC");
		if($counter != -1){
			$this->readdb->limit($limit,$counter);
		}   
		$query = $this->readdb->get();
		$offerdata = $query->result_array();

		$data=array();
		if(!empty($offerdata)) {
			foreach($offerdata as $offer){

				$offerimagedata['images'] = $this->readdb->select('oi.id,oi.filename')
						->from(tbl_offerimage." as oi")
						->where('oi.offerid='.$offer['offerid'])
						->order_by("oi.priority ASC")
						->get()->result_array();
		
				$data[] = array_merge($offer, $offerimagedata);
			}
		}
		return $data;
	}

	function getTargetOffer(){
		$this->readdb->select('o.id,o.name,o.type,o.status,o.targetvalue,o.rewardvalue,o.rewardtype');
		$this->readdb->from($this->_table." as o");
		$this->readdb->where("o.type=4 AND o.status=1");
		$this->readdb->order_by("o.id DESC");

		$query = $this->readdb->get();
		return $query->result_array();
	}

	function getAllOfferData($channelid=0,$memberid=0){
		$this->readdb->select('o.id,o.name,o.type,o.status');
		$this->readdb->from($this->_table." as o");
		$this->readdb->where("o.status=1");
		$this->readdb->order_by("o.id DESC");

		$query = $this->readdb->get();
		return $query->result_array();
	}

	function getTargetOfferDataByMemberid($memberid){
		$this->readdb->select('o.id,o.name,o.type,o.status,o.targetvalue,o.rewardvalue,o.rewardtype');
		$this->readdb->from($this->_table." as o");
		$this->readdb->where("o.type=4 AND o.status=1 AND (o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."') OR o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid='".$memberid."'))) AND IFNULL((SELECT count(id) FROM ".tbl_offerproduct." WHERE offerid=o.id),0)=0");
		$this->readdb->order_by("o.id DESC");

		$query = $this->readdb->get();
		return $query->result_array();
	}
	
}