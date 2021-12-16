<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscribe extends Admin_Controller {
     public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Subscribe');
        $this->load->model('Subscribe_model', 'Subscribe');
        
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Subscribe";
        $this->viewData['module'] = "subscribe/Subscribe";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Subscribe','View subscribe.');
        }
        $this->admin_headerlib->add_javascript("Subscribe", "pages/subscribe.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    
    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Subscribe->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                //$actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'subscribe/subscribe-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a> ';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'Subscribe/subscribeenabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'Subscribe/subscribeenabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'subscribe/check-subscribe-use","Product&nbsp;Unit","'.ADMIN_URL.'Subscribe/delete-mul-subscribe") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            $row[] = $datarow->email;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = $actions;
             $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Subscribe->count_all(),
                        "recordsFiltered" => $this->Subscribe->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }	
    public function check_subscribe_use() {
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        
        echo $count;
   }
    public function subscribeenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Subscribe->_where = array("id" => $PostData['id']);
        $this->Subscribe->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Subscribe->_where = array("id"=>$PostData['id']);
            $data = $this->Subscribe->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['email'].' subscribe.';
            
            $this->general_model->addActionLog(2,'Subscribe', $msg);
        }
        echo $PostData['id'];
    }

    public function delete_mul_subscribe() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {            
            $checkuse = 0;           
            
            if($checkuse == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Subscribe->_where = array("id"=>$row);
                    $data = $this->Subscribe->getRecordsById();

                    $this->general_model->addActionLog(3,'Subscribe','Delete '.$data['email'].' subscribe.');
                }
                $this->Subscribe->Delete(array('id'=>$row));
            }
        }
    }


    
}

?>