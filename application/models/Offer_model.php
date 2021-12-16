<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offer_model extends Common_model {
	public $_table = tbl_offer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('o.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'channel','o.name','o.type','o.startdate','o.enddate',null,'offerstatus');

	//set column field database for datatable searchable 
	public $column_search = array('o.name','((IFNULL((SELECT name FROM '.tbl_channel.' WHERE id=o.channelid),"All Channel & '.Member_label.'")))','o.description','o.startdate','o.enddate');

	function __construct() {
		parent::__construct();
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
		$status_where = "";
		
		$displayoffertype = isset($_REQUEST['displayoffertype'])?$_REQUEST['displayoffertype']:0;
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$channelid = $this->session->userdata(base_url().'CHANNELID');

		$type = isset($_REQUEST['type'])?$_REQUEST['type']:0;
		$filtermemberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
		$filterchannelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:"";

		if(!is_null($memberid)){
			$status_where = '(SELECT status FROM '.tbl_offerparticipants.' WHERE offerid=o.id AND memberid='.$memberid.') as offerstatus,';
		}else{
			$status_where ='"" as offerstatus';
		}
		$this->readdb->select('o.id,o.name,o.description,o.startdate,o.enddate,o.status,o.type,o.createddate,o.usertype,o.addedby,o.channelid,IFNULL((SELECT name FROM '.tbl_channel.' WHERE id=o.channelid),"All Channel & '.Member_label.'") as channel,'.$status_where);
		$this->readdb->from($this->_table." as o");
		
		if(!is_null($memberid)){
			// $this->readdb->join(tbl_offermembermapping." as omm","omm.offerid=o.id AND omm.memberid=","INNER");
			//$this->readdb->where("((o.channelid=0 AND o.usertype=0) OR o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."') OR (o.usertype=1 AND o.addedby='".$memberid."') OR (o.channelid=0 AND o.usertype=1 AND (o.addedby IN (select sellermemberid from ".tbl_orders." where memberid='".$memberid."') OR o.addedby in(select mainmemberid from ".tbl_membermapping." where submemberid='".$memberid."'))))");
			// $this->readdb->where("o.type=1");
			if($displayoffertype==1){
				$this->readdb->where("o.usertype=1 AND o.addedby='".$memberid."'");
			}else if($displayoffertype==2){
				$this->readdb->where("o.id IN (SELECT offerid FROM ".tbl_offerparticipants." WHERE memberid='".$memberid."' AND status=1)");
			}else if($displayoffertype==3){
				$this->readdb->where("((o.channelid=0 AND o.usertype=0) OR o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."') AND o.id NOT IN (SELECT offerid FROM ".tbl_offerparticipants." WHERE memberid='".$memberid."' AND status=1))");
			}else{
				$this->readdb->where("((o.channelid=0 AND o.usertype=0) OR o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."') OR (o.usertype=1 AND o.addedby='".$memberid."'))");
			}
		}
		if($type!=0){
			$this->readdb->where("o.type='".$type."'");
		}
		if($filterchannelid!=""){
			$this->readdb->where("o.channelid='".$filterchannelid."'");
		}
		if($filtermemberid!=0){
			$this->readdb->where("o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$filtermemberid."')");
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

	function getOfferDetailsByID($id) {
		
		$this->load->model("Offer_purchased_product_model","Offer_purchased_product");
		$this->load->model("Offer_product_model","Offer_product");
		$MEMBERID = (!is_null($this->session->userdata(base_url().'MEMBERID'))?$this->session->userdata(base_url().'MEMBERID'):0);

		$query = $this->readdb->select("o.id,o.name,o.description,o.startdate,o.enddate,o.minbillamount,o.noofcustomerused,o.maximumusage,o.createddate,o.shortdescription,o.type,o.targetvalue,o.rewardvalue,o.rewardtype,o.useractivationrequired,
		o.minimumpurchaseamount,
		status,o.channelid,o.offertype,o.usertype,
										IFNULL((SELECT GROUP_CONCAT(oc.id) FROM ".tbl_offercombination." as oc WHERE oc.offerid=o.id),0) as offercombinationid,
										IFNULL((SELECT GROUP_CONCAT(omm.memberid) FROM ".tbl_offermembermapping." as omm WHERE omm.offerid=o.id),0) as memberid,
										IFNULL((SELECT GROUP_CONCAT(oc.multiplication) FROM ".tbl_offercombination." as oc WHERE oc.offerid=o.id),0) as multiplication,o.addedby,
										IF('".$MEMBERID."'!=0,(SELECT membernotes FROM ".tbl_offerparticipants." WHERE offerid=o.id AND memberid=".$MEMBERID."),'') as membernotes")
								->from($this->_table." as o")
								->where('o.id='.$id)
								->get();
		
		if($query->num_rows() == 1){
			$offerdetail = $query->row_array();
			$offerdata = array();
			if(!empty($offerdetail)){
				$cobinationarr=array();
				if($offerdetail['type']!=1){
					$cobinationdata = $this->readdb->select("oc.id,oc.offerid,oc.multiplication")
												->from(tbl_offercombination." as oc")
												->where('oc.offerid='.$offerdetail['id'])
												->get()->result_array();
					if(!empty($cobinationdata)){
						foreach($cobinationdata as $oc){
							
							$purchaseproductdata = $this->Offer_purchased_product->getPurchaseProduct($offerdetail['id'],$oc['id']);

							$offerproductdata = $this->readdb->select('op.id,pp.productid,op.productvariantid,op.quantity,
									CONCAT((SELECT name FROM '.tbl_product.' WHERE id = pp.productid), " ", 
									IFNULL((SELECT CONCAT("[",GROUP_CONCAT(v.value),"]") 
									FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"")) as productname,
									
									IF(op.discounttype=1,IF(op.discountvalue="100.00","FREE",CONCAT(op.discountvalue,"% off")),CONCAT("<?=CURRENCY_CODE?>",op.discountvalue," off")) as offerdiscountlabel
								')
									->from(tbl_offerproduct." as op")
									->join(tbl_productprices." as pp","pp.id=op.productvariantid","INNER")
									->where("op.offerid='".$offerdetail['id']."' AND op.offercombinationid=".$oc['id'])
									->get()->result_array();

							$cobinationarr[] = array("combinationid"=>$oc['id'],
													"multiplication"=>$oc['multiplication'],
													"purchaseproductdata"=>$purchaseproductdata,
													"offerproductdata"=>$offerproductdata
												);

						}
					}
				}
				$offerdata = array("offerdata"=>$offerdetail,
								"combination"=>$cobinationarr
							);
				
			}
			return $offerdata;
		}else {
			return array();
		}
	}

	function getOfferDataByID($id) {
		
		$query = $this->readdb->select("o.id,o.name,o.description,o.startdate,enddate,status,o.minbillamount,o.maximumusage,o.noofcustomerused,o.channelid,o.type,o.shortdescription,o.useractivationrequired,o.offertype,o.minimumpurchaseamount,
		o.targetvalue,o.rewardvalue,o.rewardtype,
			IF(o.offertype=0,(SELECT brandid FROM ".tbl_product." WHERE id = (SELECT (SELECT productid FROM ".tbl_productprices."  WHERE id = opp.productvariantid) as productid FROM ".tbl_offerpurchasedproduct." as opp WHERE opp.offerid=o.id LIMIT 1)),'') as brandid,
										IFNULL((SELECT GROUP_CONCAT(oc.id) FROM ".tbl_offercombination." as oc WHERE oc.offerid=o.id),0) as offercombinationid,
										IFNULL((SELECT GROUP_CONCAT(omm.memberid) FROM ".tbl_offermembermapping." as omm WHERE omm.offerid=o.id),0) as memberid,
										IFNULL((SELECT GROUP_CONCAT(oc.multiplication) FROM ".tbl_offercombination." as oc WHERE oc.offerid=o.id),0) as multiplication")
								->from($this->_table." as o")
								->where('o.id='.$id)
								->get();
		
		if($query->num_rows() == 1){
			return $query->row_array();
		}else {
			return array();
		}
	}
	function getOfferImageDataByOfferID($offerid) {
	
		$query = $this->readdb->select('oi.id,oi.offerid,oi.filename,oi.priority')
						->from(tbl_offerimage." as oi")
						->where('oi.offerid='.$offerid)
						->get();
		
		if($query->num_rows() > 0){
			return $query->result_array();
		}else {
			return array();
		}
	}
	
	function getOfferImages($offerid) {
		
		$query = $this->readdb->select('oi.id,oi.filename')
						->from(tbl_offerimage." as oi")
						->where('oi.offerid='.$offerid)
						->order_by('oi.priority ASC')
						->get();
		
		if($query->num_rows() > 0){
			return $query->result_array();
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

	function getMemberListByOfferChannelID($offerid,$channelid){
		
		$query = $this->readdb->select('m.id,m.name,m.membercode')
				->from(tbl_member." as m")
				->where('m.channelid = '.$channelid.' AND m.id IN (SELECT memberid FROM '.tbl_offermembermapping.' WHERE offerid='.$offerid.')')
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
		$this->readdb->where("o.type=1 or o.type=4");
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
	public function getofferproducts($memberid,$productid,$productvariantid){
		
		$this->load->model('Offer_purchased_product_model', 'Offer_purchased_product');
        $this->load->model('Offer_product_model', 'Offer_product');
		
        $sellerid = 0;
        $json=array();
        $offerdata = $this->getOfferProductsInOrder($memberid,$productid,$productvariantid);
        //echo $this->readdb->last_query();exit;
        if(!empty($offerdata)){
            foreach($offerdata as $offer){
                $offerimages = $this->getOfferImages($offer['offerid']);
                $noofmemberusedoffer = $this->noOfMemberUsedOffer($offer['offerid']);
                $termscondition = "";
                if($offer['description']!=""){
                    $termscondition = "<a href='javascript:void(0)' data-content='".str_replace("'","",$offer['description'])."' data-original-title='Terms & Conditions' data-toggle='popover' data-placement='bottom'>*Terms & Conditions</a>";
                }
                $combinationarr=array();                
                $combinationdata = $this->getOfferCombinations($offer['offerid'],$productid,$productvariantid);
                if(!empty($combinationdata)){
                    foreach($combinationdata as $combination){

                        $purchaseproduct = $this->Offer_purchased_product->getPurchaseProduct($offer['offerid'],$combination['id']);
                        $offerproduct = $this->Offer_product->getOfferProducts($offer['offerid'],$combination['id'],$memberid,$sellerid);

                        $combinationarr[] = array(
                                                "id"=>$combination['id'],
                                                "multiplication"=>$combination['multiplication'],
                                                "purchaseproduct"=>$purchaseproduct,
                                                "offerproduct"=>$offerproduct,
                                            );
                    }
                }

                $json[] = array('offerid'=>$offer['offerid'],
                                'offername'=>$offer['offername'],
                                'minbillamount'=>$offer['minbillamount'],
                                'minimumpurchaseamount'=>$offer['minimumpurchaseamount'],
                                'maximumusage'=>$offer['maximumusage'],
                                'used'=>$offer['used'],
                                'noofmembersused'=>$offer['noofcustomerused'],
                                'noofmemberusedoffer'=>$noofmemberusedoffer,
                                'offertype'=>$offer['offertype'],
                                'termscondition'=>$termscondition,
                                'offerimage'=>$offer['offerimage'],
                                'offerimages'=>$offerimages,
                                'shortdescription'=>$offer['shortdescription'],
                                'combination'=>$combinationarr
                            );
            }
        }
        return $json;
    }
	function getOfferProductsInOrder($memberid,$productid,$productvariantid){
		
		$this->readdb->select('o.id as offerid, o.name as offername,o.shortdescription,o.description,o.maximumusage,o.noofcustomerused,
						IFNULL((SELECT filename FROM '.tbl_offerimage.' WHERE offerid=o.id LIMIT 1),"") as offerimage,o.minbillamount,o.offertype,o.minimumpurchaseamount,
						IFNULL((SELECT COUNT(offerp.id) FROM '.tbl_offerproduct.' as offerp INNER JOIN '.tbl_orderproducts.' as op ON op.offerproductid=offerp.id INNER JOIN '.tbl_orders.' as orders ON orders.id=op.orderid AND orders.approved=1 AND orders.isdelete=0 WHERE offerp.offerid=o.id),0) as used
					');
					
		$this->readdb->from($this->_table." as o");
		$this->readdb->where("o.status=1 AND (o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."' AND offerid=o.id) OR (o.channelid=0 && o.usertype=0))
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
		$this->readdb->where("op.memberid=".$userid." AND (o.type=1 OR  o.type=4) AND op.status=1");
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
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$this->readdb->select('o.id,o.name,o.type,o.status,o.targetvalue,o.rewardvalue,o.rewardtype');
		$this->readdb->from($this->_table." as o");
		$this->readdb->where("o.type=4 AND o.status=1");
		$this->readdb->order_by("o.id DESC");
		if(!is_null($MEMBERID)){
			$this->readdb->where("(o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$MEMBERID."') OR o.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid='".$MEMBERID."'))) AND IFNULL((SELECT count(id) FROM ".tbl_offerproduct." WHERE offerid=o.id),0)=0");

		}
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