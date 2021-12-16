<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Category');
        $this->load->model('Category_model', 'Category');
    }

    public function getmaincategory() {
       
        $this->Category->_order = 'id DESC';
        return $this->Category->getRecordByID();
    }

    public function index() {
        $this->viewData['title'] = "Category";
        $this->viewData['module'] = "category/Category";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->channel_headerlib->add_javascript("category", "pages/category.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Category->get_datatables($MEMBERID,$CHANNELID);
        $data = array();       
        $counter = $_POST['start'];
        $internalErrors = libxml_use_internal_errors(true);

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
            $checkbox = '';

             if(in_array($rollid, $edit) && $datarow->addedby==$MEMBERID && $datarow->usertype==1) {
                $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'category/category-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'category/category-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'category/category-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
           if(in_array($rollid, $delete)&& $datarow->addedby==$MEMBERID && $datarow->usertype==1) {
            $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.CHANNEL_URL.'category/check-category-use","Category","'.CHANNEL_URL.'category/delete-mul-category","categorytable") >'.delete_text.'</a>';

                $checkbox = '<span style="display: none;">'.$datarow->priority.'</span><div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }

            $row[] = ++$counter;
            $row[] = $datarow->name;
            $row[] = ($datarow->maincategoryname!="")?$datarow->maincategoryname:"-";

            $row[] = $this->general_model->displaydatetime($createddate);  
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        libxml_use_internal_errors($internalErrors);

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Category->count_all($MEMBERID,$CHANNELID),
                        "recordsFiltered" => $this->Category->count_filtered($MEMBERID,$CHANNELID),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    
    public function add_category() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        //$this->viewData = $this->getAdminSettings('submenu', 'Category');
        $this->viewData['title'] = "Add category";
        $this->viewData['module'] = "category/Add_category";   
        $this->viewData['VIEW_STATUS'] = "0";
        $this->viewData['maincategorydata'] = $this->Category->getmaincategory();       
        $this->channel_headerlib->add_javascript("category", "pages/add_category.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function category_add() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $slug = isset($PostData['categoryslug']) ? trim($PostData['categoryslug']) : '';
        $maincategoryid = isset($PostData['maincategoryid']) ? trim($PostData['maincategoryid']) : '0';
        //$sectionid = isset($PostData['maincategoryid']) ? trim($PostData['maincategoryid']) : '';
        $status = $PostData['status'];

        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

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
                                    'modifiedby' => $modifiedby,
                                    "channelid"=>$CHANNELID,
                                    "memberid"=>$modifiedby,
                                    "usertype"=>1
                                );
            
                $CategoryID = $this->Category->Add($InsertData);
                
                if($CategoryID != 0){

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
        $this->channel_headerlib->add_javascript("topic","pages/add_category.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
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
            $modifiedby = $this->session->userdata(base_url().'MEMBERID');
            $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

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
                                    'modifieddate' => $modifieddate,
                                    'memberid'=>$modifiedby,
                                    'channelid'=>$CHANNELID
                                );

                $this->Category->_where = array('id' =>$id );
                $updateid = $this->Category->Edit($updateData);
                
                if($updateid != 0){

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
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(CHANNEL_URL . 'ADMINUSERTYPE'));
        $this->Category->_table = tbl_productcategory;
        $this->Category->_where = array("id" => $PostData['id']);
        $this->Category->Edit($updatedata);

       
        echo $PostData['id'];
    }

    public function getProductCategory(){

        $PostData = $this->input->post();
        $memberid = $PostData['memberid'];

        $sellerid = $this->session->userdata(base_url().'MEMBERID');
        $categorydata = $this->Category->getProductCategoryList($memberid,$sellerid);
        echo json_encode($categorydata);
    }
    public function checkimagefile(){

         $PostData = $this->input->post();
         $imagedata= $_FILES['fileimage']['tmp_name'];
         $image_info = getimagesize($imagedata);
         // $image_width= $image_info[0];
         // $image_height=$image_info[1];
    
         // if($image_width != PRODUCT_IMG_WIDTH  && $image_height!=PRODUCT_IMG_HEIGHT){
         //  echo '0';
         // }else{
            echo '1';
         // }
        
    }
}
?>