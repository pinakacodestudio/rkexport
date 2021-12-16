<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Invoice_setting extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Invoice_setting');
		$this->load->model('Invoice_setting_model','Invoice_setting');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Invoice Setting";
		$this->viewData['module'] = "invoice_setting/Invoice_setting";

		$this->viewData['invoicesettingdata'] = $this->Invoice_setting->getInvoiceSettingsByMember();

		//print_r($this->viewData['invoicesettingdata'] );exit;
		
		$this->admin_headerlib->add_bottom_javascripts("invoice_setting","pages/invoice_setting.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function listing() {
		
		$list = $this->Invoice_setting->get_datatables();
		$data = array();
        $counter = $_POST['start'];
        
        
		foreach ($list as $datarow) {
			$row = array();
			$Action = $Checkbox = $membername = $channelname = "";
            
            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'invoice-setting/invoice-setting-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Extra&nbsp;Charges","'.ADMIN_URL.'invoice-setting/delete-mul-invoice-setting") >'.delete_text.'</a>';
                
                $Checkbox .=  '<div class="checkbox">
                  <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                  <label for="deletecheck'.$datarow->id.'"></label>
                </div>';
            }

			$channelname = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            
			$membername = '-';
            
            $row[] = ++$counter;
            $row[] = $channelname;
            $row[] = $membername;
            $row[] = ucwords($datarow->businessname)."<br><b>GST No: </b>".$datarow->gstno;
            $row[] = ucfirst($datarow->businessaddress).", ".$datarow->cityname." (".$datarow->postcode."), ".$datarow->provincename.", ".$datarow->countryname;
			$row[] = $datarow->email;
            $row[] = '<img class="thumbwidth" src="'. MAIN_LOGO_IMAGE_URL.$datarow->logo.'">';
            $row[] = '<button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="getinvoicenotes('.$datarow->id.')">View Notes</button>';
            $row[] = $Action;
            $row[] = $Checkbox;

			$data[] = $row;
        }
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Invoice_setting->count_all(),
						"recordsFiltered" => $this->Invoice_setting->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}

	public function invoice_setting_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Invoice Setting";
		$this->viewData['module'] = "invoice_setting/Add_invoice_setting";

		$this->load->model("Country_model","Country");
		$this->viewData['countrydata'] = $this->Country->getCountry();
		
		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->admin_headerlib->add_javascript("add_invoice_setting","pages/add_invoice_setting.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function add_invoice_setting() {
        $PostData = $this->input->post();
		
		$businessname = $PostData['businessname'];
		$businessaddress = $PostData['businessaddress'];
		$countryid = $PostData['countryid'];
		$provinceid = $PostData['provinceid'];
		$cityid = $PostData['cityid'];
		$email = $PostData['email'];
		$gstno = $PostData['gstno'];
		$postcode = $PostData['postcode'];
		$invoicenotes = $PostData['invoicenotes'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'ADMINID');

        $this->Invoice_setting->_where = ("channelid='" .($PostData['channelid']) . "' AND memberid='" .($PostData['memberid']) . "'");
        $Count = $this->Invoice_setting->CountRecords();

        if ($Count == 0) {
		   
				if($_FILES["logo"]['name'] != ''){

					$image = uploadFile('logo', 'SETTINGS', SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
					if($image !== 0){
						if($image==2){
							echo 4;//file not uploaded
							exit;
						}
					}else{
						echo 3;//INVALID IMAGE TYPE
						exit;
					}	
				}else{
					$image = '';
				}


          
                $insertdata = array(
                    "channelid" => $PostData['channelid'],
                    "memberid" => $PostData['memberid'],
					"businessname"=>$businessname,
					"businessaddress"=>$businessaddress,
					"email"=>$email,
					"gstno"=>$gstno,
					"logo"=>$image,
					"cityid" => $cityid,
					"provinceid" => $provinceid,
					"countryid" => $countryid,
					"postcode" => $postcode,
					"notes"=>$invoicenotes,
                    "createddate" => $createddate,
                    "modifieddate" => $createddate,
                    "addedby" => $addedby,
                    "modifiedby" => $addedby
                );
                $insertdata = array_map('trim', $insertdata);
                $Add = $this->Invoice_setting->Add($insertdata);
                if ($Add) {
                    /* if($this->viewData['submenuvisibility']['managelog'] == 1){
                        $this->general_model->addActionLog(1,'Courier Company','Add new '.$PostData['companyname'].' courier company.');
                    } */
                    echo 1;
                } else {
                    echo 0;
                }
           
        } else {
            echo 2;
        }
    }

	public function invoice_setting_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Invoice Setting";
		$this->viewData['module'] = "invoice_setting/Add_invoice_setting";
		$this->viewData['action'] = "1";//Edit
		
		$this->viewData['invoicesettingdata'] = $this->Invoice_setting->getInvoiceSettingsdata($id);

		//print_r($this->viewData['invoicesettingdata']);exit;

		$this->load->model("Country_model","Country");
        $this->viewData['countrydata'] = $this->Country->getCountry();

		$this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
		$this->admin_headerlib->add_bottom_javascripts("add_invoice_setting","pages/add_invoice_setting.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function update_invoice_setting(){

		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$businessname = $PostData['businessname'];
		$businessaddress = $PostData['businessaddress'];
		$countryid = $PostData['countryid'];
		$provinceid = $PostData['provinceid'];
		$cityid = $PostData['cityid'];
		$email = $PostData['email'];
		$gstno = $PostData['gstno'];
		$postcode = $PostData['postcode'];
		$oldlogo = $PostData['oldlogo'];
		$invoicenotes = $PostData['invoicenotes'];

		if($_FILES["logo"]['name'] != ''){
			if($oldlogo == ""){
				$logo = uploadfile('logo', 'SETTINGS', SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
			}else{
				$logo = reuploadfile('logo', 'SETTINGS', $PostData['oldlogo'], SETTINGS_PATH, "jpeg|png|jpg|ico|JPEG|PNG|JPG");
			}
			if($logo !== 0){	
				if($logo==2){
					echo 4;
				}
			}else{
				echo 3;
			}
		}else{
			$logo = $oldlogo;
		}
		
		$this->Invoice_setting->_where = ("channelid='" .($PostData['channelid']) . "' AND memberid='" .($PostData['memberid']) . "' AND id!='".$PostData['invoicesettingid']."'");
        $Count = $this->Invoice_setting->CountRecords();

        if ($Count == 0) {
		
			$updatedata = array("channelid" => $PostData['channelid'],
								"memberid" => $PostData['memberid'],
								"businessname"=>$businessname,
								"businessaddress"=>$businessaddress,
								"email"=>$email,
								"gstno"=>$gstno,
								"logo"=>$logo,
								"cityid" => $cityid,
								"provinceid" => $provinceid,
								"countryid" => $countryid,
								"postcode" => $postcode,
								"notes"=>$invoicenotes,
								"modifieddate"=>$modifieddate,
								"modifiedby"=>$modifiedby
							);

			$updatedata=array_map('trim',$updatedata);
			$this->Invoice_setting->_where = array("id"=>$PostData['invoicesettingid']);
			$this->Invoice_setting->Edit($updatedata);
			echo 1;

		}else{
			echo 2;
		}
		
		
	}

	public function check_invoice_setting_use()
    {
       $count = 0;
       echo $count;
    }

	public function delete_mul_invoice_setting() {
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
           /* 
            if($this->viewData['submenuvisibility']['managelog'] == 1){

                $this->Invoice_setting->_where = array("id"=>$row);
                $Invoicesettingdata = $this->Invoice_setting->getRecordsById();
            
                $this->general_model->addActionLog(3,'Courier Company','Delete '.$Couriercompanydata['companyname'].' courier company.');
            } */
            $this->Invoice_setting->Delete(array("id"=>$row));    
        }
	}
	
	function getnotesbyid(){
        $PostData = $this->input->post();
        
        $this->Invoice_setting->_fields = "id,notes";
        $this->Invoice_setting->_where = "id=".$PostData['id'];
        $data = $this->Invoice_setting->getRecordsByID();
        
        $pagetitle='Invoice Notes';
    
        echo json_encode(array('pagetitle'=>$pagetitle,'description'=>$data['notes']));
    }
}