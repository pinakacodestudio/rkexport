<?php

class Sales_analysis_model extends Common_model {

    public $_table = tbl_orders;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = 'id DESC';
   
    function __construct() {
        parent::__construct();
    }

    function getSalesAnalysisReportDataOnAPI($year,$month,$countryid,$provinceid,$cityid,$seller,$buyer,$counter){
        
        $limit = 10;
        $this->readdb->select("MONTH(o.orderdate) as month,YEAR(o.orderdate) as year,ct.id as ctid,ct.name as cityname,
                pr.id as prid,pr.name as provincename,cn.name as countryname,
                IFNULL(u.name,'') as employee,

                SUM(o.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as totalsales,

                buyer.id as buyerid,
                buyer.name as buyername,
                buyer.membercode as buyercode,
                buyer.channelid as buyerchannelid,

                IFNULL(seller.id,0) as sellerid,
                IFNULL(seller.name,'') as sellername,
                IFNULL(seller.membercode,'') as sellercode,
                IFNULL(seller.channelid,0) as sellerchannelid,
            ");

        $this->readdb->from($this->_table." as o");		
        $this->readdb->join(tbl_user." as u","u.id=o.salespersonid","LEFT");
        $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid","INNER");
        $this->readdb->join(tbl_city." as ct","ct.id=buyer.cityid","INNER");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","INNER"); 
        $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","INNER"); 
        $this->readdb->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT");
        $this->readdb->where("o.sellermemberid=".$seller." AND o.status=1 AND o.approved=1");
        
        if(!empty($seller)){			
            $this->readdb->where("(FIND_IN_SET(o.sellermemberid,'".$seller."')>0)");
        }

        $this->readdb->where("(FIND_IN_SET(o.memberid,'".$buyer."')>0 OR '".$buyer."'='')");
        if(!empty($buyer)){			
        }

        if(!empty($cityid)){			
            $this->readdb->where("(FIND_IN_SET(buyer.cityid,'".$cityid."')>0)");
        }
    
        if(!empty($provinceid)){			
            $this->readdb->where("(FIND_IN_SET(ct.stateid,'".$provinceid."')>0)");
        }
    
        if(!empty($countryid)){			
            $this->readdb->where("(FIND_IN_SET(pr.countryid,'".$countryid."')>0)");
        }
    
        if(!empty($year)){			
            $this->readdb->where("(YEAR(o.orderdate)='".$year."')");
        }
    
        if(!empty($month)){			
            $this->readdb->where("FIND_IN_SET(MONTH(o.orderdate),'".$month."')>0");
        }
        
        $this->readdb->group_by("o.sellermemberid");
        if(!empty($seller)){			
        }

        $this->readdb->group_by("o.memberid");
        if(!empty($buyer)){			
        }

        if(!empty($cityid)){			
            $this->readdb->group_by("buyer.cityid");
        }
    
        if(!empty($provinceid)){			
            $this->readdb->group_by("ct.stateid");
        }
    
        if(!empty($countryid)){			
            $this->readdb->group_by("pr.countryid");
        }
    
        if(!empty($year)){			
            $this->readdb->group_by("YEAR(o.orderdate)");
        }
    
        if(!empty($month)){			
            $this->readdb->group_by("MONTH(o.orderdate)");
        }
        $this->readdb->order_by("o.orderdate DESC");
        if($counter != -1){
			$this->readdb->limit($limit,$counter);
        }
        $query = $this->readdb->get();
        
        return $query->result_array();
    }
    function getSalesAnalysisData($employee,$year,$month,$countryid,$provinceid,$cityid,$seller,$buyer){
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');

        $this->readdb->select("MONTH(o.orderdate) as month,YEAR(o.orderdate) as year,ct.id as ctid,ct.name as cityname,
                pr.id as prid,pr.name as provincename,cn.name as countryname,
                IFNULL(u.name,'') as employee,

                SUM(o.payableamount + IFNULL((SELECT SUM(amount) FROM ".tbl_extrachargemapping." WHERE referenceid=o.id AND type=0),0)) as totalsales,

                buyer.id as buyerid,
                buyer.name as buyername,
                buyer.membercode as buyercode,
                buyer.channelid as buyerchannelid,

                IFNULL(seller.id,0) as sellerid,
                IFNULL(seller.name,'') as sellername,
                IFNULL(seller.membercode,'') as sellercode,
                IFNULL(seller.channelid,0) as sellerchannelid,
            ");

        $this->readdb->from($this->_table." as o");		
        if(is_null($MEMBERID)){
            $this->readdb->join(tbl_user." as u","u.id=o.salespersonid","INNER");
        }else{
            $this->readdb->join(tbl_user." as u","u.id=o.salespersonid","LEFT");
        }
        $this->readdb->join(tbl_member." as buyer","buyer.id=o.memberid","INNER");
        $this->readdb->join(tbl_city." as ct","ct.id=buyer.cityid","INNER");
        $this->readdb->join(tbl_province." as pr","pr.id=ct.stateid","INNER"); 
        $this->readdb->join(tbl_country." as cn","cn.id=pr.countryid","INNER"); 
        $this->readdb->join(tbl_member." as seller","seller.id=o.sellermemberid","LEFT");
        if(!is_null($MEMBERID)){
            $this->readdb->where("o.sellermemberid=".$MEMBERID." AND o.status=1 AND o.approved=1");
        }else{
            $this->readdb->where("o.salespersonid!=0 AND o.status=1 AND o.approved=1");
        }
        
        if(isset($employee) && $employee!=""){			
            $this->readdb->where("(FIND_IN_SET(o.salespersonid,'".$employee."')>0)");
        }
        
        if(isset($seller) && $seller!=""){			
            $this->readdb->where("(FIND_IN_SET(o.sellermemberid,'".$seller."')>0)");
        }

        if(isset($buyer) && $buyer!=""){			
            $this->readdb->where("(FIND_IN_SET(o.memberid,'".$buyer."')>0)");
        }

        if(isset($cityid) && $cityid!=""){			
            $this->readdb->where("(FIND_IN_SET(buyer.cityid,'".$cityid."')>0)");
        }
    
        if(isset($provinceid) && $provinceid!=""){			
            $this->readdb->where("(FIND_IN_SET(ct.stateid,'".$provinceid."')>0)");
        }
    
        if(isset($countryid) && $countryid!=""){			
            $this->readdb->where("(FIND_IN_SET(pr.countryid,'".$countryid."')>0)");
        }
    
        if(isset($year) && $year!=""){			
            $this->readdb->where("(YEAR(o.orderdate)='".$year."')");
        }
    
        if(isset($month) && $month !=""){			
            $this->readdb->where("FIND_IN_SET(MONTH(o.orderdate),'".$month."')>0");
        }
        
        if(isset($employee) && $employee!=""){
            $this->readdb->group_by("o.salespersonid");
        }
        if(isset($seller) && $seller!=""){			
           $this->readdb->group_by("o.sellermemberid");
        }

        if(isset($buyer) && $buyer!=""){			
            $this->readdb->group_by("o.memberid");
        }

        if(isset($cityid) && $cityid!=""){			
            $this->readdb->group_by("buyer.cityid");
        }
    
        if(isset($provinceid) && $provinceid!=""){			
            $this->readdb->group_by("ct.stateid");
        }
    
        if(isset($countryid) && $countryid!=""){			
            $this->readdb->group_by("pr.countryid");
        }
    
        if(isset($year) && $year!=""){			
            $this->readdb->group_by("YEAR(o.orderdate)");
        }
    
        if(isset($month) && $month !=""){			
            $this->readdb->group_by("MONTH(o.orderdate)");
        }
        $this->readdb->order_by("o.orderdate DESC");
        $query = $this->readdb->get();
        
        return $query->result();
    }
    
    function getCity(){
        $query = $this->readdb->select("c.id as cid,c.name as cityname,c.stateid")		
                        ->from(tbl_member." as m")
                        ->join(tbl_orders." as o","o.memberid=m.id AND o.isdelete=0","INNER")
                        ->join(tbl_city." as c","m.cityid=c.id","INNER")
                        ->group_by("m.cityid")
                        ->order_by("c.name ASC")
                        ->get();	
        
        return $query->result_array();
    }

    function getState($stateid){
        $query = $this->readdb->select("s.id as sid,s.name as statename,s.countryid")
                        ->from(tbl_province." as s")
                        ->where("FIND_IN_SET(s.id,'".$stateid."')>0")
                        ->order_by("s.name ASC")
                        ->get();	

        return $query->result_array();
    }

    function getCountry($countryid){
        $query = $this->readdb->select("co.id as coid,co.name as countryname")
                        ->from(tbl_country." as co")
                        ->where("FIND_IN_SET(co.id,'".$countryid."')>0")
                        ->order_by("co.name ASC")
                        ->get();	
                        
        return $query->result_array();
    }

    function getSellerBySales(){

        $query = $this->readdb->select("m.id,CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')') as name")		
                        ->from(tbl_member." as m")
                        ->join(tbl_orders." as o","o.sellermemberid=m.id AND o.isdelete=0","INNER")
                        ->group_by("o.sellermemberid")
                        ->get();	
        
        return $query->result_array();
    }

    function getBuyerBySales($sellerid=0){

        $query = $this->readdb->select("m.id,CONCAT(m.name,' (',m.membercode,' - ',m.mobile,')') as name")		
                        ->from(tbl_member." as m")
                        ->join(tbl_orders." as o","o.memberid=m.id AND o.isdelete=0","INNER")
                        ->where("(o.sellermemberid=".$sellerid." OR ".$sellerid."=0)")
                        ->group_by("o.memberid")
                        ->get();	
        
        return $query->result_array();
    }
}  