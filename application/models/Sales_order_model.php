<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sales_order_model extends Common_model
{

    public $_table = tbl_sorder;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = array('id' => 'DESC');

    //set column field database for datatable orderable
    public $column_order = array('serial_number', 'sgv.voucherno', 'sgv.voucherdate', 'productname', 'sgvp.price', 'sgvp.quantity', 'sgvp.totalprice', 'typename', 'sgv.createddate');

    //set column field database for datatable searchable
    public $column_search = array('sgv.voucherno', 'DATE_FORMAT(sgv.voucherdate,"%d/%m/%Y")', 'DATE_FORMAT(sgv.createddate,"%d %b %Y %h:%i %p")', 'sgvp.quantity', 'sgvp.price', 'sgvp.totalprice', "(CONCAT(p.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM " . tbl_productcombination . " as pc INNER JOIN " . tbl_variant . " as v on v.id=pc.variantid WHERE pc.priceid=sgvp.priceid),'')))", "(IF(sgvp.type=1,'Increment','Decrement'))");

    public function __construct()
    {
        parent::__construct();
    }

    public function getStockGeneralVoucherDataByIDs($IDs)
    {

        $query = $this->readdb->select("sgv.id,sgv.voucherno,sgv.voucherdate")
            ->from($this->_table . " as sgv")
            ->where("sgv.id IN (" . implode(",", $IDs) . ")")
            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }
    public function getStockGeneralVoucherProductDataByIDs($voucherproductids)
    {

        $query = $this->readdb->select("sgvp.id,sgvp.productid,sgvp.priceid,sgvp.price,
					CONCAT((SELECT voucherdate FROM " . tbl_stockgeneralvoucher . " WHERE id=sgvp.stockgeneralvoucherid),'_',sgvp.productid,'_',sgvp.priceid,'_',sgvp.price) as uniquestring
			")
            ->from(tbl_stockgeneralvoucherproducts . " as sgvp")
            ->where("sgvp.id IN (" . implode(",", $voucherproductids) . ")")
            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function getStockGeneralVoucherDataByID($ID)
    {

        $query = $this->readdb->select("sgv.id,sgv.voucherno,sgv.voucherdate")
            ->from($this->_table . " as sgv")
            ->where("sgv.id='" . $ID . "'")
            ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return array();
        }
    }
    public function getStockGeneralVoucherProductsByVoucherID($stockgeneralvoucherid)
    {

        $query = $this->readdb->select("sgvp.id,sgvp.stockgeneralvoucherid,sgvp.productid,sgvp.priceid,sgvp.quantity,sgvp.type,sgvp.narrationid,sgvp.price,sgvp.totalprice")
            ->from(tbl_stockgeneralvoucherproducts . " as sgvp")
            ->where("sgvp.stockgeneralvoucherid='" . $stockgeneralvoucherid . "'")
            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }
    public function getStockGeneralVoucherProductDataByID($id)
    {

        $query = $this->readdb->select("sgvp.id,
				sgvp.productid,sgvp.priceid,
				CONCAT((SELECT name FROM " . tbl_product . " WHERE id=sgvp.productid),' ',IFNULL(
					(SELECT CONCAT('[',GROUP_CONCAT(v.value),']')
						FROM " . tbl_productcombination . " as pc INNER JOIN " . tbl_variant . " as v on v.id=pc.variantid WHERE pc.priceid=sgvp.priceid),'')
				) as productname,
				sgvp.quantity,sgvp.type")
            ->from(tbl_stockgeneralvoucherproducts . " as sgvp")
            ->where("sgvp.id='" . $id . "'")
            ->get();

        if ($query->num_rows() == 1) {
            log_message("error", $this->readdb->last_query());
            return $query->row_array();
        } else {
            return array();
        }
    }
    public function deleteVouchersByVoucherProductId($ids)
    {
        $query = $this->readdb->select("sgvp.id,sgvp.stockgeneralvoucherid,sgvp.productid,sgvp.priceid,sgvp.quantity,sgvp.type")
            ->from(tbl_stockgeneralvoucherproducts . " as sgvp")
            ->where("sgvp.id IN (" . implode(",", $ids) . ")")
            ->group_by("sgvp.stockgeneralvoucherid")
            ->get();

        if ($query->num_rows() > 0) {
            $data = $query->result_array();

            if (!empty($data)) {
                foreach ($data as $row) {
                    $this->Stock_general_voucher->Delete(array("id" => $row['stockgeneralvoucherid'], "(IFNULL((SELECT count(id) FROM " . tbl_stockgeneralvoucherproducts . " WHERE stockgeneralvoucherid=" . $row['stockgeneralvoucherid'] . " AND id NOT IN (" . implode(",", $ids) . ")),0)=0)" => null));
                }
            }
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            return $query->result();
        }
    }

	function _get_datatables_query(){
		
		$salespersonid = isset($_REQUEST['salespersonid'])?$_REQUEST['salespersonid']:'0';
        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:'0';
        $status = isset($_REQUEST['status'])?$_REQUEST['status']:'-1';
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);
		//o.orderid,buyer.name as buyername,buyer.membercode as buyercode,buyer.channelid as buyerchannelid, o.status,IF(o.salespersonid!=0,(@netamount*o.commission/100),0) as commissionamount 

		//buyer.id as buyerid, IFNULL((SELECT name FROM ".tbl_user." WHERE id=o.salespersonid),'') as salespersonname

		//@netamount:=(payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as netamount, o.salespersonid,o.commission,o.commissionwithgst,
        $this->readdb->select("o.id,o.remarks,o.partyid,o.inquiryno,o.inquiryno,o.orderno,o.clientpono,o.dicountamount,c.companyname,u.name as username");
        $this->readdb->from($this->_table." as o");
        $this->readdb->join(tbl_party." as p","o.partyid=p.id","LEFT");
        $this->readdb->join(tbl_company." as c","p.companyid=c.id","LEFT");
        $this->readdb->join(tbl_user." as u","o.addedby=u.id","LEFT");
        // $this->readdb->where("o.memberid!=0 AND ((o.salespersonid!=0 OR o.id IN (SELECT orderid FROM ".tbl_orderproduct." WHERE salespersonid!=0))) AND ((o.salespersonid = ".$salespersonid." OR ".$salespersonid."=0 OR o.id IN (SELECT orderid FROM ".tbl_orderproduct." WHERE (salespersonid=".$salespersonid." OR ".$salespersonid."=0))))");
        // $this->readdb->where("(buyer.channelid = ".$channelid." OR ".$channelid."=0)");
        // $this->readdb->where("(o.status = ".$status." OR '".$status."'='-1')");
        // $this->readdb->where("(o.podate BETWEEN '".$fromdate."' AND '".$todate."')");

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

    public function count_all()
    {
        $this->_get_datatables_query();
        return $this->readdb->count_all_results();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

	function getpartydata(){
        $this->readdb->select("c.id,c.companyname as name");
        $this->readdb->from(tbl_company." as c");
		$readdb = $this->readdb->get();
		return $readdb->result();
	}
}
?>
