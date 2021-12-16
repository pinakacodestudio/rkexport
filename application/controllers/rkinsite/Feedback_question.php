<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feedback_question extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Feedback_question');
        $this->load->model('Feedback_question_model', 'Feedback_question');
    }

    public function index() {
        $this->viewData['title'] = "Feedback Question";
        $this->viewData['module'] = "feedback_question/Feedback_question";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['feedbackquestiondata'] = $this->Feedback_question->getFeedbackQuestionData();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Feedback Question','View feedback question.');
        }

        $this->admin_headerlib->add_javascript("feedback_question", "pages/feedback_question.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_feedback_question() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Feedback Question";
        $this->viewData['module'] = "feedback_question/Add_feedback_question";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->admin_headerlib->add_javascript("feedback_question", "pages/add_feedback_question.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function edit_feedback_question($id) {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Feedback Question";
        $this->viewData['module'] = "feedback_question/Add_feedback_question";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['feedbackquestiondata'] = $this->Feedback_question->getFeedbackQuestionDataByID($id);
       
        $this->admin_headerlib->add_javascript("add_feedback_question","pages/add_feedback_question.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function feedback_question_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $question = trim($PostData['question']);
        $status = $PostData['status'];
     
        $this->Feedback_question->_where = array('question' => $question);
        $Count = $this->Feedback_question->CountRecords();

        if($Count==0){
            
            $this->Feedback_question->_where = array();
            $this->Feedback_question->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
            $feedbackquestion = $this->Feedback_question->getRecordsById();
            
            $maxpriority = (!empty($feedbackquestion))?$feedbackquestion['maxpriority']:1;
            
            $InsertData = array('question' => $question,
                                'priority' => $maxpriority,
                                'status' => $status,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
        
            $FeedbackQuestionID = $this->Feedback_question->Add($InsertData);
            
            if($FeedbackQuestionID){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Feedback Question','Add new '.$question.' feedback question.');
                }
                echo 1; // Question inserted successfully
            } else {
                echo 0; // Question not inserted 
            }
        } else {
            echo 2; // Question already added
        }
    }
    public function update_feedback_question() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $feedbackquestionid = trim($PostData['feedbackquestionid']);
        $question = trim($PostData['question']);
        $status = $PostData['status'];
       
        $this->Feedback_question->_where = array("id<>"=>$feedbackquestionid,'question' => $question);
        $Count = $this->Feedback_question->CountRecords();

        if($Count==0){
                
            $updateData = array('question' => $question,
                                'status'=>$status,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);

            $this->Feedback_question->_where = array('id' =>$feedbackquestionid);
            $isUpdated = $this->Feedback_question->Edit($updateData);
            
            if($isUpdated){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Feedback Question','Edit '.$question.' feedback question.');
                }
                echo 1; // Product unit update successfully
            } else {
                echo 0; // Product unit not updated
            }
        } else {
            echo 2; // Product unit already added
        }
    }
    public function feedback_question_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Feedback_question->_where = array("id" => $PostData['id']);
        $this->Feedback_question->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Feedback_question->_where = array("id"=>$PostData['id']);
            $data = $this->Feedback_question->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['question'].' feedback question.';
            
            $this->general_model->addActionLog(2,'Product Unit', $msg);
        }
        echo $PostData['id'];
    }
    public function delete_mul_feedback_question() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
        
            $this->Feedback_question->_where = array("id"=>$row);
            $questiondata = $this->Feedback_question->getRecordsById();
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Feedback Question','Delete '.$questiondata['question'].' feedback question.');
            }
            $this->Feedback_question->Delete(array('id'=>$row));
        }
    }
    public function update_priority(){

		$PostData = $this->input->post();
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();
        for($i = 0; $i < count($sequenceno); $i++){
            $updatedata[] = array(
                'priority'=>$sequenceno[$i]['sequenceno'],
                'id' => $sequenceno[$i]['id']
            );
        }
        if(!empty($updatedata)){
            $this->Feedback_question->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Feedback Question','Change feedback question priority.');
		}
        echo 1;
    }
}?>