<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_category extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Blog_category');
        $this->load->model('Blog_category_model', 'Blog_category');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Blog Category";
        $this->viewData['module'] = "blog_category/Blog_category";
        $this->viewData['blogcategorydata'] = $this->Blog_category->getBlogCategory();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Blog Category','View blog category.');
        }

        $this->admin_headerlib->add_javascript("Blog_category", "pages/blogcategory.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function getactiveblogcategory(){
        $PostData = $this->input->post();

        if(isset($PostData["term"])){
            $Blogdata = $this->Blog_category->searchblogcategory(1,$PostData["term"]);
        }else if(isset($PostData["ids"])){
            $Blogdata = $this->Blog_category->searchblogcategory(0,$PostData["ids"]);
        }
        
        echo json_encode($Blogdata);
    }
    public function add_blog_category() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = " Add Blog Category";
        $this->viewData['module'] = "blog_category/Add_blog_category";
        
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("Blog_category", "pages/add_blog_category.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function blog_category_add() {
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
      
        $status = $PostData['status'];
        $createddate = $this->general_model->getCurrentDateTime();        
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $CheckDuplicateValue = $this->Blog_category->CheckDuplicateValue($name);
        
        if ($CheckDuplicateValue != 0){
            
            $insertdata = array(
                "name" => $PostData['name'], 
                "slug" => preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($PostData['name']))),      
                "status" => $PostData['status'],
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );
            
            $insertdata = array_map('trim', $insertdata);  
            $this->db->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_blogcategory." as b)",FALSE);
            $Add = $this->db->insert(tbl_blogcategory, $insertdata);
            if ($Add) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Blog Category','Add new '.$PostData['name'].' blog category.');
                }
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }     
    }
    public function edit_blog_category($id) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Blog Category";
        $this->viewData['module'] = "blog_category/Add_blog_category";
        $this->viewData['action'] = "1"; //Edit

        $this->Blog_category->_where = array('id' => $id);
        $this->viewData['blogcategorydatadata'] = $this->Blog_category->getRecordsByID();
        
        $this->admin_headerlib->add_javascript("Blog_category", "pages/add_blog_category.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_blog_category() {
        
        $PostData = $this->input->post();

        $ID = $PostData['id'];
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $slug = isset($PostData['slug']) ? trim($PostData['slug']) : '';
        $status = $PostData['status'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $CheckDuplicateValue = $this->Blog_category->CheckDuplicateValue($name,$ID);
        if ($CheckDuplicateValue != 0){        
          $updatedata = array(
                    "name" => $PostData['name'],  
                    "slug" => preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($PostData['name']))),              
                    "status" => $PostData['status'],
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
            );
            $updatedata = array_map('trim', $updatedata);
            $this->Blog_category->_where = array('id' => $ID);
            $this->Blog_category->Edit($updatedata);

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Blog Category','Edit '.$PostData['name'].' blog category.');
            }
            echo 1;
        } else {
            echo 2;
        }
    }     

    
    public function blog_category_enabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Blog_category->_where = array("id" => $PostData['id']);
        $this->Blog_category->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Blog_category->_where = array("id"=>$PostData['id']);
            $data = $this->Blog_category->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' blog category.';
            
            $this->general_model->addActionLog(2,'Blog Category', $msg);
        }
        echo $PostData['id'];
    }
    public function updatepriority(){

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
            $this->Blog_category->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Blog Category','Change blog category priority.');
		}
        echo 1;
    }
    public function check_blog_category_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->db->query("SELECT id FROM ".tbl_blogcategory." WHERE 
                    id IN (SELECT blogcategoryid FROM ".tbl_blog." WHERE blogcategoryid = $row)
                    ");

            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
    public function delete_mul_blog_category(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $query = $this->db->query("SELECT id FROM ".tbl_blogcategory." WHERE 
                    id IN (SELECT blogcategoryid FROM ".tbl_blog." WHERE blogcategoryid = $row)
                    ");

            if($query->num_rows() == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){

                    $this->Blog_category->_where = array("id"=>$row);
                    $data = $this->Blog_category->getRecordsById();

                    $this->general_model->addActionLog(3,'Blog Category','Delete '.$data['name'].' blog category.');
                }
            
                $this->Blog_category->Delete(array("id"=>$row));
            }
            
        }
    }
}

?>