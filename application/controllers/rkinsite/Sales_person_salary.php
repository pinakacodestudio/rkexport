<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_person_salary extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_person_salary');
        $this->load->model('Sales_person_salary_model', 'Sales_person_salary');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Sales Person Salary";
        $this->viewData['module'] = "report/Sales_person_salary";     
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Sales Person Salary','View sales person salary.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("sales_person_salary", "pages/sales_person_salary.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    } 
    
    public function listing() {
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Sales_person_salary->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $channellabel = $checkbox = "";

            $salary = (float)$datarow->distance * AMOUNT_PER_KM;


            $row[] = ++$counter;
            $row[] = $datarow->salespersonname;
            $row[] = $datarow->date;
            $row[] = numberFormat(AMOUNT_PER_KM,2);
            $row[] = numberFormat($datarow->distance,2);
            $row[] = numberFormat($salary,2);
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Sales_person_salary->count_all(),
                        "recordsFiltered" => $this->Sales_person_salary->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
}

?>