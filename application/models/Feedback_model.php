<?php

class Feedback_model extends Common_model {

    public $_table = tbl_feedback;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $_order = array('id' => 'DESC');

    public $column_order = array(null,'m.name', 'f.subject', 'f.message','f.createddate');
        
    public $column_search = array('m.name', 'f.subject', 'f.message','f.createddate');

    function __construct() {
        parent::__construct();
    }
    
    function recentfeedback(){
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        
        $this->readdb->select('message,subject');
        $this->readdb->from($this->_table.' AS f');
        $this->readdb->join(tbl_member.' AS m', 'm.id = f.memberid', 'INNER');
        $this->readdb->where(array("DATE(f.createddate)"=>date("Y-m-d"))); 
        if(!is_null($MEMBERID)){
            $this->readdb->where(array("memberid"=>$MEMBERID)); 
        }
        $this->readdb->limit(100); 
        $this->readdb->order_by("f.id","desc");   
        $query=$this->readdb->get();
        return $query->result_array();
    }

    function get_datatables() {
       $this->_get_datatables_query();
        if($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
            $query = $this->readdb->get();
            return $query->result();
        }
       }

    function _get_datatables_query(){  

    	$this->readdb->select('f.id, m.name, m.membercode,m.email,m.mobile,m.channelid,f.subject,f.message,f.createddate,memberid');
		$this->readdb->from($this->_table.' AS f');
		$this->readdb->join(tbl_member.' AS m', 'm.id = f.memberid', 'INNER');
          
        $where='';
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		if(!is_null($MEMBERID)) {
            $this->readdb->where("(f.memberid=".$MEMBERID." OR f.memberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$MEMBERID."))");
        }
        /*$query = $this->db->select("f.id, (select name from ".tbl_customer." where  id=f.customerid)as customername,(select email from ".tbl_customer." where id=f.customerid)as email, (select mobile from ".tbl_customer." where  id=f.customerid)as mobile,f.message,f.createddate")
				->from($this->_table .' AS f');*/
				
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

     function CheckContent($contentid,$id=''){

        if (isset($id) && $id != '') {
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid." AND id <> '".$id."'");
        }else{
            $query = $this->readdb->query("SELECT id FROM ".$this->_table." WHERE contentid =".$contentid);
        }
       
        if($query->num_rows()  > 1){
            return 0;
        }
        else{
            return 1;
        }
    }

}

	
    

?>