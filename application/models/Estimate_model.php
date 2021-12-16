<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Estimate_model extends Common_model {

	public $_table = tbl_estimate;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = array('e.id' => 'DESC');

	//set column field database for datatable orderable
	public $column_order = array(null,'e.estimatename','e.filename','e.createddate','createdby');

	//set column field database for datatable searchable 
	public $column_search = array('e.estimatename','e.filename','e.createddate','(IFNULL((SELECT name FROM '.tbl_user.' WHERE id=e.addedby),""))','DATE_FORMAT(e.createddate,"%d %b %Y %h:%i %p")');

	function __construct() {
		parent::__construct();
    }
	function create_pdf($productdata,$filename,$savetype="D"){
        $style = 'style="padding: 5px;border: 1px solid #666;font-size: 12px;"';
        $outproducttable = '';
        if(!empty($productdata[0])){
            $totaloutproductamount = $totaloutqty = 0;
            foreach($productdata[0] as $i=>$row){
                $totaloutproductamount = $totaloutproductamount + $row['amount'];
                $totaloutqty = $totaloutqty + $row['qty'];

                $outproducttable .= '<tr class="cnttblrow">';
                $outproducttable .= '<td class="text-center" '.$style.'>'.($i+1).'</td>';
                $outproducttable .= '<td '.$style.'>'.$row['productname'].'</td>';
                $outproducttable .= '<td '.$style.'>'.$row['variantname'].'</td>';
                $outproducttable .= '<td '.$style.'>'.$row['unit'].'</td>';
                $outproducttable .= '<td class="text-right" '.$style.'>'.numberFormat($row['price'],2,',').'</td>';
                $outproducttable .= '<td class="text-right" '.$style.'>'.numberFormat($row['qty'],2,',').'</td>';
                $outproducttable .= '<td class="text-right" '.$style.'>'.numberFormat($row['amount'],2,',').'</td>';
                $outproducttable .= '</tr>';
            }
            $outproducttable .= '<tr>
                                    <th colspan="5" class="text-right" '.$style.'>Total</th>
                                    <th class="text-right" '.$style.'>'.numberFormat($totaloutqty,2,',').'</th>
                                    <th class="text-right" '.$style.'>'.numberFormat($totaloutproductamount,2,',').'</th>
                                </tr>';
        }else{
            $outproducttable .= '<tr>
                                <th colspan="7" class="text-center" '.$style.'>No data available.</th>
                            </tr>';
        }
        $inproducttable = '';
        if(!empty($productdata[1])){
            $totalinproductamount = $totalinqty = 0;
            foreach($productdata[1] as $j=>$rows){
                
                $totalinproductamount = $totalinproductamount + $rows['amount'];
                $totalinqty = $totalinqty + $rows['qty'];

                $inproducttable .= '<tr class="cnttblrow">';
                $inproducttable .= '<td class="text-center" '.$style.'>'.($j+1).'</td>';
                $inproducttable .= '<td '.$style.'>'.$rows['productname'].'</td>';
                $inproducttable .= '<td '.$style.'>'.$rows['variantname'].'</td>';
                $inproducttable .= '<td class="text-right" '.$style.'>'.numberFormat($rows['price'],2,',').'</td>';
                $inproducttable .= '<td class="text-right" '.$style.'>'.numberFormat($rows['qty'],2,',').'</td>';
                $inproducttable .= '<td class="text-right" '.$style.'>'.numberFormat($rows['amount'],2,',').'</td>';
                $inproducttable .= '</tr>';
            }
            $inproducttable .= '<tr>
                                    <th colspan="4" class="text-right" '.$style.'>Total</th>
                                    <th class="text-right" '.$style.'>'.numberFormat($totalinqty,2,',').'</th>
                                    <th class="text-right" '.$style.'>'.numberFormat($totalinproductamount,2,',').'</th>
                                </tr>';
        }else{
            $inproducttable .= '<tr>
                                <th colspan="6" class="text-center" '.$style.'>No data available.</th>
                            </tr>';
        }
        $content = '<table id="outproducttable" class="table table-striped table-bordered mb-md mt-md" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th colspan="7" '.$style.'>Total OUT Product</th>  
                        </tr>
                        <tr>
                            <th class="width8 text-center" '.$style.'>Sr. No.</th>
                            <th '.$style.'>Product Name</th> 
                            <th '.$style.'>Variant Name</th> 
                            <th '.$style.'>Unit</th> 
                            <th class="text-right" '.$style.'>Price ('.CURRENCY_CODE.')</th> 
                            <th class="text-right" '.$style.'>Quantity</th> 
                            <th class="text-right" '.$style.'>Total Amount ('.CURRENCY_CODE.')</th> 
                        </tr>
                    </thead>
                    <tbody>
                    '.$outproducttable.'
                    </tbody>
                </table>
                <table id="inproducttable" class="table table-striped table-bordered mb-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th colspan="6" '.$style.'>Total Estimate Product</th>  
                        </tr>
                        <tr>
                            <th class="width8 text-center" '.$style.'>Sr. No.</th>
                            <th '.$style.'>Product Name</th> 
                            <th '.$style.'>Variant Name</th> 
                            <th class="text-right" '.$style.'>Per Pcs Cost ('.CURRENCY_CODE.')</th> 
                            <th class="text-right" '.$style.'>Quantity</th>
                            <th class="text-right" '.$style.'>Total Amount ('.CURRENCY_CODE.')</th> 
                        </tr>
                    </thead>
                    <tbody>
                    '.$inproducttable.'
                    </tbody>
                </table>';
                
        $Data['content'] = $content;
        $Data['invoicesettingdata'] = $this->general_model->getShipperDetails();

        $header=$this->load->view(ADMINFOLDER . 'Companyheader', $Data,true);
        $html=$this->load->view(ADMINFOLDER . 'product_process/Estimateformat', $Data,true);
        
        $this->load->library('m_pdf');
        //actually, you can pass mPDF parameter on this load() function
        $pdf = $this->m_pdf->load();

        // Set a simple Footer including the page number
        $pdf->setFooter('Side {PAGENO} 0f {nb}');

        //this the the PDF filename that user will get to download
        
        if($savetype=="F"){
            $pdfFilePath = ESTIMATE_PATH.$filename;
        }else{
            $pdfFilePath = $filename;
        }

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
        $pdf->Output($pdfFilePath, $savetype);

    }
	function get_datatables() {
		$this->_get_datatables_query();
		if($_POST['length'] != -1) {
			$this->readdb->limit($_POST['length'], $_POST['start']);
			$query = $this->readdb->get();
			return $query->result();
		}
	}

	function _get_datatables_query(){
		
		$this->readdb->select('e.id,e.estimatename,e.filename,e.createddate,e.addedby,IFNULL((SELECT name FROM '.tbl_user.' WHERE id=e.addedby),"") as createdby');
		$this->readdb->from($this->_table." as e");
        
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_search as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_search) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_order)) {
			$order = $this->_order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}


}
 ?>            
