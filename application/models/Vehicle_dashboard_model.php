<?php
class Vehicle_dashboard_model extends CI_model{

	function getPartyCount(){
		$query = $this->readdb->select("COUNT(id) as count")
        ->from(tbl_party)
        ->get();

		$count=$query->row_array();
        return $count['count'];	
	}

	function getVehicleCount(){
		$query = $this->readdb->select("COUNT(id) as count")
        ->from(tbl_vehicle)
        ->get();

		$count=$query->row_array();
        return $count['count'];		
	}


	function getSiteCount(){
		$query = $this->readdb->select("COUNT(id) as count")
        ->from(tbl_site)
        ->get();

		$count=$query->row_array();
        return $count['count'];	
	}

	function folderSize ($dir){
			/*  $size = 0;

			foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
				$size += is_file($each) ? filesize($each) : folderSize($each);
			}

			return $size;  */

			$f = 'uploaded/'.CLIENT_FOLDER;
			$io = popen ( '/usr/bin/du -sk ' . $f, 'r' );
			$size = fgets ( $io, 4096);
			$size = substr ( $size, 0, strpos ( $size, "\t" ) );
			pclose ( $io );

			
			$size = round($size / 1024 / 1024 / 1024,4) ;	
			

			return  $size;
	}

	function getExpiredDocumentData($days='30',$resulttype=0){
		// resulttype='0' array ,$resulttype='1' object
		$where = "d.duedate!='0000-00-00' AND d.duedate BETWEEN curdate() AND DATE_ADD(curdate(),INTERVAL +".$days." DAY)";

		$this->readdb->select("d.id,d.referencetype,d.referenceid,d.documenttypeid,
				d.documentnumber,d.fromdate,d.duedate,d.documentfile,
				IFNULL(p.id,'') as partyid,IFNULL(v.id,'') as vehicleid,
                CONCAT(p.firstname,' ',p.middlename,' ',p.lastname,' (',p.partycode,')') as partyname,
                IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
                dt.documenttype,DATEDIFF(d.duedate,CURDATE()) as days
        ");

        $this->readdb->from(tbl_document." as d");
        $this->readdb->join(tbl_documenttype." as dt","dt.id=d.documenttypeid","INNER");
		$this->readdb->join(tbl_vehicle." as v","v.id=d.referenceid AND d.referencetype=0","LEFT");
		$this->readdb->join(tbl_party." as p","(p.id=d.referenceid AND d.referencetype=1) OR (d.referencetype=0 AND p.id=v.ownerpartyid)","LEFT");
		$this->readdb->where($where);
		$this->readdb->where("IF(v.id=d.referenceid AND d.referencetype=0,(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3))),1=1)");
		$this->readdb->limit("10");
		$this->readdb->order_by('d.duedate',"ASC");
		$query =$this->readdb->get();
		
		if($resulttype==0){
			return $query->result_array();
		}else{
			return $query->result();
		}
    	
	}

	function getExpiredPartsData($days='30',$resulttype=0){
		// resulttype='0' array ,$resulttype='1' object
		$where = "spd.duedate!='0000-00-00' AND spd.duedate BETWEEN curdate() AND DATE_ADD(curdate(),INTERVAL +".$days." DAY)";

		$query = $this->readdb->select("spd.id,spd.serviceid,s.vehicleid,spd.partname,
		IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
		spd.serialnumber,spd.warrantyenddate,spd.duedate,s.amount,DATEDIFF(spd.duedate,CURDATE()) as days")
		->from(tbl_servicepartdetails. " as spd")
		->join(tbl_service." as s","s.id=serviceid","LEFT")
		->join(tbl_vehicle." as v","v.id=vehicleid","LEFT")
		->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))")
		->where($where)
		->order_by('spd.duedate',"ASC")
		->get();

        if($resulttype==0){
			return $query->result_array();
		}else{
			return $query->result();
		}
    	
	}

