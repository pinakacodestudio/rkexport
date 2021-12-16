<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('News_model', 'News');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'News');
    }
    public function index() {
        $this->viewData['title'] = "News";
        $this->viewData['module'] = "news/News";
        $this->viewData['VIEW_STATUS'] = "1";
       
        $this->channel_headerlib->add_javascript("News", "pages/news.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
     public function listing() {   
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->News->get_datatables();   
        // echo $this->db->last_query();exit();   
        $data = array();        
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';

            if(in_array($rollid, $edit)) {

                if($datarow->addedby == $this->session->userdata(base_url().'MEMBERID')){
                    $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'news/edit-news/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
               
                    if($datarow->status==1){
                        $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'news/news-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                    else{
                        $actions .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'news/news-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                    }
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","News","'.CHANNEL_URL.'news/delete-mul-news","news") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
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
            $row[] = ucwords($datarow->title);
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
        $this->viewData['title'] = "Add News";
        $this->viewData['module'] = "news/Add_news";   
        $this->viewData['VIEW_STATUS'] = "0";            

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'allowedchannelmemberregistration');

        

        $this->channel_headerlib->add_javascript("news", "pages/add_news.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function news_add() {
         
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post(); 
        $newsname = isset($PostData['newsname']) ? trim($PostData['newsname']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';   
        $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';   
        $createddate  =  $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');       
        $status = $PostData['status'];   
        $notification ='1';   
        
        $this->News->_where=array("title"=>$newsname);
        $sqlname = $this->News->getRecordsByID();
        if(empty($sqlname)){          
            $InsertData = array(
                        'title' => $newsname,
                        'description'=>$description,   
                        'type' => 1,    
                        'forapp' => 1,
                        'createddate' => $createddate,                    
                        'modifieddate' => $createddate, 
                        'addedby'=>$addedby,
                        'modifiedby'=>$addedby,
                        'status' => $status);
           
            $insertid = $this->News->add($InsertData);
            
            if($insertid != 0){
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
                
                if(!empty($memberidarr)){
                    if($notification == 1){    
                      
                        //$memberidarr = array (  2 );
                        $this->load->model('Fcm_model','Fcm');
                        $fcmquery = $this->Fcm->getFcmDataByMemberId(implode(",",$memberidarr));
                        //print_r($fcmquery);exit;
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
                    
                    //print_r($insertData);exit;
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
        $this->viewData['title'] = "Edit News";
        $this->viewData['module'] = "news/Add_news";
        $this->viewData['VIEW_STATUS'] = "1";   
        $this->viewData['action'] = "1";  
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $this->News->_where = array('id' => $id);
        $newsdata = $this->News->getRecordsByID();    
        
        if($newsdata['addedby']!=$MEMBERID){
            redirect('Pagenotfound');
        }
        $this->viewData['newsdata'] = $newsdata;
        $this->load->model("Member_model","Member"); 
        $this->viewData['memberdata'] = $this->Member->getMemberListInUnderChannel($MEMBERID);

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'allowedchannelmemberregistration');

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

        // print_r($this->viewData['memberidarr']); exit;
        $this->channel_headerlib->add_javascript("News", "pages/add_news.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    } 
    
    public function news_update()
    {
        $PostData = $this->input->post(); 
        $newsid= trim($PostData['newsid']);
        $newsname = isset($PostData['newsname']) ? trim($PostData['newsname']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';      
        $status = $PostData['status'];   
        $modifieddate  =  $this->general_model->getCurrentDateTime();  
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');     
        
        $this->News->_where = "title= '".$newsname."' AND id <> ".$newsid;
        $sqlname = $this->News->getRecordsByID();

        if(empty($sqlname)){          
            $updateData = array(
                            'title' => $newsname,
                            'description'=>$description,               
                            'status'=>$status,            
                            'modifieddate' => $modifieddate,
                            'modifiedby'=>$modifiedby
                          );
           
            $this->News->_where = array('id' => $newsid);
            $updateid = $this->News->Edit($updateData);
            
            if($updateid != 0){

                $this->load->model('Member_model', 'Member');
                $deletearr=array();

                $oldmemberid = (!empty($PostData['oldmemberid']))?explode(",",$PostData['oldmemberid']):'';
                $memberidarr = $PostData['memberid'];

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
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $this->News->_table = tbl_newschannelmapping;
            $this->News->Delete(array("newsid"=>$row));

            $this->News->_table = tbl_news;
            $this->News->Delete(array('id'=>$row));          
        }
    }

    public function news_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->News->_table = tbl_news;
        $this->News->_where = array("id" => $PostData['id']);
        $this->News->Edit($updatedata);

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