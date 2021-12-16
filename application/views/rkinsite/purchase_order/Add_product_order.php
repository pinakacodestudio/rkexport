<script>
    var EMIreceived = 0;
    var PRODUCT_DISCOUNT = '<?=PRODUCTDISCOUNT?>';
    var globaldicountper = '<?php if(isset($globaldiscountper)){ echo $globaldiscountper; } ?>';
    var globaldicountamount = '<?php if(isset($globaldiscountamount)){ echo $globaldiscountamount; } ?>';
    var discountminamount = '<?php if(isset($discountonbillminamount)){ echo $discountonbillminamount; }else{ echo -1; } ?>';
    var EDITTAXRATE_SYSTEM = '<?=EDITTAXRATE?>';
    var EDITTAXRATE_CHANNEL = '<?php if(!empty($orderdata) && isset($orderdata['orderdetail']['vendoredittaxrate'])){ echo $orderdata['orderdetail']['vendoredittaxrate']; }?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1>Add Product <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active">Add Product <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>
    <div class="container-fluid">
        <div data-widget-group="group1">
		    <div class="row">
		        <div class="col-md-12">
		            <div class="panel panel-default border-panel">
		                <div class="panel-body pt-n">
                            <form class="form-horizontal" id="productorderform" name="productorderform">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group" id="product_div">
                                            <label for="productid" class="col-sm-4 control-label">Select Product <span class="mandatoryfield">*</span></label>
                                            <div class="col-sm-7">
                                                <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                    <option value="0">Select Product</option>
                                                    <?php if(!empty($productdata)) { 
                                                        foreach($productdata as $product){ ?>
                                                            <option value="<?=$product['id']?>" data-discount="<?=$product['discount']?>"><?=$product['name']?></option>
                                                    <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group" id="orderdate_div">
                                            <label for="orderdate" class="col-sm-4 control-label">Order Date <span class="mandatoryfield">*</span></label>
                                            <div class="col-sm-7">
                                                <div class="input-group">
                                                    <input id="orderdate" type="text" name="orderdate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" class="form-control" readonly>
                                                    <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12"><hr></div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 p-n">
                                        <div id="orderproductdivs" class="">
                                            <table id="orderproducttable" class="table table-hover table-bordered m-n" style="width: 100%;" >
                                                <thead>
                                                    <tr>
                                                        <th class="width15">Select Vendor <span class="mandatoryfield">*</span></th>
                                                        <th>Billing Address <span class="mandatoryfield">*</span></th>
                                                        <th>Variant <span class="mandatoryfield">*</span></th>
                                                        <th>Select Price (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></th>
                                                        <th class="width8">Qty <span class="mandatoryfield">*</span></th>
                                                        <th class="width8" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; } ?>">Discount</th>
                                                        <th class="text-right width8">Tax (%)</th>
                                                        <th class="text-right width8">Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></th>
                                                        <th class="width8">Action</th>
                                                    </tr>
                                                </thead>                 
                                                <tbody>
                                                    <tr class="countproducts" id="orderproductdiv1">
                                                        <td>
                                                            <input type="hidden" name="producttax[]" id="producttax1">
                                                            <input type="hidden" name="productrate[]" id="productrate1">
                                                            <input type="hidden" name="originalprice[]" id="originalprice1">
                                                            <input type="hidden" name="referencetype[]" id="referencetype1">
                                                            <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                                            <div class="form-group" id="vendor1_div">
                                                                <div class="col-sm-12">
                                                                    <select id="vendorid1" name="vendorid[]" class="selectpicker form-control vendorid" data-width="150px" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                                        <option value="0">Select Vendor</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group" id="billingaddress1_div">
                                                                <div class="col-sm-12">
                                                                    <select id="billingaddressid1" name="billingaddressid[]" data-width="150px" class="selectpicker form-control billingaddressid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                                        <option value="0">Select Address</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group" id="price1_div">
                                                                <div class="col-md-12">
                                                                    <select id="priceid1" name="priceid[]" data-width="150px" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                                        <option value="">Select Variant</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group" id="comboprice1_div">
                                                                <div class="col-sm-12">
                                                                    <select id="combopriceid1" name="combopriceid[]" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                                        <option value="0">Price</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group" id="actualprice1_div">
                                                                <div class="col-sm-12">
                                                                    <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="1">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group" id="qty1_div">
                                                                <div class="col-md-12">
                                                                    <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" maxlength="4" onkeypress="return isNumber(event);" style="display: block;" div-id="1">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>"> 
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
                                                                    <input type="text" class="form-control discountinrs" id="discountinrs1" name="discountinrs[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">	
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
                                                                    <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value="" div-id="1" readonly>
                                                                    <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <button type="button" class="btn btn-default btn-raised add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                                    <button type="button" class="btn btn-default btn-raised add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label class="control-label">Common Remarks for All</label>
                                                <textarea id="remarks" name="remarks" class="form-control"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                            <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL."purchase-order"?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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

