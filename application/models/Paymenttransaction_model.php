<?php
class Paymenttransaction_model extends Common_model {

	//put your code here
	public $_table = tbl_transaction;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'customername',null,'p.txnid','p.amount','p.ordernumber','paymentmethod','p.status',null,'p.createddate'); //set column field database for datatable orderable
	public $column_search = array('p.ordernumber','p.createddate','p.txnid','p.amount'); //set column field database for datatable searchable 
	public $order = array('p.id' => 'DESC'); // default order

	function __construct() {
		parent::__construct();
	}
	function getPaymenttransactionByCustomer($customerid){
		$query = $this->readdb->select("IF(t.transactionid!='',t.transactionid,'-----') as transactionid,t.orderammount,IF(po.orderid!=0,po.orderid,'') as ordernumber,t.createddate,t.paymentgetwayid,po.amount,
									IF(t.paymentstatus=1,'Success','Fail') as status")
							->from($this->_table." as t")
							->join(tbl_productorder." as po","po.id=t.orderid","LEFT")
							->where("po.customerid=".$customerid)
							->order_by("t.id DESC")
							->get();
		return $query->result_array();				
	}
	function lastweeksales(){
		$query = $this->readdb->select("IFNULL(sum(amount),0) as amount")
							->from($this->_table)
							->where("createddate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY AND createddate < curdate() - INTERVAL DAYOFWEEK(curdate())-1 DAY AND status=1")
							->get();
		return $query->row_array();				
	}
	function totalsales(){
		$query = $this->readdb->select("IFNULL(sum(amount),0) as amount")
							->from($this->_table)
							->where("status=1")
							->get();
		return $query->row_array();				
	}

	//LISTING DATA
	function _get_datatables_query(){

		$customerid = $_REQUEST['customerid'];
		$fromdate = $_REQUEST['fromdate'];
		$todate = $_REQUEST['todate'];
		
		$this->readdb->select("IF(p.txnid!='',p.txnid,'-----') as txnid,amount,IF(p.ordernumber!=0,p.ordernumber,'') as ordernumber,p.createddate,
							CASE WHEN o.portalid=1 THEN  IF(p.txnid!='','Online','COD') WHEN o.portalid!=1 THEN '---' END as paymentmethod,
							IF(p.status=1,'Success','Fail') as status,
							IFNULL((SELECT portal.name FROM ".tbl_portal." as portal WHERE portal.id=o.portalid AND portal.status=1),'') as portalname,
							CONCAT(o.firstname,' ',o.lastname) as customername,p.customerid");
		$this->readdb->from($this->_table." as p");
		$this->readdb->join(tbl_orders." as o","o.ordernumber=p.ordernumber","INNER");
		$this->readdb->where("(p.customerid=".$customerid." OR ".$customerid."=0) AND (DATE(p.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
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
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
	}
}
        