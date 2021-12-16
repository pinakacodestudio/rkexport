<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('News_model', 'News');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'News');
        $this->load->model('News_category_model', 'News_category');
    }
    
    public function index() {
        $this->viewData['title'] = "News";
        $this->viewData['module'] = "news/News";
        $this->viewData['VIEW_STATUS'] = "1";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'News','View news.');
        }

        $this->admin_headerlib->add_javascript("News", "pages/news.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
     public function listing() {   
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->News->get_datatables();   
        // echo $this->db->last_query();exit();   
        $data = array();        
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
      

        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';

            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'news/edit-news/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){

                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'news/news-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'news/news-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","News","'.ADMIN_URL.'news/delete-mul-news","news") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
           
            if($datarow->image!=''){
                $image = '<img src="'.NEWS.$datarow->image.'" class="thumbwidth">';
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
                    $description .= "<a data-toggle='modal' data-target='#myModal' onclick='viewmore(".$datarow->id.")'>[View More]</a>";
                } else {
                    $datarow->description = strip_tags($datarow->description);
                    $description .= strlen($datarow->description) > 100 ? substr(ucfirst($datarow->description), 0, 100)."<a data-toggle='modal' data-target='#myModal' onclick='viewmore(".$datarow->id.");'>...[view more]</a>": ucfirst($datarow->description);
                } 
            }
           
            $row[] = ++$counter;
            $row[] = ($datarow->brand!="")?$datarow->brand:"-";       
            $row[] = ucwords($datarow->title);
            $row[] = $image;
            $row[] = $description;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);        
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        libxml_use_internal_errors($internalErrors);

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->News->count_all(),
                        "recordsFiltered" => $this->News->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
   
    public function add_news() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'News');      
        $this->viewData['title'] = "Add News";
        $this->viewData['module'] = "news/Add_news";   
        $this->viewData['VIEW_STATUS'] = "0";  
        $this->load->model('News_category_model', 'News_category');
        $this->viewData['newscategorydata'] = $this->News_category->getActiveNewsCategory();          

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

        $this->load->model("Brand_model","Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();
      
        
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("news", "pages/add_news.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function news_add() {
         
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post(); 
        $newsname = isset($PostData['newsname']) ? trim($PostData['newsname']) : '';
        $link = isset($PostData['link']) ? trim($PostData['link']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';      
        $createddate  =  $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');       
        $status = $PostData['status'];   
        $channelidarr = isset($PostData['channelid']) ? $PostData['channelid'] : '';
        $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';
        $newscategoryid = $PostData['newscategoryid'];
        $brandid= trim($PostData['brandid']);
        $metatitle = $PostData['metatitle'];
        $metadescription = $PostData['metadescription'];
        $metakeywords = $PostData['metakeywords'];
        $forwebsite = (isset($PostData['forwebsite']))?1:0;
		$forapp = (isset($PostData['forapp']))?1:0;
        
        
        $notification ='1';   
        if($_FILES["image"]['name'] != ''){
            if($_FILES["image"]['size'] != '' && $_FILES["image"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                echo json_encode($json);
                exit;
            }
            $image = uploadFile('image', 'NEWS_PATH', NEWS_PATH, '*', "", 1, NEWS_LOCAL_PATH);
            if($image !== 0){
                if($image==2){
                    $json = array('error'=>3);	// IMAGE NOT UPLOADED
                    echo json_encode($json);
                    exit;
                }
            } else {
                $json = array('error'=>4); //INVALID IMAGE TYPE
                echo json_encode($json);
                exit;
            }   
        } else {
            $image = '';
        }
        

        $this->News->_where=array("title"=>$newsname);       
        $sqlname = $this->News->getRecordsByID();
        if(empty($sqlname)){          
            $InsertData = array(
                        'title' => $newsname,
                        'link'=>$link,
                        "brandid"=>$brandid,
                        'description'=>$description, 
                        'newscategoryid'=>$newscategoryid,  
                        "image"=>$image,
                        'forwebsite'=>$forwebsite,
                        'forapp'=>$forapp,
                        'metatitle'=>$metatitle,   
                        "metakeywords"=>$metakeywords,
                        'metadescription'=>$metadescription,
                        'type' => 0,    
                        'createddate' => $createddate,                    
                        'modifieddate' => $createddate, 
                        'addedby'=>$addedby,
                        'modifiedby'=>$addedby,
                        'status' => $status);
           
            $insertid = $this->News->add($InsertData);
            
            if($insertid != 1){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'News','Add new '.$newsname.' news.');
                }
        
                echo 1;//NEWS inserted successfully
                $memberid_arr=array();
                if(!empty($memberidarr)){
                    foreach($memberidarr as $memberid){
                        
                        $this->load->model('Member_model', 'Member');
                        $this->Member->_fields = "channelid";
                        $this->Member->_where = array("id"=>$memberid);
                        $memberdata = $this->Member->getRecordsById();
                        
                        $memberid_arr[] = array("channelid"=>$memberdata['channelid'],"memberid"=>$memberid,'newsid'=>$insertid);
                    }
                }
                if(count($memberid_arr)>0){
                    $this->News->_table = tbl_newschannelmapping;
                    $this->News->add_batch($memberid_arr);
                }
                $insertData = array();
                
                if(!empty($memberidarr)){
                    if($forapp == 1){    
                      
                        $this->load->model('Fcm_model','Fcm');
                        $fcmquery = $this->Fcm->getFcmDataByMemberId(implode(",",$memberidarr));
                       
                        if(!empty($fcmquery)){
                            $notificationdata=array();
                            foreach ($fcmquery as $fcmrow){   
                                $fcmarray=array();                       
                                $type = "2";// catalog =1 , news =2 , product =3
                                $msg = ucwords($newsname)." has News Add.";    
                                $memberid = $fcmrow['memberid'];                     
                                $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$insertid.'"}';
                                $fcmarray[] = $fcmrow['fcm'];
                                $insert = 0;
                                
                                //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);
                                $this->Fcm->sendFcmNotification($type,$pushMessage,$memberid,$fcmarray,$insert,$fcmrow['devicetype']);

                                $insertData[] = array(
                                    'type'=>$type,
                                    'message' => $pushMessage,
                                    'memberid'=>$memberid, 
                                    'isread'=>0,                        
                                    'createddate' => $createddate,               
                                    'addedby'=>$addedby
                                    );    
                            }
                        }  
                    }
                    
                    if(!empty($insertData)){
                        $this->load->model('Notification_model','Notification');
                        $this->Notification->_table = tbl_notification;
        
                        $this->Notification->add_batch($insertData);
                        echo 7;//send notification
                    }else{
                        echo 9;//not set notification
                    }
                }
            } else {
                echo 0; // News not inserted 
            }

                
                       
           
        } else {
            echo 2; // News name already added
        }
    }

    public function edit_news($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'News');      
        $this->viewData['title'] = "Edit News";
        $this->viewData['module'] = "news/Add_news";
        $this->viewData['VIEW_STATUS'] = "1";   
        $this->viewData['action'] = "1";  

        $this->News->_where = array('id' => $id);
        $this->viewData['newsdata'] = $this->News->getRecordsByID();    

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

        $this->load->model("Brand_model","Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();
        
        $this->load->model('News_category_model', 'News_category');
        $this->viewData['newscategorydata'] = $this->News_category->getActiveNewsCategory();
        $this->viewData['newsdata'] = $this->News->getNewsDataByID($id);         


        $newschannelmapping = $this->News->getNewsChannelMappingDataByNewsID($id);
        
        $this->viewData['channelidarr'] = array();
        $channelidarr=array();
        foreach($newschannelmapping as $ncm){
            
            if (!in_array($ncm['channelid'], $channelidarr)){
                $channelidarr[]=$ncm['channelid'];
            }
            $this->viewData['memberidarr'][]=$ncm['memberid'];
        }
        $this->viewData['channelidarr']=$channelidarr;
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("News", "pages/add_news.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    } 
    
    public function news_update()
    {
        $PostData = $this->input->post(); 
        $modifieddate  =  $this->general_model->getCurrentDateTime();  
        $modifiedby = $this->session->userdata(base_url().'ADMINID');     
        
        $newsid= trim($PostData['newsid']);
        $newsname = isset($PostData['newsname']) ? trim($PostData['newsname']) : '';
        $link = isset($PostData['link']) ? trim($PostData['link']) : '';
        $channelidarr = $PostData['channelid'];
        $memberidarr = $PostData['memberid'];
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';  
        $newscategoryid = $PostData['newscategoryid'];
        $metatitle = $PostData['metatitle'];
        $metadescription = $PostData['metadescription'];
        $metakeywords = $PostData['metakeywords'];    
        $status = $PostData['status'];   
        $brandid= trim($PostData['brandid']);
        $forwebsite = (isset($PostData['forwebsite']))?1:0;
		$forapp = (isset($PostData['forapp']))?1:0;


       
        $Count = $this->News->CountRecords();
       
        $this->News->_where = "title= '".$newsname."' AND id <> ".$newsid;
        $sqlname = $this->News->getRecordsByID();

        $oldnewsimage = trim($PostData['oldnewsimage']);
        $removeoldImage = trim($PostData['removeoldImage']);

        if($_FILES["image"]['name'] != ''){

            $image = reuploadfile('image', 'NEWS_PATH', $oldnewsimage,NEWS_PATH ,"jpeg|png|jpg|JPEG|PNG|JPG", '', 1, NEWS_LOCAL_PATH);
            if($image !== 0){	
                if ($image == 2) {
                    echo 3;//STAFF IMAGE NOT UPLOADED
                    exit;
				}
			}else{
				echo 4;//invalid image type
				exit;
			}	
		}else if($_FILES["image"]['name'] == '' && $oldnewsimage !='' && $removeoldImage=='1'){
			unlinkfile('NEWS_PATH', $oldnewsimage, NEWS_PATH);
			$image = '';
		}else if($_FILES["image"]['name'] == '' && $oldnewsimage ==''){
			$image = '';
		}else{
			$image = $oldnewsimage;
		}

        if(empty($sqlname)){          
            $updateData = array(
                            'title' => $newsname,
                            'link' => $link,
                            "brandid"=>$brandid,
                            'description'=>$description, 
                            "newscategoryid" => $newscategoryid,    
                            "image"=>$image,
                            'forwebsite'=>$forwebsite,
                            'forapp'=>$forapp,
                            'metatitle'=>$metatitle,   
                            "metakeywords"=>$metakeywords,
                            'metadescription'=>$metadescription,          
                            'status'=>$status,                  
                            'modifieddate' => $modifieddate,
                            'modifiedby'=>$modifiedby
                          );
           
            $this->News->_where = array('id' => $newsid);
            $updateid = $this->News->Edit($updateData);
            
            if($updateid != 0){
                $this->load->model('Member_model', 'Member');
                $deletearr=array();

                $oldchannelid = (!empty($PostData['oldchannelid']))?explode(",",$PostData['oldchannelid']):'';
                $oldmemberid = (!empty($PostData['oldmemberid']))?explode(",",$PostData['oldmemberid']):'';
               
                if(!empty($oldmemberid)){
                    $deletearr = array_diff($oldmemberid,$memberidarr);
                }
                if(!empty($deletearr)){
                    $this->News->_table = tbl_newschannelmapping;
                    $this->News->Delete(array("memberid IN (".implode(",",$deletearr).")"=>null,"newsid"=>$newsid));
                }
                
                $insertdata = $updatedata = array();
                $this->News->_table = tbl_newschannelmapping;
                if(!empty($memberidarr)){
                    foreach($memberidarr as $memberid){
                        
                        $this->Member->_fields = "channelid";
                        $this->Member->_where = array("id"=>$memberid);
                        $memberdata = $this->Member->getRecordsById();
                        
                        $this->News->_fields = "id";
                        $this->News->_where = ("channelid=".$memberdata['channelid']." AND memberid=".$memberid." AND newsid=".$newsid);
                        $newsmappingdata = $this->News->getRecordsByID();
                        
                        if(!empty($newsmappingdata)){
                            $updatedata[]=array("channelid"=>$memberdata['channelid'],
                                                "memberid"=>$memberid,
                                                "newsid"=>$newsid,
                                                "id"=>$newsmappingdata['id']
                                            );
                        }else{
                            
                            $insertdata[]=array("channelid"=>$memberdata['channelid'],
                                                "memberid"=>$memberid,
                                                "newsid"=>$newsid
                                            );
                        }
                    }
                }
                
                if(!empty($insertdata)){
                    $this->News->_table = tbl_newschannelmapping;
                    $this->News->add_batch($insertdata);
                }
                if(!empty($updatedata)){
                    $this->News->_table = tbl_newschannelmapping;
                    $this->News->edit_batch($updatedata, "id");
                }
                
                /* $oldchannelid=array();
               
                if(isset($PostData['oldchannelid']) && $PostData['oldchannelid']!=""){
                    $oldchannelid = explode(",",$PostData['oldchannelid']);
                }
                $delete_arr=array();
                $add_arr=array();
                if(isset($PostData['channelid'])){
                    $delete_arr = array_diff($oldchannelid,$PostData['channelid']);
                    $add_arr = array_diff($PostData['channelid'],$oldchannelid);
                }else{
                    $this->News->_table = tbl_newschannelmapping;
                    $this->News->Delete(array("newsid"=>$newsid));
                }

                if(count($add_arr)>0){
                    $channelid_arr=array();
                    foreach($add_arr as $aa){
                        $channelid_arr[]=array('newsid'=>$newsid,'channelid'=>$aa);
                    }
                    if(count($channelid_arr)>0){
                        $this->News->_table = tbl_newschannelmapping;
                        $this->News->add_batch($channelid_arr);
                    }
                }

                if(count($delete_arr)>0){
                    $this->News->_table = tbl_newschannelmapping;
                    $this->News->Delete(array("channelid in(".implode(",",$delete_arr).")"=>null,"newsid"=>$newsid));
                } */
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'News','Edit '.$newsname.' news.');
                }
                echo 1; //NEWS updated successfully
              } else {
                echo 0; // news not inserted 
            }
        } else {
            echo 2; // news  name already added
        }
    } 

    public function delete_mul_news() {
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);

        foreach ($ids as $row) {
            // get essay id
            $this->News->_table = tbl_newschannelmapping;
            $this->News->Delete(array("newsid"=>$row));

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->News->_table = tbl_news;
                $this->News->_fields = "title";
                $this->News->_where = array("id"=>$row);
                $data = $this->News->getRecordsByID();
                
                $this->general_model->addActionLog(3,'News','Delete '.$data['title'].' news.');
            }

            $this->News->_table = tbl_news;
            $this->News->Delete(array("id"=>$row));          
        }
    }

    public function news_enable_disable() {
        $this->viewData = $this->getAdminSettings('submenu', 'News');
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->News->_table = tbl_news;
        $this->News->_where = array("id" => $PostData['id']);
        $this->News->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->News->_where = array("id"=>$PostData['id']);
            $data = $this->News->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['title'].' news.';
            
            $this->general_model->addActionLog(2,'News', $msg);
        }
        echo $PostData['id'];
    }
    
    function getnewsdescriptionbyid(){
        $PostData = $this->input->post();
        $this->News->_fields = "title,description";
        $this->News->_where = "id=".$PostData['id'];
        $data = $this->News->getRecordsByID();
        
        
        echo json_encode(array('pagetitle'=>ucwords($data['title']),'description'=>$data['description']));
    }
}
?>