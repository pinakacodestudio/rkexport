<?php

class Sales_commission_report_model extends Common_model {
//put your code here
	public $_table = tbl_invoice;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array("temp.id"=>"DESC");
	public $column_order = array(null,'i.invoiceno','employeename','m.name','i.invoicedate','grosssales','cost','profit','profitpercent','commissionruppees'); //set column field database for datatable orderable
	public $column_search = array('i.invoiceno','u.name',"m.name","m.membercode",'i.invoicedate'); //set column field database for datatable searchable 
    public $mainquery = '';
    
	function __construct() {
		parent::__construct();
	}

    //Export Data
    function exportSalesCommissionReportData(){
        
        $employeeid = (!empty($_REQUEST['employeeid']))?$_REQUEST['employeeid']:'0';
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->mainquery = "SELECT temp.*
            FROM 
            ( 
                SELECT `i`.`id`, `i`.`invoiceno`, `u`.`name` as `employeename`, `i`.`memberid`, `m`.`name` as `membername`, `m`.`channelid`, `m`.`membercode`, `i`.`invoicedate`, @gorsssales:=IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM transactiondiscount WHERE transactionid=i.id), 0) + IFNULL((SELECT SUM(amount) FROM extrachargemapping WHERE type=2 AND referenceid=i.id), 0)), 0) as grosssales, @cost:=IFNULL((i.amount + i.taxamount), 0) as cost, IFNULL(@gorsssales - @cost, 0) as profit, IFNULL(IFNULL(@gorsssales - @cost, 0) * 100 / @cost, 0) as profitpercent, `i`.`commission`, IFNULL((IF(i.commissionwithgst=0, (@gorsssales), (@gorsssales-i.taxamount))*i.commission/100), 0) as commissionruppees

                FROM ".$this->_table." as i
                INNER JOIN ".tbl_user." as u ON u.id=i.salespersonid
                INNER JOIN ".tbl_member." as m ON m.id=i.memberid
                WHERE i.status <> 2 AND i.salespersonid <>0 AND i.sellermemberid =0 AND i.memberid <>0
                AND (i.salespersonid='".$employeeid."' OR ".$employeeid."=0) AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')

                UNION

                SELECT `i`.`id`, `i`.`invoiceno`, `u`.`name` as `employeename`, `i`.`memberid`, `m`.`name` as `membername`, `m`.`channelid`, `m`.`membercode`, `i`.`invoicedate`, 
                IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0) as grosssales, 

                IFNULL(SUM(trp.price * trp.quantity), 0) as cost, 

                IFNULL((IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0)) - (IFNULL(SUM(trp.price * trp.quantity), 0)), 0) as profit,
                IFNULL(IFNULL((IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0)) - (IFNULL(SUM(trp.price * trp.quantity), 0)), 0) * 100 / (IFNULL(SUM(trp.price), 0)), 0) as profitpercent, 

                SUM(`trp`.`commission`) as commission, 

                IFNULL((IF(trp.commissionwithgst=0, (IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0)), ((IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0))-SUM((trp.price*trp.tax/100)*trp.quantity)))*trp.commission/100), 0) as commissionruppees

                FROM ".$this->_table." as i
                INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3 
                INNER JOIN ".tbl_orderproducts." as op ON op.id=trp.referenceproductid
                INNER JOIN ".tbl_orders." as o ON o.id=op.orderid AND o.isdelete=0
                INNER JOIN ".tbl_user." as u ON u.id=trp.salespersonid
                INNER JOIN ".tbl_member." as m ON m.id=i.memberid
                WHERE i.status <> 2 AND i.sellermemberid =0 AND i.memberid <>0
                AND (trp.salespersonid='".$employeeid."' OR ".$employeeid."=0) AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')
                GROUP BY i.id,trp.salespersonid
            
        ) as temp";

        $order = $this->_order;
        $this->mainquery .= " ORDER BY ".key($order)." ".$order[key($order)];
        $query = $this->readdb->query($this->mainquery);
        
		return $query->result();
    }

    //LISTING DATA
	function _get_datatables_query($type=1){

		$employeeid = (!empty($_REQUEST['employeeid']))?$_REQUEST['employeeid']:'0';
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->mainquery = "SELECT temp.*
            FROM 
            ( 
                SELECT `i`.`id`, `i`.`invoiceno`, `u`.`name` as `employeename`, `i`.`memberid`, `m`.`name` as `membername`, `m`.`channelid`, `m`.`membercode`, `i`.`invoicedate`, @gorsssales:=IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM transactiondiscount WHERE transactionid=i.id), 0) + IFNULL((SELECT SUM(amount) FROM extrachargemapping WHERE type=2 AND referenceid=i.id), 0)), 0) as grosssales, @cost:=IFNULL((i.amount + i.taxamount), 0) as cost, IFNULL(@gorsssales - @cost, 0) as profit, IFNULL(IFNULL(@gorsssales - @cost, 0) * 100 / @cost, 0) as profitpercent, `i`.`commission`, IFNULL((IF(i.commissionwithgst=0, (@gorsssales), (@gorsssales-i.taxamount))*i.commission/100), 0) as commissionruppees, 0 as type,
                '' as productname, '' as commissiongroup,'' as commissionruppeesgroup

                FROM ".$this->_table." as i
                INNER JOIN ".tbl_user." as u ON u.id=i.salespersonid
                INNER JOIN ".tbl_member." as m ON m.id=i.memberid
                WHERE i.status <> 2 AND i.salespersonid <>0 AND i.sellermemberid =0 AND i.memberid <>0
                AND (i.salespersonid='".$employeeid."' OR ".$employeeid."=0) AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')

                UNION

                SELECT `i`.`id`, `i`.`invoiceno`, `u`.`name` as `employeename`, `i`.`memberid`, `m`.`name` as `membername`, `m`.`channelid`, `m`.`membercode`, `i`.`invoicedate`, 
                IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0) as grosssales, 

                IFNULL(SUM(trp.price * trp.quantity), 0) as cost, 

                IFNULL((IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0)) - (IFNULL(SUM(trp.price * trp.quantity), 0)), 0) as profit,
                IFNULL(IFNULL((IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0)) - (IFNULL(SUM(trp.price * trp.quantity), 0)), 0) * 100 / (IFNULL(SUM(trp.price), 0)), 0) as profitpercent, 

                SUM(trp.commission) as commission, 

                IFNULL(
                    (
                        IF(trp.commissionwithgst=0, 
                            (IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0)), 
                            ((IFNULL(SUM(trp.price + trp.price*trp.tax/100 * trp.quantity), 0))
                            -
                            SUM((trp.price*trp.tax/100)*trp.quantity))
                        )*trp.commission/100
                    )
                , 0) as commissionruppees, 
                
                1 as type,
                GROUP_CONCAT(p.name SEPARATOR '|') as productname,
                GROUP_CONCAT(trp.commission SEPARATOR '|') as commissiongroup,
                GROUP_CONCAT(IFNULL(IF(trp.commissionwithgst=0, 
                IFNULL((trp.price + trp.price*trp.tax/100 * trp.quantity), 0), 
            
            
                (IFNULL((trp.price + trp.price*trp.tax/100 * trp.quantity), 0)-((trp.price*trp.tax/100)*trp.quantity)))
                
                *trp.commission/100, 0) SEPARATOR '|') as commissionruppeesgroup

                FROM ".$this->_table." as i
                INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactionid=i.id AND trp.transactiontype=3 
                INNER JOIN ".tbl_orderproducts." as op ON op.id=trp.referenceproductid
                INNER JOIN ".tbl_orders." as o ON o.id=op.orderid AND o.isdelete=0
                INNER JOIN ".tbl_product." as p ON p.id=op.productid
                INNER JOIN ".tbl_user." as u ON u.id=trp.salespersonid
                INNER JOIN ".tbl_member." as m ON m.id=i.memberid
                WHERE i.status <> 2 AND i.sellermemberid =0 AND i.memberid <>0
                AND (trp.salespersonid='".$employeeid."' OR ".$employeeid."=0) AND (i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')
                GROUP BY i.id,trp.salespersonid
            
        ) as temp";
        
        $i = 0;
        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                
                if($i===0) // first loop
                {
                    $this->mainquery .= " WHERE (";
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
        }else if(isset($this->_order)) {
            $order = $this->_order;
            $this->mainquery .= " ORDER BY ".key($order)." ".$order[key($order)];
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->mainquery .= " LIMIT ".$_POST['start'].','.$_POST['length'];
            $query = $this->readdb->query($this->mainquery);
            // echo $this->readdb->last_query(); exit;
            return $query->result();
        }
    }

    function count_all() {
        $this->_get_datatables_query();
        $query = $this->readdb->query($this->mainquery);
        
        return $query->num_rows();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->query($this->mainquery);

        return $query->num_rows();
    }
}