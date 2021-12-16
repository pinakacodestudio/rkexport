<?php

class Reward_point_history_model extends Common_model {

	//put your code here
	public $_table = tbl_rewardpointhistory;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $column_order = array(null,'sellername','buyername',null,null,'pointstatus',null,null,null,'rh.createddate');    
    public $column_search = array('IFNULL(seller.name,"Company")','IFNULL(buyer.name,"")' ,'rh.point', 'rh.rate');
    public $order = array('rh.id' => 'DESC'); // default order

    //set column field database for member datatable searchable 
    public $column_ordermember = array(null,'sellername','buyername',null,null,'pointstatus',null,null,null,'rh.createddate');
	public $column_searchmember = array('IFNULL(seller.name,"Company")','IFNULL(buyer.name,"")' ,'rh.point', 'rh.rate');
	public $ordermember = array('rh.id' => 'DESC');
    
    function getPointHistoryReportDataOnAPI($userid,$channelid,$fromdate,$todate,$memberchannelid,$memberid,$transactiontype,$pointtype,$counter){
        
        if(empty($memberchannelid)){
            $memberchannelid = $channelid;
            $memberid = $userid;
        }
        $limit = 10;
        $this->readdb->select("rh.point,rh.rate,rh.type,rh.detail,rh.createddate,rh.transactiontype,
							IFNULL(rh.rate*rh.type,0) as amount,
                            IFNULL(seller.name,'Company') as sellername,
                            IFNULL(seller.membercode,'') as sellercode,
                            IFNULL(buyer.name, if(rh.frommemberid=0,m.name,'Company')) as buyername,
                            IFNULL(buyer.membercode,'') as buyercode,
                            IFNULL(seller.channelid,'') as sellerchannelid,
                            IFNULL(buyer.channelid, if(rh.frommemberid=0,m.channelid,'')) as buyerchannelid,
                            IFNULL(seller.id,'') as sellerid,
                            IFNULL(buyer.id, if(rh.frommemberid=0,m.id,'')) as buyerid,
                            IFNULL(o.id,'') as orderid,
                            IFNULL(o.orderid,'') as ordernumber,
                            @orderstatus := IFNULL(o.status,'-1') as orderstatus,
                            CASE WHEN @orderstatus=2 THEN 'Fail' WHEN @orderstatus=-1 OR @orderstatus=1 THEN 'Clear' ELSE 'Unclear' END as pointstatus,

                            IFNULL(
                                ((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=4)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))

                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.frommemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                +
			                    (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
                                
                            ,0) as closingpoint
                            
							");
		$this->readdb->from(tbl_rewardpointhistory." as rh");
		$this->readdb->join(tbl_member." as m","(FIND_IN_SET(m.channelid,\"".$memberchannelid."\")>0 OR '0'='".$memberchannelid."' OR  m.id='".$memberid."') AND m.status=1 AND (rh.frommemberid=m.id OR rh.tomemberid=m.id) AND (m.id='".$memberid."' OR '".$memberid."'='0')","INNER");
        
        $this->readdb->join(tbl_orders." as o","(o.memberrewardpointhistoryid=rh.id or o.sellermemberrewardpointhistoryid=rh.id or o.redeemrewardpointhistoryid=rh.id or o.samechannelreferrermemberpointid=rh.id) AND o.isdelete=0","LEFT");
        $this->readdb->join(tbl_offerparticipants." as op","op.redeemrewardpointhistoryid=rh.id AND op.status=1","LEFT");
        $this->readdb->join(tbl_creditnote." as c","c.redeemrewardpointhistoryid=rh.id AND c.status=1","LEFT");
        
        $this->readdb->join(tbl_member." as buyer","(buyer.id=o.memberid OR buyer.id=op.memberid OR buyer.id=c.buyermemberid)","LEFT");
		$this->readdb->join(tbl_member." as seller","(seller.id=o.sellermemberid OR seller.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=op.memberid) OR seller.id=c.sellermemberid)","LEFT");
        
        $this->readdb->where("(DATE(rh.createddate) BETWEEN '".$fromdate."' AND '".$todate."') AND (rh.type='".$pointtype."' OR ''='".$pointtype."') AND (rh.transactiontype='".$transactiontype."' OR ''='".$transactiontype."')");

        $this->readdb->order_by(key($this->order), $this->order[key($this->order)]);
        if($counter != -1){
			$this->readdb->limit($limit,$counter);
        }
        $query = $this->readdb->get();

        return $query->result_array();
    }
    function exportpointshistoryreport(){
        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
        $memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
        $transactiontype = isset($_REQUEST['transactiontype'])?$_REQUEST['transactiontype']:'';
        $channelid = (is_array($channelid))?implode(",",$channelid):$channelid;

        if(!is_null($this->session->userdata(base_url().'MEMBERID'))){
            if(empty($channelid)){
                $memberid = $this->session->userdata(base_url().'MEMBERID');
                $channelid = $this->session->userdata(base_url().'CHANNELID');
            }
        }
        
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        
		$this->readdb->select("rh.point,rh.rate,rh.type,rh.detail,rh.createddate,rh.transactiontype,
							IFNULL(rh.rate*rh.type,0) as amount,
                            IFNULL(seller.name,'Company') as sellername,
                            IFNULL(seller.membercode,'') as sellercode,
                            IFNULL(buyer.name, if(rh.frommemberid=0,m.name,'Company')) as buyername,
                            IFNULL(buyer.membercode,'') as buyercode,
                            IFNULL(seller.channelid,'') as sellerchannelid,
                            IFNULL(buyer.channelid, if(rh.frommemberid=0,m.channelid,'')) as buyerchannelid,
                            IFNULL(seller.id,'') as sellerid,
                            IFNULL(buyer.id, if(rh.frommemberid=0,m.id,'')) as buyerid,
                            @orderstatus := IFNULL(o.status,'-1') as orderstatus,
                            CASE WHEN @orderstatus=2 THEN 'Fail' WHEN @orderstatus=-1 OR @orderstatus=1 THEN 'Clear' ELSE 'Unclear' END as pointstatus,
                            ,

                            IFNULL(
                                ((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=4)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))

                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.frommemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                +
			                    (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
                                
                            ,0) as closingpoint
							");
		$this->readdb->from(tbl_rewardpointhistory." as rh");
		$this->readdb->join(tbl_member." as m","(FIND_IN_SET(m.channelid,\"".$channelid."\")>0 OR ''='".$channelid."' OR  m.id='".$memberid."') AND m.status=1 AND (rh.frommemberid=m.id OR rh.tomemberid=m.id) AND (m.id='".$memberid."' OR '".$memberid."'='')","INNER");
        $this->readdb->join(tbl_orders." as o","(o.memberrewardpointhistoryid=rh.id or o.sellermemberrewardpointhistoryid=rh.id or o.redeemrewardpointhistoryid=rh.id or o.samechannelreferrermemberpointid=rh.id) AND o.isdelete=0","LEFT");
        $this->readdb->join(tbl_offerparticipants." as op","op.redeemrewardpointhistoryid=rh.id AND op.status=1","LEFT");
        $this->readdb->join(tbl_creditnote." as c","c.redeemrewardpointhistoryid=rh.id AND c.status=1","LEFT");
        
        $this->readdb->join(tbl_member." as buyer","(buyer.id=o.memberid OR buyer.id=op.memberid OR buyer.id=c.buyermemberid)","LEFT");
		$this->readdb->join(tbl_member." as seller","(seller.id=o.sellermemberid OR seller.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=op.memberid) OR seller.id=c.sellermemberid)","LEFT");
		$this->readdb->where("(DATE(rh.createddate) BETWEEN '".$startdate."' AND '".$enddate."') AND (rh.type='".$type."' OR ''='".$type."') AND (rh.transactiontype='".$transactiontype."' OR ''='".$transactiontype."')");
        $this->readdb->order_by("rh.id DESC");
		
		$query = $this->readdb->get();
		//echo $this->readdb->last_query();exit;
		return $query->result();
	}

	function _get_datatables_query(){  
        $channelid = (isset($_REQUEST['channelid']) && $_REQUEST['channelid']!="")?$_REQUEST['channelid']:'';
        $memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
        $transactiontype = isset($_REQUEST['transactiontype'])?$_REQUEST['transactiontype']:'';
        $channelid = (is_array($channelid))?implode(",",$channelid):$channelid;
        
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        if(!is_null($this->session->userdata(base_url().'MEMBERID'))){
            //if($channelid == $this->session->userdata(base_url().'CHANNELID') && $memberid==0){
                
                if(empty($channelid)){
                    $channelid = $this->session->userdata(base_url().'CHANNELID');
                    $memberid = $this->session->userdata(base_url().'MEMBERID');
                }
            //}
        }
		
		$this->readdb->select("rh.point,rh.rate,rh.type,rh.detail,rh.createddate,rh.transactiontype,
							IFNULL(rh.rate*rh.type,0) as amount,
                            IFNULL(seller.name,'Company') as sellername,
                            IFNULL(seller.membercode,'') as sellercode,
                            IFNULL(buyer.name, if(rh.frommemberid=0,m.name,'Company')) as buyername,
                            IFNULL(buyer.membercode,'') as buyercode,
                            IFNULL(seller.channelid,'') as sellerchannelid,
                            IFNULL(buyer.channelid, if(rh.frommemberid=0,m.channelid,'')) as buyerchannelid,
                            IFNULL(seller.id,'') as sellerid,
                            IFNULL(buyer.id, if(rh.frommemberid=0,m.id,'')) as buyerid,
                            IFNULL(o.id,'') as orderid,
                            IFNULL(o.orderid,'') as ordernumber,
                            @orderstatus := IFNULL(o.status,'-1') as orderstatus,
                            CASE WHEN @orderstatus=2 THEN 'Fail' WHEN @orderstatus=-1 OR @orderstatus=1 THEN 'Clear' ELSE 'Unclear' END as pointstatus,

                            IFNULL(
                                ((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as samechannelreferrermemberpoint FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=5 AND rh2.id IN (SELECT o.samechannelreferrermemberpointid FROM ".tbl_orders." as o WHERE o.samechannelreferrermemberpointid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) )
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=0 AND rh2.transactiontype=4)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0))

                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.frommemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                +
			                    (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
                                
                            ,0) as closingpoint
                            
							");
		$this->readdb->from(tbl_rewardpointhistory." as rh");
		$this->readdb->join(tbl_member." as m","(FIND_IN_SET(m.channelid,\"".$channelid."\")>0 OR ''='".$channelid."' OR  m.id='".$memberid."') AND m.status=1 AND (rh.frommemberid=m.id OR rh.tomemberid=m.id) AND (m.id='".$memberid."' OR '".$memberid."'='')","INNER");
        $this->readdb->join(tbl_orders." as o","(o.memberrewardpointhistoryid=rh.id or o.sellermemberrewardpointhistoryid=rh.id or o.redeemrewardpointhistoryid=rh.id or o.samechannelreferrermemberpointid=rh.id) AND o.isdelete=0","LEFT");
        $this->readdb->join(tbl_offerparticipants." as op","op.redeemrewardpointhistoryid=rh.id AND op.status=1","LEFT");
        $this->readdb->join(tbl_creditnote." as c","c.redeemrewardpointhistoryid=rh.id AND c.status=1","LEFT");
        
        $this->readdb->join(tbl_member." as buyer","(buyer.id=o.memberid OR buyer.id=op.memberid OR buyer.id=c.buyermemberid)","LEFT");
		$this->readdb->join(tbl_member." as seller","(seller.id=o.sellermemberid OR seller.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=op.memberid) OR seller.id=c.sellermemberid)","LEFT");
        
        $this->readdb->where("(DATE(rh.createddate) BETWEEN '".$startdate."' AND '".$enddate."') AND (rh.type='".$type."' OR ''='".$type."') AND (rh.transactiontype='".$transactiontype."' OR ''='".$transactiontype."')");

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
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->order)) {
            $order = $this->order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            //echo $this->readdb->last_query(); exit;
            return $query->result();
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
    function getRewardPointHistoryListOnFront($memberid){
        
        $query = $this->readdb->select("rh.point,rh.rate,rh.type,rh.detail,rh.createddate,rh.transactiontype,
							IFNULL(rh.rate*rh.type,0) as amount,IFNULL(o.orderid,'--') as orderid,
                            
                            IFNULL(
                                ((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype=4))
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0)

                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.frommemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                +
			                    (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
                                
                            ,0) as closingpoint
                        ")
                    ->from(tbl_rewardpointhistory." as rh")
		            ->join(tbl_member." as m","m.id='".$memberid."' AND m.status=1 AND (rh.frommemberid=m.id OR rh.tomemberid=m.id)","INNER")
                    ->join(tbl_orders." as o","(o.memberrewardpointhistoryid=rh.id or o.sellermemberrewardpointhistoryid=rh.id or o.redeemrewardpointhistoryid=rh.id) AND o.isdelete=0","LEFT")
                    ->join(tbl_offerparticipants." as op","op.redeemrewardpointhistoryid=rh.id AND op.status=1","LEFT")
                    ->join(tbl_creditnote." as c","c.redeemrewardpointhistoryid=rh.id AND c.status=1","LEFT")
                    ->join(tbl_member." as buyer","(buyer.id=o.memberid OR buyer.id=op.memberid OR buyer.id=c.buyermemberid)","LEFT")
                    ->order_by("rh.id DESC")
                    ->get();
		            
        // echo $this->readdb->last_query();exit;
		
        return $query->result_array();
    }

    function member__get_datatables_query(){
        
        $memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        $type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
        $transactiontype = isset($_REQUEST['transactiontype'])?$_REQUEST['transactiontype']:'';
        
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        if(!is_null($this->session->userdata(base_url().'MEMBERID'))){
            //$memberid = $this->session->userdata(base_url().'MEMBERID');
        }
		
		$this->readdb->select("rh.point,rh.rate,rh.type,rh.detail,rh.createddate,rh.transactiontype,
							IFNULL(rh.rate*rh.type,0) as amount,
                            IFNULL(seller.name, 'Company') as sellername,
                            IFNULL(seller.membercode, '') as sellercode,
                            IFNULL(buyer.name, if(rh.frommemberid=0,m.name,'Company')) as buyername,
                            IFNULL(buyer.membercode, '') as buyercode,
                            IFNULL(seller.channelid, '') as sellerchannelid,
                            IFNULL(buyer.channelid, if(rh.frommemberid=0,m.channelid,'')) as buyerchannelid,
                            IFNULL(seller.id, '') as sellerid,
                            IFNULL(buyer.id, if(rh.frommemberid=0,m.id,'')) as buyerid,

                            IFNULL(
                                ((SELECT IFNULL(SUM(rh2.point),0) as earnbysalesandpurchaseorder FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype IN(1,2) AND (rh2.id IN (SELECT o.memberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.memberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT o.sellermemberrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.sellermemberrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0)))
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as refferandearn FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=0 AND rh2.transactiontype=4))
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as creditbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=0)

                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as redeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.frommemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT o.redeemrewardpointhistoryid FROM ".tbl_orders." as o WHERE o.redeemrewardpointhistoryid=rh2.id AND o.status=1 AND o.approved=1 AND o.isdelete=0) OR rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                -
                                (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.frommemberid=0 AND rh2.type=1 AND rh2.transactiontype=0)
                                +
                                (SELECT IFNULL(SUM(rh2.point),0) as sellerredeempoints FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = ".$memberid." AND rh2.type=1 AND rh2.transactiontype=3 AND (rh2.id IN (SELECT op2.redeemrewardpointhistoryid FROM ".tbl_offerparticipants." as op2 WHERE op2.redeemrewardpointhistoryid=rh2.id AND op2.status=1) OR rh2.id IN (SELECT cr.redeemrewardpointhistoryid FROM ".tbl_creditnote." as cr WHERE cr.redeemrewardpointhistoryid=rh2.id AND cr.status=1)))
                                +
			                    (SELECT IFNULL(SUM(rh2.point),0) as debitbyadmin FROM ".tbl_rewardpointhistory." as rh2 WHERE rh2.id<=rh.id AND rh2.tomemberid = m.id AND rh2.frommemberid=0 AND rh2.type=0 AND rh2.transactiontype=6)
                                
                            ,0) as closingpoint
							");
		$this->readdb->from(tbl_rewardpointhistory." as rh");
		$this->readdb->join(tbl_member." as m","m.id='".$memberid."' AND m.status=1 AND (rh.frommemberid=m.id OR rh.tomemberid=m.id)","INNER");
		$this->readdb->join(tbl_orders." as o","(o.memberrewardpointhistoryid=rh.id or o.sellermemberrewardpointhistoryid=rh.id or o.redeemrewardpointhistoryid=rh.id) AND o.isdelete=0","LEFT");
		$this->readdb->join(tbl_offerparticipants." as op","op.redeemrewardpointhistoryid=rh.id AND op.status=1","LEFT");
        $this->readdb->join(tbl_creditnote." as c","c.redeemrewardpointhistoryid=rh.id AND c.status=1","LEFT");
        $this->readdb->join(tbl_member." as buyer","(buyer.id=o.memberid OR buyer.id=op.memberid OR buyer.id=c.buyermemberid)","LEFT");
		$this->readdb->join(tbl_member." as seller","(seller.id=o.sellermemberid OR seller.id IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=op.memberid) OR seller.id=c.sellermemberid)","LEFT");
        
        $this->readdb->where("(DATE(rh.createddate) BETWEEN '".$startdate."' AND '".$enddate."') AND (rh.type='".$type."' OR ''='".$type."') AND (rh.transactiontype='".$transactiontype."' OR ''='".$transactiontype."')");

        $i = 0;

        foreach ($this->column_searchmember as $item) // loop column 
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

                if(count($this->column_searchmember) - 1 == $i) //last loop
                    $this->readdb->group_end(); //close bracket
            }
            $i++;
        }
        
        if(isset($_POST['order'])) { // here order processing
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        }else if(isset($this->ordermember)) {
            $order = $this->ordermember;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    function member_get_datatables() {
        $this->member__get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            //echo $this->readdb->last_query(); exit;
            return $query->result();
        }
    }

    function member_count_all() {
        $this->member__get_datatables_query();
        return $this->readdb->count_all_results();
    }

    function member_count_filtered() {
        $this->member__get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }
}
