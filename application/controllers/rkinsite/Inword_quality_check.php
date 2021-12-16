<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Inword_quality_check extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Inword_quality_check_model', 'Inword_quality_check');
        
        
        $this->load->model('Side_navigation_model','Side_navigation');
        $this->viewData = $this->getAdminSettings('submenu', 'Inword_quality_check');
    }

    public function index() {
        $this->viewData['title'] = "Inword Q.C.";
        $this->viewData['module'] = "inword_quality_check/inword_quality_check";
        $this->viewData['VIEW_STATUS'] = "1";

        // if($this->viewData['submenuvisibility']['managelog'] == 1){
        //     $this->general_model->addActionLog(4,'Product','View product.');
        // }
        $this->load->model("Vendor_model","Vendor");
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');

        $this->viewData['grndata'] = $this->Inword_quality_check->getGoodsReceivedNotesNumber();

        $this->viewData['orderdata'] = $this->Inword_quality_check->getallorders();

        // pre($this->viewData['orderdata']);
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        // $this->load->model('Brand_model','Brand');
        // $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->admin_headerlib->add_javascript("Inword_quality_check", "pages/inword_quality_check.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        
        $this->load->model('Inword_quality_check_model','Inword_quality_check');
        // $this->load->model("Product_combination_model","Product_combination");
        // $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        // $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        // $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        // $list()
        $list = $this->Inword_quality_check->get_datatables();
        // pre($list);
        $data = array();       
        $counter = $_POST['start'];
        // $pokemon_doc = new DOMDocument();
       
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
            $status =$datarow['status'];
            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown1">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="changequalitystatus(1,'.$datarow['id'].')">Partially</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="changequalitystatus(2,'.$datarow['id'].')">Complete</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="changequalitystatus(3,'.$datarow['id'].')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown1">Partially <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                            <a onclick="changequalitystatus(2,'.$datarow['id'].')">Complete</a>
                            </li>
                            <li id="dropdown-menu">
                            <a onclick="changequalitystatus(3,'.$datarow['id'].')">Cancel</a>
                            </li>
                        </ul>';
            }else if($status == 2){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown1">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                            <a onclick="changequalitystatus(3,'.$datarow['id'].')">Cancel</a>
                            </li>
                        </ul>';
            }else if($status == 3){
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }
            $inwordstatus = '<div class="dropdown">'.$dropdownmenu.'</div>';

            $actions .= '<a href="'.ADMIN_URL.'inword-quality-check/view-inword-quality-check/'.$datarow['id'].'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';           
            $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'inword-quality-check/edit-inword-quality-check/'.$datarow['id'].'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow['id'].',"'.ADMIN_URL.'inword-quality-check/check-inword-qc-use","Inword&nbsp;Q.C.","'.ADMIN_URL.'inword-quality-check/delete-mul-inword","inwordtable") >'.delete_text.'</a>';
            $checkbox = '<div class="checkbox"><input value="'.$datarow['id'].'" type="checkbox" class="checkradios" name="check'.$datarow['id'].'" id="check'.$datarow['id'].'" onchange="singlecheck(this.id)"><label for="check'.$datarow['id'].'"></label></div>';


            $row[] = ++$counter;
            $row[] = $datarow['vendorname'];
            $row[] = $datarow['orderid'];
            $row[] = $datarow['grnnumber'];
            $row[] = $this->general_model->displaydate($datarow['receiveddate']);
            $row[] = $inwordstatus;
            $row[] = $this->general_model->displaydatetime($datarow['createddate']);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }

            $output = array(
                            "draw" => $_POST['draw'],
                            "recordsTotal" => $this->Inword_quality_check->count_all(),
                            "recordsFiltered" => $this->Inword_quality_check->count_filtered(),
                            "data" => $data,
                            );
            echo json_encode($output);
    }
    
    public function add_inword_quality_check() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Inword_quality_check');      
        $this->viewData['title'] = "Add Inword Quality Check";
        $this->viewData['module'] = "inword_quality_check/add_inword_quality_check";   
        $this->viewData['VIEW_STATUS'] = "0";            

        $this->load->model("Vendor_model","Vendor");
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');
        
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("inword-quality-check", "pages/add_inword_quality_check.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function edit_inword_quality_check($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Inword Quality Check";
        $this->viewData['module'] = "inword_quality_check/add_inword_quality_check";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model("Vendor_model","Vendor");
        $this->viewData['vendordata'] = $this->Vendor->getActiveVendorData('withcodeormobile');

        $this->viewData['inworddata'] = $this->Inword_quality_check->getInworddatabyID($id);
                
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("inword-quality-check", "pages/add_inword_quality_check.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function inword_quality_check_add() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();  
        // print_r($PostData); exit;
        $this->load->model("Inword_quality_check_model","Inword_quality_check");
        
        $grnid = $PostData['grnid'];
        $createddate = $this->general_model->getCurrentDateTime();
        $inworddate = $this->general_model->convertdatetime($PostData['inworddate']);
        // echo $inworddate;
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $productsid = $PostData['transactionproductsid'];
        $visuallycheckedqty = $PostData['visuallycheckedqty'];
        $dimensioncheckedqty = $PostData['dimensioncheckedqty'];
        $visuallydefectqty =$PostData['visuallydefectqty'];
        $dimensiondefectqty = $PostData['dimensiondefectqty'];
        $transactionproductsid = $PostData['transactionproductsid'];
        $Filetext = $PostData['Filetext'];
        $inwordremarks = $PostData['inwordremarks'];
        // print_r($transactionproductsid); exit;
        if(!is_dir(INWORD_PATH)){
            @mkdir(INWORD_PATH);
        }
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                // echo $key
                if (isset($_FILES['qualitycheckfile'.($key+1).'']['name']) && $_FILES['qualitycheckfile'.($key+1).'']['name'] != '') {
                    if($_FILES["qualitycheckfile".($key+1).""]['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $qualitycheckfile = uploadFile('qualitycheckfile'.($key+1).'', 'INWORD_IMGPDF', INWORD_PATH, '*', '', $compress, INWORD_LOCAL_PATH, '', '', 0);
                    if ($qualitycheckfile !== 0) {
                        if ($qualitycheckfile == 2) {
                            echo 3;
                            exit;
                        }
                    } else {
                        echo 4;
                        exit;
                    }
                }
            }
        }
        $InsertData = array('grnid' => $grnid,
                        'createddate' => $inworddate,
                        'addedby' => $addedby,
                        'modifeddate' => $createddate,
                        'modififedby' => $addedby,
                        'remarks' => $inwordremarks,
                        'status' =>0
                        );
        $InsertData = array_map('trim',$InsertData);
        $insertid = $this->Inword_quality_check->add($InsertData);
        
        if($insertid){

            foreach($productsid as $key=>$value){
                // echo($_FILES['qualitycheckfile3']['name']);exit;
                $qualitycheckfile = "";
                $compress = 0;

                if(isset($_FILES['qualitycheckfile'.($key+1).'']['name']) && $_FILES['qualitycheckfile'.($key+1).'']['name'] != ''){
                    if($_FILES["qualitycheckfile".($key+1).""]['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $qualitycheckfile = uploadFile('qualitycheckfile'.($key+1).'', 'INWORD_IMGPDF', INWORD_PATH, '*', '', $compress, INWORD_LOCAL_PATH);
                    if ($qualitycheckfile !== 0) {
                        if ($qualitycheckfile == 2) {
                            echo 3;
                            exit;
                        }
                    } else {
                        echo 4;
                        exit;
                    }
                }   

                if(isset($PostData['visuallycheck'.($key+1)]) || isset($PostData['dimensioncheck'.($key+1)])){
                $insertdata = array('inwordid' =>$insertid,
                                    'transactionproductsid' =>$transactionproductsid[$key],
                                    'visuallycheckedqty' =>$visuallycheckedqty[$key],
                                    'visuallydefectqty' =>$visuallydefectqty[$key],
                                    'dimensioncheckedqty' =>$dimensioncheckedqty[$key],
                                    'dimensiondefectqty' =>$dimensiondefectqty[$key],
                                    'dimensionchecked' =>isset($PostData['dimensioncheck'.($key+1)])?1:0,
                                    'visuallychecked' =>isset($PostData['visuallycheck'.($key+1)])?1:0,
                                    'filename' =>$qualitycheckfile
                                    );
                $insertdata = array_map('trim',$insertdata);
                $this->Inword_quality_check->_table=tbl_inwordqcmapping;
                $this->Inword_quality_check->add($insertdata);
                }
            }
            echo 1;  
        }else{
            echo 0;
        }
    }

    public function update_status(){
        $PostData = $this->input->post();
        // pre($PostData);
        $status = $PostData['status'];
        $inwordId = $PostData['inwordId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifeddate' => $modifieddate, 
            'modififedby'=>$modifiedby
        );  
       
        $updateData = array_map('trim',$updateData);
        // pre($updateData);
        $this->Inword_quality_check->_where = array("id" => $inwordId);
        $update = $this->Inword_quality_check->Edit($updateData);
        if($update) {
            $this->general_model->addActionLog(2,'Inword','Change status '.$inwordId.' on quality check.');
            
            echo 1;    
        }else{
            echo 0;
        }
    }

    public function inword_quality_check_edit() {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $this->load->model("Inword_quality_check_model","Inword_quality_check");
        $inwordid = $PostData['inwordid'];
        $modifeddate = $this->general_model->getCurrentDateTime();
        $inworddate = $this->general_model->convertdatetime($PostData['inworddate']);
        $modififedby = $this->session->userdata(base_url().'ADMINID');
        // $productsid = $PostData['transactionproductsid'];
        $visuallycheckedqty = $PostData['visuallycheckedqty'];
        $dimensioncheckedqty = $PostData['dimensioncheckedqty'];
        $visuallydefectqty =$PostData['visuallydefectqty'];
        $dimensiondefectqty = $PostData['dimensiondefectqty'];
        $transactionproductsid = $PostData['transactionproductsid'];
        $oldFiletext = $PostData['oldFiletext'];
        $inwordremarks = $PostData['inwordremarks'];
        $mappingid = $PostData['mappingid'];
        // print_r($oldFiletext);exit;

        if(!is_dir(INWORD_PATH)){
            @mkdir(INWORD_PATH);
        }
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                // echo $key;
                if (isset($_FILES[$key]['name']) && $_FILES[$key]['name'] != '') {
                    if($_FILES["qualitycheckfile".($key+1).""]['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $qualitycheckfile = uploadFile($key, 'INWORD_IMGPDF', INWORD_PATH, '*', '', $compress, INWORD_LOCAL_PATH, '', '', 0);
                    if ($qualitycheckfile !== 0) {
                        if ($qualitycheckfile == 2) {
                            echo 3;
                            exit;
                        }
                    } else {
                        echo 4;
                        exit;
                    }
                }
            }
        }
            $UpdateData = array(
                'createddate' => $inworddate,
                'remarks' => $inwordremarks,
                'modifeddate' => $modifeddate,
                'modififedby' => $modififedby
            );
            $UpdateData = array_map('trim',$UpdateData);
            
            $this->Inword_quality_check->_table = tbl_inwordqc;
            $this->Inword_quality_check->_where = array("id" => $inwordid);
            $Updateid = $this->Inword_quality_check->Edit($UpdateData);
            
            foreach($transactionproductsid as $key=>$value){
                
                $qualitycheckfile = "";
                $compress = 0;
                $oldqualitycheckfile = $oldFiletext[$key];
                
                $compress = 0;

                $qualitycheckfile = $oldqualitycheckfile;
                if(isset($_FILES['qualitycheckfile'.($key+1).'']['name']) && $_FILES['qualitycheckfile'.($key+1).'']['name'] != '' && $oldqualitycheckfile != ''){
                    if($_FILES['qualitycheckfile'.($key+1).'']['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                   
                    $qualitycheckfile = reuploadfile('qualitycheckfile'.($key+1), 'INWORD_IMGPDF', $oldqualitycheckfile ,INWORD_PATH,"*", '', $compress, INWORD_LOCAL_PATH);
                    
                    if($qualitycheckfile !== 0){
                        if($qualitycheckfile==2){
                            echo 3;// file not uploaded
                            exit;
                        }
                    } else {
                        echo 4; //INVALID TYPE
                        exit;
                    } 	
                }else if(isset($_FILES['qualitycheckfile'.($key+1).'']['name']) && $_FILES['qualitycheckfile'.($key+1).'']['name'] != '' && $oldqualitycheckfile == ''){
                    // echo "upload";
                    if($_FILES['qualitycheckfile'.($key+1).'']['type'] != 'application/pdf'){
                        $compress = 1;
                    }

                    $qualitycheckfile = uploadFile('qualitycheckfile'.($key+1), 'INWORD_IMGPDF', INWORD_PATH, '*', '', $compress, INWORD_LOCAL_PATH);         
                    if($qualitycheckfile !== 0){
                        if($qualitycheckfile==2){
                            echo 3;// file not uploaded
                            exit;
                        }
                    } else {
                        echo 4; //INVALID TYPE
                        exit;
                    } 
                    // echo "*";
                }
                
                if(!empty($mappingid[$key])){
                    if(isset($PostData['visuallycheck'.($key+1)]) || isset($PostData['dimensioncheck'.($key+1)])){

                        $updateData =array('visuallycheckedqty' =>$visuallycheckedqty[$key],
                            'visuallydefectqty' =>$visuallydefectqty[$key],
                            'dimensioncheckedqty' =>$dimensioncheckedqty[$key],
                            'dimensiondefectqty' =>$dimensiondefectqty[$key],
                            'transactionproductsid' =>$transactionproductsid[$key],
                            'dimensionchecked' =>isset($PostData['dimensioncheck'.($key+1)])?1:0,
                            'visuallychecked' =>isset($PostData['visuallycheck'.($key+1)])?1:0,
                            'filename' =>$qualitycheckfile
                        );
                    }else{

                        $reportdata = $this->Inword_quality_check->getProductReportbyinwordIDAndTransactionproductid($inwordid,$transactionproductsid[$key]);
                        unlinkfile('INWORD_IMGPDF',$reportdata['filename'],INWORD_PATH);

                        $updateData =array('visuallycheckedqty' => 0,
                            'visuallydefectqty' => 0,
                            'dimensioncheckedqty' => 0,
                            'dimensiondefectqty' => 0,
                            'transactionproductsid' =>$transactionproductsid[$key],
                            'dimensionchecked' => 0,
                            'visuallychecked' => 0,
                            'filename' => ''
                        );    
                    }
                    $updateData = array_map('trim',$updateData);
                    // print_r($updateData);
                    $this->Inword_quality_check->_table=tbl_inwordqcmapping;
                    $this->Inword_quality_check->_where = array("inwordid" => $inwordid,"id"=>$mappingid[$key]);
                    $this->Inword_quality_check->Edit($updateData);
                }else{

                    if(isset($PostData['visuallycheck'.($key+1)]) || isset($PostData['dimensioncheck'.($key+1)])){

                        $insertdata = array('inwordid' =>$inwordid,
                            'transactionproductsid' =>$transactionproductsid[$key],
                            'visuallycheckedqty' =>$visuallycheckedqty[$key],
                            'visuallydefectqty' =>$visuallydefectqty[$key],
                            'dimensioncheckedqty' =>$dimensioncheckedqty[$key],
                            'dimensiondefectqty' =>$dimensiondefectqty[$key],
                            'dimensionchecked' =>isset($PostData['dimensioncheck'.($key+1)])?1:0,
                            'visuallychecked' =>isset($PostData['visuallycheck'.($key+1)])?1:0,
                            'filename' =>$qualitycheckfile
                        );
                        $insertdata = array_map('trim',$insertdata);
                        $this->Inword_quality_check->_table=tbl_inwordqcmapping;
                        $this->Inword_quality_check->add($insertdata);
                    }
                }
            }
            echo 2;
            
    }

    public function printInwordqcDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
       
        $inwordid = $PostData['id'];
        
        $PostData['headerdata'] = $this->Inword_quality_check->getHeaderdatabyID($inwordid);
        $productdata = $this->Inword_quality_check->getProductbyinwordID($inwordid);

        $product = $this->Inword_quality_check->getProductnamebyinwordID($inwordid);

       
        foreach($productdata as $key => $value){
            
            $productData[] = array('visuallycheckedqty'=>$productdata[$key]['visuallycheckedqty'],
                                 'dimensioncheckedqty'=>$productdata[$key]['dimensioncheckedqty'],
                                 'visuallydefectqty'=>$productdata[$key]['visuallydefectqty'],
                                 'dimensiondefectqty'=>$productdata[$key]['dimensiondefectqty'],
                                 'filename'=>$productdata[$key]['filename'],
                                 'mappingid'=>$productdata[$key]['mappingid'],
                                 'productname'=>$product[$key]['name'],
                                 'qty'=>$product[$key]['quantity']
                                );
        }
        $PostData['productdetails'] = $productData;
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = "inword-q.c.";
        $PostData['heading'] = "Inword Q.C.";
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."inword_quality_check/Printinwordqcformat.php",$PostData,true);
        
        echo json_encode($html); 
    }

    public function getProductdatabyinwordID(){
        $PostData = $this->input->post();
        
        $inwordid = $PostData['inwordid'];
        // $productdata = $this->Inword_quality_check->getProductbyinwordID($inwordid);
        $productData = $this->Inword_quality_check->getProductbyinwordID($inwordid);
        
        echo json_encode($productData);
    }
    public function getProductdatabyGRN(){
        $PostData = $this->input->post();
        
        $grnid = $PostData['grnid'];        
        $inwordid = $this->Inword_quality_check->getInwordIdbyGRNID($grnid);
        $productData = $this->Inword_quality_check->getProductbyinwordID($inwordid);
        
        echo json_encode(array("productData"=>$productData,"inwordid"=>$inwordid));
    }

    public function check_inword_qc_use() {
          $PostData = $this->input->post();
        //   print_r($PostData);exit;
         $count = 0;
        //  $ids = explode(",",$PostData['ids']);
        //  foreach($ids as $row){
        //     $this->readdb->select('productid');
        //     $this->readdb->from(tbl_orderproducts);
        //     $where = array("productid"=>$row);
        //     $this->readdb->where($where);
        //     $query = $this->readdb->get();
        //     if($query->num_rows() > 0){
        //       $count++;
        //     }

        //     $this->readdb->select('productid');
        //     $this->readdb->from(tbl_cart);
        //     $where = array("productid"=>$row);
        //     $this->readdb->where($where);
        //     $query = $this->readdb->get();
        //     if($query->num_rows() > 0){
        //       $count++;
        //     }

        //     $this->readdb->select('productid');
        //     $this->readdb->from(tbl_quotationproducts);
        //     $where = array("productid"=>$row);
        //     $this->readdb->where($where);
        //     $query = $this->readdb->get();
        //     if($query->num_rows() > 0){
        //       $count++;
        //     }
        //   }
        echo $count;
    }

    public function delete_mul_inword() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            
            $this->load->model("Inword_quality_check_model","Inword_quality_check");
            $this->readdb->select('filename');
            $this->readdb->from(tbl_inwordqcmapping);
            $this->readdb->where('inwordid', $row);
            $query1 = $this->readdb->get();
            $filedata = $query1->result_array();
            if(count($filedata)>0){
                foreach ($filedata as $fd) {
                    unlinkfile('INWORD_PATH', $fd['filename'], INWORD_PATH);
                }
            }
            $this->Inword_quality_check->_table =tbl_inwordqcmapping;
            $this->Inword_quality_check->Delete(array('inwordid'=>$row));

            $this->Inword_quality_check->_table = tbl_inwordqc;
            $this->Inword_quality_check->Delete(array('id'=>$row));
        }
       
    }

    public function view_inword_quality_check($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Inword Q.C.";
        $this->viewData['module'] = "inword_quality_check/View_inword_quality_check";
        $this->viewData['action'] ='1'; 
        $this->viewData['id'] = $id;
        $this->viewData['printtype'] = 'inword-q.c.';
        $this->viewData['heading'] = 'Inword Q.C.';

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();

        $this->viewData['headerdata'] = $this->Inword_quality_check->getHeaderdatabyID($id);
       
        $productdata=$this->Inword_quality_check->getProductbyinwordID($id);

        $product = $this->Inword_quality_check->getProductnamebyinwordID($id);
       
        // $visuallycheckedqty = explode(',',$productdata['visuallycheckedqty']);
        // $dimensioncheckedqty = explode(',',$productdata['dimensioncheckedqty']);
        // $visuallydefectqty = explode(',',$productdata['visuallydefectqty']);
        // $dimensiondefectqty = explode(',',$productdata['dimensiondefectqty']);
        // $mappingid = explode(',',$productdata['mappingid']);
        // $filename = explode(',',$productdata['filename']);
        
        foreach($productdata as $key => $value){
            
            $productData[] = array('visuallycheckedqty'=>$productdata[$key]['visuallycheckedqty'],
                                 'dimensioncheckedqty'=>$productdata[$key]['dimensioncheckedqty'],
                                 'visuallydefectqty'=>$productdata[$key]['visuallydefectqty'],
                                 'dimensiondefectqty'=>$productdata[$key]['dimensiondefectqty'],
                                 'filename'=>$productdata[$key]['filename'],
                                 'mappingid'=>$productdata[$key]['mappingid'],
                                 'productname'=>$product[$key]['name'],
                                 'qty'=>$product[$key]['quantity']
                                );
        }
        // pre($productdata);
        $this->viewData['productdetails'] = $productData;

        
        $this->admin_headerlib->add_javascript("view_inword_quality_check", "pages/view_inword_quality_check.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function exporttopdfTestingandrdDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        // $PostData = $this->input->post();
       
        $inwordid = $_REQUEST['id'];
        
        $PostData['headerdata'] = $this->Inword_quality_check->getHeaderdatabyID($inwordid);
        $productdata = $this->Inword_quality_check->getProductbyinwordID($inwordid);


        $product = $this->Inword_quality_check->getProductnamebyinwordID($inwordid);

        foreach($productdata as $key => $value){
            
            $productData[] = array('visuallycheckedqty'=>$productdata[$key]['visuallycheckedqty'],
                                 'dimensioncheckedqty'=>$productdata[$key]['dimensioncheckedqty'],
                                 'visuallydefectqty'=>$productdata[$key]['visuallydefectqty'],
                                 'dimensiondefectqty'=>$productdata[$key]['dimensiondefectqty'],
                                 'filename'=>$productdata[$key]['filename'],
                                 'mappingid'=>$productdata[$key]['mappingid'],
                                 'productname'=>$product[$key]['name'],
                                 'qty'=>$product[$key]['quantity']
                                );
        }
        $PostData['productdetails'] = $productData;
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = "testing-and-rd";
        $PostData['heading'] = "Testing And R&D";
        $PostData['hideonprint'] = '1';

        $header=$this->load->view(ADMINFOLDER . 'inword_quality_check/Inwordheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'inword_quality_check/PDFinwordqcformat', $PostData,true);

        // $this->general_model->exportToPDF("",$header,$html);

        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $file = "Inword_quality_check.pdf";
        $pdfFilePath = $file;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   60, // margin top
                   15, // margin bottom
                    3, // margin header
                    10); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
       
        ob_start();
        if (ob_get_contents()) ob_end_clean();
        
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
       
        $pdf->Output($pdfFilePath, "D");
        

        
        // $html['content'] = $this->load->view(ADMINFOLDER."testing_and_rd/Printtestingandrdformat.php",$PostData,true);
        
        echo json_encode($html); 
    }

}