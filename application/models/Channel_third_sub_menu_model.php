<?php

class Channel_third_sub_menu_model extends Common_model {

	public $_table = tbl_channelthirdlevelsubmenu;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('csm.id' => 'DESC');
	public $column_order = array('csm.id','mainmenuname','csm.name','csm.url','csm.inorder');
    public $column_search = array('csm.name','((SELECT name FROM '.tbl_channelsubmenu.' WHERE id=csm.channelsubmenuid))','csm.url','csm.inorder');

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
	function _get_datatables_query($type=1){  
		
		if($type == 0){
            $this->readdb->select("s.id");
        }else{
            $this->readdb->select("csm.channelsubmenuid,csm.id,csm.url,csm.name,csm.inorder,
							(SELECT name FROM ".tbl_channelsubmenu." WHERE id=csm.channelsubmenuid) as submenuname");
        }
		$this->readdb->from($this->_table." as csm");
		
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

	function getSubMenuDataByRole() {

		$profileid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
		$query = $this->readdb->select($this->_fields)
							->from($this->_table)
							->where("FIND_IN_SET('".$profileid."',submenuvisible)>0")
							->get();
		
		return $query->result_array();
	}
	function getSubMenuBySlug($slug) {

		$query = $this->readdb->select("channelsubmenuid,id,menuurl,name,
									(SELECT name FROM ".tbl_channelsubmenu." WHERE id=submenu.channelsubmenuid) as submenuname")
							->from($this->_table)
							->where("menuurl='".$slug."'")
							->get();

		return $query->row_array();
	}
}