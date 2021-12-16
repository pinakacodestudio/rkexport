<?php

class Order_to_delivered_model extends Common_model {
//put your code here
	public $_table = tbl_member;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'m.name','countorder','partiallydelivered','cancelorder','orderdelivered'); //set column field database for datatable orderable
	public $column_search = array('m.name'); //set column field database for datatable searchable 
	public $order = array('m.name' => 'ASC');

	function __construct() {
		parent::__construct();
	}

    
		
	function _get_datatables_query($type=1){	
		
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
		$todate = $this->general_model->convertdate($_REQUEST['todate']);
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:0;
        $memberid = (!empty($_REQUEST['memberid']))?implode(",",$_REQUEST['memberid']):'';
	
		if(is_null($MEMBERID)){
			$this->readdb->select("m.id,m.name,m.membercode,m.channelid,
		
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status!=0 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as countorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status!=0 AND approved=3 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as partiallydelivered,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status=2 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as cancelorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as orderdelivered");
					
		}
		else{
			$this->readdb->select("m.id,m.name,m.membercode,m.channelid,
		
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status!=0 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as countorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status!=0 AND approved=3 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as partiallydelivered,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=2 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as cancelorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as orderdelivered");		
		}
							
		$this->readdb->from($this->_table." as m");
		$this->readdb->where("m.channelid NOT IN(".VENDORCHANNELID.",".GUESTCHANNELID.") AND m.status=1 AND (m.channelid=".$channelid." OR ".$channelid."=0) AND (FIND_IN_SET(m.id,'".$memberid."')>0 OR '".$memberid."'='')");
				
		if(!is_null($MEMBERID)){
			$this->readdb->where("(m.id IN(SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.") OR m.id IN(select memberid from ".tbl_orders." where sellermemberid=".$MEMBERID." AND isdelete=0))");
		}
		

		$i = 0;
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				$_POST['search']['value'] = trim($_POST['search']['value']);
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
			
			if(isset($_POST['order'])) // here order processing
			{
				$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			} 
			else if(isset($this->order)){
				$order = $this->order;
				$this->readdb->order_by(key($order), $order[key($order)]);
			}
		}
	}
    
    function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		// echo $this->db->last_query();exit;
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
	//Export Data
	function exporttoexcelordertodeliveredreport() {
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
		$todate = $this->general_model->convertdate($_REQUEST['todate']);
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:0;
		$memberid = (!empty($_REQUEST['memberid']))?$_REQUEST['memberid']:'';

		if(is_null($MEMBERID)){
			$this->readdb->select("m.id,m.name,m.membercode,m.channelid,
		
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status!=0 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as countorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status!=0 AND approved=3 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as partiallydelivered,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status=2 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as cancelorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as orderdelivered");
					
		}
		else{
			$this->readdb->select("m.id,m.name,m.membercode,m.channelid,
		
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status!=0 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as countorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status!=0 AND approved=3 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as partiallydelivered,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=2 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as cancelorder,

				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as orderdelivered");		
		}
						
		$this->readdb->from($this->_table." as m");
		$this->readdb->where("m.channelid NOT IN(".VENDORCHANNELID.",".GUESTCHANNELID.") AND m.status=1 AND (m.channelid=".$channelid." OR ".$channelid."=0) AND (FIND_IN_SET(m.id,'".$memberid."')>0 OR '".$memberid."'='')");
		
		if(!is_null($MEMBERID)){
			$this->readdb->where("(m.id IN(SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.") OR m.id IN(select memberid from ".tbl_orders." where sellermemberid=".$MEMBERID." AND isdelete=0))");
		}
		$this->readdb->order_by("m.id ASC");
		$query = $this->readdb->get();
		
		return $query->result();
	}
	//Get Report Data On API
	function getOrderToDeliveredReportDataOnAPI($sellerid,$buyerchannelid,$buyerid,$fromdate,$todate,$counter) {
		$limit = 10;
		$this->readdb->select("m.id,m.name,m.membercode,m.channelid,
		
			IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$sellerid." AND memberid=m.id AND status!=0 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as countorder,

			IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$sellerid." AND memberid=m.id AND status!=0 AND approved=3 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as partiallydelivered,

			IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$sellerid." AND memberid=m.id AND status=2 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as cancelorder,

			IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$sellerid." AND memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as orderdelivered");		
						
		$this->readdb->from($this->_table." as m");
		$this->readdb->where("m.channelid NOT IN(".VENDORCHANNELID.",".GUESTCHANNELID.") AND m.status=1 AND (m.channelid=".$buyerchannelid." OR ".$buyerchannelid."=0) AND (FIND_IN_SET(m.id,'".$buyerid."')>0 OR '".$buyerid."'='')");
		
		$this->readdb->where("(m.id IN(SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$sellerid.") OR m.id IN(select memberid from ".tbl_orders." where sellermemberid=".$sellerid." AND isdelete=0))");
		
		$this->readdb->order_by("m.name ASC");
		if($counter != -1){
			$this->readdb->limit($limit,$counter);
        }
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
}


