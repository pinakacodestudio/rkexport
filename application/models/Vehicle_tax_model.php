<?php

class Vehicle_tax_model extends Common_model {

	//put your code here
	public $_table = tbl_vehicletax;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('vt.id' => 'DESC');
	public $column_order = array('vt.id','employeename', 'v.manufacturingcompany','vt.receiptno','vt.fromdate','vt.todate','vt.paymentdate');
    public $column_search = array('v.manufacturingcompany','v.registrationno','vt.receiptno','DATE_FORMAT(vt.fromdate, "%d/%m/%Y")','DATE_FORMAT(vt.todate, "%d/%m/%Y")','DATE_FORMAT(vt.paymentdate, "%d/%m/%Y")');

	function __construct() {
		parent::__construct();
	}

	function getvehicletaxDataByID($ID){
		$query = $this->readdb->select("vt.id,vt.vehicleid,vt.receiptno,vt.fromdate,vt.todate,vt.paymentdate,vt.proof,vt.taxamount,vt.status")
							->from($this->_table." as vt")
							->where("vt.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function get_datatables() {
	   $this->_get_datatables_query();
	    if($_POST['length'] != -1) {
	        $this->readdb->limit($_POST['length'], $_POST['start']);
	        $query = $this->readdb->get();
	        //echo $this->db->last_query();
	        return $query->result();
	    }
    }
	function _get_datatables_query($type=1){  
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        if($type == 0){
            $this->readdb->select("vt.id");
        }else{
            $this->readdb->select("vt.id,vt.vehicleid,vt.receiptno,vt.fromdate,vt.todate,vt.paymentdate,vt.proof,vt.taxamount,v.manufacturingcompany,v.registrationno,vt.status");
        }
        $this->readdb->from($this->_table." as vt");
        $this->readdb->where("vt.channelid='".$channelid."' AND vt.memberid='".$memberid."' ");
        $this->readdb->join(tbl_vehicle." as v", "v.id = vt.vehicleid", "LEFT");
        
        $i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
                if($i===0) // first loop
                {
                    $this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->readdb->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->readdb->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->readdb->group_end(); //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->_order)) {
            $order = $this->_order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    function count_all() {
        $this->_get_datatables_query(0);
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

    function getExpiredVehicletaxList(){

        if($this->session->userdata(base_url().'SCHOOL') != ""){
            $schoolorbranchid = $this->session->userdata(base_url().'SCHOOL');
        }else{
            $schoolorbranchid = 0;
        }

        $query = $this->db->select("vt.id,CONCAT(v.manufacturingcompany,' (',v.registrationno,')') as vehiclename,vt.receiptno,vt.todate as expiredate")
                          ->from($this->_table." as vt")
                          ->join(tbl_vehicle." as v", "v.id=vt.vehicleid", "LEFT")
                          ->where("date(vt.todate) < CURDATE() AND v.status=1 AND (IF(v.isanybranch=0, v.schoolorbranchid=".$schoolorbranchid.", schoolorbranchid in(SELECT id FROM ".tbl_branch." WHERE schoolid=".$schoolorbranchid.")) OR ".$schoolorbranchid."= 0)")
                          ->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return 0;
        }
    }
}
