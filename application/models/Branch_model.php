<?php

class Branch_model extends Common_model {

    //put your code here
    public $_table = tbl_branch;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    
    function __construct() {
        parent::__construct();
    }
    
    function getBranchDataByID($ID){
        $query = $this->readdb->select("b.*,b.branchname as branch,
        (SELECT countryid FROM ".tbl_province." WHERE id=b.provinceid) as countryid    
        ")
                            ->from($this->_table." as b")
                            ->where("b.id", $ID)
                            ->get();
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return 0;
        }	
    }

    function getBranchData($order="DESC"){
        
        $query = $this->readdb->select("b.id,b.branchname,b.services,b.address,b.status,b.createddate,b.modifieddate,
        (SELECT u.name FROM ".tbl_user." as u WHERE u.id=b.addedby)as addedby,
        (SELECT u.name FROM ".tbl_user." as u WHERE u.id=b.modifiedby)as modifiedby,
        (SELECT name FROM ".tbl_province." WHERE id=b.provinceid) as province,
        (SELECT name FROM ".tbl_city." WHERE id=b.cityid) as city,

        ")
                ->from($this->_table." as b")
                ->order_by("b.id", $order)
                ->get();

        return $query->result_array();
    }

    function getActiveBranchData(){
       
        $query = $this->readdb->select("b.id,b.branchname")
                ->from($this->_table." as b")
                ->where('b.status = 1')
                ->order_by('branchname','ASC')
                ->get();
       
        return $query->result_array();
    }
    function getUserBranchData($menu,$viewData){
        
        $where = '1=1';
        if($menu=='mainmenu'){
            $mainmenu = 'menu';
        }else{
            $mainmenu = $menu;
        }
        if (isset($viewData[$menu.'visibility'][$mainmenu.'viewalldata']) && strpos($viewData[$menu.'visibility'][$mainmenu.'viewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            /* $ADMINID = $this->session->userdata(base_url().'ADMINID');
            $this->load->model('User_model','User');
            $chidemp = $this->User->getchildemployee($ADMINID);
                AND FIND_IN_SET(b.addedby,"'.$chidemp['childemp'].','.$ADMINID.'")>0
            */
            $BRANCHID = $this->session->userdata(base_url().'ADMINHEADERUSERBRANCHID');
            $where = 'FIND_IN_SET(b.id,"'.$BRANCHID.'")>0';
        }
        $BRANCHID = $this->session->userdata(base_url().'ADMINHEADERUSERBRANCHID');
        $where = 'FIND_IN_SET(b.id,"'.$BRANCHID.'")>0';

        // var_dump($viewData[$menu.'visibility']);exit;
        
        $query = $this->readdb->select("b.id,b.branchname")
                ->from($this->_table." as b")
                ->where('b.status = 1')
                ->where($where)
                ->get();
                // echo $this->readdb->last_query();exit;

        return $query->result_array();
    }

    function getActiveBranchDataForUser(){
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $BRANCHID = $this->session->userdata(base_url().'ADMINUSERBRANCHID');
            $where = 'FIND_IN_SET(b.id,"'.$BRANCHID.'")>0';
        }else{
            $where = '1=1';
        }
        
        $query = $this->readdb->select("b.id,b.branchname")
                ->from($this->_table." as b")
                ->where('b.status = 1')
                ->where($where)
                ->order_by('branchname','ASC')
                ->get();
        
        return $query->result_array();
    }
}
?>