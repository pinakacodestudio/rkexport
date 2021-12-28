<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

if (!function_exists('lang')) {
	function lang($line, $id = '') {
		$CI = &get_instance();
		$line = $CI->lang->line($line);

		if ($id != '') {
			$line = '<label for="' . $id . '">' . $line . "</label>";
		}

		return $line;
	}
}

if (!function_exists('pre')) {
	function pre($data) {
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		exit;
	}

}


if (!function_exists('je')) {

	function je($data) {
		if (is_array($data)) {
			echo json_encode($data);
		} else {
			echo json_encode(array($data));
		}

		exit;
	}

}

if (!function_exists('compulsoryAlphaNumeric')) {
	function compulsoryAlphaNumeric($my_str) {
		$alpha_okay = FALSE;
		$num_okay = FALSE;
		$string_okay = TRUE;

		$without_alpha = str_replace(
			array_merge(
				range("a", "z"), range("A", "Z")
			), '', strtolower($my_str)
		);

		if (strlen($my_str) > strlen($without_alpha)) {
			$alpha_okay = true;
		}

		$without_num = str_replace(range(0, 9), '', $without_alpha);

		if (strlen($without_alpha) > strlen($without_num)) {
			$num_okay = true;
		}

		if (strlen($without_num) > 0) {
			$string_okay = false;
		}

		if (!($alpha_okay)) {
			return 'noalpha';
		} else if (!($num_okay)) {
			return 'nonum';
		} else if ($string_okay == false) {
			return 'specialchar';
		} else {
			return TRUE;
		}

	}

}

if (!function_exists('Xauto_link')) {
	/**
	 * Replace links in text with html links
	 *
	 * @param  string $text
	 * @return string
	 */
	function Xauto_link($str, $attributes = array()) {
		$attrs = '';
		foreach ($attributes as $attribute => $value) {
			$attrs .= " {$attribute}=\"{$value}\"";
		}
		$str = ' ' . $str;
		$str = preg_replace(
			'`([^"=\'>])(((http|https|ftp)://|www.)[^\s<]+[^\s<\.)])`i', '$1<a href="$2"' . $attrs . ' target="_blank">$2</a>', $str
		);
		$str = substr($str, 1);
		$str = preg_replace('`href=\"www`', 'href="http://www', $str);
		// fügt http:// hinzu, wenn nicht vorhanden
		return $str;
	}

}
if (!function_exists('check_url')) {

	function check_url($url) {
		if (preg_match("/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/", $url, $matches)) {
			return 'youtube';
		} else if (preg_match("/^.*(vimeo\.com\/)((channels\/[A-z]+\/)|(groups\/[A-z]+\/videos\/))?([0-9]+)/", $url, $matches)) {
			return 'vimeo';
		} else if (preg_match("/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/", $url, $matches)) {
			return 'dailymotion';
		} else if (preg_match("/^.*(metacafe\.com)(\/watch\/)(\d+)(.*)/i", $url, $matches)) {
			return 'metacafe';
		} else {
			return false;
		}
	}

}

