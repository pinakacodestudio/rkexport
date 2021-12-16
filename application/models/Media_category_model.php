<?php

class Media_category_model extends Common_model {

	//put your code here
    public $_table = tbl_mediacategory;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();

    function __construct() {
        parent::__construct();
    }
    function getMediaCategory(){
		$query = $this->db->select("id,name")
						->from($this->_table)						
						->order_by("priority","ASC")
						->get();
		return $query->result_array();				
    }   
     
    function CheckDuplicateValue($name,$id='')
    {
        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT name FROM ".tbl_mediacategory." WHERE name ='".$name."' AND id <> '".$id."'");
        }else{
            $query = $this->readdb->query("SELECT name FROM ".tbl_mediacategory." WHERE name ='".$name."'");
        }
       
        if($query->num_rows()  > 0){
            return 0;
        }
        else{
            return 1;
        }
    }
    
    function getActiveMediaCategoryForPhotoGallery(){
		$query = $this->db->select("mc.id,mc.name")
                        ->from($this->_table." as mc")
                        ->where("mc.status=1 AND IFNULL((SELECT count(pg.id) FROM ".tbl_photogallery." as pg WHERE FIND_IN_SET(mc.id,pg.mediacategoryid)>0 AND pg.status=1),0) > 0")			                        				
                        ->order_by("mc.priority","ASC")
                        ->get();
		return $query->result_array();				
    }    

    function getActiveMediaCategoryForVideoGallery(){
		$query = $this->db->select("mc.id,mc.name")
                        ->from($this->_table." as mc")                        
                        ->where("mc.status=1 AND IFNULL((SELECT count(vg.id) FROM ".tbl_videogallery." as vg WHERE FIND_IN_SET(mc.id,vg.mediacategoryid)>0 AND vg.status=1),0) > 0")		                        				
						->order_by("mc.priority","ASC")
						->get();
		return $query->result_array();				
    }  

    // END
}
?>