<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_process extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Product_process');
        $this->load->model('Process_group_model', 'Process_group');
        $this->load->model('Product_process_model', 'Product_process');
    }

    public function index() {
        $this->viewData['title'] = "Product Process";
        $this->viewData['module'] = "product_process/Product_process";
        $this->viewData['VIEW_STATUS'] = "1";
        
        $this->viewData['processgroupdata'] = $this->Product_process->getProcessGroupOnProductProcess();

        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['processdata'] = $this->Product_process->getProcessOnProductProcess();
        $this->viewData['finalproductdata'] = $this->Product_process->getFinalProductsOnProductProcess();
        $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOnProductProcess();
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        $this->viewData['designationdata'] = $this->Product_process->getDesignationOnProductProcess();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Process','View product process.');
        }
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("product_process", "pages/product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];
        $list = $this->Product_process->get_datatables();
        // print_r($list); exit;
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image = $dropdownmenu = '';
            
            $actions .= '<a class="'.view_class.'" href="'.ADMIN_URL.'product-process/view-product-process/'. $datarow->id.'/'.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';
            
            if(in_array($rollid, $edit)) {
                if($datarow->type == 0){
                    $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'product-process/stock-out-process-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }else{
                    $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'product-process/stock-in-process-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Product&nbsp;Process","'.ADMIN_URL.'product-process/delete-mul-product-process") >'.delete_text.'</a>';

            }
            $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            if($datarow->type == 1 && $datarow->processstatus != 0 && $datarow->ispending > 0 && $datarow->isinprocess == 0 && in_array($rollid, $edit)){
                $actions.='<a class="'.inprocess_class.'" title="'.inprocess_title.'" href="'.ADMIN_URL.'product-process/stock-in-process/'.$datarow->id.'">'.inprocess_text.'</a>';
            }
            if($datarow->type == 0 && $datarow->productcount > 0){
                if(in_array($rollid, $edit)) {
                    $actions.='<a class="'.reprocess_class.'" title="'.reprocess_title.'" href="'.ADMIN_URL.'product-process/reprocess/'.
                    $datarow->id.'">'.reprocess_text.'</a>';
                    if($datarow->processstatus == 1){
                        if($datarow->isinprocess == 0){
                            $actions.='<a class="'.inprocess_class.'" title="'.inprocess_title.'" href="'.ADMIN_URL.'product-process/stock-in-process/'.$datarow->id.'">'.inprocess_text.'</a>';
                        }
                    }else{
                        /* $actions.='<a class="'.outprocess_class.'" title="'.outprocess_title.'" href="'.ADMIN_URL.'product-process/start-new-process">'.outprocess_text.'</a>'; */
                    }
                }
            }
            if($datarow->type == 1 && $datarow->processstatus == 1 && $datarow->isnextprocess != 0 && $datarow->productcount > 0){
                if(in_array($rollid, $edit)) {
                    $actions .= '<a class="'.nextprocess_class.'" title="'.nextprocess_title.'" href="'.ADMIN_URL.'product-process/next-process/'.$datarow->id.'">'.nextprocess_text.'</a>';
                }
            }
            if($datarow->isfirstprocess == 1 && $datarow->type == 0 && $datarow->isreprocess == 0){
                $actions .= '<a class="'.viewprocess_class.'" title="'.viewprocess_title.'" href="'.ADMIN_URL.'product-process/view-all-process/'.$datarow->processgroupid.'/'.$datarow->productprocessid.'" target="_blank">'.viewprocess_text.'</a>';
            }
            if($datarow->type == 0){
                if(in_array('print', $additionalrights)) {
                    $actions .= '<a class="'.print_class.'" title="'.print_title.'" onclick="printchallan('.$datarow->productprocessid.')">'.print_text.'</a>';
                }
            }
            /* if($datarow->processstatus == 0){
                
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
            } */

            if($datarow->processstatus == 0){
                
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Running <span class="caret"></span></button><ul class="dropdown-menu" role="menu">';

                if($datarow->type == 1){
                    $dropdownmenu .= '<li id="dropdown-menu">
                                        <a onclick="chageprocessstatus(2,'.$datarow->id.')">Partially</a>
                                    </li>';    
                }
                $dropdownmenu .= '<li id="dropdown-menu">
                                    <a onclick="chageprocessstatus(1,'.$datarow->id.')">Complete</a>
                                </li>
                            </ul>';
                
                $dropdownmenu .= '</ul>';
            }else if($datarow->processstatus == 2){
                $dropdownmenu = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Partially <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                    <li id="dropdown-menu">
                                        <a onclick="chageprocessstatus(1,'.$datarow->id.')">Complete</a>
                                    </li>
                                </ul>';
            }else if($datarow->processstatus == 1){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';
            }
            $processstatus = '<div class="dropdown">'.$dropdownmenu.'</div>';
            if($datarow->comments!=""){
                $groupname = '<a title="Comments" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.ucfirst($datarow->comments).'">'.(($datarow->processgroup!="")?ucwords($datarow->processgroup):"-").'</a>';
            }else{
                $groupname = ($datarow->processgroup!="")?ucwords($datarow->processgroup):"-";
            }

            if($datarow->processbymemberid==0){
                $row[] = ++$counter.'<i class="fa fa-truck fa-lg" style="float: right;"></i>';
            }else{
                $row[] = ++$counter;
            }
            $row[] = $groupname;
            $row[] = ($datarow->processname!="")?ucwords($datarow->processname):"-";
            $row[] = $datarow->batchno;
            $row[] = $datarow->designation;
            $row[] = $datarow->processtype;
            $row[] = $this->general_model->displaydate($datarow->transactiondate);  
            $row[] = $processstatus;
            $row[] = ucwords($datarow->addedby);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_process->count_all(),
                        "recordsFiltered" => $this->Product_process->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function start_new_process($processgroupid="",$productionplanid="",$orderid="") {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Start New Process | Stock Out Process";
        $this->viewData['module'] = "product_process/Add_product_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "OUT";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];

        $where = "";
        if($processgroupid!=""){
            $processgroupidarr = explode("-",$processgroupid);
            /* if(count($processgroupidarr)==1){
                $this->viewData['processgroupid'] = $processgroupidarr[0];
            } */
            $this->viewData['processgroupid'] = $processgroupidarr;
            $where .= " AND id IN (".implode(",",$processgroupidarr).")";
        }
        if($this->session->userdata[base_url().'ADMINUSERTYPE']!=1){
            $where .= " AND (id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$ADMINID."),designationid)>0)) OR 1=1)";
        }

        
        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup($where);
        
        // print_r($this->viewData['processgroupdata']); exit;
        if(!empty($this->viewData['processgroupdata'])){
            $this->load->model('Process_model', 'Process');
            foreach($this->viewData['processgroupdata'] as $pg=>$processgroup){
                $processdata = $this->Process->getProcessByProcessGroupId($processgroup['id'],'','OUT');

                $this->viewData['processgroupdata'][$pg]['processdata'] = json_encode($processdata,true);
            }
        }
        
        if($productionplanid!=""){
            $this->viewData['productionplanid'] = $productionplanid;
        }
        if($orderid!=""){
            $this->viewData['orderid'] = $orderid;
        }
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        $this->viewData['processbatchno'] = PROCESS_BATCH_NO."-1";
        
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getCompanySalesOrderOnProductProcess();

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
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];

        $this->viewData['productprocessdata'] = $this->Product_process->getProductProcessDataById($productprocessid,"IN","add");
        // echo "<pre>"; print_r($this->viewData['productprocessdata']); exit;
        $where = "";
        if($this->session->userdata[base_url().'ADMINUSERTYPE']!=1){
            $where .= " AND (id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$ADMINID."),designationid)>0)) OR 1=1)";
        }
        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup($where);
        if(!empty($this->viewData['processgroupdata'])){
            $this->load->model('Process_model', 'Process');
            foreach($this->viewData['processgroupdata'] as $pg=>$processgroup){
                   $processdata = $this->Process->getProcessByProcessGroupId($processgroup['id'],'','OUT');

                $this->viewData['processgroupdata'][$pg]['processdata'] = json_encode($processdata,true);
            }
        }
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "IN";

        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();
        
        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getCompanySalesOrderOnProductProcess();

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
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];

        $this->viewData['productprocessdata'] = $this->Product_process->getProductProcessDataById($productprocessid,"REPROCESS");
       
        $where = "";
        if($this->session->userdata[base_url().'ADMINUSERTYPE']!=1){
            $where .= " AND (id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$ADMINID."),designationid)>0)) OR 1=1)";
        }
        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup($where);
        if(!empty($this->viewData['processgroupdata'])){
            $this->load->model('Process_model', 'Process');
            foreach($this->viewData['processgroupdata'] as $pg=>$processgroup){
                $processdata = $this->Process->getProcessByProcessGroupId($processgroup['id'],'','OUT');

                $this->viewData['processgroupdata'][$pg]['processdata'] = json_encode($processdata,true);
            }
        }
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "REPROCESS";

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getCompanySalesOrderOnProductProcess();

        // echo "<pre>"; print_r($this->viewData['productprocessdata']); exit;
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("start_new_process", "pages/add_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function next_process($productprocessid) {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Next Process";
        $this->viewData['module'] = "product_process/Add_product_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "NEXTPROCESS";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];

        if($productprocessid == ""){
            redirect(ADMINFOLDER."dashboard");
        }
       
        $this->viewData['productprocessdata'] = $this->Product_process->getProductProcessDataById($productprocessid,"NEXTPROCESS");
        $this->viewData['processgroupid'] = array($this->viewData['productprocessdata']['processgroupid']);
        
        $where = "";
        if($this->session->userdata[base_url().'ADMINUSERTYPE']!=1){
            $where .= " AND (id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$ADMINID."),designationid)>0)) OR 1=1)";
        }
        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup($where);
        if(!empty($this->viewData['processgroupdata'])){
            $this->load->model('Process_model', 'Process');
            foreach($this->viewData['processgroupdata'] as $pg=>$processgroup){
                $processdata = $this->Process->getProcessByProcessGroupId($processgroup['id'],$this->viewData['productprocessdata']['processgroupmappingid'],'NEXTPROCESS');

                $this->viewData['processgroupdata'][$pg]['processdata'] = json_encode($processdata,true);
            }
        }
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        // echo "<pre>"; print_r($this->viewData['processgroupdata']); exit;

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getCompanySalesOrderOnProductProcess();

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("start_new_process", "pages/add_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function stock_out_process_edit($productprocessid) {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Stock Out Process";
        $this->viewData['module'] = "product_process/Add_product_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "OUT";
        $this->viewData['action'] = "1";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];

        if($productprocessid == ""){
            redirect(ADMINFOLDER."dashboard");
        }
        $this->viewData['productprocessdata'] = $this->Product_process->getProductProcessDataById($productprocessid);
        // print_r($this->viewData['productprocessdata']); exit;
        $where = "";
        if($this->session->userdata[base_url().'ADMINUSERTYPE']!=1){
            $where .= " AND (id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$ADMINID."),designationid)>0)) OR 1=1)";
        }
        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup($where);
        if(!empty($this->viewData['processgroupdata'])){
            $this->load->model('Process_model', 'Process');
            foreach($this->viewData['processgroupdata'] as $pg=>$processgroup){
                $processdata = $this->Process->getProcessByProcessGroupId($processgroup['id'],'','OUT');

                $this->viewData['processgroupdata'][$pg]['processdata'] = json_encode($processdata,true);
            }
        }
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');

        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Order_model', 'Order');
        $this->viewData['ExtraChargesData'] = $this->Order->getExtraChargesDataByReferenceID($productprocessid,4);
        
        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getCompanySalesOrderOnProductProcess();

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("start_new_process", "pages/add_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function stock_in_process_edit($productprocessid) {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Stock In Process";
        $this->viewData['module'] = "product_process/Add_product_process";   
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['processtype'] = "IN";
        $this->viewData['action'] = "1";
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];
        
        if($productprocessid == ""){
            redirect(ADMINFOLDER."dashboard");
        }
        $this->viewData['productprocessdata'] = $this->Product_process->getProductProcessDataById($productprocessid,"IN","edit");
        // echo "<pre>"; print_r($this->viewData['productprocessdata']); exit;
        if(empty($this->viewData['productprocessdata'])){
            redirect(ADMINFOLDER."dashboard");
        }
        $where = "";
        if($this->session->userdata[base_url().'ADMINUSERTYPE']!=1){
            $where .= " AND (id IN (SELECT processgroupid FROM ".tbl_processgroupmapping." WHERE processid IN (SELECT id FROM ".tbl_process." WHERE FIND_IN_SET((SELECT designationid FROM ".tbl_user." WHERE id=".$ADMINID."),designationid)>0)) OR 1=1)";
        }
        $this->viewData['processgroupdata'] = $this->Process_group->getActiveProcessGroup($where);
        if(!empty($this->viewData['processgroupdata'])){
            $this->load->model('Process_model', 'Process');
            foreach($this->viewData['processgroupdata'] as $pg=>$processgroup){
                $processdata = $this->Process->getProcessByProcessGroupId($processgroup['id'],'','OUT');

                $this->viewData['processgroupdata'][$pg]['processdata'] = json_encode($processdata,true);
            }
        }
        $this->load->model('Vendor_model', 'Vendor');
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        
        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getMemberActiveExtraCharges();

        $this->load->model('Order_model', 'Order');
        $this->viewData['ExtraChargesData'] = $this->Order->getExtraChargesDataByReferenceID($productprocessid,4);

        $this->load->model('Order_model', 'Order');
        $this->viewData['orderdata'] = $this->Order->getCompanySalesOrderOnProductProcess();

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("start_new_process", "pages/add_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function view_product_process($productprocessid){
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Product Process";
        $this->viewData['module'] = "product_process/View_product_process";
        $this->viewData['title'] = "View Product Process";
        $this->viewData['type'] = "OUT";
        
        $this->viewData['productprocessdata'] = $this->Product_process->getProductProcessDetailsById($productprocessid);
        
        if(empty($this->viewData['productprocessdata'])){
            redirect(ADMINFOLDER."dashboard");
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){

            $this->general_model->addActionLog(4,'Product Process','View '.$this->viewData['productprocessdata']['processname'].' product process.');
        }
        // echo "<pre>"; print_r($this->viewData['productprocessdata']); exit;
        $this->admin_headerlib->add_javascript("View_product_process", "pages/view_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function view_all_process($processgroupid,$productprocessid){
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Product Process";
        $this->viewData['module'] = "product_process/View_all_product_process";
        $this->viewData['title'] = "View All Product Process";
        $this->viewData['heading'] = 'View All Product Process Details';

        $this->viewData['productprocessdata'] = $this->Product_process->getAllProductProcessDetailsByProcessGroupId($processgroupid,$productprocessid);
        // print_r($this->viewData['productprocessdata']); exit;
        $this->viewData['producttotal'] = $this->Product_process->getTotalProductProcessAmountDetailsByProcessGroupId($processgroupid,$productprocessid);
        // echo "<pre>"; print_r($this->viewData['productprocessdata']); exit;
        if(empty($this->viewData['productprocessdata'])){
            redirect(ADMINFOLDER."dashboard");
        }

        if($this->viewData['submenuvisibility']['managelog'] == 1){

            $this->general_model->addActionLog(0,'Product Process','View '.$this->viewData['productprocessdata']['processes'][0]['processgroup'].' process group all product process details.');
        }
        
        $this->admin_headerlib->add_javascript("View_product_process", "pages/view_product_process.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function product_process_add2() {
        
        $PostData = $this->input->post();
        
        // $finalorderproducts = $PostData['finalorderproducts'][1];

        // print_r(json_decode($finalorderproducts));
       
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $json = array();
        
        $processtype = $PostData['processtype'];
        $processgroupmappingid = $PostData['processgroupmappingid'];
        $batchno = $PostData['batchno'];
        $mainbatchprocessid = $PostData['mainbatchprocessid'];
        $productionplanqtydetail = !empty($PostData['productionplanqtydetail'])?json_decode($PostData['productionplanqtydetail'],true):"";
        // print_r($productionplanqtydetail); exit;
        // $processid = trim($PostData['processid']);
        $transactiondate = $this->general_model->convertdate($PostData['transactiondate']);
        $comments = $PostData['remarks'];
        $isreprocess = $parentproductprocessid = $productprocessid = 0;
        
        if($processtype == "IN"){
            $processbymemberid = $PostData['processbymemberid'];
            $vendorid = $PostData['postvendorid'];
            $machineid = $PostData['postmachineid'];
            $estimatedate = !empty($PostData['postestimatedate'])?$this->general_model->convertdate($PostData['postestimatedate']):"";
            $orderid = $PostData['postorderid'];
            
            $type = 1;
            $parentproductprocessid = $PostData['parentproductprocessid'];
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
            if(!empty($PostData['inextrachargesid'])){
                $extrachargesidarr = (isset($PostData['inextrachargesid']))?$PostData['inextrachargesid']:'';
                $extrachargestaxarr = (isset($PostData['inextrachargestax']))?$PostData['inextrachargestax']:'';
                $extrachargeamountarr = (isset($PostData['inextrachargeamount']))?$PostData['inextrachargeamount']:'';
                $extrachargesnamearr = (isset($PostData['inextrachargesname']))?$PostData['inextrachargesname']:'';
                $extrachargepercentagearr = (isset($PostData['inextrachargepercentage']))?$PostData['inextrachargepercentage']:'';
            }
        }else{
            /* if($processtype == "REPROCESS"){
                $processbymemberid = $PostData['processbymemberid'];
            }else{
            } */
            $processbymemberid = $PostData['processedby'];
            $vendorid = ($PostData['processedby']==0 && isset($PostData['vendorid']))?$PostData['vendorid']:0;
            $machineid = ($PostData['processedby']==1 && isset($PostData['machineid']))?$PostData['machineid']:0;
            $estimatedate = !empty($PostData['estimatedate'])?$this->general_model->convertdate($PostData['estimatedate']):"";
            $orderid = $PostData['orderid'];

            $type = $parentproductprocessid = 0;
            
            /* $this->Product_process->_table = tbl_productprocess;
            $this->Product_process->_where = array('batchno' => $batchno);
            $Count = $this->Product_process->CountRecords();
            if($Count>0){
                $json = array('error'=>2); // Process group already added.
                json_encode($json);exit;
            } */
            if(!empty($PostData['outextrachargesid'])){
                $extrachargesidarr = (isset($PostData['outextrachargesid']))?$PostData['outextrachargesid']:'';
                $extrachargestaxarr = (isset($PostData['outextrachargestax']))?$PostData['outextrachargestax']:'';
                $extrachargeamountarr = (isset($PostData['outextrachargeamount']))?$PostData['outextrachargeamount']:'';
                $extrachargesnamearr = (isset($PostData['outextrachargesname']))?$PostData['outextrachargesname']:'';
                $extrachargepercentagearr = (isset($PostData['outextrachargepercentage']))?$PostData['outextrachargepercentage']:'';
            }

            if(START_PROCESS_WITHOUT_STOCK==0){
                $this->load->model('Stock_report_model', 'Stock');
                if(!empty($PostData['outproductid'])){
                    $this->load->model('Stock_report_model', 'Stock');
                    foreach($PostData['outproductid'] as $s=>$stockproductid){
                        if($stockproductid > 0 && $PostData['outproductvariantid'][$s] > 0 && $PostData['quantity'][$s] > 0){
                            $ProductStock = $this->Stock->getAdminProductStock($stockproductid,1);
                            $key = array_search($PostData['outproductvariantid'][$s], array_column($ProductStock, 'priceid'));
                            $availablestock = !empty($ProductStock)?$ProductStock[$key]['overallclosingstock']:0;
                            if(STOCKMANAGEMENT==1){
                                if($PostData['quantity'][$s] > $availablestock){
                                    //Quantity greater than stock quantity.
                                    echo json_encode(array("error"=>-3,"message"=>"Quantity greater than stock quantity of ".($s+1)." out product !")); exit;
                                }
                            }
                        }
                    }
                }
            }
        }
       
        if($processtype == "REPROCESS"){
            $isreprocess = 1;
        }
        $InsertData = array('productprocessid' => $mainbatchprocessid,
                            'parentproductprocessid' => $parentproductprocessid,
                            'processgroupmappingid' => $processgroupmappingid,
                            'batchno' => $batchno,
                            'transactiondate' => $transactiondate,
                            'estimatedate' => $estimatedate,
                            'processbymemberid' => $processbymemberid,
                            'vendorid' => $vendorid,
                            'machineid' => $machineid,
                            'orderid' => $orderid,
                            'comments' => $comments,
                            'isreprocess' => $isreprocess,
                            'type' => $type,
                            'processstatus' => 0,
                            'createddate' => $createddate,
                            'addedby' => $addedby,                              
                            'modifieddate' => $createddate,                             
                            'modifiedby' => $addedby 
                        );
        // print_r($InsertData); exit;
        $ProductProcessID = $this->Product_process->Add($InsertData);
        // $ProductProcessID = 1;
        if($ProductProcessID){
            
            if(($processtype == "IN" && !empty($PostData['inproductid']) || ($processtype != "IN" && !empty($PostData['outproductid'])))){
                $this->load->model('Extra_charges_model', 'Extra_charges');
                $insertprocessdetaildata = $insertprocesscertificates = $insertOptionValueData = $insertstockdata = array();
                $productidarray = ($processtype == "IN"?$PostData['inproductid']:$PostData['outproductid']);
                $productvariantid = ($processtype == "IN"?$PostData['inproductvariantid']:$PostData['outproductvariantid']);
                $quantity = ($processtype == "IN"?$PostData['inquantity']:$PostData['quantity']);
                $pendingquantityArr = ($processtype == "IN"?$PostData['inpendingquantity']:"");
                $laborcostArr = ($processtype == "IN"?$PostData['inlaborcost']:"");
                $productprice = ($processtype == "IN"?$PostData['inprice']:$PostData['outprice']);
                
                $calculatelandingcost = 0;
                if(STOCK_MANAGE_BY == 1 && $processtype == "IN"){
                    $productprocessdata = $this->Product_process->getProductProcessDetailsById($parentproductprocessid);
                    if(!empty($productprocessdata['outproducts'])){
                        foreach($productprocessdata['outproducts'] as $op){ 
                            $calculatelandingcost += $op['price']*$op['quantity'];
                        }
                    }
                    if(!empty($productprocessdata['outextracharges'])){
                        foreach($productprocessdata['outextracharges'] as $oc){ 
                            $calculatelandingcost += $oc['amount'];
                        }
                    }
                    if(!empty($productidarray)){
                        foreach($productidarray as $c=>$productid){
                            if($productid > 0 && $productvariantid[$c] > 0 && ($processtype == "IN" || $quantity[$c] > 0)){
                                $calculatelandingcost += $productprice[$c]*$quantity[$c];
                            }
                        }
                    }
                    if(!empty($extrachargesidarr)){
                        foreach($extrachargesidarr as $index=>$extrachargesid){
        
                            if($extrachargesid > 0){
                                $calculatelandingcost += $extrachargeamountarr[$index];
                            }
                        }
                    }
                    $calculatelandingcost = $calculatelandingcost / count($productidarray);
                }
                if(!empty($productidarray)){
                    foreach($productidarray as $k=>$productid){
                        
                        if($productid > 0 && $productvariantid[$k] > 0 && ($processtype == "IN" || $quantity[$k] > 0)){
                            if($processtype == "IN"){
                                $issupportingproduct = 0;
                                $isfinalproduct = (isset($PostData['finalproduct'.($k+1)])?1:0);
                                $unit = "";
                                $pendingquantity = $pendingquantityArr[$k];
                                $laborcost = $laborcostArr[$k];
                                $landingcost = 0;
                                if($isfinalproduct==1){
                                    $landingcost = ($quantity[$k]>0)?($calculatelandingcost / $quantity[$k]):0;
                                }
                            }else{
                                $issupportingproduct = (isset($PostData['outproductadditional'.($k+1)])?1:0);
                                $isfinalproduct = 0;
                                $unit = $PostData['unitid'][$k];
                                $pendingquantity = 0;
                                $laborcost = 0;
                                $landingcost = 0;
                            }
                            $insertprocessdetaildata = array('productprocessid' => $ProductProcessID,
                                    'productpriceid' => $productvariantid[$k],
                                    'unit' => $unit,
                                    'quantity' => $quantity[$k],
                                    'pendingquantity' => $pendingquantity,
                                    'price' => $productprice[$k],
                                    'issupportingproduct' => $issupportingproduct,                        
                                    'isfinalproduct' => $isfinalproduct,
                                    'laborcost' => $laborcost,
                                    'landingcost' => $landingcost
                            );

                            $this->Product_process->_table = tbl_productprocessdetails;
                            $productprocessdetailsid = $this->Product_process->Add($insertprocessdetaildata);

                            if($productprocessdetailsid){
                                if(STOCK_MANAGE_BY == 1){
                                    if($processtype != "IN"){
                                      
                                        $orderproducts = json_decode($PostData['finalorderproducts'][$k],true);
        
                                        if(!empty($orderproducts)){
                                            foreach($orderproducts as $op){
        
                                                $insertstockdata[] = array('referencetype' => 0,
                                                        'referenceid' => $productprocessdetailsid,
                                                        'orderproductsid' => $op['orderproductsid'],
                                                        'qty' => $op['qty']
                                                );
                                            }
                                        }
        
                                    }else{
                                        $orderproductsidarr = !empty($PostData['orderproductsid'][$k+1])?$PostData['orderproductsid'][$k+1]:"";
    
                                        if(!empty($orderproductsidarr)){
                                            foreach($orderproductsidarr as $s=>$orderproductsid){
                                                $qty = !empty($PostData['stockqty'][$k+1][$s])?$PostData['stockqty'][$k+1][$s]:"";
    
                                                if(!empty($qty)){
                                                    $insertstockdata[] = array('referencetype' => 0,
                                                        'referenceid' => $productprocessdetailsid,
                                                        'orderproductsid' => $orderproductsid,
                                                        'qty' => $qty
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                if(STOCK_MANAGE_BY == 1 && !empty($insertstockdata)){
                    $this->Product_process->_table = tbl_transactionproductstockmapping;
                    $this->Product_process->add_batch($insertstockdata);
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
                                 
                                $file = "";
                                if(strpos($key, 'docfile') !== false && $_FILES['docfile'.$id]['name']!=''){
                                    $file = uploadFile('docfile'.$id, 'PRODUCT_PROCESS_CERTIFICATE', PRODUCT_PROCESS_CERTIFICATE_PATH, '*', '', 1, PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH);
    
                                    if($file == 0 && $file == 2){
                                        $file = "";
                                    } 
                                }
                                if($docno[$id]!="" && !empty($doctitle[$id]) && !empty($file)){

                                    $insertprocesscertificates[] = array('productprocessid' => $ProductProcessID,
                                            'docno' => $docno[$id],
                                            'documentdate' => ($docdate[$id]!=""?$this->general_model->convertdate($docdate[$id]):""),
                                            'title' => $doctitle[$id],
                                            'remarks' => $docdescription[$id],                        
                                            'filename' => $file
                                    );
                                }
                            }
                        }
                        if(count($insertprocesscertificates) > 0){
                            $this->Product_process->_table = tbl_productprocesscertificates;
                            $this->Product_process->add_batch($insertprocesscertificates);
                        }
                    }
                    /* $optionidarray = $PostData['optionid'];
                    $optionvalue = $PostData['optionvalue'];
                    
                    if(!empty($optionidarray)){
                        foreach($optionidarray as $i=>$processoptionid){
                            
    
                            $insertOptionData = array('productprocessid'=>$ProductProcessID,
                                                    'processoptionid'=>$processoptionid
                                                    );
    
                            $this->Product_process->_table = tbl_productprocessoption;
                            $productprocessoptionid = $this->Product_process->Add($insertOptionData);
                            if($productprocessoptionid){
    
                                $insertOptionValueData[] = array('productprocessoptionid'=>$productprocessoptionid,
                                                                'value'=>$optionvalue[$i]
                                                            );
    
                            }
                            
                        }
                    } */
                    $outproductprocessdetailidarray = (isset($PostData['outproductprocessdetailid']))?$PostData['outproductprocessdetailid']:"";
                    $optionidarray = (isset($PostData['optionid']))?$PostData['optionid']:"";
                    $optionvalue = (isset($PostData['optionvalue']))?$PostData['optionvalue']:"";
                    $unitid = (isset($PostData['unitid']))?$PostData['unitid']:"";
    
                    if(!empty($outproductprocessdetailidarray) && !empty($optionidarray)){
                        foreach($outproductprocessdetailidarray as $productprocessdetailid){
    
                            if(!empty($optionidarray[$productprocessdetailid])){
                                foreach($optionidarray[$productprocessdetailid] as $i=>$processoptionid){
                                    if(!empty($optionvalue[$productprocessdetailid][$i])){
                                        
                                        $insertOptionData = array('productprocessdetailsid'=>$productprocessdetailid,
                                                                'unitid'=>$unitid[$productprocessdetailid][$i],
                                                                'processoptionid'=>$processoptionid
                                                                );
                        
                                        $this->Product_process->_table = tbl_productprocessoption;
                                        $productprocessoptionid = $this->Product_process->Add($insertOptionData);
                                        if($productprocessoptionid){
                        
                                            $insertOptionValueData[] = array('productprocessoptionid'=>$productprocessoptionid,
                                                                            'value'=>$optionvalue[$productprocessdetailid][$i]
                                                                        );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(!empty($insertOptionValueData)){
                        $this->Product_process->_table = tbl_productprocessoptionvalue;
                        $this->Product_process->add_batch($insertOptionValueData);
                    }
                }
                if(!empty($extrachargesidarr)){
                    $insertextracharges = array();
                    foreach($extrachargesidarr as $index=>$extrachargesid){
    
                        if($extrachargesid > 0){
                            $extrachargesname = trim($extrachargesnamearr[$index]);
                            $extrachargestax = trim($extrachargestaxarr[$index]);
                            $extrachargeamount = trim($extrachargeamountarr[$index]);
                            $extrachargepercentage = trim($extrachargepercentagearr[$index]);
    
                            if($extrachargeamount > 0){
    
                                $insertextracharges[] = array("type"=>4,
                                                        "referenceid" => $ProductProcessID,
                                                        "extrachargesid" => $extrachargesid,
                                                        "extrachargesname" => $extrachargesname,
                                                        "extrachargepercentage" => $extrachargepercentage,
                                                        "taxamount" => $extrachargestax,
                                                        "amount" => $extrachargeamount,
                                                        "createddate" => $createddate,
                                                        "addedby" => $addedby
                                                    );
                            }
                        }
                    }
                    if(!empty($insertextracharges)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->add_batch($insertextracharges);
                    }
                }
            }

            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Product_process->_table = tbl_processgroupmapping;
                $this->Product_process->_fields = "(SELECT name FROM ".tbl_process." WHERE id=processid) as process";
                $this->Product_process->_where = array("id"=>$processgroupmappingid);
                $data = $this->Product_process->getRecordsById(); 

                $this->general_model->addActionLog(1,'Product Process','Add new '.$data['process'].' product process.');
            }

            $productionplanqtydetailarray = array();
            if(!empty($productionplanqtydetail)){
                foreach($productionplanqtydetail as $row){

                    $productionplanqtydetailarray[] = array(
                        "productprocessid"=>$ProductProcessID,
                        "productionplandetailid"=>$row['productionplandetailid'],
                        "productid"=>$row['productid'],
                        "priceid"=>$row['priceid'],
                        "quantity"=>$row['quantity']
                    );
                }
            }
            if(!empty($productionplanqtydetailarray)){
                $this->Product_process->_table = tbl_productionplanqtydetail;
                $this->Product_process->add_batch($productionplanqtydetailarray);
            }
    
            $json = array('error'=>1); // Process group inserted successfully.
        } else {
            $json = array('error'=>0); // Process group not inserted.
        }
        
        echo json_encode($json);
    }
    public function update_product_process() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $json = array();
        
        $pgid = $PostData['postprocessgroupid'];
        $processtype = $PostData['processtype'];
        $productprocessid = $PostData['productprocessid'][$pgid];
        $processgroupmappingid = $PostData['processgroupmappingid'][$pgid];
        $batchno = $PostData['batchno'][$pgid];
        $mainbatchprocessid = $PostData['mainbatchprocessid'][$pgid];
        // $processid = trim($PostData['processid']);
        $transactiondate = $this->general_model->convertdate($PostData['transactiondate'][$pgid]);
        $comments = $PostData['remarks'][$pgid];
        $olddocfilearray = isset($PostData['olddocfile'])?$PostData['olddocfile']:"";

        if($processtype == "IN"){
            $processbymemberid = $PostData['processbymemberid'][$pgid];
            $vendorid = $PostData['postvendorid'][$pgid];
            $machineid = $PostData['postmachineid'][$pgid];
            $estimatedate = !empty($PostData['postestimatedate'][$pgid])?$this->general_model->convertdate($PostData['postestimatedate'][$pgid]):"";
            $orderid = $PostData['postorderid'][$pgid];

            $type = 1;

            if(!is_dir(PRODUCT_PROCESS_CERTIFICATE_PATH)){
                @mkdir(PRODUCT_PROCESS_CERTIFICATE_PATH);
            }
    
            if(!empty($_FILES)){
                        
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    
                    if(strpos($key, 'docfile') !== false){
                        if($_FILES['docfile'.$id]['name']!=''){
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
            }   
            if(!empty($PostData['inextrachargesid'][$pgid])){
                $extrachargemappingidarr = (isset($PostData['inextrachargemappingid'][$pgid]))?$PostData['inextrachargemappingid'][$pgid]:'';
                $extrachargesidarr = (isset($PostData['inextrachargesid'][$pgid]))?$PostData['inextrachargesid'][$pgid]:'';
                $extrachargestaxarr = (isset($PostData['inextrachargestax'][$pgid]))?$PostData['inextrachargestax'][$pgid]:'';
                $extrachargeamountarr = (isset($PostData['inextrachargeamount'][$pgid]))?$PostData['inextrachargeamount'][$pgid]:'';
                $extrachargesnamearr = (isset($PostData['inextrachargesname'][$pgid]))?$PostData['inextrachargesname'][$pgid]:'';
                $extrachargepercentagearr = (isset($PostData['inextrachargepercentage'][$pgid]))?$PostData['inextrachargepercentage'][$pgid]:'';
            }
        }else{
            /* if($processtype == "REPROCESS"){
                $processbymemberid = $PostData['processbymemberid'];
            }else{
            } */
            $processbymemberid = $PostData['processedby'][$pgid];
            $vendorid = ($PostData['processedby'][$pgid]==0 && isset($PostData['vendorid'][$pgid]))?$PostData['vendorid'][$pgid]:0;
            $machineid = ($PostData['processedby'][$pgid]==1 && isset($PostData['machineid'][$pgid]))?$PostData['machineid'][$pgid]:0;
            $estimatedate = !empty($PostData['estimatedate'][$pgid])?$this->general_model->convertdate($PostData['estimatedate'][$pgid]):"";
            $orderid = $PostData['orderid'][$pgid];
            $type = 0;
            
            if(!empty($PostData['outextrachargesid'][$pgid])){
                $extrachargemappingidarr = (isset($PostData['outextrachargemappingid'][$pgid]))?$PostData['outextrachargemappingid'][$pgid]:'';
                $extrachargesidarr = (isset($PostData['outextrachargesid'][$pgid]))?$PostData['outextrachargesid'][$pgid]:'';
                $extrachargestaxarr = (isset($PostData['outextrachargestax'][$pgid]))?$PostData['outextrachargestax'][$pgid]:'';
                $extrachargeamountarr = (isset($PostData['outextrachargeamount'][$pgid]))?$PostData['outextrachargeamount'][$pgid]:'';
                $extrachargesnamearr = (isset($PostData['outextrachargesname'][$pgid]))?$PostData['outextrachargesname'][$pgid]:'';
                $extrachargepercentagearr = (isset($PostData['outextrachargepercentage'][$pgid]))?$PostData['outextrachargepercentage'][$pgid]:'';
            }

            if(START_PROCESS_WITHOUT_STOCK==0 && STOCKMANAGEMENT==1){
                $this->load->model('Stock_report_model', 'Stock');
                if(!empty($PostData['outproductid'][$pgid])){
                    $this->load->model('Stock_report_model', 'Stock');
                    foreach($PostData['outproductid'][$pgid] as $s=>$stockproductid){
                        if($stockproductid > 0 && $PostData['outproductvariantid'][$pgid][$s] > 0 && $PostData['quantity'][$pgid][$s] > 0){
                            
                            $keynm = 'overallclosingstock';
                            if(STOCK_MANAGE_BY==0){
                                $ProductStock = $this->Stock->getAdminProductStock($stockproductid,1);
                            }else{
                                $ProductStock = $this->Stock->getAdminProductFIFOStock($stockproductid,1);
                            }
                            $availablestock = 0;
                            if(!empty($ProductStock)){
                                $key = array_search($PostData['outproductvariantid'][$pgid][$s], array_column($ProductStock, 'priceid'));
                                if(trim($key)!="" && isset($ProductStock[$key][$keynm])){
                                    $availablestock = (int)$ProductStock[$key][$keynm];
                                }
                            }
                            
                            if($PostData['quantity'][$pgid][$s] > $availablestock){
                                //Quantity greater than stock quantity.
                                echo json_encode(array("error"=>-3,"message"=>"Quantity greater than stock quantity of ".($s+1)." out product !")); exit;
                            }
                        }
                    }
                }
            }
        }
        
        $updateData = array('processgroupmappingid' => $processgroupmappingid,
                            'batchno' => $batchno,
                            'transactiondate' => $transactiondate,
                            'estimatedate' => $estimatedate,
                            'processbymemberid' => $processbymemberid,
                            'vendorid' => $vendorid,
                            'machineid' => $machineid,
                            'orderid' => $orderid,
                            'comments' => $comments,
                            'modifieddate' => $modifieddate,                             
                            'modifiedby' => $modifiedby 
                        );
    
        $this->Product_process->_where = array('id' =>$productprocessid);
        $isUpdated = $this->Product_process->Edit($updateData);
        
        if($isUpdated){
            if(($processtype == "IN" && !empty($PostData['inproductid'][$pgid]) || ($processtype != "IN" && !empty($PostData['outproductid'][$pgid])))){
                $this->load->model('Extra_charges_model', 'Extra_charges');
                
                if(isset($PostData['removeproductprocessdetailid']) && $PostData['removeproductprocessdetailid']!=''){
                    $query=$this->readdb->select("id")
                                    ->from(tbl_productprocessdetails)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeproductprocessdetailid'])))."')>0")
                                    ->get();
                    $ProcessDetailData = $query->result_array();
                    
                    if(!empty($ProcessDetailData)){
                        foreach ($ProcessDetailData as $row) {
                            
                            $this->Product_process->_table = tbl_transactionproductstockmapping;
                            $this->Product_process->Delete(array('referencetype'=>0,'referenceid'=>$row['id']));

                            $this->Product_process->_table = tbl_productprocessdetails;
                            $this->Product_process->Delete(array('id'=>$row['id']));
                            
                        }
                    }
                } 
                if(isset($PostData['removeextrachargemappingid']) && $PostData['removeextrachargemappingid']!=''){
                        
                    $query=$this->readdb->select("id")
                                    ->from(tbl_extrachargemapping)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeextrachargemappingid'])))."')>0")
                                    ->get();
                    $MappingData = $query->result_array();

                    if(!empty($MappingData)){
                        foreach ($MappingData as $row) {

                            $this->Extra_charges->_table = tbl_extrachargemapping;
                            $this->Extra_charges->Delete("id=".$row['id']);
                        }
                    }
                }
                $insertprocessdetaildata = $insertprocesscertificates = $insertOptionValueData = array();
                $updateprocessdetaildata = $updateprocesscertificates = $updateOptionData = $updateOptionValueData = array();

                $productprocessdetailidarray = isset($PostData['productprocessdetailid'][$pgid])?$PostData['productprocessdetailid'][$pgid]:'';
                $productidarray = ($processtype == "IN"?$PostData['inproductid'][$pgid]:$PostData['outproductid'][$pgid]);
                $productvariantid = ($processtype == "IN"?$PostData['inproductvariantid'][$pgid]:$PostData['outproductvariantid'][$pgid]);
                $quantity = ($processtype == "IN"?$PostData['inquantity'][$pgid]:$PostData['quantity'][$pgid]);
                $pendingquantityArr = ($processtype == "IN"?$PostData['inpendingquantity'][$pgid]:"");
                $laborcostArr = ($processtype == "IN"?$PostData['inlaborcost'][$pgid]:"");
                $productprice = ($processtype == "IN"?$PostData['inprice'][$pgid]:$PostData['outprice'][$pgid]);

                $total_out_product_amount = $total_out_extra_charges_amount = 0;
                if($processtype == "IN"){
                    $planningqtyarray = $PostData['planningqty'][$pgid];

                    $this->Product_process->_table = tbl_productprocess;
                    $productprocessdata = $this->Product_process->getProductProcessDetailsById($mainbatchprocessid);
                    if(!empty($productprocessdata['outproducts'])){
                        foreach($productprocessdata['outproducts'] as $op){ 
                            $total_out_product_amount += $op['price']*$op['quantity'];
                        }
                    }
                    if(!empty($productprocessdata['outextracharges'])){
                        foreach($productprocessdata['outextracharges'] as $oc){ 
                            if($oc['chargetype'] == 0){
                                $amount = $oc['amount'];
                            }else{
                                $amount = $oc['amount'] * array_sum(array_column($productprocessdata['outproducts'], 'quantity'));
                            } 
                            $total_out_extra_charges_amount += $amount;
                        }
                    }
                    $totalinextracharges =0;
                    if(!empty($extrachargesidarr)){
                        foreach($extrachargesidarr as $index=>$extrachargesid){
                            if($extrachargesid > 0){
                                $totalinextracharges += $extrachargeamountarr[$index];
                            }
                        }
                    }
                    $countproducts = 0;
                    if(!empty($productidarray)){
                        foreach($productidarray as $c=>$productid){
                            if($productid > 0 && $productvariantid[$c] > 0 && ($processtype == "IN" || $quantity[$c] > 0)){
                                $countproducts++;
                            }
                        }
                    }
                    $totalexrachargeofproduct = $totalinextracharges / $countproducts;
                }
                if(!empty($productidarray)){
                    foreach($productidarray as $k=>$productid){
                        
                        if($productid > 0 && $productvariantid[$k] > 0 && ($processtype == "IN" || $quantity[$k] > 0)){

                            if($processtype == "IN"){
                                $issupportingproduct = 0;
                                $isfinalproduct = (isset($PostData['finalproduct_'.$pgid.'_'.($k+1)])?1:0);
                                $unit = "";
                                $pendingquantity = $pendingquantityArr[$k];
                                $laborcost = $laborcostArr[$k];

                                $landing_per_piece = $net_landed_cost_price = 0;
                                /* $landingcost = 0;
                                if($isfinalproduct==1){
                                    $landingcost = ($quantity[$k]>0)?($calculatelandingcost / $quantity[$k]):0;
                                } */

                                /*$landingcostamount = $calculatelandingcost + ($quantity[$k]*$laborcost);
                                        
                                $total = $landingcostamount + $totalexrachargeofproduct;
                                
                                $landingcostprice = $total / $quantity[$k];*/

                                $inqty = $quantity[$k];

                                /* if(!empty($productprocessdata['outproducts'])){
                                    $total_oc_amount_perproduct = $calculatelandingcost = 0;
                                    if(count($productprocessdata['outextracharges']) > 0){
                                        $total_oc_amount_perproduct = array_sum(array_column($productprocessdata['outextracharges'],"amount")) / count($productprocessdata['outproducts']);
                                    }
                                    foreach($productprocessdata['outproducts'] as $_op){ 
                                        $finalqty = $_op['quantity'] - $_op['scrapqty'];
                                        if($isfinalproduct==1 || $_op['productpriceid'] == $productvariantid[$k]){
                                            if($inqty > 0){
                                                if($inqty > $finalqty){
                                                    $calculatelandingcost += ($_op['price'] * $finalqty) + $total_oc_amount_perproduct;
                                                    $inqty -= $finalqty;
            
                                                }else if($inqty <= $finalqty){
            
                                                    $calculatelandingcost += ($_op['price'] * $inqty) + $total_oc_amount_perproduct;
                                                    $inqty = 0; 
            
                                                }
                                            }
                                        }
                                    }
                                    if($calculatelandingcost > 0){
                                        $landingcost = $calculatelandingcost / $quantity[$k] + $laborcost;
                                        $landingcostamount = $calculatelandingcost + ($quantity[$k]*$laborcost);
                                        
                                        $total = $landingcostamount + $totalexrachargeofproduct;
                                        
                                        $landingcostprice = $total / $quantity[$k];
                                    }
                                } */

                                $plan_qty = !empty($planningqtyarray[$k])?$planningqtyarray[$k]:0;

                                if($plan_qty!=0){
                                    $per_piece_extra_charge = $total_out_extra_charges_amount / $plan_qty;
                                    $per_piece_out_price = $total_out_product_amount / $plan_qty + $per_piece_extra_charge;

                                    $landing_per_piece = $laborcost + $per_piece_out_price;
                                    $total_landing_cost = $landing_per_piece * $inqty;

                                    $total = $total_landing_cost + $totalexrachargeofproduct;

                                    $net_landed_cost_price = $total / $inqty;
                                }

                            }else{
                                $issupportingproduct = (isset($PostData['outproductadditional'.($k+1)])?1:0);
                                $isfinalproduct = 0;
                                $unit = $PostData['unitid'][$pgid][$k];
                                $pendingquantity = $laborcost = $landing_per_piece = $net_landed_cost_price = 0;
                            }
        
                            $productprocessdetailid = (isset($productprocessdetailidarray[$k]) && !empty($productprocessdetailidarray[$k]))?$productprocessdetailidarray[$k]:"";
        
                            if($productprocessdetailid!=""){
                            
                                $updateprocessdetaildata[] = array('id' => $productprocessdetailid,
                                        'productpriceid' => $productvariantid[$k],
                                        'unit' => $unit,
                                        'quantity' => $quantity[$k],
                                        'pendingquantity' => $pendingquantity,
                                        'price' => $productprice[$k],
                                        'issupportingproduct' => $issupportingproduct,                        
                                        'isfinalproduct' => $isfinalproduct,
                                        'laborcost' => $laborcost,
                                        'landingcost' => $net_landed_cost_price,
                                        'landingcostperpiece' => $landing_per_piece
                                );

                                if($processtype != "IN"){
                                    $postorderproductsdata = json_decode($PostData['finalorderproducts'][$pgid][$k],true);
                                   
                                    $this->Product_process->_table = tbl_transactionproductstockmapping;
                                    $this->Product_process->Delete(array("referencetype"=>0,"referenceid"=>$productprocessdetailid));
                                    
                                    if(!empty($postorderproductsdata)){

                                        foreach($postorderproductsdata as $op){
                                            $this->Product_process->_table = tbl_transactionproductstockmapping;
                                            $this->Product_process->_where = array("referencetype"=>0,"referenceid"=>$productprocessdetailid,"stocktype"=>$op['stocktype'],"stocktypeid"=>$op['stocktypeid']);
                                            $Count = $this->Product_process->getRecordsById();

                                            if(!empty($Count)){

                                                $updatestockdata = array(
                                                    "id"=>$Count['id'],
                                                    'qty' => $op['qty']);
                                                $this->Product_process->Edit($updatestockdata, "id");
                                            }else{

                                                $stocktypeid = !empty($op['stocktypeid'])?$op['stocktypeid']:$productprocessdetailid;

                                                $insertstockdata = array("referencetype"=>0,
                                                    "referenceid"=>$productprocessdetailid,
                                                    "stocktype"=>$op['stocktype'],
                                                    "stocktypeid"=>$stocktypeid,
                                                    "productid"=>$productid,
                                                    "priceid"=>$productvariantid[$k],
                                                    "qty"=>$op['qty'],
                                                    "action"=>1,
                                                    "createddate"=>$modifieddate
                                                );
                                                $this->Product_process->Add($insertstockdata);
                                            }
                                        }
                                    }else{
                                        $insertstockdata = array("referencetype"=>0,
                                                    "referenceid"=>$productprocessdetailid,
                                                    "stocktype"=>1,
                                                    "stocktypeid"=>$productprocessdetailid,
                                                    "productid"=>$productid,
                                                    "priceid"=>$productvariantid[$k],
                                                    "qty"=>$quantity[$k],
                                                    "action"=>1,
                                                    "createddate"=>$modifieddate
                                                );

                                        $this->Product_process->Add($insertstockdata);
                                    }
    
                                }else{
                                    $stocktypeidarr = !empty($PostData['stocktypeid'][$pgid][$k+1])?$PostData['stocktypeid'][$pgid][$k+1]:"";
                                    if(!empty($stocktypeidarr)){
                                        $deletestockmapping = $updatestockqtydata = array();
                                        foreach($stocktypeidarr as $s=>$stocktypeid){
                                            $qty = !empty($PostData['stockqty'][$pgid][$k+1][$s])?$PostData['stockqty'][$pgid][$k+1][$s]:"";
                                            $stockmappingid = !empty($PostData['stockmappingid'][$pgid][$k+1][$s])?$PostData['stockmappingid'][$pgid][$k+1][$s]:"";
                                            
                                            if(!empty($stockmappingid)){
                                                if(!empty($qty)){
                                                    $updatestockqtydata[] = array(
                                                        'id' => $stockmappingid,
                                                        'qty' => $qty
                                                    );
                                                }else{
                                                    $deletestockmapping[] = $stockmappingid;
                                                }
                                            }
                                        }
                                        if(count($updatestockqtydata) > 0){
                                            $this->Product_process->_table = tbl_transactionproductstockmapping;
                                            $this->Product_process->edit_batch($updatestockqtydata, "id");
                                        }
                                        if(!empty($deletestockmapping)){
                                            $this->Product_process->_table = tbl_transactionproductstockmapping;
                                            $this->Product_process->Delete(array("id IN (".implode(",",$deletestockmapping).")"=>null));
                                        }
                                    }else{
                                        $insertstockdata = array("referencetype"=>0,
                                                    "referenceid"=>$productprocessdetailid,
                                                    "stocktype"=>1,
                                                    "stocktypeid"=>$productprocessdetailid,
                                                    "productid"=>$productid,
                                                    "priceid"=>$productvariantid[$k],
                                                    "qty"=>$quantity[$k],
                                                    "action"=>1,
                                                    "createddate"=>$modifieddate
                                                );
                                        $this->Product_process->Add($insertstockdata);
                                    }
                                }

                            }else{
        
                                $insertprocessdetaildata = array(
                                        'productprocessid' => $productprocessid,
                                        'productpriceid' => $productvariantid[$k],
                                        'unit' => $unit,
                                        'quantity' => $quantity[$k],
                                        'pendingquantity' => $pendingquantity,
                                        'price' => $productprice[$k],
                                        'issupportingproduct' => $issupportingproduct,                        
                                        'isfinalproduct' => $isfinalproduct,
                                        'laborcost' => $laborcost,
                                        'landingcost' => $net_landed_cost_price,
                                        'landingcostperpiece' => $landing_per_piece
                                );

                                $this->Product_process->_table = tbl_productprocessdetails;
                                $productprocessdetailid = $this->Product_process->Add($insertprocessdetaildata);

                                if($processtype != "IN"/*  && STOCK_MANAGE_BY == 1 */){
                              
                                    $orderproducts = json_decode($PostData['finalorderproducts'][$pgid][$k],true);
    
                                    $this->Product_process->_table = tbl_transactionproductstockmapping;
                                    if(!empty($orderproducts)){
                                        foreach($orderproducts as $op){
    
                                            $stocktype = !empty($op['stocktype'])?$op['stocktype']:"";
                                            $stocktypeid = !empty($op['stocktypeid'])?$op['stocktypeid']:$productprocessdetailid;

                                            $insertstockdata = array("referencetype"=>0,
                                                    "referenceid"=>$productprocessdetailid,
                                                    "stocktype"=>$stocktype,
                                                    "stocktypeid"=>$stocktypeid,
                                                    "productid"=>$productid,
                                                    "priceid"=>$productvariantid[$k],
                                                    "qty"=>$op['qty'],
                                                    "action"=>1,
                                                    "createddate"=>$transactiondate,
                                                    "modifieddate"=>$modifieddate
                                                );
                                                
                                            $this->Product_process->Add($insertstockdata);
                                        }
                                    }
    
                                }else{
                                    $insertstockdata = array("referencetype"=>0,
                                                            "referenceid"=>$productprocessdetailid,
                                                            "stocktype"=>1,
                                                            "stocktypeid"=>$productprocessdetailid,
                                                            "productid"=>$productid,
                                                            "priceid"=>$productvariantid[$k],
                                                            "qty"=>$quantity[$k],
                                                            "action"=>0,
                                                            "createddate"=>$modifieddate
                                                        );

                                    $this->Product_process->Add($insertstockdata);
                                }
                            }
                        }
                    }
                }
                
                if(count($updateprocessdetaildata) > 0){
                    $this->Product_process->_table = tbl_productprocessdetails;
                    $this->Product_process->edit_batch($updateprocessdetaildata,"id");
                }
                if($processtype == "IN"){
                    if(isset($PostData['removeproductprocesscertificatesid']) && $PostData['removeproductprocesscertificatesid']!=''){
                        $query=$this->readdb->select("id,filename")
                                        ->from(tbl_productprocesscertificates)
                                        ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeproductprocesscertificatesid'])))."')>0")
                                        ->get();
                        $CertificateData = $query->result_array();
                        
                        if(!empty($CertificateData)){
                            foreach ($CertificateData as $row) {
                                
                                unlinkfile("PRODUCT_PROCESS_CERTIFICATE",$row['filename'],PRODUCT_PROCESS_CERTIFICATE_PATH);
                                $this->Product_process->_table = tbl_productprocesscertificates;
                                $this->Product_process->Delete(array('id'=>$row['id']));
                                
                            }
                        }
                    } 

                    $isCertificate = $PostData['isCertificate'];
                    if($isCertificate!=0){
                        
                        $doctitle = $PostData['doctitle'];
                        $docno = $PostData['docno'];
                        $docdescription = $PostData['docdescription'];
                        $docdate = $PostData['docdate'];
                        $productprocesscertificatesidarray = isset($PostData['productprocesscertificatesid'])?$PostData['productprocesscertificatesid']:'';

                        if(!empty($_FILES)){
                            
                            foreach ($_FILES as $key => $value) {
                                $id = preg_replace('/[^0-9]/', '', $key);
                            
                                if(strpos($key, 'docfile') !== false){

                                    $productprocesscertificatesid = (isset($productprocesscertificatesidarray[$id]) && !empty($productprocesscertificatesidarray[$id]))?$productprocesscertificatesidarray[$id]:"";

                                    if($productprocesscertificatesid!=""){
                                        

                                        if($_FILES['docfile'.$id]['name']!='' && $olddocfilearray[$id]==""){
                                        
                                            $file = uploadFile('docfile'.$id, 'PRODUCT_PROCESS_CERTIFICATE', PRODUCT_PROCESS_CERTIFICATE_PATH, '*', '', 1, PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH);
                                            
                                            if($file == 0 && $file == 2){
                                                $file = "";
                                            } 
                                            if($docno[$id]!="" && !empty($doctitle[$id]) && !empty($file)){
                                                $insertprocesscertificates[] = array('productprocessid' => $productprocessid,
                                                    'docno' => $docno[$id],
                                                    'documentdate' => ($docdate[$id]!=""?$this->general_model->convertdate($docdate[$id]):""),
                                                    'title' => $doctitle[$id],
                                                    'remarks' => $docdescription[$id],                        
                                                    'filename' => $file
                                                );
                                            }
                                        }else if(($_FILES['docfile'.$id]['name']!='' || $_FILES['docfile'.$id]['name']=='') && $olddocfilearray[$id]!=""){
                                            $file = $olddocfilearray[$id];
                                            if($_FILES['docfile'.$id]['name']!=''){

                                                $file = reuploadFile('docfile'.$id, 'PRODUCT_PROCESS_CERTIFICATE', $olddocfilearray[$id], PRODUCT_PROCESS_CERTIFICATE_PATH, '*', '', 1, PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH);
        
                                                if($file == 0 && $file == 2){
                                                    $file = "";
                                                } 
                                            }
                                            
                                            $updateprocesscertificates[] = array('id' => $productprocesscertificatesid,
                                                'docno' => $docno[$id],
                                                'documentdate' => ($docdate[$id]!=""?$this->general_model->convertdate($docdate[$id]):""),
                                                'title' => $doctitle[$id],
                                                'remarks' => $docdescription[$id],                        
                                                'filename' => $file
                                            );
                                        } 
                                    }else{
                                        $file = "";
                                        if($_FILES['docfile'.$id]['name']!=''){

                                            $file = uploadFile('docfile'.$id, 'PRODUCT_PROCESS_CERTIFICATE', PRODUCT_PROCESS_CERTIFICATE_PATH, '*', '', 1, PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH);
                                            
                                            if($file == 0 && $file == 2){
                                                $file = "";
                                            } 
                                        }
                                        if($docno[$id]!="" && !empty($doctitle[$id]) && !empty($file)){
                                            $insertprocesscertificates[] = array('productprocessid' => $productprocessid,
                                                'docno' => $docno[$id],
                                                'documentdate' => ($docdate[$id]!=""?$this->general_model->convertdate($docdate[$id]):""),
                                                'title' => $doctitle[$id],
                                                'remarks' => $docdescription[$id],                        
                                                'filename' => $file
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    
                        if(count($insertprocesscertificates) > 0){
                            $this->Product_process->_table = tbl_productprocesscertificates;
                            $this->Product_process->add_batch($insertprocesscertificates);
                        }
                        if(count($updateprocesscertificates) > 0){
                            $this->Product_process->_table = tbl_productprocesscertificates;
                            $this->Product_process->edit_batch($updateprocesscertificates, "id");
                        }
                    }
                    /* $optionidarray = $PostData['optionid'];
                    $optionvalue = $PostData['optionvalue'];

                    $productprocessoptionidarray = isset($PostData['productprocessoptionid'])?$PostData['productprocessoptionid']:'';

                    if(!empty($optionidarray)){
                        foreach($optionidarray as $i=>$processoptionid){

                            $productprocessoptionid = (isset($productprocessoptionidarray[$i]) && !empty($productprocessoptionidarray[$i]))?$productprocessoptionidarray[$i]:"";

                            if($productprocessoptionid != ""){

                                $updateOptionValueData[] = array('productprocessoptionid'=>$productprocessoptionid,
                                                                'value'=>$optionvalue[$i]
                                );
                            }else{

                                $insertOptionData = array('productprocessid'=>$productprocessid,
                                                    'processoptionid'=>$processoptionid
                                                    );

                                $this->Product_process->_table = tbl_productprocessoption;
                                $productprocessoptionid = $this->Product_process->Add($insertOptionData);
                                if($productprocessoptionid){

                                    $insertOptionValueData[] = array('productprocessoptionid'=>$productprocessoptionid,
                                                                    'value'=>$optionvalue[$i]
                                                                );

                                }
                            }

                            
                        }
                    } */
                    /* $outproductprocessdetailidarray = (isset($PostData['outproductprocessdetailid'][$pgid]))?$PostData['outproductprocessdetailid'][$pgid]:"";
                    $optionidarray = (isset($PostData['optionid'][$pgid]))?$PostData['optionid'][$pgid]:"";
                    $optionvalue = (isset($PostData['optionvalue'][$pgid]))?$PostData['optionvalue'][$pgid]:"";
                    $unitid = (isset($PostData['unitid'][$pgid]))?$PostData['unitid'][$pgid]:"";

                    $productprocessoptionidarray = isset($PostData['productprocessoptionid'][$pgid])?$PostData['productprocessoptionid'][$pgid]:'';
                    $deleteproductprocessoptionid = array();
                    if(!empty($outproductprocessdetailidarray) && !empty($optionidarray)){
                        foreach($outproductprocessdetailidarray as $productprocessdetailid){

                            if(!empty($optionidarray[$productprocessdetailid])){
                                foreach($optionidarray[$productprocessdetailid] as $i=>$processoptionid){
                                    $productprocessoptionid = (isset($productprocessoptionidarray[$productprocessdetailid][$i]) && !empty($productprocessoptionidarray[$productprocessdetailid][$i]))?$productprocessoptionidarray[$productprocessdetailid][$i]:"";

                                    if(!empty($optionvalue[$productprocessdetailid][$i])){
                                        if($productprocessoptionid != ""){

                                            $updateOptionData[] = array('id'=>$productprocessoptionid,
                                                                        'unitid'=>$unitid[$productprocessdetailid][$i]
                                            );

                                            $updateOptionValueData[] = array(
                                                                    'productprocessoptionid'=>$productprocessoptionid,
                                                                    'value'=>$optionvalue[$productprocessdetailid][$i]
                                            );
                                        }else{

                                            $insertOptionData = array('productprocessdetailsid'=>$productprocessdetailid,
                                                                'unitid'=>$unitid[$productprocessdetailid][$i],
                                                                'processoptionid'=>$processoptionid
                                                                );
                            
                                            $this->Product_process->_table = tbl_productprocessoption;
                                            $productprocessoptionid = $this->Product_process->Add($insertOptionData);
                                            if($productprocessoptionid){
                            
                                                $insertOptionValueData[] = array('productprocessoptionid'=>$productprocessoptionid,
                                                                                'value'=>$optionvalue[$productprocessdetailid][$i]
                                                                            );
                                            }
                                        }
                                    }else{
                                        if($productprocessoptionid != ""){
                                            $deleteproductprocessoptionid[] = $productprocessoptionid;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(!empty($updateOptionData)){
                        $this->Product_process->_table = tbl_productprocessoption;
                        $this->Product_process->edit_batch($updateOptionData,"id");
                    }
                    if(!empty($insertOptionValueData)){
                        $this->Product_process->_table = tbl_productprocessoptionvalue;
                        $this->Product_process->add_batch($insertOptionValueData);
                    }
                    if(!empty($updateOptionValueData)){
                        $this->Product_process->_table = tbl_productprocessoptionvalue;
                        $this->Product_process->edit_batch($updateOptionValueData, "productprocessoptionid");
                    }
                    if(!empty($deleteproductprocessoptionid)){
                        $this->Product_process->_table = tbl_productprocessoptionvalue;
                        $this->Product_process->Delete(array("productprocessoptionid IN (".implode(",",$deleteproductprocessoptionid).")"=>null));
                        
                        $this->Product_process->_table = tbl_productprocessoption;
                        $this->Product_process->Delete(array("id IN (".implode(",",$deleteproductprocessoptionid).")"=>null));
                    } */

                    $outproductprocessdetailidarray = (isset($PostData['outproductprocessdetailid'][$pgid]))?$PostData['outproductprocessdetailid'][$pgid]:"";
                    
                    $scrapmappingidarray = (isset($PostData['productprocessoptionid'][$pgid]))?$PostData['productprocessoptionid'][$pgid]:"";
                    $optionidarray = (isset($PostData['optionid'][$pgid]))?$PostData['optionid'][$pgid]:"";
                    $optionvalue = (isset($PostData['optionvalue'][$pgid]))?$PostData['optionvalue'][$pgid]:"";
                    $unitid = (isset($PostData['unitid'][$pgid]))?$PostData['unitid'][$pgid]:"";
                    $tpreferenceid = (isset($PostData['tpreferenceid'][$pgid]))?$PostData['tpreferenceid'][$pgid]:"";
                    $tpstocktype = (isset($PostData['tpstocktype'][$pgid]))?$PostData['tpstocktype'][$pgid]:"";
                    $tpstocktypeid = (isset($PostData['tpstocktypeid'][$pgid]))?$PostData['tpstocktypeid'][$pgid]:"";
                    $tpproductid = (isset($PostData['tpproductid'][$pgid]))?$PostData['tpproductid'][$pgid]:"";
                    $tpproductpriceid = (isset($PostData['tpproductpriceid'][$pgid]))?$PostData['tpproductpriceid'][$pgid]:"";

                    $insertScrapData = $updateScrapData = $deletescrapmappingid = array();
                    if(!empty($outproductprocessdetailidarray) && !empty($optionidarray)){
                        foreach($outproductprocessdetailidarray as $k=>$ppdid){

                            $optionkey = $ppdid.'_'.$tpstocktype[$k].'_'.$tpstocktypeid[$k];

                            foreach($optionidarray[$optionkey] as $i=>$processoptionid){
        
                                $scrapmappingid = (isset($scrapmappingidarray[$optionkey][$i]) && !empty($scrapmappingidarray[$optionkey][$i]))?$scrapmappingidarray[$optionkey][$i]:"";

                                if(!empty($optionvalue[$optionkey][$i])){
                                    
                                    if($scrapmappingid != ""){
                                        
                                        $updateScrapData[] = array("id"=>$scrapmappingid,
                                                                "scraptype"=>$processoptionid,
                                                                "qty"=>$optionvalue[$optionkey][$i],
                                                                "unitid"=>$unitid[$optionkey][$i]
                                                            );
                                    }else{

                                        $insertScrapData[] = array("referencetype"=>0,
                                                                "referenceid"=>$tpreferenceid[$k],
                                                                "stocktype"=>$tpstocktype[$k],
                                                                "stocktypeid"=>$tpstocktypeid[$k],
                                                                "scraptype"=>$processoptionid,
                                                                "productid"=>$tpproductid[$k],
                                                                "priceid"=>$tpproductpriceid[$k],
                                                                "qty"=>$optionvalue[$optionkey][$i],
                                                                "unitid"=>$unitid[$optionkey][$i],
                                                                "action"=>0,
                                                                "createddate"=>$modifieddate
                                                            );
                                    }
                                }else{
                                    if($scrapmappingid != ""){
                                        $deletescrapmappingid[] = $scrapmappingid;
                                    }
                                }
                            }
                        }
                        if(!empty($updateScrapData)){
                            $this->Product_process->_table = tbl_transactionproductscrapmapping;
                            $this->Product_process->edit_batch($updateScrapData,"id");
                        }
                        if(!empty($insertScrapData)){
                            $this->Product_process->_table = tbl_transactionproductscrapmapping;
                            $this->Product_process->add_batch($insertScrapData);
                        }
                        if(!empty($deletescrapmappingid)){
                            $this->Product_process->_table = tbl_transactionproductscrapmapping;
                            $this->Product_process->Delete(array("id IN (".implode(",",$deletescrapmappingid).")"=>null));
                        }
                        // print_r($insertScrapData);
                        // print_r($updateScrapData);
                        // print_r($deletescrapmappingid); exit;
                    }
                }else{
                }
                if(!empty($extrachargesidarr)){
                    $insertextracharges = $updateextracharges = array();
                    foreach($extrachargesidarr as $index=>$extrachargesid){

                        if($extrachargesid > 0){
                            
                            $extrachargesname = trim($extrachargesnamearr[$index]);
                            $extrachargestax = trim($extrachargestaxarr[$index]);
                            $extrachargeamount = trim($extrachargeamountarr[$index]);
                            $extrachargepercentage = trim($extrachargepercentagearr[$index]);

                            $extrachargemappingid = (!empty($extrachargemappingidarr[$index]))?trim($extrachargemappingidarr[$index]):'';
                            
                            if($extrachargeamount > 0){

                                if($extrachargemappingid!=""){
                                
                                    $updateextracharges[] = array("id"=>$extrachargemappingid,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "extrachargepercentage" => $extrachargepercentage
                                                        );
                                }else{
                                    $insertextracharges[] = array("type"=>4,
                                                            "referenceid" => $productprocessid,
                                                            "extrachargesid" => $extrachargesid,
                                                            "extrachargesname" => $extrachargesname,
                                                            "taxamount" => $extrachargestax,
                                                            "amount" => $extrachargeamount,
                                                            "extrachargepercentage" => $extrachargepercentage,
                                                            "createddate" => $modifieddate,
                                                            "addedby" => $modifiedby
                                                        );
                                }
                            }
                        }
                    }
                    if(!empty($insertextracharges)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->add_batch($insertextracharges);
                    }
                    if(!empty($updateextracharges)){
                        $this->Extra_charges->_table = tbl_extrachargemapping;
                        $this->Extra_charges->edit_batch($updateextracharges,"id");
                    }
                }
            }

            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Product_process->_table = tbl_processgroupmapping;
                $this->Product_process->_fields = "(SELECT name FROM ".tbl_process." WHERE id=processid) as process";
                $this->Product_process->_where = array("id"=>$processgroupmappingid);
                $data = $this->Product_process->getRecordsById(); 

                $this->general_model->addActionLog(2,'Product Process','Edit '.$data['process'].' product process.');
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
        $this->Product_process->_where = array("id" => $id);
        $IsUpdate = $this->Product_process->Edit($updateData);
        if($IsUpdate) {
            
            if($status==1){
                $employeedata = $this->Product_process->getNextProcessEmployeeByProductProcess($id);
                if(!empty($employeedata) && $employeedata['employeeid']){
                    $this->load->model('Fcm_model','Fcm');
                    $fcmquery = $this->Fcm->getFcmDataByEmployeeId($employeedata['employeeid']);
                    if(!empty($fcmquery)){
                       
                        $insertData = array();
                        foreach ($fcmquery as $fcmrow){ 
                            $fcmarray=array();               
                            
                            $type = "16";
                            $msg = "Your ".strtolower($employeedata['currentprocess'])." process in ".strtolower($employeedata['processgroupname'])." process group is completed. You can start a next ".strtolower($employeedata['nextprocess'])." process in ".strtolower($employeedata['processgroupname'])." process group.";
                            $pushMessage = '{"type":"'.$type.'", "message":"'.$msg.'","id":"'.$id.'"}';
                            $fcmarray[] = $fcmrow['fcm'];
                    
                            //$this->Fcm->sendPushNotificationToFCM($fcmarray,$pushMessage);                         
                            $this->Fcm->sendFcmNotification($type,$pushMessage,$fcmrow['memberid'],$fcmarray,0,$fcmrow['devicetype']);
                            
                            $insertData[] = array(
                                'type'=>$type,
                                'message' => $pushMessage,
                                'memberid'=>$fcmrow['memberid'],    
                                'isread'=>0,   
                                'usertype'=>1,                    
                                'createddate' => $modifieddate,               
                                'addedby'=>$modifiedby
                            );
                        }  
                        if(!empty($insertData)){
                            $this->load->model('Notification_model','Notification');
                            $this->Notification->_table = tbl_notification;
                            $this->Notification->add_batch($insertData);
                            //echo 1;//send notification
                        }                
                    }
                }
            }

            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Product_process->_table = tbl_processgroupmapping;
                $this->Product_process->_fields = "(SELECT name FROM ".tbl_process." WHERE id=processid) as process";
                $this->Product_process->_where = array("id IN (SELECT processgroupmappingid FROM ".tbl_productprocess." WHERE id='".$id."')"=>null);
                $data = $this->Product_process->getRecordsById(); 

                $this->general_model->addActionLog(2,'Product Process','Change '.$data['process'].' product process status.');
            }
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

            $this->Product_process->_table = tbl_productprocess;
            $this->Product_process->_fields = "id,type";
            $this->Product_process->_where = array('id'=>$row);
            $productprocessdata = $this->Product_process->getRecordsById();

            if(!empty($productprocessdata)){

                if($productprocessdata['type'] == 1){
                    
                    $this->Product_process->_table = tbl_productprocessoptionvalue;
                    $this->Product_process->Delete(array('productprocessoptionid IN (SELECT id FROM '.tbl_productprocessoption.' WHERE productprocessdetailsid IN (SELECT id FROM '.tbl_productprocessdetails.' WHERE productprocessid IN (SELECT parentproductprocessid FROM '.tbl_productprocess.' WHERE id = "'.$row.'")))'=>null));
                    
                    $this->Product_process->_table = tbl_productprocessoption;
                    $this->Product_process->Delete(array('productprocessdetailsid IN (SELECT id FROM '.tbl_productprocessdetails.' WHERE productprocessid IN (SELECT parentproductprocessid FROM '.tbl_productprocess.' WHERE id = "'.$row.'"))'=>null));
        
                    $this->Product_process->_table = tbl_productprocesscertificates;
                    $this->Product_process->_fields = "id,filename";
                    $this->Product_process->_where = array('productprocessid'=>$row);
                    $certificatesdata = $this->Product_process->getRecordById();
        
                    if(!empty($certificatesdata)){
                        foreach($certificatesdata as $certificate){
                            unlinkfile("PRODUCT_PROCESS_CERTIFICATE",$certificate['filename'],PRODUCT_PROCESS_CERTIFICATE_PATH);
                        }
                    }
                    $this->Product_process->Delete(array('productprocessid'=>$row));
                }
                
                $this->load->model('Extra_charges_model', 'Extra_charges');
                $this->Extra_charges->_table = tbl_extrachargemapping;
                $this->Extra_charges->Delete(array("type"=>4,"referenceid"=>$row));

                $this->Product_process->_table = tbl_transactionproductstockmapping;
                $this->Product_process->Delete(array("referencetype"=>0,"referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid='".$row."')"=>null));

                $this->Product_process->_table = tbl_transactionproductscrapmapping;
                $this->Product_process->Delete(array("referencetype"=>0,"referenceid IN (SELECT id FROM ".tbl_productprocessdetails." WHERE productprocessid='".$row."')"=>null));

                $this->Product_process->_table = tbl_productionplanqtydetail;
                $this->Product_process->Delete(array('productprocessid'=>$row));

                $this->Product_process->_table = tbl_productprocessdetails;
                $this->Product_process->Delete(array('productprocessid'=>$row));
    
                if($this->viewData['submenuvisibility']['managelog'] == 1){

                    $this->Product_process->_table = tbl_processgroupmapping;
                    $this->Product_process->_fields = "(SELECT name FROM ".tbl_process." WHERE id=processid) as process";
                    $this->Product_process->_where = array("id IN (SELECT processgroupmappingid FROM ".tbl_productprocess." WHERE id='".$row."')"=>null);
                    $data = $this->Product_process->getRecordsById(); 
    
                    $this->general_model->addActionLog(3,'Product Process','Delete '.$data['process'].' product process.');
                }

                $this->Product_process->_table = tbl_productprocess;
                $this->Product_process->Delete(array('id'=>$row));
            }
        }
    }
    public function printProcessDetail(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $productprocessid = $PostData['id'];
        $PostData['printtype'] = 1;
        $PostData['productprocessdata'] = $this->Product_process->getProductProcessDetailsById($productprocessid);
        
        $html['content'] = $this->load->view(ADMINFOLDER."product_process/Printprocessdetails.php",$PostData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){

            $this->general_model->addActionLog(0,'Product Process','Print '.$PostData['productprocessdata']['processname'].' product process details.');
        }
        echo json_encode($html); 
    }
    public function printAllProcessDetail(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $processgroupid = $PostData['id'];
        $productprocessid = $PostData['productprocessid'];

        $PrintData['productprocessdata'] = $this->Product_process->getAllProductProcessDetailsByProcessGroupId($processgroupid,$productprocessid);
        $PrintData['producttotal'] = $this->Product_process->getTotalProductProcessAmountDetailsByProcessGroupId($processgroupid,$productprocessid);
        $PrintData['printtype'] = 1; 
        if($PostData['type'] == 'total'){
            $PrintData['section'] = 'total';
            $PrintData['heading'] = 'View Total Amount Details';
        }else{
            $PrintData['heading'] = 'View All Product Process Details';
        }
        $html['content'] = $this->load->view(ADMINFOLDER."product_process/Printallprocessdetails.php",$PrintData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){

            $this->general_model->addActionLog(0,'Product Process','Print '.$PrintData['productprocessdata']['processes'][0]['processgroup'].' process group all product process details.');
        }
        echo json_encode($html); 
    }
    public function printProcessWiseProductDetail(){

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $processgroupmappingid = $PostData['id'];
        $productprocessid = $PostData['productprocessid'];

        $PrintData['process'] = $this->Product_process->getProcessWiseProductDetailByProcessGroupMappingId($processgroupmappingid,$productprocessid);
        $PrintData['printtype'] = 1; 
        $PrintData['heading'] = 'View Process Details';
        
        $html['content'] = $this->load->view(ADMINFOLDER."product_process/Printprocesswisedetail.php",$PrintData,true);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
    
            $this->general_model->addActionLog(0,'Product Process','Print '.$PrintData['process']['processname'].' process detail on product process.');
        }
        echo json_encode($html); 
    }
    public function exporttopdfallprocessdetail(){
        
        if($_REQUEST['type'] == 'total'){
            $PrintData['section'] = 'total';
            $PrintData['heading'] = 'View Total Amount Details';
        }else if($_REQUEST['type'] == 'process'){
            $PrintData['heading'] = 'View Process Details';
        }else{
            $PrintData['heading'] = 'View All Product Process Details';
        }
        $html="";
        $PrintData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        $PrintData['printtype'] = 1; 

        $header=$this->load->view(ADMINFOLDER . 'Companyheader.php', $PrintData,true);
        
        $productprocessid = $_REQUEST['productprocessid'];
        if($_REQUEST['type'] == 'process'){

            $processgroupmappingid = $_REQUEST['processgroupmappingid'];
            $PrintData['process'] = $this->Product_process->getProcessWiseProductDetailByProcessGroupMappingId($processgroupmappingid,$productprocessid);
            $html=$this->load->view(ADMINFOLDER . 'product_process/Processwisedetailsformatforpdf.php', $PrintData,true);

            $filename = "ViewProcessDetails.pdf";

            if($this->viewData['submenuvisibility']['managelog'] == 1){
    
                $this->general_model->addActionLog(0,'Product Process','Export to PDF '.$PrintData['process']['processname'].' process detail on product process.');
            }
        }else{

            $processgroupid = $_REQUEST['processgroupid'];
            $PrintData['productprocessdata'] = $this->Product_process->getAllProductProcessDetailsByProcessGroupId($processgroupid,$productprocessid);
            $PrintData['producttotal'] = $this->Product_process->getTotalProductProcessAmountDetailsByProcessGroupId($processgroupid,$productprocessid);

            $html=$this->load->view(ADMINFOLDER . 'product_process/Allprocessdetailsformatforpdf.php', $PrintData,true);

            $filename = "ViewAllProcessDetails.pdf";
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
    
                $this->general_model->addActionLog(0,'Product Process','Export to PDF '.$PrintData['productprocessdata']['processes'][0]['processgroup'].' process group all product process details.');
            }
        }
      
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} af {nb}');
        $pdfFilePath = $filename;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   40, // margin top
                   15, // margin bottom
                    3, // margin header
                    10); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
       
        ob_start();
        ob_end_clean();
        // echo $html; exit;
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "D");
    }
    public function print_process_challan(){
        
        $productprocessid = $_REQUEST['productprocessid'];
        $PrintData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        $PrintData['heading'] = 'Out Product Details';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader.php', $PrintData,true);

        $PrintData['challandata'] = $this->Product_process->getProcessDetailByProductProcessId($productprocessid);
        $html=$this->load->view(ADMINFOLDER . 'product_process/Processchallanformatforpdf.php', $PrintData,true);

        // echo $header.$html; exit;
        if(!is_dir(PROCESS_CHALLAN_PATH)){
            @mkdir(PROCESS_CHALLAN_PATH);              
        } 
        $filename = PROCESS_CHALLAN_PATH.str_replace(" ","-",strtolower($PrintData['challandata']['processname']))."-challan.pdf";
        $pdfurl = PROCESS_CHALLAN.str_replace(" ","-",strtolower($PrintData['challandata']['processname']))."-challan.pdf";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Product Process','Print challan detail of '.$PrintData['challandata']['processname'].' process.');
        }
      
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load(array('mode' => '',                                          
                                          'format' => 'A5',
                                          'default_font_size' => 0,
                                          'default_font' => '',
                                          'margin_left' => 15,
                                          'margin_right' => 15,
                                          'margin_top' => 16,
                                          'margin_bottom' => 16,
                                          'margin_header' => 10,
                                          'margin_footer' => 9,
                                          'orientation' => 'L'));
        $pdf->AddPage(
            'L', // L - landscape, P - portrait
            '',
            '',
            '',
            '',
            10, // margin_left
            10, // margin right
            40, // margin top
            15, // margin bottom
            10, // margin header
            10
        ); // margin footer

        // Set a simple Footer including the page number
        // $pdf->setFooter('Side {PAGENO} af {nb}');
        $pdfFilePath = $filename;

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css

        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
       
        ob_start();
        ob_end_clean();
        // echo $html; exit;
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "F");

        echo json_encode(array("error"=>"1","file"=>$pdfurl));
    }
    public function print_mul_process_challan(){
        
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $PrintData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        $PrintData['heading'] = 'Out Product Details';
        
        $header=$this->load->view(ADMINFOLDER . 'Companyheader.php', $PrintData,true);
        
        if(!is_dir(PROCESS_CHALLAN_PATH)){
            @mkdir(PROCESS_CHALLAN_PATH);              
        } 
        $filename = PROCESS_CHALLAN_PATH."process-challan.pdf";
        $pdfurl = PROCESS_CHALLAN."process-challan.pdf";
        $html = '';

        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load(array('mode' => '',                                          
        'format' => 'A5',
                                        'default_font_size' => 0,
                                        'default_font' => '',
                                        'margin_left' => 15,
                                        'margin_right' => 15,
                                        'margin_top' => 16,
                                        'margin_bottom' => 16,
                                        'margin_header' => 10,
                                        'margin_footer' => 9,
                                        'orientation' => 'L'));
        
        $challandata = array();
        /* foreach ($ids as $productprocessid) {
            
            $data = $this->Product_process->getProductProcessDataById($productprocessid);
            if($data['type']==0 && $data['vendorid']!=0){
                $challandata[] = $this->Product_process->getProcessDetailByProductProcessId($productprocessid);
            }
            
        } */
        $challandata = $this->Product_process->getOutwardProcessByProductProcessIds($ids);
        // print_r($challandata); exit;
        $PrintData['multiplechallandata'] = $challandata;
        $html .= $this->load->view(ADMINFOLDER . 'product_process/Processmultilechallanformatforpdf.php', $PrintData,true);
        $pdf->AddPage(
            'L', // L - landscape, P - portrait
            '',
            '',
            '',
            '',
            10, // margin_left
            10, // margin right
            40, // margin top
            15, // margin bottom
            10, // margin header
            10
        ); // margin footer
        // echo $header.$html; exit;
        
        
        // Set a simple Footer including the page number
        // $pdf->setFooter('Side {PAGENO} af {nb}');
        $pdfFilePath = $filename;

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css

        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
    
        ob_start();
        ob_end_clean();
        // echo $html; exit;
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "F");

        echo json_encode(array("error"=>"1","file"=>$pdfurl));
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Product Process','Print out product process challan.');
        }
    }
    public function getOrderProductsForFIFO(){
        $PostData = $this->input->post();
        $productid = $PostData['productid'];
        $priceid = $PostData['priceid'];

        $productdata = $this->Product_process->getOrderProductsForFIFO($productid,$priceid);

        echo json_encode($productdata);      
    }
    public function product_process_add() {
        
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        // $finalorderproducts = $PostData['finalorderproducts'][1];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $json = array();
        
        $processgroupidarr = isset($PostData['processgroupid'])?$PostData['processgroupid']:array($PostData['postprocessgroupid']);
        $processtype = $PostData['processtype'];

        $this->load->model('Stock_report_model', 'Stock');
        $this->load->model('Extra_charges_model', 'Extra_charges');

        if(!empty($processgroupidarr)){
            foreach($processgroupidarr as $processgroupid){
                $pgid = $processgroupid;
                
                $mainbatchprocessid = $PostData['mainbatchprocessid'][$pgid];
                $processid = $PostData['processid'][$pgid];
                $processgroupmappingid = $PostData['processgroupmappingid'][$pgid];
                $batchno = $PostData['batchno'][$pgid];
                $transactiondate = $this->general_model->convertdate($PostData['transactiondate'][$pgid]);
                $comments = $PostData['remarks'][$pgid];
                $productionplanqtydetail = !empty($PostData['productionplanqtydetail'][$pgid])?json_decode($PostData['productionplanqtydetail'][$pgid],true):"";
                $isreprocess = $parentproductprocessid = $productprocessid = 0;
                
                if($processtype == "IN"){
                    $processbymemberid = $PostData['processbymemberid'][$pgid];
                    $vendorid = $PostData['postvendorid'][$pgid];
                    $machineid = $PostData['postmachineid'][$pgid];
                    $estimatedate = !empty($PostData['postestimatedate'][$pgid])?$this->general_model->convertdate($PostData['postestimatedate'][$pgid]):"";
                    $orderid = $PostData['postorderid'][$pgid];
                    
                    $type = 1;
                    $parentproductprocessid = $PostData['parentproductprocessid'][$pgid];
                    $qcrequire = $PostData['qcrequire'][$pgid];
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
                    if(!empty($PostData['inextrachargesid'][$pgid])){
                        $extrachargesidarr = (isset($PostData['inextrachargesid'][$pgid]))?$PostData['inextrachargesid'][$pgid]:'';
                        $extrachargestaxarr = (isset($PostData['inextrachargestax'][$pgid]))?$PostData['inextrachargestax'][$pgid]:'';
                        $extrachargeamountarr = (isset($PostData['inextrachargeamount'][$pgid]))?$PostData['inextrachargeamount'][$pgid]:'';
                        $extrachargesnamearr = (isset($PostData['inextrachargesname'][$pgid]))?$PostData['inextrachargesname'][$pgid]:'';
                        $extrachargepercentagearr = (isset($PostData['inextrachargepercentage'][$pgid]))?$PostData['inextrachargepercentage'][$pgid]:'';
                    }
                }else{
                    /* if($processtype == "REPROCESS"){
                        $processbymemberid = $PostData['processbymemberid'];
                    }else{
                    } */
                    $processbymemberid = $PostData['processedby'][$pgid];
                    $vendorid = ($PostData['processedby'][$pgid]==0 && isset($PostData['vendorid'][$pgid]))?$PostData['vendorid'][$pgid]:0;
                    $machineid = ($PostData['processedby'][$pgid]==1 && isset($PostData['machineid'][$pgid]))?$PostData['machineid'][$pgid]:0;
                    $estimatedate = !empty($PostData['estimatedate'][$pgid])?$this->general_model->convertdate($PostData['estimatedate'][$pgid]):"";
                    $orderid = $PostData['orderid'][$pgid];
        
                    $type = $parentproductprocessid = $qcrequire = 0;
                    
                    /* $this->Product_process->_table = tbl_productprocess;
                    $this->Product_process->_where = array('batchno' => $batchno);
                    $Count = $this->Product_process->CountRecords();
                    if($Count>0){
                        $json = array('error'=>2); // Process group already added.
                        json_encode($json);exit;
                    } */
                    if(!empty($PostData['outextrachargesid'][$pgid])){
                        $extrachargesidarr = (isset($PostData['outextrachargesid'][$pgid]))?$PostData['outextrachargesid'][$pgid]:'';
                        $extrachargestaxarr = (isset($PostData['outextrachargestax'][$pgid]))?$PostData['outextrachargestax'][$pgid]:'';
                        $extrachargeamountarr = (isset($PostData['outextrachargeamount'][$pgid]))?$PostData['outextrachargeamount'][$pgid]:'';
                        $extrachargesnamearr = (isset($PostData['outextrachargesname'][$pgid]))?$PostData['outextrachargesname'][$pgid]:'';
                        $extrachargepercentagearr = (isset($PostData['outextrachargepercentage'][$pgid]))?$PostData['outextrachargepercentage'][$pgid]:'';
                    }
        
                    if(START_PROCESS_WITHOUT_STOCK==0 && STOCKMANAGEMENT==1){
                        
                        if(!empty($PostData['outproductid'][$pgid])){
                            foreach($PostData['outproductid'][$pgid] as $s=>$stockproductid){
                                if($stockproductid > 0 && $PostData['outproductvariantid'][$pgid][$s] > 0 && $PostData['quantity'][$pgid][$s] > 0){

                                    $keynm = 'overallclosingstock';
                                    if(STOCK_MANAGE_BY==0){
                                        $ProductStock = $this->Stock->getAdminProductStock($stockproductid,1);
                                    }else{
                                        $ProductStock = $this->Stock->getAdminProductFIFOStock($stockproductid,1);
                                    }
                                    $availablestock = 0;
                                    if(!empty($ProductStock)){
                                        $key = array_search($PostData['outproductvariantid'][$pgid][$s], array_column($ProductStock, 'priceid'));
                                        if(trim($key)!="" && isset($ProductStock[$key][$keynm])){
                                            $availablestock = (int)$ProductStock[$key][$keynm];
                                        }
                                    }

                                    if($PostData['quantity'][$pgid][$s] > $availablestock){
                                        //Quantity greater than stock quantity.
                                        echo json_encode(array("error"=>-3,"message"=>"Quantity greater than stock quantity of ".($s+1)." out product !")); exit;
                                    }
                                    
                                }
                            }
                        }
                    }
                }
                if($processtype == "REPROCESS"){
                    $isreprocess = 1;
                }
                $InsertData = array('productprocessid' => $mainbatchprocessid,
                                    'parentproductprocessid' => $parentproductprocessid,                                    
                                    'processgroupmappingid' => $processgroupmappingid,
                                    'batchno' => $batchno,
                                    'transactiondate' => $transactiondate,
                                    'estimatedate' => $estimatedate,
                                    'processbymemberid' => $processbymemberid,
                                    'vendorid' => $vendorid,
                                    'machineid' => $machineid,
                                    'orderid' => $orderid,
                                    'comments' => $comments,
                                    'isreprocess' => $isreprocess,
                                    'type' => $type,
                                    'processstatus' => 0,
                                    'createddate' => $createddate,
                                    'addedby' => $addedby,                              
                                    'modifieddate' => $createddate,                             
                                    'modifiedby' => $addedby 
                                );
                // print_r($InsertData);
                $this->Product_process->_table = tbl_productprocess;
                $ProductProcessID = $this->Product_process->Add($InsertData);
                
                if($ProductProcessID){
                    
                    if(($processtype == "IN" && !empty($PostData['inproductid'][$pgid]) || ($processtype != "IN" && !empty($PostData['outproductid'][$pgid])))){
                        
                        $insertprocessdetaildata = $insertprocesscertificates = $insertOptionValueData = $insertstockdata = $inserttestingdata = array();
                        $productidarray = ($processtype == "IN"?$PostData['inproductid'][$pgid]:$PostData['outproductid'][$pgid]);
                        $productvariantid = ($processtype == "IN"?$PostData['inproductvariantid'][$pgid]:$PostData['outproductvariantid'][$pgid]);
                        $quantity = ($processtype == "IN"?$PostData['inquantity'][$pgid]:$PostData['quantity'][$pgid]);
                        $pendingquantityArr = ($processtype == "IN"?$PostData['inpendingquantity'][$pgid]:"");
                        $laborcostArr = ($processtype == "IN"?$PostData['inlaborcost'][$pgid]:"");
                        $productprice = ($processtype == "IN"?$PostData['inprice'][$pgid]:$PostData['outprice'][$pgid]);
                        
                        $total_out_product_amount = $total_out_extra_charges_amount = 0;
                        if($processtype == "IN"){
                            $planningqtyarray = $PostData['planningqty'][$pgid];
                            
                            $productprocessdata = $this->Product_process->getProductProcessDetailsById($parentproductprocessid);
                            if(!empty($productprocessdata['outproducts'])){
                                foreach($productprocessdata['outproducts'] as $op){ 
                                    $total_out_product_amount += $op['price'] * $op['quantity'];
                                }
                            }
                            if(!empty($productprocessdata['outextracharges'])){
                                foreach($productprocessdata['outextracharges'] as $oc){ 
                                    if($oc['chargetype'] == 0){
                                        $amount = $oc['amount'];
                                    }else{
                                        $amount = $oc['amount'] * array_sum(array_column($productprocessdata['outproducts'], 'quantity'));
                                    } 
                                    $total_out_extra_charges_amount += $amount;
                                }
                            }
                            $totalinextracharges =0;
                            if(!empty($extrachargesidarr)){
                                foreach($extrachargesidarr as $index=>$extrachargesid){
                                    if($extrachargesid > 0){
                                        $totalinextracharges += $extrachargeamountarr[$index];
                                    }
                                }
                            }
                            $countproducts = 0;
                            if(!empty($productidarray)){
                                foreach($productidarray as $c=>$productid){
                                    if($productid > 0 && $productvariantid[$c] > 0 && ($processtype == "IN" || $quantity[$c] > 0)){
                                        $countproducts++;
                                    }
                                }
                            }
                            $totalexrachargeofproduct = $totalinextracharges / $countproducts;
                        }
                        if(!empty($productidarray)){
                            foreach($productidarray as $k=>$productid){
                                
                                if($productid > 0 && $productvariantid[$k] > 0 && ($processtype == "IN" || $quantity[$k] > 0)){
                                    if($processtype == "IN"){
                                        $issupportingproduct = 0;
                                        $isfinalproduct = (isset($PostData['finalproduct_'.$pgid.'_'.($k+1)])?1:0);
                                        $unit = "";
                                        $pendingquantity = $pendingquantityArr[$k];
                                        $laborcost = $laborcostArr[$k];

                                        $landing_per_piece = $net_landed_cost_price = 0;

                                        /* $landingcost = 0;
                                        if($isfinalproduct==1){
                                            $landingcost = ($quantity[$k]>0)?($calculatelandingcost / $quantity[$k]):0;
                                        } */

                                        // add on testing
                                        /*$landingcost = $calculatelandingcost / $quantity[$k] + $laborcost;
                                        $landingcostamount = $calculatelandingcost + ($quantity[$k]*$laborcost);
                                        
                                        $total = $landingcostamount + $totalexrachargeofproduct;
                                        
                                        $landingcostprice = $total / $quantity[$k];*/

                                        $inqty = $quantity[$k];
                                        
                                        
                                        /* $landingcostprice = 0;
                                        if(!empty($productprocessdata['outproducts'])){
                                            $total_oc_amount_perproduct = $calculatelandingcost = 0;
                                            if(count($productprocessdata['outextracharges']) > 0){
                                                $total_oc_amount_perproduct = array_sum(array_column($productprocessdata['outextracharges'],"amount")) / count($productprocessdata['outproducts']);
                                            }
                                            foreach($productprocessdata['outproducts'] as $_op){ 
                                                $finalqty = $_op['quantity'] - $_op['scrapqty'];
                                                if($isfinalproduct==1 || $_op['productpriceid'] == $productvariantid[$k]){
                                                    if($inqty > 0){
                                                        if($inqty > $finalqty){
                                                            $calculatelandingcost += ($_op['price'] * $finalqty) + $total_oc_amount_perproduct;
                                                            $inqty -= $finalqty;
                    
                                                        }else if($inqty <= $finalqty){
                    
                                                            $calculatelandingcost += ($_op['price'] * $inqty) + $total_oc_amount_perproduct;
                                                            $inqty = 0; 
                    
                                                        }
                                                    }
                                                }
                                            }
                                            if($calculatelandingcost > 0){
                                                $landingcost = $calculatelandingcost / $quantity[$k] + $laborcost;
                                                $landingcostamount = $calculatelandingcost + ($quantity[$k]*$laborcost);
                                                
                                                $total = $landingcostamount + $totalexrachargeofproduct;
                                                
                                                $landingcostprice = $total / $quantity[$k];
                                            }
                                        } */
                                        $plan_qty = !empty($planningqtyarray[$k])?$planningqtyarray[$k]:0;
                                        
                                        if($plan_qty!=0){

                                            $per_piece_extra_charge = $total_out_extra_charges_amount / $plan_qty;
                                            $per_piece_out_price = $total_out_product_amount / $plan_qty + $per_piece_extra_charge;
    
                                            $landing_per_piece = $laborcost + $per_piece_out_price;
                                            $total_landing_cost = $landing_per_piece * $inqty;
    
                                            $total = $total_landing_cost + $totalexrachargeofproduct;
    
                                            $net_landed_cost_price = $total / $inqty;
                                        }

                                    }else{
                                        $issupportingproduct = 0;
                                        $isfinalproduct = 0;
                                        $unit = $PostData['unitid'][$pgid][$k];
                                        $pendingquantity = $laborcost = $landing_per_piece = $net_landed_cost_price = 0;
                                    }
                                    
                                    
                                    $insertprocessdetaildata = array('productprocessid' => $ProductProcessID,
                                            'productpriceid' => $productvariantid[$k],
                                            'unit' => $unit,
                                            'quantity' => $quantity[$k],
                                            'pendingquantity' => $pendingquantity,
                                            'price' => $productprice[$k],
                                            'issupportingproduct' => $issupportingproduct,                        
                                            'isfinalproduct' => $isfinalproduct,
                                            'laborcost' => $laborcost,
                                            'landingcost' => $net_landed_cost_price,
                                            'landingcostperpiece' => $landing_per_piece
                                    );
                                    $this->Product_process->_table = tbl_productprocessdetails;
                                    $productprocessdetailsid = $this->Product_process->Add($insertprocessdetaildata);
        
                                    if($productprocessdetailsid){
                                        //if(STOCK_MANAGE_BY == 1){
                                            if($processtype != "IN"){
                                              
                                                $orderproducts = json_decode($PostData['finalorderproducts'][$pgid][$k],true);
                
                                                if(!empty($orderproducts)){
                                                    foreach($orderproducts as $op){
                
                                                        $stocktype = !empty($op['stocktype'])?$op['stocktype']:"";
                                                        $stocktypeid = !empty($op['stocktypeid'])?$op['stocktypeid']:$productprocessdetailsid;

                                                        $insertstockdata[] = array("referencetype"=>0,
                                                                "referenceid"=>$productprocessdetailsid,
                                                                "stocktype"=>$stocktype,
                                                                "stocktypeid"=>$stocktypeid,
                                                                "productid"=>$productid,
                                                                "priceid"=>$productvariantid[$k],
                                                                "qty"=>$op['qty'],
                                                                "action"=>1,
                                                                "createddate"=>$createddate
                                                            );
                                                    }
                                                }else{
                                                    $insertstockdata[] = array("referencetype"=>0,
                                                                "referenceid"=>$productprocessdetailsid,
                                                                "stocktype"=>1,
                                                                "stocktypeid"=>$productprocessdetailsid,
                                                                "productid"=>$productid,
                                                                "priceid"=>$productvariantid[$k],
                                                                "qty"=>$quantity[$k],
                                                                "action"=>1,
                                                                "createddate"=>$createddate
                                                            );

                                                }
                
                                            }else{
                                                $stocktypeidarr = !empty($PostData['stocktypeid'][$pgid][$k+1])?$PostData['stocktypeid'][$pgid][$k+1]:"";
            
                                                if(!empty($stocktypeidarr)){
                                                    foreach($stocktypeidarr as $s=>$stocktypeid){
                                                        $qty = !empty($PostData['stockqty'][$pgid][$k+1][$s])?$PostData['stockqty'][$pgid][$k+1][$s]:"";
                                                        $stocktype = !empty($PostData['stocktype'][$pgid][$k+1][$s])?$PostData['stocktype'][$pgid][$k+1][$s]:"";
                                                        
                                                        if(!empty($qty)){
                                                            
                                                            $stocktypeid = !empty($stocktypeid)?$stocktypeid:$productprocessdetailsid;

                                                            $insertstockdata[] = array("referencetype"=>0,
                                                                "referenceid"=>$productprocessdetailsid,
                                                                "stocktype"=>$stocktype,
                                                                "stocktypeid"=>$stocktypeid,
                                                                "productid"=>$productid,
                                                                "priceid"=>$productvariantid[$k],
                                                                "qty"=>$qty,
                                                                "action"=>0,
                                                                "createddate"=>$transactiondate,
                                                                "modifieddate"=>$createddate,
                                                            );
                                                        }
                                                    }
                                                }else{
                                                    $insertstockdata[] = array("referencetype"=>0,
                                                                "referenceid"=>$productprocessdetailsid,
                                                                "stocktype"=>1,
                                                                "stocktypeid"=>$productprocessdetailsid,
                                                                "productid"=>$productid,
                                                                "priceid"=>$productvariantid[$k],
                                                                "qty"=>$quantity[$k],
                                                                "action"=>0,
                                                                "createddate"=>$transactiondate,
                                                                "modifieddate"=>$createddate,
                                                            );
                                                }


                                                if($isfinalproduct==1){
                                                    if($qcrequire==1){
        
                                                        $inserttestingdata[] = array(
                                                            'mechanicledefectqty'=>0,
                                                            'electricallydefectqty'=>0,
                                                            'visuallydefectqty'=>0,
                                                            'transactionproductsid'=>$productprocessdetailsid,
                                                            'mechaniclecheck'=> 0,
                                                            'electricallycheck'=> 0,
                                                            'visuallycheck'=> 0,
                                                            'filename' => '' 
                                                        );
                                                    }
                                                }
                                            }
                                        //}
                                    }
                                }
                            }
                        }
                        // pre($inserttestingdata);
                        if(/* STOCK_MANAGE_BY == 1 &&  */!empty($insertstockdata)){
                            $this->Product_process->_table = tbl_transactionproductstockmapping;
                            $this->Product_process->add_batch($insertstockdata);
                        }
                        if($processtype == "IN"){
                            
                            if(!empty($inserttestingdata)){

                                $InsertData = array('batchid'=>$ProductProcessID,
                                    'testdate'=>$transactiondate,
                                    'remarks'=>'',
                                    'processid'=>$processid,
                                    'status'=>0,
                                    'createddate' => $createddate,
                                    'addedby' => $addedby,
                                    'modifeddate' => $createddate,
                                    'modififedby' => $addedby
                                );

                                $InsertData = array_map('trim',$InsertData);
                                $this->Product_process->_table = tbl_testingrd;
                                $testingid = $this->Product_process->add($InsertData);

                                foreach($inserttestingdata as $key=>$insert){
                                    $inserttestingdata[$key]['testingrdid']=$testingid;
                                }

                                $this->Product_process->_table = tbl_testingrdmapping;
                                $this->Product_process->add_batch($inserttestingdata);
                            }

                            $isCertificate = $PostData['isCertificate'];
                            if($isCertificate!=0){
                                
                                $doctitle = $PostData['doctitle'];
                                $docno = $PostData['docno'];
                                $docdescription = $PostData['docdescription'];
                                $docdate = $PostData['docdate'];
                                
                                if(!empty($_FILES)){
                                    
                                    foreach ($_FILES as $key => $value) {
                                        $id = preg_replace('/[^0-9]/', '', $key);
                                         
                                        $file = "";
                                        if(strpos($key, 'docfile') !== false && $_FILES['docfile'.$id]['name']!=''){
                                            $file = uploadFile('docfile'.$id, 'PRODUCT_PROCESS_CERTIFICATE', PRODUCT_PROCESS_CERTIFICATE_PATH, '*', '', 1, PRODUCT_PROCESS_CERTIFICATE_LOCAL_PATH);
            
                                            if($file == 0 && $file == 2){
                                                $file = "";
                                            } 
                                        }
                                        if($docno[$id]!="" && !empty($doctitle[$id]) && !empty($file)){
        
                                            $insertprocesscertificates[] = array('productprocessid' => $ProductProcessID,
                                                    'docno' => $docno[$id],
                                                    'documentdate' => ($docdate[$id]!=""?$this->general_model->convertdate($docdate[$id]):""),
                                                    'title' => $doctitle[$id],
                                                    'remarks' => $docdescription[$id],                        
                                                    'filename' => $file
                                            );
                                        }
                                    }
                                }
                                if(count($insertprocesscertificates) > 0){
                                    $this->Product_process->_table = tbl_productprocesscertificates;
                                    $this->Product_process->add_batch($insertprocesscertificates);
                                }
                            }
                            
                            /* $outproductprocessdetailidarray = (isset($PostData['outproductprocessdetailid'][$pgid]))?$PostData['outproductprocessdetailid'][$pgid]:"";
                            $optionidarray = (isset($PostData['optionid'][$pgid]))?$PostData['optionid'][$pgid]:"";
                            $optionvalue = (isset($PostData['optionvalue'][$pgid]))?$PostData['optionvalue'][$pgid]:"";
                            $unitid = (isset($PostData['unitid'][$pgid]))?$PostData['unitid'][$pgid]:"";
            
                            if(!empty($outproductprocessdetailidarray) && !empty($optionidarray)){
                                foreach($outproductprocessdetailidarray as $productprocessdetailid){
            
                                    if(!empty($optionidarray[$productprocessdetailid])){
                                        foreach($optionidarray[$productprocessdetailid] as $i=>$processoptionid){
                                            if(!empty($optionvalue[$productprocessdetailid][$i])){
                                                
                                                $insertOptionData = array('productprocessdetailsid'=>$productprocessdetailid,
                                                                        'unitid'=>$unitid[$productprocessdetailid][$i],
                                                                        'processoptionid'=>$processoptionid
                                                                        );
                                
                                                $this->Product_process->_table = tbl_productprocessoption;
                                                $productprocessoptionid = $this->Product_process->Add($insertOptionData);
                                                if($productprocessoptionid){
                                
                                                    $insertOptionValueData[] = array('productprocessoptionid'=>$productprocessoptionid,
                                                                                    'value'=>$optionvalue[$productprocessdetailid][$i]
                                                                                );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            if(!empty($insertOptionValueData)){
                                $this->Product_process->_table = tbl_productprocessoptionvalue;
                                $this->Product_process->add_batch($insertOptionValueData);
                            } */
                            $outproductprocessdetailidarray = (isset($PostData['outproductprocessdetailid'][$pgid]))?$PostData['outproductprocessdetailid'][$pgid]:"";
                            $optionidarray = (isset($PostData['optionid'][$pgid]))?$PostData['optionid'][$pgid]:"";
                            $optionvalue = (isset($PostData['optionvalue'][$pgid]))?$PostData['optionvalue'][$pgid]:"";
                            $unitid = (isset($PostData['unitid'][$pgid]))?$PostData['unitid'][$pgid]:"";
                            $tpreferenceid = (isset($PostData['tpreferenceid'][$pgid]))?$PostData['tpreferenceid'][$pgid]:"";
                            $tpstocktype = (isset($PostData['tpstocktype'][$pgid]))?$PostData['tpstocktype'][$pgid]:"";
                            $tpstocktypeid = (isset($PostData['tpstocktypeid'][$pgid]))?$PostData['tpstocktypeid'][$pgid]:"";
                            $tpproductid = (isset($PostData['tpproductid'][$pgid]))?$PostData['tpproductid'][$pgid]:"";
                            $tpproductpriceid = (isset($PostData['tpproductpriceid'][$pgid]))?$PostData['tpproductpriceid'][$pgid]:"";
                            
                            $insertScrapData = $updateScrapData = array();
                            if(!empty($outproductprocessdetailidarray) && !empty($optionidarray)){
                                foreach($outproductprocessdetailidarray as $k=>$ppdid){

                                    $optionkey = $ppdid.'_'.$tpstocktype[$k].'_'.$tpstocktypeid[$k];

                                    $this->Product_process->_table = tbl_transactionproductscrapmapping;
                                    foreach($optionidarray[$optionkey] as $i=>$processoptionid){
                
                                        if(!empty($optionvalue[$optionkey][$i])){
                                            
                                            /* $this->Product_process->_where = array("referencetype"=>0,"referenceid"=>$tpreferenceid[$k],"stocktype"=>$tpstocktype[$k],"stocktypeid"=>$tpstocktypeid[$k],"scraptype"=>$processoptionid);
                                            $scrapdata = $this->Product_process->getRecordsById();
                                            
                                            if(!empty($scrapdata)){
                                                $updateScrapData[] = array("id"=>$scrapdata['id'],
                                                    "qty"=>$optionvalue[$optionkey][$i],
                                                    "unitid"=>$unitid[$optionkey][$i]
                                                );
                                            }else{ */

                                                $insertScrapData[] = array("referencetype"=>0,
                                                                        "referenceid"=>$tpreferenceid[$k],
                                                                        "stocktype"=>$tpstocktype[$k],
                                                                        "stocktypeid"=>$tpstocktypeid[$k],
                                                                        "scraptype"=>$processoptionid,
                                                                        "productid"=>$tpproductid[$k],
                                                                        "priceid"=>$tpproductpriceid[$k],
                                                                        "qty"=>$optionvalue[$optionkey][$i],
                                                                        "unitid"=>$unitid[$optionkey][$i],
                                                                        "action"=>0,
                                                                        "createddate"=>$createddate
                                                                    );
                                            // }
                                        }

                                    }
                                }
                                if(!empty($insertScrapData)){
                                    $this->Product_process->_table = tbl_transactionproductscrapmapping;
                                    $this->Product_process->add_batch($insertScrapData);
                                }
                                /* if(!empty($updateScrapData)){
                                    $this->Product_process->_table = tbl_transactionproductscrapmapping;
                                    $this->Product_process->edit_batch($updateScrapData);
                                } */
                            }
                        }
                        if(!empty($extrachargesidarr)){
                            $insertextracharges = array();
                            foreach($extrachargesidarr as $index=>$extrachargesid){
            
                                if($extrachargesid > 0){
                                    $extrachargesname = trim($extrachargesnamearr[$index]);
                                    $extrachargestax = trim($extrachargestaxarr[$index]);
                                    $extrachargeamount = trim($extrachargeamountarr[$index]);
                                    $extrachargepercentage = trim($extrachargepercentagearr[$index]);
            
                                    if($extrachargeamount > 0){
            
                                        $insertextracharges[] = array("type"=>4,
                                                                "referenceid" => $ProductProcessID,
                                                                "extrachargesid" => $extrachargesid,
                                                                "extrachargesname" => $extrachargesname,
                                                                "extrachargepercentage" => $extrachargepercentage,
                                                                "taxamount" => $extrachargestax,
                                                                "amount" => $extrachargeamount,
                                                                "createddate" => $createddate,
                                                                "addedby" => $addedby
                                                            );
                                    }
                                }
                            }
                            if(!empty($insertextracharges)){
                                $this->Extra_charges->_table = tbl_extrachargemapping;
                                $this->Extra_charges->add_batch($insertextracharges);
                            }
                        }
                    }
        
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
        
                        $this->Product_process->_table = tbl_processgroupmapping;
                        $this->Product_process->_fields = "(SELECT name FROM ".tbl_process." WHERE id=processid) as process";
                        $this->Product_process->_where = array("id"=>$processgroupmappingid);
                        $data = $this->Product_process->getRecordsById(); 
        
                        $this->general_model->addActionLog(1,'Product Process','Add new '.$data['process'].' product process.');
                    }
        
                    $productionplanqtydetailarray = array();
                    if(!empty($productionplanqtydetail)){
                        foreach($productionplanqtydetail as $row){
        
                            $productionplanqtydetailarray[] = array(
                                "productprocessid"=>$ProductProcessID,
                                "productionplandetailid"=>$row['productionplandetailid'],
                                "productid"=>$row['productid'],
                                "priceid"=>$row['priceid'],
                                "quantity"=>$row['quantity']
                            );
                        }
                    }
                    if(!empty($productionplanqtydetailarray)){
                        $this->Product_process->_table = tbl_productionplanqtydetail;
                        $this->Product_process->add_batch($productionplanqtydetailarray);
                    }
            
                    $json = array('error'=>1); // Process group inserted successfully.
                } else {
                    $json = array('error'=>0); // Process group not inserted.
                }
            }
        }
        
        echo json_encode($json);
    }
}?>