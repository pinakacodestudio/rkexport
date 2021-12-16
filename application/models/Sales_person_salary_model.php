<?php

class Sales_person_salary_model extends Common_model {

	//put your code here
	public $_table = tbl_salespersonroutelocation;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('s.createddate' => 'ASC'); // default order 
	public $column_order = array("salespersonname","date");
	public $column_search = array();

	function __construct() {
		parent::__construct();
	}
	
	//LISTING DATA
	function _get_datatables_query(){
		
		$dayormonth = isset($_REQUEST['dayormonth'])?$_REQUEST['dayormonth']:'0';
        $startdate = isset($_REQUEST['startdate'])?$_REQUEST['startdate']:'';
        $enddate = isset($_REQUEST['enddate'])?$_REQUEST['enddate']:'';
		
		if($dayormonth==0){
			$startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            // $monthArray = $this->general_model->month_range($startdate,$enddate,'m');
            $dateformat = '%m/%Y';
            $group_by = 'MONTH(s.createddate)';
        }else{
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = '%d/%m/%Y';
			$group_by = 'DATE(s.createddate)';
        }
		
		$this->readdb->select("CONCAT(u.name,' - ',u.mobileno) as salespersonname,DATE_FORMAT(s.createddate,'".$dateformat."') as date,
							SUM(IFNULL((6371 * acos( 
								cos( radians(s.latitude) ) 
								* cos( radians( (SELECT latitude FROM ".tbl_salespersonroutelocation." as spl WHERE spl.id!=s.id AND spl.id>s.id AND (DATE(spl.createddate) BETWEEN '".$startdate."' AND '".$enddate."') AND spl.addedby=u.id ORDER BY spl.createddate DESC limit 1) ) ) 
								* cos( radians( (SELECT longitude FROM ".tbl_salespersonroutelocation." as spl WHERE spl.id!=s.id AND spl.id>s.id AND (DATE(spl.createddate) BETWEEN '".$startdate."' AND '".$enddate."') AND spl.addedby=u.id ORDER BY spl.createddate DESC limit 1) ) - radians(s.longitude) ) 
								+ sin( radians(s.latitude) ) 
								* sin( radians( (SELECT latitude FROM ".tbl_salespersonroutelocation." as spl WHERE spl.id!=s.id AND spl.id>s.id AND (DATE(spl.createddate) BETWEEN '".$startdate."' AND '".$enddate."') AND spl.addedby=u.id ORDER BY spl.createddate DESC limit 1) ) )
									) ),0)) as distance");
		$this->readdb->from(tbl_user." as u");
		$this->readdb->join(tbl_salespersonroutelocation." as s","s.addedby=u.id",'LEFT');
		$this->readdb->where("(DATE(s.createddate) BETWEEN '".$startdate."' AND '".$enddate."')");
		// $this->readdb->where("(@distance)!=0");
			
		$this->readdb->group_by($group_by);
        $this->readdb->order_by("s.createddate",'ASC');
        
        // $this->readdb->where("(spm.channelid='".$channelid."' OR ".$channelid."=0)");
        // $this->readdb->where("(FIND_IN_SET(spm.memberid,'".$memberid."')>0 OR '".$memberid."'='')");

		/* $i = 0;
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
		} */
		
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
		// pre($this->readdb->last_query());
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
