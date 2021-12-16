<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hsn_code extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Hsn_code');
        $this->load->model('Side_navigation_model');
        $this->load->model('Hsn_code_model', 'Hsn_code');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Hsn Code";
        $this->viewData['module'] = "hsn_code/Hsn_code";
        
        $this->channel_headerlib->add_javascript("Hsn_code", "pages/hsn_code.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
		
        $list = $this->Hsn_code->get_datatables();
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $hsncode) {
			$row = array();
            $Action='';
            $Checkbox='';
            $channellabel="";
            $membername = "";
            $channelname = "";
            
            if($hsncode->channelid != 0){
                $key = array_search($hsncode->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channelname = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '.$channeldata[$key]['name'];
                    
                }
            }else{
                $channelname = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($hsncode->memberid != 0){
                $membername = ucwords($hsncode->membername);
            }else{
                $membername = '-';
            }
            
            if($hsncode->type==1 && $MEMBERID==$hsncode->memberid){
                
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                    $Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'hsn-code/hsn-code-edit/'.$hsncode->id.'" title='.edit_title.'>'.edit_text.'</a>';
                }
                
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                    if($hsncode->status==1){
                        $Action .= '<span id="span'.$hsncode->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$hsncode->id.',\''.CHANNEL_URL.'hsn-code/hsn-code-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                    else{
                        $Action .='<span id="span'.$hsncode->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$hsncode->id.',\''.CHANNEL_URL.'hsn-code/hsn-code-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                    }
                }
                
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){                
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$hsncode->id.',"'.CHANNEL_URL.'hsn-code/check-hsn-code-use","HSN&nbsp;Code","'.CHANNEL_URL.'hsn-code/delete-mul-hsn-code") >'.delete_text.'</a>';
                    
                    $Checkbox .=  '<div class="checkbox">
                    <input id="deletecheck'.$hsncode->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$hsncode->id.'" name="deletecheck'.$hsncode->id.'" class="checkradios">
                    <label for="deletecheck'.$hsncode->id.'"></label>
                    </div>';
                }
            }
            
            $row[] = ++$counter;
            $row[] = $channelname;
            $row[] = $membername;
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
        
        $this->channel_headerlib->add_javascript("add_hsn_code", "pages/add_hsn_code.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function add_hsn_code() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

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

            $this->Hsn_code->_where = ("channelid='" .$CHANNELID. "' AND memberid='" .$MEMBERID. "' AND hsncode='" . trim($hsncode) . "'");
            $Count = $this->Hsn_code->CountRecords();

            if ($Count == 0) {
            
                $insertdata = array(
                    "channelid" => $CHANNELID,
                    "memberid" => $MEMBERID,
                    "hsncode" => $hsncode,
                    "integratedtax" => $integratedtax,
                    "description" => $description,
                    "type" => 1,
                    "status" => $status,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Hsn_code->Add($insertdata);
                if ($Add) {
                    $json = array('error'=>1); // HSN Code inserted successfully
                } else {
                    $json = array('error'=>0); // HSN Code not inserted 
                }
            } else {
                $json = array('error'=>2); // HSN Code already added
            }
        }
        echo json_encode($json);
    }

    public function hsn_code_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Hsn Code";
        $this->viewData['module'] = "hsn_code/Add_hsn_code";
        $this->viewData['action'] = "1"; //Edit

        $this->Hsn_code->_where = array('id' => $id);
        $this->viewData['hsncodedata'] = $this->Hsn_code->getRecordsByID();
        
        $this->channel_headerlib->add_javascript("Hsn_code", "pages/add_hsn_code.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function update_hsn_code() {

        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

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

            $this->Hsn_code->_where = ("id!='" . $HsncodeID . "' AND channelid='" .$CHANNELID. "' AND memberid='" .$MEMBERID. "' AND hsncode='" . trim($hsncode) . "'");
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
                $Edit = $this->Hsn_code->Edit($updatedata);
                if ($Edit) {
                    $json = array('error'=>1); // HSN code inserted successfully
                } else {
                    $json = array('error'=>0); // HSN code charges not inserted 
                }
            } else {
                $json = array('error'=>2); // HSN code charges already added
            }
        }
        echo json_encode($json);
    }

    public function hsn_code_enable_disable() {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Hsn_code->_where = array("id" => $PostData['id']);
        $this->Hsn_code->Edit($updatedata);

        echo $PostData['id'];
    }
    public function check_hsn_code_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT id FROM ".tbl_hsncode." WHERE 
                    id IN (SELECT hsncodeid FROM ".tbl_product." WHERE hsncodeid = '".$row."') OR 
                    id IN (SELECT hsncodeid FROM ".tbl_extracharges." WHERE hsncodeid = '".$row."')
                ");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
    public function delete_mul_hsn_code(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT id FROM ".tbl_hsncode." WHERE 
                    id IN (SELECT hsncodeid FROM ".tbl_product." WHERE hsncodeid = '".$row."') OR 
                    id IN (SELECT hsncodeid FROM ".tbl_extracharges." WHERE hsncodeid = '".$row."')
                    ");

            if($query->num_rows() == 0){
            
                $this->Hsn_code->Delete(array('id'=>$row));
            }
        }
    }
}