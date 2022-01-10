<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Category_model', 'Category');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Category');
    }

    public function getmaincategory() {
       
        $this->Category->_order = 'id DESC';
        return $this->Category->getRecordByID();
    }

    public function index() {
        $this->viewData['title'] = "Category";
        $this->viewData['module'] = "category/Category";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['maincategorydata'] = $this->Category->getmaincategory();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Category','View product category.');
		}
		
        $this->admin_headerlib->add_javascript("category", "pages/category.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Category->get_datatables();
        // echo '<pre>';
        // print_r($list);exit;
        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);
 
        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';

             if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'category/category-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'category/category-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'category/category-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
           if(in_array($rollid, $delete)) {
            $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'category/check-category-use","Category","'.ADMIN_URL.'category/delete-mul-category","categorytable") >'.delete_text.'</a>';

                $checkbox = '<span style="display: none;">'.$datarow->priority.'</span><div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }

            $row['DT_RowId'] = $datarow->id;
            $row[] = ++$counter;
            $row[] = $datarow->name; 
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        libxml_use_internal_errors($internalErrors);

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Category->count_all(),
                        "recordsFiltered" => $this->Category->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_category() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Category');
        $this->viewData['title'] = "Add category";
        $this->viewData['module'] = "category/Add_category";   
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['maincategorydata'] = $this->Category->getmaincategory();       
        $this->admin_headerlib->add_javascript("category", "pages/add_category.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function category_add() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $slug = isset($PostData['categoryslug']) ? trim($PostData['categoryslug']) : '';
        $maincategoryid = isset($PostData['maincategoryid']) ? trim($PostData['maincategoryid']) : '0';
        //$sectionid = isset($PostData['maincategoryid']) ? trim($PostData['maincategoryid']) : '';
        $status = $PostData['status'];

        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
     
        $this->form_validation->set_rules('name', 'category name', 'required|min_length[2]');
        $this->form_validation->set_rules('categoryslug', 'category link', 'required|min_length[2]');
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>5, 'message'=>$validationError);
	    }else{

            if($_FILES["fileimage"]['name'] != ''){
                if($_FILES["fileimage"]['size'] != '' && $_FILES["fileimage"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                    $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                    echo json_encode($json);
                    exit;
                }
                $image = uploadFile('fileimage', 'CATEGORY_PATH', CATEGORY_PATH, '*', "", 1, CATEGORY_LOCAL_PATH);
                if($image !== 0){
                    if($image==2){
						$json = array('error'=>3);	// IMAGE NOT UPLOADED
                        echo json_encode($json);
                    	exit;
					}
                } else {
                    $json = array('error'=>4); //INVALID IMAGE TYPE
                    echo json_encode($json);
                    exit;
                }   
            } else {
                $image = '';
            }

            $this->Category->_where = 'maincategoryid="'.$maincategoryid.'" AND (name="'.$name.'" OR slug="'.$slug.'")';
            $sqlname = $this->Category->getRecordsByID();

            if(empty($sqlname)){
                
                $this->Category->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
                $this->Category->_where = ("maincategoryid='".$maincategoryid."'");
                $category = $this->Category->getRecordsById();
                
                $maxpriority = (!empty($category))?$category['maxpriority']:1;
                
                $InsertData = array(
                                    'name' => $name,
                                    'slug' => $slug,
                                    'image'=>$image,
                                    'maincategoryid' => $maincategoryid, 
                                    'priority' => $maxpriority,
                                    'status' => $status,
                                    'createddate' => $modifieddate,                                
                                    'addedby' => $modifiedby,                              
                                    'modifieddate' => $modifieddate,                             
                                    'modifiedby' => $modifiedby 
                                );
            
                $CategoryID = $this->Category->Add($InsertData);
                
                if($CategoryID != 0){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Product Category','Add new '.$name.' product category.');
                    }

                    $json = array('error'=>1); // Category inserted successfully
                } else {
                    $json = array('error'=>0); // Category not inserted 
                }
            } else {
                $json = array('error'=>2); // Category already added
            }
        }
        echo json_encode($json);
    }

    public function category_edit($categoryid) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit category";
        //$this->viewData['sectiondata'] = $this->getsections();
        $this->viewData['module'] = "category/Add_category";
        $this->viewData['action'] = "1"; //Edit
        $this->viewData['VIEW_STATUS'] = "1";
        // $this->viewData['sectionid'] = $sectionid; 
        $this->viewData['maincategorydata'] = $this->Category->getmaincategory();

        $this->Category->_where = array('id' => $categoryid);
        $this->viewData['categorydata'] = $this->Category->getRecordsByID();
        //   print_R(  $this->viewData['categorydata']);exit;
        $this->admin_headerlib->add_javascript("topic","pages/add_category.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_category() {
        
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $this->form_validation->set_rules('name', 'Name', 'required|min_length[2]');
        $this->form_validation->set_rules('categoryslug', 'category link', 'required|min_length[2]');

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>5, 'message'=>$validationError);
	    }else{

            $name = isset($PostData['name']) ? trim($PostData['name']) : '';
            $slug = isset($PostData['categoryslug']) ? trim($PostData['categoryslug']) : '';
            $id = isset($PostData['categoryid']) ? trim($PostData['categoryid']) : '';
            $maincategoryid = isset($PostData['maincategoryid']) ? trim($PostData['maincategoryid']) : '0';      
            $modifiedby = $this->session->userdata(base_url().'ADMINID');
            $modifieddate = $this->general_model->getCurrentDateTime();
            $status = $PostData['status'];
            $oldfileimage= isset($PostData['oldfileimage']) ? trim($PostData['oldfileimage']) : '';
            
            $this->Category->_where = 'id!="'.$id.'" AND maincategoryid="'.$maincategoryid.'" AND (name="'.$name.'" OR slug="'.$slug.'")';// AND maincategoryid = ".$maincategoryid;
            $sqlname = $this->Category->getRecordsByID();
            if($_FILES["fileimage"]['name'] != ''){

                if($_FILES["fileimage"]['size'] != '' && $_FILES["fileimage"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                    $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                    echo json_encode($json);
                    exit;
                }

                $FLNM1 = reuploadFile('fileimage', 'CATEGORY_PATH',$oldfileimage, CATEGORY_PATH, '*', "", 1, CATEGORY_LOCAL_PATH);
                if($FLNM1 !== 0){
                    if($FLNM1==2){
						$json = array('error'=>3); // IMAGE NOT UPLOADED
                        echo json_encode($json);
                        exit;
					}
                } else {
                    $json = array('error'=>4); //INVALID IMAGE TYPE
                    echo json_encode($json);
                    exit;
                }   
            }elseif($PostData['removeimg']==1) {
                unlinkfile('CATEGORY_PATH', $oldfileimage, CATEGORY_PATH);
                $FLNM1 = "";
            } 
            else {
                $FLNM1 =  $oldfileimage;
            }
            if(empty($sqlname)){
                $updateData = array('name' => $name,
                                    'slug' => $slug,
                                    'image' => $FLNM1,
                                    'status'=>$status,
                                    'maincategoryid' => $maincategoryid, 
                                    'modifiedby' => $modifiedby,
                                    'modifieddate' => $modifieddate);

                $this->Category->_where = array('id' =>$id );
                $updateid = $this->Category->Edit($updateData);
                
                if($updateid != 0){

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Product Category','Edit '.$name.' product category.');
                    }

                    $json = array('error'=>1); // Category update successfully
                } else {
                    $json = array('error'=>0); // Category not updated
                }
            } else {
                $json = array('error'=>2); // Category already added
            }
        }
        echo json_encode($json);
    }

    public function delete_mul_category() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $this->readdb->select('id,name,image');
            $this->readdb->from($this->Category->_table);
            $this->readdb->where('id', $row);
            $query = $this->readdb->get();
            $categorydata = $query->row_array();
            if(count($categorydata)>0){
                unlinkfile('CATEGORY_PATH', $categorydata['image'], CATEGORY_PATH);
                // Delete from essay data table

                if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(3,'Product Category','Delete '.$categorydata['name'].' product category.');
				}
                $this->Category->Delete(array('id'=>$row));
            }
        }
    }

    public function check_category_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            $this->readdb->select('categoryid');
            $this->readdb->from(tbl_product);
            $where = array("categoryid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
          }
        echo $count;
    }

    public function category_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Category->_table = tbl_productcategory;
        $this->Category->_where = array("id" => $PostData['id']);
        $this->Category->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Category->_where = array("id"=>$PostData['id']);
            $data = $this->Category->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' product category.';
            
            $this->general_model->addActionLog(2,'Product Category', $msg);
        }
        echo $PostData['id'];
    }

    public function update_priority(){

		$PostData = $this->input->post();
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();
        for($i = 0; $i < count($sequenceno); $i++){
            $updatedata[] = array(
                'priority'=>$sequenceno[$i]['sequenceno'],
                'id' => $sequenceno[$i]['id']
            );
        }
        if(!empty($updatedata)){
            $this->Category->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Product Category','Change product category priority.');
		}
        echo 1;
    }
    
    public function getProductCategory(){

        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        
        $categorydata = $this->Category->getProductCategoryList($memberid);
        echo json_encode($categorydata);
    }

    public function getMultipleMemberProductCategory(){

        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];
        
        $categorydata = $this->Category->getMultipleMemberProductCategoryList($memberid);
        echo json_encode($categorydata);
    }
    
    public function getCategoryBySeller(){

        $PostData = $this->input->post();
        $sellermemberid = $PostData['sellermemberid'];
        $memberid = $PostData['memberid'];
        
        $this->load->model('Product_model', 'Product');
        $categorydata = $this->Product->getMemberProductCategory($memberid,$sellermemberid);
        echo json_encode($categorydata);
    }
}?>