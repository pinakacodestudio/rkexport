<?php
class Expire_insurance_report_model extends Common_model {

	//put your code here
	public $_table = tbl_insurance;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array('v.vehiclename','i.companyname','i.policyno','i.fromdate','i.todate','i.amount','i.createddate','days'); //set column field database for datatable orderable
	public $column_search = array("(CONCAT(v.vehiclename,' (',v.vehicleno,')'))",'i.companyname','i.policyno','(CONCAT(DATE_FORMAT(i.fromdate, "%d/%m/%Y")," - ",DATE_FORMAT(i.todate, "%d/%m/%Y")))','DATE_FORMAT(i.createddate , "%d %b %Y %H:%i %p")','i.amount',"DATEDIFF(i.todate,now())"); //set column field database for datatable searchable 
	public $_order = array('i.todate' => 'ASC'); // default order

	function __construct() {
        parent::__construct();
	}

	function exportInsuranceReport(){

        $insurancecompany = (isset($_REQUEST['insurancecompany']))?$_REQUEST['insurancecompany']:'';
        $days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "i.todate!='0000-00-00' AND (DATE(i.todate) < curdate() + ".$days.")";

        $this->readdb->select("i.id,i.vehicleid,i.companyname,i.createddate,
        IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
        i.policyno,i.fromdate,i.todate,i.amount,DATEDIFF(todate,curdate()) as days");

        $this->readdb->from($this->_table." as i");
        $this->readdb->join(tbl_vehicle." as v", "v.id = i.vehicleid", "INNER");
        $this->readdb->where("(i.id=(SELECT id FROM ".tbl_insurance." WHERE vehicleid=i.vehicleid ORDER By id DESC LIMIT 1))");
        $this->readdb->where("(i.companyname='".$insurancecompany."' OR '".$insurancecompany."'='')");
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
        $this->readdb->where($where);
        $this->readdb->group_by("i.id");
        $this->readdb->order_by('i.todate', 'ASC');
		$query = $this->readdb->get();
		
		return $query->result();
    }

    function _get_datatables_query(){
        
        $insurancecompany = (isset($_REQUEST['insurancecompany']))?$_REQUEST['insurancecompany']:'';
        $days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "i.todate!='0000-00-00' AND (DATE(i.todate) < curdate() + ".$days.")";

        $this->readdb->select("i.id,i.vehicleid,i.companyname,
        IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
        i.policyno,i.fromdate,i.todate,i.amount,i.createddate,
        DATEDIFF(i.todate,curdate()) as days
        ");
        $this->readdb->from($this->_table." as i");
        $this->readdb->join(tbl_vehicle." as v", "v.id = i.vehicleid", "INNER");
        $this->readdb->where("(i.id=(SELECT id FROM ".tbl_insurance." WHERE vehicleid=i.vehicleid ORDER By id DESC LIMIT 1))");
        $this->readdb->where("(i.companyname='".$insurancecompany."' OR '".$insurancecompany."'='')");
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
        $this->readdb->where($where);
        $i = 0;

        foreach ($this->column_search as $item){ // loop column 
        
            if($_POST['search']['value']){ // if datatable send POST for search
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
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->_order)) {
            $order = $this->_order;
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

    function getInsurancecompanyDataByExpiredData(){
		$where = "i.todate!='0000-00-00' AND (DATE(i.todate) < curdate())";

		$query = $this->readdb->select("i.id,companyname")
							  ->from($this->_table." as i")
							  ->where($where)
							  ->group_by('i.id')
							  ->get();
		
		return $query->result_array();
	}
}