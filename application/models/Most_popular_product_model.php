<?php

class Most_popular_product_model extends Common_model {

        public $_table = tbl_mostpopularproduct;
        public $_fields = "*";
        public $_where = array();
        public $_except_fields = array();
        public $_order = array('pc1.priority' => 'ASC');
    
        //set column field database for datatable orderable
        public $column_order = array(null, 'name',"product",'createddate');
    
        //set column field database for datatable searchable 
        public $column_search = array('name',"((select name from ".tbl_product." as pc2 where pc2.id=pc1.productid))",'DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s")');
    
        function __construct() {
            parent::__construct();
        }
        function getproduct() {
            $this->readdb->select('id ,name');
            $this->readdb->from(tbl_product.' AS pc');
            $this->readdb->where("pc.status=1");
            $this->readdb->order_by('pc.priority ASC,name ASC');
            $query = $this->readdb->get();
           
            if($query->num_rows() == 0) {
                return array();
            } else {
                return $query->result_array();
            }
        }
        function _get_datatables() {
            $query = $this->readdb->select('id,productid,priority')
                ->from($this->_table)
                ->order_by("id","DESC")
                ->get();
    
            if ($query->num_rows() > 0) {
                return $query->result_array();
            } else {
                return false;
            }
        }
             //LISTING DATA
	

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		return $query->result();
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
    