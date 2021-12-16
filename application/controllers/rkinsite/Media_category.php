<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Media_category extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Media_category');
        $this->load->model('Media_category_model', 'Media_category');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Media Category";
        $this->viewData['module'] = "media_category/Media_category";     
        $this->viewData['mediacategorydata'] = $this->Media_category->get_all_listdata('priority','ASC'); 

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Media Category','View media category.');
        }

        $this->admin_headerlib->add_javascript("Media_category", "pages/media_category.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }      
    public function add_media_category()
	{
		$this->viewData = $this->getAdminSettings('submenu','media_category');
		$this->viewData['title'] = "Add Media Category";
        $this->viewData['module'] = "media_category/Add_media_category";
        
		$this->admin_headerlib->add_javascript("Media_category","pages/add_media_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function media_category_add() {
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $name = trim($PostData['name']);
        $createddate = $this->general_model->getCurrentDateTime();        
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $CheckDuplicateValue = $this->Media_category->CheckDuplicateValue($name);
        
        if ($CheckDuplicateValue != 0){
            
            $insertdata = array(
                "name" => $PostData['name'],            
                "status" => $PostData['status'],
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );
            
            $insertdata = array_map('trim', $insertdata);  
            $this->db->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_mediacategory." as mc)",FALSE);
            $Add = $this->db->insert(tbl_mediacategory, $insertdata);
            if ($Add) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Media Category','Add new '.$PostData['name'].' media category.');
                }
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }                            
    }
    public function edit_media_category($ID) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Media Category";
		$this->viewData['module'] = "media_category/Add_media_category";
		$this->viewData['action'] = "1";//Edit
        
        
        $this->Media_category->_where = array('id' => $ID);
        $this->viewData['mediacategorydata'] = $this->Media_category->getRecordsByID();

		$this->admin_headerlib->add_javascript("Media_category","pages/add_media_category.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function update_media_category() {
        
        $PostData = $this->input->post();
        $name = trim($PostData['name']);
        $ID = $PostData['mediacategoryid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');        
        $CheckDuplicateValue = $this->Media_category->CheckDuplicateValue($name,$ID);
        
        if ($CheckDuplicateValue != 0){
            
            $updatedata = array(
                "name" => $PostData['name'],                    
                "status" => $PostData['status'],
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $updatedata = array_map('trim', $updatedata);
            $this->Media_category->_where = array('id' => $ID);
            $this->Media_category->Edit($updatedata);
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Media Category','Edit '.$PostData['name'].' media category.');
            }
            echo 1;
        } else {
            echo 2;
        }
    }

    public function media_category_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Media_category->_where = array("id" => $PostData['id']);
        $this->Media_category->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Media_category->_where = array("id"=>$PostData['id']);
            $data = $this->Media_category->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' media category.';
            
            $this->general_model->addActionLog(2,'Media Category', $msg);
        }
        echo $PostData['id'];
    }
    public function check_media_category_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
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
    public function delete_mul_media_category(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Media_category->_fields = "name";
                $this->Media_category->_where = array("id"=>$row);
                $data = $this->Media_category->getRecordsByID();
                
                $this->general_model->addActionLog(3,'Media Category','Delete '.$data['name'].' media category.');
            }

            $this->Media_category->Delete(array("id"=>$row));
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
            $this->Media_category->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Media Category','Change media category priority.');
		}
        echo 1;
    }
}

?>