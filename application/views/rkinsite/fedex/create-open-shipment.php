<?php

$this->load->view(ADMINFOLDER.'fedex/fedex');

use FedEx\OpenShipService\Request;
use FedEx\OpenShipService\ComplexType;
use FedEx\OpenShipService\SimpleType;


/********************************************************
 * Create initial open shipment request with 1 package...
 ********************************************************/
$shipDate = new \DateTime($shipDate);

$createOpenShipmentRequest = new ComplexType\CreateOpenShipmentRequest();
// web authentication detail
$createOpenShipmentRequest->WebAuthenticationDetail->UserCredential->Key = FEDEX_KEY;
$createOpenShipmentRequest->WebAuthenticationDetail->UserCredential->Password = FEDEX_PASSWORD;
// client detail
$createOpenShipmentRequest->ClientDetail->MeterNumber = FEDEX_METER_NUMBER;
$createOpenShipmentRequest->ClientDetail->AccountNumber = FEDEX_ACCOUNT_NUMBER;
// version
$createOpenShipmentRequest->Version->ServiceId = 'ship';
$createOpenShipmentRequest->Version->Major = 11;
$createOpenShipmentRequest->Version->Intermediate = 0;
$createOpenShipmentRequest->Version->Minor = 0;

// package 1
$requestedPackageLineItem1 = new ComplexType\RequestedPackageLineItem();
$requestedPackageLineItem1->SequenceNumber = 1;
$requestedPackageLineItem1->ItemDescription = 'Product description 1';
$requestedPackageLineItem1->Dimensions->Width = 10;
$requestedPackageLineItem1->Dimensions->Height = 10;
$requestedPackageLineItem1->Dimensions->Length = 15;
$requestedPackageLineItem1->Dimensions->Units = SimpleType\LinearUnits::_IN;
$requestedPackageLineItem1->Weight->Value = $Weight;
$requestedPackageLineItem1->Weight->Units = SimpleType\WeightUnits::_LB;

// requested shipment
$createOpenShipmentRequest->RequestedShipment->DropoffType = SimpleType\DropoffType::_REGULAR_PICKUP;
$createOpenShipmentRequest->RequestedShipment->ShipTimestamp = $shipDate->format('c');
$createOpenShipmentRequest->RequestedShipment->ServiceType = "FEDEX_2_DAY";
//$createOpenShipmentRequest->RequestedShipment->ServiceType = $ServiceType;
$createOpenShipmentRequest->RequestedShipment->PackagingType = SimpleType\PackagingType::_YOUR_PACKAGING;
$createOpenShipmentRequest->RequestedShipment->LabelSpecification->ImageType = SimpleType\ShippingDocumentImageType::_PDF;
$createOpenShipmentRequest->RequestedShipment->LabelSpecification->LabelFormatType = SimpleType\LabelFormatType::_COMMON2D;
$createOpenShipmentRequest->RequestedShipment->LabelSpecification->LabelStockType = SimpleType\LabelStockType::_PAPER_4X6;
$createOpenShipmentRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED];
//$createOpenShipmentRequest->RequestedShipment->Surcharges->SurchargeType = SimpleType\SurchargeType::_COD;
$createOpenShipmentRequest->RequestedShipment->PackageCount = 1;
$createOpenShipmentRequest->RequestedShipment->RequestedPackageLineItems = [$requestedPackageLineItem1];

// requested shipment shipper
$createOpenShipmentRequest->RequestedShipment->Shipper->AccountNumber = FEDEX_ACCOUNT_NUMBER;
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->StreetLines = $shipperdetail['businessaddress'];
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->City = $shipperdetail['cityname'];
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = $shipperdetail['code'];
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->PostalCode = $shipperdetail['postcode'];
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->CountryCode = 'IN';
$createOpenShipmentRequest->RequestedShipment->Shipper->Contact->CompanyName = $shipperdetail['businessname'];
$createOpenShipmentRequest->RequestedShipment->Shipper->Contact->EMailAddress = $shipperdetail['email'];
$createOpenShipmentRequest->RequestedShipment->Shipper->Contact->PhoneNumber = '1-123-123-1234';

/*$createOpenShipmentRequest->RequestedShipment->Shipper->Address->StreetLines = ['1234 Main Street'];
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->City = 'Anytown';
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = 'NY';
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->PostalCode = '12345';
$createOpenShipmentRequest->RequestedShipment->Shipper->Address->CountryCode = 'US';
$createOpenShipmentRequest->RequestedShipment->Shipper->Contact->CompanyName = 'Company Name';
$createOpenShipmentRequest->RequestedShipment->Shipper->Contact->PersonName = 'Person Name';
$createOpenShipmentRequest->RequestedShipment->Shipper->Contact->EMailAddress = 'shipper@example.com';
$createOpenShipmentRequest->RequestedShipment->Shipper->Contact->PhoneNumber = '1-123-123-1234'*/;

