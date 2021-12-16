<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Unit_conversation_model extends Common_model {

	public $_table = tbl_unitconversation;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('uc.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'productname','inputunitname','uc.inputunitvalue','outputunitname','uc.outputunitvalue','uc.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('p.name','(SELECT name FROM '.tbl_productunit.' WHERE id=uc.inputunitid)','uc.inputunitvalue','(SELECT name FROM '.tbl_productunit.' WHERE id=uc.outputunitid)','uc.outputunitvalue','uc.createddate');

	function __construct() {
		parent::__construct();
    }
    
    function getUnitConversationDataByID($ID){
       
        $query = $this->readdb->select("id,productid,inputunitid,inputunitvalue,outputunitid,outputunitvalue")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
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

	function _get_datatables_query(){
		$memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $this->readdb->select('uc.id,
                            IFNULL(p.name,"") as productname,
                            uc.inputunitid,uc.inputunitvalue,
                            (SELECT name FROM '.tbl_productunit.' WHERE id=uc.inputunitid) as inputunitname,
                            uc.outputunitid,uc.outputunitvalue,
                            (SELECT name FROM '.tbl_productunit.' WHERE id=uc.outputunitid) as outputunitname,
                            uc.createddate');
		$this->readdb->from($this->_table." as uc");
		$this->readdb->where("uc.channelid='".$channelid."' AND uc.memberid='".$memberid."'");
        $this->readdb->join(tbl_product." as p","p.id=uc.productid","LEFT");
        
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
