<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_commission  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_commission');
        $this->load->model('Sales_commission_model', 'Sales_commission');
        $this->load->model('User_model', 'User');
    }

    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_commission');
        $this->viewData['title'] = "Sales Commission";
        $this->viewData['module'] = "sales_commission/Sales_commission";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getUserListData($where);
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("salescommission", "pages/sales_commission.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Sales_commission->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $Checkbox = "";
            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'sales-commission/sales-commission-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'sales-commission/sales-commission-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'sales-commission/sales-commission-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Sales&nbsp;Commission","'.ADMIN_URL.'sales-commission/delete-mul-sales-commission") >'.delete_text.'</a>';

                $Checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            if($datarow->commissiontype==1){
                $text = "<br><br><b>".($datarow->flatgst==1?'With GST':'Without GST')." (".number_format($datarow->flatcommission,2)."%)</b>";
                $view = "-";
            }else{
                $text = "";
                
                $view = '<button class="btn btn-inverse btn-raised btn-sm" title="View Commission Detail" onclick="viewcommissiondetail('.$datarow->id.')">View Detail</button>';
            }

            $row[] = ++$counter;
            $row[] = ucwords($datarow->employeename);
            $row[] = $this->Commissiontype[$datarow->commissiontype].$text;
            $row[] = $view;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $Action;
            $row[] = $Checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Sales_commission->count_all(),
                        "recordsFiltered" => $this->Sales_commission->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function sales_commission_add(){
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_commission');
		$this->viewData['title'] = "Add Sales Commission";
		$this->viewData['module'] = "sales_commission/Add_sales_commission";
        
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getUserListData($where);

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductList();
        
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberByAdmin('concatnameormembercodeormobile');
    	
		$this->admin_headerlib->add_javascript("add_sales_commission","pages/Add_sales_commission.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function sales_commission_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Sales Commission";
        $this->viewData['module'] = "sales_commission/Add_sales_commission";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getUserListData($where);

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductList();
        
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveBuyerMemberByAdmin('concatnameormembercodeormobile');
        
        $salescommissiondata = $this->Sales_commission->getSalesCommissionDataByID($id);
        if(empty($salescommissiondata)){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $salescommissiondetaildata = $this->Sales_commission->getCommissionDetailBySalesCommissionID($salescommissiondata['id']);
        $salescommissiondata['salescommissiondetail'] = $salescommissiondetaildata;
        // echo "<pre>"; print_r($salescommissiondata); exit;
        $this->viewData['salescommissiondata'] = $salescommissiondata;
        
		$this->admin_headerlib->add_javascript("add_sales_commission","pages/Add_sales_commission.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_sales_commission() {
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $employeeid = $PostData['employeeid'];
        $commissiontype = $PostData['commissiontype'];

        $this->Sales_commission->_where = array("employeeid"=>$employeeid,"commissiontype"=>$commissiontype);
        $Count = $this->Sales_commission->CountRecords();
        
        $json = array();
        if($Count==0){

            $InsertData = array("employeeid"=>$employeeid,
                                "commissiontype"=>$commissiontype,
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $InsertData=array_map('trim',$InsertData);
            $SalesCommissionId = $this->Sales_commission->Add($InsertData);
            // $SalesCommissionId = 1;
            if($SalesCommissionId){
                $InsertCommissionData = $InsertMappingData =  array();
                if($commissiontype==1){
                    $InsertCommissionData[] = array("salescommissionid"=>$SalesCommissionId,
                                                    "commission"=>$PostData['flatcommission'],
                                                    "gst"=>$PostData['flatcommissiongst']
                                                );
                }elseif($commissiontype==2){
                    $productidarr = $PostData['productid'];
                    $productcommissionarr = $PostData['productcommission'];
                    $productgstarr = $PostData['productgst'];

                    if(!empty($productidarr)){
                        foreach($productidarr as $key=>$productid){
                            
                            $commission = $productcommissionarr[$key];
                            $gst = $productgstarr[$key];

                            $InsertCommissionData[] = array("salescommissionid"=>$SalesCommissionId,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );

                            $InsertMappingData[] = array("salescommissiondetailid"=>'',
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>$productid,
                                                );
                        }
                    }
                }elseif($commissiontype==3){
                    $memberidarr = $PostData['memberid'];
                    $membercommissionarr = $PostData['membercommission'];
                    $membergstarr = $PostData['membergst'];

                    if(!empty($memberidarr)){
                        foreach($memberidarr as $key=>$memberid){
                            
                            $commission = $membercommissionarr[$key];
                            $gst = $membergstarr[$key];

                            $InsertCommissionData[] = array("salescommissionid"=>$SalesCommissionId,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );

                            $InsertMappingData[] = array("salescommissiondetailid"=>'',
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>$memberid,
                                                );
                        }
                    }
                }elseif($commissiontype==4){
                    $rangestartarr = $PostData['rangestart'];
                    $rangeendarr = $PostData['rangeend'];
                    $tieredcommissionarr = $PostData['tieredcommission'];
                    $tieredgstarr = $PostData['tieredgst'];

                    if(!empty($rangestartarr)){
                        foreach($rangestartarr as $key=>$rangestart){
                            
                            $commission = $tieredcommissionarr[$key];
                            $gst = $tieredgstarr[$key];

                            $InsertCommissionData[] = array("salescommissionid"=>$SalesCommissionId,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );

                            $InsertMappingData[] = array("salescommissiondetailid"=>'',
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>0,
                                                    "startrange"=>$rangestart,
                                                    "endrange"=>$rangeendarr[$key],
                                                );
                        }
                    }
                }
                
                if(!empty($InsertCommissionData)){
                    $this->Sales_commission->_table = tbl_salescommissiondetail;
                    $this->Sales_commission->add_batch($InsertCommissionData);

                    if(!empty($InsertMappingData) && $commissiontype!=1){
                        $firstbatch_id = $this->writedb->insert_id();
                        $lastbatch_id = $firstbatch_id + (count($InsertCommissionData)-1);
                            

                        $i = 0;
                        for($n=$firstbatch_id; $n<=$lastbatch_id;$n++){
                            $InsertMappingData[$i]['salescommissiondetailid'] = $n;
                            $i++;
                        }
                    
                        $this->Sales_commission->_table = tbl_salescommissionmapping;
                        $this->Sales_commission->add_batch($InsertMappingData);
                    }
                }
                $json = array("error"=>1);
            }
        }else{
            $json = array("error"=>2);
        }
        echo json_encode($json);
    }
    public function update_sales_commission() {
        $PostData = $this->input->post();
        // print_r($PostData);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $salescommissionid = $PostData['salescommissionid'];
        $employeeid = $PostData['employeeid'];
        $commissiontype = $PostData['commissiontype'];
        $oldcommissiontype = $PostData['oldcommissiontype'];
        
        $this->Sales_commission->_where = array("id<>"=>$salescommissionid,"employeeid"=>$employeeid,"commissiontype"=>$commissiontype);
        $Count = $this->Sales_commission->CountRecords();
        
        $json = array();
        if($Count==0){

            $UpdateData = array("employeeid"=>$employeeid,
                                "commissiontype"=>$commissiontype,
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby
                            );

            $UpdateData=array_map('trim',$UpdateData);
            $this->Sales_commission->_where = array("id"=>$salescommissionid);
            $this->Sales_commission->Edit($UpdateData);
            
            if($oldcommissiontype != $commissiontype){
               
                $this->Sales_commission->_table = tbl_salescommissionmapping;
                $this->Sales_commission->Delete(array("salescommissiondetailid IN (SELECT id FROM ".tbl_salescommissiondetail." WHERE salescommissionid=".$salescommissionid.")"=>null));

                $this->Sales_commission->_table = tbl_salescommissiondetail;
                $this->Sales_commission->Delete(array("salescommissionid"=>$salescommissionid));
            }else{
                if($commissiontype!=1){
                    $olddetailid = (!empty($PostData['olddetailid']))?explode(",",$PostData['olddetailid']):'';
                    
                    $salescommissiondetailidarr = isset($PostData['salescommissiondetailid'])?$PostData['salescommissiondetailid']:array();
                    
                    $deletearr=array();
                    if(!empty($olddetailid)){
                        $deletearr = array_diff($olddetailid,$salescommissiondetailidarr);
                    }
                    
                    if(!empty($deletearr)){
                        $this->Sales_commission->_table = tbl_salescommissionmapping;
                        $this->Sales_commission->Delete(array("salescommissiondetailid IN (".implode(",",$deletearr).")"=>null));

                        $this->Sales_commission->_table = tbl_salescommissiondetail;
                        $this->Sales_commission->Delete(array("id IN (".implode(",",$deletearr).")"=>null));
                    }
                }
            }
            $InsertCommissionData = $InsertMappingData = $UpdateCommissionData = $UpdateMappingData = array();
            if($commissiontype==1){
                if(!empty($PostData['salescommissiondetailid'])){
                    $UpdateCommissionData[] = array("id"=>$PostData['salescommissiondetailid'],
                                                    "commission"=>$PostData['flatcommission'],
                                                    "gst"=>$PostData['flatcommissiongst']
                                                );
                }else{
                    $InsertCommissionData[] = array("salescommissionid"=>$salescommissionid,
                                                    "commission"=>$PostData['flatcommission'],
                                                    "gst"=>$PostData['flatcommissiongst']
                                                );
                }
            }elseif($commissiontype==2){
                $productidarr = $PostData['productid'];
                $productcommissionarr = $PostData['productcommission'];
                $productgstarr = $PostData['productgst'];
                $salescommissiondetailidarr = isset($PostData['salescommissiondetailid'])?$PostData['salescommissiondetailid']:'';
                $salescommissionmappingidarr = isset($PostData['salescommissionmappingid'])?$PostData['salescommissionmappingid']:'';

                if(!empty($productidarr)){
                    foreach($productidarr as $key=>$productid){
                        
                        $detailid = !empty($salescommissiondetailidarr[$key])?$salescommissiondetailidarr[$key]:0;
                        $mappingid = !empty($salescommissionmappingidarr[$key])?$salescommissionmappingidarr[$key]:0;
                        $commission = $productcommissionarr[$key];
                        $gst = $productgstarr[$key];

                        if(empty($detailid)){

                            $InsertCommissionData[] = array("salescommissionid"=>$salescommissionid,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );
    
                            $InsertMappingData[] = array("salescommissiondetailid"=>'',
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>$productid,
                                                );
                        }else{
                            $UpdateCommissionData[] = array("id"=>$detailid,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );
    
                            $UpdateMappingData[] = array("id"=>$mappingid,
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>$productid,
                                                );
                        }
                    }
                }
            }elseif($commissiontype==3){
                $memberidarr = $PostData['memberid'];
                $membercommissionarr = $PostData['membercommission'];
                $membergstarr = $PostData['membergst'];
                $salescommissiondetailidarr = isset($PostData['salescommissiondetailid'])?$PostData['salescommissiondetailid']:'';
                $salescommissionmappingidarr = isset($PostData['salescommissionmappingid'])?$PostData['salescommissionmappingid']:'';

                if(!empty($memberidarr)){
                    foreach($memberidarr as $key=>$memberid){
                        
                        $detailid = !empty($salescommissiondetailidarr[$key])?$salescommissiondetailidarr[$key]:0;
                        $mappingid = !empty($salescommissionmappingidarr[$key])?$salescommissionmappingidarr[$key]:0;
                        $commission = $membercommissionarr[$key];
                        $gst = $membergstarr[$key];

                        if(empty($detailid)){
                            $InsertCommissionData[] = array("salescommissionid"=>$salescommissionid,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );

                            $InsertMappingData[] = array("salescommissiondetailid"=>'',
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>$memberid,
                                                );
                        }else{
                            $UpdateCommissionData[] = array("id"=>$detailid,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );
    
                            $UpdateMappingData[] = array("id"=>$mappingid,
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>$memberid,
                                                );
                        }
                    }
                }
            }elseif($commissiontype==4){
                $rangestartarr = $PostData['rangestart'];
                $rangeendarr = $PostData['rangeend'];
                $tieredcommissionarr = $PostData['tieredcommission'];
                $tieredgstarr = $PostData['tieredgst'];
                $salescommissiondetailidarr = isset($PostData['salescommissiondetailid'])?$PostData['salescommissiondetailid']:'';
                $salescommissionmappingidarr = isset($PostData['salescommissionmappingid'])?$PostData['salescommissionmappingid']:'';

                if(!empty($rangestartarr)){
                    foreach($rangestartarr as $key=>$rangestart){
                        
                        $detailid = !empty($salescommissiondetailidarr[$key])?$salescommissiondetailidarr[$key]:0;
                        $mappingid = !empty($salescommissionmappingidarr[$key])?$salescommissionmappingidarr[$key]:0;
                        $commission = $tieredcommissionarr[$key];
                        $gst = $tieredgstarr[$key];

                        if(empty($detailid)){
                            $InsertCommissionData[] = array("salescommissionid"=>$salescommissionid,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );

                            $InsertMappingData[] = array("salescommissiondetailid"=>'',
                                                    "referencetype"=>$commissiontype,
                                                    "referenceid"=>0,
                                                    "startrange"=>$rangestart,
                                                    "endrange"=>$rangeendarr[$key],
                                                );

                        }else{
                            $UpdateCommissionData[] = array("id"=>$detailid,
                                                    "commission"=>$commission,
                                                    "gst"=>$gst
                                                );
    
                            $UpdateMappingData[] = array("id"=>$mappingid,
                                                        "referencetype"=>$commissiontype,
                                                        "referenceid"=>0,
                                                        "startrange"=>$rangestart,
                                                        "endrange"=>$rangeendarr[$key],
                                                    );
                        }
                    }
                }
            }
            
            if(!empty($InsertCommissionData)){
                $this->Sales_commission->_table = tbl_salescommissiondetail;
                $this->Sales_commission->add_batch($InsertCommissionData);

                if(!empty($InsertMappingData) && $commissiontype!=1){
                    $firstbatch_id = $this->writedb->insert_id();
                    $lastbatch_id = $firstbatch_id + (count($InsertCommissionData)-1);
                        

                    $i = 0;
                    for($n=$firstbatch_id; $n<=$lastbatch_id;$n++){
                        $InsertMappingData[$i]['salescommissiondetailid'] = $n;
                        $i++;
                    }
                
                    $this->Sales_commission->_table = tbl_salescommissionmapping;
                    $this->Sales_commission->add_batch($InsertMappingData);
                }
            }
            if(!empty($UpdateCommissionData)){
                $this->Sales_commission->_table = tbl_salescommissiondetail;
                $this->Sales_commission->edit_batch($UpdateCommissionData,"id");
            }
            if(!empty($UpdateMappingData)){
                $this->Sales_commission->_table = tbl_salescommissionmapping;
                $this->Sales_commission->edit_batch($UpdateMappingData,"id");
            }
            $json = array("error"=>1);
        }else{
            $json = array("error"=>2);
        }
        echo json_encode($json);
    }
    public function sales_commission_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();

        $check = $this->Sales_commission->checkCommissionEnable($PostData['id']);
        if(($check < 1 && $PostData['val']==0) || $PostData['val']==1){
            
            $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
            $this->Sales_commission->_where = array("id" => $PostData['id']);
            $this->Sales_commission->Edit($updatedata);
        }
        if($PostData['val']==1){

            $this->Sales_commission->_where = array("id"=>$PostData['id']);
            $data = $this->Sales_commission->getRecordsById();

            $updatedata = array("status" => 0, "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
            $this->Sales_commission->_where = array("id<>"=>$PostData['id'],"employeeid"=>$data['employeeid']);
            $this->Sales_commission->Edit($updatedata);
        }
		
        echo $PostData['id'];
    }
    public function delete_mul_sales_commission() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
           
            $this->Sales_commission->_table = tbl_salescommissiondetail;
            $this->Sales_commission->_fields = "GROUP_CONCAT(id) as detailid";
            $this->Sales_commission->_where = array('salescommissionid'=>$row);
            $commissiondata = $this->Sales_commission->getRecordsById();
            if(!empty($commissiondata)){
                $this->Sales_commission->_table = tbl_salescommissionmapping;
                $this->Sales_commission->Delete(array('FIND_IN_SET(salescommissiondetailid, "'.$commissiondata['detailid'].'")'=>null));
            }
            $this->Sales_commission->_table = tbl_salescommissiondetail;
            $this->Sales_commission->Delete(array('salescommissionid'=>$row));

            $this->Sales_commission->_table = tbl_salescommission;
            $this->Sales_commission->Delete(array('id'=>$row));
        }
    }

    public function getSalesCommissionDataById(){
        $PostData = $this->input->post();
        $salescommissionid = $PostData['salescommissionid'];

        $salescommissiondetaildata = $this->Sales_commission->getCommissionDetailBySalesCommissionID($salescommissionid);
        $data = array();
        if(!empty($salescommissiondetaildata)){
            foreach($salescommissiondetaildata as $row){
                
                if($row['referencetype'] == 2){
                    $reference = ucwords($row['productname']);
                }else if($row['referencetype'] == 3){
                    $reference = ucwords($row['membername']);
                }else if($row['referencetype'] == 4){
                    $reference = "<b>".CURRENCY_CODE."</b> ".$row['startrange']." to <b>".CURRENCY_CODE."</b> ".$row['endrange'];
                }
                $data[] = array(
                    "employeename"=>$row['employeename'],
                    "commissiontype"=>$row['referencetype'],
                    "typename"=>$this->Commissiontype[$row['referencetype']],
                    "reference"=>$reference,
                    "commission"=>$row['commission'],
                    "gst"=>$row['gst'],
                );
            }
        }
        echo json_encode($data);
    }
}

?>