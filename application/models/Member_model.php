<?php

class Member_model extends Common_model {

	//put your code here
	public $_table = tbl_member;
	public $_fields = "*";
	public $_where = array();
	public $_order = '';
	public $_except_fields = array();
	public $column_order = array(null,'m.name',"((select name from ".tbl_member." where id=m.parentmemberid limit 1))","((select name from ".tbl_member." where id=(select mainmemberid from ".tbl_membermapping." where submemberid=m.id limit 1) limit 1))",null,"(select count(id) from ".tbl_cart." where customerid=m.id and usertype=1)",'balance','m.createddate'); //set column field database for datatable orderable
	public $column_search = array('m.name',"((select name from ".tbl_member." where id=(select mainmemberid from ".tbl_membermapping." where submemberid=m.id limit 1) limit 1))",'m.createddate','m.mobile','m.email'); //set column field database for datatable searchable 
	public $order = array('m.id' => 'DESC'); // default order 

	//set column field database for datatable orderable
	//public $column_orderproduct = array(null,'name','categoryname','channelid','universalprice','salesprice');

	//set column field database for datatable searchable 
	//public $column_searchproduct = array('pr.name','CONCAT(pr.name," ",(SELECT CONCAT("[",GROUP_CONCAT(v.value),"]")  FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_variant.' as v on v.id=pc.variantid WHERE pc.priceid=mvp.priceid),"")','((select name from '.tbl_productcategory.' where id=pr.categoryid))','mvp.price','mvp.salesprice');

	//set column field database for datatable orderable
	public $column_orderorder = array(null,'m.name','o.orderid','date','o.status','o.payableamount');

	//set column field database for datatable searchable 
	public $column_searchorder = array('m.name','o.orderid','o.payableamount');
	public $_orderorder = array('o.createddate' => 'DESC');
	public $_orderproduct = array('pr.id' => 'DESC');

	public $column_orderquotation = array(null,'m.name','q.quotationid','date','q.status','q.quotationamount');
	public $column_searchquotation = array('m.name','q.quotationid','q.quotationamount');
	public $_orderquotation = array('q.createddate' => 'DESC');

	public $column_orderbillingaddress = array(null,'ma.name',null,'ma.email','ma.mobileno');
	public $column_searchbillingaddress = array('ma.name','ma.email','ma.address','ma.mobileno');
	public $_orderbillingaddress = array('ma.id' => 'DESC');

	function __construct() {
		parent::__construct();
	}
	 
	function getGlobalDiscountOfMember($memberid=0){
		$this->readdb->select('discountonbilltype,gstondiscount,discountonbillvalue,discountonbillstartdate,discountonbillenddate,discountonbillminamount,discountonbill,vendordiscount,discountpriority');
		$this->readdb->from(tbl_systemconfiguration); 
		$systemconfiguration = $this->readdb->get()->row_array();

		$discount = array();
		if($systemconfiguration['vendordiscount']==1 && !empty($memberid)){

			$memberdata = $this->getMemberDataByID($memberid);

			$this->load->model("Member_discount_model","Member_discount");
			$this->Member_discount->_fields = "discountonbill,gstondiscount,discountonbilltype,discountonbillvalue,discountonbillminamount,discountonbillstartdate,discountonbillenddate";
			$this->Member_discount->_where = array("discountonbill"=>1,"memberid"=>$memberid);
			$memberdiscount = $this->Member_discount->getRecordsByID();

			if(count($memberdiscount)>0){

				if($systemconfiguration['discountonbill']==1 && $systemconfiguration['discountpriority']==1 && $memberdata['discountpriority']==1){        
				   
					$discount['discounttype'] = $memberdiscount['discountonbilltype'];
					
					if(($memberdiscount['discountonbillstartdate']=="0000-00-00" && $memberdiscount['discountonbillenddate']=="0000-00-00") || 
					($memberdiscount['discountonbillstartdate']!="0000-00-00" && $memberdiscount['discountonbillenddate']!="0000-00-00") || 
					($memberdiscount['discountonbillstartdate']<=date("Y-m-d") && $memberdiscount['discountonbillenddate']>=date("Y-m-d"))){
						$discount['discount'] = $memberdiscount['discountonbillvalue'];
					}else{
						$discount['discount']='0';
					}
					$discount['minimumbillamount']=$memberdiscount['discountonbillminamount'];
					$discount['gstondiscount']=$memberdiscount['gstondiscount'];
				}else{
					$discount['discounttype'] = $systemconfiguration['discountonbilltype'];
					if(($systemconfiguration['discountonbillstartdate']=="0000-00-00" && $systemconfiguration['discountonbillenddate']=="0000-00-00") || 
					($systemconfiguration['discountonbillstartdate']<=date("Y-m-d") && $systemconfiguration['discountonbillenddate']>=date("Y-m-d"))){
						$discount['discount']=$systemconfiguration['discountonbillvalue'];
					}else{
						$discount['discount']='0';
					}
					$discount['minimumbillamount']=$systemconfiguration['discountonbillminamount'];
					$discount['gstondiscount']=$systemconfiguration['gstondiscount'];
				}    

			}else{
				
				$this->load->model('Channel_model', 'Channel');
				$channeldiscount = $this->Channel->getChannelDataByID($memberdata['channelid']);
				
				if(!empty($channeldiscount) && $systemconfiguration['discountonbill']==1 && $systemconfiguration['discountpriority']==1 && $channeldiscount['discountonbill']==1 && $channeldiscount['discountpriority']==1){

					$discount['discounttype']=$channeldiscount['discountonbilltype'];
					if(($channeldiscount['discountonbillstartdate']=="0000-00-00" && $channeldiscount['discountonbillenddate']=="0000-00-00") || ($channeldiscount['discountonbillstartdate']!="0000-00-00" && $channeldiscount['discountonbillenddate']!="0000-00-00") || ($channeldiscount['discountonbillstartdate']<=date("Y-m-d") && $channeldiscount['discountonbillenddate']>=date("Y-m-d"))){
						$discount['discount']=$channeldiscount['discountonbillvalue'];
					}else{
						$discount['discount']='0';
					}
					$discount['minimumbillamount']=$channeldiscount['discountonbillminamount'];
					$discount['gstondiscount']=$channeldiscount['gstondiscount'];
					
				}else{
					if($systemconfiguration['discountonbill']==1){        
						$discount['discounttype']=$systemconfiguration['discountonbilltype'];
						if(($systemconfiguration['discountonbillstartdate']=="0000-00-00" && $systemconfiguration['discountonbillenddate']=="0000-00-00") || ($systemconfiguration['discountonbillstartdate']<=date("Y-m-d") && $systemconfiguration['discountonbillenddate']>=date("Y-m-d"))){
							$discount['discount']=$systemconfiguration['discountonbillvalue'];
						}else{
							$discount['discount']='0';
						}
						$discount['minimumbillamount']=$systemconfiguration['discountonbillminamount'];
						$discount['gstondiscount']=$systemconfiguration['gstondiscount'];
					}else{
						$discount['minimumbillamount']='0';
						$discount['discounttype']='0';
						$discount['discount']='0';
						$discount['gstondiscount']='0';
					}
				}
			}
		}else{
			if($systemconfiguration['discountonbill']==1){        
				$discount['discounttype']=$systemconfiguration['discountonbilltype'];
				if(($systemconfiguration['discountonbillstartdate']=="0000-00-00" && $systemconfiguration['discountonbillenddate']=="0000-00-00") || ($systemconfiguration['discountonbillstartdate']<=date("Y-m-d") && $systemconfiguration['discountonbillenddate']>=date("Y-m-d"))){
					$discount['discount']=$systemconfiguration['discountonbillvalue'];
				}else{
					$discount['discount']='0';
				}
				$discount['minimumbillamount']=$systemconfiguration['discountonbillminamount'];
				$discount['gstondiscount']=$systemconfiguration['gstondiscount'];
			}else{
				$discount['minimumbillamount']='0';
				$discount['discounttype']='0';
				$discount['discount']='0';
				$discount['gstondiscount']='0';
			}
		}
		return $discount;
	}

	function getMemberCountByChannel($channelid){

		$query = $this->readdb->select("id")
						->from($this->_table)
						->where("channelid", $channelid)
						->get();

		if($query->num_rows() > 0){
			return $query->num_rows();
		}else{
			return 0; 
		}
	}
	
	function getDuplicateMember($where=array())
	{
		$this->readdb->select("m.id,m.name,cd.email,concat(cd.countrycode,cd.mobileno)as mobileno,companyname,remarks,(select group_concat(name) from ".tbl_user." where id in(select employeeid from ".tbl_crmassignmember." where memberid=m.id))as assigntoname,DATE_FORMAT(m.createddate,'%d/%m/%Y')as createddate,concat(ct.name,',<br>',s.name,',<br>',cn.name) as city,(select group_concat(inquirynote separator '|') from ".tbl_crminquiry." where memberid=m.id and inquirynote!='')as inquirynotes");
		
		$this->readdb->from($this->_table." as m");
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1");
		$this->readdb->join(tbl_city." as ct","ct.id=m.cityid","left");
		$this->readdb->join(tbl_province." as s","s.id=ct.stateid","left");
		$this->readdb->join(tbl_country." as cn","cn.id=s.countryid","left");
		$this->readdb->where($where);
		$query = $this->readdb->get();

		return $query->result_array();
	}

	function getSingleMember($id) {

		$this->readdb->select("m.membertype,m.id,m.name,cd.firstname,cd.lastname,cd.email,cd.countrycode,cd.mobileno,cd.birthdate,cd.annidate,cd.designation,cd.department,m.companyname,m.address,ar.areaname as area,m.pincode,m.latitude,m.longitude,ls.name as leadsource,z.zonename as zone,ic.name as industry,m.rating,m.status,ct.name as city,pr.name as province,cn.name as country,m.remarks,u.name as ename,m.website");

		$this->readdb->from($this->_table." as m");
		$this->readdb->where("m.id=".$id);
		$this->readdb->join(tbl_contactdetail." as cd","cd.memberid=m.id and primarycontact=1");
		$this->readdb->join(tbl_area." as ar","ar.id=m.areaid",'LEFT');
		$this->readdb->join(tbl_leadsource." as ls","ls.id=m.leadsourceid",'LEFT');
		$this->readdb->join(tbl_industrycategory." as ic","ic.id=m.industryid",'LEFT');
		$this->readdb->join(tbl_zone." as z","z.id=m.zoneid",'LEFT');
		$this->readdb->join(tbl_city." as ct","ct.id=m.cityid",'LEFT');
		$this->readdb->join(tbl_province." as pr","pr.id=ct.stateid",'LEFT');
		$this->readdb->join(tbl_country." as cn","cn.id=pr.countryid",'LEFT');
		$this->readdb->join(tbl_user." as u","u.id=m.assigntoid",'LEFT');
		$this->readdb->order_by($this->_order);
        if (isset($this->viewData['submenuvisibility']['submenuviewalldata']) && strpos($this->viewData['submenuvisibility']['submenuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			// $this->readdb->escape($this->session->userdata(base_url().'ADMINID')
            $this->readdb->where(array("("."(select count(id) from ".tbl_crmassignmember." where (employeeid=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")) and memberid=".$id.")>0 or m.addedby=".$this->readdb->escape($this->session->userdata(base_url().'ADMINID')).")"=>null));	
        }       
	
		$query = $this->readdb->get();
               
		if ($query->num_rows() > 0) {
			return $query->row_array();
		} else {
			return false;
		}
	}

	function getMemberOfCustomerChannel(){

		$query = $this->readdb->select("m.id,m.name")
							->from(tbl_member." as m")
							->where("m.channelid IN (SELECT id FROM ".tbl_channel." WHERE status=1 AND id='".CUSTOMERCHANNELID."') AND m.status=1")
							->order_by("m.name ASC")
							->get();

		return $query->result_array();
	}
	function generateQRCode($memberid){

		$memberdata = $this->getMemberDetail($memberid);
		
		$address = $memberdata['cityname'].", ".$memberdata['provincename'].", ".$memberdata['countryname'];
		$memberdetail = "";
		$memberdetail .= "BEGIN:VCARD\n";
		$memberdetail .= "VERSION:2.1\n";
		$memberdetail .= "N:".$memberdata['name']."\n";
		$memberdetail .= "ORG:".$memberdata['membercode']."\n";
		$memberdetail .= "TEL;WORK;VOICE:+".$memberdata['countrycode']." ".$memberdata['mobile']."\n";
		$memberdetail .= "EMAIL:".$memberdata['email']."\n";
		$memberdetail .= "ADR;TYPE=work;LABEL='Address':".$address."\n";
		$memberdetail .= "END:VCARD\n";
		
		return urlencode($memberdetail); 
	}

