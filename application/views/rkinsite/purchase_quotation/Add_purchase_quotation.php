<?php    $productdiscount = 0; ?>
<script>
    var partialpayment = '<?php if(!empty($channelsetting)){ echo $channelsetting['partialpayment']; } ?>';
    var addressid = <?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['addressid']; }else{ echo "0"; } ?>;
    var shippingaddressid = <?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['shippingaddressid']; }else{ echo "0"; } ?>;
    var addquotationtype = <?php if(isset($quotationtype) && $quotationtype==1){ echo '1'; }else{ echo "0"; } ?>;
    var ISDUPLICATE = <?php if(isset($isduplicate) && $isduplicate==1){ echo '1'; }else{ echo "0"; } ?>;

    oldproductid = [];
    oldpriceid = [];
    oldtax = [];
    productdiscount = [];
    oldcombopriceid = [];
    oldprice = [];
    var EMIreceived = 0;
    var productoptionhtml = "";
    var salesproducthtml = "";
    //var PRODUCT_DISCOUNT = '<?= PRODUCTDISCOUNT ?>';
 

    var GSTonDiscount = '<?php //if(isset($gstondiscount)){ echo $gstondiscount; } ?>';
    var globaldicountper = '<?php //if(isset($globaldiscountper)){ echo $globaldiscountper; } ?>';
    var globaldicountamount = '<?php //if(isset($globaldiscountamount)){ echo $globaldiscountamount; } ?>';
    var discountminamount = '<?php //if(isset($discountonbillminamount)){ echo $discountonbillminamount; }else{ echo -1; } ?>';

    var extrachargeoptionhtml = "";
    <?php if(!empty($extrachargesdata)){ 
    foreach($extrachargesdata as $extracharges){ ?>
        extrachargeoptionhtml+='<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>';
    <?php }
    } ?>

    var EDITTAXRATE_SYSTEM = '<?=EDITTAXRATE?>';
    var EDITTAXRATE_CHANNEL = '<?php if(!empty($quotationdata) && isset($quotationdata['quotationdetail']['vendoredittaxrate'])){ echo $quotationdata['quotationdetail']['vendoredittaxrate']; }?>';
