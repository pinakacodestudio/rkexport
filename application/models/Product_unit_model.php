<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_unit_model extends Common_model {

	public $_table = tbl_productunit;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'name','createddate');

	//set column field database for datatable searchable 
	public $column_search = array('name','createddate');

	function __construct() {
		parent::__construct();
    }
    
    function getActiveProductUnit($MEMBERID=0,$CHANNELID=0){
       
        $query = $this->readdb->select("id,name")
							->from($this->_table)
							->where("memberid='".$MEMBERID."' AND channelid='".$CHANNELID."' AND status=1")
							->order_by("id DESC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	function getProductUnitDataByID($ID){
       
        $query = $this->readdb->select("id,name,status")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
	}
	function getActiveBrand(){
       
        $query = $this->readdb->select("id,name")
							->from($this->_table)
							->where("status", "1")
							->order_by("id", "DESC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
	}
	
	function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			return $query->result();
		}
	}

	function _get_datatables_query($MEMBERID,$CHANNELID){
		
		$this->readdb->select('id,name,createddate,status');
		$this->readdb->from($this->_table);
		$this->readdb->where("memberid='".$MEMBERID."' AND channelid='".$CHANNELID."'");
        
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

	function count_all($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		return $this->readdb->count_all_results();
	}

	function count_filtered($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		$query = $this->readdb->get();
		return $query->num_rows();
	}


}
 ?>            
