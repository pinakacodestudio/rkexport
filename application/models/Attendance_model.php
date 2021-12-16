<?php

class Attendance_model extends Common_model {

    //put your code here
    public $_table = tbl_attendance;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = "at.id DESC";
    public $column_order = array(null,'u.name','at.date',null,'at.checkintime','at.checkouttime'); //set column field database for datatable orderable
    public $column_search = array('u.name','at.date','at.checkintime','at.checkouttime','at.totaltime'); //set column field database for datatable searchable 
    public $order = "at.id DESC"; // default order 

    function __construct() {
        parent::__construct();
    }
    
    function getEmployeeAttendance($employeeid) {   
        
        $query = $this->readdb->select("*")
                    ->from(tbl_attendance)
                    ->where("employeeid='".$employeeid."' and status=1")
                    ->order_by("id DESC")
                    ->get();
                        
        return $query->row_array();
    }
    function getCurrentDateEmployeeAttendance() {   
        
        $createddate = $this->general_model->convertdate($this->general_model->getCurrentDateTime());

        $query = $this->readdb->select("a.id,a.employeeid,a.status,a.checkintime as checkin,a.checkouttime as checkout,u.checkintime,
        a.breakintime,a.breakouttime")
                    ->from(tbl_attendance." as a")
                    ->join(tbl_user." as u","u.id = a.employeeid","LEFT")
                    ->where("a.employeeid = '".$this->session->userdata(base_url().'ADMINID')."' and a.date = '".$createddate."'")
                    ->order_by("a.createddate DESC")
                    ->limit(1)
                    ->get();
                        
        return $query->row_array();
    }
    function getCurrentDateEmployeeNonAttendance() {   
        
        $createddate = $this->general_model->convertdate($this->general_model->getCurrentDateTime());

        $query = $this->readdb->select("na.id,na.employeeid,na.nastarttime,na.naendtime,na.date")
                    ->from(tbl_nonattendance." as na")
                    ->join(tbl_user." as u","u.id = na.employeeid","LEFT")
                    ->where("na.employeeid = '".$this->session->userdata(base_url().'ADMINID')."' and date(na.date) = '".$createddate."'")
                    ->order_by("na.date DESC")
                    ->limit(1)
                    ->get();
                        
        return $query->row_array();
    }
    function getCountCurrentDateEmployeeAttendance($employeeid) {   
        
        $query = $this->readdb->select("count(*) as count")
                    ->from(tbl_attendance." as a")
                    ->where("date = '".date('Y-m-d')."' and employeeid='".$employeeid."'")
                    ->get();
                        
        $data = $query->row_array();
        return $data['count'];
    }

    function _get_datatables_query(){   
        $this->readdb->select("at.id,u.name,at.date,at.checkintime,at.checkouttime,at.checkinip,at.checkoutip,
                        at.breakintime,at.breakouttime,at.breakinip,at.breakoutip,
                        (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(n.naendtime))) FROM ".tbl_nonattendance." as n WHERE n.employeeid = at.employeeid AND DATE_FORMAT(n.date,'%Y-%m-%d') = at.date AND n.attendanceid = at.id) as naendtime,
                        (SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(n.nastarttime))) FROM ".tbl_nonattendance." as n WHERE n.employeeid = at.employeeid AND DATE_FORMAT(n.date,'%Y-%m-%d') = at.date AND n.attendanceid = at.id) as nastarttime,at.image
                        ");
        $this->readdb->from($this->_table." as at");
        $this->readdb->join(tbl_user." as u","u.id=at.employeeid");       
        $PostData = $this->input->post();
        
        if(isset($PostData['employee']) && $PostData['employee']!="" && $PostData['employee']!="-1"){			
			$this->readdb->where("at.employeeid = '".$PostData['employee']."'");
        }
						
		if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
			$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
			$todate = $this->general_model->convertdate($_REQUEST['todate']);
			$this->readdb->where("(at.date >= '".$fromdate."' AND at.date <= '".$todate."')");
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
        $this->readdb->order_by($this->order);
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



