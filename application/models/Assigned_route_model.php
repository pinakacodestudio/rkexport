<?php

class Assigned_route_model extends Common_model {

	//put your code here
	public $_table = tbl_assignedroute;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('id' => 'DESC'); // default order 
	public $column_order = array(null,'r.route','salespersonname','vehicle','ar.startdate','ar.time',null,null,'status','assignedby'); //set column field database for datatable orderable
	public $column_search = array('r.route',"(CONCAT(v.vehiclename,' (',v.vehicleno,')'))","(DATE_FORMAT(ar.startdate, '%d/%m/%Y'))","(DATE_FORMAT(ar.time,'%h:%i'))","(SELECT name FROM ".tbl_user." WHERE id=ar.addedby)"); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
	}
	
	function getMembersByAssignedRoute($assignedrouteid){
		
		$query = $this->readdb->select("m.id, CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')') as name")
				->from(tbl_assignedrouteinvoicemapping." as arim")
				->join(tbl_member." as m","m.id=arim.memberid","INNER")
				->where("arim.assignedrouteid = ".$assignedrouteid."")
				->group_by("arim.memberid")
				->get();

		return $query->result_array();
	}

	function getAssignedByUserList(){
		
		$query = $this->readdb->select("ar.addedby as id,(SELECT name FROM ".tbl_user." WHERE id=ar.addedby) as name")
				->from(tbl_assignedroute." as ar")
				->group_by("ar.addedby")
				->get();

		return $query->result_array();
	}

	function getInvoiceDataByAssignedRoute($assignedrouteid){
		
		$query = $this->readdb->select("i.invoiceno,
					IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM transactiondiscount WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM extrachargemapping WHERE type=2 AND referenceid=i.id),0)),0) as invoiceamount
				")

				->from(tbl_assignedrouteinvoicemapping." as arim")
				->join(tbl_invoice." as i","i.id=arim.invoiceid","INNER")
				->where("arim.assignedrouteid = ".$assignedrouteid."")
				->get();

		return $query->result_array();
	}
	function getRouteByEmployee($employeeid) {
		
		$query = $this->readdb->select("r.id,r.route")
					->from(tbl_route." as r")
					->join($this->_table." as ar","ar.routeid=r.id","INNER")
					->where("r.isdelete=0 AND ar.employeeid = ".$employeeid."")
					->group_by("ar.routeid")
					->get();

		return $query->result_array();
	}
	function getVehicleByEmployeeId($employeeid,$channelid=0,$memberid=0){
        
        $query = $this->readdb->select("v.id,
                        CONCAT(v.vehiclename,' (',v.vehicleno,')') as name,
                        
                    ")
				->from(tbl_vehicle." as v")
				->join($this->_table." as ar","ar.vehicleid=v.id","INNER")
				//->where("v.employeeid = ".$employeeid." AND ar.employeeid = ".$employeeid." AND v.channelid=".$channelid." AND v.memberid=".$memberid)
				->where("ar.employeeid = ".$employeeid)
				->group_by("ar.vehicleid")
                ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return 0;
        }	
    }
	function getAssignedRouteList($assignedrouteid){
		
		$query = $this->readdb->select("arep.id,arep.reason,arep.memberid,CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')') as membername,m.channelid,(SELECT route FROM ".tbl_route." WHERE id=ar.routeid) as route,
					arep.invoiceid,arep.isvisited,arep.image,
					IFNULL(i.invoiceno,'') as invoiceno,
					IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as invoiceprice
				")
				->from(tbl_assignedrouteinvoicemapping." as arep")
				->join(tbl_assignedroute." as ar","ar.id=arep.assignedrouteid","INNER")
				->join(tbl_member." as m","m.id=arep.memberid","INNER")
				->join(tbl_invoice." as i","i.id=arep.invoiceid","LEFT")
				->where("arep.assignedrouteid = ".$assignedrouteid)
				->get();

		return $query->result_array();
	}
	function getAssignedRouteProductList($assignedrouteid){
		
		$query = $this->readdb->query("

					SELECT 
						temp.productname,
						temp.variantname,
						SUM(temp.quantity) as quantity,
						temp.price,
						temp.tax,
						CAST(IFNULL(((temp.price + temp.price*temp.tax/100) * SUM(temp.quantity)), 0) AS DECIMAL(14, 2)) as totalprice
					FROM (
						
						SELECT tp.productid,tp.priceid,SUM(tp.quantity) as quantity, tp.price, tp.tax, 
							CAST(IFNULL(((tp.price + tp.price*tp.tax/100) * SUM(tp.quantity)), 0) AS DECIMAL(14, 2)) as totalprice, tp.name as productname, IFNULL((SELECT GROUP_CONCAT(tv.variantvalue) FROM ".tbl_transactionvariant." as tv WHERE tv.transactionproductid=tp.referenceproductid AND tv.transactionid=tp.transactionid), '') as variantname
				
						FROM ".tbl_transactionproducts." as tp
						WHERE tp.transactionid IN (SELECT invoiceid FROM ".tbl_assignedrouteinvoicemapping." WHERE assignedrouteid=".$assignedrouteid.") AND tp.transactiontype = 3
						GROUP BY tp.productid, tp.priceid, tp.price, tp.tax
				
						UNION ALL
				
						SELECT arep.productid,arep.priceid,SUM(arep.quantity) as quantity, arep.price, arep.tax, 
							CAST(IFNULL(((arep.price + arep.price*arep.tax/100) * SUM(arep.quantity)), 0) AS DECIMAL(14, 2)) as totalprice, p.name as productname, IFNULL((SELECT GROUP_CONCAT(v.value) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v ON v.id=pc.variantid WHERE pc.priceid=arep.priceid),'') as variantname
				
						FROM ".tbl_assignedrouteextraproduct." as arep
						INNER JOIN ".tbl_product." as p ON p.id=arep.productid
						WHERE arep.assignedrouteid=".$assignedrouteid."
						GROUP BY arep.productid, arep.priceid, arep.price, arep.tax
					
					) as temp
					GROUP BY temp.productid, temp.priceid, temp.price, temp.tax
			");
			
			// echo $this->readdb->last_query(); exit;
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
	function getAssignedRouteDataByID($id){
		
		$query = $this->readdb->select("ar.id,ar.employeeid,ar.provinceid,ar.cityid,ar.routeid,ar.vehicleid,ar.capacity,ar.startdate,ar.time,ar.totalweight,ar.loosmoney,
		(SELECT GROUP_CONCAT(DISTINCT memberid) FROM ".tbl_assignedrouteinvoicemapping." WHERE assignedrouteid=ar.id) as memberid,
		(SELECT GROUP_CONCAT(DISTINCT invoiceid) FROM ".tbl_assignedrouteinvoicemapping." WHERE assignedrouteid=ar.id AND invoiceid!=0) as invoiceid,

		IFNULL((SELECT route FROM ".tbl_route." WHERE id=ar.routeid),'') as routename,
		IFNULL((SELECT CONCAT(vehiclename,' (',vehicleno,')') as name FROM ".tbl_vehicle." WHERE id=ar.vehicleid),'') as vehiclename
		")
					->from($this->_table." as ar")
					->where("ar.id='".$id."'")
					->get();
		
		if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
	}
	function getExtraProductsByAssignedRouteID($assignedrouteid){
		
		$query = $this->readdb->select("arep.id,arep.productid,arep.priceid,arep.quantity,arep.price,arep.tax,arep.totalprice
			")
			->from(tbl_assignedrouteextraproduct." as arep")
			->join(tbl_product." as p","p.id=arep.productid","INNER")
			->where("arep.assignedrouteid='".$assignedrouteid."'")
			->get();
		
		if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
    //LISTING DATA
	function _get_datatables_query(){
		
		$routeid = isset($_REQUEST['routeid'])?$_REQUEST['routeid']:'0';
		$employeeid = isset($_REQUEST['employeeid'])?$_REQUEST['employeeid']:'0';
		$assignedbyid = isset($_REQUEST['assignedbyid'])?$_REQUEST['assignedbyid']:'0';
		
		$this->readdb->select("ar.id,r.route,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehicle,
					ar.startdate,ar.time,ar.cityid,ar.provinceid,ar.createddate,
					IFNULL((SELECT name FROM ".tbl_user." WHERE id=ar.employeeid),'') as salespersonname,
					(SELECT name FROM ".tbl_user." WHERE id=ar.addedby) as assignedby,
					ar.status
				");
		$this->readdb->from($this->_table." as ar");
		$this->readdb->join(tbl_route." as r","r.id=ar.routeid","INNER");
		$this->readdb->join(tbl_vehicle." as v","v.id=ar.vehicleid","LEFT");
		$this->readdb->where("(ar.routeid = ".$routeid." OR ".$routeid."=0)");
		$this->readdb->where("(ar.employeeid = ".$employeeid." OR ".$employeeid."=0)");
		$this->readdb->where("(ar.addedby = ".$assignedbyid." OR ".$assignedbyid."=0)");
		
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
