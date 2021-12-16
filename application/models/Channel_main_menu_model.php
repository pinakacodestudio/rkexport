<?php

class Channel_main_menu_model extends Common_model {

	//put your code here
	public $_table = tbl_channelmainmenu;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');
	public $column_order = array('id','name','icon','inorder');
    public $column_search = array('name','icon','inorder');

	function __construct() {
		parent::__construct();
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
	function _get_datatables_query(){  
		
		$this->readdb->select($this->_fields);
		$this->readdb->from($this->_table);
		
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
	function getMainMenuDataByRole() {

		$profileid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
		$query = $this->readdb->select($this->_fields)
							->from($this->_table)
							->where("id IN(SELECT channelmainmenuid FROM ".tbl_channelsubmenu." WHERE FIND_IN_SET(".$profileid.",submenuvisible)) OR  FIND_IN_SET(".$profileid.",submenuvisible)")
							->order_by("inorder","ASC")
							->get();

		return $query->result_array();
	}
	function getMainMenuBySlug($slug) {

		$query = $this->readdb->select("id,name,menuurl")
							->from($this->_table)
							->where("menuurl='".$slug."'")
							//->where('menu_slug LIKE "%'.$slug.'%"')
							->get();

		return $query->row_array();
	}
	function channelmainmenudata($role=0)
	{
		
		$arrSessionDetails = $this->session->userdata;        
        $member_login = isset($arrSessionDetails[base_url().'CHANNELLOGIN']) ? $arrSessionDetails[base_url().'CHANNELLOGIN'] : "";
		$this->readdb->select('*');
		$this->readdb->from($this->_table);

		if($member_login && $member_login == TRUE) {
			$profileid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
			$this->readdb->where("id IN(SELECT channelmainmenuid FROM ".tbl_channelsubmenu." WHERE find_in_set(".$profileid.",submenuvisible)) OR  find_in_set(".$profileid.",menuvisible)");
		}
		if($role==1){
			$this->readdb->where("showinrole=1");
		}
		$this->readdb->order_by("inorder", "asc");
		$query = $this->readdb->get();
		return $query->result_array();
	}
	function channelsubmenudata($role=0)
	{
		$arrSessionDetails = $this->session->userdata;        
        $member_login = isset($arrSessionDetails[base_url().'CHANNELLOGIN']) ? $arrSessionDetails[base_url().'CHANNELLOGIN'] : "";
		$this->readdb->select('*');
		$this->readdb->from(tbl_channelsubmenu);
		
		if($member_login && $member_login == TRUE) {
			$mainmenuid = $this->session->userdata(base_url().'channelmainmenuid');
			$profileid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
			$this->readdb->where("find_in_set(".$profileid.",submenuvisible)>0");
		}
		
		if($role==1){
			$this->readdb->where("showinrole=1");
		}
		$this->readdb->order_by("inorder", "asc");

		$query1 = $this->readdb->get();
		return $query1->result_array();
	}
	function channelthirdsubmenudata($role=0)
	{
		$arrSessionDetails = $this->session->userdata;        
        $member_login = isset($arrSessionDetails[base_url().'CHANNELLOGIN']) ? $arrSessionDetails[base_url().'CHANNELLOGIN'] : "";
		$this->readdb->select('*');
		$this->readdb->from(tbl_channelthirdlevelsubmenu);
		
		if($member_login && $member_login == TRUE) {
			$mainmenuid = $this->session->userdata(base_url().'channelmainmenuid');
			$profileid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
			$this->readdb->where("find_in_set(".$profileid.",thirdlevelsubmenuvisible)>0");
		}
		
		if($role==1){
			$this->readdb->where("showinrole=1");
		}
		$this->readdb->order_by("inorder", "asc");

		$query1 = $this->readdb->get();
		return $query1->result_array();
	}
}