if (!function_exists('generate_token')) {

	function generate_token($len = 32,$numbersonly=false) {

// Array of potential characters, shuffled.
		if($numbersonly){
			$chars = array(
				'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			);
		}else{
			$chars = array(
				'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
				'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
				'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
				'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
			);
		}
		
		shuffle($chars);

		$num_chars = count($chars) - 1;
		$token = '';

// Create random token at the specified length.
		for ($i = 0; $i < $len; $i++) {
			$token .= $chars[mt_rand(0, $num_chars)];
		}

		return $token;
	}

}

function appUploadedBefore($secs) {
	$bit = array(
		' year' => $secs / 31556926 % 12,
		' week' => $secs / 604800 % 52,
		' day' => $secs / 86400 % 7,
		' hour' => $secs / 3600 % 24,
		' minute' => $secs / 60 % 60,
		' second' => $secs % 60,
	);
	$ret = array();
	foreach ($bit as $k => $v) {
		if ($v > 1) {
			$ret[] = $v . $k . 's';
		}

		if ($v == 1) {
			$ret[] = $v . $k;
		}

	}
	if (count($ret) == 0) {
		$ret[] = '0 seconds ago.';
	} else {
		if (count($ret) == 1) {
			array_splice($ret, count($ret) - 1, 0);
		} else {
			array_splice($ret, count($ret) - 1, 0, 'and');
		}

		$ret[] = 'ago.';
	}
	return join(' ', $ret);
}

if (!function_exists('get_random_password')) {
	/**
	 * Generate a random password.
	 *
	 * get_random_password() will return a random password with length 6-8 of lowercase letters only.
	 *
	 * @access    public
	 * @param    $chars_min the minimum length of password (optional, default 6)
	 * @param    $chars_max the maximum length of password (optional, default 8)
	 * @param    $use_upper_case boolean use upper case for letters, means stronger password (optional, default false)
	 * @param    $include_numbers boolean include numbers, means stronger password (optional, default false)
	 * @param    $include_special_chars include special characters, means stronger password (optional, default false)
	 *
	 * @return    string containing a random password
	 */
	function get_random_password($chars_min = 6, $chars_max = 8, $use_upper_case = false, $include_numbers = false, $include_special_chars = false) {
		$length = rand($chars_min, $chars_max);
		$selection = 'aeuoyibcdfghjklmnpqrstvwxz';
		if ($include_numbers) {
			$selection .= "1234567890";
		}
		if ($include_special_chars) {
			$selection .= "!@\"#$%&[]{}?|";
		}

		$password = "";
		for ($i = 0; $i < $length; $i++) {
			$current_letter = $use_upper_case ? (rand(0, 1) ? strtoupper($selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))]) : $selection[(rand() % strlen($selection))];
			$password .= $current_letter;
		}

		return $password;
	}

}

if (!function_exists('random_username')) {
	
	function random_username($string) {
		$pattern = " ";
		$firstPart = strstr(strtolower($string), $pattern, true);
		$secondPart = substr(strstr(strtolower($string), $pattern, false), 0,3);
		$nrRand = rand(0, 100);

		$username = trim($firstPart).trim($secondPart).trim($nrRand);
		return $username;
	}

}

if (!function_exists('ws_response')) {

	function ws_response($Status, $Message = "", $ResponseData = array(), $extraData = array()) {

		if (!empty($ResponseData) && (is_array($ResponseData) || is_object($ResponseData))) {
			$data["result"] = $Status;
			$data["data"] = $ResponseData;
			$data["error"] = $Message;
		}else if (empty($ResponseData) && $Message=="") {
			$data["result"] = $Status;
			$data["data"] = $ResponseData;
			$data["error"] = $Message;
		}else {
			$data["result"] = $Status;
			$data["error"] = $Message;
		}

		if (!empty($extraData)) {
			foreach ($extraData as $key => $val) {
				$data[$key] = $val;
			}
		}
		echo json_encode($data);exit;
	}

}

if (!function_exists('nice_number')) {
	
	function nice_number($n) {
        // first strip any formatting;
        $n = (0+str_replace(",", "", $n));

        // is this a number?
        if (!is_numeric($n)) return false;

        // now filter it;
        if ($n > 1000000000000) return round(($n/1000000000000), 2).' T';
        elseif ($n > 1000000000) return round(($n/1000000000), 2).' B';
        elseif ($n > 1000000) return round(($n/1000000), 2).' M';
        elseif ($n > 1000) return round(($n/1000), 2).' K';

        return number_format($n);
    }


}

