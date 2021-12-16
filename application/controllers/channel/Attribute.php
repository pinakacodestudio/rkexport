<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attribute extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Attribute');
		$this->load->model('Attribute_model','Attribute');
	}
	public function index() {
		// $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Attribute";
		$this->viewData['module'] = "attribute/Attribute";
		
		$this->channel_headerlib->add_javascript("attribute","pages/attribute.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
		
	}
	public function listing() {

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		
		$list = $this->Attribute->get_datatables($MEMBERID,$CHANNELID);
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Attribute) {
			$row = array();
			$Checkbox = '';
			$row[] = ++$counter;
			$row[] = $Attribute->variantname;
			
			$Action='';

			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false && $Attribute->addedby==$MEMBERID && $Attribute->usertype==1){
				$Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'attribute/attribute-edit/'.$Attribute->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false && $Attribute->addedby==$MEMBERID && $Attribute->usertype==1){                
					$Action.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Attribute->id.',"'.CHANNEL_URL.'attribute/check-attribute-use","Attribute","'.CHANNEL_URL.'attribute/delete-mul-attribute") >'.delete_text.'</a>';

					$Checkbox = '<div class="checkbox">
					<input id="deletecheck'.$Attribute->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Attribute->id.'" name="deletecheck'.$Attribute->id.'" class="checkradios">
					<label for="deletecheck'.$Attribute->id.'"></label>
				  </div>';
			}
			
			$row[] = $Action;
			$row[] = $Checkbox;
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Attribute->count_all(),
						"recordsFiltered" => $this->Attribute->count_filtered($MEMBERID,$CHANNELID),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function attribute_add() {
		// $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Attribute";
		$this->viewData['module'] = "attribute/Add_attribute";

		$this->channel_headerlib->add_javascript("add_attribute","pages/add_attribute.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);

	}
	public function attribute_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Attribute";
		$this->viewData['module'] = "attribute/Add_attribute";
		$this->viewData['action'] = "1";//Edit

		//Get Attribute data by id
		$this->viewData['attributedata'] = $this->Attribute->getAttributeDataByID($id);

		$this->channel_headerlib->add_javascript("add_attribute","pages/add_attribute.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);

	}
	public function add_attribute(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');

		$this->Attribute->_where = "variantname='".trim($PostData['name'])."' AND channelid=".$CHANNELID." AND memberid=".$addedby;
		$Count = $this->Attribute->CountRecords();

		if($Count==0){

			$insertdata = array("variantname"=>$PostData['name'],
								"priority"=>$PostData['priority'],
								"addedby"=>$addedby,
								"modifiedby"=>$addedby,
								"createddate"=>$createddate,
								"modifieddate"=>$createddate,
								"memberid"=>$addedby,
								"channelid"=>$CHANNELID,
								"usertype"=>1,
							
							
							);

			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Attribute->Add($insertdata);
			if($Add){
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
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');

		$this->Attribute->_where = "id!=".$PostData['attributeid']." AND variantname='".trim($PostData['name'])."' AND channelid=".$CHANNELID." AND memberid=".$modifiedby;
		$Count = $this->Attribute->CountRecords();

		if($Count==0){

			$updatedata = array("variantname"=>$PostData['name'],
								"priority"=>$PostData['priority'],
								"modifiedby"=>$modifiedby,
								"modifieddate"=>$modifieddate,
								"memberid"=>$modifiedby,
								"channelid"=>$CHANNELID,
							
							);

			$updatedata=array_map('trim',$updatedata);

			$this->Attribute->_where = array("id"=>$PostData['attributeid']);
			$Edit = $this->Attribute->Edit($updatedata);
			echo 1;
		}else{
			echo 2;
		}
	}

	public function check_attribute_use()
    {
       $PostData = $this->input->post();
         $count = 0;
	  	 $ids = explode(",",$PostData['ids']);
	     foreach($ids as $row){
	        $this->readdb->select('attributeid');
	        $this->readdb->from(tbl_variant);
	        $where = array("attributeid"=>$row);
	        $this->readdb->where($where);
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

	    $count = 0;
	    $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
	    foreach($ids as $row){
			$this->Attribute->Delete(array('id'=>$row));
	    }
	}

}