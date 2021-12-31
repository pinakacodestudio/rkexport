<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');
/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/* * ***************************************************************
 *  DEFINE CONSTANTS FOR SITE CONFIG
 * *************************************************************** */

define('_PATH', substr(dirname(__FILE__), 0, -19));
define('_URL', substr($_SERVER['PHP_SELF'], 0, -(strlen($_SERVER['SCRIPT_FILENAME']) - strlen(_PATH))));

$portal = $_SERVER["SERVER_NAME"];
include APPPATH . 'config/client.php';
$clientfolder = '';
if (!empty($portaldetail[$portal])) {
	$clientfolder = $portaldetail[$portal]['folder'];
}

define('SITE_PATH', _PATH . "/");

define("ADMINFOLDER", "rkinsite/");
define("CHANNELFOLDER", "channel/");
define("MEMBERFRONTFOLDER", "member/");
	
if($portal == 'localhost'){
	define('DOMAIN_URL', 'http://' . $_SERVER['HTTP_HOST'].'/rkexport/');
}else{
	define('DOMAIN_URL', 'http://' . $_SERVER['HTTP_HOST'].'/');
}


define('DOMAIN_PREFIX', substr(rtrim(ltrim($_SERVER['HTTP_HOST'],'www.'),'.com'),0,3));

/* DEFINE CONSTANTS FOR ADMIN */
define('ADMIN_URL', DOMAIN_URL .ADMINFOLDER);
define('CSS_ADMIN_URL', "admin/css/");
define('ADMIN_CSS_URL', DOMAIN_URL .'assets/'. CSS_ADMIN_URL);
define('JS_ADMIN_URL', "admin/js/");
define('ADMIN_JS_URL', DOMAIN_URL .'assets/'. JS_ADMIN_URL);
define('PLUGIN_ADMIN_URL',"admin/plugins/");
define('ADMIN_PLUGIN_URL', DOMAIN_URL .'assets/'. PLUGIN_ADMIN_URL);

/* DEFINE CONSTANTS FOR CHANNEL */
define('CHANNEL_URL', DOMAIN_URL .CHANNELFOLDER);
define('CSS_CHANNEL_URL', "channel/css/");
define('CHANNEL_CSS_URL', DOMAIN_URL .'assets/'. CSS_CHANNEL_URL);
define('PLUGIN_CHANNEL_URL',"channel/plugins/");
define('CHANNEL_PLUGIN_URL', DOMAIN_URL .'assets/'. PLUGIN_CHANNEL_URL);
define('JS_CHANNEL_URL', "channel/js/");
define('CHANNEL_JS_URL', DOMAIN_URL .'assets/'. JS_CHANNEL_URL);

/* DEFINE CONSTANTS FOR FRONT */
define('FRONT_URL', DOMAIN_URL);
define('FRONT_CSS_URL', DOMAIN_URL . "assets/css/");
define('FRONT_PLUGIN_URL', DOMAIN_URL . "assets/plugins/");
define('FRONT_JS_URL', DOMAIN_URL . "assets/js/");

/* DEFINE CONSTANTS FOR MEMBER FRONT */
define('MEMBER_FRONT_URL', DOMAIN_URL.MEMBERFRONTFOLDER);
define('CSS_MEMBER_FRONT_URL', MEMBERFRONTFOLDER."css/");
define('MEMBER_FRONT_CSS_URL', DOMAIN_URL . "assets/".CSS_MEMBER_FRONT_URL);
define('PLUGIN_MEMBER_FRONT_URL',MEMBERFRONTFOLDER."plugins/");
define('MEMBER_FRONT_PLUGIN_URL', DOMAIN_URL . "assets/".PLUGIN_MEMBER_FRONT_URL);
define('JS_MEMBER_FRONT_URL', MEMBERFRONTFOLDER."js/");
define('MEMBER_FRONT_JS_URL', DOMAIN_URL . "assets/".JS_MEMBER_FRONT_URL);

//ADD BUTTON CONSTANTS
define("addbtn_text", "<i class='fa fa-plus'></i> ADD");
define("addbtnicon_text", "<i class='fa fa-plus'></i> ");
define("addbtn_class", "btn btn-primary btn-raised btn-label");
define("addbtn_title", "ADD");

//transfer inquiery
define("transferbtn_text", "<i class='fa fa-exchange'></i>");
define("transferbtn_class", "btn btn-primary btn-raised btn-sm");
define("transferbtn_title", "Transfer");

//DELETE BUTTON CONSTANTS
define("deletebtn_text", "<i class='fa fa-trash-o'></i> DELETE");
define("deletebtn_class", "btn btn-danger btn-raised btn-label");
define("deletebtn_title", "DELETE");

//IMPORT BUTTON CONSTANTS
define("importbtn_text", "<i class='fa fa-upload'></i> Import");
define("importbtn_class", "btn btn-primary btn-raised btn-label");
define("importbtn_title", "Import");

//ASSIGN PRODUCT BUTTON CONSTANTS
define("assignproductbtn_text", "<i class='fa fa-upload'></i> Assign Product");
define("assignproductbtn_class", "btn btn-primary btn-raised btn-label");
define("assignproductbtn_title", "Assign Product");

//UPLOAD PRODUCT IMAGE BUTTON CONSTANTS
define("uploadproductimagebtn_text", "<i class='fa fa-upload'></i> Upload Image");
define("uploadproductimagebtn_class", "btn btn-primary btn-raised btn-label");
define("uploadproductimagebtn_title", "Upload Image");

