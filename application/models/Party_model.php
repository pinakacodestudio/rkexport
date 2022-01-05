<?php

class Party_model extends Common_model {

	//put your code here
	public $_table = tbl_party;
	public $_fields = "*";
	public $_where = array();
    public $_except_fields = array();
    public $column_order = array(null,'p.websitename','pt.partytype','p.contactno1','c.name','p.createddate'); //set column field database for datatable orderable
    public $column_search = array('p.websitename','p.partycode','p.contactno1','pt.partytype','(IFNULL(c.name,""))','DATE_FORMAT(p.createddate , "%d %b %Y %H:%i %p")'); //set column field database for datatable searchable 
	public $order = array('p.id' => 'DESC'); // default order  
	
    public $col_order_doc = array(null,'dt.documenttype','d.documentnumber','d.fromdate','d.duedate');
    public $col_search_doc = array('dt.documenttype','d.documentnumber','DATE_FORMAT(d.fromdate , "%d/%m/%Y")','DATE_FORMAT(d.duedate , "%d/%m/%Y")');
	public $order_doc = array('d.id' => 'DESC');

	public $col_order_site = array('s.sitename',null,'cityname','provincename','s.createddate');
    public $col_search_site = array('s.sitename','(IFNULL(ct.name,""))','(IFNULL(pr.name,""))','s.createddate','s.address');
	public $order_site = array('s.id' => 'DESC');

	public $col_order_vehicle = array('v.vehiclename','v.vehicleno','v.createddate');
    public $col_search_vehicle = array('v.createddate','v.vehiclename','v.vehicleno');
	public $order_vehicle = array('v.id' => 'DESC');

	function __construct() {
		parent::__construct();
	}
    
