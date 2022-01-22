<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class Product_wise_purchase_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Purchase_report_model', 'Purchase_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Product_wise_purchase_report');
    }
    public function index() {
        $this->viewData['title'] = "Product Wise Purchase Report";
        $this->viewData['module'] = "report/Product_wise_purchase_report";
         $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("product-wise-purchase-report", "pages/product_wise_purchase_report.js")
       ;
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
}