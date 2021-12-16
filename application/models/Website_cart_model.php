<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Website_cart_model extends Common_model {

	public $_table = tbl_cart;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('c.id'=> 'DESC');
	
	//set column field database for datatable orderable
	public $column_order = array(null, 'customername','productname','variantprice','tax','quantity',null,'createddate');
	//set column field database for datatable searchable 
	public $column_search = array('name');
	//set column field database for datatable orderable
	//public $column_ordercustomercart = array(null,'name',"price",'tax','quantity','name','c.createddate');
	public $column_searchcustomercart = array('name',"IF(price>0,price,(select price from ".tbl_productprices." where id=c.priceid))",'tax','quantity','name','c.createddate');

	function __construct() {
		parent::__construct();
	}

	
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			
			return $query->result();
		}
	}

	function _get_datatables_query(){
		
		$channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
		$productid = $_REQUEST['productid'];
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		
		
		$this->readdb->select("c.id,
						buyer.id as buyerid,
						buyer.name as buyername,
						buyer.membercode as buyercode,
						buyer.channelid as buyerchannelid,
						seller.id as sellerid,
						seller.channelid as sellerchannelid,
						seller.name as sellername,
						seller.membercode as sellercode,
						p.isuniversal,
						p.id as productid,
						IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0) as tax,
						p.name as productname,quantity,(select filename from ".tbl_productimage." where productid=p.id limit 1)as image,
						c.priceid,c.createddate,(SELECT GROUP_CONCAT(pc.variantid) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_productprices." as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid,

						IFNULL(IF(IFNULL((SELECT 1 FROM ".tbl_channel." WHERE memberbasicsalesprice=1 AND id = buyer.channelid),0)=0,mvp.price,mvp.salesprice),pp.price) as price,
						p.discount,
				
						(IFNULL(IF((SELECT 1 FROM ".tbl_channel." as c WHERE c.memberbasicsalesprice=1 AND c.id = buyer.channelid)=1,
						mvp.salesprice,mvp.price 
						),pp.price))as variantprice,
							
						(SELECT GROUP_CONCAT(CONCAT(`variantname`,' : ',`value`,'<br>') SEPARATOR '')
								FROM ".tbl_productcombination." as `pc`
								JOIN ".tbl_variant." as `v` ON `v`.`id`=`pc`.`variantid`
								JOIN ".tbl_attribute." as `a` ON `a`.`id`=`v`.`attributeid`
								WHERE `priceid` = c.priceid limit 1)as productvariants,

						c.referencetype,c.referenceid
			");	

		$this->readdb->from($this->_table." as c");
		$this->readdb->join(tbl_member." as buyer","buyer.id=c.memberid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=c.sellermemberid","LEFT");
		$this->readdb->join(tbl_product." as p","c.productid=p.id","LEFT");
		$this->readdb->join(tbl_productprices." as pp","pp.id=c.priceid","LEFT");
		$this->readdb->join(tbl_memberproduct." as mp","mp.productid=p.id AND mp.memberid=c.memberid","LEFT");
		$this->readdb->join(tbl_membervariantprices." as mvp","mvp.priceid=pp.id AND mp.memberid=c.memberid","LEFT");
		
		$where='';
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		/* if(!is_null($MEMBERID)) {
			$where .= " AND c.memberid=".$MEMBERID." AND c.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.")";
			
		} */
		if(!is_null($MEMBERID)) {
			$where .= " AND c.type=2 AND c.sellermemberid=$MEMBERID AND c.usertype=1";
			
		}
		if($channelid != 0 || $productid != 0){
			if($channelid != 0){
				$where .= ' AND FIND_IN_SET(c.memberid, (SELECT GROUP_CONCAT(id) FROM '.tbl_member.' WHERE channelid IN ('.implode(",",$channelid).')))';
			}
			if($productid != 0){
				$where .= ' AND p.id='.$productid;
			}

			$this->readdb->where("p.status=1 AND date(c.createddate) BETWEEN '".$startdate."' AND '".$enddate."'".$where);
		}else{
			$this->readdb->where("p.status=1 AND date(c.createddate) BETWEEN '".$startdate."' AND '".$enddate."'".$where);
		}
		$this->readdb->group_by("p.id");
		/* (select IF(IFNULL((SELECT 1 FROM ".tbl_channel." WHERE memberbasicsalesprice=1 AND id = buyer.channelid),0)=0,price,salesprice) from ".tbl_membervariantprices." where priceid=c.priceid AND memberid=mp.memberid)as variantprice, */
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
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

	function getcartrecord($memberid,$type="1") {

		$this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		
		if($type=="count"){
			$this->readdb->select('count(c.id)as cartcount');	
		}else{
			$this->readdb->select('c.id as cartid,productid,
							IF(IFNULL((SELECT 1 FROM '.tbl_channel.' WHERE id IN (SELECT channelid FROM '.tbl_member.' WHERE id='.$memberid.') AND productwisepoints=1 AND productwisepointsforbuyer=1 AND IFNULL((SELECT rewardspoints FROM '.tbl_systemconfiguration.' LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,(SELECT pointsforbuyer FROM '.tbl_productprices.' WHERE id=c.priceid),p.pointsforbuyer),0) as rewardpoints,
							IF(IFNULL((SELECT 1 FROM '.tbl_channel.' WHERE id in (SELECT m.channelid FROM '.tbl_membermapping.' as mp INNER JOIN '.tbl_member.' as m ON m.id=mp.mainmemberid AND m.status=1 WHERE mp.submemberid = '.$memberid.') AND productwisepoints=1 AND productwisepointsforseller=1 AND IFNULL((SELECT rewardspoints FROM '.tbl_systemconfiguration.' LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,(SELECT pointsforseller FROM '.tbl_productprices.' WHERE id=c.priceid),p.pointsforseller),0) as referrerrewardpoints,
							(SELECT integratedtax FROM '.tbl_hsncode.' WHERE id=p.hsncodeid) as tax,name as productname,quantity,
							IFNULL((select filename from '.tbl_productimage.' where productid=p.id limit 1),"'.PRODUCTDEFAULTIMAGE.'")as image,
							priceid,
							IF('.PRODUCTDISCOUNT.'=1,discount,0)as discountper,p.isuniversal,p.brandid');
			if($memberspecificproduct==1){
				$this->readdb->select("(IF(
											(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
											(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=c.priceid AND mp.productid=c.productid LIMIT 1),
											
											IF(
												(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
												(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=c.priceid AND productid=c.productid LIMIT 1),
												(SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=c.priceid AND mp.productid=c.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 LIMIT 1)
											)
									)) as price");
			}else{
				$this->readdb->select('IFNULL((SELECT pbp.salesprice FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=c.priceid AND pbp.channelid='.$channelid.' AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as price');
			}
			
		}
		$this->readdb->from($this->_table." as c");
		$this->readdb->join(tbl_product." as p","c.productid=p.id");
		$this->readdb->where(array("memberid"=>$memberid,"(sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR `sellermemberid`=0)"=>null,"p.status"=>1,"p.producttype"=>0));
		$this->readdb->order_by("c.id","DESC");  
		$query = $this->readdb->get();

		// echo $this->readdb->last_query(); exit;
		if($type=="count"){
			return $query->row_array();
		}else{
			return $query->result_array();
		}
	}

	//LISTING DATA
	function _getcustomercart_datatables_query(){
		
		$startdate = isset($_REQUEST['startdate'])?$this->general_model->convertdate($_REQUEST['startdate']):'';
		$enddate = isset($_REQUEST['enddate'])?$this->general_model->convertdate($_REQUEST['enddate']):'';
		
		$this->readdb->select("p.id as productid,p.name as productname,quantity,isuniversal,c.createddate,p.discount,
							IFNULL(mvp.salesprice,pp.price) as price,
							(SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid) as tax,
							(SELECT GROUP_CONCAT(pc.variantid) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_productprices." as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid,
							(SELECT GROUP_CONCAT(CONCAT(`variantname`,' : ',`value`,'<br>') SEPARATOR '')
									FROM ".tbl_productcombination." as `pc`
									JOIN ".tbl_variant." as `v` ON `v`.`id`=`pc`.`variantid`
									JOIN ".tbl_attribute." as `a` ON `a`.`id`=`v`.`attributeid`
									WHERE `priceid` = c.priceid limit 1)as productvariants");	

		$this->readdb->from($this->_table." as c");
		$this->readdb->join(tbl_product." as p","c.productid=p.id");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->join(tbl_memberproduct." as mp","mp.productid=p.id AND mp.memberid=c.memberid","LEFT");
		$this->readdb->join(tbl_membervariantprices." as mvp","mvp.memberid=mp.memberid AND pp.id=mvp.priceid AND mvp.priceid=c.priceid","LEFT");

		if(isset($_REQUEST['memberid'])){
			$this->readdb->where(array("c.memberid"=>$_REQUEST['memberid'],"c.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$_REQUEST['memberid'].")"=>null,'date(c.createddate) BETWEEN "'.$startdate.'" AND "'.$enddate.'"'=>null));
		}else{
		
			$this->readdb->where(array("c.memberid"=>0));
		}

		$this->readdb->where(array("c.type"=>1));
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_searchcustomercart as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_searchcustomercart) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_ordercustomercart[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_datatableorder)) {
			$order = $this->_datatableorder;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function getcustomercart_datatables() {
		$this->_getcustomercart_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		return $query->result();
	}

	function countcustomercart_all() {
		$this->_getcustomercart_datatables_query();
		return $this->readdb->count_all_results();
	}

	function countcustomercart_filtered() {
		$this->_getcustomercart_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}


}