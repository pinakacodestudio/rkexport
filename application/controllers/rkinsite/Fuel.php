<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fuel extends Admin_Controller{

    public $viewData = array();
    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Fuel');
        $this->load->model('Fuel_model', 'Fuel');
    }

    public function index() {
 
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Fuel";
        $this->viewData['module'] = "fuel/Fuel";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Party_model', 'Party');
        $this->viewData['partydata'] = $this->Party->getActiveParty('driver');
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Fuel','View fuel.');
        }
        $this->admin_headerlib->add_javascript("fuel", "pages/fuel.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() { 
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Fuel->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';
 
            if(in_array($rollid, $edit)) {
                $Action .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'fuel/edit-fuel/' . $datarow->id . '/' . '" title="' . edit_title . '">' . edit_text . '</a>';
            } 
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'fuel/check-fuel-use","Fuel","'.ADMIN_URL.'fuel/delete-mul-fuel") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            $partyname = $vehiclename = "-";
            if($datarow->partyname!=""){
                $partyname = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->partyid.'#personaldetails" target="_blank">'.$datarow->partyname.'</a>';
            }
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#fueltab" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $vehiclename;
            $row[] = $this->Fueltype[$datarow->fueltype];
            $row[] = ($datarow->date!="0000-00-00")?$this->general_model->displaydate($datarow->date):"-";  
            $row[] = $partyname;
            $row[] = number_format(($datarow->amount), 2, '.', ',');
            $row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";  
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Fuel->count_all(),
                        "recordsFiltered" => $this->Fuel->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }

    public function add_fuel() {

        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Fuel";
        $this->viewData['module'] = "fuel/Add_fuel";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Party_model', 'Party');
        $this->viewData['partydata'] = $this->Party->getActiveParty('driver');

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_fuel", "pages/add_fuel.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function edit_fuel($id) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Fuel";
        $this->viewData['module'] = "fuel/Add_fuel";
        $this->viewData['action'] = "1"; //Edit

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Party_model', 'Party');
        $this->viewData['partydata'] = $this->Party->getActiveParty('driver');

        //Get Section data by id
        $this->viewData['fueldata'] = $this->Fuel->getFuelDataById($id);
        if(empty($this->viewData['fueldata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
        $this->viewData['fuelfiledata'] = $this->Fuel->getFuelFileDataByFuelId($id);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_fuel", "pages/add_fuel.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function fuel_add() {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $vehicleid = $PostData['vehicleid'];
        $partyid = $PostData['partyid'];
        $date = $PostData['vehiclefueldate'];
        $fueltype = $PostData['fueltype'];
        $paymenttype = $PostData['paymenttype'];
        $liter = $PostData['liter'];
        $km = $PostData['km'];
        $amount = $PostData['amount'];
        $billno = $PostData['billno'];
        $location = $PostData['location'];
        $remarks = $PostData['remarks'];

        if (!is_dir(FUEL_PATH)) {
            @mkdir(FUEL_PATH);
        }

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                if (strpos($key, 'fuelfile') !== false && $_FILES['fuelfile' . $id]['name'] != '') {
                    if ($_FILES['fuelfile' . $id]['size'] != '' && $_FILES['fuelfile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                        echo -1;
                        exit;
                    }
                    $file = uploadFile('fuelfile' . $id, 'FUEL', FUEL_PATH, '*', '', 1, FUEL_LOCAL_PATH, '', '', 0);
                    if ($file !== 0) {
                        if ($file == 2) {
                            echo -2;
                            exit;
                        }
                    } else {
                        echo -2;
                        exit;
                    }
                }
            }
        }

        $insertdata = array(
            "vehicleid" => $vehicleid,
            "partyid" => $partyid,
            "date" => ($date != "" ? $this->general_model->convertdate($date) : ""),
            "fueltype" => $fueltype,
            "billno" => $billno,
            "paymenttype" => $paymenttype,
            "liter" => $liter,
            "km" => $km,
            "amount" => $amount,
            "location" => $location,
            "remarks" => $remarks,
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );

        $insertdata = array_map('trim', $insertdata);
        $Add = $this->Fuel->Add($insertdata);

        if ($Add) {

            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    $file = $_FILES['fuelfile' . $id]['name'];

                    if (strpos($key, 'fuelfile' . $id) !== false && $_FILES['fuelfile' . $id]['name'] != '') {
                        $file = uploadFile('fuelfile' . $id, 'FUEL', FUEL_PATH, '*', '', 1, FUEL_LOCAL_PATH);

                        if ($file == 0 && $file == 2) {
                            $file = "";
                        }
                        if(!empty($file)){
                            $insertData[] = array(
                                "fuelid" => $Add,
                                "file" => $file,
                                "modifieddate" => $createddate,
                                "modifiedby" => $addedby
                            );
                        }
                    }
                }

                if (!empty($insertData)) {
                    $this->Fuel->_table = tbl_fueldocument;
                    $this->Fuel->add_batch($insertData);
                }
            }

            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(1, 'Fuel', 'Add new fuel.');
            }
            echo 1;
        }
    }

    public function update_fuel() {

        $PostData = $this->input->post();
        $modifieddate = $createddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $fuelid = $PostData['fuelid'];
        $vehicleid = $PostData['vehicleid'];
        $partyid = $PostData['partyid'];
        $date = $PostData['vehiclefueldate'];
        $fueltype = $PostData['fueltype'];
        $paymenttype = $PostData['paymenttype'];
        $liter = $PostData['liter'];
        $km = $PostData['km'];
        $amount = $PostData['amount'];
        $billno = $PostData['billno'];
        $location = $PostData['location'];
        $remarks = $PostData['remarks'];

        if (!is_dir(FUEL_PATH)) {
            @mkdir(FUEL_PATH);
        }

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                if (strpos($key, 'fuelfile') !== false && $_FILES['fuelfile' . $id]['name'] != '') {
                    if ($_FILES['fuelfile' . $id]['size'] != '' && $_FILES['fuelfile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                        echo -1;
                        exit;
                    }
                    $file = uploadFile('fuelfile' . $id, 'FUEL', FUEL_PATH, '*', '', 1, FUEL_LOCAL_PATH, '', '', 0);
                    if ($file !== 0) {
                        if ($file == 2) {
                            echo -2;
                            exit;
                        }
                    } else {
                        echo -2;
                        exit;
                    }
                }
            }
        }

        $Updatedata = array(
            "vehicleid" => $vehicleid,
            "partyid" => $partyid,
            "date" => ($date != "" ? $this->general_model->convertdate($date) : ""),
            "billno" => $billno,
            "fueltype" => $fueltype,
            "paymenttype" => $paymenttype,
            "liter" => $liter,
            "km" => $km,
            "amount" => $amount,
            "location" => $location,
            "remarks" => $remarks,
            "modifieddate" => $createddate,
            "modifiedby" => $addedby
        );

        $Updatedata = array_map('trim', $Updatedata);
        $this->Fuel->_where = array("id" => $fuelid);
        $Edit = $this->Fuel->Edit($Updatedata);

        if ($Edit) {

            $documentidarray = isset($PostData['documentid']) ? $PostData['documentid'] : '';
            $olddocfilearray = isset($PostData['olddocfile']) ? $PostData['olddocfile'] : "";
            $insertDocumentData = $updateDocumentData = $deleteidsarray = array();

            if (!empty($_FILES)) {

                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if (strpos($key, 'fuelfile') !== false) {
                        
                        $documentid = (isset($documentidarray[$id]) && !empty($documentidarray[$id])) ? $documentidarray[$id] : "";
                        if ($documentid != "") {
                            $file = $olddocfilearray[$id];

                            if ($_FILES['fuelfile' . $id]['name'] != '' && $olddocfilearray[$id] == "") {
                                $file = uploadFile('fuelfile' . $id, 'FUEL', FUEL_PATH, '*', '', 1, FUEL_LOCAL_PATH);
                                if ($file == 0 && $file == 2) {
                                    $file = "";
                                }
                            } else if (($_FILES['fuelfile' . $id]['name'] != '' || $_FILES['fuelfile' . $id]['name'] == '') && $olddocfilearray[$id] != "") {
                                $file = $olddocfilearray[$id];
                                if ($_FILES['fuelfile' . $id]['name'] != '') {

                                    $file = reuploadFile('fuelfile' . $id, 'FUEL', $olddocfilearray[$id], FUEL_PATH, '*', '', 1, FUEL_LOCAL_PATH);

                                    if ($file == 0 && $file == 2) {
                                        $file = "";
                                    }
                                }

                            }
                            
                            if(!empty($file)){
                                $updateDocumentData[] = array(
                                    'id' => $documentid,
                                    "fuelid" => $fuelid,
                                    "file" => $file,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby" => $modifiedby
                                );
                                
                                $deleteidsarray[] = $documentid;
                            }

                        } else {
                            if ($_FILES['fuelfile' . $id]['name'] != '') {

                                $file = uploadFile('fuelfile' . $id, 'FUEL', FUEL_PATH, '*', '', 1, FUEL_LOCAL_PATH);
                                if ($file == 0 && $file == 2) {
                                    $file = "";
                                }
                                if(!empty($file)){
                                    $insertDocumentData[] = array(
                                        "fuelid" => $fuelid,
                                        "file" => $file,
                                        "modifieddate" => $modifieddate,
                                        "modifiedby" => $modifiedby
                                    );
                                }
                            }
                        }
                    }
                }
            }
            $fueldocumentdata = $this->Fuel->getFuelFileDataByFuelId($fuelid);
            $fueldocumentidarray = (!empty($fueldocumentdata) ? array_column($fueldocumentdata, "id") : array());
            if (!empty($documentidarray)) {
                $deletearr = array_diff($fueldocumentidarray, $deleteidsarray);
            }
            if (!empty($deletearr)) {

                $unlinkfueldocuments = $this->Fuel->getFuelDocumentsById(implode(",", $deletearr));
                if(!empty($unlinkfueldocuments)){
                    foreach($unlinkfueldocuments as $doc){
                        unlinkfile('FUEL', $doc['file'], FUEL_PATH);
                    }
                }
                $this->Fuel->_table = tbl_fueldocument;
                $this->Fuel->Delete(array("id IN (" . implode(",", $deletearr) . ")" => null));
            }
            if (count($insertDocumentData) > 0) {
                $this->Fuel->_table = tbl_fueldocument;
                $this->Fuel->add_batch($insertDocumentData);
            }
            if (count($updateDocumentData) > 0) {
                $this->Fuel->_table = tbl_fueldocument;
                $this->Fuel->edit_batch($updateDocumentData, "id");
            }

            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(2, 'Fuel', 'Edit fuel.');
            }
            echo 1;
        } else {
            echo 0;
        }
    }

    public function check_fuel_use() {
       
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach($ids as $row){
            /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE 
                    id IN (SELECT documenttypeid FROM ".tbl_document." WHERE documenttypeid = $row) ");

            if($query->num_rows() > 0){
                $count++;
            } */
        }
        echo $count;
    }

    public function delete_mul_fuel() {
        
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {

            $files=$this->Fuel->getFuelFileDataByFuelId($row);
            foreach($files as $file){
                unlinkfile('FUEL',$file['file'],FUEL_PATH);
            }
            $this->Fuel->_table = tbl_fueldocument;
            $this->Fuel->Delete(array("fuelid" => $row));

            $this->Fuel->_table = tbl_fuel;
            $this->Fuel->Delete(array("id" => $row));
        }
        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(3, 'Fuel', 'Delete fuel.');
        }
    }

    public function exportToExcelFuelDetail(){
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Fuel','Export to excel Fuel.');
        }
        $exportdata = $this->Fuel->getFuelDataForExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {
            $paymenttype='';
            if($row->paymenttype==1){
                $paymenttype='Petro-Card';
            }elseif($row->paymenttype==2){
                $paymenttype='Cash';
            }elseif($row->paymenttype==3){
                $paymenttype='Bank';
            }
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->drivername!=''?$row->drivername:'-'),
                            ($row->date!='0000-00-00'?$this->general_model->displaydate($row->date):'-'),
                            (isset($this->Fueltype[$row->fueltype])?$this->Fueltype[$row->fueltype]:'-'),
                            ($paymenttype!=''?$paymenttype:'-'),
                            ($row->liter>0?numberFormat($row->liter,2,','):'-'),
                            ($row->km>0?numberFormat($row->km,2,','):'-'),
                            ($row->amount>0?numberFormat($row->amount,2,','):'-'),
                            ($row->billno!=''?$row->billno:'-'),
                            ($row->location!=''?$row->location:'-'),
                            ($row->remarks!=''?$row->remarks:'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                        );
        }  
        $headings = array('Sr. No.','Vehicle Name','Party Name','Date','Type','Payment Type','Liter','Km','Amount','Bill No.','Location','Remarks','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Fuel",$headings,"Fuel.xls",array('H','I','J'));

    }
    
    public function exportToPDFFuel(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Fuel','Export to PDF Fuel.');
        }

        $PostData['reportdata'] = $this->Fuel->getFuelDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Fuel';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'fuel/FuelPdfFormate', $PostData,true);

        $this->general_model->exportToPDF("fuel.pdf",$header,$html);
    }
    
    public function printFuelDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Fuel','Print Fuel.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Fuel->getFuelDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Fuel';

        $html['content'] = $this->load->view(ADMINFOLDER."fuel/FuelPrintFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}