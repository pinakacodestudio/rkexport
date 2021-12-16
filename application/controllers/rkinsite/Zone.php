<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Zone extends Admin_Controller 
{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Zone');
        $this->load->model('Zone_model','Zone');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Zone";
        $this->viewData['module'] = "zone/Zone";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Zone','View zone.');
        }

        $this->admin_headerlib->add_javascript("zone","pages/zone.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Zone->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Zone) {
            $row = array();
            
            $row[] = ++$counter;
            $row[] = $Zone->zonename;
        
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'zone/zone-edit/'.$Zone->id.'" title='.edit_title.'>'.edit_text.'</a>';

                if($Zone->status==1){
                    $Action .= ' <span id="span'.$Zone->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Zone->id.',\''.ADMIN_URL.'zone/zone-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .=' <span id="span'.$Zone->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Zone->id.',\''.ADMIN_URL.'zone/zone-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }

            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Zone->id.',"'.ADMIN_URL.'zone/check-zone-use","Zone","'.ADMIN_URL.'zone/delete-mul-zone") >'.delete_text.'</a>';
            }
        
            $row[] = $Action;

            $row[] = '<div class="checkbox table-checkbox">
                    <input id="deletecheck'.$Zone->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Zone->id.'" name="deletecheck'.$Zone->id.'" class="checkradios">
                    <label for="deletecheck'.$Zone->id.'"></label>
                    </div>';

            $data[] = $row;
        }
        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->Zone->count_all(),
                "recordsFiltered" => $this->Zone->count_filtered(),
                "data" => $data,
        );
        echo json_encode($output);
    }
  
  
    public function zone_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Zone";
        $this->viewData['module'] = "zone/Add_zone";

        $this->admin_headerlib->add_javascript("add_zone","pages/add_zone.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function zone_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Zone";
        $this->viewData['module'] = "zone/Add_zone";
        $this->viewData['action'] = "1";//Edit

        //Get zone data by id
        $this->Zone->_where = 'id='.$id;
        $this->viewData['zonedata'] = $this->Zone->getRecordsByID();
        
        $this->admin_headerlib->add_javascript("add_zone","pages/add_zone.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_zone(){
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->Zone->_where = "zonename='".trim($PostData['zone'])."'";
        $Count = $this->Zone->CountRecords();

        if($Count==0){

            $insertdata = array("zonename"=>$PostData['zone'],
                        "createddate"=>$createddate,
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby,
                        "status"=>$PostData['status']);

            $insertdata=array_map('trim',$insertdata);

            $Add = $this->Zone->Add($insertdata);
            if($Add){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Zone','Add new '.$PostData['zone'].' zone.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function update_zone(){
        $PostData = $this->input->post();
        
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $this->Zone->_where = "id!=".$PostData['id']." AND zonename='".trim($PostData['zone'])."'";
        $Count = $this->Zone->CountRecords();

        if($Count==0){

            $updatedata = array("zonename"=>$PostData['zone'],
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby,
                        "status"=>$PostData['status']);

            $updatedata=array_map('trim',$updatedata);

            $this->Zone->_where = array("id"=>$PostData['id']);
            $Edit = $this->Zone->Edit($updatedata);
            if($Edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Zone','Edit '.$PostData['zone'].' zone.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function zone_enable_disable() 
    {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Zone->_where = array("id" => $PostData['id']);
        $this->Zone->Edit($updatedata);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Zone->_where = array("id"=>$PostData['id']);
            $data = $this->Zone->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['zonename'].' zone.';
            
            $this->general_model->addActionLog(2,'Zone', $msg);
        }
        echo $PostData['id'];
    }

    public function check_zone_use()
    {
        $count = 0;
        $PostData = $this->input->post();

        $ids = explode(",",$PostData['ids']);
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $count = 0;
        foreach($ids as $row){
            /* $this->db->select('id');
            $this->db->from(tbl_user);
            $where = "zoneid = $row";
            $this->db->where($where);
            $query = $this->db->get();
            if($query->num_rows() > 0){
                $count++;
            } */
        }
        echo $count;
    }

    public function delete_mul_zone(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row)
        {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Zone->_where = array("id"=>$row);
                $data = $this->Zone->getRecordsById();

                $this->general_model->addActionLog(3,'Zone','Delete '.$data['zonename'].' zone.');
            }
            $this->Zone->Delete(array('id'=>$row));
        }
    }

}