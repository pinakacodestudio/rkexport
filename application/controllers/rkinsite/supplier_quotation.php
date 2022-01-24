<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_Quotation extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('supplier_quotation_model', 'Supplier_Quotation');
        $this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Quotation');
    }
    public function index() {
    
        $this->viewData['title'] = "Supplier_Quotation";
        $this->viewData['module'] = "supplier_quotation/Supplier_Quotation";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model("Channel_model","Channel"); 
        // $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Quotation','View sales quotation.');
        }

        $where=array();
        $this->load->model("User_model","User");
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("Supplier Quotation", "pages/supplier_quotation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();

        $list = $this->Supplier_Quotation->get_datatables();
        
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        $this->load->model("Order_model","Order"); 
        $companyname = $this->Order->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';
            $status = $datarow->status;
            $menu = '';
            $channellabel = '';
            $finalprice = "<span class='pull-right'>".number_format(($datarow->netamount), 2, '.', ',')."</span>";

            // if($status == 0 && $datarow->sellermemberid == 0){
            //     if(in_array($rollid, $edit)) {
            //         $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'quotation/quotation-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            //     }
            // }
            
            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(1,'.$datarow->id.',\''.$datarow->quotationid.'\',&quot;'.$datarow->membername.'&quot;)">Approved</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(2,'.$datarow->id.',\''.$datarow->quotationid.'\')">Rejected</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagequotationstatus(3,'.$datarow->id.',\''.$datarow->quotationid.'\')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approved <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                            <a onclick="chagequotationstatus(2,'.$datarow->id.',\''.$datarow->quotationid.'\')">Rejected</a>
                            </li>
                            <li id="dropdown-menu">
                            <a onclick="chagequotationstatus(3,'.$datarow->id.',\''.$datarow->quotationid.'\')">Cancel</a>
                            </li>
                        </ul>';
            }else if($status == 2){
                /* $dropdownmenu = '<button class="btn btn-danger btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Rejected <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                            <a onclick="chagequotationstatus(3,'.$datarow->id.','.$datarow->quotationid.')">Cancel</a>
                            </li>
                        </ul>'; */
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Rejected</span>';
            }else if($status == 3){
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }

            $quotationstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            $actions .= '<a href="'.ADMIN_URL.'supplier_quotation/view-supplier-quotation/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';                  

            /* if(file_exists(QUOTATION_PATH.$companyname.'-'.$datarow->quotationid.'.pdf')){
                $actions .= '<a href="'.QUOTATION.$companyname.'-'.$datarow->quotationid.'.pdf" class="'.viewquotation_class.'" title="'.viewquotation_title.'" target="_blank">'.viewquotation_text.'</a>'; 
            }
            $actions .= '<a href="javascript:void(0);" class="'.regeneratequotation_class.'" title="'.regeneratequotation_title.'" onclick="regeneratequotation('.$datarow->id.')">'.regeneratequotation_text.'</a>';  */
            if(in_array('print', $additionalrights)) {
                $actions .= '<a href="javascript:void(0)" onclick="printquotationinvoice('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  
            }
            // if($datarow->sellermemberid == 0){
            //     if($status==1){
            //         $actions .= '<a href="'.ADMIN_URL.'order/order-add/'.$datarow->id.'" class="btn btn-sm btn-raised btn-primary" title="Add Order"><i class="fa fa-plus"></i></a>';
            //     }
                
            //     $actions .= '<a class="'.duplicatebtn_class.'" href="'.ADMIN_URL.'quotation/quotation-add/'. $datarow->id.'/'.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';
            // }

            $actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',1)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            // $actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',1,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            // if($datarow->whatsappno!=''){
            //     $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" target="_blank" href="https://api.whatsapp.com/send?phone='.$datarow->whatsappno.'&text=" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            // }else{
            //     $actions .= '<input type="hidden" id="checkwhatsappnumber'. $datarow->id.'" value="'.$datarow->whatsappno.'"><a class="'.whatsapp_class.' checkwhatsapp" id="checkwhatsapp'. $datarow->id.'" href="javascript:void(0)" onclick="checkwhatsappnumber('. $datarow->id .')" title="'.whatsapp_title.'">'.whatsapp_text.'</a>';
            // }
            
            $row[] = ++$counter;

            // $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            // if(!empty($channeldata) && isset($channeldata[$key])){
            //     $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            // }

            // $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.ucwords($datarow->membername).' ('.$datarow->membercode.')</a>';
           
            // $channellabel="";
            // if($datarow->sellerchannelid!=0){
            //     $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
            //     if(!empty($channeldata) && isset($channeldata[$key])){
            //         $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            //     }
            //     $row[] = '<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->sellermembername).'" target="_blank">'.$channellabel." ".ucwords($datarow->sellermembername).' ('.$datarow->sellermembercode.')</a>';
            // }else{
            //     $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            // }
            $row[] = $datarow->cname;
            $row[] = $datarow->inquiryno;
            $row[] = $datarow->quotationno;

            // $row[] = '<a href="'.ADMIN_URL.'quotation/view-quotation/'.$datarow->id.'" title="'.viewpdf_title.'" target="_blank">'.$datarow->quotationid.'</a>';
            
            $row[] = ($datarow->quotationdate!="0000-00-00")?$this->general_model->displaydate($datarow->quotationdate):'';
            $row[] = $quotationstatus;
            $row[] = $datarow->addbyname;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
            // $row[] = $finalprice;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Supplier_Quotation->count_all(),
                        "recordsFiltered" => $this->Supplier_Quotation->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function supplier_quotation_add($id="") {
        
        $this->viewData['title'] = "Add Supplier Quotation";
        $this->viewData['module'] = "supplier_quotation/add_supplier_quotation";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['multiplememberchannel'] = "1";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];
        
        if($id!=""){
            /* Add Duplicate Quotation */
            $this->viewData['quotationdata'] = $this->Supplier_Quotation->getQuotationDataById($id,'sales');
            $this->viewData['installmentdata'] = $this->Supplier_Quotation->getQuotationInstallmentDataByQuotationId($id);
            $this->viewData['ExtraChargesData'] = $this->Supplier_Quotation->getExtraChargesDataByReferenceID($id);
            $this->viewData['isduplicate'] = "1";
        }
       
        $this->viewData['channelsetting'] = array('partialpayment'=>1);
        $this->viewData['quotationid'] = $this->general_model->generateTransactionPrefixByType(0);

        $this->load->model('Party_model','Party');
		$this->viewData['Partydata'] = $this->Party->getRecordByID();

        $this->load->model('User_model','User');
		$this->viewData['Employedata'] = $this->User->getRecordByID();

        $this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();
        $this->viewData['countrycodedata'] = $this->Country->getCountrycode();

        $where=array();
        $this->load->model("User_model","User");
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_supplier_quotation", "pages/add_supplier_quotation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_quotation() {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        $this->load->model('Stock_report_model', 'Stock');
        $this->load->model("Member_model","Member");

        /* if(isset($PostData['isduplicate']) && $PostData['isduplicate']==1){
            $PostData['memberid'] = $PostData['oldmemberid'];
        } */
        // $sellermemberid = 0;
        $memberid = $PostData['memberid'];
        $addquotationtype = "0";//by_seller
        
        $addressid = $PostData['billingaddressid'];
        $shippingaddressid = $PostData['shippingaddressid'];
        $quotationid = $PostData['quotationid'];
        $quotationdate = ($PostData['quotationdate']!="")?$this->general_model->convertdate($PostData['quotationdate']):'';
        $remarks = $PostData['remarks'];
        $overalldiscountpercent = $PostData['overalldiscountpercent'];
        $cashorbankid = $PostData['cashorbankid'];
        
        // $grossamount = $PostData['grossamount'];
        $discountpercentage = $PostData['overalldiscountpercent'];
        $overalldiscountamount = $PostData['overalldiscountamount'];
        $taxamount = $PostData['inputtotaltaxamount'];
        $totalgrossamount = $PostData['totalgrossamount'];
        $netamount = $PostData['netamount'];
        $paymenttype = $PostData['paymenttypeid']; //$paymenttype = 1-COD,2-Advance Payment,3-Partial Payment
        $referencetypearr = isset($PostData['referencetype'])?$PostData['referencetype']:""; 
        $combopriceidarr = isset($PostData['combopriceid'])?$PostData['combopriceid']:""; 

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

        //$installmentstatus = $PostData['installmentstatus'];
        $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
        $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
        $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
        $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
        
        $salespersonid = 0; 
        if(CRM==1){
            $salespersonid = $PostData['salespersonid'];
        }

        $this->Supplier_Quotation->_table = tbl_quotation;
        $this->Supplier_Quotation->_where = ("quotationid='".$quotationid."'");
        $Count = $this->Supplier_Quotation->CountRecords();
        if($Count==0){
            
                //"sellermemberid" => $sellermemberid,

            $insertdata = array(
                "memberid" => $memberid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "remarks" => $remarks,
                "quotationid" => $quotationid,
                "quotationdate" => $quotationdate,
                "cashorbankid" => $cashorbankid,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "quotationamount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountpercentage" => $discountpercentage,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                "addquotationtype" => $addquotationtype,
                'deliverypriority'=>$deliverypriority,
                "type" => 0,
                "gstprice" => PRICE,
                "salespersonid" => $salespersonid,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" =>$addedby,
                "modifiedby" => $addedby,
                "status" => 0);
            
            $insertdata=array_map('trim',$insertdata);
            $QuotationId = $this->Supplier_Quotation->Add($insertdata);
            //$QuotationId = 10;
            if($QuotationId){
                $this->general_model->updateTransactionPrefixLastNoByType(0);
                if(!empty($productidarr)){

                    $insertData = array();
                    //$productidarr = array_unique($productidarr);
                    $this->load->model('Product_model', 'Product');
                    $this->load->model('Channel_model', 'Channel');

                    $channeldata = $this->Channel->getMemberChannelData($memberid);
		            $memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
                            
                    // $CheckProduct = $this->Product->getMemberProductCount($memberid);

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
                            $product = $this->Product->getProductData($memberid,$productid,$memberbasicsalesprice,1);
                            $isvariant = ($product['isuniversal']==0)?1:0;
                            
                            $this->Supplier_Quotation->_table = tbl_quotationproducts;
                            $this->Supplier_Quotation->_where = ("quotationid=".$QuotationId." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Supplier_Quotation->CountRecords();
                                
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
                        $this->Supplier_Quotation->_table = tbl_quotationproducts;
                        $this->Supplier_Quotation->add_batch($insertData);
                        
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
                            $this->Supplier_Quotation->_table = tbl_quotationvariant;
                            $this->Supplier_Quotation->add_batch($insertVariantData);
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
                            $this->Supplier_Quotation->_table = tbl_extrachargemapping;
                            $this->Supplier_Quotation->add_batch($insertextracharges);
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
                        $this->Supplier_Quotation->_table = tbl_installment;
                        $this->Supplier_Quotation->add_batch($insertData_installment);
                    }
                }
                $insertstatusdata = array(
                    "quotationid" => $QuotationId,
                    "status" => 0,
                    "type" => 0,
                    "modifieddate" => $createddate,
                    "modifiedby" => $addedby);
                
                $insertstatusdata=array_map('trim',$insertstatusdata);
                $this->Supplier_Quotation->_table = tbl_quotationstatuschange;  
                $this->Supplier_Quotation->Add($insertstatusdata);

                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId($memberid);

                if(!empty($fcmquery)){

                    $this->Member->_fields="id,name";
                    $this->Member->_where = array("id"=>$memberid);
                    $memberdata = $this->Member->getRecordsByID();
                    
                    $insertData = array();
                    $androidid[] = $iosid[] = array();
                    $fcmarray=array();               

                    $type = "11";
                    $msg = "Dear ".ucwords($memberdata['name']).", New Order added from Company.";
                    $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$QuotationId.'"}';
                    
                    foreach ($fcmquery as $fcmrow){ 
                        $fcmarray=array();               
                        
                        $fcmarray[] = $fcmrow['fcm'];
                
                        if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==0){
                            $androidid[] = $fcmrow['fcm']; 	 
                        }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                            $iosid[] = $fcmrow['fcm'];
                        }
                        
                        $insertData[] = array(
                            'type'=>$type,
                            'message' => $pushMessage,
                            'memberid'=>$fcmrow['memberid'],    
                            'isread'=>0,                     
                            'createddate' => $createddate,               
                            'addedby'=>$addedby
                        );
                    }      
                    if(count($androidid) > 0){
                        $this->Fcm->sendFcmNotification($type,$pushMessage,0,$fcmarray,0,0);
                    }
                    if(count($iosid) > 0){								
                        $this->Fcm->sendFcmNotification($type,$pushMessage,0,$fcmarray,0,1);
                    }               
                    if(!empty($insertData)){
                        $this->load->model('Notification_model','Notification');
                        $this->Notification->_table = tbl_notification;
                        $this->Notification->add_batch($insertData);
                        //echo 1;//send notification
                    }                
                    
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Quotation','Add new '.$quotationid.' sales quotation.');
                }
                
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function quotation_edit($id) {
      
        $this->viewData['title'] = "Edit Quotation";
        $this->viewData['module'] = "quotation/Add_quotation";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;

        $this->viewData['quotationdata'] = $this->Supplier_Quotation->getQuotationDataById($id,'sales');
        $this->viewData['installmentdata'] = $this->Supplier_Quotation->getQuotationInstallmentDataByQuotationId($id);
        $this->viewData['ExtraChargesData'] = $this->Supplier_Quotation->getExtraChargesDataByReferenceID($id);
        
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getMemberOnFirstLevelUnderCompany();
        $this->viewData['channelsetting'] = array('partialpayment'=>1);
        
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
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Cash_or_bank_model', 'Cash_or_bank');
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getBankAccountsByMember(0);
        $this->viewData['defaultbankdata'] = $this->Cash_or_bank->getDefaultBankAccount(0);

        $where=array();
        $this->load->model("User_model","User");
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_quotation", "pages/add_quotation.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function update_quotation() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $ADMINID = $this->session->userdata(base_url().'ADMINID');

        $this->load->model('Stock_report_model', 'Stock');
        // $sellermemberid = 0;
        $memberid = $PostData['oldmemberid'];
        $addquotationtype = "0";//by_seller

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

        $salespersonid = 0; 
        if(CRM==1){
            $salespersonid = $PostData['salespersonid'];
        }
        $this->Supplier_Quotation->_table = tbl_quotation;
        $this->Supplier_Quotation->_where = ("id!=".$quotationsid." AND quotationid='".$quotationid."'");
        $Count = $this->Supplier_Quotation->CountRecords();
        if($Count==0){
           
            $updatedata = array(
                /* "memberid" => $memberid,
                "sellermemberid" => $sellermemberid, */
                "quotationid" => $quotationid,
                "cashorbankid" => $cashorbankid,
                "addressid" => $addressid,
                "shippingaddressid" => $shippingaddressid,
                "quotationdate" => $quotationdate,
                "remarks" => $remarks,
                "paymenttype" => $paymenttype,
                "taxamount" => $taxamount,
                "quotationamount" => $totalgrossamount,
                "payableamount" => $netamount,
                "discountpercentage" => $discountpercentage,
                "discountamount" => 0,
                "globaldiscount" => $overalldiscountamount,
                'deliverypriority'=>$deliverypriority,
                "gstprice" => PRICE,
                "salespersonid" => $salespersonid,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby);
            
            $updatedata=array_map('trim',$updatedata);
            $this->Supplier_Quotation->_where = array('id' => $quotationsid);
            $this->Supplier_Quotation->Edit($updatedata);

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

                            $this->Supplier_Quotation->_table = tbl_quotationproducts;
                            $this->Supplier_Quotation->Delete("id=".$row['id']);
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

                            $this->Supplier_Quotation->_table = tbl_extrachargemapping;
                            $this->Supplier_Quotation->Delete("id=".$row['id']);
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
                        
                        $product = $this->Product->getProductData($memberid,$productid,$memberbasicsalesprice,1);
                        $isvariant = ($product['isuniversal']==0)?1:0;
                        $this->Supplier_Quotation->_table = tbl_quotationproducts;
                       
                        if($quotationproductsid != ""){
                            
                            $this->Supplier_Quotation->_table = tbl_quotationproducts;
                            $this->Supplier_Quotation->_where = ("id!=".$quotationproductsid." AND quotationid=".$quotationsid." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Supplier_Quotation->CountRecords();
                            
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

                            $this->Supplier_Quotation->_table = tbl_quotationproducts;
                            $this->Supplier_Quotation->_where = ("quotationid=".$quotationsid." AND productid=".$productid." AND price='".$productrate."'");
                            $Count = $this->Supplier_Quotation->CountRecords();
                                
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
                    $this->Supplier_Quotation->_table = tbl_quotationproducts;
                    $this->Supplier_Quotation->edit_batch($updateData,"id");
                    
                    if(!empty($updatequotationproductsidsarr)){
                        $this->Supplier_Quotation->_table = tbl_quotationvariant;
                        $this->Supplier_Quotation->Delete(array("quotationid"=>$quotationsid,"quotationproductid IN (".implode(",",$updatequotationproductsidsarr).")"));
                    }
                    if(!empty($deletequotationproductsidsarr)){
                        foreach ($deletequotationproductsidsarr as $quotationproductid) {
                            
                            $this->Supplier_Quotation->_table = tbl_quotationproducts;
                            $this->Supplier_Quotation->Delete("id=".$quotationproductid);
                        }
                    }
                   
                    $this->load->model('Product_combination_model', 'Product_combination');
                    $this->Supplier_Quotation->_table = tbl_quotationvariant;
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
                        $this->Supplier_Quotation->add_batch($updateVariantData);
                    }
                }
                if(!empty($insertData)){
                    $this->Supplier_Quotation->_table = tbl_quotationproducts;
                    $this->Supplier_Quotation->add_batch($insertData);

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
                        $this->Supplier_Quotation->_table = tbl_quotationvariant;
                        $this->Supplier_Quotation->add_batch($insertVariantData);
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
                        $this->Supplier_Quotation->_table = tbl_extrachargemapping;
                        $this->Supplier_Quotation->add_batch($insertextracharges);
                    }
                    if(!empty($updateextracharges)){
                        $this->Supplier_Quotation->_table = tbl_extrachargemapping;
                        $this->Supplier_Quotation->edit_batch($updateextracharges,"id");
                    }
                }
            }

            //$installmentstatus = $PostData['installmentstatus'];
            $percentagearr = isset($PostData['percentage'])?$PostData['percentage']:'';
            $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
            $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
            $paymentdatearr = isset($PostData['paymentdate'])?$PostData['paymentdate']:'';
            
            $EMIReceived=array();
            $this->Supplier_Quotation->_table = tbl_installment;
            $this->Supplier_Quotation->_fields = "GROUP_CONCAT(status) as status";
            $this->Supplier_Quotation->_where = array('quotationid' => $quotationsid);
            $EMIReceived = $this->Supplier_Quotation->getRecordsById();
           
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
                    $this->Supplier_Quotation->edit_batch($updateinstallmentdata,'id');
                    if(count($installmentidids)>0){
                        $this->Supplier_Quotation->Delete(array("id not in(".implode(",", $installmentidids).")"=>null,"quotationid"=>$quotationsid));
                    }
                }else{
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        $this->Supplier_Quotation->Delete(array("quotationid"=>$quotationsid));
                    }
                }
                if(count($insertinstallmentdata)>0){
                    if(!in_array('1',explode(",",$EMIReceived['status']))){
                        $this->Supplier_Quotation->add_batch($insertinstallmentdata);
                    }
                }
                
            }else{
                if(!in_array('1',explode(",",$EMIReceived['status']))){
                    $this->Supplier_Quotation->Delete(array("quotationid"=>$quotationsid));
                }
            }

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Quotation','Edit '.$quotationid.' sales quotation.');
            }
            /***********Re-generate Quotation***********/
            /* $this->Supplier_Quotation->_table = tbl_quotation;
            $this->Supplier_Quotation->generatequotation($quotationsid); */

            echo 1;
        }else{
            echo 2;
        }
    }
    public function regeneratequotation(){
        $PostData = $this->input->post();
        
        $quotationid = $PostData['quotationid'];
        echo $this->Supplier_Quotation->generatequotation($quotationid);
    }

    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }

    public function view_supplier_quotation($quotationid)
    {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Supplier Quotation";
        $this->viewData['module'] = "supplier_quotation/View_supplier_quotation";
        $this->viewData['transactiondata'] = $this->Supplier_Quotation->getQuotationDetails($quotationid);
        
        $this->viewData['printtype'] = 'quotation';
        $this->viewData['heading'] = 'Quotation';
        // $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        // $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        // $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid);
        // $this->Supplier_Quotation->_table = tbl_installment;
        $this->Supplier_Quotation->_where = array("quotationid"=>$quotationid);
        // $this->Supplier_Quotation->_order = ("date ASC");
        $this->viewData['installment'] = $this->Supplier_Quotation->getRecordByID();
       
        // $this->viewData['quotationstatushistory'] = $this->Supplier_Quotation->getQuotationStatusHistory($quotationid);

        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Quotation','View '.$this->viewData['transactiondata']['transactiondetail']['quotationid'].' sales quotation details.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("jquery.number", "jquery.number.js");
        $this->admin_headerlib->add_javascript("invoice", "pages/quotation_view.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_status()
    {
        $PostData = $this->input->post();
        $outletname = isset($PostData['outletname']) ? trim($PostData['outletname']) : '';
        $status = $PostData['status'];
        $quotationId = $PostData['quotationId'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        // $companyname = $this->Supplier_Quotation->getCompanyName();
        // $PostData['companyname'] = str_replace(" ", "", strtolower($companyname['businessname']));

        $insertstatusdata = array(
            "quotationid" => $quotationId,
            "status" => $status,
            "type" => 0,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby);
        
        $insertstatusdata=array_map('trim',$insertstatusdata);
        $this->Supplier_Quotation->_table = tbl_quotationstatuschange;  
        $this->Supplier_Quotation->Add($insertstatusdata);

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        if($status==2){
            $updateData['resonforrejection'] = $PostData['resonforrejection'];
        }
        $this->Supplier_Quotation->_table = tbl_quotation; 
        $this->Supplier_Quotation->_where = array("id" => $quotationId);
        $updateid = $this->Supplier_Quotation->Edit($updateData);
        if($updateid) {

            /**/
            $createddate  =  $this->general_model->getCurrentDateTime();
            
            $this->Supplier_Quotation->_fields="quotationid,memberid,(select name from ".tbl_member." where id=memberid) as name";
            $this->Supplier_Quotation->_where=array("id"=>$quotationId);
            $quotationdetail = $this->Supplier_Quotation->getRecordsByID();

            if(count($quotationdetail)>0){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Quotation','Change status '.$quotationdetail['quotationid'].' on sales quotation.');
                }
                $this->load->model('Fcm_model','Fcm');
                $fcmquery = $this->Fcm->getFcmDataByMemberId($quotationdetail['memberid']);

                if(!empty($fcmquery)){
                    $insertData = array();
                    foreach ($fcmquery as $fcmrow){ 
                        $fcmarray=array();               
                        $type = "7";
                        if($status==1){
                            $msg = "Dear ".ucwords($quotationdetail['name']).",Your Quotation is Approved.";
                        }else if($status==2){
                            $msg = "Dear ".ucwords($quotationdetail['name']).",Your Quotation is Rejected.";
                        }else if($status==3){
                            $msg = "Dear ".ucwords($quotationdetail['name']).",Your Quotation is Cancelled.";
                        }else{
                            $msg = "Dear ".ucwords($quotationdetail['name']).",Your Quotation Status Change to Pending.";
                        }
                        
                        $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$quotationId.'"}';
                        $fcmarray[] = $fcmrow['fcm'];
                        //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                        $this->Fcm->sendFcmNotification($type,$pushMessage,$quotationdetail['memberid'],$fcmarray,0,$fcmrow['devicetype']);
                        
                        $insertData[] = array(
                            'type'=>$type,
                            'message' => $pushMessage,
                            'memberid'=>$quotationdetail['memberid'],  
                            'isread'=>0,                    
                            'createddate' => $createddate,               
                            'addedby'=>$addedby
                            );
                    }                    
                    if(!empty($insertData)){
                        $this->load->model('Notification_model','Notification');
                        $this->Notification->_table = tbl_notification;
                        $this->Notification->add_batch($insertData);
                    }
                }
            }
            echo 1;    
        }else{
            echo 0;
        }
                     
       
    }

    public function update_installment_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $installmentid = $PostData['installmentid'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
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
        $this->Supplier_Quotation->_table = tbl_installment;
        $this->Supplier_Quotation->_where = array("id" => $installmentid);
        $updateid = $this->Supplier_Quotation->Edit($updateData);
        if($updateid!=0) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Supplier_Quotation->_fields="(select quotationid from ".tbl_quotation." where id=".tbl_installment.".quotationid) as quotationnumber";
                $this->Supplier_Quotation->_where=array("id"=>$installmentid);
                $quotationdetail = $this->Supplier_Quotation->getRecordsByID();

                $this->general_model->addActionLog(2,'Quotation','Change installment status '.$quotationdetail['quotationnumber'].' on sales quotation.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }

    public function printQuotationInvoice()
    {
        $PostData = $this->input->post();
        $quotationid = $PostData['id'];
        $PostData['transactiondata'] = $this->Supplier_Quotation->getQuotationDetails($quotationid);

        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        // $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid);
        $PostData['printtype'] = "quotation";
        $PostData['heading'] = "Quotation";
        $PostData['hideonprint'] = '1';

        $html['content'] = $this->load->view(ADMINFOLDER."quotation/Printquotationformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Quotation','Print '.$PostData['transactiondata']['transactiondetail']['quotationid'].' sales quotation details.');
        }
        echo json_encode($html); 
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

    public function add_billing_address() {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
       
        $memberid = $PostData['memberid'];
        $name = $PostData['baname'];
        $email = $PostData['baemail'];
        $address = $PostData['baddress'];
        $town = $PostData['batown'];
        $postalcode = $PostData['bapostalcode'];
        $mobileno = $PostData['bamobileno'];
        $countryid = $PostData['countryid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $status = 1;
        $this->load->model('Customeraddress_model','Member_address');
        
        $insertdata = array(
            "memberid" => $memberid,
            "name" => $name,
            "address" => $address,
            "provinceid" => $provinceid,
            "cityid" => $cityid,
            "town" => $town,
            "postalcode" => $postalcode,
            "mobileno" => $mobileno,
            "email" => $email,
            "status" => $status,
            "createddate" => $createddate,
            "addedby" => $addedby,
            "modifieddate" => $createddate,
            "modifiedby" => $addedby
        );
        $insertdata = array_map('trim', $insertdata);
        $AddressID = $this->Member_address->Add($insertdata);
        if ($AddressID) {
            $addressdata = $this->Member_address->getMemberAddressById($AddressID);
            echo json_encode(array("error"=>1,"id"=>$AddressID,"text"=>$addressdata['address']));
        } else {
            echo json_encode(array("error"=>0));
        }
    }

    public function addnewproductitemcloop($id)
    {
        $params = array('id' => $id);
        $this->load->view('rkinsite/quotation/itemproduct.php', $params);
    }

    public function addprodocitem($id)
    {
        $params = array('id' => $id);
        $this->load->view('rkinsite/party/itemdoc.php', $params);
    }
}