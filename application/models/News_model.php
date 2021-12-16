<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News_model extends Common_model {
	public $_table = tbl_news;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('n.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'brand','n.title','n.image','n.description','n.createddate','n.forwebsite','n.forapp');

	//set column field database for datatable searchable 
	public $column_search = array('IFNULL((SELECT name FROM '.tbl_brand.' WHERE id=n.brandid),"")','n.title','n.description','n.createddate','n.forwebsite','n.forapp');

	function __construct() {
		parent::__construct();
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			// echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}
	

	function _get_datatables_query(){
		
		$memberid = $this->session->userdata(base_url().'MEMBERID');
		$channelid = $this->session->userdata(base_url().'CHANNELID');

		$this->readdb->select('n.id,n.title,IFNULL((SELECT name FROM '.tbl_brand.' WHERE id=n.brandid),"") as brand,n.image,n.description,n.createddate,n.status,n.addedby');
		$this->readdb->from($this->_table." as n");
		
		
		if(!is_null($memberid)) {

			$this->readdb->where('(n.addedby = "'. $memberid .'" OR (n.addedby IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid="'. $memberid .'") AND n.id IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid="'. $memberid .'" AND channelid="'. $channelid .'") AND n.status=1) OR (n.id IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='. $memberid .' AND channelid='. $channelid .') AND n.type=0 AND n.status=1))');
            /* $this->readdb->where('(IF((SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $memberid .')=0,type=0 OR `addedby` = '. $memberid .',(`addedby` IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $memberid .') OR `addedby` = '. $memberid .'))) OR id IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='.$memberid.')'); */
        }
		
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

	function getnewsrecord($counter,$search,$memberid=0,$channelid=0) {
		$limit=10;
 
		$this->readdb->select('id,title,IFNULL((SELECT name FROM '.tbl_brand.' WHERE id=brandid),"") as brand,description, DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s") as date');		
		$this->readdb->from($this->_table);
		$this->readdb->where(array('status'=>1));
		
		if($memberid!=0 || $channelid!=0){
            //$this->readdb->where('(addedby='.$memberid.' OR id IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='.$memberid.'))');

			$this->readdb->where('createddate>=(SELECT m.createddate FROM '.tbl_member.' as m WHERE m.id='.$memberid.') AND 
			((`addedby` IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $memberid .') AND `id` IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='. $memberid .' AND channelid='. $channelid .')) OR (`id` IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='. $memberid .' AND channelid='. $channelid .') AND type=0))
								');

			/* (addedby='.$memberid.' OR (addedby IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='.$memberid.') AND id IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='.$memberid.')) OR id IN (SELECT newsid FROM '.tbl_newschannelmapping.' WHERE memberid='.$memberid.')) */
		}	
		if($search!=""){
			$this->readdb->where("(title LIKE CONCAT('%','$search','%'))");
		}
		$this->readdb->order_by("id","DESC");
        $this->readdb->limit($limit,$counter);     
		$query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		if($query->num_rows() == 0){
			return array();
		} 
		 else {
			$Data = $query->result_array();
			$json = array();
			foreach ($Data as $row) {
				$json[] = $row;
			}
			return $json;
		}
	}

	function getNewsChannelMappingDataByNewsID($newsid) {
		
		$query = $this->readdb->select('id,channelid,memberid')
						->from(tbl_newschannelmapping)
						->where(array('newsid'=>$newsid))
						->get();
		
		if($query->num_rows() == 0){
			return array();
		}else {
			return $query->result_array();
		}
	}
	function getNews($limit,$offset=0,$filterarray='[]'){

		$filterarray = json_decode($filterarray);
		
		$this->readdb->select("id,title,slug,link,image,description,createddate");
		$this->readdb->from($this->_table);
		$this->readdb->where("status=1 AND IFNULL((SELECT 1 FROM ".tbl_newscategory." WHERE id=newscategoryid AND status=1),0) = 1");
		if(!empty($filterarray)){
			if(!empty($filterarray->newscategoryslug) && $filterarray->newscategoryslug!='0'){
				$this->readdb->where("newscategoryid IN (SELECT id FROM ".tbl_newscategory." WHERE slug='".$filterarray->newscategoryslug."')");
			}else if(!empty($filterarray->search) && $filterarray->search!='0'){
				$this->readdb->where("(title LIKE '%".$filterarray->search."%' OR '".$filterarray->search."'='')");
			}
		}

		$this->readdb->order_by("id","DESC");
		$this->readdb->limit($limit,$offset);
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getLatestNews(){
		
		$query = $this->readdb->select("id,title,slug,link,image,description,forwebsite,forapp,createddate")
				->from($this->_table)
				->where("status=1 AND IFNULL((SELECT 1 FROM ".tbl_newscategory." WHERE id=newscategoryid AND status=1),0) = 1")
				->order_by("id","DESC")
				->limit(6)
				->get();
		
		return $query->result_array();
	}


	function getNewsDataByID($ID){
		$query = $this->readdb->select("id,title,brandid,link,image,description,newscategoryid,status,forwebsite,forapp,metatitle,metadescription,metakeywords,createddate")
				->from($this->_table)
				->where("id=".$ID)
				->get();
		return $query->row_array();
	}
	
	function getNewsDataBySlug($slug){
		$query = $this->readdb->select("id,title,slug,link,image,description,newscategoryid,status,metatitle,metadescription,metakeywords,forwebsite,forapp,createddate")
				->from($this->_table)
				->where("slug='".$slug."'")
				->get();
		return $query->row_array();
	}
}