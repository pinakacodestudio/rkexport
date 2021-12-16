<?php

class Challan_detail_model extends Common_model {

	//put your code here
	public $_table = tbl_challandetail;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(); //set column field database for datatable orderable
	public $column_search = array(); //set column field database for datatable searchable 
	public $_order = array('c.id' => 	'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getdchallantypeDataByID($ID){
		$query = $this->readdb->select("c.id,c.partytypeid,c.vehicleid")
							->from($this->_table." as c")
							->where("c.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	
	//LISTING DATA
	function _get_datatables_query($type=1){
        
        if(!is_null($this->session->userdata(base_url().'MEMBERID'))){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
        }else{
            $memberid = $channelid = 0;
        }

        if($type == 0){
            $this->readdb->select("c.id");
        }else{
            $this->readdb->select("c.id,c.partytypeid,c.vehicleid,v.vehiclename,v.vehicleno");
        }                                
        $this->readdb->from($this->_table." as c");
        $this->readdb->join(tbl_vehicle . " as v", "v.id=c.vehicleid", "LEFT");
        // $this->readdb->join(tbl_challandetail . " as cd", "c.id=cd.challanid", "LEFT");

        
        
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
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
        $query = $this->readdb->get();
		return $query->result();
	}

	function count_filtered($ADDEDBY=0,$MODIFIEDBY=0) {
		$this->_get_datatables_query($ADDEDBY,$MODIFIEDBY);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
    }
    
}
?>