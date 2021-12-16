<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_wise_sales_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Sales_report_model', 'Sales_report');
        $this->load->model("Channel_model","Channel");
        $this->viewData = $this->getChannelSettings('submenu', 'Product_wise_sales_report');
    }
    public function index() {
        $this->viewData['title'] = "Product Wise Sales Report";
        $this->viewData['module'] = "report/Product_wise_sales_report";

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getProductCategoryList($MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("Product_wise_sales_report", "pages/product_wise_sales_report.js");

        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function getproductwisesalesdata(){
        $PostData = $this->input->post();
        //print_r($PostData);
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'0';
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';
        $categoryid = $PostData['categoryid'];
        $productid = (!empty($PostData['productid']))?implode(',',$PostData['productid']):'';
        $status = (isset($PostData['status']))?implode(',',$PostData['status']):'';
        $datetype = $PostData['datetype'];
        
        $this->load->model('Member_model', 'Member');
        $this->load->model('Product_model', 'Product');
        
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
        
        // $req = array();
        // $req['COLUMNS'][] = array('title'=>'Sr.No.',"sortable"=>true);
        // $req['COLUMNS'][] = array('title'=>Member_label.' Name',"sortable"=>true);
        // $req['COLUMNS'][] = array('title'=>'Product Name',"sortable"=>true);
        // $req['COLUMNS'][] = array('title'=>'Total Sales',"sortable"=>true,"class"=>"text-right");
        
		/* foreach($memberdata as $memberrow){
			$req['COLUMNS'][] = array('title'=>$memberrow['name'],"sortable"=>true);
        } */
        // foreach ($Date as $daterow) {
        //     $req['COLUMNS'][] = array('title'=>$this->general_model->displaydate($daterow,$dateformat),"sortable"=>true,"class"=>"text-right");
        // }

        $html = '<thead>
                    <tr>
                        <th rowspan="2">Sr. No.</th>
                        <th rowspan="2">'. Member_label . ' Name</th>
                        <th rowspan="2">Product Name</th>
                        <th rowspan="2" class="text-right">Total Sales</th>
                        <th rowspan="2" class="text-right">Avg. Price</th>
                        <th rowspan="2" class="text-right">Total Sold Qty</th>';
        
        foreach ($Date as $daterow) {
            $html .= '<th colspan="2" class="text-center">'.$this->general_model->displaydate($daterow, $dateformat).'</th>';
        }
        $html .= '</tr><tr>';
        foreach ($Date as $daterow) {
            $html .= '<th class="text-right">Sales</th>';
            $html .= '<th class="text-right">Sold Qty</th>';
        }
        $html .= '</tr>';
        $html .= '</thead><tbody>';

        $memberid = implode(',',array_column($memberdata,'id'));
        $productwisesalesdata = $this->Sales_report->getproductwisesalesdata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype,$categoryid);
        //echo $this->db->last_query();exit;
        $datearray = array_column($productwisesalesdata, 'date');

        // print_r($memberdata); exit;
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company"));
        }
        $count=0;
        foreach ($memberdata as $index=>$memberrow) {
            
            $productdata = $this->Product->getMemberProducts($memberrow['id'],$productid,$categoryid);
            
            if(!empty($productdata)){

                foreach ($productdata as $index=>$productrow) {
                    $formateddata = $data = $soldqtyformateddata = $soldqty = array();
                    $temphtml = '';
                    foreach ($Date as $daterow) {
    
                        if (in_array($daterow, $datearray)) {
    
                            $combinationid = $productrow['conbinationid'];
                           
                            //check date on date array
                            $keys = array_keys($datearray, $daterow);
                            
                            //get array from search key
                            $searchdatedata = array_intersect_key($productwisesalesdata, array_flip($keys));
    
                            $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);
                            
                            if(!empty($searchkeys)){
                                $temphtml .= '<td class="text-right">'.number_format($searchdatedata[$searchkeys[0]]['totalsales'], 2, '.', '').'</td>';
                                $temphtml .= '<td class="text-right">'.number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', '').'</td>';
                                // $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalsales'],2,'.',',');
                                $data[]= number_format($searchdatedata[$searchkeys[0]]['totalsales'],2,'.','');
                                $soldqty[] = number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', '');
                            }else{
                                $formateddata[] = $data[] = $soldqtyformateddata[] = $soldqty[] = '0.00';
                                $temphtml .= '<td class="text-right">0.00</td>';
                                $temphtml .= '<td class="text-right">0.00</td>';
                            }
                        }else{
                            $formateddata[] = $data[] = $soldqtyformateddata[] = $soldqty[] = '0.00';
                            $temphtml .= '<td class="text-right">0.00</td>';
                            $temphtml .= '<td class="text-right">0.00</td>';
                        }
                    }
                    
                    //if(array_sum($data)>0){
                        // $req['DATA'][] = array_merge(array(++$count,ucwords($memberrow['name']),ucwords($productrow['productname']),number_format(array_sum($data),2,'.',',')),$formateddata);
                    //}
                    $totalsold = $avgprice = $totalsoldqty = 0;
                    $totalsold = array_sum($data);
                    $totalsoldqty = array_sum($soldqty);
                    if(!empty($totalsoldqty)){
                        $avgprice = $totalsold/$totalsoldqty;
                    }
                    $html .= '<tr>
                                <td>'.++$count.'</td>
                                <td>'.ucwords($memberrow['name']).'</td>
                                <td>'.ucwords($productrow['productname']).'</td>
                                <td class="text-right">'.number_format($totalsold, 2, '.', ',').'</td>
                                <td class="text-right">'.number_format($avgprice, 2, '.', ',').'</td>
                                <td class="text-right">'.number_format($totalsoldqty, 2, '.', ',').'</td>';
                    $html .= $temphtml.'</tr>';
                }
            }
        }
		$html .= '</tbody>';

        echo $html;
    }

    public function exportproductwisesalesreport(){

        $PostData = $this->input->get();
       
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = ($PostData['memberid'] != "")?$PostData['memberid']:'';
        $categoryid = $PostData['categoryid'];
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
            $headings[] = $this->general_model->displaydate($daterow, $dateformat)." - Sales";
            $headings[] = $this->general_model->displaydate($daterow, $dateformat)." - Sold Qty";
        }

        $headings = array_merge(array('Sr.No.',Member_label.' Name','Product Name', 'Total Sales', 'Total Sold Qty'),$headings);
        
        $memberid = implode(',',array_column($memberdata,'id'));
        $productwisesalesdata = $this->Sales_report->getproductwisesalesdata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype,$categoryid);
        $datearray = array_column($productwisesalesdata, 'date');

        
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
                $productdata = $this->Product->getMemberProducts($memberrow['id'],$productid,$categoryid);
                
                if(!empty($productdata)){

                    if($PostData['productid']!=''){
                        $productidarr = explode(",", $PostData['productid']);
                    }else{
                        $productidarr = array_column($productdata,'productid');
                    }

                    foreach ($productdata as $index=>$productrow) {

                        if(in_array($productrow['productid'],$productidarr)){

                            $formateddata = $data = $totalsold = $datewisesales = array();
                            foreach ($Date as $daterow) {

                                if (in_array($daterow, $datearray)) {

                                    $combinationid = $productrow['conbinationid'];
                                
                                    //check date on date array
                                    $keys = array_keys($datearray, $daterow);
                            
                                    //get array from search key
                                    $searchdatedata = array_intersect_key($productwisesalesdata, array_flip($keys));

                                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);
                                    
                                    if(!empty($searchkeys)){
                                        $formateddata[]= number_format($searchdatedata[$searchkeys[0]]['totalsales'],2,'.',',');
                                        $data[]= (int)($searchdatedata[$searchkeys[0]]['totalsales']);
                                        $totalsold[] = number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', '');

                                        $datewisesales[] = number_format($searchdatedata[$searchkeys[0]]['totalsales'], 2, '.', '');
                                        $datewisesales[] = number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', '');
                                    }else{
                                        $formateddata[]= $data[] = $totalsold[] = '0';
                                        $datewisesales[] = '0';
                                        $datewisesales[] = '0';
                                    }
                                }else{
                                    $formateddata[]= $data[] = $totalsold[] = '0';
                                    $datewisesales[] = '0';
                                    $datewisesales[] = '0';
                                }
                                
                            }
                        }

                        $result[] = array_merge(array(++$count,ucwords($memberrow['name']),ucwords($productrow['productname']),number_format(array_sum($data),2,'.',','),number_format(array_sum($totalsold), 2, '.', '')),$datewisesales);
                    }
                }
            }
        }
        
        $this->general_model->exporttoexcel($result,"A1:DD1","Product Wise Sales Report",$headings,"ProductWiseSalesReport.xls","D:ZZ");
        
    }

    public function exporttopdfproductwisesalesreport()
    {
        $PostData = $this->input->get();
        // print_r($PostData); exit;
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = ($PostData['memberid'] != "") ? $PostData['memberid'] : '';
        $categoryid = $PostData['categoryid'];
        $productid = ($PostData['productid'] != "") ? $PostData['productid'] : '';
        $status = ($PostData['status'] != '') ? $PostData['status'] : '';
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
        $productwisesalesdata = $this->Sales_report->getproductwisesalesdata($startdate,$enddate,$channelid,$memberid,$productid,$status,$datetype,$categoryid);
        $datearray = array_column($productwisesalesdata, 'date');

        
        if($channelid=='0'){
            $memberdata = array(array('id'=>'0','name'=>"Company"));
        }
        if($PostData['memberid']!=''){
            $memberidarr = explode(",", $PostData['memberid']);
        }else{
            $memberidarr = array_column($memberdata,'id');
        }
        $count = 0;
        foreach ($memberdata as $index => $memberrow) {

            if (in_array($memberrow['id'], $memberidarr)) {
                $productdata = $this->Product->getMemberProducts($memberrow['id'], $productid,$categoryid);

                if (!empty($productdata)) {

                    if ($PostData['productid'] != '') {
                        $productidarr = explode(",", $PostData['productid']);
                    } else {
                        $productidarr = array_column($productdata, 'productid');
                    }

                    foreach ($productdata as $index => $productrow) {

                        if (in_array($productrow['productid'], $productidarr)) {

                            $formateddata = $data = $datewisesales = $totalsold = array();
                            foreach ($Date as $daterow) {

                                if (in_array($daterow, $datearray)) {

                                    $combinationid = $productrow['conbinationid'];

                                    //check date on date array
                                    $keys = array_keys($datearray, $daterow);

                                    //get array from search key
                                    $searchdatedata = array_intersect_key($productwisesalesdata, array_flip($keys));

                                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);

                                    if (!empty($searchkeys)) {
                                        $formateddata[] = number_format($searchdatedata[$searchkeys[0]]['totalsales'], 2, '.', '');
                                        $data[] = (int)($searchdatedata[$searchkeys[0]]['totalsales']);
                                        $totalsold[] = number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', '');
                                        $datewisesales[] = array("sales"=>number_format($searchdatedata[$searchkeys[0]]['totalsales'], 2, '.', ''),"qty"=>number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', ''));
                                    } else {
                                        $formateddata[] = $data[] = $totalsold[] = '0';
                                        $datewisesales[] = array("sales"=>0,"qty"=>0);
                                    }
                                } else {
                                    $formateddata[] = $data[] = $totalsold[] = '0';
                                    $datewisesales[] = array("sales"=>0,"qty"=>0);
                                }
                            }
                        }

                        $result[] = array_merge(array("membername" => ucwords($memberrow['name']), "productname" => ucwords($productrow['productname']), "total" => number_format(array_sum($data), 2, '.', ''), "totalsold" => number_format(array_sum($totalsold), 2, '.', '')), array("datewisesales" => $datewisesales));
                    }
                }
            }
        }

        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;

        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html = $this->load->view(ADMINFOLDER . 'report/Productwisesalesformat', $PostData, true);
        // echo $header.$html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download

        $filename = "Product-Wise-Sales-Report.pdf";
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

    public function printproductwisesalesreport()
    {
        $PostData = $this->input->post();
        // echo "<pre>"; print_r($PostData); exit;
        $startdate = $PostData['startdate'];
        $enddate = $PostData['enddate'];
        $channelid = $PostData['channelid'];
        $memberid = ($PostData['memberid'] != "") ? $PostData['memberid'] : '';
        $categoryid = $PostData['categoryid'];
        $productid = ($PostData['productid'] != "") ? $PostData['productid'] : '';
        $status = ($PostData['status'] != '') ? $PostData['status'] : '';
        $datetype = $PostData['datetype'];

        $this->load->model('Product_model', 'Product');
        $this->load->model('Member_model', 'Member');
        $memberdata = $this->Member->getActiveMemberByChannel($channelid, $memberid);

        if ($datetype == 1) {
            $startdate = $this->general_model->convertdate($startdate);
            $enddate = $this->general_model->convertdate($enddate);
            $Date = $this->general_model->date_range($startdate, $enddate, '+1 day', 'Y-m-d');
            $dateformat = 'd/m/Y';
        } else {
            $startdate = $this->general_model->convertdate($startdate, 'Y-m-d');
            $enddate = $this->general_model->convertdate($enddate, 'Y-m-d');
            $Date = $this->general_model->month_range($startdate, $enddate, 'Y-m');
            $dateformat = 'm/Y';
        }

        $headings = $result = array();
        foreach ($Date as $daterow) {
            $headings[] = $this->general_model->displaydate($daterow, $dateformat);
        }


        // $headings = array_merge(array('Sr.No.',Member_label.' Name','Product Name', 'Total Sales'),$headings);

        $memberid = implode(',', array_column($memberdata, 'id'));
        $productwisesalesdata = $this->Sales_report->getproductwisesalesdata($startdate, $enddate, $channelid, $memberid, $productid, $status, $datetype,$categoryid);
        $datearray = array_column($productwisesalesdata, 'date');


        if ($channelid == '0') {
            $memberdata = array(array('id' => '0', 'name' => "Company"));
        }
        if ($PostData['memberid'] != '') {
            $memberidarr = explode(",", $PostData['memberid']);
        } else {
            $memberidarr = array_column($memberdata, 'id');
        }
        $count = 0;
        foreach ($memberdata as $index => $memberrow) {

            if (in_array($memberrow['id'], $memberidarr)) {
                $productdata = $this->Product->getMemberProducts($memberrow['id'], $productid,$categoryid);

                if (!empty($productdata)) {

                    if ($PostData['productid'] != '') {
                        $productidarr = explode(",", $PostData['productid']);
                    } else {
                        $productidarr = array_column($productdata, 'productid');
                    }

                    foreach ($productdata as $index => $productrow) {

                        if (in_array($productrow['productid'], $productidarr)) {

                            $formateddata = $data = $datewisesales = $totalsold = array();
                            foreach ($Date as $daterow) {

                                if (in_array($daterow, $datearray)) {

                                    $combinationid = $productrow['conbinationid'];

                                    //check date on date array
                                    $keys = array_keys($datearray, $daterow);

                                    //get array from search key
                                    $searchdatedata = array_intersect_key($productwisesalesdata, array_flip($keys));

                                    $searchkeys = array_keys(array_combine(array_keys($searchdatedata), array_column($searchdatedata, 'conbinationid')), $combinationid);

                                    if (!empty($searchkeys)) {
                                        $formateddata[] = number_format($searchdatedata[$searchkeys[0]]['totalsales'], 2, '.', '');
                                        $data[] = (int)($searchdatedata[$searchkeys[0]]['totalsales']);
                                        $totalsold[] = number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', '');
                                        $datewisesales[] = array("sales"=>number_format($searchdatedata[$searchkeys[0]]['totalsales'], 2, '.', ''),"qty"=>number_format($searchdatedata[$searchkeys[0]]['soldqty'], 2, '.', ''));
                                    } else {
                                        $formateddata[] = $data[] = $totalsold[] = '0';
                                        $datewisesales[] = array("sales"=>0,"qty"=>0);
                                    }
                                } else {
                                    $formateddata[] = $data[] = $totalsold[] = '0';
                                    $datewisesales[] = array("sales"=>0,"qty"=>0);
                                }
                            }
                        }

                        $result[] = array_merge(array("membername" => ucwords($memberrow['name']), "productname" => ucwords($productrow['productname']), "total" => number_format(array_sum($data), 2, '.', ''), "totalsold" => number_format(array_sum($totalsold), 2, '.', '')), array("datewisesales" => $datewisesales));
                    }
                }
            }
        }

        $PostData['headings'] = $headings;
        $PostData['reportdata'] = $result;

        // print_r($PostData); exit;
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Productwisesalesformat", $PostData, true);

        echo json_encode($html);
    }
}