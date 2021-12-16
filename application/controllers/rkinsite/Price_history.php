<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Price_history extends Admin_Controller {

    public $viewData = array();
    function __construct(){
        parent::__construct();
        $this->load->model('Price_history_model', 'Price_history');
        $this->viewData = $this->getAdminSettings('submenu', 'Price_history');
        // $this->load->model('Member_model', 'Member');
    }
    public function index() {
        
        $this->viewData['title'] = "Price History";
        $this->viewData['module'] = "price_history/Price_history";
        
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $this->general_model->addActionLog(4,'Price History','View price history.');
        }

        $this->admin_headerlib->add_javascript_plugins("bootstrap-datepicker","bootstrap-datepicker/bootstrap-datepicker.js");
        $this->admin_headerlib->add_javascript("Price_history", "pages/price_history.js");
        $this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    public function listing() {

        $edit = explode(',', $this->viewData['submenuvisibility']['submenuedit']);
        $delete = explode(',', $this->viewData['submenuvisibility']['submenudelete']);
        $rollid = $this->session->userdata[base_url().'ADMINUSERTYPE'];
        $currentdatetime = $this->general_model->getCurrentDateTime();

        $list = $this->Price_history->get_datatables();
        $data = array();       
        $counter = $_POST['start'];
        foreach ($list as $datarow) {         
            $row = array();
            $channellabel = '';
            $status = '';
            $Actions = ''; 
            $Checkbox = '';
            $approvestatus = '';

            if($datarow->scheduleddate!='0000-00-00 00:00:00' && $datarow->scheduleddate > $currentdatetime){

                if(in_array($rollid, $edit)){
                    $Actions .= '<a class="'.edit_class.'" href="'.ADMIN_URL.'price-history/price-history-edit/'. $datarow->id.'/'.'" title="'.edit_title.'">'.edit_text.'</a>';
                }
                if(in_array($rollid, $delete)) {
                    $Actions.='<a class="'.delete_class.'" href="javascript:void(0)" title="'.delete_title.'" onclick=deleterow('.$datarow->id.',"","Product","'.ADMIN_URL.'price-history/delete-mul-price-history","") >'.delete_text.'</a>';
    
                    $Checkbox = '<div class="checkbox">
                                    <input id="deletecheck'.$datarow->id.'" onchange="singlecheck(this.id)" type="checkbox" value="'.$datarow->id.'" name="deletecheck'.$datarow->id.'" class="checkradios">
                                    <label for="deletecheck'.$datarow->id.'"></label>
                                </div>';
    
                }
            }

            $Actions .= '<a href="'.ADMIN_URL.'price-history/view-price-history/'. $datarow->id.'/'.'" class="'.view_class.'" title="'.view_title.'">'.view_text.'</a>';   
            //echo $Checkbox;exit;
            $row[] = ++$counter;
            $row[] = $datarow->typename;
            $row[] = ($datarow->scheduleddate!="0000-00-00 00:00:00")?$this->general_model->displaydatetime($datarow->scheduleddate):"-";
            $row[] = ($datarow->remarks!='')?$datarow->remarks:'-';
            $row[] = ucwords($datarow->addedby);         
            $row[] = $this->general_model->displaydatetime($datarow->createddate);
            $row[] = $Actions;
            $row[] = $Checkbox;
            $data[] = $row;
        }
        $output = array(
                        "draw" => $_POST['draw'],
                        "recordsTotal" => $this->Price_history->count_all(),
                        "recordsFiltered" => $this->Price_history->count_filtered(),
                        "data" => $data,
                        );
        echo json_encode($output);
    }

    public function price_history_add() {
        
        $this->checkAdminAccessModule('submenu','add',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Add Price History";
		$this->viewData['module'] = "price_history/Add_price_history";
        
        $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getmaincategory();

        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript("add_price_history","pages/add_price_history.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function price_history_edit($ID) {
        
        $this->checkAdminAccessModule('submenu','edit',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "Edit Price History";
		$this->viewData['module'] = "price_history/Add_price_history";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;
        $currentdatetime = $this->general_model->getCurrentDateTime();

        $pricehistorydata = $this->Price_history->getPricehistoryDataById($ID);
        // echo "<pre>"; print_r($pricehistorydata); exit;
        if($pricehistorydata['scheduleddate']=='0000-00-00 00:00:00' || $pricehistorydata['scheduleddate'] < $currentdatetime){
            
            redirect('Pagenotfound');
        }
        $this->viewData['pricehistorydata'] = $pricehistorydata;
        $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getmaincategory();
        
        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList();
        // echo "<pre>"; print_r($this->viewData['pricehistorydata']); exit;
        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript("edit_price_history","pages/add_price_history.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }

    public function view_price_history($ID) {
        
        $this->checkAdminAccessModule('submenu','view',$this->viewData['submenuvisibility']);
		$this->viewData['title'] = "View Price History";
		$this->viewData['module'] = "price_history/Add_price_history";
        $this->viewData['VIEW_STATUS'] = "1";
        $this->viewData['action'] = 1;
        $this->viewData['displaytype'] = 'view';

        $pricehistorydata = $this->Price_history->getPricehistoryDataById($ID);
        
        if(empty($pricehistorydata)){
            redirect('Pagenotfound');
        }
        $this->viewData['pricehistorydata'] = $pricehistorydata;
        $this->load->model('Category_model', 'Category');
        $this->viewData['categorydata'] = $this->Category->getmaincategory();

        $this->load->model('Channel_model', 'Channel');
        $this->viewData['channeldata'] = $this->Channel->getChannelList();

        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $name = ($pricehistorydata['type']==0?'admin product':Member_label.' product');
            $this->general_model->addActionLog(4,'Price History','View '.$name.' price history.');
        }

        $this->admin_headerlib->add_javascript("bootstrap-toggle.min","bootstrap-toggle.min.js");
        $this->admin_headerlib->add_stylesheet("bootstrap-toggle.min","bootstrap-toggle.min.css");
        $this->admin_headerlib->add_javascript_plugins("bootstrap-datetimepicker","bootstrap-datetimepicker/bootstrap-datetimepicker.js");
        $this->admin_headerlib->add_javascript("edit_price_history","pages/add_price_history.js");
		$this->load->view(ADMINFOLDER.'template',$this->viewData);
    }
    
    public function getpricehistorydata() {

        $this->load->model("Product_combination_model","Product_combination");
        $this->load->model("Channel_model","Channel"); 
        $this->load->model("Member_model","Member"); 
        $this->load->model("Price_list_model","Price_list"); 

        $PostData = $this->input->post();
        $type = $PostData['type'];
        $categoryid = (!empty($PostData['categoryid']))?implode(",", $PostData['categoryid']):0;
        $productid = (!empty($PostData['productid']))?implode(",", $PostData['productid']):'';
        $pricehistoryid = (!empty($PostData['pricehistoryid']))?$PostData['pricehistoryid']:'';
        $displaytype = $PostData['displaytype'];
        $readonly = ($PostData['displaytype']=='view')?'readonly':'';
        $disabled = ($PostData['displaytype']=='view')?'disabled':'';

        $datahtml = '';
        $counter = 0;

        if($type==0){

            $pricehistorydata = $this->Price_history->getproductpricehistorydata($categoryid,$productid,$pricehistoryid);
            $channeldata = $this->Channel->getChannelList('notdisplayguestorvendorchannel');

            if(!empty($pricehistorydata)){
                $datahtml .= '<table class="table table-bordered" width="100%"><thead>';
                
                $datahtml .= '<tr>';
                $datahtml .= '<th>Sr.No.</th>';
                $datahtml .= '<th>Product Name</th>';
                $datahtml .= '<th>Category</th>';
                $datahtml .= '<th class="text-right width15">Admin Price <input type="hidden" name="channelid[]" value="0"></th>';

                if(!empty($channeldata)){
                    foreach($channeldata as $channel){
                        $datahtml .= '<th class="text-center">'.$channel['name'].'<input type="hidden" name="channelid[]" value="'.$channel['id'].'"></th>';
                    }
                }
                $datahtml .= '</tr>';
                $datahtml .= '</thead><tbody>';
                
                foreach($pricehistorydata as $datarow){
                    $varianthtml = $amount = $pricepercentage = $productpricehistoryid = '';
                    $pricetype = $priceincreaseordecrease = 'checked';
                    
                    if(!empty($pricehistoryid)){
                        $productpricehistoryid = $datarow['productpricehistoryid'];
                        $amount = $datarow['amount'];
                        $pricepercentage = $datarow['pricepercentage'];
                        if($productpricehistoryid!=''){
                            $pricetype = ($datarow['pricetype']==0)?"checked":"";
                            $priceincreaseordecrease = ($datarow['priceincreaseordecrease']==1)?"checked":"";
                        }
                    }

                    if($datarow['isuniversal']==0 && $datarow['variantid']!=''){
                        $variantdata = $this->Product_combination->getProductVariantDetails($datarow['id'],$datarow['variantid']);
        
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
                        $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($datarow['productname']).'</a>';
                    }else{
                        $productname = ucwords($datarow['productname']);
                    }
                    if(isset($datarow['actualprice'])){
                        $adminprice = $datarow['actualprice'];
                    }else{
                        $adminprice = $datarow['price'];
                    }
                    $datahtml .= "<tr><td>".++$counter."</td>";
                    $datahtml .= "<td>".$productname."</td>";
                    $datahtml .= "<td>".$datarow['categoryname']."</td>";
                    $datahtml .= "<td>
                    <p class='text-right'>".number_format($adminprice,2,'.',',')."</p>";

                    $datahtml .= '<div class="col-md-6 mr-xs"><div class="form-group is-empty" id=""><input type="text" name="amount['.$datarow['priceid'].'][0]" id="amount_'.$datarow['priceid'].'_0" class="form-control amount" value="'.$amount.'" placeholder="Amount" onkeypress="return decimal(event,this.value);" '.$readonly.'><span class="material-input"></span></div></div>
                    
                    <div class="col-md-5 ml-xs"><div class="form-group is-empty" id=""><input type="text" name="pricepercentage['.$datarow['priceid'].'][0]" id="pricepercentage_'.$datarow['priceid'].'_0" class="form-control percentage" value="'.$pricepercentage.'" placeholder="Percent" onkeypress="return decimal(event,this.value)" '.$readonly.'><span class="material-input"></span></div></div>

                    <div class="col-sm-6 p-n">
                        <div class="yesnoprice">
                            <input type="checkbox" name="incrementdecrementprice['.$datarow['priceid'].'][0]" id="incrementdecrementprice_'.$datarow['priceid'].'_0" value="1" '.$priceincreaseordecrease.' '.$disabled.'>
                        </div>
                    </div>';

                    $datahtml .= '<div class="col-sm-6 pl-xs">
                                            <div class="amntpercent">
                                                <input type="checkbox" name="pricetype['.$datarow['priceid'].'][0]" value="1" id="pricetype_'.$datarow['priceid'].'_0" '.$pricetype.' '.$disabled.'>
                                                </div>
                                            </div></td>';

                    if($displaytype!='view'){
                        $datahtml .= '
                        <input type="hidden" name="productpricehistoryid['.$datarow['priceid'].'][0]" value="'.$productpricehistoryid.'" id="productpricehistoryid_'.$datarow['priceid'].'_0">
                        <input type="hidden" name="categoryids[]" value="'.$datarow['categoryid'].'" id="categoryids'.$counter.'">
                        <input type="hidden" name="productid[]" value="'.$datarow['id'].'" id="productid'.$counter.'">
                        <input type="hidden" name="productpriceid[]" value="'.$datarow['priceid'].'" id="productpriceid'.$counter.'">
                        <input type="hidden" name="price['.$datarow['priceid'].'][0]" value="'.$adminprice.'" id="price_'.$datarow['priceid'].'_0">';
                    }
                    if(!empty($channeldata)){
                        foreach($channeldata as $channel){
        
                            $pricedata = $this->Price_list->getChannelBasicPrice($channel['id'],$datarow['id'],$datarow['priceid'],$pricehistoryid);
                            
                            $amount = $pricepercentage = $productpricehistoryid = '';
                            $pricetype = $priceincreaseordecrease = 'checked';
                            
                            if(!empty($pricehistoryid)){

                                $productpricedata = $this->Price_history->getChannelPriceHistoryData($channel['id'],$datarow['priceid'],$pricehistoryid);
                                if(!empty($productpricedata)){
                                    $productpricehistoryid = $productpricedata['productpricehistoryid'];
                                    $amount = $productpricedata['amount'];
                                    $pricepercentage = $productpricedata['pricepercentage'];
                                    $pricetype = ($productpricedata['pricetype']==0)?"checked":"";
                                    $priceincreaseordecrease = ($productpricedata['priceincreaseordecrease']==1)?"checked":"";
                                }
                            }
                            
                            if(!empty($pricedata)){
                                $productbasicpricemappingid = $pricedata['id'];
                                if(isset($pricedata['actualprice'])){
                                    $price = $pricedata['actualprice'];
                                }else{
                                    $price = $pricedata['salesprice'];
                                }
                            }else{
                                $price = ($datarow['price']>0)?$datarow['price']:'0';
                                $productbasicpricemappingid = "";
                            }
                            

                            $datahtml .= '<td class="width15">
                            <p class="text-right">'.number_format($price,2,'.',',').'</p>
                            <div class="col-md-6 mr-xs"><div class="form-group is-empty" id=""><input type="text" name="amount['.$datarow['priceid'].']['.$channel['id'].']" id="amount_'.$datarow['priceid'].'_'.$channel['id'].'" class="form-control amount" value="'.$amount.'" placeholder="Amount" onkeypress="return decimal(event,this.value)" '.$readonly.'><span class="material-input"></span></div></div>
                            
                            <div class="col-md-5 ml-xs"><div class="form-group is-empty" id=""><input type="text" name="pricepercentage['.$datarow['priceid'].']['.$channel['id'].']" id="pricepercentage_'.$datarow['priceid'].'_'.$channel['id'].'" class="form-control percentage" value="'.$pricepercentage.'" placeholder="Percent" onkeypress="return decimal(event,this.value)" '.$readonly.'><span class="material-input"></span></div></div>
                            ';

                            $datahtml .= '<div class="col-sm-6 p-n">
                                            <div class="yesnoprice">
                                                <input type="checkbox" name="incrementdecrementprice['.$datarow['priceid'].']['.$channel['id'].']" id="incrementdecrementprice_'.$datarow['priceid'].'_'.$channel['id'].'" value="1" '.$priceincreaseordecrease.' '.$disabled.'>
                                                </div>
                                            </div>';

                            $datahtml .= '<div class="col-sm-6 pl-xs">
                                            <div class="amntpercent">
                                                <input type="checkbox" name="pricetype['.$datarow['priceid'].']['.$channel['id'].']" id="pricetype_'.$datarow['priceid'].'_'.$channel['id'].'" value="1" '.$pricetype.' '.$disabled.'>
                                                </div>
                                            </div></td>';

                            if($displaytype!='view'){
                                $datahtml .= ' 
                                <input type="hidden" name="productpricehistoryid['.$datarow['priceid'].']['.$channel['id'].']" value="'.$productpricehistoryid.'" id="productpricehistoryid_'.$datarow['priceid'].'_'.$channel['id'].'">
                                <input type="hidden" name="price['.$datarow['priceid'].']['.$channel['id'].']" value="'.$price.'" id="price_'.$datarow['priceid'].'_'.$channel['id'].'">';
                            }
                        }
                    }
                    
                    $datahtml .= '</tr>';
                }
                $datahtml .= '</tbody>';
                $datahtml .= '</table>';
            }
        }else if($type==1){
            
            $channelid = $PostData['channelid'];
            $memberid = (!empty($PostData['memberid']))?implode(",", $PostData['memberid']):0;

            $pricehistorydata = $this->Price_history->getmemberproductpricehistorydata($channelid,$memberid,$categoryid,$productid,$pricehistoryid);
            
            $memberdata = $this->Member->getActiveMemberByChannel($channelid,$memberid);

            if(!empty($pricehistorydata)){

                $datahtml .= '<table class="table table-bordered" width="100%"><thead>';
                $datahtml .= '<tr>';
                $datahtml .= '<th class="width5">Sr.No.</th>';
                $datahtml .= '<th width="20%">Product Name</th>';
                $datahtml .= '<th class="width8">Category</th>';
                if(empty($pricehistoryid)){
                    $datahtml .= '<th class="text-center" style="width: 20%;">Change to All</th>';
                }
                if(!empty($memberdata)){
                    foreach($memberdata as $member){
                        $this->Price_history->_table = tbl_membervariantprices;
                        $this->Price_history->_where = ("memberid = ".$member['id']." AND priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid IN (".$productid.")) AND sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$member['id'].")");
                        $Count = $this->Price_history->CountRecords();
                        
                        if($Count > 0){
                            $datahtml .= '<th class="text-center" style="width: 20%;">'.ucwords($member['name']).'</th>';
                        }
                    }
                }
                $datahtml .= '</tr>';
                $datahtml .= '</thead><tbody>';
                
                foreach($pricehistorydata as $datarow){
                    $varianthtml = $memberproductpricehistoryid = '';
                    
                    if($datarow['isuniversal']==0 && $datarow['variantid']!=''){
                        $variantdata = $this->Product_combination->getProductVariantDetails($datarow['id'],$datarow['variantid']);
        
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
                        $productname = '<a href="javascript:void(0)" class="popoverButton a-without-link" data-trigger="hover" data-container="body" data-toggle="popover" title="Variant Information" data-content="'.$varianthtml.'" style="color: '.VARIANT_COLOR.' !important;">'.ucwords($datarow['productname']).'</a>';
                    }else{
                        $productname = ucwords($datarow['productname']);
                    }
                    
                    $datahtml .= "<tr><td>".++$counter."</td>";
                    $datahtml .= "<td>".$productname."</td>";
                    $datahtml .= "<td>".$datarow['categoryname']."</td>";
                    if(empty($pricehistoryid)){
                        $datahtml .= "<td style='width: 20%;'><p>&nbsp;</p>";

                        $datahtml .= '<div class="col-md-6 mr-xs">
                                        <div class="form-group is-empty" id="">
                                            <input type="text" id="changeamount_'.$datarow['priceid'].'_0" class="form-control amount" value="" placeholder="Amount" onkeypress="return decimal(event,this.value);">
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                        
                                    <div class="col-md-5 ml-xs">
                                        <div class="form-group is-empty" id="">
                                            <input type="text" id="changepricepercentage_'.$datarow['priceid'].'_0" class="form-control percentage" placeholder="Percent" onkeypress="return decimal(event,this.value)">
                                            <span class="material-input"></span>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 p-n">
                                        <div class="yesnoprice">
                                            <input type="checkbox" id="changeincrementdecrementprice_'.$datarow['priceid'].'_0" value="1" checked>
                                        </div>
                                    </div>';

                        $datahtml .= '<div class="col-sm-6 pl-xs">
                                        <div class="amntpercent">
                                            <input type="checkbox" id="changepricetype_'.$datarow['priceid'].'_0" value="1" checked>      
                                        </div>
                                    </div></td>';
                    }
                    if($displaytype!='view'){
                        $datahtml .= '
                        <input type="hidden" name="categoryids[]" value="'.$datarow['categoryid'].'" id="categoryids'.$counter.'">
                        <input type="hidden" name="productid[]" value="'.$datarow['id'].'" id="productid'.$counter.'">
                        <input type="hidden" name="productpriceid[]" value="'.$datarow['priceid'].'" id="productpriceid'.$counter.'">
                        ';
                    }
                    if(!empty($memberdata)){
                        foreach($memberdata as $member){
                        
                            $this->Price_history->_table = tbl_membervariantprices;
                            $this->Price_history->_where = ("memberid = ".$member['id']." AND priceid IN (SELECT id FROM ".tbl_productprices." WHERE productid IN (".$productid.")) AND sellermemberid IN (SELECT mainmemberid FROM ".tbl_membermapping." WHERE submemberid=".$member['id'].")");
                            $Count = $this->Price_history->CountRecords();
                            
                            if($Count > 0){
                                /* $pricedata = $this->Price_list->getChannelBasicPrice($channeldata['id'],$datarow['id'],$datarow['priceid'],$pricehistoryid); */

                                $pricedata = $this->Price_history->getMemberProductPrice($channelid,$member['id'],$datarow['priceid'],$pricehistoryid);
                                
                                $amount = $pricepercentage = $memberproductpricehistoryid = '';
                                $pricetype = $priceincreaseordecrease = 'checked';
                                
                                if(!empty($pricedata)){
                                    $price = $pricedata['price'];
                                    $membervariantpriceid = $pricedata['id'];
                                    //$productbasicpricemappingid = $pricedata['id'];
                                    if(!empty($pricehistoryid)){
            
                                        $memberproductpricehistoryid = $pricedata['memberproductpricehistoryid'];
                                        $amount = $pricedata['amount'];
                                        $pricepercentage = $pricedata['pricepercentage'];
                                        $pricetype = ($pricedata['pricetype']==0)?"checked":"";
                                        $priceincreaseordecrease = ($pricedata['priceincreaseordecrease']==1)?"checked":"";
                                        $price = $pricedata['actualprice'];
                                    }
                                }else{
                                    $price = $datarow['price'];
                                    $membervariantpriceid = '';
                                }
                            
                                
                                $datahtml .= '<td style="width: 20%;">
                                                <p class="text-right">'.number_format($price,2,'.',',').'</p>';
                                
                                if(!empty($pricedata)){
                                    $datahtml .= '<div class="col-md-6 mr-xs">
                                                        <div class="form-group is-empty" id="">
                                                            <input type="text" name="amount['.$datarow['priceid'].']['.$member['id'].']" id="amount_'.$datarow['priceid'].'_'.$member['id'].'" class="form-control amount" value="'.$amount.'" placeholder="Amount" onkeypress="return decimal(event,this.value)" '.$readonly.'>
                                                            <span class="material-input"></span>
                                                        </div>
                                                    </div>
                                    
                                                    <div class="col-md-5 ml-xs">
                                                        <div class="form-group is-empty" id="">
                                                            <input type="text" name="pricepercentage['.$datarow['priceid'].']['.$member['id'].']" id="pricepercentage_'.$datarow['priceid'].'_'.$member['id'].'" class="form-control percentage" value="'.$pricepercentage.'" placeholder="Percent" onkeypress="return decimal(event,this.value)" '.$readonly.'>
                                                            <span class="material-input"></span>
                                                        </div>
                                                    </div>';

                                    $datahtml .= '<div class="col-sm-6 p-n">
                                                    <div class="yesnoprice">
                                                        <input type="checkbox" name="incrementdecrementprice['.$datarow['priceid'].']['.$member['id'].']" id="incrementdecrementprice_'.$datarow['priceid'].'_'.$member['id'].'" value="1" '.$priceincreaseordecrease.' '.$disabled.'>
                                                        </div>
                                                    </div>';

                                    $datahtml .= '<div class="col-sm-6 pl-xs">
                                                <div class="amntpercent">
                                                    <input type="checkbox" name="pricetype['.$datarow['priceid'].']['.$member['id'].']" id="pricetype_'.$datarow['priceid'].'_'.$member['id'].'" value="1" '.$pricetype.' '.$disabled.'>
                                                    </div>
                                                </div>';
                                                
                                    $datahtml .= '</td>';
                                }
                                if($displaytype!='view'){
                                    $datahtml .= ' 
                                    <input type="hidden" name="memberproductpricehistoryid['.$datarow['priceid'].']['.$member['id'].']" value="'.$memberproductpricehistoryid.'" id="memberproductpricehistoryid_'.$datarow['priceid'].'_'.$member['id'].'">
                                    ';
                                    if(!empty($pricedata)){
                                        $datahtml .= '<input type="hidden" name="sellermemberid['.$datarow['priceid'].']['.$member['id'].']" value="'.$member['sellerid'].'" id="memberid'.$counter.'">
                                        <input type="hidden" name="memberid['.$datarow['priceid'].'][]" value="'.$member['id'].'" id="memberid'.$counter.'">
                                        <input type="hidden" name="price['.$datarow['priceid'].']['.$member['id'].']" value="'.$price.'" id="price_'.$datarow['priceid'].'_'.$member['id'].'">
                                        
                                        <input type="hidden" name="membervariantpriceid['.$datarow['priceid'].']['.$member['id'].']" value="'.$membervariantpriceid.'" id="membervariantpriceid_'.$datarow['priceid'].'_'.$member['id'].'">
                                        '; 
                                    }
                                }
                            }
                        }
                    }
                    $datahtml .= '</tr>';
                }
                $datahtml .= '</tbody>';
                $datahtml .= '</table>';
            }
        }
        $json['html'] = $datahtml;
        echo json_encode($json);
    }
     
    public function add_price_history() {
        
        $PostData = $this->input->post();
        $createddate = $this->general_model->getCurrentDateTime();
        $addedby = $this->session->userdata(base_url().'ADMINID');
        // echo "<pre>"; print_r($PostData); exit;

        $type = $PostData['type']; // 0 For Admin & 1 For Member Product
        $scheduleddate = ($PostData['scheduleddate']!='')?$this->general_model->convertdatetime($PostData['scheduleddate']):'';
        $remarks = $PostData['remarks'];
        
        $insertdata = array(
            "type" => $type,
            "scheduleddate" => $scheduleddate,
            "remarks" => $remarks,
            "usertype" => 0,
            "createddate" => $createddate,
            "modifieddate" => $createddate,
            "addedby" => $addedby,
            "modifiedby" => $addedby
        );

        $insertdata = array_map('trim',$insertdata);
        $PriceHistoryId = $this->Price_history->Add($insertdata);

        if($PriceHistoryId){
            
            if($type==0){
                
                $channelidarr = $PostData['channelid'];
                $productpriceidarr = $PostData['productpriceid'];
                $categoryidsarr = $PostData['categoryids'];
                $actualpricearr = $PostData['price'];
                $pricearr = $PostData['amount'];
                $pricepercentagearr = $PostData['pricepercentage'];
                $pricetypearr = (isset($PostData['pricetype']))?$PostData['pricetype']:'';
                $priceincreaseordecreasearr = (isset($PostData['incrementdecrementprice']))?$PostData['incrementdecrementprice']:'';
                
                $insertpricehistorydata = $updateproductprice = $updatechannelsalesprice = array();
                if(!empty($productpriceidarr)){
                    
                    foreach($productpriceidarr as $key=>$productpriceid){
                        
                        foreach($channelidarr as $channelid){

                            $categoryid = (isset($categoryidsarr[$key]))?$categoryidsarr[$key]:'';
                            $actualprice = (isset($actualpricearr[$productpriceid][$channelid]))?$actualpricearr[$productpriceid][$channelid]:'';
                            $price = (isset($pricearr[$productpriceid][$channelid]))?$pricearr[$productpriceid][$channelid]:'';
                            $pricepercentage = (isset($pricepercentagearr[$productpriceid][$channelid]))?$pricepercentagearr[$productpriceid][$channelid]:'';
                            $pricetype = (isset($pricetypearr[$productpriceid][$channelid]))?0:1;
                            $priceincreaseordecrease = (isset($priceincreaseordecreasearr[$productpriceid][$channelid]))?1:0;

                            if($categoryid!='' && !empty($price) && !empty($pricepercentage)){

                                $insertpricehistorydata[]=array("pricehistoryid"=>$PriceHistoryId,
                                                                "channelid"=>$channelid,
                                                                "categoryid"=>$categoryid,
                                                                "productpriceid"=>$productpriceid,
                                                                "actualprice"=>$actualprice,
                                                                "price"=>$price,
                                                                "pricepercentage"=>$pricepercentage,
                                                                "pricetype"=>$pricetype,
                                                                "priceincreaseordecrease"=>$priceincreaseordecrease,
                                                                "createddate"=>$createddate
                                                            );

                            }
                        }
                    }

                    if(!empty($insertpricehistorydata)){
                        $this->Price_history->_table = tbl_productpricehistory;
                        $this->Price_history->Add_batch($insertpricehistorydata);
                    }
                }
            }else{
                $channelid = $PostData['channelid'];
                $memberidarr = $PostData['memberid'];
                $productpriceidarr = $PostData['productpriceid'];
                $categoryidsarr = $PostData['categoryids'];

                $actualpricearr = $PostData['price'];
                $pricearr = $PostData['amount'];
                $pricepercentagearr = $PostData['pricepercentage'];
                $pricetypearr = (isset($PostData['pricetype']))?$PostData['pricetype']:'';
                $priceincreaseordecreasearr = (isset($PostData['incrementdecrementprice']))?$PostData['incrementdecrementprice']:'';

                $membervariantpriceidarr = $PostData['membervariantpriceid'];
                $sellermemberidarr = $PostData['sellermemberid'];

                $insertmemberpricehistorydata = array();
                if(!empty($productpriceidarr)){
                    
                    foreach($productpriceidarr as $key=>$productpriceid){
                        
                        $memberids = (isset($memberidarr[$productpriceid]))?$memberidarr[$productpriceid]:'';
                        $categoryid = (isset($categoryidsarr[$key]))?$categoryidsarr[$key]:'';
                        
                        if(!empty($memberids)){
                            foreach($memberids as $memberid){
                                
                                $sellermemberid = (isset($sellermemberidarr[$productpriceid][$memberid]))?$sellermemberidarr[$productpriceid][$memberid]:'';
                                $membervariantpriceid = (isset($membervariantpriceidarr[$productpriceid][$memberid]))?$membervariantpriceidarr[$productpriceid][$memberid]:'';

                                $actualprice = (isset($actualpricearr[$productpriceid][$memberid]))?$actualpricearr[$productpriceid][$memberid]:'';
                                $price = (isset($pricearr[$productpriceid][$memberid]))?$pricearr[$productpriceid][$memberid]:'';
                                $pricepercentage = (isset($pricepercentagearr[$productpriceid][$memberid]))?$pricepercentagearr[$productpriceid][$memberid]:'';
                                $pricetype = (isset($pricetypearr[$productpriceid][$memberid]))?0:1;
                                $priceincreaseordecrease = (isset($priceincreaseordecreasearr[$productpriceid][$memberid]))?1:0;
                                
                                if($categoryid!='' && !empty($price) && !empty($pricepercentage)){
    
                                    $insertmemberpricehistorydata[]=array(
                                        "pricehistoryid"=>$PriceHistoryId,
                                        "sellermemberid"=>$sellermemberid,
                                        "channelid"=>$channelid,
                                        "memberid"=>$memberid,
                                        "categoryid"=>$categoryid,
                                        "membervariantpriceid"=>$membervariantpriceid,
                                        "actualprice"=>$actualprice,
                                        "price"=>$price,
                                        "pricepercentage"=>$pricepercentage,
                                        "pricetype"=>$pricetype,
                                        "priceincreaseordecrease"=>$priceincreaseordecrease,
                                        "createddate"=>$createddate
                                    );
                                }
                            }
                        }
                    }

                    if(!empty($insertmemberpricehistorydata)){
                        $this->Price_history->_table = tbl_memberproductpricehistory;
                        $this->Price_history->Add_batch($insertmemberpricehistorydata);
                    }
                }
            }
            if($scheduleddate==""){
                $this->Price_history->updateAdminProductOrMemberProductPrice($PriceHistoryId);
            }
            if($this->viewData['submenuvisibility']['managelog'] == 1){
                $name = ($type==0?'admin product':Member_label.' product');
                $this->general_model->addActionLog(1,'Price History','Add new '.$name.' price history.');
            }
            echo 1;
        }else{
            echo 0;
        }
    }

    public function update_price_history() {
        
        $PostData = $this->input->post();
        $modifieddate = $this->general_model->getCurrentDateTime();
        $modifiedby = $this->session->userdata(base_url().'ADMINID');
        // echo "<pre>"; print_r($PostData); exit;

        $PriceHistoryId = $PostData['pricehistoryid'];
        $ProductPriceHistoryId = (!empty($PostData['oldproductpricehistoryid']))?explode(",",$PostData['oldproductpricehistoryid']):array();
        $type = $PostData['type']; // 0 For Admin & 1 For Member Product
        $scheduleddate = ($PostData['scheduleddate']!='')?$this->general_model->convertdatetime($PostData['scheduleddate']):'';
        $remarks = $PostData['remarks'];
        
        $updatedata = array(
            "type" => $type,
            "scheduleddate" => $scheduleddate,
            "remarks" => $remarks,
            "modifieddate" => $modifieddate,
            "modifiedby" => $modifiedby
        );

        $updatedata = array_map('trim',$updatedata);
        $this->Price_history->_where = array("id"=>$PriceHistoryId);
        $this->Price_history->Edit($updatedata);

        if($type==0){
            
            $channelidarr = $PostData['channelid'];
            $productpriceidarr = $PostData['productpriceid'];
            $categoryidsarr = $PostData['categoryids'];
            $actualpricearr = $PostData['price'];
            $pricearr = $PostData['amount'];
            $pricetypearr = (isset($PostData['pricetype']))?$PostData['pricetype']:'';
            $priceincreaseordecreasearr = (isset($PostData['incrementdecrementprice']))?$PostData['incrementdecrementprice']:'';
            $pricepercentagearr = $PostData['pricepercentage'];

            $productpricehistoryidarr = $PostData['productpricehistoryid'];

            $insertpricehistorydata = $updatepricehistorydata = $productpricehistoryids = $updateproductprice = $updatechannelsalesprice = array();
           
            if(!empty($productpriceidarr)){
               
                foreach($productpriceidarr as $key=>$productpriceid){
                    
                    foreach($channelidarr as $channelid){
                        
                        $categoryid = (isset($categoryidsarr[$key]))?$categoryidsarr[$key]:'';
                        $actualprice = (isset($actualpricearr[$productpriceid][$channelid]))?$actualpricearr[$productpriceid][$channelid]:'';
                        $price = (isset($pricearr[$productpriceid][$channelid]))?$pricearr[$productpriceid][$channelid]:'';
                        $pricepercentage = (isset($pricepercentagearr[$productpriceid][$channelid]))?$pricepercentagearr[$productpriceid][$channelid]:'';
                        $pricetype = (isset($pricetypearr[$productpriceid][$channelid]))?0:1;
                        $priceincreaseordecrease = (isset($priceincreaseordecreasearr[$productpriceid][$channelid]))?1:0;

                        $productpricehistoryid = (!empty($productpricehistoryidarr[$productpriceid][$channelid]))?$productpricehistoryidarr[$productpriceid][$channelid]:'';

                       
                        if($categoryid!='' && !empty($price) && $pricepercentage!=''){
                            
                            if($productpricehistoryid != ''){

                                $updatepricehistorydata[]=array("id"=>$productpricehistoryid, 
                                                                "actualprice"=>$actualprice,
                                                                "price"=>$price,
                                                                "pricepercentage"=>$pricepercentage,
                                                                "pricetype"=>$pricetype,
                                                                "priceincreaseordecrease"=>$priceincreaseordecrease
                                                            );

                                $productpricehistoryids[] = $productpricehistoryid;

                            }else{

                                $insertpricehistorydata[]=array("pricehistoryid"=>$PriceHistoryId,
                                                                "channelid"=>$channelid,
                                                                "categoryid"=>$categoryid,
                                                                "productpriceid"=>$productpriceid,
                                                                "actualprice"=>$actualprice,
                                                                "price"=>$price,
                                                                "pricepercentage"=>$pricepercentage,
                                                                "pricetype"=>$pricetype,
                                                                "priceincreaseordecrease"=>$priceincreaseordecrease,
                                                                "createddate"=>$modifieddate
                                                            );

                            }
                        }
                    }
                }

                $deletearr = array_diff($ProductPriceHistoryId,$productpricehistoryids);
                if(!empty($deletearr)){
                    $this->Price_history->_table = tbl_productpricehistory;
                    foreach($deletearr as $rowid){
                        $this->Price_history->Delete(array("id"=>$rowid));
                    }
                }

                if(!empty($insertpricehistorydata)){
                    $this->Price_history->_table = tbl_productpricehistory;
                    $this->Price_history->Add_batch($insertpricehistorydata);
                }
                if(!empty($updatepricehistorydata)){
                    $this->Price_history->_table = tbl_productpricehistory;
                    $this->Price_history->Edit_batch($updatepricehistorydata, "id");
                }
            }
        }else if($type==1){
            
            $channelid = $PostData['channelid'];
            $memberidarr = $PostData['memberid'];
            $productpriceidarr = $PostData['productpriceid'];
            $categoryidsarr = $PostData['categoryids'];
            $actualpricearr = $PostData['price'];
            $pricearr = $PostData['amount'];
            $pricepercentagearr = $PostData['pricepercentage'];
            $pricetypearr = (isset($PostData['pricetype']))?$PostData['pricetype']:'';
            $priceincreaseordecreasearr = (isset($PostData['incrementdecrementprice']))?$PostData['incrementdecrementprice']:'';
            $membervariantpriceidarr = $PostData['membervariantpriceid'];
            $sellermemberidarr = $PostData['sellermemberid'];
            $memberproductpricehistoryidarr = $PostData['memberproductpricehistoryid'];

            $insertmemberpricehistorydata = $updatememberpricehistorydata = $memberproductpricehistoryids = array();
            if(!empty($productpriceidarr)){
                
                foreach($productpriceidarr as $key=>$productpriceid){
                    
                    $memberids = (isset($memberidarr[$productpriceid]))?$memberidarr[$productpriceid]:'';
                    $categoryid = (isset($categoryidsarr[$key]))?$categoryidsarr[$key]:'';
                   
                    if(!empty($memberids)){
                        foreach($memberids as $index=>$memberid){
                            
                            $sellermemberid = (isset($sellermemberidarr[$productpriceid][$memberid]))?$sellermemberidarr[$productpriceid][$memberid]:'';
                            $membervariantpriceid = (isset($membervariantpriceidarr[$productpriceid][$memberid]))?$membervariantpriceidarr[$productpriceid][$memberid]:'';
                            
                            $actualprice = (isset($actualpricearr[$productpriceid][$memberid]))?$actualpricearr[$productpriceid][$memberid]:'';
                            $price = (isset($pricearr[$productpriceid][$memberid]))?$pricearr[$productpriceid][$memberid]:'';
                            $pricepercentage = (isset($pricepercentagearr[$productpriceid][$memberid]))?$pricepercentagearr[$productpriceid][$memberid]:'';
                            $pricetype = (isset($pricetypearr[$productpriceid][$memberid]))?0:1;
                            $priceincreaseordecrease = (isset($priceincreaseordecreasearr[$productpriceid][$memberid]))?1:0;
                            $memberproductpricehistoryid = (isset($memberproductpricehistoryidarr[$productpriceid][$memberid]))?$memberproductpricehistoryidarr[$productpriceid][$memberid]:'';

                            if($categoryid!='' && !empty($price) && !empty($pricepercentage)){
                                if(isset($memberproductpricehistoryid) && $memberproductpricehistoryid != ''){
                                    
                                    $updatememberpricehistorydata[]=array(
                                        "id"=>$memberproductpricehistoryid,
                                        "actualprice"=>$actualprice,
                                        "price"=>$price,
                                        "pricepercentage"=>$pricepercentage,
                                        "pricetype"=>$pricetype,
                                        "priceincreaseordecrease"=>$priceincreaseordecrease
                                    );
                                    $memberproductpricehistoryids[] = $memberproductpricehistoryid;
                                }else{
                                    $insertmemberpricehistorydata[]=array(
                                        "pricehistoryid"=>$PriceHistoryId,
                                        "sellermemberid"=>$sellermemberid,
                                        "channelid"=>$channelid,
                                        "memberid"=>$memberid,
                                        "categoryid"=>$categoryid,
                                        "membervariantpriceid"=>$membervariantpriceid,
                                        "actualprice"=>$actualprice,
                                        "price"=>$price,
                                        "pricepercentage"=>$pricepercentage,
                                        "pricetype"=>$pricetype,
                                        "priceincreaseordecrease"=>$priceincreaseordecrease,
                                        "createddate"=>$modifieddate
                                    );

                                }
                            }
                        }
                    }
                }
                
                $deletearr = array_diff($ProductPriceHistoryId,$memberproductpricehistoryids);
                if(!empty($deletearr)){
                    $this->Price_history->_table = tbl_memberproductpricehistory;
                    foreach($deletearr as $rowid){
                        $this->Price_history->Delete(array("id"=>$rowid));
                    }
                }
                if(!empty($insertmemberpricehistorydata)){
                    $this->Price_history->_table = tbl_memberproductpricehistory;
                    $this->Price_history->Add_batch($insertmemberpricehistorydata);
                }
                if(!empty($updatememberpricehistorydata)){
                    $this->Price_history->_table = tbl_memberproductpricehistory;
                    $this->Price_history->Edit_batch($updatememberpricehistorydata, "id");
                }
            }
        }
        if($scheduleddate==""){
            $this->Price_history->updateAdminProductOrMemberProductPrice($PriceHistoryId);
        }
        if($this->viewData['submenuvisibility']['managelog'] == 1){
            $name = ($type==0?'admin product':Member_label.' product');
            $this->general_model->addActionLog(2,'Price History','Edit '.$name.' price history.');
        }
        echo 1;
    }

    public function delete_mul_price_history(){
       
        $PostData = $this->input->post();
	    $ids = explode(",",$PostData['ids']);

	    $count = 0;
	    $ADMINID = $this->session->userdata(base_url().'ADMINID');
	    foreach($ids as $row)
	    {
            $this->Price_history->_fields = "type";
            $this->Price_history->_where = array("id"=>$row);
            $rowdata = $this->Price_history->getRecordsById();
            
            if(!empty($rowdata)){
                if($rowdata['type']=0){

                    $this->Price_history->_table = tbl_productpricehistory;
                    $this->Price_history->Delete(array('pricehistoryid'=>$row));
                }else{
                    
                    $this->Price_history->_table = tbl_memberproductpricehistory;
                    $this->Price_history->Delete(array('pricehistoryid'=>$row));
                }

                if($this->viewData['submenuvisibility']['managelog'] == 1){
                    $name = ($rowdata['type']==0?'admin product':Member_label.' product');
                    $this->general_model->addActionLog(3,'Price History','Delete '.$name.' price history.');
                }
            }

            $this->Price_history->_table = tbl_pricehistory;
            $this->Price_history->Delete(array('id'=>$row));
	    }
	}
}