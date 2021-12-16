<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Party_type extends Admin_Controller{

    public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Party_type');
		$this->load->model('Party_type_model','Party_type');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Party Type";
		$this->viewData['module'] = "party_type/Party_type";   

		$this->viewData['partytypedata'] = $this->Party_type->getPartyTypeData();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Party Type','View party type.');
		}

		$this->admin_headerlib->add_javascript("party_type","pages/party_type.js");  
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

    public function add_party_type() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Party Type";
		$this->viewData['module'] = "party_type/Add_party_type";

		$this->admin_headerlib->add_javascript("add_party_type","pages/add_party_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function edit_party_type($id) {
		
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Party Type";
		$this->viewData['module'] = "party_type/Add_party_type";
		$this->viewData['action'] = "1";//Edit

        $this->viewData['partytypedata'] = $this->Party_type->getPartyTypeDataByID($id);
		if(empty($this->viewData['partytypedata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
		$this->admin_headerlib->add_javascript("add_party_type","pages/add_party_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
    
    public function party_type_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Party_type->_where = "partytype='".trim($PostData['partytype'])."'";
        $Count = $this->Party_type->CountRecords();

        if($Count==0){

            $insertdata = array("partytype"=>$PostData['partytype'],
                                "status"=>$PostData['status'],
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $insertdata=array_map('trim',$insertdata);
			$Add = $this->Party_type->Add($insertdata);
            
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Party Type','Add new '.$PostData['partytype'].' party type.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }

    } 

    public function update_party_type() {

		$PostData = $this->input->post();   
			
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		$partytypeid = $PostData['id'];
	
		$this->Party_type->_where = "partytype='".trim($PostData['partytype'])."' AND id<>".$partytypeid;
		$Count = $this->Party_type->CountRecords();
			
		if($Count==0){

			$updatedata = array("partytype"=>$PostData['partytype'],
								"status"=>$PostData['status'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby
							);
			$this->Party_type->_where = array("id"=>$partytypeid);
			$Edit = $this->Party_type->Edit($updatedata);
			if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Party Type','Edi '.$PostData['partytype'].' party type.');
                }
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
    }

    public function party_type_enable_disable() {

		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Party_type->_where = array("id"=>$PostData['id']);
		$this->Party_type->Edit($updatedata);

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Party_type->_where = array("id"=>$PostData['id']);
            $data = $this->Party_type->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['partytype'].' party type.';
            
            $this->general_model->addActionLog(2,'Party Type', $msg);
        }
		echo $PostData['id'];
    }

    public function check_party_type_use(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		// use for check document type available or not in other table
		foreach($ids as $row){
		    $this->readdb->select('partytypeid');
            $this->readdb->from(tbl_party);
            $where = array("partytypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
		}
		echo $count;
	}

	public function delete_mul_party_type(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$this->Party_type->_where = array("id"=>$PostData['id']);
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){

			$checkuse = 0;
            $this->readdb->select('partytypeid');
            $this->readdb->from(tbl_party);
            $where = array("partytypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
			if($checkuse == 0 && $row['id']!=1 && $row['id']!=2 && $row['id']!=3){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->Party_type->_where = array("id"=>$row);
					$data = $this->Party_type->getRecordsById();
					$this->general_model->addActionLog(3,'Party Type','Delete '.$data['partytype'].' party type.');
				}
				$this->Party_type->Delete(array("id"=>$row));
			}
		}
	}
    
}
?>