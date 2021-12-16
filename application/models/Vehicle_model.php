<?php

class Vehicle_model extends Common_model {

	//put your code here
	public $_table = tbl_vehicle;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
    public $_order = array('v.id' => 'DESC');
	public $column_order = array(null,'v.vehiclename', 'vehicletypename','ownername','ownercontactno','v.createddate');
        
    public $column_search = array("v.vehiclename","vc.companyname","v.vehicleno","(CASE WHEN v.vehicletype=1 THEN 'Two Wheel' 
    WHEN v.vehicletype=2 THEN 'Four Wheel' ELSE 'Heavy Vehicles' END)","(CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname))",'owner.contactno1',"IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=v.id)),'')",'DATE_FORMAT(v.createddate , "%d %b %Y %H:%i %p")');
    
	function __construct() {
		parent::__construct();
	}

    function updateVehicleRegDateAndRCBookDateByAppliedOnFitnessDocument($vehicleid,$documentid){

        $this->load->model("Document_model","Document");
        $this->load->model("Document_type_model","Document_type");
        
        $FitnessDocument = $this->Document_type->getDocumentTypeByName('Fitness');
        $RCBookDocument = $this->Document_type->getDocumentTypeByName('RC Book');
        
        if(!empty($FitnessDocument)){
            $FitnessDocumentTypeId = !empty($FitnessDocument)?$FitnessDocument['id']:0;
            $RCBookDocumentTypeId = !empty($RCBookDocument)?$RCBookDocument['id']:0;
            
            $FitnessData = $this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,d.fromdate,d.duedate")
                                ->from(tbl_document." as d")
                                ->where("d.referencetype=0 AND d.referenceid='".$vehicleid."' AND d.id='".$documentid."' AND d.documenttypeid='".$FitnessDocumentTypeId."' AND (d.fromdate!='0000-00-00' OR d.duedate!='0000-00-00')")
                                ->limit(1)
                                ->get()->row_array();
    
            if(!empty($FitnessData)){
    
                $modifieddate = $this->general_model->getCurrentDateTime();
                $modifiedby = $this->session->userdata(base_url().'ADMINID');
                $regDate = $FitnessData['fromdate'];
                $dueDate = $FitnessData['duedate'];
                
                $updateVehicleData = $updateRCBookData = array();
                if($regDate!=""){
                    $updateVehicleData['dateofregistration'] = $regDate;
                    $updateRCBookData['fromdate'] = $regDate;
                }
                if($dueDate!=""){
                    $updateVehicleData['duedateofregistration'] = $dueDate;
                    $updateRCBookData['duedate'] = $dueDate;
                }
                $updateVehicleData['modifieddate'] = $modifieddate;
                $updateVehicleData['modifiedby'] = $modifiedby;
        
                $updateRCBookData['modifieddate'] = $modifieddate;
                $updateRCBookData['modifiedby'] = $modifiedby;
                
                $this->_table = tbl_vehicle; 
                $this->_where = array("id"=>$vehicleid);
                $this->Edit($updateVehicleData);
    
                $RCBookData = $this->readdb->select("GROUP_CONCAT(d.id) as documentids")
                                ->from(tbl_document." as d")
                                ->where("d.referencetype=0 AND d.referenceid='".$vehicleid."' AND d.documenttypeid='".$RCBookDocumentTypeId."'")
                                ->get()->row_array();
                               
                if(!empty($RCBookData)){
                    $this->Document->_where = array("id IN (".$RCBookData['documentids'].")"=>null);
                    $this->Document->Edit($updateRCBookData);
                }
            }
        }

    }
    function getVehicleDataByID($ID){
    $query = $this->readdb->select("v.id,v.vehiclecompanyid,v.ownerpartyid,v.vehiclename,v.vehicleno,
                                    v.engineno,v.chassisno,v.dateofregistration,v.duedateofregistration,
                                    v.commercial,v.fueltype,v.buyerid,v.startingkm,v.petrocardno,
                                    v.fuelratetype,v.fuelrate,v.sold,v.solddate,v.soldpartyid,v.remarks,
                                    v.status,v.vehicletype,v.installmentamount")
							->from($this->_table." as v")
							->where("v.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
    }
    function getVehicleDetailById($vehicleid){
        
        $query = $this->readdb->select("v.id,vc.companyname,v.vehiclename,v.vehicleno,v.engineno,v.chassisno,v.vehicletype,
                v.dateofregistration,v.duedateofregistration,v.commercial,v.fueltype,v.buyerid,v.startingkm,v.petrocardno,v.fuelratetype,v.fuelrate,
                v.sold,v.solddate,v.soldpartyid,v.remarks,v.status,v.createddate,v.vehiclecompanyid,

                owner.id as ownerid,
                CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')') as ownername,
                IFNULL(CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')'),'') as soldpartyname,
                IFNULL(CONCAT(buyer.firstname,' ',buyer.middlename,' ',buyer.lastname,' (',buyer.partycode,')'),'') as buyername,

                vft.accountno,vft.walletid,vft.rfidno

            ")
                    ->from($this->_table." as v")
                    ->join(tbl_party." as owner","owner.id=v.ownerpartyid","INNER")
                    ->join(tbl_vehiclecompany." as vc","vc.id=v.vehiclecompanyid","LEFT")
                    ->join(tbl_party." as p","p.id=v.soldpartyid","LEFT")
                    ->join(tbl_party." as buyer","buyer.id=v.buyerid","LEFT")
                    ->join(tbl_vehiclefasttag." as vft","vft.vehicleid=v.id","LEFT")
                    ->where("v.id", $vehicleid)
                    ->get();
		
		$json=array();
		if ($query->num_rows() == 1) {
			$json = $query->row_array();
			$json['vehicledocuments'] = $this->getVehicleDocumentsByVehicleID($vehicleid);

			return $json;
		}else {
			return 0;
		}	
    }
    function getVehicleDocumentsByVehicleID($vehicleid){
        
        $query = $this->readdb->select("d.id,d.documenttypeid,d.documentnumber,d.fromdate,d.duedate,d.licencetype,d.documentfile")
							->from(tbl_document." as d")
                            ->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER")
                            ->where("d.referencetype=0 AND d.referenceid=".$vehicleid)
                            ->get();
                            
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
    }

    function getVehicleInsuranceByVehicleID($vehicleid){

        $query = $this->readdb->select("vi.id,vi.vehicleid,vi.companyname,vi.policyno,vi.fromdate,vi.todate,vi.paymentdate,vi.proof,vi.amount,
        vi.insuranceagentid
        ")
							->from(tbl_insurance." as vi")
                            ->where("vi.vehicleid=".$vehicleid)
                            ->get();

        if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
    }

    function getVehicleChallanByVehicleID($vehicleid){

        $query = $this->readdb->select("c.id,c.partyid,c.vehicleid,c.challantypeid,c.date,c.amount,c.attachment,c.remarks")
							->from(tbl_challan." as c")
                            ->where("c.vehicleid=".$vehicleid)
                            ->get();

        if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
    }

    function getVehicleFastTagByVehicleID($vehicleid){

        $query = $this->readdb->select("vft.id,vft.accountno,vft.walletid,vft.rfidno")
                            ->from(tbl_vehiclefasttag." as vft")
                            ->where("vft.vehicleid=".$vehicleid)
                            ->get();
        
        if ($query->num_rows() == 1) {
            return $query->row_array();
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

	function _get_datatables_query($type=1){
        
        $companyid = (isset($_REQUEST['companyid']))?$_REQUEST['companyid']:0;
        $ownerpartyid = (isset($_REQUEST['ownerpartyid']))?$_REQUEST['ownerpartyid']:0;
        $vehicletype = (isset($_REQUEST['vehicletype']))?$_REQUEST['vehicletype']:0;
        $commercial = (isset($_REQUEST['commercial']))?$_REQUEST['commercial']:'';
        $sold = (isset($_REQUEST['sold']))?$_REQUEST['sold']:'';
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);
        
        if($type == 0){
            $this->readdb->select("v.id");
        }else{
            $this->readdb->select("v.id,v.vehiclename,v.vehicleno,v.vehicletype,v.status,v.vehiclecompanyid,
                vc.companyname,v.createddate,
                owner.id as ownerid,
                CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')') as ownername,
                owner.contactno1 as ownercontactno,
                
                IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=v.id)),'') as site,

                CASE 
                    WHEN v.vehicletype=1 THEN 'Two Wheel' 
                    WHEN v.vehicletype=2 THEN 'Four Wheel'
                    ELSE 'Heavy Vehicles' 
                END AS vehicletypename
            ");
        }                                
        $this->readdb->from($this->_table." as v");
        $this->readdb->join(tbl_party." as owner","owner.id=v.ownerpartyid","INNER");
        $this->readdb->join(tbl_vehiclecompany." as vc","vc.id=v.vehiclecompanyid","LEFT");
        $this->readdb->where("(v.vehiclecompanyid=".$companyid." OR ".$companyid."=0)");
        $this->readdb->where("(v.ownerpartyid=".$ownerpartyid." OR ".$ownerpartyid."=0)");
        $this->readdb->where("(v.vehicletype=".$vehicletype." OR ".$vehicletype."=0)");
        $this->readdb->where("(v.commercial='".$commercial."' OR '".$commercial."'='')");
        $this->readdb->where("(v.sold='".$sold."' OR '".$sold."'='')");
        $this->readdb->where("(date(v.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");

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
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->_order)) {
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
    function getVehicle(){
    	$query = $this->readdb->select("id,vehiclename,vehicleno")
				    	  ->from($this->_table)
						  ->where("status = 1")
						  ->get();
    	
		return $query->result_array();
    }
    function getVehicleForImport(){
    	$query = $this->readdb->select("id,CONCAT(vehiclename,'#',vehicleno) as vehiclename")
				    	  ->from($this->_table)
						  ->get();
    	
		return $query->result_array();
    }    

    function getVehicleDataForExport(){

        $companyid = (isset($_REQUEST['companyid']))?$_REQUEST['companyid']:0;
        $ownerpartyid = (isset($_REQUEST['ownerpartyid']))?$_REQUEST['ownerpartyid']:0;
        $vehicletype = (isset($_REQUEST['vehicletype']))?$_REQUEST['vehicletype']:0;
        $commercial = (isset($_REQUEST['commercial']))?$_REQUEST['commercial']:'';
        $sold = (isset($_REQUEST['sold']))?$_REQUEST['sold']:'';
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("v.id,v.vehiclename,v.vehicleno,v.vehicletype,v.status,v.sold,v.solddate,v.vehiclecompanyid,
                vc.companyname,v.engineno,v.chassisno,v.dateofregistration,v.duedateofregistration,v.remarks,v.createddate,
                owner.id as ownerid,v.commercial,v.fueltype,v.startingkm,v.petrocardno,v.fuelratetype,v.fuelrate,
                CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')') as ownername,
                CONCAT(buyer.firstname,' ',buyer.middlename,' ',buyer.lastname,' (',buyer.partycode,')') as buyername,
                CONCAT(soldparty.firstname,' ',soldparty.middlename,' ',soldparty.lastname,' (',soldparty.partycode,')') as soldpartyname,
                owner.contactno1 as ownercontactno,
                IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=v.id)),'') as site,
                CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename
            ");
        $this->readdb->from($this->_table." as v");
        $this->readdb->join(tbl_party." as owner","owner.id=v.ownerpartyid","INNER");
        $this->readdb->join(tbl_vehiclecompany." as vc","vc.id=v.vehiclecompanyid","LEFT");
        $this->readdb->join(tbl_party." as buyer","buyer.id=v.buyerid","LEFT");
        $this->readdb->join(tbl_party." as soldparty","soldparty.id=v.soldpartyid","LEFT");
        $this->readdb->where("(v.vehiclecompanyid=".$companyid." OR ".$companyid."=0)");
        $this->readdb->where("(v.ownerpartyid=".$ownerpartyid." OR ".$ownerpartyid."=0)");
        $this->readdb->where("(v.vehicletype=".$vehicletype." OR ".$vehicletype."=0)");
        $this->readdb->where("(v.commercial='".$commercial."' OR '".$commercial."'='')");
        $this->readdb->where("(v.sold='".$sold."' OR '".$sold."'='')");
        $this->readdb->where("(date(v.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
        $this->readdb->order_by('v.id','DESC');
        $query=$this->readdb->get();
        return $query->result();
    }

    function getVehicleInstallmentDataByVehicleId($vehicleid){

        $query = $this->readdb->select("id,installmentamount as amount,installmentdate as date,
        (SELECT installmentamount FROM ".tbl_vehicle." where id='".$vehicleid."') as totalamount,DATEDIFF(installmentdate,curdate()) as days
        ")
                        ->from(tbl_vehicleinstallment)
                        ->where("vehicleid=".$vehicleid)
                        ->get();
                        
        if($query->num_rows() > 0){
            return $query->result_array();   
        }else{
            return array();
        }
    }

}