	function getExpiredInsuranceData($days='30',$resulttype=0){
		// resulttype='0' array ,$resulttype='1' object
		
		$where = "i.todate!='0000-00-00' AND i.todate BETWEEN curdate() AND DATE_ADD(curdate(),INTERVAL +".$days." DAY)";

		$this->readdb->select("i.id,i.vehicleid,IF(IFNULL(v.vehiclename,'')!='',CONCAT(v.vehiclename,' (',v.vehicleno,')'),'') as vehiclename,
		i.companyname,i.policyno,v.vehicleno,i.todate,i.fromdate,i.amount,DATEDIFF(i.todate,CURDATE()) as days");
		$this->readdb->from(tbl_insurance." as i");
        $this->readdb->join(tbl_vehicle." as v", "v.id = i.vehicleid", "INNER");
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
		$this->readdb->where($where);
		$this->readdb->order_by('i.todate',"ASC");
		$query =$this->readdb->get();

		if($resulttype==0){
			return $query->result_array();
		}else{
			return $query->result();
		}
    	
	}

	function getExpiredVehicleRegistrationData($days='30',$resulttype=0){

		// resulttype='0' array ,$resulttype='1' object

		$where = "v.duedateofregistration!='0000-00-00' AND v.duedateofregistration BETWEEN curdate() AND DATE_ADD(curdate(),INTERVAL +".$days." DAY)";

		$this->readdb->select("v.id,v.vehicleno,v.vehicletype,v.status,v.duedateofregistration,
		vc.companyname,CONCAT(v.vehiclename,' (',vc.companyname,')')as vehiclename,
		owner.id as ownerid,DATEDIFF(duedateofregistration,CURDATE()) as days,
		CONCAT(owner.firstname,' ',owner.middlename,' ',owner.lastname,' (',owner.partycode,')') as ownername,
		owner.contactno1 as ownercontactno,
		
		IFNULL((SELECT GROUP_CONCAT(DISTINCT sitename SEPARATOR ', ') FROM ".tbl_site." WHERE id IN (SELECT siteid FROM ".tbl_assignvehicle." WHERE vehicleid=v.id)),'') as site,

		CASE 
			WHEN v.vehicletype=1 THEN 'Two Wheel' 
			WHEN v.vehicletype=2 THEN 'Four Wheel'
			ELSE 'Heavy Vehicles' 
		END AS vehicletypename");
		$this->readdb->from(tbl_vehicle." as v");
		$this->readdb->join(tbl_vehiclecompany." as vc","vc.id=v.vehiclecompanyid","INNER");
		$this->readdb->join(tbl_party." as owner","owner.id=v.ownerpartyid","INNER");
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
		$this->readdb->where($where);
		$this->readdb->order_by('v.duedateofregistration',"ASC");
		$query =$this->readdb->get();

        if($resulttype==0){
			return $query->result_array();
		}else{
			return $query->result();
		}
	}
	// function getExpiredVehicleRegistrationCount(){
	// 	$where = array("duedateofregistration!="=>'0000-00-00',"(DATE(duedateofregistration) < curdate())"=>null);

	// 	$query = $this->readdb->select("COUNT(id) as count")
	// 				  ->from(tbl_vehicle)
	// 				  ->where($where)
	// 				  ->get();

	// 	$count=$query->row_array();
    //     return $count['count'];	
	// }

	// function getExpiredDocumentsCount(){
	// 	$where = array("duedate!="=>'0000-00-00',"(DATE(duedate) < curdate())"=>null);

	// 	$query = $this->readdb->select("COUNT(id) as count")
	// 				  ->from(tbl_document)
	// 				  ->where($where)
	// 				  ->get();

	// 	$count=$query->row_array();
    //     return $count['count'];	
	// }

	// function getExpiredServicePartCount(){
	// 	$where = array("duedate!="=>'0000-00-00',"(DATE(duedate) < curdate())"=>null);

	// 	$query = $this->readdb->select("COUNT(id) as count")
	// 				  ->from(tbl_servicepartdetails)
	// 				  ->where($where)
	// 				  ->get();

	// 	$count=$query->row_array();
    //     return $count['count'];	
	// }

	// function getExpiredInsuranceCount(){
	// 	$where = array("todate!="=>'0000-00-00',"(DATE(todate) < curdate())"=>null);

	// 	$query = $this->readdb->select("COUNT(id) as count")
	// 				  ->from(tbl_insurance)
	// 				  ->where($where)
	// 				  ->get();

	// 	$count=$query->row_array();
    //     return $count['count'];	
	// }

	function getAlertServicePartsData($resulttype=0){

		$this->readdb->select("CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,s.vehicleid,sd.partname,sd.serialnumber,sd.alertkmhr,
            IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.currentkmhr) as currentkmhr
            ");

		$this->readdb->from(tbl_servicepartdetails." as sd");
		$this->readdb->join(tbl_service." as s","(s.id=sd.serviceid AND sd.setalert=1)","INNER");
		$this->readdb->join(tbl_vehicle." as v","(v.id=s.vehicleid)","INNER");
		$this->readdb->where("IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<sd.currentkmhr)");
		$this->readdb->limit("10");
		$this->readdb->order_by('sd.id',"DESC");
		$query =$this->readdb->get();
		
		if($resulttype==0){
			return $query->result_array();
		}else{
			return $query->result();
		}
	}

	function getAlertServicePartCount(){

		$query = $this->readdb->select("COUNT(sd.id) as count")
					  ->from(tbl_servicepartdetails." as sd")
					  ->join(tbl_service." as s","(s.id=sd.serviceid AND sd.setalert=1)","INNER")
					  ->join(tbl_vehicle." as v","(v.id=s.vehicleid)","INNER")
					  ->where("IF(sd.currentkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<(SELECT f.km FROM ".tbl_fuel." as f WHERE f.vehicleid=v.id ORDER BY f.date DESC LIMIT 1),sd.alertkmhr<sd.currentkmhr)")
					  ->get();

		$count=$query->row_array();
        return $count['count'];	
	}

	function getVehicleEMIData($days='30',$resulttype=0){

		$where = "vi.installmentdate!='0000-00-00' AND vi.installmentdate BETWEEN curdate() AND DATE_ADD(curdate(),INTERVAL +".$days." DAY)";

		$this->readdb->select("CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,vi.vehicleid,vi.installmentamount,vi.installmentdate,DATEDIFF(vi.installmentdate,curdate()) as days");
        $this->readdb->from(tbl_vehicleinstallment." as vi");
        $this->readdb->join(tbl_vehicle." as v","(v.id=vi.vehicleid)","INNER");
		$this->readdb->where($where);
        $this->readdb->order_by('days','ASC');
		$query =$this->readdb->get();
		
		if($resulttype==0){
			return $query->result_array();
		}else{
			return $query->result();
		}
    	
	}

}