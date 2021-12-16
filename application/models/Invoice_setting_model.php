<?php
class Invoice_setting_model extends Common_model{
	
	//put your code here
	public $_table = tbl_invoicesetting;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;
	public $column_order = array(null,'channel','membername','is.businessname',null,'is.email'); //set column field database for datatable orderable
	public $column_search = array('IFNULL(c.name,"Company")','m.name','m.membercode','is.businessname','is.email'); //set column field database for datatable searchable 
	public $order = array('is.id' => 'DESC'); // default order 

	function __construct() {
		parent::__construct();
	}

	function getShipperDetails($channelid=0,$memberid=0){
		
		$query = $this->readdb->select("is.businessname,is.businessaddress,is.gstno,is.email as invoiceemail,is.logo,is.notes,is.postcode,p.code,
									ct.name as cityname,p.name as provincename,cn.name as countryname,
									IFNULL((SELECT mobileno FROM ".tbl_settings."),'') as phonenumber,
									IFNULL((SELECT email FROM ".tbl_fedexdetail." WHERE memberid=0 AND channelid=0 LIMIT 1),'') as email
									
								")
							->from($this->_table." as is")
							->join(tbl_country." as cn","cn.id=is.countryid","INNER")
							->join(tbl_province." as p","p.id=is.provinceid","INNER")
							->join(tbl_city." as ct","ct.id=is.cityid","INNER")
							->where("IF(IFNULL((SELECT count(id) FROM ".$this->_table." WHERE channelid='".$channelid."' AND memberid='".$memberid."'),0),channelid='".$channelid."' AND memberid='".$memberid."',channelid=0 AND memberid=0)")
							->get();
							
		$rowdata =  $query->row_array();		
		
		if(!empty($rowdata)){

			$address = ucwords($rowdata['businessaddress']);
			$address = preg_replace('!\s+!', ' ', $address);
			$address = substr($address, 0, 70);
	
			$fulladdress = ucwords($rowdata['businessaddress']);
	
			$shipperdetail = array("businessname"=>$rowdata['businessname'],
								"invoiceemail"=>$rowdata['invoiceemail'],
								"email"=>$rowdata['email'],
								"phonenumber"=>$rowdata['phonenumber'],
								"businessaddress"=>$address,
								"businessaddressfull"=>$fulladdress,
								"gstno"=>$rowdata['gstno'],
								"postcode"=>$rowdata['postcode'],
								"cityname"=>ucwords($rowdata['cityname']),
								"provincename"=>ucwords($rowdata['provincename']),
								"countryname"=>ucwords($rowdata['countryname']),
								"code"=>$rowdata['code'],
								"logo"=>$rowdata['logo'],
								"notes"=>$rowdata['notes'],
							);
			
			return $shipperdetail;
		}else{
			return array();
		}
	}

	function getInvoiceSettingsByMember($channelid=0,$memberid=0){

		$this->readdb->select("is.id,is.businessname,is.businessaddress,is.gstno,is.email,is.logo,is.notes,is.postcode,is.channelid,is.memberid,
						is.countryid,is.provinceid,is.cityid,p.code, ct.name as cityname,p.name as provincename,cn.name as countryname
					");
		$this->readdb->from($this->_table." as is");
		$this->readdb->join(tbl_country." as cn","cn.id=is.countryid","INNER");
		$this->readdb->join(tbl_province." as p","p.id=is.provinceid","INNER");
		$this->readdb->join(tbl_city." as ct","ct.id=is.cityid","INNER");
        if ($channelid!=0 && $memberid!=0) {
            $this->readdb->where("(is.channelid='".$channelid."' ) AND (is.memberid='".$memberid."' )");
		}
		$this->readdb->order_by('is.id DESC');
		$query = $this->readdb->get();
							
        if ($channelid!=0 && $memberid!=0) {
            return $query->row_array();
        }else{
			return $query->result_array();
		}
		//print_r($query->result_array());
		
	}

	function getInvoiceSettingsdata($id){

		$this->readdb->select("is.id,is.businessname,is.businessaddress,is.gstno,is.email,is.logo,is.notes,is.postcode,is.channelid,is.memberid,
						is.countryid,is.provinceid,is.cityid,p.code, ct.name as cityname,p.name as provincename,cn.name as countryname
					");
		$this->readdb->from($this->_table." as is");
		$this->readdb->join(tbl_country." as cn","cn.id=is.countryid","INNER");
		$this->readdb->join(tbl_province." as p","p.id=is.provinceid","INNER");
		$this->readdb->join(tbl_city." as ct","ct.id=is.cityid","INNER");
		$this->readdb->where('is.id='.$id);
		$query = $this->readdb->get();
							
		//print_r($query->result_array());
		return $query->row_array();
		
	}

	 //LISTING DATA
	 function _get_datatables_query(){
		
		$channelid = (isset($_REQUEST['channelid']))?$_REQUEST['channelid']:'';
		$memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:'0';
		/* $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID'); */

		$this->readdb->select("is.id,is.businessname,is.businessaddress,is.gstno,is.email,is.logo,is.notes,is.postcode,is.channelid,is.memberid,
		is.countryid,is.provinceid,is.cityid,p.code, ct.name as cityname,p.name as provincename,cn.name as countryname
						
		");
		$this->readdb->from($this->_table." as is");
		$this->readdb->join(tbl_country." as cn","cn.id=is.countryid","INNER");
		$this->readdb->join(tbl_province." as p","p.id=is.provinceid","INNER");
		$this->readdb->join(tbl_city." as ct","ct.id=is.cityid","INNER");

		/* if(!is_null($MEMBERID)){
			$this->readdb->where("cc.channelid = ".$CHANNELID." AND cc.memberid = ".$MEMBERID);
		} */
		$this->readdb->where("(is.channelid = '".$channelid."' OR '".$channelid."'='') AND (is.memberid = ".$memberid." OR ".$memberid."=0)");
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

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
	}
}