//EXPORT TO EXCEL BUTTON CONSTANTS
define("exportbtn_text", "<i class='fa fa-download'></i> Excel");
define("exportbtn_class", "btn btn-primary btn-raised btn-label");
define("exportbtn_title", "Export To Excel");

//Enable Disable constants
define("disable_text", "<i class=\'fa fa-ban\' aria-hidden=\'true\'></i>");
define("enable_text", "<i class=\'fa fa-check\' aria-hidden=\'true\'></i>");
define("disable_class", "btn btn-danger btn-raised btn-sm");
define("enable_class", "btn btn-success btn-raised btn-sm");
define("enable_title", "Enable");
define("disable_title", "Disable");

//Delete constants
define("delete_text", "<i class='fa fa-trash-o'></i>");
define("delete_class", "btn btn-danger btn-raised btn-sm");
define("delete_title", "DELETE");

//Edit constants
define("edit_text", "<i class='fa fa-pencil'></i>");
define("edit_class", "btn btn-success btn-raised btn-sm");
define("edit_title", "EDIT");

//Edit Button constants
define("editbtn_text", "<i class='fa fa-edit'></i> Edit");
define("editbtn_class", "btn btn-success btn-raised btn-label");
define("editbtn_title", "EDIT");

//SET ORDER BUTTON CONSTANTS
define("orderbtn_text", "Set Priority");
define("orderbtn_class", "btn btn-primary btn-raised btn-label");
define("orderbtn_title", "Set Priority");

//Reply constants
define("reply_text", "<i class='fa fa-reply'></i>");
define("reply_class", "btn btn-success btn-raised btn-sm");
define("reply_title", "Reply");

//view constants
define("view_text", "<i class='fa fa-eye'></i>");
define("view_class", "btn btn-info btn-raised btn-sm");
define("view_title", "View");

//view document constants
define("viewdoc_text", "<i class='fa fa-eye'></i>");
define("viewdoc_class", "btn btn-inverse btn-raised btn-sm");
define("viewdoc_title", "View");

//Shipping constants
define("shipping_text", "<i class='fa fa-truck'></i>");
define("shipping_class", "btn btn-inverse btn-raised btn-sm");
define("shipping_title", "Shipping Orders");

//Credit constants
define("credit_text", "<i class='fa fa-money'></i>");
define("credit_class", "btn btn-inverse btn-raised btn-sm");
define("credit_title", "Credit Note");


//Re-generate credit note constants
define("regeneratecredit_text", "<i class='fa fa-refresh'></i>");
define("regeneratecredit_class", "btn btn-success btn-raised btn-sm");
define("regeneratecredit_title", "Re-generate Credit Note");

//Approve Unapprove constants
define("unapprove_text", "<i class=\'fa fa-ban\' aria-hidden=\'true\'></i>");
define("approve_text", "<i class=\'fa fa-check\' aria-hidden=\'true\'></i>");
define("unapprove_class", "btn btn-danger btn-raised btn-sm");
define("approve_class", "btn btn-success btn-raised btn-sm");
define("approve_title", "Approve");
define("unapprove_title", "Unapprove");

//Tracking constants
define("track_text", "<i class='fa fa-location-arrow'></i>");
define("track_class", "btn btn-inverse btn-raised btn-sm");
define("track_title", "Track");

//View invoice constants
define("viewpdf_text", "<i class='fa fa-file-pdf-o'></i>");
define("viewpdf_class", "btn btn-primary btn-raised btn-sm");
define("viewpdf_title", "PDF");

//View Quotation constants
define("viewquotation_text", "<i class='fa fa-file-pdf-o'></i>");
define("viewquotation_class", "btn btn-primary btn-raised btn-sm");
define("viewquotation_title", "View Quotation");

//Re-Generate invoice constants
define("regenerateinvoice_text", "<i class='fa fa-refresh'></i>");
define("regenerateinvoice_class", "btn btn-success btn-raised btn-sm");
define("regenerateinvoice_title", "Re-Generate Invoice");

//Send invoice constants
define("sendinvoice_text", "<i class='fa fa-file-text-o'></i>");
define("sendinvoice_class", "btn btn-success btn-raised btn-sm");
define("sendinvoice_title", "Send Invoice");

//Generate Button constants
define("generatebtn_text", "Generate");
define("generatebtn_class", "btn btn-primary btn-raised btn-label");
define("generatebtn_title", "Generate");

//Re-Generate Quotation constants
define("regeneratequotation_text", "<i class='fa fa-refresh'></i>");
define("regeneratequotation_class", "btn btn-success btn-raised btn-sm");
define("regeneratequotation_title", "Re-Generate Quotation");

//Generate invoice constants
define("generateinvoice_text", "<i class='fa fa-file-text-o'></i>");
define("generateinvoice_class", "btn btn-success btn-raised btn-sm");
define("generateinvoice_title", "Create Invoice");

//Cancel constants
define("cancel_text", "<i class='fa fa-times'></i>");
define("cancel_class", "btn btn-danger btn-raised btn-sm");
define("cancel_title", "Cancel");

//Cancel constants
define("cancellink_text", "Cancel");
define("cancellink_class", "btn btn-danger btn-raised");
define("cancellink_title", "Cancel");

