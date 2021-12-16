<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_credit_note extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Purchase_credit_note');
        $this->load->model('Purchase_credit_note_model', 'Purchase_credit_note');
        $this->load->model('Vendor_model', 'Vendor');
    }
    public function index() {
        $this->viewData['title'] = "Purchase Credit Note";
        $this->viewData['module'] = "purchase_credit_note/Purchase_credit_note";
        $this->viewData['vendordata'] = $this->Vendor->getVendorByPurchaseCreditNote();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Credit Note','View purchase credit note.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Purchase_credit_note", "pages/purchase_credit_note.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('onlyvendor');
        
        $list = $this->Purchase_credit_note->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = $invoiceno_text = array();
            $channellabel = $dropdownmenu = $Actions = $creditnotestatus = '';
            $status = $datarow->status;
            $invoiceIdArr = explode(",",$datarow->invoiceid);
            $invoiceNumberArr = explode(",",$datarow->invoiceno);

            if(!empty($invoiceNumberArr)){
                foreach($invoiceNumberArr as $key=>$invoiceNumber){
                    $invoiceid = $invoiceIdArr[$key];
                    $invoiceno_text[] = "<a href='".ADMIN_URL."purchase-invoice/view-purchase-invoice/". $invoiceid."/"."' title='".$invoiceNumber."' target='_blank'>".$invoiceNumber."</a>";
                }
            }

            $row[] = ++$counter;

            if($datarow->vendorchannelid != 0){
                $key = array_search($datarow->vendorchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'vendor/vendor-detail/'.$datarow->vendorid.'" target="_blank" title="'.$datarow->vendorname.'">'.ucwords($datarow->vendorname).' ('.$datarow->vendorcode.')'."</a>";
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
            $creditnotestatus = '<div class="dropdown">'.$dropdownmenu.'</div>';
        
            $Actions .= '<a href="'.ADMIN_URL.'purchase-credit-note/view-purchase-credit-note/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            if(in_array('print', $additionalrights)) {
                $Actions .= '<a href="javascript:void(0)" onclick="printCreditNote('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  
            }

            $row[] = implode(", ",$invoiceno_text);
            $row[] = $datarow->creditnotenumber;
            $row[] = $this->general_model->displaydate($datarow->creditnotedate);
            $row[] = $creditnotestatus;         
            $row[] = number_format(round($datarow->netamount),'2','.',',');
            
            $row[] = $Actions;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Purchase_credit_note->count_all(),
                        "recordsFiltered" => $this->Purchase_credit_note->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function purchase_credit_note_add($invoiceid="") {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Purchase Credit Note";
        $this->viewData['module'] = "purchase_credit_note/Add_purchase_credit_note";
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData();
 
        if($invoiceid!="" && $invoiceid > 0){
            
            $this->load->model('Purchase_invoice_model', 'Purchase_invoice');
            $this->Purchase_invoice->_fields = "sellermemberid as vendorid";
            $this->Purchase_invoice->_where = array("id"=>$invoiceid);
            $InvoiceData = $this->Purchase_invoice->getRecordsById();
    
            $this->viewData['vendorid'] = $InvoiceData['vendorid']; 
            $this->viewData['invoiceid'] = $invoiceid; 
            $this->viewData['action'] = "0";

        }

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->viewData['creditnotenumber'] = $this->general_model->generateTransactionPrefixByType(8);
        
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_purchase_credit_note", "pages/add_purchase_credit_note.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function view_purchase_credit_note($id) {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Purchase Credit Note";
        $this->viewData['module'] = "purchase_credit_note/View_purchase_credit_note";
        $this->viewData['printtype'] = "creditnote";

        $this->viewData['transactiondata'] = $this->Purchase_credit_note->getCreditNoteDetails($id);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $this->viewData['printtype'] = 'creditnote';
        $this->viewData['heading'] = 'Purchase Credit Note';
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Credit Note','View '.$this->viewData['transactiondata']['transactiondetail']['creditnoteno'].' purchase credit note details.');
        }

        $this->admin_headerlib->add_javascript("view_purchase_credit_note", "pages/view_purchase_credit_note.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function getTransactionProducts() {

        $this->load->model('Purchase_invoice_model', 'Purchase_invoice');
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        $invoiceid = $PostData['invoiceid'];
        $creditnoteid = $PostData['creditnoteid'];
        $invoiceproductdata = $this->Purchase_credit_note->getInvoiceProductsByIDOrVendorID($vendorid,$invoiceid,$creditnoteid);
        $invoicedata = $this->Purchase_invoice->getInvoiceAmountDataByID($invoiceid);
        $gstpricearray = !empty($invoiceproductdata)?array_column($invoiceproductdata, 'gstprice'):array();

        $json['gstprice'] = in_array("1", $gstpricearray)?1:0;
        $json['invoiceproducts'] = $invoiceproductdata;
        $json['invoiceamountdata'] = $invoicedata;
        
        echo json_encode($json);
    }
    public function add_purchase_credit_note() {
        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $vendorid = (!empty($PostData['vendorid']))?$PostData['vendorid']:$PostData['oldvendorid'];
        $invoiceid = isset($PostData['invoiceid'])?$PostData['invoiceid']:'';
        $creditnoteno = $PostData['creditnoteno'];
        $iseditedcreditnoteno = isset($PostData['editcreditnotenumber'])?1:0;
        $billingaddressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $creditnotedate = (!empty($PostData['creditnotedate']))?$this->general_model->convertdate($PostData['creditnotedate']):"";
        $remarks = $PostData['remarks'];

        $invoiceidarr = isset($PostData['invoiceidarr'])?$PostData['invoiceidarr']:'';
        $transactionproductsidarr = isset($PostData['transactionproductsid'])?$PostData['transactionproductsid']:'';
        $referenceproductidarr = (!empty($PostData['referenceproductid']))?$PostData['referenceproductid']:'';
        $inputtotalpayableamount = $PostData['inputtotalpayableamount'];
        
        $producttotal = $PostData['inputproducttotal'];
        $gsttotal = $PostData['inputgsttotal'];
        $globaldiscount = $PostData['inputovdiscamnt'];
       
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

        $this->Purchase_credit_note->_where = array("creditnotenumber"=>$creditnoteno);
        $Count = $this->Purchase_credit_note->CountRecords();
        if($Count==0){

            $invoiceidsarr=array();
            if(!empty($invoiceidarr)){
                foreach($invoiceid as $InvoiceId){
                    if(in_array($InvoiceId, $invoiceidarr)){
                        $invoiceidsarr[] = $InvoiceId;
                    } 
                }
            }
            $invoiceids = implode(",", $invoiceidsarr);
            if(!empty($invoiceids)){
                
                $insertdata = array("sellermemberid" => $vendorid,
                                    "buyermemberid" => 0,
                                    "invoiceid" => $invoiceids,
                                    "creditnotenumber" => $creditnoteno,
                                    "addressid" => $billingaddressid,
                                    "shippingaddressid" => $shippingaddressid,
                                    "billingaddress" => $billingaddress,
                                    "shippingaddress" => $shippingaddress,
                                    "creditnotedate" => $creditnotedate,
                                    "remarks" => $remarks,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "globaldiscount" => $globaldiscount,
                                    "status" => 0,
                                    "type" => 0,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby);
                
                $this->writedb->set($insertdata);
                // $this->writedb->set('creditnotenumber',"(SELECT IFNULL(max(c.creditnotenumber)+1,100001) as creditnotenumber from ".tbl_creditnote." as c)",FALSE);
                $this->writedb->insert(tbl_creditnote);
                $CreditnoteID = $this->writedb->insert_id();
                
                if ($CreditnoteID) {
                    
                    if($iseditedcreditnoteno==0){
                        $this->general_model->updateTransactionPrefixLastNoByType(8);
                    }
                    $this->load->model('Extra_charges_model', 'Extra_charges');
                    $transactionproductdata = $this->Purchase_credit_note->getInvoiceProductsByIDOrVendorID($vendorid,implode(",",$invoiceid));

                    $insertcreditnoteproduct = $inserttransactionproductstock = $inserttransactionproductscrap = array();
                    if(!empty($transactionproductsidarr)){
                        foreach($transactionproductsidarr as $key=>$traproductid){
                        
                            $trkey = array_search($traproductid, array_column($transactionproductdata, 'transactionproductsid'));

                            $iscreditcheck = isset($PostData['creditcheck'.$traproductid])?1:0;

                            if($iscreditcheck==1 && !empty($traproductid)){

                                $creditqty = (!empty($PostData['creditqty'][$traproductid]))?$PostData['creditqty'][$traproductid]:'';
                                $creditpercent = (!empty($PostData['creditpercent'][$traproductid]))?$PostData['creditpercent'][$traproductid]:'';
                                $creditamount = (!empty($PostData['creditamount'][$traproductid]))?$PostData['creditamount'][$traproductid]:'';
                                // $stockqty = (!empty($PostData['productstockqty'][$traproductid]))?$PostData['productstockqty'][$traproductid]:'';
                                // $rejectqty = (!empty($PostData['productrejectqty'][$traproductid]))?$PostData['productrejectqty'][$traproductid]:'';
                            
                                $stockidsarray = (!empty($PostData['stockids'][$traproductid]))?$PostData['stockids'][$traproductid]:'';
                                
                                $stockqtysarray = (!empty($PostData['stockqtys'][$traproductid]))?$PostData['stockqtys'][$traproductid]:'';
                                $stockqty = array_sum($stockqtysarray);

                                $scrapqtysarray = (!empty($PostData['scrapqtys'][$traproductid]))?$PostData['scrapqtys'][$traproductid]:'';
                                $rejectqty = array_sum($scrapqtysarray);
                                
                                $referenceproductid = $referenceproductidarr[$key];
                                 
                                if($traproductid == $transactionproductdata[$trkey]['transactionproductsid'] && !empty($creditqty) && !empty($creditamount)){
                                
                                    $insertcreditnoteproduct = array("creditnoteid"=>$CreditnoteID,
                                            "transactionproductsid"=>$traproductid,
                                            "creditqty"=>$creditqty,
                                            "creditpercent"=>$creditpercent,
                                            "creditamount"=>$creditamount,
                                            "productstockqty"=>$stockqty,
                                            "productrejectqty"=>$rejectqty,
                                        );
                                    $this->Purchase_credit_note->_table = tbl_creditnoteproducts;
                                    $creditnoteproductsID = $this->Purchase_credit_note->Add($insertcreditnoteproduct);

                                    if(!empty($stockqtysarray)){
                                        foreach($stockqtysarray as $k=>$stockqtys){
                                            
                                            if($stockqtys > 0){
                                                
                                                $inserttransactionproductstock[] = array(
                                                    "referencetype"=>4,
                                                    "referenceid"=>$creditnoteproductsID,
                                                    "stocktype"=>0,
                                                    "stocktypeid"=>$referenceproductid,
                                                    "productid"=>$transactionproductdata[$trkey]['productid'],
                                                    "priceid"=>$transactionproductdata[$trkey]['productpriceid'],
                                                    "qty"=>$stockqtys,
                                                    "action"=>1,
                                                    "createddate"=>$creditnotedate,
                                                    "modifieddate"=>$createddate
                                                );
                                            }

                                            if($scrapqtysarray[$k] > 0){
                                                 
                                                $inserttransactionproductscrap[] = array(
                                                    "referencetype"=>4,
                                                    "referenceid"=>$creditnoteproductsID,
                                                    "stocktype"=>0,
                                                    "stocktypeid"=>$referenceproductid,
                                                    "productid"=>$transactionproductdata[$trkey]['productid'],
                                                    "priceid"=>$transactionproductdata[$trkey]['productpriceid'],
                                                    "qty"=>$scrapqtysarray[$k],
                                                    "scraptype"=>1, //1=rejection, 2=wastage, 3=lost
                                                    "unitid"=>$transactionproductdata[$trkey]['unitid'],
                                                    "action"=>0,
                                                    "createddate"=>$createddate
                                                );
                                            }

                                        }
                                    }
                                }
                            }
                        }
                        /* if(!empty($insertcreditnoteproduct)){
                            $this->Purchase_credit_note->_table = tbl_creditnoteproducts;
                            $this->Purchase_credit_note->Add_batch($insertcreditnoteproduct);
                        } */
                        if(!empty($inserttransactionproductstock)){
                            $this->Purchase_credit_note->_table = tbl_transactionproductstockmapping;
                            $this->Purchase_credit_note->Add_batch($inserttransactionproductstock);
                        }
                        if(!empty($inserttransactionproductscrap)){
                            $this->Purchase_credit_note->_table = tbl_transactionproductscrapmapping;
                            $this->Purchase_credit_note->Add_batch($inserttransactionproductscrap);
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
                            $this->Purchase_credit_note->_table = tbl_transactionextracharges;
                            $this->Purchase_credit_note->add_batch($insertcreditnoteinvoice);
                        }
                    }

                    if(!empty($invoiceidsarr)){
                        $insertcreditnotediscount = array();
                        foreach($invoiceidsarr as $invoiceid){

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
                            $this->Purchase_credit_note->_table = tbl_transactiondiscount;
                            $this->Purchase_credit_note->add_batch($insertcreditnotediscount);
                        }
                    }

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->Purchase_credit_note->_table = tbl_creditnote;
                        $this->Purchase_credit_note->_fields = 'creditnotenumber';
                        $this->Purchase_credit_note->_where = array("id"=>$CreditnoteID);
                        $creditnotedata = $this->Purchase_credit_note->getRecordsById();

                        $this->general_model->addActionLog(1,'Credit Note','Add new '.$creditnotedata['creditnotenumber'].' purchase credit note.');
                    }
                    echo json_encode(array("error"=>"1","creditnoteid"=>$CreditnoteID));
                }else{
                    echo json_encode(array("error"=>"0"));
                }
            }else{
                echo json_encode(array("error"=>"1"));
            }
        }else{
            echo json_encode(array('error'=>2));
        }
    }
    public function printPurchaseCreditNote(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $creditnoteid = $PostData['id'];
        $PostData['transactiondata'] = $this->Purchase_credit_note->getCreditNoteDetails($creditnoteid);

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = 'creditnote';
        $PostData['heading'] = 'Purchase Credit Note';
        $PostData['hideonprint'] = '1';

        $html['content'] = $this->load->view(ADMINFOLDER."purchase_credit_note/Printpurchasecreditnoteformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Credit Note','Print '.$PostData['transactiondata']['transactiondetail']['creditnoteno'].' purchase credit note details.');
        }

        echo json_encode($html); 
    }
    public function update_status(){
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $CredinoteId = $PostData['CredinoteId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
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
        
        $this->Purchase_credit_note->_where = array("id" => $CredinoteId);
        $IsUpdate = $this->Purchase_credit_note->Edit($updateData);
        if($IsUpdate) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Purchase_credit_note->_fields="creditnotenumber";
                $this->Purchase_credit_note->_where=array("id"=>$CredinoteId);
                $creditnotedata = $this->Purchase_credit_note->getRecordsByID();

                $this->general_model->addActionLog(2,'Credit Note','Change status '.$creditnotedata['creditnotenumber'].' on purchase credit note.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
}