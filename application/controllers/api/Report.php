<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends MY_Controller {

    public $PostData = array();
    public $data = array();

    function __construct() {
        parent::__construct();
        if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
            $this->PostData = $this->input->post();

            if (isset($this->PostData['apikey'])) {
                $apikey = $this->PostData['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response('fail', API_KEY_NOT_MATCH);
                }
            } else {
                ws_response('fail', API_KEY_MISSING);
                exit;
            }
        } else {
            ws_response('fail', 'Authentication failed');
            exit;
        }
        $this->load->model('Invoice_model','Invoice'); 
    }
     
    function getgstrreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : 0;
        $channelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $type = isset($PostData['type']) ? trim($PostData['type']) : '';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        
        if(empty($userid) || $fromdate == "" || $todate == "" || $counter=='' || $type=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                if($type=="GSTR1"){
                    $this->load->model('Gstr1_report_model', 'Gstr1_report');
                    $reportdata = $this->Gstr1_report->getGSTR1ReportDataOnAPI($userid,$channelid,$memberid,$fromdate,$todate,$counter);
                }else if($type=="GSTR2"){
                    $this->load->model('Gstr2_report_model', 'Gstr2_report');
                    $reportdata = $this->Gstr2_report->getGSTR2ReportDataOnAPI($userid,$channelid,$memberid,$fromdate,$todate,$counter);
                }
                
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){

                        if($type=="GSTR1"){
                            $memberid = $row['sellerid'];
                            $membername = $row['sellername'];
                            $membercode = $row['sellercode'];
                            $memberchannelid = $row['sellerchannelid'];
                        }else{
                            $memberid = $row['buyerid'];
                            $membername = $row['buyername'];
                            $membercode = $row['buyercode'];
                            $memberchannelid = $row['buyerchannelid'];
                        }
                        $this->data[] = array(
                            "gstno"=>$row['gstno'],
                            "memberid"=>$memberid,
                            "membername"=>$membername,
                            "membercode"=>$membercode,
                            "memberchannelid"=>$memberchannelid,
                            "cityname"=>$row['cityname'],
                            "invoiceno"=>$row['invoiceno'],
                            "invoicedate"=>$row['invoicedate'],
                            "invoicevalue"=>$row['invoicevalue'],
                            "placeofsupply"=>$row['placeofsupply'],
                            "reversecharge"=>$row['reversecharge'],
                            "taxrate"=>$row['taxrate'],
                            "taxablevalue"=>$row['taxablevalue'],
                            "igst"=>$row['igst'],
                            "cgst"=>$row['cgst'],
                            "sgst"=>$row['sgst']
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getminimumstockreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        
        if(empty($userid) || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Minimum_stock_report_model', 'Minimum_stock_report');
                $reportdata = $this->Minimum_stock_report->getMinimumStockReportOnAPI($userid,$counter);
                
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){

                        $this->data[] = array(
                            "productid"=>$row['productid'],
                            "productname"=>$row['productname'],
                            "sku"=>$row['sku'],
                            "minimumstock"=>$row['minimumstocklimit'],
                            "currentstock"=>$row['stock'],
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getabcinventoryanalysisreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $classA = (!empty($PostData['classA'])) ?trim($PostData['classA']):50;
        $classB = (!empty($PostData['classB'])) ?trim($PostData['classB']):30;
        $classC = (!empty($PostData['classC'])) ?trim($PostData['classC']):20;
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        
        if(empty($userid) || $fromdate == "" || $todate == "" || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Abc_inventory_analysis_model', 'Abc_inventory_analysis');
                $reportdata = $this->Abc_inventory_analysis->getABCInventoryReportDataOnAPI($userid,$fromdate,$todate,$counter);
                
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){

                        if($row['cumulativeshare'] >= $classA){
                            $class = "A";
                        }else if($row['cumulativeshare'] <= $classC){
                            $class = "C";
                        }else{
                            $class = "B";
                        }

                        $this->data[] = array(
                            "productid"=>$row['productid'],
                            "productname"=>$row['productname'],
                            "sku"=>$row['sku'],
                            "price"=>number_format($row['price'],2,'.',''),
                            "sold"=>$row['sold'],
                            "cumulativeshare"=>number_format($row['cumulativeshare'],2,'.','')."%",
                            "class"=>$class,
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getquotationtoorderconversionreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $channelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        
        if(empty($userid) || $fromdate == "" || $todate == "" || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Quotation_to_order_conversion_model', 'Quotation_to_order_conversion');
                $reportdata = $this->Quotation_to_order_conversion->getQuotationToOrderConversionReportDataOnAPI($userid,$channelid,$memberid,$fromdate,$todate,$counter);
                
                if(!empty($reportdata)) {
                    ws_response('success','',$reportdata);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getordertodeliveredreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $channelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        
        if(empty($userid) || $fromdate == "" || $todate == "" || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();
            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Order_to_delivered_model', 'Order_to_delivered');
                $reportdata = $this->Order_to_delivered->getOrderToDeliveredReportDataOnAPI($userid,$channelid,$memberid,$fromdate,$todate,$counter);
                
                if(!empty($reportdata)) {
                    ws_response('success','',$reportdata);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getordercancelreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $level = isset($PostData['level']) ? trim($PostData['level']) : '';
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $channelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $countryid = isset($PostData['countryid']) ? trim($PostData['countryid']) : '';
        $provinceid = isset($PostData['provinceid']) ? trim($PostData['provinceid']) : '';
        $cityid = isset($PostData['cityid']) ? trim($PostData['cityid']) : '';
        $year = isset($PostData['year']) ? trim($PostData['year']) : '';
        $month = isset($PostData['month']) ? trim($PostData['month']) : '';
        
        if(empty($userid) || empty($level) || empty($year)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{


                $this->load->model('Cancelled_orders_report_model', 'Cancelled_orders_report');
                $reportdata = $this->Cancelled_orders_report->getCancelledOrdersReportData($year,$month,$countryid,$provinceid,$cityid,$channelid,$memberid,$userid);
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){

                        $this->data[] = array(
                            "memberid"=>$row['id'],
                            "membername"=>$row['name'],
                            "memberchannelid"=>$row['channelid'],
                            "membercode"=>$row['membercode'],
                            "channel"=>$row['channel'],
                            "totalcancelorder"=>$row['countcanncelorder'],
                            "year"=>$row['year'],
                            "month"=>$row['month'],
                            "countryname"=>$row['countryname'],
                            "provincename"=>$row['provincename'],
                            "cityname"=>$row['cityname'],
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getsalesreturnreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $level = isset($PostData['level']) ? trim($PostData['level']) : '';
        $type = isset($PostData['type']) ? trim($PostData['type']) : '';
        $fromdate = isset($PostData['fromdate']) ? trim($PostData['fromdate']) : '';
        $todate = isset($PostData['todate']) ? trim($PostData['todate']) : '';
        $channelid = !empty($PostData['channelid']) ? trim($PostData['channelid']) : 0;
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : '';
        $status = isset($PostData['status']) ? trim($PostData['status']) : '';
        
        if(empty($userid) || empty($level) || $type=="") {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{

                $this->load->model('Member_model', 'Member');
                $this->load->model('Credit_note_report_model', 'Sales_return_report');
                if($level==$channelid){
                    $memberdata = $this->Member->getActiveMemberByChannel($level,$userid);
                }else if($channelid!=0 && $memberid==''){
                    $memberdata = $this->Member->getActiveMemberByUnderMember($channelid,"");
                }else{
                    $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
                }

                if($type==1){
                    $startdate = $this->general_model->convertdate($fromdate);
                    $enddate = $this->general_model->convertdate($todate);
                    $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
                    $dateformat = 'd/m/Y';
                }else{
                    $startdate = $this->general_model->convertdate($fromdate,'Y-m-d');
                    $enddate = $this->general_model->convertdate($todate,'Y-m-d');
                    $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
                    $dateformat = 'm/Y';
                }
                $memberid = implode(',',array_column($memberdata,'id'));
                $creditnotedata = $this->Sales_return_report->getcreditnotedata($startdate,$enddate,$channelid,$memberid,$status,$type,$userid);
                $datearray = array_column($creditnotedata, 'date');
                
                if($channelid=='0'){
                    $memberdata = array(array('id'=>'0','name'=>"Company","channelid"=>0));
                }
                $result = array();
                foreach ($Date as $index=>$daterow) {
                    foreach ($memberdata as $memberrow) {
                        
                        if($memberrow['channelid']!=0){
                            $membername = $memberrow['name'];
                        }else{
                            $membername = 'Company';
                        }
                    
                        if (in_array($daterow, $datearray)) {
        
                            //check date on date array
                            $keys = array_keys($datearray, $daterow);
                    
                            //get array from search key
                            $searchdatedata = array_intersect_key($creditnotedata, array_flip($keys));
        
                            $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'buyermemberid')), $memberrow['id']);
                            
                            if(!empty($searchkeys)){
                                $totalsalesreturn = $searchdatedata[$searchkeys[0]]['totalsalesreturn'];
                            }else{
                                $totalsalesreturn = '0.00';
                            }
                        }else{
                            $totalsalesreturn = '0.00';
                        }
                        $result[] = array("membername"=>$membername,
                                        "date"=>$this->general_model->displaydate($daterow,$dateformat),
                                        "totalsalesreturn"=>number_format($totalsalesreturn,2,'.','')
                                    );
                    }
        
                }
                
                if(!empty($result)) {
                    ws_response('success','',$result);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getabcpaymentcycleanalysisreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $classA = (!empty($PostData['classA'])) ?trim($PostData['classA']):50;
        $classB = (!empty($PostData['classB'])) ?trim($PostData['classB']):30;
        $classC = (!empty($PostData['classC'])) ?trim($PostData['classC']):20;
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        $memberchannelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : "";
        
        if(empty($userid) || $fromdate == "" || $todate == "" || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Abc_payment_cycle_analysis_report_model', 'Abc_payment_cycle_analysis_report');
                $reportdata = $this->Abc_payment_cycle_analysis_report->getABCPaymentCycleAnalysisReportDataOnAPI($userid,$fromdate,$todate,$memberchannelid,$memberid,$counter);
                
                $totalaveragepaymentcycle = (!empty($reportdata))?array_sum(array_column($reportdata, "averagepaymentcycle")):0;

                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){

                        
                        $cumulativeshare = ($totalaveragepaymentcycle>0)?(100 - ($row['averagepaymentcycle'] * 100) / $totalaveragepaymentcycle):0;
                        
                        if($cumulativeshare >= $classA){
                            $class = "A";
                        }else if($cumulativeshare <= $classC){
                            $class = "C";
                        }else{
                            $class = "B";
                        }

                        $this->data[] = array(
                            "member"=>$row['name']." (".$row['membercode'].")",
                            "totalinvoice"=>$row['totalinvoice'],
                            "invoiceamount"=>number_format($row['invoiceamount'],2,'.',''),
                            "averagepaymentcycledays"=>$row['averagepaymentcycle'],
                            "cumulativeshare"=>number_format($cumulativeshare,2,'.','')."%",
                            "class"=>$class,
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getbalancereport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        $memberchannelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : "";
        $type = !empty($PostData['type']) ? trim($PostData['type']) : "0";
        
        if(empty($userid) || $fromdate == "" || $todate == "" || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Balance_report_model', 'Balance_report');
                $reportdata = $this->Balance_report->getBalanceReportDataOnAPI($userid,$fromdate,$todate,$memberchannelid,$memberid,$type,$counter);
                
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){

                        $this->data[] = array(
                            "member"=>$row['membername']." (".$row['membercode'].")",
                            "balancedate"=>$this->general_model->displaydate($this->general_model->getCurrentDate()),
                            "totalamount"=>number_format($row['closingbalance'],2,'.',''),
                            "type"=>$type
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getpointhistoryreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid = isset($PostData['level']) ? trim($PostData['level']) : '';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        $fromdate = (!empty($PostData['fromdate'])) ?$this->general_model->convertdate($PostData['fromdate']):'';
        $todate = (!empty($PostData['todate'])) ?$this->general_model->convertdate($PostData['todate']):'';
        $memberchannelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : "";
        $transactiontype = ($PostData['transactiontype']!="") ? trim($PostData['transactiontype']) : "";
        $pointtype = ($PostData['pointtype']!="") ? trim($PostData['pointtype']) : "";
        
        if(empty($userid) || empty($channelid) || $fromdate == "" || $todate == "" || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Reward_point_history_model', 'Reward_point_history');
                $reportdata = $this->Reward_point_history->getPointHistoryReportDataOnAPI($userid,$channelid,$fromdate,$todate,$memberchannelid,$memberid,$transactiontype,$pointtype,$counter);
                
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){
                        if($row['sellerchannelid'] != 0){
                            $sellername = $row['sellername'].' ('.$row['sellercode'].')';
                        }else{
                            $sellername = 'COMPANY';
                        }
                        if($row['buyerchannelid'] != 0){
                            $buyername = $row['buyername'].' ('.$row['buyercode'].')';
                        }else{
                            $buyername = 'COMPANY';
                        }
                        $creditpoints = $debitpoints = "0";
                        if ($row['type']==1) {
                            $debitpoints = $row['point'];
                        }else{
                            $creditpoints = $row['point'];
                        }
                        $this->data[] = array(
                            "sellername"=>$sellername,
                            "buyername"=>$buyername,
                            "creditpoint"=>$creditpoints,
                            "debitpoint"=>$debitpoints,
                            "status"=>$row['pointstatus'],
                            "closingpoint"=>$row['closingpoint'],
                            "transactiontype"=>(isset($this->Pointtransactiontype[$row['transactiontype']])?$this->Pointtransactiontype[$row['transactiontype']]:""),
                            "detail"=>$row['detail'],
                            "orderid"=>$row['orderid'],
                            "orderno"=>$row['ordernumber'],
                            "date"=>$this->general_model->displaydatetime($row['createddate'])
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getpointtransactiontype(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $level = isset($PostData['level']) ? trim($PostData['level']) : '';
        
        if(empty($userid) || empty($level)) {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid,"channelid"=>$level);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                if(!empty($this->Pointtransactiontype)) {
                    $this->data = array();
                    foreach($this->Pointtransactiontype as $key=>$type){

                        $this->data[] = array(
                            "id"=>(string)$key,
                            "name"=>$type
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getsalesanalysisreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        $countryid = !empty($PostData['countryid']) ? trim($PostData['countryid']) : '';
        $buyerid = !empty($PostData['buyerid']) ? trim($PostData['buyerid']) : "";
        $provinceid = !empty($PostData['stateid']) ? trim($PostData['stateid']) : '';
        $cityid = !empty($PostData['cityid']) ? trim($PostData['cityid']) : "";
        $year = !empty($PostData['year']) ? trim($PostData['year']) : "";
        $month = !empty($PostData['month']) ? trim($PostData['month']) : "";
        
        if(empty($userid) || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Sales_analysis_model', 'Sales_analysis');
                $reportdata = $this->Sales_analysis->getSalesAnalysisReportDataOnAPI($year,$month,$countryid,$provinceid,$cityid,$userid,$buyerid,$counter);
                
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){
                        $monthname = "";
                        foreach ($this->Monthwise as $monthid => $monthvalue) { 
                            if($monthid==$row['month']){
                              $monthname = $monthvalue;
                            }
                        }
                        $this->data[] = array(
                            "buyer"=>$row['buyername']." (".$row['buyercode'].")",
                            "totalsales"=>number_format($row['totalsales'],2,'.',''),
                            "year"=>$row['year'],
                            "month"=>$monthname,
                            "country"=>$row['countryname'],
                            "state"=>$row['provincename'],
                            "city"=>$row['cityname']
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }

    function getcashbackreport(){
        
        $PostData = json_decode($this->PostData['data'],true);
        
        $userid = isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $counter = isset($PostData['counter']) ? trim($PostData['counter']) : '';
        $memberchannelid = !empty($PostData['memberchannelid']) ? trim($PostData['memberchannelid']) : 0;
        $memberid = !empty($PostData['memberid']) ? trim($PostData['memberid']) : "";
        $status = isset($PostData['status']) ? trim($PostData['status']) : "";
        
        if(empty($userid) || $counter=='') {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
         
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$userid);
            $count = $this->Member->CountRecords();

            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{
                $this->load->model('Cashback_report_model', 'Cashback_report');
                $reportdata = $this->Cashback_report->getCashbackReportDataOnAPI($userid,$memberchannelid,$memberid,$status,$counter);
                
                if(!empty($reportdata)) {
                    $this->data = array();
                    foreach($reportdata as $row){
                       
                        $this->data[] = array(
                            "cashbackreportid"=>$row['id'],
                            "buyername"=>$row['membername']." (".$row['buyercode'].")",
                            "invoiceid"=>$row['invoiceid'],
                            "invoiceno"=>$row['invoiceno'],
                            "invoiceamount"=>number_format($row['netamount'],2,'.',''),
                            "cashbackamount"=>$row['cashbackamount'],
                            "status"=>$row['status']
                        );
                    }
                    ws_response('success','',$this->data);
                } else {
                    ws_response('fail', EMPTY_DATA);
                }
            }
        }
    }
    function changecashbackreportstatus(){

        $PostData = json_decode($this->PostData['data'],true);      
        $memberid =  isset($PostData['userid']) ? trim($PostData['userid']) : '';
        $channelid =  isset($PostData['level']) ? trim($PostData['level']) : '';
        $cashbackreportid =  isset($PostData['cashbackreportid']) ? trim($PostData['cashbackreportid']) : '';
        $status =  isset($PostData['status']) ? trim($PostData['status']) : '';
        
        if($status=='' || empty($cashbackreportid) || empty($memberid) || empty($channelid)) { 
            ws_response('fail', EMPTY_PARAMETER);
        }else{
            $this->load->model('Member_model', 'Member');  
            $this->Member->_where = array("id"=>$memberid,"channelid"=>$channelid);
            $count = $this->Member->CountRecords();
            
            if($count==0){
                ws_response('fail', USER_NOT_FOUND);
            }else{          
                $modifieddate = $this->general_model->getCurrentDateTime();
                
                $updateData = array(
                    'status'=>$status,
                    'modifieddate' => $modifieddate, 
                    'modifiedby'=>$memberid
                ); 
    
                $this->load->model("Cashback_report_model","Cashback_report");
                $this->Cashback_report->_where = array("id" => $cashbackreportid);
                $update = $this->Cashback_report->Edit($updateData);
              
                if($update) {
                    ws_response("Success", "Status changed successfully."); 
                }else{
                    ws_response("Fail", 'Status not change.'); 
                }
            }
        }   
    }
}