<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transporter extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Transporter');
        $this->load->model('Transporter_model', 'Transporter');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Transport";
        $this->viewData['module'] = "transporter/Transporter";
        
        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel'); 

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Transporter','View transporter.');
        }

        $this->admin_headerlib->add_javascript("Transporter", "pages/transporter.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {
		
        $list = $this->Transporter->get_datatables();
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $datarow) {
			$row = array();
            $Action='';
            $Checkbox='';
            $channellabel="";
            $membername = "";
            
            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $membername = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" target="_blank" title="'.$datarow->membername.'">'.ucwords($datarow->membername)."</a>";
            }else{
                $membername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'transporter/transporter-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'transporter/transporter-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'transporter/transporter-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
			
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Transporter","'.ADMIN_URL.'transporter/delete-mul-transporter") >'.delete_text.'</a>';
                
                $Checkbox .=  '<div class="checkbox">
                  <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                  <label for="deletecheck'.$datarow->id.'"></label>
                </div>';
            }
            if($datarow->trackingurl != ""){
                $Action .= '<a class="'.trackurl_class.'" href="'.urldecode($datarow->trackingurl).'" title="'.trackurl_title.'" target="_blank">'.trackurl_text.'</a>';
            }
            
            $row[] = ++$counter;
            $row[] = $membername;
            $row[] = ucwords($datarow->companyname);
            $row[] = ($datarow->contactperson!="")?ucwords($datarow->contactperson):"-";
            $row[] = $datarow->mobile;
            $row[] = ($datarow->email!="")?$datarow->email:"-";
            $row[] = ($datarow->address!="")?$datarow->address:"-";
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Transporter->count_all(),
						"recordsFiltered" => $this->Transporter->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
    }
    
    public function transporter_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Transporter";
        $this->viewData['module'] = "transporter/Add_transporter";

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel'); 

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("Transporter", "pages/add_transporter.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_transporter() {
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $channelid = $PostData['channelid'];
        $memberid = $PostData['memberid'];
        $website = trim($PostData['website']);
        $trackingurl = trim($PostData['trackingurl']);

        $this->form_validation->set_rules('companyname', 'company name', 'required|min_length[2]');
        $this->form_validation->set_rules('mobileno', 'mobile number', 'required|numeric|min_length[10]|max_length[10]');
        if($channelid!=0){
            $this->form_validation->set_rules('memberid', 'member', 'callback_dropdowncheck['.$memberid.']');
        }
        $this->form_validation->set_rules('email', 'email', 'valid_email');
        $this->form_validation->set_rules('address', 'address', 'min_length[3]');
        if($website!=""){
            $this->form_validation->set_rules('website', 'website', 'trim|callback_validurl["'.$website.'"]');
        }
        if($trackingurl!=""){
            $this->form_validation->set_rules('trackingurl', 'tracking', 'trim|callback_validurl["'.$trackingurl.'"]');
        }

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{

            $companyname = trim($PostData['companyname']);
            $mobile = trim($PostData['mobileno']);
            $contactperson = trim($PostData['contactperson']);
            $email = trim($PostData['email']);
            $address = trim($PostData['address']);
            $cityid = $PostData['cityid'];
            $website = ($website!="")?urlencode($website):'';
            $trackingurl = ($trackingurl!="")?urlencode($trackingurl):'';
            $status = $PostData['status'];

            $this->Transporter->_where = ("companyname='".$companyname."' AND mobile=".$mobile);
            $Count = $this->Transporter->CountRecords();

            if ($Count == 0) {
                
                $insertdata = array(
                    "memberid" => $memberid,
                    "companyname" => $companyname,
                    "contactperson" => $contactperson,
                    "mobile" => $mobile,
                    "email" => $email,
                    "website" => $website,
                    "address" => $address,
                    "cityid" => $cityid,
                    "trackingurl" => $trackingurl,
                    "type" => 0,
                    "status" => $status,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Transporter->Add($insertdata);
                if ($Add) {

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Transporter','Add new '.$companyname.' transporter.');
                    }
                    $json = array('error'=>1); //Transporter successfully added. 
                } else {
                    $json = array('error'=>0); //Transporter not added.
                }
            } else {
                $json = array('error'=>2); //Transporter already exists.
            }
        }
        echo json_encode($json);
    }

    public function transporter_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Transporter";
        $this->viewData['module'] = "transporter/Add_transporter";
        $this->viewData['action'] = "1"; //Edit

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel'); 
        
        $this->viewData['transporterdata'] = $this->Transporter->getTransporterDataByID($id);
        
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("Transporter", "pages/add_transporter.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_transporter() {

        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $channelid = $PostData['channelid'];
        $memberid = $PostData['memberid'];
        $website = trim($PostData['website']);
        $trackingurl = trim($PostData['trackingurl']);
        
        $this->form_validation->set_rules('companyname', 'company name', 'required|min_length[2]');
        $this->form_validation->set_rules('mobileno', 'mobile number', 'required|numeric|min_length[10]|max_length[10]');
        if($channelid!=0){
            $this->form_validation->set_rules('memberid', 'member', 'callback_dropdowncheck['.$memberid.']');
        }
        $this->form_validation->set_rules('email', 'email', 'valid_email');
        $this->form_validation->set_rules('address', 'address', 'min_length[3]');
        if($website!=""){
            $this->form_validation->set_rules('website', 'website', 'trim|callback_validurl["'.$website.'"]');
        }
        if($trackingurl!=""){
            $this->form_validation->set_rules('trackingurl', 'tracking', 'trim|callback_validurl["'.$trackingurl.'"]');
        }

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
            $TransporterID = $PostData['transporterid'];
            $companyname = trim($PostData['companyname']);
            $mobile = trim($PostData['mobileno']);
            $contactperson = trim($PostData['contactperson']);
            $email = trim($PostData['email']);
            $address = trim($PostData['address']);
            $cityid = $PostData['cityid'];
            $website = ($website!="")?urlencode($website):'';
            $trackingurl = ($trackingurl!="")?urlencode($trackingurl):'';
            $status = $PostData['status'];

            $this->Transporter->_where = ("id!=".$TransporterID." AND companyname='".$companyname."' AND mobile=".$mobile);
            $Count = $this->Transporter->CountRecords();

            if ($Count == 0) {
                
                $updatedata = array(
                    "memberid" => $memberid,
                    "companyname" => $companyname,
                    "contactperson" => $contactperson,
                    "email" => $email,
                    "mobile" => $mobile,
                    "address" => $address,
                    "cityid" => $cityid,
                    "website" => $website,
                    "trackingurl" => $trackingurl,
                    "status" => $status,
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );
                $this->Transporter->_where = array('id' => $TransporterID);
                $Edit = $this->Transporter->Edit($updatedata);
                if($Edit){

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Transporter','Edit '.$companyname.' transporter.');
                    }
                    $json = array('error'=>1); //Transporter successfully updated. 
                } else {
                    $json = array('error'=>0); //Transporter not updated.
                }
            } else {
                $json = array('error'=>2); //Transporter already exists.
            }
        }
        echo json_encode($json);
    }

    public function transporter_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Transporter->_where = array("id" => $PostData['id']);
        $this->Transporter->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Transporter->_where = array("id"=>$PostData['id']);
            $data = $this->Transporter->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['companyname'].' transporter.';
            $this->general_model->addActionLog(2,'Transporter', $msg);
        }
        echo $PostData['id'];
    }
    
    public function delete_mul_transporter(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){

            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Transporter->_where = array("id"=>$row);
                $Transporterdata = $this->Transporter->getRecordsById();
            
                $this->general_model->addActionLog(3,'Transporter','Delete '.$Transporterdata['companyname'].' transporter.');
            }
            $this->Transporter->Delete(array('id'=>$row));
        }
    }
}
?>