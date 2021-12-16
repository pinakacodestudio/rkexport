<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Import_openingstock extends Channel_Controller
{
    public $viewData = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Import_openingstock_model', 'Import_openingstock');
        //$this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model', 'Side_navigation');
        $this->viewData = $this->getChannelSettings('submenu', 'Import_openingstock');
    }

    public function index(){
    
        $this->viewData['title'] = "Import Opening Stock";
        $this->viewData['module'] = "import_openingstock/Import_openingstock";
        //$this->viewData['VIEW_STATUS'] = "1";

        $this->channel_headerlib->add_javascript("Import_openingstock", "pages/import_openingstock.js");
        $this->load->view(CHANNELFOLDER.'template', $this->viewData);
    }
    public function listing(){
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];

        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url() . 'CHANNELID');

        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Import_openingstock->get_datatables($MEMBERID,$CHANNELID);
        $data = array();
        $counter = $_POST['start'];
        $pokemon_doc = new DOMDocument();
        //echo "<pre>"; print_r($list); exit;
        $this->load->model("Channel_model","Channel");
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel'); 
        foreach ($list as $datarow) {
            $row = array();
            $actions = '';
            $checkbox = '';
           
            
            if (in_array($rollid, $delete)) {
                $actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Import-openingstock","'.CHANNEL_URL.'import-openingstock/delete-mul-Import-openingstock") >'.delete_text.'</a>';

                $checkbox = '<div class="checkbox"><input value="'.$datarow->id.'" type="checkbox" class="checkradios" name="check'.$datarow->id.'" id="check'.$datarow->id.'" onchange="singlecheck(this.id)"><label for="check'.$datarow->id.'"></label></div>';
            }

            if($datarow->channelid != 0){
                $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channelname = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> '.$channeldata[$key]['name'];
                }
            }else{
                $channelname = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }
            if($datarow->memberid != 0){
                $link = CHANNEL_URL.'member/member-detail/'.$datarow->memberid;
                $membername = '<a href="'.$link.'" target="_blank" title="'.$datarow->membername.'">'.ucwords($datarow->membername)."</a>";
            }else{
                $membername = '-';
            }


            $row[] = ++$counter;
            $row[] = $channelname;
            $row[] = $membername;
            $row[] = $datarow->employeename;
           
            $row[] =' <a href="<?=IMPORT_FILE?>'.$datarow->file.'" class="" download="'.$datarow->file.'" "> '.$datarow->file.'<div class="ripple-container"></div></a>';
            $row[] = $datarow->ipaddress;
            $row[] = '<span class="pull-right">'.$datarow->totalrow.'</span>';
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $datarow->employeename;
           
            $row[] = $actions;
            $row[] = $checkbox;
            $data[] = $row;
        }

        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Import_openingstock->count_all($MEMBERID,$CHANNELID),
                        "recordsFiltered" => $this->Import_openingstock->count_filtered($MEMBERID,$CHANNELID),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function importopeningstock(){
        //$this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
        /*         * ************
          0 - opening stock not import
          1 - opening stock import successfully
          2 - invalid file type
          3 - file not uploaded
          4 - field name not match
         * ************ */

        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url() . 'MEMBERID');
        $totalrowcount = $totalinserted = 0;

        //print_r($_FILES);exit;

        if ($_FILES["importopeningstock"]['name'] != '') {
            $FileNM = uploadFile('importopeningstock', 'IMPORT_FILE', IMPORT_PATH, "ods|xl|xlc|xls|xlsx");
                    
            if ($FileNM !== 0) {
                if ($FileNM==2) {
                    echo 3;//ATTACHMENT NOT UPLOADED
                    exit;
                }
            } else {
                echo 2; //INVALID ATTACHMENT FILE
                exit;
            }
        }

        $file_data = $this->upload->data();
        $file_path = IMPORT_PATH . $FileNM;

        $this->load->library('excel');
        $inputFileType = PHPExcel_IOFactory::identify($file_path);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);     //For excel 2003
        //$objReader= PHPExcel_IOFactory::createReader('Excel2007');    // For excel 2007
        //Set to read only
        $objReader->setReadDataOnly(true);

        //Load excel file
        $objPHPExcel = $objReader->load($file_path);
        $totalrows = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow();   //Count Number of rows avalable in excel
        //echo $totalrows;exit;
        $totalrowcount = $totalrows-1;

        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        
        $column0 = $objWorksheet->getCellByColumnAndRow(0, 1)->getValue();
        $column1 = $objWorksheet->getCellByColumnAndRow(1, 1)->getValue();
        $column2 = $objWorksheet->getCellByColumnAndRow(2, 1)->getValue();
       
        
        $error="";

        if ($column0 == "Seller Code *" && $column1 == "SKU *" && $column2 == "Stock *") {
            $empty = array();
            $moredata = array();
            $createddate = $this->general_model->getCurrentDateTime();
            
            $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
            $CHANNELID = $this->session->userdata(base_url() . 'CHANNELID');

            if ($totalrows>1) {

                $this->load->model('Member_model', 'Member');
                $this->Member->_fields = "id,membercode";
                $this->Member->_where = "id=".$MEMBERID;
                $Memberdata = $this->Member->getRecordByID();
                $Memberidarr = array_column($Memberdata, 'id');
                $Membercodearr = array_column($Memberdata, 'membercode');
                
                
                $this->load->model('Product_model', 'Product');
                $this->Product->_table = (tbl_productprices);
                $this->Product->_fields = "productid,sku";
                $this->Product->_where = "productid IN (SELECT id FROM ".tbl_product." WHERE memberid='".$MEMBERID."' AND channelid='".$CHANNELID."')";
                $this->Product->_order = "id DESC";
                $Productdata = $this->Product->getRecordByID();
                $Productidarr = array_column($Productdata, 'productid');
                $ProductSkuarr = array_column($Productdata, 'sku');
                //print_r($Productidarr);exit;

                   
                //echo $totalrows;exit;
                for ($i = 2; $i <= $totalrows; $i++) {
                    $tr="";

                    $sellercode = trim($objWorksheet->getCellByColumnAndRow(0, $i)->getValue());
                    $sku = trim($objWorksheet->getCellByColumnAndRow(1, $i)->getValue());
                    $stock = trim($objWorksheet->getCellByColumnAndRow(2, $i)->getValue());
                    $stock = (empty($stock))?0:$stock;
                    //echo $stock."row.".$i;//  exit;
                       
                      
                    //blank values
                    if ($sellercode=="") {
                        $tr.="Row ".$i." : Seller Code is blank !<br/>";
                    }
                    if ($sku=="") {
                        $tr.="Row ".$i." : Product SKU is blank !<br/>";
                    }
                        
                    if ($stock=="") {
                        //$tr.="Row ".$i." : Stock is blank !<br/>";
                        $stock=0;
                    }
                       
                    //Sellercode
                    if ($sellercode!="") {
                        
                            if (!in_array($sellercode, $Membercodearr)) {
                                $tr.="Row ".$i." : Seller code is not valid !<br/>";
                            } else {
                                $sellercode = $Memberidarr[array_search($sellercode, $Membercodearr)];
                            }
                        
                    }
                        
                    //SKU
                    if ($sku!="") {
                        if (!in_array($sku, $ProductSkuarr)) {
                            $tr.="Row ".$i." : SKU is not valid !<br/>";
                        } else {
                            $sku = $Productidarr[array_search($sku, $ProductSkuarr)];
                        }
                    }

                   

                    if ($tr=="") {
                            $this->Product->_table = (tbl_productprices);
                            $this->Product->_fields = "id";
                            $this->Product->_where = "productid='".$sku."' AND productid IN (SELECT id FROM ".tbl_product." WHERE memberid='".$MEMBERID."' AND channelid='".$CHANNELID."')";
                            $priceid  = $this->Product->getRecordsById();
                            //print_r($priceid);exit;

                            $updateData = array("stock"=>$stock);
                            $this->Product->_table = (tbl_membervariantprices);
                            $this->Product->_where = "memberid='".$sellercode."' AND priceid='".$priceid['id']."' AND channelid='".$CHANNELID."'";
                            $update = $this->Product->Edit($updateData);
                               
                            //echo $this->writedb->last_query();//exit;
                            //echo $sellercode;exit;
                        
                    } else {
                        $error.=$tr;
                    }
                }

                
                //echo $error;exit;
                if ($error!="") {
                    echo $error;
                }

                if ($error=="") {
                    //echo ":end";
                    $file_info_arr=array("employeeid"=>$MEMBERID,
                                            "channelid"=>$CHANNELID,
                                            "memberid"=>$MEMBERID,
                                            "file"=>$FileNM,
                                            "ipaddress"=>$this->input->ip_address(),
                                            "totalrow"=>$totalrowcount,
                                            "createddate"=>$createddate,
                                            "modifieddate"=>$createddate,
                                            "addedby"=>$MEMBERID,
                                            "modifiedby"=>$MEMBERID,
                                            "status"=>1,
                                            "type"=>1,
                                            "importfrom"=>1);
                
                    $this->Import_openingstock->_table = (tbl_importleadexcel);
                    $ADD = $this->Import_openingstock->add($file_info_arr);
                    if ($ADD) {
                        echo 1;
                    }
                }
            } else {
                echo 5;
            }
        } else {
            if ($column0 != "Seller Code *") {
                $error.="Seller Code field is missing in file!<br/>";
            }
            if ($column1 != "SKU *") {
                $error.="SKU field is missing in file!<br/>";
            }
            if ($column2 != "Stock") {
                $error.="Stock field is missing in file!<br/>";
            }
                

            echo $error;
        }
    }


    public function delete_mul_import_openingstock(){
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
        foreach ($ids as $row) {
            $this->Import_openingstock->_where = array("id"=>$row);
			$data = $this->Import_openingstock->getRecordsByID();
			if($data){
				unlinkfile("IMPORT_FILE", $data['file'], IMPORT_PATH);
			}
            $this->Import_openingstock->Delete(array("id"=>$row));
        }
    }
}