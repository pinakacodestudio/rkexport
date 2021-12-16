<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ordervariant_model extends Common_model {
	public $_table = tbl_ordervariant;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('id' => 'DESC');

}