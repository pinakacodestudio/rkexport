<?php
class Expire_vehicle_registration_report_model extends Common_model {

	//put your code here
	public $_table = tbl_vehicle;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array('v.vehiclename','v.vehicleno','vehicletypename','ownername','ownercontactno','v.duedateofregistration','v.createddate','days'); //set column field database for datatable orderable
	public $column_search = array("CONCAT(v.vehiclename,' (',vc.companyname,')')","v.vehicleno","CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')')","owner.contactno1",'DATE_FORMAT(v.duedateofregistration, "%d/%m/%Y")',"CASE 
    WHEN v.vehicletype=1 THEN 'Two Wheel' 
    WHEN v.vehicletype=2 THEN 'Four Wheel'
    ELSE 'Heavy Vehicles' 
END","DATEDIFF(v.duedateofregistration,now())",'DATE_FORMAT(v.createddate , "%d %b %Y %H:%i %p")'); //set column field database for datatable searchable 
	public $_order = array('v.duedateofregistration' => 'ASC'); // default order

	function __construct() {
        parent::__construct();
	}
	
	function exportVehicleRegistrationReport(){
        $where = array("v.duedateofregistration!='0000-00-00' AND (DATE(v.duedateofregistration) < curdate())");
        $companyid = (isset($_REQUEST['companyid']))?$_REQUEST['companyid']:0;
        $ownerpartyid = (isset($_REQUEST['ownerpartyid']))?$_REQUEST['ownerpartyid']:0;
        $vehicletype = (isset($_REQUEST['vehicletype']))?$_REQUEST['vehicletype']:0;
        $commercial = (isset($_REQUEST['commercial']))?$_REQUEST['commercial']:'';
        $sold = (isset($_REQUEST['sold']))?$_REQUEST['sold']:'';
        $days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "v.duedateofregistration!='0000-00-00' AND (DATE(v.duedateofregistration) < curdate() + ".$days.")";

        $this->readdb->select("v.id,v.vehicleno,v.vehicletype,v.status,v.duedateofregistration,v.createddate,
		vc.companyname,CONCAT(v.vehiclename,' (',vc.companyname,')')as vehiclename,DATEDIFF(duedateofregistration,curdate()) as days,
		owner.id as ownerid,
		CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')') as ownername,
		owner.contactno1 as ownercontactno,
		
		IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=v.id)),'') as site,

		CASE 
			WHEN v.vehicletype=1 THEN 'Two Wheel' 
			WHEN v.vehicletype=2 THEN 'Four Wheel'
			ELSE 'Heavy Vehicles' 
        END AS vehicletypename");
		$this->readdb->from($this->_table." as v");
		$this->readdb->join(tbl_vehiclecompany." as vc","vc.id=v.vehiclecompanyid","INNER");
        $this->readdb->join(tbl_party."company as owner","owner.id=v.ownerpartyid","INNER");
        $this->readdb->where("(v.vehiclecompanyid=".$companyid." OR ".$companyid."=0)");
        $this->readdb->where("(v.ownerpartyid=".$ownerpartyid." OR ".$ownerpartyid."=0)");
        $this->readdb->where("(v.vehicletype=".$vehicletype." OR ".$vehicletype."=0)");
        $this->readdb->where("(v.commercial='".$commercial."' OR '".$commercial."'='')");
        $this->readdb->where("(v.sold='".$sold."' OR '".$sold."'='')");
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
        $this->readdb->where($where);
        $this->readdb->group_by("v.id"); 
		$query = $this->readdb->get();
		
		return $query->result();
    }

    function _get_datatables_query($type=1){

        $companyid = (isset($_REQUEST['companyid']))?$_REQUEST['companyid']:0;
        $ownerpartyid = (isset($_REQUEST['ownerpartyid']))?$_REQUEST['ownerpartyid']:0;
        $vehicletype = (isset($_REQUEST['vehicletype']))?$_REQUEST['vehicletype']:0;
        $commercial = (isset($_REQUEST['commercial']))?$_REQUEST['commercial']:'';
        $sold = (isset($_REQUEST['sold']))?$_REQUEST['sold']:'';
        $days = (isset($_REQUEST['days']))?$_REQUEST['days']:'30';
		$where = "v.duedateofregistration!='0000-00-00' AND (DATE(v.duedateofregistration) < curdate() + ".$days.")";
        if($type == 0){
            $this->readdb->select("v.id");
        }else{
        $this->readdb->select("v.id,v.vehicleno,v.vehicletype,v.status,v.duedateofregistration,
		vc.companyname,CONCAT(v.vehiclename,' (',vc.companyname,')')as vehiclename,DATEDIFF(duedateofregistration,curdate()) as days,
		owner.id as ownerid,
		CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')') as ownername,
		owner.contactno1 as ownercontactno,v.createddate,
		
		IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=v.id)),'') as site,

		CASE 
			WHEN v.vehicletype=1 THEN 'Two Wheel' 
			WHEN v.vehicletype=2 THEN 'Four Wheel'
			ELSE 'Heavy Vehicles' 
        END AS vehicletypename");
        }
		$this->readdb->from($this->_table." as v");
		$this->readdb->join(tbl_vehiclecompany." as vc","vc.id=v.vehiclecompanyid","INNER");
        $this->readdb->join(tbl_party." as owner","owner.id=v.ownerpartyid","INNER");
        $this->readdb->where("(v.vehiclecompanyid=".$companyid." OR ".$companyid."=0)");
        $this->readdb->where("(v.ownerpartyid=".$ownerpartyid." OR ".$ownerpartyid."=0)");
        $this->readdb->where("(v.vehicletype=".$vehicletype." OR ".$vehicletype."=0)");
        $this->readdb->where("(v.commercial='".$commercial."' OR '".$commercial."'='')");
        $this->readdb->where("(v.sold='".$sold."' OR '".$sold."'='')");
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
    
    function getPartyByExpiredData($type=''){
        $datecondition = "v.duedateofregistration!='0000-00-00' AND (DATE(v.duedateofregistration) < curdate())";
		$patytypedata = array();
		if($type!=''){
			$query = $this->readdb->select("id")
							->from(tbl_partytype)
							->where("LOWER(partytype)='".$type."'")
							->get();
		
		 	$patytypedata = $query->row_array();
		}
		if(!empty($patytypedata)){
		 	$where = "partytypeid='".$patytypedata['id']."'";
		}else{
			$where = "1=1";
		}

		$query = $this->readdb->select("owner.id,CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')') as name")
                            ->from($this->_table." as v")
                            ->join(tbl_party." as owner","owner.id=v.ownerpartyid","INNER")
                            ->where($where)
                            ->where($datecondition)
							->group_by("owner.id")
							->get();
		
		return $query->result_array();
    }

    function getVehicleCompanyByExpiredData(){
        $where = "v.duedateofregistration!='0000-00-00' AND (DATE(v.duedateofregistration) < curdate())";
		$query = $this->readdb->select("vc.id,vc.companyname")
                              ->from($this->_table . " as v")
                              ->join(tbl_vehiclecompany." as vc","vc.id=v.vehiclecompanyid","INNER")
							  ->where($where)
							  ->group_by("vc.id")
		  					  ->get();

		return $query->result_array();
	}
}