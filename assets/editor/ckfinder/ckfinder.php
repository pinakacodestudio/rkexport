<?php
/*
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2013, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */

require_once 'core/ckfinder_php5.php' ;
function CheckAuthentication() {
return true;
}

$currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
$currentURL .= $_SERVER["SERVER_NAME"];
$url = $_SERVER['PHP_SELF'];
$url = str_replace("/admin/editor/ckfinder/core/connector/php/connector.php","",$url);

$baseUrl = $currentURL.$url.'/uploaded/ckeditor/';

$enabled = true;
$config['SecureImageUploads'] = false;
$config['ChmodFolders'] = 0777 ;
