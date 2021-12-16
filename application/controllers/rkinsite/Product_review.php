<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Product_review extends Admin_Controller {

    public $viewData = array();

    function __construct() {
        parent::__construct();
        $this->viewData = $this->getAdminSettings('submenu', 'Product_review');
        $this->load->model('Product_review_model', 'Product_review');
        
    }

    public function index() {
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
        $this->viewData['title'] = "Product Review";
        $this->viewData['module'] = "product_review/Product_review";

        //Get Product list
        $this->load->model("Product_model","Product"); 
        $this->viewData['productdata'] = $this->Product->getProductList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Product Review','View product review.');
        }
        $this->admin_headerlib->add_plugin("jquery.raty.css","raty-master/jquery.raty.css");
        $this->admin_headerlib->add_javascript_plugins("jquery.raty.js","raty-master/jquery.raty.js");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_bottom_javascripts("Product_review", "pages/product_review.js");
        $this->load->view(ADMINFOLDER . 'template', $this->viewData);
    }

    public function listing() {
        $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');
         
        $list = $this->Product_review->get_datatables();
        /* echo "<pre>";
        print_r($list);
        exit; */
        $data = array();
        $counter = $srno = $_POST['start'];
      
        foreach ($list as $Productreview) {
            $row = array();
            $channellabel = "";
             if($Productreview->memberchannelid != 0){
                $key = array_search($Productreview->memberchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
            } 
            $type = $Productreview->type;
            $row[] = ++$counter.'<input type="hidden" name="productreviewid[]" value='.$Productreview->id.' id="delectcheck'.$Productreview->id.'">';

            

            if($Productreview->memberid!=0){
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$Productreview->memberid.'" target="_blank" title="'.ucwords($Productreview->membername).'">'.ucwords($Productreview->membername).' ('.$Productreview->membercode.')</a>';    
            }else{
                $row[] = ucwords($Productreview->membername);
            }
            
            $row[] = $Productreview->email;
            $row[] = $Productreview->mobileno;
            $row[] = '<a href="'.ADMIN_URL.'product/view-product/'.$Productreview->productid.'" target="_blank" title="'.ucwords($Productreview->productname).'">'.ucwords($Productreview->productname).'</a>';

            if($type == 0){
                $dropdownmenu = '<button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Productreview->id.'">Pending <span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu">
                              <li id="dropdown-menu">
                                <a onclick="chagereviewstatus(1,'.$Productreview->id.')">Approved</a>
                              </li>
                              <li id="dropdown-menu">
                                <a onclick="chagereviewstatus(2,'.$Productreview->id.')">Not Approved</a>
                              </li>
                          </ul>';
            }else if($type == 1){
                $dropdownmenu = '<button class="btn btn-success '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Productreview->id.'">Approved <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chagereviewstatus(0,'.$Productreview->id.')">Pending</a>
                                </li>
                              <li id="dropdown-menu">
                                <a onclick="chagereviewstatus(2,'.$Productreview->id.')">Not Approved</a>
                              </li>
                          </ul>';
            }else if($type == 2){
                $dropdownmenu = '<button class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Productreview->id.'">Not Approved <span class="caret"></span></button><ul class="dropdown-menu" role="menu">
                                <li id="dropdown-menu">
                                    <a onclick="chagereviewstatus(0,'.$Productreview->id.')">Pending</a>
                                </li>
                              <li id="dropdown-menu">
                                <a onclick="chagereviewstatus(1,'.$Productreview->id.')">Approved</a>
                              </li>
                          </ul>';
                // $dropdownmenu = '<span class="btn btn-danger '.STATUS_DROPDOWN_BTN.' btn-raised">Not Approved</span>';
            }

            $reviewstatus = '<div class="dropdown" style="float: left;">'.$dropdownmenu.'</div>';

            $Type = '<div class="btn-group dropdown"><button class="btn btn-inverse btn-raised btn-sm" style="cursor: auto;">';
         
            $row[] = '<div class="rating" data-score="'.$Productreview->rating.'"></div><script>$(".rating").raty({hints:false,halfShow : true,readOnly: true,score: function() {return $(this).attr("data-score");}});</script><br>
                <div id="message'.$Productreview->id.'" style="display:none">'.$Productreview->message.'</div>
                <button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="displayproductreview('.$Productreview->id.')">View Review</button><br>'.$reviewstatus;
            
            $row[] = $Productreview->usertype;
            
            $row[] = $this->general_model->displaydatetime($Productreview->createddate);
            $Action='';

            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                $Action .= '<a href="javascript:void(0)" onclick="deleterow('.$Productreview->id.',\'\',\'Product Review\',\''.ADMIN_URL.'product-review/delete_mul_product_review\')" class="'.delete_class.'" title="'.delete_title.'">'.stripslashes(delete_text).'</a>';
            }

            
            $row[] = $Action;

            $row[] = '<div class="checkbox">
                            <input id="deletecheck'.$Productreview->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$Productreview->id.'" name="deletecheck'.$Productreview->id.'" class="checkradios">
                            <label for="deletecheck'.$Productreview->id.'"></label>
                          </div>';
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_review->count_all(),
                        "recordsFiltered" => $this->Product_review->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }

    public function productreviewapproveunapprove() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $modifieddate = $this->general_model->getCurrentDateTime();
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        $updatedata = array("status" => $PostData['val'], "modifieddate" => $modifieddate,"modifiedby"=>$ADMINID);
        $this->Product_review->_where = array("id" => $PostData['id']);
        $this->Product_review->Edit($updatedata);

        echo $PostData['id'];
    }
    
    public function delete_mul_product_review(){
        $PostData = $this->input->post();
        $ids = explode(",",$PostData['ids']);
        $count = 0;
        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        foreach($ids as $row){
            
            $this->Product_review->_fields = "id,memberid";
            $this->Product_review->_where = "id=".$row;
            $this->Product_review->_table = tbl_productreview;
            $ProductreviewData = $this->Product_review->getRecordsByID();

            if(count($ProductreviewData)>0 && $ProductreviewData['memberid']==0){
                $this->Product_review->_table = tbl_productreviewbyguest;
                $this->Product_review->Delete("productreviewid=".$ProductreviewData['id']);    
            }

            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $this->Product_review->_table = tbl_productreview;
                $this->Product_review->_fields = "(SELECT name FROM ".tbl_member." WHERE id=".tbl_productreview.".memberid) as membername";
                $this->Product_review->_where = array("id"=>$row);
                $data = $this->Product_review->getRecordsById();
            
                $this->general_model->addActionLog(3,'Product Review','Delete '.$data['membername'].' product review.');
            }

            $this->Product_review->_table = tbl_productreview;
            $this->Product_review->Delete("id=".$row);
        }
    }
    public function changereviewtype() {
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
        $PostData = $this->input->post();

        $ADMINID = $this->session->userdata(base_url().'ADMINID');
        $modifieddate = $this->general_model->getCurrentDateTime();
        $updatedata = array("type" => $PostData['type'], "modifieddate" => $modifieddate,"modifiedby"=>$ADMINID);
        $this->Product_review->_where = array("id" => $PostData['reviewId']);
        $this->Product_review->Edit($updatedata);

        echo 1;
    }

    public function updateratingstatus(){

        $PostData = $this->input->post();
       
        $updatedata = array();
        $Productreviewid = (isset($PostData['productreviewid']))?$PostData['productreviewid']:'';
        $statustype = (isset($PostData['statustype']))?$PostData['statustype']:'';
        $ADMINID = $this->session->userdata(base_url().'ADMINID');

        if(!empty($Productreviewid)){
            foreach($Productreviewid as $key=>$reviewid){
                $isupdate = (isset($PostData['deletecheck'.$reviewid.'']))?1:0;
               
                if($isupdate == 1){
                    
                    $updatedata[]=array('id'=>$reviewid,
                                        'type'=>$statustype,
                                        'modifiedby'=>$ADMINID,
                                    );
                    }
                   
            }
        }
      
        if(!empty($updatedata)){
            $this->Product_review->edit_batch($updatedata, "id");
        }
        echo 1;

    }
      
}

?>