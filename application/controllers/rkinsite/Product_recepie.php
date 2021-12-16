<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_recepie extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Product_recepie');
        $this->load->model('Product_recepie_model', 'Product_recepie');
    }

    public function index() {
        $this->viewData['title'] = "Product Recepie";
        $this->viewData['module'] = "product_recepie/Product_recepie";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Recepie','View product recepie.');
        }

        $this->admin_headerlib->add_javascript("product_recepie", "pages/product_recepie.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }


    public function listing() { 
        $add = explode(',', $this->viewData['submenuvisibility']['submenuadd']);
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];
        $list = $this->Product_recepie->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = '';
            
            $actions .= '<a class="'.view_class.'" href="'.ADMIN_URL.'product-recepie/view-product-recepie-details/'. $datarow->id.'" title="'.view_title.'">'.view_text.'</a>';
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'product-recepie/product-recepie-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                /* if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'product-recepie/product-recepie-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'product-recepie/product-recepie-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                } */
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'product-recepie/check-product-recepie-use","Product_recepie","'.ADMIN_URL.'product-recepie/delete-mul-product-recepie") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            if(in_array('print', $additionalrights)) {
                $actions .= '<a href="javascript:void(0)" onclick="printproductrecepie('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';    
            }
            if(in_array($rollid, $add)) {
                $actions .= '<a class="'.duplicatebtn_class.'" href="'.ADMIN_URL.'product-recepie/add-product-recepie/'. $datarow->id.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';
            }
            
            $row[] = ++$counter;
            $row[] = ucwords($datarow->productname);
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_recepie->count_all(),
                        "recordsFiltered" => $this->Product_recepie->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    
    public function view_product_recepie_details($recepieid){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Product Recepie Details";
        $this->viewData['module'] = "product_recepie/View_product_recepie";
        if(empty($recepieid)){
            redirect("pagenotfound");
        }
        
        $this->viewData['productrecepiedata'] = $this->Product_recepie->getProductRecepieDataByID($recepieid);
        $this->viewData['regularproductdata'] = $this->Product_recepie->getRegularProductListByProductRecepie();
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['rawproductdata'] = $this->Product->getActiveRegularOrRawProducts(2);
        
        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Recepie','View '.$this->viewData['productrecepiedata']['productname'].' product recepie details.');
        }

        $this->admin_headerlib->add_javascript("view_product_recepie", "pages/view_product_recepie.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_product_recepie($recepieid="") {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Product Recepie";
        $this->viewData['module'] = "product_recepie/Add_product_recepie";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->load->model('Product_model', 'Product');
        // $this->viewData['regularproductdata'] = $this->Product->getProductList();
        // $this->viewData['rawproductdata'] = $this->Product->getRawProductList();
        $this->viewData['regularproductdata'] = $this->Product->getActiveRegularOrRawProducts(3);
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2);
        /* foreach ($this->viewData['regularproductdata'] as $row){

            print_r($row);
            echo "<br>";
        }
        exit; */

        if($recepieid!=""){
            /***** ADD DUPLICATE PRODUCT RECEPIE ******/
            $this->viewData['productrecepiedata'] = $this->Product_recepie->getProductRecepieDataByID($recepieid);
            $this->viewData['recepiecommonmaterialdata'] = $this->Product_recepie->getProductRecepieCommonMaterialDataByRecepieID($recepieid);
            $this->viewData['isduplicate'] = "1";
        }
        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("product_recepie", "pages/add_product_recepie.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function product_recepie_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Product Recepie";
        $this->viewData['module'] = "product_recepie/Add_product_recepie";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['productrecepiedata'] = $this->Product_recepie->getProductRecepieDataByID($id);
        $this->viewData['recepiecommonmaterialdata'] = $this->Product_recepie->getProductRecepieCommonMaterialDataByRecepieID($id);
       
        $this->load->model('Product_model', 'Product');
        // $this->viewData['regularproductdata'] = $this->Product->getProductList();
        // $this->viewData['rawproductdata'] = $this->Product->getRawProductList();
        $this->viewData['regularproductdata'] = $this->Product->getActiveRegularOrRawProducts(3);
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2);

        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("add_product_recepie","pages/add_product_recepie.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function product_recepie_add() {
        
        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $json = array();
        $productid = $PostData['productid'];
        $commonproductidarray = isset($PostData['commonproductid'])?$PostData['commonproductid']:'';
        $commonpriceidarray = isset($PostData['commonpriceid'])?$PostData['commonpriceid']:'';
        $commonunitidarray = isset($PostData['commonunitid'])?$PostData['commonunitid']:'';
        $commonvaluearray = isset($PostData['commonvalue'])?$PostData['commonvalue']:'';
        
        $priceidarr = isset($PostData['priceid'])?$PostData['priceid']:'';
       
        $this->Product_recepie->_where = array('productid' => $productid);
        $Count = $this->Product_recepie->CountRecords();

        if($Count==0){
            
            $InsertData = array('productid' => $productid,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
        
            $RecepieID = $this->Product_recepie->Add($InsertData);
            
            if($RecepieID){
                $InsertCommonMaterial = array();
                if(!empty($commonproductidarray)){
                    for($i=0; $i<count($commonproductidarray); $i++){
                        
                        $productid = $commonproductidarray[$i];
                        $priceid = $commonpriceidarray[$i];
                        $unitid = $commonunitidarray[$i];
                        $value = $commonvaluearray[$i];

                        if($productid != 0 && $priceid != 0 && $unitid != 0 && $value != 0){
                            $InsertCommonMaterial[] = array('productrecepieid' => $RecepieID,
                                                            'productid' => $productid,
                                                            'rawpriceid' => $priceid,
                                                            'unitid' => $unitid,                              
                                                            'value' => $value
                                                        );
                        }
                    }
                }
                
                if(!empty($InsertCommonMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
                    $this->Product_recepie->add_batch($InsertCommonMaterial);
                }
                $InsertVariantWiseMaterial = array();
                if(!empty($priceidarr)){
                    for($j=0; $j<count($priceidarr); $j++){
                        
                        $priceid = $priceidarr[$j];
                        $isrecepievariant = isset($PostData['isrecepievariant'.($j+1)])?1:0;
                        $variantproductidarray = (isset($PostData['variantproductid'][$priceid]) && !empty($PostData['variantproductid'][$priceid])?$PostData['variantproductid'][$priceid]:'');
                        $variantproductpriceidarray = (isset($PostData['variantproductpriceid'][$priceid]) && !empty($PostData['variantproductpriceid'][$priceid])?$PostData['variantproductpriceid'][$priceid]:'');
                        $variantunitidarray = (isset($PostData['variantunitid'][$priceid]) && !empty($PostData['variantunitid'][$priceid])?$PostData['variantunitid'][$priceid]:'');
                        $variantvaluearray = (isset($PostData['variantvalue'][$priceid]) && !empty($PostData['variantvalue'][$priceid])?$PostData['variantvalue'][$priceid]:'');
                        
                        if($isrecepievariant==1){
                            for($k=0; $k<count($variantproductidarray); $k++){

                                $productid = $variantproductidarray[$k];
                                $rawpriceid = $variantproductpriceidarray[$k];
                                $unitid = $variantunitidarray[$k];
                                $value = $variantvaluearray[$k];
        
                                if($productid != 0 && $rawpriceid != 0 && $unitid != 0 && $value != 0){
                                    $InsertVariantWiseMaterial[] = array('productrecepieid' => $RecepieID,
                                                                    'priceid' => $priceid,
                                                                    'productid' => $productid,
                                                                    'rawpriceid' => $rawpriceid,
                                                                    'unitid' => $unitid,                              
                                                                    'value' => $value
                                                                );
                                }
                            }
                        }
                    }
                }
                if(!empty($InsertVariantWiseMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
                    $this->Product_recepie->add_batch($InsertVariantWiseMaterial);
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Product_recepie->_table = tbl_productrecepie;
                    $this->Product_recepie->_fields = ("(SELECT name FROM ".tbl_product." WHERE id=productid) as name");
                    $this->Product_recepie->_where = array("id"=>$RecepieID);
                    $recepiedata = $this->Product_recepie->getRecordsById();
                    $this->general_model->addActionLog(1,'Product Recepie','Add new '.$recepiedata['name'].' product recepie.');
                }
                $json = array('error'=>1); // Product recepie inserted successfully
            } else {
                $json = array('error'=>0); // Product recepie not inserted 
            }
        } else {
            $json = array('error'=>2); // Product already exist 
        }
        echo json_encode($json);
    }
    public function update_product_recepie() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $json = array();
        $productrecepieid = trim($PostData['productrecepieid']);
        $productid = $PostData['postproductid'];
        
        $recepiecommonmaterialidarray = isset($PostData['recepiecommonmaterialid'])?$PostData['recepiecommonmaterialid']:'';
        $commonproductidarray = isset($PostData['commonproductid'])?$PostData['commonproductid']:'';
        $commonpriceidarray = isset($PostData['commonpriceid'])?$PostData['commonpriceid']:'';
        $commonunitidarray = isset($PostData['commonunitid'])?$PostData['commonunitid']:'';
        $commonvaluearray = isset($PostData['commonvalue'])?$PostData['commonvalue']:'';
        $priceidarr = isset($PostData['priceid'])?$PostData['priceid']:'';
        
        $this->Product_recepie->_where = array('id<>'=>$productrecepieid,'productid' => $productid);
        $Count = $this->Product_recepie->CountRecords();

        if($Count==0){
            $updateData = array(/* 'productid' => $productid, */
                                'modifieddate' => $modifieddate,                             
                                'modifiedby' => $modifiedby 
                            );
        
            $this->Product_recepie->_where = array('id' =>$productrecepieid);
            $isUpdated = $this->Product_recepie->Edit($updateData);
            
            if($isUpdated){

                $InsertCommonMaterial = $UpdateCommonMaterial = $DeleteCommonMaterial = array();
                if(!empty($commonproductidarray)){
                    for($i=0; $i<count($commonproductidarray); $i++){
                        
                        $productid = $commonproductidarray[$i];
                        $priceid = $commonpriceidarray[$i];
                        $unitid = $commonunitidarray[$i];
                        $value = $commonvaluearray[$i];
                        $recepiecommonmaterialid = !empty($recepiecommonmaterialidarray[$i])?$recepiecommonmaterialidarray[$i]:0;

                        if(empty($recepiecommonmaterialid)){

                            if($productid != 0 && $priceid != 0 && $unitid != 0 && $value != 0){

                                $InsertCommonMaterial[] = array('productrecepieid' => $productrecepieid,
                                                                'productid' => $productid,
                                                                'rawpriceid' => $priceid,
                                                                'unitid' => $unitid,                              
                                                                'value' => $value
                                                            );
                            }
                        }else{
                            if($productid != 0 && $priceid != 0 && $unitid != 0 && $value != 0){
                                
                                $UpdateCommonMaterial[] = array('id' => $recepiecommonmaterialid,
                                                                'productid' => $productid,
                                                                'rawpriceid' => $priceid,
                                                                'unitid' => $unitid,                              
                                                                'value' => $value
                                                            );
                            }else{
                                $DeleteCommonMaterial[] = $recepiecommonmaterialid;
                            }
                        }
                    }
                }
                
                if(isset($PostData['removerecepiecommonmaterialid']) && $PostData['removerecepiecommonmaterialid']!=''){
                        
                    $query=$this->readdb->select("id")
                                    ->from(tbl_productrecepiecommonmaterial)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removerecepiecommonmaterialid'])))."')>0")
                                    ->get();
                    $CommonMaterialData = $query->result_array();
        
                    if(!empty($CommonMaterialData)){
                        foreach ($CommonMaterialData as $row) {
        
                            $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
                            $this->Product_recepie->Delete(array("id"=>$row['id']));
                        }
                    }
                }
                
                if(!empty($DeleteCommonMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
                    $this->Product_recepie->Delete(array("id IN (".implode($DeleteCommonMaterial).")"=>null));
                }
                if(!empty($InsertCommonMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
                    $this->Product_recepie->add_batch($InsertCommonMaterial);
                }
                if(!empty($UpdateCommonMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
                    $this->Product_recepie->edit_batch($UpdateCommonMaterial,'id');
                }

                if(isset($PostData['removerecepievariantwisematerialid']) && $PostData['removerecepievariantwisematerialid']!=''){
                        
                    $query=$this->readdb->select("id")
                                    ->from(tbl_productrecepievariantwisematerial)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removerecepievariantwisematerialid'])))."')>0")
                                    ->get();
                    $VariantMaterialData = $query->result_array();
        
                    if(!empty($VariantMaterialData)){
                        foreach ($VariantMaterialData as $row) {
        
                            $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
                            $this->Product_recepie->Delete(array("id"=>$row['id']));
                        }
                    }
                } 
                
                $InsertVariantWiseMaterial = $UpdateVariantWiseMaterial = $DeleteVariantWiseMaterial = array();
                if(!empty($priceidarr)){
                    for($j=0; $j<count($priceidarr); $j++){
                        
                        $priceid = $priceidarr[$j];
                        $isrecepievariant = isset($PostData['isrecepievariant'.($j+1)])?1:0;
                        $variantproductidarray = (isset($PostData['variantproductid'][$priceid]) && !empty($PostData['variantproductid'][$priceid])?$PostData['variantproductid'][$priceid]:'');
                        $variantproductpriceidarray = (isset($PostData['variantproductpriceid'][$priceid]) && !empty($PostData['variantproductpriceid'][$priceid])?$PostData['variantproductpriceid'][$priceid]:'');
                        $variantunitidarray = (isset($PostData['variantunitid'][$priceid]) && !empty($PostData['variantunitid'][$priceid])?$PostData['variantunitid'][$priceid]:'');
                        $variantvaluearray = (isset($PostData['variantvalue'][$priceid]) && !empty($PostData['variantvalue'][$priceid])?$PostData['variantvalue'][$priceid]:'');
                        $recepievariantwisematerialidarray = isset($PostData['recepievariantwisematerialid'][$priceid])?$PostData['recepievariantwisematerialid'][$priceid]:'';
                        
                        if($isrecepievariant==1){

                            for($k=0; $k<count($variantproductidarray); $k++){
                                
                                $productid = $variantproductidarray[$k];
                                $rawpriceid = $variantproductpriceidarray[$k];
                                $unitid = $variantunitidarray[$k];
                                $value = $variantvaluearray[$k];
                                $recepievariantwisematerialid = !empty($recepievariantwisematerialidarray[$k])?$recepievariantwisematerialidarray[$k]:0;
                                
                                if(empty($recepievariantwisematerialid)){

                                    if($productid != 0 && $rawpriceid != 0 && $unitid != 0 && $value != 0){
                                        $InsertVariantWiseMaterial[] = array('productrecepieid' => $productrecepieid,
                                                                            'priceid' => $priceid,
                                                                            'productid' => $productid,
                                                                            'rawpriceid' => $rawpriceid,
                                                                            'unitid' => $unitid,                              
                                                                            'value' => $value
                                                                        );
                                    }
                                }else{
                                    if($productid != 0 && $rawpriceid != 0 && $unitid != 0 && $value != 0){
                                        
                                        $UpdateVariantWiseMaterial[] = array('id' => $recepievariantwisematerialid,
                                                                        'priceid' => $priceid,
                                                                        'productid' => $productid,
                                                                        'rawpriceid' => $rawpriceid,
                                                                        'unitid' => $unitid,                              
                                                                        'value' => $value
                                                                    );
                                    }else{
                                        $DeleteVariantWiseMaterial[] = $recepievariantwisematerialid;
                                    }
                                }
                            }
                        }else{
                            $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
                            $this->Product_recepie->Delete(array('productrecepieid' => $productrecepieid,'priceid' => $priceid));
                        }
                    }
                }
                if(!empty($InsertVariantWiseMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
                    $this->Product_recepie->add_batch($InsertVariantWiseMaterial);
                }
                if(!empty($DeleteVariantWiseMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
                    $this->Product_recepie->Delete(array("id IN (".implode($DeleteVariantWiseMaterial).")"=>null));
                }
                if(!empty($UpdateVariantWiseMaterial)){
                    $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
                    $this->Product_recepie->edit_batch($UpdateVariantWiseMaterial,'id');
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Product_recepie->_table = tbl_productrecepie;
                    $this->Product_recepie->_fields = ("(SELECT name FROM ".tbl_product." WHERE id=productid) as name");
                    $this->Product_recepie->_where = array("id"=>$productrecepieid);
                    $recepiedata = $this->Product_recepie->getRecordsById();
                    $this->general_model->addActionLog(2,'Product Recepie','Edit '.$recepiedata['name'].' product recepie.');
                }

                $json = array('error'=>1); // Product recepie update successfully
            } else {
                $json = array('error'=>0); // Product recepie not updated
            }
        } else {
            $json = array('error'=>2); // Product already exist
        }
        echo json_encode($json);
    }
    public function update_product_recepie_material() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $productrecepieid = trim($PostData['productrecepieid']);
        $priceid = (isset($PostData['priceid'])?$PostData['priceid']:"");
        $materialid = trim($PostData['materialid']);
        $type = $PostData['type'];
        $productid = $PostData['editproductid'];
        $rawpriceid = $PostData['editpriceid'];
        $unitid = $PostData['editunitid'];
        $value = $PostData['editvalue'];
        $json = array();
        
        if($type == "common"){
            $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
            $this->Product_recepie->_where = array('id<>'=>$materialid,'productrecepieid'=>$productrecepieid, 'productid' => $productid, 'rawpriceid' => $rawpriceid,'unitid' => $unitid);
        }else{
            $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
            $this->Product_recepie->_where = array('id<>'=>$materialid,'productrecepieid'=>$productrecepieid,'priceid'=>$priceid, 'productid' => $productid, 'rawpriceid' => $rawpriceid,'unitid' => $unitid);
        }
        $Count = $this->Product_recepie->CountRecords();
        if($Count==0){
            if($productid != 0 && $rawpriceid != 0 && $unitid != 0 && $value != 0){
                
                $updateData = array('productid' => $productid,
                                    'rawpriceid' => $rawpriceid,
                                    'unitid' => $unitid,                              
                                    'value' => $value
                                );
               
                // $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
                $this->Product_recepie->_where = array('id' =>$materialid);
                $this->Product_recepie->Edit($updateData);
                
                $json = array('error'=>1); // Material update successfully
            }
        } else {
            $json = array('error'=>2); // Material already exist
        }
        echo json_encode($json);
    }
    public function check_product_recepie_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            /* $this->readdb->select('machineid');
            $this->readdb->from(tbl_machineservicedetails);
            $where = array("machineid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }
   
    public function delete_mul_product_recepie() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            /* $this->readdb->select('machineid');
            $this->readdb->from(tbl_machineservicedetails);
            $where = array("machineid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            } */
            
            if($checkuse == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){

                    $this->Product_recepie->_fields = ("(SELECT name FROM ".tbl_product." WHERE id=productid) as name");
                    $this->Product_recepie->_where = array("id"=>$row);
                    $recepiedata = $this->Product_recepie->getRecordsById();
                
                    $this->general_model->addActionLog(3,'Product Recepie','Delete '.$recepiedata['name'].' product recepie.');
                }
                $this->Product_recepie->_table = tbl_productrecepievariantwisematerial;
                $this->Product_recepie->Delete(array('productrecepieid'=>$row));
                
                $this->Product_recepie->_table = tbl_productrecepiecommonmaterial;
                $this->Product_recepie->Delete(array('productrecepieid'=>$row));

                $this->Product_recepie->_table = tbl_productrecepie;
                $this->Product_recepie->Delete(array('id'=>$row));
            }
        }
    }
    public function common_material_listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Product_recepie->get_datatables_common_material();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a onclick="editMaterial('.$datarow->id.',&apos;common&apos;)" class="'.edit_class.'" href="javascript:void(0)" title="'.edit_title.'">'.edit_text.'</a>';

                $actions .= '<input type="hidden" id="commonproductid'.$datarow->id.'" value="'.$datarow->productid.'">';
                $actions .= '<input type="hidden" id="commonpriceid'.$datarow->id.'" value="'.$datarow->rawpriceid.'">';
                $actions .= '<input type="hidden" id="commonunitid'.$datarow->id.'" value="'.$datarow->unitid.'">';
                $actions .= '<input type="hidden" id="commonvalue'.$datarow->id.'" value="'.$datarow->value.'">';
            }

            $row[] = ++$counter;
            $row[] = ucwords($datarow->namewithvariant);
            $row[] = ucwords($datarow->unitname);
            $row[] = number_format($datarow->value,2,'.','');
            $row[] = $actions;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_recepie->count_all_common_material(),
                        "recordsFiltered" => $this->Product_recepie->count_filtered_common_material(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function getVariantProductCombination(){
        $PostData = $this->input->post();
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Product_prices_model","Product_prices");
        $productrecepieid = $PostData["productrecepieid"];

        $combination=array();
        $json['prices'] = $this->Product_prices->getProductpriceByProductID($PostData["productid"]);
        if(!empty($productrecepieid) && count($json['prices']) > 0){
            foreach($json['prices'] as $i=>$price){
            
                $recepievariantdata = $this->Product_recepie->getProductRecepieVariantMaterialByRecepieIdOrPriceID($productrecepieid,$price['id']);

                $json['prices'][$i]['isrecepievariant'] = !empty($recepievariantdata)?1:0;
                $json['prices'][$i]['recepievariantdata'] = $recepievariantdata;
            }
        }
        $json['isedit'] = (in_array($rollid, $edit))?1:0;

        $combinationdata = $this->Product_combination->getProductcombinationByProductIDWithValue($PostData["productid"]);
        if(!empty($combinationdata)){
            foreach ($combinationdata as $comb) {
                
                $combination[$comb['priceid']][]=array("attribute"=>$comb['variantname'],"variantvalue"=>$comb['variantvalue']);
            }
        }
        $json['combination'] = $combination;        

        echo json_encode($json);
    }
    public function getVariantByProductId(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getVariantByProductIdForAdmin($PostData['productid']);
        echo json_encode($productdata);
    }
    public function printproductrecepie()
    {
        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product Recepie', 'Print product recepie.');
        }
        $PostData = $this->input->post();
        $PostData['printdata'] = $this->Product_recepie->getProductRecepieDetails($PostData['recepieid']);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "product_recepie/Printrecepiedetail", $PostData, true);

        echo json_encode($html);
    }
}?>