<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 6.0.0

require_once(APPPATH.'views/'.ADMINFOLDER.'fedex/fedex-common.php');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = APPPATH.'views/'.ADMINFOLDER.'fedex/Track/TrackService_v14.wsdl';

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
$request['TransactionDetail'] = array('CustomerTransactionId' => 'TRACK');
$request['Version'] = array(
	'ServiceId' => 'trck', 
	'Major' => '14', 
	'Intermediate' => '0', 
	'Minor' => '0'
);
$request['SelectionDetails'] = array(
	'PackageIdentifier' => array(
		'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
		'Value' => $trackingcode // Replace 'XXX' with a valid tracking identifier

	)
);



try {
	if(setEndpoint('changeEndpoint')){
		$newLocation = $client->__setLocation(PRODUCTION_URL.'track');
	}
	//log_message('error', 'Track request : '.json_encode($request), false);
	$response = $client ->track($request);

	//echo json_encode($request);exit;
	$code = '';
	//log_message('error', 'Track response : '.json_encode($response), false);
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
		if($response->HighestSeverity != 'SUCCESS'){
			/*echo '<table border="1">';
			echo '<tr><th>Track Reply</th><th>&nbsp;</th></tr>';
			trackDetails($response->Notifications, '');
			echo '</table>';*/
		}else{
	    	if ($response->CompletedTrackDetails->HighestSeverity != 'SUCCESS'){
				/*echo '<table border="1">';
			    echo '<tr><th>Shipment Level Tracking Details</th><th>&nbsp;</th></tr>';
			    trackDetails($response->CompletedTrackDetails, '');
				echo '</table>';*/
			}else{
				/*echo '<table border="1">';
			    echo '<tr><th>Package Level Tracking Details</th><th>&nbsp;</th></tr>';
			    trackDetails($response->CompletedTrackDetails->TrackDetails, '');
				echo '</table>';*/
				$code = isset($response->CompletedTrackDetails->TrackDetails->StatusDetail->Code)?$response->CompletedTrackDetails->TrackDetails->StatusDetail->Code:'';
				
				if(empty($code) && is_array($response->CompletedTrackDetails->TrackDetails)){
					
					$TrackingNumberUniqueIdentifier = $response->CompletedTrackDetails->TrackDetails[0]->TrackingNumberUniqueIdentifier;
					if(!empty($TrackingNumberUniqueIdentifier)){
						//$trackingcode = json_decode($response,true);
						//print_r($trackingcode);
						//echo json_encode($trackingcode);exit;
						$request['SelectionDetails'] = array(
							'PackageIdentifier' => array(
								'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
								'Value' => $trackingcode // Replace 'XXX' with a valid tracking identifier
							),
							'TrackingNumberUniqueIdentifier'=>$TrackingNumberUniqueIdentifier
						);
						//echo json_encode($request);exit;

						if(setEndpoint('changeEndpoint')){
							$newLocation = $client->__setLocation(PRODUCTION_URL.'track');
						}
						
						$response = $client ->track($request);
						if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
							if ($response->CompletedTrackDetails->HighestSeverity == 'SUCCESS'){
								$code = $response->CompletedTrackDetails->TrackDetails->StatusDetail->Code;
							}
						}
					}
					
					
				}
			}
		}
        //printSuccess($client, $response);
    }else{
        //printError($client, $response);
    } 
    echo $code;
    //writeToLog($client);    // Write to log file   
} catch (SoapFault $exception) {
    //printFault($exception, $client);
}

?>	