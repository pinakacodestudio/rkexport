<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalog_model extends Common_model {
	public $_table = tbl_catalog;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('c.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null, 'membername', 'c.name','counttotalcatalog','counttotalcatalog','c.createddate');

	//set column field database for datatable searchable 
	public $column_search = array('c.name','c.createddate','IF(c.type=1,(SELECT name FROM '.tbl_member.' WHERE id=c.addedby),"Company")');

	function __construct() {
		parent::__construct();
	}

	function getUniqueCountForCatalog($catalogid){
		
		$query  = $this->readdb->query("SELECT count(temp.count) as count
									FROM (
										SELECT COUNT(ch.id) as count 
										FROM ".tbl_catlogviewhistory." as ch 
										WHERE ch.catalogid = ".$catalogid."
										GROUP BY ch.memberid
										) as temp
								   ");
		return $query->row_array();
	}
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			//echo $this->readdb->last_query(); exit;
			return $query->result();
		}
	}

	function _get_datatables_query(){

		$channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
		$memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
		if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
			$channelid = $this->session->userdata(base_url().'CHANNEL');
		}
		$channelid = (is_array($channelid))?implode(",",$channelid):$channelid;
		
		$startdate = isset($_REQUEST['startdate'])?$this->general_model->convertdate($_REQUEST['startdate']):'';
		$enddate = isset($_REQUEST['enddate'])?$this->general_model->convertdate($_REQUEST['enddate']):'';
		
		
		$this->readdb->select("c.id,c.name,c.description,c.createddate,c.status,
							IF(c.type=1,(SELECT name FROM ".tbl_member." WHERE id=c.addedby),'Company') as membername,
							IF(c.type=1,(SELECT membercode FROM ".tbl_member." WHERE id=c.addedby),'') as membercode,
							IF(c.type=1,(SELECT channelid FROM ".tbl_member." WHERE id=c.addedby),'0') as channelid,
							c.addedby as memberid,c.createddate,c.type,
							IFNULL((SELECT count(catalogid) FROM ".tbl_catlogviewhistory." WHERE catalogid=c.id),0) as counttotalcatalog,
							");
		
		$this->readdb->from($this->_table." as c");
		 
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		if(!is_null($MEMBERID)){

			if(CHANNELWISECATALOG==1 && ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1){
				/* $this->db->where("(
				
					IF(
						(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.")=0,
						c.type=0 AND c.status = 1,
						(`c`.`addedby` IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.") AND c.status = 1 AND c.id IN (SELECT catalogid FROM ".tbl_catalogchannelmapping." WHERE memberid=".$MEMBERID.")) OR `c`.`addedby` = ".$MEMBERID."  OR (c.id IN (SELECT catalogid FROM ".tbl_catalogchannelmapping." WHERE memberid=".$MEMBERID.") AND c.type=0 AND c.status = 1)))
						
					"); */

					$this->readdb->where('(`addedby` = '. $MEMBERID .' OR (`addedby` IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $MEMBERID .') AND status=1) OR (`id` IN (SELECT catalogid FROM '.tbl_catalogchannelmapping.' WHERE memberid='. $MEMBERID .') AND type=0 AND status=1))');
			}else{
				$this->readdb->where('c.status = 1');
				$this->readdb->where("c.type=0");
			}
        }else{
			$where = '';
			if($channelid>0){
				$where .= " AND FIND_IN_SET(c.addedby, (SELECT GROUP_CONCAT(id) FROM ".tbl_member." WHERE channelid IN (".$channelid.")))";
			}
			if($memberid!=0){
				$where .= " AND c.addedby=".$memberid;
			}
			if($where!=''){
				$where .= " AND c.type=1";
			}
			if($channelid==-1 && $where==''){
				$where .= " AND c.type=0";
			}
			$this->readdb->where("date(c.createddate) BETWEEN '".$startdate."' AND '".$enddate."'".$where);
		}
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

	function getcatalogrecord($memberid,$channelid,$counter,$search) {
		$limit=10;

		$this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel');
        $this->readdb->from(tbl_systemconfiguration." as s"); 
        $this->readdb->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")","INNER");
		$systemconfiguration = $this->readdb->get()->row_array();


		$this->readdb->select('c.id,c.name,c.description,c.image,c.pdffile, DATE_FORMAT(c.createddate, "%d/%m/%Y %H:%i:%s") as date');		
		$this->readdb->from($this->_table." as c");
		$this->readdb->where("(c.name LIKE CONCAT('%','$search','%'))");
		if(CHANNELWISECATALOG==1 && ($systemconfiguration['allowmultiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!='')){
			
			
			/* $this->db->where('(IF((SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $memberid .')=0,c.type=0 OR `c`.`addedby` = '. $memberid .',(`c`.`addedby` IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $memberid .') OR `c`.`addedby` != '. $memberid .'))) OR c.id IN (SELECT catalogid FROM '.tbl_catalogchannelmapping.' WHERE memberid='.$memberid.')'); */

			if($channelid==GUESTCHANNELID){

				$this->readdb->where('(`id` IN (SELECT catalogid FROM '.tbl_catalogchannelmapping.' WHERE memberid='. $memberid .' AND channelid='. $channelid .') AND type=0)');
			}else{
				$this->readdb->where('
			((`addedby` IN (SELECT mainmemberid FROM '.tbl_membermapping.' WHERE submemberid='. $memberid .')) OR (`id` IN (SELECT catalogid FROM '.tbl_catalogchannelmapping.' WHERE memberid='. $memberid .' AND channelid='. $channelid .') AND type=0))
								');
			}
		}else{
			if($channelid==GUESTCHANNELID){

				$this->readdb->where('(`id` IN (SELECT catalogid FROM '.tbl_catalogchannelmapping.' WHERE memberid='. $memberid .' AND channelid='. $channelid .') AND type=0)');
			}else{

				$this->readdb->where('c.type=0');
			}
		}
		$this->readdb->where('c.status = 1');
		
		$this->readdb->order_by("c.id","DESC");
        $this->readdb->limit($limit,$counter);     
		$query = $this->readdb->get();
		// echo $this->db->last_query(); exit;
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

	function getCatalogChannelMappingDataByCatalogID($catalogid) {
		
		$query = $this->readdb->select('id,channelid,memberid')
						->from(tbl_catalogchannelmapping)
						->where(array('catalogid'=>$catalogid))
						->get();
		
		if($query->num_rows() == 0){
			return array();
		}else {
			return $query->result_array();
		}
	}
	
	

}