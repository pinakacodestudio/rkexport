<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Channel_main_menu extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Channel_main_menu');
		$this->load->model('Channel_main_menu_model','Channel_main_menu');
	}
	
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Channel Main Menu";
		$this->viewData['module'] = "channel_main_menu/Channel_main_menu";
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Channel Main Menu','View channel main menu.');
        }
		$this->admin_headerlib->add_javascript("channel_main_menu","pages/channel_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
	}
	
	public function listing() {
		$edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Channel_main_menu->get_datatables();
        
        $data = array();       
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        foreach ($list as $datarow) { 
        	$row = array();
        	$actions = '';
            $checkbox = '';

            if(in_array($rollid, $edit)) {
                $actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'channel-main-menu/channel-main-menu-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
            }

            if(in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"'.ADMIN_URL.'channel-main-menu/check-channel-main-menu-use","Mainmenu","'.ADMIN_URL.'channel-main-menu/delete-mul-channel-main-menu","channelmainmenutable") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label></div>';
            }

            $row[] = ++$counter;
            $row[] = $datarow->name;
            $row[] = $datarow->icon;
            $row[] = "<span class='pull-right'>".$datarow->inorder."</span>";
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Channel_main_menu->count_all(),
                        "recordsFiltered" => $this->Channel_main_menu->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);  
	}

	public function channel_main_menu_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Channel Main Menu";
		$this->viewData['module'] = "channel_main_menu/Add_channel_main_menu";

		$this->admin_headerlib->add_javascript("add_channel_main_menu","pages/add_channel_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_channel_main_menu(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		$profileid = $this->session->userdata[base_url().'ADMINUSERTYPE'].',';
		
		$name = $PostData['MainmenuName'];
		$menuurl = $PostData['menuurl'];
		$menuicon = $PostData['menuicon'];
		$inorder = $PostData['inorder'];
		
		$this->Channel_main_menu->_where = "name='".trim($name)."'";
		$Count = $this->Channel_main_menu->CountRecords();

		if($Count==0){

			$showinrole = (isset($PostData['showinrole']))?1:0;

			$insertdata = array("name"=>$name,
								"menuurl"=>$menuurl,
								"icon"=>$menuicon,
								'menuvisible'=>$profileid,
								'menuadd'=>$profileid,
								'menuedit'=>$profileid,
								'menudelete'=>$profileid,
								"showinrole"=>$showinrole
								);

			$insertdata=array_map('trim',$insertdata);
			if($inorder!=''){
	            $insertdata['inorder'] = $inorder;
	        }else{
	        	$this->writedb->set('inorder',"(SELECT IFNULL(max(mm.inorder),0)+1 as inorder FROM ".tbl_channelmainmenu." as mm)",FALSE);
	        }
	        $this->writedb->set($insertdata);
	        $this->writedb->insert(tbl_channelmainmenu);

	        $Add = $this->writedb->insert_id();

			if($Add){
				if($this->viewData['submenuvisibility']['managelog'] == 1){
					$this->general_model->addActionLog(1,'Channel Main Menu','Add new '.$name.' channel main menu.');
				}
				echo 1;
			}else{
				echo 0;
			}
		}else{
			echo 2;
		}
	}
	public function channel_main_menu_edit($id) {
		$this->checkAdminAccessModule('submenu', 'edit', $this->viewData['submenuvisibility']);

		$this->viewData['title'] = "Edit Channel Main Menu";
		$this->viewData['module'] = "channel_main_menu/Add_channel_main_menu";
		$this->viewData['action'] = "1";//Edit

		//Get Mainmenu data by id
		$this->Channel_main_menu->_where = 'id='.$id;
		$this->viewData['mainmenurow'] = $this->Channel_main_menu->getRecordsByID();
		
		$this->admin_headerlib->add_javascript("channel_main_menu","pages/add_channel_main_menu.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function update_channel_main_menu(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');
		
		$mainmenuid = $PostData['mainmenuid'];
		$name = $PostData['MainmenuName'];
		$menuurl = $PostData['menuurl'];
		$menuicon = $PostData['menuicon'];
		$inorder = $PostData['inorder'];
		
		$this->Channel_main_menu->_where = "id!=".$mainmenuid." AND name='".trim($name)."'";
		$Count = $this->Channel_main_menu->CountRecords();

		if($Count==0){

			$showinrole = (isset($PostData['showinrole']))?1:0;

			$updatedata = array("name"=>$name,
								"menuurl"=>$menuurl,
								"icon"=>$menuicon,
								"showinrole"=>$showinrole,
								"inorder"=>$inorder);

			$updatedata=array_map('trim',$updatedata);

			$this->Channel_main_menu->_where = array("id"=>$mainmenuid);
			$this->Channel_main_menu->Edit($updatedata);
			
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->general_model->addActionLog(2,'Channel Main Menu','Edit '.$name.' channel main menu.');
			}
			echo 1;
		}else{
			echo 2;
		}
	}
	public function check_channel_main_menu_use(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT id FROM ".tbl_channelmainmenu." WHERE 
					                    id IN (SELECT channelmainmenuid FROM ".tbl_channelsubmenu." WHERE channelmainmenuid = $row)
					                    ");

            if($query->num_rows() > 0){
                $count++;
            }
        }
        echo $count;
    }
    public function delete_mul_channel_main_menu(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            $query = $this->readdb->query("SELECT id FROM ".tbl_channelmainmenu." WHERE 
					                    id IN (SELECT channelmainmenuid FROM ".tbl_channelsubmenu." WHERE channelmainmenuid = $row)
					                    ");

            if($query->num_rows() == 0){

				if($this->viewData['submenuvisibility']['managelog'] == 1){

					$this->Channel_main_menu->_where = array("id"=>$row);
					$data = $this->Channel_main_menu->getRecordsById();
					$this->general_model->addActionLog(3,'Channel Main Menu','Delete '.$data['name'].' channel main menu.');
				}
            	$this->Channel_main_menu->Delete(array('id'=>$row));
            }
            	
        }
    }
}