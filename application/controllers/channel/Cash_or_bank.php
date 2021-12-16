<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cash_or_bank extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Cash_or_bank');
		$this->load->model('Cash_or_bank_model','Cash_or_bank');
	}
	public function index(){
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Cash or Bank";
		$this->viewData['module'] = "cash_or_bank/Cash_or_bank";

        $this->channel_headerlib->add_javascript("Cash_or_bank", "pages/cash_or_bank.js");		
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
		
        $list = $this->Cash_or_bank->get_datatables();
        
        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $index=>$datarow) {
			$row = array();
            $Action='';
            $Checkbox='';
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'cash-or-bank/cash-or-bank-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                if($datarow->status==1){
                    if(strtolower($datarow->bankname) == "cash" && $index==(count($list)-1)){
                        $Action .= '<span><a href="javascript:void(0)" class="btn btn-default btn-raised btn-sm" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }else{
                        $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'cash-or-bank/cash-or-bank-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                    }
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'cash-or-bank/cash-or-bank-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
			
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){                
                if(strtolower($datarow->bankname) == "cash" && $index==(count($list)-1)){
                    $Action.='<a href="javascript:void(0)" class="btn btn-default btn-raised btn-sm" title="'.delete_title.'">'.stripslashes(delete_text).'</a>';
                }else{
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.CHANNEL_URL.'cash-or-bank/check-cash-or-bank-use","Cash&nbsp;or&nbsp;Bank","'.CHANNEL_URL.'cash-or-bank/delete-mul-cash-or-bank") >'.delete_text.'</a>';
                    
                    $Checkbox .=  '<div class="checkbox">
                      <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                      <label for="deletecheck'.$datarow->id.'"></label>
                    </div>';
                }
            }
            
            $row[] = ++$counter;
            /* $row[] = $membername; */
            $row[] = $datarow->accountno;
            $row[] = ucwords($datarow->bankname);
            $row[] = ($datarow->branchname!="")?ucwords($datarow->branchname):"-";
            $row[] = ($datarow->branchaddress!="")?ucfirst($datarow->branchaddress):"-";
            $row[] = ($datarow->ifsccode!="")?$datarow->ifsccode:"-";
            $row[] = ($datarow->micrcode!="")?$datarow->micrcode:"-";
            $row[] = number_format($datarow->openingbalance,2,'.',',');
            $row[] = $datarow->defaultbank==1?"Yes":'No';
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Cash_or_bank->count_all(),
						"recordsFiltered" => $this->Cash_or_bank->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	public function cash_or_bank_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Cash or Bank";
		$this->viewData['module'] = "cash_or_bank/Add_cash_or_bank";

        $this->channel_headerlib->add_javascript("Add_cash_or_bank", "pages/add_cash_or_bank.js");
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}
	public function cash_or_bank_edit($id) 
	{
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Cash or Bank";
        $this->viewData['module'] = "cash_or_bank/Add_cash_or_bank";
        $this->viewData['action'] = "1"; //Edit
		
        $this->viewData['cashorbankdata'] = $this->Cash_or_bank->getCashOrBankDataById($id);
        
		if(empty($this->viewData['cashorbankdata'])){
			redirect('Pagenotfound');
		}
        
        $this->channel_headerlib->add_javascript("Add_cash_or_bank", "pages/add_cash_or_bank.js");$this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");		
        $this->load->view(CHANNELFOLDER . 'template', $this->viewData);
	}
	public function add_cash_or_bank()
	{
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		
        $this->form_validation->set_rules('accountno','account no.', 'required|min_length[6]',array("required"=>"Please enter account no. !","min_length"=>"Please enter account no. more than 6 digits !"));
        $this->form_validation->set_rules('bankname', 'bank name', 'required|min_length[3]',array("required"=>"Please enter bank name !","min_length"=>"Bank name required minimum 3 characters !"));
        $this->form_validation->set_rules('branchname','branch name', 'min_length[3]',array("min_length"=>"Branch name required minimum 3 characters !"));
        $this->form_validation->set_rules('branchaddress','branch address', 'min_length[3]',array("min_length"=>"Branch address required minimum 3 characters !"));
		
        $json = array();
        if ($this->form_validation->run() == FALSE){
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
        }else{ 

			$accountno = $PostData['accountno'];
            $bankname = $PostData['bankname'];
            $openingbalance = $PostData['openingbalance'];
            $openingbalancedate = ($PostData['openingbalancedate']!="")?$this->general_model->convertdate($PostData['openingbalancedate']):'';
			$branchname = $PostData['branchname'];
            $branchaddress = $PostData['branchaddress'];
            $ifsccode = $PostData['ifsccode'];
            $micrcode = $PostData['micrcode'];
            $defaultbank = $PostData['defaultbank'];
            $status = $PostData['status'];
            
            $this->Cash_or_bank->_where = ("name ='".trim($bankname)."' AND accountno = '".trim($accountno)."' AND memberid=".$MEMBERID);
            $Count = $this->Cash_or_bank->CountRecords();

            if($Count==0){

                $insertdata = array(
                    "memberid" => $MEMBERID,
                    "name" => $bankname,
                    "openingbalance" => $openingbalance,
                    "openingbalancedate" => $openingbalancedate,
					"accountno" => $accountno,
                    "branchname" => $branchname,
                    "branchaddress" => $branchaddress,
                    "ifsccode" => $ifsccode,
                    "micrcode" => $micrcode,
                    "defaultbank" => $defaultbank,
                    "status" => $status,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );

                $insertdata = array_map('trim', $insertdata);
                $CashOrBankId = $this->Cash_or_bank->Add($insertdata);

                if($defaultbank==1){
                    $this->updateDefaultBank($CashOrBankId);
                }
                
                if($CashOrBankId){   
                    $json = array('error'=>1);
                }else{
                    $json = array('error'=>0);
                }
            }else{
                $json = array('error'=>2);
            }
        }
        echo json_encode($json);
	}
	public function update_cash_or_bank()
	{
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        
        $this->form_validation->set_rules('accountno','account no.', 'required|min_length[6]',array("required"=>"Please enter account no. !","min_length"=>"Please enter account no. more than 6 digits !"));
        $this->form_validation->set_rules('bankname', 'bank name', 'required|min_length[3]',array("required"=>"Please enter bank name !","min_length"=>"Bank name required minimum 3 characters !"));
        $this->form_validation->set_rules('branchname','branch name', 'min_length[3]',array("min_length"=>"Branch name required minimum 3 characters !"));
        $this->form_validation->set_rules('branchaddress','branch address', 'min_length[3]',array("min_length"=>"Branch address required minimum 3 characters !"));

        $json = array();
        if ($this->form_validation->run() == FALSE){
            $validationError = implode('<br>', $this->form_validation->error_array());
        	$json = array('error'=>3, 'message'=>$validationError);
        }else{

			$cashorbankid = $PostData['cashorbankid'];

			$accountno = $PostData['accountno'];
            $bankname = $PostData['bankname'];
            $openingbalance = $PostData['openingbalance'];
            $openingbalancedate = ($PostData['openingbalancedate']!="")?$this->general_model->convertdate($PostData['openingbalancedate']):'';
			$branchname = $PostData['branchname'];
            $branchaddress = $PostData['branchaddress'];
			$ifsccode = $PostData['ifsccode'];
            $micrcode = $PostData['micrcode'];
            $defaultbank = $PostData['defaultbank'];
            $status = $PostData['status'];
            
            $this->Cash_or_bank->_where = ("id!='".$cashorbankid."' AND name ='".trim($bankname)."' AND accountno = '".trim($accountno)."' AND memberid=".$MEMBERID);
            $Count = $this->Cash_or_bank->CountRecords();

            if($Count==0){

                $updatedata = array(
                    "memberid" => $MEMBERID,
                    "name" => $bankname,
                    "openingbalance" => $openingbalance,
                    "openingbalancedate" => $openingbalancedate,
					"accountno" => $accountno,
                    "branchname" => $branchname,
                    "branchaddress" => $branchaddress,
                    "ifsccode" => $ifsccode,
                    "micrcode" => $micrcode,
                    "defaultbank" => $defaultbank,
                    "status" => $status,
                    "modifieddate" => $modifieddate,
                    "modifiedby" => $modifiedby
                );

                $updatedata = array_map('trim', $updatedata);
				$this->Cash_or_bank->_where = array("id"=>$cashorbankid);
				$this->Cash_or_bank->Edit($updatedata);
                
                if($cashorbankid){
                    $json = array('error'=>1);

                    if($defaultbank==1){
                        $this->updateDefaultBank($cashorbankid);
                    }

                }else{
                    $json = array('error'=>0);
                }
            }else{
                $json = array('error'=>2);
            }
        }
        echo json_encode($json);
	}
    public function check_cash_or_bank_use()
	{
        $this->checkAdminAccessModule('submenu','delete',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row)
        {
            $query = $this->db->query("SELECT id FROM ".tbl_cashorbank." WHERE 
                    id IN (SELECT cashorbankid FROM ".tbl_paymentreceipt." WHERE cashorbankid='".$row."')");

			if($query->num_rows() > 0)
			{
                $count++;
            }
        }
        echo $count;
    }
    public function cash_or_bank_enable_disable() 
	{
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Cash_or_bank->_where = array("id" => $PostData['id']);
        $this->Cash_or_bank->Edit($updatedata);

        echo $PostData['id'];
    }
	public function delete_mul_cash_or_bank()
    {
        
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $MEMBERID = $this->session->userdata[base_url().'MEMBERID'];
        foreach($ids as $row)
        {
            $query = $this->db->query("SELECT id FROM ".tbl_cashorbank." WHERE 
            id IN (SELECT cashorbankid FROM ".tbl_paymentreceipt." WHERE cashorbankid='".$row."')");

            if($query->num_rows() == 0)
            {
                $this->Cash_or_bank->Delete(array("id"=>$row));
            }
        }
    }
    public function getMemberBankAccounts()
    {
        $PostData = $this->input->post();
        $MEMBERID = $this->session->userdata[base_url().'MEMBERID'];
        $memberid = $PostData['memberid'];
        $bankdata = $this->Cash_or_bank->getBankAccountsByMember($memberid);
   
        echo json_encode($bankdata);
    }	

    public function updateDefaultBank($New_Cash_Or_BankId){

        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');

        $this->Cash_or_bank->_where = "defaultbank=1 AND memberid=".$MEMBERID;
        $DefaultBankId = $this->Cash_or_bank->getRecordByID();
        $updateData = array();

        foreach($DefaultBankId as $dbid){
            if($New_Cash_Or_BankId!=$dbid['id']){
                $updateData[] = array(
                    'id' => $dbid['id'],
                    'defaultbank' => 0,
                    'modifieddate' => $modifieddate,
                    'modifiedby' => $modifiedby
                );
            }
        }
        
        if(count($updateData)>0){
            $this->Cash_or_bank->edit_batch($updateData,'id');
        }
    }
}