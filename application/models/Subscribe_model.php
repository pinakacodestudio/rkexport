<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribe_model extends Common_model {

	public $_table = tbl_subscribe;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'email','createddate');

	//set column field database for datatable searchable 
	public $column_search = array('email','createddate');

	function __construct() {
		parent::__construct();
    }
    
    function getActivesubscribe(){
       
        $query = $this->readdb->select("id,email")
							->from($this->_table)
							->where("status=1")
							->order_by("id DESC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductUnitDataByID($ID){
       
        $query = $this->readdb->select("id,email,status")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
	}
	

	function _get_datatables_query(){

		$channelid = $this->session->userdata(base_url().'CHANNELID');
		$memberid = $this->session->userdata(base_url().'MEMBERID');

		$this->readdb->select('id,email,createddate,status');
		$this->readdb->from($this->_table);
		$this->readdb->where("channelid='".$channelid."' AND memberid='".$memberid."'");
        
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
			$order = $this->_order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}
		
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
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
 ?>            
