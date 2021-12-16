<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Our_client extends Admin_Controller {

	public $viewData = array();

	function __construct(){

		parent::__construct();
		
		$this->load->model('Our_client_model','Our_client');
		$this->viewData = $this->getAdminSettings('submenu','Our_client');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Our client";
		$this->viewData['module'] = "our_client/Our_client";
		$this->viewData['ourclientdata'] = $this->Our_client->getOurClientByMember();
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Our Client','View our client.');
        }
		$this->admin_headerlib->add_javascript("Our_client", "pages/our_client.js");
		$this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

	public function add_our_client(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);

		$this->viewData['title'] = "Add our client";
		$this->viewData['module'] = "our_client/Add_our_client";
		
		$this->admin_headerlib->add_javascript("Our_client","pages/add_our_client.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function our_client_add(){
		$PostData = $this->input->post();
        //print_r($_FILES);exit;

	  $this->Our_client->_where = ("name='" . trim($PostData['name']) . "'");
	  $websiteurl = isset($PostData['websiteurl']) ? trim($PostData['websiteurl']) : '';
	  $coverimage = isset($PostData['coverimage']) ? trim($PostData['coverimage']) : '';
	  $status = $PostData['status'];
        $Count = $this->Our_client->CountRecords();

        if ($Count == 0) {
	        $createddate = $this->general_model->getCurrentDateTime();
	        $addedby = $this->session->userdata(base_url() . 'ADMINID');
			
	        
	        if(!is_dir(OURCLIENT_COVER_IMAGE_PATH)){
	            @mkdir(OURCLIENT_COVER_IMAGE_PATH);
	        }

			$coverimage = "";
	        if($_FILES["coverimage"]['name'] != ''){

	            $coverimage = uploadFile('coverimage', 'OURCLIENT_COVER_IMAGE_PATH' ,OURCLIENT_COVER_IMAGE_PATH ,"*", '', 0, OURCLIENT_COVER_IMAGE_LOCAL_PATH);
	            if($coverimage !== 0){	
	                if ($coverimage == 2) {
	                    echo 3;//COVER IMAGE NOT UPLOADED
	                    exit;
	                }
	            }else{
	                echo 4;//COVER IMAGE TYPE NOT VALID
	                exit;
	            }
	        }

	        $insertdata = array("name" => $PostData['name'],
	            "websiteurl" => $PostData['websiteurl'],
	            "coverimage" => $coverimage,
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
	        	$this->writedb->set('priority',"(SELECT IFNULL(max(priority),0)+1 as priority FROM ".tbl_ourclient." as oc)",FALSE);
	        }
	       
	        $this->writedb->set($insertdata);
	        $this->writedb->insert(tbl_ourclient);
	        $Add = $this->writedb->insert_id();
	        if ($Add) {

				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Our Client','Add new '.$PostData['name'].' our client.');
				}
	            echo 1;
	        } else {
	            echo 0;
	        }
	    }else{
	    	echo 2;
	    }
	}
	public function edit_our_client($id){

		$this->viewData['title'] = "Edit our client menu";
		$this->viewData['module'] = "our_client/Add_our_client";
		$this->viewData['action'] = "1";//Edit

		$this->Our_client->_where = array('id' => $id);
		$this->viewData['ourclientdata'] = $this->Our_client->getRecordsByID();
		
		$this->admin_headerlib->add_javascript("Our_client","pages/add_our_client.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function update_our_client(){
		$PostData = $this->input->post();

		$OurclientID = $PostData['ourclientid'];
		$this->Our_client->_where = ("id!=" . $OurclientID . " AND name='" . trim($PostData['name']) . "'");
		$websiteurl = isset($PostData['websiteurl']) ? trim($PostData['websiteurl']) : '';
		$coverimage = isset($PostData['coverimage']) ? trim($PostData['coverimage']) : '';
		$status = $PostData['status'];
		$Count = $this->Our_client->CountRecords();

        if ($Count == 0) {
			$modifieddate = $this->general_model->getCurrentDateTime();
			$modifiedby = $this->session->userdata(base_url() . 'ADMINID');
			$oldcoverimage = trim($PostData['oldcoverimage']);
			$removeoldImage = trim($PostData['removeoldImage']);
			$displayonfront = (isset($PostData['displayonfront']))?1:0;
			$memberheader = (isset($PostData['memberheader']))?1:0;

			if(!is_dir(OURCLIENT_COVER_IMAGE_PATH)){
	            @mkdir(OURCLIENT_COVER_IMAGE_PATH);
			}
			
			if($_FILES["coverimage"]['name'] != '' && $oldcoverimage!=""){

    			$coverimage = reuploadfile('coverimage', 'OURCLIENT_COVER_IMAGE_PATH', $oldcoverimage ,OURCLIENT_COVER_IMAGE_PATH,"*", '', 0, OURCLIENT_COVER_IMAGE_LOCAL_PATH);
    			if($coverimage !== 0){
    				if($coverimage==2){
    					echo 3;//file not uploaded
                       	exit;
    				}
    			}else{
    				echo 4;//invalid image type
    				exit;
    			}	
            }else if($_FILES["coverimage"]['name'] != '' && $oldcoverimage==""){
            
                $coverimage = uploadFile('coverimage', 'OURCLIENT_COVER_IMAGE_PATH' ,OURCLIENT_COVER_IMAGE_PATH ,"*", '', 1, OURCLIENT_COVER_IMAGE_LOCAL_PATH);
                if($coverimage !== 0){	
                    if ($coverimage == 2) {
                        echo 3;//IMAGE NOT UPLOADED
                        exit;
                    }
                }else{
                    echo 4;//IMAGE TYPE NOT VALID
                    exit;
                }
			}
			else if($_FILES["coverimage"]['name'] == '' && ($oldcoverimage !='' && $removeoldImage=='1' || $oldcoverimage =='')){
    			unlinkfile('OURCLIENT_COVER_IMAGE_PATH', $oldcoverimage, OURCLIENT_COVER_IMAGE_PATH);
    			$coverimage = '';
    		}else{
                $coverimage = $oldcoverimage;
			}
			

	        $updatedata = array("name" => $PostData['name'],
	            "websiteurl" => $PostData['websiteurl'],
	            "priority" => $PostData['priority'],
	            "coverimage" => $coverimage,
	            "status" => $PostData['status'],
	            "modifieddate" => $modifieddate,
	            "modifiedby" => $modifiedby
	        );
	        
	        $updatedata = array_map('trim', $updatedata);
	        $this->Our_client->_where = "id=".$OurclientID;
	        $Edit = $this->Our_client->Edit($updatedata);
			
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Our Client','Edit '.$PostData['name'].' our client.');
			}
			echo 1;
	    }else{
	    	echo 2;
	    }
	}
	public function ourclientmenuenabledisable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Our_client->_where = array("id" => $PostData['id']);
        $this->Our_client->Edit($updatedata);

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Our_client->_where = array("id"=>$PostData['id']);
            $data = $this->Our_client->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['name'].' our client.';
            
            $this->general_model->addActionLog(2,'Our Client', $msg);
        }
		
		echo $PostData['id'];
    }
	public function update_priority(){
			$PostData = $this->input->post();
			//print_r($PostData);exit;
			$sequenceno = $PostData['sequencearray'];
			$updatedata = array();
	       // print_r($sequenceno = $PostData['sequencearray']);exit;
			for($i = 0; $i < count($sequenceno); $i++){
				$sequence = $sequenceno[$i]['sequenceno'];
				if($sequence==0){
	
					$query = $this->db->query("SELECT IFNULL(max(oc.priority),0)+1 as priority FROM ".tbl_ourclient." as oc WHERE oc.ourclientid = (SELECT oc2.ourclientid FROM ".tbl_ourclient." as oc2 WHERE oc2.id=".$sequenceno[$i]['id'].")");
					$sequence = $query->row_array();
					$sequence = $sequence['priority'];
				}
	
				$updatedata[] = array(
					'priority'=>$sequence,
					'id' => $sequenceno[$i]['id']
				);
			}
			if(!empty($updatedata)){
				$this->Our_client->edit_batch($updatedata, 'id');
			}
			
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Our Client','Change our client priority.');
			}
			echo 1;
	
    }
    public function check_our_client_menuuse(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }
    public function delete_mul_our_clientmenu(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->Our_client->_where = array("id"=>$row);
                $data = $this->Our_client->getRecordsById();

				$this->general_model->addActionLog(3,'Our Client','Delete '.$data['name'].' our client.');
			}
            $this->Our_client->Delete(array("id"=>$row));
        }
    }
}