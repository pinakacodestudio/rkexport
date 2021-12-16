<?php

class Productsection_model extends Common_model {

	//put your code here
	public $_table = tbl_productsection;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'name','displaytype','maxhomeproduct'); //set column field database for datatable orderable
	public $column_search = array('name','displaytype','maxhomeproduct'); //set column field database for datatable searchable 
	public $order = array('priority' => 'asc'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getProductsectionDataByID($ID){
		$query = $this->readdb->select("id,channelid,name,displaytype,status,maxhomeproduct")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}

	function getapplicationproductsection($memberid=0,$channelid=0){
		//if(APPLICATIONPRODUCTSECTION!=""){
			$this->readdb->select("id,name");
			$this->readdb->from($this->_table);
			$this->readdb->where(array("status"=>1));
			if($memberid!=0 || $channelid!=0){
				$this->readdb->where('addedby IN (SELECT id FROM '.tbl_member.' WHERE id='.$memberid.' AND channelid='.$channelid.')');
			}
			$query = $this->readdb->get();
			return $query->result_array();
		/* }else{
			return array();
		} */
	}

	//LISTING DATA
	function _get_datatables_query(){
		
		$channelid = $_REQUEST['channelid'];

		$this->readdb->select("id,name,channelid,displaytype,status,priority,maxhomeproduct");
		$this->readdb->from($this->_table);
		
		if($channelid != 0){
			$this->readdb->where("channelid=".$channelid);
		}

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
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}
}
