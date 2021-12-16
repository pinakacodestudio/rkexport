<?php

class Video_gallery_model extends Common_model {

	//put your code here
    public $_table = tbl_videogallery;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }    

    function getVideoGallery(){
		$query = $this->readdb->select("vg.id,vg.title,vg.url,vg.mediacategoryid,(SELECT GROUP_CONCAT(mc.name) FROM ".tbl_mediacategory." as mc WHERE FIND_IN_SET(mc.id,vg.mediacategoryid)) as mediacategoryid,vg.priority,vg.status,vg.createddate")
						->from($this->_table." as vg")                        
						->order_by("priority ASC")
						->get();
		return $query->result_array();			
	}
	
	// START Front end   

    function getVideoGalleryVideo(){
		$query = $this->readdb->select("vg.id,vg.mediacategoryid,vg.title,vg.url")
						->from($this->_table." as vg")	  
            			->where("IFNULL((SELECT count(mc.id) FROM ".tbl_mediacategory." as mc WHERE FIND_IN_SET(mc.id,mediacategoryid) AND mc.status=1 AND vg.status=1 ),0) > 0")                 
						->order_by("vg.priority ASC")
						->get();
		return $query->result_array();			
  }

  	function getVideoGalleryByCategoryId($id){
		$query = $this->readdb->select("vg.id,vg.mediacategoryid,vg.title,vg.url")
						->from($this->_table." as vg")							
						->where("IFNULL((SELECT count(mc.id) FROM ".tbl_mediacategory." as mc WHERE FIND_IN_SET(mc.id,vg.mediacategoryid) AND mc.status=1 AND vg.status=1 ),0) > 0 AND (FIND_IN_SET('".$id."',vg.mediacategoryid)>0 OR '".$id."'='0') ")
						->order_by("vg.priority ASC")
						->get();
		return $query->result_array();				
	}

	// END

}
?>
