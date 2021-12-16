<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_setting extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Invoice_setting');
        $this->load->model('Invoice_setting_model','Invoice_setting');
    }
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Invoice Setting";
		$this->viewData['module'] = "invoice_setting/Invoice_setting";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		$this->viewData['invoicesettingdata'] = $this->Invoice_setting->getInvoiceSettingsByMember($CHANNELID,$MEMBERID);
		
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}

	public function invoice_setting_edit() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Invoice Setting";
		$this->viewData['module'] = "invoice_setting/Invoice_setting_edit";
		$this->viewData['action'] = "1";//Edit
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		$this->viewData['invoicesettingdata'] = $this->Invoice_setting->getInvoiceSettingsByMember($CHANNELID,$MEMBERID);
		
		$this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

		$this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->channel_headerlib->add_bottom_javascripts("add_invoice_setting","pages/add_invoice_setting.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}

	public function update_invoice_setting(){

		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		$businessname = $PostData['businessname'];
		$businessaddress = $PostData['businessaddress'];
		$countryid = $PostData['countryid'];
		$provinceid = $PostData['provinceid'];
		$cityid = $PostData['cityid'];
		$email = $PostData['email'];
		$gstno = $PostData['gstno'];
		$postcode = $PostData['postcode'];
		$oldlogo = $PostData['oldlogo'];
		$invoicenotes = $PostData['invoicenotes'];

		if($_FILES["logo"]['name'] != ''){
			if($oldlogo == ""){
				$logo = uploadfile('logo', 'SETTINGS', SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
			}else{
				$logo = reuploadfile('logo', 'SETTINGS', $PostData['oldlogo'], SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
			}
			if($logo !== 0){	
				if($logo==2){
					return 3;
				}
			}else{
				return 2;
			}
		}else{
			$logo = $oldlogo;
		}
		
		$this->Invoice_setting->_where = array("channelid"=>$CHANNELID,"memberid"=>$MEMBERID);
		$Check = $this->Invoice_setting->getRecordsByID();

		if(empty($Check)){
			$insertdata = array("channelid"=>$CHANNELID,
                                "memberid"=>$MEMBERID,
                                "businessname"=>$businessname,
								"businessaddress"=>$businessaddress,
								"email"=>$email,
								"gstno"=>$gstno,
								"logo"=>$logo,
								"cityid" => $cityid,
								"provinceid" => $provinceid,
								"countryid" => $countryid,
								"postcode" => $postcode,
								"notes"=>$invoicenotes,
								"createddate"=>$modifieddate,
								"addedby"=>$modifiedby,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby
							);

			$insertdata=array_map('trim',$insertdata);
			$this->Invoice_setting->Add($insertdata);

		}else{
			$updatedata = array("businessname"=>$businessname,
								"businessaddress"=>$businessaddress,
								"email"=>$email,
								"gstno"=>$gstno,
								"logo"=>$logo,
								"cityid" => $cityid,
								"provinceid" => $provinceid,
								"countryid" => $countryid,
								"postcode" => $postcode,
								"notes"=>$invoicenotes,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby
							);

			$updatedata=array_map('trim',$updatedata);
			$this->Invoice_setting->_where = array("id"=>$Check['id'],"channelid"=>$CHANNELID,"memberid"=>$MEMBERID);
			$this->Invoice_setting->Edit($updatedata);
		}
		echo 1;
		
	}
}