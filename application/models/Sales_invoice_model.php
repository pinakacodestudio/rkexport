<?php

class Sales_invoice_model extends Common_model {

	//put your code here
	public $_table = tbl_orders;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('id' => 'DESC'); // default order 
	public $column_order = array(null,'o.orderid','buyername','o.orderdate','netamount','commissionamount','salespersonname','o.status'); //set column field database for datatable orderable
	public $column_search = array('o.orderid','buyer.name','buyer.membercode','o.orderdate'); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
	}
	
	function getSalesPersonProductCommission($orderid){
	
		$query = $this->readdb->select("SUM(price*quantity*commission/100) as commissionamount,(SELECT name FROM user WHERE id=salespersonid) as salesperson")
					->from(tbl_orderproducts)
					->where("orderid=".$orderid." AND salespersonid != 0")
					->group_by("salespersonid")
					->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
	function getRouteDataByID($routeid){
		
		$query = $this->readdb->select("r.id,r.route,r.totaltime,r.totalkm,r.cityid,r.provinceid")
					->from($this->_table." as r")
					->where("r.id='".$routeid."'")
					->get();
		
		if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
	}
	
    //LISTING DATA
	function _get_datatables_query(){
		
		$salespersonid = isset($_REQUEST['salespersonid'])?$_REQUEST['salespersonid']:'0';
        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:'0';
        $status = isset($_REQUEST['status'])?$_REQUEST['status']:'-1';
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("o.id,o.orderid,
                        buyer.id as buyerid,buyer.name as buyername,buyer.membercode as buyercode,
                        buyer.channelid as buyerchannelid,
						o.orderdate,
						@netamount:=(payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as netamount,

                        o.salespersonid,o.commission,o.commissionwithgst,
                        o.status,o.remarks,
						IFNULL((SELECT name FROM ".tbl_user." WHERE id=o.salespersonid),'') as salespersonname,
						
						IF(o.salespersonid!=0,(@netamount*o.commission/100),0) as commissionamount
                    ");
        
        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid","INNER");

        $this->readdb->where("o.memberid!=0 AND ((o.salespersonid!=0 OR o.id IN (SELECT orderid FROM ".tbl_orderproducts." WHERE salespersonid!=0))) AND ((o.salespersonid = ".$salespersonid." OR ".$salespersonid."=0 OR o.id IN (SELECT orderid FROM ".tbl_orderproducts." WHERE (salespersonid=".$salespersonid." OR ".$salespersonid."=0))))");
        $this->readdb->where("(buyer.channelid = ".$channelid." OR ".$channelid."=0)");
        $this->readdb->where("(o.status = ".$status." OR '".$status."'='-1')");

        $this->readdb->where("(o.orderdate BETWEEN '".$fromdate."' AND '".$todate."')");

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
