<?php
class Dashboard_model extends CI_model{

	function getOrderChart($startdate,$enddate,$MEMBERID=0) {

		$where = ' AND o.isdelete=0';
		if($MEMBERID!=0){
			$where = " AND (((memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR memberid=0) AND (addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) OR (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid=".$MEMBERID.") AND (memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR sellermemberid=0) AND (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) AND addedby!=0";
		}
		if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
            $where .= " AND ((o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0) or o.salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
        }
		$query = $this->readdb->select("count(o.id)as countorder,
									DATE_FORMAT(o.createddate,'%d-%m-%Y') as createddate")
							->from(tbl_orders." as o")
							->where("(DATE(o.createddate) BETWEEN '".$startdate."' AND '".$enddate."')".$where)
							->group_by("DATE(o.createddate)")
							->get();
				
		return $query->result_array();
	}

	function getSalesChart($startdate,$enddate,$MEMBERID=0) {
		$where = '0=0 ';
		if(STOCK_CALCULATION==0){
			$where .= ' AND o.isdelete=0';
			if($MEMBERID!=0){
				$where .= " AND (o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.sellermemberid = ".$MEMBERID." ) AND (o.sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR o.memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.memberid=0) AND (o.addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR o.addedby=".$MEMBERID.") AND o.addedby!=0";
			}
			if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$where .= " AND ((o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0) or o.salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
			}

			$query = $this->readdb->query("SELECT 
						round(sum(temp.totalsales),2) as totalsales,
						DATE_FORMAT(temp.createddate, '%d-%m-%Y')as createddate FROM 
							(SELECT SUM(t.payableamount) as totalsales, DATE(t.createddate) as createddate
								FROM ".tbl_orders." as o
								INNER JOIN ".tbl_transaction." as t ON t.orderid=o.id AND DATE(t.createddate) BETWEEN '".$startdate."' AND '".$enddate."'
								WHERE  o.status != 2 AND ".$where."
								GROUP BY DATE(t.createddate)

								UNION ALL

								SELECT SUM(oi.amount) as totalsales, DATE(oi.paymentdate) as createddate
								FROM ".tbl_orders." as o
								INNER JOIN ".tbl_orderinstallment." as oi ON oi.orderid=o.id AND oi.status=1 and DATE(oi.paymentdate) BETWEEN '".$startdate."' AND '".$enddate."'
								WHERE o.status != 2 AND ".$where."
								GROUP BY DATE(oi.paymentdate)
							) as temp 
								
							GROUP BY temp.createddate
						");
		}else{
			if($MEMBERID!=0){
				$where .= " AND i.sellermemberid=".$MEMBERID;
			}else{
				$where .= " AND i.sellermemberid=0";
			}
			if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$where .= " AND ( ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.addedby FROM ".tbl_orders." as o WHERE o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0 AND FIND_IN_SET(o.id,i.orderid)>0) or ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.salespersonid FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,i.orderid)>0) )";
			}
			$query = $this->readdb->query("SELECT SUM(IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0)) as totalsales, DATE(i.createddate) as createddate
							FROM ".tbl_invoice." as i
							WHERE DATE(i.createddate) BETWEEN '".$startdate."' AND '".$enddate."'
							AND i.status != 2 AND ".$where."
							GROUP BY DATE(i.createddate)"); 
		}
		//echo $this->readdb->last_query();exit;				
		return $query->result_array();
	}

