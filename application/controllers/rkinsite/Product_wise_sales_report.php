<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Product_wise_sales_report extends Admin_Controller
{

    public $viewData = array();
    function __construct()
    {
        parent::__construct();
        $this->load->model('Sales_report_model', 'Sales_report');

        $this->viewData = $this->getAdminSettings('submenu', 'Product_wise_sales_report');
    } 
    public function index() 
    {
        $this->viewData['title'] = "Product Wise Sales Report";
        $this->viewData['module'] = "report/Product_wise_sales_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("product-wise-sales-report", "pages/product_wise_sales_report.js")
       ;
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

}
