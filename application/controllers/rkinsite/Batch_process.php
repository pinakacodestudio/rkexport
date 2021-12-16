<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_process extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Batch_process');
        $this->load->model('Process_group_model', 'Process_group');
        $this->load->model('Batch_process_model', 'Batch_process');
    }

    public function index() {
        $this->viewData['title'] = "Batch Process";
        $this->viewData['module'] = "batch_process/Batch_process";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['processdata'] = $this->Batch_process->getProcessOnProductProcess();
        $this->viewData['finalproductdata'] = $this->Batch_process->getFinalProductsOnProductProcess();
        $this->viewData['batchnodata'] = $this->Batch_process->getBatchNoOnProductProcess();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("batch_process", "pages/batch_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Batch_process->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                // $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'product-process/start-new-process-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Product&nbsp;Process","'.ADMIN_URL.'product-process/delete-mul-product-process") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            $actions.='<a class="'.reprocess_class.'" title="'.reprocess_title.'" href="'.ADMIN_URL.'product-process/reprocess/'.$datarow->id.'">'.reprocess_text.'</a>';
            if($datarow->processstatus == 2){
                $actions.='<a class="'.inprocess_class.'" title="'.inprocess_title.'" href="'.ADMIN_URL.'product-process/stock-in-process/'.$datarow->id.'">'.inprocess_text.'</a>';
            }else{
                $actions.='<a class="'.outprocess_class.'" title="'.outprocess_title.'" href="'.ADMIN_URL.'product-process/start-new-process">'.outprocess_text.'</a>';
            }
            if($datarow->processstatus == 0){
                
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Hold <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chageprocessstatus(1,'.$datarow->id.')">Running</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageprocessstatus(2,'.$datarow->id.')">Complete</a>
                              </li>
                          </ul>';
            }else if($datarow->processstatus == 1){
                
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Running <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chageprocessstatus(2,'.$datarow->id.')">Complete</a>
                            </li>
                          </ul>';
            }else if($datarow->processstatus == 2){
               
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';

            }
            $processstatus = '<div class="dropdown">'.$dropdownmenu.'</div>';
            
            $row[] = ++$counter;
            $row[] = ucwords($datarow->processgroup);
            $row[] = ucwords($datarow->processname);
            $row[] = $datarow->batchno;
            $row[] = $processstatus;
            $row[] = ucwords($datarow->addedby);
            // $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Batch_process->count_all(),
                        "recordsFiltered" => $this->Batch_process->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function start_new_process() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Start New Process | Stock Out Process";
        $this->viewData['module'] = "product_process/Add_product_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "OUT";

        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup();
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("start_new_process", "pages/add_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function stock_in_process($productprocessid) {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        
        if($productprocessid==""){
            redirect(ADMINFOLDER."dashboard");
        }
        $this->viewData['title'] = "Stock In Process";
        $this->viewData['module'] = "product_process/Add_product_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->viewData['productprocessdata'] = $this->Batch_process->getProductProcessDataById($productprocessid);

        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup();
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "IN";

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("start_new_process", "pages/add_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function reprocess($productprocessid) {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        
        if($productprocessid==""){
            redirect(ADMINFOLDER."dashboard");
        }
        $this->viewData['title'] = "Send for Reprocessing";
        $this->viewData['module'] = "product_process/Add_product_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->viewData['productprocessdata'] = $this->Batch_process->getProductProcessDataById($productprocessid);

        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup();
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "REPROCESS";

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("start_new_process", "pages/add_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function product_process_add() {
        
        $PostData = $this->input->post();

        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $json = array();
        
        $processtype = $PostData['processtype'];
        $processgroupmappingid = $PostData['processgroupmappingid'];
        $batchno = $PostData['batchno'];
        // $processid = trim($PostData['processid']);
        $transactiondate = $this->general_model->convertdate($PostData['transactiondate']);
        $comments = $PostData['remarks'];
        $isreprocess = $parentproductprocessid = 0;
        
        if($processtype == "IN"){
            $processbymemberid = $PostData['processbymemberid'];
            $type = 1;

            if(!is_dir(PRODUCT_PROCESS_CERTIFICATE_PATH)){
                @mkdir(PRODUCT_PROCESS_CERTIFICATE_PATH);
            }
    
            if(!empty($_FILES)){
                        
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if(strpos($key, 'docfile') !== false && $_FILES['docfile'.$id]['name']!=''){
                        if($_FILES['docfile'.$id]['size'] != '' && $_FILES['docfile'.$id]['size'] >= UPLOAD_MAX_FILE_SIZE){
                            $json = array('error'=>-1,"id"=>$id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('docfile'.$id, 'PRODUCT_PROCESS_CERTIFICATE', PRODUCT_PROCESS_CERTIFICATE_PATH, '*', '', 1, PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH,'','',0);
                        if($file !== 0){
                            if($file == 2){
                                $json = array('error'=>-2,'message'=>$id." File not upload !","id"=>$id);
                                echo json_encode($json);
                                exit;
                            }
                        }else{
                            $json = array('error'=>-2,'message'=>$id." File type does not valid !","id"=>$id);
                            echo json_encode($json);
                            exit;
                        }           
                    }
                }
            }   
        }else{
            if($processtype == "REPROCESS"){
                $processbymemberid = $PostData['processbymemberid'];
            }else{
                $processbymemberid = ($PostData['processedby']==0?$PostData['vendorid']:0);
            }
            $type = 0;
       
            /* $this->Batch_process->_table = tbl_productprocess;
            $this->Batch_process->_where = array('batchno' => $batchno);
            $Count = $this->Batch_process->CountRecords();
            if($Count>0){
                $json = array('error'=>2); // Process group already added.
                json_encode($json);exit;
            } */
        }
        if($processtype == "REPROCESS"){
            $isreprocess = 1;
        }
        $InsertData = array('parentproductprocessid' => 0,
                            'processgroupmappingid' => $processgroupmappingid,
                            'batchno' => $batchno,
                            'transactiondate' => $transactiondate,
                            'processbymemberid' => $processbymemberid,
                            'comments' => $comments,
                            'isreprocess' => $isreprocess,
                            'type' => $type,
                            'processstatus' => 0,
                            'createddate' => $createddate,
                            'addedby' => $addedby,                              
                            'modifieddate' => $createddate,                             
                            'modifiedby' => $addedby 
                        );
    
        $ProductProcessID = $this->Batch_process->Add($InsertData);
        
        if($ProductProcessID){

            $insertprocessdetaildata = $insertprocesscertificates = $insertOptionValueData = array();
            $productidarray = ($processtype == "IN"?$PostData['inproductid']:$PostData['outproductid']);
            $productvariantid = ($processtype == "IN"?$PostData['inproductvariantid']:$PostData['outproductvariantid']);
            $quantity = ($processtype == "IN"?$PostData['inquantity']:$PostData['quantity']);
            
            if(!empty($productidarray)){
                foreach($productidarray as $k=>$productid){
                    
                    if($processtype == "IN"){
                        $issupportingproduct = 0;
                        $isfinalproduct = (isset($PostData['finalproduct'.($k+1)])?1:0);
                        $unitid = 0;
                    }else{
                        $issupportingproduct = (isset($PostData['outproductadditional'.($k+1)])?1:0);
                        $isfinalproduct = 0;
                        $unitid = $PostData['unitid'][$k];
                    }
                    $insertprocessdetaildata[] = array('productprocessid' => $ProductProcessID,
                            'productpriceid' => $productvariantid[$k],
                            'unitid' => $unitid,
                            'quantity' => $quantity[$k],
                            'issupportingproduct' => $issupportingproduct,                        
                            'isfinalproduct' => $isfinalproduct
                    );
                }
            }
            if(count($insertprocessdetaildata) > 0){
                $this->Batch_process->_table = tbl_productprocessdetails;
                $this->Batch_process->add_batch($insertprocessdetaildata);
            }
            if($processtype == "IN"){
                $isCertificate = $PostData['isCertificate'];
                if($isCertificate!=0){
                    
                    $doctitle = $PostData['doctitle'];
                    $docno = $PostData['docno'];
                    $docdescription = $PostData['docdescription'];
                    $docdate = $PostData['docdate'];
                    
                    if(!empty($_FILES)){
                        
                        foreach ($_FILES as $key => $value) {
                            $id = preg_replace('/[^0-9]/', '', $key);
                            $rowid = $id - 1;

                            if(strpos($key, 'docfile') !== false && $_FILES['docfile'.$id]['name']!=''){
                                $file = uploadFile('docfile'.$id, 'PRODUCT_PROCESS_CERTIFICATE', PRODUCT_PROCESS_CERTIFICATE_PATH, '*', '', 1, PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH);

                                if($file !== 0 && $file !== 2){
                                    
                                    $insertprocesscertificates[] = array('productprocessid' => $ProductProcessID,
                                            'docno' => $docno[$rowid],
                                            'documentdate' => ($docdate[$rowid]!=""?$this->general_model->convertdate($docdate[$rowid]):""),
                                            'title' => $doctitle[$rowid],
                                            'remarks' => $docdescription[$rowid],                        
                                            'filename' => $file
                                    );
                                } 
                            }
                        }
                    }
                    if(count($insertprocesscertificates) > 0){
                        $this->Batch_process->_table = tbl_productprocesscertificates;
                        $this->Batch_process->add_batch($insertprocesscertificates);
                    }
                }
                $optionidarray = $PostData['optionid'];
                $optionvalue = $PostData['optionvalue'];
                
                if(!empty($optionidarray)){
                    foreach($optionidarray as $i=>$processoptionid){
                        

                        $insertOptionData = array('productprocessid'=>$ProductProcessID,
                                                'processoptionid'=>$processoptionid
                                                );

                        $this->Batch_process->_table = tbl_productprocessoption;
                        $productprocessoptionid = $this->Batch_process->Add($insertOptionData);
                        if($productprocessoptionid){

                            $insertOptionValueData[] = array('productprocessoptionid'=>$productprocessoptionid,
                                                            'value'=>$optionvalue[$i]
                                                        );

                        }
                        
                    }
                }
                if(!empty($insertOptionValueData)){
                    $this->Batch_process->_table = tbl_productprocessoptionvalue;
                    $this->Batch_process->add_batch($insertOptionValueData);
                }
            }
            $json = array('error'=>1); // Process group inserted successfully.
        } else {
            $json = array('error'=>0); // Process group not inserted.
        }
        
        echo json_encode($json);
    }
    public function update_process_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $id = $PostData['id'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
       
        $updateData = array(
            'processstatus'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        $this->Batch_process->_where = array("id" => $id);
        $IsUpdate = $this->Batch_process->Edit($updateData);
        if($IsUpdate) {
           echo 1;    
        }else{
            echo 0;
        }
    }
    public function delete_mul_product_process() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id

            $this->Batch_process->_table = tbl_productprocess;
            $this->Batch_process->_fields = "id,type";
            $this->Batch_process->_where = array('id'=>$row);
            $productprocessdata = $this->Batch_process->getRecordsById();

            if(!empty($productprocessdata)){

                if($productprocessdata['type'] == 1){
                    
                    $this->Batch_process->_table = tbl_productprocessoptionvalue;
                    $this->Batch_process->Delete(array('productprocessoptionid IN (SELECT id FROM '.tbl_productprocessoption.' WHERE productprocessid = "'.$row.'")'=>null));
                    
                    $this->Batch_process->_table = tbl_productprocessoption;
                    $this->Batch_process->Delete(array('productprocessid'=>$row));
        
                    $this->Batch_process->_table = tbl_productprocesscertificates;
                    $this->Batch_process->_fields = "id,filename";
                    $this->Batch_process->_where = array('productprocessid'=>$row);
                    $certificatesdata = $this->Batch_process->getRecordById();
        
                    if(!empty($certificatesdata)){
                        foreach($certificatesdata as $certificate){
                            unlinkfile("PRODUCT_PROCESS_CERTIFICATE",$certificate['filename'],PRODUCT_PROCESS_CERTIFICATE_PATH);
                        }
                    }
                    $this->Batch_process->Delete(array('productprocessid'=>$row));
                }
    
                $this->Batch_process->_table = tbl_productprocessdetails;
                $this->Batch_process->Delete(array('productprocessid'=>$row));
    
                $this->Batch_process->_table = tbl_productprocess;
                $this->Batch_process->Delete(array('id'=>$row));
            }
        }
    }
}?>