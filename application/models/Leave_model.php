<?php

class Leave_model extends Common_model {

    //put your code here
    public $_table = tbl_leave;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = "el.id DESC";
    public $column_order = array(null,'el.createddate','u.name',null,'el.fromdate','el.todate',null,'paidunpaid',null); //set column field database for datatable orderable
    public $column_search = array('DATE_FORMAT(el.createddate,"%d/%m/%Y")','u.name','DATE_FORMAT(el.fromdate,"%d/%m/%Y")','DATE_FORMAT(el.todate,"%d/%m/%Y")','el.reason','el.status'); //set column field database for datatable searchable 
    public $order = "el.id DESC"; // default order 
    
    function __construct() {
        parent::__construct();
    }
    
    function getLeave($id=0) {
        $this->readdb->select("employeeleave.id,name,email,fromdate,todate,reason,halfleave,employeeleave.status,leavetype");
        $this->readdb->from($this->_table);
        if($id!=0){
            $this->readdb->where("employeeleave.employeeid=".$id);
        }
        $this->readdb->join("employee","employeeleave.employeeid=employee.id");
        $this->readdb->where("employee.isdelete","0");
        $this->readdb->order_by($this->_order);
    
        $query = $this->readdb->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    function getsingleLeave($id) {
        $this->readdb->select("employeeleave.id,name,email,image,mobileno,fromdate,todate,reason,remarks,employeeleave.halfleave,employeeleave.leavetype,employeeleave.status");
        $this->readdb->from($this->_table);
        $this->readdb->where("employeeleave.id=".$id);
        $this->readdb->join("employee","employeeleave.employeeid=employee.id");
        $this->readdb->order_by($this->_order);
    
        $query = $this->readdb->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    }

    function getUser()
	{
      $ADMINID = $this->session->userdata(base_url().'ADMINID');
      $childemployee = $this->User->getchildemployee($ADMINID);
      if($childemployee['childemp'] == ''){
        $_where = "id=".$ADMINID."";
      }else{
        $childemployee = implode(',',$childemployee);
        $_where = "id IN (".$childemployee.") OR id=".$ADMINID.""; 
      }
      $query = $this->readdb->select("id,name")
                  ->from(tbl_user)
                  ->where($_where)
                  ->get();
        return $query->result_array();
    }

    function _get_datatables_query(){  
        
        $this->readdb->select("el.employeeid,el.id,u.name,u.email,el.fromdate,el.todate,el.reason,el.leavetype,el.halfleave,el.paidunpaid,el.status,u.id as userid,el.granted,el.createddate");
        $this->readdb->from($this->_table." as el");
        $this->readdb->join(tbl_user." as u","el.employeeid=u.id");
        
                   
        $PostData = $this->input->post();
		if(isset($PostData['statusid']) && $PostData['statusid']!=""){
			$this->readdb->where("el.status = ".$PostData['statusid']);
		}
		if(isset($PostData['startdate']) && $PostData['startdate']!="" ){
			$fromdate = $this->general_model->convertdate($_REQUEST['startdate']);
			$this->readdb->where("DATE(el.fromdate) >= '".$fromdate."'");
        }
        if(isset($PostData['enddate']) && $PostData['enddate']!=""){
			$todate = $this->general_model->convertdate($_REQUEST['enddate']);
			$this->readdb->where("DATE(el.todate) <= '".$todate."'");
        }
        if(isset($PostData['userid']) && $PostData['userid']!="" && $PostData['userid']!="-1"){
			$this->readdb->where(array("el.employeeid"=>$PostData['userid']));
		}else{
            if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
                // $this->readdb->escape($this->session->userdata(base_url().'ADMINID')
                $this->readdb->where(array("(el.employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or el.employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))."))"=>null));	
            }
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
        
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->readdb->limit($_POST['length'], $_POST['start']);
        // $this->readdb->order_by($this->orderhr);
        $query = $this->readdb->get();
        // echo $this->readdb->last_query();exit;
        return $query->result_array();
    }

    function count_filtered() {
        $this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
    }

    function count_all() {
        $this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
    }

    function getEmployeeLeaves(){
        //SELECT l.*,(SELECT e.name FROM employee AS e WHERE e.id = l.employeeid) AS name, (SELECT t.name FROM technology AS t WHERE t.id = (SELECT e.technologyid FROM employee AS e WHERE e.id = l.employeeid)) AS technology FROM employeeleave AS l WHERE l.status = 1 AND ((fromdate BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)) OR (date(todate) = date(now()))) ORDER BY l.id DESC

        $this->readdb->select("l.*,(SELECT e.name FROM employee AS e WHERE e.id = l.employeeid) AS name, (SELECT t.name FROM technology AS t WHERE t.id = (SELECT e.technologyid FROM employee AS e WHERE e.id = l.employeeid)) AS technology");
        $this->readdb->from($this->_table." as l"); 
        $this->readdb->where("l.status = 1 AND ((fromdate BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)) OR (todate = CURDATE()))");
        $this->readdb->order_by("l.id DESC");
    
        $query = $this->readdb->get();
        //echo $this->readdb->last_query();exit;
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

}



