<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Extra_charges extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Extra_charges');
        $this->load->model('Extra_charges_model', 'Extra_charges');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Extra Charges";
        $this->viewData['module'] = "extra_charges/Extra_charges";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Extra Charges','View extra charges.');
        }

        $this->admin_headerlib->add_javascript("Extra_charges", "pages/extra_charges.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
		
        $list = $this->Extra_charges->get_datatables();
        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $datarow) {
			$row = array();
            $Action='';
            $Checkbox='';
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'extra-charges/extra-charges-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'extra-charges/extra-charges-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'extra-charges/extra-charges-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
			
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'extra-charges/check-extra-charges-use","Extra&nbsp;Charges","'.ADMIN_URL.'extra-charges/delete-mul-extra-charges") >'.delete_text.'</a>';
                
                $Checkbox .=  '<div class="checkbox">
                  <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                  <label for="deletecheck'.$datarow->id.'"></label>
                </div>';
            }
            
            $row[] = ++$counter;
            $row[] = ucwords($datarow->name);
            $row[] = $datarow->hsncode;
            $row[] = ($datarow->amounttype==1)?"Amount":"Percentage";
            $row[] = number_format($datarow->defaultamount,2,'.',',');
            $row[] = $datarow->chargetypename;
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Extra_charges->count_all(),
						"recordsFiltered" => $this->Extra_charges->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
    public function extra_charges_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Extra Charges";
        $this->viewData['module'] = "extra_charges/Add_extra_charges";

        $this->load->model("Hsn_code_model","Hsn_code");
        $this->viewData['hsncodedata'] = $this->Hsn_code->getMemberActiveHSNCode();

        $this->admin_headerlib->add_javascript("extra_charges", "pages/add_extra_charges.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_extra_charges() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $this->form_validation->set_rules('name', 'name', 'required|min_length[2]');
        $this->form_validation->set_rules('defaultamount', 'default amount', 'required');

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{

            $name = trim($PostData['name']);
            $hsncodeid = $PostData['hsncodeid'];
            $amounttype = $PostData['amounttype'];
            $defaultamount = $PostData['defaultamount'];
            $chargetype = $PostData['chargetype'];
            $status = $PostData['status'];

            $this->Extra_charges->_where = ("name='" .$name. "'");
            $Count = $this->Extra_charges->CountRecords();

            if ($Count == 0) {
            
                $insertdata = array(
                    "hsncodeid" => $hsncodeid,
                    "name" => $name,
                    "amounttype" => $amounttype,
                    "defaultamount" => $defaultamount,
                    "chargetype" => $chargetype,
                    "type" => 0,
                    "status" => $status,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Extra_charges->Add($insertdata);
                if ($Add) {
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Extra Charges','Add new '.$name.' extra charge.');
                    }
                    $json = array('error'=>1); // Extra charges inserted successfully
                } else {
                    $json = array('error'=>0); // Extra charges not inserted 
                }
            } else {
                $json = array('error'=>2); // Extra charges already added
            }
        }
        echo json_encode($json);
    }

    public function extra_charges_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Extra Charges";
        $this->viewData['module'] = "extra_charges/Add_extra_charges";
        $this->viewData['action'] = "1"; //Edit

        $this->Extra_charges->_where = array('id' => $id);
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getExtrachargesDataByID($id);
        
        $this->load->model("Hsn_code_model","Hsn_code");
        $this->viewData['hsncodedata'] = $this->Hsn_code->getMemberActiveHSNCode();

        $this->admin_headerlib->add_javascript("extra_charges", "pages/add_extra_charges.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_extra_charges() {

        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->form_validation->set_rules('name', 'name', 'required|min_length[2]');
        $this->form_validation->set_rules('defaultamount', 'default amount', 'required');

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
            $extrachargesid = $PostData['extrachargesid'];
            $name = trim($PostData['name']);
            $hsncodeid = $PostData['hsncodeid'];
            $amounttype = $PostData['amounttype'];
            $defaultamount = $PostData['defaultamount'];
            $chargetype = $PostData['chargetype'];
            $status = $PostData['status'];

            $this->Extra_charges->_where = ("id!=" . $extrachargesid . " AND name='" .$name. "'");
            $Count = $this->Extra_charges->CountRecords();

            if ($Count == 0) {

                $updatedata = array(
                    "hsncodeid" => $hsncodeid,
                    "name" => $name,
                    "amounttype" => $amounttype,
                    "defaultamount" => $defaultamount,
                    "chargetype" => $chargetype,
                    "status" => $status,
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );
                $this->Extra_charges->_where = array('id' => $extrachargesid);
                $Edit = $this->Extra_charges->Edit($updatedata);
                if ($Edit) {
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Extra Charges','Edit '.$name.' extra charge.');
                    }
                    $json = array('error'=>1); // Extra charges inserted successfully
                } else {
                    $json = array('error'=>0); // Extra charges not inserted 
                }
            } else {
                $json = array('error'=>2); // Extra charges already added
            }
        }
        echo json_encode($json);
    }

    public function extra_charges_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Extra_charges->_where = array("id" => $PostData['id']);
        $this->Extra_charges->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Extra_charges->_where = array("id"=>$PostData['id']);
            $data = $this->Extra_charges->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' extra charge.';
            
            $this->general_model->addActionLog(2,'Extra Charges', $msg);
        }
        echo $PostData['id'];
    }

    public function check_extra_charges_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("
                    SELECT id FROM ".tbl_extrachargemapping." as ecm 
                    WHERE ecm.extrachargesid = '".$row."'
            ");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
    public function delete_mul_extra_charges(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $query = $this->readdb->query("
                    SELECT id FROM ".tbl_extrachargemapping." as ecm 
                    WHERE ecm.extrachargesid = '".$row."'
            ");

            if($query->num_rows() == 0){
            
                if($this->viewData['submenuvisibility']['managelog'] == 1){

                    $this->Extra_charges->_where = array("id"=>$row);
                    $Extrachargesdata = $this->Extra_charges->getRecordsById();
                
                    $this->general_model->addActionLog(3,'Extra Charges','Delete '.$Extrachargesdata['name'].' extra charge.');
                }
                $this->Extra_charges->Delete(array('id'=>$row));
            }
            
        }
    }
}

?>