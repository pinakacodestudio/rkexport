<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  Dealer extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Dealer_model', 'Dealer');
    
        $this->load->model('Side_navigation_model');
    }

    public function index() {
        $this->viewData = $this->getAdminSettings('submenu', 'Dealer');
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Dealer";
        $this->viewData['module'] = "dealer/dealer";
        $this->viewData['VIEW_STATUS'] = "1";

        $this->viewData['dealerdata'] = $this->Dealer->get_all_listdata('id','DESC');
        $this->admin_headerlib->add_javascript("Dealer", "pages/dealer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function adddealer() {
        $this->viewData = $this->getAdminSettings('submenu', 'Dealer');
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
              
        $this->viewData['title'] = "Add Dealer";
        $this->viewData['module'] = "dealer/adddealer";   
        $this->viewData['VIEW_STATUS'] = "1";            
        $this->admin_headerlib->add_javascript("Dealer", "pages/adddealer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function dealeradd() {
        $this->viewData = $this->getAdminSettings('submenu', 'Dealer');
        $this->checkAdminAccessModule('submenu', 'add', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post(); 
        
        $outletname = isset($PostData['outletname']) ? trim($PostData['outletname']) : '';
        $createddate  =  $this->general_model->getCurrentDateTime();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');       
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $address = $PostData['address'];
        $status = $PostData['status'];
        

        $latlong=$this->Dealer->getLatLong($address);
        if($latlong != ''){
            $lng=$latlong->results[0]->geometry->location->lng;
            $lat=$latlong->results[0]->geometry->location->lat;
        }else{
            $lng='';
            $lat='';
        }

        $this->Dealer->_where=array("outletname"=>$outletname);
        $sqlname = $this->Dealer->getRecordsByID();
        if(empty($sqlname)){          
            $InsertData = array(
                'outletname' => $outletname,
                'address' => $address,
                'city' => $PostData['city'],
                'mobile' => $PostData['mobile'],
                'email' => $PostData['email'],
                'latitude' => $lat,
                'longitude' => $lng,
                'createddate' => $createddate,                              
                'modifieddate' => $modifieddate, 
                'addedby'=>$addedby,
                'modifiedby'=>$modifiedby,
                'status' => $status);
            $insertid = $this->Dealer->add($InsertData);
            
            if($insertid != 0){
                echo 1;
            } else {
               echo 0; // Dealer not inserted 
            }
        } else {
            echo 2; // Dealer name already added
        }
    }

    public function updatedealer() {
       $this->viewData = $this->getAdminSettings('submenu', 'Dealer');
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $dealerid = isset($PostData['dealerid']) ?  trim($PostData['dealerid']) : '';
        $outletname = isset($PostData['outletname']) ? trim($PostData['outletname']) : '';
        $status = $PostData['status'];
        $modifiedby = $this->session->userdata(base_url().'ADMINUSERTYPE'); 
        $modifieddate = $this->general_model->getCurrentDateTime();

        $this->Dealer->_where = "outletname = '".$outletname."' AND id <> ".$dealerid;
        $sqlname = $this->Dealer->getRecordsByID();
        $address = $PostData['address'];
        $latlong=$this->Dealer->getLatLong($address);
        if($latlong != ''){
            $lng=$latlong->results[0]->geometry->location->lng;
            $lat=$latlong->results[0]->geometry->location->lat;
        }else{
            $lng='';
            $lat='';
        }

          if(empty($sqlname)){
                $updateData = array(
                    'outletname' => $outletname,
                    'address' => $address,
                    'city' => $PostData['city'],
                    'mobile' => $PostData['mobile'],
                    'email' => $PostData['email'],
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'modifieddate' => $modifieddate, 
                    'modifiedby'=>$modifiedby,
                    'status' => $status);
                                      
            $this->Dealer->_where = array('id' => $dealerid);
            $updateid = $this->Dealer->Edit($updateData);
            if($updateid != 0){
                echo 1; // Dealer update successfully
            } else {
                echo 0; // Dealer not updated
            }
         }else{
            echo 2;//Dealer already updated
         }
    }

    public function dealeredit($id) {
        $this->viewData = $this->getAdminSettings('submenu', 'Dealer');
        $this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Dealer";
        $this->viewData['module'] = "dealer/adddealer";
        $this->viewData['action'] ='1';   
        $this->viewData['VIEW_STATUS'] = "1";  
        $this->Dealer->_where=array("id"=>$id);     
        $this->viewData['dealerdata'] =  $this->Dealer->getRecordsByID(); 
        $this->admin_headerlib->add_javascript("Dealer", "pages/adddealer.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function deletemuldealer() {

        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;

        foreach ($ids as $row) {
            // get essay id
            $this->Dealer->_table = tbl_dealer;
            $this->db->select('id');
            $this->db->from($this->Dealer->_table);
            $this->db->where('id', $row);
            $query = $this->db->get();
            $dealerdata = $query->row_array();
            if(count($dealerdata)>0)
            {

                $this->Dealer->_table = tbl_dealer;
                $this->db->where('id', $row);
                $this->db->delete($this->Dealer->_table);          
            }
           
        }
    }

  public function dealerenabledisable() {
        $this->viewData = $this->getAdminSettings('submenu', 'Dealer');
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINUSERTYPE'));
        $this->Dealer->_table = tbl_dealer;
        $this->Dealer->_where = array("id" => $PostData['id']);
        $this->Dealer->Edit($updatedata);

        echo $PostData['id'];
    }
    
    
}