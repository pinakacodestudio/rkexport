<?php
class Purchase_invoice_model extends Common_model 
{
	//put your code here
	public $_table = tbl_invoice;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'vendorname',null,'i.invoiceno','i.invoicedate','statusname','netamount'); //set column field database for datatable orderable
    
    public $column_search = array('vendor.name','vendor.membercode','i.invoiceno','i.invoicedate','IFNULL((i.amount + i.taxamount - i.globaldiscount + IFNULL((SELECT SUM(amount) FROM '.tbl_extrachargemapping.' WHERE type=2 AND referenceid=i.id),0)),0)','(SELECT GROUP_CONCAT(o.orderid) FROM '.tbl_purchaseorders.' as o WHERE FIND_IN_SET(o.id,i.orderid)>0)'); //set column field database for datatable searchable 
	public $order = array('i.id' => 'DESC'); // default order  

	function __construct() {
		parent::__construct();
	}
    function sendInvoiceMailToVendor($InvoiceData,$file){
        /***************send email to vendor***************************/
        if(!empty($InvoiceData)){
            $vendorname = $InvoiceData['transactiondetail']['membername'];
            $vendoremail = $InvoiceData['transactiondetail']['email'];
            
            if(!empty($vendoremail)){
                $mailto = $vendoremail;
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
                $emailSend = $this->Purchase_invoice->mail_attachment($file, ORDER_PATH, $mailto, $from_mail, $from_name, $from_mail, $subject, $mailBodyArr,$mailid);

                return $emailSend;
            }
        }
        return false;
    }
    function generatetransactionpdf($transactionid,$type){

		$companyname = $this->getCompanyName();
        $companyname = str_replace(" ", "", strtolower($companyname['businessname']));
        
        if ($transactionid) {

            if($type==0){
                $this->load->model('Purchase_order_model','Purchase_order');
                $PostData['transactiondata'] = $this->Purchase_order->getOrderDetails($transactionid);
                $PostData['printtype'] = "order";
                $PostData['heading'] = "Order";
                $transactionnumber = $PostData['transactiondata']['transactiondetail']['orderid'];
                $DIRECTORY_PATH = ORDER_PATH;
                $DIRECTORY = ORDER;
            }else if($type==2){
                $PostData['transactiondata'] = $this->getInvoiceDetails($transactionid);
                $PostData['printtype'] = "purchase-invoice";
                $PostData['heading'] = "Purchase Invoice";
                $transactionnumber = $PostData['transactiondata']['transactiondetail']['invoiceno'];
                $DIRECTORY_PATH = INVOICE_PATH;
                $DIRECTORY = INVOICE;
            }
            $PostData['hideonprint'] = '1';
            
            $this->load->model('Invoice_setting_model','Invoice_setting');
            $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();

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
    function confirmOnCreditNotesForInvoiceCancellation($invoiceid){
        $this->load->model("Purchase_credit_note_model","Purchase_credit_note");
       
        $data = $this->Purchase_credit_note->getCreditNotesByInvoiceId($invoiceid);
        
        if(!empty($data)){
            $status=array();
            foreach($data as $val){
                $status[] =  $val['status'];
            }
            if(!in_array("1", $status)){
                $updatedata = array("status"=>"2",
                                    "cancelreason"=>"Invoice Cancelled.");
                
                $this->Purchase_credit_note->_where = array("(FIND_IN_SET('".$invoiceid."', invoiceid)>0)"=>null);
                $this->Purchase_credit_note->Edit($updatedata);

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

    /* function generatetransactionpdf($transactionid,$type){

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
            $filename = $companyname."-".$transactionnumber.".pdf";
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
    } */

    function getInvoiceDetails($transactionid){

        $transactiondata['transactiondetail'] = $transactiondata['transactionproduct'] = array();
        
        $query = $this->readdb->select("i.id,i.orderid as grnid,i.addressid,i.shippingaddressid,i.memberid,i.sellermemberid as vendorid,m.gstno,i.status, i.invoiceno,i.invoicedate,i.createddate,i.billingaddress,i.shippingaddress,

                                    ma.name as vendorname,ma.address,ma.postalcode as postcode,ma.mobileno,ma.email,
                                    ct.name as cityname,
                                    pr.name as provincename, cn.name as countryname,
                                    
                                    shipper.name as shippingname,
                                    shipper.mobileno as shippingmobileno,
                                    shipper.email as shippingemail,
                                   
                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    i.amount as payableamount,i.remarks,
                                    i.globaldiscount,

                                    IF((SELECT count(tp.id) FROM ".tbl_transactionproducts." as tp WHERE tp.transactiontype=3 AND tp.transactionid=i.id AND tp.discount>0)>0,1,0) as displaydiscountcolumn,
                                    
                                    IF((SELECT count(id) FROM ".tbl_goodsreceivednotes." WHERE id IN (i.orderid))>0,1,0) as gstprice,

                                    @netamount:=IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,

                                    IFNULL(
                                        (SELECT pm.name FROM ".tbl_transaction." as tr 
                                                        INNER JOIN ".tbl_paymentmethod." as pm on pm.id IN (SELECT paymentmethodid FROM ".tbl_paymentgateway." WHERE paymentgatewaytype=tr.paymentgetwayid GROUP BY paymentmethodid) 
                                                        WHERE tr.orderid IN (SELECT orderid FROM ".tbl_goodsreceivednotes." WHERE id=i.orderid)
                                                        LIMIT 1
                                    ),'COD') as paymentmethod,
                            ")

                            ->from($this->_table." as i")
                            ->join(tbl_member." as m","m.id=i.sellermemberid","LEFT") 
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
                                            "billingaddressid"=>$rowdata['addressid'],
                                            "shippingaddressid"=>$rowdata['shippingaddressid'],
                                            "grnid"=>ucwords($rowdata['grnid']),
                                            "invoiceno"=>$rowdata['invoiceno'],
                                            "invoicedate"=>$this->general_model->displaydate($rowdata['invoicedate']),
                                            "createddate"=>$this->general_model->displaydate($rowdata['createddate']),
                                            "membername"=>ucwords($rowdata['vendorname']),
                                            "memberid"=>$rowdata['vendorid'],
                                            "mobileno"=>$rowdata['mobileno'],
                                            "email"=>$rowdata['email'],
                                            "gstno"=>$rowdata['gstno'],
                                            "status"=>$rowdata['status'],
                                            "address"=>$address,
                                            "billingaddress"=>$rowdata['billingaddress'],
                                            "igst"=>$rowdata['igst'],
                                            "payableamount"=>$rowdata['payableamount'],
                                            "netamount"=>$rowdata['netamount'],
                                            "remarks"=>$rowdata['remarks'],
                                            "globaldiscount"=>$rowdata['globaldiscount'],
                                            "displaydiscountcolumn"=>$rowdata['displaydiscountcolumn'],
                                            "shippingmembername"=>$rowdata['shippingname'],
                                            "shippingaddress"=>$rowdata['shippingaddress'],
                                            "shippingmobileno"=>$rowdata['shippingmobileno'],
                                            "shippingemail"=>$rowdata['shippingemail'],
                                            "gstprice"=>$rowdata['gstprice'],
                                            "paymentmethod"=>$rowdata['paymentmethod'],
                                            );

        $query = $this->readdb->select("CONCAT(tp.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_transactionvariant." WHERE transactionproductid=tp.id AND transactionid=".$transactionid."),''),' | ',(SELECT name FROM ".tbl_productcategory." WHERE id=p.categoryid),IFNULL((SELECT CONCAT(' (',name,')') FROM ".tbl_brand." WHERE id=p.brandid),'')) as name,tp.quantity,tp.price,tp.tax,tp.hsncode,tp.discount,
        (SELECT grnnumber FROM ".tbl_goodsreceivednotes." WHERE id IN (SELECT transactionid FROM ".tbl_transactionproducts." WHERE id=tp.referenceproductid)) as orderid,
        (SELECT originalprice FROM ".tbl_orderproducts." WHERE id IN (SELECT referenceproductid FROM ".tbl_transactionproducts." WHERE id=tp.referenceproductid)) as originalprice,
        IFNULL((select filename from ".tbl_productimage." where productid=p.id limit 1),'') as productimage,tp.remarks")
                            ->from(tbl_transactionproducts." as tp")
                            ->join(tbl_product." as p","p.id=tp.productid","LEFT")
                            ->where("tp.transactiontype=3 AND tp.transactionid=".$transactionid)
                            ->get();
        $transactiondata['transactionproduct'] =  $query->result_array();
        // print_r($transactiondata['transactionproduct']); exit;
        $query = $this->readdb->select("ecm.extrachargesname,ecm.taxamount,ecm.amount")
                            ->from(tbl_extrachargemapping." as ecm")
                            ->where("ecm.referenceid=".$transactionid." AND ecm.type=2")
                            ->get();
        $transactiondata['extracharges'] =  $query->result_array();

        return $transactiondata;
    }

   /*  function getShipperDetails(){
        $query = $this->readdb->select($this->_fields)
                            ->from(tbl_settings)
                           ->get();
        return $query->row_array();                 
    } */

    function getInvoiceDataByID($ID){
		$query = $this->readdb->select("id,invoiceno,invoicedate,memberid,addressid,shippingaddressid,remarks,taxamount,amount,orderid")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
    }
    
    function getOrderProductsByOrderIDOrVendorID($vendorid,$orderid,$transactionid=0){

        if(!empty($transactionid)){
            $where_sum = " AND i.status NOT IN (2) AND tp.id != trp.id";
            $sql_edit = "trp.quantity as editquantity,";
        }else{
            $sql_edit = "";
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

                                    o.gstprice
                                    
                                ");
        $this->readdb->from(tbl_purchaseorders." as o");                           
        $this->readdb->join(tbl_orderproducts." as op", "op.orderid=o.id", "INNER");
        
        if(!empty($transactionid)){
            $this->readdb->join(tbl_transactionproducts." as trp", "trp.referenceproductid = op.id AND trp.transactionid=".$transactionid, "INNER");
        }
        $this->readdb->join(tbl_memberaddress." as ma","ma.id=o.addressid","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->where("FIND_IN_SET(o.id, '".$orderid."') AND o.sellermemberid=".$vendorid." AND o.memberid=0 AND o.isdelete=0");
        $this->readdb->order_by("op.id","DESC");
        $query = $this->readdb->get();
        // echo $this->readdb->last_query(); exit;
        
        return $query->result_array();             
    }
    
    function getOrderProductsByGRNIDOrVendorID($vendorid,$grnid,$transactionid=0){

        if(!empty($transactionid)){
            $where_sum = " AND grn.status NOT IN (2) AND tp.id != trp2.id";
            $sql_edit = "trp2.quantity as editquantity,";
        }else{
            $sql_edit = "";
            $where_sum = " AND grn.status NOT IN (2)";
        }
        $this->readdb->select("grn.id as grnid,grn.grnnumber,trp.id as transactionproductsid,(grn.taxamount+grn.amount) as payableamount,
                                    IFNULL((SELECT id
                                    FROM ".tbl_transactionproducts." as tp
                                    WHERE tp.referenceproductid = trp.id AND trp.transactiontype=3 AND (tp.transactionid='".$transactionid."' OR ''='".$transactionid."') LIMIT 1
                                    ),'') as invtransactionproductsid,
                                    trp.productid,
                                    CONCAT(trp.name,' ',IFNULL((SELECT CONCAT('[',GROUP_CONCAT(variantvalue),']') FROM ".tbl_transactionvariant." WHERE transactionproductid=trp.id),'')) as productname,
                                    trp.priceid as combinationid,
                                    trp.quantity,
                                    trp.discount,
                                    trp.price as amount,
                                    (SELECT originalprice FROM ".tbl_orderproducts." WHERE id=trp.referenceproductid) as originalprice,
                                    trp.hsncode,trp.tax,trp.isvariant,trp.name,
                                    IFNULL((trp.price*trp.quantity),0) as finalprice,
                                    IF(pr.id=12 OR pr.name='gujarat',1,0) as igst,
                                    
                                    @productamount:=IFNULL((trp.price - (trp.price * trp.discount / 100)),0) as productamount, 
                                    IFNULL((@productamount + (@productamount * trp.tax / 100)),0) as pricewithtax, 

                                    @invoiceqty:=IFNULL((SELECT SUM(tp.quantity)
                                    FROM ".$this->_table." as i
                                    INNER JOIN ".tbl_transactionproducts." as tp ON tp.transactionid=i.id AND tp.transactiontype=3
                                    WHERE tp.referenceproductid = trp.id AND find_in_set(grn.id, i.orderid)
                                    ".$where_sum."
                                    ),0) as invoiceqty,
                                    ".$sql_edit."

                                    (SELECT gstprice FROM ".tbl_purchaseorders." WHERE id IN (grn.orderid) LIMIT 1) as gstprice
                                    
                                ");
        $this->readdb->from(tbl_goodsreceivednotes." as grn");                           
        $this->readdb->join(tbl_transactionproducts." as trp", "trp.transactionid=grn.id AND trp.transactiontype=4", "INNER");
        
        if(!empty($transactionid)){
            $this->readdb->join(tbl_transactionproducts." as trp2", "trp2.referenceproductid = trp.id AND trp2.transactiontype=3 AND trp2.transactionid=".$transactionid, "INNER");
        }
        $this->readdb->join(tbl_memberaddress." as ma","ma.id=(SELECT addressid FROM ".tbl_purchaseorders." WHERE id IN (grn.orderid) LIMIT 1)","LEFT");
        $this->readdb->join(tbl_city." as ct","ct.id=ma.cityid","LEFT");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","LEFT");
        $this->readdb->where("FIND_IN_SET(grn.id, '".$grnid."') AND grn.sellermemberid=".$vendorid." AND grn.memberid=0");
        $this->readdb->order_by("trp.id","DESC");
        $query = $this->readdb->get();
        // echo $this->readdb->last_query(); exit;
        
        return $query->result_array();             
    }
    function getOrderVariantsData($orderid,$orderproductsid){
        
        $query = $this->readdb->select("ov.id,ov.variantid,ov.variantname,ov.variantvalue")
                    ->from(tbl_ordervariant." as ov")                          
                    ->where("FIND_IN_SET(ov.orderid, '".$orderid."') AND ov.orderproductid=".$orderproductsid)
                    ->get();

        return $query->result_array();       
    }
    function getGRNProductVariantsData($grnid,$transactionproductid){
        
        $query = $this->readdb->select("tv.id,tv.variantid,tv.variantname,tv.variantvalue")
                    ->from(tbl_transactionvariant." as tv")                          
                    ->where("FIND_IN_SET(tv.transactionid, '".$grnid."') AND tv.transactionproductid=".$transactionproductid)
                    ->get();

        return $query->result_array();       
    }
    function getApprovedInvoiceByVendor($vendorid,$type=''){

        $query = $this->readdb->select("i.id,i.invoiceno,i.addressid as billingid,i.shippingaddressid as shippingid")
                    ->from(tbl_invoice." as i")
                    ->where("i.memberid=0 AND
                        i.sellermemberid=".$vendorid." AND 
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

    function getPurchaseInvoiceByVendor($vendorid,$paymentreceiptid=0){
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
                        ->where("i.sellermemberid=".$vendorid." AND
                            i.memberid=0
                            AND i.status = 0 AND
                            
                            IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) 
                            
                            > 
                            IFNULL((SELECT SUM(prt.amount) 
                            FROM ".tbl_paymentreceipttransactions." as prt 
                            WHERE prt.invoiceid = i.id AND prt.paymentreceiptid IN (SELECT id FROM ".tbl_paymentreceipt." WHERE isagainstreference=1 AND status!=2)".$sql."),0) 

                            ")
                        ->order_by("i.id", "DESC")
                        ->get();
		
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
                                    IFNULL((i.amount + i.taxamount - i.globaldiscount),0) as netamount,

                                    i.invoicedate,
                                    i.globaldiscount as discountamount, 
                                    
                                    i.addressid as billingaddressid,
                                    i.shippingaddressid
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
        
        $vendorid = (isset($_REQUEST['vendorid']))?$_REQUEST['vendorid']:'';
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $status = $_REQUEST['status'];

		$this->readdb->select("i.id,i.sellermemberid as vendorid,
                            (SELECT GROUP_CONCAT(grn.id) FROM ".tbl_goodsreceivednotes." as grn WHERE FIND_IN_SET(grn.id,i.orderid)>0) as grnid,
                            (SELECT GROUP_CONCAT(grn.grnnumber) FROM ".tbl_goodsreceivednotes." as grn WHERE FIND_IN_SET(grn.id,i.orderid)>0) as grnnumbers,
                            i.invoiceno,
                            i.invoicedate,
                            i.taxamount,
                            i.amount,
                            IFNULL((i.amount + i.taxamount - i.globaldiscount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0) as netamount,
                            i.status,
                            vendor.name as vendorname,
                            vendor.membercode as vendorcode,
                            vendor.channelid as vendorchannelid,
                            
                            @primarynumber:=IF(vendor.isprimarywhatsappno=1,vendor.mobile,'') as primarynumber,
                            @secondarynumber:=IF(vendor.issecondarywhatsappno=1,vendor.secondarymobileno,'') as secondarynumber,
                            IF(@primarynumber='',IF(vendor.issecondarywhatsappno=1,CONCAT(vendor.secondarycountrycode,vendor.secondarymobileno),''),CONCAT(vendor.countrycode,@primarynumber)) as whatsappno,

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
                            where FIND_IN_SET(cnp.creditnoteid, (SELECT GROUP_CONCAT(id) FROM ".tbl_creditnote." where FIND_IN_SET(i.id, invoiceid)>0 AND status!=2))>0 AND tp.transactionid=i.id),0)),1,0) as allowcreditnote
                            
                        ");

        $this->readdb->from($this->_table." as i");
        $this->readdb->join(tbl_member." as vendor","vendor.id=i.sellermemberid","LEFT");
        
        if(!empty($vendorid)){
            $this->readdb->where("i.sellermemberid = '".$vendorid."'"); //Filter seller member
        }
        $this->readdb->where("i.memberid=0"); //Filter seller 
        if($status != -1){
            $this->readdb->where("i.status=".$status);
        }
        $this->readdb->where("(i.invoicedate BETWEEN '".$startdate."' AND '".$enddate."')");

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
}
?>