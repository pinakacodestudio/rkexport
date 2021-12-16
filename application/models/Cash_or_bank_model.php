<?php

class Cash_or_bank_model extends Common_model {

	//put your code here
	public $_table = tbl_cashorbank;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $order = array('cb.id' => 'DESC'); // default order 
	public $column_order = array(null,'cb.accountno','bankname','cb.branchname',null,'cb.ifsccode','cb.micrcode','cb.openingbalance',"cb.defaultbank"); //set column field database for datatable orderable
	public $column_search = array('cb.name','cb.accountno','cb.ifsccode','cb.micrcode','cb.openingbalance','cb.branchname','cb.branchaddress','(IF(cb.defaultbank=1,"YES","No"))'); //set column field database for datatable searchable 

	function __construct() {
		parent::__construct();
    }
	
	function getBankAccountsByMember($memberid){
        
        $query=$this->readdb->select("id,IF(LOWER(name)='cash',name,CONCAT(name,' (',accountno,')')) as bankname")
                            ->from($this->_table)
                            ->where("memberid=".$memberid." AND status=1")
                            ->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        }else {
            return array();
        }	
	}
	
    function getCashOrBankDataById($ID){
        
        $query=$this->readdb->select("id,memberid,openingbalance,name as bankname,openingbalancedate,accountno,branchname,branchaddress,ifsccode,micrcode,defaultbank,status")
                            ->from($this->_table)
                            ->where("id", $ID)
                            ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        }else {
            return array();
        }	
    }

	//LISTING DATA
	function _get_datatables_query(){
        
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        $memberid = (!is_null($MEMBERID))?$MEMBERID:0;

		$this->readdb->select("cb.id,cb.memberid,cb.name as bankname,cb.defaultbank,cb.openingbalance,cb.openingbalancedate,cb.accountno,cb.branchname,cb.branchaddress,cb.ifsccode,cb.micrcode,cb.status,
            IFNULL(m.channelid,'0') as channelid,
            IFNULL(CONCAT(m.name,' (',m.membercode,'%)'),'') as membername,
        ");
		$this->readdb->from($this->_table." as cb");
		$this->readdb->join(tbl_member." as m","m.id=cb.memberid","LEFT");
        $this->readdb->where("cb.memberid=".$memberid);
        
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

	function getDefaultBankAccount($memberid=0){
        
		$query=$this->readdb->select("id")
			->from($this->_table)
			->where("memberid=".$memberid." AND defaultbank=1")
			->get();

		$defaultbank = $query->row_array();

		return $defaultbank['id'];
	}
}