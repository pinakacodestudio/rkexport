<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company extends Admin_Controller
{

    public $viewData = array();
    public function __construct()
    {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'company');
        $this->load->model('Company_model', 'company');
    }
    public function index()
    {
        $this->viewData['title'] = "Company";
        $this->viewData['module'] = "Company/Company";

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Company', 'View Company.');
        }

        $this->admin_headerlib->add_javascript("company", "pages/company.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing()
    {
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->company->get_datatables();

        $data = array();
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
            //Edit Button
            if (in_array($rollid, $edit)) {
                $actions .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'Company/edit_company/' . $datarow->id . '/' . '" title="' . edit_title . '">' . edit_text . '</a>';
            }
            //Delete and Enable/Disable Button
            if (in_array($rollid, $delete)) {
                $actions .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"","company","' . ADMIN_URL . 'Company/delete-mul-company") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck' . $datarow->id . '" onchange="singlecheck(this.id)" type="checkbox" value="' . $datarow->id . '" name="deletecheck' . $datarow->id . '" class="checkradios">
                            <label for="deletecheck' . $datarow->id . '"></label></div>';
            }

            $row[] = ++$counter;
            $row[] = $datarow->companyname;
            $row[] = $datarow->email;
            $row[] = $actions;
            $row[] = $checkbox; 
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->company->count_all(),
            "recordsFiltered" => $this->company->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function add_company()
    {

        $this->viewData['title'] = "Add Additional Rights";
        $this->viewData['module'] = "company/Add_company";

        $this->admin_headerlib->add_javascript("add_company", "pages/add_company.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function edit_company($id)
    {

        $this->viewData['title'] = "Edit Company";
        $this->viewData['module'] = "company/Add_company";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['companydata'] = $this->company->getcompanyDataByID($id);
        $this->admin_headerlib->add_javascript("add_company", "pages/add_company.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);

    }
    public function company_add()
    {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $companyname = $PostData['companyname'];
        $email = $PostData['email'];
      
        $this->form_validation->set_rules('companyname', 'Company Name', 'required');

        $json = array();
        if ($this->form_validation->run() == false) {
            $validationError = implode('<br>', $this->form_validation->error_array());
            $json = array('error' => 3, 'message' => $validationError);
        } else {
            $insertdata = array("companyname" => $companyname,
                "email" => $email,
                "createddate" => $createddate,
                "addedby" => $addedby,
                "modifieddate" => $createddate,
                "modifiedby" => $addedby);

            $insertdata = array_map('trim', $insertdata);
            $Add = $this->company->Add($insertdata);
            if ($Add) {
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(1, 'Company', 'Company.');
                }
                $json = array('error' => 1); //Rights successfully added.
            } else {
                $json = array('error' => 0); //Rights not added.
            }

        }
        echo json_encode($json);
    }
    public function update_company()
    {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $id = $PostData['id'];
        $companyname = $PostData['companyname'];
        $email = $PostData['email'];

        $this->form_validation->set_rules('companyname', 'Company Type', 'required');

        $json = array();
        if ($this->form_validation->run() == false) {
            $validationError = implode('<br>', $this->form_validation->error_array());
            $json = array('error' => 3, 'message' => $validationError);
        } else {

            $updatedata = array("companyname" => $companyname,
                "email" => $email,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby);
            $updatedata = array_map('trim', $updatedata);

            $this->company->_where = array("id" => $id);
            $Edit = $this->company->Edit($updatedata);
            if ($Edit) {
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Company Type', 'Edit ' . $companyname . ' company type.');
                }
                $json = array('error' => 1); //Rights successfully updated.
            } else {
                $json = array('error' => 0); //Rights not updated.
            }

        }
        echo json_encode($json);
    }
    public function delete_mul_company()
    {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {
            if ($this->viewData['submenuvisibility']['managelog'] == 1) {

                $this->company->_where = array("id" => $row);
                $data = $this->company->getRecordsById();

                $this->general_model->addActionLog(3, 'Additional Rights', 'Delete ' . $data['name'] . ' additional rights.');
            }
            $this->company->Delete(array("id" => $row));
        }
    }
}
