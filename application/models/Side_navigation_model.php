<?php
class Side_navigation_model extends Common_model{

	public $_table = tbl_mainmenu;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order;

    function __construct() {
        parent::__construct();
    }
	
	function mainnav()
	{
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		//echo $profileid;exit;
		$query = $this->readdb->query("SELECT * FROM ".tbl_mainmenu." WHERE (id IN(SELECT mainmenuid FROM ".tbl_submenu." WHERE find_in_set(".$profileid.",submenuvisible)>0) OR  find_in_set(".$profileid.",menuvisible)>0) AND
		
		(IF(".CRM."=0,(name != 'CRM'),0=0)) ORDER BY inorder ASC");
		return $query->result_array();
	}
	function subnav()
	{
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		$query = $this->readdb->query("SELECT * FROM ".tbl_submenu." WHERE (id IN(SELECT submenuid FROM ".tbl_thirdlevelsubmenu." WHERE find_in_set(".$profileid.",thirdlevelsubmenuvisible)>0) OR find_in_set('".$profileid."',submenuvisible)>0) AND
		
		(IF(".CRM."=0,mainmenuid NOT IN (SELECT id FROM ".tbl_mainmenu." WHERE name = 'CRM'),0=0)) ORDER BY inorder ASC");

		return $query->result_array();
	}
	function thirdlevelsubnav()
	{
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		$query = $this->readdb->query("SELECT * FROM ".tbl_thirdlevelsubmenu." WHERE find_in_set('".$profileid."',thirdlevelsubmenuvisible)>0 ORDER BY inorder ASC");

		return $query->result_array();
	}
	function subnavtabsmenu()
	{
		$mainmenuid = $this->session->userdata(base_url().'mainmenuid');
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		$query = $this->readdb->query("SELECT * FROM ".tbl_submenu." WHERE find_in_set('".$profileid."',submenuvisible) AND mainmenuid=".$mainmenuid." ORDER BY inorder ASC");
		return $query->result_array();
	}
	function thirdlevelsubnavtabsmenu()
	{
		$mainmenuid = $this->session->userdata(base_url().'mainmenuid');
		$submenuid = $this->session->userdata(base_url().'submenuid');
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		$query = $this->readdb->query("SELECT * FROM ".tbl_thirdlevelsubmenu." WHERE find_in_set('".$profileid."',thirdlevelsubmenuvisible) AND (SELECT mainmenuid FROM ".tbl_submenu." where id='".$submenuid."')='".$mainmenuid."' ORDER BY inorder ASC");
		return $query->result_array();
	}
	
	function mainmenudata($role=0)
	{
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		
		$this->readdb->select('*');
		$this->readdb->from(tbl_mainmenu);
		$this->readdb->where("(id IN(SELECT mainmenuid FROM ".tbl_submenu." WHERE find_in_set('".$profileid."',submenuvisible)>0) OR  find_in_set('".$profileid."',menuvisible)>0)");
		if($role==1){
			$this->readdb->where("showinrole=1");
		}
		$this->readdb->order_by("inorder", "asc");
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}
	function submenudata($role=0)
	{
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		
		$this->readdb->select('s.*,(SELECT m.name FROM '.tbl_mainmenu.' as m WHERE m.id=s.mainmenuid) as mainmenuname');
		$this->readdb->from(tbl_submenu." as s");
		$this->readdb->where("(s.id IN(SELECT submenuid FROM ".tbl_thirdlevelsubmenu." WHERE find_in_set('".$profileid."',thirdlevelsubmenuvisible)>0) OR find_in_set('".$profileid."',s.submenuvisible)>0) AND IF(".CRM."=0,mainmenuid NOT IN (SELECT id FROM ".tbl_mainmenu." WHERE name = 'CRM'),'1=1')");
		if($role==1){
			$this->readdb->where("s.showinrole=1");
		}
		$this->readdb->order_by("s.inorder", "asc");

		$query1 = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		return $query1->result_array();
	}
	function thirdlevelsubmenudata($role=0)
	{
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
		
		$this->readdb->select('*');
		$this->readdb->from(tbl_thirdlevelsubmenu);
		$this->readdb->where("(find_in_set('".$profileid."',thirdlevelsubmenuvisible)>0) AND IF(".CRM."=0,(SELECT mainmenuid FROM ".tbl_submenu." WHERE id=submenuid) NOT IN (SELECT id FROM ".tbl_mainmenu." WHERE name = 'CRM'),'1=1')");
		if($role==1){
			$this->readdb->where("showinrole=1");
		}
		$this->readdb->order_by("inorder", "asc");

		$query1 = $this->readdb->get();
		return $query1->result_array();
	}
	function mainmenuedit($id)
	{
		$this->readdb->select('*');
		$this->readdb->from(tbl_mainmenu);
		$this->readdb->where('id', $id);
		$query1 = $this->readdb->get();
		return $query1->row_array();
	}
	function submenuedit($id)
	{
		$this->readdb->select('*');
		$this->readdb->from(tbl_submenu);
		$this->readdb->where('id', $id);
		$query1 = $this->readdb->get();
		return $query1->row_array();
	}
	function thirdlevelsubmenuedit($id)
	{
		$this->readdb->select('*');
		$this->readdb->from(tbl_thirdlevelsubmenu);
		$this->readdb->where('id', $id);
		$query1 = $this->readdb->get();
		return $query1->row_array();
	}
	function submenuselect()
	{
		$id = $this->session->userdata(base_url().'submenuid');
		$this->readdb->select('*');
		$this->readdb->from(tbl_submenu);
		$this->readdb->where('id', $id);
		$query1 = $this->readdb->get();
		return $query1->row_array();
	}
	function thirdlevelsubmenuselect()
	{
		$id = $this->session->userdata(base_url().'thirdlevelsubmenuid');
		$this->readdb->select('*');
		$this->readdb->from(tbl_thirdlevelsubmenu);
		$this->readdb->where('id', $id);
		$query1 = $this->readdb->get();
		return $query1->row_array();
	}
	

	function addmainmenu()
	{
		$this->load->model('Side_navigation_model', 'Side_navigation');
		$this->Side_navigation->_table = tbl_mainmenu;

		$MainmenuName = $_REQUEST ['MainmenuName'];
		$menuicon = $_REQUEST ['menuicon'];
		$menuurl = $_REQUEST ['menuurl'];
		$inorder = $_REQUEST ['inorder'];
		$showinrole = (isset($_REQUEST ['showinrole']))?1:0;
		$managelog = (isset($_REQUEST ['managelog']))?1:0;
		$approvallevel = (isset($_REQUEST ['approvallevel']))?1:0;
		$additionalrights = isset($_REQUEST['rightsid'])?implode(",",$_REQUEST['rightsid']):'';

		/* $mainmenuvisibleinrole = (isset($_REQUEST ['mainmenuvisibleinrole']))?1:0;
		$mainmenuaddinrole = (isset($_REQUEST ['mainmenuaddinrole']))?1:0;
		$mainmenueditinrole = (isset($_REQUEST ['mainmenueditinrole']))?1:0;
		$mainmenudeleteinrole = (isset($_REQUEST ['mainmenudeleteinrole']))?1:0; */

		$this->Side_navigation->_where = array('name'=>$MainmenuName);
		$Count = $this->Side_navigation->CountRecords();
		if($Count==0){
			$data=array('name'=>$MainmenuName,
						'icon'=>$menuicon,
						'menuurl'=>$menuurl,
						'menuvisible'=>',1,',
						'menuadd'=>',1,',
						'menuedit'=>',1,',
						'menudelete'=>',1,',
						'showinrole'=>$showinrole,
						'additionalrights'=>$additionalrights,
						'managelog'=>$managelog,
						'approvallevel'=>$approvallevel,
						'inorder'=>$inorder);
			$insertid = $this->Side_navigation->Add($data);
			if($insertid != 0){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 2;
		}				
		
	}
	function updatemainmenu()
	{
		$id = $_REQUEST ['mainmenuid'];
		$MainmenuName = $_REQUEST ['MainmenuName'];
		$menuicon = $_REQUEST ['menuicon'];
		$menuurl = $_REQUEST ['menuurl'];
		$inorder = $_REQUEST ['inorder'];
		$showinrole = (isset($_REQUEST ['showinrole']))?1:0;
		$managelog = (isset($_REQUEST ['managelog']))?1:0;
		$approvallevel = (isset($_REQUEST ['approvallevel']))?1:0;
		$additionalrights = isset($_REQUEST['rightsid'])?implode(",",$_REQUEST['rightsid']):'';
		
		$this->load->model("Side_navigation_model","Side_navigation");
		$this->Side_navigation->_table = tbl_mainmenu;

		/* $mainmenuvisibleinrole = (isset($_REQUEST ['mainmenuvisibleinrole']))?1:0;
		$mainmenuaddinrole = (isset($_REQUEST ['mainmenuaddinrole']))?1:0;
		$mainmenueditinrole = (isset($_REQUEST ['mainmenueditinrole']))?1:0;
		$mainmenudeleteinrole = (isset($_REQUEST ['mainmenudeleteinrole']))?1:0; */
		$this->Side_navigation->_where = "name='".$MainmenuName."' AND id!=".$id;
		$Count = $this->Side_navigation->CountRecords();
		
		if($Count==0){

			$data=array('name'=>$MainmenuName,
						'icon'=>$menuicon,
						'menuurl'=>$menuurl,
						'showinrole'=>$showinrole,
						'managelog'=>$managelog,
						'additionalrights'=>$additionalrights,
						'approvallevel'=>$approvallevel,
						'inorder'=>$inorder);
			
			$this->Side_navigation->_where = array('id'=>$id);
			$this->Side_navigation->Edit($data);
			
			return 1;
		}else{
			return 2;
		}
	}
	function mainmenuselect()
	{
		$id = $this->session->userdata(base_url().'mainmenuid');
		$this->readdb->select('*');
		$this->readdb->from(tbl_mainmenu);
		$this->readdb->where('id', $id);
		$query1 = $this->readdb->get();
		return $query1->row_array();
	}
	function addsubmenu()
	{
		$this->load->model('Side_navigation_model', 'Side_navigation');
		$this->Side_navigation->_table = tbl_submenu;

		$MainmenuId = $_REQUEST ['mainmenuid'];
		$SubmenuName = $_REQUEST ['SubmenuName'];
		$MenuUrl = $_REQUEST ['menuurl'];
		$inorder = $_REQUEST ['inorder'];
		$showinrole = (isset($_REQUEST ['showinrole']))?1:0;
		$managelog = (isset($_REQUEST ['managelog']))?1:0;
		$approvallevel = (isset($_REQUEST ['approvallevel']))?1:0;
		$additionalrights = isset($_REQUEST['rightsid'])?implode(",",$_REQUEST['rightsid']):'';

		$submenuvisibleinrole = (isset($_REQUEST ['submenuvisibleinrole']))?1:0;
		$submenuaddinrole = (isset($_REQUEST ['submenuaddinrole']))?1:0;
		$submenueditinrole = (isset($_REQUEST ['submenueditinrole']))?1:0;
		$submenudeleteinrole = (isset($_REQUEST ['submenudeleteinrole']))?1:0;

		$this->Side_navigation->_where = array('name'=>$SubmenuName,'mainmenuid'=>$MainmenuId);
		$Count = $this->Side_navigation->CountRecords();

		if($Count==0){
			$data=array('mainmenuid'=>$MainmenuId,
						'name'=>$SubmenuName,
						'url'=>$MenuUrl,
						'submenuvisible'=>',1,',
						'submenuadd'=>',1,',
						'submenuedit'=>',1,',
						'submenudelete'=>',1,',
						'submenuvisibleinrole'=>$submenuvisibleinrole,
						'submenuaddinrole'=>$submenuaddinrole,
						'submenueditinrole'=>$submenueditinrole,
						'submenudeleteinrole'=>$submenudeleteinrole,   
						'additionalrights'=>$additionalrights,
						'showinrole'=>$showinrole,
						'managelog'=>$managelog,
						'approvallevel'=>$approvallevel,
						'inorder'=>$inorder);
			$insertid = $this->Side_navigation->Add($data);

			if($insertid != 0){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 2;
		}
	}
	function addthirdlevelsubmenu()
	{
		$this->load->model('Side_navigation_model', 'Side_navigation');
		$this->Side_navigation->_table = tbl_thirdlevelsubmenu;

		$submenuid = $_REQUEST ['submenuid'];
		$SubmenuName = $_REQUEST ['SubmenuName'];
		$MenuUrl = $_REQUEST ['menuurl'];
		$inorder = $_REQUEST ['inorder'];
		$showinrole = (isset($_REQUEST ['showinrole']))?1:0;
		$managelog = (isset($_REQUEST ['managelog']))?1:0;
		$approvallevel = (isset($_REQUEST ['approvallevel']))?1:0;
		$additionalrights = isset($_REQUEST['rightsid'])?implode(",",$_REQUEST['rightsid']):'';

		$submenuvisibleinrole = (isset($_REQUEST ['submenuvisibleinrole']))?1:0;
		$submenuaddinrole = (isset($_REQUEST ['submenuaddinrole']))?1:0;
		$submenueditinrole = (isset($_REQUEST ['submenueditinrole']))?1:0;
		$submenudeleteinrole = (isset($_REQUEST ['submenudeleteinrole']))?1:0;

		$this->Side_navigation->_where = array('name'=>$SubmenuName,'submenuid'=>$submenuid);
		$Count = $this->Side_navigation->CountRecords();

		if($Count==0){
			$data=array('submenuid'=>$submenuid,
						'name'=>$SubmenuName,
						'url'=>$MenuUrl,
						'thirdlevelsubmenuvisible'=>',1,',
						'thirdlevelsubmenuadd'=>',1,',
						'thirdlevelsubmenuedit'=>',1,',
						'thirdlevelsubmenudelete'=>',1,',
						'thirdlevelsubmenuvisibleinrole'=>$submenuvisibleinrole,
						'thirdlevelsubmenuaddinrole'=>$submenuaddinrole,
						'thirdlevelsubmenueditinrole'=>$submenueditinrole,
						'thirdlevelsubmenudeleteinrole'=>$submenudeleteinrole,   
						'additionalrights'=>$additionalrights,
						'showinrole'=>$showinrole,
						'managelog'=>$managelog,
						'approvallevel'=>$approvallevel,
						'inorder'=>$inorder);
			$insertid = $this->Side_navigation->Add($data);

			if($insertid != 0){
				return 1;
			}else{
				return 0;
			}
		}else{
			return 2;
		}
	}
	function updatesubmenu()
	{
		$Submenuid = $_REQUEST ['submenuid'];
		$MainmenuId = $_REQUEST ['mainmenuid'];
		$SubmenuName = $_REQUEST ['SubmenuName'];
		$MenuUrl = $_REQUEST ['menuurl'];
		$inorder = $_REQUEST ['inorder'];
		$showinrole = (isset($_REQUEST ['showinrole']))?1:0;
		$managelog = (isset($_REQUEST ['managelog']))?1:0;
		$approvallevel = (isset($_REQUEST ['approvallevel']))?1:0;
		$additionalrights = isset($_REQUEST['rightsid'])?implode(",",$_REQUEST['rightsid']):'';

		$submenuvisibleinrole = (isset($_REQUEST ['submenuvisibleinrole']))?1:0;
		$submenuaddinrole = (isset($_REQUEST ['submenuaddinrole']))?1:0;
		$submenueditinrole = (isset($_REQUEST ['submenueditinrole']))?1:0;
		$submenudeleteinrole = (isset($_REQUEST ['submenudeleteinrole']))?1:0;

		$this->load->model("Side_navigation_model","Side_navigation");
		$this->Side_navigation->_table = tbl_submenu;

		$insertid=0;
		$this->Side_navigation->_where = "name='".$SubmenuName."' AND mainmenuid=".$MainmenuId." AND id!=".$Submenuid;
		$Count = $this->Side_navigation->CountRecords();

		if($Count==0){
			$data=array('mainmenuid'=>$MainmenuId,
						'name'=>$SubmenuName,
						'url'=>$MenuUrl,
						'submenuvisibleinrole'=>$submenuvisibleinrole,
						'submenuaddinrole'=>$submenuaddinrole,
						'submenueditinrole'=>$submenueditinrole,
						'submenudeleteinrole'=>$submenudeleteinrole,  
						'additionalrights'=>$additionalrights,
						'showinrole'=>$showinrole,
						'managelog'=>$managelog,
						'approvallevel'=>$approvallevel,
						'inorder'=>$inorder);

			$this->Side_navigation->_where = array('id'=>$Submenuid);
			$this->Side_navigation->Edit($data);

			return 1;
		}else{
			return 2;
		}
	}
	function updatethirdlevelsubmenu()
	{
		$id = $_REQUEST ['id'];
		$Submenuid = $_REQUEST ['submenuid'];
		$SubmenuName = $_REQUEST ['SubmenuName'];
		$MenuUrl = $_REQUEST ['menuurl'];
		$inorder = $_REQUEST ['inorder'];
		$showinrole = (isset($_REQUEST ['showinrole']))?1:0;
		$managelog = (isset($_REQUEST ['managelog']))?1:0;
		$approvallevel = (isset($_REQUEST ['approvallevel']))?1:0;
		$additionalrights = isset($_REQUEST['rightsid'])?implode(",",$_REQUEST['rightsid']):'';

		$submenuvisibleinrole = (isset($_REQUEST ['submenuvisibleinrole']))?1:0;
		$submenuaddinrole = (isset($_REQUEST ['submenuaddinrole']))?1:0;
		$submenueditinrole = (isset($_REQUEST ['submenueditinrole']))?1:0;
		$submenudeleteinrole = (isset($_REQUEST ['submenudeleteinrole']))?1:0;

		$this->load->model("Side_navigation_model","Side_navigation");
		$this->Side_navigation->_table = tbl_thirdlevelsubmenu;

		$insertid=0;
		$this->Side_navigation->_where = "name='".$SubmenuName."' AND submenuid=".$Submenuid." AND id!=".$id;
		$Count = $this->Side_navigation->CountRecords();

		if($Count==0){
			$data=array('submenuid'=>$Submenuid,
						'name'=>$SubmenuName,
						'url'=>$MenuUrl,
						'thirdlevelsubmenuvisibleinrole'=>$submenuvisibleinrole,
						'thirdlevelsubmenuaddinrole'=>$submenuaddinrole,
						'thirdlevelsubmenueditinrole'=>$submenueditinrole,
						'thirdlevelsubmenudeleteinrole'=>$submenudeleteinrole,  
						'additionalrights'=>$additionalrights,
						'showinrole'=>$showinrole,
						'managelog'=>$managelog,
						'approvallevel'=>$approvallevel,
						'inorder'=>$inorder);

			$this->Side_navigation->_where = array('id'=>$id);
			$this->Side_navigation->Edit($data);

			return 1;
		}else{
			return 2;
		}
	}
	function mainmenudelete($id)
	{
		$this->load->model("Side_navigation_model","Side_navigation");
		$this->Side_navigation->_table = tbl_mainmenu;

		$query = $this->readdb->query("SELECT id FROM ".tbl_mainmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_submenu." WHERE mainmenuid = $id)");
		if($query->num_rows() == 0){
			$this->Side_navigation->Delete(array('id'=>$id));
			return 1;
		}else{
			return 2;
		}
	}
	function checkmainmenuuse()
	{
		$ids = explode(",",$_REQUEST['ids']);
		$count = 0;
		foreach($ids as $row){
			$query = $this->readdb->query("SELECT id FROM ".tbl_mainmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_submenu." WHERE mainmenuid = $row)");
			if($query->num_rows() > 0){
				$count++;
			}
		}
		return $count;
	}
	function deletemulmainmenu($managelog)
	{
		$this->load->model("Side_navigation_model","Side_navigation");
		$this->Side_navigation->_table = tbl_mainmenu;

		$ids = explode(",",$_REQUEST['ids']);
		foreach($ids as $row){
			$query = $this->readdb->query("SELECT id FROM ".tbl_mainmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_submenu." WHERE mainmenuid = $row)");
			if($query->num_rows() == 0){

				if($managelog == 1){

					$this->Side_navigation->_where = array("id"=>$row);
					$data = $this->Side_navigation->getRecordsById();
					
					$this->general_model->addActionLog(3,'Main Menu','Delete '.$data['name'].' main menu.');
				}
				$this->Side_navigation->Delete(array('id'=>$row));
			}
		}
	}
	function submenudelete($id)
	{
		$this->load->model("Side_navigation_model","Side_navigation");
		$this->Side_navigation->_table = tbl_submenu;
		$this->Side_navigation->Delete(array('id'=>$id));

		return 1;
	}
	function checksubmenuuse()
	{
		$ids = explode(",", $_REQUEST['ids']);
		$count = 0;
		foreach ($ids as $row) {
			$query = $this->readdb->query("SELECT id FROM " . tbl_submenu . " WHERE id IN (SELECT submenuid FROM " . tbl_thirdlevelsubmenu . " WHERE submenuid = $row)");
			if ($query->num_rows() > 0) {
				$count++;
			}
		}
		return $count;
	}
	function deletemulsubmenu($managelog)
	{
		$this->load->model("Side_navigation_model", "Side_navigation");
		$this->Side_navigation->_table = tbl_submenu;

		$ids = explode(",", $_REQUEST['ids']);
		foreach ($ids as $row) {

			$query = $this->readdb->query("SELECT id FROM " . tbl_submenu . " WHERE id IN (SELECT submenuid FROM " . tbl_thirdlevelsubmenu . " WHERE submenuid = $row)");
			if ($query->num_rows() == 0) {

				if ($managelog == 1) {

					$this->Side_navigation->_where = array("id" => $row);
					$data = $this->Side_navigation->getRecordsById();

					$this->general_model->addActionLog(3, 'Sub Menu', 'Delete ' . $data['name'] . ' sub menu.');
				}
				$this->Side_navigation->Delete(array('id' => $row));
			}
		}
	}
	function checkthirdlevelsubmenuuse()
	{
		$count = 0;
		return $count;
	}
	function deletemulthirdlevelsubmenu($managelog)
	{
		$this->load->model("Side_navigation_model","Side_navigation");
		$this->Side_navigation->_table = tbl_thirdlevelsubmenu;

		$ids = explode(",",$_REQUEST['ids']);
		foreach($ids as $row){

			if($managelog == 1){

				$this->Side_navigation->_where = array("id"=>$row);
				$data = $this->Side_navigation->getRecordsById();
				
				$this->general_model->addActionLog(3,'Third Level Sub Menu','Delete '.$data['name'].'third level sub menu.');
			}
			$this->Side_navigation->Delete(array('id'=>$row));
		}
	}
	
	function getsubmenubymainmenuname($name)
	{
		$this->readdb->select('*');
		$this->readdb->from(tbl_submenu);
		$this->readdb->where('mainmenuid IN (SELECT id FROM '.tbl_mainmenu.' WHERE name="'.$name.'")');
		$query1 = $this->readdb->get();

		return $query1->result_array();
	}
}