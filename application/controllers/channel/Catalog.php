<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Catalog extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Catalog');
        $this->load->model('Catalog_model', 'Catalog');
    }

    public function index() {
        $this->viewData['title'] = "Catalog";
        $this->viewData['module'] = "catalog/Catalog";
        $this->viewData['VIEW_STATUS'] = "1";
        
        if(CHANNELWISECATALOG==1){
            $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
            $this->load->model("Channel_model","Channel"); 
            $this->viewData['ChannelData'] = $this->Channel->getCurrentOrUpperChannelListByMember($MEMBERID,'');
        }

        $this->channel_headerlib->add_javascript("Catalog", "pages/catalog.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() {   

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Catalog->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            $channellabel = '';
            $membername = '';

            if($datarow->memberid == $MEMBERID && $datarow->type == 1){

                $countuniquecatalog = $this->Catalog->getUniqueCountForCatalog($datarow->id);
                $counttotalcatalog = $datarow->counttotalcatalog;
                $countuniquecatalog = $countuniquecatalog['count'];
            }else{
                $counttotalcatalog = 0;
                $countuniquecatalog = 0;
            }

            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($datarow->memberid == $MEMBERID && $datarow->type == 1){
                    $membername = $channellabel.ucwords($datarow->membername).' ('.$datarow->membercode.')';
                }else{
                    $membername = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" target="_blank" title="'.$datarow->membername.'">'.ucwords($datarow->membername).' ('.$datarow->membercode.')'."</a>";
                }
            }else{
                $membername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $actions .= ' <button class="'.view_class.'" data-toggle="modal" data-target="#myModal" title="'.view_title.'" onclick="getcontent('.$datarow->id.')">'.view_text.'</button>';
            
            //if(CHANNELWISECATALOG==1 && $datarow->memberid == $MEMBERID && $datarow->type == 1 && ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
            if(CHANNELWISECATALOG==1 && $datarow->memberid == $MEMBERID && $datarow->type == 1 && ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1){
                if(in_array($rollid, $edit)) {
                    $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'catalog/catalog-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                    
                    if($datarow->status==1){
                        $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'catalog/catalog-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                    else{
                        $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'catalog/catalog-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                    }
            
                }
            
                // $actions .= '<a class="'.DOWNLOAD_CLASS.'" href="'.CHANNEL_URL.'customer/downloadinvoice/'.$datarow->id.'" title="'.DOWNLOAD_TITLE.'" >'.DOWNLOAD_TEXT.'</a>';

                if(in_array($rollid, $delete)) {
                    $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.CHANNEL_URL.'catalog/check-catalog-use","Catalog","'.CHANNEL_URL.'catalog/delete-mul-catalog","catalog") >'.delete_text.'</a>';

                    $checkbox = ' <div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
                }
            }

            $description  = '<div style="display:none" id="detail'.$datarow->id.'">'.stripslashes($datarow->description).'</div>';
            if($datarow->description != ""){
                $pokemon_doc->loadHTML($datarow->description);
                $pokemon_xpath = new DOMXPath($pokemon_doc);
                $href = $pokemon_xpath->evaluate('//@href'); 
                $iframe = $pokemon_xpath->evaluate('//iframe');
                $table = $pokemon_xpath->evaluate('//table');
                $img = $pokemon_xpath->evaluate('//img');

                if($href->length > 0 || $iframe->length > 0 || $table->length > 0 || $img->length > 0){
                    $description.= "<a href='#' onclick='viewmore(\"detail".$datarow->id."\", \"catalog\")'>[View More]</a>";
                } else {
                    $datarow->description = strip_tags($datarow->description);
                    $description .= strlen($datarow->description) > 100 ? substr($datarow->description, 0, 100)."<a href='#' onclick='viewmore(\"detail".$datarow->id."\", \"catalog \");'>...[view more]</a>": $datarow->description;
                } 
            }

            $row[] = ++$counter;
            $row[] = $membername;
            $row[] = ucfirst($datarow->name);
            $row[] = "<span class='pull-right'>".$counttotalcatalog."</span>";
            $row[] = "<span class='pull-right'>".$countuniquecatalog."</span>";
            $row[] = $this->general_model->displaydatetime($datarow->createddate);   
            $row[] = $actions;
            if(CHANNELWISECATALOG==1){ 
                $row[] = $checkbox;
            }
            $data[] = $row;

        }
        libxml_use_internal_errors($internalErrors);

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Catalog->count_all(),
                        "recordsFiltered" => $this->Catalog->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function add_catalog() {

        if(CHANNELWISECATALOG==0){
            redirect("Pagenotfound");
        }
        //$this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Catalog";
        $this->viewData['module'] = "catalog/Add_catalog";   
        $this->viewData['VIEW_STATUS'] = "0";            
        $this->channel_headerlib->add_javascript("Catalog", "pages/add_catalog.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function catalog_add() {
        if(CHANNELWISECATALOG==0){
            redirect("Pagenotfound");
        }
        $PostData = $this->input->post(); 
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $catalogname = isset($PostData['catalogname']) ? trim($PostData['catalogname']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';      
        $createddate  =  $this->general_model->getCurrentDateTime();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');       
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $notification ='1';   
        $status = $PostData['status'];
      
         if($_FILES["fileimage"]['name'] != ''){
            if($_FILES["fileimage"]['size'] != '' && $_FILES["fileimage"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG){
                echo 8;	// FILE SIZE IS LARGE
                exit;
            }
            $image = uploadFile('fileimage','CATALOGIMAGE_PATH', CATALOG_PATH, '*', '', 1, CATALOG_LOCAL_PATH);
            if($image !== 0){
                if($image==2){
                    echo 3;//file not uploaded
                    exit;
                }
            } else {
                echo 4; //INVALID IMAGE TYPE
                exit;
            }   
        } else {
            $image = '';
        }
       
        if($_FILES['filepdf']['name'] != ''){
            if($_FILES["filepdf"]['size'] != '' && $_FILES["filepdf"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG){
                echo 8;	// FILE SIZE IS LARGE
                exit;
            }
            $pdf = uploadFile('filepdf', 'CATALOGPDF_PATH', CATALOG_PATH);         
             if($pdf !== 0){
                if($pdf==2){
                    echo 5;//file not uploaded
                    exit;
                }
            } else {
                echo 6; //INVALID PDF TYPE
                exit;
            }   
        } else {
            $pdf = '';
        }
        $this->Catalog->_where=array("name"=>$catalogname,"addedby"=>$addedby);
        $sqlname = $this->Catalog->getRecordsByID();
        if(empty($sqlname)){          
            $InsertData = array(
                                'name' => $catalogname,
                                'description'=>$description,
                                'image'=>$image,
                                'pdffile'=>$pdf,
                                'type' => 1,                    
                                'createddate' => $createddate,
                                'modifieddate' => $modifieddate, 
                                'addedby'=>$addedby,
                                'modifiedby'=>$modifiedby,
                                'status' => $status);
           
            $insertid = $this->Catalog->add($InsertData);
            
            if($insertid != 0){
                echo 1; //catalog  inserted successfully


                if($notification == 1)
                { 
                    $this->load->model('Member_model', 'Member');
                    $this->Member->_fields = "GROUP_CONCAT(id) as id";
                    $this->Member->_where = array("id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.")"=>null);
                    $member = $this->Member->getRecordsById();
                    
                    if(!empty($member)){
                        $this->load->model('Fcm_model','Fcm');
                        $fcmquery = $this->Fcm->getFcmDataByMemberId($member['id']);
                                    
                        if(!empty($fcmquery)){
                            $insertData = array();
                            foreach ($fcmquery as $fcmrow){   
                                $fcmarray=array();                           
                                $type = "1"; // catalog =1 , news =2 , product =3
                                $msg = $catalogname." has Catalog Add.";                          
                                $pushdescription = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$insertid.'"}';
                                $fcmarray[] = $fcmrow['fcm'];
                                $memberid = $fcmrow['memberid'];
                   
                                $this->Fcm->sendFcmNotification($type,$pushdescription,$memberid,$fcmarray,0,$fcmrow['devicetype']);
    
                                $insertData[] = array(
                                    'type'=>$type,
                                    'message' => $pushdescription,
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
                                echo "notification has sent";//send notification
                            }else{
                                echo "notification has not sent";// not send notification
                            }
                        }
                        else{
                                echo "notification has not sent";// not send notification
                        }    
                    }
                }else{
                    echo 7;//not set notification
                }

            } else {
                echo 0; // catalog not inserted 
              }
        } else {
            echo 2; // catalog name already added
        }
    }

    public function update_catalog() {
        if(CHANNELWISECATALOG==0){
            redirect("Pagenotfound");
        }
        //$this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $catalogid = isset($PostData['catalogid']) ?  trim($PostData['catalogid']) : '';
        $catalogname = isset($PostData['catalogname']) ? trim($PostData['catalogname']) : '';
        $description = isset($PostData['description']) ?  trim($PostData['description']) : '';          
        $status = $PostData['status'];
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        $oldfileimage =  isset($PostData['oldfileimage']) ? trim($PostData['oldfileimage']) : '';
        $oldfilepdf = isset($PostData['oldfilepdf']) ? trim($PostData['oldfilepdf']) : '';
        $status = $PostData['status'];
        if($_FILES["fileimage"]['name'] != ''){
            if($_FILES["fileimage"]['size'] != '' && $_FILES["fileimage"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG){
                echo 7;	// FILE SIZE IS LARGE
                exit;
            }
            $FileNM1 = reuploadfile('fileimage', 'CATALOGIMAGE_PATH', $oldfileimage, CATALOG_PATH, '*', '', 1, CATALOG_LOCAL_PATH);
        
            if($FileNM1 !== 0){ 
                if($FileNM1==2){
                    echo 3;//file not uploaded
                    exit;
                }
            }else{
                echo 4; //INVALID IMAGE TYPE
                exit;
            }
        }else{
            $FileNM1 =  $oldfileimage;
        }
      if($_FILES['filepdf']['name'] != ''){
            if($_FILES["filepdf"]['size'] != '' && $_FILES["filepdf"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG){
                echo 7;	// FILE SIZE IS LARGE
                exit;
            }
            $FileNM2 = reuploadFile('filepdf', 'CATALOGPDF_PATH', $oldfilepdf, CATALOG_PATH);
             if($FileNM2 !== 0){
                if($FileNM2==2){
                    echo 5;//file not uploaded
                    exit;
                }
                
            } else {
                echo 6; //INVALID PDF TYPE
                exit;
            }   
        } else {
            $FileNM2 = $oldfilepdf;
        }
        $this->Catalog->_where = "name = '".$catalogname."' AND addedby = ".$modifiedby." AND id <> ".$catalogid;
        $sqlname = $this->Catalog->getRecordsByID();
      

      if(empty($sqlname)){
            $updateData = array(
                                'id' => $catalogid,
                                'name' => $catalogname,
                                'description' => $description,
                                'type' => 1,
                                'image' => $FileNM1,
                                'pdffile' =>$FileNM2,
                                'status' =>$status,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);
       
            $this->Catalog->_where = array('id' => $catalogid);
            $updateid = $this->Catalog->Edit($updateData);
            if($updateid != 0){
                echo 1; // catalog update successfully
            } else {
                echo 0; // catalog not updated
            }
         }else{
            echo 2;//catalog already updated
         }
    }

    public function catalog_edit($id) {
        if(CHANNELWISECATALOG==0){
            redirect("Pagenotfound");
        }
       //$this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Catalog";
        $this->viewData['module'] = "catalog/Add_catalog";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";  
        $memberid = $this->session->userdata(base_url().'MEMBERID'); 
        $this->Catalog->_where=array("id"=>$id,"addedby"=>$memberid,"type"=>'1');     
        $this->viewData['catalogdata'] =  $this->Catalog->getRecordsByID(); 
        if(count($this->viewData['catalogdata'])>0){
            $this->channel_headerlib->add_javascript("Catalog", "pages/add_catalog.js");
            $this->load->view(CHANNELFOLDER.'template',$this->viewData);
        }else{
            redirect(CHANNEL_URL);
        }
    }

    public function delete_mul_catalog() {
        
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        foreach ($ids as $row) {

            $this->Catalog->_fields = 'id,image,pdffile';
            $this->Catalog->_where = array('id'=>$row);
            $catalogdata = $this->Catalog->getRecordsByID();
            
            if(count($catalogdata)>0){
                
                unlinkfile('CATALOG_PATH', $catalogdata['image'], CATALOG_PATH);
                unlinkfile('CATALOG_PATH', $catalogdata['pdffile'], CATALOG_PATH);
                            
                $this->Catalog->Delete(array('id'=>$row,"addedby"=>$memberid,"type"=>'1'));
            }
           
        }
    }

    public function check_catalog_use() {
         $PostData = $this->input->post();
         $count = 0;
        echo $count;
    }

  public function catalog_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $this->Catalog->_table = tbl_catalog;
        $this->Catalog->_where = array("id" => $PostData['id'],"addedby"=>$memberid,"type"=>'1');
        $this->Catalog->Edit($updatedata);

        echo $PostData['id'];
    }
    
    function getcontentbyid(){
        $PostData = $this->input->post();

        $this->Catalog->_where=array("id"=>$PostData['id']);     
        $data =  $this->Catalog->getRecordsByID();

        echo json_encode(array('id'=>$data['id'],'catalogname'=> $data['name'],'catalogdescription'=> $data['description'],'catalogimage'=> CATALOG_IMAGE.$data['image'],'catalogpdffile'=> CATALOG_IMAGE.$data['pdffile'],'catalogcreateddate'=> $this->general_model->displaydatetime($data['createddate']),'catalogstatus'=> $data['status']));
    }
}