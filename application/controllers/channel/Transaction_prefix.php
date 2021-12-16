<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_prefix extends Channel_Controller 
{
    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu','Transaction_prefix');
        $this->load->model('Transaction_prefix_model','Transaction_prefix');  
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Transaction Prefix";
        $this->viewData['module'] = "transaction_prefix/Transaction_prefix";

        $this->channel_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->channel_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->channel_headerlib->add_javascript("transaction_prefix","pages/transaction_prefix.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);        
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
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');

        $quotationprefix = (!isset($PostData['quotationprefix']))?0:1;
        $quotationprefixformat = ($quotationprefix==1)?$PostData['quotationprefixformat']:"";
        $quotationprefixlastno = ($quotationprefix==1)?(!empty($PostData['quotationprefixlastno'])?$PostData['quotationprefixlastno']:1):"";
        $quotationprefixsuffixlength = ($quotationprefix==1)?$PostData['quotationprefixsuffixlength']:"";
        
        $orderprefix = (!isset($PostData['orderprefix']))?0:1;
        $orderprefixformat = ($orderprefix==1)?$PostData['orderprefixformat']:"";
        $orderprefixlastno = ($orderprefix==1)?(!empty($PostData['orderprefixlastno'])?$PostData['orderprefixlastno']:1):"";
        $orderprefixsuffixlength = ($orderprefix==1)?$PostData['orderprefixsuffixlength']:"";

        $invoiceprefix = (!isset($PostData['invoiceprefix']))?0:1;
        $invoiceprefixformat = ($invoiceprefix==1)?$PostData['invoiceprefixformat']:"";
        $invoiceprefixlastno = ($invoiceprefix==1)?(!empty($PostData['invoiceprefixlastno'])?$PostData['invoiceprefixlastno']:1):"";
        $invoiceprefixsuffixlength = ($invoiceprefix==1)?$PostData['invoiceprefixsuffixlength']:"";

        $creditnoteprefix = (!isset($PostData['creditnoteprefix']))?0:1;
        $creditnoteprefixformat = ($creditnoteprefix==1)?$PostData['creditnoteprefixformat']:"";
        $creditnoteprefixlastno = ($creditnoteprefix==1)?(!empty($PostData['creditnoteprefixlastno'])?$PostData['creditnoteprefixlastno']:1):"";
        $creditnoteprefixsuffixlength = ($creditnoteprefix==1)?$PostData['creditnoteprefixsuffixlength']:"";

        $stockgeneralvoucherprefix = (!isset($PostData['stockgeneralvoucherprefix']))?0:1;
        $stockgeneralvoucherprefixformat = ($stockgeneralvoucherprefix==1)?$PostData['stockgeneralvoucherprefixformat']:"";
        $stockgeneralvoucherprefixlastno = ($stockgeneralvoucherprefix==1)?(!empty($PostData['stockgeneralvoucherprefixlastno'])?$PostData['stockgeneralvoucherprefixlastno']:1):"";
        $stockgeneralvoucherprefixsuffixlength = ($stockgeneralvoucherprefix==1)?$PostData['stockgeneralvoucherprefixsuffixlength']:"";

        $this->Transaction_prefix->_where = ("channelid=".$channelid." AND memberid=".$memberid);
        $Check = $this->Transaction_prefix->getRecordById();
        if(empty($Check)){
            //ADD
            $insertData = array();
            //Quotation Prefix
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>0,
                "transactionprefix"=>$quotationprefix,
                "transactionprefixformat"=>$quotationprefixformat,
                "lastno"=>$quotationprefixlastno,
                "suffixlength"=>$quotationprefixsuffixlength,
                "usertype"=>1,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            //Order Prefix
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>1,
                "transactionprefix"=>$orderprefix,
                "transactionprefixformat"=>$orderprefixformat,
                "lastno"=>$orderprefixlastno,
                "suffixlength"=>$orderprefixsuffixlength,
                "usertype"=>1,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>2,
                "transactionprefix"=>$invoiceprefix,
                "transactionprefixformat"=>$invoiceprefixformat,
                "lastno"=>$invoiceprefixlastno,
                "suffixlength"=>$invoiceprefixsuffixlength,
                "usertype"=>1,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $insertData[] = array("channelid"=>$channelid,
                "memberid"=>$memberid,
                "transactiontype"=>3,
                "transactionprefix"=>$creditnoteprefix,
                "transactionprefixformat"=>$creditnoteprefixformat,
                "lastno"=>$creditnoteprefixlastno,
                "suffixlength"=>$creditnoteprefixsuffixlength,
                "usertype"=>1,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
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

            $this->Transaction_prefix->add_batch($insertData);
       
        }else{
            //Update
            $qtkey = array_search('0', array_column($Check, 'transactiontype'));
            $ordkey = array_search('1', array_column($Check, 'transactiontype'));
            $inkey = array_search('2', array_column($Check, 'transactiontype'));
            $cnkey = array_search('3', array_column($Check, 'transactiontype'));
            $sgvkey = array_search('4', array_column($Check, 'transactiontype'));

            $updateData = array();
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
            $updateData[] = array(
                "id" => $Check[$inkey]['id'],
                "transactionprefix"=>$invoiceprefix,
                "transactionprefixformat"=>$invoiceprefixformat,
                "lastno"=>$invoiceprefixlastno,
                "suffixlength"=>$invoiceprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $updateData[] = array(
                "id" => $Check[$cnkey]['id'],
                "transactionprefix"=>$creditnoteprefix,
                "transactionprefixformat"=>$creditnoteprefixformat,
                "lastno"=>$creditnoteprefixlastno,
                "suffixlength"=>$creditnoteprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $updateData[] = array(
                "id" => $Check[$sgvkey]['id'],
                "transactionprefix"=>$stockgeneralvoucherprefix,
                "transactionprefixformat"=>$stockgeneralvoucherprefixformat,
                "lastno"=>$stockgeneralvoucherprefixlastno,
                "suffixlength"=>$stockgeneralvoucherprefixsuffixlength,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
           
            $this->Transaction_prefix->edit_batch($updateData,"id");
        }
        echo 1;
    }
}