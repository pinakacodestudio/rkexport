<?php

class Admin_Controller extends MY_Controller {

    public $data = array();
    function __construct() {
        
        parent::__construct();
        $this->load->library("admin_headerlib");
        $this->load->library('form_validation');
        $this->load->library('aws');
        $this->chk_admin_session(); 
    }
    
    function getLoginSettings(){
        $this->db->reconnect();
        $this->load->database();
        
        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails[base_url().'ADMINLOGIN']) && $arrSessionDetails[base_url().'ADMINLOGIN']){
            // redirect(ADMIN_URL."dashboard");           
        }
        return $this->data;

    }

    function getAdminSettings($menu,$Controller){
        $this->db->reconnect();
        $this->load->database();
        
        $arrSessionDetails = $this->session->userdata;
        if(isset($arrSessionDetails) && !empty($arrSessionDetails[base_url().'ADMINLOGIN'])){
            if(!$arrSessionDetails[base_url().'ADMINLOGIN']){
                redirect(ADMIN_URL."login");           
            }
            else if($arrSessionDetails[base_url().'ADMINLOGIN']  === TRUE){
                $ADMINUSERTYPE = $this->session->userdata(base_url().'ADMINUSERTYPE');
                $ADMINID = $this->session->userdata(base_url().'ADMINID');
                
                if(!is_null($ADMINUSERTYPE)){

                    $query = $this->readdb->query("SELECT id,status,channelid FROM ".tbl_user." WHERE id = ".$ADMINID);
                    $UserData = $query->row_array();
                    if(!empty($UserData) && $UserData['status']==0){
                        redirect(ADMINFOLDER.'logout');
                    }  

                    $query = $this->readdb->query("SELECT id,status FROM ".tbl_userrole." WHERE id = ".$ADMINUSERTYPE);
                    $RoleData = $query->row_array();
                    if(!empty($RoleData) && $RoleData['status']==0){
                        redirect(ADMINFOLDER.'logout');
                    }    
                   
                    $this->load->model('Side_navigation_model');
                    
                }

                if(!is_null($this->session->userdata(base_url().'ADMINID')) && strtotime(date("d-m-Y h:i:s",strtotime('-10 minute'))) > strtotime($this->session->userdata(base_url().'CHECKUSERDETAILTIME'))) {
                    $this->load->model('User_model', 'User');

                    $this->User->_where = array('id'=>$this->session->userdata(base_url().'ADMINID'));
                    $this->User->_fields = "name,email,roleid,(select role from ".tbl_userrole." where id=roleid)as role,image,sidebarcount";
                    $result = $this->User->getRecordsByID();
                    if(!empty($result)) {
                    $this->session->set_userdata(
                        array(
                            base_url().'ADMINNAME'=>$result['name'],
                            base_url().'ADMINEMAIL'=>$result['email'],
                            base_url().'ADMINUSERTYPE'=>$result['roleid'],
                            base_url().'ADMINUSERROLE'=>$result['role'],
                            base_url().'ADMINUSERIMAGE'=>$result['image'],
                            base_url().'SIDEBARCOUNT'=>$result['sidebarcount'])
                        );
                    }
                    $this->session->set_userdata(base_url().'CHECKUSERDETAILTIME',date("d-m-Y h:i:s"));
                }
                
                $this->load->model('Side_navigation_model');
                if($menu=='mainmenu'){
                    $this->readdb->select('id,name,menuurl');
                    $this->readdb->from(tbl_mainmenu);
                    $where = "menuurl LIKE '%".$Controller."%'";
                    $this->readdb->where($where);
                    $query1 = $this->readdb->get();
                    if($query1->num_rows()!=0){
                        $row = $query1->row_array();

                        $MenuData = array(base_url()."mainmenuid"=>$row ['id'],
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
                        $data['mainmenuvisibility'] = $this->Side_navigation_model->mainmenuselect();
                    }else{
                        redirect(ADMINFOLDER.'login');
                    }
                }else if ($menu=='submenu'){
                    $this->readdb->select('id,mainmenuid,url,name,(SELECT name FROM '.tbl_mainmenu.' WHERE id=submenu.mainmenuid) as mainmenuname');
                    $this->readdb->from(tbl_submenu);
                    //$where = "menuurl LIKE '%/%".$Controller."'";
                    $where = "url LIKE '%".$Controller."'";
                    $this->readdb->where($where);
                    $query1 = $this->readdb->get();
                    
                    if($query1->num_rows()!=0){
                        $row = $query1->row_array();
                        $MenuData = array(base_url()."mainmenuid"=>$row ['mainmenuid'],
                                        base_url()."mainmenuname"=>$row ['mainmenuname'],
                                        base_url()."submenuid"=>$row ['id'],
                                        base_url()."submenuname"=>$row ['name'],
                                        base_url()."submenuurl"=>$row ['url'],
                                        base_url()."thirdlevelsubmenuid"=>'',
                                        base_url()."thirdlevelsubmenuname"=>'',
                                        base_url()."thirdlevelsubmenuurl"=>'',
                                    );
                        $this->session->set_userdata($MenuData);
                        $data['submenuvisibility'] = $this->Side_navigation_model->submenuselect();
                        //print_r($data['submenuvisibility']);exit;
                    }else{
                        redirect(ADMINFOLDER.'login');
                    }
                    
                }else{
                    $this->readdb->select('id,submenuid,url,name,(SELECT name FROM '.tbl_submenu.' WHERE id=submenuid) as submenuname,
                                            (SELECT id FROM '.tbl_mainmenu.' WHERE id=(SELECT mainmenuid FROM '.tbl_submenu.' WHERE id=submenuid)) as mainmenuid,                        
                                            (SELECT name FROM '.tbl_mainmenu.' WHERE id=(SELECT mainmenuid FROM '.tbl_submenu.' WHERE id=submenuid)) as mainmenuname                        
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
                
                $data['mainnavdata'] = $this->Side_navigation_model->mainnav();
                // print_r($data['mainnavdata']);exit;
                $data['subnavdata'] = $this->Side_navigation_model->subnav();
                $data['thirdlevelsubnav'] = $this->Side_navigation_model->thirdlevelsubnav();
                $data['subnavtabsmenu'] = $this->Side_navigation_model->subnavtabsmenu();
                $data['thirdlevelsubnavtabsmenu'] = $this->Side_navigation_model->thirdlevelsubnavtabsmenu();
                // print_r($data['thirdlevelsubnavtabsmenu']);exit;
                
                if(!empty($data['mainmenuvisibility']['assignadditionalrights'])){
                    $ADMINID = $this->session->userdata(base_url().'ADMINUSERTYPE');
                    $assignadditionalrightsmm = json_decode($data['mainmenuvisibility']['assignadditionalrights'], true);
                   
                    if (array_key_exists($ADMINID, $assignadditionalrightsmm)) {
                        $mainmenurightsarr = explode(",",str_replace("#","",$assignadditionalrightsmm[$ADMINID]));
                        $mainmenurightsarr = implode(",",$mainmenurightsarr);

                        $this->load->model('Additional_rights_model','Additional_rights');
                        $this->Additional_rights->_fields = "GROUP_CONCAT(slug) as slug";
                        $this->Additional_rights->_where = "FIND_IN_SET(id,'".$mainmenurightsarr."')>0";
                        $mmdataarr = $this->Additional_rights->getRecordsByID();
                        
                        $data['mainmenuvisibility']['assignadditionalrights'] = explode(",",$mmdataarr['slug']);
                    }else{
                        $data['mainmenuvisibility']['assignadditionalrights'] = array();
                    }
                }else{
                    $data['mainmenuvisibility']['assignadditionalrights'] = array();
                }
                // var_dump($data['submenuvisibility']['assignadditionalrights']);exit;
                if(!empty($data['submenuvisibility']['assignadditionalrights'])){
                    $ADMINID = $this->session->userdata(base_url().'ADMINUSERTYPE');
                    $assignadditionalrights = json_decode($data['submenuvisibility']['assignadditionalrights'], true);
                    
                    if (array_key_exists($ADMINID, $assignadditionalrights)) {
                        $menurightsarr = explode(",",str_replace("#","",$assignadditionalrights[$ADMINID]));
                        $menurightsarr = implode(",",$menurightsarr);

                        $this->load->model('Additional_rights_model','Additional_rights');
                        $this->Additional_rights->_fields = "GROUP_CONCAT(slug) as slug";
                        $this->Additional_rights->_where = "FIND_IN_SET(id,'".$menurightsarr."')>0";
                        $dataarr = $this->Additional_rights->getRecordsByID();
                        
                        $data['submenuvisibility']['assignadditionalrights'] = explode(",",$dataarr['slug']);
                    }else{
                        $data['submenuvisibility']['assignadditionalrights'] = array();
                    }
                }else{
                    $data['submenuvisibility']['assignadditionalrights'] = array();
                }

                if(!empty($data['thirdlevelsubmenuvisibility']['assignadditionalrights'])){
                    $ADMINID = $this->session->userdata(base_url().'ADMINUSERTYPE');
                    $assignadditionalrights = json_decode($data['thirdlevelsubmenuvisibility']['assignadditionalrights'], true);
                    
                    if (array_key_exists($ADMINID, $assignadditionalrights)) {
                        $menurightsarr = explode(",",str_replace("#","",$assignadditionalrights[$ADMINID]));
                        $menurightsarr = implode(",",$menurightsarr);

                        $this->load->model('Additional_rights_model','Additional_rights');
                        $this->Additional_rights->_fields = "GROUP_CONCAT(slug) as slug";
                        $this->Additional_rights->_where = "FIND_IN_SET(id,'".$menurightsarr."')>0";
                        $dataarr = $this->Additional_rights->getRecordsByID();
                        
                        $data['thirdlevelsubmenuvisibility']['assignadditionalrights'] = explode(",",$dataarr['slug']);
                    }else{
                        $data['thirdlevelsubmenuvisibility']['assignadditionalrights'] = array();
                    }
                }else{
                    $data['thirdlevelsubmenuvisibility']['assignadditionalrights'] = array();
                }
                
                if($menu=='mainmenu'){
                    $this->data = array(
                        'mainnavdata' => $data['mainnavdata'],
                        'subnavdata' => $data['subnavdata'],
                        'thirdlevelsubnav' => $data['thirdlevelsubnav'],
                        'thirdlevelsubnavtabsmenu' => $data['thirdlevelsubnavtabsmenu'],
                        'subnavtabsmenu' => $data['subnavtabsmenu'],
                        'mainmenuvisibility' => $data['mainmenuvisibility'],
                    );
                }else if($menu=='submenu'){
                    $this->data = array(
                        'mainnavdata' => $data['mainnavdata'],
                        'subnavdata' => $data['subnavdata'],
                        'thirdlevelsubnav' => $data['thirdlevelsubnav'],
                        'thirdlevelsubnavtabsmenu' => $data['thirdlevelsubnavtabsmenu'],
                        'subnavtabsmenu' => $data['subnavtabsmenu'],
                        'submenuvisibility' => $data['submenuvisibility'],
                    );
                }else{
                    $this->data = array(
                        'mainnavdata' => $data['mainnavdata'],
                        'subnavdata' => $data['subnavdata'],
                        'thirdlevelsubnav' => $data['thirdlevelsubnav'],
                        'subnavtabsmenu' => $data['subnavtabsmenu'],
                        'thirdlevelsubnavtabsmenu' => $data['thirdlevelsubnavtabsmenu'],
                        'thirdlevelsubmenuvisibility' => $data['thirdlevelsubmenuvisibility'],
                    );
                }
                
                
                
                /* if(!is_null($this->session->userdata('SESSION_FILTERS')) && !empty($this->session->userdata('SESSION_FILTERS'))){
                    if(!isset($this->session->userdata('SESSION_FILTERS')[$Controller])){
                        
                       
                        $this->session->set_userdata('SESSION_FILTERS', array($Controller => array("panelcollapsed"=>0)));
                    }
                }else{
                    $this->session->set_userdata('SESSION_FILTERS',array($Controller => array("panelcollapsed"=>0)));
                } */
                // echo "<pre>"; print_r($this->session->userdata('SESSION_FILTERS')); exit;
            }
        }
        
        return $this->data;
    }
   
    function chk_admin_session() {
        $arrSessionDetails = $this->session->userdata;        
        $session_login = isset($arrSessionDetails[base_url().'ADMINLOGIN']) ? $arrSessionDetails[base_url().'ADMINLOGIN'] : "";
        
        $arrAllowedWithoutLogin = array('login','forgot-password','reset-password','privacy-policy');
        $arrNotAllowedAfterLogin = array('login','forgot-password','reset-password');
        
        if (!$session_login) {
            
            if (!in_array($this->uri->segment(2), $arrAllowedWithoutLogin) && !in_array($this->uri->segment(1), $arrAllowedWithoutLogin)) {
                redirect(ADMINFOLDER.'login/');
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
                //redirect(ADMINFOLDER.'dashboard');
            }else if (in_array($this->uri->segment(2), $arrNotAllowedAfterLogin)) {
                //redirect(ADMINFOLDER.'dashboard');
            }
        }
    }

    function checkAdminAccessModule($menu,$role,$menuvisibility){
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        
        $query = $this->readdb->query("SELECT IF(m.menuurl='',(SELECT s.url FROM ".tbl_submenu." as s WHERE find_in_set((SELECT roleid FROM ".tbl_user." WHERE id=".$ADMINID.") ,submenuvisible) and mainmenuid=m.id AND s.url!='' LIMIT 1),m.menuurl) as menuurl FROM ".tbl_mainmenu." as m WHERE find_in_set((SELECT roleid FROM ".tbl_user." WHERE id=".$ADMINID.") ,menuvisible)  ORDER BY inorder ASC LIMIT 1");
        // $query = $this->readdb->query("SELECT IF(m.menuurl='',IF((SELECT s.url FROM ".tbl_submenu." as s WHERE find_in_set((SELECT roleid FROM ".tbl_user." WHERE id=".$ADMINID.") ,submenuvisible) and mainmenuid=m.id AND s.url!='' LIMIT 1)='',(SELECT t.url FROM ".tbl_thirdlevelsubmenu." as t WHERE find_in_set((SELECT roleid FROM ".tbl_user." WHERE id=".$ADMINID.") ,submenuvisible) and submenuid=(SELECT s.id FROM ".tbl_submenu." as s WHERE find_in_set((SELECT roleid FROM ".tbl_user." WHERE id=".$ADMINID.") ,submenuvisible) and mainmenuid=m.id AND s.url!='' LIMIT 1) AND t.url!='' LIMIT 1),(SELECT s.url FROM ".tbl_submenu." as s WHERE find_in_set((SELECT roleid FROM ".tbl_user." WHERE id=".$ADMINID.") ,submenuvisible) and mainmenuid=m.id AND s.url!='' LIMIT 1)),m.menuurl) as menuurl FROM ".tbl_mainmenu." as m WHERE find_in_set((SELECT roleid FROM ".tbl_user." WHERE id=".$ADMINID.") ,menuvisible)  ORDER BY inorder ASC LIMIT 1");

        $redirecturl = $query->row_array();
        // echo $this->readdb->last_query(); exit;
        if($menu=='submenu'){
            if ($role=='add' && strpos($menuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }else if ($role=='edit' && strpos($menuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }else if ($role=='view' && strpos($menuvisibility['submenuvisible'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }
        }else if($menu=='mainmenu'){
            if ($role=='add' && strpos($menuvisibility['mainmenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }else if ($role=='edit' && strpos($menuvisibility['mainmenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }else if ($role=='view' && strpos($menuvisibility['menuvisible'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }
        }else if($menu=='thirdlevelsubmenu'){
            if ($role=='add' && strpos($menuvisibility['thirdlevelsubmenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }else if ($role=='edit' && strpos($menuvisibility['thirdlevelsubmenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }else if ($role=='view' && strpos($menuvisibility['thirdlevelsubmenuvisible'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') === false){
                redirect(DOMAIN_URL.ADMINFOLDER.$redirecturl['menuurl']);
            }
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
    
}
