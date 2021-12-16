<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sitemap extends Channel_Controller 
{

    public $viewData = array();

    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu','Sitemap');
        $this->load->model('Sitemap_model', 'Sitemap');
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Sitemap";
        $this->viewData['module'] = "sitemap/Sitemap";

        $this->channel_headerlib->add_javascript("Sitemap","pages/sitemap.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
    
        $list = $this->Sitemap->get_datatables();
       
        
        $data = array();
        $counter = $_POST['start'];
        foreach ($list as $datarow) {
            $row = array();
          
            $row[] = ++$counter;
            $row[] = '<a href="'.DOMAIN_URL.$datarow->slug.'" target="_blank" title="'.ucwords($datarow->slug).'">'.ucwords(DOMAIN_URL.$datarow->slug).'</a>';
            $row[] = $this->general_model->displaydatecustome($datarow->lastchange);        
        
            if($datarow->priority==0){
              $row[] = "0.0";
            }elseif($datarow->priority==1){
              $row[] = "0.1";
            }elseif($datarow->priority==2){
              $row[] = "0.2";
            }elseif($datarow->priority==3){
              $row[] = "0.3";
            }elseif($datarow->priority==4){
              $row[] = "0.4";
            }elseif($datarow->priority==5){
              $row[] = "0.5";
            }elseif($datarow->priority==6){
              $row[] = "0.6";
            }elseif($datarow->priority==7){
              $row[] = "0.7";
            }elseif($datarow->priority==8){
              $row[] = "0.8";
            }elseif($datarow->priority==9){
              $row[] = "0.9";
            }elseif($datarow->priority==10){
              $row[] = "1.0";
            }
    
            if($datarow->changefrequency==0){
              $row[] = "Always";
            }elseif($datarow->changefrequency==1){
              $row[] = "Hourly";
            }elseif($datarow->changefrequency==2){
              $row[] = "Daily";
            }elseif($datarow->changefrequency==3){
              $row[] = "Weekly";
            }elseif($datarow->changefrequency==4){
              $row[] = "Monthly";
            }elseif($datarow->changefrequency==5){
              $row[] = "Yearly";
            }elseif($datarow->changefrequency==6){
              $row[] = "Never";
            }
          
            $Action='';
            if(in_array($rollid, $edit)) {
              $Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'sitemap/edit-sitemap/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
    
              if($datarow->status==1){
                $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.CHANNEL_URL.'sitemap/sitemap-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
              }else{
                  $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.CHANNEL_URL.'sitemap/sitemap-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
              }
            }
    
            if(strpos(trim($this->viewData['submenuvisibility']['submenudelete'],','),$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE']) !== false){
              $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Sitemap","'.CHANNEL_URL.'sitemap/delete-mul-sitemap") >'.delete_text.'</a>';
            }
            $row[] = $Action;
    
            if(in_array($rollid, $delete)) {
              $row[] = '<div class="checkbox">
                          <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                          <label for="deletecheck'.$datarow->id.'"></label>
                        </div>';
            }else{
              $row[] = "";                
            }
    
            $data[] = $row;
        }
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->Sitemap->count_all(),
            "recordsFiltered" => $this->Sitemap->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
      }


    public function add_sitemap() {
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Add Sitemap";
        $this->viewData['module'] = "sitemap/Add_sitemap";
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
        $this->channel_headerlib->add_javascript("Sitemap","pages/add_sitemap.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function sitemap_add() {
        $PostData = $this->input->post();

        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $slug = isset($PostData['slug']) ? trim($PostData['slug']) : '';
        $priority = isset($PostData['priority']) ? trim($PostData['priority']) : '0';
        $changefrequency = isset($PostData['changefrequency']) ? trim($PostData['changefrequency']) : '0';
        
        $lastchange = ($PostData['lastchange']!="")?$this->general_model->convertdate($PostData['lastchange']):'';
        $status = $PostData['status'];
        $CheckDuplicateValue = $this->Sitemap->CheckDuplicateValue($slug,'',$CHANNELID,$MEMBERID);
        
        if ($CheckDuplicateValue != 0){
            
            $insertdata = array(
                "channelid"=>$CHANNELID,
                "memberid"=> $MEMBERID,
                "slug" => $slug,  
                "lastchange" => $lastchange,            
                "changefrequency" => $changefrequency,  
                "priority" => $priority,  
                "status" => $status,
                "createddate" => $createddate,
                "modifieddate" => $createddate,
                "usertype" => 1,
                "addedby" => $addedby,
                "modifiedby" => $addedby
            );
            
            $insertdata = array_map('trim', $insertdata);  
            $Add = $this->Sitemap->Add($insertdata);
            if ($Add) {
                echo 1;
            } else {
                echo 0;
            }
        }else{
            echo 2;
        }                            
    }

    public function edit_sitemap($sitemapid) {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Edit Sitemap";
        $this->viewData['module'] = "sitemap/Add_sitemap";
        $this->viewData['action'] = "1"; //Edit
  
        $this->Sitemap->_where = array('id' => $sitemapid);
        $this->viewData['sitemapdata'] = $this->Sitemap->getRecordsByID();
  
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");			
        $this->channel_headerlib->add_javascript("Sitemap","pages/add_sitemap.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
  
    public function sitemap_update() {
  
        $PostData = $this->input->post();
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url() . 'MEMBERID');
        
        $Sitemapid = $PostData['sitemapid'];
        $slug = isset($PostData['slug']) ? trim($PostData['slug']) : '';
        $priority = isset($PostData['priority']) ? trim($PostData['priority']) : '0';
        $changefrequency = isset($PostData['changefrequency']) ? trim($PostData['changefrequency']) : '0';
        $lastchange = ($PostData['lastchange']!="")?$this->general_model->convertdate($PostData['lastchange']):'';
        $status = $PostData['status'];
        
        $CheckDuplicateValue = $this->Sitemap->CheckDuplicateValue($slug,$Sitemapid,$CHANNELID,$MEMBERID);
        if ($CheckDuplicateValue != 0){
            
            $updatedata = array(
                "channelid"=>$CHANNELID,
                "memberid"=> $MEMBERID,
                "slug" => $slug,
                "priority" => $priority,
                "changefrequency" => $changefrequency,
                "lastchange" => $lastchange,                    
                "status" => $status,
                "modifieddate" => $modifieddate,
                "usertype" => 1,
                "modifiedby" => $modifiedby
            );
          
            $this->Sitemap->_where = array('id' => $Sitemapid);
            $Edit = $this->Sitemap->Edit($updatedata);
            if($Edit){
                echo 1;
            }else{
                echo 0;
            }
        }else{
            echo 2;
        }   
    }
    
    public function sitemap_enable_disable() {
      $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
      $PostData = $this->input->post();
  
      $modifieddate = $this->general_model->getCurrentDateTime();
      $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
      $this->Sitemap->_where = array("id" => $PostData['id']);
      $this->Sitemap->Edit($updatedata);
  
      /* if($this->viewData['submenuvisibility']['managelog'] == 1){
          $this->Sitemap->_where = array("id"=>$PostData['id']);
          $data = $this->Sitemap->getRecordsById();
          $msg = ($PostData['val']==0?"Disable":"Enable").' '.$data['slug'].' sitemap.';
          
          $this->general_model->addActionLog(2,'Sitemap', $msg);
      } */
      echo $PostData['id'];
    }
    public function delete_mul_sitemap(){
      $PostData = $this->input->post();
      $ids = explode(",",$PostData['ids']);
      $count = 0;
      $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
      foreach($ids as $row){
        
        /* if($this->viewData['submenuvisibility']['managelog'] == 1){
          $this->Sitemap->_where = array("id"=>$row);
          $data = $this->Sitemap->getRecordsById();
          
          $this->general_model->addActionLog(3,'Sitemap', "Delete ".$data['slug']." sitemap.");
        } */
        $this->Sitemap->Delete(array('id'=>$row));
      }
    }

}
