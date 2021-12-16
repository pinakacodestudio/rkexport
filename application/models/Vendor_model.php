<?php

class Vendor_model extends Common_model {

	//put your code here
	public $_table = tbl_member;
	public $_fields = "*";
	public $_where = array();
	public $_order = '';
	public $_except_fields = array();
	public $column_order = array(null,'m.name',null,"(select count(id) from ".tbl_cart." where customerid=m.id and usertype=1)",'balance','m.createddate'); //set column field database for datatable orderable
	public $column_search = array('m.name','m.createddate','m.mobile','m.email'); //set column field database for datatable searchable 
    public $order = array('m.id' => 'DESC'); // default order 
    
    public $column_order_ba = array(null,'ma.name',null,'ma.email','ma.mobileno');
	public $column_search_ba = array('ma.name','ma.email','ma.address','ma.mobileno');
	public $_order_ba = array('ma.id' => 'DESC');

    public $column_order_po = array(null,'o.orderid','o.orderdate','orderstatus','payableamount');
    public $column_search_po = array('o.orderid','o.orderdate','(o.payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=o.id AND type=0),0))');
    public $_order_po = array('o.createddate' => 'DESC');
    
    public $column_order_pq = array(null,'q.quotationid','q.quotationdate','quotationtatus','payableamount');
	public $column_search_pq = array('q.quotationid','q.quotationdate','(q.payableamount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE referenceid=q.id AND type=1),0))');
    public $_order_pq = array('q.createddate' => 'DESC');
    
