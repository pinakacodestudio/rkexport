<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Price_list extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Price_list');
        $this->load->model('Price_list_model', 'Price_list');
        $this->load->model('Product_model', 'Product');
        $this->load->model('Product_file_model', 'Product_file');

        $this->load->model('Side_navigation_model','Side_navigation');
        $this->load->model('Price_list_model', 'Price_list');
    }

    public function index() {
        $this->viewData['title'] = "Price List";
        $this->viewData['module'] = "price_list/Price_list";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model("Channel_model","Channel"); 
        // $this->viewData['channeldata'] = $this->Channel->getChannelList('all');
        $this->viewData['categorydata'] = $this->Product->getAllCategory();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Price List','View price list.');
        }

        $this->admin_headerlib->add_javascript("Price_list", "pages/price_list.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function getpricelistdata(){
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $producttype = $PostData['producttype'];
        $categoryid = isset($PostData['categoryid'])?$PostData['categoryid']:'';
        
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Product_prices_model","Product_prices"); 

        $channeltype = ($producttype==0?'all':"onlyvendor"); 
        $channeldata = $this->Channel->getChannelList($channeltype);

        $req = array();
        $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true,"class"=>"width8");
        $req['COLUMNS'][] = array('title'=>'Product Name',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Category',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Price',"sortable"=>true,"class"=>"text-right");
        
        if(!empty($channeldata)){
            foreach($channeldata as $channel){
                $req['COLUMNS'][] = array('title'=>ucwords($channel['name']).'<input type="hidden" name="channelid[]" id="channelid'.$channel['id'].'" value="'.$channel['id'].'">',"sortable"=>false,"class"=>"text-right");
            }
        }
        $req['COLUMNS'][] = array('title'=>'Actions',"sortable"=>false,"class"=>"width5");

        $pricelistdata = $this->Price_list->getpricelistdata($producttype,$categoryid);
        $counter=0;
        if(!empty($pricelistdata)){

            foreach ($pricelistdata as $index=>$productrow) {
                $channeldataarr = $data = array();
                $actions = $allowproduct = $varianthtml = $productname = '';
                
                $actions .= '<a href="'.ADMIN_URL.'price-list/edit-product-price/'. $productrow['id'].'/'. $productrow['priceid'].'/'.'" class="'.edit_class.'" title="'.edit_title.'">'.edit_text.'</a>'; 

                $actions .= '<a href="'.ADMIN_URL.'product/view-product/'. $productrow['id'].'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';        

                if($productrow['isuniversal']==0 && $productrow['variantid']!=''){
                    $variantdata = $this->Product_combination->getProductVariantDetails($productrow['id'],$productrow['variantid']);
    
                    if(!empty($variantdata)){
                        $varianthtml .= "<div class='row' style=''>";
                        foreach($variantdata as $variant){
                            $varianthtml .= "<div class='col-md-12 p-n'>";
                            $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                            $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                            $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                            $varianthtml .= "</div>";
                        }
                        $varianthtml .= "</div>";
                    }
                    $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($productrow['productname']).'</a>';
                }else{
                    $productname = ucwords($productrow['productname']);
                }

                $productname = $productname.'<input type="hidden" name="productid[]" id="productid'.$productrow['id'].'" value="'.$productrow['id'].'"><input type="hidden" name="productpriceid['.$productrow['id'].'][]" id="productpriceid'.$productrow['priceid'].'" value="'.$productrow['priceid'].'">';

                if($productrow['productimage']!="" && file_exists(PRODUCT_PATH.$productrow['productimage'])){
                    $productimage = $productrow['productimage'];
                }else{
                    $productimage = PRODUCTDEFAULTIMAGE;
                }
                $productdetail = '<img class="pull-left thumbwidth mr-sm" src="'.PRODUCT.$productimage.'" style="margin-bottom: 5px;"><div class="" style="display: inline-block;">'.$productname.'</div>'; 

                if(!empty($channeldata)){
                    foreach($channeldata as $channel){
    
                        $pricedata = $this->Product_prices->getChannelBasicPriceByChannelID($productrow['id'],$productrow['priceid'],$channel['id']);
                        $channelprice = "0";
                        if(!empty($pricedata)){
                            if(number_format($pricedata['minprice'],2,'.','') == number_format($pricedata['maxprice'],2,'.','')){
                                $channelprice = numberFormat($pricedata['minprice'], 2, ',');
                            }else{
                                $channelprice = numberFormat($pricedata['minprice'], 2, ',')." - ".numberFormat($pricedata['maxprice'], 2, ',');
                            }
                        }
                        $channeldataarr[] = $channelprice;
                    }
                }

                if(number_format($productrow['minprice'],2,'.','') == number_format($productrow['maxprice'],2,'.','')){
					$productprice = numberFormat($productrow['minprice'], 2, ',');
				}else{
					$productprice = numberFormat($productrow['minprice'], 2, ',')." - ".numberFormat($productrow['maxprice'], 2, ',');
				}
                
                $req['DATA'][] = array_merge(
                                        array(++$counter,
                                            $productdetail,
                                            $productrow['categoryname'],
                                            $productprice
                                        ),
                                        $channeldataarr,
                                        array($actions)
                                    );
            }
        }
        // print_r($req); exit;
        
		echo json_encode($req);
    }
    public function add_product_price() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Product Price";
        $this->viewData['module'] = "price_list/Add_product_price";   
        
        $this->viewData['categorydata'] = $this->Price_list->getProductCategory(1);
        
        $this->admin_headerlib->add_javascript("add_product_price", "pages/add_product_price.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function product_price_add() {
        
        $PostData = $this->input->post();
        $productid = trim($PostData['productid']);
        $priceid = trim($PostData['priceid']);
        $channelidarray = isset($PostData['channelid'])?$PostData['channelid']:"";  
       
        $json = array();
        $insertMultiPriceData = array();
        if(!empty($channelidarray)){
            foreach($channelidarray as $key=>$channelid){

                $allowproduct = isset($PostData['allowproduct'][$channelid])?1:0;
                $minimumsalesprice = !empty($PostData['minimumsalesprice'][$key])?$PostData['minimumsalesprice'][$key]:"";
                $minimumqty = !empty($PostData['minqty'][$key])?$PostData['minqty'][$key]:"";
                $maximumqty = !empty($PostData['maxqty'][$key])?$PostData['maxqty'][$key]:"";
                $pricetype = isset($PostData['pricetype'.$channelid])?$PostData['pricetype'.$channelid]:0;

                $this->Price_list->_table = tbl_productbasicpricemapping;
                $this->Price_list->_fields = "id";
                $this->Price_list->_where = array("productid"=>$productid,"productpriceid"=>$priceid,"channelid"=>$channelid);
                $Check = $this->Price_list->getRecordsById();

                if(empty($Check)){

                    $checkprice = 0;
                    if($pricetype==0){
                        $salesprice = isset($PostData['salesprice'][$key])?$PostData['salesprice'][$key]:"";
                        $discountpercent = isset($PostData['discper'][$key])?$PostData['discper'][$key]:"";
                        $discountamount = isset($PostData['discamnt'][$key])?$PostData['discamnt'][$key]:"";
                    
                        $checkprice = ($salesprice!="")?1:0;
                    }else{
                        if(!empty($PostData['variantsalesprice'][$channelid])){
                            foreach($PostData['variantsalesprice'][$channelid] as $variantpricerow){
                                if(!empty($variantpricerow)){
                                    $checkprice = 1; 
                                }
                            }
                        }
                    }
                    if($checkprice == 1){
                        $InsertData = array('productid' => $productid,
                                            'productpriceid'=>$priceid,
                                            'pricetype' => $pricetype,
                                            'channelid' => $channelid,
                                            'allowproduct' => $allowproduct,
                                            'minimumsalesprice' => $minimumsalesprice,
                                            'minimumqty' => $minimumqty,                              
                                            'maximumqty' => $maximumqty
                                        );

                        
                        $productbasicpricemappingid = $this->Price_list->Add($InsertData);   
                        if($productbasicpricemappingid){
                            if($pricetype==1){
                                if(!empty($PostData['variantsalesprice'][$channelid])){
                                    foreach($PostData['variantsalesprice'][$channelid] as $k=>$variantprice) {
    
                                        $insertMultiPriceData[] = array(
                                                "productbasicpricemappingid"=>$productbasicpricemappingid,
                                                "salesprice"=>$variantprice,
                                                "quantity" => $PostData['variantqty'][$channelid][$k],
                                                'discount' => $PostData['variantdiscpercent'][$channelid][$k]
                                            );
                                    }
                                }
                            }else{
                                $insertMultiPriceData[] = array(
                                        "productbasicpricemappingid"=>$productbasicpricemappingid,
                                        "salesprice"=>$salesprice,
                                        "quantity" => 1,
                                        'discount' => $discountpercent
                                    );
                            }
                        }
                    }
                }
            }
            
            if(!empty($insertMultiPriceData)){
                $this->Price_list->_table = tbl_productbasicquantityprice;
                $this->Price_list->add_batch($insertMultiPriceData);
            }

            echo 1;
        }
    }
    public function edit_product_price($productid,$priceid) {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Product Price";
        $this->viewData['module'] = "price_list/Add_product_price";   
        $this->viewData['action'] = 1;

        $this->viewData['categorydata'] = $this->Price_list->getProductCategory();
        $pricelistdata = $this->Price_list->getPriceListDataByPriceID($productid,$priceid);

        if(empty($pricelistdata)){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $channeltype = ($pricelistdata['producttype']==0?'all':"onlyvendor"); 
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList($channeltype);

        $this->load->model("Product_prices_model","Product_prices"); 
        $channelarray = array();
        if(!empty($channeldata)){
            foreach($channeldata as $channel){ 

                $pricedata = $this->Price_list->getChannelPriceDataByPriceIDORChannelID($productid,$priceid,$channel['id']);

                if(!empty($pricedata)){
                    $pricedata['multipleprice'] = $this->Product_prices->getProductBasicQuantityPriceDataByPriceID($channel['id'],$priceid,$productid); 
                }
                $channelarray[] = array_merge($channel,array("pricedata"=>$pricedata));
            }
        }
        $this->viewData['pricelistdata'] = $pricelistdata;
        $this->viewData['channeldata'] = $channelarray;
        // pre($channelarray);

        $this->admin_headerlib->add_javascript("add_product_price", "pages/add_product_price.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function update_product_price() {
        
        $PostData = $this->input->post();
        
        $productid = trim($PostData['postproductid']);
        $priceid = trim($PostData['postpriceid']);
        $channelidarray = isset($PostData['channelid'])?$PostData['channelid']:"";  
       
        $InsertMultiplePriceData = $UpdateMultiplePriceData = $UpdatedProductQuantityPrice = array();
        if(!empty($channelidarray)){
            foreach($channelidarray as $key=>$channelid){

                $productbasicpricemapingid = isset($PostData['productbasicpricemappingid'][$key])?$PostData['productbasicpricemappingid'][$key]:'0';
                $allowproduct = isset($PostData['allowproduct'.$channelid])?1:0;
                $minimumsalesprice = !empty($PostData['minimumsalesprice'][$key])?$PostData['minimumsalesprice'][$key]:"";
                $minimumqty = !empty($PostData['minqty'][$key])?$PostData['minqty'][$key]:"";
                $maximumqty = !empty($PostData['maxqty'][$key])?$PostData['maxqty'][$key]:"";
                $pricetype = isset($PostData['pricetype'.$channelid])?$PostData['pricetype'.$channelid]:0;
                
                if(!empty($productbasicpricemapingid)){
                    
                    $updatedata = array('pricetype' => $pricetype,
                                        'allowproduct' => $allowproduct,
                                        'minimumsalesprice' => $minimumsalesprice,                              
                                        'minimumqty' => $minimumqty,                              
                                        'maximumqty' => $maximumqty
                                    );

                    $this->Price_list->_table = tbl_productbasicpricemapping;
                    $this->Price_list->_where = array("id"=>$productbasicpricemapingid);
                    $this->Price_list->Edit($updatedata);  
                    
                    if($pricetype==1) { 
                        if(!empty($PostData['variantsalesprice'][$channelid])){
                            foreach ($PostData['variantsalesprice'][$channelid] as $vp=>$variantpricerow) {
    
                                $productbasicquantitypricesid = isset($PostData['productbasicquantitypricesid'][$channelid][$vp])?$PostData['productbasicquantitypricesid'][$channelid][$vp]:"";
    
                                if($variantpricerow > 0 && $PostData['variantqty'][$channelid][$vp] > 0){
                                    
                                    if(!empty($productbasicquantitypricesid)){
                                       
                                        $UpdateMultiplePriceData[] = array(
                                            "id"=>$productbasicquantitypricesid,
                                            "salesprice"=>$variantpricerow,
                                            "quantity"=>$PostData['variantqty'][$channelid][$vp],
                                            "discount"=>$PostData['variantdiscpercent'][$channelid][$vp]
                                        );
    
                                        $UpdatedProductQuantityPrice[] = $productbasicquantitypricesid;
                                    }else{
    
                                        $InsertMultiplePriceData[] = array(
                                            "productbasicpricemappingid"=>$productbasicpricemapingid,
                                            "salesprice"=>$variantpricerow,
                                            "quantity"=>$PostData['variantqty'][$channelid][$vp],
                                            "discount"=>$PostData['variantdiscpercent'][$channelid][$vp]
                                        );
                                    }
                                }
                            }
                        }
                    }else{
                        $singlequantitypricesid = !empty($PostData['singlequantitypricesid'][$key])?$PostData['singlequantitypricesid'][$key]:"";
    
                        if($PostData['salesprice'][$key] > 0){
                            
                            if(!empty($singlequantitypricesid)){
                                
                                $UpdateMultiplePriceData[] = array(
                                    "id"=>$singlequantitypricesid,
                                    "salesprice"=>$PostData['salesprice'][$key],
                                    "quantity"=>1,
                                    "discount"=>$PostData['discper'][$key]
                                );
    
                                $UpdatedProductQuantityPrice[] = $singlequantitypricesid;
                            }else{
    
                                $InsertMultiplePriceData[] = array(
                                    "productbasicpricemappingid"=>$productbasicpricemapingid,
                                    "salesprice"=>$PostData['salesprice'][$key],
                                    "quantity"=>1,
                                    "discount"=>$PostData['discper'][$key]
                                );
                            }
                        }
                    }
                }else{
                    $this->Price_list->_table = tbl_productbasicpricemapping;
                    $this->Price_list->_fields = "id";
                    $this->Price_list->_where = array("productid"=>$productid,"productpriceid"=>$priceid,"channelid"=>$channelid);
                    $Check = $this->Price_list->getRecordsById();
    
                    if(empty($Check)){
    
                        $checkprice = 0;
                        if($pricetype==0){
                            $salesprice = isset($PostData['salesprice'][$key])?$PostData['salesprice'][$key]:"";
                            $discountpercent = isset($PostData['discper'][$key])?$PostData['discper'][$key]:"";
                            $discountamount = isset($PostData['discamnt'][$key])?$PostData['discamnt'][$key]:"";
                        
                            $checkprice = ($salesprice!="")?1:0;
                        }else{
                            if(!empty($PostData['variantsalesprice'][$channelid])){
                                foreach($PostData['variantsalesprice'][$channelid] as $variantpricerow){
                                    if(!empty($variantpricerow)){
                                        $checkprice = 1; 
                                    }
                                }
                            }
                        }
                        if($checkprice == 1){
                            $InsertData = array('productid' => $productid,
                                                'productpriceid'=>$priceid,
                                                'pricetype' => $pricetype,
                                                'channelid' => $channelid,
                                                'allowproduct' => $allowproduct,
                                                'minimumsalesprice' => $minimumsalesprice,                              
                                                'minimumqty' => $minimumqty,                              
                                                'maximumqty' => $maximumqty
                                            );
                            
                            $productbasicpricemappingid = $this->Price_list->Add($InsertData);   
                            if($productbasicpricemappingid){
                                if($pricetype==1){
                                    if(!empty($PostData['variantsalesprice'][$channelid])){
                                        foreach($PostData['variantsalesprice'][$channelid] as $k=>$variantprice) {
        
                                            $InsertMultiplePriceData[] = array(
                                                    "productbasicpricemappingid"=>$productbasicpricemappingid,
                                                    "salesprice"=>$variantprice,
                                                    "quantity" => $PostData['variantqty'][$channelid][$k],
                                                    'discount' => $PostData['variantdiscpercent'][$channelid][$k]
                                                );
                                        }
                                    }
                                }else{
                                    $InsertMultiplePriceData[] = array(
                                            "productbasicpricemappingid"=>$productbasicpricemappingid,
                                            "salesprice"=>$salesprice,
                                            "quantity" => 1,
                                            'discount' => $discountpercent
                                        );
                                }
                            }
                        }
                    }
                }
            }

            $this->Price_list->_table = tbl_productbasicquantityprice;
            $this->Price_list->_where = array("productbasicpricemappingid IN (SELECT id FROM ".tbl_productbasicpricemapping." WHERE productid=".$productid." AND productpriceid=".$priceid.")"=>null);
            $priceqtydata = $this->Price_list->getRecordById();

            if(!empty($priceqtydata)){
                $priceqtyids = array_column($priceqtydata, "id");
                $resultId = array_diff($priceqtyids, $UpdatedProductQuantityPrice);

                if(!empty($resultId)){
                    $this->Price_list->_table = tbl_productbasicquantityprice;
                    $this->Price_list->Delete(array("id IN (".implode(",",$resultId).")"=>null));
                }
            }
            
            if(!empty($InsertMultiplePriceData)){
                $this->Price_list->_table = tbl_productbasicquantityprice;
                $this->Price_list->add_batch($InsertMultiplePriceData);
            }
            if(!empty($UpdateMultiplePriceData)){
                $this->Price_list->_table = tbl_productbasicquantityprice;
                $this->Price_list->edit_batch($UpdateMultiplePriceData,'id');
            }
           
            echo 1;
        }
    }
    public function getProductByCategoryId(){

        $PostData = $this->input->post();
        $categoryid = $PostData['categoryid'];
        $type = $PostData['type'];

        $productdata = $this->Price_list->getProductByCategoryId($categoryid,$type);
        echo json_encode($productdata);
    }
    public function getVariantByProductId(){

        $PostData = $this->input->post();
        $productid = $PostData['productid'];
        $type = $PostData['type'];

        $variantdata = $this->Price_list->getVariantByProductId($productid,$type);
        echo json_encode($variantdata);
    }
    public function getchannelpricelistdata() {
        $PostData = $this->input->post();
        $productid = $PostData['productid'];
        $priceid = $PostData['priceid'];
        $producttype = $PostData['producttype'];
        $json = array();

        $channeltype = ($producttype==0?'all':"onlyvendor"); 
        $this->load->model("Channel_model","Channel"); 
        $json['channeldata'] = $this->Channel->getChannelList($channeltype);

        echo json_encode($json);
    }
    
    /* public function getpricelistdata(){
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $producttype = $PostData['producttype'];
        $categoryid = isset($PostData['categoryid'])?$PostData['categoryid']:'';
        
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Channel_model","Channel"); 
        $channeltype = ($producttype==0?'all':"onlyvendor"); 
        $channeldata = $this->Channel->getChannelList($channeltype);

        $req = array();
        $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true,"class"=>"width8");
        $req['COLUMNS'][] = array('title'=>'Product Name',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Category',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Price',"sortable"=>true,"class"=>"text-right");
        
        if(!empty($channeldata)){
            foreach($channeldata as $channel){
                $req['COLUMNS'][] = array('title'=>ucwords($channel['name']).'<input type="hidden" name="channelid[]" id="channelid'.$channel['id'].'" value="'.$channel['id'].'">',"sortable"=>false,"class"=>"text-center");
            }
        }
        $req['COLUMNS'][] = array('title'=>'Actions',"sortable"=>false,"class"=>"width5");

        $pricelistdata = $this->Price_list->getpricelistdata($producttype,$categoryid);
        $counter=0;
        if(!empty($pricelistdata)){

            foreach ($pricelistdata as $index=>$productrow) {
                $channeldataarr = $data = array();
                $actions = $allowproduct = $varianthtml = $productname = '';
                
                $actions .= '<a href="'.ADMIN_URL.'product/view-product/'. $productrow['id'].'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';        

                if($productrow['isuniversal']==0 && $productrow['variantid']!=''){
                    $variantdata = $this->Product_combination->getProductVariantDetails($productrow['id'],$productrow['variantid']);
    
                    if(!empty($variantdata)){
                        $varianthtml .= "<div class='row' style=''>";
                        foreach($variantdata as $variant){
                            $varianthtml .= "<div class='col-md-12 p-n'>";
                            $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                            $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                            $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                            $varianthtml .= "</div>";
                        }
                        $varianthtml .= "</div>";
                    }
                    $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($productrow['productname']).'</a>';
                }else{
                    $productname = ucwords($productrow['productname']);
                }

                $productname = $productname.'<input type="hidden" name="productid[]" id="productid'.$productrow['id'].'" value="'.$productrow['id'].'"><input type="hidden" name="productpriceid['.$productrow['id'].'][]" id="productpriceid'.$productrow['priceid'].'" value="'.$productrow['priceid'].'">';

                if($productrow['productimage']!="" && file_exists(PRODUCT_PATH.$productrow['productimage'])){
                    $productimage = $productrow['productimage'];
                }else{
                    $productimage = PRODUCTDEFAULTIMAGE;
                }
                $productdetail = '<img class="pull-left thumbwidth mr-sm" src="'.PRODUCT.$productimage.'" style="margin-bottom: 5px;"><div class="" style="display: inline-block;">'.$productname.'</div>'; 

                if(!empty($channeldata)){
                    foreach($channeldata as $channel){
    
                        $pricedata = $this->Price_list->getChannelBasicPrice($channel['id'],$productrow['id'],$productrow['priceid']);
                        $allowproduct = "checked";
                        if(!empty($pricedata)){
                            $productbasicpricemappingid = $pricedata['id'];
                            $price = $pricedata['salesprice'];
                            $allowproduct = ($pricedata['allowproduct']==1)?"checked":"";

                            $minimumqty = ($pricedata['minimumqty']>0)?$pricedata['minimumqty']:"";
                            $maximumqty = ($pricedata['maximumqty']>0)?$pricedata['maximumqty']:"";
                            $discountpercent = ($pricedata['discountpercent']>0)?$pricedata['discountpercent']:"";
                            $discountamount = ($pricedata['discountamount']>0)?$pricedata['discountamount']:"";
                        }else{
                            $price = ($productrow['price']>0)?$productrow['price']:'';
                            $productbasicpricemappingid = "";

                            $minimumqty = $maximumqty = $discountpercent = $discountamount = "";
                        }
                        $channeldataarr[] = '
                            <input type="hidden" name="productbasicpricemappingid['.$productrow['id'].'_'.$productrow['priceid'].']['.$channel['id'].']" id="productbasicpricemappingid'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" value="'.$productbasicpricemappingid.'">
                            
                            <div class="form-group m-n p-n" id="price'.$counter.'_div">
                                <input type="text" style="width:100%;" name="price['.$productrow['id'].'_'.$productrow['priceid'].']['.$channel['id'].']" id="price_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="form-control text-right price" value="'.$price.'" onkeypress="return decimal_number_validation(event,this.value,8)" placeholder="Sales price">
                            </div>
                            
                            <div class="form-group m-n p-n">
                                <div class="checkbox">
                                    <input id="allowproduct'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'"  type="checkbox" value="1" name="allowproduct'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="checkradios m-n" '.$allowproduct  .'>
                                    <label style="font-size: 14px;" for="allowproduct'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'"> Allowed</label>
                                </div>
                            </div>
                            <div class="col-md-12 p-n">
                                <div class="form-group m-n p-n" id="minqty'.$counter.'_div">
                                    <div class="col-md-12 p-n text-left">
                                        <label for="minqty_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="control-label">Min. Qty.</label>
                                        <input type="text" style="width:100%;" name="minqty['.$productrow['id'].'_'.$productrow['priceid'].']['.$channel['id'].']" id="minqty_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="form-control text-right" value="'.$minimumqty.'" onkeypress="return isNumber(event)" maxlength="4">
                                    </div>
                                </div>
                                <div class="form-group m-n p-n" id="maxqty'.$counter.'_div">
                                    <div class="col-md-12 p-n text-left">
                                        <label for="maxqty_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="control-label">Max. Qty.</label>  
                                        <input type="text" style="width:100%;" name="maxqty['.$productrow['id'].'_'.$productrow['priceid'].']['.$channel['id'].']" id="maxqty_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="form-control text-right" value="'.$maximumqty.'" onkeypress="return isNumber(event)" maxlength="4">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-n">
                                <div class="form-group m-n p-n" id="discper'.$counter.'_div">
                                    <div class="col-md-12 p-n text-left">
                                        <label for="discper_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="control-label">Disc. (%)</label>  
                                        <input type="text" style="width:100%;" name="discper['.$productrow['id'].'_'.$productrow['priceid'].']['.$channel['id'].']" id="discper_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="form-control text-right discper" value="'.$discountpercent.'" onkeypress="return decimal_number_validation(event,this.value,5)">
                                    </div>
                                </div>
                                <div class="form-group m-n p-n" id="discamnt'.$counter.'_div">
                                    <div class="col-md-12 p-n text-left">
                                        <label for="discamnt_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="control-label">Disc. ('.CURRENCY_CODE.')</label>  
                                        <input type="text" style="width:100%;" name="discamnt['.$productrow['id'].'_'.$productrow['priceid'].']['.$channel['id'].']" id="discamnt_'.$productrow['id'].'_'.$productrow['priceid'].'_'.$channel['id'].'" class="form-control text-right discamnt" value="'.$discountamount.'" onkeypress="return decimal_number_validation(event,this.value,10)">
                                    </div>
                                </div>
                            </div>';
                    }
                }

                $req['DATA'][] = array_merge(
                                        array(++$counter,
                                            $productdetail,
                                            $productrow['categoryname'],
                                            number_format($productrow['price'],2,'.',',')
                                        ),
                                        $channeldataarr,
                                        array($actions)
                                    );
            }
        }
        // print_r($req); exit;
        
		echo json_encode($req);
    } */

    /* public function listing() {
       
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Channel_model","Channel"); 
        $channeltype = ($_REQUEST['producttype']==0?'notdisplayvendorchannel':"onlyvendor"); 
        $channeldata = $this->Channel->getChannelList($channeltype);
        
        $list = $this->Price_list->get_datatables();
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';  
            $allowproduct = '';
            $varianthtml = '';
            $productname = '';
            
             $actions .= '<a href="'.ADMIN_URL.'product/view-product/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';        
            
           
            if($datarow->isuniversal==0 && $datarow->variantid!=''){
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->id,$datarow->variantid);

                if(!empty($variantdata)){
                    $varianthtml .= "<div class='row' style=''>";
                    foreach($variantdata as $variant){
                        $varianthtml .= "<div class='col-md-12 p-n'>";
                        $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                        $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                        $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                        $varianthtml .= "</div>";
                    }
                    $varianthtml .= "</div>";
                }
                $productname = '<a href="javascript:void(0)" class="a-without-link" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($datarow->productname).'</a>';
            }else{
                $productname = ucwords($datarow->productname);
            }
            
            $row[] = ++$counter;
            $row[] = $productname.'<input type="hidden" name="productid[]" id="productid'.$datarow->id.'" value="'.$datarow->id.'">
            <input type="hidden" name="productpriceid['.$datarow->id.'][]"                 id="productpriceid'.$datarow->priceid.'" value="'.$datarow->priceid.'">';
            $row[] = $datarow->categoryname;
            $row[] = "<span class='pull-right'>".number_format($datarow->price,2,'.',',')."</span>";

            if(!empty($channeldata)){
                foreach($channeldata as $channel){

                    $pricedata = $this->Price_list->getChannelBasicPrice($channel['id'],$datarow->id,$datarow->priceid);
                    $allowproduct = "checked";
                    if(!empty($pricedata)){
                        $productbasicpricemappingid = $pricedata['id'];
                        $price = $pricedata['salesprice'];
                        $allowproduct = ($pricedata['allowproduct']==1)?"checked":"";
                    }else{
                        $price = ($datarow->price>0)?$datarow->price:'';
                        $productbasicpricemappingid = "";
                    }
                    $row[] = '
                    <input type="hidden" name="productbasicpricemappingid['.$datarow->id.'_'.$datarow->priceid.']['.$channel['id'].']" id="productbasicpricemappingid'.$datarow->id.'_'.$datarow->priceid.'_'.$channel['id'].'" value="'.$productbasicpricemappingid.'">
                    <div class="form-group m-n p-n" id="price'.$counter.'_div">
                        
                      <input type="text" style="width:100%;" name="price['.$datarow->id.'_'.$datarow->priceid.']['.$channel['id'].']" id="price_'.$datarow->id.'_'.$datarow->priceid.'_'.$channel['id'].'" class="form-control text-right" value="'.$price.'" onkeypress="return decimal_number_validation(event,this.value,8)" placeholder="Sales price">
                      </div>
                    
                      <div class="form-group m-n p-n"><div class="checkbox"><input id="allowproduct'.$datarow->id.'_'.$datarow->priceid.'_'.$channel['id'].'"  type="checkbox" value="1" name="allowproduct'.$datarow->id.'_'.$datarow->priceid.'_'.$channel['id'].'" class="checkradios m-n" '.$allowproduct  .'>
                                      <label style="font-size: 14px;" for="allowproduct'.$datarow->id.'_'.$datarow->priceid.'_'.$channel['id'].'"> Allowed</label></div></div>';
                }
            }
            
            $row[] = $actions;
            $data[] = $row;

        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Price_list->count_all(),
                        "recordsFiltered" => $this->Price_list->count_filtered(),
                        "data" => $data
                    );
        
        echo json_encode($output);
    } */

    public function updateproductbasicprice(){
        $PostData = $this->input->post();
                
        $productidarr = (isset($PostData['productid']))?$PostData['productid']:'';
        $channelidarr = (isset($PostData['channelid']))?$PostData['channelid']:'';
        $productpriceidarr = (isset($PostData['productpriceid']))?$PostData['productpriceid']:'';
        $basicpricearr = (isset($PostData['price']))?$PostData['price']:'';
        $minqtyarr = (isset($PostData['minqty']))?$PostData['minqty']:'';
        $maxqtyarr = (isset($PostData['maxqty']))?$PostData['maxqty']:'';
        $discperarr = (isset($PostData['discper']))?$PostData['discper']:'';
        $discamntarr = (isset($PostData['discamnt']))?$PostData['discamnt']:'';

        $productbasicpricemappingidarr = (isset($PostData['productbasicpricemappingid']))?$PostData['productbasicpricemappingid']:'';
        // print_r($PostData);exit;
        $insertdata=array();
        $updatedata=array();
        
        if(!empty($productpriceidarr)){
            foreach($productpriceidarr as $productid=>$priceids){
                foreach($priceids as $priceid){
                    foreach($channelidarr as $i=>$channelid){

                        $keyprice = $productid."_".$priceid;
                        $productbasicpricemappingid = (!empty($productbasicpricemappingidarr[$keyprice][$channelid]))?$productbasicpricemappingidarr[$keyprice][$channelid]:'';
                        $basicprice = (isset($basicpricearr[$keyprice][$channelid]))?$basicpricearr[$keyprice][$channelid]:'';
                        $productallow = (isset($PostData["allowproduct".$productid."_".$priceid."_".$channelid]))?1:0; 
                        
                        $minimumqty = (isset($minqtyarr[$keyprice][$channelid]))?$minqtyarr[$keyprice][$channelid]:'';
                        $maximumqty = (isset($maxqtyarr[$keyprice][$channelid]))?$maxqtyarr[$keyprice][$channelid]:'';
                        $discountpercent = (isset($discperarr[$keyprice][$channelid]))?$discperarr[$keyprice][$channelid]:'';
                        $discountamount = (isset($discamntarr[$keyprice][$channelid]))?$discamntarr[$keyprice][$channelid]:'';
                        
                        $this->Price_list->_table = tbl_productbasicpricemapping;
                        $this->Price_list->_where = array("productid"=>$productid,"productpriceid"=>$priceid,"channelid"=>$channelid);
                        $Count = $this->Price_list->CountRecords();
                        if($Count==0 && $productbasicpricemappingid==""){
                        
                            $insertdata[]=array('productid'=>$productid,
                                                'productpriceid'=>$priceid,
                                                'channelid'=>$channelid,
                                                'salesprice'=>$basicprice,
                                                "allowproduct" => $productallow,
                                                'minimumqty'=>$minimumqty,
                                                'maximumqty'=>$maximumqty,
                                                'discountpercent'=>$discountpercent,
                                                "discountamount" => $discountamount
                                            );
                        }else{
                        
                            $updatedata[]=array('id'=>$productbasicpricemappingid,
                                                'salesprice' => $basicprice,
                                                "allowproduct" => $productallow,
                                                'minimumqty'=>$minimumqty,
                                                'maximumqty'=>$maximumqty,
                                                'discountpercent'=>$discountpercent,
                                                "discountamount" => $discountamount
                                            );
                        }
                    }
                }
            }    
        }
        if(!empty($updatedata)){
            $this->Price_list->_table = tbl_productbasicpricemapping;
            $this->Price_list->edit_batch($updatedata, "id");
        }
        if(!empty($insertdata)){
            $this->Price_list->_table = tbl_productbasicpricemapping;
            $this->Price_list->add_batch($insertdata);
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(2,'Price List','Edit price list.');
        }
        echo 1;
    }
    public function exportproduct(){
        $PostData = $this->input->get();
        
        $producttype = $PostData['producttype'];
        $categoryid = !empty($PostData['categoryid'])?$PostData['categoryid']:'';
        
        $this->load->model("Channel_model","Channel"); 
        $channeltype = ($producttype==0?'all':"onlyvendor"); 
        $channeldata = $this->Channel->getChannelList($channeltype);
		$data = array();
               
        $this->readdb->select("p.id,pp.id as priceid,CONCAT(p.id,'|',IFNULL(pp.id,0)) as productcode,
							p.name as productname,
                            IFNULL((SELECT GROUP_CONCAT(v.value SEPARATOR ' | ') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=pp.id),'') as variantname,
                            pp.price as price");
		$this->readdb->from(tbl_product." as p");
        $this->readdb->join(tbl_productprices." as pp","pp.productid = p.id","INNER");
        $this->readdb->where("p.producttype = '".$producttype."' AND (FIND_IN_SET(p.categoryid,'".$categoryid."')>0 OR '".$categoryid."'='')"); 
        $this->readdb->order_by("p.id DESC");
        $query = $this->readdb->get();
        $productdata = $query->result_array();
        
        if(!empty($productdata)){
            foreach ($productdata as $index=>$productrow) {

                $price = array();
                if(!empty($channeldata)){
                    foreach($channeldata as $channel){
        
                        $pricedata = $this->Price_list->getChannelBasicPrice($channel['id'],$productrow['id'],$productrow['priceid']);
                        if(!empty($pricedata)){
                            $price[] = number_format($pricedata['salesprice'],2,'.','');
                        }else{
                            $price[] = ($productrow['price']>0)?number_format($productrow['price'],2,'.',''):'';
                        }
                    }
                }

                $data[] = array_merge(array($productrow['productcode'],$productrow['productname'],$productrow['variantname'],number_format($productrow['price'],2,'.','')),$price);
            }
        }
        $headings = array();
        $headings[] = 'Product Code';
        $headings[] = 'Product Name';
        $headings[] = 'Variant';
        $headings[] = 'Price';

        if(!empty($channeldata)){
            foreach($channeldata as $channel){
                $headings[] = ucwords($channel['name']);
            }
        }

        $this->general_model->exporttoexcel($data,"A1:DD1","Product Price",$headings,"Product Price.xls","D:N");

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Price List','Export price list.');
        }
    }
    public function download_excel_file(){
        $PostData = $this->input->get();

        $producttype = $PostData['producttype'];
        $this->load->model("Channel_model","Channel"); 
        $channeltype = ($producttype==0?'all':"onlyvendor"); 
        $channeldata = $this->Channel->getChannelList($channeltype);

        $headings = $data = array();
        $headings[] = 'Product Code';
        $headings[] = 'Product Name';
        $headings[] = 'Variant';
        $headings[] = 'Price';

        if(!empty($channeldata)){
            foreach($channeldata as $channel){
                $headings[] = ucwords($channel['name']);
            }
        }
        $this->general_model->exporttoexcel($data,"A1:DD1","Product Price",$headings,"Product Price.xls","D:N");
    }
    /* public function importproductprice(){
        $PostData = $this->input->post();
        //print_r($PostData);exit;

        $addedby = $this->session->userdata(base_url().'ADMINID');
        $this->load->model('Product_model', 'Product');

        $query = $this->readdb->select("CONCAT(p.id,'|',IFNULL(pp.id,0)) as priceid")
		                    ->from(tbl_product." as p")
                            ->join(tbl_productprices." as pp","pp.productid = p.id","LEFT")
                            ->where("p.producttype IN (0,2)")
                            ->order_by("p.id DESC")
                            ->get();

        $pricedata = $query->result_array();
        $pricedata = array_column($pricedata,'priceid');

        if($_FILES["attachment"]['name'] != ''){

			$FileNM = uploadFile('attachment', 'IMPORT_FILE', IMPORT_PATH);
			            
            if($FileNM !== 0){
                if($FileNM==2){
                    echo 3;//file not uploaded
                    exit;
                }
                
            }else{
                echo 2;//INVALID ATTACHMENT FILE
                exit;
            }

            $updateproductdata = $updateproductpricesdata = array();
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
                    
            if($column0=="Product Code" && $column1=="Product Name" && $column2=="Variant" && $column3=="Price"){
                if($totalrows>1){
                    $error = array();
                    for($i=2;$i<=$totalrows;$i++){

                        $createddate = $this->general_model->getCurrentDateTime();
                        $productpriceid = trim($objWorksheet->getCellByColumnAndRow(0,$i)->getValue());
                        $price = trim($objWorksheet->getCellByColumnAndRow(3,$i)->getValue());

                        $isvalid = 1;
                        if(empty($productpriceid)){
                            echo "Row no. ".$i." product code is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }else if(!in_array($productpriceid,$pricedata)){
                            echo "Row no. ".$i." product code not exist !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }

                        if(empty($price)){
                            echo "Row no. ".$i." product price is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }
                        
                        if($isvalid){
                            $productpriceid = array_filter(explode('|',$productpriceid));
                            
                            if(count($productpriceid)==1 || empty($productpriceid[1])){
                                $this->Product->_table = tbl_product;
                                $this->Product->_fields = 'id';
                                $this->Product->_where = "id=".$productpriceid[0];
                                $Product = $this->Product->getRecordsByID();

                                if(!empty($Product)){
                                    $updateproductdata[] = array('price'=>$price,
                                                                'modifeddate'=>$createddate,
                                                                'modififedby'=>$addedby,
                                                                'id'=>$Product['id']);
                                }
                            }else if(count($productpriceid)==2 && !empty($productpriceid[1])){
                                $this->Product->_table = tbl_productprices;
                                $this->Product->_fields = 'id';
                                $this->Product->_where = "productid=".$productpriceid[0]." AND id=".$productpriceid[1];
                                $Product = $this->Product->getRecordsByID();

                                if(!empty($Product)){
                                    $updateproductdata[] = array('modifeddate'=>$createddate,
                                                                'modififedby'=>$addedby,
                                                                'id'=>$productpriceid[0]);
                                    $updateproductpricesdata[] = array('price'=>$price,
                                                                        'id'=>$Product['id']);
                                }
                            }
                        }
                    }
                    if(empty($error)){
                        if(!empty($updateproductdata)){
                            $this->Product->_table = tbl_product;
                            $this->Product->edit_batch($updateproductdata,'id');
                        }
                        if(!empty($updateproductpricesdata)){
                            $this->Product->_table = tbl_productprices;
                            $this->Product->edit_batch($updateproductpricesdata,'id');
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

        
    } */
    public function importchannelproductprice(){
        $PostData = $this->input->post();
       
        $producttype = $PostData['producttype'];
        $this->load->model("Channel_model","Channel"); 
        $channeltype = ($producttype==0?'all':"onlyvendor"); 
        $channeldata = $this->Channel->getChannelList($channeltype);
        $channelidarray = array_column($channeldata,"id");
        $channelnamearray = array_column($channeldata,"name");
        
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $this->load->model('Product_model', 'Product');

        $query = $this->readdb->select("CONCAT(p.id,'|',IFNULL(pp.id,0)) as priceid")
		                    ->from(tbl_product." as p")
                            ->join(tbl_productprices." as pp","pp.productid = p.id","LEFT")
                            ->where("p.producttype IN (0,2)")
                            ->order_by("p.id DESC")
                            ->get();

        $pricedata = $query->result_array();
        $pricedata = array_column($pricedata,'priceid');
        /* if($PostData['importchannelid']==0){
            $this->load->model("Channel_model","Channel"); 
            $channeldata = $this->Channel->getChannelList('all');
            $channelidarray = array_column($channeldata,"id");
        }else{
            $channelidarray = array("id"=>$PostData['importchannelid']);
        } */

        if($_FILES["importattachment"]['name'] != ''){

			$FileNM = uploadFile('importattachment', 'IMPORT_FILE', IMPORT_PATH);
			            
            if($FileNM !== 0){
                if($FileNM==2){
                    echo 3;//file not uploaded
                    exit;
                }
                
            }else{
                echo 2;//INVALID ATTACHMENT FILE
                exit;
            }

            $insertproductpricesdata = $updateproductpricesdata = array();
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
                    
            if($column0=="Product Code" && $column1=="Product Name" && $column2=="Variant" && $column3=="Price"){
                if($totalrows>1){
                    $error = array();
                   
                    for($i=2;$i<=$totalrows;$i++){
                       
                        $createddate = $this->general_model->getCurrentDateTime();
                        $productpriceid = trim($objWorksheet->getCellByColumnAndRow(0,$i)->getValue());
                        $price = trim($objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
                       
                        $isvalid = 1;
                        if(empty($productpriceid)){
                            echo "Row no. ".$i." product code is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }else if(!in_array($productpriceid,$pricedata)){
                            echo "Row no. ".$i." product code not exist !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }

                        if(empty($price)){
                            echo "Row no. ".$i." product price is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }
                        /* $channelprice = array();
                        $cell = 4;
                        foreach($channelnamearray as $channelname){
                            $salesprice = trim($objWorksheet->getCellByColumnAndRow($cell,$i)->getValue());

                            if(empty($salesprice)){
                                echo "Row no. ".$i." ".$channelname." product price is empty !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }
                            $channelprice[] = $salesprice;
                            $cell++;
                        } */
                        
                        if($isvalid){
                            $productpriceid = array_filter(explode('|',$productpriceid));
                           
                            $countcell = 4;
                            foreach($channelidarray as $channelid){
                                $salesprice = trim($objWorksheet->getCellByColumnAndRow($countcell,$i)->getCalculatedValue());
                                
                                $productid = $productpriceid[0];
                                if(count($productpriceid)==1 && empty($productpriceid[1])){
                                    $this->Product->_table = tbl_productprices;
                                    $this->Product->_fields = "id";
                                    $this->Product->_where = array("productid"=>$productid);
                                    $prices = $this->Product->getRecordsByid();
                                    $priceid = $prices['id'];
                                }else{
                                    $priceid = $productpriceid[1];
                                }
                                $this->Price_list->_table = tbl_productbasicpricemapping;
                                $this->Price_list->_fields = "id";
                                $this->Price_list->_where = array("productid"=>$productid,"productpriceid"=>$priceid,"channelid"=>$channelid);
                                $productpricedata = $this->Price_list->getRecordsByid();
                               
                                if(!empty($productpricedata)){
                                    if(!empty($salesprice)){
                                        $updateproductpricesdata[]=array('id'=>$productpricedata['id'],
                                            'salesprice' => str_replace(",","",$salesprice)
                                        );
                                    }
                                }else{
                                    
                                    $salesprice = !empty($salesprice)?str_replace(",","",$salesprice):str_replace(",","",$price);
                                    
                                    $insertproductpricesdata[]=array('productid'=>$productid,
                                                                'productpriceid'=>$priceid,
                                                                'channelid'=>$channelid,
                                                                'salesprice'=>$salesprice,
                                                                "allowproduct" =>1
                                                            );
                                }
                                $countcell++;
                            }
                        }
                    }
                    
                    if(empty($error)){
                        if(!empty($insertproductpricesdata)){
                            $this->Product->_table = tbl_productbasicpricemapping;
                            $this->Product->add_batch($insertproductpricesdata);
                        }
                        if(!empty($updateproductpricesdata)){
                            $this->Product->_table = tbl_productbasicpricemapping;
                            $this->Product->edit_batch($updateproductpricesdata,'id');
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
}