<?php

class Product_section_model extends Common_model {

	//put your code here
	public $_table = tbl_productsection;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'name','displaytype','maxhomeproduct','forwebsite','forapp'); //set column field database for datatable orderable
	public $column_search = array('name','displaytype','maxhomeproduct','forwebsite','forapp'); //set column field database for datatable searchable 
	public $_order = array('priority' => 'asc');
	public $order = array('priority' => 'asc'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getProductSectionsProductById($id){

		$query = $this->readdb->select("ps.name,(SELECT name FROM ".tbl_product." WHERE id=psm.productid) as productname")
							->from($this->_table." as ps")
							->join(tbl_productsectionmapping." as psm","psm.productsectionid=ps.id","INNER")
							->where("psm.id='".$id."'")
							->get();
		return $query->row_array();
	}
	function getProductSectionsByProductId($productid){

		$query = $this->readdb->select("ps.id,ps.name,CONCAT(ps.name, IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_channel." WHERE id=ps.channelid),'')) as sectionname")
							->from($this->_table." as ps")
							->join(tbl_productsectionmapping." as psm","psm.productsectionid=ps.id","INNER")
							->where("ps.status=1 AND psm.productid='".$productid."'")
							->order_by("ps.priority","ASC")
							->get();
		return $query->result_array();
	}

	function getProductSectionOnFrontWebsite(){
		
		$channelid = GUESTCHANNELID;
		if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
			$channelid = CUSTOMERCHANNELID;
		}

		$query = $this->readdb->select("ps.id,ps.name,ps.maxhomeproduct,ps.description,ps.displaytype")
					->from($this->_table." ps")
					->where("(ps.channelid= '".$channelid."' OR ps.channelid=0) AND ps.status=1 AND ps.type=0 AND ps.forwebsite=1 AND memberid=0")
					->order_by("ps.inorder ASC")
					->get();
		
		$sectiondata = $query->result_array();

		if(!empty($sectiondata)){
			
			$json = array();
			foreach($sectiondata as $section){

				$query = $this->readdb->select("p.id,p.name as productname,p.discount,p.slug,
						IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,p.isuniversal,

						(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid) as category,
						(SELECT slug FROM ".tbl_productcategory." WHERE id=p.categoryid) as categoryslug,
						
						@price:=IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm
									INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0 
									WHERE pbpm.channelid = '".$channelid."' AND pbpm.allowproduct=1 AND pbpm.productid=p.id),0) as price,

						@discount:=IFNULL((SELECT pbqp.discount FROM ".tbl_productbasicpricemapping." as pbpm 
									INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0
									WHERE pbpm.channelid='".$channelid."' AND pbpm.allowproduct=1 AND pbpm.productid=p.id AND pbqp.salesprice=@price LIMIT 1),0) as discount,

						IF(@discount>0,(@price-(@price*@discount/100)),@price) as pricewithdiscount
							")
							->from(tbl_productsectionmapping." as psm")
							->join(tbl_product." as p","p.id=psm.productid","INNER")
							->where("psm.productsectionid=".$section['id'])
							->where("IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicpricemapping." as pbpm INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0  WHERE channelid='".$channelid."' AND pbpm.allowproduct=1 AND pbpm.productid=p.id),0) > 0 AND p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1")
							->limit($section['maxhomeproduct'])
							->order_by("psm.id DESC")
							->get();
			
				$productdata = $query->result_array();

				if(count($productdata) > 0){
					$json[] = array_merge($section,array("products"=>$productdata));
				}
			}	
			return $json;
		}else{
			return array();
		}
	}
	function getProductSectionOnFrontMemberWebsite($channelid,$memberid){
		
		$query = $this->readdb->select("ps.id,ps.name,ps.maxhomeproduct,ps.description,ps.displaytype")
					->from($this->_table." ps")
					->where("ps.channelid= '".$channelid."' AND ps.memberid='".$memberid."' AND ps.status=1 AND ps.type=1 AND ps.forwebsite=1 AND memberid!=0")
					->order_by("ps.inorder ASC")
					->get();
		
		$sectiondata = $query->result_array();
		
		if(!empty($sectiondata)){
			
			$json = array();
			foreach($sectiondata as $section){

				$query = $this->readdb->select("p.id,p.name as productname,p.discount,p.slug,
						IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,p.isuniversal,

						(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid) as category,
						(SELECT slug FROM ".tbl_productcategory." WHERE id=p.categoryid) as categoryslug,
						
						@price:=IFNULL((SELECT min(pqp.price) FROM ".tbl_productprices." as pp
									INNER JOIN ".tbl_productquantityprices." as pqp ON pqp.productpricesid=pp.id AND pqp.price>0 
									WHERE pp.productid=p.id),0) as price,

						@discount:=IFNULL((SELECT pqp.discount FROM ".tbl_productprices." as pp
									INNER JOIN ".tbl_productquantityprices." as pqp ON pqp.productpricesid=pp.id AND pqp.price>0 
									WHERE pp.productid=p.id AND pqp.price=@price LIMIT 1),0) as discount,

						IF(@discount>0,(@price-(@price*@discount/100)),@price) as pricewithdiscount
							")
							->from(tbl_productsectionmapping." as psm")
							->join(tbl_product." as p","p.id=psm.productid","INNER")
							->where("psm.productsectionid=".$section['id'])
							->where("IFNULL((SELECT count(id) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0) > 0 AND p.status=1 AND p.producttype=0 AND p.channelid= '".$channelid."' AND p.memberid='".$memberid."'")
							->limit($section['maxhomeproduct'])
							->order_by("psm.id DESC")
							->get();
			
				$productdata = $query->result_array();

				if(count($productdata) > 0){
					$json[] = array_merge($section,array("products"=>$productdata));
				}
			}	
			return $json;
		}else{
			return array();
		}
	}

	function getProductsectionDataByID($ID){
		$query = $this->readdb->select("id,channelid,name,displaytype,status,maxhomeproduct,inorder,forwebsite,forapp,addedby")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	function getapplicationproductsection($memberid=0,$channelid=0){
		$this->readdb->select("id,name");
		$this->readdb->from($this->_table);
		$this->readdb->where(array("status"=>1));
		if($memberid!=0 || $channelid!=0){
			$this->readdb->where("((channelid=".$channelid." AND status=1 AND type=0) OR (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") AND channelid=".$channelid." AND status=1 AND type=1)) AND id IN (SELECT productsectionid FROM ".tbl_productsectionmapping." WHERE productid IN (SELECT productid FROM ".tbl_memberproduct." where memberid=".$memberid."))");

			//$this->readdb->where("addedby=".$memberid." OR addedby IN (select mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")");
		}
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result_array();
	}

	//LISTING DATA
	function _get_datatables_query(){
		
		$channelid = $_REQUEST['channelid'];

		$this->readdb->select("id,name,channelid,displaytype,status,priority,maxhomeproduct,addedby,inorder");
		$this->readdb->from($this->_table);
		
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		if(!is_null($MEMBERID)) {
            $this->readdb->where("(addedby=".$MEMBERID." OR (channelid=".$CHANNELID." AND status=1 AND type=0) OR (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.") AND channelid=".$CHANNELID." AND status=1 AND type=1))");
		}
		
		if($channelid != 0){
			if(is_array($channelid)){
				$channelid = implode(",",$channelid);
			}
			$this->readdb->where("channelid IN (".$channelid.")");
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
		} else if(isset($this->_order)) {
			$order = $this->_order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}
}
