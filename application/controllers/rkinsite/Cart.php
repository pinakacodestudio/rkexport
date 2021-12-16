<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cart extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Cart_model', 'Cart');
        //$this->load->model('Product_file_model', 'Product_file');
        
        $this->load->model('Side_navigation_model');
        $this->viewData = $this->getAdminSettings('submenu', 'Cart');
    }
    public function index() {
        $this->viewData['title'] = "Cart";
        $this->viewData['module'] = "cart/Cart";
        //Get Channel List
        $this->load->model("Channel_model","Channel"); 
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        //Get Product List
        $this->load->model("Product_model","Product"); 
        $this->viewData['productdata'] = $this->Product->getProductList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Cart','View cart.');
        }
        
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_plugin("form-select2","form-select2/select2.css");
        $this->admin_headerlib->add_javascript_plugins("form-select2","form-select2/select2.min.js");
        $this->admin_headerlib->add_javascript("cart", "pages/cart.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {
        $this->load->model("Product_combination_model","Product_combination");
        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $createddate = $this->general_model->getCurrentDateTime();

        $list = $this->Cart->get_datatables();
        
        $this->load->model("Channel_model","Channel"); 
        $channeldata = $this->Channel->getChannelList();

        $this->load->model("Product_prices_model","Product_prices"); 
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
                $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" class="popoverButton" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($datarow->productname).'</a>';
            }else{
                $productname = '<a href="'.ADMIN_URL.'product/view-product/'.$datarow->productid.'" title="'.ucwords($datarow->productname).'">'.ucwords($datarow->productname).'</a>';
            }
            
            $row[] = ++$counter;
            if($datarow->buyerchannelid != 0){
                $key = array_search($datarow->buyerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->buyerid.'" target="_blank" title="'.$datarow->buyername.'">'.ucwords($datarow->buyername).' ('.$datarow->buyercode.')'."</a>";
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
            }

            if($datarow->sellerchannelid != 0){
                $key = array_search($datarow->sellerchannelid, array_column($channeldata, 'id'));
                if(!empty($channeldata) && isset($channeldata[$key])){
                    $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                }
                $row[] = $channellabel.'<a href="'.ADMIN_URL.'member/member-detail/'.$datarow->sellerid.'" target="_blank" title="'.$datarow->sellername.'">'.ucwords($datarow->sellername).' ('.$datarow->sellercode.')'."</a>";
            
            }else{
                $row[] = '<span class="label" style="background:#49bf88;">COMPANY</span>';
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