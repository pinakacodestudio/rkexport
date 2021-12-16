<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Extra_charges extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Extra_charges');
        $this->load->model('Side_navigation_model');
        $this->load->model('Extra_charges_model', 'Extra_charges');
        $this->load->library("form_validation");
    }
    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Extra Charges";
        $this->viewData['module'] = "extra_charges/Extra_charges";
        
        $this->channel_headerlib->add_javascript("Extra_charges", "pages/extra_charges.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
		
        $list = $this->Extra_charges->get_datatables();
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $datarow) {
			$row = array();
            $Action='';
            $Checkbox='';
            $channellabel="";
            $membername = "";
            $channelname = "";
            
            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channelname = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '.$channeldata[$key]['name'];
                    
                }
            }else{
                $channelname = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($datarow->memberid != 0){
                $membername = ucwords($datarow->membername);
            }else{
                $membername = '-';
            }
            
            if($datarow->type==1 && $MEMBERID==$datarow->memberid){
                
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                    $Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'extra-charges/extra-charges-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
                }
                
                if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                    if($datarow->status==1){
                        $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'extra-charges/extra-charges-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                    else{
                        $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'extra-charges/extra-charges-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                    }
                }
                
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){                
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.CHANNEL_URL.'extra-charges/check-extra-charges-use","Extra&nbsp;Charges","'.CHANNEL_URL.'extra-charges/delete-mul-extra-charges") >'.delete_text.'</a>';
                    
                    $Checkbox .=  '<div class="checkbox">
                    <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                    <label for="deletecheck'.$datarow->id.'"></label>
                    </div>';
                }
            }
            
            $row[] = ++$counter;
            $row[] = $channelname;
            $row[] = $membername;
            $row[] = ucwords($datarow->name);
            $row[] = $datarow->hsncode;
            $row[] = ($datarow->amounttype==1)?"Amount":"Percentage";
            $row[] = number_format($datarow->defaultamount,2,'.',',');
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Extra_charges->count_all(),
						"recordsFiltered" => $this->Extra_charges->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
    }
    
    public function extra_charges_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Extra Charges";
        $this->viewData['module'] = "extra_charges/Add_extra_charges";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model("Hsn_code_model","Hsn_code");
        $this->viewData['hsncodedata'] = $this->Hsn_code->getMemberActiveHSNCode($CHANNELID,$MEMBERID);

        $this->channel_headerlib->add_javascript("extra_charges", "pages/add_extra_charges.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function add_extra_charges() {
        $PostData = $this->input->post();
        
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->form_validation->set_rules('name', 'name', 'required|min_length[2]');
        $this->form_validation->set_rules('defaultamount', 'default amount', 'required');

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{

            $name = trim($PostData['name']);
            $hsncodeid = $PostData['hsncodeid'];
            $amounttype = $PostData['amounttype'];
            $defaultamount = $PostData['defaultamount'];
            $status = $PostData['status'];

            $this->Extra_charges->_where = ("channelid='" .$CHANNELID. "' AND memberid='" .$MEMBERID. "' AND name='" .$name. "'");
            $Count = $this->Extra_charges->CountRecords();

            if ($Count == 0) {
            
                $insertdata = array(
                    "channelid" => $CHANNELID,
                    "memberid" => $MEMBERID,
                    "hsncodeid" => $hsncodeid,
                    "name" => $name,
                    "amounttype" => $amounttype,
                    "defaultamount" => $defaultamount,
                    "type" => 1,
                    "status" => $status,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Extra_charges->Add($insertdata);
                if ($Add) {
                    $json = array('error'=>1); // Extra charges inserted successfully
                } else {
                    $json = array('error'=>0); // Extra charges not inserted 
                }
            } else {
                $json = array('error'=>2); // Extra charges already added
            }
        }
        echo json_encode($json);
    }

    public function extra_charges_edit($id) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Extra Charges";
        $this->viewData['module'] = "extra_charges/Add_extra_charges";
        $this->viewData['action'] = "1"; //Edit

        $this->Extra_charges->_where = array('id' => $id);
        $this->viewData['extrachargesdata'] = $this->Extra_charges->getExtrachargesDataByID($id);
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->load->model("Hsn_code_model","Hsn_code");
        $this->viewData['hsncodedata'] = $this->Hsn_code->getMemberActiveHSNCode($CHANNELID,$MEMBERID);

        $this->channel_headerlib->add_javascript("extra_charges", "pages/add_extra_charges.js");
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
    }

    public function update_extra_charges() {

        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');

        $this->form_validation->set_rules('name', 'name', 'required|min_length[2]');
        $this->form_validation->set_rules('defaultamount', 'default amount', 'required');

        $json = array();
        if ($this->form_validation->run() == FALSE) {
        	$validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
	    }else{
            
            $extrachargesid = $PostData['extrachargesid'];
            $name = trim($PostData['name']);
            $hsncodeid = $PostData['hsncodeid'];
            $amounttype = $PostData['amounttype'];
            $defaultamount = $PostData['defaultamount'];
            $status = $PostData['status'];

            $this->Extra_charges->_where = ("id!='" . $extrachargesid . "' AND channelid='" .$CHANNELID. "' AND memberid='" .$MEMBERID. "' AND name='" .$name. "'");
            $Count = $this->Extra_charges->CountRecords();

            if ($Count == 0) {

                $updatedata = array(
                    "hsncodeid" => $hsncodeid,
                    "name" => $name,
                    "amounttype" => $amounttype,
                    "defaultamount" => $defaultamount,
                    "status" => $status,
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );
                $this->Extra_charges->_where = array('id' => $extrachargesid);
                $Edit = $this->Extra_charges->Edit($updatedata);
                if ($Edit) {
                    $json = array('error'=>1); // Extra charges inserted successfully
                } else {
                    $json = array('error'=>0); // Extra charges not inserted 
                }
            } else {
                $json = array('error'=>2); // Extra charges already added
            }
        }
        echo json_encode($json);
    }

    public function extra_charges_enable_disable() {
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Extra_charges->_where = array("id" => $PostData['id']);
        $this->Extra_charges->Edit($updatedata);

        echo $PostData['id'];
    }
    
    public function check_extra_charges_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("
                SELECT id FROM ".tbl_extrachargemapping." as ecm 
                WHERE ecm.extrachargesid = '".$row."' AND 
                        (CASE
                            WHEN ecm.type = 0 THEN IFNULL((SELECT 1 FROM ".tbl_orders." WHERE id=ecm.referenceid AND status=1 AND approved=1 AND o.isdelete=0),0)=0
                            WHEN ecm.type = 1 THEN IFNULL((SELECT 1 FROM ".tbl_quotation." WHERE id=ecm.referenceid AND status=1 AND o.isdelete=0),0)=0
                            ELSE 0=0
                        END)
                ");
            
            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }

    public function delete_mul_extra_charges(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("
                    SELECT id FROM ".tbl_extrachargemapping." as ecm 
                    WHERE ecm.extrachargesid = '".$row."' AND 
                    (CASE
                        WHEN ecm.type = 0 THEN IFNULL((SELECT 1 FROM ".tbl_orders." WHERE id=ecm.referenceid AND status=1 AND approved=1),0)=0
                        WHEN ecm.type = 1 THEN IFNULL((SELECT 1 FROM ".tbl_quotation." WHERE id=ecm.referenceid AND status=1),0)=0
                        ELSE 0=0
                    END)
            ");

            if($query->num_rows() == 0){
            
                $this->Extra_charges->Delete(array('id'=>$row));
            }
        }
    }
}