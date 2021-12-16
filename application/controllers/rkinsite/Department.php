<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends Admin_Controller 
{

  public $viewData = array();
  function __construct(){
    parent::__construct();
    $this->viewData = $this->getAdminSettings('submenu','Department');
    $this->load->model('Department_model','Department');
  }
  public function index() {
    $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
    $this->viewData['title'] = "Department";
    $this->viewData['module'] = "department/Department";

    $this->admin_headerlib->add_javascript("department","pages/department.js");
    $this->load->view(ADMINFOLDER.'template',$this->viewData);
    
  }

  public function listing() {
    
    $list = $this->Department->get_datatables();
    $data = array();
    $counter = $_POST['start'];
    foreach ($list as $Department) {
      $row = array();
      
      $row[] = ++$counter;
      $row[] = ucwords($Department->name);
      
      $Action='';

      if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
              $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'department/edit-department/'.$Department->id.'" title='.edit_title.'>'.edit_text.'</a>';
        if($Department->status==1){
            $Action .= '<span id="span'.$Department->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Department->id.',\''.ADMIN_URL.'department/department_enabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
        }
        else{
            $Action .='<span id="span'.$Department->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Department->id.',\''.ADMIN_URL.'department/department_enabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
        }
    }
      if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Department->id.',"'.ADMIN_URL.'department/check_department_use","department","'.ADMIN_URL.'department/delete_mul_department") >'.delete_text.'</a>';
            }
      
      $row[] = $Action;

      $row[] = '<div class="checkbox table-checkbox">
                  <input id="deletecheck'.$Department->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Department->id.'" name="deletecheck'.$Department->id.'" class="checkradios">
                  <label for="deletecheck'.$Department->id.'"></label>
                </div>';

      $data[] = $row;
    }
    $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Department->count_all(),
            "recordsFiltered" => $this->Department->count_filtered(),
            "data" => $data,
        );
    echo json_encode($output);
  }
  
  public function add_department() {
    $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
    $this->viewData['title'] = "Add Department";
    $this->viewData['module'] = "department/Add_department";

    $this->admin_headerlib->add_javascript("adddepartment","pages/add_department.js");
    $this->load->view(ADMINFOLDER.'template',$this->viewData);

  }
  public function edit_department($id) {
    $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
    $this->viewData['title'] = "Edit Department";
    $this->viewData['module'] = "department/Add_department";
    $this->viewData['action'] = "1";//Edit

    //Get Department data by id
    $this->Department->_where = 'id='.$id;
    $this->viewData['departmentdata'] = $this->Department->getRecordsByID();
    
    $this->admin_headerlib->add_javascript("adddepartment","pages/add_department.js");
    $this->load->view(ADMINFOLDER.'template',$this->viewData);
  }

  public function department_add(){
    
    $PostData = $this->input->post();
    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url().'ADMINID');

    $this->Department->_where = "name='".trim($PostData['name'])."'";
    $Count = $this->Department->CountRecords();

    if($Count==0){

      $insertdata = array("name"=>$PostData['name'],
                          "status"=>$PostData['status'],
                          "createddate"=>$createddate,
                          "addedby"=>$addedby,
                          "modifieddate"=>$createddate,
                          "modifiedby"=>$addedby
                        );

      $insertdata=array_map('trim',$insertdata);
      $Add = $this->Department->Add($insertdata);
      if($Add){
        echo 1;
      }else{
        echo 0;
      }
    }else{
      echo 2;
    }
  }
  public function update_department(){
    $PostData = $this->input->post();
    
    $modifieddate = $this->general_model->getCurrentDateTime();
    $modifiedby = $this->session->userdata(base_url().'ADMINID');

    $this->Department->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
    $Count = $this->Department->CountRecords();

    if($Count==0){

      $updatedata = array("name"=>$PostData['name'],
                          "status"=>$PostData['status'],
                          "modifieddate"=>$modifieddate,
                          "modifiedby"=>$modifiedby
                        );

      $updatedata=array_map('trim',$updatedata);
      $this->Department->_where = array("id"=>$PostData['id']);
      $Edit = $this->Department->Edit($updatedata);
      if($Edit){
        echo 1;
      }else{
        echo 0;
      }
    }else{
      echo 2;
    }
  }

  public function department_enabledisable() 
  {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Department->_where = array("id" => $PostData['id']);
        $this->Department->Edit($updatedata);

        echo $PostData['id'];
    }

    public function check_department_use()
    {
      $PostData = $this->input->post();
      $count = 0;
      $ids = explode(",",$PostData['ids']);
      
      echo $count;
    }

  public function delete_mul_department(){
    $PostData = $this->input->post();
    $ids = explode(",",$PostData['ids']);

    $count = 0;
    $ADMINID = $this->session->userdata(base_url().'ADMINID');
    foreach($ids as $row)
    {
        $this->Department->Delete(array('id'=>$row));
    }
    
  }

}