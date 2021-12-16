<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pending_shipping extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Pending_shipping');
        $this->load->model('Pending_shipping_model', 'Pending_shipping');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Pending Shipping";
        $this->viewData['module'] = "pending_shipping/Pending_shipping";

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->load->model('Fedexaccount_model','Fedexaccount');
        $this->viewData['fedexaccountdata'] = $this->Fedexaccount->getFedexaccountList();

        $this->load->model('Courier_company_model', 'Courier_company');
        $this->viewData['couriercompanylist'] = $this->Courier_company->getActiveCouriercompanyList();

        $this->load->model('Transporter_model', 'Transporter');
        $this->viewData['transporterlist'] = $this->Transporter->getActiveTransporterList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Pending Shipping','View pending shipping.');
        }

        $fedexcourierdata = $this->Pending_shipping->getFedexCourierID();
        $this->viewData['fedexcourierid'] = $fedexcourierdata['fedexcourierid'];

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("bootstrap-datetimepicker","bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_bottom_javascripts("Pending_shipping", "pages/pending_shipping.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
        
        $this->load->model('Shiprocket_order_model','Shiprocket_order');

        $fedexcourierdata = $this->Pending_shipping->getFedexCourierID();
        $fedexcourierid = $fedexcourierdata['fedexcourierid'];

        $list = $this->Pending_shipping->get_datatables();
        
        $data = array();
        $counter = $srno = $_POST['start'];

        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        foreach ($list as $Pendingshipping) {
            $row = array();
            $channellabel = "";
            if($Pendingshipping->buyerchannelid != 0){
                $key = array_search($Pendingshipping->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }
           
            $invoicestatus = '<span class="label" style="background-color:'.$this->Invoicestatuscolorcode[$Pendingshipping->status].';color: #fff;">'.$this->Invoicestatus[$Pendingshipping->status].'</span>';

            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Pendingshipping->memberid.'" title="'.ucwords($Pendingshipping->name).'" target="_blank">'.ucwords($Pendingshipping->name).' ('.$Pendingshipping->membercode.')'.'</a>';

            if($Pendingshipping->sellerchannelid != 0){
                $key = array_search($Pendingshipping->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Pendingshipping->sellerid.'" target="_blank" title="'.$Pendingshipping->sellername.'">'.ucwords($Pendingshipping->sellername).' ('.$Pendingshipping->sellercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            $row[] = '<a href="'.ADMIN_URL.'invoice/view-invoice/'.$Pendingshipping->id.'" title="View Invoice" target="_blank">'.$Pendingshipping->invoicenumber.'</a>';
            $row[] = $this->general_model->displaydatetime($Pendingshipping->invoicedate);
            $row[] = '<span id="paymentmethod'.$Pendingshipping->id.'">'.$Pendingshipping->paymentmethod.'</span>';
            
            if($Pendingshipping->courierid!=0){
                $shippingcompany = '<a href="javascript:void(0)" data-toggle="modal" data-target="#myModal2" onclick="viewshippingorder('.$Pendingshipping->id.');" title="View Shipping Order">'.ucwords($Pendingshipping->shippingcompany).'</a>';
            }else{
                $shippingcompany = '-';
            }
            $row[] = '<input type="hidden" id="courierid'.$Pendingshipping->id.'" value="'.$Pendingshipping->courierid.'">'.$shippingcompany;
            
            $row[] = '<input type="hidden" id="weight'.$Pendingshipping->id.'" value="'.$Pendingshipping->weight.'">
                        <input type="hidden" id="invoicestatus'.$Pendingshipping->id.'" value="'.$Pendingshipping->status.'">'.$invoicestatus;

            $row[] = '<input type="hidden" id="invoiceamount'.$Pendingshipping->id.'" value="'.$Pendingshipping->amount.'">
                        <input type="hidden" id="codamount'.$Pendingshipping->id.'" value="'.$Pendingshipping->amount.'">
                        '.number_format($Pendingshipping->amount,'2','.',',');
            
            $Action='';

            if($Pendingshipping->status==0){
                $Action .= '<a class="'.shipping_class.'" href="javascript:void(0)" onclick="openshippingorder('.$Pendingshipping->id.','.$Pendingshipping->extendshippingaddress.');" title='.shipping_title.'>'.shipping_text.'</a>';
            }else{

                $Action .= '<a class="'.view_class.'" href="javascript:void(0)" data-toggle="modal" data-target="#myModal2" onclick="viewshippingorder('.$Pendingshipping->id.');" title='.view_title.'>'.view_text.'</a>';
                if($Pendingshipping->courierid==$fedexcourierid){
                    $url = str_replace('{tracknumbers}', $Pendingshipping->trackingcode, FEDEX_TRACK_URL);
                }else if($Pendingshipping->trackingurl!=''){
                    $url = urldecode($Pendingshipping->trackingurl);
                }else{
                    if ($Pendingshipping->awbcode!='') {
                        $url = 'https://app.shiprocket.in/tracking/awb/'.$Pendingshipping->awbcode;
                    }else{
                        $url = '#';
                    }
                }

                if($url!='#'){
                    $Action .= '<a class="'.track_class.'" href="'.$url.'" target="_blank" title='.track_title.'>'.track_text.'</a>';
                }
                if($Pendingshipping->courierid==$fedexcourierid){
                    $Action .= '<a class="'.downloadlabel_class.'" href="'.ADMIN_URL.'Pending-shipping/downloadlabel/'.$Pendingshipping->id.'" title="'.downloadlabel_title.'" onclick="$.skylo(\'end\');">'.downloadlabel_text.'</a>';
                }else{
                    
                    $shiprocketsetting = $this->Shiprocket_order->getShiprocketSetting();
                    if(!empty($shiprocketsetting)){

                        $generatelabel = $this->Shiprocket_order->generateLabel($Pendingshipping->shiprocketshipmentid);
                        $generatelabel = '{
                            "label_created": 1,
                            "label_url": "https://kr-shipmultichannel.s3.ap-southeast-1.amazonaws.com/25149/labels/shipping-label-16104408-788830567028.pdf",
                            "response": "Label has been created and uploaded successfully!",
                            "not_created": []
                          }';
                        
                        $generatelabel = json_decode($generatelabel);
                        //print_r($generatelabel->label_created);exit;
    
                        if((isset($generatelabel->label_url)) && $generatelabel->label_created==1){
                            $labelurl = $generatelabel->label_url;
                        }
    
                        $Action .= '<a href="'.$labelurl.'" target="_blank" class="'.downloadlabel_class.'" title="'.downloadlabel_title.'">'.downloadlabel_text.'</a>';
                    }
                }
                if($Pendingshipping->status==4){
                    $Action .= '<a class="'.generateinvoice_class.'" href="javascript:void(0)" title="'.generateinvoice_title.'" onclick="generateinvoice(&quot;'.$Pendingshipping->invoicenumber.'&quot;,'.$Pendingshipping->id.')">'.generateinvoice_text.'</a>';
                }


                /* if($Pendingshipping->status==4){
                    $Action .= '<a class="'.sendinvoice_class.'" href="javascript:void(0)" title="'.sendinvoice_title.'" onclick="generateinvoice('.$Pendingshipping->invoicenumber.','.$Pendingshipping->id.')">'.sendinvoice_text.'</a>';
                } */
            }

            $row[] = $Action;

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Pending_shipping->count_all(),
                        "recordsFiltered" => $this->Pending_shipping->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }
    
    public function place_shipping_order(){
        $PostData = $this->input->post();
        //$PostData['fedexcodamount'];exit;
        // print_r($PostData); exit;
        $invoiceid = $PostData['invoiceid'];
        $shippingby = $PostData['shippingby'];
        $shippingcompanyid = ($shippingby==0)?$PostData['courierid']:$PostData['transporterid'];
        
        $TotalWeight = 0;
        $label = $labelname = $response = array();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $this->Pending_shipping->_table = tbl_shippingorder;
        $this->Pending_shipping->_where = "invoiceid=".$invoiceid;
        $Count = $this->Pending_shipping->CountRecords();
       
        if($Count==0){

            $shippingamount = $fedexdetailid = 0;
            $trackingcode = '';

            $fedexcourierdata = $this->Pending_shipping->getFedexCourierID();
            $fedexcourierid = $fedexcourierdata['fedexcourierid'];
            $fedexcodamount = isset($PostData['fedexcodamount'])?$PostData['fedexcodamount']:0;

            if($shippingby==0 && $shippingcompanyid==$fedexcourierid){

                $data = array();
                $fedexdetailid = (!empty($PostData['fedexdetailid']))?$PostData['fedexdetailid']:0;
                $this->load->model('Fedexaccount_model','Fedexaccount');
                $this->Fedexaccount->_where = array('id'=>$PostData['fedexdetailid']);
                $data['fedexaccount'] = $this->Fedexaccount->getRecordsByID();

                $this->load->model('Invoice_setting_model','Invoice');
                $shipperdetailsdata = $this->Invoice->getShipperDetails();


                $data['shipperdetail'] = $shipperdetailsdata;
               
                $data['recipientdetail'] = $this->Pending_shipping->getRecipientDetails($PostData['invoiceid']);
                
                $data['fedexservice'] = $PostData['fedexservice'];
                $data['invoiceamount'] = $PostData['invoiceamount'];
                $shippingamount = $PostData['shippingamount'];
                $data['fedexcodamount'] = isset($PostData['fedexcodamount'])?$PostData['fedexcodamount']:0;
                
              
                
                $data['Weight'] = $PostData['fedexweight'];
                $data['length'] = $PostData['length'];
                $data['width'] = $PostData['width'];
                $data['height'] = $PostData['height'];
                $data['units'] = $PostData['units'];
                
                for ($i=0; $i < count($PostData['fedexweight']); $i++) { 
                    $TotalWeight = $TotalWeight + $PostData['fedexweight'][$i];
                    
                
                    
                }
            
                //$data['fedexservice'] = $PostData['fedexservice'];
                $data['TotalWeight'] = $TotalWeight;

               
                /* $this->load->model('Common_model');
                $responsedata = $this->Common_model->curl_get_contents(ADMIN_URL.'fedex/OpenShip/MasterShipWebServiceClient'); 
                print_r($responsedata);exit;
                 */ 

                $responsedata = $this->load->view(ADMINFOLDER.'fedex/OpenShip/MasterShipWebServiceClient',$data,true);
                
               /*  print_r($responsedata);exit;  */
                
               $responsedata = json_decode($responsedata,true); 
                if(!empty($responsedata) && isset($responsedata['TrackingNumber'])){
                    $trackingcode = $responsedata['TrackingNumber'];
                    $label = $responsedata['Label'];

                }else{
                    $response = array("error"=>0,"label"=>$responsedata[0]);
                    echo json_encode($response);
                    exit;
                }
                //print_r($responsedata);
                //exit;

            }else{
                $indianpostremarks = $PostData['indianpostremarks'];
                $trackingcode = $PostData['indianposttrackingcode'];
                $indianpostamount = $PostData['indianpostamount'];
                $invoiceamount = $PostData['invoiceamount'];

                $shippingamount = 0;
                for ($i=0; $i < count($indianpostamount); $i++) {
                    $shippingamount = (float)$shippingamount + (float)$indianpostamount[$i];
                }
            }
            
            $insertdata = array("invoiceid"=>$invoiceid,
                                "shippingby"=>$shippingby,
                                "courierid"=>$shippingcompanyid,
                                "trackingcode"=>$trackingcode,
                                "currency"=>"INR",
                                "shippingamount"=>$shippingamount,
                                "invoiceamount"=>$PostData['invoiceamount'],
                                "codcharges"=>$fedexcodamount,
                                "shipdate"=>$this->general_model->getCurrentDate(),
                                "iscod"=>1,
                                "status"=>2,
                                "addedby"=>$addedby,
                                "createddate"=>$createddate,
                            );
            $insertdata = array_map('trim', $insertdata);
            $this->Pending_shipping->_table = (tbl_shippingorder);
            $ShippingOrderID = $this->Pending_shipping->Add($insertdata);

            //PLACE ORDER IN FEDEX
            if($shippingby==0 && $shippingcompanyid==$fedexcourierid){

                $fedexweight = $PostData['fedexweight'];
                $lengtharr = $PostData['length'];
                $widtharr = $PostData['width'];
                $heightarr = $PostData['height'];
                $unitsarr = $PostData['units'];
                
                $this->Pending_shipping->_table = tbl_shippingpackage;
                for ($i=0; $i < count($fedexweight); $i++) { 
                    
                    $length = (!empty($lengtharr[$i]))?$lengtharr[$i]:0;
                    $width = (!empty($widtharr[$i]))?$widtharr[$i]:0;
                    $height = (!empty($heightarr[$i]))?$heightarr[$i]:0;
                    $units = (!empty($unitsarr[$i]))?$unitsarr[$i]:'';

                    $insertdata = array("shippingorderid"=>$ShippingOrderID,
                                    "weight"=>$fedexweight[$i],
                                    "amount"=>$shippingamount/count($fedexweight),
                                    "length"=>$length,
                                    "width"=>$width,
                                    "height"=>$height,
                                    "units"=>$units,
                                    );

                    $insertdata = array_map('trim', $insertdata);
                    $this->Pending_shipping->Add($insertdata);
                }

                /*  if(isset($PostData['fedexcodamount'])){
                    $this->Pendingshipping->_table = tbl_fedexshippingorder;
                    $insertdata = array("shippingorderid"=>$ShippingOrderID,
                                        "iscod"=>1,
                                        "codamount"=>$PostData['fedexcodamount']);

                    $insertdata = array_map('trim', $insertdata);
                    $this->Pendingshipping->Add($insertdata);
                } */

                if(!empty($label)){
                    $this->Pending_shipping->_table = tbl_fedexshippinglabel;
                    for ($i=0; $i < count($label); $i++) { 
                        
                        $insertdata = array("shippingorderid"=>$ShippingOrderID,
                                        "type"=>$label[$i]['type'],
                                        "file"=>$label[$i]['file']);

                        $insertdata = array_map('trim', $insertdata);
                        $this->Pending_shipping->Add($insertdata);

                        $labelname[] = $label[$i]['file'];
                    }
                }

               //update invoice status
               $this->load->model('Invoice_model', 'Invoice');
               $this->Invoice->_table = (tbl_invoice);
               $updatedata = array("shippingby"=>$shippingby,"courierid" => $shippingcompanyid,"status" => 4,'modifieddate'=>$createddate,"addedby" => $addedby);
               $this->Invoice->_where = array('id' => $invoiceid);
               $this->Invoice->Edit($updatedata);


                

                /* $this->Order->_fields = "ordernumber";
                $this->Order->_where = array('id' => $PostData['orderid']);
                $OrderID = $this->Order->getRecordsByID(); */

                
                /***************send email to customer***************************/
                /* $this->load->model('Customer_model', 'Customer');
                $CustomerData = $this->Customer->getCustomerName($data['recipientdetail']['customerid']);
                $customername = $CustomerData['firstname']." ".$CustomerData['lastname'];

                $mailto = $CustomerData['email'];
                $url = str_replace('{tracknumbers}', $trackingcode, FEDEX_TRACK_URL);
                $subject= array("{companyname}"=>Companyname);

                $mailBodyArr = array(
                            "{logo}" => '<a href="'. DOMAIN_URL.'"><img src="' . MAIN_LOGO_IMAGE_URL. CompanyLogo.'" alt="' . Companyname . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{name}" => $customername,
                            "{ordernumber}" => $OrderID['ordernumber'],
                            "{trackingnumber}" => $trackingcode,
                            "{trackurl}" => $url,
                            "{companyemail}" => CompanyEmail,
                            "{companyname}" => Companyname,
                            "{companywebsite}" => '<a href="'.DOMAIN_URL.'" target="_blank">'.CompanyWebsite.'</a>'
                        );
                
                //Send mail with email format store in database
                $mailid=array_search("Tracking Order For Customer",$this->Emailformattype);
                if(isset($mailid) && !empty($mailid)){
                    $this->Customer->sendMail($mailid, $mailto, $mailBodyArr);
                } */

                $response = array("error"=>1,"label"=>$labelname);

            }else{
                $indianpostweight = $PostData['indianpostweight'];
                $indianpostamount = $PostData['indianpostamount'];

                $this->Pending_shipping->_table = tbl_shippingpackage;

                for ($i=0; $i < count($indianpostamount); $i++) {
                    $insertdata = array("shippingorderid"=>$ShippingOrderID,
                                "weight"=>$indianpostweight[$i],
                                "amount"=>$indianpostamount[$i]);

                    $insertdata = array_map('trim', $insertdata);
                    $this->Pending_shipping->Add($insertdata);
                }

                //update invoice status
                $this->load->model('Invoice_model', 'Invoice');
                $this->Invoice->_table = (tbl_invoice);
                $updatedata = array("shippingby"=>$shippingby,"courierid" => $shippingcompanyid,"status" => 4,'modifieddate'=>$createddate,"addedby" => $addedby);
                $this->Invoice->_where = array('id' => $invoiceid);
                $this->Invoice->Edit($updatedata);

                
                $response = array("error"=>1);
            }

            $this->load->model('Invoice_model', 'Invoices');
            $remarks = (isset($indianpostremarks))?$indianpostremarks:'';
            $this->Invoices->updateremarks($invoiceid,$remarks);
            $this->Invoice->_table = (tbl_invoice);

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Invoice->_fields="invoiceno";
                $this->Invoice->_where=array("id"=>$invoiceid);
                $invoicedata = $this->Invoice->getRecordsByID();

                $this->general_model->addActionLog(1,'Pending Shipping','Add new shipping order invoice '.$invoicedata['invoiceno'].'.');
            }
            echo json_encode($response);
            exit;
        }else{
            
            $response = array("error"=>0);
            echo json_encode($response);
        }
        
    }
    public function viewshippingorderdetails(){
        $PostData = $this->input->post();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->load->model('Invoice_model', 'Invoice');
            $this->Invoice->_fields="invoiceno";
            $this->Invoice->_where=array("id"=>$PostData['invoiceid']);
            $invoicedata = $this->Invoice->getRecordsByID();

            $this->general_model->addActionLog(4,'Pending Shipping','View '.$invoicedata['invoiceno'].' shipping order details.');
        }
        echo $this->Pending_shipping->viewShippingOrderDetails($PostData['invoiceid']);
    }
    public function generateinvoice(){
        $PostData = $this->input->post();
        //print_r($PostData);exit;

       /*  $this->load->model('Invoice_model', 'Invoice');
        $this->Invoice->sendinvoice($PostData);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->load->model('Invoice_model', 'Invoice');
            $this->Invoice->_fields="invoiceno";
            $this->Invoice->_where=array("id"=>$PostData['invoiceid']);
            $invoicedata = $this->Invoice->getRecordsByID();

            $this->general_model->addActionLog(0,'Pending Shipping','Send shipping order invoice '.$invoicedata['invoiceno'].'.');
        } */

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
       
        $updatedata = array("status" => 1,'modifieddate'=>$createddate,"modifiedby" => $addedby);
        $this->Pending_shipping->_where = array('id' => $PostData['invoiceid']);
        $this->Pending_shipping->Edit($updatedata);

        echo 1;
        
    }
    public function getfedexrate(){
        $PostData = $this->input->post();
        //print_r($PostData);exit;
        $this->load->model('Fedexaccount_model','Fedexaccount');
        $this->Fedexaccount->_where = array('id'=>$PostData['fedexdetailid']);
        $data['fedexaccount'] = $this->Fedexaccount->getRecordsByID();

       
        $this->load->model('Invoice_setting_model','Invoice');
        $data['shipperdetail'] = $this->Invoice->getShipperDetails();
        //print_r( $data['shipperdetail']);exit;ok
        $data['fedexweight'] = $PostData['fedexweight'];
        $data['length'] = $PostData['length'];
        $data['width'] = $PostData['width'];
        $data['height'] = $PostData['height'];
        $data['units'] = $PostData['units'];
        $data['invoiceamount'] = $PostData['invoiceamount'];
        $data['fedexservice'] = $PostData['fedexservice'];

        /* $dimensions = array();
        if($PostData['length']!='' && $PostData['width']!='' && $PostData['height']!=''){
            $dimensions = array('Length'=>$PostData['length'],
                                'Width'=>$PostData['width'],
                                'Height'=>$PostData['height'],
                                'Units'=>$PostData['units']);
        }
        $data['dimensions'] = $dimensions; */
        
        $data['fedexcodamount'] = isset($PostData['fedexcodamount'])?$PostData['fedexcodamount']:0;
        
        //this->load->model('Order_model','Order');
        $data['recipientdetail'] = $this->Pending_shipping->getRecipientDetails($PostData['invoiceid']);
        //$data['recipient'] = $this->Pendingshipping->getRecipientdata($PostData['invoiceid']);
        /* print_r($data['recipientdetail']);exit; */
        echo $this->load->view(ADMINFOLDER.'fedex/Rate/RateWebServiceClient',$data,true);
        //echo $this->load->view(ADMINFOLDER.'fedex/rate-service-request',$data,true);
    }
    public function downloadlabel($invoiceid){
        
        $this->load->library('zip');

        $LabelData = $this->Pending_shipping->getShippingLabel($invoiceid);

        if(!empty($LabelData)){
            foreach ($LabelData as $row) {
                $this->zip->read_file(FEDEX_LABEL_PATH.'/'.$row['file']);
            }
         
            // prompt user to download the zip file
            $this->zip->download('labelpdf.zip');
        }
    }
}

?>