<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Assigned_route  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Assigned_route');
        $this->load->model('Assigned_route_model', 'Assigned_route');
        $this->load->model('User_model', 'User');
    }
    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Assigned_route');
        $this->viewData['title'] = "Assigned Route";
        $this->viewData['module'] = "assigned_route/Assigned_route";

        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->load->model('Route_model', 'Route');
        $this->viewData['routedata'] = $this->Route->getRouteList();

        $this->viewData['assignedbydata'] = $this->Assigned_route->getAssignedByUserList();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Assigned Route','View assigned route.');
        }
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

        $this->admin_headerlib->add_javascript("assigned_route", "pages/assigned_route.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Assigned_route->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $Checkbox = $viewList = "";
            $status = $datarow->status;

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'assigned-route/assigned-route-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Assigned&nbsp;Route","'.ADMIN_URL.'assigned-route/delete-mul-assigned-route") >'.delete_text.'</a>';

                $Checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            $viewRouteList = '<a class="btn btn-info btn-raised btn-xs" title="View Route List" onclick="viewroutelist('.$datarow->id.')">View</a>';

            $viewProductList = '<button class="btn btn-info btn-raised btn-xs" title="View Product List" onclick="viewproductlist('.$datarow->id.')">View</button>';
            
            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning btn-raised '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">'.$this->Routestatus[$status].' <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                            <li class="active" id="dropdown-menu">
                                <a>'.$this->Routestatus[0].'</a>
                            </li>
                              <li id="dropdown-menu">
                                <a onclick="chageroutestatus(1,'.$datarow->id.')">'.$this->Routestatus[1].'</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageroutestatus(2,'.$datarow->id.')">'.$this->Routestatus[2].'</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageroutestatus(3,'.$datarow->id.')">'.$this->Routestatus[3].'</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageroutestatus(4,'.$datarow->id.')">'.$this->Routestatus[4].'</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chageroutestatus(5,'.$datarow->id.')">'.$this->Routestatus[5].'</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn btn-warning btn-raised '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">'.$this->Routestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(0,'.$datarow->id.')">'.$this->Routestatus[0].'</a>
                            </li>
                            <li class="active" id="dropdown-menu">
                                <a>'.$this->Routestatus[1].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(2,'.$datarow->id.')">'.$this->Routestatus[2].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(3,'.$datarow->id.')">'.$this->Routestatus[3].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(4,'.$datarow->id.')">'.$this->Routestatus[4].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(5,'.$datarow->id.')">'.$this->Routestatus[5].'</a>
                            </li>
                          </ul>';
            }else if($status == 2){
                $dropdownmenu = '<button class="btn btn-primary btn-raised '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">'.$this->Routestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(0,'.$datarow->id.')">'.$this->Routestatus[0].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(1,'.$datarow->id.')">'.$this->Routestatus[1].'</a>
                            </li>
                            <li class="active" id="dropdown-menu">
                                <a>'.$this->Routestatus[2].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(3,'.$datarow->id.')">'.$this->Routestatus[3].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(4,'.$datarow->id.')">'.$this->Routestatus[4].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(5,'.$datarow->id.')">'.$this->Routestatus[5].'</a>
                            </li>
                          </ul>';
            }else if($status == 3){
                $dropdownmenu = '<button class="btn btn-info btn-raised '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">'.$this->Routestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(0,'.$datarow->id.')">'.$this->Routestatus[0].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(1,'.$datarow->id.')">'.$this->Routestatus[1].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(2,'.$datarow->id.')">'.$this->Routestatus[2].'</a>
                            </li>
                            <li class="active" id="dropdown-menu">
                                <a>'.$this->Routestatus[3].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(4,'.$datarow->id.')">'.$this->Routestatus[4].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(5,'.$datarow->id.')">'.$this->Routestatus[5].'</a>
                            </li>
                        </ul>';
            }else if($status == 4){
                $dropdownmenu = '<button class="btn btn-success btn-raised '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">'.$this->Routestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(0,'.$datarow->id.')">'.$this->Routestatus[0].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(1,'.$datarow->id.')">'.$this->Routestatus[1].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(2,'.$datarow->id.')">'.$this->Routestatus[2].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(3,'.$datarow->id.')">'.$this->Routestatus[3].'</a>
                            </li>
                            <li class="active" id="dropdown-menu">
                                <a>'.$this->Routestatus[4].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(5,'.$datarow->id.')">'.$this->Routestatus[5].'</a>
                            </li>
                        </ul>';
            }else if($status == 5){
                $dropdownmenu = '<button class="btn btn-danger btn-raised '.STATUS_DROPDOWN_BTN.' dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">'.$this->Routestatus[$status].' <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(0,'.$datarow->id.')">'.$this->Routestatus[0].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(1,'.$datarow->id.')">'.$this->Routestatus[1].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(2,'.$datarow->id.')">'.$this->Routestatus[2].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(3,'.$datarow->id.')">'.$this->Routestatus[3].'</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chageroutestatus(4,'.$datarow->id.')">'.$this->Routestatus[4].'</a>
                            </li>
                            <li class="active" id="dropdown-menu">
                                <a>'.$this->Routestatus[5].'</a>
                            </li>
                        </ul>';
            }
            $routestatus = '<div class="dropdown">'.$dropdownmenu.'</div>';

            $row[] = ++$counter;
            $row[] = ucfirst($datarow->route);
            $row[] = ucfirst($datarow->salespersonname);
            $row[] = ucfirst($datarow->vehicle);
            $row[] = $this->general_model->displaydate($datarow->startdate);
            $row[] = $this->general_model->displaydatetime($datarow->time,'h:i');
            $row[] = $viewRouteList;
            $row[] = $viewProductList;
            $row[] = $routestatus;
            $row[] = ucfirst($datarow->assignedby);
            $row[] = $Action;
            $row[] = $Checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Assigned_route->count_all(),
                        "recordsFiltered" => $this->Assigned_route->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function assigned_route_add(){
        $this->viewData = $this->getAdminSettings('submenu', 'Assigned_route');
		$this->viewData['title'] = "Add Assigned Route";
		$this->viewData['module'] = "assigned_route/Add_assigned_route";
        
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductActiveList();

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->admin_headerlib->add_stylesheet("bootstrap-datetimepicker","bootstrap-datetimepicker.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_assigned_route","pages/add_assigned_route.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function assigned_route_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Assigned Route";
        $this->viewData['module'] = "assigned_route/Add_assigned_route";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['assignedroutedata'] = $this->Assigned_route->getAssignedRouteDataByID($id);
        if(empty($this->viewData['assignedroutedata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $this->viewData['extraproductdata'] = $this->Assigned_route->getExtraProductsByAssignedRouteID($id);
        
		$where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID')." or reportingto=(select reportingto from ".tbl_user." where id=".$this->session->userdata(base_url().'ADMINID')."))"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductActiveList();

        $this->admin_headerlib->add_stylesheet("bootstrap-datetimepicker","bootstrap-datetimepicker.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("add_assigned_route","pages/add_assigned_route.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_assigned_route() {
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

        $this->load->model('Route_model', 'Route');
       
        $employeeid = $PostData['employeeid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $vehicleid = $PostData['vehicleid'];
        $capacity = $PostData['capacity'];
        $startdate = ($PostData['startdate']!="")?$this->general_model->convertdate($PostData['startdate']):"";
        $time = $PostData['time'];
        $totalweight = $PostData['totalweight'];
        $loosmoney = $PostData['loosmoney'];
        $routeid = $PostData['routeid'];
        
        $InsertData = array("employeeid"=>$employeeid,
                            "provinceid"=>$provinceid,
                            "cityid"=>$cityid,
                            "routeid"=>$routeid,
                            "vehicleid"=>$vehicleid,
                            "capacity"=>$capacity,
                            "startdate"=>$startdate,
                            "time"=>$time,
                            "totalweight"=>$totalweight,  
                            "loosmoney"=>$loosmoney,  
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate,
                            "addedby"=>$addedby,
                            "modifiedby"=>$addedby
                        );

        $InsertData=array_map('trim',$InsertData);
        $AssignedRouteId = $this->Assigned_route->Add($InsertData);
        $routename = $this->Route->getRouteDataByID($routeid)['route'];

        $trackroutedata = array(
            'employeeid'=>$employeeid,
            'assignedrouteid'=>$AssignedRouteId,
            'routename'=>$routename,
            'addedby' => $employeeid,
            'modifiedby' => $employeeid,
        );
        $trackroutedata=array_map('trim',$trackroutedata);
        $this->Route->_table = tbl_salespersonroute;
        $this->Route->Add($trackroutedata);
        
        if($AssignedRouteId){
            $InsertInvoiceData = $InsertProductData = array();

            $memberidarr = isset($PostData['memberid'])?$PostData['memberid']:"";
            $invoiceidarr = isset($PostData['invoiceid'])?$PostData['invoiceid']:"";

            if(!empty($memberidarr)){
                $this->load->model('Member_model', 'Member');
                foreach($memberidarr as $k=>$memberid){

                    $check = $this->Member->CheckSalesPersonsByMemberId($memberid,$employeeid);
                    $channelid = $this->Member->GetChannelIdByMemberId($memberid);

                    if($check==0){
                        $UpdateSalesPerson = array(
                            "employeeid"=>$employeeid,
                            "channelid"=>$channelid,
                            "memberid"=>$memberid,
                            "createddate"=>$createddate,
                            "modifieddate"=>$createddate,
                            "addedby"=>$addedby,
                            "modifiedby"=>$addedby
                        ); 
                        // $this->Member->_where = array('id' =>$memberid);
                        $this->Member->_table = tbl_salespersonmember;
                        $this->Member->Add($UpdateSalesPerson);
                    }

                    if(!empty($invoiceidarr)){
                        $invoicememberid = array();
                        foreach($invoiceidarr as $k=>$invoiceid){
                            $invoice = explode("_",$invoiceid);
                            if($invoice[0] == $memberid){
                                $InsertInvoiceData[] = array(
                                    "assignedrouteid"=>$AssignedRouteId,
                                    "memberid"=>$invoice[0],
                                    "invoiceid"=>$invoice[1]
                                ); 
                            }

                            $invoicememberid[] = $invoice[0];
                        }

                        if(!in_array($memberid,$invoicememberid)){
                            $InsertInvoiceData[] = array(
                                "assignedrouteid"=>$AssignedRouteId,
                                "memberid"=>$memberid,
                                "invoiceid"=>0
                            ); 
                        }
                    }else{
                        $InsertInvoiceData[] = array(
                            "assignedrouteid"=>$AssignedRouteId,
                            "memberid"=>$memberid,
                            "invoiceid"=>0
                        );
                    }
                }
                if(!empty($InsertInvoiceData)){
                    $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                    $this->Assigned_route->add_batch($InsertInvoiceData);
                }
            }
           
            $productidarr = isset($PostData['productid'])?$PostData['productid']:"";
            
            if(!empty($productidarr)){
                $priceid = $PostData['priceid'];
                $qty = $PostData['qty'];
                $price = $PostData['price'];
                $tax = $PostData['tax'];
                $totalprice = $PostData['totalprice'];

                foreach($productidarr as $k=>$productid){
                    
                    if(!empty($productid) && !empty($priceid[$k]) && !empty($qty[$k]) && !empty($price[$k])){
                        
                        $InsertProductData[] = array(
                            "assignedrouteid"=>$AssignedRouteId,
                            "productid"=>$productid,
                            "priceid"=>$priceid[$k],
                            "quantity"=>$qty[$k],
                            "price"=>$price[$k],
                            "tax"=>$tax[$k],
                            "totalprice"=>$totalprice[$k],
                        );
                    }
                }
                if(!empty($InsertProductData)){
                    $this->Assigned_route->_table = tbl_assignedrouteextraproduct;
                    $this->Assigned_route->add_batch($InsertProductData);
                }
            }
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(1,'Assigned_route','Add new assigned route.');
            }
            $json = array("error"=>1);
        }else{
            $json = array("error"=>0);
        }
    
        echo json_encode($json);
    }
    public function update_assigned_route() {
        $PostData = $this->input->post();
        // print_r($PostData);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $assignedrouteid = $PostData['id'];
        $employeeid = $PostData['employeeid'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $vehicleid = $PostData['vehicleid'];
        $capacity = $PostData['capacity'];
        $startdate = ($PostData['startdate']!="")?$this->general_model->convertdate($PostData['startdate']):"";
        $time = $PostData['time'];
        $totalweight = $PostData['totalweight'];
        $loosmoney = $PostData['loosmoney'];
        $routeid = $PostData['routeid'];
        
        $UpdateData = array("employeeid"=>$employeeid,
                        "provinceid"=>$provinceid,
                        "cityid"=>$cityid,
                        "routeid"=>$routeid,
                        "vehicleid"=>$vehicleid,
                        "capacity"=>$capacity,
                        "startdate"=>$startdate,
                        "time"=>$time,
                        "totalweight"=>$totalweight,  
                        "loosmoney"=>$loosmoney,  
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby
                    );
        $UpdateData=array_map('trim',$UpdateData);
        $this->Assigned_route->_where = array("id"=>$assignedrouteid);
        $isUpdated = $this->Assigned_route->Edit($UpdateData);

        /* $routename = $this->Route->getRouteDataByID($routeid)['route'];

        $trackroutedata = array(
            'employeeid'=>$employeeid,
            'assignedrouteid'=>$assignedrouteid,
            'routename'=>$routename,
            'addedby' => $employeeid,
            'modifiedby' => $employeeid,
        );
        $trackroutedata=array_map('trim',$trackroutedata);
        $this->Route->_table = tbl_salespersonroute;
        $this->Route->Add($trackroutedata); */
        
        if($isUpdated){

            $InsertInvoiceData = $InsertProductData = $deleteProductData = array();
            $memberidarr = isset($PostData['memberid'])?$PostData['memberid']:"";
            $invoiceidarr = isset($PostData['invoiceid'])?$PostData['invoiceid']:"";

            $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
            $this->Assigned_route->Delete(array("assignedrouteid"=>$assignedrouteid));
        
            if(!empty($memberidarr)){
                $this->load->model('Member_model', 'Member');
                foreach($memberidarr as $k=>$memberid){

                    $check = $this->Member->CheckSalesPersonsByMemberId($memberid,$employeeid);
                    $channelid = $this->Member->GetChannelIdByMemberId($memberid);

                    if($check==0){
                        $UpdateSalesPerson = array(
                            "employeeid"=>$employeeid,
                            "channelid"=>$channelid,
                            "memberid"=>$memberid,
                            "modifieddate"=>$modifieddate,
                            "modifiedby"=>$modifiedby
                        ); 
                        // $this->Member->_where = array('id' =>$memberid);
                        $this->Member->_table = tbl_salespersonmember;
                        $this->Member->Add($UpdateSalesPerson);
                    }
                    
                    if(!empty($invoiceidarr)){
                        $invoicememberid = array();
                        foreach($invoiceidarr as $k=>$invoiceid){
                            $invoice = explode("_",$invoiceid);
                            if($invoice[0] == $memberid){
                                $InsertInvoiceData[] = array(
                                    "assignedrouteid"=>$assignedrouteid,
                                    "memberid"=>$invoice[0],
                                    "invoiceid"=>$invoice[1]
                                ); 
                            }

                            $invoicememberid[] = $invoice[0];
                        }

                        if(!in_array($memberid,$invoicememberid)){
                            $InsertInvoiceData[] = array(
                                "assignedrouteid"=>$assignedrouteid,
                                "memberid"=>$memberid,
                                "invoiceid"=>0
                            ); 
                        }
                    }else{
                        $InsertInvoiceData[] = array(
                            "assignedrouteid"=>$assignedrouteid,
                            "memberid"=>$memberid,
                            "invoiceid"=>0
                        );
                    }
                }
                if(!empty($InsertInvoiceData)){
                    $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
                    $this->Assigned_route->add_batch($InsertInvoiceData);
                }
            }

            $productidarr = isset($PostData['productid'])?$PostData['productid']:"";
            $this->Assigned_route->_table = tbl_assignedrouteextraproduct;
            $this->Assigned_route->_where = array("assignedrouteid"=>$assignedrouteid);
            $extraproductData = $this->Assigned_route->getRecordById();
            $extraproductids = array_column($extraproductData, 'id'); 
            
            if(!empty($productidarr)){
                $priceid = $PostData['priceid'];
                $qty = $PostData['qty'];
                $price = $PostData['price'];
                $tax = $PostData['tax'];
                $totalprice = $PostData['totalprice'];

                foreach($productidarr as $k=>$productid){
                    
                    if(!empty($productid) && !empty($priceid[$k]) && !empty($qty[$k]) && !empty($price[$k])){
                        
                        $extraproductid = isset($PostData['extraproductid'][$k])?$PostData['extraproductid'][$k]:0;

                        if(!empty($extraproductid)){
                        
                            $UpdateProductData[] = array(
                                "id"=>$extraproductid,
                                "productid"=>$productid,
                                "priceid"=>$priceid[$k],
                                "quantity"=>$qty[$k],
                                "price"=>$price[$k],
                                "tax"=>$tax[$k],
                                "totalprice"=>$totalprice[$k],
                            );
    
                            $deleteProductData[] = $extraproductid;
                        }else{
                            $InsertProductData[] = array(
                                "assignedrouteid"=>$assignedrouteid,
                                "productid"=>$productid,
                                "priceid"=>$priceid[$k],
                                "quantity"=>$qty[$k],
                                "price"=>$price[$k],
                                "tax"=>$tax[$k],
                                "totalprice"=>$totalprice[$k],
                            );
                        }
                    }
                }
                if(!empty($extraproductids)){
                    $deletearr = array_diff($extraproductids,$deleteProductData);
                }
                if(!empty($deletearr)){
                    $this->Assigned_route->_table = tbl_assignedrouteextraproduct;
                    $this->Assigned_route->Delete(array("id IN (".implode(",",$deletearr).")"=>null));
                }
                if(!empty($InsertProductData)){
                    $this->Assigned_route->_table = tbl_assignedrouteextraproduct;
                    $this->Assigned_route->add_batch($InsertProductData);
                }
                if(!empty($UpdateProductData)){
                    $this->Assigned_route->_table = tbl_assignedrouteextraproduct;
                    $this->Assigned_route->edit_batch($UpdateProductData,"id");
                }
            }
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Assigned_route','Edit assigned route.');
            }
            $json = array("error"=>1);
        }else{
            $json = array("error"=>0);
        }
           
        echo json_encode($json);
    }
    public function delete_mul_assigned_route() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
           
            $this->Assigned_route->_table = tbl_assignedrouteextraproduct;
            $this->Assigned_route->Delete(array('assignedrouteid'=>$row));

            $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
            $this->Assigned_route->Delete(array('assignedrouteid'=>$row));

            $this->Assigned_route->_table = tbl_assignedroute;
            $this->Assigned_route->Delete(array('id'=>$row));

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Assigned_route','Delete assigned route.');
            }
        }
    }
    public function delete_mul_route() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
           
            $this->Assigned_route->_table = tbl_assignedrouteinvoicemapping;
            $this->Assigned_route->Delete(array('id'=>$row));

        }
    }
    public function getAssignedRouteProductList(){
        $PostData = $this->input->post();
        $assignedrouteid = $PostData['assignedrouteid'];
        
        $productdata = $this->Assigned_route->getAssignedRouteProductList($assignedrouteid);
        echo json_encode($productdata);
    }
    public function getAssignedRouteList(){
        $PostData = $this->input->post();
        $assignedrouteid = $PostData['assignedrouteid'];
        
        $routedata = $this->Assigned_route->getAssignedRouteList($assignedrouteid);
        echo json_encode($routedata);
    }
    public function update_route_status(){
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $assignedrouteid = $PostData['assignedrouteid'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        
        $this->Assigned_route->_where = array("id" => $assignedrouteid);
        $update = $this->Assigned_route->Edit($updateData);
        if($update) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Assigned Route','Change status of assigned route.');
            }
            echo 1;    
        }else{
            echo 0;
        }
    }
    public function getVehicleByEmployeeId(){
        $PostData = $this->input->post();
        $employeeid = $PostData['employeeid'];
        
        $vehicledata = $this->Assigned_route->getVehicleByEmployeeId($employeeid);
        echo json_encode($vehicledata);
    }
    public function getRouteByEmployee(){
        $PostData = $this->input->post();
        $employeeid = $PostData['employeeid'];
        
        $routedata = $this->Assigned_route->getRouteByEmployee($employeeid);
        echo json_encode($routedata);
    }
}

?>