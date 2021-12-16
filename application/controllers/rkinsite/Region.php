<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Region extends Admin_Controller {

	function __construct(){
		parent::__construct();
	}
	
	public function getlocation()
	{
		$latlng = $_REQUEST['latlong'];
		$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latlng."&key=AIzaSyCVpevANWSnWl2ZJkE5yJ5rfNlgDReOBGw";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
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
		$returndata = array();
		foreach($response_a->results[0]->address_components as $address)
		{
			//  print_r($address->types[0]); exit; 
			if($address->types[0] == 'country')
			{
				$returndata['country'] = $address->long_name;	
			}
			if($address->types[0] == 'postal_code')
			{
				$returndata['zipcode'] = $address->long_name;	
			}
			if($address->types[0] == 'locality')
			{
				$returndata['city'] = $address->long_name;	
			}
			else if($address->types[0] == 'administrative_area_level_2')
			{
				$returndata['city'] = $address->long_name;	
			}
			if($address->types[0] == 'administrative_area_level_1')
			{
				$returndata['state'] = $address->short_name;
				$returndata['statefull'] = $address->long_name;
			}
			if($address->types[0] == 'street_number')
			{
					$returndata['address1'] = $address->long_name;
			}
			if($address->types[0] == 'route')
			{
					$returndata['address2'] = $address->long_name;
			}

		}
		$data['result'] = $returndata;
		echo json_encode($data);
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