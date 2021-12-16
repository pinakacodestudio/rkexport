<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_note extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Credit_note_model', 'Credit_note');
        $this->load->model('Order_model', 'Order');
        $this->viewData = $this->getChannelSettings('submenu', 'Credit_note');
    }
    public function index() {
        $this->viewData['title'] = "Credit Note";
        $this->viewData['module'] = "credit_note/Credit_note";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Credit_note", "pages/credit_note.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->Credit_note->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = $invoiceno_text = array();
            $channellabel = '';
            $Actions = ''; 
            $creditnotestatus = '';
            $status = $datarow->status;
            $invoiceIdArr = explode(",",$datarow->invoiceid);
            $invoiceNumberArr = explode(",",$datarow->invoiceno);

            if(!empty($invoiceNumberArr)){
                foreach($invoiceNumberArr as $key=>$invoiceNumber){
                    $invoiceid = $invoiceIdArr[$key];
                    $invoiceno_text[] = "<a href='".CHANNEL_URL."invoice/view-invoice/". $invoiceid."/"."' title='".$invoiceNumber."' target='_blank'>".$invoiceNumber."</a>";
                }
            }
            $row[] = ++$counter;
            
            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->buyerid){
                    $row[] = $channellabel.ucwords($datarow->buyername).' ('.$datarow->buyercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->sellerid){
                    $row[] = $channellabel.ucwords($datarow->sellername).' ('.$datarow->sellercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.$datarow->sellername.'">'.ucwords($datarow->sellername).' ('.$datarow->sellercode.')'."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
          
            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chagecreditnotestatus(1,'.$datarow->id.')">Complete</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagecreditnotestatus(2,'.$datarow->id.')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chagecreditnotestatus(2,'.$datarow->id.')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }

            $creditnotestatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';

            $Actions .= '<a href="'.CHANNEL_URL.'credit-note/view-credit-note/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     
            
            $Actions .= '<a href="javascript:void(0)" onclick="printCreditNote('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  
            
           /*  if($datarow->status==0){
                if(in_array($rollid, $edit)) {
                    $Actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'credit-note/view-credit-note/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
            } */
           /*  if(file_exists(CREDITNOTE_PATH.$companyname.'-creditnote-'.$datarow->creditnotenumber.'.pdf')){
                $Actions .= '<a href="'.CREDITNOTE.$companyname.'-creditnote-'.$datarow->creditnotenumber.'.pdf" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>'; 
                
            }
            $Actions .= '<a href="javascript:void(0)" class="'.regeneratecredit_class.'" title="'.regeneratecredit_title.'" onclick="regeneratecreditnote('.$datarow->id.')">'.regeneratecredit_text.'</a>';   */

            $Actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',3)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            
            $Actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',3,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            
            $row[] = implode(", ",$invoiceno_text);
            $row[] = ($datarow->creditnotetype==0?'Product':'Offer');
            $row[] = $datarow->creditnotenumber;
            $row[] = $this->general_model->displaydate($datarow->creditnotedate);
            $row[] = $creditnotestatus;         
            $row[] = number_format(round($datarow->netamount),'2','.',',');
            
            $row[] = $Actions;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Credit_note->count_all(),
                        "recordsFiltered" => $this->Credit_note->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function credit_note_add($invoiceid="",$offerid="") {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Credit Note";
        $this->viewData['module'] = "credit_note/Add_credit_note";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');
        
        if($invoiceid!="" && $invoiceid > 0 && $offerid == ""){
            $this->load->model('Invoice_model', 'Invoice');
            $this->Invoice->_fields = "memberid";
            $this->Invoice->_where = array("id"=>$invoiceid);
            $InvoiceData = $this->Invoice->getRecordsById();
    
            $this->viewData['memberid'] = $InvoiceData['memberid']; 
            $this->viewData['invoiceid'] = $invoiceid; 
            $this->viewData['action'] = "0";
        }
        if($invoiceid!="" && $invoiceid > 0 && $offerid!="" && $offerid > 0){
            $this->viewData['memberid'] = $invoiceid;
            $this->viewData['offerid'] = $offerid;
            $this->viewData['action'] = "0";
        }
        $this->load->model('Offer_model','Offer');
        $this->viewData['offerdata'] = $this->Offer->getTargetOfferDataByMemberid($MEMBERID);
       
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("add_credit_note", "pages/add_credit_note.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }
    public function view_credit_note($id) {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Credit Note";
        $this->viewData['module'] = "credit_note/View_credit_note";
        $this->viewData['printtype'] = "creditnote";

        $this->viewData['transactiondata'] = $this->Credit_note->getCreditNoteDetails($id);

        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        
        $this->viewData['printtype'] = 'creditnote';
        $this->viewData['heading'] = 'Credit Note';

        $this->channel_headerlib->add_javascript("view_credit_note", "pages/view_credit_note.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function getTransactionProducts() {

        $this->load->model('Invoice_model', 'Invoice');
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $invoiceid = $PostData['invoiceid'];
        $creditnoteid = $PostData['creditnoteid'];
        $invoiceproductdata = $this->Credit_note->getInvoiceProductsByIDOrMemberID($memberid,$invoiceid,$creditnoteid);
        $invoicedata = $this->Invoice->getInvoiceAmountDataByID($invoiceid);
        $gstpricearray = !empty($invoiceproductdata)?array_column($invoiceproductdata, 'gstprice'):array();

        $json['gstprice'] = in_array("1", $gstpricearray)?1:0;
        $json['invoiceproducts'] = $invoiceproductdata;
        $json['invoiceamountdata'] = $invoicedata;
        
        echo json_encode($json);
    }
    public function add_credit_note() {
        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $buyermemberid = $PostData['memberid'];
        $invoiceid = isset($PostData['invoiceid'])?$PostData['invoiceid']:'';
        $billingaddressid = !empty($PostData['billingaddressid'])?$PostData['billingaddressid']:0;
        $shippingaddressid = !empty($PostData['shippingaddressid'])?$PostData['shippingaddressid']:0;
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $creditnotedate = (!empty($PostData['creditnotedate']))?$this->general_model->convertdate($PostData['creditnotedate']):"";
        $remarks = $PostData['remarks'];
        $creditnotetype = $PostData['creditnotetype'];
        $offerid = isset($PostData['offerid'])?$PostData['offerid']:0;

        $invoiceidarr = isset($PostData['invoiceidarr'])?$PostData['invoiceidarr']:'';
        $transactionproductsidarr = isset($PostData['transactionproductsid'])?$PostData['transactionproductsid']:'';
        $inputtotalpayableamount = $PostData['inputtotalpayableamount'];
        $creditqtyarr = isset($PostData['creditqty'])?$PostData['creditqty']:'';
        $creditpercentarr = isset($PostData['creditpercent'])?$PostData['creditpercent']:'';
        $creditamountarr = isset($PostData['creditamount'])?$PostData['creditamount']:'';
        $productstockqtyarr = isset($PostData['productstockqty'])?$PostData['productstockqty']:'';
        $productrejectqtyarr = isset($PostData['productrejectqty'])?$PostData['productrejectqty']:'';
        
        if($creditnotetype==0){
            $producttotal = $PostData['inputproducttotal'];
            $gsttotal = $PostData['inputgsttotal'];
            $globaldiscount = $PostData['inputovdiscamnt'];
        }else{
            $producttotal = $PostData['inputoffertotal'];
            $gsttotal = $PostData['inputoffergsttotal'];
            $globaldiscount = 0;
        }
       
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';

        $invoiceextrachargesidarr = (isset($PostData['invoiceextrachargesid']))?$PostData['invoiceextrachargesid']:'';
        $invoiceextrachargestaxarr = (isset($PostData['invoiceextrachargestax']))?$PostData['invoiceextrachargestax']:'';
        $invoiceextrachargesnamearr = (isset($PostData['invoiceextrachargesname']))?$PostData['invoiceextrachargesname']:'';
        $invoiceextrachargeamountarr = (isset($PostData['invoiceextrachargeamount']))?$PostData['invoiceextrachargeamount']:'';
        $invoiceextrachargepercentagearr = (isset($PostData['invoiceextrachargepercentage']))?$PostData['invoiceextrachargepercentage']:'';

        $invoicediscountpercentarr = (isset($PostData['invoicediscountpercent']))?$PostData['invoicediscountpercent']:'';
        $invoicediscountamountarr = (isset($PostData['invoicediscountamount']))?$PostData['invoicediscountamount']:'';

        $invoiceidsarr=array();
        if($creditnotetype==0){
            if(!empty($invoiceidarr)){
                foreach($invoiceid as $InvoiceId){
                    if(in_array($InvoiceId, $invoiceidarr)){
                        $invoiceidsarr[] = $InvoiceId;
                    } 
                }
            }
        }else{
            foreach($invoiceid as $InvoiceId){
                $invoiceidsarr[] = $InvoiceId;
            }
        }
        
        $invoiceids = implode(",", $invoiceidsarr);
        if(!empty($invoiceids)){
            
            $creditnotenumber = $this->general_model->generateTransactionPrefixByType(3,$CHANNELID,$MEMBERID);
            $this->Credit_note->_table = tbl_creditnote;
            $this->Credit_note->_where = ("creditnotenumber='".$creditnotenumber."'");
            $Count = $this->Credit_note->CountRecords();
            if($Count==0){

                $insertdata = array("sellermemberid" => $MEMBERID,
                                    "buyermemberid" => $buyermemberid,
                                    "invoiceid" => $invoiceids,
                                    "creditnotenumber" => $creditnotenumber,
                                    "addressid" => $billingaddressid,
                                    "shippingaddressid" => $shippingaddressid,
                                    "billingaddress" => $billingaddress,
                                    "shippingaddress" => $shippingaddress,
                                    "creditnotedate" => $creditnotedate,
                                    "remarks" => $remarks,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "globaldiscount" => $globaldiscount,
                                    "creditnotetype" => $creditnotetype,
                                    "offerid" => $offerid,
                                    "status" => 0,
                                    "type" => 1,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby);
                
                $insertdata=array_map('trim',$insertdata);
                $CreditnoteID = $this->Credit_note->Add($insertdata);
                
                if ($CreditnoteID) {
                    $this->general_model->updateTransactionPrefixLastNoByType(3,$CHANNELID,$MEMBERID);

                    if($creditnotetype==0){
                        $this->load->model('Extra_charges_model', 'Extra_charges');
                        $transactionproductdata = $this->Credit_note->getInvoiceProductsByIDOrMemberID($buyermemberid,implode(",",$invoiceid));

                        $insertcreditnoteproduct=array();
                        if(!empty($transactionproductsidarr)){
                            foreach($transactionproductsidarr as $key=>$traproductid){
                            
                                if(!empty($traproductid)){
                                
                                    $creditqty = (!empty($creditqtyarr[$traproductid]))?$creditqtyarr[$traproductid]:'';
                                    $creditpercent = (!empty($creditpercentarr[$traproductid]))?$creditpercentarr[$traproductid]:'';
                                    $creditamount = (!empty($creditamountarr[$traproductid]))?$creditamountarr[$traproductid]:'';
                                    $stockqty = (!empty($productstockqtyarr[$traproductid]))?$productstockqtyarr[$traproductid]:'';
                                    $rejectqty = (!empty($productrejectqtyarr[$traproductid]))?$productrejectqtyarr[$traproductid]:'';
                                
                                    if($traproductid == $transactionproductdata[$key]['transactionproductsid'] && !empty($creditqty) && !empty($creditamount)){
                                    
                                        $insertcreditnoteproduct[] = array("creditnoteid"=>$CreditnoteID,
                                                "transactionproductsid"=>$traproductid,
                                                "creditqty"=>$creditqty,
                                                "creditpercent"=>$creditpercent,
                                                "creditamount"=>$creditamount,
                                                "productstockqty"=>$stockqty,
                                                "productrejectqty"=>$rejectqty,
                                            );
                                    }
                                }
                            }
                            if(!empty($insertcreditnoteproduct)){
                                $this->Credit_note->_table = tbl_creditnoteproducts;
                                $this->Credit_note->Add_batch($insertcreditnoteproduct);
                            }
                        }
                    
                        if(!empty($extrachargesidarr)){
                            $insertextracharges = array();
                            foreach($extrachargesidarr as $key=>$extrachargesid){
                                
                                if($extrachargesid > 0){
                                    
                                    $extrachargesname = trim($extrachargesnamearr[$key]);
                                    $extrachargestax = trim($extrachargestaxarr[$key]);
                                    $extrachargeamount = trim($extrachargeamountarr[$key]);
                                    $extrachargepercentage = trim($extrachargepercentagearr[$key]);

                                    if($extrachargeamount > 0){

                                        $insertextracharges[] = array("type"=>3,
                                                                "referenceid" => $CreditnoteID,
                                                                "extrachargesid" => $extrachargesid,
                                                                "extrachargesname" => $extrachargesname,
                                                                "taxamount" => $extrachargestax,
                                                                "amount" => $extrachargeamount,
                                                                "extrachargepercentage" => $extrachargepercentage,
                                                                "createddate" => $createddate,
                                                                "addedby" => $addedby 
                                                            );
                                    }
                                }
                            }
                            
                            if(!empty($insertextracharges)){
                                $this->Extra_charges->_table = tbl_extrachargemapping;
                                $this->Extra_charges->add_batch($insertextracharges);
                            }
                        }

                        if(!empty($invoiceextrachargesidarr)){
                            $insertcreditnoteinvoice = array();
                            foreach($invoiceextrachargesidarr as $invoiceid=>$creditinvoice){
                                if($invoiceid > 0){
                                    foreach($creditinvoice as $key=>$extrachargesid){
                                        if($extrachargesid > 0){
                                            
                                            $extrachargesname = trim($invoiceextrachargesnamearr[$invoiceid][$key]);
                                            $extrachargestax = trim($invoiceextrachargestaxarr[$invoiceid][$key]);
                                            $extrachargeamount = trim($invoiceextrachargeamountarr[$invoiceid][$key]);
                                            $extrachargepercentage = trim($invoiceextrachargepercentagearr[$invoiceid][$key]);

                                            if($extrachargeamount > 0){

                                                $insertcreditnoteinvoice[] = array(
                                                                        "transactiontype" => 1,
                                                                        "transactionid" => $CreditnoteID,
                                                                        "referenceid" => $invoiceid,
                                                                        "extrachargesid" => $extrachargesid,
                                                                        "extrachargesname" => $extrachargesname,
                                                                        "taxamount" => $extrachargestax,
                                                                        "amount" => $extrachargeamount,
                                                                        "extrachargepercentage" => $extrachargepercentage
                                                                    );
                                            }
                                        }
                                    }
                                }
                            }
                            
                            if(!empty($insertcreditnoteinvoice)){
                                $this->Credit_note->_table = tbl_transactionextracharges;
                                $this->Credit_note->add_batch($insertcreditnoteinvoice);
                            }
                        }

                        if(!empty($invoiceidarr)){
                            $insertcreditnotediscount = array();
                            foreach($invoiceidarr as $invoiceid){

                                $orderdiscountpercent = (!empty($invoicediscountpercentarr[$invoiceid]))?$invoicediscountpercentarr[$invoiceid]:0;
                                $orderdiscountamount = (!empty($invoicediscountamountarr[$invoiceid]))?$invoicediscountamountarr[$invoiceid]:0;
                            
                                if($orderdiscountamount > 0){

                                    $insertcreditnotediscount[] = array(
                                                            "transactiontype" => 1,
                                                            "transactionid" => $CreditnoteID,
                                                            "referenceid" => $invoiceid,
                                                            "discountpercentage" => $orderdiscountpercent,
                                                            "discountamount" => $orderdiscountamount
                                                        );
                                }
                            }
                            if(!empty($insertcreditnotediscount)){
                                $this->Credit_note->_table = tbl_transactiondiscount;
                                $this->Credit_note->add_batch($insertcreditnotediscount);
                            }
                        }
                    }else{
                        $creditnoteamountarr = $PostData['creditnoteamount']; //without tax
                        $creditnotetaxarr = $PostData['creditnotetax'];
                        $creditnotedetailarr = $PostData['creditnotedetail'];

                        if(!empty($creditnoteamountarr)){
                            $insertData = array();
                            foreach($creditnoteamountarr as $k=>$creditnoteamount){
                                if(!empty($creditnoteamount) && $creditnotedetailarr[$k] != ""){
                                    
                                    $insertData[] = array("creditnoteid"=>$CreditnoteID,
                                        "creditnotedetails"=>$creditnotedetailarr[$k],
                                        "tax"=>$creditnotetaxarr[$k],
                                        "amount"=>$creditnoteamount
                                    ); 
                                }
                            }
                            if(!empty($insertData)){
                                $this->Credit_note->_table = tbl_creditnoteofferdetails;
                                $this->Credit_note->add_batch($insertData);
                            }
                        }

                        if(REWARDSPOINTS==1){
                            $this->load->model('Reward_point_history_model','RewardPointHistory'); 
                          
                            $memberpoint = $PostData['redeempoints'];
                            $memberpointrate = $PostData['redeempointsrate'];
            
                            if($memberpoint>0){
                                $transactiontype=array_search('Redeem points',$this->Pointtransactiontype);
                                $insertData = array(
                                    "frommemberid"=>$buyermemberid,
                                    "tomemberid"=>$MEMBERID,
                                    "point"=>$memberpoint,
                                    "rate"=>$memberpointrate,
                                    "detail"=>REDEEM_POINTS_ON_TARGET_OFFER,
                                    "type"=>1,
                                    "transactiontype"=>$transactiontype,
                                    "createddate"=>$createddate,
                                    "addedby"=>$addedby
                                );
                                
                                $redeemrewardpointhistoryid = $this->RewardPointHistory->add($insertData);
                
                                $updateData = array(
                                    "redeemrewardpointhistoryid"=>$redeemrewardpointhistoryid,
                                    "modifieddate"=>$createddate,
                                    "modifiedby"=>$addedby
                                );
                                $this->Credit_note->_table = tbl_creditnote;
                                $this->Credit_note->_where = array("id"=>$CreditnoteID);
                                $this->Credit_note->Edit($updateData);
                               
                            }
                        }
                    }

                    echo json_encode(array("error"=>"1","creditnoteid"=>$CreditnoteID));
                }else{
                    echo json_encode(array("error"=>"0"));
                }
            }else{
                echo json_encode(array("error"=>"0"));
            }
        }else{
            echo json_encode(array("error"=>"1"));
        }

    }
    public function printCreditNote(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $creditnoteid = $PostData['id'];
        $PostData['transactiondata'] = $this->Credit_note->getCreditNoteDetails($creditnoteid);

        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $PostData['printtype'] = 'creditnote';
        $PostData['heading'] = 'Credit Note';
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."credit_note/Printcreditnoteformat.php",$PostData,true);
        
        echo json_encode($html); 
    }
    public function update_status(){
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $CredinoteId = $PostData['CredinoteId'];
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==1){
            $updateData['delivereddate'] = $this->general_model->getCurrentDateTime();
        }
        if($status==2){
            $updateData['cancelreason'] = $PostData['resonforcancellation'];
            $updateData['cancelledby'] = $modifiedby;
        }
        
        $this->Credit_note->_where = array("id" => $CredinoteId);
        $update = $this->Credit_note->Edit($updateData);
        if($update) {
            echo 1;    
        }else{
            echo 0;
        }
    }
    /* public function view_credit_note() {

        $this->viewData['title'] = "View Order Credit Note";
        $this->viewData['module'] = "credit_note/View_order_credit_note";
        
        $companyname = $this->Order->getCompanyName();
        $this->viewData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForOrderBySeller($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');
        
        $this->load->model('Order_model', 'Order');
        if($this->uri->segment(4)=="order" && $this->uri->segment(5)!=""){

            $orderid = $this->uri->segment(5);
            $this->Order->_fields = "memberid,sellermemberid,status,approved";
            $this->Order->_where = array("id"=>$orderid);
            $OrderData = $this->Order->getRecordsById();
           
            if(empty($OrderData) || $OrderData['status']!=1 || $OrderData['approved']==0 || $OrderData['sellermemberid']!=$MEMBERID){
                redirect('Pagenotfound');
            }
            $this->viewData['memberid'] = $OrderData['memberid']; 
            $this->viewData['orderid'] = $orderid; 
            $this->viewData['action'] = "1";

        }else if($this->uri->segment(4)!="" && $this->uri->segment(5)==""){
            
            $creditnoteid = $this->uri->segment(4);
            $this->Credit_note->_fields = "buyermemberid as memberid,sellermemberid,orderid,status";
            $this->Credit_note->_where = array("id"=>$creditnoteid);
            $CreditnoteData = $this->Credit_note->getRecordsById();
            
            if(empty($CreditnoteData) || $CreditnoteData['status']!=0 || $CreditnoteData['sellermemberid']!=$MEMBERID){
                redirect('Pagenotfound');
            }
            $this->viewData['creditnoteid'] = $creditnoteid; 
            $this->viewData['memberid'] = $CreditnoteData['memberid']; 
            $this->viewData['orderid'] = $CreditnoteData['orderid']; 
            $this->viewData['action'] = "1";
        }
        //echo $this->uri->segment(5)."<pre>"; print_r($this->viewData); exit;
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("invoice", "pages/add_credit_note.js");
        
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }
   
    public function edit_credit_note() {
        $PostData = $this->input->post();

        //print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $sellermemberid = $this->session->userdata(base_url() . 'MEMBERID');
        $creditnoteid = $PostData['creditnoteid'];
        $buyermemberid = $PostData['oldmemberid'];
        $orderid = isset($PostData['oldorderid'])?explode(",",$PostData['oldorderid']):'';
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');

        $orderidarr = isset($PostData['orderidarr'])?$PostData['orderidarr']:'';
        $orderproductid = isset($PostData['orderproductid'])?$PostData['orderproductid']:'';
        $totalorderamount = $PostData['totalorderamount'];

        $orderidsarr=array();
        if(!empty($orderidarr)){
            foreach($orderid as $OrderId){
                if(in_array($OrderId, $orderidarr)){
                    $orderidsarr[] = $OrderId;
                } 
            }
        }
        $ordersid = implode(",", $orderidsarr);
        if(!empty($ordersid)){
            $updatedata = array("totalamount" => $totalorderamount,
                              );
            
          
            $this->Credit_note->_where = array("id"=>$creditnoteid);
            $this->Credit_note->Edit($updatedata);
                
            $this->Credit_note->_fields = "creditnotenumber";
            $this->Credit_note->_where = "id=".$creditnoteid;
            $Creditnotenumber = $this->Credit_note->getRecordsByID();
            $PostData['creditnotenumber'] = $Creditnotenumber['creditnotenumber'];
            
            $orderproductdata = $this->Credit_note->getOrderProductsByOrderIDOrMemberID($buyermemberid,implode(",",$orderid),$creditnoteid);
            $insertcreditnoteproduct=$updatecreditnoteproduct=$removecreditnoteproduct=array();
          
            for ($i=0; $i < count($orderproductdata); $i++) {

                $transactionproductsid = $orderproductdata[$i]['orderproductsid'];
                $creditnoteproductsid = $orderproductdata[$i]['creditnoteproductsid'];
                
                if(!empty($PostData['creditqty'.$transactionproductsid]) && !empty($PostData['creditamount'.$transactionproductsid]) && !empty($PostData['creditpercent'.$transactionproductsid])){

                    if($transactionproductsid == $PostData['orderproductid'][$i]){

                        $creditqty = $PostData['creditqty'.$transactionproductsid];
                        $creditpercent = $PostData['creditpercent'.$transactionproductsid];
                        $creditamount = $PostData['creditamount'.$transactionproductsid];
                        $productstockqty = $PostData['productstockqty'.$transactionproductsid];
                        $productrejectqty = $PostData['productrejectqty'.$transactionproductsid];
                        

                        if(isset($creditnoteproductsid) && $creditnoteproductsid!=''){

                            $updatecreditnoteproduct[] = array("id"=>$creditnoteproductsid,
                                                            "creditqty"=>$creditqty,
                                                            "creditpercent"=>$creditpercent,
                                                            "creditamount"=>$creditamount,
                                                            "productstockqty"=>$productstockqty,
                                                            "productrejectqty"=>$productrejectqty,
                                                        );
                        }else{
                            
                            $insertcreditnoteproduct[] = array("creditnoteid"=>$creditnoteid,
                                                            "transactionproductsid"=>$transactionproductsid,
                                                            "creditqty"=>$creditqty,
                                                            "creditpercent"=>$creditpercent,
                                                            "creditamount"=>$creditamount,
                                                            "productstockqty"=>$productstockqty,
                                                            "productrejectqty"=>$productrejectqty,
                                                        );
                        }
                    }
                }else{
                    if(isset($creditnoteproductsid) && $creditnoteproductsid!=''){
                        $removecreditnoteproduct[] = $creditnoteproductsid;
                    }
                }
            }
            if(!empty($insertcreditnoteproduct)){
                $this->Credit_note->_table = tbl_creditnoteproducts;
                $this->Credit_note->Add_batch($insertcreditnoteproduct);
            }
            if(!empty($updatecreditnoteproduct)){
                $this->Credit_note->_table = tbl_creditnoteproducts;
                $this->Credit_note->Edit_batch($updatecreditnoteproduct, "id");
            }
            if(!empty($removecreditnoteproduct)){
                foreach ($removecreditnoteproduct as $creditnoteproductsid) {
                    $this->Credit_note->_table = tbl_creditnoteproducts;
                    $this->Credit_note->Delete(array("id"=>$creditnoteproductsid));
                }
            }

            $this->Credit_note->_table = tbl_creditnote;
            $this->Credit_note->regeneratecreditnote($creditnoteid);

            echo json_encode(array("error"=>"1","creditnotenumber"=>$PostData['creditnotenumber']));
        }else{
            echo json_encode(array("error"=>"1"));
        }
    }

    public function regeneratecreditnote() {
        $PostData = $this->input->post();
        
        $creditnoteid = $PostData['creditnoteid'];
        echo $this->Credit_note->regeneratecreditnote($creditnoteid);
    } */
}