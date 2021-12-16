<?php
class Invoice_model extends Common_model 
{
	//put your code here
	public $_table = tbl_invoice;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'buyername','sellername',null,'i.invoiceno','i.invoicedate','statusname','netamount'); //set column field database for datatable orderable
    
    public $column_search = array('IFNULL(seller.name,"Company")','IFNULL(buyer.name,"Company")','IFNULL(seller.membercode,"")','IFNULL(buyer.membercode,"")','i.invoiceno','i.invoicedate','i.amount','(SELECT GROUP_CONCAT(o.orderid) FROM '.tbl_orders.' as o WHERE FIND_IN_SET(o.id,i.orderid)>0)'); //set column field database for datatable searchable 
	public $order = array('i.id' => 'DESC'); // default order  

	function __construct() {
		parent::__construct();
	}
    function sendInvoiceMailToBuyer($InvoiceData,$file){
        /***************send email to buyer***************************/
        if(!empty($InvoiceData)){
            $buyername = $InvoiceData['transactiondetail']['buyername'];
            $buyeremail = $InvoiceData['transactiondetail']['buyeremail'];
            
            if(!empty($buyeremail)){
                $mailto = $buyeremail;
                $from_mail = explode(",",COMPANY_EMAIL)[0];
                $from_name = COMPANY_NAME;

                $subject= array("{companyname}"=>COMPANY_NAME,"{invoicenumber}"=>$InvoiceData['transactiondetail']['invoiceno']);
                $totalamount = round($InvoiceData['transactiondetail']['netamount']);

                $mailBodyArr = array(
                            "{logo}" => '<a href="'. DOMAIN_URL.'"><img src="' . MAIN_LOGO_IMAGE_URL. COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                            "{invoicenumber}" => $InvoiceData['transactiondetail']['invoiceno'],
                            "{invoicedate}" => $this->general_model->displaydate($InvoiceData['transactiondetail']['invoicedate']),
                            "{invoiceaddress}" => "<p>".$InvoiceData['transactiondetail']['membername']."<br>".$InvoiceData['transactiondetail']['address']."<br>"."<b>Tel/Mobile : </b> ".$InvoiceData['transactiondetail']['mobileno']."<br>"."<b>Email : </b> ".$InvoiceData['transactiondetail']['email']."</p>",
                            "{paymentdate}" => $this->general_model->displaydate($InvoiceData['transactiondetail']['createddate']),
                            "{paymentmethod}" => $InvoiceData['transactiondetail']['paymentmethod'],
                            "{amount}" => numberFormat(round($totalamount),2,','),
                            "{companyname}" => COMPANY_NAME,
                            "{companyemail}" => explode(",",COMPANY_EMAIL)[0]
                        );
                
                //Send mail with email format store in database
                $mailid=array_search("Invoice",$this->Emailformattype);
                $emailSend = $this->Invoice->mail_attachment($file, ORDER_PATH, $mailto, $from_mail, $from_name, $from_mail, $subject, $mailBodyArr,$mailid);

                return $emailSend;
            }
        }
        return false;
    }
    function exportinvoicedata(){
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $buyerchannelid = (isset($_REQUEST['buyerchannelid']))?$_REQUEST['buyerchannelid']:'';
        $buyermemberid = (isset($_REQUEST['buyermemberid']))?$_REQUEST['buyermemberid']:'';
        $sellerchannelid = (isset($_REQUEST['sellerchannelid']))?$_REQUEST['sellerchannelid']:'';
        $sellermemberid = (isset($_REQUEST['sellermemberid']))?$_REQUEST['sellermemberid']:'';
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

		$this->readdb->select("i.id,i.sellermemberid,i.memberid,
                            (SELECT o.orderid FROM ".tbl_orders." as o WHERE o.id=op.orderid) as ordernumber,
                            i.invoiceno,
                            i.invoicedate,
                            i.taxamount,
                            i.amount,
                            i.courierid,
                            
                            @netamount:=IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,

                            i.status,
                            IFNULL(seller.name,'') as sellername,
                            IFNULL(seller.membercode,'') as sellercode,
                            IFNULL(i.sellermemberid,'') as sellerid,
                            IFNULL(seller.channelid,'') as sellerchannelid,

                            buyer.name as buyername,
                            buyer.membercode as buyercode,
                            buyer.countrycode as buyercountrycode,
                            buyer.mobile as buyermobileno,
                            buyer.address as buyeraddress,
                            ct.name as buyercity,
                            pr.name as buyerstate,
                            buyer.pincode as buyerpincode,
                            buyer.gstno as buyergstno,

                            trp.name as productname,
                            IFNULL((SELECT sku FROM ".tbl_productprices." WHERE id=IF(trp.priceid=0,(SELECT id FROM ".tbl_productprices." WHERE productid=trp.productid LIMIT 1),trp.priceid)),'') as sku,
                            p.shortdescription as productdescription,
                            trp.quantity,
                            trp.hsncode,
                            trp.price,
                            trp.tax,
                            IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
         
                        ");

        $this->readdb->from($this->_table." as i");
        $this->readdb->join(tbl_transactionproducts." as trp","trp.transactionid=i.id AND trp.transactiontype=3","INNER");
        $this->readdb->join(tbl_orderproducts." as op","op.id=trp.referenceproductid","INNER");
        $this->readdb->join(tbl_product." as p","p.id=op.productid","LEFT");
        $this->readdb->join(tbl_member." as buyer","buyer.id=i.memberid","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=buyer.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT");
        if(!is_null($MEMBERID)){
            $type = isset($_REQUEST['type'])?$_REQUEST['type']:'sales';
            if($type=="purchase"){
                $this->readdb->where("i.memberid=".$MEMBERID." AND i.sellermemberid IN  (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.")");
            }else{
                $this->readdb->where("i.sellermemberid=".$MEMBERID);
            }
        }
        if(!empty($buyerchannelid)){
            $this->readdb->where("buyer.channelid=".$buyerchannelid); //Filter buyer Channel
           
            if(!empty($buyermemberid)){
                $this->readdb->where("buyer.id IN (".$buyermemberid.")"); //Filter buyer member
            }
        }
        if($sellerchannelid!=""){
            if($sellerchannelid!=0){
                $this->readdb->where("seller.channelid=".$sellerchannelid); //Filter seller channel
                
                if(!empty($sellermemberid)){
                    $this->readdb->where("seller.id IN (".$sellermemberid.")"); //Filter seller member
                }
            }else{
                $this->readdb->where("i.sellermemberid=0"); //Filter seller 
            }
        }
        if($status != -1){
            $this->readdb->where("i.status=".$status);
        }
        $this->readdb->where("(i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."') AND i.memberid!=0");
        $this->readdb->order_by(key($this->order), $this->order[key($this->order)]);
        $query = $this->readdb->get();
        
        return $query->result(); 
    }
    function exportinvoice(){

        $exportdata = $this->exportinvoicedata();
        $data = array();
        foreach ($exportdata as $row) {         

            $taxamount = ($row->price * $row->tax / 100);
            $netprice = $row->price + $taxamount;
            $taxableamount = number_format($row->price * $row->quantity,2,'.','');
         
            $cgstamount = $sgstamount = $igstamount = 0;
            if($row->igst==1){
                $cgstamount = number_format(($taxamount * $row->quantity / 2),2,'.','');
                $sgstamount = number_format(($taxamount * $row->quantity / 2),2,'.','');
            }else{
                $igstamount = number_format($taxamount * $row->quantity,2,'.','');
            }
            $netamount = $netprice * $row->quantity;
            
            $data[] = array($row->invoiceno,
                            $this->general_model->displaydate($row->invoicedate),
                            '',
                            $row->ordernumber,
                            '',
                            'ACTIVE',
                            $row->buyername.' ('.$row->buyercode.')',
                            $row->buyercountrycode."-".$row->buyermobileno,
                            $row->buyeraddress,
                            '',
                            '',
                            $row->buyercity,
                            $row->buyerstate,
                            $row->buyerpincode,
                            $row->buyergstno,
                            $row->sku,
                            $row->productdescription,
                            $row->quantity,
                            $row->hsncode,
                            numberFormat($row->price,2,','),
                            numberFormat($netprice,2,','),
                            '0.0',
                            $row->tax,
                            '0.0',
                            numberFormat($taxableamount,2,','),
                            '0.0',
                            numberFormat($cgstamount,2,','),
                            numberFormat($sgstamount,2,','),
                            numberFormat($igstamount,2,','),
                            '0.0',
                            numberFormat($netamount,2,','),
                        );
        }
            
        $headings = array('Invoice Id','Invoice Date','Warehouse/Godown Id ','Order Id','Shipment Id','State','Buyer Name','Buyer Ph Num','Buyer Address Line 1','Buyer Address Line 2','Buyer Address Line 3','Buyer City','Buyer State','Buyer Pincode','Buyer GSTIN','Product Id','Description','Quantity','Hsn','Unit Price','Net Price','Market Fees %','Gst %','Cess %','Taxable Amount','Market Fees Amount','Cgst Amount','Sgst Amount','Igst Amount','Cess Amount','Line Total','Extra Info (IMEI #, Serial #)'); 

        $this->general_model->exporttoexcel($data,"A1:AF1","Invoice",$headings,"Invoice.xls");
    }

    function getInvoiceProducts($invoiceid){
        
        $invoiceid = (is_array($invoiceid)?implode(",",$invoiceid):$invoiceid);

        $query=$this->readdb->select("tp.transactionid,tp.referenceproductid,tp.productid,tp.priceid,
                        SUM(tp.quantity) as quantity,tp.price,tp.tax,
                        CAST(IFNULL(((tp.price + tp.price*tp.tax/100) * SUM(tp.quantity)),0) AS DECIMAL(14,2)) as totalprice,
                        
                        tp.name as productname,
                        IFNULL((SELECT GROUP_CONCAT(tv.variantvalue) FROM ".tbl_transactionvariant." as tv WHERE tv.transactionproductid=tp.referenceproductid AND tv.transactionid=tp.transactionid),'') as variantname,
                    ")
                
                ->from(tbl_transactionproducts." as tp")
                ->where("tp.transactionid IN (".$invoiceid.") AND tp.transactiontype=3")
                ->group_by("tp.productid,tp.priceid,tp.price,tp.tax")
                ->get();
        
        return $query->result_array();  
    }
    function sendinvoice($PostData){

        $invoicenumber = $PostData['invoicenumber'];
        $invoiceid = $PostData['invoiceid'];
        $invoiceamount = $PostData['invoiceamount'];

        $file = $this->generatetransactionpdf($invoiceid,2);
        if($file){
            
            $file=basename($file);
            if (strpos($file, '?') !== false) {
                $t = explode('?',$file);
                $file = $t[0];            
            } 
           
            
            $invoicedata = $this->getInvoiceDetails($PostData['invoiceid']);
            
            $invoicedetail = $invoicedata['transactiondetail'];
            $sellerchannelid = $invoicedata['transactiondetail']['sellerchannelid'];
            $sellermemberid = $invoicedata['transactiondetail']['sellermemberid'];
            
            $this->load->model('Invoice_setting_model','Invoice_setting');
            $invoicesettingdata = $this->Invoice_setting->getShipperDetails($sellerchannelid,$sellermemberid);
    
            $mailto = $invoicedetail['email'];
            $from_mail = $invoicesettingdata['email']; 
            $from_name = $invoicesettingdata['businessname'];
            $replyto = $invoicesettingdata['email'];
    
            $subject= array("{companyname}"=>COMPANY_NAME,"{invoicenumber}"=>$invoicedetail['invoiceno']);
    
            $mailBodyArr = array(
                        "{logo}" => '<a href="'. DOMAIN_URL.'"><img src="' . MAIN_LOGO_IMAGE_URL. $invoicesettingdata['logo'].'" alt="' . COMPANY_NAME. '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
                        "{invoicenumber}" => $invoicedetail['invoiceno'],
                        "{amount}" => 'INR '.number_format($invoiceamount,2,'.',''),
                        "{invoicedate}" => $invoicedetail['createddate'],
                        "{invoiceaddress}" => "<p>".ucwords($invoicedetail['membername'])."<br>".ucwords($invoicedetail['address'])."<br>"."<b>Tel/Mobile : </b> ".ucwords($invoicedetail['mobileno'])."<br>"."<b>Email : </b> ".$invoicedetail['email']."</p>",
                        "{paymentdate}" => $this->general_model->displaydate($invoicedetail['createddate']),
                        "{paymentmethod}" => $invoicedetail['paymentmethod'],
                        "{companyemail}" =>$invoicesettingdata['email'],
                        "{companyname}" =>$invoicesettingdata['businessname'],
                        "{companywebsite}" => '<a href="'.DOMAIN_URL.'" target="_blank">'.COMPANY_WEBSITE.'</a>'
                    );
            
            //Send mail with email format store in database
            $mailid=array_search("Invoice",$this->Emailformattype);
            if(isset($mailid) && !empty($mailid)){
                $emailSend = $this->Invoice->mail_attachment($file, INVOICE_PATH, $mailto, $from_mail, $from_name, $from_mail, $subject, $mailBodyArr,$mailid,$sellerchannelid,$sellermemberid); 
            }
            
            $createddate = $this->general_model->getCurrentDateTime();
            $addedby = $this->session->userdata(base_url() . 'ADMINID');
            $updatedata = array("status" => 1,'modifieddate'=>$createddate,"addedby" => $addedby);
            $this->Invoice->_where = array('invoiceno' => $invoicedetail['invoiceno']);
            $this->Invoice->Edit($updatedata);

            @unlink(INVOICE_PATH.$file);
        }
    }
    function confirmOnCreditNotesForInvoiceCancellation($invoiceid){
        $this->load->model("Credit_note_model","Credit_note");
       
        $data = $this->Credit_note->getCreditNotesByInvoiceId($invoiceid);
        
        if(!empty($data)){
            $status=array();
            foreach($data as $val){
                $status[] =  $val['status'];
            }
            if(!in_array("1", $status)){
                $updatedata = array("status"=>"2",
                                    "cancelreason"=>"Invoice Cancelled.");
                
                $this->Credit_note->_where = array("(FIND_IN_SET('".$invoiceid."', invoiceid)>0)"=>null);
                $this->Credit_note->Edit($updatedata);

                return 1;
            }else{
                return 0;
            }
        }else{
            return 1;
        }
    }

    function getInvoicesByOrderId($orderid){
        
        $query=$this->readdb->select("id,status")
                            ->from($this->_table)
                            ->where(array("(FIND_IN_SET('".$orderid."', orderid)>0)"=>null))
                            ->get();
       
        return $query->result_array();  
    }

    function getCompanyName(){
        $query = $this->readdb->select("businessname")
                            ->from(tbl_settings)
                            ->get();
        return $query->row_array();                 
    }

    function generatetransactionpdf($transactionid,$type){

		$companyname = $this->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));
        
        if ($transactionid) {

            if($type==0){
                $this->load->model('Order_model','Order');
                $PostData['transactiondata'] = $this->Order->getOrderDetails($transactionid);
                $PostData['printtype'] = "order";
                $PostData['heading'] = "Order";
                $transactionnumber = $PostData['transactiondata']['transactiondetail']['orderid'];
                $DIRECTORY_PATH = ORDER_PATH;
                $DIRECTORY = ORDER;
            }else if($type==1){
                $this->load->model('Quotation_model','Quotation');
                $PostData['transactiondata'] = $this->Quotation->getQuotationDetails($transactionid);
                $PostData['printtype'] = "quotation";
                $PostData['heading'] = "Quotation";
                $transactionnumber = $PostData['transactiondata']['transactiondetail']['quotationid'];
                $DIRECTORY_PATH = QUOTATION_PATH;
                $DIRECTORY = QUOTATION;
            }else if($type==2){
                $PostData['transactiondata'] = $this->getInvoiceDetails($transactionid);
                $PostData['printtype'] = "invoice";
                $PostData['heading'] = "Invoice";
                $transactionnumber = $PostData['transactiondata']['transactiondetail']['invoiceno'];
                $DIRECTORY_PATH = INVOICE_PATH;
                $DIRECTORY = INVOICE;
            }else if($type==3){
                $this->load->model('Credit_note_model','Credit_note');
                $PostData['transactiondata'] = $this->Credit_note->getCreditNoteDetails($transactionid);
                $PostData['printtype'] = "creditnote";
                $PostData['heading'] = "Credit Note";
                $transactionnumber = $PostData['transactiondata']['transactiondetail']['creditnoteno'];
                $DIRECTORY_PATH = CREDITNOTE_PATH;
                $DIRECTORY = CREDITNOTE;
            }
            $PostData['hideonprint'] = '1';
            $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
            $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
            
            $this->load->model('Invoice_setting_model','Invoice_setting');
            $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);

            $header=$this->load->view(ADMINFOLDER . 'invoice/Transactionheader', $PostData,true);
            $html=$this->load->view(ADMINFOLDER . 'invoice/Transactionformatforpdf', $PostData,true);
            
            $this->load->library('m_pdf');
            //actually, you can pass mPDF parameter on this load() function
            $pdf = $this->m_pdf->load();

            // Set a simple Footer including the page number
            $pdf->setFooter('Side {PAGENO} 0f {nb}');

            //this the the PDF filename that user will get to download
            if(!is_dir($DIRECTORY_PATH)){
                mkdir($DIRECTORY_PATH);
            }
            $transactionnumber = $this->general_model->replaceStringWithDashes($transactionnumber);
            $filename = $PostData['invoicesettingdata']['businessname']."-".$transactionnumber.".pdf";
            $pdfFilePath =$DIRECTORY_PATH.$filename;

            $pdf->AddPage('', // L - landscape, P - portrait 
                        '', '', '', '',
                        5, // margin_left
                        5, // margin right
                       80, // margin top
                       15, // margin bottom
                        10, // margin header
                        10); // margin footer

            $this->load->model('Common_model');
            //$stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
            $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
            $pdf->WriteHTML($stylesheet,1);
            $pdf->SetHTMLHeader($header,'',true);
            $pdf->WriteHTML($html,0);

            //offer it to user via browser download! (The PDF won't be saved on your server HDD)
            $pdf->Output($pdfFilePath, "F");

            return $DIRECTORY.$filename;
        } else {
            return "";
        }
    }

    function getInvoiceDetails($transactionid){

        $transactiondata['transactiondetail'] = $transactiondata['transactionproduct'] = array();
        
        $query = $this->readdb->select("i.id,i.orderid,i.addressid,i.shippingaddressid,i.memberid,m.gstno,i.status, i.invoiceno,i.invoicedate,i.createddate,i.billingaddress,i.shippingaddress,i.sellermemberid,IFNULL((SELECT channelid FROM ".tbl_member." WHERE id=i.sellermemberid),0) as sellerchannelid,
                                    ma.name as membername,ma.address,ma.postalcode as postcode,ma.mobileno,ma.email,
                                    ct.name as cityname,
                                    pr.name as provincename, cn.name as countryname,
                                    
                                    shipper.name as shippingmembername,
                                    shipper.mobileno as shippingmobileno,
                                    shipper.email as shippingemail,
                                   
                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    i.amount as payableamount,i.remarks,
                                    i.couponcodeamount,
                                    i.globaldiscount,

                                    IF((SELECT count(tp.id) FROM ".tbl_transactionproducts." as tp WHERE tp.transactionid=i.id AND tp.discount>0)>0,1,0) as displaydiscountcolumn,

                                    IFNULL((SELECT SUM(redeempoints) FROM ".tbl_transactiondiscount." WHERE 
                                    transactionid = i.id),0) as redeempoints,
                                    IFNULL((SELECT redeemrate FROM ".tbl_transactiondiscount." WHERE 
                                    transactionid = i.id LIMIT 1),0) as redeemrate,
                                    IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE 
                                    transactionid = i.id),0) as redeemamount,

                                    i.cashorbankid,

                                    IFNULL((SELECT name FROM ".tbl_cashorbank." WHERE id=i.cashorbankid),'') as bankname,
                                    IFNULL((SELECT branchname FROM ".tbl_cashorbank." WHERE id=i.cashorbankid),'') as branchname,
                                    IFNULL((SELECT accountno FROM ".tbl_cashorbank." WHERE id=i.cashorbankid),'') as bankaccountnumber,
                                    IFNULL((SELECT ifsccode FROM ".tbl_cashorbank." WHERE id=i.cashorbankid),'') as ifsccode,
                                    IFNULL((SELECT micrcode FROM ".tbl_cashorbank." WHERE id=i.cashorbankid),'') as micrcode,

                                    IFNULL(
                                        (SELECT pm.name FROM ".tbl_transaction." as tr 
                                                        INNER JOIN ".tbl_paymentmethod." as pm on pm.id IN (SELECT paymentmethodid FROM ".tbl_paymentgateway." WHERE paymentgatewaytype=tr.paymentgetwayid GROUP BY paymentmethodid) 
                                                        WHERE tr.orderid=i.orderid
                                    ),'COD') as paymentmethod,

                                    IF((SELECT COUNT(o.id)
                                    FROM ".tbl_invoice." as inv
                                    INNER JOIN ".tbl_orders." as o ON FIND_IN_SET(o.id, inv.orderid)>0 AND o.gstprice=1
                                    WHERE inv.id=i.id)>0,1,0) as gstprice,

                                    i.paymentdays,i.cashbackpercent,i.cashbackamount,

                                    @netamount:=IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,


                                    @paymentreceiptamount:=IFNULL((SELECT SUM(prt.amount) 
                                    FROM ".tbl_paymentreceipttransactions." as prt 
                                    WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE isagainstreference=1 AND status!=2)),0) as paymentreceiptamount,

                                    CAST(IFNULL((@netamount - @paymentreceiptamount),0) AS DECIMAL(14,2)) as dueamount,
                                    
                                    IF((DATE_ADD(i.invoicedate, INTERVAL i.paymentdays DAY) >= CURDATE()),1,0) as iscashback,
                                    m.name as buyername,
                                    m.email as buyeremail,
                                    m.mobile as buyermobile,
                                    m.countrycode as buyercountrycode,
                                    m.secondarymobileno as buyersecondarymobileno,
                                    m.secondarycountrycode as buyersecondarycountrycode,
                                    m.isprimarywhatsappno,
                                    m.issecondarywhatsappno,

                            ")

                            ->from($this->_table." as i")
                            ->join(tbl_member." as m","m.id=i.memberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=i.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=i.shippingaddressid","LEFT")
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->where("i.id=".$transactionid)
                            ->get();
        $rowdata =  $query->row_array();
        
        if(empty($rowdata)){
            redirect('Pagenotfound');
        }

        $address = ucwords($rowdata['address']).",".ucwords($rowdata['cityname'])." - ".$rowdata['postcode'].", ".ucwords($rowdata['provincename']).", ".ucwords($rowdata['countryname']).".";
       
        $transactiondata['transactiondetail'] = array("id"=>$rowdata['id'],
                                            "orderid"=>ucwords($rowdata['orderid']),
                                            "buyername"=>$rowdata['buyername'],
                                            "buyeremail"=>$rowdata['buyeremail'],
                                            "buyercountrycode"=>$rowdata['buyercountrycode'],
                                            "buyermobile"=>$rowdata['buyermobile'],
                                            "buyersecondarycountrycode"=>$rowdata['buyersecondarycountrycode'],
                                            "buyersecondarymobileno"=>$rowdata['buyersecondarymobileno'],
                                            "isprimarywhatsappno"=>$rowdata['isprimarywhatsappno'],
                                            "issecondarywhatsappno"=>$rowdata['issecondarywhatsappno'],
                                            "billingaddressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "invoiceno"=>$rowdata['invoiceno'],
                                            "invoicedate"=>$this->general_model->displaydate($rowdata['invoicedate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "membername"=>ucwords($rowdata['membername']),
                                            "memberid"=>$rowdata['memberid'],
                                            "sellermemberid"=>$rowdata['sellermemberid'],
                                            "sellerchannelid"=>$rowdata['sellerchannelid'],
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "cashorbankid"=>$rowdata['cashorbankid'],
                                            "bankname"=>$rowdata['bankname'],
                                            "branchname"=>$rowdata['branchname'],
                                            "bankaccountnumber"=>$rowdata['bankaccountnumber'],
                                            "ifsccode"=>$rowdata['ifsccode'],
                                            "micrcode"=>$rowdata['micrcode'],
                                            "status"=>$rowdata['status'],
                                            "address"=>$address,
                                            "billingaddress"=>$rowdata['billingaddress'],
                                            "igst"=>$rowdata['igst'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "remarks"=>$rowdata['remarks'],
                                            "couponcodeamount"=>$rowdata['couponcodeamount'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],
                                            "shippingmembername"=>$rowdata['shippingmembername'],
                                            "shippingaddress"=>$rowdata['shippingaddress'],
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail'],
                                            "redeempoints"=>$rowdata['redeempoints'],
                                            "redeemrate"=>$rowdata['redeemrate'],
                                            "redeemamount"=>$rowdata['redeemamount'],
                                            "paymentmethod"=>$rowdata['paymentmethod'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            "paymentdays"=>$rowdata['paymentdays'],
                                            "cashbackpercent"=>$rowdata['cashbackpercent'],
                                            "cashbackamount"=>$rowdata['cashbackamount'],
                                            "netamount"=>$rowdata['netamount'],
                                            "dueamount"=>$rowdata['dueamount'],
                                            "iscashback"=>$rowdata['iscashback'],
                                            );

        $query = $this->readdb->select("CONCAT(tp.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_transactionvariant." WHERE transactionproductid=tp.id AND transactionid=".$transactionid."),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,tp.quantity,tp.price,tp.tax,tp.hsncode,tp.discount,(SELECT orderid FROM ".tbl_orders." WHERE id IN (SELECT orderid FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid )) as orderid,(SELECT serialno FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid ) as serialno,(SELECT originalprice FROM ".tbl_orderproducts." WHERE id=tp.referenceproductid) as originalprice,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage,tp.remarks")
                            ->from(tbl_transactionproducts." as tp")
                            ->join(tbl_product." as p","p.id=tp.productid","LEFT")
                            ->where("tp.transactiontype=3 AND tp.transactionid=".$transactionid)
                            //->group_by('orderid')
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();
        
        $query = $this->readdb->select("ecm.extrachargesname,ecm.taxamount,ecm.amount")
                            ->from(tbl_extrachargemapping." as ecm")
                            ->where("ecm.referenceid=".$transactionid." AND ecm.type=2")
                            ->get();
        $transactiondata['extracharges'] =  $query->result_array();

        $query = $this->db->select("so.id,so.trackingcode,so.shippingamount,so.invoiceamount,so.shipdate,
                                        IF(so.remarks!='',so.remarks,'-') as remarks,
                                           
                                        so.shippingby,            
                                        IF(so.shippingby=0,
                                            IFNULL((SELECT cc.companyname FROM ".tbl_couriercompany." as cc WHERE cc.id=so.courierid AND cc.status=1),'-'),
                                            IFNULL((SELECT t.companyname FROM ".tbl_transport." as t WHERE t.id=so.courierid AND t.status=1),'-')
                                        ) as shippingcompany
                                    ")
                            ->from(tbl_shippingorder." as so")
                            ->join(tbl_invoice." as i","i.id=so.invoiceid","INNER")
                            ->where("so.invoiceid=".$transactionid)
                            ->get();
        $shippingdata = $query->row_array();
        
        if(!empty($shippingdata)){
			$query = $this->db->select("sp.weight,sp.amount")
							->from(tbl_shippingpackage." as sp")
							->where("sp.shippingorderid=".$shippingdata['id'])
							->get();
            
            $shippingdata['shippingpackagedata'] = $query->result_array();				
        }
        
        $transactiondata['shippingdata'] = $shippingdata;
       
        return $transactiondata;
    }

    function getExtraChargesDataByInvoiceId($id){
        $query=$this->readdb->select("te.id,te.extrachargesid,te.extrachargesname,te.amount,te.taxamount,te.extrachargepercentage")
                            ->from(tbl_transactionextracharges." as te")
                            ->where("te.transactionid=".$id." AND te.transactiontype=0")
                            ->get();
                            // echo $this->readdb->last_query();exit;
                return $query->result_array();

    }
    function getExtraChargesDataByInvoiceIdForEdit($id){

        $where="ecm.referenceid=".$id." AND ecm.type=2 AND ecm.id NOT IN (SELECT oiecm.extrachargesmappingid FROM ".tbl_orderinvoiceextrachargesmapping." as oiecm)";

        $query = $this->readdb->select("ecm.id,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage,IF(ecm.extrachargepercentage>0,0,1) as amounttype")
                ->from(tbl_extrachargemapping." as ecm")
                ->where($where)
                ->get();

                return $query->result_array();

    }
    function getExtraChargesMappingDataByInvoiceId($id){
        $query = $this->readdb->select("ecm.id,ecm.extrachargesname as chargename,
                                    CAST(IFNULL(ecm.amount,0) AS DECIMAL(14,2)) as chargeamount,
                                    CAST(IFNULL(ecm.taxamount,0) AS DECIMAL(14,2)) as chargetax")
                            ->from(tbl_extrachargemapping." as ecm")
                            ->where("ecm.referenceid=".$id." AND ecm.type=2")
                            ->get();
        return $query->result_array();

    }

    function getInvoiceDataByID($ID){
        $query = $this->readdb->select("i.id,i.invoiceno,i.invoicedate,i.memberid,i.sellermemberid,i.addressid,i.shippingaddressid,i.remarks,i.taxamount,i.amount,i.orderid,i.status,
                    i.paymentdays,i.cashbackpercent,i.cashbackamount,i.remarks,i.globaldiscount,
                    (SELECT channelid FROM ".tbl_member." WHERE id=i.memberid) as channelid,
                    (SELECT GROUP_CONCAT(o.orderid) FROM ".tbl_orders." as o WHERE o.id IN (i.orderid)) as orderno,

                    IF((DATE_ADD(i.invoicedate, INTERVAL i.paymentdays DAY) >= CURDATE()),1,0) as iscashback,
        ")
							->from($this->_table." as i")
							->where("i.id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
    }
    
    function getOrderProductsByOrderIDOrMemberID($memberid,$orderid,$transactionid=0){

        if(!empty($transactionid)){
            $where_sum = " AND i.status NOT IN (2) AND tp.id != trp.id";
            $sql_edit = "trp.quantity as editquantity,";
            $remarks = "trp.remarks,";
            $transactionproductid = "trp.id as transactionproductid,";
        }else{
            $sql_edit = "'' as editquantity,";
            $remarks = "'' as remarks,";
            $transactionproductid = "0 as transactionproductid,";
            $where_sum = " AND i.status NOT IN (2)";
        }
        $this->readdb->select("o.id as orderid,o.orderid as ordernumber,op.id as orderproductsid,o.payableamount,
                                    IFNULL((SELECT id
                                    FROM ".tbl_transactionproducts." as tp
                                    WHERE tp.referenceproductid = op.id AND (tp.transactionid='".$transactionid."' OR ''='".$transactionid."') LIMIT 1
                                    ),'') as transactionproductsid,
                                    op.productid,
                                    CONCAT(op.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_ordervariant." WHERE orderproductid=op.id),'')) as productname,
                                    IFNULL((SELECT priceid FROM ".tbl_ordervariant." WHERE orderproductid=op.id LIMIT 1),0) as combinationid,
                                    op.quantity,
                                    op.discount,
                                    op.price as amount,
                                    op.originalprice,
                                    op.hsncode,op.tax,op.isvariant,op.name,
                                    op.finalprice,
                                    ".$remarks."
                                    ".$transactionproductid."
                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    
                                    @productamount:=IFNULL((op.price - (op.price * op.discount / 100)),0) as productamount, 
                                    IFNULL((@productamount + (@productamount * op.tax / 100)),0) as pricewithtax, 

                                    @invoiceqty:=IFNULL((SELECT SUM(tp.quantity)
                                    FROM ".$this->_table." as i
                                    INNER JOIN ".tbl_transactionproducts." as tp ON tp.transactionid=i.id
                                    WHERE tp.referenceproductid = op.id AND find_in_set(o.id, i.orderid)
                                    ".$where_sum."
                                    ),0) as invoiceqty,
                                    ".$sql_edit."

                                    o.gstprice,op.salespersonid,op.commission,op.commissionwithgst
                                    
                                ");
        $this->readdb->from(tbl_orders." as o");                           
        $this->readdb->join(tbl_orderproducts." as op", "op.orderid=o.id", "INNER");
        
        if(!empty($transactionid)){
        $this->readdb->join(tbl_transactionproducts." as trp", "trp.referenceproductid = op.id AND trp.transactionid=".$transactionid, "INNER");
        }
        $this->readdb->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->where("FIND_IN_SET(o.id, '".$orderid."') AND o.memberid=".$memberid);
        $this->readdb->order_by("op.id","DESC");
        $query = $this->readdb->get();
       
        return $query->result_array();             
    }

    function getOrderVariantsData($orderid,$orderproductsid){
        
        $query = $this->readdb->select("ov.id,ov.variantid,ov.variantname,ov.variantvalue")
                    ->from(tbl_ordervariant." as ov")                          
                    ->where("FIND_IN_SET(ov.orderid, '".$orderid."') AND ov.orderproductid=".$orderproductsid)
                    ->get();

        return $query->result_array();       
    }

    function getApprovedInvoiceByMember($sellerid,$buyerid,$type=''){

        if($type=='API'){
            $select = "i.id as invoiceid,i.invoiceno,i.invoicedate,CAST(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) AS DECIMAL(14,2)) as invoicepayableamount";
        }else{
            $select = "i.id,i.invoiceno,i.addressid as billingid,i.shippingaddressid as shippingid";
        }

        $query = $this->readdb->select($select)
                    ->from(tbl_invoice." as i")
                    ->where("i.memberid=".$buyerid." AND
                        i.sellermemberid=".$sellerid." AND 
                        i.status=1 AND  
                        IFNULL((SELECT SUM(quantity) FROM ".tbl_transactionproducts." where transactionid = i.id AND transactiontype=3),0) 
                    
                        > 
                        IFNULL((SELECT SUM(cnp.creditqty) 
                        FROM ".tbl_creditnoteproducts." as cnp 
                        INNER JOIN ".tbl_transactionproducts." as tp ON tp.id=cnp.transactionproductsid AND tp.transactiontype=3 
                        where FIND_IN_SET(cnp.creditnoteid, (SELECT GROUP_CONCAT(id) FROM ".tbl_creditnote." where FIND_IN_SET(i.id, invoiceid)>0 AND status!=2))>0 AND tp.transactionid=i.id),0)")
                    ->order_by("i.id", "DESC")
                    ->get();
		
		if($query->num_rows() > 0) {
			return $query->result_array();
		}else{
			return array();
		}
    }
    function getInvoiceByBuyer($memberid){
        
        $memberid = (is_array($memberid)?implode(",",$memberid):$memberid);

        $query = $this->readdb->select("i.id,i.invoiceno,i.memberid")
                ->from(tbl_invoice." as i")
                ->where("i.sellermemberid=0 AND i.memberid IN (".$memberid.") AND i.status=1")
                ->order_by("i.id", "DESC")
                ->get();

        if($query->num_rows() > 0) {
            return $query->result_array();
        }else{
            return array();
        }
    }
    function getPaymentReceiptInvoice($sellerid,$buyerid,$paymentreceiptid=0){
        $sql = "";
        if(!empty($paymentreceiptid)){
            $sql = ' AND prt.paymentreceiptid!='.$paymentreceiptid;
        }
        $query = $this->readdb->select("i.id,i.invoiceno,
                            
                            @netamount:=IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,

                            @paymentreceiptamount:=IFNULL((SELECT SUM(prt.amount) 
                            FROM ".tbl_paymentreceipttransactions." as prt 
                            WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE isagainstreference=1 AND status!=2)".$sql."),0) as paymentreceiptamount,

                            IFNULL((@netamount - @paymentreceiptamount),0) as invoiceamount
                        ")
                        ->from(tbl_invoice." as i")
                        ->where("i.memberid=".$buyerid." AND
                            i.sellermemberid=".$sellerid." 
                            AND i.status = 0 AND
                            
                            IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) 
                            
                            > 
                            IFNULL((SELECT SUM(prt.amount) 
                            FROM ".tbl_paymentreceipttransactions." as prt 
                            WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE isagainstreference=1 AND status!=2)".$sql."),0) 

                            ")
                        ->order_by("i.id", "DESC")
                        ->get();
                        // echo $this->readdb->last_query(); exit;
		if($query->num_rows() > 0) {
			return $query->result_array();
		}else{
			return array();
		}
    }

    function getInvoiceAmountDataByID($invoiceid){

        $query = $this->readdb->select("i.id,i.invoiceno,
                                    i.taxamount,
                                    i.amount as invoiceamount,
                                    IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactiontype=0 AND transactionid=i.id),0)),0) as netamount,

                                    i.invoicedate,
                                    i.globaldiscount as discountamount, 
                                    i.couponcode,
                                    i.couponcodeamount as couponamount,

                                    i.addressid as billingaddressid,
                                    i.shippingaddressid,

                                    IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactiontype=0 AND transactionid=i.id),0) as redeemamount
                                ")

            ->from($this->_table." as i")
            ->where("FIND_IN_SET(i.id,'".$invoiceid."')>0")
            ->order_by("i.id","DESC")
            ->get();


        $invoicedata = $query->result_array();
        $data = array();
        if(!empty($invoicedata)){
            foreach($invoicedata as $invoice){
               
                $query = $this->readdb->select("ecm.id,ecm.extrachargesid,ecm.extrachargesname,ecm.taxamount,ecm.amount,ecm.extrachargepercentage,
                IF(ecm.extrachargepercentage>0,0,1) as amounttype,

                (SELECT sum(amount) FROM ".tbl_transactionextracharges." WHERE transactiontype=1 AND referenceid=ecm.referenceid AND extrachargesid=ecm.extrachargesid) as creditnoteamount")
                        ->from(tbl_extrachargemapping." as ecm")
                        ->where("ecm.referenceid=".$invoice['id']." AND ecm.type=2")
                        ->get();
                $extracharges =  $query->result_array();

               $data[] = array("id"=>$invoice['id'],
                                "invoiceno"=>$invoice['invoiceno'],
                                "invoicedate"=>$this->general_model->displaydate($invoice['invoicedate']),
                                "taxamount"=>$invoice['taxamount'],
                                "invoiceamount"=>$invoice['invoiceamount'],
                                "netamount"=>$invoice['netamount'],
                                "discountamount"=>$invoice['discountamount'],
                                "couponcode"=>$invoice['couponcode'],
                                "couponamount"=>$invoice['couponamount'],
                                "redeemamount"=>$invoice['redeemamount'],
                                "billingaddressid"=>$invoice['billingaddressid'],
                                "shippingaddressid"=>$invoice['shippingaddressid'],
                                "extracharges"=>$extracharges
                            );
            }
        }
        return $data;
    }

	//LISTING DATA
	function _get_datatables_query(){
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $buyerchannelid = (isset($_REQUEST['buyerchannelid']))?$_REQUEST['buyerchannelid']:'';
        $buyermemberid = (isset($_REQUEST['buyermemberid']))?$_REQUEST['buyermemberid']:'';
        $sellerchannelid = (isset($_REQUEST['sellerchannelid']))?$_REQUEST['sellerchannelid']:'';
        $sellermemberid = (isset($_REQUEST['sellermemberid']))?$_REQUEST['sellermemberid']:'';
        // print_r($_REQUEST); exit;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

		$this->readdb->select("i.id,i.sellermemberid,i.memberid,
                            (SELECT GROUP_CONCAT(o.id) FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,i.orderid)>0) as orderid,
                            (SELECT GROUP_CONCAT(o.orderid) FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,i.orderid)>0) as ordernumbers,
                            i.invoiceno,
                            i.invoicedate,
                            i.taxamount,
                            i.amount,
                            i.courierid,
                            
                            @netamount:=IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,

                            @paymentreceiptamount:=IFNULL((SELECT SUM(prt.amount) 
                            FROM ".tbl_paymentreceipttransactions." as prt 
                            WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE isagainstreference=1 AND status!=2)),0) as paymentreceiptamount,
                            
                            CAST(IFNULL((@netamount - @paymentreceiptamount),0) AS DECIMAL(14,2)) as dueamount,

                            @primarynumber:=IF(buyer.isprimarywhatsappno=1,buyer.mobile,'') as primarynumber,
                            @secondarynumber:=IF(buyer.issecondarywhatsappno=1,buyer.secondarymobileno,'') as secondarynumber,
                            IF(@primarynumber='',IF(buyer.issecondarywhatsappno=1,CONCAT(buyer.secondarycountrycode,buyer.secondarymobileno),''),CONCAT(buyer.countrycode,@primarynumber)) as whatsappno,

                            i.status,
                            IFNULL(seller.name,'') as sellername,
                            IFNULL(seller.membercode,'') as sellercode,
                            IFNULL(i.sellermemberid,'') as sellerid,
                            IFNULL(seller.channelid,'') as sellerchannelid,
                            buyer.name as buyername,
                            buyer.membercode as buyercode,
                            i.memberid as buyerid,
                            buyer.channelid as buyerchannelid,
                           
                            CASE
                                WHEN i.status = 0 THEN 'Pending'
                                WHEN i.status = 1 THEN 'Complete'
                                
                                ELSE 'Cancel'
                            END AS statusname,

                                
                            IF((IFNULL((SELECT SUM(quantity) FROM ".tbl_transactionproducts." where transactionid = i.id AND transactiontype=3),0) 
                    
                            > 
                            IFNULL((SELECT SUM(cnp.creditqty) 
                            FROM ".tbl_creditnoteproducts." as cnp 
                            INNER JOIN ".tbl_transactionproducts." as tp ON tp.id=cnp.transactionproductsid AND tp.transactiontype=3 
                            where FIND_IN_SET(cnp.creditnoteid, (SELECT GROUP_CONCAT(id) FROM ".tbl_creditnote." where FIND_IN_SET(i.id, invoiceid)>0 AND status!=2))>0 AND tp.transactionid=i.id),0)),1,0) as allowcreditnote,
                            IFNULL((SELECT trackingcode FROM ".tbl_shippingorder." WHERE invoiceid=i.id),'') as trackingcode,
                            IFNULL((SELECT cc.trackurl FROM ".tbl_couriercompany." as cc WHERE cc.id=i.courierid AND cc.status=1),'') as trackingurl,

                            IFNULL((SELECT spo.awb_code FROM ".tbl_shiprocketorder." spo INNER JOIN ".tbl_shippingorder." so ON so.id=spo.shippingorderid WHERE so.invoiceid=i.id LIMIT 1),'') as awbcode
                            
                        ");

        $this->readdb->from($this->_table." as i");
        $this->readdb->join(tbl_member." as buyer","buyer.id=i.memberid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT");
        if(!is_null($MEMBERID)){
            $type = isset($_REQUEST['type'])?$_REQUEST['type']:'sales';
            if($type=="purchase"){
                $this->readdb->where("i.memberid=".$MEMBERID." AND i.sellermemberid IN  (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID.")");
            }else{
                $this->readdb->where("i.sellermemberid=".$MEMBERID);
            }
        }
        if(!empty($buyerchannelid)){
            $this->readdb->where("buyer.channelid=".$buyerchannelid); //Filter buyer Channel
           
            if(!empty($buyermemberid)){
                $this->readdb->where("buyer.id IN (".implode(",",$buyermemberid).")"); //Filter buyer member
            }
        }
        if($sellerchannelid!=""){
            if($sellerchannelid!=0){
                $this->readdb->where("seller.channelid=".$sellerchannelid); //Filter seller channel
                
                if(!empty($sellermemberid)){
                    $this->readdb->where("seller.id IN (".implode(",",$sellermemberid).")"); //Filter seller member
                }
            }else{
                $this->readdb->where("i.sellermemberid=0"); //Filter seller 
            }
        }
        if($status != -1){
            $this->readdb->where("i.status=".$status);
        }
        $this->readdb->where("(i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."') AND i.memberid!=0");

        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $this->readdb->where("( ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.addedby FROM ".tbl_orders." as o WHERE o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0 AND FIND_IN_SET(o.id,i.orderid)>0) or ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.salespersonid FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,i.orderid)>0) )");
        }

		$i = 0;
        foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->readdb->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->readdb->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
        $query = $this->readdb->get();
        // echo $this->readdb->last_query(); exit;
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
    }

    function updateremarks($invoiceid,$remarks){

        $query = $this->readdb->select('i.invoiceno')
                            ->from($this->_table." as i")
                            ->where("i.id=".$invoiceid)
                            ->get();
        $InvoiceData = $query->row_array();

        if(!empty($InvoiceData)){

            if($remarks!=''){
                $updatedata = array('remarks'=>$remarks);
                $this->Invoice->_table = tbl_shippingorder;
                $this->Invoice->_where = "invoiceid='".$invoiceid."'";
                $this->Invoice->Edit($updatedata); 
            }

            // $this->updatecourierapistatus($InvoiceData);
        }
        
    }

    function generateAwB($invoiceid,$type=0){

        $invoicequery = $this->readdb->select("i.id,i.invoiceno,i.memberid,
            i.taxamount,
            i.amount as invoiceamount,
            IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactiontype=0 AND transactionid=i.id),0)),0) as netamount,
            i.invoicedate,
            i.globaldiscount as discountamount, 
            i.couponcode,
            i.couponcodeamount as couponamount,
            i.addressid as billingaddressid,
            i.shippingaddressid,
            IFNULL(m.gstno,'') as gstno,
            IFNULL(is.gstno,'') as fromgstno,
            IFNULL(is.businessname,'') as businessname,
            IFNULL(is.businessaddress,'') as businessaddress,
            IFNULL((SELECT name FROM ".tbl_city." WHERE id=is.cityid),'') as cityname,
            IFNULL(is.postcode,'') as postcode,
            IFNULL((SELECT code FROM ".tbl_province." WHERE id=is.provinceid),0) as statecode,
            IFNULL(ma.name,'') as toname, 
            IFNULL(ma.address,'') as toaddress, 
            IFNULL((SELECT name FROM ".tbl_city." WHERE id=ma.cityid),'') as tocityname,
            IFNULL(ma.postalcode,'') as topostcode,
            IFNULL((SELECT code FROM ".tbl_province." WHERE id=ma.provinceid),0) as tostatecode,
            IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactiontype=0 AND transactionid=i.id),0) as redeemamount

        ")

                ->from($this->_table." as i")
                ->join(tbl_member." as m","m.id=IF(".$type."=0,i.memberid,i.sellermemberid)","INNER")
                ->join(tbl_memberaddress." as ma","ma.id=i.addressid","LEFT")
                ->join(tbl_invoicesetting." as is","is.memberid=i.sellermemberid OR is.memberid=0","LEFT")
                ->where("i.id='".$invoiceid."'")
                ->order_by("i.id","DESC")
                ->get();

                   


        $invoicedata = $invoicequery->result_array();
        $data = array();
        $productdata = array();
        if(!empty($invoicedata)){
            $igstValue = $sgstValue = $cgstValue  = 0;
            $i=1;
            foreach($invoicedata as $invoice){
                $productquery = $this->readdb->select("
                    ".$i." as itemNo,
                    op.name as productName,
                    IFNULL(p.slug,'') asproductDesc,
                    op.hsncode as hsnCode,
                    op.quantity as quantity,
                    'Nos.' as qtyUnit,
                    op.originalprice as taxableAmount,
                    
                    IFNULL(IF(pr.id=12 OR pr.name='gujarat',0,(op.tax/2)),0) as sgstRate,
                    IFNULL(IF(pr.id=12 OR pr.name='gujarat',0,(op.tax/2)),0) as cgstRate,
                    IFNULL(IF(pr.id=12 OR pr.name='gujarat',(op.tax),0),0) as igstRate,
                    '0' as cessRate,
                    '0' as cessNonAdvol
                ")
                            ->from(tbl_orderproducts." as op")
                            ->join(tbl_product." as p","p.id=op.productid","LEFT")
                            ->join(tbl_memberaddress." as ma",'ma.id='.$invoice['billingaddressid'],"LEFT")
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->where("op.orderid=".$invoice['id'])
                            ->get();
                $productquerydata = $productquery->row_array();
                $productdata[] = $productquerydata;
                //print_r($productquerydata);exit;
                $igstValue = $igstValue + $productquerydata['igstRate'];
                $cgstValue = $cgstValue + $productquerydata['cgstRate'];
                $sgstValue = $sgstValue + $productquerydata['sgstRate'];
                
                $i++;
            }
            $data = array("itemList"=>$productdata,
                          "userGstin"=>$invoice['gstno'],
                          "supplyType"=>0,
                          "subSupplyType"=>5,
                          "docType"=>"INV",
                          "docNo"=>$invoice['invoiceno'],
                          "docDate"=>$this->general_model->displaydate($invoice['invoicedate']),
                          "transType"=>1,
                          "fromGstin"=>$invoice['fromgstno'],
                          "fromTrdName"=>$invoice['businessname'],
                          "fromAddr1"=>$invoice['businessaddress'],
                          "fromAddr2"=>$invoice['businessaddress'],
                          "fromPlace"=>$invoice['cityname'],
                          "fromPincode"=>$invoice['postcode'],
                          "fromStateCode"=>$invoice['statecode'],
                          "actualFromStateCode"=>$invoice['statecode'],
                          "toGstin"=>$invoice['gstno'],
                          "toTrdName"=>$invoice['toname'],
                          "toAddr1"=>$invoice['toaddress'],
                          "toAddr2"=>$invoice['toaddress'],
                          "toPlace"=>$invoice['tocityname'],
                          "toPincode"=>$invoice['topostcode'],
                          "toStateCode"=>$invoice['tostatecode'],
                          "actualToStateCode"=>$invoice['tostatecode'],
                          "totalValue"=>$invoice['invoiceamount'],
                          "cgstValue"=>$cgstValue,
                          "sgstValue"=>$sgstValue,
                          "igstValue"=>$igstValue,
                          "cessValue"=>'0',
                          "OthValue"=>'0',
                          "TotNonAdvolVal"=>'0',
                          "transMode"=>1,
                          "transDistance"=>'500.0',
                          "transporterName"=>"",
                          "transporterId"=>"",
                          "transDocNo"=>"",
                          "transDocDate"=>"23-10-2020",
                          "vehicleNo"=>"",
                          "vehicleType"=>"R",
                          "totInvValue"=>$invoice['invoiceamount'],
                          "mainHsnCode"=>"",
                                
                        );
        }

        //print_r($data);exit;
        return $data;

    }

    /*********** FUNCTION USE IN API *************/
    function getInvoicesByType($sellermemberid,$sellerchannelid,$buyermemberid,$fromdate,$todate,$issales,$status,$counter){

        $limit=10;
        /* issales is 1-sales & 0-purchase */
        
        $this->readdb->select("i.id as invoiceid,
                            i.invoiceno,
                            i.invoicedate,
                            i.orderid,
                            i.status,
                            
                            @amount:=CAST(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) AS DECIMAL(14,2)) as amount,

                            buyer.id as buyerid,
                            CONCAT(buyer.name,' (',buyer.membercode,')') as buyername,
                            IFNULL(seller.id,'0') as sellerid,
                            IFNULL(CONCAT(seller.name,' (',seller.membercode,')'),'Company') as sellername,
                            i.cancelreason as reason,

                            CAST(IFNULL((@amount - IFNULL((SELECT SUM(prt.amount) 
                            FROM ".tbl_paymentreceipttransactions." as prt 
                            WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE isagainstreference=1 AND status!=2)),0)),0) AS DECIMAL(14,2)) as dueamount,

                            IF((IFNULL((SELECT SUM(quantity) FROM ".tbl_transactionproducts." where transactionid = i.id AND transactiontype=3),0) 
                    
                            > 
                            IFNULL((SELECT SUM(cnp.creditqty) 
                            FROM ".tbl_creditnoteproducts." as cnp 
                            INNER JOIN ".tbl_transactionproducts." as tp ON tp.id=cnp.transactionproductsid AND tp.transactiontype=3 
                            where FIND_IN_SET(cnp.creditnoteid, (SELECT GROUP_CONCAT(id) FROM ".tbl_creditnote." where FIND_IN_SET(i.id, invoiceid)>0 AND status!=2))>0 AND tp.transactionid=i.id),0)),1,0) as isaddcreditnote
                        ");

        $this->readdb->from($this->_table." as i");
        $this->readdb->join(tbl_member." as buyer","buyer.id=i.memberid","LEFT");
        $this->readdb->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT");
       
        $type = ($issales==0)?'purchase':'sales';
        if($type=="purchase"){
            $this->readdb->where("i.memberid=".$sellermemberid);
            if($buyermemberid!=""){
                $this->readdb->where("i.sellermemberid =".$buyermemberid);
            }
        }else{
            $this->readdb->where("i.sellermemberid=".$sellermemberid);
        }
        if($type=="sales" && !empty($buyermemberid)){
            $this->readdb->where("buyer.id = ".$buyermemberid); //Filter buyer member
        }
        if($status != ""){
            $this->readdb->where("i.status=".$status);
        }
        $this->readdb->where("(i.invoicedate BETWEEN '".$fromdate."' AND '".$todate."')");
        
        $this->readdb->order_by("i.id", "DESC");
        if($counter >= 0){
        $this->readdb->limit($limit,$counter);
        }
        $query = $this->readdb->get();
                            
        if($query->num_rows() > 0) {
            $json=array();
            $data = $query->result_array();
            if(!empty($data)){
                foreach($data as $row){
                    
                    $query = $this->readdb->select("o.id as orderid,
                                    o.orderid as ordernumber,
                                    CAST((IFNULL(o.payableamount,0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=0 AND referenceid=o.id),0)) AS DECIMAL(14,2)) as orderpayableamount")
                                ->from(tbl_orders." as o")
                                ->where("o.id IN (".$row['orderid'].")")
                                ->get();
                    
                    $invoiceorders['invoiceorders'] =  $query->result_array();
                    unset($row['orderid']);
                    $row['issales'] = $issales;
                    $json[] = array_merge($row, $invoiceorders);
                }
            }
            return $json;
		}else{
			return array();
		}
    }

    function getInvoiceDetailsForAPI($invoiceid,$userid){

        $query = $this->readdb->select("i.id,i.orderid,i.memberid,m.gstno,i.status, i.invoiceno,i.invoicedate,i.createddate,
                                    i.addressid as billingaddressid,
                                    ma.addresstype as billingaddresstype,
                                    i.billingaddress,
                                    i.shippingaddressid,
                                    shipper.addresstype as shippingaddresstype,
                                    i.shippingaddress,
                                    
                                    IFNULL(seller.name, 'Company') as sellername,
                                    IFNULL(seller.email,'') as selleremail,
                                    IFNULL(seller.mobile,'') as sellermobile,
                                    IFNULL(seller.membercode,'') as sellercode,
                                    
                                    IFNULL(m.name, 'Company') as buyername,
                                    IFNULL(m.email,'') as buyeremail,
                                    IFNULL(m.mobile,'') as buyermobile,
                                    IFNULL(m.membercode,'') as buyercode,
                                    
                                    i.amount as assessableamount,
                                    i.taxamount,

                                    @chargeamount:=IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0) as chargeamount,

                                    @extrachargetaxamount:=CAST(IFNULL((SELECT SUM(taxamount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0) AS DECIMAL(14,2)) as extrachargetaxamount,

                                    CAST(IFNULL((@chargeamount - @extrachargetaxamount),0) AS DECIMAL(14,2)) as extrachargeamount,
                                    
                                    @producttoatal:=CAST(IFNULL((i.amount + i.taxamount),0) AS DECIMAL(14,2)) as producttoatal,

                                    i.globaldiscount,
                                    i.couponcodeamount,
                                    
                                    @redeemamount:=CAST(IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE 
                                    transactionid = i.id),0) AS DECIMAL(14,2)) as redeemamount,

                                    @payableamount:=CAST(IFNULL((@producttoatal - i.globaldiscount - i.couponcodeamount - @redeemamount + @chargeamount),0) AS DECIMAL(14,2)) as payableamount,

                                    CAST(IFNULL((@payableamount - IFNULL((SELECT SUM(prt.amount) 
                                    FROM ".tbl_paymentreceipttransactions." as prt 
                                    WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE isagainstreference=1 AND status!=2)),0)),0) AS DECIMAL(14,2)) as dueamount,
                                    i.paymentdays,i.cashbackpercent,

                                    i.cashorbankid,
                                    IFNULL(cb.name,'') as bankname,
                                    IFNULL(cb.branchname,'') as branchname,
                                    IFNULL(cb.accountno,'') as bankaccountnumber,
                                    IFNULL(cb.ifsccode,'') as ifsccode,
                                    IFNULL(cb.micrcode,'') as micrcode

                            ")

                            ->from($this->_table." as i")
                            ->join(tbl_member." as m","m.id=i.memberid","LEFT") 
                            ->join(tbl_member." as seller","seller.id=i.sellermemberid","LEFT") 
                            ->join(tbl_memberaddress." as ma","ma.id=i.addressid","LEFT")
                            ->join(tbl_memberaddress." as shipper","shipper.id=i.shippingaddressid","LEFT")
                            ->join(tbl_city." as ct","ct.id=ma.cityid","LEFT")
                            ->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
                            ->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
                            ->join(tbl_cashorbank." as cb","cb.id=i.cashorbankid","LEFT")
                            ->where("i.id=".$invoiceid)
                            ->get();
        $invoicedata =  $query->row_array();

        $json=array();
        if(!empty($invoicedata)){
                
            $query=$this->readdb->select("op.orderid,
                    (SELECT orderid FROM ".tbl_orders." WHERE id=op.orderid) as ordernumber,
                    tp.productid,
                    IFNULL((SELECT filename FROM ".tbl_productimage." WHERE productid=p.id LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as productimage,
                    CONCAT(tp.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_transactionvariant." WHERE transactionproductid=tp.id AND transactionid=tp.transactionid),'')) as productname,
                    
                    tp.hsncode as producthsncode,
                    tp.quantity as invoiceqty,
                    tp.price, tp.discount, tp.tax,
                    CAST(IFNULL(((tp.price + (tp.price * tp.tax / 100)) * tp.quantity),0) AS DECIMAL(14,2)) as amount,
                    ")
                                ->from(tbl_transactionproducts." as tp")
                                ->join(tbl_orderproducts." as op","op.id=tp.referenceproductid","INNER")
                                ->join(tbl_product." as p","p.id=tp.productid","LEFT")
                                ->where("tp.transactionid=".$invoicedata['id']." AND tp.transactiontype=3")
                                ->get();
            $orderproducts = $query->result_array();

            $query = $this->readdb->select("ecm.extrachargesname as chargename,
                                    CAST(IFNULL(ecm.amount,0) AS DECIMAL(14,2)) as chargeamount,
                                    CAST(IFNULL(ecm.taxamount,0) AS DECIMAL(14,2)) as chargetax")
                            ->from(tbl_extrachargemapping." as ecm")
                            ->where("ecm.referenceid=".$invoicedata['id']." AND ecm.type=2")
                            ->get();
            $appliedcharges =  $query->result_array();

            $issales = ($invoicedata['memberid']==$userid)?'0':'1';

            if($invoicedata['cashorbankid']!=0){
                
                $bankdetail = array(
                    "cashorbankid"=>$invoicedata['cashorbankid'],
                    "bankname"=>$invoicedata['bankname'],
                    "branchname"=>$invoicedata['branchname'],
                    "bankaccountnumber"=>$invoicedata['bankaccountnumber'],
                    "ifsccode"=>$invoicedata['ifsccode'],
                    "micrcode"=>$invoicedata['micrcode']
                  );
            }else{
                $bankdetail = (object)array();
            }
            $json[] = array("invoiceid"=>$invoicedata['id'],
                            "invoiceno"=>$invoicedata['invoiceno'],
                            "invoicestatus"=>$invoicedata['status'],
                            "invoicedate"=>$invoicedata['invoicedate'],
                            "issales"=>$issales,
                            "addressdetail"=>array(
                                "billingaddress"=>array("id"=>$invoicedata['billingaddressid'],
                                                        "type"=>$invoicedata['billingaddresstype'],"address"=>$invoicedata['billingaddress'],
                                                ),
                                "shippingaddress"=>array("id"=>$invoicedata['shippingaddressid'],
                                                        "type"=>$invoicedata['shippingaddresstype'],"address"=>$invoicedata['shippingaddress'],
                                                ),
                            ),
                            "sellerdetail"=>array(
                                "name"=>$invoicedata['sellername'],
                                "email"=>$invoicedata['selleremail'],
                                "mobile"=>$invoicedata['sellermobile'],
                                "code"=>$invoicedata['sellercode'],
                            ),
                            "buyerdetail"=>array(
                                "name"=>$invoicedata['buyername'],
                                "email"=>$invoicedata['buyeremail'],
                                "mobile"=>$invoicedata['buyermobile'],
                                "code"=>$invoicedata['buyercode'],
                            ),
                            "bankdetail"=>$bankdetail,
                            "orderproducts"=>$orderproducts,
                            "gstsummary"=>array(
                                "productdetail"=>array(
                                    "assessableamount"=>$invoicedata['assessableamount'],
                                    "gstamount"=>$invoicedata['taxamount']
                                ),
                                "extrachargedetail"=>array(
                                    "assessableamount"=>$invoicedata['extrachargeamount'],
                                    "gstamount"=>$invoicedata['extrachargetaxamount']
                                )
                            ),
                            "invoicesummary"=>array(
                                "producttoatal"=> $invoicedata['producttoatal'],
                                "discount"=> $invoicedata['globaldiscount'],
                                "couponamount"=> $invoicedata['couponcodeamount'],
                                "redeemamount"=> $invoicedata['redeemamount'],
                                "amountpayable"=> $invoicedata['payableamount'],
                                "dueamount"=> $invoicedata['dueamount'],
                                "paymentdays"=> $invoicedata['paymentdays'],
                                "cashbackpercent"=> $invoicedata['cashbackpercent'],
                                "appliedcharges"=> $appliedcharges
                            )
                        ); 
            
        }
        return $json;
    }

    function getMemberSalesInvoice($sellerid,$buyerid,$type=''){
       
        if($type=='API'){
            $select = "o.id as orderid,o.orderid as orderno";
        }else{
            $select = "o.id,o.orderid,o.addressid as billingid,o.shippingaddressid as shippingid";
        }
    
        $query = $this->readdb->select($select)
                            ->from($this->_table." as o")
                            ->where("o.memberid=".$buyerid." AND
                                o.sellermemberid=".$sellerid." AND 
                                o.status IN (0,1) AND o.approved=1 AND  

                                IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
                                > 
                                IFNULL((SELECT SUM(tp.quantity) 
                                FROM ".tbl_transactionproducts." as tp 
                                INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
                                where tp.transactionid IN (SELECT id FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2) AND transactiontype=3),0)")
                            ->order_by("o.id","desc")
                            ->get();
        
        return $query->result_array();
    }
    /*********** ^ FUNCTION USE IN API ^ *************/





}
?>