//Back constants
define("back_text", "<i class='fa fa-backward'></i> GO BACK");
define("back_class", "btn btn-danger btn-raised btn-sm");
define("back_title", "Go Back");

//Download label constants
define("downloadlabel_text", "<i class='fa fa-download'></i>");
define("downloadlabel_class", "btn btn-warning btn-raised btn-sm");
define("downloadlabel_title", "Download Label");

//Download constants
define("download_text", "<i class='fa fa-download'></i>");
define("download_class", "btn btn-primary btn-sm btn-raised btn-sm");
define("download_title", "Download");

//Download Label constants
define("downloadlblbtn_text", "<i class='fa fa-download'></i> Download");
define("downloadlblbtn_class", "btn btn-danger btn-sm btn-raised btn-sm");
define("downloadlblbtn_title", "Download");

//Download Label constants
define("downloadlbltxt_text", "<i class='fa fa-download'></i> Download");
define("downloadlbltxt_class", "btn btn-primary btn-sm btn-raised btn-sm");
define("downloadlbltxt_title", "Download Identity Proof");

//View Files
define("viewbtn_text", "<i class='fa fa-download'></i>");
define("viewbtn_class", "btn btn-warning btn-sm btn-raised btn-sm");
define("viewbtn_title", "View");

//APPLY FILTER BUTTON CONSTANTS
define("applyfilterbtn_text", "<i class='fa fa-search'></i> Apply Filter");
define("applyfilterbtn_class", "btn btn-primary btn-raised btn-label");
define("applyfilterbtn_title", "Apply Filter");

//RESET PASSWORD BUTTON CONSTANTS
define("resetpasswordbtn_text", "<i class='fa fa-key'></i>");
define("resetpasswordbtn_class", "btn btn-inverse btn-raised btn-sm");
define("resetpasswordbtn_title", "Reset Password");

//Apply Filter text constant
define("APPLY_FILTER", "Apply Filter");

//Search seller button constant
define("searchsellerbtn_text", "<i class='fa fa-search'></i> SELLER");
define("searchsellerbtn_class", "btn btn-primary btn-raised btn-sm");
define("searchsellerbtn_title", "Search Seller");

//Reset button constant
define("resetbtn_text", "<i class='fa fa-refresh'></i> RESET");
define("resetbtn_class", "btn btn-info btn-raised btn-sm");
define("resetbtn_title", "Reset Seller");

//PRINT BUTTON WITHOUT LABEL CONSTANTS
define("print_text", "<i class='fa fa-print'></i>");
define("print_class", "btn btn-primary btn-raised btn-sm");
define("print_title", "PRINT");

//PRINT BUTTON WITH LABEL CONSTANTS
define("printbtn_text", "<i class='fa fa-print'></i> PRINT");
define("printbtn_class", "btn btn-primary btn-raised btn-sm");
define("printbtn_title", "PRINT");

//DUPLICATE BUTTON CONSTANTS
define("duplicatebtn_text", "<i class='fa fa-copy'></i>");
define("duplicatebtn_class", "btn btn-warning btn-raised btn-sm");
define("duplicatebtn_title", "DUPLICATE");

//VERIFIED CONSTANTS
define("verifiedbtn_text", "<i class='fa fa-check-circle'></i> Verified");
define("verifiedbtn_class", "badge badge-verified");
define("verifiedbtn_title", "Verified");

//NOT VERIFIED CONSTANTS
define("notverifiedbtn_text", "<i class='fa fa-times-circle'></i> Not Verified");
define("notverifiedbtn_class", "badge badge-notverified");
define("notverifiedbtn_title", "Not Verified");

//Verify button constant
define("verifybtn_text", "<i class='fa fa-check-circle'></i> Verify Now");
define("verifybtn_class", "btn btn-primary btn-raised btn-sm");
define("verifybtn_title", "Verify Now");

//TRACK URL CONSTANT
define("trackurl_text", "<i class='fa fa-map-marker fa-lg'></i>");
define("trackurl_class", "btn btn-info btn-raised btn-sm");
define("trackurl_title", "Tracking URL");

//GENERATE QR CODE CONSTANT
define("generateqrcode_text", "<i class='fa fa-qrcode'></i>");
define("generateqrcode_class", "btn btn-warning btn-raised btn-sm");
define("generateqrcode_title", "View QR Code");

//Download Transaction Proof constants
define("downloadfile_text", "<i class='fa fa-download'></i>");
define("downloadfile_class", "btn btn-danger btn-sm btn-raised btn-sm");
define("downloadfile_title", "Download");

//QR Code constants with label
define("qrcode_text", "<i class='fa fa-qrcode'></i> QR Code");
define("qrcode_class", "btn btn-info btn-sm btn-raised btn-sm");
define("qrcode_title", "QR Code");

//View details constants
define("addandnew_text", "Add & New");
define("addandnew_class", "btn btn-primary btn-raised");
define("addandnew_title", "Add & New");

//File Destination
define("DEFAULT_PROFILE", DOMAIN_URL."assets/img/profile/");
define("DEFAULT_IMG", DOMAIN_URL."assets/img/");
define("DEFAULT_IMAGE_PREVIEW", "noimage.jpg");

//Start New Process constant Without Label
define("startprocess_text", "<i class='fa fa-plus'></i>");
define("startprocess_class", "btn btn-info btn-sm btn-raised btn-sm");
define("startprocess_title", "Start New Process");

