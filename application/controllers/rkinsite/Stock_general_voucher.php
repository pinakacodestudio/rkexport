<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Stock_general_voucher extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Stock_general_voucher');
        $this->load->model('Stock_general_voucher_model', 'Stock_general_voucher');
    }

    public function index() {
        $this->viewData['title'] = "Stock General Voucher";
        $this->viewData['module'] = "stock_general_voucher/Stock_general_voucher";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getAllProductList();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Stock General Voucher','View stock general voucher.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("stock_general_voucher", "pages/stock_general_voucher.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Stock_general_voucher->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'stock-general-voucher/stock-general-voucher-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->voucherproductsid.',"","Stock&nbsp;General&nbsp;Voucher","'.ADMIN_URL.'stock-general-voucher/delete-mul-stock-general-voucher") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->voucherproductsid.'" type="checkbox" class="checkradios" name="check'.$datarow->voucherproductsid.'" id="check'.$datarow->voucherproductsid.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->voucherproductsid.'"></label></div>';
            }

            if($datarow->narration!=""){

                $type = '<span class="label label-'.(($datarow->type==1)?"success":"danger").'"><a style="color: #fff;" href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Narration" data-content="'.$datarow->narration.'">'.(($datarow->type==1)?"Increment":"Decrement").'</a></span>';
            }else{
                $type = ($datarow->type==1)?"<span class='label label-success'>Increment</span>":"<span class='label label-danger'>Decrement</span>";
            }

            $row[] = $datarow->serial_number;
            $row[] = $datarow->voucherno;
            $row[] = $this->general_model->displaydate($datarow->voucherdate);
            $row[] = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" target="_blank">'.$datarow->productname.'</a>';
            $row[] = numberFormat($datarow->price,2,',');
            $row[] = $datarow->quantity;
            $row[] = numberFormat($datarow->totalprice,2,',');
            $row[] = $type;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Stock_general_voucher->count_all(),
                        "recordsFiltered" => $this->Stock_general_voucher->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_stock_general_voucher() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Stock General Voucher";
        $this->viewData['module'] = "stock_general_voucher/Add_stock_general_voucher";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getAllProductList();
        
        $this->load->model('Narration_model', 'Narration');
        $this->viewData['narrationdata'] = $this->Narration->getActiveNarration();

        $this->viewData['voucherno'] = $this->general_model->generateTransactionPrefixByType(4);
        
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_stock_general_voucher", "pages/add_stock_general_voucher.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function stock_general_voucher_add() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $voucherno = $PostData['voucherno'];
        $voucherdate = $this->general_model->convertdate($PostData['voucherdate']);
        $this->load->model('Product_model', 'Product');

        $this->Stock_general_voucher->_where = array('voucherno' => $voucherno);
        $Count = $this->Stock_general_voucher->CountRecords();

        $json = array();
        if($Count==0){
                
            $InsertData = array('voucherno' => $voucherno,
                                'voucherdate' => $voucherdate,
                                'createddate' => $createddate,
                                'addedby' => $addedby,                              
                                'modifieddate' => $createddate,                             
                                'modifiedby' => $addedby 
                            );
            
            $VoucherID = $this->Stock_general_voucher->Add($InsertData);
            if($VoucherID){
                $this->general_model->updateTransactionPrefixLastNoByType(4);
                $productidArr = $PostData['productid'];
                
                if(!empty($productidArr)){
                    $insertData = $inserttransactionproductstock = array();
                    foreach($productidArr as $i=>$productid){
                        
                        $id = $PostData['productrow'][$i];
                        $priceid = $PostData['priceid'][$i];
                        $type = isset($PostData['type'.$id])?1:0;
                        $quantity = $PostData['qty'][$i];
                        $price = $PostData['price'][$i];
                        $totalprice = $PostData['totalprice'][$i];
                        $narrationid = $PostData['narrationid'][$i];

                        if(!empty($productid) && !empty($priceid) && !empty($quantity)){

                            $insertData = array(
                                "stockgeneralvoucherid"=>$VoucherID,
                                "productid"=>$productid,
                                "priceid"=>$priceid,
                                "type"=>$type,
                                "quantity"=>$quantity,
                                "price"=>$price,
                                "totalprice"=>$totalprice,
                                "narrationid"=>$narrationid
                            );
                            $this->Stock_general_voucher->_table = tbl_stockgeneralvoucherproducts;
                            $VoucherProductID = $this->Stock_general_voucher->Add($insertData);

                            if($VoucherProductID){

                                $action = ($type==1)?0:1;
    
                                $inserttransactionproductstock[] = array("referencetype"=>5,
                                                "referenceid"=>$VoucherProductID,
                                                "stocktype"=>2, 
                                                "stocktypeid"=>$VoucherProductID,
                                                "productid"=>$productid,
                                                "priceid"=>$priceid,
                                                "qty"=>$quantity,
                                                "action"=>$action,
                                                "createddate"=>$this->general_model->convertdatetime($PostData['voucherdate']),
                                                'modifieddate' => $createddate
                                            );
                            }
                        }
                    } 
                    /* if(!empty($insertData)){
                        $this->Stock_general_voucher->_table = tbl_stockgeneralvoucherproducts;
                        $this->Stock_general_voucher->add_batch($insertData);
                    } */
                    if(!empty($inserttransactionproductstock)){
                        $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                        $this->Stock_general_voucher->Add_batch($inserttransactionproductstock);
                    }
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Stock General Voucher','Add new voucher no. '.$voucherno.' stock general voucher.');
                }
                echo json_encode(array("error"=>1,"voucherno"=>$this->general_model->generateTransactionPrefixByType(4)));
            } else {
                echo json_encode(array("error"=>0));
            }
        } else {
            echo json_encode(array("error"=>2)); // raw material request already added
        }
    }
    public function stock_general_voucher_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Stock General Voucher";
        $this->viewData['module'] = "stock_general_voucher/Add_stock_general_voucher";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getAllProductList();

        $this->load->model('Narration_model', 'Narration');
        $this->viewData['narrationdata'] = $this->Narration->getActiveNarration();

        $this->viewData['stockgeneralvoucherdata'] = $this->Stock_general_voucher->getStockGeneralVoucherDataByID($id);
        if(empty($this->viewData['stockgeneralvoucherdata'])){
            redirect(ADMINFOLDER."pagenotfound");
        }
        $this->viewData['stockgeneralvoucherproductdata'] = $this->Stock_general_voucher->getStockGeneralVoucherProductsByVoucherID($id);
        
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("add_stock_general_voucher","pages/add_stock_general_voucher.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_stock_general_voucher() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $stockgeneralvoucherid = $PostData['stockgeneralvoucherid'];
        $voucherno = $PostData['voucherno'];
        $voucherdate = $this->general_model->convertdate($PostData['voucherdate']);
        $this->load->model('Product_model', 'Product');

        $this->Stock_general_voucher->_where = array("id<>"=>$stockgeneralvoucherid,'voucherno' => $voucherno);
        $Count = $this->Stock_general_voucher->CountRecords();

        if($Count==0){
                
            $updateData = array('voucherno' => $voucherno,
                                'voucherdate' => $voucherdate,
                                'modifiedby' => $modifiedby,
                                'modifieddate' => $modifieddate);

            $this->Stock_general_voucher->_where = array('id' =>$stockgeneralvoucherid);
            $isUpdated = $this->Stock_general_voucher->Edit($updateData);
            
            if($isUpdated){

                $productidArr = $PostData['productid'];
                
                if(!empty($productidArr)){
                    $insertData = $updateData = $isUpdateData = $inserttransactionproductstock = $inserttransactionproductstockwithid = $updatetransactionproductstock = array();
                    $index = 0;
                    foreach($productidArr as $i=>$productid){
                        
                        $id = $PostData['productrow'][$i];
                        $priceid = $PostData['priceid'][$i];
                        $type = isset($PostData['type'.$id])?1:0;
                        $quantity = $PostData['qty'][$i];
                        $price = $PostData['price'][$i];
                        $totalprice = $PostData['totalprice'][$i];
                        $narrationid = $PostData['narrationid'][$i];
                        $stockgeneralvoucherproductid = isset($PostData['stockgeneralvoucherproductid'][$i])?$PostData['stockgeneralvoucherproductid'][$i]:0;
                        
                        if(!empty($productid) && !empty($priceid) && !empty($quantity)){
                            if(!empty($stockgeneralvoucherproductid)){
                                
                                $updateData[] = array(
                                    "id"=>$stockgeneralvoucherproductid,
                                    "productid"=>$productid,
                                    "priceid"=>$priceid,
                                    "type"=>$type,
                                    "quantity"=>$quantity,
                                    "price"=>$price,
                                    "totalprice"=>$totalprice,
                                    "narrationid"=>$narrationid
                                );
                               
                                $isUpdateData[] = $stockgeneralvoucherproductid;

                                $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                                $this->Stock_general_voucher->_where = array("referencetype"=>5,"referenceid"=>$stockgeneralvoucherproductid,"stocktype"=>2,"stocktypeid"=>$stockgeneralvoucherproductid);
                                $StockData = $this->Stock_general_voucher->getRecordsByID();
                                if(!empty($StockData)){
                                    $action = ($type==1)?0:1;
                                    $updatetransactionproductstock[] = array(
                                        "id"=> $StockData['id'],
                                        "qty"=>$quantity,
                                        "action"=>$action,
                                        "createddate"=>$this->general_model->convertdatetime($PostData['voucherdate']),
                                        'modifieddate' => $modifieddate
                                    );
                                }else{
                                    $action = ($type==1)?0:1;
    
                                    $inserttransactionproductstockwithid[] = array("referencetype"=>5,
                                                    "referenceid"=>$stockgeneralvoucherproductid,
                                                    "stocktype"=>2, 
                                                    "stocktypeid"=>$stockgeneralvoucherproductid,
                                                    "productid"=>$productid,
                                                    "priceid"=>$priceid,
                                                    "qty"=>$quantity,
                                                    "action"=>$action,
                                                    "createddate"=>$this->general_model->convertdatetime($PostData['voucherdate']),
                                                    'modifieddate' => $modifieddate
                                                );
                                }
                            }else{

                                $insertData[] = array(
                                    "stockgeneralvoucherid"=>$stockgeneralvoucherid,
                                    "productid"=>$productid,
                                    "priceid"=>$priceid,
                                    "type"=>$type,
                                    "quantity"=>$quantity,
                                    "price"=>$price,
                                    "totalprice"=>$totalprice,
                                    "narrationid"=>$narrationid
                                );

                                $action = ($type==1)?0:1;
    
                                $inserttransactionproductstock[] = array("referencetype"=>5,
                                                "referenceid"=>$index,
                                                "stocktype"=>2, 
                                                "stocktypeid"=>$index,
                                                "productid"=>$productid,
                                                "priceid"=>$priceid,
                                                "qty"=>$quantity,
                                                "action"=>$action,
                                                "createddate"=>$this->general_model->convertdatetime($PostData['voucherdate']),
                                                'modifieddate' => $modifieddate
                                            );

                                $index++;
                            }
                        }
                    }

                    $voucherproducts = $this->Stock_general_voucher->getStockGeneralVoucherProductsByVoucherID($stockgeneralvoucherid);
                
                    if(!empty($voucherproducts)){
                        $voucherproductids = array_column($voucherproducts, "id");
                        $resultId = array_diff($voucherproductids, $isUpdateData);

                        if(!empty($resultId)){
                            $this->Stock_general_voucher->_table = tbl_stockgeneralvoucherproducts;
                            $this->Stock_general_voucher->Delete(array("id IN (".implode(",",$resultId).")"=>null));
                        }
                    }
                    if(!empty($insertData)){
                        $this->Stock_general_voucher->_table = tbl_stockgeneralvoucherproducts;
                        $this->Stock_general_voucher->add_batch($insertData);

                        $first_id = $this->writedb->insert_id();
                        $last_id = $first_id + (count($insertData)-1);

                        $i=0;
                        for($id=$first_id;$id<=$last_id;$id++){
                            $orderproductsidsarr[]=$id;

                            if(!empty($inserttransactionproductstock) && $inserttransactionproductstock[$i]['referenceid']==$i){
                                $inserttransactionproductstock[$i]['referenceid'] = $id;
                                $inserttransactionproductstock[$i]['stocktypeid'] = $id;
                            }
                            $i++;
                        }

                        if(!empty($inserttransactionproductstock)){
                            $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                            $this->Stock_general_voucher->Add_batch($inserttransactionproductstock);
                        }
                    }
                    if(!empty($updateData)){
                        $this->Stock_general_voucher->_table = tbl_stockgeneralvoucherproducts;
                        $this->Stock_general_voucher->edit_batch($updateData,"id");
                    }
                    if(!empty($updatetransactionproductstock)){
                        $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                        $this->Stock_general_voucher->Edit_batch($updatetransactionproductstock, "id");
                    }
                    if(!empty($inserttransactionproductstockwithid)){
                        $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                        $this->Stock_general_voucher->Add_batch($inserttransactionproductstockwithid);
                    }
                } 
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Stock General Voucher','Edit voucher no. '.$voucherno.' stock general voucher.');
                }
                echo 1; // Raw material request update successfully
            } else {
                echo 0; // Raw material request unit not updated
            }
        } else {
            echo 2; // Raw material request already added
        }
    }

    public function delete_mul_stock_general_voucher() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        $this->Stock_general_voucher->_table = tbl_stockgeneralvoucher;
        $this->Stock_general_voucher->deleteVouchersByVoucherProductId($ids);

        foreach ($ids as $row) {
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $data = $this->Stock_general_voucher->getStockGeneralVoucherProductDataByID($row);
                
                $this->general_model->addActionLog(3,'Stock General Voucher','Delete '.$data['productname'].' product on stock general voucher.');
            }

            $this->Stock_general_voucher->_table = tbl_stockgeneralvoucherproducts;
            $this->Stock_general_voucher->Delete(array('id'=>$row));

            $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
            $this->Stock_general_voucher->Delete(array("referencetype"=>5,"referenceid"=>$row,"stocktype"=>2,"stocktypeid"=>$row));
        }
        
    }
    
    public function importstockgeneralvoucher(){
        $PostData = $this->input->post();
        
        if($_FILES["attachment"]['name'] != ''){

			$FileNM = uploadFile('attachment', 'IMPORT_FILE', IMPORT_PATH, "ods|xl|xlc|xls|xlsx");
			            
            if($FileNM !== 0){
                if($FileNM==2){
                    echo 3;//image not uploaded
                    exit;
                }
            }else{
                echo 2;//INVALID ATTACHMENT FILE
                exit;
            }

            $insertdata = $insertproductdata = array();
            $file_data = $this->upload->data();
            $file_path =  IMPORT_PATH.$FileNM;

            $this->load->library('excel');
            $inputFileType = PHPExcel_IOFactory::identify($file_path);
            $objReader =PHPExcel_IOFactory::createReader($inputFileType);     //For excel 2003 
            //$objReader= PHPExcel_IOFactory::createReader('Excel2007');    // For excel 2007     

            //Set to read only
            $objReader->setReadDataOnly(true);        

            //Load excel file
            $objPHPExcel=$objReader->load($file_path);
            

            $totalrows=$objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Number of rows avalable in excel        
            
            $objWorksheet=$objPHPExcel->setActiveSheetIndex(0);
            
            $column0 = $objWorksheet->getCellByColumnAndRow(0,1)->getValue();
            $column1 = $objWorksheet->getCellByColumnAndRow(1,1)->getValue();
            $column2 = $objWorksheet->getCellByColumnAndRow(2,1)->getValue();
            $column3 = $objWorksheet->getCellByColumnAndRow(3,1)->getValue();
            $column4 = $objWorksheet->getCellByColumnAndRow(4,1)->getValue();
            $column5 = $objWorksheet->getCellByColumnAndRow(5,1)->getValue();
            $column6 = $objWorksheet->getCellByColumnAndRow(6,1)->getValue();
            
            if($column0=="Voucher Date *" && $column1=="Product Name *" && $column2=="Variant" && $column3=="Quantity *" && 
                $column4=="Price *" && $column5=="Type (1=>Increment,0=>Decrement) *" && $column6=="Narration"){

                if($totalrows>1){
                    $error = $voucherdatearray = $voucheridarray = $isuniquefieldsarray = $inserttransactionproductstock = array();

                    $addedby = $this->session->userdata(base_url().'ADMINID');
                    $createddate = $this->general_model->getCurrentDateTime();

                    $this->load->model('Product_model', 'Product');
                    $this->load->model('Variant_model', 'Variant');
                    $this->load->model('Product_prices_model', 'Product_prices');
                    $this->load->model('Product_combination_model', 'Product_combination');
                    $this->load->model('Narration_model', 'Narration');

                    $this->Product->_fields = "id,name";
                    $productdata = $this->Product->getRecordByID();
                    $productidarr = array_column($productdata,'id');
                    $productnamearr = array_column($productdata,'name');
                    
                    $variantdata = $this->Variant->getVariantDataForImport();
                    $variantidarr = array_column($variantdata,'id');
                    $variantnamearr = array_column($variantdata,'variantname');

                    $this->Product_prices->_fields = "id,productid,CONCAT(productid,'|',id) as name";
                    $this->Product_prices->_order = 'id';
                    $productpricedata = $this->Product_prices->getRecordByID();
                    $productpriceidarr = array_column($productpricedata,'id');
                    $productpriceproductidarr = array_column($productpricedata,'productid');
                    
                    $this->Product_combination->_fields = "id,priceid,variantid,CONCAT(priceid,'|',variantid) as name";
                    $productcombinationdata = $this->Product_combination->getRecordByID();
                    $productcombinationvariantidarr = array_column($productcombinationdata,'variantid');
                    $productcombinationpriceidarr = array_column($productcombinationdata,'priceid');
                    
                    $this->Narration->_fields = "id,narration";
                    $this->Narration->_where = array("channelid"=>0,"memberid"=>0);
                    $narrationdata = $this->Narration->getRecordByID();
                    $narrationidarr = array_column($narrationdata,'id');
                    $narrationarr = array_column($narrationdata,'narration');

                    for($i=2;$i<=$totalrows;$i++){
                        
                        $voucherdate = trim($objWorksheet->getCellByColumnAndRow(0,$i)->getValue());
                        $voucherdate = (!empty($voucherdate))?$this->general_model->convertdate(date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($voucherdate))):"";
                        $productname = trim($objWorksheet->getCellByColumnAndRow(1,$i)->getValue());
                        $variant = trim($objWorksheet->getCellByColumnAndRow(2,$i)->getValue());
                        $qty = trim($objWorksheet->getCellByColumnAndRow(3,$i)->getValue());
                        $price = trim($objWorksheet->getCellByColumnAndRow(4,$i)->getValue());
                        $type = trim($objWorksheet->getCellByColumnAndRow(5,$i)->getValue());
                        $narration = trim($objWorksheet->getCellByColumnAndRow(6,$i)->getValue());
                        $productid = $priceid = 0;
                        
                        $isvalid = 1;
                        if(empty($voucherdate)){
                            echo "Row no. ".$i." voucher date is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
                        }
                        if(empty($productname)){
                            echo "Row no. ".$i." product name is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
						}else{
                            if (!in_array($productname, $productnamearr)) {
                                echo "Row no. ".$i." product name not found !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }else{
                                $productid = $productidarr[array_search($productname,$productnamearr)];
                            }
                        }
                        if($variant!=""){
                            $variants = array_map('trim', explode('|',$variant));
                            $variant_diff = array_diff($variants, $variantnamearr);

                            if(!empty($variant_diff)){
                                echo "Row no. ".$i." variant not found !<br>";
                                $isvalid = 0;
                                $error[] = $i;  
                            }else{
                                $priceids = array();
                                foreach($variants as $value){
                                    $variantid = $variantidarr[array_search($value,$variantnamearr)];
                                    $priceids[] = $productcombinationpriceidarr[array_search($variantid,$productcombinationvariantidarr)];
                                }
                                $priceid = array_filter(array_unique($priceids))[0];
                            }
                        }else{
                            if($productid!=0){
                                $priceid = $productpriceidarr[array_search($productid,$productpriceproductidarr)];
                            }
                        }
                        if(empty($qty)){
                            echo "Row no. ".$i." quantity is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
						}
                        if(empty($price)){
                            echo "Row no. ".$i." price is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
						}
                        if($type==""){
                            echo "Row no. ".$i." type is empty !<br>";
                            $isvalid = 0;
                            $error[] = $i;
						}
                        if($isvalid){
                            $unique_string = $voucherdate."_".$productid.'_'.$priceid.'_'.number_format($price,2,'.','');

                            if(!in_array($unique_string, $isuniquefieldsarray)){
                                $isuniquefieldsarray[] = $unique_string;
                                
                                $narrationid = 0;
                                if($narration!=""){
                                    if (in_array($narration, $narrationarr)) {
                                        $narrationid = $narrationidarr[array_search($narration,$narrationarr)];
                                    }else{
                                        
                                        $InsertData = array('channelid'=>0,
                                                            'memberid'=>0,
                                                            'narration' => $narration,
                                                            'status'=>1,
                                                            'usertype'=>0,
                                                            'createddate' => $createddate,
                                                            'addedby' => $addedby,                              
                                                            'modifieddate' => $createddate,                             
                                                            'modifiedby' => $addedby 
                                                        );
                                    
                                        $narrationid = $this->Narration->Add($InsertData);
                                    
                                        $this->Narration->_fields = "id,narration";
                                        $this->Narration->_order = "id";
                                        $this->Narration->_where = array("channelid"=>0,"memberid"=>0);
                                        $narrationdata = $this->Narration->getRecordByID();
                                        $narrationidarr = array_column($narrationdata,'id');
                                        $narrationarr = array_column($narrationdata,'narration');
                                    }
                                }
                                if(!in_array($voucherdate, $voucherdatearray)){
                                    $voucherdatearray[] = $voucherdate;
                                    
                                    $insertdata[] = array("voucherdate"=>$voucherdate);
                                }
    
                                $insertproductdata[] = array("stockgeneralvoucherid"=>$voucherdate,
                                                            "productid"=>$productid,
                                                            "priceid"=>$priceid,
                                                            "quantity"=>$qty,
                                                            "type"=>$type,
                                                            "narrationid"=>$narrationid,
                                                            "price"=>$price,
                                                            "totalprice"=>($price * $qty)
                                                        );

                                
                                $inserttransactionproductstock[] = array("referencetype"=>5,
                                                "referenceid"=>$unique_string,
                                                "stocktype"=>2, 
                                                "stocktypeid"=>$unique_string,
                                                "productid"=>$productid,
                                                "priceid"=>$priceid,
                                                "qty"=>$qty,
                                                "action"=>($type==1)?0:1,
                                                "createddate"=>$this->general_model->convertdatetime($voucherdate),
                                                'modifieddate' => $createddate
                                            );
                            
                            }else{
                                echo "Row no. ".$i." add product with different variant & price !<br>";
                                $isvalid = 0;
                                $error[] = $i;
                            }
                        }
                    }
                    // print_r($inserttransactionproductstock); exit;
                    if(empty($error)){
                        if(!empty($insertdata)){
                            $voucherids = array();
                            $this->Stock_general_voucher->_table = tbl_stockgeneralvoucher;
                            
                            for($j=0; $j<count($insertdata); $j++){
                                $voucherno = $this->general_model->generateTransactionPrefixByType(4);
                            
                                $this->Stock_general_voucher->_where = array('voucherno' => $voucherno);
                                $Count = $this->Stock_general_voucher->CountRecords();
    
                                if($Count == 0){
                                    
                                    $InsertVoucherData = array("channelid"=>0,
                                                            "memberid"=>0,
                                                            "voucherno"=>$voucherno,
                                                            "voucherdate"=>$this->general_model->convertdate($insertdata[$j]['voucherdate']),
                                                            "createddate"=>$createddate,
                                                            "modifieddate"=>$createddate,
                                                            "addedby"=>$addedby,
                                                            "modifiedby"=>$addedby
                                                        );
    
                                    $VoucherID = $this->Stock_general_voucher->Add($InsertVoucherData);
    
                                    if($VoucherID){
                                        $this->general_model->updateTransactionPrefixLastNoByType(4);
    
                                        $voucherids[] = $VoucherID;
                                    }
                                }

                            }

                            $voucherdata = $this->Stock_general_voucher->getStockGeneralVoucherDataByIDs($voucherids);
                            $voucherdatearray = array_column($voucherdata,'voucherdate');
                            $voucheridarr = array_column($voucherdata,'id');

                            if(!empty($insertproductdata)){

                                foreach($insertproductdata as $k => $row){
                                    if(!empty($row['stockgeneralvoucherid'])){
                                        if(!empty($voucherdatearray) && in_array($row['stockgeneralvoucherid'],$voucherdatearray)){
                                            $insertproductdata[$k]['stockgeneralvoucherid'] = $voucheridarr[array_search($row['stockgeneralvoucherid'],$voucherdatearray)];
                                        }else{
                                            unset($insertproductdata[$k]);
                                        }
                                    }
                                }

                                if (!empty($insertproductdata)) {
                                    $this->Stock_general_voucher->_table = tbl_stockgeneralvoucherproducts;
                                    $this->Stock_general_voucher->add_batch($insertproductdata);

                                    $firstbatch_id = $this->writedb->insert_id();
                                    $lastbatch_id = $firstbatch_id + (count($insertproductdata)-1);
                                        
                                    $voucherproductids = array();
                                    for($pr=$firstbatch_id; $pr<=$lastbatch_id;$pr++){
                                        $voucherproductids[] = $pr;
                                    }

                                    $voucherproductdata = $this->Stock_general_voucher->getStockGeneralVoucherProductDataByIDs($voucherproductids);
                                    $uniquestringarray = array_column($voucherproductdata,'uniquestring');
                                    $voucherproductidarr = array_column($voucherproductdata,'id');

                                    foreach($inserttransactionproductstock as $k => $row){
                                        if(!empty($row['referenceid'])){
                                            if(!empty($uniquestringarray) && in_array($row['referenceid'],$uniquestringarray)){
                                                $inserttransactionproductstock[$k]['referenceid'] = $voucherproductidarr[array_search($row['referenceid'],$uniquestringarray)];
                                                $inserttransactionproductstock[$k]['stocktypeid'] = $voucherproductidarr[array_search($row['stocktypeid'],$uniquestringarray)];
                                            }else{
                                                unset($inserttransactionproductstock[$k]);
                                            }
                                        }
                                    }
                                    if(!empty($inserttransactionproductstock)){
                                        $this->Stock_general_voucher->_table = tbl_transactionproductstockmapping;
                                        $this->Stock_general_voucher->Add_batch($inserttransactionproductstock);
                                    }
                                }
                            }
                        }
                        echo 1;
                    }
                }else{
                    echo 5;
				}
				unlinkfile('', $FileNM, IMPORT_PATH);
            }else{
                echo 4;
                unlinkfile('', $FileNM, IMPORT_PATH);
                exit;
            }
        }
    }
}?>