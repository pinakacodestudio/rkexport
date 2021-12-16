<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Photo_gallery extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Photo_gallery');
        $this->load->model('Photo_gallery_model', 'Photo_gallery');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Photo Gallery";
        $this->viewData['module'] = "photo_gallery/Photo_gallery";   

        $this->viewData['photogallerydata'] = $this->Photo_gallery->getPhotoGallery('priority','ASC');
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Photo Gallery','View photo gallery.',$this->session->userdata(base_url().'ADMINEMAIL'),$this->session->userdata(base_url().'ADMINNAME'));
        }
        $this->admin_headerlib->add_javascript("Photo_gallery", "pages/photo_gallery.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }      
    public function add_photo_gallery()
	{
		$this->viewData = $this->getAdminSettings('submenu','photo_gallery');
		$this->viewData['title'] = "Add Photo Gallery";
        $this->viewData['module'] = "photo_gallery/Add_photo_gallery";
            
        $this->load->model('Media_category_model', 'Media_category');
        $this->viewData['mediacategorydata'] = $this->Media_category->getMediaCategory();

		$this->admin_headerlib->add_javascript("photo_gallery","pages/add_photo_gallery.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function photo_gallery_add() {
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $title = isset($PostData['title']) ? trim($PostData['title']) : '';
        $image = isset($PostData['image']) ? trim($PostData['image']) : '';        
        $mediacategoryid = implode(',', $PostData['mediacategoryid']);
        $alttag = isset($PostData['alttag']) ? trim($PostData['alttag']) : '';
        $status = $PostData['status'];
        $createddate = $this->general_model->getCurrentDateTime();        
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        
        if($_FILES["image"]['name'] != ''){

            $image = uploadFile('image', 'PHOTOGALLERY' ,PHOTOGALLERY_PATH ,"jpeg|png|jpg|JPEG|PNG|JPG", '', 0, PHOTOGALLERY_LOCAL_PATH);
            if($image !== 0){	
                if ($image == 2) {
                    echo 3;//STAFF IMAGE NOT UPLOADED
                    exit;
                }
            }
        }else{
            $image = '';
        }

        $insertdata = array(
            "title" => $PostData['title'],            
            "image" => $image,
            "mediacategoryid" => $mediacategoryid,
            "alttag" => $PostData['alttag'],            
            "status" => $PostData['status'],
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );       
        $insertdata = array_map('trim', $insertdata);  
        if($PostData['priority']!=''){
            $insertdata['priority'] = $PostData['priority'];
        }else{
            $this->db->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_photogallery." as pg)",FALSE);
        }        
        $Add = $this->db->insert(tbl_photogallery, $insertdata);             
        if ($Add) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(1,'Photo Gallery','Add new '.$PostData['title'].' photo gallery.',$this->session->userdata(base_url().'ADMINEMAIL'),$this->session->userdata(base_url().'ADMINNAME'));
            }
            echo 1;
        } else {
            echo 0;
        }                    
    }

    public function edit_photo_gallery($Photogalleryid) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Photo Gallery";
        $this->viewData['module'] = "photo_gallery/Add_photo_gallery";
        $this->viewData['action'] = "1"; //Edit

        $this->Photo_gallery->_where = array('id' => $Photogalleryid);
        $this->viewData['photogallerydata'] = $this->Photo_gallery->getRecordsByID();

        
        $this->load->model('Media_category_model', 'Media_category');
        $this->viewData['mediacategorydata'] = $this->Media_category->getMediaCategory();
        
        $this->admin_headerlib->add_javascript("Photo_gallery", "pages/add_photo_gallery.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_photo_gallery() {
        
        $PostData = $this->input->post();

        $PhotogalleryID = $PostData['photogalleryid'];
        $title = isset($PostData['title']) ? trim($PostData['title']) : '';
        $image = isset($PostData['image']) ? trim($PostData['image']) : '';        
        $mediacategoryid = implode(',', $PostData['mediacategoryid']);
        $alttag = isset($PostData['alttag']) ? trim($PostData['alttag']) : '';
        $status = $PostData['status'];
        $mediacategoryid = implode(',',  $PostData['mediacategoryid']);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $oldphotogalleryimage = trim($PostData['oldphotogalleryimage']);
        $removeoldImage = trim($PostData['removeoldImage']);

        if($_FILES["image"]['name'] != ''){

			$image = reuploadfile('image', 'PHOTOGALLERY', $oldphotogalleryimage ,PHOTOGALLERY_PATH,"jpeg|png|jpg|JPEG|PNG|JPG", '', 0, PHOTOGALLERY_LOCAL_PATH);
			if($image !== 0){
				if($image==2){
					echo 3;//file not uploaded
                   	exit;
				}
			}else{
				echo 4;//invalid image type
				exit;
			}	
		}else if($_FILES["image"]['name'] == '' && $oldphotogalleryimage !='' && $removeoldImage=='1'){
			unlinkfile('PHOTOGALLERY', $oldphotogalleryimage, PHOTOGALLERY_PATH);
			$image = '';
		}else if($_FILES["image"]['name'] == '' && $oldphotogalleryimage ==''){
			$image = '';
		}else{
            $image = $oldphotogalleryimage;
        }
        
        $updatedata = array(
                    "title" => $PostData['title'],                   
                    "image" => $image,
                    "mediacategoryid" => $mediacategoryid,
                    "alttag" => $PostData['alttag'],
                    "priority" => $PostData['priority'],
                    "status" => $PostData['status'],
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
        );
        $updatedata = array_map('trim', $updatedata);
        $this->Photo_gallery->_where = array('id' => $PhotogalleryID);
        $this->Photo_gallery->Edit($updatedata);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(2,'Photo Gallery','Edit '.$PostData['title'].' photo gallery.',$this->session->userdata(base_url().'ADMINEMAIL'),$this->session->userdata(base_url().'ADMINNAME'));
        }
        
        echo 1;
    }

    public function photo_gallery_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Photo_gallery->_where = array("id" => $PostData['id']);
        $this->Photo_gallery->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Photo_gallery->_where = array("id"=>$PostData['id']);
            $data = $this->Photo_gallery->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['title'].' photo gallery.';
            
            $this->general_model->addActionLog(2,'Photo Gallery', $msg);
        }
        echo $PostData['id'];
    }
    public function check_photo_gallery_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }

    public function delete_mul_photo_gallery(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
			if($ADMINID!=$row){

				$this->Photo_gallery->_fields = "id,image";
            	$this->Photo_gallery->_where = "id=$row AND (id = 1 OR id = $ADMINID)";
            	$Count = $this->Photo_gallery->getRecordsByID();

				if(count($Count) == 0){

					$this->Photo_gallery->_fields = "id,title,image";
	            	$this->Photo_gallery->_where = "id=$row";
            		$Count = $this->Photo_gallery->getRecordsByID();

                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(3,'Photo Gallery','Delete '.$Count['title'].' photo gallery.',$this->session->userdata(base_url().'ADMINEMAIL'),$this->session->userdata(base_url().'ADMINNAME'));
                    }

					unlinkfile('PHOTOGALLERY', $Count['image'], PHOTOGALLERY_PATH);
					$this->Photo_gallery->Delete(array('id'=>$row));
					
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
            $this->Photo_gallery->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Photo Gallery','Change photo gallery priority.');
		}
        echo 1;
    }
    
}

?>