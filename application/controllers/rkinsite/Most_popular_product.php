<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Most_popular_product extends Admin_Controller {

	public $viewData = array();

    function __construct() {
		parent::__construct();
		
        $this->viewData = $this->getAdminSettings('submenu', 'Most_popular_product');
		$this->load->model('Most_popular_product_model', 'Most_popular_product');
		$this->load->model('Product_model','Product');
	}
     //changes show in model under
    public function getproduct() {
       
        $this->Product->_order = 'id DESC';
        return $this->Product->getRecordByID();
    }
   
    public function index() {
       $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Most Popular Product";
        $this->viewData['module'] = "most_popular_product/Most_popular_product";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['productdata'] = $this->Most_popular_product->getproduct(); 
       $this->viewData['Most_popular_productdata'] = $this->Most_popular_product->get_all_listdata('priority','asc');
       $createddate = $this->general_model->getCurrentDateTime();
        $this->admin_headerlib->add_bottom_javascripts("most_popular_product", "pages/most_popular_product.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
 
	
	public function add_most_popular_product() {

        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        //$this->viewData = $this->getAdminSettings('submenu', 'Most_popular_product');
        $this->viewData['title'] = "Add Most Popular Product";
        $this->viewData['module'] = "most_popular_product/Add_most_popular_product";   
        $this->viewData['VIEW_STATUS'] = "0";
		$this->viewData['productdata'] = $this->Most_popular_product->getproduct(); 
		//print_r($this->viewData['Product'] );   exit;
        $this->admin_headerlib->add_javascript("Most_popular_product", "pages/add_most_popular_prodcut.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function most_popular_product_add() {
      
        $PostData = $this->input->post();
        
       $productid = isset($PostData['productid']) ? trim($PostData['productid']) : '0';
       $priority = isset($PostData['priority']) ? trim($PostData['priority']) : '';
       $status = $PostData['status'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
                $insertdata = array(
                    "productid" => $PostData['productid'],
                    "priority" => $PostData['priority'],
                    "status" => $PostData['status'],
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                
				
                $Add = $this->Most_popular_product->Add($insertdata);
                if ($Add) {
                    echo 1;
                } else {
                    echo 0;
                }         
                      
    }
   
    


    public function edit_most_popualr_product($mostid) {
       
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Most Popular Product";
        
        $this->viewData['module'] = "most_popular_product/Add_most_popular_product";
        $this->viewData['action'] = "1"; //Edit
       
        $this->Most_popular_product->_where = array('id' => $mostid);
        $this->viewData['productdata'] = $this->Most_popular_product->getproduct();
        
        $this->viewData['Most_popular_productdata'] = $this->Most_popular_product->getRecordsByID();
        $this->admin_headerlib->add_javascript("Most_popular_product", "pages/add_most_popular_prodcut.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function update_most_popular_product() {
      
        $PostData = $this->input->post();
        $mostID = $PostData['mostid'];
        $productid = isset($PostData['productid']) ? trim($PostData['productid']) : '0';
        $priority = isset($PostData['priority']) ? trim($PostData['priority']) : '';
        $status = $PostData['status'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
                
                $updatedata = array(
                    "productid" => $PostData['productid'],
                    "priority" => $PostData['priority'],
                    "status" => $PostData['status'],
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );
                $updatedata = array_map('trim', $updatedata);
                $this->Most_popular_product->_where = array('id' => $mostID);
                $this->Most_popular_product->Edit($updatedata);
            echo 1;
            /*}else{
                echo 3;
            }

            } else {
            echo 2;
            }*/
    }
    
    public function delete_mul_most_popular_product() {

        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $this->db->where('id', $row);
            $this->db->delete(tbl_mostpopularproduct);
        }
    }

    public function check_category_use() {
         $PostData = $this->input->post();
         $count = 0;
         $ids = explode(",",$PostData['ids']);
         foreach($ids as $row){
            $this->readdb->select('categoryid');
            $this->readdb->from(tbl_product);
            $where = array("categoryid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
          }
        echo $count;
    }

    public function most_popular_product_enabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Most_popular_product->_where = array("id" => $PostData['id']);
        $this->Most_popular_product->Edit($updatedata);

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
            $this->Most_popular_product->edit_batch($updatedata, 'id');
        }
        
        echo 1;
    }
    
   
    

    
}