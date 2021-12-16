<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_format extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Sms_format');
		$this->load->model('Sms_format_model','Sms_format');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "SMS Format";
		$this->viewData['module'] = "sms_format/Sms_format";
        
        $this->channel_headerlib->add_javascript("sms_format","pages/sms_format.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Sms_format->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $datarow) {
			$row = array();
			$Action='';
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'sms-format/sms-format-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'sms-format/sms-format-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'sms-format/sms-format-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            
            $row[] = ++$counter;
            $row[] = $this->Smsformattype[$datarow->smsformattype];
			$row[] = $datarow->gateway;
            $row[] = $datarow->format;
            $row[] = $Action;
			$data[] = $row;
        }
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Sms_format->count_all(),
						"recordsFiltered" => $this->Sms_format->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function sms_format_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

		$this->viewData['title'] = "Add SMS Format";
		$this->viewData['module'] = "sms_format/Add_sms_format";

        $this->load->model('Sms_gateway_model','Sms_gateway');
        $this->viewData['smsgatewaydata'] = $this->Sms_gateway->getActiveSMSGateway($channelid,$memberid);

		$this->channel_headerlib->add_javascript("add_sms_format","pages/add_sms_format.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

	public function sms_format_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

		$this->viewData['title'] = "Edit SMS Format";
		$this->viewData['module'] = "sms_format/Add_sms_format";
		$this->viewData['action'] = "1";//Edit

        $this->load->model('Sms_gateway_model','Sms_gateway');
        $this->viewData['smsgatewaydata'] = $this->Sms_gateway->getActiveSMSGateway($channelid,$memberid);

		$this->viewData['smsformatdata'] = $this->Sms_format->getSmsFormatDataByID($id);
		
		$this->channel_headerlib->add_javascript("add_sms_format","pages/add_sms_format.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function add_sms_format(){
		$PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $smsformattype = $PostData['smsformattype'];
        $smsgatewayid = $PostData['smsgatewayid'];
        $format = $PostData['format'];
        $status = $PostData['status'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        
        $this->Sms_format->_where = "smsformattype='".$smsformattype."' AND channelid='".$channelid."' AND memberid='".$memberid."'";
		$Count = $this->Sms_format->CountRecords();
		if($Count==0){
            $insertdata = array(
                            "channelid" => $channelid,
                            "memberid" => $memberid,
                            "smsformattype"=>$smsformattype,
                            "smsgatewayid"=>$smsgatewayid,
                            "format"=>$format,
                            "createdate"=>$createddate,
                            "usertype" => 1,
                            "addedby"=>$addedby,
                            "modifieddate"=>$createddate,
                            "modifiedby"=>$addedby,
                            "status"=>$status);

            $insertdata=array_map('trim',$insertdata);

            $Add = $this->Sms_format->Add($insertdata);
            if($Add){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    
    public function update_sms_format(){
        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');
     
        $smsformatid = $PostData['smsformatid'];
        $smsformattype = $PostData['smsformattype'];
        $smsgatewayid = $PostData['smsgatewayid'];
        $format = $PostData['format'];
        $status = $PostData['status'];

        $this->Sms_format->_where = "id!='".$smsformatid."' AND smsformattype='".$smsformattype."' AND channelid='".$channelid."' AND memberid='".$memberid."'";
		$Count = $this->Sms_format->CountRecords();
		if($Count==0){

            $updatedata = array(
                                "channelid" => $channelid,
                                "memberid" => $memberid,
                                "smsformattype"=>$smsformattype,
                                "smsgatewayid"=>$smsgatewayid,
                                "format"=>$format,
                                "status"=>$status,
                                "modifieddate"=>$modifieddate,
                                "usertype" => 1,
                                "modifiedby"=>$modifiedby);

            $updatedata=array_map('trim',$updatedata);

            $this->Sms_format->_where = array("id"=>$smsformatid);
            $Edit = $this->Sms_format->Edit($updatedata);
            if($Edit){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    
    public function sms_format_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Sms_format->_where = array("id" => $PostData['id']);
        $this->Sms_format->Edit($updatedata);

        echo $PostData['id'];
    }

}