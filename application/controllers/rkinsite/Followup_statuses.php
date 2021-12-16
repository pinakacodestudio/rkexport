<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Followup_statuses extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Followup_statuses');
        $this->load->model('Followup_statuses_model','Followup_statuses');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Followup Statuses";
        $this->viewData['module'] = "followup_statuses/Followup_statuses";

        $this->admin_headerlib->add_javascript("followup_statuses","pages/followup_statuses.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Followup_statuses->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Followupstatuses) {
            $row = array();
            
            $row[] = ++$counter;
            $row[] = $Followupstatuses->name;
            $row[] = '<div style="background: '.$Followupstatuses->color.';" class="statusescolor"></div>';
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'followup-statuses/followup-statuses-edit/'.$Followupstatuses->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Followupstatuses->id.',"'.ADMIN_URL.'followup-statuses/check-followup-statuses-use","Followup&nbsp;Statuses","'.ADMIN_URL.'followup-statuses/delete-mul-followup-statuses") >'.delete_text.'</a>';
            }
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){

                if($Followupstatuses->status==1){
                    $Action .= '<span id="span'.$Followupstatuses->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Followupstatuses->id.',\''.ADMIN_URL.'followup_statuses/followup_statuses_enable_disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Followupstatuses->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Followupstatuses->id.',\''.ADMIN_URL.'followup_statuses/followup_statuses_enable_disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }

            $row[] = $Action;

            $row[] = '<div class="checkbox table-checkbox">
                        <input id="deletecheck'.$Followupstatuses->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Followupstatuses->id.'" name="deletecheck'.$Followupstatuses->id.'" class="checkradios">
                        <label for="deletecheck'.$Followupstatuses->id.'"></label>
                        </div>';

            $data[] = $row;
        }
        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->Followup_statuses->count_all(),
                "recordsFiltered" => $this->Followup_statuses->count_filtered(),
                "data" => $data,
            );
        echo json_encode($output);
    }
  
    public function followup_statuses_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Followup Status";
        $this->viewData['module'] = "followup_statuses/Add_followup_statuses";
        
        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        $this->admin_headerlib->add_javascript("add_followup_statuses","pages/add_followup_statuses.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function followup_statuses_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Followup Status";
        $this->viewData['module'] = "followup_statuses/Add_followup_statuses";
        $this->viewData['action'] = "1";//Edit

        //Get followup statuses data by id
        $this->Followup_statuses->_where = 'id='.$id;
        $this->viewData['followupstatusesdata'] = $this->Followup_statuses->getRecordsByID();
        
        $this->admin_headerlib->add_javascript("add_followup_statuses","pages/add_followup_statuses.js");
        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_followup_statuses(){
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Followup_statuses->_where = "name='".trim($PostData['name'])."'";
        $Count = $this->Followup_statuses->CountRecords();

        if($Count==0){

            $insertdata = array("name"=>$PostData['name'],
                        "createddate"=>$createddate,
                        "color"=>$PostData['color'],
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby,
                        "status"=>$PostData['status']);

            $insertdata=array_map('trim',$insertdata);

            $Add = $this->Followup_statuses->Add($insertdata);
            if($Add){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function update_followup_statuses(){
        $PostData = $this->input->post();
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Followup_statuses->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
        $Count = $this->Followup_statuses->CountRecords();

        if($Count==0){

            $updatedata = array("name"=>$PostData['name'],
                        "color"=>$PostData['color'],
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby,
                        "status"=>$PostData['status']);

            $updatedata=array_map('trim',$updatedata);

            $this->Followup_statuses->_where = array("id"=>$PostData['id']);
            $Edit = $this->Followup_statuses->Edit($updatedata);
            if($Edit){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function followup_statuses_enable_disable() 
    {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Followup_statuses->_where = array("id" => $PostData['id']);
        $this->Followup_statuses->Edit($updatedata);

        echo $PostData['id'];
    }

    public function check_followup_statuses_use()
    {
        $count = 0;
        $PostData = $this->input->post();

        $ids = explode(",",$PostData['ids']);
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $count = 0;
        foreach($ids as $row){
            /* $this->db->select('id');
            $this->db->from(tbl_followup);
            $where = "status = $row";
            $this->db->where($where);
            $query = $this->db->get();
            if($query->num_rows() > 0){
            $count++;
            } */
        }
        echo $count;  
    }

    public function delete_mul_followup_statuses(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row)
        {
            $this->Followup_statuses->Delete(array('id'=>$row));
        }
    }
}