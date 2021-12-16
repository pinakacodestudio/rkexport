<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Store_location extends Channel_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'store_location');
        $this->load->model('Store_location_model', 'Store_location');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        
        $this->viewData['title'] = "Store Location";
        $this->viewData['module'] = "store_location/Store_location";
        $this->viewData['store_location'] = $this->Store_location->getStoreLocationByMember($channelid,$memberid);
        
        $this->channel_headerlib->add_javascript("Store_location", "pages/store_location.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function add_store_location() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = " Add Store Locations";
        $this->viewData['module'] = "store_location/Add_store_location.php";
        
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_bottom_javascripts("Store_location", "pages/add_store_location.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }

    public function store_location_add() {
        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $contactperson = isset($PostData['contactperson']) ? trim($PostData['contactperson']) : '';
        $email = isset($PostData['email']) ? trim($PostData['email']) : '';
        $mobileno = isset($PostData['mobileno']) ? trim($PostData['mobileno']) : '';
        $cityid = isset($PostData['cityid']) ? trim($PostData['cityid']) : '';
        $latitude = isset($PostData['latitude']) ? trim($PostData['latitude']) : '';
        $longitude = isset($PostData['longitude']) ? trim($PostData['longitude']) : '';
        $link = isset($PostData['link']) ? trim($PostData['link']) : '';
        $status = $PostData['status'];

        $insertdata = array(
            "channelid" => $channelid,
            "memberid" => $memberid,
            "name" => $PostData['name'],
            "contactperson" => $PostData['contactperson'],
            "email" => $PostData['email'],
            "mobileno" => $PostData['mobileno'],
            "address" => $PostData['address'],
            "cityid" => $PostData['cityid'],
            "latitude" => $PostData['latitude'],
            "longitude" => $PostData['longitude'],
            "link" => urlencode($PostData['link']),
            "status" => $PostData['status'],
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "usertype" => 1,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );
        $insertdata = array_map('trim', $insertdata);
        $Add = $this->Store_location->Add($insertdata);
        if ($Add) {
            echo 1;
        } else {
            echo 0;
        }        
    }
    public function edit_store_location($Store_locationid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Store Location";
        $this->viewData['module'] = "store_location/Add_store_location";
        $this->viewData['action'] = "1"; //Edit

        $this->Store_location->_where = array('id' => $Store_locationid);
        $this->viewData['store_locationdata'] = $this->Store_location->getRecordsByID();

        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_bottom_javascripts("Store_location", "pages/add_store_location.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }

    public function update_store_location() {

        $PostData = $this->input->post();
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $Store_locationID = $PostData['store_locationid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $contactperson = isset($PostData['contactperson']) ? trim($PostData['contactperson']) : '';
        $email = isset($PostData['email']) ? trim($PostData['email']) : '';
        $mobileno = isset($PostData['mobileno']) ? trim($PostData['mobileno']) : '';
        $cityid = isset($PostData['cityid']) ? trim($PostData['cityid']) : '';
        $latitude = isset($PostData['latitude']) ? trim($PostData['latitude']) : '';
        $longitude = isset($PostData['longitude']) ? trim($PostData['longitude']) : '';
        $link = isset($PostData['link']) ? trim($PostData['link']) : '';
        $status = $PostData['status'];

        $updatedata = array(
            "channelid" => $channelid,
            "memberid" => $memberid,
            "name" => $PostData['name'],
            "contactperson" => $PostData['contactperson'],
            "email" => $PostData['email'],
            "mobileno" => $PostData['mobileno'],
            "address" => $PostData['address'],
            "cityid" => $PostData['cityid'],
            "latitude" => $PostData['latitude'],
            "longitude" => $PostData['longitude'],
            "link" => urlencode($PostData['link']),
            "status" => $PostData['status'],
            "modifieddate" => $modifieddate,
            "usertype" => 1,
            "modifiedby" => $modifiedby
        );
        $this->Store_location->_where = array('id' => $Store_locationID);
        $this->Store_location->Edit($updatedata);
        echo 1;      
    }

    public function store_location_enabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Store_location->_where = array("id" => $PostData['id']);
        $this->Store_location->Edit($updatedata);

        echo $PostData['id'];
    }

    public function delete_mul_store_location(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        foreach($ids as $row){

            $this->Store_location->Delete(array("id"=>$row));

        }
    }
    public function getlatlong(){

		$PostData = $this->input->post();
		
		$address = $PostData['location'];
		$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&key=AIzaSyCVpevANWSnWl2ZJkE5yJ5rfNlgDReOBGw";
		$url1 = str_replace(" ","%20",$url);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Accept: application/json'
		));
		$response = curl_exec($ch);
		curl_close($ch); 

		$response_a = json_decode($response);

		echo $lat = $response_a->results[0]->geometry->location->lat;
		echo ",";
		echo $long = $response_a->results[0]->geometry->location->lng;
	}
}

?>