<?php
class Paymentgateway_model extends Common_model {

	//put your code here
	public $_table = tbl_paymentgateway;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}
}
        