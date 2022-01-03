<?php

class General_model extends CI_Model {

    function __construct() {
        parent::__construct();
        //$this->db->query("SET time_zone = '+0:00'");
    }
    function encryptIt($q){

        // $secret_key = 'conductexam10008php';
        // $secret_iv = 'php10008conductexam'; // for information
        // $cryptKey = hash('sha256', $secret_key);
        // $iv = substr(hash('sha256', $secret_iv), 0, 16);
        $encrypt_method = "AES-256-CBC";
        $cryptKey = 'dbb6321f3acc7c7fffb0452138a12f167817c9106dcb9d427cef204c8f6cf4ac';
        // encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = '7ff7f9f491592afa'; // it must be 16 bytes
        $qEncoded = base64_encode( openssl_encrypt($q, $encrypt_method, $cryptKey, 0, $iv));
        return( $qEncoded );

        /* $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qEncoded = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        return( $qEncoded ); */
    }
    function decryptIt($q){
        $encrypt_method = "AES-256-CBC";
        $cryptKey = 'dbb6321f3acc7c7fffb0452138a12f167817c9106dcb9d427cef204c8f6cf4ac';
        $iv = '7ff7f9f491592afa';
        $qDecoded = rtrim(openssl_decrypt(base64_decode($q), $encrypt_method, $cryptKey, 0, $iv));
        return( $qDecoded );

        /* $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded ); */
    }
    function oldencryptIt($q){
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qEncoded = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        return( $qEncoded );
    }
    function olddecryptIt($q){
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
    }
    function getCurrentDateTime() {
       date_default_timezone_set('Asia/Kolkata');

        return date('Y-m-d H:i:s');
    }
    function getCurrentDate() {
       date_default_timezone_set('Asia/Kolkata');

        return date('Y-m-d');
    }
    function convertdate($date,$output_format = 'Y-m-d'){
        return date($output_format,strtotime(str_replace('/', '-',$date)));
    }
    function displaydate($date,$output_format = 'd/m/Y'){
        return date($output_format,strtotime(str_replace('/', '-',$date)));
    }
    function convertdatetime($datetime){
        return date('Y-m-d H:i:s',strtotime(str_replace('/', '-',$datetime)));
    }
    function gettime($output_format='h:i'){
        date_default_timezone_set('Asia/Kolkata');
        return date($output_format);
    }
    function displayapidate($date){
        return date('j-M-Y',strtotime(str_replace('/', '-',$date)));
    }
    function displaydatetime($datetime,$output_format = 'd M Y h:i A'){
        return date_format(date_create($datetime), $output_format);
    }
    function displaydatecustome($date){
        return date_format(date_create($date), 'd M Y ');
    }
    function isSuperAdmin() {
        $user_session_data = $this->session->userdata('ADMIN');
        if (isset($user_session_data['ADMINUSERTYPE']) && $user_session_data['ADMINUSERTYPE'] == 'Super')
            return 1;
        return 0;
    }
    function removeElementWithValue($array, $key, $value){
        foreach ($array as $subKey => $subArray) {
           if($subArray[$key] == $value){
                   unset($array[$subKey]);
              }
        }
        return array_values($array);
    }

    function getDatabseFields($postData, $tableName) {
        $table_fields = $this->getFields($tableName);

        $final = array_intersect_key($postData, $table_fields);

        return $final;
    }

    public function getFields($tableName) {

        $query = $this->readdb->query("SHOW COLUMNS FROM " . $tableName);

        foreach ($query->result() as $row)
            $table_fields[$row->Field] = $row->Field;

        return $table_fields;
    }

    function getDBDateTime() {

        $result = $this->readdb->query("SELECT now() as dt");

        if ($result->num_rows() > 0) {
            $row = $result->row_array();
            return $row['dt'];
        } else
            return '';
    }

    function sanitize($input_array, $messages, $rule = '') {
        $message = array();
        foreach ($input_array as $key => $value) {

            $input_array[$key] = $value;
            if (empty($input_array[$key])) {
                if (isset($messages[$key])) {
                    $message[$key] = $messages[$key];
                    unset($input_array[$key]);
                }
            }
            if (!empty($input_array[$key])) {
                if (isset($rule[$key])) {
                    if (!preg_match($rule[$key], $input_array[$key])) {
                        $message[$key] = $messages[$key];
                        unset($input_array[$key]);
                    }
                }
            }
        }

        return array('message' => $message);
    }

