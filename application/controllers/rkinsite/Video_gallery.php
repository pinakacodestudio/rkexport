<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Video_gallery extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Video_gallery');
        $this->load->model('Video_gallery_model', 'Video_gallery');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Video Gallery";
        $this->viewData['module'] = "video_gallery/Video_gallery";     

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Video Gallery','View video gallery.');
        }

        $this->viewData['videogallerydata'] = $this->Video_gallery->getVideoGallery('priority','ASC');        
        $this->admin_headerlib->add_javascript("Video_gallery", "pages/video_gallery.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }      
    public function add_video_gallery()
	{
		$this->viewData = $this->getAdminSettings('submenu','video_gallery');
		$this->viewData['title'] = "Add Video Gallery";
        $this->viewData['module'] = "video_gallery/Add_video_gallery";
        
        $this->load->model('Media_category_model', 'Media_category');
        $this->viewData['mediacategorydata'] = $this->Media_category->getMediaCategory();

		$this->admin_headerlib->add_javascript("Video_gallery","pages/add_video_gallery.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function video_gallery_add() {
        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $title = isset($PostData['title']) ? trim($PostData['title']) : '';
        $url = isset($PostData['url']) ? trim($PostData['url']) : '';
        $mediacategoryid = implode(',', $PostData['mediacategoryid']);
        $status = $PostData['status'];     
        $createddate = $this->general_model->getCurrentDateTime();        
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $insertdata = array(
            "title" => $PostData['title'],                        
            "url" => $PostData['url'],            
            "mediacategoryid" => $mediacategoryid,
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
            $this->db->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_videogallery." as vg)",FALSE);
        }        
        $Add = $this->db->insert(tbl_videogallery, $insertdata);             
        if ($Add) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(1,'Video Gallery','Add new '.$PostData['title'].' video gallery.');
            }
            echo 1;
        } else {
            echo 0;
        }                    
    }

    public function edit_video_gallery($Videogalleryid) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Video Gallery";
        $this->viewData['module'] = "video_gallery/Add_video_gallery";
        $this->viewData['action'] = "1"; //Edit

        $this->Video_gallery->_where = array('id' => $Videogalleryid);
        $this->viewData['videogallerydata'] = $this->Video_gallery->getRecordsByID();

        
        $this->load->model('Media_category_model', 'Media_category');
        $this->viewData['mediacategorydata'] = $this->Media_category->getMediaCategory();
        
        $this->admin_headerlib->add_javascript("Video_gallery", "pages/add_video_gallery.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_video_gallery() {
        
        $PostData = $this->input->post();

        $VideogalleryID = $PostData['videogalleryid'];
        $title = isset($PostData['title']) ? trim($PostData['title']) : '';
        $url = isset($PostData['url']) ? trim($PostData['url']) : '';
        $mediacategoryid = implode(',', $PostData['mediacategoryid']);
        $status = $PostData['status'];    
        $mediacategoryid = implode(',',  $PostData['mediacategoryid']);
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $updatedata = array(
                    "title" => $PostData['title'],                                   
                    "url" => $PostData['url'],
                    "mediacategoryid" => $mediacategoryid,
                    "priority" => $PostData['priority'],
                    "status" => $PostData['status'],
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
        );
        $updatedata = array_map('trim', $updatedata);
        $this->Video_gallery->_where = array('id' => $VideogalleryID);
        $this->Video_gallery->Edit($updatedata);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(2,'Video Gallery','Edit '.$PostData['title'].' video gallery.');
        }
        echo 1;
    }

    public function video_gallery_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Video_gallery->_where = array("id" => $PostData['id']);
        $this->Video_gallery->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Video_gallery->_where = array("id"=>$PostData['id']);
            $data = $this->Video_gallery->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['title'].' video gallery.';
            
            $this->general_model->addActionLog(2,'Video Gallery', $msg);
        }
        echo $PostData['id'];
    }
    public function check_video_gallery_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }

    public function delete_mul_video_gallery(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            
            $this->Video_gallery->_fields = "id,title";
            $this->Video_gallery->_where = array('id'=>$row);
            $data = $this->Video_gallery->getRecordsByID();
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Video Gallery','Delete '.$data['title'].' video gallery.');
            }
            
            $this->Video_gallery->Delete(array('id'=>$row));
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
            $this->Video_gallery->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Video Gallery','Change video gallery priority.');
		}
        echo 1;
    }
    
}

?>




