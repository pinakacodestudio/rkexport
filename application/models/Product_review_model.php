<?php

class Product_review_model extends Common_model {

	//put your code here
	public $_table = tbl_productreview;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'membername','email','mobileno','productname','typename',null,'pr.createddate',null,null); //set column field database for datatable orderable
	public $column_search = array("IF(pr.memberid!=0,IFNULL((SELECT CONCAT(cba.name) FROM ".tbl_memberaddress." as cba WHERE cba.memberid=c.id LIMIT 1),''),prg.name)","IF(pr.memberid!=0,c.email,prg.email)","IF(pr.memberid!=0,'Register','Guest')","pr.createddate","pr.type","pr.memberid"); //set column field database for datatable searchable 
	public $order = array('pr.id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}


	function getProductReviewByCustomer($memberid){
		
		$this->readdb->select('c.productid,c.priceid,c.quantity');
		$this->readdb->from($this->_table.' AS c');
		$this->readdb->where("c.memberid=".$memberid." AND c.type=1");
	    $query = $this->readdb->get();
       
		if($query->num_rows() == 0) {
			return array();
		} else {
			return $query->result_array();
		}
	}
	public function getProductReviewByCustomerForFront($memberid) {

		$query = $this->readdb->select("pr.id,pr.message,pr.rating,p.name as productname,pr.createddate")
							->from($this->_table." as pr")
							->join(tbl_member." as c","c.id=".$memberid." AND c.status=1","INNER")
							->join(tbl_product." as p","p.id=pr.productid AND p.status=1","INNER")
							->where("pr.memberid=".$memberid)
							->get();
        return $query->result_array();
    }
    public function getProductReviewByMemberForFront($limit,$offset=0,$filterarray,$type='data') {

    	$filterarray = json_decode($filterarray,true);

		$this->readdb->select("pr.message,pr.rating,p.name as productname,p.slug,p.singlelink,pr.createddate,p.id as productid,
							");
		$this->readdb->from($this->_table." as pr");
		$this->readdb->join(tbl_member." as c","c.id=".$filterarray['memberid']." AND c.status=1","INNER");
		$this->readdb->join(tbl_product." as p","p.id=pr.productid AND p.status=1","INNER");
		$this->readdb->where("pr.status=1 AND pr.memberid=".$filterarray['memberid']);
		$this->readdb->where("pr.status=1 AND pr.type=".$filterarray['type']);
		$this->readdb->where("pr.status=1 AND pr.type=".$filterarray['type']);
		$this->readdb->where("pr.status=1 AND pr.type=".$filterarray['type']);
		
		$this->readdb->order_by('pr.createddate DESC');
		if($type=='data'){
			$this->readdb->limit($limit,$offset);
		}
		$query = $this->readdb->get();
		if($type=='data'){
			
    		$this->load->model('Product_model', 'Product');
			$data = array();
			foreach ($query->result_array() as $row) {

				$ProductFiles = $this->Product->getProductFiles($row['productid'],'',2);
				$image = array();
				foreach ($ProductFiles as $filerow) {
					if($filerow['type']==1){
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['file']);
					}else if($filerow['type']==2){
						$image[] = array('type'=>$filerow['type'],'file'=>$filerow['videothumb']);
					}else if($filerow['type']==3){
						preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", urldecode($filerow['file']), $matches);
						$image[] = array('type'=>$filerow['type'],'file'=>$matches[1]);
					}
				}
				$image = json_encode($image);

				$data[] = array("productname"=>$row['productname'],
								"slug"=>$row['slug'],
								"singlelink"=>$row['singlelink'],
								"image"=>$image,
								"createddate"=>$row['createddate'],
								"verified"=>$row['verified'],
								"rating"=>$row['rating'],
								"message"=>$row['message']
								);

			}
	        return $data;
		}else{
			return $query->num_rows();
		}
		
    }
    function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		return $query->result();
	}
    
	//LISTING DATA
	function _get_datatables_query($MEMBERID,$CHANNELID){
		
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$productid = $_REQUEST['productid'];		
		$type = $_REQUEST['type'];
		$memberid = $_REQUEST['memberid'];

		$this->readdb->select("pr.id,pr.message,pr.rating,pr.memberid,pr.createddate,pr.type,p.name as productname,p.id as productid,
							IF(pr.memberid!=0,m.name,prg.name) as membername,
							IF(pr.memberid!=0,m.channelid,0) as memberchannelid,
							IF(pr.memberid!=0,m.membercode,0) as membercode,	
							IF(pr.memberid!=0,m.mobile,prg.mobileno) as mobileno,
							IF(pr.memberid!=0,m.email,prg.email) as email,
							IF(pr.memberid!=0,'Register','Guest') as usertype,
							CASE 
								WHEN pr.type=0 THEN 'Pending'
								WHEN pr.type=1 THEN 'Approved'
								WHEN pr.type=2 THEN 'Not Approved' 
							END as typename
						");
		$this->readdb->from($this->_table." as pr");
		$this->readdb->join(tbl_product." as p","p.id=pr.productid AND p.status=1","INNER");	
		$this->readdb->join(tbl_member." as m","m.id=pr.memberid","LEFT");
		$this->readdb->join(tbl_productreviewbyguest." as prg","prg.productreviewid=pr.id","LEFT");
		$this->readdb->where('(p.id="'.$productid.'" OR  "'.$productid.'" ="0")');
		$this->readdb->where('(pr.type="'.$type.'" OR  "" ="'.$type.'")');
		$this->readdb->where("DATE(pr.createddate) BETWEEN '".$startdate."' AND '".$enddate."' AND pr.channelid='".$CHANNELID."' AND pr.sellermemberid='".$MEMBERID."'");
		if($memberid!='') {
			if($memberid==1){
				$this->readdb->where('pr.memberid!=0');
			}else{
				$this->readdb->where('pr.memberid=0');
			}
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
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	
	function count_filtered($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
	}
}
