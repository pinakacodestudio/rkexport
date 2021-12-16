<?php

class Payment_model extends Common_model {

	//put your code here
	public $_table = tbl_paymentreceipt;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('pr.id' => 'DESC'); // default order 
	public $column_order = array(null,'vendorname','bankname','pr.transactiondate','pr.paymentreceiptno','transactiontype','statusname','pr.amount'); //set column field database for datatable orderable
	public $column_search = array("vendor.name","vendor.membercode",'cb.name','cb.accountno','pr.transactiondate','pr.paymentreceiptno','pr.amount','IF(pr.isagainstreference=1,"Is Against Invoice","On Account")',"CASE WHEN pr.status=0 THEN 'Pending' WHEN pr.status=1 THEN 'Approve' Else 'Cancel' END"); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
	}
	function getTotalRemainingPaymentAmount($memberid){

		$query = $this->readdb->query("SELECT CAST(IFNULL(temp.invoiceamount - temp.returnamount - temp.payamount,0) AS DECIMAL(18,2)) as amount  
						FROM (
							SELECT 
								IFNULL((SELECT SUM(i.amount+i.taxamount+IFNULL((SELECT SUM(ec.amount) FROM ".tbl_extrachargemapping." as ec WHERE ec.type=2 AND ec.referenceid=i.id), 0)-i.globaldiscount-i.couponcodeamount-IFNULL((SELECT sum(td.redeemamount) FROM ".tbl_transactiondiscount." as td WHERE td.transactiontype=0 and td.transactionid=i.id), 0)) as invoiceamount FROM ".tbl_invoice." as i WHERE i.memberid=m.id AND i.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=m.id) AND i.status=1), 0) as invoiceamount,
							
								IFNULL((SELECT SUM(pr.amount) FROM ".tbl_paymentreceipt." as pr WHERE pr.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=m.id) AND pr.memberid=m.id AND pr.status=1 AND pr.type=1), 0) as payamount,
							
								IFNULL((SELECT SUM(cn.amount+cn.taxamount+IFNULL((SELECT SUM(ec.amount) FROM ".tbl_extrachargemapping." as ec WHERE ec.type=3 AND ec.referenceid=cn.id), 0)-cn.globaldiscount) as creditnoteamount FROM ".tbl_creditnote." as cn WHERE cn.status=1 AND cn.buyermemberid=m.id AND cn.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=m.id)), 0) as returnamount
							
							FROM ".tbl_member." as m
							WHERE m.id='".$memberid."' AND m.status = 1
							
							
						) as temp");

		if($query->num_rows() > 0){
			$data = $query->row_array();
			return $data['amount'];
		}else{
			return 0;
		}
	}
	function getPaymentDetails($id){
		
		$paymentreceiptdata['paymentreceiptdetail'] = $paymentreceiptdata['paymentreceipttransaction'] = array();
        
		$query = $this->readdb->select("pr.id,pr.memberid,pr.sellermemberid,pr.cashorbankid,pr.sellercashorbankid,
			pr.type, pr.paymentreceiptno,pr.transactiondate,pr.amount,pr.method,pr.remarks,pr.isagainstreference,pr.cancelreason,pr.status,pr.createddate,
			IF(pr.isagainstreference=1,'Is Against Invoice','On Account') as transactiontype,
        
            vendor.id as vendorid,
			vendor.name as vendorname,
			vendor.mobile as mobileno,vendor.email,vendor.gstno,
			CONCAT(ct.name,', ',p.name,', ',cn.name) as vendoraddress,
                                    
			pr.amount as totalamount,
                                   
		")

                    ->from($this->_table." as pr")
                    ->join(tbl_member." as vendor","vendor.id=pr.sellermemberid","LEFT") 
                    ->join(tbl_city." as ct","ct.id=vendor.cityid","LEFT")
                    ->join(tbl_province." as p","p.id=ct.stateid","LEFT")
                    ->join(tbl_country." as cn","cn.id=p.countryid","LEFT")
                    ->where("pr.id=".$id)
                    ->get();
        $rowdata =  $query->row_array();
        
        if(empty($rowdata)){
            redirect('Pagenotfound');
        }
		
        $paymentreceiptdata['paymentreceiptdetail'] = array("id"=>$rowdata['id'],
                                            "paymentreceiptno"=>ucwords($rowdata['paymentreceiptno']),
                                            "transactiondate"=>$this->general_model->displaydate($rowdata['transactiondate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "vendorname"=>ucwords($rowdata['vendorname']),
											"vendorid"=>$rowdata['vendorid'],
											"mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "vendoraddress"=>$rowdata['vendoraddress'],
                                            "status"=>$rowdata['status'],
                                            "remarks"=>$rowdata['remarks'],
                                            "totalamount"=>$rowdata['totalamount'],
											"method"=>$rowdata['method'],
											"transactiontype"=>$rowdata['transactiontype'],
											"isagainstreference"=>$rowdata['isagainstreference']
                                            );

		
		if($rowdata['isagainstreference']==1){

			$query = $this->readdb->select("prt.id,prt.invoiceid,i.invoiceno,
			
			IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as invoiceamount,
			prt.amount")
								->from(tbl_paymentreceipttransactions." as prt")
								->join(tbl_invoice." as i","i.id=prt.invoiceid","INNER")
								->where("prt.paymentreceiptid=".$id)
								->get();
								
			$paymentreceiptdata['paymentreceipttransaction'] = $query->result_array();
		}

		$query = $this->readdb->select("prsh.id,prsh.paymentreceiptid,prsh.status,prsh.createddate as modifieddate,prsh.type,(SELECT CONCAT(name,' (','".COMPANY_CODE."',')') FROM ".tbl_user." WHERE id=prsh.addedby) as name,prsh.addedby as modifiedby,
		IF(prsh.status=2,(SELECT cancelreason FROM ".tbl_paymentreceipt." WHERE id=prsh.paymentreceiptid),'') as reason
		")
						->from(tbl_paymentreceiptstatushistory." as prsh")
						->where("prsh.paymentreceiptid=".$id." AND type=0")
						->get();    
								
		$paymentreceiptdata['paymentreceiptstatushistory'] = $query->result_array();

        return $paymentreceiptdata;
	}
	function getShipperDetails(){
        $query = $this->readdb->select($this->_fields)
                            ->from(tbl_settings)
                           ->get();
        return $query->row_array();                 
    }
	function updatePurchaseInvoiceStatus($vendorid,$paymentreceiptid,$status){
		
		if($status==1){
			$query = $this->readdb->select("i.id")
							->from(tbl_invoice." as i")
							->where("i.id IN (SELECT invoiceid FROM ".tbl_paymentreceipttransactions." 
								WHERE paymentreceiptid = ".$paymentreceiptid." GROUP BY invoiceid) AND 
								i.status=0 AND
								
								IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) 
								
								= 
								IFNULL((SELECT SUM(prt.amount) 
								FROM ".tbl_paymentreceipttransactions." as prt 
								WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE sellermemberid=".$vendorid." AND memberid=0 AND isagainstreference=1 AND status=1)),0) 

								")
							->order_by("i.id", "DESC")
							->get();
							
			$invoicedata = $query->result_array();
			if(!empty($invoicedata)){
				$invoiceidarr = array_column($invoicedata, "id");

				$this->writedb->query("UPDATE ".tbl_invoice." SET status=1 WHERE id IN (".implode(",",$invoiceidarr).")");
			}
		}elseif($status==2){
			$this->writedb->query("UPDATE ".tbl_invoice." SET status=0 WHERE id IN (SELECT invoiceid FROM ".tbl_paymentreceipttransactions." 
			WHERE paymentreceiptid = ".$paymentreceiptid." GROUP BY invoiceid)");
		}
	}
    function getPaymentDataById($ID){
        
        $query=$this->readdb->select("id,memberid,sellermemberid as vendorid,cashorbankid,paymentreceiptno,transactiondate,amount,method,remarks,type,status,isagainstreference")
		->from($this->_table)
                            ->where("id", $ID)
                            ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
    }
	function getPaymentTransactions($id){
		
		$query=$this->readdb->select("id,invoiceid,amount")
							->from(tbl_paymentreceipttransactions)
                            ->where("paymentreceiptid", $id)
                            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
    function generatePaymentReceiptNo(){
		
		$financialyearid = $this->session->userdata(base_url() . 'FINANCIAL_YEAR');

		$query=$this->db->select("IFNULL(LPAD(max(paymentreceiptno)+1, 6, '0'),'100001') as paymentreceiptno")
						->from($this->_table)
						->get();

		$paymentreceipt = $query->row_array();
		
		return $paymentreceipt['paymentreceiptno'];
    }
    
	//LISTING DATA
	function _get_datatables_query(){
        
        $vendorid = (isset($_REQUEST['vendorid']))?$_REQUEST['vendorid']:-1;
        $transactiontype = isset($_REQUEST['transactiontype'])?$_REQUEST['transactiontype']:0;
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        
        $this->readdb->select("pr.id,pr.memberid,pr.sellermemberid,pr.cashorbankid,pr.type,
            pr.paymentreceiptno,pr.transactiondate,pr.amount,pr.method,pr.remarks,pr.status,
			IF(pr.isagainstreference=1,'Is Against Invoice','On Account') as transactiontype,
            IF(pr.type=1,'Payment','Receipt') as typename,
            cb.name as bankname,cb.accountno,

            CASE WHEN pr.status=0 THEN 'Pending' WHEN pr.status=1 THEN 'Approve' Else 'Cancel' END as statusname,

            vendor.channelid as vendorchannelid,
            vendor.id as vendorid,
            vendor.name as vendorname,
			vendor.membercode as vendorcode
        ");
		$this->readdb->from($this->_table." as pr");
        $this->readdb->join(tbl_member." as vendor","vendor.id=pr.sellermemberid","LEFT");
        $this->readdb->join(tbl_cashorbank." as cb","cb.id=pr.cashorbankid","LEFT");
        $this->readdb->where("pr.memberid=0 AND pr.type=1");
        
        //FILTER VENDOR
        $this->readdb->where("(pr.sellermemberid = '".$vendorid."' OR '".$vendorid."'=-1)");
        //FILTER BANK METHOD
        $this->readdb->where("(pr.isagainstreference = '".$transactiontype."' OR '".$transactiontype."'=0)");
        //FILTER TRANSACTION DATE
        $this->readdb->where("(pr.transactiondate BETWEEN '".$startdate."' AND '".$enddate."')");
        
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