</script>
<style> .mt-30{ margin-top: 30px; } 

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
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($quotationdata) && !isset($isduplicate)){ echo 'Edit'; }else{ echo 'Add'; } ?> Purchase <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($quotationdata) && !isset($isduplicate)){ echo 'Edit'; }else{ echo 'Add'; } ?> Purchase <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
                    <form class="form-horizontal" id="purchasequotationform" name="purchasequotationform">
                        <input type="hidden" name="quotationsid" id="quotationsid" value="<?php if(!empty($quotationdata) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['id']; } ?>">
                        <input type="hidden" name="isduplicate" id="isduplicate" value="<?php if(isset($isduplicate) && $isduplicate==1){ echo $isduplicate; } ?>">
                        <div class="row">
                            <input type="hidden" id="quotationtype" name="quotationtype" value="<?php if(isset($quotationtype) && $quotationtype==1){ echo "1"; }else{ echo "0"; } ?>">
                            <input type="hidden" id="oldvendorid" name="oldvendorid" value="<?php if(!empty($quotationdata) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['vendorid']; } ?>">
                            <div class="col-sm-4">
                                <div class="form-group" id="vendor_div">
                                    <div class="col-sm-12 pr-sm">
                                        <label for="vendorid" class="control-label">Select Vendor <span class="mandatoryfield">*</span></label>
                                        <select id="vendorid" name="vendorid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" <?php if(!empty($quotationdata) && !isset($isduplicate)){ echo "disabled"; } ?>>
                                            <option value="0">Select Vendor</option>
                                            <?php foreach($vendordata as $vendor){ ?>
                                                <option data-code="<?=$vendor['membercode']?>" data-billingid="<?=$vendor['billingaddressid']?>" data-shippingid="<?=$vendor['shippingaddressid']?>" value="<?php echo $vendor['id']; ?>" <?php if(!empty($quotationdata['quotationdetail'])){ if($quotationdata['quotationdetail']['vendorid']==$vendor['id']){ echo "selected"; }} ?>><?php echo ucwords($vendor['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <?php if(isset($multiplememberchannel) && $multiplememberchannel==1){?>
                                    <div class="col-sm-2" style="padding-top: 28px !important;<?php if(isset($quotationtype) && $quotationtype==1){ echo "display:none;"; } ?>">
                                        <a href="javascript:void(0)" onclick="resetbuyerform()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Search Buyer <?=Member_label?>" data-toggle="modal" data-target="#addbuyerModal">search</i></a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group" id="billingaddress_div">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label for="billingaddressid" class="control-label">Select Billing Address <span class="mandatoryfield">*</span></label>
                                        <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Billing Address</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group" id="shippingaddress_div">
                                    <div class="col-sm-12 pl-sm">
                                        <label for="shippingaddressid" class="control-label">Select Shipping Address <span class="mandatoryfield">*</span></label>
                                        <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Shipping Address</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" id="quotationid_div">
                                    <div class="col-sm-12 pr-sm">
                                        <label for="quotationid" class="control-label">Quotation ID <span class="mandatoryfield">*</span></label>
                                        <input id="quotationid" type="text" name="quotationid" class="form-control" value="<?php if(!empty($quotationdata['quotationdetail']) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['quotationid']; }else if(!empty($quotationid)){ echo $quotationid; }?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" id="quotationdate_div">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label for="quotationdate" class="control-label">Quotation Date <span class="mandatoryfield">*</span></label>
                                        <div class="input-group">
                                            <input id="quotationdate" type="text" name="quotationdate" value="<?php if(!empty($quotationdata['quotationdetail']) && $quotationdata['quotationdetail']['quotationdate']!="0000-00-00"){ echo $this->general_model->displaydate($quotationdata['quotationdetail']['quotationdate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                            <span class="btn btn-default datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
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
                        <div id="quotationproductdivs">
                        <table id="quotationproducttable" class="table table-hover table-bordered m-n">
                                <thead>
                                    <tr>
                                        <th>Product Name <span class="mandatoryfield">*</span></th>
                                        <th>Select Variant <span class="mandatoryfield">*</span></th>
                                        <th class="width12">Price <span class="mandatoryfield">*</span></th>
                                        <th class="width8">Qty <span class="mandatoryfield">*</span></th>
                                        <th class="width8" style="<?php if($productdiscount==0){ echo "display:none;"; } ?>">Discount</th>
                                        <th class="text-right width8">Tax (%)</th>
                                        <th class="text-right width8">Amount (<?=CURRENCY_CODE?>)</th>
                                        <th class="width8">Action</th>
                                    </tr>
                                </thead>      
                                <tbody>
                                    <?php if(!empty($quotationdata) && !empty($quotationdata['quotationproduct'])) { ?>
                                        <input type="hidden" name="removequotationproductid" id="removequotationproductid">
                                        
                                        <?php for ($i=0; $i < count($quotationdata['quotationproduct']); $i++) { ?>
                                            <tr class="countproducts" id="quotationproductdiv<?=($i+1)?>">
                                                <td>
                                                    <input type="hidden" name="quotationproductsid[]" value="<?=(!isset($isduplicate))?$quotationdata['quotationproduct'][$i]['id']:""?>" id="quotationproductsid<?=$i+1?>">
                                                    <input type="hidden" name="producttax[]" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" id="producttax<?=$i+1?>">
                                                    <input type="hidden" name="productrate[]" value="<?=$quotationdata['quotationproduct'][$i]['price']?>" id="productrate<?=$i+1?>">
                                                    <input type="hidden" name="originalprice[]" value="<?=$quotationdata['quotationproduct'][$i]['originalprice']?>" id="originalprice<?=$i+1?>">
                                                    <input type="hidden" name="uniqueproduct[]" value="<?=$quotationdata['quotationproduct'][$i]['productid']."_".$quotationdata['quotationproduct'][$i]['priceid']?>" id="uniqueproduct<?=$i+1?>">
                                                    <input type="hidden" name="referencetype[]" id="referencetype<?=$i+1?>" value="<?=$quotationdata['quotationproduct'][$i]['referencetype']?>">
                                                    <div class="form-group" id="product<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <select id="productid<?=($i+1)?>" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                                <option value="0">Select Product</option>
                                                                <?php /* foreach($productdata as $product){ ?>
                                                                <option value="<?php echo $product['id']; ?>" <?php if($quotationdata['quotationproduct'][$i]['productid']==$product['id']){ echo "selected"; } ?>><?php echo $product['name']; ?></option>
                                                                <?php } */ ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="price<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <select id="priceid<?=($i+1)?>" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                                <option value="">Select Variant</option>
                                                            </select>
                                                            <div class="form-group m-n p-n" id="applyoldprice<?=($i+1)?>_div">
                                                                <div class="col-sm-12">
                                                                    <div class="checkbox pt-n pl-xs text-left">
                                                                        <input id="applyoldprice<?=($i+1)?>" type="checkbox" value="0" class="checkradios applyoldprice" checked>   
                                                                        <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Apply Old Quotation Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$quotationdata['quotationproduct'][$i]['pricewithtax']?></span></label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="comboprice<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <select id="combopriceid<?=($i+1)?>" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                                <option value="">Price</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="actualprice<?=($i+1)?>_div">
                                                        <div class="col-sm-12">
                                                            <label for="actualprice<?=($i+1)?>" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                            <input type="text" class="form-control actualprice text-right" id="actualprice<?=($i+1)?>" name="actualprice[]" value="<?=$quotationdata['quotationproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value);" style="display: block;" div-id="<?=($i+1)?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="qty<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$quotationdata['quotationproduct'][$i]['quantity']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="<?=($i+1)?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td <?php if($productdiscount==0){ echo "style='display:none;'"; }?>>
                                                    <div class="form-group" id="discount<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <label for="discount<?=($i+1)?>" class="control-label">Dis. (%)</label>
                                                            <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">
                                                            <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="discountinrs<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <label for="discountinrs<?=($i+1)?>" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                            <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="tax<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                            <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                                            if($quotationdata['quotationdetail']['vendoredittaxrate']==1 && EDITTAXRATE==1){ 
                                                                echo ""; 
                                                            }else{ 
                                                                echo "readonly"; 
                                                            }?>>	
                                                            <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['tax']?>" id="ordertax<?=$i+1?>">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group" id="amount<?=($i+1)?>_div">
                                                        <div class="col-md-12">
                                                        <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" readonly="" div-id="<?=($i+1)?>">
                                                        <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">		
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-group pt-sm">
                                                        <div class="col-md-12 pr-n">
                                                            <?php if($i==0){?>
                                                            <?php if(count($quotationdata['quotationproduct'])>1){ ?>
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
                                                </td>
                                            </tr>
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    oldproductid.push(<?=$quotationdata['quotationproduct'][$i]['productid']?>);
                                                    oldpriceid.push(<?=$quotationdata['quotationproduct'][$i]['priceid']?>);
                                                    oldtax.push(<?=$quotationdata['quotationproduct'][$i]['tax']?>);
                                                    productdiscount.push(<?=$quotationdata['quotationproduct'][$i]['discount']?>);
                                                    oldcombopriceid.push(<?=$quotationdata['quotationproduct'][$i]['referenceid']?>);
                                                    oldprice.push(<?=$quotationdata['quotationproduct'][$i]['originalprice']?>);

                                                    $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                                                    getproduct(<?=$i+1?>);
                                                    getproductprice(<?=$i+1?>);
                                                    getmultiplepricebypriceid(<?=$i+1?>);
                                                    calculatediscount(<?=$i+1?>);
                                                    changeproductamount(<?=$i+1?>);
                                                });
                                            </script>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <tr class="countproducts" id="quotationproductdiv1">
                                            <td>
                                                <input type="hidden" name="producttax[]" id="producttax1">
                                                <input type="hidden" name="productrate[]" id="productrate1">
                                                <input type="hidden" name="originalprice[]" id="originalprice1">
                                                <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                                <input type="hidden" name="referencetype[]" id="referencetype1">
                                                <div class="form-group" id="product1_div">
                                                    <div class="col-sm-12">
                                                        <select id="productid1" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                            <option value="0">Select Product</option>
                                                        </select>
                                                    </div>
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
                                                        <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value)" style="display: block;" div-id="1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="qty1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" maxlength="6" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td <?php if($productdiscount==0){ echo "style='display:none;'"; }?>>
                                                <div class="form-group" id="discount1_div">
                                                    <div class="col-md-12">
                                                        <label for="discount1" class="control-label">Dis. (%)</label>
                                                        <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">
                                                        <input type="hidden" value="" id="orderdiscount1">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="discountinrs1_div">
                                                    <div class="col-md-12">
                                                        <label for="discountinrs1" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                        <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">
                                                        <input type="hidden" value="" id="orderdiscount1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="tax1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control text-right tax" id="tax1" name="tax[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" readonly>	
                                                        <input type="hidden" value="" id="ordertax1">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="amount1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value="" readonly="" div-id="1">	
                                                        <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group pt-sm">
                                                    <div class="col-md-12 pr-n">
                                                    <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>		               
                                                <button type="button" class="btn btn-default btn-raised  add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                        </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="col-md-3 p-n">
                                    <div class="col-md-12 pr-n">
                                        <div class="form-group" id="deliverypriority_div">
                                            <div class="col-md-12">
                                                <label for="deliverypriority" class="control-label">Delivery Priority</label>

                                                <?php
                                                $selectedpriority = 1;
                                                if(!empty($quotationdata['quotationdetail'])){
                                                    $selectedpriority = $quotationdata['quotationdetail']['deliverypriority'];
                                                } 
                                                ?>
                                                <select id="deliverypriority" name="deliverypriority" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                    <option value="1" <?=($selectedpriority==1)?"selected":""?>>Medium</option>
                                                    <option value="2" <?=($selectedpriority==2)?"selected":""?>>High</option>
                                                    <option value="3" <?=($selectedpriority==3)?"selected":""?>>Low</option>
                                                    <option value="4" <?=($selectedpriority==4)?"selected":""?>>Urgent</option>
                                                </select>
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
                                    
                                        if(!empty($quotationdata['quotationdetail'])){ 
                                            if($quotationdata['quotationdetail']['globaldiscount'] > 0){
                                                /* if(isset($gstondiscount) && $gstondiscount==1){ 
                                                    $discountper = $quotationdata['quotationdetail']['globaldiscount']*100/ ($quotationdata['quotationdetail']['quotationamount']); 
                                                }else{
                                                    $discountper = $quotationdata['quotationdetail']['globaldiscount']*100/ ($quotationdata['quotationdetail']['quotationamount'] + $quotationdata['quotationdetail']['taxamount']); 
                                                }
                                                if($discountper!=0){
                                                    $discountpercentage = number_format($discountper,2); 
                                                } */
                                                $discountper = $quotationdata['quotationdetail']['globaldiscount']*100/ ($quotationdata['quotationdetail']['quotationamount'] + $quotationdata['quotationdetail']['taxamount']);
                                                $discountpercentage = number_format($discountper,2); 

                                                $discountamnt = $quotationdata['quotationdetail']['globaldiscount']; 
                                                if($discountamnt!=0 || $discountamnt!=0.00){
                                                    $discountamount = number_format($discountamnt,2,'.',''); 
                                                }
                                            }else{
                                                $discountpercentage = $discountamount = '';
                                            }
                                            ?>
                                            <script type="text/javascript">
                                                globaldicountper = "<?=$discountpercentage?>"; 
                                                globaldicountamount = "<?=$discountamount?>"; 
                                            </script>
                                        <?php } ?>
                                        <div class="col-md-6">
                                            <div class="form-group  ml-n mr-n text-right">
                                                <label for="overalldiscountpercent" class="control-label">Discount (%)</label>
                                                <input type="text" class="form-control overalldiscountpercent text-right" id="overalldiscountpercent" name="overalldiscountpercent" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $discountpercentage; } ?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                                <span class="material-input"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-n">
                                            <div class="form-group  ml-n mr-n text-right">
                                                <label for="overalldiscountamount" class="control-label">Discount (<?=CURRENCY_CODE?>)</label>
                                                <input type="text" class="form-control overalldiscountamount text-right" id="overalldiscountamount" name="overalldiscountamount" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $discountamount; } ?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                                <span class="material-input"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pr-n">
                                        <div class="form-group" id="remarks_div">
                                            <div class="col-sm-12">
                                                <label for="remarks" class="control-label">Remarks</label>
                                                <textarea id="remarks" name="remarks" class="form-control"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['remarks']; }?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-9 pull-right p-n">
                                    <div class="col-md-6 pr-xs">
                                        <table class="table table-bordered table-striped" cellspacing="0" width="100%" style="border: 1px solid #e8e8e8;">
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
                                                <?php if(!isset($quotationtype)){ ?>
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
                                                    <th colspan="2" class="text-center">Purchase Quotation Summary (<?=CURRENCY_CODE?>)</th>
                                                </tr>  
                                                <tr>                  
                                                    <th>Total Of Product</th>
                                                    <td class="text-right" width="30%">
                                                        <span id="grossamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['quotationamount']; }else{ echo "0.00"; }?></span>
                                                        <input type="hidden" id="inputgrossamount" name="grossamount" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['quotationamount']; } ?>">
                                                    </td>
                                                </tr>
                                                <tr id="discountrow" style="display:none">                  
                                                    <th>Discount (<span id="discountpercentage"><?php if(!empty($quotationdata['ordquotationdetailrdetail'])){ echo number_format($quotationdata['quotationdetail']['globaldiscount']*100/$quotationdata['quotationdetail']['quotationamount'],2); }else{ echo "0"; }?></span>%)
                                                    </th>
                                                    <td class="text-right">
                                                        <span id="discountamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['globaldiscount']; }else{ echo "0.00"; }?></span>
                                                    </td>
                                                </tr>

                                                <?php 
                                                if(!empty($quotationdata) && !empty($ExtraChargesData)) { ?>
                                                    <?php for ($i=0; $i < count($ExtraChargesData); $i++) { ?>
                                                        <tr class="countcharges" id="countcharges<?=$i+1?>" style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>">                  <th>
                                                                <input type="hidden" name="extrachargemappingid[]" value="<?=(!isset($isduplicate))?$ExtraChargesData[$i]['id']:""?>" id="extrachargemappingid<?=$i+1?>">
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
                                                    <tr class="countcharges" id="countcharges1" style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>">                    
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
                                                <?php } ?>
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
                                                        <span id="netamount" name="netamount"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['payableamount']; }else{ echo "0.00"; } ?></span>
                                                        <input type="hidden" id="inputnetamount" name="netamount" value="<?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['payableamount']; }?>">
                                                    </th>
                                                </tr>  
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group" id="paymenttype_div">
                                    <div class="col-sm-12">
                                        <input type="hidden" name="oldpaymenttype" id="oldpaymenttype" value="<?php if(!empty($quotationdata['quotationdetail']) && !isset($isduplicate)){ echo $quotationdata['quotationdetail']['paymenttype']; } ?>">
                                        <label for="paymenttypeid" class="control-label">Select Payment Type <span class="mandatoryfield">*</span></label>
                                        <select id="paymenttypeid" name="paymenttypeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Payment Type</option>
                                            <option value="1" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==1){ echo "selected"; } }?>>COD</option>
                                            <option value="3" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==3){ echo "selected"; } }?>>Advance Payment</option>
                                            <?php if(HIDE_EMI==0){ ?>
                                            <option value="4" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==4){ echo "selected"; } }?>>EMI Payment</option>
                                            <?php } ?>
                                            <option value="5" <?php if(!empty($quotationdata)){ if($quotationdata['quotationdetail']['paymenttype']==5){ echo "selected"; } }?>>Debit</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($channelsetting) && $channelsetting['partialpayment']==1){ 
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
                            <div class="row">
                                
                                <div class="col-md-12">
                                    <div class="row" id="installmentsetting_div" style="<?php if(!empty($installmentdata) && HIDE_EMI==0){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
                                        <div class="col-sm-2">
                                            <div class="form-group" id="noofinstallment_div">
                                                <div class="col-sm-12">
                                                    <label for="noofinstallment" class="control-label">No. of Installment</label>
                                                    <input type="text" class="form-control" id="noofinstallment" name="noofinstallment" maxlength="2" value="<?php if(!empty($installmentdata)){ echo count($installmentdata); } ?>" onkeypress="return isNumber(event)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="">
                                                <div class="col-sm-12">
                                                    <label for="emidate" class="control-label">EMI Start Date</label>
                                                    <input id="emidate" type="text" name="emidate" value="<?php if(!empty($installmentdata)){ echo $this->general_model->displaydate($installmentdata[0]['date']); } ?>" class="form-control" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="">
                                                <div class="col-sm-12">
                                                    <label for="emiduration" class="control-label">EMI Duration (In Days)</label>
                                                    <input id="emiduration" type="text" name="emiduration" value="<?php if(!empty($installmentdata)){ if(count($installmentdata)==1){ echo "1"; } else{ echo ceil(abs(strtotime($installmentdata[0]['date']) - strtotime($installmentdata[1]['date'])) / 86400);
                                                       } }?>" class="form-control" maxlength="4">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 pt-xxl">
                                            <div class="form-group">
                                                <div class="col-sm-12">
                                                    <button type="button" onclick="generateinstallment()" class="btn btn-primary btn-raised" <?php if($received==1){ echo "disabled"; } ?>>Generate</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="installmentmaindiv" style="margin-top: 12px;<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
                                <div class="row" id="installmentmaindivheading" style="<?php if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
                                    <div class="col-md-1 text-center"><b>Sr.No</b></div>
                                    <div class="col-md-2 text-center"><b>Installment (%)</b></div>
                                    <div class="col-md-2 text-center"><b>Amount</b></div>
                                    <div class="col-md-2 text-center"><b>Installment Date</b></div>
                                    <div class="col-md-2 text-center"><b>Payment Date</b></div>
                                    <div class="col-md-2 text-center"><b>Received Status</b></div>
                                </div>
                            </div>
                            <div id="installmentdivs" style="<?php if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } ?>">
                            
                            <?php if(!empty($installmentdata)){ 
                                    for($i=0; $i < count($installmentdata); $i++){ ?>
                                        <input type="hidden" name="installmentid[]" value="<?=(!isset($isduplicate))?$installmentdata[$i]['id']:""?>">   
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
                                        </div>
                                    <?php } ?>
                            <?php } ?>
                            
                            </div>
                         <?php } ?>
                        <div class="col-sm-12 p-n">
                            <hr>
                            <div class="panel-heading p-n"><h2>Upload Purchase Quotation Documents</h2></div>
                            <div class="row m-n">
                                <div class="col-md-6 p-n" id="filesheading1"> 
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-n">
                                                <label class="control-label">Select File</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 p-n" id="filesheading2" style="<?php if(!empty($quotationattachment) && count($quotationattachment)>1) { echo "display:block;"; }else{ echo "display:none;"; }?>"> 
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-n">
                                                <label class="control-label">Select File</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Remarks</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($quotationdata) && !empty($quotationattachment)) { ?>
                                <input type="hidden" name="removetransactionattachmentid" id="removetransactionattachmentid">
                                <?php for ($i=0; $i < count($quotationattachment); $i++) { ?>
                                    <div class="col-md-6 p-n countfiles" id="countfiles<?=$i+1?>">
                                        <input type="hidden" name="transactionattachmentid<?=$i+1?>" value="<?=$quotationattachment[$i]['id']?>" id="transactionattachmentid<?=$i+1?>">
                                        <div class="col-md-7">
                                            <div class="form-group" id="file<?=$i+1?>_div">
                                                <div class="col-md-12 pl-n">
                                                    <div class="input-group" id="fileupload<?=$i+1?>">
                                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                            <span class="btn btn-primary btn-raised btn-file"><i
                                                                    class="fa fa-upload"></i>
                                                                <input type="file" name="file<?=$i+1?>"
                                                                    class="file" id="file<?=$i+1?>"
                                                                    accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),'file<?=$i+1?>',this)">
                                                            </span>
                                                        </span>
                                                        <input type="text" readonly="" id="Filetext<?=$i+1?>"
                                                            class="form-control" name="Filetext[]" value="<?=$quotationattachment[$i]['filename']?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="fileremarks<?=$i+1?>_div">
                                                <input type="text" class="form-control" name="fileremarks<?=$i+1?>" id="fileremarks<?=$i+1?>" value="<?=$quotationattachment[$i]['remarks']?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-sm pr-sm mt-md">
                                            <?php if($i==0){?>
                                                <?php if(count($quotationattachment)>1){ ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_file_btn" onclick="removeattachfile(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                <?php }else { ?>
                                                    <button type="button" class="btn btn-default btn-raised add_file_btn" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                <?php } ?>

                                            <?php }else if($i!=0) { ?>
                                                <button type="button" class="btn btn-default btn-raised remove_file_btn" onclick="removeattachfile(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-default btn-raised btn-sm remove_file_btn" onclick="removeattachfile(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                        
                                            <button type="button" class="btn btn-default btn-raised add_file_btn" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button> 
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php }else{ ?>
                                <div class="col-md-6 p-n countfiles" id="countfiles1"> 
                                    <div class="col-md-7">
                                        <div class="form-group" id="file1_div">
                                            <div class="col-md-12 pl-n">
                                                <div class="input-group" id="fileupload1">
                                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                        <span class="btn btn-primary btn-raised btn-file"><i
                                                                class="fa fa-upload"></i>
                                                            <input type="file" name="file1"
                                                                class="file" id="file1"
                                                                accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png,.doc,.docx,.pdf" onchange="validattachmentfile($(this),'file1',this)">
                                                        </span>
                                                    </span>
                                                    <input type="text" readonly="" id="Filetext1"
                                                        class="form-control" name="Filetext[]" value="">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="fileremarks1_div">
                                            <input type="text" class="form-control" name="fileremarks1" id="fileremarks1" value="">
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-sm pr-sm mt-md">
                                        <button type="button" class="btn btn-default btn-raised remove_file_btn m-n" onclick="removeattachfile(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                        <button type="button" class="btn btn-default btn-raised add_file_btn m-n" onclick="addattachfile()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col-md-12 mb-sm">
                            <div class="form-group">
                                <label for="focusedinput" class="col-sm-5 control-label"></label>
                                <div class="col-sm-6">
                                    <?php if(!empty($quotationdata) && !isset($isduplicate)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
									<?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                    <?php } ?>
                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL."purchase-quotation"?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                   
                                </div>
                            </div>
                        </div>
                    </form>
				</div>
		      </div>
		    </div>
		  </div>
		</div>
        
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->