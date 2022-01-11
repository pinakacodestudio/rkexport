<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hsn_code extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Hsn_code');
        $this->load->model('Hsn_code_model', 'Hsn_code');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Hsn Code";
        $this->viewData['module'] = "hsn_code/Hsn_code";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'HSN Code','View HSN code.');
        }
        
        $this->admin_headerlib->add_javascript("Hsn_code", "pages/hsn_code.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
		
        $list = $this->Hsn_code->get_datatables();
       
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $hsncode) {
			$row = array();
            $Action='';
            $Checkbox='';
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'hsn-code/hsn-code-edit/'.$hsncode->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($hsncode->status==1){
                    $Action .='<span id="span'.$hsncode->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$hsncode->id.',\''.ADMIN_URL.'hsn-code/hsn-code-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';

                    
                }
                else{
                    $Action .= '<span id="span'.$hsncode->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$hsncode->id.',\''.ADMIN_URL.'hsn-code/hsn-code-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
            }
			
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$hsncode->id.',"'.ADMIN_URL.'hsn-code/check-hsn-code-use","HSN&nbsp;Code","'.ADMIN_URL.'hsn-code/delete-mul-hsn-code","hsncodetable") >'.delete_text.'</a>';
                
                $Checkbox .=  '<div class="checkbox">
                  <input id="deletecheck'.$hsncode->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$hsncode->id.'" name="deletecheck'.$hsncode->id.'" class="checkradios">
                  <label for="deletecheck'.$hsncode->id.'"></label>
                </div>';
            }
            $row[] = ++$counter;
            $row[] = $hsncode->hsncode;
            $row[] = "<span class='pull-right'>".number_format($hsncode->integratedtax,2,'.',',')."</span>";
            $row[] = "<span class='pull-right'>".number_format($hsncode->integratedtax/2,2,'.',',')."</span>";
            $row[] = "<span class='pull-right'>".number_format($hsncode->integratedtax/2,2,'.',',')."</span>";
            $row[] = ucfirst($hsncode->description);
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Hsn_code->count_all(),
						"recordsFiltered" => $this->Hsn_code->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
    public function hsn_code_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Hsn Code";
        $this->viewData['module'] = "hsn_code/Add_hsn_code";
        
        $this->admin_headerlib->add_bottom_javascripts("Hsn_code", "pages/add_hsn_code.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_hsn_code() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->form_validation->set_rules('hsncode', 'HSN code', 'required|min_length[2]',array("required"=>"Please enter HSN code !","min_length"=>"HSN Code name require minimum 2 characters !"));
        $this->form_validation->set_rules('integratedtax', 'integrated tax', 'required',array("required"=>"Please enter integrated tax !"));
        $this->form_validation->set_rules('description', 'description', 'min_length[3]',array("min_length"=>"Description require minimum 3 character !"));

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
            $hsncode = trim($PostData['hsncode']);
            $integratedtax = $PostData['integratedtax'];
            $description = $PostData['description'];
            $status = $PostData['status'];

            $this->Hsn_code->_where = ("hsncode='" . trim($hsncode) . "'");
            $Count = $this->Hsn_code->CountRecords();

            if ($Count == 0) {
            
                $insertdata = array(
                    "hsncode" => $hsncode,
                    "integratedtax" => $integratedtax,
                    "description" => $description,
                    "type" => 0,
                    "status" => $status,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Hsn_code->Add($insertdata);
                if ($Add) {
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'HSN Code','Add new '.$hsncode.' HSN code.');
                    }
                    $json = array('error'=>1); // HSN Code added.
                } else {
                    $json = array('error'=>0); // HSN Code not added.
                }
            
            } else {
                $json = array('error'=>2); // HSN Code already exist.
            }
        }
        echo json_encode($json);    
    }

    public function hsn_code_edit($Hsncodeid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Hsn Code";
        $this->viewData['module'] = "hsn_code/Add_hsn_code";
        $this->viewData['action'] = "1"; //Edit

        $this->Hsn_code->_where = array('id' => $Hsncodeid);
        $this->viewData['hsncodedata'] = $this->Hsn_code->getRecordsByID();

        if(empty($this->viewData['hsncodedata']) || empty($Hsncodeid)){
            redirect("pagenotfound");
        }
       
        $this->admin_headerlib->add_bottom_javascripts("Hsn_code", "pages/add_hsn_code.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_hsn_code() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $this->form_validation->set_rules('hsncode', 'HSN code', 'required|min_length[2]',array("required"=>"Please enter HSN code !","min_length"=>"HSN Code name require minimum 2 characters !"));
        $this->form_validation->set_rules('integratedtax', 'integrated tax', 'required',array("required"=>"Please enter integrated tax !"));
        $this->form_validation->set_rules('description', 'description', 'min_length[3]',array("min_length"=>"Description require minimum 3 character !"));

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
            $HsncodeID = $PostData['hsncodeid'];
            $hsncode = trim($PostData['hsncode']);
            $integratedtax = $PostData['integratedtax'];
            $description = $PostData['description'];
            $status = $PostData['status'];

            $this->Hsn_code->_where = ("id!=" . $HsncodeID . " AND hsncode='" . $hsncode. "'");
            $Count = $this->Hsn_code->CountRecords();

            if ($Count == 0) {

                $updatedata = array(
                    "hsncode" => $hsncode,
                    "integratedtax" => $integratedtax,
                    "description" => $description,
                    "status" => $status,
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );
                $this->Hsn_code->_where = array('id' => $HsncodeID);
                $hsncodeid = $this->Hsn_code->Edit($updatedata);
                if($hsncodeid){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'HSN Code','Edit '.$hsncode.' HSN code.');
                    }
                    $json = array('error'=>1); // HSN Code added.
                } else {
                    $json = array('error'=>0); // HSN Code not added.
                }
            } else {
                $json = array('error'=>2); // HSN code already exist.
            }
        }
        echo json_encode($json);
    }

    public function hsn_code_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Hsn_code->_where = array("id" => $PostData['id']);
        $this->Hsn_code->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Hsn_code->_where = array("id"=>$PostData['id']);
            $data = $this->Hsn_code->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['hsncode'].' HSN code.';
            
            $this->general_model->addActionLog(2,'HSN Code', $msg);
        }
        echo $PostData['id'];
    }

    public function check_hsn_code_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT id FROM ".tbl_hsncode." WHERE 
                    id IN (SELECT hsncodeid FROM ".tbl_product." WHERE hsncodeid = $row) OR 
                    id IN (SELECT hsncodeid FROM ".tbl_extracharges." WHERE hsncodeid = $row)
                ");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
    public function delete_mul_hsn_code(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT id FROM ".tbl_hsncode." WHERE 
                    id IN (SELECT hsncodeid FROM ".tbl_product." WHERE hsncodeid = $row) OR 
                    id IN (SELECT hsncodeid FROM ".tbl_extracharges." WHERE hsncodeid = $row)
                    ");

            if($query->num_rows() == 0){
            
                $this->Hsn_code->_where = array("id"=>$row);
                $Hsncodedata = $this->Hsn_code->getRecordsById();
                
                if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(3,'HSN Code','Delete '.$Hsncodedata['hsncode'].' HSN code.');
				}
                $this->Hsn_code->Delete(array('id'=>$row));
            }
            
        }
    }
    
}
?>