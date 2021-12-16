<?php
class Payment_gateway_model extends Common_model {

	//put your code here
	public $_table = tbl_paymentgateway;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order;

	function __construct() {
		parent::__construct();
	}

	function getPaymentMethodForFront() {
		$query = $this->db->select("pg.id,pg.paymentgatewayid,pg.merchantkey,pg.merchantid,pg.merchantsalt,pg.authheader,pg.merchantwebsiteforweb,pg.merchantwebsiteforapp,pg.channelidforweb,pg.channelidforapp,pg.industrytypeid,pg.transactioncharge,pg.isdebug,pg.status,pg.paymentsuccessurl,pg.paymentfaileddurl")
						->from($this->_table." as pg")
						->where("pg.status=1")
						->get();

		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return false;
		}

	}
}
        