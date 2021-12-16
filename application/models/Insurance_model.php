<?php

class Insurance_model extends Common_model {

	//put your code here
	public $_table = tbl_insurance;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('i.id' => 'DESC');

	public $column_order = array(null,'v.vehiclename','i.companyname','i.policyno','i.fromdate','i.todate','i.amount','i.createddate');
        
    public $column_search = array("(CONCAT(v.vehiclename,' (',v.vehicleno,')'))",'i.companyname','i.policyno','DATE_FORMAT(i.fromdate, "%d/%m/%Y")','DATE_FORMAT(i.todate, "%d/%m/%Y")','i.amount','DATE_FORMAT(i.createddate , "%d %b %Y %H:%i %p")');
    
	function __construct() {
		parent::__construct();
    }
    
    function getInsuranceCompanyByVehicleId($ID) {

        $query = $this->readdb->select("i.id,i.companyname")
                    ->from($this->_table." as i")
                    ->where("i.vehicleid", $ID)
                    ->group_by("i.companyname")
                    ->get();

        return $query->result_array();
    }
    function getInsurancePolicyNumberByVehicleOrCompany($vehicleid,$insurancecompany) {

        $query = $this->readdb->select("i.id,i.policyno")
                    ->from($this->_table." as i")
                    ->where("i.vehicleid='".$vehicleid."' AND i.companyname='".$insurancecompany."'")
                    ->get();

        return $query->result_array();
    }
    function searchInsuranceCompany($type,$search){

		$this->readdb->select("id,companyname as text");
		$this->readdb->from($this->_table);
		if($type==1){
			$this->readdb->where("companyname LIKE '%".$search."%'");
		}else{
			$this->readdb->where("companyname='".$search."'");
        }
        $this->readdb->group_by("companyname");
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			if($type==1){
				return $query->result_array();
			}else{
				return $query->row_array();
			}
		}else {
			return 0;
		}	
    }
    
    function getInsuranceCompanyList(){
       
        $query = $this->readdb->select("i.id,i.companyname")
                ->from($this->_table." as i")
                ->group_by("i.companyname")
                ->order_by("i.companyname", "ASC")
                ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return 0;
        }	

    }

	function getvehicleinsuranceDataByID($ID){
		$query = $this->readdb->select("vi.id,vi.vehicleid,vi.companyname,vi.policyno,vi.fromdate,vi.todate,vi.paymentdate,vi.proof,vi.amount")
							->from($this->_table." as vi")
							->where("vi.id", $ID)
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

        $insurancecompany = (isset($_REQUEST['insurancecompany']))?$_REQUEST['insurancecompany']:'';

        if($type == 0){
            $this->readdb->select("i.id");
        }else{
            $this->readdb->select("i.id,i.vehicleid,i.companyname,i.policyno,i.fromdate,i.todate,i.paymentdate,i.proof,v.vehiclename,v.vehicleno,i.amount,i.createddate,");
        }
        $this->readdb->from($this->_table." as i");
        $this->readdb->join(tbl_vehicle." as v", "v.id = i.vehicleid", "INNER");
        $this->readdb->where("(i.companyname='".$insurancecompany."' OR '".$insurancecompany."'='')");
        
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
    function getVehicle(){
    	$query = $this->readdb->select("vehicleid")
				    	  ->from($this->_table)
						  ->get();
    	
		return $query->result_array();
    }
   
    function getInsuranceDataForExport(){
        $insurancecompany = (isset($_REQUEST['insurancecompany']))?$_REQUEST['insurancecompany']:'';

        $query=$this->readdb->select("i.id,i.vehicleid,i.companyname,i.policyno,i.fromdate,i.todate,i.paymentdate,i.proof,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,i.amount,i.createddate")
                    ->from($this->_table." as i")
                    ->join(tbl_vehicle." as v", "v.id = i.vehicleid", "INNER")
                    ->where("(i.companyname='".$insurancecompany."' OR '".$insurancecompany."'='')")
                    ->order_by('i.id',"DESC")
                    ->get();

                    return $query->result();
    }
}
