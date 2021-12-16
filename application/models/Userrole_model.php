<?php
class Userrole_model extends Common_model 
{
	//put your code here
	public $_table = tbl_userrole;
	public $_fields = "*";
	public $_where = array();
	public $_order = "id desc";
	public $_except_fields = array();

	function __construct() {
		parent::__construct();
	}
}
?>