<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_type extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Expense_type');
		$this->load->model('Expense_category_model','Expense_type');
	}
	public function index() {

		$this->viewData['title'] = "Expense Type";
		$this->viewData['module'] = "expense_type/Expense_type";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Expense Type','View payment type.');
		}

		$this->admin_headerlib->add_javascript("expense_type","pages/expense_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {

		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Expense_type->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'expense-type/edit-expense-type/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            //Delete and Enable/Disable Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Additional-rights","'.ADMIN_URL.'expense_type/delete_mul_expense_type") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
            
            
        	$row[] = ++$counter;
            $row[] = $datarow->expense_type;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Expense_type->count_all(),
                        "recordsFiltered" => $this->Expense_type->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function add_expense_type() {
    
		$this->viewData['title'] = "Add Expense Type";
		$this->viewData['module'] = "expense_type/Add_expense_type";
		$this->admin_headerlib->add_javascript("add_expense_type","pages/add_expense_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_expense_type($id) {
		
		$this->viewData['title'] = "Edit Expense Type";
		$this->viewData['module'] = "expense_type/Add_expense_type";
		$this->viewData['action'] = "1";//Edit

		//Get Admission Inquiry Status Data By ID
		$this->viewData['additionalrightsrow'] = $this->Expense_type->getAdditionalrightsDataByID($id);
		
		$this->admin_headerlib->add_javascript("add_expense_type","pages/add_expense_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function expense_type_add() {

		$PostData = $this->input->post();

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		
		$expense_type = $PostData['expense_type'];

        $this->form_validation->set_rules('expense_type', 'Expense Type', 'required');
	
		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
                $this->Expense_type->_where = ("expense_type='".$expense_type."'");
                $Count = $this->Expense_type->CountRecords();
                
                if($Count==0){
                    
                    $insertdata = array("expense_type"=>$expense_type,
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby);
                    $insertdata=array_map('trim',$insertdata);
                    
                    $Add = $this->Expense_type->Add($insertdata);
                    if($Add){
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(1,'Expense Type','Add new Expense Type.');
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
	public function update_expense_type() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$id = $PostData['id'];
		$expense_type = $PostData['expense_type'];

		$this->form_validation->set_rules('expense_type', 'Expense Type', 'required');
        

		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
         
                $this->Expense_type->_where = ("id!=".$id." AND expense_type='".$expense_type."'");

                $Count = $this->Expense_type->CountRecords();
            
                if ($Count==0) {
                    $updatedata = array(
                        "expense_type"=>$expense_type,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby
                    );

                    $updatedata=array_map('trim', $updatedata);

                    $this->Expense_type->_where = array("id"=>$id);
                    $Edit = $this->Expense_type->Edit($updatedata);
                    if ($Edit) {
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(2,'Expense Type','Edit '.$expense_type.' payment type.');
                        }
                        $json = array('error'=>1); //Rights successfully updated.
                    } else {
                        $json = array('error'=>0); //Rights not updated.
                    }
                } else {
                    $json = array('error'=>2); //Rights already exist.
                }
          
		}
		echo json_encode($json);
	}
	public function delete_mul_expense_type(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Expense_type->_where = array("id"=>$row);
                $data = $this->Expense_type->getRecordsById();
            
                $this->general_model->addActionLog(3,'Expense Type','Delete '.$data['name'].' Expense Type.');
            }
  			$this->Expense_type->Delete(array("id"=>$row));
		}
	}
}
?>