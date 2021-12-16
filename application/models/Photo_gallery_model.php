<?php

class Photo_gallery_model extends Common_model {

	//put your code here
    public $_table = tbl_photogallery;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }    
    function getPhotoGallery(){
		$query = $this->readdb->select("pg.id,pg.title,pg.image,pg.mediacategoryid,(SELECT GROUP_CONCAT(mc.name) FROM ".tbl_mediacategory." as mc WHERE FIND_IN_SET(mc.id,pg.mediacategoryid)) as mediacategoryid,pg.alttag,pg.priority,pg.status,pg.createddate")
						->from($this->_table." as pg")						
						->order_by("pg.priority ASC")
						->get();
		return $query->result_array();				
	}	

	// START Front end    

	function getPhotoGalleryImage(){
		$query = $this->readdb->select("id,mediacategoryid,title,image,alttag")
						->from($this->_table)
						->where("status=1 AND IFNULL((SELECT count(mc.id) FROM ".tbl_mediacategory." as mc WHERE FIND_IN_SET(mc.id,mediacategoryid)>0 AND mc.status=1),0) > 0")							
						->order_by("priority ASC")
						->get();
		return $query->result_array();				
	}	

	function getPhotoGalleryByCategoryId($id){
		$query = $this->readdb->select("pg.id,pg.mediacategoryid,pg.title,pg.image,pg.alttag")
						->from($this->_table." as pg")	
						->where("pg.status=1 AND IFNULL((SELECT count(mc.id) FROM ".tbl_mediacategory." as mc WHERE FIND_IN_SET(mc.id,pg.mediacategoryid)>0 AND mc.status=1),0) > 0 AND (FIND_IN_SET('".$id."',mediacategoryid)>0 OR '".$id."'='0') ")						
						->order_by("pg.priority ASC")
						->get();
		return $query->result_array();				
	}

	// END
}
?>