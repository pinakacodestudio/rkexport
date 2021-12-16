<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estimate extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Estimate');
        $this->load->model('Estimate_model', 'Estimate');
        $this->load->model('Process_group_model', 'Process_group');
        $this->load->model('Process_model', 'Process');
    }

    public function index() {
        $this->viewData['title'] = "Bill of Material";
        $this->viewData['module'] = "product_process/Estimate";
        $this->viewData['VIEW_STATUS'] = "1";
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['mainproductdata'] = $this->Product->getActiveRegularOrRawProducts(3);
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2,0,0,'withvariant');

        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Bill of Material','View bill of material.');
        }

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("product_process", "pages/estimate.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() { 
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        
        $list = $this->Estimate->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $actions = $checkbox = '';
            
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Bill&nbsp;of&nbsp;Material","'.ADMIN_URL.'estimate/delete-mul-estimate") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }

            if($datarow->filename!="" && file_exists(ESTIMATE_PATH.$datarow->filename)){
                $actions.='<a class="'.download_class.'" href="'.ESTIMATE.$datarow->filename.'" title="'.download_title.'" download>'.download_text.'</a>';

                $filename = '<a href="'.ESTIMATE.$datarow->filename.'" title="View Estimate" target="_blank">'.$datarow->filename.'</a>';
            }else{
                $filename = $datarow->filename;
            }
            
           
            $row[] = ++$counter;
            $row[] = $datarow->estimatename;
            $row[] = $filename;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);  
            $row[] = $datarow->createdby;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Estimate->count_all(),
                        "recordsFiltered" => $this->Estimate->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    public function create_estimate() {
        $this->viewData['title'] = "Create Bill of Material";
        $this->viewData['module'] = "product_process/Create_estimate";
        $this->viewData['VIEW_STATUS'] = "1";
        
        $this->load->model('Product_model', 'Product');
        $this->viewData['mainproductdata'] = $this->Product->getActiveRegularOrRawProducts(3);
        $this->viewData['productdata'] = $this->Product->getActiveRegularOrRawProducts(2,0,0,'withvariant');

        $this->load->model('Product_unit_model', 'Product_unit');
        $this->viewData['unitdata'] = $this->Product_unit->getActiveProductUnit();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Bill of Material','View bill of material.');
        }

        $this->admin_headerlib->add_plugin("jquery.bootstrap-touchspin.min", "bootstrap-touchspin/jquery.bootstrap-touchspin.min.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.bootstrap-touchspin", "bootstrap-touchspin/jquery.bootstrap-touchspin.js");
        $this->admin_headerlib->add_javascript("product_process", "pages/estimate.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function getProcessGroupByProduct(){
        $PostData = $this->input->post();
        
        $processgroupdata = $this->Process_group->getProcessGroupByProduct($PostData['priceid']);
        echo json_encode($processgroupdata);
    }

    function exporttopdfestimate() {
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Bill of Material','Export to PDF bill of material.');
        }
        
        $productdata = json_decode($_REQUEST['content'], true);

        $filename = "Bill of Material.pdf";
        $this->Estimate->create_pdf($productdata, $filename,"D");
    }

    function printestimate() {
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Bill of Material','Print bill of material.');
        }
        $PostData = $this->input->post();

        $html['content'] = $this->load->view(ADMINFOLDER."product_process/Estimateformat",$PostData,true);
        echo json_encode($html); 
    }

    function save_estimate(){
        $PostData = $this->input->post();
        $estimatename = $PostData['estimatename'];
        $productdata = json_decode($PostData['content'], true);
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        // print_r($PostData); exit;
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Bill of Material','Save '.$estimatename.' bill of material.');
        }
        if(!is_dir(ESTIMATE_PATH)){
            @mkdir(ESTIMATE_PATH);
        }
        $this->Estimate->_where = ("estimatename='" .$estimatename. "'");
        $Count = $this->Estimate->CountRecords();

        if ($Count == 0) {
        
            $filename = "Estimate-".str_replace(" ","-",$estimatename).".pdf";
            $this->Estimate->create_pdf($productdata, $filename,"F");

            $insertdata = array(
                "estimatename" => $estimatename,
                "filename" => $filename,
                "createddate" => $createddate,
                "addedby" => $addedby
            );
            $insertdata = array_map('trim', $insertdata);
            $Add = $this->Estimate->Add($insertdata);
            if ($Add) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Bill of Material','Save new '.$estimatename.' bill of material.');
                }
                echo 1; 
            } else {
                echo 0;
            }
        } else {
            echo 2;
        }
    }

    public function delete_mul_estimate() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            $this->Estimate->_fields = 'id,estimatename,filename';
            $this->Estimate->_where = array('id'=>$row);
            $estimatedata = $this->Estimate->getRecordsByID();

            if(!empty($estimatedata)){
                unlinkfile('ESTIMATE', $estimatedata['filename'], ESTIMATE_PATH);
                // Delete from essay data table

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(3,'Bill of Material','Delete '.$estimatedata['estimatename'].' bill of material.');
                }
                $this->Estimate->Delete(array('id'=>$row));
            }
        }
    }
    
}?>