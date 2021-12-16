<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_status extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Member_status');
        $this->load->model('Member_status_model','Member_status');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = Member_label." Status";
        $this->viewData['module'] = "member_status/Member_status";

        $this->admin_headerlib->add_javascript("Member_status","pages/member_status.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Member_status->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $memberstatus) {
            $row = array();
            
            $row[] = ++$counter;
            $row[] = ucwords($memberstatus->name);
            $row[] = '<div style="background: '.$memberstatus->color.';" class="statusescolor"></div>';
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'member-status/edit-member-status/'.$memberstatus->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$memberstatus->id.',"'.ADMIN_URL.'member-status/check-member-status-use","'.Member_label.'&nbsp;Status","'.ADMIN_URL.'member-status/delete-mul-member-status") >'.delete_text.'</a>';
            }
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){

                if($memberstatus->status==1){
                    $Action .= '<span id="span'.$memberstatus->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$memberstatus->id.',\''.ADMIN_URL.'member_status/member_status_enable_disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$memberstatus->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$memberstatus->id.',\''.ADMIN_URL.'member_status/member_status_enable_disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }
            $row[] = $Action;

            $row[] = '<div class="checkbox table-checkbox">
                        <input id="deletecheck'.$memberstatus->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$memberstatus->id.'" name="deletecheck'.$memberstatus->id.'" class="checkradios">
                        <label for="deletecheck'.$memberstatus->id.'"></label>
                        </div>';

            $data[] = $row;
        }
        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->Member_status->count_all(),
                "recordsFiltered" => $this->Member_status->count_filtered(),
                "data" => $data,
            );
        echo json_encode($output);
    }
  
    public function add_member_status() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add ".Member_label." Status";
        $this->viewData['module'] = "member_status/Add_member_status";
        
        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        $this->admin_headerlib->add_javascript("Member_status","pages/add_member_status.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
  

    public function member_status_add(){
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Member_status->_where = "name='".trim($PostData['name'])."'";
        $Count = $this->Member_status->CountRecords();

        if($Count==0){

            $insertdata = array("name"=>$PostData['name'],
                        "createddate"=>$createddate,
                        "color"=>$PostData['color'],
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby,
                        "status"=>$PostData['status']);

            $insertdata=array_map('trim',$insertdata);

            $Add = $this->Member_status->Add($insertdata);
            if($Add){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function edit_member_status($id) {
      $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "Edit ".Member_label." Status";
      $this->viewData['module'] = "member_status/Add_member_status";
      $this->viewData['action'] = "1";//Edit

      //Get followup statuses data by id
      $this->Member_status->_where = 'id='.$id;
      $this->viewData['memberstatusdata'] = $this->Member_status->getRecordsByID();
      
      $this->admin_headerlib->add_javascript("Member_status","pages/add_member_status.js");
      $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
      $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
      $this->load->view(ADMINFOLDER.'template',$this->viewData);
  }
    public function update_member_status(){
        $PostData = $this->input->post();
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Member_status->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
        $Count = $this->Member_status->CountRecords();

        if($Count==0){

            $updatedata = array("name"=>$PostData['name'],
                        "color"=>$PostData['color'],
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby,
                        "status"=>$PostData['status']);

            $updatedata=array_map('trim',$updatedata);

            $this->Member_status->_where = array("id"=>$PostData['id']);
            $Edit = $this->Member_status->Edit($updatedata);
            if($Edit){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function member_status_enable_disable() {
        $this->viewData = $this->getAdminSettings('submenu', 'Member_status');
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Member_status->_table = tbl_memberstatus;
        $this->Member_status->_where = array("id" => $PostData['id']);
        $this->Member_status->Edit($updatedata);

        echo $PostData['id'];
    }

    public function check_member_status_use()
    {
        $count = 0;
        $PostData = $this->input->post();

        $ids = explode(",",$PostData['ids']);
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $count = 0;
        
        echo $count;  
    }

    public function delete_mul_member_status(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row)
        {
            $this->Member_status->Delete(array('id'=>$row));
        }
    }
}