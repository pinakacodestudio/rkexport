<?php

class Channel_model extends Common_model {

	//put your code here
	public $_table = tbl_channel;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'name','priority'); //set column field database for datatable orderable
	public $column_search = array('name','priority'); //set column field database for datatable searchable 
	public $order = array('priority' => 'asc'); // default order 

	function __construct() {
		parent::__construct();
	}
	
	function getChannelBySalesOrderOnCompany(){

		$this->readdb->select("c.id,c.name");					
		$this->readdb->from(tbl_member." as m");
		$this->readdb->join($this->_table." as c","c.id=m.channelid","INNER");
		$this->readdb->where("m.status=1 AND (m.id in (select submemberid from ".tbl_membermapping." where mainmemberid=0) OR m.id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid=0 AND memberid!=0 AND isdelete=0)) AND c.id NOT IN (".VENDORCHANNELID.",".GUESTCHANNELID.")");
		$this->readdb->group_by("m.channelid");
		$query = $this->readdb->get();

		return $query->result_array();
	}

	function getChannelBySalesOrder($memberid,$channelid){

		$this->readdb->select("c.id,c.name");					
		$this->readdb->from(tbl_member." as m");
		$this->readdb->join(tbl_member." as m2","m2.id=".$memberid." AND m2.channelid=".$channelid,"INNER");
		$this->readdb->join($this->_table." as c","c.id=m.channelid","INNER");
		$this->readdb->where("m.status=1");
		$this->readdb->where("m.id in (select submemberid from ".tbl_membermapping." where mainmemberid=".$memberid.")");
		if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && channel_multiplememberwithsamechannel==1 && channel_multiplememberchannel!=''){
			$this->readdb->or_where("(IFNULL((SELECT 1 FROM ".tbl_systemconfiguration." WHERE allowmultiplememberwithsamechannel=1),0)=1 AND
							IFNULL((SELECT 1 FROM ".tbl_channel." as c WHERE m2.channelid=c.id AND c.multiplememberwithsamechannel=1 AND FIND_IN_SET(m.channelid,c.multiplememberchannel)>0),0)=1 AND 
							m.id IN (SELECT memberid FROM ".tbl_orders." WHERE sellermemberid=".$memberid." AND memberid!=".$memberid." AND isdelete=0))");
		}
		$this->readdb->group_by("m.channelid");
		$query = $this->readdb->get();

		return $query->result_array();
	}
	function getChannelDataByID($ID){
		$query = $this->readdb->select("*")
							->from($this->_table)
							->where("id", $ID)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}	
	}
	function getChannelSettingsForAPP($ID,$memberid=0,$sellermemberid=0){
		$sql='';
		if($memberid==0){
			$sql = "IFNULL((SELECT conversationrate FROM ".tbl_channel." WHERE id < ".$ID." ORDER BY id DESC LIMIT 1),0) as referralconversationrate,'' as upperlevelname,0 as minimumorderamount,";
		}else if(($memberid==-1 && $ID==-1) || ($ID == GUESTCHANNELID)){
			$ID = GUESTCHANNELID;
			$sql = "0 as referralconversationrate,'Company' as upperlevelname,0 as minimumorderamount,";
		}else{
			$sql = "IFNULL((SELECT conversationrate FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid."))),0) as referralconversationrate,
			
			IF(c.showupperdirectory=1,IFNULL((SELECT name FROM ".tbl_channel." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id=(SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$memberid."))),'Company'),'') as upperlevelname,
			
			IF((SELECT minimumorderamount FROM ".tbl_member." WHERE id=".$memberid.")>0,(SELECT minimumorderamount FROM ".tbl_member." WHERE id=".$memberid."),c.minimumorderamount) as minimumorderamount,";
		}
		$this->readdb->select("c.quotation,c.partialpayment,c.identityproof,c.discount,c.discountcoupon,
							c.rating,c.debitlimit,c.discountpriority,c.multiplememberwithsamechannel,
		
							c.productwisepoints,c.productwisepointsmultiplywithqty,
							c.productwisepointsforbuyer,

							c.overallproductpoints,c.buyerpointsforoverallproduct,c.mimimumorderqtyforoverallproduct,

							c.pointsonsalesorder,c.buyerpointsforsalesorder,c.mimimumorderamountforsalesorder,

							c.referandearn,c.rewardforrefferedby,c.rewardfornewregister,c.conversationrate,
							".$sql."
							c.minimumpointsonredeem,c.minimumpointsonredeemfororder,c.mimimumpurchaseorderamountforredeem,
							c.addorderwithoutstock,c.edittaxrate as channeledittaxrate,c.offermodule,

							IFNULL(c2.productwisepoints,0) as sellerproductwisepoints,
							IFNULL(c2.overallproductpoints,0) as selleroverallproductpoints,
							IFNULL(c2.pointsonsalesorder,0) as sellerpointsonsalesorder,

							IFNULL(c2.productwisepointsmultiplywithqty,0) as sellerproductwisepointsmultiplywithqty,
							IFNULL(c2.productwisepointsforseller,0) as productwisepointsforseller,

							IFNULL(c2.sellerpointsforoverallproduct,0) as sellerpointsforoverallproduct,
							IFNULL(c2.mimimumorderamountforsalesorder,0) as sellermimimumorderamountforsalesorder,
							
							IFNULL(c2.sellerpointsforsalesorder,0) as sellerpointsforsalesorder,
							IFNULL(c2.mimimumorderamountforsalesorder,0) as sellermimimumorderamountforsalesorder,

							c.allowedchannelmemberregistration,
							c.addmemberforrapp,
							IF(c.advancepaymentpriority=0,c.advancepaymentcod,(SELECT advancepaymentcod FROM ".tbl_member." WHERE id=".$memberid.")) as advancepaymentcod
						");
		$this->readdb->from($this->_table." as c");
		$this->readdb->join($this->_table." as c2", "c2.id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$sellermemberid.")", "LEFT");
		$this->readdb->where("c.id", $ID);
		$query = $this->readdb->get();
		//echo $this->db->last_query(); exit;
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}	
	}
	function getChannelList($type=''){

		if($type=='all'){
			$where = '1=1';
		}else if($type=='notdisplayvendorchannel'){
			$where = "id!=".VENDORCHANNELID;
		}else if($type=='notdisplayguestorvendorchannel'){
			$ids = implode(",",array(VENDORCHANNELID,GUESTCHANNELID));
			$where = "id NOT IN (".$ids.")";
		}else if($type=='notdisplaycustomerorguestorvendorchannel'){
			$ids = implode(",",array(VENDORCHANNELID,GUESTCHANNELID,CUSTOMERCHANNELID));
			$where = "id NOT IN (".$ids.")";
		}else if($type=='onlyvendor'){
			$where = "id = ".VENDORCHANNELID;
		}else{
			$where = "id!=".GUESTCHANNELID;
		}
		$query = $this->readdb->select("id,name,color")
							->from($this->_table)
							->where($where)
							->order_by("priority ASC")
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
	}
	function getSellerChannelByChannel($channelid){

		$query = $this->readdb->query("(SELECT 0 as id,'Company' as name FROM ".tbl_channel." as c WHERE IFNULL((SELECT 1 FROM ".tbl_channel." as c2 WHERE c2.id=".$channelid." AND FIND_IN_SET(0,c2.multiplememberchannel)),0)=1)
									UNION
									(SELECT c.id,c.name FROM ".tbl_channel." as c WHERE ".$channelid."!=".GUESTCHANNELID." AND c.id in (SELECT max(c2.id) FROM channel as c2 WHERE c2.priority<(SELECT c1.priority FROM channel as c1 WHERE c1.id=".$channelid.")))
									UNION
									(SELECT c.id,c.name FROM ".tbl_channel." as c WHERE FIND_IN_SET(c.id,(SELECT c2.multiplememberchannel FROM ".tbl_channel." as c2 WHERE c2.id=".$channelid."))>0 ORDER BY c.priority)");
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array(array('id'=>0,'name'=>'Company'));
		}	
	}
	function getChannelIDByFirstLevel(){
		$query = $this->readdb->select("id")
							->from($this->_table)
							->where("status", 1)
							->order_by("id", "ASC")
							->limit(1)
							->get();
		
		if ($query->num_rows() > 0) {
			return $query->row_array();
		}else {
			return array();
		}	
	}
	function getBottomLevelChannel($id){
		$query = $this->readdb->select("c.id,c.name,c.color")
							->from($this->_table." as c")
							->where("c.id<>".GUESTCHANNELID." AND c.priority > IFNULL((SELECT c1.priority FROM ".$this->_table." as c1 WHERE c1.id=".$id."),0) AND c.status=1")
							->order_by("c.id", "ASC")
							->get();
		
		return $query->result_array();
	}
	
	function getChannelListByMember($MEMBERID,$type='withoutcurrentchannel'){
		

		$this->readdb->select('s.allowmultiplememberwithsamechannel,c.multiplememberwithsamechannel, c.multiplememberchannel,c.allowedchannelmemberregistration');
        $this->readdb->from(tbl_systemconfiguration." as s"); 
        $this->readdb->join(tbl_channel." as c","c.id=(SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")","INNER");
		$systemconfiguration = $this->readdb->get()->row_array();
		
		if(ALLOWMULTIPLEMEMBERWITHSAMECHANNEL==1 && $systemconfiguration['multiplememberwithsamechannel']==1 && $systemconfiguration['multiplememberchannel']!='' && $type!='allowedchannelmemberregistration'){

			if($type=="multiplesellerchannel"){
				
				$query = $this->readdb->query("
				(SELECT IFNULL(m.channelid, '0') as id, 
						IFNULL(c.name, 'Company') as name
						
						FROM ".tbl_orders." 
						LEFT JOIN ".tbl_member." as m ON m.id = sellermemberid
						LEFT JOIN ".$this->_table." as c ON c.id = m.channelid AND c.id<>".GUESTCHANNELID."
						WHERE sellermemberid!=".$MEMBERID." 
						AND memberid=".$MEMBERID." AND isdelete=0 GROUP BY id)");
			
			}else if($type=="homebanner"){
				
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE FIND_IN_SET(id, '".$systemconfiguration['multiplememberchannel']."') AND id<>".GUESTCHANNELID." AND id<>(SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID."))");
			
			}else if($type=='withoutcurrentchannel'){
			
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE priority > (SELECT priority FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")) AND id<>".GUESTCHANNELID." LIMIT 1)");
			
			}else if($type=="vouchercode"){
				
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID."))
				UNION
				(SELECT id,name,color FROM ".$this->_table." WHERE priority > (SELECT priority FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")) AND id<>".GUESTCHANNELID." LIMIT 1)");
			
			}else if($type=="memberchannel"){
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE FIND_IN_SET(id, '".$systemconfiguration['multiplememberchannel']."') AND id<>".GUESTCHANNELID." AND id<>(SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID."))");
			}else{
				
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE id IN (SELECT memberid FROM ".tbl_orders." where sellermemberid = ".$MEMBERID." AND memberid != ".$MEMBERID." AND isdelete=0 GROUP BY memberid) GROUP BY channelid) AND id <>".GUESTCHANNELID.")");

			}
			
		}else if($type=='allowedchannelmemberregistration'){
			$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE FIND_IN_SET(id,'".$systemconfiguration['allowedchannelmemberregistration']."')>0 AND id<>".GUESTCHANNELID.")");
		}else{
			if($type=='withoutcurrentchannel'){
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE priority > (SELECT priority FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")) AND id<>".GUESTCHANNELID." LIMIT 1)");
			}else if($type=="memberchannel"){
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE id IN (SELECT channelid FROM ".tbl_member." WHERE (id IN (SELECT memberid FROM ".tbl_orders." where sellermemberid = ".$MEMBERID." AND memberid != ".$MEMBERID." AND isdelete=0 GROUP BY memberid) OR id IN (SELECT submemberid FROM ".tbl_membermapping." where mainmemberid = ".$MEMBERID.")) GROUP BY channelid) AND id <>".GUESTCHANNELID.")");
			}else{
				$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID."))
										UNION
										(SELECT id,name,color FROM ".$this->_table." WHERE priority > (SELECT priority FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")) AND id<>".GUESTCHANNELID." LIMIT 1)");
			}
			
		}
		if($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
	}
	function getCurrentOrUpperChannelListByMember($MEMBERID,$type='withoutcurrentchannel'){
		if($type=='withoutcurrentchannel'){
			$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE priority < (SELECT priority FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")) AND id<>".GUESTCHANNELID." LIMIT 1)");
		}else{
			$query = $this->readdb->query("(SELECT id,name,color FROM ".$this->_table." WHERE priority < (SELECT priority FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID.")) AND id<>".GUESTCHANNELID." LIMIT 1)
									UNION
									(SELECT id,name,color FROM ".$this->_table." WHERE id = (SELECT channelid FROM ".tbl_member." WHERE id=".$MEMBERID."))");
		}
		
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
	}
	function getUpperChannelListById($channelid){
		
		$query = $this->readdb->query("(SELECT id,name FROM ".$this->_table." WHERE priority < (SELECT priority FROM ".$this->_table." WHERE id = ".$channelid."))");
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}else {
			return array();
		}	
	}
	function getLevelForRegister($type=""){
		
		$this->readdb->select("id,name");
		$this->readdb->from($this->_table);
		if($type=="API"){
			$this->readdb->where("mobileapplication=1");	
		}
		$this->readdb->where("status=1 AND id<>".GUESTCHANNELID);
		$query = $this->readdb->get();
		return $query->result_array();
	}
	function getChannelRewardPointsByIdOrReferralId($channelid,$referralid){

		if($channelid!=0){
			$where = "id=".$channelid." AND referandearn=1 AND rewardfornewregister!=0";
		}else{
			$where = "referandearn=1 AND rewardforrefferedby!=0 AND id IN (SELECT channelid FROM ".tbl_member." WHERE id=".$referralid.")";
		}
		
		$query = $this->readdb->select("rewardforrefferedby,rewardfornewregister,conversationrate as rate,")
							->from($this->_table)
							->where($where)
							->get();
		
		if ($query->num_rows() == 1) {
			return $query->row_array();
		}else {
			return array();
		}	
	}

	function getMemberChannelData($memberid){
		$query = $this->readdb->select("c.id as channelid,memberbasicsalesprice,memberspecificproduct,
									IFNULL((SELECT mainmemberid FROM ".tbl_membermapping." where submemberid='".$memberid."'),0) as currentsellerid,
									IFNULL((SELECT count(id) FROM ".tbl_memberproduct." where sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid='".$memberid."') AND memberid='".$memberid."'),0) as totalproductcount,
									c.addorderwithoutstock,
									c.offermodule,c.debitlimit,c.automaticgenerateinvoice,c.conversationrate
									")
									->from(tbl_channel." as c")
									->where("c.id = (SELECT channelid FROM ".tbl_member." WHERE id='".$memberid."')")
									->get();
                                    
		return $query->row_array();
	}
	
	function getMemberSpecificProductFlag($memberid){
		$query = $this->readdb->select("memberspecificproduct")
									->from(tbl_channel." as c")
									->where("c.id = (SELECT channelid FROM ".tbl_member." WHERE id=".$memberid.")")
									->get();
                                    
    	$channel = $query->row_array();
		$memberspecificproduct = (!empty($channel))?$channel['memberspecificproduct']:0;
		
		return $memberspecificproduct;
	}

	function getSameChannelReferrerMemberPoints($orderid){

		$query = $this->readdb->select("c.samechannelreferrermemberpoint,c.conversationrate,m.referralid")
									->from(tbl_orders." as o")
									->join(tbl_member." as m","o.memberid=m.id","INNER")
									->join(tbl_channel." as c","c.id=m.channelid AND c.samechannelreferrermemberpointonoff=1 AND c.samechannelreferrermemberpoint!=0 AND c.mimimumorderamountforsamechannelreferrer!=0 AND o.payableamount >= c.mimimumorderamountforsamechannelreferrer","INNER")
									->where("o.id=".$orderid." AND o.isdelete=0 AND m.channelid=IFNULL((SELECT m2.channelid FROM member as m2 WHERE m2.id=m.referralid),0)")
									->get();
		return $query->row_array();
	}

	//LISTING DATA
	function _get_datatables_query(){
		
		$this->readdb->select("id,name,status,priority,color");
		$this->readdb->from($this->_table);
	    
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
		return $query->result();
	}

	function count_filtered() {
		$this->_get_datatables_query();
		$query = $this->readdb->get();
		return $query->num_rows();
	}

	function count_all() {
		$this->readdb->from($this->_table);
		return $this->readdb->count_all_results();
	}
}
