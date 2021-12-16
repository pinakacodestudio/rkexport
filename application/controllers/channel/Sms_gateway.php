<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_gateway extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Sms_gateway');
		$this->load->model('Sms_gateway_model','Sms_gateway');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "SMS Gateway";
		$this->viewData['module'] = "sms_gateway/Sms_gateway";
		
		$this->channel_headerlib->add_javascript("sms_gateway","pages/sms_gateway.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Sms_gateway->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $datarow) {
			$row = array();
			$Action='';
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'sms-gateway/sms-gateway-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'sms-gateway/sms-gateway-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'sms-gateway/sms-gateway-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            
            $row[] = ++$counter;
			$row[] = $datarow->name;
            $row[] = $datarow->gatewaylink;
            $row[] = $datarow->userid;
            $row[] = $datarow->password;
            $row[] = $datarow->senderid;
            $row[] = $datarow->description;
			$row[] = $Action;
			$data[] = $row;
        }
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Sms_gateway->count_all(),
						"recordsFiltered" => $this->Sms_gateway->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function sms_gateway_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add SMS Gateway";
		$this->viewData['module'] = "sms_gateway/Add_sms_gateway";

		$this->channel_headerlib->add_javascript("add_sms_gateway","pages/add_sms_gateway.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

	public function sms_gateway_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit SMS Gateway";
		$this->viewData['module'] = "sms_gateway/Add_sms_gateway";
		$this->viewData['action'] = "1";//Edit

		//Get Channel data by id
		$this->viewData['smsgatewaydata'] = $this->Sms_gateway->getSmsGatewayDataByID($id);
		
		$this->channel_headerlib->add_javascript("add_sms_gateway","pages/add_sms_gateway.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function add_sms_gateway(){
        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
     
        $name = $PostData['name'];
        $gatewaylink = $PostData['gatewaylink'];
        $userid = $PostData['userid'];
        $password = $PostData['password'];
        $senderid = $PostData['senderid'];
        $description = $PostData['description'];
        $status = $PostData['status'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        
        $this->Sms_gateway->_where = "name='".trim($name)."' AND channelid='".$channelid."' AND memberid='".$memberid."'";
		$Count = $this->Sms_gateway->CountRecords();
		if($Count==0){
			
			$check = $this->Sms_gateway->checkSMSGatewayEnable($channelid,$memberid);
			if(!$check){
				$status = $PostData['status'];
			}else{
				$status = 0;
			}
			$insertdata = array(
                        "channelid" => $channelid,
                        "memberid" => $memberid,
                        "name"=>$name,
						"gatewaylink"=>$gatewaylink,
						"userid"=>$userid,
						"password"=>$password,
						"senderid"=>$senderid,
						"description"=>$description,
                        "createddate"=>$createddate,
                        "usertype" => 1,
						"addedby"=>$addedby,
						"modifieddate"=>$createddate,
						"modifiedby"=>$addedby,
						"status"=>$status);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Sms_gateway->Add($insertdata);
			if($Add){
			    echo 1;
			}else{
				echo 0;
			}
		}else{
		    echo 2;
        }
    }
    
    public function update_sms_gateway(){
        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
     
        $modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');
     
        $smsgatewayid = $PostData['smsgatewayid'];
        $name = trim($PostData['name']);
        $gatewaylink = $PostData['gatewaylink'];
        $userid = $PostData['userid'];
        $password = $PostData['password'];
        $senderid = $PostData['senderid'];
        $description = $PostData['description'];
        $status = $PostData['status'];

		$this->Sms_gateway->_where = "id!='".$smsgatewayid."' AND name='".$name."' AND channelid='".$channelid."' AND memberid='".$memberid."' ";
		$Count = $this->Sms_gateway->CountRecords();

		if($Count==0){

			$this->Sms_gateway->_fields = "status";
			$this->Sms_gateway->_where = "id=".$smsgatewayid;
			$SMSGateway = $this->Sms_gateway->getRecordsById();

			if($SMSGateway['status']==1){

			}
			$check = $this->Sms_gateway->checkSMSGatewayEnable($channelid,$memberid);
			if(!$check || ($check==1 && $SMSGateway['status']==1)){
				$status = $PostData['status'];
			}else{
				$status = 0;
			}
			$updatedata = array(
                                "channelid" => $channelid,
                                "memberid" => $memberid,
                                "name"=>$name,
                                "gatewaylink"=>$gatewaylink,
                                "userid"=>$userid,
                                "password"=>$password,
                                "senderid"=>$senderid,
								"description"=>$description,
								"status"=>$status,
                                "modifieddate"=>$modifieddate,
                                "usertype" => 1,
                                "modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Sms_gateway->_where = array("id"=>$smsgatewayid);
			$Edit = $this->Sms_gateway->Edit($updatedata);
			if($Edit){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
    }
    
    public function sms_gateway_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
     
		$modifieddate = $this->general_model->getCurrentDateTime();
		
		$check = $this->Sms_gateway->checkSMSGatewayEnable($channelid,$memberid);
		if((!$check && $PostData['val']==1)|| $PostData['val']==0){

			$updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
			$this->Sms_gateway->_where = array("id" => $PostData['id']);
			$this->Sms_gateway->Edit($updatedata);
		}else{
			$PostData['id'] = 0;
		}

        echo $PostData['id'];
    }

}