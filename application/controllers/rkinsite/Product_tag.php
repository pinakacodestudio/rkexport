<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Product_tag extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Product_tag');
        $this->load->model('Product_tag_model', 'Product_tag');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Product Tag";
        $this->viewData['module'] = "product_tag/Product_tag";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Tag','View product tag.');
        }

        //$this->viewData['producttagdata'] = $this->Product_tag->get_all_listdata('priority', 'asc');
        $this->admin_headerlib->add_javascript("Product_tag", "pages/product_tag.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() { 
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Product_tag->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = '';
            
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'product-tag/product-tag-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                if($datarow->status==1){
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'product-tag/product-tag-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $actions .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'product-tag/product-tag-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'product-tag/check-product-tag-use","Brand","'.ADMIN_URL.'product-tag/delete-mul-product-tag","producttagtable") >'.delete_text.'</a>';

                $checkbox = '<span style="display: none;">'.$datarow->priority.'</span><div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            

            $row['DT_RowId'] = $datarow->id;
            $row[] = ++$counter;
            $row[] = $datarow->tag;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_tag->count_all(),
                        "recordsFiltered" => $this->Product_tag->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function product_tag_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Product Tag";
        $this->viewData['module'] = "product_tag/Add_product_tag";
        
        $this->admin_headerlib->add_javascript("product_tag", "pages/add_product_tag.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_product_tag() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->Product_tag->_where = ("(tag='" . trim($PostData['tag']) . "' OR slug='" . trim($PostData['slug']) . "') AND channelid=0 AND memberid=0");
        $Count = $this->Product_tag->CountRecords();

        if ($Count == 0) {
            
            $insertdata = array(
                "tag" => $PostData['tag'],
                "slug" => $PostData['slug'],
                "status" => $PostData['status'],
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );
            $insertdata = array_map('trim', $insertdata);
            $this->writedb->set($insertdata);
            $this->writedb->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_producttag." as pt)",FALSE);
            $this->writedb->insert(tbl_producttag);

            $Add = $this->writedb->insert_id();
            
            if ($Add) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Product Tag','Add new '.$PostData['tag'].' product tag.');
                }
                echo 1;
            } else {
                echo 0;
            }
        } else {
            echo 2;
        }
    }

    public function product_tag_edit($Producttagid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Product Tag";
        $this->viewData['module'] = "product_tag/Add_product_tag";
        $this->viewData['action'] = "1"; //Edit

        $this->Product_tag->_where = array('id' => $Producttagid);
        $this->viewData['producttagdata'] = $this->Product_tag->getRecordsByID();

        $this->admin_headerlib->add_javascript("product_tag", "pages/add_product_tag.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_product_tag() {

        $PostData = $this->input->post();

        $ProducttagID = $PostData['producttagid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->Product_tag->_where = ("id!='" . $ProducttagID . "' AND (tag='" . trim($PostData['tag']) . "' OR slug='" . trim($PostData['slug']) . "') AND channelid=0 AND memberid=0");
        $Count = $this->Product_tag->CountRecords();

        if ($Count == 0) {
            $updatedata = array(
                "tag" => $PostData['tag'],
                "slug" => $PostData['slug'],
                "status" => $PostData['status'],
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            $this->Product_tag->_where = array('id' => $ProducttagID);
            $this->Product_tag->Edit($updatedata);

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Product Tag','Edit '.$PostData['tag'].' product tag.');
            }
            echo 1;
        } else {
            echo 2;
        }
    }

    public function product_tag_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Product_tag->_where = array("id" => $PostData['id']);
        $this->Product_tag->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Product_tag->_where = array("id"=>$PostData['id']);
            $data = $this->Product_tag->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['tag'].' product tag.';
            
            $this->general_model->addActionLog(2,'Product Tag', $msg);
        }
        echo $PostData['id'];
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
            $this->Product_tag->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Product Tag','Change product tag priority.');
		}
        echo 1;
    }
    public function getactiveproducttag(){
        $PostData = $this->input->post();

        if(isset($PostData["term"])){
            $Producttagdata = $this->Product_tag->searchproducttag(1,$PostData["term"]);
        }else if(isset($PostData["ids"])){
            $Producttagdata = $this->Product_tag->searchproducttag(0,$PostData["ids"]);
        }
        
        echo json_encode($Producttagdata);
    }
    public function check_product_tag_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT pt.id FROM ".tbl_producttag." as pt WHERE 
                    FIND_IN_SET(pt.id,(SELECT GROUP_CONCAT(tagid) FROM ".tbl_producttagmapping." WHERE FIND_IN_SET($row,tagid)>0))>0
                    ");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
    public function delete_mul_product_tag(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT pt.id FROM ".tbl_producttag." as pt WHERE 
                    FIND_IN_SET(pt.id,(SELECT GROUP_CONCAT(tagid) FROM ".tbl_producttagmapping." WHERE FIND_IN_SET($row,tagid)>0))>0
                    ");

            if($query->num_rows() == 0){
                if($this->viewData['submenuvisibility']['managelog'] == 1){

                    $this->Product_tag->_where = array("id"=>$row);
                    $tagdata = $this->Product_tag->getRecordsById();
                
                    $this->general_model->addActionLog(3,'Product Tag','Delete '.$tagdata['tag'].' product tag.');
                }
                $this->Product_tag->Delete(array('id'=> $row));
            }
            
        }
    }
}

?>