<?php
defined('BASEPATH') or exit('No direct script access allowed');

class  Product extends Admin_Controller
{

    public $viewData = array();
    function __construct()
    {
        parent::__construct();
        $this->load->model('Product_model', 'Product');
        $this->load->model('Product_file_model', 'Product_file');

        $this->load->model('Side_navigation_model', 'Side_navigation');
        $this->viewData = $this->getAdminSettings('submenu', 'Product');
    }

    public function index()
    {
        $this->viewData['title'] = "Product";
        $this->viewData['module'] = "product/product";
        $this->viewData['VIEW_STATUS'] = "1";

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Product', 'View product.');
        }

        $this->viewData['categorydata'] = $this->Product->getAllCategory();

        $this->load->model('Brand_model', 'Brand');
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->admin_headerlib->add_javascript("Product", "pages/product.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing()
    {

        $this->load->model('Stock_report_model', 'Stock');
        $this->load->model("Product_combination_model", "Product_combination");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url() . 'ADMINUSERTYPE'];

        $list = $this->Product->get_datatables();
        $data = array();
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();

        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
            $varianthtml = '';
            $productname = '';


            if (in_array($rollid, $edit)) {
                $actions .= '<a class="' . edit_class . '" href="' . ADMIN_URL . 'product/product-edit/' . $datarow->id . '/' . '" title="' . edit_title . '">' . edit_text . '</a>';


                if ($datarow->status == 1) {
                    $actions .= '<span id="span' . $datarow->id . '"><a href="javascript:void(0)" onclick="enabledisable(0,' . $datarow->id . ',\'' . ADMIN_URL . 'product/product-enable-disable\',\'' . disable_title . '\',\'' . disable_class . '\',\'' . enable_class . '\',\'' . disable_title . '\',\'' . enable_title . '\',\'' . disable_text . '\',\'' . enable_text . '\')" class="' . disable_class . '" title="' . disable_title . '">' . stripslashes(disable_text) . '</a></span>';
                } else {
                    $actions .= '<span id="span' . $datarow->id . '"><a href="javascript:void(0)" onclick="enabledisable(1,' . $datarow->id . ',\'' . ADMIN_URL . 'product/product-enable-disable\',\'' . enable_title . '\',\'' . disable_class . '\',\'' . enable_class . '\',\'' . disable_title . '\',\'' . enable_title . '\',\'' . disable_text . '\',\'' . enable_text . '\')" class="' . enable_class . '" title="' . enable_title . '">' . stripslashes(enable_text) . '</a></span>';
                }
            }


            if (in_array($rollid, $delete)) {
                $actions .= '<a class="' . delete_class . '" href="javascript:void(0)" title="' . delete_title . '" onclick=deleterow(' . $datarow->id . ',"' . ADMIN_URL . 'product/check-product-use","Product","' . ADMIN_URL . 'product/delete-mul-product","producttable") >' . delete_text . '</a>';

                $checkbox = '<div class="checkbox"><input value="' . $datarow->id . '" type="checkbox" class="checkradios" name="check' . $datarow->id . '" id="check' . $datarow->id . '" onchange="singlecheck(this.id)"><label for="check' . $datarow->id . '"></label></div>';
            }

            if (in_array($rollid, $edit)) {
                if ($datarow->isuniversal == 0) {
                    $actions .= '<a class="btn btn-primary btn-raised btn-sm" href="' . ADMIN_URL . 'product/product-variant/' . $datarow->id . '/' . '" title="ADD VARIANT"><i class="fa fa-plus"></i></a> ';
                }
            }

            
            $productcombination = array();
            if ($datarow->isuniversal == 0 && $datarow->variantid != '') {
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->id, $datarow->variantid);

                if (!empty($variantdata)) {
                    $varianthtml .= "<div class='row' style=''>";
                    foreach ($variantdata as $variant) {
                        $varianthtml .= "<div class='col-md-12 p-n'>";
                        $varianthtml .= "<div class='col-sm-3 popover-content-style'>" . $variant['variantname'] . "</div>";
                        $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                        $varianthtml .= "<div class='col-sm-7 popover-content-style'>" . $variant['variantvalue'] . "</div>";
                        $varianthtml .= "</div>";
                    }
                    $varianthtml .= "</div>";
                }
                $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="' . $varianthtml . '" style="color: ' . VARIANT_COLOR . ' !important;">' . ucwords($datarow->name) . '</a>';

                $productattribute = $this->Product_combination->getProductAttribute($datarow->id);
                $productattributewithcombination = $this->Product_combination->getProductCombinationWithAttribute($datarow->id);
                $productattributejoindata = array_column($productattributewithcombination, 'joindata');
                $productvariantdata = array_column($productattributewithcombination, 'value');

                $productcombinationarr = $this->Product_combination->getProductcombinationWithStock($datarow->id);

                if (count($productcombinationarr)) {
                    $html = '<div id="variant' . $datarow->id . '" style="display:none;">
                            <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th class="width15">Price</th>
                                    <th>Stock</th>
                                    <th>Points for Seller</th>
                                    <th>Points for  Buyer</th>';
                    foreach ($productattribute as $attributerow) {
                        $html .= '<th>' . $attributerow['variantname'] . '</th>';
                    }
                    $html .= '</tr></thead>
                            <tbody>';


                    foreach ($productcombinationarr as $pc) {
                        $pricesArray = explode(",", $pc['productprice']);
                        $qtyArray = explode(",", $pc['productqty']);
                        $discArray = explode(",", $pc['productdisc']);
                        $priceHTML = "";
                        foreach ($pricesArray as $prkey => $rowval) {

                            $discounthtml = "";
                            if ($discArray[$prkey] > 0) {
                                $discounthtml = " " . $discArray[$prkey] . "% Off";
                                if (number_format($discArray[$prkey], 2, '.', '') == "100.00") {
                                    $discounthtml = " Free";
                                }
                            }
                            $priceHTML .= '<p>' . CURRENCY_CODE . (!empty($rowval) ? numberFormat($rowval, 2, ',') : '0.00') . ' ' . $qtyArray[$prkey] . ($datarow->quantitytype == 0 ? "+" : "") . ' Qty' . $discounthtml . '</p>';
                        }
                      
                        $html .= '<td>' . $priceHTML . '</td>
                                    <td>' . (int)$pc['pointsforseller'] . '</td>
                                    <td>' . (int)$pc['pointsforbuyer'] . '</td>';

                        foreach ($productattribute as $attributerow) {
                            $searchstring = $pc['priceid'] . '|' . $attributerow['id'];
                            if (in_array($searchstring, $productattributejoindata)) {
                                $key = array_search($searchstring, $productattributejoindata);
                                $html .= '<td>' . $productvariantdata[$key] . '</td>';
                            } else {
                                $html .= '<td>-</td>';
                            }
                        }
                        $html .= '</tr>';
                    }
                    $html .= '</tbody></table></div>';
                    $checkbox .= $html;
                }
            } else {
                $productname = ucwords($datarow->name);
            }
            $productdetail='';
            if ($datarow->productimage != "" && file_exists(PRODUCT_PATH . $datarow->productimage)) {
                $productdetail = '<img class="pull-left thumbwidth" src="' . PRODUCT . $datarow->productimage . '" style="margin-right: 10px;"><div class="" style="display: flex;">' . $productname . '</div>';
            } else if (PRODUCTDEFAULTIMAGE != "" && file_exists(PRODUCT_PATH . PRODUCTDEFAULTIMAGE)) {
                $productdetail = '<img class="pull-left thumbwidth" src="' . PRODUCT . PRODUCTDEFAULTIMAGE . '" style="margin-right: 10px;"><div class="" style="display: flex;">' . $productname . '</div>';
            }

           
            if (number_format($datarow->minprice, 2, '.', '') == number_format($datarow->maxprice, 2, '.', '')) {
                $price = number_format($datarow->minprice, 2, '.', ',');
            } else {
                $price = number_format($datarow->minprice, 2, '.', ',') . " - " . number_format($datarow->maxprice, 2, '.', ',');

                if ($datarow->isuniversal == 0) {
                    $price = "<a href='javascript:void(0)' onclick='viewvariantdetails(" . $datarow->id . ",\"" . ucwords($datarow->name) . "\")'><span class='pull-right'>" . $price . "</span></a>";
                }
            }

            
        