// requested shipment recipient
$createOpenShipmentRequest->RequestedShipment->Recipient->Address->StreetLines = ['54321 1st Ave.'];
$createOpenShipmentRequest->RequestedShipment->Recipient->Address->City = 'Anytown';
$createOpenShipmentRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = 'NY';
$createOpenShipmentRequest->RequestedShipment->Recipient->Address->PostalCode = '12345';
$createOpenShipmentRequest->RequestedShipment->Recipient->Address->CountryCode = 'US';
$createOpenShipmentRequest->RequestedShipment->Recipient->Contact->PersonName = 'John Doe';
$createOpenShipmentRequest->RequestedShipment->Recipient->Contact->EMailAddress = 'recipient@example.com';
$createOpenShipmentRequest->RequestedShipment->Recipient->Contact->PhoneNumber = '1-321-321-4321';

// shipping charges payment
$createOpenShipmentRequest->RequestedShipment->ShippingChargesPayment->Payor->ResponsibleParty = $createOpenShipmentRequest->RequestedShipment->Shipper;
$createOpenShipmentRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;

// send the create open shipment request
$openShipServiceRequest = new Request();
$openShipServiceRequest->getSoapClient()->__setLocation($openShipServiceRequest::TESTING_URL); //use production URL

$createOpenShipmentReply = $openShipServiceRequest->getCreateOpenShipmentReply($createOpenShipmentRequest);

// shipment is created and we have an index number
$index = $createOpenShipmentReply->Index;


/********************************
 * Add a package to open shipment
 ********************************/
$addPackagesToOpenShipmentRequest = new ComplexType\AddPackagesToOpenShipmentRequest();

// set index
$addPackagesToOpenShipmentRequest->Index = $index;

// reuse web authentication detail from previous request
$addPackagesToOpenShipmentRequest->WebAuthenticationDetail = $createOpenShipmentRequest->WebAuthenticationDetail;

// reuse client detail from previous request
$addPackagesToOpenShipmentRequest->ClientDetail = $createOpenShipmentRequest->ClientDetail;

// reuse version from previous request
$addPackagesToOpenShipmentRequest->Version = $createOpenShipmentRequest->Version;

// requested package line item
/*$requestedPackageLineItem2 = new ComplexType\RequestedPackageLineItem();
$requestedPackageLineItem2->SequenceNumber = 2;
$requestedPackageLineItem2->ItemDescription = 'New package added to open shipment';
$requestedPackageLineItem2->Dimensions->Width = 20;
$requestedPackageLineItem2->Dimensions->Height = 10;
$requestedPackageLineItem2->Dimensions->Length = 12;
$requestedPackageLineItem2->Dimensions->Units = SimpleType\LinearUnits::_IN;
$requestedPackageLineItem2->Weight->Value = 4;
$requestedPackageLineItem2->Weight->Units = SimpleType\WeightUnits::_LB;
$addPackagesToOpenShipmentRequest->RequestedPackageLineItems = [$requestedPackageLineItem2];*/

// send the add packages to open shipment request
$addPackagesToOpenShipmentReply = $openShipServiceRequest->getAddPackagesToOpenShipmentReply($addPackagesToOpenShipmentRequest);

//var_dump($addPackagesToOpenShipmentReply);

/************************************
 * Retrieve the open shipment details
 ************************************/
$retrieveOpenShipmentRequest = new ComplexType\RetrieveOpenShipmentRequest();
$retrieveOpenShipmentRequest->Index = $index;
$retrieveOpenShipmentRequest->WebAuthenticationDetail = $createOpenShipmentRequest->WebAuthenticationDetail;
$retrieveOpenShipmentRequest->ClientDetail = $createOpenShipmentRequest->ClientDetail;
$retrieveOpenShipmentRequest->Version = $createOpenShipmentRequest->Version;

$retrieveOpenShipmentReply = $openShipServiceRequest->getRetrieveOpenShipmentReply($retrieveOpenShipmentRequest);

//var_dump($retrieveOpenShipmentReply);

/***********************
 * Confirm open shipment
 ***********************/
$confirmOpenShipmentRequest = new ComplexType\ConfirmOpenShipmentRequest();
$confirmOpenShipmentRequest->WebAuthenticationDetail = $createOpenShipmentRequest->WebAuthenticationDetail;
$confirmOpenShipmentRequest->ClientDetail = $createOpenShipmentRequest->ClientDetail;
$confirmOpenShipmentRequest->Version = $createOpenShipmentRequest->Version;
$confirmOpenShipmentRequest->Index = $index;

$confirmOpenShipmentReply = $openShipServiceRequest->getConfirmOpenShipmentReply($confirmOpenShipmentRequest);
//print_r($confirmOpenShipmentReply->toArray(0));
$confirmOpenShipmentReply = $confirmOpenShipmentReply->toArray(0);

$response = array();
if (!empty($confirmOpenShipmentReply['CompletedShipmentDetail'])) {
	$response['TrackingNumber'] = $confirmOpenShipmentReply['CompletedShipmentDetail']['MasterTrackingId']['TrackingNumber'];
}
echo json_encode($confirmOpenShipmentReply);exit;
//echo json_encode($response);

//var_dump($confirmOpenShipmentReply);
