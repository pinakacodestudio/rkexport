<?php

class Transporter_model extends Common_model {

	//put your code here
	public $_table = tbl_transport;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('tr.id' => 'DESC'); // default order 
	public $column_order = array(null,'membername','tr.companyname','tr.contactperson','tr.mobile','tr.email'); //set column field database for datatable orderable
	public $column_search = array('m.membercode','m.name','tr.companyname','tr.mobile','tr.contactperson','tr.email','tr.website'); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
    }
	public function getActiveTransporterList($memberid=0){

		$query = $this->readdb->select("id,companyname")
						->from($this->_table)
						->where("(memberid = ".$memberid." OR memberid=0) AND status=1")
						->order_by("companyname ASC")
						->get();		

		return $query->result_array();			
    }
	
	function getMemberActiveExtraCharges($channelid=0,$memberid=0){
		
		//IF $channelid is 0 than get admin extra charges
		$query = $this->readdb->select("id,
		
		CONCAT(name,IF(amounttype=0,CONCAT(' (',CAST(defaultamount AS DECIMAL(14,2)),'%)'),'' )) as extrachargename,
		IFNULL((SELECT CAST(integratedtax AS DECIMAL(14,2)) FROM ".tbl_hsncode." WHERE id=ec.hsncodeid),0) as tax,
		amounttype,CAST(defaultamount AS DECIMAL(14,2)) as defaultamount")
                        ->from($this->_table." as ec")
						->where("ec.channelid = ".$channelid." AND (ec.memberid = ".$memberid." OR ec.memberid=0) AND ec.status=1")
                        ->get();

		
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
    }
    function getTransporterDataByID($ID){
        
		$query=$this->readdb->select("tr.id,
								tr.memberid,
								IFNULL(m.channelid,'0') as channelid,
								tr.companyname,
								tr.mobile,
								tr.contactperson,tr.email,tr.address,tr.cityid,tr.status,
								tr.trackingurl,tr.website")

						->from($this->_table." as tr")
						->join(tbl_member." as m","m.id=tr.memberid","LEFT")
                        ->where("tr.id", $ID)
                        ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
	}
	
	//LISTING DATA
	function _get_datatables_query(){
		
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:'';
		$memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:'0';

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		
		$this->readdb->select("tr.id,tr.memberid,tr.companyname,tr.contactperson,tr.mobile,tr.email,tr.website,tr.cityid,tr.trackingurl,tr.status,tr.type,

			IF(tr.address!='',
				CONCAT(tr.address,
				IF(tr.cityid!=0,
					CONCAT(', ',c.name,' (',p.name,'), ',cn.name),''
				)),
				
				
				IF(tr.cityid!=0,
					CONCAT(', ',c.name,' (',p.name,'), ',cn.name),''
				) 
			) as address,
		
			IFNULL(m.channelid,'0') as channelid,
		    IFNULL(CONCAT(m.name,' (',m.membercode,'%)'),'') as membername
		");
		
		$this->readdb->from($this->_table." as tr");
		$this->readdb->join(tbl_member." as m","m.id=tr.memberid","LEFT");
		$this->readdb->join(tbl_city." as c","c.id=tr.cityid","LEFT");
		$this->readdb->join(tbl_province." as p","p.id=c.stateid","LEFT");
		$this->readdb->join(tbl_country." as cn","cn.id=p.countryid","LEFT");
		
		if(!is_null($MEMBERID)){
			$this->readdb->where("tr.memberid = ".$MEMBERID);
		}
		if($channelid!=""){
			if(!empty($channelid)){
				$this->readdb->where("(FIND_IN_SET(tr.memberid,(SELECT GROUP_CONCAT(id) FROM ".tbl_member." WHERE channelid = ".$channelid."))>0)");
			}else{
				$this->readdb->where("tr.memberid=0");
			}
		}
		if($memberid!=0){
			$this->readdb->where("(tr.memberid = ".$memberid." OR ".$memberid."=0)");
		}
		// $this->readdb->where("(tr.memberid = ".$memberid." OR ".$memberid."=0)");
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
		// echo $this->readdb->last_query(); exit;
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
