<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Vehicle extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Vehicle');
		$this->load->model('Vehicle_model','Vehicle');
		$this->load->model('User_model','User');
        
	}
	public function index() {

		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Vehicle";
		$this->viewData['module'] = "vehicle/Vehicle";     

		$this->load->model('Vehicle_company_model','Vehicle_company');
        $this->viewData['companydata'] = $this->Vehicle_company->getActiveVehicleCompany();
		
		$this->load->model('Party_model','Party');
        $this->viewData['ownerdata'] = $this->Party->getActiveParty('owner');
        
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Vehicle','View vehicle.');
		}
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js"); 
		$this->admin_headerlib->add_javascript("vehicle","pages/vehicle.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
       
        $list = $this->Vehicle->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) { 
        	$row = array();
            $Action = $checkbox = '';
        

            if(in_array($rollid, $edit)) {
				$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'vehicle/edit-vehicle/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
				
				if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'vehicle/vehicle-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'vehicle/vehicle-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }

            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'vehicle/check-vehicle-use","Vehicle","'.ADMIN_URL.'vehicle/delete-mul-vehicle") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
			}
			
			$Action .= '<a class="'.view_class.'" href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->id.'#vehicledetails" title='.view_title.' target="_blank">'.view_text.'</a>';

			$vehicletype = "-";
            if(!empty($datarow->vehicletype)){
                $vehicletype = $this->Licencetype[$datarow->vehicletype];
            }

            $vehicleName=$datarow->vehiclename;
            $vehicleName.=$datarow->vehiclecompanyid!=0?" (".$datarow->companyname.")":"";
 
        	$row[] = ++$counter;
            $row[] = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->id.'#vehicledetails" target="_blank">'.$vehicleName.'</a>';
            $row[] = $datarow->vehicleno;
            $row[] = $vehicletype;
            $row[] = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->ownerid.'#personaldetails" target="_blank">'.$datarow->ownername.'</a>';
			$row[] = $datarow->ownercontactno;
			$row[] = ($datarow->site!="")?$datarow->site:"-";
            $row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";
			$row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle->count_all(),
                        "recordsFiltered" => $this->Vehicle->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}
	public function add_vehicle() {
		
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Vehicle";
		$this->viewData['module'] = "vehicle/Add_vehicle";

		$this->load->model('Party_model','Party');
		$this->viewData['ownerdata'] = $this->Party->getActiveParty('owner');
        $this->viewData['partydata'] = $this->Party->getActiveParty();
        $this->viewData['driverdata'] = $this->Party->getActiveParty('driver');

        $this->load->model('Challan_type_model', 'Challan_type');
        $this->viewData['challantype'] = $this->Challan_type->getActiveChallanType();

		$this->load->model('Document_type_model','Document_type');
		$this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();

        $this->load->model('Site_model', 'Site');
        $this->viewData['sitedata'] = $this->Site->getActiveSiteData();
		
		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle","pages/add_vehicle.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	public function edit_vehicle($vehicleid) {
		
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Vehicle";
		$this->viewData['module'] = "vehicle/Add_vehicle";
		$this->viewData['action'] = "1";//Edit

        
		$this->viewData['vehicledata'] = $this->Vehicle->getVehicleDataByID($vehicleid);
		if(empty($this->viewData['vehicledata'])){
            redirect(ADMINFOLDER."pagenotfound");
		}
		$this->viewData['vehicledocumentdata'] = $this->Vehicle->getVehicleDocumentsByVehicleID($vehicleid);
		$this->viewData['vehicleInsurancedata'] = $this->Vehicle->getVehicleInsuranceByVehicleID($vehicleid);
		$this->viewData['vehicleChallandata'] = $this->Vehicle->getVehicleChallanByVehicleID($vehicleid);
        $this->viewData['vehicleFasttagdata'] = $this->Vehicle->getVehicleFastTagByVehicleID($vehicleid);
        
		$this->load->model('Party_model','Party');
		$this->viewData['ownerdata'] = $this->Party->getActiveParty('owner');
		$this->viewData['partydata'] = $this->Party->getActiveParty();
        $this->viewData['driverdata'] = $this->Party->getActiveParty('driver');
        
		$this->load->model('Document_type_model','Document_type');
		$this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();

        $this->load->model('Challan_type_model', 'Challan_type');
        $this->viewData['challantype'] = $this->Challan_type->getActiveChallanType();

        $this->viewData['installmentdata'] = $this->Vehicle->getVehicleInstallmentDataByVehicleId($vehicleid);
		
		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
		$this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
		$this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
		$this->admin_headerlib->add_javascript("add_vehicle","pages/add_vehicle.js");	
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function vehicle_add() {

        $PostData = $this->input->post();
        // print_r($PostData);exit;
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $vehiclename = $PostData['vehiclename'];
        $vehicleno = $PostData['vehicleno'];
        $engineno = $PostData['engineno'];
        $chassisno = $PostData['chassisno'];
        $vehiclecompanyid = $PostData['companyid'];
        $ownerpartyid = $PostData['ownerpartyid'];
        $vehicletype = $PostData['vehicletype'];
        $dateofregistration = ($PostData['dateofregistration']!="")?$this->general_model->convertdate($PostData['dateofregistration']):"";
        $duedateofregistration = ($PostData['duedateofregistration']!="")?$this->general_model->convertdate($PostData['duedateofregistration']):"";
        $commercial = $PostData['commercial'];
        $fueltype = $PostData['fueltype'];
        $buyer = $PostData['buyer'];
        $startingkm = $PostData['startingkm'];
        $petrocardno = $PostData['petrocardno'];
        $fuelratetype = $PostData['fuelratetype'];
        $fuelrate = $PostData['fuelrate'];
        $remarks = $PostData['remarks'];
        $issold = isset($PostData['sold'])?1:0;
		$status = $PostData['status'];
        $installmentTotalamount = $PostData['installmentTotalamount'];

        if($issold==1){
			$soldpartyid = $PostData['soldpartyid'];
			$solddate = ($PostData['solddate']!="")?$this->general_model->convertdate($PostData['solddate']):"";
        }else{
			$solddate = "";
			$soldpartyid = 0;
        }
        
        $json = array();
		$this->Vehicle->_where = array("vehicleno"=>$vehicleno);
        $Count = $this->Vehicle->CountRecords();
        if($Count == 0){

            $this->load->model('Vehicle_company_model','Vehicle_company');
            if($vehiclecompanyid!='' && !is_numeric($vehiclecompanyid)){

                $this->Vehicle_company->_where = array("companyname"=>trim($vehiclecompanyid));
                $CompanyData = $this->Vehicle_company->getRecordsByID();
              
                if(empty($CompanyData)){
    
                    $insertdata = array("companyname"=>trim($vehiclecompanyid),
								"status"=>1,
								"createddate"=>$createddate,
								"modifieddate"=>$createddate,
								"addedby"=>$addedby,
								"modifiedby"=>$addedby
							);
                    
                    $insertdata = array_map('trim', $insertdata);
                    $vehiclecompanyid = $this->Vehicle_company->Add($insertdata);
    
                }else{
                    $vehiclecompanyid = $CompanyData['id'];
                }
            }
            
            if(!is_dir(DOCUMENT_PATH)){
                @mkdir(DOCUMENT_PATH);
            }
            if(!is_dir(INSURANCE_PATH)){
                @mkdir(INSURANCE_PATH);
            }
            if(!is_dir(CHALLAN_PATH)){
                @mkdir(CHALLAN_PATH);
            }
            if(!empty($_FILES)){
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if(strpos($key, 'docfile') !== false && $_FILES['docfile'.$id]['name']!=''){
                        if($_FILES['docfile'.$id]['size'] != '' && $_FILES['docfile'.$id]['size'] >= UPLOAD_MAX_FILE_SIZE){
                            $json = array('error'=>-1,"id"=>$id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('docfile'.$id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH,'','',0);
                        if($file !== 0){
                            if($file == 2){
                                $json = array('error'=>-2,'message'=>"File not upload !","id"=>'docfile'.$id);
                                echo json_encode($json);
                                exit;
                            }
                        }else{
                            $json = array('error'=>-2,'message'=>"Accept only Image and PDF Files !","id"=>'docfile'.$id);
                            echo json_encode($json);
                            exit;
                        }  
                    }else if (strpos($key, 'fileproof') !== false && $_FILES['fileproof' . $id]['name'] != '') {
                        if ($_FILES['fileproof' . $id]['size'] != '' && $_FILES['fileproof' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            $json = array('error' => -1, "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('fileproof' . $id, 'INSURANCE', INSURANCE_PATH, '*', '', 1, INSURANCE_LOCAL_PATH, '', '', 0);
                        if ($file !== 0) {
                            if ($file == 2) {
                                $json = array('error' => -2, 'message' => "File not upload !", "id" => 'fileproof' . $id);
                                echo json_encode($json);
                                exit;
                            }
                        } else {
                            $json = array('error' => -2, 'message' => "Accept only Image and PDF Files !", "id" => 'fileproof' . $id);
                            echo json_encode($json);
                            exit;
                        }
                    }else if (strpos($key, 'challanfile') !== false && $_FILES['challanfile' . $id]['name'] != '') {
                        if ($_FILES['challanfile' . $id]['size'] != '' && $_FILES['challanfile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            $json = array('error' => -1, "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('challanfile' . $id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH, '', '', 0);
                        if ($file !== 0) {
                            if ($file == 2) {
                                $json = array('error' => -2, 'message' =>"File not upload !", "id" => 'fileproof' . $id);
                                echo json_encode($json);
                                exit;
                            }
                        } else {
                            $json = array('error' => -2, 'message' =>"Accept only Image and PDF Files !", "id" => 'fileproof' . $id);
                            echo json_encode($json);
                            exit;
                        }
                    }
                }
            }


                

            $insertdata = array("vehiclecompanyid"=>$vehiclecompanyid,
                                "ownerpartyid"=>$ownerpartyid,
                                "vehiclename"=>$vehiclename,
                                "vehicleno"=>$vehicleno,
                                "engineno"=>$engineno,
                                "chassisno"=>$chassisno,
                                "vehicletype"=>$vehicletype,
                                "dateofregistration"=>$dateofregistration,
                                "duedateofregistration"=>$duedateofregistration,
                                "commercial"=>$commercial,
                                "buyerid"=>$buyer,
                                "fueltype"=>$fueltype,
                                "startingkm"=>$startingkm,
                                "petrocardno"=>$petrocardno,
                                "fuelratetype"=>$fuelratetype,
                                "fuelrate"=>$fuelrate,
                                "sold"=>$issold,
                                "solddate"=>$solddate,
                                "soldpartyid"=>$soldpartyid,
                                "remarks"=>$remarks,
                                "status"=>$status,
                                "installmentamount"=>$installmentTotalamount,
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $insertdata=array_map('trim',$insertdata);
            $VehicleId = $this->Vehicle->Add($insertdata);
            
            if($VehicleId){

                $site = $PostData['siteid'];

                $AssignSite=array();
                $this->load->model('Assign_vehicle_model', 'Assign_vehicle');
                if(!empty($site)){
                    $AssignSite=array(
                        "vehicleid" => $VehicleId,
                        "siteid" => $site,
                        "date" => ($createddate != "" ? $this->general_model->convertdate($createddate) : ""),
                        "createddate" => $createddate,
                        "modifieddate" => $createddate,
                        "addedby" => $addedby,
                        "modifiedby" => $addedby
                    );

                    $AssignSite=array_map('trim',$AssignSite);
                    $this->Assign_vehicle->Add($AssignSite);
                }
                
                $documenttypeid = $PostData['documenttypeid'];
                $documentnumber = $PostData['documentnumber'];
                $fromdate = $PostData['fromdate'];
                $duedate = $PostData['duedate'];

                $insurancecompany = $PostData['insurancecompanyname'];
                $insurancefromdate = $PostData['insurancefromdate'];
                $insurancetodate = $PostData['insurancetodate'];
                $policyno = $PostData['policyno'];
                $amount = $PostData['amount'];
                $paymentdate = $PostData['paymentdate'];
                $insuranceagent = $PostData['insuranceagent'];

                $challanfor = $PostData['challanfor'];
                $challantype = $PostData['challantype'];
                $challandate = $PostData['challandate'];
                $challanamount = $PostData['challanamount'];
                $challanremarks = $PostData['challanremarks'];

                $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
                $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';
                
                $insertDocumentData = $insertInsuranceData = $insertChallanData = array();

                $this->load->model('Document_model','Document');
                $this->load->model('Insurance_model','Insurance');
                $this->load->model('Challan_model','Challan');

                if(!empty($_FILES)){
                    
                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);

                        if(strpos($key, 'docfile') !== false){
                            if(!empty($documenttypeid[$id]) && !empty($documentnumber[$id])){

                                $this->Document->_where = array("referencetype"=>0,"referenceid"=>$VehicleId,"documenttypeid" => $documenttypeid[$id],"documentnumber" => $documentnumber[$id]);
                                $Count = $this->Document->CountRecords();

                                if($Count==0){
                                    $file = "";
                                    if(strpos($key, 'docfile') !== false && $_FILES['docfile'.$id]['name']!=''){
                                        $file = uploadFile('docfile'.$id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);
                                        if($file == 0 && $file == 2){
                                            $file = "";
                                        } 
                                    }
                                
                                    $insertDocumentData[] = array("referencetype"=>0,
                                                                "referenceid"=>$VehicleId,
                                                                "documenttypeid"=>$documenttypeid[$id],
                                                                "documentnumber"=>$documentnumber[$id],
                                                                "fromdate"=>($fromdate[$id]!=""?$this->general_model->convertdate($fromdate[$id]):""),
                                                                "duedate"=>($duedate[$id]!=""?$this->general_model->convertdate($duedate[$id]):""),
                                                                "documentfile"=>$file,
                                                                "createddate"=>$createddate,
                                                                "modifieddate"=>$createddate,
                                                                "addedby"=>$addedby,
                                                                "modifiedby"=>$addedby
                                                            );
                                }
                            }
                        } 
                        if(strpos($key, 'fileproof') !== false){
                            
                            if(!empty($insurancefromdate[$id]) && !empty($insurancecompany[$id]) && !empty($insurancetodate[$id])){


                                $file = "";
                                if(strpos($key, 'fileproof') !== false && $_FILES['fileproof'.$id]['name']!=''){
                                    $file = uploadFile('fileproof'.$id, 'INSURANCE', INSURANCE_PATH, '*','',1,INSURANCE_LOCAL_PATH);
                                    if($file == 0 && $file == 2){
                                        $file = "";
                                    } 
                                }

                                    $insertInsuranceData[] = array("vehicleid"=>$VehicleId,
                                        "companyname"=>$insurancecompany[$id],
                                        "policyno"=>$policyno[$id],
                                        "fromdate"=> $insurancefromdate[$id]!=''?$this->general_model->convertdate($insurancefromdate[$id]):"",
                                        "todate"=> $insurancetodate[$id]!=''?$this->general_model->convertdate($insurancetodate[$id]):"",
                                        "paymentdate"=> $paymentdate[$id]!=''?$this->general_model->convertdate($paymentdate[$id]):"",
                                        "amount"=> $amount[$id],
                                        "insuranceagentid"=> $insuranceagent[$id],
                                        "proof"=> $file,
                                        "createddate"=>$createddate,
                                        "addedby"=>$addedby,
                                        "modifieddate"=>$createddate,
                                        "modifiedby"=>$addedby
                                    );
                            }

                        }

                        if(strpos($key, 'challanfile') !== false){

                            if(!empty($challanfor[$id]) && !empty($challantype[$id]) && !empty($challandate[$id]) && !empty($challanamount[$id])){
                                
                                $challanfile = "";
                                if(strpos($key, 'challanfile') !== false && $_FILES['challanfile'.$id]['name']!=''){
                                    $challanfile = uploadFile('challanfile'.$id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH);
                                    if($challanfile == 0 && $challanfile == 2){
                                        $challanfile = "";
                                    } 
                                    }
                                $insertChallanData[] = array(
                                    "partyid" => $challanfor[$id],
                                    "vehicleid" => $VehicleId,
                                    'challantypeid' => $challantype[$id],
                                    'date' => ($challandate[$id] != "")?$this->general_model->convertdate($challandate[$id]):"",
                                    'amount' => $challanamount[$id],
                                    'attachment' => $challanfile,
                                    'remarks' => $challanremarks[$id],
                                    "createddate" => $createddate,
                                    "modifieddate" => $createddate,
                                    "addedby" => $addedby,
                                    "modifiedby" => $addedby
                                );
                            }

                        }

                    }
                    if(count($insertDocumentData) > 0){
                        $this->Document->add_batch($insertDocumentData);
                    }

                    if(count($insertInsuranceData) > 0){
                        $this->Insurance->add_batch($insertInsuranceData);
                    }

                    if(count($insertChallanData) > 0){
                        $this->Challan->add_batch($insertChallanData);
                    }
                }

                if(!empty($installmentamountarr)){
                    $insertData_installment = array();
    
                    foreach($installmentamountarr as $index=>$percentage){
                        
                        $installmentamount = trim($installmentamountarr[$index]);
                        $installmentdate = $installmentdatearr[$index]!=''?$this->general_model->convertdate(trim($installmentdatearr[$index])):'';
                        $insertData_installment[] = array("vehicleid"=>$VehicleId,
                                "installmentamount" => $installmentamount,
                                "installmentdate" => $installmentdate,
                                "modifieddate" => $createddate,
                                "modifiedby"=>$addedby);
                    }
                    if(!empty($insertData_installment)){
                        $this->Vehicle->_table = tbl_vehicleinstallment;
                        $this->Vehicle->add_batch($insertData_installment);
                    }
                }

                $accountno = $PostData['accountno'];
                $walletid = $PostData['walletid'];
                $rfidno = $PostData['rfidno'];

                if(!empty($accountno) && !empty($walletid) && !empty($rfidno)){
                    
                    $insertFasttagData = array(
                        "vehicleid"=>$VehicleId,
                        "accountno"=>$accountno,
                        "walletid"=>$walletid,
                        "rfidno"=>$rfidno,
                        "createddate"=>$createddate,
                        "addedby"=>$addedby,
                        "modifieddate"=>$createddate,
                        "modifiedby"=>$addedby
                    );

                    $insertFasttagData=array_map('trim',$insertFasttagData);
                    $this->Vehicle->_table = tbl_vehiclefasttag;
                    $this->Vehicle->Add($insertFasttagData);
                }

                
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Vehicle','Add new '.$vehiclename.' vehicle.');
                }
                $json = array("error"=>1);
            }else{
                $json = array("error"=>0);
            }
        }else{
            $json = array("error"=>2);
        }
        echo json_encode($json);
	}
	public function update_vehicle() {
        
        $PostData = $this->input->post();
        // print_r($PostData);exit;
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
			
		$vehicleid = $PostData['vehicleid'];
		$vehiclename = $PostData['vehiclename'];
        $vehicleno = $PostData['vehicleno'];
        $engineno = $PostData['engineno'];
        $chassisno = $PostData['chassisno'];
        $vehiclecompanyid = $PostData['companyid'];
        $ownerpartyid = $PostData['ownerpartyid'];
        $vehicletype = $PostData['vehicletype'];
        $dateofregistration = ($PostData['dateofregistration']!="")?$this->general_model->convertdate($PostData['dateofregistration']):"";
        $duedateofregistration = ($PostData['duedateofregistration']!="")?$this->general_model->convertdate($PostData['duedateofregistration']):"";
        $commercial = $PostData['commercial'];
        $fueltype = $PostData['fueltype'];
        $buyer = $PostData['buyer'];
        $startingkm = $PostData['startingkm'];
        $petrocardno = $PostData['petrocardno'];
        $fuelratetype = $PostData['fuelratetype'];
        $fuelrate = $PostData['fuelrate'];
        $remarks = $PostData['remarks'];
        $issold = isset($PostData['sold'])?1:0;
		$status = $PostData['status'];
        $installmentTotalamount = $PostData['installmentTotalamount'];
		
        if($issold==1){
			$soldpartyid = $PostData['soldpartyid'];
			$solddate = ($PostData['solddate']!="")?$this->general_model->convertdate($PostData['solddate']):"";
        }else{
			$solddate = "";
			$soldpartyid = 0;
        }

		$json = array();
		$this->Vehicle->_where = array("id<>"=>$vehicleid,"vehicleno"=>$vehicleno);
        $Count = $this->Vehicle->CountRecords();
        if($Count == 0){
            
            $this->load->model('Vehicle_company_model','Vehicle_company');
            if($vehiclecompanyid!='' && !is_numeric($vehiclecompanyid)){

                $this->Vehicle_company->_where = array("companyname"=>trim($vehiclecompanyid));
                $CompanyData = $this->Vehicle_company->getRecordsByID();
              
                if(empty($CompanyData)){
    
                    $insertdata = array("companyname"=>trim($vehiclecompanyid),
								"status"=>1,
								"createddate"=>$modifieddate,
								"modifieddate"=>$modifieddate,
								"addedby"=>$modifiedby,
								"modifiedby"=>$modifiedby
							);
                    
                    $insertdata = array_map('trim', $insertdata);
                    $vehiclecompanyid = $this->Vehicle_company->Add($insertdata);
    
                }else{
                    $vehiclecompanyid = $CompanyData['id'];
                }
            }

			if(!is_dir(DOCUMENT_PATH)){
                @mkdir(DOCUMENT_PATH);
            }
            if(!is_dir(INSURANCE_PATH)){
                @mkdir(INSURANCE_PATH);
            }
            if(!is_dir(CHALLAN_PATH)){
                @mkdir(CHALLAN_PATH);
            }
            if(!empty($_FILES)){
                foreach ($_FILES as $key => $value) {
                    $id = preg_replace('/[^0-9]/', '', $key);
                    if(strpos($key, 'docfile') !== false && $_FILES['docfile'.$id]['name']!=''){
                        if($_FILES['docfile'.$id]['size'] != '' && $_FILES['docfile'.$id]['size'] >= UPLOAD_MAX_FILE_SIZE){
                            $json = array('error'=>-1,"id"=>$id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('docfile'.$id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH,'','',0);
                        if($file !== 0){
                            if($file == 2){
                                $json = array('error'=>-2,'message'=>"File not upload !","id"=>'docfile'.$id);
                                echo json_encode($json);
                                exit;
                            }
                        }else{
                            $json = array('error'=>-2,'message'=>"Accept only Image and PDF Files !","id"=>'docfile'.$id);
                            echo json_encode($json);
                            exit;
                        }  
                    }else if (strpos($key, 'fileproof') !== false && $_FILES['fileproof' . $id]['name'] != '') {
                        if ($_FILES['fileproof' . $id]['size'] != '' && $_FILES['fileproof' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            $json = array('error' => -1, "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('fileproof' . $id, 'INSURANCE', INSURANCE_PATH, '*', '', 1, INSURANCE_LOCAL_PATH, '', '', 0);
                        if ($file !== 0) {
                            if ($file == 2) {
                                $json = array('error' => -2, 'message' => "File not upload !", "id" => 'fileproof'.$id);
                                echo json_encode($json);
                                exit;
                            }
                        } else {
                            $json = array('error' => -2, 'message' => "Accept only Image and PDF Files !", "id" => 'fileproof'.$id);
                            echo json_encode($json);
                            exit;
                        }
                    }else if (strpos($key, 'challanfile') !== false && $_FILES['challanfile' . $id]['name'] != '') {
                        if ($_FILES['challanfile' . $id]['size'] != '' && $_FILES['challanfile' . $id]['size'] >= UPLOAD_MAX_FILE_SIZE) {
                            $json = array('error' => -1, "id" => $id);
                            echo json_encode($json);
                            exit;
                        }
                        $file = uploadFile('challanfile' . $id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH, '', '', 0);
                        if ($file !== 0) {
                            if ($file == 2) {
                                $json = array('error' => -2, 'message' => "File not upload !", "id" => 'challanfile'.$id);
                                echo json_encode($json);
                                exit;
                            }
                        } else {
                            $json = array('error' => -2, 'message' => "Accept only Image and PDF Files !", "id" => 'challanfile'.$id);
                            echo json_encode($json);
                            exit;
                        }
                    }
                }
            }

			$updatedata =  array("vehiclecompanyid"=>$vehiclecompanyid,
								"ownerpartyid"=>$ownerpartyid,
								"vehiclename"=>$vehiclename,
								"vehicleno"=>$vehicleno,
								"engineno"=>$engineno,
								"chassisno"=>$chassisno,
								"vehicletype"=>$vehicletype,
								"dateofregistration"=>$dateofregistration,
								"duedateofregistration"=>$duedateofregistration,
                                "commercial"=>$commercial,
                                "buyerid"=>$buyer,
                                "fueltype"=>$fueltype,
                                "startingkm"=>$startingkm,
                                "petrocardno"=>$petrocardno,
                                "fuelratetype"=>$fuelratetype,
                                "fuelrate"=>$fuelrate,
								"sold"=>$issold,
								"solddate"=>$solddate,
								"soldpartyid"=>$soldpartyid,
								"remarks"=>$remarks,
								"status"=>$status,
                                "installmentamount"=>$installmentTotalamount,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby
							);

			$this->Vehicle->_where = array("id"=>$vehicleid);
			$Edit = $this->Vehicle->Edit($updatedata);
			if($Edit){
				$documenttypeid = $PostData['documenttypeid'];
                $documentnumber = $PostData['documentnumber'];
                $fromdate = $PostData['fromdate'];
                $duedate = $PostData['duedate'];
                $documentidarray = isset($PostData['documentid'])?$PostData['documentid']:'';
                $olddocfilearray = isset($PostData['olddocfile'])?$PostData['olddocfile']:"";

                $insurancecompany = $PostData['insurancecompanyname'];
                $insurancefromdate = $PostData['insurancefromdate'];
                $insurancetodate = $PostData['insurancetodate'];
                $policyno = $PostData['policyno'];
                $amount = $PostData['amount'];
                $paymentdate = $PostData['paymentdate'];
                $insuranceagent = $PostData['insuranceagent'];
                $InsuranceIdArray = isset($PostData['insuranceid'])?$PostData['insuranceid']:'';
                $oldInsurancefile = isset($PostData['oldInsurancefile'])?$PostData['oldInsurancefile']:'';
                $ChallanIdArray = isset($PostData['challanid'])?$PostData['challanid']:'';
                $oldchallanfile = isset($PostData['oldChallanfile'])?$PostData['oldChallanfile']:'';
                $challanfor = $PostData['challanfor'];
                $challantype = $PostData['challantype'];
                $challandate = $PostData['challandate'];
                $challanamount = $PostData['challanamount'];
                $challanremarks = $PostData['challanremarks'];

                $installmentid = isset($PostData['installmentid'])?$PostData['installmentid']:array();
                $installmentamountarr = isset($PostData['installmentamount'])?$PostData['installmentamount']:'';
                $installmentdatearr = isset($PostData['installmentdate'])?$PostData['installmentdate']:'';

                $insertDocumentData = $updateDocumentData = $deleteidsarray = array();
                $insertInsuranceData = $UpdateInsuranceData = $deleteInsuranceIdData = array();
                $insertChallanData = $UpdateChallanData = $deleteChallanIdData = array();

                $this->load->model('Document_model','Document');
                $this->load->model('Insurance_model','Insurance');
                $this->load->model('Challan_model','Challan');
                $this->load->model('Document_type_model','Document_type');

                if(!empty($_FILES)){
                    
                    foreach ($_FILES as $key => $value) {
                        $id = preg_replace('/[^0-9]/', '', $key);
                    
                        if(strpos($key, 'docfile') !== false){

                            $documentid = (isset($documentidarray[$id]) && !empty($documentidarray[$id]))?$documentidarray[$id]:"";
                            if($documentid!=""){
                                $file=$olddocfilearray[$id];
                                
                                if($_FILES['docfile'.$id]['name']!='' && $olddocfilearray[$id]==""){
									$file = uploadFile('docfile'.$id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);
									if($file == 0 && $file == 2){
										$file = "";
									}
								}else if(($_FILES['docfile'.$id]['name']!='' || $_FILES['docfile'.$id]['name']=='') && $olddocfilearray[$id]!=""){
                                    if($_FILES['docfile'.$id]['name']!=''){

                                        $file = reuploadFile('docfile'.$id, 'DOCUMENT', $olddocfilearray[$id], DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);

                                        if($file == 0 && $file == 2){
                                            $file = "";
                                        } 
                                    }
                                    
                                }else{
									$file = "";
								}
								$updateDocumentData[] = array('id' => $documentid,
											"documenttypeid"=>$documenttypeid[$id],
											"documentnumber"=>$documentnumber[$id],
											"fromdate"=>($fromdate[$id]!=""?$this->general_model->convertdate($fromdate[$id]):""),
											"duedate"=>($duedate[$id]!=""?$this->general_model->convertdate($duedate[$id]):""),
											"documentfile"=>$file,
											"modifieddate"=>$modifieddate,
											"modifiedby"=>$modifiedby
										);

                                $deleteidsarray[] = $documentid;

                            }else{
                                if(!empty($documenttypeid[$id]) && !empty($documentnumber[$id])){
                                    $file = "";
                                    if($_FILES['docfile'.$id]['name']!=''){

                                        $file = uploadFile('docfile'.$id, 'DOCUMENT', DOCUMENT_PATH, '*', '', 1, DOCUMENT_LOCAL_PATH);
                                        if($file == 0 && $file == 2){
                                            $file = "";
                                        } 
                                    }
                                    $insertDocumentData[] = array("referencetype"=>0,
                                                            "referenceid"=>$vehicleid,
                                                            "documenttypeid"=>$documenttypeid[$id],
                                                            "documentnumber"=>$documentnumber[$id],
                                                            "fromdate"=>($fromdate[$id]!=""?$this->general_model->convertdate($fromdate[$id]):""),
                                                            "duedate"=>($duedate[$id]!=""?$this->general_model->convertdate($duedate[$id]):""),
                                                            "documentfile"=>$file,
                                                            "createddate"=>$modifieddate,
                                                            "modifieddate"=>$modifieddate,
                                                            "addedby"=>$modifiedby,
                                                            "modifiedby"=>$modifiedby
                                                        );

                                }
                            }
                        }else if (strpos($key, 'fileproof') !== false){

                            $InsuranceId = (isset($InsuranceIdArray[$id]) && !empty($InsuranceIdArray[$id]))?$InsuranceIdArray[$id]:"";

                            if($InsuranceId!=""){

                                $file=$oldInsurancefile[$id];
                                
                                if($_FILES['fileproof'.$id]['name']!='' && $oldInsurancefile[$id]==""){
									$file = uploadFile('fileproof'.$id, 'INSURANCE', INSURANCE_PATH, '*', '', 1, INSURANCE_LOCAL_PATH);
									if($file == 0 && $file == 2){
										$file = "";
									}
								}else if(($_FILES['fileproof'.$id]['name']!='' || $_FILES['fileproof'.$id]['name']=='') && $oldInsurancefile[$id]!=""){
                                    if($_FILES['fileproof'.$id]['name']!=''){
                                        $file = reuploadFile('fileproof'.$id, 'INSURANCE', $oldInsurancefile[$id], INSURANCE_PATH, '*', '', 1, INSURANCE_LOCAL_PATH);
                                        if($file == 0 && $file == 2){
                                            $file = "";
                                        }
                                    }
                                    
                                }else{
									$file = "";
								}

                                $UpdateInsuranceData[] = array("id"=>$InsuranceId,
                                        "companyname"=>$insurancecompany[$id],
                                        "policyno"=>$policyno[$id],
                                        "fromdate"=> $insurancefromdate[$id]!=''?$this->general_model->convertdate($insurancefromdate[$id]):"",
                                        "todate"=> $insurancetodate[$id]!=''?$this->general_model->convertdate($insurancetodate[$id]):"",
                                        "paymentdate"=> $paymentdate[$id]!=''?$this->general_model->convertdate($paymentdate[$id]):"",
                                        "amount"=> $amount[$id],
                                        "insuranceagentid"=> $insuranceagent[$id],
                                        "proof"=> $file,
                                        "modifieddate"=>$modifieddate,
                                        "modifiedby"=>$modifiedby
                                    );


                                $deleteInsuranceIdData[] = $InsuranceId;
                            }
                            else{
                                if(!empty($insurancecompany[$id]) && !empty($insurancefromdate[$id]) && !empty($insurancetodate[$id])){
                                    $file = "";
                                    if($_FILES['fileproof'.$id]['name']!=''){

                                        $file = uploadFile('fileproof'.$id, 'INSURANCE', INSURANCE_PATH, '*', '', 1, INSURANCE_LOCAL_PATH);
                                        if($file == 0 && $file == 2){
                                            $file = "";
                                        } 
                                    }

                                    $insertInsuranceData[] = array("vehicleid"=>$vehicleid,
                                                "companyname"=>$insurancecompany[$id],
                                                "policyno"=>$policyno[$id],
                                                "fromdate"=> $insurancefromdate[$id]!=''?$this->general_model->convertdate($insurancefromdate[$id]):"",
                                                "todate"=> $insurancetodate[$id]!=''?$this->general_model->convertdate($insurancetodate[$id]):"",
                                                "paymentdate"=> $paymentdate[$id]!=''?$this->general_model->convertdate($paymentdate[$id]):"",
                                                "amount"=> $amount[$id],
                                                "insuranceagentid"=> $insuranceagent[$id],
                                                "proof"=> $file,
                                                "createddate"=>$modifieddate,
                                                "modifieddate"=>$modifieddate,
                                                "addedby"=>$modifiedby,
                                                "modifiedby"=>$modifiedby
                                    );
                                }
                            }

                        }else if (strpos($key, 'challanfile') !== false){

                            $ChallanId = (isset($ChallanIdArray[$id]) && !empty($ChallanIdArray[$id]))?$ChallanIdArray[$id]:"";

                            if($ChallanId!=""){

                                $file=$oldchallanfile[$id];
                                
                                if($_FILES['challanfile'.$id]['name']!='' && $oldchallanfile[$id]==""){
									$file = uploadFile('challanfile'.$id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH);
									if($file == 0 && $file == 2){
										$file = "";
									}
								}else if(($_FILES['challanfile'.$id]['name']!='' || $_FILES['challanfile'.$id]['name']=='') && $oldchallanfile[$id]!=""){
                                    if($_FILES['challanfile'.$id]['name']!=''){
                                        $file = reuploadFile('challanfile'.$id, 'CHALLAN', $oldchallanfile[$id], CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH);
                                        if($file == 0 && $file == 2){
                                            $file = "";
                                        }
                                    }
                                }else{
									$file = "";
								}
                                    $UpdateChallanData[] = array(
                                        "id" => $ChallanId,
                                        "partyid" => $challanfor[$id],
                                        'challantypeid' => $challantype[$id],
                                        'date' => ($challandate[$id] != "")?$this->general_model->convertdate($challandate[$id]):"",
                                        'amount' => $challanamount[$id],
                                        'attachment' => $file,
                                        'remarks' => $challanremarks[$id],
                                        "modifieddate" => $modifieddate,
                                        "modifiedby" => $modifiedby
                                    );
                                    $deleteChallanIdData[] = $ChallanId;
                            }
                            else{

                                if(!empty($challanfor[$id]) && !empty($challantype[$id]) && !empty($challandate[$id]) && !empty($challanamount[$id])){
                                    $file = "";
                                    if($_FILES['challanfile'.$id]['name']!=''){

                                        $file = uploadFile('challanfile'.$id, 'CHALLAN', CHALLAN_PATH, '*', '', 1, CHALLAN_LOCAL_PATH);
                                        if($file == 0 && $file == 2){
                                            $file = "";
                                        } 
                                    }

                                    $insertChallanData[] = array(
                                        "partyid" => $challanfor[$id],
                                        "vehicleid" => $vehicleid,
                                        'challantypeid' => $challantype[$id],
                                        'date' => ($challandate[$id] != "")?$this->general_model->convertdate($challandate[$id]):"",
                                        'amount' => $challanamount[$id],
                                        'attachment' => $file,
                                        'remarks' => $challanremarks[$id],
                                        "createddate" => $modifieddate,
                                        "modifieddate" => $modifieddate,
                                        "addedby" => $modifiedby,
                                        "modifiedby" => $modifiedby
                                    );
                                }

                            }
                        }
                    }
                }

                if(empty($installmentid)){
                    if(!empty($installmentamountarr)){
                        $insertData_installment = array();
        
                        foreach($installmentamountarr as $index=>$percentage){
                            
                            $installmentamount = trim($installmentamountarr[$index]);
                            $installmentdate = $installmentdatearr[$index]!=''?$this->general_model->convertdate(trim($installmentdatearr[$index])):'';
                            $insertData_installment[] = array(
                                    "vehicleid"=>$vehicleid,
                                    "installmentamount" => $installmentamount,
                                    "installmentdate" => $installmentdate,
                                    "modifieddate" => $modifieddate,
                                    "modifiedby"=>$modifiedby);
                        }
                    }
                }

                // DOCUMENT OPERATIONS
                $vehicledocumentdata = $this->Vehicle->getVehicleDocumentsByVehicleID($vehicleid);
                $documentidarray = (!empty($vehicledocumentdata)?array_column($vehicledocumentdata,"id"):array()); 
                if(!empty($documentidarray)){
                    $deletearr = array_diff($documentidarray,$deleteidsarray);
                }
                if(!empty($deletearr)){

                    if(!empty($vehicledocumentdata)){
                        foreach($vehicledocumentdata as $doc){
                            unlinkfile("DOCUMENT",$doc['documentfile'],DOCUMENT_PATH);
                        }
                    }   
                    $this->Document->Delete(array("id IN (".implode(",",$deletearr).")"=>null));
                }
                if(count($insertDocumentData) > 0){
                    $this->Document->add_batch($insertDocumentData);
				}
                if(count($updateDocumentData) > 0){
                    $this->Document->edit_batch($updateDocumentData, "id");
                }

                // INSURANCE OPERATIONS
                $vehicleInsurancedata = $this->Vehicle->getVehicleInsuranceByVehicleID($vehicleid);
                $Insuranceidarray = (!empty($vehicleInsurancedata)?array_column($vehicleInsurancedata,"id"):array()); 
                if(!empty($Insuranceidarray)){
                    $deleteInsurancearr = array_diff($Insuranceidarray,$deleteInsuranceIdData);
                }
                if(!empty($deleteInsurancearr)){
                    if(!empty($vehicleInsurancedata)){
                        foreach($vehicleInsurancedata as $data){
                            unlinkfile("INSURANCE", $data['proof'], INSURANCE_PATH);
                        }
                    } 
                    $this->Insurance->Delete(array("id IN (".implode(",",$deleteInsurancearr).")"=>null));
                }
                if(count($insertInsuranceData) > 0){
                    $this->Insurance->add_batch($insertInsuranceData);
				}
                if(count($UpdateInsuranceData) > 0){
                    $this->Insurance->edit_batch($UpdateInsuranceData, "id");
                }

                // CHALLAN OPERATIONS
                $vehicleChallandata = $this->Vehicle->getVehicleChallanByVehicleID($vehicleid);
                $Challanidarray = (!empty($vehicleChallandata)?array_column($vehicleChallandata,"id"):array()); 
                if(!empty($Challanidarray)){
                    $deleteChallanarr = array_diff($Challanidarray,$deleteChallanIdData);
                }
                if(!empty($deleteChallanarr)){
                    if(!empty($vehicleChallandata)){
                        foreach($vehicleChallandata as $data){
                            unlinkfile("CHALLAN", $data['attachment'], CHALLAN_PATH);
                        }
                    } 
                    $this->Challan->Delete(array("id IN (".implode(",",$deleteChallanarr).")"=>null));
                }
                if(count($insertChallanData) > 0){
                    $this->Challan->add_batch($insertChallanData);
				}
                if(count($UpdateChallanData) > 0){
                    $this->Challan->edit_batch($UpdateChallanData, "id");
                }


                $vehicleInstallmentdata = $this->Vehicle->getVehicleInstallmentDataByVehicleId($vehicleid);
                $EMIidarray = (!empty($vehicleInstallmentdata)?array_column($vehicleInstallmentdata,"id"):array()); 
                if(!empty($EMIidarray)){
                    $deleteEMIarr = array_diff($EMIidarray,$installmentid);
                }
                if(!empty($deleteEMIarr)){
                    $this->Vehicle->_table = tbl_vehicleinstallment;
                    $this->Vehicle->Delete(array("id IN (".implode(",",$deleteEMIarr).")"=>null));
                }
                if(!empty($insertData_installment)){
                    $this->Vehicle->_table = tbl_vehicleinstallment;
                    $this->Vehicle->add_batch($insertData_installment);
                }

                $accountno = $PostData['accountno'];
                $walletid = $PostData['walletid'];
                $rfidno = $PostData['rfidno'];


                $vehiclefasttagdata = $this->Vehicle->getVehicleFastTagByVehicleID($vehicleid);

                if(empty($vehiclefasttagdata)){
                    $insertFasttagData  = array(
                        "vehicleid"=>$vehicleid,
                        "accountno"=>$accountno,
                        "walletid"=>$walletid,
                        "rfidno"=>$rfidno,
                        "createddate"=>$modifieddate,
                        "addedby"=>$modifiedby,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby
                    );
                    $insertFasttagData=array_map('trim',$insertFasttagData);
                    $this->Vehicle->_table = tbl_vehiclefasttag;
                    $this->Vehicle->Add($insertFasttagData);

                } else {
                    $updateFasttagData = array(
                        "accountno"=>$accountno,
                        "walletid"=>$walletid,
                        "rfidno"=>$rfidno,
                        "modifieddate"=>$modifieddate,
                        "modifiedby"=>$modifiedby
                    );
                    $updateFasttagData=array_map('trim',$updateFasttagData);
                    $this->Vehicle->_table = tbl_vehiclefasttag;
                    $this->Vehicle->_where = array("vehicleid"=>$vehicleid);
                    $this->Vehicle->Edit($updateFasttagData);    
                }
                

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Vehicle','Edit '.$vehiclename.' vehicle.');
                }
				$json = array("error"=>1);				
			}else{
                $json = array("error"=>0);
            }
		}else{
            $json = array("error"=>2);
        }
		echo json_encode($json);
	}
	public function view_vehicle($vehicleid){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Vehicle";
        $this->viewData['module'] = "vehicle/View_vehicle";
        
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicleDetailById($vehicleid);
        if(empty($this->viewData['vehicledata'])) {    
            redirect(ADMINFOLDER."pagenotfound");
        }

        $this->load->model('Document_type_model','Document_type');
        $this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();

        $this->load->model('Party_model','Party');
        $this->viewData['driverdata'] = $this->Party->getActiveParty('driver');
        $this->viewData['garagedata'] = $this->Party->getActiveParty('garage');
        
        $this->load->model('Service_type_model','Service_type');
        $this->viewData['servicetypedata'] = $this->Service_type->getActiveServiceType();

        $this->load->model('Assign_vehicle_model','Assign_vehicle');
        $this->viewData['assignedsitecitydata'] = $this->Assign_vehicle->getActiveCityOnAssignVehicleSite($vehicleid);

        $this->load->model('Challan_type_model','Challan_type');
        $this->viewData['challantypedata'] = $this->Challan_type->getActiveChallanType();

        $this->load->model('Insurance_model','Insurance');
        $this->viewData['insurancecompanydata'] = $this->Insurance->getInsuranceCompanyByVehicleId($vehicleid);

        $this->viewData['emiremainder'] = $this->Vehicle->getVehicleInstallmentDataByVehicleId($vehicleid);
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");    
        $this->admin_headerlib->add_javascript("view_vehicle","pages/view_vehicle.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
	public function vehicle_enable_disable() {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Vehicle->_where = array("id"=>$PostData['id']);
		$this->Vehicle->Edit($updatedata);
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Vehicle->_where = array("id"=>$PostData['id']);
            $data = $this->Vehicle->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['vehiclename'].' vehicle.';
            
            $this->general_model->addActionLog(2,'Vehicle', $msg);
        }
		echo $PostData['id'];
	}
	public function check_vehicle_use(){
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){
            $query = $this->db->query("SELECT id FROM ".tbl_vehicle." WHERE 
                    id IN (SELECT referenceid FROM ".tbl_document." WHERE referencetype=0 AND referenceid = $row) OR 
                    id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR 
                    id IN (SELECT vehicleid FROM ".tbl_fuel." WHERE vehicleid = $row) OR
                    id IN (SELECT vehicleid FROM ".tbl_assignvehicle." WHERE vehicleid = $row) OR 
                    id IN (SELECT vehicleid FROM ".tbl_challan." WHERE vehicleid = $row) OR 
                    id IN (SELECT vehicleid FROM ".tbl_service." WHERE vehicleid = $row) 
                    ");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
		echo $count;
	}
	public function delete_mul_vehicle(){
		$this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$this->load->model('Document_model','Document');

        $checkuse = 0;
            foreach($ids as $row){
                $query = $this->db->query("SELECT id FROM ".tbl_vehicle." WHERE 
                        id IN (SELECT referenceid FROM ".tbl_document." WHERE referencetype=0 AND referenceid = $row) OR 
                        id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR
                        id IN (SELECT vehicleid FROM ".tbl_fuel." WHERE vehicleid = $row) OR
                        id IN (SELECT vehicleid FROM ".tbl_assignvehicle." WHERE vehicleid = $row) OR
                        id IN (SELECT vehicleid FROM ".tbl_challan." WHERE vehicleid = $row) OR
                        id IN (SELECT vehicleid FROM ".tbl_service." WHERE vehicleid = $row) 
                        ");
                
                if($query->num_rows() > 0){
                    $checkuse++;
            }
        }
            
        if($checkuse == 0){

            $this->Vehicle->_where = array("id"=>$row);
            $data = $this->Vehicle->getRecordsById();
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Vehicle','Delete '.$data['vehiclename'].' vehicle.');
            }

            $this->Document->_where = array("referencetype"=>0, "referenceid"=>$row);
            $documents = $this->Document->getRecordsById();

            if(!empty($documents)){
                foreach($documents as $document){
                    if($document['documentfile']!=""){
                        unlinkfile("DOCUMENT",$document['documentfile'],DOCUMENT_PATH);
                    }
                }
            }
            $this->Document->Delete(array("referencetype"=>0, "referenceid"=>$row));
            $this->Vehicle->Delete(array("id"=>$row));
        }
	}
	public function getActiveVehicleCompany(){
        $PostData = $this->input->post();

		$this->load->model('Vehicle_company_model','Vehicle_company');
        if(isset($PostData["term"])){
            $Companydata = $this->Vehicle_company->searchVehicleCompany(1,$PostData["term"]);
        }else if(isset($PostData["ids"])){
            $Companydata = $this->Vehicle_company->searchVehicleCompany(0,$PostData["ids"]);
        }
        
        echo json_encode($Companydata);
	}
	public function vehicledocumentlisting() { 
		
		$this->load->model("Vehicle_document_model","Vehicle_document");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Vehicle_document->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="javascript:void(0)" onclick="openDocumentModal('.$datarow->referencetype.','.$datarow->referenceid.','.$datarow->id.')" title='.edit_title.'>'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'document/check-document-use","Document","'.ADMIN_URL.'document/delete-mul-document",\'\',\'\',\'documenttable\',\'deletecheckalldocument\') >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="doccheck'.$datarow->id.'" id="doccheck'.$datarow->id.'" onchange="singlecheck(this.id,\'documenttable\',\'deletecheckalldocument\')"><label for="doccheck'.$datarow->id.'"></label></div>';
            }
            if($datarow->documentfile!="" && file_exists(DOCUMENT_PATH.$datarow->documentfile)){
                $Action .= '<a class="'.download_class.'" href="'.DOCUMENT.$datarow->documentfile.'" title="'.download_title.'" download>'.download_text.'</a>';
                $Action .= '<a class="'.viewdoc_class.'" href="'.DOCUMENT.$datarow->documentfile.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $datarow->documenttype;
            $row[] = $datarow->documentnumber;
            $row[] = ($datarow->fromdate!="0000-00-00")?$this->general_model->displaydate($datarow->fromdate):"-";  
            $row[] = ($datarow->duedate!="0000-00-00")?$this->general_model->displaydate($datarow->duedate):"-";  
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_document->count_all(),
                        "recordsFiltered" => $this->Vehicle_document->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
	}
	public function vehiclefuellisting() { 
		
		$this->load->model("Vehicle_fuel_model","Vehicle_fuel");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Vehicle_fuel->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'fuel/edit-fuel/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'fuel/check-fuel-use","Fuel","'.ADMIN_URL.'fuel/delete-mul-fuel",\'\',\'\',\'fueltable\',\'deletecheckallfuel\') >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="fuelcheck'.$datarow->id.'" id="fuelcheck'.$datarow->id.'" onchange="singlecheck(this.id,\'fueltable\',\'deletecheckallfuel\')"><label for="fuelcheck'.$datarow->id.'"></label></div>';
            }
            
            // $row[] = ++$counter;
            $row[] = ($datarow->date!="0000-00-00")?$this->general_model->displaydate($datarow->date):"-";  
            $row[] = $this->Fueltype[$datarow->fueltype];
            $row[] = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->partyid.'" target="_blank">'.$datarow->drivername.'</a>';
			$row[] = numberFormat($datarow->amount,2,',');
			$row[] = $datarow->km;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_fuel->count_all(),
                        "recordsFiltered" => $this->Vehicle_fuel->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function vehicleservicelisting() { 
		
		$this->load->model("Vehicle_service_model","Vehicle_service");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Vehicle_service->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'service/edit-service/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'service/check-service-use","Service","'.ADMIN_URL.'service/delete-mul-service",\'\',\'\',\'servicetable\',\'deletecheckallservice\') >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="servicecheck'.$datarow->id.'" id="servicecheck'.$datarow->id.'" onchange="singlecheck(this.id,\'servicetable\',\'deletecheckallservice\')"><label for="servicecheck'.$datarow->id.'"></label></div>';
            }
            
            $row[] = $datarow->servicetype;
            $row[] = ($datarow->date!="0000-00-00")?$this->general_model->displaydate($datarow->date):"-";  
            $row[] = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->driverid.'" target="_blank">'.$datarow->drivername.'</a>';
            $row[] = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->garageid.'" target="_blank">'.$datarow->garagename.'</a>';
			$row[] = numberFormat($datarow->amount,2,',');
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
			$row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_service->count_all(),
                        "recordsFiltered" => $this->Vehicle_service->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function vehiclechallanlisting() { 
		
		$this->load->model("Vehicle_challan_model","Vehicle_challan");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Vehicle_challan->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'challan/edit-challan/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'challan/check-challan-use","Challan","'.ADMIN_URL.'challan/delete-mul-challan",\'\',\'\',\'challantable\',\'deletecheckallchallan\') >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="challancheck'.$datarow->id.'" id="challancheck'.$datarow->id.'" onchange="singlecheck(this.id,\'challantable\',\'deletecheckallchallan\')"><label for="challancheck'.$datarow->id.'"></label></div>';
            }
            if ($datarow->attachment !== '' && file_exists(CHALLAN_PATH . $datarow->attachment)) {
                $Action .= '<a class="'.download_class.'" href="'.CHALLAN.$datarow->attachment.'" title="'.download_title.'" download>'.download_text.'</a>';
                $Action .= '<a class="'.viewdoc_class.'" href="'.CHALLAN.$datarow->attachment.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
            }
            $row[] = $datarow->challantype;
            $row[] = ($datarow->date!="0000-00-00")?$this->general_model->displaydate($datarow->date):"-";  
            $row[] = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->partyid.'" target="_blank">'.$datarow->drivername.'</a>';
            $row[] = numberFormat($datarow->amount,2,',');
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
			$row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_challan->count_all(),
                        "recordsFiltered" => $this->Vehicle_challan->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function vehicleinsurancelisting() { 
		
		$this->load->model("Vehicle_insurance_model","Vehicle_insurance");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Vehicle_insurance->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'insurance/edit-insurance/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Insurance","'.ADMIN_URL.'insurance/delete-mul-insurance",\'\',\'\',\'insurancetable\',\'deletecheckallinsurance\') >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="insurancecheck'.$datarow->id.'" id="insurancecheck'.$datarow->id.'" onchange="singlecheck(this.id,\'insurancetable\',\'deletecheckallinsurance\')"><label for="insurancecheck'.$datarow->id.'"></label></div>';
            }
            if($datarow->proof!="" && file_exists(INSURANCE_PATH.$datarow->proof)){
                $Action .= '<a class="'.download_class.'" href="'.INSURANCE.$datarow->proof.'" title="'.viewbtn_title.'" download>'.download_text.'</a>';
                $Action .= '<a class="'.viewdoc_class.'" href="'.INSURANCE.$datarow->proof.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
			}
            
            $row[] = $datarow->companyname;
            $row[] = ($datarow->fromdate!="0000-00-00")?$this->general_model->displaydate($datarow->fromdate):"-";    
            $row[] = ($datarow->todate!="0000-00-00")?$this->general_model->displaydate($datarow->todate):"-";    
            $row[] = numberFormat($datarow->amount,2,',');
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
			$row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_insurance->count_all(),
                        "recordsFiltered" => $this->Vehicle_insurance->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function vehicleinsuranceclaimlisting() { 
		
		$this->load->model("Vehicle_insurance_claim_model","Vehicle_insurance_claim");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Vehicle_insurance_claim->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';

            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'insurance-claim/edit-insurance-claim/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Insurance&nbsp;Claim","'.ADMIN_URL.'insurance-claim/delete-mul-insurance-claim",\'\',\'\',\'insuranceclaimtable\',\'deletecheckallinsuranceclaim\') >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="insuranceclaimcheck'.$datarow->id.'" id="insuranceclaimcheck'.$datarow->id.'" onchange="singlecheck(this.id,\'insuranceclaimtable\',\'deletecheckallinsuranceclaim\')"><label for="insuranceclaimcheck'.$datarow->id.'"></label></div>';
            }
            if ($datarow->attachment!=='' && file_exists(INSURANCE_CLAIM_PATH.$datarow->attachment)) {
                $Action .= '<a class="'.download_class.'" href="'.INSURANCE_CLAIM.$datarow->attachment.'" title="'.download_title.'" download>'.download_text.'</a>';
                $Action .= '<a class="'.viewdoc_class.'" href="'.INSURANCE_CLAIM.$datarow->attachment.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
            }
            $claimstatus = "-";
            if ($datarow->status == 0) {
                $claimstatus = '<button class="btn btn-warning btn-raised">Pending</button>';
            }else if ($datarow->status == 1) {
                $claimstatus = '<button class="btn btn-success btn-raised">Approved</button>';
            }else if ($datarow->status == 2) {
                $claimstatus = '<button class="btn btn-danger btn-raised">Rejected</button>';
            }else if ($datarow->status == 3) {
                $claimstatus = '<button class="btn btn-danger btn-raised">Cancel</button>';
            }
            
            $row[] = $datarow->companyname;
            $row[] = $datarow->policyno;
            $row[] = $datarow->agentname;
            $row[] = $datarow->claimnumber;            
            $row[] = ($datarow->insuranceclaimdate!="0000-00-00")?$this->general_model->displaydate($datarow->insuranceclaimdate):"-";    
            $row[] = $claimstatus;
            $row[] = numberFormat($datarow->claimamount,2,',');
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
			$row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_insurance_claim->count_all(),
                        "recordsFiltered" => $this->Vehicle_insurance_claim->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function vehicleassignedsitelisting() { 
		
		$this->load->model("Vehicle_assigned_site_model","Vehicle_assigned_site");
        $list = $this->Vehicle_assigned_site->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            
            $row[] = ($datarow->date!="0000-00-00")?$this->general_model->displaydate($datarow->date):"-";    
            $row[] = $datarow->sitename;
            $row[] = $datarow->sitecity;
            $row[] = $datarow->siteprovince;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_assigned_site->count_all(),
                        "recordsFiltered" => $this->Vehicle_assigned_site->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function vehicleassignedpartylisting() { 
		
		$this->load->model("Vehicle_assigned_party_model","Vehicle_assigned_party");
        $list = $this->Vehicle_assigned_party->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            
            $row[] = ($datarow->date!="0000-00-00")?$this->general_model->displaydate($datarow->date):"-";    
            $row[] = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->partyid.'" target="_blank">'.$datarow->partyname.'</a>';
            $row[] = $datarow->sitecity;
            $row[] = $datarow->siteprovince;
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Vehicle_assigned_party->count_all(),
                        "recordsFiltered" => $this->Vehicle_assigned_party->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function exportToExcelVehicle(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vehicle','Export to excel Vehicle.');
        }
        $exportdata = $this->Vehicle->getVehicleDataForExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) { 
            $data[] = array(++$srno,
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->ownername!=''?$row->ownername:'-'),
                            (isset($this->Licencetype[$row->vehicletype])?$this->Licencetype[$row->vehicletype]:'-'),
                            ($row->companyname!=''?$row->companyname:'-'),
                            ($row->site!=''?$row->site:'-'),
                            ($row->engineno!=''?$row->engineno:'-'),
                            ($row->chassisno!=''?$row->chassisno:'-'),
                            ($row->dateofregistration!='0000-00-00'?$this->general_model->displaydate($row->dateofregistration):'-'),
                            ($row->duedateofregistration!='0000-00-00'?$this->general_model->displaydate($row->duedateofregistration):'-'),
                            ($row->commercial!='0'?"Commercial":'Non-Commercial'),
                            ($row->buyername!=''?$row->buyername:'-'),
                            (isset($this->Fueltype[$row->fueltype])?$this->Fueltype[$row->fueltype]:'-'),
                            ($row->startingkm!=''?$row->startingkm:'-'),
                            ($row->petrocardno!=''?$row->petrocardno:'-'),
                            ($row->fuelrate>0?numberFormat($row->fuelrate,2,','):'-'),
                            ($row->fuelratetype!='1'?'Hour':'KM'),
                            ($row->sold!='0'?"Sold":'Not Sold'),
                            ($row->solddate!='0000-00-00'?$this->general_model->displaydate($row->solddate):'-'),
                            ($row->soldpartyname!=''?$row->soldpartyname:'-'),
                            ($row->remarks!=''?$row->remarks:'-'),
                            ($row->status!='0'?"Active":'InActive'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                        );
        }  
        $headings = array('Sr. No.','Vehicle Name','Owner Name','Vehicle Type','Company Name','Site Name','Engine Number','Chassis Number','Date Of Registration','Due Date Of Registration','Commercial','Buyer Name','Fuel Type','Starting Km','Petro Card','Fuel Rate Type','Fuel Rate','Sold','Sold Date','Sold Party Name','Remarks','Status','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:W1","Vehicle",$headings,"Vehicle.xls");
    }
    public function exportToPDFVehicle(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vehicle','Export to PDF Vehicle.');
        }

        $PostData['reportdata'] = $this->Vehicle->getVehicleDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Vehicle';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'vehicle/VehicleforPDFFormate', $PostData,true);

        $this->general_model->exportToPDF("Vehicle.pdf",$header,$html);
    }
    public function printVehicleDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Vehicle','Print Vehicle.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Vehicle->getVehicleDataForExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Vehicle';
        
        $html['content'] = $this->load->view(ADMINFOLDER."vehicle/PrintVehicleFormate.php",$PostData,true);
        echo json_encode($html); 
    }
}
?>