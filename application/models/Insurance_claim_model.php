<?php

class Insurance_claim_model extends Common_model {

    //put your code here
    public $_table = tbl_insuranceclaim;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = array('ic.id' => 'DESC');
    
    public $column_order = array(null, 'v.vehiclename', 'i.companyname', 'i.policyno', 'ic.insuranceclaimdate', 'ic.billnumber', 'ic.billamount', 'ic.claimnumber', 'ic.claimamount','ic.status','ic.createddate');

    public $column_search = array("DATE_FORMAT(ic.insuranceclaimdate, '%d/%m/%Y')", 'v.vehiclename', 'v.vehicleno', 'i.companyname', 'i.policyno', 'ic.billnumber', 'ic.billamount', 'ic.claimnumber', 'ic.claimamount','DATE_FORMAT(ic.createddate , "%d %b %Y %H:%i %p")');
    
    function __construct() {
        parent::__construct();
    }

    function getvehicleinsuranceclaimDataByID($ID) {
        
        $query = $this->readdb->select("vic.id,vic.insuranceid,vi.vehicleid,vic.insuranceclaimdate,vic.insuranceagentid,vic.billnumber,vic.billamount,vic.attachment,vic.claimnumber,vic.claimamount,vic.status,vi.companyname,vi.policyno,v.vehiclename,v.vehicleno")
            ->from($this->_table . " as vic")
            ->where("vic.id", $ID)
            ->join(tbl_insurance . " as vi", "vi.id = vic.insuranceid", "LEFT")
            ->join(tbl_vehicle . " as v", "v.id = vi.vehicleid", "LEFT")
            ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return 0;
        }
    }

    function getInsuranceClaimFileByID($ids){

		$query = $this->readdb->select('id,file')
				->from(tbl_insuranceclaimdocument)
				->where("id IN (".$ids.")")
				->get();

		return $query->result_array();
    }
    
    function getInsuranceClaimFileDataByID($ID) {
        
        $query = $this->readdb->select("icd.id,icd.insuranceclaimid,icd.file")
            ->from(tbl_insuranceclaimdocument . " as icd")
            ->where("icd.insuranceclaimid", $ID)
            ->get();

            return $query->result_array();
    }
    function get_datatables(){
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            //echo $this->db->last_query();
            return $query->result();
        }
    }

    function _get_datatables_query($type = 1){

        $companyid = $_REQUEST['companyid'];
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        
        if ($type == 0) {
            $this->readdb->select("ic.id");
        } else {
            $this->readdb->select("ic.id,ic.insuranceid,ic.insuranceclaimdate,ic.billnumber,ic.billamount,ic.claimnumber,ic.claimamount,ic.status,i.vehicleid,v.vehiclename,v.vehicleno,i.companyname,i.policyno,ic.status,ic.createddate,
                                    IFNULL((SELECT icd.file FROM ".tbl_insuranceclaimdocument." as icd WHERE icd.insuranceclaimid=ic.id LIMIT 1),'') as attachment");
        }
        $this->readdb->from($this->_table." as ic");
        $this->readdb->join(tbl_insurance." as i", "i.id = ic.insuranceid", "LEFT");
        $this->readdb->join(tbl_vehicle." as v", "v.id = i.vehicleid", "LEFT");

        $where = '';
        if ($companyid != 0) {
            if (is_array($companyid)) {
                $companyid = implode(",", $companyid);
            }
            $this->readdb->where("ic.insuranceid IN (".$companyid.")");
        }
        $this->readdb->where("(ic.insuranceclaimdate BETWEEN '".$startdate."' AND '".$enddate."')");

        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->readdb->like($item, $_POST['search']['value']);
                } else {
                    $this->readdb->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->readdb->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->_order)) {
            $order = $this->_order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    function count_all()
    {
        $this->_get_datatables_query(0);
        return $this->readdb->count_all_results();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }
    function getVehicle(){
        
        $query = $this->readdb->select("id,vehiclename,vehicleno")
            ->from(tbl_vehicle)
            ->where("status = 1 AND id IN (SELECT vehicleid from " . tbl_insurance . ")")
            ->get();

        return $query->result_array();
    }

    function getinsuranceclaimpolicybycompanyname($companyname)
    {
        $query = $this->readdb->select("vic.id,vic.policyno")
            ->from(tbl_insurance . " as vic")
            ->where("vic.companyname", $companyname)
            ->get();

        return $query->result_array();
    }

    function getInsuranceCompanyOnClaim() {
        
        $query = $this->readdb->select("id,companyname")
            ->from(tbl_insurance)
            ->where("id IN (SELECT insuranceid FROM ".tbl_insuranceclaim.")")
            ->get();

        return $query->result_array();
    }

    function getInsuranceClaimDataForExport()
    {
        $this->readdb->select("vic.id,vic.insuranceid,vic.insuranceclaimdate,vic.billnumber,(SELECT agentname FROM ".tbl_insuranceagent." where id=vic.insuranceagentid) as agentname,vic.billamount,vic.claimnumber,vic.claimamount,vic.status,CONCAT(v.vehiclename,' (',v.vehicleno,')') as vehiclename,vi.companyname,vi.policyno,vi.createddate");
        $this->readdb->from($this->_table . " as vic");
        $this->readdb->join(tbl_insurance . " as vi", "vi.id = vic.insuranceid", "LEFT");
        $this->readdb->join(tbl_vehicle . " as v", "v.id = vi.vehicleid", "LEFT");
        $this->readdb->group_by("vic.id");
        $this->readdb->order_by("vic.id","DESC");
        $query = $this->readdb->get();
        return $query->result();
    }

    
}
