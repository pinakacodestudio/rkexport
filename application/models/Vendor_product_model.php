<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vendor_product_model extends Common_model {
	public $_table = tbl_product;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('p.priority' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'p.name','pc.name','sellermembername',null,'price','salesprice');

	//set column field database for datatable searchable 
	public $column_search = array('CONCAT(p.name," ",IFNULL(
		(SELECT CONCAT("[",GROUP_CONCAT(v.value),"]")  
		FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),""))','pc.name','IFNULL(m.name,"Company")');

	function __construct() {
		parent::__construct();
	}

	/* function getAllProductsDetail(){
		$memberid = $this->session->userdata(base_url().'MEMBERID');

		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;

		$this->readdb->select('p.id,pp.id as priceid,pc.name as categoryname,
							CONCAT(p.name," ",IFNULL(
								(SELECT CONCAT("[",GROUP_CONCAT(v.value),"]")  
								FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"")) as productname,
								p.name,
							p.createddate,p.status,p.isuniversal,
							(SELECT GROUP_CONCAT(pc.variantid) FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_productprices.' as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid,
							p.discount,p.priority,
							IFNULL(m.id,0) as sellermemberid,
							IFNULL(m.name,"Company") as sellermembername,
							IFNULL(m.membercode,"") as sellermembercode,
							IFNULL(m.channelid,"0") as sellerchannelid,
							');

		if ($memberspecificproduct==1) {
			$this->readdb->select("$channelid as channelid,
								IFNULL((SELECT salesprice FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$memberid." LIMIT 1),0) as salesprice,
								IFNULL((SELECT productallow FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$memberid." LIMIT 1),0) as productallow,
								(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
										(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
										(SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)) as price");
		}else{
			$this->readdb->select('0 as channelid,IFNULL((SELECT pbp.salesprice FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0) as price');
		}
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");

		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->join(tbl_member." as m", "m.id=".$currentsellerid,"LEFT");

		if($memberspecificproduct==1){
			$this->readdb->where("(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								pp.id IN(SELECT mvp.priceid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mvp.priceid),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									pp.id IN (SELECT productpriceid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productpriceid),
									pp.id IN(SELECT mvp.priceid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mvp.priceid)
								)
						))");
		}else{
			$this->readdb->where("p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."'
								AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid)");
		}
		$this->readdb->where('p.status=1 AND p.producttype=0');
		$this->readdb->group_by("p.id");
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();

		if($query->num_rows() > 0){
			$data = $query->result_array();
			$json=array();
			if(!empty($data)){
				foreach($data as $row){
					$this->load->model("Product_combination_model","Product_combination");
					$this->load->model("Stock_report_model","Stock");
					$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
					$productdata = $this->Stock->getProductStockList($MEMBERID,0,'',$row['id']);
					$row['universalstock'] = (!empty($productdata[0]['overallclosingstock']))?$productdata[0]['overallclosingstock']:0;
					$variantarr['variant'] = array();
					$productcombination = $this->Product_combination->getMemberProductcombinationByProductID($row['id'],$MEMBERID);
		
					$ProductStock = $this->Stock->getVariantStock($MEMBERID,$row['id'],'','');
					
					foreach ($productcombination as $pc) {
						$key = array_search($pc['priceid'], array_column($ProductStock, 'combinationid'));

						$variantarr['variant'][$pc['priceid']]['price']=$pc['price'];
						$variantarr['variant'][$pc['priceid']]['stock']=(int)$ProductStock[$key]['overallclosingstock'];
						$variantarr['variant'][$pc['priceid']]['variants'][] = array("variantvalue"=>$pc['variantname'],"variantname"=>$pc['attributename']);
					}
					
					$json[] = array_merge($row, $variantarr);
				}
			}
			return $json;
			
		}else{
			return array();
		}
	} */
	function _get_datatables_query(){
		$PostData=$this->input->post();
		$vendorid = $PostData['vendorid'];
		
		$channeldata = $this->Channel->getMemberChannelData($vendorid);
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;
		
		$this->readdb->select('p.id,pp.id as priceid,pc.name as categoryname,
							CONCAT(p.name," ",IFNULL(
								(SELECT CONCAT("[",GROUP_CONCAT(v.value),"]")  
								FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"")) as name,
							p.createddate,p.status,p.isuniversal,
							(SELECT GROUP_CONCAT(pc.variantid) FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_productprices.' as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid,
							p.discount,p.priority,
							IFNULL(m.id,0) as sellermemberid,
							IFNULL(m.name,"Company") as sellermembername,
							IFNULL(m.membercode,"") as sellermembercode,
							IFNULL(m.channelid,"0") as sellerchannelid,
						');
		
		if($totalproductcount>0 && $memberspecificproduct==1){
			$this->readdb->select('mvp.channelid,mvp.productallow,mvp.price,mvp.salesprice');
		}else{
			if ($memberspecificproduct==1) {
				$this->readdb->select("$channelid as channelid,
									IFNULL((SELECT salesprice FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$vendorid." LIMIT 1),0) as salesprice,
									IFNULL((SELECT productallow FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$vendorid." LIMIT 1),0) as productallow,
									(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=0 and mp.memberid='".$vendorid."')>0,
										(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=0 and mp.memberid='".$vendorid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
										
										(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1)
									)) as price");
			}else{
				$this->readdb->select('0 as channelid,IFNULL((SELECT pbp.salesprice FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0) as price');
			}
		}
        
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");

        if ($totalproductcount>0 && $memberspecificproduct==1) {
			$this->readdb->join(tbl_memberproduct." as mp", "mp.memberid=".$vendorid." AND mp.productid=p.id","INNER");
			$this->readdb->join(tbl_membervariantprices." as mvp", "mvp.memberid=mp.memberid","INNER");
			$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id AND mvp.priceid=pp.id","INNER");
			$this->readdb->join(tbl_member." as m", "m.id=mvp.sellermemberid","LEFT");
			$this->readdb->where("p.status=1 AND (p.producttype = 2 OR IF((SELECT purchaseregularproduct FROM ".tbl_member." where id='".$vendorid."')=1,p.producttype = 0,''))");
			$this->readdb->group_by("mvp.priceid,mvp.sellermemberid,mvp.memberid");
        }else{
			$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
			$this->readdb->join(tbl_member." as m", "m.id=0","LEFT");

			if($memberspecificproduct==1){
				$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=0 and mp.memberid='".$vendorid."')>0,
									pp.id IN(SELECT mvp.priceid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=0 and mp.memberid='".$vendorid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mvp.priceid),
									
									pp.id IN (SELECT productpriceid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$vendorid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productpriceid)
								
							))");
			}else{
				$this->readdb->where("p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."'
									AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid)");
			}
			$this->readdb->where("p.status=1 AND (p.producttype = 2 OR IF((SELECT purchaseregularproduct FROM ".tbl_member." where id='".$vendorid."')=1,p.producttype = 0,''))");
			$this->readdb->group_by("pp.id");
		}
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
		} else if(isset($this->_orderproduct)) {
			$order = $this->_orderproduct;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1){
			$this->readdb->limit($_POST['length'], $_POST['start']);
		}
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result();
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