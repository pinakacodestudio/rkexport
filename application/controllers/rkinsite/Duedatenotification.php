<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Duedatenotification extends MY_Controller {

    public $viewData = array();
    public $contenttype ;

    function __construct() {
        parent::__construct();
    }

   public function index() {
        $this->readdb->select("oi.date,po.orderid,memberid");
        $this->readdb->from(tbl_productorder." as po");
        $this->readdb->join(tbl_orderinstallment." as oi","po.id=oi.orderid");
        $this->readdb->where(array("po.status"=>1,"oi.status"=>0,"po.usertype"=>1,"oi.date >= curdate()"=>null,"oi.date <= (curdate() + INTERVAL 2 DAY)"=>null));
        $duedatedata=$this->readdb->get()->result_array();
        // echo "<pre>";print_r( $duedatedata);exit();  
        /**/
        $createddate  =  $this->general_model->getCurrentDateTime();
        foreach ($duedatedata as $dd) {
            $this->load->model('Fcm_model','Fcm');
            $this->Fcm->_fields='*';
            $this->Fcm->_where = array("memberid"=>$dd['memberid']);
            $fcmquery = $this->Fcm->getRecordByID();
            // print_r($fcmquery);exit;                      
            if(!empty($fcmquery)){
              foreach ($fcmquery as $fcmrow){ 
                $fcmarray=array();                             
                $type = "4";// catalog =1 , news =2 , product =3
                if($dd['date']==date("Y-m-d")){
                    $msg = "Your payment is due for order id : ".$dd['orderid'];    
                }else{
                    $msg = "Your payment for order id : ".$dd['orderid']." will be due on ".$this->general_model->displaydate($dd['date']);    
                }
                                          
                $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$dd['orderid'].'"}';
                $fcmarray[] = $fcmrow['fcm'];
           
                //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                $this->Fcm->sendFcmNotification($type,$pushMessage,$dd['memberid'],$fcmarray,0,$fcmrow['devicetype']);

               }                    
                $notificationdata = array('message'=>$pushMessage,'type'=>$type,'createddate'=>$createddate,"memberid"=>$dd['memberid']);                  
                $this->load->model('Notification_model','Notification');  
                $this->Notification->_table = tbl_notification;
                $insertfcmnotification = $this->Notification->Add($notificationdata);
            }   
        }
        /**/
    }
}