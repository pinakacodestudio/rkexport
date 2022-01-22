<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Transaction_report_model', 'Transaction_report');
        
        $this->viewData = $this->getAdminSettings('submenu', 'Transaction_report');
    }
    public function index() {
        $this->viewData['title'] = "Transaction Report";
        $this->viewData['module'] = "report/Transaction_report";
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Transaction_report", "pages/transaction_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
		
        $additionalrights = $this->viewData['submenuvisibility']['assignadditionalrights'];
        $list = $this->Payment_receipt->get_datatables();
     
        $data = array();
		$counter = $_POST['start'];
		foreach ($list as $index=>$datarow) {
			$row = array();
            $Action = $Checkbox = $channellabel = $buyermembername = '';
            $status = $datarow->status;

            if($status == 0){
                if($datarow->sellermemberid==0 && $datarow->type==2){
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
                }else{
                    $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Pending <span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="changestatus(1,'.$datarow->id.')">Approve</a>
                                </li>
                                <li id="dropdown-menu">
                                    <a onclick="changestatus(2,'.$datarow->id.')">Cancel</a>
                                </li>
                            </ul>';
                }
            }else if($status == 1){
                /* if($datarow->sellermemberid==0 && $datarow->type==2){
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Approve</button>';
                }else{ */
                    
                    $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Approve <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="changestatus(2,'.$datarow->id.')">Cancel</a>
                                </li>
                              </ul>';
                // }
            }else if($status==2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }

            $receiptstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';

            $Action .= '<a href="'.ADMIN_URL.'payment-receipt/view-payment-receipt/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            if(in_array('print', $additionalrights)) {
                $Action .= '<a href="javascript:void(0)" onclick="printPaymentReceipt('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  
            }

            if($status == 0){
                if($datarow->sellermemberid==0 && $datarow->type==2){
                    if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                        $Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'payment-receipt/payment-receipt-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
                    }
                }
                if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                    $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Receipt","'.ADMIN_URL.'payment-receipt/delete-mul-payment-receipt") >'.delete_text.'</a>';
                    
                    $Checkbox .=  '<div class="checkbox">
                        <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                        <label for="deletecheck'.$datarow->id.'"></label>
                    </div>';
                }
            }
            $row[] = ++$counter;
            $row[] = $buyermembername;
            $row[] = 'setic 1';
            $row[] = ($datarow->transactiondate!="0000-00-00")?$this->general_model->displaydate($datarow->transactiondate):"-";
            $row[] = $datarow->paymentreceiptno;
            $row[] = 'setic 1';
            $row[] = $receiptstatus;
            $row[] = number_format($datarow->amount,2,'.',',');
            $row[] = $Action;
            $row[] = $Checkbox;
			
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Payment_receipt->count_all(),
						"recordsFiltered" => $this->Payment_receipt->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
    
}