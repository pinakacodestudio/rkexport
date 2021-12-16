<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channel_sub_menu extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Channel_sub_menu');
		$this->load->model('Channel_sub_menu_model','Channel_sub_menu');
	}
	
	public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Channel Sub Menu";
		$this->viewData['module'] = "channel_sub_menu/Channel_sub_menu";

		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Channel Sub Menu','View channel sub menu.');
		}

		$this->admin_headerlib->add_javascript("channel_sub_menu","pages/channel_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Channel_sub_menu->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
			$row = array();
			$mainmenuname = '';
        	$actions = '';
            $checkbox = '';

			/* foreach($this->viewData['mainnavdata'] as $row1){
				if($row1['id'] == $datarow->channelmainmenuid){
					$mainmenuname = $row1['name'];
				}
			} */

            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'channel-sub-menu/channel-sub-menu-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'channel-sub-menu/check-channel-sub-menu-use","Channel-sub-menu","'.ADMIN_URL.'channel-sub-menu/delete-mul-channel-sub-menu","channelsubmenutable") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }

			$row[] = ++$counter;
			$row[] = $datarow->mainmenuname;
            $row[] = $datarow->name;
            $row[] = $datarow->url;
            $row[] = "<span class='pull-right'>".$datarow->inorder."</span>";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Channel_sub_menu->count_all(),
                        "recordsFiltered" => $this->Channel_sub_menu->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}

	public function channel_sub_menu_add() {
		
		$this->viewData['title'] = "Add Channel Sub Menu";
		$this->viewData['module'] = "channel_sub_menu/Add_channel_sub_menu";

		$this->load->model('Channel_main_menu_model','Channel_main_menu');
		$this->Channel_main_menu->_order = "inorder ASC";
		$this->viewData['mainmenudata'] = $this->Channel_main_menu->getRecordByID();
		
		$this->admin_headerlib->add_javascript("channel_sub_menu","pages/add_channel_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_channel_sub_menu(){
		
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'].',';

		$mainmenuid = $PostData['mainmenuid'];
		$name = $PostData['SubmenuName'];
		$menuurl = $PostData['menuurl'];
		$inorder = $PostData['inorder'];
		
		$this->Channel_sub_menu->_where = "channelmainmenuid=".$mainmenuid." AND name='".trim($name)."'";
		$Count = $this->Channel_sub_menu->CountRecords();

		if($Count==0){

			$showinrole = (isset($PostData['showinrole']))?1:0;

			$insertdata = array("channelmainmenuid"=>$mainmenuid,
								"name"=>$name,
								"url"=>$menuurl,
								"submenuvisible"=>$profileid,
								"submenuadd"=>$profileid,
								"submenuedit"=>$profileid,
								"submenudelete"=>$profileid,
								"showinrole"=>$showinrole,
								"inorder"=>$inorder);

			$insertdata=array_map('trim',$insertdata);

	        $Add = $this->Channel_sub_menu->Add($insertdata);

			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Channel Sub Menu','Add new '.$name.' channel sub menu.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function channel_sub_menu_edit($id) {
		
		$this->viewData['title'] = "Edit Channel Sub Menu";
		$this->viewData['module'] = "channel_sub_menu/Add_channel_sub_menu";
		$this->viewData['action'] = "1";//Edit

		//Get Submenu data by id
		$this->Channel_sub_menu->_where = 'id='.$id;
		$this->viewData['channelsubmenurow'] = $this->Channel_sub_menu->getRecordsByID();

		$this->load->model('Channel_main_menu_model','Channel_main_menu');
		$this->Channel_main_menu->_order = "inorder ASC";
		$this->viewData['mainmenudata'] = $this->Channel_main_menu->getRecordByID();
        
        $this->admin_headerlib->add_javascript("add_channel_sub_menu","pages/add_channel_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function update_channel_sub_menu(){
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$channelsubmenuid = $PostData['channelsubmenuid'];
		$mainmenuid = $PostData['mainmenuid'];
		$name = $PostData['SubmenuName'];
		$menuurl = $PostData['menuurl'];
		$inorder = $PostData['inorder'];
	
		$this->Channel_sub_menu->_where = "id!=".$channelsubmenuid." AND channelmainmenuid=".$mainmenuid." AND name='".trim($name)."'";
		$Count = $this->Channel_sub_menu->CountRecords();

		if($Count==0){
			$showinrole = (isset($PostData['showinrole']))?1:0;

			$updatedata = array("channelmainmenuid"=>$mainmenuid,
								"name"=>$name,
								"url"=>$menuurl,
								"showinrole"=>$showinrole,
								"inorder"=>$inorder
								);

			$updatedata=array_map('trim',$updatedata);

			$this->Channel_sub_menu->_where = array("id"=>$channelsubmenuid);
			$this->Channel_sub_menu->Edit($updatedata);
			
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Channel Sub Menu','Edit '.$name.' channel sub menu.');
			}
			echo 1;
		}else{
			echo 2;
		}
	}
	public function check_channel_sub_menu_use(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT id FROM ".tbl_channelsubmenu." WHERE 
					                    id IN (SELECT channelsubmenuid FROM ".tbl_channelthirdlevelsubmenu." WHERE channelsubmenuid = $row)
					                    ");

            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
	public function delete_mul_channel_sub_menu(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        
        foreach($ids as $row){
			$query = $this->readdb->query("SELECT id FROM ".tbl_channelsubmenu." WHERE 
						id IN (SELECT channelsubmenuid FROM ".tbl_channelthirdlevelsubmenu." WHERE channelsubmenuid = $row)
						");
			if($query->num_rows() == 0){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					
					$this->Channel_sub_menu->_where = array("id"=>$row);
					$data = $this->Channel_sub_menu->getRecordsById();
					$this->general_model->addActionLog(3,'Channel Sub Menu','Delete '.$data['name'].' channel sub menu.');
				}
				$this->Channel_sub_menu->Delete(array('id'=>$row));
			}
        }
    }
}