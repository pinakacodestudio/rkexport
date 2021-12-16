<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Approval_levels_model extends Common_model {

	public $_table = tbl_approvallevels;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('al.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'module',null,'al.createddate','addedbyname');

	//set column field database for datatable searchable 
	public $column_search = array("((IF(submenuid>0,CONCAT(sm.name,' (',mm.name,')'),mm.name)))",'al.createddate',"((SELECT name FROM ".tbl_user." WHERE id=al.addedby))");

	function __construct() {
		parent::__construct();
    }
    
    function getApprovalLevelsModuleList(){

		$query = $this->readdb->query("SELECT id,name, id as ids FROM ".tbl_mainmenu." WHERE menuurl!='' AND approvallevel=1
								UNION
							SELECT id,CONCAT(name,' (',(SELECT name FROM ".tbl_mainmenu." WHERE id=mainmenuid),')') as name,concat((SELECT id FROM ".tbl_mainmenu." WHERE id=mainmenuid),'|',id) as ids FROM ".tbl_submenu." WHERE approvallevel=1");
	
		return $query->result_array();
    }
    
	function getApprovalLevelsDataByID($ID){
       
		$query = $this->readdb->select("id,mainmenuid,submenuid,channelid,memberid,netprice,status,
		CONCAT(mainmenuid,IF(submenuid>0,CONCAT('|',submenuid),'')) as moduleids")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}
	}
	function getApprovalLevelsMappingDataByApprovalLevelID($ApprovalLevelID){
       
		$query = $this->readdb->select("alm.id,alm.level,alm.designation,alm.isenable,alm.sendemail,
				IF(al.submenuid>0,CONCAT((SELECT name FROM ".tbl_submenu." WHERE id=al.submenuid),' (',(SELECT name FROM ".tbl_mainmenu." WHERE id=al.mainmenuid),')'),(SELECT name FROM ".tbl_mainmenu." WHERE id=al.mainmenuid)) as modulename
			")

							->from(tbl_approvallevelsmapping." as alm")
							->join(tbl_approvallevels." as al","al.id=alm.approvallevelsid","INNER")
							->where("alm.approvallevelsid='".$ApprovalLevelID."'")
							->order_by("alm.level ASC")
							->get();
							
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}
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
		
        $this->readdb->select("al.id,al.mainmenuid,al.submenuid,al.channelid,al.memberid,al.netprice,al.status,al.createddate,
								IF(submenuid>0,CONCAT(sm.name,' (',mm.name,')'),mm.name) as module,

								(SELECT name FROM ".tbl_user." WHERE id=al.addedby) as addedbyname,
								'' as detail

        	");
		$this->readdb->from($this->_table." as al");
		$this->readdb->join(tbl_mainmenu." as mm","mm.id=al.mainmenuid","INNER");
		$this->readdb->join(tbl_submenu." as sm","sm.id=al.submenuid","LEFT");
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
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
 ?>            
