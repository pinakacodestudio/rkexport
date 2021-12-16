<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Raw_material_request extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Raw_material_request');
        $this->load->model('Raw_material_request_model', 'Raw_material_request');
    }

    public function index() {
        $this->viewData['title'] = "Raw Material Request";
        $this->viewData['module'] = "raw_material_request/Raw_material_request";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Raw Material Request','View raw material request.');
        }
        $this->admin_headerlib->add_javascript("raw_material_request", "pages/raw_material_request.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Raw_material_request->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'raw-material-request/raw-material-request-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'raw-material-request/check-raw-material-request-use","Raw&nbsp;Material&nbsp;Request","'.ADMIN_URL.'raw-material-request/delete-mul-raw-material-request") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            $actions.='<a class="'.view_class.'" href="javascript:void(0)" title="'.view_title.'" onclick=viewrequestdetail('.$datarow->id.') >'.view_text.'</a>';

            if($datarow->status==0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                    <ul class="dropdown-menu" role="menu">
                        <li id="dropdown-menu">
                            <a onclick="chagerequeststatus(1,'.$datarow->id.')">Approve</a>
                        </li>
                        <li id="dropdown-menu">
                            <a onclick="chagerequeststatus(2,'.$datarow->id.')">Cancel</a>
                        </li>
                    </ul>';
            }else if($datarow->status==1){
                $dropdownmenu = '<span class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approve</span>';
            }else if($datarow->status==2){
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }
            $requeststatus = '<div class="dropdown">'.$dropdownmenu.'</div>';
            if($datarow->ordernumber!=""){
                $ordernumber = '<a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" target="_blank">'.$datarow->ordernumber.'</a>';
            }else{
                $ordernumber = "<p class='text-center'>-</p>";
            }

            $row[] = ++$counter;
            $row[] = $ordernumber;
            $row[] = $datarow->requestno;
            $row[] = ucwords($datarow->addedbyname);
            $row[] = $this->general_model->displaydate($datarow->requestdate);
            $row[] = ($datarow->estimatedate!="0000-00-00")?$this->general_model->displaydate($datarow->estimatedate):"-";
            $row[] = $requeststatus;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Raw_material_request->count_all(),
                        "recordsFiltered" => $this->Raw_material_request->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_raw_material_request($productionplanid="") {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Raw Material Request";
        $this->viewData['module'] = "raw_material_request/Add_raw_material_request";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2,0,0,'withvariant','admin_variant');
        
        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->viewData['requestno'] = time().rand(10,99).rand(10,99).rand(10,99).rand(10,99);

        if(!empty($productionplanid)){
            $this->load->model('Production_plan_model', 'Production_plan');
            $productionplandata = $this->Production_plan->getProductionPlanDataByID($productionplanid);

            $this->viewData['orderid'] = $productionplandata['orderid'];
            $productidarray = explode(",",$productionplandata['productids']);
            $priceidarray = explode(",",$productionplandata['priceids']);
            $quantityarray = explode(",",$productionplandata['qtys']);

            $this->viewData['rawmaterialrequestproductdata'] = $this->Production_plan->getProductionPlanRawMaterials($productidarray,$priceidarray,$quantityarray);
            
            // echo "<pre>"; print_r($priceidarray); exit;

        }
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_raw_material_request", "pages/add_raw_material_request.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function raw_material_request_add() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $orderid = $PostData['orderid'];
        $requestno = $PostData['requestno'];
        $requestdate = $this->general_model->convertdate($PostData['requestdate']);
        $estimatedate = ($PostData['estimatedate']!="")?$this->general_model->convertdate($PostData['estimatedate']):"";
        $remarks = $PostData['remarks'];
        $this->load->model('Product_model', 'Product');

        $this->Raw_material_request->_where = array('requestno' => $requestno);
        $Count = $this->Raw_material_request->CountRecords();

        if($Count==0){
                
            $InsertData = array('orderid' => $orderid,
                                'requestno' => $requestno,
                                'requestdate' => $requestdate,
                                'estimatedate' => $estimatedate,
                                'remarks' => $remarks,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
            
            $RequestID = $this->Raw_material_request->Add($InsertData);
            if($RequestID){
                
                $productidArr = $PostData['productid'];
                
                if(!empty($productidArr)){
                    $insertData = array();
                    foreach($productidArr as $i=>$productid){
                        $priceid = $PostData['priceid'][$i];
                        $unitid = $PostData['unitid'][$i];
                        $quantity = $PostData['qty'][$i];
                        
                        if(!empty($productid) && !empty($priceid) && !empty($quantity)){

                            $this->Product->_fields = "name";
                            $this->Product->_where = array("id"=>$productid);
                            $productdata = $this->Product->getRecordsById();

                            $insertData[] = array(
                                "rawmaterialrequestid"=>$RequestID,
                                "productid"=>$productid,
                                "priceid"=>$priceid,
                                "unitid"=>$unitid,
                                "quantity"=>$quantity,
                                "productname"=>$productdata['name']
                            );
                        }
                    } 
                    if(!empty($insertData)){
                        $this->Raw_material_request->_table = tbl_rawmaterialrequestproduct;
                        $this->Raw_material_request->add_batch($insertData);
                    }
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Raw Material Request','Add new request no. '.$requestno.' raw material request.');
                }
                echo 1; // raw material request inserted successfully
            } else {
                echo 0; // raw material request not inserted 
            }
        } else {
            echo 2; // raw material request already added
        }
    }
    public function raw_material_request_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Raw Material Request";
        $this->viewData['module'] = "raw_material_request/Add_raw_material_request";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2,0,0,'withvariant','admin_variant');

        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->viewData['rawmaterialrequestdata'] = $this->Raw_material_request->getRawMaterialRequestDataByID($id);
        if(empty($this->viewData['rawmaterialrequestdata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $this->viewData['rawmaterialrequestproductdata'] = $this->Raw_material_request->getRawMaterialRequestProductsByRequestID($id);
        
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_raw_material_request","pages/add_raw_material_request.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_raw_material_request() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        // echo "<pre>"; print_r($PostData); exit;
        $rawmaterialrequestid = $PostData['rawmaterialrequestid'];
        $orderid = $PostData['orderid'];
        $requestno = $PostData['requestno'];
        $requestdate = $this->general_model->convertdate($PostData['requestdate']);
        $estimatedate = ($PostData['estimatedate']!="")?$this->general_model->convertdate($PostData['estimatedate']):"";
        $remarks = $PostData['remarks'];
        $this->load->model('Product_model', 'Product');

        $this->Raw_material_request->_where = array("id<>"=>$rawmaterialrequestid,'requestno' => $requestno);
        $Count = $this->Raw_material_request->CountRecords();

        if($Count==0){
                
            $updateData = array('orderid' => $orderid,
                                'requestno' => $requestno,
                                'requestdate' => $requestdate,
                                'estimatedate' => $estimatedate,
                                'remarks' => $remarks,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);

            $this->Raw_material_request->_where = array('id' =>$rawmaterialrequestid);
            $isUpdated = $this->Raw_material_request->Edit($updateData);
            
            if($isUpdated){

                if(isset($PostData['removerawmaterialrequestproductid']) && $PostData['removerawmaterialrequestproductid']!=''){
                    $query=$this->readdb->select("id")
                                    ->from(tbl_rawmaterialrequestproduct)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removerawmaterialrequestproductid'])))."')>0")
                                    ->get();
                    $rawmaterialrequestproductData = $query->result_array();
                    
                    if(!empty($rawmaterialrequestproductData)){
                        foreach ($rawmaterialrequestproductData as $row) {
                            $this->Raw_material_request->_table = tbl_rawmaterialrequestproduct;
                            $this->Raw_material_request->Delete(array("id"=>$row['id']));
                        }
                    }
                }
                $productidArr = $PostData['productid'];
                
                if(!empty($productidArr)){
                    $insertData = $updateData = $deleteData = array();
                    foreach($productidArr as $i=>$productid){
                        $priceid = $PostData['priceid'][$i];
                        $unitid = $PostData['unitid'][$i];
                        $quantity = $PostData['qty'][$i];
                        $rawmaterialrequestproductid = isset($PostData['rawmaterialrequestproductid'][$i])?$PostData['rawmaterialrequestproductid'][$i]:0;
                        
                        if(!empty($productid) && !empty($priceid) && !empty($quantity)){
                            $this->Product->_fields = "name";
                            $this->Product->_where = array("id"=>$productid);
                            $productdata = $this->Product->getRecordsById();
                            
                            if(!empty($rawmaterialrequestproductid)){
                                
                                $updateData[] = array(
                                    "id"=>$rawmaterialrequestproductid,
                                    "productid"=>$productid,
                                    "priceid"=>$priceid,
                                    "unitid"=>$unitid,
                                    "quantity"=>$quantity,
                                    "productname"=>$productdata['name']
                                );
                            }else{

                                $insertData[] = array(
                                    "rawmaterialrequestid"=>$rawmaterialrequestid,
                                    "productid"=>$productid,
                                    "priceid"=>$priceid,
                                    "unitid"=>$unitid,
                                    "quantity"=>$quantity,
                                    "productname"=>$productdata['name']
                                );
                            }
                        }else{
                            if(!empty($rawmaterialrequestproductid)){
                                $deleteData[] = $rawmaterialrequestproductid;
                            }
                        }
                    }
                    if(!empty($insertData)){
                        $this->Raw_material_request->_table = tbl_rawmaterialrequestproduct;
                        $this->Raw_material_request->add_batch($insertData);
                    }
                    if(!empty($updateData)){
                        $this->Raw_material_request->_table = tbl_rawmaterialrequestproduct;
                        $this->Raw_material_request->edit_batch($updateData,"id");
                    }
                    if(!empty($deleteData)){
                        $this->Raw_material_request->_table = tbl_rawmaterialrequestproduct;
                        $this->Raw_material_request->Delete(array("id IN (".implode(",",$deleteData).")"=>null));
                    }
                } 
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Raw Material Request','Edit request no. '.$requestno.' raw material request.');
                }
                echo 1; // Raw material request update successfully
            } else {
                echo 0; // Raw material request unit not updated
            }
        } else {
            echo 2; // Raw material request already added
        }
    }

    public function check_raw_material_request_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            /* $this->readdb->select('raw_material_requestid');
            $this->readdb->from(tbl_product);
            $where = array("raw_material_requestid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }

    public function delete_mul_raw_material_request() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            if($checkuse == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Raw_material_request->_fields = "requestno";
                    $this->Raw_material_request->_where = array("id"=>$row);
                    $data = $this->Raw_material_request->getRecordsByID();
                    
                    $this->general_model->addActionLog(3,'Raw Material Request','Delete request no. '.$data['requestno'].' raw material request.');
                }
                $this->Raw_material_request->Delete(array('id'=>$row));
            }
        }
    }
    public function update_raw_material_request_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $requestId = $PostData['requestId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        
        $this->Raw_material_request->_where = array("id" => $requestId);
        $this->Raw_material_request->Edit($updateData);
       
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Raw_material_request->_fields = "requestno";
            $this->Raw_material_request->_where=array("id"=>$requestId);
            $data = $this->Raw_material_request->getRecordsByID();

            $this->general_model->addActionLog(2,'Order','Change request no. '.$data['requestno'].' status on raw material request.');
        }

        echo 1;    
    }
    public function getVariantByProductId(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getVariantByProductIdForAdmin($PostData['productid']);
        echo json_encode($productdata);
    }
    public function getproductdetailsByBarcode(){
        $PostData = $this->input->post();
        
        $this->load->model('Product_model','Product');
        $productdata = $this->Product->getadminproductdetailsByBarcode($PostData['barcode']);
        
        echo json_encode($productdata);
    }
    public function get_raw_material_request_detail(){
        $PostData = $this->input->post();
        
        $requestid = $PostData['requestid'];
        $requestproductdata = $this->Raw_material_request->getRawMaterialRequestProductsByRequestID($requestid);
        
        echo json_encode($requestproductdata);
    }
    
}?>