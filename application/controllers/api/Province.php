<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Province extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function getprovince() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['countryid'])){

                        $countryid = $PostData['countryid'];
                        
                        if($countryid==''){
                            ws_response("Fail", "Authentication Faild");
                        }else{
                            $query = $this->readdb->select("p.id, p.name, p.countryid");
                            $this->readdb->from(tbl_province." as p");
                            if(isset($PostData['userid']) && isset($PostData['type'])) {
                                if($PostData['type']=="ordercancelreport"){
                                    $this->readdb->join(tbl_city." as c","c.stateid=p.id","INNER");
                                    $this->readdb->join(tbl_member." as m","m.cityid=c.id","INNER");
                                    $this->readdb->join(tbl_orders." as o","o.memberid=m.id AND o.status=2 AND o.isdelete=0","INNER");
                                    $this->readdb->where("m.status=1 AND o.sellermemberid='".$PostData['userid']."'");
                                }else if($PostData['type']=="salesanalysis"){
                                    $this->readdb->join(tbl_city." as c","c.stateid=p.id","INNER");
                                    $this->readdb->join(tbl_member." as m","m.cityid=c.id","INNER");
                                    $this->readdb->join(tbl_orders." as o","o.memberid=m.id AND o.isdelete=0","INNER");
                                    $this->readdb->where("m.status=1 AND o.sellermemberid='".$PostData['userid']."'");
                                }
                            }
                            $this->readdb->where("FIND_IN_SET(p.countryid,'".$countryid."')>0");
                            $this->readdb->group_by("p.id");
                            $this->readdb->order_by('p.name','ASC');
                            $query = $this->readdb->get();
                            $ProvinceData = $query->result_array();      
                                                       
                            if(!empty($ProvinceData)){
                                foreach ($ProvinceData as $row) {              
                                     $this->data[]= array("id"=>$row['id'],"name"=>$row['name'],"countryid"=>$row['countryid']);
                                }
                            }
                            if(empty($this->data)){
                               ws_response("Fail", "No more data found.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

    function getcity() {
        
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{

                    $PostData = json_decode($JsonArray['data'], true);
                    if(isset($PostData['stateid'])){

                        $stateid = $PostData['stateid'];
                        
                        if($stateid==''){
                            ws_response("Fail", "Fields value are missing.");
                        }else{

                            $query = $this->readdb->select("c.id, c.name, c.stateid, c.latitude, c.longitude");
                            $this->readdb->from(tbl_city." as c");
                            if(isset($PostData['userid']) && isset($PostData['type'])) {
                                if($PostData['type']=="ordercancelreport"){
                                    $this->readdb->join(tbl_member." as m","m.cityid=c.id","INNER");
                                    $this->readdb->join(tbl_orders." as o","o.memberid=m.id AND o.status=2 AND o.isdelete=0","INNER");
                                    $this->readdb->where("m.status=1 AND o.sellermemberid='".$PostData['userid']."'");
                                }else if($PostData['type']=="salesanalysis"){
                                    $this->readdb->join(tbl_member." as m","m.cityid=c.id","INNER");
                                    $this->readdb->join(tbl_orders." as o","o.memberid=m.id AND o.isdelete=0","INNER");
                                    $this->readdb->where("m.status=1 AND o.sellermemberid='".$PostData['userid']."'");
                                }
                            }
                            $this->readdb->where("FIND_IN_SET(c.stateid,'".$stateid."')>0");
                            $this->readdb->group_by("c.id");
                            $this->readdb->order_by('c.name','ASC');
                            $query = $this->readdb->get();
                            $CityData = $query->result_array();      

                            /* $query = $this->readdb->select("id, name, stateid, latitude, longitude")
                                     ->from(tbl_city)
                                     ->where("FIND_IN_SET(stateid, '".$stateid."') !=", 0)
                                     ->order_by('name','ASC')
                                     ->get();
                                        
                            $CityData = $query->result_array(); */      
                           
                            if(!empty($CityData)){
                                foreach ($CityData as $row) {              
                                    $this->data[]= array("id"=>$row['id'],
                                                    "name"=>$row['name'],
                                                    "stateid"=>$row['stateid'],
                                                    "latitude"=>$row['latitude'],
                                                    "longitude"=>$row['longitude']
                                                );
                                }
                            }
                            if(empty($this->data)){
                                ws_response("Fail", "No more data found.");
                            }else{
                                ws_response("Success", "",$this->data);
                            }
                        }
                    }else{
                        ws_response("Fail", "Fields are missing.");
                    } 
                }
            }else{
                ws_response("Fail", "Fields are missing.");
            }    
        }else{
            ws_response("Fail", "Authentication failed.");
        }
    }

}