<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Testimonials extends Channel_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Testimonials');
        $this->load->model('testimonials_model', 'Testimonials');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $this->viewData['title'] = "Testimonials";
        $this->viewData['module'] = "testimonials/Testimonials";   
        $this->viewData['testimonialsdata'] = $this->Testimonials->getTestimonialsListData($channelid,$memberid);

        $this->channel_headerlib->add_javascript("testimonials", "pages/testimonials.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }
    public function add_testimonials()
	{
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
		
		$this->viewData['title'] = "Add Testimonials";
		$this->viewData['module'] = "testimonials/Add_testimonials";
		$this->channel_headerlib->add_javascript("testimonials","pages/add_testimonials.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function testimonials_add(){

        $PostData = $this->input->post(); 
        // print_r($PostData); exit;       
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');

        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $testimonials = isset($PostData['testimonials']) ? trim($PostData['testimonials']) : '';
        $image = isset($PostData['image']) ? trim($PostData['image']) : '';
        $status = $PostData['status'];
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');

       
        if($_FILES["image"]['name'] != ''){
            if($_FILES["image"]['size'] != '' && $_FILES["image"]['size'] >= UPLOAD_MAX_FILE_SIZE){
                $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
                echo json_encode($json);
                exit;
            }
            $image = uploadFile('image', 'TESTIMONIALS_PATH', TESTIMONIALS_PATH, '*', "", 1, TESTIMONIALS_LOCAL_PATH,TESTIMONIALSIMG_IMG_WIDTH,TESTIMONIALSIMG_IMG_HEIGHT);
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

        $insertdata = array(
            "channelid" => $channelid,
            "memberid" => $memberid,
            "name" => $PostData['name'],
            "testimonials" => $PostData['testimonials'],
            "image" => $image,            
            "status" => $PostData['status'],
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "usertype" => 1,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );
        
        $insertdata = array_map('trim', $insertdata);        
        $Add = $this->Testimonials->Add($insertdata);                
        if ($Add) {
            echo 1;
        } else {
            echo 0;
        }         
    }
    public function edit_testimonials($Testimonialsid) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Testimonials";
        $this->viewData['module'] = "testimonials/Add_testimonials";
        $this->viewData['action'] = "1"; //Edit

        $this->Testimonials->_where = array('id' => $Testimonialsid);
        $this->viewData['testimonialsdata'] = $this->Testimonials->getRecordsByID();

        $this->channel_headerlib->add_bottom_javascripts("Testimonials", "pages/add_testimonials.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function update_testimonials() {
        
        $PostData = $this->input->post();
        // print_r($PostData); exit;
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');


        $TestimonialsID = $PostData['testimonialsid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $testimonials = isset($PostData['testimonials']) ? trim($PostData['testimonials']) : '';
        $image = isset($PostData['image']) ? trim($PostData['image']) : '';
        $status = $PostData['status'];
        
        $oldtestimonialsimage = trim($PostData['oldtestimonialsimage']);
        $removeoldImage = trim($PostData['removeoldImage']);

        if($_FILES["image"]['name'] != ''){

            $image = reuploadfile('image', 'TESTIMONIALS_PATH', $oldtestimonialsimage,TESTIMONIALS_PATH ,"jpeg|png|jpg|JPEG|PNG|JPG", '', 1, TESTIMONIALS_LOCAL_PATH,TESTIMONIALSIMG_IMG_WIDTH,TESTIMONIALSIMG_IMG_HEIGHT);
            if($image !== 0){	
                if ($image == 2) {
                    echo 3;//STAFF IMAGE NOT UPLOADED
                    exit;
				}
			}else{
				echo 4;//invalid image type
				exit;
			}	
		}else if($_FILES["image"]['name'] == '' && $oldtestimonialsimage !='' && $removeoldImage=='1'){
			unlinkfile('TESTIMONIALS_PATH', $oldtestimonialsimage, TESTIMONIALS_PATH);
			$image = '';
		}else if($_FILES["image"]['name'] == '' && $oldtestimonialsimage ==''){
			$image = '';
		}else{
			$image = $oldtestimonialsimage;
		}
        
        $updatedata = array(
                    "channelid" => $channelid,
                    "memberid" => $memberid,
                    "name" => $PostData['name'],
                    "testimonials" => $PostData['testimonials'],
                    "image" => $image,  
                    "status" => $PostData['status'],
                    "modifieddate" => $modifieddate,
                    "usertype" => 1,
                    "modifiedby" => $modifiedby
        );
        $updatedata = array_map('trim', $updatedata);
        $this->Testimonials->_where = array('id' => $TestimonialsID);
        $this->Testimonials->Edit($updatedata);
        
        echo 1;
    }

    public function testimonials_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Testimonials->_where = array("id" => $PostData['id']);
        $this->Testimonials->Edit($updatedata);

        echo $PostData['id'];
    }
    public function delete_mul_testimonials(){
        $PostData = $this->input->post();    
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        foreach($ids as $row){
			if($MEMBERID!=$row){

				$this->Testimonials->_fields = "id,image";
            	$this->Testimonials->_where = "id=$row AND (id = 1 OR id = $MEMBERID)";
            	$Count = $this->Testimonials->getRecordsByID();

				if(count($Count) == 0){

					$this->Testimonials->_fields = "id,name,image";
	            	$this->Testimonials->_where = "id=$row";
            		$Count = $this->Testimonials->getRecordsByID();

					unlinkfile('TESTIMONIALSIMG', $Count['image'], TESTIMONIALS_PATH);
					$this->Testimonials->Delete(array('id'=>$row));
					
				}
				
			}
		}
    }
}

?>