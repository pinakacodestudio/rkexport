<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Advertisement extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Advertisement');
        $this->load->model('Advertisement_model', 'Advertisement');
    }

    public function index() {
      $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);

      $this->viewData['title'] = "Advertisement";
      $this->viewData['module'] = "advertisement/Advertisement";
      /* $this->viewData['advertisementdata'] = $this->Advertisement->get_all_listdata('advertisement_id','DESC'); */
      
      if($this->viewData['submenuvisibility']['managelog'] == 1){
        $this->general_model->addActionLog(4,'Advertisement','View advertisement.');
      }

      $this->admin_headerlib->add_javascript("Advertisement", "pages/advertisement.js");
      $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {

      $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
      $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
      $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];

      $list = $this->Advertisement->get_datatables();
      
      $data = array();
      $counter = $_POST['start'];
      foreach ($list as $Advertisement) {
          $row = array();
          $actions = $checkbox = $image ='';

          $row[] = ++$counter;
          if(isset($this->AdPage[$Advertisement->adpage_id])){
            $row[] = $this->AdPage[$Advertisement->adpage_id];
          }else{
            $row[] = "";
          }
          if(isset($this->AdPageSection[$Advertisement->adpage_id][$Advertisement->adpage_section_id])){
            $row[] = $this->AdPageSection[$Advertisement->adpage_id][$Advertisement->adpage_section_id];
          }else{
            $row[] = "";
          }

          if($Advertisement->image!=''){
            $image = '<img src="'.ADVERTISEMENT.$Advertisement->image.'" class="thumbwidth">';
          }     
      
          if($Advertisement->adtype==1){
            $row[] = "Google";
          }elseif($Advertisement->adtype==2){
            $row[] = "Amazon";
          }elseif($Advertisement->adtype==3){
            $row[] = "Custome Add";
          }
          
          $Action='';
          if (strpos(trim($this->viewData['submenuvisibility']['submenuedit'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
            $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'advertisement/edit-advertisement/'.$Advertisement->id.'" title='.edit_title.'>'.edit_text.'</a>';
          }

          if(strpos(trim($this->viewData['submenuvisibility']['submenuedit'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
            if($Advertisement->status==1){
                $Action .= '<span id="span'.$Advertisement->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Advertisement->id.',\''.ADMIN_URL.'advertisement/advertisementenabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
            }
            else{
                $Action .='<span id="span'.$Advertisement->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Advertisement->id.',\''.ADMIN_URL.'advertisement/advertisementenabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
            }
          }

          if(strpos(trim($this->viewData['submenuvisibility']['submenudelete'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
            $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Advertisement->id.',"","Advertisement","'.ADMIN_URL.'advertisement/deletemuladvertisement") >'.delete_text.'</a>';
          }
          // status
          $row[] = $image;
          $row[] = $Action;

          if(strpos(trim($this->viewData['submenuvisibility']['submenudelete'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
            $row[] = '<div class="checkbox">
                            <input id="deletecheck'.$Advertisement->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Advertisement->id.'" name="deletecheck'.$Advertisement->id.'" class="checkradios">
                            <label for="deletecheck'.$Advertisement->id.'"></label>
                          </div>';
          }else{
            $row[] = "";                
          }

          $data[] = $row;
      }
      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->Advertisement->count_all(),
          "recordsFiltered" => $this->Advertisement->count_filtered(),
          "data" => $data,
      );
      echo json_encode($output);
    }

    public function add_advertisement() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Advertisement";
        $this->viewData['module'] = "advertisement/Add_advertisement";
        
        $this->admin_headerlib->add_javascript("Advertisement", "pages/addadvertisement.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function advertisement_add() {
        $PostData = $this->input->post();

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->Advertisement->_where = array('adpage_id'=>$PostData['adpage_id'],
        'adpage_section_id'=>$PostData['adpage_section_id']);
        $adtype = isset($PostData['adtype']) ? trim($PostData['adtype']) : '';
        $google_ad = isset($PostData['google_ad']) ? trim($PostData['google_ad']) : '';
        $amazon_ad = isset($PostData['amazon_ad']) ? trim($PostData['amazon_ad']) : ''; 
        $image = isset($PostData['image']) ? trim($PostData['image']) : '';
        $status = $PostData['status'];
        $Count = $this->Advertisement->CountRecords();
        if($_FILES["image"]['name'] != ''){
          if($_FILES["image"]['size'] != '' && $_FILES["image"]['size'] >= UPLOAD_MAX_FILE_SIZE){
              $json = array('error'=>6);	// IMAGE FILE SIZE IS LARGE
              echo json_encode($json);
              exit;
          }
          $image = uploadFile('image', 'ADVERTISEMENT_PATH', ADVERTISEMENT_PATH, '*', "", 1, ADVERTISEMENT_LOCAL_PATH);
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
   
        if ($Count == 0) {
            $insertdata = array(
                'adpage_id'=>$PostData['adpage_id'],
                'adpage_section_id'=>$PostData['adpage_section_id'],
                'adtype'=>$PostData['adtype'],
                'google_ad'=>$PostData['google_ad'],
                'amazon_ad'=>$PostData['amazon_ad'],
                "image" => $image,  
                'status'=>$PostData['status'],
                "created_date" => $createddate,
                "modified_date" => $createddate,
                "created_by" => $addedby,
                "modified_by" => $addedby
            );
            $insertdata = array_map('trim', $insertdata);

            $Add = $this->Advertisement->Add($insertdata);
            if ($Add) {
              if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(1,'Advertisement','Add new advertisement on '.$this->AdPageSection[$PostData['adpage_id']][$PostData['adpage_section_id']].'.');
              }
              echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2;
        }
    }

    public function edit_advertisement($advid) {
      $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
      $this->viewData['title'] = "Edit Advertisement";
      $this->viewData['module'] = "advertisement/Add_advertisement";
      $this->viewData['action'] = "1"; //Edit

      $this->Advertisement->_where = array('id' => $advid);
      $this->viewData['advertisementdata'] = $this->Advertisement->getRecordsByID();
       //print_r($this->viewData['advertisementdata']);die;

      $this->admin_headerlib->add_javascript("Advertisement", "pages/addadvertisement.js");
      $this->load->view(ADMINFOLDER . 'template', $this->viewData);
  }
  
  

  public function updateadvertisement() {
      $PostData = $this->input->post();
         
      $AdvID = $PostData['advid'];
      
      
      $modifieddate = $this->general_model->getCurrentDateTime();
      $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
      $newscategoryid = trim($PostData['advid']);
      
      $this->Advertisement->_where = array('adpage_id'=>$PostData['adpage_id'],
      'adpage_section_id'=>$PostData['adpage_section_id']);
      $adtype = isset($PostData['adtype']) ? trim($PostData['adtype']) : '';
      $google_ad = isset($PostData['google_ad']) ? trim($PostData['google_ad']) : '';
      $amazon_ad = isset($PostData['amazon_ad']) ? trim($PostData['amazon_ad']) : ''; 
      $image = isset($PostData['image']) ? trim($PostData['image']) : '';
      $status = $PostData['status'];
      $Count = $this->Advertisement->CountRecords();
      $oldtestimonialsimage = trim($PostData['oldtestimonialsimage']);
      $removeoldImage = trim($PostData['removeoldImage']);

      if($_FILES["image"]['name'] != ''){

          $image = reuploadfile('image', 'ADVERTISEMENT_PATH', $oldtestimonialsimage,ADVERTISEMENT_PATH ,"jpeg|png|jpg|JPEG|PNG|JPG", '', 1, TESTIMONIALS_LOCAL_PATH);
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
      unlinkfile('ADVERTISEMENT_PATH', $oldtestimonialsimage, TESTIMONIALS_PATH);
      $image = '';
    }else if($_FILES["image"]['name'] == '' && $oldtestimonialsimage ==''){
      $image = '';
    }else{
      $image = $oldtestimonialsimage;
    }
      

     
          $updatedata = array(
              'adpage_id'=>$PostData['adpage_id'],
              'adpage_section_id'=>$PostData['adpage_section_id'],
              'adtype'=>$PostData['adtype'],
              'google_ad'=>$PostData['google_ad'],
              'amazon_ad'=>$PostData['amazon_ad'],
              "image" => $image,  
              "status" => $PostData['status'],
              "modified_date" => $modifieddate,
              "modified_by" => $modifiedby
          );
          
          $this->Advertisement->_where = array('id' => $AdvID);
          $this->Advertisement->Edit($updatedata);
          
          if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(2,'Advertisement','Edit '.$this->AdPageSection[$PostData['adpage_id']][$PostData['adpage_section_id']].' section advertisement.');
          }
          echo 1;
  }


    public function advertisementenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modified_date" => $modifieddate, "modified_by" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Advertisement->_where = array("id" => $PostData['id']);
        $this->Advertisement->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
          $this->Advertisement->_where = array("id"=>$PostData['id']);
          $data = $this->Advertisement->getRecordsById();
          $msg = ($PostData['val']==0?"Disable":"Enable").' '.$this->AdPageSection[$data['adpage_id']][$data['adpage_section_id']].' advertisement.';
          
          $this->general_model->addActionLog(2,'Advertisement', $msg);
      }
        echo $PostData['id'];
    }

    public function getpagesection() {
      //print_r('page');die
      $PostData = $this->input->post();
      if(isset($PostData['page'])){
        if(isset($this->AdPageSection[$PostData['page']])){
          echo json_encode(array($this->AdPageSection[$PostData['page']]));
        }else{
          echo json_encode(array());  
        }
      }else{
        echo json_encode(array());
      }
    }

    public function checkadvertisementuse(){
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

    public function deletemuladvertisement(){
      $PostData = $this->input->post();
      $ids = explode(",",$PostData['ids']);
      $count = 0;
      $ADMINID = $this->session->userdata(base_url().'ADMINID');
      foreach($ids as $row){

          if($this->viewData['submenuvisibility']['managelog'] == 1){

            $this->Advertisement->_fields = "adpage_id,adpage_section_id";
            $this->Advertisement->_where = array('id'=>$row);
            $data = $this->Advertisement->getRecordsByID();
            
            $this->general_model->addActionLog(3,'Advertisement','Delete '.$this->AdPageSection[$data['adpage_id']][$data['adpage_section_id']].' section advertisement.');
          }
          
          $this->Advertisement->Delete(array('id'=>$row));
      }
    }
}

?>