//Start New Process constant With Label
define("startnewprocess_text", "<i class='fa fa-plus'></i> Start New Process");
define("startnewprocess_class", "btn btn-primary btn-sm btn-raised btn-sm");
define("startnewprocess_title", "Start New Process");

//Start New Process constant With Label
define("clonebtnicon_text", "<i class='fa fa-plus'></i> ");
define("clone_title", "Clone Followup");
define("clonebtnicon_class", "btn btn-primary btn-sm btn-raised btn-sm");

//IN Process label constant
define("inprocess_text", "IN");
define("inprocess_class", "btn btn-primary btn-sm btn-raised btn-sm");
define("inprocess_title", "Stock IN Process");

//OUT Process label constant
define("outprocess_text", "OUT");
define("outprocess_class", "btn btn-primary btn-sm btn-raised btn-sm");
define("outprocess_title", "Stock OUT Process");

//Reprocess constant
define("reprocess_text", "<i class='fa fa-recycle' aria-hidden='true'></i>");
define("reprocess_class", "btn btn-inverse btn-sm btn-raised btn-sm");
define("reprocess_title", "Send for Reprocessing");

//Next process constant
define("nextprocess_text", "<i class='fa fa-forward' aria-hidden='true'></i>");
define("nextprocess_class", "btn btn-warning btn-sm btn-raised btn-sm");
define("nextprocess_title", "Next Process");

//View all process constant
define("viewprocess_text", "<i class='fa fa-list' aria-hidden='true'></i>");
define("viewprocess_class", "btn btn-info btn-sm btn-raised btn-sm");
define("viewprocess_title", "View All Process");

//Assign brand constant
define("assignbrand_text", "<i class='fa fa-plus' aria-hidden='true'></i> Assign Brand");
define("assignbrand_class", "btn btn-primary btn-sm btn-raised btn-sm");
define("assignbrand_title", "Assign Brand");

//EXPORT TO PDF BUTTON CONSTANTS
define("exportpdfbtn_text", "<i class='fa fa-download'></i> PDF");
define("exportpdfbtn_class", "btn btn-primary btn-raised btn-label");
define("exportpdfbtn_title", "Export To PDF");

//ADD PRODUCT ORDER BUTTON CONSTANTS
define("addproductorderbtn_text", "<i class='fa fa-plus'></i> ADD PRODUCT ORDER");
define("addproductorderbtn_class", "btn btn-primary btn-raised btn-label");
define("addproductorderbtn_title", "ADD PRODUCT ORDER");

//ADD PRODUCT ORDER BUTTON CONSTANTS
define("viewmap_text", "<i class='fa fa-map-marker fa-lg'></i>");
define("viewmap_class", "btn btn-primary btn-raised btn-label");
define("viewmap_title", "View Map");

//MAKE PAYMENT BUTTON CONSTANTS
define("makepaymentbtn_text", "<i class='fa fa-credit-card'></i> Make Payment");
define("makepayment_text", "<i class='fa fa-credit-card'></i>");
define("makepayment_class", "btn btn-info btn-raised btn-label");
define("makepayment_title", "Make Payment");

//Send mail button constants
define("sendmail_text", "<i class='fa fa-envelope'></i>");
define("sendmail_class", "btn btn-warning btn-raised btn-sm");
define("sendmail_title", "Send Mail");

//Whatsapp button constants
define("whatsapp_text", "<i class='fa fa-whatsapp'></i>");
define("whatsapp_class", "btn btn-default btn-raised btn-sm");
define("whatsapp_title", "Send on Whatsapp");

//previuos button constants
define("previuos_text", "<i class='fa fa-arrow-left'></i> Prev");
define("previuos_class", "btn btn-info btn-raised btn-sm");
define("previuos_title", "Previuos");

//next button constants
define("next_text", "<i class='fa fa-arrow-right'></i> Next");
define("next_class", "btn btn-info btn-raised btn-sm");
define("next_title", "Next");

//Generate grn constants
define("generategrn_text", "<i class='fa fa-file-text-o'></i>");
define("generategrn_class", "btn btn-success btn-raised btn-sm");
define("generategrn_title", "Create Goods Received Notes");

//Start production button constants
define("startproduction_text", "<i class='fa fa-industry'></i>");
define("startproduction_class", "btn btn-warning btn-raised btn-sm");
define("startproduction_title", "Start Production");

// s3 development
define("AWS_ASSETS_PATH", SITE_PATH."assets/");
define("AWS_UPLOADED_PATH", SITE_PATH."uploaded/".$clientfolder."/");

