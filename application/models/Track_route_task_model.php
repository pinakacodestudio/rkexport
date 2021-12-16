<?php

class Track_route_task_model extends Common_model {

	//put your code here
	public $_table = tbl_trackroutetask;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	
	function __construct() {
		parent::__construct();
	}

	function getTrackRouteTaskByFollowup($followupid){
		$query = $this->readdb->select("t.id,t.taskname,t.startdatetime")
							->from($this->_table." as t")
							->join(tbl_trackroute." as tr","tr.taskid=t.id AND tr.followupid=".$followupid)
							->group_by("t.id")
							->order_by("t.id DESC")
							->get();
		
		return $query->result_array();
	}
	
	function getMapPoints($trackroutedata){

		$PostData = $this->input->post();

		$markerlat_long_arr = $flightPlanlat_long_arr = $lat_long_center_point = $icon_array = $time_array = array();
		$spent_time_minute="";
		$iv=1;
		if (isset($trackroutedata) && $trackroutedata) {
			foreach ($trackroutedata as $td) {
				if ($iv==1) {
					$lat_long_center_point=array("lat"=>(float)$td['latitude'],"lng"=>(float)$td['longitude']);
				}
			
				$datetime2 = new DateTime($td['trackroutelocationtime']);
				$t2 = strtotime($td['trackroutelocationtime']);

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

					$distance = $this->general_model->distance($firstlatitude,$firstlongitude,$secondlatitude,$secondlongitude,'METER').' Meter';
					/**/
					/*$interval = $datetime1->diff($datetime2);
					$elapsed = $interval->format('%H:%I:%S');
					$elapsed1 = $interval->format('%i');*/

					if (isset($PostData['spent_time']) && $PostData['spent_time']!="") {
						
						if ($spent_time_minute>=(int)$PostData['spent_time']) {
							if ((int)$dtd->total_min>=30) {
								$icon_array[]="";
							} else {
								$icon_array[]=DEFAULT_IMG."pin.png";
							}
							$time_array[]="Point ".$iv." \n Time Taken : ".$spent_time." \n P".($iv-1)." - P".$iv." (Distance) : ".$distance;
						}
					} else {
						if ((int)$dtd->total_min>=30) {
							$icon_array[]="";
						} else {
							$icon_array[]=DEFAULT_IMG."pin.png";
						}
						$time_array[]="Point ".$iv." \n Time Taken : ".$spent_time." \n P".($iv-1)." - P".$iv." (Distance) : ".$distance;
					}
				} else {
					$icon_array[]=DEFAULT_IMG."home.png";
					$time_array[]="Start : ".date("d-m-Y h:i:s", strtotime($td['trackroutelocationtime']));
				}

				$datetime1 = new DateTime($td['trackroutelocationtime']);
				$t1 = strtotime($td['trackroutelocationtime']);
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

		$endtask = (!empty($trackroutedata))?$trackroutedata[0]['endtask']:1;
		if (count($icon_array)>1) {
			if($endtask){
				$icon_array[count($icon_array)-1]=DEFAULT_IMG."destination.png";
			}else{
				$icon_array[count($icon_array)-1]=DEFAULT_IMG."current-location.png";
			}
		}
		return array('endtask'=>$endtask,'time_array'=>$time_array,'icon_array'=>$icon_array,'flightPlanlat_long_arr'=>$flightPlanlat_long_arr,'markerlat_long_arr'=>$markerlat_long_arr,"lat_long_center_point"=>$lat_long_center_point);
	}
}
