<?php
class Abc_payment_cycle_analysis_report_model extends Common_model {

	//put your code here
	public $_table = tbl_member;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(); //set column field database for datatable orderable
	public $column_search = array(); //set column field database for datatable searchable 
	public $order = array('m.name' => 'ASC'); // default order
    public $mainquery = '';
    
	function __construct() {
        parent::__construct();
    }

    //Get ABC Payment Cycle Analysis Report Data On API
    function getABCPaymentCycleAnalysisReportDataOnAPI($userid,$fromdate,$todate,$channelid,$memberid,$counter){
        
        $limit = 10;
        $this->mainquery = "SELECT temp.id,temp.channelid,temp.name,temp.membercode,temp.totalinvoice,temp.invoiceamount,
							
                            @creditpurchases:=IFNULL((temp.invoiceamount/365),0) as creditpurchases,
                            ROUND(IFNULL((temp.totalpayment/temp.countpayment),0) / @creditpurchases ) as averagepaymentcycle
                        FROM 
                            ( 
                                SELECT m.id,m.channelid,m.name,m.membercode,";
                                    
        $this->mainquery .= "
                                
                            IFNULL((SELECT COUNT(id) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$userid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')
                            ),0) as totalinvoice,
                            
                            IFNULL((SELECT SUM(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0)) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$userid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')
                            ),0) as invoiceamount,
                            
                            IFNULL((SELECT SUM(pr.amount)
                                FROM ".tbl_paymentreceipt." as pr 
                                WHERE pr.sellermemberid=".$userid." AND pr.memberid=m.id AND pr.status=1 AND 
                                pr.transactiondate <= '".$todate."'),0
                            ) as totalpayment,

                            IFNULL((SELECT COUNT(pr.id)
                                FROM ".tbl_paymentreceipt." as pr 
                                WHERE pr.sellermemberid=".$userid." AND pr.memberid=m.id AND pr.status=1 AND 
                                pr.transactiondate <= '".$todate."'),0
                            ) as countpayment


                        ";
                       
		$this->mainquery .= " FROM ".tbl_member." as m
                            
                            WHERE m.status=1 AND m.channelid NOT IN (".GUESTCHANNELID.",".VENDORCHANNELID.") AND 
                              
                            IF(IFNULL((SELECT COUNT(id) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$userid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')
                            ),0) > 0,1,0)=1
                        ";

        if($channelid!=0){
            $this->mainquery .= " AND m.channelid='".$channelid."'";
        }
        if($memberid!=""){
            $this->mainquery .= " AND m.id IN (".$memberid.")";
        }
       
        $this->mainquery .= " ORDER BY ".key($this->order)." ".$this->order[key($this->order)];
        $this->mainquery .= ") as temp ORDER BY averagepaymentcycle ASC";
        if($counter != -1){
			$this->mainquery .= " LIMIT ".$counter.", ".$limit;
        } 
        $query = $this->readdb->query($this->mainquery);
		return $query->result_array();
    }
    function getChannelListOnABCPAymentReport($sellermemberid=0){
        
        $query = $this->readdb->select("id, name")
                            ->from(tbl_channel)
                            ->where("id IN (SELECT channelid FROM ".tbl_member." WHERE id IN (SELECT memberid FROM ".tbl_invoice." WHERE sellermemberid=".$sellermemberid." AND status=1) )")
                            ->get();
                           
        return $query->result_array();         
    }
    
