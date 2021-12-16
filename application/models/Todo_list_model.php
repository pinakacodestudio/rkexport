<?php

class Todo_list_model extends Common_model {

    //put your code here
    public $_table = tbl_todolist;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = "tdl.id DESC";
    public $column_order = array(null,'u.name','tdl.list','assignby','tdl.status','tdl.createdate'); //set column field database for datatable orderable
    public $column_search = array('DATE_FORMAT(tdl.createdate,"%d/%m/%Y %h:%i %p")','u.name','tdl.list'); //set column field database for datatable searchable 
    public $order = array("tdl.priority"=>"ASC"); // default order 

    function __construct() {
        parent::__construct();
    }
    
/*     function getLeave($id=0) {
        $this->db->select("employeeleave.id,name,email,fromdate,todate,reason,halfleave,employeeleave.status");
        $this->db->from($this->_table);
        if($id!=0){
            $this->db->where("employeeleave.employeeid=".$id);
        }
        $this->db->join("employee","employeeleave.employeeid=employee.id");
        $this->db->order_by($this->_order);
    
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    function getsingleLeave($id) {
        $this->db->select("employeeleave.id,name,email,image,mobileno,fromdate,todate,reason,remarks,employeeleave.halfleave,employeeleave.leavetype,employeeleave.status");
        $this->db->from($this->_table);
        $this->db->where("employeeleave.id=".$id);
        $this->db->join("employee","employeeleave.employeeid=employee.id");
        $this->db->order_by($this->_order);
    
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->row_array();
        } else {
            return false;
        }
    } */

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
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        $childemployee = $this->User->getchildemployee($ADMINID);
        if($childemployee['childemp'] == ''){
            $_where = "u.id=".$ADMINID."";
          }else{
            $childemployee = implode(',',$childemployee);
            $_where = "u.id IN (".$childemployee.",".$ADMINID.") "; 
        }
        $this->db->select("tdl.id,u.name,tdl.employeeid,tdl.list,(select name from ".tbl_user." where id = tdl.addedby) as assignby,tdl.status,tdl.createdate,tdl.priority");
        $this->db->from($this->_table." as tdl");
        $this->db->join(tbl_user." as u","u.id = tdl.employeeid");
        if($this->session->userdata(base_url().'ADMINUSERPROFILEID') != "1") {
            $this->db->where($_where);
        }
        $PostData = $this->input->post();
		if(isset($PostData['filterstatus']) && $PostData['filterstatus']!=""){
			$this->db->where(array("tdl.status"=>$PostData['filterstatus']));
		}
		if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
			$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
			$todate = $this->general_model->convertdate($_REQUEST['todate']);
			$this->db->where("(date(tdl.createdate) >= '".$fromdate."' AND date(tdl.createdate) <= '".$todate."')");
        }
        if(isset($PostData['filteremployee']) && $PostData['filteremployee']!="" && $PostData['filteremployee']!="-1"){
			$this->db->where(array("tdl.employeeid"=>$PostData['filteremployee']));
		}
        
        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                $_POST['search']['value'] = trim($_POST['search']['value']);
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order)){
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        // $this->db->order_by($this->order);
        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all() {
        $this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
    }
    
    function searchtodolist($type,$search){
        
		$this->db->select("tdl.id,tdl.list as text");
		$this->db->from($this->_table." as tdl");
		//$this->readdb->where("t.channelid=".$channelid." AND t.memberid=".$memberid);
		if($type==1){
			$this->db->where("tdl.list LIKE '%".$search."%'");
		}else{
			$this->db->where("FIND_IN_SET(tdl.id,'".$search."')>0");
		}
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
			/* if($type==1){
				return $query->result_array();
			}else{
				return $query->row_array();
			} */
		}else {
			return 0;
		}	
	}

}



