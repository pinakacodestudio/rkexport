<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_unit extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData = $this->getAdminSettings('submenu', 'Product_unit');
    }

    public function index() {
        $this->viewData['title'] = "Product Unit";
        $this->viewData['module'] = "product_unit/Product_unit";
        $this->viewData['VIEW_STATUS'] = "1";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Unit','View product unit.');
        }

        $this->admin_headerlib->add_javascript("product_unit", "pages/product_unit.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Product_unit->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = $image ='';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'product-unit/product-unit-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){

                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'product-unit/product-unit-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';

                    
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'product-unit/product-unit-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'product-unit/check-product-unit-use","Product&nbsp;Unit","'.ADMIN_URL.'product-unit/delete-mul-product-unit") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            
            $row[] = ++$counter;
            $row[] = $datarow->name;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = $actions;
             $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_unit->count_all(),
                        "recordsFiltered" => $this->Product_unit->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
 
    public function add_product_unit() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Product Unit";
        $this->viewData['module'] = "product_unit/Add_product_unit";   
        $this->viewData['VIEW_STATUS'] = "0";
        
        $this->admin_headerlib->add_javascript("product_unit", "pages/add_product_unit.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function product_unit_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        $name = trim($PostData['name']);
        $status = $PostData['unitstatus'];
     
        $this->form_validation->set_rules('name', 'product unit name', 'required|min_length[2]',array('required'=>"Please enter product unit name !",'min_length'=>"Product unit name required minimum 2 characters !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{

            $this->Product_unit->_where = array('name' => $name);
            $Count = $this->Product_unit->CountRecords();

            if($Count==0){
                
                $InsertData = array('name' => $name,
                                    'status' => $status,
                                    'createddate' => $createddate,
                                    'addedby' => $addedby,                              
                                    'modifieddate' => $createddate,                             
                                    'modifiedby' => $addedby 
                                );
            
                $ProductUnitID = $this->Product_unit->Add($InsertData);
                
                if($ProductUnitID){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Product Unit','Add new '.$name.' product unit.');
                    }
                    $json = array('error'=>1,'unitid'=>$ProductUnitID,'name'=>$name,'status'=>$status); // Product unit inserted successfully
                } else {
                    $json = array('error'=>0); // Product unit not inserted 
                }
            } else {
                $json = array('error'=>2); // Product unit already added
            }
        }
        echo json_encode($json);
    }
     public function product_unit_edit($id) {
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Product Unit";
        $this->viewData['module'] = "product_unit/Add_product_unit";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = "1"; //Edit
       
        $this->viewData['productunitdata'] = $this->Product_unit->getProductUnitDataByID($id);
       
        $this->admin_headerlib->add_javascript("add_product_unit","pages/add_product_unit.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_product_unit() {
        
        $PostData = $this->input->post();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $productunitid = trim($PostData['productunitid']);
        $name = trim($PostData['name']);
        $status = $PostData['unitstatus'];
       
        $this->form_validation->set_rules('name', 'product unit name', 'required|min_length[2]',array('required'=>"Please enter product unit name !",'min_length'=>"Product unit name required minimum 2 characters !"));
        
        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{

            $this->Product_unit->_where = array("id<>"=>$productunitid,'name' => $name);
            $Count = $this->Product_unit->CountRecords();

            if($Count==0){
                
                $updateData = array('name' => $name,
                                    'status'=>$status,
                                    'modifiedby' => $modifiedby,
                                    'modifieddate' => $modifieddate);

                $this->Product_unit->_where = array('id' =>$productunitid);
                $isUpdated = $this->Product_unit->Edit($updateData);
                
                if($isUpdated){
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(2,'Product Unit','Edit '.$name.' product unit.');
                    }
                    $json = array('error'=>1); // Product unit update successfully
                } else {
                    $json = array('error'=>0); // Product unit not updated
                }
            } else {
                $json = array('error'=>2); // Product unit already added
            }
        }
        echo json_encode($json);
    }

    public function check_product_unit_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            
            /* $this->readdb->select('product_unitid');
            $this->readdb->from(tbl_product);
            $where = array("product_unitid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            } */
        }
        echo $count;
    }

    public function product_unit_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Product_unit->_where = array("id" => $PostData['id']);
        $this->Product_unit->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Product_unit->_where = array("id"=>$PostData['id']);
            $data = $this->Product_unit->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' product unit.';
            
            $this->general_model->addActionLog(2,'Product Unit', $msg);
        }
        echo $PostData['id'];
    }

    public function delete_mul_product_unit() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $checkuse = 0;
            /* $this->readdb->select('product_unitid');
            $this->readdb->from(tbl_product);
            $where = array("product_unitid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            } */
            
            if($checkuse == 0){
                $this->Product_unit->_where = array("id"=>$row);
				$unitdata = $this->Product_unit->getRecordsById();
				
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(3,'Product Unit','Delete '.$unitdata['name'].' product unit.');
                }
                
                $this->Product_unit->Delete(array('id'=>$row));
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
            $this->Product_unit->edit_batch($updatedata, 'id');
        }
        echo 1;
    }

    public function addunitformodal() {
    
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Product Unit";
        $this->viewData['module'] = "product_unit/Add_product_unit";   
        $this->viewData['modalview'] = "1";
        
        echo $this->load->view(ADMINFOLDER.'product_unit/Add_product_unit',$this->viewData,true);
    
      }
    
}?>