    function getMemberOnPaymentCycleReportByChannel($channelid,$sellermemberid=0){
        
        $query = $this->readdb->select("id, CONCAT(name,' (',membercode,')') as name")
                            ->from($this->_table)
                            ->where("channelid=".$channelid." AND id IN (SELECT memberid FROM ".tbl_invoice." WHERE sellermemberid=".$sellermemberid." AND status=1)")
                            ->get();
                           
        return $query->result_array();         
    }
	//Export Data
    function exportABCPaymentCycleReport(){
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $filterchannelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
        $filtermemberid = !empty($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $sellermemberid = (!is_null($MEMBERID)?$MEMBERID:0);

        $this->mainquery = "SELECT temp.id,temp.channelid,temp.name,temp.channel,temp.membercode,temp.totalinvoice,temp.invoiceamount,
							
                            @creditpurchases:=IFNULL((temp.invoiceamount/365),0) as creditpurchases,
                            ROUND(IFNULL((temp.totalpayment/temp.countpayment),0) / @creditpurchases ) as averagepaymentcycle
                        FROM 
                            ( 
                                SELECT m.id,m.channelid,m.name,m.membercode,(SELECT name FROM ".tbl_channel." WHERE id=m.channelid) as channel,";


        $this->mainquery .= "
                                
                            IFNULL((SELECT COUNT(id) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$sellermemberid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                            ),0) as totalinvoice,
                            
                            IFNULL((SELECT SUM(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0)) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$sellermemberid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                            ),0) as invoiceamount,
                            
                            IFNULL((SELECT SUM(pr.amount)
                                FROM ".tbl_paymentreceipt." as pr 
                                WHERE pr.sellermemberid=".$sellermemberid." AND pr.memberid=m.id AND pr.status=1 AND 
                                pr.transactiondate <= '".$enddate."'),0
                            ) as totalpayment,

                            IFNULL((SELECT COUNT(pr.id)
                                FROM ".tbl_paymentreceipt." as pr 
                                WHERE pr.sellermemberid=".$sellermemberid." AND pr.memberid=m.id AND pr.status=1 AND 
                                pr.transactiondate <= '".$enddate."'),0
                            ) as countpayment


                        ";

        $this->mainquery .= " FROM ".tbl_member." as m
                            
                            WHERE m.status=1 AND m.channelid NOT IN (".GUESTCHANNELID.",".VENDORCHANNELID.") AND 
                            
                            IF(IFNULL((SELECT COUNT(id) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$sellermemberid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                            ),0) > 0,1,0)=1
                        ";
        if($filterchannelid!=0){
            $this->mainquery .= " AND m.channelid='".$filterchannelid."'";
        }
        if($filtermemberid!=0){
            $this->mainquery .= " AND m.id IN (".$filtermemberid.")";
        }

        $this->mainquery .= " ORDER BY ".key($this->order)." ".$this->order[key($this->order)];
        $this->mainquery .= ") as temp ORDER BY averagepaymentcycle ASC";
        
        $query = $this->readdb->query($this->mainquery);
		return $query->result();
    }
   //LISTING DATA
	function _get_datatables_query($type=1){

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$filterchannelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
        $filtermemberid = !empty($_REQUEST['memberid'])?implode(",",$_REQUEST['memberid']):0;

        $sellermemberid = (!is_null($MEMBERID)?$MEMBERID:0);

        $this->mainquery = "SELECT temp.id,temp.channelid,temp.name,temp.membercode,temp.totalinvoice,temp.invoiceamount,
							
                            @creditpurchases:=IFNULL((temp.invoiceamount/365),0) as creditpurchases,
                            ROUND(IFNULL((temp.totalpayment/temp.countpayment),0) / @creditpurchases ) as averagepaymentcycle
                        FROM 
                            ( 
                                SELECT m.id,m.channelid,m.name,m.membercode,";
                                    
        $this->mainquery .= "
                                
                            IFNULL((SELECT COUNT(id) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$sellermemberid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                            ),0) as totalinvoice,
                            
                            IFNULL((SELECT SUM(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0)) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$sellermemberid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                            ),0) as invoiceamount,
                            
                            IFNULL((SELECT SUM(pr.amount)
                                FROM ".tbl_paymentreceipt." as pr 
                                WHERE pr.sellermemberid=".$sellermemberid." AND pr.memberid=m.id AND pr.status=1 AND 
                                pr.transactiondate <= '".$enddate."'),0
                            ) as totalpayment,

                            IFNULL((SELECT COUNT(pr.id)
                                FROM ".tbl_paymentreceipt." as pr 
                                WHERE pr.sellermemberid=".$sellermemberid." AND pr.memberid=m.id AND pr.status=1 AND 
                                pr.transactiondate <= '".$enddate."'),0
                            ) as countpayment


                        ";
                       
		$this->mainquery .= " FROM ".tbl_member." as m
                            
                            WHERE m.status=1 AND m.channelid NOT IN (".GUESTCHANNELID.",".VENDORCHANNELID.") AND 
                              
                            IF(IFNULL((SELECT COUNT(id) 
                                FROM ".tbl_invoice." as i
                                WHERE i.sellermemberid=".$sellermemberid." AND i.memberid=m.id AND i.status=1 AND (i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')
                            ),0) > 0,1,0)=1
                        ";

        if($filterchannelid!=0){
            $this->mainquery .= " AND m.channelid='".$filterchannelid."'";
        }
        if($filtermemberid!=0){
            $this->mainquery .= " AND m.id IN (".$filtermemberid.")";
        }
        
		$i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
                if($i===0) // first loop
                {
                    $this->mainquery .= " AND (";
                    $this->mainquery .= $item." LIKE '%".$_POST['search']['value']."%'";
                }
                else
                {
                    $this->mainquery .= " OR ".$item." LIKE '%".$_POST['search']['value']."%'";
                }

                if(count($this->column_search) - 1 == $i) //last loop
                    $this->mainquery .= ")"; //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) { // here order processing
            $this->mainquery .= " ORDER BY ".$this->column_order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir'];
        }else if(isset($this->order)) {
            $order = $this->order;
            $this->mainquery .= " ORDER BY ".key($order)." ".$order[key($order)];
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->mainquery .= ") as temp ORDER BY averagepaymentcycle ASC";
            $this->mainquery .= " LIMIT ".$_POST['start'].','.$_POST['length'];
            $query = $this->readdb->query($this->mainquery);
            // echo $this->readdb->last_query(); exit;
            return $query->result();
        }
    }

    function count_all() {
        $this->_get_datatables_query();
        $this->mainquery .= ") as temp ORDER BY averagepaymentcycle ASC";
        $query = $this->readdb->query($this->mainquery);
        
        return $query->num_rows();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $this->mainquery .= ") as temp ORDER BY averagepaymentcycle ASC";
        $query = $this->readdb->query($this->mainquery);
        return $query->num_rows();
    }
}

