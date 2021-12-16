<?php

class Coupon_code_report_model extends Common_model {

    public $_table = tbl_orders;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = 'id DESC';
    public $_detatableorder = array('createddate'=>'DESC');
    public $column_order = array(null,'temp.buyername','temp.sellername','temp.orderid', 'temp.createddate', 'temp.paymenttype','temp.status','temp.purchase');    
    public $column_search = array('temp.sellername','temp.buyername' ,'temp.orderid', 'temp.createddate','temp.status','temp.purchase');

    function __construct() {
        parent::__construct();
    }

    function exportpurchasereport(){
        $channelid = $_REQUEST['channelid'];
        $memberid = $_REQUEST['memberid'];
        $channelid = (is_array($channelid))?implode(",",$channelid):$channelid;

        $sellerchannelid = (!empty($this->session->userdata(base_url().'CHANNELID')))?$this->session->userdata(base_url().'CHANNELID'):'';
        
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        if(!is_null($this->session->userdata(base_url().'MEMBERID'))){
            /* if($channelid!=$sellerchannelid){
            } */
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }

		$this->readdb->select("temp.*");
        $this->readdb->from("(SELECT t.payableamount as purchase, DATE(t.createddate) as createddate,
                                seller.name as sellername,o.sellermemberid as sellerid,seller.channelid as sellerchannelid,
                                buyer.name as buyername,o.memberid as buyerid,buyer.channelid as buyerchannelid,
                                o.orderid as ordernumber,o.id as orderid,o.status,
                                o.paymenttype,t.paymentgetwayid
                                
                                FROM ".tbl_orders." as o
                                INNER JOIN ".tbl_transaction." as t ON t.orderid=o.id AND DATE(t.createddate) BETWEEN '".$startdate."' AND '".$enddate."'
                                LEFT JOIN ".tbl_member." as buyer ON buyer.id=o.memberid AND (buyer.id='".$memberid."' OR 0='".$memberid."') 
                                LEFT JOIN ".tbl_member." as seller ON seller.id=o.sellermemberid
                                WHERE  o.status != 2 AND o.isdelete=0 AND (FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR ''='".$channelid."' OR (o.memberid='".$memberid."' AND o.memberid=0))

                                UNION ALL

                                SELECT oi.amount as purchase, DATE(o.createddate) as createddate,
                                seller.name as sellername,o.sellermemberid as sellerid,seller.channelid as sellerchannelid,
                                buyer.name as buyername,o.memberid as buyerid,buyer.channelid as buyerchannelid,
                                o.orderid as ordernumber,o.id as orderid,o.status,o.paymenttype,-1 as paymentgetwayid
                                FROM ".tbl_orders." as o
                                INNER JOIN ".tbl_orderinstallment." as oi ON oi.orderid=o.id
                                LEFT JOIN ".tbl_member." as buyer ON buyer.id=o.memberid AND (buyer.id='".$memberid."' OR 0='".$memberid."') 
                                LEFT JOIN ".tbl_member." as seller ON seller.id=o.sellermemberid
                                WHERE DATE(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."' AND o.status != 2 AND o.isdelete=0 AND (FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR ''='".$channelid."' OR (o.memberid='".$memberid."' AND o.memberid=0)) GROUP BY o.orderid
                                
                            ) as temp");
        $this->readdb->order_by("temp.createddate DESC");
		
		$query = $this->readdb->get();
		
		return $query->result();
    }
    
    function getcouponcodedata($startdate,$enddate,$channelid,$memberid,$status,$datetype){
        
        if($datetype==1){
			$this->readdb->select("DATE(o.createddate) as date");
		}else{
			$this->readdb->select("DATE_FORMAT(o.createddate,'%Y-%m') as date");
        }
        $this->readdb->select("o.memberid as buyermemberid,SUM(o.payableamount) as totalpurchase");
        $this->readdb->from($this->_table." as o");

        if($channelid!='0'){
            $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid AND FIND_IN_SET(buyer.id,'".$memberid."')>0");
        }
        if($datetype==1){
			$this->readdb->where("DATE(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."'");
		}else{
            $this->readdb->where("DATE(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."'");
			//$this->readdb->where("CONCAT(YEAR(o.createddate),'-',DATE_FORMAT(o.createddate,'%m')) BETWEEN '".$startdate."' AND '".$enddate."'");
        }
        if($status!=''){
            $this->readdb->where("(FIND_IN_SET(o.status,'".$status."')>0)");
        }
        if($channelid!='0'){
            $this->readdb->where("(FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR '".$channelid."'='')");
        }else{
            $this->readdb->where("o.memberid=0");
        }
        if($datetype==1){
            $this->readdb->group_by("DATE(o.createddate)");
		}else{
            $this->readdb->group_by("DATE_FORMAT(o.createddate,'%Y-%m')");
        }
        $this->readdb->group_by("o.memberid");
        $query = $this->readdb->get();
        
        return $query->result_array();
    }
    
    function getproductwisepurchasedata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype){
        
        if($datetype==1){
			$this->readdb->select("DATE(o.createddate) as date");
		}else{
			$this->readdb->select("DATE_FORMAT(o.createddate,'%Y-%m') as date");
        }
        //IFNULL((SUM((op.price*op.quantity) - IF(op.discount>0,IFNULL(((op.price - IFNULL(((op.price*op.quantity)*op.tax/(100+op.tax) ),0)) * op.discount/100),0),0))),0) as totalpurchase,
        $this->readdb->select("o.memberid as buyermemberid,op.productid,IFNULL(pp.id,0) as priceid,
                        CONCAT(o.memberid,'|',op.productid,'|',IFNULL(pp.id,0)) as conbinationid, 
                        IFNULL(op.finalprice,0) as totalpurchase
                        ");
        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_orderproducts." as op", "op.orderid = o.id AND (FIND_IN_SET(op.productid,'".$productid."')>0 OR ''='".$productid."')","LEFT"); 
        
        if($channelid!='0'){
            $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid AND FIND_IN_SET(buyer.id,'".$memberid."')>0", "LEFT");
        }
        $this->readdb->join(tbl_productprices." as pp","pp.productid=op.productid", "LEFT");
        
        if($datetype==1){
			$this->readdb->where("DATE(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."'");
		}else{
            $this->readdb->where("DATE(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."'");
		}
        if($status!=''){
            $this->readdb->where("(FIND_IN_SET(o.status,'".$status."')>0)");
        }
        if($channelid!='0'){
            $this->readdb->where("(FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR '".$channelid."'='')");
        }else{
            $this->readdb->where("o.memberid=0");
        }
        $this->readdb->where("IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o.id AND ov.orderproductid=op.id AND ov.priceid=pp.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id");
        
        if($datetype==1){
            $this->readdb->group_by("DATE(o.createddate)");
		}else{
            $this->readdb->group_by("DATE_FORMAT(o.createddate,'%Y-%m')");
        }
        $this->readdb->group_by("conbinationid");
        $query = $this->readdb->get();
        //echo $this->readdb->last_query(); exit;
        return $query->result_array();
    }

    function _get_datatables_query(){  
        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
        $memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $channelid = (is_array($channelid))?implode(",",$channelid):$channelid;

        $sellerchannelid = (!empty($this->session->userdata(base_url().'CHANNELID')))?$this->session->userdata(base_url().'CHANNELID'):'';
        
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        if(!is_null($this->session->userdata(base_url().'MEMBERID'))){
            /* if($channelid!=$sellerchannelid){
            } */
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }

        $this->readdb->select("temp.*");
        $this->readdb->from("(SELECT t.payableamount as purchase, 
                            DATE(o.createddate) as createddate, 
                            DATE(t.createddate) as paymentdate,
                            seller.name as sellername,o.sellermemberid as sellerid,seller.channelid as sellerchannelid,
                            buyer.name as buyername,o.memberid as buyerid,buyer.channelid as buyerchannelid,
                            o.orderid as ordernumber,o.id as orderid,o.status,
                            o.paymenttype,t.paymentgetwayid
                            
                            FROM ".tbl_orders." as o
                            INNER JOIN ".tbl_transaction." as t ON t.orderid=o.id AND DATE(t.createddate) BETWEEN '".$startdate."' AND '".$enddate."'
                            LEFT JOIN ".tbl_member." as buyer ON buyer.id=o.memberid AND (buyer.id='".$memberid."' OR 0='".$memberid."') 
                            LEFT JOIN ".tbl_member." as seller ON seller.id=o.sellermemberid
                            WHERE  o.status != 2 AND o.isdelete=0 AND (FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR ''='".$channelid."' OR (o.memberid='".$memberid."' AND o.memberid=0))

                            UNION ALL

                            SELECT oi.amount as purchase, 
                            DATE(o.createddate) as createddate, 
                            DATE(o.createddate) as paymentdate,
                            seller.name as sellername,o.sellermemberid as sellerid,seller.channelid as sellerchannelid,
                            buyer.name as buyername,o.memberid as buyerid,buyer.channelid as buyerchannelid,
                            o.orderid as ordernumber,o.id as orderid,o.status,o.paymenttype,-1 as paymentgetwayid
                            FROM ".tbl_orders." as o
                            INNER JOIN ".tbl_orderinstallment." as oi ON oi.orderid=o.id
                            LEFT JOIN ".tbl_member." as buyer ON buyer.id=o.memberid AND (buyer.id='".$memberid."' OR 0='".$memberid."') 
                            LEFT JOIN ".tbl_member." as seller ON seller.id=o.sellermemberid
                            WHERE DATE(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."' AND o.status != 2 AND o.isdelete=0 AND (FIND_IN_SET(buyer.channelid,'".$channelid."')>0 OR ''='".$channelid."' OR (o.memberid='".$memberid."' AND o.memberid=0)) GROUP BY o.orderid
                            
                        ) as temp");

     // AND (FIND_IN_SET(seller.channelid,'".$sellerchannelid."')>0 OR ''='".$sellerchannelid."')
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
        }else if(isset($this->_detatableorder)) {
            $order = $this->_detatableorder;
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