<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Notification_model', 'Notification');
        $this->load->model('Channel_model', 'Channel');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Notification');
    }

    public function index() {
        $this->viewData['title'] = "Notification";
        $this->viewData['module'] = "notification/Notification";
        $this->viewData['VIEW_STATUS'] = "1";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Notification','View notification.');
        }
        $this->admin_headerlib->add_javascript("Notification", "pages/notification.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {   

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Notification->get_datatables();      
        $data = array();        
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $channellabel = '';

            if($datarow->usertype==1){
                $membername = $datarow->membername;
            }else{
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $membername = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'">'.ucwords($datarow->membername).' ('.$datarow->membercode.')'.'</a>';
            }

            if(in_array($rollid, $delete)) {
                $actions.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Notification","'.ADMIN_URL.'notification/delete-mul-notification","notification") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }

            $message = '';
            $messageData['message'] = json_decode($datarow->message);
            if(!empty($messageData['message'])){
                if(isset($messageData['message']->message)){
                    $message = $messageData['message']->message;    
                }
                /* foreach ($messageData['message'] as $messageName) {
                } */
            }

            $row[] = ++$counter;
            $row[] = $membername;
            $row[] = $message;
            $row[] = $datarow->createddate;       
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
        $this->viewData = $this->getAdminSettings('submenu', 'Notification');      
        $this->viewData['title'] = "Add Notification";
        $this->viewData['module'] = "notification/Add_notification";   
        $this->viewData['VIEW_STATUS'] = "0";      
        
        $this->viewData['channeldata'] = $this->Channel->getChannelList(); 
        
        $this->load->model("Brand_model","Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        //$this->viewData['memberData'] = $this->Notification->getmemberdata();
        $this->admin_headerlib->add_javascript("Notification", "pages/add_notification.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function get_students()
    {
      $PostData = $this->input->post();
      $notificationtype = $PostData['notificationtype'];
      $studentdata = $this->Member->getMultipleStudents($notificationtype);
      echo json_encode($studentdata);
    }

    public function notification_add() {
         
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post(); 
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';      
        $createddate  =  $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');       
        $notification ='1';   
        $insertData = array();
        //print_r($PostData); exit;

        if(!empty($PostData['memberid'])){
            if($notification == 1){    
                
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId(implode(",",$PostData['memberid']));
                
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

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Notification','Add new notification "'.$description.'".');
                }
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

        $this->Notification->_table = tbl_notification;
        foreach ($ids as $row) {
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Notification->_fields = "message";
                $this->Notification->_where = array("id"=>$row);
                $Notificationdata = $this->Notification->getRecordsById();
                $message = json_decode($Notificationdata['message']);
                
                $this->general_model->addActionLog(3,'Notification','Delete "'.$message->message.'" notification.');
            }
            $this->Notification->Delete(array('id'=>$row));
        }
    }
}
?>