<?php

use Mpdf\Tag\Q;

defined('BASEPATH') OR exit('No direct script access allowed');

class Production_plan extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Production_plan');
        $this->load->model('Production_plan_model', 'Production_plan');
    }

    public function index() {
        $this->viewData['title'] = "Production Plan";
        $this->viewData['module'] = "production_plan/Production_plan";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Production Plan','View production plan.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("production_plan", "pages/production_plan.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Production_plan->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $materialstatus = $generatepo = '';
            
            $productidarray = explode(",",$datarow->productids);
            $priceidarray = explode(",",$datarow->priceids);
            $quantityarray = explode(",",$datarow->quanity);

            $productdata = $this->Production_plan->getProductionPlanRawMaterials($productidarray,$priceidarray,$quantityarray);

            $isAvailabel = array_filter(array_column($productdata, "requiredtostartproduction"));
            if(empty($isAvailabel)){
                if(!empty($productdata)){
                    $materialstatus = "Available";
                }
            }else{
                if(count($isAvailabel) == count($productdata)){
                    $materialstatus = "Not Available";
                }else{
                    $materialstatus = "Partial Available";
                }
                $generatepo = '<a href="javascript:void(0);">Place PO</a> | <a href="'.ADMIN_URL.'raw-material-request/add-raw-material-request/'.$datarow->id.'">Purchase Raw Material Request</a>';
            }
        
            if(($materialstatus=="Available" || $materialstatus=="Partial Available") || START_PROCESS_WITHOUT_STOCK==1){
                if($datarow->productionplanstatus!=1 && $datarow->processquanity < $datarow->productionquanity){
                    $actions .= '<a class="'.startprocess_class.'" href="javascipt:void(0)" onclick="openstartprocesspopup('.$datarow->id.','.$datarow->orderid.')" title="'.startprocess_title.'">'.startprocess_text.'</a>';
                }
            }
            $actions .= '<a class="'.view_class.'" href="'.ADMIN_URL.'production-plan/view-production-plan/'.$datarow->id.'" title="'.view_title.'">'.view_text.'</a>';
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'production-plan/production-plan-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            if($datarow->productionplanstatus==0){
                if(in_array($rollid, $delete)) {
                    $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Production&nbsp;Plan","'.ADMIN_URL.'production-plan/delete-mul-production-plan") >'.delete_text.'</a>';

                    $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
                }
            
                $productionplanstatus = '<span class="label label-warning">Pending</span>';
            }elseif($datarow->productionplanstatus==1){
                $productionplanstatus = '<span class="label label-green">Complete</span>';
            }else{
                $productionplanstatus = '<span class="label label-indigo">Running</span>';
            }
            
            $row[] = ++$counter;
            $row[] = ($datarow->orderid!=0)?"Order Wise":"Product Wise";
            $row[] = ($datarow->ordernumber=="")?"-":'<a href="'.ADMIN_URL.'order/view-order/'.$datarow->orderid.'" target="_blank">'.$datarow->ordernumber.'</a>';
            $row[] = $materialstatus;
            $row[] = $generatepo;
            $row[] = $productionplanstatus;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Production_plan->count_all(),
                        "recordsFiltered" => $this->Production_plan->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function add_production_plan($orderid='') {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Production Plan";
        $this->viewData['module'] = "production_plan/Add_production_plan";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getOrderListOnProductionPlan();
        $this->viewData['orderid'] = $orderid;

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(3);
        
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("production_plan", "pages/add_production_plan.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function production_plan_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Production Plan";
        $this->viewData['module'] = "production_plan/Add_production_plan";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getOrderListOnProductionPlan();
        $this->viewData['productionplandata'] = $this->Production_plan->getProductionPlanDataByID($id);

        if(empty($this->viewData['productionplandata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }
        if(empty($this->viewData['productionplandata']['orderid'])){
            $this->viewData['productionproductdata'] = $this->Production_plan->getProductionPlanProductsDataByPlanID($id);
        }
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(3);

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("add_production_plan","pages/add_production_plan.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function production_plan_add() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $json = array();
        $orderid = isset($PostData['orderid'])?$PostData['orderid']:"";
        
        if(!empty($orderid)){
            $this->Production_plan->_where = array('orderid' => $orderid);
            $Count = $this->Production_plan->CountRecords();
            if($Count>0){
                $json = array('error'=>2); // Product already exist 
                echo json_encode($json);
                exit;
            }
        }
            
        $InsertData = array('orderid' => $orderid,
                            'createddate' => $createddate,
                            'addedby' => $addedby,                              
                            'modifieddate' => $createddate,                             
                            'modifiedby' => $addedby 
                        );
    
        $ProductionPlanID = $this->Production_plan->Add($InsertData);
        
        if($ProductionPlanID){

            $productidarray = $PostData['productid'];
            $priceidarray = $PostData['priceid'];
            $quantityarray = $PostData['quantity'];

            $InsertData = array();
            if(!empty($productidarray)){
                foreach($productidarray as $key=>$productid){
                    
                    $orderproductid = !empty($PostData['orderproductid'])?$PostData['orderproductid'][$key]:0;
                    if($productid>0 && $priceidarray[$key]>0 && !empty($quantityarray[$key])){

                        $InsertData[] = array("productionplanid"=>$ProductionPlanID,
                                            "orderproductid"=>$orderproductid,
                                            "productid"=>$productid,
                                            "priceid"=>$priceidarray[$key],
                                            "quantity"=>$quantityarray[$key],
                                            'createddate' => $createddate,
                                            'modifieddate' => $createddate
                                        );

                    }

                }
                if(!empty($InsertData)){
                    $this->Production_plan->_table = tbl_productionplandetail;
                    $this->Production_plan->add_batch($InsertData);
                }
            }
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Production_plan->_table = tbl_productionplan;
                $this->Production_plan->_fields = '(SELECT orderid FROM '.tbl_orders.' WHERE id='.tbl_productionplan.'.orderid) as orderid';
                $this->Production_plan->_where = array("id"=>$ProductionPlanID);
                $plandata = $this->Production_plan->getRecordsById();
                $this->general_model->addActionLog(1,'Production Plan','Add new '.$plandata['orderid'].' order production plan.');
            }
            $json = array('error'=>1); // Product recepie inserted successfully
        } else {
            $json = array('error'=>0); // Product recepie not inserted 
        }
        echo json_encode($json);
    }
    public function update_production_plan() {
        
        $PostData = $this->input->post();
        
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $json = array();
        $productionplanid = trim($PostData['productionplanid']);
        $postorderid = $PostData['postorderid'];
        $orderid = isset($PostData['orderid'])?$PostData['orderid']:0;

        $updateData = array('modifieddate' => $modifieddate,                             
                            'modifiedby' => $modifiedby 
                        );
                        
        if(!empty($postorderid) && empty($orderid)){ // edit order wise
            $this->Production_plan->_where = array('id<>'=>$productionplanid,'orderid' => $postorderid);
            $Count = $this->Production_plan->CountRecords();
    
            if($Count>0){
                $json = array('error'=>2); // Already exist 
                echo json_encode($json);
                exit;
            }
        }
        if(!empty($orderid) && empty($postorderid)){ // edit order to product wise
            $this->Production_plan->_where = array('orderid' => $orderid);
            $Count = $this->Production_plan->CountRecords();
    
            if($Count>0){
                $json = array('error'=>2); // Already exist 
                echo json_encode($json);
                exit;
            }
            $updateData['orderid'] = $orderid;
        }
       
        $this->Production_plan->_where = array('id' =>$productionplanid);
        $isUpdated = $this->Production_plan->Edit($updateData);
        
        if($isUpdated){
            
            $productionplandetailidarray = $PostData['productionplandetailid'];
            $productidarray = $PostData['productid'];
            $priceidarray = $PostData['priceid'];
            $quantityarray = $PostData['quantity'];

            $InsertData = $UpdateData = $UpdatedPlanData = array();
            if(!empty($productidarray)){
                foreach($productidarray as $key=>$productid){
                    
                    $orderproductid = !empty($PostData['orderproductid'])?$PostData['orderproductid'][$key]:0;
                    $productionplandetailid = !empty($productionplandetailidarray[$key])?$productionplandetailidarray[$key]:"";
                    if(!empty($productionplandetailid)){
                        if($productid>0 && $priceidarray[$key]>0 && !empty($quantityarray[$key])){

                            $UpdateData[] = array("id"=>$productionplandetailid,
                                                "orderproductid"=>$orderproductid,
                                                "productid"=>$productid,
                                                "priceid"=>$priceidarray[$key],
                                                "quantity"=>$quantityarray[$key],
                                                'modifieddate' => $modifieddate
                                            );

                            $UpdatedPlanData[] = $productionplandetailid;
                        }
                    }else{
                        if($productid>0 && $priceidarray[$key]>0 && !empty($quantityarray[$key])){

                            $InsertData[] = array("productionplanid"=>$productionplanid,
                                                "orderproductid"=>$orderproductid,
                                                "productid"=>$productid,
                                                "priceid"=>$priceidarray[$key],
                                                "quantity"=>$quantityarray[$key],
                                                'createddate' => $modifieddate,
                                                'modifieddate' => $modifieddate
                                            );

                        }
                    }

                }
                
                $productionplandata = $this->Production_plan->getProductionPlanProductsDataByPlanID($productionplanid);
                
                if(!empty($productionplandata)){
                    $productionplanids = array_column($productionplandata, "id");
                    $resultId = array_diff($productionplanids, $UpdatedPlanData);

                    if(!empty($resultId)){
                        $this->Production_plan->_table = tbl_productionplandetail;
                        $this->Production_plan->Delete(array("id IN (".implode(",",$resultId).")"=>null));
                    }
                }
                $this->Production_plan->_table = tbl_productionplandetail;
                if(!empty($InsertData)){
                    $this->Production_plan->add_batch($InsertData);
                }
                if(!empty($UpdateData)){
                    $this->Production_plan->edit_batch($UpdateData, "id");
                }
            }

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Production_plan->_table = tbl_productionplan;
                $this->Production_plan->_fields = 'IFNULL((SELECT CONCAT(orderid," order") FROM '.tbl_orders.' WHERE id='.tbl_productionplan.'.orderid),"product wise") as orderid';
                $this->Production_plan->_where = array("id"=>$productionplanid);
                $plandata = $this->Production_plan->getRecordsById();
                
                $this->general_model->addActionLog(2,'Production Plan','Edit '.$plandata['orderid'].' production plan.');
            }
            $json = array('error'=>1); // Product recepie update successfully
        } else {
            $json = array('error'=>0); // Product recepie not updated
        }
        echo json_encode($json);
    }
    public function delete_mul_production_plan() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            
            if($checkuse == 0){

                $this->Production_plan->Delete(array('id'=>$row));
            }
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(3,'Production Plan','Delete production plan.');
        }
    }
    public function getOrderProductDetails(){
        $PostData = $this->input->post();
        $orderid = $PostData["orderid"];
        $productionplanid = $PostData["productionplanid"];

        $this->load->model("Order_model","Order");
        $productdata = $this->Order->getOrderProductDetails($orderid,$productionplanid);

        echo json_encode($productdata);
    }
    public function get_production_plan_raw_material(){

        $PostData = $this->input->post();
        $productidarray = $PostData["productid"];
        $priceidarray = $PostData["priceid"];
        $quantityarray = $PostData["quantity"];
        
        $productdata = $this->Production_plan->getProductionPlanRawMaterials($productidarray,$priceidarray,$quantityarray);

        // print_r($productdata); exit;
        echo json_encode($productdata);
    }
    public function view_production_plan($productionplanid){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Production Plan";
        $this->viewData['module'] = "production_plan/View_production_plan";
        
        /* 
        $this->viewData['regularproductdata'] = $this->Product_recepie->getRegularProductListByProductRecepie(); */
        
        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getOrderListOnProductionPlan("view");

        $this->viewData['productionplandata'] = $this->Production_plan->getProductionPlanDataByID($productionplanid);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Production Plan','View '.$this->viewData['productionplandata']['ordernumber'].' order production plan.');
        }

        $this->admin_headerlib->add_javascript("view_production_plan", "pages/view_production_plan.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function getProductionPlanMaterial(){
        $PostData = $this->input->post();
        $productionplanid = $PostData["productionplanid"];
        $json['readytostart'] = $json['missingrawmaterial'] = array();
        
        $productiondata = $this->Production_plan->getProductionPlanDetailsOnView($productionplanid);

        if(!empty($productiondata)){
            foreach($productiondata as $row){

                $row['date'] = $this->general_model->displaydatetime($row['createddate']);
                $productid = array($row['productid']);
                $priceid = array($row['priceid']);
                $quantity = array($row['quantity']);

                $productdata = $this->Production_plan->getProductionPlanRawMaterials($productid,$priceid,$quantity);

                $isAvailabel = array_filter(array_column($productdata, "requiredtostartproduction"));
                if(empty($isAvailabel)){
                    $json['readytostart'][] = $row;
                }else{
                    $json['missingrawmaterial'][] = $row;
                }
            }     
        }

        // print_r($json); exit;
        echo json_encode($json);
    }
    public function getStrartProcessProducts(){
        $PostData = $this->input->post();
        $productionplanid = $PostData["productionplanid"];
        $json = array();
        
        $productiondata = $this->Production_plan->getProductionPlanDetailsOnView($productionplanid);

        if(!empty($productiondata)){
            foreach($productiondata as $row){

                $productid = array($row['productid']);
                $priceid = array($row['priceid']);
                $quantity = array($row['quantity']);

                $productdata = $this->Production_plan->getProductionPlanRawMaterials($productid,$priceid,$quantity);

                $isAvailabel = array_filter(array_column($productdata, "requiredtostartproduction"));
                if(empty($isAvailabel)){
                    $row['available'] = 1;
                }else{
                    $row['available'] = 0;
                }
                $row['rawmaterials'] = json_encode($productdata);

                $row['processgroup'] = $this->Production_plan->getProcessGroupByProductionPlan($row['id']);

                $json[] = $row;
            }     
        }

        // print_r($productiondata); exit;
        echo json_encode($json);
    }
    public function start_production_plan_process() {
        
        $PostData = $this->input->post();
        $productionplanid = $PostData['productionplanid'];
        $productionplandetailidarray = $PostData['productionplandetailid'];
        $productidarray = $PostData['productid'];
        $priceidarray = $PostData['priceid'];
        
        $productionplandetid = $flashdata = $processgroupidarray = array();
        if(!empty($productionplandetailidarray)){
            foreach($productionplandetailidarray as $k=>$productionplandetailid){

                
                $checked = isset($PostData['productcheck'.$productionplandetailid])?1:0;
                if($checked == 1){
                    $productid = $productidarray[$k];
                    $priceid = $priceidarray[$k];
                    $productionqty = $PostData['productionqty'][$k];
                    $processgroupid = $PostData['processgroupid'][$k];

                    if($processgroupid!=0){

                        if(!in_array($processgroupid, $processgroupidarray)){
                            $processgroupidarray[] = $processgroupid;
                        }
                        
                        $productionplandetid[] = $productionplandetailid;
                        $flashdata[] = array(
                                "productionplanid"=>$productionplanid,
                                "productionplandetailid"=>$productionplandetailid,
                                "productid"=>$productid,
                                "priceid"=>$priceid,
                                "quantity"=>$productionqty,
                                "processgroupid"=>$processgroupid
                            );
                    }
                }
            }
        }
        if(!empty($flashdata)){
            $this->session->set_flashdata('productionplandata', $flashdata);
        }
        if(!empty($productionplandetid)){
            // $processgroup = $this->Production_plan->getProcessGroupByProductionPlan(implode(",",$productionplandetid));
            
            if(!empty($processgroupidarray)){
                $processgroupid = implode("-",$processgroupidarray);
                
                echo json_encode(array('error'=>1,"processgroupid"=>$processgroupid));
            }else{
                echo json_encode(array('error'=>0));
            }
        }
    }
    public function refresh_production_process() {
        
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $productionplandetailidarray = isset($PostData['productionplandetailid'])?$PostData['productionplandetailid']:'';
        $productionqtyarray = isset($PostData['productionqty'])?$PostData['productionqty']:'';

        $UpdateData = $DeleteData = array();
        if(!empty($productionplandetailidarray)){
            foreach($productionplandetailidarray as $key=>$productionplandetailid){

                if(!empty($productionqtyarray[$key])){
                    $UpdateData[] = array("id"=>$productionplandetailid,
                                        "quantity"=>$productionqtyarray[$key],
                                        'modifieddate' => $modifieddate
                                    );
                }else{
                    $DeleteData[] = $productionplandetailid;
                }
            }
        }
        $this->Production_plan->_table = tbl_productionplandetail;
        if(!empty($UpdateData)){
            $this->Production_plan->edit_batch($UpdateData, "id");
        }
        if(!empty($DeleteData)){
            $this->Production_plan->Delete(array("id IN (".implode(",",$DeleteData).")"=>null));
        }
        echo 1; 
    }
    /* public function get_production_plan_raw_material(){

        $PostData = $this->input->post();
        $productidarray = $PostData["productid"];
        $priceidarray = $PostData["priceid"];
        $quantityarray = $PostData["quantity"];
        $this->load->model('Stock_report_model', 'Stock');
       
        $productdata = array();
        if(!empty($productidarray)){
            $materialdata = $this->Production_plan->getRawMaterialDetails($productidarray,$priceidarray);
            
            foreach($materialdata as $material){

                $key = array_search($material['orderproductid'],$productidarray);
                $quantity = $quantityarray[$key];
                if(!empty($quantity)){
                    
                    $stockdata = $this->Stock->getAdminProductStock($material['productid'],0);
                    $stockqty = $stockdata[0]['openingstock'];
    
                    $SingleQty = $this->Production_plan->convertProductStockToUnitConversation($material['productid'],$material['unitid']);
                    $stock = $stockqty * $SingleQty;
                    $requiredstock = $material['value'] * $quantity;
                    
                    $requiredtostartproduction = "";
                    if($requiredstock > $stock){
                        $requiredtostartproduction = $requiredstock - $stock;
                    }
                    $remainingstock = ($stock - $requiredstock)>=0?($stock - $requiredstock):0;

                    $productdata[] = array("productid"=>$material['productid'],
                                            "productname"=>$material['productname'],
                                            "value"=>$material['value'],
                                            "unit"=>$material['unit'],
                                            "stock"=>$stock,
                                            "requiredstock"=>$requiredstock,
                                            "requiredtostartproduction"=>$requiredtostartproduction,
                                            "remainingstock"=>$remainingstock
                                        );
                }

            }
        }
        // print_r($productdata); exit;
        echo json_encode($productdata);
    } */
}?>