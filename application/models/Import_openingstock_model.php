<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_openingstock_model extends Common_model {
	public $_table = tbl_importleadexcel;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order ='id DESC';
	public $_datatableorder = array('ie.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'employeename','channel','membername','ie.file','ie.ipaddress','ie.totalrow','ie.createddate','employeename');

	//set column field database for datatable searchable 
	public $column_search = array('IFNULL(c.name,"Company")','m.name','ie.file','ie.ipaddress');

	function __construct() {
		parent::__construct();
	}

	

	function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			//echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}
	
	function _get_datatables_query($MEMBERID,$CHANNELID){
		$PostData = $this->input->post();
		

		$this->readdb->select('ie.id,ie.employeeid,ie.file,ie.ipaddress,ie.totalrow,ie.createddate,ie.channelid,ie.memberid,
			CASE
				WHEN ie.type=0 THEN "Admin"
				WHEN ie.type=1 THEN (SELECT name FROM '.tbl_member.' WHERE channelid=ie.channelid AND id=ie.memberid)
				ELSE "-"
			END as employeename,
			IFNULL(c.name,"Company") as channel,
			IFNULL(CONCAT(m.name," (",m.membercode,"%)"),"") as membername
		');

		$this->readdb->from($this->_table." as ie");
		$this->readdb->join(tbl_channel." as c","c.id=ie.channelid","LEFT");
		$this->readdb->join(tbl_member." as m","m.id=ie.memberid","LEFT");
		$this->readdb->where("ie.importfrom=1 AND ie.channelid='".$CHANNELID."' AND ie.memberid='".$MEMBERID."'");
		
	
		$this->readdb->group_by("ie.id");
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
		} else if(isset($this->_datatableorder)) {
			$order = $this->_datatableorder;
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
