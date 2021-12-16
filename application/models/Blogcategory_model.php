<?php

class Blogcategory_model extends Common_model {

	//put your code here
	public $_table = tbl_blogcategory;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	function getBlogcategory(){
		$query = $this->readdb->select("bc.id,bc.name,bc.priority")
						->from($this->_table." as bc")						
						->order_by("bc.priority ASC")
						->get();
		return $query->result_array();				
	}	
	function searchblogcategory($type,$search){

		$this->db->select("id,name as text");
		$this->db->from($this->_table);
		if($type==1){
			$this->db->where("name LIKE '%".$search."%' AND status=1");
		}else{
			$this->db->where("id=".$search." AND status=1");
		}
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			if($type==1){
				return $query->result_array();
			}else{
				return $query->row_array();
			}
		}else {
			return 0;
		}	
	}
	public function getActiveBlogCategoryListOnFront($channelid=0,$memberid=0){

		$query = $this->db->select("bc.id,bc.name,COUNT(b.id) as count,bc.slug")
							->from($this->_table." as bc")
							->join(tbl_blog." as b","b.blogcategoryid=bc.id AND b.status=1","INNER")
							->where("bc.status=1 AND bc.channelid='".$channelid."' AND bc.memberid='".$memberid."'")
							->group_by("bc.id")
							->order_by("priority ASC")
							->limit(BLOG_CATEGORY_LIMIT)
							->get();		

		return $query->result_array();			
	}
	function CheckDuplicateValue($name,$id='')
	{
	    if (isset($id) && $id != '') {
		  $query = $this->readdb->query("SELECT name FROM ".tbl_blogcategory." WHERE name ='".$name."' AND id <> '".$id."'");
	    }else{
		  $query = $this->readdb->query("SELECT name FROM ".tbl_blogcategory." WHERE name ='".$name."'");
	    }
	   
	    if($query->num_rows()  > 0){
		  return 0;
	    }
	    else{
		  return 1;
	    }
	}
	
	
}
