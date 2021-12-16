<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Site extends Admin_Controller{

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu','Site');
        $this->load->model('Site_model','Site');
    }
    public function index() {

        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Site";
        $this->viewData['module'] = "site/Site";    

        $this->viewData['sitedata'] = $this->Site->getSiteData();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Site','View site.');
        }
        
        $this->admin_headerlib->add_javascript("site","pages/site.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function add_site() {
        
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Site";
        $this->viewData['module'] = "site/Add_site";
        
        $this->load->model('Party_model','Party');
        $this->viewData['partydata']=$this->Party->getActiveParty();

        $this->load->model('Country_model','Country');
        $this->viewData['countrydata']=$this->Country->getCountry();
        
        $this->admin_headerlib->add_bottom_javascripts("site","pages/add_site.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function edit_site($siteid) {
        
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Site";
        $this->viewData['module'] = "site/Add_site";
        $this->viewData['action'] = "1";//Edit

		//Get Section data by id
		$this->viewData['sitedata'] = $this->Site->getSiteDataByID($siteid);
        if(empty($this->viewData['sitedata'])){
			redirect(ADMINFOLDER."pagenotfound");
		}
        $this->load->model('Party_model','Party');
        $this->viewData['partydata']=$this->Party->getActiveParty();

        $this->load->model('Country_model', 'Country');
        $this->viewData['countrydata']=$this->Country->getCountry();

        $this->admin_headerlib->add_bottom_javascripts("site","pages/add_site.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

    public function site_add(){
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        $sitename = trim($PostData['sitename']);
        $address = trim($PostData['address']);
        $petrocardno = $PostData['petrocardno'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $status = $PostData['status'];
        $sitemanageridarray = $PostData['sitemanagerid'];

        $this->Site->_where = array("sitename"=>$sitename);
        $Count = $this->Site->CountRecords();

        if($Count==0){

            $insertdata = array("sitename"=>$sitename,
                                "address"=>$address,
                                "petrocardno"=>$petrocardno,
                                "provinceid"=>$provinceid,
                                "cityid"=>$cityid,
                                "status"=>$status,
                                "createddate"=>$createddate,
                                "modifieddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifiedby"=>$addedby
                            );

            $insertdata=array_map('trim',$insertdata);
            $SiteId = $this->Site->Add($insertdata);
            if($SiteId){
                $insertMappingData=array();
                if(!empty($sitemanageridarray)){
                    foreach($sitemanageridarray as $sitemanagerid){
                        
                        $insertMappingData[] = array("siteid"=>$SiteId,"partyid"=>$sitemanagerid);
                    }
                }
                if(!empty($insertMappingData)){
                    $this->Site->_table = tbl_sitemapping;
                    $this->Site->add_batch($insertMappingData);
                }
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Site','Add new '.$PostData['sitename'].' site.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }            

    public function update_site() {

		$PostData = $this->input->post();
			
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
        
        $siteid = $PostData['siteid'];
		$sitename = trim($PostData['sitename']);
        $address = trim($PostData['address']);
        $petrocardno = $PostData['petrocardno'];
        $provinceid = $PostData['provinceid'];
        $cityid = $PostData['cityid'];
        $status = $PostData['status'];
        $sitemanageridarray = $PostData['sitemanagerid'];

        $this->Site->_where = array("id<>"=>$siteid,"sitename"=>$sitename);
        $Count = $this->Site->CountRecords();
            
        if($Count==0){

            $updatedata = array("sitename"=>$sitename,
                                "address"=>$address,
                                "petrocardno"=>$petrocardno,
                                "provinceid"=>$provinceid,
                                "cityid"=>$cityid,
                                "status"=>$status,
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby
                            );

            $this->Site->_where = array("id"=>$siteid);
            $Edit = $this->Site->Edit($updatedata);
            if($Edit){

                $insertMappingData=array();
                if(!empty($sitemanageridarray)){
                    $this->Site->_table = tbl_sitemapping;
                    foreach($sitemanageridarray as $sitemanagerid){
                        
                        $this->Site->_where = array("siteid"=>$siteid,"partyid"=>$sitemanagerid);
                        $Count = $this->Site->CountRecords();
                        if($Count==0){
                            $insertMappingData[] = array("siteid"=>$siteid,"partyid"=>$sitemanagerid);
                        }
                    }
                }
                $this->Site->_where = array("siteid"=>$siteid);
                $MappingData = $this->Site->getRecordById();
                $SiteManagerIds = !empty($MappingData)?array_column($MappingData, "partyid"):array();
                if(!empty($SiteManagerIds)){
                    $deleteMappingData = array_diff($SiteManagerIds,$sitemanageridarray);
                    if(!empty($deleteMappingData)){
                        $this->Site->Delete(array("siteid"=>$siteid,"partyid IN (".implode(",",$deleteMappingData).")"=>null));
                    }
                }

                if(!empty($insertMappingData)){
                    $this->Site->add_batch($insertMappingData);
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Site','Edit '.$PostData['sitename'].' site.');
                }
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }
    }
    
    public function site_enable_disable() {

        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$updatedata = array("status"=>$PostData['val'],"modifieddate"=>$modifieddate,"modifiedby"=>$this->session->userdata(base_url().'ADMINID'));
		$this->Site->_where = array("id"=>$PostData['id']);
        $this->Site->Edit($updatedata);
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Site->_where = array("id"=>$PostData['id']);
            $data = $this->Site->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['sitename'].' site.';
            
            $this->general_model->addActionLog(2,'Site', $msg);
        }
		echo $PostData['id'];
    }
    
    public function check_site_use(){
        $PostData = $this->input->post();
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		// use for check document type available or not in other table
		foreach($ids as $row){
		    $this->readdb->select('siteid');
            $this->readdb->from(tbl_fuel);
            $where = array("siteid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
            $this->readdb->select('siteid');
            $this->readdb->from(tbl_assignvehicle);
            $where = array("siteid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
              $count++;
            }
		}
		echo $count;
	}

	public function delete_mul_site(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$this->Site->_where = array("id"=>$PostData['id']);
		$ids = explode(",",$PostData['ids']);
		$count = 0;
		foreach($ids as $row){

            $checkuse = 0;
            $this->readdb->select('siteid');
            $this->readdb->from(tbl_fuel);
            $where = array("siteid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
            $this->readdb->select('siteid');
            $this->readdb->from(tbl_assignvehicle);
            $where = array("siteid"=>$row);
            $this->readdb->where($where);
            $query = $this->readdb->get();
            if($query->num_rows() > 0){
                $checkuse++;
            }
            if($checkuse == 0){

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->Site->_table = tbl_site;
                    $this->Site->_where = array("id"=>$row);
                    $data = $this->Site->getRecordsById();
                    $this->general_model->addActionLog(3,'Site','Delete '.$data['sitename'].' site.');
                }
                $this->Site->_table = tbl_sitemapping;
                $this->Site->Delete(array("siteid"=>$row));

                $this->Site->_table = tbl_site;
                $this->Site->Delete(array("id"=>$row));
            }
		}
	}
    
    public function exportToExcelSite(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Site','Export to excel Site.');
        }
        $exportdata = $this->Site->getSiteforExport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) { 
            $sitemanager = array();
            $sitemanagernamearray = explode(",", $row->sitemanagername);
            foreach($sitemanagernamearray as $key=>$sitemanagerid){
            $sitemanager[] = $sitemanagernamearray[$key]; 
      }
            
            $data[] = array(++$srno,
                            ($row->sitename!=''?$row->sitename:'-'),
                            implode(", ",$sitemanager), 
                            ($row->address!=''?$row->address:'-'),
                            ($row->cityname!=''?$row->cityname:'-'),
                            ($row->createddate!='0000-00-00'?$this->general_model->displaydatetime($row->createddate):'-'),
                        );
        }  
        $headings = array('Sr. No.','Site Name','Site Manager','Address','City','Entry Date'); 
        $this->general_model->exporttoexcel($data,"A1:N1","Site",$headings,"Site.xls");
    }

    public function exportToPDFSite(){
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Site','Export to PDF Site.');
        }

        $PostData['reportdata'] = $this->Site->getSiteforExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Site';

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'site/Sitereportforpdf', $PostData,true);

        $this->general_model->exportToPDF("Site.pdf",$header,$html);
    }

    public function printSiteDetails(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Site','Print Site.');
        }

        $PostData = $this->input->post();
        
        $PostData['reportdata'] = $this->Site->getSiteforExport();
        $this->load->model('Settings_model', 'Setting');
        $PostData['invoicesettingdata'] = $this->Setting->getsetting();
        $PostData['heading'] = 'Site';
        
        $html['content'] = $this->load->view(ADMINFOLDER."site/PrintSiteDetails.php",$PostData,true);
        echo json_encode($html); 
    }
}
?>