<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_method extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Payment_method');
		$this->load->model('Payment_method_model','Payment_method');
	}
	public function index() {

		$this->viewData['title'] = "Payment Method";
		$this->viewData['module'] = "payment_method/Payment_method";

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Payment method','View payment method.');
		}

		$this->admin_headerlib->add_javascript("payment_method","pages/payment_method.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {

		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Payment_method->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';
            //Edit Button
            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'payment-method/edit-payment-method/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }
            //Delete and Enable/Disable Button
            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Additional-rights","'.ADMIN_URL.'payment-method/delete-mul-payment-method") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }
            
            
        	$row[] = ++$counter;
            $row[] = $datarow->paymentmethod;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Payment_method->count_all(),
                        "recordsFiltered" => $this->Payment_method->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function add_payment_method() {
		
		$this->viewData['title'] = "Add Payment method";
		$this->viewData['module'] = "payment_method/Add_payment_method";

		$this->admin_headerlib->add_javascript("add_payment_method","pages/add_payment_method.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_payment_method($id) {
		
		$this->viewData['title'] = "Edit Payment method";
		$this->viewData['module'] = "payment_method/Add_payment_method";
		$this->viewData['action'] = "1";//Edit

		//Get Admission Inquiry Status Data By ID
		$this->viewData['paymentmethoddata'] = $this->Payment_method->getpaymentmethoddataByID($id);
		
		$this->admin_headerlib->add_javascript("add_payment_method","pages/add_payment_method.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function payment_method_add() {

		$PostData = $this->input->post();

		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		
		$paymentmethod = $PostData['paymentmethod'];

        $this->form_validation->set_rules('paymentmethod', 'Payment method', 'required');
		
		$json = array();

        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
         
                $this->Payment_method->_where = ("paymentmethod='".$paymentmethod."'");
                $Count = $this->Payment_method->CountRecords();
                
                if($Count==0){
                    
                    $insertdata = array("paymentmethod"=>$paymentmethod,
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby);
                    $insertdata=array_map('trim',$insertdata);
                    
                 $Add = $this->Payment_method->Add($insertdata);
                    if($Add){
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(1,'Payment method','Add new payment method.');
                        }
                        $json = 1; //Rights successfully added.
                    }else{
                        $json = 0; //Rights not added.
                    }
                }else{
                    $json = 2; //Rights already exist.
                }
           
			
		}
		echo json_encode($json);
	}
	public function update_payment_method() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$id = $PostData['id'];
		$paymentmethod = $PostData['paymentmethod'];

		$this->form_validation->set_rules('paymentmethod', 'Payment method', 'required');
        

		$json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
         
                $this->Payment_method->_where = ("id!=".$id." AND paymentmethod='".$paymentmethod."'");

                $Count = $this->Payment_method->CountRecords();
            
                if ($Count==0) {
                    $updatedata = array(
                        "paymentmethod"=>$paymentmethod,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby
                    );

                    $updatedata=array_map('trim', $updatedata);

                    $this->Payment_method->_where = array("id"=>$id);
                    $Edit = $this->Payment_method->Edit($updatedata);
                    if ($Edit) {
                        if($this->viewData['submenuvisibility']['managelog'] == 1){
                            $this->general_model->addActionLog(2,'Payment method','Edit '.$paymentmethod.' payment method.');
                        }
                        $json = 1; //Rights successfully updated.
                    } else {
                        $json = 0; //Rights not updated.
                    }
                } else {
                    $json = 2; //Rights already exist.
                }
          
		}
		echo json_encode($json);
	}
	public function delete_mul_payment_method(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Payment_method->_where = array("id"=>$row);
                $data = $this->Payment_method->getRecordsById();
            
                $this->general_model->addActionLog(3,'Payment method','Delete '.$data['name'].' payment method.');
            }
  			$this->Payment_method->Delete(array("id"=>$row));
		}
	}
}
?>