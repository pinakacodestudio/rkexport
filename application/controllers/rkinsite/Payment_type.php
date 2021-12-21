<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_type extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Payment_type');
		$this->load->model('Payment_type_model','Payment_type');
	}
	public function index() {

		$this->viewData['title'] = "Payment Type";
		$this->viewData['module'] = "payment_type/Payment_type";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Payment Type','View payment type.');
		}

		$this->admin_headerlib->add_javascript("payment_type","pages/paymenttype.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {

		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Payment_type->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'payment-type/edit-payment-type/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            //Delete and Enable/Disable Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Additional-rights","'.ADMIN_URL.'payment_type/delete_mul_payment_type") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
            
            
        	$row[] = ++$counter;
            $row[] = $datarow->payment_type;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Payment_type->count_all(),
                        "recordsFiltered" => $this->Payment_type->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function add_payment_type() {
		
		$this->viewData['title'] = "Add Additional Rights";
		$this->viewData['module'] = "payment_type/Add_payment_type";

		$this->admin_headerlib->add_javascript("add_payment_type","pages/add_payment_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_payment_type($id) {
		
		$this->viewData['title'] = "Edit Additional Rights";
		$this->viewData['module'] = "payment_type/Add_payment_type";
		$this->viewData['action'] = "1";//Edit

		//Get Admission Inquiry Status Data By ID
		$this->viewData['additionalrightsrow'] = $this->Payment_type->getAdditionalrightsDataByID($id);
		
		$this->admin_headerlib->add_javascript("add_payment_type","pages/add_payment_type.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function payment_type_add() {

		$PostData = $this->input->post();

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		
		$payment_type = $PostData['payment_type'];

        $this->form_validation->set_rules('payment_type', 'Payment Type', 'required');
		
		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
         
                $this->Payment_type->_where = ("payment_type='".$payment_type."'");
                $Count = $this->Payment_type->CountRecords();
                
                if($Count==0){
                    
                    $insertdata = array("payment_type"=>$payment_type,
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby);
                    $insertdata=array_map('trim',$insertdata);
                    
                    $Add = $this->Payment_type->Add($insertdata);
                    if($Add){
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(1,'Additional Rights','Add new additional rights.');
                        }
                        $json = array('error'=>1); //Rights successfully added.
                    }else{
                        $json = array('error'=>0); //Rights not added.
                    }
                }else{
                    $json = array('error'=>2); //Rights already exist.
                }
           
			
		}
		echo json_encode($json);
	}
	public function update_payment_type() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$id = $PostData['id'];
		$payment_type = $PostData['payment_type'];

		$this->form_validation->set_rules('payment_type', 'Payment Type', 'required');
        

		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
         
                $this->Payment_type->_where = ("id!=".$id." AND payment_type='".$payment_type."'");

                $Count = $this->Payment_type->CountRecords();
            
                if ($Count==0) {
                    $updatedata = array(
                        "payment_type"=>$payment_type,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby
                    );

                    $updatedata=array_map('trim', $updatedata);

                    $this->Payment_type->_where = array("id"=>$id);
                    $Edit = $this->Payment_type->Edit($updatedata);
                    if ($Edit) {
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(2,'Payment Type','Edit '.$payment_type.' payment type.');
                        }
                        $json = array('error'=>1); //Rights successfully updated.
                    } else {
                        $json = array('error'=>0); //Rights not updated.
                    }
                } else {
                    $json = array('error'=>2); //Rights already exist.
                }
          
		}
		echo json_encode($json);
	}
	public function delete_mul_payment_type(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Payment_type->_where = array("id"=>$row);
                $data = $this->Payment_type->getRecordsById();
            
                $this->general_model->addActionLog(3,'Additional Rights','Delete '.$data['name'].' additional rights.');
            }
  			$this->Payment_type->Delete(array("id"=>$row));
		}
	}
}
?>