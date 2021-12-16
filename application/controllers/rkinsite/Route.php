<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Route  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Route');
        $this->load->model('Route_model', 'Route');
        $this->load->model('User_model', 'User');
    }
    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Route');
        $this->viewData['title'] = "Route";
        $this->viewData['module'] = "route/Route";

        $this->viewData['routedata'] = $this->Route->getRouteList();
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Route','View route.');
        }
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript("route", "pages/route.js");
        $this->load->view(ADMINFOLDER.'template', $this->viewData);
    }
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Route->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $Checkbox = $viewList = "";

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'route/route-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';

                $viewList = '<button class="btn btn-inverse btn-raised btn-sm" title="View Edit '.Member_label.' List" onclick="viewmemberlist('.$datarow->id.')">View Edit</button>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Route","'.ADMIN_URL.'route/delete-mul-route") >'.delete_text.'</a>';

                $Checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            if($datarow->totaltime != "00:00:00"){
                $totaltime = $this->general_model->convertTimetoWords($datarow->totaltime);
            }else{
                $totaltime = "-";
            }
            $row[] = ++$counter;
            $row[] = ucfirst($datarow->route);
            $row[] = $totaltime;
            $row[] = ($datarow->totalkm>0)?$datarow->totalkm." KM":"-";
            $row[] = $viewList;
            $row[] = $Action;
            $row[] = $Checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Route->count_all(),
                        "recordsFiltered" => $this->Route->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function route_add(){
        $this->viewData = $this->getAdminSettings('submenu', 'Route');
		$this->viewData['title'] = "Add Route";
		$this->viewData['module'] = "route/Add_route";
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_stylesheet("bootstrap-datetimepicker","bootstrap-datetimepicker.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript("add_route","pages/add_route.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function route_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Route";
        $this->viewData['module'] = "route/Add_route";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $this->viewData['routedata'] = $this->Route->getRouteDataByID($id);
        if(empty($this->viewData['routedata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $this->viewData['routememberdata'] = $this->Route->getRouteMemberDataByRouteID($id);
        
		$this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_stylesheet("bootstrap-datetimepicker","bootstrap-datetimepicker.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript("add_route","pages/add_route.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function add_route() {
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $route = $PostData['route'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $totaltime = $PostData['totaltime'];
        $totalkm = $PostData['totalkm'];
        
        $this->Route->_where = array("route"=>$route,"provinceid"=>$provinceid,"cityid"=>$cityid);
        $Count = $this->Route->CountRecords();
        
        $json = array();
        if($Count==0){

            $InsertData = array("route"=>$route,
                                "totaltime"=>$totaltime,
                                "totalkm"=>$totalkm,
                                "cityid"=>$cityid,
                                "provinceid"=>$provinceid,
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $InsertData=array_map('trim',$InsertData);
            $RouteId = $this->Route->Add($InsertData);
            
            if($RouteId){
                $InsertData =  array();

                $channelidarr = $PostData['channelid'];
                $memberid = $PostData['memberid'];
                $priority = $PostData['priority'];
                
                if(!empty($channelidarr)){
                    foreach($channelidarr as $k=>$channelid){

                        $active = isset($PostData['active'.($k+1)])?1:0;
                        if(!empty($channelid) && !empty($memberid[$k])){
                            $InsertData[] = array(
                                "routeid"=>$RouteId,
                                "channelid"=>$channelid,
                                "memberid"=>$memberid[$k],
                                "priority"=>$priority[$k],
                                "active"=>$active,
                            );
                        }
                    }
                    if(!empty($InsertData)){
                        $this->Route->_table = tbl_routemember;
                        $this->Route->add_batch($InsertData);
                    }
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Route','Add new '.$route.' route.');
                }
                $json = array("error"=>1);
            }
        }else{
            $json = array("error"=>2);
        }
        echo json_encode($json);
    }
    public function update_route() {
        $PostData = $this->input->post();
        // print_r($PostData);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $routeid = $PostData['id'];
        $route = $PostData['route'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $totaltime = $PostData['totaltime'];
        $totalkm = $PostData['totalkm'];
        
        $this->Route->_where = array("id<>"=>$routeid,"route"=>$route,"provinceid"=>$provinceid,"cityid"=>$cityid);
        $Count = $this->Route->CountRecords();
        
        $json = array();
        if($Count==0){

            $UpdateData = array("route"=>$route,
                                "totaltime"=>$totaltime,
                                "totalkm"=>$totalkm,
                                "cityid"=>$cityid,
                                "provinceid"=>$provinceid,
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby
                            );

            $UpdateData=array_map('trim',$UpdateData);
            $this->Route->_where = array("id"=>$routeid);
            $isUpdated = $this->Route->Edit($UpdateData);
            
            if($isUpdated){

                if(isset($PostData['removeroutememberid']) && $PostData['removeroutememberid']!=''){
                    $query=$this->readdb->select("id")
                                    ->from(tbl_routemember)
                                    ->where("FIND_IN_SET(id,'".implode(',',array_filter(explode(",",$PostData['removeroutememberid'])))."')>0")
                                    ->get();
                    $routememberData = $query->result_array();
                    
                    if(!empty($routememberData)){
                        foreach ($routememberData as $row) {
                            $this->Route->_table = tbl_routemember;
                            $this->Route->Delete(array("id"=>$row['id']));
                        }
                    }
                }
                
                $InsertData = $updateData = $deleteData = array();
                $channelidarr = $PostData['channelid'];
                $memberid = $PostData['memberid'];
                $priority = $PostData['priority'];
                
                if(!empty($channelidarr)){
                    foreach($channelidarr as $k=>$channelid){

                        $routememberid = isset($PostData['routememberid'][$k])?$PostData['routememberid'][$k]:0;
                        $active = isset($PostData['active'.($k+1)])?1:0;

                        if(!empty($channelid) && !empty($memberid[$k])){

                            if(!empty($routememberid)){
                                
                                $updateData[] = array(
                                    "id"=>$routememberid,
                                    "channelid"=>$channelid,
                                    "memberid"=>$memberid[$k],
                                    "priority"=>$priority[$k],
                                    "active"=>$active
                                );
                            }else{
                                $InsertData[] = array(
                                    "routeid"=>$routeid,
                                    "channelid"=>$channelid,
                                    "memberid"=>$memberid[$k],
                                    "priority"=>$priority[$k],
                                    "active"=>$active
                                );
                            }
                        }else{
                            if(!empty($routememberid)){
                                $deleteData[] = $routememberid;
                            }
                        }
                    }
                    if(!empty($InsertData)){
                        $this->Route->_table = tbl_routemember;
                        $this->Route->add_batch($InsertData);
                    }
                    if(!empty($updateData)){
                        $this->Route->_table = tbl_routemember;
                        $this->Route->edit_batch($updateData,"id");
                    }
                    if(!empty($deleteData)){
                        $this->Route->_table = tbl_routemember;
                        $this->Route->Delete(array("id IN (".implode(",",$deleteData).")"=>null));
                    }
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Route','Edit '.$route.' route.');
                }
                $json = array("error"=>1);
            }else{
                $json = array("error"=>0);
            }
           /*  $InsertCommissionData = $InsertMappingData = $UpdateCommissionData = $UpdateMappingData = array();
          
            if(!empty($UpdateCommissionData)){
                $this->Route->_table = tbl_salescommissiondetail;
                $this->Route->edit_batch($UpdateCommissionData,"id");
            }
            if(!empty($UpdateMappingData)){
                $this->Route->_table = tbl_salescommissionmapping;
                $this->Route->edit_batch($UpdateMappingData,"id");
            } */
            
        }else{
            $json = array("error"=>2);
        }
        echo json_encode($json);
    }
    public function delete_mul_route() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        foreach ($ids as $row) {
           
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Route','Delete route.');
            }
            
            $UpdateData = array("isdelete"=>1,"modifieddate"=>$modifieddate,"modifiedby"=>$modifiedby);
            $UpdateData=array_map('trim',$UpdateData);
           
            $this->Route->_where = array("id"=>$row);
            $this->Route->Edit($UpdateData);
        }
    }
    public function getRouteMemberByRouteId(){
        $PostData = $this->input->post();
        $routeid = $PostData['routeid'];

        $routememberdata = $this->Route->getRouteMemberDetailByRouteID($routeid);
        echo json_encode($routememberdata);
    }
    public function update_route_member() {
        $PostData = $this->input->post();
        // print_r($PostData);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');

        $routeid = $PostData['editrouteid'];
        
        $InsertData = $updateData = $deleteData = $deletearr = array();
        $channelidarr = $PostData['channelid'];
        $memberid = $PostData['memberid'];
        $priority = $PostData['priority'];
           
        $this->Route->_table = tbl_routemember;
        $this->Route->_where = array("routeid"=>$routeid);
        $routememberData = $this->Route->getRecordById();
        $routememberids = array_column($routememberData, 'id'); 

        if(!empty($channelidarr)){
            foreach($channelidarr as $k=>$channelid){

                $routememberid = isset($PostData['routememberid'][$k])?$PostData['routememberid'][$k]:0;
                $active = isset($PostData['active'.($k+1)])?1:0;

                if(!empty($channelid) && !empty($memberid[$k])){

                    if(!empty($routememberid)){
                        
                        $updateData[] = array(
                            "id"=>$routememberid,
                            "channelid"=>$channelid,
                            "memberid"=>$memberid[$k],
                            "priority"=>$priority[$k],
                            "active"=>$active
                        );

                        $deleteData[] = $routememberid;
                    }else{
                        $InsertData[] = array(
                            "routeid"=>$routeid,
                            "channelid"=>$channelid,
                            "memberid"=>$memberid[$k],
                            "priority"=>$priority[$k],
                            "active"=>$active
                        );
                    }
                }
            }
            if(!empty($routememberids)){
                $deletearr = array_diff($routememberids,$deleteData);
            }
            if(!empty($deletearr)){
                $this->Route->_table = tbl_routemember;
                $this->Route->Delete(array("id IN (".implode(",",$deletearr).")"=>null));
            }
            if(!empty($InsertData)){
                $this->Route->_table = tbl_routemember;
                $this->Route->add_batch($InsertData);
            }
            if(!empty($updateData)){
                $this->Route->_table = tbl_routemember;
                $this->Route->edit_batch($updateData,"id");
            }
        }

        $UpdateData = array("modifieddate"=>$modifieddate,"modifiedby"=>$modifiedby);
        $UpdateData=array_map('trim',$UpdateData);
        $this->Route->_table = tbl_route;
        $this->Route->_where = array("id"=>$routeid);
        $this->Route->Edit($UpdateData);
        
        $routedata = $this->Route->getRouteDataByID($routeid);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(2,'Route','View & edit members of '.$routedata['route'].' route.');
        }
        echo 1;
        
    }
    public function getRouteByProvinceOrCity(){
        $PostData = $this->input->post();
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        
		$routedata = $this->Route->getRouteByProvinceOrCity($provinceid,$cityid);
        echo json_encode($routedata);
    }
    public function getMembersInRoute(){
        $PostData = $this->input->post();
        $routeid = $PostData['routeid'];

        $memberdata = $this->Route->getMembersInRoute($routeid);
        echo json_encode($memberdata);
    }
}

?>