            $row[] = ++$counter;
            $row[] = $productname;
            $row[] = $datarow->categoryname;
            $row[] = $datarow->brandname;
            $row[] = $datarow->stock;
            $row[] = $price;
            $row[] = "<span class='pull-right'>" . $datarow->priority . "</span>";
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $row[] = $checkbox;
            
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Product->count_all(),
            "recordsFiltered" => $this->Product->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add_product()
    {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Product');
        $this->viewData['title'] = "Add Product";
        $this->viewData['module'] = "product/add_product";
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['maincategorydata'] = $this->Product->getmaincategory();
        
        duplicate:
        $barcode = rand(1000000000, 9999999999);

        $this->load->model("Product_prices_model", "Product_prices");
        $this->Product_prices->_where = "barcode='" . $barcode . "'";
        $Count = $this->Product_prices->CountRecords();
        if ($Count > 0) {
            goto duplicate;
        }
        $this->viewData['barcode'] = $barcode;

        $this->load->model("Hsn_code_model", "Hsn_code");
        $this->viewData['hsncodedata'] = $this->Hsn_code->getActiveHsncode();

        $this->load->model("Brand_model", "Brand");
        $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->load->model("Product_unit_model", "Product_unit");
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        $this->viewData['productcount'] = $this->Product->CountRecords();
        //echo NOOFPRODUCT;exit;

        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("product", "pages/add_product.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function product_edit($id)
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
        $this->viewData['productid'] =  $id;
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
        $this->admin_headerlib->add_bottom_javascripts("product", "pages/add_product.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function product_add()
    {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $this->load->model("Product_prices_model", "Product_prices");
        $this->load->model("Related_product_model", "Related_product");
        
        $productname = isset($PostData['productname']) ? trim($PostData['productname']) : '';
        $importerproductname = isset($PostData['importerproductname']) ? trim($PostData['importerproductname']) : '';
        $supplierproductname = isset($PostData['supplierproductname']) ? trim($PostData['supplierproductname']) : '';
        $installationcost=isset($PostData['installationcost']) ? trim($PostData['installationcost']) : '0';
        $slug = isset($PostData['productslug']) ? trim($PostData['productslug']) : '';
        $shortdescription = isset($PostData['shortdescription']) ? trim($PostData['shortdescription']) : '';

        $description = isset($PostData['description']) ? trim($PostData['description']) : '';
        $stock = isset($PostData['stock']) ? trim($PostData['stock']) : 0;
        $hsncodeid = isset($PostData['hsncodeid']) ? trim($PostData['hsncodeid']) : '';
        $priority =  isset($PostData['priority']) ? trim($PostData['priority']) : '';
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
            $price =  isset($PostData['price']) ? $PostData['price'] : 0;
            $pointspriority = 0;
        } else {
            $isuniversal = 0;
            $price =  isset($PostData['prices']) ? $PostData['prices'] : 0;
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
                echo 5;    // CATALOG FILE SIZE IS LARGE
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
                'importerProductName'=>$importerproductname,
                'supplierProductName'=>$supplierproductname,
                'installationcost'=>$installationcost,
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
                'modififedby' => $modifiedby
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
                        "price"=>$price,
                        "stock" => $stock,
                        "unitid" => $unitid,
                        'sku' => $sku,
                        'weight' => $weight,
                        'barcode' => $barcode,
                        'minimumstocklimit' => $minimumstocklimit
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
                                "filename" => $file
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

    public function update_product()
    {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $productid = isset($PostData['productid']) ? trim($PostData['productid']) : '';
        $productname = isset($PostData['productname']) ? trim($PostData['productname']) : '';
        $importerproductname = isset($PostData['importerproductname']) ? trim($PostData['importerproductname']) : '';
        $supplierproductname = isset($PostData['supplierproductname']) ? trim($PostData['supplierproductname']) : '';
        $installationcost=isset($PostData['installationcost']) ? trim($PostData['installationcost']) : '0';
        $slug = isset($PostData['productslug']) ? trim($PostData['productslug']) : '';
        $shortdescription = isset($PostData['shortdescription']) ? trim($PostData['shortdescription']) : '';
        $description = isset($PostData['description']) ? trim($PostData['description']) : '';
        $stock = isset($PostData['stock']) ? trim($PostData['stock']) : 0;
        $hsncodeid = isset($PostData['hsncodeid']) ? trim($PostData['hsncodeid']) : '';
        $metatitle = isset($PostData['metatitle']) ? trim($PostData['metatitle']) : '';
        $metadescription =  isset($PostData['metadescription']) ? trim($PostData['metadescription']) : '';
        $metakeyword =  isset($PostData['metakeyword']) ? trim($PostData['metakeyword']) : '';
        $priority =  isset($PostData['priority']) ? trim($PostData['priority']) : '';
        $discount = isset($PostData['discount']) ? trim($PostData['discount']) : '';
        $status = $PostData['status'];
        $categoryid = isset($PostData['categoryid']) ? trim($PostData['categoryid']) : '';
        $brandid = $PostData['brandid'];
        $unitid = $PostData['unitid'];
        //$producttype = $PostData['producttype'];
        $sku = $PostData['sku'];
        $weight = isset($PostData['weight']) ? $PostData['weight'] : 0;
        $minimumstocklimit = $PostData['minimumstocklimit'];
        // $minimumsalesprice = $PostData['minimumsalesprice'];
        // $minimumorderqty = $PostData['minimumorderqty'];
        // $maximumorderqty = $PostData['maximumorderqty'];
        $productdisplayonfront = (isset($PostData['productdisplayonfront']) ? 1 : 0);
        //$quantitytype = $PostData['quantitytype'];

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
            $price =  isset($PostData['price']) ? $PostData['price'] : 0;
        } else {
            $isuniversal = 0;
            $prices =  isset($PostData['prices']) ? $PostData['prices'] : 0;
        }
        $barcode = ($isuniversal == 1 ? $PostData['barcode'] : '');
        $commingsoon = isset($PostData['commingsoon']) ? $PostData['commingsoon'] : '0';
        //$pricetype = $PostData['pricetype'];
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
        // $oldcatalogfile = trim($PostData['oldcatalogfile']);
        // $catalogfile = $oldcatalogfile;
        // $compress = 0;
        // if (isset($_FILES['catalogfile']['name']) && $_FILES['catalogfile']['name'] != '' && $oldcatalogfile != '') {
        //     if ($_FILES["catalogfile"]['size'] != '' && $_FILES["catalogfile"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG) {
        //         echo 5;    // CATALOG FILE SIZE IS LARGE
        //         exit;
        //     }
        //     if ($_FILES["catalogfile"]['type'] != 'application/pdf') {
        //         $compress = 1;
        //     }

        //     $catalogfile = reuploadfile('catalogfile', 'CATALOG_IMGPDF', $oldcatalogfile, CATALOG_PATH, "*", '', $compress, CATALOG_LOCAL_PATH);
        //     if ($catalogfile !== 0) {
        //         if ($catalogfile == 2) {
        //             echo 7; //catalog file not uploaded
        //             exit;
        //         }
        //     } else {
        //         echo 6; //INVALID TYPE
        //         exit;
        //     }
        // } else if (isset($_FILES['catalogfile']['name']) && $_FILES['catalogfile']['name'] != '' && $oldcatalogfile == '') {
        //     if ($_FILES["catalogfile"]['type'] != 'application/pdf') {
        //         $compress = 1;
        //     }
        //     if ($_FILES["catalogfile"]['size'] != '' && $_FILES["catalogfile"]['size'] >= UPLOAD_MAX_FILE_SIZE_CATALOG) {
        //         echo 5;    // CATALOG FILE SIZE IS LARGE
        //         exit;
        //     }
        //     $catalogfile = uploadFile('catalogfile', 'CATALOG_IMGPDF', CATALOG_PATH, '*', '', $compress, CATALOG_LOCAL_PATH);
        //     if ($catalogfile !== 0) {
        //         if ($catalogfile == 2) {
        //             echo 7; //catalog file not uploaded
        //             exit;
        //         }
        //     } else {
        //         echo 6; //INVALID TYPE
        //         exit;
        //     }
        // } else if (isset($_FILES['catalogfile']['name']) && $_FILES['catalogfile']['name'] == '') {
        //     if ($oldcatalogfile != '' && $PostData['isvalidcatalogfile'] == 0) {
        //         unlinkfile("CATALOG_IMGPDF", $oldcatalogfile, CATALOG_PATH);
        //         $catalogfile = '';
        //     } else if ($oldcatalogfile == '') {
        //         $catalogfile = '';
        //     }
        // }


        if (empty($sqlname)) {
            $updateData = array(
                'categoryid' => $categoryid,
                'brandid' => $brandid,
                'name' => $productname,
                'importerProductName'=>$importerproductname,
                'supplierProductName'=>$supplierproductname,
                'slug' => $slug,
                'shortdescription' => $shortdescription,
                'description' => $description,
                'isuniversal' => $isuniversal,
                'hsncodeid' => $hsncodeid,
                /* 'discount'=>$discount, */
                'metatitle' => $metatitle,
                'metakeyword' => $metakeyword,
                'metadescription' => $metadescription,
                'installationcost'=>$installationcost,
                'priority' => $priority,
                'commingsoon' => $commingsoon,
                //'producttype' => $producttype,
                //'catalogfile' => $catalogfile,
                'returnpolicytitle' => $returnpolicytitle,
                'returnpolicydescription' => $returnpolicydescription,
                'replacementpolicytitle' => $replacementpolicytitle,
                'replacementpolicydescription' => $replacementpolicydescription,
                'productdisplayonfront' => $productdisplayonfront,
               // 'quantitytype' => $quantitytype,
                'status' => $status,
                'modifeddate' => $modifieddate,
                'modififedby' => $modifiedby
            );

            $this->Product->_where = array('id' => $productid);
            $updateid = $this->Product->Edit($updateData);

            $this->load->model("Product_prices_model", "Product_prices");
            if ($isuniversal) {
                $updatedata["price"] = $price;
                $updatedata["stock"] = $stock;
                $updatedata["barcode"] = $barcode;
                $updatedata["sku"] = $sku;
                // $updatedata['minimumstocklimit'] = $minimumstocklimit;
                // $updatedata['minimumsalesprice'] = $minimumsalesprice;
                // $updatedata["minimumorderqty"] = $minimumorderqty;
                // $updatedata["maximumorderqty"] = $maximumorderqty;
                // $updatedata["pricetype"] = $pricetype;
            }
            $updatedata["weight"] = $weight;
            $updatedata["unitid"] = $unitid;

            $this->Product_prices->_where = array('productid' => $productid);
            $this->Product_prices->Edit($updatedata);

            if (!empty($productpricesid)) {
                $variantpricearray = isset($PostData['variantprice']) ? $PostData['variantprice'] : array();
                $variantqtyarray =  isset($PostData['variantqty']) ? $PostData['variantqty'] : array();
                $variantdiscpercentarray =  isset($PostData['variantdiscpercent']) ? $PostData['variantdiscpercent'] : array();

                $InsertMultiplePriceData = $UpdateMultiplePriceData = $UpdatedProductQuantityPrice = array();

                // if ($pricetype == 1) {
                //     if (!empty($variantpricearray)) {
                //         foreach ($variantpricearray as $k => $variantprice) {

                //             $productquantitypricesid = isset($PostData['productquantitypricesid'][$k]) ? $PostData['productquantitypricesid'][$k] : "";

                //             if ($variantprice > 0 && $variantqtyarray[$k] > 0) {

                //                 if (!empty($productquantitypricesid)) {

                //                     $UpdateMultiplePriceData[] = array(
                //                         "id" => $productquantitypricesid,
                //                         "price" => $variantprice,
                //                         "quantity" => $variantqtyarray[$k],
                //                         "discount" => $variantdiscpercentarray[$k]
                //                     );

                //                     $UpdatedProductQuantityPrice[] = $productquantitypricesid;
                //                 } else {

                //                     $InsertMultiplePriceData[] = array(
                //                         "productpricesid" => $productpricesid,
                //                         "price" => $variantprice,
                //                         "quantity" => $variantqtyarray[$k],
                //                         "discount" => $variantdiscpercentarray[$k]
                //                     );
                //                 }
                //             }
                //         }
                //     }
                // } else {
                    $productquantitypricesid = !empty($PostData['singlequantitypricesid']) ? $PostData['singlequantitypricesid'] : "";

                    if ($PostData['price'] > 0) {

                        if (!empty($productquantitypricesid)) {

                            $UpdateMultiplePriceData[] = array(
                                "id" => $productquantitypricesid,
                                "price" => $PostData['price'],
                                "discount" => $discount
                            );

                            $UpdatedProductQuantityPrice[] = $productquantitypricesid;
                        } else {

                            $InsertMultiplePriceData[] = array(
                                "productpricesid" => $productpricesid,
                                "price" => $PostData['price'],
                                "quantity" => 1,
                                "discount" => $discount
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
                                //$ProductfileID = $this->Productfile->Add($insertdata);
                                /*if($PostData['filepriority'.$id]!=''){
                                    $insertdata['priority'] = $PostData['filepriority'.$id];
                                }else{
                                    $this->db->set('priority',"(SELECT IFNULL(MAX(pf.priority),0)+1 as priority FROM ".tbl_productfile." as pf WHERE pf.productid=".$ProductID.")",FALSE);
                                }*/


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

                            /*$updatedata = array("priority"=>$PostData['filepriority'.$id]);
                            $updatedata = array_map('trim', $updatedata);
    
                            $this->Productfile->_where = "id=".$PostData['productfileid'.$id];
                            $this->Productfile->Edit($updatedata); 
                            */
                            $productfileid_arr[] = $PostData['productfileid' . $id];
                        } else {
                            /*$updatedata = array("priority"=>$PostData['filepriority'.$id]);
                            $updatedata = array_map('trim', $updatedata);
    
                            $this->Productfile->_where = "id=".$PostData['productfileid'.$id];
                            $this->Productfile->Edit($updatedata);
                            */
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
                    /* for ($i=0; $i < count($PostData['relatedproductid']); $i++) {
                        $this->Related_product->_where = "relatedproductid=".$PostData['relatedproductid'][$i]." AND productid=".$productid;
                        $Count = $this->Related_product->CountRecords();
                        if($Count==0){
                            $insertdata = array(
                                "productid" => $productid,
                                "relatedproductid" => $PostData['relatedproductid'][$i],
                            );
                            $insertdata = array_map('trim', $insertdata);
                            $this->Related_product->Add($insertdata);
                        }
                        
                    } */
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

    public function set_barcode($code)
    {
        $this->load->model('Common_model', 'Common_model');
        $this->Common_model->set_barcode($code);
    }

    public function generateBarcode()
    {

        $PostData = $this->input->post();

        duplicate:
        $barcode = rand(1000000000, 9999999999);

        $this->load->model("Product_prices_model", "Product_prices");
        $this->Product_prices->_where = "barcode='" . $barcode . "'";
        $Count = $this->Product_prices->CountRecords();
        if ($Count > 0) {
            goto duplicate;
        }

        echo $barcode;
    }
    public function verifyBarcode()
    {

        $PostData = $this->input->post();
        $barcode = $PostData['barcode'];

        $this->load->model("Product_prices_model", "Product_prices");
        $this->Product_prices->_where = "barcode='" . $barcode . "'";
        $Count = $this->Product_prices->CountRecords();
        if ($Count > 0) {
            echo 0;
        } else {
            echo 1;
        }
    }
    public function product_variant($id)
    {

        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Product Variant";
        $this->viewData['module'] = "product/product_variant";
        $this->viewData['action'] = '1';
        $this->viewData['VIEW_STATUS'] = "1";
        $this->Product->_where = array("id" => $id);
        $this->viewData['productid'] =  $id;
        $this->viewData['productdata'] =  $this->Product->getRecordsByID();

        $this->load->model("Product_prices_model", "Product_prices");
        $productprices = $this->Product_prices->getProductpriceByProductID($id);

        foreach ($productprices as $price) {
            if ($price['barcode'] == '') {
                duplicate:
                $barcode = rand(1000000000, 9999999999);

                $this->Product_prices->_where = "barcode='" . $barcode . "'";
                $Count = $this->Product_prices->CountRecords();
                if ($Count > 0) {
                    goto duplicate;
                }
                $price['barcode'] = $barcode;
            }
            $productquantitypricesdata = $this->Product_prices->getProductQuantityPriceDataByPriceID($price['id']);

            $this->viewData['productprices'][] = array(
                "id" => $price['id'],
                "price" => $price['price'],
                "stock" => $price['stock'],
                "pointsforseller" => $price['pointsforseller'],
                "pointsforbuyer" => $price['pointsforbuyer'],
                "sku" => $price['sku'],
                "barcode" => $price['barcode'],
                "minimumorderqty" => $price['minimumorderqty'],
                "maximumorderqty" => $price['maximumorderqty'],
                "minimumstocklimit" => $price['minimumstocklimit'],
                "minimumsalesprice" => $price['minimumsalesprice'],
                "weight" => $price['weight'],
                "pricetype" => $price['pricetype'],
                "installationcharge" => $price['installationcharge'],
                "productquantitypricesdata" => $productquantitypricesdata
            );
        }

        $this->viewData['productcombination'] = array();
        $this->load->model("Product_combination_model", "Product_combination");
        $productcombination = $this->Product_combination->getProductcombinationByProductID($id);

        foreach ($productcombination as $pc) {
            $this->viewData['productcombination'][$pc['priceid']][] = array(
                "id" => $pc['id'],
                "priceid" => $pc['priceid'],
                "variantid" => $pc['variantid'],
                "attributeid" => $pc['attributeid']
            );
        }

        $this->load->model("Attribute_model", "Attribute");
        $this->viewData['attributedata'] = $this->Attribute->getActiveAttribute();
  
        $this->admin_headerlib->add_plugin("form-select2", "form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2", "form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("product", "pages/product_variant.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_product_variant()
    {

        $PostData = $this->input->post();
        // echo '<pre>';
        // print_r($PostData); 
        // exit;
        $this->load->model("Product_prices_model", "Product_prices");
        $this->load->model("Product_combination_model", "Product_combination");
        $this->load->model("Member_model", "Member");
        $this->load->model("Channel_model", "Channel");
        
        $this->Member->_table = tbl_membervariantprices;

        $insert_variant_arr = $update_variant_arr = $delete_variant_arr = $update_price = $priceid_arr = $final_delete_arr = $insert_member_product_variant_arr = $InsertMultiplePriceData = $UpdateMultiplePriceData = $UpdatedProductQuantityPrice = array();

        // foreach ($PostData['priceid'] as $k => $v) {
        //     if ($v == 0) {
        //         $Count = $this->Product->CheckProductSKUAvailable($PostData['sku'][$k]);
        //         if ($Count > 0) {
        //             echo json_encode(array("error" => 2, "index" => ($k + 1)));
        //             exit;
        //         }
        //     } else {
        //         $Count = $this->Product->CheckProductSKUAvailable($PostData['sku'][$k], $v);
        //         if ($Count > 0) {
        //             echo json_encode(array("error" => 2, "index" => ($k + 1)));
        //             exit;
        //         }
        //     }
        // }

        $productid = $PostData['productid'];
        $producttype = $PostData['producttype'];

      
        foreach ($PostData['priceid'] as $key => $row) {
            // echo '<pre>';
            // print_r($PostData);
            // exit;
            // $pricetype = $PostData['pricetype' . $key];
            if ($row == 0) {
              
                $insertpricedata = array(
                    "productid" => $PostData['productid'],
                    "stock" => $PostData['stock'][$key],
                    "pointsforseller" => $PostData['pointsforseller'][$key],
                    "pointsforbuyer" => $PostData['pointsforbuyer'][$key],
                    // "sku" => $PostData['sku'][$key],
                    "weight" => $PostData['weight'][$key],
                    "barcode" => $PostData['barcode'][$key],
                    "minimumorderqty" => $PostData['minimumorderqty'][$key],
                    "maximumorderqty" => $PostData['maximumorderqty'][$key],
                    "minimumstocklimit" => $PostData['minimumstocklimit'][$key],
                    // "pricetype" => $pricetype,
                    "minimumsalesprice" => $PostData['minimumsalesprice'][$key],
                );
                $row = $this->Product_prices->Add($insertpricedata);

                $insert_member_product_variant_arr[] = array("priceid" => $row, "price" => $PostData['price'][$key]);

                foreach ($PostData['variantid'][$key] as $variantkey => $variantrow) {
                    if (isset($PostData['availablevariantid'][$key][$variantkey])) {
                        $update_variant_arr[] = array("priceid" => $row, "variantid" => $variantrow, "id" => $PostData['availablevariantid'][$key][$variantkey]);
                        $delete_variant_arr[$row][] = $PostData['availablevariantid'][$key][$variantkey];
                    } else {
                        $insert_variant_arr[] = array("priceid" => $row, "variantid" => $variantrow);
                    }
                }

                if ($pricetype == 1) {
                    if (!empty($PostData['variantprice'][$key])) {
                        foreach ($PostData['variantprice'][$key] as $pricekey => $prices) {

                            if ($prices > 0 && $PostData['variantqty'][$key][$pricekey] > 0) {

                                $InsertMultiplePriceData[] = array(
                                    "productpricesid" => $row,
                                    "price" => $prices,
                                    "quantity" => $PostData['variantqty'][$key][$pricekey],
                                    "discount" => $PostData['variantdiscpercent'][$key][$pricekey]
                                );
                            }
                        }
                    }
                } else {
                    if ($PostData['price'][$key] > 0) {

                        $InsertMultiplePriceData[] = array(
                            "productpricesid" => $row,
                            "price" => $PostData['price'][$key],
                            "quantity" => 1,
                            "discount" => $PostData['discount'][$key],
                        );
                    }
                }
                $priceid_arr[] = $row;
            } else {
              
                foreach ($PostData['variantid'][$key] as $variantkey => $variantrow) {
                    if (isset($PostData['availablevariantid'][$key][$variantkey])) {
                        $update_variant_arr[] = array("priceid" => $row, "variantid" => $variantrow, "id" => $PostData['availablevariantid'][$key][$variantkey]);
                        $delete_variant_arr[$row][] = $PostData['availablevariantid'][$key][$variantkey];
                    } else {
                        $insert_variant_arr[] = array("priceid" => $row, "variantid" => $variantrow);
                    }
                }
               
                $update_price[] = array(
                    "id" => $row,
                    /* "price"=>$price, */
                    "price"=>$PostData['price'][$key],
                    "stock" => (int)$PostData['stock'][$key],
                    // "pointsforseller" => $PostData['pointsforseller'][$key],
                    // "pointsforbuyer" => $PostData['pointsforbuyer'][$key],
                    //"sku" => $PostData['sku'][$key],
                    "weight" => $PostData['weight'][$key],
                    // "barcode" => $PostData['barcode'][$key],
                    "minimumorderqty" => $PostData['minimumorderqty'][$key],
                    "maximumorderqty" => $PostData['maximumorderqty'][$key],
                    "minimumstocklimit" => $PostData['minimumstocklimit'][$key],
                    "installationcharge" => $PostData['installationcharge'][$key],
                    // "pricetype" => $pricetype,
                    // "minimumsalesprice" => $PostData['minimumsalesprice'][$key],
                );

                // if ($pricetype == 1) {
                //     if (!empty($PostData['variantprice'][$key])) {
                //         foreach ($PostData['variantprice'][$key] as $pricekey => $prices) {

                //             $productquantitypricesid = isset($PostData['productquantitypricesid'][$key][$pricekey]) ? $PostData['productquantitypricesid'][$key][$pricekey] : "";

                //             if ($prices > 0 && $PostData['variantqty'][$key][$pricekey] > 0) {

                //                 if (!empty($productquantitypricesid)) {

                //                     $UpdateMultiplePriceData[] = array(
                //                         "id" => $productquantitypricesid,
                //                         "price" => $prices,
                //                         "quantity" => $PostData['variantqty'][$key][$pricekey],
                //                         "discount" => $PostData['variantdiscpercent'][$key][$pricekey]
                //                     );

                //                     $UpdatedProductQuantityPrice[] = $productquantitypricesid;
                //                 } else {

                //                     $InsertMultiplePriceData[] = array(
                //                         "productpricesid" => $row,
                //                         "price" => $prices,
                //                         "quantity" => $PostData['variantqty'][$key][$pricekey],
                //                         "discount" => $PostData['variantdiscpercent'][$key][$pricekey]
                //                     );
                //                 }
                //             }
                //         }
                //     }
                // } else {
                //     $productquantitypricesid = !empty($PostData['singlequantitypricesid'][$key]) ? $PostData['singlequantitypricesid'][$key] : "";

                //     if ($PostData['price'][$key] > 0) {

                //         if (!empty($productquantitypricesid)) {

                //             $UpdateMultiplePriceData[] = array(
                //                 "id" => $productquantitypricesid,
                //                 "price" => $PostData['price'][$key],
                //                 "discount" => $PostData['discount'][$key],
                //             );

                //             $UpdatedProductQuantityPrice[] = $productquantitypricesid;
                //         } else {

                //             $InsertMultiplePriceData[] = array(
                //                 "productpricesid" => $row,
                //                 "price" => $PostData['price'][$key],
                //                 "quantity" => 1,
                //                 "discount" => $PostData['discount'][$key],
                //             );
                //         }
                //     }
                // }
                $priceid_arr[] = $row;
            }
        }

      
        // if (count($priceid_arr) > 0) {
        //     $deleteprices = $this->Product_prices->getProductprices(array("id not in(" . implode(",", $priceid_arr) . ")" => null, "productid" => $PostData['productid']));
        //     if (count($deleteprices) > 0) {
        //         foreach ($deleteprices as $dp) {
        //             $this->Product_combination->Delete(array("priceid" => $dp['id']));
        //             $final_delete_arr[] = $dp['id'];
        //         }
        //     }
        //     if (count($final_delete_arr) > 0) {
        //         $this->Product_prices->_table = tbl_productprices;
        //         $this->Product_prices->Delete(array("id in (" . implode(",", $final_delete_arr) . ")" => null));

        //         $this->Member->Delete(array("priceid in (" . implode(",", $final_delete_arr) . ")" => null));

        //         $this->Product_prices->_table = tbl_productquantityprices;
        //         $this->Product_prices->Delete(array("productpricesid IN (" . implode(",", $final_delete_arr) . ")" => null));
        //     }
        //     foreach ($priceid_arr as $priceid) {

        //         $priceqtydata = $this->Product_prices->getProductQuantityPriceDataByPriceID($priceid);
        //         if (!empty($priceqtydata)) {
        //             $priceqtyids = array_column($priceqtydata, "id");
        //             $resultId = array_diff($priceqtyids, $UpdatedProductQuantityPrice);

        //             if (!empty($resultId)) {
        //                 $this->Product_prices->_table = tbl_productquantityprices;
        //                 $this->Product_prices->Delete(array("id IN (" . implode(",", $resultId) . ")" => null));
        //             }
        //         }
        //     }
        // }
        // print_r($delete_variant_arr);exit();
        if (count($delete_variant_arr) > 0) {
            foreach ($delete_variant_arr as $dvk => $dv) {
                if (count($dv) > 0) {
                    $this->Product_combination->Delete(array("id not in(" . implode(",", $dv) . ")" => null, "priceid" => $dvk));
                }
            }
        }
        if (count($update_variant_arr) > 0) {
            $this->Product_combination->edit_batch($update_variant_arr, 'id');
        }
        if (count($insert_variant_arr) > 0) {
            $this->Product_combination->add_batch($insert_variant_arr);
        }

        if (!empty($InsertMultiplePriceData)) {
            $this->Product_prices->_table = tbl_productquantityprices;
            $this->Product_prices->add_batch($InsertMultiplePriceData);
        }

        if (!empty($update_price)) {
            $this->Product_prices->_table = tbl_productprices;
            $price = $this->Product_prices->edit_batch($update_price, 'id');

            if (!empty($UpdateMultiplePriceData)) {
                $this->Product_prices->_table = tbl_productquantityprices;
                $this->Product_prices->edit_batch($UpdateMultiplePriceData, 'id');
            }

            echo json_encode(array("error" => 1));
        } else {
            echo json_encode(array("error" => 0));
        }
        /* if((isset($combination) && $combination) || (isset($price) && $price) || (isset($updatecombination) || $updatecombination)){
            echo 1;
        }else{
            echo 0;
        } */
    }

    public function delete_mul_product()
    {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $this->Product->_table = tbl_productimage;
            $this->readdb->select('id,filename');
            $this->readdb->from($this->Product->_table);
            $this->readdb->where('productid', $row);
            $query1 = $this->readdb->get();
            $productimagedata = $query1->result_array();

            $this->Product->_table = tbl_productprices;
            $this->readdb->select('id');
            $this->readdb->from($this->Product->_table);
            $this->readdb->where('productid', $row);
            $query2 = $this->readdb->get();
            $productpricesdata = $query2->result_array();

            if (count($productimagedata) > 0) {
                $this->Product->_table = tbl_productimage;

                foreach ($productimagedata as $pi) {
                    unlinkfile('PRODUCT_PATH', $pi['filename'], PRODUCT_PATH);
                    $this->Product->Delete(array('id' => $pi['id']));
                }
            }
            if (count($productpricesdata) > 0) {
                $this->Product->_table = tbl_productcombination;
                $this->load->model("Product_prices_model", "Product_prices");

                foreach ($productpricesdata as $pd) {
                    $this->Product->Delete(array("priceid" => $pd['id']));

                    $this->Product_prices->_table = tbl_productprices;
                    $this->Product_prices->Delete(array("id" => $pd['id']));
                }
            }

            // $this->load->model("Related_product_model", "Related_product");
            // $this->Related_product->Delete(array('productid' => $row));

            $this->Product->_table = tbl_product;
            $this->Product->_fields = "name";
            $this->Product->_where = array("id" => $row);
            $productdata = $this->Product->getRecordsById();
            if ($this->viewData['submenuvisibility']['managelog'] == 1) {
                $this->general_model->addActionLog(3, 'Product', "Delete " . ucfirst($productdata['name']) . ' product.');
            }
            $this->Product->Delete(array("id" => $row));
        }
    }

    public function check_product_use()
    {
        $PostData = $this->input->post();
        $count = 0;
        $ids = explode(",", $PostData['ids']);
        // foreach ($ids as $row) {
        //     $this->readdb->select('productid');
        //     $this->readdb->from(tbl_orderproducts);
        //     $where = array("productid" => $row);
        //     $this->readdb->where($where);
        //     $query = $this->readdb->get();
        //     if ($query->num_rows() > 0) {
        //         $count++;
        //     }

        //     $this->readdb->select('productid');
        //     $this->readdb->from(tbl_cart);
        //     $where = array("productid" => $row);
        //     $this->readdb->where($where);
        //     $query = $this->readdb->get();
        //     if ($query->num_rows() > 0) {
        //         $count++;
        //     }

        //     $this->readdb->select('productid');
        //     $this->readdb->from(tbl_quotationproducts);
        //     $where = array("productid" => $row);
        //     $this->readdb->where($where);
        //     $query = $this->readdb->get();
        //     if ($query->num_rows() > 0) {
        //         $count++;
        //     }
        // }
        echo $count;
    }

    public function product_enable_disable()
    {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Product->_table = tbl_product;
        $this->Product->_where = array("id" => $PostData['id']);
        $this->Product->Edit($updatedata);

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->Product->_where = array("id" => $PostData['id']);
            $data = $this->Product->getRecordsById();
            $msg = ($PostData['val'] == 0 ? "Disable" : "Enable") . " " . $data['name'] . ' product.';
            $this->general_model->addActionLog(2, 'Product', $msg);
        }
        echo $PostData['id'];
    }

    function getcontentbyid()
    {
        $PostData = $this->input->post();

        $this->Product->_where = array("id" => $PostData['id']);
        $data =  $this->Product->getRecordsByID();

        echo json_encode(array('id' => $data['id'], 'productname' => $data['name'], 'productdescription' => $data['description'], 'productimage' => CATALOG_IMAGE . $data['image'], 'productpdffile' => CATALOG_IMAGE . $data['pdffile'], 'productcreateddate' => $this->general_model->displaydatetime($data['createddate']), 'productstatus' => $data['status']));
    }

    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }

    public function view_product($id)
    {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Product";
        $this->viewData['module'] = "product/view_product";
        $this->viewData['action'] = '1';

        $this->viewData['productdata'] =  $this->Product->getProductDataByID($id);
        $this->viewData['productid'] =  $id;
        $this->viewData['maincategorydata'] = $this->Product->getmaincategory();

        $this->viewData['productfile'] = $this->Product_file->getProductfilesByProductID($id);
        $this->load->model("Product_prices_model", "Product_prices");
        $this->viewData['productprices'] = array();
        $productprices = $this->Product_prices->getProductpriceByProductID($id);
        $this->viewData['productpricesdata'] = $this->Product_prices->getProductPricesByProductOrPriceId($id);
        /* foreach ($productprices as $v) {
            $this->viewData['productprices'][]=$v['price'];
        } */
        $this->viewData['productprices'] = array_column($productprices, 'price');
        $this->viewData['productstock'] = array_column($productprices, 'stock');

        $this->load->model('Stock_report_model', 'Stock');
        if (STOCK_MANAGE_BY == 0) {
            $productdata = $this->Stock->getAdminProductStock($id, 0);
            $stock = (!empty($productdata) ? $productdata[0]['openingstock'] : 0);

            $ProductStock = $this->Stock->getAdminProductStock($id, 1);
            $keynm = 'openingstock';
        } else {
            $stock ='';
            // $productdata = $this->Stock->getAdminProductFIFOStock($id, 0);
            // $stock = (!empty($productdata[0]['openingstock']) ? $productdata[0]['openingstock'] : 0);

            // $ProductStock = $this->Stock->getAdminProductFIFOStock($id, 1);
            // $keynm = 'openingstock';
        }
        $this->viewData['productdata']['universalstock'] = $stock;

        $this->load->model("Product_combination_model", "Product_combination");
        $this->viewData['productcombination'] = array();
        $productcombination = $this->Product_combination->getProductcombinationByProductIDWithValue($id);
 
        foreach ($productcombination as $pc) {
            $stock = 0;
            if (!empty($ProductStock)) {
                $key = array_search($pc['priceid'], array_column($ProductStock, 'priceid'));
                if (trim($key) != "" && isset($ProductStock[$key][$keynm])) {
                    $stock = (int)$ProductStock[$key][$keynm];
                }
            }
            $productpricesdata = $this->Product_prices->getProductPricesByProductOrPriceId($id, $pc['priceid']);

            if (number_format($productpricesdata['minprice'], 2, '.', '') == number_format($productpricesdata['maxprice'], 2, '.', '')) {
                $price = numberFormat($productpricesdata['minprice'], 2, ',');
            } else {
                $price = numberFormat($productpricesdata['minprice'], 2, ',') . " - " . numberFormat($productpricesdata['maxprice'], 2, ',');
            }

            $this->viewData['productcombination'][$pc['priceid']]['pricetype'] = $pc['pricetype'];
            $this->viewData['productcombination'][$pc['priceid']]['price'] = $price;
            $this->viewData['productcombination'][$pc['priceid']]['stock'] = $stock;
            $this->viewData['productcombination'][$pc['priceid']]['pointsforseller'] = (int)$pc['pointsforseller'];
            $this->viewData['productcombination'][$pc['priceid']]['pointsforbuyer'] = (int)$pc['pointsforbuyer'];
            $this->viewData['productcombination'][$pc['priceid']]['sku'] = $pc['sku'];
            $this->viewData['productcombination'][$pc['priceid']]['weight'] = $pc['weight'];
            $this->viewData['productcombination'][$pc['priceid']]['barcode'] = $pc['barcode'];

            $this->viewData['productcombination'][$pc['priceid']]['variants'][] = array("variantvalue" => $pc['variantvalue'], "variantname" => $pc['variantname']);

            $this->viewData['productcombination'][$pc['priceid']]['multipleprice'] = $this->Product_prices->getProductQuantityPriceDataByPriceID($pc['priceid']);
        }

        $this->load->model("Related_product_model", "Related_product");
        // $relatedproducts = $this->Related_product->getRelatedProducts($id);
        $relatedproducts = '';
        $this->viewData['relatedproducts'] = !empty($relatedproducts) ? implode(", ", array_column($relatedproducts, 'productname')) : "";

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Product', "View " . ucfirst($this->viewData['productdata']['name']) . ' product detail.');
        }


        $this->admin_headerlib->add_javascript_plugins("html5gallery", "html5gallery/html5gallery.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function qr_code()
    {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "QR Code";
        $this->viewData['module'] = "product/View_product_details";
        $this->viewData['action'] = '1';

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Product', 'View product QR code.');
        }

        $this->admin_headerlib->add_bottom_javascripts("view_product", "pages/view_product.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function printProductDetails()
    {
        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $productids = is_array($PostData['productid']) ? implode(",", $PostData['productid']) : $PostData['productid'];
        $PostData['productdata'] = $this->Product->getAllProductsDetail($productids);

        $html['content'] = $this->load->view(ADMINFOLDER . "product/product.php", $PostData, true);

        echo json_encode($html);
    }
    public function exportToPDFQRCode()
    {

        $this->checkAdminAccessModule('submenu', 'view', $this->viewData['submenuvisibility']);

        $productids = is_array($_REQUEST['productid']) ? implode(",", $_REQUEST['productid']) : $_REQUEST['productid'];
        $PostData['productname'] = $_REQUEST['productname'];
        $PostData['sku'] = $_REQUEST['sku'];
        $PostData['productprice'] = $_REQUEST['productprice'];
        $PostData['variant'] = $_REQUEST['variant'];

        $PostData['productdata'] = $this->Product->getAllProductsDetail($productids);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'product/Qrcodeformatforpdf', $PostData, true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Product-QR-Code.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage(
            '', // L - landscape, P - portrait 
            '',
            '',
            '',
            '',
            10, // margin_left
            10, // margin right
            40, // margin top
            15, // margin bottom
            3, // margin header
            10
        ); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL . 'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL . 'styles.css'); // external css
        $pdf->WriteHTML($stylesheet, 1);
        $pdf->WriteHTML($stylesheet2, 1);
        $pdf->SetHTMLHeader($header, '', true);
        $pdf->WriteHTML($html, 0);

        ob_start();
        ob_end_clean();

        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "I");

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product', 'Export to PDF product QR code.');
        }
    }

    public function getProductByChannelId()
    {

        $PostData = $this->input->post();
        $channelid = (!empty($PostData['channelid'])) ? implode(",", $PostData['channelid']) : "";
        $productdata = $this->Product->getProductByChannelId($channelid);

        echo json_encode($productdata);
    }

    public function getProductByCategoryId()
    {

        $PostData = $this->input->post();
        //$categoryid = $PostData['categoryid'];
        $memberid = $PostData['memberid'];
        $withvariantdata = (isset($PostData['withvariantdata'])) ? $PostData['withvariantdata'] : 1;

        $productdata = $this->Product->getProductByCategoryId($memberid, 0, 1, $withvariantdata);
        echo json_encode($productdata);
    }
    public function getAllProductByCategoryID()
    {

        $PostData = $this->input->post();
        $categoryid = $PostData['categoryid'];

        $productdata = $this->Product->getAllProductByCategoryID($categoryid);
        echo json_encode($productdata);
    }
    public function getVendorProducts()
    {

        $PostData = $this->input->post();
        $vendorid = $PostData['vendorid'];

        $productdata = $this->Product->getVendorProducts($vendorid);
        echo json_encode($productdata);
    }

    public function getProductByBrandid()
    {
        $PostData = $this->input->post();
        $brandid = $PostData['brandid'];

        $productdata = $this->Product->getProductByBrandId($brandid);
        echo json_encode($productdata);
    }
    public function getProductsByMultipleCategoryIds()
    {

        $PostData = $this->input->post();
        $categoryid = $PostData['categoryid'];
        $memberid = $PostData['memberid'];
        $channelid = $PostData['channelid'];

        $productdata = $this->Product->getProductsByMultipleCategoryIds($categoryid, $channelid, $memberid);
        echo json_encode($productdata);
    }
    public function getProductTaxById()
    {

        $PostData = $this->input->post();
        $productid = $PostData["productid"];
        //$priceid = $PostData["priceid"];
        //$ordertype = $PostData["ordertype"];
        $price = $PostData["price"];

        $this->load->model('Product_model', 'Product');
        $this->Product->_fields = "id,(SELECT integratedtax FROM " . tbl_hsncode . " WHERE id=hsncodeid) as tax";
        $this->Product->_where = array("id" => $productid);
        $producttax = $this->Product->getRecordsByID();
        //$productprice = $this->Member->getMemberProductPrice($productid,$memberid,$priceid);

        $taxamount = $price * $producttax['tax'] / (100 + $producttax['tax']);

        echo json_encode($taxamount, JSON_NUMERIC_CHECK);
    }
    public function getProductRewardpoints()
    {
        $PostData = $this->input->post();
        $productid = $PostData["productid"];
        $ordertype = $PostData["ordertype"];
        if ($ordertype == 1) {
            $memberid = $this->session->userdata(base_url() . 'MEMBERID');
        } else {
            $memberid = $PostData["memberid"];
        }

        $this->load->model('Product_model', 'Product');
        $productdata = $this->Product->getProductRewardpointsOrChannelSettings($productid, $memberid);

        echo json_encode($productdata);
    }
    public function exportproduct()
    {
        $this->load->model('Product_model', 'Product');
        $this->Product->exportproduct();
        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->load->model("Member_model", "Member");
            $PostData = $this->input->get();
            $memberDta = $this->Member->getMemberDataByID($PostData['memberid']);

            $module = ($memberDta['channelid'] == VENDORCHANNELID ? "Vendor" : Member_label);
            $this->general_model->addActionLog(0, $module, 'Export to excel ' . strtolower($module) . ' product ' . $memberDta['name'] . ' (' . $memberDta['membercode'] . ').');
        }
    }
    public function importproductprice()
    {
        $this->load->model('Product_model', 'Product');
        $this->Product->importproductprice();
    }
    public function importproduct()
    {
        $PostData = $this->input->post();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $createddate = $this->general_model->getCurrentDateTime();
        $totalrowcount = $totalinserted = 0;
        $FileNM = $ErrorFileNM = 0;
        
        
        if ($_FILES["attachment"]['name'] != '') {

            $FileNM = uploadFile('attachment', 'IMPORT_FILE', IMPORT_PATH, "ods|xl|xlc|xls|xlsx");
            if ($FileNM !== 0) {
                if ($FileNM == 2) {
                    $file_info_arr = array(
                        "channelid" => 0,
                        "memberid" => 0,
                        "type" => 1,
                        "file" => $FileNM,
                        "errorfile" => $ErrorFileNM,
                        "ipaddress" => $this->input->ip_address(),
                        "totalrow" => $totalrowcount,
                        "createddate" => $createddate,
                        "modifieddate" => $createddate,
                        "addedby" => $addedby,
                        "modifiedby" => $addedby
                    );
                    $this->load->model("Import_excel_model", "Import_excel");
                    $this->Import_excel->add($file_info_arr);
                    echo 3; //image not uploaded
                    exit;
                }
            } else {

               
                /* $file_info_arr = array(
                    "channelid" => 0,
                    "memberid" => 0,
                    "type" => 1,
                    "file" => $FileNM,
                    "errorfile" => $ErrorFileNM,
                    "ipaddress" => $this->input->ip_address(),
                    "totalrow" => $totalrowcount,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $this->load->model("Import_excel_model", "Import_excel");
                $this->Import_excel->add($file_info_arr);

                echo 2; //INVALID ATTACHMENT FILE
                exit; */
            }
            
            
            $insertproductdata = $insertproductimagedata = $insertproductimagewithiddata = $updateproductdata = $insertproductpricesdata = $insertproductpriceswithvariantdata = $updateproductpricesdata = $variantarr = $insertproductcombination = $removeproductcombination = $InsertMultiplePriceData = $insertMultiPriceDataWithVariant = $UpdateMultiplePriceData = $UpdatedProductQuantityPrice = $DB_Productquantityprice_ID = array();
            
            $file_data = $this->upload->data();
            $file_path =  IMPORT_PATH . $FileNM;

            
            $this->load->library('excel');
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);     //For excel 2003 
            //$objReader= PHPExcel_IOFactory::createReader('Excel2007');    // For excel 2007     
            //Set to read only
            $objReader->setReadDataOnly(true);

            //Load excel file
            $objPHPExcel = $objReader->load($file_path);

            $Worksheetname = $objPHPExcel->getSheetNames();

            if (!in_array('Product', $Worksheetname) || !in_array('Price', $Worksheetname)) {
                echo 6;
                // unlinkfile('', $FileNM, IMPORT_PATH);
                exit;
            }

            $ProductobjWorksheet = $objPHPExcel->getSheetByName('Product');
            $producttotalrows = $objPHPExcel->getSheetByName('Product')->getHighestRow();   //Count Number of rows avalable in excel

            $PriceobjWorksheet = $objPHPExcel->getSheetByName('Price');
            $pricetotalrows = $objPHPExcel->getSheetByName('Price')->getHighestRow();   //Count Number of rows avalable in excel

           
            $column0 = $ProductobjWorksheet->getCellByColumnAndRow(0, 1)->getValue();
            $column1 = $ProductobjWorksheet->getCellByColumnAndRow(1, 1)->getValue();
            $column2 = $ProductobjWorksheet->getCellByColumnAndRow(2, 1)->getValue();
            $column3 = $ProductobjWorksheet->getCellByColumnAndRow(3, 1)->getValue();
            $column4 = $ProductobjWorksheet->getCellByColumnAndRow(4, 1)->getValue();
            $column5 = $ProductobjWorksheet->getCellByColumnAndRow(5, 1)->getValue();
            $column6 = $ProductobjWorksheet->getCellByColumnAndRow(6, 1)->getValue();
            $column7 = $ProductobjWorksheet->getCellByColumnAndRow(7, 1)->getValue();
            $column8 = $ProductobjWorksheet->getCellByColumnAndRow(8, 1)->getValue();
            $column9 = $ProductobjWorksheet->getCellByColumnAndRow(9, 1)->getValue();
            $column10 = $ProductobjWorksheet->getCellByColumnAndRow(10, 1)->getValue();
            $column11 = $ProductobjWorksheet->getCellByColumnAndRow(11, 1)->getValue();
            $column12 = $ProductobjWorksheet->getCellByColumnAndRow(12, 1)->getValue();
            $column13 = $ProductobjWorksheet->getCellByColumnAndRow(13, 1)->getValue();
            $column14 = $ProductobjWorksheet->getCellByColumnAndRow(14, 1)->getValue();
            $column15 = $ProductobjWorksheet->getCellByColumnAndRow(15, 1)->getValue();
            $column16 = $ProductobjWorksheet->getCellByColumnAndRow(16, 1)->getValue();
            $column17 = $ProductobjWorksheet->getCellByColumnAndRow(17, 1)->getValue();

          

            if (
                $column0 == "Category Name *" && $column1 == "Product Name *" && $column2 == "Link" && $column3 == "Short Description" && $column4 == "Description *" && $column5 == "Hsn Code(%)" &&
                $column6 == "Priority *" && $column7 == "Brand" && $column8 == "Image" && $column9 == "Quantity Type (1=>Range Base,0=>Multiplication)" && $column10 == "Points For Seller" && $column11 == "Points For Buyer" && $column12 == "Tag" &&
                $column13 == "Product Display on Front or Not" && $column14 == "Activate (1=>Yes,0=>No)" && $column15 == "Meta Title" && $column16 == "Meta Keywords" && $column17 == "Meta Description"
            ) {
                if ($producttotalrows > 1) {

                
                    $column0 = $PriceobjWorksheet->getCellByColumnAndRow(0, 1)->getValue();
                    $column1 = $PriceobjWorksheet->getCellByColumnAndRow(1, 1)->getValue();
                    $column2 = $PriceobjWorksheet->getCellByColumnAndRow(2, 1)->getValue();
                    $column3 = $PriceobjWorksheet->getCellByColumnAndRow(3, 1)->getValue();
                    $column4 = $PriceobjWorksheet->getCellByColumnAndRow(4, 1)->getValue();
                    $column5 = $PriceobjWorksheet->getCellByColumnAndRow(5, 1)->getValue();
                    $column6 = $PriceobjWorksheet->getCellByColumnAndRow(6, 1)->getValue();
                    $column7 = $PriceobjWorksheet->getCellByColumnAndRow(7, 1)->getValue();
                    $column8 = $PriceobjWorksheet->getCellByColumnAndRow(8, 1)->getValue();
                    $column9 = $PriceobjWorksheet->getCellByColumnAndRow(9, 1)->getValue();
                    $column10 = $PriceobjWorksheet->getCellByColumnAndRow(10, 1)->getValue();

                    $column11 = $PriceobjWorksheet->getCellByColumnAndRow(11, 1)->getValue();
                    $column12 = $PriceobjWorksheet->getCellByColumnAndRow(12, 1)->getValue();
                    $column13 = $PriceobjWorksheet->getCellByColumnAndRow(13, 1)->getValue();
                    $column14 = $PriceobjWorksheet->getCellByColumnAndRow(14, 1)->getValue();
                    $column15 = $PriceobjWorksheet->getCellByColumnAndRow(15, 1)->getValue();
                    $column16 = $PriceobjWorksheet->getCellByColumnAndRow(16, 1)->getValue();
                    $column17 = $PriceobjWorksheet->getCellByColumnAndRow(17, 1)->getValue();
                    $column18 = $PriceobjWorksheet->getCellByColumnAndRow(18, 1)->getValue();
                    $column19 = $PriceobjWorksheet->getCellByColumnAndRow(19, 1)->getValue();
                    $column20 = $PriceobjWorksheet->getCellByColumnAndRow(20, 1)->getValue();

                    $column21 = $PriceobjWorksheet->getCellByColumnAndRow(21, 1)->getValue();
                    $column22 = $PriceobjWorksheet->getCellByColumnAndRow(22, 1)->getValue();
                    $column23 = $PriceobjWorksheet->getCellByColumnAndRow(23, 1)->getValue();
                    $column24 = $PriceobjWorksheet->getCellByColumnAndRow(24, 1)->getValue();
                    $column25 = $PriceobjWorksheet->getCellByColumnAndRow(25, 1)->getValue();
                    $column26 = $PriceobjWorksheet->getCellByColumnAndRow(26, 1)->getValue();
                    $column27 = $PriceobjWorksheet->getCellByColumnAndRow(27, 1)->getValue();
                    $column28 = $PriceobjWorksheet->getCellByColumnAndRow(28, 1)->getValue();
                    $column29 = $PriceobjWorksheet->getCellByColumnAndRow(29, 1)->getValue();
                    $column30 = $PriceobjWorksheet->getCellByColumnAndRow(30, 1)->getValue();

                    $column31 = $PriceobjWorksheet->getCellByColumnAndRow(31, 1)->getValue();
                    $column32 = $PriceobjWorksheet->getCellByColumnAndRow(32, 1)->getValue();
                    $column33 = $PriceobjWorksheet->getCellByColumnAndRow(33, 1)->getValue();
                    $column34 = $PriceobjWorksheet->getCellByColumnAndRow(34, 1)->getValue();
                    $column35 = $PriceobjWorksheet->getCellByColumnAndRow(35, 1)->getValue();
                    $column36 = $PriceobjWorksheet->getCellByColumnAndRow(36, 1)->getValue();
                    $column37 = $PriceobjWorksheet->getCellByColumnAndRow(37, 1)->getValue();
                    $column38 = $PriceobjWorksheet->getCellByColumnAndRow(38, 1)->getValue();
                    $column39 = $PriceobjWorksheet->getCellByColumnAndRow(39, 1)->getValue();
                    $column40 = $PriceobjWorksheet->getCellByColumnAndRow(40, 1)->getValue();

                    $column41 = $PriceobjWorksheet->getCellByColumnAndRow(41, 1)->getValue();

                    
                    if (
                        $column0 == "Product Code" && $column1 == "Product Name *" && $column2 == "Variant" &&
                        $column3 == "Stock" && $column4 == "SKU *" && $column5 == "Barcode" &&
                        $column6 == "Minimum Order Quantity" && $column7 == "Maximum Order Quantity" &&$column8 == "Minimum Stock Limit" && $column9 == "Weight (kg)" &&
                        $column10 == "Add Price In Price List (1=>Yes, 0=>No)" &&
                        $column11 == "Price Type (0=>Single Quantity, 1=>Multiple Quantity)" &&
                        $column12 == "Price 1" &&
                        $column13 == "Quantity 1" &&
                        $column14 == "Discount 1 (%)" &&
                        $column15 == "Price 2" && $column16 == "Quantity 2" && $column17 == "Discount 2 (%)" && 
                        $column18 == "Price 3" && $column19 == "Quantity 3" && $column20 == "Discount 3 (%)" &&
                        $column21 == "Price 4" && $column22 == "Quantity 4" && $column23 == "Discount 4 (%)" &&
                        $column24 == "Price 5" && $column25 == "Quantity 5" && $column26 == "Discount 5 (%)" &&
                        $column27 == "Price 6" && $column28 == "Quantity 6" && $column29 == "Discount 6 (%)" &&
                        $column30 == "Price 7" && $column31 == "Quantity 7" && $column32 == "Discount 7 (%)" &&
                        $column33 == "Price 8" && $column34 == "Quantity 8" && $column35 == "Discount 8 (%)" &&
                        $column36 == "Price 9" && $column37 == "Quantity 9" && $column38 == "Discount 9 (%)" &&
                        $column39 == "Price 10" && $column40 == "Quantity 10" && $column41 == "Discount 10 (%)"

                    ) {
                        
              
                        
                        $error = array();
                        $ProductSheeterror = array();
                        $PriceSheeterror = array();
                        $NewProductSheeterror = array();
                        $NewPriceSheeterror = array();

                        $this->load->model('Member_model', 'Member');
                        $this->load->model('Product_model', 'Product');
                        $this->load->model('Category_model', 'Category');
                        $this->load->model('Hsn_code_model', 'Hsn_code');
                        $this->load->model('Variant_model', 'Variant');
                        $this->load->model('Price_list_model', 'Price_list');
                        $this->load->model('Product_combination_model', 'Product_combination');
                        $this->load->model('Product_prices_model', 'Product_prices');
                        $this->load->model('Brand_model', 'Brand');
                        $this->load->model('Attribute_model', 'Attribute');
                        $this->load->model('Product_tag_model', 'Product_tag');


                        $productcount = $this->Product->getProductCount();

                        $this->Product->_fields = "id,name";
                        $productdata = $this->Product->getRecordByID();
                        $productidarr = array_column($productdata, 'id');
                        $productnamearr = array_column($productdata, 'name');

                        $this->Category->_fields = "id,name";
                        $this->Category->_order = "id";
                        $categorydata = $this->Category->getRecordByID();
                        $categoryidarr = array_column($categorydata, 'id');
                        $categorynamearr = array_column($categorydata, 'name');

                        $this->Product_prices->_fields = "id,productid,CONCAT(productid,'|',id) as name";
                        $this->Product_prices->_order = 'id';
                        $productpricedata = $this->Product_prices->getRecordByID();
                        $productpriceidarr = array_column($productpricedata, 'id');
                        $productpricenamearr = array_column($productpricedata, 'name');
                        $productpriceproductidarr = array_column($productpricedata, 'productid');

                        $this->Price_list->_fields = "productid,filename";
                        $this->Price_list->_table = tbl_productimage;
                        $this->Price_list->_order = 'id';
                        $productimagedata = $this->Price_list->getRecordByID();
                        $productimagedata = array_column($productimagedata, 'filename');

                        $this->Product_combination->_fields = "id,priceid,variantid,CONCAT(priceid,'|',variantid) as name";
                        $productcombinationdata = $this->Product_combination->getRecordByID();
                        $productcombinationnamearr = array_column($productcombinationdata, 'name');

                        $productcombinationgroupdata = $this->Product_combination->getProductCombinationGroupByPriceID();
                        $productcombinationgrouppriceidarr = array_column($productcombinationgroupdata, 'priceid');
                        $productcombinationgroupvariantarr = array_column($productcombinationgroupdata, 'variant');

                        $this->Hsn_code->_fields = "id,hsncode";
                        $hsncodedata = $this->Hsn_code->getRecordByID();
                        $hsncodeidarr = array_column($hsncodedata, 'id');
                        $hsncodearr = array_column($hsncodedata, 'hsncode');

                        $variantdata = $this->Variant->getVariantDataForImport();
                        $variantidarr = array_column($variantdata, 'id');
                        $variantnamearr = array_column($variantdata, 'variantname');

                        $producttotalrowserrorcoount = 2;
                        $pricetotalrowserrorcoount = 2;

                        $getvariants = $noofproductimport = 0;
                      
                        for ($i = 2; $i <= $producttotalrows; $i++) {
                           
                            $createddate = $this->general_model->getCurrentDateTime();

                            $category = trim($ProductobjWorksheet->getCellByColumnAndRow(0, $i)->getValue());
                            $productname = trim($ProductobjWorksheet->getCellByColumnAndRow(1, ($i+1))->getValue());
                            $slug = trim($ProductobjWorksheet->getCellByColumnAndRow(2, $i)->getValue());
                            $shortdescription = trim($ProductobjWorksheet->getCellByColumnAndRow(3, $i)->getValue());
                            $shortdescription = (!empty($shortdescription)) ? $shortdescription : '';
                            $description = trim($ProductobjWorksheet->getCellByColumnAndRow(4, $i)->getValue());
                            $hsncode = trim($ProductobjWorksheet->getCellByColumnAndRow(5, $i)->getValue());

                            $priority = trim($ProductobjWorksheet->getCellByColumnAndRow(6, $i)->getValue());
                            $priority = (!empty($priority)) ? $priority : 0;

                            $brand = trim($ProductobjWorksheet->getCellByColumnAndRow(7, $i)->getValue());
                            $image = trim($ProductobjWorksheet->getCellByColumnAndRow(8, $i)->getValue());

                            $quantitytype = trim($ProductobjWorksheet->getCellByColumnAndRow(9, $i)->getValue());
                            $quantitytype = (!empty($quantitytype)) ? $quantitytype : 0;

                            $pointsforseller = trim($ProductobjWorksheet->getCellByColumnAndRow(10, $i)->getValue());
                            $pointsforseller = (!empty($pointsforseller)) ? $pointsforseller : 0;

                            $pointsforbuyer = trim($ProductobjWorksheet->getCellByColumnAndRow(11, $i)->getValue());
                            $pointsforbuyer = (!empty($pointsforbuyer)) ? $pointsforbuyer : 0;

                            $tag = trim($ProductobjWorksheet->getCellByColumnAndRow(12, $i)->getValue());
                            $tag = (!empty($tag)) ? $tag : '';

                            $productdisplayonfront = trim($ProductobjWorksheet->getCellByColumnAndRow(13, $i)->getValue());
                            $productdisplayonfront = (!empty($productdisplayonfront)) ? $productdisplayonfront : 0;

                            $status = trim($ProductobjWorksheet->getCellByColumnAndRow(14, $i)->getValue());
                            $status = (!empty($status)) ? $status : 0;

                            $metatitle = trim($ProductobjWorksheet->getCellByColumnAndRow(15, $i)->getValue());
                            $metatitle = (!empty($metatitle)) ? $metatitle : '';

                            $metakeyword = trim($ProductobjWorksheet->getCellByColumnAndRow(16, $i)->getValue());
                            $metakeyword = (!empty($metakeyword)) ? $metakeyword : '';

                            $metadescription = trim($ProductobjWorksheet->getCellByColumnAndRow(17, $i)->getValue());
                            $metadescription = (!empty($metadescription)) ? $metadescription : '';
                            
                            // $ProductnamePricesheetArr = $PriceobjWorksheet->getColumnDimensions("B2:B".$pricetotalrows,null,true,true,true);

                            // Price Sheet Array 
                            $ProductCodePricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(0,'2',$producttotalrows+1);
                            $ProductnamePricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(1,'2',$producttotalrows+1);
                            $VariantnamePricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(2,'2',$producttotalrows+1);

                            $stockPricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(3,'2',$producttotalrows+1);
                            $stock = (!empty($stockPricesheetArr)) ? $stockPricesheetArr : 0;

                            $skuPricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(4,'2',$producttotalrows+1);
                            $sku = (!empty($skuPricesheetArr)) ? $skuPricesheetArr : '';

                            $barcodePricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(5,'2',$producttotalrows+1);
                            $barcode = (!empty($barcodePricesheetArr)) ? $barcodePricesheetArr : '';

                            $minimumorderqtyPricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(6,'2',$producttotalrows+1);
                            $minimumorderqty = (!empty($minimumorderqtyPricesheetArr)) ? $minimumorderqtyPricesheetArr : 0;

                            $maximumorderqtyPricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(7,'2',$producttotalrows+1);
                            $maximumorderqty = (!empty($maximumorderqtyPricesheetArr)) ? $maximumorderqtyPricesheetArr : 0;

                            $minimumstocklimitPricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(8,'2',$producttotalrows+1);
                            $minimumstocklimit = (!empty($minimumstocklimitPricesheetArr)) ? $minimumstocklimitPricesheetArr : 0;

                            $weightPricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(9,'2',$producttotalrows+1);
                            $weight = (!empty($weightPricesheetArr)) ? $weightPricesheetArr : 0;

                            $addpriceinpricelistPricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(10,'2',$producttotalrows+1);
                            $addpriceinpricelist = (!empty($addpriceinpricelistPricesheetArr)) ? $addpriceinpricelistPricesheetArr : 0;

                            $pricetypePricesheetArr = $PriceobjWorksheet->SingleColumnDataArray(11,'2',$producttotalrows+1);
                            $pricetype = (!empty($pricetypePricesheetArr)) ? $pricetypePricesheetArr : 0;

                            $PricesArrayPricesheetArr = $QuantityArrayPricesheetArr = $DiscountArrayPricesheetArr = array();

                           
                            $PricesArrayPricesheetArr['1'] = $PriceobjWorksheet->SingleColumnDataArray(12,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['1'] = $PriceobjWorksheet->SingleColumnDataArray(13,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['1'] = $PriceobjWorksheet->SingleColumnDataArray(14,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['2'] = $PriceobjWorksheet->SingleColumnDataArray(15,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['2'] = $PriceobjWorksheet->SingleColumnDataArray(16,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['2'] = $PriceobjWorksheet->SingleColumnDataArray(17,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['3'] = $PriceobjWorksheet->SingleColumnDataArray(18,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['3'] = $PriceobjWorksheet->SingleColumnDataArray(19,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['3'] = $PriceobjWorksheet->SingleColumnDataArray(20,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['4'] = $PriceobjWorksheet->SingleColumnDataArray(21,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['4'] = $PriceobjWorksheet->SingleColumnDataArray(22,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['4'] = $PriceobjWorksheet->SingleColumnDataArray(23,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['5'] = $PriceobjWorksheet->SingleColumnDataArray(24,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['5'] = $PriceobjWorksheet->SingleColumnDataArray(25,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['5'] = $PriceobjWorksheet->SingleColumnDataArray(26,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['6'] = $PriceobjWorksheet->SingleColumnDataArray(27,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['6'] = $PriceobjWorksheet->SingleColumnDataArray(28,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['6'] = $PriceobjWorksheet->SingleColumnDataArray(29,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['7'] = $PriceobjWorksheet->SingleColumnDataArray(30,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['7'] = $PriceobjWorksheet->SingleColumnDataArray(31,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['7'] = $PriceobjWorksheet->SingleColumnDataArray(32,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['8'] = $PriceobjWorksheet->SingleColumnDataArray(33,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['8'] = $PriceobjWorksheet->SingleColumnDataArray(34,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['8'] = $PriceobjWorksheet->SingleColumnDataArray(35,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['9'] = $PriceobjWorksheet->SingleColumnDataArray(36,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['9'] = $PriceobjWorksheet->SingleColumnDataArray(37,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['9'] = $PriceobjWorksheet->SingleColumnDataArray(38,'2',$producttotalrows+1);

                            $PricesArrayPricesheetArr['10'] = $PriceobjWorksheet->SingleColumnDataArray(39,'2',$producttotalrows+1);
                            $QuantityArrayPricesheetArr['10'] = $PriceobjWorksheet->SingleColumnDataArray(40,'2',$producttotalrows+1);
                            $DiscountArrayPricesheetArr['10'] = $PriceobjWorksheet->SingleColumnDataArray(41,'2',$producttotalrows+1);
                                
                          
                           
                            if(in_array($productname,$ProductnamePricesheetArr)){
                                $productdatafromprice = array_keys($ProductnamePricesheetArr,$productname);
                                $updateIsUniversal = $updateIsUniversalwithiddata = $barcodearray = $skuarray = array();
                                echo '<pre>';
                               
                                for ($pp = 0; $pp < count($productdatafromprice); $pp++) {

                                    // print_r($pp);
                                    // echo '<pre>';
                                    // echo '<br>';
                                    // print_r($productdatafromprice);
                                   
                                    $ProductCodePricesheet = $ProductCodePricesheetArr[$productdatafromprice[$pp]];
                                    $ProductnamePricesheet = $ProductnamePricesheetArr[$productdatafromprice[$pp]];
                                    $VariantnamePricesheet = $VariantnamePricesheetArr[$productdatafromprice[$pp]];
                                    $stockPricesheet = $stock[$productdatafromprice[$pp]];
                                    $skuPricesheet = $sku[$productdatafromprice[$pp]];
                                    $barcodePricesheet = $barcode[$productdatafromprice[$pp]];
                                    $minimumorderqtyPricesheet = $minimumorderqty[$productdatafromprice[$pp]];
                                    $maximumorderqtyPricesheet = $maximumorderqty[$productdatafromprice[$pp]];
                                    $minimumstocklimitPricesheet = $minimumstocklimit[$productdatafromprice[$pp]];
                                    $weightPricesheet = $weight[$productdatafromprice[$pp]];
                                    $addpriceinpricelistPricesheet = $addpriceinpricelist[$productdatafromprice[$pp]];
                                    $pricetypePricesheet = $pricetype[$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet = $QuantityArrayPricesheet = $DiscountArrayPricesheet = array();

                                    $PricesArrayPricesheet['1']=$PricesArrayPricesheetArr['1'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['1']=$QuantityArrayPricesheetArr['1'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['1']=$DiscountArrayPricesheetArr['1'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['2']=$PricesArrayPricesheetArr['2'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['2']=$QuantityArrayPricesheetArr['2'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['2']=$DiscountArrayPricesheetArr['2'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['3']=$PricesArrayPricesheetArr['3'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['3']=$QuantityArrayPricesheetArr['3'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['3']=$DiscountArrayPricesheetArr['3'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['4']=$PricesArrayPricesheetArr['4'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['4']=$QuantityArrayPricesheetArr['4'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['4']=$DiscountArrayPricesheetArr['4'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['5']=$PricesArrayPricesheetArr['5'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['5']=$QuantityArrayPricesheetArr['5'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['5']=$DiscountArrayPricesheetArr['5'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['6']=$PricesArrayPricesheetArr['6'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['6']=$QuantityArrayPricesheetArr['6'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['6']=$DiscountArrayPricesheetArr['6'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['7']=$PricesArrayPricesheetArr['7'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['7']=$QuantityArrayPricesheetArr['7'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['7']=$DiscountArrayPricesheetArr['7'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['8']=$PricesArrayPricesheetArr['8'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['8']=$QuantityArrayPricesheetArr['8'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['8']=$DiscountArrayPricesheetArr['8'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['9']=$PricesArrayPricesheetArr['9'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['9']=$QuantityArrayPricesheetArr['9'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['9']=$DiscountArrayPricesheetArr['9'][$productdatafromprice[$pp]];

                                    $PricesArrayPricesheet['10']=$PricesArrayPricesheetArr['10'][$productdatafromprice[$pp]];
                                    $QuantityArrayPricesheet['10']=$QuantityArrayPricesheetArr['10'][$productdatafromprice[$pp]];
                                    $DiscountArrayPricesheet['10']=$DiscountArrayPricesheetArr['10'][$productdatafromprice[$pp]];

                                    
                                    if (!empty($ProductCodePricesheet) || !empty($ProductnamePricesheet) || !empty($VariantnamePricesheet) || !empty($stockPricesheet) || !empty($skuPricesheet) || !empty($barcodePricesheet)) {

                                        if(empty($ProductnamePricesheet)){
                                            $PriceSheeterror[] = "Row no. " . $i . " product name is empty !";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }

                                        if(empty($skuPricesheet)){
                                            $PriceSheeterror[] = "Row no. ".$i." product SKU is empty !";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }else{
                                            if (!empty($ProductCodePricesheet) && in_array($ProductCodePricesheet, $productpricenamearr)) {
                                                $productpriceid = $productpriceidarr[array_search($ProductCodePricesheet, $productpricenamearr)];
                                                $this->Product_prices->_where = "id<>'".$productpriceid."' AND sku='".$skuPricesheet."'";
                                            }else{
                                                $this->Product_prices->_where = "sku='".$skuPricesheet."'";
                                            }
                                            $Count = $this->Product_prices->CountRecords();
                                            if($Count > 0  || in_array($skuPricesheet, $skuarray)){
                                                $PriceSheeterror[] = "Row no. ".$i." product SKU is already exist !";
                                                $isvalid = 0;
                                                $error[] = $i;
                                            }
                                        }

                                        if(empty($barcodePricesheet)){
                                            duplicates : $barcodePricesheet = rand(1000000000,9999999999);
                                            $this->Product_prices->_where = "barcode='".$barcodePricesheet."'";
                                            $Count = $this->Product_prices->CountRecords();
                                            if($Count > 0 || in_array($barcodePricesheet, $barcodearray)){
                                                goto duplicates;
                                            }
                                        }else{
                                            $pattern = '/^[a-zA-Z0-9]+$/';
                                            if (!preg_match($pattern, $barcodePricesheet)){
                                                $PriceSheeterror[] = "Row no. ".$i." product barcode is allow only alphabetic & numeric characters !";
                                                $isvalid = 0;
                                                $error[] = $i;   
                                            }elseif(strlen($barcodePricesheet)>30){
                                                $PriceSheeterror[] = "Row no. ".$i." product barcode is required maximum 30 characters !";
                                                $isvalid = 0;
                                                $error[] = $i;   
                                            }else{
                                                if (!empty($ProductCodePricesheet) && in_array($ProductCodePricesheet, $productpricenamearr)) {
                                                    $productpriceid = $productpriceidarr[array_search($ProductCodePricesheet, $productpricenamearr)];
                                                    $this->Product_prices->_where = "id<>'".$productpriceid."' AND barcode='".$ProductCodePricesheet."'";
                                                }else{
                                                    $this->Product_prices->_where = "barcode='".$ProductCodePricesheet."'";
                                                }
                                                $Count = $this->Product_prices->CountRecords();
                                                if($Count > 0  || in_array($ProductCodePricesheet, $barcodearray)){
                                                    $PriceSheeterror[] = "Row no. ".$i." product barcode is already exist !";
                                                    $isvalid = 0;
                                                    $error[] = $i;
                                                }
                                            }
                                        }
                                        if ($PricesArrayPricesheet[1] == "" || $PricesArrayPricesheet[1] == 0) {
                                            $PriceSheeterror[] = "Row no. " . $i . " product price 1 is empty !";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                        if ($QuantityArrayPricesheet[1] == "" || $QuantityArrayPricesheet[1] == 0) {
                                            $PriceSheeterror[] = "Row no. " . $i . " product quantity 1 is empty !";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                    }
                                    



                                }

                               
                            }

                         
                          
                            // pre($productdatafromprice);
                            $isvalid = 1;
                          
                          
                            if (empty($category)) {
                                // echo "Row no. " . $i . " product category is empty !";
                                $ProductSheeterror[] = "Row no. " . $i . " product category is empty !";
                                $isvalid = 0;
                                $error[] = $i;
                            }
                            //print_r(explode("(",$hsncode));
                            //$tax = substr($hsncode, strpos($hsncode, "(") + 1,1); 
                            // $code = substr($hsncode, 0, strpos($hsncode, '('));
                            $code = explode("(", $hsncode);
                            if (!in_array($code[0], $hsncodearr) && isset($code[1])) {
                                $tax =  preg_replace("/[^0-9]{1,4}/", '', $code[1]); //exit;

                                /* echo "Row no. ".$i." product hsncode not found !";
                                $isvalid = 0;
                                $error[] = $i; */




                                $ADD = $this->Hsn_code->Add(array(
                                    "hsncode" => $code[0],
                                    "integratedtax" => $tax,
                                    "status" => 1,
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby
                                ));

                                //echo $ADD;

                                $this->Hsn_code->_fields = "id,hsncode";
                                $hsncodedata = $this->Hsn_code->getRecordByID();
                                $hsncodeidarr = array_column($hsncodedata, 'id');
                                $hsncodearr = array_column($hsncodedata, 'hsncode');
                            }
                            if (empty($productname)) {
                                // echo "Row no. " . $i . " product name is empty !";
                                $ProductSheeterror[] = "Row no. " . $i . " product name is empty !";
                                $isvalid = 0;
                                $error[] = $i;
                            }
                            if (empty($description)) {
                                // echo "Row no. " . $i . " product description is empty !";
                                $ProductSheeterror[] = "Row no. " . $i . " product description is empty !";
                                $isvalid = 0;
                                $error[] = $i;
                            }
                            if (!empty($tag)) {
                                $checkproducttag = explode(",", $tag);
                                $isvalidtag = 1;
                                foreach ($checkproducttag as $tagvalue) {
                                    $pattern = '/[\'^£$%@#~?><>,|=]/';
                                    if (preg_match($pattern, $tagvalue)) {
                                        $isvalidtag = 0;
                                    }
                                }
                                if ($isvalidtag == 0) {
                                    // echo "Row no. " . $i . " product tag is not valid !";
                                    $ProductSheeterror[] = "Row no. " . $i . " product tag is not valid !";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                            }

                            
                            
                            if ($isvalid) {
                                if (empty($slug)) {
                                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $productname)));
                                }
                                $categoryid = $hsncodeid = $brandid = 0;
                                if ($category != "") {
                                    if (in_array($category, $categorynamearr)) {
                                        $categoryid = $categoryidarr[array_search($category, $categorynamearr)];
                                    } else {
                                        $this->Category->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
                                        $this->Category->_where = array("maincategoryid" => 0);
                                        $categorydata = $this->Category->getRecordsById();

                                        $maxpriority = (!empty($categorydata)) ? $categorydata['maxpriority'] : 1;
                                        $categoryslug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $category)));
                                        $InsertData = array(
                                            'name' => $category,
                                            'slug' => $categoryslug,
                                            'priority' => $maxpriority,
                                            'status' => 1,
                                            'createddate' => $createddate,
                                            'addedby' => $addedby,
                                            'modifieddate' => $createddate,
                                            'modifiedby' => $addedby
                                        );

                                        $categoryid = $this->Category->Add($InsertData);

                                        $this->Category->_fields = "id,name";
                                        $this->Category->_order = "id";
                                        $categorydata = $this->Category->getRecordByID();
                                        $categoryidarr = array_column($categorydata, 'id');
                                        $categorynamearr = array_column($categorydata, 'name');
                                    }
                                }
                                $code = explode("(", $hsncode);
                                if (in_array($code[0], $hsncodearr) && isset($code[1])) {
                                    $tax =  preg_replace("/[^0-9]{1,4}/", '', $code[1]);
                                    $hsncodeid = $hsncodeidarr[array_search($code[0], $hsncodearr)];
                                }
                                if ($brand != "") {
                                    $branddata = $this->Brand->getAllBrands();
                                    $brandidarr = $brandnamearr = array();
                                    if (!empty($branddata)) {
                                        $brandidarr = array_column($branddata, 'id');
                                        $brandnamearr = array_column($branddata, 'name');
                                    }
                                    if (in_array($brand, $brandnamearr)) {
                                        $brandid = $brandidarr[array_search($brand, $brandnamearr)];
                                    } else {
                                        $this->Brand->_where = array();
                                        $this->Brand->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
                                        $branddata = $this->Brand->getRecordsById();

                                        $maxpriority = (!empty($branddata)) ? $branddata['maxpriority'] : 1;

                                        $InsertData = array(
                                            'name' => $brand,
                                            'priority' => $maxpriority,
                                            'status' => 1,
                                            'createddate' => $createddate,
                                            'addedby' => $addedby,
                                            'modifieddate' => $createddate,
                                            'modifiedby' => $addedby
                                        );

                                        $brandid = $this->Brand->Add($InsertData);
                                    }
                                }

                                // $this->Product->_where = array('(name="'.$productname.'" OR slug="'.$slug.'")';
                                $this->Product->_where = array('name' => $productname, "slug" => $slug);
                                $productData = $this->Product->getRecordsByID();


                               
                                if (!empty($productData)) {

                        

                                    $productid = $productData['id'];

                                    $UpdateData = array(
                                        'categoryid' => $categoryid,
                                        'name' => $productname,
                                        'brandid' => $brandid,
                                        'slug' => $slug,
                                        'shortdescription' => $shortdescription,
                                        'description' => $description,
                                        'hsncodeid' => $hsncodeid,
                                        'metatitle' => $metatitle,
                                        'metadescription' => $metadescription,
                                        'metakeyword' => $metakeyword,
                                        'priority' => $priority,
                                        'quantitytype' => $quantitytype,
                                        'status' => $status,
                                        'productdisplayonfront' => $productdisplayonfront,
                                        'modifeddate' => $createddate,
                                        'modififedby' => $addedby,
                                        'id' => $productid
                                    );
                                    if (REWARDSPOINTS == 1) {
                                        $UpdateData['pointsforseller'] = $pointsforseller;
                                        $UpdateData['pointsforbuyer'] = $pointsforbuyer;
                                    }
                                    $updateproductdata[] = $UpdateData;

                                    if (!empty($image)) {
                                        $imagearr = explode('|', $image);
                                        foreach ($imagearr as $imagerow) {
                                            $productimage = trim($imagerow);
                                            if (filter_var($productimage, FILTER_VALIDATE_URL)) {
                                                $productimage = $this->Product->saveimagefromurl($productimage);
                                            }
                                            if (!in_array($productimage, $productimagedata)) {

                                                $insertproductimagewithiddata[] = array(
                                                    'filename' => $productimage,
                                                    'type' => 1,
                                                    'productid' => $productid
                                                );
                                            }
                                        }
                                    }


                                    $tagidarray = array();
                                    if (!empty($tag)) {
                                        $this->Product_tag->_table = tbl_producttag;
                                        $tagid = $this->Product_tag->addMultipleProducttag(array_map('trim', explode(",", $tag)), 'notallow');
                                        $tagidarray = array_unique($tagid);

                                        if (!empty($tagidarray)) {
                                            foreach ($tagidarray as $tagid) {
                                                $this->Product_tag->_table = tbl_producttagmapping;
                                                $this->Product_tag->_where = array('productid' => $productid, 'tagid' => $tagid);
                                                $Count = $this->Product_tag->CountRecords();
                                                if ($Count == 0) {
                                                    $InsertTagMappingwithiddata[] = array("productid" => $productid, "tagid" => $tagid);
                                                }
                                            }
                                        }
                                    }
                                    $this->Product_tag->_table = tbl_producttagmapping;
                                    $this->Product_tag->_fields = "tagid";
                                    $this->Product_tag->_where = array('productid' => $productid);
                                    $producttagdata = $this->Product_tag->getRecordById();
                                    $producttagmappingids = (!empty($producttagdata) ? array_column($producttagdata, 'tagid') : array());
                                    $RemoveIagMappingIds = array_diff($producttagmappingids, $tagidarray);

                                    if (!empty($RemoveIagMappingIds)) {
                                        $this->Product_tag->Delete(array('productid' => $productid, 'tagid IN (' . implode(",", $RemoveIagMappingIds) . ')' => null));
                                    }
                                } else {

                                

                                    $InsertData = array(
                                        'categoryid' => $categoryid,
                                        'name' => $productname,
                                        'brandid' => $brandid,
                                        'slug' => $slug,
                                        'shortdescription' => $shortdescription,
                                        'description' => $description,
                                        'hsncodeid' => $hsncodeid,
                                        'metatitle' => $metatitle,
                                        'metadescription' => $metadescription,
                                        'metakeyword' => $metakeyword,
                                        'priority' => $priority,
                                        'quantitytype' => $quantitytype,
                                        'status' => $status,
                                        'productdisplayonfront' => $productdisplayonfront,
                                        'createddate' => $createddate,
                                        'modifeddate' => $createddate,
                                        'addedby' => $addedby,
                                        'modififedby' => $addedby
                                    );
                                    if (REWARDSPOINTS == 1) {
                                        $InsertData['pointsforseller'] = $pointsforseller;
                                        $InsertData['pointsforbuyer'] = $pointsforbuyer;
                                    }
                                    $insertproductdata[] = $InsertData;

                                    if (!empty($image)) {
                                        $imagearr = explode('|', $image);
                                        foreach ($imagearr as $imagerow) {
                                            $productimage = trim($imagerow);
                                            if (filter_var($productimage, FILTER_VALIDATE_URL)) {
                                                $productimage = $this->Product->saveimagefromurl($productimage);
                                            }
                                            if (!in_array($productimage, $productimagedata)) {
                                                $insertproductimagedata[] = array(
                                                    'filename' => $productimage,
                                                    'type' => 1,
                                                    'productid' => $productname
                                                );
                                            }
                                        }
                                    }
                                    if (!empty($tag)) {
                                        $this->Product_tag->_table = tbl_producttag;
                                        $tagid = $this->Product_tag->addMultipleProducttag(array_map('trim', explode(",", $tag)), 'notallow');
                                        $tagidarray = array_unique($tagid);

                                        if (!empty($tagidarray)) {
                                            foreach ($tagidarray as $tagid) {
                                                $InsertTagMapping[] = array("productid" => $productname, "tagid" => $tagid);
                                            }
                                        }
                                    }

                                    $noofproductimport++;
                                }
                            }else{
                                $NewProductSheeterror[] = array($category,$productname,$slug,$shortdescription,$description,$hsncode,$priority,$brand,$image,$quantitytype,$pointsforseller,$pointsforbuyer,$tag,$productdisplayonfront,$status,$metatitle,$metakeyword,$metadescription);
                            }
                        }
                     
                       
                        $totalproduct = $productcount + $noofproductimport;
                        if ($productcount >= NOOFPRODUCT) {
                            $error[] = 1;
                            $ProductSheeterror[] = "Maximum product limit over !";
                            // echo "Maximum product limit over !";
                        } else {
                            if ($totalproduct > NOOFPRODUCT) {
                                $error[] = 1;
                                // echo "Minimum " . ($totalproduct - NOOFPRODUCT) . " product insert !";
                                $ProductSheeterror[] = "Minimum " . ($totalproduct - NOOFPRODUCT) . " product insert !";
                            }
                        }

                        
                        $updateIsUniversal = $updateIsUniversalwithiddata = $barcodearray = $skuarray = array();
                       
                        for ($i = 2; $i <= $pricetotalrows; $i++) {
                            $productcode = trim($PriceobjWorksheet->getCellByColumnAndRow(0, $i)->getValue());
                            $productname = trim($PriceobjWorksheet->getCellByColumnAndRow(1, $i)->getValue());
                            $variantname = trim($PriceobjWorksheet->getCellByColumnAndRow(2, $i)->getValue());
                            
                            $stock = trim($PriceobjWorksheet->getCellByColumnAndRow(3, $i)->getValue());
                            $stock = (!empty($stock)) ? $stock : 0;
                            $sku = trim($PriceobjWorksheet->getCellByColumnAndRow(4, $i)->getValue());
                            $sku = (!empty($sku)) ? $sku : '';
                            
                            $barcode = trim($PriceobjWorksheet->getCellByColumnAndRow(5, $i)->getValue());
                            $barcode = (!empty($barcode)) ? $barcode : '';
                            $minimumorderqty = trim($PriceobjWorksheet->getCellByColumnAndRow(6, $i)->getValue());
                            $minimumorderqty = (!empty($minimumorderqty)) ? $minimumorderqty : 0;
                            $maximumorderqty = trim($PriceobjWorksheet->getCellByColumnAndRow(7, $i)->getValue());
                            $maximumorderqty = (!empty($maximumorderqty)) ? $maximumorderqty : 0;
                            $minimumstocklimit = trim($PriceobjWorksheet->getCellByColumnAndRow(8, $i)->getValue());
                            $minimumstocklimit = (!empty($minimumstocklimit)) ? $minimumstocklimit : 0;

                            $weight = trim($PriceobjWorksheet->getCellByColumnAndRow(9, $i)->getValue());
                            $weight = (!empty($weight)) ? $weight : 0;
                            
                            $addpriceinpricelist = trim($PriceobjWorksheet->getCellByColumnAndRow(10, $i)->getValue());
                            $addpriceinpricelist = (!empty($addpriceinpricelist)) ? $addpriceinpricelist : 0;
                            
                            $pricetype = trim($PriceobjWorksheet->getCellByColumnAndRow(11, $i)->getValue());
                            $pricetype = (!empty($pricetype)) ? $pricetype : 0;
                            
                            $PricesArray = $QuantityArray = $DiscountArray = array();
                            
                            $PricesArray['1'] = trim($PriceobjWorksheet->getCellByColumnAndRow(12, $i)->getValue());
                            $QuantityArray['1'] = trim($PriceobjWorksheet->getCellByColumnAndRow(13, $i)->getValue());
                            $DiscountArray['1'] = trim($PriceobjWorksheet->getCellByColumnAndRow(14, $i)->getValue());

                            $PricesArray['2'] = trim($PriceobjWorksheet->getCellByColumnAndRow(15, $i)->getValue());
                            $QuantityArray['2'] = trim($PriceobjWorksheet->getCellByColumnAndRow(16, $i)->getValue());
                            $DiscountArray['2'] = trim($PriceobjWorksheet->getCellByColumnAndRow(17, $i)->getValue());

                            $PricesArray['3'] = trim($PriceobjWorksheet->getCellByColumnAndRow(18, $i)->getValue());
                            $QuantityArray['3'] = trim($PriceobjWorksheet->getCellByColumnAndRow(19, $i)->getValue());
                            $DiscountArray['3'] = trim($PriceobjWorksheet->getCellByColumnAndRow(20, $i)->getValue());

                            $PricesArray['4'] = trim($PriceobjWorksheet->getCellByColumnAndRow(21, $i)->getValue());
                            $QuantityArray['4'] = trim($PriceobjWorksheet->getCellByColumnAndRow(22, $i)->getValue());
                            $DiscountArray['4'] = trim($PriceobjWorksheet->getCellByColumnAndRow(23, $i)->getValue());

                            $PricesArray['5'] = trim($PriceobjWorksheet->getCellByColumnAndRow(24, $i)->getValue());
                            $QuantityArray['5'] = trim($PriceobjWorksheet->getCellByColumnAndRow(25, $i)->getValue());
                            $DiscountArray['5'] = trim($PriceobjWorksheet->getCellByColumnAndRow(26, $i)->getValue());

                            $PricesArray['6'] = trim($PriceobjWorksheet->getCellByColumnAndRow(27, $i)->getValue());
                            $QuantityArray['6'] = trim($PriceobjWorksheet->getCellByColumnAndRow(28, $i)->getValue());
                            $DiscountArray['6'] = trim($PriceobjWorksheet->getCellByColumnAndRow(29, $i)->getValue());

                            $PricesArray['7'] = trim($PriceobjWorksheet->getCellByColumnAndRow(30, $i)->getValue());
                            $QuantityArray['7'] = trim($PriceobjWorksheet->getCellByColumnAndRow(31, $i)->getValue());
                            $DiscountArray['7'] = trim($PriceobjWorksheet->getCellByColumnAndRow(32, $i)->getValue());

                            $PricesArray['8'] = trim($PriceobjWorksheet->getCellByColumnAndRow(33, $i)->getValue());
                            $QuantityArray['8'] = trim($PriceobjWorksheet->getCellByColumnAndRow(34, $i)->getValue());
                            $DiscountArray['8'] = trim($PriceobjWorksheet->getCellByColumnAndRow(35, $i)->getValue());

                            $PricesArray['9'] = trim($PriceobjWorksheet->getCellByColumnAndRow(36, $i)->getValue());
                            $QuantityArray['9'] = trim($PriceobjWorksheet->getCellByColumnAndRow(37, $i)->getValue());
                            $DiscountArray['9'] = trim($PriceobjWorksheet->getCellByColumnAndRow(38, $i)->getValue());

                            $PricesArray['10'] = trim($PriceobjWorksheet->getCellByColumnAndRow(39, $i)->getValue());
                            $QuantityArray['10'] = trim($PriceobjWorksheet->getCellByColumnAndRow(40, $i)->getValue());
                            $DiscountArray['10'] = trim($PriceobjWorksheet->getCellByColumnAndRow(41, $i)->getValue());

                            //$productpriceid = array_filter(explode('|',$productcode));
                            $isvalid = 1;

                        
                            if (!empty($productcode) || !empty($productname) || !empty($variantname) || !empty($price) || !empty($stock) || !empty($sku) || !empty($barcode)) {

                                
                                if (empty($productname)) {
                                    // echo "Row no. " . $i . " product name is empty !";
                                    $PriceSheeterror[] = "Row no. " . $i . " product name is empty !";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                                if (empty($sku)) {
                                    // echo "Row no. " . $i . " product SKU is empty !";
                                    $PriceSheeterror[] = "Row no. " . $i . " product SKU is empty !";
                                    $isvalid = 0;
                                    $error[] = $i;
                                } else {
                                    if (!empty($productcode) && in_array($productcode, $productpricenamearr)) {
                                        $productpriceid = $productpriceidarr[array_search($productcode, $productpricenamearr)];
                                        $this->Product_prices->_where = "id<>'" . $productpriceid . "' AND sku='" . $sku . "'";
                                    } else {
                                        $this->Product_prices->_where = "sku='" . $sku . "'";
                                    }
                                    $Count = $this->Product_prices->CountRecords();
                                    if ($Count > 0  || in_array($sku, $skuarray)) {
                                        // echo "Row no. " . $i . " product SKU is already exist !";
                                        $PriceSheeterror[] = "Row no. " . $i . " product SKU is already exist !";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    }
                                }
                                if (empty($barcode)) {
                                    duplicate1:
                                    $barcode = rand(1000000000, 9999999999);
                                    $this->Product_prices->_where = "barcode='" . $barcode . "'";
                                    $Count = $this->Product_prices->CountRecords();
                                    if ($Count > 0 || in_array($barcode, $barcodearray)) {
                                        goto duplicate1;
                                    }
                                } else {
                                    $pattern = '/^[a-zA-Z0-9]+$/';
                                    if (!preg_match($pattern, $barcode)) {
                                        // echo "Row no. " . $i . " product barcode is allow only alphabetic & numeric characters !";
                                        $PriceSheeterror[] = "Row no. " . $i . " product barcode is allow only alphabetic & numeric characters !";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    } elseif (strlen($barcode) > 30) {
                                        // echo "Row no. " . $i . " product barcode is required maximum 30 characters !";
                                        $PriceSheeterror[] = "Row no. " . $i . " product barcode is required maximum 30 characters !";
                                        $isvalid = 0;
                                        $error[] = $i;
                                    } else {
                                        if (!empty($productcode) && in_array($productcode, $productpricenamearr)) {
                                            $productpriceid = $productpriceidarr[array_search($productcode, $productpricenamearr)];
                                            $this->Product_prices->_where = "id<>'" . $productpriceid . "' AND barcode='" . $barcode . "'";
                                        } else {
                                            $this->Product_prices->_where = "barcode='" . $barcode . "'";
                                        }
                                        $Count = $this->Product_prices->CountRecords();
                                        if ($Count > 0  || in_array($barcode, $barcodearray)) {
                                            // echo "Row no. " . $i . " product barcode is already exist !";
                                            $PriceSheeterror[] = "Row no. " . $i . " product barcode is already exist !";
                                            $isvalid = 0;
                                            $error[] = $i;
                                        }
                                    }
                                }
                                
                                if ($PricesArray[1] == "" || $PricesArray[1] == 0) {
                                    // echo "Row no. " . $i . " product price 1 is empty !";
                                    $PriceSheeterror[] = "Row no. " . $i . " product price 1 is empty !";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }
                                if ($QuantityArray[1] == "" || $QuantityArray[1] == 0) {
                                    // echo "Row no. " . $i . " product quantity 1 is empty !";
                                    $PriceSheeterror[] = "Row no. " . $i . " product quantity 1 is empty !";
                                    $isvalid = 0;
                                    $error[] = $i;
                                }

                                if(in_array($productname,$NewProductSheeterror)){
                                    $isvalid = 0;
                                }
                     
                               
                                if ($isvalid){
                                  
                                  
                                    if (!empty($variantname)) {
                                        $variant = explode('|', $variantname);
                                        $variant = array_map('trim', $variant);

                                      
                                        $variant_diff = array_diff($variant,$variantnamearr);

                                     
                                        if (!empty($variant_diff)) {

                                        
                                            foreach ($variant_diff as $val) {
                                                $attributeid = 0;
                                                $variantarray = explode('#', $val);
                                                $attribute = $variantarray[0];
                                                $variantnm = $variantarray[1];

                                                $attributedata = $this->Attribute->getAllAttributes();
                                                $attributeidarr = $attributenamearr = array();
                                                if (!empty($attributedata)) {
                                                    $attributeidarr = array_column($attributedata, 'id');
                                                    $attributenamearr = array_column($attributedata, 'variantname');
                                                }
                                                if (in_array($attribute, $attributenamearr)) {
                                                    $attributeid = $attributeidarr[array_search($attribute, $attributenamearr)];
                                                } else {
                                                    $this->Attribute->_where = array();
                                                    $this->Attribute->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
                                                    $attributedata = $this->Attribute->getRecordsById();

                                                    $maxpriority = (!empty($attributedata)) ? $attributedata['maxpriority'] : 1;

                                                    $InsertData = array(
                                                        'variantname' => $attribute,
                                                        'priority' => $maxpriority,
                                                    );

                                                    $attributeid = $this->Attribute->Add($InsertData);
                                                }

                                                $variantdata = $this->Variant->getVariantDataByAttributeID($attributeid);
                                                $variantidarr = $variantnamearr = array();
                                                if (!empty($variantdata)) {
                                                    $variantidarr = array_column($variantdata, 'id');
                                                    $variantnamearr = array_column($variantdata, 'value');
                                                }
                                                if (!in_array($variantnm, $variantnamearr)) {

                                                    $this->Variant->_where = array();
                                                    $this->Variant->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
                                                    $variantdata = $this->Variant->getRecordsById();

                                                    $maxpriority = (!empty($variantdata)) ? $variantdata['maxpriority'] : 1;

                                                    $InsertData = array(
                                                        'attributeid' => $attributeid,
                                                        'value' => $variantnm,
                                                        'priority' => $maxpriority,
                                                    );

                                                    $this->Variant->Add($InsertData);
                                                    $getvariants = 1;
                                                }
                                            }
                                        }
                                      

                                        
                                        if (!empty($productcode) && in_array($productcode, $productpricenamearr)) { //EDIT
                                            $productd = $productpriceproductidarr[array_search($productcode, $productpricenamearr)];
                                            $productpriceid = $productpriceidarr[array_search($productcode, $productpricenamearr)];

                                            $updteproductpricesdataarray = array(
                                                'pricetype' => $pricetype,
                                                'weight' => $weight,
                                                'sku' => $sku,
                                                'barcode' => $barcode,
                                                'minimumorderqty' => $minimumorderqty,
                                                'maximumorderqty' => $maximumorderqty,
                                                'minimumstocklimit' => $minimumstocklimit,
                                                'id' => $productpriceid
                                            );

                                            if (!empty($stock)) {
                                                $updteproductpricesdataarray['stock'] = $stock;
                                                /* $updateproductpricesdata[] = array('pricetype'=>$pricetype,
                                                                                    'weight'=>$weight,
                                                                                    'stock'=>$stock,
                                                                                    'sku' => $sku,
                                                                                    'barcode' => $barcode,
                                                                                    'minimumorderqty' => $minimumorderqty,
                                                                                    'maximumorderqty' => $maximumorderqty,
                                                                                    'minimumstocklimit' => $minimumstocklimit,
                                                                                    'id'=>$productpriceid); */
                                            } else {
                                                /* $updateproductpricesdata[] = array( 'pricetype'=>$pricetype,
                                                                                    'weight'=>$weight,
                                                                                    'sku' => $sku,
                                                                                    'barcode' => $barcode,
                                                                                    'minimumorderqty' => $minimumorderqty,
                                                                                    'maximumorderqty' => $maximumorderqty,
                                                                                    'minimumstocklimit' => $minimumstocklimit,
                                                                                    'id'=>$productpriceid); */
                                            }

                                            /* $updateproductpricesdata[] = array('pricetype'=>$pricetype,
                                                                                'weight'=>$weight,
                                                                                'sku' => $sku,
                                                                                'barcode' => $barcode,
                                                                                'minimumorderqty' => $minimumorderqty,
                                                                                'maximumorderqty' => $maximumorderqty,
                                                                                'minimumstocklimit' => $minimumstocklimit,
                                                                                'id'=>$productpriceid
                                                                        ); */
                                            $updateproductpricesdata[] = $updteproductpricesdataarray;

                                            //UPDATE MULTIPLE PRICE
                                            $dbpricedata = $this->Product_prices->getProductQuantityPriceDataByPriceID($productpriceid, array("id" => "ASC"));

                                            
                                            $isUpdateCount = !empty($dbpricedata) ? ($pricetype == 1 ? count($dbpricedata) : 1) : 0;
                                            if (!empty($dbpricedata)) {
                                                foreach ($dbpricedata as $kp => $dbprice) {
                                                    $DB_Productquantityprice_ID[] = $dbprice['id'];
                                                    if ($pricetype == 1) {
                                                        if (!empty($PricesArray[$kp + 1]) && !empty($QuantityArray[$kp + 1])) {

                                                            $UpdateMultiplePriceData[] = array(
                                                                "id" => $dbprice['id'],
                                                                "price" => $PricesArray[$kp + 1],
                                                                "quantity" => $QuantityArray[$kp + 1],
                                                                'discount' => $DiscountArray[$kp + 1]
                                                            );

                                                            $UpdatedProductQuantityPrice[] = $dbprice['id'];

                                                            unset($PricesArray[$kp + 1]);
                                                            unset($QuantityArray[$kp + 1]);
                                                            unset($DiscountArray[$kp + 1]);
                                                        }
                                                    } else {
                                                        if ($kp == 0) {
                                                            if (!empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                                $UpdateMultiplePriceData[] = array(
                                                                    "id" => $dbprice['id'],
                                                                    "price" => $PricesArray[1],
                                                                    "quantity" => 1,
                                                                    'discount' => $DiscountArray[1]
                                                                );

                                                                $UpdatedProductQuantityPrice[] = $dbprice['id'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            
                                            if ($pricetype == 1) {
                                                foreach ($PricesArray as $ksp => $sheetprice) {
                                                    if (!empty($sheetprice) && !empty($QuantityArray[$ksp])) {

                                                        $InsertMultiplePriceDataUpdt[] = array(
                                                            "productpricesid" => $productpriceid,
                                                            "price" => $sheetprice,
                                                            "quantity" => $QuantityArray[$ksp],
                                                            'discount' => $DiscountArray[$ksp]
                                                        );
                                                    }
                                                }
                                            } else {
                                                if ($isUpdateCount == 0 && !empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                    $InsertMultiplePriceDataUpdt[] = array(
                                                        "productpricesid" => $productpriceid,
                                                        "price" => $PricesArray[1],
                                                        "quantity" => 1,
                                                        'discount' => $DiscountArray[1]
                                                    );
                                                }
                                            }

                                            if (in_array($productpriceid, $productcombinationgrouppriceidarr)) {
                                                foreach ($variant as $variantrow) {
                                                    if (in_array($variantrow, $variantnamearr)) {
                                                        $variantidarrr[] = $variantid = $variantidarr[array_search($variantrow, $variantnamearr)];
                                                        if (!in_array($productpriceid . '|' . $variantid, $productcombinationnamearr)) {
                                                            $insertproductcombination[] = array('priceid' => $productpriceid, 'variantid' => $variantid);
                                                        }
                                                    }
                                                }

                                                if (count(array_diff(explode(',', $productcombinationgroupdata[array_search($productpriceid, $productcombinationgrouppriceidarr)]['variant']), $variantidarrr)) > 0) {
                                                    $removedata = array_diff(explode(',', $productcombinationgroupdata[array_search($productpriceid, $productcombinationgrouppriceidarr)]['variant']), $variantidarrr);
                                                    foreach ($removedata as $removerow) {
                                                        $removeproductcombination[] = $productcombinationdata[array_search(($productpriceid . '|' . $removerow), $productcombinationnamearr)]['id'];
                                                    }
                                                }
                                            }
                                        } else { //ADD
                                            
                                            $variantarr[] = $variantname;
                                            if (in_array($productname, $productnamearr)) {
                                                $productid = (int)$productidarr[array_search($productname, $productnamearr)];
                                                $updateIsUniversalwithiddata[] = array("id" => $productid, "isuniversal" => 0);
                                            } else {
                                                $productid = $productname;
                                                $updateIsUniversal[] = array("id" => $productid, "isuniversal" => 0);
                                            }
                                            $insertproductpriceswithvariantdata[] = array(/* 'price'=>$price, */
                                                'pricetype' => $pricetype,
                                                'weight' => $weight,
                                                'stock' => $stock,
                                                'sku' => $sku,
                                                'barcode' => $barcode,
                                                'minimumorderqty' => $minimumorderqty,
                                                'maximumorderqty' => $maximumorderqty,
                                                'minimumstocklimit' => $minimumstocklimit,
                                                'productid' => $productid
                                            );

                                            
                                            if ($pricetype == 1) {
                                                
                                                for ($p = 1; $p <= 10; $p++) {

                                                    if (!empty($PricesArray[$p]) && !empty($QuantityArray[$p])) {

                                                        $insertMultiPriceDataWithVariant[] = array(
                                                            "productpricesid" => $sku,
                                                            "price" => $PricesArray[$p],
                                                            "quantity" => $QuantityArray[$p],
                                                            'discount' => $DiscountArray[$p]
                                                        );
                                                    }
                                                }
                                            } else {

                                                if (!empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                    $insertMultiPriceDataWithVariant[] = array(
                                                        "productpricesid" => $sku,
                                                        "price" => $PricesArray[1],
                                                        "quantity" => 1,
                                                        'discount' => $DiscountArray[1]
                                                    );
                                                }
                                            }
                                        }
                                    } else {

                                        
                                        if (!empty($productcode) && in_array($productcode, $productpricenamearr)) { //EDIT
                                            $productd = $productpriceproductidarr[array_search($productcode, $productpricenamearr)];
                                            $productpriceid = $productpriceidarr[array_search($productcode, $productpricenamearr)];

                                            $updteproductpricesdataarray = array(
                                                'pricetype' => $pricetype,
                                                'weight' => $weight,
                                                'sku' => $sku,
                                                'barcode' => $barcode,
                                                'minimumorderqty' => $minimumorderqty,
                                                'maximumorderqty' => $maximumorderqty,
                                                'minimumstocklimit' => $minimumstocklimit,
                                                'id' => $productpriceid
                                            );

                                            if (!empty($stock)) {
                                                $updteproductpricesdataarray['stock'] = $stock;
                                                /* $updateproductpricesdata[] = array(
                                                                                'pricetype'=>$pricetype,
                                                                                'weight'=>$weight,
                                                                                'stock'=>$stock,
                                                                                'sku' => $sku,
                                                                                'barcode' => $barcode,
                                                                                'minimumorderqty' => $minimumorderqty,
                                                                                'maximumorderqty' => $maximumorderqty,
                                                                                'minimumstocklimit' => $minimumstocklimit,
                                                                                'id'=>$productpriceid); */
                                            } else {
                                                /* $updateproductpricesdata[] = array(
                                                                                'pricetype'=>$pricetype,
                                                                                'weight'=>$weight,
                                                                                'sku' => $sku,
                                                                                'barcode' => $barcode,
                                                                                'minimumorderqty' => $minimumorderqty,
                                                                                'maximumorderqty' => $maximumorderqty,
                                                                                'minimumstocklimit' => $minimumstocklimit,
                                                                                'id'=>$productpriceid); */
                                            }
                                            $updateproductpricesdata[] = $updteproductpricesdataarray;

                                            //UPDATE MULTIPLE PRICE
                                            $dbpricedata = $this->Product_prices->getProductQuantityPriceDataByPriceID($productpriceid, array("id" => "ASC"));

                                            $isUpdateCount = !empty($dbpricedata) ? ($pricetype == 1 ? count($dbpricedata) : 1) : 0;
                                            if (!empty($dbpricedata)) {
                                                foreach ($dbpricedata as $kp => $dbprice) {
                                                    $DB_Productquantityprice_ID[] = $dbprice['id'];
                                                    if ($pricetype == 1) {
                                                        if (!empty($PricesArray[$kp + 1]) && !empty($QuantityArray[$kp + 1])) {

                                                            $UpdateMultiplePriceData[] = array(
                                                                "id" => $dbprice['id'],
                                                                "price" => $PricesArray[$kp + 1],
                                                                "quantity" => $QuantityArray[$kp + 1],
                                                                'discount' => $DiscountArray[$kp + 1]
                                                            );

                                                            $UpdatedProductQuantityPrice[] = $dbprice['id'];

                                                            unset($PricesArray[$kp + 1]);
                                                            unset($QuantityArray[$kp + 1]);
                                                            unset($DiscountArray[$kp + 1]);
                                                        }
                                                    } else {
                                                        if ($kp == 0) {
                                                            if (!empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                                $UpdateMultiplePriceData[] = array(
                                                                    "id" => $dbprice['id'],
                                                                    "price" => $PricesArray[1],
                                                                    "quantity" => 1,
                                                                    'discount' => $DiscountArray[1]
                                                                );

                                                                $UpdatedProductQuantityPrice[] = $dbprice['id'];
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            if ($pricetype == 1) {
                                                foreach ($PricesArray as $ksp => $sheetprice) {
                                                    if (!empty($sheetprice) && !empty($QuantityArray[$ksp])) {

                                                        $InsertMultiplePriceDataUpdt[] = array(
                                                            "productpricesid" => $productpriceid,
                                                            "price" => $sheetprice,
                                                            "quantity" => $QuantityArray[$ksp],
                                                            'discount' => $DiscountArray[$ksp]
                                                        );
                                                    }
                                                }
                                            } else {
                                                if ($isUpdateCount == 0 && !empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                    $InsertMultiplePriceDataUpdt[] = array(
                                                        "productpricesid" => $productpriceid,
                                                        "price" => $PricesArray[1],
                                                        "quantity" => 1,
                                                        'discount' => $DiscountArray[1]
                                                    );
                                                }
                                            }
                                        } else {
                                            if (in_array($productname, $productnamearr)) {
                                                $productid = (int)$productidarr[array_search($productname, $productnamearr)];
                                                if (!in_array($productid . '|0', $productpricenamearr)) { //EDIT
                                                    $productpriceid = $productpriceidarr[array_search($productid . '|0', $productpricenamearr)];

                                                    $updteproductpricesdataarray = array(/* 'price'=>$price, */
                                                        'pricetype' => $pricetype,
                                                        'weight' => $weight,
                                                        'sku' => $sku,
                                                        'barcode' => $barcode,
                                                        'minimumorderqty' => $minimumorderqty,
                                                        'maximumorderqty' => $maximumorderqty,
                                                        'minimumstocklimit' => $minimumstocklimit,
                                                        'id' => $productpriceid
                                                    );
                                                    if (!empty($stock)) {
                                                        $updteproductpricesdataarray['stock'] = $stock;
                                                        /* $updateproductpricesdata[] = array(
                                                                                        'pricetype'=>$pricetype,
                                                                                        'weight'=>$weight,
                                                                                        'stock'=>$stock,
                                                                                        'sku' => $sku,
                                                                                        'barcode' => $barcode,
                                                                                        'minimumorderqty' => $minimumorderqty,
                                                                                        'maximumorderqty' => $maximumorderqty,
                                                                                        'minimumstocklimit' => $minimumstocklimit,
                                                                                        'id'=>$productpriceid); */
                                                    } else {
                                                        /* $updateproductpricesdata[] = array(
                                                                                        'pricetype'=>$pricetype,
                                                                                        'weight'=>$weight,
                                                                                        'sku' => $sku,
                                                                                        'barcode' => $barcode,
                                                                                        'minimumorderqty' => $minimumorderqty,
                                                                                        'maximumorderqty' => $maximumorderqty,
                                                                                        'minimumstocklimit' => $minimumstocklimit,
                                                                                        'id'=>$productpriceid); */
                                                    }
                                                    $updateproductpricesdata[] = $updteproductpricesdataarray;

                                                    //UPDATE MULTIPLE PRICE
                                                    $dbpricedata = $this->Product_prices->getProductQuantityPriceDataByPriceID($productpriceid, array("id" => "ASC"));

                                                    $isUpdateCount = !empty($dbpricedata) ? ($pricetype == 1 ? count($dbpricedata) : 1) : 0;
                                                    if (!empty($dbpricedata)) {
                                                        foreach ($dbpricedata as $kp => $dbprice) {
                                                            $DB_Productquantityprice_ID[] = $dbprice['id'];
                                                            if ($pricetype == 1) {
                                                                if (!empty($PricesArray[$kp + 1]) && !empty($QuantityArray[$kp + 1])) {

                                                                    $UpdateMultiplePriceData[] = array(
                                                                        "id" => $dbprice['id'],
                                                                        "price" => $PricesArray[$kp + 1],
                                                                        "quantity" => $QuantityArray[$kp + 1],
                                                                        'discount' => $DiscountArray[$kp + 1]
                                                                    );

                                                                    $UpdatedProductQuantityPrice[] = $dbprice['id'];

                                                                    unset($PricesArray[$kp + 1]);
                                                                    unset($QuantityArray[$kp + 1]);
                                                                    unset($DiscountArray[$kp + 1]);
                                                                }
                                                            } else {
                                                                if ($kp == 0) {
                                                                    if (!empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                                        $UpdateMultiplePriceData[] = array(
                                                                            "id" => $dbprice['id'],
                                                                            "price" => $PricesArray[1],
                                                                            "quantity" => 1,
                                                                            'discount' => $DiscountArray[1]
                                                                        );

                                                                        $UpdatedProductQuantityPrice[] = $dbprice['id'];
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($pricetype == 1) {
                                                        foreach ($PricesArray as $ksp => $sheetprice) {
                                                            if (!empty($sheetprice) && !empty($QuantityArray[$ksp])) {

                                                                $InsertMultiplePriceDataUpdt[] = array(
                                                                    "productpricesid" => $productpriceid,
                                                                    "price" => $sheetprice,
                                                                    "quantity" => $QuantityArray[$ksp],
                                                                    'discount' => $DiscountArray[$ksp]
                                                                );
                                                            }
                                                        }
                                                    } else {
                                                        if ($isUpdateCount == 0 && !empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                            $InsertMultiplePriceDataUpdt[] = array(
                                                                "productpricesid" => $productpriceid,
                                                                "price" => $PricesArray[1],
                                                                "quantity" => 1,
                                                                'discount' => $DiscountArray[1]
                                                            );
                                                        }
                                                    }
                                                } else { //ADD
                                                    $insertproductpricesdata[] = array(/* 'price'=>$price, */
                                                        'pricetype' => $pricetype,
                                                        'weight' => $weight,
                                                        'stock' => $stock,
                                                        'sku' => $sku,
                                                        'barcode' => $barcode,
                                                        'minimumorderqty' => $minimumorderqty,
                                                        'maximumorderqty' => $maximumorderqty,
                                                        'minimumstocklimit' => $minimumstocklimit,
                                                        'productid' => $productid
                                                    );

                                                    if ($pricetype == 1) {
                                                        for ($p = 1; $p <= 10; $p++) {

                                                            if (!empty($PricesArray[$p]) && !empty($QuantityArray[$p])) {

                                                                $InsertMultiplePriceData[] = array(
                                                                    "productpricesid" => $sku,
                                                                    "price" => $PricesArray[$p],
                                                                    "quantity" => $QuantityArray[$p],
                                                                    'discount' => $DiscountArray[$p]
                                                                );
                                                            }
                                                        }
                                                    } else {

                                                        if (!empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                            $InsertMultiplePriceData[] = array(
                                                                "productpricesid" => $sku,
                                                                "price" => $PricesArray[1],
                                                                "quantity" => 1,
                                                                'discount' => $DiscountArray[1]
                                                            );
                                                        }
                                                    }
                                                }
                                                $updateIsUniversalwithiddata[] = array("id" => $productid, "isuniversal" => 1);
                                            } else { //ADD
                                                $productid = $productname;
                                                $insertproductpricesdata[] = array(/* 'price'=>$price, */
                                                    'pricetype' => $pricetype,
                                                    'weight' => $weight,
                                                    'stock' => $stock,
                                                    'sku' => $sku,
                                                    'barcode' => $barcode,
                                                    'minimumorderqty' => $minimumorderqty,
                                                    'maximumorderqty' => $maximumorderqty,
                                                    'minimumstocklimit' => $minimumstocklimit,
                                                    'productid' => $productid
                                                );

                                                if ($pricetype == 1) {
                                                    for ($p = 1; $p <= 10; $p++) {

                                                        if (!empty($PricesArray[$p]) && !empty($QuantityArray[$p])) {

                                                            $InsertMultiplePriceData[] = array(
                                                                "productpricesid" => $sku,
                                                                "price" => $PricesArray[$p],
                                                                "quantity" => $QuantityArray[$p],
                                                                'discount' => $DiscountArray[$p]
                                                            );
                                                        }
                                                    }
                                                } else {

                                                    if (!empty($PricesArray[1]) && !empty($QuantityArray[1])) {

                                                        $InsertMultiplePriceData[] = array(
                                                            "productpricesid" => $sku,
                                                            "price" => $PricesArray[1],
                                                            "quantity" => 1,
                                                            'discount' => $DiscountArray[1]
                                                        );
                                                    }
                                                }
                                                $updateIsUniversal[] = array("id" => $productid, "isuniversal" => 1);
                                            }
                                        }
                                    }

                                    
                                    if($addpriceinpricelist==1){
                                    }
                                    if($addpriceinpricelist==1){
                                        // $channeltype = ($producttype==0?'all':"onlyvendor");
                                        
                                        $query = $this->readdb->select("id,name,color,productpriceid,pricetype,channelid,allowproduct,minimumqty,maximumqty")
                                        ->from(tbl_channel)
                                        ->where('1=1')
                                        ->order_by("priority ASC")
                                        ->get();

                                      

                                        if ($query->num_rows() > 0) {
                                            $query = $query->result_array();
                                            $channeldata = $query;
                                        }
                                        // $channeldata = $query;
                                        // $channeldata = $this->Channel->getChannelList('all');
                                        if(!empty($channeldata)){
                                            print_r($channeldata);
                                            foreach($channeldata as $channel){
                                $InsertPriceList =  array('productid' => $productid,
                                                        'productpriceid'=>$productpricesid,
                                                        'pricetype' => $pricetype,
                                                        'channelid' => $channel['id'],
                                                        'allowproduct' => 1,
                                                        'minimumqty' => $minimumorderqty,                            
                                                        'maximumqty' => $maximumorderqty
                                                    );
            
                                                $this->Price_list->_table = tbl_productbasicpricemapping;
                                                $productbasicpricemappingid = $this->Price_list->Add($InsertPriceList);   
                                                if($productbasicpricemappingid){
                                                    if($pricetype==1){
                                                        if(!empty($variantpricearray)){
                                                            foreach($variantpricearray as $k=>$variantprice) {
                            
                                                                $insertMultiPriceListData[] = array(
                                                                        "productbasicpricemappingid"=>$productbasicpricemappingid,
                                                                        "salesprice"=>$variantprice,
                                                                        "quantity" => $variantqtyarray[$k],
                                                                        'discount' => $variantdiscpercentarray[$k]
                                                                    );
                                                            }
                                                        }
                                                    }else{
                                                        $insertMultiPriceListData[] = array(
                                                                "productbasicpricemappingid"=>$productbasicpricemappingid,
                                                                "salesprice"=>$price,
                                                                "quantity" => 1,
                                                                'discount' => $discount
                                                            );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $barcodearray[] = $barcode;
                                    $skuarray[] = $sku;
                                }else{
                                 
                                   
                                    $NewPriceSheeterror[] = array($productcode,$productname,$variantname,$stock,$sku,$barcode,$minimumorderqty,$maximumorderqty,$minimumstocklimit,$weight,$addpriceinpricelist,$pricetype,$PricesArray['1'],$QuantityArray['1'],$DiscountArray['1'],$PricesArray['2'],$QuantityArray['2'],$DiscountArray['2'],$PricesArray['3'],$QuantityArray['3'],$DiscountArray['3'],$PricesArray['4'],$QuantityArray['4'],$DiscountArray['4'],$PricesArray['5'],$QuantityArray['5'],$DiscountArray['5'],$PricesArray['6'],$QuantityArray['6'],$DiscountArray['6'],$PricesArray['7'],$QuantityArray['7'],$DiscountArray['7'],$PricesArray['8'],$QuantityArray['8'],$DiscountArray['8'],$PricesArray['9'],$QuantityArray['9'],$DiscountArray['9'],$PricesArray['10'],$QuantityArray['10'],$DiscountArray['10']);
                                }
                            }
                        }
                        // print_r($UpdateMultiplePriceData);
                        // print_r($UpdatedProductQuantityPrice);
                        // print_r($insertMultiPriceDataWithVariant);
                        // print_r($DB_Productquantityprice_ID); 
                        // print_r(array_diff($DB_Productquantityprice_ID, $UpdatedProductQuantityPrice));
                        // exit;
                        if (empty($error)) {
                            if ($getvariants == 1) {
                                $variantdata = $this->Variant->getVariantDataForImport();
                                $variantidarr = array_column($variantdata, 'id');
                                $variantnamearr = array_column($variantdata, 'variantname');
                            }
                            $productnamearr = $productidarr = array();
                            if (!empty($insertproductdata)) {
                                $this->Product->_table = tbl_product;
                                $this->Product->add_batch($insertproductdata);

                                $firstbatch_id = $this->writedb->insert_id();
                                $lastbatch_id = $firstbatch_id + (count($insertproductdata) - 1);

                                $productids = array();
                                for ($n = $firstbatch_id; $n <= $lastbatch_id; $n++) {
                                    $productids[] = $n;
                                }

                                $this->Product->_table = tbl_product;
                                $this->Product->_fields = "id,name";
                                $this->Product->_where = array("id IN (" . implode(",", $productids) . ")" => null);
                                $productdata = $this->Product->getRecordByID();
                                $productnamearr = array_column($productdata, 'name');
                                $productidarr = array_column($productdata, 'id');

                                foreach ($insertproductimagedata as $index => $row) {
                                    if (!empty($productnamearr) && in_array($row['productid'], $productnamearr)) {
                                        $insertproductimagedata[$index]['productid'] = $productidarr[array_search($row['productid'], $productnamearr)];
                                    } else {
                                        unset($insertproductimagedata[$index]);
                                    }
                                }
                                foreach ($updateIsUniversal as $index => $row) {
                                    if (!empty($productnamearr) && in_array($row['id'], $productnamearr)) {
                                        $updateIsUniversal[$index]['id'] = $productidarr[array_search($row['id'], $productnamearr)];
                                    } else {
                                        unset($updateIsUniversal[$index]);
                                    }
                                }
                            }

                            if (!empty($updateproductdata)) {
                                $this->Product->_table = tbl_product;
                                $this->Product->edit_batch($updateproductdata, 'id');
                            }
                            $IsUniversal = array_merge($updateIsUniversal, $updateIsUniversalwithiddata);
                            if (!empty($IsUniversal)) {
                                $this->Product->_table = tbl_product;
                                $this->Product->edit_batch($IsUniversal, 'id');
                            }
                            $productimagedata = array_merge($insertproductimagedata, $insertproductimagewithiddata);
                            if (!empty($productimagedata)) {
                                $this->Product->_table = tbl_productimage;
                                $this->Product->add_batch($productimagedata);
                            }
                            if (!empty($insertproductpricesdata)) {

                                foreach ($insertproductpricesdata as $index => $row) {
                                    if (!is_int($row['productid'])) {
                                        if (!empty($productnamearr) && in_array($row['productid'], $productnamearr)) {
                                            $insertproductpricesdata[$index]['productid'] = $productidarr[array_search($row['productid'], $productnamearr)];
                                        } else {
                                            unset($insertproductpricesdata[$index]);
                                        }
                                    }
                                }
                                if (!empty($insertproductpricesdata)) {
                                    $this->Product_prices->_table = tbl_productprices;
                                    $this->Product_prices->add_batch($insertproductpricesdata);

                                    $firstpricebatch_id = $this->writedb->insert_id();
                                    $lastpricebatch_id = $firstpricebatch_id + (count($insertproductpricesdata) - 1);

                                    $productpriceids = array();
                                    for ($pr = $firstpricebatch_id; $pr <= $lastpricebatch_id; $pr++) {
                                        $productpriceids[] = $pr;
                                    }

                                    $this->Product_prices->_table = tbl_productprices;
                                    $this->Product_prices->_fields = "id,sku";
                                    $this->Product_prices->_where = array("id IN (" . implode(",", $productpriceids) . ")" => null);
                                    $productpricedata = $this->Product_prices->getRecordByID();
                                    $productpriceskuarr = array_column($productpricedata, 'sku');
                                    $productpriceidarr = array_column($productpricedata, 'id');

                                    if (!empty($InsertMultiplePriceData)) {

                                        foreach ($InsertMultiplePriceData as $k => $row) {
                                            if (!empty($row['productpricesid'])) {
                                                if (!empty($productpriceskuarr) && in_array($row['productpricesid'], $productpriceskuarr)) {
                                                    $InsertMultiplePriceData[$k]['productpricesid'] = $productpriceidarr[array_search($row['productpricesid'], $productpriceskuarr)];
                                                } else {
                                                    unset($InsertMultiplePriceData[$k]);
                                                }
                                            }
                                        }
                                        if (!empty($InsertMultiplePriceData)) {
                                            $this->Product_prices->_table = tbl_productquantityprices;
                                            $this->Product_prices->add_batch($InsertMultiplePriceData);
                                        }
                                    }
                                }
                            }
                            if (!empty($insertproductpriceswithvariantdata)) {
                                foreach ($insertproductpriceswithvariantdata as $index => $row) {
                                    if (!is_int($row['productid'])) {
                                        if (!empty($productnamearr) && in_array($row['productid'], $productnamearr)) {
                                            $insertproductpriceswithvariantdata[$index]['productid'] = $productidarr[array_search($row['productid'], $productnamearr)];
                                        } else {
                                            unset($insertproductpriceswithvariantdata[$index]);
                                        }
                                    }
                                }

                                if (!empty($insertproductpriceswithvariantdata)) {
                                    $this->Product_prices->_table = tbl_productprices;
                                    $this->Product_prices->add_batch($insertproductpriceswithvariantdata);

                                    $first_id = $this->writedb->insert_id();
                                    $last_id = $first_id + (count($insertproductpriceswithvariantdata) - 1);

                                    $count = 0;
                                    $productpriceids = array();
                                    for ($i = $first_id; $i <= $last_id; $i++) {

                                        $variant = explode('|', $variantarr[$count]);
                                        $variant = array_map('trim', $variant);
                                        foreach ($variant as $variantrow) {
                                            if (in_array($variantrow, $variantnamearr)) {
                                                $variantid = $variantidarr[array_search($variantrow, $variantnamearr)];
                                                $insertproductcombination[] = array('priceid' => $i, 'variantid' => $variantid);
                                            }
                                        }
                                        $count++;

                                        $productpriceids[] = $i;
                                    }

                                    $this->Product_prices->_table = tbl_productprices;
                                    $this->Product_prices->_fields = "id,sku";
                                    $this->Product_prices->_where = array("id IN (" . implode(",", $productpriceids) . ")" => null);
                                    $productpricedata = $this->Product_prices->getRecordByID();
                                    $productpriceskuarr = array_column($productpricedata, 'sku');
                                    $productpriceidarr = array_column($productpricedata, 'id');

                                    if (!empty($insertMultiPriceDataWithVariant)) {

                                        foreach ($insertMultiPriceDataWithVariant as $index => $row) {
                                            if (!empty($row['productpricesid'])) {
                                                if (!empty($productpriceskuarr) && in_array($row['productpricesid'], $productpriceskuarr)) {
                                                    $insertMultiPriceDataWithVariant[$index]['productpricesid'] = $productpriceidarr[array_search($row['productpricesid'], $productpriceskuarr)];
                                                } else {
                                                    unset($insertMultiPriceDataWithVariant[$index]);
                                                }
                                            }
                                        }
                                        if (!empty($insertMultiPriceDataWithVariant)) {
                                            $this->Product_prices->_table = tbl_productquantityprices;
                                            $this->Product_prices->add_batch($insertMultiPriceDataWithVariant);
                                        }
                                    }
                                }
                                // print_r($insertproductcombination); exit;
                            }
                            if (!empty($insertproductcombination)) {
                                $this->Product_combination->add_batch($insertproductcombination);
                            }
                            if (!empty($removeproductcombination)) {
                                $this->Product->_table = tbl_productcombination;
                                $this->Product->Delete(array("id IN($removeproductcombination)" => null));
                            }
                            if (!empty($updateproductpricesdata)) {
                                $this->Product_prices->_table = tbl_productprices;
                                $this->Product_prices->edit_batch($updateproductpricesdata, 'id');
                            }

                            if (!empty($DB_Productquantityprice_ID)) {
                                $resultId = array_diff($DB_Productquantityprice_ID, $UpdatedProductQuantityPrice);

                                if (!empty($resultId)) {
                                    $this->Product_prices->_table = tbl_productquantityprices;
                                    $this->Product_prices->Delete(array("id IN (" . implode(",", $resultId) . ")" => null));
                                }
                            }
                            if (!empty($InsertMultiplePriceDataUpdt)) {
                                $this->Product_prices->_table = tbl_productquantityprices;
                                $this->Product_prices->add_batch($InsertMultiplePriceDataUpdt);
                            }
                            if (!empty($UpdateMultiplePriceData)) {
                                $this->Product_prices->_table = tbl_productquantityprices;
                                $this->Product_prices->edit_batch($UpdateMultiplePriceData, 'id');
                            }


                            $file_info_arr = array(
                                "channelid" => 0,
                                "memberid" => 0,
                                "type" => 1,
                                "file" => $FileNM,
                                "errorfile" => $ErrorFileNM,
                                "ipaddress" => $this->input->ip_address(),
                                "totalrow" => count($insertproductdata),
                                "createddate" => $createddate,
                                "modifieddate" => $createddate,
                                "addedby" => $addedby,
                                "modifiedby" => $addedby
                            );
                            $this->load->model("Import_excel_model", "Import_excel");
                            $this->Import_excel->add($file_info_arr);

                            echo 1;
                        } else {

                            $data = $title = $headerstyle = $headings = array();
                            if(!empty($NewProductSheeterror)){
                                $data[] = $NewProductSheeterror;
                                $title[] = "Product";
                                $headings[] = array("Category Name *","Product Name *","Link","Short Description","Description *","Hsn Code(%)","Priority *","Brand","Image","Quantity Type (1=>Range Base,0=>Multiplication)","Points For Seller","Points For Buyer","Tag","Product Display on Front or Not","Activate (1=>Yes,0=>No)","Meta Title","Meta Keywords","Meta Description");
                                $headerstyle[] = "A1:AAA1";

                            }
                            
                            if(!empty($NewPriceSheeterror)){
                                $data[] = $NewPriceSheeterror;
                                $title[] = "Price";
                                $headings[] = array("Product Code","Product Name *","Variant","Stock","SKU *","Barcode","Minimum Order Quantity","Maximum Order Quantity","Minimum Stock Limit","Weight (kg)","Add Price In Price List (1=>Yes, 0=>No)","Price Type (0=>Single Quantity, 1=>Multiple Quantity)","Price 1 *","Quantity 1 *","Discount 1 (%)","Price 2","Quantity 2","Discount 2 (%)","Price 3","Quantity 3","Discount 3 (%)","Price 4","Quantity 4","Discount 4 (%)","Price 5","Quantity 5","Discount 5 (%)","Price 6","Quantity 6","Discount 6 (%)","Price 7","Quantity 7","Discount 7 (%)","Price 8","Quantity 8","Discount 8 (%)","Price 9","Quantity 9","Discount 9 (%)","Price 10","Quantity 10","Discount 10 (%)");
                                $headerstyle[] = "A1:AAA1";
                                // $data[] = $ProductSheeterror;
                            }
                            if(!empty($NewProductSheeterror)){

                                $ProductSheeterrordata = array();
                                if(!empty($ProductSheeterror)){
                                    foreach($ProductSheeterror as $err){
                                        $ProductSheeterrordata[] = array($producttotalrowserrorcoount,$err);
                                    }
                                }
                                $producttotalrowserrorcoount++;

                                $data[] = $ProductSheeterrordata;
                                $title[] = "Product Error";
                                $headings[] = array("Row No.","Detail");
                                $headerstyle[] = "A1:AAA1";
                            }
                            if(!empty($NewPriceSheeterror)){
                                $PriceSheeterrordata = array();
                                if(!empty($PriceSheeterror)){
                                    foreach($PriceSheeterror as $err){
                                        $PriceSheeterrordata[] = array($pricetotalrowserrorcoount,$err);
                                    }
                                }
                                $pricetotalrowserrorcoount++;

                                $title[] = "Price Error";
                                $data[] = $PriceSheeterrordata;
                                $headings[] = array("Row No.","Detail");
                                $headerstyle[] = "A1:AAA1";
                                // producttotalrowserrorcoount

                            }
                            // pre($data);
                            if(!empty($data)){
                                $ErrorFileNM = 'error-'.$FileNM;
                                $this->general_model->exporttoexcelwithmultiplesheet($data,$headerstyle,$title,$headings,$ErrorFileNM,IMPORT_PATH);
                            }

                            $file_info_arr = array(
                                "channelid" => 0,
                                "memberid" => 0,
                                "type" => 1,
                                "file" => $FileNM,
                                "errorfile" => $ErrorFileNM,
                                "ipaddress" => $this->input->ip_address(),
                                "totalrow" => $totalrowcount,
                                "createddate" => $createddate,
                                "modifieddate" => $createddate,
                                "addedby" => $addedby,
                                "modifiedby" => $addedby
                            );
                            $this->load->model("Import_excel_model", "Import_excel");
                            $this->Import_excel->add($file_info_arr);

                        }
                    } else {
                       
                        $file_info_arr = array(
                            "channelid" => 0,
                            "memberid" => 0,
                            "type" => 1,
                            "file" => $FileNM,
                            "errorfile" => $ErrorFileNM,
                            "ipaddress" => $this->input->ip_address(),
                            "totalrow" => $totalrowcount,
                            "createddate" => $createddate,
                            "modifieddate" => $createddate,
                            "addedby" => $addedby,
                            "modifiedby" => $addedby
                        );
                        $this->load->model("Import_excel_model", "Import_excel");
                        $this->Import_excel->add($file_info_arr);
                        echo 4;
                        // unlinkfile('', $FileNM, IMPORT_PATH);
                      
                    }
                } else {
                    $file_info_arr = array(
                        "channelid" => 0,
                        "memberid" => 0,
                        "type" => 1,
                        "file" => $FileNM,
                        "errorfile" => $ErrorFileNM,
                        "ipaddress" => $this->input->ip_address(),
                        "totalrow" => $totalrowcount,
                        "createddate" => $createddate,
                        "modifieddate" => $createddate,
                        "addedby" => $addedby,
                        "modifiedby" => $addedby
                    );
                    $this->load->model("Import_excel_model", "Import_excel");
                    $this->Import_excel->add($file_info_arr);

                    echo 5;
                }
                // unlinkfile('', $FileNM, IMPORT_PATH);
            } else {
                $file_info_arr = array(
                    "channelid" => 0,
                    "memberid" => 0,
                    "type" => 1,
                    "file" => $FileNM,
                    "errorfile" => $ErrorFileNM,
                    "ipaddress" => $this->input->ip_address(),
                    "totalrow" => $totalrowcount,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $this->load->model("Import_excel_model", "Import_excel");
                $this->Import_excel->add($file_info_arr);
                echo 4;
                // unlinkfile('', $FileNM, IMPORT_PATH);
               
            }
        }
    }
    public function exportadminproduct()
    {
        //$this->load->model('Product_model', 'Product');
        $categoryid = $_REQUEST['categoryid'];
        $brandid = $_REQUEST['brandid'];
        $producttype = $_REQUEST['producttype'];

        $data[] = $this->Product->getProductDataForExport($categoryid, $brandid, $producttype);
        $data[] = $this->Product->getProductPriceDataForExport($categoryid, $brandid, $producttype);

        $headerstyle = array("A1:S1", "A1:AP1");
        $title = array("Product", "Price");
        $headings = array(
            array("Category Name *", "Product Name *", "Link", "Short Description", "Description *", "Hsn Code(%)", "Priority *", "Brand", "Image", "Quantity Type (1=>Range Base,0=>Multiplication)", "Points For Seller", "Points For Buyer", "Tag", "Product Display on Front or Not", "Activate (1=>Yes,0=>No)", "Meta Title", "Meta Keywords", "Meta Description"),
            array("Product Code", "Product Name *", "Variant", "Stock", "SKU *", "Barcode", "Minimum Order Quantity", "Maximum Order Quantity", "Minimum Stock Limit", "Weight (kg)", "Add Price In Price List (1=>Yes, 0=>No)", "Price Type (0=>Single Quantity, 1=>Multiple Quantity)", "Price 1 *", "Quantity 1 *", "Discount 1 (%)", "Price 2", "Quantity 2", "Discount 2 (%)", "Price 3", "Quantity 3", "Discount 3 (%)", "Price 4", "Quantity 4", "Discount 4 (%)", "Price 5", "Quantity 5", "Discount 5 (%)", "Price 6", "Quantity 6", "Discount 6 (%)", "Price 7", "Quantity 7", "Discount 7 (%)", "Price 8", "Quantity 8", "Discount 8 (%)", "Price 9", "Quantity 9", "Discount 9 (%)", "Price 10", "Quantity 10", "Discount 10 (%)")
        );
        // print_r($data); exit;
        $this->general_model->exporttoexcelwithmultiplesheet($data, $headerstyle, $title, $headings, "Product.xls");

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product', 'Export to excel product.');
        }
    }
    public function uploadproductfile()
    {
        $this->Product->_fields = "filename";
        $this->Product->_table = tbl_productimage;
        $this->Product->_order = 'id';
        $productimagedata = $this->Product->getRecordByID();
        $productimagedata = array_column($productimagedata, 'filename');

        if ($_FILES["zipfile"]['name'] != '') {
            if ($_FILES["zipfile"]['size'] > UPLOAD_MAX_ZIP_FILE_SIZE) {
                echo 4; // ZIP FILE SIZE IS LARGE
                exit;
            }
            $FileNM = uploadFile('zipfile', 'UPLOAD_PRODUCT_FILE', IMPORT_PATH, "zip");

            if ($FileNM !== 0) {
                if ($FileNM == 2) {
                    echo 3; //image not uploaded
                    exit;
                }
            } else {
                echo 2; //INVALID ATTACHMENT FILE
                exit;
            }
        }

        $zip = new ZipArchive;
        $empty = array();
        if ($zip->open(IMPORT_PATH . $FileNM) === TRUE) {

            //unzip into the folders
            for ($i = 0; $i < $zip->numFiles; $i++) {

                $OnlyFileName = $zip->getNameIndex($i);
                $FullFileName = $zip->statIndex($i);

                if (!($FullFileName['name'][strlen($FullFileName['name']) - 1] == "/")) {
                    if (preg_match('#\.(bmp|bm|jpg|jpeg|png|jpe)$#i', $OnlyFileName)) {
                        if (in_array($FullFileName['name'], $productimagedata)) {
                            copy('zip://' . IMPORT_PATH . $FileNM . '#' . $OnlyFileName, PRODUCT_PATH . $FullFileName['name']);

                            $this->general_model->resizeimage(PRODUCT_LOCAL_PATH, $FullFileName['name'], PRODUCT_IMG_WIDTH, PRODUCT_IMG_HEIGHT, 1);

                            $this->general_model->compress(PRODUCT_PATH . $FullFileName['name'], PRODUCT_PATH . $FullFileName['name'], FILE_COMPRESSION);
                        } else {
                            echo $FullFileName['name'] . " image file not match with database !";
                            if (!in_array($i, $empty)) {
                                $empty[] = $i;
                            }
                        }
                    } else {
                        echo $FullFileName['name'] . " not an image file !";
                        if (!in_array($i, $empty)) {
                            $empty[] = $i;
                        }
                    }
                }
            }
            $zip->close();
        }

        unlinkfile('', $FileNM, IMPORT_PATH);
        if (empty($empty)) {
            echo 1;
        }
    }
    public function getVariantByProductIdForAdmin()
    {
        $PostData = $this->input->post();
        $productdata = $this->Product->getVariantByProductIdForAdmin($PostData['productid']);
        echo json_encode($productdata);
    }
    public function getactiveproduct()
    {
        $PostData = $this->input->post();
        if (isset($PostData["term"])) {
            $Productdata = $this->Product->searchproduct(1, $PostData["term"]);
        } else if (isset($PostData["ids"])) {
            $Productdata = $this->Product->searchproduct(0, $PostData["ids"]);
        }

        echo json_encode($Productdata);
    }
    public function getCustomerDetail()
    {
        $PostData = $this->input->post();

        $CustomerData = $this->Customer->getCustomerName($PostData['customerid']);
        echo json_encode($CustomerData);
    }
    public function getProductsByIDs()
    {
        $PostData = $this->input->post();

        $ProductData = $this->Product->getAllProductsDetail($PostData['productid']);
        echo json_encode($ProductData);
    }
}
