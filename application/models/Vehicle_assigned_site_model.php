<?php

class Vehicle_assigned_site_model extends Common_model {

	//put your code here
	public $_table = tbl_assignvehicle;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('av.id' => 'DESC');
	public $column_order = array('av.date','s.sitename','ct.name','p.name');
    public $column_search = array('s.sitename','ct.name','p.name','DATE_FORMAT(av.date, "%d/%m/%Y")');
        
	function __construct() {
		parent::__construct();
    }
    
    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
        
            return $query->result();
        }
    }

	function _get_datatables_query(){  
        
        $vehicleid = (isset($_REQUEST['vehicleid']))?$_REQUEST['vehicleid']:0;
        $cityid = (isset($_REQUEST['cityid']))?$_REQUEST['cityid']:0;
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("av.id,s.sitename,IFNULL(ct.name,'') as sitecity,IFNULL(p.name,'') as siteprovince,av.date,av.createddate");
        $this->readdb->from($this->_table." as av");
        $this->readdb->join(tbl_site." as s","s.id=av.siteid","INNER");
        $this->readdb->join(tbl_city." as ct","ct.id=s.cityid","LEFT");
        $this->readdb->join(tbl_province." as p","p.id=s.provinceid","LEFT");
        $this->readdb->where("av.vehicleid=".$vehicleid);
        $this->readdb->where("(s.cityid='".$cityid."' OR '".$cityid."'='0')");
        $this->readdb->where("(av.date BETWEEN '".$fromdate."' AND '".$todate."')");

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
}
