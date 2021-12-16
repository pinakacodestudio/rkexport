<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subscriber extends Channel_Controller {
     public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Subscriber');
        $this->load->model('Subscribe_model', 'Subscriber');
        
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Subscriber";
        $this->viewData['module'] = "subscriber/Subscriber";

        $this->channel_headerlib->add_javascript("Subscriber", "pages/subscriber.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }
    
    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $list = $this->Subscriber->get_datatables();
       
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                //$actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'subscribe/subscribe-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a> ';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'Subscriber/subscriberenabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'Subscriber/subscriberenabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.CHANNEL_URL.'subscriber/check-subscriber-use","Product&nbsp;Unit","'.CHANNEL_URL.'Subscriber/delete-mul-subscriber") >'.delete_text.'</a>';

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
                        "recordsTotal" => $this->Subscriber->count_all(),
                        "recordsFiltered" => $this->Subscriber->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }	
    public function check_subscriber_use() {
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        
        echo $count;
   }
    public function subscriberenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Subscriber->_where = array("id" => $PostData['id']);
        $this->Subscriber->Edit($updatedata);

        echo $PostData['id'];
    }

    public function delete_mul_subscriber() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {            
            $checkuse = 0;           
            
            if($checkuse == 0){
                $this->Subscriber->Delete(array('id'=>$row));
            }
        }
    }


    
}

?>