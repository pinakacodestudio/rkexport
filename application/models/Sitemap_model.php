<?php

class Sitemap_model extends Common_model {

	//put your code here
      public $_table = tbl_sitemap;
      public $_fields = "*";
      public $_where = array();
      public $_except_fields = array();
      public $column_order = array(null,'slug','lastchange','priority'); //set column field database for datatable orderable
      public $column_search = array('slug');

      function __construct() {
            parent::__construct();
      }
    
     
      function getSitemapData($channelid=0,$memberid=0){

            $query = $this->readdb->select("id,slug,lastchange,priority,changefrequency")
                              ->from($this->_table)
                              ->where("status=1 AND channelid='".$channelid."' AND memberid='".$memberid."'")
                              ->order_by("priority", "DESC")
                              ->get();
            
            return $query->result_array();
      }
      function CheckDuplicateValue($slug,$id='',$channelid=0,$memberid=0)
      {
            if (isset($id) && $id != '') {
                  $query = $this->readdb->query("SELECT slug FROM ".tbl_sitemap." WHERE slug ='".$slug."' AND id <> '".$id."' AND channelid='".$channelid."' AND memberid='".$memberid."'");
            }else{
                  $query = $this->readdb->query("SELECT slug FROM ".tbl_sitemap." WHERE slug ='".$slug."'");
            }
            
            if($query->num_rows()  > 0){
                  return 0;
            }
            else{
                  return 1;
            }
      }
     
      //LISTING DATA
      function _get_datatables_query(){
            
            $channelid = $this->session->userdata(base_url().'CHANNELID');
            $memberid = $this->session->userdata(base_url().'MEMBERID');

            $query = $this->readdb->select("id,channelid,memberid,slug,lastchange,priority,changefrequency,status");
            $this->readdb->from($this->_table);
            $this->readdb->where("channelid='".$channelid."' AND memberid='".$memberid."'");
      
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

      function get_datatables() {
            $this->_get_datatables_query();
            if($_POST['length'] != -1) {
                  $this->readdb->limit($_POST['length'], $_POST['start']);
                  $query = $this->readdb->get();
                  return $query->result();
            }
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
}
?>