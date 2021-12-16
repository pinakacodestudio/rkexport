<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Feedback extends Channel_Controller {

    public $viewData = array();
    public $contenttype;
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Feedback');
        $this->load->model('Feedback_model', 'Feedback');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Feedback";
        $this->viewData['module'] = "feedback/Feedback";
        $this->viewData['VIEW_STATUS'] = "1";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['ChannelData'] = $this->Channel->getCurrentOrUpperChannelListByMember($MEMBERID,'');
        
        $this->viewData['feedbackdata'] = $this->Feedback->get_all_listdata('id','DESC');
        $this->channel_headerlib->add_javascript("Feedback", "pages/feedback.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $list = $this->Feedback->get_datatables();
        $data = array();
        $counter = $srno = $_POST['start'];
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        foreach ($list as $feedback) {
            $row = array();
            $channellabel = '';
            $membername = '';
            $check = '';

            if($feedback->channelid != 0){
                $key = array_search($feedback->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($feedback->memberid == $MEMBERID){
                    $membername = $channellabel.ucwords($feedback->name).' ('.$feedback->membercode.')';
                }else{
                    $membername = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$feedback->memberid.'" target="_blank" title="'.$feedback->name.'">'.ucwords($feedback->name).' ('.$feedback->membercode.')'."</a>";
                }
            }

            $row[] = ++$counter;
            $row[] = $membername;
            $row[] = $feedback->subject;
            $row[] = '<button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="getfeedbackmessage('.$feedback->id.')">View Feedback</button>';

            $row[] = date_format(date_create($feedback->createddate), 'd M Y h:i A');
            
            $Action='';
            
            
            $Action .= '<a class="'.reply_class.'" href="mailto:'.$feedback->email.'?subject='.$feedback->subject.'" title="'.reply_title.'">'.reply_text.'</a>';
                
            if($feedback->memberid == $MEMBERID){
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                    
                    $Action .= '<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick="deleterow('.$feedback->id.',&quot;&quot;,&quot;Feedback&quot;,&quot;'.CHANNEL_URL.'feedback/delete-mul-feedback&quot;,&quot;feedbacktable&quot;)">'.delete_text.'</a>';
                }

                $check = '<td><div class="checkbox">
                                <input id="deletecheck'.$feedback->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$feedback->id.'" name="deletecheck'.$feedback->id.'" class="checkradios">
                                <label for="deletecheck'.$feedback->id.'"></label>
                            </div></td>';
            }
            $row[] = $Action;
            $row[] = $check;                
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Feedback->count_all(),
                        "recordsFiltered" => $this->Feedback->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }
    
    function getfeedbackmessagebyid(){
        $PostData = $this->input->post();
        
        $this->Feedback->_fields = "id,message";
        $this->Feedback->_where = "id=".$PostData['id'];
        $data = $this->Feedback->getRecordsByID();
        
        $pagetitle='Feedback';
    
        echo json_encode(array('pagetitle'=>$pagetitle,'description'=>$data['message']));
    }
    public function delete_mul_feedback() {
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
       
        foreach ($ids as $row) {
            
            $deleteData= array("id"=> $row);
            $this->Feedback->Delete($deleteData);          
      
        }
    }

  /* public function dealerenabledisable() {
        $this->viewData = $this->getAdminSettings('submenu', 'Feedback');
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Feedback->_where = array("id" => $PostData['id']);
        $this->Feedback->Edit($updatedata);

        echo $PostData['id'];
    } */
    
    
}