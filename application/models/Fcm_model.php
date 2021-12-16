<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fcm_model extends Common_model {

    public $_table = tbl_fcmdata;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order;

    function __construct() {
        parent::__construct();
    }

    function insertfcm($deviceid, $fcm,$devicetype,$memberid,$channelid,$usertype=0) {
        $query = $this->getRecordsByID();

        $this->load->model('Fcm_model', 'Fcm');

        $createddate = $this->general_model->getCurrentDateTime();
        if (!empty($query)) {
            $query = $this->readdb->query("SELECT id FROM " . $this->_table . " WHERE deviceid='" . $deviceid . "' AND usertype='".$usertype."'");     
           
            if ($query->num_rows() > 0) {
                $updatedata = array(
                    'fcm' => $fcm,
                    'devicetype'=>$devicetype,                    
                    'memberid'=>$memberid,                    
                    'channelid'=>$channelid
                );

                $this->writedb->where('deviceid',$deviceid);
                $updateid =  $this->writedb->update(tbl_fcmdata, $updatedata);

                if($updateid != 0){
                    ws_response('success', 'FCM successfully updated.');
                }
            }else{
                $insertdata = array(
                            'deviceid' => $deviceid,                
                            'fcm' => $fcm,
                            'devicetype'=>$devicetype,
                            'memberid'=>$memberid,
                            'usertype'=>$usertype,
                            'channelid'=>$channelid,
                            'datetime'=>$createddate);
                            
                $insertid =  $this->writedb->insert(tbl_fcmdata, $insertdata);
                
                if ($insertid != 0) {
                    ws_response('success', 'FCM successfully added.');
                } else {
                    ws_response('fail', 'FCM not added.');
                }
            }
        }else{
            $insertdata = array(
                'deviceid' => $deviceid,                
                'fcm' => $fcm,
                'devicetype'=>$devicetype,
                'datetime'=>$createddate,
                'memberid'=>$memberid,
                'channelid'=>$channelid,
                'usertype'=>$usertype
            );
            $insertid =  $this->Fcm->Add($insertdata);
            
            if ($insertid != 0) {
                ws_response('success', 'FCM successfully added.');
            } else {
                ws_response('fail', 'FCM not added.');
            }  
        }
    }
    function getFcmDataByChannelId($channelid) {
        
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        $REPORTINGTO = $this->session->userdata(base_url().'REPORTINGTO');
        
        $where='';
		if(!is_null($MEMBERID)) {
			$where .= "m.id=fcm.memberid AND FIND_IN_SET(m.id, (SELECT GROUP_CONCAT(submemberid) FROM ".tbl_membermapping." where mainmemberid=".$MEMBERID.")) AND m.status=1";
		}else{
            $where .= "m.id=fcm.memberid AND FIND_IN_SET(m.channelid, '".$channelid."')";
        }
        $query = $this->readdb->select($this->_fields)
                    ->from($this->_table." as fcm")
                    ->join(tbl_member." as m", $where, "INNER")
                    ->where("fcm.usertype=0")
                    ->get();
        //echo $this->readdb->last_query(); exit;
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
    }
    
    function getFcmDataByMemberId($memberid) {
        
        $query = $this->readdb->select($this->_fields.",(select name from ".tbl_member." where id=fcm.memberid) as membername")
                    ->from($this->_table." as fcm")
                    ->where("FIND_IN_SET(fcm.memberid, '".$memberid."')>0 AND fcm.usertype=0")
                    ->get();
        
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
    }

    function getFcmDataByEmployeeId($employeeid) {
        
        $query = $this->readdb->select($this->_fields.",(select name from ".tbl_user." where id=fcm.memberid) as employeename")
                    ->from($this->_table." as fcm")
                    ->where("fcm.memberid IN (".$employeeid.") AND fcm.usertype=1")
                    ->get();
        
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
    }


    function getFcmData() {
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');

        $query = $this->readdb->select($this->_fields)
                    ->from($this->_table." as fcm")
                    ->join(tbl_member." as m", "FIND_IN_SET(m.id, (SELECT GROUP_CONCAT(submemberid) FROM ".tbl_membermapping." where mainmemberid=".$memberid.")) AND m.status=1", "INNER")
                    ->where("fcm.usertype=0")
                    ->get();
        
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return array();
		}
    }
}
?>