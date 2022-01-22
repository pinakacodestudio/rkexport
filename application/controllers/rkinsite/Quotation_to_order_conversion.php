<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quotation_to_order_conversion extends Admin_Controller 
{
    public $viewData = array();
    function __construct(){ 
        parent::__construct();

        $this->viewData = $this->getAdminSettings('submenu', 'Quotation_to_order_conversion');
        $this->load->model('Quotation_to_order_conversion_model','Quotation_to_order_conversion');
        
    }
    public function index(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Quotation to order conversion";
        $this->viewData['module'] = "quotation_to_order_conversion/Quotation_to_order_conversion";
          $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Quotation-to-order-conversion", "pages/quotation_to_order_conversion.js");
        
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
}
