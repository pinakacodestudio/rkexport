<?php

class Document_model extends Common_model {

	//put your code here
	public $_table = tbl_documents;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'partyname','vehiclename','documenttype','d.documentnumber','d.fromdate','d.duedate','d.createddate'); //set column field database for datatable orderable
    public $column_search = array('dt.documenttype','(IFNULL(v.vehiclename,""))','(IFNULL(p.firstname,""))','(IFNULL(p.middlename,""))','(IFNULL(p.lastname,""))','(IFNULL(p.partycode,""))','d.documentnumber',"DATE_FORMAT(d.fromdate, '%d/%m/%Y')","DATE_FORMAT(d.duedate, '%d/%m/%Y')",'DATE_FORMAT(d.createddate , "%d %b %Y %H:%i %p")'); //set column field database for datatable searchable 
    public $order = array('d.id' => 'DESC'); // default order  
    
	function __construct() {
		parent::__construct();
	}

	function getdDocumentDataByID($ID){
    
        $query = $this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,d.documentnumber,d.fromdate,d.duedate,d.documentfile,d.licencetype")
							->from($this->_table." as d")
							->where("d.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	
	function getDocumentDataforExport(){
		$type = (isset($_REQUEST['type']))?$_REQUEST['type']:'';
        $documenttypeid = (isset($_REQUEST['documenttypeid']))?$_REQUEST['documenttypeid']:'0';
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:'0';
		$vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:'0';

		$this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,
				d.documentnumber,d.fromdate,d.duedate,d.documentfile,d.createddate,
				IFNULL(p.id,'') as partyid,d.licencetype,
				CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',partycode,')') as partyname,
				IFNULL(v.id,0) as vehicleid,
                IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
                dt.documenttype,
        ");

        $this->readdb->from($this->_table." as d");
        $this->readdb->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER");
		$this->readdb->join(tbl_vehicle." as v","v.id=d.referenceid AND d.referencetype=0","LEFT");
		$this->readdb->join(tbl_party." as p","(p.id=d.referenceid AND d.referencetype=1) OR (d.referencetype=0 AND p.id=v.ownerpartyid)","LEFT");
		$this->readdb->where("(d.referencetype='".$type."' OR '".$type."'='')");
		$this->readdb->where("(d.documenttypeid='".$documenttypeid."' OR '".$documenttypeid."'='0')");		
		$this->readdb->where("(p.id='".$partyid."' OR '".$partyid."'='0')");		
		$this->readdb->where("(v.id='".$vehicleid."' OR '".$vehicleid."'='0')");
		$this->readdb->order_by('d.id','DESC');
		$query=$this->readdb->get();

		return $query->result();
	}
    
	//LISTING DATA
	function _get_datatables_query(){
        
        $type = (isset($_REQUEST['type']))?$_REQUEST['type']:'';
        $documenttypeid = (isset($_REQUEST['documenttypeid']))?$_REQUEST['documenttypeid']:'0';
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:'0';
		$vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:'0';

        $this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,
				d.documentnumber,d.fromdate,d.duedate,d.documentfile,
				IFNULL(p.id,'') as partyid,
				CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',partycode,')') as partyname,
				IFNULL(v.id,0) as vehicleid,
                IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
                dt.documenttype,d.createddate
        ");

        $this->readdb->from($this->_table." as d");
        $this->readdb->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER");
		$this->readdb->join(tbl_vehicle." as v","v.id=d.referenceid AND d.referencetype=0","LEFT");
		$this->readdb->join(tbl_party." as p","(p.id=d.referenceid AND d.referencetype=1) OR (d.referencetype=0 AND p.id=v.ownerpartyid)","LEFT");
		$this->readdb->where("(d.referencetype='".$type."' OR '".$type."'='')");
		$this->readdb->where("(d.documenttypeid='".$documenttypeid."' OR '".$documenttypeid."'='0')");		
		$this->readdb->where("(p.id='".$partyid."' OR '".$partyid."'='0')");		
		$this->readdb->where("(v.id='".$vehicleid."' OR '".$vehicleid."'='0')");
		
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
?>