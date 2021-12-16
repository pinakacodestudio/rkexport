<?php

class Country_model extends Common_model {

	//put your code here
	public $_table = tbl_country;
	public $_fields = "*";
	public $_where = array();
	public $_except_fields = array();
	public $_order = "name ASC";

	function __construct() {
		parent::__construct();
	}

	function getCountrycode() {
		$query = $this->readdb->select("id,phonecode,CONCAT(phonecode,' (',name,')') as phonecodewithname")
							->from($this->_table)
							->where("phonecode!='' AND phonecode!='+0' AND phonecode!='+00'")
							->group_by("phonecode")
							->order_by("phonecode","ASC")
							->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}

	}
	

	function getCountryDataByID($id) {
		$query = $this->readdb->select($this->_fields)
			->from($this->_table)
			->group_by($this->_order)
			->order_by("phonecode","ASC")
			->get();

		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getActivecountrylist() {

		$query = $this->readdb->select("id,name")
							->from($this->_table)
							
							->order_by("name ASC")							
							->get();
		return $query->result_array();
		
	}
	
	function getCountry($id=0) {

		$this->readdb->select("id,name");
		$this->readdb->from($this->_table);
		if($id!=0){
			$this->readdb->where("id=".$id);
		}
		$this->readdb->order_by($this->_order);
	
		$query = $this->readdb->get();
		if ($query->num_rows() > 0) {
			return $query->result_array();
		} else {
			return false;
		}
	}

	function getCountryDetailById($countryid) {

		$query = $this->readdb->select($this->_fields)
						->from($this->_table)
						->where("id=".$countryid)
						->get();
	
		return $query->row_array();
	}
}
