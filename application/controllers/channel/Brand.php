<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Brand_model', 'Brand');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Brand');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Brand";
        $this->viewData['module'] = "brand/Brand";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->channel_headerlib->add_javascript("brand", "pages/brand.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() { 

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Brand->get_datatables($MEMBERID,$CHANNELID);
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit) && $datarow->addedby==$MEMBERID && $datarow->usertype==1) {
                $actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'brand/brand-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'brand/brand-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'brand/brand-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete) && $datarow->addedby==$MEMBERID && $datarow->usertype==1) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.CHANNEL_URL.'brand/check-brand-use","Brand","'.CHANNEL_URL.'brand/delete-mul-brand","brandtable") >'.delete_text.'</a>';

                $checkbox = '<span style="display: none;">'.$datarow->priority.'</span><div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            if($datarow->image!=''){
                $image = '<img src="'.BRAND.$datarow->image.'" class="thumbwidth">';
            }

            $row['DT_RowId'] = $datarow->id;
            $row[] = ++$counter;
            $row[] = $datarow->name;
            $row[] = $image;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = $actions;
             $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Brand->count_all($MEMBERID,$CHANNELID),
                        "recordsFiltered" => $this->Brand->count_filtered($MEMBERID,$CHANNELID),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_brand() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getChannelSettings('submenu', 'Brand');
        $this->viewData['title'] = "Add Brand";
        $this->viewData['module'] = "brand/Add_brand";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->channel_headerlib->add_javascript("brand", "pages/add_brand.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function brand_add() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $name = trim($PostData['brandname']);
        $status = $PostData['status'];

        $addedby = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $createddate = $this->general_model->getCurrentDateTime();
     
        $this->form_validation->set_rules('brandname', 'brand name', 'required|min_length[3]',array('required'=>"Please enter brand name !",'min_length'=>"Brand name required minimum 2 characters !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>5, 'message'=>$validationError);
	    }else{

            if($_FILES["brandimage"]['name'] != ''){
                if($_FILES["brandimage"]['size'] != '' && $_FILES["brandimage"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                    $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                    echo json_encode($json);
                    exit;
                }
                $image = uploadFile('brandimage', 'BRAND', BRAND_PATH, '*', "", 1, BRAND_LOCAL_PATH);
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

            $this->Brand->_where = array('name' => $name);
            $Count = $this->Brand->CountRecords();

            if($Count==0){
                
                $this->Brand->_where = array();
                $this->Brand->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
                $brand = $this->Brand->getRecordsById();
                
                $maxpriority = (!empty($brand))?$brand['maxpriority']:1;
                
                $InsertData = array('name' => $name,
                                    'image'=>$image,
                                    'priority' => $maxpriority,
                                    'status' => $status,
                                    'createddate' => $createddate,
                                    'addedby' => $addedby,                              
                                    'modifieddate' => $createddate,                             
                                    'modifiedby' => $addedby ,
                                    "channelid"=>$CHANNELID,
                                    "memberid"=>$addedby,
                                    "usertype"=>1
                                );
            
                $BrandID = $this->Brand->Add($InsertData);
                
                if($BrandID){
                    
                    $json = array('error'=>1); // Brand inserted successfully
                } else {
                    $json = array('error'=>0); // Brand not inserted 
                }
            } else {
                $json = array('error'=>2); // Brand already added
            }
        }
        echo json_encode($json);
    }
     public function brand_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Brand";
        $this->viewData['module'] = "brand/Add_brand";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['branddata'] = $this->Brand->getBrandDataByID($id);
       
        $this->channel_headerlib->add_javascript("topic","pages/add_brand.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function update_brand() {
        
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $modifieddate = $this->general_model->getCurrentDateTime();

        $name = trim($PostData['brandname']);
        $status = $PostData['status'];
       
        $this->form_validation->set_rules('brandname', 'brand name', 'required|min_length[3]',array('required'=>"Please enter brand name !",'min_length'=>"Brand name required minimum 2 characters !"));
        
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>5, 'message'=>$validationError);
	    }else{

            $brandid = trim($PostData['brandid']);
            $oldbrandimage= trim($PostData['oldbrandimage']);
            
            $this->Brand->_where = "id<>'".$brandid."' AND name = '".$name."'";
            $Count = $this->Brand->CountRecords();

            if($Count==0){
                if($_FILES["brandimage"]['name'] != '' && $oldbrandimage != ""){

                    if($_FILES["brandimage"]['size'] != '' && $_FILES["brandimage"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                        $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                        echo json_encode($json);
                        exit;
                    }

                    $FLNM1 = reuploadFile('brandimage', 'BRAND',$oldbrandimage, BRAND_PATH, '*', "", 1, BRAND_LOCAL_PATH);
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
                }else if($_FILES["brandimage"]['name'] != '' && $oldbrandimage == ""){

                    if($_FILES["brandimage"]['size'] != '' && $_FILES["brandimage"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                        $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                        echo json_encode($json);
                        exit;
                    }
                    $FLNM1 = uploadFile('brandimage', 'BRAND', BRAND_PATH, '*', "", 1, BRAND_LOCAL_PATH);
                    if($FLNM1 !== 0){
                        if($FLNM1==2){
                            $json = array('error'=>3);	// IMAGE NOT UPLOADED
                            echo json_encode($json);
                            exit;
                        }
                    } else {
                        $json = array('error'=>4); //INVALID IMAGE TYPE
                        echo json_encode($json);
                        exit;
                    }     
                }elseif($_FILES["brandimage"]['name'] == '' && $PostData['removebrandimage']==1) {
                    unlinkfile('BRAND', $oldbrandimage, BRAND_PATH);
                    $FLNM1 = "";
                } else {
                    $FLNM1 =  $oldbrandimage;
                }
                
                $updateData = array('name' => $name,
                                    'image' => $FLNM1,
                                    'status'=>$status,
                                    'modifiedby' => $modifiedby,
                                    'modifieddate' => $modifieddate,
                                    "channelid"=>$CHANNELID,
                                    "memberid"=>$modifiedby,
                                    "usertype"=>1
                                
                                );

                $this->Brand->_where = array('id' =>$brandid);
                $isUpdated = $this->Brand->Edit($updateData);
                
                if($isUpdated){
                    
                    $json = array('error'=>1); // Brand update successfully
                } else {
                    $json = array('error'=>0); // Brand not updated
                }
            } else {
                $json = array('error'=>2); // Brand already added
            }
        }
        echo json_encode($json);
    }

    public function check_brand_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            $this->readdb->select('brandid');
            $this->readdb->from(tbl_product);
            $where = array("brandid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }

            $this->readdb->select('brandid');
            $this->readdb->from(tbl_news);
            $where = array("brandid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }

        }
        echo $count;
    }

    public function brand_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(CHANNEL_URL . 'ADMINUSERTYPE'));
        $this->Brand->_where = array("id" => $PostData['id']);
        $this->Brand->Edit($updatedata);

        
        echo $PostData['id'];
    }

    public function delete_mul_brand() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            $this->readdb->select('brandid');
            $this->readdb->from(tbl_product);
            $where = array("brandid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
            $this->readdb->select('brandid');
            $this->readdb->from(tbl_news);
            $where = array("brandid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $checkuse++;
            }

            if($checkuse == 0){

                $this->Brand->_fields = 'id,name,image';
                $this->Brand->_where = array('id'=>$row);
                $branddata = $this->Brand->getRecordsByID();
    
                if(!empty($branddata)){
                    unlinkfile('BRAND', $branddata['image'], BRAND_PATH);
                    // Delete from essay data table

                    
                    $this->Brand->Delete(array('id'=>$row));
                }
            }
        }
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
            $this->Brand->edit_batch($updatedata, 'id');
        }
        
        echo 1;
    }
    
}?>