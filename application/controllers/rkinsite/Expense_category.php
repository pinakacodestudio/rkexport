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
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Expense Category";
		$this->viewData['module'] = "expense_category/Expense_category";
		$this->admin_headerlib->add_javascript("expensecategory","pages/expense_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
    }

    public function listing() 
	{
		$list = $this->Expense_category->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Expensecategory) {
			$row = array();
			
			$row[] = ++$counter;
			$row[] = $Expensecategory->name;
			
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
				$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'expense-category/expense-category-edit/'.$Expensecategory->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
  
			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
			  if($Expensecategory->status==1){
				  $Action .= '<span id="span'.$Expensecategory->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Expensecategory->id.',\''.ADMIN_URL.'expense_category/expense-category-enabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
			  }
			  else{
				  $Action .= '<span id="span'.$Expensecategory->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Expensecategory->id.',\''.ADMIN_URL.'expense_category/expense-category-enabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
			  }
		 	}
  
			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
				  $Action.= '<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Expensecategory->id.',"'.ADMIN_URL.'expense_category/check-expense-category-use","expensecategory","'.ADMIN_URL.'expense_category/delete-mulexpense-category") >'.delete_text.'</a>';
			  }
			
			$row[] = $Action;
  			$row[] = '<div class="checkbox table-checkbox">
					<input id="deletecheck'.$Expensecategory->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Expensecategory->id.'" name="deletecheck'.$Expensecategory->id.'" class="checkradios">
					<label for="deletecheck'.$Expensecategory->id.'"></label>
				  </div>';
			$data[] = $row;
		}
		$output = array(
						"recordsTotal" => $this->Expense_category->count_all(),
						"recordsFiltered" => $this->Expense_category->count_filtered(),
						"data" => $data,
		);
		echo json_encode($output);
	}
	
	public function expense_category_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Expense Category";
		$this->viewData['module'] = "expense_category/Add_expense_category";
		$this->admin_headerlib->add_javascript("add_expense_category","pages/add_expense_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	
	public function add_expense_category(){
        $PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Expense_category->_where = "name='".trim($PostData['name'])."'";
		$Count = $this->Expense_category->CountRecords();

        if($Count==0){

            $insertdata = array("name"=>$PostData['name'],
                      "createddate"=>$createddate,
                      "addedby"=>$addedby,
                      "modifieddate"=>$createddate,
                      "modifiedby"=>$addedby,
                      "status"=>$PostData['status']);
      
            $insertdata=array_map('trim',$insertdata);
      
            $Add = $this->Expense_category->Add($insertdata);
            if($Add){
              echo 1;
            }else{
              echo 0;
            }
          }else{
            echo 2;
          }
		
	}
	public function expense_category_enabledisable() {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Expense_category->_where = array("id" => $PostData['id']);
        $this->Expense_category->Edit($updatedata);
        echo $PostData['id'];
	}

	public function check_expense_category_use()
    {
      $count = 0;
      $PostData = $this->input->post();

      $ids = explode(",",$PostData['ids']);
      $addedby = $this->session->userdata(base_url() . 'ADMINID');
      
      $count = 0;
      foreach($ids as $row){
        // $this->db->select('id');
        // $this->db->from(tbl_customer);
        // $where = "industryid = $row";
        // $this->db->where($where);
        // $query = $this->db->get();
        // if($query->num_rows() > 0){
        //   $count++;
        // }
      }
      echo $count;
    }

  public function delete_mulexpense_category(){
    $PostData = $this->input->post();
    $ids = explode(",",$PostData['ids']);

    $count = 0;
	$ADMINID = $this->session->userdata(base_url().'ADMINID');
	foreach($ids as $row){

	    $this->Expense_category->Delete(array('id'=>$row));
		}
		
		
	}

	public function expense_category_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Expense Category";
		$this->viewData['module'] = "expense_category/Add_expense_category";
		$this->viewData['action'] = "1";//Edit
	
		//Get Industrycategory data by id
		$this->viewData['expense_category_data'] = $this->Expense_category->getExpenseCategoryDataByID($id);
		if($this->viewData['expense_category_data'] == 0)
		{
			$this->load->view(ADMINFOLDER.'Pagenotfound');
		}
		else{
			$this->admin_headerlib->add_javascript("add_expense_category","pages/add_expense_category.js");
			$this->load->view(ADMINFOLDER.'template',$this->viewData);
		}
		
	  }

	  public function update_expense_category(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
	
		$this->Expense_category->_where = "id!=".$PostData['expensecategoryid']." AND name='".trim($PostData['name'])."'";
		$Count = $this->Expense_category->CountRecords();
	
		if($Count==0){
	
		  $updatedata = array("name"=>$PostData['name'],
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby,
					"status"=>$PostData['status']);
	
		  $updatedata=array_map('trim',$updatedata);
	
		  $this->Expense_category->_where = array("id"=>$PostData['expensecategoryid']);
		  $Edit = $this->Expense_category->Edit($updatedata);
		  echo 1;
		}else{
			echo 2;
		}
	  }
	
  }

	