	function __construct() {
		parent::__construct();
	}
	function getVendorByProductId($productid){
		
		$this->readdb->select("m.id,(CONCAT(m.name,CONCAT(' (',m.membercode,' - ',m.mobile,')'))) as name");
		$this->readdb->from($this->_table." as m");
		$this->readdb->join(tbl_channel." as c","m.channelid=c.id","INNER");

		$this->readdb->where("
			IF(c.memberspecificproduct=1,
				(IF(
					(SELECT COUNT(mp.memberid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=0 and mp.memberid=m.id)>0,

					m.id IN(SELECT mp.memberid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.price>0) WHERE mp.sellermemberid=0 and mp.productid=".$productid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) GROUP BY mp.memberid),
					
					IF(
						((SELECT COUNT(mp.memberid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=0 and mp.
						memberid=m.id)=0),
						
						c.id IN (SELECT channelid FROM ".tbl_productbasicpricemapping." WHERE channelid = c.id AND salesprice >0 AND allowproduct = 1 AND productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=".$productid.") GROUP BY productid),
						
						m.id IN (SELECT mp.memberid FROM ".tbl_memberproduct." as mp INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.salesprice>0 AND mvp.productallow=1) WHERE mp.sellermemberid='0' and mp.memberid=m.id AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND (SELECT c2.memberbasicsalesprice FROM ".tbl_channel." as c2 WHERE c2.id=(SELECT m2.channelid FROM ".tbl_member." as m2 WHERE m2.id=mp.memberid))=1 GROUP BY mp.productid)
					)
				)),
				c.id IN (IF(c.memberbasicsalesprice=1,IFNULL((SELECT pbp.channelid FROM ".tbl_productbasicpricemapping." as pbp WHERE pbp.channelid=c.id AND pbp.productid='".$productid."' AND pbp.salesprice!=0 GROUP BY pbp.productid),0),c.id))
			)
		");

		$this->readdb->where("m.status=1 AND m.channelid='".VENDORCHANNELID."'");
		$this->readdb->order_by("m.name ASC");
		$query = $this->readdb->get();
		return $query->result_array();
	}
    function getVendorDataByIDForEdit($ID){
		$MEMBERID = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):0;
		$query = $this->readdb->select("m.id,m.channelid,m.name,m.membercode,m.image,m.mobile,m.countrycode,m.secondarymobileno,m.secondarycountrycode,m.email,m.password,m.debitlimit,m.status,m.memberratingstatusid,m.purchaseregularproduct,
										(select mainmemberid from ".tbl_membermapping." where submemberid=m.id limit 1)as sellermemberid,
										IFNULL((select p.countryid from ".tbl_province." as p where p.id=provinceid),0) as countryid,
										m.gstno,m.minimumstocklimit,m.provinceid,m.cityid,m.paymentcycle,m.emireminderdays,m.advancepaymentcod,
										m.parentmemberid,m.roleid,m.billingaddressid,m.shippingaddressid,
										IFNULL(ob.balancedate,'0000-00-00') as balancedate,
										IFNULL(ob.balance,0) as balance,
										IFNULL(ob.id,'') as balanceid")
								->from($this->_table." as m")
								->join(tbl_openingbalance." as ob","ob.memberid=m.id AND ob.sellermemberid=".$MEMBERID,"LEFT")
								->where("m.id='".$ID."'")
								->get();
		return $query->row_array();
    }
    
    //LISTING VENDOR DATA
    function _get_datatables_query(){

        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        $sellermemberid = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):0;

        $this->readdb->select("DISTINCT(m.id),
                    m.channelid,m.name,m.membercode,m.mobile,m.email,m.status,m.createddate,m.emailverified,
                    (select count(id) from ".tbl_cart." where memberid=m.id)as cartcount,
                   
                    IFNULL(ob.id,0) as balanceid,
                    IFNULL(ob.balancedate,'') as balancedate,
                    IFNULL(ob.balance,0) as balance,

                    ");
                    
        $this->readdb->from($this->_table." as m");
        $this->readdb->join(tbl_openingbalance." as ob","ob.memberid=m.id AND ob.sellermemberid=".$sellermemberid,"LEFT");

        $this->readdb->where("m.channelid = '".VENDORCHANNELID."'");
        $this->readdb->where("date(m.createddate) BETWEEN '".$startdate."' AND '".$enddate."'");
        
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
        } else if(isset($this->order)){
            $order = $this->order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    //LISTING VENDOR BILLING ADDRESS DATA
    function _get_datatables_query_billingaddress(){
        
        if(isset($_REQUEST['vendorid'])){
			$vendorid=$this->db->escape($_REQUEST['vendorid']);
		}else{
			$vendorid=$this->db->escape(0);
		}
		
		$this->readdb->select("ma.id, ma.name,ma.address,ma.town, ma.postalcode, ma.email,ma.mobileno,ma.createddate,ma.status");
		$this->readdb->from(tbl_memberaddress." as ma");
		$this->readdb->where('(ma.addedby='.$vendorid.' OR ma.memberid='.$vendorid.') AND ma.status <> 2');
		// $this->db->group_by('ma.email');
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search_ba as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search_ba) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order_ba[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order_ba)) {
			$order = $this->_order_ba;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
    }

    //LISTING VENDOR PURCHASE ORDER DATA
    function _get_datatables_query_purchaseorder(){
		
		$vendorid=$this->db->escape($_REQUEST['vendorid']);
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$status = $_REQUEST['status'];
		
		$this->readdb->select("o.id,o.orderid,o.status, 
        (select sum(finalprice) from ".tbl_orderproducts." where orderid = o.id ) as finalprice,
		o.orderdate, o.createddate as date, o.memberid,
		CASE 
			WHEN o.status=0 THEN 'Pending'
			WHEN o.status=1 THEN 'Complete'
			WHEN o.status=2 THEN 'Cancel'
		END as orderstatus,
        (o.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as payableamount,o.sellermemberid");
		
		$this->readdb->from(tbl_orders." as o");
		$this->readdb->where("o.sellermemberid =".$vendorid." AND o.memberid=0 AND o.isdelete=0");
        
		if($status != -1){
			$this->readdb->where("o.status=".$status);
		}
		$this->readdb->where("(o.orderdate BETWEEN '".$startdate."' AND '".$enddate."')");
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search_po as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search_po) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order_po[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order_po)) {
			$order = $this->_order_po;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
    }
    
    //LISTING VENDOR PURCHASE QUOTATION DATA
    function _get_datatables_query_purchasequotation(){
		
		$vendorid=$this->db->escape($_REQUEST['vendorid']);
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$status = $_REQUEST['status'];

		$this->readdb->select("q.id,q.quotationid,q.quotationdate,q.status,q.createddate as date,q.quotationamount,
						q.memberid,q.sellermemberid,
						CASE 
							WHEN q.status=0 THEN 'Pending'
							WHEN q.status=1 THEN 'Complete'
							WHEN q.status=2 THEN 'Rejected' 
							WHEN q.status=3 THEN 'Cancel'
						END as quotationtatus,
						(q.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=q.id AND type=1),0)) as payableamount");
		
		$this->readdb->from(tbl_quotation." as q");
		$this->readdb->where("q.sellermemberid = ".$vendorid." AND q.memberid=0");
        
		if($status != -1){
			$this->readdb->where("q.status=".$status);
		}
		$this->readdb->where("q.quotationdate BETWEEN '".$startdate."' AND '".$enddate."'");
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search_pq as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search_pq) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order_pq[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order_pq)) {
			$order = $this->_order_pq;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
    }

