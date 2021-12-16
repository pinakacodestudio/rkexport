<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process extends MY_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		
	}
	
	public function getProvinceList() {
		
		$PostData = $this->input->post();
        $this->load->model('Province_model','Province');
		$this->Province->_fields = "id,name";
		$this->Province->_where = array("countryid"=>$PostData['countryid']);
		$ProvinceData = $this->Province->getRecordByID();
		echo json_encode($ProvinceData);
		
	}
    public function getCityList() {
		
		$PostData = $this->input->post();
        $this->load->model('City_model','City');
		$this->City->_fields = "id,name";
		$this->City->_where = array("stateid"=>$PostData['provinceid']);
		$CityData = $this->City->getRecordByID();
		echo json_encode($CityData);
		
	}

	public function search_seller() {
		
		$PostData = $this->input->post();
		$this->load->model('Member_model','Member');
		$json=array();

		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url() . 'CHANNELID');
		$searchcode = isset($_REQUEST['sellercode'])?$_REQUEST['sellercode']:'';
	
		if(isset($_REQUEST['ids']) && trim($_REQUEST['ids'])!==''){
			$memberdata = $this->Member->searchMemberCode($MEMBERID,$CHANNELID,$_REQUEST['ids'],0);
		}else{
			$memberdata = $this->Member->searchMemberCode($MEMBERID,$CHANNELID,$searchcode,0);
		} 
		if(!empty($memberdata)){
			if(isset($_REQUEST['ids']) && trim($_REQUEST['ids'])!==''){
				$json[] = array("id"=>$memberdata['id'],"text"=>$memberdata['membercode']);
			}else{
				$json[] = array("id"=>$memberdata['membercode'],"text"=>$memberdata['membercode']);
			}
		}
		echo json_encode($json);
	}
	public function select_seller() {
		
		$PostData = $this->input->post();

		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url() . 'CHANNELID');
		$searchcode = $PostData['sellercode'];
	
		// echo "<pre>"; print_r($memberdata); 
	
        $this->load->model('Member_model','Member');
		$memberdata = $this->Member->searchMemberCode($MEMBERID,$CHANNELID,$searchcode,0);

		if(!empty($memberdata)){
			
			$modifieddate = $this->general_model->getCurrentDateTime();
                                
			$updatedata = array('mainmemberid'=>$memberdata['id'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$MEMBERID
							);

			$this->Member->_table = tbl_membermapping;
			$this->Member->_where=array("submemberid"=>$MEMBERID);
			$this->Member->Edit($updatedata);
			echo 1;
		}else{
			echo 0;
		} 
	}
	public function reset_seller() {
		
		$PostData = $this->input->post();

		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url() . 'CHANNELID');
		$this->load->model('Member_model','Member');
		
		$this->Member->_fields = "parentmemberid";
		$this->Member->_where = array("id"=>$MEMBERID,"channelid"=>$CHANNELID);
		$memberdata = $this->Member->getRecordsById();

		if(!empty($memberdata)){
			
			$modifieddate = $this->general_model->getCurrentDateTime();
                                
			$updatedata = array('mainmemberid'=>$memberdata['parentmemberid'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$MEMBERID
							);

			$this->Member->_table = tbl_membermapping;
			$this->Member->_where=array("submemberid"=>$MEMBERID);
			$this->Member->Edit($updatedata);
			echo 1;
		}else{
			echo 0;
		} 
	}
	public function setsidebarcollapsed(){
		$PostData = $this->input->post();
		$sessionclass = $PostData['sessionclass'];
		
		if(!empty($sessionclass)){
			$sessiondata = array(
				base_url().'SIDEBAR_COLLAPASED' => $sessionclass
			);
			$this->session->set_userdata($sessiondata);
		}	
	}
	public function getactivecity(){
        
        $PostData = $this->input->post();
        $this->load->model('City_model', 'City');

		if(isset($PostData["term"])){
			$Citydata = $this->City->searchcity(1,$PostData["term"]);
		}else if(isset($PostData["ids"])){
			$Citydata = $this->City->searchcity(0,$PostData["ids"]);
		}
	    
		echo json_encode($Citydata);
	}
}