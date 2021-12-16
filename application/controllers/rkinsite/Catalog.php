<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Catalog extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Catalog_model', 'Catalog');
    
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Catalog');
    }

   
    public function index() {
        $this->viewData['title'] = "Catalog";
        $this->viewData['module'] = "catalog/Catalog";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        $sessionarr = (isset($this->session->userdata('SESSION_FILTERS')['Catalog']))?$this->session->userdata('SESSION_FILTERS')['Catalog']:"";

        if(!is_null($sessionarr) && !empty($sessionarr)){

            $this->viewData['panelcollapsed'] = (isset($sessionarr['panelcollapsed']))?$sessionarr['panelcollapsed']:"0";
            $this->viewData['startdate'] = (isset($sessionarr['startdate']))?$sessionarr['startdate']:"";
            $this->viewData['enddate'] = (isset($sessionarr['enddate']))?$sessionarr['enddate']:"";
            $this->viewData['ChannelId'] = (isset($sessionarr['channelid']))?$sessionarr['channelid']:"0";
            $this->viewData['MemberId'] = (isset($sessionarr['memberid']))?$sessionarr['memberid']:"0";
        }

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Catalogue','View Catalogue.');
		}
		
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Catalog", "pages/catalog.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {   

        $this->general_model->saveModuleWiseFiltersOnSession('Catalog');
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Catalog->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            $channellabel = '';
            $membername = '';
           
            $countuniquecatalog = $this->Catalog->getUniqueCountForCatalog($datarow->id);
            
            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $membername = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" target="_blank" title="'.$datarow->membername.'">'.$datarow->membername.' ('.$datarow->membercode.')'."</a>";
            }else{
                $membername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $actions .= '<button class="'.view_class.'" data-toggle="modal" data-target="#myModal" title="'.view_title.'" onclick="getcontent('.$datarow->id.')">'.view_text.'</button>';
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'catalog/catalog-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                

                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'catalog/catalog-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'catalog/catalog-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
          
            }
            // $actions .= '<a class="'.DOWNLOAD_CLASS.'" href="'.ADMIN_URL.'customer/downloadinvoice/'.$datarow->id.'" title="'.DOWNLOAD_TITLE.'" >'.DOWNLOAD_TEXT.'</a>';

            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'catalog/check-catalog-use","Catalog","'.ADMIN_URL.'catalog/delete-mul-catalog","catalog") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';

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
                    $description .= "<a href='#' onclick='viewmore(\"detail".$datarow->id."\", \"catalog\")'>[View More]</a>";
                } else {
                    $datarow->description = strip_tags($datarow->description);
                    $description .= strlen($datarow->description) > 100 ? substr($datarow->description, 0, 100)."<a href='#' onclick='viewmore(\"detail".$datarow->id."\", \"catalog \");'>...[view more]</a>": $datarow->description;
                } 
            }

            
            $row[] = ++$counter;
            $row[] = $membername;
            $row[] = ucfirst($datarow->name);
            $row[] = "<span class='pull-right'>".$datarow->counttotalcatalog."</span>";
            $row[] = "<span class='pull-right'>".$countuniquecatalog['count']."</span>";
            $row[] = $this->general_model->displaydatetime($datarow->createddate);   
            $row[] = $actions;
            $row[] = $checkbox;
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

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Catalog');      
        $this->viewData['title'] = "Add Catalog";
        $this->viewData['module'] = "catalog/Add_catalog";   
        $this->viewData['VIEW_STATUS'] = "0";        
        
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        $this->admin_headerlib->add_javascript("Catalog", "pages/add_catalog.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function catalog_add() {
        
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post(); 
        $catalogname = isset($PostData['catalogname']) ? trim($PostData['catalogname']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';      
        $createddate  =  $this->general_model->getCurrentDateTime();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');       
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $notification ='1';   
        $status = $PostData['status'];
      
        if($_FILES["fileimage"]['name'] != ''){
            if($_FILES["fileimage"]['size'] != '' && $_FILES["fileimage"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG){
                echo 8;	// FILE SIZE IS LARGE
                exit;
            }
            $image = uploadFile('fileimage','CATALOGIMAGE_PATH', CATALOG_PATH, '*', "", 1, CATALOG_LOCAL_PATH);
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
        $this->Catalog->_where=array("name"=>$catalogname);
        $sqlname = $this->Catalog->getRecordsByID();
        if(empty($sqlname)){          
            $InsertData = array(
                                'name' => $catalogname,
                                'description'=>$description,
                                'image'=>$image,
                                'pdffile'=>$pdf,
                                'createddate' => $createddate,                              
                                'modifieddate' => $modifieddate, 
                                'addedby'=>$addedby,
                                'modifiedby'=>$modifiedby,
                                'status' => $status);
           
            $CatalogID = $this->Catalog->add($InsertData);
            
            if($CatalogID != 0){
                echo 1; //catalog  inserted successfully

                if(CHANNELWISECATALOG==1){

                    $channelid = isset($PostData['channelid']) ? $PostData['channelid'] : '';
                    $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';

                    $memberid_arr=array();
                    if(!empty($memberidarr)){
                        foreach($memberidarr as $memberid){
                            
                            $memberid_arr[] = array("channelid"=>$channelid,"memberid"=>$memberid,'catalogid'=>$CatalogID);
                        }
                    }
                    if(count($memberid_arr)>0){
                        $this->Catalog->_table = tbl_catalogchannelmapping;
                        $this->Catalog->add_batch($memberid_arr);
                    }
                }
                if($notification == 1)
                { 
                      
                    $this->load->model('Fcm_model','Fcm');
                    if(CHANNELWISECATALOG==1){
                        $this->load->model('Member_model', 'Member');
                        //$member = $this->Member->getMemberByFirstChannel();
                        $fcmquery = $this->Fcm->getFcmDataByMemberId(implode(",", $memberidarr));
                    }else{
                        $this->Fcm->_fields='*';
                        $fcmquery = $this->Fcm->getRecordByID();
                    }
                                
                    if(!empty($fcmquery)){
                        $insertData = array();
                        foreach ($fcmquery as $fcmrow){   
                            $fcmarray=array();                           
                            $type = "1"; // catalog =1 , news =2 , product =3
                            $msg = $catalogname." has Catalog Add.";                          
                            $pushdescription = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$CatalogID.'"}';
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
                            //echo "notification has sent";//send notification
                        }else{
                            //echo "notification has not sent";// not send notification
                        }
                    }
                }else{
                    echo 7;//not set notification
                }
                    
                if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Catalogue','Add new '.$catalogname.' catalogue.');
				}
            } else {
                echo 0; // catalog not inserted 
            }
        } else {
            echo 2; // catalog name already added
        }
    }

    public function catalog_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Catalog";
        $this->viewData['module'] = "catalog/Add_catalog";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1"; 

        $this->Catalog->_fields = "*,(SELECT channelid FROM ".tbl_catalogchannelmapping." WHERE catalogid=".tbl_catalog.".id LIMIT 1) as channelid";
        $this->Catalog->_where=array("id"=>$id);     
        $this->viewData['catalogdata'] =  $this->Catalog->getRecordsByID(); 

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

        $catalogchannelmapping = $this->Catalog->getCatalogChannelMappingDataByCatalogID($id);
        
        $this->viewData['memberidarr'] = array();
        $memberidarr=array();
        foreach($catalogchannelmapping as $ccm){
            $this->viewData['memberidarr'][]=$ccm['memberid'];
        }
        //echo "<pre>"; print_r($catalogchannelmapping); exit;
        $this->admin_headerlib->add_javascript("Catalog", "pages/add_catalog.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_catalog() {
       
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $catalogid = isset($PostData['catalogid']) ?  trim($PostData['catalogid']) : '';
        $catalogname = isset($PostData['catalogname']) ? trim($PostData['catalogname']) : '';
        $description = isset($PostData['description']) ?  trim($PostData['description']) : '';          
        $status = $PostData['status'];
        $modifiedby = $this->session->userdata(base_url().'ADMINUSERTYPE'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        $oldfileimage =  isset($PostData['oldfileimage']) ? trim($PostData['oldfileimage']) : '';
        $oldfilepdf = isset($PostData['oldfilepdf']) ? trim($PostData['oldfilepdf']) : '';
        $status = $PostData['status'];
        if($_FILES["fileimage"]['name'] != ''){
            if($_FILES["fileimage"]['size'] != '' && $_FILES["fileimage"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG){
                echo 7;	// FILE SIZE IS LARGE
                exit;
            }
            $FileNM1 = reuploadfile('fileimage', 'CATALOGIMAGE_PATH', $oldfileimage, CATALOG_PATH, '*', "", 1, CATALOG_LOCAL_PATH);
        
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
            $FileNM2 = reuploadFile('filepdf', 'CATALOGPDF_PATH',$oldfilepdf, CATALOG_PATH);         
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
        $this->Catalog->_where = "name = '".$catalogname."' AND id <> ".$catalogid;
        $sqlname = $this->Catalog->getRecordsByID();
      

      if(empty($sqlname)){
            $updateData = array(
                                'id' => $catalogid,
                                'name' => $catalogname,
                                'description' => $description,
                                'image' => $FileNM1,
                                'pdffile' =>$FileNM2,
                                'status' =>$status,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);
       
            $this->Catalog->_where = array('id' => $catalogid);
            $updateid = $this->Catalog->Edit($updateData);
            if($updateid != 0){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Catalogue','Edit '.$catalogname.' catalogue.');
                }
                echo 1; // catalog update successfully
            } else {
                echo 0; // catalog not updated
            }
         }else{
            echo 2;//catalog already updated
         }
    }

    public function delete_mul_catalog() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);

        foreach ($ids as $row) {

            $this->Catalog->_fields = 'id,name,image,pdffile';
            $this->Catalog->_where = array('id'=>$row);
            $catalogdata = $this->Catalog->getRecordsByID();
            if(count($catalogdata)>0){
                unlinkfile('CATALOG_PATH', $catalogdata['image'], CATALOG_PATH);
                unlinkfile('CATALOG_PATH', $catalogdata['pdffile'], CATALOG_PATH);
                
                if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(3,'Catalogue','Delete '.$catalogdata['name'].' catalogue.');
				}
                $this->Catalog->Delete(array('id'=>$row));
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
        $this->Catalog->_table = tbl_catalog;
        $this->Catalog->_where = array("id" => $PostData['id']);
        $this->Catalog->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Catalog->_where = array("id"=>$PostData['id']);
            $data = $this->Catalog->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' catalogue.';
            
            $this->general_model->addActionLog(2,'Catalogue', $msg);
        }
        echo $PostData['id'];
    }
    
    function getcontentbyid(){
        $PostData = $this->input->post();

        $this->Catalog->_where=array("id"=>$PostData['id']);     
        $data =  $this->Catalog->getRecordsByID();

        echo json_encode(array('id'=>$data['id'],'catalogname'=> $data['name'],'catalogdescription'=> $data['description'],'catalogimage'=> CATALOG_IMAGE.$data['image'],'catalogpdffile'=> CATALOG_IMAGE.$data['pdffile'],'catalogcreateddate'=> $this->general_model->displaydatetime($data['createddate']),'catalogstatus'=> $data['status']));
    }

    public function savecollapse(){
        $PostData = $this->input->post();
        $panelcollapsed = $this->general_model->saveModuleWiseFiltersOnSession('Catalog','collapse');
    
        echo $panelcollapsed;
    }
}