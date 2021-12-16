<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public $Emailformattype;
    public $Smsformattype;
    public $contenttype;
    public $footercontenttype;
    public $automailtype;
    public $Pointtransactiontype;
    public $Currencycode;
    public $Bankmethod;
    public $AdPage;
    public $AdPageSection;
    public $Invoicestatus;
    public $Invoicestatuscolorcode;
    public $Membertype;
    public $Yearwise;
    public $Monthwise;
    public $Targettype;
    public $Targetduration;
    public $Defaultdesignation;
    public $Routestatus;
    public $Licencetype;
    public $Fueltype;
    public $Additionalrights;
    public $GRNstatuscolorcode;
    public $GRNstatus;

    function __construct()
    {
        parent::__construct();
        $this->readdb = $this->load->database('readdb',TRUE);  // for read
        $this->getSettingDetail();
        
        $portal = $_SERVER["SERVER_NAME"];
        include APPPATH . 'config/client.php';
        $clientfolder = '';
        if (!empty($portaldetail[$portal])) {
            $clientfolder = $portaldetail[$portal]['folder'];
        }

        $FILE_URL = DOMAIN_URL."uploaded/".$clientfolder."/";
        $localfolderlocation = $location = SITE_PATH."uploaded/".$clientfolder."/";

        if(ALLOWS3==1){
            $this->writedb = $this->load->database('writedb',TRUE);   // for write
            $FILE_URL = AWSLINK.BUCKETNAME.'/';
            $location = '';
        }else{
            $this->writedb = $this->load->database('readdb',TRUE);   // for write
        }
        
        define("CLIENT_FOLDER", $clientfolder);
        define("MAIN_LOGO_IMAGE_URL", $FILE_URL."company/");
        define("SETTINGS_PATH", $location."company/");

        define("MEMBER_WEBSITE_LOGO_URL", $FILE_URL."memberwebsite/");
        define("MEMBER_WEBSITE_SETTINGS_PATH", $location."memberwebsite/");

        define("IMPORTLOCATION_PATH", $location."importlocation/");
        define("PREVIEW_IMAGE", $FILE_URL."images/");

        define("PROFILE", $FILE_URL."profileimage/");
        define("PROFILE_PATH", $location."profileimage/");
        define("PROFILE_LOCAL_PATH", $localfolderlocation."profileimage/");

        define("BANNER", $FILE_URL."banner/");
        define("BANNER_PATH", $location."banner/");
        define("BANNER_LOCAL_PATH", $localfolderlocation."banner/");
        define("BANNER_IMAGE", $FILE_URL."banner/");

        define("BLOG", $FILE_URL."blog/");
        define("BLOG_PATH", $location."blog/");
        define("BLOG_LOCAL_PATH", $localfolderlocation."blog/");
        define("BLOG_IMAGE", $FILE_URL."blog/");

        define("PRODUCT", $FILE_URL."product/");
        define("PRODUCT_PATH", $location."product/");
        define("PRODUCT_LOCAL_PATH", $localfolderlocation."product/");
        define("PRODUCT_IMAGE", $FILE_URL."product/");
        
        define("ADVERTISEMENT", $FILE_URL."advertisement/");
        define("ADVERTISEMENT_PATH", $location."advertisement/");
        define("ADVERTISEMENT_LOCAL_PATH", $localfolderlocation."advertisement/");
        define("ADVERTISEMENT_IMAGE", $FILE_URL."advertisement/");
        
        define("NEWS", $FILE_URL."news/");
        define("NEWS_PATH", $location."news/");
        define("NEWS_LOCAL_PATH", $localfolderlocation."news/");
        define("NEWS_IMAGE", $FILE_URL."news/");
        
        define("FRONTMENU_COVER_IMAGE", $FILE_URL."frontmenucoverimage/");
        define("FRONTMENU_COVER_IMAGE_PATH", $location."frontmenucoverimage/");
        define("FRONTMENU_COVER_IMAGE_LOCAL_PATH", $localfolderlocation."frontmenucoverimage/");

        define("OURCLIENT_COVER_IMAGE", $FILE_URL."ourclientcoverimage/");
        define("OURCLIENT_COVER_IMAGE_PATH", $location."ourclientcoverimage/");
        define("OURCLIENT_COVER_IMAGE_LOCAL_PATH", $localfolderlocation."ourclientcoverimage/");
        
        define("INVOICE", $FILE_URL."invoice/");
        define("INVOICE_PATH", $location."invoice/");

        define("CREDITNOTE", $FILE_URL."creditnote/");
        define("CREDITNOTE_PATH", $location."creditnote/");

        /*define("FEDEXLABEL", $FILE_URL."fedexlabel/");	
        define("FEDEXLABEL_PATH", $location."fedexlabel/");*/
        
        define("IDPROOF", $FILE_URL."idproof/");
        define("IDPROOF_PATH", $location."idproof/");

        define("DONATION_RECEIPT", $FILE_URL."donationreceipt/");
        define("DONATION_RECEIPT_PATH", $location."donationreceipt/");

        define("ORDER", $FILE_URL."order/");
        define("ORDER_PATH", $location."order/");

        define("ORDER_INSTALLMENT", $FILE_URL."orderinstallment/");
        define("ORDER_INSTALLMENT_PATH", $location."orderinstallment/");

        define("QUOTATION", $FILE_URL."quotation/");
        define("QUOTATION_PATH", $location."quotation/");
        define("QUOTATION_LOCAL_PATH", $localfolderlocation."quotation/");
        
        define("TESTIMONIALS", $FILE_URL."testimonials/");
        define("TESTIMONIALS_IMAGE", $FILE_URL."testimonials/");
        define("TESTIMONIALS_PATH", $location."testimonials/");
        define("TESTIMONIALS_LOCAL_PATH", $localfolderlocation."testimonials/");

        define("PHOTOGALLERY", $FILE_URL."photogallery/");
        define("PHOTOGALLERY_PATH", $location."photogallery/");
        define("PHOTOGALLERY_LOCAL_PATH", $localfolderlocation."photogallery/");

        define("CATEGORY", $FILE_URL."category/");
        define("CATEGORY_IMAGE", $FILE_URL."category/");
        define("CATEGORY_PATH", $location."category/");
        define("CATEGORY_LOCAL_PATH", $localfolderlocation."category/");

        define("CATALOG_IMAGE", $FILE_URL."catalog/");
        define("CATALOG_PATH", $location."catalog/");
        define("CATALOG_LOCAL_PATH", $localfolderlocation."catalog/");

        define("CUSTOMER_PATH", $location."customer/");
        define("CUSTOMER_IMAGE", $FILE_URL."customer/");

        define("HOMEBANNER", $FILE_URL."homebanner/");
        define("HOMEBANNER_PATH", $location."homebanner/");
        define("HOMEBANNER_LOCAL_PATH", $localfolderlocation."homebanner/");

        define("IMPORT_FILE", $FILE_URL."import/");
        define("IMPORT_PATH", $location."import/");
        
        define("OFFER", $FILE_URL."offer/");
        define("OFFER_PATH", $location."offer/");
        define("OFFER_LOCAL_PATH", $localfolderlocation."offer/");

        define("BRAND", $FILE_URL."brand/");
        define("BRAND_PATH", $location."brand/");
        define("BRAND_LOCAL_PATH", $localfolderlocation."brand/");

        define("TRANSACTION_ATTACHMENT", $FILE_URL."transactionattachment/");
        define("TRANSACTION_ATTACHMENT_PATH", $location."transactionattachment/");
        define("TRANSACTION_ATTACHMENT_LOCAL_PATH", $localfolderlocation."transactionattachment/");

        define("PRODUCT_PROCESS_CERTIFICATE", $FILE_URL."productprocesscertificate/");
        define("PRODUCT_PROCESS_CERTIFICATE_PATH", $location."productprocesscertificate/");
        define("PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH", $localfolderlocation."productprocesscertificate/");

        define("PAYMENT_METHOD_LOGO", $FILE_URL."paymentmethodlogo/");
        define("PAYMENT_METHOD_LOGO_PATH", $location."paymentmethodlogo/");
        define("PAYMENT_METHOD_LOGO_LOCAL_PATH", $localfolderlocation."paymentmethodlogo/");
       
        define("EXPENSE_RECEIPT", $FILE_URL."expensereceipt/");
        define("EXPENSE_RECEIPT_PATH", $location."expensereceipt/");
        define("EXPENSE_RECEIPT_LOCAL_PATH", $localfolderlocation."expensereceipt/");

        define("INSURANCE", $FILE_URL."insurance/");
        define("INSURANCE_PATH", $location."insurance/");
        define("INSURANCE_LOCAL_PATH", $localfolderlocation."insurance/");

        define("INSURANCE_CLAIM", $FILE_URL."vehicleinsuranceclaim/");
        define("INSURANCE_CLAIM_PATH", $location."vehicleinsuranceclaim/");
        define("INSURANCE_CLAIM_LOCAL_PATH", $localfolderlocation."vehicleinsuranceclaim/");

        define("VEHICLEPOLLUTIONCERTIFICATE", $FILE_URL."vehiclepollutioncertificate/");
        define("VEHICLEPOLLUTIONCERTIFICATE_PATH", $location."vehiclepollutioncertificate/");
        define("VEHICLEPOLLUTIONCERTIFICATE_LOCAL_PATH", $localfolderlocation."vehiclepollutioncertificate/");

        define("VEHICLEREGISTRATIONCERTIFICATE", $FILE_URL."vehicleregistrationcertificate/");
        define("VEHICLEREGISTRATIONCERTIFICATE_PATH", $location."vehicleregistrationcertificate/");
        define("VEHICLEREGISTRATIONCERTIFICATE_LOCAL_PATH", $localfolderlocation."vehicleregistrationcertificate/");

        define("VEHICLETAX", $FILE_URL."vehicletax/");
        define("VEHICLETAX_PATH", $location."vehicletax/");
        define("VEHICLETAX_LOCAL_PATH", $localfolderlocation."vehicletax/");
        
        define("UPLOADED_IMPORT_EXCEL_FILE", $FILE_URL."uploadedimportexcelfile/");
        define("UPLOADED_IMPORT_EXCEL_FILE_PATH", $location."uploadedimportexcelfile/");
        define("UPLOADED_IMPORT_EXCEL_FILE_LOCAL_PATH", $localfolderlocation."uploadedimportexcelfile/");
        
        define("FEDEX_LABEL", $FILE_URL."fedexlabel/");
        define("FEDEX_LABEL_PATH", $location."fedexlabel/");
        define("FEDEX_LABEL_LOCAL_PATH", $localfolderlocation."fedexlabel/");

        define("DOCUMENT", $FILE_URL."documents/");
        define("DOCUMENT_PATH", $location."documents/");
        define("DOCUMENT_LOCAL_PATH", $localfolderlocation."documents/");

        define("FUEL", $FILE_URL."fuel/");
        define("FUEL_PATH", $location."fuel/");
        define("FUEL_LOCAL_PATH", $localfolderlocation."fuel/");

        define("CHALLAN", $FILE_URL."challan/");
        define("CHALLAN_PATH", $location."challan/");
        define("CHALLAN_LOCAL_PATH", $localfolderlocation."challan/");

        define("SERVICE", $FILE_URL."service/");
        define("SERVICE_PATH", $location."service/");
        define("SERVICE_LOCAL_PATH", $localfolderlocation."service/");

        define("PROCESS_CHALLAN", $FILE_URL."processchallan/");
        define("PROCESS_CHALLAN_PATH", $location."processchallan/");
        define("PROCESS_CHALLAN_LOCAL_PATH", $localfolderlocation."processchallan/");
        
        define("ESTIMATE", $FILE_URL."estimate/");
        define("ESTIMATE_PATH", $location."estimate/");
        define("ESTIMATE_LOCAL_PATH", $localfolderlocation."estimate/");

        define("ASSIGNED_ROUTE", $FILE_URL."assignroute/");
        define("ASSIGNED_ROUTE_PATH", $location."assignroute/");
        define("ASSIGNED_ROUTE_LOCAL_PATH", $localfolderlocation."assignroute/");
        
        define("INWORD_IMAGE", $FILE_URL."inword/");
        define("INWORD_PATH", $location."inword/");
        define("INWORD_LOCAL_PATH", $localfolderlocation."inword/");

        define("TESTING_IMAGE", $FILE_URL."testing/");
        define("TESTING_PATH", $location."testing/");
        define("TESTING_LOCAL_PATH", $localfolderlocation."testing/");

        if( 
            (string)$this->uri->segment(1)."/" !== (string)ADMINFOLDER && 
            (string)$this->uri->segment(1) !== (string)"api" && 
            WEBSITE == 0 && 
            (string)$this->uri->segment(1)."/" !== (string)CHANNELFOLDER &&
            (string)$this->uri->segment(1)."/" !== (string)MEMBERFRONTFOLDER &&
            (string)$this->uri->segment(1) !== (string)"privacy-policy" &&
            (string)$this->uri->segment(1) !== (string)"assets" &&
            (string)$this->uri->segment(1) !== (string)"uploaded"
        ){
            redirect(CHANNELFOLDER."login");
        }
        $this->Emailformattype = array(
            1 => "Forgot Password",
            2 => "Reset Password",
            3 => "Verify Email",
            4 => "Order For Seller",
            5 => "Order For Buyer",
            6 => "Invoice",
            7 => "Inquiry Assign",
            8 => "Follow UP Assign",
            9 => "Inquiry Status Change",
            10 => "Follow Up Status Change",
            11 => "Quotation For Buyer",
            12 => "Credit Note For Buyer",
            13 => "Invoice For Buyer",
            14 => "Expire Licence",
            15 => "OTP Verification",
            16 => "Request For Leave",
        );
        $this->Smsformattype = array(
            1 => "OTP",
            2 => "Order SMS For Seller"
        );
        $this->contenttype = array(  
            1=>  "Certificate",
            2=>  "Infrastructure",
            3 => "Term & Condition",
            4 => "About Us",
            5 => "Privacy Policy",
            6 => "Return & Refund Policy",
            7 => "Shipping Policy"
        );
        $this->footercontenttype = array(
            6 => "Cancellation &amp; Returns",
            7 => "Shipping Policy",
            8 => "Privacy Policy",
            9 => "Term & Condition"
        );
        $this->automailtype = array(
            1 => "Cart Product Mail",
            2 => "Combination Product Mail",
        );
      
        $this->Pointtransactiontype = array(
            0 => "Admin",
            1 => "Purchase Order",
            2 => "Sales Order",
            3 => "Redeem points",
            4 => "Reffer & Earn",
            5 => "Same Channel Referrer",
            6 => "Scan QR & Earn",
        );
        $this->Currencycode = array(
            "₹" => "₹",
            "$" => "$",
            "¢" => "¢",
            "£" => "£",
            "¥" => "¥",
            "₣" => "₣",
            "₤" => "₤",
            "€" => "€",
        );
        $this->Bankmethod = array(
            1 => "Cash",
            2 => "Cheque",
            3 => "RTGS",
            4 => "NEFT",
            5 => "IMPS",
            6 => "CreditCard",
            7 => "DebitCard"
        );
        $this->AdPage = array(
            1 => 'Home',
            2 => 'My Profile',
            3 => 'Membership Plans',
            4 => 'Search', 
            5 => 'Member List',
            6 => "Blog",
            7 => "Blog Detail"
        );
        $this->AdPageSection = array(
            1 => array(
                1 => 'Above Premium Profiles (970x90)',
                2 => 'Above Featured Profiles (970x90)',
                3 => 'Below Happy Stories Profiles (970x90)'
            ), 
            2 => array(
                1 => 'Above Profile Detail (970x90)',
                2 => 'Below Profile Detail (728x90)'
            ), 
            3 => array(
                1 => 'Above Membership Plans (970x90)',
                2 => 'Below Membership Plans (970x90)',
                3 => 'Above FREE Features (970x90)'
            ),
            4 => array(
                1 => 'Above Smart search button (728x90)',
                2 => 'Above Advanced search button (728x90)',
                3 => 'Below Recent Profile Views Title (300x250)'
            ),
            5 => array(
                1 => 'Side Bar (300x250)'
            ),
            6 => array(
                1 => 'Side Bar (336x280)',
                2 => 'Below Blog List (728x90)'
            ),
            7 => array(
                1 => 'Side Bar (336x280)',
                2 => 'Below Blog Heading (728x90)'
            )
        );
        $this->Invoicestatus = array(
            0 => "Book",
            1 => "Delivered",
            2 => "Cancel",
            3 => "Pickup",
            4 => "Shipping",
            5 => "Return"
        );
        $this->Invoicestatuscolorcode = array(
            0 => "#9e9e9e",
            1 => "#8bc34a",
            2 => "#e51c23",
            3 => "#9e9e9e",
            4 => "#00bcd4",
            5 => "#ffc107"
        );
        $this->Targettype = array(
            1 => "Employee",
            2 => "Zone",
            3 => "Product",
        );
        $this->Targetduration = array(
            1 => "Yearly",
            2 => "Monthly",
            3 => "Quaterly",
        );
        $this->Membertype = array(
            1 => "Individual",
            2 => "Agent",
            3 => "Reseller",
        );
        $this->Yearwise = array(           
            2020 => "2020",
            2019 => "2019",
            2018 => "2018",
            2017 => "2017",
            2016 => "2016"
        );
        $this->Monthwise = array(
            1 => "January",
            2 => "February",
            3 => "March",
            4 => "April",
            5 => "May",
            6 => "June",
            7 => "July",
            8 => "August",
            9 => "September",
            10 => "October",
            11 => "November",
            12 => "December"            
        );
        $this->Commissiontype = array(
            1 => "Flat Commission",
            2 => "Product Base Commission",
            3 => "Member Base Commission",
            4 => "Tiered Commission"
        );
        $this->Userposition = array(
            1 => "Owner",
            2 => "General Manager",
            3 => "Finance",
            4 => "Store Manager",
            5 => "Production Manager",
            6 => "Sales",
            7 => "HR",
            8 => "Purchase",
            9 => "Despatch",            
        );
        $this->Vehicletype = array(
            1 => "Bus",
            2 => "Mini Bus",
            3 => "Truck",
            4 => "Rickshaw",
            5 => "Bike"
        );
        $this->Ownership = array(
            1 => "Company",
            2 => "Rent",
            3 => "Self"
        );
        $this->Defaultdesignation = array(
            1 => "Account Department",
            2 => "Store Management",
            3 => "IT Department"
        );
        $this->Routestatus = array(
            0 => "Pending",
            1 => "In Process",
            2 => "Ready to Pickup",
            3 => "Dispatch",
            4 => "Completed",
            5 => "Route Close"
        );

        $this->Socialmediatype = array(
            1 => "Facebook",
            2 => "Instagram",
            3 => "Google",
            4 => "Twitter",
            5 => "LinkedIn",
            6 => "YouTube",
            7 => "Pinterest",
        );
        $this->Licencetype = array(
            1 => "Two Wheel",
            2 => "Four Wheel",
            3 => "Heavy Vehicles"
        );
        $this->Fueltype = array(
            1 => "Petrol",
            2 => "Diesel",
            3 => "Bio-Diesel",
            4 => "Oil"
        );
        $this->Additionalrights = array(
            1 => "export-to-excel",
            2 => "export-to-pdf",
            3 => "print",
            4 => "total-member",
            5 => "total-product",
            6 => "total-order-completed",
            7 => "total-order-cancelled",
            8 => "total-quotation",
            9 => "total-sales",
            10 => "total-sales-chart",
            11 => "total-sales-chart-box",
            12 => "total-orders-chart",
            13 => "total-orders-chart-box",
            14 => "recent-orders",
            15 => "recent-quotations",
            16 => "feedback-of-the-day",
            17 => "import-to-excel",
            18 => "upload-image",
            19 => "total-vehicle",
            20 => "total-owner",
            21 => "total-drivers",
            22 => "total-garage",
            23 => "total-site",
            24 => "total-alert-service-parts",
            25 => "recent-upcoming-expire-vehicle-registration",
            26 => "recent-upcoming-expire-insurance",
            27 => "recent-upcoming-expire-document",
            28 => "recent-upcoming-expire-service-parts",
            29 => "recent-service-part-alerts",
            30 => "recent-vehicle-emi-reminder",
            31 => "vehicle-dropdown",
            32 => "total-inquiry",
            33 => "total-followup",
            34 => "member-download",
            35 => "member-inquiry",
            36 => "not-delivered-service",
            37 => "open-service",
            38 => "today-follow-up", 
            39 => "to-do-list"
        );
        $this->GRNstatuscolorcode = array(
            0 => "#9e9e9e",
            2 => "#e51c23",
        );
        $this->GRNstatus = array(
            0 => "Pending",
            1 => "Complete",
            2 => "Cancel",
        );
    }
    function getSettingDetail()
    {
        $this->readdb->select('*,
						IFNULL((SELECT countryid FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=cityid)),0) as countryid,
						IFNULL((SELECT stateid FROM '.tbl_city.' WHERE id=cityid),0) as provinceid,
						
						IFNULL((SELECT name FROM '.tbl_country.' WHERE id IN (SELECT countryid FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=cityid))),0) as countryname,
						IFNULL((SELECT name FROM '.tbl_province.' WHERE id IN (SELECT stateid FROM '.tbl_city.' WHERE id=cityid)),0) as provincename,
                        IFNULL((SELECT name FROM '.tbl_city.' WHERE id=cityid),0) as cityname,
                        
                        IFNULL((SELECT GROUP_CONCAT(email) FROM '.tbl_companycontactdetails.' WHERE type=1 LIMIT 1),"") as email,
                        IFNULL((SELECT GROUP_CONCAT(mobileno) FROM '.tbl_companycontactdetails.' WHERE type=0 LIMIT 1),"") as mobileno
						');
        $query = $this->readdb->get_where(tbl_settings);
        if ($query->num_rows() > 0) {
            $arr = $query->row_array();
            
            define("COMPANY_NAME", $arr['businessname']);
            define("COMPANY_ADDRESS", $arr['address']);
            define("COMPANY_CITYID", $arr['cityid']);
            define("COMPANY_PROVINCEID", $arr['provinceid']);
            define("COMPANY_COUNTRYID", $arr['countryid']);
            define("COMPANY_WEBSITE", $arr['website']);
            define("COMPANY_EMAIL", $arr['email']);
            define("COMPANY_MOBILENO", $arr['mobileno']);
            define("COMPANY_LOGO", $arr['logo']);
            define("COMPANY_FACEBOOKLINK", $arr['facebooklink']);
            define("COMPANY_GOOGLELINK", $arr['googlelink']);
            define("COMPANY_TWITTERLINK", $arr['twitterlink']);
            define("COMPANY_INSTAGRAMLINK", $arr['instagramlink']);
            define("COMPANY_FAVICON", $arr['favicon']);
            define("COMPANY_SMALL_LOGO", $arr['company_small_logo']);
            define("CURRENCY_CODE",'₹');
            define("PRODUCT_UNIT_IS_OPTIONAL",'0');
            define("HSNCODE_IS_COMPULSARY",'0');
            define("PRODUCTDEFAULTIMAGE",$arr['productdefaultimage']);
            define("CATEGORYDEFAULTIMAGE",$arr['defaultimagecategory']);
            define("EDITTAXRATE", '1');
            define("ADMIN_ORDER_EMAIL", $arr['orderemails']);
            define("THEME_COLOR",$arr['themecolor']);
            define("FONT_COLOR",$arr['fontcolor']);
            define("LINK_COLOR",$arr['linkcolor']);
            define("TABLE_HEADER_COLOR",$arr['tableheadercolor']);
            define("FOOTER_BG_COLOR",$arr['footerbgcolor']);
            define("SIDEBAR_BG_COLOR",$arr['sidebarbgcolor']);
            define("SIDEBAR_MENU_ACTIVE_COLOR",$arr['sidebarmenuactivecolor']);
            define("SIDEBAR_SUBMENU_BG_COLOR",$arr['sidebarsubmenubgcolor']);
            define("SIDEBAR_SUBMENU_ACTIVE_COLOR",$arr['sidebarsubmenuactivecolor']);
            
        } else
            return '';

        $query = $this->readdb->get_where(tbl_systemconfiguration);
        $arr = $query->row_array();
        
        $website = (isset($arr['website']))?$arr['website']:0;
        $sms = (isset($arr['sms']))?$arr['sms']:0;

        $price = (isset($arr['price']))?$arr['price']:0;
        $listing = (isset($arr['listing']))?$arr['listing']:0;
        $branding = (isset($arr['branding']))?$arr['branding']:'';
        $brandingallow = (isset($arr['brandingallow']))?$arr['brandingallow']:'';
        $footer = (isset($arr['footer']))?$arr['footer']:'';
        $copyright = (isset($arr['copyright']))?$arr['copyright']:'';
        $brandingtype = (isset($arr['brandingtype']))?$arr['brandingtype']:'';
        $brandinglogo = (isset($arr['brandinglogo']))?$arr['brandinglogo']:'';
        $brandingurl = (isset($arr['brandingurl']))?$arr['brandingurl']:'';
        $storagespace = (isset($arr['storagespace']))?$arr['storagespace']:''; 
        $startdate = (isset($arr['startdate']))?$arr['startdate']:''; 
        $expirydate = (isset($arr['expirydate']))?$arr['expirydate']:'';
        $maintenancestartdatetime = (isset($arr['maintenancestartdatetime']))?$arr['maintenancestartdatetime']:'';
        $maintenanceexpirydatetime = (isset($arr['maintenanceexpirydatetime']))?$arr['maintenanceexpirydatetime']:'';  
        $maintenance_mode =(date("d/m/Y h:i A") >= date('d/m/Y h:i A',strtotime($maintenancestartdatetime)) && date("d/m/Y h:i A") <= date('d/m/Y h:i A',strtotime($maintenanceexpirydatetime)) ) ? TRUE:FALSE ;
        $startprocesswithoutstock = (isset($arr['startprocesswithoutstock']))?$arr['startprocesswithoutstock']:0;
        $stockmanageby = (isset($arr['stockmanageby']))?$arr['stockmanageby']:0;
        $managedecimalqty = (isset($arr['managedecimalqty']))?$arr['managedecimalqty']:0;
        $hidesellercolumninorder = (isset($arr['hidesellercolumninorder']))?$arr['hidesellercolumninorder']:0;
        $hideemi = (isset($arr['hideemi']))?$arr['hideemi']:0;
        $hidepurchaseextracharges = (isset($arr['hidepurchaseextracharges']))?$arr['hidepurchaseextracharges']:0;

        $memberlatlong = (isset($arr['memberlatlong']))?$arr['memberlatlong']:0;
        $crm = (isset($arr['crm']))?$arr['crm']:0;
        $inquiryfinalstatus = (isset($arr['inquiryfinalstatus']))?$arr['inquiryfinalstatus']:'';
        $inquiryconfirmstatus = (isset($arr['inquiryconfirmstatus']))?$arr['inquiryconfirmstatus']:'';
        $followupfinalstatus = (isset($arr['followupfinalstatus']))?$arr['followupfinalstatus']:'';
        $inquirydefaultstatus = (isset($arr['inquirydefaultstatus']))?$arr['inquirydefaultstatus']:'';
        $followupdefaultstatus = (isset($arr['followupdefaultstatus']))?$arr['followupdefaultstatus']:'';
        $memberdefaultstatus = (isset($arr['memberdefaultstatus']))?$arr['memberdefaultstatus']:'';
        $defaultfollowuptype = (isset($arr['defaultfollowuptype']))?$arr['defaultfollowuptype']:'';
        $inquiryassign = (isset($arr['inquiryassign']))?$arr['inquiryassign']:'';
        $installmentreminder = (isset($arr['installmentreminder']))?$arr['installmentreminder']:'';
        $followupdatetype = (isset($arr['followupdatetype']))?$arr['followupdatetype']:'';
        $defaultfollowupdate = (isset($arr['defaultfollowupdate']))?$arr['defaultfollowupdate']:'';
        $inquirywithproduct = (isset($arr['inquirywithproduct']))?$arr['inquirywithproduct']:'';

        $allows3 = (isset($arr['allows3']))?$arr['allows3']:0;
        $iamkey = (isset($arr['iamkey']))?$arr['iamkey']:'';
        $iamsecret = (isset($arr['iamsecret']))?$arr['iamsecret']:'';
        $region = (isset($arr['region']))?$arr['region']:'';
        $awslink = (isset($arr['awslink']))?$arr['awslink']:'';
        $commonbucket = (isset($arr['commonbucket']))?$arr['commonbucket']:'';
        $clientname = (isset($arr['clientname']))?$arr['clientname']:'';
        $bucketname = (isset($arr['bucketname']))?$arr['bucketname']:'';
        $leavemail = (isset($arr['leavemail']))?$arr['leavemail']:'';

        define("WEBSITE",$website);
        define("SMS_SYSTEM", $sms);
        
        define("PRICE", $price);
        define("LISTING", $listing);
        define("BRANDING", $branding);
        define("BRANDING_ALLOW", $brandingallow);
        define("FOOTER", $footer);
        define("COPYRIGHT", $copyright);
        define("BRANDING_TYPE", $brandingtype);
        define("BRANDING_LOGO", $brandinglogo);
        define("BRANDING_URL", $brandingurl);
        define("STORAGESPACE", $storagespace);
        define("STARTDATE",$startdate);
        define("EXPIRYDATE",$expirydate);
        define("MAINTENANCE_MODE",$maintenance_mode);
        define('MAINTENANCE_IPS','localhost');
        define("STARTDATETIME",$maintenancestartdatetime);
        define("EXPIRYDATETIME",$maintenanceexpirydatetime);
        define("START_PROCESS_WITHOUT_STOCK",$startprocesswithoutstock);
        define("STOCK_MANAGE_BY",$stockmanageby);
        define("MANAGE_DECIMAL_QTY",$managedecimalqty);
        define("HIDE_SELLER_IN_ORDER",$hidesellercolumninorder);
        define("HIDE_EMI",$hideemi);
        define("HIDE_PURCHASE_EXTRA_CHARGES",$hidepurchaseextracharges);
        define("MEMBER_LAT_LONG",$memberlatlong);
        
        //CRM CONSTANT
        define("CRM", $crm);
        define("MEMBER_DEFAULT_STATUS", $memberdefaultstatus);
        define("INQUIRY_FINAL_STATUS", $inquiryfinalstatus);
        define("INQUIRY_DEFAULT_STATUS", $inquirydefaultstatus);
        define("INQUIRY_CONFIRM_STATUS", $inquiryconfirmstatus);
        define("FOLLOWUP_DEFAULT_STATUS", $followupdefaultstatus);
        define("FOLLOWUP_FINAL_STATUS", $followupfinalstatus);
        define("DEFAULT_FOLLOWUP_TYPE", $defaultfollowuptype);
        define("INQUIRY_ASSIGN", $inquiryassign);
        define("INSTALLMENT_REMAINDER", $installmentreminder);
        define("FOLLOWUP_DATE_TYPE", $followupdatetype);
        define("DEFAULT_FOLLOWUP_DATE", $defaultfollowupdate);
        define("INQUIRY_WITH_PRODUCT", $inquirywithproduct);
        define("LeaveMail" , $leavemail);
        
        define("IAMKEY", $iamkey);
        define("IAMSECRETKEY", $iamsecret);
        define("REGION", $region);
        define("AWSLINK", $awslink);
        define("ALLOWS3", $allows3);
        define("COMMONBUCKETLINK", $commonbucket);
        define("CLIENTNAME", '/');
        /* if($clientname == "/"){
        }else{
            define("CLIENTNAME", $clientname.'/assets/uploaded');
        } */
        define("BUCKETNAME", $bucketname);
    }
}


