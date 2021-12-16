<?php

class Notification extends MY_Controller
{

    function __construct(){
        parent::__construct();
    }
    public $data=array();

    function getnotification()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['employeeid']) && isset($PostData['counter'])){

                        if($PostData['employeeid'] == "" || $PostData['counter']==""){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            if(isset($PostData['type']) && $PostData['type']==1){
                                $usertype = 1;
                            }else{
                                $usertype = 0;   
                            }
                                $this->readdb->select("id,message,description,createddate");
                                $this->readdb->from(tbl_notification);
                                $this->readdb->where(array("usertype"=>$usertype,"FIND_IN_SET(memberid,".$PostData['employeeid'].")>0"=>null));
                                $this->readdb->order_by("id desc");
                                if($PostData['counter']!="-1"){
                                    $this->readdb->limit(10,$PostData['counter']);
                                }
                                $query = $this->readdb->get();
                                $notification = $query->result_array();
                            
                            if(!empty($notification)){
                                foreach ($notification as $row) { 
                                    $message="";
                                    $message_arr = json_decode($row['message'],true); 
                                    if(isset($message_arr['message'])){
                                        $message=$message_arr['message'];
                                    }
                                    $this->data[]= array("id"=>$row['id'],"title"=>$message,"desc"=>$row['description'],"date"=>$row['createddate']);
                                }
                            }
                            if(empty($this->data)){
                                ws_response("Fail", "Notification not available.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        }
                    }
                    else
                    {
                        ws_response("Fail", "Fields value are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

}