    function checkbox_sanitize($input_array, $messages, $rule = '') {
        $message = array();
        foreach ($messages as $key => $value) {
            if (!in_array($key, array_keys($input_array))) {
                $message[$key] = $messages[$key];
            }
        }
        return array('message' => $message);
    }

    /* TO TRUNCATE LONG STRING */

    function TruncateStr($string, $length = 80, $etc = '...', $breakWords = false) {
        if ($length == 0)
            return '';

        if (strlen($string) > $length) {
            $length -= strlen($etc);
            $fragment = substr($string, 0, $length + 1);
            if ($breakWords)
                $fragment = substr($fragment, 0, -1);
            else
                $fragment = preg_replace('/\s+(\S+)?$/', '', $fragment);
            return $fragment . " " . $etc;
        } else
            return $string;
    }


    function time_difference($time_1, $time_2) {

        $val_1 = new DateTime($time_1);
        $val_2 = new DateTime($time_2);

        $interval = $val_1->diff($val_2);
        $year = $interval->y;
        $month = $interval->m;
        $day = $interval->d;
        $hour = $interval->h;
        $minute = $interval->i;
        $second = $interval->s;

        $output = '';

        if ($year > 0) {
            if ($year > 1) {
                $output .= $year . " years ";
            } else {
                $output .= $year . " year ";
            }
        }

        if ($month > 0) {
            if ($month > 1) {
                $output .= $month . " months ";
            } else {
                $output .= $month . " month ";
            }
        }

        if ($day > 0) {
            if ($day > 1) {
                $output .= $day . " days ";
            } else {
                $output .= $day . " day ";
            }
        }

        if ($hour > 0) {
            if ($hour > 1) {
                $output .= $hour . " hours ";
            } else {
                $output .= $hour . " hour ";
            }
        }

        if ($minute > 0) {
            if ($minute > 1) {
                $output .= $minute . " minutes ";
            } else {
                $output .= $minute . " minute ";
            }
        }

        if ($second > 0) {
            if ($second > 1) {
                $output .= $second . " seconds";
            } else {
                $output .= $second . " second";
            }
        }

        return $output;
    }

    function format_size($size) {
        $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
        if ($size == 0) {
            return('n/a');
        } else {
            return (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sizes[$i]);
        }
    }

    function resizeimage($path, $file, $width, $height, $rename_fileName = 0){

        $config['image_library'] = 'gd2';
        $config['source_image'] = $path.$file;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width'] = $width;
        $config['height'] = $height;

        $this->load->library('image_lib');
        $this->image_lib->clear();
        $this->image_lib->initialize($config);

        $this->image_lib->resize();
        
        @unlink($path.$file);
        $temp = explode('.', $file );
        $extension = array_pop($temp);
       
        if($rename_fileName == 1){
            $name = implode('.', $temp );
        }else{
            $name = preg_replace("/[^a-zA-Z0-9-]/", "-",implode('.', $temp ));
        }
        $file = $name.".".$extension;
        rename($path.$name."_thumb.".$extension, $path.$file);
    }

    function uk_date_to_mysql_date($date) {
        $date_year = substr($date, 6, 4);
        $date_month = substr($date, 3, 2);
        $date_day = substr($date, 0, 2);
        $date = date("Y-m-d", mktime(0, 0, 0, $date_month, $date_day, $date_year));
        return $date;
    }

    function uk_date_to_mysql_date_search($date) {
        $date_year = substr($date, 3, 4);
        $date_month = substr($date, 0, 2);
        $date = date("Y-m", mktime(0, 0, 0, $date_month, "01", $date_year));
        return $date;
    }

    function datediffInWeeks($date1, $date2) {
        $diff = strtotime($date2, 0) - strtotime($date1, 0);
        return floor($diff / 604800);
    }

    function monthDiff($date1, $date2) {
        $date1 = date(strtotime($date1));
        $date2 = date(strtotime($date2));

        $difference = $date2 - $date1;
        $months = floor($difference / 86400 / 30);

        return $months;
    }

