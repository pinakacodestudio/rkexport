<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fedexaccount extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Fedexaccount');
		$this->load->model('Fedexaccount_model','Fedexaccount');
	}
	public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

		$this->viewData['title'] = "Fedex Account";
		$this->viewData['module'] = "fedexaccount/Fedexaccount";
		
        $this->viewData['fedexaccountdata'] = $this->Fedexaccount->getFedexAccountByMember($channelid,$memberid);
       
		$this->channel_headerlib->add_javascript("fedexaccount", "pages/fedexaccount.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}

	public function fedexaccountadd() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Fedex Account";
        $this->viewData['module'] = "fedexaccount/Addfedexaccount";
        
        $this->channel_headerlib->add_javascript("Fedexaccount", "pages/addfedexaccount.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
	}
	
	public function addfedexaccount() {
        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');

        $this->Fedexaccount->_where = "accountnumber='".trim($PostData['accountnumber'])."' OR meternumber='".trim($PostData['meternumber'])."' OR apikey='".trim($PostData['apikey'])."' AND channelid='".$channelid."' AND memberid='".$memberid."' ";
        $Count = $this->Fedexaccount->CountRecords();

        if ($Count == 0) {
            $insertdata = array(
                "channelid" => $channelid,
                "memberid" => $memberid,
                "accountnumber" => $PostData['accountnumber'],
                "meternumber" => $PostData['meternumber'],
                "apikey" => $PostData['apikey'],
                "password" => $PostData['password'],
                "email" => $PostData['email'],
                "status" => $PostData['status'],
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "usertype" => 1,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );

            $Add = $this->Fedexaccount->Add($insertdata);
            if ($Add) {
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2;
        }
	}

	public function fedexaccountedit($Fedexaccountid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Fedex Account";
        $this->viewData['module'] = "fedexaccount/Addfedexaccount";
        $this->viewData['action'] = "1"; //Edit

        $this->Fedexaccount->_where = array('id' => $Fedexaccountid);
        $this->viewData['fedexaccountdata'] = $this->Fedexaccount->getRecordsByID();

        $this->channel_headerlib->add_javascript("fedexaccount", "pages/addfedexaccount.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
	}
	
	public function updatefedexaccount() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $FedexaccountID = $PostData['fedexaccountid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
		
		$this->Fedexaccount->_where = "id!=".$FedexaccountID ." AND (accountnumber='".trim($PostData['accountnumber'])."' OR meternumber='".trim($PostData['meternumber'])."' OR apikey='".trim($PostData['apikey'])."') AND channelid='".$channelid."' AND memberid='".$memberid."'";
        $Count = $this->Fedexaccount->CountRecords();

        if ($Count == 0) {
			$updatedata = array(
                "accountnumber" => $PostData['accountnumber'],
                "meternumber" => $PostData['meternumber'],
                "apikey" => $PostData['apikey'],
                "password" => $PostData['password'],
                "email" => $PostData['email'],
                "status" => $PostData['status'],
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
			);
			
            $this->Fedexaccount->_where = array('id' => $FedexaccountID);
            $this->Fedexaccount->Edit($updatedata);
            echo 1;
        } else {
            echo 2;
        }
	}
	
	public function fedexaccountenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() .'MEMBERID'));
        $this->Fedexaccount->_where = array("id" => $PostData['id']);
        $this->Fedexaccount->Edit($updatedata);

        echo $PostData['id'];
	}
	
	public function checkfedexaccountuse(){
        /* $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
			$query = $this->db->query("SELECT fd.id FROM ".tbl_fedexdetail." as fd WHERE 
										fd.id IN (SELECT fedexdetailid FROM ".tbl_fedexshippingorder." WHERE fedexdetailid='".$row."')
                    					");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count; */
    }
    public function deletemulfedexaccount(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        
        foreach($ids as $row){
            /* $query = $this->db->query("SELECT fd.id FROM ".tbl_fedexdetail." as fd WHERE 
										fd.id IN (SELECT fedexdetailid FROM ".tbl_fedexshippingorder." WHERE fedexdetailid='".$row."')
                    					");

            if($query->num_rows() == 0){ */
				$this->Fedexaccount->Delete(array('id'=>$row));
            //}
            
        }
    }
	
}