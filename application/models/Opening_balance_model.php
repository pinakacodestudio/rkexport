<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opening_balance_model extends Common_model {
	public $_table = tbl_openingbalance;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array();

	function __construct() {
		parent::__construct();
	}
	function setOpeningBalance($PostData) {

		$this->load->model('Opening_balance_model', 'Opening_balance');

		$balancedate = ($PostData['balancedate']!='')?$this->general_model->convertdate($PostData['balancedate']):'';
		$modifieddate = $this->general_model->getCurrentDateTime();
		$balance = $PostData['balance'];
		$memberid = $PostData['memberid'];
		$sellermemberid = $PostData['sellermemberid'];
		$modifiedby = $PostData['modifiedby'];

		
		if(!empty($PostData['balanceid'])){
			$updatedata = array('balancedate'=>$balancedate,
								'balance'=>$balance,
								'modifieddate'=>$modifieddate,
								'modifiedby'=>$modifiedby);

			$this->Opening_balance->_where = array('id'=>$PostData['balanceid'],'memberid'=>$memberid,'sellermemberid'=>$sellermemberid);
			$this->Opening_balance->Edit($updatedata);
		}else{
            if (!empty($balancedate)) {
                $insertdata = array('memberid'=>$memberid,
                                'sellermemberid'=>$sellermemberid,
                                'balancedate'=>$balancedate,
                                'balance'=>$balance,
                                'createddate'=>$modifieddate,
                                'modifieddate'=>$modifieddate,
                                'addedby'=>$modifiedby,
                                'modifiedby'=>$modifiedby);
                $this->Opening_balance->Add($insertdata);
            }
		}
        return 1;
	}

	function getOpeningBalanceDetailByMember($memberid) {
		
		$query = $this->readdb->select("op.id,op.balancedate,op.balance,op.paymentcycle,op.debitlimit")
						->from($this->_table." as op")
						->where("op.memberid='".$memberid."' AND op.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."')")
						->get();

		return $query->row_array();
	}
        
}
 ?>            
