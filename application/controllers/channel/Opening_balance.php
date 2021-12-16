<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Opening_balance extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->load->model('Opening_balance_model', 'Opening_balance');
	}

	public function setopeningbalance(){
		$PostData = $this->input->post();
		//print_r($PostData);exit;
		
		echo $this->Opening_balance->setOpeningBalance($PostData);
        
	}
}
