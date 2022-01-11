<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Variant extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Variant');
		$this->load->model('Variant_model','Variant');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Variant";
		$this->viewData['module'] = "variant/Variant";

		$this->load->model('Attribute_model','Attribute');
		$this->viewData['attributedata'] = $this->Attribute->getActiveAttribute();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Variant','View variant.');
		}
		$this->admin_headerlib->add_javascript("variant","pages/variant.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Variant->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Variant) {
			$row = array();
			
			$row['DT_RowId'] = $Variant->id;
			$row['row'] = ++$counter;
			$row['value'] = $Variant->value;
			$row['variantname'] = $Variant->variantname;
			
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'variant/variant-edit/'.$Variant->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Variant->id.',"'.ADMIN_URL.'variant/check-variant-use","variant","'.ADMIN_URL.'variant/delete-mul-variant","varianttable") >'.delete_text.'</a>';
            }
			$row['createddate'] = $this->general_model->displaydatetime($Variant->createddate);
			$row['action'] = $Action;
			$row['checkbox'] = '<span style="display: none;">'.$Variant->priority.'</span><div class="checkbox">
								<input id="deletecheck'.$Variant->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Variant->id.'" name="deletecheck'.$Variant->id.'" class="checkradios">
								<label for="deletecheck'.$Variant->id.'"></label>
								</div>';
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Variant->count_all(),
						"recordsFiltered" => $this->Variant->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function variant_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Variant";
		$this->viewData['module'] = "variant/Add_variant";

		$this->load->model('Attribute_model','Attribute');
		$this->viewData['attributedata'] = $this->Attribute->getActiveAttribute();

		$this->admin_headerlib->add_javascript("add_variant","pages/add_variant.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function variant_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Variant";
		$this->viewData['module'] = "variant/Add_variant";
		$this->viewData['action'] = "1";//Edit

		//Get Variant data by id
		$this->viewData['variantdata'] = $this->Variant->getVariantDataByID($id);
		$this->load->model('Attribute_model','Attribute');
		$this->viewData['attributedata'] = $this->Attribute->getActiveAttribute();

		$this->admin_headerlib->add_javascript("add_variant","pages/add_variant.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_variant(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');

		$this->Variant->_where = "value='".trim($PostData['name'])."' AND attributeid=".$PostData['attributeid'];
		$Count = $this->Variant->CountRecords();

		if($Count==0){

			$insertdata = array("attributeid"=>$PostData['attributeid'],
								"value"=>$PostData['name'],
								"priority"=>$PostData['priority'],
								"addedby"=>$addedby,
								"modifiedby"=>$addedby,
								"createddate"=>$createddate,
								"modifieddate"=>$createddate,
							
							);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Variant->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Variant','Add new '.$PostData['name'].' variant.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function update_variant(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->Variant->_where = "id!=".$PostData['variantid']." AND value='".trim($PostData['name'])."' AND attributeid=".$PostData['attributeid'];
		$Count = $this->Variant->CountRecords();

		if($Count==0){

			$updatedata = array("value"=>$PostData['name'],
								"attributeid"=>$PostData['attributeid'],
								"priority"=>$PostData['priority'],
								"modifiedby"=>$modifiedby,
								"modifieddate"=>$modifieddate,
							
							);

			$updatedata=array_map('trim',$updatedata);

			$this->Variant->_where = array("id"=>$PostData['variantid']);
			$Edit = $this->Variant->Edit($updatedata);

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(3,'Variant','Edit '.$PostData['name'].' variant.');
			}

			echo 1;
		}else{
			echo 2;
		}
	}

	public function check_variant_use()
    {
       $PostData = $this->input->post();
         $count = 0;
	  	 $ids = explode(",",$PostData['ids']);
	     foreach($ids as $row){
	        $this->readdb->select('variantid');
	        $this->readdb->from(tbl_productcombination);
	        $this->readdb->where(array("variantid"=>$row));
	        $query = $this->readdb->get();
	        if($query->num_rows() > 0){
	          $count++;
	        }
	      }
      	echo $count;
    }

    public function delete_mul_variant(){
	    $PostData = $this->input->post();
	    $ids = explode(",",$PostData['ids']);

	    foreach($ids as $row){
			$this->readdb->select('variantid');
	        $this->readdb->from(tbl_productcombination);
	        $this->readdb->where(array("variantid"=>$row));
	        $query = $this->readdb->get();
            if ($query->num_rows()==0) {

				$this->Variant->_where = array("id"=>$row);
				$Variantdata = $this->Variant->getRecordsById();

				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(3,'Variant','Delete '.$Variantdata['value'].' variant.');
				}
				$this->Variant->Delete(array('id'=>$row));
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
            $this->Variant->edit_batch($updatedata, 'id');
		}
		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(2,'Variant','Change variant priority.');
		}
        echo 1;
    }

}