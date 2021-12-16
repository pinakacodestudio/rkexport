<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Process_group extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Process_group');
        $this->load->model('Process_group_model', 'Process_group');
        $this->load->model('Process_model', 'Process');
    }

    public function index() {
        $this->viewData['title'] = "Process Group";
        $this->viewData['module'] = "process_group/Process_group";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['productcategorydata'] = $this->Process_group->getProductCategoryOnProcessGroup();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Process Group','View process group.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("process_group", "pages/process_group.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $add = explode(',', $this->viewData['submenuvisibility']['submenuadd']);
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Process_group->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'process-group/process-group-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'process-group/process-group-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'process-group/process-group-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Process&nbsp;Group","'.ADMIN_URL.'process-group/delete-mul-process-group") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            $actions.='<a class="'.startprocess_class.'" href="'.ADMIN_URL.'product-process/start-new-process/'.$datarow->id.'" title="'.startprocess_title.'">'.startprocess_text.'</a>';
            if(in_array($rollid, $add)) {
                $actions .= '<a class="'.duplicatebtn_class.'" href="'.ADMIN_URL.'process-group/add-process-group/'. $datarow->id.'" title="'.duplicatebtn_title.'">'.duplicatebtn_text.'</a>';
            }

            $row[] = ++$counter;
            $row[] = ucwords($datarow->name);
            $row[] = $datarow->noofprocesses;
            $row[] = $datarow->noofbatches;
            //$row[] = ($datarow->description!="")?ucfirst($datarow->description):"-";
            $row[] = ucwords($datarow->addedby);
            $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Process_group->count_all(),
                        "recordsFiltered" => $this->Process_group->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_process_group($processgroupid="") {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Process Group";
        $this->viewData['module'] = "process_group/Add_process_group";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->viewData['processdata'] = $this->Process->getActiveProcess();

        $this->load->model('Product_model', 'Product');
        //$this->viewData['rawproductdata'] = $this->Product->getActiveRegularOrRawProducts(1);
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2,0,0,"withvariant","admin_variant");
        
        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->viewData['processoptiondata'] = $this->Process_group->getActiveProcessOption();  
        
        if($processgroupid!=""){
            /***** ADD DUPLICATE PROCESS GROUP ******/
            $this->viewData['processgroupdata'] = $this->Process_group->getProcessGroupDataById($processgroupid);
            $this->viewData['isduplicate'] = "1";
        }
        // echo "<pre>"; print_r($this->viewData['processoptiondata']); exit;
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("add_process_group", "pages/add_process_group.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function process_group_add() {
        
        $PostData = $this->input->post();

        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $json = array();
        
        $groupname = trim($PostData['groupname']);
        $description = trim($PostData['description']);
        $status = $PostData['status'];
        
        $this->Process_group->_where = array('name' => $groupname);
        $Count = $this->Process_group->CountRecords();
        if($Count==0){
            
            $InsertData = array('name' => $groupname,
                                'description' => $description,
                                'status' => $status,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
        
            $ProcessGroupID = $this->Process_group->Add($InsertData);
            if($ProcessGroupID){

                $insertmappingdata = array();
                $generatedsequencenoarray = $PostData['generatedsequenceno'];
                
                $processid = $PostData['postprocessid'];
                $priority = $PostData['priority'];

                if(!empty($generatedsequencenoarray)){
                    foreach($generatedsequencenoarray as $k=>$seqno){
                        
                        $ssno = $PostData['sortablesequenceno'][$k];
                        $isoptional = (isset($PostData['processisoptional'.$seqno])?1:0);
                        $qcrequire = (isset($PostData['processqcrequire'.$seqno])?1:0);
                        $processedby = $PostData['processedby'.$seqno];
                        $vendorid = ($processedby==0 && !empty($PostData['vendorid'][$seqno]))?implode(",",$PostData['vendorid'][$seqno]):0;
                        $machineid = ($processedby==1 && !empty($PostData['machineid'][$seqno]))?implode(",",$PostData['machineid'][$seqno]):0;

                        $insertmappingdata[] = array('processgroupid' => $ProcessGroupID,
                                'processid' => $processid[$k],
                                'priority' => $priority[$k],
                                'sequenceno' => $ssno,
                                'isoptional' => $isoptional,                        
                                'qcrequire' => $qcrequire,                        
                                'processedby' => $processedby,
                                'vendorid' => $vendorid,
                                'machineid' => $machineid,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                        );
                    }
                }
                
                if(count($insertmappingdata) > 0){
                    $this->Process_group->_table = tbl_processgroupmapping;
                    $this->Process_group->add_batch($insertmappingdata);

                    $proccessgroupmappingidsarr = array();
                    $first_id = $this->writedb->insert_id();
                    $last_id = $first_id + (count($insertmappingdata)-1);
                    
                    for($id=$first_id;$id<=$last_id;$id++){
                        $proccessgroupmappingidsarr[]=$id;
                    }
                    $insertProductData = $insertOptionValueData = array();
                    foreach($proccessgroupmappingidsarr as $k=>$proccessgroupmappingid){

                        $gsno = $generatedsequencenoarray[$k];
                        $outproductidarray = $PostData['outproductid'.$gsno];
                        $outproductvariantid = $PostData['outproductvariantid'.$gsno];
                        $unitid = $PostData['unitid'.$gsno];
                        
                        if(!empty($outproductidarray)){
                            foreach($outproductidarray as $i=>$outproductid){

                                $isoptional = (isset($PostData['outproductisoptional'.$gsno.'_'.($i+1)])?1:0);
                                $issupportingproduct = (isset($PostData['outproductadditional'.$gsno.'_'.($i+1)])?1:0);

                                if(!empty($outproductid)){

                                    $insertProductData[] = array('processgroupmappingid' => $proccessgroupmappingid,
                                            'productpriceid' => $outproductvariantid[$i],
                                            'type' => 0,
                                            'unitid' => $unitid[$i],
                                            'isoptional' => $isoptional,                        
                                            /* 'issupportingproduct' => $issupportingproduct */
                                    );

                                }
                            }
                        }

                        $inproductidarray = $PostData['inproductid'.$gsno];
                        $inproductvariantid = $PostData['inproductvariantid'.$gsno];

                        if(!empty($inproductidarray)){
                            foreach($inproductidarray as $i=>$inproductid){
                                
                                if(!empty($inproductid)){
                                    $insertProductData[] = array('processgroupmappingid' => $proccessgroupmappingid,
                                            'productpriceid' => $inproductvariantid[$i],
                                            'type' => 1,
                                            'unitid' => 0,
                                            'isoptional' => 0,                        
                                            /* 'issupportingproduct' => 0 */
                                    );
                                }
                            }
                        }

                        $optionidarray = $PostData['optionid'.$gsno];
                        $optionvalue = $PostData['optionvalue'.$gsno];
                        
                        if(!empty($optionidarray)){
                            foreach($optionidarray as $i=>$processoptionid){
                                
                                $insertOptionData = array('processgroupmappingid'=>$proccessgroupmappingid,
                                                            'processoptionid'=>$processoptionid
                                                        );

                                $this->Process_group->_table = tbl_processgroupoption;
                                $processgroupoptionid = $this->Process_group->Add($insertOptionData);
                                if($processgroupoptionid){

                                    $insertOptionValueData[] = array('processgroupoptionid'=>$processgroupoptionid,
                                                                    'value'=>$optionvalue[$i]
                                                                );

                                }
                                
                            }
                        }
                    }
                    if(!empty($insertProductData)){
                        $this->Process_group->_table = tbl_processgroupproducts;
                        $this->Process_group->add_batch($insertProductData);
                    }
                    if(!empty($insertOptionValueData)){
                        $this->Process_group->_table = tbl_processgroupoptionvalue;
                        $this->Process_group->add_batch($insertOptionValueData);
                    }
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Process Group','Add new '.$groupname.' process group.');
                }
                $json = array('error'=>1); // Process group inserted successfully.
            } else {
                $json = array('error'=>0); // Process group not inserted.
            }
        } else {
            $json = array('error'=>2); // Process group already added.
        }
        echo json_encode($json);
    }
    public function process_group_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Process Group";
        $this->viewData['module'] = "process_group/Add_process_group";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
        // $this->viewData['processgroupdata'] = $this->Process_group->getProcessDataByID($id);
        
        $this->viewData['processdata'] = $this->Process->getActiveProcess();
        
        $this->load->model('Product_model', 'Product');
        //$this->viewData['rawproductdata'] = $this->Product->getActiveRegularOrRawProducts(1);
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2,0,0,"withvariant","admin_variant");

        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->viewData['processdata'] = $this->Process->getActiveProcess();
        $this->viewData['processoptiondata'] = $this->Process_group->getActiveProcessOption();        
        $this->viewData['processgroupdata'] = $this->Process_group->getProcessGroupDataById($id);
        // echo "<pre>"; print_r($this->viewData['processgroupdata']); exit;
        if(empty($this->viewData['processgroupdata'])){
            redirect("pagenotfound");
        }
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("add_process_group","pages/add_process_group.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_process_group() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        // print_r($PostData); exit;

        $processgroupid = $PostData['processgroupid'];
        $groupname = trim($PostData['groupname']);
        $description = trim($PostData['description']);
        $status = $PostData['status'];
        
        $this->Process_group->_where = array("id!="=>$processgroupid,'name' => $groupname);
        $Count = $this->Process_group->CountRecords();
        if($Count==0){
                
            $updateData = array('name' => $groupname,
                                'description' => $description,
                                'status' => $status,
                                'modifieddate' => $modifieddate,                             
                                'modifiedby' => $modifiedby);

            $this->Process_group->_where = array('id' =>$processgroupid);
            $isUpdated = $this->Process_group->Edit($updateData);
            
            if($isUpdated){
               
                if(isset($PostData['removeprocessgroupmappingid']) && $PostData['removeprocessgroupmappingid']!=''){
                    $query=$this->readdb->select("id")
                                    ->from(tbl_processgroupmapping)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeprocessgroupmappingid'])))."')>0")
                                    ->get();
                    $ProcessFroupMappingData = $query->result_array();
                    
                    if(!empty($ProcessFroupMappingData)){
                        foreach ($ProcessFroupMappingData as $row) {
                            
                            $this->Process_group->_table = tbl_processgroupoptionvalue;
                            $this->Process_group->Delete(array('processgroupoptionid IN (SELECT id FROM '.tbl_processgroupoption.' WHERE processgroupmappingid = "'.$row['id'].'")'=>null));
                            
                            $this->Process_group->_table = tbl_processgroupoption;
                            $this->Process_group->Delete(array('processgroupmappingid'=>$row['id']));

                            $this->Process_group->_table = tbl_processgroupproducts;
                            $this->Process_group->Delete(array('processgroupmappingid'=>$row['id']));
                            
                            $this->Process_group->_table = tbl_processgroupmapping;
                            $this->Process_group->Delete("id=".$row['id']);

                        }
                    }
                }
                if(isset($PostData['removeprocessgroupproductid']) && $PostData['removeprocessgroupproductid']!=''){
                    $query=$this->readdb->select("id")
                                    ->from(tbl_processgroupproducts)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeprocessgroupproductid'])))."')>0")
                                    ->get();
                    $ProcessGroupProductData = $query->result_array();
                    
                    if(!empty($ProcessGroupProductData)){
                        foreach ($ProcessGroupProductData as $row) {
                            
                            $this->Process_group->_table = tbl_processgroupproducts;
                            $this->Process_group->Delete(array('id'=>$row['id']));
                            
                        }
                    }
                } 
                $insertmappingdata = $updatemappingdata = $insertgsnoarray = array();
                $generatedsequencenoarray = $PostData['generatedsequenceno'];
                
                $processid = $PostData['postprocessid'];
                $priority = $PostData['priority'];
                $processgroupmappingidarray = isset($PostData['processgroupmappingid'])?$PostData['processgroupmappingid']:'';

                if(!empty($generatedsequencenoarray)){
                    foreach($generatedsequencenoarray as $k=>$seqno){
                        
                        $ssno = $PostData['sortablesequenceno'][$k];
                        $isoptional = (isset($PostData['processisoptional'.$seqno])?1:0);
                        $qcrequire = (isset($PostData['processqcrequire'.$seqno])?1:0);
                        $processgroupmappingid = (isset($processgroupmappingidarray[$k]) && !empty($processgroupmappingidarray[$k]))?$processgroupmappingidarray[$k]:"";
                        $processedby = $PostData['processedby'.$seqno];
                        $vendorid = ($processedby==0 && !empty($PostData['vendorid'][$seqno]))?implode(",",$PostData['vendorid'][$seqno]):0;
                        $machineid = ($processedby==1 && !empty($PostData['machineid'][$seqno]))?implode(",",$PostData['machineid'][$seqno]):0;
                        
                        if($processgroupmappingid != ""){

                            $updatemappingdata[] = array("id"=>$processgroupmappingid,
                                    'priority' => $priority[$k],
                                    'sequenceno' => $ssno,
                                    'isoptional' => $isoptional, 
                                    'qcrequire' => $qcrequire, 
                                    'processedby' => $processedby,
                                    'vendorid' => $vendorid,
                                    'machineid' => $machineid,
                                    'modifieddate' => $modifieddate,                             
                                    'modifiedby' => $modifiedby
                            );

                        }else{

                            $insertmappingdata[] = array('processgroupid' => $processgroupid,
                                    'processid' => $processid[$k],
                                    'priority' => $priority[$k],
                                    'sequenceno' => $ssno,
                                    'isoptional' => $isoptional,     
                                    'qcrequire' => $qcrequire,     
                                    'processedby' => $processedby,
                                    'vendorid' => $vendorid,
                                    'machineid' => $machineid,                   
                                    'createddate' => $modifieddate,
                                    'addedby' => $modifiedby,                              
                                    'modifieddate' => $modifieddate,                             
                                    'modifiedby' => $modifiedby 
                            );

                            $insertgsnoarray[] = $seqno;
                        }
                    }
                    // print_r($updatemappingdata); exit;
                    if(count($insertmappingdata) > 0){
                        $this->Process_group->_table = tbl_processgroupmapping;
                        $this->Process_group->add_batch($insertmappingdata);
    
                        $proccessgroupmappingidsarr = array();
                        $first_id = $this->writedb->insert_id();
                        $last_id = $first_id + (count($insertmappingdata)-1);
                        
                        for($id=$first_id;$id<=$last_id;$id++){
                            $proccessgroupmappingidsarr[]=$id;
                        }
                    }
                    $insertProductData = $insertOptionValueData = array();
                    foreach($generatedsequencenoarray as $k=>$seqno){
    
                        $outproductidarray = $PostData['outproductid'.$seqno];
                        $outproductvariantid = $PostData['outproductvariantid'.$seqno];
                        $unitid = $PostData['unitid'.$seqno];
                        $processgroupmappingid = (isset($processgroupmappingidarray[$k]) && !empty($processgroupmappingidarray[$k]))?$processgroupmappingidarray[$k]:"";

                        $processgroupoutproductidarray = isset($PostData['processgroupoutproductid'.$seqno])?$PostData['processgroupoutproductid'.$seqno]:'';
                        
                        if(in_array($seqno, $insertgsnoarray) && $processgroupmappingid == ""){
                            $processgroupmappingid = $proccessgroupmappingidsarr[array_search($seqno, $insertgsnoarray)];
                        }

                        if(!empty($outproductidarray)){
                            foreach($outproductidarray as $i=>$outproductid){
                                
                                $processgroupoutproductid = (isset($processgroupoutproductidarray[$i]) && !empty($processgroupoutproductidarray[$i]))?$processgroupoutproductidarray[$i]:"";

                                $isoptional = (isset($PostData['outproductisoptional'.$seqno.'_'.($i+1)])?1:0);
                                $issupportingproduct = (isset($PostData['outproductadditional'.$seqno.'_'.($i+1)])?1:0);

                                if(!empty($outproductid)){
                                    if($processgroupoutproductid != ""){

                                        $updateProductData[] = array('id' => $processgroupoutproductid,
                                                'productpriceid' => $outproductvariantid[$i],
                                                'unitid' => $unitid[$i],
                                                'isoptional' => $isoptional,                        
                                                'issupportingproduct' => $issupportingproduct
                                        );
                                    }else{
                                        $insertProductData[] = array('processgroupmappingid' => $processgroupmappingid,
                                                'productpriceid' => $outproductvariantid[$i],
                                                'type' => 0,
                                                'unitid' => $unitid[$i],
                                                'isoptional' => $isoptional,                        
                                                'issupportingproduct' => $issupportingproduct
                                        );
                                    }
                                }
                            }
                        }

                        $inproductidarray = $PostData['inproductid'.$seqno];
                        $inproductvariantid = $PostData['inproductvariantid'.$seqno];
                        
                        $processgroupinproductidarray = isset($PostData['processgroupinproductid'.$seqno])?$PostData['processgroupinproductid'.$seqno]:'';

                        if(!empty($inproductidarray)){
                            foreach($inproductidarray as $i=>$inproductid){
                                
                                $processgroupinproductid = (isset($processgroupinproductidarray[$i]) && !empty($processgroupinproductidarray[$i]))?$processgroupinproductidarray[$i]:"";

                                if(!empty($inproductid)){
                                    if($processgroupinproductid != ""){

                                        $updateProductData[] = array('id' => $processgroupinproductid,
                                                'productpriceid' => $inproductvariantid[$i],
                                        );
                                    }else{

                                        $insertProductData[] = array('processgroupmappingid' => $processgroupmappingid,
                                                'productpriceid' => $inproductvariantid[$i],
                                                'type' => 1,
                                                'unitid' => 0,
                                                'isoptional' => 0,                        
                                                'issupportingproduct' => 0
                                        );
                                    }
                                }
                            }
                        }

                        $optionidarray = $PostData['optionid'.$seqno];
                        $optionvalue = $PostData['optionvalue'.$seqno];
                        $processgroupoptionidarray = isset($PostData['processgroupoptionid'.$seqno])?$PostData['processgroupoptionid'.$seqno]:'';

                        if(!empty($optionidarray)){
                            foreach($optionidarray as $i=>$processoptionid){
                                

                                $processgroupoptionid = (isset($processgroupoptionidarray[$i]) && !empty($processgroupoptionidarray[$i]))?$processgroupoptionidarray[$i]:"";

                                if($processgroupoptionid != ""){

                                    $updateOptionValueData[] = array('processgroupoptionid'=>$processgroupoptionid,
                                                                    'value'=>$optionvalue[$i]
                                );
                                }else{

                                    $insertOptionData = array('processgroupmappingid'=>$processgroupmappingid,
                                                                'processoptionid'=>$processoptionid
                                                            );
                                    $this->Process_group->_table = tbl_processgroupoption;
                                    $processgroupoptionid = $this->Process_group->Add($insertOptionData);
                                    if($processgroupoptionid){

                                        $insertOptionValueData[] = array('processgroupoptionid'=>$processgroupoptionid,
                                                                        'value'=>$optionvalue[$i]
                                                                    );

                                    }
                                }

                                
                            }
                        }
                       
                    }
                    if(!empty($insertProductData)){
                        $this->Process_group->_table = tbl_processgroupproducts;
                        $this->Process_group->add_batch($insertProductData);
                    }
                    if(!empty($updateProductData)){
                        $this->Process_group->_table = tbl_processgroupproducts;
                        $this->Process_group->edit_batch($updateProductData, "id");
                    }
                    if(!empty($insertOptionValueData)){
                        $this->Process_group->_table = tbl_processgroupoptionvalue;
                        $this->Process_group->add_batch($insertOptionValueData);
                    }
                    if(!empty($updateOptionValueData)){
                        $this->Process_group->_table = tbl_processgroupoptionvalue;
                        $this->Process_group->edit_batch($updateOptionValueData,"processgroupoptionid");
                    }
                    if(!empty($updatemappingdata)){
                        $this->Process_group->_table = tbl_processgroupmapping;
                        $this->Process_group->edit_batch($updatemappingdata,"id");
                    }
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Process Group','Edit '.$groupname.' process group.');
                }
                $json = array('error'=>1); // Process group updated.
            } else {
                $json = array('error'=>0); // Process group not updated.
            }
        } else {
            $json = array('error'=>2); // Process group already exist.
        }
        echo json_encode($json);
    }

    public function process_group_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Process_group->_where = array("id" => $PostData['id']);
        $this->Process_group->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Process_group->_where = array("id"=>$PostData['id']);
            $data = $this->Process_group->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' process group.';
            
            $this->general_model->addActionLog(2,'Process Group', $msg);
        }
        echo $PostData['id'];
    }

    public function delete_mul_process_group() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            
            $this->Process_group->_table = tbl_processgroupoptionvalue;
            $this->Process_group->Delete(array('processgroupoptionid IN (SELECT id FROM '.tbl_processgroupoption.' WHERE processgroupmappingid IN (SELECT id FROM '.tbl_processgroupmapping.' WHERE processgroupid = "'.$row.'"))'=>null));
            
            $this->Process_group->_table = tbl_processgroupoption;
            $this->Process_group->Delete(array('processgroupmappingid IN (SELECT id FROM '.tbl_processgroupmapping.' WHERE processgroupid = "'.$row.'")'=>null));

            $this->Process_group->_table = tbl_processgroupproducts;
            $this->Process_group->Delete(array('processgroupmappingid IN (SELECT id FROM '.tbl_processgroupmapping.' WHERE processgroupid = "'.$row.'")'=>null));
            
            $this->Process_group->_table = tbl_processgroupmapping;
            $this->Process_group->Delete(array('processgroupid'=>$row));

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Process_group->_table = tbl_processgroup;
                $this->Process_group->_fields = "name";
                $this->Process_group->_where = array("id"=>$row);
                $data = $this->Process_group->getRecordsByID();
                
                $this->general_model->addActionLog(3,'Process Group','Delete '.$data['name'].' process group.');
            }

            $this->Process_group->_table = tbl_processgroup;
            $this->Process_group->Delete(array('id'=>$row));
        }
    }
    public function getProductVariantByProductId(){
        $PostData = $this->input->post();
        $type = $PostData['type'];
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getProductVariantByINOUTProductId($PostData['productid'],$type,0);
        echo json_encode($productdata);
    }
    public function getProductByCategoryId(){
        $PostData = $this->input->post();
        
        $productdata = $this->Process_group->getProductByCategoryIdOnProcessGroup($PostData['categoryid']);
        echo json_encode($productdata);
    }
    public function getOutProductByProcessGroupIdOrProcessId(){
        $PostData = $this->input->post();
        $processgroupid = $PostData['processgroupid'];
        $processid = $PostData['processid'];
        $productprocessid = $PostData['productprocessid'];
        $parentproductprocessid = $PostData['parentproductprocessid'];
        $productionplanid = $PostData['productionplanid'];
        $productplandata = isset($PostData['productionplanqtydetail'])?json_decode($PostData['productionplanqtydetail'],true):"";
        
        $productdata = $this->Process_group->getOutProductByProcessGroupIdOrProcessId($processgroupid, $processid, $productprocessid, $parentproductprocessid);
        if($productprocessid == "" && $productionplanid != "" && !empty($productdata) && !empty($productplandata)){
            
            $this->load->model("Production_plan_model","Production_plan");
            // $productplandata = $this->Production_plan->getProductionPlanDataByID($productionplanid);
            if(!empty($productplandata)){
                $productidarray = array_column($productplandata, 'productid');
                $priceidarray = array_column($productplandata, 'priceid');
                $quantityarray = array_column($productplandata, "quantity");

                $materialdata = $this->Production_plan->getProductionPlanRawMaterials($productidarray,$priceidarray,$quantityarray);

                if(!empty($materialdata)){
                    foreach($productdata as $i=>$product){
                        $key = array_search($product['productpriceid'], array_column($materialdata, 'priceid'));
                        
                        if(trim($key)!="" && isset($materialdata[$key])){
                            $productdata[$i]['quantity'] = $materialdata[$key]['requiredstock']; 
                        }
                    }
                }
            }
        }
        
        echo json_encode($productdata);
    }
    public function getProductByProcessGroupIdOrProcessId(){
        $PostData = $this->input->post();
        $processgroupid = $PostData['processgroupid'];
        $processid = $PostData['processid'];
        $type = (isset($PostData['type']) && $PostData['type']=="in")?1:0;
        $referencetype = (isset($PostData['referencetype']))?$PostData['referencetype']:"";
        $referenceid = (isset($PostData['referenceid']))?$PostData['referenceid']:"";

        $productdata = $this->Process_group->getProductByProcessGroupIdOrProcessId($processgroupid, $processid, $type,$referencetype,$referenceid);
        echo json_encode($productdata);
    }
    public function getProductVariantByProcessGroupIdOrProcessIdOrProductId(){
        $PostData = $this->input->post();
        $processgroupid = $PostData['processgroupid'];
        $processid = $PostData['processid'];
        $productid = $PostData['productid'];
        $type = (isset($PostData['type']) && $PostData['type']=="in")?1:0;
        
        $productvariantdata = $this->Process_group->getProductVariantByProcessGroupIdOrProcessIdOrProductId($processgroupid, $processid, $productid, $type);
        echo json_encode($productvariantdata);
    }
    public function getProductUnitByProcessGroupIdOrProcessId(){
        $PostData = $this->input->post();
        $processgroupid = $PostData['processgroupid'];
        $processid = $PostData['processid'];
        
        $productvariantdata = $this->Process_group->getProductUnitByProcessGroupIdOrProcessId($processgroupid, $processid);
        echo json_encode($productvariantdata);
    }
    public function getMachineByProcessGroupIdOrProcessId(){
        $PostData = $this->input->post();
        $processgroupid = $PostData['processgroupid'];
        $processid = $PostData['processid'];
        $type = (isset($PostData['type']) && $PostData['type']=="in")?1:0;

        $productdata = $this->Process_group->getMachineByProcessGroupIdOrProcessId($processgroupid, $processid, $type);
        echo json_encode($productdata);
    }
    public function getProcessByProcessGroupID(){
        $PostData = $this->input->post();
        $processgroupid = $PostData['processgroupid'];
        $productid = $PostData['productid'];
        $priceid = $PostData['priceid'];
        $qty = $PostData['qty'];
        $pricetype = $PostData['pricetype'];

        $processdata = $this->Process_group->getProcessByProcessGroupID($processgroupid,$productid,$priceid,$qty,$pricetype);
        echo json_encode($processdata);
    }
    
}?>