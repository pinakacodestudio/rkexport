<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shiprocket_order extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Shiprocket_order_model', 'Shiprocket_order');
        $this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Shiprocket_order');
    }
    public function index() {

        $this->viewData['title'] = "Shiprocket Order";
        $this->viewData['module'] = "shiprocket_order/Shiprocket_order";
        $this->viewData['VIEW_STATUS'] = "1";
        
        //$this->Shiprocket_order->updateShiprocketOrderStatus();

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("Shiprocket_order", "pages/shiprocket_order.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function listing() {
        
        $list = $this->Shiprocket_order->get_datatables();

        /* $tokendata = $this->Shiprocket_order->generate_token();
        $tokendata = json_decode($tokendata); */

      

        // echo $this->db->last_query();exit();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {

            $row = array();
            
            $actions = '';

            $row[] = ++$counter;

            $row[] = $this->general_model->displaydatetime($datarow->createddate);
           
            $row[] = $datarow->shiprocketorderid;
            $row[] = $datarow->shiprocketshipmentid;

            $row[] = '<span class="pull-right"><a href="'.ADMIN_URL.'invoice/view-invoice/'. $datarow->invoiceid.'/'.'" target="_blank" >'.$datarow->totalrate.'</a></span>';
            
            $awbno = (isset($datarow->awbno))?$datarow->awbno:'-';

            $row[] = '<b>N : </b>'.$datarow->membername.'<br><b>E : </b>'.$datarow->email.'<br><b>M :</b>'.$datarow->mobile;
            $row[] = '<b>Courier : </b>'.$datarow->couriername.'<br><b>AWB No : </b>'.$awbno.'<br><b>Pickup Address : </b>'.$datarow->pickuplocation; 

            $row[] = $datarow->length.' x '.$datarow->breath.' x '.$datarow->height.'<br><b>Weight : </b>'.$datarow->weight.'KG';   

            if($datarow->status==0){
                
                $btncls="btn-warning";
                $btntxt="Pending";
               
            }elseif($datarow->status==1){

                $btncls="btn-danger";
                $btntxt="Cancelled";
                
            }elseif($datarow->status==2){

                $btncls="btn-inverse";
                $btntxt="Shipping";
                
            }elseif($datarow->status==3){

                $btncls="btn-success";
                $btntxt="Delivered";
                
            }

            $row[] = '<button class="btn '.$btncls.'  '.STATUS_DROPDOWN_BTN.' btn-raised "  >'.$btntxt.' </button>';
           

            if($datarow->status==1){
                $actions = '';
            }else{
                if($datarow->awb_code==''){
                    $actions .= '<a href="javascript:void(0)" class="'.generateqrcode_class.'" title="Generate AWB Code" onclick="generateAwB('.$datarow->shiprocketshipmentid.','.$datarow->shippingorderid.','.$datarow->invoiceid.')">'.generateqrcode_text.'</a>';
                }else{

                    $actions .= '<a href="https://app.shiprocket.in/tracking/awb/'.$datarow->awb_code.'" target="_blank" class="'.track_class.'" title="'.track_title.'">'.track_text.'</a>';

                    $generatelabel = $this->Shiprocket_order->generateLabel($datarow->shiprocketshipmentid);
                    $generatelabel = '{
                        "label_created": 1,
                        "label_url": "https://kr-shipmultichannel.s3.ap-southeast-1.amazonaws.com/25149/labels/shipping-label-16104408-788830567028.pdf",
                        "response": "Label has been created and uploaded successfully!",
                        "not_created": []
                      }';
                    
                    $generatelabel = json_decode($generatelabel);
                    //print_r($generatelabel->label_created);exit;

                    if((isset($generatelabel->label_url)) && $generatelabel->label_created==1){
                        $labelurl = $generatelabel->label_url;
                    }

                    $actions .= '<a href="'.$labelurl.'" target="_blank" class="'.download_class.'" title="'.download_title.'">'.download_text.'</a>';
                }
                /*  $actions .= '<a href="javascript:void(0)" target="_blank" class="'.shipping_class.'" title="'.shipping_title.'">'.shipping_text.'</a>'; */
                $actions .= '<a href="javascript:void(0)"  class="'.cancel_class.'" title="'.cancel_title.'" onclick="cancelorder('.$datarow->shiprocketorderid.','.$datarow->shippingorderid.')">'.cancel_text.'</a>';
            }
            $row[] =  $actions;
            
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Shiprocket_order->count_all(),
                        "recordsFiltered" => $this->Shiprocket_order->count_filtered(),
                        "data" => $data,
                    );
        echo json_encode($output);
    }
    public function order_add() {
       
        $this->viewData['title'] = "Add Shiprocket Order";
        $this->viewData['module'] = "shiprocket_order/Add_shiprocket_order";
     
        $ADMINID = $this->session->userdata[base_url().'ADMINID'];
        

        /*$data = $this->Shiprocket_order->generate_token();
        $data = json_decode($data);
        $this->viewData['token'] = $data->token;*/

        

        $pickupdata = $this->Shiprocket_order->pickup_location();
        $pickupdata = json_decode($pickupdata);
        
        if($pickupdata->status_code!=401){
            
            $this->viewData['pickuplocation'] = $pickupdata->data->shipping_address;
        }
        //print_r($this->viewData['pickuplocation']);exit;

        $this->viewData['invoicedata'] = $this->Shiprocket_order->getInvoice();
        
        $this->admin_headerlib->add_plugin("progress-skylo","progress-skylo/skylo.css");
        $this->admin_headerlib->add_javascript_plugins("progress-skylo","progress-skylo/skylo.js"); 
       
        $this->admin_headerlib->add_javascript("add_shiprocketorder", "pages/add_shiprocketorder.js");

        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function insert_order(){

		

		$PostData = $this->input->post();
		//print_r($PostData);exit;
	
		$invoiceid = trim($PostData['invoiceid']);
		$pickuplocation = trim($PostData['pickuplocation']);
		$length = ($PostData['length']);
		$breath = trim($PostData['breath']);
		$height = trim($PostData['height']);
        $weight = trim($PostData['weight']);
        $couriercompanyid = trim($PostData['couriercompanyid']);
		$name = trim($PostData['name']);
		$rtocharges = ($PostData['rtocharges']);
		$trackingservice = trim($PostData['trackingservice']);
		$etd = trim($PostData['etd']);
        $totalrate = trim($PostData['totalrate']);
        $freightcharge = trim($PostData['freightcharge']);
		$codcharges = trim($PostData['codcharges']);
		$pickupaddress = trim($PostData['pickupaddress']);
		
	
		$createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        
        /*  $data = $this->Shiprocket_order->generate_token();
        $data = json_decode($data); */

        $createorder = $this->Shiprocket_order->create_order($PostData);
        $createorder = json_decode($createorder);
        //print_r($createorder);exit;

        

        $this->readdb->select('id');
		$this->readdb->from(tbl_couriercompany);
		$this->readdb->where('type=0 AND thirdparty=1 AND memberid=0 AND channelid=0');
		$this->readdb->limit(1);
        $courierid = $this->readdb->get()->row_array();
        
       

           
            if ((isset($createorder->order_id))) {
                $insertshippingorder = array("invoiceid"=>$invoiceid,
                                            "courierid"=>$courierid['id'],
                                            "currency"=>"INR",
                                            "shippingamount"=>$freightcharge,
                                            "invoiceamount"=>$createorder->invoiceamount,
                                            "codcharges"=>$codcharges,
                                            "shipdate"=>$this->general_model->convertdate($etd),
                                            "iscod"=>$createorder->cod,
                                            "createddate"=>$createddate,
                                            "addedby"=>$addedby,

                );
                $this->Shiprocket_order->_table = (tbl_shippingorder);
                $ShipOrderID = $this->Shiprocket_order->Add($insertshippingorder);

                if ($ShipOrderID) {
                    $insertdata = array("shippingorderid"=>$ShipOrderID,
                                        "shiprocketorderid"=>$createorder->order_id,
                                        "shiprocketshipmentid"=>$createorder->shipment_id,
                                        "pickupaddress"=>$pickupaddress,
                                        "pickuplocation"=>$pickuplocation,
                                        "length"=>$length,
                                        "breath"=>$breath,
                                        "height"=>$height,
                                        "weight"=>$weight,
                                        "courierid"=>$couriercompanyid,
                                        "couriername"=>$name,
                                        "rtocharges"=>$rtocharges,
                                        "trackingservice"=>$trackingservice,
                                        "etd"=>$this->general_model->convertdate($etd),
                                        "totalrate"=>$totalrate,
                                        "createddate"=>$createddate,
                                        "modifieddate"=>$createddate,
                                        "addedby"=>$addedby,
                                        "modifiedby"=>$addedby
                    );
                    $this->Shiprocket_order->_table = (tbl_shiprocketorder);
                    $OrderRegID = $this->Shiprocket_order->Add($insertdata);
                }

            
                //print_r($insertdata);exit;
            
                if ($OrderRegID) {

                    //update invoice status
                   
                    echo 1;
                    
                    //exit();
                }
            }else{
			    echo 0;//STAFF DETAILS NOT INSERTED
		    }
        
    }
    

    //AJAX CALL
    public function getCouriercompany(){
        $PostData = $this->input->post();
       
        $couriercompanydata = array();
        $couriercompanydata = $this->Shiprocket_order->courier($PostData);
       //print_r($couriercompanydata);exit;

        echo json_encode($couriercompanydata);
        
    }

    public function cancel_order(){
        $shiprocketorderid = $this->input->post('shiprocketorderid');
        $id = $this->input->post('id');
       /*  $data = $this->Shiprocket_order->generate_token();
        $data = json_decode($data); */
        $this->Shiprocket_order->cancel_order($shiprocketorderid);

        $updateData = array('status'=>1);
        $this->Shiprocket_order->_table = tbl_shippingorder;
        $this->Shiprocket_order->_where = array("id" => $id);
        $this->Shiprocket_order->Edit($updateData);

        echo 1;
    }

    public function generateAwB(){
        $shiprocketshipmentid = $this->input->post('shiprocketshipmentid');
        $shippingorderid = $this->input->post('shippingorderid');
        $invoiceid = $this->input->post('invoiceid');
       /*  $data = $this->Shiprocket_order->generate_token();
        $data = json_decode($data); */

        $awbdata = $this->Shiprocket_order->generateAwB($shiprocketshipmentid);

        $awbdata = '{
            "awb_assign_status": 1,
            "response": {
              "data": {
                "courier_company_id": 10,
                "awb_code": "788830567028",
                "cod": 0,
                "order_id": 16241076,
                "shipment_id": 16090281,
                "awb_code_status": 1,
                "assigned_date_time": {
                  "date": "2019-08-01 12:41:46.281791",
                  "timezone_type": 3,
                  "timezone": "Asia/Kolkata"
                },
                "applied_weight": 2.5,
                "company_id": 25149,
                "courier_name": "Delhivery",
                "child_courier_name": null
              }
            }
          }';

        $awbdata = json_decode($awbdata);
        //print_r($awbdata->response->data->awb_code);exit;

        if((isset($awbdata->awb_assign_status)) && $awbdata->awb_assign_status==1 ){
            $awbcode = $awbdata->response->data->awb_code;
            $updateData = array('awb_code'=>$awbcode);
            $this->Shiprocket_order->_table = tbl_shiprocketorder;
            $this->Shiprocket_order->_where = array("shiprocketshipmentid" => $shiprocketshipmentid);
            $this->Shiprocket_order->Edit($updateData);

            $updateData = array('trackingcode'=>$awbcode,'status'=>2);
            $this->Shiprocket_order->_table = tbl_shippingorder;
            $this->Shiprocket_order->_where = array("id" => $shippingorderid);
            $this->Shiprocket_order->Edit($updateData);

            $createddate = $this->general_model->getCurrentDateTime();
            $addedby = $this->session->userdata(base_url().'ADMINID');
            
            $this->load->model('Invoice_model', 'Invoices');
            $this->Invoices->_table = (tbl_invoice);
            $updatedata = array("status" => 4,'modifieddate'=>$createddate,"modifiedby" => $addedby);
            $this->Invoices->_where = array('id' => $invoiceid);
            $this->Invoices->Edit($updatedata); 
        }
        echo 1;
    }

    public function savecollapse(){
        $PostData = $this->input->post();
        $panelcollapsed = $this->general_model->saveModuleWiseFiltersOnSession('Order','collapse');
    
        echo $panelcollapsed;
    }

    public function getWeight(){

        $invoiceid = $this->input->post('invoiceid');
        $totalweight = $this->Shiprocket_order->getWeight($invoiceid);
        echo json_encode($totalweight);
    }
    
}