<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_inquiry extends Channel_Controller {

    public $viewData = array();
    public $contenttype;
    function __construct(){
        parent::__construct();
        $this->viewData = $this->getChannelSettings('submenu', 'Product_inquiry');
        $this->load->model('Product_inquiry_model', 'Product_inquiry');
        //$this->load->model('Side_navigation_model');
    }

    public function index() {
        
        $this->viewData['title'] = "Product_inquiry";
        $this->viewData['module'] = "product_inquiry/Product_inquiry";
        $this->viewData['VIEW_STATUS'] = "1";
        
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'');

        $this->viewData['productinquirydata'] = $this->Product_inquiry->get_all_listdata('id','DESC');
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_javascript("product_inquiry", "pages/product_inquiry.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }

    public function listing() {
        $this->load->model("Product_combination_model","Product_combination");
        $list = $this->Product_inquiry->get_datatables();
        $data = array();
        $counter = $srno = $_POST['start'];
        
        $memberid = $this->session->userdata(base_url().'MEMBERID');
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        foreach ($list as $datarow) {
            $row = array();
            $channellabel = '';
            $request = '';
            $membername = '';
            $varianthtml = '';
            $productname = '';
            
            $key = array_search($datarow->channelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
            if($memberid==$datarow->memberid){
                $request .= 'Sent';
                $membername = $channellabel.ucwords($datarow->membername).' ('.$datarow->membercode.')';
            }else{
                $request .= 'Receive';
                $membername = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->memberid.'" target="_blank" title="'.ucwords($datarow->membername).'">'.ucwords($datarow->membername).'</a>';
            }

            if($datarow->isuniversal==0 && $datarow->variantid!=''){
                $variantdata = $this->Product_combination->getProductVariantDetails($datarow->productid,$datarow->variantid);

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
                $productname = '<a href="'.CHANNEL_URL.'product/view-product/'.$datarow->productid.'" target="_blank" class="popoverButton" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.$datarow->productname.'</a>';
            }else{
                $productname = '<a href="'.CHANNEL_URL.'product/view-product/'.$datarow->productid.'" target="_blank" title="'.ucwords($datarow->productname).'">'.ucwords($datarow->productname).'</a>';
            }
            
            $row[] = ++$counter;
            $row[] = $membername;
            $row[] = $productname;
            $row[] = ucwords($datarow->name);
            $row[] = $request;
            $row[] = $this->general_model->displaydate($datarow->createddate);
            $row[] = '<a href="mailto:'.$datarow->email.'?subject=Product Inquiry : '.ucwords($datarow->productname).'" title="'.reply_title.'">'.$datarow->email.'</a>';
            $row[] = $datarow->mobile;
            $row[] = ucwords($datarow->organizations);
            $row[] = ucfirst($datarow->address);

            $Action='';

            if($datarow->message!=''){
                $Action .= '<a href="javascript:void(0)" onclick="viewinquirymessage('.$datarow->id.')" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';
            }
            

            //$Action .= '<a class="'.reply_class.'" href="mailto:'.$datarow->email.'?subject='.$datarow->subject.'" title="'.reply_title.'">'.reply_text.'</a>';
            if(strpos($this->viewData['submenuvisibility']['submenudelete'],','.$this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'].',') !== false){
                
                $Action .= '<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick="deleterow('.$datarow->id.',\'\',\'Product Inquiry\',&quot;'.CHANNEL_URL.'product_inquiry/delete-mul-product-inquiry&quot;,&quot;productinquirytable&quot;)">'.delete_text.'</a>';
            }

            $check = '<td>
                        <div id="message'.$datarow->id.'" style="display:none;">'.$datarow->message.'</div>
                        <div class="checkbox">
                            <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                            <label for="deletecheck'.$datarow->id.'"></label>
                          </div></td>';
            $row[] = $Action;
            $row[] = $check;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Product_inquiry->count_all(),
                        "recordsFiltered" => $this->Product_inquiry->count_filtered(),
                        "data" => $data,
                );
        echo json_encode($output);
    }
    
   
    public function delete_mul_product_inquiry() {
        
        $this->checkAdminAccessModule('submenu', 'delete', $this->viewData['submenuvisibility']);
        $PostData = $this->input->post();
        $ids = explode(",", $PostData['ids']);
        $count = 0;
       
        foreach ($ids as $row) {
            
            $deleteData= array("id"=> $row);
            $this->Product_inquiry->Delete($deleteData);          
      
        }
    }
}