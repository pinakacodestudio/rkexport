<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Price_wise_stock_report extends Admin_Controller {

    public $viewData = array(); 
    function __construct(){
        parent::__construct();
        $this->load->model('Price_wise_stock_report_model', 'Price_wise_stock_report');
        $this->viewData = $this->getAdminSettings('submenu', 'Price_wise_stock_report');
    }
    public function index() {
        $this->viewData['title'] = "Price Wise Stock Report";
        $this->viewData['module'] = "report/Price_wise_stock_report";
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("price-wise-stock-report", "pages/price_wise_stock_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
}