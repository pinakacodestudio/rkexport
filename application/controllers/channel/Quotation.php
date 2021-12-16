<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Quotation_model', 'Quotation');
        //$this->load->model('Product_file_model', 'Product_file');
        
        //$this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Quotation');
    }
    public function index() {
        // echo channel_quotation;exit;
        $this->viewData['title'] = "Quotation";
        $this->viewData['module'] = "quotation/Quotation";
        $this->viewData['VIEW_STATUS'] = "1";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        $this->load->model("Member_model","Member"); 
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForQuotationBySeller($MEMBERID,$CHANNELID,'');

        //$this->viewData['memberdata'] = $this->Member->getMemberListInUnderChannel($MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("Quotation", "pages/quotation.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();

        $list = $this->Quotation->get_datatables();
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Order_model","Order"); 
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $companyname = $this->Order->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            $channellabel = '';
            $status = $datarow->status;
            $menu = '';
            $approvestatus = '';
            $finalprice = "<span class='pull-right'>".number_format(($datarow->netamount), 2, '.', ',')."</span>";

            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }

            if($status == 0){
                if(in_array($rollid, $edit) && channel_quotation=="1") {
                    $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'quotation/quotation-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
            }
            if($status == 0){
                if(channel_quotation=="1"){
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                  <li id="dropdown-menu">
                                    <a onclick="chagequotationstatus(1,'.$datarow->id.',&quot;'.$datarow->quotationid.'&quot;,&quot;'.$datarow->membername.'&quot;)">Approve</a>
                                  </li>
                                  <li id="dropdown-menu">
                                    <a onclick="chagequotationstatus(2,'.$datarow->id.',&quot;'.$datarow->quotationid.'&quot;)">Rejected</a>
                                  </li>
                                  <li id="dropdown-menu">
                                    <a onclick="chagequotationstatus(3,'.$datarow->id.',&quot;'.$datarow->quotationid.'&quot;)">Cancel</a>
                                  </li>
                              </ul>';
                }else{
                    $dropdownmenu = '<span class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</span>';
                }
            }else if($status == 1){
                if(channel_quotation=="1"){
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approve <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chagequotationstatus(2,'.$datarow->id.',&quot;'.$datarow->quotationid.'&quot;)">Rejected</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="chagequotationstatus(3,'.$datarow->id.',&quot;'.$datarow->quotationid.'&quot;)">Cancel</a>
                                </li>
                          </ul>';
                }else{
                    $dropdownmenu = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approve</span>';
                }
            }else if($status == 2){
                if(channel_quotation=="1"){
                    /* $dropdownmenu = '<button class="btn btn-danger btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Rejected <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chagequotationstatus(3,'.$datarow->id.','.$datarow->quotationid.')">Cancel</a>
                                </li>
                          </ul>'; */
                    $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</span>';
                }else{
                    $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</span>';
                }
            }else if($status == 3){
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $quotationstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            $actions .= '<a href="'.CHANNEL_URL.'quotation/view-quotation/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';
            
            $actions .= '<a href="javascript:void(0)" onclick="printquotationinvoice('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';    
           /*  if(file_exists(QUOTATION_PATH.$companyname.'-'.$datarow->quotationid.'.pdf')){
                $actions .= '<a href="'.QUOTATION.$companyname.'-'.$datarow->quotationid.'.pdf" class="'.viewquotation_class.'" title="'.viewquotation_title.'" target="_blank">'.viewquotation_text.'</a>'; 
            } */

            /* $actions .= '<a href="javascript:void(0);" class="'.regeneratequotation_class.'" title="'.regeneratequotation_title.'" onclick="regeneratequotation('.$datarow->id.')">'.regeneratequotation_text.'</a>'; */ 

            if($status==1){
                $actions .= '<a href="'.CHANNEL_URL.'order/order-add/'.$datarow->id.'" class="btn btn-sm btn-raised btn-primary" title="Add Order"><i class="fa fa-plus"></i></a>';
            }
            $actions .= '<a class="'.duplicatebtn_class.'" href="'.CHANNEL_URL.'quotation/quotation-add/'. $datarow->id.'/'.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';
            
            $actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',1)" title="'.sendmail_title.'">'.sendmail_text.'</a>';

            // $actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',1,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            if($datarow->whatsappno!=''){
                $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" target="_blank" href="https://api.whatsapp.com/send?phone='.$datarow->whatsappno.'&text=" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }else{
                $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" href="javascript:void(0)" onclick="checkwhatsappnumber('. $datarow->id .')" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            }

            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.ucwords($datarow->membername).' ('.$datarow->membercode.')</a>';
            $row[] = '<a href="'.CHANNEL_URL.'quotation/view-quotation/'.$datarow->id.'" title="'.viewpdf_title.'" target="_blank">'.$datarow->quotationid.'</a>';
            $row[] = ($datarow->quotationdate!="0000-00-00")?$this->general_model->displaydate($datarow->quotationdate):'';
            $row[] = $quotationstatus;            
            $row[] =  "<span class='pull-right'>".number_format(($datarow->netamount), 2, '.', ',')."</span>";

            if($datarow->addedbychannelid!=0){
                $key = array_search($datarow->addedbychannelid, array_column($channeldata, 'id'));
                $channellabel="";
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel." ".ucwords($datarow->addedby).' ('.$datarow->addedbycode.')';
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Quotation->count_all(),
                        "recordsFiltered" => $this->Quotation->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function regeneratequotation(){
        $PostData = $this->input->post();
        
        $quotationid = $PostData['quotationid'];
        echo $this->Quotation->generatequotation($quotationid);
    }
    public function quotation_add($id="") {
        
        if(channel_quotation!="1"){
            redirect("Pagenotfound");
        }
        $this->viewData['title'] = "Add Quotation";
        $this->viewData['module'] = "quotation/Add_quotation";
        $this->viewData['VIEW_STATUS'] = "1";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        
        if($id!=""){
            /* Add Duplicate Quotation */
            $this->viewData['quotationdata'] = $this->Quotation->getQuotationDataById($id,'sales');
            $this->viewData['installmentdata'] = $this->Quotation->getQuotationInstallmentDataByQuotationId($id);
            $this->viewData['ExtraChargesData'] = $this->Quotation->getExtraChargesDataByReferenceID($id);
            $this->viewData['isduplicate'] = "1";
        }

        if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
            $this->viewData['multiplememberchannel'] = "1";
        } 
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForQuotationBySeller($MEMBERID,$CHANNELID,'concatnameoremail');
        
        $this->load->model('Channel_model', 'Channel');
        $this->Channel->_fields = "id,partialpayment";
        $this->Channel->_where = ("id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")");
        $this->viewData['channelsetting'] = $this->Channel->getRecordsById();

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount($MEMBERID);

        // $this->load->model('Category_model', 'Category');
        // $this->viewData['categorydata'] = $this->Category->getProductCategoryList();

        // $this->viewData['quotationid'] = time().$MEMBERID.rand(10,99).rand(10,99);
        $this->viewData['quotationid'] = $this->general_model->generateTransactionPrefixByType(0,$CHANNELID,$MEMBERID);
        
        /* $this->load->model('System_configuration_model', 'System_configuration');
        $discount = $this->System_configuration->getsetting();
        if($discount['discountonbill']==1){
            $startdate = $discount['discountonbillstartdate'];
            $enddate = $discount['discountonbillenddate'];
            $currentdate = $this->general_model->getCurrentDate();
            $this->viewData['gstondiscount']= $discount['gstondiscount'];
            
            if($startdate=='0000-00-00' && $enddate=='0000-00-00'){
                $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                if($discount['discountonbilltype']==1){
                    $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                }else {
                    $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                }
            }else{
                if($currentdate >= $startdate && $currentdate <= $enddate){
                    $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                    if($discount['discountonbilltype']==1){
                        $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                    }else {
                        $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                    }
                }
            }
        } */
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("add_quotation", "pages/add_quotation.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function add_quotation() {
        if(channel_quotation!="1"){
            echo 0;exit;
        }
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model('Stock_report_model', 'Stock');
        $this->load->model("Member_model","Member");

        if(isset($PostData['isduplicate']) && $PostData['isduplicate']==1){
            $PostData['memberid'] = $PostData['oldmemberid'];
        }
        if($PostData['quotationtype']==1){ 
            $memberdata = $this->Member->getmainmember($MEMBERID,"row");
            if(isset($memberdata['id'])){
                $sellermemberid = $memberdata['id'];
                $sellerchannelid = $memberdata['channelid'];
            }else{
                $sellermemberid = $sellerchannelid = 0;
            }
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $addquotationtype = "1";//by_purchaser
        }else{
            $sellermemberid = $this->session->userdata(base_url().'MEMBERID');
            $sellerchannelid = $this->session->userdata(base_url().'CHANNELID');
            $memberid = $PostData['memberid'];
            $addquotationtype = "0";//by_seller
        }

        $this->Member->_fields="name";
        $this->Member->_where = array("id"=>$this->session->userdata(base_url().'MEMBERID'));
        $membername = $this->Member->getRecordsByID();
        
        if($addquotationtype=="1"){
            //$meber_id = $sellermemberid; 
            $meber_id = $MEMBERID; 
        }else{
            //$meber_id = $MEMBERID;
            $meber_id = $PostData['memberid'];
        }
       
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $quotationid = $PostData['quotationid'];
        $quotationdate = ($PostData['quotationdate']!="")?$this->general_model->convertdate($PostData['quotationdate']):'';
        $remarks = $PostData['remarks'];
        $cashorbankid = $PostData['cashorbankid'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];
        
        // $grossamount = $PostData['grossamount'];
        $discountpercentage = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $totalgrossamount = $PostData['totalgrossamount'];
        $netamount = $PostData['netamount'];
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,2-Advance Payment,3-Partial Payment

        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';

        if(!empty($extrachargesidarr)){
            $totalgrossamount = ($totalgrossamount - (array_sum($extrachargeamountarr) - array_sum($extrachargestaxarr)));
            $taxamount = ($taxamount - array_sum($extrachargestaxarr));
            $netamount = ($netamount - array_sum($extrachargeamountarr));
        }else{
            $totalgrossamount = $totalgrossamount;
        }
        
        $productidarr = $PostData['productid'];
        $actualpricearr = $PostData['actualprice'];
        $priceidarr = $PostData['priceid'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $deliverypriority =  $PostData['deliverypriority'];
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

        //$installmentstatus = $PostData['installmentstatus'];
        $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
        $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
        $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
        $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
      
        foreach($productidarr as $index=>$productid){
            $priceid = trim($priceidarr[$index]);
            $actualprice = trim($actualpricearr[$index]);
            $qty = trim($qtyarr[$index]);
            if(isset($discountarr[$index])){
                $discount = trim($discountarr[$index]);
            }else{
                $discount = 0;
            }
            $amount = trim($amountarr[$index]);
            
            if($productid!=0 && $qty!='' && $amount>0){
                
                //CHECK PURCHASE ORDER PRODUCT PRICE MATCH ON DATABASE
                if($addquotationtype==1){
                    $this->load->model('Product_model','Product');
                    $productdata = $this->Product->getVariantByProductId($productid,$memberid,'purchase',$sellermemberid);
                     
                    if(!empty($productdata)){
                        if($priceid==0){
                            $purchaseprice = trim($productdata[0]['actualprice']);
                            $purchasediscount = trim($productdata[0]['discount']);
                        }else{
                            $arrkey = array_search($priceid, array_column($productdata, 'priceid'));
                            $purchaseprice = trim($productdata[$arrkey]['actualprice']);
                            $purchasediscount = trim($productdata[$arrkey]['discount']);
                        }
                        if($actualprice != $purchaseprice){
                            echo "Price does not match of ".($index+1)." product !"; exit;
                        }
                        if($discount != '' && $discount != $purchasediscount){
                            echo "Product discount does not match of ".($index+1)." product !"; exit;
                        }
                    }
                }
            }
        }
        
        $this->Quotation->_table = tbl_quotation;
        $this->Quotation->_where = ("quotationid='".$quotationid."'");
        $Count = $this->Quotation->CountRecords();
        if($Count==0){

            $insertdata = array(
                "memberid" => $memberid,
                "sellermemberid" => $sellermemberid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "remarks" => $remarks,
                "cashorbankid" => $cashorbankid,
                "quotationid" => $quotationid,
                "quotationdate" => $quotationdate,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "quotationamount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountpercentage" => $discountpercentage,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                "addquotationtype" => $addquotationtype,
                'deliverypriority'=>$deliverypriority,
                "type" => 1,
                "gstprice" => PRICE,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" =>$addedby,
                "modifiedby" => $addedby,
                "status" => 0);
            
            $insertdata=array_map('trim',$insertdata);
            $QuotationId = $this->Quotation->Add($insertdata);
            //$QuotationId = 10;
            if($QuotationId){
                $this->general_model->updateTransactionPrefixLastNoByType(0,$sellerchannelid,$sellermemberid);
                if(!empty($productidarr)){

                    $insertData = array();
                    //$productidarr = array_unique($productidarr);
                    $this->load->model('Product_model', 'Product');
                    $this->load->model('Channel_model', 'Channel');

                    $channeldata = $this->Channel->getMemberChannelData($memberid);
		            $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
                            
                    $CheckProduct = $this->Product->getMemberProductCount($memberid);

                    $priceidsarr = array();
                    foreach($productidarr as $index=>$productid){
                        
                        $priceid = trim($priceidarr[$index]);
                        $qty = trim($qtyarr[$index]);
                        $productrate = trim($PostData['productrate'][$index]);
                        $originalprice = trim($PostData['originalprice'][$index]);
                        $tax = (!empty($taxarr))?trim($taxarr[$index]):'';
                        $actualprice = trim($actualpricearr[$index]);
                        $referencetype = !empty($referencetypearr[$index])?$referencetypearr[$index]:"";
                        $combopriceid = !empty($combopriceidarr[$index])?$combopriceidarr[$index]:"";

                        if(isset($discountarr[$index])){
                            $discount = trim($discountarr[$index]);
                        }else{
                            $discount = 0;
                        }
                        $amount = trim($amountarr[$index]);
                        
                        if($productid!=0 && $qty!='' && $amount>0){
                            $product = array();
                            $product = $this->Product->getProductData($meber_id,$productid,$memberbasicsalesprice,1);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            if($addquotationtype==1){
                                $tax = $product['tax'];
                            }
                            $this->Quotation->_table = tbl_quotationproducts;
                            $this->Quotation->_where = ("quotationid=".$QuotationId." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Quotation->CountRecords();
                                
                            if($Count==0){
                               
                                $priceidsarr[] = $priceid;

                                $insertData[] = array("quotationid"=>$QuotationId,
                                        "productid" => $productid,
                                        "quantity" => $qty,
                                        "price" => $productrate,
                                        "originalprice" => $actualprice,
                                        "referencetype" => $referencetype,
                                        "referenceid" => $combopriceid,
                                        "hsncode" => $product['hsncode'],
                                        "tax" => $tax,
                                        "isvariant" => $isvariant,
                                        "discount" => $discount,
                                        "finalprice" => round($amount + ($amount*$tax)/100),
                                        "name" => $product['name']);
                            }
                        }
                    }
                    //print_r($insertData);exit;
                    if(!empty($insertData)){
                        $this->Quotation->_table = tbl_quotationproducts;
                        $this->Quotation->add_batch($insertData);
                        
                        $quotationproductsidsarr=array();
                        $first_id = $this->writedb->insert_id();
                        $last_id = $first_id + (count($insertData)-1);
                        
                        for($id=$first_id;$id<=$last_id;$id++){
                            $quotationproductsidsarr[]=$id;
                        }

                        $this->load->model('Product_combination_model', 'Product_combination');
                        $insertVariantData = array();
                        
                        foreach($quotationproductsidsarr as $k=>$quotationproductsid){

                            $variantdata = $this->Product_combination->getProductcombinationByPriceID($priceidsarr[$k]);
                            
                            foreach($variantdata as $variant){

                                $insertVariantData[] = array("quotationid"=>$QuotationId,
                                                        "priceid" => $priceidsarr[$k],
                                                        "quotationproductid" => $quotationproductsid,
                                                        "variantid" => $variant['variantid'],
                                                        "variantname" => $variant['variantname'],
                                                        "variantvalue" => $variant['variantvalue']);
                            }
                        }
                        if(count($insertVariantData)>0){
                            $this->Quotation->_table = tbl_quotationvariant;
                            $this->Quotation->add_batch($insertVariantData);
                        }
                    }

                    if(!empty($extrachargesidarr)){
                        $insertextracharges = array();
                        foreach($extrachargesidarr as $index=>$extrachargesid){

                            if($extrachargesid > 0){
                                
                                $extrachargesname = trim($extrachargesnamearr[$index]);
                                $extrachargestax = trim($extrachargestaxarr[$index]);
                                $extrachargeamount = trim($extrachargeamountarr[$index]);
                                $extrachargepercentage = trim($extrachargepercentagearr[$index]);
                                if($extrachargeamount > 0){

                                    $insertextracharges[] = array("type"=>1,
                                                            "referenceid" => $QuotationId,
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
                            $this->Quotation->_table = tbl_extrachargemapping;
                            $this->Quotation->add_batch($insertextracharges);
                        }
                    }
                }
                if(!empty($percentagearr) && $paymenttype==4){
                    $insertData_installment = array();

                    foreach($percentagearr as $index=>$percentage){
                        
                        $installmentamount = trim($installmentamountarr[$index]);
                        $installmentdate = $installmentdatearr[$index]!=''?$this->general_model->convertdate(trim($installmentdatearr[$index])):'';
                        $paymentdate = $paymentdatearr[$index]!=''?$this->general_model->convertdate(trim($paymentdatearr[$index])):'';
                       
                        if(isset($PostData['installmentstatus'.($index+1)]) && !empty($PostData['installmentstatus'.($index+1)])){
                            $status=1;
                        }else{
                            $status=0;
                        }
                       
                        $insertData_installment[] = array("quotationid"=>$QuotationId,
                                "percentage" => $percentage,
                                "amount" => $installmentamount,
                                "date" => $installmentdate,
                                "paymentdate" => $paymentdate,
                                "status" => $status,
                                "createddate" => $createddate,
                                "modifieddate" => $createddate,
                                "addedby" => $addedby,
                                "modifiedby"=>$addedby);
                    }
                    if(!empty($insertData_installment)){
                        $this->Quotation->_table = tbl_installment;
                        $this->Quotation->add_batch($insertData_installment);
                    }
                }
                $insertstatusdata = array(
                    "quotationid" => $QuotationId,
                    "status" => 0,
                    "type" => 1,
                    "modifieddate" => $createddate,
                    "modifiedby" => $addedby);
                
                $insertstatusdata=array_map('trim',$insertstatusdata);
                $this->Quotation->_table = tbl_quotationstatuschange;  
                $this->Quotation->Add($insertstatusdata);

                if($addquotationtype==1){
                    $this->Member->_fields="GROUP_CONCAT(id) as memberid";
                    $this->Member->_where = array("(id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR id=".$memberid.")"=>null);
                    $memberdata = $this->Member->getRecordsByID();
                }else{
                    $memberdata['memberid'] = $memberid;
                }
                               
                if(count($memberdata)>0){
                    $this->load->model('Fcm_model','Fcm');
                    $fcmquery = $this->Fcm->getFcmDataByMemberId($memberdata['memberid']);

                    if(!empty($fcmquery)){
                        $insertData = array();
                        foreach ($fcmquery as $fcmrow){ 
                            $fcmarray=array();               
                            
                            if($addquotationtype==1){
                                $type = "12";
                                if($memberid == $fcmrow['memberid']){
                                    $msg = "Dear ".ucwords($fcmrow['membername']).", Your quotation request successfully added".".";
                                }else{
                                    $msg = "Dear ".ucwords($fcmrow['membername']).", New quotation request added from ".ucwords($membername['name']).".";
                                }
                            }else{
                                $type = "13";
                                $msg = "Dear ".ucwords($fcmrow['membername']).", New quotation request added from ".ucwords($membername['name']).".";
                            }
                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$QuotationId.'"}';
                            $fcmarray[] = $fcmrow['fcm'];
                    
                            //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                            $this->Fcm->sendFcmNotification($type,$pushMessage,$fcmrow['memberid'],$fcmarray,0,$fcmrow['devicetype']);
                            
                            $insertData[] = array(
                                'type'=>$type,
                                'message' => $pushMessage,
                                'memberid'=>$fcmrow['memberid'],    
                                'isread'=>0,                     
                                'createddate' => $createddate,               
                                'addedby'=>$addedby
                                );
                        }                    
                        if(!empty($insertData)){
                            $this->load->model('Notification_model','Notification');
                            $this->Notification->_table = tbl_notification;
                            $this->Notification->add_batch($insertData);
                            //echo 1;//send notification
                        }                
                        
                    }
                }

                /***********Generate Quotation***********/
               /*  $this->Quotation->_table = tbl_quotation;
                $this->Quotation->generatequotation($QuotationId); */
                
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function quotation_edit($id) {
        if(channel_quotation!="1"){
            redirect("Pagenotfound");
        }
        $this->viewData['title'] = "Edit Quotation";
        $this->viewData['module'] = "quotation/Add_quotation";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;

        $this->viewData['quotationdata'] = $this->Quotation->getQuotationDataById($id,'sales');
        $this->viewData['installmentdata'] = $this->Quotation->getQuotationInstallmentDataByQuotationId($id);
        $this->viewData['ExtraChargesData'] = $this->Quotation->getExtraChargesDataByReferenceID($id);
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember($MEMBERID);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount($MEMBERID);
        
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberForQuotationBySeller($MEMBERID,$CHANNELID,'concatnameoremail');

        $this->load->model('Channel_model', 'Channel');
        $this->Channel->_fields = "id,partialpayment";
        $this->Channel->_where = ("id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")");
        $this->viewData['channelsetting'] = $this->Channel->getRecordsById();
        
        /* $this->load->model('System_configuration_model', 'System_configuration');
        $discount = $this->System_configuration->getsetting();
        if($discount['discountonbill']==1){
            $startdate = $discount['discountonbillstartdate'];
            $enddate = $discount['discountonbillenddate'];
            $currentdate = $this->general_model->getCurrentDate();
            $this->viewData['gstondiscount']= $discount['gstondiscount'];
            
            if($startdate=='0000-00-00' && $enddate=='0000-00-00'){
                $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                if($discount['discountonbilltype']==1){
                    $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                }else {
                    $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                }
            }else{
                if($currentdate >= $startdate && $currentdate <= $enddate){
                    $this->viewData['discountonbillminamount'] = $discount['discountonbillminamount'];
                    if($discount['discountonbilltype']==1){
                        $this->viewData['globaldiscountper']= $discount['discountonbillvalue'];
                    }else {
                        $this->viewData['globaldiscountamount']= $discount['discountonbillvalue'];
                    }
                }
            }
        } */
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges($CHANNELID,$MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        
        $this->channel_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->channel_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->channel_headerlib->add_javascript("add_quotation", "pages/add_quotation.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function update_quotation() 
    {
        if(channel_quotation!="1"){
            echo 0;exit;
        }
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $this->load->model('Stock_report_model', 'Stock');
        if($PostData['quotationtype']==1){
            $this->load->model("Member_model","Member");
            $memberdata = $this->Member->getmainmember($MEMBERID,"row");
            if(isset($memberdata['id'])){
                $sellermemberid = $memberdata['id'];
            }else{
                $sellermemberid = 0;
            }
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $addquotationtype = "1";//by_purchaser
        }else{
            $sellermemberid = $this->session->userdata(base_url().'MEMBERID');
            $memberid = $PostData['oldmemberid'];
            $addquotationtype = "0";//by_seller
        }
        if($addquotationtype=="1"){
            //$meber_id = $sellermemberid; 
            $meber_id = $MEMBERID; 
        }else{
            //$meber_id = $MEMBERID;
            $meber_id = $PostData['oldmemberid'];
        }
        $quotationsid = $PostData['quotationsid'];
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $quotationid = $PostData['quotationid'];
        $quotationdate = ($PostData['quotationdate']!="")?$this->general_model->convertdate($PostData['quotationdate']):'';
        $remarks = $PostData['remarks'];
        $cashorbankid = $PostData['cashorbankid'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];

        //$grossamount = $PostData['grossamount'];
        $discountpercentage = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $totalgrossamount = $PostData['totalgrossamount'];
        $netamount = $PostData['netamount'];
        $deliverypriority =  $PostData['deliverypriority'];

        $extrachargemappingidarr = (isset($PostData['extrachargemappingid']))?$PostData['extrachargemappingid']:'';
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';

        //$categoryidarr = $PostData['categoryid'];
        $productidarr = $PostData['productid'];
        $actualpricearr = $PostData['actualprice'];
        $priceidarr = $PostData['priceid'];
        $qtyarr = $PostData['qty'];
        $taxarr = isset($PostData['tax'])?$PostData['tax']:'';
        $discountarr = $PostData['discount'];
        $amountarr = $PostData['amount'];
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,3-Advance Payment,4-Partial Payment
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

        if(!empty($extrachargesidarr)){
            $totalgrossamount = ($totalgrossamount - (array_sum($extrachargeamountarr) - array_sum($extrachargestaxarr)));
            $taxamount = ($taxamount - array_sum($extrachargestaxarr));
            $netamount = ($netamount - array_sum($extrachargeamountarr));
        }else{
            $totalgrossamount = $totalgrossamount;
        }

        $quotationproductsidarr = isset($PostData['quotationproductsid'])?$PostData['quotationproductsid']:'';

        foreach($productidarr as $index=>$productid){
            $priceid = trim($priceidarr[$index]);
            $actualprice = trim($actualpricearr[$index]);
            $qty = trim($qtyarr[$index]);
            if(isset($discountarr[$index])){
                $discount = trim($discountarr[$index]);
            }else{
                $discount = 0;
            }
            $amount = trim($amountarr[$index]);
            
            if($productid!=0 && $qty!='' && $amount>0){
                
                //CHECK PURCHASE ORDER PRODUCT PRICE MATCH ON DATABASE
                if($addquotationtype==1){
                    $this->load->model('Product_model','Product');
                    $productdata = $this->Product->getVariantByProductId($productid,$memberid,'purchase',$sellermemberid);
                     
                    if(!empty($productdata)){
                        if($priceid==0){
                            $purchaseprice = trim($productdata[0]['actualprice']);
                            $purchasediscount = trim($productdata[0]['discount']);
                        }else{
                            $arrkey = array_search($priceid, array_column($productdata, 'priceid'));
                            $purchaseprice = trim($productdata[$arrkey]['actualprice']);
                            $purchasediscount = trim($productdata[$arrkey]['discount']);
                        }
                        if($actualprice != $purchaseprice){
                            echo "Price does not match of ".($index+1)." product !"; exit;
                        }
                        if($discount != '' && $discount != $purchasediscount){
                            echo "Product discount does not match of ".($index+1)." product !"; exit;
                        }
                    }
                }
            }
        }
        
        $this->Quotation->_table = tbl_quotation;
        $this->Quotation->_where = ("id!='".$quotationsid."' AND quotationid='".$quotationid."'");
        $Count = $this->Quotation->CountRecords();
        if($Count==0){
           
            $updatedata = array(
                "memberid" => $memberid,
                "sellermemberid" => $sellermemberid,
                "quotationid" => $quotationid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "quotationdate" => $quotationdate,
                "cashorbankid" => $cashorbankid,
                "remarks" => $remarks,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "quotationamount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountpercentage" => $discountpercentage,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                'deliverypriority'=>$deliverypriority,
                "type" => 1,
                "gstprice" => PRICE,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby);
            
            $updatedata=array_map('trim',$updatedata);
            $this->Quotation->_where = array('id' => $quotationsid);
            $this->Quotation->Edit($updatedata);

            if(!empty($productidarr)){

                $insertData = array();
                $updateData = array();
                $priceidsarr = $updatepriceidsarr = $updatequotationproductsidsarr = $deletequotationproductsidsarr = array();
                //$productidarr = array_unique($productidarr);
                $this->load->model('Product_model', 'Product');
                
                if(isset($PostData['removequotationproductid']) && $PostData['removequotationproductid']!=''){
                    
                    $this->readdb->select("id");
                    $this->readdb->from(tbl_quotationproducts);
                    $this->readdb->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removequotationproductid'])))."')>0");
                    $query = $this->readdb->get();
                    $ProductsData = $query->result_array();

                    if(!empty($ProductsData)){
                        foreach ($ProductsData as $row) {

                            $this->Quotation->_table = tbl_quotationproducts;
                            $this->Quotation->Delete("id=".$row['id']);
                        }
                    }
                } 
                if(isset($PostData['removeextrachargemappingid']) && $PostData['removeextrachargemappingid']!=''){
                    
                    $this->readdb->select("id");
                    $this->readdb->from(tbl_extrachargemapping);
                    $this->readdb->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeextrachargemappingid'])))."')>0");
                    $query = $this->readdb->get();
                    $MappingData = $query->result_array();

                    if(!empty($MappingData)){
                        foreach ($MappingData as $row) {

                            $this->Quotation->_table = tbl_extrachargemapping;
                            $this->Quotation->Delete("id=".$row['id']);
                        }
                    }
                } 

                $this->load->model('Channel_model', 'Channel');
                $channeldata = $this->Channel->getMemberChannelData($memberid);
		        $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;

                //$CheckProduct = $this->Product->getMemberProductCount($memberid);

                foreach($productidarr as $index=>$productid){
                    
                    $priceid = trim($priceidarr[$index]);
                    $qty = trim($qtyarr[$index]);
                    $discount = trim($discountarr[$index]);
                    $productrate = trim($PostData['productrate'][$index]);
                    $originalprice = trim($PostData['originalprice'][$index]);
                    $amount = trim($amountarr[$index]);
                    $tax = (!empty($taxarr))?trim($taxarr[$index]):'';
                    $actualprice = trim($actualpricearr[$index]);
                    $referencetype = !empty($referencetypearr[$index])?$referencetypearr[$index]:"";
                    $combopriceid = !empty($combopriceidarr[$index])?$combopriceidarr[$index]:"";

                    if(isset($quotationproductsidarr[$index]) && !empty($quotationproductsidarr[$index])){
                        $quotationproductsid = trim($quotationproductsidarr[$index]);
                    }else{
                        $quotationproductsid = "";
                    }

                    if($productid!=0 && $qty!='' && $amount>0){
                        
                        $product = $this->Product->getProductData($meber_id,$productid,$memberbasicsalesprice,1);
                        $isvariant = ($product['isuniversal']==0)?1:0;
                        if($addquotationtype==1){
                            $tax = $product['tax'];
                        }

                        $this->Quotation->_table = tbl_quotationproducts;
                       
                        if($quotationproductsid != ""){
                            
                            $this->Quotation->_table = tbl_quotationproducts;
                            $this->Quotation->_where = ("id!=".$quotationproductsid." AND quotationid=".$quotationsid." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Quotation->CountRecords();
                            
                            if($Count==0){
                                
                                $updatequotationproductsidsarr[] = $quotationproductsid; 
                                $updatepriceidsarr[] = $priceid;
                                
                                $updateData[] = array("id"=>$quotationproductsid,
                                                    "productid" => $productid,
                                                    "quantity" => $qty,
                                                    "price" => $productrate,
                                                    "originalprice" => $actualprice,
                                                    "referencetype" => $referencetype,
                                                    "referenceid" => $combopriceid,
                                                    "hsncode" => $product['hsncode'],
                                                    "tax" => $tax,
                                                    "isvariant" => $isvariant,
                                                    "discount" => $discount,
                                                    "finalprice" => round($amount + ($amount*$tax)/100),
                                                    "name" => $product['name']);
    
                            }else{
                                $deletequotationproductsidsarr[] = $quotationproductsid; 
                            }
						}else{

                            $this->Quotation->_table = tbl_quotationproducts;
                            $this->Quotation->_where = ("quotationid=".$quotationsid." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Quotation->CountRecords();
                                
                            if($Count==0){
                                $priceidsarr[] = $priceid;
                                
                                $insertData[] = array("quotationid"=>$quotationsid,
                                                        "productid" => $productid,
                                                        "quantity" => $qty,
                                                        "price" => $productrate,
                                                        "originalprice" => $actualprice,
                                                        "referencetype" => $referencetype,
                                                        "referenceid" => $combopriceid,
                                                        "hsncode" => $product['hsncode'],
                                                        "tax" => $tax,
                                                        "isvariant" => $isvariant,
                                                        "discount" => $discount,
                                                        "finalprice" => round($amount + ($amount*$tax)/100),
                                                        "name" => $product['name']);
                            }
                        }
                    }
                }
                if(!empty($updateData)){
                    $this->Quotation->_table = tbl_quotationproducts;
                    $this->Quotation->edit_batch($updateData,"id");
                    
                    if(!empty($updatequotationproductsidsarr)){
                        $this->Quotation->_table = tbl_quotationvariant;
                        $this->Quotation->Delete(array("quotationid"=>$quotationsid,"quotationproductid IN (".implode(",",$updatequotationproductsidsarr).")"));
                    }
                    if(!empty($deletequotationproductsidsarr)){
                        foreach ($deletequotationproductsidsarr as $quotationproductid) {
                            
                            $this->Quotation->_table = tbl_quotationproducts;
                            $this->Quotation->Delete("id=".$quotationproductid);
                        }
                    }
                   
                    $this->load->model('Product_combination_model', 'Product_combination');
                    $this->Quotation->_table = tbl_quotationvariant;
                    foreach($updatequotationproductsidsarr as $k=>$quotationproductid){

                        $variantdata = $this->Product_combination->getProductcombinationByPriceID($updatepriceidsarr[$k]);
                        
                        foreach($variantdata as $variant){

                            $updateVariantData[] = array("quotationid"=>$quotationsid,
                                                    "priceid" => $updatepriceidsarr[$k],
                                                    "quotationproductid" => $quotationproductid,
                                                    "variantid" => $variant['variantid'],
                                                    "variantname" => $variant['variantname'],
                                                    "variantvalue" => $variant['variantvalue']);
                        }
                    }
                    if(isset($updateVariantData) && count($updateVariantData)>0){
                        $this->Quotation->add_batch($updateVariantData);
                    }
                }
                if(!empty($insertData)){
                    $this->Quotation->_table = tbl_quotationproducts;
                    $this->Quotation->add_batch($insertData);

                    $quotationproductsidsarr=array();
                    $first_id = $this->writedb->insert_id();
                    $last_id = $first_id + (count($insertData)-1);
                    
                    for($id=$first_id;$id<=$last_id;$id++){
                        $quotationproductsidsarr[]=$id;
                    }

                    $this->load->model('Product_combination_model', 'Product_combination');
                    $insertVariantData = array();
                    
                    foreach($quotationproductsidsarr as $k=>$quotationproductid){

                        $variantdata = $this->Product_combination->getProductcombinationByPriceID($priceidsarr[$k]);
                        
                        foreach($variantdata as $variant){

                            $insertVariantData[] = array("quotationid"=>$quotationsid,
                                                    "priceid" => $priceidsarr[$k],
                                                    "quotationproductid" => $quotationproductid,
                                                    "variantid" => $variant['variantid'],
                                                    "variantname" => $variant['variantname'],
                                                    "variantvalue" => $variant['variantvalue']);
                        }
                    }
                    if(!empty($insertVariantData)){
                        $this->Quotation->_table = tbl_quotationvariant;
                        $this->Quotation->add_batch($insertVariantData);
                    }
                }

                if(!empty($extrachargesidarr)){
                    $insertextracharges = $updateextracharges = array();
                    foreach($extrachargesidarr as $index=>$extrachargesid){

                        if($extrachargesid > 0){
                            
                            $extrachargesname = trim($extrachargesnamearr[$index]);
                            $extrachargestax = trim($extrachargestaxarr[$index]);
                            $extrachargeamount = trim($extrachargeamountarr[$index]);
                            $extrachargepercentage = trim($extrachargepercentagearr[$index]);
                            $extrachargemappingid = (!empty($extrachargemappingidarr[$index]))?trim($extrachargemappingidarr[$index]):'';
                            
                            if($extrachargeamount > 0){

                                if($extrachargemappingid!=""){
                                
                                    $updateextracharges[] = array("id"=>$extrachargemappingid,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "extrachargepercentage" => $extrachargepercentage
                                                        );
                                }else{
                                    $insertextracharges[] = array("type"=>1,
                                                            "referenceid" => $quotationsid,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "extrachargepercentage" => $extrachargepercentage,
                                                            "createddate" => $modifieddate,
                                                            "addedby" => $modifiedby
                                                        );
                                }
                            }
                        }
                    }
                    if(!empty($insertextracharges)){
                        $this->Quotation->_table = tbl_extrachargemapping;
                        $this->Quotation->add_batch($insertextracharges);
                    }
                    if(!empty($updateextracharges)){
                        $this->Quotation->_table = tbl_extrachargemapping;
                        $this->Quotation->edit_batch($updateextracharges,"id");
                    }
                }
            }

            //$installmentstatus = $PostData['installmentstatus'];
            $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
            $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
            $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
            $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
            
            $EMIReceived=array();
            $this->Quotation->_table = tbl_installment;
            $this->Quotation->_fields = "GROUP_CONCAT(status) as status";
            $this->Quotation->_where = array('quotationid' => $quotationsid);
            $EMIReceived = $this->Quotation->getRecordsById();
           
            if(!empty($percentagearr) && $paymenttype==4){
                $insertinstallmentdata = array();
                $updateinstallmentdata = array();
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                    foreach($percentagearr as $k=>$percentage){
                        
                        $installmentamount = trim($installmentamountarr[$k]);
                        $installmentdate = $installmentdatearr[$k]!=''?$this->general_model->convertdate(trim($installmentdatearr[$k])):'';
                        $paymentdate = $paymentdatearr[$k]!=''?$this->general_model->convertdate(trim($paymentdatearr[$k])):'';
                        
                        if(isset($PostData['installmentstatus'.($k+1)]) && !empty($PostData['installmentstatus'.($k+1)])){
                            $status=1;
                        }else{
                            $status=0;
                        }
                        if(isset($PostData['installmentid'][$k+1])){
                            $installmentidids[] = $PostData['installmentid'][$k+1];
                        
                            $updateinstallmentdata[] = array(
                                "id"=>$PostData['installmentid'][$k+1],
                                "quotationid"=>$quotationsid,
                                "percentage"=>$percentage,
                                "amount" => $installmentamount,
                                "date" => $installmentdate,
                                "paymentdate" => $paymentdate,
                                'status'=>$status,
                                'modifieddate'=>$modifieddate,
                                'modifiedby'=>$modifiedby);
                        }else{
                            $insertinstallmentdata[] = array(
                                    "quotationid"=>$quotationsid,
                                    "percentage"=>$percentage,
                                    "amount" => $installmentamount,
                                    "date" => $installmentdate,
                                    "paymentdate" => $paymentdate,
                                    "status" => $status,
                                    "createddate" => $modifieddate,
                                    "modifieddate" => $modifieddate,
                                    "addedby" => $modifiedby,
                                    "modifiedby"=>$modifiedby);
                        }
                    }
                }
                if(count($updateinstallmentdata)>0){
                    $this->Quotation->edit_batch($updateinstallmentdata,'id');
                    if(count($installmentidids)>0){
                        $this->Quotation->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"quotationid"=>$quotationsid));
                    }
                }else{
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        $this->Quotation->Delete(array("quotationid"=>$quotationsid));
                    }
                }
                if(count($insertinstallmentdata)>0){
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        $this->Quotation->add_batch($insertinstallmentdata);
                    }
                }
                
            }else{
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                    $this->Quotation->Delete(array("quotationid"=>$quotationsid));
                }
            }

            /***********Re-generate Quotation***********/
            /* $this->Quotation->_table = tbl_quotation;
            $this->Quotation->generatequotation($quotationsid); */

            echo 1;
        }else{
            echo 2;
        }
    }
    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }
    public function getBillingAddresstByMemberId()
    {
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        
        $this->load->model('Customeraddress_model', 'Member_address');
        $BillingAddress['billingaddress'] = $this->Member_address->getaddress($memberid);

        $this->load->model('Member_model', 'Member');
        $BillingAddress['globaldiscount'] = $this->Member->getGlobalDiscountOfMember($memberid);
       
        echo json_encode($BillingAddress);
    }
    public function view_quotation($quotationid)
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Quotation";
        $this->viewData['module'] = "quotation/View_quotation";
        $this->viewData['transactiondata'] = $this->Quotation->getQuotationDetails($quotationid,'sales');
        $this->viewData['printtype'] = 'quotation';
        $this->viewData['heading'] = 'Quotation';
        
        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);

        $this->Quotation->_table = tbl_installment;
        $this->Quotation->_where = array("quotationid"=>$quotationid);
        $this->viewData['installment'] = $this->Quotation->getRecordByID();
        
        $this->viewData['quotationstatushistory'] = $this->Quotation->getQuotationStatusHistory($quotationid);

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        // echo "<pre>";print_r($this->viewData['quotationdata']);exit();
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("jquery.number", "jquery.number.js");
        $this->channel_headerlib->add_javascript("invoice", "pages/quotation_view.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function update_status()
    {
        if(channel_quotation!="1"){
            echo 0;exit;
        }
        $PostData = $this->input->post();
        $outletname = isset($PostData['outletname']) ? trim($PostData['outletname']) : '';
        $status = $PostData['status'];
        $quotationId = $PostData['quotationId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        // $companyname = $this->Quotation->getCompanyName();
        // $PostData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));

        $insertstatusdata = array(
            "quotationid" => $quotationId,
            "status" => $status,
            "type" => 1,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Quotation->_table = tbl_quotationstatuschange;  
        $this->Quotation->Add($insertstatusdata);

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==2){
            $updateData['resonforrejection'] = $PostData['resonforrejection'];
        }

        $this->Quotation->_table = tbl_quotation;  
        $this->Quotation->_where = array("id" => $quotationId);
        $updateid = $this->Quotation->Edit($updateData);
        if($updateid) {

            /**/
            $createddate  =  $this->general_model->getCurrentDateTime();
            $this->load->model('Member_model','Member');
            $this->Member->_fields="name,id";
            $this->Member->_where=array("id=(select memberid from ".tbl_quotation." where id=".$quotationId.")"=>null);
            $memberdetail = $this->Member->getRecordsByID();

            if(count($memberdetail)>0){
                    $this->load->model('Fcm_model','Fcm');
                    $fcmquery = $this->Fcm->getFcmDataByMemberId($memberdetail['id']);
                           
                    if(!empty($fcmquery)){
                        $insertData = array();
                        foreach ($fcmquery as $fcmrow){ 
                            $fcmarray=array();               
                            $type = "7";
                            if($status==1){
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation is Approved.";
                            }else if($status==2){
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation is Rejected.";
                            }else if($status==3){
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation is Cancelled.";
                            }else{
                                $msg = "Dear ".ucwords($memberdetail['name']).",Your Quotation Status Change to Pending.";
                            }
                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$quotationId.'"}';
                            $fcmarray[] = $fcmrow['fcm'];
                    
                            //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                            $this->Fcm->sendFcmNotification($type,$pushMessage,$memberdetail['id'],$fcmarray,0,$fcmrow['devicetype']);

                            $insertData[] = array(
                                'type'=>$type,
                                'message' => $pushMessage,
                                'memberid'=>$memberdetail['id'], 
                                'isread'=>0,                      
                                'createddate' => $createddate,               
                                'addedby'=>$addedby
                                );
                        }                    
                        if(!empty($insertData)){
                            $this->load->model('Notification_model','Notification');
                            $this->Notification->_table = tbl_notification;
                            $this->Notification->add_batch($insertData);
                            //echo 1;//send notification
                        }
                    }
                }
                /**/

            /* if($status==1){
                if($PostData['membername']!=''){
                    $this->load->model('Invoice_model', 'Invoice');
                    $this->Invoice->generateorderpdf($PostData); 
                }
            } */
            echo 1;    
        }else{
            echo 0;
        }
    }
    
    public function getVariantByProductId()
    {
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        if(isset($PostData['memberid']) && $PostData['memberid']>0){
            $memberid = $PostData['memberid'];
            $sellerid = $this->session->userdata(base_url().'MEMBERID');
        }else{
            $sellerid = $this->data['sellerid'];
            $memberid = $this->session->userdata(base_url().'MEMBERID');
        }
        $productdata = $this->Product->getVariantByProductId($PostData['productid'],$memberid,'purchase',$sellerid);

        echo json_encode($productdata);
    }
    public function update_installment_status()
    {
        if(channel_quotation!="1"){
            echo 0;exit;
        }
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $installmentid = $PostData['installmentid'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $modifiedby = $this->session->userdata(base_url().'MEMBERID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();

        $updateData = array(
            'status'=>$PostData['status'],
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );
        if($PostData['status']==1){
            $updateData['paymentdate']=$this->general_model->getCurrentDate();
        }else{
            $updateData['paymentdate']="";
        }  
        
        $this->Quotation->_table = tbl_installment;
        $this->Quotation->_where = array("id" => $installmentid);
        $updateid = $this->Quotation->Edit($updateData);
        if($updateid!=0) {
            echo 1;    
        }else{
            echo 0;
        }
    }

    public function search_buyer(){

        $PostData = $this->input->post();
        $searchcode = $PostData['buyercode'];

        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $memberdata = $this->Member->searchMemberCode($memberid,$channelid,$searchcode,3);

        
        if(!empty($memberdata)){
            echo json_encode($memberdata); 
        }else{
            echo 0;
        }
    }

    public function printQuotationInvoice()
    {
        $PostData = $this->input->post();
        $quotationid = $PostData['id'];
        $PostData['transactiondata'] = $this->Quotation->getQuotationDetails($quotationid);

        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);

        $PostData['printtype'] = "quotation";
        $PostData['heading'] = "Quotation";
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."quotation/Printquotationformat.php",$PostData,true);
        
        echo json_encode($html); 
    }
}