<?php

        class Insurance_agent_model extends Common_model {
        
            //put your code here
            public $_table = tbl_insuranceagent;
            public $_fields = "*";
            public $_where = array();
            public $_except_fields = array();
            
            function __construct() {
                parent::__construct();
            }
            
            function getInsuranceAgentDataByID($ID){
                $query = $this->readdb->select("id,insuranceid,agentname,email,mobileno,status")
                                    ->from($this->_table)
                                    ->where("id", $ID)
                                    ->get();
                
                if ($query->num_rows() == 1) {
                    return $query->row_array();
                }else {
                    return 0;
                }	
            }
        
            function getInsuranceAgentData($order="DESC"){
                
                $query = $this->readdb->select("id,
                (SELECT GROUP_CONCAT(companyname) FROM ".tbl_insurance." WHERE FIND_IN_SET(id,insuranceid)) as insurancecompany, 
                agentname,email,mobileno,status,createddate")
                        ->from($this->_table)
                        ->order_by("id", $order)
                        ->get();
        
                return $query->result_array();
            }

            function getInsuranceAgentDataByInsurance($insurancename){
                $query = $this->readdb->select("a.id,a.agentname")
                        ->from($this->_table." as a")
                        ->join(tbl_insurance." as i","FIND_IN_SET(i.id,a.insuranceid)>0","INNER")
                        ->where("status=1 AND i.companyname='".$insurancename."'")
                        ->group_by("a.id")
                        ->get();
        
                return $query->result_array();
            }
        }
        ?>