	/* function getnearmemberlist($counter,$stateid,$cityid,$channelid){

		$query = $this->readdb->select("m.id,m.name,p.name as provincename,c.name as cityname,
									(6371 * acos( 
										cos( radians(c.latitude) ) 
									* cos( radians( IFNULL((SELECT c2.latitude FROM city as c2 WHERE c2.id=".$cityid."),'') ) ) 
									* cos( radians( IFNULL((SELECT c2.longitude FROM city as c2 WHERE c2.id=".$cityid."),'') ) - radians(c.longitude) ) 
									+ sin( radians(c.latitude) ) 
									* sin( radians( IFNULL((SELECT c2.latitude FROM city as c2 WHERE c2.id=".$cityid."),'') ) )
										) ) as distance")
							->from($this->_table." as m")
							->join(tbl_province." as p","p.id=m.provinceid","INNER")
							->join(tbl_city." as c","c.id=m.cityid","INNER")
							->where("m.status=1 AND m.cityid IN (SELECT id FROM city as c2 WHERE c2.stateid=".$stateid.")")
							->where("m.channelid = (SELECT c.id FROM ".tbl_channel." as c WHERE c.priority < (SELECT c2.id FROM ".tbl_channel." as c2 WHERE c2.id=".$channelid.") ORDER BY c.priority DESC LIMIT 1)")
							->order_by("distance ASC")
							->limit(10,$counter)
							->get();

		//echo $this->readdb->last_query();exit;
		return $query->result_array();
		
	} */
	function getnearmemberlist($counter,$stateid,$cityid,$channelid,$referrerid){

		$query = $this->readdb->query("
			(SELECT m.id,m.name,p.name as provincename,c.name as cityname,
					(6371 * acos( 
						cos( radians(c.latitude) ) 
					* cos( radians( IFNULL((SELECT c2.latitude FROM city as c2 WHERE c2.id=1041),'') ) ) 
					* cos( radians( IFNULL((SELECT c2.longitude FROM city as c2 WHERE c2.id=1041),'') ) - radians(c.longitude) ) 
					+ sin( radians(c.latitude) ) 
					* sin( radians( IFNULL((SELECT c2.latitude FROM city as c2 WHERE c2.id=1041),'') ) )
						) ) as distance,
					m.email,m.mobile,m.membercode,m.image
			FROM member as m
			INNER JOIN province as p ON p.id=m.provinceid
			INNER JOIN city as c ON c.id=m.cityid
			WHERE m.id='".$referrerid."' AND 
			m.status=1 AND m.cityid IN (SELECT id FROM ".tbl_city." as c2 WHERE c2.stateid=".$stateid.") AND 
			m.channelid = (SELECT c.id FROM ".tbl_channel." as c WHERE c.priority < (SELECT c2.id FROM ".tbl_channel." as c2 WHERE c2.id=".$channelid.") ORDER BY c.priority DESC LIMIT 1)
			LIMIT ".$counter.",1)

			UNION
										
			(SELECT m.id,m.name,p.name as provincename,c.name as cityname,
					(6371 * acos( 
						cos( radians(c.latitude) ) 
					* cos( radians( IFNULL((SELECT c2.latitude FROM city as c2 WHERE c2.id=".$cityid."),'') ) ) 
					* cos( radians( IFNULL((SELECT c2.longitude FROM city as c2 WHERE c2.id=".$cityid."),'') ) - radians(c.longitude) ) 
					+ sin( radians(c.latitude) ) 
					* sin( radians( IFNULL((SELECT c2.latitude FROM city as c2 WHERE c2.id=".$cityid."),'') ) )
						) ) as distance,
					m.email,m.mobile,m.membercode,m.image

			FROM ".$this->_table." as m
			INNER JOIN ".tbl_province." as p ON p.id=m.provinceid
			INNER JOIN ".tbl_city." as c ON c.id=m.cityid
			WHERE m.status=1 AND m.cityid IN (SELECT id FROM ".tbl_city." as c2 WHERE c2.stateid=".$stateid.") AND 
			m.channelid = (SELECT c.id FROM ".tbl_channel." as c WHERE c.priority < (SELECT c2.id FROM ".tbl_channel." as c2 WHERE c2.id=".$channelid.") ORDER BY c.priority DESC LIMIT 1)
			AND m.id!='".$referrerid."'
			ORDER BY distance ASC
			LIMIT ".$counter.",10)");
		// echo $this->readdb->last_query();exit;
		return $query->result_array();
		
	}
	function insertRewardPointHistory($frommemberid,$tomemberid,$point,$rate,$detail,$type,$transactiontype,$createddate,$addedby){
		$this->load->model('Member_model', 'Member');
		$this->Member->_table = tbl_rewardpointhistory;

		$insertdata = array("frommemberid"=>$frommemberid,
							"tomemberid"=>$tomemberid,
							"point"=>$point,
							"rate"=>$rate,
							"detail"=>$detail,
							"type"=>$type,
							"transactiontype"=>$transactiontype,
							"createddate"=>$createddate,
							"addedby"=>$addedby);

		$rewardpointid = $this->Member->Add($insertdata);
		
		if ($rewardpointid > 0) {
			return $rewardpointid;
		} else {
			return '';
		}
		
	}
	function getMemberDataByIDForEdit($ID,$sellermemberid=0){
		$MEMBERID = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):0;
		$MEMBERID = ($sellermemberid>0?$sellermemberid:$MEMBERID);
		
		$query = $this->readdb->select("m.id,m.channelid,m.name,m.membercode,m.image,m.mobile,m.countrycode,m.email,m.password,m.debitlimit,m.status,m.memberratingstatusid,
										(select mainmemberid from ".tbl_membermapping." where submemberid=m.id limit 1)as sellermemberid,
										IFNULL((select m2.channelid from ".tbl_member." as m2 where m2.id=m.parentmemberid),0) as parentchannelid,
										IFNULL((select m2.channelid from ".tbl_member." as m2 INNER JOIN ".tbl_membermapping." as mp ON m2.id=mp.mainmemberid AND mp.submemberid=".$ID."),0) as sellerchannelid,
										IFNULL((select p.countryid from ".tbl_province." as p where p.id=provinceid),0) as countryid,
										m.gstno,m.minimumstocklimit,m.provinceid,m.cityid,m.paymentcycle,m.emireminderdays,
										m.parentmemberid,m.roleid,m.billingaddressid,m.shippingaddressid,
										IFNULL(ob.balancedate,'0000-00-00') as balancedate,
										IFNULL(ob.balance,0) as balance,
										IFNULL(ob.id,'') as balanceid,m.companyname,m.website,m.areaid,m.leadsourceid,m.industryid,m.assigntoid,m.memberstatus,m.remarks,m.address,m.zoneid,m.pincode,m.latitude,m.longitude,m.rating,m.membertype,m.panno,m.secondarymobileno,m.secondarycountrycode,m.websitelink,m.anniversarydate,m.minimumorderamount,m.advancepaymentcod,
										IFNULL((SELECT GROUP_CONCAT(employeeid) FROM ".tbl_salespersonmember." WHERE memberid=m.id),'') as employeeids, m.defaultcashorbankid,m.defaultbankmethod,m.isprimarywhatsappno,m.issecondarywhatsappno
										")
								->from($this->_table." as m")
								->join(tbl_openingbalance." as ob","ob.memberid=m.id AND ob.sellermemberid=".$MEMBERID,"LEFT")
								//->where("m.id='".$ID."' AND m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.")")
								->where("m.id='".$ID."'")
								->get();
		return $query->row_array();
	}
	function getMemberDataByID($ID){
		$query = $this->readdb->select("id,name,image,email,mobile as mobileno,status,roleid,reportingto,channelid,membercode,password,
		defaultcashorbankid,defaultbankmethod,countrycode,(SELECT discountpriority FROM ".tbl_channel." WHERE id=channelid) as discountpriority
		")
							->from($this->_table)
							->where("id='".$ID."'")
							->get();
							
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return 0;
		}	
	}
	function getMemberProductByID($channelid,$memberid){
		$this->readdb->select("p.id,p.name,IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image");
		$this->readdb->from(tbl_product." as p");
		if($channelid!=0){
			$this->readdb->join(tbl_memberproduct." as mp","mp.productid=p.id AND mp.memberid=".$memberid,"INNER");

			$this->readdb->where("IF(
								 (SELECT count(id) FROM ".tbl_memberproduct." WHERE memberid='".$memberid."' LIMIT 1)>0,
						
								 p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
										WHERE mp.memberid='".$memberid."' AND 
										(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))),
								
								 p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
										WHERE mp.memberid IN (SELECT mainmemberid FROM membermapping where submemberid='".$memberid."') AND 
										(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0)))
							)");
		}
		$this->readdb->where('p.status=1 AND p.producttype=0');
		$this->readdb->group_by('p.id');
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();
							
		return $query->result_array();
	}
	function getMemberProductsByMemberID($channelid,$memberid){

		$this->load->model('Channel_model', 'Channel');
        $channeldata = $this->Channel->getMemberChannelData($memberid);
		$memberbasicsalesprice = (!empty($channeldata['memberbasicsalesprice']))?$channeldata['memberbasicsalesprice']:0;
		$memberspecificproduct = (!empty($channeldata['memberspecificproduct']))?$channeldata['memberspecificproduct']:0;
		$channelid = (!empty($channeldata['channelid']))?$channeldata['channelid']:0;
		$currentsellerid = (!empty($channeldata['currentsellerid']))?$channeldata['currentsellerid']:0;
		$totalproductcount = (!empty($channeldata['totalproductcount']))?$channeldata['totalproductcount']:0;

		$this->readdb->select("p.id,p.name,IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'".PRODUCTDEFAULTIMAGE."') as image,");
		$this->readdb->from(tbl_product." as p");
		if($channelid!=0){
			if($totalproductcount > 0 && $memberspecificproduct==1){
				$this->readdb->where("(IF(
										(SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')>0,
										
										p.id IN(SELECT mp.productid FROM ".tbl_memberproduct." as mp 
											INNER JOIN ".tbl_membervariantprices." as mvp ON (mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid) 
											WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."' AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.price>0),0) > 0 GROUP BY mp.productid),
										
										IF(
											(".$currentsellerid."=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.memberid='".$currentsellerid."')=0 OR (SELECT COUNT(mp.productid) FROM ".tbl_memberproduct." as mp WHERE mp.sellermemberid=".$currentsellerid." and mp.memberid='".$memberid."')=0),

											p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp
												WHERE pbp.channelid = (SELECT channelid FROM member WHERE id='".$memberid."') AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid),

											p.id IN (SELECT mp.productid FROM ".tbl_memberproduct." as mp 
												INNER JOIN ".tbl_membervariantprices." as mvp ON mvp.sellermemberid=mp.sellermemberid AND mvp.memberid=mp.memberid AND mvp.productallow=1 
												WHERE mp.sellermemberid=(SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$currentsellerid."') and mp.memberid=".$currentsellerid." AND mvp.priceid IN (SELECT pp.id FROM ".tbl_productprices." as pp WHERE pp.id=mvp.priceid and pp.productid=mp.productid) AND IFNULL((SELECT count(mpqp.id) FROM ".tbl_memberproductquantityprice." as mpqp WHERE mpqp.membervariantpricesid=mvp.id AND mpqp.salesprice>0),0) > 0 AND (SELECT c.memberbasicsalesprice FROM ".tbl_channel." as c WHERE c.id=(SELECT m.channelid FROM ".tbl_member." as m WHERE m.id=mp.memberid))=1 GROUP BY mp.productid)
										)
								))");
			}else{
				$this->readdb->where("p.id IN (SELECT pbp.productid FROM ".tbl_productbasicpricemapping." as pbp
                WHERE pbp.channelid = '".$channelid."'
                AND IFNULL((SELECT count(pbqp.id) FROM ".tbl_productbasicquantityprice." as pbqp WHERE pbqp.productbasicpricemappingid=pbp.id AND pbqp.salesprice>0),0) > 0 AND pbp.allowproduct = 1 AND pbp.productpriceid IN (SELECT id FROM ".tbl_productprices." WHERE productid=p.id) GROUP BY pbp.productid)");
			}
		}
		/* if($channelid!=0){

			$this->readdb->join(tbl_memberproduct." as mp","mp.productid=p.id AND mp.memberid=".$memberid,"INNER");

			$this->readdb->where("IF(
								 (SELECT count(id) FROM ".tbl_memberproduct." WHERE memberid='".$memberid."' LIMIT 1)>0,
						
								 p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
										WHERE mp.memberid='".$memberid."' AND 
										(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))),
								
								 p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
										INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
										WHERE mp.memberid IN (SELECT mainmemberid FROM membermapping where submemberid='".$memberid."') AND 
										(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0)))
							)");
		} */
		if($channelid!=0){
			$this->readdb->where('p.producttype=0');
		}
		$this->readdb->where('p.status=1 AND p.channelid=0 AND p.memberid=0');
		$this->readdb->group_by('p.id');
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();
							
		return $query->result_array();
	}
	function getMemberSpecificProductByMemberID($memberid){
		
		$this->readdb->select("p.id,p.name,IFNULL((SELECT pi.filename FROM ".tbl_productimage." as pi WHERE pi.productid=p.id ORDER BY pi.priority LIMIT 1),'') as image");
		$this->readdb->from(tbl_product." as p");
		$this->readdb->join(tbl_memberproduct." as mp","mp.productid=p.id AND mp.memberid=".$memberid,"INNER");
		$this->readdb->where("IF(
							(SELECT count(id) FROM ".tbl_memberproduct." WHERE memberid='".$memberid."' LIMIT 1)>0,
				
							p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
								INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
								WHERE mp.memberid='".$memberid."' AND 
								(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0))),
						
							p.id IN ((SELECT mp.productid FROM ".tbl_memberproduct." as mp 
								INNER JOIN ".tbl_member." as m ON m.id=mp.memberid 
								WHERE mp.memberid IN (SELECT mainmemberid FROM membermapping where submemberid='".$memberid."') AND 
								(IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE c.id = `m`.`channelid` AND c.memberspecificproduct=1),0) = 1 OR 0=0)))
					)");
		$this->readdb->where('p.status=1 AND p.producttype=0');
		$this->readdb->order_by("p.name ASC");
		$query = $this->readdb->get();
							
		return $query->result_array();
	}
	
	function CheckMemberEmailAvailable($email, $ID = '',$mainmemberid="") {
		//  $type :- main - main member , sub - sub member
		if($mainmemberid==""){
			$where = "email='".$email."' AND email!='' and reportingto=0";
		}else{
			$where = "email='".$email."' AND email!='' and reportingto=".$mainmemberid;
		}
		
		if (isset($ID) && $ID != '') {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where('id <>',$ID)
			->where($where)
			->get();

		} else {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where($where)
			->get();
		}
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function CheckMemberSocialLoginAvailable($SocialID, $type) {
		
		if($type=="google"){
			$where = "msl.googleid='".$SocialID."'";
		}else{
			$where = "msl.facebookid='".$SocialID."'";
		}
		
		$query = $this->readdb->select("msl.id,msl.memberid,msl.googleid,msl.facebookid,m.name,m.email,m.mobile,m.image,m.channelid")
							->from(tbl_membersociallogin." as msl")
							->join(tbl_member." as m","m.id=msl.memberid","LEFT")
							->where($where)
							->get();
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function CheckChannelLogin($emailid,$password) {
		/* $where = '';
		if($socialid!=''){
			$where = " AND socialid=".$socialid;
		} */
		$query = $this->readdb->select("m.id,m.reportingto,m.channelid,m.membercode,m.emailverified,
									m.roleid,m.status,m.email,
									m.mobile,m.image,m.name,
									(select vendormanagement from ".tbl_systemconfiguration." limit 1) as checkmembermanagement,
									m.password,
									(select website from ".tbl_channel." where id=m.channelid)as website,
									(select mobileapplication from ".tbl_channel." where id=m.channelid)as mobileapplication,
									m.socialid,
									IFNULL((SELECT status FROM ".tbl_channel." where id=m.channelid),0) as ischannelactive,
									IFNULL((SELECT status FROM ".tbl_memberrole." where id=m.roleid),0) as isroleactive,
									
									IFNULL(seller.id,0) as sellerid,
									IFNULL(seller.name,'Company') as sellername,
									IFNULL(seller.channelid,'') as sellerlevel,
									IFNULL(seller.email,'') as selleremail,
									IFNULL(seller.mobile,'') as sellermobile,
									IFNULL(seller.membercode,'') as sellercode,
									IFNULL(seller.image,'') as sellerimage
									")
				->from($this->_table." as m")
				->join(tbl_member." as seller","seller.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = m.id)","LEFT")

				->where("(m.email='".$emailid."' OR m.mobile='".$emailid."')", "", FALSE)
				->get();
			 
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return array();
		}

	}

	function CheckExpirydate(){
		
		$today = strtotime($this->general_model->getCurrentDate());
		if($today>=strtotime(STARTDATE) && $today<=strtotime(EXPIRYDATE)){
			return 1;
		}else{
			return 0;
		}
	}

	function channelresetpassworddata($rcode){
		$this->load->model('Member_model', 'Member');
		$this->Member->_table = tbl_memberemailverification;

		$currentdate =  $this->general_model->getCurrentDateTime();
		$this->readdb->select('id');
	    $this->readdb->from(tbl_memberemailverification);
		$where = "rcode='".$rcode."' AND createddate > '".$currentdate."' - INTERVAL 24 HOUR AND status = 0";
		$this->readdb->where($where);
	    $query = $this->readdb->get();
		$result = array();
		
		if($query->num_rows()>0){

			$this->readdb->select('*');
			$this->readdb->from(tbl_memberemailverification);
			$this->readdb->where('rcode',$rcode);
			$query1 = $this->readdb->get();
			$result = $query1->row_array();
			return $result;
			
		}else{
			$where = "rcode ='".$rcode."' AND createddate < '".$currentdate."' - INTERVAL 24 HOUR";
			$this->Member->Delete($where);
			return $result;
		}
	}
	function CheckMemberMobileAvailable($countrycode,$mobileno, $ID = '',$mainmemberid="") {
		if($mainmemberid==""){
			$where = "countrycode='".$countrycode."' AND mobile='".$mobileno."' AND mobile!='' and reportingto=0";
		}else{
			$where = "countrycode='".$countrycode."' AND mobile='".$mobileno."' AND mobile!='' and reportingto=".$mainmemberid;
		}
		
		if (isset($ID) && $ID != '') {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where('id <>',$ID)
			->where($where)
			->get();

		} else {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where($where)
			->get();
		}
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function CheckMemberWebsiteLinkExist($websitelink, $ID = '') {
		
		$where = "websitelink='".$websitelink."'";
		
		if (isset($ID) && $ID != '') {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where('id <>',$ID)
			->where($where)
			->get();

		} else {
			$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->where($where)
			->get();
		}
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	
	function getMemberData(){

		$this->readdb->select("c.id, c.name,c.image,c.email,c.mobile,c.createddate");
		$this->readdb->from($this->_table." as c");
		
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getMemberOrderData($Memberid){
		$this->readdb->select('o.id,o.orderid,o.status, (select sum(finalprice) from '.tbl_orderproducts.' where orderid = o.id ) as finalprice, o.createddate as date, o.memberid, ca.name as membername,payableamount');
        $this->readdb->from(tbl_productorder." as o");
        $this->readdb->join(tbl_memberaddress." as ca","ca.id=o.addressid","left");
        $this->readdb->where(array('o.memberid'=>$Memberid,'o.usertype'=>1));
        $this->readdb->group_by('o.orderid');
        $this->readdb->order_by('o.id', 'DESC');

        $query = $this->readdb->get();
		
		return $query->result_array();
    }
	function getMemberQuotationData($Memberid){
		$this->readdb->select('q.id,q.quotationid,q.status,q.createddate as date,q.quotationamount,q.memberid');
        $this->readdb->from(tbl_quotation." as q");
        $this->readdb->join(tbl_memberaddress." as ca","ca.id=q.addressid","left");
        $this->readdb->where(array('q.memberid'=>$Memberid));
        $this->readdb->group_by('q.quotationid');
        $this->readdb->order_by('q.id', 'DESC');

        $query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getMemberIdentityproofData($Memberid){
		
		$this->readdb->select('mip.id,mip.memberid,mip.idproof,mip.title,mip.modifieddate,mip.status');
        $this->readdb->from(tbl_memberidproof." as mip");
        $this->readdb->where(array('mip.memberid'=>$Memberid));
        
        $query = $this->readdb->get();
		
		return $query->result_array();
		
    }
	function CheckMemberLogin($username,$password) {

		$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->group_start()
			->where("email = '" . $username. "'")
			->or_where("username = '" . $username. "'")
			->group_end()
			->where("password", $password)
			->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return 0;
		}
	}
	function gerMemberByMobileOrEmail($email,$mobile) {
		
		$where = '1=1';
		if(!empty($email) && !empty($mobile)){
			$where = "(email = '" . $email. "' OR mobile = '" . $mobile. "')";
		}else if(!empty($email) && empty($mobile)){
			$where = "email = '" . $email. "'";
		}else if(empty($email) && !empty($mobile)){
			$where = "mobile = '" . $mobile. "'";
		}
		$query = $this->readdb->select("id,name,email,mobile")
			->from($this->_table)
			->where($where)
			->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	function getMemberDetailOnCRMAPI($employeeid,$memberid){

		$query = $this->readdb->select("m.id as memberid,m.companyname,m.name as membername,
							
					IF(IFNULL((select employeeid from ".tbl_crmassignmember." where memberid=m.id AND (employeeid = ".$employeeid." or employeeid in(select id from ".tbl_user." where reportingto=".$employeeid.")) LIMIT 1),assigntoid)=".$employeeid.",0,1) as inquirymember,

					m.assigntoid as assignto,
					
					m.address,cn.name as country,pr.name as state,ct.name as city,
					m.areaid,IFNULL(ar.areaname,'') as area,
					m.pincode,cn.id as countryid,pr.id as stateid,m.cityid,
					m.latitude,m.longitude,m.leadsourceid,ls.name as leadsourcename,
					m.zoneid,IFNULL(z.zonename,'') as zone,
					m.industryid,IFNULL(ic.name,'') as industryname,

					m.rating,m.remarks,
					
					m.addedby as addedbyid,
					(SELECT name FROM ".tbl_user." WHERE id=m.addedby) as addedbyname,
					m.status as memberstatus,
					m.membertype as type,m.website,m.requirement,m.status,m.createddate
				")
			->from($this->_table." as m")
			->join(tbl_city." as ct","ct.id=m.cityid","LEFT")
			->join(tbl_province." as pr","pr.id=ct.stateid","LEFT")
			->join(tbl_country." as cn","cn.id=pr.countryid","LEFT")
			->join(tbl_area." as ar","ar.id=m.areaid","LEFT")
			->join(tbl_leadsource." as ls","ls.id=m.leadsourceid","LEFT")
			->join(tbl_zone." as z","z.id=m.zoneid","LEFT")
			->join(tbl_industrycategory." as ic","ic.id=m.industryid","LEFT")
			->where("m.id=".$memberid)
			->get();
               
		if ($query->num_rows() == 1) {
			$memberdata = $query->row_array();

			$contactdata = $this->readdb->select("cd.id,cd.firstname,cd.lastname,cd.email,cd.mobileno,
						IF(cd.birthdate!='0000-00-00',cd.birthdate,'') as birthdate,
						IF(cd.annidate!='0000-00-00',cd.annidate,'') as annidate,
						cd.designation,cd.department")
						->from(tbl_contactdetail." as cd")
						->where("cd.memberid=".$memberid)
						->get()->result_array();

			$memberdata['contactdata'] = $contactdata;

			return $memberdata;
		} else {
			return array();
		}
	}
	function getMemberDetail($Memberid){

		$this->readdb->select("m.id,m.channelid,m.name,m.image,m.email,m.mobile,m.createddate,m.debitlimit,m.gstno,m.membercode,m.countrycode,m.emailverified,m.secondarymobileno,m.secondarycountrycode,m.address,
							(SELECT name FROM ".tbl_channel." WHERE id=m.channelid) as channelname,
							IFNULL((select mainmemberid from ".tbl_membermapping." where submemberid=m.id limit 1),0) as sellermemberid,
							IFNULL((select CONCAT(m.name,' (',m.membercode,')') from ".tbl_member." as m INNER JOIN ".tbl_membermapping." as mp ON m.id=mp.mainmemberid AND mp.submemberid=".$Memberid."),'') as sellername,
							IFNULL((select m.channelid from ".tbl_member." as m INNER JOIN ".tbl_membermapping." as mp ON m.id=mp.mainmemberid AND mp.submemberid=".$Memberid."),0) as sellerchannelid,
							IFNULL((select m2.channelid from ".tbl_member." as m2 where m2.id=m.parentmemberid),0) as parentchannelid,
							IFNULL((select CONCAT(m2.name,' (',m2.membercode,')') from ".tbl_member." as m2 where m2.id=m.parentmemberid),'') as parentname,
							IFNULL((select CONCAT(m3.name,' (',m3.membercode,')') from ".tbl_member." as m3 where m3.id=m.referralid),'') as refermembername,
							m.referralid as refermemberid,
							IFNULL((select m3.channelid from ".tbl_member." as m3 where m3.id=m.referralid),0) as referchannelid,
							IFNULL(c.name,'') as cityname,
							IFNULL(p.name,'') as provincename,
							IFNULL(country.name,'') as countryname,
							parentmemberid,minimumstocklimit,
							IFNULL((select mrs.name FROM ".tbl_memberratingstatus." as mrs where mrs.id=m.memberratingstatusid),'') as memberratingstatus,
							paymentcycle,memberratingstatusid,emireminderdays,
							IFNULL((SELECT count(id) FROM ".tbl_memberproduct." where sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$Memberid.") AND memberid='".$Memberid."'),0) as totalproductcount,m.anniversarydate,m.minimumorderamount");
		$this->readdb->from($this->_table." as m");
		$this->readdb->join(tbl_city." as c","c.id=m.cityid","LEFT");
		$this->readdb->join(tbl_province." as p","p.id=m.provinceid","LEFT");
		$this->readdb->join(tbl_country." as country","country.id=p.countryid","LEFT");
		//$this->readdb->join(tbl_memberaddress." as ca","ca.memberid=c.id","LEFT");
		$this->readdb->where("m.id=".$Memberid);
		$query = $this->readdb->get();
		
		return $query->row_array();
	}
	function getMemberShippingDetail($Memberid){

		$this->readdb->select("ca.id, ca.name,ca.address,ca.town, ca.postalcode, ca.email,ca.mobileno,ca.createddate");
		$this->readdb->from(tbl_memberaddress." as ca");
		//$this->readdb->join(tbl_memberaddress." as ca","ca.memberid=c.id","LEFT");
		$this->readdb->group_by('ca.email');
		$this->readdb->where(array('ca.memberid'=>$Memberid));
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getMemberByFirstChannel(){
		$query = $this->readdb->select("GROUP_CONCAT(id) as id")
							->from(tbl_member)
							->where("FIND_IN_SET(channelid, (SELECT id FROM ".tbl_channel." WHERE status=1 ORDER BY priority ASC LIMIT 1)) AND status=1")
							->order_by("name ASC")
							->get();

		return $query->row_array();
	}
	function getMemberOnFirstLevelUnderCompany(){
		
		$query = $this->readdb->select("id, name, mobile, membercode,
							(CONCAT(membercode,' (+',countrycode,' ',mobile,')')) as membername,
							CONCAT(name,' (',membercode,')') as namewithcode,
							CONCAT(name,' (',membercode,' - ',mobile,')') as namewithcodeormobile,
							CONCAT(name,' (',email,')') as namewithemail,
							membercode,billingaddressid,shippingaddressid
						")
							->from(tbl_member)
							->where("FIND_IN_SET(channelid, (SELECT id FROM ".tbl_channel." WHERE status=1 ORDER BY priority ASC LIMIT 1)) AND status=1")
							->order_by("name ASC")
							->get();

		return $query->result_array();
	}
	function getActiveMemberByChannel($channelid,$memberid='',$notmemberid='0',$vendor=0,$provinceid=0,$cityid=0){
		$this->readdb->select("id,CONCAT(name, IF(membercode!='',CONCAT(' (',membercode,')'),'')) as name,
		,(CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')')) as namewithcodeormobile,
		(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=m.id LIMIT 1) as sellerid,membercode,channelid");
		$this->readdb->from(tbl_member." as m");
		$this->readdb->where("status=1");
		if($vendor == 0 && $channelid!=VENDORCHANNELID){
			$this->readdb->where("m.channelid != '".VENDORCHANNELID."'");
		}
		$this->readdb->where("(FIND_IN_SET(channelid,'".$channelid."')>0 OR '".$channelid."'='')");
		$this->readdb->where("(FIND_IN_SET(id,'".$memberid."')>0 OR '".$memberid."'='')");
		$this->readdb->where("(id NOT IN (".$notmemberid.") OR '".$notmemberid."'='0')");
		$this->readdb->where("(provinceid IN (".$provinceid.") OR '".$provinceid."'='0')");
		$this->readdb->where("(cityid IN (".$cityid.") OR '".$cityid."'='0')");
		$this->readdb->order_by("name ASC");
		$query = $this->readdb->get();

		return $query->result_array();
	}
	function getActiveMemberByUnderMember($channelid,$type=""){
		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$REPORTINGTO = $this->session->userdata(base_url().'REPORTINGTO');

		$this->readdb->select("id,CONCAT(name, IF(membercode!='',CONCAT(' (',membercode,')'),'')) as name,(CONCAT(name,' (',membercode,' - ',mobile,')')) as namewithcodeormobile,channelid");
		$this->readdb->from(tbl_member);
		if($channelid!=$CHANNELID){
			if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){

				if($type=="multiplesellerchannel"){

					$this->readdb->where("status=1 AND FIND_IN_SET(channelid, '".$channelid."')>0 AND id IN (SELECT sellermemberid FROM ".tbl_orders." where memberid = ".$MEMBERID." AND isdelete='0' GROUP BY sellermemberid)");
					
				}else{
					
					$this->readdb->where("status=1 AND channelid='".$channelid."' AND id IN (SELECT memberid FROM ".tbl_orders." where sellermemberid = ".$MEMBERID." AND isdelete='0' GROUP BY memberid)");
				}
			}else{
				$this->readdb->where("status=1 AND channelid=".$channelid." AND id in(select submemberid from ".tbl_membermapping." where mainmemberid=".$MEMBERID.")");
			}
		}else{
			$this->readdb->where("status=1 AND (id=".$MEMBERID." OR id=".$REPORTINGTO.")" );
		}
		
		$this->readdb->order_by("name ASC");
		$query = $this->readdb->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}
	function getActiveMemberByUnderMemberOnAPI($MEMBERID,$CHANNELID,$channelid,$type=""){
		
		$this->load->model("Channel_model","Channel");
		$channeldata = $this->Channel->getChannelDataByID($CHANNELID);
		
		$this->readdb->select("id,CONCAT(name, IF(membercode!='',CONCAT(' (',membercode,')'),'')) as name,(CONCAT(name,' (',membercode,' - ',mobile,')')) as namewithcodeormobile");
		$this->readdb->from(tbl_member);
		if($channelid!=$CHANNELID){
			if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && $channeldata['multiplememberwithsamechannel']==1 && $channeldata['multiplememberchannel']!=''){

				if($type=="multiplesellerchannel"){

					$this->readdb->where("status=1 AND FIND_IN_SET(channelid, '".$channelid."')>0 AND id IN (SELECT sellermemberid FROM ".tbl_orders." where memberid = ".$MEMBERID." AND isdelete='0' GROUP BY sellermemberid)");
					
				}else{
					
					$this->readdb->where("status=1 AND channelid='".$channelid."' AND id IN (SELECT memberid FROM ".tbl_orders." where sellermemberid = ".$MEMBERID." AND isdelete='0' GROUP BY memberid)");
				}
			}else{
				$this->readdb->where("status=1 AND channelid=".$channelid." AND id in(select submemberid from ".tbl_membermapping." where mainmemberid=".$MEMBERID.")");
			}
		}else{
			$this->readdb->where("status=1 AND (id=".$MEMBERID." OR id=(SELECT reportingto FROM ".tbl_member." WHERE id=".$MEMBERID."))" );
		}
		
		$this->readdb->order_by("name ASC");
		$query = $this->readdb->get();

		return $query->result_array();
	}
	function getActiveMemberByUpperMember($channelid){
		
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		
		$this->readdb->select("id,name");
		$this->readdb->from(tbl_member);
		$this->readdb->where("status=1 AND id in(select mainmemberid from ".tbl_membermapping." where submemberid=".$MEMBERID.")");
		
		$this->readdb->order_by("name ASC");
		$query = $this->readdb->get();

		return $query->result_array();
	}
	function getreferralhistory($memberid){

		$query = $this->readdb->select("m.id,m.name,m.email,m.mobile as mobileno,m.image,
									IFNULL((SELECT CONCAT(mm.address,', ',p.name,', ',c.name) FROM ".tbl_memberaddress." as mm INNER JOIN ".tbl_province." as p ON p.id=mm.provinceid INNER JOIN ".tbl_city." as c on c.id=mm.cityid WHERE memberid = m.id LIMIT 1),'') as address,
									IFNULL((SELECT c.name FROM ".tbl_channel." as c WHERE c.id=m.channelid),'') as levelname
									")
							->from($this->_table." as m")
							->where("m.status=1 AND m.referralid=".$memberid)
							->get();
		//echo $this->readdb->last_query();exit;
		return $query->result_array();
	}

	//LISTING DATA
	function _get_datatables_query(){

		$channelid = $_REQUEST['channelid'];
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
		$sellermemberid = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):0;

		$this->readdb->select("DISTINCT(m.id),
					m.channelid,m.name,m.membercode,m.countrycode,m.mobile,m.email,m.status,m.createddate,m.emailverified,
					m.secondarymobileno,m.secondarycountrycode,
					(select count(id) from ".tbl_cart." where memberid=m.id)as cartcount,
					IFNULL(parent.name,'Company') as parentmembername,
					IFNULL(parent.membercode,'') as parentcode,
					IFNULL(parent.id,'') as parentid,
					IFNULL(parent.channelid,'0') as parentchannelid,

					IFNULL(ob.id,0) as balanceid,
					IFNULL(ob.balancedate,'') as balancedate,
					IFNULL(ob.balance,0) as balance,

					IFNULL(seller.id,'') as sellerid,
					IFNULL(seller.channelid,'0') as sellerchannelid,
					IFNULL(seller.name,'Company') as sellername,
					IFNULL(seller.membercode,'') as sellercode
					");
					
		$this->readdb->from($this->_table." as m");
		$this->readdb->join($this->_table." as seller","seller.id IN (select mainmemberid from ".tbl_membermapping." where submemberid=m.id)","LEFT");
		$this->readdb->join($this->_table." as parent","parent.id=m.parentmemberid","LEFT");
		$this->readdb->join(tbl_openingbalance." as ob","ob.memberid=m.id AND ob.sellermemberid=".$sellermemberid,"LEFT");
		$this->readdb->where("m.channelid != '".VENDORCHANNELID."'");
		$where='';
		if(!is_null($MEMBERID)) {
			//$where .= " AND m.id in(select submemberid from ".tbl_membermapping." where mainmemberid=".$MEMBERID.")";
			$where .= " AND (m.id in(select memberid from ".tbl_orders." where sellermemberid=".$MEMBERID." AND isdelete=0) OR m.id in(select submemberid from ".tbl_membermapping." where mainmemberid=".$MEMBERID."))";
		}else{
			//$where .= " AND c.reportingto=0";
		}
        if($channelid != 0){
			if(is_array($channelid)){
				$channelid = implode(",",$channelid);
			}
			$where .= ' AND m.channelid IN ('.$channelid.')';
		}
		//echo $where; exit;
		$this->readdb->where("date(m.createddate) BETWEEN '".$startdate."' AND '".$enddate."'".$where);
		//$this->readdb->join(tbl_memberaddress." as ca","ca.userid=c.id","INNER");
		// $this->readdb->group_by('c.name');

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

	function get_datatables() {
		$this->_get_datatables_query();
		
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->_get_datatables_query();
		return $this->readdb->count_all_results();
	}

	//LISTING DATA
	function _getorder_datatables_query(){
		
		if(isset($_REQUEST['memberid'])){
			$memberid=$this->readdb->escape($_REQUEST['memberid']);
		}else{
			$memberid=$this->readdb->escape(0);
		}

		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$status = $_REQUEST['status'];
		
		$this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel');
        $this->readdb->from(tbl_systemconfiguration." as s"); 
        $this->readdb->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")","INNER");
		$systemconfiguration = $this->readdb->get()->row_array();
		
		
		$this->readdb->select('o.id,o.orderid,o.status, (select sum(finalprice) from '.tbl_orderproducts.' where orderid = o.id ) as finalprice,o.orderdate, o.createddate as date, o.memberid,m.name as membername,m.membercode,m.channelid,payableamount,(select name from '.tbl_member.' where id=sellermemberid)as sellermembername,(select membercode from '.tbl_member.' where id=sellermemberid)as sellercode,(select channelid from '.tbl_member.' where id=sellermemberid)as sellerchannelid,sellermemberid');
		
		$this->readdb->from(tbl_productorder." as o");
		$this->readdb->join(tbl_memberaddress." as ca","ca.id=o.addressid","left");
		$this->readdb->join(tbl_member." as m","m.id=o.memberid","left");
        //$this->readdb->where(array('o.customerid'=>$memberid,'o.usertype'=>1));
        
		
		$where = '';
		if($systemconfiguration['allowmultiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!=''){
			if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='0'){
               
				//Sales Order
				$where = ' AND o.sellermemberid='.$memberid.' AND FIND_IN_SET(m.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id=(SELECT channelid FROM '.tbl_member.' WHERE id='.$memberid.')))>0'; 
				$this->column_orderorder[1]='(select name from '.tbl_member.' where id=sellermemberid)';
				$this->column_searchorder[0]='((select name from '.tbl_member.' where id=sellermemberid))';
			}else if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1') {
                
                 //Purchase Order
                $where = ' AND o.memberid = '.$memberid; 
            }
        }else{
			if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='0'){
				$where .= ' AND (o.memberid='.$memberid.')';
				$this->column_orderorder[1]='(select name from '.tbl_member.' where id=sellermemberid)';
				$this->column_searchorder[0]='((select name from '.tbl_member.' where id=sellermemberid))';
			}else if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1') {
				$where .= ' AND o.sellermemberid='.$memberid;
				//$where .= ' AND (o.sellermemberid='.$memberid.' OR o.sellermemberid='.$reportingto.')';
			}
		}
		/* $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		if(!is_null($MEMBERID)){
			$where .= " AND (((o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.") OR o.sellermemberid=".$MEMBERID.") AND 

			(o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.") OR o.memberid=0) AND (o.addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$memberid.") OR o.addedby=".$memberid.") AND o.addedby!=0) OR ((o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR o.memberid=".$memberid.") AND 

			(o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.") OR o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR o.sellermemberid=0) AND (o.addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$memberid.") OR o.addedby=".$memberid.") AND o.addedby!=0)) ";
		} */
		if($status != -1){
            $where .= ' AND o.status='.$status;
		}
		$this->readdb->where("o.orderdate BETWEEN '".$startdate."' AND '".$enddate."'".$where);
		$this->readdb->group_by('o.orderid');
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_searchorder as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_searchorder) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_orderorder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_orderorder)) {
			$order = $this->_orderorder;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}
	//LISTING DATA
	function _getproduct_datatables_query(){
		
	}
	//LISTING DATA
	function _getquotation_datatables_query(){
			
		if(isset($_REQUEST['memberid'])){
			$memberid=$this->readdb->escape($_REQUEST['memberid']);
		}else{
			$memberid=$this->readdb->escape(0);
		}

		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
		$enddate = $this->general_model->convertdate($_REQUEST['enddate']);
		$status = $_REQUEST['status'];

		$this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel');
        $this->readdb->from(tbl_systemconfiguration." as s"); 
        $this->readdb->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")","INNER");
		$systemconfiguration = $this->readdb->get()->row_array();
		
		$this->readdb->select('q.id,q.quotationid,q.quotationdate,q.status,q.createddate as date,q.payableamount,q.quotationamount,q.memberid,m.name as membername,m.membercode,m.channelid,(select name from '.tbl_member.' where id=sellermemberid)as sellermembername,(select membercode from '.tbl_member.' where id=sellermemberid)as sellercode,(select channelid from '.tbl_member.' where id=sellermemberid)as sellerchannelid,sellermemberid');
        $this->readdb->from(tbl_quotation." as q");
		$this->readdb->join(tbl_memberaddress." as ca","ca.id=q.addressid","left");
		$this->readdb->join(tbl_member." as m","m.id=q.memberid","left");
		
		$where = '';
		if($status != -1){
			$where .= ' AND q.status='.$status;
		}
		
		if($systemconfiguration['allowmultiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!=''){
			if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='0'){
               
				//Sales Order
				$where .= ' AND q.sellermemberid='.$memberid.' AND FIND_IN_SET(m.channelid, (SELECT c.multiplememberchannel FROM '.tbl_channel.' as c WHERE c.id=(SELECT channelid FROM '.tbl_member.' WHERE id='.$memberid.')))>0'; 
				$this->column_orderquotation[1]='(select name from '.tbl_member.' where id=sellermemberid)';
				$this->column_searchquotation[0]='((select name from '.tbl_member.' where id=sellermemberid))';
			}else if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1') {
                
                 //Purchase Order
                $where .= ' AND q.memberid = '.$memberid; 
            }
        }else{
			if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='0'){
				$where .= ' AND (q.memberid='.$memberid.')';
				$this->column_orderquotation[1]='(select name from '.tbl_member.' where id=sellermemberid)';
				$this->column_searchquotation[0]='((select name from '.tbl_member.' where id=sellermemberid))';
			}else if(isset($_REQUEST['displaytype']) && $_REQUEST['displaytype']=='1') {
				$where .= ' AND q.sellermemberid='.$memberid;
			}
		}
		/* $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		if(!is_null($memberid)){
			$where .= " AND (((q.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.") OR q.sellermemberid=".$memberid.") AND 

			(q.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR q.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.") OR q.memberid=0) AND (q.addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$memberid.") OR q.addedby=".$memberid.") AND q.addedby!=0) OR ((q.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR q.memberid=".$memberid.") AND 

			(q.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.") OR q.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR q.sellermemberid=0) AND (q.addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$memberid.") OR q.addedby=".$memberid.") AND q.addedby!=0)) ";
		} */
		$this->readdb->where("q.quotationdate BETWEEN '".$startdate."' AND '".$enddate."'".$where);
		$this->readdb->group_by('q.quotationid');
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_searchquotation as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_searchquotation) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_orderquotation[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_orderquotation)) {
			$order = $this->_orderquotation;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}
	
	function getorder_datatables() {
		$this->_getorder_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		//echo  $this->readdb->last_query(); exit;
		return $query->result();
	}
	function countorder_filtered() {
		$this->_getorder_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function countorder_all() {
		$this->_getorder_datatables_query();
		return $this->readdb->count_all_results();
	}
	function getquotation_datatables() {
		$this->_getquotation_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		return $query->result();
	}
	function countquotation_filtered() {
		$this->_getquotation_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function countquotation_all() {
		$this->_getquotation_datatables_query();
		return $this->readdb->count_all_results();
	}
	function getbillingaddress_datatables() {
		$this->_getbillingaddress_datatables_query();
		if($_POST['length'] != -1)
		$this->readdb->limit($_POST['length'], $_POST['start']);
		$query = $this->readdb->get();
		//echo $this->readdb->last_query(); exit;
		return $query->result();
	}
	//LISTING DATA
	function _getbillingaddress_datatables_query(){
			
		if(isset($_REQUEST['memberid'])){
			$memberid=$this->readdb->escape($_REQUEST['memberid']);
		}else{
			$memberid=$this->readdb->escape(0);
		}
		
		$this->readdb->select("ma.id, ma.name,ma.address,ma.town, ma.postalcode, ma.email,ma.mobileno,ma.createddate,ma.status");
		$this->readdb->from(tbl_memberaddress." as ma");
		$this->readdb->where('(ma.addedby='.$memberid.' OR ma.memberid='.$memberid.') AND ma.status <> 2');
		// $this->readdb->group_by('ma.email');
		
		$i = 0;

		if($_POST['search']['value']) { 
			foreach ($this->column_searchbillingaddress as $item) { // loop column 
				if($_POST['search']['value']) { // if datatable send POST for search
					if($i === 0) { // first loop
						$this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
						
						$this->readdb->like($item, $_POST['search']['value']);
					} else {
						$this->readdb->or_like($item, $_POST['search']['value']);
					}

					if(count($this->column_searchbillingaddress) - 1 == $i) //last loop
						$this->readdb->group_end(); //close bracket
				}
				$i++;
			}
		}
		
		if(isset($_POST['order'])) { // here order processing
			$this->readdb->order_by($this->column_orderbillingaddress[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} else if(isset($this->_orderbillingaddress)) {
			$order = $this->_orderbillingaddress;
			$this->readdb->order_by(key($order), $order[key($order)]);
		}
	}
	function countgetbillingaddress_filtered() {
		$this->_getbillingaddress_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function countgetbillingaddress_all() {
		$this->_getbillingaddress_datatables_query();
		return $this->readdb->count_all_results();
	}
	function searchproduct($search){

		$this->readdb->select("id as productid,name as text");
		$this->readdb->from(tbl_product);
		$this->readdb->where("name LIKE ".$this->readdb->escape("%".$search."%"));
		$query = $this->readdb->get();
		
		return $query->result_array();	
	}
	
	function getparentchannelmembers($channelid){

		$query = $this->readdb->select("m.id,m.name,email")
						->from(tbl_channel." as c")
						->join(tbl_member." as m","c.id=m.channelid","left")
						->where(array("c.id = (SELECT (SELECT c2.id FROM ".tbl_channel." as c2 WHERE c2.id!=c1.id and c2.priority<=c1.priority ORDER BY priority DESC LIMIT 1) FROM ".tbl_channel." as c1 WHERE c1.id='".$channelid."')"=>null,"m.status"=>1,"c.id!="=>GUESTCHANNELID))
						->order_by("m.name ASC")
						->get();
		//echo $this->readdb->last_query();exit;
		return $query->result_array();	
	}
	function getMemberListInUnderChannel($memberid){

		$this->readdb->select("m.id,m.name,email");
		$this->readdb->from(tbl_member." as m");
		$this->readdb->where(array("m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.")"=>null));
		$this->readdb->order_by("m.name ASC");
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result_array();	
	}
	function getMultipleChannelMembers($channelid,$brandid){

		$channelid = (!empty($channelid) && is_array($channelid))?implode(",", $channelid):$channelid;
		
		$this->readdb->select("m.id,CONCAT(m.name,' (',m.membercode,' - ',(SELECT name FROM ".tbl_channel." WHERE id=m.channelid),')') as name,m.email");
		$this->readdb->from($this->_table." as m");

		if($brandid == 0){

			$this->readdb->where(array("FIND_IN_SET(m.channelid, '".$channelid."')>0"=>null,"status"=>1));
		}else{
			$this->readdb->where(
				array(
					"status"=>1,
					'm.id IN (SELECT memberid FROM '.tbl_memberproduct.' WHERE 
								productid IN (SELECT id FROM '.tbl_product.' WHERE brandid="'.$brandid.'")) AND
						m.id IN (SELECT mvp.memberid FROM '.tbl_membervariantprices.' as mvp
							INNER JOIN '.tbl_productprices.' as pp ON pp.id = mvp.priceid
							INNER JOIN '.tbl_product.' as p ON p.id = pp.productid
							WHERE FIND_IN_SET(mvp.channelid, "'.$channelid.'")>0 AND p.brandid = "'.$brandid.'" 
							)'
					=>null
				)
			);
			$this->readdb->group_by("m.id");
		}
		$this->readdb->order_by("m.name ASC");
		$query = $this->readdb->get();
		// echo $this->readdb->last_query();exit;
		return $query->result_array();	
	}
	function getMultipleChannelMembersOnChannel($channelid){

		$channelid = (!empty($channelid))?implode(",", $channelid):$channelid;
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		
		$query = $this->readdb->select("m.id,CONCAT(m.name,' (',(SELECT name FROM ".tbl_channel." WHERE id=m.channelid),')') as name,email")
					->from($this->_table." as m")
					->where(array("FIND_IN_SET(m.channelid, '".$channelid."')>0"=>null,"status"=>1,'(m.id in(select memberid from '.tbl_orders.' where sellermemberid='.$MEMBERID.' AND isdelete=0) OR m.id in(select submemberid from '.tbl_membermapping.' where mainmemberid='.$MEMBERID.'))'=>null))
					->order_by("m.name ASc")
					->get();
		//echo $this->readdb->last_query();exit;
		return $query->result_array();	
	}
	
	function getmainmember($memberid,$type="row"){
		$this->readdb->select("mainmemberid as id,name,m.channelid");
		$this->readdb->from(tbl_membermapping." as mm");
		$this->readdb->join(tbl_member." as m","m.id=mm.mainmemberid");
		$this->readdb->where(array("submemberid"=>$memberid));
		$this->readdb->limit(1);
		$query = $this->readdb->get();
		if($type=="row"){
			return $query->row_array();	
		}else{
			return $query->result_array();
		}
	}

	function getUserListData($where=array()){

		$query = $this->readdb->select("id,name as username,
									IFNULL((SELECT GROUP_CONCAT(role SEPARATOR ' | ') FROM ".tbl_memberrole." WHERE id=s.roleid),'' )as role,
									image,email,mobile as mobileno,status")
				->from($this->_table." as s")->where($where)
				->order_by("s.id","DESC")
				->get();
	
		return $query->result_array();
	}
	public function insertmemberemailverification($id,$code){
        
        $adminuserid = $id;
		
		$this->load->model('Member_model', 'Member');
		$this->Member->_table = tbl_memberemailverification;
		$this->Member->_fields = 'id';
		$this->Member->_where = array('memberid'=>$id,'status'=>0);
		$memberdata = $this->Member->getRecordsByID();

        $createddate = $this->general_model->getCurrentDateTime();
        if(!empty($memberdata)){
            
            $updatedata=array('rcode'=>$code,
						'createddate'=>$createddate);
			
			$this->Member->_where = array('id'=>$memberdata['id']);
            $this->Member->Edit($updatedata);
        }else{
            $insertdata=array('memberid'=>$adminuserid,
								'rcode'=>$code,
								'createddate'=>$createddate);
			$this->Member->Add($insertdata);
        }
	}
	
	function getMemberCodeOrDetails($memberid){

		$query = $this->readdb->select("id,channelid as level,name,email,mobile,membercode,
									IFNULL(
										((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
										+
										(SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=4)
										+
										(SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id  AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))
										-
										(SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.frommemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
										-
										(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
										+
										(SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
										+
										(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
										
									,0)	as totalpoint")
					->from(tbl_member." as m")
					->where("id IN (select submemberid from ".tbl_membermapping." where mainmemberid=".$memberid.") AND status=1")
					->get();
		/* IFNULL(
			(SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=".$memberid." AND type=0 AND rh.id not in (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh.id) AND rh.id not in (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh.id))
			+
			(SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.tomemberid=".$memberid." AND type=0 AND (rh.id in (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh.id AND (o.status=1 OR o.status=2)) OR rh.id in (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh.id AND (o.status=1 OR o.status=2))))
			-
			(SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=".$memberid." AND type=1 AND rh.id not in (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh.id) AND rh.id not in (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh.id))
			-
			(SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh WHERE rh.frommemberid=".$memberid." AND type=1 AND rh.id in (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh.id AND (o.status=1 OR o.status=2)) )
		,0) */
		return $query->result_array();
	}
	function getMemberRewardHistory($memberid,$counter){

		$query = $this->readdb->select("rh.point,rh.rate,rh.type,rh.detail,
									DATE(rh.createddate) as date ,rh.createddate,
									IFNULL(o.id,'') as orderid,
									IFNULL(o.orderid,'') as orderno,
									IFNULL(o.amount+o.taxamount,0) as amount,
									IFNULL(o.status,'-1') as orderstatus,
									IFNULL(o.approved,'') as orderapprovestatus,

									IFNULL(seller.name,'Company') as sellername,
									IFNULL(seller.email,'') as selleremail,
									IFNULL(seller.mobile,'') as sellermobileno,
									IFNULL(seller.membercode,'') as sellercode,

									IFNULL(buyer.name,'') as buyername,
									IFNULL(buyer.email,'') as buyeremail,
									IFNULL(buyer.mobile,'') as buyermobileno,
									IFNULL(buyer.membercode,'') as buyercode,

									IFNULL(
										((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
										+
										(SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
										+
										(SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype=4)
										+
										(SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid."  AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))
										-
										(SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.frommemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
										-
										(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
										+
										(SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
										+
										(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
										
									,0) as closingpoint,

								
							")
					->from(tbl_rewardpointhistory." as rh")
					->join(tbl_member." as m","m.id=".$memberid." AND status=1 AND (rh.frommemberid=m.id OR rh.tomemberid=m.id)","INNER")
					->join(tbl_orders." as o","(o.memberrewardpointhistoryid=rh.id or o.sellermemberrewardpointhistoryid=rh.id or o.redeemrewardpointhistoryid=rh.id or or o.samechannelreferrermemberpointid=rh.id) AND o.isdelete=0","LEFT")
					->join(tbl_offerparticipants." as op","op.redeemrewardpointhistoryid=rh.id AND op.status=1","LEFT")
        			->join(tbl_creditnote." as c","c.redeemrewardpointhistoryid=rh.id AND c.status=1","LEFT")
                	->join(tbl_member." as buyer","(buyer.id=o.memberid OR buyer.id=op.memberid OR buyer.id=c.buyermemberid)","LEFT")
					->join(tbl_member." as seller","(seller.id=o.sellermemberid OR seller.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=op.memberid) OR seller.id=c.sellermemberid)","LEFT")
					->order_by("rh.id DESC")
					->limit(50,$counter)
					->get();
		//echo $this->readdb->last_query();exit;

		/* IFNULL(
			(SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid=".$memberid." AND type=0 AND rh2.id not in (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id) AND rh2.id not in (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id) OR rh2.id not in (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.status!=2))
			+
			(SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid=".$memberid." AND type=0 AND (rh2.id in (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1) OR rh2.id in (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1)))
			-
			(SELECT IFNULL(SUM(point),0) as withoutorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.frommemberid=".$memberid." AND type=1 AND rh2.id not in (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1) AND rh2.id not in (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1))
			-
			(SELECT IFNULL(SUM(point),0) as withorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid=".$memberid." AND type=1 AND rh2.id in (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1) )

		,0) as closingpoints, */
		return $query->result_array();
	}
	function getCountRewardPoint($memberid){

		$query = $this->readdb->select("IFNULL(
										((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
										+
										(SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
										+
										(SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype=4)
										+
										(SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid."  AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))
										-
										(SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.frommemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
										-
										(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
										+
										(SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
										+
										(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
										
									,0) as rewardpoint
									
								")
				
				->get();
		
		if($query->num_rows() > 0){
			return $query->row_array();
		} else {
			return array();
		}
	}
	function getdealerrecord($memberid,$channelid,$counter,$search="",$isupper) {
		
		$limit = 10;
		$this->load->model("Channel_model","Channel"); 
		$channel = $this->Channel->getChannelIDByFirstLevel();
		$channeldata = $this->Channel->getChannelDataByID($channelid);
		
		if($isupper == "true" && $channelid==$channel['id']){
			if($channeldata['showupperdirectory']==1){
				$this->readdb->select('s.id,s.businessname as name,0 as level,0 as roleid,s.email,s.mobileno as mobile,"" as secondarymobileno,
								s.companycode as membercode,
								"" as image,
								c.name as cityname,
								pr.name as provincename, 
								"" as latitude,
								"" as longitude,
								"" as password,
								pr.countryid,c.stateid,s.cityid, "" as gstno, "" as panno,"" as minstocklimit,"" as emireminderdays,
							');		
				
				$this->readdb->from(tbl_settings." as s");
				$this->readdb->join(tbl_city." as c","c.id=s.cityid", "LEFT");
				$this->readdb->join(tbl_province." as pr","pr.id=c.stateid", "LEFT");
				$this->readdb->where('s.id=1');
				if($search!=""){
					$this->readdb->where("(s.businessname LIKE CONCAT('%','".$search."','%') OR s.mobileno LIKE CONCAT('%','".$search."','%') OR s.email LIKE CONCAT('%','".$search."','%') OR s.companycode LIKE CONCAT('%','".$search."','%') OR c.name LIKE CONCAT('%','".$search."','%') OR pr.name LIKE CONCAT('%','".$search."','%'))");
				}
				$this->readdb->limit(1);
				
				$query = $this->readdb->get();

				if($query->num_rows() == 0){
					return array();
				} 
				 else {
					$Data = $query->result_array();
					$json = array();
					foreach ($Data as $row) {
						$row['addressdetail'] = array();
						$row['balancedetail'] = (object)array();
						$json[] = $row;
					}
					return $json;
				}
			}else{
				return array();
			}
		}else{
			$this->readdb->select('m.id,m.name,m.channelid as level,m.roleid,m.email,m.mobile,m.secondarymobileno,m.membercode,m.image,
							IFNULL(c.name,"") as cityname,
							IFNULL((SELECT name FROM '.tbl_province.' WHERE id=c.stateid),"") as provincename, 
							IFNULL(c.latitude,"") as latitude,
							IFNULL(c.longitude,"") as longitude,
							m.password,IFNULL(pr.countryid, 0) as countryid,IFNULL(c.stateid,0) as stateid,IFNULL(m.cityid,0) as cityid,m.gstno,m.panno,m.minimumstocklimit as minstocklimit,m.emireminderdays,m.debitlimit,m.paymentcycle
							
						');		
			
			$this->readdb->from($this->_table." as m");
			$this->readdb->join(tbl_city." as c","c.id=m.cityid", "LEFT");
			$this->readdb->join(tbl_province." as pr","pr.id=c.stateid", "LEFT");
			$this->readdb->where('m.status = 1');
			if($search!=""){
				$this->readdb->where("(m.name LIKE CONCAT('%','".$search."','%') OR m.mobile LIKE CONCAT('%','".$search."','%') OR m.email LIKE CONCAT('%','".$search."','%') OR m.membercode LIKE CONCAT('%','".$search."','%') OR m.secondarymobileno LIKE CONCAT('%','".$search."','%') OR c.name LIKE CONCAT('%','".$search."','%') OR pr.name LIKE CONCAT('%','".$search."','%'))");
			}
			if($isupper == "true"){
				if($channeldata['showupperdirectory']==1){
					$this->readdb->where('(m.channelid = (SELECT id FROM '.tbl_channel.' WHERE id < '.$channelid.' ORDER BY id DESC LIMIT 1)) AND m.status=1');
				}else{
					$this->readdb->where('m.id=0');
				}
			}else{
				$this->readdb->where('m.id IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$memberid.') AND m.status=1');
			}
			
			$this->readdb->order_by("m.id","DESC");
			if($counter != -1){
				$this->readdb->limit($limit,$counter);
			}    
			$query = $this->readdb->get();

			if($query->num_rows() == 0){
				return array();
			}else {
				$Data = $query->result_array();
				$json = array();
				$this->load->model("Customeraddress_model","Member_address"); 
				$this->load->model("Opening_balance_model","Opening_balance"); 
				foreach ($Data as $row) {
					$row['password'] = $this->general_model->decryptIt($row['password']);
					$addressdetail =  $this->Member_address->getaddress($row['id']); 
					$addressdetailarr = array();
					if(!empty($addressdetail)){
						foreach($addressdetail as $address){
							$addressdetailarr[] = array("addressid"=>$address['id'],
													"name"=>$address['membername'],
													"email"=>$address['email'],
													"mobileno"=>$address['mobileno'],
													"address"=>$address['address'],
													"postalcode"=>$address['postalcode'],
													"billingid"=>$address['billingid'],
													"shippingid"=>$address['shippingid'],
												);
						}
					}
					$row['addressdetail'] = $addressdetailarr;
					
					$balancedetailarr = array();
					$balancedetail = $this->Opening_balance->getOpeningBalanceDetailByMember($row['id']);
					if(!empty($balancedetail)){
						$balancedetailarr = array("openingdate"=>$balancedetail['balancedate'],
													"openingbalance"=>$balancedetail['balance'],
													"debitlimit"=>$row['debitlimit'],
													"paymentcycle"=>$row['paymentcycle']
												);
					}
					$row['balancedetail'] = (object)$balancedetailarr;

					unset($row['debitlimit']);
					unset($row['paymentcycle']);
					$json[] = $row;
				}
				
				return $json;
			}
		}
		
		
	}
	function getChannelSettingsByMemberID($memberid){

		$query = $this->readdb->select("c.productwisepoints,
									c.productwisepointsmultiplywithqty,
									c.productwisepointsforseller,
									c.productwisepointsforbuyer,
									c.overallproductpoints,
									c.sellerpointsforoverallproduct,
									c.buyerpointsforoverallproduct,
									c.mimimumorderqtyforoverallproduct,
									c.pointsonsalesorder,
									c.sellerpointsforsalesorder,
									c.buyerpointsforsalesorder,
									c.mimimumorderamountforsalesorder,
									c.conversationrate,
									IFNULL((SELECT conversationrate FROM ".$this->_table." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid."))),0) as referrerconversationrate,
									c.minimumpointsonredeem,
									c.minimumpointsonredeemfororder,
									c.mimimumpurchaseorderamountforredeem,
									c.edittaxrate,
									c.advancepaymentcod,
									c.advancepaymentpriority,
									IF(c.advancepaymentpriority=0,c.advancepaymentcod,(SELECT advancepaymentcod FROM ".tbl_member." WHERE id=".$memberid.")) as advancepaymentcodfororder

								")
						->from(tbl_channel." as c")
						->where(array("c.id = (SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")"=>null))
						->get();
		
		if($query->num_rows() == 1) {
			return $query->row_array();
		}else{
			return array();
		}
	}

	/* function getMemberSalesOrders($memberid,$from=""){

		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$where = "";
		if(!is_null($MEMBERID)){
			$where .= " AND o.sellermemberid=".$MEMBERID;
		}else{
			$where .= " AND o.sellermemberid=0";
		}
		if($from!="" && $from=="invoice"){
			$where .= " AND 
				IFNULL((SELECT SUM(quantity) FROM ".tbl_orderproducts." where orderid = o.id),0) 
				
				> 
				IFNULL((SELECT SUM(tp.quantity) 
				FROM ".tbl_transactionproducts." as tp 
				INNER JOIN ".tbl_orderproducts." as op ON op.id=tp.referenceproductid 
				where FIND_IN_SET(tp.transactionid, (SELECT GROUP_CONCAT(id) FROM ".tbl_invoice." where FIND_IN_SET(o.id, orderid)>0 AND status!=2))>0 AND transactiontype=3),0)";
		}
		$query = $this->readdb->select("o.id,o.orderid,o.addressid as billingid,o.shippingaddressid as shippingid")
						->from(tbl_orders." as o")
						->where("o.memberid=".$memberid." AND status IN (0,1) AND approved=1".$where)
						->order_by("o.id", "DESC")
						->get();
		// echo $this->readdb->last_query(); exit;
		if($query->num_rows() > 0) {
			return $query->result_array();
		}else{
			return array();
		}
	} */

	function searchMemberCode($memberid,$channelid,$searchcode,$type){

		$where = '';
		if($type==1){
			$where = " AND m.id!=".$memberid;
		}else if($type==2){
			$where = " AND m.id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid = ".$memberid." AND isdelete=0) AND m.id!=".$memberid;
		}else if($type==0 || $type=="3"){
			$where = " AND m.id!=".$memberid;
		}

		$select_rewardpoint = "IFNULL(
			((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
			+
			(SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
			+
			(SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=4)
			+
			(SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id  AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))
			-
			(SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.frommemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
			-
			(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
			+
			(SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
			+
			(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
			
		,0) as rewardpoint";

		if($type==0){
			$query = $this->readdb->query("SELECT m.name,m.email,m.mobile,m.id,m.channelid,
											m.membercode,m.image,".$select_rewardpoint."
										FROM ".$this->_table." as m
										INNER JOIN ".$this->_table." as m2 ON m2.id=".$memberid." AND m2.channelid=".$channelid."
										WHERE (IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
										IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 OR m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.")) AND 
										m.membercode='".$searchcode."' AND m.status=1".$where."

										UNION

										SELECT s.businessname as name, s.email, s.mobileno, 0 as id, 0 as channelid, s.companycode as membercode, '' as image, 0 as rewardpoint
										FROM ".tbl_settings." as s 
										INNER JOIN ".$this->_table." as m2 ON m2.id=".$memberid." AND m2.channelid=".$channelid."
										WHERE (IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
										IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET('0',c.multiplememberchannel)>0),0)=1 ) AND 
										companycode='".$searchcode."'
										LIMIT 1
										
									");
		}else{
			$query = $this->readdb->select("m.name,m.email,m.mobile,m.id,m.channelid,m.membercode,m.image,".$select_rewardpoint)
							->from($this->_table." as m")
							->join($this->_table." as m2","m2.id=".$memberid." AND m2.channelid=".$channelid,"INNER")
							->where("(IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
									IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 OR m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid.")) AND 
									m.membercode='".$searchcode."' AND m.status=1".$where)
							->get();
		}
		
		// echo $this->readdb->last_query(); exit;
		return $query->row_array();
	}

	function recentSellerorBuyer($memberid,$counter,$type){
		
		$limit = 10;
		$recentmember = ($type==1)?'recentseller':'recentbuyer';
		$currentmember = ($type==1)?'currentseller':'currentbuyer';

		$this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel');
        $this->readdb->from(tbl_systemconfiguration." as s"); 
        $this->readdb->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")","INNER");
		$systemconfiguration = $this->readdb->get()->row_array();
		
		if($type==1){
			$where = " AND o.memberid=".$memberid." AND (o.sellermemberid NOT IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") OR o.sellermemberid=0) AND o.sellermemberid!=".$memberid;
			$recentmemberid = "o.sellermemberid";
		}else{
			$where = " AND o.sellermemberid=".$memberid." AND o.memberid!=".$memberid;
			$recentmemberid = "o.memberid";
		}
		$select_rewardpoint = "IFNULL(
			((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
			+
			(SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
			+
			(SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=4)
			+
			(SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id  AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))
			-
			(SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.frommemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
			-
			(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
			+
			(SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
			+
			(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
			
		,0) as rewardpoint";
		$this->readdb->select("IFNULL(m.id, '0') as id,
							IFNULL(m.name, 'Company') as name,
							IFNULL(m.channelid, '') as level,
							IFNULL(m.email, '') as email,
							IFNULL(m.mobile, '') as mobileno,
							IFNULL(m.membercode, '') as membercode,
							IFNULL(m.image, '') as image,
							(SELECT conversationrate FROM ".tbl_channel." WHERE id=m.channelid) as pointrate,
							".$select_rewardpoint."	
							");
		
		if($type==1){
			
			$this->readdb->from(tbl_orders." as o");
			$this->readdb->join($this->_table." as m","m.id=".$recentmemberid,"LEFT");
			$this->readdb->join($this->_table." as m2","m2.id=".$memberid,"LEFT");
			$this->readdb->where("o.isdelete=0 AND (IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE 
							allowmultiplememberwithsamechannel=1),0)=1 AND
							IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1".$where." OR m.id IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$memberid."))");
			$this->readdb->group_by($recentmemberid);
		
		}else{
			
			$this->readdb->from($this->_table." as m");
			$this->readdb->join($this->_table." as m2","m2.id=".$memberid,"INNER");
			$this->readdb->where("m.status=1");
			$this->readdb->where("m.id in (select submemberid from ".tbl_membermapping." where mainmemberid=".$memberid.")");
			if($systemconfiguration['allowmultiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!=''){
				$this->readdb->or_where("(IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
								IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 AND 
								m.id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid=".$memberid." AND memberid!=".$memberid." AND isdelete=0))");
			}
		}
		
		
		
		if($counter != -1){
			$this->readdb->limit($limit,$counter);
	 	}  
		$query = $this->readdb->get();
		
		$recentdata = $query->result_array();
		$json=$memberdetail=array();

		if($type==1){
			
			$memberdata = $this->readdb->select("*,".$select_rewardpoint)
								 	->from($this->_table." as m")
								   ->where("m.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.") AND m.status=1")
								   ->get()
								   ->row_array();

			if(!empty($memberdata)){
				$memberdetail = array("id"=>$memberdata['id'],
									"name"=>$memberdata['name'],
									"level"=>$memberdata['channelid'],
									"email"=>$memberdata['email'],
									"mobileno"=>$memberdata['mobile'],
									"membercode"=>$memberdata['membercode'],
									"image"=>$memberdata['image'],
									"rewardpoint"=>$memberdata['rewardpoint']);
			}else{
				$memberdetail = array("id"=>0,"name"=>'Company',"level"=>'',"email"=>'',"mobileno"=>'',"membercode"=>'',"image"=>'','rewardpoint'=>0);
			}
		}
		
		$json[$currentmember] = (!empty($memberdetail))?$memberdetail:(object)$memberdetail;
		$json[$recentmember] = $recentdata;
		
		return $json;
	}

	function recentBuyerInCRM($employeeid,$counter){
		
		$limit = 10;
		$recentmember = 'recentbuyer';
		$currentmember = 'currentbuyer';

		$select_rewardpoint = "IFNULL(
			((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
			+
			(SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
			+
			(SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=4)
			+
			(SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id  AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))
			-
			(SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.frommemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
			-
			(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
			+
			(SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
			+
			(SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
			
		,0) as rewardpoint";
		$this->readdb->select("IFNULL(m.id, '0') as id,
							IFNULL(m.name, 'Company') as name,
							IFNULL(m.channelid, '') as level,
							IFNULL(m.email, '') as email,
							IFNULL(m.mobile, '') as mobileno,
							IFNULL(m.membercode, '') as membercode,
							IFNULL(m.image, '') as image,
							IFNULL((SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=m.id LIMIT 1),0) as sellerid,
							(SELECT conversationrate FROM ".tbl_channel." WHERE id=m.channelid) as pointrate,
							".$select_rewardpoint."	
							");
		
		
			
		$this->readdb->from($this->_table." as m");
		$this->readdb->join(tbl_salespersonmember." as spm","spm.memberid=m.id","INNER");
		$this->readdb->where("m.status=1");
		$this->readdb->where("spm.employeeid=".$employeeid."");
		
		$this->readdb->or_where("(IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
							IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE spm.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 AND 
							m.id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid=0 AND memberid!=0 AND isdelete=0 AND salespersonid=".$employeeid."))");
		
		if($counter != -1){
			$this->readdb->limit($limit,$counter);
	 	}  
		$query = $this->readdb->get();
		
		$recentdata = $query->result_array();
		$json=$memberdetail=array();

		$json[$currentmember] = (object)$memberdetail;
		$json[$recentmember] = $recentdata;
		
		return $json;
	}
	function getActiveBuyerMemberForOrderBySellerInCompany($concatname,$buyerchannelid=0){

		if($concatname=='concatnameoremail'){
			$select = ",(CONCAT(m.name,' (',m.email,')')) as name";
		}else if($concatname=='concatcodeormobile'){
			$select = ",(CONCAT(m.membercode,' (+',m.countrycode,' ',m.mobile,')')) as name";
		}else if($concatname=='concatnameormembercodeormobile'){
			$select = ",(CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')')) as name";
		}else if($concatname=='concatnameormembercode'){
			$select = ",(CONCAT(m.name,' (',m.membercode,')')) as name";
		}else{
			$select = ",m.name";
		}
		
		$this->readdb->select("m.id,m.membercode,m.billingaddressid,m.shippingaddressid".$select);					
		$this->readdb->from($this->_table." as m");
		$this->readdb->where("m.status=1");
		if($buyerchannelid!=0){
			$this->readdb->where("m.channelid='".$buyerchannelid."'");
		}
		$where = "(m.id in (select submemberid from ".tbl_membermapping." where mainmemberid=0) OR m.id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid=0 AND memberid!=0 AND isdelete=0))";
		$this->readdb->where($where);
		$query = $this->readdb->get();
		
		return $query->result_array();
	}

	function getActiveBuyerMemberForOrderBySeller($memberid,$channelid,$concatname,$buyerchannelid=0){

		if($concatname=='concatnameoremail'){
			$select = ",(CONCAT(m.name,' (',m.email,')')) as name";
		}else if($concatname=='concatcodeormobile'){
			$select = ",(CONCAT(m.membercode,' (+',m.countrycode,' ',m.mobile,')')) as name";
		}else if($concatname=='concatnameormembercodeormobile'){
			$select = ",(CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')')) as name";
		}else if($concatname=='concatnameormembercode'){
			$select = ",(CONCAT(m.name,' (',m.membercode,')')) as name";
		}else{
			$select = ",m.name";
		}
		
		$this->readdb->select("m.id,m.membercode,m.billingaddressid,m.shippingaddressid".$select.",IF(m.minimumorderamount>0,m.minimumorderamount,(SELECT minimumorderamount FROM ".tbl_channel." WHERE id=m.channelid)) as minimumorderamount");					
		$this->readdb->from($this->_table." as m");
		$this->readdb->join($this->_table." as m2","m2.id=".$memberid." AND m2.channelid=".$channelid,"INNER");
		$this->readdb->where("m.status=1");
		if($buyerchannelid!=0){
			$this->readdb->where("m.channelid='".$buyerchannelid."'");
		}
		$where = "(m.id in (select submemberid from ".tbl_membermapping." where mainmemberid=".$memberid.")";
		if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
			$where .= " OR (IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
							IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 AND 
							m.id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid=".$memberid." AND memberid!=".$memberid." AND isdelete=0))";
		}
		$where .= ")";
		$this->readdb->where($where);
		$query = $this->readdb->get();
		
		return $query->result_array();
	}

	function getActiveBuyerMemberForQuotationBySeller($memberid,$channelid,$concatname){

		if($concatname=='concatnameoremail'){
			$select = ",(CONCAT(m.name,' (',m.email,')')) as name";
		}else if($concatname=='concatcodeormobile'){
			$select = ",(CONCAT(m.membercode,' (+',m.countrycode,' ',m.mobile,')')) as name";
		}else{
			$select = ",m.name";
		}
		
		$this->readdb->select("m.id,m.membercode,m.billingaddressid,m.shippingaddressid".$select);					
		$this->readdb->from($this->_table." as m");
		$this->readdb->join($this->_table." as m2","m2.id=".$memberid." AND m2.channelid=".$channelid,"INNER");
		$this->readdb->where("m.status=1");
		$this->readdb->where("m.id in (select submemberid from ".tbl_membermapping." where mainmemberid=".$memberid.")");
		if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
			$this->readdb->or_where("(IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
							IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 AND 
							m.id IN (SELECT memberid FROM ".tbl_quotation." WHERE sellermemberid=".$memberid." AND memberid!=".$memberid."))");
		}
		$query = $this->readdb->get();

		//echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}

	function getActiveSellerMemberByBuyer($memberid,$channelid,$concatname){

		if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
			$where = " AND IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
			IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 AND m.id IN (SELECT sellermemberid FROM ".tbl_orders." WHERE memberid=".$memberid." AND sellermemberid!=".$memberid." AND isdelete=0)";
		}else{
			$where = " AND m.id in (select mainmemberid from ".tbl_membermapping." where submemberid=".$memberid.")";
		}
		if($concatname=='concatnameoremail'){
			$select = ",(CONCAT(m.name,' (',m.email,')')) as name";
		}else if($concatname=='concatcodeormobile'){
			$select = ",(CONCAT(m.membercode,' (+',m.countrycode,' ',m.mobile,')')) as name";
		}else if($concatname=='concatnameormembercode'){
			$select = ",(CONCAT(m.name,' (',m.membercode,')')) as name";
		}else if($concatname=='concatnameormembercodeormobile'){
			$select = ",(CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')')) as name";
		}else{
			$select = ",m.name";
		}
		
		$query = $this->readdb->select("m.id,m.membercode".$select)
							
							->from($this->_table." as m")
							->join($this->_table." as m2","m2.id=".$memberid." AND m2.channelid=".$channelid,"INNER")
							->where("m.status=1".$where)
							->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}
	function getSellerMemberByBuyer($memberid,$channelid,$concatname){

		if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
			$where = " AND (IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
			IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE buyer.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 OR sellermemberid=0)";
		}else{
			$where = " AND m.id in (select mainmemberid from ".tbl_membermapping." where submemberid=".$memberid.")";
		}
		if($concatname=='concatnameoremail'){
			$select = ",IFNULL((CONCAT(m.name,' (',m.email,')')),'Company') as name";
		}else if($concatname=='concatcodeormobile'){
			$select = ",IFNULL((CONCAT(m.membercode,' (+',m.countrycode,' ',m.mobile,')')),'Company') as name";
		}else if($concatname=='concatnameormembercode'){
			$select = ",IFNULL((CONCAT(m.name,' (',m.membercode,')')),'Company') as name";
		}else if($concatname=='concatnameormembercodeormobile'){
			$select = ",IFNULL((CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')')),'Company') as name";
		}else{
			$select = ",IFNULL(m.name,'Company')";
		}
		
		$query = $this->readdb->select("IFNULL(m.id,0) as id,IFNULL(m.membercode,'') as membercode".$select)
							
							->from(tbl_orders." as o")
							->join($this->_table." as m","m.id=o.sellermemberid","LEFT")
							->join($this->_table." as buyer","buyer.id=o.memberid","LEFT")
							->where("o.memberid='".$memberid."' AND o.sellermemberid!='".$memberid."' AND o.isdelete=0".$where)
							->group_by("o.sellermemberid")
							->get();
		// echo $this->readdb->last_query(); exit;
		return $query->result_array();
	}
	function getCurrentSellerCode($memberid){

		$member = $this->getmainmember($memberid,"row");

		if(isset($member['id']) && $member['id']>0){
			
			$query = $this->readdb->query("SELECT membercode,m.id as sellerid FROM ".$this->_table." as m WHERE id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid.")
			");
		}else{

			$query = $this->readdb->query("SELECT companycode as membercode,0 as sellerid FROM ".tbl_settings." LIMIT 1");
		}
							
		// echo $this->readdb->last_query(); exit;
		return $query->row_array();
	}

	function getActiveBuyerMemberByAdmin($concatname=''){

		if($concatname=='concatnameoremail'){
			$select = ",(CONCAT(name,' (',email,')')) as name";
		}else if($concatname=='concatcodeormobile'){
			$select = ",(CONCAT(membercode,' (+',countrycode,' ',mobile,')')) as name";
		}else if($concatname=='concatnameorcode'){
			$select = ",(CONCAT(name, IF(membercode!='',CONCAT(' (',membercode,')'),''))) as name";
		}else if($concatname=='concatnameormembercodeormobile'){
			$select = ",IFNULL((CONCAT(name,' (',membercode,' - ',mobile,')')),'Company') as name";
		}else{
			$select = ",name";
		}
		
		$query = $this->readdb->query("SELECT temp.* 
								
									FROM (
										SELECT id,membercode,billingaddressid,shippingaddressid,channelid".$select.",IF(minimumorderamount>0,minimumorderamount,(SELECT minimumorderamount FROM ".tbl_channel." WHERE id=channelid)) as minimumorderamount FROM ".tbl_member." WHERE channelid IN (SELECT id FROM ".tbl_channel." WHERE status=1 AND id<>".GUESTCHANNELID." AND FIND_IN_SET(0,multiplememberchannel)>0 ) AND status=1
										
										UNION
										
										SELECT id,membercode,billingaddressid,shippingaddressid,channelid".$select.",IF(minimumorderamount>0,minimumorderamount,(SELECT minimumorderamount FROM ".tbl_channel." WHERE id=channelid)) as minimumorderamount FROM ".tbl_member." WHERE id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid=0 AND isdelete=0) AND channelid<>".GUESTCHANNELID." AND status=1
									) as temp 
									
									ORDER BY temp.name ASC
								");
		//echo $this->readdb->last_query();exit;
		return $query->result_array();
	}

	function getBuyerByCode($membercode){

		$query = $this->readdb->select("m.name,m.email,m.mobile,m.id,m.channelid,m.membercode,m.image")
								->from($this->_table." as m")
								->where("m.membercode='".$membercode."' AND m.status=1")
								->get();
	
		return $query->row_array();
	}
	
	function getIdFromCode($membercode){

		$query = $this->readdb->select("IFNULL(m.id,0) as id,m.channelid")
							->from(tbl_member." as m")
							->join(tbl_settings." as s","s.companycode='".$membercode."'","LEFT")
							->where("m.membercode='".$membercode."' AND m.status=1")
							->get();
	
		return $query->row_array();
	}

	function verifyemail($email){

		$this->load->model('Member_model','Member');
		$this->Member->_fields = 'id,name';
		$this->Member->_where = array('email'=>$email);
		$MemberData = $this->Member->getRecordsByID();

		if(!empty($MemberData)){
			$code = generate_token(6,true);//get code for verification
			
			$this->insertmemberemailverification($MemberData['id'],$code);
			
			$Url = base_url().CHANNELFOLDER.'verifyemail/index/'.urlencode($code);
			
			/* SEND EMAIL TO USER */
			$mailBodyArr1 = array(
				"{logo}" => '<a href="' . DOMAIN_URL . '"><img src="' . MAIN_LOGO_IMAGE_URL.COMPANY_LOGO.'" alt="' . COMPANY_NAME . '" style="border: none; display: inline; font-size: 14px; font-weight: bold; height: auto; line-height: 100%; outline: none; text-decoration: none; text-transform: capitalize;"/></a>',
				"{url}" => $Url,
				"{email}" => $email,
				"{name}" => $MemberData['name'],
				"{companyemail}" => explode(",",COMPANY_EMAIL)[0],
				"{companyname}" => COMPANY_NAME
			);
			//Send mail with email format store in database
			$mailid=array_search('Verify Email',$this->Emailformattype);
			
			if(!empty($mailid)){
				$emailSend = $this->Member->sendMail($mailid, $email, $mailBodyArr1);
				if($emailSend){
					return 1;
				}
			}
		}
		return 0;
	}

	function addsmsverification($memberid,$code,$type=0){

		$insertdata = array("memberid"=>$memberid,
							"code"=>$code,
							"type"=>$type,
							"createddate"=>$this->general_model->getCurrentDateTime()
						);
		$this->writedb->insert(tbl_smsverification, $insertdata);
		
	}

	function countSendOTPRequest($memberid,$type=0){
		
		$query = $this->readdb->query("SELECT count(*) as count FROM ".tbl_smsverification." WHERE type=".$type." AND memberid=".$memberid." AND createddate >= date_add(now(), interval -1 hour)");

		$count = $query->row_array();
		return $count['count'];
	}

	function getSMSByMember($memberid,$type=0){

		$query = $this->readdb->select("id,memberid,code,createddate")
					->from(tbl_smsverification." as sv")
					->where("memberid = '".$memberid."' AND type=".$type)
					->order_by("sv.id DESC")
					->limit(1)
					->get();
	    /* ->where("memberid = '".$memberid."' AND code = '".$code."' AND createddate >= NOW() - INTERVAL 10 MINUTE") */
		
		if($query->num_rows() == 1){
			return $query->row_array();
		}else{
			return array();
		}
	}
	function getMemberrDataByID($Memberid){

		$this->readdb->select("cba.firstname,cba.lastname,c.username,c.ipaddress,c.email,c.createddate,
						cba.mobileno,cba.address,cba.postcode,
						(country.name) as country,(p.name) as province,(city.name) as city");
		$this->readdb->from($this->_table." as c");
		$this->readdb->join(tbl_memberaddress." as cba","cba.Memberid=c.id","INNER");
		$this->readdb->join(tbl_city." as city","city.id=cba.cityid","INNER");
		$this->readdb->join(tbl_province." as p","p.id=city.provinceid","INNER");
		$this->readdb->join(tbl_country." as country","country.id=p.countryid","INNER");
		$this->readdb->where("c.id='".$Memberid."' AND c.status=1");
		$query = $this->readdb->get();
		
		return $query->result_array();
	}
	function getMemberCodes(){

		$query = $this->readdb->select("m.id,m.membercode")
						->from($this->_table." as m")
						->where("m.membercode!=''")
						->get();
		
		return $query->result_array();
	}

	function checkMemberCodeExists($membercode){

		$query = $this->readdb->select("m.id,m.membercode")
						->from($this->_table." as m")
						->where("m.membercode='".$membercode."'")
						->get();
		
		if ($query->num_rows() >= 1) {
			return $query->row_array();
		} else {
			return array();
		}
	}
	
	function getMemberDetailByWebsiteLink($websitelink){

		$query = $this->readdb->select("m.id,m.channelid,m.name,m.roleid,m.image,m.email,m.mobile,m.createddate,m.debitlimit,m.gstno,m.membercode,m.countrycode,m.emailverified,m.secondarymobileno,m.secondarycountrycode,
							IFNULL((select mainmemberid from ".tbl_membermapping." where submemberid=m.id limit 1),0) as sellermemberid,
							(select website from ".tbl_channel." where id=m.channelid)as website,
							IFNULL(c.name,'') as cityname,
							IFNULL(p.name,'') as provincename,
							IFNULL(country.name,'') as countryname,
							parentmemberid,minimumstocklimit,
							IFNULL((select mrs.name FROM ".tbl_memberratingstatus." as mrs where mrs.id=m.memberratingstatusid),'') as memberratingstatus,
							paymentcycle,memberratingstatusid,emireminderdays,
							IFNULL((SELECT count(id) FROM ".tbl_memberproduct." where memberid=m.id),0) as totalproductcount,
							m.status,
							
							IFNULL((SELECT status FROM ".tbl_channel." where id=m.channelid),0) as ischannelactive,
							IFNULL((SELECT status FROM ".tbl_memberrole." where id=m.roleid),0) as isroleactive,
							m.reportingto,m.websitelink
							")
							
				->from($this->_table." as m")
				->join(tbl_city." as c","c.id=m.cityid","LEFT")
				->join(tbl_province." as p","p.id=m.provinceid","LEFT")
				->join(tbl_country." as country","country.id=p.countryid","LEFT")
				->where("m.websitelink='".$websitelink."'")
				->get();
				
		return $query->row_array();
	}

	function CheckSalesPersonsByMemberId($memberid,$employeeid){

		$query = $this->readdb->select("COUNT(m.id) as validornot")
				->from($this->_table." as m")
				->join(tbl_salespersonmember." as spm","spm.memberid=m.id","LEFT")
				->where("m.id='".$memberid."'")
				->where("spm.employeeid='".$employeeid."'")
				->get();
				
				$salespersondata = $query->row_array();
				
		return $salespersondata['validornot'];

	}

	function getSalesPersonByMemberChannel($channelid){

		$query = $this->readdb->select("u.id,u.name")
						->from(tbl_user." as u")
						->where("FIND_IN_SET(".$channelid.",u.workforchannelid)>0")
						->get();

		return $query->result_array();
	}

	function GetChannelIdByMemberId($memberid){

		$query = $this->readdb->select("channelid")
				->from(tbl_member." as m")
				->where("m.id='".$memberid."'")
				->get();
				
		return $query->row_array()['channelid'];

	}
}
