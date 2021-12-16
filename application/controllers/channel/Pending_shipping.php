<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pending_shipping extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Pending_shipping');
        $this->load->model('Side_navigation_model');
        $this->load->model('Pending_shipping_model', 'Pending_shipping');
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Pending Shipping";
        $this->viewData['module'] = "pending_shipping/Pending_shipping";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model('Courier_company_model', 'Courier_company');
        $this->viewData['couriercompanylist'] = $this->Courier_company->getActiveCouriercompanyList($CHANNELID,$MEMBERID);

        $this->load->model('Transporter_model', 'Transporter');
        $this->viewData['transporterlist'] = $this->Transporter->getActiveTransporterList($MEMBERID);

        $this->load->model('Fedexaccount_model','Fedexaccount');
        $this->Fedexaccount->_table = (tbl_fedexdetail);
        $this->viewData['fedexaccountdata'] = $this->Fedexaccount->getFedexaccountList($MEMBERID,$CHANNELID);
        //print_r($this->viewData['fedexaccountdata']);exit;

        $fedexcourierdata = $this->Pending_shipping->getFedexCourierID($MEMBERID,$CHANNELID);
        $this->viewData['fedexcourierid'] = $fedexcourierdata['fedexcourierid'];
        
        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Pending_shipping", "pages/pending_shipping.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
		
        $this->load->model("Channel_model","Channel");
        $this->load->model('Shiprocket_order_model','Shiprocket_order');
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $fedexcourierdata = $this->Pending_shipping->getFedexCourierID($MEMBERID,$CHANNELID);
        $fedexcourierid = $fedexcourierdata['fedexcourierid'];

        $list = $this->Pending_shipping->get_datatables($MEMBERID,$CHANNELID);
       
        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $datarow) {
			$row = array();
            $Action = $channellabel = "";
            
            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }

            $invoicestatus = '<span class="label" style="background-color:'.$this->Invoicestatuscolorcode[$datarow->status].';color: #fff;">'.$this->Invoicestatus[$datarow->status].'</span>';

            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->name).'" target="_blank">'.ucwords($datarow->name).' ('.$datarow->membercode.')'.'</a>';

            $row[] = '<a href="'.CHANNEL_URL.'invoice/view-invoice/'.$datarow->id.'" title="View Invoice" target="_blank">'.$datarow->invoicenumber.'</a>';
            $row[] = $this->general_model->displaydatetime($datarow->invoicedate);
            $row[] = '<span id="paymentmethod'.$datarow->id.'">'.$datarow->paymentmethod.'</span>';
            if($datarow->courierid!=0){
                $shippingcompany = '<a href="javascript:void(0)" data-toggle="modal" data-target="#myModal2" onclick="viewshippingorder('.$datarow->id.');" title="View Shipping Order">'.ucwords($datarow->shippingcompany).'</a>';
            }else{
                $shippingcompany = '-';
            }
            $row[] = '<input type="hidden" id="courierid'.$datarow->id.'" value="'.$datarow->courierid.'">'.$shippingcompany;
            
            $row[] = '<input type="hidden" id="weight'.$datarow->id.'" value="'.$datarow->weight.'">
                        <input type="hidden" id="invoicestatus'.$datarow->id.'" value="'.$datarow->status.'">'.$invoicestatus;

            $row[] = '<input type="hidden" id="invoiceamount'.$datarow->id.'" value="'.$datarow->amount.'">
                        <input type="hidden" id="codamount'.$datarow->id.'" value="'.$datarow->codamount.'">
                        '.number_format($datarow->amount,'2','.',',');
            
            $Action='';

            if($datarow->status==0){
                $Action .= '<a class="'.shipping_class.'" href="javascript:void(0)" onclick="openshippingorder('.$datarow->id.','.$datarow->extendshippingaddress.');" title='.shipping_title.'>'.shipping_text.'</a>';
            }else{

                $Action .= '<a class="'.view_class.'" href="javascript:void(0)" data-toggle="modal" data-target="#myModal2" onclick="viewshippingorder('.$datarow->id.');" title='.view_title.'>'.view_text.'</a>';
                if($datarow->courierid==$fedexcourierid){
                    $url = str_replace('{tracknumbers}', $datarow->trackingcode, FEDEX_TRACK_URL);
                }else if($datarow->trackingurl!=''){
                    $url = urldecode($datarow->trackingurl);
                }else{
                    if ($datarow->awbcode!='') {
                        $url = 'https://app.shiprocket.in/tracking/awb/'.$datarow->awbcode;
                    }else{
                        $url = '#';
                    }
                }
                if($url!='#'){
                    $Action .= '<a class="'.track_class.'" href="'.$url.'" target="_blank" title='.track_title.'>'.track_text.'</a>';
                }
                if($datarow->courierid==$fedexcourierid){
                    $Action .= '<a class="'.downloadlabel_class.'" href="'.CHANNEL_URL.'Pending-shipping/downloadlabel/'.$datarow->id.'" title="'.downloadlabel_title.'" onclick="$.skylo(\'end\');">'.downloadlabel_text.'</a>';
                }else{
                    
                    $shiprocketsetting = $this->Shiprocket_order->getShiprocketSetting($MEMBERID,$CHANNELID);
                    if(!empty($shiprocketsetting)){
                        $generatelabel = $this->Shiprocket_order->generateLabel($datarow->shiprocketshipmentid);
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
                if($datarow->status==4){
                    $Action .= '<a class="'.sendinvoice_class.'" href="javascript:void(0)" title="'.sendinvoice_title.'" onclick="generateinvoice(&quot;'.$datarow->invoicenumber.'&quot;,'.$datarow->id.')">'.sendinvoice_text.'</a>';
                }
            }
            $row[] = $Action;

            $data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Pending_shipping->count_all($MEMBERID,$CHANNELID),
						"recordsFiltered" => $this->Pending_shipping->count_filtered($MEMBERID,$CHANNELID),
						"data" => $data,
				);
		echo json_encode($output);
    }
    
    /* public function place_shipping_order(){
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $invoiceid = $PostData['invoiceid'];
        $courierid = $PostData['courierid'];
        
        $TotalWeight = 0;
        $label = $labelname = $response = array();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $this->Pending_shipping->_table = tbl_shippingorder;
        $this->Pending_shipping->_where = "invoiceid=".$invoiceid;
        $Count = $this->Pending_shipping->CountRecords();

        if($Count==0){

            $indianpostremarks = $PostData['indianpostremarks'];
            $trackingcode = $PostData['indianposttrackingcode'];
            $indianpostamount = $PostData['indianpostamount'];
            $invoiceamount = $PostData['invoiceamount'];

            $shippingamount = 0;
            for ($i=0; $i < count($indianpostamount); $i++) { 
                $shippingamount = (float)$shippingamount + (float)$indianpostamount[$i];
            }
            
            $insertdata = array("invoiceid"=>$invoiceid,
                                "courierid"=>$courierid,
                                "trackingcode"=>$trackingcode,
                                "currency"=>"INR",
                                "shippingamount"=>$shippingamount,
                                "invoiceamount"=>$invoiceamount,
                                "shipdate"=>$this->general_model->getCurrentDate(),
                                "addedby"=>$addedby,
                                "createddate"=>$createddate,
                            );
            $insertdata = array_map('trim', $insertdata);
            $ShippingOrderID = $this->Pending_shipping->Add($insertdata);

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
            $updatedata = array("courierid" => $courierid,"status" => 4,'modifieddate'=>$createddate,"addedby" => $addedby);
            $this->Invoice->_where = array('id' => $invoiceid);
            $this->Invoice->Edit($updatedata);

            $response = array("error"=>1);

            $remarks = (isset($indianpostremarks))?$indianpostremarks:'';
            $this->Invoice->updateremarks($invoiceid,$remarks);

            echo json_encode($response);
            exit;
        }else{
            $response = array("error"=>0);
            echo json_encode($response);
        }
        
    } */

    public function place_shipping_order(){
        $PostData = $this->input->post();
        //$PostData['fedexcodamount'];
        // print_r($PostData); exit;
        $invoiceid = $PostData['invoiceid'];
        $shippingby = $PostData['shippingby'];
        $shippingcompanyid = ($shippingby==0)?$PostData['courierid']:$PostData['transporterid'];
        
        $TotalWeight = 0;
        $label = $labelname = $response = array();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $this->Pending_shipping->_table = tbl_shippingorder;
        $this->Pending_shipping->_where = "invoiceid=".$invoiceid;
        $Count = $this->Pending_shipping->CountRecords();

        if($Count==0){

            $shippingamount = $fedexdetailid = 0;
            $trackingcode = '';

            $fedexcourierdata = $this->Pending_shipping->getFedexCourierID($MEMBERID,$CHANNELID);
            $fedexcourierid = $fedexcourierdata['fedexcourierid'];
            $fedexcodamount = isset($PostData['fedexcodamount'])?$PostData['fedexcodamount']:0;

            if($shippingby==0 && $shippingcompanyid==$fedexcourierid){

                $data = array();
                $fedexdetailid = (!empty($PostData['fedexdetailid']))?$PostData['fedexdetailid']:0;
                $this->load->model('Fedexaccount_model','Fedexaccount');
                $this->Fedexaccount->_where = array('id'=>$PostData['fedexdetailid']);
                $data['fedexaccount'] = $this->Fedexaccount->getRecordsByID();

                $this->load->model('Invoice_setting_model','Invoice');
                $shipperdetailsdata = $this->Invoice->getShipperDetails($CHANNELID,$MEMBERID);


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

                //print_r($data);exit;
                
                $responsedata = $this->load->view(CHANNELFOLDER.'fedex/OpenShip/MasterShipWebServiceClient',$data,true);
             
                //print_r($responsedata);exit;
                
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
            
            $insertdata = array(
                                "channelid"=>$CHANNELID,
                                "memberid"=>$MEMBERID,
                                "invoiceid"=>$invoiceid,
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
                                "usertype"=>1,
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

            
            echo json_encode($response);
            exit;
        }else{
            $response = array("error"=>0);
            echo json_encode($response);
        }
        
    }

    public function viewshippingorderdetails(){
        $PostData = $this->input->post();
        echo $this->Pending_shipping->viewShippingOrderDetails($PostData['invoiceid']);
    }
    public function generateinvoice(){
        $PostData = $this->input->post();
        //print_r($PostData);exit;

        $this->load->model('Invoice_model', 'Invoice');
        $this->Invoice->sendinvoice($PostData);
        echo 1;
        
    }

    public function getfedexrate(){
        $PostData = $this->input->post();
        //print_r($PostData);exit;

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model('Fedexaccount_model','Fedexaccount');
        $this->Fedexaccount->_where = array('id'=>$PostData['fedexdetailid']);
        $data['fedexaccount'] = $this->Fedexaccount->getRecordsByID();

        
        $this->load->model('Invoice_setting_model','Invoice');
        $data['shipperdetail'] = $this->Invoice->getShipperDetails($CHANNELID,$MEMBERID);
        //print_r( $data['shipperdetail']);exit;
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
        //print_r($data['recipientdetail']);exit;
        echo $this->load->view(CHANNELFOLDER.'fedex/Rate/RateWebServiceClient',$data,true);
        //echo $this->load->view(CHANNELFOLDER.'fedex/rate-service-request',$data,true);
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