    function get_datatables($list='vendor') {
        
        if($list=='billingaddress'){
            $this->_get_datatables_query_billingaddress();
        }else if($list=='purchaseorder'){
            $this->_get_datatables_query_purchaseorder();
        }else if($list=='purchasequotation'){
            $this->_get_datatables_query_purchasequotation();
        }else{
            $this->_get_datatables_query();
        }
        
        if($_POST['length'] != -1)
        $this->readdb->limit($_POST['length'], $_POST['start']);
        $query = $this->readdb->get();
        // echo $this->readdb->last_query();exit;
        return $query->result();
    }

    function count_filtered($list='vendor') {
        
        if($list=='billingaddress'){
            $this->_get_datatables_query_billingaddress();
        }else if($list=='purchaseorder'){
            $this->_get_datatables_query_purchaseorder();
        }else if($list=='purchasequotation'){
            $this->_get_datatables_query_purchasequotation();
        }else{
            $this->_get_datatables_query();
        }
        $query = $this->readdb->get();
        return $query->num_rows();
    }

    function count_all($list='vendor') {
        
        if($list=='billingaddress'){
            $this->_get_datatables_query_billingaddress();
        }else if($list=='purchaseorder'){
            $this->_get_datatables_query_purchaseorder();
        }else if($list=='purchasequotation'){
            $this->_get_datatables_query_purchasequotation();
        }else{
            $this->_get_datatables_query();
        }
        return $this->readdb->count_all_results();
    }

