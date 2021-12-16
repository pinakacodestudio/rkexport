<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends Channel_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Blog');
        $this->load->model('Blog_model', 'Blog');
        $this->load->model('Blog_category_model', 'Blog_category');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');

        $this->viewData['title'] = "Blog";
        $this->viewData['module'] = "blog/Blog";
        $this->viewData['blogdata'] = $this->Blog->getBloglist($channelid,$memberid);

        $this->channel_headerlib->add_bottom_javascripts("Blog", "pages/website_blog.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function add_blog() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Blog";
        $this->viewData['module'] = "blog/Add_blog";
        
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_bottom_javascripts("Blog", "pages/add_blog.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }
    
    public function blog_add() {
        $PostData = $this->input->post();
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
       
        $title = isset($PostData['title']) ? trim($PostData['title']) : '';
        $slug = preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($PostData['title'])));
        $status = $PostData['status'];
       
        if($_FILES["image"]['name'] != ''){
            if($_FILES["image"]['size'] != '' && $_FILES["image"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                echo json_encode($json);
                exit;
            }
            $image = uploadFile('image', 'BLOG_PATH', BLOG_PATH, '*', "", 1, BLOG_LOCAL_PATH);
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

        
        if($PostData['blogcategoryid']!='' && !is_numeric($PostData['blogcategoryid'])){

            $this->Blog_category->_where = "name='".trim($PostData['blogcategoryid'])."'";
            $BlogData = $this->Blog_category->getRecordsByID();
            $categoryslug = preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($PostData['blogcategoryid'])));
            if(count($BlogData)==0){

                $insertdata = array(
                    "channelid" => $channelid,
                    "memberid" => $memberid,
                    "name" => $PostData['blogcategoryid'],
                    "slug" => $categoryslug ,
                    "status" => $PostData['status'],
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "usertype" => 1,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                
                $insertdata = array_map('trim', $insertdata);
                $this->writedb->set($insertdata);
                $this->writedb->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_blogcategory." as bc)",FALSE);
                $this->writedb->insert(tbl_blogcategory);

                $PostData['blogcategoryid'] = $this->db->insert_id();

            }else{
                $PostData['blogcategoryid'] = $BlogData['id'];
            }
        }
        $CheckDuplicateValue = $this->Blog->CheckDuplicateValue($slug,'',$channelid,$memberid);

        if ($CheckDuplicateValue != 0){  
            $insertdata = array(
                "channelid" => $channelid,
                "memberid" => $memberid,
                "title" => $PostData['title'],
                "description" => $PostData['description'],
                "slug" => $slug,
                "blogcategoryid" => $PostData['blogcategoryid'],
                "image" => $image,
                "metatitle" => $PostData['metatitle'],
                "metadescription" => $PostData['metadescription'],
                "metakeywords" => $PostData['metakeywords'],
                "status" => $PostData['status'],
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "usertype" =>1,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );
            $insertdata = array_map('trim', $insertdata);
            
            $Add = $this->Blog->Add($insertdata);
            if ($Add) {
                echo 1;
            } 
        } else {
            echo 0;
        }
    }

    public function edit_blog($Blogid) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Blog";
        $this->viewData['module'] = "blog/Add_blog";
        $this->viewData['action'] = "1"; //Edit

        $this->Blog->_where = array('id' => $Blogid);
        $this->viewData['blogdata'] = $this->Blog->getRecordsByID();

        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_bottom_javascripts("Blog", "pages/add_blog.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function update_blog() {

        $PostData = $this->input->post();
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $memberid = $this->session->userdata(base_url().'MEMBERID');

        $ID = $PostData['id'];
        $title = isset($PostData['title']) ? trim($PostData['title']) : '';
        $slug = preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($PostData['title'])));
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');

        $status = $PostData['status'];

        $oldblogimage = trim($PostData['oldblogimage']);
        $removeoldImage = trim($PostData['removeoldImage']);
        if($_FILES["image"]['name'] != ''){

            $image = reuploadfile('image', 'BLOG_PATH', $oldblogimage,BLOG_PATH ,"jpeg|png|jpg|JPEG|PNG|JPG", '', 1, BLOG_LOCAL_PATH);
            if($image !== 0){	
                if ($image == 2) {
                    echo 3;//STAFF IMAGE NOT UPLOADED
                    exit;
				}
			}else{
				echo 4;//invalid image type
				exit;
			}	
		}else if($_FILES["image"]['name'] == '' && $oldblogimage !='' && $removeoldImage=='1'){
			unlinkfile('BLOG_PATH', $oldblogimage, BLOG_PATH);
			$image = '';
		}else if($_FILES["image"]['name'] == '' && $oldblogimage ==''){
			$image = '';
		}else{
			$image = $oldblogimage;
		}
          

        if($PostData['blogcategoryid']!='' && !is_numeric($PostData['blogcategoryid'])){

            $this->Blog_category->_where = "name='".trim($PostData['blogcategoryid'])."'";
            $BlogData = $this->Blog_category->getRecordsByID();
            $categoryslug = preg_replace("![^a-z0-9]+!i", "-", strtolower(trim($PostData['blogcategoryid'])));
            if(count($BlogData)==0){

                $insertdata = array(
                    "channelid" => $channelid,
                    "memberid" => $memberid,
                    "name" => $PostData['blogcategoryid'],
                    "slug" => $categoryslug ,
                    "status" => $PostData['status'],
                    "createddate" => $modifieddate,
                    "modifieddate" => $modifieddate,
                    "usertype" => 1,
                    "addedby" => $modifiedby,
                    "modifiedby" => $modifiedby
                );
                
                $insertdata = array_map('trim', $insertdata);
                $this->db->set($insertdata);
                $this->db->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_blogcategory." as bc)",FALSE);
                $this->db->insert(tbl_blogcategory);

                $PostData['blogcategoryid'] = $this->db->insert_id();

            }else{
                $PostData['blogcategoryid'] = $BlogData['id'];
            }
        }

        $CheckDuplicateValue = $this->Blog->CheckDuplicateValue($slug,$ID,$channelid,$memberid);
        if ($CheckDuplicateValue != 0){  
            $updatedata = array(
                        "channelid" => $channelid,
                        "memberid" => $memberid,
                        "title" => $PostData['title'],
                        "description" => $PostData['description'],
                        "slug" => $slug,
                        "blogcategoryid" => $PostData['blogcategoryid'],
                        "image" => $image,
                        "metatitle" => $PostData['metatitle'],
                        "metadescription" => $PostData['metadescription'],
                        "metakeywords" => $PostData['metakeywords'],
                        "status" => $PostData['status'],
                        "modifieddate" => $modifieddate,
                        "usertype" => 1,
                        "modifiedby" => $modifiedby
            );
            $updatedata = array_map('trim', $updatedata);
            
            $this->Blog->_where = array('id' => $ID);
            $this->Blog->Edit($updatedata);
            echo 1;
        } else {
            echo 0;
        }

    }

    public function blogenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Blog->_where = array("id" => $PostData['id']);
        $this->Blog->Edit($updatedata);

        echo $PostData['id'];
    }

    public function deletemulblog(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        foreach($ids as $row){
			if($MEMBERID!=$row){

				$this->Blog->_fields = "id,image";
            	$this->Blog->_where = "id=$row AND (id = 1 OR id = $MEMBERID)";
            	$Count = $this->Blog->getRecordsByID();

				if(count($Count) == 0){

					$this->Blog->_fields = "id,title,image";
	            	$this->Blog->_where = array('id'=>$row);
            		$Count = $this->Blog->getRecordsByID();

                    unlinkfile('BLOG', $Count['image'], BLOG_PATH);
					$this->Blog->Delete(array('id'=>$row));
					
				}
				
			}
		}
    }
}


?>