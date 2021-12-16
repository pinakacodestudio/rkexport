<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once(APPPATH.'views/'.ADMINFOLDER.'fedex/fedex-common.php');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = APPPATH.'views/'.ADMINFOLDER.'fedex/Pickup/PickupService_v17.wsdl';

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
$returndata = array();
try {
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
	$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Create Pickup Request using PHP ***');
	$request['Version'] = array(
		'ServiceId' => 'disp', 
		'Major' => 17, 
		'Intermediate' => 0, 
		'Minor' => 0
	);

	$request['OriginDetail'] = array(
			'PickupLocation' => addShipper($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
		   	'PackageLocation' => 'FRONT', // valid values NONE, FRONT, REAR and SIDE
		    'BuildingPartCode' => 'DEPARTMENT', // valid values APARTMENT, BUILDING, DEPARTMENT, SUITE, FLOOR and ROOM
		    'BuildingPartDescription' => '3B',
		    'ReadyTimestamp' => $readytime, // Replace with your ready date time
		    'CompanyCloseTime' => '20:00:00'
		);
	$request['PackageCount'] = $totalpackage;
	$request['TotalWeight'] = array('Value' => $totalweight, 
									'Units' => 'KG' // valid values LB and KG
								);
	$request['CarrierCode'] = 'FDXE'; // valid values FDXE-Express, FDXG-Ground, FDXC-Cargo, FXCC-Custom Critical and FXFR-Freight
	//$request['OversizePackageCount'] = '1';
	//$request['CourierRemarks'] = 'This is a test.  Do not pickup';
	try {
		if(setEndpoint('changeEndpoint')){
			$newLocation = $client->__setLocation(PRODUCTION_URL.'pickup');
		}
		log_message('error', 'Pickup Request : '.json_encode($request), false);
		$response = $client ->createPickup($request);
		log_message('error', 'Pickup Request : '.json_encode($response), false);

	    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
	        //echo 'Pickup confirmation number is: '.$response -> PickupConfirmationNumber .Newline;
	        //echo 'Location: '.$response -> Location .Newline;
	        //printSuccess($client, $response);
	        $returndata =  array("error"=>0,"label"=>'Pickup confirmation number is: '.$response->PickupConfirmationNumber);
			echo json_encode($returndata);
	    }else{
	        //printError($client, $response);
	        $returndata =  array("error"=>1,"label"=>$response->Notifications->Message);
			echo json_encode($returndata);
	    } 
	    
	} catch (SoapFault $exception) {
	    //printFault($exception, $client);           
	    $returndata =  array("error"=>1,"label"=>"Problem with the fedex server!");
		echo json_encode($returndata);
	}
	
} catch (SoapFault $exception) {
    //printFault($exception, $client);
    $returndata =  array("error"=>1,"Problem with the fedex server!");
	echo json_encode($returndata);
}

