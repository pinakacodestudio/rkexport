<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Currency_rate extends Admin_Controller
{

    public $viewData = array();
    public function __construct()
    {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Currency_rate');
        $this->load->model('Currency_rate_model', 'Currency_rate');
    }
    public function index()
    {

        $this->viewData['title'] = "Currency Rate";
        $this->viewData['module'] = "currency_rate/Currency_rate";

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Currency Rate', 'View currency rate.');
        }
        $this->admin_headerlib->add_javascript_plugins("bootstrap-p", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("currency_rate", "pages/currency_rate.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing()
    {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Currency_rate->get_datatables();

        $data = array();
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
            //Edit Button
            if (in_array($rollid, $edit)) {
                $actions .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'currency-rate/edit_currency_rate/' . $datarow->id . '/' . '" title="' . edit_title . '">' . edit_text . '</a>';
            }
            //Delete and Enable/Disable Button
            if (in_array($rollid, $delete)) {
                $actions .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"","currency-rate","' . ADMIN_URL . 'currency-rate/delete-mul-currency-rate") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck' . $datarow->id . '" onchange="singlecheck(this.id)" type="checkbox" value="' . $datarow->id . '" name="deletecheck' . $datarow->id . '" class="checkradios">
                            <label for="deletecheck' . $datarow->id . '"></label></div>';
            }

            $row[] = ++$counter;
            $row[] = $datarow->currency;
            $row[] = $datarow->value;
            $row[] = $this->general_model->displaydate($datarow->date);
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Currency_rate->count_all(),
            "recordsFiltered" => $this->Currency_rate->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }
    public function add_Currency_rate()
    {

        $this->viewData['title'] = "Add Currency Rate";
        $this->viewData['module'] = "currency_rate/Add_currency_rate";

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("add_currency_rate", "pages/add_currency_rate.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function edit_Currency_rate($id)
    {

        $this->viewData['title'] = "Edit Currency Rate";
        $this->viewData['module'] = "currency_rate/Add_currency_rate";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['currencydata'] = $this->Currency_rate->getcurrencyDataByID($id);
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("add_currency_rate", "pages/add_currency_rate.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);

    }
    public function currencyrate_add()
    {

        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $currency = $PostData['currency'];
        $value = $PostData['value'];

        $this->form_validation->set_rules('currency', 'Currency', 'required');
        $this->form_validation->set_rules('value', 'value', 'required');

        $json = array();
        if ($this->form_validation->run() == false) {
            $validationError = implode('<br>', $this->form_validation->error_array());
            $json = array('error' => 3, 'message' => $validationError);
        } else {

            $this->Currency_rate->_where = ("currency='".$currency."'");
            $Count = $this->Currency_rate->CountRecords();
          
            if($Count==0){
                $insertdata = array("currency" => $currency,
                    "value" => $value,
                    "date" => $this->general_model->convertdate($PostData['date']),
                    "createddate" => $createddate,
                    "addedby" => $addedby,
                    "modifieddate" => $createddate,
                    "modifiedby" => $addedby);
    
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Currency_rate->Add($insertdata);
                if ($Add) {
                    if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                        $this->general_model->addActionLog(1, 'Currency Rate', 'Currency Rate Add.');
                    }
                    $json = array('error' => 1); //Rights successfully added.
                } else {
                    $json = array('error' => 0); //Rights not added.
                }
            }else{
                $json = array('error' => 2);
            }

        }
        echo json_encode($json);
    }
    public function update_currency_rate()
    {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $rightsid = $PostData['rightsid'];
        $currency = $PostData['currency'];
        $value = $PostData['value'];

        $this->form_validation->set_rules('currency', 'Currency', 'required');
        $this->form_validation->set_rules('value', 'Value', 'required');

        $json = array();
        if ($this->form_validation->run() == false) {
            $validationError = implode('<br>', $this->form_validation->error_array());
            $json = array('error' => 3, 'message' => $validationError);
        } else {

            $updatedata = array("currency" => $currency,
                "value" => $value,
                "date" => $this->general_model->convertdate($PostData['date']),
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby);

            $updatedata = array_map('trim', $updatedata);

            $this->Currency_rate->_where = array("id" => $rightsid);
            $Edit = $this->Currency_rate->Edit($updatedata);
            if ($Edit) {
                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Currency Rate', 'Currency Rate Edit ' . $currency . ' currency rate.');
                }
                $json = array('error' => 1); //Rights successfully updated.
            } else {
                $json = array('error' => 0); //Rights not updated.
            }

        }
        echo json_encode($json);
    }
    public function delete_mul_currency_rate()
    {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {
            if ($this->viewData['submenuvisibility']['managelog'] == 1) {

                $this->Currency_rate->_where = array("id" => $row);
                $data = $this->Currency_rate->getRecordsById();

                $this->general_model->addActionLog(3, 'Currency Rate', 'Currency Rate Delete ' . $data['name'] . ' Currency Rate.');
            }
            $this->Currency_rate->Delete(array("id" => $row));
        }
    }
}