define("HOMEBANNER_IMG_WIDTH", "2200");
define("HOMEBANNER_IMG_HEIGHT", "700");
define("TESTIMONIALSIMG_IMG_WIDTH", "300");
define("TESTIMONIALSIMG_IMG_HEIGHT", "300");
define("VARIANT_COLOR", "#e60909");
define("UPLOAD_MAX_FILE_SIZE", "3145728"); // 1048576 bytes = 1 MB
define("UPLOAD_MAX_FILE_SIZE_CATALOG", "10485760"); // 1048576 bytes = 1 MB
define("UPLOAD_MAX_VIDEO_FILE_SIZE", "52428800"); // 1048576 bytes = 1 MB
define("UPLOAD_MAX_ZIP_FILE_SIZE", "52428800"); // 1048576 bytes = 1 MB -- LIMIT 50 MB
define("FILE_COMPRESSION", "80");
define("STATUS_DROPDOWN_BTN", "btn-xs");
define("DEFAULT_COUNTRY_ID", "101");
define("DEFAULT_PHONECODE", "+91");
define('PRODUCT_IMG_WIDTH', '720');
define('PRODUCT_IMG_HEIGHT', '540');
define('ATTEMPTS_OTP_ON_HOUR', '3');
define('PROCESS_BATCH_NO', "PB-".date("Y-m-d-H-i"));
define('REPROCESS_BATCH_NO', "RB-".date("Y-m-d-H-i"));
define('DEFAULT_COVER_IMAGE_COLOR', "#0e5b98"); //#1e48bb
define('DEFAULT_PASSWORD', "default123*");
define("FEDEX_TRACK_URL", "https://www.fedex.com/apps/fedextrack/index.html?action=track&tracknumbers={tracknumbers}&locale=en_US&cntry_code=en");
		
/* DATABASE RECORDS LIMIT */
define('PER_PAGE_BLOG', "10");
define('BLOG_CATEGORY_LIMIT', "5");
define('RECENT_BLOG_LIMIT', "5");
define('PER_PAGE_OUR_PRODUCTS', "15");
define('SIDEBAR_PRODUCT_CATEGORY_LIMIT', "10");
define('PER_PAGE_PRODUCT_REVIEW', "10");
define('SIDEBAR_PRODUCT_TAG_LIMIT', "20");
define('DEFAULT_LAT', '20.5937');
define('DEFAULT_LNG', '78.9629');

/* CRM */
define('Inquiry', 'Inquiry');
define('inquiry', 'inquiry');
define('Followup', 'Followup');
define('followup', 'followup');
define('follow_up', 'follow up');
define('Follow_Up', 'Follow up');

define('Member_label', 'Member');
define('member_label', 'member');

