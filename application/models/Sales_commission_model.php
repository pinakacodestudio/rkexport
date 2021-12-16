<?php

class Sales_commission_model extends Common_model {
//put your code here
	public $_table = tbl_salescommission;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array("sc.id"=>"DESC");
	public $column_order = array(null,'employeename','typename',null,'sc.createddate'); //set column field database for datatable orderable
	public $column_search = array('u.name',"(CASE WHEN sc.commissiontype=1 THEN 'Flat Commission' WHEN sc.commissiontype=2 THEN 'Product Base Commission' WHEN sc.commissiontype=3 THEN 'Member Base Commission' ELSE 'Tiered Commission' END)", "sc.createddate"); //set column field database for datatable searchable 
	//public $order = "id DESC"; // default order 

	function __construct() {
		parent::__construct();
	}

    function getCommissionByType($salescommissionid,$referencetype,$referenceid=""){
        
        $this->readdb->select("scd.id,scd.commission,scd.gst");
        $this->readdb->from(tbl_salescommissiondetail." as scd");
        if($referencetype!=1 && $referenceid!=""){
            if($referencetype == 4){
                $this->readdb->join(tbl_salescommissionmapping." as scm","scm.salescommissiondetailid=scd.id AND scm.referencetype=".$referencetype." AND ('".$referenceid."' BETWEEN scm.startrange AND scm.endrange)","INNER");
            }else{
                $this->readdb->join(tbl_salescommissionmapping." as scm","scm.salescommissiondetailid=scd.id AND scm.referencetype=".$referencetype." AND scm.referenceid=".$referenceid,"INNER");
            }
        }
        $this->readdb->where("scd.salescommissionid='".$salescommissionid."'");
        $query = $this->readdb->get();
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }
    }
    function checkCommissionEnable($id){
		$query = $this->readdb->select("count(sc.id) as count")
								->from($this->_table." as sc")
								->where("sc.status=1 AND sc.employeeid IN (SELECT employeeid FROM ".tbl_salescommission." WHERE id='".$id."')")
								->get()
								->row_array();

		if($query['count']==0){
			return 0;
		}else{
			return $query['count'];
		}
    }
    function getActiveSalesCommission(){

        $query = $this->readdb->select("sc.id,sc.employeeid,sc.commissiontype")
							->from($this->_table." as sc")
							->where("sc.commissiontype!=2 AND sc.status=1")
							->order_by("sc.id ASC")
                            ->limit(1)
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    function getEmployeeActiveSalesCommission($employeeid){
       
        $query = $this->readdb->select("sc.id,sc.employeeid,sc.commissiontype")
							->from($this->_table." as sc")
							->where("employeeid='".$employeeid."' AND status=1")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    function getActiveProductBaseCommission($productid){
       
        $query = $this->readdb->select("sc.employeeid,scd.commission,scd.gst")
                            ->from(tbl_salescommissionmapping." as scm")
                            ->join(tbl_salescommissiondetail." as scd","scd.id = scm.salescommissiondetailid")
                            ->join($this->_table." as sc","sc.id=scd.salescommissionid AND sc.commissiontype=2 AND sc.status=1")
                            ->where("scm.referencetype = 2 AND scm.referenceid = '".$productid."'")
                            ->order_by("sc.id ASC")
                            ->limit(1)
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    function getSalesCommissionDataByID($ID){
       
        $query = $this->readdb->select("sc.id,sc.employeeid,sc.commissiontype")
							->from($this->_table." as sc")
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }

    function getCommissionDetailBySalesCommissionID($salescommissionid){
       
        $query = $this->readdb->select("scd.id,scd.salescommissionid,scd.commission,scd.gst,
                    IFNULL(scm.id,'') as mappingid,
                    IFNULL(scm.referenceid,'') as referenceid,
                    IFNULL(scm.referencetype,'') as referencetype,
                    IFNULL(scm.startrange,'') as startrange,
                    IFNULL(scm.endrange,'') as endrange,
                    
                    IFNULL(IF(scm.referencetype=2,(SELECT name FROM ".tbl_product." WHERE id=scm.referenceid),''),'') as productname,
                    IFNULL(IF(scm.referencetype=3,(SELECT name FROM ".tbl_member." WHERE id=scm.referenceid),''),'') as membername,

                    IFNULL(IF(scm.referencetype=3,(SELECT channelid FROM ".tbl_member." WHERE id=scm.referenceid),''),'') as memberchannelid,

                    (SELECT name FROM ".tbl_user." WHERE id=sc.employeeid) as employeename
                ")
                            ->from(tbl_salescommissiondetail." as scd")
                            ->join(tbl_salescommission." as sc","scd.salescommissionid=sc.id","INNER")
                            ->join(tbl_salescommissionmapping." as scm","scm.salescommissiondetailid=scd.id","LEFT")
							->where("scd.salescommissionid='".$salescommissionid."'")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }

    //LISTING DATA IN ADMIN
	function _get_datatables_query($type=1){

		$employeeid = (!empty($_REQUEST['employeeid']))?$_REQUEST['employeeid']:'0';
		$commissiontype = (!empty($_REQUEST['commissiontype']))?$_REQUEST['commissiontype']:'0';

        if($type==1){
			$this->readdb->select("sc.id,sc.employeeid,u.name as employeename,sc.commissiontype,
								CASE 
									WHEN sc.commissiontype=1 THEN 'Flat Commission'
									WHEN sc.commissiontype=2 THEN 'Product Base Commission'
									WHEN sc.commissiontype=3 THEN 'Member Base Commission'
									ELSE 'Tiered Commission'
								END as typename,
                                sc.createddate,sc.status,
                                
                                IF(sc.commissiontype=1,(SELECT commission FROM ".tbl_salescommissiondetail." WHERE salescommissionid=sc.id LIMIT 1),'') as flatcommission,
                                IF(sc.commissiontype=1,(SELECT gst FROM ".tbl_salescommissiondetail." WHERE salescommissionid=sc.id LIMIT 1),'') as flatgst
			");
		}else{
			$this->readdb->select("sc.id");
		}
		
		$this->readdb->from($this->_table." as sc");
        $this->readdb->join(tbl_user." as u","u.id=sc.employeeid","INNER");
		$this->readdb->where("(sc.employeeid='".$employeeid."' OR ".$employeeid."=0) AND (sc.commissiontype='".$commissiontype."' OR ".$commissiontype."=0)");

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

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            //echo $this->readdb->last_query(); exit;
            return $query->result();
        }
    }

    function count_all() {
        $this->_get_datatables_query();
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

}