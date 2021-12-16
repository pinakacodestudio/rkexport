<script>
    var PRODUCT_PATH='<?=PRODUCT?>'; 
    var partialpayment = '<?php if(!empty($channelsetting)){ echo $channelsetting['partialpayment']; } ?>';
    var addressid = <?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['addressid']; }else{ echo "0"; } ?>;
    var shippingaddressid = <?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['shippingaddressid']; }else{ echo "0"; } ?>;
    var addordertype = <?php if(isset($ordertype) && $ordertype==1){ echo '1'; }else{ echo "0"; } ?>;
    var addquotationorder = <?php if(isset($addordertype) && $addordertype==1){ echo '1'; }else{ echo "0"; } ?>;
    var addpuchaseordertype = <?php if(isset($addpuchaseordertype) && $addpuchaseordertype==1){ echo '1'; }else{ echo "0"; } ?>;
    oldproductid = [];
    oldpriceid = [];
    oldtax = [];
    productdiscount = [];
    oldcombopriceid = [];
    oldprice = [];
    var EMIreceived = 0;
    var productoptionhtml = "";
    var salesproducthtml = "";
    <?php if(isset($ordertype) && $ordertype==1){
            foreach($productdata as $product){ ?>
                productoptionhtml+='<option data-pointsforbuyer="<?=$product['pointsforbuyer']?>" data-pointsforseller="<?=$product['pointsforseller']?>" value="<?php echo $product['id']; ?>"><?php echo $product['name']; ?></option>';
    <?php } } ?>
    var PRODUCT_DISCOUNT = '<?=PRODUCTDISCOUNT?>';
    var REWARDS_POINTS = '<?=REWARDSPOINTS?>';

    var GSTonDiscount = '<?php //if(isset($gstondiscount)){ echo $gstondiscount; } ?>';
    var globaldicountper = '<?php //if(isset($globaldiscountper)){ echo $globaldiscountper; } ?>';
    var globaldicountamount = '<?php //if(isset($globaldiscountamount)){ echo $globaldiscountamount; } ?>';
    var discountminamount = '<?php //if(isset($discountonbillminamount)){ echo $discountonbillminamount; }else{ echo -1; } ?>';
    
    var firstlevel = <?php if(isset($firstlevel) && $firstlevel==1 && isset($ordertype) && $ordertype==1){ echo '1'; }else{ echo "0"; } ?>;

    var approvestatus = <?php if(!empty($orderdata) && isset($orderdata['orderdetail']['approvestatus'])){ echo $orderdata['orderdetail']['approvestatus']; }else{ echo "0"; }?>;

    var extrachargeoptionhtml = "";
    <?php if(!isset($ordertype)){ 
        foreach($extrachargesdata as $extracharges){ ?>
        extrachargeoptionhtml+='<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>';
    <?php }
    }?>

    var EDITTAXRATE_SYSTEM = '<?=EDITTAXRATE?>';
    var EDITTAXRATE_CHANNEL = '<?php if(!empty($orderdata) && isset($orderdata['orderdetail']['memberedittaxrate'])){ echo $orderdata['orderdetail']['memberedittaxrate']; }?>';

    var OFFER = '<?=OFFER?>';
    var DEFAULT_COUNTRY_ID = '<?=DEFAULT_COUNTRY_ID?>';

    var advancepayment = '<?php if(!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==1 || $orderdata['orderdetail']['paymenttype']==3)){ echo $orderdata['transaction']['payableamount']; } ?>';
    var STOCK_MANAGE_BY = '<?=STOCK_MANAGE_BY?>';
