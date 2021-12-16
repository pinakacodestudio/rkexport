<?php
// Copyright 2009, FedEx Corporation. All rights reserved.
// Version 12.0.0

require_once(APPPATH.'views/'.ADMINFOLDER.'fedex/fedex-common.php');

//The WSDL is not included with the sample code.
//Please include and reference in $path_to_wsdl variable.
$path_to_wsdl = APPPATH.'views/'.ADMINFOLDER.'fedex/OpenShip/ShipService_v23.wsdl';

if(!is_dir(FEDEX_LABEL_PATH)){
	@mkdir(FEDEX_LABEL_PATH);
}

define('SHIP_MASTERLABEL', 'fedexmasterlabel');    // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
define('SHIP_CODLABEL', 'fedexcodlabel');
define('SHIP_CHILDLABEL', 'fexexchildlabel');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)
//define('SHIP_CHILDLABEL_2', 'shipchildlabel_2.pdf');  // PNG label file. Change to file-extension .pdf for creating a PDF label (e.g. shiplabel.pdf)

ini_set("soap.wsdl_cache_enabled", "0");

$client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information
$returndata = array();
try {
	$masterRequest['WebAuthenticationDetail'] = array(
			/*'ParentCredential' => array(
				'Key' => getProperty('parentkey'), 
				'Password' => getProperty('parentpassword')
			),*/
			'UserCredential' => array(
				'Key' => getProperty('key'), 
				'Password' => getProperty('password')
			)
	);
	
	$masterRequest['ClientDetail'] = array(
		'AccountNumber' => getProperty('shipaccount'), 
		'MeterNumber' => getProperty('meter')
	);
	$masterRequest['TransactionDetail'] = array('CustomerTransactionId' => '*** Express Domestic Shipping Request - Master using PHP ***');
	$masterRequest['Version'] = array(
		'ServiceId' => 'ship', 
		'Major' => '23',
		'Intermediate' => '0', 
		'Minor' => '0'
	);
	$masterRequest['RequestedShipment']['ShipTimestamp'] = date('c');
	// valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
	$masterRequest['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP';
	// valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
	$masterRequest['RequestedShipment']['ServiceType'] = $fedexservice;
	// valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
	$masterRequest['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING';
	$masterRequest['RequestedShipment']['TotalWeight'] = array(
														'Value' => $TotalWeight, 
														'Units' => 'KG' // valid values LB and KG
													);
	$masterRequest['RequestedShipment']['Shipper'] = addShipper($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']);
	$masterRequest['RequestedShipment']['Recipient'] = addRecipient($recipientdetail['customername'],$recipientdetail['mobileno'],$recipientdetail['address'],$recipientdetail['cityname'],$recipientdetail['code'],$recipientdetail['postcode']);
	$masterRequest['RequestedShipment']['ShippingChargesPayment'] = addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']);
	$masterRequest['RequestedShipment']['CustomsClearanceDetail'] = array("DutiesPayment"=>addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
																	"DocumentContent" =>"DOCUMENTS_ONLY",
																	"CustomsValue" => array("Currency"=>"INR","Amount"=>$invoiceamount),
																	"CommercialInvoice" => array("Purpose"=>"SOLD"),
																	"Commodities" => array("NumberOfPieces"=>1,"Description"=>"Product","CountryOfManufacture"=>"IN","Weight" => array("Units"=>"KG","Value"=>$TotalWeight),
																	"Quantity" => 1,
																	"QuantityUnits" => "EA",
																	"UnitPrice" => array("Currency"=>"INR","Amount"=>$invoiceamount)),
																	);
	$masterRequest['RequestedShipment']['SpecialServicesRequested'] = addSpecialServices($fedexcodamount,$recipientdetail['email'],$shipperdetail['email']);
	
	$masterRequest['RequestedShipment']['LabelSpecification'] = addLabelSpecification();
	$masterRequest['RequestedShipment']['PackageCount'] = count($Weight);
	//$masterRequest['RequestedShipment']['RateRequestTypes'] = ["ACCOUNT"];
	//for ($i=0; $i < count($Weight); $i++) {
		$dimensions = array();
		if($length[0]!='' && $width[0]!='' && $height[0]!=''){
			$dimensions = array('Length'=>$length[0],
								'Width'=>$width[0],
								'Height'=>$height[0],
								'Units'=>$units[0]);
		}
		$masterRequest['RequestedShipment']['RequestedPackageLineItems'][] = addPackageLineItem1(1,$Weight[0],$dimensions);
	//}
                                                                                        
	if(setEndpoint('changeEndpoint')){
		$newLocation = $client->__setLocation(PRODUCTION_URL.'ship');
	}

	/* $masterRequest = '{
		"WebAuthenticationDetail": {
			"UserCredential": {
				"Key": "sxDiMZ8zGaPDZBfz",
				"Password": "eBibAfRet5cPkB2592srsgjJ7"
			}
		},
		"ClientDetail": {
			"AccountNumber": "510087100",
			"MeterNumber": "119055696"
		},
		"TransactionDetail": {
			"CustomerTransactionId": "*** Express Domestic Shipping Request - Master using PHP ***"
		},
		"Version": {
			"ServiceId": "ship",
			"Major": "23",
			"Intermediate": "0",
			"Minor": "0"
		},
		"RequestedShipment": {
			"ShipTimestamp": "2019-06-08T10:20:10+05:30",
			"DropoffType": "REGULAR_PICKUP",
			"ServiceType": "FEDEX_EXPRESS_SAVER",
			"PackagingType": "YOUR_PACKAGING",
			"TotalWeight": {
				"Value": 1.25,
				"Units": "KG"
			},
			"Shipper": {
				"Contact": {
					"PersonName": "Roger Motors",
					"CompanyName": "Roger Motors",
					"PhoneNumber": "9725697970"
				},
				"Address": {
					"StreetLines": ["B\/H Rajkamal Petrol Pump Gondal Roa", "d, Rajkot."],
					"City": "Rajkot",
					"StateOrProvinceCode": "GJ",
					"PostalCode": "360002",
					"CountryCode": "IN",
					"CountryName": "INDIA"
				}
			},
			"Recipient": {
				"Contact": {
					"PersonName": "Khodidas Raiyani",
					"CompanyName": "",
					"PhoneNumber": "9725697970"
				},
				"Address": {
					"StreetLines": ["B\/H RAJKAMAL PETROL PUMP, GONDAL RO", "AD"],
					"City": "Addanki",
					"StateOrProvinceCode": "AP",
					"PostalCode": "360004",
					"CountryCode": "IN",
					"Residential": true
				}
			},
			"ShippingChargesPayment": {
				"PaymentType": "SENDER",
				"Payor": {
					"ResponsibleParty": {
						"AccountNumber": "510087100",
						"Contact": {
							"PersonName": "Roger Motors",
							"CompanyName": "Roger Motors",
							"PhoneNumber": "9725697970"
						},
						"Address": {
							"StreetLines": ["B\/H Rajkamal Petrol Pump Gondal Roa", "d, Rajkot."],
							"City": "Rajkot",
							"StateOrProvinceCode": "GJ",
							"PostalCode": "360002",
							"CountryCode": "IN"
						}
					}
				}
			},
			"CustomsClearanceDetail": {
				"DutiesPayment": {
					"PaymentType": "SENDER",
					"Payor": {
						"ResponsibleParty": {
							"AccountNumber": "510087100",
							"Contact": {
								"PersonName": "Roger Motors",
								"CompanyName": "Roger Motors",
								"PhoneNumber": "9725697970"
							},
							"Address": {
								"StreetLines": ["B\/H Rajkamal Petrol Pump Gondal Roa", "d, Rajkot."],
								"City": "Rajkot",
								"StateOrProvinceCode": "GJ",
								"PostalCode": "360002",
								"CountryCode": "IN"
							}
						}
					}
				},
				"DocumentContent": "DOCUMENTS_ONLY",
				"CustomsValue": {
					"Currency": "INR",
					"Amount": "3979.99"
				},
				"CommercialInvoice": {
					"Purpose": "SOLD"
				},
				"Commodities": {
					"NumberOfPieces": 1,
					"Description": "Product",
					"CountryOfManufacture": "IN",
					"Weight": {
						"Units": "KG",
						"Value": 1.25
					},
					"Quantity": 1,
					"QuantityUnits": "EA",
					"UnitPrice": {
						"Currency": "INR",
						"Amount": "3979.99"
					}
				}
			},
			"SpecialServicesRequested": {
				"SpecialServiceTypes": ["COD", "EVENT_NOTIFICATION"],
				"CodDetail": {
					"CodCollectionAmount": {
						"Currency": "INR",
						"Amount": "3979.99"
					},
					"CollectionType": "CASH"
				},
				"EventNotificationDetail": {
					"EventNotifications": [{
						"Role": "RECIPIENT",
						"Events": ["ON_DELIVERY", "ON_SHIPMENT", "ON_EXCEPTION", "ON_TENDER"],
						"NotificationDetail": {
							"NotificationType": "EMAIL",
							"EmailDetail": {
								"EmailAddress": "khodidas@rkinfotechindia.com"
							},
							"Localization": {
								"LanguageCode": "EN"
							}
						},
						"FormatSpecification": {
							"Type": "HTML"
						}
					}, {
						"Role": "SHIPPER",
						"Events": ["ON_DELIVERY", "ON_SHIPMENT", "ON_EXCEPTION", "ON_TENDER"],
						"NotificationDetail": {
							"NotificationType": "EMAIL",
							"EmailDetail": {
								"EmailAddress": "khodidas@rkinfotechindia.com"
							},
							"Localization": {
								"LanguageCode": "EN"
							}
						},
						"FormatSpecification": {
							"Type": "HTML"
						}
					}]
				}
			},
			"LabelSpecification": {
				"LabelFormatType": "COMMON2D",
				"ImageType": "PDF",
				"LabelStockType": "PAPER_7X4.75"
			},
			"PackageCount": 1,
			"RequestedPackageLineItems": [{
				"SequenceNumber": 1,
				"GroupPackageCount": 1,
				"Weight": {
					"Value": "1.250",
					"Units": "KG"
				}
			}]
		}
	}'; */
	log_message('error', 'Shipment Request : '.json_encode($masterRequest), false);
	$masterResponse = $client->processShipment(($masterRequest));  // FedEx web service invocation for master label
	log_message('error', 'Shipment Response : '.json_encode($masterResponse), false);
	//writeToLog($client);    // Write to log file
	
	if ($masterResponse->HighestSeverity != 'FAILURE' && $masterResponse->HighestSeverity != 'ERROR'){
	    //printSuccess($client, $masterResponse);
	    //echo 'Generating label ...'. Newline;
	    //echo json_encode($masterResponse);exit;
	    $returndata['TrackingNumber'] = $masterResponse->CompletedShipmentDetail->MasterTrackingId->TrackingNumber;
	    // Create PNG or PDF label
	    // Set LabelSpecification.ImageType to 'PDF' for generating a PDF label
	    $fp = fopen(FEDEX_LABEL_PATH.SHIP_MASTERLABEL.$returndata['TrackingNumber'].'.pdf', 'wb');   
	    fwrite($fp, $masterResponse->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image); //Create PNG or PDF file
	    fclose($fp);
	    //echo 'Label <a href="./'.SHIP_MASTERLABEL.'">'.SHIP_MASTERLABEL.'</a> was generated. Processing package#1 ...';
	 	$returndata['Label'][] = array('type'=>1,'file'=>SHIP_MASTERLABEL.$returndata['TrackingNumber'].'.pdf');
	 	if(count($Weight)>1){
	 		for ($i=1; $i < count($Weight); $i++) {
	 			$childRequest = array();
	 			$childRequest['WebAuthenticationDetail'] = array(
					/*'ParentCredential' => array(
						'Key' => getProperty('parentkey'), 
						'Password' => getProperty('parentpassword')
					),*/
					'UserCredential' => array(
						'Key' => getProperty('key'), 
						'Password' => getProperty('password')
					)
				);
			    
			    $childRequest['ClientDetail'] = array(
			    	'AccountNumber' => getProperty('shipaccount'), 
			    	'MeterNumber' => getProperty('meter')
			    );
			    $childRequest['TransactionDetail'] = array('CustomerTransactionId' => '*** Express Domestic Shipping Request Child '.($i).' using PHP ***');
			    $childRequest['Version'] = array(
			    	'ServiceId' => 'ship', 
			    	'Major' => '23', 
			    	'Intermediate' => '0', 
			    	'Minor' => '0'
			    );
			    $childRequest['RequestedShipment']['ShipTimestamp'] = date('c');
				// valid values REGULAR_PICKUP, REQUEST_COURIER, DROP_BOX, BUSINESS_SERVICE_CENTER and STATION
				$childRequest['RequestedShipment']['DropoffType'] = 'REGULAR_PICKUP';
				// valid values STANDARD_OVERNIGHT, PRIORITY_OVERNIGHT, FEDEX_GROUND, ...
				$childRequest['RequestedShipment']['ServiceType'] = $fedexservice;
				// valid values FEDEX_BOX, FEDEX_PAK, FEDEX_TUBE, YOUR_PACKAGING, ...
				$childRequest['RequestedShipment']['PackagingType'] = 'YOUR_PACKAGING';
				$childRequest['RequestedShipment']['TotalWeight'] = array(
																	'Value' => $Weight[$i],
																	'Units' => 'KG' // valid values LB and KG
																);
				$childRequest['RequestedShipment']['Shipper'] = addShipper($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']);
				$childRequest['RequestedShipment']['Recipient'] = addRecipient($recipientdetail['customername'],$recipientdetail['mobileno'],$recipientdetail['address'],$recipientdetail['cityname'],$recipientdetail['code'],$recipientdetail['postcode']);
				$childRequest['RequestedShipment']['ShippingChargesPayment'] = addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']);
				$childRequest['RequestedShipment']['CustomsClearanceDetail'] = array("DutiesPayment"=>addShippingChargesPayment($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
																				"DocumentContent" =>"DOCUMENTS_ONLY",
																				"CustomsValue" => array("Currency"=>"INR","Amount"=>$invoiceamount),
																				"CommercialInvoice" => array("Purpose"=>"SOLD"),
																				"Commodities" => array("NumberOfPieces"=>1,"Description"=>"ABCD","CountryOfManufacture"=>"IN","Weight" => array("Units"=>"KG","Value"=>$Weight[$i]),
																				"Quantity" => 1,
																				"QuantityUnits" => "EA",
																				"UnitPrice" => array("Currency"=>"INR","Amount"=>$invoiceamount)),
																				);
				
				$childRequest['RequestedShipment']['LabelSpecification'] = addLabelSpecification();
				$childRequest['RequestedShipment']['PackageCount'] = count($Weight);
				$childRequest['RequestedShipment']['MasterTrackingId'] = $masterResponse->CompletedShipmentDetail->MasterTrackingId;
				$childRequest['RequestedShipment']['RateRequestTypes'] = ["ACCOUNT"];
				$childRequest['RequestedShipment']['PackageDetail'] = 'INDIVIDUAL_PACKAGES';
																				
				$dimensions = array();
				if($length[$i]!='' && $width[$i]!='' && $height[$i]!=''){
					$dimensions = array('Length'=>$length[$i],
										'Width'=>$width[$i],
										'Height'=>$height[$i],
										'Units'=>$units[$i]);
				}

				$childRequest['RequestedShipment']['RequestedPackageLineItems'][] = addPackageLineItem1($i+1,$Weight[$i],$dimensions);
				
				$childResponse = $client->processShipment($childRequest);  // FedEx web service invocation  for child label #1
	    
			    //writeToLog($client);    // Write to log file
			    
			    if ($childResponse->HighestSeverity != 'FAILURE' && $childResponse->HighestSeverity != 'ERROR'){
			        //printSuccess($client, $childResponse);
			        
			        // Create PNG or PDF label
			        // Set LabelSpecification.ImageType to 'PDF' for generating a PDF label
			        $fp = fopen(FEDEX_LABEL_PATH.SHIP_CHILDLABEL.$returndata['TrackingNumber']."-".($i).".pdf", 'wb');   
			        fwrite($fp, $childResponse->CompletedShipmentDetail->CompletedPackageDetails->Label->Parts->Image);
			        fclose($fp);
			        //echo 'Label <a href="./shipchildlabel_'.$i.'.pdf">shipchildlabel_'.$i.'.pdf</a> was generated. Processing package#2 ...';   
			        $returndata['Label'][] = array('type'=>2,'file'=>SHIP_CHILDLABEL.$returndata['TrackingNumber']."-".($i).".pdf");

			        if($fedexcodamount!=0 && count($Weight)==($i+1)){
			
					 	//Printing COD label from last child shipment
				        $fp = fopen(FEDEX_LABEL_PATH.SHIP_CODLABEL.$returndata['TrackingNumber'].'.pdf', 'wb');   
				    	fwrite($fp, $childResponse->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image); //Create COD Return PNG or PDF file
				    	fclose($fp);
				    	//echo '<a href="./'.SHIP_CODLABEL.'">'.SHIP_CODLABEL.'</a> was generated.'.Newline;
				    	$returndata['Label'][] = array('type'=>3,'file'=>SHIP_CODLABEL.$returndata['TrackingNumber'].'.pdf');
					}
					
			    }else{
			        echo 'Processing child shipment '.($i) . Newline;
					printError($client, $childResponse);
			    }
	 		}
	 	}else{
	 		if($fedexcodamount!=0){
			
			 	//Printing COD label from last child shipment
		        $fp = fopen(FEDEX_LABEL_PATH.SHIP_CODLABEL.$returndata['TrackingNumber'].'.pdf', 'wb');   
		    	fwrite($fp, $masterResponse->CompletedShipmentDetail->AssociatedShipments->Label->Parts->Image); //Create COD Return PNG or PDF file
		    	fclose($fp);
		    	$returndata['Label'][] = array('type'=>3,'file'=>SHIP_CODLABEL.$returndata['TrackingNumber'].'.pdf');
		    	//echo '<a href="./'.SHIP_CODLABEL.'">'.SHIP_CODLABEL.'</a> was generated.'.Newline;
		    }
	 	}

	 	/*****************PICKUP******************/
	 	
	 // 	$path_to_wsdl = APPPATH.'views/'.ADMINFOLDER.'fedex/OpenShip/PickupService_v17.wsdl';

		// ini_set("soap.wsdl_cache_enabled", "0");

		// $client = new SoapClient($path_to_wsdl, array('trace' => 1)); // Refer to http://us3.php.net/manual/en/ref.soap.php for more information

		// $request['WebAuthenticationDetail'] = array(
		// 	/*'ParentCredential' => array(
		// 		'Key' => getProperty('parentkey'),
		// 		'Password' => getProperty('parentpassword')
		// 	),*/
		// 	'UserCredential' => array(
		// 		'Key' => getProperty('key'), 
		// 		'Password' => getProperty('password')
		// 	)
		// );
		// $request['ClientDetail'] = array(
		// 	'AccountNumber' => getProperty('shipaccount'), 
		// 	'MeterNumber' => getProperty('meter')
		// );
		// $request['TransactionDetail'] = array('CustomerTransactionId' => $returndata['TrackingNumber']);
		// $request['Version'] = array(
		// 	'ServiceId' => 'disp', 
		// 	'Major' => 17, 
		// 	'Intermediate' => 0, 
		// 	'Minor' => 0
		// );

		// $request['OriginDetail'] = array(
		// 	'PickupLocation' => addShipper($shipperdetail['businessname'],$shipperdetail['phonenumber'],$shipperdetail['businessaddress'],$shipperdetail['cityname'],$shipperdetail['code'],$shipperdetail['postcode']),
		//    	'PackageLocation' => 'FRONT', // valid values NONE, FRONT, REAR and SIDE
		//     'BuildingPartCode' => 'DEPARTMENT', // valid values APARTMENT, BUILDING, DEPARTMENT, SUITE, FLOOR and ROOM
		//     'BuildingPartDescription' => '3B',
		//     'ReadyTimestamp' => getProperty('pickuptimestamp'), // Replace with your ready date time
		//     'CompanyCloseTime' => '20:00:00'
		// );
		// $request['PackageCount'] = count($Weight);
		// $request['TotalWeight'] = array('Value' => $TotalWeight, 
		// 								'Units' => 'KG' // valid values LB and KG
		// 							);
		// $request['CarrierCode'] = 'FDXE'; // valid values FDXE-Express, FDXG-Ground, FDXC-Cargo, FXCC-Custom Critical and FXFR-Freight
		// //$request['OversizePackageCount'] = '1';
		// //$request['CourierRemarks'] = 'This is a test.  Do not pickup';
		// try {
		// 	if(setEndpoint('changeEndpoint')){
		// 		$newLocation = $client->__setLocation(PRODUCTION_URL.'pickup');
		// 	}
		// 	log_message('error', 'Pickup Request : '.json_encode($request), false);
		// 	$response = $client ->createPickup($request);
		// 	log_message('error', 'Pickup Request : '.json_encode($response), false);

		//     if ($response -> HighestSeverity != 'FAILURE' && $response -> HighestSeverity != 'ERROR'){
		//         //echo 'Pickup confirmation number is: '.$response -> PickupConfirmationNumber .Newline;
		//         //echo 'Location: '.$response -> Location .Newline;
		//         //printSuccess($client, $response);
		//     }else{
		//         //printError($client, $response);
		//         $returndata =  array("label"=>$response->Notifications->Message);
		// 		echo json_encode($returndata);exit;
		//     } 
		    
		// } catch (SoapFault $exception) {
		//     //printFault($exception, $client);           
		//     $returndata =  array("label"=>"Problem with the fedex server!");
		// 	echo json_encode($returndata);exit;
		// }
	 	/***********************************/
	 	
	 	echo json_encode($returndata);
	}else{
		$returndata =  array($masterResponse->Notifications->Message);
		echo json_encode($returndata);
		//exit;
	    //echo 'Processing Master shipment' . Newline;
		//printError($client, $masterResponse);
	}
	
	 //writeToLog($client);    // Write to log file
} catch (SoapFault $exception) {
	

    printFault($exception, $client);
    $returndata =  array("Problem with the fedex server!");
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
function addPackageLineItem1($SequenceNumber,$Weight,$Dimensions=array()){
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
		'Dimensions' => $Dimensions
	);
	if(empty($Dimensions)){
		unset($packageLineItem['Dimensions']);
	}
	return $packageLineItem;
}
?>