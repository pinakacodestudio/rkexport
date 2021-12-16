<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Designation extends Admin_Controller 
{

  public $viewData = array();
  function __construct(){
    parent::__construct();
    $this->viewData = $this->getAdminSettings('submenu','Designation');
    $this->load->model('Designation_model','Designation');
  }
  public function index() {
    $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
    $this->viewData['title'] = "Designation";
    $this->viewData['module'] = "designation/Designation";

    $this->admin_headerlib->add_javascript("designation","pages/designation.js");
    $this->load->view(ADMINFOLDER.'template',$this->viewData);
  }

  public function listing() {
    
    $list = $this->Designation->get_datatables();
    $data = array();
    $counter = $_POST['start'];
    foreach ($list as $Designation) {
      $row = array();
      $Action='';
      
      $row[] = ++$counter;
      $row[] = ucwords($Designation->name);
      
      if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
        $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'designation/edit-designation/'.$Designation->id.'" title='.edit_title.'>'.edit_text.'</a>';
      }
      if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
          if($Designation->status==1){
              $Action .= '<span id="span'.$Designation->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Designation->id.',\''.ADMIN_URL.'designation/designation_enabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
          }
          else{
              $Action .='<span id="span'.$Designation->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Designation->id.',\''.ADMIN_URL.'designation/designation_enabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
          }
      }

      if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
          $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Designation->id.',"'.ADMIN_URL.'designation/check_designation_use","designation","'.ADMIN_URL.'designation/delete_mul_designation") >'.delete_text.'</a>';
      }
      
      $row[] = $Action;

      $row[] = '<div class="checkbox table-checkbox">
                  <input id="deletecheck'.$Designation->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Designation->id.'" name="deletecheck'.$Designation->id.'" class="checkradios">
                  <label for="deletecheck'.$Designation->id.'"></label>
                </div>';

      $data[] = $row;
    }
    $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Designation->count_all(),
            "recordsFiltered" => $this->Designation->count_filtered(),
            "data" => $data,
        );
    echo json_encode($output);
  }

  
  public function add_designation() {
    $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
    $this->viewData['title'] = "Add Designation";
    $this->viewData['module'] = "designation/Add_designation";
    
    $this->admin_headerlib->add_javascript("Designation","pages/add_designation.js");
    $this->load->view(ADMINFOLDER.'template',$this->viewData);
  }
  public function edit_designation($id) {
    $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
    $this->viewData['title'] = "Edit Designation";
    $this->viewData['module'] = "designation/Add_designation";
    $this->viewData['action'] = "1";//Edit

    //Get designation data by id
    $this->Designation->_where = 'id='.$id;
    $this->viewData['designationdata'] = $this->Designation->getRecordsByID();
    
    $this->admin_headerlib->add_javascript("Designation","pages/add_designation.js");
    $this->load->view(ADMINFOLDER.'template',$this->viewData);
  }

  public function designation_add(){
    $PostData = $this->input->post();
    
    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url().'ADMINID');

    $this->Designation->_where = "name='".trim($PostData['name'])."'";
    $Count = $this->Designation->CountRecords();

    if($Count==0){

      $insertdata = array("name"=>$PostData['name'],
                          "status"=>$PostData['status'],
                          "createddate"=>$createddate,
                          "addedby"=>$addedby,
                          "modifieddate"=>$createddate,
                          "modifiedby"=>$addedby
                        );

      $insertdata=array_map('trim',$insertdata);
      $Add = $this->Designation->Add($insertdata);
      if($Add){
        echo 1;
      }else{
        echo 0;
      }
    }else{
      echo 2;
    }
  }
  public function update_designation(){
    $PostData = $this->input->post();
    
    $modifieddate = $this->general_model->getCurrentDateTime();
    $modifiedby = $this->session->userdata(base_url().'ADMINID');

    $this->Designation->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
    $Count = $this->Designation->CountRecords();

    if($Count==0){

      $updatedata = array("name"=>$PostData['name'],
                          "status"=>$PostData['status'],
                          "modifieddate"=>$modifieddate,
                          "modifiedby"=>$modifiedby
                        );

      $updatedata=array_map('trim',$updatedata);
      $this->Designation->_where = array("id"=>$PostData['id']);
      $Edit = $this->Designation->Edit($updatedata);
      if($Edit){
        echo 1;
      }else{
        echo 0;
      }
    }else{
      echo 2;
    }
  }

  public function designation_enabledisable() 
  {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Designation->_where = array("id" => $PostData['id']);
        $this->Designation->Edit($updatedata);

        echo $PostData['id'];
    }

    public function check_designation_use()
    {
      $PostData = $this->input->post();
      $count = 0;
      $ids = explode(",",$PostData['ids']);
    
      echo $count;
    }

  public function delete_mul_designation(){
    $PostData = $this->input->post();
    $ids = explode(",",$PostData['ids']);

    $count = 0;
    $ADMINID = $this->session->userdata(base_url().'ADMINID');
    foreach($ids as $row)
    {
        $this->Designation->Delete(array('id'=>$row));
    }
  }
  

}