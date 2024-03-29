<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Brand_model', 'Brand');
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Brand');
    }

    public function index() {
        $this->viewData['title'] = "Brand";
        $this->viewData['module'] = "brand/Brand";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Brand','View brand.');
        }

        $this->admin_headerlib->add_javascript("brand", "pages/brand.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Brand->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'brand/brand-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'brand/brand-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
                else{

                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'brand/brand-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';

                  
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'brand/check-brand-use","Brand","'.ADMIN_URL.'brand/delete-mul-brand","brandtable") >'.delete_text.'</a>';

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
                        "recordsTotal" => $this->Brand->count_all(),
                        "recordsFiltered" => $this->Brand->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_brand() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData = $this->getAdminSettings('submenu', 'Brand');
        $this->viewData['title'] = "Add Brand";
        $this->viewData['module'] = "brand/Add_brand";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->admin_headerlib->add_javascript("brand", "pages/add_brand.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function brand_add() {
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $name = trim($PostData['brandname']);
        $status = $PostData['status'];

        $addedby = $this->session->userdata(base_url().'ADMINID');
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
                                    'modifiedby' => $addedby 
                                );
            
                $BrandID = $this->Brand->Add($InsertData);
                
                if($BrandID){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Brand','Add new '.$name.' brand.');
                    }
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
       
        $this->admin_headerlib->add_javascript("topic","pages/add_brand.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_brand() {
        
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
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
                                    'modifieddate' => $modifieddate);

                $this->Brand->_where = array('id' =>$brandid);
                $isUpdated = $this->Brand->Edit($updateData);
                
                if($isUpdated){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Brand','Edit '.$name.' brand.');
                    }
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
        exit;
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
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Brand->_where = array("id" => $PostData['id']);
        $this->Brand->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Brand->_where = array("id"=>$PostData['id']);
            $data = $this->Brand->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' brand.';
            
            $this->general_model->addActionLog(2,'Brand', $msg);
        }
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
            // $this->readdb->select('brandid');
            // $this->readdb->from(tbl_product);
            // $where = array("brandid"=>$row);
            // $this->readdb->where($where);
            // $query = $this->readdb->get();
            // if($query->num_rows() > 0){
            //     $checkuse++;
            // }
            // $this->readdb->select('brandid');
            // $this->readdb->from(tbl_news);
            // $where = array("brandid"=>$row);
            // $this->readdb->where($where);
            // $query = $this->readdb->get();
            // if($query->num_rows() > 0){
            //   $checkuse++;
            // }
           
            if($checkuse == 0){
              
                $this->Brand->_fields = 'id,name,image';
                $this->Brand->_where = array('id'=>$row);
                 $branddata = $this->Brand->getRecordsByID();
               
                if(!empty($branddata)){
                    unlinkfile('BRAND', $branddata['image'], BRAND_PATH);
                    // Delete from essay data table

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(3,'Brand','Delete '.$branddata['name'].' brand.');
                    }
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
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Brand','Change brand priority.');
		}
        echo 1;
    }
    
}?>