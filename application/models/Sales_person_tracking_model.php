<?php

class Sales_person_tracking_model extends Common_model {

	//put your code here
	public $_table = tbl_salespersonroute;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = ""; // default order 
	
	function __construct() {
		parent::__construct();
	}
	
	function getSalesPersonRouteByID($salespersonrouteid) {

		$this->readdb->select("spr.assignedrouteid,sprl.latitude,sprl.longitude,sprl.createddate as routelocationtime,
							IF(spr.enddatetime='0000-00-00 00:00:00',0,1) as endroute,
							(SELECT type FROM ".tbl_vehicle." WHERE id=ar.vehicleid) as vehicletype
							"); 
		$this->readdb->from(tbl_salespersonroutelocation." as sprl");
		$this->readdb->join($this->_table." as spr","spr.id=sprl.salespersonrouteid","INNER");
		$this->readdb->join(tbl_assignedroute." as ar","ar.id=spr.assignedrouteid","INNER");
		$this->readdb->where("spr.id=".$salespersonrouteid."");
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getSalesPersonRoute($employeeid,$vehicleid,$routeid,$date) {

		$this->readdb->select("spr.assignedrouteid,sprl.latitude,sprl.longitude,sprl.createddate as routelocationtime,
							IF(spr.enddatetime='0000-00-00 00:00:00',0,1) as endroute,
							IFNULL((SELECT vehicletype FROM ".tbl_vehicle." WHERE id=ar.vehicleid),0) as vehicletype
							"); 
		$this->readdb->from(tbl_salespersonroutelocation." as sprl");
		$this->readdb->join($this->_table." as spr","spr.id=sprl.salespersonrouteid","INNER");
		$this->readdb->join(tbl_assignedroute." as ar","ar.id=spr.assignedrouteid","INNER");
		$this->readdb->where("(spr.employeeid=".$employeeid." OR ".$employeeid."=0) AND (ar.routeid=".$routeid." OR ".$routeid."=0) AND (ar.vehicleid=".$vehicleid." OR ".$vehicleid."=0)");
		$this->readdb->where("(DATE(sprl.createddate) = '".$date."' )");
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getMapPoints($routelocationdata){

		$PostData = $this->input->post();
		// pre($PostData);
		$this->load->model('Assigned_route_model', 'Assigned_route');

		$markerlat_long_arr = $flightPlanlat_long_arr = $icon_array = $time_array = $info_window = array();
		$lat_long_center_point = array("lat" => (float)0,"long" => (float)0);
		$spent_time_minute="";
		$iv=1;
		if (isset($routelocationdata) && $routelocationdata) {
			foreach ($routelocationdata as $td) {
				
				if ($iv==1) {
					$lat_long_center_point=array("lat"=>(float)$td['latitude'],"lng"=>(float)$td['longitude']);
				}
			
				$datetime2 = new DateTime($td['routelocationtime']);
				$t2 = strtotime($td['routelocationtime']);

				$secondlatitude = $td['latitude'];
				$secondlongitude = $td['longitude'];

				if (isset($datetime1) && isset($t1)) {
					/**/
					$dtd = new stdClass();
					$dtd->interval = $t2 - $t1;
					$dtd->total_sec = abs($t2-$t1);
					$dtd->total_min = floor($dtd->total_sec/60);
					$dtd->total_hour = floor($dtd->total_min/60);
					$dtd->total_day = floor($dtd->total_hour/24);

					$dtd->day = $dtd->total_day;
					$dtd->hour = $dtd->total_hour -($dtd->total_day*24);
					$dtd->min = $dtd->total_min -($dtd->total_hour*60);
					$dtd->sec = $dtd->total_sec -($dtd->total_min*60);
					if ($dtd->total_hour>0) {
						$spent_time= $dtd->total_hour." Hours, ".$dtd->min." Minutes, ".$dtd->sec." Seconds";
						$spent_time_minute= $dtd->total_min;
					} elseif ($dtd->total_min>0) {
						$spent_time= $dtd->total_min." Minutes, ".$dtd->sec." Seconds";
						$spent_time_minute= $dtd->total_min;
					} else {
						$spent_time= $dtd->total_sec." Seconds";
						$spent_time_minute= $dtd->total_min;
					}

					$distance = $this->general_model->distance($td['latitude'],$td['longitude'],$secondlatitude,$secondlongitude,'METER').' Meter';
					
					/*$interval = $datetime1->diff($datetime2);
					$elapsed = $interval->format('%H:%I:%S');
					$elapsed1 = $interval->format('%i');*/

					if (isset($PostData['spent_time']) && $PostData['spent_time']!="") {
						
						if ($spent_time_minute>=(int)$PostData['spent_time']) {
							if ((int)$dtd->total_min>=30) {
								$icon_array[]="";
							} else {
								// $icon_array[]=DEFAULT_IMG."pin.png";
								$icon_array[]=DEFAULT_IMG."user-location.png";
							}
							$time_array[]="Point ".$iv." \n Time Taken : ".$spent_time." \n P".($iv-1)." - P".$iv." (Distance) : ".$distance;
							$info_window[]="";
						}
					} else {
						if ((int)$dtd->total_min>=30) {
							$icon_array[]="";
						} else {
							// $icon_array[]=DEFAULT_IMG."pin.png";
							$icon_array[]=DEFAULT_IMG."user-location.png";
						}
						$time_array[]="Point ".$iv." \n Time Taken : ".$spent_time." \n P".($iv-1)." - P".$iv." (Distance) : ".$distance;
						$info_window[]="";
					}
				} else {
					if(isset($this->Vehicletype[$td['vehicletype']]) && $this->Vehicletype[$td['vehicletype']] == "Bike"){
						$icon_array[]=DEFAULT_IMG."quad-bike.png";
					}else if(isset($this->Vehicletype[$td['vehicletype']]) && $this->Vehicletype[$td['vehicletype']] == "Rickshaw"){
						$icon_array[]=DEFAULT_IMG."auto-rickshaw.png";
					}else{
						$icon_array[]=DEFAULT_IMG."bus.png";
					}
					$time_array[]="Start : ".date("d-m-Y h:i:s", strtotime($td['routelocationtime']));

					$invoicedata = $this->Assigned_route->getInvoiceDataByAssignedRoute($td['assignedrouteid']);

					$distancedata = $this->getDistancebySalesPersonIdAndRouteId($PostData['employeeid'],$this->general_model->convertdate($PostData['date']),$PostData['routeid']);
					// pre($distancedata);
					
					$text = "";

					if(!empty($invoicedata)){
						$text .= "<h4>Invoice Detail</h4>
								<table class='table table-bordered'>
									<thead>
										<tr>
											<th>Invoice No.</th>
											<th class='text-right'>Invoice Amount (".CURRENCY_CODE.")</th>
										</tr>
									</thead>
									<tbody>";
						foreach($invoicedata as $k=>$invoice){
							$text .= "<tr>";
							$text .= "<td>".$invoice['invoiceno']."</td>";
							$text .= "<td class='text-right'>".numberFormat($invoice['invoiceamount'],2,',')."</td>";
							$text .= "</tr>";
						}
						$text .= "</tbody></table>";
					}
					if(!empty($distancedata)){
						$text .= "<h4><strong>Total KM:".numberFormat($distancedata['distance'],2)."</strong></h4>";
					}
					$info_window[] = $text;
				}

				$datetime1 = new DateTime($td['routelocationtime']);
				$t1 = strtotime($td['routelocationtime']);
				$firstlatitude = $td['latitude'];
				$firstlongitude = $td['longitude'];

				if (isset($PostData['spent_time']) && $PostData['spent_time']!="" && $iv!=1) {
					if ($spent_time_minute>=(int)$PostData['spent_time']) {
						$markerlat_long_arr[]=array($td['latitude'],$td['longitude']);
						$flightPlanlat_long_arr[]=array("lat"=>(float)$td['latitude'],"lng"=>(float)$td['longitude']);
					}
				} else {
					$markerlat_long_arr[]=array($td['latitude'],$td['longitude']);
					$flightPlanlat_long_arr[]=array("lat"=>(float)$td['latitude'],"lng"=>(float)$td['longitude']);
				}

				$iv++;
			}
		}

		$endroute = (!empty($routelocationdata))?$routelocationdata[0]['endroute']:1;
		if (count($icon_array)>1) {
			if($endroute){
				$icon_array[count($icon_array)-1]=DEFAULT_IMG."destination.png";
				// $icon_array[count($icon_array)-1]=DEFAULT_IMG."user-location.png";
				$info_window[]="";
			}else{
				// $icon_array[count($icon_array)-1]=DEFAULT_IMG."current-location.png";
				if($this->Vehicletype[$td['vehicletype']] == "Bike"){
					$icon_array[]=DEFAULT_IMG."quad-bike.png";
				}else if($this->Vehicletype[$td['vehicletype']] == "Rickshaw"){
					$icon_array[]=DEFAULT_IMG."auto-rickshaw.png";
				}else{
					$icon_array[]=DEFAULT_IMG."bus.png";
				}
				$info_window[]="";
			}
		}
		// print_r(array('endroute'=>$endroute,'time_array'=>$time_array,'icon_array'=>$icon_array,'flightPlanlat_long_arr'=>$flightPlanlat_long_arr,'markerlat_long_arr'=>$markerlat_long_arr,"lat_long_center_point"=>$lat_long_center_point,'info_window'=>$info_window));
		return array('endroute'=>$endroute,'time_array'=>$time_array,'icon_array'=>$icon_array,'flightPlanlat_long_arr'=>$flightPlanlat_long_arr,'markerlat_long_arr'=>$markerlat_long_arr,"lat_long_center_point"=>$lat_long_center_point,'info_window'=>$info_window);
	}

	function getDistancebySalesPersonIdAndRouteId($employeeid,$date,$routeid='0'){
		$this->readdb->select("SUM(IFNULL((6371 * acos( 
			cos( radians(sprl.latitude) ) 
			* cos( radians( (SELECT latitude FROM ".tbl_salespersonroutelocation." as spl WHERE spl.id!=sprl.id AND spl.id>sprl.id AND DATE(spl.createddate)='".$date."' AND spl.addedby=u.id ORDER BY spl.createddate DESC limit 1) ) ) 
			* cos( radians( (SELECT longitude FROM ".tbl_salespersonroutelocation." as spl WHERE spl.id!=sprl.id AND spl.id>sprl.id AND DATE(spl.createddate)='".$date."' AND spl.addedby=u.id ORDER BY spl.createddate DESC limit 1) ) - radians(sprl.longitude) ) 
			+ sin( radians(sprl.latitude) ) 
			* sin( radians( (SELECT latitude FROM ".tbl_salespersonroutelocation." as spl WHERE spl.id!=sprl.id AND spl.id>sprl.id AND DATE(spl.createddate)='".$date."' AND spl.addedby=u.id ORDER BY spl.createddate DESC limit 1) ) )
				) ),0)) as distance"); 
		$this->readdb->from(tbl_salespersonroutelocation." as sprl");
		$this->readdb->join(tbl_user." as u","u.id=sprl.addedby","INNER");
		$this->readdb->join(tbl_salespersonroute." as spr","spr.id=sprl.salespersonrouteid","INNER");
		$this->readdb->join(tbl_assignedroute." as ar","ar.id=spr.assignedrouteid","INNER");
		$this->readdb->where("(spr.employeeid=".$employeeid." OR ".$employeeid."=0) AND (ar.routeid=".$routeid." OR ".$routeid."=0)");
		$this->readdb->where("(DATE(sprl.createddate) = '".$date."' )");
		$query = $this->readdb->get();

		return $query->row_array();
	}

}
