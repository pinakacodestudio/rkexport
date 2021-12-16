<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_section extends Channel_Controller {

	public $viewData = array();
	function __construct(){
		parent::__construct();
		$this->viewData = $this->getChannelSettings('submenu','Product_section');
		$this->load->model('Product_section_model','Product_section');
	}
	public function index() {
		$this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Product Section";
		$this->viewData['module'] = "product_section/Product_section";
		
		//Get Channel List
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'');
		
		$this->channel_headerlib->add_javascript("product_section","pages/product_section.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);
		
	}
	public function listing() {
		
		$list = $this->Product_section->get_datatables();

		$this->load->model("Channel_model","Channel"); 
		$channeldata = $this->Channel->getChannelList();
		$data = array();
		$counter = $_POST['start'];
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');

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

			$Action .= '<a href="'.CHANNEL_URL.'product-section/products/'. $Productsection->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';        

			if($MEMBERID == $Productsection->addedby){

				if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
					$Action .= '<a class="'.edit_class.'" href="'.CHANNEL_URL.'product-section/product-section-edit/'.$Productsection->id.'" title='.edit_title.'>'.edit_text.'</a>';
				}

				if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){                
					$Action.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$Productsection->id.',"'.CHANNEL_URL.'product-section/check-product-section-use","Productsection","'.CHANNEL_URL.'product-section/delete-mul-product-section") >'.delete_text.'</a>';
				}

				if(strpos($this->viewData['submenuvisibility']['submenuedit'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
					if($Productsection->status==1){
						$Action .= '<span id="span'.$Productsection->id.'"><a href="javascript:void(0)" onclick="enabledisable(0,'.$Productsection->id.',\''.CHANNEL_URL.'product-section/product-section-enable-disable\',\''.disable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.disable_class.'" title="'.disable_title.'">'.stripslashes(disable_text).'</a></span>';
					}
					else{
						$Action .='<span id="span'.$Productsection->id.'"><a href="javascript:void(0)" onclick="enabledisable(1,'.$Productsection->id.',\''.CHANNEL_URL.'product-section/product-section-enable-disable\',\''.enable_title.'\',\''.disable_class.'\',\''.enable_class.'\',\''.disable_title.'\',\''.enable_title.'\',\''.disable_text.'\',\''.enable_text.'\')" class="'.enable_class.'" title="'.enable_title.'">'.stripslashes(enable_text).'</a></span>';
					}
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
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		$this->load->model("Channel_model","Channel"); 
		$this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,"homebanner");
		
		$this->channel_headerlib->add_javascript("add_product_section","pages/add_product_section.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);

	}

	public function products($sectionid="")
	{
		if($sectionid==""){
			redirect(CHANNEL_URL."dashboard");
		}
		$this->viewData['title'] = "Section Products";
		$this->viewData['sectionid'] = $sectionid;
        $this->viewData['module'] = "product_section/Section_products";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->channel_headerlib->add_javascript("Section Products", "pages/section_products.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
	}

	public function productslisting() {  
		$this->load->model("Product_combination_model","Product_combination"); 
		$this->load->model("Product_model","Product");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
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
            
            // $actions .= '<a class="'.DOWNLOAD_CLASS.'" href="'.CHANNEL_URL.'customer/downloadinvoice/'.$datarow->id.'" title="'.DOWNLOAD_TITLE.'" >'.DOWNLOAD_TEXT.'</a>';
			
            if(in_array($rollid, $delete)) {
                $actions.=' <a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->psid.',"'.CHANNEL_URL.'product-section/check-section-product-use","Product","'.CHANNEL_URL.'product-section/delete-mul-section-product") >'.delete_text.'</a>';

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
            if($datarow->isuniversal==0){
				$row[] = "<span class='pull-right'>".$price."</span>";
	        }else{
                $row[] = "<span class='pull-right'>".$price."</span>";
            }
            // $row[] = "<span class='pull-right'>".number_format($datarow->discount,2,'.','')."</span>";
            // $row[] = $datarow->priority;
            // $row[] = $actions;
            // $row[] = $checkbox;
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
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');

		//Get Productsection data by id
		$productsectiondata = $this->Product_section->getProductsectionDataByID($id);
		
		if($productsectiondata['addedby'] != $MEMBERID){
			redirect('Pagenotfound');
		}
		$this->viewData['productsectiondata'] = $productsectiondata;

		//Get Channel List
		$this->load->model("Channel_model","Channel"); 
		$this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,"homebanner");
		
		$this->channel_headerlib->add_javascript("add_product_section","pages/add_product_section.js");
		$this->load->view(CHANNELFOLDER.'template',$this->viewData);

	}
	public function add_product_section(){
		$PostData = $this->input->post();
		
		$createddate = $this->general_model->getCurrentDateTime();
		$addedby = $this->session->userdata(base_url().'MEMBERID');
		$channelidarr = isset($PostData['channelid'])?$PostData['channelid']:'';
		$inorder = $PostData['inorder'];
		$forapp = $PostData['type']==0?1:0;
		$forwebsite = $PostData['type']==1?1:0;
		
		if(!empty($channelidarr) && $forapp == 1){
			$insertdata=array();
			foreach ($channelidarr as $channelid) {

				$this->Product_section->_where = "channelid=".$channelid." AND name='".trim($PostData['name'])."' AND addedby=".$addedby;
				$Count = $this->Product_section->CountRecords();

				if($Count==0){

					$insertdata[] = array("channelid"=>$channelid,
							"name"=>$PostData['name'],
							"maxhomeproduct"=>$PostData['maxhomeproduct'],
							"inorder"=>$inorder,
							"forwebsite"=>$forwebsite,
							"forapp"=>$forapp,
							"type"=>1,
							"status"=>$PostData['status'],
							"displaytype"=>$PostData['displaytype'],
							"createddate"=>$createddate,
							"addedby"=>$addedby,
							"modifieddate"=>$createddate,
							"modifiedby"=>$addedby);
				}
			}
			
			if(!empty($insertdata)){
				$this->Product_section->Add_batch($insertdata);
			}
			echo 1;
		}else{
			$this->Product_section->_where = "name='".trim($PostData['name'])."' AND addedby=".$addedby;
			$Count = $this->Product_section->CountRecords();

			if($Count==0){
				$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
				$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
				if($forwebsite==1){
					$channelid = $CHANNELID;
					$memberid = $MEMBERID;
				}else{
					$channelid = $memberid = 0;
				}
				$insertdata = array("channelid"=>$channelid,
									"memberid"=>$memberid,
									"name"=>$PostData['name'],
									"maxhomeproduct"=>$PostData['maxhomeproduct'],
									"inorder"=>$inorder,
									"status"=>$PostData['status'],
									"displaytype"=>$PostData['displaytype'],
									"forwebsite"=>$forwebsite,
									"forapp"=>$forapp,
									"type"=>1,
									"createddate"=>$createddate,
									"addedby"=>$addedby,
									"modifieddate"=>$createddate,
									"modifiedby"=>$addedby);
		
				$insertdata=array_map('trim',$insertdata);
			
				$Add = $this->Product_section->Add($insertdata);
				if($Add){
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
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate, "modifiedby" => $this->session->userdata(base_url() . 'MEMBERID'));
        $this->Product_section->_where = array("id" => $PostData['id']);
        $this->Product_section->Edit($updatedata);

        echo $PostData['id'];
    }

	public function update_product_section(){
		$PostData = $this->input->post();
		
		$modifieddate = $this->general_model->getCurrentDateTime();
		$modifiedby = $this->session->userdata(base_url().'MEMBERID');

		$channelid = isset($PostData['channelid'])?$PostData['channelid']:0;
		$inorder = $PostData['inorder'];
		$forapp = $PostData['type']==0?1:0;
		$forwebsite = $PostData['type']==1?1:0;

		$CHANNELID = $this->session->userdata(base_url().'CHANNELID');
		$MEMBERID = $this->session->userdata(base_url().'MEMBERID');
		if($forwebsite==1){
			$channelid = $CHANNELID;
			$memberid = $MEMBERID;
		}else{
			$memberid = 0;
		}
		$this->Product_section->_where = "id!=".$PostData['productsectionid']." AND channelid=".$channelid." AND name='".trim($PostData['name'])."' AND addedby=".$modifiedby;
		$Count = $this->Product_section->CountRecords();

		if($Count==0){

			$updatedata = array("channelid"=>$channelid,
								"memberid"=>$memberid,
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
		
		$this->Product_section->_table = tbl_productsectionmapping;
		foreach ($ids as $row) {
			$this->Product_section->Delete(array('id'=>$row)); 
		}
	}
}