    function getDBDateTimeByTimeZone($DateTime, $TimeZone) {

        $result = $this->readdb->query("SELECT CONVERT_TZ('" . $DateTime . "','" . $TimeZone . "',@@session.time_zone) as dt");

        if ($result->num_rows() > 0) {
            $row = $result->row_array();
            return $row['dt'];
        } else
            return '';
    }
    function getYoutubevideoThumb($url) {

        preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", urldecode($url), $matches);
        return '//img.youtube.com/vi/'.$matches[1].'/mqdefault.jpg';
    }

    function compress($source, $destination, $quality=80) {
        $info = getimagesize($source);
        
        if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/pjpeg') {
            try{
                $image = imagecreatefromjpeg($source);
                @unlink($source);
                imagejpeg($image, $destination, $quality);
            } catch(Exception $e){
                echo $source.' : '.$e->getMessage().'<br>';
            }
        }elseif ($info['mime'] == 'image/gif') {
            try{
                $image = imagecreatefromgif($source);
                @unlink($source);
                imagegif($image, $destination, $quality);
            } catch(Exception $e){
                echo $source.' : '.$e->getMessage().'<br>';
            }
        }elseif ($info['mime'] == 'image/png' || $info['mime'] == 'x-png') {
            try{
                $image = imagecreatefrompng($source);
                @unlink($source);
                imagepng($image, $destination, 7);
            } catch (Exception $e){
                echo $source.' : '.$e->getMessage().'<br>';
            }
        }
        
