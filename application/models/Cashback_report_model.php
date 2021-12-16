<?php
class Cashback_report_model extends Common_model {

	//put your code here
	public $_table = tbl_cashbackreport;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'membername','i.invoiceno','netamount','cr.cashbackamount','cr.status');
	public $column_search = array('i.invoiceno',"(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0))",'cr.cashbackamount',"(CASE WHEN cr.status=1 THEN 'Paid' ELSE 'Not Paid' END)",'IFNULL(CONCAT(buyer.name," (",buyer.membercode,")"),"")','IFNULL(CONCAT(seller.name," (",seller.membercode,")"),"")');
	public $order = array('cr.id' => 'DESC');

	function __construct() {
        parent::__construct();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            $this->column_order = array(null,'membername','i.invoiceno','netamount','cr.cashbackamount','cr.status');
        }else{
            $this->column_order = array(null,'membername','sellername','i.invoiceno','netamount','cr.cashbackamount','cr.status');
        }
	}

    function getCashbackReportDataOnAPI($userid,$buyerchannelid,$buyermemberid,$status,$counter){

        $limit = 10;
        $this->readdb->select("cr.id,i.id as invoiceid,i.invoiceno,
            
            IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,

            cr.cashbackamount,
            cr.status,

            buyer.name as membername,i.memberid,
            buyer.channelid as buyerchannelid,
            buyer.membercode as buyercode,

            IFNULL(seller.name,'') as sellername,
            IFNULL(seller.membercode,'') as sellercode,
            IFNULL(i.sellermemberid,'') as sellerid,
            IFNULL(seller.channelid,'') as sellerchannelid
            
        ");
        $this->readdb->from($this->_table." as cr");
        $this->readdb->join(tbl_invoice." as i","i.id=cr.invoiceid","INNER");
        $this->readdb->join(tbl_member." as buyer","buyer.id=i.memberid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT");
        $this->readdb->where("i.sellermemberid=".$userid);
        
        if(!empty($buyerchannelid)){
            $this->readdb->where("buyer.channelid=".$buyerchannelid); //Filter buyer Channel
           
            if(!empty($buyermemberid)){
                $this->readdb->where("buyer.id IN (".$buyermemberid.")"); //Filter buyer member
            }
        }
        
        $this->readdb->where("(cr.status = '".$status."' OR '".$status."'='')");
        $this->readdb->order_by(key($this->order), $this->order[key($this->order)]);
        if($counter != -1){
			$this->readdb->limit($limit,$counter);
        }
        $query = $this->readdb->get();
        
        return $query->result_array();
    }
    //Export Data
    function exportcashbackreport(){
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $buyerchannelid = (isset($_REQUEST['buyerchannelid']))?$_REQUEST['buyerchannelid']:'';
        $buyermemberid = (isset($_REQUEST['buyermemberid']))?$_REQUEST['buyermemberid']:'';
        $sellerchannelid = (isset($_REQUEST['sellerchannelid']))?$_REQUEST['sellerchannelid']:'';
        $sellermemberid = (isset($_REQUEST['sellermemberid']))?$_REQUEST['sellermemberid']:'';
        $status = (isset($_REQUEST['status']))?$_REQUEST['status']:'';
        
		$this->readdb->select("cr.id,i.id as invoiceid,i.invoiceno,
            
                IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,

                cr.cashbackamount,
                cr.status,

                buyer.name as membername,i.memberid,
                buyer.channelid as buyerchannelid,
                buyer.membercode as buyercode,

                IFNULL(seller.name,'') as sellername,
                IFNULL(seller.membercode,'') as sellercode,
                IFNULL(i.sellermemberid,'') as sellerid,
                IFNULL(seller.channelid,'') as sellerchannelid
                
            ");
        $this->readdb->from($this->_table." as cr");
        $this->readdb->join(tbl_invoice." as i","i.id=cr.invoiceid","INNER");
        $this->readdb->join(tbl_member." as buyer","buyer.id=i.memberid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT");
        
        if(!is_null($MEMBERID)){
            $this->readdb->where("i.sellermemberid=".$MEMBERID);
        }
        if(!empty($buyerchannelid)){
            $this->readdb->where("buyer.channelid=".$buyerchannelid); //Filter buyer Channel
            
            if(!empty($buyermemberid)){
                $this->readdb->where("buyer.id IN (".implode(",",$buyermemberid).")"); //Filter buyer member
            }
        }
        if($sellerchannelid!=""){
            if($sellerchannelid!=0){
                $this->readdb->where("seller.channelid=".$sellerchannelid); //Filter seller channel
                
                if(!empty($sellermemberid)){
                    $this->readdb->where("seller.id IN (".implode(",",$sellermemberid).")"); //Filter seller member
                }
            }else{
                $this->readdb->where("i.sellermemberid=0"); //Filter seller 
            }
        }
        $this->readdb->where("(cr.status = '".$status."' OR '".$status."'='')");
        $this->readdb->order_by("cr.id DESC");
		$query = $this->readdb->get();
		
		return $query->result();
	}
	//LISTING DATA
	function _get_datatables_query($type=1){

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $buyerchannelid = (isset($_REQUEST['buyerchannelid']))?$_REQUEST['buyerchannelid']:'';
        $buyermemberid = (isset($_REQUEST['buyermemberid']))?$_REQUEST['buyermemberid']:'';
        $sellerchannelid = (isset($_REQUEST['sellerchannelid']))?$_REQUEST['sellerchannelid']:'';
        $sellermemberid = (isset($_REQUEST['sellermemberid']))?$_REQUEST['sellermemberid']:'';
        $status = (isset($_REQUEST['status']))?$_REQUEST['status']:'';
		
		if($type==1){
            $this->readdb->select("cr.id,i.id as invoiceid,i.invoiceno,
            
                IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,

                cr.cashbackamount,
                cr.status,

                buyer.name as membername,i.memberid,
                buyer.channelid as buyerchannelid,
                buyer.membercode as buyercode,

                IFNULL(seller.name,'') as sellername,
                IFNULL(seller.membercode,'') as sellercode,
                IFNULL(i.sellermemberid,'') as sellerid,
                IFNULL(seller.channelid,'') as sellerchannelid
                
            ");
		}else{
			$this->readdb->select("i.invoiceno");
		}
		
		$this->readdb->from($this->_table." as cr");
        $this->readdb->join(tbl_invoice." as i","i.id=cr.invoiceid","INNER");
        $this->readdb->join(tbl_member." as buyer","buyer.id=i.memberid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT");
        
		if(!is_null($MEMBERID)){
            $this->readdb->where("i.sellermemberid=".$MEMBERID);
        }
        if(!empty($buyerchannelid)){
            $this->readdb->where("buyer.channelid=".$buyerchannelid); //Filter buyer Channel
           
            if(!empty($buyermemberid)){
                $this->readdb->where("buyer.id IN (".implode(",",$buyermemberid).")"); //Filter buyer member
            }
        }
        if($sellerchannelid!=""){
            if($sellerchannelid!=0){
                $this->readdb->where("seller.channelid=".$sellerchannelid); //Filter seller channel
                
                if(!empty($sellermemberid)){
                    $this->readdb->where("seller.id IN (".implode(",",$sellermemberid).")"); //Filter seller member
                }
            }else{
                $this->readdb->where("i.sellermemberid=0"); //Filter seller 
            }
        }
        $this->readdb->where("(cr.status = '".$status."' OR '".$status."'='')");

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
        }else if(isset($this->order)) {
            $order = $this->order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            //echo $this->readdb->last_query(); exit;
            return $query->result();
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
        