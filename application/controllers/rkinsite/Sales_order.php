<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_order extends Admin_Controller
{

    public $viewData = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sales_order_model', 'Sales_order');
        $this->load->model('User_model', 'User');
        $this->load->model('Side_navigation_model', 'Side_navigation');
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_order');
    }
    public function index()
    {
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_order');
        $this->viewData['title'] = "Sales Order";
        $this->viewData['module'] = "sales_order/sales_order";

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Sales Order', 'View sales order.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
        $this->admin_headerlib->add_javascript("Sales_order", "pages/sales_order.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing()
    {
       
        $list = $this->Sales_order->get_datatables();

        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $view = "";
            $channellabel = "";
            
            $status = "";
            if ($datarow->status == 0) {
                $status = '<button class="btn btn-warning ' . STATUS_DROPDOWN_BTN . ' btn-raised">Pending</button>';
            } else if ($datarow->status == 1) {
                $status = '<button class="btn btn-success ' . STATUS_DROPDOWN_BTN . ' btn-raised">Complete</button>';
            } else if ($datarow->status == 2) {
                $status = '<button class="btn btn-danger ' . STATUS_DROPDOWN_BTN . ' btn-raised">Cancel</button>';
            } else if ($datarow->status == 3) {
                $status = '<button class="btn btn-info ' . STATUS_DROPDOWN_BTN . ' btn-raised">Partially</button>';
            }

            if ($datarow->remarks != "") {
                $remarks = '<span id="orderremarks' . $datarow->id . '" style="display:none;">' . $datarow->remarks . '</span><a href="javascript:void(0)" onclick="viewreason(' . $datarow->id . ')">View</a>';
            } else {
                $remarks = "";
            }

            if ($datarow->salespersonid != 0) {
                $commissionamounttext = numberFormat($datarow->commissionamount, 2, '.', ',');
            }
            $commissionamount = number_format($datarow->commissionamount, 2, '.', '');
            $commissiondata = $this->Sales_order->getSalesPersonProductCommission($datarow->id);
            if (!empty($commissiondata)) {
                $str = "";
                foreach ($commissiondata as $comm) {
                    $commissionamount += number_format($comm['commissionamount'], 2, '.', '');
                    $str .= '<p>' . ucwords($comm['salesperson']) . " - " . CURRENCY_CODE . " " . numberFormat($comm['commissionamount'], 2, '.', ',') . "</p>";
                }
                $commissionamounttext = '<a title="Commission" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="' . $str . '">' . numberFormat($commissionamount, 2, '.', ',') . '</a>';
            }

            $row[] = '<a href="' . ADMIN_URL . 'order/view-order/' . $datarow->id . '" title="View Order" target="_blank">' . $datarow->orderid . '</a>';
            $row[] = '<a href="' . ADMIN_URL . 'member/member-detail/' . $datarow->buyerid . '" title="' . ucwords($datarow->buyername) . '" target="_blank">' . $channellabel . " " . ucwords($datarow->buyername) . ' (' . $datarow->buyercode . ')</a>';
            $row[] = $this->general_model->displaydate($datarow->orderdate);
            $row[] = numberFormat($datarow->netamount, 2, '.', ',');
            // $row[] = $commissionamounttext;
            $row[] = ($datarow->salespersonid != 0) ? ucwords($datarow->salespersonname) : "-";
            $row[] = $status;
            $row[] = $remarks;
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Sales_order->count_all(),
            "recordsFiltered" => $this->Sales_order->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

  


    public function add_Sales_order()
    {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_order');
        $this->viewData['title'] = "Add Sales Order";
        $this->viewData['module'] = "sales_order/Add_sales_order";
        $this->viewData['VIEW_STATUS'] = "0";

        // $this->viewData['productcount'] = $this->Product->CountRecords();
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");	
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("product", "pages/add_sales_order.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }



  

    

}