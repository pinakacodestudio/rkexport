<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once(APPPATH.'views/'.ADMINFOLDER.'fedex/fedex-common.php');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = APPPATH.'views/'.ADMINFOLDER.'fedex/OpenShip/ShipService_v21.wsdl';

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

$request['WebAuthenticationDetail'] = array(
	/*'ParentCredential' => array(
		'Key' => getProperty('parentkey'), 
		'Password' => getProperty('parentpassword')
	),*/
	'UserCredential' => array(
		'Key' => getProperty('key'), 
		'Password' => getProperty('password')
	)
);

$request['ClientDetail'] = array(
	'AccountNumber' => getProperty('shipaccount'), 
	'MeterNumber' => getProperty('meter')
);
$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Cancel Shipment Request using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'ship', 
	'Major' => '21', 
	'Intermediate' => '0', 
	'Minor' => '0'
);
$request['ShipTimestamp'] = date('c');
$request['TrackingId'] = array(
	'TrackingIdType' =>'FEDEX', // valid values EXPRESS, GROUND, USPS, etc
   	'TrackingNumber'=>getProperty('trackingnumber')
);  
$request['DeletionControl'] = 'DELETE_ALL_PACKAGES'; // Package/Shipment

try {
	if(setEndpoint('changeEndpoint')){
		$newLocation = $client->__setLocation(PRODUCTION_URL.'ship');
	}
	
	$response = $client ->deleteShipment($request);
    
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
        //printSuccess($client, $response);
        echo 1;
    }else{
        //printError($client, $response);
        echo 0;
    } 
    
    //writeToLog($client);    // Write to log file   
} catch (SoapFault $exception) {
    printFault($exception, $client);
}
?>