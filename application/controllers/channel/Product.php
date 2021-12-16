<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Product extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Product_model', 'Product');
        $this->load->model('Product_file_model', 'Product_file');
        $this->load->model('Member_model', 'Member');
        $this->load->model('Side_navigation_model','Side_navigation');
        $this->viewData = $this->getChannelSettings('submenu','Product');
        // $this->viewData = $this->getAdminSettings('submenu', 'Product');
    }

    public function index() {
        $this->viewData['title'] = "Product";
        $this->viewData['module'] = "product/product";
        $this->viewData['VIEW_STATUS'] = "1";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['productcount'] = $this->Product->getTotalProductCount($MEMBERID);
        
        $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getProductCategoryList($MEMBERID);

        $this->channel_headerlib->add_javascript("Product", "pages/product.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() {   
        $this->load->model("Product_combination_model","Product_combination");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $this->load->model("Stock_report_model","Stock");

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $memberchanneldata = $this->Channel->getMemberChannelData($MEMBERID);
        $memberspecificproduct = (!empty($memberchanneldata['memberspecificproduct']))?$memberchanneldata['memberspecificproduct']:0;
        $productcount = $this->Product->getTotalProductCount($MEMBERID);
        $totalproductcount = (!empty($productcount['totalproduct']))?$productcount['totalproduct']:0;

        $this->load->model("Memberproduct_model","Memberproduct");
        $list = $this->Memberproduct->get_datatables();
        /* if(channel_memberspecificproduct==1 && $productcount['totalproduct']>0){
        }else{
            $list = $this->Product->get_datatables();
        } */
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $allowproduct = '';
            $varianthtml = '';
            $productname = '';
            $channelname = '';
            
            $actions .= '<a href="'.CHANNEL_URL.'product/view-product/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';        
            
            /*if($datarow->isuniversal==0 && $datarow->variantid!=''){
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
                $productname = '<a href="javascript:void(0)" class="a-without-link" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($datarow->name).'</a>';
            }else{
            }*/
            $productname = ucwords($datarow->name);

            $row[] = ++$counter;
            $row[] = $productname;
            $row[] = $datarow->categoryname;
            $row[] = $datarow->brandname;
            if(channel_memberspecificproduct==1 && $totalproductcount>0){

                if($datarow->sellerchannelid!=0){
                    $channellabel="";
                    $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                    if(!empty($channeldata) && isset($channeldata[$key])){
                        $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                    }
                    $row[] = $channellabel." ".ucwords($datarow->sellermembername).' ('.$datarow->sellermembercode.')';
                }else{
                    $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
                }
    
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channelname = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '.$channeldata[$key]['name'];
                }
                $row[] = $channelname;

                if($datarow->productallow==1){
                    $checked = 'checked';
                }else{
                $checked = '';
                }
                $allowproduct .= '<div class="text-center"><div class="checkbox"><input id="allowcheck'.($counter+1).'"  type="checkbox" value="1" name="allowcheck'.($counter+1).'" class="checkradios m-n" '.$checked.'>
                                        <label for="allowcheck'.($counter+1).'"></label></div></div>';
            }

            if(number_format($datarow->minprice,2,'.','') == number_format($datarow->maxprice,2,'.','')){
                $price = numberFormat($datarow->minprice, 2, ',');
            }else{
                $price = numberFormat($datarow->minprice, 2, ',')." - ".numberFormat($datarow->maxprice, 2, ',');
            }
            $row[] = "<span class='pull-right'>".$price."</span>";

            if(channel_memberspecificproduct==1 && $totalproductcount>0){
                //$row[] = "<span class='pull-right'>".number_format($datarow->universalprice,2,'.',',')."</span>";
                if(channel_memberbasicsalesprice==1 && in_array($rollid, $edit)==true && $totalproductcount>0){
                    /* $row[] = '<input type="hidden" name="productid[]" id="productid'.$datarow->id.'" value="'.$datarow->id.'">
                            <input type="hidden" name="productpriceid[]" id="productpriceid'.$datarow->priceid.'" value="'.$datarow->priceid.'">
                                <div class="width15 form-group" id="price'.$counter.'_div">
                                    <input type="text" name="price[]" id="price'.$counter.'" class="form-control text-right" value="'.$datarow->salesprice.'" onkeypress="return isNumber(event)">
                                </div>'; */

                    if(number_format($datarow->minsalesprice,2,'.','') == number_format($datarow->maxsalesprice,2,'.','')){
                        $salesprice = numberFormat($datarow->minsalesprice, 2, ',');
                    }else{
                        $salesprice = numberFormat($datarow->minsalesprice, 2, ',')." - ".numberFormat($datarow->maxsalesprice, 2, ',');
                    }
                    $row[] = '<input type="hidden" name="productid[]" id="productid'.$datarow->id.'" value="'.$datarow->id.'">
                                <input type="hidden" name="productpriceid[]" id="productpriceid'.$datarow->priceid.'" value="'.$datarow->priceid.'">
                            <span class="pull-right">'.$salesprice.'</span>';

                    $actions .= '<a href="javascript:void(0)" onclick="openpopup('.$datarow->id.','.$datarow->priceid.')" class="'.edit_class.'" title="'.edit_title.'">'.edit_text.'</a>';
                
                }
            }

            $ProductStock = $this->Stock->getVariantStock($MEMBERID,$datarow->id,'','',$datarow->priceid,1,$CHANNELID);
            $row[] = "<span class='pull-right'>".((!empty($ProductStock[0]['overallclosingstock']))?$ProductStock[0]['overallclosingstock']:0)."</span>";
    

            if (channel_memberspecificproduct==1 && $totalproductcount>0) {
                $row[] = $allowproduct;
            }
            
            $row[] = $actions;
            $data[] = $row;

        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Memberproduct->count_filtered(),
            "recordsFiltered" => $this->Memberproduct->count_all(),
            "data" => $data,
            );
        /* if(channel_memberspecificproduct==1 && $productcount['totalproduct']>0){
            
        }else{
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->Product->count_all(),
                "recordsFiltered" => $this->Product->count_filtered(),
                "data" => $data,
                );
        } */
        echo json_encode($output);
    }

    public function add_product() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
         
        $this->viewData['title'] = "Add Product";
        $this->viewData['module'] = "product/add_product";   
        $this->viewData['VIEW_STATUS'] = "0";            
        //$this->viewData['maincategorydata'] = $this->Product->getmaincategory();

        $this->load->model("Product_section_model","Product_section");
        $this->Product_section->_fields = "id,name";
        $this->Product_section->_order = "priority ASC";
        $this->viewData['productsection'] = $this->Product_section->getRecordByID();

        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->channel_headerlib->add_bottom_javascripts("product", "pages/add_product.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function product_add() {
      
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();  
        
        $productname = isset($PostData['productname']) ? trim($PostData['productname']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';
        $tax = isset($PostData['tax']) ? trim($PostData['tax']) : '';
        $stock = isset($PostData['stock']) ? trim($PostData['stock']) : '';
        $hsncodeid = isset($PostData['hsncodeid']) ? trim($PostData['hsncodeid']) : '';
        $discount = isset($PostData['discount']) ? trim($PostData['discount']) : '';
        $metatitle = isset($PostData['metatitle']) ? trim($PostData['metatitle']) : '';  
        $metadescription=  isset($PostData['metadescription']) ? trim($PostData['metadescription']) : '';
        $metakeyword=  isset($PostData['metakeyword']) ? trim($PostData['metakeyword']) : ''; 
        $priority=  isset($PostData['priority']) ? trim($PostData['priority']) : '';           
        $status = $PostData['status'];
        $commingsoon = $PostData['commingsoon'];
        $categoryid = isset($PostData['categoryid']) ? trim($PostData['categoryid']) : '';     
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $modifieddate = $this->general_model->getCurrentDateTime();
            
        $prices =  isset($PostData['prices'])? $PostData['prices'] : '';

        if(isset($PostData['checkuniversal'])){
            $isuniversal = 1;
            $price =  isset($PostData['price'])? $PostData['price'] : '';
        }else{
            $isuniversal = 0;
            $price = 0;
            $stock = 0;
        }
        $commingsoon = isset($PostData['commingsoon'])? $PostData['commingsoon'] : '0';
      
           $notification ='1';   
           if(!is_dir(PRODUCT_PATH)){
                @mkdir(PRODUCT_PATH);
            }
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                if($_FILES['productfile'.$id]['name']!=''){
                    $file = uploadFile('productfile'.$id, 'PRODUCT');
                    if($file === 0){
                        echo 3; //INVALID image FILE TYPE
                        exit;
                    }
                }
            }
            $this->Product->_where = array('name' => $productname);
            $sqlname = $this->Product->getRecordsByID();
            // print_r($sqlname);exit;
        if(empty($sqlname))
        {
            $InsertData = array('categoryid' => $categoryid,'name' => $productname,'description' => $description,'isuniversal'=>$isuniversal,'price'=>$price,'tax'=>$tax,'hsncodeid'=>$hsncodeid,'metatitle' =>$metatitle,'metadescription' => $metadescription,'metakeyword' =>$metakeyword, 'priority'=>$priority, 'discount'=>$discount,'status' => $status,'commingsoon' => $commingsoon,'createddate' => $createddate, 'modifeddate' => $modifieddate,'addedby' => $addedby,'modififedby' => $modifiedby,'universalstock'=>$stock);
            $insertid = $this->Product->add($InsertData);
                          
            if($insertid!=0){

                $productsection_arr=array();
                if(isset($PostData['productsection'])){
                    foreach($PostData['productsection'] as $ps){
                        $productsection_arr[] = array("productsectionid"=>$ps,'productid'=>$insertid);
                    }
                }
                if(count($productsection_arr)>0){
                    $this->load->model("Product_section_model","Product_section");
                    $this->Product_section->_table = tbl_productsectionmapping;
                    $this->Product_section->add_batch($productsection_arr);
                }

                $insetprice_arr=array();
                 if($prices!="" && $isuniversal==0){
                    $price_arr = explode(",",$prices);
                    foreach ($price_arr as $pa) {
                        $insetprice_arr[]=array("productid"=>$insertid,"price"=>$pa);
                    }
                 }
                 if(count($insetprice_arr)>0){
                    $this->Product_section->_table = tbl_productprices;
                    $this->Product_section->add_batch($insetprice_arr);
                 }
                  $Imageextensions = array("bmp","bm","gif","ico","jfif","jfif-tbnl","jpe","jpeg","jpg","pbm","png","svf","tif","tiff","wbmp","x-png");

                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);

                     if($_FILES['productfile'.$id]['name']!='' && strpos($key, 'productfile') !== false){

                        $temp = explode('.', $_FILES['productfile'.$id]['name']);
                        $extension = end($temp);                        
                        $type = 0;
                        $image_width = $image_height = '';
                        if (in_array($extension, $Imageextensions, true)) {
                            $type = 1;
                            $image_width = PRODUCT_IMG_WIDTH;
                            $image_height = PRODUCT_IMG_HEIGHT;
                        }
                        $file = uploadFile('productfile'.$id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, $image_width, $image_height);
                        if($file !== 0){
                            if($file==2){
                                echo 2;//file not uploaded
                                exit;
                            }
                            $insertdata = array("productid" => $insertid,
                                                "type" => $type,
                                                "filename" => $file
                                                );                                         
                            $this->Product->_table = tbl_productimage;
                            $this->Product->add($insertdata);
                        }else {
                            echo 3; //INVALID image TYPE
                            exit;
                        }            
                    }else{
                         $file = '';
                     }
                }
                
                /*notification*/ 
                $insertData = array();     
                if($notification == 1){
                        $this->load->model('Fcm_model','Fcm');
                        $fcmquery = $this->Fcm->getFcmData();
                        
                        if(!empty($fcmquery)){
                          foreach ($fcmquery as $fcmrow){ 
                            $fcmarray=array();                             
                            $type = "3";// catalog =1 , news =2 , product =3
                            $msg = ucwords($productname)." has Product Add.";                          
                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                            //$pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$insertid.'"}';
                            $fcmarray[] = $fcmrow['fcm'];
                            $memberid = $fcmrow['memberid']; 

                            //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                            $this->Fcm->sendFcmNotification($type,$pushMessage,'0',$fcmarray,0,$fcmrow['devicetype']);

                            $insertData[] = array(
                                'type'=>$type,
                                'message' => $pushMessage,
                                'memberid'=>$memberid,    
                                'isread'=>0,                   
                                'createddate' => $createddate,               
                                'addedby'=>$addedby
                                );
                           }                    
                           if(!empty($insertData)){
                                $this->load->model('Notification_model','Notification');
                                $this->Notification->_table = tbl_notification;
                                $this->Notification->add_batch($insertData);
                                //echo 1;//send notification
                            }
                        }      
                } 
                /*notification end*/    
                echo 1;  
            }else{
                echo 0;// page content not added
            } 
        }else{
                echo 4;//page content already exists 
        }  
    }

    public function update_product() {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
       
        // print_r($PostData);exit();
        $productid = isset($PostData['productid']) ? trim($PostData['productid']) : '';
        $productname = isset($PostData['productname']) ? trim($PostData['productname']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';
        $tax = isset($PostData['tax']) ? trim($PostData['tax']) : '';
        $stock = isset($PostData['stock']) ? trim($PostData['stock']) : '';
        $hsncodeid = isset($PostData['hsncodeid']) ? trim($PostData['hsncodeid']) : '';
        $metatitle = isset($PostData['metatitle']) ? trim($PostData['metatitle']) : '';  
        $metadescription=  isset($PostData['metadescription']) ? trim($PostData['metadescription']) : '';
        $metakeyword=  isset($PostData['metakeyword']) ? trim($PostData['metakeyword']) : ''; 
        $priority=  isset($PostData['priority']) ? trim($PostData['priority']) : '';
        $discount = isset($PostData['discount']) ? trim($PostData['discount']) : '';
        $status = $PostData['status'];
        $commingsoon = $PostData['commingsoon'];
        $categoryid = isset($PostData['categoryid']) ? trim($PostData['categoryid']) : '';     
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $modifieddate = $this->general_model->getCurrentDateTime();
            
        $prices =  isset($PostData['prices'])? $PostData['prices'] : '';

        if(isset($PostData['checkuniversal'])){
            $isuniversal = 1;
            $price =  isset($PostData['price'])? $PostData['price'] : '';
        }else{
            $isuniversal = 0;
            $price = 0;
            $stock = 0;
        }
        $commingsoon = isset($PostData['commingsoon'])? $PostData['commingsoon'] : '0';

        $this->Product->_where = "name = '".$productname."' and id <>".$productid;//AND id <> ".$id." AND maincategoryid = ".$maincategoryid;
        $sqlname = $this->Product->getRecordsByID();

        if(!is_dir(PRODUCT_PATH)){
            @mkdir(PRODUCT_PATH);
        }
        foreach ($_FILES as $key => $value) {
            
            $id = preg_replace('/[^0-9]/', '', $key);
            if(!isset($PostData['productfileid'.$id]) && $_FILES['productfile'.$id]['name'] != ''){
                $file = uploadFile('productfile'.$id, 'PRODUCT');
                if($file === 0){
                    echo 3;//INVALID PRODUCT FILE TYPE
                    exit;
                }
            }
        }

        if(empty($sqlname)){
            $updateData = array(
                                'categoryid' => $categoryid,
                                'name' => $productname,
                                'universalstock'=>$stock,
                                'description' => $description,
                                'isuniversal'=>$isuniversal,
                                'price'=>$price,
                                'tax'=>$tax,
                                'hsncodeid'=>$hsncodeid,
                                'discount'=>$discount,
                                'metatitle' =>$metatitle,
                                'metakeyword' =>$metakeyword,     
                                'metadescription' => $metadescription,
                                'modifeddate' => $modifieddate,    
                                'modififedby' => $modifiedby, 
                                'priority'=>$priority,
                                'status' => $status,
                                'commingsoon' => $commingsoon);

            $this->Product->_where = array('id' => $productid);
            $updateid = $this->Product->Edit($updateData);
            
            if($updateid != 0){

            $oldproductsection=array();
            if(isset($PostData['oldproductsection']) && $PostData['oldproductsection']!=""){
                $oldproductsection = explode(",",$PostData['oldproductsection']);
            }
            $delete_arr=array();
            $add_arr=array();
            if(isset($PostData['productsection'])){
                $delete_arr = array_diff($oldproductsection,$PostData['productsection']);
                $add_arr = array_diff($PostData['productsection'],$oldproductsection);
            }else{
                $this->load->model("Product_section_model","Product_section");
                $this->Product_section->_table = tbl_productsectionmapping;
                $this->Product_section->Delete(array("productid"=>$productid));
            }

            if(count($add_arr)>0){
                $productsection_arr=array();
                foreach($add_arr as $aa){
                    $productsection_arr[]=array('productid'=>$productid,'productsectionid'=>$aa);
                }
                if(count($productsection_arr)>0){
                    $this->load->model("Product_section_model","Product_section");
                    $this->Product_section->_table = tbl_productsectionmapping;
                    $this->Product_section->add_batch($productsection_arr);
                }
            }

            if(count($delete_arr)>0){
                $this->load->model("Product_section_model","Product_section");
                $this->Product_section->_table = tbl_productsectionmapping;
                $this->Product_section->Delete(array("productsectionid in(".implode(",",$delete_arr).")"=>null,"productid"=>$productid));
            }

            $productfileid_arr=array();
            // print_r($_FILES);exit();
            foreach ($_FILES as $key => $value) {

                $id = preg_replace('/[^0-9]/', '', $key);
                $type = $PostData['filetype'.$id];
                $image_width = $image_height = '';
                if($type==1){
                    $image_width = PRODUCT_IMG_WIDTH;
                    $image_height = PRODUCT_IMG_HEIGHT;
                }
                if(!isset($PostData['productfileid'.$id])){

                    if($_FILES['productfile'.$id]['name']!=''){
                        $file = uploadFile('productfile'.$id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, $image_width, $image_height);
                        if($file !== 0){
                            if($file==2){
                                echo 2;//file not uploaded
                                exit;
                            }
                        }
                        
                    }    

                    $insertdata = array("productid" => $productid,
                                    "type" => $type,
                                    "filename" => $file,
                                );
                    $insertdata = array_map('trim', $insertdata);
                    //$ProductfileID = $this->Productfile->Add($insertdata);
                    /*if($PostData['filepriority'.$id]!=''){
                        $insertdata['priority'] = $PostData['filepriority'.$id];
                    }else{
                        $this->db->set('priority',"(SELECT IFNULL(MAX(pf.priority),0)+1 as priority FROM ".tbl_productfile." as pf WHERE pf.productid=".$ProductID.")",FALSE);
                    }*/
                    
                    $ProductfileID = $this->Product_file->Add($insertdata);
                    $productfileid_arr[] = $ProductfileID;

                }else if($_FILES['productfile'.$id]['name'] != '' && isset($PostData['productfileid'.$id])){

                    $this->Product_file->_where = "id=".$PostData['productfileid'.$id];
                    $FileData = $this->Product_file->getRecordsByID();

                    if(!empty($FileData)){
                        if($FileData['type']==1){
                            unlinkfile("PRODUCT",$FileData['filename'], PRODUCT_PATH);
                        }
                        
                    }
                        
                    $file = uploadFile('productfile'.$id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, $image_width, $image_height);
                    if($file !== 0){
                        if($file==2){
                            echo 2;//file not uploaded
                            exit;
                        }
                    }

                    $updatedata = array("type" => $type,"filename" => $file/*,"priority"=>$PostData['filepriority'.$id]*/);
                    $updatedata = array_map('trim', $updatedata);

                    $this->Product_file->_where = "id=".$PostData['productfileid'.$id];
                    $this->Product_file->Edit($updatedata);
                    $productfileid_arr[]=$PostData['productfileid'.$id];

                }else if($_FILES['productfile'.$id]['name'] == '' && isset($PostData['productfileid'.$id])){
                
                    /*$updatedata = array("priority"=>$PostData['filepriority'.$id]);
                    $updatedata = array_map('trim', $updatedata);

                    $this->Productfile->_where = "id=".$PostData['productfileid'.$id];
                    $this->Productfile->Edit($updatedata); 
                    */      
                        $productfileid_arr[]=$PostData['productfileid'.$id];
                    
                }else{
                    /*$updatedata = array("priority"=>$PostData['filepriority'.$id]);
                    $updatedata = array_map('trim', $updatedata);

                    $this->Productfile->_where = "id=".$PostData['productfileid'.$id];
                    $this->Productfile->Edit($updatedata);
                    */
                    $productfileid_arr[]=$PostData['productfileid'.$id];
                }
                }
                if(isset($productfileid_arr) && count($productfileid_arr)>0){
                    $this->Product->_table = tbl_productimage;
                    $this->Product->Delete(array("id not in(".implode(",",$productfileid_arr).")"=>null,"productid"=>$productid));
                }
                echo 1; 
            } else {
                echo 0; 
            }
        } else {
            echo 4; 
        }
    }

    public function product_edit($id) {
       $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Product";
        $this->viewData['module'] = "product/add_product";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";  
        $this->Product->_where=array("id"=>$id);     
        $this->viewData['productdata'] =  $this->Product->getRecordsByID(); 
        $this->viewData['productid'] =  $id; 
        $this->viewData['maincategorydata'] = $this->Product->getmaincategory();
        $this->viewData['productfile'] = $this->Product_file->getProductfilesByProductID($id);

        $this->load->model("Product_section_model","Product_section");
        $this->Product_section->_fields = "id,name";
        $this->viewData['productsection'] = $this->Product_section->getRecordByID();

        $this->Product_section->_table = tbl_productsectionmapping;
        $this->Product_section->_fields = "productsectionid";
        $this->Product_section->_where = array("productid"=>$id);
        $productsectionmapping = $this->Product_section->getRecordByID();
        // print_r($productsectionmapping);exit;
        $this->viewData['productsectionarr'] = array();
        foreach($productsectionmapping as $pm){
            $this->viewData['productsectionarr'][]=$pm['productsectionid'];
        }
        // print_r($this->viewData['productsectionarr']);exit;

         $this->load->model("Product_prices_model","Product_prices");
         $this->viewData['productprices'] = array();
         $productprices = $this->Product_prices->getProductpriceByProductID($id);
         foreach ($productprices as $v) {
             $this->viewData['productprices'][]=$v['price'];
         }
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->channel_headerlib->add_bottom_javascripts("product", "pages/add_product.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function product_variant($id)
    {

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');

       $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Product Variant";
        $this->viewData['module'] = "product/product_variant";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";  
        $this->Product->_where=array("id"=>$id);     
        $this->viewData['productid'] =  $id; 
        $this->viewData['productdata'] =  $this->Product->getRecordsByID(); 

        $this->load->model("Product_prices_model","Product_prices");
        $this->load->model("Product_combination_model","Product_combination");
        $this->viewData['productprices'] = $this->Product_prices->getProductpriceByProductID($id);
        $this->viewData['productcombination']=array();
        $productcombination = $this->Product_combination->getProductcombinationByProductID($id);
        foreach ($productcombination as $pc) {
            $this->viewData['productcombination'][$pc['priceid']][]=array("id"=>$pc['id'],"priceid"=>$pc['priceid'],"variantid"=>$pc['variantid'],"attributeid"=>$pc['attributeid']);
        }
        // echo "<pre>";print_r($this->viewData['productcombination']);exit();
         $this->load->model("Attribute_model","Attribute");
         $this->viewData['attributedata'] = $this->Attribute->getActiveAttribute($MEMBERID,$CHANNELID);
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_bottom_javascripts("product", "pages/product_variant.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function delete_mul_product() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $this->Product->_table = tbl_productimage;
            $this->readdb->select('id,filename');
            $this->readdb->from($this->Product->_table);
            $this->readdb->where('productid', $row);
            $query1 = $this->readdb->get();
            $productimagedata = $query1->result_array();
            $this->Product->_table = tbl_productprices;
            $this->readdb->select('id');
            $this->readdb->from($this->Product->_table);
            $this->readdb->where('productid', $row);
            $query2 = $this->readdb->get();
            $productpricesdata = $query2->result_array();
            if(count($productimagedata)>0){
                foreach ($productimagedata as $pi) {
                    unlinkfile('PRODUCT_PATH', $pi['filename'], PRODUCT_PATH);

                    $this->Product->_table = tbl_productimage;
                    $this->Product->Delete(array("id"=>$pi['id']));
                }
            }
            if(count($productpricesdata)>0){
                foreach ($productpricesdata as $pd) {
                    $this->Product->_table = tbl_productcombination;
                    $this->Product->Delete(array("priceid"=>$pd['id']));

                    $this->Product->_table = tbl_productprices;
                    $this->Product->Delete(array("id"=>$pd['id']));
                }
            }
            $this->Product->_table = tbl_productsectionmapping;
            $this->Product->Delete(array("productid"=>$row));

            $this->Product->_table = tbl_product;
            $this->Product->Delete(array("id"=>$row));
        }
    }

    public function check_product_use() {
          $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            $this->readdb->select('productid');
            $this->readdb->from(tbl_orderproducts);
            $where = array("productid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }

            $this->readdb->select('productid');
            $this->readdb->from(tbl_cart);
            $where = array("productid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }

            $this->readdb->select('productid');
            $this->readdb->from(tbl_quotationproducts);
            $where = array("productid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
          }
        echo $count;
    }

    public function product_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Product->_table = tbl_product;
        $this->Product->_where = array("id" => $PostData['id']);
        $this->Product->Edit($updatedata);

        echo $PostData['id'];
    }
    
    function getcontentbyid(){
        $PostData = $this->input->post();

        $this->Product->_where=array("id"=>$PostData['id']);     
        $data =  $this->Product->getRecordsByID();

        echo json_encode(array('id'=>$data['id'],'productname'=> $data['name'],'productdescription'=> $data['description'],'productimage'=> CATALOG_IMAGE.$data['image'],'productpdffile'=> CATALOG_IMAGE.$data['pdffile'],'productcreateddate'=> $this->general_model->displaydatetime($data['createddate']),'productstatus'=> $data['status']));
    }

    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }
    public function set_barcode($code){
		$this->load->model('Common_model','Common_model');
		$this->Common_model->set_barcode($code);
    }
    
    public function view_product($id)
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Product";
        $this->viewData['module'] = "product/view_product";
        $this->viewData['action'] ='1';   
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->viewData['productdata'] =  $this->Product->getProductByMemeberIDOrProductID($id,$MEMBERID);
        
        if(count($this->viewData['productdata'])>0){
            $this->viewData['productid'] =  $id; 
            $this->viewData['maincategorydata'] = $this->Product->getmaincategory();

            $this->viewData['productfile'] = $this->Product_file->getProductfilesByProductID($id);
            $this->load->model("Product_prices_model","Product_prices");
            $this->viewData['productprices'] = array();
            $productprices = $this->Product_prices->getMemberProductpriceByProductID($MEMBERID,$id);
            $this->viewData['productpricesdata'] = $this->Product_prices->getMemberProductPricesByProductOrPriceId($MEMBERID,$id);

            $this->viewData['productprices'] = array_column($productprices,'price');
            $this->viewData['productstock'] = array_column($productprices,'stock');
            
            $this->load->model("Product_combination_model","Product_combination");
            $this->viewData['productcombination']=array();
            
            $this->load->model('Stock_report_model', 'Stock');
            $productdata = $this->Stock->getProductStockList($MEMBERID,0,'',$id);
            $this->viewData['productdata']['universalstock'] = (!empty($productdata[0]['overallclosingstock']))?$productdata[0]['overallclosingstock']:0;
            
            $productcombination = $this->Product_combination->getMemberProductcombinationByProductID($id,$MEMBERID);
            $ProductStock = $this->Stock->getVariantStock($MEMBERID,$id,'','',0,1,$CHANNELID);
            //print_r($ProductStock);exit;
            foreach ($productcombination as $pc) {
                if(!empty($ProductStock)){
                    $key = array_search($pc['priceid'], array_column($ProductStock, 'combinationid'));
                    $stock = $ProductStock[$key]['overallclosingstock'];
                }else{
                    $stock = 0;
                }
                $productpricesdata = $this->Product_prices->getMemberProductPricesByProductOrPriceId($MEMBERID,$id,$pc['priceid']);

                if(number_format($productpricesdata['minprice'],2,'.','') == number_format($productpricesdata['maxprice'],2,'.','')){
                    $price = numberFormat($productpricesdata['minprice'], 2, ',');
                }else{
                    $price = numberFormat($productpricesdata['minprice'], 2, ',')." - ".numberFormat($productpricesdata['maxprice'], 2, ',');
                }

                $this->viewData['productcombination'][$pc['priceid']]['pricetype']=$pc['pricetype'];
                $this->viewData['productcombination'][$pc['priceid']]['price'] = $price;
                $this->viewData['productcombination'][$pc['priceid']]['stock'] = $stock;
                $this->viewData['productcombination'][$pc['priceid']]['pointsforseller']=(int)$pc['pointsforseller'];
                $this->viewData['productcombination'][$pc['priceid']]['pointsforbuyer']=(int)$pc['pointsforbuyer'];
                $this->viewData['productcombination'][$pc['priceid']]['sku']=$pc['sku'];
                $this->viewData['productcombination'][$pc['priceid']]['barcode']=$pc['barcode'];
                
                $this->viewData['productcombination'][$pc['priceid']]['variants'][] = array("variantvalue"=>$pc['variantname'],"variantname"=>$pc['attributename']);

                $this->viewData['productcombination'][$pc['priceid']]['multipleprice']=$this->Product_prices->getMemberProductQuantityPriceDataByPriceID($MEMBERID,$pc['priceid']);
            }

            $this->load->model("Related_product_model","Related_product");
            $relatedproducts = $this->Related_product->getRelatedProducts($id);
            $this->viewData['relatedproducts'] = !empty($relatedproducts)?implode(", ",array_column($relatedproducts, 'productname')):"";
            
            $this->load->model("Product_section_model","Product_section");
            $productsections = $this->Product_section->getProductSectionsByProductId($id);
            $this->viewData['productsections'] = !empty($productsections)?implode(", ",array_column($productsections, 'sectionname')):"";
            
            // echo "<pre>";print_r($this->viewData['productcombination']);exit();
            $this->channel_headerlib->add_javascript_plugins("html5gallery","html5gallery/html5gallery.js");
            $this->load->view(CHANNELFOLDER.'template',$this->viewData);
        }else{
            redirect(CHANNEL_URL);
        }
    }

    public function qr_code()
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "QR Code";
        $this->viewData['module'] = "product/View_product_details";
        $this->viewData['action'] ='1';   

        /* $this->load->model("Memberproduct_model","Memberproduct");
        $this->viewData['productdata'] = $this->Memberproduct->getAllProductsDetail(); */
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Product_model","Product"); 
        $this->viewData['productlist'] = $this->Product->getProductByCategoryId($MEMBERID,0,1);
       
        $this->channel_headerlib->add_bottom_javascripts("view_product", "pages/view_product.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function exportToPDFQRCode(){

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        
        $productids = is_array($_REQUEST['productid'])?implode(",", $_REQUEST['productid']):$_REQUEST['productid'];
        $PostData['productname'] = $_REQUEST['productname'];
        $PostData['sku'] = $_REQUEST['sku'];
        $PostData['productprice'] = $_REQUEST['productprice'];
        $PostData['variant'] = $_REQUEST['variant'];

        $this->load->model("Memberproduct_model","Memberproduct");
        $PostData['productdata'] = $this->Memberproduct->getAllProductsDetail($productids);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'product/Qrcodeformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Product-QR-Code.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   40, // margin top
                   15, // margin bottom
                    3, // margin header
                    10); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
       
        ob_start();
        ob_end_clean();
        
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "I");
    }
    public function printProductDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $this->load->model("Memberproduct_model","Memberproduct");
        $PostData['productdata'] = $this->Memberproduct->getAllProductsDetail();
        $html['content'] = $this->load->view(ADMINFOLDER."product/Printproductdetails.php",$PostData,true);
        
        echo json_encode($html); 
    }
    public function getProductsByIDs(){
        $PostData = $this->input->post();

        $this->load->model("Memberproduct_model","Memberproduct");
        $ProductData = $this->Memberproduct->getAllProductsDetail($PostData['productid']);
        echo json_encode($ProductData);
    }

    public function add_product_variant()
    {
        $PostData = $this->input->post();
        $this->load->model("Product_prices_model","Product_prices");
        $insert_variant_arr=array();
        $update_variant_arr=array();
        $delete_variant_arr=array();
        $update_price=array();
        $priceid_arr=array();
        $final_delete_arr=array();
        $insert_member_product_variant_arr=array();
        
        foreach ($PostData['priceid'] as $key => $row) {

            if($row==0){
                $insertpricedata = array("productid"=>$PostData['productid'],"price"=>$PostData['price'][$key],"stock"=>$PostData['stock'][$key]);
                $row = $this->Product_prices->Add($insertpricedata);
                
                $insert_member_product_variant_arr[]=array("priceid"=>$row,"price"=>$PostData['price'][$key]);

                foreach ($PostData['variantid'][$key] as $variantkey => $variantrow) {
                    if(isset($PostData['availablevariantid'][$key][$variantkey])){
                        $update_variant_arr[] = array("priceid"=>$row,"variantid"=>$variantrow,"id"=>$PostData['availablevariantid'][$key][$variantkey]);
                        $delete_variant_arr[$row][]=$PostData['availablevariantid'][$key][$variantkey];
                    }else{
                        $insert_variant_arr[] = array("priceid"=>$row,"variantid"=>$variantrow);
                    }
                }
                $priceid_arr[]=$row;
            }else{            
                foreach ($PostData['variantid'][$key] as $variantkey => $variantrow) {
                    if(isset($PostData['availablevariantid'][$key][$variantkey])){
                        $update_variant_arr[] = array("priceid"=>$row,"variantid"=>$variantrow,"id"=>$PostData['availablevariantid'][$key][$variantkey]);
                        $delete_variant_arr[$row][]=$PostData['availablevariantid'][$key][$variantkey];
                    }else{
                        $insert_variant_arr[] = array("priceid"=>$row,"variantid"=>$variantrow);
                    }
                }
                $update_price[]=array("id"=>$row,"price"=>$PostData['price'][$key],"stock"=>$PostData['stock'][$key]);
                 $priceid_arr[]=$row;
            }

        }
        /* print_r($update_variant_arr);
        print_r($insert_variant_arr);
        print_r($delete_variant_arr);
        
        exit; */
        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Member_model","Member");

        if(count($priceid_arr)>0){
            $deleteprices = $this->Product_prices->getProductprices(array("id not in(".implode(",",$priceid_arr).")"=>null,"productid"=>$PostData['productid']));
            if(count($deleteprices)>0){
                foreach ($deleteprices as $dp) {
                    $this->Product_combination->Delete(array("priceid"=>$dp['id']));
                    $final_delete_arr[]=$dp['id'];
                }
            }
            if(count($final_delete_arr)>0){
                $this->Product_prices->Delete(array("id in (".implode(",",$final_delete_arr).")"=>null));

                $this->Member->_table = tbl_membervariantprices;
                $this->Member->Delete(array("priceid in (".implode(",",$final_delete_arr).")"=>null));
                
            }
        }
        // print_r($delete_variant_arr);exit();
        if(count($delete_variant_arr)>0){   
            foreach ($delete_variant_arr as $dvk=>$dv) {
                if(count($dv)>0){
                    $this->Product_combination->Delete(array("priceid"=>array("id not in(".implode(",",$dv).")"=>null,"priceid"=>$dvk)));
                }
            }
        }
        if(count($update_variant_arr)>0){
            $this->Product_combination->edit_batch($update_variant_arr,'id');
        }
        if(count($insert_variant_arr)>0){
            $this->Product_combination->add_batch($insert_variant_arr);
        }

        if(count($insert_member_product_variant_arr)>0){

            $memberids=array();
            if(count($PostData['priceid'])>0){
                $query = $this->readdb->select("(select group_concat(id) from  ".tbl_productprices." where productid=".$PostData['productid'].")as priceids,memberid")
                                    ->from(tbl_memberproduct." as vp")
                                    ->where(array("productid"=>$PostData['productid']))
                                    ->get();
                $memberids = $query->result_array();
            }

            if(count($memberids)>0){
                $insert_member_product_variant_final_arr=array();
                $createddate = $this->general_model->getCurrentDateTime();
                $addedby = $this->session->userdata(base_url().'ADMINID');
                foreach($insert_member_product_variant_arr as $k=>$ivpv){
                    foreach($memberids as $k1=>$vids){
                        $insert_member_product_variant_final_arr[]=array("priceid"=>$ivpv['priceid'],"price"=>$ivpv['price'],"memberid"=>$vids['memberid'],"createddate"=>$createddate,"modifieddate"=>$createddate,"addedby"=>$addedby,"modifiedby"=>$addedby);
                    }
                }
                $this->Member->_table = tbl_membervariantprices;
                $this->Member->add_batch($insert_member_product_variant_final_arr);
            }
        }
        

        if(count($update_price)>0){
            $price = $this->Product_prices->edit_batch($update_price,'id');
        }
        if((isset($combination) && $combination) || (isset($price) || $price) || (isset($updatecombination) || $updatecombination)){
            echo 1;
        }else{
            echo 0;
        }
    }
    public function getProductByCategoryId(){

        $PostData = $this->input->post();
        //$categoryid = $PostData['categoryid'];
        $memberid = (empty($PostData['memberid']))?$this->session->userdata(base_url().'MEMBERID'):$PostData['memberid'];
        if((empty($PostData['memberid']))){
            $productdata = $this->Product->getProductByCategoryId($memberid,0,1);
        }else{
            $sellerid = $this->session->userdata(base_url().'MEMBERID');
            $productdata = $this->Product->getProductByCategoryId($memberid,$sellerid,1);
        }

        
        echo json_encode($productdata);
    }
    public function getProductTaxById()
    {
        $PostData = $this->input->post();
        //$memberid = $PostData["memberid"];
        $productid = $PostData["productid"];
        //$priceid = $PostData["priceid"];
        //$ordertype = $PostData["ordertype"];
        $price = $PostData["price"];
        
        $this->load->model('Product_model', 'Product');
        $this->Product->_fields = "id,(SELECT integratedtax FROM ".tbl_hsncode." WHERE id=hsncodeid) as tax";
        $this->Product->_where = array("id"=>$productid);
        $producttax = $this->Product->getRecordsByID();
        //$productprice = $this->Member->getMemberProductPrice($productid,$memberid,$priceid);
        
        $taxamount = $price * $producttax['tax'] / (100 + $producttax['tax']);

        echo json_encode($taxamount, JSON_NUMERIC_CHECK);
    }
    public function getProductRewardpoints()
    {
        $PostData = $this->input->post();
        $productid = $PostData["productid"];
        $ordertype = $PostData["ordertype"];
        if($ordertype==1){
            $buyermemberid = $this->session->userdata(base_url().'MEMBERID');
            $this->load->model('Member_model', 'Member');
            $memberdata = $this->Member->getmainmember($buyermemberid,"row");
            if(isset($memberdata['id'])){
              $sellermemberid = $memberdata['id'];
            }else{
              $sellermemberid = 0;
            }
        }else{
            $buyermemberid = $PostData["memberid"];
            $sellermemberid = $this->session->userdata(base_url().'MEMBERID');
        }
        
        $this->load->model('Product_model', 'Product');
        $productdata = $this->Product->getProductRewardpointsOrChannelSettings($productid,$buyermemberid,$sellermemberid);
        
        echo json_encode($productdata);
    }
    public function getproductdiscount()
    {
        $PostData = $this->input->post();
        $productid = $PostData["productid"];
        
        $this->load->model('Product_model', 'Product');
        $this->Product->_fields = "id,discount";
        $this->Product->_where = array("id"=>$productid);
        $productdata = $this->Product->getRecordsByID();
        //$productprice = $this->Member->getMemberProductPrice($productid,$memberid,$priceid);
        
        echo json_encode($productdata['discount'], JSON_NUMERIC_CHECK); 
    }

    public function getProductByChannelId(){

        $PostData = $this->input->post();
        $channelid = (!empty($PostData['channelid']))?$PostData['channelid']:$this->session->userdata(base_url().'CHANNELID');
        $productdata = $this->Product->getProductByChannelId($channelid);
       
        echo json_encode($productdata);
    }

    public function updateproductsalesprice(){
        $PostData = $this->input->post();
       
        $productid = json_decode($PostData['productid']);
        $productpriceid = json_decode($PostData['productpriceid']);
        // $price = json_decode($PostData['price']);
        $productallowarr = (!empty($PostData['productallow']))?$PostData['productallow']:0;

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $updatevariantprice=array();
        $updateproductprice=array();
        for ($i=0; $i < count($productid); $i++) { 
            
            $productallow = $productallowarr[$i];
            if($productpriceid[$i]!=0){
                
                $this->Member->_table = tbl_membervariantprices;
                $this->Member->_fields = "id as membervariantpriceid";
                $this->Member->_where = array("priceid"=>$productpriceid[$i],"memberid"=>$memberid);
                $product = $this->Member->getRecordsById();
                
                $updatevariantprice[] = array('id'=>$product['membervariantpriceid'],
                                              /* 'salesprice' => $price[$i], */
                                              "productallow" => $productallow,
                                            );
            }else{
                 
                $this->Member->_table = tbl_memberproduct;
                $this->Member->_fields = "id as memberproductid";
                $this->Member->_where = array("productid"=>$productid[$i],"memberid"=>$memberid);
                $product = $this->Member->getRecordsById();

                $updateproductprice[] = array('id'=>$product['memberproductid'],
                                              /* 'salesprice'=>$price[$i], */
                                              "productallow" => $productallow,
                                            );
            }
           
        }
        if(!empty($updatevariantprice)){
            $this->Member->_table = tbl_membervariantprices;
            $this->Member->edit_batch($updatevariantprice, "id");
        }
        if(!empty($updateproductprice)){
            $this->Member->_table = tbl_memberproduct;
            $this->Member->edit_batch($updateproductprice, "id");
        }
        echo 1;
    }
    public function exportproduct(){
        $this->load->model('Product_model', 'Product');
        $this->Product->exportproduct();
    }
    public function importproductprice(){
        $this->load->model('Product_model', 'Product');
        $this->Product->importproductprice();
    }

    public function getProductByBrandid(){
        $PostData = $this->input->post();
        $brandid = $PostData['brandid'];
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $productdata = $this->Product->getProductByBrandId($brandid,$channelid,$memberid);
        echo json_encode($productdata);
    }

    public function getMemberProductPriceDataByID(){
        $PostData = $this->input->post();
        $productid = $PostData['productid'];
        $priceid = $PostData['priceid'];
        $memberid = $this->session->userdata(base_url().'MEMBERID');

        $memberdata = $this->Member->getmainmember($memberid,"row");
        if(isset($memberdata['id'])){
            $sellerid = $memberdata['id'];
        }else{
            $sellerid = 0;
        }
        $this->load->model("Product_prices_model","Product_prices");
        $this->load->model("Product_model","Product");
        $pricedata = $this->Product->getMemberProductData($memberid,$productid,$priceid,$sellerid);

        $pricedata['multipleprice'] = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$priceid,array("mpqp.id","ASC"));
        echo json_encode($pricedata);
    }

    public function update_sales_price() {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $memberdata = $this->Member->getmainmember($modifiedby,"row");
        if(isset($memberdata['id'])){
            $sellerid = $memberdata['id'];
        }else{
            $sellerid = 0;
        }
        $pricetype = $PostData['pricetype'];

        // print_r($PostData);exit();
        $productid = json_decode($PostData['postproductid']);
        $productpriceid = json_decode($PostData['postpriceid']);
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        
        $this->Member->_table = tbl_membervariantprices;
        $this->Member->_fields = "id as membervariantpriceid";
        $this->Member->_where = array("priceid"=>$productpriceid,"sellermemberid"=>$sellerid,"memberid"=>$memberid);
        $product = $this->Member->getRecordsById();
        
        $updatevariantprice = array("pricetype" => $pricetype);
        $this->Member->_where = array('id'=>$product['membervariantpriceid']);
        $this->Member->Edit($updatevariantprice);
        
        $InsertMultiplePriceData = $UpdateMultiplePriceData = $UpdatedProductQuantityPrice = array();
        if($pricetype==1) { 
            if(!empty($PostData['variantsalesprice'])){
                foreach ($PostData['variantsalesprice'] as $key => $price) {

                    $memberproductquantitypriceid = isset($PostData['memberproductquantitypriceid'][$key])?$PostData['memberproductquantitypriceid'][$key]:"";

                    if($price > 0 && $PostData['variantqty'][$key] > 0){
                        
                        if(!empty($memberproductquantitypriceid)){
                           
                            $UpdateMultiplePriceData[] = array(
                                "id"=>$memberproductquantitypriceid,
                                "salesprice"=>$price,
                                "quantity"=>$PostData['variantqty'][$key],
                                "discount"=>$PostData['variantdiscpercent'][$key]
                            );

                            $UpdatedProductQuantityPrice[] = $memberproductquantitypriceid;
                        }else{

                            $InsertMultiplePriceData[] = array(
                                "membervariantpricesid"=>$product['membervariantpriceid'],
                                "salesprice"=>$price,
                                "quantity"=>$PostData['variantqty'][$key],
                                "discount"=>$PostData['variantdiscpercent'][$key]
                            );
                        }
                    }
                }
            }
        }else{
            $memberproductquantitypriceid = !empty($PostData['singlequantitypricesid'])?$PostData['singlequantitypricesid']:"";

            if($PostData['salesprice'] > 0){
                
                if(!empty($memberproductquantitypriceid)){
                    
                    $UpdateMultiplePriceData[] = array(
                        "id"=>$memberproductquantitypriceid,
                        "salesprice"=>$PostData['salesprice'],
                        "quantity"=>1,
                        "discount"=>$PostData['discper']
                    );

                    $UpdatedProductQuantityPrice[] = $memberproductquantitypriceid;
                }else{

                    $InsertMultiplePriceData[] = array(
                        "membervariantpricesid"=>$product['membervariantpriceid'],
                        "salesprice"=>$PostData['salesprice'],
                        "quantity"=>1,
                        "discount"=>$PostData['discper']
                    );
                }
            }
        }
        $this->load->model("Product_prices_model","Product_prices");
        $priceqtydata = $this->Product_prices->getMemberProductQuantityPriceDataByPriceID($memberid,$productpriceid);
        if(!empty($priceqtydata)){
            $priceqtyids = array_column($priceqtydata, "id");
            $resultId = array_diff($priceqtyids, $UpdatedProductQuantityPrice);

            if(!empty($resultId)){
                $this->Product_prices->_table = tbl_memberproductquantityprice;
                $this->Product_prices->Delete(array("id IN (".implode(",",$resultId).")"=>null));
            }
        }
        if(!empty($InsertMultiplePriceData)){
            $this->Product_prices->_table = tbl_memberproductquantityprice;
            $this->Product_prices->add_batch($InsertMultiplePriceData);
        }
        if(!empty($UpdateMultiplePriceData)){
            $this->Product_prices->_table = tbl_memberproductquantityprice;
            $this->Product_prices->edit_batch($UpdateMultiplePriceData,'id');
        }
        echo 1;
    }
}