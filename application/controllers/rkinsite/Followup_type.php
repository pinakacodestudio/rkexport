<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Followup_type extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Followup_type');
        $this->load->model('Followup_type_model','Followup_type');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Follow Up Type";
        $this->viewData['module'] = "followup_type/Followup_type";

        $this->admin_headerlib->add_javascript("followup_type","pages/followup_type.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Followup_type->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Followtype) {
            $row = array();
            
            $row[] = ++$counter;
            $row[] = $Followtype->name;
            $row[] = '<div style="background: '.$Followtype->color.';" class="statusescolor"></div>';
            
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'followup-type/followup-type-edit/'.$Followtype->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($Followtype->status==1){
                    $Action .= '<span id="span'.$Followtype->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Followtype->id.',\''.ADMIN_URL.'followup-type/followup-type-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Followtype->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Followtype->id.',\''.ADMIN_URL.'followup-type/followup-type-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Followtype->id.',"'.ADMIN_URL.'followup-type/check-followup-type-use","Followup&nbsp;Type","'.ADMIN_URL.'followup-type/delete-mul-followup-type") >'.delete_text.'</a>';
            }
            
            $row[] = $Action;

            $row[] = '<div class="checkbox table-checkbox">
                        <input id="deletecheck'.$Followtype->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Followtype->id.'" name="deletecheck'.$Followtype->id.'" class="checkradios">
                        <label for="deletecheck'.$Followtype->id.'"></label>
                    </div>';

            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Followup_type->count_all(),
            "recordsFiltered" => $this->Followup_type->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    public function followup_type_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Follow Up Type";
        $this->viewData['module'] = "followup_type/Add_followup_type";

        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");

        $this->admin_headerlib->add_javascript("add_followup_type","pages/add_followup_type.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function followup_type_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Follow Up Type";
        $this->viewData['module'] = "followup_type/Add_followup_type";
        $this->viewData['action'] = "1";//Edit

        //Get Followup type data by id
        $this->Followup_type->_where = 'id='.$id;
        $this->viewData['followtypedata'] = $this->Followup_type->getRecordsByID();

        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        
        $this->admin_headerlib->add_javascript("add_followup_type","pages/add_followup_type.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_followup_type(){
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Followup_type->_where = "name='".trim($PostData['name'])."'";
        $Count = $this->Followup_type->CountRecords();

        if($Count==0){

            $insertdata = array("name"=>$PostData['name'],
                                "color"=>$PostData['color'],
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby,
                                "status"=>$PostData['status']);

            $insertdata=array_map('trim',$insertdata);

            $Add = $this->Followup_type->Add($insertdata);
            if($Add){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function update_followup_type(){
        $PostData = $this->input->post();
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Followup_type->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
        $Count = $this->Followup_type->CountRecords();

        if($Count==0){

            $updatedata = array("name"=>$PostData['name'],
                                "color"=>$PostData['color'],
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby,
                                "status"=>$PostData['status']);

            $updatedata=array_map('trim',$updatedata);

            $this->Followup_type->_where = array("id"=>$PostData['id']);
            $Edit = $this->Followup_type->Edit($updatedata);
            if($Edit){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function followup_type_enable_disable() 
    {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Followup_type->_where = array("id" => $PostData['id']);
        $this->Followup_type->Edit($updatedata);

        echo $PostData['id'];
    }

    public function check_followup_type_use()
    {
        $count = 0;
        $PostData = $this->input->post();

        $ids = explode(",",$PostData['ids']);
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $count = 0;
        foreach($ids as $row){
            /* $this->db->select('id');
            $this->db->from(tbl_followup);
            $where = "followuptype = $row";
            $this->db->where($where);
            $query = $this->db->get();
            if($query->num_rows() > 0){
            $count++;
            } */
        }
        echo $count;  
    }

    public function delete_mul_followup_type(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row)
        {
            $this->Followup_type->Delete(array('id'=>$row));
        }
    }
}