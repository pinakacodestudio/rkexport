<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_analysis extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Product_analysis');
        $this->load->model('Product_analysis_model', 'Product_analysis');
    }
    public function index() {
        $this->viewData['title'] = "Product Analysis";
        $this->viewData['module'] = "report/Product_analysis";
        
        $this->load->model('User_model', 'User');
        $where=array();
        
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && in_array($this->session->userdata[base_url().'ADMINUSERTYPE'], explode(',',$this->viewData['submenuvisibility']['submenuviewalldata'])) === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getactiveUserListData($where);

        $this->load->model('Product_model', 'Product');
        $this->viewData['productdata'] = $this->Product->getProductList();
        
        $this->viewData['citydata'] = $this->Product_analysis->getCity();
        
        $stateid = implode(',', array_column($this->viewData['citydata'],"stateid"));                   
        $this->viewData['statedata'] = $this->Product_analysis->getState($stateid);

        $countryid = implode(',', array_column($this->viewData['statedata'],"countryid")); 
        $this->viewData['countrydata'] = $this->Product_analysis->getCountry($countryid);

        $this->load->model('Sales_analysis_model', 'Sales_analysis');
        $this->viewData['sellerdata'] = $this->Sales_analysis->getSellerBySales();
        $this->viewData['buyerdata'] = $this->Sales_analysis->getBuyerBySales();
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Analysis','View product analysis report.');
        }

        $this->admin_headerlib->add_plugin("datatables","datatables/fixedColumns.dataTables.min.css");
        $this->admin_headerlib->add_javascript_plugins("datatables","datatables/dataTables.fixedColumns.min.js");

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Product_analysis", "pages/product_analysis.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
    public function getproductanalysisdata(){
        
        $PostData = $this->input->post();
        $employee = (!empty($PostData['employee']))?implode(',',$PostData['employee']):'';
        $product = (!empty($PostData['product']))?implode(',',$PostData['product']):'';
        $countryid = (!empty($PostData['countryid']))?implode(',',$PostData['countryid']):'';
        $provinceid = (!empty($PostData['provinceid']))?implode(',',$PostData['provinceid']):'';
        $cityid = (!empty($PostData['cityid']))?implode(',',$PostData['cityid']):'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?implode(',',$PostData['month']):'';
        $seller = (!empty($PostData['seller']))?implode(',',$PostData['seller']):'';
        $buyer = (!empty($PostData['buyer']))?implode(',',$PostData['buyer']):'';

        $req = array();
        $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Product',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Total Sales ('.CURRENCY_CODE.')',"sortable"=>true,"class"=>"text-right");
        if(!empty($employee)){
            $req['COLUMNS'][] = array('title'=>'Sales Person',"sortable"=>true);
        }
        if($seller!=""){
            $req['COLUMNS'][] = array('title'=>'Seller',"sortable"=>false);
        }
        if(!empty($buyer)){
            $req['COLUMNS'][] = array('title'=>'Buyer',"sortable"=>false);
        }
        if(!empty($year)){
            $req['COLUMNS'][] = array('title'=>'Year',"sortable"=>false,"class"=>"width8");
        }
        if(!empty($month)){
            $req['COLUMNS'][] = array('title'=>'Month',"sortable"=>true,"class"=>"width12");
        }
        if(!empty($countryid)){
            $req['COLUMNS'][] = array('title'=>'Country',"sortable"=>true);
        }
        if(!empty($provinceid)){
            $req['COLUMNS'][] = array('title'=>'Province',"sortable"=>true);
        }
        if(!empty($cityid)){
            $req['COLUMNS'][] = array('title'=>'City',"sortable"=>true);
        }
        
		$reportdata = $this->Product_analysis->getProductAnalysisData($employee,$product,$year,$month,$countryid,$provinceid,$cityid,$seller,$buyer);
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();
        
        $data = array();
        $counter = 0;       
        foreach ($reportdata as $datarow) {
            
            $row = array();
            $row[] = ++$counter;   
            $row[] = $datarow->product;
            $row[] = numberFormat($datarow->totalsales,2,',');  
            if(!empty($employee)){
                $row[] = $datarow->employee;          
            }
            if($seller!=""){
                if($datarow->sellerchannelid!=0){
                    $channellabel="";
                    $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                    if(!empty($channeldata) && isset($channeldata[$key])){
                        $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                    }
                    $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellerid.'" title="'.$datarow->sellername.'" target="_blank">'.$datarow->sellername.' ('.$datarow->sellercode.')</a>';
                }else{
                    $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
                }
            }
            if(!empty($buyer)){
                $channellabel="";
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->buyerid.'" title="'.$datarow->buyername.'" target="_blank">'.$datarow->buyername.' ('.$datarow->buyercode.')</a>';
            }
            if(!empty($year)){
                $row[] = $datarow->year;
            }
            if(!empty($month)){
                
                foreach ($this->Monthwise as $monthid => $monthvalue) { 
                    if($monthid==$datarow->month){
                      $monthname = $monthvalue;
                    }
                }  
                $row[] = $monthname;
            }
            if(!empty($countryid)){
                $row[] = $datarow->countryname;
            }
            if(!empty($provinceid)){
                $row[] = $datarow->provincename;
            }
            if(!empty($cityid)){
                $row[] = $datarow->cityname;
            }
            
            $data[] = $row;
        }
        $req['DATA'] = $data;
        
		echo json_encode($req);
    }
    
    public function exporttoexcelproductanalysisreport(){
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Product Analysis','Export to excel product analysis report.');
        }
        $PostData = $this->input->get();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $product = (!empty($PostData['product']))?implode(',',$PostData['product']):'';
        $countryid = (!empty($PostData['countryid']))?$PostData['countryid']:'';
        $provinceid = (!empty($PostData['provinceid']))?$PostData['provinceid']:'';
        $cityid = (!empty($PostData['cityid']))?$PostData['cityid']:'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?$PostData['month']:'';
        $seller = (!empty($PostData['seller']))?$PostData['seller']:'';
        $buyer = (!empty($PostData['buyer']))?$PostData['buyer']:'';

        $exportdata = $this->Product_analysis->getProductAnalysisData($employee,$product,$year,$month,$countryid,$provinceid,$cityid,$seller,$buyer);
        
        $headings = $data = array();
        $counter = 0;       
        foreach ($exportdata as $datarow) {
            
            $row = array();
            $row[] = ++$counter;   
            $row[] = $datarow->product;
            $row[] = numberFormat($datarow->totalsales,2,',');  
            if(!empty($employee)){
                $row[] = $datarow->employee;                   
            }
            if($seller!=""){
                if($datarow->sellerchannelid!=0){
                    $row[] = $datarow->sellername." (".$datarow->sellercode.")";
                }else{
                    $row[] = 'COMPANY';
                }
            }
            if(!empty($buyer)){
                $row[] = $datarow->buyername." (".$datarow->buyercode.")";
            }
            if(!empty($year)){
                $row[] = $datarow->year;
            }
            if(!empty($month)){
                foreach ($this->Monthwise as $monthid => $monthvalue) { 
                    if($monthid==$datarow->month){
                        $monthname = $monthvalue;
                    }
                }  
                $row[] = $monthname;
            }
            if(!empty($countryid)){
                $row[] = $datarow->countryname;
            }
            if(!empty($provinceid)){
                $row[] = $datarow->provincename;
            }
            if(!empty($cityid)){
                $row[] = $datarow->cityname;
            }
            
            $data[] = $row;

        }
        $headings[] = 'Sr. No.';
        $headings[] = 'Product';
        $headings[] = 'Total Sales ('.CURRENCY_CODE.')';
        if(!empty($employee)){
            $headings[] = 'Sales Person';
        }
        if($seller!=""){
            $headings[] = 'Seller';
        }
        if(!empty($buyer)){
            $headings[] = 'Buyer';
        }
        if(!empty($year)){
            $headings[] = 'Year';
        }
        if(!empty($month)){
            $headings[] = 'Month';
        }
        if(!empty($countryid)){
            $headings[] = 'Country';
        }
        if(!empty($provinceid)){
            $headings[] = 'Province';
        }
        if(!empty($cityid)){
            $headings[] = 'City';
        }
        
        $this->general_model->exporttoexcel($data,"A1:N1","Product Analysis",$headings,"Product-Analysis.xls","C");
    }
   
    public function exporttopdfproductanalysisreport() {
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Product Analysis','Export to PDF product analysis report.');
        }

        $PostData = $this->input->get();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $product = (!empty($PostData['product']))?implode(',',$PostData['product']):'';
        $countryid = (!empty($PostData['countryid']))?$PostData['countryid']:'';
        $provinceid = (!empty($PostData['provinceid']))?$PostData['provinceid']:'';
        $cityid = (!empty($PostData['cityid']))?$PostData['cityid']:'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?$PostData['month']:'';
        $seller = (!empty($PostData['seller']))?$PostData['seller']:'';
        $buyer = (!empty($PostData['buyer']))?$PostData['buyer']:'';

        $PostData['reportdata'] = $this->Product_analysis->getProductAnalysisData($employee,$product,$year,$month,$countryid,$provinceid,$cityid,$seller,$buyer);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        
        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Productsanalysisformatforpdf', $PostData,true);
        // echo $header.$html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Product-Analysis-Report.pdf";
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

    public function printproductanalysisreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Product Analysis', 'Print product analysis report.');
        }
        $PostData = $this->input->post();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $product = (!empty($PostData['product']))?implode(',',$PostData['product']):'';
        $countryid = (!empty($PostData['countryid']))?$PostData['countryid']:'';
        $provinceid = (!empty($PostData['provinceid']))?$PostData['provinceid']:'';
        $cityid = (!empty($PostData['cityid']))?$PostData['cityid']:'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?$PostData['month']:'';
        $seller = (!empty($PostData['seller']))?$PostData['seller']:'';
        $buyer = (!empty($PostData['buyer']))?$PostData['buyer']:'';

        $PostData['reportdata'] = $this->Product_analysis->getProductAnalysisData($employee,$product,$year,$month,$countryid,$provinceid,$cityid,$seller,$buyer);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Productsanalysisformatforpdf", $PostData, true);

        echo json_encode($html);
    }
}