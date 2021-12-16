<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once(APPPATH.'views/'.ADMINFOLDER.'fedex/fedex-common.php');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = APPPATH.'views/'.ADMINFOLDER.'fedex/OpenShip/ShipService_v21.wsdl';

define('SHIP_LABEL', 'shipexpresslabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
define('SHIP_CODLABEL', 'CODexpressreturnlabel.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. CODexpressreturnlabel.pdf)

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
$request['TransactionDetail'] = array('CustomerTransactionId' => '*** Express Domestic Shipping Request using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'ship', 
	'Major' => '21', 
	'Intermediate' => '0', 
	'Minor' => '0'
);
$request['RequestedShipment']['ShipTimestamp'] = date('c');
// valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP';
// valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
$request['RequestedShipment']['ServiceType'] = $fedexservice;
// valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING';
$request['RequestedShipment']['TotalWeight'] = array(
													'Value' => $Weight[0], 
													'Units' => 'KG' // valid values LB and KG
												);
$request['RequestedShipment']['Shipper'] = addShipper($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']);
$request['RequestedShipment']['Recipient'] = addRecipient($recipientdetail['customername'],$recipientdetail['mobileno'],$recipientdetail['address'],$recipientdetail['cityname'],$recipientdetail['code'],$recipientdetail['postcode']);
$request['RequestedShipment']['ShippingChargesPayment'] = addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']);
$request['RequestedShipment']['CustomsClearanceDetail'] = array("DutiesPayment"=>addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
																"DocumentContent" =>"DOCUMENTS_ONLY",
																"CustomsValue" => array("Currency"=>"INR","Amount"=>$invoiceamount),
																"CommercialInvoice" => array("Purpose"=>"SOLD"),
																"Commodities" => array("NumberOfPieces"=>1,"Description"=>"ABCD","CountryOfManufacture"=>"IN","Weight" => array("Units"=>"KG","Value"=>$Weight[0]),
																"Quantity" => 1,
																"QuantityUnits" => "EA",
																"UnitPrice" => array("Currency"=>"INR","Amount"=>$invoiceamount)),
																);
if($fedexcodamount!=0){
	$request['RequestedShipment']['SpecialServicesRequested'] = addSpecialServices($fedexcodamount);
}
$request['RequestedShipment']['LabelSpecification'] = addLabelSpecification();
$request['RequestedShipment']['PackageCount'] = 1;
$request['RequestedShipment']['RateRequestTypes'] = ["ACCOUNT"];
$request['RequestedShipment']['RequestedPackageLineItems'][] = addPackageLineItem1(1,$Weight[0]);
//$request['RequestedShipment']['RequestedPackageLineItems'][] = addPackageLineItem1(2,$Weight);

/*$request['RequestedShipment'] = array(
	//'ShipTimestamp' => date('c'),
	//'DropoffType' => 'REGULAR_PICKUP', // valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
	'ServiceType' => $fedexservice, // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
	'PackagingType' => 'YOUR_PACKAGING', // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
	'TotalWeight' => array(
		'Value' => 50.0, 
		'Units' => 'KG' // valid values LB and KG
	), 
	'Shipper' => addShipper($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
	'Recipient' => addRecipient($recipientdetail['customername'],$recipientdetail['mobileno'],$recipientdetail['address'],$recipientdetail['cityname'],$recipientdetail['code'],$recipientdetail['postcode']),
	'ShippingChargesPayment' => addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
	'CustomsClearanceDetail' => array("DutiesPayment"=>addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
									"DocumentContent" =>"DOCUMENTS_ONLY",
									"CustomsValue" => array("Currency"=>"INR","Amount"=>10.000000),
									"CommercialInvoice" => array("Purpose"=>"SOLD"),
									"Commodities" => array("NumberOfPieces"=>1,"Description"=>"ABCD","CountryOfManufacture"=>"US","Weight" => array("Units"=>"KG","Value"=>10.00),
									"Quantity" => 1,
									"QuantityUnits" => "EA",
									"UnitPrice" => array("Currency"=>"INR","Amount"=>10.000000)),
									),
	'SpecialServicesRequested' => addSpecialServices(),
	'LabelSpecification' => addLabelSpecification(), 
	'PackageCount' => 1,
	'RateRequestTypes' => ["ACCOUNT"],
	'RequestedPackageLineItems' => array(
		'0' => addPackageLineItem1()
	)
);*/



try {
	if(setEndpoint('changeEndpoint')){
		$newLocation = $client->__setLocation(PRODUCTION_URL.'ship');
	}
	//echo json_encode($request);
	//exit;
	$response = $client->processShipment($request);  // FedEx web service invocation

    if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR'){    	
    	printSuccess($client, $response);
        //echo json_encode($response);exit;
        if($fedexcodamount!=0){
	        $fp = fopen(SHIP_CODLABEL, 'wb');
	        fwrite($fp, $response->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image); //Create COD Return PNG or PDF file
	        fclose($fp);
	        echo '<a href="./'.SHIP_CODLABEL.'">'.SHIP_CODLABEL.'</a> was generated.'.Newline;
	    }

        // Create PNG or PDF label
        // Set LabelSpecification.ImageType to 'PDF' or 'PNG for generating a PDF or a PNG label   
        $fp = fopen(SHIP_LABEL, 'wb');   
        fwrite($fp, $response->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image); //Create PNG or PDF file
        fclose($fp);
        echo '<a href="./'.SHIP_LABEL.'">'.SHIP_LABEL.'</a> was generated.';
    }else{
        printError($client, $response);
    }

    writeToLog($client);    // Write to log file
} catch (SoapFault $exception) {
    printFault($exception, $client);
}



function addShipper($CompanyName,$PhoneNumber,$StreetLines,$City,$StateOrProvinceCode,$PostalCode){

	$shipper = array(
		'Contact' => array(
			'PersonName' => $CompanyName,
			'CompanyName' => $CompanyName,
			'PhoneNumber' => $PhoneNumber
		),
		'Address' => array(
			'StreetLines' => array($StreetLines),
			'City' => $City,
			'StateOrProvinceCode' => $StateOrProvinceCode,
			'PostalCode' => $PostalCode,
			'CountryCode' => 'IN'
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
			'StreetLines' => array($StreetLines),
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
				'StreetLines' => array($StreetLines),
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
		'LabelStockType' => 'PAPER_7X4.75'
	);
	return $labelSpecification;
}
function addSpecialServices($fedexcodamount){
	$specialServices = array(
		'SpecialServiceTypes' => array('COD'),
		'CodDetail' => array(
			'CodCollectionAmount' => array(
				'Currency' => 'INR', 
				'Amount' => $fedexcodamount
			),
			'CollectionType' => 'CASH' // ANY, GUARANTEED_FUNDS
		)
	);
	return $specialServices; 
}
function addPackageLineItem1($SequenceNumber,$Weight){
	$packageLineItem = array(
		'SequenceNumber'=>$SequenceNumber,
		'GroupPackageCount'=>$SequenceNumber,
		'Weight' => array(
			'Value' => $Weight,
			'Units' => 'KG'
		),
		/*'Dimensions' => array(
			'Length' => 0,
			'Width' => 0,
			'Height' => 0,
			'Units' => 'CM'
		)*/
	);
	return $packageLineItem;
}
?>