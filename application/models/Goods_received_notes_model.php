<?php
class Goods_received_notes_model extends Common_model 
{
	//put your code here
	public $_table = tbl_goodsreceivednotes;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array('serial_number',"vendor.name","grn.orderid","grn.grnnumber","grn.receiveddate","statusname","netamount","grn.createddate","(SELECT u.name FROM ".tbl_user." as u WHERE u.id=grn.addedby)"); //set column field database for datatable orderable
    
    public $column_search = array("vendor.name","vendor.membercode","grn.grnnumber","DATE_FORMAT(grn.receiveddate, '%d/%m/%Y')","DATE_FORMAT(grn.createddate,'%d %b %Y %H:%i %p')","IFNULL((grn.amount + grn.taxamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=grn.id),0)),0)","CASE
                                        WHEN grn.status = 0 THEN 'Pending'
                                        WHEN grn.status = 1 THEN 'Complete'
                                        ELSE 'Cancel'
                                    END","(SELECT GROUP_CONCAT(o.orderid) FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,grn.orderid)>0)"); //set column field database for datatable searchable 
	public $order = array("serial_number"=>"DESC"); // default order  

	function __construct() {
		parent::__construct();
	}
    
    function getVendorGRN($vendorid){
       
        $query = $this->readdb->select("grn.id,grn.grnnumber,(SELECT addressid FROM ".tbl_orders." WHERE id IN (grn.orderid) LIMIT 1) as billingid,(SELECT shippingaddressid FROM ".tbl_orders." WHERE id IN (grn.orderid) LIMIT 1) as shippingid")
                            ->from($this->_table." as grn")
                            ->where("grn.sellermemberid='".$vendorid."' AND grn.memberid=0 AND grn.status=1")
                            // ->where("grn.sellermemberid=".$vendorid." AND
                            //     grn.memberid=0 AND 
                            //     grn.status=1 AND  

                            //     IFNULL((SELECT SUM(quantity) FROM ".tbl_transactionproducts." where transactionid = grn.id AND transactiontype=4),0) 
                    
                            //     > 
                            //     IFNULL((SELECT SUM(tp.quantity) 
                            //     FROM ".tbl_transactionproducts." as tp
                            //     INNER JOIN ".tbl_invoice." as i ON i.status != 2 AND tp.transactionid=i.id
                            //     WHERE tp.transactiontype=3 AND FIND_IN_SET(grn.id, i.orderid) >0 ),0)")
                            ->order_by("grn.id","desc")
                            ->get();
        //echo $this->readdb->last_query();exit;
        return $query->result_array();
    }
	function getOrderProductsByOrderIDOrVendorID($vendorid,$orderid,$GrnId=''){

        if(!empty($GrnId)){
            $where_sum = " AND grn.status NOT IN (2) AND tp.id != trp.id";
            $sql_edit = "trp.quantity as editquantity,";
        }else{
            $sql_edit = "";
            $where_sum = " AND grn.status NOT IN (2)";
        }

        $this->readdb->select("o.id as orderid,o.orderid as ordernumber,op.id as orderproductsid,o.payableamount,
                                    op.productid,
                                    IFNULL((SELECT id
                                    FROM ".tbl_transactionproducts." as tp
                                    WHERE tp.transactiontype=4 AND tp.referenceproductid = op.id AND (tp.transactionid='".$GrnId."' OR ''='".$GrnId."') LIMIT 1
                                    ),'') as transactionproductsid,
                                    CONCAT(op.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=op.id),'')) as productname,
                                    IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1),0) as combinationid,

                                    IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1),(SELECT id FROM ".tbl_productprices." WHERE productid=op.productid LIMIT 1)) as productpriceid,

                                    op.quantity,
                                    op.discount,
                                    op.price as amount,
                                    op.originalprice,
                                    op.hsncode,op.tax,op.isvariant,op.name,
                                    op.finalprice,
                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    
                                    @productamount:=IFNULL((op.price - (op.price * op.discount / 100)),0) as productamount, 
                                    IFNULL((@productamount + (@productamount * op.tax / 100)),0) as pricewithtax, 
                                    
                                    @grnqty:=IFNULL((SELECT SUM(tp.quantity)
                                    FROM ".$this->_table." as grn
                                    INNER JOIN ".tbl_transactionproducts." as tp ON tp.transactiontype=4 AND tp.transactionid=grn.id
                                    WHERE tp.referenceproductid = op.id AND find_in_set(o.id, grn.orderid)
                                    ".$where_sum."
                                    ),0) as grnqty,
                                    ".$sql_edit."
                                    o.gstprice,
                                    
                                ");
        $this->readdb->from(tbl_orders." as o");                           
        $this->readdb->join(tbl_orderproducts." as op", "op.orderid=o.id", "INNER");
        if(!empty($GrnId)){
            $this->readdb->join(tbl_transactionproducts." as trp", " trp.transactiontype=4 AND trp.referenceproductid = op.id AND trp.transactionid=".$GrnId, "INNER");
        }
        $this->readdb->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->where("FIND_IN_SET(o.id, '".$orderid."') AND o.sellermemberid=".$vendorid." AND o.memberid=0 AND o.isdelete=0");
        $this->readdb->order_by("op.id","DESC");
        $query = $this->readdb->get();
        // echo $this->readdb->last_query();exit;
       
        return $query->result_array();             
    }
    function getOrdersAmountDataByGRNID($grnid){

        $query = $this->readdb->select("grn.id,grn.grnnumber,grn.receiveddate,
                                    grn.taxamount,
                                    grn.amount as orderamount,
                                    (grn.taxamount+grn.amount) as netamount,
                                    (SELECT globaldiscount FROM ".tbl_orders." WHERE id IN (grn.orderid) LIMIT 1) as discountamount, 
                                    (SELECT addressid FROM ".tbl_orders." WHERE id IN (grn.orderid) LIMIT 1) as billingaddressid,
                                    (SELECT shippingaddressid FROM ".tbl_orders." WHERE id IN (grn.orderid) LIMIT 1) as shippingaddressid
                                ")

            ->from($this->_table." as grn")
            ->where("FIND_IN_SET(grn.id,'".$grnid."')>0")
            ->order_by("grn.id","DESC")
            ->get();

        $orderdata = $query->result_array();
        $data = array();
        if(!empty($orderdata)){
            foreach($orderdata as $order){
               
                $query = $this->readdb->select("ecm.id,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage,IF(ecm.extrachargepercentage>0,0,1) as amounttype,
                            (SELECT sum(amount) FROM ".tbl_transactionextracharges." WHERE transactiontype=0 AND referenceid=ecm.referenceid AND extrachargesid=ecm.extrachargesid) as invoiceamount")
                        ->from(tbl_extrachargemapping." as ecm")
                        ->where("ecm.referenceid=".$order['id']." AND ecm.type=5")
                        ->get();
                $extracharges =  $query->result_array();

               $data[] = array("id"=>$order['id'],
                                "grnnumber"=>$order['grnnumber'],
                                "receiveddate"=>$this->general_model->displaydate($order['receiveddate']),
                                "taxamount"=>$order['taxamount'],
                                "orderamount"=>$order['orderamount'],
                                "netamount"=>$order['netamount'],
                                "discountamount"=>$order['discountamount'],
                                "billingaddressid"=>$order['billingaddressid'],
                                "shippingaddressid"=>$order['shippingaddressid'],
                                "extracharges"=>$extracharges
                            );
            }
        }
        return $data;
    }
	function _get_datatables_query(){
       
        $vendorid = isset($_REQUEST['vendorid'])?$_REQUEST['vendorid']:0;
        $vendorid = is_array($vendorid)?implode(',',$vendorid):$vendorid;

        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

        $this->readdb->query("SET @srno:=0;");
		$this->readdb->select("@srno:=@srno+1 as serial_number,grn.id,grn.sellermemberid,
                            (SELECT GROUP_CONCAT(o.id) FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,grn.orderid)>0) as orderid,
                            (SELECT GROUP_CONCAT(o.orderid) FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,grn.orderid)>0) as ordernumbers,
                            grn.grnnumber,grn.receiveddate,grn.taxamount,grn.amount,grn.createddate,grn.modifieddate,
                            (SELECT u.name FROM ".tbl_user." as u WHERE u.id=grn.addedby)as addedby,
        					(SELECT u.name FROM ".tbl_user." as u WHERE u.id=grn.modifiedby)as modifiedby,
                            
                            @netamount:=IFNULL((grn.amount + grn.taxamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=5 AND referenceid=grn.id),0)),0) as netamount,
                            
                            grn.status,
                            vendor.name as vendorname,
                            vendor.membercode as vendorcode,
                            vendor.channelid as vendorchannelid,
                            
                            CASE
                                WHEN grn.status = 0 THEN 'Pending'
                                WHEN grn.status = 1 THEN 'Complete'
                                ELSE 'Cancel'
                            END AS statusname,

                            IF((IFNULL((SELECT SUM(quantity) FROM ".tbl_transactionproducts." where transactionid = grn.id AND transactiontype=4),0) 
				
                            > 
                            IFNULL((SELECT SUM(tp.quantity) 
                            FROM ".tbl_transactionproducts." as tp 
                            where tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET(grn.id, orderid)>0 AND status!=2) AND tp.transactiontype=3),0)),1,0) as allowinvoice
                            
                        ", false);

        $this->readdb->from($this->_table." as grn");
        $this->readdb->join(tbl_member." as vendor","vendor.id=grn.sellermemberid","LEFT");
        $where='';
        if($vendorid != 0){
            $where .= ' AND grn.sellermemberid IN ('.$vendorid.')';
        }
        if($status != -1){
            $where .= ' AND grn.status='.$status;
        }

        $this->readdb->where("(grn.receiveddate BETWEEN '".$startdate."' AND '".$enddate."') AND grn.sellermemberid!=0".$where);

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

	function getGoodsReceivedNotesDetails($transactionid){


        $transactiondata['transactiondetail'] = $transactiondata['transactionproduct'] = array();
        
        $query = $this->readdb->select("grn.id,grn.orderid,o.addressid,o.shippingaddressid,grn.memberid,grn.sellermemberid,m.gstno,grn.status,grn.grnnumber,grn.receiveddate,
                                    IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=grn.sellermemberid),0) as sellerchannelid,
                                    ma.name as membername,ma.address,ma.postalcode as postcode,ma.mobileno,ma.email,
                                    ct.name as cityname,
                                    pr.name as provincename, cn.name as countryname,
                                    
                                    shipping.name as shippingmembername,
                                    shipping.mobileno as shippingmobileno,
                                    shipping.email as shippingemail,

									grn.createddate,
                                    
                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    grn.amount as payableamount,grn.remarks,
                                    
                                    IF((SELECT count(tp.id) FROM ".tbl_transactionproducts." as tp WHERE tp.transactiontype=4 AND tp.transactionid=grn.id AND tp.discount>0)>0,1,0) as displaydiscountcolumn,
                                   
                                    @netamount:=IFNULL((grn.amount + grn.taxamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=5 AND referenceid=grn.id),0)),0) as netamount,

                                    IF((SELECT COUNT(o.id)
                                    FROM ".tbl_goodsreceivednotes." as gr
                                    INNER JOIN ".tbl_orders." as o ON FIND_IN_SET(o.id, gr.orderid)>0 AND o.gstprice=1
                                    WHERE gr.id=grn.id)>0,1,0) as gstprice,

                                    shipping.address as shippingaddress,
                                    shippingcity.name as shippingcityname,
                                    shippingpr.name as shippingprovincename, 
                                    shippingcn.name as shippingcountryname
                                ")

                            ->from($this->_table." as grn")
                            ->join(tbl_orders." as o","o.id IN (grn.orderid)","LEFT") 
                            ->join(tbl_member." as m","m.id=grn.sellermemberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipping","shipping.id=o.shippingaddressid","LEFT")
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
							->join(tbl_city." as shippingcity","shippingcity.id=shipping.cityid","LEFT")
                            ->join(tbl_province." as shippingpr","shippingpr.id=shippingcity.stateid","LEFT")
                            ->join(tbl_country." as shippingcn","shippingcn.id=shippingpr.countryid","LEFT")
                            ->where("grn.id=".$transactionid)
                            ->get();

        $rowdata =  $query->row_array();
        
        if(empty($rowdata)){
            redirect(ADMINFOLDER.'pagenotfound');
        }

        $address = ucwords($rowdata['address']).",".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";
       
        $shippingaddress = ucwords($rowdata['shippingaddress']).",<br>".ucwords($rowdata['shippingcityname']).", ".ucwords($rowdata['shippingprovincename']).", ".ucwords($rowdata['shippingcountryname']).".";
       
        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "orderid"=>ucwords($rowdata['orderid']),
                                            "gstno"=>$rowdata['gstno'],
                                            "receiveddate"=>$this->general_model->displaydate($rowdata['receiveddate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "grnnumber"=>ucwords($rowdata['grnnumber']),
                                            "membername"=>ucwords($rowdata['membername']),
                                            "memberid"=>$rowdata['memberid'],
                                            "sellermemberid"=>$rowdata['sellermemberid'],
                                            "sellerchannelid"=>$rowdata['sellerchannelid'],
                                            "status"=>$rowdata['status'],
                                            "igst"=>$rowdata['igst'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "billingaddressid"=>$rowdata['addressid'],
											"address"=>$address,
                                            "billingaddress"=>$address,
                                            "shippingaddress"=>$shippingaddress,
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "shippingmembername"=>$rowdata['shippingmembername'],
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "remarks"=>$rowdata['remarks'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            "netamount"=>$rowdata['netamount'],
                                            // "dueamount"=>$rowdata['dueamount'],
                                            "couponcodeamount"=>0,
                                            "globaldiscount"=>0,
                                        );

        $query = $this->readdb->select("CONCAT(tp.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_transactionvariant." WHERE transactionproductid=tp.id AND transactionid=".$transactionid."),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,tp.quantity,tp.price,tp.tax,tp.hsncode,tp.discount,(SELECT orderid FROM ".tbl_orders." WHERE id IN (SELECT orderid FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid )) as orderid,(SELECT serialno FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid ) as serialno,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid) as originalprice,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage,tp.remarks")
                            ->from(tbl_transactionproducts." as tp")
                            ->join(tbl_product." as p","p.id=tp.productid","LEFT")
                            ->where("tp.transactiontype=4 AND tp.transactionid=".$transactionid)
                            //->group_by('orderid')
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();
        
        
        $query = $this->readdb->select("ecp.extrachargesname,ecp.taxamount,ecp.amount")
                            ->from(tbl_extrachargemapping." as ecp")
                            ->where("ecp.referenceid=".$transactionid." AND ecp.type=5")
                            ->get();
        $transactiondata['extracharges'] =  $query->result_array();

        
        return $transactiondata;
    }

    function confirmForGRNCancellation($grnid){
        $this->load->model("Invoice_model","Invoice");
       /*  $this->Credit_note->_fields = 'id,status';
        $this->Credit_note->_where = array("(FIND_IN_SET('".$orderid."', orderid)>0)"=>null); */
        
        $data = $this->Invoice->getInvoicesByOrderId($grnid);
        
        if(!empty($data)){
            $status=array();
            foreach($data as $val){
                $status[] =  $val['status'];
            }
            if(!in_array("1", $status)){
                $updatedata = array("status"=>"2",
                                    "cancelreason"=>"Order Cancelled.");
                
                $this->Invoice->_where = array("(FIND_IN_SET('".$grnid."', orderid)>0)"=>null);
                $this->Invoice->Edit($updatedata);

                return 1;
            }else{
                return 0;
            }
        }else{
            return 1;
        }
    }

    function getGRNDetailsById($id){
        $query=$this->readdb->select("grn.id,grn.sellermemberid,grn.remarks,
                                    grn.orderid,
                                    grn.grnnumber,
                                    grn.receiveddate,
                                    grn.taxamount,
                                    grn.amount,
                                    grn.status")
                                    ->from($this->_table." as grn")
                                    ->where("grn.id",$id)
                                    ->get();
                return $query->row_array();

    }

    function getExtraChargesDataByGrnId($id){
        $query=$this->readdb->select("e.id,e.extrachargesid,e.extrachargesname,e.amount,e.taxamount,e.extrachargepercentage")
                            ->from(tbl_extrachargemapping." as e")
                            ->where("e.referenceid=".$id." AND e.type=5")
                            ->get();
                            // echo $this->readdb->last_query();exit;
                return $query->result_array();

    }

    function getOrderVariantsData($orderid,$orderproductsid){
        
        $query = $this->readdb->select("ov.id,ov.variantid,ov.variantname,ov.variantvalue")
                    ->from(tbl_ordervariant." as ov")                          
                    ->where("FIND_IN_SET(ov.orderid, '".$orderid."') AND ov.orderproductid=".$orderproductsid)
                    ->get();

        return $query->result_array();       
    }
}
?>