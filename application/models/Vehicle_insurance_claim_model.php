<?php

class Vehicle_insurance_claim_model extends Common_model {

	//put your code here
	public $_table = tbl_insuranceclaim;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('ic.id' => 'DESC');
	public $column_order = array('i.companyname','i.policyno','(SELECT agentname FROM '.tbl_insuranceagent.' where id=ic.insuranceagentid)','ic.claimnumber','ic.insuranceclaimdate','ic.status','ic.claimamount');
    public $column_search = array('i.companyname','i.policyno','ic.claimnumber','DATE_FORMAT(ic.insuranceclaimdate, "%d/%m/%Y")','ic.claimamount');
        
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
        $insurancecompany = (isset($_REQUEST['insurancecompany']))?$_REQUEST['insurancecompany']:"";
        $fromdate = $this->general_model->convertdate($_REQUEST['fromdate']);
        $todate = $this->general_model->convertdate($_REQUEST['todate']);

        $this->readdb->select("ic.id,i.companyname,i.policyno,(SELECT agentname FROM ".tbl_insuranceagent." where id=ic.insuranceagentid) as agentname,ic.claimnumber,ic.claimamount,ic.insuranceclaimdate,ic.attachment,ic.status,ic.createddate");
        $this->readdb->from($this->_table." as ic");
        $this->readdb->join(tbl_insurance." as i","i.id=ic.insuranceid","INNER");
        $this->readdb->where("i.vehicleid=".$vehicleid);
        $this->readdb->where("(i.companyname='".$insurancecompany."' OR '".$insurancecompany."'='')");
        $this->readdb->where("(ic.insuranceclaimdate BETWEEN '".$fromdate."' AND '".$todate."')");

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
