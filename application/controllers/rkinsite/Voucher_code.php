<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher_code extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Voucher_code');
        $this->load->model('Voucher_code_model', 'Voucher_code');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Coupon Code";
        $this->viewData['module'] = "voucher_code/Voucher_code";
        //$this->viewData['vouchercodedata'] = $this->Vouchercode->get_all_listdata();

        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $sessionarr = (isset($this->session->userdata('SESSION_FILTERS')['Voucher_code']))?$this->session->userdata('SESSION_FILTERS')['Voucher_code']:"";

        if(!is_null($sessionarr) && !empty($sessionarr)){

            $this->viewData['panelcollapsed'] = (isset($sessionarr['panelcollapsed']))?$sessionarr['panelcollapsed']:"0";
            $this->viewData['ChannelId'] = (isset($sessionarr['channelid']))?$sessionarr['channelid']:"";
            $this->viewData['MemberId'] = (isset($sessionarr['memberid']))?$sessionarr['memberid']:"";
        }
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Coupon Code','View coupon code.');
        }
        $this->admin_headerlib->add_javascript("voucher_code", "pages/voucher_code.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {
        
        $this->general_model->saveModuleWiseFiltersOnSession('Voucher_code');
        $list = $this->Voucher_code->get_datatables();

        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $data = array();
        $counter = $srno = $_POST['start'];
        foreach ($list as $Vouchercode) {
            $channellabel = '';
            $row = array();
            
            $row[] = ++$counter;
            
            if($Vouchercode->channelid != 0){
                $channelnamearr = array();  
                $channelidarr = (!empty($Vouchercode->channelid))?explode(",", $Vouchercode->channelid):'';
                foreach($channelidarr as $channelid){
                
                    $key = array_search($channelid, array_column($channeldata, 'id'));
                    if(!empty($channeldata) && isset($channeldata[$key])){
                        $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].';margin-bottom:5px;">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                    }
                    $channelnamearr[] = $channellabel.$channeldata[$key]['name'];
                }
                $row[] = implode(" | ", $channelnamearr);
                $row[] = ucwords($Vouchercode->membername);
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
                $row[] = "-";
            }
            
            
            $row[] = ucfirst($Vouchercode->name);
            $minbill = '-';
            if($Vouchercode->minbillamount>0){
                $minbill = number_format($Vouchercode->minbillamount,2,'.',',');
            }
            $row[] = $Vouchercode->vouchercode."<br><br><b>Min. Bill : </b>".$minbill;
            
            if($Vouchercode->discounttype==1){
                $discountvalue = "<span class='pull-right'>".number_format($Vouchercode->discountvalue,2)."%</span>";
            }else{
                $discountvalue = "<span class='pull-right'>".'<i class="fa fa-rupee"></i> '.number_format($Vouchercode->discountvalue,2,'.',',')."</span>";
            }
            $row[] = $discountvalue;
           
            $row[] = "<span class='pull-right'>".$Vouchercode->usestatus."</span>";
            $startdate = ($Vouchercode->startdate!='0000-00-00')?$this->general_model->displaydate($Vouchercode->startdate):'';
            $enddate = ($Vouchercode->enddate!='0000-00-00')?$this->general_model->displaydate($Vouchercode->enddate):'';
            $row[] = $startdate." - ".$enddate;
            $row[] = $this->general_model->displaydatetime($Vouchercode->createddate);

            $Action='';
             if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                    $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'voucher-code/voucher-code-edit/'.$Vouchercode->id.'" title='.edit_title.'>'.edit_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($Vouchercode->status==1){
                    $Action .= '<span id="span'.$Vouchercode->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Vouchercode->id.',\''.ADMIN_URL.'voucher-code/voucher-code-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Vouchercode->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Vouchercode->id.',\''.ADMIN_URL.'voucher-code/voucher-code-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
            
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a href="javascript:void(0)" onclick="deleterow('.$Vouchercode->id.',\'\',\'Voucher Code\',\''.ADMIN_URL.'voucher-code/delete-mul-voucher-code\',\'vouchercodetable\')" class="'.delete_class.'" title="'.delete_title.'">'.stripslashes(delete_text).'</a>';
            }

            $row[] = $Action;

            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $row[] = '<div class="checkbox">
                                <input id="deletecheck'.$Vouchercode->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Vouchercode->id.'" name="deletecheck'.$Vouchercode->id.'" class="checkradios">
                                <label for="deletecheck'.$Vouchercode->id.'"></label>
                              </div>';
             }else{
                $row[] = "";                
             }

            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Voucher_code->count_all(),
                        "recordsFiltered" => $this->Voucher_code->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }

    public function voucher_code_add() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Coupon Code";
        $this->viewData['module'] = "voucher_code/Add_voucher_code";

        duplicate : $code = generate_token(10);

        $this->Voucher_code->_table = tbl_voucher;
        $this->Voucher_code->_where = "vouchercode='".$code."'";
        $Count = $this->Voucher_code->CountRecords();

        if($Count==0){

            $this->viewData['vouchercode'] = $code;

            //Get Channel List
            $this->load->model("Channel_model","Channel"); 
            $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

            $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
            $this->admin_headerlib->add_bottom_javascripts("Voucher_code", "pages/add_voucher_code.js");
            
            $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
            $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
            $this->load->view(ADMINFOLDER . 'template', $this->viewData);
        }else{
            goto duplicate;
        }
    }

    public function add_voucher_code() {
        $PostData = $this->input->post();
        //print_r($PostData);exit;
        $discountvalue = ($PostData['discounttype']==1)?$PostData['percentageval']:$PostData['amount'];
        $noofcustomerused = $PostData['noofcustomerused'];
        $productid = (isset($PostData['productid']) && !empty($PostData['productid']))?(implode(',', $PostData['productid'])):0;
        $startdate = ($PostData['startdate']!='')?$this->general_model->convertdate($PostData['startdate']):'';
        $enddate = ($PostData['enddate']!='')?$this->general_model->convertdate($PostData['enddate']):'';
        $channelid = (!empty($PostData['channelid']))?implode(",", $PostData['channelid']):'';
        $name = $PostData['name'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');
        $this->Voucher_code->_where = array("(FIND_IN_SET('".$channelid."', channelid)>0)"=>null,"(name='".$name."' OR vouchercode='".trim($PostData['vouchercode'])."')"  => null);
        $Count = $this->Voucher_code->CountRecords();

        if($Count==0){
            $insertdata = array(
                "channelid"=>$channelid,
                "name" => $name,
                "discounttype" => $PostData['discounttype'],
                "discountvalue" => $discountvalue,
                "maximumusage" => $PostData['maximumusage'],
                "noofcustomerused" => $noofcustomerused,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "vouchercode" => $PostData['vouchercode'],
                "minbillamount" => $PostData['minbillamount'],
                "status" => $PostData['status'],
                "type" => 0,
                "createddate" => $createddate,
                "addedby" => $addedby,
            );
            $insertdata = array_map('trim', $insertdata);
            $VoucherID = $this->Voucher_code->Add($insertdata);
            if ($VoucherID) {
                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $this->general_model->addActionLog(1,'Coupon Code','Add new '.$name.' coupon code.');
                }
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }   
    }

    public function voucher_code_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Voucher_code->_table = tbl_voucher;
        $this->Voucher_code->_where = array("id" => $PostData['id']);
        $this->Voucher_code->Edit($updatedata);

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Voucher_code->_where = array("id"=>$PostData['id']);
            $data = $this->Voucher_code->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' coupon code.';
            $this->general_model->addActionLog(2,'Coupon Code', $msg);
        }
        echo $PostData['id'];
    }

    public function voucher_code_edit($vouchercode) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Coupon Code";
        $this->viewData['module'] = "voucher_code/Add_voucher_code";
        $this->viewData['action'] = "1"; //Edit

        $this->Voucher_code->_where = array('id' => $vouchercode);
        $this->Voucher_code->_fields = "*";
        $this->viewData['vouchercodedata'] = $this->Voucher_code->getRecordsByID();
        // print_r($this->viewData['vouchercodedata']);exit();

         //Get Channel List
         $this->load->model("Channel_model","Channel"); 
         $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        $this->admin_headerlib->add_javascript("Voucher_code", "pages/add_voucher_code.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function update_voucher_code() {

        $PostData = $this->input->post();

        $voucher_id = $PostData['voucherid'];
        $discount_value = ($PostData['discounttype']==1)?$PostData['percentageval']:$PostData['amount'];
        $no_of_customer_used = $PostData['noofcustomerused'];
        $startdate = ($PostData['startdate']!='')?$this->general_model->convertdate($PostData['startdate']):'';
        $enddate = ($PostData['enddate']!='')?$this->general_model->convertdate($PostData['enddate']):'';
        $channelid = (!empty($PostData['channelid']))?implode(",", $PostData['channelid']):'';
        $name = $PostData['name'];

        $created_date = $this->general_model->getCurrentDateTime();
        $created_by = $this->session->userdata(base_url() . 'ADMINID');

        $this->Voucher_code->_where = array("(FIND_IN_SET('".$channelid."', channelid)>0)"=>null,"(name='".$name."' OR vouchercode='".trim($PostData['vouchercode'])."')"  => null,"id!="=>trim($PostData['voucherid']));
        $Count = $this->Voucher_code->CountRecords();
        if($Count==0){
            $updatedata = array(
                "channelid"=>$channelid,
                "name" => $name,
                "discounttype" => $PostData['discounttype'],
                "discountvalue" => $discount_value,
                "maximumusage" => $PostData['maximumusage'],
                "vouchercode" => $PostData['vouchercode'],
                "minbillamount" => $PostData['minbillamount'],
                "status" => $PostData['status'],
                "noofcustomerused" => $no_of_customer_used,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "type" => 0,
                "createddate" => $created_date,
                "addedby" => $created_by
            );
            $this->Voucher_code->_where = array('id' => $voucher_id);
            $edit=$this->Voucher_code->Edit($updatedata);
            
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->general_model->addActionLog(2,'Coupon Code','Edit '.$name.' coupon code.');
            }
            echo 1;
        }else{
            echo 2;
        } 
    }
    public function check_voucher_code_use(){
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $count = 0;
        echo $count;
    }

    public function delete_mul_voucher_code(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        
        foreach($ids as $row){

            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Voucher_code->_where = array("id"=>$row);
                $Vouchercodedata = $this->Voucher_code->getRecordsById();

                $this->general_model->addActionLog(3,'Coupon Code','Delete '.$Vouchercodedata['name'].' coupon code.');
            }
            $this->Voucher_code->Delete(array('id'=>$row));
        }
    }

    public function savecollapse(){
        $PostData = $this->input->post();
        $panelcollapsed = $this->general_model->saveModuleWiseFiltersOnSession('Voucher_code','collapse');
    
        echo $panelcollapsed;
    }
}

?>