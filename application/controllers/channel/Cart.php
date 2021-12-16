<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends Channel_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Cart_model', 'Cart');
        //$this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getChannelSettings('submenu', 'Cart');
    }
    public function index() {
        $this->viewData['title'] = "Cart";
        $this->viewData['module'] = "cart/Cart";
        
        //Get Product List
        $MEMBERID = $this->session->userdata(base_url() . 'MEMBERID');
        $this->load->model('Member_model','Member');
        $this->viewData['productdata'] = $this->Member->getMemberSpecificProductByMemberID($MEMBERID);

        $this->channel_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->channel_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->channel_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->channel_headerlib->add_javascript("cart", "pages/cart.js");
        $this->load->view(CHANNELFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $this->load->model("Product_combination_model","Product_combination");
        $MEMBERID = $this->session->userdata(base_url().'MEMBERID');
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[CHANNEL_URL.'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();
        $list = $this->Cart->get_datatables();
        
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
          
            $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
            if(!empty($channeldata) && isset($channeldata[$key])){
                $channellabel .= '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
            }
          
            /* if(!is_null($datarow->variantprice)){
                $price .= "<span class='pull-right'>".number_format($datarow->variantprice, 2, '.', ',')."</span>";
                $pricemultipywithqty = $datarow->variantprice * $datarow->quantity;
            }else{
                $price .= "<span class='pull-right'>".number_format($datarow->price, 2, '.', ',')."</span>";
                $pricemultipywithqty = $datarow->price * $datarow->quantity;
            } */

            $pricedata = $this->Product_prices->getPriceDetailByIdAndType($datarow->referenceid,$datarow->referencetype);
            $price = (!empty($pricedata)?$pricedata['price']:0);
            $pricemultipywithqty = $price * $datarow->quantity;
            $discount = !empty($pricedata)?$pricedata['discount']:0;

            $netprice = ($pricemultipywithqty - ($pricemultipywithqty * $datarow->tax / (100 + $datarow->tax)));
            $netprice = "<span class='pull-right'>".numberFormat(($netprice - $netprice * $discount / 100), 2, ',')."</span>";
           
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
                $productname = '<a href="'.CHANNEL_URL.'product/view-product/'.$datarow->productid.'" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color:  '.VARIANT_COLOR.' !important;">'.$datarow->productname.'</a>';
            }else{
                $productname = '<a href="'.CHANNEL_URL.'product/view-product/'.$datarow->productid.'">'.$datarow->productname.'</a>';
            }

            $row[] = ++$counter;
            if($MEMBERID == $datarow->buyerid){
                $row[] = $channellabel.ucwords($datarow->buyername).' ('.$datarow->buyercode.')';
            }else{
                $row[] = $channellabel.'<a href="'.CHANNEL_URL.'member/member-detail/'.$datarow->buyerid.'" title="'.ucwords($datarow->buyername).'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'.'</a>';
            }
            $row[] = $productname;
            $row[] = "<span class='pull-right'>".$datarow->quantity."</span>";
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
                        "recordsTotal" => $this->Cart->count_all(),
                        "recordsFiltered" => $this->Cart->count_filtered(),
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