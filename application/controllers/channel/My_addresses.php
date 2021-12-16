<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_addresses extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		// $this->viewData = $this->getChannelSettings('submenu','My_addresses');
		$this->load->model('Customeraddress_model','Member_address');
	}
	public function index() {
        /* $this->viewData['title'] = "User Profile";
        $this->viewData['module'] = "User_profile"; */
        $this->viewData['title'] = "My Addresses";
		$this->viewData['module'] = "my_addresses/My_addresses";

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');

		$this->load->model('Member_model','Member');
		$this->Member->_fields = "id,name,image,mobile,countrycode,email,issocialmedia,socialid,debitlimit,status,channelid";
		$this->Member->_where = "id = ".$MEMBERID;
		$this->viewData['userdata'] = $this->Member->getRecordsByID();
        
        $this->viewData['memberid'] = $MEMBERID;
        $this->viewData['channelid'] = $this->viewData['userdata']['channelid'];

		//Get Country code list
		$this->load->model('Country_model', 'Country');
		$this->Country->_fields = array("id","phonecode");
		$this->viewData['countrycodedata'] = $this->Country->getCountrycode();
		
		$this->load->model('Side_navigation_model', 'Side_navigation');
		$this->viewData['mainnavdata'] = $this->Side_navigation->channelmainnav(1);
        $this->viewData['subnavdata'] = $this->Side_navigation->channelsubnav(1);

        $this->viewData['membershippingdata'] = $this->Member->getMemberShippingDetail($MEMBERID);
        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();
        
        /* 
		
		$this->channel_headerlib->add_javascript("my_addresses","pages/my_addresses.js"); */
        
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->channel_headerlib->add_javascript("my_addresses", "pages/my_addresses.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
    public function listing() {   
        $this->load->model('Member_model','Member');
        /* $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']); */
        // $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Member->getbillingaddress_datatables();
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';

            $address="<address>".$datarow->address.", ".$datarow->town." - ".$datarow->postalcode."</address>";
            
            // if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="javascript:void(0)" title="'.edit_title.'" onclick="getbillingaddressdetail('.$datarow->id.')">'.edit_text.'</a> ';
              
                if($datarow->status==1){
                    $actions .= ' <span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'my-addresses/billing-address-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= ' <span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'my-addresses/billing-address-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            // }

            // if(in_array($rollid, $delete)) {     
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Billing-address","'.CHANNEL_URL.'my-addresses/delete-mul-billing-address/'.$datarow->id.'") >'.delete_text.'</a>';
           
                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            // }
            $row[] = ++$counter;
            $row[] = ucwords($datarow->name);
            $row[] = $address;
            $row[] = $datarow->email;
            $row[] = $datarow->mobileno;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Member->countgetbillingaddress_filtered(),
                        "recordsFiltered" => $this->Member->countgetbillingaddress_all(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
	
    public function add_billing_address() {

        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
       
        $memberid = $PostData['memberid'];
        $name = $PostData['baname'];
        $email = $PostData['baemail'];
        $address = $PostData['baddress'];
        $town = $PostData['batown'];
        $postalcode = $PostData['bapostalcode'];
        $mobileno = $PostData['bamobileno'];
        $countryid = $PostData['countryid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $status = $PostData['statusba'];

        $this->load->model('Customeraddress_model','Member_address');
        /* $this->Member_address->_where = array("memberid" => $memberid,"name"=>trim($name),"email"=>$email);
        $Count = $this->Member_address->CountRecords();

        if($Count==0){ */
            $insertdata = array(
                "memberid" => $memberid,
                "name" => $name,
                "address" => $address,
                "provinceid" => $provinceid,
                "cityid" => $cityid,
                "town" => $town,
                "postalcode" => $postalcode,
                "mobileno" => $mobileno,
                "email" => $email,
                "status" => $status,
                "createddate" => $createddate,
                "addedby" => $addedby,
                "modifieddate" => $createddate,
                "modifiedby" => $addedby
            );
            $insertdata = array_map('trim', $insertdata);
            $AddressID = $this->Member_address->Add($insertdata);
            if ($AddressID) {
                echo 1;
            } else {
                echo 0;
            }
        /* }else{
            echo 2;
        }  */  
    }
    public function update_billing_address() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
       
        $billingaddressid = $PostData['billingaddressid'];
        $memberid = $PostData['memberid'];
        $name = $PostData['baname'];
        $email = $PostData['baemail'];
        $address = $PostData['baddress'];
        $town = $PostData['batown'];
        $postalcode = $PostData['bapostalcode'];
        $mobileno = $PostData['bamobileno'];
        $countryid = $PostData['countryid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $status = $PostData['statusba'];

        $this->load->model('Customeraddress_model','Member_address');
       /*  $this->Member_address->_where = ("id!=".$billingaddressid." AND memberid=".$memberid." AND name='".trim($name)."' AND email='".$email."'");
        $Count = $this->Member_address->CountRecords();

        if($Count==0){ */
            $updatedata = array(
                "memberid" => $memberid,
                "name" => $name,
                "address" => $address,
                "provinceid" => $provinceid,
                "cityid" => $cityid,
                "town" => $town,
                "postalcode" => $postalcode,
                "mobileno" => $mobileno,
                "email" => $email,
                "status" => $status,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $updatedata = array_map('trim', $updatedata);
            $this->Member_address->_where = ("id=".$billingaddressid);
            $AddressID = $this->Member_address->Edit($updatedata);
            if ($AddressID) {
                echo 1;
            } else {
                echo 0;
            }
        /* }else{
            echo 2;
        }   */ 
    }

    public function billing_address_enable_disable() {
        /* $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']); */
        $PostData = $this->input->post();
        $this->load->model('Customeraddress_model','Member_address');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Member_address->_where = array("id" => $PostData['id']);
        $this->Member_address->Edit($updatedata);

        echo $PostData['id'];
    }
    public function delete_mul_billing_address(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $this->load->model('Customeraddress_model','Member_address');
        $ADMINID = $this->session->userdata(base_url().'MEMBERID');
        foreach($ids as $row){
            
            $this->Member_address->_where = ("id=".$row);
            $this->Member_address->Edit(array("status"=>2));
        }
    }

    public function getBillingAddressDataById()
    {
        $PostData = $this->input->post();
        $this->load->model("Customeraddress_model","Member_address");
        $this->Member_address->_fields = "id,memberid,name,address,email,town,(SELECT countryid FROM ".tbl_province." WHERE id=provinceid) as countryid,provinceid,cityid,postalcode,mobileno,status";
        $this->Member_address->_where = array('id' => $PostData['billingaddressid']);
        $BillingAddressData = $this->Member_address->getRecordsByID();
        echo json_encode($BillingAddressData);
    }

}