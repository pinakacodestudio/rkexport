<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Courier_company extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Courier_company');
        $this->load->model('Courier_company_model', 'Courier_company');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Courier Company";
        $this->viewData['module'] = "courier_company/Courier_company";
        $this->viewData['couriercompanylist'] = $this->Courier_company->getActiveCouriercompanyList();

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Courier Company','View courier company.');
        }

        $this->admin_headerlib->add_javascript("Courier_company", "pages/courier_company.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }
    public function listing() {
		
		$list = $this->Courier_company->get_datatables();
		$data = array();
        $counter = $_POST['start'];
        
        $this->load->model("Channel_model","Channel");
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel'); 
		foreach ($list as $datarow) {
			$row = array();
			$Action = $Checkbox = $membername = $channelname = "";
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'courier-company/courier-company-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'courier-company/courier-company-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'courier-company/courier-company-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Extra&nbsp;Charges","'.ADMIN_URL.'courier-company/delete-mul-courier-company") >'.delete_text.'</a>';
                
                $Checkbox .=  '<div class="checkbox">
                  <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                  <label for="deletecheck'.$datarow->id.'"></label>
                </div>';
            }

            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channelname = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '.$channeldata[$key]['name'];
                }
            }else{
                $channelname = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            if($datarow->memberid != 0){
                $link = ADMIN_URL.'member/member-detail/'.$datarow->memberid;
                $membername = '<a href="'.$link.'" target="_blank" title="'.$datarow->membername.'">'.ucwords($datarow->membername)."</a>";
            }else{
                $membername = '-';
            }
            
            $row[] = ++$counter;
            $row[] = $channelname;
            $row[] = $membername;
            $row[] = ucwords($datarow->companyname);
            $row[] = ucwords($datarow->contactperson);
			$row[] = $datarow->email;
            $row[] = $datarow->mobileno;
            $row[] = $datarow->address;
            $row[] = $Action;
            $row[] = $Checkbox;

			$data[] = $row;
        }
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Courier_company->count_all(),
						"recordsFiltered" => $this->Courier_company->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
    public function courier_company_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Courier Company";
        $this->viewData['module'] = "courier_company/Add_courier_company";
        
        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel'); 

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("Courier_company", "pages/add_courier_company.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function add_courier_company() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->Courier_company->_where = ("email='" . trim($PostData['email']) . "'");
        $Count = $this->Courier_company->CountRecords();

        if ($Count == 0) {
            $this->Courier_company->_where = ("mobileno='" . trim($PostData['mobileno']) . "'");
            $Count = $this->Courier_company->CountRecords();

            if ($Count == 0) {
                $insertdata = array(
                    "channelid" => $PostData['channelid'],
                    "memberid" => $PostData['memberid'],
                    "companyname" => $PostData['companyname'],
                    "contactperson" => $PostData['contactperson'],
                    "email" => $PostData['email'],
                    "mobileno" => $PostData['mobileno'],
                    "address" => $PostData['address'],
                    "cityid" => $PostData['cityid'],
                    "trackurl" => urlencode($PostData['trackurl']),
                    "type" => 0,
                    "status" => $PostData['status'],
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Courier_company->Add($insertdata);
                if ($Add) {
                    if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Courier Company','Add new '.$PostData['companyname'].' courier company.');
                    }
                    echo 1;
                } else {
                    echo 0;
                }
            }else{
                echo 3;
            }
        } else {
            echo 2;
        }
    }

    public function courier_company_edit($Couriercompanyid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Courier Company";
        $this->viewData['module'] = "courier_company/Add_courier_company";
        $this->viewData['action'] = "1"; //Edit

        $this->load->model("Channel_model","Channel");
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel'); 

        $this->Courier_company->_where = array('id' => $Couriercompanyid);
        $this->viewData['couriercompanydata'] = $this->Courier_company->getRecordsByID();

        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_bottom_javascripts("Courier_company", "pages/add_courier_company.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_courier_company() {

        $PostData = $this->input->post();

        $CouriercompanyID = $PostData['couriercompanyid'];
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->Courier_company->_where = ("id!=" . $CouriercompanyID . " AND email='" . trim($PostData['email']) . "'");
        $Count = $this->Courier_company->CountRecords();

        if ($Count == 0) {

            $this->Courier_company->_where = ("id!=" . $CouriercompanyID . " AND mobileno='" . trim($PostData['mobileno']) . "'");
            $Count = $this->Courier_company->CountRecords();

            if ($Count == 0) {
                $updatedata = array(
                    "channelid" => $PostData['channelid'],
                    "memberid" => $PostData['memberid'],
                    "companyname" => $PostData['companyname'],
                    "contactperson" => $PostData['contactperson'],
                    "email" => $PostData['email'],
                    "mobileno" => $PostData['mobileno'],
                    "address" => $PostData['address'],
                    "cityid" => $PostData['cityid'],
                    "trackurl" => urlencode($PostData['trackurl']),
                    "status" => $PostData['status'],
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );
                $this->Courier_company->_where = array('id' => $CouriercompanyID);
                $this->Courier_company->Edit($updatedata);

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(2,'Courier Company','Edit '.$PostData['companyname'].' courier company.');
                }
                echo 1;
            }else{
                echo 3;
            }
            
        } else {
            echo 2;
        }
    }

    public function courier_company_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Courier_company->_where = array("id" => $PostData['id']);
        $this->Courier_company->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Courier_company->_where = array("id"=>$PostData['id']);
            $data = $this->Courier_company->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['companyname'].' courier company.';
            $this->general_model->addActionLog(2,'Courier Company', $msg);
        }
        echo $PostData['id'];
    }

    public function delete_mul_courier_company() {
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
           
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Courier_company->_where = array("id"=>$row);
                $Couriercompanydata = $this->Courier_company->getRecordsById();
            
                $this->general_model->addActionLog(3,'Courier Company','Delete '.$Couriercompanydata['companyname'].' courier company.');
            }
            $this->Courier_company->Delete(array("id"=>$row));    
        }
    }

}
?>