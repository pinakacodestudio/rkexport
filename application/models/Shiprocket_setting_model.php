<?php
class Shiprocket_setting_model extends Common_model{
	
public $_table = tbl_shiprocketsetting;
public $_fields = "*";
public $_where = array();
public $_except_fields = array();

function __construct() {
	parent::__construct();
}

function getsetting($channelid=0,$memberid=0)
{
	$this->readdb->select('s.*');
    $this->readdb->from(tbl_shiprocketsetting." as s");
	$this->readdb->where('s.memberid="'.$memberid.'" AND s.channelid="'.$channelid.'"');
	$this->readdb->limit(1);
    $query = $this->readdb->get();
	return $query->row_array();
}


}
        
