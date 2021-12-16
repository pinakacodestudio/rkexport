<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once(APPPATH.'views/'.ADMINFOLDER.'fedex/fedex-common.php');

$newline = "<br />";
//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.

$path_to_wsdl = APPPATH.'views/'.ADMINFOLDER.'fedex/Rate/RateService_v22.wsdl';

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
$request['TransactionDetail'] = array('CustomerTransactionId' => ' *** Rate Request using PHP ***');
$request['Version'] = array(
	'ServiceId' => 'crs', 
	'Major' => '22', 
	'Intermediate' => '0', 
	'Minor' => '0'
);
$request['ReturnTransitAndCommit'] = true;
$request['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP'; // valid values REGULAR_PICKUP, REQUEST_COURIER, ...
$request['RequestedShipment']['ShipTimestamp'] = date('c');
$request['RequestedShipment']['ServiceType'] = $fedexservice; // valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
$request['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING'; // valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
/*$request['RequestedShipment']['TotalInsuredValue']=array(
	'Ammount'=>100,
	'Currency'=>'USD'
);*/
$request['RequestedShipment']['Shipper'] = addShipper($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']);
//print_r($recipientdetail);exit;
$request['RequestedShipment']['Recipient'] = addRecipient($recipientdetail['customername'],$recipientdetail['mobileno'],$recipientdetail['address'],$recipientdetail['cityname'],$recipientdetail['code'],$recipientdetail['postcode']);
$request['RequestedShipment']['ShippingChargesPayment'] = addShippingChargesPayment();
$request['RequestedShipment']['PackageCount'] = count($fedexweight);
$request['RequestedShipment']['PreferredCurrency'] = 'INR';
$request['RequestedShipment']['RateRequestType'] = 'PREFERRED';

for ($i=0; $i < count($fedexweight); $i++) { 
	$dimensions = array();
	if($length[$i]!='' && $width[$i]!='' && $height[$i]!=''){
		$dimensions = array('Length'=>$length[$i],
							'Width'=>$width[$i],
							'Height'=>$height[$i],
							'Units'=>$units[$i]);
	}
	$request['RequestedShipment']['RequestedPackageLineItems'][] = addPackageLineItem1(($i+1),$fedexweight[$i],'KG',$dimensions);
}

$request['RequestedShipment']['CustomsClearanceDetail'] = array("DocumentContent" =>"NON_DOCUMENTS",
											"CustomsValue" => array("Currency"=>"INR","Amount"=>$invoiceamount),
											"CommercialInvoice" => array("Purpose"=>"SOLD"));
if($fedexcodamount!=0){
	$request['RequestedShipment']['SpecialServicesRequested'] = addSpecialServices($fedexcodamount);
}

try {
	if(setEndpoint('changeEndpoint')){
		$newLocation = $client->__setLocation(PRODUCTION_URL.'rate');
	}
	//log_message('error', 'Rate : '.json_encode($request), false);
	//print_r($request);exit;
	$response = $client -> getRates($request);
    $responsedata = array();
    if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
    	//echo json_encode($response);exit;
    	$rateReply = $response -> RateReplyDetails;
    	/* if(array_key_exists('DeliveryTimestamp',$rateReply)){
        	$deliveryDate= $rateReply->DeliveryTimestamp;
        }else if(array_key_exists('TransitTime',$rateReply)){
        	$deliveryDate= $rateReply->TransitTime;
        }else {
        	$deliveryDate='';
		} */
		if(isset($rateReply->DeliveryTimestamp)){
        	$deliveryDate= $rateReply->DeliveryTimestamp;
        }else if(isset($rateReply->TransitTime)){
        	$deliveryDate= $rateReply->TransitTime;
        }else {
        	$deliveryDate='';
        }
        if($rateReply->RatedShipmentDetails && is_array($rateReply->RatedShipmentDetails)){
			$amount = number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2);
			$ratetype = $rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->RateType;
			$currency = $rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Currency;
		}elseif($rateReply->RatedShipmentDetails && ! is_array($rateReply->RatedShipmentDetails)){
			$ratetype = $rateReply->RatedShipmentDetails->ShipmentRateDetail->RateType;
			$amount = number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount,2);
			$currency = $rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Currency;
		}
    	$responsedata['error'] = array("RateType" => $ratetype,
                            //"ServiceType" => $rateReplyDetail->ServiceType,
                            "Currency" => $currency,
                            "Amount" => $amount,
                            "DeliveryDate" => $deliveryDate);
    	$responsedata['result'] = 'success';

    	/*$rateReply = $response -> RateReplyDetails;
    	echo '<table border="1">';
        echo '<tr><td>Service Type</td><td>Amount</td><td>Delivery Date</td></tr><tr>';
    	$serviceType = '<td>'.$rateReply -> ServiceType . '</td>';
    	if($rateReply->RatedShipmentDetails && is_array($rateReply->RatedShipmentDetails)){
			$amount = '<td>$' . number_format($rateReply->RatedShipmentDetails[0]->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
		}elseif($rateReply->RatedShipmentDetails && ! is_array($rateReply->RatedShipmentDetails)){
			$amount = '<td>$' . number_format($rateReply->RatedShipmentDetails->ShipmentRateDetail->TotalNetCharge->Amount,2,".",",") . '</td>';
		}
        
        echo $serviceType . $amount. $deliveryDate;
        echo '</tr>';
        echo '</table>';*/
        
        //printSuccess($client, $response);
    }else{
    	$responsedata['result'] = 'fail';
    	$error = array();
	    foreach($response -> Notifications as $noteKey => $note){
			if(is_string($note)){
				if($noteKey=='Message'){
					$error[] = json_encode($note);
				}
	        }else{
	        	//$error[] = json_encode($note->Message);
	        }
		}
    	//$responsedata['error'] = $response -> Notifications->Message;
    	$responsedata['error'] = implode('<br>', $error);
        //printError($client, $response);
    }
    echo json_encode($responsedata);exit;
    //writeToLog($client);    // Write to log file   
} catch (SoapFault $exception) {
   printFault($exception, $client);        
}

