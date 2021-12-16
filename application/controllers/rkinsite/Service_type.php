<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service_type extends Admin_Controller{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Service_type');
        $this->load->model('Service_type_model','Service_type');
    }
    public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Service Type";
		$this->viewData['module'] = "service_type/Service_type";    
		
		$this->viewData['servicetypedata'] = $this->Service_type->getServiceTypeData();   
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Service Type','View service type.');
		}

        $this->admin_headerlib->add_javascript("service_type","pages/service_type.js");  
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_service_type() {
		
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Service Type";
		$this->viewData['module'] = "service_type/Add_service_type";

		$this->admin_headerlib->add_javascript("add_service_type","pages/add_service_type.js");
	    $this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function edit_service_type($id) {
		
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Service Type";
		$this->viewData['module'] = "service_type/Add_service_type";
		$this->viewData['action'] = "1";//Edit

        $this->viewData['servicetypedata'] = $this->Service_type->getServiceTypeDataByID($id);
		
		if(empty($this->viewData['servicetypedata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
		$this->admin_headerlib->add_javascript("add_service_type","pages/add_service_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

    public function service_type_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Service_type->_where = array("name" => trim($PostData['name'])); 
        $Count = $this->Service_type->CountRecords();

        if($Count==0){

            $insertdata = array("name"=>$PostData['name'],
                                "status"=>$PostData['status'],
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $insertdata=array_map('trim',$insertdata);
            $Add = $this->Service_type->Add($insertdata);
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Service Type','Add new '.$PostData['name'].' service type.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }   
    
	public function update_service_type() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		$servicetypeid = $PostData['id'];
	
		$this->Service_type->_where = array("id<>"=>$servicetypeid,"name" => trim($PostData['name']));
		$Count = $this->Service_type->CountRecords();
			
		if($Count==0){

			$updatedata = array("name"=>$PostData['name'],
								"status"=>$PostData['status'],
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby
							);
			$this->Service_type->_where = array("id"=>$servicetypeid);
			$Edit = $this->Service_type->Edit($updatedata);
			if($Edit){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Service Type','Edit '.$PostData['name'].' service type.');
                }
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	
	public function service_type_enable_disable() {

		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Service_type->_where = array("id"=>$PostData['id']);
		$this->Service_type->Edit($updatedata);

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Service_type->_where = array("id"=>$PostData['id']);
            $data = $this->Service_type->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' service type.';
            
            $this->general_model->addActionLog(2,'Service Type', $msg);
        }
		echo $PostData['id'];
    }
    
    public function check_service_type_use(){
		
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		// use for check data available or not in other table
		foreach($ids as $row){
			$this->readdb->select('servicetypeid');
            $this->readdb->from(tbl_service);
            $where = array("servicetypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
		}
		echo $count;
	}

	public function delete_mul_service_type(){

		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$this->Service_type->_where = array("id"=>$PostData['id']);
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){

			$checkuse = 0;
            $this->readdb->select('servicetypeid');
            $this->readdb->from(tbl_service);
            $where = array("servicetypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
           
            if($checkuse == 0){
            
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->Service_type->_where = array("id"=>$row);
					$data = $this->Service_type->getRecordsById();
					$this->general_model->addActionLog(3,'Service Type','Delete '.$data['name'].' service type.');
				}
				$this->Service_type->Delete(array("id"=>$row));
			}
		}
	}

}
?>