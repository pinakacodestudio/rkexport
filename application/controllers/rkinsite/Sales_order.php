<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_Order extends Admin_Controller
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

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Sales_order", "pages/sales_order.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
 
    public function listing()
    {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];

        $list = $this->Sales_order->get_datatables();

        $data = array();
        $counter = $_POST['start'];
        // echo '<pre>';
        // print_r($list);exit;
        foreach ($list as $datarow) {

            $row = array();
            $view = "";
            $channellabel = "";
            $Action = $checkbox = '';
            // $status = "";
            // if ($datarow->status == 0) {
            //     $status = '<button class="btn btn-warning ' . STATUS_DROPDOWN_BTN . ' btn-raised">Pending</button>';
            // } else if ($datarow->status == 1) {
            //     $status = '<button class="btn btn-success ' . STATUS_DROPDOWN_BTN . ' btn-raised">Complete</button>';
            // } else if ($datarow->status == 2) {
            //     $status = '<button class="btn btn-danger ' . STATUS_DROPDOWN_BTN . ' btn-raised">Cancel</button>';
            // } else if ($datarow->status == 3) {
            //     $status = '<button class="btn btn-info ' . STATUS_DROPDOWN_BTN . ' btn-raised">Partially</button>';
            // }

            // if ($datarow->remarks != "") {
            //     $remarks = '<span id="orderremarks' . $datarow->id . '" style="display:none;">' . $datarow->remarks . '</span><a href="javascript:void(0)" onclick="viewreason(' . $datarow->id . ')">View</a>';
            // } else {
            //     $remarks = "";
            // }

            // if ($datarow->salespersonid != 0) {
            //     $commissionamounttext = numberFormat($datarow->commissionamount, 2, '.', ',');
            // }
            // $commissionamount = number_format($datarow->commissionamount, 2, '.', '');
            // $commissiondata = $this->Sales_order->getSalesPersonProductCommission($datarow->id);
            // if (!empty($commissiondata)) {
            //     $str = "";
            //     foreach ($commissiondata as $comm) {
            //         $commissionamount += number_format($comm['commissionamount'], 2, '.', '');
            //         $str .= '<p>' . ucwords($comm['salesperson']) . " - " . CURRENCY_CODE . " " . numberFormat($comm['commissionamount'], 2, '.', ',') . "</p>";
            //     }
            //     $commissionamounttext = '<a title="Commission" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="' . $str . '">' . numberFormat($commissionamount, 2, '.', ',') . '</a>';
            // }

            // $row[] = '<a href="' . ADMIN_URL . 'order/view-order/' . $datarow->id . '" title="View Order" target="_blank">' . $datarow->orderid . '</a>';
            // $row[] = '<a href="' . ADMIN_URL . 'member/member-detail/' . $datarow->buyerid . '" title="' . ucwords($datarow->buyername) . '" target="_blank">' . $channellabel . " " . ucwords($datarow->buyername) . ' (' . $datarow->buyercode . ')</a>';
            // $row[] = $this->general_model->displaydate($datarow->orderdate);
            // $row[] = numberFormat($datarow->netamount, 2, '.', ',');
            // $row[] = $commissionamounttext;
            // $row[] = ($datarow->salespersonid != 0) ? ucwords($datarow->salespersonname) : "-";
            // $row[] = $status;
            // $row[] = $remarks;

            $Action = $checkbox = '';

            if (in_array($rollid, $edit)) {
                $Action .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'Sales-order/Sales-order-edit/' . $datarow->id . '" title=' . edit_title . '>' . edit_text . '</a>';
            }
            if (in_array($rollid, $delete)) {
                $Action .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"' . ADMIN_URL . 'Sales-order/check-party-use","Party","' . ADMIN_URL . 'Sales-order/delete-mul-party") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input value="' . $datarow->id . '" type="checkbox" class="checkradios" name="check' . $datarow->id . '" id="check' . $datarow->id . '" onchange="singlecheck(this.id)"><label for="check' . $datarow->id . '"></label></div>';
            }
            // $Action .= '<a class="' . view_class . '" href="' . ADMIN_URL . 'party/view-party/' . $datarow->id . '" title=' . view_title . ' target="_blank">' . view_text . '</a>';

            $row[] = ++$counter;
            $row[] = $datarow->companyname;
            $row[] = $datarow->inquiryno;
            $row[] = $datarow->clientpono;
            $row[] = $datarow->dicountamount;
            $row[] = $datarow->username;
            $row[] = $Action;
            $row[] = $checkbox;

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

        $this->load->model('Party_model', 'Party');
        $this->viewData['Partydorpdowndata'] = $this->Sales_order->getpartydata();

        $this->load->model('Category_model', 'category');
        $this->viewData['categorydorpdowndata'] = $this->category->getRecordByID();

        $this->load->model('Product_model', 'product');
        $this->viewData['productdorpdowndata'] = $this->product->getRecordByID();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("sales_order", "pages/add_sales_order.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    
    public function Sales_order_add()
    {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $party = $PostData['party'];
        $pono = $PostData['pono'];
        $inquiryno = $PostData['inquiryno'];
        $podate = $this->general_model->convertdate($PostData['podate']);
        $orderno = $PostData['orderno'];
        // $inquiryno = $PostData['inquiryno'];
        // $voucherdate = $this->general_model->convertdate($PostData['voucherdate']);

        // $this->load->model('Sales_order_model', 'salesorder');

        // $this->Sales_invoice->_where = array('inquiryno' => $inquiryno);
        // $Count = $this->Sales_invoice->CountRecords();

        $json = array();
        // if($Count==0){

        $InsertData = array('partyid' => $party,
            'clientpono' => $pono,
            'inquiryno' => $inquiryno,
            'podate' => $podate,
            'orderno' => $orderno,
            'createddate' => $createddate,
            'addedby' => $addedby,
            'modifieddate' => $createddate,
            'modifiedby' => $addedby,
        );

        $Sales_orderid = $this->Sales_order->Add($InsertData);

        if ($Sales_orderid) {
            $this->general_model->updateTransactionPrefixLastNoByType(4);
            $cetegoryArr = $PostData['cetegory'];
            if (!empty($cetegoryArr)) {
                $insertData = $inserttransactionproductstock = array();
                foreach ($cetegoryArr as $i => $cetegory) {

                    $cetegory = $PostData['cetegory'][$i];
                    $product = $PostData['product'][$i];
                    $price = $PostData['price'][$i];
                    $actualprice = $PostData['actualprice'][$i];
                    $qty = $PostData['qty'][$i];

                    if (!empty($cetegory) && !empty($product) && !empty($price)) {
                        $insertData2 = array(
                            "orderid" => $Sales_orderid,
                            "categoryid" => $cetegory,
                            "productid" => $product,
                            "price" => $price,
                            "actualprice" => $actualprice,
                            "qty" => $qty,
                            'createddate' => $createddate,
                            'addedby' => $addedby,
                            'modifieddate' => $createddate,
                            'modifiedby' => $addedby,
                        );
                        $this->Sales_order->_table = tbl_orderproduct;
                        $SalesorderID = $this->Sales_order->Add($insertData2);

                    }
                }

                if (!empty($inserttransactionproductstock)) {
                    $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                    $this->Stock_general_voucher->Add_batch($inserttransactionproductstock);
                }
            }

            echo 1;
        } else {
            echo json_encode(array("error" => 0));
        }

        foreach ($_FILES as $key => $value) {
            $id = preg_replace('/[^0-9]/', '', $key);
            $fileremarks = $PostData['fileremarks' . $id];
            if (isset($_FILES['file' . $id]['name']) && $_FILES['file' . $id]['name'] != '') {
                $file = uploadFile('file' . $id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, '', '', 0);
                if ($file === 0) {
                    echo 3; //INVALID image FILE TYPE
                    exit;
                }
                $insertData2 = array(
                    "orderid" => $Sales_orderid,
                    "documentfile" => $file,
                    "documentname" => $fileremarks,
                    'createddate' => $createddate,
                    'addedby' => $addedby,
                    'modifieddate' => $createddate,
                    'modifiedby' => $addedby,
                );
                $this->Sales_order->_table = tbl_orderdocument;
                $SalesorderID = $this->Sales_order->Add($insertData2);
            }
        }

    }
    
    public function Sales_order_edit($id)
    {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Purchase Quotation";
        // $this->viewData['module'] = "purchase_quotation/Add_purchase_quotation";
        $this->viewData['module'] = "sales_order/Add_sales_order";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;
        $this->viewData['salesorderdata'] = $this->Sales_order->getSalesorderdataByID($id);
        if (empty($this->viewData['salesorderdata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }
       
        $this->viewData['productdata'] = $this->Sales_order->getProductdataByID($id);
        if (empty($this->viewData['productdata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }
        $this->viewData['documentdata'] = $this->Sales_order->getdocumentdataByID($id);
        if (empty($this->viewData['documentdata'])) {
            redirect(ADMINFOLDER . "pagenotfound");
        }
        // print_r($this->viewData['documentdata']);
        // exit;
        $this->load->model('Party_model', 'Party');
        $this->viewData['Partydorpdowndata'] = $this->Sales_order->getpartydata();

        $this->load->model('Category_model', 'category');
        $this->viewData['categorydorpdowndata'] = $this->category->getRecordByID();

        $this->load->model('Product_model', 'product');
        $this->viewData['productdorpdowndata'] = $this->product->getRecordByID();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("sales_order", "pages/add_sales_order.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    
    public function update_sales_order()
    {
        
        // $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
       
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $sid = $PostData['sid'];
        $party = $PostData['party'];
        $pono = $PostData['pono'];
        $inquiryno = $PostData['inquiryno'];
        $podate = $this->general_model->convertdate($PostData['podate']);
        $orderno = $PostData['orderno'];
        $json = array();
        $InsertData = array('partyid' => $party,
            'clientpono' => $pono,
            'inquiryno' => $inquiryno,
            'podate' => $podate,
            'orderno' => $orderno,
            'modifieddate' => $createddate,
            'modifiedby' => $addedby,
        );

        
        $this->Sales_order->_where = array("id" => $sid);
        $Sales_orderid = $this->Sales_order->Edit($InsertData);

        // $Sales_orderid = $this->Sales_order->Add($InsertData);
        if ($Sales_orderid) {
            echo 101;
            $this->general_model->updateTransactionPrefixLastNoByType(4);
            $cetegoryArr = $PostData['cetegory'];
            if (!empty($cetegoryArr)) {
                echo 102;
                $insertData = $inserttransactionproductstock = array();
                foreach ($cetegoryArr as $i => $cetegory) {

                    $cetegory = $PostData['cetegory'][$i];
                    $product = $PostData['product'][$i];
                    $price = $PostData['price'][$i];
                    $actualprice = $PostData['actualprice'][$i];
                    $qty = $PostData['qty'][$i];
                    if (!empty($cetegory) && !empty($product) && !empty($price)) {
                        $insertData2 = array(
                            "orderid" => $Sales_orderid,
                            "categoryid" => $cetegory,
                            "productid" => $product,
                            "price" => $price,
                            "actualprice" => $actualprice,
                            "qty" => $qty,
                            'createddate' => $createddate,
                            'addedby' => $addedby,
                            'modifieddate' => $createddate,
                            'modifiedby' => $addedby,
                        );
                        $this->Sales_order->_table = tbl_orderproduct;
                        $this->Sales_order->_where = array("orderid" => $sid);
                        $SalesorderID = $this->Sales_order->Edit($insertData2);
                        print_r($SalesorderID);exit;
                    }
                }

                if (!empty($inserttransactionproductstock)) {
                    $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                    $this->Stock_general_voucher->Add_batch($inserttransactionproductstock);
                }
            }

            echo 1;
        } else {
            echo 103;
            echo json_encode(array("error" => 0));
        }

        foreach ($_FILES as $key => $value) {
            $id = preg_replace('/[^0-9]/', '', $key);
            $fileremarks = $PostData['fileremarks' . $id];
            if (isset($_FILES['file' . $id]['name']) && $_FILES['file' . $id]['name'] != '') {
                $file = uploadFile('file' . $id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, '', '', 0);
                if ($file === 0) {
                    echo 3; //INVALID image FILE TYPE
                    exit;
                }
                $insertData2 = array(
                    "orderid" => $Sales_orderid,
                    "documentfile" => $file,
                    "documentname" => $fileremarks,
                    'createddate' => $createddate,
                    'addedby' => $addedby,
                    'modifieddate' => $createddate,
                    'modifiedby' => $addedby,
                );
                $this->Sales_order->_table = tbl_orderdocument;
                $SalesorderID = $this->Sales_order->Add($insertData2);
            }
        }

    }
    
    public function regeneratequotation()
    {
        $PostData = $this->input->post();

        $quotationid = $PostData['quotationid'];
        echo $this->Purchase_quotation->generatequotation($quotationid);
    }
    
    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }
    
    public function view_purchase_quotation($quotationid)
    {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Purchase Quotation";
        $this->viewData['module'] = "purchase_quotation/View_purchase_quotation";

        $this->load->model("Purchase_order_model", "Purchase_order");
        $this->viewData['transactiondata'] = $this->Purchase_quotation->getPurchaseQuotationDetails($quotationid);
        $this->viewData['transactionattachment'] = $this->Purchase_order->getTransactionAttachmentDataByTransactionId($quotationid, 1);
        $this->viewData['printtype'] = 'quotation';
        $this->viewData['heading'] = 'Purchase Quotation';

        $this->load->model('Invoice_setting_model', 'Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $this->Purchase_quotation->_table = tbl_installment;
        $this->Purchase_quotation->_where = array("quotationid" => $quotationid);
        $this->Purchase_quotation->_order = ("date ASC");
        $this->viewData['installment'] = $this->Purchase_quotation->getRecordByID();

        $this->viewData['quotationstatushistory'] = $this->Purchase_quotation->getPurchaseQuotationStatusHistory($quotationid);

        $this->load->model("Channel_model", "Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Quotation', 'View ' . $this->viewData['transactiondata']['transactiondetail']['quotationid'] . ' purchase quotation details.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("jquery.number", "jquery.number.js");
        $this->admin_headerlib->add_javascript("view_purchase_quotation", "pages/view_purchase_quotation.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    
    public function update_status()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $quotationId = $PostData['quotationId'];
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();

        $insertstatusdata = array(
            "quotationid" => $quotationId,
            "status" => $status,
            "type" => 0,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby);

        $insertstatusdata = array_map('trim', $insertstatusdata);
        $this->Purchase_quotation->_table = tbl_quotationstatuschange;
        $this->Purchase_quotation->Add($insertstatusdata);

        $updateData = array(
            'status' => $status,
            'modifieddate' => $modifieddate,
            'modifiedby' => $modifiedby,
        );
        if ($status == 2) {
            $updateData['resonforrejection'] = $PostData['resonforrejection'];
        }
        $this->Purchase_quotation->_table = tbl_quotation;
        $this->Purchase_quotation->_where = array("id" => $quotationId);
        $isupdate = $this->Purchase_quotation->Edit($updateData);

        if ($isupdate) {
            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->Purchase_quotation->_fields = "quotationid";
                $this->Purchase_quotation->_where = array("id" => $quotationId);
                $quotationdetail = $this->Purchase_quotation->getRecordsByID();

                $this->general_model->addActionLog(2, 'Quotation', 'Change status ' . $quotationdetail['quotationid'] . ' on purchase quotation.');
            }
            echo 1;
        } else {
            echo 0;
        }
    }
    public function update_installment_status()
    {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $status = $PostData['status'];
        $installmentid = $PostData['installmentid'];

        $updateData = array(
            'status' => $status,
            'modifieddate' => $modifieddate,
            'modifiedby' => $modifiedby,
        );
        if ($PostData['status'] == 1) {
            $updateData['paymentdate'] = $this->general_model->getCurrentDate();
        } else {
            $updateData['paymentdate'] = "";
        }
        $this->Purchase_quotation->_table = tbl_installment;
        $this->Purchase_quotation->_where = array("id" => $installmentid);
        $IsUpdate = $this->Purchase_quotation->Edit($updateData);
        if ($IsUpdate != 0) {
            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->Purchase_quotation->_fields = "(select quotationid from " . tbl_quotation . " where id=" . tbl_installment . ".quotationid) as quotationnumber";
                $this->Purchase_quotation->_where = array("id" => $installmentid);
                $quotationdetail = $this->Purchase_quotation->getRecordsByID();

                $this->general_model->addActionLog(2, 'Quotation', 'Change installment status ' . $quotationdetail['quotationnumber'] . ' on purchase quotation.');
            }
            echo 1;
        } else {
            echo 0;
        }
    }

    public function printPurchaseQuotationInvoice()
    {
        $PostData = $this->input->post();
        $quotationid = $PostData['id'];
        $this->load->model("Purchase_order_model", "Purchase_order");
        $PostData['transactiondata'] = $this->Purchase_quotation->getPurchaseQuotationDetails($quotationid);

        $this->load->model('Invoice_setting_model', 'Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = "quotation";
        $PostData['heading'] = "Purchase Quotation";
        $PostData['hideonprint'] = '1';

        $html['content'] = $this->load->view(ADMINFOLDER . "purchase_quotation/Printpurchasequotationformat.php", $PostData, true);

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Quotation', 'Print ' . $PostData['transactiondata']['transactiondetail']['quotationid'] . ' purchase quotation details.');
        }
        echo json_encode($html);
    }
    
    public function Productpricesdorpdowndata($pid)
    {
        $this->load->model('Productprices_model', 'Productprices');
        $this->viewData['Productpricesdorpdowndata'] = $this->Productprices->getProductpriceByProductID($pid);
        if (isset($this->viewData['Productpricesdorpdowndata']) > 0) {
            echo json_encode($this->viewData['Productpricesdorpdowndata']);
        } else {
            echo json_encode(0);
        }
    }
    
    public function check_party_use()
    {
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach ($ids as $row) {
            // $count++;
            /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE
        id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
        //OR id IN (SELECT vehicleid FROM ".tbl_vehicleroute." WHERE vehicleid = $row)
        if($query->num_rows() > 0){
        } */
        }
        echo $count;
    }
    
    public function delete_mul_party()
    {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        $this->load->model('Document_model', 'Document');
        foreach ($ids as $row) {

            $this->Sales_order->_where = array("id" => $row);
            $data = $this->Sales_order->getRecordsById();

            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(3, 'Sales_order', 'Delete ' . $data['firstname'] . ' ' . $data['lastname'] . ' Sales_order.');
            }

            $this->Document->_where = array("referencetype" => 1, "referenceid" => $row);
            $documents = $this->Document->getRecordsById();

            if (!empty($documents)) {
                foreach ($documents as $document) {
                    if ($document['documentfile'] != "") {
                        unlinkfile("DOCUMENT", $document['documentfile'], DOCUMENT_PATH);
                    }
                }
            }
            $this->Document->Delete(array("referencetype" => 1, "referenceid" => $row));
            $this->Sales_order->Delete(array("id" => $row));
        }
    }

}
