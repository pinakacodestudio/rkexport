<?php

class Payment_collection_model extends Common_model {

	//put your code here
	public $_table = tbl_paymentreceipt;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('pr.id' => 'DESC'); // default order 
	public $column_order = array('i.invoiceno','u.name','buyer.name','prt.amount','invoiceamount','methodname','pr.paymentreceiptno','pr.transactiondate','statusname'); //set column field database for datatable orderable
    public $column_search = array('i.invoiceno','u.name','buyer.name','buyer.membercode','prt.amount',
        "(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0))",'DATE_FORMAT(pr.transactiondate,"%d/%m/%Y")','pr.paymentreceiptno',
        "((CASE 
            WHEN pr.method=2 THEN 'Cheque' WHEN pr.method=3 THEN 'RTGS' 
            WHEN pr.method=4 THEN 'NEFT' WHEN pr.method=5 THEN 'IMPS'
            WHEN pr.method=6 THEN 'CreditCard' WHEN pr.method=7 THEN 'DebitCard'
            ELSE 'Cash'
        END))",
        "((CASE 
            WHEN pr.status=0 THEN 'Pending' WHEN pr.status=1 THEN 'Complete' WHEN pr.status=2 THEN 'Cancel'
        END))"
    ); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
    }
    
	function getPaymentCollectedMemberList(){

        $query = $this->readdb->select("m.id,CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')') as name")
                    ->from(tbl_member." as m")
                    ->where("m.id in (SELECT memberid FROM ".$this->_table." WHERE isagainstreference=1 AND usertype=0) AND m.status=1")
                    ->get();

        if($query->num_rows() > 0 ){
            return $query->result_array();
        }else{
            return array();
        }
    }

    //LISTING DATA
	function _get_datatables_query(){
		
		$employeeid = isset($_REQUEST['employeeid'])?$_REQUEST['employeeid']:'0';
        $memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:'0';
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("prt.invoiceid,i.invoiceno,u.name as employeename, 
            pr.memberid,buyer.name as membername,buyer.membercode,buyer.channelid, 
            prt.amount as collectedamount,
            IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as invoiceamount, 
        
            pr.method,pr.paymentreceiptno,
            pr.transactiondate,pr.status,
            CASE 
                WHEN pr.method=1 THEN 'Cheque' WHEN pr.method=2 THEN 'RTGS' 
                WHEN pr.method=3 THEN 'NEFT' WHEN pr.method=4 THEN 'IMPS'
                WHEN pr.method=5 THEN 'CreditCard' WHEN pr.method=6 THEN 'DebitCard'
                ELSE 'Cash'
            END as methodname,
            CASE 
                WHEN pr.status=1 THEN 'Complete' WHEN pr.status=2 THEN 'Cancel' ELSE 'Pending'
            END as statusname
                
        ");
        
        $this->readdb->from($this->_table." as pr");
        $this->readdb->join(tbl_paymentreceipttransactions." as prt","prt.paymentreceiptid=pr.id","INNER");
        $this->readdb->join(tbl_invoice." as i","i.id=prt.invoiceid","INNER");
        $this->readdb->join(tbl_user." as u","u.id=pr.addedby","INNER");
        $this->readdb->join(tbl_member." as buyer","buyer.id=pr.memberid AND buyer.status=1","INNER");

        $this->readdb->where("pr.isagainstreference=1 AND pr.usertype=0 AND (pr.addedby=".$employeeid." OR ".$employeeid."=0)");
        $this->readdb->where("(buyer.id = ".$memberid." OR ".$memberid."=0)");
        $this->readdb->where("(pr.transactiondate BETWEEN '".$fromdate."' AND '".$todate."')");

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
