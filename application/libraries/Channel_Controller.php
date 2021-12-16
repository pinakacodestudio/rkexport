<?php

class Channel_Controller extends MY_Controller {

    public $data = array();
    function __construct() {
        
        parent::__construct();
        $this->load->library("channel_headerlib");
        $this->load->library('form_validation');
        //echo 1;exit;
        $this->chk_channel_session();    
        
    }
    function getLoginSettings(){
        $this->db->reconnect();
        $this->load->database();;
        
        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails[base_url().'CHANNELLOGIN']) && $arrSessionDetails[base_url().'CHANNELLOGIN']){
            redirect(CHANNEL_URL."dashboard");           
        }
        return $this->data;

    }

    function getChannelSettings($menu,$Controller){
        $this->db->reconnect();
        $this->load->database();
        $this->load->model('Notification_model','Notification');
        
        if(strtolower($Controller)=='notification'){
            $this->Notification->updateUnreadNotification();
        }else if(strtolower($Controller)=='news'){
            $this->Notification->updateUnreadNewsNotification();
        }
       
        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails) && !empty($arrSessionDetails[base_url().'CHANNELLOGIN'])){
            if(!$arrSessionDetails[base_url().'CHANNELLOGIN']){
                redirect(CHANNEL_URL."login");           
            }
            else if($arrSessionDetails[base_url().'CHANNELLOGIN']  === TRUE){
                $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
                if(!is_null($MEMBERID)){

                    $query = $this->readdb->query("SELECT id,status FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id = ".$MEMBERID.")");
                    $ChannelData = $query->row_array();
                    
                    if(!empty($ChannelData) && $ChannelData['status']==0){
                        redirect(CHANNELFOLDER.'logout');
                    }    
                    
                    $query = $this->readdb->query("SELECT mr.id,mr.status FROM ".tbl_memberrole." as mr INNER JOIN ".tbl_member." as m ON m.roleid=mr.id AND m.id = ".$MEMBERID);
                    $RoleData = $query->row_array();
                    //print_r($RoleData); exit;
                    if(!empty($RoleData) && $RoleData['status']==0){
                        redirect(CHANNELFOLDER.'logout');
                    }    

                    if(PRICEINCREMENTDECREMENT==0 && channel_incrementdecrementprice==0 && $Controller=="Price_history"){
                        redirect('Pagenotfound');
                    }
                }
                
                $this->load->model('Side_navigation_model');
               
                if($menu=='mainmenu'){
                    $this->readdb->select('id,name,menuurl');
                    $this->readdb->from(tbl_channelmainmenu);
                    $where = "menuurl LIKE '%".$Controller."%'";
                    $this->readdb->where($where);
                    $query1 = $this->readdb->get();
                    if($query1->num_rows()!=0){
                        $row = $query1->row_array();
                        
                        $MenuData = array(base_url()."channelmainmenuid"=>$row ['id'],
                                        base_url()."mainmenuname"=>$row ['name'],
                                        base_url()."mainmenuurl"=>$row ['menuurl'],
                                        base_url()."submenuid"=>'',
                                        base_url()."submenuname"=>'',
                                        base_url()."submenuurl"=>'',
                                        base_url()."thirdlevelsubmenuid"=>'',
                                        base_url()."thirdlevelsubmenuname"=>'',
                                        base_url()."thirdlevelsubmenuurl"=>'',
                                    );
                                   
                        $this->session->set_userdata($MenuData);
                        $data['mainmenuvisibility'] = $this->Side_navigation_model->channelmainmenuselect();
                    }else{
                        redirect(CHANNELFOLDER.'login');
                    }
                    
                }else if($menu=='submenu'){
                    
                    $this->readdb->select('id,channelmainmenuid,url,name,(SELECT name FROM '.tbl_channelmainmenu.' WHERE id='.tbl_channelsubmenu.'.channelmainmenuid) as mainmenuname');
                    $this->readdb->from(tbl_channelsubmenu);
                    //$where = "menuurl LIKE '%/%".$Controller."'";
                    $where = "url LIKE '%".$Controller."'";
                    $this->readdb->where($where);
                    $query1 = $this->readdb->get();
                    
                    if($query1->num_rows()!=0){
                        $row = $query1->row_array();
                        $MenuData = array(base_url()."channelmainmenuid"=>$row ['channelmainmenuid'],
                                        base_url()."mainmenuname"=>$row ['mainmenuname'],
                                        base_url()."submenuid"=>$row ['id'],
                                        base_url()."submenuname"=>$row ['name'],
                                        base_url()."submenuurl"=>$row ['url'],
                                        base_url()."thirdlevelsubmenuid"=>'',
                                        base_url()."thirdlevelsubmenuname"=>'',
                                        base_url()."thirdlevelsubmenuurl"=>'',
                                    );
                        $this->session->set_userdata($MenuData);
                        $data['submenuvisibility'] = $this->Side_navigation_model->channelsubmenuselect();
                       
                    }else{
                        redirect(CHANNELFOLDER.'login');
                    }
                    
                }else{
                    $this->readdb->select('id,channelsubmenuid,url,name,(SELECT name FROM '.tbl_channelsubmenu.' WHERE id=channelsubmenuid) as submenuname,
                                            (SELECT id FROM '.tbl_channelmainmenu.' WHERE id=(SELECT mainmenuid FROM '.tbl_channelsubmenu.' WHERE id=channelsubmenuid)) as mainmenuid,                        
                                            (SELECT name FROM '.tbl_channelmainmenu.' WHERE id=(SELECT mainmenuid FROM '.tbl_channelsubmenu.' WHERE id=channelsubmenuid)) as mainmenuname                        
                    ');
                    $this->readdb->from(tbl_thirdlevelsubmenu);
                    //$where = "menuurl LIKE '%/%".$Controller."'";
                    $where = "url LIKE '%".$Controller."'";
                    $this->readdb->where($where);
                    $query1 = $this->readdb->get();
                    
                    if($query1->num_rows()!=0){
                        $row = $query1->row_array();
                        $MenuData = array(base_url()."mainmenuid"=>$row ['mainmenuid'],
                                        base_url()."mainmenuname"=>$row ['mainmenuname'],
                                        base_url()."submenuid"=>$row ['submenuid'],
                                        base_url()."submenuname"=>$row ['submenuname'],
                                        base_url()."thirdlevelsubmenuid"=>$row ['id'],
                                        base_url()."thirdlevelsubmenuname"=>$row ['name'],
                                        base_url()."thirdlevelsubmenuurl"=>$row ['url'],
                                    );
                        $this->session->set_userdata($MenuData);
                        $data['thirdlevelsubmenuvisibility'] = $this->Side_navigation_model->thirdlevelsubmenuselect();
                        // print_r($data['submenuvisibility']);exit;
                    }else{
                        redirect(ADMINFOLDER.'login');
                    }
                }
                
                $data['mainnavdata'] = $this->Side_navigation_model->channelmainnav(1);
                $data['subnavdata'] = $this->Side_navigation_model->channelsubnav(1);
                $data['subnavtabsmenu'] = $this->Side_navigation_model->channelsubnavtabsmenu();
                $data['thirdlevelsubnav'] = $this->Side_navigation_model->channelthirdlevelsubnav();
                $data['thirdlevelsubnavtabsmenu'] = $this->Side_navigation_model->channelthirdlevelsubnavtabsmenu();
                
                if($menu=='mainmenu'){
                    $this->data = array(
                        'mainnavdata' => $data['mainnavdata'],
                        'subnavdata' => $data['subnavdata'],
                        'subnavtabsmenu' => $data['subnavtabsmenu'],
                        'mainmenuvisibility' => $data['mainmenuvisibility'],
                        'thirdlevelsubnav' => $data['thirdlevelsubnav'],
                        'thirdlevelsubnavtabsmenu' => $data['thirdlevelsubnavtabsmenu'],
                        'notificationbadges' => $this->Notification->getUnreadNotificationBadge($arrSessionDetails[base_url().'MEMBERID']),
                        'notificationdata' => $this->Notification->getUnreadNotification($arrSessionDetails[base_url().'MEMBERID'])
                    );
                }else if($menu=='submenu'){
                    $this->data = array(
                        'mainnavdata' => $data['mainnavdata'],
                        'subnavdata' => $data['subnavdata'],
                        'subnavtabsmenu' => $data['subnavtabsmenu'],
                        'submenuvisibility' => $data['submenuvisibility'],
                        'thirdlevelsubnav' => $data['thirdlevelsubnav'],
                        'thirdlevelsubnavtabsmenu' => $data['thirdlevelsubnavtabsmenu'],
                        'notificationbadges' => $this->Notification->getUnreadNotificationBadge($arrSessionDetails[base_url().'MEMBERID']),
                        'notificationdata' => $this->Notification->getUnreadNotification($arrSessionDetails[base_url().'MEMBERID'])
                    );
                }else{
                    $this->data = array(
                        'mainnavdata' => $data['mainnavdata'],
                        'subnavdata' => $data['subnavdata'],
                        'thirdlevelsubnav' => $data['thirdlevelsubnav'],
                        'subnavtabsmenu' => $data['subnavtabsmenu'],
                        'thirdlevelsubnavtabsmenu' => $data['thirdlevelsubnavtabsmenu'],
                        'thirdlevelsubmenuvisibility' => $data['thirdlevelsubmenuvisibility'],
                        'ChannelIDArr' => $data['ChannelIDArr'],
                        'headernotificationbadges' => $this->Notification->getAdminUnreadNotificationBadge($this->session->userdata(base_url().'ADMINID')),
                        'headernotificationdata' => $this->Notification->getAdminUnreadNotification($this->session->userdata(base_url().'ADMINID'),5)
                    );
                }
            }
        }
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $this->load->model("Channel_model","Channel");
        $this->data['ChannelData'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');

        $this->load->model("Member_model","Member");
        $this->load->model("Order_model","Order");
        $memberdata = $this->Member->getMemberDetail($MEMBERID);
        $this->data['debitlimit'] = (isset($memberdata['debitlimit']))?number_format($memberdata['debitlimit'],2,".",","):0;
        $creditlimit = $this->Order->creditamount($MEMBERID);
        $this->data['creditlimit']= number_format($creditlimit,2,".",",");
        $rewardpoints = $this->Member->getCountRewardPoint($MEMBERID);
        $this->data['rewardpoints']= ((!empty($rewardpoints))?$rewardpoints['rewardpoint']:0);

        $this->data['sellerid'] = 0;
        if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1){
            
            $memberrows = $this->Member->getCurrentSellerCode($MEMBERID);
            $this->data['sellercode'] = (!empty($memberrows))?$memberrows['membercode']:'';
            $this->data['sellerid'] = (!empty($memberrows))?$memberrows['sellerid']:0;
        }
        
        return $this->data;

    }
   
    function chk_channel_session() {
        $arrSessionDetails = $this->session->userdata;        
        $session_login = isset($arrSessionDetails[base_url().'CHANNELLOGIN']) ? $arrSessionDetails[base_url().'CHANNELLOGIN'] : "";
        $arrAllowedWithoutLogin = array('login','forgot-password','reset-password');
        $arrNotAllowedAfterLogin = array('login','forgot-password','reset-password');
        if (!$session_login) {
            if (!in_array($this->uri->segment(2), $arrAllowedWithoutLogin) && !in_array($this->uri->segment(1), $arrAllowedWithoutLogin)) {
                redirect(CHANNELFOLDER.'login/');
            }
        } else if ($session_login && $session_login == TRUE) {
            $arrSegment2WithoutAjax = array('dashboard', 'logout');
            if($this->uri->segment(2)=='login'){
                $this->session->unset_userdata(base_url().'ADMINLOGIN');
                $this->session->unset_userdata(base_url().'ADMINID');
                $this->session->unset_userdata(base_url().'ADMINEMAIL');
                $this->session->unset_userdata(base_url().'ADMINUSERTYPE');
                $this->session->unset_userdata(base_url().'ADMINUSERIMAGE');
                redirect(ADMINFOLDER.'login/');
                // redirect(CHANNELFOLDER.'dashboard');
            }else if (in_array($this->uri->segment(2), $arrNotAllowedAfterLogin)) {
                redirect(CHANNELFOLDER.'dashboard');
            }
            
        }
    }

    function checkAdminAccessModule($menu,$role,$menuvisibility){
        $ADMINID = $this->session->userdata(base_url().'MEMBERID');
        
        $query = $this->readdb->query("SELECT IF(m.menuurl='',
                                    (SELECT s.url FROM ".tbl_channelsubmenu." as s WHERE find_in_set((SELECT roleid FROM ".tbl_member." WHERE id=".$ADMINID.") ,submenuvisible) and channelmainmenuid=m.id AND s.url!='' LIMIT 1),
                                    m.menuurl) as menuurl 
                                    FROM ".tbl_channelmainmenu." as m 
                                    WHERE find_in_set((SELECT roleid FROM ".tbl_member." WHERE id=".$ADMINID.") ,menuvisible) 
                                    ORDER BY inorder ASC LIMIT 1");
    
        $redirecturl = $query->row_array();
        //echo $this->db->last_query();exit;
        if($menu=='submenu'){
            if ($role=='add' && strpos($menuvisibility['submenuadd'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.CHANNELFOLDER.$redirecturl['menuurl']);
            }else if ($role=='edit' && strpos($menuvisibility['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.CHANNELFOLDER.$redirecturl['menuurl']);
            }else if ($role=='view' && strpos($menuvisibility['submenuvisible'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.CHANNELFOLDER.$redirecturl['menuurl']);
            }
        }else if($menu=='mainmenu'){
            if ($role=='add' && strpos($menuvisibility['mainmenuadd'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.CHANNELFOLDER.$redirecturl['menuurl']);
            }else if ($role=='edit' && strpos($menuvisibility['mainmenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.CHANNELFOLDER.$redirecturl['menuurl']);
            }else if ($role=='view' && strpos($menuvisibility['menuvisible'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.CHANNELFOLDER.$redirecturl['menuurl']);
            }
        }
    }

    public function validurl($field, $url){
       
        $pattern = '/(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/';
        if (!preg_match($pattern, $url))
        {
            $this->form_validation->set_message('validurl', 'Please enter valid {field} url !');
            return false;
        }else{
            return true;    
        }
    }
    public function dropdowncheck($field, $value){
       
        if($value == '0'){
            $this->form_validation->set_message('dropdowncheck', 'Please select {field} !');
            return false;
        }else{
            return true;    
        }
    }

}
