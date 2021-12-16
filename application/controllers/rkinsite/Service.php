<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service extends Admin_Controller
{
    public $viewData = array();
    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Service');
        $this->load->model('Service_model', 'Service');
    }
    public function index() {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Service";
        $this->viewData['module'] = "service/Service";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Service_type_model', 'Service_type');
        $this->viewData['servicetypedata'] = $this->Service_type->getactiveservicetype();

        $this->load->model('Party_model', 'Party');
        $this->viewData['partydata'] = $this->Party->getActiveParty('driver');

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("service", "pages/service.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $list = $this->Service->get_datatables();

        $data = array();
        $counter = $_POST['start'];

        foreach ($list as $datarow) {
            $row = array();
            $Action = $checkbox = '';

            if (in_array($rollid, $edit)) {
                $Action .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'service/edit-service/' . $datarow->id . '" title=' . edit_title . '>' . edit_text . '</a>';
            }
            if (in_array($rollid, $delete)) {
                $Action .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"' . ADMIN_URL . 'service/check-service-use","Service","' . ADMIN_URL . 'service/delete-mul-service") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input value="' . $datarow->id . '" type="checkbox" class="checkradios" name="check' . $datarow->id . '" id="check' . $datarow->id . '" onchange="singlecheck(this.id)"><label for="check' . $datarow->id . '"></label></div>';
            }
            
            $vehiclename = '<a href="' . ADMIN_URL . 'vehicle/view-vehicle/' . $datarow->vehicleid . '#challantab" target="_blank">' . ($datarow->vehiclename . " (" . $datarow->vehicleno . ")") . "</a>";
            
            $drivername = '<a href="' . ADMIN_URL . 'party/view-party/' . $datarow->driverid . '#personaldetails" target="_blank">' . ($datarow->drivername) . "</a>";
            
            $garagename = '<a href="' . ADMIN_URL . 'party/view-party/' . $datarow->garageid . '#personaldetails" target="_blank">' . ($datarow->garagename) . "</a>";
            

            $row[] = ++$counter;
            $row[] = $vehiclename;
            $row[] = $datarow->servicetype;
            $row[] = $this->general_model->displaydate($datarow->date);
            $row[] = $drivername;
            $row[] = $garagename;
            $row[] = numberFormat($datarow->amount,2,',');
            $row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Service->count_all(),
            "recordsFiltered" => $this->Service->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function add_service() {

        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Service";
        $this->viewData['module'] = "service/Add_service";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
        
        $this->load->model('Party_model', 'Party');
        $this->viewData['driverdata'] = $this->Party->getActiveParty('driver');
        $this->viewData['garagedata'] = $this->Party->getActiveParty('garage');

        $this->load->model('Service_type_model', 'Service_type');
        $this->viewData['servicetypedata'] = $this->Service_type->getActiveServiceType();

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_service", "pages/add_service.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function edit_service($serviceid) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Service";
        $this->viewData['module'] = "service/Add_service";
        $this->viewData['action'] = "1"; //Edit

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
        
        $this->load->model('Service_type_model', 'Service_type');
        $this->viewData['servicetypedata'] = $this->Service_type->getActiveServiceType();
        
        $this->load->model('Party_model', 'Party');
        $this->viewData['driverdata'] = $this->Party->getActiveParty('driver');
        $this->viewData['garagedata'] = $this->Party->getActiveParty('garage');

        $this->viewData['servicedata'] = $this->Service->getServiceDataByID($serviceid);
        if(empty($this->viewData['servicedata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
        $this->viewData['servicepartdata'] = $this->Service->getServicePartDataById($serviceid);
        $this->viewData['servicefiledata'] = $this->Service->getServiceFileDataById($serviceid);

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_service", "pages/add_service.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function service_add() {

        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $vehicleid = $PostData['vehicleid'];
        $servicetypeid = $PostData['servicetypeid'];
        $driverid = $PostData['driverid'];
        $garageid = $PostData['garageid'];
        $servicedate = ($PostData['servicedate']!="")?$this->general_model->convertdate($PostData['servicedate']):"";
        $remarks = $PostData['remarks'];
        $amount = $PostData['totalpriceamount'];
        $taxamount = $PostData['totaltaxamount'];

        if (!is_dir(SERVICE_PATH)) {
            @mkdir(SERVICE_PATH);
        }
        $json = array();
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                if (strpos($key, 'servicefile' . $id) !== false && $_FILES['servicefile' . $id]['name'] != '') {
                    if ($_FILES['servicefile' . $id]['size'] != '' && $_FILES['servicefile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                        $json = array('error' => -1, "id" => $id);
                        echo json_encode($json);
                        exit;
                    }
                    $file = uploadFile('servicefile' . $id, 'SERVICE', SERVICE_PATH, '*', '', 1, SERVICE_LOCAL_PATH, '', '', 0);
                    if ($file !== 0) {
                        if ($file == 2) {
                            $json = array('error' => -2, 'message' => $id . " File not upload !", "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                    } else {
                        $json = array('error' => -2, 'message' => $id . " Accept only Image and PDF Files !", "id" => $id);
                        echo json_encode($json);
                        exit;
                    }
                }
            }
        }

        $insertdata = array(
            "vehicleid" => $vehicleid,
            "driverid" => $driverid,
            "servicetypeid" => $servicetypeid,
            "garageid" => $garageid,
            "date" => $servicedate,
            "remarks" => $remarks,
            "taxamount" => $taxamount,
            "amount" => $amount,
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );

        $insertdata = array_map('trim', $insertdata);
        $Add = $this->Service->Add($insertdata);

        if ($Add) {
            $partnamearray = $PostData['partname'];
            $serialno = $PostData['serialno'];
            $warrantydatearray = $PostData['warrantydate'];
            $duedatearray = $PostData['duedate'];
            $price = $PostData['price'];
            $tax = $PostData['tax'];
            $currentkmhr = $PostData['currentkmhr'];
            $changeafter = $PostData['changeafter'];
            $alertkmhr = $PostData['alertkmhr'];

            $insertservicepartdata = $insertservicedocument = array();

            if(!empty($partnamearray)){
                foreach($partnamearray as $id=>$partname) {
                    $setalert = isset($PostData['setalert'][$id])?1:0;

                    if (!empty($partname) && !empty($price[$id]) && $serialno[$id]!="") {

                        $warrantydate = !empty($warrantydatearray[$id])?$this->general_model->convertdate($warrantydatearray[$id]):"";
                        $duedate = !empty($duedatearray[$id])?$this->general_model->convertdate($duedatearray[$id]):"";

                        $insertservicepartdata[] = array(
                            "serviceid" => $Add,
                            "partname" => $partname,
                            "serialnumber" => $serialno[$id],
                            "warrantyenddate" => $warrantydate,
                            "duedate" => $duedate,
                            "price" => $price[$id],
                            "tax" => $tax[$id],
                            "setalert" => $setalert,
                            "currentkmhr" => $currentkmhr[$id],
                            "changeafter" => $changeafter[$id],
                            "alertkmhr" => $alertkmhr[$id],
                        );
                    }
                }
            }
            if (!empty($_FILES)) {
                $title = $PostData['filetitle'];
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    
                    if (strpos($key, 'servicefile' . $id) !== false) {

                        $file = "";
                        if($_FILES['servicefile' . $id]['name'] != ''){
                            $file = uploadFile('servicefile' . $id, 'SERVICE', SERVICE_PATH, '*', '', 1, SERVICE_LOCAL_PATH);
                            if ($file == 0 && $file == 2) {
                                $file = "";
                            }
                        }
                        if(!empty($file) || !empty($title[$id])){
                            
                            $insertservicedocument[] = array(
                                "serviceid" => $Add,
                                "title" => $title[$id],
                                "file" => $file,
                                "modifieddate" => $createddate,
                                "modifiedby" => $addedby
                            );
                        }
                    }
                }
            }
            if (count($insertservicedocument) > 0) {
                $this->Service->_table = tbl_servicedocument;
                $this->Service->add_batch($insertservicedocument);
            }
            if (count($insertservicepartdata) > 0) {
                $this->Service->_table = tbl_servicepartdetails;
                $this->Service->add_batch($insertservicepartdata);
            }

            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(1, 'Service', 'Add new service.');
            }
            $json = array('error' => 1);
        } else {
            $json = array('error' => 0);
        }
        echo json_encode($json);
    }
    public function update_service() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $serviceid = $PostData['serviceid'];
        $vehicleid = $PostData['vehicleid'];
        $servicetypeid = $PostData['servicetypeid'];
        $driverid = $PostData['driverid'];
        $garageid = $PostData['garageid'];
        $servicedate = ($PostData['servicedate']!="")?$this->general_model->convertdate($PostData['servicedate']):"";
        $remarks = $PostData['remarks'];
        $amount = $PostData['totalpriceamount'];
        $taxamount = $PostData['totaltaxamount'];

        if (!is_dir(SERVICE_PATH)) {
            @mkdir(SERVICE_PATH);
        }
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                if (strpos($key, 'servicefile' . $id) !== false && $_FILES['servicefile' . $id]['name'] != '') {
                    if ($_FILES['servicefile' . $id]['size'] != '' && $_FILES['servicefile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                        $json = array('error' => -1, "id" => $id);
                        echo json_encode($json);
                        exit;
                    }
                    $file = uploadFile('servicefile' . $id, 'SERVICE', SERVICE_PATH, '*', '', 1, SERVICE_LOCAL_PATH, '', '', 0);
                    if ($file !== 0) {
                        if ($file == 2) {
                            $json = array('error' => -2, 'message' => $id . " File not upload !", "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                    } else {
                        $json = array('error' => -2, 'message' => $id . " Accept only Image and PDF Files !", "id" => $id);
                        echo json_encode($json);
                        exit;
                    }
                }
            }
        }

        $Updatedata = array(
            "vehicleid" => $vehicleid,
            "driverid" => $driverid,
            "servicetypeid" => $servicetypeid,
            "garageid" => $garageid,
            "date" => $servicedate,
            "remarks" => $remarks,
            "taxamount" => $taxamount,
            "amount" => $amount,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby
        );

        $Updatedata = array_map('trim', $Updatedata);
        $this->Service->_where = array("id" => $serviceid);
        $Edit = $this->Service->Edit($Updatedata);

        if ($Edit) {
            $documentidarray = isset($PostData['documentid']) ? $PostData['documentid'] : '';
            $partidarray = isset($PostData['partid']) ? $PostData['partid'] : '';

            $olddocfilearray = isset($PostData['olddocfile']) ? $PostData['olddocfile'] : "";
            $partnamearray = $PostData['partname'];
            $serialno = $PostData['serialno'];
            $warrantydatearray = $PostData['warrantydate'];
            $duedatearray = $PostData['duedate'];
            $price = $PostData['price'];
            $tax = $PostData['tax'];
            $title = $PostData['filetitle'];
            $currentkmhr = $PostData['currentkmhr'];
            $changeafter = $PostData['changeafter'];
            $alertkmhr = $PostData['alertkmhr'];

            $updateservicepart = $insertservicepart = $deleteidpartsarray = array();
            $updateservicedocument = $insertservicedocument = $deleteidsarray = array();

            // print_r($PostData); exit;
            if(!empty($partnamearray)){
                foreach($partnamearray as $id=>$partname) {
                    $setalert = isset($PostData['setalert'][$id])?1:0;
                    $servicepartid = (!empty($partidarray) && !empty($partidarray[$id])) ? $partidarray[$id] : "";
                    $warrantydate = !empty($warrantydatearray[$id])?$this->general_model->convertdate($warrantydatearray[$id]):"";
                    $duedate = !empty($duedatearray[$id])?$this->general_model->convertdate($duedatearray[$id]):"";

                    if (!empty($servicepartid)) {
                        $updateservicepart[] = array(
                            "id" => $servicepartid,
                            "serviceid" => $serviceid,
                            "partname" => $partname,
                            "serialnumber" => $serialno[$id],
                            "warrantyenddate" => $warrantydate,
                            "duedate" => $duedate,
                            "price" => $price[$id],
                            "tax" => $tax[$id],
                            "setalert" => $setalert,
                            "currentkmhr" => $currentkmhr[$id],
                            "changeafter" => $changeafter[$id],
                            "alertkmhr" => $alertkmhr[$id],
                        );
                        $deleteidpartsarray[] = $servicepartid;
                    } else {

                        if (!empty($partname) && !empty($price[$id]) && $serialno[$id]!="") {
                            $insertservicepart[] = array(
                                "serviceid" => $serviceid,
                                "partname" => $partname,
                                "serialnumber" => $serialno[$id],
                                "warrantyenddate" => $warrantydate,
                                "duedate" => $duedate,
                                "price" => $price[$id],
                                "tax" => $tax[$id],
                                "setalert" => $setalert,
                                "currentkmhr" => $currentkmhr[$id],
                                "changeafter" => $changeafter[$id],
                                "alertkmhr" => $alertkmhr[$id],     
                            );
                        }
                    }
                }
            }
            $servicedata = $this->Service->getServicePartDataById($serviceid);
            $servicearray = (!empty($servicedata) ? array_column($servicedata, "id") : array());

            if (!empty($servicearray)) {
                $deleteserviceparts = array_diff($servicearray, $deleteidpartsarray);
            }
            if (!empty($deleteserviceparts)) {
                $this->Service->_table = tbl_servicepartdetails;
                $this->Service->Delete(array("serviceid" => $serviceid,"id IN (" . implode(",", $deleteserviceparts) . ")" => null));
            }

            if (count($insertservicepart) > 0) {
                $this->Service->_table = tbl_servicepartdetails;
                $this->Service->add_batch($insertservicepart);
            }
            if (count($updateservicepart) > 0) {
                $this->Service->_table = tbl_servicepartdetails;
                $this->Service->edit_batch($updateservicepart, "id");
            }

            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);


                    $file = "";
                    if (strpos($key, 'servicefile' . $id) !== false) {
                        $documentid = (isset($documentidarray[$id]) && !empty($documentidarray[$id])) ? $documentidarray[$id] : "";

                        if ($documentid != "") {
                            if ($_FILES['servicefile' . $id]['name'] != '' && $olddocfilearray[$id] == "") {
                                $file = uploadFile('servicefile' . $id, 'SERVICE', SERVICE_PATH, '*', '', 1, SERVICE_LOCAL_PATH);
                                if ($file == 0 && $file == 2) {
                                    $file = "";
                                }
                            } else if (($_FILES['servicefile' . $id]['name'] != '' || $_FILES['servicefile' . $id]['name'] == '') && $olddocfilearray[$id] != "") {
                                $file = $olddocfilearray[$id];
                                if ($_FILES['servicefile' . $id]['name'] != '') {
                                    $file = reuploadFile('servicefile' . $id, 'SERVICE', $olddocfilearray[$id], SERVICE_PATH, '*', '', 1, SERVICE_LOCAL_PATH);
                                    if ($file == 0 && $file == 2) {
                                        $file = "";
                                    }
                                }
                            } else {
                                $file = "";
                            }

                            $updateservicedocument[] = array(
                                "id" => $documentid,
                                "serviceid" => $serviceid,
                                "title" => $title[$id],
                                "file" => $file,
                                "modifieddate" => $modifieddate,
                                "modifiedby" => $modifiedby
                            );
                            $deleteidsarray[] = $documentid;
                        } else {
                            
                            $file = "";
                            if ($_FILES['servicefile' . $id]['name'] != '') {
                                $file = uploadFile('servicefile' . $id, 'SERVICE', SERVICE_PATH, '*', '', 1, SERVICE_LOCAL_PATH);
                                if ($file == 0 && $file == 2) {
                                    $file = "";
                                }
                            }
                            if(!empty($file) || !empty($title[$id])){
                                $insertservicedocument[] = array(
                                    "serviceid" => $serviceid,
                                    "title" => $title[$id],
                                    "file" => $file,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby" => $modifiedby
                                );
                            }
                        }
                    }
                }
            }
            $servicefiledata = $this->Service->getServiceFileDataById($serviceid);
            $filearray = (!empty($servicefiledata) ? array_column($servicefiledata, "id") : array());

            if (!empty($filearray)) {
                $deletearr = array_diff($filearray, $deleteidsarray);
            }
            if (!empty($deletearr)) {
                $unlinkdocuments = $this->Service->getServiceDocumentDataById(implode(",", $deletearr));
                if (!empty($unlinkdocuments)) {
                    foreach ($unlinkdocuments as $doc) {
                        unlinkfile('SERVICE', $doc['file'], SERVICE_PATH);
                    }
                }
                $this->Service->_table = tbl_servicedocument;
                $this->Service->Delete(array("serviceid" => $serviceid,"id IN (" . implode(",", $deletearr) . ")" => null));
            }

            if (count($insertservicedocument) > 0) {
                $this->Service->_table = tbl_servicedocument;
                $this->Service->add_batch($insertservicedocument);
            }
            if (count($updateservicedocument) > 0) {
                $this->Service->_table = tbl_servicedocument;
                $this->Service->edit_batch($updateservicedocument, "id");
            }

            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(2, 'Service', 'Edit service.');
            }
            $json = array('error' => 1);
        } else {
            $json = array('error' => 0);
        }
        echo json_encode($json);
    }
    public function check_service_use() {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach($ids as $row){

            /* $this->readdb->select('documenttypeid');
            $this->readdb->from(tbl_document);
            $where = array("documenttypeid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }
    public function delete_mul_service() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {

            $files = $this->Service->getServiceFileDataById($row);
            foreach ($files as $file) {
                if(!empty($file['file'])){
                    unlinkfile('SERVICE', $file['file'], SERVICE_PATH);
                }
            }
            $this->Service->_table = tbl_servicedocument;
            $this->Service->Delete(array("serviceid" => $row));

            $this->Service->_table = tbl_servicepartdetails;
            $this->Service->Delete(array("serviceid" => $row));

            $this->Service->_table = tbl_service;
            $this->Service->Delete(array("id" => $row));
        }
        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(3, 'Service', 'Delete services.');
        }
    }

    public function exportToExcelService(){
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Service','Export to excel Service.');
        }
        $exportdata = $this->Service->getServiceDataForExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->servicetype!=''?$row->servicetype:'-'),
                            ($row->date!='0000-00-00'?$this->general_model->displaydate($row->date):'-'),
                            ($row->drivername!=''?$row->drivername:'-'),
                            ($row->garagename!=''?$row->garagename:'-'),
                            ($row->amount>0?numberFormat($row->amount,2,','):'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                        );
        }
        $headings = array('Sr. No.','Vehicle Name','Service Type','Date','Driver Name','Garage Name','Amount','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Service",$headings,"Service.xls",'G');

    }
    
    public function exportToPDFService(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Service','Export to PDF Service.');
        }

        $PostData['reportdata'] = $this->Service->getServiceDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Service';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'service/ServicePDFFormat', $PostData,true);

        $this->general_model->exportToPDF("Service.pdf",$header,$html);
    }
    
    public function printService(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Service','Print Service.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Service->getServiceDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Service';

        $html['content'] = $this->load->view(ADMINFOLDER."service/ServicePrintFormat.php",$PostData,true);
        echo json_encode($html); 
    }

    public function searchServiceParts(){
        $PostData = $this->input->post();
    
        if(isset($PostData["term"])){
            $Companydata = $this->Service->searchServiceParts(1,$PostData["term"]);
        }else if(isset($PostData["ids"])){
            $Companydata = $this->Service->searchServiceParts(0,$PostData["ids"]);
        }
        
        echo json_encode($Companydata);
    }
}
