<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Companysetting extends MY_Controller {
    public $PostData = array();
    function __construct() {
        parent::__construct();
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
        	$this->PostData = $this->input->post();
        	if(isset($this->PostData['apikey'])){
        		$apikey = $this->PostData['apikey'];
        		if($apikey == '' || $apikey != APIKEY){
        			ws_response('fail', API_KEY_NOT_MATCH);
        			exit;
        		}
        	} else {
        		ws_response('fail', API_KEY_MISSING);
        		exit;
        	}
        } else {
        	ws_response('fail', 'Authentication failed');
        	exit;
        }
    }

    public $json = array();

    function getsetting() {
        $PostData = json_decode($this->PostData['data'],true);      
        $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
       
        if (empty($channelid)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            
            $this->load->model('Channel_model','Channel');
            if(!empty($memberid) && $memberid!=-1 && $channelid!=-1){
                $this->load->model('Member_model', 'Member');  
                $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
                $count = $this->Member->CountRecords();

                if($count==0){
                    ws_response('fail', USER_NOT_FOUND);
                    exit;
                }

                $this->Channel->_fields = "discountpriority";
                $this->Channel->_where = array("id"=>$channelid);
                $checkmember = $this->Channel->getRecordsByID();
            }

            //if($channelid == GUESTCHANNELID){
            if(($memberid==-1 && $channelid==-1)){ // || $channelid == GUESTCHANNELID
                $istype = 1;
            }else{
                $istype = 0;
            }
            $this->load->model('Settings_model','Settings');
            $systemsetting = $this->Settings->getsetting();
        
            $companydata = $channeldata = array();
            $this->readdb->select('dealer,payment,discountonbilltype,gstondiscount,discountonbillvalue,discountonbillstartdate,discountonbillenddate,discountonbillminamount,discountonbill,discountcoupon,vendordiscount,discountpriority,rewardspoints,onlinepayment,guestchannelid,IFNULL((SELECT id FROM '.tbl_member.' WHERE channelid=guestchannelid LIMIT 1),0) as guestmemberid,allowmultiplememberwithsamechannel,sms,withclientfolder,customerchannelid,qrcode,hideregisteraschanneliddropdowninapp,orderassign,registeraschannelidinapp,price,applicationlayout,hsncode,gstr1report,gstr2report,pointhistoryreport,targetoffer,cashbackofferqr,cashbackreport,extracharge,reports,paymentreport,productwisestockreport,totalstockreport,minimumstockreport,abcinventoryreport,allowsettingstocklimit,quotationtoorderreport,ordertodeliverreport,cancelorderreport,salesreturnreport,abcpaymentreport,salesanalysisreport,balancereport,overallinouthistory,shownews,feedbackquestion,invoicepayment,invoicepaymentoffer,document,sociallogin,facebooklogin,googlelogin,guestcart,selectordertypeincart,selectaddressincart,hidesummaryscreenforcustomer,showemireminderalert,showstocklimitreminder,allowaddinvoice,allowaddcreditnote,showbrands,allowsettingreminderdays,allowquicklogin,allowautolock,showdeliverypriority,registerfromapp,generateinvoiceaddingsalesorder,allowselectingnearestmember,bankdropdownonsales,cashorbankmodule,orderdeliveryoption,addressrequiredinregister,hideemi,codoptioninorder,advancepaymentoptioninorder,routelistmodule,paymenttitleinapp,registeronlydefaultcountry');
            $this->readdb->from(tbl_systemconfiguration); 
            $systemconfiguration=$this->readdb->get()->row_array();
            if(!is_null($systemconfiguration) && $systemconfiguration['dealer']==1){
                $dealerecord=true;
            }else{
                $dealerecord=false;
            }

            $hsncode = $systemconfiguration['hsncode'];
            $gstr1report = $systemconfiguration['gstr1report'];
            $gstr2report = $systemconfiguration['gstr2report'];
            $pointhistoryreport = $systemconfiguration['pointhistoryreport'];
            $targetoffer = $systemconfiguration['targetoffer'];
            $cashbackofferqr = $systemconfiguration['cashbackofferqr'];
            $cashbackreport = $systemconfiguration['cashbackreport'];
            $extracharge = $systemconfiguration['extracharge'];
            $reports = $systemconfiguration['reports'];
            $paymentreport = $systemconfiguration['paymentreport'];
            $productwisestockreport = $systemconfiguration['productwisestockreport'];
            $totalstockreport = $systemconfiguration['totalstockreport'];
            $minimumstockreport = $systemconfiguration['minimumstockreport'];
            $abcinventoryreport = $systemconfiguration['abcinventoryreport'];
            $allowsettingstocklimit = $systemconfiguration['allowsettingstocklimit'];
            $quotationtoorderreport = $systemconfiguration['quotationtoorderreport'];
            $ordertodeliverreport = $systemconfiguration['ordertodeliverreport'];
            $cancelorderreport = $systemconfiguration['cancelorderreport'];
            $salesreturnreport = $systemconfiguration['salesreturnreport'];
            $abcpaymentreport = $systemconfiguration['abcpaymentreport'];
            $salesanalysisreport = $systemconfiguration['salesanalysisreport'];
            $balancereport = $systemconfiguration['balancereport'];
            $overallinouthistory = $systemconfiguration['overallinouthistory'];
            $shownews = $systemconfiguration['shownews'];
            $feedbackquestion = $systemconfiguration['feedbackquestion'];
            $invoicepayment = $systemconfiguration['invoicepayment'];
            $invoicepaymentoffer = $systemconfiguration['invoicepaymentoffer'];
            $document = $systemconfiguration['document'];
            $sociallogin = $systemconfiguration['sociallogin'];
            $facebooklogin = $systemconfiguration['facebooklogin'];
            $googlelogin = $systemconfiguration['googlelogin'];
            $guestcart = $systemconfiguration['guestcart'];
            $selectordertypeincart = $systemconfiguration['selectordertypeincart'];
            $selectaddressincart = $systemconfiguration['selectaddressincart'];
            $hidesummaryscreenforcustomer = $systemconfiguration['hidesummaryscreenforcustomer'];
            $showemireminderalert = $systemconfiguration['showemireminderalert'];
            $showstocklimitreminder = $systemconfiguration['showstocklimitreminder'];
            $allowaddinvoice = $systemconfiguration['allowaddinvoice'];
            $allowaddcreditnote = $systemconfiguration['allowaddcreditnote'];
            $showbrands = $systemconfiguration['showbrands'];
            $allowsettingreminderdays = $systemconfiguration['allowsettingreminderdays'];
            $allowquicklogin = $systemconfiguration['allowquicklogin'];
            $allowautolock = $systemconfiguration['allowautolock'];
            $showdeliverypriority = $systemconfiguration['showdeliverypriority'];
            $registerfromapp = $systemconfiguration['registerfromapp'];
            $generateinvoiceaddingsalesorder = $systemconfiguration['generateinvoiceaddingsalesorder'];
            $allowselectingnearestmember = $systemconfiguration['allowselectingnearestmember'];
            $bankdropdownonsales = $systemconfiguration['bankdropdownonsales'];
            $cashorbankmodule = $systemconfiguration['cashorbankmodule'];
            $orderdeliveryoption = $systemconfiguration['orderdeliveryoption'];
            $addressrequiredinregister = $systemconfiguration['addressrequiredinregister'];
            $codoptioninorder = $systemconfiguration['codoptioninorder'];
            $advancepaymentoptioninorder = $systemconfiguration['advancepaymentoptioninorder'];
            $routelistmodule = $systemconfiguration['routelistmodule'];
            $paymenttitleinapp = $systemconfiguration['paymenttitleinapp'];
            $registeronlydefaultcountry = $systemconfiguration['registeronlydefaultcountry'];

            $this->load->Model("Manage_content_model","Manage_content");
            
            $infrastructureid= array_search('Infrastructure', $this->contenttype);
            $infrastructure = $this->Manage_content->CheckContentIsAvailable($infrastructureid);
            (string)$infrastructure=(bool)$infrastructure;      

            $certificateid= array_search('Certificate', $this->contenttype);  
            $certificate = $this->Manage_content->CheckContentIsAvailable($certificateid);              
            (string)$certificate=(bool)$certificate; 

            $aboutusid= array_search('About Us', $this->contenttype);  
            $aboutus = $this->Manage_content->CheckContentIsAvailable($aboutusid); 
            (string)$aboutus=(bool)$aboutus; 
            
            if($dealerecord==0){
                (string)$dealerecord=false;
            }else{
                (string)$dealerecord=true;
            }
            $this->load->model('Settings_model', 'Setting');
            $this->Setting->_fields = 'businessname, address,website,email,logo,facebooklink,googlelink,twitterlink,instagramlink,(select count(id) from '.tbl_catalog.' where status=1)as checkcatalog,
            IFNULL((SELECT GROUP_CONCAT(mobileno SEPARATOR " / ") FROM '.tbl_companycontactdetails.' WHERE type=0),"") as mobileno,
            IFNULL((SELECT GROUP_CONCAT(email) FROM '.tbl_companycontactdetails.' WHERE type=1),"") as email
            ';
            $companydata = $this->Setting->getRecordsByID();     
            if(isset($companydata['checkcatalog']) && $companydata['checkcatalog']>0){
                $companydata['iscatalogvariable']= true;
            }else{
                $companydata['iscatalogvariable']= false;
            }

            unset($companydata['checkcatalog']);
            $companydata['isdealervariable']=$dealerecord;
            $companydata['isinfrastructure']=$infrastructure;
            $companydata['iscertificate']=$certificate;
            $companydata['isaboutus']=$aboutus;
            $companydata['isrewardspoints']= $istype==0?$systemconfiguration['rewardspoints']:'0';
            
            //Order System enable to make a payment 
            if(isset($systemconfiguration['payment']) && $systemconfiguration['payment']==1){
                $companydata['isPayment'] = $istype==0?true:false;
            }else{
                $companydata['isPayment']=false;
            }
            
            //Payment Gateway enable to make a online payment 
            if(isset($systemconfiguration['onlinepayment']) && $systemconfiguration['onlinepayment']==1){
                $companydata['onlinepayment'] = $istype==0?true:false;
            }else{
                $companydata['onlinepayment']=false;
            }
            $companydata['allowmultiplememberwithsamechannel'] = $systemconfiguration['allowmultiplememberwithsamechannel'];
            if($systemconfiguration['discountcoupon']==1){ 
                $companydata['couponcode'] = $istype==0?true:false;
            }else{ 
                $companydata['couponcode']=false; 
            }

            if($systemconfiguration['vendordiscount']==1 && !empty($memberid)){

                $this->load->model("Member_discount_model","Member_discount");
                $this->Member_discount->_fields = "discountonbill,gstondiscount,discountonbilltype,discountonbillvalue,discountonbillminamount,discountonbillstartdate,discountonbillenddate";
                $this->Member_discount->_where = array("discountonbill"=>1,"memberid"=>$memberid);
                $memberdiscount = $this->Member_discount->getRecordsByID();

                if(count($memberdiscount)>0){

                    if($systemconfiguration['discountonbill']==1 && $systemconfiguration['discountpriority']==1 && $checkmember['discountpriority']==1){        
                       
                        $companydata['discounttype']=$memberdiscount['discountonbilltype'];
                        if(($memberdiscount['discountonbillstartdate']=="0000-00-00" && $memberdiscount['discountonbillenddate']=="0000-00-00") || ($memberdiscount['discountonbillstartdate']!="0000-00-00" && $memberdiscount['discountonbillenddate']!="0000-00-00") || ($memberdiscount['discountonbillstartdate']<=date("Y-m-d") && $memberdiscount['discountonbillenddate']>=date("Y-m-d"))){
                            $companydata['discount']=$memberdiscount['discountonbillvalue'];
                        }else{
                            $companydata['discount']='0';
                        }
                        $companydata['minimumbillamount']=$memberdiscount['discountonbillminamount'];
                        $companydata['gstondiscount']=$memberdiscount['gstondiscount'];
                    }else{
                        $companydata['discounttype']=$systemconfiguration['discountonbilltype'];
                        if(($systemconfiguration['discountonbillstartdate']=="0000-00-00" && $systemconfiguration['discountonbillenddate']=="0000-00-00") || ($systemconfiguration['discountonbillstartdate']<=date("Y-m-d") && $systemconfiguration['discountonbillenddate']>=date("Y-m-d"))){
                            $companydata['discount']=$systemconfiguration['discountonbillvalue'];
                        }else{
                            $companydata['discount']='0';
                        }
                        $companydata['minimumbillamount']=$systemconfiguration['discountonbillminamount'];
                        $companydata['gstondiscount']=$systemconfiguration['gstondiscount'];
                    }    

                }else{
                    
                    $channeldiscount = $this->Channel->getChannelDataByID($channelid);
                    
                    if(!empty($channeldiscount)){

                        if($systemconfiguration['discountonbill']==1 && $systemconfiguration['discountpriority']==1 && $checkmember['discountpriority']==1){        
                        
                            $companydata['discounttype']=$channeldiscount['discountonbilltype'];
                            if(($channeldiscount['discountonbillstartdate']=="0000-00-00" && $channeldiscount['discountonbillenddate']=="0000-00-00") || ($channeldiscount['discountonbillstartdate']!="0000-00-00" && $channeldiscount['discountonbillenddate']!="0000-00-00") || ($channeldiscount['discountonbillstartdate']<=date("Y-m-d") && $channeldiscount['discountonbillenddate']>=date("Y-m-d"))){
                                $companydata['discount']=$channeldiscount['discountonbillvalue'];
                            }else{
                                $companydata['discount']='0';
                            }
                            $companydata['minimumbillamount']=$channeldiscount['discountonbillminamount'];
                            $companydata['gstondiscount']=$channeldiscount['gstondiscount'];
                        }else{
                            $companydata['discounttype']=$systemconfiguration['discountonbilltype'];
                            if(($systemconfiguration['discountonbillstartdate']=="0000-00-00" && $systemconfiguration['discountonbillenddate']=="0000-00-00") || ($systemconfiguration['discountonbillstartdate']<=date("Y-m-d") && $systemconfiguration['discountonbillenddate']>=date("Y-m-d"))){
                                $companydata['discount']=$systemconfiguration['discountonbillvalue'];
                            }else{
                                $companydata['discount']='0';
                            }
                            $companydata['minimumbillamount']=$systemconfiguration['discountonbillminamount'];
                            $companydata['gstondiscount']=$systemconfiguration['gstondiscount'];
                        }    

                    }else{
                        if($systemconfiguration['discountonbill']==1){        
                            $companydata['discounttype']=$systemconfiguration['discountonbilltype'];
                            if(($systemconfiguration['discountonbillstartdate']=="0000-00-00" && $systemconfiguration['discountonbillenddate']=="0000-00-00") || ($systemconfiguration['discountonbillstartdate']<=date("Y-m-d") && $systemconfiguration['discountonbillenddate']>=date("Y-m-d"))){
                                $companydata['discount']=$systemconfiguration['discountonbillvalue'];
                            }else{
                                $companydata['discount']='0';
                            }
                            $companydata['minimumbillamount']=$systemconfiguration['discountonbillminamount'];
                            $companydata['gstondiscount']=$systemconfiguration['gstondiscount'];
                        }else{
                            $companydata['minimumbillamount']='0';
                            $companydata['discounttype']='0';
                            $companydata['discount']='0';
                            $companydata['gstondiscount']='0';
                        }
                    }
                }
            }else{
                if($systemconfiguration['discountonbill']==1){        
                    $companydata['discounttype']=$systemconfiguration['discountonbilltype'];
                    if(($systemconfiguration['discountonbillstartdate']=="0000-00-00" && $systemconfiguration['discountonbillenddate']=="0000-00-00") || ($systemconfiguration['discountonbillstartdate']<=date("Y-m-d") && $systemconfiguration['discountonbillenddate']>=date("Y-m-d"))){
                        $companydata['discount']=$systemconfiguration['discountonbillvalue'];
                    }else{
                        $companydata['discount']='0';
                    }
                    $companydata['minimumbillamount']=$systemconfiguration['discountonbillminamount'];
                    $companydata['gstondiscount']=$systemconfiguration['gstondiscount'];
                }else{
                    $companydata['minimumbillamount']='0';
                    $companydata['discounttype']='0';
                    $companydata['discount']='0';
                    $companydata['gstondiscount']='0';
                }
            }
            if($istype==1){
                $companydata['guestchannelid'] = $systemconfiguration['guestchannelid'];
                $companydata['guestmemberid'] = $systemconfiguration['guestchannelid']!=0?$systemconfiguration['guestmemberid']:'0';
            }else{
                $companydata['guestchannelid'] = 0;
                $companydata['guestmemberid'] = 0;
            }
            $companydata['customerchannelid'] = $systemconfiguration['customerchannelid'];
            $companydata['systemedittaxrate'] = $systemsetting['edittaxrate'];
            unset($companydata['payment']);
            
            $this->load->model('Channel_model', 'Channel'); 
            if(!empty($memberid)){
                $this->load->model('Member_model', 'Member');
                $memberdata = $this->Member->getmainmember($memberid,"row");
                if(isset($memberdata['id'])){
                    $sellermemberid = $memberdata['id'];
                }else{
                    $sellermemberid = 0;
                }
                $channeldata = $this->Channel->getChannelSettingsForAPP($channelid,$memberid,$sellermemberid);
            }else{
                $channeldata = $this->Channel->getChannelSettingsForAPP($channelid);
            }

            $companydata['allows3']= ALLOWS3;
            $companydata['awslink']= AWSLINK.BUCKETNAME.'/';
            $companydata['clientfolder']= CLIENT_FOLDER;
            
            $this->load->model('System_configuration_model','System_configuration');
            $companydata['currentandroidversion']= $this->System_configuration->getAppVesrion(0);
            $companydata['currentiosversion']= $this->System_configuration->getAppVesrion(1);
            
            $companydata['smssystem'] = $systemconfiguration['sms'];
            $companydata['withclientfolder'] = $systemconfiguration['withclientfolder'];
            
            $companydata['qrcode'] = $systemconfiguration['qrcode'];
            $companydata['hideregisteraschanneliddropdowninapp'] = $systemconfiguration['hideregisteraschanneliddropdowninapp'];
            $companydata['orderassign'] = $systemconfiguration['orderassign'];
            $companydata['registeraschannelidinapp'] = $systemconfiguration['registeraschannelidinapp'];
            $companydata['gstprice'] = $systemconfiguration['price'];

            $companydata['Member_label'] = Member_label;
            $companydata['member_label'] = member_label;
            $companydata['applicationlayout'] = $systemconfiguration['applicationlayout'];
            $companydata['hideemi'] = $systemconfiguration['hideemi'];
            
            $this->load->model("Social_media_model","Social_media");
            $socialmediadata = $this->Social_media->getActiveSocialmediaList();
            $sociallink = array();
            if(!empty($socialmediadata)){
                foreach($socialmediadata as $socialmedia){
                    $sociallink[] = array("name"=>$socialmedia['name'],"socialmediatype"=>$socialmedia['socialmediatype'],"url"=>$socialmedia['url']);
                }
            }
            $companydata['socialmedia'] = $sociallink;

            $mobilenodata = $this->Setting->getCompanyContactDetailsByType();
            $mobileno = array();
            if(!empty($mobilenodata)){
                foreach($mobilenodata as $i=>$mobile){
                    $mobileno["mobile".($i+1)] = $mobile['mobileno'];
                }
            }

            $emaildata = $this->Setting->getCompanyContactDetailsByType(1);
            $email = array();
            if(!empty($emaildata)){
                foreach($emaildata as $e=>$Email){
                    $email["email".($e+1)] = $Email['email'];
                }
            }
            $companydata['contactdetail'] = array("mobileno"=>$mobileno,"email"=>$email);

            $appsettingdata = array(
                "hsncode" => $hsncode,
                "gstr1report" => $gstr1report,
                "gstr2report" => $gstr2report,
                "pointhistoryreport" => $pointhistoryreport,
                "targetoffer" => $targetoffer,
                "cashbackofferqr" => $cashbackofferqr,
                "cashbackreport" => $cashbackreport,
                "extracharge" => $extracharge,
                "reports" => $reports,
                "paymentreport" => $paymentreport,
                "productwisestockreport" => $productwisestockreport,
                "totalstockreport" => $totalstockreport,
                "minimumstockreport" => $minimumstockreport,
                "abcinventoryreport" => $abcinventoryreport,
                "allowsettingstocklimit" => $allowsettingstocklimit,
                "quotationtoorderreport" => $quotationtoorderreport,
                "ordertodeliverreport" => $ordertodeliverreport,
                "cancelorderreport" => $cancelorderreport,
                "salesreturnreport" => $salesreturnreport,
                "abcpaymentreport" => $abcpaymentreport,
                "salesanalysisreport" => $salesanalysisreport,
                "balancereport" => $balancereport,
                "overallinouthistory" => $overallinouthistory,
                "shownews" => $shownews,
                "feedbackquestion" => $feedbackquestion,
                "invoicepayment" => $invoicepayment,
                "invoicepaymentoffer" => $invoicepaymentoffer,
                "document" => $document,
                "sociallogin" => $sociallogin,
                "facebooklogin" => $facebooklogin,
                "googlelogin" => $googlelogin,
                "guestcart" => $guestcart,
                "selectordertypeincart" => $selectordertypeincart,
                "selectaddressincart" => $selectaddressincart,
                "hidesummaryscreenforcustomer" => $hidesummaryscreenforcustomer,
                "showemireminderalert" => $showemireminderalert,
                "showstocklimitreminder" => $showstocklimitreminder,
                "allowaddinvoice" => $allowaddinvoice,
                "allowaddcreditnote" => $allowaddcreditnote,
                "showbrands" => $showbrands,
                "allowsettingreminderdays" => $allowsettingreminderdays,
                "allowquicklogin" => $allowquicklogin,
                "allowautolock" => $allowautolock,
                "showdeliverypriority" => $showdeliverypriority,
                "registerfromapp" => $registerfromapp,
                "generateinvoiceaddingsalesorder" => $generateinvoiceaddingsalesorder,
                "allowselectingnearestmember" => $allowselectingnearestmember,
                "bankdropdownonsales" => $bankdropdownonsales,
                "cashorbankmodule" => $cashorbankmodule,
                "orderdeliveryoption" => $orderdeliveryoption,
                "addressrequiredinregister" => $addressrequiredinregister,
                "codoptioninorder" => $codoptioninorder,
                "advancepaymentoptioninorder" => $advancepaymentoptioninorder,
                "routelistmodule" => $routelistmodule,
                "paymenttitle" => $paymenttitleinapp,
                "registeronlydefaultcountry" => $registeronlydefaultcountry
            );

            $data['companysettings'] = $companydata;
            $data['channelsettings'] = (object)$channeldata;
            $data['appsetting'] = $appsettingdata;

            if(!empty($data)){
                ws_response("success", "", $data);
            } else {
                ws_response("fail", EMPTY_DATA);
            }
            
        }
    }
}