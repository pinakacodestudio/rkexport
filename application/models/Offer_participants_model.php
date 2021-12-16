<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offer_participants_model extends Common_model {
	public $_table = tbl_offer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('op.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'membername','m.mobile',null,null,'op.status','op.createddate');

	//set column field database for datatable searchable 
    public $column_search = array('m.name', 'm.mobile', 'm.email', 'op.adminnotes', 'op.membernotes', 'op.createddate','m.membercode');
    
	function __construct() {
		parent::__construct();
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			// echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}

	function _get_datatables_query(){
		
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $offerid = $_REQUEST['offerid'];
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->readdb->select('op.id,m.name as membername,op.status,
						op.createddate,op.memberid,op.offerid, 
                        m.channelid,
                        m.membercode as membercode,
						m.name as membername,m.channelid,
						m.email,m.mobile,
                        op.adminnotes,op.membernotes
                ');

        $this->readdb->from(tbl_offerparticipants." as op");
        $this->readdb->join(tbl_member." as m","m.id=op.memberid","LEFT");
		$this->readdb->where("op.offerid='".$offerid."'");
		if(!is_null($MEMBERID)){
			$this->readdb->where("(op.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid='".$MEMBERID."') OR op.memberid='".$MEMBERID."')");
		}
        $this->readdb->where("(date(op.createddate) BETWEEN '".$startdate."' AND '".$enddate."')");
        
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