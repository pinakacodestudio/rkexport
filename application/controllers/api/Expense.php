<?php

class Expense extends MY_Controller
{

    function __construct(){
        parent::__construct();
        $this->load->model('Expense_model', 'Expense');
    }
    public $data=array();

    function getexpense() {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['employeeid']) && isset($PostData['modifieddate']) && isset($PostData['search']) && isset($PostData['counter']) && isset($PostData['status'])){
                    
                        if($PostData['employeeid'] == ""){
                            ws_response("Fail", "Fields value are missing.");
                        }else{
                            $statues = explode(",",$PostData['status']);
                            $statusstr = array();
                            if(count($statues)>0){
                                foreach($statues as $ss){
                                    if($ss=="pending"){
                                        $statusstr[] = 0;
                                    }elseif($ss=="approve"){
                                        $statusstr[] = 1;
                                    }elseif($ss=="reject"){
                                        $statusstr[] = 2;
                                    }
                                }
                            }
                        
                            $this->readdb->select("exc.id as ecid,ex.id as eid,e.id as employeeid,exc.name,date,reason,amount,remarks,receipt,ex.status,ex.createddate");
                            $this->readdb->from(tbl_expense." as ex");
                            $this->readdb->join(tbl_expensecategory." as exc","exc.id=ex.expensecategoryid");
                            $this->readdb->join(tbl_user." as e","e.id=ex.employeeid");

                            if($PostData['search']!=""){
                            $datearr = explode("/",$PostData['search']);
                            // arsort($arr);
                            $datestr = array();
                            if(count($datearr)>0){
                                foreach($datearr as $key=>$da){
                                    $datestr[] = $datearr[count($datearr)-($key+1)];
                                }
                            }
                            $datesearch = implode("/",$datestr);
                            $datesearch = str_replace("/","-",$datesearch);
                            if($PostData['modifieddate']!=""){
                                $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"ex.modifieddate > "=>$PostData['modifieddate'],"(exc.name like '%".$PostData['search']."%' or reason like '%".$PostData['search']."%' or amount like '%".$PostData['search']."%' or date like '%".$datesearch."%')"=>null));
                            }else{
                                $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"(exc.name like '%".$PostData['search']."%' or reason like '%".$PostData['search']."%' or amount like '%".$PostData['search']."%' or date like '%".$datesearch."%')"=>null));
                            }
                        }else{
                            if($PostData['modifieddate']!=""){
                                $this->readdb->where(array("employeeid"=>$PostData['employeeid'],"ex.modifieddate > "=>$PostData['modifieddate']));
                            }else{
                                $this->readdb->where(array("employeeid"=>$PostData['employeeid']));
                            }
                        }
                        if(count($statusstr)>0){
                            $this->readdb->where(array("ex.status in(".implode(",",$statusstr).")"=>null));
                        }

                        if(isset($PostData['fromdate']) && $PostData['fromdate']!="" && isset($PostData['todate']) && $PostData['todate']!=""){
                            $fromdate = $this->general_model->convertdate($PostData['fromdate']);
                            $todate = $this->general_model->convertdate($PostData['todate']);
                            $this->readdb->where("(DATE(date) BETWEEN '".$fromdate."' AND '".$todate."')");
                        }

                        // counter
                        if($PostData['counter']!=-1){
                            $this->readdb->limit(10,$PostData['counter']);
                        }
                        $this->readdb->order_by("ex.id desc");
                        $query = $this->readdb->get();
                            
                        $expense = $query->result_array();
                        // echo $this->db->last_query();exit;
                        
                        if(!empty($expense)){
                            foreach ($expense as $row) { 
                                $this->data[]= array("expenseid"=>$row['eid'],"expensetypeid"=>$row['ecid'],"expensecategory"=>$row['name'],"date"=>$row['date'],"reason"=>$row['reason'],"amount"=>$row['amount'],"remarks"=>$row['remarks'],"status"=>$row['status'],"receipt"=>$row['receipt'],"createddate"=>date("Y-m-d h:i:s a",strtotime($row['createddate'])),'employeeid'=>$row['employeeid']);
                            }
                        }
                        if(empty($this->data)){
                            ws_response("Fail", "Expense not available.");
                        }else{
                            ws_response("Success", "",$this->data);
                        }
                    
                    }
                       
                    }
                    else
                    {
                        ws_response("Fail", "Fields value are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getexpensecategory() 
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['modifieddate']))
                    {               
                        /* if($PostData['modifieddate']==''){
                             ws_response("Fail", "Fields value are missing.");
                        }
                        else{ */
                            if($PostData['modifieddate']!=''){
                                $query = $this->readdb->select("id,name as expensename,createddate,status")
                                         ->where("modifieddate > ",$PostData['modifieddate'])
                                         ->from(tbl_expensecategory)
                                         ->get();
                            }else{
                                $query = $this->readdb->select("id,name as expensename,createddate,status")
                                        ->from(tbl_expensecategory)
                                        ->get();
                            }
                            $expensecategory = $query->result_array();      
                                        
                           
                            if(!empty($expensecategory)){
                                foreach ($expensecategory as $row) {              
                                    $this->data[]= array("id"=>$row['id'],"expensename"=>$row['expensename'],"status"=>$row["status"],"createddate"=>date("Y-m-d h:i:s a",strtotime($row['createddate'])));
                                }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "No more data found.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        // }     
                    }
                    else
                    {
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function addeditexpense()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post(); 

            $createddate = $this->general_model->getCurrentDateTime();           
           
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);

                    if(isset($PostData['employeeid']) && isset($PostData['expensecatgoryid']) && isset($PostData['date']) && isset($PostData['amount']) && isset($PostData['reason']) && isset($PostData['status']) && isset($PostData['isreceipt'])) {

                        if($PostData['employeeid'] == "" || $PostData['expensecatgoryid'] == "" || $PostData['date'] == "" || $PostData['amount'] == "" || $PostData['reason'] == "" || $PostData['status'] == ""){
                            ws_response("Fail", "Fields value are missing.");
                        }

                        if(isset($PostData['id']) && !empty($PostData['id'])){

                            $query = $this->readdb->select("*")
                                     ->where(array("id" => $PostData['id']))
                                     ->from(tbl_expense)
                                     ->get();
                                        
                            $expensereceipt = $query->row_array(); 
                            
                            if(isset($_FILES["receipt"]['name']) && !empty($_FILES["receipt"]['name'])){

                                if($expensereceipt['receipt'] ==''){
                                    $receipt = uploadFile('receipt','EXPENSE_RECEIPT', EXPENSE_RECEIPT_PATH, '*', "", 1, EXPENSE_RECEIPT_LOCAL_PATH);
                                    if($receipt !== 0){	
                                        if($receipt==2){
                                            ws_response("Fail","Image not uploaded");
                                            exit;
                                        }
                                    }else{
                                        ws_response("Fail","Invalid image type");
                                        exit;
                                    }	
                                }else{
                                    $receipt = reuploadfile('receipt', 'EXPENSE_RECEIPT', $expensereceipt['receipt'], EXPENSE_RECEIPT_PATH, "*");
                                    if($receipt !== 0){	
                                        if($receipt==2){
                                            ws_response("Fail","Image not uploaded");
                                            exit;
                                        }
                                    }else{
                                        ws_response("Fail","Invalid image type");
                                        exit;
                                    }	
                                }

                            }else{
                              if($PostData['isreceipt']=="false"){
                                unlinkfile('EXPENSE_RECEIPT', $expensereceipt['receipt'],EXPENSE_RECEIPT_PATH);
                                $receipt = "";
                              }else{
                                $receipt = $expensereceipt['receipt']; 
                              }
                            }

                            if(isset($PostData['remarks']) && !empty($PostData['remarks'])){
                                $remarks = $PostData['remarks'];
                            } else {
                                $remarks = $expensereceipt['remarks'];
                            }

                            $updatedata = array(
                                'employeeid' => $PostData['employeeid'],
                                'expensecategoryid' => $PostData['expensecatgoryid'],
                                'date' => $PostData['date'],
                                'amount' => $PostData['amount'],
                                'reason' => $PostData['reason'],
                                'remarks' => $remarks,
                                'receipt' => $receipt,
                                'status' => $PostData['status'],
                                'modifieddate' => $createddate,
                                'modifiedby' => $PostData['employeeid'],
                            );
                           
                            $updatedata=array_map('trim',$updatedata);
                            $this->Expense->_where = array("id"=>$PostData['id']);
                            $Edit = $this->Expense->Edit($updatedata);
                            $this->data = array("id" => $PostData['id']);
                           
                            if($Edit){
                                ws_response("Success","Expense updated", $this->data);
                            } else {
                                ws_response("Success","Expense already updated",$this->data);
                            } 

                        } else {

                            if(isset($_FILES["receipt"]['name']) && !empty($_FILES["receipt"]['name'])){

                                $receipt = uploadFile('receipt','EXPENSE_RECEIPT', EXPENSE_RECEIPT_PATH, '*', "", 1, EXPENSE_RECEIPT_LOCAL_PATH);
                                if($receipt !== 0){	
                                    if($receipt==2){
                                        ws_response("Fail","Image not uploaded");
                                        exit;
                                    }
                                }else{
                                    ws_response("Fail","Invalid image type");
                                    exit;
                                }	
                            }else{
                              $receipt = "";
                            }

                            if(isset($PostData['remarks']) && !empty($PostData['remarks'])){
                                $remarks = $PostData['remarks'];
                            } else {
                                $remarks = "";
                            }

                             $insertdata = array(
                                'employeeid' => $PostData['employeeid'],
                                'expensecategoryid' => $PostData['expensecatgoryid'],
                                'date' => $PostData['date'],
                                'amount' => $PostData['amount'],
                                'reason' => $PostData['reason'],
                                'remarks' => $remarks,
                                'receipt' => $receipt,
                                'status' => $PostData['status'],
                                'createddate' => $createddate,
                                'modifieddate' => $createddate,
                                'addedby' => $PostData['employeeid'],
                                'modifiedbyby' => $PostData['employeeid'],
                            );
                           
                            $insertdata=array_map('trim',$insertdata);
                            $add = $this->Expense->add($insertdata);
                            $this->data = array("id" => $add);
                           
                            if($add){
                                /**/
                                $this->Expense->_table = (tbl_user);
                                $this->Expense->_fields="reportingto,name";
                                $this->Expense->_where = 'id='.$PostData['employeeid'];
                                $reportingtoemployee = $this->Expense->getRecordsByID();
                                   
                                if(count($reportingtoemployee)>0)
                                {             

                                    $fcmquery = $this->readdb->query("SELECT * FROM ".tbl_fcmdata." WHERE usertype=1 AND memberid=".$reportingtoemployee['reportingto']); 
                                    $this->load->model('Common_model','FCMData'); 
                                    $employeearr = $androidfcmid = $iosfcmid = array();
                                    if($fcmquery->num_rows() > 0) {
                                        $type = 16;
                                        $msg = "New Expense Added";
                                        $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'"}';
                                        $description = "New Expense Added by ".$reportingtoemployee['name'];
                                        $employeearr[] = $reportingtoemployee['reportingto'];
                                          
                                        foreach ($fcmquery->result_array() as $fcmrow) {
                                            if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==1){
                                                $androidfcmid[] = $fcmrow['fcm']; 	 
                                            }else if(trim($fcmrow['fcm'])!=='' && $fcmrow['devicetype']==2){
                                                $iosfcmid[] = $fcmrow['fcm'];
                                            }
                                        }   
                                        if(!empty($androidfcmid)){
                                            $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$androidfcmid ,0,$description,1);
                                        }
                                        if(!empty($iosfcmid)){							
                                            $this->FCMData->sendFcmNotification($type, $pushMessage,implode(",",$employeearr) ,$iosfcmid ,0,$description,2);		
                                        }     
                                        $notificationdata = array('memberid' => $reportingtoemployee['reportingto'],
                                                                'message' => $pushMessage,
                                                                'type' => $type,
                                                                'usertype' => 1,
                                                                'description'=> $description,
                                                                'createddate' => $createddate);

                                        $this->load->model('Notification_model','Notification');
                                        $this->Notification->add($notificationdata);
                                    }
                                }
                                /**/
                                ws_response("Success","Expense added", $this->data);
                            } 
                        } 
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    }
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

}
