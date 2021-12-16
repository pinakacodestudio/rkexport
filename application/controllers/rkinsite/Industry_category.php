<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Industry_category extends Admin_Controller {
    public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Industry_category');
		$this->load->model('Industry_category_model','Industry_category');
    }
    public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Industry Category";
		$this->viewData['module'] = "industry_category/Industry_category";
		$this->admin_headerlib->add_javascript("industrycategory","pages/industry_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
    }
	public function listing() 
	{
		$list = $this->Industry_category->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Industrycategory) {
			$row = array();
			
			$row[] = ++$counter;
			$row[] = ucwords($Industrycategory->name);
			
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
				$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'industry-category/industry-category-edit/'.$Industrycategory->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
  
			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
			  if($Industrycategory->status==1){
				  $Action .= '<span id="span'.$Industrycategory->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Industrycategory->id.',\''.ADMIN_URL.'industry_category/industry-category-enabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
			  }
			  else{
				  $Action .= '<span id="span'.$Industrycategory->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Industrycategory->id.',\''.ADMIN_URL.'industry_category/industry-category-enabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
			  }
		 	}
  
			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
				  $Action.= '<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Industrycategory->id.',"'.ADMIN_URL.'industry_category/check-industry-category-use","industrycategory","'.ADMIN_URL.'industry_category/delete-mulindustry-category") >'.delete_text.'</a>';
			  }
			
			$row[] = $Action;
  			$row[] = '<div class="checkbox table-checkbox">
					<input id="deletecheck'.$Industrycategory->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Industrycategory->id.'" name="deletecheck'.$Industrycategory->id.'" class="checkradios">
					<label for="deletecheck'.$Industrycategory->id.'"></label>
				  </div>';
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Industry_category->count_all(),
						"recordsFiltered" => $this->Industry_category->count_filtered(),
						"data" => $data,
		);
		echo json_encode($output);
    }
    public function industry_category_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Industry Category";
		$this->viewData['module'] = "industry_category/Add_industry_category";
		$this->admin_headerlib->add_javascript("add_industry_category","pages/add_industry_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

    }
	
	public function industry_category_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Industry Category";
		$this->viewData['module'] = "industry_category/Add_industry_category";
		$this->viewData['action'] = "1";//Edit
	
		//Get Industrycategory data by id
		$this->viewData['industry_category_data'] = $this->Industry_category->getIndustryCategoryDataByID($id);	
		if($this->viewData['industry_category_data'] == 0)
		{
			$this->load->view(ADMINFOLDER.'Pagenotfound');
		}
		else
		{
				
			$this->admin_headerlib->add_javascript("add_industry_category","pages/add_industry_category.js");
			$this->load->view(ADMINFOLDER.'template',$this->viewData);
		}
	  }
    public function add_industry_category(){
        $PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Industry_category->_where = "name='".trim($PostData['name'])."'";
		$Count = $this->Industry_category->CountRecords();

        if($Count==0){

            $insertdata = array("name"=>$PostData['name'],
                      "createddate"=>$createddate,
                      "addedby"=>$addedby,
                      "modifieddate"=>$createddate,
                      "modifiedby"=>$addedby,
                      "status"=>$PostData['status']);
      
            $insertdata=array_map('trim',$insertdata);
      
            $Add = $this->Industry_category->Add($insertdata);
            if($Add){
              echo 1;
            }else{
              echo 0;
            }
          }else{
            echo 2;
          }
		
	}

	public function update_industry_category(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
	
		$this->Industry_category->_where = "id!=".$PostData['industrycategoryid']." AND name='".trim($PostData['name'])."'";
		$Count = $this->Industry_category->CountRecords();
	
		if($Count==0){
	
		  $updatedata = array("name"=>$PostData['name'],
					"modifieddate"=>$modifieddate,
					"modifiedby"=>$modifiedby,
					"status"=>$PostData['status']);
	
		  $updatedata=array_map('trim',$updatedata);
	
		  $this->Industry_category->_where = array("id"=>$PostData['industrycategoryid']);
		  $Edit = $this->Industry_category->Edit($updatedata);
		  echo 1;
		}else{
			echo 2;
		}
	  }

	  public function industry_category_enabledisable() {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Industry_category->_where = array("id" => $PostData['id']);
        $this->Industry_category->Edit($updatedata);
        echo $PostData['id'];
	}
	
	public function check_industry_category_use()
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

  public function delete_mulindustry_category(){
    $PostData = $this->input->post();
    $ids = explode(",",$PostData['ids']);

    $count = 0;
	$ADMINID = $this->session->userdata(base_url().'ADMINID');
	foreach($ids as $row){

	    $this->Industry_category->Delete(array('id'=>$row));
		}
		
		
	}
    
  }

	


