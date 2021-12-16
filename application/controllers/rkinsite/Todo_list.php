<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Todo_list extends Admin_Controller
{
    public $viewData = array();
    public function __construct()
    {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'todo_list');
        $this->load->model('Todo_list_model', 'Todo_list');
        $this->load->model('User_model', 'User');
    }

    public function index()
    {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "To Do List";
        $this->viewData['module'] = "todo_list/Todo_list";
        
        $this->viewData['employee_data'] = $this->Todo_list->getUser();
        
        $this->admin_headerlib->add_javascript("Todo_list", "pages/todo_list.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("bootstrap-datepicker", "bootstrap-datetimepicker/bootstrap-datetimepicker.css");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }


    public function listing()
    {
        $list = $this->Todo_list->get_datatables();
        // pre($list);
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Todo_list) {
            $row = array();
            $row['DT_RowId'] = $Todo_list->id;
            $row[] = ++$counter;            
            $row[] = ucwords($Todo_list->name);
                    
            $row[] = wordwrap($Todo_list->list,40,"<br>\n");
            $row[] = $Todo_list->assignby;
          
            if (strpos($this->viewData['submenuvisibility']['submenuedit'], ','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false) {
             
                $btn_cls=$sts_val=$active1=$active2="";
                if ($Todo_list->status==0) {
                    $btn_cls="btn-warning";
                    $sts_val="Pending";
                    $active1="active";
                } elseif ($Todo_list->status==1) {
                    $btn_cls="btn-success";
                    $sts_val="Done";
                    $active2="active";
                } 
                $row[] ='<div class="dropdown">
                    <button class="btn '.$btn_cls.' btn-raised btn-sm dropdown-toggle" type="button" data-toggle="dropdown" id="liststatusdropdown'.$Todo_list->id.'">'.$sts_val.'
                    <span class="caret"></span></button>
                    <ul class="dropdown-menu">
                    <li><a href="javascript:void(0)" class="dropdown-item '.$active2.'" id="done_btn" onclick="changeliststatus('.(1).','.$Todo_list->id.')">Done</a></li>
                    <li><a href="javascript:void(0)" class="dropdown-item '.$active1.'" id="pending_btn" onclick="changeliststatus('.(0).','.$Todo_list->id.')">Pending</a></li>
                    </ul>
                </div>';            
            }

            // $date = '<a class="a-without-link btn-tooltip mt-1" id="date'.$Todo_list->id.'" onclick="'."copyelementtext('date".$Todo_list->id."','".$this->general_model->displaydatetime($Todo_list->createdate,'d/m/Y')."')".'" onmouseout="resettooltiptitle(\'date'.$Todo_list->id.'\')" data-toggle="tooltip" style="word-break: break-word !important;">'.$this->general_model->displaydatetime($Todo_list->createdate,'d/m/Y').'</a>';
            // $date .= '<a class="a-without-link btn-tooltip mt-1" id="datetime'.$Todo_list->id.'" onclick="'."copyelementtext('datetime".$Todo_list->id."','".$this->general_model->displaydatetime($Todo_list->createdate,'d/m/Y h:i A')."')".'" onmouseout="resettooltiptitle(\'datetime'.$Todo_list->id.'\')"  data-toggle="tooltip" style="word-break: break-word !important;"> '.$this->general_model->displaydatetime($Todo_list->createdate,'h:i A').'</a>';
            $row[] = $this->general_model->displaydatetime($Todo_list->createdate);

            //$row[] = $this->general_model->displaydatetime($Todo_list->createddate);
            
            $Action='';
                 
            if (strpos($this->viewData['submenuvisibility']['submenuedit'], ','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false) {
                $Action .= ' <a class="'.edit_class.'" href="'.ADMIN_URL.'todo-list/todo-list-edit/'.$Todo_list->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Todo_list->id.',"'.ADMIN_URL.'todo-list/check-todo-list-use","todo-list","'.ADMIN_URL.'todo-list/delete-mul-todo-list") >'.delete_text.'</a>';
            }

          
            $row[] = $Action;

            $row[] = '<span style="display: none;">'.$Todo_list->priority.'</span><div class="checkbox table-checkbox">
	                  <input id="deletecheck'.$Todo_list->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Todo_list->id.'" name="deletecheck'.$Todo_list->id.'" class="checkradios">
	                  <label for="deletecheck'.$Todo_list->id.'"></label>
	                </div>';

            $data[] = $row;
        }
        $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->Todo_list->count_all(),
                "recordsFiltered" => $this->Todo_list->count_filtered(),
                "data" => $data,
            );
        echo json_encode($output);
    }

    public function todo_list_add()
    {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add To Do List";
        $this->viewData['module'] = "todo_list/Add_todo_list";
       
        $this->viewData['employee_data'] = $this->Todo_list->getUser();
        // pre($this->viewData['employee_data']);
        
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("add_todo_list", "pages/add_todo_list.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function add_todo_list()
    {
        $PostData = $this->input->post();
        // pre($PostData);
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $list = explode(",",$PostData['todolist']);

        $this->Todo_list->_fields = 'max(priority) as priority';
        $this->Todo_list->_where = 'employeeid = "'.$PostData['employeeid'].'"';
        $priority = $this->Todo_list->getRecordsByID();

        $pcount = $priority['priority'];
        
        if(isset($list) && count($list)>0){
            foreach($list as $k=>$mne){                
               if($mne != ''){

                    if(is_numeric($mne)){
                        $this->Todo_list->_fields = 'list';
                        $this->Todo_list->_where = 'id='.$mne;
                        $listbyid = $this->Todo_list->getRecordsByID();
                        $mne = $listbyid['list'];
                    }
                    $addtodolist[] = array("employeeid"=>$PostData['employeeid'],
                                            "list"=>$mne, 
                                            "priority"=>++$pcount, 
                                            "status"=>0,
                                            "createdate"=>$createddate,
                                            "modifieddate"=>$createddate,
                                            "addedby"=>$addedby,
                                            "modifiedby"=>$addedby);
                }
            }
        }          
       
        if(count($addtodolist)>0){						
            $this->Todo_list->add_batch($addtodolist);
            echo 1;
        } else {
            echo 0;
        }
    }

    public function todo_list_edit($id)
    {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit To Do List";
        $this->viewData['module'] = "todo_list/Add_todo_list";
        $this->viewData['action'] = "1";//Edit

        $this->viewData['employee_data'] = $this->Todo_list->getUser();

        $this->Todo_list->_where = 'id='.$id;
        $this->viewData['todolistdata'] = $this->Todo_list->getRecordsByID();
        // pre($this->viewData['todolistdata']);
        
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("add_todo_list", "pages/add_todo_list.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function update_todo_list()
    {
        $PostData = $this->input->post();
        // pre($PostData);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $list = explode(",",$PostData['todolist']);
        // pre($list);
        

        $this->Todo_list->_where = 'id='.$PostData['id'];
        $todolistdata = $this->Todo_list->getRecordsByID();
        // pre($todolistdata);
        $prelist = $todolistdata['list'];
        $recentlist = array();
        foreach($list as $key => $todolist){
            if(is_numeric($todolist)){
                // echo($todolist);
                $this->Todo_list->_fields = 'list';
                $this->Todo_list->_where = 'id='.$todolist;
                $listbyid = $this->Todo_list->getRecordsByID();
                // print_r($listbyid);
               
                $recentlist[] = $listbyid['list'];
            }
        }
        // echo $prelist;exit;
        if(!in_array($prelist,$recentlist)){
            
            $this->Todo_list->Delete(array('list'=>$prelist,'id'=>$PostData['id']));
        }
        $this->Todo_list->_fields = 'max(priority) as priority';
        $this->Todo_list->_where = 'employeeid = "'.$PostData['employeeid'].'"';
        $priority = $this->Todo_list->getRecordsByID();

        $pcount = $priority['priority'];
        // pre($prelist);
        // pre($list);
        foreach($list as $key => $mne)
        {
            if($mne!=''){
                if(is_numeric($mne)){
                    // echo($mne);
                    $this->Todo_list->_fields = 'list';
                    $this->Todo_list->_where = 'id='.$mne;
                    $listbyid = $this->Todo_list->getRecordsByID();
                    // print_r($listbyid);
                    $mne = $listbyid['list'];
                }
                if($mne != $prelist){
                    $insertata = array("employeeid"=>$PostData['employeeid'],
                                    "list"=>$mne,
                                    "status"=>0,
                                    "priority"=>$pcount+1,
                                    "createdate"=>$modifieddate,
                                    "modifieddate"=>$modifieddate,
                                    "modifiedby"=>$modifiedby,
                                    "addedby"=>$modifiedby);

                    $insertata=array_map('trim', $insertata);
                    $ID = $this->Todo_list->Add($insertata);
                }
            }
        }
        
        
        if ($ID) {
            echo 1;
        } else {
            echo 0;
        }
    }
 
    public function check_todo_list_use()
    {
        echo $count = 0;
    }

    public function delete_mul_todo_list()
    {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach ($ids as $row) {
            $this->db->where('id', $row);
            $this->db->delete(tbl_todolist);
        }
    }

    public function changestatus()
    {
        $PostData = $this->input->post();
        
        $updatedata = array("status"=>$PostData['status']);
        $updatedata=array_map('trim', $updatedata);
        $this->Todo_list->_where = array("id"=>$PostData['id']);
        $Edit = $this->Todo_list->Edit($updatedata);
    
        if ($Edit) {           
            echo 1;
        } else {
            echo 0;
        }
    }

    public function gettodolist(){
        $PostData = $this->input->post();
        // pre($PostData);
        if(isset($PostData["term"])){
            $todolistdata = $this->Todo_list->searchtodolist(1,$PostData["term"]);
        }else if(isset($PostData["ids"])){
            $todolistdata = $this->Todo_list->searchtodolist(0,$PostData["ids"]);
        }
        // pre($todolistdata);
        echo json_encode($todolistdata);
    }

    public function addtodolistbypopup()
    {
        $PostData = $this->input->post();
       
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $list = explode(",",$PostData['todolist']);
        $this->Todo_list->_fields = 'max(priority) as priority';
        $this->Todo_list->_where = 'employeeid = "'.$addedby.'"';
        $priority = $this->Todo_list->getRecordsByID();

        $pcount = $priority['priority'];
        if(isset($list) && count($list)>0){
            foreach($list as $k=>$mne){
                $addtodolist[] = array("employeeid"=>$addedby,
                                        "list"=>$mne, 
                                        "status"=>0,
                                        "priority"=>++$pcount,
                                        "createdate"=>$createddate,
                                        "modifieddate"=>$createddate,
                                        "addedby"=>$addedby,
                                        "modifiedby"=>$addedby);
            }
        }          
    //    print_r($addtodolist);exit;
        if(count($addtodolist)>0){						
            $this->Todo_list->add_batch($addtodolist);
            echo 1;
        } else {
            echo 0;
        }
    }

    public function get_todo_list_by_id(){
        $PostData = $this->input->post();
        $this->Todo_list->_fields = "*";
        $this->Todo_list->_table = tbl_todolist;
        $this->Todo_list->_where = "id = '".$PostData['tdlid']."'";
        $row = $this->Todo_list->getRecordsByID();       
        echo json_encode($row);
    }

    public function updatetodolistbypopup()
    {
        $PostData = $this->input->post();
        // pre($PostData);exit;
        $id = $PostData['tdlid'];
        $list = $PostData['todolist1'];

        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $updatedata = array("list"=>$list,                           
                            "modifieddate"=>$modifieddate,
                            "modifiedby"=>$modifiedby);

        $updatedata=array_map('trim', $updatedata);

        $this->Todo_list->_where = array("id"=>$id);
        $Edit = $this->Todo_list->Edit($updatedata);
        if ($Edit) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function updatepriority(){

        $PostData = $this->input->post();
        // pre($PostData);exit;
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();

        for($i = 0; $i < count($sequenceno); $i++){
            $updatedata[] = array(
                'priority'=>$sequenceno[$i]['sequenceno'],
                'id' => $sequenceno[$i]['id']
            );
        }
       
        if(!empty($updatedata)){
            $this->db->update_batch(tbl_todolist,$updatedata,"id");           
		}
		
        echo 1;
    }
    
}
