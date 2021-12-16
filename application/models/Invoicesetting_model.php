<?php
class InvoiceSetting_model extends Common_model{
	
	//put your code here
	public $_table = tbl_invoicesetting;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
	function getShipperDetails(){
		$query = $this->db->select("businessaddress,postcode,p.code,is.businessname,
									IFNULL((SELECT name FROM ".tbl_city." WHERE id=cityid),'') as cityname,
									IFNULL((SELECT mobileno FROM ".tbl_settings."),'') as phonenumber,
									IFNULL((SELECT email FROM ".tbl_fedexdetail." LIMIT 1),'') as email,
									")
							->from($this->_table." as is")
							->join(tbl_city." as c","c.id=is.cityid","INNER")
							->join(tbl_province." as p","p.id=c.provinceid","INNER")
							->get();
							
		return $query->row_array();					
	}
}