<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Testing_and_rd extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Testing_and_rd_model', 'Testing_and_rd');
        // $this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model','Side_navigation');
        $this->viewData = $this->getAdminSettings('submenu', 'Testing_and_rd');
    }

    public function index() {
        $this->viewData['title'] = "Testing AND R&D";
        $this->viewData['module'] = "testing_and_rd/testing_and_rd";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model("Product_process_model","Product_process");
        $this->viewData['processdata'] = $this->Product_process->getProcessOnProductProcess();

        // $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOfINProductProcess();

        $this->load->model('User_model','User');
        $this->viewData['userdata'] = $this->User->getUserListData();

        // pre($this->viewData['userdata']);
        // $this->load->model('Brand_model','Brand');
        // $this->viewData['branddata'] = $this->Brand->getActiveBrand();

        $this->admin_headerlib->add_javascript("Testing_and_rd", "pages/testing_and_rd.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        
   
       
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        
        $list = $this->Testing_and_rd->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        //    print_r($list);exit;
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
            $status =$datarow['status'];
            if($status == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown1">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="changestatus(1,'.$datarow['id'].')">Partially</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="changestatus(2,'.$datarow['id'].')">Complete</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="changestatus(3,'.$datarow['id'].')">Cancel</a>
                              </li>
                          </ul>';
            }else if($status == 1){
                $dropdownmenu = '<button class="btn btn-info '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown1">Partially <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                            <a onclick="changestatus(2,'.$datarow['id'].')">Complete</a>
                            </li>
                            <li id="dropdown-menu">
                            <a onclick="changestatus(3,'.$datarow['id'].')">Cancel</a>
                            </li>
                        </ul>';
            }else if($status == 2){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown1">Complete <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                            <li id="dropdown-menu">
                            <a onclick="changestatus(3,'.$datarow['id'].')">Cancel</a>
                            </li>
                        </ul>';
            }else if($status == 3){
                $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</span>';
            }
            $testingandrdstatus = '<div class="dropdown">'.$dropdownmenu.'</div>';

            $actions .= '<a href="'.ADMIN_URL.'testing-and-rd/view-testing-and-rd/'.$datarow['id'].'/'.'" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';           
            $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'testing-and-rd/edit-testing-and-rd/'.$datarow['id'].'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            if($status == 1){
                $actions .= '<a class="'.reprocess_class.'" href="'.ADMIN_URL.'testing-and-rd/re-testing-and-rd/'.$datarow['id'].'/'.'" title="'.reprocess_title.'">'.reprocess_text.'</a>';
            }
            $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow['id'].',"'.ADMIN_URL.'testing-and-rd/check-testing-and-rd-use","Testing&nbsp;And&nbsp;R&D","'.ADMIN_URL.'testing-and-rd/delete-mul-testing","testingtable") >'.delete_text.'</a>';
            $checkbox = '<div class="checkbox"><input value="'.$datarow['id'].'" type="checkbox" class="checkradios" name="check'.$datarow['id'].'" id="check'.$datarow['id'].'" onchange="singlecheck(this.id)"><label for="check'.$datarow['id'].'"></label></div>';

            
            $row[] = ++$counter;
            $row[] = $datarow['processname'];
            $row[] = $datarow['productname'];
            $row[] = $datarow['batchno'];
            $row[] = $this->general_model->displaydate($datarow['testdate']);
            $row[] = $testingandrdstatus;
            $row[] = $datarow['addedby'];
            $row[] = $this->general_model->displaydatetime($datarow['createddate']);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Testing_and_rd->count_all(),
                        "recordsFiltered" => $this->Testing_and_rd->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function add_testing_and_rd() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Testing_and_rd');      
        $this->viewData['title'] = "Add Testing And R&D";
        $this->viewData['module'] = "testing_and_rd/add_testing_and_rd";   
        $this->viewData['VIEW_STATUS'] = "0";            
        

        $this->load->model("Product_process_model","Product_process");
        $this->viewData['processdata'] = $this->Product_process->getProcessOnProductProcess();

        // $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOfINProductProcess();
        // pre($this->viewData['batchnodata']);
        //echo NOOFPRODUCT;exit;
        
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("add-testing-and-rd", "pages/add_testing_and_rd.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function edit_testing_and_rd($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Testing And R&D";
        $this->viewData['module'] = "testing_and_rd/add_testing_and_rd";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model("Product_process_model","Product_process");
        $this->viewData['processdata'] = $this->Product_process->getProcessOnProductProcess();

        // $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOfINProductProcess();

        $this->viewData['testingdata'] = $this->Testing_and_rd->getTestingdatabyID($id);
        // pre($this->viewData['testingdata']);
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("add-testing-and-rd", "pages/add_testing_and_rd.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function re_testing_and_rd($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Re Testing And R&D";
        $this->viewData['module'] = "testing_and_rd/Re_testing";
        $this->viewData['RETESTING'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";

        $this->load->model("Product_process_model","Product_process");
        $this->viewData['processdata'] = $this->Product_process->getProcessOnProductProcess();

        $this->viewData['testingdata'] = $this->Testing_and_rd->getTestingdatabyID($id);
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("jquery-dropzone", "jquery-dropzone.js");
        $this->admin_headerlib->add_javascript_plugins("fileinput", "form-jasnyupload/fileinput.min.js");
        $this->admin_headerlib->add_bottom_javascripts("re-testing", "pages/re_testing.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function re_testing_and_rd_add() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();  
        // print_r($PostData); exit;

        $batchid  = $PostData['testingbatchid'];
        $parenttestingid  = $PostData['parenttestingid'];
        $processid = $PostData['testingprocessid'];
        $productsid = $PostData['newretestingtransactionproductsid'];
        $testdate = $this->general_model->convertdate($PostData['newtestdate']);
        // $quantity = $PostData['quantity'];
        $transactionproductsid = $PostData['newretestingtransactionproductsid'];
        $mechanicledefectqty = $PostData['newretestingmechanicledefectqty'];
        $electricallydefectqty = $PostData['newretestingelectricallydefectqty'];
        $visuallydefectqty = $PostData['newretestingvisuallydefectqty'];

        $remarks = $PostData['newtestingremarks'];
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
    
        if(!is_dir(TESTING_PATH)){
            @mkdir(TESTING_PATH);
        }
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $compress = 0;
                $testingfile = '';
                if (isset($_FILES['testingfile'.($key+1).'']['name']) && $_FILES['testingfile'.($key+1).'']['name'] != '') {
                    if($_FILES["testingfile".($key+1).""]['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $testingfile = uploadFile('testingfile'.($key+1).'', 'TESTING_IMGPDF', TESTING_PATH, '*', '', $compress, TESTING_LOCAL_PATH, '', '', 0);
                    if ($testingfile !== 0) {
                        if ($testingfile == 2) {
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

            $InsertData = array(
                'parenttestingid'=>$parenttestingid,
                'batchid'=>$batchid,
                'testdate'=>$testdate,
                'remarks'=>$remarks,
                'processid'=>$processid,
                'status'=>0,
                'createddate' => $createddate,
                'addedby' => $addedby,
                'modifeddate' => $createddate,
                'modififedby' => $addedby
            );

            $InsertData = array_map('trim',$InsertData);
            $insertid = $this->Testing_and_rd->add($InsertData);

            if($insertid){
                foreach($productsid as $key => $value){

                    $testingfile = "";
                    $compress = 0;
                    if(isset($_FILES['newretestingtestingfile'.($key+1).'']['name']) && $_FILES['newretestingtestingfile'.($key+1).'']['name'] != ''){
                        if($_FILES["newretestingtestingfile".($key+1).""]['type'] != 'application/pdf'){
                            $compress = 1;
                        }
                        $testingfile = uploadFile('newretestingtestingfile'.($key+1).'', 'TESTING_IMGPDF', TESTING_PATH, '*', '', $compress, TESTING_LOCAL_PATH);         
                        // echo $qualitycheckfile;
                         
                    } 

                    if(isset($PostData['newretestingmechaniclecheck'.($key+1)]) || isset($PostData['newretestingelectricallycheck'.($key+1)]) || isset($PostData['newretestingvisuallycheck'.($key+1)])){
                        $insertdata = array('testingrdid'=>$insertid,
                                            'mechanicledefectqty'=>$mechanicledefectqty[$key],
                                            'electricallydefectqty'=>$electricallydefectqty[$key],
                                            'visuallydefectqty'=>$visuallydefectqty[$key],
                                            'transactionproductsid'=>$transactionproductsid[$key],
                                            'mechaniclecheck'=>isset($PostData['newretestingmechaniclecheck'.($key+1)])?1:0,
                                            'electricallycheck'=>isset($PostData['newretestingelectricallycheck'.($key+1)])?1:0,
                                            'visuallycheck'=>isset($PostData['newretestingvisuallycheck'.($key+1)])?1:0,
                                            'filename'=>$testingfile
                                        );
                        $insertdata = array_map('trim',$insertdata);
                        $this->Testing_and_rd->_table=tbl_testingrdmapping;
                        $this->Testing_and_rd->add($insertdata);
                    }
                }
                echo 1;
            
            }else{
                echo 0;
            }
          
    }

    public function getBatchNoOfINProductProcess(){
        $PostData = $this->input->post();
        // pre($PostData);
        $processid = $PostData['processid'];
        $this->load->model('Product_process_model','Product_process');
        $data = $this->viewData['batchnodata'] = $this->Product_process->getBatchNoOfINProductProcess($processid);
        
        echo json_encode($data);
    }

    public function testing_and_rd_add() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();  
        // print_r($PostData); exit;
        $batchid  = $PostData['batchid'];
        $processid = $PostData['processid'];
        $productsid = $PostData['transactionproductsid'];
        $testdate = $this->general_model->convertdate($PostData['testdate']);
        // $quantity = $PostData['quantity'];
        $transactionproductsid = $PostData['transactionproductsid'];
        $mechanicledefectqty = $PostData['mechanicledefectqty'];
        $electricallydefectqty = $PostData['electricallydefectqty'];
        $visuallydefectqty = $PostData['visuallydefectqty'];
        $Filetext = $PostData['Filetext'];
        $remarks = $PostData['testingremarks'];
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();

        if(!is_dir(TESTING_PATH)){
            @mkdir(TESTING_PATH);
        }
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $compress = 0;
                $testingfile = '';
                if (isset($_FILES['testingfile'.($key+1).'']['name']) && $_FILES['testingfile'.($key+1).'']['name'] != '') {
                    if($_FILES["testingfile".($key+1).""]['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $testingfile = uploadFile('testingfile'.($key+1).'', 'TESTING_IMGPDF', TESTING_PATH, '*', '', $compress, TESTING_LOCAL_PATH, '', '', 0);
                    if ($testingfile !== 0) {
                        if ($testingfile == 2) {
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

            $InsertData = array('batchid'=>$batchid,
                'testdate'=>$testdate,
                'remarks'=>$remarks,
                'processid'=>$processid,
                'status'=>0,
                'createddate' => $createddate,
                'addedby' => $addedby,
                'modifeddate' => $createddate,
                'modififedby' => $addedby
            );

            $InsertData = array_map('trim',$InsertData);
            $insertid = $this->Testing_and_rd->add($InsertData);

            if($insertid){
                foreach($productsid as $key => $value){

                    $testingfile = "";
                    $compress = 0;
                    if(isset($_FILES['testingfile'.($key+1).'']['name']) && $_FILES['testingfile'.($key+1).'']['name'] != ''){
                        if($_FILES["testingfile".($key+1).""]['type'] != 'application/pdf'){
                            $compress = 1;
                        }
                        $testingfile = uploadFile('testingfile'.($key+1).'', 'TESTING_IMGPDF', TESTING_PATH, '*', '', $compress, TESTING_LOCAL_PATH);         
                        // echo $qualitycheckfile;
                         
                    } 

                    if(isset($PostData['mechaniclecheck'.($key+1)]) || isset($PostData['electricallycheck'.($key+1)]) || isset($PostData['visuallycheck'.($key+1)])){
                        $insertdata = array('testingrdid'=>$insertid,
                                            'mechanicledefectqty'=>$mechanicledefectqty[$key],
                                            'electricallydefectqty'=>$electricallydefectqty[$key],
                                            'visuallydefectqty'=>$visuallydefectqty[$key],
                                            'transactionproductsid'=>$transactionproductsid[$key],
                                            'mechaniclecheck'=>isset($PostData['mechaniclecheck'.($key+1)])?1:0,
                                            'electricallycheck'=>isset($PostData['electricallycheck'.($key+1)])?1:0,
                                            'visuallycheck'=>isset($PostData['visuallycheck'.($key+1)])?1:0,
                                            'filename'=>$testingfile
                                        );
                        $insertdata = array_map('trim',$insertdata);
                        $this->Testing_and_rd->_table=tbl_testingrdmapping;
                        $this->Testing_and_rd->add($insertdata);
                    }
                }
                echo 1;
            
            }else{
                echo 0;
            }
          
    }

    public function testing_and_rd_edit(){
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData=$this->input->post();
        // print_r($PostData);exit;
        $modifeddate = $this->general_model->getCurrentDateTime();
        $modififedby = $this->session->userdata(base_url().'ADMINID');

        $testdate = $this->general_model->convertdate($PostData['testdate']);
        $testingid = $PostData['testingid'];
        $productsid = $PostData['transactionproductsid'];
        $transactionproductsid = $PostData['transactionproductsid'];
        $mappingid = $PostData['mappingid'];
        $mechanicledefectqty = $PostData['mechanicledefectqty'];
        $electricallydefectqty = $PostData['electricallydefectqty'];
        $visuallydefectqty = $PostData['visuallydefectqty'];
        $remarks = $PostData['testingremarks'];
       
        $oldFiletext = $PostData['oldFiletext'];

        if(!is_dir(TESTING_PATH)){
            @mkdir(TESTING_PATH);
        }
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $compress = 0;
                $testingfile = '';
                if (isset($_FILES['testingfile'.($key+1).'']['name']) && $_FILES['testingfile'.($key+1).'']['name'] != '') {
                    if($_FILES["testingfile".($key+1).""]['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $testingfile = uploadFile('testingfile'.($key+1).'', 'TESTING_IMGPDF', TESTING_PATH, '*', '', $compress, TESTING_LOCAL_PATH, '', '', 0);
                    if ($testingfile !== 0) {
                        if ($testingfile == 2) {
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
                            'testdate'=>$testdate,
                            'remarks'=>$remarks,
                            'modifeddate' => $modifeddate,
                            'modififedby' => $modififedby
                        );
            $UpdateData = array_map('trim',$UpdateData);
            
            $this->Testing_and_rd->_table = tbl_testingrd;
            $this->Testing_and_rd->_where = array("id" => $testingid);
            $Updateid = $this->Testing_and_rd->Edit($UpdateData);

            foreach($productsid as $key=>$value){
                $testingfile = "";
                $compress = 0;
                $oldtestingfile = $oldFiletext[$key];
                
                $testingfile = $oldtestingfile;
                if(isset($_FILES['testingfile'.($key+1).'']['name']) && $_FILES['testingfile'.($key+1).'']['name'] != '' && $oldtestingfile != ''){
                    if($_FILES['testingfile'.($key+1).'']['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $testingfile = reuploadfile('testingfile'.($key+1).'', 'TESTING_IMGPDF', $oldtestingfile ,TESTING_PATH,"*", '', $compress, TESTING_LOCAL_PATH);

                    if($testingfile !== 0){
                        if($testingfile==2){
                            echo 3;// file not uploaded
                            exit;
                        }
                    } else {
                        echo 4; //INVALID TYPE
                        exit;
                    } 	
                }else if(isset($_FILES['testingfile'.($key+1).'']['name']) && $_FILES['testingfile'.($key+1).'']['name'] != '' && $oldtestingfile == ''){
               
                    if($_FILES['testingfile'.($key+1).'']['type'] != 'application/pdf'){
                        $compress = 1;
                    }
                    $testingfile = uploadFile('testingfile'.($key+1).'', 'TESTING_IMGPDF', TESTING_PATH, '*', '', $compress, TESTING_LOCAL_PATH);         
                    if($testingfile !== 0){
                        if($testingfile==2){
                            echo 3;// file not uploaded
                            exit;
                        }
                    } else {
                        echo 4; //INVALID TYPE
                        exit;
                    } 
                }
                
                if(!empty($mappingid[$key])){

                    if(isset($PostData['mechaniclecheck'.($key+1)]) || isset($PostData['electricallycheck'.($key+1)]) || isset($PostData['visuallycheck'.($key+1)])){
                        $updateData =array(
                            'mechanicledefectqty' =>$mechanicledefectqty[$key],
                            'electricallydefectqty'=>$electricallydefectqty[$key],
                            'visuallydefectqty'=>$visuallydefectqty[$key],
                            'transactionproductsid'=>$transactionproductsid[$key],
                            'mechaniclecheck'=>isset($PostData['mechaniclecheck'.($key+1)])?1:0,
                            'electricallycheck'=>isset($PostData['electricallycheck'.($key+1)])?1:0,
                            'visuallycheck'=>isset($PostData['visuallycheck'.($key+1)])?1:0,
                            'filename' =>$testingfile
                        );
                    }else{
                        $data = $this->Testing_and_rd->getProductReportbyTestingIdAndTransactionproductid($testingid,$transactionproductsid[$key]);
                        unlinkfile('TESTING_IMGPDF',$data['filename'],TESTING_PATH);

                        $updateData =array(
                            'mechanicledefectqty' => 0,
                            'electricallydefectqty'=> 0,
                            'visuallydefectqty'=> 0,
                            'transactionproductsid'=>$transactionproductsid[$key],
                            'mechaniclecheck'=> 0,
                            'electricallycheck'=> 0,
                            'visuallycheck'=> 0,
                            'filename' => ''
                        );
                        
                    }
                    $updateData = array_map('trim',$updateData);
                    
                    $this->Testing_and_rd->_table=tbl_testingrdmapping;
                    $this->Testing_and_rd->_where = array("testingrdid" => $testingid,"id"=>$mappingid[$key]);
                    $this->Testing_and_rd->Edit($updateData);
                }else{

                    if(isset($PostData['mechaniclecheck'.($key+1)]) || isset($PostData['electricallycheck'.($key+1)]) || isset($PostData['visuallycheck'.($key+1)])){
                        $insertdata = array('testingrdid'=>$testingid,
                                            'mechanicledefectqty'=>$mechanicledefectqty[$key],
                                            'electricallydefectqty'=>$electricallydefectqty[$key],
                                            'visuallydefectqty'=>$visuallydefectqty[$key],
                                            'transactionproductsid'=>$transactionproductsid[$key],
                                            'mechaniclecheck'=>isset($PostData['mechaniclecheck'.($key+1)])?1:0,
                                            'electricallycheck'=>isset($PostData['electricallycheck'.($key+1)])?1:0,
                                            'visuallycheck'=>isset($PostData['visuallycheck'.($key+1)])?1:0,
                                            'filename'=>$testingfile
                                        );
                        $insertdata = array_map('trim',$insertdata);
                        $this->Testing_and_rd->_table=tbl_testingrdmapping;
                        $this->Testing_and_rd->add($insertdata);
                    }
                }
            }
            
            if($Updateid!=0){
                echo 2;  
            }else{
                echo 5;// page content not added
            }
    }

    public function update_status() {
        $PostData = $this->input->post();
        // pre($PostData);
        $status = $PostData['status'];
        $testingId = $PostData['testingId'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifeddate' => $modifieddate, 
            'modififedby'=>$modifiedby
        );  
       
        $updateData = array_map('trim',$updateData);
        // pre($updateData);
        $this->Testing_and_rd->_where = array("id" => $testingId);
        $update = $this->Testing_and_rd->Edit($updateData);
        if($update) {
            $this->general_model->addActionLog(2,'TESTING AND R&D','Change status '.$testingId.' on testing and  r&d.');
            echo 1;    
        }else{
            echo 0;
        }
    }

    public function check_testing_and_rd_use() {
        $PostData = $this->input->post();
       $count = 0;
        //    $ids = explode(",",$PostData['ids']);
        //    foreach($ids as $row){
        //       $this->readdb->select('productid');
        //       $this->readdb->from(tbl_orderproducts);
        //       $where = array("productid"=>$row);
        //       $this->readdb->where($where);
        //       $query = $this->readdb->get();
        //       if($query->num_rows() > 0){
        //         $count++;
        //       }

        //       $this->readdb->select('productid');
        //       $this->readdb->from(tbl_cart);
        //       $where = array("productid"=>$row);
        //       $this->readdb->where($where);
        //       $query = $this->readdb->get();
        //       if($query->num_rows() > 0){
        //         $count++;
        //       }

        //       $this->readdb->select('productid');
        //       $this->readdb->from(tbl_quotationproducts);
        //       $where = array("productid"=>$row);
        //       $this->readdb->where($where);
        //       $query = $this->readdb->get();
        //       if($query->num_rows() > 0){
        //         $count++;
        //       }
        //     }
      echo $count;
    }
    
    public function delete_mul_testing() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach($ids as $row){

            $getparenttestings = $this->Testing_and_rd->getparenttestings($row);

            
            $this->load->model("Testing_and_rd_model","Testing_and_rd");
            $this->readdb->select('filename');
            $this->readdb->from(tbl_testingrdmapping);
            $this->readdb->where('(testingrdid='.$row.' OR testingrdid IN ('.$getparenttestings.'))');
            $query1 = $this->readdb->get();
            $filedata = $query1->result_array();
            if(count($filedata)>0){
                foreach ($filedata as $fd) {
                    unlinkfile('TESTING_PATH', $fd['filename'], TESTING_PATH);
                }
            }
            $this->Testing_and_rd->_table =tbl_testingrdmapping;
            $this->Testing_and_rd->Delete('(testingrdid='.$row.' OR testingrdid IN ('.$getparenttestings.'))');

            $this->Testing_and_rd->_table = tbl_testingrd;
            $this->Testing_and_rd->Delete('(id='.$row.' OR id IN ('.$getparenttestings.'))');
            // $this->Testing_and_rd->Delete(array('parenttestingid'=>$row));
        }
    }

    public function view_testing_and_rd($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Testing And R&D";
        $this->viewData['module'] = "testing_and_rd/View_testing_and_rd";
        $this->viewData['action'] ='1'; 
        $this->viewData['id'] = $id;
        $this->viewData['printtype'] = 'testing-and-rd';
        $this->viewData['heading'] = 'Testing And R&D';

        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        
        $this->viewData['headerdata'] = $this->Testing_and_rd->getHeaderdatabyID($id);
        // pre($this->viewData['headerdata']);

        $productdata=$this->Testing_and_rd->getProductbytestingID($id);

        $product = $this->Testing_and_rd->getProductnamebytestingID($id);

        // print_r($product);exit;
        /* $mappingid = explode(',',$productdata['mappingid']);
        $mechanicledefectqty = explode(',',$productdata['mechanicledefectqty']);
        $electricallydefectqty = explode(',',$productdata['electricallydefectqty']);
        $filename = explode(',',$productdata['filename']);
        $visuallydefectqty = explode(',',$productdata['visuallydefectqty']); */

        foreach($productdata as $key => $value){
            $productData[] = array('mechanicledefectqty'=>$productdata[$key]['mechanicledefectqty'],
                                   'electricallydefectqty'=>$productdata[$key]['electricallydefectqty'],
                                   'filename'=>$productdata[$key]['filename'],
                                   'visuallydefectqty'=>$productdata[$key]['visuallydefectqty'],
                                   'mappingid'=>$productdata[$key]['mappingid'],
                                   'productname'=>$product[$key]['productname'],
                                   'qty'=>$product[$key]['quantity']
                                );

        }
        $this->viewData['productdetails'] = $productData;
        // pre($this->viewData['productdetails']);
        $this->admin_headerlib->add_javascript("view_testing_and_rd", "pages/view_testing_and_rd.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function printTestingandrdDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
       
        $testingid = $PostData['id'];
        
        $PostData['headerdata'] = $this->Testing_and_rd->getHeaderdatabyID($testingid);
        $productdata=$this->Testing_and_rd->getProductbytestingID($testingid);


        $product = $this->Testing_and_rd->getProductnamebytestingID($testingid);

        /* $mappingid = explode(',',$productdata['mappingid']);
        $mechanicledefectqty = explode(',',$productdata['mechanicledefectqty']);
        $electricallydefectqty = explode(',',$productdata['electricallydefectqty']);
        $filename = explode(',',$productdata['filename']);
        $visuallydefectqty = explode(',',$productdata['visuallydefectqty']); */

        foreach($productdata as $key => $value){
            $productData[] = array('mechanicledefectqty'=>$productdata[$key]['mechanicledefectqty'],
                                   'electricallydefectqty'=>$productdata[$key]['electricallydefectqty'],
                                   'filename'=>$productdata[$key]['filename'],
                                   'visuallydefectqty'=>$productdata[$key]['visuallydefectqty'],
                                   'mappingid'=>$productdata[$key]['mappingid'],
                                   'productname'=>$product[$key]['productname'],
                                   'qty'=>$product[$key]['quantity']
                                );

        }
        $PostData['productdetails'] = $productData;
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = "testing-and-rd";
        $PostData['heading'] = "Testing And R&D";
        $PostData['hideonprint'] = '1';
        
        $html['content'] = $this->load->view(ADMINFOLDER."testing_and_rd/Printtestingandrdformat.php",$PostData,true);
        
        echo json_encode($html); 
    }
    public function exporttopdfTestingandrdDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        // $PostData = $this->input->post();
       
        $testingid = $_REQUEST['testingid'];
        
        $PostData['headerdata'] = $this->Testing_and_rd->getHeaderdatabyID($testingid);
        $productdata=$this->Testing_and_rd->getProductbytestingID($testingid);


        $product = $this->Testing_and_rd->getProductnamebytestingID($testingid);

       
        foreach($productdata as $key => $value){
            $productData[] = array('mechanicledefectqty'=>$productdata[$key]['mechanicledefectqty'],
                                   'electricallydefectqty'=>$productdata[$key]['electricallydefectqty'],
                                   'filename'=>$productdata[$key]['filename'],
                                   'visuallydefectqty'=>$productdata[$key]['visuallydefectqty'],
                                   'mappingid'=>$productdata[$key]['mappingid'],
                                   'productname'=>$product[$key]['productname'],
                                   'qty'=>$product[$key]['quantity']
                                );

        }
        $PostData['productdetails'] = $productData;
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails();
        $PostData['printtype'] = "testing-and-rd";
        $PostData['heading'] = "Testing And R&D";
        $PostData['hideonprint'] = '1';

        $header=$this->load->view(ADMINFOLDER . 'testing_and_rd/Testingheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'testing_and_rd/PDFtestingandrdformat', $PostData,true);

        // $this->general_model->exportToPDF("",$header,$html);

        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $file = "Testing_and_r&d.pdf";
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

    public function getProductByBatchno(){
        $PostData = $this->input->post();
        
        $batchno = $PostData['batchno'];
        $this->load->model('Product_process_model','Product_process');
        
        $productdata = $this->Product_process->getProductByBatchno($batchno);
        $json['productdata'] = $productdata;
        
        echo json_encode($json);
    }
    public function getProductByBatchnoForRetesting(){
        $PostData = $this->input->post();
        
        $batchno = $PostData['batchno'];
        $this->load->model('Product_process_model','Product_process');
        
        $productdata = $this->Product_process->getProductByBatchnoForTesting($batchno);
        $remaintetproductdata = $this->Product_process->getRemainTestProductByBatchnoForReTesting($batchno);
        $retestingproductdata = $this->Product_process->getProductByBatchnoForRetesting($batchno);
        $json['productdata'] = $productdata;
        $json['retestingproductdata'] = $retestingproductdata;
        $json['remaintetproductdata'] = $remaintetproductdata;
        
        // pre($json);
        echo json_encode($json);
    }
    
    public function getProductdatabytestingID(){
        $PostData = $this->input->post();
        
        $TestingId = $PostData['TestingId'];
        $productData = $this->Testing_and_rd->getProductbytestingID($TestingId);
        /* $mechanicledefectqty = explode(',',$productdata['mechanicledefectqty']);
        $electricallydefectqty = explode(',',$productdata['electricallydefectqty']);
        $filename = explode(',',$productdata['filename']);
        $visuallydefectqty = explode(',',$productdata['visuallydefectqty']);
        $mappingid = explode(',',$productdata['mappingid']); */
        
        /* foreach($mechanicledefectqty as $key => $value){
            $productData[] = array('mechanicledefectqty'=>$mechanicledefectqty[$key],
                                 'electricallydefectqty'=>$electricallydefectqty[$key],
                                 'filename'=>$filename[$key],
                                 'visuallydefectqty'=>$visuallydefectqty[$key],
                                 'mappingid'=>$mappingid[$key]
                                );
        } */
        // print_r($productData);
        echo json_encode($productData);
    }

    public function getproductDetailsByBatchId(){
        $PostData = $this->input->post();
        
        $batchno = $PostData['batchno'];
        
        $TestingId = $this->Testing_and_rd->getTestinfIdbyBatchId($batchno);
        $productData = $this->Testing_and_rd->getProductbytestingID($TestingId);
        
        echo json_encode(array("productData"=>$productData,"TestingId"=>$TestingId));
    }
}