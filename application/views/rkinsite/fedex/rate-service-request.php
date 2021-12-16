<?php

$this->load->view(ADMINFOLDER.'fedex/fedex');

use FedEx\RateService;
use FedEx\RateService\ComplexType;
use FedEx\RateService\SimpleType;

//RateRequest
$rateRequest = new ComplexType\RateRequest();

//authentication & client details
$rateRequest->WebAuthenticationDetail->UserCredential->Key = FEDEX_KEY;
$rateRequest->WebAuthenticationDetail->UserCredential->Password = FEDEX_PASSWORD;
$rateRequest->ClientDetail->AccountNumber = FEDEX_ACCOUNT_NUMBER;
$rateRequest->ClientDetail->MeterNumber = FEDEX_METER_NUMBER;

$rateRequest->TransactionDetail->CustomerTransactionId = 'testing rate service request';

//version
$rateRequest->Version->ServiceId = 'crs';
$rateRequest->Version->Major = 10;
$rateRequest->Version->Minor = 0;
$rateRequest->Version->Intermediate = 0;

$rateRequest->ReturnTransitAndCommit = true;

//shipper
$rateRequest->RequestedShipment->Shipper->Address->StreetLines = $shipperdetail['businessaddress'];
$rateRequest->RequestedShipment->Shipper->Address->City = $shipperdetail['cityname'];
$rateRequest->RequestedShipment->Shipper->Address->StateOrProvinceCode = $shipperdetail['code'];
$rateRequest->RequestedShipment->Shipper->Address->PostalCode = $shipperdetail['postcode'];
$rateRequest->RequestedShipment->Shipper->Address->CountryCode = 'IN';

//recipient
$rateRequest->RequestedShipment->Recipient->Address->StreetLines = $recipient['address'];
//$rateRequest->RequestedShipment->Recipient->Address->StreetLines = ['13450 Farmcrest Ct'];
$rateRequest->RequestedShipment->Recipient->Address->City = $recipient['cityname'];
//$rateRequest->RequestedShipment->Recipient->Address->City = 'Herndon';
//$rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = $recipient['code'];
$rateRequest->RequestedShipment->Recipient->Address->StateOrProvinceCode = 'VA';
//$rateRequest->RequestedShipment->Recipient->Address->PostalCode = $recipient['postcode'];
$rateRequest->RequestedShipment->Recipient->Address->PostalCode = 20171;
$rateRequest->RequestedShipment->Recipient->Address->CountryCode = 'US';

//shipping charges payment
$rateRequest->RequestedShipment->ShippingChargesPayment->PaymentType = SimpleType\PaymentType::_SENDER;
$rateRequest->RequestedShipment->ServiceType = SimpleType\ServiceType::_SENDER;
$rateRequest->RequestedShipment->ShippingChargesPayment->Payor->AccountNumber = FEDEX_ACCOUNT_NUMBER;
$rateRequest->RequestedShipment->ShippingChargesPayment->Payor->CountryCode = 'IN';

//rate request types
//$rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_ACCOUNT, SimpleType\RateRequestType::_LIST];
$rateRequest->RequestedShipment->RateRequestTypes = [SimpleType\RateRequestType::_PREFERRED];

$rateRequest->RequestedShipment->PackageCount = 1;

//create package line items
$rateRequest->RequestedShipment->RequestedPackageLineItems = [new ComplexType\RequestedPackageLineItem()];

//package 1
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Value = $fedexweight;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Weight->Units = SimpleType\WeightUnits::_KG;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Length = 0;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Width = 0;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Height = 0;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->Dimensions->Units = SimpleType\LinearUnits::_IN;
$rateRequest->RequestedShipment->RequestedPackageLineItems[0]->GroupPackageCount = 1;

//package 2
/*$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Weight->Value = 5;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Weight->Units = SimpleType\WeightUnits::_LB;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Length = 20;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Width = 20;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Height = 10;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->Dimensions->Units = SimpleType\LinearUnits::_IN;
$rateRequest->RequestedShipment->RequestedPackageLineItems[1]->GroupPackageCount = 1;*/

//echo json_encode($rateRequest->toArray(0));exit;

$rateServiceRequest = new RateService\Request();
$rateServiceRequest->getSoapClient()->__setLocation(RateService\Request::TESTING_URL); //use production URL
log_message('error', 'Rate : '.json_encode($rateRequest), false);
$rateReply = $rateServiceRequest->getGetRatesReply($rateRequest); // send true as the 2nd argument to return the SoapClient's stdClass response.
$response = array();
if (!empty($rateReply->RateReplyDetails)) {
    foreach ($rateReply->RateReplyDetails as $rateReplyDetail) {
        //echo json_encode($rateReplyDetail->ServiceType);
        //var_dump($rateReplyDetail->ServiceType);
        //var_dump($rateReplyDetail->DeliveryTimestamp);
        if (!empty($rateReplyDetail->RatedShipmentDetails)) {
            foreach ($rateReplyDetail->RatedShipmentDetails as $ratedShipmentDetail) {
                //var_dump($ratedShipmentDetail->ShipmentRateDetail->RateType . ": " . $ratedShipmentDetail->ShipmentRateDetail->TotalNetCharge->Amount);
                $response[] = array("RateType" => $ratedShipmentDetail->ShipmentRateDetail->RateType,
                                    "ServiceType" => $rateReplyDetail->ServiceType,
                                    "Currency" => $ratedShipmentDetail->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Currency,
                                    "Amount" => $ratedShipmentDetail->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Amount);
                //echo $ratedShipmentDetail->ShipmentRateDetail->RateType.':'.$ratedShipmentDetail->ShipmentRateDetail->TotalNetChargeWithDutiesAndTaxes->Amount."<br>";
            }
        }
        //echo "<hr />";
    }
}
echo json_encode($response);
//echo json_encode($rateReply->toArray(0));

?>
