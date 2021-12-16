<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Insurance extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Insurance');
		$this->load->model('Insurance_model','Insurance');
	}
	public function index() {

		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Insurance";
		$this->viewData['module'] = "insurance/Insurance";
		
		$this->viewData['insurancecompanydata'] = $this->Insurance->getInsuranceCompanyList();

		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Insurance','View insurance.');
		}
		$this->admin_headerlib->add_javascript("insurance","pages/insurance.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Insurance->get_datatables();

        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';

            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'insurance/edit-insurance/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'insurance/check-insurance-use","Insurance","'.ADMIN_URL.'insurance/delete-mul-insurance") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
			}
			
			if($datarow->proof!="" && file_exists(INSURANCE_PATH.$datarow->proof)){
				$actions .= '<a class="'.download_class.'" href="'.INSURANCE.$datarow->proof.'" title="'.download_title.'" download>'.download_text.'</a>';
				$actions .= '<a class="'.viewdoc_class.'" href="'.INSURANCE.$datarow->proof.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
			}

			$row[] = ++$counter;
			$row[] = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#insurancetab" target="_blank">'.$datarow->vehiclename.' ('.$datarow->vehicleno.')</a>';
            $row[] = $datarow->companyname;
            $row[] = $datarow->policyno;           
            $row[] = ($datarow->fromdate!="0000-00-00")?$this->general_model->displaydate($datarow->fromdate):"-";    
            $row[] = ($datarow->todate!="0000-00-00")?$this->general_model->displaydate($datarow->todate):"-";    
            $row[] = numberFormat($datarow->amount,2,',');
			$row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Insurance->count_all(),
                        "recordsFiltered" => $this->Insurance->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function add_insurance() {

		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Insurance";
		$this->viewData['module'] = "insurance/Add_insurance";
		
		//GET VEHICLE DATA
		$this->load->model('Vehicle_model','Vehicle');
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
		
		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_insurance","pages/add_insurance.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_insurance($insuranceid) {
		
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Insurance";
		$this->viewData['module'] = "insurance/Add_insurance";
		$this->viewData['action'] = "1";//Edit
		
		//GET VEHICLE DATA
		$this->load->model('Vehicle_model','Vehicle');
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicle();
		
		//GET INSURANCE DATA BY ID
		$this->viewData['insurancedata'] = $this->Insurance->getvehicleinsuranceDataByID($insuranceid);
		if(empty($this->viewData['insurancedata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}

		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_insurance","pages/add_insurance.js");	
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function insurance_add() {

		$PostData = $this->input->post();
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		
		$vehicleid = $PostData['vehicleid'];
		$companyname = $PostData['companyname'];
		$policyno = $PostData['policyno'];
		$fromdate = $this->general_model->convertdate($PostData['fromdate']);
		$todate = $this->general_model->convertdate($PostData['todate']);
		$paymentdate = $this->general_model->convertdate($PostData['paymentdate']);
		$amount = $PostData['amount'];

		$file = "";
		if(!empty($_FILES['fileproof']['name'])){
			if(!is_dir(INSURANCE_PATH)){
				@mkdir(INSURANCE_PATH);
			}
			if($_FILES['fileproof']['size'] != '' && $_FILES['fileproof']['size'] >= UPLOAD_MAX_FILE_SIZE){
				$json = array('error'=>-1);
				echo json_encode($json);
				exit;
			}
			
			$file = uploadFile('fileproof', 'INSURANCE', INSURANCE_PATH, '*','',1,INSURANCE_LOCAL_PATH);
			if($file !== 0){
				if($file==2){
					$json = array('error'=>4);//file not uploaded
					exit;
				}
			}else{
				$json = array('error'=>3);//INVALID FILE TYPE
				exit;
			}	
		}

		$insertdata = array("vehicleid"=>$vehicleid,
							"companyname"=>$companyname,
							"policyno"=>$policyno,
							"fromdate"=> $fromdate,
							"todate"=> $todate,
							"paymentdate"=> $paymentdate,
							"amount"=> $amount,
							"proof"=> $file,
							"createddate"=>$createddate,
							"addedby"=>$addedby,
							"modifieddate"=>$createddate,
							"modifiedby"=>$addedby
						);

		$insertdata=array_map('trim',$insertdata);
		$InsuranceId = $this->Insurance->Add($insertdata);
		if($InsuranceId){
			$json = array('error'=>1);
		}else{
			$json = array('error'=>0);
		}
		echo json_encode($json);
	}
	public function update_insurance() {

		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		
		$insuranceid = $PostData['insuranceid'];
		$vehicleid = $PostData['vehicleid'];
		$companyname = $PostData['companyname'];
		$policyno = $PostData['policyno'];
		$fromdate = $this->general_model->convertdate($PostData['fromdate']);
		$todate = $this->general_model->convertdate($PostData['todate']);
		$paymentdate = $this->general_model->convertdate($PostData['paymentdate']);
		$amount = $PostData['amount'];

		$file = "";
		$oldfile = $PostData['oldproof'];
		if($_FILES['fileproof']['name']!="" && $oldfile==""){
			if($_FILES['fileproof']['size'] != '' && $_FILES['fileproof']['size'] >= UPLOAD_MAX_FILE_SIZE){
				$json = array('error'=>-1);
				echo json_encode($json);
				exit;
			}
			$file = uploadfile('fileproof', 'INSURANCE', INSURANCE_PATH, '*', '', 1, INSURANCE_LOCAL_PATH);
			if($file !== 0){	
				if($file==2){
					$json = array('error'=>4);
				}
			}else{
				$json = array('error'=>3);
			}
		}else if(($_FILES['fileproof']['name']=="" || $_FILES['fileproof']['name']!="") && $oldfile!=""){
			$file = $PostData['oldproof'];
			if($_FILES['fileproof']['name']!=""){
				$file = reuploadfile('fileproof', 'INSURANCE', $file, INSURANCE_PATH, '*', '', 1, INSURANCE_LOCAL_PATH);
				if($file !== 0){	
					if($file==2){
						$json = array('error'=>4);
					}
				}else{
					$json = array('error'=>3);
				}
			}
		}

		$updatedata = array("vehicleid"=>$vehicleid,
							"companyname"=>$companyname,
							"policyno"=>$policyno,
							"fromdate"=> $fromdate,
							"todate"=> $todate,
							"paymentdate"=> $paymentdate,
							"amount"=> $amount,
							"proof"=> $file,
							"modifieddate"=>$modifieddate,
							"modifiedby"=>$modifiedby
						);

		$this->Insurance->_where = array("id"=>$insuranceid);
		$Edit = $this->Insurance->Edit($updatedata);
		if($Edit){
			$json = array('error'=>1);
		}else{
			$json = array('error'=>0);
		}
		echo json_encode($json);
	}
	public function insurance_enable_disable() {

		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Insurance->_where = array("id"=>$PostData['id']);
		$this->Insurance->Edit($updatedata);
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Insurance->_where = array("id"=>$PostData['id']);
            $data = $this->Insurance->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['companyname'].' '.$data['policyno'].' insurance.';
            
            $this->general_model->addActionLog(2,'Insurance', $msg);
        }
		echo $PostData['id'];
	}
	public function check_insurance_use(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
            $query = $this->db->query("SELECT id FROM ".tbl_insurance." WHERE 
                    id IN (SELECT insuranceid FROM ".tbl_insuranceclaim." WHERE insuranceid = $row)");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
		echo $count;
	}
	public function delete_mul_insurance(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		foreach($ids as $row){
			$count = 0;
			$query = $this->db->query("SELECT id FROM ".tbl_insurance." WHERE 
					id IN (SELECT insuranceid FROM ".tbl_insuranceclaim." WHERE insuranceid = $row)");
			
			if($query->num_rows() > 0){
				$count++;
			}
			if($count == 0){
				$this->Insurance->_where = array("id"=>$row);
				$data = $this->Insurance->getRecordsByID();
				if(!empty($data) && $data['proof']!=""){
					unlinkfile("INSURANCE", $data['proof'], INSURANCE_PATH);
				}
				$this->Insurance->Delete(array('id'=>$row));
	
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(3,'Insurance', 'Delete '.$data['companyname'].' '.$data['policyno'].' insurance.');
				}
			}
		}
	}
	public function searchInsuranceCompany(){
        $PostData = $this->input->post();
 
		if(isset($PostData["term"])){
            $Companydata = $this->Insurance->searchInsuranceCompany(1,$PostData["term"]);
        }else if(isset($PostData["ids"])){
            $Companydata = $this->Insurance->searchInsuranceCompany(0,$PostData["ids"]);
        }
        
        echo json_encode($Companydata);
	}
	public function getInsuranceCompanyByVehicleId() {
		$PostData = $this->input->post();
		$vehicleid = $PostData['vehicleid'];

        $companydata = $this->Insurance->getInsuranceCompanyByVehicleId($vehicleid);
        echo json_encode($companydata);
	}
	public function getInsurancePolicyNumberByVehicleOrCompany() {
		$PostData = $this->input->post();
		$vehicleid = $PostData['vehicleid'];
		$insurancecompany = $PostData['insurancecompany'];

        $policydata = $this->Insurance->getInsurancePolicyNumberByVehicleOrCompany($vehicleid,$insurancecompany);
        echo json_encode($policydata);
	}
	public function exportToExcelInsurance(){
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Insurance','Export to excel Insurance.');
        }
        $exportdata = $this->Insurance->getInsuranceDataForExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) { 
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->companyname!=''?$row->companyname:'-'),
                            ($row->policyno!=''?$row->policyno:'-'),
                            ($row->fromdate!='0000-00-00'?$this->general_model->displaydate($row->fromdate):'-'),
                            ($row->todate!='0000-00-00'?$this->general_model->displaydate($row->todate):'-'),
                            ($row->amount>0?numberFormat($row->amount,2,','):'-'),
                            ($row->paymentdate!='0000-00-00'?$this->general_model->displaydate($row->paymentdate):'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-')
                        );
        }  
        $headings = array('Sr. No.','Vehicle Name','Company Name','Policy No.','Register Date','Due Date','Amount','Payment Date','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Insurance",$headings,"Insurance.xls",'G');

	}

	public function exportToPDFInsurance(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Insurance','Export to PDF Insurance.');
        }

        $PostData['reportdata'] = $this->Insurance->getInsuranceDataForExport();
		$this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Insurance';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'insurance/InsuranceforPDFFormate', $PostData,true);

        $this->general_model->exportToPDF("Insurance.pdf",$header,$html);
	}
	
	public function printInsuranceDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vehicle','Print Vehicle.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Insurance->getInsuranceDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Insurance';

        $html['content'] = $this->load->view(ADMINFOLDER."insurance/PrintInsuranceFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
?>