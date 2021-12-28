<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends Common_model {
	public $_table = tbl_product;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = tbl_product.'.id DESC';
	public $_datatableorder = array('p.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'p.name','categoryname','brandname','price',null,'discount','p.priority');

	//set column field database for datatable searchable 
	public $column_search = array('p.name','(IFNULL((select name from '.tbl_productcategory.' where id=categoryid),"-"))','((select name from '.tbl_brand.' where id=p.brandid))','price','discount','p.priority');

	function __construct() {
		parent::__construct();
	}

	function getProductBySKU($sku){
	
		$query = $this->readdb->select('p.id,p.name,pp.id as priceid,pp.price,
		IFNULL((SELECT integratedtax FROM '.tbl_hsncode.' WHERE id=p.hsncodeid),0) as tax')
							->from(tbl_productprices." as pp")
							->join($this->_table." as p","pp.productid=p.id","INNER")
                            ->where("pp.sku='".$sku."' AND p.producttype=0 AND p.status=1")
							->get();
							
        return $query->row_array();
	}

	function getProductCount($MEMBERID=0,$CHANNELID=0){

		$query = $this->readdb->select("p.id")
						->from($this->_table." as p")
						->where("p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'")
						->get();

		if($query->num_rows() > 0){
			return $query->num_rows();
		}else{
			return 0; 
		}
	}
	
	public function getproductcountdashboard($where=array())
	{
		// var_dump($where);exit;
		$this->db->from($this->_table." as p");
		$this->db->where($where);
		$this->db->get();
		// echo($this->db->last_query());exit;
		return $this->db->count_all_results();
	}

	function getAllProductByCategoryID($categoryid) {
		
		$query = $this->readdb->select("p.id,p.name,p.isuniversal,p.discount,
		IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,
		IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=hsncodeid),0) as tax")
							->from($this->_table." as p")
							->where("(p.categoryid=".$categoryid." OR ".$categoryid."=0) AND p.status=1")
							->order_by("p.name ASC")
							->get();
		
		return $query->result_array();
	}
	function getProductByCategory($productcategoryid) {
		
		$query = $this->readdb->select("p.id,p.name,p.isuniversal,p.discount,
		IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,
		IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=hsncodeid),0) as tax")
							->from($this->_table." as p")
							->where("p.categoryid=".$productcategoryid." AND p.status=1 AND p.producttype=0")
							->order_by("p.name ASC")
							->get();
		
		return $query->result_array();
	}
	function searchProductByString($search,$channelid=0,$memberid=0){

		$this->readdb->select("p.name");
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		if($channelid==0 || $memberid==0){
			$this->readdb->join(tbl_productbasicpricemapping." as pbp","pbp.productid=p.id AND pbp.channelid = '".GUESTCHANNELID."' AND pbp.salesprice >0 AND pbp.allowproduct = 1","INNER");
			$this->readdb->where("p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.memberid=0 AND p.channelid=0");
		}else{
			$this->readdb->where("p.status=1 AND p.producttype=0 AND p.memberid='".$memberid."' AND p.channelid='".$channelid."' AND IFNULL((SELECT count(id) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)>0");
		}
		
		if(!empty($search)){
			$this->readdb->where("(p.name LIKE '%".$search."%' OR pc.name LIKE '%".$search."%')");
		}
		if($channelid==0 || $memberid==0){
			$this->readdb->group_by("pbp.productid");
		}
		$this->readdb->order_by("p.name","ASC");
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return 0;
		}	
	}
	function getPriceDetails(){
		$Cartproduct = $viewcartproducts = $totalpricearray = array();
		$arrSessionDetails = $this->session->userdata;
		if(isset($arrSessionDetails[base_url().'MEMBER_ID'])){
            $this->load->model('Cart_model', 'Cart');
            $Cartproduct = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'MEMBER_ID']);
        }
        
        if(isset($arrSessionDetails[base_url().'PRODUCT']) && !empty($arrSessionDetails[base_url().'PRODUCT'])){
            $this->load->model('Product_model', 'Product');
            $viewcartproducts = $this->Product->getCartProductBysession($arrSessionDetails);
        }else{
            if(!empty($Cartproduct)){
                $this->load->model('Product_model', 'Product');
                $viewcartproducts = $this->Product->getCartProduct();
            }
        }

		if(!empty($viewcartproducts)){
			$subtotal = $taxamount = $coupondiscount = $productdiscount = $netamount = $redeemamount = $redeempoint = $redeemrate = 0;
			$couponcode = $couponcodeid = "";
			foreach($viewcartproducts as $product){

				if ($product['discount'] != '' && $product['discount'] != 0) {
					$price = ($product['price'] - ($product['price'] * $product['discount'] / 100));
					$productdiscount = $productdiscount + (($product['price'] * $product['discount'] / 100) * $product['quantity']);
				} else {
					$price = $product['price'];
				}
				if(PRICE==1){
					$tax = ($price * $product['quantity'])*$product['tax']/100;
					$subtotal = $subtotal + ($price * $product['quantity']);
					$taxamount = $taxamount + $tax;
				}else{
					$tax = ($price * $product['quantity'])*$product['tax']/(100+$product['tax']);
					$subtotal = $subtotal + ($price * $product['quantity']) - $tax;
					$taxamount = $taxamount + $tax;
				}
			}
			if(isset($arrSessionDetails[base_url().'COUPON']) && !empty($arrSessionDetails[base_url().'COUPON'])){
				$coupondiscount = $arrSessionDetails[base_url().'COUPON']['discountedamount'];
				$couponcode = $arrSessionDetails[base_url().'COUPON']['vouchercode'];
				$couponcodeid = $arrSessionDetails[base_url().'COUPON']['vouchercodeid'];
			}
			if(isset($arrSessionDetails[base_url().'REDEEMPOINT']) && !empty($arrSessionDetails[base_url().'REDEEMPOINT'])){
				$redeemamount = $arrSessionDetails[base_url().'REDEEMPOINT'] * $arrSessionDetails[base_url().'REDEEMRATE'];
				$redeempoint = $arrSessionDetails[base_url().'REDEEMPOINT'];
				$redeemrate = $arrSessionDetails[base_url().'REDEEMRATE'];
			}
			if(($subtotal + $taxamount) < ($coupondiscount + $redeemamount)){
				$totaldiscount = $productdiscount + $subtotal + $taxamount;
			}else{
				$totaldiscount = $productdiscount + $coupondiscount + $redeemamount;
			}
			$netamount = $subtotal + $taxamount - $coupondiscount -$redeemamount;
			if($netamount < 0){
				$netamount = 0;
			}
			$totalpricearray = array("subtotal"=>$subtotal,
									 "taxamount"=>$taxamount,
									 "productdiscount"=>$productdiscount,
									 "coupondiscount"=>$coupondiscount,
									 "redeempoint"=>$redeempoint,
									 "redeemrate"=>$redeemrate,
									 "redeemamount"=>$redeemamount,
									 "totaldiscount"=>$totaldiscount,
									 "couponcode"=>$couponcode,
									 "couponcodeid"=>$couponcodeid,
									 "netamount"=>$netamount,
									 "cartcount"=>count($viewcartproducts)
									);
		}
		return $totalpricearray;
	}
	function getMemberWebsitePriceDetails(){
		$Cartproduct = $viewcartproducts = $totalpricearray = array();
		$arrSessionDetails = $this->session->userdata;
		$sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
        $sellerchannelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];

		if(isset($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
            $this->load->model('Cart_model', 'Cart');
            $Cartproduct = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'],$sellermemberid,"useformemberwebsite");
        }
        
        if(isset($arrSessionDetails[base_url().'MEMBERPRODUCT']) && !empty($arrSessionDetails[base_url().'MEMBERPRODUCT'])){
            $this->load->model('Product_model', 'Product');
            $viewcartproducts = $this->Product->getCartProductBysession($arrSessionDetails,$sellerchannelid,$sellermemberid);
        }else{
            if(!empty($Cartproduct)){
                $this->load->model('Product_model', 'Product');
                $viewcartproducts = $this->Product->getMemberWebsiteCartProduct();
            }
        }

		if(!empty($viewcartproducts)){
			$subtotal = $taxamount = $coupondiscount = $productdiscount = $netamount = $redeemamount = $redeempoint = $redeemrate = 0;
			$couponcode = $couponcodeid = "";
			foreach($viewcartproducts as $product){

				if ($product['discount'] != '' && $product['discount'] != 0) {
					$price = ($product['price'] - ($product['price'] * $product['discount'] / 100));
					$productdiscount = $productdiscount + (($product['price'] * $product['discount'] / 100) * $product['quantity']);
				} else {
					$price = $product['price'];
				}
				if(PRICE==1){
					$tax = ($price * $product['quantity'])*$product['tax']/100;
					$subtotal = $subtotal + ($price * $product['quantity']);
					$taxamount = $taxamount + $tax;
				}else{
					$tax = ($price * $product['quantity'])*$product['tax']/(100+$product['tax']);
					$subtotal = $subtotal + ($price * $product['quantity']) - $tax;
					$taxamount = $taxamount + $tax;
				}
			}
			if(isset($arrSessionDetails[base_url().'MEMBERCOUPON']) && !empty($arrSessionDetails[base_url().'MEMBERCOUPON'])){
				$coupondiscount = $arrSessionDetails[base_url().'MEMBERCOUPON']['discountedamount'];
				$couponcode = $arrSessionDetails[base_url().'MEMBERCOUPON']['vouchercode'];
				$couponcodeid = $arrSessionDetails[base_url().'MEMBERCOUPON']['vouchercodeid'];
			}
			if(isset($arrSessionDetails[base_url().'MEMBERREDEEMPOINT']) && !empty($arrSessionDetails[base_url().'MEMBERREDEEMPOINT'])){
				$redeemamount = $arrSessionDetails[base_url().'MEMBERREDEEMPOINT'] * $arrSessionDetails[base_url().'MEMBERREDEEMRATE'];
				$redeempoint = $arrSessionDetails[base_url().'MEMBERREDEEMPOINT'];
				$redeemrate = $arrSessionDetails[base_url().'MEMBERREDEEMRATE'];
			}
			if(($subtotal + $taxamount) < ($coupondiscount + $redeemamount)){
				$totaldiscount = $productdiscount + $subtotal + $taxamount;
			}else{
				$totaldiscount = $productdiscount + $coupondiscount + $redeemamount;
			}
			$netamount = $subtotal + $taxamount - $coupondiscount -$redeemamount;
			if($netamount < 0){
				$netamount = 0;
			}
			$totalpricearray = array("subtotal"=>$subtotal,
									 "taxamount"=>$taxamount,
									 "productdiscount"=>$productdiscount,
									 "coupondiscount"=>$coupondiscount,
									 "redeempoint"=>$redeempoint,
									 "redeemrate"=>$redeemrate,
									 "redeemamount"=>$redeemamount,
									 "totaldiscount"=>$totaldiscount,
									 "couponcode"=>$couponcode,
									 "couponcodeid"=>$couponcodeid,
									 "netamount"=>$netamount,
									 "cartcount"=>count($viewcartproducts)
									);
		}
		return $totalpricearray;
	}
	function getCodWeight($memberid){

		$this->readdb->select("pp.weight");
		$this->readdb->from(tbl_cart." as c");
		$this->readdb->join(tbl_productprices." as pp","c.productid=pp.productid","INNER");
		$this->readdb->where("memberid=".$memberid);
		$data = $this->readdb->get()->result_array();

		$returndata = array();
		$totalweight = 0;
		$cod = 1;
		foreach ($data as $row){
		
				$totalweight = $totalweight + $row['weight'];
				if($row['weight']=='0.000'){
					$cod  = 0;
					
				}
			
		}
		$returndata['iscod'] = $cod;
		$returndata['totalweight'] = $totalweight;
		return $returndata;
	}
	function getProductReviews($limit,$offset=0,$filterarray,$type='data',$channelid=0,$memberid=0){

		$filterarray = json_decode(html_entity_decode($filterarray),true);
		
		$this->readdb->select("IF(pr.memberid!=0,m.name,prg.name) as membername,
							pr.rating,message,pr.createddate,pr.memberid");

		$this->readdb->from(tbl_productreview." as pr");
		$this->readdb->join(tbl_member." as m","m.id=pr.memberid AND m.status=1","LEFT");
		$this->readdb->join(tbl_productreviewbyguest." as prg","prg.productreviewid=pr.id","LEFT");
		$this->readdb->where("pr.type=1 AND pr.productid='".$filterarray['productid']."'");
		$this->readdb->where("pr.sellermemberid='".$memberid."' AND pr.channelid='".$channelid."'");
		$this->readdb->order_by('pr.createddate DESC');
		
		if($type=='data'){
			$this->readdb->limit($limit,$offset);
		}
		$query = $this->readdb->get();
		
		if($type=='data'){
			return $query->result_array();			
		}else{
			return $query->num_rows();
		}
	}
	function checkProductIsAssignToMember($memberid,$sellermemberid,$productid,$priceid){


		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;

		$query = $this->readdb->select("p.id")
					  	->from($this->_table." as p")
						->join(tbl_productprices." as pp","pp.id = '".$priceid."'","INNER")
						->where("p.id = '".$productid."' AND p.status=1 AND p.producttype=0 AND
						
							IF(".$memberspecificproduct."=1,
								IF((IFNULL((SELECT count(*) FROM ".tbl_memberproduct." WHERE sellermemberid='".$sellermemberid."' AND memberid='".$memberid."'),0) > 0 OR IFNULL((SELECT count(*) FROM ".tbl_membervariantprices." WHERE sellermemberid='".$sellermemberid."' AND memberid='".$memberid."'),0) > 0),

									IFNULL((SELECT count(id) FROM ".tbl_memberproduct." WHERE sellermemberid='".$sellermemberid."' AND memberid='".$memberid."' AND productid='".$productid."' UNION SELECT count(id) FROM ".tbl_membervariantprices." WHERE sellermemberid='".$sellermemberid."' AND memberid='".$memberid."' AND priceid='".$priceid."'),0) > 0 ,

									IFNULL((SELECT count(id) FROM ".tbl_productbasicpricemapping." WHERE channelid='".$channelid."' AND productid='".$productid."' AND productpriceid='".$priceid."' AND salesprice >0 AND allowproduct=1),0) > 0
								),
								IFNULL((SELECT count(id) FROM ".tbl_productbasicpricemapping." WHERE channelid='".$channelid."' AND productid='".$productid."' AND productpriceid='".$priceid."' AND salesprice >0 AND allowproduct=1),0) > 0
							)
						")

						->get();
			
		
		if($query->num_rows() > 0) {
			return $query->num_rows();
		} else {
			return 0;
		}
	}
	function getCartProduct(){
		$productdata = $temp = array();
		
		$arrSessionDetails = $this->session->userdata;
		//$this->session->unset_userdata(base_url().'PRODUCT');
        if(isset($arrSessionDetails[base_url().'PRODUCT']) && !empty($arrSessionDetails[base_url().'PRODUCT'])){

        	if(isset($arrSessionDetails[base_url().'MEMBER_ID'])){

        		$this->load->model('Cart_model', 'Cart');
        		$CartData = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'MEMBER_ID']);
        		
        		if(!empty($CartData)){
        			$Product = json_decode($arrSessionDetails[base_url().'PRODUCT'],true);
        			
        			for ($j=0; $j < count($CartData); $j++) { 
        				$count = 0;
        				for ($i=0; $i < count($Product); $i++) { 
        					if($Product[$i]['productid']!=$CartData[$j]['productid'] && $Product[$i]['productpriceid']!=$CartData[$j]['productpriceid']){
        						$count++;
			                }
        				}
        				if($count==count($Product)){
        					$temp[] = $CartData[$j];	
        				}
			            
		            }
		            $Product = array_merge($Product,$temp);
		            //print_r($Product);exit;
        			$productdata = array(base_url().'PRODUCT' => json_encode($Product));
            		$this->session->set_userdata($productdata);

        		}
        	}
        	$arrSessionDetails = $this->session->userdata;
        	$productdata = $this->getCartProductBysession($arrSessionDetails);
			
        }else{
        	if(isset($arrSessionDetails[base_url().'MEMBER_ID'])){
        		$this->load->model('Cart_model', 'Cart');
        		$CartData = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'MEMBER_ID']);
        		
        		if(!empty($CartData)){
        			$productdata = array(base_url().'PRODUCT' => json_encode($CartData));
            		$this->session->set_userdata($productdata);

            		$arrSessionDetails = $this->session->userdata;
        			$productdata = $this->getCartProductBysession($arrSessionDetails);

        		}
        	}
		}
		
		return $productdata;
	}
	function getMemberWebsiteCartProduct(){
		$productdata = $temp = array();
		
		$arrSessionDetails = $this->session->userdata;
		$sellermemberid = $this->session->userdata[base_url().'WEBSITEMEMBERID'];
		$sellerchannelid = $this->session->userdata[base_url().'WEBSITECHANNELID'];
		
		//$this->session->unset_userdata(base_url().'MEMBERPRODUCT');
        if(isset($arrSessionDetails[base_url().'MEMBERPRODUCT']) && !empty($arrSessionDetails[base_url().'MEMBERPRODUCT'])){

        	if(isset($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){

        		$this->load->model('Cart_model', 'Cart');
        		$CartData = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'],$sellermemberid,"useformemberwebsite");
        		
        		if(!empty($CartData)){
        			$Product = json_decode($arrSessionDetails[base_url().'MEMBERPRODUCT'],true);
        			
        			for ($j=0; $j < count($CartData); $j++) { 
        				$count = 0;
        				for ($i=0; $i < count($Product); $i++) { 
        					if($Product[$i]['productid']!=$CartData[$j]['productid'] && $Product[$i]['productpriceid']!=$CartData[$j]['productpriceid']){
        						$count++;
			                }
        				}
        				if($count==count($Product)){
        					$temp[] = $CartData[$j];	
        				}
			            
		            }
		            $Product = array_merge($Product,$temp);
		            //print_r($Product);exit;
        			$productdata = array(base_url().'MEMBERPRODUCT' => json_encode($Product));
            		$this->session->set_userdata($productdata);

        		}
        	}
        	$arrSessionDetails = $this->session->userdata;
        	$productdata = $this->getCartProductBysession($arrSessionDetails,$sellerchannelid,$sellermemberid);
			
        }else{
        	if(isset($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'])){
        		$this->load->model('Cart_model', 'Cart');
        		$CartData = $this->Cart->getCustomerCartProducts($arrSessionDetails[base_url().'WEBSITE_MEMBER_ID'],$sellermemberid,"useformemberwebsite");
        		
        		if(!empty($CartData)){
        			$productdata = array(base_url().'MEMBERPRODUCT' => json_encode($CartData));
            		$this->session->set_userdata($productdata);

            		$arrSessionDetails = $this->session->userdata;
        			$productdata = $this->getCartProductBysession($arrSessionDetails,$sellerchannelid,$sellermemberid);

        		}
        	}
		}
		
		return $productdata;
	}
	function getCartProductBysession($arrSessionDetails,$sellerchannelid=0,$sellermemberid=0){
		$productdata = array();
		$this->load->model('Stock_report_model', 'Stock');
		$this->load->model('Product_prices_model', 'Product_prices');
		if($sellerchannelid==0 && $sellermemberid==0){
			$snProductArray = isset($arrSessionDetails[base_url().'PRODUCT'])?$arrSessionDetails[base_url().'PRODUCT']:'';
			$pricechannelid = GUESTCHANNELID;
			if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
				$pricechannelid = CUSTOMERCHANNELID;
			}
		}else{
			$snProductArray = isset($arrSessionDetails[base_url().'MEMBERPRODUCT'])?$arrSessionDetails[base_url().'MEMBERPRODUCT']:'';
		}
		
		if(!empty($snProductArray)){
			$Product = json_decode($snProductArray,true);

			foreach ($Product as $row) {
				if($sellerchannelid==0 && $sellermemberid==0){
					$sel_price = "IFNULL((SELECT pbqp.salesprice FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.id='".$row['referenceid']."'),0) as price,

					IFNULL((SELECT pbqp.discount FROM ".tbl_productbasicquantityprice." as pbqp 
						WHERE pbqp.id='".$row['referenceid']."'),0) as discount,
					
					IFNULL((SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid=pp.id AND channelid=".$pricechannelid." AND allowproduct=1 LIMIT 1),0) as minimumorderqty,
					
					IFNULL((SELECT maximumqty FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid=pp.id AND channelid=".$pricechannelid." AND allowproduct=1 LIMIT 1),0) as maximumorderqty";
				}else{
					$sel_price = "IFNULL((SELECT pqp.price FROM ".tbl_productquantityprices." as pqp WHERE pqp.id=".$row['referenceid']."),0) as price,

					IFNULL((SELECT pqp.discount FROM ".tbl_productquantityprices." as pqp 
						WHERE pqp.id=".$row['referenceid']."),0) as discount,
					pp.minimumorderqty,pp.maximumorderqty";
				}

				$this->readdb->select("p.id,p.name,p.slug,
					".$sel_price."
					,'".$row['quantity']."' as quantity,pp.id as productpriceid,p.isuniversal,
					IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'') as variantname,
					IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,
					(SELECT name from ".tbl_productcategory." WHERE id=p.categoryid) as category,
					(SELECT slug from ".tbl_productcategory." WHERE id=p.categoryid) as categoryslug,
					IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0) as tax,
					pp.pricetype,p.quantitytype
					
				");
				$this->readdb->from($this->_table." as p");
				$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id AND pp.id=".$row['productpriceid'],"INNER");
				
				$this->readdb->where("p.status=1 AND p.producttype=0 AND p.id=".$row['productid']." AND p.productdisplayonfront=1 AND p.channelid=".$sellerchannelid." AND p.memberid=".$sellermemberid);
				$query = $this->readdb->get();		
				
				if($query->num_rows()>0){
					$rowdata = $query->row_array();
					if(PRODUCTDISCOUNT==1){
						/* if(isset($arrSessionDetails[base_url().'COUPON']) && $arrSessionDetails[base_url().'COUPON']==1){
							$productdiscount = 0;
						}else{
						} */
						$productdiscount = $rowdata['discount'];
					}else{
						$productdiscount = 0;
					}
					$rowdata['discount'] = $productdiscount;
					if(STOCKMANAGEMENT==1){
						if($rowdata['isuniversal']==1){
							$productstock = $this->Stock->getAdminProductStock($rowdata['id'],0,'','',0,$sellermemberid,$sellerchannelid);
							$rowdata['productstock'] = (!empty($productstock)?$productstock[0]['openingstock']:0);
						}else{
							$stock = 0;
							$productstock = $this->Stock->getAdminProductStock($rowdata['id'],1,'','',0,$sellermemberid,$sellerchannelid);
							if(!empty($productstock)){
								$key = array_search($rowdata['productpriceid'], array_column($productstock, 'priceid'));
								if($productstock[$key]['openingstock']>0 && STOCKMANAGEMENT==1){
									$stock = $productstock[$key]['openingstock'];
								}
							}
							$rowdata['productstock'] = $stock;
						}
					}else{
						$rowdata['productstock'] = 0;
					}

					if($row['referencetype']==1){
						$multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($pricechannelid,$row['productpriceid'],$row['productid']);
					}else{
						$multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($row['productpriceid']);
					}
					$rowdata['referencetype'] = $row['referencetype'];
					$rowdata['referenceid'] = $row['referenceid'];
					$rowdata['multipleprice'] = $multipleprice;

					$productdata[] = $rowdata;
				}
        	}
		}
		
        return $productdata;
	}
	function getProductDataByID($ID){
		$query = $this->readdb->select("p.id,p.name,p.importerProductName,p.supplierProductName,p.installationcost,p.shortdescription,p.description,p.slug,p.isuniversal,p.hsncodeid,
									p.metatitle,p.metakeyword,p.metadescription,p.status,
									p.categoryid,p.priority,p.pointsforseller,p.pointsforbuyer,p.producttype,p.brandid,p.pointspriority,p.catalogfile,p.commingsoon,
									p.returnpolicytitle,p.returnpolicydescription,p.replacementpolicytitle,p.replacementpolicydescription,p.productdisplayonfront,p.quantitytype,
									(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid) as category,
									IFNULL((SELECT GROUP_CONCAT(tagid) FROM ".tbl_producttagmapping." WHERE productid=p.id),'') as tagid,
									p.createddate,
									(SELECT hsncode FROM ".tbl_hsncode." WHERE id=p.hsncodeid) as hsncode,
									IFNULL((SELECT name FROM ".tbl_brand." WHERE id=p.brandid),'') as brandname,
									IFNULL((SELECT name FROM ".tbl_productunit." WHERE id IN (SELECT unitid FROM ".tbl_productprices." WHERE productid = p.id GROUP BY unitid)),'') as unitname,
									(SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid) as tax,
									@priceid:=IF(p.isuniversal=1,(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as priceid,
									IF(p.isuniversal=1,(SELECT sku FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as sku,
									IF(p.isuniversal=1,(SELECT weight FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as weight,
									IF(p.isuniversal=1,(SELECT barcode FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as barcode,
									IFNULL((SELECT GROUP_CONCAT(tag SEPARATOR ', ') FROM ".tbl_producttag." WHERE id IN (SELECT tagid FROM ".tbl_producttagmapping." WHERE productid=p.id)),'') as tagsname,

									IF(p.isuniversal=1,(SELECT minimumorderqty FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as minimumorderqty,
									IF(p.isuniversal=1,(SELECT maximumorderqty FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as maximumorderqty,

									IF(p.isuniversal=1,(SELECT minimumstocklimit FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as minimumstocklimit,

									IF(p.isuniversal=1,(SELECT minimumsalesprice FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as minimumsalesprice,

									@pricetype:=IF(p.isuniversal=1,(SELECT pricetype FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as pricetype,

									IF(p.isuniversal=1,
										IF(IFNULL((SELECT count(id) FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid=(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1) GROUP BY productpriceid),0)>0,0,1)
									,'') as addpriceinpricelist,

									IF(p.isuniversal=1 AND @pricetype=0,IFNULL((SELECT discount FROM ".tbl_productquantityprices." WHERE productpricesid=@priceid),0),'') as discount
								")
							->from($this->_table." as p")
							->where("p.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function searchproduct($type,$search){

		$this->readdb->select("id,name as text");
		$this->readdb->from($this->_table);
		$this->readdb->where("status=1");
		if($type==1){
			$this->readdb->where("name LIKE '%".$search."%'");
		}else{
			$this->readdb->where("FIND_IN_SET(id,'".$search."')>0");
		}
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return 0;
		}	
	}
	function getProductDetailsBySlug($productslug,$channelid=0,$memberid=0){
		
		$pricechannelid = GUESTCHANNELID;
		if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
			$pricechannelid = CUSTOMERCHANNELID;
		}
		
		if($channelid==0 && $memberid==0){
			$select_price = "
			
				@price:=IF(p.isuniversal=1,
								IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp
								INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0 
								WHERE pbp.productid=p.id AND pbp.channelid=".$pricechannelid." AND pbp.allowproduct=1 LIMIT 1),0)
						,0) as price,

				IF(p.isuniversal=1,
					(SELECT IFNULL((SELECT minimumqty FROM ".tbl_productbasicpricemapping." as pbp 
						WHERE pbp.productid=p.id AND pbp.channelid=".$pricechannelid." AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0) FROM ".tbl_productprices." as pp WHERE pp.productid=p.id LIMIT 1),'') as minimumorderqty,

				IF(p.isuniversal=1,
					(SELECT IFNULL((SELECT maximumqty FROM ".tbl_productbasicpricemapping." as pbp 
						WHERE pbp.productid=p.id AND pbp.channelid=".$pricechannelid." AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0) FROM ".tbl_productprices." as pp WHERE pp.productid=p.id LIMIT 1),'') as maximumorderqty,
						
				IF(p.isuniversal=1,
					(SELECT IFNULL((SELECT pbqp.discount FROM ".tbl_productbasicpricemapping." as pbp  
						INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
						WHERE pbp.productid=p.id AND pbp.channelid=".$pricechannelid." AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id LIMIT 1),0) FROM ".tbl_productprices." as pp WHERE pp.productid=p.id LIMIT 1),'') as discount
					";

			$select_price2 = "
			
				@price:=IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) as price,
				
				pbp.minimumqty as minimumorderqty,pbp.maximumqty as maximumorderqty,
				
				IFNULL((SELECT pbqp.discount FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0 AND pbqp.salesprice=@price LIMIT 1),0) as discount
			";
			
			$type="admin";
		}else{
			$select_price = "
					@price:=IF(p.isuniversal=1,
							IFNULL((SELECT min(pqp.price) FROM ".tbl_productprices." as pp
							INNER JOIN ".tbl_productquantityprices." as pqp ON pqp.productpricesid=pp.id AND pqp.price>0 
							WHERE pp.productid=p.id LIMIT 1),0)
					,0) as price,

					IF(p.isuniversal=1,
						(SELECT IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp 
						WHERE pbp.productid=p.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0) FROM ".tbl_productprices." as pp WHERE pp.productid=p.id LIMIT 1)
					,'') as minimumorderqty,

					IF(p.isuniversal=1,
						(SELECT IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp 
						WHERE pbp.productid=p.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0) FROM ".tbl_productprices." as pp WHERE pp.productid=p.id LIMIT 1)
					,'') as maximumorderqty,
					
					IF(p.isuniversal=1,
						(SELECT IFNULL((SELECT pbqp.discount FROM ".tbl_productbasicpricemapping." as pbp 
						INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
						WHERE pbp.productid=p.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id LIMIT 1),0) FROM ".tbl_productprices." as pp WHERE pp.productid=p.id LIMIT 1),'') as discount
					";
			$select_price2 = "
			
				@price:=IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp 
						WHERE pqp.productpricesid=pp.id AND pqp.price>0 LIMIT 1),0) as price,
			
				IFNULL((SELECT pqp.discount FROM ".tbl_productquantityprices." as pqp 
							WHERE pqp.productpricesid=pp.id AND pqp.price>0 AND pqp.price=@price LIMIT 1),0) as discount,
							
				IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp 
						WHERE pbp.productid=pp.productid AND pbp.channelid='".$channelid."' AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0) as minimumorderqty,
						
				IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp 
						WHERE pbp.productid=pp.productid AND pbp.channelid='".$channelid."' AND pbp.allowproduct=1 AND pbp.productpriceid=pp.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0) as maximumorderqty";

			$type="member";
		}
		$query = $this->readdb->select("p.id,(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid) as category,
		(SELECT slug FROM ".tbl_productcategory." WHERE id=p.categoryid) as categoryslug,
		p.name as productname,p.shortdescription,p.description,p.isuniversal,
		IF(p.isuniversal=1,(SELECT sku FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as sku,
		IF(p.isuniversal=1,(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as priceid,
		p.quantitytype,
		".$select_price.",
		IF(p.isuniversal=1,IFNULL((SELECT pricetype FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),0),0) as pricetype,
		p.metatitle,p.metadescription,p.metakeyword,
		
		IFNULL((SELECT ROUND(SUM(CASE
					WHEN rating='0.5' THEN IF(rating='0.5',1,0)*0.5
					WHEN rating='1.0' THEN IF(rating='1.0',1,0)*1.0 
					WHEN rating='1.5' THEN IF(rating='1.5',1,0)*1.5 
					WHEN rating='2.0' THEN IF(rating='2.0',1,0)*2.0 
					WHEN rating='2.5' THEN IF(rating='2.5',1,0)*2.5 
					WHEN rating='3.0' THEN IF(rating='3.0',1,0)*3.0 
					WHEN rating='3.5' THEN IF(rating='3.5',1,0)*3.5
					WHEN rating='4.0' THEN IF(rating='4.0',1,0)*4.0
					WHEN rating='4.5' THEN IF(rating='4.5',1,0)*4.5 
					WHEN rating='5.0' THEN IF(rating='5.0',1,0)*5.0 
				END)/COUNT(id),1) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as productreview,
		
		IFNULL((SELECT SUM(CASE
						WHEN rating='0.5' THEN IF(rating='0.5',1,0)*0.5
						WHEN rating='1.0' THEN IF(rating='1.0',1,0)*1.0 
						WHEN rating='1.5' THEN IF(rating='1.5',1,0)*1.5 
						WHEN rating='2.0' THEN IF(rating='2.0',1,0)*2.0 
						WHEN rating='2.5' THEN IF(rating='2.5',1,0)*2.5 
						WHEN rating='3.0' THEN IF(rating='3.0',1,0)*3.0 
						WHEN rating='3.5' THEN IF(rating='3.5',1,0)*3.5
						WHEN rating='4.0' THEN IF(rating='4.0',1,0)*4.0
						WHEN rating='4.5' THEN IF(rating='4.5',1,0)*4.5 
						WHEN rating='5.0' THEN IF(rating='5.0',1,0)*5.0 
					END) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as productratingcount,

		IFNULL((SELECT COUNT(id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as productreviewcount,

		IFNULL((SELECT COUNT(id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND rating='1.0' AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as onereviewcount,

		IFNULL((SELECT COUNT(id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND rating='2.0' AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as tworeviewcount,

		IFNULL((SELECT COUNT(id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND rating='3.0' AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as threereviewcount,

		IFNULL((SELECT COUNT(id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND rating='4.0' AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as fourreviewcount,

		IFNULL((SELECT COUNT(id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1 AND rating='5.0' AND pr.channelid=".$channelid." AND pr.sellermemberid=".$memberid."),0) as fivereviewcount,

		p.returnpolicytitle,p.returnpolicydescription,p.replacementpolicytitle,p.replacementpolicydescription
		")
							->from($this->_table." as p")
							->where("p.slug='".$productslug."' AND p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.channelid=".$channelid." AND p.memberid=".$memberid)
							->get();

		$data = $query->row_array();
		
		if(!empty($data)){
			$variantarray = array();

			$ProductFiles = $this->getProductFiles($data['id']);

			$this->load->model("Product_combination_model","Product_combination");
			$this->load->model("Stock_report_model","Stock");
			$this->load->model("Product_prices_model","Product_prices");
			
			$productdata = $this->Stock->getAdminProductStock($data['id'],0,'','',0,$memberid,$channelid);
			$data['stock'] = $productdata[0]['openingstock'];

			$productcombination = $this->Product_combination->getProductCombinationByProductIDOnFront($data['id'],$channelid,$memberid);
			$referencetype = "";
			$multipleprice = array();
			if($data['isuniversal']==0){
				
				$this->readdb->select('pp.id,'.$select_price2.',pp.sku,pp.pricetype');
				$this->readdb->from(tbl_productprices." as pp");
				
				if($channelid==0 && $memberid==0){
					$this->readdb->join(tbl_productbasicpricemapping." as pbp","pbp.productpriceid=pp.id AND channelid = '".$pricechannelid."' AND allowproduct = 1 AND pbp.productid=pp.productid","INNER");
					$this->readdb->where("IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0");
				}else{
					$this->readdb->where("IFNULL((SELECT count(pqp.id) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0) > 0");
				}
				$this->readdb->where("pp.productid=".$data['id']);
				$pricedata = $this->readdb->get()->result_array();
				 
				$variantarray=array();
				if(!empty($pricedata)){
					foreach ($pricedata as $pd) {

						$variantdata = $this->readdb->select('variantid,variantname,value')
													->from(tbl_productcombination." as pc")
													->join(tbl_variant." as v","v.id=pc.variantid")
													->join(tbl_attribute." as a","a.id=v.attributeid")
													->where(array("pc.priceid"=>$pd['id'],"v.memberid"=>0,"v.channelid"=>0))
													->order_by("pc.variantid ASC")
													->get()->result_array();
							
						$variantids = (!empty($variantdata)?array_column($variantdata, "variantid"):array());
						$ProductVariantStock = $this->Stock->getAdminProductStock($data['id'],1,'','',0,$memberid,$channelid);
						$stock = 0;
						if(!empty($ProductVariantStock)){
							$key = array_search($pd['id'], array_column($ProductVariantStock, 'priceid'));
							$price = $ProductVariantStock[$key]['price'];
							if($ProductVariantStock[$key]['openingstock']>0 && STOCKMANAGEMENT==1){
								$stock = $ProductVariantStock[$key]['openingstock'];
							}
						}
						$pricereferencetype = "";
						$multipleprices = array();
						if($channelid==0 && $memberid==0){
							$multipleprices = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($pricechannelid,$pd['id'],$data['id']);
							$pricereferencetype = "defaultproduct";
						}else{
							$multipleprices = $this->Product_prices->getProductQuantityPriceDataByPriceID($pd['id']);
							$pricereferencetype = "adminproduct";
						}
						$variantarray[]=array('combinationid'=>$pd['id'],'quantitytype'=>$data['quantitytype'],"pricetype"=>$pd['pricetype'],"referencetype"=>$pricereferencetype,"price"=>$pd['price'],"variantid"=>implode(",",$variantids),"stock"=>$stock,"sku"=>$pd['sku'],"minimumorderqty"=>$pd['minimumorderqty'],"maximumorderqty"=>$pd['maximumorderqty'],"discount"=>$pd['discount'],"multipleprice"=>$multipleprices);
					}
				}
			}else{
				if($channelid==0 && $memberid==0){
					$multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($pricechannelid,$data['priceid'],$data['id']);
					$referencetype = "defaultproduct";
				}else{
					$multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($data['priceid']);
					$referencetype = "adminproduct";
				}
			}
			$data['referencetype'] = $referencetype;
			$data['multipleprice'] = $multipleprice;

			$this->load->model("Product_tag_model","Product_tag");
			$producttagdata = $this->Product_tag->getProductTagsByProductId($data['id'],$channelid,$memberid);

			return array_merge($data,array("images"=>$ProductFiles),array("variants"=>$productcombination),array("variantprice"=>$variantarray),array("producttagdata"=>$producttagdata)); 
		}else{
			return array();
		}
	}
	function CountOurProductsOnFront($filterarray='[]'){
		
		$filterarray = json_decode($filterarray);
		
		$where = "";
		if(!empty($filterarray) && isset($filterarray->categoryid) && !empty($filterarray->categoryid)){
			$where .= " AND p.categoryid IN (".$filterarray->categoryid.")";
		}	
		if(!empty($filterarray) && isset($filterarray->search) && !empty($filterarray->search)){
			$where .= " AND (p.name LIKE '%".$filterarray->search."%' OR pc.name LIKE '%".$filterarray->search."%' OR '".$filterarray->search."'='')";
		}
		if(!empty($filterarray) && isset($filterarray->minprice) && isset($filterarray->maxprice)){
			$where .= " AND (IFNULL(IF(p.discount>0,(pbp.salesprice-(pbp.salesprice*p.discount/100)),pbp.salesprice),0) BETWEEN '".$filterarray->minprice."' AND '".$filterarray->maxprice."')";
		}
		if(!empty($filterarray) && isset($filterarray->categoryslug) && !empty($filterarray->categoryslug)){
			$where .= " AND pc.slug='".$filterarray->categoryslug."'";
		}	
		if(!empty($filterarray) && isset($filterarray->producttagid) && !empty($filterarray->producttagid)){
			$where .= " AND p.id IN (SELECT GROUP_CONCAT(DISTINCT productid) FROM ".tbl_producttagmapping." WHERE FIND_IN_SET(tagid,'".$filterarray->producttagid."')>0)";
		}	
		$channelid = GUESTCHANNELID;
		if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
			$channelid = CUSTOMERCHANNELID;
		}

		$query = $this->readdb->select("p.id")
						->from($this->_table." as p")
						->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER")
						->join(tbl_productbasicpricemapping." as pbp","pbp.productid=p.id AND pbp.channelid = '".$channelid."' AND pbp.allowproduct = 1","INNER")
						->where("IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.memberid='0' AND p.channelid='0'".$where)
						->group_by("pbp.productid")
						->get();
		
		// echo $this->readdb->last_query(); exit;
		return $query->num_rows();
	}
	function getOurProductsOnFront($limit,$offset=0,$filterarray='[]'){

		$filterarray = json_decode($filterarray);
		$channelid = GUESTCHANNELID;
		if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
			$channelid = CUSTOMERCHANNELID;
		}
		$this->readdb->select("p.id,p.name as productname,p.isuniversal,p.slug,p.categoryid,
						IF(p.isuniversal=1,(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as priceid,
						IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,
						pc.name as category,pc.slug as categoryslug,

						@price:=IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicquantityprice." as pbqp 
									WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) as price,

						@discount:=IFNULL((SELECT pbqp.discount FROM ".tbl_productbasicquantityprice." as pbqp 
									WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0 AND pbqp.salesprice=@price LIMIT 1),0) as discount,

						IF(@discount>0,(@price-(@price*@discount/100)),@price) as pricewithdiscount,
						p.shortdescription,

						'defaultproduct' as referencetype, (SELECT pbqp.id FROM ".tbl_productbasicquantityprice." as pbqp 
						WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0 AND pbqp.salesprice=@price LIMIT 1) as referenceid
					");
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		$this->readdb->join(tbl_productbasicpricemapping." as pbp","pbp.productid=p.id AND pbp.channelid = '".$channelid."' AND pbp.allowproduct = 1","INNER");
		$this->readdb->where("IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.memberid=0 AND p.channelid=0");
		
		if(!empty($filterarray) && isset($filterarray->categoryid) && !empty($filterarray->categoryid)){
			$this->readdb->where("pc.id IN (".$filterarray->categoryid.")");
		}
		if(!empty($filterarray) && isset($filterarray->categoryslug) && !empty($filterarray->categoryslug)){
			$this->readdb->where("pc.slug='".$filterarray->categoryslug."'");
		}			
		if(!empty($filterarray) && isset($filterarray->search) && !empty($filterarray->search)){
			$this->readdb->where("(p.name LIKE '%".$filterarray->search."%' OR pc.name LIKE '%".$filterarray->search."%' OR '".$filterarray->search."'='')");
		}
		if(!empty($filterarray) && isset($filterarray->minprice) && isset($filterarray->maxprice)){
			$this->readdb->where("(IFNULL(IF(p.discount>0,(pbp.salesprice-(pbp.salesprice*p.discount/100)),pbp.salesprice),0) BETWEEN '".$filterarray->minprice."' AND '".$filterarray->maxprice."')");
		}
		if(!empty($filterarray) && isset($filterarray->producttagid) && !empty($filterarray->producttagid)){
			$this->readdb->where("p.id IN (SELECT productid FROM ".tbl_producttagmapping." WHERE FIND_IN_SET(tagid,'".$filterarray->producttagid."')>0)");
		}	
		$this->readdb->group_by("pbp.productid");

		if(!empty($filterarray) && isset($filterarray->orderby)){
			if($filterarray->orderby==0){
				$this->readdb->order_by("pbp.salesprice","ASC");
			}elseif($filterarray->orderby==1){
				$this->readdb->order_by("pbp.salesprice","DESC");
			}elseif($filterarray->orderby==2){
				$this->readdb->order_by("p.priority","ASC");
			}
		}else{
			$this->readdb->order_by("p.priority","ASC");
		}
		
		$this->readdb->limit($limit,$offset);
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}
	function getMaxProductPriceOnFront(){

		$channelid = GUESTCHANNELID;
		if(!is_null($this->session->userdata(base_url().'MEMBER_ID'))){
			$channelid = CUSTOMERCHANNELID;
		}
		$query = $this->readdb->select("CAST(IF(p.discount>0,(pbp.salesprice-(pbp.salesprice*p.discount/100)),pbp.salesprice) AS DECIMAL(14,2)) as price")
					->from($this->_table." as p")
					->join(tbl_productbasicpricemapping." as pbp","pbp.productid=p.id AND pbp.channelid = '".$channelid."' AND pbp.allowproduct = 1","INNER")
					->where("IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.memberid=0 AND p.channelid=0")
					->group_by("pbp.productid")
					->get();
		
		return $query->result_array();
	}
	function CountOurProductsOnMemberFront($filterarray='[]',$channelid=0,$memberid=0){
		
		$filterarray = json_decode($filterarray);

		$where = "";
		if(!empty($filterarray) && isset($filterarray->categoryid) && !empty($filterarray->categoryid)){
			$where .= " AND p.categoryid IN (".$filterarray->categoryid.")";
		}	
		if(!empty($filterarray) && isset($filterarray->search) && !empty($filterarray->search)){
			$where .= " AND (p.name LIKE '%".$filterarray->search."%' OR pc.name LIKE '%".$filterarray->search."%' OR '".$filterarray->search."'='')";
		}
		if(!empty($filterarray) && isset($filterarray->minprice) && isset($filterarray->maxprice)){
			$where .= " AND (IFNULL(IF(p.discount>0,(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)-(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)*p.discount/100)),IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)),0) BETWEEN '".$filterarray->minprice."' AND '".$filterarray->maxprice."')";
		}
		if(!empty($filterarray) && isset($filterarray->categoryslug) && !empty($filterarray->categoryslug)){
			$where .= " AND pc.slug='".$filterarray->categoryslug."'";
		}	
		if(!empty($filterarray) && isset($filterarray->producttagid) && !empty($filterarray->producttagid)){
			$where .= " AND p.id IN (SELECT GROUP_CONCAT(DISTINCT productid) FROM ".tbl_producttagmapping." WHERE  (FIND_IN_SET(tagid,'".$filterarray->producttagid."')>0))";
		}	

		$query = $this->readdb->select("p.id")
						->from($this->_table." as p")
						->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER")
						->where("p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.memberid='".$memberid."' AND p.channelid='".$channelid."' AND IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)>0".$where)
						->get();
		
		// echo $this->readdb->last_query(); exit;
		return $query->num_rows();
	}
	function getOurProductsOnMemberFront($limit,$offset=0,$filterarray='[]',$channelid=0,$memberid=0){

		$filterarray = json_decode($filterarray);
		
		$this->readdb->select("p.id,p.name as productname,p.discount,p.isuniversal,p.slug,p.categoryid,
						IF(p.isuniversal=1,(SELECT id FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as priceid,
						IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,
						pc.name as category,pc.slug as categoryslug,
						IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0) as price,

						IF(p.discount>0,(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)-(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)*p.discount/100)),IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)) as pricewithdiscount,
						p.shortdescription
					");
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		$this->readdb->where("p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.memberid='".$memberid."' AND p.channelid='".$channelid."' AND IFNULL((SELECT count(id) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)>0");
		
		if(!empty($filterarray) && isset($filterarray->categoryid) && !empty($filterarray->categoryid)){
			$this->readdb->where("pc.id IN (".$filterarray->categoryid.")");
		}
		if(!empty($filterarray) && isset($filterarray->categoryslug) && !empty($filterarray->categoryslug)){
			$this->readdb->where("pc.slug='".$filterarray->categoryslug."'");
		}			
		if(!empty($filterarray) && isset($filterarray->search) && !empty($filterarray->search)){
			$this->readdb->where("(p.name LIKE '%".$filterarray->search."%' OR pc.name LIKE '%".$filterarray->search."%' OR '".$filterarray->search."'='')");
		}
		if(!empty($filterarray) && isset($filterarray->minprice) && isset($filterarray->maxprice)){
			$this->readdb->where("(IFNULL(IF(p.discount>0,(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)-(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)*p.discount/100)),IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)),0) BETWEEN '".$filterarray->minprice."' AND '".$filterarray->maxprice."')");
		}
		if(!empty($filterarray) && isset($filterarray->producttagid) && !empty($filterarray->producttagid)){
			$this->readdb->where("p.id IN (SELECT productid FROM ".tbl_producttagmapping." WHERE (FIND_IN_SET(tagid,'".$filterarray->producttagid."')>0))");
		}	
		// $this->readdb->group_by("pbp.productid");

		if(!empty($filterarray) && isset($filterarray->orderby)){
			if($filterarray->orderby==0){
				$this->readdb->order_by("price","ASC");
			}elseif($filterarray->orderby==1){
				$this->readdb->order_by("price","DESC");
			}elseif($filterarray->orderby==2){
				$this->readdb->order_by("p.priority","ASC");
			}
		}else{
			$this->readdb->order_by("p.priority","ASC");
		}
		
		$this->readdb->limit($limit,$offset);
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}
	function getMaxProductPriceOnMemberFront($channelid=0,$memberid=0){

		$query = $this->readdb->select("CAST(IF(p.discount>0,(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)-(IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)*p.discount/100)),IFNULL((SELECT min(price) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)) AS DECIMAL(14,2)) as price")
					->from($this->_table." as p")
					->where("p.status=1 AND p.producttype=0 AND p.productdisplayonfront=1 AND p.memberid='".$memberid."' AND p.channelid='".$channelid."' AND IFNULL((SELECT count(id) FROM ".tbl_productprices." WHERE price>0 AND productid=p.id),0)>0")
					->get();
		
		return $query->result_array();
	}
	function getMemberProductData($memberid,$productid,$priceid,$sellermemberid) {

		//$sellermemberid = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):"0";
		
		$this->readdb->select("p.id,(select name from ".tbl_productcategory." where id=p.categoryid)as categoryname,CONCAT(p.name,' ',IFNULL(
            (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
			FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as name,
			IFNULL(pp.id,0) as priceid, IF(p.isuniversal=0,pp.pointsforseller,p.pointsforseller) as pointsforseller, IF(p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer) as pointsforbuyer,

			IF(p.isuniversal=0,IFNULL((SELECT vp.price FROM ".tbl_membervariantprices." as vp WHERE vp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = '".$sellermemberid."') AND vp.memberid ='".$sellermemberid."' AND vp.priceid=mvp.priceid), pp.price), pp.price) as price,
			
			mvp.productallow as allowproduct,
			mvp.minimumqty,mvp.maximumqty,
			mvp.id as memberproductorvariantid,
			p.categoryid,
			mvp.stock as memberstock,
			mvp.channelid as channelid,
			mvp.sellermemberid,
			p.quantitytype,
			mvp.pricetype,
			mvp.minimumsalesprice
		");

        $this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_member." as m", "m.id=".$memberid, "INNER");
		$this->readdb->join(tbl_memberproduct." as mp", "mp.memberid=m.id AND mp.productid=p.id", "INNER");
		$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id", "INNER");
		$this->readdb->join(tbl_membervariantprices." as mvp", "(mvp.sellermemberid=".$sellermemberid." OR ".$sellermemberid."=0) AND mvp.memberid=mp.memberid AND mvp.priceid=pp.id", "INNER");
		$this->readdb->where("p.id=".$productid." AND (pp.id='".$priceid."' OR ''='".$priceid."')");
        $query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		if($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function getVendorProductData($vendorid,$productid,$priceid)
	{
		$this->readdb->select("p.id,(select name from ".tbl_productcategory." where id=p.categoryid)as categoryname,CONCAT(p.name,' ',IFNULL(
            (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
			FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as name,
			IFNULL(pp.id,0) as priceid, IF(p.isuniversal=0,pp.pointsforseller,p.pointsforseller) as pointsforseller, IF(p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer) as pointsforbuyer,

			IF(p.isuniversal=0,IFNULL((SELECT vp.price FROM ".tbl_membervariantprices." as vp WHERE vp.memberid = 0 AND vp.priceid=mvp.priceid), pp.price), pp.price) as price,
			mvp.price as memberprice,
			mvp.salesprice as salesprice,
			mvp.productallow as allowproduct,
			mvp.minimumqty,mvp.maximumqty,mvp.discountpercent,mvp.discountamount,
			mvp.id as memberproductorvariantid,
			p.categoryid,
			mvp.stock as memberstock,
			mvp.channelid as channelid,
			mvp.sellermemberid,
			p.quantitytype,
			mvp.pricetype
		");

        $this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_member." as m", "m.id=".$vendorid, "INNER");
		$this->readdb->join(tbl_memberproduct." as mp", "mp.memberid=m.id AND mp.productid=p.id", "INNER");
		$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id", "INNER");
		$this->readdb->join(tbl_membervariantprices." as mvp", "(mvp.sellermemberid=0 OR 0=0) AND mvp.memberid=mp.memberid AND mvp.priceid=pp.id", "INNER");
		$this->readdb->where("p.id=".$productid." AND (pp.id='".$priceid."' OR ''='".$priceid."')");
        $query = $this->readdb->get();
		
		if($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function getMemberProductCount($memberid){
		$query = $this->readdb->select('count(mp.id) as count,(SELECT mainmemberid FROM '.tbl_membermapping.' where submemberid='.$memberid.') as sellerid')
                            ->from(tbl_memberproduct." as mp")
                            ->where(array("memberid"=>$memberid))
                            ->get();
        return $query->row_array();
	}
	function getProductData($memberid,$productid,$memberbasicsalesprice=0,$sellerid=0){

		
		$this->readdb->select("p.name,pp.price,p.isuniversal,pp.stock,
							IFNULL((SELECT hsncode FROM ".tbl_hsncode." WHERE id=hsncodeid),'') as hsncode,
							IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=hsncodeid),0) as tax");
		
        $this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id", "INNER");
		$this->readdb->where("p.id=".$productid);
        $query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		return $query->row_array();
	}
	
	/* function getProductByCategorywithNotAssignMember($categoryid,$memberid,$currentsellerid=0,$brandid=0,$PCHANNELID=0,$PMEMBERID=0)
	{
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		// $memberid = (!is_null($MEMBERID)?$MEMBERID:$memberid);
		
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
        if (!is_null($MEMBERID)) {
			$memberdata = $this->Member->getmainmember($MEMBERID);
			$memberdata['id'] = !empty($memberdata)?$memberdata['id']:0;
        }
		$this->readdb->select("p.id,CONCAT(p.name,' ',IFNULL(
								(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
								FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as name, p.quantitytype
								
								");

        if (!is_null($MEMBERID)) {
			
            if ($memberspecificproduct==1) {
                $this->readdb->select("pp.id as priceid, pp.pointsforseller, pp.pointsforbuyer,
											(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$memberdata['id']." and mp.memberid='".$MEMBERID."')>0,
												
												IFNULL((SELECT min(mpqp.price) FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid)
													INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.sellermemberid=".$memberdata['id']." and mp.memberid='".$MEMBERID."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),0),
												
												IF(
													(".$memberdata['id']."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$memberdata['id']."')=0 OR 
													(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$memberdata['id']." and mp.memberid='".$MEMBERID."')=0),
													
													IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp
														INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
														WHERE pbp.channelid='".$CHANNELID."' AND pbp.allowproduct = 1 AND pbp.productpriceid=pp.id AND pbp.productid=pp.productid LIMIT 1),0),
													
													IFNULL((SELECT min(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
														INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
														INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
														WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$memberdata['id']."') and mp.memberid=".$memberdata['id']." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1),0)
												)
											)) as minprice,
												
											(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$memberdata['id']." and mp.memberid='".$MEMBERID."')>0,
												
												IFNULL((SELECT max(mpqp.price) FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid)
													INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.sellermemberid=".$memberdata['id']." and mp.memberid='".$MEMBERID."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),0),
												
												IF(
													(".$memberdata['id']."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$memberdata['id']."')=0 OR 
													(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$memberdata['id']." and mp.memberid='".$MEMBERID."')=0),
													
													IFNULL((SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp
														INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
														WHERE pbp.channelid='".$CHANNELID."' AND pbp.allowproduct = 1 AND pbp.productpriceid=pp.id AND pbp.productid=pp.productid LIMIT 1),0),
													
													IFNULL((SELECT max(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
														INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
														INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
														WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$memberdata['id']."') and mp.memberid=".$memberdata['id']." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1),0)
												)
											)) as maxprice
										");
            } else {
                $this->readdb->select('pp.id as priceid, pp.pointsforseller, pp.pointsforbuyer,
						
					IFNULL((SELECT min(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
						INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
						WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$CHANNELID.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as minprice,

					IFNULL((SELECT max(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
						INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
						WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$CHANNELID.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as maxprice
					');
            }
        }else{
			// $this->readdb->select('pp.id as priceid, pp.pointsforseller, pp.pointsforbuyer,
			// 	IFNULL((SELECT min(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
			// 		INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
			// 		WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as minprice,
					
			// 	IFNULL((SELECT max(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
			// 		INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
			// 		WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as maxprice
			// ');

			$this->readdb->select('pp.id as priceid, pp.pointsforseller, pp.pointsforbuyer,
				IFNULL((SELECT min(pqp.price) FROM '.tbl_productquantityprices.' as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0) as minprice,
				IFNULL((SELECT max(pqp.price) FROM '.tbl_productquantityprices.' as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0) as maxprice
				
			');
		}

        $this->readdb->from($this->_table." as p");
        $this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id", "INNER");
        $this->readdb->join(tbl_member." as m", "m.id=".$memberid, "INNER");
        $this->readdb->where("categoryid='".$categoryid."' AND p.status=1 AND p.producttype=0");
		$this->readdb->where("(brandid=".$brandid." OR ".$brandid."=0)");
		// if($currentsellerid!=0){
		// 	$this->readdb->where("pp.id IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.memberid='".$currentsellerid."') AND
		// 						pp.id NOT IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."')");
		// }else{
		// 	$this->readdb->where("pp.id NOT IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."')");
		// }

		
		if ($memberspecificproduct==1) {
			$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									
									pp.id NOT IN (SELECT mvp.priceid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) 
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mvp.priceid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

										pp.id IN (SELECT productpriceid FROM ".tbl_productbasicpricemapping." as pbp 
											WHERE channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productpriceid),
										
										pp.id NOT IN (SELECT mvp.priceid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mvp.priceid)
									)
							))");
		} else {
			$this->readdb->where("pp.id IN (SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp 
				WHERE pbp.channelid = (SELECT channelid FROM member WHERE id='".$memberid."')
				AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productpriceid)");
		}
        if (!is_null($MEMBERID)) {
        }else{
		}
		$this->readdb->where("p.status=1 AND p.producttype=0 AND p.channelid='".$PCHANNELID."' AND p.memberid='".$PMEMBERID."'");
		
        $this->readdb->order_by("p.priority ASC");
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		//if(p.isuniversal=1,(select count(id) from ".tbl_memberproduct." where memberid=".$memberid." and productid=p.id),0)=0
		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	} */
	function getProductByCategorywithNotAssignMember($categoryid,$memberid,$currentsellerid=0,$brandid=0,$PCHANNELID=0,$PMEMBERID=0) {
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		if (!is_null($MEMBERID)) {
			$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
			$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		}

		$this->readdb->select("p.id,CONCAT(p.name,' ',IFNULL(
								(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
								FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as name");

		if (!is_null($MEMBERID)) {
			if ($memberspecificproduct==1) {
				$this->readdb->select("pp.id as priceid, pp.pointsforseller, pp.pointsforbuyer,
						IFNULL((IF(
							(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
							
							(SELECT min(mpqp.price) FROM ".tbl_memberproduct." as mp 
								INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1
								INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
								WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
							
							IF(
								(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR 
								
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
								
								(SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp
									INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
									WHERE channelid = '".$channelid."' AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
								(SELECT min(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
									INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
									INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
									WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
							)
						)),0) as minprice,
							
						IFNULL((IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
								(SELECT max(mpqp.price) FROM ".tbl_memberproduct." as mp 
									INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1 
									INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
									WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR 
									
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
									(SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp
										INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
										WHERE channelid = '".$channelid."' AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
									(SELECT max(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
								)
							)),0) as maxprice,
							
						");
			} else {
				$this->readdb->select('pp.id as priceid, pp.pointsforseller, pp.pointsforbuyer,
									IFNULL((SELECT min(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
										INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
										WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$PCHANNELID.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as minprice,

									IFNULL((SELECT max(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
										INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
										WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$PCHANNELID.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as maxprice,
								');
			}
		}else{
			$this->readdb->select('pp.id as priceid, pp.pointsforseller, pp.pointsforbuyer,
					
					IFNULL((SELECT min(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
						INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
						WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as minprice,

					IFNULL((SELECT max(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp 
						INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0
						WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND pbp.allowproduct=1),0) as maxprice
						
					');
		}

		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id", "INNER");
		$this->readdb->join(tbl_member." as m", "m.id=".$memberid, "INNER");
		$this->readdb->where("categoryid='".$categoryid."' AND p.status=1 AND p.producttype=0");
		$this->readdb->where("(brandid=".$brandid." OR ".$brandid."=0)");
		
		if($currentsellerid!=0){
			$this->readdb->where("pp.id IN (SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.memberid='".$currentsellerid."') AND
								pp.id NOT IN (SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."')");
		}else{
			$this->readdb->where("pp.id NOT IN (SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."')");
		}

		if (!is_null($MEMBERID)) {
			if ($memberspecificproduct==1) {
				$this->readdb->where("(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
											p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) 
												WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
											
											p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbp
												WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),
											
											p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
												WHERE IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
										)
								))");
			} else {
				$this->readdb->where("p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbp 
					WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."')
					AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid)");
			}
		}else{
			$this->readdb->where("p.channelid='".$PCHANNELID."' AND p.memberid='".$PMEMBERID."'");
		}
		
		$this->readdb->order_by("p.priority ASC");
		$query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		//if(p.isuniversal=1,(select count(id) from ".tbl_memberproduct." where memberid=".$memberid." and productid=p.id),0)=0
		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	function getProductByCategorywithNotAssignVendor($categoryid,$vendorid)
	{
		$this->readdb->select("p.id,CONCAT(p.name,' ',IFNULL(
								(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
								FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as name,
								IFNULL(pp.id,0) as priceid, pp.pointsforseller, pp.pointsforbuyer,
								
								IFNULL((SELECT min(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as minprice,
								IFNULL((SELECT max(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as maxprice
							");

        $this->readdb->from($this->_table." as p");
        $this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id", "INNER");
        $this->readdb->join(tbl_member." as m", "m.id=".$vendorid, "INNER");
        $this->readdb->where("categoryid=".$categoryid." AND p.status=1 AND (p.producttype = 2 OR IF((SELECT purchaseregularproduct FROM ".tbl_member." where id='".$vendorid."')=1,p.producttype = 0,''))");
		
		$this->readdb->where("pp.id NOT IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid=0 and mvp.memberid='".$vendorid."')");

        $this->readdb->order_by("p.priority ASC");
		$query = $this->readdb->get();
		
		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	function getProductByBrand($memberid,$channelid,$brandid){
		
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;

		$this->readdb->select("p.id,CONCAT(p.name,' ',IFNULL(
			(SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
			FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'')) as name,
			IFNULL(pp.id,0) as priceid, pp.pointsforseller, pp.pointsforbuyer,
			
			IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),pp.price) as price,

			".$currentsellerid." as sellermemberid,

			pp.id as priceid	
		");
			
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id", "INNER");
		$this->readdb->join(tbl_member." as m", "m.id=".$memberid, "INNER");
		$this->readdb->where("p.status=1 AND p.producttype=0 AND brandid='".$brandid."'");
		
		if($currentsellerid!=0){
			$this->readdb->where("pp.id IN (SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.memberid='".$currentsellerid."') AND
								pp.id NOT IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."')");
		}else{
			$this->readdb->where("pp.id NOT IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."')");
		}

		$this->readdb->order_by("p.priority ASC");
		$query = $this->readdb->get();

		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	function getProductByChannelId($channelid){

		if($channelid!=""){
			//$where = "id IN (SELECT productid FROM ".tbl_memberproduct." WHERE memberid IN (SELECT id FROM ".tbl_member." WHERE FIND_IN_SET(channelid, '".$channelid."')>0 ))";
			//$where = "p.id IN (IFNULL((SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE FIND_IN_SET(pbp.channelid, '".$channelid."')>0 AND pbp.productid=p.id AND pbp.salesprice!=0 GROUP BY pbp.productid),0))"; 
		}else{
			$where = "1=1";
		}
		$where = "1=1";
		$query = $this->readdb->select("id, name,
								IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'') as image,	
						")
						->from($this->_table." as p")
						->where($where)
						->where("status=1 AND producttype=0")
						->order_by("name","ASC")
						->get();

		return $query->result_array();
						
	}
	function getProductByMemeberIDOrProductID($productid,$memberid){

		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;

		$this->readdb->select("p.id,pp.id as priceid,p.categoryid,p.name,p.shortdescription,p.description,p.isuniversal,
							p.pointsforseller,p.pointsforbuyer,p.producttype,p.catalogfile,p.pointspriority,
							(SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid) as tax,
							IFNULL((SELECT name FROM ".tbl_brand." WHERE id=p.brandid),'') as brandname,
							IFNULL((SELECT name FROM ".tbl_productunit." WHERE id IN (SELECT unitid FROM ".tbl_productprices." WHERE productid = p.id GROUP BY unitid)),'') as unitname,
							p.discount,p.hsncodeid,p.priority,p.status,p.createddate,p.quantitytype,

							IFNULL((SELECT GROUP_CONCAT(relatedproductid) FROM ".tbl_relatedproduct." as rp WHERE rp.productid=p.id),0) as relatedproductid,p.returnpolicytitle,p.returnpolicydescription,p.replacementpolicytitle,p.replacementpolicydescription,p.productdisplayonfront,
									(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid) as category,
									IFNULL((SELECT GROUP_CONCAT(tagid) FROM ".tbl_producttagmapping." WHERE productid=p.id),'') as tagid,
							
							(SELECT hsncode FROM ".tbl_hsncode." WHERE id=p.hsncodeid) as hsncode,
							IF(p.isuniversal=1,(SELECT sku FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as sku,
							IF(p.isuniversal=1,(SELECT barcode FROM ".tbl_productprices." WHERE productid=p.id LIMIT 1),'') as barcode,
							
							IFNULL((SELECT GROUP_CONCAT(tag SEPARATOR ', ') FROM ".tbl_producttag." WHERE id IN (SELECT tagid FROM ".tbl_producttagmapping." WHERE productid=p.id)),'') as tagsname,
							");
							
        if ($memberspecificproduct==1) {
			$this->readdb->select("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
										(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
										(SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)) as price,
								
								(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									(SELECT min(mvp.price) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mp.productid=pp.productid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
										(SELECT min(salesprice) FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productid=pp.productid),
										(SELECT min(mvp.salesprice) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid)
									)
								)) as minprice,
								
								(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									(SELECT max(mvp.price) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mp.productid=pp.productid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
										(SELECT max(salesprice) FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productid=pp.productid),
										(SELECT max(mvp.salesprice) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid)
									)
								)) as maxprice");
        }else{
			$this->readdb->select('IFNULL((SELECT pbp.salesprice FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0) as price');
		}

		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");

		$this->readdb->where("p.id=".$productid);	
		$this->readdb->group_by("p.id");
		$query = $this->readdb->get();
		/* $query = $this->readdb->select("
									IF(p.isuniversal=1,mvp.price,pp.price) as price,
									IF(p.isuniversal=1,mvp.stock,pp.stock) as universalstock,
									commingsoon,
									mvp.createddate,
									")
							->from($this->_table." as p")
							->join(tbl_membervariantprices." as mvp","mvp.priceid=pp.id AND mvp.memberid=".$memberid,"INNER")
							->where("p.id=".$productid)
							->get(); */

		return $query->row_array();
						
	}
	function getProductRewardpointsOrChannelSettings($productid,$buyermemberid,$sellermemberid=0){

		$query = $this->readdb->select("p.id,
									IF(p.pointspriority=0,p.pointsforseller,0) as pointsforseller,
									IF(p.pointspriority=0,p.pointsforbuyer,0) as pointsforbuyer,
									p.pointspriority,
									
									c.productwisepoints,
									c.productwisepointsmultiplywithqty,
									c.productwisepointsforbuyer,
									
									c.overallproductpoints,
									c.buyerpointsforoverallproduct,
									c.mimimumorderqtyforoverallproduct,
									
									c.pointsonsalesorder,
									c.buyerpointsforsalesorder,
									c.mimimumorderamountforsalesorder,

									c.conversationrate,
									c.minimumpointsonredeem,
									c.minimumpointsonredeemfororder,
									c.mimimumpurchaseorderamountforredeem,

									IFNULL(c2.productwisepoints,0) as sellerproductwisepoints,
									IFNULL(c2.productwisepointsmultiplywithqty,0) as sellerproductwisepointsmultiplywithqty,
									IFNULL(c2.productwisepointsforseller,0) as productwisepointsforseller,
									
									IFNULL(c2.overallproductpoints,0) as 
									selleroverallproductpoints,
									IFNULL(c2.sellerpointsforoverallproduct,0) as sellerpointsforoverallproduct,
									IFNULL(c2.mimimumorderqtyforoverallproduct,0) as sellermimimumorderqtyforoverallproduct,

									IFNULL(c2.pointsonsalesorder,0) as sellerpointsonsalesorder,
									IFNULL(c2.sellerpointsforsalesorder,,0) as sellerpointsforsalesorder,
									IFNULL(c2.mimimumorderamountforsalesorder,0) as sellermimimumorderamountforsalesorder,

									IFNULL(c2.conversationrate,0) as sellerconversationrate,
									
								")
						->from($this->_table." as p")
						->join(tbl_member." as m","m.id=".$buyermemberid, "LEFT")
						->join(tbl_channel." as c","c.id=m.channelid","LEFT")
						->join(tbl_channel." as c2","c2.id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$sellermemberid.")","LEFT")
						->where(array("p.id"=>$productid))
						->get();
		// echo $this->readdb->last_query(); exit;
		if($query->num_rows() == 1) {
			return $query->row_array();
		}else{
			return array();
		}
	}
   	function getmaincategory($MEMBERID=0,$CHANNELID=0) {
        $this->readdb->select('id, maincategoryid, IF(maincategoryid = 0, name, CONCAT((SELECT name FROM '.tbl_productcategory.' WHERE id = t.maincategoryid), " > ",name )) AS name');
		$this->readdb->from(tbl_productcategory.' AS t');
		$this->readdb->where("t.status=1 AND t.memberid='".$MEMBERID."' AND t.channelid='".$CHANNELID."'");
		$this->readdb->order_by('priority','ASC');
		$this->readdb->order_by('name','ASC');
        $query = $this->readdb->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	function getproduct() {
       // $this->readdb->select('id, productid, IF(productid = 0, priority, CONCAT((SELECT priority FROM '.tbl_productreview.' WHERE id = t.productid), " > ",name )) AS name');
		//	$this->readdb->from(tbl_product.' AS t');
		//  $this->readdb->where("t.status=1");
		//  $this->readdb->order_by('priority','ASC');
		//	//$this->readdb->order_by('name','ASC');
     ///   $query = $this->readdb->get();
       
		//if($query->num_rows() == 0) {
		//	return array();
		//} else {
		//	return $query->result_array();
		//}/
	}
	
	function getProductVariantsByProductId($productid){
	
		$query = $this->readdb->select("variantname as attribute,v.value,GROUP_CONCAT(v.id) as variantid")
						->from(tbl_attribute." as a")
						->join(tbl_variant." as v","v.attributeid=a.id","INNER")
						->join(tbl_productcombination." as pc","pc.variantid=v.id","INNER")
						->join(tbl_productprices." as pp","pc.priceid=pp.id","INNER")
						->where(array("pp.productid"=>$productid))
						->group_by("v.attributeid")
						->get();
		
		if($query->num_rows() > 0) {
			$variantdata = $query->result_array();
			$json=array();
			if(!empty($variantdata)){
				foreach($variantdata as $row){
					$variant=array();
					$variant_query = $this->readdb->select("v.value as name,v.id")
											->from(tbl_variant." as v")
											->where("FIND_IN_SET(v.id,'".$row['variantid']."')>0")
											->get();
					
					if($variant_query->num_rows() > 0) {
						$variant = $variant_query->result_array();
					}
					$json[] = array("attribute"=>$row['attribute'],
									"value"=>$variant);
				}
			}
			return $json;
		} else {
			return array();
		}
	}
	function getProductVariantsStockwise($productid,$priceid){
	
		$query = $this->readdb->select("variantname as attribute,v.value,v.value as variantvalue")
						->from(tbl_attribute." as a")
						->join(tbl_variant." as v","v.attributeid=a.id","INNER")
						->join(tbl_productcombination." as pc","pc.variantid=v.id","INNER")
						->join(tbl_productprices." as pp","pc.priceid=pp.id","INNER")
						->where(array("pp.productid"=>$productid,"pp.id"=>$priceid))
						->get();
		//echo $this->readdb->last_query(); exit;
		if($query->num_rows() > 0) {
			$variantdata = $query->result_array();
			$json=array();
			if(!empty($variantdata)){
				foreach($variantdata as $row){
					/* $variant=array();
					$variant_query = $this->readdb->select("v.value as name,v.id")
											->from(tbl_variant." as v")
											->where("FIND_IN_SET(v.id,'".$row['variantid']."')>0")
											->get();
					
					if($variant_query->num_rows() > 0) {
						$variant = $variant_query->result_array();
					} */
					$json[] = array("variantname"=>$row['attribute'],
									"variantvalue"=>$row['variantvalue']);
				}
			}
			return $json;
		} else {
			return array();
		}
	}
	function getallcategory($MEMBERID=0,$CHANNELID=0) {
        $this->readdb->select('id,maincategoryid,name');
        $this->readdb->from(tbl_productcategory.' AS t');
        $this->readdb->where("t.memberid='".$MEMBERID."' AND t.channelid='".$CHANNELID."'");
        $this->readdb->order_by('name','ASC');
        $query = $this->readdb->get();
		return $query->result_array();
	}
	function getMemberProductCategory($memberid,$currentsellerid=0) {
		
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        if (!is_null($MEMBERID)) {
			$channeldata = $this->Channel->getMemberChannelData($memberid);
			$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
            $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
        }
		
		$this->readdb->select('pc.id,pc.maincategoryid,pc.name');
		$this->readdb->from(tbl_productcategory.' AS pc');
		$this->readdb->join(tbl_product." as p","p.categoryid=pc.id","INNER");
		
		if(!is_null($MEMBERID)){
			$this->readdb->group_start();
			if($memberspecificproduct==1){
				$this->readdb->where("(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
										
										p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) 
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
					WHERE pbp.channelid = (SELECT channelid FROM member WHERE id='".$memberid."')
					AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)");
			}
			$this->readdb->group_end();
			$this->readdb->or_group_start();
				$this->readdb->where("(pc.id in (SELECT categoryid FROM ".tbl_product." WHERE id IN (select productid from ".tbl_memberproduct." where memberid=".$this->readdb->escape($memberid)."))) OR (pc.memberid='".$MEMBERID."')");
			$this->readdb->group_end();
			/* if($memberid == $MEMBERID){
				
				$where = array("pc.id in (SELECT categoryid FROM ".tbl_product." WHERE IFNULL(FIND_IN_SET(id,(SELECT GROUP_CONCAT(productid) FROM ".tbl_memberproduct." WHERE memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$MEMBERID."))),id>0))"=>null,

				"IF((SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid.")=0,0,IF((SELECT COUNT(pr.id) FROM ".tbl_product." as pr INNER JOIN ".tbl_member." as m ON m.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$MEMBERID.") 
				INNER JOIN ".tbl_memberproduct." as mp ON memberid=m.id AND mp.productid=pr.id 
				LEFT JOIN ".tbl_productprices." as pp ON pp.productid=pr.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) from ".tbl_membervariantprices." WHERE memberid = m.id))>0)
				WHERE pr.id in (select productid from ".tbl_memberproduct." where memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$MEMBERID.")) AND pr.categoryid=pc.id) 

				!= (SELECT COUNT(pr.id) FROM ".tbl_product." as pr INNER JOIN ".tbl_member." as m ON m.id='".$MEMBERID."' 
				INNER JOIN ".tbl_memberproduct." as mp ON memberid=m.id AND mp.productid=pr.id 
				LEFT JOIN ".tbl_productprices." as pp ON pp.productid=pr.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) from ".tbl_membervariantprices." WHERE memberid = m.id))>0)
				WHERE pr.id in (select productid from ".tbl_memberproduct." where memberid='".$MEMBERID."') AND pr.categoryid=pc.id),0,1))=0"=>null);
			}else{
				$where = array("pc.id in (SELECT categoryid FROM ".tbl_product." WHERE id IN (select productid from ".tbl_memberproduct." where memberid=".$MEMBERID."))"=>null,
			
				"IF((SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid.")=0,0,IF((SELECT COUNT(pr.id) FROM ".tbl_product." as pr INNER JOIN ".tbl_member." as m ON m.id='".$MEMBERID."' 
				INNER JOIN ".tbl_memberproduct." as mp ON memberid=m.id AND mp.productid=pr.id 
				LEFT JOIN ".tbl_productprices." as pp ON pp.productid=pr.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) from ".tbl_membervariantprices." WHERE memberid = m.id))>0)
				WHERE pr.id in (select productid from ".tbl_memberproduct." where memberid='".$MEMBERID."') AND pr.categoryid=pc.id) 
				!= (SELECT COUNT(pr.id) FROM ".tbl_product." as pr INNER JOIN ".tbl_member." as m ON m.id='".$memberid."' 
				INNER JOIN ".tbl_memberproduct." as mp ON memberid=m.id AND mp.productid=pr.id 
				LEFT JOIN ".tbl_productprices." as pp ON pp.productid=pr.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) from ".tbl_membervariantprices." WHERE memberid = m.id))>0)
				WHERE pr.id in (select productid from ".tbl_memberproduct." where memberid='".$memberid."') AND pr.categoryid=pc.id),0,1))=0"=>null);
			} */
			
		}else{
			/*$where = array("pc.id in (SELECT categoryid FROM ".tbl_product." WHERE IFNULL(FIND_IN_SET(id,(SELECT GROUP_CONCAT(productid) FROM ".tbl_memberproduct." WHERE memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid."))),id>0))"=>null,
		
			"IF((SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid.")=0,0,IF((SELECT COUNT(pr.id) FROM ".tbl_product." as pr INNER JOIN ".tbl_member." as m ON m.id = (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid.") 
			INNER JOIN ".tbl_memberproduct." as mp ON memberid=m.id AND mp.productid=pr.id 
			LEFT JOIN ".tbl_productprices." as pp ON pp.productid=pr.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) from ".tbl_membervariantprices." WHERE memberid = m.id))>0)
			WHERE pr.id in (select productid from ".tbl_memberproduct." where memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid =".$memberid.")) AND pr.categoryid=pc.id) 

			!= (SELECT COUNT(pr.id) FROM ".tbl_product." as pr INNER JOIN ".tbl_member." as m ON m.id=".$memberid." 
			INNER JOIN ".tbl_memberproduct." as mp ON memberid=m.id AND mp.productid=pr.id
			LEFT JOIN ".tbl_productprices." as pp ON pp.productid=pr.id AND (FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(priceid) from ".tbl_membervariantprices." WHERE memberid = m.id))>0)
			WHERE pr.id in (select productid from ".tbl_memberproduct." where memberid='".$memberid."') AND pr.categoryid=pc.id),0,1))=0"=>null);*/
			
			$this->readdb->where("pc.memberid='".$MEMBERID."'");
		}

		//$this->readdb->where("pc.id IN (SELECT p.categoryid FROM ".tbl_product." as p WHERE p.status=1 AND p.producttype=0)");
		/* $this->readdb->where("IF('".$currentsellerid."'!=0,
								pc.id IN (SELECT p.categoryid FROM 
									".tbl_product." as p 
									INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id
									WHERE p.status=1 AND p.producttype=0 AND p.categoryid=pc.id AND 
									pp.id IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.memberid='".$currentsellerid."') AND 
									pp.id NOT IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."')),

								pc.id IN (SELECT p.categoryid FROM 
									".tbl_product." as p 
									INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id
									WHERE p.status=1 AND p.producttype=0 AND p.categoryid=pc.id AND 
									pp.id NOT IN(SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$currentsellerid."' and mvp.memberid='".$memberid."'))
							)"); */
		//$this->readdb->where($where);
		$this->readdb->group_by("pc.id");
		$this->readdb->order_by('pc.priority','ASC');
		$this->readdb->order_by('pc.name','ASC');
		$query = $this->readdb->get();
		
		//echo $this->readdb->last_query(); exit;

		return $query->result_array();
	}
	function getVendorProductCategory($vendorid) {
		
		$this->readdb->select('pc.id,pc.maincategoryid,pc.name');
		$this->readdb->from(tbl_productcategory.' AS pc');
		
		//$this->readdb->where("pc.id IN (SELECT p.categoryid FROM ".tbl_product." as p WHERE p.status=1 AND p.producttype=2)");
		$this->readdb->where("pc.id IN (SELECT p.categoryid FROM 
									".tbl_product." as p 
									INNER JOIN ".tbl_productprices." as pp ON pp.productid=p.id
									WHERE p.status=1 AND (p.producttype = 2 OR IF((SELECT purchaseregularproduct FROM ".tbl_member." where id='".$vendorid."')=1,p.producttype = 0,'')) AND p.categoryid=pc.id AND 
									pp.id NOT IN (SELECT mvp.priceid FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid=0 and mvp.memberid='".$vendorid."'))");
		$this->readdb->order_by('pc.priority','ASC');
		$this->readdb->order_by('pc.name','ASC');
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	
	function getProductListByMember()
	{
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$REPORTINGTO = $this->session->userdata(base_url().'REPORTINGTO');
		
		$query = $this->readdb->select('p.id,p.name')
				
					->from($this->_table." as p")
					->join(tbl_memberproduct." as mp","mp.productid=p.id","LEFT")
					->where(array("(mp.memberid=".$MEMBERID." OR (mp.memberid=".$REPORTINGTO."))"=>null))
					->get();
		
		if($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
	}
	function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			//echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}
	function getAllProductsDetail($productid='',$MEMBERID=0,$CHANNELID=0){
		

		if ($MEMBERID!=0) {
            $channeldata = $this->Channel->getMemberChannelData($MEMBERID);
            $currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
            $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
        }

		$this->readdb->select('p.id,pp.id as priceid,pc.name as categoryname,p.name,
							description,
							p.createddate,p.priority,p.status,isuniversal,
							(SELECT GROUP_CONCAT(pc.variantid) FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_productprices.' as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid,
							discount,p.priority as productpriority,
							pp.sku');

		if($MEMBERID!=0){
			

			$this->readdb->select("$channelid as channelid,
								(SELECT salesprice FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$MEMBERID." AND sellermemberid=".$currentsellerid.") as salesprice,
								(SELECT productallow FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$MEMBERID." AND sellermemberid=".$currentsellerid.") as productallow,
									(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')>0,
										(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')=0),
											(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
											(SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
										)
									)) as price,
									
									(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')>0,
										(SELECT max(mvp.price) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."' AND mp.productid=pp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')=0),
											(SELECT max(salesprice) FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productid=pp.productid),
											(SELECT max(mvp.salesprice) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid)
										)
									)) as maxprice,
									
									(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')>0,
										(SELECT min(mvp.price) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."' AND mp.productid=pp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')=0),
											(SELECT min(salesprice) FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productid=pp.productid),
											(SELECT min(mvp.salesprice) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1 WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid)
										)
									)) as minprice");
		}else{
			$this->readdb->select("0 as salesprice,pp.price as price,
								(select max(price) from ".tbl_productprices." where productid=p.id)as maxprice,
								(select min(price) from ".tbl_productprices." where productid=p.id)as minprice");

		}
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");

		if ($MEMBERID!=0) {
			$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')>0,
									p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mp.productid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')=0),
										p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$MEMBERID."') AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),
										p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
									)
							))");
        }
		
		if(channel_memberspecificproduct==0){
			$this->readdb->where("p.id IN (IFNULL((SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$CHANNELID." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0 GROUP BY pbp.productid),0))");
		}
		if(!empty($productid)){
			$this->readdb->where('p.id IN ('.$productid.')');
		}
		$this->readdb->group_by("p.id");
		$this->readdb->order_by("p.id ASC");
		$query = $this->readdb->get();

		if($query->num_rows() > 0){
			$data = $query->result_array();
			$json=array();
			if(!empty($data)){
				foreach($data as $row){
					$this->load->model("Product_combination_model","Product_combination");
					$this->load->model("Stock_report_model","Stock");
					$productdata = $this->Stock->getAdminProductStock($row['id'],0,'','',0,$MEMBERID,$CHANNELID);
					$row['universalstock'] = $productdata[0]['openingstock'];
		
					$variantarr['variant'] = array();
					$productcombination = $this->Product_combination->getProductcombinationByProductIDWithValue($row['id']);
		
					$ProductStock = $this->Stock->getAdminProductStock($row['id'],1,'','',0,$MEMBERID,$CHANNELID);
		
					foreach ($productcombination as $pc) {
						$key = array_search($pc['priceid'], array_column($ProductStock, 'priceid'));
						$variantarr['variant'][$pc['priceid']]['price']=$pc['price'];
						$variantarr['variant'][$pc['priceid']]['sku']=$pc['sku'];
						$variantarr['variant'][$pc['priceid']]['stock']=(int)$ProductStock[$key]['openingstock'];
						$variantarr['variant'][$pc['priceid']]['variants'][]=array("variantvalue"=>$pc['variantvalue'],"variantname"=>$pc['variantname']);
					}
					
					$json[] = array_merge($row, $variantarr);
				}
			}
			return $json;
			
		}else{
			return array();
		}

	}
	function _get_datatables_query($MEMBERID,$CHANNELID){
		$PostData = $this->input->post();
		$categoryid = (!empty($_REQUEST['categoryid']))?implode(",",$_REQUEST['categoryid']):"";
		$brandid = (!empty($_REQUEST['brandid']))?implode(",",$_REQUEST['brandid']):"";
        $producttype = (!empty($_REQUEST['producttype']))?$_REQUEST['producttype']:0;

		$this->readdb->select('p.id,pp.id as priceid,pc.name as categoryname,p.name,description,
							IFNULL((select name from '.tbl_brand.' where id=p.brandid),"-") as brandname,
							p.createddate,p.priority,p.status,isuniversal,
							(SELECT GROUP_CONCAT(pc.variantid) FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_productprices.' as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid,p.quantitytype,
							p.priority as productpriority,IFNULL((select filename from '.tbl_productimage.' where productid=p.id limit 1),"") as productimage');

		$this->readdb->select("0 as salesprice,
					(SELECT max(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid IN (SELECT id FROM ".tbl_productprices." as pp where pp.productid=p.id))  as maxprice,
					
					(SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid IN (SELECT id FROM ".tbl_productprices." as pp where pp.productid=p.id))  as minprice
				");
		if(isset($PostData['sectionid'])){
			$this->readdb->select('ps.id as psid');
			$this->_datatableorder = array("productpriority"=>"asc");
		}
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->where("p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'");
		$this->readdb->where("(FIND_IN_SET(p.categoryid, '".$categoryid."')>0 OR '".$categoryid."'='')");
		$this->readdb->where("(FIND_IN_SET(p.brandid, '".$brandid."')>0 OR '".$brandid."'='')");
		$this->readdb->where("(p.producttype='".$producttype."' OR '".$producttype."'='0')");

		/*if(isset($PostData['loadtype']) && $PostData['loadtype']==1){

		}else{
			if(!is_null($MEMBERID)){
				$this->readdb->join(tbl_memberproduct." as mp","mp.productid=p.id","LEFT");
				$this->readdb->where(array("mp.memberid"=>$MEMBERID));
			}
		}*/
       /*  if ($MEMBERID!=0) {
			$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')>0,
									p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mp.productid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$MEMBERID."')=0),
										p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$MEMBERID."') AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),
										p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
									)
							))");
        } */
		
		if(isset($PostData['sectionid'])){
			$this->readdb->join(tbl_productsectionmapping." as ps","ps.productid=p.id","LEFT");
			$this->readdb->where(array("productsectionid"=>$PostData['sectionid']));
		}
		if(!is_null($this->session->userdata(base_url()."MEMBERID")) && channel_memberspecificproduct==0){
			
			$this->readdb->where("p.id IN (IFNULL((SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp 
				WHERE pbp.productpriceid=pp.id and pbp.channelid=".$CHANNELID." AND pbp.allowproduct=1 AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 GROUP BY pbp.productid),0))");
		}
		$this->readdb->group_by("p.id");
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


	function count_all($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		return $this->readdb->count_all_results();
	}

	function count_filtered($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		$query = $this->readdb->get();
		return $query->num_rows();
	}
	
	function getproductrecord($counter,$id='',$variantid='',$search,$memberid=0,$channelid=0,$sectionid='',$brandid=0) {
		$limit=10;

		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;

		if($variantid == "" ){
			$this->readdb->select('p.id,p.categoryid,p.name as productname,p.description,
									(select name from '.tbl_productcategory.' as pc where pc.id=p.categoryid limit 1)as categoryname,
									(select filename from '.tbl_productimage.' where productid=p.id limit 1)as file,
									(select type from '.tbl_productimage.' where productid=p.id limit 1)as filetype');
			$this->readdb->from($this->_table." as p");
			if($sectionid!=''){
				$this->readdb->where(array('ps.id in('.$sectionid.')'=>null));
				$this->readdb->join(tbl_productsectionmapping." as pm","pm.productid=p.id");
				$this->readdb->join(tbl_productsection." as ps","pm.productsectionid=ps.id and ps.status=1");
			}
			if($id!=""){
				$this->readdb->where('p.categoryid = "'.$id.'"');
			}
			if($memberid!=0 || $channelid!=0){
				if($totalproductcount > 0 && $memberspecificproduct==1){
					$this->readdb->where("(IF(
											(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
											
											p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) 
												WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
											
											IF(
												(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

												p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp 
													WHERE pbp.channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid),
												
												p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
													WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
											)
									))");
				}else{
					$this->readdb->where("p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."')
					AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)");
				}
				
			}
			$this->readdb->where("(p.name LIKE CONCAT('%','".$search."','%'))");
			$this->readdb->where(array("p.status"=>1,"p.producttype"=>0,"p.channelid"=>0,"p.memberid"=>0));
			if(!empty($brandid)){
				$this->readdb->where("(FIND_IN_SET(p.brandid,'".$brandid."')>0 OR '".$brandid."'='')");
			}
			if($sectionid!=''){
				$this->readdb->order_by("ps.priority asc,p.priority asc");	
			}else{
				$this->readdb->order_by("p.priority asc");
			}
			$this->readdb->group_by("p.id");
			if($counter != -1){
			$this->readdb->limit($limit,$counter);
			}   
			$query = $this->readdb->get();
			
		}else{
			$this->readdb->select('DISTINCT(p.id),p.categoryid,p.name as productname,p.description,(select filename from '.tbl_productimage.' where productid=p.id limit 1)as file,(select type from '.tbl_productimage.' where productid=p.id limit 1)as filetype,
									(select name from '.tbl_productcategory.' as pc where pc.id=p.categoryid limit 1)as categoryname,');
			$this->readdb->from($this->_table." as p");
			$this->readdb->join(tbl_productprices." as pp","p.id=pp.productid");
			$this->readdb->join(tbl_productcombination." as pc","pc.priceid=pp.id");
			$this->readdb->where("(pp.price > 0 or (select count(id) from ".tbl_productcombination." where priceid in (select id from ".tbl_productprices." where productid=p.id))>0)");
			if($memberid!=0 || $channelid!=0){

				if($totalproductcount > 0 && $memberspecificproduct==1){
					$this->readdb->where("(IF(
											(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
											
											p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) 
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
						WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."')
						AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)");
				}
			}
			if($sectionid!=''){
				$this->readdb->where(array('ps.id in('.$sectionid.')'=>null));
				$this->readdb->join(tbl_productsectionmapping." as pm","pm.productid=p.id");
				$this->readdb->join(tbl_productsection." as ps","pm.productsectionid=ps.id and ps.status=1");
			}
			$this->readdb->where(array("variantid in(".$variantid.")"=>null,"p.status"=>1,"p.producttype"=>0,"p.channelid"=>0,"p.memberid"=>0));
			if($id!=""){
				$this->readdb->where("p.categoryid='".$id."'");
			} 
			$this->readdb->where("(p.name LIKE CONCAT('%','$search','%'))");
			if(!empty($brandid)){
				$this->readdb->where("(FIND_IN_SET(p.brandid,'".$brandid."')>0 OR '".$brandid."'='')");
			}
			if($counter != -1){
				$this->readdb->limit($limit,$counter);
				}
			if($sectionid!=''){
				$this->readdb->order_by("ps.priority asc,p.priority asc");	
			}else{
				$this->readdb->order_by("p.priority asc");
			}
			// $this->readdb->order_by("p.id","DESC");
			$query=$this->readdb->get();
		}
		// echo $this->readdb->last_query(); exit;
		if($query->num_rows() == 0){
			return array(); 
		} 
		 else {	
			 $Data =$query->result_array();
			 foreach($Data as $k=>$dt){
				if (!file_exists(PRODUCT_PATH.$dt['file'])) {
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
	function getsubcategoryrecord($counter,$id,$search) {
		$limit=10;
		$this->readdb->select('id,name,image');		
		$this->readdb->from($this->_table);
	    $this->readdb->where('maincategoryid="'.$id.'" AND status = 1');
	    $this->readdb->where("(name LIKE CONCAT('%','$search','%'))");
        $this->readdb->limit($limit,$counter);     
		$query = $this->readdb->get();		
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

	function getproductrecordbyid2($id,$memberid=0,$channelid=0) {
			
		$this->load->model("Product_prices_model","Product_prices");
		$this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$memberaddorderwithoutstock = (!empty($channeldata['addorderwithoutstock']))?$channeldata['addorderwithoutstock']:0;        
		$totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;

		$this->readdb->select('p.id as productid,p.name as productname,p.description as description,p.brandid,
				IF('.GSTBILL.'=1,IFNULL((SELECT integratedtax FROM '.tbl_hsncode.' WHERE id=p.hsncodeid),""),0)as tax,
				IFNULL((SELECT hsncode FROM '.tbl_hsncode.' WHERE id=p.hsncodeid),"") as hsncode,pp.id as productpriceid,

				IF(IFNULL((SELECT 1 FROM '.tbl_channel.' WHERE id='.$channelid.' AND productwisepoints=1 AND productwisepointsforbuyer=1 AND IFNULL((SELECT rewardspoints FROM '.tbl_systemconfiguration.' LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer),0) as rewardpoints,
		
				IF(IFNULL((SELECT 1 FROM '.tbl_channel.' WHERE id in (SELECT m.channelid FROM '.tbl_membermapping.' as mp INNER JOIN '.tbl_member.' as m ON m.id=mp.mainmemberid AND m.status=1 WHERE mp.submemberid = '.$memberid.') AND productwisepoints=1 AND productwisepointsforseller=1 AND IFNULL((SELECT rewardspoints FROM '.tbl_systemconfiguration.' LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,pp.pointsforseller,p.pointsforseller),0) as referrerrewardpoints,

				IF(p.isuniversal=1,pp.sku,"") as sku,IF(p.isuniversal=1,pp.weight,"") as weight,p.catalogfile,

				IFNULL((SELECT ROUND(AVG(pr.rating),1) as rating FROM '.tbl_productreview.' as pr WHERE pr.productid=p.id AND pr.type=1),0) as rating,
        		IFNULL((SELECT COUNT(pr.id) FROM '.tbl_productreview.' as pr WHERE pr.productid=p.id AND pr.type=1),0) as reviews,

				IFNULL((SELECT min(price) FROM '.tbl_productquantityprices.' WHERE productpricesid=pp.id),0) as minprice,
				IFNULL((SELECT max(price) FROM '.tbl_productquantityprices.' WHERE productpricesid=pp.id),0) as maxprice,
				p.quantitytype,
				
			');

		if($memberid!=0 || $channelid!=0){
			
			$this->readdb->select('p.pointsforbuyer,p.pointsforseller,p.pointspriority,p.isuniversal');

			if ($totalproductcount > 0 && $memberspecificproduct==1) {
				
				$this->readdb->select("
							IFNULL(IF(p.isuniversal=1,(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
								(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
									INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
									WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
									(SELECT pricetype FROM ".tbl_productbasicpricemapping." as pbp
										WHERE channelid = '".$channelid."' AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
								
									(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
										INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
										WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
								)
							)),''),'') as pricetype,
								
							(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									
									(SELECT min(mpqp.price) 
										FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mp.productid = p.id AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

										(SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm
										INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
										WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),

										(SELECT min(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
										WHERE mp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) AND 
											(SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id IN (SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=p.id LIMIT 1)
									)
							)) as minmemberprice,
							

							(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									
									(SELECT max(mpqp.price) 
										FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1 
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mp.productid = p.id AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

										(SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm
										INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
										WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),

										(SELECT max(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
										WHERE mp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) AND 
											(SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id IN (SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=p.id LIMIT 1)
									)
							)) as maxmemberprice,

						");

				$this->readdb->select("IF(".PRODUCTDISCOUNT."=1,(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									
									(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow = 1  
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=p.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

										(SELECT pbp.discountpercent FROM ".tbl_productbasicpricemapping." as pbp 
										WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=p.id LIMIT 1),

										(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=p.id LIMIT 1)
									)
							)),0) as discount");

				$this->readdb->select("(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
								(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp 
									INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow = 1 
									WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=p.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 LIMIT 1),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

									(SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp 
									WHERE channelid = '".$channelid."' AND allowproduct = 1 AND productpriceid=pp.id AND productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),

									(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=p.id LIMIT 1)
								)
						)) as minimumorderqty");

				$this->readdb->select("(IF(
							(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
							
							(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp 
								INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow = 1 
								WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=p.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 LIMIT 1),
							
							IF(
								(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

								(SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp 
									WHERE channelid = '".$channelid."' AND allowproduct = 1 AND productpriceid=pp.id AND productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),

								(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp 
									INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1 
									WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=p.id LIMIT 1)
							)
					)) as maximumorderqty");
			}else{
				$this->readdb->select("
					IFNULL((SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm
						INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
						WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id)),0) as minmemberprice,

					IFNULL((SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm
						INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
						WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id)),0) as maxmemberprice,
					
					IFNULL((SELECT pbp.discountpercent FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1 AND pbp.productid=p.id),0) as discount,

					IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1 AND pbp.productid=p.id),0) as minimumorderqty,

					IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0) as maximumorderqty,

					IF(p.isuniversal=1,IFNULL((SELECT pbp.pricetype FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id LIMIT 1),''),'') as pricetype
				");
			}

		}else{
			$this->readdb->select('IF(pp.stock>0,"true","false")as instock,
							IF('.PRODUCTDISCOUNT.'=1,discount,0)as discount,p.isuniversal,pp.minimumorderqty,pp.maximumorderqty,

							IF(p.isuniversal=1,IFNULL((SELECT pbp.pricetype FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=p.id LIMIT 1),""),"") as pricetype
						');
		}
		
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->where("p.id='".$id."' AND p.status=1");
		
		if($memberid!=0 || $channelid!=0){
		    if($totalproductcount > 0 && $memberspecificproduct==1){
		    	
				$this->readdb->where("(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,

										p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1  
											INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
											WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mp.productid = p.id AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY mp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

											p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbpm 
											INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
											WHERE channelid IN (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND pbqp.salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),

											p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
											INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
											WHERE mp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
										)
									))
								");
		    }else{
				$this->readdb->where("p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbpm 
					INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
					WHERE pbpm.channelid = '".$channelid."'
					AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND pbpm.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid)");
			}
	    }
		
		$this->readdb->group_by("p.id");
		$query=$this->readdb->get();
		// echo $this->readdb->last_query();exit;
		if($query->num_rows() == 0){
			return array(); 
		} 
		 else {	
		 	$Data =$query->result_array();
			$json=array();
			 
			$this->readdb->select('id as sellermemberid');
			$this->readdb->from(tbl_member." as m");
			$this->readdb->where("id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")");
			$memberdata = $this->readdb->get()->row_array();
			$sellermemberid = isset($memberdata['sellermemberid'])?$memberdata['sellermemberid']:0;
		 	
		   	foreach ($Data as $row) {	
		        $ProductFiles = $this->getProductFiles($row['productid']);
				$variantarray = $image = $values_arr = array();
				
				foreach ($ProductFiles as $filerow) {
					if($filerow['type']==1){
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['filename']);
					}
				}
				$categoryfinal = $all_varianrids = $price_arr = $variantsarr = array();

				$referencetype = "";
				$multipleprice = array();
				if($row['isuniversal']==0){
					if(($memberid!=0 || $channelid!=0) && $totalproductcount > 0 && $memberspecificproduct==1){
						$this->readdb->select("pp.id,pp.pointsforseller,pp.pointsforbuyer,pp.sku,pp.weight,

							(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
								(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
									INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
									WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
									(SELECT pricetype FROM ".tbl_productbasicpricemapping." as pbp
										WHERE channelid = '".$channelid."' AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
								
									(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
										INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
										WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
								)
							)) as pricetype,

							IFNULL((SELECT min(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as minprice,
							IFNULL((SELECT max(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as maxprice,

								(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									
									(SELECT min(mpqp.price) 
										FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1 
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

										(SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm
											INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
											WHERE pbpm.channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productpriceid=pp.id AND productid=pp.productid LIMIT 1),

										(SELECT min(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
										WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND 
											(SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)) as minmemberprice,

								(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									
									(SELECT max(mpqp.price) 
										FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) 
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

										(SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm
											INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
											WHERE pbpm.channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productpriceid=pp.id AND productid=pp.productid LIMIT 1),

										(SELECT max(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
										WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND 
											(SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)) as maxmemberprice,


										IF(".PRODUCTDISCOUNT."=1,(IF(
											(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
										
											(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
											
											IF(
												(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
												
												(SELECT discountpercent FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),

												(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  
												WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
											)
										)),0) as discount,

										(IF(
											(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
											
											(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
											
											IF(
												(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
												
												(SELECT minimumqty FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),

												(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  
												WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
											)
										)) as minimumorderqty,

										(IF(
											(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
											
											(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
											
											IF(
												(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
												
												(SELECT maximumqty FROM ".tbl_productbasicpricemapping." as pbp
													WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),

												(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
													WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
											)
										)) as maximumorderqty,

										pp.stock");
					}else{
						$this->readdb->select('pp.id,
							pp.stock,pp.pointsforseller,pp.pointsforbuyer,pp.sku,pp.weight,
						
						IFNULL((SELECT MIN(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbpm 
						INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
						WHERE pbpm.productpriceid=pp.id and pbpm.channelid="'.$channelid.'" AND pbpm.allowproduct=1 AND pbpm.productid=pp.productid AND pbqp.salesprice>0),0) as minprice,

						IFNULL((SELECT MAX(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbpm 
						INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
						WHERE pbpm.productpriceid=pp.id and pbpm.channelid="'.$channelid.'" AND pbpm.allowproduct=1 AND pbpm.productid=pp.productid AND pbqp.salesprice>0),0) as maxprice,
						
						IFNULL((SELECT pbp.discountpercent FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid="'.$channelid.'" AND pbp.allowproduct=1 AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0),0) as discount,
						
						IFNULL((SELECT pbp.minimumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid="'.$channelid.'" AND pbp.allowproduct=1 AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0),0) as minimumorderqty,
						
						IFNULL((SELECT pbp.maximumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid="'.$channelid.'" AND pbp.allowproduct=1 AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0),0) as maximumorderqty,
						
						IFNULL((SELECT pbp.pricetype FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid),0) as pricetype
						');
					}
					$this->readdb->from(tbl_productprices." as pp");
					if(($memberid!=0 || $channelid!=0) && $totalproductcount > 0 && $memberspecificproduct==1){
						
						$this->readdb->where("
							(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,

								pp.id IN (SELECT mvp.priceid FROM ".tbl_memberproduct." as mp 
									INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1 
									WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

									pp.id IN (SELECT pbpm.productpriceid FROM ".tbl_productbasicpricemapping." as pbpm
										WHERE pbpm.channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0),0) > 0 AND pbpm.allowproduct=1 AND pbpm.productpriceid=pp.id GROUP BY pbpm.productpriceid),
									
									pp.id IN(SELECT mvp.priceid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										WHERE mp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mp.productid=pp.productid AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id IN (SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
								)
							))");
					}else{
						$this->readdb->where("pp.id IN (select productpriceid from ".tbl_productbasicpricemapping." as pbpm 
							WHERE channelid=".$channelid." AND productid=".$row['productid']." AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0),0) > 0 AND pbpm.allowproduct=1)");
					}
					$this->readdb->where("pp.productid=".$row['productid']);
					$pricedata = $this->readdb->get()->result_array();
					foreach ($pricedata as $pd) {
						
						$this->readdb->select('variantid,variantname,value');
						$this->readdb->from(tbl_productcombination." as pc");
						$this->readdb->join(tbl_variant." as v","v.id=pc.variantid");
						$this->readdb->join(tbl_attribute." as a","a.id=v.attributeid");
						$this->readdb->where(array("priceid"=>$pd['id']));
						
						$variantbyprice = $this->readdb->get()->result_array();
						
						$variantids=array();
						
						foreach($variantbyprice as $k=>$vp) {
							$variantsarr[$vp["variantname"]][]=array("id"=>$vp["variantid"],"optionvalue"=>$vp["value"]);
							$variantids[]=$vp['variantid'];
							$all_varianrids[]=$vp['variantid'];
					
							$values_arr[]=$vp["value"];
						}

						if(($memberid!=0 || $channelid!=0) && $totalproductcount > 0 && $memberspecificproduct==1){
							if(!is_null($pd['minmemberprice'])){
								$pd['minprice']=$pd['minmemberprice'];
								$pd['maxprice']=$pd['maxmemberprice'];
							}
						}
						
						$stockcheck = "false";
						$this->load->model('Stock_report_model', 'Stock');
						if($memberaddorderwithoutstock==0){
							if($sellermemberid!=0){
								$ProductVariantStock = $this->Stock->getVariantStock($sellermemberid,$row['productid'],'','',0,1,$channelid);
								if(!empty($ProductVariantStock)){

									$key = array_search($pd['id'], array_column($ProductVariantStock, 'combinationid'));
									//print_r($ProductVariantStock); exit;
									$price = $ProductVariantStock[$key]['price'];
									if($ProductVariantStock[$key]['overallclosingstock']>0 && STOCKMANAGEMENT==1){
										$stockcheck = "true";
									}
								}else{
									$stockcheck = "false";
								}
							}else{
								$ProductVariantStock = $this->Stock->getAdminProductStock($row['productid'],1);
								if(!empty($ProductVariantStock)){
									$key = array_search($pd['id'], array_column($ProductVariantStock, 'priceid'));
									$price = $ProductVariantStock[$key]['price'];
									if($ProductVariantStock[$key]['overallclosingstock']>0 && STOCKMANAGEMENT==1){
										$stockcheck = "true";
									}
								}else{
									$stockcheck = "false";
								}
							}
						}else{
							$stockcheck = "true";
						}
						//$price_arr[]=$price;

						if(number_format($pd['minprice'],2,'.','') == number_format($pd['maxprice'],2,'.','')){
							$price = number_format($pd['minprice'], 2, '.', '');
						}else{
							$price = number_format($pd['minprice'], 2, '.', '')." - ".number_format($pd['maxprice'], 2, '.', '');
						}
						$price_arr[]=$price;
						/* $stockcheck = "false";
						
						$this->load->model('Stock_report_model', 'Stock');
						$ProductVariantStock = $this->Stock->getVariantStock($memberid,$row['productid'],'','');

						$key = array_search($pd['id'], array_column($ProductVariantStock, 'combinationid'));
						
						if($ProductVariantStock[$key]['overallclosingstock']>0 && STOCKMANAGEMENT==1){
							$stockcheck = "true";
						} */


						$multipleprices = array();
						if(($memberid!=0 || $channelid!=0) && $totalproductcount > 0 && $memberspecificproduct==1){
							$multipleprices = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$pd['id']);
							$pricereferencetype = "memberproduct";
						}else{
							$multipleprices = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($channelid,$pd['id'],$row['productid']);
							$pricereferencetype = "defaultproduct";
						}
						
						$variantarray[]=array('combinationid'=>$pd['id'],"quantitytype"=>$row['quantitytype'],"pricetype"=>$pd['pricetype'],"price"=>$price,"variantid"=>implode(",",$variantids),"instock"=>$stockcheck,"pointsforbuyer"=>$pd['pointsforbuyer'],"sku"=>$pd['sku'],"weight"=>$pd['weight'],
						"minimumorderqty"=>$pd['minimumorderqty'],"maximumorderqty"=>$pd['maximumorderqty'],"discount"=>$pd['discount'],"referencetype"=>$pricereferencetype,"multipleprice"=>$multipleprices);
					}	
				}else{
					if(($memberid!=0 || $channelid!=0) && $totalproductcount > 0 && $memberspecificproduct==1){
						$multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$row['productpriceid']);
						$referencetype = "memberproduct";
					}else{
						$multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($channelid,$row['productpriceid'],$row['productid']);
						$referencetype = "defaultproduct";
					}
				}
				if(count($variantarray)>0 && $row['isuniversal']==0){
					/* if(number_format(min($price_arr),2,'.','') == number_format(max($price_arr),2,'.','')){
						$price = number_format(min($price_arr), 2, '.', ',');
					}else{
						$price = number_format(min($price_arr), 2, '.', ',')." - ".number_format(max($price_arr), 2, '.', ',');
					} */
					$productpriceid = "";
				}else{
					$productpriceid = $row['productpriceid'];
				}
				$minprice = $row['minprice'];
				$maxprice = $row['maxprice'];
				
				if($memberid!=0){
					if(!is_null($row['minmemberprice'])){
						$minprice = $row['minmemberprice'];
						$maxprice = $row['maxmemberprice'];
					}
				}
				if(number_format($minprice,2,'.','') == number_format($maxprice,2,'.','')){
					$price = number_format($minprice, 2, '.', '');
				}else{
					$price = number_format($minprice, 2, '.', '')." - ".number_format($maxprice, 2, '.', '');
				}
				$this->load->model('Stock_report_model', 'Stock');
				if($memberaddorderwithoutstock==0){
					if($sellermemberid!=0){
						$productdata = $this->Stock->getProductStockList($sellermemberid,0,'',$row['productid']);
						if(!empty($productdata) && STOCKMANAGEMENT==1){
							$instock = $productdata[0]['overallclosingstock'];
						}else{
							$instock = 0;
						}
						$instock = ($instock>0)?"true":"false"; 
					}else{
						$productdata = $this->Stock->getAdminProductStock($row['productid'],0);
						if(!empty($productdata) && STOCKMANAGEMENT==1){
						    $instock = $productdata[0]['overallclosingstock'];
						}else{
						    $instock = 0;
						}
						$instock = ($instock>0)?"true":"false"; 
					}
				}else{
					$instock = "true"; 
				}
				$variantsfinalarr = array();
				$i=0;
			
				foreach($variantsarr as $key=>$va){
					$va = array_values(array_map("unserialize", array_unique(array_map("serialize", $va))));
				  
					$variantsfinalarr[$i]['variantname']=$key;
					$variantsfinalarr[$i]['value'] = $va;
					$i++;
				}
				if(count($image)==0){
					$image[]=array("type"=>"1","file"=>PRODUCTDEFAULTIMAGE);
				}
				$variantsfinalarr = (!empty($variantsfinalarr))?$variantsfinalarr:$variantsfinalarr;
				$variantarray = (!empty($variantarray))?$variantarray:$variantarray;
				
				$this->load->model('Offer_model', 'Offer');
				$offerdata = $this->Offer->getOfferDataByProductorVariant($memberid,$row['productid'],$productpriceid);
				
				$json = array("id"=>$row['productid'],
							"combinationid"=>$productpriceid,
							"productname"=>$row['productname'],
							"quantitytype"=>$row['quantitytype'],
							"pricetype"=>$row['pricetype'],
							"rating"=>$row['rating'],
							"reviews"=>$row['reviews'],
							"referencetype"=>$referencetype,
							"multipleprice"=>$multipleprice,
							"price"=>$price,
							"sku"=>$row['sku'],
							'instock'=>$instock,
							"tax"=>$row['tax'],
							"discountper"=>$row['discount'],
							"brandid"=>$row['brandid'],
							"hsncode"=>$row['hsncode'],
							"image"=>$image,
							"pointsforbuyer"=>$row['pointsforbuyer'],
							/* "pointsforseller"=>$row['pointsforseller'], */
							"pointspriority"=>$row['pointspriority'],
							"description"=>$row['description'],
							"weight"=>$row['weight'],
							"minimumorderqty"=>$row['minimumorderqty'],
							"maximumorderqty"=>$row['maximumorderqty'],
							"catalogfile"=>$row['catalogfile'],
							"variantdata" =>$variantsfinalarr,
							"variantprice" =>$variantarray,		
							"offerdata" =>$offerdata,	
						);
			
			}

			return $json;
		}
	}
	function getproductforstockentry($memberid,$channelid,$categoryid,$sellerid=0,$counter="",$variantid,$sectionid,$search,$brandid,$sortbyid){
	
		$limit=10;
		$this->load->model("Product_prices_model","Product_prices");
		$this->load->model('Stock_report_model', 'Stock');
		$this->load->model('Channel_model', 'Channel');

		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']) && $sellerid==0)?$channeldata['currentsellerid']:$sellerid;
		
		if($memberspecificproduct==1){
			$select_price = "(IF(".$memberbasicsalesprice."=0,mvp.price,mvp.price)) as memberprice,";
		}else{
			$select_price = "pbpm.salesprice as memberprice,";
		}
		$this->readdb->select("p.id as productid,p.name as productname,p.isuniversal,pp.id as priceid,
							IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0) as tax,
							IF(IFNULL((SELECT 1 FROM ".tbl_channel." WHERE id=".$channelid." AND productwisepoints=1 AND productwisepointsforbuyer=1 AND IFNULL((SELECT rewardspoints FROM ".tbl_systemconfiguration." LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer),0) as rewardpoints,
							IF(IFNULL((SELECT 1 FROM ".tbl_channel." WHERE id in (SELECT m.channelid FROM ".tbl_membermapping." as mp INNER JOIN ".tbl_member." as m ON m.id=mp.mainmemberid AND m.status=1 WHERE mp.submemberid = '.$memberid.') AND productwisepoints=1 AND productwisepointsforseller=1 AND IFNULL((SELECT rewardspoints FROM ".tbl_systemconfiguration." LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,pp.pointsforseller,p.pointsforseller),0) as referrerrewardpoints,
							IF(IFNULL((SELECT conversationrate FROM ".tbl_channel." WHERE id=".$channelid." AND productwisepoints=1 AND productwisepointsforbuyer=1 AND IFNULL((SELECT rewardspoints FROM ".tbl_systemconfiguration." LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer),0) as conversationrate,
							IF(IFNULL((SELECT conversationrate FROM ".tbl_channel." WHERE id in (SELECT m.channelid FROM ".tbl_membermapping." as mp INNER JOIN ".tbl_member." as m ON m.id=mp.mainmemberid AND m.status=1 WHERE mp.submemberid = '.$memberid.') AND productwisepoints=1 AND productwisepointsforseller=1 AND IFNULL((SELECT rewardspoints FROM ".tbl_systemconfiguration." LIMIT 1),0)=1),0)=1,IF(p.pointspriority=1 AND p.isuniversal=0,pp.pointsforseller,p.pointsforseller),0) as referrerconversationrate,

							IFNULL((SELECT ROUND(AVG(pr.rating),1) as rating FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1),0) as rating,
        					IFNULL((SELECT COUNT(pr.id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1),0) as reviews,

							IF(p.isuniversal=1,pp.sku,'') as sku,

							IF(p.isuniversal=1,IFNULL((SELECT id FROM ".tbl_cart." as c WHERE c.memberid='".$memberid."' AND c.sellermemberid='".$sellerid."' AND c.type=0 AND c.productid=pp.productid AND c.priceid=pp.id),0),'') as cartid,

							IF(p.isuniversal=1,IFNULL((SELECT quantity FROM ".tbl_cart." as c WHERE c.memberid='".$memberid."' AND c.sellermemberid='".$sellerid."' AND c.type=0 AND c.productid=pp.productid AND c.priceid=pp.id),0),'') as cartqty,

							IF(p.isuniversal=1,IFNULL((SELECT referenceid FROM ".tbl_cart." as c WHERE c.memberid='".$memberid."' AND c.sellermemberid='".$sellerid."' AND c.type=0 AND c.productid=pp.productid AND c.priceid=pp.id),0),'') as cartreferenceid,

							p.quantitytype,

							IFNULL((SELECT min(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as minprice,
							IFNULL((SELECT max(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as maxprice,
						");
        if ($totalproductcount>0 && $memberspecificproduct==1) {
			$this->readdb->select("

								IFNULL(IF(p.isuniversal=1,(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
									
									(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
										INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
										
										(SELECT pricetype FROM ".tbl_productbasicpricemapping." as pbp
											WHERE channelid = '".$channelid."' AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
									
										(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
											INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)),''),'') as pricetype,

								(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
									(SELECT min(mpqp.price)  FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mp.productid = p.id AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
										(SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm 
											INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
											WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),
									
										(SELECT min(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1
											INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)) as minmemberprice,

								(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
									(SELECT max(mpqp.price)  FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mp.productid = p.id AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
										(SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm 
										INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
										WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) LIMIT 1),
									
										(SELECT max(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1
											INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)) as maxmemberprice,
								
								IFNULL(IF(".PRODUCTDISCOUNT."=1 AND p.isuniversal=1,(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
									(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1   
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
										(SELECT pbp.discountpercent FROM ".tbl_productbasicpricemapping." as pbp 
											WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
									
										(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1 
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)),0),0) as discount,
								
								IFNULL(IF(p.isuniversal=1,(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
									(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1   
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
										(SELECT minimumqty FROM ".tbl_productbasicpricemapping." as pbp
											WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
									
										(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1 
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)),0),0) as minqty,

								IFNULL(IF(p.isuniversal=1,(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
									(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1   
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
									
										(SELECT maximumqty FROM ".tbl_productbasicpricemapping." as pbp 
											WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
									
										(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1 
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
									)
								)),0),0) as maxqty
							");
        }else{
			$this->readdb->select('
				IFNULL((SELECT min(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbpm
					INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
					WHERE channelid = "'.$channelid.'" AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM '.tbl_productprices.' WHERE productid=p.id)),0) as minmemberprice,

				IFNULL((SELECT max(pbqp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbpm
					INNER JOIN '.tbl_productbasicquantityprice.' as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
					WHERE channelid = "'.$channelid.'" AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND productpriceid IN (SELECT id FROM '.tbl_productprices.' WHERE productid=p.id)),0) as maxmemberprice,
				
				IF('.PRODUCTDISCOUNT.'=1 AND p.isuniversal=1,IFNULL((SELECT pbp.discountpercent FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND pbp.allowproduct=1 AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0),0) as discount,

				IF(p.isuniversal=1,IFNULL((SELECT pbp.minimumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND pbp.allowproduct=1 AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0),"") as minqty,

				IF(p.isuniversal=1,IFNULL((SELECT pbp.maximumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid='.$channelid.' AND pbp.allowproduct=1 AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 LIMIT 1),0),"") as maxqty,

				IF(p.isuniversal=1,IFNULL((SELECT pbp.pricetype FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=p.id LIMIT 1),""),"") as pricetype
			');
		}
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		if($totalproductcount>0 && $memberspecificproduct==1){
			$this->readdb->where("(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
								p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp 
									INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
									INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
									WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mp.productid),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

									p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbpm  
										INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
										WHERE channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND pbqp.salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),

									p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
										WHERE mp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
								)
						))");
		}else{
			$this->readdb->where("p.id IN (SELECT pbpm.productid FROM ".tbl_productbasicpricemapping." as pbpm 
								WHERE pbpm.channelid = '".$channelid."'
								AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0),0) > 0 AND pbpm.allowproduct = 1 AND pbpm.productid=p.id AND pbpm.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbpm.productid)");
		}
		$this->readdb->where("p.status = 1 AND p.producttype=0 AND p.channelid=0 AND p.memberid=0 AND (FIND_IN_SET(p.categoryid,'".$categoryid."')>0 OR '".$categoryid."'='')");
		if(!empty($sectionid)){
			$this->readdb->where("(FIND_IN_SET(p.id,(SELECT GROUP_CONCAT(DISTINCT productid) FROM ".tbl_productsectionmapping." WHERE FIND_IN_SET(productsectionid,'".$sectionid."')>0))>0 OR '".$sectionid."'='')");
		}
		if(!empty($variantid)){
			$this->readdb->where("(FIND_IN_SET(pp.id,(SELECT GROUP_CONCAT(DISTINCT priceid) FROM ".tbl_productcombination." WHERE FIND_IN_SET(variantid,'".$variantid."')>0))>0 OR '".$variantid."'='')");
		}
		if(!empty($brandid)){
			$this->readdb->where("(FIND_IN_SET(p.brandid,'".$brandid."')>0 OR '".$brandid."'='')");
		}
		if($search!=""){
			$this->readdb->where("p.name LIKE '%".$search."%'");
		}
		$this->readdb->group_by("p.id");
		if(!empty($sortbyid)){
			if($sortbyid==1){//Popularity
				$this->readdb->order_by("rating DESC");	
			}else if($sortbyid==2){//Price-Low to High
				$this->readdb->order_by("minprice ASC");	
			}else if($sortbyid==3){//Price- High to Low
				$this->readdb->order_by("minprice DESC");	
			}else if($sortbyid==4){//Newest First
				$this->readdb->order_by("p.id DESC");	
			}
		}
		if($counter != -1){
			$this->readdb->limit($limit,$counter);
		}
		$query = $this->readdb->get();
		// echo $this->readdb->last_query();exit;
		if($query->num_rows() > 0) {
			$productdata =$query->result_array();
		 	$json=array();
		 	
			foreach($productdata as $product){
				
				$variantarray=array();
				$ProductFiles = $this->Product->getProductFiles($product['productid']);
			 
				$image = array();
				$values_arr=array();
				foreach ($ProductFiles as $filerow) {
					if($filerow['type']==1){
						if (!file_exists(PRODUCT_PATH.$filerow['filename'])) {
							$filerow['filename'] = PRODUCTDEFAULTIMAGE;
						}
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['filename']);
					}
				}
				
				$all_varianrids=array();
				$price_arr=array();
				$variantsarr=array();

				$referencetype = "";
				$multipleprice = array();
				if($product['isuniversal']==0){

					$this->readdb->select('pp.id,pp.sku,IFNULL((SELECT GROUP_CONCAT(v.value SEPARATOR ", ") FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=pp.id),"") as combinationname, 
					IFNULL((SELECT id FROM '.tbl_cart.' as c WHERE c.memberid="'.$memberid.'" AND c.sellermemberid="'.$sellerid.'" AND c.type=0 AND c.productid=pp.productid AND c.priceid=pp.id LIMIT 1),"") as cartid,
					IFNULL((SELECT quantity FROM '.tbl_cart.' as c WHERE c.memberid="'.$memberid.'" AND c.sellermemberid="'.$sellerid.'" AND c.type=0 AND c.productid=pp.productid AND c.priceid=pp.id LIMIT 1),"") as cartqty,
					
					IFNULL((SELECT referenceid FROM '.tbl_cart.' as c WHERE c.memberid="'.$memberid.'" AND c.sellermemberid="'.$sellerid.'" AND c.type=0 AND c.productid=pp.productid AND c.priceid=pp.id LIMIT 1),"") as cartreferenceid,
					
					');
					if($memberid!=0){
						if ($totalproductcount>0 && $memberspecificproduct==1) {
							$this->readdb->select("

											(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
												
												(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
													INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
													WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
												
												IF(
													(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
													
													(SELECT pricetype FROM ".tbl_productbasicpricemapping." as pbp
														WHERE channelid = '".$channelid."' AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
												
													(SELECT mvp.pricetype FROM ".tbl_memberproduct." as mp
														INNER JOIN ".tbl_membervariantprices." as mvp ON mp.sellermemberid=mvp.sellermemberid and mp.memberid=mvp.memberid
														WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
												)
											)) as pricetype,

											(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
													
												(SELECT min(mpqp.price) FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1 
													INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
													
													IF(
														(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
														
														(SELECT min(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm 
															INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
															WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
													
														(SELECT min(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
															INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
															INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
															WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
													)
											)) as minprice,

											(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
													
												(SELECT max(mpqp.price) FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1 
													INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
													
													IF(
														(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
														
														(SELECT max(pbqp.salesprice) FROM ".tbl_productbasicpricemapping." as pbpm 
															INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbpm.id
															WHERE channelid = '".$channelid."' AND pbqp.salesprice>0 AND pbpm.allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
													
														(SELECT max(mpqp.salesprice) FROM ".tbl_memberproduct." as mp 
															INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
															INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
															WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
													)
											)) as maxprice,
											
											IF(".PRODUCTDISCOUNT."=1,(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
												
												(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1
													WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND mp.productid=pp.productid LIMIT 1),
												
												IF(
													(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
													
													(SELECT discountpercent FROM ".tbl_productbasicpricemapping." as pbp
														WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
												
													(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp 
														INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
														WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
												)
											)),0) as discount,
										
											(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
												
												(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1   
													WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND mp.productid=pp.productid LIMIT 1),
												
												IF(
													(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
													
													(SELECT minimumqty FROM ".tbl_productbasicpricemapping." as pbp
														WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
												
													(SELECT mvp.minimumqty FROM ".tbl_memberproduct." as mp 
														INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
														WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
												)
											)) as minimumorderqty,

											(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
												
												(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1   
													WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND mp.productid=pp.productid LIMIT 1),
												
												IF(
													(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
													
													(SELECT maximumqty FROM ".tbl_productbasicpricemapping." as pbp
														WHERE channelid = '".$channelid."' AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
												
													(SELECT mvp.maximumqty FROM ".tbl_memberproduct." as mp 
														INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
														WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
												)
											)) as maximumorderqty
											
										");
						}else{
							$this->readdb->select('
							
							IFNULL((SELECT min(pbp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as minprice,

							IFNULL((SELECT max(pbp.salesprice) FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as maxprice,
							
							IF('.PRODUCTDISCOUNT.'=1,IFNULL((SELECT pbp.discountpercent FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0),0) as discount,

							IFNULL((SELECT pbp.minimumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as minimumorderqty,

							IFNULL((SELECT pbp.maximumqty FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND IFNULL((SELECT count(pbqp.id) FROM '.tbl_productbasicquantityprice.' as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as maximumorderqty,
							
							IFNULL((SELECT pbp.pricetype FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid),0) as pricetype');
						}
					}else{
						$this->readdb->select("
						IFNULL((SELECT min(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as minprice,
						IFNULL((SELECT max(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id),0) as maxprice,
						(SELECT p.discount FROM ".tbl_product." WHERE id=pp.productid) as discount,pp.minimumorderqty,pp.maximumorderqty,pp.pricetype");
					}
					$this->readdb->from(tbl_productprices." as pp");
					if($memberid!=0){
						//$this->readdb->where(array("pp.productid"=>$product['productid'],"pp.id IN (select priceid from ".tbl_membervariantprices." where memberid=".$memberid.")"=>null,"IFNULL((SELECT 1 FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$memberid." AND IF(".$memberbasicsalesprice."=0,price,price)>0 AND productallow=1),0)=1"=>null));
						$this->readdb->where("pp.productid",$product['productid']);
						$this->readdb->where("(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,

												pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) AND mvp.productallow=1
													WHERE mp.sellermemberid=".$currentsellerid." AND mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0),
												
												IF(
													(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

													pp.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbpm 
														WHERE channelid IN (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbpm.id AND pbqp.salesprice>0),0) > 0 AND pbpm.allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid),

													pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
														INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
														WHERE mp.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
												)
										))");
					}else{
						$this->readdb->where(array("pp.productid"=>$product['productid']));
					}
					$pricedata = $this->readdb->get()->result_array();
					$all_varianrids=array();
					
					foreach ($pricedata as $pd) {
						
						$this->readdb->select('variantid,variantname,value');
						$this->readdb->from(tbl_productcombination." as pc");
						$this->readdb->join(tbl_variant." as v","v.id=pc.variantid");
						$this->readdb->join(tbl_attribute." as a","a.id=v.attributeid");
						$this->readdb->where(array("priceid"=>$pd['id']));
						$variantbyprice = $this->readdb->get()->result_array();
						$variantids=array();
						foreach($variantbyprice as $k=>$vp) {
							$variantsarr[$vp["variantname"]][]=array("id"=>$vp["variantid"],"optionvalue"=>$vp["value"]);
							$variantids[]=$vp['variantid'];
							$all_varianrids[]=$vp['variantid'];
						
							$values_arr[]=$vp["value"];
						}

						$multipleprices = array();
						if($memberid!=0){
							if($totalproductcount>0 && $memberspecificproduct==1){
								$multipleprices = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$pd['id']);
								$pricereferencetype = "memberproduct";
							}else{
								$multipleprices = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($channelid,$pd['id'],$product['productid']);
								$pricereferencetype = "defaultproduct";
							}
						}else{
							$multipleprices = $this->Product_prices->getProductQuantityPriceDataByPriceID($pd['id']);
							$pricereferencetype = "adminproduct";
						}

						/* if(($memberid!=0 || $channelid!=0) && $memberspecificproduct==1){
							
							$ProductVariantStock = $this->Stock->getVariantStock($memberid,$product['productid']);
							$keyval = "overallclosingstock";
						}else{

							$ProductVariantStock = $this->Stock->getAdminProductStock($product['productid'],1);
							$keyval = "openingstock";
						} */
						if($sellerid==0){
                            $ProductStock = $this->Stock->getAdminProductStock($product['productid'],1);
                            $key = array_search($pd['id'], array_column($ProductStock, 'priceid'));
					    }else{
                            $ProductStock = $this->Stock->getVariantStock($sellerid,$product['productid'],'','',$pd['id']);
                            $key = array_search($pd['id'], array_column($ProductStock, 'combinationid'));
                        }
						
						if(number_format($pd['minprice'],2,'.','') == number_format($pd['maxprice'],2,'.','')){
							$price = number_format($pd['minprice'], 2, '.', '');
						}else{
							$price = number_format($pd['minprice'], 2, '.', '')." - ".number_format($pd['maxprice'], 2, '.', '');
						}
						$price_arr[]=$price;
						$pricewithouttax = ($pd['minprice'] - ($pd['minprice'] * $product['tax'] / (100+$product['tax'])));
						//$variantarray[]=array("price"=>$pd['price'],"variantid"=>implode(",",$variantids));
						// $key = array_search($pd['id'], array_column($ProductVariantStock, 'combinationid'));
						$keyval = "overallclosingstock";
						$variantarray[] = array(
							'combinationid'=>$pd['id'],
							'combinationname'=>$pd['combinationname'],
							"quantitytype"=>$product['quantitytype'],
							"pricetype"=>$pd['pricetype'],
							"price"=>$price,
							"pricewithouttax"=>number_format($pricewithouttax,2,'.',''),
							"qty"=>(isset($ProductStock[$key]))?(string)$ProductStock[$key][$keyval]:'0',
							"variantid"=>implode(",",$variantids),
							"variantsku"=>$pd['sku'],
							"minqty"=>$pd['minimumorderqty'],
							"maxqty"=>$pd['maximumorderqty'],
							"discount"=>$pd['discount'],
							"cartid"=>$pd['cartid'],
							"cartqty"=>(string)$pd['cartqty'],
							"cartreferenceid"=>$product['cartreferenceid'],
							"referencetype"=>$pricereferencetype,
							"multipleprice"=>$multipleprices
						);
					}
				}else{
					if($memberid!=0){
						if($totalproductcount>0 && $memberspecificproduct==1){
							$multipleprice = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$product['priceid']);
							$referencetype = "memberproduct";
						}else{
							$multipleprice = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($channelid,$product['priceid'],$product['productid']);
							$referencetype = "defaultproduct";
						}
					}else{
						$multipleprice = $this->Product_prices->getProductQuantityPriceDataByPriceID($product['priceid']);
						$referencetype = "adminproduct";
					}
				}
				
				$minprice = $product['minprice'];
				$maxprice = $product['maxprice'];
				
				if($memberid!=0){
					if(!is_null($product['minmemberprice'])){
						$minprice = $product['minmemberprice'];
						$maxprice = $product['maxmemberprice'];
					}
				}
				if(number_format($minprice,2,'.','') == number_format($maxprice,2,'.','')){
					$price = number_format($minprice, 2, '.', '');
				}else{
					$price = number_format($minprice, 2, '.', '')." - ".number_format($maxprice, 2, '.', '');
				}

				if(!empty($variantarray) && $product['isuniversal']==0){
					/* if(number_format(min($price_arr),2,'.','') == number_format(max($price_arr),2,'.','')){
						$price = number_format(min($price_arr), 2, '.', '');
					}else{
						$price = number_format(min($price_arr), 2, '.', '')." - ".number_format(max($price_arr), 2, '.', '');
					} */
					$pricewithouttax=0;
				}else{
					// $price=$product['price'];
					$pricewithouttax = ($minprice - ($minprice * $product['tax'] / (100 + $product['tax'])));
				}
				$variantsfinalarr = array();
				$i=0;

				foreach($variantsarr as $key=>$va){
					$va = array_values(array_map("unserialize", array_unique(array_map("serialize", $va))));
					$variantsfinalarr[$i]['variantname']=$key;
					$variantsfinalarr[$i]['value'] = $va;
					$i++;
				}
				if(count($image)==0){
					$image[]=array("type"=>"1","file"=>PRODUCTDEFAULTIMAGE);
				}
				
				if(!empty($variantarray) && $product['isuniversal']==0){
					$qty = array_sum(array_column($variantarray, 'qty'));
				}else{
					if($sellerid==0){
						$ProductStock = $this->Stock->getAdminProductStock($product['productid'],0,'','',0,$memberid,$channelid);
					}else{
						$ProductStock = $this->Stock->getProductStockList($sellerid,0,'',$product['productid']);
					}
					$qty = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
					// $qty = (!empty($productdata)?$productdata[0]['openingstock']:0);
				}
				
				$variantsfinalarr = (!empty($variantsfinalarr))?$variantsfinalarr:$variantsfinalarr;
				$variantarray = (!empty($variantarray))?$variantarray:$variantarray;

				$json[]= array("productid"=>$product['productid'],
								"combinationid"=>($product['isuniversal']==1)?$product['priceid']:"",
								"productname"=>$product['productname'],
								"quantitytype"=>$product['quantitytype'],
								"pricetype"=>$product['pricetype'],
								"rating"=>$product['rating'],
								"reviews"=>$product['reviews'],
								"referencetype"=>$referencetype,
								"multipleprice"=>$multipleprice,
								"price"=>$price,
								"pricewithouttax"=>number_format($pricewithouttax,2,'.',''),
								"qty"=>(string)$qty,
								"image"=>$image,
								"cartid"=>$product['cartid'],
								"cartqty"=>(string)$product['cartqty'],
								"cartreferenceid"=>$product['cartreferenceid'],
								"tax"=>$product['tax'],
								"productsku"=>$product['sku'],
								"minqty"=>$product['minqty'],
								"maxqty"=>$product['maxqty'],
								"discount"=>$product['discount'],
								"rewardpoints"=>$product['rewardpoints'],
								"referrerrewardpoints"=>$product['referrerrewardpoints'],
								"conversationrate"=>$product['conversationrate'],
								"referrerconversationrate"=>$product['referrerconversationrate'],
								"variantdata" =>$variantsfinalarr,
								"variantprice" =>$variantarray
							);
			
			}
			return $json;

		} else {
			return array();
		}
	}
	function getdashboardproduct($memberid=0,$channelid=0) {

		if($memberid!=0 || $channelid!=0){

			$this->load->model('Channel_model', 'Channel');
            $channeldata = $this->Channel->getMemberChannelData($memberid);
			$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
			$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
			$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
			
			$select = 'p.id as productid,p.name as productname,p.description as description,
						IF('.GSTBILL.'=1,(SELECT integratedtax FROM '.tbl_hsncode.' WHERE id=p.hsncodeid),0)as tax,(SELECT hsncode FROM '.tbl_hsncode.' WHERE id=p.hsncodeid) as hsncode,
						ps.name as productsectionname,displaytype,productsectionid,p.isuniversal,IF('.PRODUCTDISCOUNT.'=1,discount,0)as discount,';
			
            if ($memberspecificproduct==1) {
                $select .= "(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=p.id LIMIT 1),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

									(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=p.id LIMIT 1),
								
									(SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=p.id LIMIT 1)
								)
							)) as price,";

				$select .= "IF(".PRODUCTDISCOUNT."=1,(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								
								(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=p.id LIMIT 1),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
								
									(SELECT discountpercent FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=p.id LIMIT 1),
								
									(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=p.id LIMIT 1)
								)
							)),0) as discount";
            }else{
				$select .= 'IFNULL((SELECT pbp.salesprice FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as price';
			}
		}else{
			$select = 'p.id as productid,p.name as productname,p.description as description,pp.price,IF('.GSTBILL.'=1,(SELECT integratedtax FROM '.tbl_hsncode.' WHERE id=p.hsncodeid),0)as tax,(SELECT hsncode FROM '.tbl_hsncode.' WHERE id=p.hsncodeid) as hsncode,ps.name as productsectionname,displaytype,IF('.PRODUCTDISCOUNT.'=1,discount,0)as discount,productsectionid,p.isuniversal';
		}

		if($memberid!=0 || $channelid!=0){
            if ($memberspecificproduct==1) {
                /* $where = "IF(
					(SELECT count(id) FROM ".tbl_memberproduct." WHERE memberid='".$memberid."')>0 OR ".$memberbasicsalesprice."=1,
			
					p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
								INNER JOIN ".tbl_member." as m ON m.id=mp.memberid
								WHERE mp.memberid='".$memberid."' AND 
									(SELECT count(mvp2.productallow) FROM ".tbl_membervariantprices." as mvp2 WHERE mvp2.memberid=mp.memberid AND mvp2.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp2.productallow=1 LIMIT 1)>0
									AND
									(SELECT SUM(IF((".$memberbasicsalesprice."=1 AND mvp2.price>0) OR mvp2.salesprice>0,1,0)) FROM ".tbl_membervariantprices." as mvp2 WHERE mvp2.memberid=mp.memberid AND mvp2.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp2.productallow=1)>0
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
				) and p.status=1"; */
				$where = "(IF(
								(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
								p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid =pp.id GROUP BY mp.productid),
								
								IF(
									(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

									p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = ".$channelid." AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY productid),
									
									p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
								)
						)) AND p.status=1 AND p.producttype=0";
            }else{
				$where = "pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id and pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0),pp.id)) and p.status=1 AND p.producttype=0";
			}
        }else{
			$where = '(pp.price > 0 OR (select count(id) from '.tbl_productcombination.' where priceid = pp.id)>0) and p.status=1 AND p.producttype=0'; 
        }
		
		$this->load->model("Product_section_model","Product_section");
		$this->Product_section->_fields = "id,maxhomeproduct";
		$this->Product_section->_order = "priority,inorder asc";

		if($memberid!=0 || $channelid!=0){
			$this->Product_section->_where = ("(((channelid=".$channelid." OR channelid=0) AND status=1 AND type=0 AND forapp=1) OR (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") AND (channelid=".$channelid." OR channelid=0) AND status=1 AND type=1  AND forapp=1))");
		}

		$productsection = $this->Product_section->getRecordByID();
		
		$queryarr = array();
		foreach($productsection as $ps){
			$queryarr[] = '(SELECT '.$select.'
			FROM '.$this->_table.' as `p`
			INNER JOIN '.tbl_productprices.' as `pp` ON `pp`.`productid`=`p`.`id`
			INNER JOIN '.tbl_productsectionmapping.' as `pm` ON `pm`.`productid`=`p`.`id`
			INNER JOIN '.tbl_productsection.' as `ps` ON `pm`.`productsectionid`=`ps`.`id` and `ps`.`status`=1
			WHERE `ps`.`id`='.$ps['id'].'
			AND '.$where.'
			GROUP BY p.id
			ORDER BY `ps`.`priority` asc, `p`.`priority` asc
			LIMIT '.$ps['maxhomeproduct'].')';
		}

		if(count($queryarr)>0){
			$query=$this->readdb->query(implode(" UNION ",$queryarr));
		}else{
			return array();exit;
		}
		
		if($query->num_rows() == 0){
			return array(); 
		} else {	
			$section_arr=array();
		 	$Data =$query->result_array();
		 	$json=array();
			
		   	foreach ($Data as $row) {
				$variantarray=array();

				$ProductFiles = $this->getProductFiles($row['productid']);
			 
				$image = array();
				$values_arr=array();
				foreach ($ProductFiles as $filerow) {
					if($filerow['type']==1){
						if($filerow['filename']!="" && file_exists(PRODUCT_PATH.$filerow['filename'])){
							$image[]=array("type"=>"1","file"=>$filerow['filename']);
						}else{
							$image[]=array("type"=>"1","file"=>PRODUCTDEFAULTIMAGE);
						}
					}/*else if($filerow['type']==2){
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['videothumb']);
					}else if($filerow['type']==3){
						preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", urldecode($filerow['file']), $matches);
						$image[] = array('type'=>$filerow['type'],'file'=>$matches[1]);
					}*/
				 }

				$price_arr = $variantsarr = $categoryfinal = array();
				if($row['isuniversal']==0){
					if($memberid!=0){
						if ($memberspecificproduct==1) {
							$this->readdb->select("pp.id,
												(IF(
													(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
													
													(SELECT mvp.price FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
													
													IF(
														(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR 
														
														(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
														
														(SELECT salesprice FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
														(SELECT mvp.salesprice FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
													)
											)) as price");

							$this->readdb->select("IF(".PRODUCTDISCOUNT."=1,(IF(
													(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
													
													(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid LIMIT 1),
													
													IF(
														(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR 
														(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
														
														(SELECT discountpercent FROM ".tbl_productbasicpricemapping." WHERE channelid = '".$channelid."' AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid LIMIT 1),
														
														(SELECT mvp.discountpercent FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 AND mp.productid=pp.productid LIMIT 1)
													)
											)),0) as discount");
						}else{
							$this->readdb->select('pp.id,IFNULL((SELECT pbp.salesprice FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as price,
							IFNULL((SELECT pbp.discountpercent FROM '.tbl_productbasicpricemapping.' as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid='.$channelid.' AND pbp.productid=pp.productid AND pbp.salesprice!=0 AND pbp.allowproduct=1),0) as discount');
						}
					}else{
						$this->readdb->select('pp.id,pp.price,p.discount');
					}
					$this->readdb->from(tbl_productprices." as pp");
					if($memberid!=0){
						//$this->readdb->where(array("pp.productid"=>$row['productid'],"pp.id IN (select priceid from ".tbl_membervariantprices." where memberid=".$memberid.")"=>null,"IFNULL((SELECT 1 FROM ".tbl_membervariantprices." where priceid=pp.id AND memberid=".$memberid." AND IF(".$memberbasicsalesprice."=0,price,price)>0 AND productallow=1),0)=1"=>null));
						$this->readdb->where("pp.productid",$row['productid']);
						$this->readdb->where("(IF(
												(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
												pp.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=".$currentsellerid." AND mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid),
												
												IF(
													(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
													pp.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND salesprice >0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid),
													pp.productid IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1  WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
												)
										))");
					}else{
						$this->readdb->where(array("pp.productid"=>$row['productid']));
					}
					$pricedata = $this->readdb->get()->result_array();
					$all_varianrids=array();
					// print_r($pricedata);
					
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
	
						$price_arr[]=$pd['price'];
						$variantarray[]=array("price"=>$pd['price'],"variantid"=>implode(",",$variantids),"discount"=>$pd['discount']);
					}
				}
				
				if(count($variantarray)>0 && $row['isuniversal']==0){
					if(number_format(min($price_arr),2,'.','') == number_format(max($price_arr),2,'.','')){
						$price = number_format(min($price_arr), 2, '.', ',');
					}else{
						$price = number_format(min($price_arr), 2, '.', ',')." - ".number_format(max($price_arr), 2, '.', ',');
					}
				}else{
					$price=$row['price'];
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
									"variantprice" =>$variantarray);
				
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

	function getProductList($MEMBERID=0,$CHANNELID=0) {
	   
		$query = $this->readdb->select("p.id, p.name, isuniversal, 
										IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,
										IF(isuniversal=1,IFNULL((SELECT pp.sku FROM ".tbl_productprices." as pp WHERE pp.productid=p.id AND pp.sku!='' LIMIT 1),''),'') as sku")
								->from($this->_table.' as p')
								->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
								->where("status=1 AND p.producttype=0 AND p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'")
								->group_by("p.id")
								->order_by('p.name ASC')
								->get();
		
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	function getAllProductList($MEMBERID=0,$CHANNELID=0) {
	   
		$query = $this->readdb->select('id, name, isuniversal,IFNULL((select filename from '.tbl_productimage.' where productid=p.id limit 1),"'.PRODUCTDEFAULTIMAGE.'") as productimage')
						->from($this->_table.' as p')
						->where("memberid='".$MEMBERID."' AND channelid='".$CHANNELID."'")
						->order_by('name ASC')
						->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	
	function getProductsByMultipleMemberId($memberid,$categoryid=0){
		
		$this->readdb->select('p.id, p.name,
			IFNULL((SELECT pi.filename FROM '.tbl_productimage.' as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),"'.PRODUCTDEFAULTIMAGE.'") as image');
							
		$this->readdb->from($this->_table.' as p');
		if($memberid!=''){
			$this->readdb->join(tbl_memberproduct." as mp","mp.productid=p.id","LEFT");
			//$this->readdb->where(array("(FIND_IN_SET(mp.memberid, '".$memberid."')>0)"=>null,"status"=>1));

			/* $this->readdb->where("(FIND_IN_SET(mp.memberid, '".$memberid."')>0) AND p.status=1 AND p.producttype=0 AND 
									IF(
										(SELECT count(id) FROM ".tbl_memberproduct." WHERE FIND_IN_SET(memberid, '".$memberid."')>0)>0,
								
										p.id IN ((SELECT mp2.productid FROM ".tbl_memberproduct." as mp2 
												INNER JOIN ".tbl_member." as m2 ON m2.id=mp2.memberid 
												WHERE FIND_IN_SET(mp2.memberid, '".$memberid."')>0 AND
												(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m2`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))),
										
										p.id IN ((SELECT mp2.productid FROM ".tbl_memberproduct." as mp2 
												INNER JOIN ".tbl_member." as m2 ON m2.id=mp2.memberid 
												WHERE mp2.memberid IN (SELECT mainmemberid FROM membermapping where FIND_IN_SET(submemberid, '".$memberid."')>0) AND 
												(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m2`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0)))
									) "); */
		}else{
		}
		$this->readdb->where("(FIND_IN_SET(p.categoryid, '".$categoryid."')>0 OR '".$categoryid."'='0') AND p.status=1 AND p.producttype!=1 AND p.channelid=0 AND p.memberid=0");
		$this->readdb->group_by('p.id');
		$this->readdb->order_by('p.name ASC');
		$query = $this->readdb->get();
      
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}

	function getMemberProducts($memberid,$productid,$categoryid=0){
		
		$CheckProduct = $this->getMemberProductCount($memberid);
		
		if($CheckProduct['count'] > 0 && $memberid!=0){
			
			$this->readdb->select("m.id as memberid,p.id as productid,IFNULL(mvp.priceid,0) as priceid,
						CONCAT(m.id,'|',p.id,'|',IFNULL(mvp.priceid,pp.id)) as conbinationid, 
						m.name as member, 
						CONCAT(p.name, ' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']')  
									FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=mvp.priceid),'')) as productname
						");
		}else{
			$this->readdb->select("0 as memberid, p.id as productid,
						IFNULL(pp.id, 0) as priceid, CONCAT(".$memberid.", '|', p.id, '|', IFNULL(pp.id, 0)) as conbinationid, 
						CONCAT(p.name, ' ', IFNULL((SELECT CONCAT('[', GROUP_CONCAT(v.value), ']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id), '')) as productname
					");
		}	
		$this->readdb->from($this->_table.' as p');

		if($CheckProduct['count'] > 0 && $memberid!=0){
			$this->readdb->join(tbl_member." as m","(FIND_IN_SET(m.id, '".$memberid."')>0 OR ''='".$memberid."')","INNER");
			$this->readdb->join(tbl_memberproduct." as mp","mp.memberid=m.id AND mp.productid=p.id","LEFT");
		}
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","LEFT");
		
		if($CheckProduct['count'] > 0 && $memberid!=0){
			$this->readdb->join(tbl_membervariantprices." as mvp","mvp.memberid=mp.memberid AND mvp.priceid=pp.id","LEFT");
		}
		
		$this->readdb->where(array("(FIND_IN_SET(p.id, '".$productid."')>0 OR ''='".$productid."')"=>null,"(FIND_IN_SET(p.categoryid, '".$categoryid."')>0 OR '".$categoryid."'='0')"=>null,"p.status"=>1,"p.producttype"=>0));
		$this->readdb->group_by("conbinationid");
		$this->readdb->order_by('memberid,productname ASC');
		$query = $this->readdb->get();
        // echo $this->readdb->last_query(); exit;
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}

	function getProductByCategoryId($memberid,$sellerid=0,$producttype="0",$withvariantdata=1){
		
		$this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		// $currentsellerid = (!empty($channeldata['currentsellerid']) && $sellerid==0)?$channeldata['currentsellerid']:$sellerid;
		$currentsellerid = $sellerid;
		$CheckProduct = $this->getMemberProductCount($memberid);

		$this->readdb->select('p.id,CONCAT(p.name," | ",(SELECT name FROM '.tbl_productcategory.' WHERE id=p.categoryid),IF(p.brandid!=0, CONCAT(" (", (SELECT name FROM brand WHERE id=p.brandid), ")"),"")) as name,
		IFNULL((SELECT pi.filename FROM '.tbl_productimage.' as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),"'.PRODUCTDEFAULTIMAGE.'") as image,
		IFNULL((SELECT pp.sku FROM '.tbl_productprices.' as pp WHERE pp.productid=p.id LIMIT 1),"") as sku,p.pointsforbuyer,p.pointsforseller,p.discount,

		IFNULL((SELECT count(of.id) 
			FROM offer as of
			WHERE of.status=1 AND (of.id IN (SELECT offerid FROM '.tbl_offermembermapping.' WHERE memberid="'.$memberid.'" AND offerid=of.id) OR (of.channelid=0 && of.usertype=0))
				AND type != 1
				AND ((CURRENT_DATE() BETWEEN of.startdate AND of.enddate) OR of.startdate="0000-00-00" OR of.enddate="0000-00-00") AND 

				(SELECT 1 FROM '.tbl_offerpurchasedproduct.' WHERE (SELECT productid FROM '.tbl_productprices.' WHERE id IN (productvariantid)) = p.id AND offerid = of.id LIMIT 1)=1
		),0) as countoffer
		');
		$this->readdb->from($this->_table." as p");

		if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
			/*$memberbasicsalesprice = ($type=='purchase')?0:$memberbasicsalesprice;
			$this->readdb->where("	IF(		
									(SELECT count(id) FROM ".tbl_memberproduct." WHERE memberid='".$memberid."')>0 OR ".$memberbasicsalesprice."=0,
									
										p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
												WHERE mp.memberid='".$memberid."' AND 
												(SELECT count(mvp.productallow) FROM ".tbl_membervariantprices." as mvp WHERE mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1 LIMIT 1)>0
												AND 
												(SELECT SUM(IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice)) FROM ".tbl_membervariantprices." as mvp WHERE mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1 LIMIT 1)>0
												
												)),
												
										IF((SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$memberid."')=0,
								
											p.id IN (SELECT productid FROM ".tbl_productbasicpricemapping." WHERE channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND salesprice > 0 AND allowproduct = 1 AND productid=p.id GROUP BY productid),
									
											p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
													INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
													WHERE mp.memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$memberid."') AND 
													(SELECT count(mvp.productallow) FROM ".tbl_membervariantprices." as mvp WHERE mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1 LIMIT 1) > 0
													AND 
													(SELECT SUM(IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice)) FROM ".tbl_membervariantprices." as mvp WHERE mvp.memberid=mp.memberid AND mvp.priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=mp.productid) AND mvp.productallow=1 LIMIT 1)>0
													AND
													(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))))
									)");*/
			$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,

									p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
										WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
									
									IF(
										(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),
										
										p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp
											WHERE pbp.channelid = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."') AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid),
										
										p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1
											WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
									)
							))");
		}else{
			$this->readdb->where("p.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 GROUP BY pbp.productid),0),p.id))");
		}
		
		$this->readdb->where("p.status=1 AND p.channelid=0 AND p.memberid=0");
		if($producttype=="1"){
			$this->readdb->where("p.producttype",0);
		}else if($producttype=="2"){
			$this->readdb->where("p.producttype",1);
		}else if($producttype=="2"){
			$this->readdb->where("p.producttype",1);
		}
		$this->readdb->order_by("name ASC");
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		$productdata = $query->result_array();
		$this->load->model('Offer_model', 'Offer');

		if(!empty($productdata)){
			if($withvariantdata==1){
				foreach($productdata as $k=>$row){
					$productdata[$k]['variantdata'] = $this->getVariantByProductId($row['id'],$memberid,'purchase',$sellerid,$channeldata,$CheckProduct);
					
					$productdata[$k]['offerproductsdata'] = array();
					if($row['countoffer'] > 0){
						$productdata[$k]['offerproductsdata'] = $this->Offer->getofferproducts($memberid,$row['id'],'');
					}
				}
			}
			
		}
		return $productdata;
	}
	function getVendorProducts($vendorid){
		
		$this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($vendorid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$CheckProduct = $this->getMemberProductCount($vendorid);

		$this->readdb->select('p.id,CONCAT(p.name," | ",(SELECT name FROM '.tbl_productcategory.' WHERE id=p.categoryid),IF(p.brandid!=0, CONCAT(" (", (SELECT name FROM brand WHERE id=p.brandid), ")"),"")) as name,p.discount,IFNULL((SELECT pi.filename FROM '.tbl_productimage.' as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),"'.PRODUCTDEFAULTIMAGE.'") as image,
		
		IFNULL(
			(SELECT GROUP_CONCAT(DISTINCT(CONCAT(m.name,"#",op.originalprice)) ORDER BY o.createddate DESC SEPARATOR " | ") 
				FROM '.tbl_orders.' as o
				INNER JOIN '.tbl_orderproducts.' as op ON op.orderid=o.id
				LEFT JOIN '.tbl_member.' as m ON m.id=o.sellermemberid
				WHERE op.productid=p.id AND o.sellermemberid!=0 AND o.memberid=0 AND o.status=1 AND o.approved=1
				GROUP BY op.productid
				ORDER BY o.createddate DESC
				LIMIT 1)
		,"") as lastpurchaseproduct,
		
		');
		$this->readdb->from($this->_table." as p");

		if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
			
			$this->readdb->where("(IF(
									(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=0 and mp.memberid='".$vendorid."')>0,

									p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
										INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
										WHERE mp.sellermemberid=0 and mp.memberid='".$vendorid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mp.productid),
									
									IF(
										((SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=0 and mp.
										memberid='".$vendorid."')=0),
										
										p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp
											INNER JOIN ".tbl_productbasicquantityprice." as pbqp ON pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0 
											WHERE pbp.channelid = '".$channelid."' AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid),
										
										p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
											INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0
											WHERE mp.sellermemberid='0' and mp.memberid=".$vendorid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
									)
							))");
		}else{
			$memberbasicsalesprice = ($memberbasicsalesprice==0)?1:$memberbasicsalesprice;
			$this->readdb->where("p.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 GROUP BY pbp.productid),0),p.id))");
		}
		
		$this->readdb->where("p.status=1 AND p.producttype != 1");
		// $this->readdb->where("p.status=1 AND (p.producttype = 2 OR IF((SELECT purchaseregularproduct FROM ".tbl_member." where id='".$vendorid."')=1,p.producttype = 0,''))");
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();
		$productdata = $query->result_array();

		if(!empty($productdata)){
			foreach($productdata as $k=>$row){
				$productdata[$k]['variantdata'] = $this->getVariantByProductId($row['id'],$vendorid,'purchase',0,$channeldata,$CheckProduct);
			}
		}
		return $productdata;
	}
	function getProductByBrandId($brandid,$channelid=0,$memberid=0){
		
		$this->load->model('Channel_model', 'Channel');

		$this->readdb->select('p.id,CONCAT(p.name," | ",(SELECT name FROM '.tbl_productcategory.' WHERE id=p.categoryid)) as name,p.pointsforbuyer,p.pointsforseller');
		$this->readdb->from($this->_table." as p");
		//$this->readdb->where("p.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 GROUP BY pbp.productid),0),p.id))");
		
		$this->readdb->where("p.status=1 AND p.producttype=0 AND p.channelid=".$channelid." AND p.memberid=".$memberid." AND p.brandid =".$brandid);
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getProductsByMultipleCategoryIds($categoryid,$channelid,$memberid=''){
		
		$this->readdb->select('p.id,p.name,
		IFNULL((SELECT pi.filename FROM '.tbl_productimage.' as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),"") as image,
		');
		$this->readdb->from($this->_table." as p");
		
		$producttype = 0;
		if($memberid!=''){
			if($channelid == VENDORCHANNELID){
				$producttype = 2;
			}
			$this->readdb->where("
				p.id IN (SELECT productid FROM ".tbl_memberproduct." 
						WHERE (FIND_IN_SET(memberid, '".$memberid."')>0) AND 
						(FIND_IN_SET(sellermemberid, (SELECT GROUP_CONCAT(mainmemberid) FROM ".tbl_membermapping." WHERE submemberid IN (".$memberid.")))>0)
					)");
		}
		$this->readdb->where("FIND_IN_SET(p.categoryid, '".$categoryid."')>0 AND p.status=1 AND p.producttype=".$producttype);
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}
	
	function getVariantByProductId($productid,$memberid,$type='sales',$sellerid,$channeldata=array(),$CheckProduct=array()){
		$data = array();
		if(isset($productid)){
			
			$this->load->model('Channel_model', 'Channel');
			$this->load->model('Stock_report_model', 'Stock');

			if(empty($channeldata)){
				$channeldata = $this->Channel->getMemberChannelData($memberid);
			}
			$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
			$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
			$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
			
			
            $this->load->model('Member_model','Member');
			
			if(empty($CheckProduct)){
				$CheckProduct = $this->getMemberProductCount($memberid);
			}
			if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
				$memberbasicsalesprice = ($memberbasicsalesprice==1 && $type=='sales')?$memberbasicsalesprice:0;

				$query = $this->readdb->select('pp.id,pp.price,p.isuniversal,p.quantitytype,
				
												IFNULL((SELECT pricetype 
													FROM '.tbl_membervariantprices.' as mvp 
													WHERE mvp.memberid="'.$memberid.'" AND mvp.sellermemberid="'.$sellerid.'" AND mvp.priceid=pp.id limit 1),pp.pricetype
												) as pricetype,

												IFNULL((SELECT integratedtax FROM '.tbl_hsncode.' WHERE id=p.hsncodeid),0) as tax,
												
												IFNULL((SELECT IF('.$memberbasicsalesprice.'=0,mvp.price,mvp.salesprice) FROM '.tbl_memberproduct.' as mp INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid="'.$sellerid.'" where mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$memberid.'" AND IF('.$memberbasicsalesprice.'=0,mvp.price,mvp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1),pp.price) as memberprice,
												
												IFNULL((SELECT IF('.$memberbasicsalesprice.'=0,min(mpqp.price),min(mpqp.salesprice)) 
													FROM '.tbl_memberproduct.' as mp 
													INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid="'.$sellerid.'" 
													INNER JOIN '.tbl_memberproductquantityprice.' as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$memberid.'" AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1),
													IFNULL((SELECT min(pqp.price) FROM '.tbl_productquantityprices.' as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
												) as minprice,

												IFNULL((SELECT IF('.$memberbasicsalesprice.'=0,max(mpqp.price),max(mpqp.salesprice)) 
													FROM '.tbl_memberproduct.' as mp 
													INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid="'.$sellerid.'" 
													INNER JOIN '.tbl_memberproductquantityprice.' as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$memberid.'" AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1),
													IFNULL((SELECT max(pqp.price) FROM '.tbl_productquantityprices.' as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
												) as maxprice,

												IFNULL((SELECT mvp.stock FROM '.tbl_memberproduct.' as mp INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid="'.$sellerid.'" where mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$memberid.'" LIMIT 1),pp.stock) as stock,
												
												IF(p.isuniversal=0,pp.pointsforseller,p.pointsforseller) as pointsforseller,IF(p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer) as pointsforbuyer,
												
												IFNULL((SELECT mvp.minimumqty FROM '.tbl_membervariantprices.' as mvp WHERE mvp.memberid="'.$memberid.'" AND mvp.sellermemberid="'.$sellerid.'" AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM '.tbl_memberproductquantityprice.' as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0),0) > 0 AND mvp.productallow=1),0) as minimumorderqty,

												IFNULL((SELECT mvp.maximumqty FROM '.tbl_membervariantprices.' as mvp WHERE mvp.memberid="'.$memberid.'" AND mvp.sellermemberid="'.$sellerid.'" AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM '.tbl_memberproductquantityprice.' as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0),0) > 0 AND mvp.productallow=1),0) as maximumorderqty,

												IFNULL((SELECT mvp.discountpercent FROM '.tbl_membervariantprices.' as mvp WHERE mvp.memberid="'.$memberid.'" AND mvp.sellermemberid="'.$sellerid.'" AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM '.tbl_memberproductquantityprice.' as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0),0) > 0 AND mvp.productallow=1),0) as discount,

												IFNULL((SELECT mvp.discountamount FROM '.tbl_membervariantprices.' as mvp WHERE mvp.memberid="'.$memberid.'" AND mvp.sellermemberid="'.$sellerid.'" AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM '.tbl_memberproductquantityprice.' as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0),0) > 0 AND mvp.productallow=1),0) as discountamount,

												IFNULL((SELECT count(of.id) 
													FROM offer as of
													WHERE of.status=1 AND (of.id IN (SELECT offerid FROM '.tbl_offermembermapping.' WHERE memberid="'.$memberid.'" AND offerid=of.id) OR (of.channelid=0 && of.usertype=0))
														AND type != 1
														AND ((CURRENT_DATE() BETWEEN of.startdate AND of.enddate) OR of.startdate="0000-00-00" OR of.enddate="0000-00-00") AND 

														(SELECT 1 FROM '.tbl_offerpurchasedproduct.' WHERE (SELECT productid FROM '.tbl_productprices.' WHERE id IN (productvariantid)) = p.id AND offerid = of.id LIMIT 1)=1
													AND (SELECT 1 FROM '.tbl_offerpurchasedproduct.' WHERE FIND_IN_SET(pp.id,productvariantid) > 0 AND offerid = of.id LIMIT 1)= 1
												),0) as countoffer
										')

									->from(tbl_product." as p")
									->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
									->where(array(
										"p.id"=>$productid,
										"IF(p.isuniversal=0,IF((SELECT count(mpqp.id) 
											FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid 
											INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id
											WHERE (mp.productid=p.id AND mp.memberid='".$memberid."') AND mvp.priceid=pp.id AND mvp.productallow = 1 AND IF(".$memberbasicsalesprice."=0,mpqp.price,mpqp.salesprice)>0 LIMIT 1)>0,0,1),0)=0"=>null
											)
										)
									->get();
				$productdata = $query->row_array();
				// echo $this->readdb->last_query(); exit;
				if(!empty($productdata)){
					if($productdata['isuniversal']==0){
						
						$query = $this->readdb->select("pp.id,pp.id as priceid,pp.price,mvp.stock,p.quantitytype,
						
													mvp.pricetype,
													IFNULL((SELECT h.integratedtax FROM ".tbl_hsncode." as h INNER JOIN ".$this->_table." as pr ON pr.hsncodeid = h.id WHERE pr.id=pp.productid),0) as tax,
													CONCAT(IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice),' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
																					FROM ".tbl_productcombination." as pc 
																					INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=mvp.priceid)
																			,'')) as memberprice,
													IFNULL(IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice),'') as actualprice,

													IFNULL((SELECT GROUP_CONCAT(v.value) 
															FROM ".tbl_productcombination." as pc 
															INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=mvp.priceid)
													,'') AS variant,

													pp.pointsforseller,pp.pointsforbuyer,

													mvp.minimumqty as minimumorderqty,mvp.maximumqty as maximumorderqty,
													mvp.discountpercent as discount,mvp.discountamount,
													IFNULL((SELECT count(of.id) 
														FROM offer as of
														WHERE of.status=1 AND (of.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."' AND offerid=of.id) OR (of.channelid=0 && of.usertype=0))
															AND type != 1
															AND ((CURRENT_DATE() BETWEEN of.startdate AND of.enddate) OR of.startdate='0000-00-00' OR of.enddate='0000-00-00') AND 

															(SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE (SELECT productid FROM ".tbl_productprices." WHERE id IN (productvariantid)) = p.id AND offerid = of.id LIMIT 1)=1
														AND (SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE FIND_IN_SET(pp.id,productvariantid) > 0 AND offerid = of.id LIMIT 1)= 1
													),0) as countoffer

												")
											->from(tbl_productprices." as pp")
											->join(tbl_product." as p","p.id=pp.productid","INNER")
											->join(tbl_membervariantprices." as mvp","mvp.priceid=pp.id AND mvp.sellermemberid='".$sellerid."' AND mvp.memberid=".$memberid,"INNER")
											->where(array("pp.productid"=>$productid,"mvp.productallow"=>1,"IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF(".$memberbasicsalesprice."=0,mpqp.price,mpqp.salesprice)>0),0) >"=>0))
											// ->order_by("IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice) ASC")
											->get();
						$variantdata = $query->result_array();
						// echo $this->readdb->last_query(); exit;
						if(!empty($variantdata)){
							foreach($variantdata as $variant){

								if(STOCKMANAGEMENT==1){
									if($sellerid==0){
										if(STOCK_MANAGE_BY==0){
											$ProductStock = $this->Stock->getAdminProductStock($productid,1);
											$keynm = 'overallclosingstock';
										}else{
											$ProductStock = $this->Stock->getAdminProductFIFOStock($productid,1);
											$keynm = 'overallclosingstock';
										}
										$stock = 0;
										if(!empty($ProductStock)){
											$key = array_search($variant['priceid'], array_column($ProductStock, 'priceid'));
											if(trim($key)!="" && isset($ProductStock[$key][$keynm])){
												$stock = (int)$ProductStock[$key][$keynm];
											}
										}
									}else{
										$ProductStock = $this->Stock->getVariantStock($sellerid,$productid,'','',$variant['priceid'],1);
										$key = array_search($variant['priceid'], array_column($ProductStock, 'combinationid'));
										$keynm = 'overallclosingstock';
										$stock = !empty($ProductStock[$key][$keynm])?$ProductStock[$key][$keynm]:0;
									}
								}else{
									$stock = 0;
								}
								$variantname = $variant['variant'];
								
								$data[] = array('id'=>$variant['id'],
										'priceid'=>$variant['priceid'],
										'price'=>$variant['price'],
										'quantitytype'=>$variant['quantitytype'],
										'pricetype'=>$variant['pricetype'],
										'tax'=>$variant['tax'],
										'actualprice'=>$variant['memberprice'],
										'memberprice'=>$variant['memberprice'],
										'variantname'=>$variantname,
										'stock'=>$stock,
										'discount'=>$variant['discount'],
										'discountamount'=>$variant['discountamount'],
										'pointsforseller'=>$variant['pointsforseller'],
										'pointsforbuyer'=>$variant['pointsforbuyer'],
										'minimumorderqty'=>$variant['minimumorderqty'],
										'maximumorderqty'=>$variant['maximumorderqty'],
										'referencetype'=>2,
										'countoffer'=>$variant['countoffer']);
							}
						}
					}else{

						if($sellerid==0){
							if(STOCK_MANAGE_BY==0){
								$ProductStock = $this->Stock->getAdminProductStock($productid,0);
								$keynm = 'overallclosingstock';
							}else{
								$ProductStock = $this->Stock->getAdminProductFIFOStock($productid,0);
								$keynm = 'overallclosingstock';
							}
							
						}else{
							$ProductStock = $this->Stock->getProductStockList($sellerid,0,'',$productid);
							$keynm = 'overallclosingstock';
						}
						$availablestock = !empty($ProductStock[0][$keynm])?$ProductStock[0][$keynm]:0;
						$stock = (STOCKMANAGEMENT==1)?$availablestock:0;
						
						if(number_format($productdata['minprice'],2,'.','') == number_format($productdata['maxprice'],2,'.','')){
							$variantname = number_format($productdata['minprice'], 2, '.', '');
						}else{
							$variantname = number_format($productdata['minprice'], 2, '.', '')." - ".number_format($productdata['maxprice'], 2, '.', '');
						}

						$data[] = array('id'=>0,
										'priceid'=>$productdata['id'],
										'price'=>$productdata['price'],
										'quantitytype'=>$productdata['quantitytype'],
										'pricetype'=>$productdata['pricetype'],
										'tax'=>$productdata['tax'],
										'actualprice'=>$productdata['memberprice'],
										'memberprice'=>$productdata['memberprice'],
										'variantname'=>$variantname,
										'stock'=>$stock,
										'discount'=>$productdata['discount'],
										'discountamount'=>$productdata['discountamount'],
										'universal'=>'1',
										'pointsforseller'=>$productdata['pointsforseller'],
										'pointsforbuyer'=>$productdata['pointsforbuyer'],
										'minimumorderqty'=>$productdata['minimumorderqty'],
										'maximumorderqty'=>$productdata['maximumorderqty'],
										'referencetype'=>2,
										'countoffer'=>$productdata['countoffer']);
					}
				}
			}else{
				$memberbasicsalesprice = ($sellerid==0 && $memberbasicsalesprice==0)?1:$memberbasicsalesprice;
				$query = $this->readdb->select("IF(p.isuniversal=0,pp.id,0) as id,pp.id as priceid,price,pp.stock,p.isuniversal,p.								quantitytype,
											(SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid=pp.id AND channelid=".$channelid." LIMIT 1) as pricetype,
											IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=hsncodeid),0) as tax,
											
											CONCAT(IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0),pp.price),' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
																																															FROM ".tbl_productcombination." as pc 
											INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
											,'')) as memberprice,
											
											IF(".$memberbasicsalesprice."=1,
												IFNULL((SELECT min(pqbp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp 
													INNER JOIN ".tbl_productbasicquantityprice." as pqbp ON pqbp.productbasicpricemappingid=pbp.id AND pqbp.salesprice>0
													WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.allowproduct=1),0),
												
												IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
											) as minprice,
											IF(".$memberbasicsalesprice."=1,
												IFNULL((SELECT max(pqbp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp 
													INNER JOIN ".tbl_productbasicquantityprice." as pqbp ON pqbp.productbasicpricemappingid=pbp.id AND pqbp.salesprice>0
													WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.allowproduct=1),0),
												
												IFNULL((SELECT max(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
											) as maxprice,

											IF(p.isuniversal=0,IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id),''),'') as variant,

											IFNULL(IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0),pp.price),0) as actualprice,
											
											IF(p.isuniversal=0,pp.pointsforseller,p.pointsforseller) as pointsforseller,IF(p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer) as pointsforbuyer,
											
											IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as minimumorderqty,

											IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as maximumorderqty,

											IFNULL((SELECT pbp.discountpercent FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as discount,

											IFNULL((SELECT pbp.discountamount FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as discountamount,

											IFNULL((SELECT count(of.id) 
												FROM offer as of
												WHERE of.status=1 AND (of.id IN (SELECT offerid FROM ".tbl_offermembermapping." WHERE memberid='".$memberid."' AND offerid=of.id) OR (of.channelid=0 && of.usertype=0))
													AND type != 1
													AND ((CURRENT_DATE() BETWEEN of.startdate AND of.enddate) OR of.startdate='0000-00-00' OR of.enddate='0000-00-00') AND 

													(SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE (SELECT productid FROM ".tbl_productprices." WHERE id IN (productvariantid)) = p.id AND offerid = of.id LIMIT 1)=1
												AND (SELECT 1 FROM ".tbl_offerpurchasedproduct." WHERE FIND_IN_SET(pp.id,productvariantid) > 0 AND offerid = of.id LIMIT 1)= 1
											),0) as countoffer
									")
								->from(tbl_product." as p")
								->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
								->where("p.id",$productid)
								->where("pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0),0),pp.id))")
								->get();

				$variantdata = $query->result_array();
					   
				if(!empty($variantdata)){
					foreach($variantdata as $variant){
						$ProductStock = array();
						if(STOCKMANAGEMENT==1){
							if($sellerid==0){
								if(STOCK_MANAGE_BY==0){
									//$ProductStock = $this->Stock->getAdminProductStock($productid,1);
									$keynm = 'overallclosingstock';
								}else{
									//$ProductStock = $this->Stock->getAdminProductFIFOStock($productid,1);
									$keynm = 'overallclosingstock';
								}
								
								$stock = 0;
								if(!empty($ProductStock)){
									$key = array_search($variant['priceid'], array_column($ProductStock, 'priceid'));
									if(trim($key)!="" && isset($ProductStock[$key][$keynm])){
										$stock = (int)$ProductStock[$key][$keynm];
									}
								}
							}else{
								$ProductStock = $this->Stock->getVariantStock($sellerid,$productid,'','',$variant['priceid'],1);
								$key = array_search($variant['priceid'], array_column($ProductStock, 'combinationid'));
								$keynm = 'overallclosingstock';
								$stock = !empty($ProductStock[$key][$keynm])?$ProductStock[$key][$keynm]:0;
							}
						}else{
							$stock = 0;
						}

						if($variant['isuniversal']==1){
							if(number_format($variant['minprice'],2,'.','') == number_format($variant['maxprice'],2,'.','')){
								$variantname = number_format($variant['minprice'], 2, '.', '');
							}else{
								$variantname = number_format($variant['minprice'], 2, '.', '')." - ".number_format($variant['maxprice'], 2, '.', '');
							}
						}else{
							$variantname = $variant['variant'];
						}
						$data[] = array('id'=>$variant['id'],
								'priceid'=>$variant['priceid'],
								'price'=>$variant['price'],
								'quantitytype'=>$variant['quantitytype'],
								'pricetype'=>$variant['pricetype'],
								'tax'=>$variant['tax'],
								'actualprice'=>$variant['memberprice'],
								'memberprice'=>$variant['memberprice'],
								'variantname'=>$variantname,
								'stock'=>$stock,
								'discount'=>$variant['discount'],
								'discountamount'=>$variant['discountamount'],
								'universal'=>$variant['isuniversal'],
								'pointsforseller'=>$variant['pointsforseller'],
								'pointsforbuyer'=>$variant['pointsforbuyer'],
								'minimumorderqty'=>$variant['minimumorderqty'],
								'maximumorderqty'=>$variant['maximumorderqty'],
								'referencetype'=>1,
								'countoffer'=>$variant['countoffer']
							);
					}
				}
			}
        }
		$this->load->model('Offer_model', 'Offer');
		if(!empty($data)){
			foreach($data as $k=>$row){
				$data[$k]['multiplepricedata'] = $this->getMultiplePriceByPriceIdOrMemberId($productid,$row['priceid'],$memberid,$channeldata,$CheckProduct);

				$data[$k]['offerproductsdata'] = array();
				if($row['countoffer'] > 0){
					$data[$k]['offerproductsdata'] = $this->Offer->getofferproducts($memberid,$productid,$row['priceid']);
				}
			}
		}
		return $data;
	}

	function getVariantByProductIdForAdmin($productid){
		
		$query = $this->readdb->select("pp.id,
										CONCAT(pp.price,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc 
											INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
											,'')) as memberprice,

										IF(p.isuniversal=0,IFNULL((SELECT GROUP_CONCAT(v.value SEPARATOR ', ') FROM ".tbl_productcombination." as pc 
											INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
											,''),(SELECT IF(min(price)=max(price),min(price),CONCAT(min(price),' - ',max(price))) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id)) as variantname,

										IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=p.hsncodeid),0) as tax,p.isuniversal,

										IFNULL((SELECT min(price) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id AND price>0),0) as price
									")
								->from(tbl_product." as p")
								->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
								->where("p.id",$productid)
								->get();
		return $query->result_array();
	}

	function getVariantByProductIdForVendor($productid,$vendorid){
		$data = array();
		if(isset($productid)){
			
			$this->load->model('Channel_model', 'Channel');
			$this->load->model('Stock_report_model', 'Stock');

			$channeldata = $this->Channel->getMemberChannelData($vendorid);
			$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
			$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
			$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
			
            $this->load->model('Vendor_model','Vendor');
			
			$CheckProduct = $this->getMemberProductCount($vendorid);
			
			if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
				$memberbasicsalesprice = ($memberbasicsalesprice==1)?$memberbasicsalesprice:0;

				$query = $this->readdb->select('pp.id,pp.price,p.isuniversal,p.quantitytype,pp.pricetype,
												IFNULL((SELECT integratedtax FROM '.tbl_hsncode.' WHERE id=p.hsncodeid),0) as tax,
												
												IFNULL((SELECT IF('.$memberbasicsalesprice.'=0,mvp.price,mvp.salesprice) FROM '.tbl_memberproduct.' as mp INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid="0" where mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$vendorid.'" AND IF('.$memberbasicsalesprice.'=0,mvp.price,mvp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1),pp.price) as memberprice,
												
												IFNULL((SELECT IF('.$memberbasicsalesprice.'=0,min(mpqp.price),min(mpqp.salesprice)) 
													FROM '.tbl_memberproduct.' as mp 
													INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid=0 
													INNER JOIN '.tbl_memberproductquantityprice.' as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$vendorid.'" AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1),
													IFNULL((SELECT min(pqp.price) FROM '.tbl_productquantityprices.' as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
												) as minprice,

												IFNULL((SELECT IF('.$memberbasicsalesprice.'=0,max(mpqp.price),max(mpqp.salesprice)) 
													FROM '.tbl_memberproduct.' as mp 
													INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid=0 
													INNER JOIN '.tbl_memberproductquantityprice.' as mpqp ON mpqp.membervariantpricesid=mvp.id AND mpqp.price>0
													WHERE mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$vendorid.'" AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1),
													IFNULL((SELECT max(pqp.price) FROM '.tbl_productquantityprices.' as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
												) as maxprice,
												
												IFNULL((SELECT mvp.stock FROM '.tbl_memberproduct.' as mp INNER JOIN '.tbl_membervariantprices.' as mvp ON mvp.memberid=mp.memberid AND mvp.sellermemberid="0" where mp.productid="'.$productid.'" AND mvp.priceid=pp.id AND mp.memberid="'.$vendorid.'" LIMIT 1),pp.stock) as stock,
												
												IFNULL((SELECT mvp.minimumqty FROM '.tbl_membervariantprices.' as mvp WHERE mvp.memberid="'.$vendorid.'" AND mvp.sellermemberid=0 AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM '.tbl_memberproductquantityprice.' as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0),0) > 0 AND mvp.productallow=1),0) as minimumorderqty,

												IFNULL((SELECT mvp.maximumqty FROM '.tbl_membervariantprices.' as mvp WHERE mvp.memberid="'.$vendorid.'" AND mvp.sellermemberid=0 AND mvp.priceid=pp.id AND IFNULL((SELECT count(mpqp.id) FROM '.tbl_memberproductquantityprice.' as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF('.$memberbasicsalesprice.'=0,mpqp.price,mpqp.salesprice)>0),0) > 0 AND mvp.productallow=1),0) as maximumorderqty
											')

									->from(tbl_product." as p")
									->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
									->where(array(
										"p.id"=>$productid,
										"IF(p.isuniversal=0,IF((SELECT IF(".$memberbasicsalesprice."=0,mpqp.price,mpqp.salesprice) 
											FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid 
											INNER JOIN ".tbl_memberproductquantityprice." as mpqp ON mpqp.membervariantpricesid=mvp.id AND IF(".$memberbasicsalesprice."=0,mpqp.price,mpqp.salesprice)>0
											WHERE (mp.productid=p.id AND mp.memberid='".$vendorid."') AND mvp.priceid=pp.id AND mvp.productallow = 1 LIMIT 1)>0,0,1),0)=0"=>null
											)
										)
									->get();
				$productdata = $query->row_array();
				
				if(!empty($productdata)){
					if($productdata['isuniversal']==0){
	
						$query = $this->readdb->select("pp.id,pp.id as priceid,pp.price,mvp.stock,p.quantitytype,mvp.pricetype,
													IFNULL((SELECT h.integratedtax FROM ".tbl_hsncode." as h INNER JOIN ".$this->_table." as pr ON pr.hsncodeid = h.id WHERE pr.id=pp.productid),0) as tax,
													CONCAT(IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice),' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
																					FROM ".tbl_productcombination." as pc 
																					INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=mvp.priceid)
																			,'')) as memberprice,pp.minimumorderqty,pp.maximumorderqty,

													IFNULL((SELECT GROUP_CONCAT(v.value) 
															FROM ".tbl_productcombination." as pc 
															INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=mvp.priceid)
													,'') AS variant,
													
													mvp.minimumqty as minimumorderqty,mvp.maximumqty as maximumorderqty

													")
											->from(tbl_productprices." as pp")
											->join(tbl_product." as p","p.id=pp.productid","INNER")
											->join(tbl_membervariantprices." as mvp","mvp.priceid=pp.id AND mvp.sellermemberid='0' AND mvp.memberid=".$vendorid,"INNER")
											->where(array("pp.productid"=>$productid,"mvp.productallow"=>1,"IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND IF(".$memberbasicsalesprice."=0,mpqp.price,mpqp.salesprice)>0),0) >"=>0))
											// ->order_by("IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice) ASC")
											->get();
						$variantdata = $query->result_array();
						
						if(!empty($variantdata)){
							$this->load->model('Product_prices_model','Product_prices');
							foreach($variantdata as $variant){

								if(STOCKMANAGEMENT==1){
									$ProductStock = $this->Stock->getAdminProductStock($productid,1);
									$key = array_search($variant['priceid'], array_column($ProductStock, 'priceid'));
									$stock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
								}else{
									$stock = 0;
								}
								$variantname = $variant['variant'];
								$multipleprices = $this->Product_prices->getProductQuantityPriceDataByPriceID($variant['id']);

								$data[] = array('id'=>$variant['id'],
										'priceid'=>$variant['priceid'],
										'price'=>$variant['price'],
										'quantitytype'=>$variant['quantitytype'],
										'pricetype'=>$variant['pricetype'],
										'tax'=>$variant['tax'],
										'memberprice'=>$variant['memberprice'],
										'variantname'=>$variantname,
										'stock'=>$stock,
										'minimumorderqty'=>$variant['minimumorderqty'],
										'maximumorderqty'=>$variant['maximumorderqty'],
										'referencetype'=>2,
										'multipleprices' => $multipleprices
										// 'variantdata' => $this->getVariantByProductId($productid,$vendorid,'purchase',0,$channeldata,$CheckProduct)
									);
							}
						}

					}else{

						$ProductStock = $this->Stock->getAdminProductStock($productid,0);
						$availablestock = !empty($ProductStock)?$ProductStock[0]['overallclosingstock']:0;
						$stock = (STOCKMANAGEMENT==1)?$availablestock:0;
						if(number_format($productdata['minprice'],2,'.','') == number_format($productdata['maxprice'],2,'.','')){
							$variantname = number_format($productdata['minprice'], 2, '.', '');
						}else{
							$variantname = number_format($productdata['minprice'], 2, '.', '')." - ".number_format($productdata['maxprice'], 2, '.', '');
						
						$data[] = array('id'=>0,
										'priceid'=>$productdata['id'],
										'price'=>$productdata['price'],
										'variantname'=>$variantname,
										'quantitytype'=>$productdata['quantitytype'],
										'pricetype'=>$productdata['pricetype'],
										'tax'=>$productdata['tax'],
										'memberprice'=>$productdata['memberprice'],
										'stock'=>$stock,
										'universal'=>'1',
										'minimumorderqty'=>$productdata['minimumorderqty'],
										'maximumorderqty'=>$productdata['maximumorderqty'],
										'maximumorderqty'=>$productdata['maximumorderqty'],
										'referencetype'=>2,
										// 'variantdata' => $this->getVariantByProductId($productid,$vendorid,'purchase',0,$channeldata,$CheckProduct)
									);
							}
					}
				}
			}else{
				$memberbasicsalesprice = ($memberbasicsalesprice==0)?1:$memberbasicsalesprice;
				$query = $this->readdb->select("IF(p.isuniversal=0,pp.id,0) as id,pp.id as priceid,price,pp.stock,p.isuniversal,p.								quantitytype,
				
											(SELECT pricetype FROM ".tbl_productbasicpricemapping." WHERE productid=p.id AND productpriceid=pp.id AND channelid=".$channelid." LIMIT 1) as pricetype,
											IFNULL((SELECT integratedtax FROM ".tbl_hsncode." WHERE id=hsncodeid),0) as tax,
											
											CONCAT(IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.salesprice FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.salesprice!=0 AND pbp.allowproduct=1),0),pp.price),' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
																																															FROM ".tbl_productcombination." as pc 
											INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
											,'')) as memberprice,
											
											IF(".$memberbasicsalesprice."=1,
												IFNULL((SELECT min(pqbp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp 
													INNER JOIN ".tbl_productbasicquantityprice." as pqbp ON pqbp.productbasicpricemappingid=pbp.id AND pqbp.salesprice>0
													WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.allowproduct=1),0),
												
												IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
											) as minprice,
											IF(".$memberbasicsalesprice."=1,
												IFNULL((SELECT max(pqbp.salesprice) FROM ".tbl_productbasicpricemapping." as pbp 
													INNER JOIN ".tbl_productbasicquantityprice." as pqbp ON pqbp.productbasicpricemappingid=pbp.id AND pqbp.salesprice>0
													WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND pbp.allowproduct=1),0),
												
												IFNULL((SELECT max(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0)
											) as maxprice,
											
											IF(p.isuniversal=0,IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id),''),'') as variant,

											IF(p.isuniversal=0,pp.pointsforseller,p.pointsforseller) as pointsforseller,
											IF(p.isuniversal=0,pp.pointsforbuyer,p.pointsforbuyer) as pointsforbuyer,
											
											IFNULL((SELECT pbp.minimumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as minimumorderqty,

											IFNULL((SELECT pbp.maximumqty FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct=1),0) as maximumorderqty,
									")
								->from(tbl_product." as p")
								->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
								->where("p.id=".$productid)
								->where("pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0),0),pp.id))")
								->get();
				$variantdata = $query->result_array();
					
				if(!empty($variantdata)){
					foreach($variantdata as $variant){

						if(STOCKMANAGEMENT==1){
							$ProductStock = $this->Stock->getAdminProductStock($productid,1);
							$key = array_search($variant['priceid'], array_column($ProductStock, 'priceid'));
							$stock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
						}else{
							$stock = 0;
						}

						if($variant['isuniversal']==1){
							if(number_format($variant['minprice'],2,'.','') == number_format($variant['maxprice'],2,'.','')){
								$variantname = number_format($variant['minprice'], 2, '.', '');
							}else{
								$variantname = number_format($variant['minprice'], 2, '.', '')." - ".number_format($variant['maxprice'], 2, '.', '');
							}
						}else{
							$variantname = $variant['variant'];
						}
						$data[] = array('id'=>$variant['id'],
								'priceid'=>$variant['priceid'],
								'price'=>$variant['price'],
								'quantitytype'=>$variant['quantitytype'],
								'pricetype'=>$variant['pricetype'],
								'tax'=>$variant['tax'],
								'memberprice'=>$variant['memberprice'],
								'variantname'=>$variantname,
								'stock'=>$stock,
								'universal'=>$variant['isuniversal'],
								'minimumorderqty'=>$variant['minimumorderqty'],
								'maximumorderqty'=>$variant['maximumorderqty'],
								'referencetype'=>1,
							);
					}
				}
			}
        }
		return $data;
	}

	function getMultiplePriceByPriceIdOrMemberId($productid,$priceid,$memberid,$channeldata=array(),$CheckProduct=array()){
		$data = array();
		if(isset($priceid)){
			
			$this->load->model('Channel_model', 'Channel');
			$this->load->model('Stock_report_model', 'Stock');
			$this->load->model('Product_prices_model', 'Product_prices');

			if(empty($channeldata)){
				$channeldata = $this->Channel->getMemberChannelData($memberid);
			}
			$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
			$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
			$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
			
            $this->load->model('Member_model','Member');
			
			if(empty($CheckProduct)){
				$CheckProduct = $this->getMemberProductCount($memberid);
			}
			if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
				$data = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$priceid);
			}else{
				$data = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($channelid,$priceid,$productid);
			}
        }
		return $data;
	}


	public function exportproduct(){
        $PostData = $this->input->get();
		$this->load->model('Stock_report_model', 'Stock');
		$this->load->model('Channel_model', 'Channel');
		$memberid = $PostData['memberid'];
		$brandid = isset($PostData['brandid'])?$PostData['brandid']:0;
		$categoryid = $PostData['categoryid'];
		
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$currentsellerid = (empty($this->session->userdata(base_url().'ADMINID')))?$currentsellerid:-1;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0; 
		if($channelid == VENDORCHANNELID){
			$producttype = 2;
		}else{
			$producttype = 0;
		}
		$data = array();
		$this->readdb->select("CONCAT(p.id,'|',IFNULL(pp.id,0)) as productcode,
							p.id as productid,pp.id as priceid,
							IFNULL(seller.membercode,(SELECT companycode FROM ".tbl_settings.")) as sellermembercode,
							m.membercode,
							IFNULL(seller.name,'Company') as sellername,
							m.name as membername,
							p.name as productname,
							IFNULL(
								(SELECT GROUP_CONCAT(CONCAT(a.variantname,'#',v.value) SEPARATOR ' | ')  
								FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid INNER JOIN ".tbl_attribute." as a ON a.id=v.attributeid WHERE pc.priceid=mvp.priceid),'') as variantname,
                            mvp.price as universalprice,

							IFNULL((SELECT min(mpqp.price) FROM ".tbl_memberproductquantityprice." as mpqp 
									WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0 LIMIT 1),0) as minprice,

							IFNULL((SELECT max(mpqp.price) FROM ".tbl_memberproductquantityprice." as mpqp 
									WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0 LIMIT 1),0) as maxprice,

							mvp.productallow as productallow,
							
							mvp.minimumqty,mvp.maximumqty,mvp.discountpercent,mvp.discountamount
						");
                            
        if ($memberbasicsalesprice==1) {
            $this->readdb->select("
								IFNULL((SELECT min(mpqp.salesprice) FROM ".tbl_memberproductquantityprice." as mpqp 
										WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0 LIMIT 1),0) as minsalesprice,

								IFNULL((SELECT max(mpqp.salesprice) FROM ".tbl_memberproductquantityprice." as mpqp 
										WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0 LIMIT 1),0) as maxsalesprice
							");
		}
		
		$this->readdb->from(tbl_product." as p");
		$this->readdb->join(tbl_member." as m", "m.id=".$memberid,"INNER");
		$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id AND pp.id IN (SELECT priceid from ".tbl_membervariantprices." WHERE memberid = m.id)","INNER");
		$this->readdb->join(tbl_membervariantprices." as mvp", "mvp.memberid=m.id AND mvp.priceid=pp.id AND (mvp.sellermemberid='".$currentsellerid."' OR '".$currentsellerid."'='-1')","INNER");
		$this->readdb->join(tbl_member." as seller", "seller.id = (SELECT mp.mainmemberid  FROM ".tbl_membermapping." as mp WHERE submemberid = ".$memberid.")","LEFT");
		$this->readdb->where("p.status=1 AND p.producttype = '".$producttype."' AND (p.brandid=".$brandid." OR ".$brandid."=0) AND (FIND_IN_SET(p.categoryid,'".$categoryid."')>0 OR '".$categoryid."'='')");
		if($currentsellerid!=-1){

		}
		$this->readdb->group_by("p.id, mvp.priceid");
        $this->readdb->order_by("p.id DESC");
        $query = $this->readdb->get();
		
		$data = array();
		foreach($query->result_array() as $row){
			$ProductStock = $this->Stock->getVariantStock($memberid,$row['productid'],'','',$row['priceid'],1,$channelid);
			$currentstock = (!empty($ProductStock[0]['overallclosingstock']))?$ProductStock[0]['overallclosingstock']:0;

			/* if(STOCK_MANAGE_BY==0){
                $productdata = $this->Stock->getAdminProductStock($row['productid'],0);
                $currentstock = (!empty($productdata)?$productdata[0]['openingstock']:0);
            }else{
                $productdata = $this->Stock->getAdminProductFIFOStock($row['productid'],0);
                $currentstock = (!empty($productdata[0]['openingstock'])?$productdata[0]['openingstock']:0);
            } */
			
			if(number_format($row['minprice'],2,'.','') == number_format($row['maxprice'],2,'.','')){
				$price = numberFormat($row['minprice'], 2, ',');
			}else{
				$price = numberFormat($row['minprice'], 2, ',')." - ".numberFormat($row['maxprice'], 2, ',');
			}

            if ($memberbasicsalesprice==1) {

				if(number_format($row['minsalesprice'],2,'.','') == number_format($row['maxsalesprice'],2,'.','')){
					$salesprice = numberFormat($row['minsalesprice'], 2, ',');
				}else{
					$salesprice = numberFormat($row['minsalesprice'], 2, ',')." - ".numberFormat($row['maxsalesprice'], 2, ',');
				}

                $data[] = array('productcode'=>$row['productcode'],
                                'sellercode'=>$row['sellermembercode'],
                                'membercode'=>$row['membercode'],
                                'sellername'=>$row['sellername'],
                                'membername'=>$row['membername'],
                                'productname'=>$row['productname'],
                                'variant'=>$row['variantname'],
                                'price'=>$price,
                                'allowproduct'=>$row['productallow'],
								'stock'=>$currentstock,
								'minimumqty'=>($row['minimumqty']>0)?$row['minimumqty']:"",
								'maximumqty'=>($row['maximumqty']>0)?$row['maximumqty']:"",
								'discountpercent'=>($row['discountpercent']>0)?$row['discountpercent']:"",
								'discountamount'=>($row['discountamount']>0)?$row['discountamount']:"",
                                'salesprice'=>$salesprice
							);
            }else{
				$data[] = array('productcode'=>$row['productcode'],
                                'sellercode'=>$row['sellermembercode'],
                                'membercode'=>$row['membercode'],
                                'sellername'=>$row['sellername'],
                                'membername'=>$row['membername'],
                                'productname'=>$row['productname'],
                                'variant'=>$row['variantname'],
                                'price'=>$price,
                                'allowproduct'=>$row['productallow'],
								'stock'=>$currentstock,
								'minimumqty'=>($row['minimumqty']>0)?$row['minimumqty']:"",
								'maximumqty'=>($row['maximumqty']>0)?$row['maximumqty']:"",
								'discountpercent'=>($row['discountpercent']>0)?$row['discountpercent']:"",
								'discountamount'=>($row['discountamount']>0)?$row['discountamount']:"",
							);
			}
		}
       
        if ($memberbasicsalesprice==1) {
            $headings = array('Product Code','Seller Code',Member_label.' Code','Seller Name',Member_label.' Name','Product Name','Variant','Price','Allow Product (1=>yes)','Current Stock','Minimum Quantity','Maximum Quantity','Disc. (%)','Disc. (<?=CURRENCY_CODE?>)','Sales Price');
        }else{
            $headings = array('Product Code','Seller Code',Member_label.' Code','Seller Name',Member_label.' Name','Product Name','Variant','Price','Allow Product (1=>yes)','Current Stock','Minimum Quantity','Maximum Quantity','Disc. (%)','Disc. (<?=CURRENCY_CODE?>)');
        }
		
        $this->general_model->exporttoexcel($data,"A1:DD1","Product Price",$headings,"Product Price.xls",array("H","O"));
	}
	
	public function importproductprice(){
		$PostData = $this->input->post();
        
        $this->load->model('Member_model', 'Member');

        $postmemberid = (!empty($PostData['memberid']))?$PostData['memberid']:0;
		$MEMBERID = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):$postmemberid;
		
		$ADMINID = (!empty($this->session->userdata(base_url().'ADMINID')))?$this->session->userdata(base_url().'ADMINID'):0;
				
		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($MEMBERID);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');

		if(!empty($MEMBERID)){
			
			if($ADMINID!=0){
				if($channelid == VENDORCHANNELID){
					$producttype = 2;
				}else{
					$producttype = 0;
				}

				$query = $this->readdb->select("CONCAT(p.id,'|',IFNULL(pp.id,0)) as priceid")
								->from(tbl_product." as p")
								->join(tbl_productprices." as pp", "pp.productid=p.id","LEFT")
								->where("FIND_IN_SET(pp.id, (SELECT GROUP_CONCAT(productpriceid) FROM ".tbl_productbasicpricemapping." WHERE channelid='".$channelid."' AND salesprice > 0 AND allowproduct = 1))>0")
								->where("p.status=1 AND p.producttype=".$producttype)
								->group_by("p.id, pp.id")
								->order_by("p.id DESC")
								->get();
			}else{

				$query = $this->readdb->select("CONCAT(p.id,'|',IFNULL(pp.id,0)) as priceid")
								->from(tbl_product." as p")
								->join(tbl_member." as m", "m.id=".$MEMBERID,"INNER")
								->join(tbl_memberproduct." as mp", "mp.memberid=m.id AND mp.productid=p.id","INNER")
								->join(tbl_productprices." as pp", "pp.productid=p.id AND pp.id IN (SELECT priceid from ".tbl_membervariantprices." WHERE memberid = m.id)","LEFT")
								->join(tbl_membervariantprices." as mvp", "mvp.memberid=mp.memberid AND mvp.priceid=pp.id","LEFT")
								->where("p.status=1 AND p.producttype=0")
								->group_by("p.id, mvp.priceid")
								->order_by("p.id DESC")
								->get();
			}
			$memberpricedata = $query->result_array();
			$memberpricedata = array_column($memberpricedata,'priceid');

		}
		
		$this->Member->_fields = "membercode";
		$memberdata = $this->Member->getRecordByID();
		$memberdata = array_filter(array_column($memberdata,'membercode'));

        if($_FILES["attachment"]['name'] != ''){

			$FileNM = uploadFile('attachment', 'IMPORT_FILE', IMPORT_PATH);
			            
            if($FileNM !== 0){
                if($FileNM==2){
					echo 3;//image not uploaded
					exit;
				}
            }else{
                echo 2;//INVALID ATTACHMENT FILE
                exit;
            }

            $insertmemberproductdata = $updatememberproductdata = $insertmembervariantpricesdata = $updatemembervariantpricesdata = array();
            $file_data = $this->upload->data();
            $file_path =  IMPORT_PATH.$FileNM;

            $this->load->library('excel');
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader =PHPExcel_IOFactory::createReader($inputFileType);     //For excel 2003 
            //$objReader= PHPExcel_IOFactory::createReader('Excel2007');    // For excel 2007     

            //Set to read only
            $objReader->setReadDataOnly(true);        

            //Load excel file
            $objPHPExcel=$objReader->load($file_path);
            

            $totalrows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Number of rows avalable in excel        
            
            $objWorksheet=$objPHPExcel->setActiveSheetIndex(0);
            //print_r($objWorksheet);
            
            $column0 = $objWorksheet->getCellByColumnAndRow(0,1)->getValue();
            $column1 = $objWorksheet->getCellByColumnAndRow(1,1)->getValue();
            $column2 = $objWorksheet->getCellByColumnAndRow(2,1)->getValue();
            $column3 = $objWorksheet->getCellByColumnAndRow(3,1)->getValue();
            $column4 = $objWorksheet->getCellByColumnAndRow(4,1)->getValue();
            $column5 = $objWorksheet->getCellByColumnAndRow(5,1)->getValue();
            $column6 = $objWorksheet->getCellByColumnAndRow(6,1)->getValue();
            $column7 = $objWorksheet->getCellByColumnAndRow(7,1)->getValue();
			$column8 = $objWorksheet->getCellByColumnAndRow(8,1)->getValue();
			$column9 = $objWorksheet->getCellByColumnAndRow(9,1)->getValue();
			$column10 = $objWorksheet->getCellByColumnAndRow(10,1)->getValue();
			$column11 = $objWorksheet->getCellByColumnAndRow(11,1)->getValue();
			$column12 = $objWorksheet->getCellByColumnAndRow(12,1)->getValue();
			$column13 = $objWorksheet->getCellByColumnAndRow(13,1)->getValue();
                    
            if($column0=="Product Code" && $column1=="Seller Code" && $column2=="Member Code" && $column3=="Seller Name" && 
                $column4=="Member Name" && $column5=="Product Name" && $column6=="Variant" && $column7=="Price" && $column8=="Allow Product (1=>yes)" && $column9=="Minimum Quantity" && $column10=="Maximum Quantity" && $column11=="Disc. (%)" && $column12=="Disc. (<?=CURRENCY_CODE?>)"){

                if($totalrows>1){
                    $error = $selleridarr = $buyerchannelidarr = $buyermemberidarr = array();

					$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
					if(!empty($this->session->userdata(base_url().'MEMBERID'))){
                        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
					}else{
                        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
                    }
                    $updateonsalesprice = ($memberbasicsalesprice==1 && in_array($rollid,$edit)==true)?1:0;
                    
                    for($i=2;$i<=$totalrows;$i++){

                        $createddate = $this->general_model->getCurrentDateTime();
                        $productpriceid = trim($objWorksheet->getCellByColumnAndRow(0,$i)->getValue());
                        $sellercode = trim($objWorksheet->getCellByColumnAndRow(1,$i)->getValue());
                        $membercode = trim($objWorksheet->getCellByColumnAndRow(2,$i)->getValue());
                        $price = trim($objWorksheet->getCellByColumnAndRow(7,$i)->getValue());
                        $productallow = trim($objWorksheet->getCellByColumnAndRow(8,$i)->getValue());
                        $productallow = (!empty($productallow))?1:0;
						$minimumqty = trim($objWorksheet->getCellByColumnAndRow(9,$i)->getValue());
						$maximumqty = trim($objWorksheet->getCellByColumnAndRow(10,$i)->getValue());
						$discountpercent = trim($objWorksheet->getCellByColumnAndRow(11,$i)->getValue());
						$discountamount = trim($objWorksheet->getCellByColumnAndRow(12,$i)->getCalculatedValue());
                        $salesprice = trim($objWorksheet->getCellByColumnAndRow(13,$i)->getValue());

                        $sellermemberid = $buyerchannelid = $buyermemberid = 0;

                        $isvalid = 1;
                        if(empty($productpriceid)){
                            echo "Row no. ".$i." product code is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }else if($MEMBERID!=0 && !in_array($productpriceid,$memberpricedata)){
                            echo "Row no. ".$i." product code not exist !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }

                        if(empty($price)){
                            echo "Row no. ".$i." product price is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
						}
						
						if(!in_array($membercode,$memberdata)){
                            echo "Row no. ".$i." member code does not match !<br>";
                            $isvalid = 0;
                            $error[] = $i;
						}
						
						if($sellercode==$membercode){
                            echo "Row no. ".$i." seller or member code can not be same !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }

                        if(isset($selleridarr[$sellercode])){
                            $sellermemberid = $selleridarr[$sellercode];
                        }else{
                            $Id = $this->Member->getIdFromCode($sellercode);
                            if(!empty($Id)){
                                $selleridarr[$sellercode] = $Id['id'];
                                $sellermemberid = $selleridarr[$sellercode];
                            }
                        }

                        if(isset($buyermemberidarr[$membercode])){
                            $buyermemberid = $buyermemberidarr[$membercode];
                            $buyerchannelid = $buyerchannelidarr[$membercode];
                        }else{
                            $Id = $this->Member->getIdFromCode($membercode);
                            if(!empty($Id)){
                                $buyermemberidarr[$membercode] = $Id['id'];
                                $buyerchannelidarr[$membercode] = $Id['channelid'];

                                $buyerchannelid = $buyerchannelidarr[$membercode];
                                $buyermemberid = $buyermemberidarr[$membercode];
                            }
                        }
						
						if($MEMBERID!=0){
							if(intval($MEMBERID)!=intval($sellermemberid) && intval($MEMBERID)!=intval($buyermemberid)){
								echo "Row no. ".$i." seller or member code does not match with login member !<br>";
								$isvalid = 0;
								$error[] = $i;
							}else if(!empty($postmemberid) && $postmemberid!=$buyermemberid && $MEMBERID!=$sellermemberid){
								echo "Row no. ".$i." member code does not match with current member !<br>";
								$isvalid = 0;
								$error[] = $i;
							}
						}
                        
                        
                        if($isvalid){
                            $productpriceid = array_filter(explode('|',$productpriceid));

                            if($MEMBERID==$buyermemberid && !empty($CHANNELID)){
                                $this->Member->_table = tbl_membervariantprices;
                                $this->Member->_fields = 'id';
                                $this->Member->_where = "memberid=".$buyermemberid." AND sellermemberid=".$sellermemberid." AND priceid=".$productpriceid[1];
                                $MemberProduct = $this->Member->getRecordsByID();
    
                                if(!empty($MemberProduct)){
                                    if($updateonsalesprice){
                                        $updatemembervariantpricesdata[] = array('salesprice'=>$salesprice,
                                                                                    'productallow'=>$productallow,
                                                                                    'modifieddate'=>$createddate,
                                                                                    'id'=>$MemberProduct['id']);
                                    }else{
                                        $updatemembervariantpricesdata[] = array('productallow'=>$productallow,
                                                                                'modifieddate'=>$createddate,
                                                                                'id'=>$MemberProduct['id']);
                                    }
                                    
                                }
                            }else{
                                $this->Member->_table = tbl_memberproduct;
                                $this->Member->_fields = 'id';
                                $this->Member->_where = "memberid=".$buyermemberid." AND sellermemberid=".$sellermemberid." AND productid=".$productpriceid[0];
                                $MemberProduct = $this->Member->getRecordsByID();

                                if(empty($MemberProduct)){
									
									$productidkeyarr = array_keys(array_column($insertmemberproductdata, 'productid'), $productpriceid[0]);
									if(!empty($productidkeyarr)){
										foreach($productidkeyarr as $key){
											if($insertmemberproductdata[$key]['productid']!=$productpriceid[0] && $insertmemberproductdata[$key]['sellermemberid']!=$sellermemberid && $insertmemberproductdata[$key]['memberid']!=$buyermemberid){

												$insertmemberproductdata[] = array('sellermemberid'=>$sellermemberid,
																		'memberid'=>$buyermemberid,
																		'productid'=>$productpriceid[0],
																		'createddate'=>$createddate,
																		'modifieddate'=>$createddate);

											}

										}
									}else{
										$insertmemberproductdata[] = array('sellermemberid'=>$sellermemberid,
																			'memberid'=>$buyermemberid,
																			'productid'=>$productpriceid[0],
																			'createddate'=>$createddate,
																			'modifieddate'=>$createddate);
									}	
					            }


                                $this->Member->_table = tbl_membervariantprices;
                                $this->Member->_fields = 'id';
                                $this->Member->_where = "memberid=".$buyermemberid." AND sellermemberid=".$sellermemberid." AND priceid=".$productpriceid[1];
                                $MemberProduct = $this->Member->getRecordsByID();

                                if(!empty($MemberProduct)){
                                    /* if ($updateonsalesprice) {
                                        $updatemembervariantpricesdata[] = array('price'=>$price,
                                                                            'salesprice'=>$salesprice,
                                                                            'productallow'=>$productallow,
                                                                            'modifieddate'=>$createddate,
                                                                            'id'=>$MemberProduct['id']);
                                    }else{
                                        $updatemembervariantpricesdata[] = array('price'=>$price,
                                                                                    'productallow'=>$productallow,
                                                                                    'modifieddate'=>$createddate,
                                                                                    'id'=>$MemberProduct['id']);
									} */
									$pricearray = array('id'=>$MemberProduct['id'],
														'price'=>$price,
														'productallow'=>$productallow,
														'minimumqty'=>$minimumqty,
														'maximumqty'=>$maximumqty,
														'discountpercent'=>$discountpercent,
														'discountamount'=>$discountamount,
														'modifieddate'=>$createddate,
													);
									if ($updateonsalesprice) {
										$pricearray['salesprice'] = $salesprice;
									}
									$updatemembervariantpricesdata[] = $pricearray;
                                }else{
                                    /* if ($updateonsalesprice) {
                                        $insertmembervariantpricesdata[] = array('sellermemberid'=>$sellermemberid,
                                                                                'memberid'=>$buyermemberid,
                                                                                'channelid'=>$buyerchannelid,
                                                                                'priceid'=>$productpriceid[1],
                                                                                'salesprice'=>$salesprice,
                                                                                'price'=>$price,
                                                                                'productallow'=>$productallow,
                                                                                'createddate'=>$createddate,
                                                                                'modifieddate'=>$createddate);
                                    }else{
                                        $insertmembervariantpricesdata[] = array('sellermemberid'=>$sellermemberid,
                                                                                'memberid'=>$buyermemberid,
                                                                                'channelid'=>$buyerchannelid,
                                                                                'priceid'=>$productpriceid[1],
                                                                                'price'=>$price,
                                                                                'productallow'=>$productallow,
                                                                                'createddate'=>$createddate,
                                                                                'modifieddate'=>$createddate);
									} */
									$pricearray = array('sellermemberid'=>$sellermemberid,
														'memberid'=>$buyermemberid,
														'channelid'=>$buyerchannelid,
														'priceid'=>$productpriceid[1],
														'price'=>$price,
														'productallow'=>$productallow,
														'minimumqty'=>$minimumqty,
														'maximumqty'=>$maximumqty,
														'discountpercent'=>$discountpercent,
														'discountamount'=>$discountamount,
														'createddate'=>$createddate,
														'modifieddate'=>$createddate
													);
									if ($updateonsalesprice) {
										$pricearray['salesprice'] = $salesprice;
									}
									$insertmembervariantpricesdata[] = $pricearray;
                                }
                            }
                        }
					}
					
                    if(empty($error)){
                        if(!empty($insertmemberproductdata)){
                            $this->Member->_table = tbl_memberproduct;
                            $this->Member->add_batch($insertmemberproductdata);
                        }
                        if(!empty($updatememberproductdata)){
                            $this->Member->_table = tbl_memberproduct;
                            $this->Member->edit_batch($updatememberproductdata,'id');
                        }
                        if(!empty($insertmembervariantpricesdata)){
                            $this->Member->_table = tbl_membervariantprices;
                            $this->Member->add_batch($insertmembervariantpricesdata);
                        }
                        if(!empty($updatemembervariantpricesdata)){
                            $this->Member->_table = tbl_membervariantprices;
                            $this->Member->edit_batch($updatemembervariantpricesdata,'id');
                        }
                        echo 1;
                    }
                    
                }else{
                    echo 5;
				}
				unlinkfile('', $FileNM, IMPORT_PATH);
            }else{
                echo 4;
                unlinkfile('', $FileNM, IMPORT_PATH);
                exit;
            }
        }

	}

	function getProductDataForExport($categoryid,$brandid,$producttype,$MEMBERID=0,$CHANNELID=0){
		$this->readdb->select('pc.name as categoryname,p.name,p.slug,p.shortdescription,p.description,
							(SELECT hsncode FROM '.tbl_hsncode.' as h WHERE h.id=p.hsncodeid) as hsccode,
							p.priority,IFNULL((SELECT name FROM '.tbl_brand.' WHERE id=p.brandid),"") brand,
							(SELECT GROUP_CONCAT(pi.filename SEPARATOR "|") FROM '.tbl_productimage.' as pi WHERE pi.productid=p.id) as image,
							p.quantitytype,p.pointsforseller,p.pointsforbuyer,
							IFNULL((SELECT GROUP_CONCAT(tag) FROM '.tbl_producttag.' WHERE id IN (SELECT tagid FROM '.tbl_producttagmapping.' WHERE productid=p.id)),"") as tag,p.productdisplayonfront,
							p.status,p.metatitle,p.metakeyword,p.metadescription');

		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_productcategory." as pc","pc.id=p.categoryid","INNER");
		$this->readdb->where("p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'");
		$this->readdb->where("(FIND_IN_SET(p.categoryid, '".$categoryid."')>0 OR '".$categoryid."'='')");
		$this->readdb->where("(FIND_IN_SET(p.brandid, '".$brandid."')>0 OR '".$brandid."'='')");
		$this->readdb->where("(p.producttype='".$producttype."' OR '".$producttype."'='')");
		$this->readdb->group_by("p.id");
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();
		return $query->result_array();
	}

	function getProductPriceDataForExport($categoryid,$brandid,$producttype,$MEMBERID=0,$CHANNELID=0){
		$this->load->model('Stock_report_model', 'Stock');
		$this->load->model('Product_prices_model', 'Product_prices');
		$this->readdb->select("CONCAT(p.id,'|',IFNULL(pp.id,0)) as priceid,p.id,
							p.name as productname,
							IFNULL(
								(SELECT GROUP_CONCAT(CONCAT(a.variantname,'#',v.value) SEPARATOR ' | ')  
								FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid INNER JOIN ".tbl_attribute." as a ON a.id=v.attributeid WHERE pc.priceid=pp.id),'') as variantname,
							pp.price,p.isuniversal,pp.id as priceId,
							pp.sku,pp.barcode,pp.minimumorderqty,pp.maximumorderqty,pp.minimumstocklimit,pp.weight,pp.pricetype,
							
							IF(IFNULL((SELECT count(pbp.id) FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productid=pp.productid AND pbp.productpriceid=pp.id GROUP BY productpriceid),0)>0,0,1) as addpriceinpricelist");
                            
		$this->readdb->from(tbl_product." as p");
		$this->readdb->join(tbl_productprices." as pp", "pp.productid=p.id","INNER");
		$this->readdb->where("p.memberid='".$MEMBERID."' AND  p.channelid='".$CHANNELID."'");
		$this->readdb->where("(FIND_IN_SET(p.categoryid, '".$categoryid."')>0 OR '".$categoryid."'='')");
		$this->readdb->where("(FIND_IN_SET(p.brandid, '".$brandid."')>0 OR '".$brandid."'='')");
		$this->readdb->where("(p.producttype='".$producttype."' OR '".$producttype."'='')");
        $this->readdb->group_by("p.id, pp.id");
        $this->readdb->order_by("p.name ASC");
        $query = $this->readdb->get();
		
		$data = array();
		foreach($query->result_array() as $row){
			/* if($row['isuniversal']==0){
				$productdata = $this->Stock->getAdminProductStock($row['id'],1,'','',$row['priceId'],'','',0,$MEMBERID,$CHANNELID);
			}else{
				$productdata = $this->Stock->getAdminProductStock($row['id'],0,'','',0,$MEMBERID,$CHANNELID);
			}
			$currentstock = (!empty($productdata[0]['openingstock']))?$productdata[0]['openingstock']:0; */
			if(STOCK_MANAGE_BY==0){
                $productdata = $this->Stock->getAdminProductStock($row['id'],0);
                $currentstock = (!empty($productdata)?$productdata[0]['openingstock']:0);
            }else{
                $productdata = $this->Stock->getAdminProductFIFOStock($row['id'],0);
                $currentstock = (!empty($productdata[0]['openingstock'])?$productdata[0]['openingstock']:0);
            }
            $pricesdata = $this->Product_prices->getProductQuantityPriceDataByPriceID($row['priceId'],array("id"=>"ASC"));

			$pricearray=array();
			if(!empty($pricesdata)){
				foreach($pricesdata as $i=>$price){
					$pricearray['price'.($i+1)] = $price['price'];
					$pricearray['quantity'.($i+1)] = $price['quantity'];
					$pricearray['discount'.($i+1)] = $price['discount']; 
				}
			}
			$data[] = array_merge(array('priceid'=>$row['priceid'],
							'productname'=>$row['productname'],
							'variantname'=>$row['variantname'],
							'stock'=>$currentstock,
							'sku'=>$row['sku'],
							'barcode'=>$row['barcode'],
							'minimumorderqty'=>$row['minimumorderqty'],
							'maximumorderqty'=>$row['maximumorderqty'],
							'minimumstocklimit'=>$row['minimumstocklimit'],
							'weight'=>$row['weight'],
							'addpriceinpricelist'=>$row['addpriceinpricelist'],
							'pricetype'=>$row['pricetype']
						),$pricearray);
		}
		return $data;
	}

	function getTotalProductCount($memberid){
		
		$query = $this->readdb->select("COUNT(mp.productid) as totalproduct")
							->from(tbl_memberproduct." as mp")
							->where("mp.sellermemberid=IFNULL((SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."'),0) AND mp.memberid='".$memberid."'")
							->get();
        
		return $query->row_array();
	}

	function getActiveRegularOrRawProducts($type=0,$MEMBERID = 0,$CHANNELID=0,$return="withoutvariant",$variant=""){

		if($type==1){
			//Raw Products Return
			$where = "(p.producttype = 2)";
		}else if($type==2){
			//Regular, Raw and semi-finish Products Return
			$where = "(p.producttype = 0 OR p.producttype = 2 OR p.producttype = 3)";
		}else if($type==3){
			//Regular and semi-finish Products Return
			$where = "(p.producttype = 0 OR p.producttype = 3)";
		}else{
			//Regular and Raw Products Return
			$where = "(p.producttype = 0 OR p.producttype = 2)";
		}

		$query = $this->readdb->select('id,CONCAT(p.name," | ",(SELECT name FROM '.tbl_productcategory.' WHERE id=p.categoryid)) as name,
		IFNULL((SELECT pi.filename FROM '.tbl_productimage.' as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),"") as image,p.discount,p.isuniversal')
							
						->from($this->_table.' as p')
						->where("status=1 AND ".$where." AND p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'")
						->order_by('name ASC')
						->get();
       
		if($query->num_rows() > 0) {
			$productdata = $query->result_array();
			if($return=="withvariant"){
				foreach($productdata as $k=>$product){
					if($variant=="admin_variant"){
						$productdata[$k]['variantdata'] = $this->getVariantByProductIdForAdmin($product['id']);
					}else{
						$productdata[$k]['variantdata'] = $this->getProductVariantByINOUTProductId($product['id'],$type);
					}
				}
			}
			return $productdata;
		} else {
			return array();
		}
	}

	function getProductVariantByINOUTProductId($productid,$type=0,$returnseprateprice=1){
		
		if($type==1){
			//Raw Products Return
			$where = "(p.producttype = 2)";
		}else if($type==2){
			//Regular, Raw and semi-finish Products Return
			$where = "(p.producttype = 0 OR p.producttype = 2 OR p.producttype = 3)";
		}else{
			//Regular and Raw Products Return
			$where = "(p.producttype = 0 OR p.producttype = 2)";
		}

		$query = $this->readdb->select("pp.id,
				IFNULL((SELECT min(pqp.price) FROM ".tbl_productquantityprices." as pqp WHERE pqp.productpricesid=pp.id AND pqp.price>0),0) as price,
										
										IF(p.isuniversal=0,IFNULL((SELECT GROUP_CONCAT(v.value SEPARATOR ', ') FROM ".tbl_productcombination." as pc 
											INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=pp.id)
											,''),(SELECT IF(min(price)=max(price),min(price),CONCAT(min(price),' - ',max(price))) FROM ".tbl_productquantityprices." WHERE productpricesid=pp.id)) as variantname
									")
								->from(tbl_product." as p")
								->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
								->where("p.id = ".$productid." AND ".$where)
								->get();
		$variantdata = $query->result_array();
		//echo $this->readdb->last_query();exit;
		$returnArr = array();
		if(!empty($variantdata)){

			if($returnseprateprice==1){
				$this->load->model("Product_process_model","Product_process");
				foreach($variantdata as $value){

					$average_price = $latest_price = $low_price = 0;
					$fifo_price = $value['price'];

					$orderproductsforfifo = $this->Product_process->getOrderProductsForFIFO($productid,$value['id']);
				
					if(!empty($orderproductsforfifo)){
						$priceArray = array_column($orderproductsforfifo, "originalprice");
						$totalqty = array_sum(array_column($orderproductsforfifo, "qty"));
						
						$fifo_price = !empty($priceArray[0])?$priceArray[0]:0;

						$price = 0;
						foreach($orderproductsforfifo as $orderproduct){
							$price += ($orderproduct['originalprice']>0)?($orderproduct['originalprice'] * $orderproduct['qty']):0;
						}
						$price = ($price != 0)?(($price * (-1)) / ($totalqty * (-1))):0;
						$average_price = number_format($price,2,'.','');
						
						$price = $priceArray[count($orderproductsforfifo)-1];
						$latest_price = number_format((!empty($price)?$price:0),2,'.','');

						$price = min($priceArray);
						$low_price = number_format((!empty($price)?$price:0),2,'.','');
					}
					
					$returnArr[] = array("id"=>$value['id'],
										"price"=>$value['price'],
										"variantname"=>$value['variantname'],
										"fifo_price"=>$fifo_price,
										"average_price"=>$average_price,
										"latest_price"=>$latest_price,
										"low_price"=>$low_price,
									);
				}
			}else{
				$returnArr = $variantdata;
			}
		}

		return $returnArr;
	}
	function getChannelList($type=''){

		if($type=='all'){
			$where = '1=1';
		}else if($type=='notdisplayvendorchannel'){
			$where = "id!=".VENDORCHANNELID;
		}else if($type=='notdisplayguestorvendorchannel'){
			$ids = implode(",",array(VENDORCHANNELID,GUESTCHANNELID));
			$where = "id NOT IN (".$ids.")";
		}else if($type=='onlyvendor'){
			$where = "id = ".VENDORCHANNELID;
		}else{
			$where = "id!=".GUESTCHANNELID;
		}
		$query = $this->readdb->select("id,name,color")
							->from($this->_table)
							->where($where)
							->order_by("priority ASC")
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
	}
	function getSellerChannelByChannel($channelid){

		$query = $this->readdb->query("(SELECT 0 as id,'Company' as name FROM ".tbl_channel." as c WHERE IFNULL((SELECT 1 FROM ".tbl_channel." as c2 WHERE c2.id=".$channelid." AND FIND_IN_SET(0,c2.multiplememberchannel)),0)=1)
									UNION
									(SELECT c.id,c.name FROM ".tbl_channel." as c WHERE ".$channelid."!=".GUESTCHANNELID." AND c.id in (SELECT max(c2.id) FROM channel as c2 WHERE c2.priority<(SELECT c1.priority FROM channel as c1 WHERE c1.id=".$channelid.")))
									UNION
									(SELECT c.id,c.name FROM ".tbl_channel." as c WHERE FIND_IN_SET(c.id,(SELECT c2.multiplememberchannel FROM ".tbl_channel." as c2 WHERE c2.id=".$channelid."))>0 ORDER BY c.priority)");
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array(array('id'=>0,'name'=>'Company'));
		}	
	}
	function getActiveAttribute($MEMBERID=0,$CHANNELID=0){
		$query = $this->readdb->select("id,name")
							->from($this->_table)
							->where("memberid='".$MEMBERID."' AND chaneelid='".$CHANNELID."'")
							->order_by("priority", 'ASC')
							->get();
		
		return $query->result_array();
	}

	function getActiveProductList($limit,$offset=0,$filterarray='[]',$type='data'){

		$filterarray = json_decode($filterarray);
		
		//print_r($filterarray);exit;
		if($type=='data'){
			$select = "SELECT p.id,p.name,p.slug,p.singlelink,p.keyfeatures,p.applypriceformat,
									pp.price as price,
									pp.discount as discount,
									IFNULL((SELECT ROUND(SUM(CASE
												 WHEN rating='0.5' THEN IF(rating='0.5',1,0)*0.5
												 WHEN rating='1.0' THEN IF(rating='1.0',1,0)*1.0 
												 WHEN rating='1.5' THEN IF(rating='1.5',1,0)*1.5 
												 WHEN rating='2.0' THEN IF(rating='2.0',1,0)*2.0 
												 WHEN rating='2.5' THEN IF(rating='2.5',1,0)*2.5 
												 WHEN rating='3.0' THEN IF(rating='3.0',1,0)*3.0 
												 WHEN rating='3.5' THEN IF(rating='3.5',1,0)*3.5
												 WHEN rating='4.0' THEN IF(rating='4.0',1,0)*4.0
												 WHEN rating='4.5' THEN IF(rating='4.5',1,0)*4.5 
												 WHEN rating='5.0' THEN IF(rating='5.0',1,0)*5.0 
												END)/COUNT(id),1) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1),0) as productreview,
									IFNULL((SELECT COUNT(pr.id) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.type=1),0) as productreviewcount";
		}else{
			$select = "SELECT p.id";
		}
		$select .= " FROM ".$this->_table." as p 
					INNER JOIN (SELECT MIN(pp2.price) as price,pp2.productid,pp2.status,pp2.discount FROM ".tbl_productprice." as pp2 INNER JOIN ".tbl_product." as p1 on pp2.productid=p1.id WHERE pp2.productid=p1.id AND pp2.status=1 AND IFNULL((SELECT (SELECT COUNT(pmd.id) FROM ".tbl_productmappingdata." as pmd WHERE pmd.productmappingid=pm.id) FROM ".tbl_productmapping." as pm WHERE pm.productid=pp2.productid AND pm.carmodelid=pp2.carmodelid),0)>=IF(p1.applypriceformat=1 AND p1.displaypairprice=1,2,1) GROUP BY pp2.productid) as pp ON pp.productid=p.id AND pp.status=1";
		/*$this->readdb->select("p.id,p.name,p.slug,p.singlelink,
									ROUND((pp.price+(pp.price*IFNULL(hc.integratedtax,0)/100)),2) as price,
									IFNULL((SELECT ROUND(SUM(CASE
												 WHEN rating='0.5' THEN IF(rating='0.5',1,0)*0.5
												 WHEN rating='1.0' THEN IF(rating='1.0',1,0)*1.0 
												 WHEN rating='1.5' THEN IF(rating='1.5',1,0)*1.5 
												 WHEN rating='2.0' THEN IF(rating='2.0',1,0)*2.0 
												 WHEN rating='2.5' THEN IF(rating='2.5',1,0)*2.5 
												 WHEN rating='3.0' THEN IF(rating='3.0',1,0)*3.0 
												 WHEN rating='3.5' THEN IF(rating='3.5',1,0)*3.5
												 WHEN rating='4.0' THEN IF(rating='4.0',1,0)*4.0
												 WHEN rating='4.5' THEN IF(rating='4.5',1,0)*4.5 
												 WHEN rating='5.0' THEN IF(rating='5.0',1,0)*5.0 
												END)/COUNT(id),1) FROM ".tbl_productreview." as pr WHERE pr.productid=p.id AND pr.status=1),0) as productreview");*/
		//$this->readdb->from($this->_table." as p");
		//$this->readdb->join(tbl_productprice." as pp","pp.productid=p.id","INNER");
		//$this->readdb->join(tbl_hsncode." as hc","hc.id=p.hsncodeid AND hc.status=1","LEFT");
		//$this->readdb->where("p.status=1");
		$where = ' WHERE p.status=1 ';
		if(!empty($filterarray)){
			if(isset($filterarray->categoryfilter) && count($filterarray->categoryfilter)>0){
				$where .= ' AND (';
				//$this->readdb->group_start();
				//$category = explode(',', $filterarray->categoryfilter);
				$category = $filterarray->categoryfilter;
				for ($i=0; $i < count($category); $i++) {
					if(($i+1)==count($category)){
						$where .= "FIND_IN_SET('".$category[$i]."',p.categoryid)>0";	
					}else{
						$where .= "FIND_IN_SET('".$category[$i]."',p.categoryid)>0 OR ";
					}
					//$this->readdb->or_where("FIND_IN_SET('".$category[$i]."',p.categoryid)>0");	
				}
				//$this->readdb->group_end();
				$where .= ')';
			}
		}

		if(!empty($filterarray)){
			if(isset($filterarray->tagfilter) && $filterarray->tagfilter!=''){
				//$this->readdb->group_start();
				$where .= ' AND (';
				$tag = explode(',', $filterarray->tagfilter);
				for ($i=0; $i < count($tag); $i++) { 
					if(($i+1)==count($tag)){
						$where .= "FIND_IN_SET('".$tag[$i]."',p.tagid)>0";	
					}else{
						$where .= "FIND_IN_SET('".$tag[$i]."',p.tagid)>0 OR ";
					}
					//$this->readdb->or_where("FIND_IN_SET('".$tag[$i]."',p.tagid)>0");	
				}
				$where .= ')';
				//$this->readdb->group_end();
			}
		}

		if(!empty($filterarray)){
			if(isset($filterarray->pricemin) && $filterarray->pricemin!=''){
				$where .= " AND (pp.price>=".$filterarray->pricemin." AND pp.price<=".$filterarray->pricemax.")";
				/*$this->readdb->group_start();
				$this->readdb->where("pp.price>=".$filterarray->pricemin." AND pp.price<=".$filterarray->pricemax);
				$this->readdb->group_end();*/
			}
		}

		$search = '';
		if(!empty($filterarray)){
			if(isset($filterarray->searchword) && $filterarray->searchword!=''){
				$search = " AND (p.name LIKE \"%".$filterarray->searchword."%\" OR
								1 in (SELECT 1 FROM ".tbl_category." as c WHERE FIND_IN_SET(c.id,p.categoryid) AND c.name LIKE \"%".$filterarray->searchword."%\") OR 
								1 in (SELECT 1 FROM ".tbl_producttag." as t WHERE FIND_IN_SET(t.id,p.tagid) AND t.tag LIKE \"%".$filterarray->searchword."%\")
								)";
				/*$this->readdb->group_start();
				$this->readdb->where("p.name LIKE '%".$filterarray->searchword."%'");
				$this->readdb->or_where("1 in (SELECT 1 FROM ".tbl_category." as c WHERE FIND_IN_SET(c.id,p.categoryid) AND c.name LIKE '%".$filterarray->searchword."%')");
				$this->readdb->or_where("1 in (SELECT 1 FROM ".tbl_producttag." as t WHERE FIND_IN_SET(t.id,p.tagid) AND t.tag LIKE '%".$filterarray->searchword."%')");
				$this->readdb->group_end();*/
			}
		}
		//$this->readdb->group_by("pp.productid");
		
		$order = 'GROUP BY pp.productid ORDER BY ';

		if(!empty($filterarray) && isset($filterarray->sortby)){
			if($filterarray->sortby==1){
				$order .= 'pp.price ASC';
				//$this->readdb->order_by("pp.price","ASC",false);
			}else if($filterarray->sortby==2){
				$order .= 'pp.price DESC';
				//$this->readdb->order_by("pp.price","DESC",false);
			}else if($filterarray->sortby==0){
				$order .= 'priority ASC';
				//$this->readdb->order_by("priority DESC");	
			}
		}else{
			$order .= "priority ASC";
			//$this->readdb->order_by("priority DESC");
		}
		$limits = '';
		if($type=='data'){
			$limits .= " LIMIT $offset,$limit";
			//$this->readdb->limit($limit,$offset);
		}
		//$query = $this->readdb->query($select.$where.$search.$order);
		//$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;

		/*********Remain result**********/
		if(!empty($filterarray)){
			if(isset($filterarray->searchword) && $filterarray->searchword!=''){
				/*$query1 = $select.$where.$search.$order;

				$search = " AND (p.name NOT LIKE \"%".$filterarray->searchword."%\" OR
								1 NOT in (SELECT 1 FROM ".tbl_category." as c WHERE FIND_IN_SET(c.id,p.categoryid) AND c.name NOT LIKE \"%".$filterarray->searchword."%\") OR 
								1 NOT in (SELECT 1 FROM ".tbl_producttag." as t WHERE FIND_IN_SET(t.id,p.tagid) AND t.tag NOT LIKE \"%".$filterarray->searchword."%\")
								)";

				$query2 = $select.$where.$search.$order;
				$query = $this->readdb->query('('.$query1.') UNION ('.$query2.')'.$limits);*/
				$query = $this->readdb->query($select.$where.$search.$order.$limits);
			}else{
				$query = $this->readdb->query($select.$where.$search.$order.$limits);
			}
		}else{ 
			
			$query = $this->readdb->query($select.$where.$search.$order.$limits);
		}
		//echo $this->readdb->last_query();exit;
		if($type=='data'){
			$data = array();
			foreach ($query->result_array() as $row) {
				
				$ProductFiles = $this->getProductFiles($row['id'],'',2);
				$image = array();
				foreach ($ProductFiles as $filerow) {
					if($filerow['type']==1){
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['file'],'alttext'=>$filerow['alttext']);
					}else if($filerow['type']==2){
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['videothumb'],'alttext'=>$filerow['alttext']);
					}else if($filerow['type']==3){
						preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", urldecode($filerow['file']), $matches);
						$image[] = array('type'=>$filerow['type'],'file'=>$matches[1],'alttext'=>$filerow['alttext']);
					}
				}
				$image = json_encode($image);
				
				$data[] = array("id"=>$row['id'],
								"name"=>$row['name'],
								"slug"=>$row['slug'],
								"singlelink"=>$row['singlelink'],
								"keyfeatures"=>$row['keyfeatures'],
								"price"=>$row['price'],
								"discount"=>(AllowDiscount==1)?$row['discount']:0,
								"image"=>$image,
								"productreview"=>$row['productreview'],
								"applypriceformat"=>$row['applypriceformat'],
								"productreviewcount"=>$row['productreviewcount']);
			}
			//print_r($data);exit;
			return $data;
		}else{
			return $query->num_rows();
		}
					
	}
	function getRawProductList($MEMBERID=0,$CHANNELID=0) {
	   
		$query = $this->readdb->select('id, name')
							
						->from($this->_table.' as p')
						->where("status=1 AND p.producttype=2  AND p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'")
						->order_by('name ASC')
						->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}

	function saveimagefromurl($url){

        $filename = basename($url);

        if (strpos($filename, '?') !== false) {
            $t = explode('?',$filename);
            $filename = $t[0];            
        } 
       
        $ch = curl_init($url);
        $fp = fopen(PRODUCT_PATH.$filename, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
		fclose($fp);

		return $filename;
	}
	
	function getvendorproductdetailsByBarcode($vendorid,$barcode){

		$this->load->model('Vendor_model','Vendor');
		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($vendorid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$CheckProduct = $this->getMemberProductCount($vendorid);
		
		if(strpos($barcode, '|') !== false) {
			$barcode = explode(":",explode("|",$barcode)[0])[1];
		}
		$this->readdb->select('p.id,p.isuniversal,pp.id as priceid');
		$this->readdb->from($this->_table.' as p');
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->where("p.status=1 AND (pp.barcode='".$barcode."' OR pp.sku='".$barcode."') AND (p.producttype = 2 OR IF((SELECT purchaseregularproduct FROM ".tbl_member." where id='".$vendorid."')=1,p.producttype = 0,''))");

		if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
			$memberbasicsalesprice = ($memberbasicsalesprice==1)?$memberbasicsalesprice:0;
								
			$this->readdb->where("
					IF(p.isuniversal=1,
						IF((SELECT IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid where (mp.productid=p.id AND mp.memberid='".$vendorid."')  AND mvp.priceid=pp.id AND IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1)>0,0,1),

						IF((SELECT 1 FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid=0 AND memberid='".$vendorid."' AND mvp.productallow=1 AND IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice)>0)>0,0,1)

					)=0");
							
		}else{
			$memberbasicsalesprice = ($memberbasicsalesprice==0)?1:$memberbasicsalesprice;
			
			$this->readdb->where("pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0),pp.id))");
		}
		$query = $this->readdb->get();
		
		if($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function getadminproductdetailsByBarcode($barcode){

		if(strpos($barcode, '|') !== false) {
			$barcode = explode(":",explode("|",$barcode)[0])[1];
		}
		$this->readdb->select('p.id,p.isuniversal,pp.id as priceid,pp.unitid');
		$this->readdb->from($this->_table.' as p');
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->where("p.status=1 AND (pp.barcode='".$barcode."' OR pp.sku='".$barcode."') AND p.producttype = 2");
		$this->readdb->where("pp.price > 0");
		$query = $this->readdb->get();
		
		if($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}

	function getproductdetailsByBarcode($memberid,$barcode,$sellerid){
		
		$this->load->model('Member_model','Member');
		$this->load->model('Channel_model', 'Channel');
		$channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$CheckProduct = $this->getMemberProductCount($memberid);
		
		if(strpos($barcode, '|') !== false) {
			$barcode = explode(":",explode("|",$barcode)[0])[1];
		}
		$this->readdb->select('p.id,p.isuniversal,pp.id as priceid');
		$this->readdb->from($this->_table.' as p');
		$this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
		$this->readdb->where("p.status=1 AND (pp.barcode='".$barcode."' OR pp.sku='".$barcode."') AND p.producttype = 0");
		
		if($CheckProduct['count'] > 0 && $memberspecificproduct==1){
			$memberbasicsalesprice = ($memberbasicsalesprice==1)?$memberbasicsalesprice:0;
								
			$this->readdb->where("
					IF(p.isuniversal=1,
						IF((SELECT IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice) FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.memberid=mp.memberid where (mp.productid=p.id AND mp.memberid='".$memberid."')  AND mvp.priceid=pp.id AND IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice)>0 AND mvp.productallow = 1 LIMIT 1)>0,0,1),

						IF((SELECT 1 FROM ".tbl_membervariantprices." as mvp WHERE mvp.priceid=pp.id AND mvp.sellermemberid='".$sellerid."' AND memberid='".$memberid."' AND mvp.productallow=1 AND IF(".$memberbasicsalesprice."=0,mvp.price,mvp.salesprice)>0)>0,0,1)

					)=0");
							
		}else{
			$memberbasicsalesprice = ($memberbasicsalesprice==0)?1:$memberbasicsalesprice;
			
			$this->readdb->where("pp.id IN (IF(".$memberbasicsalesprice."=1,IFNULL((SELECT pbp.productpriceid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.productpriceid=pp.id AND pbp.channelid=".$channelid." AND pbp.allowproduct=1 AND pbp.productid=p.id AND pbp.salesprice!=0),0),pp.id))");
		}
		$query = $this->readdb->get();
       
		if($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function getProductActiveList($MEMBERID=0,$CHANNELID=0) {
		
		$query = $this->readdb->select("id,name,
		IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid= p.id ORDER BY pi.priority LIMIT 1),'') as image,
								")
							->from($this->_table." as p")
							->where("status=1 AND producttype=0 AND p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'")
							->order_by("name ASC")							
							->get();
		return $query->result_array();
	}
	function CheckProductSKUAvailable($sku,$PriceID='',$type=0) {
		
		if($PriceID==""){
			$where = "pp.sku ='".$sku."'";
		}else{
			if($type == 0){
				$where = "pp.id <> '".$PriceID."' AND pp.sku ='".$sku."'";
			}else{
				$where = "pp.productid <> '".$PriceID."' AND pp.sku ='".$sku."'";
			}
		}
		
		$query = $this->readdb->select("pp.id")
							->from(tbl_productprices." as pp")
							->where($where)
							->get();
		
		if ($query->num_rows() >= 1) {
			return $query->num_rows();
		} else {
			return 0;
		}
	}
	function removeVariantInUpdateProduct($productid){

		$query = $this->readdb->select("min(id) as priceid")
							->from(tbl_productprices." as pp")
							->where("pp.productid = '".$productid."'")
							->get();
		
		if ($query->num_rows() == 1) {
			$data = $query->row_array();

			$this->load->model('Product_prices_model', 'Product_prices');
            $this->Product_prices->Delete(array("productid"=>$productid,"id!="=>$data['priceid']));
		}
	}
}
