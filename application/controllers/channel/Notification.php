<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Notification_model', 'Notification');
        $this->load->model('Channel_model', 'Channel');
        $this->viewData = $this->getChannelSettings('submenu', 'Notification');
    }

    public function index() {
        $this->viewData['title'] = "Notification";
        $this->viewData['module'] = "notification/Notification";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->channel_headerlib->add_javascript("Notification", "pages/notification.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {   
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Notification->get_datatables();      
        $data = array();        
        $counter = $_POST['start'];

        $channeldata = $this->Channel->getChannelList();

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';

            if(in_array($rollid, $delete)) {
                $actions.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Notification","'.CHANNEL_URL.'notification/delete-mul-notification","notification") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }

            $message = '';
            $messageData['message'] = json_decode($datarow->message);
            // print_r($messageData['message']->message);exit;
            if(!empty($messageData['message'])){
                if(isset($messageData['message']->message)){
                    $message = $messageData['message']->message;    
                }
                /* foreach ($messageData['message'] as $messageName) {
                } */
            }

            $row[] = ++$counter;

            if($datarow->memberid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->memberid){
                    $row[] = $channellabel.ucwords($datarow->membername).' ('.$datarow->membercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->memberid.'" target="_blank" title="'.ucwords($datarow->membername).'">'.ucwords($datarow->membername).' ('.$datarow->membercode.')'."</a>";
                }
            }

            $row[] = $message;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Notification->count_all(),
                        "recordsFiltered" => $this->Notification->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
   
    public function add_notification() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Notification";
        $this->viewData['module'] = "notification/Add_notification";   
        $this->viewData['VIEW_STATUS'] = "0";          
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');  
        /*  $this->load->model("Member_model","Member"); 
        $this->viewData['memberdata'] = $this->Member->getMemberListInUnderChannel($MEMBERID); */

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'allowedchannelmemberregistration');

        //$this->viewData['memberData'] = $this->Notification->getmemberdata();
        $this->channel_headerlib->add_javascript("Notification", "pages/add_notification.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function notification_add() {
         
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post(); 
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';  
        $memberidarr = isset($PostData['memberid']) ? $PostData['memberid'] : '';       
        $createddate  =  $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');       
        $notification ='1';   
        $insertData = array();
        
        if(!empty($memberidarr)){
               
            if($notification == 1){    
                //$memberidarr = array (  2 );
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId(implode(",",$memberidarr));
                
                if(!empty($fcmquery)){
                    foreach ($fcmquery as $fcmrow){   
                        $fcmarray=array();                       
                        $type = "14";// catalog =1 , news =2 , product =3
                        $msg = $description;    
                        $memberid = $fcmrow['memberid'];  
                        
                        $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                        $fcmarray[] = $fcmrow['fcm'];
                            //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);
                        
                        $this->Fcm->sendFcmNotification($type,$pushMessage,$memberid,$fcmarray,0,$fcmrow['devicetype']);
                        
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
                echo 1;//send notification
            }else{
                echo 2;//not set notification
            }
            
        }
    }

    public function delete_mul_notification() {
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            
            // Delete from essay data table
            $this->Notification->Delete(array('id'=>$row));          
        }
    }
}
?>