    function CheckDuplicateValueAvailableInParty($fieldsArray, $valueArray, $ID = '') {
        
        $this->readdb->select($this->_fields);
        $this->readdb->from($this->_table);
		if (isset($ID) && $ID != '') {
            $this->readdb->where('id <>',$ID);
		}
        if(!empty($fieldsArray)){
            $this->readdb->group_start();
            foreach($fieldsArray as $key=>$field){
                $value = $valueArray[$key];
                
                if($key==0){
                    $this->readdb->group_start();
                    $this->readdb->where($field."='".$value."' AND ".$field."!=''");
                    $this->readdb->group_end();
                }else{
                    $this->readdb->or_group_start();
                    $this->readdb->where($field."='".$value."' AND ".$field."!=''");
                    $this->readdb->group_end();
                }
            }
            $this->readdb->group_end();
        }
        $query = $this->readdb->get();
        
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	
	function getPartyDataByID($ID){
        
        $query = $this->readdb->select("p.id,p.websitename,p.partycode,p.partytypeid,p.cityid,p.provinceid,p.gst,p.pan,p.partycode,p.countryid,p.billingaddress,p.shippingaddress,courieraddress,p.openingdate,p.openingamount,p.companyid")
		->from($this->_table." as p")
		->where("p.id", $ID)
		->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function getPartyDetailById($partyid){
        
        $query = $this->readdb->select("p.id,p.websitename,p.partycode,pt.partytype,p.partytypeid,ur.role,p.address,p.cityid,p.provinceid,p.empolyeeroleid,
			IFNULL((SELECT countryid FROM ".tbl_province." WHERE id=p.provinceid),0) as countryid,t8.email,t8.birthdate,t8.anniversarydate,
			IFNULL(ct.name,'') as cityname,IFNULL(pr.name,'') as provincename,IFNULL(cn.name,'') as countryname,
        ")
		->from($this->_table." as p")
		->join(tbl_partytype." as pt","pt.id=p.partytypeid","INNER")
		->join(tbl_userrole." as ur","ur.id=p.empolyeeroleid","LEFT")
		->join(tbl_city." as ct","ct.id=p.cityid","LEFT")
		->join(tbl_province." as pr","pr.id=p.provinceid","LEFT")
		->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
		->join(tbl_partydoc." as t7","p.id=t7.partyid","LEFT")
		->join(tbl_partycontact." as t8","p.id=t8.partyid","LEFT")
		->where("p.id", $partyid)
		->get();
		
		$json=array();
		if ($query->num_rows() == 1) {
			$json = $query->row_array();
			$json['partydocuments'] = $this->getPartyDocumentsByPartyID($partyid);

			return $json;
		}else {
			return 0;
		}	
	}
    function getPartyDocumentsByPartyID($partyid){
        
        $query = $this->readdb->select("d.id,d.documenttypeid,d.documentnumber,d.fromdate,d.duedate,d.licencetype,d.documentfile")
							->from(tbl_document." as d")
                            ->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER")
                            ->where("d.referencetype=1 AND d.referenceid=".$partyid)
                            ->get();
                            
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
    }

	function getActiveParty($type=''){
		$patytypedata = array();
		if($type!=''){
			$query = $this->readdb->select("id")
							->from(tbl_partytype)
							->where("LOWER(partytype)='".$type."'")
							->get();
		
		 	$patytypedata = $query->row_array();
		}
		if(!empty($patytypedata)){
		 	$where = "FIND_IN_SET(".$patytypedata['id'].",partytypeid)>0";
		}else{
			$where = "1=1";
		}

		$query = $this->readdb->select("id,websitename,partycode")
							->from($this->_table)
							->where($where)
							->order_by("websitename","ASC")
							->get();
		
		return $query->result_array();
    }
    
    function getCityListOnParty(){
        
        $query = $this->readdb->select("c.id,c.name,c.stateid")
							->from($this->_table." as p")
							->join(tbl_city." as c","p.cityid=c.id","INNER")
							->group_by('c.id')
                            ->get();
                            
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
    }
    //LISTING DATA
	function _get_datatables_query(){
        
        $partytypeid = (isset($_REQUEST['partytypeid']))?$_REQUEST['partytypeid']:0;
        $cityid = (isset($_REQUEST['cityid']))?$_REQUEST['cityid']:0;
        
        $this->readdb->select('p.id,p.websitename,p.partycode,pt.partytype as partytypename,p.cityid,p.provinceid,p.createddate,p.companyid,t4.companyname,(select contactno from '.tbl_partycontact.' where id=p.id LIMIT 1) as contactdetails,c.name as cityname');

        $this->readdb->from($this->_table." as p");
        $this->readdb->join(tbl_partytype." as pt","p.partytypeid=pt.id","LEFT");
        $this->readdb->join(tbl_city." as c","p.cityid=c.id","LEFT");
        $this->readdb->join(tbl_company." as t4","p.companyid=t4.id","LEFT");
        $this->readdb->where("(p.partytypeid=".$partytypeid." OR ".$partytypeid."=0)");
        $this->readdb->where("(p.cityid=".$cityid." OR ".$cityid."=0)");
        
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

	//DOCUMENT LISTING DATA
	function _get_datatables_query_document(){
        
        $partyid = (isset($_REQUEST['partyid']))?$_REQUEST['partyid']:0;
        
		$this->readdb->select("d.id,d.referencetype,d.	,d.documenttypeid,
				d.documentnumber,d.fromdate,d.duedate,d.documentfile,dt.documenttype,dt.documenttype
		");

		$this->readdb->from(tbl_document." as d");
        $this->readdb->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER");
        $this->readdb->where("d.referencetype=1 AND d.referenceid=".$partyid);
        
		$i = 0;
        foreach ($this->col_search_doc as $item) // loop column 
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

				if(count($this->col_search_doc) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->col_order_doc[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order_doc)){
			$order = $this->order_doc;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	//ASSIGNED SITE LISTING DATA
	function _get_datatables_query_assignedsite(){
        
		$partyid = $_REQUEST['partyid'];
		$cityid = $_REQUEST['cityid'];
		$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
		$todate = $this->general_model->convertdate($_REQUEST['todate']);
        
		$this->readdb->select("s.createddate,sitename,s.address,IFNULL(ct.name,'') as cityname,IFNULL(pr.name,'') as provincename");
		$this->readdb->from(tbl_site." as s");
		$this->readdb->join(tbl_sitemapping." as sm","sm.siteid=s.id AND sm.partyid=".$partyid,"INNER");
		$this->readdb->join(tbl_city." as ct","ct.id=s.cityid","LEFT");
		$this->readdb->join(tbl_province." as pr","pr.id=s.provinceid","LEFT");
		$this->readdb->where("(s.cityid=".$cityid." OR ".$cityid."=0) AND (date(s.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
        $this->readdb->group_by("sm.siteid");
        
		$i = 0;
        foreach ($this->col_search_site as $item) // loop column 
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

				if(count($this->col_search_site) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->col_order_site[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order_site)){
			$order = $this->order_site;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	//ASSIGNED VEHICLE LISTING DATA
	function _get_datatables_query_assignedvehicle(){
        
		$partyid = $_REQUEST['partyid'];
		$fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
		$todate = $this->general_model->convertdate($_REQUEST['todate']);
        
		$this->readdb->select("v.createddate,av.createddate");
		$this->readdb->from(tbl_assignvehicle." as av");
		$this->readdb->join(tbl_vehicle." as v","v.id=av.vehicleid","INNER");
		$this->readdb->join(tbl_site." as s","s.id=av.siteid","INNER");
        $this->readdb->join(tbl_sitemapping." as sm","sm.siteid=s.id","INNER");
		$this->readdb->where("sm.partyid=".$partyid." AND (date(av.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
        
		$i = 0;
        foreach ($this->col_search_vehicle as $item) // loop column 
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

				if(count($this->col_search_vehicle) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->col_order_vehicle[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order_vehicle)){
			$order = $this->order_vehicle;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}


	function get_datatables($listtype="") {
		if($listtype=="documents"){
			
			$this->_get_datatables_query_document();
		}else if($listtype=="assignedsite"){
			$this->_get_datatables_query_assignedsite();
		}else if($listtype=="assignedvehicle"){
			$this->_get_datatables_query_assignedvehicle();
		}else{
			$this->_get_datatables_query();
		}
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		return $query->result();
	}

	function count_filtered($listtype="") {
		if($listtype=="documents"){
			$this->_get_datatables_query_document();
		}else if($listtype=="assignedsite"){
			$this->_get_datatables_query_assignedsite();
		}else if($listtype=="assignedvehicle"){
			$this->_get_datatables_query_assignedvehicle();
		}else{
			$this->_get_datatables_query();
		}
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all($listtype="") {
		if($listtype=="documents"){
			$this->_get_datatables_query_document();
		}else if($listtype=="assignedsite"){
			$this->_get_datatables_query_assignedsite();
		}else if($listtype=="assignedvehicle"){
			$this->_get_datatables_query_assignedvehicle();
		}else{
			$this->_get_datatables_query();
		}
		return $this->readdb->count_all_results();
	}
	
	function getPartyDataforExport(){
        
        $cityid = (isset($_REQUEST['cityid']))?$_REQUEST['cityid']:0;
        $partytypeid = (isset($_REQUEST['partytypeid']))?$_REQUEST['partytypeid']:0;
        
        $this->readdb->select("p.id,p.websitename,
                pt.partytype,p.email,ur.role,p.birthdate,p.anniversarydate,p.address,p.cityid,p.provinceid,pr.countryid,p.createddate,IFNULL(c.name,'') as cityname,IFNULL(pr.name,'') as provincename,IFNULL(cn.name,'') as countryname,
        ");

        $this->readdb->from($this->_table." as p");
        $this->readdb->join(tbl_partytype." as pt","pt.id=p.partytypeid","INNER");
		$this->readdb->join(tbl_city." as c","c.id=p.cityid","LEFT");
		$this->readdb->join(tbl_userrole." as ur","ur.id=p.empolyeeroleid","LEFT");
		$this->readdb->join(tbl_province." as pr","pr.id=p.provinceid","LEFT");
		$this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","LEFT");
        $this->readdb->where("(p.partytypeid=".$partytypeid." OR ".$partytypeid."=0)");
		$this->readdb->where("(p.cityid=".$cityid." OR ".$cityid."=0)");
		$this->readdb->order_by('p.id','DESC');
		$query=$this->readdb->get();
		
		return $query->result();
	}
}
?>