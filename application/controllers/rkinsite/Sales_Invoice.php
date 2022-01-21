<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sales_Invoice extends Admin_Controller
{

    public $viewData = array();

    public function __construct()
    {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_Invoice');
        $this->load->model('Sales_invoice_model', 'Sales_Invoice');
        $this->load->model('User_model', 'User');
    }
    public function index()
    {
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_Invoice');
        $this->viewData['title'] = "Sales Person Order";
        $this->viewData['module'] = "sales_invoice/Sales_invoice";

        $where = array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto=' . $this->session->userdata(base_url() . 'ADMINID') . " or id=" . $this->session->userdata(base_url() . 'ADMINID') . " or reportingto=(select reportingto from " . tbl_user . " where id=" . $this->session->userdata(base_url() . 'ADMINID') . "))" => null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        // $this->load->model('Channel_model', 'Channel');
        // $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Sales Person Order', 'View sales person order.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker", "bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Sales_order", "pages/sales_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing()
    {

        $list = $this->Sales_Invoice->get_datatables();
    

        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
            $view = "";
            $channellabel = "";
            if ($datarow->buyerchannelid != 0) {
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if (!empty($channeldata) && isset($channeldata[$key])) {
                    $channellabel .= '<span class="label" style="background:' . $channeldata[$key]['color'] . '">' . substr($channeldata[$key]['name'], 0, 1) . '</span> ';
                }
            }
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
            $commissiondata = $this->Sales_Invoice->getSalesPersonProductCommission($datarow->id);
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
            "recordsTotal" => $this->Sales_Invoice->count_all(),
            "recordsFiltered" => $this->Sales_Invoice->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add_sales_invoice()
    {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_Invoice');
        $this->viewData['title'] = "Add Sales Invoice";
        $this->viewData['module'] = "sales_invoice/Add_sales_invoice";
        $this->viewData['VIEW_STATUS'] = "0";

        // $this->viewData['productcount'] = $this->Product->CountRecords();

        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("product", "pages/add_sales_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function Sales_order_edit($id)
    {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Product";
        $this->viewData['module'] = "product/add_product";
        $this->viewData['action'] = '1';
        $this->viewData['VIEW_STATUS'] = "1";
        // $this->Product->_where=array("id"=>$id);
        $this->viewData['productdata'] = $this->Product->getProductDataByID($id);
        $this->load->model("Product_prices_model", "Product_prices");
        if ($this->viewData['productdata']['barcode'] == '') {
            duplicate:
            $barcode = rand(1000000000, 9999999999);

            $this->Product_prices->_where = "barcode='" . $barcode . "'";
            $Count = $this->Product_prices->CountRecords();
            if ($Count > 0) {
                goto duplicate;
            }
            $this->viewData['barcode'] = $barcode;
        }
        $this->viewData['productid'] = $id;
        $this->viewData['maincategorydata'] = $this->Product->getmaincategory();
        $this->viewData['productfile'] = $this->Product_file->getProductfilesByProductID($id);

        $this->load->model("Hsn_code_model", "Hsn_code");
        $this->viewData['hsncodedata'] = $this->Hsn_code->getActiveHsncode();

        $this->load->model("Brand_model", "Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->load->model("Product_unit_model", "Product_unit");
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->viewData['productprices'] = array();
        $productprices = $this->Product_prices->getProductpriceByProductID($id);
        $this->viewData['productprices'] = array_column($productprices, 'price');
        $this->viewData['productstock'] = array_column($productprices, 'stock');
        $this->viewData['unitid'] = (count($productprices) > 0 ? $productprices[0]['unitid'] : 0);
        $this->viewData['productpricesid'] = (count($productprices) > 0 ? $productprices[0]['id'] : 0);

        if ($this->viewData['productdata']['isuniversal'] == 1 && !empty($productprices)) {
            $this->viewData['productquantitypricesdata'] = $this->Product_prices->getProductQuantityPriceDataByPriceID($productprices[0]['id']);
        }

        $this->viewData['productcount'] = $this->Product->CountRecords();

        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("product", "pages/add_sales_invoice.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function Sales_order_add()
    {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $this->load->model("Product_prices_model", "Product_prices");
        $this->load->model("Related_product_model", "Related_product");

        $productname = isset($PostData['productname']) ? trim($PostData['productname']) : '';
        $importerproductname = isset($PostData['importerproductname']) ? trim($PostData['importerproductname']) : '';
        $supplierproductname = isset($PostData['supplierproductname']) ? trim($PostData['supplierproductname']) : '';
        $installationcost = isset($PostData['installationcost']) ? trim($PostData['installationcost']) : '0';
        $slug = isset($PostData['productslug']) ? trim($PostData['productslug']) : '';
        $shortdescription = isset($PostData['shortdescription']) ? trim($PostData['shortdescription']) : '';

        $description = isset($PostData['description']) ? trim($PostData['description']) : '';
        $stock = isset($PostData['stock']) ? trim($PostData['stock']) : 0;
        $hsncodeid = isset($PostData['hsncodeid']) ? trim($PostData['hsncodeid']) : '';
        $priority = isset($PostData['priority']) ? trim($PostData['priority']) : '';
        $status = $PostData['status'];
        $categoryid = isset($PostData['categoryid']) ? trim($PostData['categoryid']) : '';
        $brandid = $PostData['brandid'];
        $unitid = $PostData['unitid'];
        $sku = $PostData['sku'];
        $weight = isset($PostData['weight']) ? trim($PostData['weight']) : 0;
        $minimumstocklimit = $PostData['minimumstocklimit'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();

        if (isset($PostData['checkuniversal'])) {
            $isuniversal = 1;
            $price = isset($PostData['price']) ? $PostData['price'] : 0;
            $pointspriority = 0;
        } else {
            $isuniversal = 0;
            $price = isset($PostData['prices']) ? $PostData['prices'] : 0;
        }

        if ($isuniversal == 1) {
            $Count = $this->Product->CheckProductSKUAvailable($sku);
            if ($Count > 0) {
                echo 8;
                exit;
            }
        }

        if (!is_dir(PRODUCT_PATH)) {
            @mkdir(PRODUCT_PATH);
        }
        if (!is_dir(CATALOG_PATH)) {
            @mkdir(CATALOG_PATH);
        }
        foreach ($_FILES as $key => $value) {
            $id = preg_replace('/[^0-9]/', '', $key);
            if (isset($_FILES['productfile' . $id]['name']) && $_FILES['productfile' . $id]['name'] != '') {
                $file = uploadFile('productfile' . $id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, '', '', 0);
                if ($file === 0) {
                    echo 3; //INVALID image FILE TYPE
                    exit;
                }
            }
        }
        $catalogfile = "";
        $compress = 0;
        if (isset($_FILES['catalogfile']['name']) && $_FILES['catalogfile']['name'] != '') {
            if ($_FILES["catalogfile"]['size'] != '' && $_FILES["catalogfile"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG) {
                echo 5; // CATALOG FILE SIZE IS LARGE
                exit;
            }
            if ($_FILES["catalogfile"]['type'] != 'application/pdf') {
                $compress = 1;
            }
            $catalogfile = uploadFile('catalogfile', 'CATALOG_IMGPDF', CATALOG_PATH, '*', '', $compress, CATALOG_LOCAL_PATH);
            if ($catalogfile !== 0) {
                if ($catalogfile == 2) {
                    echo 7; //catalog file not uploaded
                    exit;
                }
            } else {
                echo 6; //INVALID TYPE
                exit;
            }
        }
        $this->Product->_where = '(name="' . $productname . '" OR slug="' . $slug . '")';
        $sqlname = $this->Product->getRecordsByID();

        if (empty($sqlname)) {

            $InsertData = array(
                'categoryid' => $categoryid,
                'brandid' => $brandid,
                'name' => $productname,
                'importerProductName' => $importerproductname,
                'supplierProductName' => $supplierproductname,
                'installationcost' => $installationcost,
                'slug' => $slug,
                'shortdescription' => $shortdescription,
                'description' => $description,
                'isuniversal' => $isuniversal,
                'hsncodeid' => $hsncodeid,
                'priority' => $priority,
                'catalogfile' => $catalogfile,
                'status' => $status,
                'createddate' => $createddate,
                'modifeddate' => $modifieddate,
                'addedby' => $addedby,
                'modififedby' => $modifiedby,
            );

            $insertid = $this->Product->add($InsertData);

            if ($insertid != 0) {

                $insetprice_arr = array();
                $price_arr = explode(",", $price);
                foreach ($price_arr as $pa) {
                    $price = (!empty($pa)) ? $pa : 0;
                    $barcode = ($isuniversal == 1 ? $PostData['barcode'] : '');

                    $insetprice_arr = array(
                        "productid" => $insertid,
                        "price" => $price,
                        "stock" => $stock,
                        "unitid" => $unitid,
                        'sku' => $sku,
                        'weight' => $weight,
                        'barcode' => $barcode,
                        'minimumstocklimit' => $minimumstocklimit,
                    );

                    $productpricesid = $this->Product_prices->add($insetprice_arr);
                }

                $Imageextensions = array("bmp", "bm", "gif", "ico", "jfif", "jfif-tbnl", "jpe", "jpeg", "jpg", "pbm", "png", "svf", "tif", "tiff", "wbmp", "x-png");

                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if (isset($_FILES['productfile' . $id]['name']) && $_FILES['productfile' . $id]['name'] != '' && strpos($key, 'productfile') !== false) {
                        $temp = explode('.', $_FILES['productfile' . $id]['name']);
                        $extension = end($temp);
                        $type = 0;
                        $image_width = $image_height = '';
                        if (in_array($extension, $Imageextensions, true)) {
                            $type = 1;
                            $image_width = PRODUCT_IMG_WIDTH;
                            $image_height = PRODUCT_IMG_HEIGHT;
                        }
                        $file = uploadFile('productfile' . $id, 'PRODUCT', PRODUCT_PATH, '*', '', 1, PRODUCT_LOCAL_PATH, $image_width, $image_height);
                        if ($file !== 0) {
                            if ($file == 2) {
                                echo 2; //image not uploaded
                                exit;
                            }
                            $insertdata = array(
                                "productid" => $insertid,
                                "type" => $type,
                                "filename" => $file,
                            );
                            $this->Product->_table = tbl_productimage;
                            $this->Product->add($insertdata);
                        } else {
                            echo 3; //INVALID image TYPE
                            exit;
                        }
                    } else {
                        $file = '';
                    }
                }

                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(1, 'Product', "Add new " . ucfirst($productname) . ' product.');
                }
                echo 1;
            } else {
                echo 0; // page content not added
            }
        } else {
            echo 4; //page content already exists
        }
    }

    public function update_Sales_order()
    {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $productid = isset($PostData['productid']) ? trim($PostData['productid']) : '';
        $productname = isset($PostData['productname']) ? trim($PostData['productname']) : '';
        $importerproductname = isset($PostData['importerproductname']) ? trim($PostData['importerproductname']) : '';
        $supplierproductname = isset($PostData['supplierproductname']) ? trim($PostData['supplierproductname']) : '';
        $installationcost = isset($PostData['installationcost']) ? trim($PostData['installationcost']) : '0';
        $slug = isset($PostData['productslug']) ? trim($PostData['productslug']) : '';
        $shortdescription = isset($PostData['shortdescription']) ? trim($PostData['shortdescription']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';
        $stock = isset($PostData['stock']) ? trim($PostData['stock']) : 0;
        $hsncodeid = isset($PostData['hsncodeid']) ? trim($PostData['hsncodeid']) : '';
        $metatitle = isset($PostData['metatitle']) ? trim($PostData['metatitle']) : '';
        $metadescription = isset($PostData['metadescription']) ? trim($PostData['metadescription']) : '';
        $metakeyword = isset($PostData['metakeyword']) ? trim($PostData['metakeyword']) : '';
        $priority = isset($PostData['priority']) ? trim($PostData['priority']) : '';
        $discount = isset($PostData['discount']) ? trim($PostData['discount']) : '';
        $status = $PostData['status'];
        $categoryid = isset($PostData['categoryid']) ? trim($PostData['categoryid']) : '';
        $brandid = $PostData['brandid'];
        $unitid = $PostData['unitid'];
        $sku = $PostData['sku'];
        $weight = isset($PostData['weight']) ? $PostData['weight'] : 0;
        $minimumstocklimit = $PostData['minimumstocklimit'];

        $productdisplayonfront = (isset($PostData['productdisplayonfront']) ? 1 : 0);

        $returnpolicytitle = isset($PostData['returnpolicytitle']) ? trim($PostData['returnpolicytitle']) : '';
        $returnpolicydescription = isset($PostData['returnpolicydescription']) ? trim($PostData['returnpolicydescription']) : '';
        $replacementpolicytitle = isset($PostData['replacementpolicytitle']) ? trim($PostData['replacementpolicytitle']) : '';
        $replacementpolicydescription = isset($PostData['replacementpolicydescription']) ? trim($PostData['replacementpolicydescription']) : '';

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();

        if (isset($PostData['checkuniversal'])) {
            $isuniversal = 1;
            $price = isset($PostData['price']) ? $PostData['price'] : 0;
        } else {
            $isuniversal = 0;
            $prices = isset($PostData['prices']) ? $PostData['prices'] : 0;
        }
        $barcode = ($isuniversal == 1 ? $PostData['barcode'] : '');
        $commingsoon = isset($PostData['commingsoon']) ? $PostData['commingsoon'] : '0';
        $productpricesid = isset($PostData['productpricesid']) ? trim($PostData['productpricesid']) : '';

        if ($isuniversal == 1) {
            $this->Product->removeVariantInUpdateProduct($productid);
            $Count = $this->Product->CheckProductSKUAvailable($sku, $productid, 1);
            if ($Count > 0) {
                echo 8;
                exit;
            }
        }

        $this->Product->_where = 'id <>"' . $productid . '" AND (name="' . $productname . '" OR slug="' . $slug . '")'; //AND id <> ".$id." AND maincategoryid = ".$maincategoryid;
        $sqlname = $this->Product->getRecordsByID();

        if (!is_dir(PRODUCT_PATH)) {
            @mkdir(PRODUCT_PATH);
        }
        if (!is_dir(CATALOG_PATH)) {
            @mkdir(CATALOG_PATH);
        }
        foreach ($_FILES as $key => $value) {

            $id = preg_replace('/[^0-9]/', '', $key);
            if (!isset($PostData['productfileid' . $id]) && isset($_FILES['productfile' . $id]['name']) && $_FILES['productfile' . $id]['name'] != '') {
                $file = uploadFile('productfile' . $id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, '', '', 0);
                if ($file === 0) {
                    echo 3; //INVALID PRODUCT FILE TYPE
                    exit;
                }
            }
        }

        if (empty($sqlname)) {
            $updateData = array(
                'categoryid' => $categoryid,
                'brandid' => $brandid,
                'name' => $productname,
                'importerProductName' => $importerproductname,
                'supplierProductName' => $supplierproductname,
                'slug' => $slug,
                'shortdescription' => $shortdescription,
                'description' => $description,
                'isuniversal' => $isuniversal,
                'hsncodeid' => $hsncodeid,
                'metatitle' => $metatitle,
                'metakeyword' => $metakeyword,
                'metadescription' => $metadescription,
                'installationcost' => $installationcost,
                'priority' => $priority,
                'commingsoon' => $commingsoon,
                'returnpolicytitle' => $returnpolicytitle,
                'returnpolicydescription' => $returnpolicydescription,
                'replacementpolicytitle' => $replacementpolicytitle,
                'replacementpolicydescription' => $replacementpolicydescription,
                'productdisplayonfront' => $productdisplayonfront,
                'status' => $status,
                'modifeddate' => $modifieddate,
                'modififedby' => $modifiedby,
            );

            $this->Product->_where = array('id' => $productid);
            $updateid = $this->Product->Edit($updateData);

            $this->load->model("Product_prices_model", "Product_prices");
            if ($isuniversal) {
                $updatedata["price"] = $price;
                $updatedata["stock"] = $stock;
                $updatedata["barcode"] = $barcode;
                $updatedata["sku"] = $sku;
            }
            $updatedata["weight"] = $weight;
            $updatedata["unitid"] = $unitid;

            $this->Product_prices->_where = array('productid' => $productid);
            $this->Product_prices->Edit($updatedata);

            if (!empty($productpricesid)) {
                $variantpricearray = isset($PostData['variantprice']) ? $PostData['variantprice'] : array();
                $variantqtyarray = isset($PostData['variantqty']) ? $PostData['variantqty'] : array();
                $variantdiscpercentarray = isset($PostData['variantdiscpercent']) ? $PostData['variantdiscpercent'] : array();

                $InsertMultiplePriceData = $UpdateMultiplePriceData = $UpdatedProductQuantityPrice = array();

                $productquantitypricesid = !empty($PostData['singlequantitypricesid']) ? $PostData['singlequantitypricesid'] : "";

                if ($PostData['price'] > 0) {

                    if (!empty($productquantitypricesid)) {

                        $UpdateMultiplePriceData[] = array(
                            "id" => $productquantitypricesid,
                            "price" => $PostData['price'],
                            "discount" => $discount,
                        );

                        $UpdatedProductQuantityPrice[] = $productquantitypricesid;
                    } else {

                        $InsertMultiplePriceData[] = array(
                            "productpricesid" => $productpricesid,
                            "price" => $PostData['price'],
                            "quantity" => 1,
                            "discount" => $discount,
                        );
                    }
                }
                //    }

                $priceqtydata = $this->Product_prices->getProductQuantityPriceDataByPriceID($productpricesid);
                if (!empty($priceqtydata)) {
                    $priceqtyids = array_column($priceqtydata, "id");
                    $resultId = array_diff($priceqtyids, $UpdatedProductQuantityPrice);

                    if (!empty($resultId)) {
                        $this->Product_prices->_table = tbl_productquantityprices;
                        $this->Product_prices->Delete(array("id IN (" . implode(",", $resultId) . ")" => null));
                    }
                }

                if (!empty($InsertMultiplePriceData)) {
                    $this->Product_prices->_table = tbl_productquantityprices;
                    $this->Product_prices->add_batch($InsertMultiplePriceData);
                }

                if (!empty($UpdateMultiplePriceData)) {
                    $this->Product_prices->_table = tbl_productquantityprices;
                    $this->Product_prices->edit_batch($UpdateMultiplePriceData, 'id');
                }

            }
            if ($updateid != 0) {

                $this->Product->_table = tbl_productimage;

                if (isset($PostData['removeproductfileid']) && $PostData['removeproductfileid'] != '') {

                    $this->readdb->select("id,type,filename");
                    $this->readdb->from(tbl_productimage);
                    $this->readdb->where("FIND_IN_SET(id,'" . implode(',', array_filter(explode(",", $PostData['removeproductfileid']))) . "')>0");
                    $query = $this->readdb->get();
                    $FileMappingData = $query->result_array();

                    if (!empty($FileMappingData)) {
                        foreach ($FileMappingData as $row) {
                            if ($row['type'] == 1) {
                                unlinkfile("PRODUCT", $row['filename'], PRODUCT_PATH);
                            }
                            $this->Product->Delete(array('id' => $row['id']));
                        }
                    }
                }

                $productfileid_arr = array();
                foreach ($_FILES as $key => $value) {

                    $id = preg_replace('/[^0-9]/', '', $key);
                    if (isset($_FILES['productfile' . $id]['name'])) {

                        $type = $PostData['filetype' . $id];
                        $image_width = $image_height = '';
                        if ($type == 1) {
                            $image_width = PRODUCT_IMG_WIDTH;
                            $image_height = PRODUCT_IMG_HEIGHT;
                        }
                        if (!isset($PostData['productfileid' . $id])) {

                            if (isset($_FILES['productfile' . $id]['name']) && $_FILES['productfile' . $id]['name'] != '') {

                                $file = uploadFile('productfile' . $id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, $image_width, $image_height);
                                if ($file !== 0) {
                                    if ($file == 2) {
                                        echo 2; //image not uploaded
                                        exit;
                                    }
                                }

                                $insertdata = array(
                                    "productid" => $productid,
                                    "type" => $type,
                                    "filename" => $file,
                                );
                                $insertdata = array_map('trim', $insertdata);

                                $ProductfileID = $this->Product->Add($insertdata);
                                $productfileid_arr[] = $ProductfileID;
                            }
                        } else if (isset($_FILES['productfile' . $id]['name']) && $_FILES['productfile' . $id]['name'] != '' && isset($PostData['productfileid' . $id])) {

                            $this->Product_file->_where = "id=" . $PostData['productfileid' . $id];
                            $FileData = $this->Product_file->getRecordsByID();

                            if (!empty($FileData)) {
                                if ($FileData['type'] == 1) {
                                    unlinkfile("PRODUCT", $FileData['filename'], PRODUCT_PATH);
                                }
                            }

                            $file = uploadFile('productfile' . $id, 'PRODUCT', PRODUCT_PATH, '*', '', 0, PRODUCT_LOCAL_PATH, $image_width, $image_height);
                            if ($file !== 0) {
                                if ($file == 2) {
                                    echo 2; //image not uploaded
                                    exit;
                                }
                            }

                            $updatedata = array("type" => $type, "filename" => $file/*,"priority"=>$PostData['filepriority'.$id]*/);
                            $updatedata = array_map('trim', $updatedata);

                            $this->Product_file->_where = "id=" . $PostData['productfileid' . $id];
                            $this->Product_file->Edit($updatedata);
                            $productfileid_arr[] = $PostData['productfileid' . $id];
                        } else if (isset($_FILES['productfile' . $id]['name']) && $_FILES['productfile' . $id]['name'] == '' && isset($PostData['productfileid' . $id])) {

                            $productfileid_arr[] = $PostData['productfileid' . $id];
                        } else {

                            $productfileid_arr[] = $PostData['productfileid' . $id];
                        }
                    }
                }
                if (isset($productfileid_arr) && count($productfileid_arr) > 0) {
                    $this->Product->Delete("id NOT IN (" . implode(",", $productfileid_arr) . ") and productid=$productid");
                }

                if (!empty($PostData['relatedproductid'])) {

                    $this->load->model("Related_product_model", "Related_product");
                    if ($PostData['removerelatedproductid'] != '') {
                        $this->Related_product->Delete("FIND_IN_SET(relatedproductid,'" . $PostData['removerelatedproductid'] . "')>0 AND productid=" . $productid);
                    }
                    $PostData['relatedproductid'] = array_filter(explode(',', $PostData['relatedproductid']));

                    if (count($PostData['relatedproductid']) > 0) {
                        foreach ($PostData['relatedproductid'] as $relatedproductid) {

                            $this->Related_product->_where = "relatedproductid=" . $relatedproductid . " AND productid=" . $productid;
                            $Count = $this->Related_product->CountRecords();
                            if ($Count == 0) {
                                $insertdata = array(
                                    "productid" => $productid,
                                    "relatedproductid" => $relatedproductid,
                                );
                                $insertdata = array_map('trim', $insertdata);
                                $this->Related_product->Add($insertdata);
                            }
                        }
                    }
                }

                if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                    $this->general_model->addActionLog(2, 'Product', "Edit " . ucfirst($productname) . ' product.');
                }
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 4;
        }
    }

}