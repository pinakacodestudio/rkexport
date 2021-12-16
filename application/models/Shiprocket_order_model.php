<?php
class Shiprocket_order_model extends Common_model{
	
	public $_table = tbl_shiprocketorder;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'so.createddate','so.shiprocketorderid','so.shiprocketshipmentid','so.totalrate',null,null,null,'so.status'); //set column field database for datatable orderable
	public $column_search = array(); //set column field database for datatable searchable
	public $order = array('so.id' => 'DESC');

	function __construct() {
		parent::__construct();
	}

	function getsetting($MEMBERID=0,$CHANNELID=0){
		
		$this->readdb->select('s.*');
		$this->readdb->from(tbl_shiprocketsetting." as s");
		$this->readdb->where('s.memberid="'.$MEMBERID.'" AND s.channelid="'.$CHANNELID.'"');
		$this->readdb->limit(1);
		$query = $this->readdb->get();
		return $query->row_array();
	}

	function getInvoice($MEMBERID=0){

		$this->readdb->select('i.id,i.invoiceno,i.amount');
		$this->readdb->from(tbl_invoice." as i");
		$this->readdb->where('i.sellermemberid="'.$MEMBERID.'" AND i.status!=1 AND i.status!=2');
		$this->readdb->where('i.id NOT IN (SELECT i2.id FROM '.tbl_invoice.' i2 INNER JOIN '.tbl_shippingorder.' sp ON i2.id=sp.invoiceid)');
		
		$query = $this->readdb->get();

		//print_r($query->result_array());exit;
		return $query->result_array();
	}

	function getToken($MEMBERID=0,$CHANNELID=0){
		$tokendata = $this->readdb->select("t.token")
								  ->from(tbl_token." as t")
								  ->join(tbl_shiprocketsetting." as ss","ss.id=t.shiprocketsettingid","INNER")
								  ->where("ss.memberid='".$MEMBERID."' AND ss.channelid='".$CHANNELID."'")
								  ->get()->row_array();

		if(empty($tokendata)){
			$data = $this->generate_token($MEMBERID,$CHANNELID);
			$data = json_decode($data);
            
			/* $this->_table = (tbl_shiprocketsetting);
			$this->_where = "memberid=0 AND channelid=0";
			$this->_fields = "id";
			$shiprocketsettingid = $this->Shiprocket->getRecordsByID(); */

			$shiprocketsettingid = $this->readdb->select("id")
								  ->from(tbl_shiprocketsetting)
								  ->where("memberid='".$MEMBERID."' AND channelid='".$CHANNELID."'")
								  ->get()->row_array();

			$tokendata = array('token'=>$data->token,'createddate'=>$this->general_model->getCurrentDateTime(),"shiprocketsettingid"=>$shiprocketsettingid['id']);

			$this->_table = tbl_token;
			$this->Add($tokendata);
		}
		return $tokendata;
	}

	function getShiprocketOrders(){
		$this->readdb->select('so.Shiprocketshipmentid,so.shippingorderid');
		$this->readdb->from(tbl_shiprocketorder." as so");
		$this->readdb->join(tbl_shippingorder." as s","s.id=so.shippingorderid","INNER");
		$this->readdb->where('s.status=2');
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result_array();
	}

	function getShiprocketCourierID($pickup_postcode,$delivery_postcode,$weight,$MEMBERID=0,$CHANNELID=0){

		$this->readdb->select('id');
		$this->readdb->from(tbl_couriercompany);
		$this->readdb->where('type=0 AND thirdparty=1 AND memberid=0 AND channelid=0');
		$this->readdb->limit(1);
		$courierid = $this->readdb->get()->row_array();

		if(empty($courierid['id'])){

			$this->readdb->select('id');
			$this->readdb->from(tbl_couriercompany);
			$this->readdb->where('type=0 AND thirdparty=0 AND memberid=0 AND channelid=0');
			$this->readdb->limit(1);
			$fedexcourierid = $this->readdb->get()->row_array();

			if(empty($fedexcourierid['id'])){
				$returndata = 1;
			}else{
				$returndata = $this->CheckCodAvailableByPincode($delivery_postcode);				
			}
			
		}else{
			$returndata = $this->courierserviceability($pickup_postcode,$delivery_postcode,$weight,$MEMBERID,$CHANNELID);
		}
		return $returndata;
	}

	function CheckCodAvailableByPincode($pincode){
		$data = array();
		$query = $this->readdb->select("IFNULL(cc.id,2) as courierid,IFNULL(cdl.providecod,0) as providecod")
							->from(tbl_courierdeliverylocation." as cdl")
							->where("cdl.pincode='".$pincode."'")
							->join(tbl_couriercompany." as cc","cc.id=cdl.courierid AND cc.status=1","INNER")
							->get();
		
		$data = $query->row_array();

		if(!empty($data)){
			return 1;
		}else{
			return 0;
		}
		
		
		return json_encode($data);
	}

	function getWeight($invoiceid){

		$this->readdb->select('sum(pp.weight) as totalweight');
		$this->readdb->from(tbl_invoice." as i");
		$this->readdb->join(tbl_orderproducts." as op","op.orderid=i.orderid","INNER");
		$this->readdb->join(tbl_productprices." as pp","pp.productid=op.productid","INNER");
		$this->readdb->where('i.id='.$invoiceid);
		$query = $this->readdb->get();

		return $query->row_array();

	}

	//*******************************SHIPROCKET API*********************************

	function generate_token($MEMBERID=0,$CHANNELID=0){

		$url = 'https://apiv2.shiprocket.in/v1/external/auth/login';
		
		$this->load->model('Shiprocket_setting_model','Shiprocket_setting');
		$this->Shiprocket_setting->_where = array("channelid"=>$CHANNELID,"memberid"=>$MEMBERID);
		$userdata = $this->Shiprocket_setting->getRecordsByID();

		//print_r($userdata);exit;
		$data = array("email"=>$userdata['email'],"password"=>$this->general_model->decryptIt($userdata['password']));
		
		$headers = array('Content-Type:application/json','Accept: application/json');
		
		$response = $this->shiprocket_curl($url,1,$data,$headers);
		return $response;
	}

	function pickup_location($MEMBERID=0,$CHANNELID=0){

		$tokendata = $this->getToken($MEMBERID,$CHANNELID);

		$url = 'https://apiv2.shiprocket.in/v1/external/settings/company/pickup';
		
		$headers = array('Content-Type:application/json','Authorization: Bearer '.$tokendata['token']);
		
		$response = $this->shiprocket_curl($url,0,array(),$headers);
		return $response;
	}

	function courier($PostData,$MEMBERID=0,$CHANNELID=0){

		$length = $PostData['length'];
        $breath = $PostData['breath'];
        $height = $PostData['height'];
		$weight = $PostData['weight'];
		$pickuplocation = $PostData['pickuplocation'];
		
		$data = $this->generate_token();
        $data = json_decode($data);
		
		
		//$pickupdata = $this->pickup_location($data->token);
		$pickupdata = $this->pickup_location($MEMBERID,$CHANNELID);
        $pickupdata = json_decode($pickupdata);
		$pincodedata = $pickupdata->data->shipping_address;
		foreach ($pincodedata as $pc){
			if($pc->pickup_location==$pickuplocation){
				$pincode = $pc->pin_code;
			}
		}
		//print_r($pincode['pin_code']);
		
		$url = 'https://apiv2.shiprocket.in/v1/external/courier/serviceability?pickup_postcode='.$pincode.'&delivery_postcode=360002&weight='.$weight.'&length='.$length.'&breadth='.$breath.'&height='.$height.'&cod=1';
		
		//print_r($url);//exit;

		$headers = array('Content-Type:application/json','Authorization: Bearer '.$data->token);
		
		$response = $this->shiprocket_curl($url,0,array(),$headers);
		return $response;
	}

	function create_order($PostData,$MEMBERID=0,$CHANNELID=0){

		$tokendata = $this->getToken($MEMBERID,$CHANNELID);

		$url = 'https://apiv2.shiprocket.in/v1/external/orders/create/adhoc';
		$headers = array('Content-Type:application/json','Authorization: Bearer '.$tokendata['token']);

		$query = $this->readdb->select('
			ma.name as billing_customer_name,ma.address as billing_address,
			(select name from '.tbl_city.' where id=ma.cityid) as billing_city,ma.postalcode as billing_pincode,(select name from '.tbl_province.' where id=ma.provinceid) as billing_state,(select c.name from '.tbl_province.' p INNER JOIN '.tbl_country.' c ON c.id=p.countryid where p.id=ma.provinceid) as billing_country,ma.email as billing_email,ma.mobileno as billing_phone,

			ma2.name as shipping_customer_name,ma2.address as shipping_address,
			(select name from '.tbl_city.' where id=ma2.cityid) as shipping_city,ma2.postalcode as shipping_pincode,(select name from '.tbl_province.' where id=ma2.provinceid) as shipping_state,(select c.name from '.tbl_province.' p INNER JOIN '.tbl_country.' c ON c.id=p.countryid where p.id=ma2.provinceid) as shipping_country,ma2.email as shipping_email,ma2.mobileno as shipping_phone,

			i.amount as sub_total,i.orderid,
		')
								->from(tbl_invoice." as i")
								->join(tbl_member." as m","m.id=i.memberid","INNER")
								->join(tbl_memberaddress." as ma","ma.id=i.addressid","INNER")
								->join(tbl_memberaddress." as ma2","ma2.id=i.shippingaddressid","INNER")
								->where('i.id',$PostData['invoiceid'])
								->get();
		$querydata =  $query->row_array();

		
		$orderquery = $this->readdb->select('op.name,op.sku,op.quantity as units,op.finalprice as selling_price,op.discount,op.tax,
		
	
		')
							->from(tbl_transactionproducts." as tp")
							->join(tbl_orderproducts." as op","op.id=tp.referenceproductid","INNER")
							->where('tp.transactiontype=3 AND tp.transactionid="'.$PostData['invoiceid'].'"')
							->get();
		$orderdata =  $orderquery->result_array();

		$orderid = explode(',',$querydata['orderid']);
		$payment_method = 'Prepaid';
		$cod = 0;
		foreach($orderid as $id){
			$paymentquery = $this->readdb->select('t.paymentgetwayid')
								->from(tbl_transaction." as t")
								->where('t.orderid="'.$id.'"')
								->get();
			$paymentdata =  $paymentquery->row_array();

			if($paymentdata['paymentgetwayid']==0){
				$payment_method = "COD";
				$cod = 1;
			}
		}
		
		
		$data = array('order_id'=>$PostData['invoiceid'],
					  'order_date'=>$this->general_model->getCurrentDateTime(),
					  'pickup_location'=>$PostData['pickuplocation'],
					  'billing_customer_name'=>$querydata['billing_customer_name'],
					  "billing_last_name"=>"",
					  'billing_address'=>$querydata['billing_address'],
					  'billing_city'=>$querydata['billing_city'],
					  'billing_pincode'=>$querydata['billing_pincode'],
					  'billing_state'=>$querydata['billing_state'],
					  'billing_country'=>$querydata['billing_country'],
					  'billing_email'=>$querydata['billing_email'],
					  'billing_phone'=>$querydata['billing_phone'],
					  'shipping_is_billing'=>0,
					  'shipping_customer_name'=>$querydata['shipping_customer_name'],
					  'shipping_address'=>$querydata['shipping_address'],
					  'shipping_city'=>$querydata['shipping_city'],
					  'shipping_pincode'=>$querydata['shipping_pincode'],
					  'shipping_country'=>$querydata['shipping_country'],
					  'shipping_state'=>$querydata['shipping_state'],
					  'shipping_email'=>$querydata['shipping_email'],
					  'shipping_phone'=>$querydata['shipping_phone'],
					  'order_items'=>$orderdata,
					  'payment_method'=>$payment_method,
					  'sub_total'=>$querydata['sub_total'],
					  'length'=>$PostData['length'],
					  'breadth'=>$PostData['breath'],
					  'height'=>$PostData['height'],
					  'weight'=>$PostData['weight'],
	
	
		);
		//print_r($data);exit;
		
		$response = $this->shiprocket_curl($url,1,$data,$headers);
		$response = json_decode($response);
		$response->cod = $cod;
		$response->invoiceamount = $querydata['sub_total'];
		$response = json_encode($response);
		//print_r($response);exit;
		return $response;
	}

	function cancel_order($id,$MEMBERID=0,$CHANNELID=0){
		
		$tokendata = $this->getToken($MEMBERID,$CHANNELID);

		$url = 'https://apiv2.shiprocket.in/v1/external/orders/cancel';
		
		$headers = array('Content-Type:application/json','Authorization: Bearer '.$tokendata['token']);
		$ids = array($id);
		$data = array('ids'=>$ids);

		$response = $this->shiprocket_curl($url,1,$data,$headers);
		return $response;
	}

	function getTrackingByShipmentID($id){

		$tokendata = $this->getToken();

		$url = 'https://apiv2.shiprocket.in/v1/external/courier/track/shipment/'.$id;
		$headers = array('Content-Type:application/json','Authorization: Bearer '.$tokendata['token']);
		
		$response = $this->shiprocket_curl($url,0,array(),$headers);
		//print_r($response);exit;
		return $response;
	}

	function generateAWB($id,$MEMBERID=0,$CHANNELID=0){

		$tokendata = $this->getToken($MEMBERID,$CHANNELID);

		$url = 'https://apiv2.shiprocket.in/v1/external/courier/assign/awb';
		
		$headers = array('Content-Type:application/json','Authorization: Bearer '.$tokendata['token']);
		
		$data = array('shipment_id'=>$id);

		$response = $this->shiprocket_curl($url,1,$data,$headers);
		//print_r($response);exit;
		return $response;
	}

	function getShiprocketSetting($MEMBERID=0,$CHANNELID=0){
		
		$query = $this->readdb->select("ss.id")
								  ->from(tbl_shiprocketsetting." as ss")
								  ->where("ss.memberid='".$MEMBERID."' AND ss.channelid='".$CHANNELID."'")
								  ->get();

		return $query->row_array();
	}

	function generateLabel($id,$MEMBERID=0,$CHANNELID=0){

		$settingdata = $this->readdb->select("ss.id")
								  ->from(tbl_shiprocketsetting." as ss")
								  ->where("ss.memberid='".$MEMBERID."' AND ss.channelid='".$CHANNELID."'")
								  ->get()->row_array();

		if(!empty($settingdata)){
			
			$tokendata = $this->getToken($MEMBERID,$CHANNELID);

			$url = 'https://apiv2.shiprocket.in/v1/external/courier/generate/label';
			
			$headers = array('Content-Type:application/json','Authorization: Bearer '.$tokendata['token']);
			
			$data = array('shipment_id'=>$id);
	
			$response = $this->shiprocket_curl($url,1,$data,$headers);
			//print_r($response);exit;
			return $response;
		}else{
			return false;
		}
	}

	function courierserviceability($pickup_postcode,$delivery_postcode,$weight,$MEMBERID,$CHANNELID){

	
		
		$tokendata = $this->getToken($MEMBERID,$CHANNELID);
		
		
		$url = 'https://apiv2.shiprocket.in/v1/external/courier/serviceability?pickup_postcode='.$pickup_postcode.'&delivery_postcode='.$delivery_postcode.'&cod=1&&weight='.$weight;
		
		//print_r($url);//exit;

		$headers = array('Content-Type:application/json','Authorization: Bearer '.$tokendata['token']);
		
		$response = $this->shiprocket_curl($url,0,array(),$headers);

		$response = json_decode($response);
		if($response->status==200 && isset($response->data)){
				return 1;
		}else{
				return 0;
		}
		//return $response;
	}

	//***************************************************************************


	//******************************LISTING DATA*********************************
	function _get_datatables_query($MEMBERID,$CHANNELID){

		$status = $_REQUEST['status'];
		
		
		$this->readdb->select("so.createddate,s.invoiceid,so.totalrate,m.name as membername,m.mobile,m.email,so.couriername,so.pickupaddress,so.pickuplocation,so.length,so.breath,so.height,so.weight,so.id,s.status,so.shiprocketorderid,so.shiprocketshipmentid,so.awb_code,so.shippingorderid,
		");	                         
		$this->readdb->from($this->_table." as so");
		$this->readdb->join(tbl_shippingorder." as s","s.id=so.shippingorderid","INNER");
		$this->readdb->join(tbl_invoice." as i","i.id=s.invoiceid","INNER");
		$this->readdb->join(tbl_member." as m","m.id=i.memberid","INNER");
		
	

		if($status!=-1){
			//echo"hcdk";exit;
			$this->readdb->where("s.status='".$status."'");
		}

		if($_REQUEST['startdate']!='' && $_REQUEST['enddate']!='')
		{
			$fromdate = $this->general_model->convertdatetime($_REQUEST['startdate']);
			$todate = $this->general_model->convertdatetime($_REQUEST['enddate']);
			$this->readdb->where("(DATE(so.createddate) BETWEEN '".$fromdate."' AND '".$todate."')");
		}
		
		$this->readdb->where("s.memberid='".$MEMBERID."' AND s.channelid='".$CHANNELID."'");


		$i = 0;

		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->readdb->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->readdb->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->readdb->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order)){
			$order = $this->order;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		
		return $query->result();
	}

	function count_filtered($MEMBERID=0,$CHANNELID=0) {
		$this->_get_datatables_query($MEMBERID,$CHANNELID);
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
	
		$this->readdb->from($this->_table." as a");
		return $this->readdb->count_all_results();
	}

	//***************************************************************************


}
        
