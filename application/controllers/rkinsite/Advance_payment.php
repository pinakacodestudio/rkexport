<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Advance_payment extends Admin_Controller 
{
    public $viewData = array();
    function __construct(){
        parent::__construct();

        $this->viewData = $this->getAdminSettings('submenu', 'Advance_payment');
        $this->load->model('Advance_payment_model','Advance_payment');
    }

    public function index(){
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Advance Payment";
        $this->viewData['module'] = "advance_payment/Advance_payment";
        
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(4, 'Advance Payment', 'View advance payment report.');
        }

        $this->admin_headerlib->add_plugin("datatables","datatables/fixedColumns.dataTables.min.css");
        $this->admin_headerlib->add_javascript_plugins("datatables","datatables/dataTables.fixedColumns.min.js");

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Advance_payment","pages/advance_payment.js");
        
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function getadvancepaymentdata()
    {
        $PostData = $this->input->post();
        $datetype = $PostData['datetype'];
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';
        $status = (!empty($PostData['status']))?implode(',',$PostData['status']):'';
        $rowtype = $PostData['rowtype'];
        $concatname='concatnameorcode';
        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveBuyerMemberByAdmin($concatname);
        
        
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

       
        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }

        $counter=0;
        $req = array();
        if($rowtype == 1){
           
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            //print_r($advancepaymentdata);exit;
            
            $datearray = array_column($advancepaymentdata, 'date');
            $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true);
            $req['COLUMNS'][] = array('title'=>Member_label,"sortable"=>true);
            $req['COLUMNS'][] = array('title'=>'Total Advance Payment ('.CURRENCY_CODE.')',"sortable"=>true,"class"=>"text-right");
            
            foreach ($Date as $daterow) {
                if (in_array($daterow, $datearray)) {
                    $req['COLUMNS'][] = array('title'=>$this->general_model->displaydate($daterow,$dateformat),"sortable"=>true,"class"=>"text-right");
                }
            }
            
            foreach ($memberdata as $index=>$memberrow){
                // echo "<pre>";
                // print_r($memberrow);
                // exit;
                $formateddata = $data = array();
                foreach ($Date as $daterow) {
    
                    if (in_array($daterow, $datearray)) {
    
                        //check date on date array
                        $keys = array_keys($datearray, $daterow);
                
                        //get array from search key
                        $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
                       
                        $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata,'memberid')), $memberrow['id']);
                       
                        if(!empty($searchkeys)){
                            $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.',',');
                            $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }/* else{
                        $formateddata[] = $data[] = '0.00';
                    } */
                }
                if(array_sum($data) > 0){
                    if($memberrow['channelid'] != 0)
                    {
                        $key = array_search($memberrow['channelid'], array_column($channeldata, 'id'));
                        if(!empty($channeldata) && isset($channeldata[$key])){
                            $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                        }
                        $memberrow['name']= $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$memberrow['id'].'" target="_blank" title="'.ucwords($memberrow['name']).'">'.ucwords($memberrow['name']).'</a>';
                    }
                    else{
                        $memberrow['name'] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
                    }
                    $req['DATA'][] = array_merge(array(++$counter, ucwords($memberrow['name']),number_format(array_sum($data),2,'.',',')),$formateddata);
                }
            }
        }else{
            //$memberid = implode(',',array_column($memberdata,'id'));
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            //print_r($advancepaymentdata);exit;
            $datearray = array_column($advancepaymentdata, 'date');
            $memberidarray = array_column($advancepaymentdata,'memberid');
            
            $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true);
            $req['COLUMNS'][] = array('title'=>'Date / Month',"sortable"=>true);
            $req['COLUMNS'][] = array('title'=>'Total Advance Payment ('.CURRENCY_CODE.')',"sortable"=>true,"class"=>"text-right");
            
            foreach ($memberdata as $memberrow) {
                if (in_array($memberrow['id'], $memberidarray)) {
                    $req['COLUMNS'][] = array('title'=>$memberrow['name'],"sortable"=>true,"class"=>"text-right");
                }
            }
           
            foreach ($Date as $index=>$daterow){
                $formateddata = $data = array();
                foreach ($memberdata as $memberrow) {
                    if (in_array($memberrow['id'], $memberidarray)) {
                        if (in_array($daterow, $datearray)) {
                    
                            //check date on date array
                            $keys = array_keys($datearray, $daterow);
                    
                            //get array from search key
                            $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
        
                            $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata,'memberid')), $memberrow['id']);
                            
                            if(!empty($searchkeys)){
                                $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.',',');
                                $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                            }else{
                                $formateddata[] = $data[] = '0.00';
                            }
                    
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }
                }
                if(array_sum($data) > 0){
                    $req['DATA'][] = array_merge(array(++$counter,$this->general_model->displaydate($daterow,$dateformat),number_format(array_sum($data),2,'.',',')),$formateddata);
                }
                
            }                
            
        }
		
		echo json_encode($req);
    }
    public function exportadvancepaymentreport(){

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Advance Payment', 'Export to Excel advance payment report.');
        }
        
        $datetype = $_REQUEST['datetype'];
        $startdate = $_REQUEST['startdate'];
        $enddate = $_REQUEST['enddate'];
        $channelid = $_REQUEST['channelid'];
        $memberid = (!empty($_REQUEST['memberid']))?implode(',',$_REQUEST['memberid']):'';
        $status = (!empty($_REQUEST['status']))?implode(',',$_REQUEST['status']):'';
        $rowtype = $_REQUEST['rowtype'];
        $concatname='concatnameorcode';
        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveBuyerMemberByAdmin($concatname);
        //print_r($memberdata);exit;
        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }
        $counter=0;
        $headings = $result = array();
        if($rowtype == 1){
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            $datearray = array_column($advancepaymentdata, 'date');

            foreach ($Date as $daterow) {
                if (in_array($daterow, $datearray)) {
                    $headings[] = $this->general_model->displaydate($daterow,$dateformat);
                }
            }

            $headings = array_merge(array('Sr. No.',Member_label,'Total Advance Payment ('.CURRENCY_CODE.')'),$headings);
            
            foreach ($memberdata as $index=>$memberrow){
                $formateddata = $data = array();
                foreach ($Date as $daterow) {
    
                    if (in_array($daterow, $datearray)) {
    
                        //check date on date array
                        $keys = array_keys($datearray, $daterow);
                
                        //get array from search key
                        $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
                       
                        $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata,'memberid')), $memberrow['id']);
                       
                        if(!empty($searchkeys)){
                            $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.',',');
                            $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }/* else{
                        $formateddata[] = $data[] = '0.00';
                    } */
                }
                if(array_sum($data) > 0){
                    $result[] = array_merge(array(++$counter,ucwords($memberrow['name']),number_format(array_sum($data),2,'.',',')),$formateddata);
                }
            }

        }
        else{
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            $datearray = array_column($advancepaymentdata, 'date');
            $memberidarray = array_column($advancepaymentdata,'memberid');
           
            foreach ($memberdata as $memberrow) {
                if (in_array($memberrow['id'], $memberidarray)) {
                    $headings[] = $memberrow['name'];
                }
            }
            $headings = array_merge(array('Sr. No.','Date / Month','Total Advance Payment ('.CURRENCY_CODE.')'),$headings);
            
            foreach ($Date as $index=>$daterow){
                $formateddata = $data = array();
                foreach ($memberdata as $memberrow) {
                    if (in_array($memberrow['id'], $memberidarray)) {
                        if (in_array($daterow, $datearray)) {
                    
                            //check date on date array
                            $keys = array_keys($datearray, $daterow);
                    
                            //get array from search key
                            $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
        
                            $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'memberid')), $memberrow['id']);
                            
                            if(!empty($searchkeys)){
                                $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.',',');
                                $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                            }else{
                                $formateddata[] = $data[] = '0.00';
                            }
                    
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }
                }
                if(array_sum($data) > 0){
                    $result[] = array_merge(array(++$counter,$this->general_model->displaydate($daterow,$dateformat),number_format(array_sum($data),2,'.','')),$formateddata);
                }
            }

        }
        $this->general_model->exporttoexcel($result,"A1:DD1","Advance Payment Report",$headings,"AdvancePaymentReport.xls","C:ZZ");       
    }

    public function exporttopdfadvancepaymentreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Advance Payment', 'Export to PDF advance payment report.');
        }

        $PostData = $this->input->get();

        $datetype = $_REQUEST['datetype'];
        $startdate = $_REQUEST['startdate'];
        $enddate = $_REQUEST['enddate'];
        $channelid = $_REQUEST['channelid'];
        $memberid = (!empty($_REQUEST['memberid']))?implode(',',$_REQUEST['memberid']):'';
        $status = (!empty($_REQUEST['status']))?implode(',',$_REQUEST['status']):'';
        $rowtype = $_REQUEST['rowtype'];
        $concatname='concatnameorcode';
        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveBuyerMemberByAdmin($concatname);
        //print_r($memberdata);exit;
        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }
        $counter=0;
        $headings = $result = array();
        if($rowtype == 1){
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            $datearray = array_column($advancepaymentdata, 'date');

            foreach ($Date as $daterow) {
                if (in_array($daterow, $datearray)) {
                    $headings[] = $this->general_model->displaydate($daterow,$dateformat);
                }
            }

            // $headings = array_merge(array('Sr. No.',Member_label,'Total Advance Payment ('.CURRENCY_CODE.')'),$headings);
            
            foreach ($memberdata as $index=>$memberrow){
                $formateddata = $data = array();
                foreach ($Date as $daterow) {
    
                    if (in_array($daterow, $datearray)) {
    
                        //check date on date array
                        $keys = array_keys($datearray, $daterow);
                
                        //get array from search key
                        $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
                       
                        $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata,'memberid')), $memberrow['id']);
                       
                        if(!empty($searchkeys)){
                            $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.','');
                            $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }/* else{
                        $formateddata[] = $data[] = '0.00';
                    } */
                }
                if(array_sum($data) > 0){
                    $result[] = array_merge(array("membername"=>ucwords($memberrow['name']),"total"=>number_format(array_sum($data),2,'.','')),array("payment"=>$formateddata));
                }
            }

        }
        else{
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            $datearray = array_column($advancepaymentdata, 'date');
            $memberidarray = array_column($advancepaymentdata,'memberid');
           
            foreach ($memberdata as $memberrow) {
                if (in_array($memberrow['id'], $memberidarray)) {
                    $headings[] = $memberrow['name'];
                }
            }
            // $headings = array_merge(array('Sr. No.','Date / Month','Total Advance Payment ('.CURRENCY_CODE.')'),$headings);
            
            foreach ($Date as $index=>$daterow){
                $formateddata = $data = array();
                foreach ($memberdata as $memberrow) {
                    if (in_array($memberrow['id'], $memberidarray)) {
                        if (in_array($daterow, $datearray)) {
                    
                            //check date on date array
                            $keys = array_keys($datearray, $daterow);
                    
                            //get array from search key
                            $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
        
                            $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'memberid')), $memberrow['id']);
                            
                            if(!empty($searchkeys)){
                                $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.','');
                                $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                            }else{
                                $formateddata[] = $data[] = '0.00';
                            }
                    
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }
                }
                if(array_sum($data) > 0){
                    $result[] = array_merge(array("date"=>$this->general_model->displaydate($daterow,$dateformat),"total"=>number_format(array_sum($data),2,'.','')),array("payment"=>$formateddata));
                }
            }

        }
        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Advancepaymentreportformat', $PostData, true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Advance-Payment-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage(
            '', // L - landscape, P - portrait 
            '',
            '',
            '',
            '',
            10, // margin_left
            10, // margin right
            40, // margin top
            15, // margin bottom
            3, // margin header
            10
        ); // margin footer

        $this->load->model('Common_model');
        $stylesheet = $this->Common_model->curl_get_contents(ADMIN_CSS_URL . 'bootstrap.min.css'); // external css
        $stylesheet2 = $this->Common_model->curl_get_contents(ADMIN_CSS_URL . 'styles.css'); // external css
        $pdf->WriteHTML($stylesheet, 1);
        $pdf->WriteHTML($stylesheet2, 1);
        $pdf->SetHTMLHeader($header, '', true);
        $pdf->WriteHTML($html, 0);

        ob_start();
        ob_end_clean();

        //offer it to user via browser download! (The PDF won't be saved on your server HDD)
        $pdf->Output($pdfFilePath, "D");
    }
    public function printadvancepaymentreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Advance Payment', 'Print advance payment report.');
        }
        $PostData = $this->input->post();

        $datetype = $PostData['datetype'];
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = (!empty($PostData['memberid']))?$PostData['memberid']:'';
        $status = (!empty($PostData['status']))?$PostData['status']:'';
        $rowtype = $PostData['rowtype'];
        $concatname='concatnameorcode';
        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveBuyerMemberByAdmin($concatname);
        //print_r($memberdata);exit;
        if($datetype==1){
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate,$enddate,'+1 day','Y-m-d');
            $dateformat = 'd/m/Y';
        }else{
            $startdate = $this->general_model->convertdate($startdate,'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate,'Y-m-d');
            $Date = $this->general_model->month_range($startdate,$enddate,'Y-m');
            $dateformat = 'm/Y';
        }
        $counter=0;
        $headings = $result = array();
        if($rowtype == 1){
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            $datearray = array_column($advancepaymentdata, 'date');

            foreach ($Date as $daterow) {
                if (in_array($daterow, $datearray)) {
                    $headings[] = $this->general_model->displaydate($daterow,$dateformat);
                }
            }

            // $headings = array_merge(array('Sr. No.',Member_label,'Total Advance Payment ('.CURRENCY_CODE.')'),$headings);
            
            foreach ($memberdata as $index=>$memberrow){
                $formateddata = $data = array();
                foreach ($Date as $daterow) {
    
                    if (in_array($daterow, $datearray)) {
    
                        //check date on date array
                        $keys = array_keys($datearray, $daterow);
                
                        //get array from search key
                        $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
                       
                        $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata,'memberid')), $memberrow['id']);
                       
                        if(!empty($searchkeys)){
                            $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.','');
                            $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }/* else{
                        $formateddata[] = $data[] = '0.00';
                    } */
                }
                if(array_sum($data) > 0){
                    $result[] = array_merge(array("membername"=>ucwords($memberrow['name']),"total"=>number_format(array_sum($data),2,'.','')),array("payment"=>$formateddata));
                }
            }

        }
        else{
            $advancepaymentdata = $this->Advance_payment->getadvancepaymentdata($datetype,$startdate,$enddate,$channelid,$memberid,$status);
            $datearray = array_column($advancepaymentdata, 'date');
            $memberidarray = array_column($advancepaymentdata,'memberid');
           
            foreach ($memberdata as $memberrow) {
                if (in_array($memberrow['id'], $memberidarray)) {
                    $headings[] = $memberrow['name'];
                }
            }
            // $headings = array_merge(array('Sr. No.','Date / Month','Total Advance Payment ('.CURRENCY_CODE.')'),$headings);
            
            foreach ($Date as $index=>$daterow){
                $formateddata = $data = array();
                foreach ($memberdata as $memberrow) {
                    if (in_array($memberrow['id'], $memberidarray)) {
                        if (in_array($daterow, $datearray)) {
                    
                            //check date on date array
                            $keys = array_keys($datearray, $daterow);
                    
                            //get array from search key
                            $searchdatedata = array_intersect_key($advancepaymentdata, array_flip($keys));
        
                            $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'memberid')), $memberrow['id']);
                            
                            if(!empty($searchkeys)){
                                $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['advancepayment'],2,'.','');
                                $data[]= $searchdatedata[$searchkeys[0]]['advancepayment'];
                            }else{
                                $formateddata[] = $data[] = '0.00';
                            }
                    
                        }else{
                            $formateddata[] = $data[] = '0.00';
                        }
                    }
                }
                if(array_sum($data) > 0){
                    $result[] = array_merge(array("date"=>$this->general_model->displaydate($daterow,$dateformat),"total"=>number_format(array_sum($data),2,'.','')),array("payment"=>$formateddata));
                }
            }

        }

        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Advancepaymentreportformat", $PostData, true);

        echo json_encode($html);
    }
}