<?php

class Customer_model extends Common_model {

	//put your code here
	public $_table = tbl_customer;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'c.name','c.mobile','c.email',"(select count(id) from ".tbl_cart." where customerid=c.id and usertype=0)",'c.createddate'); //set column field database for datatable orderable
	public $column_search = array('c.name','c.mobile','c.email',"(select count(id) from ".tbl_cart." where customerid=c.id and usertype=0)",'c.createddate'); //set column field database for datatable searchable 
	public $order = array('c.id' => 'DESC'); // default order 
	function __construct() {
		parent::__construct();
	}
	function CheckCustomerMobileAvailable($countrycode,$mobileno, $ID = '') {
		
		$where = "countrycode='".$countrycode."' AND mobile='".$mobileno."' AND mobile!=''";
		
		if (isset($ID) && $ID != '') {
			$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where('id <>',$ID)
			->where($where)
			->get();

		} else {
			$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where($where)
			->get();
		}
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}

	function CheckCustomerEmailAvailable($email, $ID = '') {
		
		$where = "email='".$email."' AND email!=''";
		
		if (isset($ID) && $ID != '') {
			$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where('id <>',$ID)
			->where($where)
			->get();

		} else {
			$query = $this->db->select($this->_fields)
			->from($this->_table)
			->where($where)
			->get();
		}
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	/*function getCustomerDataByID($Customerid){

		$this->db->select("cba.firstname,cba.lastname,c.username,c.ipaddress,c.email,c.createddate,
						cba.mobileno,cba.address,cba.postcode,
						(country.name) as country,(p.name) as province,(city.name) as city");
		$this->db->from($this->_table." as c");
		$this->db->join(tbl_customerbillingaddress." as cba","cba.customerid=c.id","INNER");
		$this->db->join(tbl_city." as city","city.id=cba.cityid","INNER");
		$this->db->join(tbl_province." as p","p.id=city.provinceid","INNER");
		$this->db->join(tbl_country." as country","country.id=p.countryid","INNER");
		$this->db->where("c.id=".$Customerid);
		$query = $this->db->get();
		
		return $query->row_array();
	}*/
	function getCustomerData(){

		$this->db->select("c.id, c.name,c.image,c.email,c.mobile,c.createddate");
		$this->db->from($this->_table." as c");
		
		
		$query = $this->db->get();
		
		return $query->result_array();
	}
	function getCustomerOrderData($Customerid){
		$this->db->select('o.id,o.orderid,o.status, (select sum(finalprice) from orderproducts where orderid = o.id ) as finalprice, o.createddate as date, o.customerid, ca.name as customername,payableamount');
        $this->db->from(tbl_productorder." as o");
        $this->db->join(tbl_memberaddress." as ca","ca.id=o.addressid","left");
        // $this->db->where('o.customerid = '.$Customerid);
        $this->db->where(array('o.customerid'=>$Customerid,'o.usertype'=>0));
        $this->db->group_by('o.orderid');
        $this->db->order_by('o.id', 'DESC');

        $query = $this->db->get();
		
		return $query->result_array();
    }

	function CheckCustomerLogin($username,$password) {

		$query = $this->db->select($this->_fields)
			->from($this->_table)
			->group_start()
			->where("email = '" . $username. "'")
			->or_where("username = '" . $username. "'")
			->group_end()
			->where("password", $password)
			->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}
	}
	function getCustomerDetail($Customerid){

		$this->db->select("c.id, c.name,c.image,c.email,c.mobile,c.createddate");
		$this->db->from($this->_table." as c");
		//$this->db->join(tbl_customeraddress." as ca","ca.customerid=c.id","LEFT");
		$this->db->where("c.id=".$Customerid);
		$query = $this->db->get();
		
		return $query->row_array();
	}
	function getCustomerShippingDetail($Customerid){

		$this->db->select("ca.id, ca.name,ca.address,ca.town, ca.postalcode, ca.email,ca.mobileno,ca.createddate");
		$this->db->from(tbl_memberaddress." as ca");
		//$this->db->join(tbl_customeraddress." as ca","ca.customerid=c.id","LEFT");
		$this->db->group_by('ca.email');
		// $this->db->where("ca.userid=".$Customerid);
		$this->db->where(array('ca.userid'=>$Customerid,'ca.usertype'=>0));
		$query = $this->db->get();
		
		return $query->result_array();
	}

	//LISTING DATA
	function _get_datatables_query(){

		$this->db->select("c.id,c.name,c.mobile,c.email,c.status,c.createddate,(select count(id) from ".tbl_cart." where customerid=c.id and usertype=0)as cartcount");
		$this->db->from($this->_table." as c");
		//$this->db->join(tbl_customeraddress." as ca","ca.userid=c.id","INNER");
		// $this->db->group_by('c.name');

		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->db->from($this->_table);
		return $this->db->count_all_results();
	}
}
