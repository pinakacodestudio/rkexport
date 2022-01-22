<?php

class Payment_receipt_model extends Common_model {

	//put your code here
	public $_table = tbl_paymentreceipt;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('pr.id' => 'DESC'); // default order 
	public $column_order = array(null,'sortorderbymember','bankname','pr.transactiondate','pr.paymentreceiptno','transactiontype','pr.amount'); //set column field database for datatable orderable
	public $column_search = array("IFNULL(buyer.name,'')","IFNULL(seller.name,'Company')","IFNULL(buyer.membercode,'')","IFNULL(seller.membercode,'')",'cb.name','cb.accountno','pr.transactiondate','pr.paymentreceiptno','pr.amount'); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
    }
	function getPaymentReceiptDetails($id){
		
		$paymentreceiptdata['paymentreceiptdetail'] = $paymentreceiptdata['paymentreceipttransaction'] = array();
        //pr.sellercashorbankid,pr.paymentreceiptno,pr.isagainstreference,pr.cancelreason,IF(pr.isagainstreference=1,'Is Against Invoice','On Account') as transactiontype,

		//	buyer.name as membername,
			// buyer.mobile as mobileno,buyer.email,buyer.gstno,
			// CONCAT(ct.name,', ',p.name,', ',cn.name) as buyeraddress,
			// IFNULL(seller.channelid,'') as sellerchannelid,
			// IFNULL(seller.name,'') as sellermembername,
			// IFNULL(seller.mobile,'') as sellermobileno,
			// IFNULL(seller.email,'') as selleremail,IFNULL(seller.gstno,'') as sellergstno,

			// IFNULL(CONCAT((SELECT name FROM ".tbl_city." WHERE id=seller.cityid),', ',(SELECT name FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=seller.cityid)),', ',(SELECT name FROM ".tbl_country." WHERE id IN (SELECT countryid FROM ".tbl_province." WHERE id IN (SELECT stateid FROM ".tbl_city." WHERE id=seller.cityid)))),'') as selleraddress,

		$query = $this->readdb->select("pr.id,
			pr.type, pr.transactiondate,pr.amount,pr.method,pr.remarks,pr.status,pr.createddate,pr.amount as totalamount,pr.partyid
                                   
		")

                            ->from($this->_table." as pr")
							// ->join(tbl_member." as buyer","buyer.id=pr.partyid","LEFT") 
							// ->join(tbl_member." as seller","seller.id=pr.sellerpartyid","LEFT") 
                            // ->join(tbl_city." as ct","ct.id=buyer.cityid","LEFT")
                            // ->join(tbl_province." as p","p.id=ct.stateid","LEFT")
                            // ->join(tbl_country." as cn","cn.id=p.countryid","LEFT")
                            ->where("pr.id=".$id)
                            ->get();
        $rowdata =  $query->row_array();
        
        if(empty($rowdata)){
            redirect('Pagenotfound');
        }
		
        $paymentreceiptdata['paymentreceiptdetail'] = array("id"=>$rowdata['id'],
                                            // "paymentreceiptno"=>ucwords($rowdata['paymentreceiptno']),
                                            "transactiondate"=>$this->general_model->displaydate($rowdata['transactiondate']),
                                            // "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            // "membername"=>ucwords($rowdata['membername']),
											"partyid"=>$rowdata['partyid'],
                                            // "mobileno"=>$rowdata['mobileno'],
                                            // "email"=>$rowdata['email'],
                                            // "gstno"=>$rowdata['gstno'],
                                            // "buyeraddress"=>$rowdata['buyeraddress'],
                                            "status"=>$rowdata['status'],
											"remarks"=>$rowdata['remarks'],
											// "sellerchannelid"=>$rowdata['sellerchannelid'],
											// "sellerpartyid"=>$rowdata['sellerpartyid'],
                                            // "sellermembername"=>$rowdata['sellermembername'],
                                            // "selleraddress"=>$rowdata['selleraddress'],
                                            // "sellermobileno"=>$rowdata['sellermobileno'],
											// "selleremail"=>$rowdata['selleremail'],
											// "sellergstno"=>$rowdata['sellergstno'],
											"totalamount"=>$rowdata['totalamount'],
											"method"=>$rowdata['method'],
											// "transactiontype"=>$rowdata['transactiontype'],
											// "isagainstreference"=>$rowdata['isagainstreference']
                                            );

		
		// if($rowdata['isagainstreference']==1){

		// 	$query = $this->readdb->select("prt.id,prt.invoiceid,i.invoiceno,
			
		// 	IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as invoiceamount,
		// 	prt.amount")
		// 						->from(tbl_paymentreceipttransactions." as prt")
		// 						->join(tbl_invoice." as i","i.id=prt.invoiceid","INNER")
		// 						->where("prt.paymentreceiptid=".$id)
		// 						->get();
								
		// 	$paymentreceiptdata['paymentreceipttransaction'] = $query->result_array();
		// }

		// $query = $this->readdb->select("prsh.id,prsh.paymentreceiptid,prsh.status,prsh.createddate as modifieddate,prsh.type,(IF(prsh.type=0,(SELECT CONCAT(name,' (','".COMPANY_CODE."',')') FROM ".tbl_user." WHERE id=prsh.addedby),(SELECT CONCAT(name,' (',membercode,')') FROM ".tbl_member." WHERE id=prsh.addedby))) as name,prsh.addedby as modifiedby,(IF(prsh.type=1,(SELECT channelid FROM ".tbl_member." WHERE id=prsh.addedby),0)) as channelid,
		
		// IF(prsh.status=2,(SELECT cancelreason FROM ".tbl_paymentreceipt." WHERE id=prsh.paymentreceiptid),'') as reason
		// ")
		// ->from(tbl_paymentreceiptstatushistory." as prsh")
		// ->where("prsh.paymentreceiptid=".$id)
		// ->get();    
								
		// $paymentreceiptdata['paymentreceiptstatushistory'] = $query->result_array();

        return $paymentreceiptdata;
	}
	function getShipperDetails(){
        $query = $this->readdb->select($this->_fields)
                            ->from(tbl_settings)
                           ->get();
        return $query->row_array();                 
    }
	function updateInvoiceStatus($buyerid,$sellerid,$paymentreceiptid,$status){
		
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
								WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE partyid=".$buyerid." AND sellerpartyid=".$sellerid." AND isagainstreference=1 AND status=1)),0) 

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
    function getPaymentReceiptDataById($ID){
        
        $query=$this->readdb->select("id,partyid,sellerpartyid,paymentreceiptno,transactiondate,amount,method,remarks,type,status,isagainstreference")
		->from($this->_table)
                            ->where("id", $ID)
                            ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
    }
	function getPaymentReceiptTransactions($id){
		
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
        
        $partyid = $this->session->userdata(base_url() . 'partyid');
        $sellerpartyid = (!is_null($partyid))?$partyid:0;
        
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:-1;
        $transactiontype = isset($_REQUEST['transactiontype'])?$_REQUEST['transactiontype']:0;
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $type = 2;
        // $where = "pr.sellerpartyid=".$sellerpartyid;
        if(!is_null($partyid)){
            if(isset($_REQUEST['type']) && $_REQUEST['type']=="purchase"){
                // $where = "pr.partyid=".$sellerpartyid;
                $type = 1;
            }
        }
		// IFNULL(buyer.channelid,'0') as buyerchannelid,
		// IFNULL(buyer.id,'') as buyerid,
		// IFNULL(buyer.name,'') as buyername,
		// IFNULL(buyer.membercode,'') as buyercode,

		// IFNULL(seller.channelid,'0') as sellerchannelid,
		// IFNULL(seller.id,'') as sellerid,
		// IFNULL(seller.name,'Company') as sellername,
		// IFNULL(seller.membercode,'') as sellercode,
		// cb.name as bankname,cb.accountno,
        $this->readdb->select("pr.id,pr.type,
            pr.paymentreceiptno,pr.transactiondate,pr.amount,pr.method,pr.remarks,pr.status
        ");
		$this->readdb->from($this->_table." as pr");
        // $this->readdb->join(tbl_member." as buyer","buyer.id=pr.partyid","LEFT");
        // $this->readdb->join(tbl_member." as seller","seller.id=pr.sellerpartyid","LEFT");
        // $this->readdb->join(tbl_cashorbank." as cb","cb.id=pr.cashorbankid","LEFT");
		// $this->readdb->where($where);
		// $this->readdb->where("pr.partyid!=0");

        //FILTER TRANSACTION DATE
        $this->readdb->where("pr.transactiondate BETWEEN '".$startdate."' AND '".$enddate."'");
        //FILTER MEMBER
        // if(!is_null($partyid) && isset($_REQUEST['type']) && $_REQUEST['type']=="purchase"){
        //     // $this->readdb->where("(pr.sellerpartyid = '".$partyid."' OR '".$partyid."'=-1)");
        // }else{
        //     // $this->readdb->where("(pr.partyid = '".$partyid."' OR '".$partyid."'=-1)");
        // }
        //FILTER BANK METHOD
        // $this->readdb->where("(pr.isagainstreference = '".$transactiontype."' OR '".$transactiontype."'=0)");
        
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
