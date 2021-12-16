<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offer extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Offer');
        $this->load->model('Offer_model', 'Offer');
        $this->load->model('Offer_purchased_product_model', 'Offer_purchased_product');
        $this->load->model('Offer_product_model', 'Offer_product');
    }
    public function index() {
        $this->viewData['title'] = "Offer";
        $this->viewData['module'] = "offer/Offer";
        $this->viewData['VIEW_STATUS'] = "1";
       
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Offer','View offer.');
        }

        $this->admin_headerlib->add_javascript("Offer", "pages/offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {   
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Offer->get_datatables();   
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
                    $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'offer/edit-offer/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){

                    if($datarow->status==1){
                        $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'offer/offer-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                    else{
                        $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'offer/offer-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                    }
                    
                }
                if(in_array($rollid, $delete)) {
                    $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'offer/check-offer-use","Offer","'.ADMIN_URL.'offer/delete-mul-offer") >'.delete_text.'</a>';

                    $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                                <label for="deletecheck'.$datarow->id.'"></label></div>';
                }
            }
            $actions .= '<a href="'.ADMIN_URL.'offer/view-offer/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';

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
           
            if($datarow->type == 1){
                $type = "Display Only";
            }else if($datarow->type == 2){
                $type = "Product Base";
            }else if($datarow->type == 3){
                $type = "Service Base";
            }else if($datarow->type == 4){
                $type = "Target Base";
            }
            $row[] = ++$counter;
            $row[] = $channelname;
            $row[] = ucwords($datarow->name);
            $row[] = $type;
            $row[] = ($datarow->startdate!="0000-00-00")?$this->general_model->displaydate($datarow->startdate):"-";       
            $row[] = ($datarow->enddate!="0000-00-00")?$this->general_model->displaydate($datarow->enddate):"-";       
            $row[] = ($datarow->description != "")?'<button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="viewdescription('.$datarow->id.')">View Description</button>':"";
            // $row[] = $description;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        libxml_use_internal_errors($internalErrors);

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Offer->count_all(),
                        "recordsFiltered" => $this->Offer->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
   
    public function offer_add() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Offer');      
        $this->viewData['title'] = "Add Offer";
        $this->viewData['module'] = "offer/Add_offer";   
        $this->viewData['VIEW_STATUS'] = "0";

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->load->model("Product_model","Product");
        $this->viewData['purchaseproductdata'] = $this->Product->getProductByCategoryId(0,0,1);
        $this->viewData['offerproductdata'] = $this->Product->getProductByCategoryId(0);

        $this->load->model("Brand_model","Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Offer", "pages/add_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_offer() {
         
        $PostData = $this->input->post(); 
        
        $createddate  =  $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');       
        // print_r($PostData); exit;
        $offername = isset($PostData['offername']) ? trim($PostData['offername']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';    
        $type = $PostData['type'];   
        $useractivationrequired = $PostData['useractivationrequired'];   
        $offertype = $PostData['offertype'];   
        $minpurchaseamount = (isset($PostData['minpurchaseamount']) && $offertype==0)?$PostData['minpurchaseamount']:0;
        
        $targetvalue = ($type==4 && isset($PostData['targetvalue']))?$PostData['targetvalue']:0;
        $rewardvalue = ($type==4 && isset($PostData['rewardvalue']))?$PostData['rewardvalue']:0;
        $rewardtype = ($type==4 && isset($PostData['rewardtype']))?$PostData['rewardtype']:0;

        $status = $PostData['status'];   
        $channelid = isset($PostData['channelid']) ? $PostData['channelid'] : '';
        $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';
        $startdate = (!empty($PostData['startdate'])) ? $this->general_model->convertdate($PostData['startdate']) : '';
        $enddate = (!empty($PostData['enddate'])) ? $this->general_model->convertdate($PostData['enddate']) : '';

        $priorityarr = isset($PostData['priority']) ? $PostData['priority'] : '';
        $memberid = (!empty($memberidarr))?implode(",",$memberidarr):"0";

        $this->form_validation->set_rules('offername', 'offer name', 'required|min_length[3]');
        
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
            echo json_encode(array("error"=>"3", 'message'=>$validationError)); 
	    }else{

            $this->Offer->_where=array("name"=>$offername);
            $Count = $this->Offer->CountRecords();
            //$Count = 0;
            if($Count == 0){          
                $InsertData = array(
                            'channelid' => $channelid,
                            'name' => $offername,
                            'description'=>$description,   
                            'startdate'=>$startdate,      
                            'enddate' => $enddate,
                            'type' => $type,
                            'offertype' => $offertype,
                            'useractivationrequired' => $useractivationrequired,
                            'minimumpurchaseamount' => $minpurchaseamount,
                            'shortdescription' => $PostData['shortdescription'],
                            'minbillamount' => $PostData['minbillamount'],
                            'maximumusage' => $PostData['maximumusage'],
                            'noofcustomerused' => $PostData['noofcustomerused'],
                            'targetvalue' => $targetvalue,
                            'rewardvalue' => $rewardvalue,
                            'rewardtype' => $rewardtype,
                            'usertype' => 0,
                            'status' => $status,
                            'createddate' => $createddate,                    
                            'modifieddate' => $createddate, 
                            'addedby'=>$addedby,
                            'modifiedby'=>$addedby);
                
                $OfferId = $this->Offer->add($InsertData);
                if($OfferId){
                    $insertofferimagedata = $insertofferpurchasedproduct = $insertoffermembermapping = $insertcombination = $insertofferproduct = array();
                    if(!empty($_FILES)){
                    
                        foreach ($_FILES as $key => $value) {
                            $id = preg_replace('/[^0-9]/', '', $key);
                            if($_FILES['offerimage'.$id]['name']!='' && strpos($key, 'offerimage') !== false){
                                
                                $image = uploadFile('offerimage'.$id, 'OFFERIMAGE', OFFER_PATH, '*', '', 1, OFFER_LOCAL_PATH);
                                if($image !== 0 && $image !== 2){
                                    
                                    $insertofferimagedata[] = array(
                                        "offerid"=>$OfferId,
                                        "filename"=>$image,
                                        'priority'=>$priorityarr[$id-1]
                                    );
                                }           
                            }
                        }
                    }
                    if(!empty($insertofferimagedata)){
                        $this->Offer->_table = tbl_offerimage;
                        $this->Offer->add_batch($insertofferimagedata);
                    }
                    if(!empty($memberidarr)){
                        for($i=0;$i<count($memberidarr);$i++){
                            $insertoffermembermapping[] = array("offerid"=>$OfferId,
                                                                "memberid"=>$memberidarr[$i]);
                        }
                        if(!empty($insertoffermembermapping)){
                            $this->Offer->_table = tbl_offermembermapping;
                            $this->Offer->add_batch($insertoffermembermapping);
                        }
                    }
                    if($type!=1){
                        $combinationidarr = $PostData['combinationid'];
                        $purchaseproductidarr = $PostData['purchaseproductid'];
                        $purchasepriceidarr = $PostData['purchasepriceid'];
                        $purchaseqtyarr = $PostData['purchaseqty'];
    
                        $offerproductidarr = $PostData['offerproductid'];
                        $offerpriceidarr = $PostData['offerpriceid'];
                        $offerqtyarr = $PostData['offerqty'];
                        $discountvaluearr = $PostData['discountvalue'];
                        $this->Offer->_table = tbl_offercombination;
                        for($i=0;$i<count($combinationidarr);$i++){
                            
                            $combinationid = $combinationidarr[$i];
                            $offercombinationid = $this->Offer->Add(array("offerid"=>$OfferId,"multiplication"=>$PostData['multiplication_'.$combinationid]));
                            if(!empty($purchaseproductidarr[$combinationid]) && !empty($offercombinationid)){

                                for ($p=0;$p<count($purchaseproductidarr[$combinationid]);$p++) {
                                    $insertofferpurchasedproduct[] = array('offerid'=>$OfferId,
                                                                            'offercombinationid'=>$offercombinationid,
                                                                            'productvariantid'=>implode(',',$purchasepriceidarr[$combinationid][$p]),
                                                                            'quantity'=>$purchaseqtyarr[$combinationid][$p]);
                                }
                            }

                            if(($type==2 || $type==4) && !empty($offerproductidarr[$combinationid]) && !empty($offercombinationid)){
                                for ($o=0;$o<count($offerproductidarr[$combinationid]);$o++) {

                                    if(!empty($offerpriceidarr[$combinationid][$o])){

                                        $discounttype = (!empty($PostData['offerdiscounttype_'.($i+1).'_'.($o+1)]))?$PostData['offerdiscounttype_'.($i+1).'_'.($o+1)]:0;
                                        $insertofferproduct[] = array('offerid'=>$OfferId,
                                                                    'offercombinationid'=>$offercombinationid,
                                                                    'productvariantid'=>$offerpriceidarr[$combinationid][$o],
                                                                    'quantity'=>$offerqtyarr[$combinationid][$o],
                                                                    'discounttype'=>$discounttype,
                                                                    'discountvalue'=>$discountvaluearr[$combinationid][$o]);
                                    }
                                }
                            }
                        }
                        
                        if(!empty($insertofferpurchasedproduct)){
                            $this->Offer_purchased_product->add_batch($insertofferpurchasedproduct);
                        }
                        if(!empty($insertofferproduct)){
                            $this->Offer_product->add_batch($insertofferproduct);
                        }
                    }
                    
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Offer','Add new '.$offername.' offer.');
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

    public function edit_offer($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Offer";
        $this->viewData['module'] = "offer/Add_offer";
        $this->viewData['VIEW_STATUS'] = "1";   
        $this->viewData['action'] = "1";  

        $this->viewData['offerdata'] = $this->Offer->getOfferDataByID($id);
        $this->viewData['offerimagedata'] = $this->Offer->getOfferImageDataByOfferID($id);
        
        $offercombinationid = array_filter(explode(',',$this->viewData['offerdata']['offercombinationid']));
        
        $offercombination = array();
        for($i=0;$i<count($offercombinationid);$i++){
            $offercombination[$offercombinationid[$i]][0] = $this->Offer_purchased_product->getOfferProduct($id,$offercombinationid[$i]);
            $offercombination[$offercombinationid[$i]][1] = $this->Offer_product->getOfferProduct($id,$offercombinationid[$i]);
        }
        $this->viewData['offercombination'] = $offercombination;
        
        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->load->model("Product_model","Product");
        $this->viewData['purchaseproductdata'] = $this->Product->getProductByCategoryId(0,0,1);
        $this->viewData['offerproductdata'] = $this->Product->getProductByCategoryId(0);
        // print_r($offercombinationid); exit;
        $this->load->model("Brand_model","Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Offer", "pages/add_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    } 
    
    public function update_offer(){

        $PostData = $this->input->post(); 
        $modifieddate  =  $this->general_model->getCurrentDateTime();  
        $modifiedby = $this->session->userdata(base_url().'ADMINID');     
        // print_r($PostData); 
        // print_r($_FILES); 
        // exit;
        
        $offerid= trim($PostData['offerid']);
        $offername = isset($PostData['offername']) ? trim($PostData['offername']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';    
        $type = $PostData['type'];
        $useractivationrequired = $PostData['useractivationrequired'];  
        $oldtype = $PostData['oldtype'];
        $status = $PostData['status'];
        $channelid = isset($PostData['channelid']) ? $PostData['channelid'] : '';
        $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';
        $startdate = (!empty($PostData['startdate'])) ? $this->general_model->convertdate($PostData['startdate']) : '';
        $enddate = (!empty($PostData['enddate'])) ? $this->general_model->convertdate($PostData['enddate']) : '';
        $offertype = $PostData['offertype'];   
        $minpurchaseamount = (isset($PostData['minpurchaseamount']) && $offertype==0)?$PostData['minpurchaseamount']:0;   
        $targetvalue = ($type==4 && isset($PostData['targetvalue']))?$PostData['targetvalue']:0;
        $rewardvalue = ($type==4 && isset($PostData['rewardvalue']))?$PostData['rewardvalue']:0;
        $rewardtype = ($type==4 && isset($PostData['rewardtype']))?$PostData['rewardtype']:0;
        $priorityarr = isset($PostData['priority']) ? $PostData['priority'] : '';

        $this->form_validation->set_rules('offername', 'offer name', 'required|min_length[3]');
        // print_r($description); exit;
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
            echo json_encode(array("error"=>"3", 'message'=>$validationError)); 
	    }else{
       
            $this->Offer->_where = "name= '".$offername."' AND id <> ".$offerid;
            $Count = $this->Offer->CountRecords();

            if(empty($Count)){     
                
                $updateData = array(
                    'channelid' => $channelid,
                    'name' => $offername,
                    'description'=>$description,   
                    'startdate'=>$startdate,   
                    'enddate' => $enddate,
                    'type' => $type,
                    'offertype' => $offertype,
                    'minimumpurchaseamount' => $minpurchaseamount,
                    'useractivationrequired' => $useractivationrequired,
                    'shortdescription' => $PostData['shortdescription'],
                    'minbillamount' => $PostData['minbillamount'],
                    'maximumusage' => $PostData['maximumusage'],
                    'noofcustomerused' => $PostData['noofcustomerused'],
                    'targetvalue' => $targetvalue,
                    'rewardvalue' => $rewardvalue,
                    'rewardtype' => $rewardtype,
                    'status' => $status,
                    'modifieddate' => $modifieddate, 
                    'modifiedby'=>$modifiedby);
              
                $this->Offer->_where = array('id' => $offerid);
                $this->Offer->Edit($updateData);

                $insertofferimagedata = $insertofferpurchasedproduct = $updateofferimagedata = $updateofferpurchasedproduct = $updateofferproduct = $insertofferproduct = $removeimage = array();

                if(isset($PostData['removeofferimageid']) && $PostData['removeofferimageid']!=''){
                    
                    $ImageData = $this->readdb->select("id,filename")
                                        ->from(tbl_offerimage)
                                        ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeofferimageid'])))."')>0")
                                        ->get()->result_array();
                    
                    if(!empty($ImageData)){
                        foreach ($ImageData as $row) {
                            unlinkfile("OFFERIMAGE",$row['filename'], OFFER_PATH);
                            $this->Offer->_table = tbl_offerimage;
                            $this->Offer->Delete(array('id'=>$row['id']));
                        }
                    }
                }
                
                if(!empty($_FILES)){
                    foreach ($_FILES as $key => $value) {

                        $id = preg_replace('/[^0-9]/', '', $key);
                        
                        if(!isset($PostData['offerimageid'.$id])){
    
                            if($_FILES['offerimage'.$id]['name']!=''){
                                $image = uploadFile('offerimage'.$id, 'OFFERIMAGE', OFFER_PATH, '*', '', 1, OFFER_LOCAL_PATH);
                                //print_r($_FILES); exit;
                                if($image !== 0 && $image !== 2){
                                    
                                    $insertofferimagedata[] = array(
                                        "offerid"=>$offerid,
                                        "filename"=>$image,
                                        'priority'=>$priorityarr[$id-1]
                                    );
                                }      
                            }    
                        }else if($_FILES['offerimage'.$id]['name'] != '' && isset($PostData['offerimageid'.$id])){
    
                            $this->Offer->_table = tbl_offerimage;
                            $this->Offer->_where = "id=".$PostData['offerimageid'.$id];
                            $ImageData = $this->Offer->getRecordsByID();
    
                            if(!empty($ImageData)){
                                unlinkfile("OFFERIMAGE",$ImageData['filename'], OFFER_PATH);
                            }
                                
                            $image = uploadFile('offerimage'.$id, 'OFFERIMAGE', OFFER_PATH, '*', '', 1, OFFER_LOCAL_PATH);
                            if($image !== 0 && $image !== 2){
                                
                                $updateofferimagedata[] = array(
                                    "id"=>$PostData['offerimageid'.$id],
                                    "filename"=>$image,
                                    'priority'=>$priorityarr[$id-1]
                                );
                            } 
                        }
                    }
                }
                if(isset($removeimage) && count($removeimage)>0){
                    $this->Offer->_table = tbl_offerimage;
                    $this->Offer->Delete("id NOT IN (".implode(",",$removeimage).") and offerid=$offerid");
                }
                if(!empty($insertofferimagedata)){
                    $this->Offer->_table = tbl_offerimage;
                    $this->Offer->add_batch($insertofferimagedata);
                }
                if(!empty($updateofferimagedata)){
                    $this->Offer->_table = tbl_offerimage;
                    $this->Offer->edit_batch($updateofferimagedata, "id");
                }

                if(!empty($PostData['removecombinationtableid'])){
                    $this->Offer_purchased_product->Delete("FIND_IN_SET(offercombinationid,'".implode(',',array_filter(explode(",",$PostData['removecombinationtableid'])))."')>0");
                    $this->Offer_product->Delete("FIND_IN_SET(offercombinationid,'".implode(',',array_filter(explode(",",$PostData['removecombinationtableid'])))."')>0");

                    $this->Offer->_table = tbl_offercombination;
                    $this->Offer->Delete("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removecombinationtableid'])))."')>0");
                }
                
                $oldmemberidarr = isset($PostData['oldmemberid']) ? explode(',',$PostData['oldmemberid']): array();
                $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : array();
                $diff_result = array_values(array_diff($oldmemberidarr, $memberidarr));

                $this->Offer->_table = tbl_offermembermapping;
                if(!empty($diff_result)){
                    $this->Offer->Delete("FIND_IN_SET(memberid,'".implode(',',$diff_result)."')>0 AND offerid=".$offerid);
                }

                $diff_result = array_values(array_diff($memberidarr,$oldmemberidarr));
                if(!empty($diff_result)){
                    
                    for($i=0;$i<count($diff_result);$i++){
                        $insertoffermembermapping[] = array("offerid"=>$offerid,
                                                            "memberid"=>$diff_result[$i]);
                    }
                    if(!empty($insertoffermembermapping)){
                        $this->Offer->add_batch($insertoffermembermapping);
                    }
                }
                if($type!=1){
                    $combinationidarr = $PostData['combinationid'];
                    $combinationtableidarr = (!empty($PostData['combinationtableid']))?$PostData['combinationtableid']:array();
                    $removepurchaseofferproductidarr = (!empty($PostData['removepurchaseofferproductid']))?$PostData['removepurchaseofferproductid']:array();
                    $removeofferproductidarr = (!empty($PostData['removeofferproductid']))?$PostData['removeofferproductid']:array();
                    $purchaseofferproductidarr = (!empty($PostData['purchaseofferproductid']))?$PostData['purchaseofferproductid']:array();
                    $purchaseproductidarr = $PostData['purchaseproductid'];
                    $purchasepriceidarr = (!empty($PostData['purchasepriceid']))?$PostData['purchasepriceid']:array();
                    $purchaseqtyarr = $PostData['purchaseqty'];

                    $offerproducttableidarr = (!empty($PostData['offerproducttableid']))?$PostData['offerproducttableid']:array();
                    $offerproductidarr = $PostData['offerproductid'];
                    $offerpriceidarr = $PostData['offerpriceid'];
                    $offerqtyarr = $PostData['offerqty'];
                    $discountvaluearr = $PostData['discountvalue'];
                    $combinationidsarr = array();
                    for($i=0;$i<count($combinationidarr);$i++){
                    
                        $combinationid = $combinationidarr[$i];
                        $this->Offer->_table = tbl_offercombination;
                        if(!empty($combinationtableidarr[$i])){
                            $offercombinationid = $combinationtableidarr[$i];
                            $this->Offer->_where = array('id'=>$offercombinationid);
                            $this->Offer->Edit(array("multiplication"=>$PostData['multiplication_'.$combinationid]));
                            $combinationidsarr[] = $offercombinationid;
                        }else{
                            $offercombinationid = $this->Offer->Add(array("offerid"=>$offerid,"multiplication"=>$PostData['multiplication_'.$combinationid]));
                        }
                        
                        if(!empty($removepurchaseofferproductidarr[$i])){
                            $this->Offer_purchased_product->Delete("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removepurchaseofferproductidarr[$i])))."')>0");
                        }
                        if(!empty($removeofferproductidarr[$i])){
                            $this->Offer_product->Delete("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$removeofferproductidarr[$i])))."')>0");
                        }
    
                        if(!empty($purchaseproductidarr[$combinationid]) && !empty($offercombinationid)){
                            $offerpurchaseproductidsarr = array();
                            for ($p=0;$p<count($purchaseproductidarr[$combinationid]);$p++) {
                                $productvariantid = (!empty($purchasepriceidarr[$combinationid][$p]))?implode(',',$purchasepriceidarr[$combinationid][$p]):'';
                                if(!empty($purchaseofferproductidarr[$combinationid][$p])){
                                    $updateofferpurchasedproduct[] = array('offerid'=>$offerid,
                                                                            'offercombinationid'=>$offercombinationid,
                                                                            'productvariantid'=>$productvariantid,
                                                                            'quantity'=>$purchaseqtyarr[$combinationid][$p],
                                                                            'id'=>$purchaseofferproductidarr[$combinationid][$p]);
                                    $offerpurchaseproductidsarr[] = $purchaseofferproductidarr[$combinationid][$p]; 
                                }else{
                                    $insertofferpurchasedproduct[] = array('offerid'=>$offerid,
                                                                            'offercombinationid'=>$offercombinationid,
                                                                            'productvariantid'=>$productvariantid,
                                                                            'quantity'=>$purchaseqtyarr[$combinationid][$p]);
                                }
                                
                            }
                            if(!empty($offerpurchaseproductidsarr) && $offertype == 0){
                                $this->Offer_purchased_product->Delete(array("offerid"=>$offerid,"id NOT IN (".implode(",",$offerpurchaseproductidsarr).")"=>null));
                            }
                        }
                        if(($type==2 || $type==4) && !empty($offerproductidarr[$combinationid]) && !empty($offercombinationid)){
                            $offerproductidsarr = array();
                            for ($o=0;$o<count($offerproductidarr[$combinationid]);$o++) {
                                
                                $discounttype = (!empty($PostData['offerdiscounttype_'.($i+1).'_'.($o+1)]))?$PostData['offerdiscounttype_'.($i+1).'_'.($o+1)]:0;
                                if (!empty($offerproducttableidarr[$combinationid][$o])) {

                                    if(!empty($offerpriceidarr[$combinationid][$o])){
                                        $updateofferproduct[] = array('offerid'=>$offerid,
                                                                        'offercombinationid'=>$offercombinationid,
                                                                        'productvariantid'=>$offerpriceidarr[$combinationid][$o],
                                                                        'quantity'=>$offerqtyarr[$combinationid][$o],
                                                                        'discounttype'=>$discounttype,
                                                                        'discountvalue'=>$discountvaluearr[$combinationid][$o],
                                                                        'id'=>$offerproducttableidarr[$combinationid][$o]);
                                        $offerproductidsarr[] = $offerproducttableidarr[$combinationid][$o];
                                    }
                                }else{
                                    if(!empty($offerpriceidarr[$combinationid][$o])){
                                        $insertofferproduct[] = array('offerid'=>$offerid,
                                                                    'offercombinationid'=>$offercombinationid,
                                                                    'productvariantid'=>$offerpriceidarr[$combinationid][$o],
                                                                    'quantity'=>$offerqtyarr[$combinationid][$o],
                                                                    'discounttype'=>$discounttype,
                                                                    'discountvalue'=>$discountvaluearr[$combinationid][$o]);
                                    }
                                }
                                
                            }
                            if(!empty($offerproductidsarr) && $offertype == 0){
                                $this->Offer_product->Delete(array("offerid"=>$offerid,"id NOT IN (".implode(",",$offerproductidsarr).")"=>null));
                            }
                        }
                    }
                    if(count($combinationidsarr) > 0 && $offertype == 0){
                        $this->Offer->_table = tbl_offercombination;
                        $this->Offer->Delete(array("offerid"=>$offerid,"id NOT IN (".implode(",",$combinationidsarr).")"=>null));
                    }
                }

                if($type==1){
                    $this->Offer->_table = tbl_offercombination;
                    $this->Offer->Delete(array("offerid"=>$offerid));
                    $this->Offer_purchased_product->Delete(array("offerid"=>$offerid));
                    $this->Offer_product->Delete(array("offerid"=>$offerid));
                }else if($type==3){
                    $this->Offer_product->Delete(array("offerid"=>$offerid));
                }
                
                
                if(!empty($updateofferpurchasedproduct)){
                    $this->Offer_purchased_product->edit_batch($updateofferpurchasedproduct,'id');
                }
                if(!empty($insertofferpurchasedproduct)){
                    $this->Offer_purchased_product->add_batch($insertofferpurchasedproduct);
                }
                if(!empty($updateofferproduct)){
                    $this->Offer_product->edit_batch($updateofferproduct,'id');
                }
                if(!empty($insertofferproduct)){
                    $this->Offer_product->add_batch($insertofferproduct);
                }
                
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Offer','Edit '.$offername.' offer.');
                }
                echo json_encode(array("error"=>"1")); //Offer updated successfully
                
            } else {
                echo json_encode(array("error"=>"2")); // Offer  name already added
            }
        }
    } 

    public function view_offer($offerid)
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Offer Details";
        $this->viewData['module'] = "offer/View_offer";
       
        $this->viewData['offerdata'] = $this->Offer->getOfferDetailsByID($offerid);
        if(empty($this->viewData['offerdata'])){
            redirect(ADMINFOLDER.'dashboard');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Offer','View '.$this->viewData['offerdata']['offerdata']['name'].' offer.');
        }
        // print_r($this->viewData['offerdata']); exit;
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("view_offer", "pages/view_offer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function viewofferdescription(){
        $PostData = $this->input->post();
        
        $offerid = $PostData['id'];
        $offerdata = $this->Offer->getOfferDataByID($offerid);
        echo json_encode(array('description'=>$offerdata['description']));
    }
    public function viewmemberlist(){
        $PostData = $this->input->post();
        
        $offerid = $PostData['id'];
        $channelid = $PostData['channelid'];


        $offerdata = $this->Offer->getMemberListByOfferChannelID($offerid,$channelid);
        echo json_encode($offerdata);
    }
    public function check_offer_use() {
        $PostData = $this->input->post();
        $count = 0;
        $ids = explode(",",$PostData['ids']);
        foreach($ids as $row){
           
           $this->readdb->select('id');
           $this->readdb->from(tbl_offer);
           $where = array("id IN (SELECT offerid FROM ".tbl_offerproduct." WHERE id IN (SELECT offerproductid FROM ".tbl_orderproducts." WHERE orderid IN (SELECT id FROM ".tbl_orders." WHERE approved!=1 AND status!=1 AND isdelete=0)) AND offerid='".$row."')"=>null);
           $this->readdb->where($where);
           $query = $this->readdb->get();
           if($query->num_rows() > 0){
             $count++;
           }
        }  
        echo $count;
   }
    public function delete_mul_offer() {
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);

        foreach ($ids as $row) {
            // get essay id
            $this->readdb->select('id');
            $this->readdb->from(tbl_offer);
            $where = array("id IN (SELECT offerid FROM ".tbl_offerproduct." WHERE id IN (SELECT offerproductid FROM ".tbl_orderproducts." WHERE orderid IN (SELECT id FROM ".tbl_orders." WHERE approved!=1 AND status!=1 AND isdelete=0)) AND offerid='".$row."')"=>null);
            $this->readdb->where($where);
            $query = $this->readdb->get();

            if($query->num_rows() == 0){
               
                $imagesdata = $this->Offer->getOfferImageDataByOfferID($row);
                if(!empty($imagesdata)){
                    foreach($imagesdata as $image){
                        unlinkfile('OFFERIMAGE', $image['filename'], OFFER_PATH);
                    }
                }
                $this->Offer->_table = tbl_offerimage;
                $this->Offer->Delete(array("offerid"=>$row));

                $this->Offer->_table = tbl_offermembermapping;
                $this->Offer->Delete(array("offerid"=>$row));

                $this->Offer->_table = tbl_offercombination;
                $this->Offer->Delete(array("offerid"=>$row));

                $this->load->model("Offer_purchased_product_model","Offer_purchased_product");
                $this->Offer_purchased_product->Delete(array("offerid"=>$row));

                $this->load->model("Offer_product_model","Offer_product");
                $this->Offer_product->Delete(array("offerid"=>$row));

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Offer->_table = tbl_offer;
                    $this->Offer->_where = array("id"=>$row);
                    $Offerdata = $this->Offer->getRecordsById();
            
                    $this->general_model->addActionLog(3,'Offer','Delete '.$Offerdata['name'].' offer.');
                }

                $this->Offer->_table = tbl_offer;
                $this->Offer->Delete(array("id"=>$row));  
            }        
        }
    }

    public function offer_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Offer->_where = array("id" => $PostData['id']);
        $this->Offer->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Offer->_where = array("id"=>$PostData['id']);
            $data = $this->Offer->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' offer.';
            
            $this->general_model->addActionLog(2,'Offer', $msg);
        }
        echo $PostData['id'];
    }
    
    function getofferdescriptionbyid(){
        $PostData = $this->input->post();
        $this->Offer->_fields = "name,description";
        $this->Offer->_where = "id=".$PostData['id'];
        $data = $this->Offer->getRecordsByID();
 
        echo json_encode(array('pagetitle'=>ucwords($data['title']),'description'=>$data['description']));
    }

    public function getofferproducts(){
        $PostData = $this->input->post();
        $productid = $PostData['productid'];
        $productvariantid = $PostData['productvariantid'];

        $memberid = $PostData['memberid'];
        $sellerid = 0;

        $json=array();
        $offerdata = $this->Offer->getOfferProductsInOrder($memberid,$productid,$productvariantid);
        //echo $this->readdb->last_query();exit;
        if(!empty($offerdata)){
            foreach($offerdata as $offer){
                $offerimages = $this->Offer->getOfferImages($offer['offerid']);
                $noofmemberusedoffer = $this->Offer->noOfMemberUsedOffer($offer['offerid']);
                $termscondition = "";
                if($offer['description']!=""){
                    $termscondition = "<a href='javascript:void(0)' data-content='".str_replace("'","",$offer['description'])."' data-original-title='Terms & Conditions' data-toggle='popover' data-placement='bottom'>*Terms & Conditions</a>";
                }
                $combinationarr=array();                
                $combinationdata = $this->Offer->getOfferCombinations($offer['offerid'],$productid,$productvariantid);
                if(!empty($combinationdata)){
                    foreach($combinationdata as $combination){

                        $purchaseproduct = $this->Offer_purchased_product->getPurchaseProduct($offer['offerid'],$combination['id']);
                        $offerproduct = $this->Offer_product->getOfferProducts($offer['offerid'],$combination['id'],$memberid,$sellerid);

                        $combinationarr[] = array(
                                                "id"=>$combination['id'],
                                                "multiplication"=>$combination['multiplication'],
                                                "purchaseproduct"=>$purchaseproduct,
                                                "offerproduct"=>$offerproduct,
                                            );
                    }
                }

                $json[] = array('offerid'=>$offer['offerid'],
                                'offername'=>$offer['offername'],
                                'minbillamount'=>$offer['minbillamount'],
                                'minimumpurchaseamount'=>$offer['minimumpurchaseamount'],
                                'maximumusage'=>$offer['maximumusage'],
                                'used'=>$offer['used'],
                                'noofmembersused'=>$offer['noofcustomerused'],
                                'noofmemberusedoffer'=>$noofmemberusedoffer,
                                'offertype'=>$offer['offertype'],
                                'termscondition'=>$termscondition,
                                'offerimage'=>$offer['offerimage'],
                                'offerimages'=>$offerimages,
                                'shortdescription'=>$offer['shortdescription'],
                                'combination'=>$combinationarr
                            );
            }
        }
        echo json_encode($json);
    }

    public function checkofferapply(){
        $PostData = $this->input->post();
        /* $memberid = $PostData['memberid'];
        $productid = $PostData['productid'];
        $productvariantid = $PostData['productvariantid'];
        $quantity = $PostData['quantity']; */
        $offerpurchasedproductid = $PostData['offerpurchasedproductid'];

        $offerdata = $this->Offer->verifyoffer($offerpurchasedproductid);
        
        echo json_encode($offerdata);
    }
    
    public function offerorderslisting() {
        
        $channeldata = $this->Channel->getChannelList();
        $this->load->model('Offer_orders_model', 'Offer_orders');
        $list = $this->Offer_orders->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $status = $datarow->status;
            $orderstatus = $approvestatus = '';
            
            if($status == 0){
                $orderstatus = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
            }else if($status == 1){
                $orderstatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';
            }else if($status == 2){
                $orderstatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }

            if($datarow->approved==1){
                $approvestatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approved</button>';
            }else if($datarow->approved==2){
                $approvestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</button>';
            }else{
                $approvestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Not Aprroved</button>';
            }

            $row[] = ++$counter;
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            $channellabel="";
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.$channellabel." ".ucwords($datarow->membername).' ('.$datarow->membercode.')</a>';
            $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
            $channellabel="";
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            if($datarow->sellerchannelid!=0){
                $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->sellermembername).'" target="_blank">'.$channellabel." ".ucwords($datarow->sellermembername).' ('.$datarow->sellermembercode.')</a>';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->id.'" title="'.viewpdf_title.'" target="_blank">'.$datarow->orderid.'</a>';
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = $orderstatus; 
            $row[] = $approvestatus;                  
            $row[] = numberFormat($datarow->netamount,2,',');
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Offer_orders->count_all(),
                        "recordsFiltered" => $this->Offer_orders->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function offerparticipantslisting() {
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $channeldata = $this->Channel->getChannelList();
        $this->load->model('Offer_participants_model', 'Offer_participants');
        $list = $this->Offer_participants->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $status = $datarow->status;
            $activestatus = '';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="javascript:void(0)" onclick="editoffernotes('.$datarow->id.')" title="'.edit_title.'" data-toggle="modal" data-target="#editnotesModal">'.edit_text.'</a>';
            }
            if($status == 0){
                $activestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Deactivate</button>';
            }else if($status == 1){
                $activestatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Activate</button>';
            }

            $row[] = ++$counter;
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            $channellabel="";
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.$channellabel." ".ucwords($datarow->membername).' ('.$datarow->membercode.')</a>';

            $row[] = "<b>Mo. : </b>".$datarow->mobile."<br><br><b>Email : </b>".$datarow->email; 
            
            $notes = "<div id='adminnotes".$datarow->id."' style='display:none;'>".$datarow->adminnotes."</div><div id='membernotes".$datarow->id."' style='display:none;'>".$datarow->membernotes."</div>";
            
            $row[] = $notes.'<button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#notesModal" onclick="viewnotes('.$datarow->id.',1)">View Notes</button>';
            $row[] = '<button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#notesModal" onclick="viewnotes('.$datarow->id.',2)">View Notes</button>';
            // $row[] = $datarow->adminnotes; 
            // $row[] = $datarow->membernotes; 
            $row[] = $activestatus; 
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Offer_participants->count_all(),
                        "recordsFiltered" => $this->Offer_participants->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function update_offer_notes()
    {
        $PostData = $this->input->post(); 
        $modifieddate  =  $this->general_model->getCurrentDateTime();  
        $modifiedby = $this->session->userdata(base_url().'ADMINID');     
        
        $offerparticipantsid= trim($PostData['offerparticipantsid']);
        $membernotes= trim($PostData['membernotes']);
        $adminnotes = trim($PostData['adminnotes']);   
     
        $updateData = array(
            'adminnotes' => $adminnotes,
            'membernotes' => $membernotes,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby);
        
        $this->Offer->_table = tbl_offerparticipants;
        $this->Offer->_where = array('id' => $offerparticipantsid);
        $Edit = $this->Offer->Edit($updateData);

        if($Edit){
            echo 1;
        } else {
            echo 0;
        }
    } 
}
?>