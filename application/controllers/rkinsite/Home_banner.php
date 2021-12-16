<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home_banner extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Home_banner');
        $this->load->model('Home_banner_model', 'Home_banner');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->load->model("Product_combination_model","Product_combination");  
        $this->viewData['title'] = "Home Banner";
        $this->viewData['module'] = "home_banner/Home_banner";
        $this->Home_banner->_fields = "id,channelid,productid,IFNULL((SELECT name FROM ".tbl_product." WHERE id=productid),'') as productname,IFNULL((SELECT isuniversal FROM ".tbl_product." WHERE id=productid),'') as isuniversal,IF(productid!=0,(SELECT GROUP_CONCAT(pc.variantid) FROM ".tbl_productcombination." as pc INNER JOIN ".tbl_productprices." as pp on pp.id=pc.priceid WHERE pp.productid=productid),'') as variantid,title,subtitle,urllink,image,inorder,status";

        if(!empty($this->session->userdata(base_url().'CHANNEL'))){
            $channel = explode(",",$this->session->userdata(base_url().'CHANNEL'));
            foreach($channel as $channelid){
                $where[] = "(FIND_IN_SET('".$channelid."', channelid)>0)";
            }
            $this->Home_banner->_where =  array("(".implode(" OR ", $where).")"=>null);
        }
        $this->Home_banner->_order = "inorder asc";
        $homebannerdata = $this->Home_banner->getRecordByID();
       
        $homebannerarr=array();
        if(!empty($homebannerdata)){
            foreach($homebannerdata as $homebanner){
                $varianthtml = '';
                if($homebanner['productid']!=0){
                    if($homebanner['isuniversal']==0 && $homebanner['variantid']!=''){
                        $variantdata = $this->Product_combination->getProductVariantDetails($homebanner['productid'],$homebanner['variantid']);
        
                        if(!empty($variantdata)){
                            $varianthtml .= "<div class='row' style=''>";
                            foreach($variantdata as $variant){
                                $varianthtml .= "<div class='col-md-12 p-n'>";
                                $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                                $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                                $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                                $varianthtml .= "</div>";
                            }
                            $varianthtml .= "</div>";
                        }
                        $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$homebanner['productid'].'" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" target="_blank" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($homebanner['productname']).'</a>';
                    }else{
                        $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$homebanner['productid'].'" target="_blank">'.ucwords($homebanner['productname']).'</a>';
                    }
                }else{
                    $productname = "-";
                }
                $homebannerarr[]= array(
                    "id"=>$homebanner['id'],
                    "channelid"=>$homebanner['channelid'],
                    "productname"=>$productname,
                    "title"=>$homebanner['title'],
                    "subtitle"=>$homebanner['subtitle'],
                    "urllink"=>$homebanner['urllink'],
                    "image"=>$homebanner['image'],
                    "inorder"=>$homebanner['inorder'],
                    "status"=>$homebanner['status']
                );

            }
        }
        $this->viewData['homebannerdata'] = $homebannerarr;
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Home Banner','View home banner.');
        }

        $this->admin_headerlib->add_javascript("Home_banner", "pages/home_banner.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function home_banner_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Home Banner";
        $this->viewData['module'] = "home_banner/Add_home_banner";
       
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');

        $this->admin_headerlib->add_javascript("Home_banner", "pages/add_home_banner.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_home_banner() {
        $PostData = $this->input->post();
       
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $channelid = (!empty($PostData['channelid']))?implode(",", $PostData['channelid']):'';
       
        if($_FILES["profile_image"]['name'] != ''){
            $image = uploadFile('profile_image', 'HOMEBANNER', HOMEBANNER_PATH, "jpeg|png|jpg|JPEG|PNG|JPG", "", 1, HOMEBANNER_LOCAL_PATH, HOMEBANNER_IMG_WIDTH, HOMEBANNER_IMG_HEIGHT);
            if($image !== 0){   
                if($image==2){
                    echo 3;//file not uploaded
                    exit;
                }
                
            }else{
                echo 4;//INVALID USER IMAGE TYPE
                exit;
            }   
        }else{
            $image = '';
        }

        $insertdata = array(
            "channelid"=>$channelid,
            "title" => $PostData['title'],
            "subtitle" => $PostData['subtitle'],
            "displayduration" => $PostData['displayduration'],
            "productid" => $PostData['productid'],
            "urllink" => $PostData['urllink'],
            "image"=>$image,
            "inorder"=>$PostData['sort_order'],
            "status" => $PostData['status'],
            "type" => 0,
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );
        $insertdata = array_map('trim', $insertdata);

        $Add = $this->Home_banner->Add($insertdata);
        if ($Add) {
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(1,'Home Banner','Add new home banner.');
            }
            echo 1;
        } else {
            echo 0;
        }
       
    }

    public function home_banner_edit($Homebannerid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Home Banner";
        $this->viewData['module'] = "home_banner/Add_home_banner";
        $this->viewData['action'] = "1"; //Edit
        
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');

        $this->Home_banner->_where = array('id' => $Homebannerid);
        $this->viewData['homebannerdata'] = $this->Home_banner->getRecordsByID();
        
        $this->admin_headerlib->add_javascript("Home_banner", "pages/add_home_banner.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_home_banner() {

        $PostData = $this->input->post();

        $oldprofileimage = trim($PostData['oldprofileimage']);
        $removeoldImage = trim($PostData['removeoldImage']);

        $channelid = (!empty($PostData['channelid']))?implode(",", $PostData['channelid']):'';

        if($_FILES["profile_image"]['name'] != ''){
            
            $image = reuploadfile('profile_image', 'HOMEBANNER', $oldprofileimage, HOMEBANNER_PATH, "jpeg|png|jpg|JPEG|PNG|JPG", "", 1, HOMEBANNER_LOCAL_PATH, HOMEBANNER_IMG_WIDTH, HOMEBANNER_IMG_HEIGHT);
            if($image !== 0){  
                if($image==2){
                    echo 3;//file not uploaded
                    exit;
                }
            }else{
                echo 4;//invalid image type
                exit;
            }
        }else if($_FILES["profile_image"]['name'] == '' && $oldprofileimage !='' && $removeoldImage=='1'){
            unlinkfile('HOMEBANNER', $oldprofileimage, HOMEBANNER_PATH);
            $image = '';
        }else if($_FILES["profile_image"]['name'] == '' && $oldprofileimage ==''){
            $image = '';
        }else{
            $image = $oldprofileimage;
        }

        $HomebannerID = $PostData['homebanner_id'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $updatedata = array(
            "channelid"=>$channelid,
            "title" => $PostData['title'],
            "subtitle" => $PostData['subtitle'],
            "inorder"=>$PostData['sort_order'],
            "urllink" => $PostData['urllink'],
            "displayduration" => $PostData['displayduration'],
            "productid" => $PostData['productid'],
            "image"=>$image,
            "status" => $PostData['status'],
            "type" => 0,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby
        );
        $this->Home_banner->_where = array('id' => $HomebannerID);
        $this->Home_banner->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(2,'Home Banner','Edit home banner.');
        }
        echo 1;
    }

    public function home_banner_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Home_banner->_where = array("id" => $PostData['id']);
        $this->Home_banner->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Home_banner->_where = array("id"=>$PostData['id']);
            $data = $this->Home_banner->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' home banner.';
            
            $this->general_model->addActionLog(2,'Home Banner', $msg);
        }
        echo $PostData['id'];
    }

    public function check_home_banner_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }
    public function delete_mul_home_banner(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $this->Home_banner->_fields = "image";
            $this->Home_banner->_where = "id=$row";
            $HomebannerData = $this->Home_banner->getRecordsByID();
            if(!empty($HomebannerData['image'])){
                unlinkfile('HOMEBANNER', $HomebannerData['image'], HOMEBANNER_PATH);

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(3,'Home Banner','Delete home banner.');
                }
            }
            $this->Home_banner->Delete(array('id'=>$row));
        }
    }
}

?>