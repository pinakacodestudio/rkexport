<?php
class Action_log_model extends Common_model 
{
	//put your code here
	public $_table = tbl_actionlog;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'al.createddate','al.username','al.fullname','action','al.module','al.message','al.ipaddress','al.browser'); //set column field database for datatable orderable
    public $column_search = array('al.createddate','al.username','al.fullname','al.module','al.message','al.ipaddress','al.browser'); //set column field database for datatable searchable 
	public $order = array('al.id' => 'DESC'); // default order  
    
	function __construct() {
		parent::__construct();
	}
	
	function getModuleList(){

		$query = $this->readdb->query("SELECT name FROM ".tbl_mainmenu." WHERE menuurl!=''
								UNION
							SELECT name FROM ".tbl_submenu."");
	
		return $query->result_array();
	}
	function exportActionLogs(){
		
		$actiontype = (isset($_REQUEST['actiontype']))?$_REQUEST['actiontype']:'';
        $module = (!empty($_REQUEST['module']))?$_REQUEST['module']:'';
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		
		$query = $this->readdb->select("al.id,al.username,al.fullname,al.actiontype,al.module,al.message,
					al.ipaddress,al.browser,al.createddate,
					CASE 
						WHEN al.actiontype=1 THEN 'Add'
						WHEN al.actiontype=2 THEN 'Edit'
						WHEN al.actiontype=3 THEN 'Delete'
						WHEN al.actiontype=4 THEN 'View'
						ELSE '-'
					END AS action,

				")

				->from($this->_table." as al")
				->where("(DATE(al.createddate) BETWEEN '".$startdate."' AND '".$enddate."')")
				->where("(al.actiontype='".$actiontype."' OR '".$actiontype."'='')")
				->where("(FIND_IN_SET(al.module, '".$module."')>0 OR '".$module."'='')")
				->order_by("al.id","DESC")
				->get();

		return $query->result();
	}
	//LISTING DATA
	function _get_datatables_query(){
        
        $actiontype = (isset($_REQUEST['actiontype']))?$_REQUEST['actiontype']:'';
        $module = (!empty($_REQUEST['module']))?implode(",", $_REQUEST['module']):'';
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
       
		$this->readdb->select("al.id,al.username,al.fullname,al.actiontype,al.module,al.message,al.ipaddress,al.browser,al.createddate,
				CASE 
					WHEN al.actiontype=1 THEN 'Add'
					WHEN al.actiontype=2 THEN 'Edit'
					WHEN al.actiontype=3 THEN 'Delete'
					WHEN al.actiontype=4 THEN 'View'
					ELSE '-'
				END AS action,

			");

        $this->readdb->from($this->_table." as al");
		$this->readdb->where("(DATE(al.createddate) BETWEEN '".$startdate."' AND '".$enddate."')");
		$this->readdb->where("(al.actiontype='".$actiontype."' OR '".$actiontype."'='')");
		$this->readdb->where("(FIND_IN_SET(al.module, '".$module."')>0 OR '".$module."'='')");

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
?>