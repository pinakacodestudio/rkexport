<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offer_orders_model extends Common_model {
	public $_table = tbl_offer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('o.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'membername','sellermembername','o.orderid', 'o.orderdate','o.status','o.approved','netamount');

	//set column field database for datatable searchable 
    public $column_search = array('m.name','m2.name', 'o.orderid', 'o.orderdate','(payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=o.id AND type=0),0))','m.membercode','m2.membercode');
    
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
        $status = $_REQUEST['status'];
        $offerid = $_REQUEST['offerid'];
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $this->readdb->select('o.id,o.orderid,o.status,o.type,
                        (select sum(finalprice) from '.tbl_orderproducts.' where orderid = o.id ) as finalprice, 
                        o.orderdate,   
                        o.createddate as date, o.memberid, 
                        m.name as membername,m.channelid,
                        (payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=o.id AND type=0),0)) as netamount,o.approved,addordertype,
                        m2.name as sellermembername,
                        m2.channelid as sellerchannelid,
                        o.sellermemberid,
                        m.membercode as membercode,
                        m2.membercode as sellermembercode,
                        IF(o.type=0,"Company",(select name from '.tbl_member.' where id=o.addedby)) as addedby,
                        IF(o.type=0,"",(select membercode from '.tbl_member.' where id=o.addedby)) as addedbycode,
                        IF(o.type=0,"0",(select channelid from '.tbl_member.' where id=o.addedby)) as addedbychannelid,
                        o.addedby as addedbyid,
                        
                ');

        $this->readdb->from(tbl_orders." as o");
        $this->readdb->join(tbl_member." as m","m.id=o.memberid","INNER");
        $this->readdb->join(tbl_member." as m2","m2.id=o.sellermemberid","LEFT");
		$this->readdb->where("o.isdelete=0 AND o.id IN (SELECT orderid FROM ".tbl_orderproducts." WHERE offerproductid IN (SELECT id FROM ".tbl_offerproduct." WHERE offerid='".$offerid."'))");
		if(!is_null($MEMBERID)){
			$this->readdb->where("o.memberid = '".$MEMBERID."'");
		}
        if($status != -1){
            $this->readdb->where("o.status=".$status);
        }
        $this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."')");
        
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