<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_prefix extends Admin_Controller 
{
    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Transaction_prefix');
        $this->load->model('Transaction_prefix_model','Transaction_prefix');  
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Transaction Prefix";
        $this->viewData['module'] = "transaction_prefix/Transaction_prefix";

        // $this->viewData['leaddata'] = $this->Transaction_prefix->getIndiaMartLeadData();       
        
        // $this->load->model('User_model','User');  
        // $this->viewData['employeename'] = $this->User->getActiveUsersList();       
        
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("transaction_prefix","pages/transaction_prefix.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);        
    }
   
    public function getTransactionPrefixData(){
        $PostData = $this->input->post();
        $channelid = $PostData['channelid'];
        $memberid = $PostData['memberid'];

        $transactionprefixdata = $this->Transaction_prefix->getTransactionPrefixData($channelid,$memberid);
        echo json_encode($transactionprefixdata);
    }

    public function update_transaction_prefix(){
                               
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $channelid = $PostData['channelid'];
        $memberid = $PostData['memberid'];

        $quotationprefix = (!isset($PostData['quotationprefix']))?0:1;
        $quotationprefixformat = ($quotationprefix==1)?$PostData['quotationprefixformat']:"";
        $quotationprefixlastno = ($quotationprefix==1)?(!empty($PostData['quotationprefixlastno'])?$PostData['quotationprefixlastno']:1):"";
        $quotationprefixsuffixlength = ($quotationprefix==1)?$PostData['quotationprefixsuffixlength']:"";
        
        $purchasequotationprefix = (!isset($PostData['purchasequotationprefix']))?0:1;
        $purchasequotationprefixformat = ($purchasequotationprefix==1)?$PostData['purchasequotationprefixformat']:"";
        $purchasequotationprefixlastno = ($purchasequotationprefix==1)?(!empty($PostData['purchasequotationprefixlastno'])?$PostData['purchasequotationprefixlastno']:1):"";
        $purchasequotationprefixsuffixlength = ($purchasequotationprefix==1)?$PostData['purchasequotationprefixsuffixlength']:"";

        $orderprefix = (!isset($PostData['orderprefix']))?0:1;
        $orderprefixformat = ($orderprefix==1)?$PostData['orderprefixformat']:"";
        $orderprefixlastno = ($orderprefix==1)?(!empty($PostData['orderprefixlastno'])?$PostData['orderprefixlastno']:1):"";
        $orderprefixsuffixlength = ($orderprefix==1)?$PostData['orderprefixsuffixlength']:"";

        $invoiceprefix = (!isset($PostData['invoiceprefix']))?0:1;
        $invoiceprefixformat = ($invoiceprefix==1)?$PostData['invoiceprefixformat']:"";
        $invoiceprefixlastno = ($invoiceprefix==1)?(!empty($PostData['invoiceprefixlastno'])?$PostData['invoiceprefixlastno']:1):"";
        $invoiceprefixsuffixlength = ($invoiceprefix==1)?$PostData['invoiceprefixsuffixlength']:"";

        $purchaseinvoiceprefix = (!isset($PostData['purchaseinvoiceprefix']))?0:1;
        $purchaseinvoiceprefixformat = ($purchaseinvoiceprefix==1)?$PostData['purchaseinvoiceprefixformat']:"";
        $purchaseinvoiceprefixlastno = ($purchaseinvoiceprefix==1)?(!empty($PostData['purchaseinvoiceprefixlastno'])?$PostData['purchaseinvoiceprefixlastno']:1):"";
        $purchaseinvoiceprefixsuffixlength = ($purchaseinvoiceprefix==1)?$PostData['purchaseinvoiceprefixsuffixlength']:"";

        $creditnoteprefix = (!isset($PostData['creditnoteprefix']))?0:1;
        $creditnoteprefixformat = ($creditnoteprefix==1)?$PostData['creditnoteprefixformat']:"";
        $creditnoteprefixlastno = ($creditnoteprefix==1)?(!empty($PostData['creditnoteprefixlastno'])?$PostData['creditnoteprefixlastno']:1):"";
        $creditnoteprefixsuffixlength = ($creditnoteprefix==1)?$PostData['creditnoteprefixsuffixlength']:"";

        $purchasecreditnoteprefix = (!isset($PostData['purchasecreditnoteprefix']))?0:1;
        $purchasecreditnoteprefixformat = ($purchasecreditnoteprefix==1)?$PostData['purchasecreditnoteprefixformat']:"";
        $purchasecreditnoteprefixlastno = ($purchasecreditnoteprefix==1)?(!empty($PostData['purchasecreditnoteprefixlastno'])?$PostData['purchasecreditnoteprefixlastno']:1):"";
        $purchasecreditnoteprefixsuffixlength = ($purchasecreditnoteprefix==1)?$PostData['purchasecreditnoteprefixsuffixlength']:"";

        $stockgeneralvoucherprefix = (!isset($PostData['stockgeneralvoucherprefix']))?0:1;
        $stockgeneralvoucherprefixformat = ($stockgeneralvoucherprefix==1)?$PostData['stockgeneralvoucherprefixformat']:"";
        $stockgeneralvoucherprefixlastno = ($stockgeneralvoucherprefix==1)?(!empty($PostData['stockgeneralvoucherprefixlastno'])?$PostData['stockgeneralvoucherprefixlastno']:1):"";
        $stockgeneralvoucherprefixsuffixlength = ($stockgeneralvoucherprefix==1)?$PostData['stockgeneralvoucherprefixsuffixlength']:"";

        $purchaseorderprefix = (!isset($PostData['purchaseorderprefix']))?0:1;
        $purchaseorderprefixformat = ($purchaseorderprefix==1)?$PostData['purchaseorderprefixformat']:"";
        $purchaseorderprefixlastno = ($purchaseorderprefix==1)?(!empty($PostData['purchaseorderprefixlastno'])?$PostData['purchaseorderprefixlastno']:1):"";
        $purchaseorderprefixsuffixlength = ($purchaseorderprefix==1)?$PostData['purchaseorderprefixsuffixlength']:"";

        $goodsreceivednotesprefix = (!isset($PostData['goodsreceivednotesprefix']))?0:1;
        $goodsreceivednotesprefixformat = ($goodsreceivednotesprefix==1)?$PostData['goodsreceivednotesprefixformat']:"";
        $goodsreceivednotesprefixlastno = ($goodsreceivednotesprefix==1)?(!empty($PostData['goodsreceivednotesprefixlastno'])?$PostData['goodsreceivednotesprefixlastno']:1):"";
        $goodsreceivednotesprefixsuffixlength = ($goodsreceivednotesprefix==1)?$PostData['goodsreceivednotesprefixsuffixlength']:"";

        $insertData = array();
        $updateData = array();
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=0");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            //ADD
            //Quotation Prefix
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>0,
                "transactionprefix"=>$quotationprefix,
                "transactionprefixformat"=>$quotationprefixformat,
                "lastno"=>$quotationprefixlastno,
                "suffixlength"=>$quotationprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $qtkey = array_search('0', array_column($Check, 'transactiontype'));
            //Quotation Prefix
            $updateData[] = array(
                "id" => $Check[$qtkey]['id'],
                "transactionprefix"=>$quotationprefix,
                "transactionprefixformat"=>$quotationprefixformat,
                "lastno"=>$quotationprefixlastno,
                "suffixlength"=>$quotationprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }

        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=1");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            //Order Prefix
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>1,
                "transactionprefix"=>$orderprefix,
                "transactionprefixformat"=>$orderprefixformat,
                "lastno"=>$orderprefixlastno,
                "suffixlength"=>$orderprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $ordkey = array_search('1', array_column($Check, 'transactiontype'));
            
            //Order Prefix
            $updateData[] = array(
                "id" => $Check[$ordkey]['id'],
                "transactionprefix"=>$orderprefix,
                "transactionprefixformat"=>$orderprefixformat,
                "lastno"=>$orderprefixlastno,
                "suffixlength"=>$orderprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }

        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=2");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            //Invoice Prefix
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>2,
                "transactionprefix"=>$invoiceprefix,
                "transactionprefixformat"=>$invoiceprefixformat,
                "lastno"=>$invoiceprefixlastno,
                "suffixlength"=>$invoiceprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $inkey = array_search('2', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$inkey]['id'],
                "transactionprefix"=>$invoiceprefix,
                "transactionprefixformat"=>$invoiceprefixformat,
                "lastno"=>$invoiceprefixlastno,
                "suffixlength"=>$invoiceprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }
            
        //Credit Note Prefix
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=3");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>3,
                "transactionprefix"=>$creditnoteprefix,
                "transactionprefixformat"=>$creditnoteprefixformat,
                "lastno"=>$creditnoteprefixlastno,
                "suffixlength"=>$creditnoteprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $cnkey = array_search('3', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$cnkey]['id'],
                "transactionprefix"=>$creditnoteprefix,
                "transactionprefixformat"=>$creditnoteprefixformat,
                "lastno"=>$creditnoteprefixlastno,
                "suffixlength"=>$creditnoteprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }
         
        //Stock Gernel Voucher Prefix
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=4");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>4,
                "transactionprefix"=>$stockgeneralvoucherprefix,
                "transactionprefixformat"=>$stockgeneralvoucherprefixformat,
                "lastno"=>$stockgeneralvoucherprefixlastno,
                "suffixlength"=>$stockgeneralvoucherprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $sgvkey = array_search('4', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$sgvkey]['id'],
                "transactionprefix"=>$stockgeneralvoucherprefix,
                "transactionprefixformat"=>$stockgeneralvoucherprefixformat,
                "lastno"=>$stockgeneralvoucherprefixlastno,
                "suffixlength"=>$stockgeneralvoucherprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }
         
        //Purchase Order Prefix
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=5");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>5,
                "transactionprefix"=>$purchaseorderprefix,
                "transactionprefixformat"=>$purchaseorderprefixformat,
                "lastno"=>$purchaseorderprefixlastno,
                "suffixlength"=>$purchaseorderprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $pokey = array_search('5', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$pokey]['id'],
                "transactionprefix"=>$purchaseorderprefix,
                "transactionprefixformat"=>$purchaseorderprefixformat,
                "lastno"=>$purchaseorderprefixlastno,
                "suffixlength"=>$purchaseorderprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }

        //Purchase Quotation Prefix
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=6");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>6,
                "transactionprefix"=>$purchasequotationprefix,
                "transactionprefixformat"=>$purchasequotationprefixformat,
                "lastno"=>$purchasequotationprefixlastno,
                "suffixlength"=>$purchasequotationprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $pqkey = array_search('6', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$pqkey]['id'],
                "transactionprefix"=>$purchasequotationprefix,
                "transactionprefixformat"=>$purchasequotationprefixformat,
                "lastno"=>$purchasequotationprefixlastno,
                "suffixlength"=>$purchasequotationprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }

        //Purchase Invoice Prefix
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=7");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>7,
                "transactionprefix"=>$purchaseinvoiceprefix,
                "transactionprefixformat"=>$purchaseinvoiceprefixformat,
                "lastno"=>$purchaseinvoiceprefixlastno,
                "suffixlength"=>$purchaseinvoiceprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $pikey = array_search('7', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$pikey]['id'],
                "transactionprefix"=>$purchaseinvoiceprefix,
                "transactionprefixformat"=>$purchaseinvoiceprefixformat,
                "lastno"=>$purchaseinvoiceprefixlastno,
                "suffixlength"=>$purchaseinvoiceprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }

        //Purchase Credit Note Prefix
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=8");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>8,
                "transactionprefix"=>$purchasecreditnoteprefix,
                "transactionprefixformat"=>$purchasecreditnoteprefixformat,
                "lastno"=>$purchasecreditnoteprefixlastno,
                "suffixlength"=>$purchasecreditnoteprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $pcnkey = array_search('8', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$pcnkey]['id'],
                "transactionprefix"=>$purchasecreditnoteprefix,
                "transactionprefixformat"=>$purchasecreditnoteprefixformat,
                "lastno"=>$purchasecreditnoteprefixlastno,
                "suffixlength"=>$purchasecreditnoteprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }

        //Goods Received Notes Prefix
        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid." AND transactiontype=9");
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>9,
                "transactionprefix"=>$goodsreceivednotesprefix,
                "transactionprefixformat"=>$goodsreceivednotesprefixformat,
                "lastno"=>$goodsreceivednotesprefixlastno,
                "suffixlength"=>$goodsreceivednotesprefixsuffixlength,
                "usertype"=>0,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }else{
            $grnkey = array_search('9', array_column($Check, 'transactiontype'));
            
            $updateData[] = array(
                "id" => $Check[$grnkey]['id'],
                "transactionprefix"=>$goodsreceivednotesprefix,
                "transactionprefixformat"=>$goodsreceivednotesprefixformat,
                "lastno"=>$goodsreceivednotesprefixlastno,
                "suffixlength"=>$goodsreceivednotesprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
        }
        if(!empty($insertData)){
            $this->Transaction_prefix->add_batch($insertData);
        }

        if(!empty($updateData)){
            $this->Transaction_prefix->edit_batch($updateData,"id");
        }
        echo 1;
    }
}