function addShipper($CompanyName,$PhoneNumber,$StreetLines,$City,$StateOrProvinceCode,$PostalCode){

	$shipper = array(
		'Contact' => array(
			'PersonName' => $CompanyName,
			'CompanyName' => $CompanyName,
			'PhoneNumber' => $PhoneNumber
		),
		'Address' => array(
			'StreetLines' => str_split($StreetLines, 35),
			'City' => $City,
			'StateOrProvinceCode' => $StateOrProvinceCode,
			'PostalCode' => $PostalCode,
			'CountryCode' => 'IN',
			'CountryName' => 'INDIA',
			/*'StateOrProvinceCode' => 'AL',
			'PostalCode' => 35004,
			'CountryCode' => 'US',
			'CountryName' => 'United States',*/
		)
	);
	return $shipper;
}
function addRecipient($PersonName,$PhoneNumber,$StreetLines,$City,$StateOrProvinceCode,$PostalCode){
	$recipient = array(
		'Contact' => array(
			'PersonName' => $PersonName,
			'CompanyName' => '',
			'PhoneNumber' => $PhoneNumber
		),
		'Address' => array(
			'StreetLines' => str_split($StreetLines, 35),
			'City' => $City,
			'StateOrProvinceCode' => $StateOrProvinceCode,
			'PostalCode' => $PostalCode,
			'CountryCode' => 'IN',
			'Residential' => true
		)
	);
	return $recipient;	                                    
}
function addShippingChargesPayment($CompanyName,$PhoneNumber,$StreetLines,$City,$StateOrProvinceCode,$PostalCode){
	$shippingChargesPayment = array('PaymentType' => 'SENDER',
        'Payor' => array(
		'ResponsibleParty' => array(
			'AccountNumber' => getProperty('billaccount'),
			/*'Contact' => array(
				'PersonName' => 'Recipient Name',
				'CompanyName' => 'Recipient Company Name',
				'PhoneNumber' => '1234567890',
				'EMailAddress' => 'admin@gmail.com',
			),
			'Address' => array(
				'StreetLines' => array($StreetLines),
				'City' => $City,
				'StateOrProvinceCode' => $StateOrProvinceCode,
				'PostalCode' => $PostalCode,
				'CountryCode' => 'IN',
				'Residential' => true
			)*/
			'Contact' => array(
				'PersonName' => $CompanyName,
				'CompanyName' => $CompanyName,
				'PhoneNumber' => $PhoneNumber
			),
			'Address' => array(
				'StreetLines' => str_split($StreetLines, 35),
				'City' => $City,
				'StateOrProvinceCode' => $StateOrProvinceCode,
				'PostalCode' => $PostalCode,
				'CountryCode' => 'IN'
			)
		)
		)
    );
	return $shippingChargesPayment;
}
function addLabelSpecification(){
	$labelSpecification = array(
		'LabelFormatType' => 'COMMON2D', // valid values COMMON2D, LABEL_DATA_ONLY
		'ImageType' => 'PDF',  // valid values DPL, EPL2, PDF, ZPLII and PNG
		//'LabelStockType' => 'PAPER_7X4.75'
		'LabelStockType' => 'PAPER_7X4.75'
	);
	return $labelSpecification;
}
function addSpecialServices($fedexcodamount,$Recipientemail,$Shipperemail){

	$EventNotifications[0] = array('Role'=>'RECIPIENT',
									'Events'=>array('ON_DELIVERY','ON_SHIPMENT','ON_EXCEPTION','ON_TENDER'),
									'NotificationDetail'=>array('NotificationType'=>'EMAIL',
									'EmailDetail'=>array('EmailAddress'=>$Recipientemail),
									'Localization'=>array('LanguageCode'=>'EN')),
									'FormatSpecification'=>array('Type'=>'HTML')
								);
	$EventNotifications[1] = array('Role'=>'SHIPPER',
									'Events'=>array('ON_DELIVERY','ON_SHIPMENT','ON_EXCEPTION','ON_TENDER'),
									'NotificationDetail'=>array('NotificationType'=>'EMAIL',
									'EmailDetail'=>array('EmailAddress'=>$Shipperemail),
									'Localization'=>array('LanguageCode'=>'EN')),
									'FormatSpecification'=>array('Type'=>'HTML')
								);
	if($fedexcodamount!=0){
		
		$specialServices = array(
			'SpecialServiceTypes' => array('COD','EVENT_NOTIFICATION'),
			'CodDetail' => array(
				'CodCollectionAmount' => array(
					'Currency' => 'INR', 
					'Amount' => $fedexcodamount
				),
				'CollectionType' => 'CASH' // ANY, GUARANTEED_FUNDS
			),
			'EventNotificationDetail'=>array(
				'EventNotifications'=>$EventNotifications
			),

		);
	}else{
		$specialServices = array(
			'SpecialServiceTypes' => array('EVENT_NOTIFICATION'),
			'EventNotificationDetail'=>array(
				'EventNotifications'=>$EventNotifications
			),
		);
	}
	
	return $specialServices; 
}
function addSpecialEmailNotificationServices(){
	$specialServices = array(
		'SpecialServiceTypes' => array('EMAIL_NOTIFICATION'),
		'EmailNotificationDetail'=>array('Recipients'=>array('EmailNotificationRecipient'=>'SHIPPER',
															'EmailAddress'=>'abc@gmail.com',
															//'NotificationEventsRequested'=>array('ON_DELIVERY','ON_SHIPMENT'),
															'Format'=>'HTML',
															//'Localization'=>array('Language'=>'EN')
														)
									)
	);
	return $specialServices; 
}
function addPackageLineItem1($SequenceNumber,$Weight){
	$packageLineItem = array(
		'SequenceNumber'=>$SequenceNumber,
		'GroupPackageCount'=>1,
		'Weight' => array(
			'Value' => $Weight,
			'Units' => 'KG'
		),
		/*'Dimensions' => array(
			'Length' => 10,
			'Width' => 10,
			'Height' => 10,
			'Units' => 'CM'
		)*/
	);
	return $packageLineItem;
}
?>