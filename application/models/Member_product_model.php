<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends Common_model {
	public $_table = tbl_product;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = tbl_product.'.id DESC';
	public $_datatableorder = array(tbl_product.'.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'name','categoryname','price','discount','priority');

	//set column field database for datatable searchable 
	public $column_search = array('name','((select name from '.tbl_productcategory.' where id=categoryid))','price','discount','priority');

	function __construct() {
		parent::__construct();
	}
   	function getmaincategory() {
        $this->readdb->select('id, maincategoryid, IF(maincategoryid = 0, name, CONCAT((SELECT name FROM '.tbl_productcategory.' WHERE id = t.maincategoryid), " > ",name )) AS name');
		$this->readdb->from(tbl_productcategory.' AS t');
		$this->readdb->where("t.status=1");
        $this->readdb->order_by = array('name' => 'ASC');
        $query = $this->readdb->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}

	function getallcategory() {
        $this->readdb->select('id,maincategoryid,name');
        $this->readdb->from(tbl_productcategory.' AS t');
        $this->readdb->order_by = array('name' => 'ASC');
        $query = $this->readdb->get();
		return $query->result_array();
	}

	function getproductdata($id)
	{
		$this->readdb->select('p.name,p.id,p.categoryid,p.description,p.metatitle,p.metadescription,p.metakeyword,p.status,p.priority');
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","left");
		$this->readdb->where('p.id='.$id);
		 $query = $this->readdb->get();
		 //print_R($query->result_array());exit;
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->row_array();
		}

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
		$PostData = $this->input->post();

		if(isset($PostData['sectionid'])){
			$this->readdb->select(tbl_product.'.id,(select name from '.tbl_productcategory.' where id=categoryid)as categoryname,name,description,price,'.tbl_product.'.createddate,priority,status,isuniversal,(select max(price) from '.tbl_productprices.' where productid='.tbl_product.'.id limit 1)as maxprice,(select min(price) from '.tbl_productprices.' where productid='.tbl_product.'.id limit 1)as minprice,discount,ps.id as psid,productpriority');
			$this->_datatableorder = array("productpriority"=>"asc");
		}else{
			$this->readdb->select(tbl_product.'.id,(select name from '.tbl_productcategory.' where id=categoryid)as categoryname,name,description,price,'.tbl_product.'.createddate,priority,status,isuniversal,(select max(price) from '.tbl_productprices.' where productid='.tbl_product.'.id limit 1)as maxprice,(select min(price) from '.tbl_productprices.' where productid='.tbl_product.'.id limit 1)as minprice,discount');
		}
		$this->readdb->from($this->_table);
		
		if(!is_null($this->session->userdata(base_url().'MEMBERID'))){
			$this->readdb->join(tbl_memberproduct." as mp",tbl_product.".id=mp.productid");
			$this->readdb->where(array("mp.memberid"=>$this->session->userdata(base_url().'MEMBERID')));
		}

		if(isset($PostData['sectionid'])){
			$this->readdb->join(tbl_productsectionmapping." as ps",tbl_product.".id=ps.productid");
			$this->readdb->where(array("productsectionid"=>$PostData['sectionid']));
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
		} else if(isset($this->_datatableorder)) {
			$order = $this->_datatableorder;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	/* function getAllTopic() {
        $this->readdb->select('id, topicid, IF(topicid = 0, name, CONCAT(name, " > ", (SELECT name FROM '.tbl_topic.' WHERE id = t.topicid))) AS name');
        $this->readdb->from(tbl_topic.' AS t');
        $this->readdb->where('sectionid = '.$sectionid);
        $this->readdb->order_by = array('name' => 'ASC');
        $query = $this->readdb->get();

		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	} */


	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}
	
	function getproductrecord($counter,$id='',$variantid='',$search,$memberid=0,$sectionid='') {
		$limit=10;

			if($variantid == "" ){
				$this->readdb->select('p.id,p.name as productname,description,(select filename from '.tbl_productimage.' where productid=p.id limit 1)as file,(select type from '.tbl_productimage.' where productid=p.id limit 1)as filetype');
				$this->readdb->from($this->_table." as p");
				if($sectionid!=''){
					$this->readdb->where(array('ps.id in('.$sectionid.')'=>null));
					$this->readdb->join(tbl_productsectionmapping." as pm","pm.productid=p.id");
					$this->readdb->join(tbl_productsection." as ps","pm.productsectionid=ps.id and ps.status=1");
					$this->readdb->order_by("ps.priority asc,p.priority asc");	
				}
                if($id!=""){
				$this->readdb->where('p.categoryid = "'.$id.'"','status = 1');
			    }
			    if($memberid!=0){
			    	$this->readdb->where('p.id in(select productid from '.tbl_memberproduct.' where memberid='.$this->readdb->escape($memberid).')');
				}
				$this->readdb->where("(p.name LIKE CONCAT('%','$search','%'))");
				$this->readdb->where(array("p.status"=>1));
				$this->readdb->where("(p.price > 0 or (select count(id) from ".tbl_productcombination." where priceid in (select id from ".tbl_productprices." where productid=p.id))>0)");
				$this->readdb->order_by("p.id", "DESC");
			    $this->readdb->group_by("p.id");
				if($counter != -1){
		        $this->readdb->limit($limit,$counter);
		        }   
				$query = $this->readdb->get();
				
		  }else{
			$this->readdb->select('DISTINCT(p.id),p.name as productname,description,(select filename from '.tbl_productimage.' where productid=p.id limit 1)as file,(select type from '.tbl_productimage.' where productid=p.id limit 1)as filetype');
			$this->readdb->from($this->_table." as p");
			$this->readdb->join(tbl_productprices." as pp","p.id=pp.productid");
			$this->readdb->join(tbl_productcombination." as pc","pc.priceid=pp.id");
			$this->readdb->where("(p.price > 0 or (select count(id) from ".tbl_productcombination." where priceid in (select id from ".tbl_productprices." where productid=p.id))>0)");
			 if($memberid!=0){
		    	$this->readdb->where('p.id in(select productid from '.tbl_memberproduct.' where memberid='.$this->readdb->escape($memberid).')');
			}
			if($sectionid!=''){
				$this->readdb->where(array('ps.id in('.$sectionid.')'=>null));
				$this->readdb->join(tbl_productsectionmapping." as pm","pm.productid=p.id");
				$this->readdb->join(tbl_productsection." as ps","pm.productsectionid=ps.id and ps.status=1");
				$this->readdb->order_by("ps.priority asc,p.priority asc");	
			}
			$this->readdb->where(array("variantid in(".$variantid.")"=>null,"p.status"=>1));
			if($id!=""){
		    	$this->readdb->where("p.categoryid='".$id."'");
		    } 
		    $this->readdb->where("(p.name LIKE CONCAT('%','$search','%'))");
		    if($counter != -1){
		        $this->readdb->limit($limit,$counter);
		        }
		    $this->readdb->order_by("p.id","DESC");
		    $query=$this->readdb->get();
		   }
	
		if($query->num_rows() == 0){
			return array(); 
		} 
		 else {	
			 $Data =$query->result_array();
			 foreach($Data as $k=>$dt){
				if(is_null($dt['file'])){
					$Data[$k]['file']=PRODUCTDEFAULTIMAGE;
				}
				if(is_null($dt['filetype'])){
					$Data[$k]['filetype']="1";
				}
			 }
			return $Data;
		}
	}


	function getProductFiles($id){

		$this->readdb->select("filename,type");
		$this->readdb->from(tbl_productimage);
		$this->readdb->where('productid = "'.$id.'"');		
		$query = $this->readdb->get();
		return $query->result_array();			
	}

	function getProductImage($id){

	  $query=$this->readdb->select("pi.filename,pi.type")
	    ->from($this->_table." as p")
		->from(tbl_productimage." as pi")
		->where("p.id=pi.productid")
		->where("FIND_IN_SET(pi.productid,'".$id."')<>0")
			
		->get()->row_array();
			if (!empty($query)) {
			return $query;
		} else {
			return array();
		}		
	}

	/*function getFiles($productid,$where='',$limit=0){

		$this->readdb->select("file,type,IFNULL((SELECT file FROM ".tbl_productvideothumb." as pvt WHERE pvt.productfileid=pf.id),'') as videothumb");
		$this->readdb->from(tbl_productfile." as pf");
		$this->readdb->where("pf.productid=".$productid);
		if($where!=''){
			$this->readdb->where($where);
		}			
		if($limit!=0){
			$this->readdb->limit($limit);
		}			
		$query = $this->readdb->get();
		return $query->result_array();			
	}*/

	function getProductvariant($id){
//SELECT p.id,p.name,p.description,a.variantname,v.value FROM product as p LEFT JOIN productvariant as pv ON p.id= pv.productid LEFT JOIN attribute as a ON a.id=pv.attributeid LEFT JOIN variant as v on v.id=pv.variantid WHERE p.id = pv.productid AND p.id=165

		$query=$this->readdb->select("pv.id,a.variantname,v.value,pv.price")
					    ->from($this->_table." as p")
						->from(tbl_productvariant." as pv","p.id= pv.productid","left")
						->Join(tbl_attribute." as a","a.id=pv.attributeid","left")
						->join(tbl_variant." as v","v.id=pv.variantid","left")
						->where("p.id = pv.productid")
						->where("FIND_IN_SET(p.id,'".$id."')<>0")
						// ->where("p.id='".$id."'")
						->get()->result_array();
						if (!empty($query)) {
						return $query;
					} else {
						return array();
					}
	 		
	}

	function getattributevalue($id){

		$query=$this->readdb->select("pv.id,a.id,a.variantname")
					    ->from($this->_table." as p")
						->from(tbl_productvariant." as pv","p.id= pv.productid","left")
						->Join(tbl_attribute." as a","a.id=pv.attributeid","left")
						->where("p.id = pv.productid")
						->where("p.id='".$id."'")
						->group_by("a.id")
						->get()->result_array();

						if (!empty($query)) {
						return $query;
					} else {
						return array();
					}
				 		
	}

	function getvariantvalue($id,$productid){
//SELECT p.id,p.name,p.description,a.variantname,v.value FROM product as p LEFT JOIN productvariant as pv ON p.id= pv.productid LEFT JOIN attribute as a ON a.id=pv.attributeid LEFT JOIN variant as v on v.id=pv.variantid WHERE p.id = pv.productid AND p.id=165

		$query=$this->readdb->select("pv.id,pv.variantid,v.value,pv.price")
						->from($this->_table." as p")
						->from(tbl_productvariant." as pv","p.id= pv.productid","left")
						->join(tbl_variant." as v", "v.id=pv.variantid","left")
						->where("p.id = pv.productid")
						->where("pv.attributeid='".$id."'")
						->where("p.id='".$productid."'")
						->get()->result_array();
					 
						if (!empty($query)) {
						return $query;
					} else {
						return array();
					}
	 		
	}

	function getvariantprice($variantid,$attributeid,$productid){
//SELECT p.id,p.name,p.description,a.variantname,v.value FROM product as p LEFT JOIN productvariant as pv ON p.id= pv.productid LEFT JOIN attribute as a ON a.id=pv.attributeid LEFT JOIN variant as v on v.id=pv.variantid WHERE p.id = pv.productid AND p.id=165

		$query=$this->readdb->select("pv.price")
						->from($this->_table." as p")
						->from(tbl_productvariant." as pv","p.id= pv.productid","left")
						->where("pv.variantid='".$variantid."'")
						->where("pv.attributeid='".$attributeid."'")
						->where("p.id='".$productid."'")
						->get()->row_array();
					
						if (!empty($query)) {
						return $query;
					} else {
						return array();
					}
	 		
	}
    function getsubcategoryrecord($counter, $id, $search)
    {
        $limit=10;
        $this->readdb->select('id,name,image');
        $this->readdb->from($this->_table);
        $this->readdb->where('maincategoryid="'.$id.'" AND status = 1');
        $this->readdb->where("(name LIKE CONCAT('%','$search','%'))");
        $this->readdb->limit($limit, $counter);
        $query = $this->readdb->get();
        if ($query->num_rows() == 0) {
            return array();
        } else {
            $Data = $query->result_array();
            $json = array();
            foreach ($Data as $row) {
                $json[] = $row;
            }
            return $json;
        }
    }


	function getdashboardproduct($memberid=0) {

		if($memberid!=0){
			$select = 'p.id as productid,p.name as productname,p.description as description,price,IF('.GSTBILL.'=1,tax,0)as tax,(SELECT hsncode FROM '.tbl_hsncode.' WHERE id=hsncodeid)as hsncode,
			(select universalprice from '.tbl_memberproduct.' where productid=p.id and memberid='.$memberid.' limit 1)as memberprice,
			ps.name as productsectionname,displaytype,IF('.PRODUCTDISCOUNT.'=1,discount,0)as discount,productsectionid';
		}else{
			$select = 'p.id as productid,p.name as productname,p.description as description,price,IF('.GSTBILL.'=1,tax,0)as tax,(SELECT hsncode FROM '.tbl_hsncode.' WHERE id=hsncodeid)as hsncode,ps.name as productsectionname,displaytype,IF('.PRODUCTDISCOUNT.'=1,discount,0)as discount,productsectionid';
		}
		if($memberid!=0){
            $where = 'p.id in(select productid from '.tbl_memberproduct.' where memberid='.$this->readdb->escape($memberid).') and (p.price > 0 or (select count(id) from '.tbl_productcombination.' where id in (select id from '.tbl_productprices.' where productid=p.id))>0) and p.status=1';
        }else{
			$where = '(p.price > 0 or (select count(id) from '.tbl_productcombination.' where priceid in (select id from '.tbl_productprices.' where productid=p.id))>0) and p.status=1'; 
        }
		//$applicationproductsection = explode(",",APPLICATIONPRODUCTSECTION);
		
		// print_r($applicationproductsection);exit;

		//if(APPLICATIONPRODUCTSECTION!=""){
			$this->load->model("Product_section_model","Product_section");
			//$this->Productsection->_where = "id in(".APPLICATIONPRODUCTSECTION.")";
			$this->Product_section->_fields = "id,maxhomeproduct";
			$productsection = $this->Product_section->getRecordByID();
			// print_r($productsection);exit;
			$queryarr = array();
			foreach($productsection as $ps){
				$queryarr[] = '(SELECT '.$select.'
				FROM '.$this->_table.' as `p`
				JOIN '.tbl_productsectionmapping.' as `pm` ON `pm`.`productid`=`p`.`id`
				JOIN '.tbl_productsection.' as `ps` ON `pm`.`productsectionid`=`ps`.`id` and `ps`.`status`=1
				WHERE `ps`.`id`='.$ps['id'].'
				AND '.$where.'
				ORDER BY `ps`.`priority` asc, `p`.`priority` asc
				LIMIT '.$ps['maxhomeproduct'].')';
			}

			if(count($queryarr)>0){
				$query=$this->readdb->query(implode(" UNION ",$queryarr));
				// echo $query;exit;
			}else{
				return array();exit;
			}

		if($query->num_rows() == 0){
			return array(); 
		} 
		 else {	
			$section_arr=array();
		 	$Data =$query->result_array();
			// print_r($Data);exit;
		 	$json=array();
		 	
		   foreach ($Data as $row) {
		         $variantarray=array();

				$ProductFiles = $this->getProductFiles($row['productid']);
			 
				$image = array();
				$values_arr=array();
				foreach ($ProductFiles as $filerow) {
					if($filerow['type']==1){
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['filename']);
					}
				 }

				            $categoryfinal = array();
				            if($memberid!=0){
				            	$this->readdb->select('id,price,(select price from '.tbl_membervariantprices.' where priceid='.tbl_productprices.'.id and memberid='.$memberid.' limit 1)as memberprice');	
							}else{
				            	$this->readdb->select('id,price');
				            }
					     	$this->readdb->from(tbl_productprices);
					     	$this->readdb->where(array("productid"=>$row['productid']));
					     	$pricedata = $this->readdb->get()->result_array();
					     	$all_varianrids=array();
					     	// print_r($pricedata);
					     	$price_arr=array();
					     	$variantsarr=array();
					     	foreach ($pricedata as $pd) {
					     		
					     		$this->readdb->select('variantid,variantname,value');
						     	$this->readdb->from(tbl_productcombination." as pc");
						     	$this->readdb->join(tbl_variant." as v","v.id=pc.variantid");
						     	$this->readdb->join(tbl_attribute." as a","a.id=v.attributeid");
						     	$this->readdb->where(array("priceid"=>$pd['id']));
						     	$variantbyprice = $this->readdb->get()->result_array();
						     	$variantids=array();
						     	foreach($variantbyprice as $k=>$vp) {
						     		$variantids[]=$vp['variantid'];
						     		$all_varianrids[]=$vp['variantid'];
						     		$variantsarr[$k]["variantname"]=$vp["variantname"];
						     		if(!in_array($vp["value"],$values_arr)){
						     			$variantsarr[$k]["value"][]=array("id"=>$vp["variantid"],"optionvalue"=>$vp["value"]);
						     		}
						     		$values_arr[]=$vp["value"];
						     	}

						     	if($memberid!=0){
						     	 	if(!is_null($pd['memberprice'])){
						     	 		$pd['price']=$pd['memberprice'];
						     	 	}
						     	 }

						     	$price_arr[]=$pd['price'];
					     		$variantarray[]=array("price"=>$pd['price'],"variantid"=>implode(",",$variantids));
					     	}	
					     	
					     	if(count($variantarray)>0){
					     		$price = min($price_arr)."-".max($price_arr);
					     	}else{
					     		$price=$row['price'];
					     		if($memberid!=0){
						     	 	if(!is_null($row['memberprice'])){
						     	 		$price=$row['memberprice'];
						     	 	}
						     	 }
							 }
							 if(count($image)==0){
								$image[]=array("type"=>"1","file"=>PRODUCTDEFAULTIMAGE);
							 }
							 $productwholearr=array();

							 $productarr = array("id"=>$row['productid'],
											"productname"=>$row['productname'],
											 "tax"=>$row['tax'],
											 "discount"=>$row['discount'],
											 "price"=>$price,
											 "hsncode"=>$row['hsncode'],
											"image"=>$image,
											"description"=>$row['description'],
											"variantdata" =>$variantsarr,
											"variantprice" =>$variantarray,			
										);
							 
							 if(!in_array($row['productsectionname'],$section_arr)){
								 $section_arr[]=$row['productsectionname'];
								 $productwholearr['section']=$row['productsectionname'];
								 if($row['displaytype']==0){
									 $productwholearr['displaytype']='grid';
								 }else{
									$productwholearr['displaytype']='slider';
								 }
								 $productwholearr['sectionid']=$row['productsectionid'];
								 
								 $productwholearr['product'][] = $productarr;
								
								$json[]=$productwholearr;
							 }else{
								$key = array_search($row['productsectionname'], $section_arr);
								$json[$key]['product'][]=$productarr;
							 }
			
			}

			return $json;
		}
	}
	function getProductList() {
	   
		$query = $this->readdb->select('id, name')
							
						->from($this->_table.' as p')
						->where("status",1)
						->order_by('name ASC')
						->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	

}
?>