<?php

class Company_model extends Common_model {

	//put your code here
	public $_table = tbl_company;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('id' => 'DESC');
	public $column_order = array('id', 'name');
    public $column_search = array('name');
	
	function __construct() {
		parent::__construct();
	}
	
	function getcompanyDataByID($ID){
		$query = $this->readdb->select("id,companyname,email,createddate,modifieddate,addedby,modifiedby")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
    }
    function getAdditionalrightsList() {
        $query = $this->readdb->select("id,companyname")
							->from($this->_table)
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();;
		}
    }
    function get_datatables() {
	   $this->_get_datatables_query();
	    if($_POST['length'] != -1) {
	        $this->readdb->limit($_POST['length'], $_POST['start']);
	        $query = $this->readdb->get();
	        //echo $this->readdb->last_query();
	        return $query->result();
	    }
    }
	function _get_datatables_query(){  

        $this->readdb->select("id,companyname,email,modifieddate,createddate,modifiedby,createddate,addedby");
        $this->readdb->from($this->_table." as ar");
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
        $this->_get_datatables_query();
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }
}
