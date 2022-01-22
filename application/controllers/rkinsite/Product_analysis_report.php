<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_analysis_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Product_analysis_report');
        $this->load->model('Product_analysis_report_model', 'Product_analysis_report');
    }
    public function index() {
        $this->viewData['title'] = "Product Analysis Report";
        $this->viewData['module'] = "report/Product_analysis_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("product-analysis-report", "pages/product_analysis_report.js")
       ; 
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

}