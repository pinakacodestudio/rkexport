<?php
class Fuel_report_model extends Common_model {

	//put your code here
	public $_table = tbl_vehicle;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'vehiclename','fueltype','fuelratetypename','totalcost','totalliter','total','averagecost'); //set column field database for datatable orderable
	public $column_search = array("CONCAT(v.vehiclename,' (',v.vehicleno,')')","CASE 
                                    WHEN v.fuelratetype=1 THEN 'KM' 
                                    WHEN v.fuelratetype=2 THEN 'Hour' 
                                    END","(SELECT SUM(amount) FROM ".tbl_fuel." WHERE vehicleid=v.id)","((SELECT SUM(amount) FROM ".tbl_fuel." WHERE vehicleid=v.id)/ IF(v.fuelratetype=1,
                                    (IFNULL((SELECT km FROM ".tbl_fuel." WHERE vehicleid=v.id ORDER BY date DESC LIMIT 1),0)-IFNULL((SELECT km FROM ".tbl_fuel." WHERE vehicleid=v.id ORDER BY date ASC LIMIT 1),0)),
                                    IFNULL((SELECT SUM(km) FROM ".tbl_fuel." WHERE vehicleid=v.id),0)
                                    ))","CASE 
                                    WHEN v.fueltype=1 THEN 'Petrol' 
                                    WHEN v.fueltype=2 THEN 'Diesel' 
                                    WHEN v.fueltype=3 THEN 'Bio-Diesel'
                                    WHEN v.fueltype=4 THEN 'Oil'
                                    END"); //set column field database for datatable searchable 
	public $_order = array("v.id"=>'DESC'); // default order

	function __construct() {
        parent::__construct();
	}
	
	function exportFuelReport(){
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        $fueltypeid = (isset($_REQUEST['fueltypeid']))?$_REQUEST['fueltypeid']:0;
        $fuelratetype = (isset($_REQUEST['fuelratetype']))?$_REQUEST['fuelratetype']:0;
            $this->readdb->select("v.id,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,v.fueltype,
            @totalcost:=(SELECT SUM(amount) FROM ".tbl_fuel." WHERE vehicleid=v.id) as totalcost,
            @totalliter:=(SELECT SUM(liter) FROM ".tbl_fuel." WHERE vehicleid=v.id) as totalliter,
            @total:=IFNULL((SELECT km FROM ".tbl_fuel." WHERE vehicleid=v.id ORDER BY date DESC LIMIT 1),0)-IFNULL((SELECT km FROM ".tbl_fuel." WHERE vehicleid=v.id ORDER BY date ASC LIMIT 1),0) as total,
            CASE 
                WHEN v.fuelratetype=1 THEN 'KM' 
                WHEN v.fuelratetype=2 THEN 'Hour' 
            END AS fuelratetypename,
            (@total/@totalliter) as averagecost,
        ");
        $this->readdb->from($this->_table." as v");
        $this->readdb->where("v.id IN (SELECT vehicleid FROM ".tbl_fuel.")");
        $this->readdb->where("(v.id=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->where("(v.fueltype=".$fueltypeid." OR ".$fueltypeid."=0)");
        $this->readdb->where("(v.fuelratetype=".$fuelratetype." OR ".$fuelratetype."=0)");
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
        $this->readdb->group_by("v.id"); 
        $this->readdb->order_by("v.id",'DESC'); 
		$query = $this->readdb->get();
		
		return $query->result();
    }

    function _get_datatables_query($type=1){

        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        $fueltypeid = (isset($_REQUEST['fueltypeid']))?$_REQUEST['fueltypeid']:0;
        $fuelratetype = (isset($_REQUEST['fuelratetype']))?$_REQUEST['fuelratetype']:0;
        if($type == 0){
            $this->readdb->select("v.id");
        }else{
            $this->readdb->select("v.id,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,v.fueltype,
            @totalcost:=(SELECT SUM(amount) FROM ".tbl_fuel." WHERE vehicleid=v.id) as totalcost,
            @totalliter:=(SELECT SUM(liter) FROM ".tbl_fuel." WHERE vehicleid=v.id) as totalliter,
            @total:=IFNULL((SELECT km FROM ".tbl_fuel." WHERE vehicleid=v.id ORDER BY date DESC LIMIT 1),0)-IFNULL((SELECT km FROM ".tbl_fuel." WHERE vehicleid=v.id ORDER BY date ASC LIMIT 1),0) as total,
            CASE 
                WHEN v.fuelratetype=1 THEN 'KM' 
                WHEN v.fuelratetype=2 THEN 'Hour' 
            END AS fuelratetypename,
            (@total/@totalliter) as averagecost,

        ");
        }                                
        $this->readdb->from($this->_table." as v");
        $this->readdb->where("v.id IN (SELECT vehicleid FROM ".tbl_fuel.")");
        $this->readdb->where("(v.id=".$vehicleid." OR ".$vehicleid."=0)");
        $this->readdb->where("(v.fueltype=".$fueltypeid." OR ".$fueltypeid."=0)");
        $this->readdb->where("(v.fuelratetype=".$fuelratetype." OR ".$fuelratetype."=0)");
		$this->readdb->where("(v.id NOT IN (SELECT vi.vehicleid FROM ".tbl_insurance." as vi INNER JOIN ".tbl_insuranceclaim." as vic ON (vic.insuranceid=vi.id AND vic.status=3)))");
        
        
        
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
    
}