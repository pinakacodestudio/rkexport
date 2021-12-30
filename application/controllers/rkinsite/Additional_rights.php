<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Additional_rights extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Additional_rights');
		$this->load->model('Additional_rights_model','Additional_rights');
	}
	public function index() {

		$this->viewData['title'] = "Additional Rights";
		$this->viewData['module'] = "additional_rights/Additional_rights";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Additional Rights','View additional rights.');
		}

		$this->admin_headerlib->add_javascript("additional_rights","pages/additional_rights.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {

		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Additional_rights->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'additional-rights/edit-additional-rights/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            //Delete and Enable/Disable Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Additional-rights","'.ADMIN_URL.'additional-rights/delete-mul-additional-rights") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
            
            
        	$row[] = ++$counter;
            $row[] = $datarow->name;
            $row[] = $datarow->slug;
            $row[] = '<target="_blank" title="Last Modified Date" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$this->general_model->displaydatetime($datarow->modifieddate).'" >'.$this->general_model->displaydatetime($datarow->createddate); 
            $row[] = '<target="_blank" title="Last Modified By" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$datarow->modifiedby.'" >'.$datarow->addedby; 
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Additional_rights->count_all(),
                        "recordsFiltered" => $this->Additional_rights->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function add_additional_rights() {
		
		$this->viewData['title'] = "Add Additional Rights";
		$this->viewData['module'] = "additional_rights/Add_additional_rights";

		$this->admin_headerlib->add_javascript("add_additional_rights","pages/add_additional_rights.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_additional_rights($id) {
		
		$this->viewData['title'] = "Edit Additional Rights";
		$this->viewData['module'] = "additional_rights/Add_additional_rights";
		$this->viewData['action'] = "1";//Edit

		//Get Admission Inquiry Status Data By ID
		$this->viewData['additionalrightsrow'] = $this->Additional_rights->getAdditionalrightsDataByID($id);
		
		$this->admin_headerlib->add_javascript("add_additional_rights","pages/add_additional_rights.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function additional_rights_add() {

		$PostData = $this->input->post();

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		$name = $PostData['name'];
		$slug = $PostData['slug'];

        $this->form_validation->set_rules('name', 'Rights Name', 'required');
        $this->form_validation->set_rules('slug', 'Slug Name', 'required');
		
		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
         
                $this->Additional_rights->_where = ("name='".$name."'");
                $Count = $this->Additional_rights->CountRecords();

                if($Count==0){
                    
                    $insertdata = array("name"=>$name,
                                "slug"=>$slug,
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby);

                    $insertdata=array_map('trim',$insertdata);
                    
                    $Add = $this->Additional_rights->Add($insertdata);
                    if($Add){
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(1,'Additional Rights','Add new additional rights.');
                        }
                        $json = array('error'=>1); //Rights successfully added.
                    }else{
                        $json = array('error'=>0); //Rights not added.
                    }
                }else{
                    $json = array('error'=>2); //Rights already exist.
                }
     
			
		}
		echo json_encode($json);
	}
	public function update_additional_rights() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$rightsid = $PostData['rightsid'];
		$name = $PostData['name'];
		$slug = $PostData['slug'];

		$this->form_validation->set_rules('name', 'Rights Name', 'required');
        $this->form_validation->set_rules('slug', 'Slug Name', 'required');

		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            if(!in_array($slug,$this->Additionalrights)){
                $json = array('error'=>4); //This rights not available in portal. 
            }else{
                $this->Additional_rights->_where = ("id!=".$rightsid." AND name='".$name."'");

                $Count = $this->Additional_rights->CountRecords();
            
                if ($Count==0) {
                    $updatedata = array("name"=>$name,
                        "slug"=>$slug,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby);

                    $updatedata=array_map('trim', $updatedata);

                    $this->Additional_rights->_where = array("id"=>$rightsid);
                    $Edit = $this->Additional_rights->Edit($updatedata);
                    if ($Edit) {
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(2,'Additional Rights','Edit '.$name.' additional rights.');
                        }
                        $json = array('error'=>1); //Rights successfully updated.
                    } else {
                        $json = array('error'=>0); //Rights not updated.
                    }
                } else {
                    $json = array('error'=>2); //Rights already exist.
                }
            }
		}
		echo json_encode($json);
	}
	public function delete_mul_additional_rights(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Additional_rights->_where = array("id"=>$row);
                $data = $this->Additional_rights->getRecordsById();
            
                $this->general_model->addActionLog(3,'Additional Rights','Delete '.$data['name'].' additional rights.');
            }
  			$this->Additional_rights->Delete(array("id"=>$row));
		}
	}
}
?>