	function getTotalSales($where){

		if(STOCK_CALCULATION==0){
			if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$where .= " AND ((o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0) or o.salespersonid=".$this->session->userdata(base_url().'ADMINID').")";
			}
			$query = $this->readdb->select("ROUND(
											SUM(IFNULL((SELECT SUM(payableamount) FROM ".tbl_transaction." as t WHERE t.orderid=o.id),0))
											+
											SUM(IFNULL((SELECT SUM(amount) FROM ".tbl_orderinstallment." as oi WHERE oi.orderid=o.id AND oi.status=1),0)
										),2) as totalsales")
								->from(tbl_orders." as o")
								->where($where)
								->get();
		}else{
			if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$where .= " AND ( ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.addedby FROM ".tbl_orders." as o WHERE o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0 AND FIND_IN_SET(o.id,i.orderid)>0) or ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.salespersonid FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,i.orderid)>0) )";
				
			}
			$query = $this->readdb->select("ROUND(
											IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0),2) as totalsales")
								->from(tbl_invoice." as i")
								->where($where)
								->get();
		}
							
		return $query->row_array();
	}

	

	function folderSize ($dir){
			/*  $size = 0;

			foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
				$size += is_file($each) ? filesize($each) : folderSize($each);
			}

			return $size;  */

			$f = 'uploaded/'.CLIENT_FOLDER;
			$io = popen ( '/usr/bin/du -sk ' . $f, 'r' );
			$size = fgets ( $io, 4096);
			$size = substr ( $size, 0, strpos ( $size, "\t" ) );
			pclose ( $io );

			
			$size = round($size / 1024 / 1024 / 1024,4) ;	
			

			return  $size;
	}
	
	//Admin chart Box ---------------------------------------------------------

	function getsaleschartbox($startdate,$enddate,$sellerid=0){

		if(STOCK_CALCULATION==0){
			$this->readdb->select("ROUND(
											SUM(IFNULL((SELECT SUM(payableamount) FROM ".tbl_transaction." as t WHERE t.orderid=o.id),0))
											+
											SUM(IFNULL((SELECT SUM(amount) FROM ".tbl_orderinstallment." as oi WHERE oi.orderid=o.id AND oi.status=1),0)
										),2) as salescount,period_diff(date_format('".$enddate."', '%Y%m'), date_format('".$startdate."', '%Y%m')) as months");
			$this->readdb->from(tbl_orders." as o");
			$this->readdb->where("o.sellermemberid=".$sellerid." AND o.status!=2 AND o.isdelete=0 AND DATE(createddate) BETWEEN '".$startdate."' and '".$enddate."' ");
			if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$this->readdb->where("((o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0) or o.salespersonid=".$this->session->userdata(base_url().'ADMINID').")");
			}
			$query = $this->readdb->get();
			
		}else{

			$this->readdb->select("ROUND(
									IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0),2) as salescount,
									period_diff(date_format('".$enddate."', '%Y%m'), date_format('".$startdate."', '%Y%m')) as months");
			$this->readdb->from(tbl_invoice." as i");
			$this->readdb->where("i.sellermemberid=".$sellerid." AND i.status!=2 AND DATE(createddate) BETWEEN '".$startdate."' and '".$enddate."' ");
			if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
				$this->readdb->where("( ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.addedby FROM ".tbl_orders." as o WHERE o.addedby=".$this->session->userdata(base_url().'ADMINID')." AND o.type=0 AND FIND_IN_SET(o.id,i.orderid)>0) or ".$this->session->userdata(base_url().'ADMINID')." IN (SELECT o.salespersonid FROM ".tbl_orders." as o WHERE FIND_IN_SET(o.id,i.orderid)>0) )");
			}
			$query = $this->readdb->get();
		}
		return $query->row_array();	
	}
	
	function getorderschartbox($startdate,$enddate){

		$this->readdb->select("count(id) as orderscount,period_diff(date_format('".$enddate."', '%Y%m'), date_format('".$startdate."', '%Y%m')) as months");
		$this->readdb->from(tbl_orders);
		$this->readdb->where("isdelete=0 AND DATE(createddate) BETWEEN '".$startdate."' and '".$enddate."' ");
		if (isset($this->viewData['mainmenuvisibility']['menuviewalldata']) && strpos($this->viewData['mainmenuvisibility']['menuviewalldata'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') === false) {
			$this->readdb->where("((addedby=".$this->session->userdata(base_url().'ADMINID')." AND type=0) or salespersonid=".$this->session->userdata(base_url().'ADMINID').")");
		}
		$query = $this->readdb->get();
		return $query->row_array();	

	}

	//Channel Panel chart Box ---------------------------------------------------------

	function getMembersaleschartbox($startdate,$enddate){

		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');

		if(STOCK_CALCULATION==0){
			$query = $this->db->select("ROUND(
				SUM(IFNULL((SELECT SUM(payableamount) FROM ".tbl_transaction." as t WHERE t.orderid=o.id),0))
				+
				SUM(IFNULL((SELECT SUM(amount) FROM ".tbl_orderinstallment." as oi WHERE oi.orderid=o.id AND oi.status=1),0)
			),2) as salescount,period_diff(date_format('".$enddate."', '%Y%m'), date_format('".$startdate."', '%Y%m')) as months")
								->from(tbl_orders." as o")
								->where("o.status!=2 AND o.isdelete=0 AND o.sellermemberid='".$MEMBERID."' AND DATE(createddate) BETWEEN '".$startdate."' and '".$enddate."' ")
								->get();
		}else{
			$query = $this->db->select("ROUND(
				IFNULL((i.amount + i.taxamount - i.globaldiscount - i.couponcodeamount - IFNULL((SELECT SUM(redeemamount) FROM ".tbl_transactiondiscount." WHERE transactionid=i.id),0) + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE type=2 AND referenceid=i.id),0)),0),2) as salescount,
				period_diff(date_format('".$enddate."', '%Y%m'), date_format('".$startdate."', '%Y%m')) as months")
								->from(tbl_invoice." as i")
								->where("i.sellermemberid=".$MEMBERID." AND i.status!=2 AND DATE(createddate) BETWEEN '".$startdate."' and '".$enddate."' ")
								->get();
		}
		return $query->row_array();	
	}
	
	function getMemberorderschartbox($startdate,$enddate){

		$MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');

		$where = array("DATE(createddate) BETWEEN '".$startdate."' and '".$enddate."' "=>null,"status"=>1,"isdelete"=>0);
        $where["(((memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid = ".$MEMBERID." ) AND (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid  IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR memberid=0) AND (addedby IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) OR (sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR memberid=".$MEMBERID.") AND (memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid = ".$MEMBERID.") OR sellermemberid  IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR sellermemberid=0) AND (addedby IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid = ".$MEMBERID.") OR addedby=".$MEMBERID.")) AND addedby!=0"] = null;

		$query = $this->db->select("count(id) as orderscount,period_diff(date_format('".$enddate."', '%Y%m'), date_format('".$startdate."', '%Y%m')) as months")
							->from(tbl_orders)
							//->where("status=1 AND DATE(createddate) BETWEEN '".$startdate."' and '".$enddate."' ")
							->where($where)
							->get();
		return $query->row_array();	

	}

	public function getrecentmember($checkrights=0)
	{
		$this->db->select("m.id as mid,m.name,m.mobile,(select name from ".tbl_memberstatus." where id=m.status) as member_status,m.createddate,(select group_concat(name) from ".tbl_user." where id in(select employeeid from ".tbl_crmassignmember." where memberid=m.id))as empname,(select count(id) from ".tbl_member.")as totalmember,(select remarks from ".tbl_crmremarkmember." where memberid=m.id order by id desc limit 1)as memberremark");
		$this->db->from(tbl_member." as m");
		$this->db->join(tbl_contactdetail." as cd","cd.memberid=m.id");
		$this->db->order_by("m.id desc",null);
		$this->db->limit(10);
		if ($checkrights==1) {
			// $this->db->escape($this->session->userdata(base_url().'ADMINID')
			$this->db->where(array("(m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")) or m.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")"=>null));	
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function getrecentinquiry($checkrights=0)
	{
		$this->db->select("m.id as mid,m.name as mname,(select name from ".tbl_user." where id=ci.inquiryassignto )as uname,ci.createddate,cd.mobileno,(select count(id) from ".tbl_crminquiry.")as totalinquiry,(select remarks from ".tbl_crmremarkmember." where memberid=m.id order by id desc limit 1)as memberremark");
		$this->db->from(tbl_crminquiry." as ci");
		$this->db->join(tbl_member." as m","ci.memberid=m.id","left");
		$this->db->join(tbl_contactdetail." as cd","cd.memberid=m.id","left");
		$this->db->join(tbl_user." as u","m.assigntoid=m.id","left");
		if($checkrights==1) {
			$this->db->where("(inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR i.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
			or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")) or m.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")");	
			// select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')
		}
		$this->db->order_by("ci.id desc",null);
		$this->db->limit(5);
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
	}
	
	public function getrecentfollowup($checkrights=0)
	{
		$this->db->select("m.id as cid,(select name from ".tbl_user." where id=assignto ) as username,notes,(select name from ".tbl_followupstatuses." where id=cf.status)as status,m.name as mname,(select count(id) from ".tbl_crmfollowup.")as totalfollowup,(select remarks from ".tbl_crmremarkmember." where memberid=m.id order by id desc limit 1)as memberremark");
		$this->db->from(tbl_crmfollowup." as cf");
		$this->db->join(tbl_crminquiry." as ci","cf.inquiryid=ci.id");	
		$this->db->join(tbl_member." as m","ci.memberid=m.id");	
		if ($checkrights==1) {
			$this->db->where("(assignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR cf.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto = ".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." OR ci.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." 
			or inquiryassignto in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).") 
			or m.id in(select memberid from ".tbl_crmassignmember." where employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID'))." or employeeid in(select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")) or m.addedby=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")");
			
			// select id from ".tbl_user." where reportingto=".$this->db->escape($this->session->userdata(base_url().'ADMINID')
		}
		$this->db->order_by("ci.id desc",null);
		// $this->db->where(array("date"=>date("Y-m-d")));
		$this->db->limit(5);
		$query = $this->db->get();
		// echo $this->db->last_query();exit;
		return $query->result_array();
	}

	public function getrecenttodolist($checkrights=0)
	{
		$this->db->select("tdl.id,tdl.list,(select name from ".tbl_user." where id = tdl.addedby ) as assignby,tdl.status");
		$this->db->from(tbl_todolist." as tdl");		
		$this->db->join(tbl_user." as u","u.id=tdl.employeeid");	
		//if ($checkrights==1) {
			$this->db->where("(tdl.employeeid=".$this->db->escape($this->session->userdata(base_url().'ADMINID')).")");			
		//}
		$this->db->order_by("tdl.priority ASC",null);	
		//$this->db->limit(5);
		$query = $this->db->get();	
		return $query->result_array();
	}

}