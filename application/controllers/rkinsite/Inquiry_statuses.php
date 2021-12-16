<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Inquiry_statuses extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Inquiry_statuses');
        $this->load->model('Inquiry_statuses_model','Inquiry_statuses');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Inquiry Statuses";
        $this->viewData['module'] = "inquiry_statuses/Inquiry_statuses";

        $this->admin_headerlib->add_javascript("inquiry_statuses","pages/inquiry_statuses.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Inquiry_statuses->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Inquirystatuses) {
            $row = array();
            
            $row[] = ++$counter;
            $row[] = $Inquirystatuses->name;
            $row[] = '<div style="background: '.$Inquirystatuses->color.';" class="statusescolor"></div>';
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'inquiry-statuses/inquiry-statuses-edit/'.$Inquirystatuses->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false && $Inquirystatuses->id!=1){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Inquirystatuses->id.',"'.ADMIN_URL.'inquiry-statuses/check-inquiry-statuses-use","Inquiry&nbsp;Statuses","'.ADMIN_URL.'inquiry-statuses/delete-mul-inquiry-statuses") >'.delete_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){

                if($Inquirystatuses->status==1){
                    $Action .= '<span id="span'.$Inquirystatuses->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Inquirystatuses->id.',\''.ADMIN_URL.'inquiry_statuses/inquiry_statuses_enable_disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Inquirystatuses->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Inquirystatuses->id.',\''.ADMIN_URL.'inquiry_statuses/inquiry_statuses_enable_disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                
            }

            $row[] = $Action;
            if($Inquirystatuses->id!=1){            
                $row[] = '<div class="checkbox table-checkbox">
                            <input id="deletecheck'.$Inquirystatuses->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Inquirystatuses->id.'" name="deletecheck'.$Inquirystatuses->id.'" class="checkradios">
                            <label for="deletecheck'.$Inquirystatuses->id.'"></label>
                        </div>';
            }else{
                $row[] = "";
            }

            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Inquiry_statuses->count_all(),
            "recordsFiltered" => $this->Inquiry_statuses->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    
    public function inquiry_statuses_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Inquiry Status";
        $this->viewData['module'] = "inquiry_statuses/Add_inquiry_statuses";

        $this->admin_headerlib->add_javascript("add_inquiry_statuses","pages/add_inquiry_statuses.js");
        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function inquiry_statuses_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Inquiry Status";
        $this->viewData['module'] = "inquiry_statuses/Add_inquiry_statuses";
        $this->viewData['action'] = "1";//Edit

        //Get inquirystatuses data by id
        $this->Inquiry_statuses->_where = 'id='.$id;
        $this->viewData['inquirystatusesdata'] = $this->Inquiry_statuses->getRecordsByID();
        
        $this->admin_headerlib->add_javascript("add_inquiry_statuses","pages/add_inquiry_statuses.js");
        $this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
        $this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_inquiry_statuses(){
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Inquiry_statuses->_where = "name='".trim($PostData['name'])."'";
        $Count = $this->Inquiry_statuses->CountRecords();

        if($Count==0){

            $insertdata = array("name"=>$PostData['name'],
                        "color"=>$PostData['color'],
                        "createddate"=>$createddate,
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby,
                        "status"=>$PostData['status']);

            $insertdata=array_map('trim',$insertdata);

            $Add = $this->Inquiry_statuses->Add($insertdata);
            if($Add){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function update_inquiry_statuses(){
        $PostData = $this->input->post();
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Inquiry_statuses->_where = "id!=".$PostData['id']." AND name='".trim($PostData['name'])."'";
        $Count = $this->Inquiry_statuses->CountRecords();

        if($Count==0){

            $updatedata = array("name"=>$PostData['name'],
                        "color"=>$PostData['color'],
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby,
                        "status"=>$PostData['status']);

            $updatedata=array_map('trim',$updatedata);

            $this->Inquiry_statuses->_where = array("id"=>$PostData['id']);
            $Edit = $this->Inquiry_statuses->Edit($updatedata);
            if($Edit){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function inquiry_statuses_enable_disable() 
    {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Inquiry_statuses->_where = array("id" => $PostData['id']);
        $this->Inquiry_statuses->Edit($updatedata);
        
        echo $PostData['id'];
    }

    public function check_inquiry_statuses_use()
    {
        $count = 0;
        $PostData = $this->input->post();

        $ids = explode(",",$PostData['ids']);
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $count = 0;
        foreach($ids as $row){
            /* $this->db->select('id');
            $this->db->from(tbl_inquirytbl);
            $where = "status = $row";
            $this->db->where($where);
            $query = $this->db->get();
            if($query->num_rows() > 0){
            $count++;
            } */
        }
        echo $count;  
    }

    public function delete_mul_inquiry_statuses(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row)
        {
            if($row!=1){
                $this->Inquiry_statuses->Delete(array("id"=>$row));            
            }
        }
    }
}