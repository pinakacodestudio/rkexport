<?php
class Sidenavigation_model extends CI_model{
	
function mainnav()
{
	$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
	$query = $this->db->query("SELECT * FROM ".tbl_mainmenu." WHERE id IN(SELECT mainmenuid FROM ".tbl_submenu." WHERE find_in_set(".$profileid.",submenuvisible)) OR  find_in_set(".$profileid.",menuvisible) ORDER BY inorder ASC");
	return $query->result_array();
}
function subnav()
{
	$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
	$query = $this->db->query("SELECT * FROM ".tbl_submenu." WHERE find_in_set(".$profileid.",submenuvisible) ORDER BY inorder ASC");
	return $query->result_array();
}
function subnavtabsmenu()
{
	$mainmenuid = $this->session->userdata(base_url().'mainmenuid');
	$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
	$query = $this->db->query("SELECT * FROM ".tbl_submenu." WHERE find_in_set(".$profileid.",submenuvisible) AND mainmenuid=".$mainmenuid);
	return $query->result_array();
}
function mainmenudata($role=0)
{
	$this->db->select('*');
	$this->db->from(tbl_mainmenu);
	if($role==1){
		$this->db->where("showinrole=1");
	}
	$this->db->order_by("inorder", "asc");
	$query = $this->db->get();
	return $query->result_array();
}
function submenudata($role=0)
{
	$this->db->select('*');
	$this->db->from(tbl_submenu);
	if($role==1){
		$this->db->where("showinrole=1");
	}
	$this->db->order_by("inorder", "asc");

	$query1 = $this->db->get();
	return $query1->result_array();
}
function mainmenuedit($id)
{
	$this->db->select('*');
	$this->db->from(tbl_mainmenu);
	$this->db->where('id', $id);
	$query1 = $this->db->get();
	return $query1->row_array();
}
function submenuedit($id)
{
	$this->db->select('*');
	$this->db->from(tbl_submenu);
	$this->db->where('id', $id);
	$query1 = $this->db->get();
	return $query1->row_array();
}
function submenuselect()
{
	$id = $this->session->userdata(base_url().'submenuid');
	$this->db->select('*');
	$this->db->from(tbl_submenu);
	$this->db->where('id', $id);
	$query1 = $this->db->get();
	return $query1->row_array();
}
function addmainmenu()
{
	$MainmenuName = $_REQUEST ['MainmenuName'];
	$menuicon = $_REQUEST ['menuicon'];
	$menuurl = $_REQUEST ['menuurl'];
	$inorder = $_REQUEST ['inorder'];
	$showinrole = (isset($_REQUEST ['showinrole']))?1:0;

	$query = $this->db->select('id')
						->from(tbl_mainmenu)
						->where('name',$MainmenuName)
						->get();
	if($query->num_rows()==0){
		$data=array('name'=>$MainmenuName,
			    'icon'=>$menuicon,
			    'menuurl'=>$menuurl,
			   	'menuvisible'=>',1,',
			   	'menuadd'=>',1,',
			   	'menuedit'=>',1,',
			   	'menudelete'=>',1,',
			   	'showinrole'=>$showinrole,
			   	'inorder'=>$inorder);
		$insertid = $this->db->insert(tbl_mainmenu,$data);
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

	$query = $this->db->select('id')
						->from(tbl_mainmenu)
						->where("name='".$MainmenuName."' AND id!=".$id)
						->get();
	if($query->num_rows()==0){

		$data=array('name'=>$MainmenuName,
				    'icon'=>$menuicon,
				    'menuurl'=>$menuurl,
				    'showinrole'=>$showinrole,
				   	'inorder'=>$inorder);
		
		$this->db->set($data);
		$this->db->where('id',$id);
		$this->db->update(tbl_mainmenu);
		return 1;
	}else{
		return 2;
	}
}
function mainmenuselect()
{
	$id = $this->session->userdata(base_url().'mainmenuid');
	$this->db->select('*');
	$this->db->from(tbl_mainmenu);
	$this->db->where('id', $id);
	$query1 = $this->db->get();
	return $query1->row_array();
}
function addsubmenu()
{
	$MainmenuId = $_REQUEST ['mainmenuid'];
	$SubmenuName = $_REQUEST ['SubmenuName'];
	$MenuUrl = $_REQUEST ['menuurl'];
	$inorder = $_REQUEST ['inorder'];
	$showinrole = (isset($_REQUEST ['showinrole']))?1:0;
	$query = $this->db->select('id')
						->from(tbl_submenu)
						->where(array('name'=>$SubmenuName,'mainmenuid'=>$MainmenuId))
						->get();
	if($query->num_rows()==0){
		$data=array('mainmenuid'=>$MainmenuId,
				    'name'=>$SubmenuName,
				    'url'=>$MenuUrl,
				   	'submenuvisible'=>',1,',
				   	'submenuadd'=>',1,',
				   	'submenuedit'=>',1,',
				   	'submenudelete'=>',1,',
					'showinrole'=>$showinrole,
					'inorder'=>$inorder);
		$insertid = $this->db->insert(tbl_submenu,$data);

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
	$insertid=0;
	$query = $this->db->select('id')
						->from(tbl_submenu)
						->where("name='".$SubmenuName."' AND mainmenuid=".$MainmenuId." AND id!=".$Submenuid)
						->get();
	if($query->num_rows()==0){
		$data=array('mainmenuid'=>$MainmenuId,
				    'name'=>$SubmenuName,
				    'url'=>$MenuUrl,
					'showinrole'=>$showinrole,
					'inorder'=>$inorder);
		$this->db->set($data);
		$this->db->where('id',$Submenuid);
		$this->db->update(tbl_submenu);
		return 1;
	}else{
		return 2;
	}
}
function mainmenudelete($id)
{
	$query = $this->db->query("SELECT id FROM ".tbl_mainmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_submenu." WHERE mainmenuid = $id)");
	if($query->num_rows() == 0){
		$this->db->where('id', $id);
  		$this->db->delete(tbl_mainmenu);
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
		$query = $this->db->query("SELECT id FROM ".tbl_mainmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_submenu." WHERE mainmenuid = $row)");
		if($query->num_rows() > 0){
			$count++;
		}
	}
	return $count;
}
function deletemulmainmenu()
{
	$ids = explode(",",$_REQUEST['ids']);
	foreach($ids as $row){
		$query = $this->db->query("SELECT id FROM ".tbl_mainmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_submenu." WHERE mainmenuid = $row)");
		if($query->num_rows() == 0){
			$this->db->where('id', $row);
  			$this->db->delete(tbl_mainmenu);
		}
	}
}
function submenudelete($id)
{
	$this->db->where('id', $id);
  	$this->db->delete(tbl_submenu);
	return 1;
}
function checksubmenuuse()
{
	$count = 0;
	return $count;
}
function deletemulsubmenu()
{
	$ids = explode(",",$_REQUEST['ids']);
	foreach($ids as $row){
		$this->db->where('id', $row);
  		$this->db->delete(tbl_submenu);
	}
}
function getallfrontendmenu()
{
	$this->db->select('*');
	$this->db->from(tbl_frontendmenu);
	$this->db->order_by("inorder", "asc");
	$query = $this->db->get();
	return $query->result_array();
}
function getfrontendmenuorder()
{
	$this->db->select('inorder');
	$this->db->from(tbl_frontendmenu);
	$query = $this->db->get();
	$result = $query->result_array();
	$a = ',';
	foreach($result as $row){
		$a .= $row['inorder'].',';
	}
	return $a;
}
function addfrontendmenu()
{
	$name = $_REQUEST ['name'];
	$menuicon = $_REQUEST ['menuicon'];
	$inorder = $_REQUEST ['inorder'];
	$data=array('name'=>$name,
			    'icon'=>$menuicon,
			    'inorder'=>$inorder);
	$insertid = $this->db->insert(tbl_frontendmenu,$data);
	if($insertid != 0){
		return 1;
	}else{
		return 0;
	}
}
function geteditfrontendmenu($id)
{
	$this->db->select('*');
	$this->db->from(tbl_frontendmenu);
	$this->db->where('id',$id);
	$query = $this->db->get();
	return $query->row_array();
}
function updatefrontendmenu()
{
	$frontendmenuid = $_REQUEST ['frontendmenuid'];
	$name = $_REQUEST ['name'];
	$menuicon = $_REQUEST ['menuicon'];
	$inorder = $_REQUEST ['inorder'];
	$data=array('name'=>$name,
			    'icon'=>$menuicon,
			    'inorder'=>$inorder);
	$this->db->set($data);
	$this->db->where('id',$frontendmenuid);
	$this->db->update(tbl_frontendmenu);
	return 1;
}
function deletefrontendmenu($id)
{
	$query = $this->db->query("SELECT id FROM ".tbl_frontendmenu." WHERE id IN (SELECT mainmenuid FROM frontendsubmenu WHERE mainmenuid = $id)");
	if($query->num_rows() == 0){
		$this->db->where('id', $id);
  		$this->db->delete(tbl_frontendmenu);
		return 1;
	}else{
		return 2;
	}
}
function getallfrontendsubmenu()
{
	$this->db->select('fs.id as id, fs.name as name, fs.menuurl as menuurl, fm.name as mainmenu');
	$this->db->from('frontendsubmenu fs');
	$this->db->join(tbl_frontendmenu.' fm', 'fm.id=fs.mainmenuid', 'inner');
	$this->db->order_by('fs.id','DESC');
	$data=$this->db->get();
	return $data->result_array();
}
function addfrontendsubmenu()
{
	$name = $_REQUEST ['name'];
	$mainmenu = $_REQUEST ['mainmenu'];
	$menuurl = $_REQUEST ['menuurl'];
	$data=array('mainmenuid'=>$mainmenu,
			    'name'=>$name,
			    'menuurl'=>$menuurl);
	$insertid = $this->db->insert(tbl_frontendsubmenu,$data);
	if($insertid != 0){
		return 1;
	}else{
		return 0;
	}
}
function geteditfrontendsubmenu($id)
{
	$this->db->select('*');
	$this->db->from(tbl_frontendsubmenu);
	$this->db->where('id',$id);
	$query = $this->db->get();
	return $query->row_array();
}
function updatefrontendsubmenu()
{
	$frontendmenuid = $_REQUEST ['frontendmenuid'];
	$name = $_REQUEST ['name'];
	$mainmenu = $_REQUEST ['mainmenu'];
	$menuurl = $_REQUEST ['menuurl'];
	$data=array('mainmenuid'=>$mainmenu,
			    'name'=>$name,
			    'menuurl'=>$menuurl);
	$this->db->set($data);
	$this->db->where('id',$frontendmenuid);
	$this->db->update(tbl_frontendsubmenu);
	return 1;
}
function deletefrontendsubmenu($id)
{
	$this->db->where('id', $id);
  	$this->db->delete(tbl_frontendsubmenu);
	return 1;
}
function frontendmainnav()
{
	$this->db->select('*');
	$this->db->from(tbl_frontendmenu);
	$this->db->order_by("inorder", "asc");
	$query = $this->db->get();
	return $query->result_array();
}
function frontendsubnav()
{
		$this->db->select('*');
		$this->db->from(tbl_frontendsubmenu);
		$query1 = $this->db->get();
		return $query1->result_array();
}
function checkfrontendmainmenuuse()
{
	$ids = explode(",",$_REQUEST['ids']);
	$count = 0;
	foreach($ids as $row){
		$query = $this->db->query("SELECT id FROM ".tbl_frontendmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_frontendsubmenu." WHERE mainmenuid = $row)");
		if($query->num_rows() > 0){
			$count++;
		}
	}
	return $count;
}
function deletemulfrontendmainmenu()
{
	$ids = explode(",",$_REQUEST['ids']);
	foreach($ids as $row){
		$query = $this->db->query("SELECT id FROM ".tbl_frontendmenu." WHERE id IN (SELECT mainmenuid FROM ".tbl_frontendsubmenu." WHERE mainmenuid = $row)");
		if($query->num_rows() == 0){
			$this->db->where('id', $row);
  			$this->db->delete(tbl_frontendmenu);
		}
	}
}
function checkfrontendsubmenuuse()
{
	$count = 0;
	
	return $count;
}
function deletemulfrontendsubmenu()
{
	$ids = explode(",",$_REQUEST['ids']);
	foreach($ids as $row){
		$this->db->where('id', $row);
  		$this->db->delete(tbl_frontendsubmenu);
	}
}
}