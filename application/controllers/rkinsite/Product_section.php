<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_section extends Admin_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getAdminSettings('submenu','Product_section');
		$this->load->model('Product_section_model','Product_section');
	}
	
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Product Section";
		$this->viewData['module'] = "product_section/Product_section";
		
		//Get Channel List
		$this->load->model("Channel_model","Channel"); 
		$this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Section','View product section.');
		}
		
		$this->admin_headerlib->add_javascript("product_section","pages/product_section.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Product_section->get_datatables();

		$this->load->model("Channel_model","Channel"); 
		$channeldata = $this->Channel->getChannelList('notdisplayvendorchannel');
		$data = array();
		$counter = $_POST['start'];
		foreach ($list as $Productsection) {
			$channellabel = '';

			$row = array();
			if($Productsection->channelid>0){
				$key = array_search($Productsection->channelid, array_column($channeldata, 'id'));
				if(!empty($channeldata) && isset($channeldata[$key])){
					$channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
				}
			}

			$row['DT_RowId'] = $Productsection->id;
			$row[] = ++$counter;
			$row[] = $channellabel.$Productsection->name;
			if($Productsection->displaytype==0){
				$row[] = 'Grid';
			}else{
				$row[] = 'Grid with slider';
			}
			$row[] = "<span class='pull-right'>".$Productsection->maxhomeproduct."</span>";
			//$row[] = "<span class='pull-right'>".$Productsection->inorder."</span>";
			$Action='';

			$Action .= '<a href="'.ADMIN_URL.'product-section/products/'. $Productsection->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';        

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
            	$Action .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'product-section/product-section-edit/'.$Productsection->id.'" title='.edit_title.'>'.edit_text.'</a>';
			}

			if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){                
                $Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Productsection->id.',"'.ADMIN_URL.'product-section/check-product-section-use","Productsection","'.ADMIN_URL.'product-section/delete-mul-product-section","productsectiontable") >'.delete_text.'</a>';
            }

            if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                if($Productsection->status==1){
                    $Action .= '<span id="span'.$Productsection->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Productsection->id.',\''.ADMIN_URL.'product-section/product-section-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
                }
                else{
                    $Action .='<span id="span'.$Productsection->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Productsection->id.',\''.ADMIN_URL.'product-section/product-section-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
                }
            }
			
			$row[] = $Action;
			$row[] =  '<span style="display: none;">'.$Productsection->priority.'</span><div class="checkbox">
                  <input id="deletecheck'.$Productsection->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Productsection->id.'" name="deletecheck'.$Productsection->id.'" class="checkradios">
                  <label for="deletecheck'.$Productsection->id.'"></label>
                </div>';
			$data[] = $row;
		}
		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->Product_section->count_all(),
						"recordsFiltered" => $this->Product_section->count_filtered(),
						"data" => $data,
				);
		echo json_encode($output);
	}
	
	public function product_section_add() {
		$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Product Section";
		$this->viewData['module'] = "product_section/Add_product_section";

		//Get Channel List
		$this->load->model("Channel_model","Channel"); 
		$this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');

		/* $this->load->model("Category_model","Category"); 
		$this->viewData['categorydata'] = $this->Product->getmaincategory();
		 */
		$this->admin_headerlib->add_javascript("add_product_section","pages/add_product_section.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}

	public function products($sectionid="")
	{
		if($sectionid==""){
			redirect(ADMIN_URL."dashboard");
		}
		$this->viewData['title'] = "Section Products";
		$this->viewData['sectionid'] = $sectionid;
        $this->viewData['module'] = "product_section/Section_products";
		$this->viewData['VIEW_STATUS'] = "1";
		
		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Section','View products of product section.');
		}

        $this->admin_headerlib->add_javascript("Section Products", "pages/section_products.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
	}

	public function productslisting() {   
		$this->load->model("Product_combination_model","Product_combination");
		$this->load->model("Product_model","Product");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
		$list = $this->Product->get_datatables();
		// echo $this->db->last_query();exit;
        $data = array();       
        $counter = $_POST['start'];

        foreach ($list as $datarow) {         
            $row = array();
            $actions = '';
			$checkbox = '';
			$varianthtml = '';
            $productname = '';

            // $actions .= '<a class="'.DOWNLOAD_CLASS.'" href="'.ADMIN_URL.'customer/downloadinvoice/'.$datarow->id.'" title="'.DOWNLOAD_TITLE.'" >'.DOWNLOAD_TEXT.'</a>';
			
            if(in_array($rollid, $delete)) {
                $actions.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->psid.',"'.ADMIN_URL.'product-section/check-section-product-use","Product","'.ADMIN_URL.'product-section/delete-mul-section-product","producttable") >'.delete_text.'</a>';

                $checkbox = ' <span style="display: none;">'.$datarow->psid.'</span><div class="checkbox"><input value="'.$datarow->psid.'" type="checkbox" class="checkradios" name="check'.$datarow->psid.'" id="check'.$datarow->psid.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->psid.'"></label></div>';

            }
			if($datarow->isuniversal==0 && $datarow->variantid!=''){
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->id,$datarow->variantid);

                if(!empty($variantdata)){
                    $varianthtml .= "<div class='row' style=''>";
                    foreach($variantdata as $variant){
                        $varianthtml .= "<div class='col-md-12 p-n'>";
                        $varianthtml .= "<div class='col-sm-3 popover-content-style'>".$variant['variantname']."</div>";
                        $varianthtml .= "<div class='col-sm-1 text-center popover-content-style'>:</div>";
                        $varianthtml .= "<div class='col-sm-7 popover-content-style'>".$variant['variantvalue']."</div>";
                        $varianthtml .= "</div>";
                    }
                    $varianthtml .= "</div>";
                }
                $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.$datarow->name.'</a>';
            }else{
                $productname = $datarow->name;
			}
			
			$row['DT_RowId'] = $datarow->psid;
			$row[] = ++$counter;
            $row[] = $productname;
            $row[] = $datarow->categoryname;
			if(number_format($datarow->minprice,2,'.','') == number_format($datarow->maxprice,2,'.','')){
				$price = number_format($datarow->minprice, 2, '.', ',');
			}else{
				$price = number_format($datarow->minprice, 2, '.', ',')." - ".number_format($datarow->maxprice, 2, '.', ',');
			}
			$row[] = "<span class='pull-right'>".$price."</span>";
            /* if($datarow->isuniversal==0){
            }else{
                $row[] = "<span class='pull-right'>".number_format($datarow->price,2,'.',',')."</span>";
            } */
            // $row[] = "<span class='pull-right'>".number_format($datarow->discount,2,'.','')."</span>";
            // $row[] = $datarow->priority;
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;

        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product->count_all(),
                        "recordsFiltered" => $this->Product->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

	public function product_section_edit($id) {
		$this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Product Section";
		$this->viewData['module'] = "product_section/Add_product_section";
		$this->viewData['action'] = "1";//Edit

		//Get Productsection data by id
		
		$this->Product_section->_where = array('id' => $id);
		$this->viewData['productsectiondata'] = $this->Product_section->getProductsectionDataByID($id);
		//print_r(($this->viewData['productsectiondata']));die;
		
		//Get Channel List
		$this->load->model("Channel_model","Channel"); 
		$this->viewData['channeldata'] = $this->Channel->getChannelList('notdisplayvendorchannel');
		
		$this->admin_headerlib->add_javascript("add_product_section","pages/add_product_section.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);

	}
	public function add_product_section(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'ADMINID');
		$channelidarr = isset($PostData['channelid'])?$PostData['channelid']:'';
		$inorder = $PostData['inorder'];
		$forwebsite = (isset($PostData['forwebsite']))?1:0;
		$forapp = (isset($PostData['forapp']))?1:0;
		
		if(!empty($channelidarr)){
			$insertdata=array();
			$this->load->model("Channel_model","Channel"); 
			foreach ($channelidarr as $channelid) {

				$this->Product_section->_where = "channelid=".$channelid." AND name='".trim($PostData['name'])."'";
				$Count = $this->Product_section->CountRecords();
               
        
				if($Count==0){

					$insertdata[] = array("channelid"=>$channelid,
							"name"=>$PostData['name'],
							"maxhomeproduct"=>$PostData['maxhomeproduct'],
							"status"=>$PostData['status'],
							"displaytype"=>$PostData['displaytype'],
							"inorder"=>$inorder,
							"forwebsite"=>$forwebsite,
							"forapp"=>$forapp,
							"type"=>0,
							"createddate"=>$createddate,
							"addedby"=>$addedby,
							"modifieddate"=>$createddate,
							"modifiedby"=>$addedby);
					
					if($this->viewData['submenuvisibility']['managelog'] == 1){
						$channeldata = $this->Channel->getChannelDataByID($channelid);
						$this->general_model->addActionLog(1,'Product Section','Add new '.$PostData['name'].' section for '.$channeldata['name'].'.');
					}
				}
			}
			if(!empty($insertdata)){
				$this->Product_section->Add_batch($insertdata);
			}
			
			echo 1;
		}else{
			$this->Product_section->_where = "name='".trim($PostData['name'])."'";
			$Count = $this->Product_section->CountRecords();

			if($Count==0){

				$insertdata = array("channelid"=>0,
									"name"=>$PostData['name'],
									"maxhomeproduct"=>$PostData['maxhomeproduct'],
									"status"=>$PostData['status'],
									"displaytype"=>$PostData['displaytype'],
									"inorder"=>$inorder,
									"forwebsite"=>$forwebsite,
							         "forapp"=>$forapp,
									"type"=>0,
									"createddate"=>$createddate,
									"addedby"=>$addedby,
									"modifieddate"=>$createddate,
									"modifiedby"=>$addedby);
		
				$insertdata=array_map('trim',$insertdata);

				$Add = $this->Product_section->Add($insertdata);
				if($Add){
					if($this->viewData['submenuvisibility']['managelog'] == 1){
						$this->general_model->addActionLog(1,'Product Section','Add new '.$PostData['name'].' section for all channel.');
					}
					echo 1;
				}else{
					echo 0;
				}
			}else{
				echo 2;
			}
		}
    }
    
    public function product_section_enable_disable() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

		
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'ADMINID'));
        $this->Product_section->_where = array("id" => $PostData['id']);
        $this->Product_section->Edit($updatedata);

		if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->Product_section->_where = array("id"=>$PostData['id']);
			$data = $this->Product_section->getRecordsById();
			$msg = ($PostData['val']==0?"Disable":"Enable")." ";
			if($data['channelid']==0){
				$msg .= $data['name'].' section for all channel.';
			}else{
				$channeldata = $this->Channel->getChannelDataByID($data['channelid']);
				$msg .= $data['name'].' section for '.$channeldata['name'].'.';
			}
            
            $this->general_model->addActionLog(2,'Product Section', $msg);
        }
        echo $PostData['id'];
    }

	public function update_product_section(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'ADMINID');

		$channelid = $PostData['channelid'];
		$inorder = $PostData['inorder'];
		$forwebsite = (isset($PostData['forwebsite']))?1:0;
		$forapp = (isset($PostData['forapp']))?1:0;
        
       
		$this->Product_section->_where = "id!=".$PostData['productsectionid']." AND channelid=".$channelid." AND name='".trim($PostData['name'])."'";
		$Count = $this->Product_section->CountRecords();

		if($Count==0){

			$updatedata = array("channelid"=>$channelid,
								"name"=>$PostData['name'],
								"maxhomeproduct"=>$PostData['maxhomeproduct'],
								"inorder"=>$inorder,
								"forwebsite"=>$forwebsite,
							     "forapp"=>$forapp,
								"status"=>$PostData['status'],
								"displaytype"=>$PostData['displaytype'],
                                "modifieddate"=>$modifieddate,
                                "modifiedby"=>$modifiedby);

			$updatedata=array_map('trim',$updatedata);

			$this->Product_section->_where = array("id"=>$PostData['productsectionid']);
			$Edit = $this->Product_section->Edit($updatedata);

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				if($channelid==0){
					$msg = 'Edit '.$PostData['name'].' section for all channel.';
				}else{
					$channeldata = $this->Channel->getChannelDataByID($channelid);
					$msg = 'Edit '.$PostData['name'].' section for '.$channeldata['name'].'.';
				}
				$this->general_model->addActionLog(2,'Product Section',$msg);
			}
			echo 1;
		}else{
			echo 2;
		}
	}

	public function check_product_section_use()
    {
       $PostData = $this->input->post();
         $count = 0;
	  	 $ids = explode(",",$PostData['ids']);
	     foreach($ids as $row){
	        $this->readdb->select('productsectionid');
	        $this->readdb->from(tbl_productsectionmapping);
	        $where = array("productsectionid"=>$row);
	        $this->readdb->where($where);
	        $query = $this->readdb->get();
	        if($query->num_rows() > 0){
	          $count++;
	        }
	      }
      echo $count;
    }

    public function delete_mul_product_section(){
	    $PostData = $this->input->post();
	    $ids = explode(",",$PostData['ids']);

	    foreach($ids as $row){

			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->Product_section->_where = array("id"=>$row);
				$data = $this->Product_section->getRecordsById();
				if($data['channelid']==0){
					$msg = 'Delete '.$data['name'].' section for all channel.';
				}else{
					$channeldata = $this->Channel->getChannelDataByID($data['channelid']);
					$msg = 'Delete '.$data['name'].' section for '.$channeldata['name'].'.';
				}
				$this->general_model->addActionLog(3,'Product Section',$msg);
			}
			$this->Product_section->Delete(array('id'=>$row));
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
            $this->Product_section->edit_batch($updatedata, 'id');
        }

        echo 1;
	}

	public function update_product_priority(){

		$PostData = $this->input->post();
		// print_r($PostData);exit;
        $sequenceno = $PostData['sequencearray'];
        $updatedata = array();

        for($i = 0; $i < count($sequenceno); $i++){
            $updatedata[] = array(
                'productpriority'=>$sequenceno[$i]['sequenceno'],
                'id' => $sequenceno[$i]['id']
            );
        }
		// print_r($updatedata);
        if(!empty($updatedata)){
			$this->Product_section->_table = tbl_productsectionmapping;
            $this->Product_section->edit_batch($updatedata, 'id');
        }

        echo 1;
	}

	public function check_section_product_use() {
	   $PostData = $this->input->post();
	   $count = 0;
	   $ids = explode(",",$PostData['ids']);
	   echo $count;
  	}
	  
  	public function delete_mul_section_product() {
		$this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
		$PostData = $this->input->post();
		$ids = explode(",", $PostData['ids']);
		
		
		foreach ($ids as $row) {
			
			if($this->viewData['submenuvisibility']['managelog'] == 1){
				$this->Product_section->_table = tbl_productsection;
				$data = $this->Product_section->getProductSectionsProductById($row);
				$msg = 'Delete '.$data['productname'].' product from '.$data['name'].' section.';
				
				$this->general_model->addActionLog(3,'Product Section',$msg);
			}
			$this->Product_section->_table = tbl_productsectionmapping;
			$this->Product_section->Delete(array("id"=>$row));
		}
	}
}