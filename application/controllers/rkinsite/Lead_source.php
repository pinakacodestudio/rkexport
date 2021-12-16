<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead_source extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Lead_source');
        $this->load->model('Lead_source_model','Lead_source');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Lead Source";
        $this->viewData['module'] = "lead_source/Lead_source";

        $this->admin_headerlib->add_javascript("lead_source","pages/lead_source.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Lead_source->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Leadsource) {
            $row = array();
            
            $row[] = ++$counter;
            $row[] = $Leadsource->name;
            $row[] = '<div style="background: '.$Leadsource->color.';" class="statusescolor"></div>';
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'lead-source/lead-source-edit/'.$Leadsource->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($Leadsource->status==1){
                    $Action .= '<span id="span'.$Leadsource->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Leadsource->id.',\''.ADMIN_URL.'lead-source/lead-source-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Leadsource->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Leadsource->id.',\''.ADMIN_URL.'lead-source/lead-source-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            // if ($Leadsource->id>12) {
                if (strpos($this->viewData['submenuvisibility']['submenudelete'], ','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false) {
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Leadsource->id.',"'.ADMIN_URL.'lead-source/check-lead-source-use","Lead&nbsp;Source","'.ADMIN_URL.'lead-source/delete-mul-lead-source") >'.delete_text.'</a>';
                }
            // }
            
            $row[] = $Action;

            // if($Leadsource->id>12){            
                $row[] = '<div class="checkbox table-checkbox">
                        <input id="deletecheck'.$Leadsource->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Leadsource->id.'" name="deletecheck'.$Leadsource->id.'" class="checkradios">
                        <label for="deletecheck'.$Leadsource->id.'"></label>
                        </div>';

            /* }else{
                $row[] = "";
            } */

            
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Lead_source->count_all(),
            "recordsFiltered" => $this->Lead_source->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
  
    public function lead_source_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Lead Source";
        $this->viewData['module'] = "lead_source/Add_lead_source";

        $this->admin_headerlib->add_javascript("add_lead_source","pages/add_lead_source.js");
        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function lead_source_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Lead Source";
        $this->viewData['module'] = "lead_source/Add_lead_source";
        $this->viewData['action'] = "1";//Edit

        //Get leadsource data by id
        $this->Lead_source->_where = 'id='.$id;
        $this->viewData['leadsourcedata'] = $this->Lead_source->getRecordsByID();
        
        $this->admin_headerlib->add_javascript("add_lead_source","pages/add_lead_source.js");
        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

  public function add_lead_source(){
    $PostData = $this->input->post();
    
    $createddate = $this->general_model->getCurrentDateTime();
    $addedby = $this->session->userdata(base_url().'ADMINID');

    $this->Lead_source->_where = "name='".trim($PostData['name'])."'";
    $Count = $this->Lead_source->CountRecords();

    if($Count==0){

      $insertdata = array("name"=>$PostData['name'],
                "color"=>$PostData['color'],
                "createddate"=>$createddate,
                "addedby"=>$addedby,
                "modifieddate"=>$createddate,
                "modifiedby"=>$addedby,
                "status"=>$PostData['status']);

      $insertdata=array_map('trim',$insertdata);

      $Add = $this->Lead_source->Add($insertdata);
      if($Add){
        echo 1;
      }else{
        echo 0;
      }
    }else{
      echo 2;
    }
  }
  public function update_lead_source(){
    $PostData = $this->input->post();
    
    $modifieddate = $this->general_model->getCurrentDateTime();
    $modifiedby = $this->session->userdata(base_url().'ADMINID');

    $this->Lead_source->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
    $Count = $this->Lead_source->CountRecords();

    if($Count==0){

      $updatedata = array("name"=>$PostData['name'],
                "color"=>$PostData['color'],
                "modifieddate"=>$modifieddate,
                "modifiedby"=>$modifiedby,
                "status"=>$PostData['status']);

      $updatedata=array_map('trim',$updatedata);

      $this->Lead_source->_where = array("id"=>$PostData['id']);
      $Edit = $this->Lead_source->Edit($updatedata);
      if($Edit){
        echo 1;
      }else{
        echo 0;
      }
    }else{
      echo 2;
    }
  }

  public function lead_source_enable_disable() 
  {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Lead_source->_where = array("id" => $PostData['id']);
        $this->Lead_source->Edit($updatedata);

        echo $PostData['id'];
    }

    public function check_lead_source_use()
    {
      $count = 0;
      $PostData = $this->input->post();

      $ids = explode(",",$PostData['ids']);
      $addedby = $this->session->userdata(base_url() . 'ADMINID');
      
      $count = 0;
      foreach($ids as $row){
        /* $this->db->select('id');
        $this->db->from(tbl_customer);
        $where = "leadsourceid = $row";
        $this->db->where($where);
        $query = $this->db->get();
        if($query->num_rows() > 0){
          $count++;
        } */
      }
      echo $count;  
    }

  public function delete_mul_lead_source(){
    $PostData = $this->input->post();
    $ids = explode(",",$PostData['ids']);

    $count = 0;
    $ADMINID = $this->session->userdata(base_url().'ADMINID');
    foreach($ids as $row)
    {
        $this->Lead_source->Delete(array('id'=>$row));
    }
  }

}