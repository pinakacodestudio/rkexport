<?php

use Monolog\Handler\IFTTTHandler;

defined('BASEPATH') OR exit('No direct script access allowed');

class Target extends Admin_Controller {
    public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Target');
		$this->load->model('Target_model','Target');
		$this->load->model('User_model', 'User');
    	$this->load->model('Zone_model', 'Zone');
    	$this->load->model('Product_model', 'Product');
    }
    public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Target";
		$this->viewData['module'] = "target/Target";
		$this->admin_headerlib->add_javascript("target","pages/target.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
    }
    public function listing() 
	{
		$list = $this->Target->get_datatables();
    	$data = array();
    	$counter = $_POST['start'];
    	foreach ($list as $Target) {
      		$row = array();

      		$row[] = ++$counter;
      		$targettype="";
      		if($Target->reference==1)
      			{ $targettype="<span class='label label-success badge-pill'>E</span>"; }
      		elseif($Target->reference==2)
      			{ $targettype="<span class='label label-primary badge-pill'>Z</span>"; }
      		elseif($Target->reference==3)
      			{ $targettype="<span class='label label-default badge-pill'>P</span>"; }
      		$row[] = $targettype." ".$Target->typename;
      		$row[] = $Target->revenue;
      		$row[] = $Target->orders;
      		$row[] = $Target->leads;
			  $row[] = $Target->meetings;
			  
      		$duration="";
      		if($Target->startdate!="0000-00-00"){
				$duration = $this->general_model->displaydate($Target->startdate)." to ".$this->general_model->displaydate($Target->enddate);
			}
			if($Target->duration==1){$duration="Yearly";}
       		if($Target->duration==2){$duration="Monthly";}
       		if($Target->duration==3){$duration="Quaterly";}  
			$row[] = $duration;
      
      		$Action='';

        	if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
              	$Action .='<a class="'.edit_class.'" href="'.ADMIN_URL.'target/target-edit/'.$Target->id.'" title='.edit_title.'>'.edit_text.'</a>';
          	}
			
			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            if($Target->status==1){
                $Action .='<span id="span'.$Target->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Target->id.',\''.ADMIN_URL.'target/target_enabledisable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
            }
            else{
                $Action .='<span id="span'.$Target->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Target->id.',\''.ADMIN_URL.'target/target_enabledisable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
            }
        }

      if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Target->id.',"'.ADMIN_URL.'target/check_target_use","target","'.ADMIN_URL.'target/delete_multarget") >'.delete_text.'</a>';
            }
      
      $row[] = $Action;

      $row[] = '<div class="checkbox table-checkbox">
                  <input id="deletecheck'.$Target->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Target->id.'" name="deletecheck'.$Target->id.'" class="checkradios">
                  <label for="deletecheck'.$Target->id.'"></label>
                </div>';

      $data[] = $row;
    }
    $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Target->count_all(),
            "recordsFiltered" => $this->Target->count_filtered(),
            "data" => $data,
        );
    echo json_encode($output);
	}
	
	public function target_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Target";
		$this->viewData['module'] = "target/Add_target";
		
		//$this->admin_headerlib->add_plugin("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.css");
    	$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_target","pages/add_target.js");

		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	
	public function add_target(){
		$PostData = $this->input->post();
		
		if(!isset($PostData['duration']))
    	{
      		$PostData['duration']="";
   	 	}
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$insertdata = array('reference'=>$PostData['type'],
                    'referenceid'=>$PostData['typeid'],
                    'revenue'=>$PostData['revenue'],
                    'orders'=>$PostData['orders'], 
                    'leads'=>$PostData['leads'],
                    'meetings'=>$PostData['meetings'],
                    'duration'=>$PostData['duration'],
                    "createddate"=>$createddate,
                    "addedby"=>$addedby,
                    "modifieddate"=>$createddate,
                    "modifiedby"=>$addedby,
					"status"=>$PostData['status']);
		
		$insertdata=array_map('trim',$insertdata);
		
		$targetid = $this->Target->Add($insertdata);

		if($targetid){

			$startdate= $enddate ="";
			if(isset($PostData['datecheckbox'])){
			  
			  if($PostData['startdate']!="" && $PostData['enddate']!=""){
				$startdate=$this->general_model->convertdate($PostData['startdate']);
				$enddate=$this->general_model->convertdate($PostData['enddate']);
			  }
			}
			$typeiddata = array('targetid'=>$targetid,
				  'startdate'=>$startdate,
				  'enddate'=>$enddate);
				  $this->Target->_table=tbl_targetduration;
				  $Add = $this->Target->Add($typeiddata);
				  if($targetid){
					  echo 1;
					}else{
					  echo 0;
					}
		  }
		  else
		  {
			echo 0;  
		  }
		
		
	}
	public function target_enabledisable() {
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Target->_where = array("id" => $PostData['id']);
        $this->Target->Edit($updatedata);
        echo $PostData['id'];
	}

	public function check_target_use()
    {
      $count = 0;
      $PostData = $this->input->post();

      $ids = explode(",",$PostData['ids']);
      $addedby = $this->session->userdata(base_url() . 'ADMINID');
      
      $count = 0;
      foreach($ids as $row){
        // $this->db->select('id');
        // $this->db->from(tbl_customer);
        // $where = "industryid = $row";
        // $this->db->where($where);
        // $query = $this->db->get();
        // if($query->num_rows() > 0){
        //   $count++;
        // }
      }
      echo $count;
    }

  public function delete_multarget(){
    $PostData = $this->input->post();
    $ids = explode(",",$PostData['ids']);

    $count = 0;
	$ADMINID = $this->session->userdata(base_url().'ADMINID');
	foreach($ids as $row){

	    $this->Target->Delete(array('id'=>$row));
		}
	}

	public function target_edit($id) {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Target";
		$this->viewData['module'] = "target/Add_target";
		$this->viewData['action'] = "1";
		
		$this->Target->_where = 'id='.$id;
    	$targetdata = $this->Target->getRecordsByID();

    	$this->Target->_table = tbl_targetduration;
    	$this->Target->_where = 'targetid='.$id;
    	$targetdurationdata = $this->Target->getRecordsByID();

    	$this->viewData['target_data']=array_merge($targetdata,$targetdurationdata);
		
		if($this->viewData['target_data'] == 0)
		{
			redirect(ADMINFOLDER.'Pagenotfound');
		}
		else
		{
			//$this->admin_headerlib->add_plugin("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.css");
    		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
			$this->admin_headerlib->add_javascript("add_target","pages/add_target.js");
			$this->load->view(ADMINFOLDER.'template',$this->viewData);
		}

	  }

	public function update_target(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
	
		if(!isset($PostData['duration']))
		{
		  $PostData['duration']="";
		}
		$updatedata = array('reference'=>$PostData['type'],
                    'referenceid'=>$PostData['typeid'],
                    'revenue'=>$PostData['revenue'],
                    'orders'=>$PostData['orders'], 
                    'leads'=>$PostData['leads'],
                    'meetings'=>$PostData['meetings'],
                    'duration'=>$PostData['duration'],
                    "modifieddate"=>$modifieddate,
                    "modifiedby"=>$modifiedby,
					"status"=>$PostData['status']);
	
		  $updatedata=array_map('trim',$updatedata);

		  $this->Target->_where = array("id"=>$PostData['targetid']);
		  $Edittarget = $this->Target->Edit($updatedata);

		   $startdate= $enddate ="";
      	  if(isset($PostData['datecheckbox'])){
        
         		if($PostData['startdate']!="" && $PostData['enddate']!=""){
          		$startdate=$this->general_model->convertdate($PostData['startdate']);
         		$enddate=$this->general_model->convertdate($PostData['enddate']);
        	}
      		}
			  $typeiddata = array('startdate'=>$startdate,
			  'enddate'=>$enddate);
				$this->Target->_table=tbl_targetduration;
				$this->Target->_where = array("targetid"=>$PostData['targetid']);
	 		$Edit = $this->Target->Edit($typeiddata);
      			if($Edit || $Edittarget){
				//if($Edittarget){
        			echo 1;
      			}else{
        		echo 0;
    			}
	

	  }
	
	
	public function getEmployee()
  	{
    	$EmployeeData = $this->User->getUserListData();
    	echo json_encode($EmployeeData);
  	}
	
	public function getZone()
  	{
    	$this->Zone->_fields = "id,zonename";
    	$ZoneData = $this->Zone->getRecordByID();
    	echo json_encode($ZoneData);
  	}
	
	public function getProduct()
  	{
		$this->Product->_fields = 'id,name,IFNULL((select filename from '.tbl_productimage.' where productid='.tbl_product.'.id limit 1),"'.PRODUCTDEFAULTIMAGE.'") as image';
		$this->Product->_order = "name ASC";
    	$ProductData = $this->Product->getRecordByID();
    	echo json_encode($ProductData);
  	}
	
}

	


