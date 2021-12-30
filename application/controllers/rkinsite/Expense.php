<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Expense extends Admin_Controller{
    public $viewData = array();
    public function __construct(){
        parent::__construct();

        $this->load->model('Expense_model', 'Expense');
        $this->load->model('Expense_category_model', 'Expensecategory');
        $this->load->model('User_model', 'User');
        $this->viewData = $this->getAdminSettings('submenu', 'Expense');
    }
    public function index(){
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Expense";
        $this->viewData['module'] = "expense/Expense";
      
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')=>null);
        }
        $this->viewData['employee_data'] = $this->User->getactiveUserListData($where);
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("Expense", "pages/expense.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function listing(){
        $list = $this->Expense->get_datatables();
       
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $Expense) {
            $row = array();

            $row[] = ++$counter;
  
            $row[] = $Expense->employeename;
            $row[] = $Expense->expensecategoryname;
            $row[] = $this->general_model->displaydate($Expense->date);
            $row[] = "<span class='float-right'>".$Expense->amount."</span>";
            $row[] = $Expense->remarks;

            $Action = $Expensestatus='';

            $btn_cls=$sts_val=$active1=$active2=$active3="";
            if ($Expense->estatus==0) {
                $btn_cls="btn-warning";
                $sts_val="Pending";
                $active1="active";
            } elseif ($Expense->estatus==1) {
                $btn_cls="btn-success";
                $sts_val="Approve";
                $active2="active";
            } elseif ($Expense->estatus==2) {
                $btn_cls="btn-danger";
                $sts_val="Reject";
                $active3="active";
            }

            if (strpos($this->viewData['submenuvisibility']['submenuedit'], ','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false) {
                // if (($Expense->reportingto == $this->session->userdata(base_url().'ADMINID')) || (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && in_array($this->session->userdata[base_url() . 'ADMINUSERTYPE'],array_filter(explode(',',$this->viewData['submenuvisibility']['submenuviewalldata']))) ) ) {
                $Expensestatus ='<div class="dropdown">
                        <button class="btn '.$btn_cls.' btn-sm dropdown-toggle btn-raised" type="button" data-toggle="dropdown" id="expensestatusdropdown'.$Expense->id.'">'.$sts_val.'
                        <span class="caret"></span></button>
                        <div class="dropdown-menu">
                          <a href="javascript:void(0)" class="dropdown-item '.$active2.'" id="approve_btn" onclick="changeexpensestatus('.(1).','.$Expense->id.')">Approve</a>
                          <a href="javascript:void(0)" class="dropdown-item '.$active1.'" id="pending_btn" onclick="changeexpensestatus('.(0).','.$Expense->id.')">Pending</a>
                          <a href="javascript:void(0)" class="dropdown-item '.$active3.'" id="reject_btn" onclick="changeexpensestatus('.(2).','.$Expense->id.')">Reject</a>
                        </div>
                      </div>';
                $row[] = $Expensestatus;
            }
            if (strpos($this->viewData['submenuvisibility']['submenuedit'], ','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'expense/edit_expense/'.$Expense->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }

            if (strpos($this->viewData['submenuvisibility']['submenudelete'], ','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false) {
                $Action.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Expense->id.',"'.ADMIN_URL.'expense/check_expense_use","expense","'.ADMIN_URL.'expense/delete_mulexpense") >'.delete_text.'</a>';
            }

    
            if ($Expense->receipt!="") {
                $Action .= ' <a href="'.EXPENSE_RECEIPT.$Expense->receipt.'" class="btn btn-primary btn-raised btn-sm" download="'.$Expense->receipt.'"><i class="fa fa-download"></i> </a>';
            }
  
            $row[] = $Action;

            $row[] = '<div class="checkbox table-checkbox">
              <input id="deletecheck'.$Expense->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Expense->id.'" name="deletecheck'.$Expense->id.'" class="checkradios">
              <label for="deletecheck'.$Expense->id.'"></label>
            </div>';

            $data[] = $row;
        }
        $output = array(
        "draw" => $_POST['draw'],
        "recordsTotal" => $this->Expense->count_all(),
        //"recordsFiltered" => $this->Expense->count_filtered(),
        "data" => $data,
    );
        echo json_encode($output);
    }

    public function add_expense(){
        $this->viewData = $this->getAdminSettings('submenu', 'Expense');
        $this->viewData['title'] = "Add Expense";
        $this->viewData['module'] = "expense/Add_expense";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
          $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['userdata'] = $this->User->getUserListData($where);

        $this->Expensecategory->_where = array("status"=>1);
        $this->viewData['expensecategory'] = $this->Expensecategory->getRecordByID();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Expense", "pages/add_expense.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function edit_expense($id){
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Expense";
        $this->viewData['module'] = "expense/Add_expense";
        $this->viewData['action'] = "1";//Edit

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['userdata'] = $this->User->getUserListData($where);

        $this->Expensecategory->_where = array("status"=>1);
        $this->viewData['expensecategory'] = $this->Expensecategory->getRecordByID();

        //Get Expense data by id
        $this->Expense->_where = 'id='.$id;
        $this->viewData['expense_data'] = $this->Expense->getRecordsByID();
    
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Expense", "pages/add_expense.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }

    public function expense_add(){
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        if ($_FILES["receipt"]['name'] != '') {
            if ($_FILES["receipt"]['size'] != '' && $_FILES["receipt"]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                echo 2;	// FILE SIZE IS LARGE
                exit;
            }
            $receipt = uploadFile('receipt', 'EXPENSE_RECEIPT', EXPENSE_RECEIPT_PATH, '*', "", 0, EXPENSE_RECEIPT_LOCAL_PATH,'','',0);
      
            if ($receipt !== 0) {
                if ($receipt==2) {
                    echo 4;//file not uploaded
                    exit;
                }
            } else {
                echo 3; //INVALID TYPE
                exit;
            }
        } else {
            $receipt = '';
        }
        
        $insertdata = array('expensecategoryid'=>$PostData['expensecategory'],
                          'employeeid'=>$PostData['employeeid'],
                          'date'=>$this->general_model->convertdate($PostData['date']),
                          'amount'=>$PostData['amount'],
                          'remarks'=>$PostData['remarks'],
                          'reason'=>$PostData['reason'],
                          "createddate"=>$createddate,
                          "modifieddate"=>$createddate,
                          "addedby"=>$addedby,
                          "modifiedby"=>$addedby,
                          "receipt"=>$receipt);

        $insertdata=array_map('trim', $insertdata);
        $Add = $this->Expense->Add($insertdata);
        if ($Add) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function expense_update(){
        
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $oldreceipt = trim($PostData['oldreceipt']);
        $removeoldreceipt = trim($PostData['removeoldreceipt']);
      
        if ($_FILES["receipt"]['name'] != '') {
          if ($_FILES["receipt"]['size'] != '' && $_FILES["receipt"]['size'] >= UPLOAD_MAX_FILE_SIZE) {
              echo 2;	// FILE SIZE IS LARGE
              exit;
          }
          if(!empty($oldreceipt)){
            $receipt = reuploadfile('receipt', 'EXPENSE_RECEIPT', $oldreceipt ,EXPENSE_RECEIPT_PATH,"*", "", 1, EXPENSE_RECEIPT_LOCAL_PATH,'','',0);
          }else{
            $receipt = uploadFile('receipt', 'EXPENSE_RECEIPT', EXPENSE_RECEIPT_PATH, '*', "", 0, EXPENSE_RECEIPT_LOCAL_PATH,'','',0);
          }
          if ($receipt !== 0) {
              if ($receipt==2) {
                  echo 4;//file not uploaded
                  exit;
              }
          } else {
              echo 3; //INVALID TYPE
              exit;
          }
        }else if($_FILES["receipt"]['name'] == '' && $oldreceipt !='' && $removeoldreceipt=='1'){
          unlinkfile('EXPENSE', $oldreceipt, EXPENSE_RECEIPT_PATH);
          $image = '';
        }else if($_FILES["receipt"]['name'] == '' && $oldreceipt ==''){
          $receipt = '';
        }else{
          $receipt = $oldreceipt;
        }

        $updatedata = array('expensecategoryid'=>$PostData['expensecategory'],
                            'employeeid'=>$PostData['employeeid'],
                            'date'=>$this->general_model->convertdate($PostData['date']),
                            'amount'=>$PostData['amount'],
                            'remarks'=>$PostData['remarks'],
                            'reason'=>$PostData['reason'],
                            "modifieddate"=>$modifieddate,
                            "modifiedby"=>$modifiedby,
                            "receipt"=>$receipt);

        $updatedata=array_map('trim', $updatedata);

        $this->Expense->_where = array("id"=>$PostData['expenseid']);
        $Edit = $this->Expense->Edit($updatedata);
        if ($Edit) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function check_expense_use()
    {
        $count = 0;
        return $count;
    }

    public function delete_mulexpense()
    {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);

        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach ($ids as $row) {
            $this->Expense->Delete(array('id'=>$row));
        }
  
    }
    public function update_status()
    {
        $PostData = $this->input->post();
        $updatedata = array("status"=>$PostData['status']);
        $updatedata=array_map('trim', $updatedata);
        $this->Expense->_where = array("id"=>$PostData['expenseid']);
        $Edit = $this->Expense->Edit($updatedata);
        //$Edit = 1;
        if ($Edit) {
            echo 1;
        } else {
            echo 0;
        }
    }
 
}
