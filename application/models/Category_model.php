<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category_model extends Common_model {
	public $_table = tbl_productcategory;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('pc1.priority' => 'ASC');

	//set column field database for datatable orderable
	public $column_order = array(null, 'pc1.name',"pc1.maincategoryname",'pc1.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('pc1.name',"((select name from ".tbl_productcategory." as pc2 where pc2.id=pc1.maincategoryid))",'DATE_FORMAT(pc1.createddate, "%d/%m/%Y %H:%i:%s")');

	function __construct() {
		parent::__construct();
	}
	function getProductCategoryBySlug($slug,$channelid=0,$memberid=0){
		
		$query = $this->readdb->select("pc.id,pc.name,pc.slug")
							->from($this->_table." as pc")
							->where("pc.status=1 AND pc.channelid='".$channelid."' AND pc.memberid='".$memberid."' AND pc.slug = '".$slug."'")
							->get();		

		return $query->row_array();			
	}
	function getActiveProductCategoryListOnFront($search="",$memberid=0,$channelid=0){
		
		$where = "";
		/* if($search!=""){
			$where .= " AND (p.name LIKE '%".$search."%' OR pc.name LIKE '%".$search."%' OR '".$search."'='')";
		} */
		$query = $this->readdb->select("pc.id,pc.name,pc.slug,COUNT(p.id) as count")
							->from($this->_table." as pc")
							->join(tbl_product." as p","p.categoryid=pc.id AND p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1","INNER")
							->where("pc.memberid='".$memberid."' AND pc.channelid='".$channelid."' AND pc.status=1 AND p.memberid='".$memberid."' AND p.channelid='".$channelid."' AND p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = '".GUESTCHANNELID."' AND salesprice >0 AND allowproduct = 1)".$where)
							->group_by("pc.id")
							->order_by("pc.name ASC")
							/* ->limit(SIDEBAR_PRODUCT_CATEGORY_LIMIT) */
							->get();		

		return $query->result_array();			
	}
	function getActiveProductCategoryListOnMemberFront($search="",$channelid,$memberid){
		
		$where = "";
		$query = $this->readdb->select("pc.id,pc.name,pc.slug,COUNT(p.id) as count")
							->from($this->_table." as pc")
							->join(tbl_product." as p","p.categoryid=pc.id AND p.status=1 AND p.producttype=0 AND p.usertype=1 AND p.memberid='".$memberid."' AND p.channelid='".$channelid."'","INNER")
							->where("pc.memberid='".$memberid."' AND pc.channelid='".$channelid."' AND pc.status=1 AND IFNULL((SELECT count(id) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)>0".$where)
							->group_by("pc.id")
							->order_by("pc.name ASC")
							->get();		
		
		return $query->result_array();			
	}
	function getmaincategory($MEMBERID=0,$CHANNELID=0) {
        $this->readdb->select('id, maincategoryid, IF(maincategoryid = 0, name, CONCAT((SELECT name FROM '.tbl_productcategory.' WHERE id = pc.maincategoryid), " > ",name )) AS name');
		$this->readdb->from(tbl_productcategory.' AS pc');
		$this->readdb->where("pc.status=1 AND pc.memberid='".$MEMBERID."' AND pc.channelid='".$CHANNELID."'");
        $this->readdb->order_by('pc.priority ASC,name ASC');
        $query = $this->readdb->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	function getAllCategory($MEMBERID=0,$CHANNELID=0) {
		
		$query = $this->db->select("pc.id,pc.name,pc.slug,COUNT(p.id) as count")
				->from($this->_table." as pc")
				->join(tbl_product." as p","p.categoryid=pc.id AND p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1","INNER")
				->where("pc.memberid='".$MEMBERID."' AND pc.channelid='".$CHANNELID."'")
				->group_by("pc.id")
				->order_by("pc.name ASC")
				->get();		

		return $query->result_array();		
	}
	
	function getProductCategoryList($memberid,$sellerid=0,$CHANNELID=0,$MEMBERID=0) {

		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		
		$currentsellerid = (!empty($channeldata['currentsellerid']) && $sellerid==0)?$channeldata['currentsellerid']:$sellerid;

		$this->readdb->select('pc.id, pc.maincategoryid, 
							IF(pc.maincategoryid = 0, pc.name, CONCAT(pc.name," (",(SELECT name FROM '.tbl_productcategory.' WHERE id = pc.maincategoryid), ")")) AS name');
		$this->readdb->from(tbl_productcategory.' AS pc');
		$this->readdb->join(tbl_product." as p","pc.id=p.categoryid","INNER");
		$this->readdb->where("pc.memberid='".$MEMBERID."' AND pc.channelid='".$CHANNELID."'");
		if($memberspecificproduct==1){
			
			$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,

									p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

										p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp 
											WHERE pbp.channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid),
										
										p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
									)
							))");
		}else{
			$this->readdb->where("p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp  
				WHERE pbp.channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)");
		}
		
		$this->readdb->group_by("pc.id");
        $this->readdb->order_by('pc.name','ASC');
        $query = $this->readdb->get();
		//echo $this->db->last_query();exit;
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	function getMultipleMemberProductCategoryList($memberid) {

		$this->readdb->select('id, maincategoryid, IF(maincategoryid = 0, name, CONCAT(name," (",(SELECT name FROM '.tbl_productcategory.' WHERE id = t.maincategoryid), ")")) AS name');
		$this->readdb->from(tbl_productcategory.' AS t');
		
		$this->readdb->where("t.id IN (SELECT p.categoryid FROM ".tbl_product." as p WHERE p.status=1 AND 
										p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												WHERE mp.memberid IN (".$memberid."))
									) GROUP BY p.categoryid)");
		
		$this->readdb->order_by('t.priority','ASC');
		$this->readdb->order_by('name','ASC');
        $query = $this->readdb->get();
		// echo $this->db->last_query();exit;
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			return $query->result();
		}
		
	}

	function _get_datatables_query($MEMBERID,$CHANNELID){
		
		$maincategoryid = (isset($_REQUEST['maincategoryid']))?$_REQUEST['maincategoryid']:"";
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		if(!is_null($memberid)){
			$this->load->model('Channel_model', 'Channel');
			$channeldata = $this->Channel->getMemberChannelData($memberid);
			$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
			$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		}
		$this->readdb->select('pc1.id,pc1.name,DATE_FORMAT(pc1.createddate, "%d/%m/%Y %H:%i:%s") as date,pc1.status,pc1.name as maincategoryname,pc1.priority,pc1.addedby,pc1.usertype,pc1.createddate');
		$this->readdb->from($this->_table." as pc1");
		$this->readdb->join(tbl_product." as p","p.categoryid=pc1.id","LEFT");
		$this->readdb->where("(pc1.maincategoryid='".$maincategoryid."' OR '".$maincategoryid."'='')");
		
		if(!is_null($memberid)){
			
			$this->readdb->group_start();
				if($memberspecificproduct==1){
					$this->readdb->where("(IF(
											(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
											p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mp.productid),
											
											IF(
												(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
												p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),
												p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
											)
									))");

									
				}else{
					$this->readdb->where("p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."')
					AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid)");
				}
			$this->readdb->group_end();
			$this->readdb->or_group_start();
				$this->readdb->where("(pc1.id in (SELECT categoryid FROM ".tbl_product." WHERE id IN (select productid from ".tbl_memberproduct." where memberid=".$this->readdb->escape($memberid)."))) OR (pc1.memberid='".$MEMBERID."' AND pc1.channelid='".$CHANNELID."')");
			$this->readdb->group_end();
		
		}else{
			$this->readdb->where("pc1.memberid='".$MEMBERID."' AND pc1.channelid='".$CHANNELID."'");
		}
		$this->readdb->group_by("pc1.id");
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

	function count_all($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		return $this->readdb->count_all_results();
	}

	function count_filtered($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function getcategoryrecord($counter,$search,$memberid=0,$channelid=0,$sellerid=0) {
		$limit=10;

		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']) && $sellerid==0)?$channeldata['currentsellerid']:$sellerid;

		$this->readdb->select('pc.id,pc.name,IF(pc.image!="",pc.image,"'.CATEGORYDEFAULTIMAGE.'") as image,count(*) as total');
		$this->readdb->from($this->_table." as pc");
		$this->readdb->join(tbl_product." as p","pc.id=p.categoryid AND p.status=1 AND p.channelid=0 AND p.memberid=0","INNER");
		//$this->readdb->where('pc.memberid=0 AND pc.channelid=0 AND pc.maincategoryid= 0 AND pc.status = 1');
		$this->readdb->where('pc.memberid=0 AND pc.channelid=0 AND pc.status = 1');
		$this->readdb->where("(pc.name LIKE CONCAT('%','".$search."','%'))");
		
		if($memberid!=0 || $channelid!=0){
	
			if($memberspecificproduct==1){
				$this->readdb->where("(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
										
										p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
											WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
											
											p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbp 
												WHERE channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),
												
											p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
												WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
										)
								))");

				/*$this->db->where("((IF(
					(SELECT count(id) FROM ".tbl_memberproduct." WHERE memberid='".$memberid."' LIMIT 1)>0 OR ".$memberbasicsalesprice."=0,
			
					p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
							INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
							WHERE mp.memberid='".$memberid."' AND 

							(SELECT count(mvp2.productallow) FROM ".tbl_membervariantprices." as mvp2 WHERE mvp2.memberid=mp.memberid AND mvp2.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp2.productallow=1 LIMIT 1)>0
							AND
							(SELECT SUM(IF((".$memberbasicsalespriPNotifyce."=1 AND mvp2.price>0) OR mvp2.salesprice>0,1,0)) FROM ".tbl_membervariantprices." as mvp2 WHERE mvp2.memberid=mp.memberid AND mvp2.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp2.productallow=1)>0
							AND 										
							(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))),
					
					IF((SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$memberid."')=0,

					p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND salesprice > 0 AND allowproduct = 1 GROUP BY productid),
					
					p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
							INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
							WHERE mp.memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$memberid."') AND 
														(SELECT count(mvp.productallow) FROM ".tbl_membervariantprices." as mvp WHERE mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1 LIMIT 1)>0
														AND 
														(SELECT SUM(IF((".$memberbasicsalesprice."=1 AND mvp.price>0) OR mvp.salesprice>0,1,0)) FROM ".tbl_membervariantprices." as mvp WHERE mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1)>0
														AND
														(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))))


				)))");*/
			}else{
				$this->readdb->where("p.id IN (SELECT pbpm.productid FROM ".tbl_productbasicpricemapping." as pbpm
						WHERE pbpm.channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0),0) > 0 AND pbpm.allowproduct = 1 AND pbpm.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbpm.productid)");
			}
		}
		
		$this->readdb->group_by("pc.id");
		$this->readdb->order_by("pc.priority","ASC");	
		if($counter != -1){
        	$this->readdb->limit($limit,$counter);
		}
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		if($query->num_rows() == 0){
			return array();
		} 
		 else {
			$Data = $query->result_array();
		
			$json = array();
			foreach ($Data as $row) {
				$json[] = $row;
			}
			return $json;
		}
	}

	function getcategorydetail($memberid=0,$channelid=0,$categoryid=0){

		$this->load->model('Channel_model', 'Channel');
		$sellerid = 0;
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']) && $sellerid=0)?$channeldata['currentsellerid']:$sellerid;

		$this->readdb->select('pc.id,pc.name,pc.image');		
		$this->readdb->from($this->_table." as pc");
		$this->readdb->join(tbl_product." as p","p.status=1","INNER");
		//$this->readdb->where('pc.memberid="'.$memberid.'" AND pc.channelid="'.$channelid.'" AND pc.maincategoryid= 0 AND pc.status = 1 AND (pc.id='.$categoryid.' OR '.$categoryid.'=0)');
		$this->readdb->where('pc.maincategoryid= 0 AND pc.status = 1 AND (pc.id='.$categoryid.' OR '.$categoryid.'=0)');
		$this->readdb->where('p.categoryid IN(SELECT pc2.id FROM '.$this->_table.' as pc2 WHERE pc2.id=pc.id AND pc2.status = 1)');
		if($memberid!=0 || $channelid!=0){
            if ($memberspecificproduct==1) {
                $this->readdb->where("(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
										
										p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
											WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

											p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp  
												WHERE pbp.channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid),

											p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
												WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
										)
								))");
            }else{
				$this->readdb->where("p.id IN (SELECT pbpm.productid FROM ".tbl_productbasicpricemapping." as pbpm 
					WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0),0) > 0
					AND pbpm.allowproduct = 1 AND pbpm.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbpm.productid)");
			}
			//$this->readdb->where('(pc.id in (select maincategoryid FROM '.$this->_table.' WHERE id IN (select categoryid from '.tbl_product.' where id in (select productid from '.tbl_memberproduct.' where memberid IN (SELECT id FROM '.tbl_member.' WHERE id = '.$memberid.' AND channelid='.$channelid.')))) OR pc.id in (select categoryid from '.tbl_product.' where id in (select productid from '.tbl_memberproduct.' where memberid IN (SELECT id FROM '.tbl_member.' WHERE id = '.$memberid.' AND channelid='.$channelid.'))))');
		}
		$this->readdb->group_by("pc.id");
		$this->readdb->order_by("pc.priority","ASC");
		$query = $this->readdb->get();
	
		if($query->num_rows() == 0){
			return array();
		} 
		 else {
			$Data = $query->result_array();
			$json=array();
		 	
		   foreach ($Data as $row) {
               
				$final = array();			
				$subcategorydata = $this->subcategory($row['id'],$memberid,$channelid,$memberspecificproduct,$currentsellerid);
				$subcat = array();	
				foreach ($subcategorydata as $subrow) {
					$final[] = array("subcatid"=>$subrow['id'],'subcatename' =>$subrow['name']);
					$subcat = $final;
				}
				$json[] = array("categoryid"=>$row['id'],
							"categoryname"=>$row['name'],
							"subcategory" => $subcat
							);

			}
			 return $json;
		}			

	
    }

	function  getsubcategoryrecord($counter,$id,$search,$memberid=0,$channelid=0) {
		$limit=10;
		$this->readdb->select('pc.id,pc.name,IF(pc.image!="",pc.image,"'.CATEGORYDEFAULTIMAGE.'") as image');		
		$this->readdb->from($this->_table." as pc");
		$this->readdb->join(tbl_product." as p","pc.id=p.categoryid","INNER");
	    $this->readdb->where('pc.memberid="'.$memberid.'" AND pc.channelid="'.$channelid.'" AND  maincategoryid="'.$id.'" AND pc.status = 1');
		$this->readdb->where("(pc.name LIKE CONCAT('%','$search','%'))");
		$this->readdb->where("((SELECT SUM(price) FROM ".tbl_productprices." WHERE productid=p.id)>0 OR (select count(id) from ".tbl_productcombination." where priceid in (select id from ".tbl_productprices." where productid=p.id))>0)");
		$this->readdb->order_by("pc.priority","ASC");
		$this->readdb->group_by("pc.id");
		$this->readdb->limit($limit,$counter);  
		 
        if($memberid!=0 || $channelid!=0){
			$this->readdb->where('pc.id in(select categoryid from '.tbl_product.' where id in(select productid from '.tbl_memberproduct.' where memberid IN (SELECT id FROM '.tbl_member.' WHERE id='.$this->db->escape($memberid).' AND channelid='.$this->db->escape($channelid).')))');
		}  
		$query = $this->readdb->get();		
		// echo $this->db->last_query(); exit;

		if($query->num_rows() == 0){
			return array();
		} 
		 else {
			$Data = $query->result_array();
			$json = array();
			foreach ($Data as $row) {
				$json[] = $row;
			}
			return $json;
		}
	}

	function subcategory($id,$memberid=0,$channelid=0,$memberspecificproduct,$currentsellerid){
		$this->readdb->select("pc.id,pc.name");
		$this->readdb->from($this->_table." as pc");
		$this->readdb->join(tbl_product." as p","p.status=1","INNER");
		//$this->readdb->where("pc.memberid='".$memberid."' AND pc.channelid='".$channelid."'");
		$this->readdb->where('pc.maincategoryid = "'.$id.'" AND pc.status = 1');
		$this->readdb->where('p.categoryid IN(SELECT pc2.id FROM '.$this->_table.' as pc2 WHERE pc2.id=pc.id AND pc2.status = 1)');
		/* if($memberid!=0 || $channelid!=0){
			$this->readdb->where('maincategoryid = "'.$id.'" AND status = 1 AND id in (select categoryid from '.tbl_product.' where id in (select productid from '.tbl_memberproduct.' where memberid IN (SELECT id FROM '.tbl_member.' WHERE id = '.$memberid.' AND channelid='.$channelid.')))');		
		}else{
			$this->readdb->where('maincategoryid = "'.$id.'" AND status = 1');
		} */
		if($memberid!=0 || $channelid!=0){
            if ($memberspecificproduct==1) {
                $this->readdb->where("(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
										
										p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1)
											WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

											p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp  
												WHERE pbp.channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid),

											p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
												WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
										)
								))");
            }else{
				$this->readdb->where("p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp
					WHERE pbp.channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)");
			}
			//$this->readdb->where('(pc.id in (select maincategoryid FROM '.$this->_table.' WHERE id IN (select categoryid from '.tbl_product.' where id in (select productid from '.tbl_memberproduct.' where memberid IN (SELECT id FROM '.tbl_member.' WHERE id = '.$memberid.' AND channelid='.$channelid.')))) OR pc.id in (select categoryid from '.tbl_product.' where id in (select productid from '.tbl_memberproduct.' where memberid IN (SELECT id FROM '.tbl_member.' WHERE id = '.$memberid.' AND channelid='.$channelid.'))))');
		}
		$this->readdb->group_by("pc.id");
		$this->readdb->order_by("pc.priority","ASC");
		$query = $this->readdb->get();
		return $query->result_array();

	}
	
	function getProductvariant($memberid=0,$channelid=0,$categoryid=0){
		$categoryfinal = array();
		$this->readdb->distinct();
		$this->readdb->select('a.id as attributeid,a.variantname');
		$this->readdb->from(tbl_attribute." as a");
		$this->readdb->join(tbl_variant." as v","a.id=v.attributeid");
		$this->readdb->order_by("a.id","DESC");
		$query=$this->readdb->get();	
		$ProductData = $query->result_array();
		$json=array();

		foreach($ProductData as $Data){
			
			$attributeid= $Data['attributeid'];
			
			$this->readdb->select('variantid,pp.productid,value');
			$this->readdb->from(tbl_productcombination." as pc");
			$this->readdb->join(tbl_productprices." as pp","pp.id=pc.priceid");
			$this->readdb->join(tbl_product." as p","p.id=pp.productid AND (p.categoryid=".$categoryid." OR 0=".$categoryid.")");
			$this->readdb->join(tbl_variant." as v","v.id=pc.variantid");
			$this->readdb->join(tbl_attribute." as a","a.id=v.attributeid");
			$this->readdb->where("v.attributeid = ".$attributeid);
			if($memberid!=0 || $channelid!=0){
				$this->readdb->where('productid in(select productid from '.tbl_memberproduct.' where memberid IN (SELECT id FROM '.tbl_member.' WHERE id='.$memberid.' AND channelid='.$channelid.'))');
			}
			$this->readdb->group_by("variantid");
			$query=$this->readdb->get();
			// echo $this->db->last_query();exit();
			$variantData= $query->result_array();
			if(count($variantData)>0){
				$categoryfinal = array();
				$categoryfinal['variantname']= $Data['variantname'];
				foreach($variantData as $row){ 
					$tmp = array("variantid"=>$row['variantid'],'optionvalue' =>$row['value']); 
					$categoryfinal['value'][] = $tmp;
				}
			$json[] = $categoryfinal;		
			}
		}
		return $json;
	}
        
}
 ?>            