</script>
<style> .mt-30{ margin-top: 30px; } 
    #offerproductsdata .popover-content{
        overflow-y: auto;
        max-height: 200px;
        color: #000000;
    }
    #offerproductsdata .popover.fade{
        left: 5px !important;
        max-width: 365px;
        width: 100%;
    }
    #offerproductsdata .table tr {
        background-color: #fcfcfc!important;   
    }
    .offers table td,.offers table th{
        padding: 5px !important;
    }
    .combopriceid .dropdown-menu{
        width: max-content;
    }
    .productid .dropdown-menu.open{
        right: unset;
    }
    .productid .dropdown-menu.inner{
        width: max-content;
        max-width: 300px;
    }
    .priceid .dropdown-menu{
        width: max-content;
    }
    .table > tbody > tr > td{
        padding: 6px 5px 6px 5px;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($orderdata) && !isset($addordertype)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($orderdata) && !isset($addordertype)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
                    <form class="form-horizontal" id="orderform" name="orderform">
                        <input type="hidden" name="ordersid" id="ordersid" value="<?php if(!empty($orderdata)){ echo $orderdata['orderdetail']['id']; } ?>">
                        <input type="hidden" name="quotationid" id="quotationid" value="<?php if(isset($quotationid)){ echo $quotationid; } ?>">
                        <input type="hidden" name="memberrewardpointhistoryid" id="memberrewardpointhistoryid" value="<?php if(!empty($orderdata) && isset($orderdata['orderdetail']['memberrewardpointhistoryid'])){ echo $orderdata['orderdetail']['memberrewardpointhistoryid']; } ?>">
                        <input type="hidden" name="sellermemberrewardpointhistoryid" id="sellermemberrewardpointhistoryid" value="<?php if(!empty($orderdata) && isset($orderdata['orderdetail']['memberrewardpointhistoryid'])){ echo $orderdata['orderdetail']['sellermemberrewardpointhistoryid']; } ?>">
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <input type="hidden" id="ordertype" name="ordertype" value="<?php if(isset($ordertype) && $ordertype==1){ echo "1"; }else{ echo "0"; } ?>">
                                <input type="hidden" id="oldmemberid" name="oldmemberid" value="<?php if(!empty($orderdata)){ echo $orderdata['orderdetail']['memberid']; } ?>">
                                <div class="col-sm-4" style="<?php if(isset($ordertype) && $ordertype==1){ echo "display:none;"; } ?>">
                                    <div class="form-group" id="member_div">
                                        <div class="col-sm-<?php if(isset($multiplememberchannel) && $multiplememberchannel==1){ echo "10  pr-n"; }else{ echo "12 pr-sm"; }?>">
                                            <label for="memberid" class="control-label">Select <?=Member_label?> <span class="mandatoryfield">*</span></label>
                                            <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(!empty($orderdata) && !isset($addordertype)){ echo "disabled"; } ?>>
                                                <option value="0">Select <?=Member_label?></option>
                                                <?php foreach($memberdata as $member){ ?>
                                                    <option data-minimumorderamount="<?php echo $member['minimumorderamount']; ?>" data-code="<?php echo $member['membercode']; ?>" data-billingid="<?=$member['billingaddressid']?>" data-shippingid="<?=$member['shippingaddressid']?>" value="<?php echo $member['id']; ?>" <?php if(!empty($orderdata['orderdetail'])){ if($orderdata['orderdetail']['memberid']==$member['id']){ echo "selected"; }} ?>><?php echo ucwords($member['name']); ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if(!isset($orderdata)){ ?>
                                                <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(3)"><i class="fa fa-plus"></i> Add New <?=Member_label?></a>
                                            <?php } ?>  
                                        </div>
                                        <?php if(isset($multiplememberchannel) && $multiplememberchannel==1){?>
                                        <div class="col-sm-2" style="padding-top: 28px !important;">
                                            <a href="javascript:void(0)" onclick="resetbuyerform()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Search Buyer <?=Member_label?>" data-toggle="modal" data-target="#addbuyerModal">search</i></a>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group" id="billingaddress_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="billingaddressid" class="control-label">Select Billing Address</label>
                                            <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Billing Address</option>
                                                <?php if(isset($billingaddress)){ foreach($billingaddress as $ba){ ?>
                                                <option value="<?php echo $ba['id']; ?>"><?php echo ucwords($ba['address']); ?></option>
                                                <?php }} ?>
                                            </select>
                                            <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(1)"><i class="fa fa-plus"></i> Add New Billing Address</a>
                                            <input type="hidden" name="billingaddress" id="billingaddress" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group" id="shippingaddress_div">
                                        <div class="col-sm-12 pl-sm">
                                            <label for="shippingaddressid" class="control-label">Select Shipping Address</label>
                                            <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Shipping Address</option>
                                                <?php if(isset($billingaddress)){ foreach($billingaddress as $sa){ ?>
                                                <option value="<?php echo $sa['id']; ?>"><?php echo ucwords($sa['address']); ?></option>
                                                <?php }} ?>
                                            </select>
                                            <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(2)"><i class="fa fa-plus"></i> Add New Shipping Address</a>
                                            <input type="hidden" name="shippingaddress" id="shippingaddress" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-n">
                                <div class="col-sm-3">
                                    <div class="form-group" id="orderid_div">
                                        <div class="col-sm-12 pr-sm">
                                            <label for="orderid" class="control-label">Order ID <span class="mandatoryfield">*</span></label>
                                            <input id="orderid" type="text" name="orderid" class="form-control" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['orderid']; }else if(!empty($orderid) || (isset($addordertype) && $addordertype==1)){ echo $orderid; } ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" id="orderdate_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="orderdate" class="control-label">Order Date <span class="mandatoryfield">*</span></label>
                                            <div class="input-group">
                                                <input id="orderdate" type="text" name="orderdate" value="<?php if(!empty($orderdata['orderdetail']) && $orderdata['orderdetail']['orderdate']!="0000-00-00"){ echo $this->general_model->displaydate($orderdata['orderdetail']['orderdate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                                <span class="btn btn-default datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" id="salesperson_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="salespersonid" class="control-label">Select Sales Person</label>
                                            <select id="salespersonid" name="salespersonid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Sales Person</option>
                                                <?php if(isset($employeedata)){ foreach($employeedata as $emp){ ?>
                                                    <option value="<?php echo $emp['id']; ?>" <?php if(!empty($orderdata['orderdetail']) && $orderdata['orderdetail']['salespersonid'] == $emp['id']){ echo "selected"; }?>><?php echo ucwords($emp['name']); ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" id="cashorbank_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="cashorbankid" class="control-label">Select Bank</label>
                                            <select id="cashorbankid" name="cashorbankid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Bank</option>
                                                <?php if(isset($cashorbankdata)){ foreach($cashorbankdata as $cashOrBank){
                                                if(strtolower($cashOrBank['bankname'])!='cash'){ ?>
                                                    <option value="<?php echo $cashOrBank['id']; ?>" <?php if(!empty($orderdata['orderdetail'])){ if($orderdata['orderdetail']['cashorbankid'] == $cashOrBank['id']) { echo "selected";} }else{ if($defaultbankdata == $cashOrBank['id']){ echo "selected"; } }?>><?php echo ucwords($cashOrBank['bankname']); ?></option>
                                                <?php }}} ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" id="productbarcode_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="productbarcode" class="control-label">Barcode or QR Code</label>
                                            <input id="productbarcode" class="form-control" name="productbarcode" onkeypress="return alphanumeric(event)" maxlength="30">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group pt-xl">
                                        <div class="col-sm-12 pl-sm">
                                            <button type="button" name="sbmtBarcode" id="sbmtBarcode" class="btn btn-primary btn-raised" onclick="checkBarcode()">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div id="orderproductdivs">
                            <input type="hidden" name="removeorderproductid" id="removeorderproductid">
                            <table id="orderproducttable" class="table table-hover table-bordered m-n">
                                <thead>
                                    <tr>
                                        <th class="width8">Serial No.</th>
                                        <th>Product Name <span class="mandatoryfield">*</span></th>
                                        <th class="width5">Select Variant <span class="mandatoryfield">*</span></th>
                                        <th class="width12">Price <span class="mandatoryfield">*</span> </th>
                                        <th class="width8">Qty <span class="mandatoryfield">*</span></th>
                                        <th class="width8" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; } ?>">Discount</th>
                                        <th class="text-right width8">Tax (%)</th>
                                        <th class="text-right width8">Amount (<?=CURRENCY_CODE?>)</th>
                                        <th class="width8">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(!empty($orderdata) && !empty($orderdata['orderproduct'])) { ?>
                                    
                                    <?php for ($i=0; $i < count($orderdata['orderproduct']); $i++) { ?>
                                        <tr class="countproducts" id="orderproductdiv<?=($i+1)?>">
                                            <td>
                                                <input type="hidden" name="orderproductsid[]" value="<?=$orderdata['orderproduct'][$i]['id']?>" id="orderproductsid<?=$i+1?>">
                                                <input type="hidden" name="producttax[]" value="<?=$orderdata['orderproduct'][$i]['tax']?>" id="producttax<?=$i+1?>">
                                                <input type="hidden" name="productrate[]" value="<?=$orderdata['orderproduct'][$i]['price']?>" id="productrate<?=$i+1?>">
                                                <input type="hidden" name="originalprice[]" value="<?=$orderdata['orderproduct'][$i]['originalprice']?>" id="originalprice<?=$i+1?>">
                                            
                                                <input type="hidden" name="uniqueproduct[]" value="<?=$orderdata['orderproduct'][$i]['productid']."_".$orderdata['orderproduct'][$i]['priceid']."_".$orderdata['orderproduct'][$i]['originalprice']?>" id="uniqueproduct<?=$i+1?>">

                                                <input type="hidden" name="offerid[]" id="offerid<?=$i+1?>">
                                                <input type="hidden" name="offerproductid[]" id="offerproductid<?=$i+1?>">
                                                <input type="hidden" name="brandoffer[]" id="brandoffer<?=$i+1?>">
                                                <input type="hidden" name="referencetype[]" id="referencetype<?=$i+1?>" value="<?=$orderdata['orderproduct'][$i]['referencetype']?>">
                                                <textarea style="display:none;" id="fifoproducts<?=$i+1?>"></textarea>
                                                <textarea style="display:none;" name="finalfifoproducts[]" id="finalfifoproducts<?=$i+1?>"><?php if(!empty($orderdata['orderproduct'][$i]['fifoproducts'])){ echo $orderdata['orderproduct'][$i]['fifoproducts']; } ?></textarea>

                                                <div class="form-group" id="serialno<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <input id="serialno<?=($i+1)?>" type="text" name="serialno[]" value="<?=$orderdata['orderproduct'][$i]['serialno']?>" class="form-control">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="product<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <select id="productid<?=($i+1)?>" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                            <option value="0">Select Product</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12 dropdown trigger-infobar" id="trigger-infobar<?=($i+1)?>" style="display:none;">
                                                        <a class="dropdown-toggle btn-available-offers" data-toggle='dropdown' href="javascript:void(0)"><i class="material-icons" style="font-size: 20px;">add_circle_outline</i>&nbsp;Available Offers</a>
                                                    </div>
                                                    <?php if(REWARDSPOINTS==1 || (isset($orderdata['orderproduct'][$i]['pointsforbuyer']) && $orderdata['orderproduct'][$i]['pointsforbuyer']!=0)){ ?>
                                                        <div class="col-sm-12 memberpointsdiv" id="memberpointsdiv<?=($i+1)?>" style="<?php if(isset($orderdata['orderproduct'][$i]['pointsforbuyer']) && $orderdata['orderproduct'][$i]['pointsforbuyer']!=0){ echo "display:block;"; }else{ echo "display:none;"; } ?>">
                                                            <input type="hidden" name="inputpointsforbuyer[]" id="inputpointsforbuyer<?=($i+1)?>" class="inputpointsforbuyer" value="<?php if(isset($orderdata['orderproduct'][$i]['pointsforbuyer'])) { echo $orderdata['orderproduct'][$i]['pointsforbuyer']; } ?>" div-id="<?=($i+1)?>">
                                                            <input type="hidden" name="pointsforbuyerwithoutmultiply[]" id="pointsforbuyerwithoutmultiply<?=($i+1)?>" class="pointsforbuyerwithoutmultiply" div-id="<?=($i+1)?>">
                                                            <input type="hidden" name="inputpointsforseller[]" id="inputpointsforseller<?=($i+1)?>" class="inputpointsforseller" value="<?php if(isset($orderdata['orderproduct'][$i]['pointsforbuyer'])){ echo $orderdata['orderproduct'][$i]['pointsforseller']; } ?>" div-id="<?=($i+1)?>">
                                                            <input type="hidden" name="pointsforsellerwithoutmultiply[]" id="pointsforsellerwithoutmultiply<?=($i+1)?>" class="pointsforsellerwithoutmultiply" div-id="<?=($i+1)?>">
                                                            <input type="hidden" name="pointspriority[]" id="pointspriority<?=($i+1)?>" class="pointspriority" div-id="<?=($i+1)?>">
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                
                                            </td>
                                            <td>
                                                <div class="form-group" id="price<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <select id="priceid<?=($i+1)?>" name="priceid[]" class="selectpicker form-control priceid" data-width="90%" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                            <option value="">Select Variant</option>
                                                        </select>
                                                        <?php
                                                        if((isset($addordertype) && $addordertype==1) || (!isset($addordertype))){
                                                            $label = (!isset($addordertype) || isset($isduplicate))?"Order":"Quotation";
                                                            $oldproductrate = $orderdata['orderproduct'][$i]['originalprice'];
                                                        ?>

                                                            <div class="form-group m-n p-n" id="applyoldprice<?=($i+1)?>_div">
                                                                <div class="col-sm-12">
                                                                    <div class="checkbox pt-n pl-xs text-left">
                                                                        <input id="applyoldprice<?=($i+1)?>" type="checkbox" value="0" class="checkradios applyoldprice" checked>
                                                                        <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Set Old <?=$label?> Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$oldproductrate?></span></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="comboprice<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <select id="combopriceid<?=($i+1)?>" name="combopriceid[]" class="selectpicker form-control combopriceid" data-width="150px" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                            <option value="">Price</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="actualprice<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <label for="actualprice<?=($i+1)?>" class="control-label">Rate (<?=CURRENCY_CODE?>)</label> 
                                                        <input type="text" class="form-control actualprice text-right" id="actualprice<?=($i+1)?>" name="actualprice[]" value="<?=$orderdata['orderproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value, 8);" style="display: block;" div-id="<?=($i+1)?>">
                                                        <input type="hidden" id="ordproductstock<?=($i+1)?>" name="ordproductstock[]" class="ordproductstock" value="0">
                                                    </div>
                                                    <div class="col-sm-12 text-right displaystockmessage" id="displaystockmessage<?=($i+1)?>"></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="qty<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$orderdata['orderproduct'][$i]['quantity']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" div-id="<?=($i+1)?>">
                                                        <input type="hidden" value="0" id="purchaseproductqty<?=$i+1?>">
                                                    </div>
                                                </div>
                                            </td>
                                            <td style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; } ?>">
                                                <div class="form-group" id="discount<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <label for="discount<?=($i+1)?>" class="control-label">Dis. (%)</label>
                                                        <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$orderdata['orderproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>
                                                        <input type="hidden" value="<?=$orderdata['orderproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="discountinrs<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <label for="discountinrs<?=($i+1)?>" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                        <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="tax<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?=$orderdata['orderproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                                        if($orderdata['orderdetail']['memberedittaxrate']==1 && EDITTAXRATE==1){ 
                                                            echo ""; 
                                                        }else{ 
                                                            echo "readonly"; 
                                                        }?>>	
                                                        <input type="hidden" value="<?=$orderdata['orderproduct'][$i]['tax']?>" id="ordertax<?=$i+1?>">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="amount<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" div-id="<?=($i+1)?>" readonly>
                                                        <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">	
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group pt-sm">
                                                    <div class="col-sm-12 pr-n">
                                                        <?php if($i==0){?>
                                                            <?php if(count($orderdata['orderproduct'])>1){ ?>
                                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                            <?php }else { ?>
                                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                            <?php } ?>

                                                        <?php }else if($i!=0) { ?>
                                                            <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                        <?php } ?>
                                                        <button type="button" class="btn btn-default btn-raised btn-sm add_remove_btn_product" onclick="removeproduct(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                        <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>

                                                <script type="text/javascript">
                                                    $(document).ready(function() {
                                                        oldproductid.push(<?=$orderdata['orderproduct'][$i]['productid']?>);
                                                        oldpriceid.push(<?=$orderdata['orderproduct'][$i]['priceid']?>);
                                                        oldtax.push(<?=$orderdata['orderproduct'][$i]['tax']?>);
                                                        productdiscount.push(<?=$orderdata['orderproduct'][$i]['discount']?>);
                                                        oldcombopriceid.push(<?=$orderdata['orderproduct'][$i]['referenceid']?>);
                                                        oldprice.push(<?=$orderdata['orderproduct'][$i]['originalprice']?>);
                                                        
                                                        $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                                                        if(addpuchaseordertype==0){
                                                            getproduct(<?=$i+1?>);
                                                        }
                                                        if(REWARDS_POINTS==1 || ACTION==1){
                                                            getProductRewardpoints(<?=$i+1?>);
                                                        }
                                                        getproductprice(<?=$i+1?>);
                                                        getmultiplepricebypriceid(<?=$i+1?>);
                                                        calculatediscount(<?=$i+1?>);
                                                        changeproductamount(<?=$i+1?>);
                                                        getofferproducts(<?=$i+1?>);
                                                        geProductFIFOStock(<?=$i+1?>);
                                                    });
                                                </script>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php }else{ ?>
                                    
                                    <tr class="countproducts" id="orderproductdiv1">
                                        <td>
                                            <input type="hidden" name="producttax[]" id="producttax1">
                                            <input type="hidden" name="productrate[]" id="productrate1">
                                            <input type="hidden" name="originalprice[]" id="originalprice1">
                                            <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                            <input type="hidden" name="offerid[]" id="offerid1">
                                            <input type="hidden" name="offerproductid[]" id="offerproductid1">
                                            <input type="hidden" name="brandoffer[]" id="brandoffer1">
                                            <input type="hidden" name="referencetype[]" id="referencetype1">
                                            <textarea style="display:none;" id="fifoproducts1"></textarea>
                                            <textarea style="display:none;" name="finalfifoproducts[]" id="finalfifoproducts1"></textarea>
                                            <div class="form-group" id="serialno1_div">
                                                <div class="col-sm-12">
                                                    <input id="serialno1" type="text" name="serialno[]" class="form-control">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" id="product1_div">
                                                <div class="col-sm-12">
                                                    <select id="productid1" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                        <option value="0">Select Product</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-12 dropdown trigger-infobar" id="trigger-infobar1" style="display:none;">
                                                    <a class="dropdown-toggle btn-available-offers" data-toggle='dropdown' href="javascript:void(0)"><i class="material-icons" style="font-size: 20px;">add_circle_outline</i>&nbsp;Available Offers</a>
                                                </div>
                                                <?php if(REWARDSPOINTS==1){ ?>
                                                    <div class="col-sm-12 memberpointsdiv" id="memberpointsdiv1" style="display:none;">
                                                        <!-- <label class="control-label">Points for Buyer : <span id="pointsforbuyer1" class="pointsforbuyer">0</span></label> -->
                                                        <input type="hidden" name="inputpointsforbuyer[]" id="inputpointsforbuyer1" class="inputpointsforbuyer" div-id="1">
                                                        <input type="hidden" name="pointsforbuyerwithoutmultiply[]" id="pointsforbuyerwithoutmultiply1" class="pointsforbuyerwithoutmultiply" div-id="1">
                                                        <input type="hidden" name="inputpointsforseller[]" id="inputpointsforseller1" class="inputpointsforseller" div-id="1">
                                                        <input type="hidden" name="pointsforsellerwithoutmultiply[]" id="pointsforsellerwithoutmultiply1" class="pointsforsellerwithoutmultiply" div-id="1">
                                                        <input type="hidden" name="pointspriority[]" id="pointspriority1" class="pointspriority" div-id="1">
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            
                                        </td>
                                        <td>
                                            <div class="form-group" id="price1_div">
                                                <div class="col-md-12">
                                                    <select id="priceid1" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                        <option value="">Select Variant</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" id="comboprice1_div">
                                                <div class="col-sm-12">
                                                    <select id="combopriceid1" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                        <option value="">Price</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" id="actualprice1_div">
                                                <div class="col-sm-12">
                                                    <label for="actualprice1" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                    <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="1">
                                                    <input type="hidden" id="ordproductstock1" name="ordproductstock[]" class="ordproductstock" value="0">
                                                </div>
                                                <div class="col-sm-12 text-right displaystockmessage" id="displaystockmessage1"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" id="qty1_div">
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                                    <input type="hidden" value="0" id="purchaseproductqty1">
                                                </div>
                                            </div>
                                        </td>
                                        <td style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; } ?>">
                                            <div class="form-group" id="discount1_div">
                                                <div class="col-md-12">
                                                    <label for="discount1" class="control-label">Dis. (%)</label>
                                                    <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                                    <input type="hidden" value="" id="orderdiscount1">
                                                </div>
                                            </div>
                                            <div class="form-group" id="discountinrs1_div">
                                                <div class="col-md-12">
                                                    <label for="discountinrs1" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                    <input type="text" class="form-control discountinrs" id="discountinrs1" name="discountinrs[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" id="tax1_div">
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control text-right tax" id="tax1" name="tax[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" readonly>	
                                                    <input type="hidden" value="" id="ordertax1">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group" id="amount1_div">
                                                <div class="col-sm-12">
                                                    <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value="" div-id="1" readonly>
                                                    <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group pt-sm">
                                                <div class="col-sm-12 pr-n">
                                                    <button type="button" class="btn btn-default btn-raised add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-default btn-raised add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                        
                                    <!--<div class="row countproducts" id="orderproductdiv1">
                                        <input type="hidden" name="producttax[]" id="producttax1">
                                        <input type="hidden" name="productrate[]" id="productrate1">
                                        <input type="hidden" name="originalprice[]" id="originalprice1">
                                        <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                        <input type="hidden" name="offerid[]" id="offerid1">
                                        <input type="hidden" name="offerproductid[]" id="offerproductid1">
                                        <input type="hidden" name="brandoffer[]" id="brandoffer1">
                                        <input type="hidden" name="referencetype[]" id="referencetype1">
                                        <textarea style="display:none;" id="fifoproducts1"></textarea>
                                        <textarea style="display:none;" name="finalfifoproducts[]" id="finalfifoproducts1"></textarea>

                                        <div class="div col-md-12 pl-sm pr-sm">
                                            <div class="col-sm-1">
                                                <div class="form-group" id="serialno1_div">
                                                    <div class="col-sm-12 pl-sm pr-xs">
                                                        <label for="serialno1" class="control-label">Serial No.</label>
                                                        <input id="serialno1" type="text" name="serialno[]" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group" id="product1_div">
                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                        <label for="productid1" class="control-label">Product Name <span class="mandatoryfield">*</span></label>
                                                        <select id="productid1" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                            <option value="0">Select Product</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-12 dropdown trigger-infobar" id="trigger-infobar1" style="display:none;">
                                                        <a class="dropdown-toggle btn-available-offers" data-toggle='dropdown' href="javascript:void(0)"><i class="material-icons" style="font-size: 20px;">add_circle_outline</i>&nbsp;Available Offers</a>
                                                    </div>
                                                    <?php if(REWARDSPOINTS==1){ ?>
                                                        <div class="col-sm-12 memberpointsdiv" id="memberpointsdiv1" style="display:none;">
                                                            <input type="hidden" name="inputpointsforbuyer[]" id="inputpointsforbuyer1" class="inputpointsforbuyer" div-id="1">
                                                            <input type="hidden" name="pointsforbuyerwithoutmultiply[]" id="pointsforbuyerwithoutmultiply1" class="pointsforbuyerwithoutmultiply" div-id="1">
                                                            <input type="hidden" name="inputpointsforseller[]" id="inputpointsforseller1" class="inputpointsforseller" div-id="1">
                                                            <input type="hidden" name="pointsforsellerwithoutmultiply[]" id="pointsforsellerwithoutmultiply1" class="pointsforsellerwithoutmultiply" div-id="1">
                                                            <input type="hidden" name="pointspriority[]" id="pointspriority1" class="pointspriority" div-id="1">
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group" id="price1_div">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="priceid1" class="control-label">Select Variant <span class="mandatoryfield">*</span></label>
                                                        <select id="priceid1" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                            <option value="">Select Variant</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <div class="form-group" id="comboprice1_div">
                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                        <label for="combopriceid1" class="control-label">Select Price<span class="mandatoryfield">*</span></label>
                                                        <select id="combopriceid1" name="combopriceid[]" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                            <option value="">Price</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <div class="form-group" id="qty1_div">
                                                    <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="qty1" class="control-label">Qty <span class="mandatoryfield">*</span></label>
                                                        <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                                        <input type="hidden" value="0" id="purchaseproductqty1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1">
                                                <div class="form-group" id="actualprice1_div">
                                                    <div class="col-sm-12 pl-xs pr-xs">
                                                        <label for="actualprice1" class="control-label">Price (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></label>
                                                        <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="1">
                                                        <input type="hidden" id="ordproductstock1" name="ordproductstock[]" class="ordproductstock" value="0">
                                                    </div>
                                                    <div class="col-sm-12 text-center p-n displaystockmessage" id="displaystockmessage1"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 pl-xs pr-xs form-group ml-n mr-n" id="discount1_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>"> 
                                                <label for="discount1" class="control-label">Dis. (%)</label>
                                                <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                                <input type="hidden" value="" id="orderdiscount1">
                                            </div>
                                            <div class="col-md-1 pl-xs pr-sm form-group ml-n mr-n" id="discountinrs1_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>"> 
                                                <label for="discountinrs1" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                <input type="text" class="form-control discountinrs" id="discountinrs1" name="discountinrs[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                            </div>
                                            <div class="col-md-1 pl-sm pr-xs form-group ml-n mr-n" id="tax1_div"> 
                                                <label for="tax1" class="control-label">Tax (%)</label>
                                                <input type="text" class="form-control text-right tax" id="tax1" name="tax[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" readonly>	
                                                <input type="hidden" value="" id="ordertax1">
                                            </div>
                                        </div>
                                        <div class="div col-md-12 pl-sm pr-sm">
                                            
                                            <div class="col-md-1 pl-sm pr-xs form-group ml-n mr-n" id="amount1_div">
                                                <label for="amount1" class="control-label">Amount (<?=CURRENCY_CODE?>)</label>
                                                <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value="" div-id="1" readonly>
                                                <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                                <span class="material-input"></span>
                                            </div>

                                            <div class="col-md-1 form-group m-n p-sm mt-md pt-md">	
                                                <button type="button" class="btn btn-default btn-raised add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-default btn-raised add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>-->
                                <?php } ?>
                            </tbody>
                        </table>
                        </div>
                        <div class="row m-n">
                            <div class="col-md-12 p-n" id="displayofferproductsdata" style="display:<?=(!empty($orderdata['orderofferproduct']))?'block':'none'?>;">
                                <div class="col-md-12 p-n">
                                    <hr>
                                </div>
                                <div class="row panel-heading">
                                    <h2>Offer Products</h2>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">Product Name</th>
                                                    <th rowspan="2">Variant</th>
                                                    <th rowspan="2" class="text-right">Qty.</th>
                                                    <th rowspan="2" class="text-right">Tax (%)</th>
                                                    <th class="text-right">Discount (%)</th>
                                                    <th rowspan="2" class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                                                </tr>
                                                <tr>
                                                    <th class="text-right">Amt. (<?=CURRENCY_CODE?>)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if(!empty($orderdata['orderofferproduct'])){
                                                    $combination = array();
                                                    foreach($orderdata['orderofferproduct'] as $index => $orderofferproduct){

                                                        $pproduct = array();
                                                        $purchaseproducts =  explode("$",$orderofferproduct['purchaseproducts']);
                                                        if(count($purchaseproducts) > 0){
                                                            foreach($purchaseproducts as $k => $pp){ 
                                                                $product =  explode("|",$pp);
                                                                $pproduct[] = array($product[0],$product[1],$product[2]);

                                                                
                                                            }
                                                        }
                                                        $combination[] = array(array(
                                                            "offerproductcombinationid"=>$orderofferproduct['offerproductcombinationid'],
                                                            "ofpid"=>$orderofferproduct['productid'],
                                                            "ofpriceid"=>$orderofferproduct['priceid'],
                                                            "ofqty"=>$orderofferproduct['offerquantity'],
                                                            "multiplication"=>$orderofferproduct['multiplication'],
                                                            "ofdisctype"=>$orderofferproduct['discounttype'],
                                                            "ofdisc"=>$orderofferproduct['discountvalue'],
                                                            "oftax"=>$orderofferproduct['tax'],
                                                            "ofprice"=>$orderofferproduct['originalprice'],
                                                            "offerminimumbillamount"=>$orderofferproduct['minimumbillamount'],
                                                            "offertype"=>$orderofferproduct['offertype'],
                                                            "minimumpurchaseamount"=>$orderofferproduct['minimumpurchaseamount'],
                                                            "purchaseproducts"=>$pproduct,
                                                            "appliedpriceid"=>$orderofferproduct['appliedpriceid']
                                                        ));

                                                        if($orderofferproduct['discounttype']==0){
                                                            $discountamount = $orderofferproduct['discountvalue'];
                                                        }else{
                                                            $discountamount = (($orderofferproduct['originalprice']*$orderofferproduct['quantity'])*$orderofferproduct['discount'])/100;
                                                        }
                                                        $offeramount = ($orderofferproduct['originalprice']*$orderofferproduct['quantity']) - $discountamount;
                                                        
                                                        $producttaxamount = ($offeramount * $orderofferproduct['tax'] / (100 + $orderofferproduct['tax']));
                                                        
                                                        if($offeramount <= 0){
                                                            $offeramount = 0;
                                                        }
                                                ?>
                                                    <tr class="postcountoffers offerproducts_<?=$orderofferproduct['offerproductcombinationid']?>" data-priceid="<?=$orderofferproduct['appliedpriceid']?>">
                                                        <td rowspan="2"><?=$orderofferproduct['name']?>
                                                            <input type="hidden" name="orderproducttableid[]" id="orderproducttableid<?=($index+1)?>" value="<?=$orderofferproduct['id']?>">
                                                            <input type="hidden" name="postofferproducttableid[]" id="postofferproducttableid<?=($index+1)?>" value="<?=$orderofferproduct['offerproductid']?>">
                                                            <input type="hidden" name="postofferproductid[]" id="postofferproductid<?=($index+1)?>" value="<?=$orderofferproduct['productid']?>">
                                                            <input type="hidden" name="appliedpriceid[]" id="appliedpriceid<?=($index+1)?>" value="<?=$orderofferproduct['appliedpriceid']?>">
                                                            <input type="hidden" name="offerproductcombinationid[]" id="offerproductcombinationid<?=($index+1)?>" value="<?=$orderofferproduct['offerproductcombinationid']?>">
                                                            <input type="hidden" name="postofferpriceid[]" id="postofferpriceid<?=($index+1)?>" value="<?=$orderofferproduct['priceid']?>">
                                                            <input type="hidden" name="postofferproductrate[]" id="postofferproductrate<?=($index+1)?>" value="<?=$orderofferproduct['price']?>">
                                                            <input type="hidden" name="postofferoriginalprice[]" id="postofferoriginalprice<?=($index+1)?>" value="<?=$orderofferproduct['originalprice']?>">
                                                            <input type="hidden" name="postofferquantity[]" id="postofferquantity<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>" value="<?=$orderofferproduct['quantity']?>">
                                                            <input type="hidden" name="postoffertax[]" id="postoffertax<?=($index+1)?>" value="<?=$orderofferproduct['tax']?>">
                                                            <input type="hidden" name="postofferdiscountper[]" id="postofferdiscountper<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>" value="<?=$orderofferproduct['discount']?>">
                                                            <input type="hidden" name="postofferproducttaxamount[]" id="postofferproducttaxamount<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>" value="<?=$producttaxamount?>">
                                                            <input type="hidden" name="postofferamount[]" id="postofferamount<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>" value="<?=number_format($offeramount,2,'.','')?>">
                                                        </td>
                                                        <td rowspan="2"><?=$orderofferproduct['originalprice'].' '.$orderofferproduct['variantname']?></td>
                                                        <td rowspan="2" class="text-right"><span id="postofferqtyspan<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>"><?=$orderofferproduct['quantity']?></span></td>
                                                        <td rowspan="2" class="text-right"><?=$orderofferproduct['tax']?> </td>
                                                        <td class="text-right"><span id="postofferdiscountperspan<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>"><?=$orderofferproduct['discount']?></span></td>
                                                        <td rowspan="2" class="text-right"><span id="postofferamountspan<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>"><?=numberFormat($offeramount,2,',')?></span></td>
                                                    </tr>
                                                    <tr class="postcountoffers offerproducts_<?=$orderofferproduct['offerproductcombinationid']?>" data-priceid="<?=$orderofferproduct['appliedpriceid']?>">
                                                        <td class="text-right"><span id="postofferdiscountspan<?=$orderofferproduct['offerproductcombinationid']?>_<?=$orderofferproduct['priceid']?>">
                                                        <?php echo numberFormat($discountamount,2,',');?></span></td>
                                                    </tr>
                                                    <?php } } ?>
                                            </tbody>
                                        </table>
                                        <div id="offerproductdetails" style="display:none">
                                            <?php
                                                if(!empty($combination)){
                                                    foreach($combination as $index => $combinationdata){
                                                ?>
                                                <div class="offerproductdetails" id="offerproductdetails<?=$combinationdata[0]['offerproductcombinationid']?>" data-priceid="<?=$combinationdata[0]['appliedpriceid']?>"><?=json_encode($combinationdata)?></div>
                                                <?php } } ?>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="col-md-3 p-n">
                                    <div class="col-md-12 p-n">
                                        <div class="form-group  ml-n mr-n is-empty" id="discountcoupon_div">
                                            <div class="col-md-10">
                                                <label for="discountcoupon" class="control-label">Apply Coupon</label>
                                                <div>
                                                    <div class="col-sm-10" style="padding: 0px;">
                                                        <input type="text" class="form-control discountcoupon" id="discountcoupon" name="discountcoupon" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['couponcode']; }?>">	
                                                    </div>
                                                    <div class="col-sm-2 pr-n mt-xs">
                                                        <?php if(!empty($orderdata['orderdetail']) && $orderdata['orderdetail']['couponcode']!='' && $orderdata['orderdetail']['couponcodeamount']!='' &&  $orderdata['orderdetail']['couponcodeamount']!='0'){?>
                                                            <button type="button" class="btn btn-danger btn-raised" id="applycoupon" name="applycoupon" onclick="removecoupon()">Remove</button>
                                                        <?php }else{ ?>
                                                            <button type="button" class="btn btn-success btn-raised" id="applycoupon" name="applycoupon" onclick="applycouponcode()">Apply</button>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 p-n">
                                        <?php 
                                            $discountpercentage =  $discountamount = $discountminamount = "";
                                            /* if(isset($discountonbillminamount) && $discountonbillminamount>=0){
                                                if(isset($globaldiscountper)){
                                                    $discountpercentage = $globaldiscountper;    
                                                }
                                                if(isset($globaldiscountamount)){
                                                    $discountamount = $globaldiscountamount;    
                                                }
                                                $discountminamount = $discountonbillminamount;    
                                            } */
                                            
                                            if(!empty($orderdata['orderdetail'])){ 
                                                if($orderdata['orderdetail']['globaldiscount'] > 0){
                                                    /* if(isset($gstondiscount) && $gstondiscount==1){ 
                                                        $discountper = $orderdata['orderdetail']['globaldiscount']*100/ ($orderdata['orderdetail']['amount']);
                                                    }else{
                                                        $discountper = $orderdata['orderdetail']['globaldiscount']*100/ ($orderdata['orderdetail']['amount'] + $orderdata['orderdetail']['taxamount']);
                                                    }

                                                    if($discountper!=0){
                                                        $discountpercentage = number_format($discountper,2); 
                                                    } */
                                                    $discountper = $orderdata['orderdetail']['globaldiscount']*100/ ($orderdata['orderdetail']['amount'] + $orderdata['orderdetail']['taxamount']);
                                                    $discountpercentage = number_format($discountper,2); 

                                                    $discountamnt = $orderdata['orderdetail']['globaldiscount']; 
                                                    if($discountamnt!=0 || $discountamnt!=0.00){
                                                        $discountamount = number_format($discountamnt,2,'.',''); 
                                                    }
                                                }else{
                                                    $discountpercentage = $discountamount = '';
                                                }
                                                if($orderdata['orderdetail']['couponcodeamount']==0){
                                                ?>
                                                <script type="text/javascript">

                                                    globaldicountper = "<?=$discountpercentage?>"; 
                                                    globaldicountamount = "<?=$discountamount?>"; 
                                                </script>
                                            <?php }else{
                                                $discountpercentage =  $discountamount = "";
                                                }
                                            } ?>
                                        
                                        <div class="col-sm-6 pr-sm">
                                            <div class="form-group  ml-n mr-n text-right">
                                                <label for="overalldiscountpercent" class="control-label">Discount (%)</label>
                                                <input type="text" class="form-control text-right overalldiscountpercent" id="overalldiscountpercent" name="overalldiscountpercent" value="<?php if(!empty($orderdata['orderdetail'])){ echo $discountpercentage; } ?>" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                                <span class="material-input"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 pl-sm">
                                            <div class="form-group ml-n mr-n text-right">
                                                <label for="overalldiscountamount" class="control-label">Discount (<?=CURRENCY_CODE?>)</label>
                                                <input type="text" class="form-control text-right overalldiscountamount" id="overalldiscountamount" name="overalldiscountamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $discountamount; } ?>" onkeypress="return decimal_number_validation(event,this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                                <span class="material-input"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <?php if(REWARDSPOINTS==1){ ?>
                                            <div class="form-group" id="redeem_div">
                                                <div class="col-md-12 text-right">
                                                    <label for="redeem" class="control-label">Redeem Points (Points : <span id="displayrewardpoints">0</span>)</label>
                                                    <input type="text" class="form-control text-right" id="redeem" name="redeem" value="<?php
                                                    if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['redeempoints']; }?>" onkeypress="return isNumber(event)" maxlength="4" <?php
                                                    if(!empty($orderdata['orderdetail'])){ echo "readonly"; }?>>

                                                    <input type="hidden" id="minimumpointsonredeem" name="minimumpointsonredeem">
                                                    <input type="hidden" id="minimumpointsonredeemfororder" name="minimumpointsonredeemfororder">
                                                    <input type="hidden" id="mimimumpurchaseorderamountforredeem" name="mimimumpurchaseorderamountforredeem">
                                                    <input type="hidden" id="redeempointsforbuyer" name="redeempointsforbuyer" value="<?php if(isset($ordertype) && $ordertype==1){ echo $countpointsforbuyer['rewardpoint']; }?>">
                                                </div>
                                            </div>
                                            <p class="mandatoryfield" id="notesredeem"></p>
                                        <?php } ?>
                                    </div>
                                    <div class="col-md-12 p-n">
                                       
                                        
                                    </div>
                                </div>
                                <div class="col-md-9 pull-right p-n">
                                    <div class="col-md-6 pr-xs">
                                        <table class="table table-bordered table-striped" cellspacing="0" width="100%" style="border: 1px solid #e8e8e8;" id="gstsummarytable">
                                            <thead>
                                                <tr>                  
                                                    <th class="text-center">GST Summary</th>
                                                    <th class="text-center">Assessable Amount (<?=CURRENCY_CODE?>)</th>
                                                    <th class="text-center">GST Amount (<?=CURRENCY_CODE?>)</th>
                                                </tr>  
                                            </thead>
                                            <tbody>
                                                <tr>                  
                                                    <th>Product Total</th>
                                                    <td class="text-right" width="20%">
                                                        <span id="productassesbaleamount">0.00</span>
                                                    </td>
                                                    <td class="text-right" width="20%">
                                                        <span id="productgstamount">0.00</span>
                                                    </td>
                                                </tr>
                                                <?php if(!isset($ordertype)){ ?>
                                                <tr>                    
                                                    <th>Extra Charges Total</th>
                                                    <td class="text-right"><span id="chargestotalassesbaleamount">0.00</span></td>
                                                    <td class="text-right"><span id="chargestotalgstamount">0.00</span></td>
                                                </tr>
                                                <?php } ?>
                                                <tr>                    
                                                    <th></th>
                                                    <th class="text-right">
                                                        <span id="producttotalassesbaleamount">0.00</span>
                                                        <input type="hidden" id="totalgrossamount" name="totalgrossamount" value="">
                                                    </th>
                                                    <th class="text-right">
                                                        <span id="producttotalgstamount">0.00</span>
                                                        <input type="hidden" id="inputtotaltaxamount" name="inputtotaltaxamount" value="">
                                                    </th>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-md-6 pl-xs">
                                        <input type="hidden" name="removeextrachargemappingid" id="removeextrachargemappingid">
                                        <table id="example" class="table table-bordered table-striped" cellspacing="0" width="100%" style="border: 1px solid #e8e8e8;">
                                            <tbody>
                                                <tr>                  
                                                    <th colspan="2" class="text-center">Order Summary (<?=CURRENCY_CODE?>)</th>
                                                </tr>  
                                                <tr>                  
                                                    <th>Total Of Product</th>
                                                    <td class="text-right" width="30%">
                                                        <span id="grossamount"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['amount']; }else{ echo "0.00"; }?></span>
                                                        <input type="hidden" id="inputgrossamount" name="grossamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['amount']; } ?>">
                                                    </td>
                                                </tr>
                                                <tr id="discountrow" style="display:none">                  
                                                    <th>Discount (<span id="discountpercentage"><?php if(!empty($orderdata['ordorderdetailrdetail'])){ echo number_format($orderdata['orderdetail']['globaldiscount']*100/$orderdata['orderdetail']['quotationamount'],2); }else{ echo "0"; }?></span>%)
                                                    </th>
                                                    <td class="text-right">
                                                        <span id="discountamount"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['globaldiscount']; }else{ echo "0.00"; }?></span>
                                                    </td>
                                                </tr>
                                                <tr id="couponrow" style="display:none">                    
                                                    <th>Coupon Discount</th>
                                                    <td class="text-right">
                                                        <span id="coupondiscountamount"><?php if(!empty($orderdata['orderdetail'])){ echo number_format($orderdata['orderdetail']['couponcodeamount'],2); }else{ echo "0.00"; }?></span>
                                                        <input type="hidden" id="couponamount" name="couponamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['couponcodeamount']; }?>">
                                                    </td>
                                                </tr> 
                                                <?php if(REWARDSPOINTS==1 || !empty($orderdata['orderdetail'])){ ?>
                                                <tr id="trconversationrate" style="display:none">                    
                                                    <th>Redeem Amount (<span id="conversationrate">0</span>)</th>
                                                    <td class="text-right">
                                                        <span id="conversationrateamount">0.00</span>
                                                        <input type="hidden" id="inputconversationrateamount" name="inputconversationrateamount" value="">
                                                        <input type="hidden" id="totalpointsforbuyer" name="totalpointsforbuyer" value="">
                                                        <input type="hidden" id="totalpointsforseller" name="totalpointsforseller" value="">
                                                        <input type="hidden" id="totalredeempointsforbuyer" name="totalredeempointsforbuyer" value="">
                                                        
                                                        <input type="hidden" id="inputconversationrate" name="inputconversationrate" value="">
                                                        <input type="hidden" id="referrerconversationrate" name="referrerconversationrate" value="">
                                                        
                                                        <input type="hidden" id="inputproductwisepoints" name="inputproductwisepoints" value="">
                                                        <input type="hidden" id="inputsellerproductwisepoints" name="inputsellerproductwisepoints" value="">
                                                        <input type="hidden" id="inputproductwisepointsforbuyer" name="inputproductwisepointsforbuyer" value="">
                                                        <input type="hidden" id="inputproductwisepointsforseller" name="inputproductwisepointsforseller" value="">
                                                        <input type="hidden" name="multiplypointswithqty" id="multiplypointswithqty">
                                                        <input type="hidden" name="sellermultiplypointswithqty" id="sellermultiplypointswithqty">
                                                        
                                                        <input type="hidden" name="overallproductpoints" id="overallproductpoints">
                                                        <input type="hidden" name="selleroverallproductpoints" id="selleroverallproductpoints">
                                                        <input type="hidden" name="sellerpointsforoverallproduct" id="sellerpointsforoverallproduct" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['sellerpointsforoverallproduct']; } ?>">
                                                        <input type="hidden" name="buyerpointsforoverallproduct" id="buyerpointsforoverallproduct" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['buyerpointsforoverallproduct']; } ?>">
                                                        <input type="hidden" name="mimimumorderqtyforoverallproduct" id="mimimumorderqtyforoverallproduct">
                                                        <input type="hidden" name="sellermimimumorderqtyforoverallproduct" id="sellermimimumorderqtyforoverallproduct">

                                                        <input type="hidden" name="pointsonsalesorder" id="pointsonsalesorder">
                                                        <input type="hidden" name="sellerpointsonsalesorder" id="sellerpointsonsalesorder">
                                                        <input type="hidden" name="sellerpointsforsalesorder" id="sellerpointsforsalesorder" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['sellerpointsforsalesorder']; } ?>">
                                                        <input type="hidden" name="buyerpointsforsalesorder" id="buyerpointsforsalesorder" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['buyerpointsforsalesorder']; } ?>">
                                                        <input type="hidden" name="mimimumorderamountforsalesorder" id="mimimumorderamountforsalesorder">
                                                        <input type="hidden" name="sellermimimumorderamountforsalesorder" id="sellermimimumorderamountforsalesorder">
                                                    
                                                        <input type="hidden" name="overallproductpointsforbuyer" id="overallproductpointsforbuyer">
                                                        <input type="hidden" name="overallproductpointsforseller" id="overallproductpointsforseller">
                                                        <input type="hidden" name="salespointsforbuyer" id="salespointsforbuyer">
                                                        <input type="hidden" name="salespointsforseller" id="salespointsforseller">                                                    
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                                <?php if(!isset($ordertype)){ 
                                                if(!empty($orderdata) && !empty($ExtraChargesData)) { ?>
                                                    <?php for ($i=0; $i < count($ExtraChargesData); $i++) { ?>
                                                        <tr class="countcharges" id="countcharges<?=$i+1?>">                  <th>
                                                                <input type="hidden" name="extrachargemappingid[]" value="<?=$ExtraChargesData[$i]['id']?>" id="extrachargemappingid<?=$i+1?>">
                                                                <div class="col-md-9 p-n">
                                                                    <div class="form-group p-n" id="extracharges<?=$i+1?>_div">
                                                                        <div class="col-sm-12">
                                                                            <select id="extrachargesid<?=$i+1?>" name="extrachargesid[]" class="selectpicker form-control extrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                                <option value="0">Select Extra Charges</option>
                                                                                <?php foreach($extrachargesdata as $extracharges){ ?>
                                                                                    <option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>" <?php if($ExtraChargesData[$i]['extrachargesid'] == $extracharges['id']){ echo "selected"; } ?>><?php echo $extracharges['extrachargename']; ?></option>
                                                                                <?php } ?>
                                                                            </select>

                                                                            <input type="hidden" name="extrachargestax[]" id="extrachargestax<?=$i+1?>" class="extrachargestax" value="<?=number_format($ExtraChargesData[$i]['taxamount'],2,'.','')?>">
                                                                            <input type="hidden" name="extrachargesname[]" id="extrachargesname<?=$i+1?>" class="extrachargesname" value="<?=$ExtraChargesData[$i]['extrachargesname']?>">
                                                                            <input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage<?=$i+1?>" class="extrachargepercentage" value="<?=$ExtraChargesData[$i]['extrachargepercentage']?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 text-right p-n pt-md">
                                                                    <?php if($i==0){?>
                                                                        <?php if(count($ExtraChargesData)>1){ ?>
                                                                            <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                        <?php }else { ?>
                                                                            <button type="button" class="btn btn-default btn-raised add_charges_btn" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                        <?php } ?>

                                                                    <?php }else if($i!=0) { ?>
                                                                        <button type="button" class="btn btn-default btn-raised remove_charges_btn" onclick="removecharge(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                    <?php } ?>
                                                                    <button type="button" class="btn btn-default btn-raised btn-sm remove_charges_btn" onclick="removecharge(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                
                                                                    <button type="button" class="btn btn-default btn-raised add_charges_btn" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                                                                </div>
                                                            </th>
                                                        
                                                            <td class="text-right">
                                                                <div class="form-group p-n" id="extrachargeamount<?=$i+1?>_div">
                                                                    <div class="col-sm-12">
                                                                        <input type="text" id="extrachargeamount<?=$i+1?>" name="extrachargeamount[]" value="<?=number_format($ExtraChargesData[$i]['amount'],2,'.','')?>" class="form-control text-right extrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        
                                                        </tr>
                                                    <?php } ?>
                                                <?php }else{ ?>
                                                    <tr class="countcharges" id="countcharges1">                    
                                                        <th>
                                                            <div class="col-md-9 p-n">
                                                                <div class="form-group p-n" id="extracharges1_div">
                                                                    <div class="col-sm-12">
                                                                        <select id="extrachargesid1" name="extrachargesid[]" class="selectpicker form-control extrachargesid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0">Select Extra Charges</option>
                                                                            <?php foreach($extrachargesdata as $extracharges){ ?>
                                                                                <option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>
                                                                            <?php } ?>
                                                                        </select>

                                                                        <input type="hidden" name="extrachargestax[]" id="extrachargestax1" class="extrachargestax" value="">
                                                                        <input type="hidden" name="extrachargesname[]" id="extrachargesname1" class="extrachargesname" value="">
                                                                        <input type="hidden" name="extrachargepercentage[]" id="extrachargepercentage1" class="extrachargepercentage" value="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3 text-right p-n pt-md">
                                                                <button type="button" class="btn btn-default btn-raised  remove_charges_btn m-n" onclick="removecharge(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                                                <button type="button" class="btn btn-default btn-raised  add_charges_btn m-n" onclick="addnewcharge()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                            </div>
                                                        </th>
                                                    
                                                        <td class="text-right">
                                                            <div class="form-group p-n" id="extrachargeamount1_div">
                                                                <div class="col-sm-12">
                                                                    <input type="text" id="extrachargeamount1" name="extrachargeamount[]" class="form-control text-right extrachargeamount" placeholder="Charge" onkeypress="return decimal_number_validation(event, this.value)">
                                                                </div>
                                                            </div>
                                                        </td>
                                                    
                                                    </tr>
                                                <?php } }?>
                                                <tr>                    
                                                    <th>Round Off</th>
                                                    <td class="text-right">
                                                        <span id="roundoff">0.00</span>
                                                        <input type="hidden" id="inputroundoff" name="inputroundoff" value="0.00">
                                                    </td>
                                                </tr>  
                                                <tr>                    
                                                    <th>Amount Payable</th>
                                                    <th class="text-right">
                                                        <span id="netamount" name="netamount"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['payableamount']; }else{ echo "0.00"; } ?></span>
                                                        <input type="hidden" id="inputnetamount" name="netamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['payableamount']; }?>">
                                                    </th>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php
                                    /* <table id="example" class="table table-striped" cellspacing="0" width="100%" style="border: 4px solid #e8e8e8;">
                                        <tbody>
                                        
                                            <tr>                    
                                                <th class="text-right">Sub Total :</th>
                                                <td class="text-right" width="25%">
                                                    <span id="grossamount"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['amount']; }else{ echo "0.00"; }?></span>
                                                    <input type="hidden" id="inputgrossamount" name="grossamount" value="<?php if(!empty($orderdata['amount'])){ echo $orderdata['orderdetail']['amount']; }?>">
                                                </td>
                                            </tr>
                                            <tr>                    
                                                <th class="text-right">Tax :</th>
                                                <td class="text-right">
                                                    <span id="totaltaxamount"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['taxamount']; }else{ echo "0.00"; }?></span>
                                                    <input type="hidden" id="inputtotaltaxamount" name="inputtotaltaxamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['taxamount']; }else{ echo "0.00"; }?>">
                                                </td>
                                            </tr> 
                                            <tr id="discountrow" style="display:none">                    
                                                <th class="text-right">Discount (<span id="discountpercentage"><?php if(!empty($orderdata['orderdetail'])){ echo number_format($orderdata['orderdetail']['globaldiscount']*100/$orderdata['orderdetail']['amount'],2); }else{ echo "0"; }?></span>%) :
                                                </th>
                                                <td class="text-right">
                                                    <span id="discountamount"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['globaldiscount']; }else{ echo "0.00"; }?></span>
                                                </td>
                                            </tr>
                                            <tr id="couponrow" style="display:none">                    
                                                <th class="text-right">Coupon Discount :</th>
                                                <td class="text-right">
                                                    <span id="coupondiscountamount"><?php if(!empty($orderdata['orderdetail'])){ echo number_format($orderdata['orderdetail']['couponcodeamount'],2); }else{ echo "0.00"; }?></span>
                                                    <input type="hidden" id="couponamount" name="couponamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['couponcodeamount']; }?>">
                                                </td>
                                            </tr> 
                                            <?php if(REWARDSPOINTS==1 || !empty($orderdata['orderdetail'])){ ?>
                                                <tr id="trconversationrate" style="display:none">                    
                                                    <th class="text-right">Redeem Amount (<span id="conversationrate">0</span>) : </th>
                                                    <td class="text-right">
                                                        <span id="conversationrateamount">0.00</span>
                                                        <input type="hidden" id="inputconversationrateamount" name="inputconversationrateamount" value="">
                                                        <input type="hidden" id="totalpointsforbuyer" name="totalpointsforbuyer" value="">
                                                        <input type="hidden" id="totalpointsforseller" name="totalpointsforseller" value="">
                                                        <input type="hidden" id="totalredeempointsforbuyer" name="totalredeempointsforbuyer" value="">
                                                        
                                                        <input type="hidden" id="inputconversationrate" name="inputconversationrate" value="">
                                                        <input type="hidden" id="referrerconversationrate" name="referrerconversationrate" value="">
                                                        
                                                        <input type="hidden" id="inputproductwisepoints" name="inputproductwisepoints" value="">
                                                        <input type="hidden" id="inputproductwisepointsforbuyer" name="inputproductwisepointsforbuyer" value="">
                                                        <input type="hidden" id="inputproductwisepointsforseller" name="inputproductwisepointsforseller" value="">
                                                        <input type="hidden" name="multiplypointswithqty" id="multiplypointswithqty">

                                                        <input type="hidden" name="overallproductpoints" id="overallproductpoints">
                                                        <input type="hidden" name="sellerpointsforoverallproduct" id="sellerpointsforoverallproduct" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['sellerpointsforoverallproduct']; } ?>">
                                                        <input type="hidden" name="buyerpointsforoverallproduct" id="buyerpointsforoverallproduct" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['buyerpointsforoverallproduct']; } ?>">
                                                        <input type="hidden" name="mimimumorderqtyforoverallproduct" id="mimimumorderqtyforoverallproduct">

                                                        <input type="hidden" name="pointsonsalesorder" id="pointsonsalesorder">
                                                        <input type="hidden" name="sellerpointsforsalesorder" id="sellerpointsforsalesorder" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['sellerpointsforsalesorder']; } ?>">
                                                        <input type="hidden" name="buyerpointsforsalesorder" id="buyerpointsforsalesorder" value="<?php if(!empty($orderdata['orderdetail']) && !isset($addordertype)){ echo $orderdata['orderdetail']['buyerpointsforsalesorder']; } ?>">
                                                        <input type="hidden" name="mimimumorderamountforsalesorder" id="mimimumorderamountforsalesorder">
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            <tr style="border-top: 2px solid #e0e0e0;">                    
                                                <th class="text-right">Net Total (<?=CURRENCY_CODE?>) :</th>
                                                <td class="text-right">
                                                    <span id="netamount" name="netamount"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['payableamount']; }else{ echo "0.00"; } ?></span>
                                                    <input type="hidden" id="inputnetamount" name="netamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['payableamount']; }?>">
                                                </td>
                                            </tr>  
                                        </tbody>
                                    </table> */ ?>
                                </div>
                            </div>
                            <div class="col-md-12 p-n">
                                <?php if(!isset($ordertype)){ ?>
                                    <div class="col-md-7">
                                        <div class="form-group" id="">
                                            <input type="hidden" name="orderdeliveryid" id="orderdeliveryid" value="<?php if(isset($orderdata) && isset($orderdata['orderdetail']['deliverytype']) && $orderdata['orderdetail']['deliverytype']!=3 && isset($orderdata['orderdeliverydata'])){ echo $orderdata['orderdeliverydata']['id']; } ?>">
                                            <label for="deliveryday" class="col-md-3 control-label m-n" style="text-align: left;">Delivery Type</label>
                                            <div class="col-md-9 p-n">
                                                <div class="col-md-4 col-xs-4 pl-n" style="padding-left: 0px;">
                                                    <div class="radio">
                                                        <input type="radio" name="deliverytype" id="deliveryday" value="1" <?php if(isset($orderdata) && isset($orderdata['orderdetail']['deliverytype']) && $orderdata['orderdetail']['deliverytype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                        <label for="deliveryday">Approx Days</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-xs-4 pl-n">
                                                    <div class="radio">
                                                        <input type="radio" name="deliverytype" id="deliverydate" value="2" <?php if(isset($orderdata) && isset($orderdata['orderdetail']['deliverytype']) && $orderdata['orderdetail']['deliverytype']==2){ echo 'checked'; }?>>
                                                        <label for="deliverydate">Approx Date</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-xs-4 pl-n">
                                                    <div class="radio">
                                                        <input type="radio" name="deliverytype" id="deliveryfix" value="3" <?php if(isset($orderdata) && isset($orderdata['orderdetail']['deliverytype']) && $orderdata['orderdetail']['deliverytype']==3){ echo 'checked'; }?>>
                                                        <label for="deliveryfix">Fix</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-9 p-n" id="deliverydays" style="<?php if((isset($orderdata) && isset($orderdata['orderdetail']['deliverytype']) && ($orderdata['orderdetail']['deliverytype']==1 || $orderdata['orderdetail']['deliverytype']==0))){ echo "display:block;"; }else if(!isset($orderdata)){ echo "display:block;"; }else{ echo "display:none;"; }?>">
                                            <div class="col-md-6 pl-n">
                                                <div class="form-group m-n" id="minimumdays_div">
                                                    <label for="minimumdays" class="control-label">Minimum Days</label>
                                                    <input type="text" class="form-control" id="minimumdays" name="minimumdays" onkeypress="return isNumber(event)" maxlength="3" value="<?php if(isset($orderdata) && isset($orderdata['orderdeliverydata']['minimumdeliverydays']) && $orderdata['orderdeliverydata']['minimumdeliverydays']>0){ echo $orderdata['orderdeliverydata']['minimumdeliverydays']; }?>">	
                                                    <span class="material-input"></span>
                                                </div>
                                            </div>  
                                            <div class="col-md-6 pr-n">
                                                <div class="form-group m-n" id="maximumdays_div">
                                                    <label for="maximumdays" class="control-label">Maximum Days</label>
                                                    <input type="text" class="form-control" id="maximumdays" name="maximumdays" onkeypress="return isNumber(event)" maxlength="3" value="<?php if(isset($orderdata) && isset($orderdata['orderdeliverydata']['maximumdeliverydays']) && $orderdata['orderdeliverydata']['maximumdeliverydays']>0){ echo $orderdata['orderdeliverydata']['maximumdeliverydays']; }?>">	
                                                    <span class="material-input"></span>
                                                </div>
                                            </div>   
                                        </div>
                                        <div class="col-md-9 p-n" id="deliverydates" style="<?php if(isset($orderdata) && isset($orderdata['orderdetail']['deliverytype']) && $orderdata['orderdetail']['deliverytype']==2){ echo "display:block;"; }else{ echo "display:none;"; }?>">
                                            <div class="form-group m-n" id="deliverydate_div">
                                                <div class="col-sm-12 p-n">
                                                    <label for="deliveryfromdate" class=" control-label">Delivery Date</label>
                                                    <div class="input-daterange input-group deliverytype_date" id="datepicker-range">
                                                        
                                                        <input type="text" class="input-small form-control" name="deliveryfromdate" id="deliveryfromdate" value="<?php if(isset($orderdata) && isset($orderdata['orderdeliverydata']['deliveryfromdate']) && $orderdata['orderdeliverydata']['deliveryfromdate']!="0000-00-00"){ echo $this->general_model->displaydate($orderdata['orderdeliverydata']['deliveryfromdate']); }?>" placeholder="From Date" title="From Date" readonly/>
                                                        
                                                        <span class="input-group-addon">to</span>
                                                        
                                                        <input type="text" class="input-small form-control" name="deliverytodate" id="deliverytodate" value="<?php if(!empty($orderdata) && isset($orderdata['orderdeliverydata']['deliverytodate']) && $orderdata['orderdeliverydata']['deliverytodate']!="0000-00-00"){ echo $this->general_model->displaydate($orderdata['orderdeliverydata']['deliverytodate']); }?>" placeholder="To Date" title="To Date" readonly/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-12 p-n" id="deliveryschedulefix" style="<?php if(isset($orderdata) && isset($orderdata['orderdetail']['deliverytype']) && $orderdata['orderdetail']['deliverytype']==3){ echo "display:block;"; }else{ echo "display:none;"; }?>border: 1px solid #e8e8e8;">
                                        <?php if(isset($orderdata) && !empty($orderdata['orderdeliverydata']) && $orderdata['orderdetail']['deliverytype']==3){ ?>
                                            <input type="hidden" name="removedeliveryproductid" id="removedeliveryproductid" value="">
                                            <table class="table table-bordered m-n" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>Product Name</th>
                                                        <th width="22%">Quantity</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            <?php foreach($orderdata['orderdeliverydata'] as $k=>$fixdelivery){ ?>
                                                <input type="hidden" name="fixdeliveryid[]" value="<?=$fixdelivery['fixdeliveryid']?>" id="fixdeliveryid<?=$k?>">
                                            
                                                <table class="table table-bordered border-panel delivery-slot" width="100%" id="table<?=$k?>">
                                                    <tbody>
                                                        <tr id="duplicatetable<?=$k?>">
                                                            <td colspan="3" class="text-right">
                                                                <div class="col-md-3 p-n">
                                                                    <div class="form-group">
                                                                        <div class="col-sm-12">
                                                                            <div class="checkbox pt-n pl-n">
                                                                                <input id="isdelivered<?=$k?>" type="checkbox" value="0" name="isdelivered<?=$k?>" class="checkradios deliverystatus" <?=$fixdelivery['deliverystatus']==1?"checked":""?> <?php if(!empty($orderdata) && $orderdata['orderdetail']['approvestatus']==0){ echo "disabled"; } ?>>
                                                                                <label for="isdelivered<?=$k?>">IsDelivered</label>
                                                                                <input type="hidden" name="fixdelivery[]" value="<?=$k?>">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group mt-n" id="deliverydate<?=$k?>_div">
                                                                        <div class="col-md-12">
                                                                            <input type="text" class="form-control deliverydate" id="deliverydate<?=$k?>" name="deliverydate[]" value="<?php if($fixdelivery['deliverydate']!='0000-00-00'){ echo $this->general_model->displaydate($fixdelivery['deliverydate']); } ?>" placeholder="Delivered date" readonly>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <button class="btn btn-primary btn-raised btn-sm duplicate" type="button" name="duplicate" id="duplicate<?=$k?>" disabled><i class="fa fa-plus"></i> Duplicate</button>
                                                                <?php if($k!=0){ ?>
                                                                    <button class="btn btn-danger btn-raised btn-sm removeorderproducts" type="button" name="removeorderproducts" id="removeorderproducts<?=$k?>"><i class="fa fa-times"></i> Remove</button>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                        <?php if(!empty($fixdelivery['deliveryproductdata'])){
                                                            foreach($fixdelivery['deliveryproductdata'] as $index=>$product){ ?>
                                                            <tr>
                                                                <td style="padding-top: 20px;"><?=$product['productname']?>
                                                                    <input type="hidden" name="fixdeliveryproductdata[<?=$k?>][]" id="deliveryproductid<?=$k.($product['div_id']+1)?>" value="<?=$product['productid']?>" div-id="<?=($product['div_id']+1)?>">
                                                                </td>
                                                                <td width="22%" class="tdisdisabled <?= $fixdelivery['deliverystatus']==1?"cls-disabled":"" ?>">
                                                                    <div class="form-group mt-n" id="deliveryqty<?=($product['div_id']+1)?>_div">
                                                                        <div class="col-md-9">
                                                                            <input type="text" class="form-control deliveryqty" id="deliveryqty<?=$k.($product['div_id']+1)?>" name="deliveryqty[<?=$k?>][]" value="<?=$product['quantity']?>" maxlength="2" onkeypress="return isNumber(event);" style="display: block;" div-id="<?=($product['div_id']+1)?>">
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } 
                                                        }?>
                                                    </tbody>
                                                </table>
                                            <?php } ?>
                                        <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="col-md-5 pull-right">
                                    <div class="form-group" id="remarks_div">
                                        <div class="col-sm-12">
                                            <label for="remarks" class="control-label">Remarks</label>
                                            <textarea id="remarks" name="remarks" class="form-control"><?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['remarks']; }?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if(!isset($orderdata)){ ?>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group" id="generateinvoice_div">
                                    <div class="col-sm-12">
                                        <div class="checkbox">
                                            <input id="generateinvoice" type="checkbox" value="1" name="generateinvoice" class="checkradios" checked>
                                            <label for="generateinvoice">Generate Invoice</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group" id="paymenttype_div">
                                    <div class="col-sm-12 pr-sm">
                                        <input type="hidden" name="oldpaymenttype" id="oldpaymenttype" value="<?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['paymenttype']; } ?>">
                                        <label for="paymenttypeid" class="control-label">Select Payment Type <span class="mandatoryfield">*</span></label>
                                        <select id="paymenttypeid" name="paymenttypeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Payment Type</option>
                                            <option value="1" <?php if(!empty($orderdata)){ if($orderdata['orderdetail']['paymenttype']==1){ echo "selected"; } }?>>COD</option>
                                            <option value="3" <?php if(!empty($orderdata)){ if($orderdata['orderdetail']['paymenttype']==3){ echo "selected"; } }?>>Advance Payment</option>
                                            <?php if(HIDE_EMI==0){ ?>
                                            <option value="4" <?php if(!empty($orderdata)){ if($orderdata['orderdetail']['paymenttype']==4){ echo "selected"; } }?>>EMI Payment</option>
                                            <?php } ?>
                                            <option value="5" <?php if(!empty($orderdata)){ if($orderdata['orderdetail']['paymenttype']==5){ echo "selected"; } }?>>Debit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" id="transactionid_div" style="<?php if(!isset($orderdata) || (!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==0 || $orderdata['orderdetail']['paymenttype']==4))){  echo "display:none;"; } ?>">
                                    <div class="col-sm-12 pl-sm pr-sm">
                                        <input type="hidden" name="transaction_id" id="transaction_id" value="<?php if(!empty($orderdata['transaction'])){ echo $orderdata['transaction']['id']; } ?>"> 
                                        <label for="transactionid" class="control-label">Transaction ID</label>
                                        <input id="transactionid" type="text" name="transactionid" class="form-control" value="<?php if(!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==1 || $orderdata['orderdetail']['paymenttype']==3)){ echo $orderdata['transaction']['transactionid']; } ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="transactionproof_div" style="<?php if(!isset($orderdata) || (!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==0 || $orderdata['orderdetail']['paymenttype']==4))){  echo "display:none;"; } ?>">
                                    
                                    <input type="hidden" name="oldtransactionproof" id="oldtransactionproof" value="<?php if(!empty($orderdata['transaction'])){ echo $orderdata['transaction']['transactionproof']; } ?>">
                                    <div class="col-md-12 pl-sm pr-sm">
                                        <label class="control-label">Transaction Proof</label>
                                        <div class="input-group" id="fileupload">
                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                <span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
                                                    <input type="file" name="transactionproof"  id="transactionproof" class="transactionproof" onchange="validfile($(this))">
                                                </span>
                                            </span>                                        
                                            <input type="text" id="textfile" class="form-control" name="textfile" value="<?php if(!empty($orderdata['transaction'])){ echo $orderdata['transaction']['transactionproof']; } ?>" readonly >
                                        </div>                                      
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="form-group text-right" id="advancepayment_div" style="<?php if(!isset($orderdata) || (!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==0 || $orderdata['orderdetail']['paymenttype']==4))){  echo "display:none;"; } ?>">
                                    <div class="col-sm-12 pl-sm">
                                        <label for="transactionid" class="control-label">Advance Payment (<?=CURRENCY_CODE?>)</label>
                                        <input id="advancepayment" data-calculate="false" type="text" name="advancepayment" class="form-control text-right" value="<?php if(!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==1 || $orderdata['orderdetail']['paymenttype']==3)){ echo $orderdata['transaction']['payableamount']; } ?>">
                                        <input type="hidden" id="channeladvancepaymentcod" value="0">
                                    </div>
                                </div>
                            </div>
                            <?php
                                if(!empty($channelsetting) && $channelsetting['partialpayment']==1){ 
                                    $received = 0;
                                    if(!empty($installmentdata)){ 
                                        //$key = array_search('1', array_column($installmentdata, 'status'));
                                        $key = false;
                                        $search = ['status' => 1];
                                        foreach ($installmentdata as $k => $v) {
                                            if ($v['status'] == $search['status']) {
                                                $key = true;
                                                // key found - break the loop
                                                break;
                                            }
                                        }
                                        if($key!=false){
                                            $received = 1;
                                        }else{
                                            $received = 0;
                                        }
                                    }
                                    ?>
                                    <script>
                                    EMIreceived = <?=$received?>;
                                    </script>
                                    <div class="row" id="partialpaymentoption" style="<?php if(!empty($orderdata['orderdetail']) && $orderdata['orderdetail']['paymenttype']==4 && HIDE_EMI==0){  echo "display:block;"; } else{ echo "display:none;"; } ?>">
                                        
                                        <div class="col-md-12">
                                            <div class="row m-n" id="installmentsetting_div" style="<?php /* if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } */ ?>">
                                                <div class="col-sm-3">
                                                    <div class="form-group" id="noofinstallment_div">
                                                        <div class="col-sm-12">
                                                            <label for="noofinstallment" class="control-label">No. of Installment <span class="mandatoryfield">*</span></label>
                                                            <input type="text" class="form-control" id="noofinstallment" name="noofinstallment" maxlength="2" value="<?php if(!empty($installmentdata)){ echo count($installmentdata); } ?>" onkeypress="return isNumber(event)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group" id="emidate_div">
                                                        <div class="col-sm-12">
                                                            <label for="emidate" class="control-label">EMI Start Date <span class="mandatoryfield">*</span></label>
                                                            <input id="emidate" type="text" name="emidate" value="<?php if(!empty($installmentdata)){ echo $this->general_model->displaydate($installmentdata[0]['date']); } ?>" class="form-control" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group" id="emiduration_div">
                                                        <div class="col-sm-12">
                                                            <label for="emiduration" class="control-label">EMI Duration (In Days) <span class="mandatoryfield">*</span></label>
                                                            <input id="emiduration" type="text" name="emiduration" value="<?php if(!empty($installmentdata)){ if(count($installmentdata)==1){ echo "1"; } else{ echo ceil(abs(strtotime($installmentdata[0]['date']) - strtotime($installmentdata[1]['date'])) / 86400); }
                                                            } ?>" class="form-control" maxlength="4">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3 pt-xxl">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <button type="button" onclick="generateinstallment(1)" class="btn btn-primary btn-raised" <?php if($received==1){ echo "disabled"; } ?>>Generate</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="installmentmaindiv" style="margin-top: 12px;<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
                                        <div class="row" id="installmentmaindivheading" style="<?php if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
                                            <div class="col-md-1 text-center"><b>Sr.No</b></div>
                                            <div class="col-md-2 text-right"><b>Installment (%)</b></div>
                                            <div class="col-md-2 text-right"><b>Amount</b></div>
                                            <div class="col-md-2 text-center"><b>Installment Date</b></div>
                                            <?php if(!isset($ordertype)){ ?>
                                            <div class="col-md-2 text-center"><b>Payment Date</b></div>
                                            <div class="col-md-2 text-center"><b>Received Status</b></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div id="installmentdivs" style="<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
                                    
                                        <?php if(!empty($installmentdata)){ 
                                            for($i=0; $i < count($installmentdata); $i++){ ?>
                                                <input type="hidden" name="installmentid[]" value="<?=$installmentdata[$i]['id']?>">   
                                                <div class="row noofinstallmentdiv">
                                                    <div class="col-md-1 text-center"><div class="form-group"><div class="col-sm-12"><?=($i+1)?></div></div></div>
                                                    <div class="col-md-2 text-center">
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <input type="text" id="percentage<?=($i+1)?>" value="<?=$installmentdata[$i]['percentage']?>" name="percentage[]" class="form-control text-right percentage"  div-id="<?=($i+1)?>" maxlength="5" onkeyup="return onlypercentage(this.id)" onkeypress="return decimal(event,this.id)" <?php if($received==1){ echo "readonly"; } ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <input type="text" id="installmentamount<?=($i+1)?>" value="<?=$installmentdata[$i]['amount']?>" name="installmentamount[]" class="form-control text-right installmentamount" div-id="<?=($i+1)?>" maxlength="5" onkeypress="return decimal(event,this.id);" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <input type="text" id="installmentdate<?=($i+1)?>" value="<?=$this->general_model->displaydate($installmentdata[$i]['date'])?>" name="installmentdate[]" class="form-control installmentdate" div-id="<?=($i+1)?>" maxlength="5" <?php if($received==1){ echo "disabled"; } ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php if(!isset($ordertype)){ ?>
                                                    <div class="col-md-2 text-center">
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <input type="text" id="paymentdate<?=($i+1)?>" value="<?=$installmentdata[$i]['paymentdate']!=''?$this->general_model->displaydate($installmentdata[$i]['paymentdate']):''?>" name="paymentdate[]" class="form-control paymentdate" div-id="<?=($i+1)?>" maxlength="5" <?php if($received==1){ echo "disabled"; } ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 text-center">
                                                        <div class="form-group">
                                                            <div class="col-sm-12">
                                                                <div class="checkbox">
                                                                    <input id="installmentstatus<?=($i+1)?>" type="checkbox" value="<?=$installmentdata[$i]['status']?>" name="installmentstatus<?=($i+1)?>" div-id="<?=($i+1)?>" class="checkradios" <?php if($received==1){ echo "disabled"; } ?> <?=$installmentdata[$i]['status']==1?"checked":""?> >
                                                                    <label for="installmentstatus<?=($i+1)?>"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                </div>
                                            <?php }?>

                                        <?php } ?>
                                    
                                    </div>
                            <?php } ?>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <?php if(!empty($orderdata) && !isset($addordertype)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?><?php if(isset($ordertype) && $ordertype==1){ echo "purchase-order"; }else{ echo "order"; } ?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                            </div>
                        </div>
                    </form>
				</div>
		      </div>
		    </div>
		  </div>
		</div>

        <div class="modal fade" id="addbuyerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 535px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Search Buyer <?=Member_label?></h4>
                    </div>
                    <div class="modal-body" style="padding-top: 4px;">
                        <form action="#" id="addbuyerform" class="form-horizontal">
                           
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" id="buyercode_div">
                                        <label class="col-sm-4 control-label" for="buyercode">Buyer Code <span
                                                    class="mandatoryfield">*</span></label>
                                        <div class="col-md-6">
                                            <input id="buyercode" type="text" name="buyercode" class="form-control"
                                                value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <input type="button" id="submit" onclick="searchmembercode()" name="submit" value="SEARCH" class="btn btn-primary btn-raised">
                                        <a class="<?=cancellink_class;?>" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<div class="infobar-wrapper scroll-pane has-scrollbar">
    <div class="infobar scroll-content mb-sm" tabindex="0" style="right: -15px;">
    
            <div class="widget">
                <div class="widget-heading mb-n">Available Offers  <a href="javascript:void(0)" class="removeoffersbar" style="float: right;color: #636363;padding-right: 5px;"><i class="fa fa-times fa-lg"></i></a></div>
                <div class="widget-body pt-n">
                    
                    <div id="offerproductsdata">
                        <!-- <tr>
                            <td class="width5">
                                <div class="radio">
                                    <input type="radio" name="offerproduct" id="offerproduct1" value="0">
                                    <label for="offerproduct1"></label>
                                </div>
                            </td>
                            <td class="pl-n">
                                <label for="offerproduct1">
                                    <h5>SHOPPASS300</h5>
                                    <img src="<?=OFFER.'ourwork1580735685.png'?>" alt="" style="width: 100%;margin-bottom: 5px;">
                                    <p>Enjoy Rs.100 cashback on a minimum cart value of Rs.649 on your next 3 purchases</p>
                                </label>
                                <p><a href="#" data-toggle="popover" data-placement="bottom" title="" data-content="<div class='popover-content-style'>asdasdasdasd</div>"  data-original-title="Terms & Conditions">Terms & Conditions</a></p>
                            </td>
                        </tr>
                        <tr>
                            <td class="width5">
                                <div class="radio">
                                    <input type="radio" name="offerproduct" id="offerproduct2" value="0">
                                    <label for="offerproduct2"></label>
                                </div>
                            </td>
                            <td class="pl-n">
                                <label for="offerproduct2">
                                    <h5>SHOPPASS300</h5>
                                    <img src="<?=OFFER.'ourwork1580735685.png'?>" alt="" style="width: 100%;margin-bottom: 5px;">
                                    <p>Enjoy Rs.100 cashback on a minimum cart value of Rs.649 on your next 3 purchases</p>
                                    <p><a href="#">Terms & Conditions</a></p>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="width5">
                                <div class="radio">
                                    <input type="radio" name="offerproduct" id="offerproduct3" value="0">
                                    <label for="offerproduct3"></label>
                                </div>
                            </td>
                            <td class="pl-n">
                                <label for="offerproduct3">
                                    <h5>SHOPPASS300</h5>
                                    <img src="<?=OFFER.'ourwork1580735685.png'?>" alt="" style="width: 100%;margin-bottom: 5px;">
                                    <p>Enjoy Rs.100 cashback on a minimum cart value of Rs.649 on your next 3 purchases</p>
                                    <p><a href="#">Terms & Conditions</a></p>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <td class="width5">
                                <div class="radio">
                                    <input type="radio" name="offerproduct" id="offerproduct4" value="0">
                                    <label for="offerproduct4"></label>
                                </div>
                            </td>
                            <td class="pl-n">
                                <label for="offerproduct4">
                                    <h5>SHOPPASS300</h5>
                                    <img src="<?=OFFER.'ourwork1580735685.png'?>" alt="" style="width: 100%;margin-bottom: 5px;">
                                    <p>Enjoy Rs.100 cashback on a minimum cart value of Rs.649 on your next 3 purchases</p>
                                    <p><a href="#">Terms & Conditions</a></p>
                                </label>
                            </td>
                        </tr> -->
                    
                    </div>
                    
                </div>
            </div>

            <!-- <div class="widget">
                <div class="widget-heading">Contacts</div>
                <div class="widget-body">
                    <ul class="media-list contacts">
                        <li class="media notification-message">
                            <div class="media-left">
                                <img class="img-circle avatar" src="assets/demo/avatar/avatar_01.png" alt="">
                            </div>
                            <div class="media-body">
                              <span class="text-gray">Jon Owens</span>
                                <span class="contact-status text-success">Online</span>
                            </div>
                        </li>
                        <li class="media notification-message">
                            <div class="media-left">
                                <img class="img-circle avatar" src="assets/demo/avatar/avatar_02.png" alt="">
                            </div>
                            <div class="media-body">
                                <span class="text-gray">Nina Huges</span>
                                <span class="contact-status text-muted">Offline</span>
                            </div>
                        </li>
                        <li class="media notification-message">
                            <div class="media-left">
                                <img class="img-circle avatar" src="assets/demo/avatar/avatar_03.png" alt="">
                            </div>
                            <div class="media-body">
                                <span class="text-gray">Austin Lee</span>
                                <span class="contact-status text-danger">Busy</span>
                            </div>
                        </li>
                        <li class="media notification-message">
                            <div class="media-left">
                                <img class="img-circle avatar" src="assets/demo/avatar/avatar_04.png" alt="">
                            </div>
                            <div class="media-body">
                                <span class="text-gray">Grady Hines</span>
                                <span class="contact-status text-warning">Away</span>
                            </div>
                        </li>
                        <li class="media notification-message">
                            <div class="media-left">
                                <img class="img-circle avatar" src="assets/demo/avatar/avatar_06.png" alt="">
                            </div>
                            <div class="media-body">
                                <span class="text-gray">Adrian Barton</span>
                                <span class="contact-status text-success">Online</span>
                            </div>
                        </li>
                    </ul>                                
                </div>
            </div> -->


            </div>
   
    <div class="scroll-track">
        <div class="scroll-thumb" style="height: 115px; transform: translate(0px, 0px);">
        </div>
    </div>
</div>

<div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h4 class="modal-title">Add Address</h4>
            </div>
            <div class="modal-body pt-sm">
                <form class="form-horizontal" id="memberaddressform">

                    <div class="col-md-6">
                        <div class="form-group" id="baname_div">
                            <label class="col-sm-4 control-label" for="baname">Name <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="baname" type="text" name="baname" class="form-control" onkeypress="return onlyAlphabets(event)" value="">
                            </div>
                        </div>
                        <div class="form-group" id="baemail_div">
                            <label class="col-sm-4 control-label" for="baemail">Email
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="baemail" type="text" name="baemail" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group" id="baddress_div">
                            <label class="col-sm-4 control-label" for="baddress">Address <span
                                    class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <textarea id="baddress" name="baddress" value="" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group" id="batown_div">
                            <label class="col-sm-4 control-label" for="batown">Town</label>
                            <div class="col-sm-8">
                                <input id="batown" type="text" name="batown" class="form-control" value="">
                            </div>
                        </div>
                        <div class="form-group" id="sameasbillingaddress_div">
                            <div class="checkbox col-md-10 col-md-offset-2 control-label">
                                <input type="checkbox" name="sameasbillingaddress" id="sameasbillingaddress" checked>
                                <label for="sameasbillingaddress">Use billing address as shipping address.</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="bapostalcode_div">
                            <label class="col-sm-4 control-label" for="bapostalcode">Postal Code
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="bapostalcode" type="text" name="bapostalcode" class="form-control"
                                onkeypress="return isNumber(event)" value="">
                            </div>
                        </div>
                        <div class="form-group" id="bamobileno_div">
                            <label class="col-sm-4 control-label" for="bamobileno">Mobile No.
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="bamobileno" type="text" name="bamobileno" class="form-control"
                                onkeypress="return isNumber(event)" maxlength="10" value="">
                            </div>
                        </div>
                        <div class="form-group" id="country_div">
                            <label class="col-sm-4 control-label" for="countryid">Country</label>
                            <div class="col-sm-8">
                                <select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select Country</option>
                                    <?php foreach($countrydata as $country){ ?>
                                    <option value="<?php echo $country['id']; ?>" <?php if(DEFAULT_COUNTRY_ID == $country['id']){ echo "selected"; } ?>><?php echo $country['name']; ?></option>
                                    <?php } ?>
                                </select>
                           </div>
                        </div>

                        <div class="form-group" id="province_div">
                            <label class="col-sm-4 control-label" for="provinceid">Province</label>
                            <div class="col-sm-8">
                                <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Province</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="city_div">
                            <label class="col-sm-4 control-label" for="cityid">City</label>
                            <div class="col-sm-8">
                                <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select City</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <div class="form-group" style="text-align: center;">
                            <input type="button" id="addressbtn" onclick="memberaddresscheckvalidation()"
                                name="submit" value="ADD" class="btn btn-primary btn-raised">
                            <a href="javascript:voi(0)" class="btn btn-info btn-raised"
                                onclick="memberaddressresetdata()">RESET</a>
                            <a class="<?=cancellink_class;?>" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="addnewmemberModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h4 class="modal-title">Add New <?=Member_label?></h4>
            </div>
            <div class="modal-body pt-sm">
                <form class="form-horizontal" id="addnewmemberform">

                    <div class="col-md-6">
                        <div class="form-group" id="newchannelid_div">
                            <label class="col-sm-4 control-label" for="newchannelid">New <?=Member_label?> Channel <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                            <select id="newchannelid" name="newchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                <option value="0">Select Channel</option>
                                <?php if(!empty($memberregchanneldata)){
                                    foreach($memberregchanneldata as $cd){ ?>
                                <option value="<?php echo $cd['id']; ?>" <?php if(count($memberregchanneldata)==1){ echo 'selected';  }  ?>><?php echo $cd['name']; ?></option>
                                <?php } } ?>
                            </select>
                            </div>
                        </div>
                        <div class="form-group" id="newmembername_div">
                            <label class="col-sm-4 control-label" for="newmembername">Name <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <input id="newmembername" type="text" name="newmembername" class="form-control" onkeypress="return onlyAlphabets(event)" value="">
                            </div>
                        </div>
                        <div class="form-group row" id="newmobile_div">
                            <label class="control-label col-md-4" for="newmobileno">Mobile No. <span class="mandatoryfield">*</span></label>  
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-4 pr-sm">
                                    <select id="newcountrycodeid" name="newcountrycodeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                        <option value="0">Code</option>
                                        <?php foreach($countrycodedata as $countrycoderow){ ?>
                                        <option value="<?php echo $countrycoderow['phonecode']; ?>" <?php if(DEFAULT_PHONECODE==$countrycoderow['phonecode']){ echo 'selected'; }  ?>><?php echo $countrycoderow['phonecode']; ?></option>
                                        <?php } ?>
                                    </select>
                                    </div>
                                    <div class="col-md-8 pl-sm">
                                        <input id="newmobileno" type="text" name="newmobileno" value="" class="form-control" maxlength="10"  onkeypress="return isNumber(event)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="newmembercode_div">
                            <label class="control-label col-md-4" for="newmembercode"><?=Member_label?> Code <span class="mandatoryfield">*</span></label>
                            <div class="col-md-8">
                                <div class="col-sm-10 p-n">
                                    <input id="newmembercode" type="text" name="newmembercode" value="" class="form-control" maxlength="8">
                                </div>
                                <div class="col-sm-2 pr-n pt-sm">
                                    <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Code" onclick="$('#newmembercode').val(randomPassword(8,8,0,0,0))"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row" id="newemail_div">
                            <label class="control-label col-md-4" for="newemail">Email <span class="mandatoryfield">*</span></label>
                            <div class="col-md-8">
                            <input id="newemail" type="text" name="newemail" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row" id="newgstno_div">
                            <label class="control-label col-md-4" for="newgstno">GST No.</label>
                            <div class="col-md-8">
                                <input id="newgstno" type="text" name="newgstno" value="" class="form-control" maxlength="15" onkeyup="this.value = this.value.toUpperCase();" onkeypress="return alphanumeric(event)" style="text-transform: uppercase;">
                            </div>
                        </div>
                        <div class="form-group row" id="newpanno_div">
                            <label class="control-label col-md-4" for="newpanno">PAN No.</label>
                            <div class="col-md-8">
                                <input id="newpanno" type="text" name="newpanno" value="" class="form-control" maxlength="10" onkeyup="this.value = this.value.toUpperCase();" style="text-transform: uppercase;" onkeypress="return alphanumeric(event)">
                            </div>
                        </div>
                        <div class="form-group" id="newcountry_div">
                            <label class="col-sm-4 control-label" for="newcountryid">Country <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <select id="newcountryid" name="newcountryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select Country</option>
                                    <?php foreach($countrydata as $country){ ?>
                                    <option value="<?php echo $country['id']; ?>" <?php if(DEFAULT_COUNTRY_ID == $country['id']){ echo "selected"; } ?>><?php echo $country['name']; ?></option>
                                    <?php } ?>
                                </select>
                           </div>
                        </div>

                        <div class="form-group" id="newprovince_div">
                            <label class="col-sm-4 control-label" for="newprovinceid">Province <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <select id="newprovinceid" name="newprovinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Province</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="newcity_div">
                            <label class="col-sm-4 control-label" for="newcityid">City <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <select id="newcityid" name="newcityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select City</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <hr>
                        <div class="form-group" style="text-align: center;">
                            <input type="button" id="addmemberbtn" onclick="addNewMember()"
                                name="submit" value="ADD" class="btn btn-primary btn-raised">
                            <a href="javascript:void(0)" class="btn btn-info btn-raised"
                                onclick="resetNewMemberForm()">RESET</a>
                            <a class="<?=cancellink_class;?>" href="javascript:void(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {   
        if(ACTION==1){            
            <?php
            if(!empty($combination)){
                $offerarr = array();
                foreach($orderdata['orderofferproduct'] as $index => $orderofferproduct){
                 
                    $offercombinationid = $orderofferproduct['offercombinationid'];
                    if(!in_array($offercombinationid, $offerarr)) {
                        $purchaseproducts =  explode("$",$orderofferproduct['purchaseproducts']); 
                        $offertype = $orderofferproduct['offertype'];
                        if(count($purchaseproducts) > 0){
                            foreach($purchaseproducts as $k => $pp){ 
                                $purchaseproduct =  explode("|",$pp); ?>
                                var ppid = <?=$purchaseproduct[0]?>;
                                var ppriceid = '<?=$purchaseproduct[1]?>';
                                var pqty = <?=$purchaseproduct[2]?>;
                                var priceidarr = [];
                                if (ppriceid.indexOf(',') > -1) { 
                                    priceidarr = ppriceid.split(',');
                                    // priceidarr.push($("#priceid"+productrowid+" option:selected").attr("data-id"));  
                                }else{
                                    priceidarr.push(ppriceid);
                                }
                                $('select.productid').each(function(index){
                                    var divid = $(this).attr("div-id");
                                    var oproductid = $("#productid"+divid).val();
                                    var opriceid = $("#priceid"+divid+" option:selected").attr("data-id");

                                    if(oproductid!=0 && opriceid != "" && oproductid==ppid && priceidarr.includes(opriceid)){
                                        if(<?=$offertype?> == 1){
                                            var purchaseproductqty = $("#purchaseproductqty"+divid).val();
                                            $("#purchaseproductqty"+divid).val(parseInt(purchaseproductqty) + parseInt(pqty));
                                        }
                                        $("#brandoffer"+divid).val(<?=$offertype?>);
                                        getofferproducts(divid);
                                    }
                                });
                    <?php   }
                        }
                    }
                    $offerarr[] = $offercombinationid;
                } 
            } ?>                                                          
        }   
    });
</script>