<?php

class Blog_model extends Common_model {

	//put your code here
	public $_table = tbl_blog;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	function getBloglist($channelid=0,$memberid=0){

		$this->readdb->select("b.id,b.title,b.image,b.description,b.status,IFNULL(bc.name,'') as category");
		$this->readdb->from($this->_table." as b");
		$this->readdb->join(tbl_blogcategory." as bc","bc.id=b.blogcategoryid ","LEFT");
		$this->readdb->where("b.channelid='".$channelid."' AND b.memberid='".$memberid."'");
		$this->readdb->order_by('b.id DESC');
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getActiveBlogList($channelid=0,$memberid=0){

		$query = $this->readdb->select("b.id,b.title,b.slug,b.image,b.description,IFNULL(bc.name,'') as category,b.createddate,b.metadescription")
					->from($this->_table." as b")
					->join(tbl_blogcategory." as bc","bc.id=b.blogcategoryid AND bc.status=1","LEFT")
					->where("b.status=1 AND b.channelid='".$channelid."' AND b.memberid='".$memberid."'")
					->order_by("b.id DESC")
					->get();		
		
		return $query->result_array();			
	}
	function getBlogDataBySlug($slug,$channelid=0,$memberid=0){

		$query = $this->readdb->select("b.id,b.title,b.slug,b.image,b.description,
									IFNULL(bc.name,'') as category,b.createddate,
									b.metatitle,b.metakeywords,b.metadescription,(SELECT name FROM ".tbl_user." WHERE id=b.addedby) as addedby
								")
				->from($this->_table." as b")
				->join(tbl_blogcategory." as bc","bc.id=b.blogcategoryid AND bc.status=1 AND bc.channelid='".$channelid."' AND bc.memberid='".$memberid."'","LEFT")
				->where("b.status=1 AND b.slug='".$slug."' AND b.channelid='".$channelid."' AND b.memberid='".$memberid."'")
				->get();		
		
		return $query->row_array();	
	}

	function getBlogListOnFront($limit,$offset=0,$filterarray='[]',$channelid=0,$memberid=0){

		$filterarray = json_decode($filterarray);
		
		$this->readdb->select("id,title,slug,image,description,createddate,(SELECT name FROM ".tbl_user." WHERE id=addedby) as addedby");
		$this->readdb->from($this->_table);
		$this->readdb->where("status=1 AND (blogcategoryid=0 OR IFNULL((SELECT 1 FROM ".tbl_blogcategory." WHERE id=blogcategoryid AND status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'),0) = 1) AND channelid='".$channelid."' AND memberid='".$memberid."'");
		if(!empty($filterarray)){
			if(!empty($filterarray->blogcategoryslug) && $filterarray->blogcategoryslug!='0'){
				$this->readdb->where("blogcategoryid IN (SELECT id FROM ".tbl_blogcategory." WHERE slug='".$filterarray->blogcategoryslug."')");
			}else if(!empty($filterarray->search) && $filterarray->search!='0'){
				$this->readdb->where("(title LIKE '%".$filterarray->search."%' OR '".$filterarray->search."'='')");
			}
		}

		$this->readdb->order_by("id","DESC");
		$this->readdb->limit($limit,$offset);
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getRecentBlogs($channelid=0,$memberid=0) {
        
        $query = $this->readdb->select("b.id,b.title,b.slug,b.image,b.description,b.createddate,(SELECT name FROM ".tbl_user." WHERE id=b.addedby) as addedby")
                ->from(tbl_blog." as b")
                ->where("b.status=1 AND (b.blogcategoryid=0 OR IFNULL((SELECT 1 FROM ".tbl_blogcategory." WHERE id=b.blogcategoryid AND status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'),0) = 1) AND channelid='".$channelid."' AND memberid='".$memberid."'")
                ->order_by("b.id","DESC")
                ->limit(RECENT_BLOG_LIMIT)
                ->get();
        
        return $query->result_array();
    }
    function CheckDuplicateValue($slug,$id='',$channelid=0,$memberid=0)
	{
	    if (isset($id) && $id != '') {
		  $query = $this->readdb->query("SELECT name FROM ".tbl_blogcategory." WHERE slug='".$slug."' AND id <> '".$id."' AND channelid='".$channelid."' AND memberid='".$memberid."'");
	    }else{
		  $query = $this->readdb->query("SELECT name FROM ".tbl_blogcategory." WHERE slug='".$slug."' AND channelid='".$channelid."' AND memberid='".$memberid."' ");
	    }
	   
	    if($query->num_rows()  > 0){
		  return 0;
	    }
	    else{
		  return 1;
	    }
	}
}
