<?php

class Documents extends MY_Controller
{

    function __construct(){
        parent::__construct();
    }
    public $data=array();

    function getdocuments()
    {
        if ($this->input->server('REQUEST_METHOD') == 'POST' && !empty($this->input->post())) {
            $JsonArray = $this->input->post();            
            
            if(isset($JsonArray['apikey'])){
                $apikey = $JsonArray['apikey'];
                if($apikey=='' || $apikey!=APIKEY){
                   ws_response("Fail", "Authentication failed.");
                }else{
                    $PostData = json_decode($JsonArray['data'], true);
                    $query = $this->readdb->select("*");
                    $this->readdb->from(tbl_documents);
                    $this->readdb->where(array("status"=>1));
                    if(isset($PostData['search']) && $PostData['search']!=""){
                        $datearr = explode("/",$PostData['search']);
                            // arsort($arr);
                        $datestr = array();
                        if(count($datearr)>0){
                            foreach($datearr as $key=>$da){
                                $datestr[] = $datearr[count($datearr)-($key+1)];
                            }
                        }
                        $datesearch = implode("/",$datestr);
                        $datesearch = str_replace("/","-",$datesearch);
                        $this->readdb->where(array("(name like '%".$PostData['search']."%' or createddate like '%".$datesearch."%')"=>null));
                    }
                    if(isset($PostData['counter']) && $PostData['counter']!=-1){
                        $this->readdb->limit(10,$PostData['counter']);
                    }
                    $this->readdb->order_by("id desc");
                    $query = $this->readdb->get();
                    $document = $query->result_array();
                    
                    if(!empty($document)){
                        foreach ($document as $row) { 
                            $this->data[]= array("id"=>$row['id'],'name'=>$row['name'],'description'=>$row['description'],'filename'=>$row['filename'],"createddate"=>date("Y-m-d h:i:s a",strtotime($row['createddate'])));
                        }
                    }
                    if(empty($this->data)){
                        ws_response("Fail", "Documents not available.");
                    }else{
                        ws_response("Success", "",$this->data);
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
