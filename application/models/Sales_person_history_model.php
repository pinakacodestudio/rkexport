<?php

class Sales_person_history_model extends Common_model {

	//put your code here
	public $_table = tbl_salespersonroute;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('spr.id' => 'DESC'); // default order 
	public $column_order = array('employeename','spr.routename','spr.vehiclename','spr.startdatetime','collection','methodname','loosmoney','spr.status'); //set column field database for datatable orderable
    public $column_search = array('(CONCAT(u.name," - ",u.mobileno))','spr.routename','spr.vehiclename','spr.startdatetime',
        "((CASE 
            WHEN spr.status=0 THEN 'Pending' WHEN spr.status=1 THEN 'Complete' WHEN spr.status=2 THEN 'Cancel'
        END))"
    ); //set column field database for datatable searchable 
	
	function __construct() {
		parent::__construct();
	}
	function getMapPoints($routelocationdata){

		$PostData = $this->input->post();
		$this->load->model('Assigned_route_model', 'Assigned_route');

		$markerlat_long_arr = $flightPlanlat_long_arr = $lat_long_center_point = $icon_array = $time_array = $info_window = array();
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
					if($this->Vehicletype[$td['vehicletype']] == "Bike"){
						$icon_array[]=DEFAULT_IMG."quad-bike.png";
					}else if($this->Vehicletype[$td['vehicletype']] == "Rickshaw"){
						$icon_array[]=DEFAULT_IMG."auto-rickshaw.png";
					}else{
						$icon_array[]=DEFAULT_IMG."bus.png";
					}
					$time_array[]="Start : ".date("d-m-Y h:i:s", strtotime($td['routelocationtime']));

					$invoicedata = $this->Assigned_route->getInvoiceDataByAssignedRoute($td['assignedrouteid']);

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
		return array('endroute'=>$endroute,'time_array'=>$time_array,'icon_array'=>$icon_array,'flightPlanlat_long_arr'=>$flightPlanlat_long_arr,'markerlat_long_arr'=>$markerlat_long_arr,"lat_long_center_point"=>$lat_long_center_point,'info_window'=>$info_window);
	}
	function getSalesPersonHistoryDataById($salespersonrouteid){

		$query = $this->readdb->select("spr.id,spr.assignedrouteid,spr.routename,spr.vehiclename,spr.description,
						spr.startdatetime,spr.enddatetime,spr.status
					")
					->from($this->_table." as spr")
					->where("spr.id=".$salespersonrouteid)
					->get();

		return $query->row_array();
	}
	function getRouteBySalesPersonRoute(){
		
		$query = $this->readdb->select("r.id,spr.routename")
					->from($this->_table." as spr")
					->join(tbl_assignedroute." as ar","ar.id=spr.assignedrouteid","INNER")
					->join(tbl_route." as r","r.id=ar.routeid","INNER")
					->group_by("ar.routeid")
					->get();

		return $query->result_array();
	}
	//LISTING DATA
	function _get_datatables_query(){
		
		$employeeid = isset($_REQUEST['employeeid'])?$_REQUEST['employeeid']:'0';
        $routeid = isset($_REQUEST['routeid'])?$_REQUEST['routeid']:'0';
        $status = isset($_REQUEST['status'])?$_REQUEST['status']:'';
        
        $this->readdb->select("spr.id,
                CONCAT(CONCAT(UCASE(MID(u.name,1,1)),MID(u.name,2)), ' - ',u.mobileno) as employeename,
                spr.routename,
                spr.vehiclename,
                spr.startdatetime, 

                IFNULL((SELECT SUM(prt.amount) 
                    FROM ".tbl_paymentreceipttransactions." as prt
                    INNER JOIN ".tbl_paymentreceipt." as pr ON pr.id=prt.paymentreceiptid
                    WHERE pr.isagainstreference=1 AND pr.usertype=0 AND (pr.addedby=".$employeeid." OR ".$employeeid."=0) AND prt.invoiceid IN (SELECT invoiceid FROM ".tbl_assignedrouteinvoicemapping." WHERE assignedrouteid=ar.id)
                ),0) as collection,
                
                0 as loosmoney,
                spr.status
            ");

        $this->readdb->from($this->_table." as spr");
        $this->readdb->join(tbl_user." as u","u.id=spr.employeeid","INNER");
        $this->readdb->join(tbl_assignedroute." as ar","ar.id=spr.assignedrouteid","INNER");

        $this->readdb->where("(spr.employeeid=".$employeeid." OR ".$employeeid."=0)");
        $this->readdb->where("(ar.routeid = ".$routeid." OR ".$routeid."=0)");
        $this->readdb->where("(spr.status='".$status."' OR '".$status."'='')");

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
