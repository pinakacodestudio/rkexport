<?php

class Attendance_report_model extends Common_model {

    //put your code here
    public $_table = tbl_attendance;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = "at.id DESC";
    public $column_order = array(null,'e.name','at.date','at.checkintime','at.checkouttime',null,null,null); //set column field database for datatable orderable
    public $column_search = array('e.name','at.date','at.checkintime','at.checkouttime'); //set column field database for datatable searchable 
    public $order = array("at.id"=>"DESC"); // default order 

    function __construct() {
        parent::__construct();
	}
		     
    function _get_datatables_query(){   
        
        $this->readdb->select("at.id,e.name,at.date,at.checkintime,at.checkouttime,at.checkinip,at.checkoutip,
        at.breakintime,at.breakouttime,at.breakinip,at.breakoutip,
        (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(n.naendtime))) FROM nonattendance as n WHERE n.employeeid = at.employeeid AND str_to_date(n.date,'%Y-%m-%d') = str_to_date(at.date,'%Y-%m-%d') AND n.attendanceid = at.id) as naendtime,
        (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(n.nastarttime))) FROM nonattendance as n WHERE n.employeeid = at.employeeid AND str_to_date(n.date,'%Y-%m-%d') = str_to_date(at.date,'%Y-%m-%d') AND n.attendanceid = at.id) as nastarttime,
        (select latitude from ".tbl_locationtracking." where employeeid=at.employeeid and attendanceid = at.id order by createddate limit 1) as latitude,
        (select longitude from ".tbl_locationtracking." where employeeid=at.employeeid and attendanceid = at.id order by createddate limit 1) as longitude,
        (select checkoutlatitude from ".tbl_locationtracking." where employeeid=at.employeeid and attendanceid = at.id order by createddate limit 1) as checkoutlatitude,
        (select checkoutlongitude from ".tbl_locationtracking." where employeeid=at.employeeid and attendanceid = at.id order by createddate limit 1) as checkoutlongitude,
        at.image");
        $this->readdb->from($this->_table." as at");
        $this->readdb->join(tbl_user." as e","e.id=at.employeeid");
        $PostData = $this->input->post();
		
		if(isset($PostData['employeeid']) && $PostData['employeeid']!="0"){			
			$this->readdb->where("at.employeeid = '".$PostData['employeeid']."'");
        }
		
		if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
			$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
			$todate = $this->general_model->convertdate($_REQUEST['todate']);
			$this->readdb->where("(at.date BETWEEN '".$fromdate."' AND '".$todate."')");
        }
		
		$i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                $_POST['search']['value'] = trim($_POST['search']['value']);
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
        
        if(isset($_POST['order'])) // here order processing
        {
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order)){
            $order = $this->order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->readdb->limit($_POST['length'], $_POST['start']);
        // $this->readdb->order_by($this->order);
        $query = $this->readdb->get();
        //echo $this->readdb->last_query();exit;
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

    function count_all() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

}



