<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Website_cart extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Website_cart_model', 'Website_cart');
               
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Website_cart');
    }
    public function index() {
        $this->viewData['title'] = "Website cart";
        $this->viewData['module'] = "website_cart/Website_cart";
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $CHANNELID = $this->session->userdata(base_url().'CHANNELID');
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelListByMember($MEMBERID,'withcurrentchannel');
        
        //Get Product List
        $this->load->model("Product_model","Product"); 
        $this->viewData['productdata'] = $this->Product->getProductList($MEMBERID,$CHANNELID);
        
        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("Website_cart", "pages/website_cart.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $this->load->model("Product_combination_model","Product_combination");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $sellermemberid = $this->session->userdata(base_url().'MEMBERID');
        $channelid = $this->session->userdata(base_url().'CHANNELID');
        $type=2;
        $usertype=1;

        $list = $this->Website_cart->get_datatables();
        
        $this->load->model("Product_prices_model","Product_prices"); 
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $price = '';
            $channellabel = '';
            $varianthtml = '';
            $productname = '';
            $pricemultipywithqty = '';
            $netprice = '';
          
            $pricedata = $this->Product_prices->getPriceDetailByIdAndType($datarow->referenceid,$datarow->referencetype);
            $price = (!empty($pricedata)?$pricedata['price']:0);
            $pricemultipywithqty = $price * $datarow->quantity;
            $discount = !empty($pricedata)?$pricedata['discount']:0;

            /* if(!is_null($datarow->variantprice)){
                $price .= "<span class='pull-right'>".number_format($datarow->variantprice, 2, '.', ',')."</span>";

                $pricemultipywithqty = $datarow->variantprice * $datarow->quantity;
            }else{
                $price .= "<span class='pull-right'>".number_format($datarow->price, 2, '.', ',')."</span>";
                $pricemultipywithqty = $datarow->price * $datarow->quantity;
            } */
            $netprice = ($pricemultipywithqty - ($pricemultipywithqty * $datarow->tax / (100 + $datarow->tax)));
            $netprice = "<span class='pull-right'>".numberFormat(($netprice - $netprice * $datarow->discount / 100), 2, ',')."</span>";
           
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
                $productname = '<a href="'.CHANNEL_URL.'product/view-product/'.$datarow->productid.'" class="popoverButton" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($datarow->productname).'</a>';
            }else{
                $productname = '<a href="'.CHANNEL_URL.'product/view-product/'.$datarow->productid.'" title="'.ucwords($datarow->productname).'">'.ucwords($datarow->productname).'</a>';
            }
            
            $row[] = ++$counter;
            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                
                $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'."</a>";
            }else{
                $row[] = '<span class="label">COMPANY</span>';
            }

            
            $row[] = $productname;
            $row[] = "<span class='pull-right'>".numberFormat($datarow->quantity, 2, ',')."</span>";
            $row[] = "<span class='pull-right'>".numberFormat($price, 2, ',')."</span>";
            $row[] = "<span class='pull-right'>".numberFormat($datarow->tax, 2, ',')."</span>";
            $row[] = "<span class='pull-right'>".numberFormat($discount, 2, ',')."</span>"; 
            $row[] = $netprice;           
            $row[] = $datarow->productvariants!=''?$datarow->productvariants:'-';
            $row[] = date('d M Y h:i A',strtotime($datarow->createddate));
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Website_cart->count_all(),
                        "recordsFiltered" => $this->Website_cart->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }


    public function getvariant()
    {
        $PostData = $this->input->post();
        $this->load->model('Variant_model', 'Variant');
        $variant = $this->Variant->getVariantDataByAttributeID($PostData['attributeid']);
        echo json_encode($variant);
    }
}