<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_category extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Expense_category');
		$this->load->model('Expense_category_model','Expense_category');
	}
	public function index() {

		$this->viewData['title'] = "Expense Type";
		$this->viewData['module'] = "Expense_category/Expense_category";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Expense Type','View payment type.');
		}

		$this->admin_headerlib->add_javascript("Expense_category","pages/Expense_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {

		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Expense_category->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'expense-category/edit-expense-category/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';

                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'Expense-category/category-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                else{

                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'Expense-category/category-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';

                  
                }

            }
            //Delete and Enable/Disable Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Additional-rights","'.ADMIN_URL.'expense_category/delete_mul_expense_category") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
            
            
        	$row[] = ++$counter;
            $row[] = $datarow->expense_category;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Expense_category->count_all(),
                        "recordsFiltered" => $this->Expense_category->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function add_expense_category() {
    
		$this->viewData['title'] = "Add Expense Type";
		$this->viewData['module'] = "Expense_category/Add_expense_category";
		$this->admin_headerlib->add_javascript("add_expense_category","pages/add_expense_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_expense_category($id) {
		
		$this->viewData['title'] = "Edit Expense Type";
		$this->viewData['module'] = "expense_category/Add_expense_category";
		$this->viewData['action'] = "1";//Edit

		//Get Admission Inquiry Status Data By ID
		$this->viewData['expensecategory'] = $this->Expense_category->getAdditionalrightsDataByID($id);
		//print_r($this->viewData['additionalrightsrow']);exit;
		$this->admin_headerlib->add_javascript("add_expense_category","pages/add_expense_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function expense_category_add() {

		$PostData = $this->input->post();

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
     
		$expense_category = $PostData['expensecategory'];
		$status = $PostData['status'];

     
        $this->form_validation->set_rules('expensecategory', 'Expense category', 'required');
	
		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
                $this->Expense_category->_where = ("expense_category='".$expense_category."'");
                $Count = $this->Expense_category->CountRecords();
                
                if($Count==0){
                    
                    $insertdata = array("expense_category"=>$expense_category,
                                "status"=>$status,
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby);
                    $insertdata=array_map('trim',$insertdata);
                    
                    $Add = $this->Expense_category->Add($insertdata);
                    if($Add){
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(1,'Expense Type','Add new Expense Type.');
                        }
                        $json = 1; //Rights successfully added.
                    }else{
                        $json = 0; //Rights not added.
                    }
                }else{
                    $json = 2; //Rights already exist.
                }
           
			
		}
		echo json_encode($json);
	}
	public function update_expense_category() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$id = $PostData['expensecategoryid'];
		$expense_category = $PostData['expensecategory'];
		$status = $PostData['status'];

		$this->form_validation->set_rules('expensecategory', 'Expense Type', 'required');
        

		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
         
                $this->Expense_category->_where = ("id!=".$id." AND expense_category='".$expense_category."'");

                $Count = $this->Expense_category->CountRecords();
            
                if ($Count==0) {
                    $updatedata = array(
                        "expense_category"=>$expense_category,
                        "status"=>$status,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby
                    );

                    $updatedata=array_map('trim', $updatedata);

                    $this->Expense_category->_where = array("id"=>$id);
                    $Edit = $this->Expense_category->Edit($updatedata);
                    if ($Edit) {
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(2,'Expense Type','Edit '.$expense_category.' payment type.');
                        }
                        $json = 1; //Rights successfully updated.
                    } else {
                        $json = 0; //Rights not updated.
                    }
                } else {
                    $json = 2; //Rights already exist.
                }
          
		}
		echo json_encode($json);
	}
	public function delete_mul_expense_category(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Expense_category->_where = array("id"=>$row);
                $data = $this->Expense_category->getRecordsById();
            
                $this->general_model->addActionLog(3,'Expense Type','Delete Expense Category.');
            }
  			$this->Expense_category->Delete(array("id"=>$row));
		}
	}

    public function category_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Expense_category->_where = array("id" => $PostData['id']);
        $this->Expense_category->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Expense_category->_where = array("id"=>$PostData['id']);
            $data = $this->Expense_category->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' Expense category.';
            
            $this->general_model->addActionLog(2,'Expense category', $msg);
        }
        echo $PostData['id'];
    }

}
?>