<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_credit_note extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Credit_note_model', 'Credit_note');
        $this->viewData = $this->getChannelSettings('submenu', 'Purchase_credit_note');
    }
    public function index() {
        $this->viewData['title'] = "Credit Note";
        $this->viewData['module'] = "purchase_credit_note/Purchase_credit_note";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $this->load->model('Member_model', 'Member');
        $this->viewData['memberdata'] = $this->Member->getActiveSellerMemberByBuyer($MEMBERID,$CHANNELID,'concatnameormembercodeormobile');

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Credit_note", "pages/purchase_credit_note.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $channeldata = $this->Channel->getChannelList();
        
        $list = $this->Credit_note->get_datatables();
        // echo $this->db->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = $invoiceno_text = array();
            $channellabel = '';
            $Actions = ''; 
            $creditnotestatus = '';
            $status = $datarow->status;
            $invoiceIdArr = explode(",",$datarow->invoiceid);
            $invoiceNumberArr = explode(",",$datarow->invoiceno);

            if(!empty($invoiceNumberArr)){
                foreach($invoiceNumberArr as $key=>$invoiceNumber){
                    $invoiceid = $invoiceIdArr[$key];
                    $invoiceno_text[] = "<a href='".CHANNEL_URL."purchase-invoice/view-invoice/". $invoiceid."/"."' title='".$invoiceNumber."' target='_blank'>".$invoiceNumber."</a>";
                }
            }
            $row[] = ++$counter;

            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->buyerid){
                    $row[] = $channellabel.ucwords($datarow->buyername).' ('.$datarow->buyercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->sellerid){
                    $row[] = $channellabel.ucwords($datarow->sellername).' ('.$datarow->sellercode.')';
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.$datarow->sellername.'">'.ucwords($datarow->sellername).' ('.$datarow->sellercode.')'."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
          
            if($status == 0){
                $creditnotestatus = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised">Pending</button>';
            }else if($status == 1){
                $creditnotestatus = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised">Complete</button>';
            }else if($status == 2){
                $creditnotestatus = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Cancel</button>';
            }
            $Actions .= '<a href="'.CHANNEL_URL.'purchase-credit-note/view-credit-note/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';     

            $Actions .= '<a href="javascript:void(0)" onclick="printCreditNote('.$datarow->id.')" class="'.print_class.'" title="'.print_title.'">'.print_text.'</a>';  

            /* if($datarow->status==0){
                if(in_array($rollid, $edit)) {
                    $Actions .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'purchase-credit-note/view-credit-note/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
            } */
            
          /*   if(file_exists(CREDITNOTE_PATH.$companyname.'-creditnote-'.$datarow->creditnotenumber.'.pdf')){
                $Actions .= '<a href="'.CREDITNOTE.$companyname.'-creditnote-'.$datarow->creditnotenumber.'.pdf" class="'.view_class.'" title="'.view_title.'" target="_blank">'.view_text.'</a>';   
                
            }
            $Actions .= '<a href="javascript:void(0)" class="'.regeneratecredit_class.'" title="'.regeneratecredit_title.'" onclick="regeneratecreditnote('.$datarow->id.')">'.regeneratecredit_text.'</a>';   */
            /* $Actions .= '<a class="'.sendmail_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',3)" title="'.sendmail_title.'">'.sendmail_text.'</a>';
            
            $Actions .= '<a class="'.whatsapp_class.'" href="javascipt:void(0)" onclick="sendtransactionpdf('.$datarow->id.',3,1)" title="'.whatsapp_title.'">'.whatsapp_text.'</a>'; */
            
            $row[] = implode(", ",$invoiceno_text);
            $row[] = ($datarow->creditnotetype==0?'Product':'Offer');
            $row[] = $datarow->creditnotenumber;
            $row[] = $this->general_model->displaydate($datarow->creditnotedate);
            $row[] = $creditnotestatus;         
            $row[] = number_format(round($datarow->netamount),'2','.',',');
            
            $row[] = $Actions;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Credit_note->count_all(),
                        "recordsFiltered" => $this->Credit_note->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }
    
    /* public function regeneratecreditnote() {
        $PostData = $this->input->post();
        $creditnoteid = $PostData['creditnoteid'];
        echo $this->Credit_note->regeneratecreditnote($creditnoteid);
    }

    public function approvecreditnotes()
    {
        $PostData = $this->input->post();
        $status = $PostData['status'];
        $CredinoteId = $PostData['CredinoteId'];
       
        $updateData = array(
            'status'=>$status,
        );
        if($status==2){
            $updateData['resonforrejection'] = $PostData['resonforrejection'];
        }
       
        $this->Credit_note->_where = array("id" => $CredinoteId);
        $this->Credit_note->Edit($updateData);
        
         echo 1;    
    } */

    public function printCreditNote(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $creditnoteid = $PostData['id'];
        $PostData['transactiondata'] = $this->Credit_note->getCreditNoteDetails($creditnoteid);

        $sellerchannelid = $PostData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $PostData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $PostData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $PostData['printtype'] = 'creditnote';
        $PostData['heading'] = 'Credit Note';
        $PostData['hideonprint'] = '1';
        $html['content'] = $this->load->view(ADMINFOLDER."credit_note/Printcreditnoteformat.php",$PostData,true);
        
        echo json_encode($html); 
    }

    public function view_credit_note($id){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "View Credit Note";
        $this->viewData['module'] = "purchase_credit_note/View_credit_note";
        
        $this->viewData['transactiondata'] = $this->Credit_note->getCreditNoteDetails($id);

        $sellerchannelid = $this->viewData['transactiondata']['transactiondetail']['sellerchannelid'];
        $sellermemberid = $this->viewData['transactiondata']['transactiondetail']['sellermemberid'];
        
        $this->load->model('Invoice_setting_model','Invoice_setting');
        $this->viewData['invoicesettingdata'] = $this->Invoice_setting->getShipperDetails($sellerchannelid, $sellermemberid);
        $this->viewData['printtype'] = 'creditnote';
        $this->viewData['heading'] = 'Credit Note';

        $this->channel_headerlib->add_javascript("view_credit_note", "pages/view_credit_note.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
}