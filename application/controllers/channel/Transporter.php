<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transporter extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Transporter');
        $this->load->model('Side_navigation_model');
        $this->load->model('Transporter_model', 'Transporter');
        $this->load->library("form_validation");
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Extra Charges";
        $this->viewData['module'] = "transporter/Transporter";
        
        $this->channel_headerlib->add_javascript("Transporter", "pages/transporter.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    
    public function listing() {
		
        $list = $this->Transporter->get_datatables();
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');

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
                $membername = $channellabel.ucwords($datarow->membername);
            }else{
                $membername = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }


            if($datarow->memberid == $MEMBERID && $datarow->type == 1){
                
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                    $Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'transporter/transporter-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
                }
                
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                    if($datarow->status==1){
                        $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'transporter/transporter-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                    else{
                        $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'transporter/transporter-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                    }
                }
                
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){                
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Transporter","'.CHANNEL_URL.'transporter/delete-mul-transporter") >'.delete_text.'</a>';
                    
                    $Checkbox .=  '<div class="checkbox">
                    <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                    <label for="deletecheck'.$datarow->id.'"></label>
                    </div>';
                }
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
        
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("transporter", "pages/add_transporter.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function add_transporter() {
        
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        
        $website = trim($PostData['website']);
        $trackingurl = trim($PostData['trackingurl']);

        $this->form_validation->set_rules('companyname', 'company name', 'required|min_length[2]');
        $this->form_validation->set_rules('mobileno', 'mobile number', 'required|numeric|min_length[10]|max_length[10]');
        
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

            $this->Transporter->_where = ("memberid=".$MEMBERID." AND companyname='".$companyname."' AND mobile=".$mobile);
            $Count = $this->Transporter->CountRecords();

            if ($Count == 0) {
                
                $insertdata = array(
                    "memberid" => $MEMBERID,
                    "companyname" => $companyname,
                    "contactperson" => $contactperson,
                    "mobile" => $mobile,
                    "email" => $email,
                    "website" => $website,
                    "address" => $address,
                    "cityid" => $cityid,
                    "trackingurl" => $trackingurl,
                    "type" => 1,
                    "status" => $status,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $MEMBERID,
                    "modifiedby" => $MEMBERID
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Transporter->Add($insertdata);
                if ($Add) {
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

        $this->viewData['transporterdata'] = $this->Transporter->getTransporterDataByID($id);
        
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("transporter", "pages/add_transporter.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function update_transporter() {

        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');

        $website = trim($PostData['website']);
        $trackingurl = trim($PostData['trackingurl']);
        
        $this->form_validation->set_rules('companyname', 'company name', 'required|min_length[2]');
        $this->form_validation->set_rules('mobileno', 'mobile number', 'required|numeric|min_length[10]|max_length[10]');
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

            $this->Transporter->_where = ("id!=".$TransporterID." AND memberid=".$MEMBERID." AND companyname='".$companyname."' AND mobile=".$mobile);
            $Count = $this->Transporter->CountRecords();

            if ($Count == 0) {
                
                $updatedata = array(
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
                    "modifiedby" => $MEMBERID
                );
                $this->Transporter->_where = array('id' => $TransporterID);
                $Edit = $this->Transporter->Edit($updatedata);
                if($Edit){
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
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'CHANNELID'));
        $this->Transporter->_where = array("id" => $PostData['id']);
        $this->Transporter->Edit($updatedata);

        echo $PostData['id'];
    }
    
    public function delete_mul_transporter(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){

            $this->Transporter->Delete(array('id'=>$row));
        }
    }

    public function getactivecity(){
        
        $PostData = $this->input->post();
        $this->load->model('City_model', 'City');

		if(isset($PostData["term"])){
			$Citydata = $this->City->searchcity(1,$PostData["term"]);
		}else if(isset($PostData["ids"])){
			$Citydata = $this->City->searchcity(0,$PostData["ids"]);
		}
	    
		echo json_encode($Citydata);
	}
}