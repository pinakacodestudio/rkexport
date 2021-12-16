<?php
class Expire_document_report_model extends Common_model {

	public $_table = tbl_document;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'vehiclename','partyname','documenttype','d.documentnumber','d.fromdate','d.duedate','d.createddate','days'); //set column field database for datatable orderable
	public $column_search = array("dt.documenttype","CONCAT(p.firstname, ' ', `p`.`middlename`, ' ', p.lastname, ' (', p.partycode, ')')","CONCAT(v.vehiclename,' (',v.vehicleno,')')","d.documentnumber",'DATE_FORMAT(d.fromdate, "%d/%m/%Y")','DATE_FORMAT(d.duedate, "%d/%m/%Y")','DATE_FORMAT(d.createddate , "%d %b %Y %H:%i %p")',"DATEDIFF(d.duedate,now())"); //set column field database for datatable searchable 
	public $order = array('d.duedate' => 'ASC'); // default order
 
	function __construct() {
        parent::__construct();
	}

	function exportdocumentreport(){
		
		$type = (isset($_REQUEST['type']))?$_REQUEST['type']:'';
        $documenttypeid = (isset($_REQUEST['documenttypeid']))?$_REQUEST['documenttypeid']:'0';
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:'0';
		$vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:'0';
		$days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "d.duedate!='0000-00-00' AND (DATE(d.duedate) < curdate() + ".$days.")";
		
        $this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,
				d.documentnumber,d.fromdate,d.duedate,d.documentfile,d.createddate,
				IFNULL(p.id,'') as partyid,IFNULL(v.id,'') as vehicleid,
                CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as partyname,
                IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
                dt.documenttype,DATEDIFF(d.duedate,now()) as days
        ");

        $this->readdb->from($this->_table." as d");
        $this->readdb->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER");
		$this->readdb->join(tbl_vehicle." as v","v.id=d.referenceid AND d.referencetype=0","LEFT");
		$this->readdb->join(tbl_party." as p","(p.id=d.referenceid AND d.referencetype=1) OR (d.referencetype=0 AND p.id=v.ownerpartyid)","LEFT");
		$this->readdb->where("(d.id=IF(d.referencetype=0,(SELECT id FROM ".tbl_document." WHERE referenceid=d.referenceid AND documenttypeid=d.documenttypeid ORDER By id DESC LIMIT 1), (SELECT id FROM ".tbl_document." WHERE referenceid=d.referenceid  AND documenttypeid=d.documenttypeid ORDER By id DESC LIMIT 1)))");
		$this->readdb->where("(d.referencetype='".$type."' OR '".$type."'='')");
		$this->readdb->where("(d.documenttypeid='".$documenttypeid."' OR '".$documenttypeid."'='0')");		
		$this->readdb->where("(p.id='".$partyid."' OR '".$partyid."'='0')");		
		$this->readdb->where("(v.id='".$vehicleid."' OR '".$vehicleid."'='0')");
        $this->readdb->where("IF(v.id=d.referenceid AND d.referencetype=0,(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3))),1=1)");
		$this->readdb->where($where);
        $this->readdb->order_by('d.duedate', 'ASC');
		$query = $this->readdb->get();
		return $query->result();
	}
	
    function _get_datatables_query(){
        
        $type = (isset($_REQUEST['type']))?$_REQUEST['type']:'';
        $documenttypeid = (isset($_REQUEST['documenttypeid']))?$_REQUEST['documenttypeid']:'0';
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:'0';
		$vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:'0';
        $days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "d.duedate!='0000-00-00' AND (DATE(d.duedate) < curdate()+".$days.")";

        $this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,
				d.documentnumber,d.fromdate,d.duedate,d.documentfile,d.createddate,
				IFNULL(p.id,'') as partyid,IFNULL(v.id,'') as vehicleid,
                CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as partyname,
                IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
				dt.documenttype,DATEDIFF(d.duedate,CURDATE()) as days
				
        "); 

        $this->readdb->from($this->_table." as d");
        $this->readdb->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER");
		$this->readdb->join(tbl_vehicle." as v","v.id=d.referenceid AND d.referencetype=0","LEFT");
		$this->readdb->join(tbl_party." as p","(p.id=d.referenceid AND d.referencetype=1) OR (d.referencetype=0 AND p.id=v.ownerpartyid)","LEFT");
		$this->readdb->where("(d.id=IF(d.referencetype=0,(SELECT id FROM ".tbl_document." WHERE referenceid=d.referenceid AND documenttypeid=d.documenttypeid ORDER By id DESC LIMIT 1), (SELECT id FROM ".tbl_document." WHERE referenceid=d.referenceid  AND documenttypeid=d.documenttypeid ORDER By id DESC LIMIT 1)))");
		$this->readdb->where("(d.referencetype='".$type."' OR '".$type."'='')");
		$this->readdb->where("(d.documenttypeid='".$documenttypeid."' OR '".$documenttypeid."'='0')");		
		$this->readdb->where("(p.id='".$partyid."' OR '".$partyid."'='0')");		
		$this->readdb->where("(v.id='".$vehicleid."' OR '".$vehicleid."'='0')");
        $this->readdb->where("IF(v.id=d.referenceid AND d.referencetype=0,(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3))),1=1)");
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
		// echo $this->readdb->last_query();exit;
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
	
	function getDocumentTypeByExpiredData(){
		$where = "d.duedate!='0000-00-00' AND (DATE(d.duedate) < curdate())";

		$query = $this->readdb->select("dt.id,dt.documenttype")
							  ->from($this->_table." as d")
							  ->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","LEFT")
							  ->where($where)
							  ->group_by('dt.id')
							  ->get();
		
		return $query->result_array();
	}

	function getPartyByExpiredData(){
		$where = "d.duedate!='0000-00-00' AND (DATE(d.duedate) < curdate())";

		$query = $this->readdb->select("p.id,CONCAT(firstname,' ',middlename,' ',lastname,' (',partycode,')') as name")
							  ->from($this->_table." as d")
							  ->join(tbl_vehicle." as v","v.id=d.referenceid AND d.referencetype=0","LEFT")
							  ->join(tbl_party." as p","(p.id=d.referenceid AND d.referencetype=1) OR (d.referencetype=0 AND p.id=v.ownerpartyid)","LEFT")
							  ->where($where)
							  ->order_by("firstname","ASC")
							  ->group_by('p.id')
							  ->get();
		
		return $query->result_array();
	}

	function getVehicleDataByExpiredData(){
		$where = "d.duedate!='0000-00-00' AND (DATE(d.duedate) < curdate())";

		$query = $this->readdb->select("v.id,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename")
							  ->from($this->_table." as d")
							  ->join(tbl_vehicle." as v","v.id=d.referenceid AND d.referencetype=0","INNER")
							  ->where($where)
							  ->group_by('v.id')
							  ->get();
		
		return $query->result_array();
	}
}