function convert_number($number){
	$no = round($number);
   	$point = round($number - $no, 2) * 100;
   	$hundred = null;
   	$digits_1 = strlen($no);
   	$i = 0;
   	$str = array();
   	$words = array('0' => '',
   					'1' => 'One',
   					'2' => 'Two',
    				'3' => 'Three', 
    				'4' => 'Four', 
    				'5' => 'Five', 
    				'6' => 'Six',
    				'7' => 'Seven', 
    				'8' => 'Eight', 
    				'9' => 'Nine',
    				'10' => 'Ten', 
    				'11' => 'Eleven', 
    				'12' => 'Twelve',
    				'13' => 'Thirteen', 
    				'14' => 'Fourteen',
    				'15' => 'Fifteen', 
    				'16' => 'Sixteen', 
    				'17' => 'Seventeen',
    				'18' => 'Eighteen', 
    				'19' => 'Nineteen', 
    				'20' => 'Twenty',
    				'30' => 'Thirty', 
    				'40' => 'Forty', 
    				'50' => 'Fifty',
    				'60' => 'Sixty', 
    				'70' => 'Seventy',
    				'80' => 'Eighty', 
    				'90' => 'Ninety');

   	$digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
   	while ($i < $digits_1) {

    	$divider = ($i == 2) ? 10 : 100;
    	$number = floor($no % $divider);
    	$no = floor($no / $divider);
    	$i += ($divider == 10) ? 1 : 2;
     	if ($number) {
        	$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        	$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        	$str [] = ($number < 21) ? $words[$number] . " " . $digits[$counter] . $plural . " " . $hundred : $words[floor($number / 10) * 10]. " " . $words[$number % 10] . " ". $digits[$counter] . $plural . " " . $hundred;
     	} else {
     		$str[] = null;
  		}
  	}
	$str = array_reverse($str);
	$result = implode('', $str);
	$points = ($point) ? "." . $words[$point / 10] . " " . $words[$point = $point % 10] : '';
	return "Rupees  ".$result . $points . " Only.";
}

function mb_truncate($str, $limit) {
	return mb_substr(strip_tags($str), 0, $limit, 'UTF-8') . (mb_strlen($str) > $limit ? '...' : '');
}

if (!function_exists('sendPushNotification')) {
	// function sendPushNotification($token, $message, $generatePayloardData, $notificationType) {

	//     /* Code Starts for sending Push Notification */
	//     $CI =& get_instance();
	//     $CI->load->library('Apn');
	//     // $this->load->library('apn');
	//     $this->apn->payloadMethod = 'enhance'; // ???????? ???? ????? ??? ???????
	//     $this->apn->connectToPush();

	//     // ?????????? ??????????? ?????????? ? notification
	//     $this->apn->setData(array('someKey' => true));

	//     $send_result = $this->apn->sendMessage($token, $generatePayloardData, $message, $notificationType, /* badge */ 1, /* sound */ 'default');

	//     if ($send_result)
	//         log_message('debug', '?????????? ???????');
	//     else
	//         log_message('error', $this->apn->error);

	//     $this->apn->disconnectPush();

	//     /* Code Ends for sending Push Notification */
	// }

	function sendPushNotification($token, $message, $generatePayloardData, $notificationType) {

		/* Code Starts for sending Push Notification */
		$CI = &get_instance();
		$CI->load->library('Apn');

		$apn = new Apn();
		// $this->load->library('apn');
		$apn->payloadMethod = 'enhance'; // ???????? ???? ????? ??? ???????
		$apn->connectToPush();

		// ?????????? ??????????? ?????????? ? notification
		$apn->setData(array('someKey' => true));

		$send_result = $apn->sendMessage($token, $generatePayloardData, $message, $notificationType, /* badge */1, /* sound */'default');

		if ($send_result) {
			log_message('debug', '?????????? ???????');
		} else {
			log_message('error', $apn->error);
		}

		$apn->disconnectPush();

		/* Code Ends for sending Push Notification */
	}
}
if (!function_exists('numberFormat')) {
	function numberFormat($number,$decimals=0,$thousands_sep=",") {
		
		if($thousands_sep==","){
			setlocale(LC_MONETARY,"en_IN");
		}
        return (int)$number;
	}

}
/* End of file programmer_helper.php */
/* Location: ./system/helpers/programmer_helper.php */