    function getVendorDetail($vendorid){

		$this->readdb->select("m.id,m.channelid,m.name,m.image,m.email,m.mobile,m.createddate,m.debitlimit,m.gstno,m.membercode,m.countrycode,m.emailverified,m.purchaseregularproduct,
							IFNULL(c.name,'') as cityname,
							IFNULL(p.name,'') as provincename,
							IFNULL(country.name,'') as countryname,
							minimumstocklimit,
							IFNULL((select mrs.name FROM ".tbl_memberratingstatus." as mrs where mrs.id=m.memberratingstatusid),'') as memberratingstatus,
							paymentcycle,memberratingstatusid,emireminderdays,
							IFNULL((SELECT count(id) FROM ".tbl_memberproduct." where memberid='".$vendorid."'),0) as totalproductcount");
        
        $this->readdb->from($this->_table." as m");
		$this->readdb->join(tbl_city." as c","c.id=m.cityid","LEFT");
		$this->readdb->join(tbl_province." as p","p.id=m.provinceid","LEFT");
		$this->readdb->join(tbl_country." as country","country.id=p.countryid","LEFT");
		//$this->db->join(tbl_memberaddress." as ca","ca.memberid=c.id","LEFT");
		$this->readdb->where("m.id=".$vendorid);
		$query = $this->readdb->get();
		
		return $query->row_array();
    }
    
    function generateQRCode($vendorid){

		$vendordata = $this->getVendorDetail($vendorid);
		
		$address = $vendordata['cityname'].", ".$vendordata['provincename'].", ".$vendordata['countryname'];
		$vendordetail = "";
		$vendordetail .= "VERSION:2.1\n";
		$vendordetail .= "BEGIN:VCARD\n";
		$vendordetail .= "N:".$vendordata['name']."\n";
		$vendordetail .= "ORG:".$vendordata['membercode']."\n";
		$vendordetail .= "TEL;WORK;VOICE:+".$vendordata['countrycode']." ".$vendordata['mobile']."\n";
		$vendordetail .= "EMAIL:".$vendordata['email']."\n";
		$vendordetail .= "ADR;TYPE=work;LABEL='Address':".$address."\n";
		$vendordetail .= "END:VCARD\n";
		
		return urlencode($vendordetail); 
    }

    function getVendorShippingDetail($vendorid){

		$this->readdb->select("ma.id,ma.name,ma.address,ma.town, ma.postalcode, ma.email,ma.mobileno,ma.createddate");
		$this->readdb->from(tbl_memberaddress." as ma");
		//$this->db->join(tbl_memberaddress." as ca","ca.memberid=c.id","LEFT");
		$this->readdb->group_by('ma.email');
		$this->readdb->where(array('ma.memberid'=>$vendorid));
		$query = $this->readdb->get();
		
		return $query->result_array();
	}

	function getVendorIdentityproofData($vendorid){
		
		$this->readdb->select('mip.id,mip.memberid,mip.idproof,mip.title,mip.modifieddate,mip.status');
        $this->readdb->from(tbl_memberidproof." as mip");
        $this->readdb->where(array('mip.memberid'=>$vendorid));
        
        $query = $this->readdb->get();
		
		return $query->result_array();
		
	}
	
	function getVendorDataByID($ID){
		$query = $this->readdb->select("id,name,image,email,mobile as mobileno,status,roleid,reportingto,channelid,membercode")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function getActiveVendorData($concatname=''){

		if($concatname=='withcodeormobile'){
			$select = ",(CONCAT(name,CONCAT(' (',membercode,' - ',mobile,')'))) as name";
		}else{
			$select = ",name";
		}
		
		$query = $this->readdb->query("SELECT temp.* 
								
									FROM (
										SELECT id,membercode,billingaddressid,shippingaddressid".$select.",IF(minimumorderamount>0,minimumorderamount,(SELECT minimumorderamount FROM ".tbl_channel." WHERE id=channelid)) as minimumorderamount FROM ".tbl_member." WHERE channelid = '".VENDORCHANNELID."' AND status=1
									) as temp 
									
									ORDER BY temp.name ASC
								");
	
		return $query->result_array();
	}
	function getChannelSettingsByVendorID($vendorid){

		$query = $this->readdb->select("c.edittaxrate,c.partialpayment,c.memberspecificproduct,c.discount
								")
						->from(tbl_channel." as c")
						->where(array("c.id = (SELECT channelid FROM ".tbl_member." WHERE id=".$vendorid.")"=>null))
						->get();
		
		if($query->num_rows() == 1) {
			return $query->row_array();
		}else{
			return array();
		}
	}
	function getVendorByGRNInAdmin(){
		
		$query = $this->readdb->select("id,(CONCAT(name,CONCAT(' (',membercode,' - ',mobile,')'))) as name")
							->from(tbl_member." as m")
							->where("m.channelid = '".VENDORCHANNELID."' AND m.id IN (SELECT sellermemberid FROM ".tbl_goodsreceivednotes." WHERE memberid=0)")
							->get();
		return $query->result_array();
	}
	function getVendorByPurchaseInvoice(){
		
		$query = $this->readdb->select("id,(CONCAT(name,CONCAT(' (',membercode,' - ',mobile,')'))) as name")
							->from(tbl_member." as m")
							->where("m.channelid = '".VENDORCHANNELID."' AND m.id IN (SELECT sellermemberid FROM ".tbl_invoice." WHERE memberid=0)")
							->get();
		return $query->result_array();
	}
	function getVendorByPurchaseCreditNote(){
		
		$query = $this->readdb->select("id,(CONCAT(name,CONCAT(' (',membercode,' - ',mobile,')'))) as name")
							->from(tbl_member." as m")
							->where("m.channelid = '".VENDORCHANNELID."' AND m.id IN (SELECT sellermemberid FROM ".tbl_creditnote." WHERE buyermemberid=0)")
							->get();
		return $query->result_array();
	}
	function getVendorByPayment(){
		
		$query = $this->readdb->select("id,(CONCAT(name,CONCAT(' (',membercode,' - ',mobile,')'))) as name")
							->from(tbl_member." as m")
							->where("m.channelid = '".VENDORCHANNELID."' AND m.id IN (SELECT sellermemberid FROM ".tbl_paymentreceipt." WHERE memberid=0)")
							->get();
		return $query->result_array();
	}
}
