<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Raw_material_request_model extends Common_model {

	public $_table = tbl_rawmaterialrequest;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('rmr.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'ordernumber','rmr.requestno','addedbyname','rmr.requestdate','statusname');

	//set column field database for datatable searchable 
	public $column_search = array("((IFNULL((SELECT orderid FROM ".tbl_orders." WHERE id=rmr.orderid),'')))",'rmr.requestno','rmr.requestdate','((SELECT name FROM '.tbl_user.' WHERE id=rmr.addedby))',"((CASE WHEN rmr.status = 0 THEN 'Pending' WHEN rmr.status = 1 THEN 'Approve' ELSE 'Cancel' END))");

	function __construct() {
		parent::__construct();
    }
	function getRawMaterialRequestDataByID($ID){
       
        $query = $this->readdb->select("id,orderid,requestno,requestdate,estimatedate,remarks")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
    }
    function getRawMaterialRequestProductsByRequestID($rowmaterialrequestid){
       
        $query = $this->readdb->select("id,rawmaterialrequestid,productid,priceid,unitid,quantity,CONCAT(productname,' ',
		IFNULL((SELECT CONCAT('[',GROUP_CONCAT(v.value),']') FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_variant." as v on v.id=pc.variantid WHERE pc.priceid=rmp.priceid),'')) as productname,
		IFNULL((SELECT name FROM ".tbl_productunit." WHERE id=rmp.unitid),'-') as unitname,
        (SELECT remarks FROM ".tbl_rawmaterialrequest." WHERE id = rmp.rawmaterialrequestid) as remarks
        ")
							->from(tbl_rawmaterialrequestproduct." as rmp")
							->where("rawmaterialrequestid='".$rowmaterialrequestid."'")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
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
		
        $this->readdb->select("rmr.id,rmr.orderid,rmr.requestno,rmr.requestdate,rmr.estimatedate,rmr.remarks,rmr.status,rmr.createddate,rmr.addedby,
                IFNULL((SELECT orderid FROM ".tbl_orders." WHERE id=rmr.orderid),'') as ordernumber,
                (SELECT name FROM ".tbl_user." WHERE id=rmr.addedby) as addedbyname,

                CASE 
                    WHEN rmr.status = 0 THEN 'Pending'
                    WHEN rmr.status = 1 THEN 'Approve'
                    ELSE 'Cancel'
                    END
                as statusname

            ");
		$this->readdb->from($this->_table." as rmr");
        
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
