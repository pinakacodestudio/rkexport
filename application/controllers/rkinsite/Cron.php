<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

	public $viewData = array();
	function __construct(){
        parent::__construct();
        $this->readdb = $this->load->database('readdb',TRUE);  // for read
        $this->writedb = $this->load->database('writedb',TRUE);  // for write
		
	}
	
	public function updateAdminProductOrMemberProductPrice()
	{
        $currentdatetime = $this->general_model->getCurrentDateTime();

        $this->load->model('Price_history_model', 'Price_history');

        $this->Price_history->_fields = 'id';
        $this->Price_history->_where = "scheduleddate = '2019-11-07 11:00:00' AND scheduleddate != '0000-00-00 00:00:00'";
        $pricehistory = $this->Price_history->getRecordByID();

        if(!empty($pricehistory)){
            $pricehistoryidarr = array_column($pricehistory, 'id');
            $this->Price_history->updateAdminProductOrMemberProductPrice($pricehistoryidarr);
        }

    }
    
    public function updateShiprocketOrderStatus(){

        $this->load->model('Shiprocket_order_model', 'Shiprocket');

       
        $shiprocketorderdata = $this->Shiprocket->getShiprocketOrders();

        //print_r($shiprocketorderdata);exit;

        $data = $this->Shiprocket->getToken();
        //$data = json_decode($data);


        foreach($shiprocketorderdata as $row){
            log_message('error', 'Cron Tracking Request : '.json_encode($row['Shiprocketshipmentid']), false);

            $responsedata = $this->Shiprocket->getTrackingByShipmentID($row['Shiprocketshipmentid'],$data['token']);
            $responsedata = '{"tracking_data": {
                "track_status": 1,
                "shipment_status": 3,
                "shipment_track": [{
                    "id": 8087109,
                    "awb_code": "788830567028",
                    "courier_company_id": 2,
                    "shipment_id": null,
                    "order_id": 63452426,
                    "pickup_date": null,
                    "delivered_date": null,
                    "weight": "2.5",
                    "packages": 1,
                    "current_status": "Pickup Generated",
                    "delivered_to": "New Delhi",
                    "destination": "New Delhi",
                    "consignee_name": "Naruto",
                    "origin": "Jammu",
                    "courier_agent_details": null
                }],
                "shipment_track_activities": [{
                    "date": "2019-08-01 05:20:55",
                    "activity": "Shipment information sent to FedEx - OC",
                    "location": "NA"
                }],
                "track_url": "https://app.shiprocket.in/tracking/awb/788830567028"
            }}';
            $responsedata = json_decode($responsedata);

            if((isset($responsedata->tracking_data)) && $responsedata->tracking_data->track_status==1){
                
                //echo $responsedata;
                log_message('error', 'Cron Tracking Response : '.json_encode($responsedata), false);
                //$responsedata = json_decode($responsedata);
                //print_r($responsedata->tracking_data->shipment_track[0]->delivered_date);exit;
                //print_r($responsedata->tracking_data->shipment_track[0]->order_id);

                if(!empty($responsedata->tracking_data->shipment_track[0]->delivered_date)){
                    $updateData = array('status'=>3);
                    $this->Shiprocket->_table = tbl_shippingorder;
                    $this->Shiprocket->_where = array("id" => $row['shippingorderid']);
                    $update = $this->Shiprocket->Edit($updateData);
                   /*  if($update){
                        echo "Updated";
                    } */
                }
            }
        }


    }

    //eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjg1NjY3OSwiaXNzIjoiaHR0cHM6Ly9hcGl2Mi5zaGlwcm9ja2V0LmluL3YxL2V4dGVybmFsL2F1dGgvbG9naW4iLCJpYXQiOjE2MDIyMjQ0OTAsImV4cCI6MTYwMzA4ODQ5MCwibmJmIjoxNjAyMjI0NDkwLCJqdGkiOiJkcU1mS01ZdXJQeU5rVWVxIn0.f59x9JzHAC_7wEoGkh6-pmXh9glH67NzcbX35S912yI
    
    public function updateToken(){

        $this->load->model('Shiprocket_order_model', 'Shiprocket');

        $data = $this->Shiprocket->generate_token();
        $data = json_decode($data);
        //print_r($data->token);

        $this->Shiprocket->_table = (tbl_shiprocketsetting);
        $this->Shiprocket->_where = "memberid=0 AND channelid=0";
        $this->Shiprocket->_fields = "id";
        $shiprocketsettingid = $this->Shiprocket->getRecordsByID();

        $this->Shiprocket->_table = (tbl_token);
        $this->Shiprocket->_where = "shiprocketsettingid=".$shiprocketsettingid['id'];
        $this->Shiprocket->_fields = "id";
        $shiprockettokendata = $this->Shiprocket->getRecordsByID();

            if(empty($shiprockettokendata)){
                $insertdata = array("token"=>$data->token,
                                    "shiprocketsettingid"=>$shiprocketsettingid['id'],
                                    "createddate"=>$this->general_model->getCurrentDateTime()

                );

                $this->Shiprocket->_table = (tbl_token);
                $this->Shiprocket->Add($insertdata);
            }      

        $updateData = array('token'=>$data->token,'createddate'=>$this->general_model->getCurrentDateTime());
        $this->Shiprocket->_table = tbl_token;
        $this->Shiprocket->_where = array("shiprocketsettingid"=>$shiprocketsettingid['id']);
        $update = $this->Shiprocket->Edit($updateData);
    }
}