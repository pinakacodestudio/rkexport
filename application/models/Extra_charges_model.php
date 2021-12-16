<?php

class Extra_charges_model extends Common_model {

	//put your code here
	public $_table = tbl_extracharges;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('ec.id' => 'DESC'); // default order 
	public $column_order = array(null,'ec.name','hsncode','ec.amounttype','ec.defaultamount','chargetypename'); //set column field database for datatable orderable
	public $column_search = array('h.hsncode','ec.name','ec.defaultamount',"(IF(ec.amounttype=0,'Percentage','Amount'))","(IF(ec.chargetype=0,'Overall','Pcs Wise'))"); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
    }
	
    function getExtrachargesDataByID($ID){
        
        $query=$this->readdb->select("id,hsncodeid,name,amounttype,defaultamount,chargetype,status")
                        ->from($this->_table)
                        ->where("id", $ID)
                        ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
	}
	//LISTING DATA
	function _get_datatables_query(){
		
		$this->readdb->select("ec.id,ec.hsncodeid,ec.name,ec.amounttype,ec.defaultamount,ec.type,ec.status,
            IFNULL(CONCAT(h.hsncode,' (',h.integratedtax,'%)'),'-')  as hsncode,
			ec.chargetype,
			IF(ec.chargetype=0,'Overall','Pcs Wise') as chargetypename
        ");
		$this->readdb->from($this->_table." as ec");
		$this->readdb->join(tbl_hsncode." as h","h.id=ec.hsncodeid","LEFT");
		
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
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
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
