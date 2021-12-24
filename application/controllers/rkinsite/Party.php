<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Party extends Admin_Controller
{

    public $viewData = array();
    public function __construct()
    {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Party');
        $this->load->model('Party_model', 'Party');
    }
    public function index()
    {

        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Party";
        $this->viewData['module'] = "party/Party";

        $this->load->model('Party_type_model', 'Party_type');
        $this->viewData['partytype'] = $this->Party_type->getActivePartyType();

        $this->viewData['citydata'] = $this->Party->getCityListOnParty();

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Party', 'View party.');
        }
        $this->admin_headerlib->add_javascript("party", "pages/party.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing()
    {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $list = $this->Party->get_datatables();

        $data = array();
        $counter = $_POST['start'];

        foreach ($list as $datarow) {
            $row = array();
            $Action = $checkbox = '';

            if (in_array($rollid, $edit)) {
                $Action .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'party/edit-party/' . $datarow->id . '" title=' . edit_title . '>' . edit_text . '</a>';
            }
            if (in_array($rollid, $delete)) {
                $Action .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"' . ADMIN_URL . 'party/check-party-use","Party","' . ADMIN_URL . 'party/delete-mul-party") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input value="' . $datarow->id . '" type="checkbox" class="checkradios" name="check' . $datarow->id . '" id="check' . $datarow->id . '" onchange="singlecheck(this.id)"><label for="check' . $datarow->id . '"></label></div>';
            }
            $Action .= '<a class="' . view_class . '" href="' . ADMIN_URL . 'party/view-party/' . $datarow->id . '#personaldetails" title=' . view_title . ' target="_blank">' . view_text . '</a>';

            /* if($datarow->documentfile!="" && file_exists(DOCUMENT_PATH.$datarow->documentfile)){
            $Action .= '<a class="'.download_class.'" href="'.DOCUMENT.$datarow->documentfile.'" title="'.download_title.'" download>'.download_text.'</a>';
            $Action .= '<a class="'.viewdoc_class.'" href="'.DOCUMENT.$datarow->documentfile.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
            } */

            $partyname = '<a href="' . ADMIN_URL . 'party/view-party/' . $datarow->id . '" target="_blank">' . ($datarow->firstname . " " . $datarow->middlename . " " . $datarow->lastname . " (" . $datarow->partycode . ")") . "</a>";

            $row[] = ++$counter;
            $row[] = $partyname;
            $row[] = $datarow->partytype;
            $row[] = $datarow->contactno1;
            $row[] = ($datarow->cityname != "") ? $datarow->cityname : "-";
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Party->count_all(),
            "recordsFiltered" => $this->Party->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add_party()
    {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Party";
        $this->viewData['module'] = "party/Add_party";

        $this->load->model('Party_type_model', 'Party_type');
        $this->viewData['partytypedata'] = $this->Party_type->getActivePartyType();

        $this->load->model('Country_model', 'Country');
        $this->viewData['countrydata'] = $this->Country->getActivecountrylist();

        $this->load->model('Province_model', 'Province');
        $this->viewData['provincedata'] = $this->Province->getRecordByID();

        $this->load->model('Company_model', 'Companymodel');
        $this->viewData['Companydata'] = $this->Companymodel->getRecordByID();

        // $this->load->model('City_model','City');
        // $this->viewData['citydata'] = $this->City->getRecordByID();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("party", "pages/add_party.js");

        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function edit_party($partyid)
    {

        $this->viewData['title'] = "Edit Party";
        $this->viewData['module'] = "party/Add_party";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['partydata'] = $this->Party->getPartyDataByID($partyid);
        if (empty($this->viewData['partydata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }
        $this->viewData['partydocumentdata'] = $this->Party->getPartyDocumentsByPartyID($partyid);

        $this->load->model('Document_type_model', 'Document_type');
        $this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();

        $this->load->model('User_role_model', 'User_role');
        $this->viewData['userroledata'] = $this->User_role->getAllActiveUsersNotSuperAdminRole();

        $this->load->model('Party_type_model', 'Party_type');
        $this->viewData['partytypedata'] = $this->Party_type->getActivePartyType();

        $this->load->model('Country_model', 'Country');
        $this->viewData['countrydata'] = $this->Country->getActivecountrylist();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("party", "pages/add_party.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function party_add()
    {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $firstname = $PostData['firstname'];
        $middlename = $PostData['middlename'];
        $lastname = $PostData['lastname'];
        $partycode = $PostData['partycode'];
        $birthdate = ($PostData['birthdate'] != "") ? $this->general_model->convertdate($PostData['birthdate']) : "";
        $anniversarydate = ($PostData['anniversarydate'] != "") ? $this->general_model->convertdate($PostData['anniversarydate']) : "";
        $education = $PostData['education'];
        $partytypeid = $PostData['partytypeid'];
        $email = $PostData['email'];
        $contactno1 = $PostData['contactno1'];
        $contactno2 = $PostData['contactno2'];
        $gender = $PostData['gender'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $address = $PostData['address'];
        $allowforlogin = isset($PostData['allowforlogin']) ? 1 : 0;

        if ($allowforlogin == 1) {
            if ($PostData['password'] != "") {
                $password = $this->general_model->encryptIt($PostData['password']);
            } else {
                $password = $this->general_model->encryptIt(DEFAULT_PASSWORD);
            }
            if ($PostData['employeerole'] != 0) {
                $employeerole = $PostData['employeerole'];
            }
        } else {
            $password = "";
            $employeerole = 0;
        }

        $json = array();
        $fieldArray = array("email", "contactno1", "contactno2", "partycode");
        $valueArray = array($email, $contactno1, $contactno2, $partycode);
        //Check email & contact number duplicated or not
        $Check = $this->Party->CheckDuplicateValueAvailableInParty($fieldArray, $valueArray);
        if (empty($Check)) {

            if (!is_dir(DOCUMENT_PATH)) {
                @mkdir(DOCUMENT_PATH);
            }
            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if (strpos($key, 'docfile') !== false && $_FILES['docfile' . $id]['name'] != '') {
                        if ($_FILES['docfile' . $id]['size'] != '' && $_FILES['docfile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            $json = array('error' => -1, "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('docfile' . $id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH, '', '', 0);
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

            $insertdata = array("partytypeid" => $partytypeid,
                "firstname" => $firstname,
                "middlename" => $middlename,
                "lastname" => $lastname,
                "partycode" => $partycode,
                "email" => $email,
                "contactno1" => $contactno1,
                "contactno2" => $contactno2,
                "gender" => $gender,
                "birthdate" => $birthdate,
                "anniversarydate" => $anniversarydate,
                "education" => $education,
                "address" => $address,
                "cityid" => $cityid,
                "provinceid" => $provinceid,
                "allowforlogin" => $allowforlogin,
                "employeeroleid" => $employeerole,
                "password" => $password,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby,
            );

            $insertdata = array_map('trim', $insertdata);
            $PartyId = $this->Party->Add($insertdata);

            if ($PartyId) {

                $documenttypeid = $PostData['documenttypeid'];
                $documentnumber = $PostData['documentnumber'];
                $fromdate = $PostData['fromdate'];
                $duedate = $PostData['duedate'];
                $licencetype = $PostData['licencetype'];

                $insertDocumentData = array();
                $this->load->model('Document_model', 'Document');
                if (!empty($_FILES)) {

                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);

                        if (!empty($documenttypeid[$id]) && !empty($documentnumber[$id])) {

                            $this->Document->_where = array("referencetype" => 1, "referenceid" => $PartyId, "documenttypeid" => $documenttypeid[$id], "documentnumber" => $documentnumber[$id]);
                            $Count = $this->Document->CountRecords();

                            if ($Count == 0) {

                                $file = "";
                                if (strpos($key, 'docfile') !== false && $_FILES['docfile' . $id]['name'] != '') {
                                    $file = uploadFile('docfile' . $id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);
                                    if ($file == 0 && $file == 2) {
                                        $file = "";
                                    }
                                }

                                $insertDocumentData[] = array("referencetype" => 1,
                                    "referenceid" => $PartyId,
                                    "documenttypeid" => $documenttypeid[$id],
                                    "documentnumber" => $documentnumber[$id],
                                    "fromdate" => ($fromdate[$id] != "" ? $this->general_model->convertdate($fromdate[$id]) : ""),
                                    "duedate" => ($duedate[$id] != "" ? $this->general_model->convertdate($duedate[$id]) : ""),
                                    "licencetype" => $licencetype[$id],
                                    "documentfile" => $file,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby,
                                );
                            }
                        }
                    }
                    if (count($insertDocumentData) > 0) {
                        $this->Document->add_batch($insertDocumentData);
                    }
                }

                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(1, 'Party', 'Add new ' . $firstname . ' ' . $lastname . ' party.');
                }
                $json = array("error" => 1);
            } else {
                $json = array("error" => 0);
            }
        } else {
            $json = array("error" => 2);
        }
        echo json_encode($json);
    }

    public function update_party()
    {

        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $partyid = $PostData['partyid'];
        $firstname = $PostData['firstname'];
        $middlename = $PostData['middlename'];
        $lastname = $PostData['lastname'];
        $partycode = $PostData['partycode'];
        $birthdate = ($PostData['birthdate'] != "") ? $this->general_model->convertdate($PostData['birthdate']) : "";
        $anniversarydate = ($PostData['anniversarydate'] != "") ? $this->general_model->convertdate($PostData['anniversarydate']) : "";
        $education = $PostData['education'];
        $partytypeid = $PostData['partytypeid'];
        $email = $PostData['email'];
        $contactno1 = $PostData['contactno1'];
        $contactno2 = $PostData['contactno2'];
        $gender = $PostData['gender'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $address = $PostData['address'];
        $allowforlogin = isset($PostData['allowforlogin']) ? 1 : 0;

        if ($allowforlogin == 1) {
            if ($PostData['password'] != "") {
                $password = $this->general_model->encryptIt($PostData['password']);
            } else {
                $password = $this->general_model->encryptIt(DEFAULT_PASSWORD);
            }
            if ($PostData['employeerole'] != 0) {
                $employeerole = $PostData['employeerole'];
            }
        } else {
            $password = "";
            $employeerole = 0;
        }

        $json = array();
        $fieldArray = array("email", "contactno1", "contactno2", "partycode");
        $valueArray = array($email, $contactno1, $contactno2, $partycode);
        //Check email & contact number duplicated or not
        $Check = $this->Party->CheckDuplicateValueAvailableInParty($fieldArray, $valueArray, $partyid);
        if (empty($Check)) {
            if (!is_dir(DOCUMENT_PATH)) {
                @mkdir(DOCUMENT_PATH);
            }
            if (!empty($_FILES)) {
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if (strpos($key, 'docfile') !== false && $_FILES['docfile' . $id]['name'] != '') {
                        if ($_FILES['docfile' . $id]['size'] != '' && $_FILES['docfile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            $json = array('error' => -1, "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('docfile' . $id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH, '', '', 0);
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

            $updatedata = array("partytypeid" => $partytypeid,
                "firstname" => $firstname,
                "middlename" => $middlename,
                "lastname" => $lastname,
                "partycode" => $partycode,
                "email" => $email,
                "contactno1" => $contactno1,
                "contactno2" => $contactno2,
                "gender" => $gender,
                "birthdate" => $birthdate,
                "anniversarydate" => $anniversarydate,
                "education" => $education,
                "address" => $address,
                "cityid" => $cityid,
                "provinceid" => $provinceid,
                "allowforlogin" => $allowforlogin,
                "employeeroleid" => $employeerole,
                "password" => $password,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby,
            );

            $this->Party->_where = array("id" => $partyid);
            $Edit = $this->Party->Edit($updatedata);
            if ($Edit) {

                $documenttypeid = $PostData['documenttypeid'];
                $documentnumber = $PostData['documentnumber'];
                $fromdate = $PostData['fromdate'];
                $duedate = $PostData['duedate'];
                $licencetype = $PostData['licencetype'];
                $documentidarray = isset($PostData['documentid']) ? $PostData['documentid'] : '';
                $olddocfilearray = isset($PostData['olddocfile']) ? $PostData['olddocfile'] : "";

                $insertDocumentData = $updateDocumentData = $deleteidsarray = array();
                $this->load->model('Document_model', 'Document');

                if (!empty($_FILES)) {

                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);

                        if (strpos($key, 'docfile') !== false) {

                            $documentid = (isset($documentidarray[$id]) && !empty($documentidarray[$id])) ? $documentidarray[$id] : "";

                            if ($documentid != "") {

                                if ($_FILES['docfile' . $id]['name'] != '' && $olddocfilearray[$id] == "") {
                                    $file = uploadFile('docfile' . $id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);
                                    if ($file == 0 && $file == 2) {
                                        $file = "";
                                    }
                                } else if (($_FILES['docfile' . $id]['name'] != '' || $_FILES['docfile' . $id]['name'] == '') && $olddocfilearray[$id] != "") {
                                    $file = $olddocfilearray[$id];
                                    if ($_FILES['docfile' . $id]['name'] != '') {

                                        $file = reuploadFile('docfile' . $id, 'DOCUMENT', $olddocfilearray[$id], DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);

                                        if ($file == 0 && $file == 2) {
                                            $file = "";
                                        }
                                    }
                                } else {
                                    $file = "";
                                }

                                $updateDocumentData[] = array('id' => $documentid,
                                    "documenttypeid" => $documenttypeid[$id],
                                    "documentnumber" => $documentnumber[$id],
                                    "fromdate" => ($fromdate[$id] != "" ? $this->general_model->convertdate($fromdate[$id]) : ""),
                                    "duedate" => ($duedate[$id] != "" ? $this->general_model->convertdate($duedate[$id]) : ""),
                                    "licencetype" => $licencetype[$id],
                                    "documentfile" => $file,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby" => $modifiedby,
                                );

                                $deleteidsarray[] = $documentid;
                            } else {
                                if (!empty($documenttypeid[$id]) && !empty($documentnumber[$id])) {
                                    $file = "";
                                    if ($_FILES['docfile' . $id]['name'] != '') {

                                        $file = uploadFile('docfile' . $id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);
                                        if ($file == 0 && $file == 2) {
                                            $file = "";
                                        }
                                    }
                                    $insertDocumentData[] = array("referencetype" => 1,
                                        "referenceid" => $partyid,
                                        "documenttypeid" => $documenttypeid[$id],
                                        "documentnumber" => $documentnumber[$id],
                                        "fromdate" => ($fromdate[$id] != "" ? $this->general_model->convertdate($fromdate[$id]) : ""),
                                        "duedate" => ($duedate[$id] != "" ? $this->general_model->convertdate($duedate[$id]) : ""),
                                        "licencetype" => $licencetype[$id],
                                        "documentfile" => $file,
                                        "createddate" => $modifieddate,
                                        "modifieddate" => $modifieddate,
                                        "addedby" => $modifiedby,
                                        "modifiedby" => $modifiedby,
                                    );

                                }
                            }
                        }
                    }
                }

                $partydocumentdata = $this->Party->getPartyDocumentsByPartyID($partyid);
                $documentidarray = (!empty($partydocumentdata) ? array_column($partydocumentdata, "id") : array());
                if (!empty($documentidarray)) {
                    $deletearr = array_diff($documentidarray, $deleteidsarray);
                }
                if (!empty($deletearr)) {
                    $this->Document->Delete(array("id IN (" . implode(",", $deletearr) . ")" => null));
                }
                if (count($insertDocumentData) > 0) {
                    $this->Document->add_batch($insertDocumentData);
                }
                if (count($updateDocumentData) > 0) {
                    $this->Document->edit_batch($updateDocumentData, "id");
                }

                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Party', 'Edit ' . $firstname . ' ' . $lastname . ' party.');
                }
                $json = array("error" => 1);
            } else {
                $json = array("error" => 0);
            }
        } else {
            $json = array("error" => 2);
        }
        echo json_encode($json);
    }

    public function view_party($partyid)
    {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Party";
        $this->viewData['module'] = "party/View_party";

        $this->viewData['partydata'] = $this->Party->getPartyDetailById($partyid);

        if (empty($this->viewData['partydata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }

        $this->load->model('Document_type_model', 'Document_type');
        $this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();

        $this->load->model('City_model', 'City');
        $this->viewData['sitecitydata'] = $this->City->getActiveCityOnAssignedSiteByParty($partyid);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("view_party", "pages/view_party.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function check_party_use()
    {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach ($ids as $row) {
            /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE
        id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
        //OR id IN (SELECT vehicleid FROM ".tbl_vehicleroute." WHERE vehicleid = $row)
        if($query->num_rows() > 0){
        $count++;
        } */
        }
        echo $count;
    }

    public function delete_mul_party()
    {
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        $this->load->model('Document_model', 'Document');
        foreach ($ids as $row) {

            $this->Party->_where = array("id" => $row);
            $data = $this->Party->getRecordsById();

            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(3, 'Party', 'Delete ' . $data['firstname'] . ' ' . $data['lastname'] . ' party.');
            }

            $this->Document->_where = array("referencetype" => 1, "referenceid" => $row);
            $documents = $this->Document->getRecordsById();

            if (!empty($documents)) {
                foreach ($documents as $document) {
                    if ($document['documentfile'] != "") {
                        unlinkfile("DOCUMENT", $document['documentfile'], DOCUMENT_PATH);
                    }
                }
            }
            $this->Document->Delete(array("referencetype" => 1, "referenceid" => $row));
            $this->Party->Delete(array("id" => $row));
        }
    }

    public function documentlisting()
    {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $list = $this->Party->get_datatables('documents');

        $data = array();
        $counter = $_POST['start'];

        foreach ($list as $datarow) {
            $row = array();
            $Action = $checkbox = '';

            if (in_array($rollid, $edit)) {
                $Action .= '<a class="' . edit_class . '" href="javascript:void(0)" onclick="openDocumentModal(' . $datarow->referencetype . ',' . $datarow->referenceid . ',' . $datarow->id . ')" title=' . edit_title . '>' . edit_text . '</a>';
            }
            if (in_array($rollid, $delete)) {
                $Action .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"' . ADMIN_URL . 'document/check-document-use","Document","' . ADMIN_URL . 'document/delete-mul-document") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input value="' . $datarow->id . '" type="checkbox" class="checkradios" name="check' . $datarow->id . '" id="check' . $datarow->id . '" onchange="singlecheck(this.id)"><label for="check' . $datarow->id . '"></label></div>';
            }
            if ($datarow->documentfile != "" && file_exists(DOCUMENT_PATH . $datarow->documentfile)) {
                $Action .= '<a class="' . download_class . '" href="' . DOCUMENT . $datarow->documentfile . '" title="' . download_title . '" download>' . download_text . '</a>';
            }
            $row[] = ++$counter;
            $row[] = $datarow->documenttype;
            $row[] = $datarow->documentnumber;
            $row[] = ($datarow->fromdate != "0000-00-00") ? $this->general_model->displaydate($datarow->fromdate) : "-";
            $row[] = ($datarow->duedate != "0000-00-00") ? $this->general_model->displaydate($datarow->duedate) : "-";
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Party->count_all('documents'),
            "recordsFiltered" => $this->Party->count_filtered('documents'),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function assignedsitelisting()
    {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $list = $this->Party->get_datatables('assignedsite');

        $data = array();
        $counter = $_POST['start'];

        foreach ($list as $datarow) {
            $row = array();

            $row[] = $datarow->sitename;
            $row[] = ($datarow->address != "") ? $datarow->address : "-";
            $row[] = ($datarow->cityname != "") ? $datarow->cityname : "-";
            $row[] = ($datarow->provincename != "") ? $datarow->provincename : "-";
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Party->count_all('assignedsite'),
            "recordsFiltered" => $this->Party->count_filtered('assignedsite'),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function assignedvehiclelisting()
    {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $list = $this->Party->get_datatables('assignedvehicle');

        $data = array();
        $counter = $_POST['start'];

        foreach ($list as $datarow) {
            $row = array();

            $row[] = $datarow->vehiclename;
            $row[] = $datarow->vehicleno;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Party->count_all('assignedvehicle'),
            "recordsFiltered" => $this->Party->count_filtered('assignedvehicle'),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function exportToExcelParty()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Party', 'Export to excel party.');
        }
        $exportdata = $this->Party->getPartyDataforExport();

        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {

            $data[] = array(++$srno,
                ($row->partyname != '' ? $row->partyname : '-'),
                ($row->partytype != '' ? $row->partytype : '-'),
                ($row->role != '' ? $row->role : '-'),
                ($row->gender != '0' ? "Female" : 'Male'),
                ($row->birthdate != '0000-00-00' ? $this->general_model->displaydate($row->birthdate) : '-'),
                ($row->anniversarydate != '0000-00-00' ? $this->general_model->displaydate($row->anniversarydate) : '-'),
                ($row->education != '' ? $row->education : '-'),
                ($row->contactno1 != '' ? $row->contactno1 : '-'),
                ($row->contactno2 != '' ? $row->contactno2 : '-'),
                ($row->email != '' ? $row->email : '-'),
                ($row->address != '' ? $row->address : '-'),
                ($row->cityname != '' ? $row->cityname : '-'),
                ($row->provincename != '' ? $row->provincename : '-'),
                ($row->countryname != '' ? $row->countryname : '-'),
            );
        }

        $headings = array('Sr. No.', 'Party Name', 'Party Type', 'Role', 'Gender', 'Birth Date', 'Anniversary Date', 'Education', 'Contact 1', 'Contact 2', 'Email', 'Address', 'City', 'Province', 'Country');
        $this->general_model->exporttoexcel($data, "A1:P1", "Party", $headings, "Party.xls");
    }

    public function exportToPDFParty()
    {
        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Party', 'Export to PDF party.');
        }

        $PostData['reportdata'] = $this->Party->getPartyDataforExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Party';

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'party/PartyforPDF', $PostData, true);

        $this->general_model->exportToPDF("Party.pdf", $header, $html);
    }

    public function printPartyDetails()
    {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Party', 'Print Party.');
        }

        $PostData = $this->input->post();

        $PostData['reportdata'] = $this->Party->getPartyDataforExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Party';

        $html['content'] = $this->load->view(ADMINFOLDER . "party/PrintpartyFormate.php", $PostData, true);
        echo json_encode($html);
    }
    public function getstate()
    {
        $postData = $this->input->post();
        $this->load->model('Province_model', 'Province');
        $data = $this->Province->getstate($postData);
        echo json_encode($data);
    }
}
