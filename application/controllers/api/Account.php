<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends MY_Controller {

    public $PostData = array();
    public $data = array();

    function __construct() {
      parent::__construct();
    }
    function getinvoice(){

        if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
            $this->PostData = $this->input->post();
  
            $PostData = json_decode($this->PostData['data'],true);      
            $counter =  !empty($PostData['UniqueNumber']) ? trim($PostData['UniqueNumber']) : 0;
            $time =  !empty($PostData['time']) ? $PostData['time'] : '';
            
            $limit = 10;
            $this->load->model('Order_model','Order');        
            $this->load->model('Product_model','Product');           
            
            $where_sql = "";
            if($time != ""){
               $where_sql = ' AND o.createddate >= "'.$time.'"'; 
            }

            $this->readdb->select("o.id,o.memberid,o.sellermemberid,
                        buyer.name as accountname,buyer.gstno as buyergstno,o.billingname,IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                        o.billingaddress,o.billingpostalcode,IFNULL(ct.name,'') as cityname,IFNULL(pr.name,'') as provincename, IFNULL(cn.name,'') as countryname,o.billingmobileno,o.billingemail,
                        o.shippingname,o.shippingaddress,o.shippingpostalcode,IFNULL(shipct.name,'') as shipcityname,IFNULL(shippr.name,'') as shipprovincename, IFNULL(shipcn.name,'') as shipcountryname,o.shippingmobileno,o.shippingemail,

                        o.paymenttype,o.orderid,o.orderdate,o.payableamount as amount,o.globaldiscount
            ");

            $this->readdb->from($this->Order->_table." as o");
            $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT");
            $this->readdb->join(tbl_city." as ct","ct.id=o.billingcityid","LEFT");
            $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
            $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","LEFT");
            $this->readdb->join(tbl_city." as shipct","shipct.id=o.shippingcityid","LEFT");
            $this->readdb->join(tbl_province." as shippr","shippr.id=shipct.stateid","LEFT");
            $this->readdb->join(tbl_country." as shipcn","shipcn.id=shippr.countryid","LEFT");
            $this->readdb->where("o.status=1 AND o.approved=1 AND o.memberid!=0".$where_sql);
            $this->readdb->order_by('o.id DESC');
            $this->readdb->limit($limit,$counter);
            $query = $this->readdb->get();
      
            if($query->num_rows() > 0) {
                $data = $query->result_array();
                $response = $json = array();
                if(!empty($data)){
                    foreach($data as $index=>$row){

                        $acoountdata =  array("AccountName"=>$row['accountname'],
                                            "GSTNumber"=>$row['buyergstno'],
                                            "RegistrationType"=>1,
                                            "ContactDetails" => array(array("ContacPerson1"=>$row['billingname'],
                                                                        "ContacPerson2"=>"",
                                                                        "Address"=>$row['billingaddress'],
                                                                        "City"=>$row['cityname'],
                                                                        "State"=>$row['provincename'],
                                                                        "Country"=>$row['countryname'],
                                                                        "ZipCode"=>$row['billingpostalcode'],
                                                                        "PhoneNumberOffice1"=>"",
                                                                        "PhoneNumberOffice2"=>"",
                                                                        "PhoneNumberResident1"=>"",
                                                                        "PhoneNumberResident2"=>"",
                                                                        "FaxNumber1"=>"",
                                                                        "FaxNumber2"=>"",
                                                                        "MobileNumber1"=>$row['billingmobileno'],
                                                                        "MobileNumber2"=>"",
                                                                        "EMail"=>$row['billingemail']
                                                                    ),
                                                                    array("ContacPerson1"=>$row['shippingname'],
                                                                        "ContacPerson2"=>"",
                                                                        "Address"=>$row['shippingaddress'],
                                                                        "City"=>$row['shipcountryname'],
                                                                        "State"=>$row['shipcountryname'],
                                                                        "Country"=>$row['shipcountryname'],
                                                                        "ZipCode"=>$row['shippingpostalcode'],
                                                                        "PhoneNumberOffice1"=>"",
                                                                        "PhoneNumberOffice2"=>"",
                                                                        "PhoneNumberResident1"=>"",
                                                                        "PhoneNumberResident2"=>"",
                                                                        "FaxNumber1"=>"",
                                                                        "FaxNumber2"=>"",
                                                                        "MobileNumber1"=>$row['shippingmobileno'],
                                                                        "MobileNumber2"=>"",
                                                                        "EMail"=>$row['shippingemail']
                                                                )),
                                            );

                        $response[$index]['AccountData'] = $acoountdata;

                        $query = $this->readdb->select("o.id,CONCAT(o.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=o.id),'')) as ItemName,o.originalprice as ItemPrice,
                        IFNULL((SELECT name FROM ".tbl_productunit." WHERE id IN (SELECT unitid FROM ".tbl_productprices." WHERE productid=p.id GROUP BY productid)),'') as unitName,
                        o.quantity as Quantity,o.price as Rate,o.tax")
                                            ->from(tbl_orderproducts." as o")
                                            ->join(tbl_product." as p","p.id=o.productid","LEFT")
                                            ->where("o.orderid=".$row['id'])
                                            ->get();
                                            
                        $productdata =  $query->result_array();

                        $productdetails = array();
                        if(!empty($productdata)){
                            foreach($productdata as $product){

                                if($row['igst'] == 1){
                                    $cgst = number_format(($product['tax']/2), 2, ".", ",");
                                    $sgst = number_format(($product['tax']/2), 2, ".", ",");
                                    $igst = number_format(0, 2, ".", ",");
                                }else{
                                    $cgst = number_format(0, 2, ".", ",");
                                    $sgst = number_format(0, 2, ".", ",");
                                    $igst = number_format($product['tax'], 2, ".", ",");
                                }

                                $variantdata = $this->readdb->select("variantname as attribute,variantvalue as variantname")
                                        ->from(tbl_ordervariant)
                                        ->where(array("orderproductid"=>$product['id']))
                                        ->get()->result_array();
                                $variantdetail = array();
                                if(!empty($variantdata)){
                                    foreach($variantdata as $variant){
                                        $variantdetail[] = array("Attribute"=>$variant['attribute'],
                                                                "Variant"=>$variant['variantname']);
                                    }
                                }

                                $productdetails[] = array("ItemName"=>$product['ItemName'],
                                                        "ItemPrice"=>$product['ItemPrice'],
                                                        "VariantDetail"=>$variantdetail,
                                                        "unitName"=>$product['unitName'],
                                                        "Quantity"=>$product['Quantity'],
                                                        "Rate"=>$product['Rate'],
                                                        "GSTPercentage"=> array(
                                                                            array("CGST"=>$cgst),
                                                                            array("SGST"=>$sgst),
                                                                            array("IGST"=>$igst)
                                                                        )
                                                    );

                            }
                        }
                        
                        $DiscountPercentage = ($row['globaldiscount']>0?($row['globaldiscount']*100/$row['amount']):0);
                        $invoicedetaildata = array("CashOrDebit"=>($row['paymenttype']==0?0:1),
                                                    "InvoiceNumber"=>$row['orderid'],
                                                    "InvoiceDate"=>$row['orderdate'],
                                                    "Amount"=>$row['amount'],
                                                    "DiscountPercentage"=>$DiscountPercentage,
                                                    "Narration"=>"",
                                                    "TransporterName"=>"",
                                                    "LRNo"=>"",
                                                    "LRDate"=>"",
                                                    "EWayBillNumber"=>"",
                                                    "SalesAccountName"=>"",
                                                    "BillType"=>array(
                                                                    array("TaxInvoice"=>1),
                                                                    array("BillOfSupply"=>0),
                                                                    array("OtherInvoice"=>0)
                                                                ),
                                                    "ProductDetails"=> $productdetails,


                    
                                                    );

                        $response[$index]['InvoiceDetails'] = $invoicedetaildata;
                        
                        
                    }
                }

                $this->readdb->select("o.id");
                $this->readdb->from($this->Order->_table." as o");
                $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT");
                $this->readdb->join(tbl_city." as ct","ct.id=o.billingcityid","LEFT");
                $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
                $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","LEFT");
                $this->readdb->join(tbl_city." as shipct","shipct.id=o.shippingcityid","LEFT");
                $this->readdb->join(tbl_province." as shippr","shippr.id=shipct.stateid","LEFT");
                $this->readdb->join(tbl_country." as shipcn","shipcn.id=shippr.countryid","LEFT");
                $this->readdb->where("o.status=1 AND o.approved=1 AND o.memberid!=0".$where_sql);
                $this->readdb->order_by('o.id DESC');
                $Count = $this->readdb->get()->result_array();

                $json['CompanyName'] = COMPANY_NAME;
                $json['InvoiceData'] = $response;
                $json['TotalRecords'] = count($Count);
               
                ws_response('success', "Data synced successfully...", $json);
            } else {
                ws_response('fail', "No data available...");
            }

        }
    }

    function getpurchaseinvoice(){

        if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
            $this->PostData = $this->input->post();
  
            $PostData = json_decode($this->PostData['data'],true);      
            $counter =  !empty($PostData['UniqueNumber']) ? trim($PostData['UniqueNumber']) : 0;
            $time =  !empty($PostData['time']) ? $PostData['time'] : '';
            
            $limit = 10;
            $this->load->model('Order_model','Order');        
            $this->load->model('Product_model','Product');           
            
            $where_sql = "";
            if($time != ""){
               $where_sql = ' AND o.createddate >= "'.$time.'"'; 
            }

            $this->readdb->select("o.id,o.memberid,o.sellermemberid,
                        vendor.name as accountname,
                        vendor.gstno as vendorgstno,
                        o.billingname,
                        IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                        o.billingaddress,
                        o.billingpostalcode,
                        IFNULL(ct.name,'') as cityname,
                        IFNULL(pr.name,'') as provincename, 
                        IFNULL(cn.name,'') as countryname,
                        o.billingmobileno,o.billingemail,
                        o.shippingname,o.shippingaddress,o.shippingpostalcode,
                        IFNULL(shipct.name,'') as shipcityname,
                        IFNULL(shippr.name,'') as shipprovincename, 
                        IFNULL(shipcn.name,'') as shipcountryname,
                        o.shippingmobileno,o.shippingemail,

                        o.paymenttype,o.orderid,o.orderdate,o.payableamount as amount,o.globaldiscount
            ");

            $this->readdb->from($this->Order->_table." as o");
            $this->readdb->join(tbl_member." as vendor","vendor.id=o.sellermemberid","LEFT");
            $this->readdb->join(tbl_city." as ct","ct.id=o.billingcityid","LEFT");
            $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
            $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","LEFT");
            $this->readdb->join(tbl_city." as shipct","shipct.id=o.shippingcityid","LEFT");
            $this->readdb->join(tbl_province." as shippr","shippr.id=shipct.stateid","LEFT");
            $this->readdb->join(tbl_country." as shipcn","shipcn.id=shippr.countryid","LEFT");
            $this->readdb->where("o.status=1 AND o.approved=1 AND o.memberid=0 AND o.sellermemberid!=0".$where_sql);
            $this->readdb->order_by('o.id DESC');
            $this->readdb->limit($limit,$counter);
            $query = $this->readdb->get();
      
            if($query->num_rows() > 0) {
                $data = $query->result_array();
                $response = $json = array();
                if(!empty($data)){
                    foreach($data as $index=>$row){

                        $accountdata =  array("AccountName"=>$row['accountname'],
                                            "GSTNumber"=>$row['vendorgstno'],
                                            "RegistrationType"=>1,
                                            "ContactDetails" => array(array("ContacPerson1"=>$row['billingname'],
                                                                        "ContacPerson2"=>"",
                                                                        "Address"=>$row['billingaddress'],
                                                                        "City"=>$row['cityname'],
                                                                        "State"=>$row['provincename'],
                                                                        "Country"=>$row['countryname'],
                                                                        "ZipCode"=>$row['billingpostalcode'],
                                                                        "PhoneNumberOffice1"=>"",
                                                                        "PhoneNumberOffice2"=>"",
                                                                        "PhoneNumberResident1"=>"",
                                                                        "PhoneNumberResident2"=>"",
                                                                        "FaxNumber1"=>"",
                                                                        "FaxNumber2"=>"",
                                                                        "MobileNumber1"=>$row['billingmobileno'],
                                                                        "MobileNumber2"=>"",
                                                                        "EMail"=>$row['billingemail']
                                                                    ),
                                                                    array("ContacPerson1"=>$row['shippingname'],
                                                                        "ContacPerson2"=>"",
                                                                        "Address"=>$row['shippingaddress'],
                                                                        "City"=>$row['shipcountryname'],
                                                                        "State"=>$row['shipcountryname'],
                                                                        "Country"=>$row['shipcountryname'],
                                                                        "ZipCode"=>$row['shippingpostalcode'],
                                                                        "PhoneNumberOffice1"=>"",
                                                                        "PhoneNumberOffice2"=>"",
                                                                        "PhoneNumberResident1"=>"",
                                                                        "PhoneNumberResident2"=>"",
                                                                        "FaxNumber1"=>"",
                                                                        "FaxNumber2"=>"",
                                                                        "MobileNumber1"=>$row['shippingmobileno'],
                                                                        "MobileNumber2"=>"",
                                                                        "EMail"=>$row['shippingemail']
                                                                )),
                                            );

                        $response[$index]['AccountData'] = $accountdata;

                        $query = $this->readdb->select("o.id,
                            CONCAT(o.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=o.id),'')) as ItemName,
                            o.originalprice as ItemPrice,
                            IFNULL((SELECT name FROM ".tbl_productunit." WHERE id IN (SELECT unitid FROM ".tbl_productprices." WHERE productid=p.id GROUP BY productid)),'') as unitName,
                            o.quantity as Quantity,o.price as Rate,o.tax
                        ")
                                            ->from(tbl_orderproducts." as o")
                                            ->join(tbl_product." as p","p.id=o.productid","LEFT")
                                            ->where("o.orderid=".$row['id'])
                                            ->get();
                                            
                        $productdata =  $query->result_array();

                        $productdetails = array();
                        if(!empty($productdata)){
                            foreach($productdata as $product){

                                if($row['igst'] == 1){
                                    $cgst = number_format(($product['tax']/2), 2, ".", ",");
                                    $sgst = number_format(($product['tax']/2), 2, ".", ",");
                                    $igst = number_format(0, 2, ".", ",");
                                }else{
                                    $cgst = number_format(0, 2, ".", ",");
                                    $sgst = number_format(0, 2, ".", ",");
                                    $igst = number_format($product['tax'], 2, ".", ",");
                                }

                                $variantdata = $this->readdb->select("variantname as attribute,variantvalue as variantname")
                                        ->from(tbl_ordervariant)
                                        ->where(array("orderproductid"=>$product['id']))
                                        ->get()->result_array();
                                $variantdetail = array();
                                if(!empty($variantdata)){
                                    foreach($variantdata as $variant){
                                        $variantdetail[] = array("Attribute"=>$variant['attribute'],
                                                                "Variant"=>$variant['variantname']);
                                    }
                                }

                                $productdetails[] = array("ItemName"=>$product['ItemName'],
                                                        "ItemPrice"=>$product['ItemPrice'],
                                                        "VariantDetail"=>$variantdetail,
                                                        "unitName"=>$product['unitName'],
                                                        "Quantity"=>$product['Quantity'],
                                                        "Rate"=>$product['Rate'],
                                                        "GSTPercentage"=> array(
                                                                            array("CGST"=>$cgst),
                                                                            array("SGST"=>$sgst),
                                                                            array("IGST"=>$igst)
                                                                        )
                                                    );

                            }
                        }
                        
                        $DiscountPercentage = ($row['globaldiscount']>0?number_format($row['globaldiscount']*100/$row['amount'],2,'.',''):0);
                        $invoicedetaildata = array("CashOrDebit"=>($row['paymenttype']==0?0:1),
                                                    "InvoiceNumber"=>$row['orderid'],
                                                    "InvoiceDate"=>$row['orderdate'],
                                                    "Amount"=>$row['amount'],
                                                    "DiscountPercentage"=>$DiscountPercentage,
                                                    "Narration"=>"",
                                                    "TransporterName"=>"",
                                                    "LRNo"=>"",
                                                    "LRDate"=>"",
                                                    "EWayBillNumber"=>"",
                                                    "SalesAccountName"=>"",
                                                    "BillType"=>array(
                                                                    array("TaxInvoice"=>1),
                                                                    array("BillOfSupply"=>0),
                                                                    array("OtherInvoice"=>0)
                                                                ),
                                                    "ProductDetails"=> $productdetails,


                    
                                                    );

                        $response[$index]['InvoiceDetails'] = $invoicedetaildata;
                        
                        
                    }
                }

                $this->readdb->select("o.id");
                $this->readdb->from($this->Order->_table." as o");
                $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid","LEFT");
                $this->readdb->join(tbl_city." as ct","ct.id=o.billingcityid","LEFT");
                $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
                $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","LEFT");
                $this->readdb->join(tbl_city." as shipct","shipct.id=o.shippingcityid","LEFT");
                $this->readdb->join(tbl_province." as shippr","shippr.id=shipct.stateid","LEFT");
                $this->readdb->join(tbl_country." as shipcn","shipcn.id=shippr.countryid","LEFT");
                $this->readdb->where("o.status=1 AND o.approved=1 AND o.memberid=0 AND o.sellermemberid!=0".$where_sql);
                $this->readdb->order_by('o.id DESC');
                $Count = $this->readdb->get()->result_array();

                $json['CompanyName'] = COMPANY_NAME;
                $json['InvoiceData'] = $response;
                $json['TotalRecords'] = count($Count);
               
                ws_response('success', "Data synced successfully...", $json);
            } else {
                ws_response('fail', "No data available...");
            }

        }
    }

    function getpaymentreceipt(){

        if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
            $this->PostData = $this->input->post();
  
            $PostData = json_decode($this->PostData['data'],true);      
            $counter =  !empty($PostData['UniqueNumber']) ? trim($PostData['UniqueNumber']) : 0;
            $time =  !empty($PostData['time']) ? $PostData['time'] : '';
            
            $limit = 10;
            $this->load->model('Payment_model','Payment');        
            $this->load->model('Product_model','Product');           
            
            $where_sql = "";
            if($time != ""){
               $where_sql = ' AND pr.transactiondate >= "'.$time.'"'; 
            }

            $this->readdb->select("pr.id,pr.memberid,pr.sellermemberid,

                        IFNULL(m.id,0) as partyid,
                        IFNULL(m.name,'') as accountname,
                        IFNULL(m.gstno,'') as buyergstno,
                        IFNULL(m.billingaddressid,0) as billingaddressid,
                        IFNULL(m.shippingaddressid,0) as shippingaddressid,

                        IF(pr.type=1,'true','false') as isPayment,
                        cb.name as bankname,
                        cb.branchname,cb.branchaddress,cb.ifsccode,
                        pr.paymentreceiptno,pr.transactiondate,pr.amount,pr.cashorbankid,pr.sellercashorbankid,pr.method,pr.remarks,

                        IF(pr.isagainstreference=1,'Against By Invoice','On Account') as isagainstreference,

                        
            ");

            $this->readdb->from($this->Payment->_table." as pr");
            $this->readdb->join(tbl_member." as m","m.id=IF(pr.memberid=0,pr.sellermemberid,pr.memberid)","LEFT");
            $this->readdb->join(tbl_cashorbank." as cb","cb.id=pr.cashorbankid","LEFT");
            // $this->readdb->join(tbl_member." as m2","m2.id=pr.sellermemberid","LEFT");
            $this->readdb->where("pr.status=1 AND (pr.memberid=0 OR pr.sellermemberid=0)".$where_sql);
            $this->readdb->order_by('pr.id DESC');
            $this->readdb->limit($limit,$counter);
            $query = $this->readdb->get();
      
            if($query->num_rows() > 0) {
                $data = $query->result_array();
                // print_r($data); exit;
                $response = $json = array();
                if(!empty($data)){
                    foreach($data as $index=>$row){
                        
                        if($row['billingaddressid']!=0){
                            $billingwhere = "billing.id=".$row['billingaddressid'];
                        }else{
                            $billingwhere = "billing.memberid=".$row['partyid'];
                        }
                        $query = $this->readdb->select("billing.name as billingname,
                                                billing.address as billingaddress,
                                                billing.postalcode as billingpostalcode,
                                                billing.mobileno as billingmobileno,
                                                billing.email as billingemail,
                                                IFNULL(ct.name,'') as cityname,
                                                IFNULL(pr.name,'') as provincename,
                                                IFNULL(cn.name,'') as countryname,
                                                
                                            ")
                                            ->from(tbl_memberaddress." as billing")
                                            ->join(tbl_city." as ct","ct.id=billing.cityid","LEFT")
                                            ->join(tbl_province." as pr","pr.id=billing.provinceid","LEFT")
                                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                                            ->where($billingwhere)
                                            ->order_by("billing.id DESC")
                                            ->limit(1)
                                            ->get();
                                            
                        $billingdata =  $query->row_array();

                        if($row['shippingaddressid']!=0){
                            $shippingwhere = "shipping.id=".$row['shippingaddressid'];
                        }else{
                            $shippingwhere = "shipping.memberid=".$row['partyid'];
                        }
                        $query = $this->readdb->select("shipping.name as shippingname,
                                                shipping.address as shippingaddress,
                                                shipping.postalcode as shippingpostalcode,
                                                shipping.mobileno as shippingmobileno,
                                                shipping.email as shippingemail,
                                                IFNULL(ct.name,'') as shipcityname,
                                                IFNULL(pr.name,'') as shipprovincename,
                                                IFNULL(cn.name,'') as shipcountryname,
                                                
                                            ")
                                            ->from(tbl_memberaddress." as shipping")
                                            ->join(tbl_city." as ct","ct.id=shipping.cityid","LEFT")
                                            ->join(tbl_province." as pr","pr.id=shipping.provinceid","LEFT")
                                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                                            ->where($shippingwhere)
                                            ->order_by("shipping.id DESC")
                                            ->limit(1)
                                            ->get();
                                            
                        $shippingdata =  $query->row_array();

                        $PartyData =  array("AccountName"=>$row['accountname'],
                                            "GSTNumber"=>$row['buyergstno'],
                                            "RegistrationType"=>1,
                                            "ContactDetails" => array(
                                                  array("ContacPerson1"=>(!empty($billingdata)?$billingdata['billingname']:""),
                                                        "ContacPerson2"=>"",
                                                        "Address"=>(!empty($billingdata)?$billingdata['billingaddress']:""),
                                                        "City"=>(!empty($billingdata)?$billingdata['cityname']:""),
                                                        "State"=>(!empty($billingdata)?$billingdata['provincename']:""),
                                                        "Country"=>(!empty($billingdata)?$billingdata['countryname']:""),
                                                        "ZipCode"=>(!empty($billingdata)?$billingdata['billingpostalcode']:""),
                                                        "PhoneNumberOffice1"=>"",
                                                        "PhoneNumberOffice2"=>"",
                                                        "PhoneNumberResident1"=>"",
                                                        "PhoneNumberResident2"=>"",
                                                        "FaxNumber1"=>"",
                                                        "FaxNumber2"=>"",
                                                        "MobileNumber1"=>(!empty($billingdata)?$billingdata['billingmobileno']:""),
                                                        "MobileNumber2"=>"",
                                                        "EMail"=>(!empty($billingdata)?$billingdata['billingemail']:""),
                                                    ),
                                                    array("ContacPerson1"=>(!empty($shippingdata)?$shippingdata['shippingname']:""),
                                                        "ContacPerson2"=>"",
                                                        "Address"=>(!empty($shippingdata)?$shippingdata['shippingaddress']:""),
                                                        "City"=>(!empty($shippingdata)?$shippingdata['shipcityname']:""),
                                                        "State"=>(!empty($shippingdata)?$shippingdata['shipprovincename']:""),
                                                        "Country"=>(!empty($shippingdata)?$shippingdata['shipcountryname']:""),
                                                        "ZipCode"=>(!empty($shippingdata)?$shippingdata['shippingpostalcode']:""),
                                                        "PhoneNumberOffice1"=>"",
                                                        "PhoneNumberOffice2"=>"",
                                                        "PhoneNumberResident1"=>"",
                                                        "PhoneNumberResident2"=>"",
                                                        "FaxNumber1"=>"",
                                                        "FaxNumber2"=>"",
                                                        "MobileNumber1"=>(!empty($shippingdata)?$shippingdata['shippingmobileno']:""),
                                                        "MobileNumber2"=>"",
                                                        "EMail"=>(!empty($shippingdata)?$shippingdata['shippingemail']:"")
                                                )),
                                            );

                        $response[$index]['PartyData'] = $PartyData;
                        
                        $response[$index]['CashOrBankDetail'] = array(
                            "Method" => isset($this->Bankmethod[$row['method']])?$this->Bankmethod[$row['method']]:"",
                            "AccountName" => $row['bankname'],
                            "BranchName" => $row['branchname'],
                            "IFSCCode" => $row['ifsccode'],
                            "BranchAddress" => $row['branchaddress'],
                        );

                        $query = $this->readdb->select("
                                            i.invoiceno as InvoiceNumber,
                                            i.invoicedate as InvoiceDate,
                                            CAST(prt.amount AS DECIMAL(14,2)) as InvoiceAmount,
                                        ")
                                                 
                                        ->from(tbl_paymentreceipttransactions." as prt")
                                        ->join(tbl_invoice." as i","i.id=prt.invoiceid","LEFT")
                                        ->where("prt.paymentreceiptid=".$row['id'])
                                        ->get();
                    
                        $invoicedata = $query->result_array();

                        $response[$index]['TransactionDetail'] = array(
                            "PaymentType" => $row['isagainstreference'],
                            "InvoiceDetails" => $invoicedata,
                            "Amount" => number_format($row['amount'],2,'.',''),
                            "IsPayment" => $row['isPayment'],
                            "Narration" => $row['remarks'],
                        );
                    }
                }

                $this->readdb->select("pr.id");
                $this->readdb->from($this->Payment->_table." as pr");
                $this->readdb->where("pr.status=1 AND (pr.memberid=0 OR pr.sellermemberid=0)".$where_sql);
                $Count = $this->readdb->get()->result_array();

                $json['CompanyName'] = COMPANY_NAME;
                $json['PaymentReceiptData'] = $response;
                $json['TotalRecords'] = count($Count);
               
                ws_response('success', "Data synced successfully...", $json);
            } else {
                ws_response('fail', "No data available...");
            }

        }
    }
}