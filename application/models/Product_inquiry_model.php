<?php

class Product_inquiry_model extends Common_model {

    public $_table = tbl_productinquiry;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = array('pi.id' => 'DESC');

    public $column_order = array('pi.id','membername', 'productname', 'pi.name',null,'pi.createddate','pi.email',null,null,null);
    public $column_search = array('m.name', 'p.name', 'pi.name','pi.email','pi.mobile','pi.organizations','pi.address','pi.createddate','pi.type');

    function __construct() {
        parent::__construct();
    }
    
    function getProductInquiryOfSalesPersonMember($employeeid,$channelid=0,$memberid=0,$counter){
        $limit=10;
        $counter = ($counter < 0)?0:$counter;

        $query = $this->readdb->select('pi.id, pi.productid,p.name as productname,pi.memberid,pi.msg,
                    IFNULL((SELECT name FROM '.tbl_channel.' WHERE id=m.channelid),"") as channel,
                    IFNULL(m.name ,"")as membername,IFNULL(m.membercode,"") as membercode,IFNULL(m.channelid,0) as channelid,pi.name,pi.email,pi.mobile,
                    pi.organizations,IF(pi.type=1,"Website","App") as typename,pi.address,pi.createddate,pi.addedby,p.isuniversal')

                ->from($this->_table.' as pi')
                ->join(tbl_member.' as m', 'm.id = pi.memberid', 'INNER')
                ->join(tbl_product.' as p', 'p.id = pi.productid', 'INNER')
                ->where("pi.memberid != 0 AND pi.memberid IN (SELECT memberid FROM ".tbl_salespersonmember." WHERE employeeid=".$employeeid.")")
                ->where("(m.channelid=".$channelid." OR ".$channelid."=0)")
                ->where("(FIND_IN_SET(pi.memberid, '".$memberid."') OR ".$memberid."=0)")
                ->order_by("pi.id DESC")
                ->limit($limit,$counter)
                ->get();

        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return array();
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

    function _get_datatables_query(){  
   
        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
        $memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
        
        
        if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
            $channelid = $this->session->userdata(base_url().'CHANNEL');
        }
        $channelid = (is_array($channelid))?implode(",",$channelid):0;
        $productid = isset($_REQUEST['productid'])?$_REQUEST['productid']:0;
        $startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);
        $requesttype = isset($_REQUEST['requesttype'])?$_REQUEST['requesttype']:0;
        $type = isset($_REQUEST['type'])?$_REQUEST['type']:0;
       

        $this->readdb->select('pi.id, pi.productid,p.name as productname,pi.memberid,pi.msg,
                            IFNULL(m.name ,"")as membername,IFNULL(m.membercode,"") as membercode,IFNULL(m.channelid,0) as channelid,pi.name,pi.email,pi.mobile,
                            pi.organizations,IF(pi.type=1,"Website","App") as typename,pi.address,pi.createddate,pi.addedby,p.isuniversal,
                            (SELECT GROUP_CONCAT(pc.variantid) FROM '.tbl_productcombination.' as pc INNER JOIN '.tbl_productprices.' as pp on pp.id=pc.priceid WHERE pp.productid=p.id) as variantid');
        
        $this->readdb->from($this->_table.' as pi');
        
        $this->readdb->join(tbl_member.' as m', 'm.id = pi.memberid', 'LEFT');
        $this->readdb->join(tbl_product.' as p', 'p.id = pi.productid', 'INNER');
        

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $where = '';
        if($channelid!=0){
            
            $where .= ' AND pi.memberid IN (SELECT id FROM '.tbl_member.' WHERE channelid IN ('.$channelid.'))';
        }
        if($memberid!=0){
            $where .= " AND FIND_IN_SET(pi.memberid, '".$memberid."')";
        }
        if($productid!=0){
            $where .= " AND FIND_IN_SET(pi.productid, '".$productid."')";
        }   
            
            
        
        
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        if(!is_null($MEMBERID)){
            if($requesttype!=0){
                if($requesttype==1){                    
                    $where .= '  AND (pi.memberid="'.$MEMBERID.'" OR pi.memberid=0)';
                }
                if($requesttype==2){
                    $where .= ' AND (pi.memberid IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$MEMBERID.')  OR pi.memberid=0)';
                }
            }   
            
            $where .= ' AND (pi.memberid IN (SELECT submemberid FROM '.tbl_membermapping.' WHERE mainmemberid = '.$MEMBERID.') OR pi.memberid='.$MEMBERID.' OR pi.memberid=0)';
        }
       
                         
        $where .= ' AND (pi.type="'.$type.'" OR  "" ="'.$type.'")';
        
       
       /*  if($channelid != 0){
			if(is_array($channelid)){
				$channelid = implode(",",$channelid);
            }
            $where .= ' AND pi.memberid IN (SELECT id FROM '.tbl_member.' WHERE channelid IN ('.$channelid.'))';
        } */
        
       $this->readdb->where("DATE(pi.createddate) BETWEEN '".$startdate."' AND '".$enddate."'".$where);
        
        /* $arrSessionDetails = $this->session->userdata;        
        $member_login = isset($arrSessionDetails[base_url().'CHANNELLOGIN']) ? $arrSessionDetails[base_url().'CHANNELLOGIN'] : "";
		
		$where='';
		if($member_login && $member_login == TRUE) {
            $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
            $REPORTINGTO = $this->session->userdata(base_url().'REPORTINGTO');
            $this->readdb->where("(f.memberid=".$MEMBERID." OR (f.memberid=".$REPORTINGTO."))");
        } */
     
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