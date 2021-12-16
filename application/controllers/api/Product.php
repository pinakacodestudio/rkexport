<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends MY_Controller {
	public $PostData = array();
	public $data = array();

	function __construct() {
		parent::__construct();

		if($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())){
			$this->PostData = $this->input->post();

			if(isset($this->PostData['apikey'])){
				$apikey = $this->PostData['apikey'];
				if($apikey == '' || $apikey != APIKEY){
					ws_response('fail', API_KEY_NOT_MATCH);
				}
			} else {
				ws_response('fail', API_KEY_MISSING);
				exit;
			}
		} else {
			ws_response('fail', 'Authentication failed');
			exit;
		}
	}

	function getproductdata() {
		$PostData = json_decode($this->PostData['data'], true);	
		$counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
		$search = isset($PostData['search']) ? trim($PostData['search']) : '';
		$id= isset($PostData['id']) ? trim($PostData['id']) : '';
		$variantid= isset($PostData['variantid'])? trim($PostData['variantid']): '' ;
		$memberid = isset($PostData['userid']) && $PostData['userid']!=""? trim($PostData['userid']): 0;
		$channelid = isset($PostData['level']) && $PostData['level']!=""? trim($PostData['level']): 0;
		$sectionid = isset($PostData['sectionid']) && $PostData['sectionid']!=""? trim($PostData['sectionid']): '' ;

		if( $counter == '' || $memberid == 0 || $channelid == 0 ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
                $brandid = 0;
                if(isset($PostData['brandid']) && !empty($PostData['brandid'])){
                    $brandid = $PostData['brandid'];
                }
				$this->load->model('Product_model','Product');			
				//$productdata = $this->Product->getproductrecord($counter,'','',$search);
				$productdata = $this->Product->getproductrecord($counter,$id,$variantid,$search,$memberid,$channelid,$sectionid,$brandid);
				//print_r($productdata);exit;
				$query = $this->readdb->query("SELECT * FROM ".tbl_productcategory." WHERE status=1 ORDER BY maincategoryid,createddate");
				//AND FIND_IN_SET(id,'85,89,87') 
				$categorydata = array();

				foreach($query->result_array() as $category){
					if($category['maincategoryid']==0){
						$categorydata[$category['id']] = array('name'=>$category['name'],'subcategories'=>array());
					}else{
                        foreach ($categorydata as $key => $category1) {
							if($key==$category['maincategoryid'] || in_array($category['maincategoryid'],$category1['subcategories'])==true ){
								array_push($categorydata[$key]['subcategories'], $category['id']);
							}
                        }
						
					}
				}
				$mainarray = array();
				foreach($productdata as $product){
                    foreach ($categorydata as $key => $category) {
						if($key==$product['categoryid']){
							if(empty($mainarray[$key])){
								$mainarray[$key] = array('categoryname'=>$category['name']);
							}
							$mainarray[$key]['categoryproducts'][] = $product;
						}else{
							if (in_array($product['categoryid'], $category['subcategories'])==true) {
								
								$mainarray[$key]['subcategory']['subcategory_'.$product['categoryid']] = array('subcategoryname'=>$product['categoryname']);
								$mainarray[$key]['subcategory']['subcategory_'.$product['categoryid']]['subcategoryproducts'][] = $product;
                            }
						}
                    }
				}
				
				$mainarray1 = array();
                foreach ($mainarray as $key=>$product) {
					$temp = $product;
					$temp['subcategory'] = array();
					if(!empty($mainarray[$key]['subcategory'])){
						foreach ($mainarray[$key]['subcategory'] as $key1=>$subproduct) {
							$temp['subcategory'][] = $subproduct;
						}
					}
						
					$mainarray1[] = $temp;
                }
				//print_r($mainarray);exit;
				//echo json_encode($mainarray1);exit;
				/* $data = '[{"categoryname":"Biscuit","categoryproducts":[{"id":"4","productname":"50 - 50","description":"A Parle Product","file":"demoimage11582264381.jpg","filetype":"1"},{"id":"4","productname":"50 - 50","description":"A Parle Product","file":"demoimage11582264381.jpg","filetype":"1"}],"subcategory":{"subcategoryname":"Parle ","subcategoryproducts":[{"id":"4","productname":"50 - 50","description":"A Parle Product","file":"demoimage11582264381.jpg","filetype":"1"}]}},{"categoryname":"Juice","categoryproducts":[{"id":"4","productname":"50 - 50","description":"A Parle Product","file":"demoimage11582264381.jpg","filetype":"1"},{"id":"4","productname":"50 - 50","description":"A Parle Product","file":"demoimage11582264381.jpg","filetype":"1"}],"subcategory":{"subcategoryname":"","subcategoryproducts":[]}}]';

				$productdata = json_decode($data, true);
				ws_response('success', '', $productdata); */		
				if(empty($productdata)) {
					ws_response('fail',EMPTY_DATA);
				} else {
					ws_response('success', '', $mainarray1);
				}
			}           
        }
    }
  
  	function getproductbyid() {
        $PostData = json_decode($this->PostData['data'], true);
        $id = isset($PostData['id']) ? trim($PostData['id']) : '';
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']): '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']): '';

		if (empty($id) || empty($memberid) || empty($channelid)){
			ws_response('fail', EMPTY_PARAMETER);
        } else {
			
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{  
				$this->load->model('Product_model', 'Product');            
				$productdata = $this->Product->getproductrecordbyid2($id,$memberid,$channelid);
				if(empty($productdata)) {
					ws_response('fail', EMPTY_DATA);
				} else {
					ws_response('success', '',$productdata);
				}
			}
        }
    }

    function stock(){
    	$PostData = json_decode($this->PostData['data'], true);
		$memberid = isset($PostData['userid'])?$PostData['userid']:'';
		$channelid = isset($PostData['level'])?$PostData['level']:'';
        

		if (empty($memberid) || empty($channelid)){
			ws_response('fail', EMPTY_PARAMETER);
        } else {

			$this->load->model('Member_model', 'Member');  
			$this->load->model('Channel_model', 'Channel');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
				ws_response('fail', USER_NOT_FOUND);
			}else{  
				$check_stock_arr=array();
				$this->load->model('Stock_report_model', 'Stock');
				$this->Member->_fields = 'id as sellermemberid';
				$this->Member->_where = "id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")";
				$memberdata = $this->Member->getRecordsById();
				$sellermemberid = isset($memberdata['sellermemberid'])?$memberdata['sellermemberid']:0;
                $sellerid = (isset($PostData['sellerid']) && $PostData['sellerid']!="")?$PostData['sellerid']:$sellermemberid;

                $channeldata = $this->Channel->getMemberChannelData($memberid);
                $memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
                $channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
                $totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;
				// echo $sellerid; 
				foreach ($PostData['stocklist'] as $pd) {
					if($pd['variantid']==""){
						$this->readdb->select('pp.id as priceid');
                        $this->readdb->from(tbl_product." as p");
                        $this->readdb->join(tbl_productprices." as pp","pp.productid=p.id","INNER");
                        // ->join(tbl_membervariantprices." as mvp","mvp.memberid=".$memberid." AND mvp.sellermemberid=".$sellermemberid." AND mvp.priceid=pp.id","INNER")
                        $this->readdb->where(array("p.id"=>$pd['productid']));
                        if($totalproductcount>0 && $memberspecificproduct==1){
                            $this->readdb->where("(IF(
                                (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$sellerid." and mp.memberid='".$memberid."')>0,
                                
                                pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
                                    INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
                                    WHERE mp.sellermemberid=".$sellerid." AND mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
                                
                                IF(
                                    (".$sellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$sellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$sellerid." and mp.memberid='".$memberid."')=0),

                                    pp.productid IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp 
                                        WHERE pbp.channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid=pp.id AND pbp.productid=pp.productid),

                                    pp.productid IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
                                        INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1  
                                        WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$sellerid."') and mp.memberid=".$sellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
                                )
                            ))");
                        }else{
                            $this->readdb->where("p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp 
									WHERE pbp.channelid = '".$channelid."'
									AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)
								");
                        }
                        
                        $query = $this->readdb->get();
						$pricedata = $query->row_array();
						if(count($pricedata)>0){
							$instock = 0;
                            if(STOCKMANAGEMENT==1){
                                if($sellerid!=0){
                                    $productdata = $this->Stock->getProductStockList($sellerid,0,'',$pd['productid']);
                                    if(!empty($productdata)){
                                        $instock = $productdata[0]['overallclosingstock'];
                                    }
                                }else{
                                    $productdata = $this->Stock->getAdminProductStock($pd['productid'],0);
                                    if(!empty($productdata)){
                                        $instock = $productdata[0]['overallclosingstock'];
                                    }
                                }
                            }

							$check_stock_arr[] = array("productid"=>$pd['productid'],"variantid"=>$pd['variantid'],"instock"=>(int)$instock);
						}else{
                            //$check_stock_arr[] = array("productid"=>$pd['productid'],"variantid"=>$pd['variantid'],"instock"=>"0");
						}
					}else{  
						
						$this->readdb->select('pp.id as priceid,GROUP_CONCAT(variantid)as variants');
                        $this->readdb->from(tbl_productprices." as pp");
                        $this->readdb->join(tbl_productcombination." as pc","pp.id=pc.priceid");
                        /* $this->readdb->join(tbl_membervariantprices." as mvp","mvp.memberid=".$memberid." AND mvp.sellermemberid=".$sellermemberid." AND mvp.priceid=pp.id","INNER") */
                        $this->readdb->where(array("variantid in (".$pd['variantid'].")"=>null,"productid"=>$pd['productid']));
                        if($totalproductcount>0 && $memberspecificproduct==1){
                            $this->readdb->where("(IF(
                                (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$sellerid." and mp.memberid='".$memberid."')>0,
                                
                                pp.productid IN (SELECT mp.productid 
                                    FROM ".tbl_memberproduct." as mp 
                                    INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1) 
                                    WHERE mp.sellermemberid=".$sellerid." AND mp.memberid='".$memberid."' AND mvp.priceid=pp.id AND mp.productid=pp.productid AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0),
                                
                                IF(
                                    (".$sellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$sellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$sellerid." and mp.memberid='".$memberid."')=0),

                                    pp.productid IN (SELECT productid FROM ".tbl_productbasicpricemapping." as pbp WHERE channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND allowproduct = 1 AND productpriceid=pp.id AND productid=pp.productid),

                                    pp.productid IN(SELECT mp.productid 
                                        FROM ".tbl_memberproduct." as mp 
                                        INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1 
                                        WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$sellerid."') and mp.memberid=".$sellerid." AND mvp.priceid=pp.id AND mp.productid=pp.productid AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1)
                                )
                            ))");
                        }else{
                            $this->readdb->where("p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp 
									WHERE pbp.channelid = '".$channelid."'
									AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)
								");
                        }
                        $this->readdb->group_by("pc.priceid");
                        $query = $this->readdb->get();
						$pricedata = $query->result_array();
                       
						if(count($pricedata)>0){
                            $check = 0;
                            foreach ($pricedata as $pcd) {
                                $pcdvariants = explode(",", $pcd['variants']);
                                $pdvariants = explode(",", $pd['variantid']);
                                sort($pcdvariants);
                                sort($pdvariants);
                                if($pcdvariants==$pdvariants){
                                    $instock = 0;
                                    if($sellerid!=0){
                                        $ProductVariantStock = $this->Stock->getVariantStock($sellerid,$pd['productid'],'','',0,1);
                                        if(STOCKMANAGEMENT==1 && !empty($ProductVariantStock)){
                                            $key = array_search($pcd['priceid'], array_column($ProductVariantStock, 'combinationid'));
                                            $instock = $ProductVariantStock[$key]['overallclosingstock'];
                                        }
                                    }else{
                                        $ProductVariantStock = $this->Stock->getAdminProductStock($pd['productid'],1);
                                        if(STOCKMANAGEMENT==1 && !empty($ProductVariantStock)){
                                            $key = array_search($pcd['priceid'], array_column($ProductVariantStock, 'priceid'));
                                            $instock = $ProductVariantStock[$key]['overallclosingstock'];
                                        }
                                    } 

                                    $check_stock_arr[] = array("productid"=>$pd['productid'],"variantid"=>$pd['variantid'],"instock"=>(int)$instock);
                                    $check=1;
                                }
                            }
                            if($check==0){
                                $check_stock_arr[] = array("productid"=>$pd['productid'],"variantid"=>$pd['variantid'],"instock"=>"0");
                            }
						}else{
                            //$check_stock_arr[] = array("productid"=>$pd['productid'],"variantid"=>$pd['variantid'],"instock"=>"0");
						}
					}
				}
				if(empty($check_stock_arr)) {
					ws_response('fail', EMPTY_DATA);
				} else {
					if(STOCKMANAGEMENT==1){    
						$data['checkstock']=true;
					}else{
						$data['checkstock']=false;
					}
					$data['variantdata']=$check_stock_arr;
					ws_response('success', '',$data);
				}
			}
		}			
    }
	
	function insertproductinquiry() {
		$PostData = json_decode($this->PostData['data'], true);	
		
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$name= isset($PostData['name']) ? trim($PostData['name']) : '';
		$email= isset($PostData['email'])? trim($PostData['email']): '' ;
		$mobile = isset($PostData['mobile'])? trim($PostData['mobile']): '';
		$organizations = isset($PostData['organizations']) ? trim($PostData['organizations']): '';
		$address = isset($PostData['address']) ? trim($PostData['address']): '' ;
		$productid = isset($PostData['productid']) ? trim($PostData['productid']): '' ;
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $memberid;

		if( empty($memberid) || empty($channelid) || empty($name) || empty($email) || empty($mobile) || empty($organizations) || empty($address) || empty($productid) ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
				$message = (!empty($PostData['message']))?trim($PostData['message']):'';
				$this->load->model('Product_model','Product');	
				$insertData = array(
                    'productid'=>$productid,
                    "sellermemberid"=>0,
					'memberid' => $memberid,                     
					'name' => $name,
					'email' => $email,
					'mobile'=>$mobile,
					'organizations'=>$organizations,
					'address' => $address,
					'message' => $message,
					'createddate'=>$createddate,
					'modifieddate'=>$createddate,
					'addedby'=>$addedby,
					'modifiedby'=>$addedby
				  );

				$this->Product->_table = tbl_productinquiry;
				$insertId = $this->Product->add($insertData);

				if($insertId) {
					ws_response('success', 'Product Inquiry Submitted Successfully.');		
				} else {
					ws_response('fail','Product Inquiry not added.');
				}
			}           
        }
	}
	
	function getstocklist() {
		$this->load->model('Stock_report_model', 'Stock');  

		$PostData = json_decode($this->PostData['data'], true);	
		
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$counter= isset($PostData['counter']) ? trim($PostData['counter']) : '';
		
		if( empty($memberid) || empty($channelid) || $counter=='' ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
				$this->load->model('Product_model','Product');
				$this->load->model('Order_model','Order');
				$stockdata = $this->Stock->getProductStockList($memberid,$channelid,$counter);
				//print_r($stockdata);  exit;
				if(!empty($stockdata)){
					$data=array();
					foreach($stockdata as $product){	
						$qty = $product['overallclosingstock'];

						$variantstock = $this->Stock->getVariantStock($memberid,$product['id']);

						if (!file_exists(PRODUCT_PATH.$product['image'])) {
							$image = PRODUCTDEFAULTIMAGE;
						}else{
							$image = $product['image'];
						}

						if(!empty($variantstock) && $product['isuniversal']==0){
							$qty = array_sum(array_column($variantstock, 'overallclosingstock'));
							$variantstockarr= array();
							
							foreach($variantstock as $variant){
								
								$variantstockarr[] = array(
										"combinationid"=>$variant['combinationid'],
										"combinationname"=>$variant['combinationname'],
										"qty"=>(int)$variant['overallclosingstock'],
										"price"=>$variant['price']
									);
							}
							$data[] = array("productid"=>$product['productid'],
											"productname"=>$product['name'],
											"image"=>$image,
											"qty"=>(int)$qty,
											"variantstock"=>$variantstockarr);
						}else{
							$data[] = array("productid"=>$product['productid'],
											"combinationid"=>0,
											"productname"=>$product['name'],
											"image"=>$image,
											"qty"=>(int)$qty,
											"price"=>$product['price'],
											"variantstock"=>array());
						}
							
					}
					
					ws_response('success', '',$data);		
				}else{
					ws_response('fail', EMPTY_DATA);
				}
				
			}           
        }
	}
	function getproductwisestockreport() {
		$this->load->model('Stock_report_model', 'Stock'); 

		$PostData = json_decode($this->PostData['data'], true);	
		
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$productid= isset($PostData['productid']) && $PostData['productid']!='' ? trim($PostData['productid']) : 0;
		$fromdate= isset($PostData['fromdate']) && !empty($PostData['fromdate']) ? trim($PostData['fromdate']) : '';
		$todate= isset($PostData['todate']) && !empty($PostData['todate']) ? trim($PostData['todate']) : '';
		
		if( empty($memberid) || empty($channelid) || empty($productid) || empty($fromdate) || empty($todate) ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
				$this->load->model('Product_model','Product');
				$this->load->model('Order_model','Order');
				$productdata = $this->Stock->getVariantStock($memberid,$productid,$fromdate,$todate);
				$stockdata = $this->Stock->getProductStockData($memberid,$channelid,$productid,$fromdate,$todate);

				//print_r($productdata);print_r($stockdata); exit;
				if(!empty($productdata)){
					$data=array();
					foreach($productdata as $k=>$row){	
						$qty = $row['closingstock'];
						
						$variantdata=array();
						if($row['isuniversal']==0){
							$priceid = $row['combinationid'];
							$variantdata= $this->Product->getProductVariantsStockwise($row['id'],$priceid);
						}
						$total = $row['price'] * $qty;

                        if(number_format($row['price'],2,'.','') == number_format($row['maxprice'],2,'.','')){
                            $price = numberFormat($row['price'], 2, ',');
                        }else{
                            $price = numberFormat($row['price'], 2, ',')." - ".numberFormat($row['maxprice'], 2, ',');
                        }

						$data['productdata'][] = array("name"=>$row['productname'],
												"openingstock"=>(int)$row['openingstock'],
												"qty"=>(int)$qty,
												"price"=>$row['price'],
												"total"=>$total,
												"variantdata"=>$variantdata
												
												);
						
					}
					$data['stockdata'] = $stockdata;
					//print_r($data); exit;
					ws_response('success', '',$data);		
				}else{
					ws_response('fail', EMPTY_DATA);
				}
				
			}           
        }
	}
	function getoverallstockreport() {
		$this->load->model('Stock_report_model', 'Stock');  

		$PostData = json_decode($this->PostData['data'], true);	
		
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$fromdate= isset($PostData['fromdate']) && !empty($PostData['fromdate']) ? trim($PostData['fromdate']) : '';
		$todate= isset($PostData['todate']) && !empty($PostData['todate']) ? trim($PostData['todate']) : '';
		
		if( empty($memberid) || empty($channelid) || empty($fromdate) || empty($todate) ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
			
				$this->load->model('Product_model','Product');
				$this->load->model('Order_model','Order');
				$productdata = $this->Stock->getProductStockList($memberid,$channelid,'',0,$fromdate,$todate);
				// echo "<pre>"; print_r($productdata); exit;
				if(!empty($productdata)){
					$data=array();
					foreach($productdata as $k=>$row){	
						$qty = $row['closingstock'];
						
						//$total = $row['price'] * $qty;
						$data['productdata'][] = array("name"=>$row['name'],
												"openingstock"=>(int)$row['openingstock'],
												"qty"=>(int)$qty,
												"price"=>$row['price']);
						
					}
					$productid = implode(',',array_column($productdata,'id'));
					if(!empty($productid)){
						$stockdata = $this->Stock->getProductStockData($memberid,$channelid,$productid,$fromdate,$todate);	
						$data['stockdata'] = $stockdata;
					}else{
						$data['stockdata'] = array();
					}
					
					ws_response('success', '',$data);		
				}else{
					ws_response('fail', EMPTY_DATA);
				}
				
			}           
        }
	}	
	function getoverallsalesreport() {
		$PostData = json_decode($this->PostData['data'], true);	
		
		$userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$productid= isset($PostData['productid'])? trim($PostData['productid']) : '';
		
		$memberid = isset($PostData['memberid']) ? trim($PostData['memberid']) : '';
		$reporttype = isset($PostData['reporttype'])? trim($PostData['reporttype']) : '';
		$orderwiseorproductwise = isset($PostData['orderwiseorproductwise'])? trim($PostData['orderwiseorproductwise']) : '';
		$orderorquotation = isset($PostData['orderorquotation']) && $PostData['orderorquotation']==1? 1 : 0;
		
		$fromdate= isset($PostData['fromdate']) && !empty($PostData['fromdate']) ? trim($PostData['fromdate']) : '';
		$todate= isset($PostData['todate']) && !empty($PostData['todate']) ? trim($PostData['todate']) : '';
		
		if( empty($userid) || empty($channelid) || empty($fromdate) || empty($todate) ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
				if($productid==''){
					$productid = -1;
				}
				$this->load->model('Product_model','Product');
				$this->load->model('Order_model','Order');
				$salesdata = $this->Order->getOverallSalesReport($userid,$channelid,$productid,$fromdate,$todate,$reporttype,$orderwiseorproductwise,$memberid,$orderorquotation);
				$stockdata = $this->Order->getProductSalesStockData($userid,$productid,$fromdate,$todate,$reporttype,$orderwiseorproductwise,$memberid,$orderorquotation);

				if(!empty($salesdata)){
					$data=array();
					foreach($salesdata as $k=>$row){	
						
						if($orderwiseorproductwise==1){
							$numberoforderorquotation = '';
							$qty = $row['qty'];
						}else{
							$numberoforderorquotation = $row['numberoforderorquotation'];
							$qty = '';
						}
						$data['productdata'][] = array("date"=>$row['date'],
													   "qty"=>$qty,
													   "amount"=>$row['amount'],
													   "numberoforderorquotation"=>$numberoforderorquotation);

					}
					$data['stockdata'] = $stockdata;
					ws_response('success', '',$data);		
				}else{
					ws_response('fail', EMPTY_DATA);
				}
				
			}           
        }
	}
	function getinouthistory() {
		$PostData = json_decode($this->PostData['data'], true);	
		
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$fromdate= isset($PostData['fromdate']) && !empty($PostData['fromdate']) ? trim($PostData['fromdate']) : '';
		$todate= isset($PostData['todate']) && !empty($PostData['todate']) ? trim($PostData['todate']) : '';
		
		if( empty($memberid) || empty($channelid) || empty($fromdate) || empty($todate) ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
			
				$this->load->model('Order_model','Order');
				$productdata = $this->Order->getInOutHistory($memberid,$channelid,$fromdate,$todate);
				//print_r($productdata); exit;
				if(!empty($productdata)){
					$data=array();
					foreach($productdata as $product){

						if($product['purchaseqty']!=0 || $product['purchaseamount']!=0 || $product['salesqty']!=0 || $product['salesamount']!=0){
							
							$data[] = array(
								"name"=>$product['name'],
								"inqty"=>$product['purchaseqty'],
								"inamount"=>$product['purchaseamount'],
								"outqty"=>$product['salesqty'],
								"outamount"=>$product['salesamount'],
							);
						}
					}
					if(!empty($data)){
						ws_response('success', '',$data);		
					}else{
						ws_response('fail', EMPTY_DATA);
					}
				}else{
					ws_response('fail', EMPTY_DATA);
				}
				
			}           
        }
	}
	function getproductforstockentry() {
		$PostData = json_decode($this->PostData['data'], true);	
		
        $memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $sellerid = isset($PostData['sellerid']) ? trim($PostData['sellerid']) : 0;
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$counter= isset($PostData['counter']) ? trim($PostData['counter']) : '';

		if( empty($memberid) || empty($channelid) || $counter=="") {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');  
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
                $categoryid = $variantid = $sectionid = $search = $brandid = "";
                if(isset($PostData['categoryid']) && !empty($PostData['categoryid'])){
                    $categoryid = $PostData['categoryid'];
                }
                if(isset($PostData['variantid']) && !empty($PostData['variantid'])){
                    $variantid = $PostData['variantid'];
                }
                if(isset($PostData['sectionid']) && !empty($PostData['sectionid'])){
                    $sectionid = $PostData['sectionid'];
                }
                if(isset($PostData['search']) && $PostData['search']!=""){
                    $search = $PostData['search'];
                }
                if(isset($PostData['brandid']) && !empty($PostData['brandid'])){
                    $brandid = $PostData['brandid'];
                }
                
				$this->load->model('Product_model','Product');
				$productdata = $this->Product->getproductforstockentry($memberid,$channelid,$categoryid,$sellerid,$counter,$variantid,$sectionid,$search,$brandid);
                // pre($productdata);
				
				if(!empty($productdata)){
					ws_response('success', '',$productdata);		
				}else{
					ws_response('fail', EMPTY_DATA);
				}
				
			}           
        }
	}
	function miniumstock() {
		$PostData = json_decode($this->PostData['data'], true);	
		
		$memberid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
		$channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
		$minimumstocklimit = isset($PostData['minimumstocklimit']) ? trim($PostData['minimumstocklimit']) : '';
		$emireminderdays = isset($PostData['emireminderdays']) ? trim($PostData['emireminderdays']) : '';
		
		if( empty($memberid) || empty($channelid) ) {
			ws_response('fail', EMPTY_PARAMETER);
		} else {
			$this->load->model('Member_model', 'Member');
			$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
			$count = $this->Member->CountRecords();
	
			if($count==0){
			  ws_response('fail', USER_NOT_FOUND);
			}else{	
				$updatedata = array();
				if(isset($PostData['minimumstocklimit']) && $minimumstocklimit!=''){
					$updatedata['minimumstocklimit'] = $minimumstocklimit;
				}
				if(isset($PostData['emireminderdays']) && $emireminderdays!=''){
					$updatedata['emireminderdays'] = $emireminderdays;
				}
				if(!empty($updatedata)){
					$modifieddate = $this->general_model->getCurrentDateTime();
					$updatedata['modifieddate'] = $modifieddate;
					$updatedata['modifiedby'] = $memberid;
					$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
					$this->Member->Edit($updatedata);
				}

				$this->load->model('Order_model', 'Order');
				$dueemidata = $this->Order->getinstallmentremindercounter($memberid,$channelid);

				$this->Member->_fields = "minimumstocklimit,emireminderdays";
				$this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
				$memberdata = $this->Member->getRecordsById();

				$memberdata['dueemi'] = (!empty($dueemidata))?$dueemidata['dueemi']:0;
				if(!empty($memberdata)){
					ws_response('success', '',$memberdata);	
				}else{
					ws_response('fail', EMPTY_DATA);
				}
				
			}           
        }
    }
    
    /**Start Delight CRM API */
	function getproductcategory() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['modifieddate'])){

						$this->readdb->select('id, maincategoryid, IF(maincategoryid = 0, name, CONCAT((SELECT name FROM '.tbl_productcategory.' WHERE id = pc.maincategoryid), " > ",name )) AS name,status,createddate');
						$this->readdb->from(tbl_productcategory.' AS pc');
						$this->readdb->where("pc.channelid=0 AND pc.memberid=0");
						if($PostData['modifieddate']!=''){
							$this->readdb->where("modifieddate >",$PostData['modifieddate']);
						}
						$this->readdb->order_by('pc.priority ASC,name ASC');
						$query = $this->readdb->get();
						$categoryproduct = $query->result_array();

						if(!empty($categoryproduct)){
							foreach ($categoryproduct as $row) { 
								$this->data[]= array("id"=>$row['id'],"categoryname"=>$row['name'],"status"=>$row['status'],"createddate"=>date("Y-m-d h:i:s A",strtotime($row['createddate'])));
							}
						}
						if(empty($this->data)){
							ws_response("Fail", "Product category not available.");
						}else{
							ws_response("Success", "",$this->data);
						}
                    }else{
						ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }
	function getproduct() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    $categoryid = (!empty($PostData['categoryid']))?$PostData['categoryid']:0;

                    if(isset($PostData['modifieddate'])){

						$query = $this->readdb->select("p.*,h.integratedtax as tax,p.modifeddate as modifieddate,IFNULL((SELECT filename from ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,p.discount");
						$this->readdb->from(tbl_product  ." as p");
						$this->readdb->join(tbl_hsncode . " as h", "h.id=p.hsncodeid", "LEFT");
						if($PostData['modifieddate']!=''){
							$this->readdb->where("p.modifeddate >",$PostData['modifieddate']);
						}
						$this->readdb->where("(p.categoryid=".$categoryid." OR ".$categoryid."=0) AND p.status=1 AND p.producttype=0 AND p.channelid=0 AND p.memberid=0");
						$this->readdb->order_by("p.name ASC");
						$query = $this->readdb->get();

						$product = $query->result_array();
						
						if(!empty($product)){
							foreach ($product as $row) { 

                                $this->load->model('Product_model', 'Product');
                                $VariantData = $this->Product->getVariantByProductIdForAdmin($row['id']);
                                $variantdata = array();
                                if(!empty($VariantData)){
                                    foreach($VariantData as $variant){

                                        $variantdata[] = array("priceid"=>$variant['id'],
                                                            "actualprice"=>$variant['price'],
                                                            "variantname"=>$variant['memberprice']
                                                        );
                                    }
                                }

                                $this->data[]= array("id"=>$row['id'],
                                                    "categoryid"=>$row['categoryid'],
                                                    "productname"=>$row['name'],
                                                    "discount"=>$row['discount'],
                                                    "packing"=>'',
                                                    "pack"=>'',
                                                    "hsncodeid"=>$row['hsncodeid'],
                                                    "cost"=>'',
                                                    "mrp"=>'',
                                                    "rate"=>'',
                                                    "discountlimit"=>'',
                                                    "image"=>$row['image'],
                                                    "tax"=>$row['tax'],
                                                    "createddate"=>date("Y-m-d h:i:s A",strtotime($row['createddate'])),
                                                    "status"=>$row['status'],
                                                    "variantdata"=>$variantdata
                                                );
							}
						}
						if(empty($this->data)){
							ws_response("Fail", "Product not available.");
						}else{
							ws_response("Success", "",$this->data);
						}
                    }else{
						ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }
	function getenquiryproduct() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['modifieddate']) && isset($PostData['employeeid']) && isset($PostData['search']) && isset($PostData['counter'])){
						if($PostData['employeeid']==''){
							ws_response("Fail", "Fields value are missing.");
						} else{

                            $direct = (isset($PostData['direct']))?$PostData['direct']:1;
                            $indirect = (isset($PostData['indirect']))?$PostData['indirect']:0;
                            $leadsource = (isset($PostData['leadsource']))?$PostData['leadsource']:'';
                            $industry = (isset($PostData['industry']))?$PostData['industry']:'';
                            $memberstatus = (isset($PostData['memberstatus']))?$PostData['memberstatus']:'';

                            $query = $this->readdb->select("ci.id,ci.memberid,contactid,inquiryassignto as assignto,inquiryfollowuptype as followuptype,inquirynote as notes,ci.createddate,ci.addedby,companyname,name as membername,
                                                        m.remarks as memberremark,
                                                        IFNULL((SELECT cd.email FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as memberemail,
                                                        IFNULL((SELECT cd.mobileno FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as membermobile,noofinstallment,
                                                        (select name from ".tbl_inquirystatuses." where id=ci.status)as statusname,
                                                        IF(ci.inquiryassignto=".$PostData['employeeid'].",1,0) as direct,
                                                        IF((ci.id IN(SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['employeeid']." OR ith.addedby=".$PostData['employeeid'].") AND ci.inquiryassignto!=".$PostData['employeeid']."),1,0)as indirect,
                                                        IFNULL((select color from ".tbl_inquirystatuses." where id=ci.status),'')as statuscolor");
                            $this->readdb->from(tbl_crminquiry." as ci");
                            $this->readdb->join(tbl_member." as m","ci.memberid=m.id AND m.channelid=".CUSTOMERCHANNELID,'LEFT');
                            
                            if($PostData['modifieddate']!=''){   
                                $this->readdb->where("ci.modifieddate > '".$PostData['modifieddate']."'");
                            }

                            if ($direct==1 && $indirect==0) {
                                $this->readdb->where("inquiryassignto = ".$PostData['employeeid']);
                            }else if($direct==0 && $indirect==1){
                                $this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['employeeid']." OR ith.addedby=".$PostData['employeeid'].") AND ci.inquiryassignto!=".$PostData['employeeid'].")");
                            }else{
                                $this->readdb->group_start();
									$this->readdb->group_start();
                                    $this->readdb->where("inquiryassignto = ".$PostData['employeeid']);
                                    $this->readdb->group_end();

                                    $this->readdb->or_group_start();
                                    $this->readdb->where("(ci.id IN (SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['employeeid']." OR ith.addedby=".$PostData['employeeid'].") AND ci.inquiryassignto!=".$PostData['employeeid'].")");
                                    $this->readdb->group_end();
                                $this->readdb->group_end();
                            }

                            if(isset($leadsource) && $leadsource!=''){
                                $this->readdb->where("FIND_IN_SET(ci.inquiryleadsourceid,'".$leadsource."')>0");
                            }

                            if(isset($industry) && $industry!=''){
                                $this->readdb->where("FIND_IN_SET(m.industryid,'".$industry."')>0");
                            }

                            if(isset($memberstatus) && $memberstatus!=''){
                                $this->readdb->where("FIND_IN_SET(m.status,'".$memberstatus."')>0");
                            }

                            if(isset($PostData['enquirystatus']) && $PostData['enquirystatus']!=""){
                                $this->readdb->where("FIND_IN_SET(ci.status,'".$PostData['enquirystatus']."')>0");
                            }
                            if(isset($PostData['member']) && $PostData['member']!=""){
                                $this->readdb->where(array("m.id"=>$PostData['member']));
                            }
                            if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
                                $fromdate = $this->general_model->convertdate($PostData['fromdate']);
                                $todate = $this->general_model->convertdate($PostData['todate']);
                                $this->readdb->where("(DATE(ci.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
                            }
							
							if($PostData['search']!=""){
								$datearr = explode("/",$PostData['search']);
                                $datestr = array();
                                if(count($datearr)>0){
                                    foreach($datearr as $key=>$da){
                                        $datestr[] = $datearr[count($datearr)-($key+1)];
                                    }
                                }
                                $datesearch = implode("/",$datestr);
                                $datesearch = str_replace("/","-",$datesearch);

                                $this->readdb->where(array("(m.name like '%".$PostData['search']."%' or m.companyname like '%".$PostData['search']."%' or ci.createddate like '%".$datesearch."%')"=>null));
                            }

                            if(isset($PostData['sortby']) && $PostData['sortby']!=""){
                                if($PostData['sortby']=="latestfirst"){
                                    $this->readdb->order_by("ci.id DESC");
                                }elseif($PostData['sortby']=="oldestfirst"){
                                    $this->readdb->order_by("ci.id ASC");
                                }
                            }else{
                                $this->readdb->order_by("ci.id DESC");
                            }

                            if($PostData['counter']!=-1){
                                $this->readdb->limit(10,$PostData['counter']);
                            }
                            $query = $this->readdb->get();
                            $enquiryproduct = $query->result_array();
                            
                            $this->load->model("Crm_inquiry_model","Crm_inquiry");
                            $this->load->model('Product_prices_model', 'Product_prices');

                            $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                            if(!empty($enquiryproduct)){
                                 foreach ($enquiryproduct as $row) { 

                                    $product_arr=array();
                                    $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                                    $myproduct_arr = $this->Crm_inquiry->getinquiryproduct($row['id'],$PostData['search']);
                                    
                                    if(!empty($myproduct_arr)){
                                        foreach($myproduct_arr as $rowdata){
                                             
                                            $VariantData = $this->Product_prices->getProductpriceById($rowdata['priceid']);
                                            $variantdata = array();
                                            if(!empty($VariantData)){
                                                
                                                    $variantdata[] = array("priceid"=>$VariantData['id'],
                                                                         "actualprice"=>$VariantData['price'],
                                                                         "variantname"=>$VariantData['pricewithvariant']
                                                                     );
                                            }
                                            $rowdata['variantdata'] = $variantdata;
                                            $product_arr[] = $rowdata;
                                        }
                                    }
                                    
                                    $installment_arr=array();
                                    if($row['noofinstallment']>0){
                                        $this->Crm_inquiry->_table = tbl_inquiryproductinstallment;
                                        $this->Crm_inquiry->_order = "inquiryid DESC";
                                        $this->Crm_inquiry->_where = array("inquiryid"=>$row['id']);
                                        $installments = $this->Crm_inquiry->getRecordByID();
                                        foreach ($installments as $is) {
                                            if($is['date']=="0000-00-00"){
                                                $is['date']="";
                                            }
                                            if($is['paymentdate']=="0000-00-00"){
                                                $is['paymentdate']="";
                                            }
                                            $installment_arr[]=array('installmentid'=>$is['id'],'amount'=>$is['amount'],'percent'=>$is['percentage'],'installmentdate'=>$is['date'],'paymentddate'=>$is['paymentdate'],'receivedstatus'=>$is['status']);
                                        }
                                    }

                                    if(count($installment_arr)>0){
                                        $installment="1";
                                    }else{
                                        $installment="0";
                                    }
                                    if(is_null($row['statusname'])){
                                        $row['statusname']="";
                                    }
                                    
                                    $this->load->model('Crm_quotation_model','Crm_quotation');
                                    $quotationdata = $this->Crm_quotation->getQuotationForapi($row['id']);

									$this->data[]= array("inquiryid"=>$row['id'],
														"direct"=>$row['direct'],
														"indirect"=>$row['indirect'],
														"memberid"=>$row['memberid'],
														'contactid'=>$row['contactid'],
														"membername"=>$row['membername'],
														"memberemail"=>$row['memberemail'],
														"membermobile"=>$row['membermobile'],
														"memberremark"=>$row['memberremark'],
														"companyname"=>$row['companyname'],
														"assigntoid"=>$row['assignto'],
														"addedby"=>$row['addedby'],
														"followupcategoryid"=>$row['followuptype'],
														"date"=>date("Y-m-d",strtotime($row['createddate'])),
														"note"=>$row['notes'],
														"createddate"=>date("Y-m-d h:i:s a",strtotime($row['createddate'])),
														"isinstallment"=>$installment,
														'receivedstatus'=> $row['statusname'],
														'receivedstatuscolor'=> $row['statuscolor'],
														"productdata"=>$product_arr,
														"installmentdata"=>$installment_arr,
														"quotationdata"=>$quotationdata);
								}
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "Inquiry product not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
						}
                    } else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

	function getinquirystatuses() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
					$this->load->model("Inquiry_statuses_model","Inquiry_statuses");
					$inquirystatuses = $this->Inquiry_statuses->getInquierystatuses();

					if(!empty($inquirystatuses)){
						foreach($inquirystatuses as $row) { 
							$this->data[]= array("id"=>$row['id'],"name"=>$row['name']);
						}
					}
					if(empty($this->data)){
						ws_response("Fail", "Inquiry status not available.");
					}else{
						ws_response("Success", "",$this->data);
					}
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
	}
	
	function changeinquirystatus() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    if(isset($PostData['inquiryid']) && isset($PostData['inquirystatusid'])  && isset($PostData['employeeid'])) {
                        if((empty($PostData['inquiryid'])) || (empty($PostData['employeeid'])) ){
                            ws_response("Fail", "Fields value are missing.");
                        }
                        $inquiryid = (isset($PostData['inquiryid']))?$PostData['inquiryid']:'';
                        $inquirystatusid = (isset($PostData['inquirystatusid']))?$PostData['inquirystatusid']:'';

                        $updatedata = array('status' => $inquirystatusid,
                                            'modifieddate' => $createddate);
                        
                        $updatedata=array_map('trim',$updatedata);

                        $this->load->model("Crm_inquiry_model", "Crm_inquiry");

                        $this->Crm_inquiry->_where = array("id"=>$inquiryid);
                        $Edit = $this->Crm_inquiry->Edit($updatedata);
                        
                        ws_response("Success","CRM inquiry status successfully updated.");
                            
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

	function transferinquiry() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    if(isset($PostData['employeeid']) && isset($PostData['inquiryid'])  && isset($PostData['selectedemployeeid']) && isset($PostData['reason'])) {

                        if((empty($PostData['inquiryid'])) || (empty($PostData['employeeid'])) || (empty($PostData['selectedemployeeid'])) || (empty($PostData['reason'])) ){
                            ws_response("Fail", "Fields value are missing.");
                        }
                        $inquiryid = (isset($PostData['inquiryid']))?$PostData['inquiryid']:'';
                        $reason = (isset($PostData['reason']))?$PostData['reason']:'';
                        $assignmember = (!empty($PostData['assignmember']))?1:0;
                        $selectedemployeeid = (isset($PostData['selectedemployeeid']))?$PostData['selectedemployeeid']:'';
                        $createddate = $this->general_model->getCurrentDateTime();

                        $this->load->model("Crm_inquiry_model", "Crm_inquiry");
                        $this->load->model('Member_model', 'Member');

                        $updatedata = array('inquiryassignto'=>$selectedemployeeid,
                                            "modifieddate"=>$createddate);

                        $updatedata=array_map('trim',$updatedata);
                        $this->Crm_inquiry->_where = array("id"=>$inquiryid);

                        $this->Crm_inquiry->_fields = "inquiryassignto,status,memberid";
                        $checkassignto = $this->Crm_inquiry->getRecordsByID();

                        $edit = $this->Crm_inquiry->Edit($updatedata);
                        
                        if($edit){
							if($selectedemployeeid!=$checkassignto['inquiryassignto']){
								
                                $this->Crm_inquiry->_table=tbl_crminquirytransferhistory;
                                $insertdata=array('inquiryid' => $inquiryid,
                                                    'transferfrom'=>$checkassignto['inquiryassignto'],
                                                    'transferto'=>$selectedemployeeid,
                                                    'reason'=>$reason,
                                                    'createddate'=>$createddate,
                                                    'modifieddate'=>$createddate);
                                $this->Crm_inquiry->Add($insertdata);
                                
                                if($assignmember){
                                    $this->Member->_table = tbl_crmassignmember;
                                    $this->Member->_where = array('employeeid'=>$selectedemployeeid,
                                                                    'memberid'=>$checkassignto['memberid']);
                                    $Count = $this->Member->CountRecords();
                                    if($Count==0){
                                        $insertdata=array('employeeid' => $selectedemployeeid,
                                                    'memberid'=>$checkassignto['memberid']);
                                        $this->Member->Add($insertdata);
                                    }
                                }

                                $inquirydata = $this->Crm_inquiry->getInquiryDetailForEmail($inquiryid);
								
                                if(!is_null($inquirydata) && $inquirydata['checknewtransferinquiry']==1){
                                    $this->data['inquirydata']=$inquirydata;
                                    $table=$this->load->view(ADMINFOLDER."crm_inquiry/inquiryreporttable",$this->data,true);
                                
                                    $mailBodyArr1 = array(
                                        "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                        "{name}" => $inquirydata['employeename'],
                                        "{assignby}" => $this->session->userdata(base_url().'ADMINNAME'),
                                        "{detailtable}"=>$table,
                                        "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                        "{companyname}" => COMPANY_NAME
                                    );
									$inquirydata['email'] = "ashishgondaliya@rkinfotechindia.com";
									$mailid=array_search('Inquiry Assign',$this->Emailformattype);
									$emailSend = $this->Crm_inquiry->sendMail($mailid,$inquirydata['email'], $mailBodyArr1);
								}
                            }
                        }
                        
                        ws_response("Success","CRM inquiry transferred successfully.");
                            
                    }
                    else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
	}
	
	function updateinquirynotes() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    
                    if(isset($PostData['inquiryid']) && isset($PostData['inquirynote'])) {
                        $inquirynote = (isset($PostData['inquirynote']))?$PostData['inquirynote']:'';
                        if(empty($PostData['inquiryid'])){
                            ws_response("Fail", "Fields value are missing.");
                        }

                        $updatedata = array('inquirynote' => $inquirynote,
                                            'modifieddate' => $createddate);
                        
                        $updatedata=array_map('trim',$updatedata);

                        $this->load->model("Crm_inquiry_model", "Crm_inquiry");

                        $this->Crm_inquiry->_where = array("id"=>$PostData['inquiryid']);
                        $Edit = $this->Crm_inquiry->Edit($updatedata);
                        
                        ws_response("Success","CRM inquiry notes successfully updated.");
                    }else{
                         ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
	}
	
	function addeditenquiry() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
					$updateinstallmentdata = $insertinstallmentdata = $installmentidsarr = array();
					$this->load->model("Crm_inquiry_model","Crm_inquiry");
                    $this->load->model("Followup_model","Followup");
                    $this->load->model("User_model","User");
                    $this->load->model('Crm_quotation_model','Crm_quotation');

                    if(isset($PostData['assigntoid']) && isset($PostData['memberid']) && isset($PostData['contactid']) && isset($PostData['notes']) && isset($PostData['productdata']) && isset($PostData['receivedstatus']) && isset($PostData['quotationdata'])) {
                        if($PostData['assigntoid'] == "" || $PostData['memberid'] == "" || $PostData['contactid']==""){
                            ws_response("Fail", "Fields value are missing.");
                        }

                        if(!isset($PostData['addedby'])) {
                        	$PostData['addedby']=0;  
                        }
                        if(empty($PostData['followupcategoryid'])) {
                        	$PostData['followupcategoryid'] = 0;
                        }
                        $insertinquiryproductdata=array();

                        if(!is_dir(QUOTATION_PATH)){
                            @mkdir(QUOTATION_PATH);
                        }
                        // print_r($PostData['quotationdata']); exit;
                        foreach ($_FILES as $key => $value) 
                        {
                            $id = preg_replace('/[^0-9]/', '', $key);
                            if($_FILES['quotation'.$id]['name']!=''){
                                $file = uploadFile('quotation'.$id, 'CRMQUOTATION',QUOTATION_PATH,"*","",'1',QUOTATION_LOCAL_PATH);
                                if($file === 0){
                                    ws_response("Fail", "Invalid file type."); //INVALID image FILE TYPE
                                }
                            }
                        }
                        if(isset($PostData['id']) && !empty($PostData['id'])) {
                            $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                            $this->Crm_inquiry->_where = array("inquiryid"=>$PostData['id']);
                            $this->Crm_inquiry->_fields = "id";
                            $this->Crm_inquiry->_order = "id desc";   
                            $all_inquiryproduct_ids = $this->Crm_inquiry->getRecordByID();

                            $myall_inquiryproduct_arr=array();
                            foreach ($all_inquiryproduct_ids as $v) {
                                $myall_inquiryproduct_arr[]=$v['id'];
                            }

                            foreach($PostData['productdata'] as $pd) {
                                if(isset($pd['productid']) && isset($pd['qty']) &&  isset($pd['rate']) && isset($pd['discount']) && isset($pd['discountpercent']) && isset($pd['amount']) && isset($pd['tax'])) {
                                    if(isset($pd['inquiryproductid'])) {
                                        if($pd['inquiryproductid']!="" && $pd['productid']!="" && $pd['qty']!="" &&  $pd['rate']!=""  && $pd['amount']!="" && $pd['tax']!="") {
											
											$insertinquiryproductdata[] = array(
												'id'=>$pd['inquiryproductid'],
												'productid'=>$pd['productid'],
												'priceid'=>$pd['priceid'],
												'qty'=>$pd['qty'],
												'rate'=>$pd['rate'],
												'discount'=>$pd['discount'],
												'discountpercentage'=>$pd['discountpercent'],
												'amount'=>$pd['amount'],
												'tax'=>$pd['tax'],
												'modifieddate' => $createddate,
												'modifiedby' => $PostData['assigntoid'],
												'createddate'=>$createddate,
												'addedby'=> $PostData['addedby']);
                                        } else{
                                            ws_response("Fail", "Fields value are missing.");
                                        }
                                    } else{
                                        if($pd['productid']!="" && $pd['qty']!="" &&  $pd['rate']!="" && $pd['amount']!="" && $pd['tax']!="") {
                                            $insertinquiryproductdata[] = array(
                                                'productid'=>$pd['productid'],
                                                'priceid'=>$pd['priceid'],
                                                'qty'=>$pd['qty'],
                                                'rate'=>$pd['rate'],
                                                'discount'=>$pd['discount'],
                                                'discountpercentage'=>$pd['discountpercent'],
                                                'amount'=>$pd['amount'],
                                                'tax'=>$pd['tax'],
                                                'modifieddate' => $createddate,
                                                'modifiedby' => $PostData['assigntoid'],
                                                'createddate'=>$createddate,
                                                'addedby'=> $PostData['addedby']
                                            );
                                        } else{
                                            if (INQUIRY_WITH_PRODUCT == 1) {
                                                ws_response("Fail", "Fields value are missing.");
                                            }
                                        }
                                    }
                                }
                            }

                            foreach($PostData['installmentdata'] as $isd) {
                                $installmentdate = $this->general_model->convertdate($isd['installmentdate']);
                                if(isset($isd['amount']) && isset($isd['percent']) && isset($installmentdate) && isset($isd['paymentddate']) && isset($isd['receivedstatus'])) {
                                    if(!empty($isd['installmentid'])) {
                                        if($isd['installmentid']!="" && $isd['amount']!="" && $isd['percent']!="" && $installmentdate!="" && $isd['receivedstatus']!="") {
                                            $updateinstallmentdata[] = array(
                                                'id'=>$isd['installmentid'],
                                                'inquiryid'=>$PostData['id'],
                                                'amount'=>$isd['amount'],
                                                'percentage'=>$isd['percent'],
                                                'date'=>$installmentdate,
                                                'paymentdate'=>$isd['paymentddate'],
                                                'status'=>$isd['receivedstatus'],
                                                'modifieddate' => $createddate,
                                                'modifiedby' => $PostData['assigntoid']
                                            );
                                            $installmentidsarr[]=(int)$isd['installmentid'];
                                        } else{
                                            ws_response("Fail", "Fields value are missing.");
                                        }
                                    } else{
                                        if($isd['amount']!="" && $isd['percent']!="" && $installmentdate!="" && $isd['receivedstatus']!=""){
                                            $insertinstallmentdata[] = array(
                                                'amount'=>$isd['amount'],
                                                'inquiryid'=>$PostData['id'],
                                                'percentage'=>$isd['percent'],
                                                'date'=>$installmentdate,
                                                'paymentdate'=>$isd['paymentddate'],
                                                'status'=>$isd['receivedstatus'],
                                                'modifieddate' => $createddate,
                                                'modifiedby' => $PostData['assigntoid'],
                                                'createddate'=>$createddate,
                                                'addedby'=> $PostData['addedby']
                                            );
                                        } else{
                                            ws_response("Fail", "Fields value are missing.");
                                        }
                                    }
                                }
                            }
                        } else{
                            foreach($PostData['productdata'] as $pd) {
                                if(isset($pd['productid']) && isset($pd['qty']) &&  isset($pd['rate']) && isset($pd['discount']) && isset($pd['discountpercent']) && isset($pd['amount']) && isset($pd['tax'])) {
                                    if($pd['productid']!="" && $pd['qty']!="" &&  $pd['rate']!="" && $pd['amount']!="" && $pd['tax']!="") {
                                        $insertinquiryproductdata[] = array(
                                            'productid'=>$pd['productid'],
                                            'priceid'=>$pd['priceid'],
                                            'qty'=>$pd['qty'],
                                            'rate'=>$pd['rate'],
                                            'discount'=>$pd['discount'],
                                            'discountpercentage'=>$pd['discountpercent'],
                                            'amount'=>$pd['amount'],
                                            'tax'=>$pd['tax']
                                        );
                                    }else{
                                        if(INQUIRY_WITH_PRODUCT==1){
                                            ws_response("Fail", "Fields value are missing.");
                                        }
                                    }
                                }
                            }
                            foreach($PostData['installmentdata'] as $isd) {
                                $installmentdate = $this->general_model->convertdate($isd['installmentdate']);
                                if($isd['amount']!="" && $isd['percent']!="" && $installmentdate!="" && $isd['receivedstatus']!="") {
                                    $insertinstallmentdata[] = array(
                                        'amount'=>$isd['amount'],
                                        'percentage'=>$isd['percent'],
                                        'date'=>$installmentdate,
                                        'paymentdate'=>$isd['paymentddate'],
                                        'status'=>$isd['receivedstatus'],
                                        'modifieddate' => $createddate,
                                        'modifiedby' => $PostData['assigntoid'],
                                        'createddate'=>$createddate,
                                        'addedby'=> $PostData['addedby']
                                    );
                                }else{
                                    ws_response("Fail", "Fields value are missing.");
                                }
                            }
                        }

                        $notes="";
                        if(isset($PostData['notes'])){
                            $notes=$PostData['notes'];
                        }

                        if(isset($PostData['memberremark'])){
                            $remarkmemberdata=array('remarks'=>$PostData['memberremark']);
                            $this->load->model("Member_model","Member");
                            $this->Member->_where = "id=".$PostData['memberid'];
                            $this->Member->Edit($remarkmemberdata);
                        }

                        $inquiryleadsourceid = (!empty($PostData['leadsource']))?$PostData['leadsource']:0;
                        
                        if(isset($PostData['id']) && !empty($PostData['id'])){
                            //EDIT INQUIRY
                            if($PostData['isinstallment']==1){
                                $totalinstallment = count($PostData['installmentdata']);
                            }else{
                                $totalinstallment = 0;
                            }

                            $this->Crm_inquiry->_table = tbl_crminquiry;
                            $this->Crm_inquiry->_where = array("id"=>$PostData['id']);
                            $this->Crm_inquiry->_fields = "inquiryassignto,status";
                            $checkassignto = $this->Crm_inquiry->getRecordsByID();

                            $updatedata = array(
                                'memberid'=>$PostData['memberid'],
                                'inquiryassignto'=>$PostData['assigntoid'],
                                'inquiryfollowuptype'=>$PostData['followupcategoryid'],
                                'inquiryleadsourceid'=>$inquiryleadsourceid,
                                'contactid'=>$PostData['contactid'],
                                'inquirynote'=>$notes,
                                'noofinstallment'=>$totalinstallment,
                                'modifieddate' => $createddate,
                                'modifiedby' => $PostData['assigntoid'],
                                'status'=>$PostData['receivedstatus']
                            );
                           
                            $this->Crm_inquiry->_where = array("id"=>$PostData['id']);
                            $editinquiry = $this->Crm_inquiry->Edit($updatedata);

                            $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                            $inquiry_product_id_arr=array();
                            foreach($insertinquiryproductdata as $ipd) {
                                if(isset($ipd['id'])){                     
                                    if(in_array($ipd['id'],$myall_inquiryproduct_arr)) {                                    
                                        $this->Crm_inquiry->_where = array("id"=>$ipd['id']);
                                        $inquiry_product_id_arr[]=$ipd['id'];
                                        unset($ipd['id']);
                                        $editinquiryproduct = $this->Crm_inquiry->Edit($ipd);
                                    }else{
                                        $this->Crm_inquiry->Delete(array("id"=>$ipd['id']));
                                    }
                                }else{
                                    $ipd['inquiryid']=$PostData['id'];
                                    $add = $this->Crm_inquiry->add($ipd);
                                    $inquiry_product_id_arr[]=$add;
                                }
                            }

                            if(isset($PostData['id']) && !empty($PostData['id'])){
                                $this->Crm_inquiry->_table = tbl_inquiryproductinstallment;
                                if($PostData['isinstallment']==1){
                                    if(count($updateinstallmentdata)>0){
                                        $this->Crm_inquiry->edit_batch($updateinstallmentdata,"id");
                                    }
                                    if(count($installmentidsarr)==0){
                                        $this->Crm_inquiry->Delete(array("inquiryid"=>$PostData['id']));
                                    }
                                    if(count($installmentidsarr)>0){
                                        $this->Crm_inquiry->Delete(array("id not in(".implode(",",$installmentidsarr).")"=>null,"inquiryid"=>$PostData['id']));
                                    }
                                    if(count($insertinstallmentdata)>0){
                                        $this->Crm_inquiry->add_batch($insertinstallmentdata);
                                        $first_id = $this->writedb->insert_id();
                                        $last_id = $first_id + (count($insertinstallmentdata)-1);
                                        for($gi=$first_id;$gi<=$last_id;$gi++){
                                            $installmentidsarr[]=$gi;
                                        }
                                    }
                                }else{
                                    $this->Crm_inquiry->Delete(array("inquiryid"=>$PostData['id']));
                                }
                            }

                            $ii=implode(",",$inquiry_product_id_arr);
                            if(count($inquiry_product_id_arr)>0){
                                $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                                $this->Crm_inquiry->Delete(array("inquiryid"=>$PostData['id'],"id not in($ii)"=>null));
                            }

                            if(isset($PostData['removequotationfileid']) && $PostData['removequotationfileid']!=''){
                    
                                $removequotationfileid = implode(',',array_filter(explode(",",$PostData['removequotationfileid'])));
                                $where = "FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removequotationfileid'])))."')>0";
                               
                                $FileMappingData = $this->Crm_quotation->getQuotationData($removequotationfileid);
                        
                                if(!empty($FileMappingData)){
                                    foreach ($FileMappingData as $row) {
                                      unlinkfile("CRMQUOTATION",$row['file'], QUOTATION_PATH);
                                    }
                                    $this->Crm_quotation->Delete($where);
                                }
                            }

                            $insertquotationdata=array();
                            foreach ($_FILES as $key => $value) {
                                $id = preg_replace('/[^0-9]/', '', $key);
                                $quotation = $PostData['quotationdata'];
                                
                                $quotationfileid = $quotation[$id]['quotationid'];
                                $quotationdescription = $quotation[$id]['description'];
                                $quotationdate = ($quotation[$id]['date']!="")?$this->general_model->convertdate($quotation[$id]['date']):"";
                                $addedbyid = $quotation[$id]['addedbyid'];
                          
                                if (empty($quotationfileid)) {
                                    if ($_FILES['quotation'.$id]['name']!='') {
                                        $file =  uploadFile('quotation'.$id, 'CRMQUOTATION',QUOTATION_PATH,"*","",'1',QUOTATION_LOCAL_PATH);
                                        
                                        if ($file !== 0) {
                                            if($file == 2){
                                                ws_response("Fail", "File not upload.");
                                            }else{

                                                $insertquotationdata[] = array("inquiryid" => $PostData['id'],
                                                                              "file" => $file,
                                                                              "description" => $quotationdescription,
                                                                              "date" => $quotationdate,
                                                                              "createddate" => $createddate,
                                                                              "addedby" => $addedbyid,
                                                                              "modifieddate" => $createddate,
                                                                              "modifiedby" => $addedbyid);
                                            }
                                        } else {
                                            ws_response("Fail", "Invalid file type.");
                                        }
                                    }
                                }else if($_FILES['quotation'.$id]['name'] != '' && !empty($quotationfileid)){
                          
                                  $this->Crm_quotation->_where = "id=".$quotationfileid;
                                  $FileData = $this->Crm_quotation->getRecordsByID();
                          
                                  $file = reuploadFile('quotationfile'.$id, 'CRMQUOTATION', $FileData['file'], QUOTATION_PATH, '*', '', 1, QUOTATION_LOCAL_PATH);
                                  if($file !== 0 && $file !== 2){
                                      
                                    $updatedata = array("file"=>$file,
                                                "description"=>$quotationdescription,
                                                "date"=>$quotationdate,
                                                "modifieddate"=>$createddate,
                                                "modifiedby"=>$addedbyid);
                        
                                    $this->Crm_quotation->_where = "id=".$quotationfileid;
                                    $this->Crm_quotation->Edit($updatedata);
                                  }
                                }else{
                                  
                                  $updatedata = array("description"=>$quotationdescription,
                                                      "date"=>$quotationdate,
                                                      "modifieddate"=>$createddate,
                                                      "modifiedby"=>$addedbyid);
                          
                                  $this->Crm_quotation->_where = "id=".$quotationfileid;
                                  $this->Crm_quotation->Edit($updatedata);
                                  
                                }
                            }
                            if(!empty($insertquotationdata)){
                                $this->Crm_quotation->add_batch($insertquotationdata);  
                            }

                            $this->data = array("id" => $PostData['id'],"inquiryproductid"=>$inquiry_product_id_arr,"installmentid"=>$installmentidsarr);
                            if($editinquiry || isset($editinquiryproduct) || count($inquiry_product_id_arr)>0){
                                if($PostData['assigntoid']!=$checkassignto['inquiryassignto']){
                                
                                    $insertdata = array('inquiryid' => $PostData['id'],
                                                        'transferfrom'=>$checkassignto['inquiryassignto'],
                                                        'transferto'=>$PostData['assigntoid'],
                                                        'createddate'=>$createddate,
                                                        'modifieddate'=>$createddate,
                                                        'addedby'=>$PostData['assigntoid'],
                                                        'modifiedby'=>$PostData['assigntoid']
                                                    );
                                    
                                    $this->Crm_inquiry->_table = tbl_crminquirytransferhistory;
                                    $this->Crm_inquiry->Add($insertdata);  
                                    
                                    $inquirydata = $this->Crm_inquiry->getInquiryDetailForEmail($PostData['id'],$PostData['addedby']);
                               
                                    if(!is_null($inquirydata) && $inquirydata['checknewtransferinquiry']==1){
                                        $this->inquirydata['inquirydata'] = $inquirydata;
                                        $table = $this->load->view(ADMINFOLDER."crm_inquiry/inquiryreporttable",$this->inquirydata,true);
                                    
                                        $mailBodyArr1 = array(
                                            "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                            "{name}" => $inquirydata['employeename'],
                                            "{assignby}" => $inquirydata['assignemployeename'],
                                            "{detailtable}"=>$table,
                                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                            "{companyname}" => COMPANY_NAME
                                        );
                            
                                        //Send mail with email format store in database
                                        $mailid=array_search('Inquiry Assign',$this->Emailformattype);
                                        $this->Crm_inquiry->sendMail($mailid,$inquirydata['email'], $mailBodyArr1);
                                    }
                                }

                                if(count($checkassignto)>0 && $PostData['receivedstatus']!=$checkassignto['status']){
                                    $inquiryemployee = $this->Crm_inquiry->getinquiryemployees($PostData['id'],$PostData['receivedstatus']);
                                    if(count($inquiryemployee)>0){
                                        $this->readdb->select("ci.id as inquiryid,(select name from ".tbl_user." where id=inquiryassignto) as employeename,DATE(ci.createddate) as date,inquirynote as notes,m.companyname,(select name from ".tbl_inquirystatuses." where id=ci.status) as statusname");
                                        $this->readdb->from(tbl_crminquiry." as ci");
                                        $this->readdb->join(tbl_member." as m","ci.memberid=m.id");
                                        $this->readdb->where(array("ci.id"=>$PostData['id']));
                                        $inquirydata=$this->readdb->get()->row_array();

                                        if(!is_null($inquirydata)){
                                            $this->inquirydata['inquirydata'] = $inquirydata;
                                            $table = $this->load->view(ADMINFOLDER."crm_inquiry/inquiryreporttable",$this->inquirydata,true);
                                            
                                            foreach($inquiryemployee as $ie){
                                                $mailBodyArr1 = array(
                                                    "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                                    "{name}" => $ie['name'],
                                                    "{detailtable}"=>$table,
                                                    "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                                    "{companyname}" => COMPANY_NAME
                                                );
                                    
                                                //Send mail with email format store in database
                                                $mailid=array_search('Inquiry Status Change',$this->Emailformattype);
                                                $this->Crm_inquiry->sendMail($mailid,$ie['email'], $mailBodyArr1);
                                            }
                                        }
                                    }
                                }
    
                                $this->User->_fields="name";
                                $this->User->_where = 'id='.$PostData['addedby'];
                                $reportingtoemployee = $this->User->getRecordsByID();
                                $employeename="";
                                if(count($reportingtoemployee)>0){
                                    $employeename = $reportingtoemployee['name'];
                                }
                             
                                $fcmquery = $this->db->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$PostData['assigntoid']); 
                                $employeearr = $androidfcmid = $iosfcmid = array();
                                if($fcmquery->num_rows() > 0) {
                                    $this->load->model('Common_model','FCMData');
                                    $type = 20;
                                    $msg = "Inquiry has been updated";
                                    $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                    if($employeename!=""){
                                        $description = "Inquiry has been updated by ".$employeename;
                                    }else{
                                        $description = "";   
                                    } 
                                    $employeearr[] = $PostData['assigntoid'];
                                   
                                    foreach ($fcmquery->result_array() as $fcmrow) {
                                        if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                            $androidfcmid[] = $fcmrow['fcm']; 	 
                                        }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                            $iosfcmid[] = $fcmrow['fcm'];
                                        }
                                    }   
                                    if(!empty($androidfcmid)){
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                                    }
                                    if(!empty($iosfcmid)){							
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                                    }
                                    $notificationdata = array('memberid' => implode(",",$employeearr),
                                          'message' => $pushMessage,
                                          'type' => $type,
                                          'usertype' => 1,
                                          'description'=>$description,
                                          'createddate' => $createddate
                                    );
                                      
                                    $this->load->model('Notification_model','Notification');
                                    $this->Notification->Add($notificationdata);
                                }
                                
                                ws_response("Success","Inquiry updated", $this->data);
                            } else {
                                ws_response("Success","Inquiry already updated",$this->data);
                            } 

                        } else {
                            /******ADD INQUIRY******/
                           
                            if($PostData['isinstallment']==1){
                                $totalinstallment = count($PostData['installmentdata']);
                            }else{
                                $totalinstallment = 0;
                            }
                            
                            $insertdata = array(
                                'channelid'=>CUSTOMERCHANNELID,
                                'memberid'=>$PostData['memberid'],
                                'inquiryassignto'=>$PostData['assigntoid'],
                                'inquiryfollowuptype'=>$PostData['followupcategoryid'],
                                'inquirynote'=>$notes,
                                'noofinstallment'=>$totalinstallment,
                                'inquiryleadsourceid'=>$inquiryleadsourceid,
                                'contactid'=>$PostData['contactid'],
                                'modifieddate' => $createddate,
                                'modifiedby' => $PostData['assigntoid'],
                                'createddate'=>$createddate,
                                'addedby'=> $PostData['addedby'],
                                'status'=>$PostData['receivedstatus']
                            );
                            $this->Crm_inquiry->_table = tbl_crminquiry;
                            $inquiryid = $this->Crm_inquiry->add($insertdata);

                            $inquiry_product_id_arr=array();

                            foreach($insertinstallmentdata as $ikey => $ivalue) {
                                $insertinstallmentdata[$ikey]['inquiryid']=$inquiryid;
                            }
                            if(count($insertinstallmentdata)>0){
                                $this->writedb->insert_batch(tbl_inquiryproductinstallment,$insertinstallmentdata);
                                $first_id = $this->writedb->insert_id();
                                $last_id = $first_id + (count($insertinstallmentdata)-1);
                                for($gi=$first_id;$gi<=$last_id;$gi++){
                                    $installmentidsarr[]=$gi;
                                }
                            }
                            foreach($insertinquiryproductdata as $ipd) {
                                $ipd['inquiryid']=$inquiryid;
                                $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                                $add = $this->Crm_inquiry->add($ipd);
                                $inquiry_product_id_arr[]=$add;
                            }
                            if($inquiryid!=""){
                                $this->Crm_inquiry->_table = tbl_crminquirytransferhistory;
                                
                                $insertdata=array('inquiryid' => $inquiryid,
                                                'transferfrom'=>$PostData['addedby'],
                                                'transferto'=>$PostData['assigntoid'],
                                                'createddate'=>$createddate,
                                                'reason'=>'New Inquiry Added',
                                                'modifieddate'=>$createddate,
                                                'addedby'=>$PostData['addedby'],
                                                'modifiedby'=>$PostData['addedby']
                                            );
                                $this->Crm_inquiry->Add($insertdata);  

                                $inquirydata = $this->Crm_inquiry->getInquiryDetailForEmail($inquiryid,$PostData['addedby']);

                                if(!is_null($inquirydata) && $inquirydata['checknewtransferinquiry']==1){
                                    $this->inquirydata['inquirydata'] = $inquirydata;
                                    $table=$this->load->view(ADMINFOLDER."crm_inquiry/inquiryreporttable",$this->inquirydata,true);
                                    
                                    $mailBodyArr1 = array(
                                        "{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none;width: 200px; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                                        "{name}" => $inquirydata['employeename'],
                                        "{assignby}" => $inquirydata['assignemployeename'],
                                        "{detailtable}"=>$table,
                                        "{companyemail}" => explode(",",COMPANY_EMAIL)[0],
                                        "{companyname}" => COMPANY_NAME
                                    );
                                
                                    //Send mail with email format store in database
                                    $mailid=array_search('Inquiry Assign',$this->Emailformattype);
                                    $this->Crm_inquiry->sendMail($mailid,$inquirydata['email'], $mailBodyArr1);
                                }

                                $insertquotationdata = array();
                                foreach ($_FILES as $key => $value) {
                                    $id = preg_replace('/[^0-9]/', '', $key);
                        
                                    $quotationdescription = $PostData['quotationdata'][$id]['description'];
                                    $quotationdate = ($PostData['quotationdata'][$id]['date']!="")?$this->general_model->convertdate($PostData['quotationdata'][$id]['date']):"";
                                    $addedbyid = $PostData['quotationdata'][$id]['addedbyid'];

                                    if($_FILES['quotation'.$id]['name']!=''){
                                        $file = uploadFile('quotation'.$id, 'CRMQUOTATION',QUOTATION_PATH,"*","",'1',QUOTATION_LOCAL_PATH);
                                        if($file !== 0){
                                            if ($file==2) {
                                                ws_response("Fail", "File not upload.");
                                            }else{
                                           
                                                $insertquotationdata[] = array("inquiryid" => $inquiryid,
                                                                "file" => $file,
                                                                "description" => $quotationdescription,
                                                                "date" => $quotationdate,
                                                                "createddate" => $createddate,
                                                                "addedby" => $addedbyid,
                                                                "modifieddate" => $createddate,
                                                                "modifiedby" => $addedbyid);
                                            }
                                        
                                        }else{
                                            ws_response("Fail", "Invalid file type.");
                                        } 
                                    }
                                }
                                
                                if(!empty($insertquotationdata)){
                                    $this->Crm_quotation->add_batch($insertquotationdata); 
                                }

                                $this->data = array("id" => $inquiryid,
                                                    "inquiryproductid"=>$inquiry_product_id_arr,"installmentid"=>$installmentidsarr
                                                );
                                
                                $this->User->_fields="name";
                                $this->User->_where = 'id='.$PostData['addedby'];
                                $reportingtoemployee = $this->User->getRecordsByID();
                                $employeename="";
                                if(count($reportingtoemployee)>0){
                                    $employeename = $reportingtoemployee['name'];
                                }

                                $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$PostData['assigntoid']); 
                                $employeearr = $androidfcmid = $iosfcmid = array();

                                if($fcmquery->num_rows() > 0) {
                                    $this->load->model('Common_model','FCMData');     
                                    $type = 19;
                                    $msg = "New inquiry added";
                                    $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                    if($employeename!=""){
                                        $description = "New inquiry added by ".$employeename;
                                    }else{
                                        $description = "";   
                                    }
                                    $employeearr[] = $PostData['assigntoid'];
                                    
                                    foreach ($fcmquery->result_array() as $fcmrow) {
                                        if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                            $androidfcmid[] = $fcmrow['fcm']; 	 
                                        }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                            $iosfcmid[] = $fcmrow['fcm'];
                                        }
                                    }   
                                    if(!empty($androidfcmid)){
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                                    }
                                    if(!empty($iosfcmid)){							
                                        $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                                    }
                                    
                                    $notificationdata = array('employeeid' => implode(",",$employeearr),
                                                            'message' => $pushMessage,
                                                            'type' => $type,
                                                            'usertype' => 1,
                                                            'description'=>$description,
                                                            'createddate' => $createddate
                                                        );
                                    
                                    $this->load->model('Notification_model','Notification');
                                    $this->Notification->Add($notificationdata);
                                }
                                ws_response("Success","Inquiry added", $this->data);
                            } 
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getinquirydetail() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['inquiryid'])){

                        if(empty($PostData['employeeid']) || empty($PostData['inquiryid'])){
                            ws_response("Fail", "Fields value are missing.");
                        }else{

                            $inquiryid = (isset($PostData['inquiryid']))?$PostData['inquiryid']:0;

                            $query = $this->readdb->select("ci.id,ci.memberid,contactid,inquiryassignto as assignto,inquiryfollowuptype as followuptype,inquirynote as notes,ci.createddate,ci.addedby,m.companyname,m.name as membername,ci.inquiryleadsourceid,
                                                        IFNULL((SELECT ls.name FROM ".tbl_leadsource." as ls WHERE ls.id=ci.inquiryleadsourceid),'') as leadsourcename,
                                                        m.remarks as memberremark,
                                                        IFNULL((SELECT cd.email FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as memberemail,
                                                        IFNULL((SELECT cd.mobileno FROM ".tbl_contactdetail." as cd WHERE cd.id=ci.contactid),'') as membermobile,noofinstallment,
                                                        (select name from ".tbl_inquirystatuses." where id=ci.status)as statusname,
                                                        IFNULL((select color from ".tbl_inquirystatuses." where id=ci.status),'')as statuscolor,
                                                        IF(ci.inquiryassignto=".$PostData['employeeid'].",1,0)as direct,
                                                        IFNULL((SELECT e.name FROM ".tbl_user." as e WHERE e.id=ci.inquiryassignto),'') as assigntoname,
                                                        IF((ci.id IN(SELECT inquiryid FROM ".tbl_crminquirytransferhistory." as ith WHERE ith.transferfrom=".$PostData['employeeid']." OR ith.addedby=".$PostData['employeeid'].") AND ci.inquiryassignto!=".$PostData['employeeid']."),1,0)as indirect,
                                                        m.latitude,m.longitude");
                            $this->readdb->from(tbl_crminquiry." as ci");
                            $this->readdb->join(tbl_member." as m","ci.memberid=m.id",'LEFT');
                            $this->readdb->where("ci.id=".$inquiryid);
                            
                            $query = $this->readdb->get();
                            $enquiryproduct = $query->row_array();
                            
                            $this->load->model("Crm_inquiry_model","Crm_inquiry");
                            $this->load->model("Product_prices_model","Product_prices");

                            $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                            if(!empty($enquiryproduct)){
                                
                                $product_arr=array();
                                $this->Crm_inquiry->_table = tbl_crminquiryproduct;
                                $myproduct_arr = $this->Crm_inquiry->getinquiryproduct($enquiryproduct['id']);

                                if(!empty($myproduct_arr)){
                                    foreach($myproduct_arr as $rowdata){
                                         
                                        $VariantData = $this->Product_prices->getProductpriceById($rowdata['priceid']);
                                        $variantdata = array();
                                        if(!empty($VariantData)){
                                            
                                                $variantdata[] = array("priceid"=>$VariantData['id'],
                                                                     "actualprice"=>$VariantData['price'],
                                                                     "variantname"=>$VariantData['pricewithvariant']
                                                                 );
                                        }
                                        $rowdata['variantdata'] = $variantdata;
                                        $product_arr[] = $rowdata;
                                    }
                                }

                                $installment_arr=array();
                                if($enquiryproduct['noofinstallment']>0){
                                    $this->Crm_inquiry->_table = tbl_inquiryproductinstallment;
                                    $this->Crm_inquiry->_order = "inquiryid DESC";
                                    $this->Crm_inquiry->_where = array("inquiryid"=>$enquiryproduct['id']);
                                    $installments = $this->Crm_inquiry->getRecordByID();
                                    
                                    foreach ($installments as $is) {
                                        if($is['date']=="0000-00-00"){
                                            $is['date']="";
                                        }
                                        if($is['paymentdate']=="0000-00-00"){
                                            $is['paymentdate']="";
                                        }
                                        $installment_arr[]=array('installmentid'=>$is['id'],'amount'=>$is['amount'],'percent'=>$is['percentage'],'installmentdate'=>$is['date'],'paymentddate'=>$is['paymentdate'],'receivedstatus'=>$is['status']);
                                    }
                                }

                                if(count($installment_arr)>0){
                                    $installment="1";
                                }else{
                                    $installment="0";
                                }
                                if(is_null($enquiryproduct['statusname'])){
                                    $enquiryproduct['statusname']="";
                                }

                                $this->load->model("Followup_model","Followup");
                                $followupdata = $this->Followup->getInquiryFollowupForapi($enquiryproduct['id']);
                                
                                $this->load->model('Crm_quotation_model','Crm_quotation');
                                $quotationdata = $this->Crm_quotation->getQuotationForapi($enquiryproduct['id']);

                                $transferhistory = $this->Crm_inquiry->getInquiryTransferForAPI($enquiryproduct['id']);                                
                                
                                $this->data= array("inquiryid"=>$enquiryproduct['id'],
                                                    "direct"=>$enquiryproduct['direct'],
                                                    "indirect"=>$enquiryproduct['indirect'],
                                                    "memberid"=>$enquiryproduct['memberid'],
                                                    'contactid'=>$enquiryproduct['contactid'],
                                                    "membername"=>$enquiryproduct['membername'],
                                                    "memberemail"=>$enquiryproduct['memberemail'],
                                                    "membermobile"=>$enquiryproduct['membermobile'],
                                                    "memberlatitude"=>$enquiryproduct['latitude'],
                                                    "memberlongitude"=>$enquiryproduct['longitude'],
                                                    "memberremark"=>$enquiryproduct['memberremark'],
                                                    "companyname"=>$enquiryproduct['companyname'],
                                                    "assigntoid"=>$enquiryproduct['assignto'],
                                                    "assigntoname"=>$enquiryproduct['assigntoname'],
                                                    "addedby"=>$enquiryproduct['addedby'],
                                                    "followupcategoryid"=>$enquiryproduct['followuptype'],
                                                    "date"=>date("Y-m-d",strtotime($enquiryproduct['createddate'])),
                                                    "note"=>$enquiryproduct['notes'],
                                                    "createddate"=>date("Y-m-d h:i:s a",strtotime($enquiryproduct['createddate'])),
                                                    "isinstallment"=>$installment,
                                                    'receivedstatus'=> $enquiryproduct['statusname'],
                                                    'receivedstatuscolor'=> $enquiryproduct['statuscolor'],
                                                    'leadsourceid'=> $enquiryproduct['inquiryleadsourceid'],
                                                    'leadsourcename'=> $enquiryproduct['leadsourcename'],
                                                    "productdata"=>$product_arr,
                                                    "installmentdata"=>$installment_arr,
                                                    "transferhistory"=>$transferhistory,
                                                    "quotationdata"=>$quotationdata,
                                                    "followupdata"=>$followupdata
                                                );
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "Inquiry product not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                         }
                    } else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }
    function getproductinquiryofsalespersonmember() {
        
		$PostData = json_decode($this->PostData['data'], true);	
		$employeeid = isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
        $channelid = !empty($PostData['channelid']) ? trim($PostData['channelid']) : '0';
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : '0';
        $counter =  isset($PostData['counter']) ? trim($PostData['counter']) : '';

        if(empty($employeeid) || $counter=="") {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('User_model', 'User');  
            $this->User->_where = array("id"=>$employeeid);
            $count = $this->User->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{	
				
				$this->load->model('Product_inquiry_model','Product_inquiry');	
				$inquirydata = $this->Product_inquiry->getProductInquiryOfSalesPersonMember($employeeid,$channelid,$memberid,$counter);

                $this->data=array();
				if(!empty($inquirydata)) {
                   
                    foreach($inquirydata as $inquiry){
                       
                        $this->data[] = array(
                            "productname" => $inquiry['productname'],
                            "channel" => $inquiry['channel'],
                            "membername" => $inquiry['membername'],
                            "membercode" => $inquiry['membercode'],
                            "name" => $inquiry['name'],
                            "email" => $inquiry['email'],
                            "mobile" => $inquiry['mobile'],
                            "organizations" => $inquiry['organizations'],
                            "message" => $inquiry['msg'],
                            "address" => $inquiry['address'],
                            "entrydate" => $inquiry['createddate'],
                        );
                    }
					ws_response('success', '', $this->data);		
				} else {
					ws_response('fail','Product inquiry not available.');
				}
			}           
        }
    }
    function getsalespersonmemberproductstock() {
        
		$PostData = json_decode($this->PostData['data'], true);	
		$employeeid = isset($PostData['employeeid']) ? trim($PostData['employeeid']) : '';
        $channelid = !empty($PostData['channelid']) ? trim($PostData['channelid']) : '0';
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : '0';
      
        if(empty($employeeid) || empty($channelid) || empty($memberid)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            $this->load->model('User_model', 'User');  
            $this->User->_where = array("id"=>$employeeid);
            $count = $this->User->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{	
				$this->load->model('Member_model', 'Member');  
                $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                $count = $this->Member->CountRecords();
        
                if($count==0){
                    ws_response('fail', "Member not available.");
                }else{	
                    $this->load->model('Stock_report_model','Stock');	
                    $productstockdata = $this->Stock->getSalesPersonMemberProductStock($employeeid,$channelid,$memberid);

                    $this->data=array();
                    if(!empty($productstockdata)) {
                    
                        foreach($productstockdata as $product){
                        
                            $this->data[] = array(
                                "productname" => $product['productname'],
                                "currentstock" => $product['stock'],
                                "membername" => $product['membername'],
                                "membercode" => $product['membercode']
                            );
                        }
                        ws_response('success', '', $this->data);		
                    } else {
                        ws_response('fail','Data not available.');
                    }
                }
			}           
        }
	}
    /**End Delight CRM API */
 } 
	