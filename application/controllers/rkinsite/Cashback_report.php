<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cashback_report extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Cashback_report');
        $this->load->model('Cashback_report_model', 'Cashback_report');
    }
    public function index() {
        $this->viewData['title'] = "Cashback Report";
        $this->viewData['module'] = "report/Cashback_report";
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if($this->viewData['submenuvisibility']['managelog'] == 1){
           $this->general_model->addActionLog(4,'Cashback Report','View cashback report.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("cashback_report", "pages/cashback_report.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
        
        $list = $this->Cashback_report->get_datatables();
        // echo $this->readdb->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = "";

            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }
            if($datarow->status==1){
                $status = '<div class="dropdown"><button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Paid <span class="caret"></span></button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li id="dropdown-menu">
                                                <a onclick="changestatus(0,'.$datarow->id.')">Not Paid</a>
                                            </li>
                                    </ul></div>';
            }else{
                $status = '<div class="dropdown"><button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$datarow->id.'">Not Paid <span class="caret"></span></button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li id="dropdown-menu">
                                                <a onclick="changestatus(1,'.$datarow->id.')">Paid</a>
                                            </li>
                                    </ul></div>';
            }

            $row[] = ++$counter;
            $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'">'.ucwords($datarow->membername).' ('.$datarow->buyercode.')'.'</a>';
            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.$datarow->sellername.'">'.ucwords($datarow->sellername).' ('.$datarow->sellercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            $row[] = '<a href="'.ADMIN_URL.'invoice/view-invoice/'.$datarow->invoiceid.'" title="View Invoice" target="_blank">'.$datarow->invoiceno.'</a>';
            $row[] = numberFormat($datarow->netamount,2,',');
            $row[] = numberFormat($datarow->cashbackamount,2,',');
            $row[] = $status;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Cashback_report->count_all(),
                        "recordsFiltered" => $this->Cashback_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    function exporttoexcelcashbackreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Cashback Report','Export to excel cashback report.');
        }
        $exportdata = $this->Cashback_report->exportcashbackreport();
        
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            if($row->buyerchannelid != 0){
                $buyername = $row->membername.' ('.$row->buyercode.')';
            }else{
                $buyername = 'COMPANY';
            }
            if($row->sellerchannelid != 0){
                $sellername = $row->sellername.' ('.$row->sellercode.')';
            }else{
                $sellername = 'COMPANY';
            }

            $data[] = array(++$srno,$buyername,$sellername,
                            $row->invoiceno,
                            numberFormat($row->netamount,2,','),
                            numberFormat($row->cashbackamount,2,','),
                            ($row->status==0?"Not Paid":"Paid")
                        );
        }
        
        $headings = array('Sr. No.','Buyer Name','Seller Name','Invoice No.','Invoice Amount ('.CURRENCY_CODE.')','Cashback Amount ('.CURRENCY_CODE.')','Status'); 
        $this->general_model->exporttoexcel($data,"A1:H1","Cashback Report",$headings,"Cashback-Report.xls","E:F");
    }

    function exportToPDFCashbackReport() {
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Cashback Report','Export to PDF cashback report.');
        }

        $PostData['reportdata'] = $this->Cashback_report->exportcashbackreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Cashbackreportformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Cashback-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage('', // L - landscape, P - portrait 
                    '', '', '', '',
                    10, // margin_left
                    10, // margin right
                   40, // margin top
                   15, // margin bottom
                    3, // margin header
                    10); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL.'styles.css'); // external css
        $pdf->WriteHTML($stylesheet,1);
        $pdf->WriteHTML($stylesheet2,1);
        $pdf->SetHTMLHeader($header,'',true);
        $pdf->WriteHTML($html,0);
       
        ob_start();
        ob_end_clean();
        
        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "D");
    }

    public function update_status() {

        $PostData = $this->input->post();
        $status = $PostData['status'];
        $id = $PostData['id'];
        $modifiedby = $this->session->userdata(base_url().'ADMINID'); 
        $modifieddate = $this->general_model->getCurrentDateTime();
        
        $updateData = array(
            'status'=>$status,
            'modifieddate' => $modifieddate, 
            'modifiedby'=>$modifiedby
        );  
        
        $this->Cashback_report->_where = array("id" => $id);
        $updateid = $this->Cashback_report->Edit($updateData);
        if($updateid!=0) {

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Cashback_report->_fields="(select invoiceno from ".tbl_invoice." where id=invoiceid) as invoiceno";
                $this->Cashback_report->_where=array("id"=>$id);
                $data = $this->Cashback_report->getRecordsByID();

                $this->general_model->addActionLog(2,'Cashback Report','Change cashback status of '.$data['invoiceno'].' invoice in cashback report.');
            }
            
            echo 1;    
        }else{
            echo 0;
        }
                     
       
    }

    public function printcashbackreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Cashback Report', 'Print cashback report.');
        }
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Cashback_report->exportcashbackreport();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Cashbackreportformatforpdf", $PostData, true);

        echo json_encode($html);
    }
}