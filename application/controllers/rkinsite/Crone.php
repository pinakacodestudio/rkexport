<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crone extends CI_Controller {

	public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->readdb = $this->load->database('readdb',TRUE);  // for read
        $this->writedb = $this->load->database('writedb',TRUE);  // for write
    }
	
	public function updateAdminProductOrMemberProductPrice()
	{
        $currentdatetime = $this->general_model->getCurrentDateTime();
        $this->load->model('Price_history_model', 'Price_history');

        $this->Price_history->_fields = 'id';
        $this->Price_history->_where = "scheduleddate = '".$currentdatetime."' AND scheduleddate != '0000-00-00 00:00:00'";
        $pricehistory = $this->Price_history->getRecordByID();
        
        if(!empty($pricehistory)){

            $pricehistoryidarr = array_column($pricehistory, 'id');
            
            $this->Price_history->updateAdminProductOrMemberProductPrice($pricehistoryidarr);
        }

    }
    public function encryptNewPasswordFromOldPassword($tablename,$fieldname)
	{
        $query=$this->db->select("id,".$fieldname)
                        ->from($tablename)
                        ->get();
        $tabledata = $query->result_array();
        
        if(!empty($tabledata)){
            $updatedata=array();
            foreach($tabledata as $row){

                $oldpassword = $this->general_model->olddecryptIt($row[$fieldname]);
                $newencryptedpassword = $this->general_model->encryptIt($oldpassword);
                
                $updatedata[] = array("id" => $row['id'],
                                      $fieldname => $newencryptedpassword
                                );
            }
            if(!empty($updatedata)){
                $this->db->update_batch($tablename,$updatedata,"id");
            }
        }
    }

    public function indiamartForwardCron(){

        date_default_timezone_set('Asia/Kolkata');
        $query=$this->readdb->select("mobileno,mobilekey,forwardassigntoid,datetime,status")
                        ->from(tbl_indiamartlead)
                        ->get();
        $result = $query->result_array();
        
        if (count($result)>0) {
            foreach ($result as $row) {
                $datetime = $row['datetime'];
                $mobile_no = $row['mobileno'];
                $key = $row['mobilekey'];
                $forwardassigntoid = $row['forwardassigntoid'];
                $status = $row['status'];
            }
                
            if ($status==1) {
                $enddate = date('d-M-Y H:i:s', strtotime(date("Y-m-d H:i:s")));
                
                if ($datetime == "0000-00-00 00:00:00" || $datetime == null) {
                    $startdate = date('d-M-Y H:i:s', strtotime(date("Y-m-d H:i:s"). "-7 days"));
                } else {
                    $startdate = date('d-M-Y H:i:s', strtotime($datetime));
                }

                if (strtotime($startdate) > strtotime($enddate)) {
                    exit;
                }
                
                $this->general_model->insertIndiaMartLead($mobile_no, $key, $forwardassigntoid, $startdate, $enddate, true);
            }
        }
    } 

    public function indiamartBackwardFirstCron() {

        date_default_timezone_set('Asia/Kolkata');
        $query=$this->readdb->select("mobileno,mobilekey,backwardassigntoid,enddate,backdatetime,status")
                        ->from(tbl_indiamartlead)
                        ->get();
        $result = $query->result_array();

        if (count($result)>0) {
            foreach ($result as $row) {
                $enddatevalidation = $row['enddate'];
                $backdatetime = $row['backdatetime'];
                $mobile_no = $row['mobileno'];
                $key = $row['mobilekey'];
                $backwardassigntoid = $row['backwardassigntoid'];
                $status = $row['status'];
            }
                
            if ($status==1 && $enddatevalidation != "0000-00-00 00:00:00") {
                //if ($status==1) {
                if ($backdatetime == "0000-00-00 00:00:00" || $backdatetime == null) {
                    $query=$this->readdb->select("min(createddate) as createddate")
                                ->from(tbl_crminquiry)
                                ->where("inquiryleadsourceid=8")
                                ->get();
                    $result = $query->result_array();
                    foreach ($result as $row) {
                        $createddate = $row['createddate'];
                    }

                    $enddate = date('d-M-Y H:i:s', strtotime($createddate));
                    $startdate = date('d-M-Y H:i:s', strtotime($enddate. "-7 days"));
                } else {
                    $enddate = date('d-M-Y H:i:s', strtotime($backdatetime));
                    $startdate = date('d-M-Y H:i:s', strtotime($backdatetime. "-7 days"));
                }
                
                if (strtotime($enddatevalidation) > strtotime($enddate)) {
                    //todo:udpate enddatevalidation
                    exit;
                }
                
                $this->general_model->insertIndiaMartLead($mobile_no, $key, $backwardassigntoid, $startdate, $enddate, false);
            }
        }
    }  
    
    public function sendexpirelicencemail()
	{
        $this->load->model('System_configuration_model', 'System_configuration');
        $this->load->model('Settings_model','Settings');
        $systemconfiguration = $this->System_configuration->getsetting();

        $this->readdb->select('*,
						IFNULL((SELECT GROUP_CONCAT(email) FROM '.tbl_companycontactdetails.' WHERE type=1 LIMIT 1),"") as email,
                        IFNULL((SELECT GROUP_CONCAT(mobileno) FROM '.tbl_companycontactdetails.' WHERE type=0 LIMIT 1),"") as mobileno
						');
        $settingdata = $this->readdb->get_where(tbl_settings)->row_array();
        define("COMPANY_EMAIL", $settingdata['email']);
        
        $systemconfiguration['expirydate'] = "2021-07-06";
        if(!empty($systemconfiguration) && $systemconfiguration['expirydate']!="0000-00-00"){
            $currentdate = date("Y-m-d");
            $beforefivedayasdate = date("Y-m-d",strtotime("-5 day", strtotime($systemconfiguration['expirydate'])));
            
            if($currentdate >= $beforefivedayasdate && $currentdate <= $systemconfiguration['expirydate']){
                
                $allows3 = (isset($systemconfiguration['allows3']))?$systemconfiguration['allows3']:0;
                $portal = $_SERVER["SERVER_NAME"];
                include APPPATH . 'config/client.php';
                $clientfolder = '';
                if (!empty($portaldetail[$portal])) {
                    $clientfolder = $portaldetail[$portal]['folder'];
                }

                $FILE_URL = DOMAIN_URL."uploaded/".$clientfolder."/";
               
                if($allows3==1){
                    $FILE_URL = AWSLINK.BUCKETNAME.'/';
                }
                
                $mail = "sales@rkinfotechindia.com";
                
                /* SEND EMAIL */
				$mailBodyArr1 = array(
					"{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' .$FILE_URL."company/". $settingdata['logo'].'" alt="' . $settingdata['businessname'] . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
					"{companyemail}" => explode(",",$settingdata['email'])[0],
					"{companyname}" => $settingdata['businessname'],
                    "{companyurl}" => '<a href="'.DOMAIN_URL.'">'.DOMAIN_URL.'</a>',
                    "{expirydate}" => $this->general_model->displaydate($systemconfiguration['expirydate'])
				);

                // echo $mailBodyArr1;
                $this->System_configuration->sendMail(14, $mail, $mailBodyArr1);
            }
        }
    }

    public function updateproductstockentry()
	{
        $this->load->model('Goods_received_notes_model', 'GRN');
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->load->model('Purchase_order_model', 'Purchase_order');
        
        // CREATE GRN BY PURCHASE ORDER
        $query=$this->db->select("*")
                        ->from(tbl_orders." as o")
                        ->where("sellermemberid!=0 AND memberid=0 AND status=1 AND approved=1")
                        ->get();
        $purchaseorderdata = $query->result_array();
        
        if(!empty($purchaseorderdata)){
        
            $inserttransactionproductstock = $inserttransactionvariant = $insertextracharges = $insertinvoiceorder = array();
            
            foreach($purchaseorderdata as $row){
                
                $createddate = $this->general_model->getCurrentDateTime();
                $vendorid = $row['sellermemberid'];
                $orderid = $row['id'];
                $receiveddate = $this->general_model->convertdate($row['orderdate']);
                $remarks = "";
                $grnno = $this->general_model->generateTransactionPrefixByType(9);
                
                $this->GRN->_table = tbl_goodsreceivednotes;
                $this->GRN->_where = ("grnnumber='".$grnno."'");
                $Count = $this->GRN->CountRecords();
                
                if($Count==0){
                    $insertdata = array("sellermemberid" => $vendorid,
                                        "memberid" => 0,
                                        "orderid" => $orderid,
                                        "receiveddate" => $receiveddate,
                                        "grnnumber" => $grnno,
                                        "remarks" => $remarks,
                                        "taxamount" => $row['taxamount'],
                                        "amount" => $row['amount'],
                                        "status" => 1,
                                        "type" => 0,
                                        "createddate" => $row['createddate'],
                                        "addedby" => $row['addedby'],
                                        "modifieddate" => $row['createddate'],
                                        "modifiedby" => $row['addedby'],
                                    );
                    
                    $insertdata=array_map('trim',$insertdata);
                    $GRNID = $this->GRN->Add($insertdata);
                    
                    if ($GRNID) {
                        $this->general_model->updateTransactionPrefixLastNoByType(9);
                        
                        $orderproductdata = $this->GRN->getOrderProductsByOrderIDOrVendorID($vendorid,$orderid);
                        
                        if(!empty($orderproductdata)){
                            foreach($orderproductdata as $key=>$orderproduct){
                                $qty = $orderproduct['quantity'];
                            
                                if($qty > 0){
                                    
                                    $orderproductsid = $orderproduct['orderproductsid'];
                                    $productid = $orderproduct['productid'];
                                    $priceid = $orderproduct['combinationid'];
                                    $price = $orderproduct['amount'];
                                    $discount = $orderproduct['discount'];
                                    $hsncode = $orderproduct['hsncode'];
                                    $tax = $orderproduct['tax'];
                                    $isvariant = $orderproduct['isvariant'];
                                    $name = $orderproduct['name'];

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
                                    $this->GRN->_table = tbl_transactionproducts;
                                    $TransactionproductsID = $this->GRN->Add($inserttransactionproduct);
                                    
                                    if ($TransactionproductsID) {
                                        if($isvariant == 1){
                                            $ordervariantdata = $this->GRN->getOrderVariantsData($orderid,$orderproductsid);

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
                                                "priceid"=>$orderproduct['productpriceid'],
                                                "qty"=>$qty,
                                                "action"=>0,
                                                "modifieddate"=>$receiveddate,
                                                "createddate"=>$createddate
                                            );
                                    }
                                }
                            }
                        }

                        $extrachargesdata = $this->Purchase_order->getExtraChargesDataByReferenceID($GRNID,5);

                        if(!empty($extrachargesdata)){
                            foreach($extrachargesdata as $index=>$extracharges){
    
                                $extrachargesid = $extracharges['extrachargesid']; 
                                if($extrachargesid > 0){
                                    $extrachargesname = trim($extracharges['extrachargesname']);
                                    $extrachargestax = trim($extracharges['taxamount']);
                                    $extrachargeamount = trim($extracharges['amount']);
                                    $extrachargepercentage = trim($extracharges['extrachargepercentage']);
    
                                    if($extrachargeamount > 0){
    
                                        $insertextracharges[] = array("type"=>5,
                                                                "referenceid" => $GRNID,
                                                                "extrachargesid" => $extrachargesid,
                                                                "extrachargesname" => $extrachargesname,
                                                                "extrachargepercentage" => $extrachargepercentage,
                                                                "taxamount" => $extrachargestax,
                                                                "amount" => $extrachargeamount,
                                                                "createddate" => $row['createddate'],
                                                                "addedby" => $row['addedby']
                                                            );

                                        $insertinvoiceorder[] = array(
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
                }
            }
            /* if(!empty($inserttransactionproduct)){
                $this->GRN->_table = tbl_transactionproducts;
                $this->GRN->Add_batch($inserttransactionproduct);
            } */
            if(!empty($inserttransactionproductstock)){
                $this->GRN->_table = tbl_transactionproductstockmapping;
                $this->GRN->Add_batch($inserttransactionproductstock);
            }
            if(!empty($inserttransactionvariant)){
                $this->GRN->_table = tbl_transactionvariant;
                $this->GRN->Add_batch($inserttransactionvariant);
            }
            if(!empty($insertextracharges)){
                $this->GRN->_table = tbl_extrachargemapping;
                $this->GRN->add_batch($insertextracharges);
            }
            if(!empty($insertinvoiceorder)){
                $this->GRN->_table = tbl_transactionextracharges;
                $this->GRN->add_batch($insertinvoiceorder);
            }
        }

        // UPDATE PURCHASE ORDER PRODUCT TO GRN PRODUCT
        $this->db->query("UPDATE ".tbl_transactionproductstockmapping." as tpsm
                            INNER JOIN ".tbl_orderproducts." as op ON op.id=tpsm.orderproductsid
                            INNER JOIN ".tbl_transactionproducts." as trp ON trp.transactiontype=4 AND trp.referenceproductid=op.id
                            SET tpsm.stocktypeid = trp.id,tpsm.productid=trp.productid,
                            tpsm.priceid=IFNULL(IF(trp.isvariant=0,(SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.productid=trp.productid LIMIT 1),trp.priceid),''),
                            tpsm.createddate=(SELECT receiveddate FROM ".tbl_goodsreceivednotes." WHERE id=trp.transactionid),
                            tpsm.modifieddate=NOW(),
                            tpsm.action=IF((SELECT type FROM ".tbl_productprocess." WHERE id IN (SELECT productprocessid FROM ".tbl_productprocessdetails." WHERE id=tpsm.referenceid))=0,1,0)                       
                            WHERE tpsm.orderproductsid!=0
                        ");
    }

    public function addsalesorderstock(){
       
        define("HIDE_SELLER_IN_ORDER","1");
        $this->load->model('Order_model', 'Order');
        
        $query=$this->db->select("op.id,op.productid,pp.id as priceid,op.quantity,op.originalprice,op.name,o.orderdate,date(o.createddate) as createddate")
                        ->from(tbl_orders." as o")
                        ->join(tbl_orderproducts." as op","op.orderid=o.id","INNER")
                        ->join(tbl_product." as p","p.id=op.productid","INNER")
                        ->join(tbl_productprices." as pp","pp.productid=p.id","INNER")
                        ->where("o.sellermemberid=0 AND o.memberid!=0 
                            AND IFNULL((SELECT ov.orderproductid FROM ".tbl_ordervariant." as ov WHERE ov.orderid=o.id AND ov.orderproductid=op.id AND ov.priceid=pp.id LIMIT 1),IF(op.isvariant=0,op.id,0))=op.id")
                        ->get();

        $orderdata = $query->result_array();
        // echo "<pre>"; print_r($orderdata); exit;
        if(!empty($orderdata)){
            
            $this->Order->_table = tbl_transactionproductstockmapping;     
            $this->Order->_fields = "*";

            $modifieddate = $this->general_model->getCurrentDateTime();

            foreach($orderdata as $row){
                
                $qty = $row['quantity'];
                $productid = $row['productid'];
                $priceid = $row['priceid'];
                $orderdate = $row['createddate'];

                $stockdata = $this->Order->geProductFIFOStock($productid,$priceid,$orderdate);  
                
                if(!empty($stockdata)){
                    foreach($stockdata as $stock){
                        $stocktype = $stock['stocktype'];
                        $stocktypeid = $stock['stocktypeid']; 
                        $orderqty = $stock['qty'];
                        
                        if($qty > 0){
                            
                            if($orderqty < $qty){
                                $qty = $qty - $orderqty;

                                $this->Order->_where = array("referencetype"=>1,"referenceid"=>$row['id'],"stocktype"=>$stocktype,"stocktypeid"=>$stocktypeid);
                                $Count = $this->Order->getRecordsByID();

                                if(!empty($Count)){

                                    $updatetransactionproductstock = array(
                                                "qty"=>$orderqty,
                                                "action"=>1,
                                            );

                                    $this->Order->_where = array("id"=>$Count['id']);
                                    $this->Order->Edit($updatetransactionproductstock);

                                }else{

                                    $inserttransactionproductstock = array("referencetype"=>1,
                                                "referenceid"=>$row['id'],
                                                "stocktype"=>$stocktype,
                                                "stocktypeid"=>$stocktypeid,
                                                "productid"=>$productid,
                                                "priceid"=>$priceid,
                                                "qty"=>$orderqty,
                                                "action"=>1,
                                                "createddate"=>$orderdate,
                                                "modifieddate"=>$modifieddate
                                            );
    
                                    $this->Order->Add($inserttransactionproductstock);
                                }

                            }else if($orderqty >= $qty){

                                $this->Order->_where = array("referencetype"=>1,"referenceid"=>$row['id'],"stocktype"=>$stocktype,"stocktypeid"=>$stocktypeid);
                                $Count = $this->Order->getRecordsByID();

                                if(!empty($Count)){

                                    $updatetransactionproductstock = array(
                                                "qty"=>$qty,
                                                "action"=>1,
                                                "modifieddate"=>$modifieddate
                                            );

                                    $this->Order->_where = array("id"=>$Count['id']);
                                    $this->Order->Edit($updatetransactionproductstock);

                                }else{

                                    $inserttransactionproductstock = array("referencetype"=>1,
                                                "referenceid"=>$row['id'],
                                                "stocktype"=>$stocktype,
                                                "stocktypeid"=>$stocktypeid,
                                                "productid"=>$productid,
                                                "priceid"=>$priceid,
                                                "qty"=>$qty,
                                                "action"=>1,
                                                "createddate"=>$orderdate,
                                                "modifieddate"=>$modifieddate
                                            );

                                    $this->Order->Add($inserttransactionproductstock);
                                }

                                $qty = 0;
                            }
                        }
                    }
                    if($qty > 0){

                        $this->Order->_where = array("referencetype"=>1,"referenceid"=>$row['id'],"stocktype"=>1,"stocktypeid"=>$row['id']);
                        $Count = $this->Order->getRecordsByID();

                        if(!empty($Count)){

                            $updatetransactionproductstock = array(
                                        "qty"=>$qty,
                                        "action"=>1,
                                        "modifieddate"=>$modifieddate
                                    );

                            $this->Order->_where = array("id"=>$Count['id']);
                            $this->Order->Edit($updatetransactionproductstock);

                        }else{

                            $inserttransactionproductstock = array("referencetype"=>1,
                                "referenceid"=>$row['id'],
                                "stocktype"=>1,
                                "stocktypeid"=>$row['id'],
                                "productid"=>$productid,
                                "priceid"=>$priceid,
                                "qty"=>$qty,
                                "action"=>1,
                                "createddate"=>$orderdate,
                                "modifieddate"=>$modifieddate
                            );

                            $this->Order->Add($inserttransactionproductstock);
                        }
                    }
                }else{

                    $this->Order->_where = array("referencetype"=>1,"referenceid"=>$row['id'],"stocktype"=>1,"stocktypeid"=>$row['id']);
                    $Count = $this->Order->getRecordsByID();

                    if(!empty($Count)){

                        $updatetransactionproductstock = array(
                                    "qty"=>$qty,
                                    "action"=>1,
                                    "modifieddate"=>$modifieddate
                                );

                        $this->Order->_where = array("id"=>$Count['id']);
                        $this->Order->Edit($updatetransactionproductstock);

                    }else{
                        $inserttransactionproductstock = array("referencetype"=>1,
                                                "referenceid"=>$row['id'],
                                                "stocktype"=>1,
                                                "stocktypeid"=>$row['id'],
                                                "productid"=>$productid,
                                                "priceid"=>$priceid,
                                                "qty"=>$qty,
                                                "action"=>1,
                                                "createddate"=>$orderdate,
                                                "modifieddate"=>$modifieddate
                                            );

                        $this->Order->Add($inserttransactionproductstock);
                    }
                }
            }
        }
    }

    public function updatestockgernelvoucherstockentry()
	{
        $this->load->model('Stock_general_voucher_model', 'Stock_general_voucher');
        
        $createddate = $this->general_model->getCurrentDateTime();

        // GET STOCK GERNEL VOUCHER PRODUCT LIST
        $query=$this->db->select("sgp.id,sg.createddate,sgp.productid,sgp.priceid,sgp.quantity,sgp.type,sg.voucherdate")
                        ->from(tbl_stockgeneralvoucherproducts." as sgp")
                        ->join(tbl_stockgeneralvoucher." as sg","sg.id=sgp.stockgeneralvoucherid","INNER")
                        ->where("sg.channelid=0 AND sg.memberid=0 AND sgp.id NOT IN (SELECT referenceid from ".tbl_transactionproductstockmapping." WHERE referencetype=5)")
                        ->get();
        $stockgernelproductdata = $query->result_array();
        
        if(!empty($stockgernelproductdata)){
            $inserttransactionproductstock = array();
            foreach($stockgernelproductdata as $row){
              
                $stockgeneralvoucherproductsid = $row['id'];
                $productid = $row['productid'];
                $priceid = $row['priceid'];
                $quantity = $row['quantity'];
                $type = $row['type'];
                
                $action = ($type==1)?0:1;
    
                $inserttransactionproductstock[] = array("referencetype"=>5,
                                "referenceid"=>$stockgeneralvoucherproductsid,
                                "stocktype"=>2, 
                                "stocktypeid"=>$stockgeneralvoucherproductsid,
                                "productid"=>$productid,
                                "priceid"=>$priceid,
                                "qty"=>$quantity,
                                "action"=>$action,
                                "createddate"=>$this->general_model->convertdatetime($row['voucherdate']),
                                "modifieddate"=>$createddate
                            );
            }
            
            if(!empty($inserttransactionproductstock)){
                $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                $this->Stock_general_voucher->Add_batch($inserttransactionproductstock);
            }
        }
    }
    /* public function updatelandingcostofinprocess(){
        define("HIDE_SELLER_IN_ORDER","1");
        
        $query=$this->db->select("pp.id,pp.parentproductprocessid")
                        ->from(tbl_productprocess." as pp")
                        ->where("pp.type=1 AND pp.id IN (SELECT productprocessid FROM ".tbl_productprocessdetails." WHERE landingcost=0)")
                        ->get();
        $processdata = $query->result_array();
        
        if(!empty($processdata)){
            echo "<pre>"; print_r($processdata); exit;
            $this->load->model('Product_process_model', 'Product_process');

            foreach($processdata as $process){

                $calculatelandingcost = 0;
                $parentproductprocessid = $process['parentproductprocessid'];
        
                $query=$this->db->select("pdp.id,pdp.productprocessid,pdp.landingcost,pp.parentproductprocessid")
                        ->from(tbl_productprocess." as pp")
                        ->join(tbl_productprocessdetails." as pdp","pdp.productprocessid=pp.id","INNER")
                        ->where("pp.type=1 AND pdp.landingcost=0")
                        ->get();
                $processdata = $query->result_array();


                $productprocessdata = $this->Product_process->getProductProcessDetailsById($parentproductprocessid);
                
                if(!empty($productprocessdata['outproducts'])){
                    foreach($productprocessdata['outproducts'] as $op){ 
                        $calculatelandingcost += $op['price']*$op['quantity'];
                    }
                }
                if(!empty($productprocessdata['outextracharges'])){
                    foreach($productprocessdata['outextracharges'] as $oc){ 
                        $calculatelandingcost += $oc['amount'];
                    }
                }
                if(!empty($productidarray)){
                    foreach($productidarray as $c=>$productid){
                        if($productid > 0 && $productvariantid[$c] > 0 && ($processtype == "IN" || $quantity[$c] > 0)){
                            $calculatelandingcost += $productprice[$c]*$quantity[$c];
                        }
                    }
                }
                if(!empty($extrachargesidarr)){
                    foreach($extrachargesidarr as $index=>$extrachargesid){
    
                        if($extrachargesid > 0){
                            $calculatelandingcost += $extrachargeamountarr[$index];
                        }
                    }
                }
                $calculatelandingcost = $calculatelandingcost / count($productidarray);
            }
        }
    } */
}