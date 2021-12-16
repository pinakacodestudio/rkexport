<?php
//Change these values below.
if(isset($fedexaccount)){
	define('FEDEX_ACCOUNT_NUMBER', $fedexaccount['accountnumber']);
	define('FEDEX_METER_NUMBER', $fedexaccount['meternumber']);
	define('FEDEX_KEY', $fedexaccount['apikey']);
	define('FEDEX_PASSWORD', $fedexaccount['password']);	
}


if (!defined('FEDEX_ACCOUNT_NUMBER') || !defined('FEDEX_METER_NUMBER') || !defined('FEDEX_KEY') || !defined('FEDEX_PASSWORD')) {
    die("The constants 'FEDEX_ACCOUNT_NUMBER', 'FEDEX_METER_NUMBER', 'FEDEX_KEY', and 'FEDEX_PASSWORD' need to be defined in: " . realpath(__FILE__));
}

spl_autoload_register(function ($class_name) {
	require_once(APPPATH . "/third_party/".str_replace('\\', '/', $class_name) . ".php");
});