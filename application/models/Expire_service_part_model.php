<?php
class Expire_service_part_model extends Common_model {

	//put your code here
	public $_table = tbl_servicepartdetails;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array("v.vehiclename","spd.partname","p.firstname","spd.serialnumber","spd.warrantyenddate","spd.duedate","s.amount",'s.createddate','days'); //set column field database for datatable orderable
	public $column_search = array("CONCAT(v.vehiclename,' (',v.vehicleno,')')","partname","CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')')",'DATE_FORMAT(spd.warrantyenddate, "%d/%m/%Y")','DATE_FORMAT(spd.duedate, "%d/%m/%Y")',"s.amount",'DATE_FORMAT(s.createddate , "%d %b %Y %H:%i %p")',"DATEDIFF(spd.duedate,now())"); //set column field database for datatable searchable 
	public $order = array('spd.duedate' => 'ASC'); // default order

	function __construct() {
        parent::__construct();
	}
	
	function exportServicePartReport(){

        $vehicle = (isset($_REQUEST['vehicle']))?$_REQUEST['vehicle']:'';
        $garageid = (isset($_REQUEST['garageid']))?$_REQUEST['garageid']:'';
        $days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "spd.duedate!='0000-00-00' AND (DATE(spd.duedate) < curdate() + ".$days.")";
		
        $this->readdb->select("spd.id,spd.serviceid,s.garageid,s.date,s.vehicleid,spd.partname,DATEDIFF(spd.duedate,CURDATE()) as days,
		IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,s.createddate,
		spd.serialnumber,spd.warrantyenddate,spd.duedate,s.amount,CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as partyname");

		$this->readdb->from($this->_table. " as spd");
		$this->readdb->join(tbl_service." as s","s.id=serviceid","LEFT");
		$this->readdb->join(tbl_vehicle." as v","v.id=vehicleid","LEFT");
        $this->readdb->join(tbl_party." as p","p.id=s.garageid","LEFT");
        $this->readdb->where("(v.id='".$vehicle."' OR '".$vehicle."'='0')");
		$this->readdb->where("(p.id='".$garageid."' OR '".$garageid."'='0')");			
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
		$this->readdb->where($where);
        // $this->readdb->where("spd.id=(SELECT id from servicepartdetails where partname=spd.partname AND (SELECT vehicleid FROM service where id=serviceid)=(SELECT vehicleid FROM service where id=spd.serviceid) ORDER By id DESC LIMIT 1)");
        $this->readdb->order_by('spd.duedate', 'ASC');
        
        $this->readdb->group_by("spd.id");
		$query = $this->readdb->get();
		
		return $query->result();
    }

    function _get_datatables_query(){
        
        $vehicle = (isset($_REQUEST['vehicle']))?$_REQUEST['vehicle']:'';
        $garageid = (isset($_REQUEST['garageid']))?$_REQUEST['garageid']:'';
        $days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "spd.duedate!='0000-00-00' AND (DATE(spd.duedate) < curdate() + ".$days.")";


		$this->readdb->select("spd.id,spd.serviceid,s.garageid,s.date,s.vehicleid,spd.partname,DATEDIFF(spd.duedate,CURDATE()) as days,
		IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,s.createddate,
		spd.serialnumber,spd.warrantyenddate,spd.duedate,s.amount,CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as partyname");
		$this->readdb->from($this->_table. " as spd");
		$this->readdb->join(tbl_service." as s","s.id=serviceid","LEFT");
		$this->readdb->join(tbl_vehicle." as v","v.id=vehicleid","INNER");
        $this->readdb->join(tbl_party." as p","p.id=s.garageid","LEFT");
		
        $this->readdb->where("(v.id='".$vehicle."' OR '".$vehicle."'='0')");
		$this->readdb->where("(p.id='".$garageid."' OR '".$garageid."'='0')");			
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
		$this->readdb->where($where);

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
	
	function getVehicleDataByExpiredData(){
		$where = "spd.duedate!='0000-00-00' AND (DATE(spd.duedate) < curdate())";

		$query = $this->readdb->select("v.id,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename")
							  ->from($this->_table." as spd")
							  ->join(tbl_service." as s","s.id=spd.serviceid","LEFT")
							  ->join(tbl_vehicle." as v","v.id=s.vehicleid","INNER")
							  ->where($where)
							  ->group_by('v.vehiclename')
							  ->get();
		
		return $query->result_array();
	}
	
	function getGaregeDataByExpiredData(){
		$where = "spd.duedate!='0000-00-00' AND (DATE(spd.duedate) < curdate())";

		$query = $this->readdb->select("p.id,CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as name")
							  ->from($this->_table." as spd")
							  ->join(tbl_service." as s","s.id=spd.serviceid","LEFT")
							  ->join(tbl_party." as p","p.id=s.garageid","LEFT")
							  ->where($where)
							  ->group_by('p.id')
							  ->get();
		
		return $query->result_array();
	}
}