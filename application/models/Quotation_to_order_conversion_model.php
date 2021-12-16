<?php

class Quotation_to_order_conversion_model extends Common_model {
//put your code here
	public $_table = '';
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'m.name','noofquotation','noofconverted','conversionrate','netquotation'); //set column field database for datatable orderable
	public $column_search = array('m.name'); //set column field database for datatable searchable 
	public $myorder = array('m.name' => 'DESC');
	public $mainquery = '';
    
	
	function __construct() {
		parent::__construct();
		
	}
		
	function _get_datatables_query($type=1){
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');	
		$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
		$todate = $this->general_model->convertdate($_REQUEST['todate']);
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:0;
		$memberid = (!empty($_REQUEST['memberid']))?implode(",",$_REQUEST['memberid']):'';
		
		$this->mainquery = "SELECT temp.id,temp.name,temp.membercode,temp.channelid,temp.noofquotation,
		temp.noofconverted,temp.netquotation,
		
		CAST(IFNULL((temp.noofconverted*100/temp.noofquotation),0) AS DECIMAL(5,2)) as conversionrate
		FROM
		(
			SELECT m.id,m.name,m.membercode,m.channelid,";
								
			if(is_null($MEMBERID)){
					
				$this->mainquery .= "IFNULL((SELECT count(id) FROM ".tbl_quotation." WHERE memberid=m.id AND status=1 AND (quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofquotation,
			
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND quotationid!=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofconverted,

				IFNULL((SELECT SUM((q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0))) FROM ".tbl_quotation." as q WHERE q.memberid=m.id AND q.status=1 AND (q.quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as netquotation";
		
			} else{
			
				$this->mainquery .= "IFNULL((SELECT count(id) FROM ".tbl_quotation." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=1 AND (quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofquotation,
			
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND quotationid!=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofconverted,

				IFNULL((SELECT SUM((q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0))) FROM ".tbl_quotation." as q WHERE q.sellermemberid=".$MEMBERID." AND q.memberid=m.id AND q.status=1 AND (q.quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as netquotation";							

			}
			$this->mainquery .= " FROM ".tbl_member." as m";

			$this->mainquery .= " WHERE m.channelid NOT IN(".VENDORCHANNELID.",".GUESTCHANNELID.") AND m.status=1";

			if(!is_null($MEMBERID)){
				$this->mainquery .= " AND (m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.")  OR m.id IN (SELECT memberid from ".tbl_orders." where sellermemberid=".$MEMBERID." AND isdelete=0))";
			}

			$this->mainquery .= " AND (m.channelid=".$channelid." OR ".$channelid."=0) AND (FIND_IN_SET(m.id,'".$memberid."')>0 OR '".$memberid."'='')";
							
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
		if($_POST['length'] != -1){
			$this->mainquery .= ") as temp ORDER BY conversionrate DESC";
			$this->mainquery .= " LIMIT ".$_POST['start'].','.$_POST['length'];
			$query = $this->readdb->query($this->mainquery);
			return $query->result();
		}
	}
	

	function count_filtered() {
		$this->_get_datatables_query();
		$this->mainquery .= ") as temp ORDER BY conversionrate DESC";
		$query = $this->readdb->query($this->mainquery);
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		$this->mainquery .= ") as temp ORDER BY conversionrate DESC";
		$query = $this->readdb->query($this->mainquery);//return $this->readdb->count_all_results();
		return $query->num_rows();
	}
	//Get Report Data on API
	function getQuotationToOrderConversionReportDataOnAPI($sellerid,$buyerchannelid,$buyerid,$fromdate,$todate,$counter) {
		
		$limit = "";
		if($counter != -1){
			$limit = " LIMIT ".$counter.", 10";
		}

		$query = $this->readdb->query("SELECT temp.memberid,temp.membername,temp.membercode,temp.memberchannelid,temp.noofquotation,
		temp.noofconverted,
		CAST(IFNULL((temp.noofconverted*100/temp.noofquotation),0) AS DECIMAL(5,2)) as conversionrate,
		CAST(temp.netquotation AS DECIMAL(14,2)) as netquotation
		FROM
		(
			SELECT m.id as memberid,m.name as membername,m.membercode,m.channelid as memberchannelid,
			
			IFNULL((SELECT count(id) FROM ".tbl_quotation." WHERE sellermemberid=".$sellerid." AND memberid=m.id AND status=1 AND (quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofquotation,
			
			IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$sellerid." AND memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND quotationid!=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofconverted,

			IFNULL((SELECT SUM((q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0))) FROM ".tbl_quotation." as q WHERE q.sellermemberid=".$sellerid." AND q.memberid=m.id AND q.status=1 AND (q.quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as netquotation

			FROM ".tbl_member." as m
			
			WHERE 
				m.channelid NOT IN(".VENDORCHANNELID.",".GUESTCHANNELID.") AND m.status=1 AND 
				(m.channelid=".$buyerchannelid." OR ".$buyerchannelid."=0) AND 
				(FIND_IN_SET(m.id,'".$buyerid."')>0 OR '".$buyerid."'='') AND 
				(m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$sellerid.") 
					OR 
				m.id IN (SELECT memberid from ".tbl_orders." where sellermemberid=".$sellerid." AND isdelete=0))
		
			ORDER BY ".key($this->myorder)." ".$this->myorder[key($this->myorder)]."
		
		) as temp ORDER BY conversionrate DESC".$limit);
		
		return $query->result_array();
	}
	//Export Data
	function exporttoexcelquotationtoorderconversionreport() {
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
		$todate = $this->general_model->convertdate($_REQUEST['todate']);
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:0;
		$memberid = (!empty($_REQUEST['memberid']))?$_REQUEST['memberid']:'';
		
		$this->mainquery = "SELECT temp.id,temp.name,temp.membercode,temp.channelid,temp.noofquotation,
		temp.noofconverted,temp.netquotation,
		
		CAST(IFNULL((temp.noofconverted*100/temp.noofquotation),0) AS DECIMAL(5,2)) as conversionrate
		FROM
		(
			SELECT m.id,m.name,m.membercode,m.channelid,";
			
			if(is_null($MEMBERID)){
					
				$this->mainquery .= "IFNULL((SELECT count(id) FROM ".tbl_quotation." WHERE memberid=m.id AND status=1 AND (quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofquotation,
			
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND quotationid!=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofconverted,

				IFNULL((SELECT SUM((q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0))) FROM ".tbl_quotation." as q WHERE q.memberid=m.id AND q.status=1 AND (q.quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as netquotation";
		
			}
			else{
			
			$this->mainquery .= "IFNULL((SELECT count(id) FROM ".tbl_quotation." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=1 AND (quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofquotation,
			
				IFNULL((SELECT count(id) FROM ".tbl_orders." WHERE sellermemberid=".$MEMBERID." AND memberid=m.id AND status=1 AND approved=1 AND isdelete=0 AND quotationid!=0 AND (orderdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as noofconverted,

				IFNULL((SELECT SUM((q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0))) FROM ".tbl_quotation." as q WHERE q.sellermemberid=".$MEMBERID." AND q.memberid=m.id AND q.status=1 AND (q.quotationdate BETWEEN '".$fromdate."' AND '".$todate."')),0) as netquotation";							

			}
			$this->mainquery .= " FROM ".tbl_member." as m";

			$this->mainquery .= " WHERE m.channelid NOT IN(".VENDORCHANNELID.",".GUESTCHANNELID.") AND m.status=1 AND (m.channelid=".$channelid." OR ".$channelid."=0) AND (FIND_IN_SET(m.id,'".$memberid."')>0 OR '".$memberid."'='')";

			if(!is_null($MEMBERID)){
				$this->mainquery .= " AND (m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.")  OR m.id IN (SELECT memberid from ".tbl_orders." where sellermemberid=".$MEMBERID." AND isdelete=0))";
			}

		
		$this->mainquery .= " ORDER BY ".key($this->myorder)." ".$this->myorder[key($this->myorder)];
        $this->mainquery .= ") as temp ORDER BY conversionrate  DESC";
		
		
		$query = $this->readdb->query($this->mainquery);
		
		return $query->result();
		
	
	}
}


