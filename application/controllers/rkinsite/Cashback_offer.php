<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashback_offer extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Cashback_offer');
        $this->load->model('Cashback_offer_model', 'Cashback_offer');
    }
    public function index() {
        $this->viewData['title'] = "Cashback Offer";
        $this->viewData['module'] = "cashback_offer/Cashback_offer";
        $this->viewData['VIEW_STATUS'] = "1";
       
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Cashback Offer','View cashback offer.');
        }

        $this->admin_headerlib->add_javascript("Cashback_offer", "pages/cashback_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {   
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
      
        $list = $this->Cashback_offer->get_datatables();   
        $ADMINID = $this->session->userdata(base_url().'ADMINID');       
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $data = array();        
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = $channelname = $type = '';

            if($datarow->usertype==0 && $datarow->addedby==$ADMINID){
                if(in_array($rollid, $edit)) {
                    $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'cashback-offer/edit-cashback-offer/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                
                    if($datarow->status==1){
                        $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'cashback-offer/cashback-offer-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                    else{
                        $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'cashback-offer/cashback-offer-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                    }
                }
                if(in_array($rollid, $delete)) {
                    $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'cashback-offer/check-cashback-offer-use","Cashback&nbsp;Offer","'.ADMIN_URL.'cashback-offer/delete-mul-cashback-offer") >'.delete_text.'</a>';

                    $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                                <label for="deletecheck'.$datarow->id.'"></label></div>';
                }
            }
            $actions .= '<a href="'.ADMIN_URL.'cashback-offer/view-cashback-offer/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';

            $description  = '<div style="display:none" id="detail'.$datarow->id.'">'.stripslashes($datarow->description).'</div>';
            if($datarow->description != ""){
                $pokemon_doc->loadHTML($datarow->description);
                $pokemon_xpath = new DOMXPath($pokemon_doc);
                $href = $pokemon_xpath->evaluate('//@href'); 
                $iframe = $pokemon_xpath->evaluate('//iframe');
                $table = $pokemon_xpath->evaluate('//table');
                $img = $pokemon_xpath->evaluate('//img');

                if($href->length > 0 || $iframe->length > 0 || $table->length > 0 || $img->length > 0){
                    $description .= "<a data-toggle='modal' data-target='#myModal' onclick='viewmore(".$datarow->id.")'>[View More]</a>";
                } else {
                    $datarow->description = strip_tags($datarow->description);
                    $description .= strlen($datarow->description) > 100 ? substr(ucfirst($datarow->description), 0, 100)."<a data-toggle='modal' data-target='#myModal' onclick='viewmore(".$datarow->id.");'>...[view more]</a>": ucfirst($datarow->description);
                } 
            }
            
            if($datarow->channelid != 0){
                $channelnamearr = array();  
                $channelidarr = (!empty($datarow->channelid))?explode(",", $datarow->channelid):'';
                foreach($channelidarr as $channelid){
                
                    $key = array_search($channelid, array_column($channeldata, 'id'));
                    if(!empty($channeldata) && isset($channeldata[$key])){
                        $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].';margin-bottom:5px;">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                    }
                    $channelnamearr[] = $channellabel."<a href='javascript:void(0)' onclick='viewmemberlist(".$datarow->id.",".$channelid.")' data-toggle='modal' data-target='#MemberModal' >".$channeldata[$key]['name']."</a>";
                }
                $channelname = implode(" | ", $channelnamearr);
            }else{
                $channelname = '<span class="label" style="background:#49bf88;">All Channel & '.Member_label.'</span>';
            }
            $actions .= '<a class="'.generateqrcode_class.'" href="'.ADMIN_URL.'cashback-offer/print-product-qr-code/'.$datarow->id.'" title="'.generateqrcode_title.'">'.generateqrcode_text.'</a>';

            $row[] = ++$counter;
            $row[] = $channelname;
            $row[] = $datarow->name;
            $row[] = ($datarow->startdate!="0000-00-00")?$this->general_model->displaydate($datarow->startdate):"-";       
            $row[] = ($datarow->enddate!="0000-00-00")?$this->general_model->displaydate($datarow->enddate):"-";       
            $row[] = ($datarow->description != "")?'<button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="viewdescription('.$datarow->id.')">View Description</button>':"-";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        libxml_use_internal_errors($internalErrors);
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Cashback_offer->count_all(),
                        "recordsFiltered" => $this->Cashback_offer->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function cashback_offer_add() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Cashback Offer";
        $this->viewData['module'] = "cashback_offer/Add_cashback_offer";   
        $this->viewData['VIEW_STATUS'] = "0";

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductActiveList();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_cashback_offer", "pages/add_cashback_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_cashback_offer() {
         
        $PostData = $this->input->post(); 
        $this->load->model('Product_model', 'Product');
        $createddate  =  $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');       
        // print_r($PostData); exit;
        $offername = isset($PostData['offername']) ? trim($PostData['offername']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';    
        $channelid = isset($PostData['channelid']) ? $PostData['channelid'] : '';
        $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';
        $startdate = (!empty($PostData['startdate'])) ? $this->general_model->convertdate($PostData['startdate']) : '';
        $enddate = (!empty($PostData['enddate'])) ? $this->general_model->convertdate($PostData['enddate']) : '';
        $status = $PostData['status'];   

        $this->form_validation->set_rules('offername', 'cashback offer name', 'required|min_length[3]');
        
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
            echo json_encode(array("error"=>"3", 'message'=>$validationError)); 
	    }else{

            $this->Cashback_offer->_where=array("name"=>$offername);
            $Count = $this->Cashback_offer->CountRecords();
            //$Count = 0;
            if($Count == 0){          
                $InsertData = array(
                            'channelid' => $channelid,
                            'name' => $offername,
                            'description'=>$description,   
                            'startdate'=>$startdate,      
                            'enddate' => $enddate,
                            'shortdescription' => $PostData['shortdescription'],
                            'minbillamount' => $PostData['minbillamount'],
                            'usertype' => 0,
                            'status' => $status,
                            'createddate' => $createddate,                    
                            'modifieddate' => $createddate, 
                            'addedby'=>$addedby,
                            'modifiedby'=>$addedby);
                
                $CashbackOfferId = $this->Cashback_offer->add($InsertData);
                if($CashbackOfferId){
                    $insertoffermembermapping = array();
   
                    if(!empty($memberidarr)){
                        for($i=0;$i<count($memberidarr);$i++){
                            $insertoffermembermapping[] = array("cashbackofferid"=>$CashbackOfferId,
                                                                "memberid"=>$memberidarr[$i]);
                        }
                        if(!empty($insertoffermembermapping)){
                            $this->Cashback_offer->_table = tbl_cashbackoffermembermapping;
                            $this->Cashback_offer->add_batch($insertoffermembermapping);
                        }
                    }
                    $productidArr = $PostData['productid'];
                
                    if(!empty($productidArr)){
                        $insertProductData = array();
                        foreach($productidArr as $i=>$productid){
                            $priceid = $PostData['priceid'][$i];
                            $earnpoints = $PostData['earnpoints'][$i];

                            if(!empty($productid) && !empty($priceid) && !empty($earnpoints)){

                                $this->Product->_fields = "name";
                                $this->Product->_where = array("id"=>$productid);
                                $productdata = $this->Product->getRecordsById();

                                $insertProductData[] = array(
                                    "cashbackofferid"=>$CashbackOfferId,
                                    "productid"=>$productid,
                                    "priceid"=>$priceid,
                                    "earnpoints"=>$earnpoints,
                                    "productname"=>$productdata['name']
                                );
                            }
                        } 
                        if(!empty($insertProductData)){
                            $this->Cashback_offer->_table = tbl_cashbackofferproductmapping;
                            $this->Cashback_offer->add_batch($insertProductData);
                        }
                    }
                    if(!empty($memberidarr)){
                        
                        $this->load->model('Fcm_model','Fcm');
                        $fcmquery = $this->Fcm->getFcmDataByMemberId(implode(",",$memberidarr));
                        
                        if(!empty($fcmquery)){
                            foreach ($fcmquery as $fcmrow){   
                                $fcmarray=array();                       
                                $type = "17";// catalog =1 , news =2 , product =3
                                $msg = $offername.", ".$PostData['shortdescription'];    
                                $memberid = $fcmrow['memberid'];                     
                                $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$CashbackOfferId.'"}';
                                $fcmarray[] = $fcmrow['fcm'];
                                $insert = 0;
                                
                                //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);
                                $this->Fcm->sendFcmNotification($type,$pushMessage,$memberid,$fcmarray,$insert,$fcmrow['devicetype']);

                                $insertData[] = array(
                                    'type'=>$type,
                                    'message' => $pushMessage,
                                    'memberid'=>$memberid, 
                                    'isread'=>0,      
                                    'usertype'=>1,                  
                                    'createddate' => $createddate,               
                                    'addedby'=>$addedby
                                );    
                            }
                        }  
                        
                        if(!empty($insertData)){
                            $this->load->model('Notification_model','Notification');
                            $this->Notification->_table = tbl_notification;
                            $this->Notification->add_batch($insertData);
                        }
                    }
                   
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Cashback Offer','Add new '.$offername.' cashback offer.');
                    }
                    echo json_encode(array("error"=>"1")); // Offer added successfully. 
                } else {
                    echo json_encode(array("error"=>"0")); // Offer not added. 
                }
            } else {
                echo json_encode(array("error"=>"2")); // Offer already exists.
            }
        }
    }
    public function edit_cashback_offer($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Cashback Offer";
        $this->viewData['module'] = "cashback_offer/Add_cashback_offer";
        $this->viewData['VIEW_STATUS'] = "1";   
        $this->viewData['action'] = "1";  

        $this->viewData['cashbackofferdata'] = $this->Cashback_offer->getCashbackOfferDataByID($id);
        if(empty($this->viewData['cashbackofferdata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $this->viewData['cashbackofferproductdata'] = $this->Cashback_offer->getCashbackOfferProductsByOfferID($id);

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductActiveList();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Add_cashback_offer", "pages/add_cashback_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    } 
    public function update_cashback_offer(){

        $PostData = $this->input->post(); 
        $modifieddate  =  $this->general_model->getCurrentDateTime();  
        $modifiedby = $this->session->userdata(base_url().'ADMINID');     
        $this->load->model('Product_model', 'Product');

        $cashbackofferid= trim($PostData['cashbackofferid']);
        $offername = isset($PostData['offername']) ? trim($PostData['offername']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';    
        $status = $PostData['status'];
        $channelid = isset($PostData['channelid']) ? $PostData['channelid'] : '';
        $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';
        $startdate = (!empty($PostData['startdate'])) ? $this->general_model->convertdate($PostData['startdate']) : '';
        $enddate = (!empty($PostData['enddate'])) ? $this->general_model->convertdate($PostData['enddate']) : '';
        
        $this->form_validation->set_rules('offername', 'cashback offer name', 'required|min_length[3]');
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
            echo json_encode(array("error"=>"3", 'message'=>$validationError)); 
	    }else{
       
            $this->Cashback_offer->_where = "name= '".$offername."' AND id <> ".$cashbackofferid;
            $Count = $this->Cashback_offer->CountRecords();

            if(empty($Count)){     
                
                $updateData = array(
                    'channelid' => $channelid,
                    'name' => $offername,
                    'description'=>$description,   
                    'startdate'=>$startdate,   
                    'enddate' => $enddate,
                    'shortdescription' => $PostData['shortdescription'],
                    'minbillamount' => $PostData['minbillamount'],
                    'status' => $status,
                    'modifieddate' => $modifieddate, 
                    'modifiedby'=>$modifiedby);
              
                $this->Cashback_offer->_where = array('id' => $cashbackofferid);
                $this->Cashback_offer->Edit($updateData);

                $oldmemberidarr = isset($PostData['oldmemberid']) ? explode(',',$PostData['oldmemberid']): array();
                $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : array();
                $diff_result = array_values(array_diff($oldmemberidarr, $memberidarr));

                $this->Cashback_offer->_table = tbl_cashbackoffermembermapping;
                if(!empty($diff_result)){
                    $this->Cashback_offer->Delete("FIND_IN_SET(memberid,'".implode(',',$diff_result)."')>0 AND cashbackofferid=".$cashbackofferid);
                }

                $diff_result = array_values(array_diff($memberidarr,$oldmemberidarr));
                if(!empty($diff_result)){
                    
                    for($i=0;$i<count($diff_result);$i++){
                        $insertoffermembermapping[] = array("cashbackofferid"=>$cashbackofferid,
                                                            "memberid"=>$diff_result[$i]);
                    }
                    if(!empty($insertoffermembermapping)){
                        $this->Cashback_offer->add_batch($insertoffermembermapping);
                    }
                }
      
                $productidArr = $PostData['productid'];
                $insertProductData = $updateProductData = $EditProductID = array();
                if(!empty($productidArr)){
                    foreach($productidArr as $i=>$productid){
                        $priceid = $PostData['priceid'][$i];
                        $earnpoints = $PostData['earnpoints'][$i];
                        $cashbackofferproductid = isset($PostData['cashbackofferproductid'][$i])?$PostData['cashbackofferproductid'][$i]:0;

                        if(!empty($productid) && !empty($priceid) && !empty($earnpoints)){
                            $this->Product->_fields = "name";
                            $this->Product->_where = array("id"=>$productid);
                            $productdata = $this->Product->getRecordsById();
                            
                            if(!empty($cashbackofferproductid)){
                                
                                $updateProductData[] = array(
                                    "id"=>$cashbackofferproductid,
                                    "productid"=>$productid,
                                    "priceid"=>$priceid,
                                    "earnpoints"=>$earnpoints,
                                    "productname"=>$productdata['name']
                                );

                                $EditProductID[] = $cashbackofferproductid;
                            }else{

                                $insertProductData[] = array(
                                    "cashbackofferid"=>$cashbackofferid,
                                    "productid"=>$productid,
                                    "priceid"=>$priceid,
                                    "earnpoints"=>$earnpoints,
                                    "productname"=>$productdata['name']
                                );
                            }
                        }
                    }
                } 
                $cashbackofferproductdata = $this->Cashback_offer->getCashbackOfferProductsByOfferID($cashbackofferid);
                $cashbackofferproductidarr = !empty($cashbackofferproductdata)?array_column($cashbackofferproductdata,"id"):array();
                $removediff = array_diff($cashbackofferproductidarr, $EditProductID);
                
                if(!empty($removediff)){
                    $this->Cashback_offer->_table = tbl_cashbackofferproductmapping;
                    $this->Cashback_offer->Delete("id IN (".implode(',',$removediff).")");
                }
                if(!empty($insertProductData)){
                    $this->Cashback_offer->_table = tbl_cashbackofferproductmapping;
                    $this->Cashback_offer->add_batch($insertProductData);
                }
                if(!empty($updateProductData)){
                    $this->Cashback_offer->_table = tbl_cashbackofferproductmapping;
                    $this->Cashback_offer->edit_batch($updateProductData,"id");
                }
                
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Cashback Offer','Edit '.$offername.' cashback offer.');
                }
                echo json_encode(array("error"=>"1")); //Offer updated successfully
                
            } else {
                echo json_encode(array("error"=>"2")); // Offer  name already added
            }
        }
    } 
    public function view_cashback_offer($cashbackofferid) {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Cashback Offer Details";
        $this->viewData['module'] = "cashback_offer/View_cashback_offer";
       
        $this->viewData['cashbackofferdata'] = $this->Cashback_offer->getCashbackOfferDetailsByID($cashbackofferid);
        if(empty($this->viewData['cashbackofferdata'])){
            redirect(ADMINFOLDER.'dashboard');
        }
        $this->viewData['cashbackofferproductdata'] = $this->Cashback_offer->getCashbackOfferProductsByOfferID($cashbackofferid);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Cashback Offer','View '.$this->viewData['cashbackofferdata']['name'].' cashback offer detail.');
        }
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("view_cashback_offer", "pages/view_cashback_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function viewcashbackofferdescription(){
        $PostData = $this->input->post();
        
        $cashbackofferid = $PostData['id'];
        $offerdata = $this->Cashback_offer->getCashbackOfferDataByID($cashbackofferid);
        echo json_encode(array('description'=>$offerdata['description']));
    }
    public function viewmemberlist(){
        $PostData = $this->input->post();
        
        $cashbackofferid = $PostData['id'];
        $channelid = $PostData['channelid'];


        $memberdata = $this->Cashback_offer->getMemberListByOfferChannelID($cashbackofferid,$channelid);
        echo json_encode($memberdata);
    }
    public function check_cashback_offer_use() {
        $PostData = $this->input->post();
        $count = 0;
        $ids = explode(",",$PostData['ids']);
        foreach($ids as $row){
           
        }  
        echo $count;
    }
    public function delete_mul_cashback_offer() {
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);

        foreach ($ids as $row) {
           
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Cashback_offer->_table = tbl_cashbackoffer;
                $this->Cashback_offer->_where = array("id"=>$row);
                $Offerdata = $this->Cashback_offer->getRecordsById();
        
                $this->general_model->addActionLog(3,'Cashback Offer','Delete '.$Offerdata['name'].' cashback offer.');
            }

            $this->Cashback_offer->_table = tbl_cashbackoffermembermapping;
            $this->Cashback_offer->Delete(array("cashbackofferid"=>$row));

            $this->Cashback_offer->_table = tbl_cashbackofferproductmapping;
            $this->Cashback_offer->Delete(array("cashbackofferid"=>$row));

            $this->Cashback_offer->_table = tbl_cashbackoffer;
            $this->Cashback_offer->Delete(array("id"=>$row));  
        }
    }
    public function cashback_offer_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Cashback_offer->_where = array("id" => $PostData['id']);
        $this->Cashback_offer->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Cashback_offer->_where = array("id"=>$PostData['id']);
            $data = $this->Cashback_offer->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' cashback offer.';
            
            $this->general_model->addActionLog(2,'Cashback Offer', $msg);
        }
        echo $PostData['id'];
    }
    public function getofferdescriptionbyid(){
        $PostData = $this->input->post();
        $this->Offer->_fields = "name,description";
        $this->Offer->_where = "id=".$PostData['id'];
        $data = $this->Offer->getRecordsByID();
 
        echo json_encode(array('pagetitle'=>ucwords($data['title']),'description'=>$data['description']));
    }
    public function getVariantByProductId(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getVariantByProductIdForAdmin($PostData['productid']);
        echo json_encode($productdata);
    }
    public function print_product_qr_code($cashbackofferid) {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Print Product QR Code";
        $this->viewData['module'] = "cashback_offer/Print_product_qr_code";   
        
        $this->viewData['cashbackofferdata'] = $this->Cashback_offer->getCashbackOfferDetailsByID($cashbackofferid);
        if(empty($this->viewData['cashbackofferdata'])){
            redirect(ADMINFOLDER.'dashboard');
        }
        $this->viewData['cashbackofferproductdata'] = $this->Cashback_offer->getCashbackOfferProductsByOfferID($cashbackofferid);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Cashback Offer','Print '.$this->viewData['cashbackofferdata']['name'].' cashback offer product QR code details.');
        }
        $this->admin_headerlib->add_javascript("view_cashback_offer", "pages/view_cashback_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function printQRCode(){
        $PostData = $this->input->post(); 
        $cashbackofferid = $PostData['cashbackofferid'];
        
        $offer=array();
        $offer['printnoofcopies'] = $PostData['printnoofcopies'];
        $offer['heading'] = "";
        $offer['cashbackofferdata'] = $this->Cashback_offer->getCashbackOfferDetailsByID($cashbackofferid);
        $offer['productdata'] = $this->Cashback_offer->getCashbackOfferProductsByOfferID($cashbackofferid);
        
        $html['content'] = $this->load->view(ADMINFOLDER."cashback_offer/printqrcodeformat.php",$offer,true);

        // echo $html['content']; exit;
        echo json_encode($html); 
    }
}
?>