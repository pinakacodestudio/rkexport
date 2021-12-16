<?php

class Vehicle_document_model extends Common_model {

	//put your code here
	public $_table = tbl_vehicle;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
    public $_order = array('d.id' => 'DESC');
    public $column_order = array(null,'documenttype','d.documentnumber','d.fromdate','d.duedate');
    public $column_search = array('dt.documenttype','d.documentnumber','DATE_FORMAT(d.fromdate , "%d/%m/%Y")','DATE_FORMAT(d.duedate , "%d/%m/%Y")');
    
	function __construct() {
		parent::__construct();
    }
    
    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
        
            return $query->result();
        }
    }

	function _get_datatables_query(){  
        
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        
		$this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,d.createddate,
				d.documentnumber,d.fromdate,d.duedate,d.documentfile,dt.documenttype,
		");

		$this->readdb->from(tbl_document." as d");
        $this->readdb->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER");
        $this->readdb->where("d.referencetype=0 AND d.referenceid=".$vehicleid);

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
