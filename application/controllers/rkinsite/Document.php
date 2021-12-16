<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Document');
        $this->load->model('Document_model','Document');
        $this->load->model('Vehicle_model','Vehicle');
	}
	public function index() {
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Document";
        $this->viewData['module'] = "document/Document";

        $this->load->model('Document_type_model','Document_type');
        $this->viewData['documenttypedata'] = $this->Document_type->getActiveDocumentType();
        
        $this->viewData['vehicledata'] = $this->Vehicle->getVehicle();

        $this->load->model('Party_model','Party');
        $this->viewData['partydata'] = $this->Party->getActiveParty();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Document','View documents.');
		}
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");    
		$this->admin_headerlib->add_javascript("document","pages/document.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
	
    public function listing() { 
        
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $list = $this->Document->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
       
        foreach ($list as $datarow) {         
            $row = array();
            $Action = $checkbox = '';
 
            if(in_array($rollid, $edit)) {
                $Action .= '<a class="'.edit_class.'" href="javascript:void(0)" onclick="openDocumentModal('.$datarow->referencetype.','.$datarow->referenceid.','.$datarow->id.')" title='.edit_title.'>'.edit_text.'</a>';
            } 
            if(in_array($rollid, $delete)) {
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'document/check-document-use","Document","'.ADMIN_URL.'document/delete-mul-document") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }
            if($datarow->documentfile!="" && file_exists(DOCUMENT_PATH.$datarow->documentfile)){
                $Action .= '<a class="'.download_class.'" href="'.DOCUMENT.$datarow->documentfile.'" title="'.download_title.'" download>'.download_text.'</a>';
                $Action .= '<a class="'.viewdoc_class.'" href="'.DOCUMENT.$datarow->documentfile.'" title="'.viewdoc_title.'" target="_blank">'.viewdoc_text.'</a>';
            }

            $partyname = $vehiclename = "-";
            if($datarow->partyname!=""){
                $partyname = '<a href="'.ADMIN_URL.'party/view-party/'.$datarow->partyid.'#documentdetails" target="_blank">'.$datarow->partyname.'</a>';
            }
            if($datarow->vehiclename!=""){
                $vehiclename = '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$datarow->vehicleid.'#documenttab" target="_blank">'.$datarow->vehiclename.'</a>';
            }
            $row[] = ++$counter;
            $row[] = $partyname;
            $row[] = $vehiclename;
            $row[] = $datarow->documenttype;
            $row[] = $datarow->documentnumber;
            $row[] = ($datarow->fromdate!="0000-00-00")?$this->general_model->displaydate($datarow->fromdate):"-";  
            $row[] = ($datarow->duedate!="0000-00-00")?$this->general_model->displaydate($datarow->duedate):"-";  
            $row[] = ($datarow->createddate!="0000-00-00")?$this->general_model->displaydatetime($datarow->createddate):"-";  
            $row[] = $Action;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Document->count_all(),
                        "recordsFiltered" => $this->Document->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    
    public function document_add() {
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $referencetype = $PostData['referencetype'];
        $referenceid = $PostData['referenceid'];
        if(empty($referenceid)){
            if($referencetype==0){
                $referenceid = (!empty($PostData['newvehicleid']))?$PostData['newvehicleid']:0;
            }else{
                $referenceid = (!empty($PostData['newpartyid']))?$PostData['newpartyid']:0;
            }
        }

        $documenttypeid = $PostData['documenttype'];
        $documentnumber = $PostData['documentnumber'];
        $fromdate = ($PostData['fromdate']!="")?$this->general_model->convertdate($PostData['fromdate']):"";
        $duedate = ($PostData['duedate']!="")?$this->general_model->convertdate($PostData['duedate']):"";
        $licencetype = $PostData['licencetype'];

        $this->Document->_where = array("referencetype"=>$referencetype,"referenceid"=>$referenceid,"documenttypeid" => $documenttypeid,"documentnumber" => $documentnumber);
        $Count = $this->Document->CountRecords();
        
        if($Count==0){
           
            $documentfile = "";
            if(!empty($_FILES['documentattachment']['name'])){
                if($_FILES['documentattachment']['size'] != '' && $_FILES['documentattachment']['size'] >= UPLOAD_MAX_FILE_SIZE){
                    echo -1;exit;
                }
                $documentfile = uploadFile('documentattachment','DOCUMENT',DOCUMENT_PATH,'*','',1,DOCUMENT_PATH,DOCUMENT_LOCAL_PATH);
                if($documentfile!==0){
                    if($documentfile==2){
                        echo 4; exit;
                    }
                }else{
                    echo 3; exit;
                }
            }

            $insertdata = array("referencetype"=>$referencetype,
                                "referenceid"=>$referenceid,
                                "documenttypeid"=>$documenttypeid,
                                "documentnumber"=>$documentnumber,
                                "fromdate"=>$fromdate,
                                "duedate"=>$duedate,
                                "licencetype"=>$licencetype,
                                "documentfile"=>$documentfile,
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $insertdata = array_map('trim',$insertdata);
            $documentid = $this->Document->Add($insertdata);
            if($documentid){
                if($referencetype==0){
                    $this->load->model("Vehicle_model","Vehicle");
                    $this->Vehicle->updateVehicleRegDateAndRCBookDateByAppliedOnFitnessDocument($referenceid,$documentid);
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Document','Add new '.$documentnumber.' document.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }  
    
    public function update_document() {

        $PostData = $this->input->post();
       
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        
        $documentid = $PostData['documentid'];
        $referencetype = $PostData['referencetype'];
        $referenceid = $PostData['referenceid'];
        $documenttypeid = $PostData['documenttype'];
        $documentnumber = $PostData['documentnumber'];
        $fromdate = ($PostData['fromdate']!="")?$this->general_model->convertdate($PostData['fromdate']):"";
        $duedate = ($PostData['duedate']!="")?$this->general_model->convertdate($PostData['duedate']):"";
        $olddocumentattachment = $PostData['olddocumentattachment'];
        $licencetype = $PostData['licencetype'];
        
        $this->Document->_where = array("id<>"=>$documentid,"referencetype"=>$referencetype,"referenceid"=>$referenceid,"documenttypeid" => $documenttypeid,"documentnumber" => $documentnumber);
        $Count = $this->Document->CountRecords();
        
        if($Count==0){
            
            $documentfile = "";
            if($_FILES['documentattachment']['name']!="" && $olddocumentattachment==""){
                if($_FILES['documentattachment']['size'] != '' && $_FILES['documentattachment']['size'] >= UPLOAD_MAX_FILE_SIZE){
                    echo -1;exit;
                }
                $documentfile = uploadFile('documentattachment','DOCUMENT',DOCUMENT_PATH,'*','',1,DOCUMENT_LOCAL_PATH);
                if($documentfile!==0){
                    if($documentfile==2){
                        echo 4; exit;
                    }
                }else{
                    echo 3; exit;
                }
            }else if($_FILES['documentattachment']['name']!="" && $olddocumentattachment!=""){
                if($_FILES['documentattachment']['size'] != '' && $_FILES['documentattachment']['size'] >= UPLOAD_MAX_FILE_SIZE){
                    echo -1;exit;
                }
                $documentfile = reuploadFile('documentattachment','DOCUMENT',$olddocumentattachment,DOCUMENT_PATH,'*','',1,DOCUMENT_LOCAL_PATH);
                if($documentfile!==0){
                    if($documentfile==2){
                        echo 4; exit;
                    }
                }else{
                    echo 3; exit;
                }
            }else if($_FILES['documentattachment']['name']=="" && $olddocumentattachment!=""){
                $documentfile = $olddocumentattachment;
            }

            $updatedata = array("documenttypeid"=>$documenttypeid,
                                "documentnumber"=>$documentnumber,
                                "fromdate"=>$fromdate,
                                "duedate"=>$duedate,
                                "licencetype"=>$licencetype,
                                "documentfile"=>$documentfile,
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby
                            );
            $this->Document->_where = array("id"=>$documentid);
            $Edit = $this->Document->Edit($updatedata);
            if($Edit){
                if($referencetype==0){
                    $this->load->model("Vehicle_model","Vehicle");
                    $this->Vehicle->updateVehicleRegDateAndRCBookDateByAppliedOnFitnessDocument($referenceid,$documentid);
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Document','Edit '.$documentnumber.' document.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }

    public function check_document_use(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        // use for check data available or not in other table
        foreach($ids as $row){
            /* $query = $this->db->query("SELECT id FROM ".tbl_documenttype." WHERE 
                    id IN (SELECT vehicleid FROM ".tbl_insurance." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehiclepollutioncertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicleregistrationcertificate." WHERE vehicleid = $row) OR id IN (SELECT vehicleid FROM ".tbl_vehicletax." WHERE vehicleid = $row) ");
                    //OR id IN (SELECT vehicleid FROM ".tbl_vehicleroute." WHERE vehicleid = $row)
            if($query->num_rows() > 0){
                $count++;
            } */
        }
        echo $count;
    }

    public function delete_mul_document(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){

            $this->Document->_where = array("id"=>$row);
            $data = $this->Document->getRecordsById();

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(3,'Document','Delete '.$data['documentnumber'].' document.');
            }
            if($data['documentfile']!=""){
                unlinkfile("DOCUMENT",$data['documentfile'],DOCUMENT_PATH);
            }
            $this->Document->Delete(array("id"=>$row));
        }
    }

    public function getDocumentByID(){
        $PostData = $this->input->post();
        $documentid = $PostData['documentid'];
        
        $documentdata = $this->Document->getdDocumentDataByID($documentid);
        if(!empty($documentdata)){ 
            $documentdata['fromdate'] = ($documentdata['fromdate']!="0000-00-00")?$this->general_model->displaydate($documentdata['fromdate']):"";
            $documentdata['duedate'] = ($documentdata['duedate']!="0000-00-00")?$this->general_model->displaydate($documentdata['duedate']):"";
        }
        echo json_encode($documentdata);
    }

    public function exportToExcelDocument(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Document','Export to excel Document.');
        }
        $exportdata = $this->Document->getDocumentDataforExport();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) { 
            $data[] = array(++$srno,
                            ($row->partyname!=''?$row->partyname:'-'),
                            ($row->vehiclename!=''?$row->vehiclename:'-'),
                            ($row->documenttype!=''?$row->documenttype:'-'),
                            ($row->documentnumber!=''?$row->documentnumber:'-'),
                            ($row->fromdate!='0000-00-00'?$this->general_model->displaydate($row->fromdate):'-'),
                            ($row->duedate!='0000-00-00'?$this->general_model->displaydate($row->duedate):'-'),
                            ($row->licencetype!=''?$this->Licencetype[$row->licencetype]:'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-')
                        );
        }  
        $headings = array('Sr. No.','Party Name','Vehicle Name','Document Type','Document Number','Register Date','Due Date','Licence Type','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:AA1","Document",$headings,"Document.xls");
    }

    public function exportToPDFDocument(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Document','Export to PDF Document.');
        }

        $PostData['reportdata'] = $this->Document->getDocumentDataforExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Document';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'document/DocumentforPDF', $PostData,true);

        $this->general_model->exportToPDF("Document.pdf",$header,$html);
    }

    public function printDocumentDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Document Details','Print Document Details.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Document->getDocumentDataforExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Document';
        
        $html['content'] = $this->load->view(ADMINFOLDER."document/PrintDocumentDetailFormate.php",$PostData,true);
        echo json_encode($html); 
    }
	
}
?>