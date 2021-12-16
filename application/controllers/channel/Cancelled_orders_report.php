<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cancelled_orders_report extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Cancelled_orders_report');
        $this->load->model('Cancelled_orders_report_model', 'Cancelled_orders_report');
    }
    public function index() {
        $this->viewData['title'] = "Cancelled Orders Report";
        $this->viewData['module'] = "report/Cancelled_orders_report";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        /* $this->viewData['citydata'] = $this->Cancelled_orders_report->getCity();
        
        $stateid = implode(',', array_column($this->viewData['citydata'],"stateid"));                   
        $this->viewData['statedata'] = $this->Cancelled_orders_report->getState($stateid);

        $countryid = implode(',', array_column($this->viewData['statedata'],"countryid"));  */
        $this->viewData['countrydata'] = $this->Cancelled_orders_report->getCountry();

        $this->viewData['channeldata'] = $this->Cancelled_orders_report->getBuyerChannel();
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("cancelled_orders_report", "pages/cancelled_orders_report.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    
    public function getcancelledordersreportdata(){
        
        $PostData = $this->input->post();
        $countryid = (!empty($PostData['countryid']))?implode(',',$PostData['countryid']):'';
        $provinceid = (!empty($PostData['provinceid']))?implode(',',$PostData['provinceid']):'';
        $cityid = (!empty($PostData['cityid']))?implode(',',$PostData['cityid']):'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?implode(',',$PostData['month']):'';
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'';
        $memberid = (!empty($PostData['memberid']))?implode(',',$PostData['memberid']):'';

        $req = array();
        $req['COLUMNS'][] = array('title'=>'Sr. No.',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>Member_label.' Name',"sortable"=>true);
        $req['COLUMNS'][] = array('title'=>'Total Cancel Orders',"sortable"=>true);
        
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
        
		$reportdata = $this->Cancelled_orders_report->getCancelledOrdersReportData($year,$month,$countryid,$provinceid,$cityid,$channelid,$memberid);
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $data = array();
        $counter = 0;       
        foreach ($reportdata as $datarow) {
            
            $channellabel="";
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            $membername = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->id.'" title="'.$datarow->name.'" target="_blank">'.$datarow->name.' ('.$datarow->membercode.')</a>';

            $row = array();
            $row[] = ++$counter;   
            $row[] = $membername;
            $row[] = $datarow->countcanncelorder;  
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
    
    public function exporttoexcelcancelledordersreport(){
        
        $PostData = $this->input->get();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $countryid = (!empty($PostData['countryid']))?$PostData['countryid']:'';
        $provinceid = (!empty($PostData['provinceid']))?$PostData['provinceid']:'';
        $cityid = (!empty($PostData['cityid']))?$PostData['cityid']:'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?$PostData['month']:'';
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'';
        $memberid = (!empty($PostData['memberid']))?$PostData['memberid']:'';

        $exportdata = $this->Cancelled_orders_report->getCancelledOrdersReportData($year,$month,$countryid,$provinceid,$cityid,$channelid,$memberid);
        
        $headings = $data = array();
        $counter = 0;       
        foreach ($exportdata as $datarow) {
            
            $row = array();
            $row[] = ++$counter;   
            $row[] = $datarow->channel; 
            $row[] = $datarow->name." (".$datarow->membercode." - ".$datarow->mobile.")";
            $row[] = $datarow->countcanncelorder;  
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
        $headings[] = 'Channel';
        $headings[] = 'Member';
        $headings[] = 'Total Cancel Orders';
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
        
        $this->general_model->exporttoexcel($data,"A1:N1","Cancelled Orders Report",$headings,"Cancelled-Orders-Report.xls","D");
    }
   
    public function exporttopdfcancelledordersreport() {
        
        $PostData = $this->input->get();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $countryid = (!empty($PostData['countryid']))?$PostData['countryid']:'';
        $provinceid = (!empty($PostData['provinceid']))?$PostData['provinceid']:'';
        $cityid = (!empty($PostData['cityid']))?$PostData['cityid']:'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?$PostData['month']:'';
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'';
        $memberid = (!empty($PostData['memberid']))?$PostData['memberid']:'';

        $PostData['reportdata'] = $this->Cancelled_orders_report->getCancelledOrdersReportData($year,$month,$countryid,$provinceid,$cityid,$channelid,$memberid);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        
        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Cancelledordersreportformatforpdf', $PostData,true);
        // echo $header.$html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Cancelled-Orders-Report.pdf";
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

    public function printcancelledordersreport()
    {

        $PostData = $this->input->post();
        $employee = (!empty($PostData['employee']))?$PostData['employee']:'';
        $countryid = (!empty($PostData['countryid']))?$PostData['countryid']:'';
        $provinceid = (!empty($PostData['provinceid']))?$PostData['provinceid']:'';
        $cityid = (!empty($PostData['cityid']))?$PostData['cityid']:'';
        $year = $PostData['year'];
        $month = (!empty($PostData['month']))?$PostData['month']:'';
        $channelid = (isset($PostData['channelid']))?$PostData['channelid']:'';
        $memberid = (!empty($PostData['memberid']))?$PostData['memberid']:'';

        $PostData['reportdata'] = $this->Cancelled_orders_report->getCancelledOrdersReportData($year,$month,$countryid,$provinceid,$cityid,$channelid,$memberid);
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Cancelledordersreportformatforpdf", $PostData, true);

        echo json_encode($html);
    }

    public function getbuyermembers(){
        $PostData = $this->input->post();
  
        $memberdata = $this->Cancelled_orders_report->getBuyerMember($PostData['channelid']);
        echo json_encode($memberdata);
    }

    public function getProvinceList(){
        $PostData = $this->input->post();
  
        $provincedata = $this->Cancelled_orders_report->getState($PostData['countryid']);
        echo json_encode($provincedata);
    }

    public function getCityList(){
        $PostData = $this->input->post();
  
        $citydata = $this->Cancelled_orders_report->getCity($PostData['provinceid']);
        echo json_encode($citydata);
    }
}