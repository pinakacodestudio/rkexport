<?php

class Pending_shipping_model extends Common_model {

	//put your code here
	public $_table = tbl_invoice;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'buyer.name','sellername','i.invoiceno','i.invoicedate','paymentmethod','couriercompany','invoicestatus','amount'); //set column field database for datatable orderable
	public $column_search = array('buyer.name','buyer.membercode','IFNULL(seller.name,"")','IFNULL(seller.membercode,"")','i.invoiceno','i.invoicedate'); //set column field database for datatable searchable 
	public $order = array('i.id' => 'DESC'); // default order  

	function __construct() {
		parent::__construct();
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            $this->column_order = array(null,'buyer.name','i.invoiceno','i.invoicedate','paymentmethod','couriercompany','invoicestatus','amount');
        }else{
            $this->column_order = array(null,'buyer.name','sellername','i.invoiceno','i.invoicedate','paymentmethod','couriercompany','invoicestatus','amount');
        }
	}

	function getFedexCourierID($MEMBERID=0,$CHANNELID=0){
		$query = $this->db->select("id as fedexcourierid")
							->from(tbl_couriercompany)
							->where("memberid='".$MEMBERID."' AND channelid='".$CHANNELID."' AND thirdparty=0")
							->limit(1)
							->get();
		return $query->row_array();		
	}
	function viewShippingOrderDetails($invoiceid){
		$data = array();
        $query = $this->db->select("so.id,so.trackingcode,so.shippingamount,so.invoiceamount,so.shipdate,
                IF(so.remarks!='',so.remarks,'-') as remarks,
                so.shippingby,            
                IF(so.shippingby=0,
					IFNULL((SELECT cc.companyname FROM ".tbl_couriercompany." as cc WHERE cc.id=so.courierid AND cc.status=1),'-'),
					IFNULL((SELECT t.companyname FROM ".tbl_transport." as t WHERE t.id=so.courierid AND t.status=1),'-')
				) as shippingcompany
            ")
							->from(tbl_shippingorder." as so")
							->join(tbl_invoice." as i","i.id=so.invoiceid","INNER")
							->where("so.invoiceid=".$invoiceid)
							->get();
		$data['shippingdata'] = $query->row_array();
		
		$data['shippingdata']['shipdate'] = $this->general_model->displaydate($data['shippingdata']['shipdate']);
		if(!empty($data['shippingdata'])){
			$query = $this->db->select("sp.weight,sp.amount")
							->from(tbl_shippingpackage." as sp")
							->where("sp.shippingorderid=".$data['shippingdata']['id'])
							->get();
			$data['shippingpackagedata'] = $query->result_array();	
			if(empty($data['shippingpackagedata'])){
				$query = $this->db->select("spo.weight,spo.totalrate as amount")
							->from(tbl_shiprocketorder." as spo")
							->where("spo.shippingorderid=".$data['shippingdata']['id'])
							->get();
				$data['shippingpackagedata'] = $query->result_array();	
			}			
		}
		return json_encode($data);
	}
	function getRecipientDetails($invoiceid){

		$orderdetail = array();
		$query = $this->db->select("m.memberid,m.name as shippingname,m.mobileno as shippingmobileno,m.address as shippingaddress,m.postalcode as shippingpostcode,sp.code,m.email as shippingemail,
									sc.name as cityname,sp.name as provincename,scountry.name as countryname")
							->from(tbl_invoice." as i")
							->join(tbl_memberaddress." as m","m.id=i.shippingaddressid","INNER" )
							->join(tbl_city." as sc","sc.id=m.cityid","INNER")
							->join(tbl_province." as sp","sp.id=m.provinceid","INNER")
							->join(tbl_country." as scountry","scountry.id=sp.countryid","INNER")
							->where("i.id=".$invoiceid)
							->get();
		$rowdata =  $query->row_array();
		$address = ucwords($rowdata['shippingaddress']);
		$address = preg_replace('!\s+!', ' ', $address);
		$address = substr($address, 0, 70);
		$orderdetail = array("memberid"=>$rowdata['memberid'],
							"customername"=>ucwords($rowdata['shippingname']),
							"email"=>$rowdata['shippingemail'],
							"mobileno"=>$rowdata['shippingmobileno'],
							"address"=>$address,
							"postcode"=>$rowdata['shippingpostcode'],
							"cityname"=>ucwords($rowdata['cityname']),
							"code"=>$rowdata['code']);

		//print_r($orderdetail);exit;
		return $orderdetail;
	}

	/* function getShipperDetails(){
		$query = $this->db->select("address as businessaddress,'360002' as postcode,p.code,businessname,
									IFNULL((SELECT name FROM ".tbl_city." WHERE id=cityid),'') as cityname,
									'9725697970' as phonenumber,
									IFNULL((SELECT email FROM ".tbl_fedexdetail." WHERE memberid=0 AND channelid=0 LIMIT 1),'') as email
									")
							->from(tbl_settings." as is")
							->join(tbl_city." as c","c.id=is.cityid","INNER")
							->join(tbl_province." as p","p.id=c.stateid","INNER")
							->get();
							
		$rowdata =  $query->row_array();	
		
		$address = ucwords($rowdata['businessaddress']);
		$address = preg_replace('!\s+!', ' ', $address);
		$address = substr($address, 0, 70);
		$shipperdetail = array("businessname"=>$rowdata['businessname'],
							"email"=>$rowdata['email'],
							"phonenumber"=>$rowdata['phonenumber'],
							"businessaddress"=>$address,
							"postcode"=>$rowdata['postcode'],
							"cityname"=>ucwords($rowdata['cityname']),
							"code"=>$rowdata['code']);

		return $shipperdetail;

	} */
	/* function getShippingOrderDetials($orderid){
		
		$query = $this->db->select("IFNULL((SELECT cc.companyname FROM ".tbl_couriercompany." as cc WHERE cc.id=so.courierid AND cc.status=1),'India Post') as couriercompany,so.trackingcode,so.servicename,so.shippingamount")
							->from(tbl_shippingorder." as so")
							->where("so.orderid=".$orderid)
							->get();
		
		return $query->row_array();
	}
	function getRecipientdata($orderid){
		$query = $this->db->select("o.address,c.name as cityname,p.code,o.postcode")
							->from($this->_table." as o")
							->join(tbl_city ." as c","c.id=o.cityid","INNER")
							->join(tbl_province ." as p","p.id=c.provinceid","INNER")
							->where("o.id=".$orderid)
							->get();
		return $query->row_array();					
	}
	function totalpendingorder(){
		$query = $this->db->select("count(o.id) as totalpendingorder")
							->from($this->_table." as o")
							->join(tbl_invoice." as i","i.ordernumber=o.ordernumber","INNER")
							->where("(o.status=0 OR o.status=1 OR o.status=2)")
							->get();
		return $query->row_array();					
	}
	
	function getPendingOrder(){
		$query = $this->db->select("o.ordernumber,CONCAT(o.firstname,' ',o.lastname) as customername,GREATEST(ROUND((GREATEST(o.totalamount-o.discountvalue,0)+o.shippingamount+o.codamount),2),0) as amount,
									IFNULL((SELECT cc.companyname FROM ".tbl_couriercompany." as cc WHERE cc.id=o.courierid AND cc.status=1),'India Post') as couriercompany")
							->from($this->_table." as o")
							->join(tbl_invoice." as i","i.ordernumber=o.ordernumber","INNER")
							->where("(o.status=0 OR o.status=1 OR o.status=2)")
							->limit(10)
							->group_by("o.ordernumber")
							->order_by("o.id DESC")
							->get();

		return $query->result_array();					
	} */

	function getShippingLabel($invoiceid){
		$query = $this->db->select("fsl.file")
							->from(tbl_shippingorder." as so")
							->join(tbl_fedexshippinglabel ." as fsl","fsl.shippingorderid=so.id","INNER")
							->where("so.invoiceid=".$invoiceid)
							->get();
							
		return $query->result_array();
	}

	//LISTING DATA
	function _get_datatables_query($MEMBERID,$CHANNELID){
		
		//$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$buyerchannelid = (isset($_REQUEST['buyerchannelid']))?$_REQUEST['buyerchannelid']:'';
        $buyermemberid = (isset($_REQUEST['buyermemberid']))?$_REQUEST['buyermemberid']:'';
        $sellerchannelid = (isset($_REQUEST['sellerchannelid']))?$_REQUEST['sellerchannelid']:'';
        $sellermemberid = (isset($_REQUEST['sellermemberid']))?$_REQUEST['sellermemberid']:'';
		$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
		$todate = $this->general_model->convertdate($_REQUEST['todate']);
		$status = (isset($_REQUEST['status']))?$_REQUEST['status']:'';

		$this->readdb->select("i.id,i.invoicedate,i.status,i.memberid,
		
					buyer.name,buyer.mobile,buyer.membercode,
					buyer.channelid as buyerchannelid,
					
					IFNULL(seller.name,'') as sellername,
					IFNULL(seller.membercode,'') as sellercode,
					IFNULL(i.sellermemberid,'') as sellerid,
					IFNULL(seller.channelid,'') as sellerchannelid,
					IFNULL(seller.mobile,'') as sellermobile,

					IFNULL((SELECT trackingcode FROM ".tbl_shippingorder." WHERE invoiceid=i.id),'') as trackingcode,

					IFNULL((SELECT cc.trackurl FROM ".tbl_couriercompany." as cc WHERE cc.id=i.courierid AND cc.status=1),'') as trackingurl,

					IFNULL((SELECT spo.awb_code FROM ".tbl_shiprocketorder." spo INNER JOIN ".tbl_shippingorder." so ON so.id=spo.shippingorderid WHERE so.invoiceid=i.id),'') as awbcode,

					IFNULL((SELECT spo.shiprocketshipmentid FROM ".tbl_shiprocketorder." spo INNER JOIN ".tbl_shippingorder." so ON so.id=spo.shippingorderid WHERE so.invoiceid=i.id),'') as shiprocketshipmentid,

					IFNULL(
						(SELECT pm.name 
							FROM ".tbl_transaction." as tr 
							INNER JOIN ".tbl_paymentmethod." as pm on pm.id IN (SELECT paymentmethodid FROM ".tbl_paymentgateway." WHERE paymentgatewaytype=tr.paymentgetwayid GROUP BY paymentmethodid) 
							WHERE tr.orderid=i.orderid
					),'COD') as paymentmethod,

					i.courierid,
					IFNULL((SELECT cc.companyname FROM ".tbl_couriercompany." as cc WHERE cc.id=i.courierid AND cc.status=1),'') as shippingcompany,

					i.shippingby,            
					IF(i.shippingby=0,
						IFNULL((SELECT cc.companyname FROM ".tbl_couriercompany." as cc WHERE cc.id=i.courierid),'-'),
						IFNULL((SELECT t.companyname FROM ".tbl_transport." as t WHERE t.id=i.courierid),'-')
					) as shippingcompany,
					
					CASE WHEN i.status=0 THEN 'Book' WHEN i.status=3 THEN 'Pickup' WHEN i.status=4 THEN 'Shipping' WHEN i.status=1 THEN 'Delivered' WHEN i.status=5 THEN 'Return' WHEN i.status=2 THEN 'Cancel' END as invoicestatus,
										
					IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as amount,
					
					'' as codamount,
			
					'' as weight,
					i.invoiceno as invoicenumber,
					IF(length(shippingaddress)>70,'true','false') as extendshippingaddress
				");
				
        $this->readdb->from($this->_table." as i");
		$this->readdb->join(tbl_member." as buyer","buyer.id=i.memberid","LEFT");
		$this->readdb->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT");
		$this->readdb->where("i.memberid!=0 AND (DATE(i.createddate) BETWEEN '".$fromdate."' AND '".$todate."') AND (i.status=0 OR i.status=4) AND (i.status='".$status."' OR '".$status."'='')");
		
		if(!is_null($MEMBERID)){
            $type = isset($_REQUEST['type'])?$_REQUEST['type']:'sales';
            if($type=="purchase"){
                $this->readdb->where("i.memberid=".$MEMBERID." AND i.sellermemberid IN  (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.")");
            }else{
                $this->readdb->where("i.sellermemberid=".$MEMBERID);
            }
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

	function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		//print_r($query->result());exit;
		return $query->result();
	}

	function count_filtered($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		return $this->readdb->count_all_results();
	}
}
