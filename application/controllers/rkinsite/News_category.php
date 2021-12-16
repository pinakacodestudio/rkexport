<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class News_category extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'News_category');
        $this->load->model('News_category_model', 'News_category');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "News Category";
        $this->viewData['module'] = "news_category/News_category";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'News Category','View news category.');
        }

        $this->admin_headerlib->add_javascript("News_category", "pages/news_category.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->News_category->get_datatables();
        $data = array();
        $counter = $srno = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        $internalErrors = libxml_use_internal_errors(true);

        foreach ($list as $category) {
            $row = array();
            $Action = $checkbox = '';
            
            $row['DT_RowId'] = $category->id;
            $row[] = ++$counter;            
            $row[] = ucwords($category->name);
            $row[] = $category->slug;
            $row[] = $createddate;
            

            $Action .= '<a href="'.ADMIN_URL.'news-category/news-category-edit/'.$category->id.'" class="'.edit_class.'" title="'.edit_title.'">'.edit_text.'</a>';
            
            if(in_array($rollid, $delete)) {
               
                $Action.= '<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick="deleterow('.$category->id.',&quot;&quot;,&quot;News&nbsp;category&quot;,&quot;'.ADMIN_URL.'news-category/delete-mul-news-category&quot;)">'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$category->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$category->id.'" name="deletecheck'.$category->id.'" class="checkradios">
                <label for="deletecheck'.$category->id.'"></label></div>';
            } 
            
               
            if(in_array($rollid, $edit)) {
                if($category->status==1){ 
                    $Action .= '<span id="span'.$category->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$category->id.',&quot;'.ADMIN_URL.'news-category/news-category-enable-disable&quot;,&quot;'.disable_title.'&quot;,&quot;'.disable_class.'&quot;,&quot;'.enable_class.'&quot;,&quot;'.disable_title.'&quot;,&quot;'.enable_title.'&quot;,&quot;'.disable_text.'&quot;,&quot;'.enable_text.'&quot;)" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }else{
                    $Action .= '<span id="span'.$category->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$category->id.',&quot;'.ADMIN_URL.'news-category/news-category-enable-disable&quot;,&quot;'.enable_title.'&quot;,&quot;'.disable_class.'&quot;,&quot;'.enable_class.'&quot;,&quot;'.disable_title.'&quot;,&quot;'.enable_title.'&quot;,&quot;'.disable_text.'&quot;,&quot;'.enable_text.'&quot;)" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                } 
            }
                 
            $row[] = $Action;
            $row[] = $checkbox;
            
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->News_category->count_all(),
                        "recordsFiltered" => $this->News_category->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);        
    }
    public function news_category_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add News Category";
        $this->viewData['module'] = "news_category/Add_news_category";
        
        $this->admin_headerlib->add_javascript("news_category", "pages/add_news_category.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_news_category() {
        $PostData = $this->input->post();
       
        $createddate = $this->general_model->getCurrentDateTime();        
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $slug = isset($PostData['slug']) ? trim($PostData['slug']) : '';
        $status = $PostData['status'];
      
        $this->News_category->_where = ("name='".$name."'");
        $Count = $this->News_category->CountRecords();
        
        if($Count==0){
            $this->News_category->_where = array();
            $this->News_category->_fields = "IFNULL(max(priority)+1,1) as maxpriority";
            $categorydata = $this->News_category->getRecordsById();
            
            $maxpriority = (!empty($categorydata))?$categorydata['maxpriority']:1;

            $insertdata = array(
                "name" => $name,
                "slug" => $slug,
                "status" => $status,
                "priority" => $maxpriority,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );
            
            $insertdata = array_map('trim', $insertdata);
            $NewsCategoryID = $this->News_category->Add($insertdata);
    
            if ($NewsCategoryID) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'News Category','Add new '.$name.' news category.');
                }
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function news_category_edit($id) {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit News Category";
        $this->viewData['module'] = "news_category/Add_news_category";
        $this->viewData['action'] = "1"; //Edit

        $this->viewData['newscategorydata'] = $this->News_category->getNewsCategoryDataByID($id);

        $this->admin_headerlib->add_javascript("news_category", "pages/add_news_category.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_news_category() {
        
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');
        
        $newscategoryid = trim($PostData['newscategoryid']);
        $name = isset($PostData['name']) ? trim($PostData['name']) : '';
        $slug = isset($PostData['slug']) ? trim($PostData['slug']) : '';
        $status = $PostData['status'];
        
        $this->News_category->_where = ("id<>'".$newscategoryid."' AND name='".$name."'");
        $Count = $this->News_category->CountRecords();
        
        if($Count==0){
            $updatedata = array(
                "name" => $name,
                "slug" => $slug,
                "status" => $status,
                "modifieddate" => $modifieddate,
                "modifiedby" => $modifiedby
            );
            
            $updatedata = array_map('trim', $updatedata);
            $this->News_category->_where = array('id' => $newscategoryid);
            $Edit = $this->News_category->Edit($updatedata);
            if($Edit){
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'News Category','Edit '.$name.' news category.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    public function searchnewscategory()
    {
        if(isset($_REQUEST["name"]) && trim($_REQUEST['name'])!==''){
            $Newsdata = $this->News_category->searchnewscategory(1,$_REQUEST["name"]);
        }else if(isset($_REQUEST["ids"]) && trim($_REQUEST['ids'])!==''){
            $Newsdata = $this->News_category->searchnewscategory(0,$_REQUEST["ids"]);
        }
        
        echo json_encode($Newsdata);
    }
    public function news_category_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->News_category->_where = array("id" => $PostData['id']);
        $this->News_category->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->News_category->_where = array("id"=>$PostData['id']);
            $data = $this->News_category->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' news category.';
            
            $this->general_model->addActionLog(2,'News Category', $msg);
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
        // print_r($updatedata); exit;
        if(!empty($updatedata)){
            $this->News_category->edit_batch($updatedata, 'id');
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'News Category','Change news category priority.');
		}
        echo 1;
    }
    public function check_news_category_use(){
        $PostData = $this->input->post();
        $count = 0;
        $ids = explode(",",$PostData['ids']);
        foreach($ids as $category){
           
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
    public function delete_mul_news_category(){
       
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->News_category->_fields = "name";
                $this->News_category->_where = array("id"=>$row);
                $data = $this->News_category->getRecordsByID();
                
                $this->general_model->addActionLog(3,'News Category','Delete '.$data['name'].' news category.');
            }

            $this->News_category->Delete(array("id"=>$row));
        }
    }
}
?>

