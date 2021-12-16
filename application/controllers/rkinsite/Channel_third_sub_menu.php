<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channel_third_sub_menu extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Channel_third_sub_menu');
		$this->load->model('Channel_third_sub_menu_model','Channel_third_sub_menu');
	}
	
	public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Channel Third Sub Menu";
		$this->viewData['module'] = "channel_third_sub_menu/Channel_third_sub_menu";

		if($this->viewData['submenuvisibility']['managelog'] == 1){
			$this->general_model->addActionLog(4,'Channel Sub Menu','View channel sub menu.');
		}

		$this->admin_headerlib->add_javascript("channel_third_sub_menu","pages/channel_third_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Channel_third_sub_menu->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
			$row = array();
			$mainmenuname = '';
        	$actions = '';
            $checkbox = '';

			/* foreach($this->viewData['mainnavdata'] as $row1){
				if($row1['id'] == $datarow->channelsubmenuid){
					$mainmenuname = $row1['name'];
				}
			} */

            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'channel-third-sub-menu/channel-third-sub-menu-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Channel-third-sub-menu","'.ADMIN_URL.'channel-third-sub-menu/delete-mul-channel-third-sub-menu","channelsubmenutable") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }

			$row[] = ++$counter;
			$row[] = $datarow->submenuname;
            $row[] = $datarow->name;
            $row[] = $datarow->url;
            $row[] = "<span class='pull-right'>".$datarow->inorder."</span>";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Channel_third_sub_menu->count_all(),
                        "recordsFiltered" => $this->Channel_third_sub_menu->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}

	public function add_channel_third_sub_menu() {
		
		$this->viewData['title'] = "Add Channel Third Sub Menu";
		$this->viewData['module'] = "channel_third_sub_menu/Add_channel_third_sub_menu";

		$this->load->model('Channel_sub_menu_model','Channel_sub_menu');
		$this->Channel_sub_menu->_order = "inorder ASC";
		$this->viewData['submenudata'] = $this->Channel_sub_menu->getSubMenuForThirdLevelSubMenu();
		
		$this->admin_headerlib->add_javascript("channel_third_sub_menu","pages/add_channel_third_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function channel_third_sub_menu_add(){
		
		$PostData = $this->input->post();
		
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'].',';

		$submenuid = $PostData['submenuid'];
		$name = $PostData['SubmenuName'];
		$menuurl = $PostData['menuurl'];
		$inorder = $PostData['inorder'];
		
		$this->Channel_third_sub_menu->_where = "channelsubmenuid=".$submenuid." AND name='".trim($name)."'";
		$Count = $this->Channel_third_sub_menu->CountRecords();

		if($Count==0){

			$showinrole = (isset($PostData['showinrole']))?1:0;

			$insertdata = array("channelsubmenuid"=>$submenuid,
								"name"=>$name,
								"url"=>$menuurl,
								"submenuvisible"=>$profileid,
								"submenuadd"=>$profileid,
								"submenuedit"=>$profileid,
								"submenudelete"=>$profileid,
								"showinrole"=>$showinrole,
								"inorder"=>$inorder);

			$insertdata=array_map('trim',$insertdata);

	        $Add = $this->Channel_third_sub_menu->Add($insertdata);

			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Channel Third Sub Menu','Add new '.$name.' channel third sub menu.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function channel_third_sub_menu_edit($id) {
		
		$this->viewData['title'] = "Edit Channel Third Sub Menu";
		$this->viewData['module'] = "channel_third_sub_menu/Add_channel_third_sub_menu";
		$this->viewData['action'] = "1";//Edit

		$this->Channel_third_sub_menu->_where = 'id='.$id;
		$this->viewData['channelthirdsubmenurow'] = $this->Channel_third_sub_menu->getRecordsByID();

		$this->load->model('Channel_sub_menu_model','Channel_sub_menu');
		$this->Channel_sub_menu->_order = "inorder ASC";
		$this->viewData['submenudata'] = $this->Channel_sub_menu->getSubMenuForThirdLevelSubMenu();
        
        $this->admin_headerlib->add_javascript("add_channel_third_sub_menu","pages/add_channel_third_sub_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function update_channel_third_sub_menu(){
		$PostData = $this->input->post();
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$channelthirdsubmenuid = $PostData['channelthirdsubmenuid'];
		$submenuid = $PostData['submenuid'];
		$name = $PostData['SubmenuName'];
		$menuurl = $PostData['menuurl'];
		$inorder = $PostData['inorder'];
	
		$this->Channel_third_sub_menu->_where = "id!=".$channelthirdsubmenuid." AND channelsubmenuid=".$submenuid." AND name='".trim($name)."'";
		$Count = $this->Channel_third_sub_menu->CountRecords();

		if($Count==0){
			$showinrole = (isset($PostData['showinrole']))?1:0;

			$updatedata = array("channelthirdsubmenuid"=>$submenuid,
								"name"=>$name,
								"url"=>$menuurl,
								"showinrole"=>$showinrole,
								"inorder"=>$inorder
								);

			$updatedata=array_map('trim',$updatedata);

			$this->Channel_third_sub_menu->_where = array("id"=>$channelthirdsubmenuid);
			$this->Channel_third_sub_menu->Edit($updatedata);
			
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Channel Third Sub Menu','Edit '.$name.' channel third sub menu.');
			}
			echo 1;
		}else{
			echo 2;
		}
	}
	public function delete_mul_channel_third_sub_menu(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        
        foreach($ids as $row){

			if($this->viewData['submenuvisibility']['managelog'] == 1){

				$this->Channel_third_sub_menu->_where = array("id"=>$row);
				$data = $this->Channel_third_sub_menu->getRecordsById();
				$this->general_model->addActionLog(3,'Channel Third Sub Menu','Delete '.$data['name'].' channel third sub menu.');
			}
            $this->Channel_third_sub_menu->Delete(array('id'=>$row));
        }
    }
}