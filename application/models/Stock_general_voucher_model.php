<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_general_voucher_model extends Common_model {

	public $_table = tbl_stockgeneralvoucher;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('sgv.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array('serial_number','sgv.voucherno','sgv.voucherdate','productname','sgvp.price','sgvp.quantity','sgvp.totalprice','typename','sgv.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('sgv.voucherno','DATE_FORMAT(sgv.voucherdate,"%d/%m/%Y")','DATE_FORMAT(sgv.createddate,"%d %b %Y %h:%i %p")','sgvp.quantity','sgvp.price','sgvp.totalprice',"(CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=sgvp.priceid),'')))","(IF(sgvp.type=1,'Increment','Decrement'))");

	function __construct() {
		parent::__construct();
    }
	
	function getStockGeneralVoucherDataByIDs($IDs){
       
        $query = $this->readdb->select("sgv.id,sgv.voucherno,sgv.voucherdate")
							->from($this->_table." as sgv")
							->where("sgv.id IN (".implode(",", $IDs).")")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
	function getStockGeneralVoucherProductDataByIDs($voucherproductids){
       
        $query = $this->readdb->select("sgvp.id,sgvp.productid,sgvp.priceid,sgvp.price,
				CONCAT((SELECT voucherdate FROM ".tbl_stockgeneralvoucher." WHERE id=sgvp.stockgeneralvoucherid),'_',sgvp.productid,'_',sgvp.priceid,'_',sgvp.price) as uniquestring
		")
							->from(tbl_stockgeneralvoucherproducts." as sgvp")
							->where("sgvp.id IN (".implode(",", $voucherproductids).")")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
	

	function getStockGeneralVoucherDataByID($ID){
       
        $query = $this->readdb->select("sgv.id,sgv.voucherno,sgv.voucherdate")
							->from($this->_table." as sgv")
							->where("sgv.id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    function getStockGeneralVoucherProductsByVoucherID($stockgeneralvoucherid){
       
        $query = $this->readdb->select("sgvp.id,sgvp.stockgeneralvoucherid,sgvp.productid,sgvp.priceid,sgvp.quantity,sgvp.type,sgvp.narrationid,sgvp.price,sgvp.totalprice")
							->from(tbl_stockgeneralvoucherproducts." as sgvp")
							->where("sgvp.stockgeneralvoucherid='".$stockgeneralvoucherid."'")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
    }
	function getStockGeneralVoucherProductDataByID($id){
       
        $query = $this->readdb->select("sgvp.id,
			sgvp.productid,sgvp.priceid,
			CONCAT((SELECT name FROM ".tbl_product." WHERE id=sgvp.productid),' ',IFNULL(
                (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
        			FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=sgvp.priceid),'')
            ) as productname,
			sgvp.quantity,sgvp.type")
							->from(tbl_stockgeneralvoucherproducts." as sgvp")
							->where("sgvp.id='".$id."'")
							->get();
							
		if ($query->num_rows() == 1) {
			log_message("error", $this->readdb->last_query());
			return $query->row_array();
		}else {
			return array();
		}
    }
	function deleteVouchersByVoucherProductId($ids){
		$query = $this->readdb->select("sgvp.id,sgvp.stockgeneralvoucherid,sgvp.productid,sgvp.priceid,sgvp.quantity,sgvp.type")
				->from(tbl_stockgeneralvoucherproducts." as sgvp")
				->where("sgvp.id IN (".implode(",",$ids).")")
				->group_by("sgvp.stockgeneralvoucherid")
				->get();
				
		if ($query->num_rows() > 0) {
			$data = $query->result_array();

			if(!empty($data)){
				foreach($data as $row){
					$this->Stock_general_voucher->Delete(array("id"=>$row['stockgeneralvoucherid'],"(IFNULL((SELECT count(id) FROM ".tbl_stockgeneralvoucherproducts." WHERE stockgeneralvoucherid=".$row['stockgeneralvoucherid']." AND id NOT IN (".implode(",",$ids).")),0)=0)"=>null));
				}
			}
		}	
	}

    function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
            return $query->result();
		}
	}

	function _get_datatables_query(){
		
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];
		$type = $_REQUEST['type'];

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		
		$this->readdb->query("SET @srno:=0;");
        $this->readdb->select("@srno:=@srno+1 as serial_number,sgv.id,sgv.voucherno,sgv.voucherdate,sgv.createddate,sgvp.productid,sgvp.priceid,IFNULL((SELECT narration FROM ".tbl_narration." where id=sgvp.narrationid),'') as narration,
                
            CONCAT(p.name,' ',IFNULL(
                (SELECT CONCAT('[',GROUP_CONCAT(v.value),']') 
                    FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=sgvp.priceid),'')
            ) as productname,

            sgvp.quantity,sgvp.type,
			sgvp.id as voucherproductsid,sgvp.price,sgvp.totalprice,
			
			IF(sgvp.type=1,'Increment','Decrement') as typename

        ", false);
		$this->readdb->from($this->_table." as sgv");
        $this->readdb->join(tbl_stockgeneralvoucherproducts." as sgvp","sgvp.stockgeneralvoucherid=sgv.id","INNER");
        $this->readdb->join(tbl_product." as p","p.id=sgvp.productid","INNER");
        $this->readdb->where("(sgvp.productid='".$productid."' OR '".$productid."'='0')");
        $this->readdb->where("(sgvp.type='".$type."' OR '".$type."'='')");
		$this->readdb->where("(sgv.voucherdate BETWEEN '".$startdate."' AND '".$enddate."')");

		if(!is_null($MEMBERID)){
			$this->readdb->where("sgv.channelid='".$CHANNELID."' AND sgv.memberid='".$MEMBERID."'");	
		}else{
			$this->readdb->where("sgv.channelid=0 AND sgv.memberid=0");	
		}
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
			$order = $this->_order;
			$this->readdb->order_by(key($order), $order[key($order)]);
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
 ?>            
