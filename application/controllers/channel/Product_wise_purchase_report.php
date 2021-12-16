<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_wise_purchase_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Purchase_report_model', 'Purchase_report');
        $this->load->model("Channel_model","Channel");
        $this->viewData = $this->getChannelSettings('submenu', 'Product_wise_purchase_report');
    }
    public function index() {
        $this->viewData['title'] = "Product Wise Purchase Report";
        $this->viewData['module'] = "report/Product_wise_purchase_report";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'multiplesellerchannel');
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Product_wise_purchase_report", "pages/product_wise_purchase_report.js");

        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    /* public function listing() {
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $channeldata = $this->Channel->getChannelList();
        $list = $this->Purchase_report->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $paymenttype = '';
            $channellabel = '';
            $purchaseprice = '<p class="text-right">'.number_format(($datarow->purchase), 2, '.', ',').'</p>';

            $row[] = ++$counter;

            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                if($MEMBERID == $datarow->buyerid){
                    $row[] = $channellabel.ucwords($datarow->buyername);
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.$datarow->buyername."</a>";
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
                    $row[] = $channellabel.ucwords($datarow->sellername);
                }else{
                    $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.$datarow->sellername.'">'.$datarow->sellername."</a>";
                }
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            $row[] = '<a href="'.CHANNEL_URL.'order/view-order/'.$datarow->orderid.'" target="_blank" title="'.$datarow->ordernumber.'">'.$datarow->ordernumber."</a>";
            $row[] = $this->general_model->displaydate($datarow->createddate);
            $row[] = $this->general_model->displaydate($datarow->paymentdate);

            if($datarow->paymenttype==1){
                $paymenttype = "COD";
            }else if($datarow->paymenttype==2){
                $paymenttype = isset($this->Paymentgatewaytype[$datarow->paymentgetwayid]) ? ucwords($this->Paymentgatewaytype[$datarow->paymentgetwayid]) : '-';
               
            }else if($datarow->paymenttype==3){
                $paymenttype = "Advance Payment";
            }else if($datarow->paymenttype==4){
                $paymenttype = "Partial Payment";
            }
           
            $row[] = '<p class="text-center">'.$paymenttype.'</p>';

            if($datarow->status==0){
                $row[] = '<span class="label label-warning">Pending</span>';
            }else if($datarow->status==1){
                $row[] = '<span class="label label-success">Complete</span>';
            }else{
                $row[] = '<span class="label label-danger">Cancel</span>';
            }
            
            $row[] =  $purchaseprice;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Purchase_report->count_all(),
                        "recordsFiltered" => $this->Purchase_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    } */

    public function getproductwisepurchasedata(){
        $PostData = $this->input->post();
        //print_r($PostData);
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'0';
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';
        $productid = (!empty($PostData['productid']))?implode(',',$PostData['productid']):'';
        $status = (!empty($PostData['status']))?implode(',',$PostData['status']):'';
        $datetype = $PostData['datetype'];

        $this->load->model('Product_model', 'Product');
        $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid,"multiplesellerchannel");
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

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
        
        $req = array();
        $req['COLUMNS'][] = array('title'=>'Sr.No.',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>Member_label.' Name',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Product Name',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Total Purchase',"sortable"=>true,"class"=>"text-right");
        
		/* foreach($memberdata as $memberrow){
			$req['COLUMNS'][] = array('title'=>$memberrow['name'],"sortable"=>true);
        } */
        foreach ($Date as $daterow) {
            $req['COLUMNS'][] = array('title'=>$this->general_model->displaydate($daterow,$dateformat),"sortable"=>true,"class"=>"text-right");
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $productwisepurchasedata = $this->Purchase_report->getproductwisepurchasedata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype);
        //echo $this->db->last_query();exit;
        $datearray = array_column($productwisepurchasedata, 'date');
        
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company"));
        }
        $count=0;
        foreach ($memberdata as $index=>$memberrow) {

            $productdata = $this->Product->getMemberProducts($memberrow['id'],$productid);
            //echo $this->db->last_query();exit;
            if(!empty($productdata)){
                foreach ($productdata as $index=>$productrow) {
                    $formateddata = $data = array();
                    foreach ($Date as $daterow) {

                        if (in_array($daterow, $datearray)) {

                            $combinationid = $productrow['conbinationid'];
                            //check date on date array
                            $keys = array_keys($datearray, $daterow);
                    
                            //get array from search key
                            $searchdatedata = array_intersect_key($productwisepurchasedata, array_flip($keys));

                            $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);
                            
                            if(!empty($searchkeys)){
                                $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalpurchase'],2,'.',',');
                                $data[]= number_format($searchdatedata[$searchkeys[0]]['totalpurchase'],2,'.','');
                            }else{
                                $formateddata[]=$data[]= '0.00';
                            }
                        }else{
                            $formateddata[]=$data[]= '0.00';
                        }
                    }

                    $req['DATA'][] = array_merge(array(++$index,ucwords($memberrow['name']),ucwords($productrow['productname']),number_format(array_sum($data),2,'.',',')),$formateddata);
                }
            }
        }
		
		echo json_encode($req);
    }
   
    public function exportproductwisepurchasereport(){
        
        $PostData = $this->input->get();
        //print_r($PostData); exit;
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = ($PostData['memberid'] != "")?$PostData['memberid']:'';
        $productid = ($PostData['productid'] != "")?$PostData['productid']:'';
        $status = ($PostData['status']!='')?$PostData['status']:'';
        $datetype = $PostData['datetype'];

        $this->load->model('Product_model', 'Product');
        $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid,"multiplesellerchannel");
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

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
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }

        $headings = array_merge(array('Sr.No.',Member_label,'Product Name', 'Total Purchase'),$headings);
        
        $memberid = implode(',',array_column($memberdata,'id'));
        $productwisepurchasedata = $this->Purchase_report->getproductwisepurchasedata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype);
        //echo $this->db->last_query();exit;
        
        $datearray = array_column($productwisepurchasedata, 'date');

        
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company"));
        }
        if($PostData['memberid']!=''){
            $memberidarr = explode(",", $PostData['memberid']);
        }else{
            $memberidarr = array_column($memberdata,'id');
        }
        $count=0;
        foreach ($memberdata as $index=>$memberrow) {

            if(in_array($memberrow['id'],$memberidarr)){
                $productdata = $this->Product->getMemberProducts($memberrow['id'],$productid);
                
                if(!empty($productdata)){

                    if($PostData['productid']!=''){
                        $productidarr = explode(",", $PostData['productid']);
                    }else{
                        $productidarr = array_column($productdata,'productid');
                    }

                    foreach ($productdata as $index=>$productrow) {

                        if(in_array($productrow['productid'],$productidarr)){
                            $formateddata = $data = array();
                            foreach ($Date as $daterow) {

                                if (in_array($daterow, $datearray)) {

                                    $combinationid = $productrow['conbinationid'];
                                    //check date on date array
                                    $keys = array_keys($datearray, $daterow);
                            
                                    //get array from search key
                                    $searchdatedata = array_intersect_key($productwisepurchasedata, array_flip($keys));

                                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);
                                    
                                    if(!empty($searchkeys)){
                                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalpurchase'],2,'.',',');
                                        $data[]= (int)($searchdatedata[$searchkeys[0]]['totalpurchase']);
                                    }else{
                                        $formateddata[]= '0.00';
                                    }
                                }else{
                                    $formateddata[]= '0.00';
                                }
                            }

                        }
                        $result[] = array_merge(array(++$index,ucwords($memberrow['name']),ucwords($productrow['productname']),number_format(array_sum($data),2,'.',',')),$formateddata);
                    }
                }
            }
        }
        
        $this->general_model->exporttoexcel($result,"A1:DD1","Product Wise Purchase Report",$headings,"ProductWisePurchaseReport.xls","D:ZZ");
        
    }

    public function exporttopdfproductwisepurchasereport()
    {
        $PostData = $this->input->get();
        // print_r($PostData); exit;
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = ($PostData['memberid'] != "")?$PostData['memberid']:'';
        $productid = ($PostData['productid'] != "")?$PostData['productid']:'';
        $status = ($PostData['status']!='')?$PostData['status']:'';
        $datetype = $PostData['datetype'];

        $this->load->model('Product_model', 'Product');
        $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid,"multiplesellerchannel");
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

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
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $productwisepurchasedata = $this->Purchase_report->getproductwisepurchasedata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype);
        $datearray = array_column($productwisepurchasedata, 'date');
        
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company"));
        }
        if($PostData['memberid']!=''){
            $memberidarr = explode(",", $PostData['memberid']);
        }else{
            $memberidarr = array_column($memberdata,'id');
        }
        $count=0;
        foreach ($memberdata as $index=>$memberrow) {

            if(in_array($memberrow['id'],$memberidarr)){
                $productdata = $this->Product->getMemberProducts($memberrow['id'],$productid);
                
                if(!empty($productdata)){

                    if($PostData['productid']!=''){
                        $productidarr = explode(",", $PostData['productid']);
                    }else{
                        $productidarr = array_column($productdata,'productid');
                    }

                    foreach ($productdata as $index=>$productrow) {

                        if(in_array($productrow['productid'],$productidarr)){

                            $formateddata = $data = array();
                            foreach ($Date as $daterow) {

                                if (in_array($daterow, $datearray)) {

                                    $combinationid = $productrow['conbinationid'];
                                
                                    //check date on date array
                                    $keys = array_keys($datearray, $daterow);
                            
                                    //get array from search key
                                    $searchdatedata = array_intersect_key($productwisepurchasedata, array_flip($keys));

                                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);
                                    
                                    if(!empty($searchkeys)){
                                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalpurchase'],2,'.','');
                                        $data[]= $searchdatedata[$searchkeys[0]]['totalpurchase'];
                                    }else{
                                        $formateddata[]= $data[] = '0';
                                    }
                                }else{
                                    $formateddata[]= $data[] = '0';
                                }
                                
                            }
                        }

                        $result[] = array_merge(array("membername" => ucwords($memberrow['name']),"productname" => ucwords($productrow['productname']),"total" => number_format(array_sum($data),2,'.','')),array("datewisepurchase" => $data));
                    }
                }
            }
        }

        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;

        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Productwisepurchaseformat', $PostData, true);
        // echo $header.$html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Product-Wise-Purchase-Report.pdf";
        $pdfFilePath = $filename;

        $pdf->AddPage(
            'L', // L - landscape, P - portrait 
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

    public function printproductwisepurchasereport()
    {
        $PostData = $this->input->post();
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = ($PostData['memberid'] != "")?$PostData['memberid']:'';
        $productid = ($PostData['productid'] != "")?$PostData['productid']:'';
        $status = ($PostData['status']!='')?$PostData['status']:'';
        $datetype = $PostData['datetype'];

        $this->load->model('Product_model', 'Product');
        $this->load->model('Member_model', 'Member');
        if(!empty($this->session->userdata(base_url().'CHANNELID')) && $this->session->userdata(base_url().'CHANNELID')==$channelid){
            $memberid = $this->session->userdata(base_url().'MEMBERID');
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }else if($channelid!=0 && $memberid==''){
            $memberdata = $this->Member->getActiveMemberByUnderMember($channelid,"multiplesellerchannel");
        }else{
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);
        }

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
        
        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow,$dateformat);
        }
        $memberid = implode(',',array_column($memberdata,'id'));
        $productwisepurchasedata = $this->Purchase_report->getproductwisepurchasedata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype);
        $datearray = array_column($productwisepurchasedata, 'date');
        
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company"));
        }
        if($PostData['memberid']!=''){
            $memberidarr = explode(",", $PostData['memberid']);
        }else{
            $memberidarr = array_column($memberdata,'id');
        }
        $count=0;
        foreach ($memberdata as $index=>$memberrow) {

            if(in_array($memberrow['id'],$memberidarr)){
                $productdata = $this->Product->getMemberProducts($memberrow['id'],$productid);
                
                if(!empty($productdata)){

                    if($PostData['productid']!=''){
                        $productidarr = explode(",", $PostData['productid']);
                    }else{
                        $productidarr = array_column($productdata,'productid');
                    }

                    foreach ($productdata as $index=>$productrow) {

                        if(in_array($productrow['productid'],$productidarr)){

                            $formateddata = $data = array();
                            foreach ($Date as $daterow) {

                                if (in_array($daterow, $datearray)) {

                                    $combinationid = $productrow['conbinationid'];
                                
                                    //check date on date array
                                    $keys = array_keys($datearray, $daterow);
                            
                                    //get array from search key
                                    $searchdatedata = array_intersect_key($productwisepurchasedata, array_flip($keys));

                                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);
                                    
                                    if(!empty($searchkeys)){
                                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalpurchase'],2,'.','');
                                        $data[]= $searchdatedata[$searchkeys[0]]['totalpurchase'];
                                    }else{
                                        $formateddata[]= $data[] = '0';
                                    }
                                }else{
                                    $formateddata[]= $data[] = '0';
                                }
                                
                            }
                        }

                        $result[] = array_merge(array("membername" => ucwords($memberrow['name']),"productname" => ucwords($productrow['productname']),"total" => number_format(array_sum($data),2,'.','')),array("datewisepurchase" => $data));
                    }
                }
            }
        }

        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Productwisepurchaseformat", $PostData, true);

        echo json_encode($html);
    }
}