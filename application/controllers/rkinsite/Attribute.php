<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attribute extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Attribute');
		$this->load->model('Attribute_model','Attribute');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Attribute";
		$this->viewData['module'] = "attribute/Attribute";
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Attribute','View attribute.');
		}
		
		$this->admin_headerlib->add_javascript("attribute","pages/attribute.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}

	public function listing() {

		
		
		$list = $this->Attribute->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Attribute) {
			$row = array();
			
			$row['DT_RowId'] = $Attribute->id;
			$row['row'] = ++$counter;
			$row['variantname'] = $Attribute->variantname;
			
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'attribute/attribute-edit/'.$Attribute->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Attribute->id.',"'.ADMIN_URL.'attribute/check-attribute-use","Attribute","'.ADMIN_URL.'attribute/delete-mul-attribute","attributetable") >'.delete_text.'</a>';
            }
			
			$row['action'] = $Action;
			$row['checkbox'] = '<span style="display: none;">'.$Attribute->priority.'</span><div class="checkbox">
						<input id="deletecheck'.$Attribute->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Attribute->id.'" name="deletecheck'.$Attribute->id.'" class="checkradios">
						<label for="deletecheck'.$Attribute->id.'"></label>
						</div>';
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Attribute->count_all(),
						"recordsFiltered" => $this->Attribute->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function attribute_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Attribute";
		$this->viewData['module'] = "attribute/Add_attribute";

		$this->admin_headerlib->add_javascript("add_attribute","pages/add_attribute.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}

	public function attribute_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Attribute";
		$this->viewData['module'] = "attribute/Add_attribute";
		$this->viewData['action'] = "1";//Edit

		//Get Attribute data by id
		$this->viewData['attributedata'] = $this->Attribute->getAttributeDataByID($id);

		$this->admin_headerlib->add_javascript("add_attribute","pages/add_attribute.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_attribute(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Attribute->_where = "variantname='".trim($PostData['name'])."'";
		$Count = $this->Attribute->CountRecords();

		if($Count==0){

			$insertdata = array("variantname"=>$PostData['name'],
								"priority"=>$PostData['priority'],
								"addedby"=>$addedby,
								"modifiedby"=>$addedby,
								"createddate"=>$createddate,
								"modifieddate"=>$createddate
							);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Attribute->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Attribute','Add new '.$PostData['name'].' attribute.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function update_attribute(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->Attribute->_where = "id!=".$PostData['attributeid']." AND variantname='".trim($PostData['name'])."'";
		$Count = $this->Attribute->CountRecords();

		if($Count==0){

			$updatedata = array("variantname"=>$PostData['name'],
								"priority"=>$PostData['priority'],
								"modifiedby"=>$modifiedby,
								"modifieddate"=>$modifieddate
							
							
							);

			$updatedata=array_map('trim',$updatedata);

			$this->Attribute->_where = array("id"=>$PostData['attributeid']);
			$Edit = $this->Attribute->Edit($updatedata);

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Attribute','Edit '.$PostData['name'].' attribute.');
			}

			echo 1;
		}else{
			echo 2;
		}
	}

	public function check_attribute_use(){
       $PostData = $this->input->post();
         $count = 0;
	  	 $ids = explode(",",$PostData['ids']);
	     foreach($ids as $row){
	        $this->readdb->select('attributeid');
	        $this->readdb->from(tbl_variant);
	        $this->readdb->where(array("attributeid"=>$row));
			$query = $this->readdb->get();
			
	        if($query->num_rows() > 0){
	          $count++;
	        }
	      }
      echo $count;
    }

    public function delete_mul_attribute(){
	    $PostData = $this->input->post();
	    $ids = explode(",",$PostData['ids']);

	    foreach($ids as $row){

			$this->readdb->select('attributeid');
	        $this->readdb->from(tbl_variant);
	        $this->readdb->where(array("attributeid"=>$row));
			$query = $this->readdb->get();
			
            if ($query->num_rows() == 0) {

				$this->Attribute->_where = array("id"=>$row);
				$Attributedata = $this->Attribute->getRecordsById();
				
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(3,'Attribute','Delete '.$Attributedata['variantname'].' attribute.');
				}
				$this->Attribute->Delete(array('id'=>$row));
            }
	    }
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
            $this->Attribute->edit_batch($updatedata, 'id');
		}
		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Attribute','Change attribute priority.');
		}
        echo 1;
    }
}