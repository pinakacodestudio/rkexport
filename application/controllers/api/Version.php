<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Version extends MY_Controller {

    public $PostData = array();
    public $data = array();

    function __construct() {
        parent::__construct();
        if ($this->input->server("REQUEST_METHOD") == 'POST' && !empty($this->input->post())) {
            $this->PostData = $this->input->post();

            if (isset($this->PostData['apikey'])) {
                $apikey = $this->PostData['apikey'];
                if ($apikey == '' || $apikey != APIKEY) {
                    ws_response('fail', API_KEY_NOT_MATCH);
                }
            } else {
                ws_response('fail', API_KEY_MISSING);
                exit;
            }
        } else {
            ws_response('fail', 'Authentication failed');
            exit;
        }
    }
    function checkversion()
    {
        $PostData = json_decode($this->PostData['data'],true);
        
        $version = isset($PostData['version']) ? trim($PostData['version']) : '';
        $type = isset($PostData['type']) ? trim($PostData['type']) : '';

        if(empty($version) || $type == "") {
            ws_response('fail', EMPTY_PARAMETER);
        }else {
            
            $modifieddate = $this->general_model->getCurrentDateTime();

            $query=$this->db->query("SELECT versionname FROM versioncheck WHERE devicetype=".$type); 
            $VersionData = $query->row_array();
                
            if(!empty($VersionData)){

                if($VersionData['versionname'] > $version){
                  
                    ws_response('success', "" , array("currentversion"=>$VersionData['versionname']));
                
                }else if($VersionData['versionname'] < $version){
                    
                    $Edit = $this->db->query("UPDATE versioncheck SET versionname='".$version."', modifieddate='".$modifieddate."' WHERE devicetype=".$type); 
                    
                    if($Edit){
                        ws_response('success', "" , array("currentversion"=>$version));
                    }
                    else{
                        ws_response('Fail', "" , array("currentversion"=>$VersionData['versionname']));
                    }
                }else{
                    ws_response('Fail', "" , array("currentversion"=>$VersionData['versionname']));
                }                
            }else{
                $Add = $this->db->query("INSERT INTO versioncheck(versionname, devicetype, modifieddate) VALUES ('".$version."',".$type.",'".$modifieddate."')"); 
                    
                if($Add){
                    ws_response('success', "" , array("currentversion"=>$version));
                }else{
                    ws_response('Fail', "" , array("currentversion"=>$version));
                }
            }
        }
    } 
}