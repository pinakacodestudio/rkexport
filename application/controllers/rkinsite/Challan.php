<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Challan extends Admin_Controller {

    public $viewData = array();
    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Challan');
        $this->load->model('Challan_model', 'Challan');
    }

    public function index() {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Challan";
        $this->viewData['module'] = "challan/Challan";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Challan_type_model', 'Challan_type');
        $this->viewData['challantype'] = $this->Challan_type->getActiveChallanType();

        $this->load->model('Party_model', 'Party');
        $this->viewData['driverdata'] = $this->Party->getActiveParty('driver');

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("challan", "pages/challan.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    
    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $list = $this->Challan->get_datatables();
        
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $actions = $checkbox = '';

            if (in_array($rollid, $edit)) {
                $actions .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'challan/edit-challan/' . $datarow->id . '/' . '" title="' . edit_title . '">' . edit_text . '</a>';
            }

            if (in_array($rollid, $delete)) {
                $actions .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"' . ADMIN_URL . 'challan/check-challan-use","Challan","' . ADMIN_URL . 'challan/delete-mul-challan") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck' . $datarow->id . '" onchange="singlecheck(this.id)" type="checkbox" value="' . $datarow->id . '" name="deletecheck' . $datarow->id . '" class="checkradios">
                                <label for="deletecheck' . $datarow->id . '"></label></div>';
            }

            if ($datarow->attachment !== '' && file_exists(CHALLAN_PATH . $datarow->attachment)) {
                $actions .= '<a class="'.download_class.'" href="'.CHALLAN.$datarow->attachment.'" title="'.download_title.'" download>'.download_text.'</a>';
                $actions .= '<a class="'.viewdoc_class.'" href="'.CHALLAN.$datarow->attachment.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
            }
            $partyname = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->partyid.'#personaldetails" target="_blank">'.($datarow->drivername)."</a>";
            $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#challantab" target="_blank">'.($datarow->vehiclename . " (" . $datarow->vehicleno . ")")."</a>";

            $row[] = ++$counter;
            $row[] = $vehiclename;
            $row[] = $partyname;
            $row[] = $datarow->challantype;
            $row[] = ($datarow->site!='')?$datarow->site:'-';
            $row[] = ($datarow->date!='0000-00-00')?$this->general_model->displaydate($datarow->date):"-";
            $row[] = numberFormat($datarow->amount,2,',');
            $row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
            "recordsTotal" => $this->Challan->count_all(),
            "recordsFiltered" => $this->Challan->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add_challan() {

        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Challan";
        $this->viewData['module'] = "challan/Add_challan";

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Challan_type_model', 'Challan_type');
        $this->viewData['challantype'] = $this->Challan_type->getActiveChallanType();

        $this->load->model('Party_model', 'Party');
        $this->viewData['partydata'] = $this->Party->getActiveParty('driver');

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_challan", "pages/add_challan.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function edit_challan($id) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Challan";
        $this->viewData['module'] = "challan/Add_challan";
        $this->viewData['action'] = "1"; //Edit

        $this->load->model('Vehicle_model', 'Vehicle');
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Challan_type_model', 'Challan_type');
        $this->viewData['challantype'] = $this->Challan_type->getActiveChallanType();
        
        $this->load->model('Party_model', 'Party');
        $this->viewData['partydata'] = $this->Party->getActiveParty('driver');

        //Get Section data by id
        $this->viewData['challandata'] = $this->Challan->getChallanDataByID($id);
        if(empty($this->viewData['challandata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_challan", "pages/add_challan.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function challan_add() {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $partyid = $PostData['challanfor'];
        $vehicleid = $PostData['vehicle'];
        $challantypeid = $PostData['challantype'];
        $date = $PostData['date'];
        $amount = $PostData['amount'];
        $remarks = $PostData['remarks'];

        if (!is_dir(CHALLAN_PATH)) {
            @mkdir(CHALLAN_PATH);
        }

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                if (strpos($key, 'attachement' . $id) !== false && $_FILES['attachement' . $id]['name'] != '') {
                    if ($_FILES['attachement' . $id]['size'] != '' && $_FILES['attachement' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                        echo -1;
                        exit;
                    }
                    $file = uploadFile('attachement' . $id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH, '', '', 0);
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
        
        
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);

                $file = "";
                if (strpos($key, 'attachment' . $id) !== false && $_FILES['attachment' . $id]['name'] != '') {
                    $file = uploadFile('attachment' . $id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH);
                    if ($file == 0 && $file == 2) {
                        $file = "";
                    }
                }
                $insertData[] = array(
                    "partyid" => $partyid,
                    "vehicleid" => $vehicleid,
                    'challantypeid' => $challantypeid[$id],
                    'date' => ($date[$id] != "")?$this->general_model->convertdate($date[$id]):"",
                    'amount' => $amount[$id],
                    'attachment' => $file,
                    'remarks' => $remarks[$id],
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
            }

            if (!empty($insertData)) {
                $this->Challan->add_batch($insertData);
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(1, 'Challan', 'Add new challan.');
                }
            }
        }
        echo 1;
    }

    public function update_challan() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
       
        $challanid = $PostData['challanid'];
        $partyid = $PostData['challanfor'];
        $vehicleid = $PostData['vehicle'];
        $challantypeid = $PostData['challantype'];
        $date = $PostData['date'];
        $amount = $PostData['amount'];
        $remarks = $PostData['remarks'];
        $olddocfile = isset($PostData['oldfile']) ? $PostData['oldfile'] : "";
        
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                if (strpos($key, 'attachement' . $id) !== false && $_FILES['attachement' . $id]['name'] != '') {
                    if ($_FILES['attachement' . $id]['size'] != '' && $_FILES['attachement' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                        echo -1;
                        exit;
                    }
                    $file = uploadFile('attachement' . $id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH, '', '', 0);
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

        if (!empty($_FILES)) {
            $file = '';
            
            if ($_FILES['attachment1']['name'] != '' && $olddocfile[1] == "") {
                $file = uploadFile('attachment1', 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH);
                if ($file == 0 && $file == 2) {
                    $file = "";
                }
            }else if (($_FILES['attachment1']['name'] != '' || $_FILES['attachment1']['name'] == '') && $olddocfile[1] != "") {
                $file = $olddocfile[1];
                if ($_FILES['attachment1']['name'] != '') {
                    $file = reuploadFile('attachment1', 'CHALLAN', $olddocfile[1], CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH);
                    if ($file == 0 && $file == 2) {
                        $file = "";
                    }
                }
            } else {
                $file = "";
            }
        
            $updatedata = array(
                "partyid" => $partyid,
                "vehicleid" => $vehicleid,
                'challantypeid' => $challantypeid[1],
                'date' => ($date[1]!="")?$this->general_model->convertdate($date[1]):"",
                'amount' => $amount[1],
                'attachment' => $file,
                'remarks' => $remarks[1],
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );

            $this->Challan->_where = array("id"=>$challanid);
            $Edit = $this->Challan->Edit($updatedata);
            if ($Edit) {
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Challan', 'Edit challan.');
                }
                echo 1;
            } else {
                echo 0;
            }
        }
    }

    public function check_challan_use() {

        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        // use for check challan type available or not in other table
        foreach($ids as $row){
            /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE 
                    id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
                    //OR id IN (SELECT vehicleid FROM ".tbl_vehicleroute." WHERE vehicleid = $row)
            if($query->num_rows() > 0){
                $count++;
            } */
        }
        echo $count;
    }

    public function delete_mul_challan() {

        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {

            $this->Challan->_where = array("id"=>$row);
			$data = $this->Challan->getRecordsByID();
			if(!empty($data) && $data['attachment']!=""){
				unlinkfile("CHALLAN", $data['attachment'], CHALLAN_PATH);
			}
            $this->Challan->Delete(array("id" => $row));
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(3,'Challan', 'Delete challan.');
        }
    }

    public function exportToExcelChallan(){
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Challan','Export to excel Challan.');
        }
        $exportdata = $this->Challan->getChallanDataForExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->drivername!=''?$row->drivername:'-'),
                            ($row->challantype!=''?$row->challantype:'-'),
                            ($row->sitename!=''?$row->sitename:'-'),
                            ($row->date!='0000-00-00'?$this->general_model->displaydate($row->date):'-'),
                            ($row->amount>0?numberFormat($row->amount,2,','):'-'),
                            ($row->remarks!=''?$row->remarks:'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                        );
        }
        $headings = array('Sr. No.','Vehicle Name','Driver Name','Challan Type','Site Name','Date','Amount','Remarks','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Challan",$headings,"Challan.xls",'G');

    }
    
    public function exportToPDFChallan(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Challan','Export to PDF Challan.');
        }

        $PostData['reportdata'] = $this->Challan->getChallanDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Challan';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'challan/ChallanPDFFormat', $PostData,true);

        $this->general_model->exportToPDF("Challan.pdf",$header,$html);
    }
    
    public function printChallan(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Challan','Print Challan.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Challan->getChallanDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Challan';

        $html['content'] = $this->load->view(ADMINFOLDER."challan/ChallanPrintFormat.php",$PostData,true);
        echo json_encode($html); 
    }
}
