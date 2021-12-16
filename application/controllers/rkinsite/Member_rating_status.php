<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member_rating_status extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Member_rating_status');
		$this->load->model('Member_rating_status_model','Member_rating_status');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = Member_label." Rating Status";
		$this->viewData['module'] = "member_rating_status/Member_rating_status";
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Rating Status','View rating status.');
		}
		
		$this->admin_headerlib->add_javascript("member_rating_status","pages/member_rating_status.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Member_rating_status->get_datatables();
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $datarow) {
			$row = array();
            
            
            $row[] = ++$counter;
			$row[] = $datarow->name;
            
            $row[] = '<div style="background: '.$datarow->color.';" class="statusescolor"></div>';
			$Action='';

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'member-rating-status/member-rating-status-edit/'.$datarow->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Member-rating-status","'.ADMIN_URL.'member-rating-status/delete-mul-member-rating-status","memberratingstatustable") >'.delete_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($datarow->status==1){
                    $Action .= '<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$datarow->id.',\''.ADMIN_URL.'member-rating-status/member-rating-status-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$datarow->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$datarow->id.',\''.ADMIN_URL.'member-rating-status/member-rating-status-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
			
			$row[] = $Action;
			$row[] =  '<div class="checkbox">
                  <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                  <label for="deletecheck'.$datarow->id.'"></label>
                </div>';
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Member_rating_status->count_all(),
						"recordsFiltered" => $this->Member_rating_status->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function member_rating_status_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add ".Member_label." Rating Status";
		$this->viewData['module'] = "member_rating_status/Add_member_rating_status";

		$this->admin_headerlib->add_javascript("add_member_rating_status","pages/add_member_rating_status.js");
		$this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
		$this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}

	public function member_rating_status_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit ".Member_label." Rating Status";
		$this->viewData['module'] = "member_rating_status/Add_member_rating_status";
		$this->viewData['action'] = "1";//Edit

		//Get Channel data by id
		$this->viewData['ratingstatusdata'] = $this->Member_rating_status->getRatingstatusDataByID($id);
		
		$this->admin_headerlib->add_javascript_plugins("minicolorjs","minicolor/jquery.minicolors.min.js");
		$this->admin_headerlib->add_plugin("minicolorcss","minicolor/jquery.minicolors.css");
		$this->admin_headerlib->add_javascript("add_member_rating_status","pages/add_member_rating_status.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_member_rating_status(){
		$PostData = $this->input->post();
        //print_r($PostData);exit;
        $name = $PostData['name'];
        $color = $PostData['color'];
        $status = $PostData['status'];

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        

		$this->Member_rating_status->_where = "name='".trim($name)."'";
		$Count = $this->Member_rating_status->CountRecords();

		if($Count==0){

			$insertdata = array("name"=>$name,
                                "color"=>$color,
                                "status"=>$status,
                                "createddate"=>$createddate,
                                "addedby"=>$addedby,
                                "modifieddate"=>$createddate,
                                "modifiedby"=>$addedby);
    
			$insertdata=array_map('trim',$insertdata);

			$Add = $this->Member_rating_status->Add($insertdata);
			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Rating Status','Add new '.$name.' rating status.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
    }
    
    public function member_rating_status_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Member_rating_status->_where = array("id" => $PostData['id']);
        $this->Member_rating_status->Edit($updatedata);

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Member_rating_status->_where = array("id"=>$PostData['id']);
            $data = $this->Member_rating_status->getRecordsById();
            $msg = ($PostData['val']==0?"Disable":"Enable")." ".$data['name'].' rating status.';
            
            $this->general_model->addActionLog(2,'Rating Status', $msg);
        }
        echo $PostData['id'];
    }

	public function update_member_rating_status(){
		$PostData = $this->input->post();
        //print_r($PostData);exit;
        $memberratingstatusid = $PostData['memberratingstatusid'];
        $name = $PostData['name'];
        $color = $PostData['color'];
        $status = $PostData['status'];
        $modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$this->Member_rating_status->_where = "id!=".$memberratingstatusid." AND name='".trim($name)."'";
		$Count = $this->Member_rating_status->CountRecords();

		if($Count==0){

			$updatedata = array("name"=>$name,
								"color"=>$color,
                                "status"=>$status,
                                "modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Member_rating_status->_where = array("id"=>$memberratingstatusid);
			$Edit = $this->Member_rating_status->Edit($updatedata);

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Rating Status','Edit '.$name.' rating status.');
			}
			echo 1;
		}else{
			echo 2;
		}
	}

	public function delete_mul_member_rating_status(){
	    $PostData = $this->input->post();
	    $ids = explode(",",$PostData['ids']);

	    $count = 0;
	    $ADMINID = $this->session->userdata(base_url().'ADMINID');
	    foreach($ids as $row)
	    {
			if($this->viewData['submenuvisibility']['managelog'] == 1){

				$this->Member_rating_status->_where = array("id"=>$row);
				$data = $this->Member_rating_status->getRecordsById();
			
				$this->general_model->addActionLog(3,'Rating Status','Delete '.$data['name'].' rating status.');
			}
	        $this->Member_rating_status->Delete(array('id'=>$row));
	    }
	}

	public function update_priority(){

		$PostData = $this->input->post();
		// print_r($PostData);exit;
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();

        for($i = 0; $i < count($sequenceno); $i++){
            $updatedata[] = array(
                'priority'=>$sequenceno[$i]['sequenceno'],
                'id' => $sequenceno[$i]['id']
            );
        }
		// print_r($updatedata);exit;
        if(!empty($updatedata)){
            $this->Member_rating_status->edit_batch($updatedata, 'id');
        }

        echo 1;
	}

}