/* DATABASE TABLE CONSTANTS */
define('tbl_country', "country");
define('tbl_province', "province");
define('tbl_city', "city");
define('tbl_area', "area");
define('tbl_zone', "zone");
define('tbl_user', "user");
define('tbl_emailtemplate', "emailtemplate");
define('tbl_settings', "systemsetting");
define('tbl_userrole', "userrole");
define('tbl_mainmenu', "mainmenu");
define('tbl_submenu', "submenu");
define('tbl_systemlimit', "	systemlimit");
define('tbl_adminemailverification', "adminemailverification");
define('tbl_smsgateway', "smsgateway");
define('tbl_category', "category");
define('tbl_managecontent', "managecontent");
define('tbl_product', "product");
define('tbl_productprice', "productprice");
define('tbl_productfile', "productfile");
define('tbl_customer', "customer");
define('tbl_memberaddress', "memberaddress");
define('tbl_vouchercodeused', "vouchercodeused");
define('tbl_socialmedia', "socialmedia");
define('tbl_orders', "orders");
define('tbl_orderproduct', "orderproduct");
define('tbl_customeremailverification', "customeremailverification");
define('tbl_smstemplate', "smstemplate");
define('tbl_attribute', "attribute");
define('tbl_variant', "variant");
define('tbl_productcombination', "productcombination");
define('tbl_productcategory', "productcategory");
define('tbl_catalog', "catalog");
define('tbl_fcmdata', "fcmdata");
define('tbl_notification', "notification");
define('tbl_productprices', "productprices");
define('tbl_productimage', "productimage");
define('tbl_orderproducts', "orderproducts");
define('tbl_dealer', "dealer");
define('tbl_productorder', "orders");
define('tbl_invoicesetting', "invoicesetting");
define('tbl_invoice', "invoice");
define('tbl_transaction', "transaction");
define('tbl_feedback', "feedback");
define('tbl_news', "news");
define('tbl_paymentgateway', "paymentgateway");
define('tbl_ordervariant', "ordervariant");
define('tbl_productvariant', "productvariant");
define('tbl_paymentsetting', "paymentsetting");
define('tbl_vendor', "vendor");
define('tbl_memberemailverification', "memberemailverification");
define('tbl_installment', "installment");
define('tbl_orderinstallment', "orderinstallment");
define('tbl_quotation', "quotation");
define('tbl_quotationproducts', "quotationproducts");
define('tbl_quotationvariant', "quotationvariant");
define('tbl_quotationstatuschange', "quotationstatuschange");
define('tbl_vendorproduct', "vendorproduct");
define('tbl_vendorvariantprices', "vendorvariantprices");
define('tbl_systemconfiguration', "systemconfiguration");
define('tbl_cart', "cart");
define('tbl_voucher', "voucher");
define('tbl_vouchercode', "vouchercode");
define('tbl_vendordiscount', "vendordiscount");
define('tbl_productsection', "productsection");
define('tbl_productsectionmapping', "productsectionmapping");
define('tbl_homebanner', "homebanner");
define('tbl_channel', "channel");
define('tbl_member', "member");
define('tbl_memberdiscount', "memberdiscount");
define('tbl_memberproduct', "memberproduct");
define('tbl_membervariantprices', "membervariantprices");
define('tbl_memberidproof', "memberidproof");
define('tbl_newschannelmapping', "newschannelmapping");
define('tbl_contentchannelmapping', "contentchannelmapping");
define('tbl_emailverification', "emailverification");
define('tbl_memberrole', "memberrole");
define('tbl_channelmainmenu', "channelmainmenu");
define('tbl_channelsubmenu', "channelsubmenu");
define('tbl_membermapping', "membermapping");
define('tbl_hsncode', "hsncode");
define('tbl_orderstatuschange', "orderstatuschange");
define('tbl_productinquiry', "productinquiry");
define('tbl_memberratingstatus', "memberratingstatus");
define('tbl_rewardpointhistory', "rewardpointhistory");
define('tbl_transactionproof', "transactionproof");
define('tbl_catalogchannelmapping', "catalogchannelmapping");
define('tbl_orderdeliverydate', "orderdeliverydate");
define('tbl_catlogviewhistory', "catlogviewhistory");
define('tbl_deliveryorderschedule', "deliveryorderschedule");
define('tbl_deliveryproduct', "deliveryproduct");
define('tbl_creditnote', "creditnote");
define('tbl_creditnoteproducts', "creditnoteproducts");
define('tbl_productbasicpricemapping', "productbasicpricemapping");     
define('tbl_pricehistory', "pricehistory");
define('tbl_productpricehistory', "productpricehistory");
define('tbl_memberproductpricehistory', "memberproductpricehistory");
define('tbl_extracharges', "extracharges");
define('tbl_extrachargemapping', "extrachargemapping");
define('tbl_transport', "transport");
define('tbl_cashorbank', "cashorbank");
define('tbl_transactionproducts', "transactionproducts");
define('tbl_transactionvariant', "transactionvariant");
define('tbl_transactionextracharges', "transactionextracharges");
define('tbl_transactiondiscount', "transactiondiscount");
define('tbl_openingbalance', "openingbalance");
define('tbl_offer', "offer");
define('tbl_offerimage', "offerimage");
define('tbl_versioncheck', "versioncheck");
define('tbl_paymentreceipt', "paymentreceipt");
define('tbl_paymentreceipttransactions', "paymentreceipttransactions");
define('tbl_paymentreceiptstatushistory', "paymentreceiptstatushistory");
define('tbl_smsformat', "smsformat");
define('tbl_smsverification', "smsverification");
define('tbl_offercombination', "offercombination");
define('tbl_offermembermapping', "offermembermapping");
define('tbl_offerproduct', "offerproduct");
define('tbl_brand', "brand");
define('tbl_offerpurchasedproduct', "offerpurchasedproduct");
define('tbl_offerparticipants', "offerparticipants");
define('tbl_productunit', "unit");
define('tbl_unitconversation', "unitconversation");
define('tbl_process', "process");
define('tbl_transactionattachment', "transactionattachment");
define('tbl_processgroup', "processgroup");
define('tbl_processgroupmapping', "processgroupmapping");
define('tbl_processgroupoption', "processgroupoption");
define('tbl_processgroupoptionvalue', "processgroupoptionvalue");
define('tbl_processgroupproducts', "processgroupproducts");
define('tbl_processoption', "processoption");
define('tbl_processoptionvalue', "processoptionvalue");
define('tbl_productprocess', "productprocess");
define('tbl_productprocessdetails', "productprocessdetails");
define('tbl_productprocesscertificates', "productprocesscertificates");
define('tbl_productprocessoption', "productprocessoption");
define('tbl_productprocessoptionvalue', "productprocessoptionvalue");
define('tbl_banner', "websitebanner");
define('tbl_store', "storelocation");
define('tbl_subscribe', "subscribe");
define('tbl_mostpopularproduct', "mostpopularproduct");
define('tbl_contact_us', "contactus");
define('tbl_testimonials', "testimonials");
define('tbl_newscategory', "newscategory");
define('tbl_mediacategory', "mediacategory");
define('tbl_photogallery', "photogallery");
define('tbl_videogallery', "videogallery");
define('tbl_advertisement', "advertisement");
define('tbl_frontendmenu', "frontendmenu");
define('tbl_frontendsubmenu', "frontendsubmenu");
define('tbl_ourclient', "ourclient");
define('tbl_blog', "blog");
define('tbl_blogcategory', "blogcategory");
define('tbl_managewebsitecontent', "managewebsitecontent");
define('tbl_relatedproduct', "relatedproduct");
define('tbl_productreview', "productreview");
define('tbl_productreviewbyguest', "productreviewbyguest");
define('tbl_paymentmethod', "paymentmethod");
define('tbl_couriercompany', "couriercompany");
define('tbl_shippingorder', "shippingorder");
define('tbl_shippingpackage', "shippingpackage");
define('tbl_machine', "machine");
define('tbl_machineservicedetails', "machineservicedetails");
define('tbl_productrecepie', "productrecepie");
define('tbl_productrecepiecommonmaterial', "productrecepiecommonmaterial");
define('tbl_productrecepievariantwisematerial', "productrecepievariantwisematerial");
define('tbl_productionplan', "productionplan");
define('tbl_productionplandetail', "productionplandetail");
define('tbl_inquirystatuses', "inquirystatuses");
define('tbl_followupstatuses', "followupstatuses");
define('tbl_followuptype', "followuptype");
define('tbl_leadsource', "leadsource");
define('tbl_department', "department");
define('tbl_designation', "designation");
define('tbl_producttag', "producttag");
define('tbl_producttagmapping', "producttagmapping");
define('tbl_membersociallogin', "membersociallogin");
define('tbl_industrycategory','industrycategory');
define('tbl_expense','expense');
define('tbl_expensecategory','expensecategory');
define('tbl_target','target');
define('tbl_targetduration', "targetduration");
define('tbl_memberstatus','memberstatus');
define('tbl_actionlog','actionlog');
define('tbl_sitemap','sitemap');
define('tbl_crminquiry','crminquiry');
define('tbl_contactdetail','contactdetail');
define('tbl_crminquiryproduct','crminquiryproduct');
define('tbl_crminquirytransferhistory','crminquirytransferhistory');
define('tbl_crmassignmember','crmassignmember');
define('tbl_crmremarkmember','crmremarkmember');
define('tbl_crmfollowup','crmfollowup');
define('tbl_followuptransferhistory','followuptransferhistory');
define('tbl_inquiryproductinstallment','inquiryproductinstallment');
define('tbl_leave','employeeleave');
define('tbl_documents','documents');
define('tbl_trackroute','trackroute');
define('tbl_trackroutetask','trackroutetask');
define('tbl_trackroutelocation','trackroutelocation');
define('tbl_shiprocketsetting','shiprocketsetting');
define('tbl_shiprocketorder','shiprocketorder');
define('tbl_token','token');
define('tbl_fedexdetail','fedexdetail');
define('tbl_fedexshippinglabel','fedexshippinglabel');
define('tbl_fedexshippingorder','fedexshippingorder');
define('tbl_courierdeliverylocation','courierdeliverylocation');
define('tbl_userposition','userposition');
define('tbl_vendorprocess','vendorprocess');
define('tbl_salescommission','salescommission');
define('tbl_salescommissiondetail','salescommissiondetail');
define('tbl_salescommissionmapping','salescommissionmapping');
define('tbl_vehicle','vehicle');
define('tbl_insurance','vehicleinsurance');
define('tbl_vehiclepollutioncertificate','vehiclepollutioncertificate');
define('tbl_vehicleregistrationcertificate','vehicleregistrationcertificate');
define('tbl_vehicletax','vehicletax');
define('tbl_indiamartlead','indiamartlead');
define('tbl_importleadexcel','importleadexcel');
define('tbl_memberwebsitesetting','memberwebsitesetting');
define('tbl_companytransactionprefix','companytransactionprefix');
define('tbl_designationmapping','designationmapping');
define('tbl_rawmaterialrequest','rawmaterialrequest');
define('tbl_rawmaterialrequestproduct','rawmaterialrequestproduct');
define('tbl_approvallevels','approvallevels');
define('tbl_approvallevelsmapping','approvallevelsmapping');
define('tbl_assigngiftproduct','assigngiftproduct');
define('tbl_creditnoteofferdetails','creditnoteofferdetails');
define('tbl_crmquotation','crmquotation');
define('tbl_route','route');
define('tbl_routemember','routemember');
define('tbl_assignedroute','assignedroute');
define('tbl_assignedrouteinvoicemapping','assignedrouteinvoicemapping');
define('tbl_assignedrouteextraproduct','assignedrouteextraproduct');
define('tbl_attendance','attendance');
define('tbl_locationtracking','locationtracking');
define('tbl_nonattendance','nonattendance');
define('tbl_salespersonmember','salespersonmember');
define('tbl_salespersonroute','salespersonroute');
define('tbl_salespersonroutelocation','salespersonroutelocation');
define('tbl_feedbackquestion','feedbackquestion');
define('tbl_orderfeedback','orderfeedback');
define('tbl_cashbackoffer','cashbackoffer');
define('tbl_cashbackoffermembermapping','cashbackoffermembermapping');
define('tbl_cashbackreport','cashbackreport');
define('tbl_companycontactdetails','companycontactdetails');
define('tbl_vehiclecompany','vehiclecompany');
define('tbl_servicetype','servicetype');
define('tbl_challantype','challantype');
define('tbl_insuranceclaim','vehicleinsuranceclaim');
define('tbl_insuranceclaimdocument','insuranceclaimdocument');
define('tbl_party','party');
define('tbl_partytype','partytype');
define("tbl_cashbackofferproductmapping","cashbackofferproductmapping");
define("tbl_productquantityprices","productquantityprices");
define("tbl_memberproductquantityprice","memberproductquantityprice");
define("tbl_productbasicquantityprice","productbasicquantityprice");
define("tbl_stockgeneralvoucher","stockgeneralvoucher");
define("tbl_stockgeneralvoucherproducts","stockgeneralvoucherproducts");
define("tbl_transactionproductstockmapping","transactionproductstockmapping");
define("tbl_documenttype","documenttype");
define("tbl_document","document");
define("tbl_site","site");
define("tbl_sitemapping","sitemapping");
define("tbl_insuranceagent","insuranceagent");
define("tbl_vehiclefasttag","vehiclefasttag");
define("tbl_vehicleinstallment","vehicleinstallment");
define("tbl_assignvehicle","assignvehicle");
define("tbl_fuel","fuel");
define('tbl_fueldocument','fueldocument');
define('tbl_challan','challan');
define('tbl_service','service');
define('tbl_servicepartdetails','servicepartdetails');
define('tbl_servicedocument','servicedocument');
define('tbl_thirdlevelsubmenu','thirdlevelsubmenu');
define('tbl_productionplanqtydetail','productionplanqtydetail');
define('tbl_orderproductsqtydetail','orderproductsqtydetail');
define('tbl_channelthirdlevelsubmenu','channelthirdlevelsubmenu');
define('tbl_additionalrights','additionalrights');
define('tbl_goodsreceivednotes','goodsreceivednotes');
define("tbl_transactionproductscrapmapping","transactionproductscrapmapping");
define("tbl_narration","narration");
define("tbl_estimate","estimate");
define("tbl_inwordqc","inwordqc");
define("tbl_inwordqcmapping","inwordqcmapping");
define("tbl_testingrd","testingrd");
define("tbl_testingrdmapping","testingrdmapping");
define("tbl_orderinvoiceextrachargesmapping","orderinvoiceextrachargesmapping");
define("tbl_leavepaidunpaidhistory","leavepaidunpaidhistory");
define("tbl_employeeleave","employeeleave");
//add ext
define("tbl_currencyrate","currencyrate");
define("tbl_paymenttype","paymenttype");
define("tbl_commission","commission");
define("tbl_transporttype","transporttype");
define("tbl_expensetype","expensetype");
define("tbl_branch","branch");
define("tbl_company","company");
define("tbl_partycontact","partycontact");
define("tbl_partydoc","partydoc");

