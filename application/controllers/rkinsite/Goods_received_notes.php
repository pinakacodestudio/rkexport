<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Goods_received_notes extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Goods_received_notes');
        $this->load->model("Goods_received_notes_model","Goods_received_notes");
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Goods Received Notes";
        $this->viewData['module'] = "goods_received_notes/Goods_received_notes";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Receivednotes','View goods received notes.');
        }

        $this->load->model("Vendor_model","Vendor"); 
        $this->viewData['vendordata'] = $this->Vendor->getVendorByGRNInAdmin();
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("goods_received_notes", "pages/goods_received_notes.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];

        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('onlyvendor');

        $list = $this->Goods_received_notes->get_datatables();
        
        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $datarow) {
            $row = $ordernumber_text = array();
            $Actions = $GRNstatus = $dropdownmenu = $checkbox = ''; 
            $status = $datarow->status;

            $orderIdArr = explode(",",$datarow->orderid);
            $orderNumberArr = explode(",",$datarow->ordernumbers);

            if(!empty($orderNumberArr)){
                foreach($orderNumberArr as $key=>$orderNumber){
                    $orderid = $orderIdArr[$key];
                    $ordernumber_text[] = "<a href='".ADMIN_URL."purchase-order/view-purchase-order/". $orderid."/"."' title='".$orderNumber."' target='_blank'>".$orderNumber."</a>";
                }
            }
            $key = array_search($datarow->vendorchannelid, array_column($channeldata, 'id'));
            $channellabel="";
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            $vendorname = '<a href="'.ADMIN_URL.'vendor/vendor-detail/'.$datarow->sellermemberid.'" title="'.ucwords($datarow->vendorname).'" target="_blank">'.$channellabel." ".ucwords($datarow->vendorname).' ('.$datarow->vendorcode.')</a>';
            
            if($status == 0){
                if(in_array($rollid, $edit)) {
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="changeGRNstatus(1,'.$datarow->id.')">Complete</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="changeGRNstatus(2,'.$datarow->id.')">Cancel</a>
                              </li>
                          </ul>';
                }else{
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
                }
            }else if($status == 1){
                if(in_array($rollid, $edit)) {
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="changeGRNstatus(2,'.$datarow->id.')">Cancel</a>
                              </li>
                          </ul>';
                }else{
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';
                }
            }else if($status == 2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }
            $GRNstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';
            $netamount = $datarow->netamount;
            if($datarow->netamount < 0){
                $netamount = 0;
            }

            if($status == 0){
                if(in_array($rollid, $edit)) {
                    $Actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'goods-received-notes/edit-goods-received-notes/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
                }
                if(in_array($rollid, $delete)) {
                    $Actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'goods-received-notes/check-goods-received-notes-use","Goods_received_notes","'.ADMIN_URL.'goods-received-notes/delete-mul-goods-received-notes") >'.delete_text.'</a>';
                    
                    $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
                }
            }
            $Actions .= '<a href="'.ADMIN_URL.'goods-received-notes/view-goods-received-notes/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';     
            
            if (in_array("print",$additionalrights)){
                $Actions .= '<a href="javascript:void(0)" onclick="printGoodsReceivedNotes('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  
            }
            if($status == 1 && $datarow->allowinvoice==1){
                
                $Actions .= '<a href="'.ADMIN_URL.'purchase-invoice/purchase-invoice-add/grn/'.$datarow->id.'" class="'.generateinvoice_class.'" title="'.generateinvoice_title.'">'.generateinvoice_text.'</a>'; 
            }

            $row[] = ++$counter;
            $row[] = $vendorname;
            $row[] = implode(", ",$ordernumber_text);
            $row[] = $datarow->grnnumber;
            $row[] = $this->general_model->displaydate($datarow->receiveddate);
            $row[] = $GRNstatus;
            $row[] = number_format(round($netamount),'2','.',',');
            $row[] = '<target="_blank" title="Last Modified Date" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$this->general_model->displaydatetime($datarow->modifieddate).'" >'.$this->general_model->displaydatetime($datarow->createddate); 
            $row[] = '<target="_blank" title="Last Modified By" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$datarow->modifiedby.'" >'.$datarow->addedby;  
            $row[] = $Actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Goods_received_notes->count_all(),
                        "recordsFiltered" => $this->Goods_received_notes->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }
    public function add_goods_received_notes() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Goods Received Notes";
        $this->viewData['module'] = "goods_received_notes/Add_goods_received_notes";

        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();
        $this->viewData['extrachargesdataForOrder'] = $this->Extra_charges->getActiveExtraChargesByOrderRefrence();

        $this->viewData['grnno'] = $this->general_model->generateTransactionPrefixByType(9);
        
        if($this->uri->segment(4)=="purchase-order" && $this->uri->segment(5)!=""){
            
            $this->load->model('Purchase_order_model', 'Purchase_order');
            $orderid = $this->uri->segment(5);
            $this->Purchase_order->_fields = "sellermemberid as vendorid";
            $this->Purchase_order->_where = array("id"=>$orderid);
            $OrderData = $this->Purchase_order->getRecordsById();
    
            $this->viewData['vendorid'] = $OrderData['vendorid']; 
            $this->viewData['orderid'] = $orderid; 
            
        }

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_goods_received_notes", "pages/add_goods_received_notes.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function getTransactionProducts() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->load->model('Purchase_order_model', 'Purchase_order');
        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];
        $orderid = $PostData['orderid'];
        $grnid = $PostData['grnid'];
        
        $orderproductdata = $this->Goods_received_notes->getOrderProductsByOrderIDOrVendorID($vendorid,$orderid,$grnid);
        $orderdata = $this->Purchase_order->getOrdersAmountDataByOrderID($orderid);
        $gstpricearray = !empty($orderproductdata)?array_column($orderproductdata, 'gstprice'):array();

        $json['gstprice'] = in_array("1", $gstpricearray)?1:0;
        $json['orderproducts'] = $orderproductdata;
        $json['orderamountdata'] = $orderdata;
        
        echo json_encode($json);
    }
    public function goods_received_notes_add() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $vendorid = isset($PostData['vendorid'])?$PostData['vendorid']:$PostData['oldvendorid'];
        $orderid = isset($PostData['orderid'])?$PostData['orderid']:explode(",",$PostData['oldorderid']);
        $grnno = $PostData['grnno'];
        $receiveddate = (!empty($PostData['receiveddate']))?$this->general_model->convertdate($PostData['receiveddate']):"";
        $remarks = $PostData['remarks'];
        $iseditedgrnid = isset($PostData['editgrnnumber'])?1:0;

        $orderidarr = isset($PostData['orderidarr'])?$PostData['orderidarr']:'';
        $orderproductsidarr = isset($PostData['orderproductsid'])?$PostData['orderproductsid']:'';
        $qtyarr = isset($PostData['quantity'])?$PostData['quantity']:'';
        
        
        $producttotal = $PostData['inputproducttotal'];
        $gsttotal = $PostData['inputgsttotal'];
        $totalpayableamount = $PostData['inputtotalpayableamount'];
        
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';
        $extrachargepercentagearr = (isset($PostData['extrachargepercentage']))?$PostData['extrachargepercentage']:'';

        $orderextrachargesidarr = (isset($PostData['orderextrachargesid']))?$PostData['orderextrachargesid']:'';
        $orderextrachargestaxarr = (isset($PostData['orderextrachargestax']))?$PostData['orderextrachargestax']:'';
        $orderextrachargeamountarr = (isset($PostData['orderextrachargeamount']))?$PostData['orderextrachargeamount']:'';
        $orderextrachargesnamearr = (isset($PostData['orderextrachargesname']))?$PostData['orderextrachargesname']:'';
        $orderextrachargepercentagearr = (isset($PostData['orderextrachargepercentage']))?$PostData['orderextrachargepercentage']:'';

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
            $this->Goods_received_notes->_table = tbl_goodsreceivednotes;
            $this->Goods_received_notes->_where = ("grnnumber='".$grnno."'");
            $Count = $this->Goods_received_notes->CountRecords();
            if($Count==0){

                $insertdata = array("sellermemberid" => $vendorid,
                                    "memberid" => 0,
                                    "orderid" => $ordersid,
                                    "receiveddate" => $receiveddate,
                                    "grnnumber" => $grnno,
                                    "remarks" => $remarks,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "status" => 0,
                                    "type" => 0,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby);
                
                $insertdata=array_map('trim',$insertdata);
                $GRNID = $this->Goods_received_notes->Add($insertdata);
                
                if ($GRNID) {
                    if($iseditedgrnid==0){
                        $this->general_model->updateTransactionPrefixLastNoByType(9);
                    }
                    $this->load->model('Extra_charges_model', 'Extra_charges');
                    $inserttransactionvariant = $inserttransactionproductstock = array();
                    $orderproductdata = $this->Goods_received_notes->getOrderProductsByOrderIDOrVendorID($vendorid,implode(",",$orderid));

                    $InsertData = array('grnid' => $GRNID,
                    'createddate' => $createddate,
                    'addedby' => $addedby,
                    'modifeddate' => $createddate,
                    'modififedby' => $addedby,
                    'status' =>0
                    );
                    $InsertData = array_map('trim',$InsertData);
                    $this->Goods_received_notes->_table = tbl_inwordqc;
                    $inwordqcid = $this->Goods_received_notes->add($InsertData);


                    if(!empty($orderproductsidarr)){
                        foreach($orderproductsidarr as $key=>$orderproductsid){
                            $qty = (!empty($qtyarr[$key]))?$qtyarr[$key]:'';

                            if($orderproductsid == $orderproductdata[$key]['orderproductsid'] && $qty > 0){
                                
                                $productid = $orderproductdata[$key]['productid'];
                                $priceid = $orderproductdata[$key]['combinationid'];
                                $price = $orderproductdata[$key]['amount'];
                                $discount = $orderproductdata[$key]['discount'];
                                $hsncode = $orderproductdata[$key]['hsncode'];
                                $tax = $orderproductdata[$key]['tax'];
                                $isvariant = $orderproductdata[$key]['isvariant'];
                                $name = $orderproductdata[$key]['name'];

                                $inserttransactionproduct = array("transactionid"=>$GRNID,
                                            "transactiontype"=>4,
                                            "referenceproductid"=>$orderproductsid,
                                            "productid"=>$productid,
                                            "priceid"=>$priceid,
                                            "quantity"=>$qty,
                                            "price"=>$price,
                                            "discount"=>$discount,
                                            "hsncode"=>$hsncode,
                                            "tax"=>$tax,
                                            "isvariant"=>$isvariant,
                                            "name"=>$name
                                        );

                                $inserttransactionproduct=array_map('trim',$inserttransactionproduct);
                                $this->Goods_received_notes->_table = tbl_transactionproducts;
                                $TransactionproductsID = $this->Goods_received_notes->Add($inserttransactionproduct);
                                
                                if ($TransactionproductsID) {
                                    if($isvariant == 1){
                                        $ordervariantdata = $this->Goods_received_notes->getOrderVariantsData(implode(",",$orderid),$orderproductsid);
    
                                        if(!empty($ordervariantdata)){
                                            foreach($ordervariantdata as $variant){
                                                
                                                $variantid = $variant['variantid'];
                                                $variantname = $variant['variantname'];
                                                $variantvalue = $variant['variantvalue'];
    
                                                $inserttransactionvariant[] = array("transactionid"=>$GRNID,
                                                            "transactionproductid"=>$TransactionproductsID,
                                                            "variantid"=>$variantid,
                                                            "variantname"=>$variantname,
                                                            "variantvalue"=>$variantvalue
                                                        );
                                            }
                                        }
    
                                    }
                                    

                                    $inserttransactionproductstock[] = array("referencetype"=>3,
                                            "referenceid"=>$TransactionproductsID,
                                            "stocktype"=>0,
                                            "stocktypeid"=>$TransactionproductsID,
                                            "productid"=>$productid,
                                            "priceid"=>$orderproductdata[$key]['productpriceid'],
                                            "qty"=>$qty,
                                            "action"=>0,
                                            "createddate"=>$receiveddate,
                                            "modifieddate"=>$createddate
                                        );

                                        $insertinworddata = array('inwordid' =>$inwordqcid,
                                            'transactionproductsid' =>$TransactionproductsID,
                                            'visuallycheckedqty' => 0,
                                            'visuallydefectqty' => 0,
                                            'dimensioncheckedqty' => 0,
                                            'dimensiondefectqty' => 0,
                                            'dimensionchecked' => 0,
                                            'visuallychecked' => 0,
                                            'filename' => ''
                                        );
                                    $insertinworddata = array_map('trim',$insertinworddata);
                                    $this->Inword_quality_check->_table=tbl_inwordqcmapping;
                                    $this->Inword_quality_check->add($insertinworddata);
                                    

                                }
                            }
                        }
                    }
                    /* if(!empty($inserttransactionproduct)){
                        $this->Goods_received_notes->_table = tbl_transactionproducts;
                        $this->Goods_received_notes->Add_batch($inserttransactionproduct);
                    } */
                    if(!empty($inserttransactionproductstock)){
                        $this->Goods_received_notes->_table = tbl_transactionproductstockmapping;
                        $this->Goods_received_notes->Add_batch($inserttransactionproductstock);
                    }
                    if(!empty($inserttransactionvariant)){
                        $this->Goods_received_notes->_table = tbl_transactionvariant;
                        $this->Goods_received_notes->Add_batch($inserttransactionvariant);
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

                                    $insertextracharges[] = array("type"=>5,
                                                            "referenceid" => $GRNID,
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

                    if(!empty($orderextrachargesidarr)){
                        $insertgrnorder = array();
                        foreach($orderextrachargesidarr as $orderid=>$invoiceorder){
                            if($orderid > 0){
                                foreach($invoiceorder as $key=>$extrachargesid){
                                    if($extrachargesid > 0){
                                        
                                        $extrachargesname = trim($orderextrachargesnamearr[$orderid][$key]);
                                        $extrachargestax = trim($orderextrachargestaxarr[$orderid][$key]);
                                        $extrachargeamount = trim($orderextrachargeamountarr[$orderid][$key]);
                                        $extrachargepercentage = trim($orderextrachargepercentagearr[$orderid][$key]);

                                        if($extrachargeamount > 0){

                                            $insertgrnorder[] = array(
                                                "transactiontype" => 2,
                                                "transactionid" => $GRNID,
                                                "referenceid" => $orderid,
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
                        
                        if(!empty($insertgrnorder)){
                            $this->Goods_received_notes->_table = tbl_transactionextracharges;
                            $this->Goods_received_notes->add_batch($insertgrnorder);
                        }
                    }

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Goods Received Notes','Add new '.$grnno.' goods received notes.');
                    }

                    echo json_encode(array("error"=>"1", "grnno"=>$this->general_model->generateTransactionPrefixByType(9), "grnid"=>$GRNID));
                }else{
                    echo json_encode(array("error"=>"0"));
                }
            }else{
                echo json_encode(array("error"=>"2"));
            }
        }else{
            echo json_encode(array("error"=>"0"));
        }
    }

    public function edit_goods_received_notes($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Goods Received Notes";
        $this->viewData['module'] = "goods_received_notes/Add_goods_received_notes";
        $this->viewData['action'] = "1"; //Edit

        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();
        $this->viewData['extrachargesdataForOrder'] = $this->Extra_charges->getActiveExtraChargesByOrderRefrence();

        $grndata = $this->Goods_received_notes->getGRNDetailsById($id);
        $this->viewData['grnExtraChargesdata']=$this->Goods_received_notes->getExtraChargesDataByGrnId($id);

        $this->viewData['vendorid'] = $grndata['sellermemberid'];
        $this->viewData['orderid'] = $grndata['orderid'];

        $this->viewData['grndata'] = $grndata;

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();
        $this->viewData['extrachargesdataForOrder'] = $this->Extra_charges->getActiveExtraChargesByOrderRefrence();
        
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_goods_received_notes", "pages/add_goods_received_notes.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function update_goods_received_notes() {
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $vendorid = $PostData['oldvendorid'];
        $orderid = isset($PostData['oldorderid'])?explode(",",$PostData['oldorderid']):'';
        $grnno = $PostData['grnno'];
        $receiveddate = (!empty($PostData['receiveddate']))?$this->general_model->convertdate($PostData['receiveddate']):"";
        $grnid = $PostData['grnid'];
        $remarks = $PostData['remarks'];
        
        $orderidarr = isset($PostData['orderidarr'])?$PostData['orderidarr']:'';
        $orderproductsidarr = isset($PostData['orderproductsid'])?$PostData['orderproductsid']:'';
        $qtyarr = isset($PostData['quantity'])?$PostData['quantity']:'';
        
        $producttotal = $PostData['inputproducttotal'];
        $gsttotal = $PostData['inputgsttotal'];
        $totalpayableamount = $PostData['inputtotalpayableamount'];

        $extrachargemappingidarr = (isset($PostData['extrachargemappingid']))?$PostData['extrachargemappingid']:'';
        $extrachargesidarr = (isset($PostData['extrachargesid']))?$PostData['extrachargesid']:'';
        $extrachargestaxarr = (isset($PostData['extrachargestax']))?$PostData['extrachargestax']:'';
        $extrachargeamountarr = (isset($PostData['extrachargeamount']))?$PostData['extrachargeamount']:'';
        $extrachargesnamearr = (isset($PostData['extrachargesname']))?$PostData['extrachargesname']:'';

        $orderextrachargesidarr = (isset($PostData['orderextrachargesid']))?$PostData['orderextrachargesid']:'';
        $orderextrachargestaxarr = (isset($PostData['orderextrachargestax']))?$PostData['orderextrachargestax']:'';
        $orderextrachargeamountarr = (isset($PostData['orderextrachargeamount']))?$PostData['orderextrachargeamount']:'';
        $orderextrachargesnamearr = (isset($PostData['orderextrachargesname']))?$PostData['orderextrachargesname']:'';
        $orderextrachargepercentagearr = (isset($PostData['orderextrachargepercentage']))?$PostData['orderextrachargepercentage']:'';
        $orderextrachargesmappingidarr = (isset($PostData['orderextrachargesmappingid']))?$PostData['orderextrachargesmappingid']:'';

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
            
            $this->Goods_received_notes->_where = array("id<>".$grnid,"grnnumber='".$grnno."'");
            $Count = $this->Goods_received_notes->CountRecords();
            if($Count==0){

                $updatedata = array("receiveddate" => $receiveddate,
                                    "grnnumber"=>$grnno,
                                    "remarks" => $remarks,
                                    "taxamount" => $gsttotal,
                                    "amount" => $producttotal,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby" => $modifiedby);
                
                $this->Goods_received_notes->_where = array("id"=>$grnid);
                $this->Goods_received_notes->Edit($updatedata);
                
                $this->load->model('Extra_charges_model', 'Extra_charges');
                $updatetransactionproduct = $removetransactionproduct = $updatetransactionproductstock = array();
                
                $orderproductdata = $this->Goods_received_notes->getOrderProductsByOrderIDOrVendorID($vendorid,implode(",",$orderid),$grnid);

                if(!empty($orderproductsidarr)){
                    foreach($orderproductsidarr as $key=>$orderproductsid){
                        
                        $qty = (!empty($qtyarr[$key]))?$qtyarr[$key]:'';
                        $transactionproductsid = $orderproductdata[$key]['transactionproductsid'];

                        if($orderproductsid == $orderproductdata[$key]['orderproductsid'] && $qty > 0){
                            
                            $productid = $orderproductdata[$key]['productid'];
                            $priceid = $orderproductdata[$key]['combinationid'];
                            $price = $orderproductdata[$key]['amount'];
                            $discount = $orderproductdata[$key]['discount'];
                            $hsncode = $orderproductdata[$key]['hsncode'];
                            $tax = $orderproductdata[$key]['tax'];
                            $isvariant = $orderproductdata[$key]['isvariant'];
                            $name = $orderproductdata[$key]['name'];

                            $updatetransactionproduct[] = array(
                                "id"=> $transactionproductsid,
                                "quantity"=>$qty,
                                "modifieddate"=>$modifieddate
                            );


                            $this->Goods_received_notes->_table = tbl_transactionproductstockmapping;
                            $this->Goods_received_notes->_where = array("referencetype"=>3,"referenceid"=>$transactionproductsid,"stocktype"=>0,"stocktypeid"=>$transactionproductsid,"productid"=>$productid,"priceid"=>$orderproductdata[$key]['productpriceid']);
                            $StockData = $this->Goods_received_notes->getRecordsByID();
                            if(!empty($StockData)){
                                $updatetransactionproductstock[] = array(
                                    "id"=> $StockData['id'],
                                    "qty"=>$qty,
                                    "createddate" => $receiveddate,
                                    "modifieddate"=>$modifieddate
                                );
                            }
                            /* if(isset($transactionproductsid) && $transactionproductsid!=''){
                                $updatetransactionproduct[] = array(
                                            "id"=> $transactionproductsid,
                                            "quantity"=>$qty,
                                            );
                            }else{
                                $inserttransactionproduct[] = array("transactionid"=>$invoiceid,
                                        "transactiontype"=>3,
                                        "referenceproductid"=>$orderproductsid,
                                        "productid"=>$productid,
                                        "priceid"=>$priceid,
                                        "quantity"=>$qty,
                                        "price"=>$price,
                                        "discount"=>$discount,
                                        "hsncode"=>$hsncode,
                                        "tax"=>$tax,
                                        "isvariant"=>$isvariant,
                                        "name"=>$name
                                    );
                                if($isvariant == 1){
                                    $ordervariantdata = $this->Invoice->getOrderVariantsData(implode(",",$orderid),$orderproductsid);
        
                                    if(!empty($ordervariantdata)){
                                        foreach($ordervariantdata as $variant){
                                            
                                            $variantid = $variant['variantid'];
                                            $variantname = $variant['variantname'];
                                            $variantvalue = $variant['variantvalue'];
        
                                            $inserttransactionvariant[] = array("transactionid"=>$invoiceid,
                                                        "transactionproductid"=>$orderproductsid,
                                                        "variantid"=>$variantid,
                                                        "variantname"=>$variantname,
                                                        "variantvalue"=>$variantvalue
                                                    );
                                        }
                                    }
        
                                }
                            } */

                        }else{
                            if(isset($transactionproductsid) && $transactionproductsid!=''){
                                $removetransactionproduct[] = $transactionproductsid;
                            }
                        }
                    }
                }
                /* if(!empty($inserttransactionproduct)){
                    $this->Invoice->_table = tbl_transactionproducts;
                    $this->Invoice->Add_batch($inserttransactionproduct);
                }
                if(!empty($inserttransactionvariant)){
                    $this->Invoice->_table = tbl_transactionvariant;
                    $this->Invoice->Add_batch($inserttransactionvariant);
                } */
                if(!empty($updatetransactionproduct)){
                    $this->Goods_received_notes->_table = tbl_transactionproducts;
                    $this->Goods_received_notes->Edit_batch($updatetransactionproduct, "id");
                }
                if(!empty($updatetransactionproductstock)){
                    $this->Goods_received_notes->_table = tbl_transactionproductstockmapping;
                    $this->Goods_received_notes->Edit_batch($updatetransactionproductstock, "id");
                }
                if(!empty($removetransactionproduct)){
                    foreach ($removetransactionproduct as $transactionproductid) {

                        $this->Goods_received_notes->_table = tbl_transactionproductstockmapping;
                        $this->Goods_received_notes->Delete("referencetype=3 AND referenceid='".$transactionproductid."' AND stocktype=0 AND stocktypeid='".$transactionproductid."'");
                        
                        $this->Goods_received_notes->_table = tbl_transactionvariant;
                        $this->Goods_received_notes->Delete(array("transactionid"=>$grnid,"transactionproductid"=>$transactionproductid));
                        $this->Goods_received_notes->_table = tbl_transactionproducts;
                        $this->Goods_received_notes->Delete(array("id"=>$transactionproductid));

                    }
                }
                if(!empty($extrachargesidarr)){
                    $insertextracharges = $DeleteExtraCharge = $updateextracharges = array();
                    foreach($extrachargesidarr as $index=>$extrachargesid){

                        if($extrachargesid > 0){
                            $extrachargesname = trim($extrachargesnamearr[$index]);
                            $extrachargestax = trim($extrachargestaxarr[$index]);
                            $extrachargeamount = trim($extrachargeamountarr[$index]);

                            $extrachargemappingid = (!empty($extrachargemappingidarr[$index]))?trim($extrachargemappingidarr[$index]):'';
                            
                            if($extrachargeamount > 0){

                                if($extrachargemappingid!=""){
                                
                                    $updateextracharges[] = array("id"=>$extrachargemappingid,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount
                                                        );
                                    $DeleteExtraCharge[]=$extrachargemappingid;
                                }else{
                                    $insertextracharges[] = array("type"=>5,
                                                            "referenceid" => $grnid,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "createddate" => $modifieddate,
                                                            "addedby" => $modifiedby
                                                        );
                                }
                            }
                        }
                    }
                    
                    $Extra_charges_Data = $this->Goods_received_notes->getExtraChargesDataByGrnId($grnid);
                    $Extra_charges_Id_Array = (!empty($Extra_charges_Data)?array_column($Extra_charges_Data,"id"):array()); 
                    if(!empty($Extra_charges_Id_Array)){
                        $deletearr = array_diff($Extra_charges_Id_Array,$DeleteExtraCharge);
                    }
                    
                    if(!empty($deletearr)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->Delete(array("id IN (".implode(",",$deletearr).")"=>null));
                    }
                    if(!empty($insertextracharges)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->add_batch($insertextracharges);
                    }
                    if(!empty($updateextracharges)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->edit_batch($updateextracharges,"id");
                    }
                }

                // FOR ORDER EXTRA CHARGES
                if(!empty($orderextrachargesidarr)){
                    $UpdateGRNorder = $DeleteORDERCharge = array();
                    foreach($orderextrachargesidarr as $orderid=>$GRNorder){
                        if($orderid > 0){
                            foreach($GRNorder as $key=>$extrachargesid){
                                if($extrachargesid > 0){
                                    
                                    $extrachargesname = trim($orderextrachargesnamearr[$orderid][$key]);
                                    $extrachargestax = trim($orderextrachargestaxarr[$orderid][$key]);
                                    $extrachargeamount = trim($orderextrachargeamountarr[$orderid][$key]);
                                    $extrachargepercentage = trim($orderextrachargepercentagearr[$orderid][$key]);
                                    $extrachargesmappingid = isset($orderextrachargesmappingidarr[$orderid][$key])?(trim($orderextrachargesmappingidarr[$orderid][$key])):"NaN";

                                    if($extrachargeamount > 0){
                                        if($extrachargesmappingid !="NaN"){
                                            $UpdateGRNorder[] = array(
                                                        "id" => $extrachargesmappingid,
                                                        "goodsreceivednotesid" => $grnid,
                                                        "extrachargesid" => $extrachargesid,
                                                        "extrachargesname" => $extrachargesname,
                                                        "taxamount" => $extrachargestax,
                                                        "amount" => $extrachargeamount,
                                                        "extrachargepercentage" => $extrachargepercentage
                                                        );

                                            $DeleteORDERCharge[]=$extrachargesmappingid;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $Goods_received_notes_Data = $this->Extra_charges->getExtraChargeByOrderIdFromGRN($grnid);
                    $Goods_received_notes_Id_Array = (!empty($Goods_received_notes_Data)?array_column($Goods_received_notes_Data,"id"):array()); 
                    if(!empty($Goods_received_notes_Id_Array)){
                        $deletearr = array_diff($Goods_received_notes_Id_Array,$DeleteORDERCharge);
                    }
                    if(!empty($deletearr)){
                        $this->Goods_received_notes->_table = tbl_transactionextracharges;
                        $this->Goods_received_notes->Delete(array("id IN (".implode(",",$deletearr).")"=>null));
                    }
                    
                    if(!empty($UpdateGRNorder)){
                        $this->Goods_received_notes->_table = tbl_transactionextracharges;
                        $this->Goods_received_notes->edit_batch($UpdateGRNorder,'id');
                    }
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Goods Received Notes','Edit '.$grnno.' goods received notes.');
                }
                echo json_encode(array("error"=>"1","grnid"=>$grnid));
            }else{
                echo json_encode(array("error"=>"2"));
            }
        }else{
            echo json_encode(array("error"=>"1"));
        }

    }
    public function check_goods_received_notes_use(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach($ids as $row){
            /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE 
                    id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
                    //OR id IN (SELECT vehicleid FROM ".tbl_vehicleroute." WHERE vehicleid = $row)
            if($query->num_rows() > 0){
                $count++;
            } */
        }
        echo $count;
    }

    public function delete_mul_goods_received_notes(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){

            $this->Goods_received_notes->_table = tbl_transactionextracharges;
            $this->Goods_received_notes->Delete(array("transactiontype" => 2,"transactionid" =>$row));

            $this->Goods_received_notes->_table = tbl_extrachargemapping;
            $this->Goods_received_notes->Delete(array("type"=>5,"referenceid" => $row));

            $this->Goods_received_notes->_table = tbl_transactionproductstockmapping;
            $this->Goods_received_notes->Delete(array("referencetype"=>3,"referenceid IN (SELECT id FROM ".tbl_transactionproducts." WHERE transactiontype=4 AND transactionid='".$row."')"=>null,"stocktype"=>0,"stocktypeid IN (SELECT id FROM ".tbl_transactionproducts." WHERE transactiontype=4 AND transactionid='".$row."')"=>null));

            $this->Goods_received_notes->_table = tbl_transactionvariant;
            $this->Goods_received_notes->Delete(array("transactionid"=>$row,"transactionproductid IN (SELECT id FROM ".tbl_transactionproducts." WHERE transactiontype=4 AND transactionid='".$row."')"=>null));

            $this->Goods_received_notes->_table = tbl_transactionproducts;
            $this->Goods_received_notes->Delete(array("transactiontype"=>4,"transactionid" => $row));

            $this->Goods_received_notes->_table =tbl_goodsreceivednotes;
            $this->Goods_received_notes->Delete(array("id"=>$row));
        }
    }
    public function view_goods_received_notes($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Goods Received Notes";
        $this->viewData['module'] = "goods_received_notes/View_goods_received_notes";
        
        $this->viewData['transactiondata'] = $this->Goods_received_notes->getGoodsReceivedNotesDetails($id);
        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $this->viewData['printtype'] = 'goods_received_notes';
        $this->viewData['heading'] = 'Goods Received Notes';
        $this->viewData['viewtype'] = 'page';

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Goods Received Notes','View '.$this->viewData['transactiondata']['transactiondetail']['grnnumber'].' goods received notes details.');
        }

        $this->admin_headerlib->add_javascript("view_goods_received_notes", "pages/view_goods_received_notes.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function printGoodsReceivedNotes(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $grnid = $PostData['id'];
        $PostData['transactiondata'] = $this->Goods_received_notes->getGoodsReceivedNotesDetails($grnid);

        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $PostData['printtype'] = "goods_received_notes";
        $PostData['heading'] = "Goods Received Notes";
        $PostData['hideonprint'] = '1';
        $PostData['printnotes'] = "1";

        $html['content'] = $this->load->view(ADMINFOLDER."goods_received_notes/Printgoodsreceivednotesformat.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Goods Received Notes','Print '.$PostData['transactiondata']['transactiondetail']['grnnumber'].' goods received notes details.');
        }
        echo json_encode($html); 
    }
    public function update_status(){
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $GRNId = $PostData['GRNId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        /* if($status==2){
            $cancelled = $this->Goods_received_notes->confirmForGRNCancellation($GRNId);

            if(!$cancelled){
                echo 1; exit;
            }
        } */

        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        
        if($status==2){
            $updateData['cancelreason'] = $PostData['resonforcancellation'];
            $updateData['cancelledby'] = $modifiedby;
        }
        
        $this->Goods_received_notes->_where = array("id" => $GRNId);
        $update = $this->Goods_received_notes->Edit($updateData);
        if($update) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Goods_received_notes->_fields="grnnumber";
                $this->Goods_received_notes->_where=array("id"=>$GRNId);
                $Goodsreceivednotesdata = $this->Goods_received_notes->getRecordsByID();

                $this->general_model->addActionLog(2,'Goods Received Notes','Change status '.$Goodsreceivednotesdata['grnnumber'].' on goods received notes.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function getApprovedInvoiceByMember(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $SellerID = 0;
        $memberdata = $this->Invoice->getApprovedInvoiceByMember($SellerID,$memberid);
        
        echo json_encode($memberdata);
    }
    
    public function getPaymentReceiptInvoice(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        $SellerID = 0;
        $paymentreceiptid = isset($PostData['paymentreceiptid'])?$PostData['paymentreceiptid']:0;
        $memberdata = $this->Invoice->getPaymentReceiptInvoice($SellerID,$memberid,$paymentreceiptid);
        
        echo json_encode($memberdata);
    }
    public function getOrderMemberByChannel(){
        $PostData = $this->input->post();
        $channelid = $PostData['channelid'];
       
        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveBuyerMemberForOrderBySellerInCompany('concatnameormembercodeormobile',$channelid);
        
        echo json_encode($memberdata);
    }
    public function getInvoiceByBuyer(){
        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        
        $invoicedata = $this->Invoice->getInvoiceByBuyer($memberid);
        echo json_encode($invoicedata);
    }
    public function getInvoiceProducts(){
        $PostData = $this->input->post();
        $invoiceid = $PostData['invoiceid'];
        
        $invoicedata = $this->Invoice->getInvoiceProducts($invoiceid);
        echo json_encode($invoicedata);
    }
    public function generateAwB(){
        $invoiceid = $this->input->post('invoiceid');

        $awbdata = array();
        $awbdata['billLists'] = $this->Invoice->generateAwB($invoiceid);
        $awbdata['version'] = '1.0.1118';

        /* $awbdata = '{
            "billLists": [{
                "itemList": [{
                    "itemNo": 1,
                    "productName": "8-P2-E",
                    "productDesc": "8-P2-E",
                    "hsnCode": 0,
                    "quantity": 1.0,
                    "qtyUnit": "Nos.",
                    "taxableAmount": 50000.0,
                    "sgstRate": 9.0,
                    "cgstRate": 9.0,
                    "igstRate": 0.00,
                    "cessRate": 0.00,
                    "cessNonAdvol": 0.00
                }],
                "userGstin": "",
                "supplyType": "O",
                "subSupplyType": 2,
                "docType": "Tax Invoice",
                "docNo": "525252",
                "docDate": "15-10-2020",
                "transType": 1,
                "fromGstin": "",
                "fromTrdName": "PARTH INSTITUTE",
                "fromAddr1": "KARELIBAUG",
                "fromAddr2": "KARELIBAUG",
                "fromPlace": "Vadodara",
                "fromPincode": 390018,
                "fromStateCode": 24,
                "actualFromStateCode": 24,
                "toGstin": "",
                "toTrdName": "CHAUHAN ARYAN VIJAY",
                "toAddr1": "",
                "toAddr2": "",
                "toPlace": "",
                "toPincode": "",
                "toStateCode": 0,
                "actualToStateCode": 0,
                "totalValue": 50000.0,
                "cgstValue": 1388.8888888888888888888888889,
                "sgstValue": 1388.8888888888888888888888889,
                "igstValue": 0.00,
                "cessValue": 0.00,
                "OthValue": 0.00,
                "TotNonAdvolVal": 0.00,
                "transMode": 2,
                "transDistance": 500.0,
                "transporterName": "ABC",
                "transporterId": "ABC",
                "transDocNo": "1235465",
                "transDocDate": "23-10-2020",
                "vehicleNo": "GJ-10-BA-1245",
                "vehicleType": "R",
                "totInvValue": 59000.0,
                "mainHsnCode": 0
            }],
            "version": "1.0.1118"
        }'; */

        echo json_encode($awbdata);
    }
    public function exporttoexcelinvoice(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Invoice','Export to excel sales invoice.');
        }

        $this->Invoice->exportinvoice();
    }
}
?>