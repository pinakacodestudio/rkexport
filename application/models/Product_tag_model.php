<?php

class Product_tag_model extends Common_model {

	//put your code here
	public $_table = tbl_producttag;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;
	//set column field database for datatable orderable
	public $column_order = array(null,'tag','createddate');

	//set column field database for datatable searchable 
	public $column_search = array('tag','DATE_FORMAT(createddate, "%d/%m/%Y %H:%i:%s")');
	public $order = array('p.priority' => 'ASC'); // default order 

	function __construct() {
		parent::__construct();
	}
	function searchproducttag($type,$search,$channelid=0,$memberid=0){

		$this->readdb->select("t.id,t.tag as text");
		$this->readdb->from($this->_table." as t");
		$this->readdb->where("t.channelid=".$channelid." AND t.memberid=".$memberid);
		if($type==1){
			$this->readdb->where("t.tag LIKE '%".$search."%' AND t.status=1");
		}else{
			$this->readdb->where("FIND_IN_SET(t.id,'".$search."')>0 AND t.status=1");
		}
		$query = $this->readdb->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
			/* if($type==1){
				return $query->result_array();
			}else{
				return $query->row_array();
			} */
		}else {
			return 0;
		}	
	}
	public function addMultipleProducttag($tags,$numerictagid='allow',$MEMBERID=0,$CHANNELID=0) {
        
		$createddate = $this->general_model->getCurrentDateTime();
		if($MEMBERID!=0){
			$addedby = $MEMBERID;
			$memberid = $MEMBERID;
			$channelid = $CHANNELID;
			$usertype = 1;
		}else{
			$addedby = $this->session->userdata(base_url() . 'ADMINID');
			$memberid = 0;
			$channelid = 0;
			$usertype = 0;
		}
		
        $tagid =array();
        for($i=0; $i<count($tags); $i++){

        	if(!is_numeric($tags[$i])){

				$slug = preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($tags[$i])));
	            $this->_fields = "id";
	            $this->_where = ("(tag='" . trim($tags[$i]) . "' OR slug='" . trim($slug) . "')");
	            $TagData = $this->getRecordsByID();

	            if (empty($TagData)) {
	                
	                $insertdata = array(
						"tag" => $tags[$i],
						'channelid' => $channelid,
						'memberid' => $memberid,
						'usertype' => $usertype,
	                    "slug" => $slug,
	                    "status" => 1,
	                    "createddate" => $createddate,
	                    "modifieddate" => $createddate,
	                    "addedby" => $addedby,
	                    "modifiedby" => $addedby
	                );
	                $insertdata = array_map('trim', $insertdata);
	                $this->writedb->set($insertdata);
	                $this->writedb->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".$this->_table." as pt)",FALSE);
	                $this->writedb->insert($this->_table);

	                $tagid[] = $this->writedb->insert_id();
	                
	            }else{
					$tagid[] = $TagData['id'];
		        }
	        }else{
				if($numerictagid=="allow"){
					$tagid[] = $tags[$i];
				}
	        }
        }
        return $tagid;
    }
    public function getActiveProductTagsOnFront($limit,$offset=0,$channelid=0,$memberid=0){

		$query = $this->readdb->select("id,tag,slug")
							->from($this->_table)
							->where("status=1 AND channelid=".$channelid." AND memberid=".$memberid)
							->order_by("priority ASC")
							->limit($limit,$offset)
							->get();		

		return $query->result_array();			
	}
	function getProductTagsByProductId($productid,$channelid,$memberid){
		
		$query = $this->readdb->select("pt.id,pt.tag,pt.slug")
					->from($this->_table." as pt")
					->join(tbl_producttagmapping." as ptm","ptm.tagid=pt.id","INNER")
					->where("pt.status=1 AND ptm.productid='".$productid."' AND pt.channelid='".$channelid."' AND pt.memberid='".$memberid."'")
					->order_by("pt.priority ASC")
					->get();		

		return $query->result_array();
	}
	function getProductTagBySlug($slug,$channelid=0,$memberid=0){
		
		$query = $this->readdb->select("pt.id,pt.tag,pt.slug")
					->from($this->_table." as pt")
					->where("pt.status=1 AND pt.slug='".$slug."' AND pt.channelid='".$channelid."' AND pt.memberid='".$memberid."'")
					->get();		

		return $query->row_array();
	}

	//**********************LISTING DATA*******************************
	function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			//echo $this->readdb->last_query();exit;
			return $query->result();
		}
	}

	function _get_datatables_query($MEMBERID,$CHANNELID){
		
		$this->readdb->select('p.*');
		$this->readdb->from($this->_table." as p");
		$this->readdb->where("p.memberid='".$MEMBERID."' AND p.channelid='".$CHANNELID."'");
        
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
		} else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function count_all($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		return $this->readdb->count_all_results();
	}

	function count_filtered($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

}
