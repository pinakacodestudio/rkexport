<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends Common_model {

	public $_table = tbl_transaction;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = 'tr.id DESC';
	public $_detatableorder = array('tr.createddate'=>'DESC');
    public $column_order = array(null,'membername','ordernumber','tr.transactionid','tr.transcationcharge','o.paymenttype', 'tr.payableamount', 'tr.createddate');    
    public $column_search = array('m.name','o.orderid' ,'tr.payableamount', 'tr.createddate','tr.transactionid','tr.transcationcharge');


	function __construct() {
		parent::__construct();
	}
	function _get_datatables_query(){  
        $channelid = isset($_REQUEST['channelid'])?$_REQUEST['channelid']:0;
        $memberid = isset($_REQUEST['memberid'])?$_REQUEST['memberid']:0;
       
        if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
            $channelid = $this->session->userdata(base_url().'CHANNEL');
        }
        
        $channelid = (is_array($channelid))?implode(",",$channelid):$channelid;
        $paymenttype = isset($_REQUEST['paymenttype'])?$_REQUEST['paymenttype']:0;
        
		$startdate = $this->general_model->convertdate($_REQUEST['startdate']);
        $enddate = $this->general_model->convertdate($_REQUEST['enddate']);

        $this->readdb->select("tr.orderid,m.id as memberid,
                            m.name as membername,
                            m.membercode,
                            m.channelid,o.orderid as ordernumber,
                            tr.createddate,o.paymenttype,
                            tr.payableamount,tr.transactionid,
                            tr.transcationcharge,
                            (SELECT file FROM ".tbl_transactionproof." WHERE transactionid=tr.id LIMIT 1) as transactionproof,
                            tr.paymentgetwayid
                        ");
        
		$this->readdb->from($this->_table." as tr");
		$this->readdb->join(tbl_orders." as o","o.id=tr.orderid AND o.isdelete=0","INNER");
		$this->readdb->join(tbl_member." as m","m.id=o.memberid","LEFT");
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $where = '';
        if(!is_null($MEMBERID)){
            if($channelid!=0){
                $where .= " AND (FIND_IN_SET(m.channelid, '".$channelid."') OR ''='".$channelid."')";
            }
            if($paymenttype!=0){
                $where .= " AND (FIND_IN_SET(o.paymenttype, '".$paymenttype."') OR 0='".$paymenttype."')";
            }
            if($memberid!=0){
                $where .= " AND (FIND_IN_SET(o.memberid, '".$memberid."') OR 0='".$memberid."')";
            }
         
            $this->readdb->where("(o.memberid IN (SELECT submemberid FROM ".tbl_membermapping." WHERE mainmemberid=".$MEMBERID.") OR o.memberid=".$MEMBERID.") AND DATE(tr.createddate) BETWEEN '".$startdate."' AND '".$enddate."'".$where);
        }else{
            $this->readdb->where("(FIND_IN_SET(m.channelid, '".$channelid."')>0 OR ''='".$channelid."') AND (FIND_IN_SET(m.id, '".$memberid."')>0 OR 0='".$memberid."') AND (FIND_IN_SET(o.paymenttype, '".$paymenttype."')>0 OR 0='".$paymenttype."')");
           
            $this->readdb->where("DATE(tr.createddate) BETWEEN '".$startdate."' AND '".$enddate."'");
        }
        
        
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
        }else if(isset($this->_detatableorder)) {
            $order = $this->_detatableorder;
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
}