<?php

class Vehicle_pollution_certificate_model extends Common_model {

	//put your code here
	public $_table = tbl_vehiclepollutioncertificate;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('vpc.id' => 'DESC');
	public $column_order = array('vpc.id','employeename','v.manufacturingcompany','vpc.pcno','vpc.issuingauthority','vpc.fromdate','vpc.todate');
    public $column_search = array('v.manufacturingcompany','v.registrationno','vpc.pcno','vpc.issuingauthority','DATE_FORMAT(vpc.fromdate, "%d/%m/%Y")','DATE_FORMAT(vpc.todate, "%d/%m/%Y")');
	
    function __construct() {
		parent::__construct();
	}
	function getvehiclepollutioncertificateDataByID($ID){
		$query = $this->readdb->select("vpc.id,vpc.vehicleid,vpc.pcno,vpc.issuingauthority,vpc.fromdate,vpc.todate,vpc.proof,vpc.status")
							->from($this->_table." as vpc")
							->where("vpc.id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function get_datatables() {
	   $this->_get_datatables_query();
	    if($_POST['length'] != -1) {
	        $this->readdb->limit($_POST['length'], $_POST['start']);
	        $query = $this->readdb->get();
	        //echo $this->db->last_query();
	        return $query->result();
	    }
    }
	function _get_datatables_query($type=1){  
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        
        if($type == 0){
            $this->readdb->select("vpc.id");
        }else{
            $this->readdb->select("vpc.id,vpc.vehicleid,vpc.pcno,vpc.issuingauthority,vpc.fromdate,vpc.todate,vpc.proof,v.manufacturingcompany,v.registrationno,vpc.status,
            ");
        }
        $this->readdb->from($this->_table." as vpc");
        $this->readdb->where("vpc.channelid='".$channelid."' AND vpc.memberid='".$memberid."'");
        $this->readdb->join(tbl_vehicle." as v", "v.id = vpc.vehicleid", "LEFT");
       
        
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
        $this->_get_datatables_query(0);
        return $this->readdb->count_all_results();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }
}