function addShipper($CompanyName,$PhoneNumber,$StreetLines,$City,$StateOrProvinceCode,$PostalCode){

	$shipper = array(
		/*'Contact' => array(
			'PersonName' => 'Sender Name',
			'CompanyName' => 'Sender Company Name',
			'PhoneNumber' => '9012638716'
		),*/
		/*'Address' => array(
			'StreetLines' => array($StreetLines),
			'City' => $City,
			'StateOrProvinceCode' => $StateOrProvinceCode,
			'PostalCode' => $PostalCode,
			'CountryCode' => 'IN'
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
	);
	return $shipper;
}
function addRecipient($PersonName,$PhoneNumber,$StreetLines,$City,$StateOrProvinceCode,$PostalCode){
	$recipient = array(
		/*'Contact' => array(
			'PersonName' => 'Recipient Name',
			'CompanyName' => 'Company Name',
			'PhoneNumber' => '9012637906'
		),
		'Address' => array(
			'StreetLines' => array($StreetLines),
			'City' => 'JAIPUR',
			'StateOrProvinceCode' => 'RJ',
			'PostalCode' => '302011',
			'CountryCode' => 'IN',
			'Residential' => false
		)*/
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
function addShippingChargesPayment(){
	$shippingChargesPayment = array(
		'PaymentType' => 'SENDER', // valid values RECIPIENT, SENDER and THIRD_PARTY
		'Payor' => array(
			'ResponsibleParty' => array(
				'AccountNumber' => getProperty('billaccount'),
				'CountryCode' => 'IN'
			)
		)
	);
	return $shippingChargesPayment;
}
function addSpecialServices($amount){
	$specialServices = array(
		'SpecialServiceTypes' => array('COD'),
		'CodDetail' => array(
			'CodCollectionAmount' => array(
				'Currency' => 'INR', 
				'Amount' => $amount
			),
			'CollectionType' => 'CASH' // ANY, GUARANTEED_FUNDS
		)
	);
	return $specialServices; 
}
function addPackageLineItem1($SequenceNumber,$WeightValue,$WeightUnits,$Dimensions=array()){

	$packageLineItem = array(
		'SequenceNumber'=>$SequenceNumber,
		'GroupPackageCount'=>1,
		'Weight' => array(
			'Value' => $WeightValue,
			'Units' => $WeightUnits
		),
		/*'Dimensions' => array(
			'Length' => 0,
			'Width' => 0,
			'Height' => 0,
			'Units' => 'IN'
		)*/
		'Dimensions' => $Dimensions
	);
	if(empty($Dimensions)){
		unset($packageLineItem['Dimensions']);
	}
	return $packageLineItem;
}
?>