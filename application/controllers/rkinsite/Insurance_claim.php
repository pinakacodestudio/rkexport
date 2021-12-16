<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Insurance_claim extends Admin_Controller {

    public $viewData = array();
    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Insurance_claim');
        $this->load->model('Insurance_claim_model', 'Insurance_claim');
    }

    public function index(){
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Insurance Claim";
        $this->viewData['module'] = "insurance_claim/Insurance_claim";
        
        $this->viewData['companydata'] = $this->Insurance_claim->getInsuranceCompanyOnClaim();
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("insurance-claim", "pages/insurance_claim.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $list = $this->Insurance_claim->get_datatables();

        $data = array(); 
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $actions = $checkbox = '';
            $billamount = "<span class='pull-right'>".number_format(($datarow->billamount), 2, '.', ',')."</span>";
            $claimamount = "<span class='pull-right'>".number_format(($datarow->claimamount), 2, '.', ',')."</span>";
            $status = $datarow->status;
                
            if (in_array($rollid, $edit)) {
                $actions .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'insurance-claim/edit-insurance-claim/' . $datarow->id . '/' . '" title="' . edit_title . '">' . edit_text . '</a>';
            }

            if (in_array($rollid, $delete)) {
                $actions .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"' . ADMIN_URL . 'insurance-claim/check-insurance-claim-use","Insurance&nbsp;Claim","' . ADMIN_URL . 'insurance-claim/delete-mul-insurance-claim") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck' . $datarow->id . '" onchange="singlecheck(this.id)" type="checkbox" value="' . $datarow->id . '" name="deletecheck' . $datarow->id . '" class="checkradios">
                                <label for="deletecheck' . $datarow->id . '"></label></div>';
            }
            if ($datarow->attachment!=='' && file_exists(INSURANCE_CLAIM_PATH.$datarow->attachment)) {
                $actions .= '<a class="'.download_class.'" href="'.INSURANCE_CLAIM.$datarow->attachment.'" title="'.download_title.'" download>'.download_text.'</a>';
                $actions .= '<a class="'.viewdoc_class.'" href="'.INSURANCE_CLAIM.$datarow->attachment.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
            }

            if ($status == 0) {
                $dropdownmenu = '<button class="btn btn-warning ' . STATUS_DROPDOWN_BTN . ' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown' . $datarow->id . '">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chagestatus(1,' . $datarow->id . ',' . $datarow->status . ')">Approve</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagestatus(2,' . $datarow->id . ',' . $datarow->status . ')">Reject</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagestatus(3,' . $datarow->id . ',' . $datarow->status . ')">Total Lost</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagestatus(4,' . $datarow->id . ',' . $datarow->status . ')">Cancel</a>
                              </li>
                          </ul>';
            } else if ($status == 1) {
                $dropdownmenu = '<button class="btn btn-success ' . STATUS_DROPDOWN_BTN . ' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown' . $datarow->id . '">Approved <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(0,' . $datarow->id . ',' . $datarow->status . ')">Pending</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(2,' . $datarow->id . ',' . $datarow->status . ')">Reject</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(3,' . $datarow->id . ',' . $datarow->status . ')">Total Lost</a>
                              </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(4,' . $datarow->id . ',' . $datarow->status . ')">Cancel</a>
                            </li>
                        </ul>';
            } else if ($status == 2) {
                $dropdownmenu = '<button class="btn btn-danger btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Rejected <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(0,' . $datarow->id . ',' . $datarow->status . ')">Pending</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(1,' . $datarow->id . ',' . $datarow->status . ')">Approve</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(3,' . $datarow->id . ',' . $datarow->status . ')">Total Lost</a>
                              </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(4,'.$datarow->id.','.$datarow->status.')">Cancel</a>
                            </li>
                        </ul>';
            } else if ($status == 3) {
                $dropdownmenu = '<button class="btn btn-danger btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Total Lost <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(0,' . $datarow->id . ',' . $datarow->status . ')">Pending</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(1,' . $datarow->id . ',' . $datarow->status . ')">Approve</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(2,' . $datarow->id . ',' . $datarow->status . ')">Reject</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(4,'.$datarow->id.','.$datarow->status.')">Cancel</a>
                            </li>
                        </ul>';
            }
            else if ($status == 4) {
                $dropdownmenu = '<button class="btn btn-danger btn-sm btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Cancle <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(0,' . $datarow->id . ',' . $datarow->status . ')">Pending</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(1,' . $datarow->id . ',' . $datarow->status . ')">Approve</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(2,' . $datarow->id . ',' . $datarow->status . ')">Reject</a>
                            </li>
                            <li id="dropdown-menu">
                                <a onclick="chagestatus(3,' . $datarow->id . ',' . $datarow->status . ')">Total Lost</a>
                              </li>
                        </ul>';
            }

            $insuranceclaimstatus = '<div class="dropdown" style="float: left;">' . $dropdownmenu . '</div>';

            $row[] = ++$counter;
            $row[] = (!empty($datarow->vehiclename))?'<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#insuranceclaimtab" target="_blank">'.$datarow->vehiclename." (".$datarow->vehicleno.")</a>":'-';
            $row[] = (!empty($datarow->companyname))?$datarow->companyname." (".$datarow->policyno.")":'-';
            $row[] = $this->general_model->displaydate($datarow->insuranceclaimdate);
            $row[] = $datarow->billnumber;
            $row[] = $billamount;
            $row[] = $datarow->claimnumber;
            $row[] = $claimamount;
            $row[] = $insuranceclaimstatus;
            $row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
            "recordsTotal" => $this->Insurance_claim->count_all(),
            "recordsFiltered" => $this->Insurance_claim->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add_insurance_claim() {

        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Insurance Claim";
        $this->viewData['module'] = "insurance_claim/Add_insurance_claim";

        $this->viewData['vehicledata'] = $this->Insurance_claim->getVehicle();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add-insurance-claim", "pages/add_insurance_claim.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function edit_insurance_claim($id) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Insurance Claim";
        $this->viewData['module'] = "insurance_claim/Add_insurance_claim";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['vehicledata'] = $this->Insurance_claim->getVehicle();
        $this->viewData['insuranceclaimdata'] = $this->Insurance_claim->getvehicleinsuranceclaimDataByID($id);
        $this->viewData['insuranceclaimfiledata'] = $this->Insurance_claim->getInsuranceClaimFileDataByID($id);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_vehicle", "pages/add_insurance_claim.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function insurance_claim_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
       
        $insurancecompany = $PostData['insurancecompany'];
        $insuranceid = $PostData['policynumber'];
        $agentname = $PostData['agentname'];
        $billnumber = $PostData['billnumber'];
        $amount = $PostData['amount'];
        $claimnumber = $PostData['claimnumber'];
        $claimamount = $PostData['claimamount'];
        $status = $PostData['status'];
        $insurancedate = ($PostData['insurancedate']!="")?$this->general_model->convertdate($PostData['insurancedate']):'';

        $file = "";
        if (!is_dir(INSURANCE_CLAIM_PATH)) {
            @mkdir(INSURANCE_CLAIM_PATH);
        }
        $this->Insurance_claim->_where = array("insuranceid"=>$insuranceid);
        $Count = $this->Insurance_claim->CountRecords();

            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if (strpos($key, 'attachment') !== false && $_FILES['attachment' . $id]['name'] != '') {
                        if ($_FILES['attachment' . $id]['size'] != '' && $_FILES['attachment' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            echo -1;
                            exit;
                        }
                        $file = uploadFile('attachment' . $id, 'INSURANCE_CLAIM', INSURANCE_CLAIM_PATH, '*', '', 1, INSURANCE_CLAIM_LOCAL_PATH, '', '', 0);
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
                "insuranceid" => $insuranceid,
                "insuranceagentid" => $agentname,
                "billnumber" => $billnumber,
                "billamount" => $amount,
                "claimnumber" => $claimnumber,
                "claimamount" => $claimamount,
                "insuranceclaimdate" => $insurancedate,
                "status" => $status,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );

            $insertdata = array_map('trim', $insertdata);
            $Add = $this->Insurance_claim->Add($insertdata);
       
            if ($Add) {
                if (!empty($_FILES)) {
                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);
                        $file = $_FILES['attachment' . $id]['name'];

                        if (strpos($key, 'attachment' . $id) !== false && $_FILES['attachment' . $id]['name'] != '') {
                            $file = uploadFile('attachment' . $id, 'INSURANCE_CLAIM', INSURANCE_CLAIM_PATH, '*', '', 1, INSURANCE_CLAIM_LOCAL_PATH);

                            if ($file == 0 && $file == 2) {
                                $file = "";
                            }
                            if(!empty($file)){
                                $insertData[] = array(
                                    "insuranceclaimid" => $Add,
                                    "file" => $file,
                                    "modifieddate" => $createddate,
                                    "modifiedby" => $addedby
                                );
                            }
                        }
                    }

                    if (!empty($insertData)) {
                        $this->Insurance_claim->_table = tbl_insuranceclaimdocument;
                        $this->Insurance_claim->add_batch($insertData);
                    }
                }
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(1, 'Insurance Claim', 'Add new '.$insurancecompany.' insurance claim.');
                }
                echo 1;
            }
        
    }

    public function update_insurance_claim() {

        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $insuranceclaimid = $PostData['insuranceclaimid'];
        $insurancecompany = $PostData['insurancecompany'];
        $insuranceid = $PostData['policynumber'];
        $agentname = $PostData['agentname'];
        $billnumber = $PostData['billnumber'];
        $amount = $PostData['amount'];
        $claimnumber = $PostData['claimnumber'];
        $claimamount = $PostData['claimamount'];
        $status = $PostData['status'];
        $insurancedate = ($PostData['insurancedate']!="")?$this->general_model->convertdate($PostData['insurancedate']):'';
        
        $file = "";
        if (!is_dir(INSURANCE_CLAIM_PATH)) {
            @mkdir(INSURANCE_CLAIM_PATH);
        }

            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if (strpos($key, 'attachment') !== false && $_FILES['attachment' . $id]['name'] != '') {
                        if ($_FILES['attachment' . $id]['size'] != '' && $_FILES['attachment' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            echo -1;
                            exit;
                        }
                        $file = uploadFile('attachment' . $id, 'INSURANCE_CLAIM', INSURANCE_CLAIM_PATH, '*', '', 1, INSURANCE_CLAIM_LOCAL_PATH, '', '', 0);
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

            $updatedata = array(
                "insuranceid" => $insuranceid,
                "insuranceagentid" => $agentname,
                "billnumber" => $billnumber,
                "billamount" => $amount,
                "claimnumber" => $claimnumber,
                "claimamount" => $claimamount,
                "insuranceclaimdate" => $insurancedate,
                "status" => $status,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );

            $updatedata = array_map('trim', $updatedata);

            $this->Insurance_claim->_where = array("id" => $insuranceclaimid);
            $Edit = $this->Insurance_claim->Edit($updatedata);
            if ($Edit) {

                $documentidarray = isset($PostData['documentid']) ? $PostData['documentid'] : '';
                $olddocfilearray = isset($PostData['olddocfile']) ? $PostData['olddocfile'] : "";
                $insertDocumentData = $updateDocumentData = $deleteidsarray = array();

                if (!empty($_FILES)) {
                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);
                        if (strpos($key, 'attachment') !== false) {
                            $documentid = (isset($documentidarray[$id]) && !empty($documentidarray[$id])) ? $documentidarray[$id] : "";
                            if ($documentid != "") {
                                $file = $olddocfilearray[$id];
                                if ($_FILES['attachment' . $id]['name'] != '' && $olddocfilearray[$id] == "") {
                                    $file = uploadFile('attachment' . $id, 'INSURANCE_CLAIM', INSURANCE_CLAIM_PATH, '*', '', 1, INSURANCE_CLAIM_LOCAL_PATH);
                                    if ($file == 0 && $file == 2) {
                                        $file = "";
                                    }
                                } else if (($_FILES['attachment' . $id]['name'] != '' || $_FILES['attachment' . $id]['name'] == '') && $olddocfilearray[$id] != "") {
                                    $file = $olddocfilearray[$id];
                                    if ($_FILES['attachment' . $id]['name'] != '') {
    
                                        $file = reuploadFile('attachment' . $id, 'INSURANCE_CLAIM', $olddocfilearray[$id], INSURANCE_CLAIM_PATH, '*', '', 1, INSURANCE_CLAIM_LOCAL_PATH);
    
                                        if ($file == 0 && $file == 2) {
                                            $file = "";
                                        }
                                    }
    
                                }
                                if(!empty($file)){
                                    $updateDocumentData[] = array(
                                        'id' => $documentid,
                                        "insuranceclaimid" => $insuranceclaimid,
                                        "file" => $file,
                                        "modifieddate" => $modifieddate,
                                        "modifiedby" => $modifiedby
                                    );
                                    
                                    $deleteidsarray[] = $documentid;
                                }
                            }else {
                                if ($_FILES['attachment' . $id]['name'] != '') {
    
                                    $file = uploadFile('attachment' . $id, 'INSURANCE_CLAIM', INSURANCE_CLAIM_PATH, '*', '', 1, INSURANCE_CLAIM_LOCAL_PATH);
                                    if ($file == 0 && $file == 2) {
                                        $file = "";
                                    }
                                    if(!empty($file)){
                                        $insertDocumentData[] = array(
                                            "insuranceclaimid" => $insuranceclaimid,
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

                $insuranceclaimfiledata = $this->Insurance_claim->getInsuranceClaimFileDataByID($insuranceclaimid);
                $insuranceclaimdocumentidarray = (!empty($insuranceclaimfiledata) ? array_column($insuranceclaimfiledata, "id") : array());
                if (!empty($documentidarray)) {
                    $deletearr = array_diff($insuranceclaimdocumentidarray, $deleteidsarray);
                }
                if (!empty($deletearr)) {

                    $unlinkinsuranceclaimdocuments = $this->Insurance_claim->getInsuranceClaimFileByID(implode(",", $deletearr));
                    if(!empty($unlinkinsuranceclaimdocuments)){
                        foreach($unlinkinsuranceclaimdocuments as $doc){
                            unlinkfile('INSURANCE_CLAIM', $doc['file'], INSURANCE_CLAIM_PATH);
                        }
                    }
                    $this->Insurance_claim->_table = tbl_insuranceclaimdocument;
                    $this->Insurance_claim->Delete(array("id IN (" . implode(",", $deletearr) . ")" => null));
                }
                if (count($insertDocumentData) > 0) {
                    $this->Insurance_claim->_table = tbl_insuranceclaimdocument;
                    $this->Insurance_claim->add_batch($insertDocumentData);
                }
                if (count($updateDocumentData) > 0) {
                    $this->Insurance_claim->_table = tbl_insuranceclaimdocument;
                    $this->Insurance_claim->edit_batch($updateDocumentData, "id");
                }

                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Insurance Claim', 'Edit '.$insurancecompany.' insurance claim.');
                }
                echo 1;
            } else {
                echo 0;
            }
    }

    public function check_insurance_claim_use() {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach($ids as $row){
        	/* $query = $this->db->query("SELECT id FROM ".tbl_vehicle." WHERE 
        			id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
        			//OR id IN (SELECT vehicleid FROM ".tbl_vehicleroute." WHERE vehicleid = $row)
        	if($query->num_rows() > 0){
        		$count++;
        	} */
        }
        echo $count;
    }

    public function delete_mul_insurance_claim(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {

            $this->Insurance_claim->_fields = ("attachment,(SELECT companyname FROM ".tbl_insurance." WHERE id=insuranceid) as companyname");
            $this->Insurance_claim->_where = array("id"=>$row);
            $data = $this->Insurance_claim->getRecordsById();
            if(!empty($data)){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(3,'Insurance Claim','Delete '.$data['companyname'].' insurance claim.');
                }
                unlinkfile("INSURANCE_CLAIM", $data['attachment'], INSURANCE_CLAIM_PATH);
            }
            $this->Insurance_claim->Delete(array("id" => $row));
        }
    }

    public function update_status() {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $status = $PostData['status'];
        $insuranceclaimid = $PostData['insuranceclaimid'];
        
        $updateData = array(
            'status' => $status,
            'modifieddate' => $modifieddate,
            'modifiedby' => $modifiedby
        );

        $this->Insurance_claim->_where = array("id"=>$insuranceclaimid);
        $Edit = $this->Insurance_claim->Edit($updateData);
        if ($Edit) {

            $this->Insurance_claim->_fields = ("(SELECT companyname FROM ".tbl_insurance." WHERE id=insuranceid) as companyname");
            $this->Insurance_claim->_where = array("id"=>$insuranceclaimid);
            $data = $this->Insurance_claim->getRecordsById();
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Insurance Claim','Change status of '.$data['companyname'].' insurance claim.');
            }
            echo 1;
        } else {
            echo 0;
        }
    }

    public function getinsurancepolicynumber() {
        $PostData = $this->input->post();
        
        $insurancecalimdata = $this->Insurance_claim->getinsuranceclaimpolicybycompanyname($PostData['insurancecompany']);
        echo json_encode($insurancecalimdata);
    }

    public function exportToExcelInsuranceClaim(){
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Insurance Claim','Export to excel Insurance Claim.');
        }
        $exportdata = $this->Insurance_claim->getInsuranceClaimDataForExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {
            $status='';
            if($row->status==0){
                $status="Pending";
            }elseif($row->status==1){
                $status="Approve";
            }elseif($row->status==2){
                $status="Rejected";
            }elseif($row->status==3){
                $status="Cancle";
            }
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->companyname!=''?$row->companyname.($row->policyno!=''?" (".$row->policyno.")":''):'-'),
                            ($row->agentname!=''?$row->agentname:'-'),
                            ($row->billnumber!=''?$row->billnumber:'-'),
                            ($row->billamount>0?numberFormat($row->billamount,2,','):'-'),
                            ($row->claimnumber!=''?$row->claimnumber:'-'),
                            ($row->claimamount>0?numberFormat($row->claimamount,2,','):'-'),
                            ($row->insuranceclaimdate!='0000-00-00'?$this->general_model->displaydate($row->insuranceclaimdate):'-'),
                            ($status!=''?$status:'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydate($row->createddate):'-')
                        );
        }  
        $headings = array('Sr. No.','Vehicle Name','Company Name (Policy No.)','Agent Name','Bill Number','Bill Amount','Claim Number','Claim Amount','Insurance Claim Date','Status','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Insurance Claim",$headings,"Insurance-Claim.xls",array('F','H'));

	}

	public function exportToPDFInsuranceClaim(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Insurance Claim','Export to PDF Insurance Claim.');
        }

        $PostData['reportdata'] = $this->Insurance_claim->getInsuranceClaimDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Insurance Claim';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'insurance_claim/InsuranceClaimforPDFFormate', $PostData,true);

        $this->general_model->exportToPDF("Insurance-Claim.pdf",$header,$html);
	}
	
	public function printInsuranceClaimDetails(){

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Insurance Claim','Print Insurance Claim.');
        }

        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Insurance_claim->getInsuranceClaimDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Insurance Claim';

        $html['content'] = $this->load->view(ADMINFOLDER."insurance_claim/PrintFormateForIncuranceClaim.php",$PostData,true);
        echo json_encode($html); 
    }
}