        return $destination;
    }
    function random_strings($length_of_string) 
    { 
      
        // String of all alphanumeric character 
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; 
      
        // Shufle the $str_result and returns substring 
        // of specified length 
        return substr(str_shuffle($str_result), 0, $length_of_string); 
    } 
    function date_range($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);
    
        while( $current <= $last ) {
    
            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }
    
        return $dates;
    }
    function month_range($first, $last, $output_format = 'd/m/Y' ) {

        $months = array();

        $start = strtotime($first);
        $end = strtotime($last);
        while($start <= $end){

            $months[] = date($output_format, $start);
            //echo date('F Y', $month), PHP_EOL;
            $start = strtotime("+1 month", $start);
        }
    
        return $months;
    }
    function exporttoexcel($data, $headerstyle, $title, $headings, $filename, $amountcolumnalign='', $calculatetotal='', $bgcolorarray=array(), $setboldstyle=array())
    {
        $this->load->library('excel');
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getDefaultStyle()->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        
        //Set Right Align 
        if($amountcolumnalign!=''){
            if(is_array($amountcolumnalign)){
                foreach($amountcolumnalign as $align){
                    //Right Align on Specific Index using array, ex. array('B','D')
                    $this->excel->getActiveSheet()->getStyle($align)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                }
            }else{
                //Right Align on specific index, ex.G,A:E
                $this->excel->getActiveSheet()->getStyle($amountcolumnalign)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            }
        }
        $this->excel->getActiveSheet()->getStyle($headerstyle)->getFont()->setBold(true);
        
        //name the worksheet
        $this->excel->getActiveSheet()->setTitle($title);
        
        /* $headings = array('Sr. No.','Academic Year','School (Branch)','Class Name','Student Name','Payment Date','Payment Type','Fee Amount','Remarks'); */
        
        $col = 'A';
        foreach($headings as $cell) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $this->excel->getActiveSheet()->setCellValue($col.'1',$cell);
            $col++;
        }
        
        $this->excel->getActiveSheet()->fromArray($data, null, 'A2');

        //SET CELL ROW BACKGOUND COLOR USING ARRAY - Ex. array("A2:G10"=>"a9d18e");
        if(!empty($bgcolorarray)){
            foreach($bgcolorarray as $k=>$bgcolor){

                $this->excel->getActiveSheet()->getStyle($k)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                ->getStartColor()
                ->setRGB($bgcolor);
                
            }
        }

        //SET STYLE OF TOTAL AMOUNT ROW  
        if($calculatetotal==1 && $calculatetotal!=''){

            $exp = explode(":",$headerstyle);
            if(isset($exp[1])){
                $Column = preg_replace('/\d+/', '', $exp[1]);    
                $highestRow = $this->excel->setActiveSheetIndex(0)->getHighestRow();
               
                $this->excel->getActiveSheet()->getStyle('A'.$highestRow.':'.$Column.$highestRow)->getFont()->setBold(true);
                $this->excel->getActiveSheet()->getStyle('A'.$highestRow.':'.$Column.$highestRow)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('F28A8C');
                $this->excel->getActiveSheet()->getStyle('A'.$highestRow.':'.$Column.$highestRow)->getFont()->getColor()->setRGB('FFFFFF');
            }
        }
        if(!empty($setboldstyle)){
            foreach($setboldstyle as $value){
                $this->excel->getActiveSheet()->getStyle($value)->getFont()->setBold(true);
            }
        }
        
        if (ob_get_contents()) ob_end_clean();

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        
        //force user to download the Excel file without writing it to server's HD
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        $objWriter->save('php://output');
    }
    function exporttoexcelwithmultiplesheet($data, $headerstyle, $title, $headings, $filename,$destination='')
    {
  
      
        $this->load->library('excel');

       
        foreach($title as $index => $titlerow){
            if($index!=0){
                $this->excel->createSheet();
            }
            $this->excel->setActiveSheetIndex($index);
            $this->excel->getDefaultStyle()->getAlignment()->setWrapText(true)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            
            //Rename sheet
            $this->excel->getActiveSheet()->setTitle($titlerow);

            $this->excel->getActiveSheet()->getStyle($headerstyle[$index])->getFont()->setBold(true);

            $col = 'A';
            foreach($headings[$index] as $cell) {
                $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
                $this->excel->getActiveSheet()->setCellValue($col.'1',$cell);
                $col++;
            }

            $this->excel->getActiveSheet()->fromArray($data[$index], null, 'A2');
        }
       
       
        if (ob_get_contents()) ob_end_clean();
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        
        //save it to Excel5 format (excel 2003 .XLS file), change this to 'Excel2007' (and adjust the filename extension, also the header mime type)
        //if you want to save it as .XLSX Excel 2007 format
       
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        
        //force user to download the Excel file without writing it to server's HD
        // if (ob_get_contents()) ob_end_clean();
        // ob_start();
        // $objWriter->save('php://output');
      
      
        if(!empty($destination)){
            if(!is_dir($destination)){
                @mkdir($destination);
            }
            $objWriter->save(str_replace(__FILE__,$destination.$filename,__FILE__));
            $this->excel->disconnectWorksheets();
            unset($objWriter, $this->excel);
        }else{
            if (ob_get_contents()) ob_end_clean();
            ob_start();
            $objWriter->save('php://output');
        }

     
      
    }

    function saveModuleWiseFiltersOnSession($Ctrl, $type='') {

        $PostData = $this->input->post();
        // print_r($PostData['memberid']); exit; 

        $sessiondata = $this->session->userdata("SESSION_FILTERS");
        if(!is_null($sessiondata)){
            $panelcollapsed = 0;
            if(count($sessiondata)>0){
                if (array_key_exists($Ctrl,$sessiondata)){
                    $panelcollapsed = (isset($sessiondata[$Ctrl]['panelcollapsed']) && $sessiondata[$Ctrl]['panelcollapsed']!='')?$sessiondata[$Ctrl]['panelcollapsed']:'0';
                }
                // $sessiondata=array(); 
            }
            
            if($type=="collapse"){

                if(isset($PostData['panelcollapsed']) && $PostData['panelcollapsed']==1){
                    $sessiondata[$Ctrl]['panelcollapsed'] = $PostData['panelcollapsed'];
                }else{
                    $sessiondata[$Ctrl]['panelcollapsed'] = '0';
                }
                $this->session->set_userdata("SESSION_FILTERS",$sessiondata);
                return $sessiondata[$Ctrl]['panelcollapsed'];
            }else{
                
                $sessiondata[$Ctrl]['panelcollapsed'] = $panelcollapsed;
                if($Ctrl == "Voucher_code" || $Ctrl == "Member"){
                    if(isset($PostData['channelid']) && $PostData['channelid']!=''){
                        $sessiondata[$Ctrl]['channelid'] = implode(",", $PostData['channelid']);
                    }else{
                        $sessiondata[$Ctrl]['channelid'] = "";
                    }
                    if(isset($PostData['memberid']) && $PostData['memberid']!='' && $Ctrl != "Member"){
                        $sessiondata[$Ctrl]['memberid'] = implode(",", $PostData['memberid']);
                    }else{
                        $sessiondata[$Ctrl]['memberid'] = "";
                    }    
                }
                
                if($Ctrl == "Order" || $Ctrl == "Credit_note" || $Ctrl == "Catalog" || $Ctrl == "Member"){
                    if(isset($PostData['startdate'])){
                        $sessiondata[$Ctrl]['startdate'] = $PostData['startdate'];
                    }else{
                        $sessiondata[$Ctrl]['startdate'] = "";
                    }
        
                    if(isset($PostData['enddate'])){
                        $sessiondata[$Ctrl]['enddate'] = $PostData['enddate'];
                    }else{
                        $sessiondata[$Ctrl]['enddate'] = "";
                    }

                    if($Ctrl != "Catalog" || $Ctrl != "Member"){
                        if(isset($PostData['status'])){
                            $sessiondata[$Ctrl]['status'] = $PostData['status'];
                        }else{
                            $sessiondata[$Ctrl]['status'] = "";
                        }
                    }
                }

                if($Ctrl == "Credit_note" || $Ctrl == "Catalog"){

                    if(isset($PostData['channelid']) && $PostData['channelid']!='' && $Ctrl == "Catalog"){
                        $sessiondata[$Ctrl]['channelid'] = $PostData['channelid'];
                    }else{
                        $sessiondata[$Ctrl]['channelid'] = "";
                    } 

                    if(isset($PostData['memberid']) && $PostData['memberid']!=''){
                        $sessiondata[$Ctrl]['memberid'] = $PostData['memberid'];
                    }else{
                        $sessiondata[$Ctrl]['memberid'] = "";
                    }   
                }
                
                $this->session->set_userdata("SESSION_FILTERS",$sessiondata);
            }
            // echo "<pre>"; print_r($this->session->userdata("SESSION_FILTERS")); exit;
        }else{
            
            $this->session->set_userdata('SESSION_FILTERS',array($Ctrl => array("panelcollapsed"=>0)));
            return 0;
        }
       
    }

	function validateemailaddress($email) {
		if(!empty($email)) {
			$domain = $this->validateDomain($email);
			$validate = $this->validateEmail($email);
	
			if($domain == false || $validate == false) {
				return false;
			}
        }
        return true;
	}

	function validateEmail($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}

	function validateDomain($mail){
		$domain = explode('@',$mail);
		
		if(empty($mail) || empty($domain[1])){
			return false;
			die;
		}		 
		
		$handle = curl_init($domain[1]);
		curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, 1);
		// Get the HTML or whatever is linked in $url. 
		$response = curl_exec($handle);
	
		// Check for 404 (file not found). 
		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		curl_close($handle);
		if(empty($response) || empty($httpCode) || $httpCode == 404) {
			return false;
		}else{
			return true;		
		}
    }
    
    function addActionLog($actiontype,$module,$message,$username='',$fullname='')
	{
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url() . 'ADMINID');
		
		$this->load->library("user_agent");
		$browser = $this->agent->browser();
		$browserversion = $this->agent->version();
		$ipaddress = $this->input->ip_address();
        $browser = $browser.' '.$browserversion;
        
        if($username == ""){
            $username = $this->session->userdata(base_url().'ADMINEMAIL');
        }
        if($fullname == ""){
            $fullname = $this->session->userdata(base_url().'ADMINNAME');
        }

		$postArray = array("username" => $username,
							"fullname" => $fullname,
							"actiontype" => $actiontype,
							"module" => $module,
							"message" => $message,
							"ipaddress" => $ipaddress,
							"browser" => $browser,
							"createddate" => $createddate,
							"addedby" => $addedby
						);
		$this->writedb->insert(tbl_actionlog, $postArray);
    }

    function checkPassword($password){
        
        /**Password Validation : 1 alphabetic, 1 numeric & 1 special character allowed */
        $pattern = '/^(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[!@#$%_\'\'""\/=(){}\^\&*-.`\?]).{6,20}$/';

        if (!preg_match($pattern, $password)) {
            return false;
        }else{
            return true;
        }
    }

    function saveimagefromurl($url,$savepath){

        /**Save image in directory using image url */
        $filename = basename($url);

        if (strpos($filename, '?') !== false) {
            $t = explode('?',$filename);
            $filename = $t[0];            
        } 
       
        $ch = curl_init($url);
        $fp = fopen($savepath.$filename, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_exec($ch);
        curl_close($ch);
		fclose($fp);

		return $filename;
    }
    
    function distance($lat1, $lon1, $lat2, $lon2, $unit='K') {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
          return 0;
        }
        else {
          $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);
      
          if ($unit == "K") {
            return number_format($miles * 1.609344,2);
          } else if ($unit == "N") {
            return number_format($miles * 0.8684,2);
          }else if ($unit == "METER") {
            return number_format($miles * 1609.34,2);
          } else {
            return number_format($miles,2);
          }
        }
    }
    function startsWith ($string, $startString) { 
        $len = strlen($startString); 
        return (substr($string, 0, $len) === $startString); 
    } 

    function generateTransactionPrefixByType($transactiontype,$channelid=0,$memberid=0){

        $this->load->model('Transaction_prefix_model','Transaction_prefix');  
        $transactionprefixdata = $this->Transaction_prefix->getTransactionPrefixDataByType($transactiontype,$channelid,$memberid);
        
        if(!empty($transactionprefixdata) && $transactionprefixdata['transactionprefix']==1){
            $format = $transactionprefixdata['transactionprefixformat'];

            if (strpos($format, '{YYYY-YY}') !== false) {
                $year = date("Y")."-".(date("y")+1);
                $format = str_replace("{YYYY-YY}",$year,$format);
            }
            if (strpos($format, '{YY-YY}') !== false) {
                $year = date("y")."-".(date("y")+1);
                $format = str_replace("{YY-YY}",$year,$format);
            }
            if (strpos($format, '{autonumber}') !== false) {
                $lastno = !empty($transactionprefixdata['lastno'])?$transactionprefixdata['lastno']:1;
                $suffixlength = $transactionprefixdata['suffixlength'];
                
                $format = str_replace("{autonumber}",str_pad($lastno, $suffixlength, '0', STR_PAD_LEFT),$format);
            }
            return $format;
        }else{
            return time().rand(10,99).rand(10,99).rand(10,99).rand(10,99);
        }
    }

    function updateTransactionPrefixLastNoByType($transactiontype,$channelid=0,$memberid=0){

        $this->load->model('Transaction_prefix_model','Transaction_prefix'); 
        $transactionprefixdata = $this->Transaction_prefix->getTransactionPrefixDataByType($transactiontype,$channelid,$memberid);
         
        if(!empty($transactionprefixdata) && $transactionprefixdata['transactionprefix']==1){
            
            $updatedata = array("lastno"=>($transactionprefixdata['lastno']+1));
            $this->Transaction_prefix->_where = array("id"=>$transactionprefixdata['id']);
            $this->Transaction_prefix->Edit($updatedata);
        }
    }
    function replaceStringWithDashes($slug){

        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
    }

    function convertTimetoWords($time){

        $hour = date("H", strtotime($time));
        $minute = date("i", strtotime($time));
        $second = date("s", strtotime($time));

        $text = $hour.(($hour>1)?" hours ":" hour ");
        $text .= ($minute>0)?$minute." minutes ":""; 
        $text .= ($second>0)?$second." seconds ":""; 

        return $text;
    }

    function getShipperDetails(){
        $query = $this->readdb->select("s.*,IFNULL((SELECT GROUP_CONCAT(email) FROM ".tbl_companycontactdetails." WHERE type=1),'') as email,IFNULL((SELECT GROUP_CONCAT(mobileno) FROM ".tbl_companycontactdetails." WHERE type=1),'') as mobileno")
                        ->from(tbl_settings." as s")
                        ->get();
        return $query->row_array();                 
    }
    function exportToPDF($filename,$header,$html,$return=0){
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $file = $filename;
        $pdfFilePath = $file;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   40, // margin top
                   15, // margin bottom
                    3, // margin header
                    10); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
       
        ob_start();
        if (ob_get_contents()) ob_end_clean();
        
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        if($return==1){
            $pdf->Output($pdfFilePath, "F");
        }else{
            $pdf->Output($pdfFilePath, "D");
        }
    }
}

?>