/* EMAIL CONFIGURATION CONSTANTS */
define("EMAIL_CONFIG", serialize(array(
	'protocol' => 'mail',
	'charset' => 'utf-8',
	'wordwrap' => TRUE,
	'mailtype' => 'html',
)
)
);


/* FRONTEND CONSTANTS */
define('APIKEY', 'CjZOpa8ZMhwtSB5N');

define('API_KEY', 'AIzaSyBIaQMh0HTUT2Vi5RuQv02UcgBUwGl9MqQ');

define("API_KEY_MISSING", "API Key is missing");
define("API_KEY_NOT_MATCH", "Authentication Failed");
define("EMPTY_PARAMETER", "Fields are missing");
define("EMPTY_DATA", "No Data Available");
define('EMPTY_SEARCH', 'No Result Found');
define('USER_NOT_FOUND', 'User Not Available');
define('DROPDOWN_PRODUCT_LIST', '0');

define('EARN_BY_PURCHASE_ORDER', 'Earn by Purchase Order');
define('EARN_BY_SALES_ORDER', 'Earn by Sales Order');
define('REFFER_AND_EARN', 'Reffer & Earn');
define('SCAN_AND_EARN', 'Scan Product QR Code & Earn');
define('REDEEM_POINTS_ON_PURCHASE_ORDER', 'Redeem points on Purchase Order');
define('REDEEM_POINTS_ON_TARGET_OFFER', 'Redeem points on Target Offer');
define('EARN_BY_SAME_CHANNEL_REFERRER', 'Same Channel Referrer Member Point');
define('GENERATE_QRCODE_SRC', 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl={encodeurlstring}&choe=UTF-8');

//Payumoney URL
define('PAYU_URL', 'https://sandboxsecure.payu.in');
//define('PAYU_URL', 'https://secure.payu.in');
define('PAYU_API_URL', 'https://test.payumoney.com/payment/merchant/refundPayment?');
//define('PAYU_API_URL', 'https://www.payumoney.com/payment/merchant/refundPayment?');

//Payubiz 
define('PAYUBIZ_URL', 'https://test.payu.in');
//define('PAYUBIZ_URL', 'https://secure.payu.in');
//for test
define('PAYUBIZ_API_URL', 'https://test.payu.in/merchant/postservice.php?form=2');
//for production
//define('PAYUBIZ_API_URL', 'https://info.payu.in/merchant/postservice.php?form=2');

//Fedex API URL	
define('PRODUCTION_URL', 'https://wsbeta.fedex.com:443/web-services/');

define('MAP_SITEKEY', '6LcGlGQUAAAAAFV8c6ORb0TvtdswbLe3z2PpzakD');
define('MAP_KEY', 'AIzaSyCQplBzEyHAjOBIXHWB1RI_Pls4qLAvxXA');
define('DIRECTION_MAP_KEY', 'AIzaSyDdV1i6t67YOIsD-uy05S9Fo79KSFIWpfs');

define('RAZOR_MERCHANT_ID', 'FGlEHewgP4BnLi');
define('RAZOR_KEY_ID', 'rzp_test_WxleV8bNBmtWYG');
define('RAZOR_KEY_SECRET', 'v7a1wPSPvyAiaNXR3rMYxshN');

/*GOOGLE API*/
define('CLIENT_ID', '1023407314483-5lr8nkd46k3ldv3lmpa1fajspre88qar.apps.googleusercontent.com');
define('CLIENT_SECRET', '9pSPADOmbyr6ugzOR959lJIu');
define('REDIRECT_URL', FRONT_URL.'google-login');
define('SIMPLE_API_KEY', '');
define('MEMBER_CLIENT_ID', '1023407314483-369tvjqce3a95gggn6genrtkj6t8376u.apps.googleusercontent.com');
define('MEMBER_CLIENT_SECRET', 'VoVVfQzkqJyZVFdWBxJoATSj');
define('MEMBER_REDIRECT_URL', FRONT_URL.MEMBERFRONTFOLDER.'google-login');

/*FACEBOOK API*/
define('APP_ID', '2328443513928026');
define('APP_SECRET', 'a3d069b6addace80abc53b14a9349daa');
define('GRAPH_VERSION', 'v2.10');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
