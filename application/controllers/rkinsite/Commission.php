<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Commission extends Admin_Controller
{

    public $viewData = array();
    public function __construct()
    {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'commission');
        $this->load->model('Commission_model', 'commission');
    }
    public function index()
    {

        $this->viewData['title'] = "Currency Rate";
        $this->viewData['module'] = "commission/Commission";

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Currency Rate', 'View currency rate.');
        }

        $this->admin_headerlib->add_javascript("commission", "pages/commission.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing()
    {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->commission->get_datatables();

        $data = array();
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
            //Edit Button
            if (in_array($rollid, $edit)) {
                $actions .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'Commission/edit_commission/' . $datarow->id . '/' . '" title="' . edit_title . '">' . edit_text . '</a>';
            }
            //Delete and Enable/Disable Button
            if (in_array($rollid, $delete)) {
                $actions .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"","commission","' . ADMIN_URL . 'Commission/delete-mul-commission") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck' . $datarow->id . '" onchange="singlecheck(this.id)" type="checkbox" value="' . $datarow->id . '" name="deletecheck' . $datarow->id . '" class="checkradios">
                            <label for="deletecheck' . $datarow->id . '"></label></div>';
            }

            $row[] = ++$counter;
            $row[] = $datarow->commission_type;
            $row[] = $datarow->date;
            $row[] = $actions;
            $row[] = $checkbox; 
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->commission->count_all(),
            "recordsFiltered" => $this->commission->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function add_commission()
    {

        $this->viewData['title'] = "Add Additional Rights";
        $this->viewData['module'] = "commission/Add_commission";

        $this->admin_headerlib->add_javascript("add_commission", "pages/add_commission.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function edit_commission($id)
    {

        $this->viewData['title'] = "Edit Currency Rate";
        $this->viewData['module'] = "commission/Add_commission";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['commissiondata'] = $this->commission->getcommissionDataByID($id);
        $this->admin_headerlib->add_javascript("add_commission", "pages/add_commission.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);

    }
    public function currencyrate_add()
    {

        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $commission_type = $PostData['commission_type'];
      
        $this->form_validation->set_rules('commission_type', 'Commission Type', 'required');

        $json = array();
        if ($this->form_validation->run() == false) {
            $validationError = implode('<br>', $this->form_validation->error_array());
            $json = array('error' => 3, 'message' => $validationError);
        } else {
            $insertdata = array("commission_type" => $commission_type,
                "date" => $this->general_model->convertdate($PostData['date']),
                "createddate" => $createddate,
                "addedby" => $addedby,
                "modifieddate" => $createddate,
                "modifiedby" => $addedby);

            $insertdata = array_map('trim', $insertdata);
            $Add = $this->commission->Add($insertdata);
            if ($Add) {
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(1, 'Currency Rate', 'Currency Rate.');
                }
                $json = array('error' => 1); //Rights successfully added.
            } else {
                $json = array('error' => 0); //Rights not added.
            }

        }
        echo json_encode($json);
    }
    public function update_commission()
    {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $id = $PostData['id'];
        $commission_type = $PostData['commission_type'];

        $this->form_validation->set_rules('commission_type', 'Commission Type', 'required');

        $json = array();
        if ($this->form_validation->run() == false) {
            $validationError = implode('<br>', $this->form_validation->error_array());
            $json = array('error' => 3, 'message' => $validationError);
        } else {

            $updatedata = array("commission_type" => $commission_type,
                "date" => $this->general_model->convertdate($PostData['date']),
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby);
            $updatedata = array_map('trim', $updatedata);

            $this->commission->_where = array("id" => $id);
            $Edit = $this->commission->Edit($updatedata);
            if ($Edit) {
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Commission Type', 'Edit ' . $commission_type . ' commission type.');
                }
                $json = array('error' => 1); //Rights successfully updated.
            } else {
                $json = array('error' => 0); //Rights not updated.
            }

        }
        echo json_encode($json);
    }
    public function delete_mul_commission()
    {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {
            if ($this->viewData['submenuvisibility']['managelog'] == 1) {

                $this->commission->_where = array("id" => $row);
                $data = $this->commission->getRecordsById();

                $this->general_model->addActionLog(3, 'Additional Rights', 'Delete ' . $data['name'] . ' additional rights.');
            }
            $this->commission->Delete(array("id" => $row));
        }
    }
}
