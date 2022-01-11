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
        $this->load->model('Party_contact_model', 'Party_contact');
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
            $Action .= '<a class="' . view_class . '" href="' . ADMIN_URL . 'party/view-party/' . $datarow->id . '" title=' . view_title . ' target="_blank">' . view_text . '</a>';

            $row[] = ++$counter;
            $row[] = $datarow->companyname;
            $row[] = $datarow->partytypename;
            $row[] = $datarow->contactdetails;
            $row[] = $datarow->cityname;
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
       

        $this->load->model('Party_doc_model', 'Party_doc');
        $this->viewData['party_docdata'] = $this->Party_doc->getparty_docdataByID($partyid);

        $this->load->model('party_contact_model', 'party_contact');
        $this->viewData['party_contactdata'] = $this->party_contact->getpartycontactdatadataByID($partyid);

        $this->viewData['partydata'] = $this->Party->getPartyDataByID($partyid);
        if (empty($this->viewData['partydata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }



        $this->load->model('Country_model', 'Country');
        $this->viewData['countrydata'] = $this->Country->getActivecountrylist();

        $this->load->model('Province_model', 'Province');
        $this->viewData['provincedata'] = $this->Province->getRecordByID();

        $this->load->model('Company_model', 'Companymodel');
        $this->viewData['Companydata'] = $this->Companymodel->getRecordByID();

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
        $pid = "";
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $PostData = $this->input->post();
        $cloopcount = $PostData['cloopcount'];

        $websitename = $PostData['websitename'];
        $companyid = $PostData['companyid'];
        $partycode = $PostData['partycode'];
        $gst = $PostData['gst'];
        $pan = $PostData['pan'];
        $partytypeid = $PostData['partytypeid'];
        $countryid = $PostData['countryid'];
        $stateid = $PostData['stateid'];
        $cityid = $PostData['cityid'];
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $courieraddress = $PostData['courieraddress'];
        $openingdate = ($PostData['openingdate'] != "") ? $this->general_model->convertdate($PostData['openingdate']) : "";
        $openingamount = $PostData['openingamount'];
        $json = array();

        $insertdata = array(
            "websitename" => $websitename,
            "companyid" => $companyid,
            "gst" => $gst,
            "pan" => $pan,
            "partycode" => $partycode,
            "partytypeid" => $partytypeid,
            "countryid" => $countryid,
            "provinceid" => $stateid,
            "cityid" => $cityid,
            "billingaddress" => $billingaddress,
            "shippingaddress" => $shippingaddress,
            "courieraddress" => $courieraddress,
            "openingdate" => $openingdate,
            "openingamount" => $openingamount,
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "addedby" => $addedby,
            "modifiedby" => $addedby,
        );

        $insertdata = array_map('trim', $insertdata);
        $partyid = $this->Party->Add($insertdata);
        $pid = $partyid;
        for ($i = 1; $i <= $cloopcount; $i++):
            $data = $this->input->post();
            $firstname = $this->input->post('firstname_' . $i);
            $lastname = $this->input->post('lastname_' . $i);
            $contactno = $this->input->post('contactno_' . $i);
            $birthdate = $this->input->post('birthdate_' . $i);
            $anniversarydate = $this->input->post('anniversarydate_' . $i);
            $email = $this->input->post('email_' . $i);
            $contectid = $this->input->post('contectid_' . $i);
            if ($contectid == 0 or $contectid == '') {
                $insertdata2 = array(
                    'partyid' => $partyid,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'contactno' => $contactno,
                    'birthdate' => $this->general_model->convertdate($birthdate),
                    'anniversarydate' => $this->general_model->convertdate($anniversarydate),
                    'email' => $email,
                    'createddate' => $createddate,
                    'modifieddate' => $createddate,
                    'addedby' => $addedby,
                    'modifiedby' => $addedby,
                );

                $this->load->model('Party_contact_model', 'Party_contact');
                $PartycontactId = $this->Party_contact->Add($insertdata2);

            }
        endfor;
        $cloopcount = $PostData['cloopcount'];
        $insertDocumentData = array();
        $this->load->model('Party_doc_model', 'Party_doc');

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $id = preg_replace('/[^0-9]/', '', $key);
                $documentnumber = $PostData['documentname_' . $id];

                if (isset($_FILES['docfile_' . $id]['name']) && $_FILES['docfile_' . $id]['name'] != '' && strpos($key, 'docfile_') !== false) {

                    $temp = explode('.', $_FILES['docfile_' . $id]['name']);
                    $extension = end($temp);
                    $type = 0;
                    $image_width = $image_height = '';
                    $Imageextensions = array("bmp", "bm", "gif", "ico", "jfif", "jfif-tbnl", "jpe", "jpeg", "jpg", "pbm", "png", "svf", "tif", "tiff", "wbmp", "x-png");
                    if (in_array($extension, $Imageextensions, true)) {
                        $type = 1;
                        $image_width = PRODUCT_IMG_WIDTH;
                        $image_height = PRODUCT_IMG_HEIGHT;
                    }

                    $file = uploadFile('docfile_' . $id, 'DOCUMENT', PARTY_PATH, '*', '', 1, PARTY_LOCAL_PATH, $image_width, $image_height);

                    if ($file !== 0) {
                        if ($file == 2) {
                            echo 3; //image not uploaded
                            exit;
                        }
                        $insertdata3 = array(
                            "partyid" => $partyid,
                            "doc" => $file,
                            "docname" => $documentnumber,
                        );

                        $this->Party_doc->add($insertdata3);
                    } else {
                        echo 3; //INVALID image TYPE
                        exit;
                    }
                } else {
                    $file = '';
                }
            }
            $json = 1;
        }
        echo json_encode($json);
    }

    public function update_party()
    {
        $pid = "";
        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $cloopcount = $PostData['cloopcount'];

        $partyid = $PostData['partyid'];
        $websitename = $PostData['websitename'];
        $companyid = $PostData['companyid'];
        $partycode = $PostData['partycode'];
        $gst = $PostData['gst'];
        $pan = $PostData['pan'];
        $partytypeid = $PostData['partytypeid'];
        $countryid = $PostData['countryid'];
        $stateid = $PostData['stateid'];
        $cityid = $PostData['cityid'];
        $billingaddress = $PostData['billingaddress'];
        $shippingaddress = $PostData['shippingaddress'];
        $courieraddress = $PostData['courieraddress'];
        $openingdate = ($PostData['openingdate'] != "") ? $this->general_model->convertdate($PostData['openingdate']) : "";
        $openingamount = $PostData['openingamount'];
        $json = array();
        $pid = $partyid;
        $insertdata4 = array(
            "websitename" => $websitename,
            "companyid" => $companyid,
            "partycode" => $partycode,
            "gst" => $gst,
            "pan" => $pan,
            "partytypeid" => $partytypeid,
            "countryid" => $countryid,
            "provinceid" => $stateid,
            "cityid" => $cityid,
            "billingaddress" => $billingaddress,
            "shippingaddress" => $shippingaddress,
            "courieraddress" => $courieraddress,
            "openingdate" => $openingdate,
            "openingamount" => $openingamount,
            "modifieddate" => $createddate,
            "modifiedby" => $addedby,
        );

        $this->Party->_where = array("id" => $PostData['partyid']);
        $partyid = $this->Party->Edit($insertdata4);

  
      
        for ($i = 1; $i <= $cloopcount; $i++):
       
            $data = $this->input->post();
            $firstname = $this->input->post('firstname_' . $i);
            $lastname = $this->input->post('lastname_' . $i);
            $contactno = $this->input->post('contactno_' . $i);
            $birthdate = $this->input->post('birthdate_' . $i);
            $anniversarydate = $this->input->post('anniversarydate_' . $i);
            $email = $this->input->post('email_' . $i);

            $contectid = $this->input->post('contectid_' . $i);
            if($firstname!=''){
                if ($contectid != 0) {
                    $insertdata2 = array(
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'contactno' => $contactno,
                        'birthdate' => $this->general_model->convertdate($birthdate),
                        'anniversarydate' => $this->general_model->convertdate($anniversarydate),
                        'email' => $email,
                        'modifieddate' => $createddate,
                        'modifiedby' => $addedby,
                    );

                    $this->Party_contact->_where = array("id" => $contectid);
                    $partyid = $this->Party_contact->Edit($insertdata2);

                } else {
                    $insertdata2 = array(
                        'partyid' => $pid,
                        'firstname' => $firstname,
                        'lastname' => $lastname,
                        'contactno' => $contactno,
                        'birthdate' => $this->general_model->convertdate($birthdate),
                        'anniversarydate' => $this->general_model->convertdate($anniversarydate),
                        'email' => $email,
                        'createddate' => $createddate,
                        'modifieddate' => $createddate,
                        'addedby' => $addedby,
                        'modifiedby' => $addedby,
                    );
                
                    $this->load->model('Party_contact_model', 'Party_contact');
                    $PartycontactId = $this->Party_contact->Add($insertdata2);
                }
            }else if($contectid != ""){

                $this->Party_contact->Delete(array("id" => $contectid));
            }

            endfor;

        $cloopdoc = $PostData['cloopdoc'];
        $insertDocumentData = array();
        $this->load->model('Party_doc_model', 'Party_doc');

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
              
                $id = preg_replace('/[^0-9]/', '', $key);
                $documentnumber = $PostData['documentname_'.$id];
                $doc_id = $PostData['doc_id_' . $id];

                if($documentnumber != ''){
                    if (isset($_FILES['docfile_' . $id]['name']) && $_FILES['docfile_' . $id]['name'] != '' && strpos($key, 'docfile_') !== false) {
    
                        $temp = explode('.', $_FILES['docfile_' . $id]['name']);
                        $extension = end($temp);
                        $type = 0;
                        $image_width = $image_height = '';
                        $Imageextensions = array("bmp", "bm", "gif", "ico", "jfif", "jfif-tbnl", "jpe", "jpeg", "jpg", "pbm", "png", "svf", "tif", "tiff", "wbmp", "x-png");
                        if (in_array($extension, $Imageextensions, true)) {
                            $type = 1;
                            $image_width = PRODUCT_IMG_WIDTH;
                            $image_height = PRODUCT_IMG_HEIGHT;
                        }
    
                            $file = uploadFile('docfile_' . $id, 'DOCUMENT', PARTY_PATH, '*', '', 1, PARTY_LOCAL_PATH, $image_width, $image_height);
    
                        if ($file !== 0) {
                            if ($file == 2) {
                                echo 3; //image not uploaded
                                exit;
                            }
    
                            if ($doc_id != 0) {
                                $insertdata4 = array(
                                    "docname" => $documentnumber,
                                    "doc" => $file,
                                );
                                $this->Party_doc->_where = array("id" => $doc_id);
                                $partyid = $this->Party_doc->Edit($insertdata4);
                            } else {
                                
                                $insertdata4 = array(
                                    "partyid" => $pid,
                                    "docname" => $documentnumber,
                                    "doc" => $file,
                                );
                                $this->Party_doc->Add($insertdata4);
                            }
    
                        } else {
                            echo 3; //INVALID image TYPE
                            exit;
                        }
                    } else {
                        $file = '';
                    }
                }
            }

            for ($i = 1; $i <= $cloopdoc; $i++):

                $docdata = $this->input->post('documentname_' . $i);
                $docid = $this->input->post('doc_id_' . $i);

                if($docid != "" && $docdata == ""){
                    $this->Party_doc->Delete(array("id" => $docid));                 
                }

            endfor;
          
            $json = 1;

           
        }

        echo json_encode($json);
    }

    public function view_party($partyid)
    {
      
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Party";
        $this->viewData['module'] = "party/View_party";
        $this->viewData['partydata'] = $this->Party->getPartyDetailById($partyid);
        $this->viewData['partycontectdata'] = $this->Party->getPartycontectDetailById($partyid);
        $this->viewData['eid']=$partyid;
       
      
        if (empty($this->viewData['partydata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }
  
        // $this->load->model('Document_type_model', 'Document_type');
        // $this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();

        // $this->load->model('City_model', 'City');
        // $this->viewData['sitecitydata'] = $this->City->getActiveCityOnAssignedSiteByParty($partyid);

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
    public function getcity()
    {
        $postData = $this->input->post();
        $this->load->model('City_model', 'City');
        $data = $this->City->getcity($postData);
        echo json_encode($data);
    }

    public function cloop($id)
    {
       
        $params = array('id' => $id);
        // $query = "select * from " . TBL_MEDICINE . " where isdelete=0 and id > 1";
        // $params['data'] = $this->Queries->get_tab_list($query, 'id', 'medicine');
        $this->load->view('rkinsite/party/item.php', $params);
    }

    public function addprodocitem($id)
    {

        $params = array('id' => $id);
        $this->load->view('rkinsite/party/itemdoc.php', $params);
    }
}   