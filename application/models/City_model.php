<?php

class City_model extends Common_model
{

    //put your code here
    public $_table = tbl_city;
    public $_fields = "*";
    public $_where = array();
    public $_except_fields = array();
    public $column_order = array(null, 'name', 'provincename', 'countryname'); //set column field database for datatable orderable
    public $column_search = array('name'); //set column field database for datatable searchable
    public $order = array('id' => 'DESC'); // default order

    public function __construct()
    {
        parent::__construct();
    }

    public function getCityByProvince($provinceid)
    {
        $query = $this->readdb->select("id,name,stateid")
            ->from($this->_table)
            ->where("stateid", $provinceid)
            ->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return 0;
        }
    }

    public function getCityDataByID($ID)
    {
        $query = $this->readdb->select("id,name,(SELECT countryid FROM " . tbl_province . " WHERE id=stateid) as countryid,stateid")
            ->from($this->_table)
            ->where("id", $ID)
            ->get();

        if ($query->num_rows() == 1) {
            return $query->row_array();
        } else {
            return 0;
        }
    }
    public function getCourierExpensesCity()
    {

        $this->readdb->select("ct.id,ct.name");
        $this->readdb->from($this->_table . " as ct");
        $this->readdb->where("ct.id IN (SELECT cityid FROM " . tbl_memberaddress . " WHERE id IN (SELECT addressid FROM " . tbl_invoice . "))");
        $this->readdb->group_by("ct.id");
        $this->readdb->order_by($this->_order);

        $query = $this->readdb->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }
    public function getActiveCityOnAssignedSiteByParty($partyid)
    {

        $this->readdb->select("ct.id,ct.name");
        $this->readdb->from($this->_table . " as ct");
        $this->readdb->where("ct.id IN (SELECT cityid FROM " . tbl_site . " WHERE id IN (SELECT siteid FROM " . tbl_sitemapping . " WHERE partyid=" . $partyid . "))");
        $this->readdb->group_by("ct.id");
        $this->readdb->order_by($this->_order);

        $query = $this->readdb->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return false;
        }
    }

    public function searchcity($type, $search)
    {
        $this->readdb->select("id,CONCAT(name,' (',(SELECT name FROM " . tbl_province . " WHERE id=stateid),')') as text");
        $this->readdb->from($this->_table);
        if ($type == 1) {
            $this->readdb->where("name LIKE '%" . $search . "%'");
        } else {
            $this->readdb->where("id=" . $search . "");
        }
        $query = $this->readdb->get();

        if ($query->num_rows() > 0) {
            if ($type == 1) {
                return $query->result_array();
            } else {
                return $query->row_array();
            }
        } else {
            return 0;
        }
    }

    //LISTING DATA
    public function _get_datatables_query()
    {

        $this->readdb->select("id,name,(SELECT name FROM " . tbl_country . " WHERE id=(SELECT countryid FROM " . tbl_province . " WHERE id=stateid)) as countryname,
							(SELECT name FROM " . tbl_province . " WHERE id=stateid) as provincename");
        $this->readdb->from($this->_table);

        $i = 0;

        foreach ($this->column_search as $item) // loop column
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->readdb->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->readdb->like($item, $_POST['search']['value']);
                } else {
                    $this->readdb->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                {
                    $this->readdb->group_end();
                }
                //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->readdb->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->readdb->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->readdb->limit($_POST['length'], $_POST['start']);
        }

        $query = $this->readdb->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->readdb->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->readdb->from($this->_table);
        return $this->readdb->count_all_results();
    }

    public function getcity($postData)
    {
        $response = array();
        $this->db->select('id,name as cityname');
        $this->db->where('stateid', $postData['stat']);
        $q = $this->db->get(tbl_city);
        $response = $q->result_array();
        return $response;
    }
}
