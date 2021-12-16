<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_commission_report  extends Admin_Controller {

    public $viewData = array();

    function __construct() {

        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Sales_commission_report');
        $this->load->model('Sales_commission_report_model', 'Sales_commission_report');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Sales Commission Report";
        $this->viewData['module'] = "report/Sales_commission_report";

        $this->load->model('User_model', 'User');
        $where=array();
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where = array('(reportingto='.$this->session->userdata(base_url().'ADMINID')." or id=".$this->session->userdata(base_url().'ADMINID').")"=>null);
        }
        $this->viewData['employeedata'] = $this->User->getUserListData($where);

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("salescommission", "pages/sales_commission_report.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {
        
        $this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getChannelList();
        $list = $this->Sales_commission_report->get_datatables();
        // print_r($list); exit;
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = "";

            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            }
           
            $row[] = ++$counter;
            $row[] = '<a href="'.ADMIN_URL.'invoice/view-invoice/'.$datarow->id.'" title="View Invoice" target="_blank">'.$datarow->invoiceno.'</a>';
            $row[] = ucwords($datarow->employeename);
            $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->memberid.'" title="'.ucwords($datarow->membername).'" target="_blank">'.ucwords($datarow->membername).' ('.$datarow->membercode.')'.'</a>';
            $row[] = $this->general_model->displaydate($datarow->invoicedate);
            $row[] = numberFormat($datarow->grosssales,2,',');
            $row[] = numberFormat($datarow->cost,2,',');
            $row[] = numberFormat($datarow->profit,2,',');
            $row[] = numberFormat($datarow->profitpercent,2,',');
            if($datarow->type == 1 && $datarow->productname != ""){
                $productarr = explode("|", $datarow->productname);
                $commissionpercentarr = explode("|", $datarow->commissiongroup);
                $commissionruppeesgrouparr = explode("|", $datarow->commissionruppeesgroup);
                $commission = $commissionrupees = "";
                
                foreach($productarr as $k=>$product){
                    $commission .= "<p>".($k+1).") ".$product."&nbsp;&nbsp;&nbsp;:&nbsp;".$commissionpercentarr[$k]."%</p>";

                    $commissionrupees .= "<p>".($k+1).") ".$product."&nbsp;&nbsp;&nbsp;:&nbsp;".numberFormat($commissionruppeesgrouparr[$k],2,',')."</p>";
                }
                
                
                $row[] = '<a title="Product Base Comm. (%)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$commission.'">'.numberFormat($datarow->commission,2,',').'</a>';
                
                $row[] = '<a title="Product Base Comm. ('.CURRENCY_CODE.')" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" data-content="'.$commissionrupees.'">'.numberFormat($datarow->commissionruppees,2,',').'</a>';
            }else{
                $row[] = numberFormat($datarow->commission,2,',');
                $row[] = numberFormat($datarow->commissionruppees,2,',');
            }
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Sales_commission_report->count_all(),
                        "recordsFiltered" => $this->Sales_commission_report->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function exporttoexcelsalescommissionreport(){
        
        /* if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Sales Commission Report','Export to excel sales commission report.');
        } */

        $exportdata = $this->Sales_commission_report->exportSalesCommissionReportData();
        
        $data = array();
        $srno = 0;
        foreach ($exportdata as $row) {         
            
            $data[] = array(++$srno,
                            $row->invoiceno,
                            ucwords($row->employeename),
                            ucwords($row->membername).' ('.$row->membercode.')',
                            $this->general_model->displaydate($row->invoicedate),
                            numberFormat($row->grosssales,2,','),
                            numberFormat($row->cost,2,','),
                            numberFormat($row->profit,2,','),
                            numberFormat($row->profitpercent,2,','),
                            numberFormat($row->commission,2,','),
                            numberFormat($row->commissionruppees,2,',')
                        );
        }
        
        $headings = array('Sr. No.','Invoice No.','Employee',Member_label,'Date','Gross Sales ('.CURRENCY_CODE.')','Cost ('.CURRENCY_CODE.')','Gross Profit ('.CURRENCY_CODE.')','GP (%)','Comm. (%)','Comm. ('.CURRENCY_CODE.')'); 
        
        $this->general_model->exporttoexcel($data,"A1:N1","Sales Commission Report",$headings,"Sales-Commission-Report.xls",array("F:K"));
    }

    public function exporttopdfsalescommissionreport() {
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(0,'Sales Commission Report','Export to PDF sales commission report.');
        }

        $PostData = $this->input->get();
        $PostData['reportdata'] = $this->Sales_commission_report->exportSalesCommissionReportData();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();
        
        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $PostData,true);
        $html=$this->load->view(ADMINFOLDER . 'report/Salescommissionformatforpdf', $PostData,true);
        // echo $html; exit;
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        $filename = "Sales-Commission-Report.pdf";
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

    public function printsalescommissionreport()
    {

        if ($this->viewData['submenuvisibility']['managelog'] == 1) {
            $this->general_model->addActionLog(0, 'Sales Commission Report', 'Print sales commission report.');
        }
        $PostData = $this->input->post();
        $PostData['reportdata'] = $this->Sales_commission_report->exportSalesCommissionReportData();
        $PostData['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $html = $this->load->view(ADMINFOLDER . 'Companyheader', $PostData, true);
        $html .= $this->load->view(ADMINFOLDER . "report/Salescommissionformatforpdf", $PostData, true);

        echo json_encode($html);
    }
}

?>