<script>
    var partialpayment = '<?php if(!empty($channelsetting)){ echo $channelsetting['partialpayment']; } ?>';
    var addressid = <?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['addressid']; }else{ echo "0"; } ?>;
    var shippingaddressid = <?php if(!empty($orderdata['orderdetail'])){ echo $orderdata['orderdetail']['shippingaddressid']; }else{ echo "0"; } ?>;
    oldproductid = [];
    oldpriceid = [];
    oldtax = [];
    productdiscount = [];
    oldcombopriceid = [];
    oldprice = [];
    var EMIreceived = 0;
    var PRODUCT_DISCOUNT = '';<?php //PRODUCTDISCOUNT?>
    var salesproducthtml = "";

    var GSTonDiscount = '<?php //if(isset($gstondiscount)){ echo $gstondiscount; } ?>';
    var globaldicountper = '<?php //if(isset($globaldiscountper)){ echo $globaldiscountper; } ?>';
    var globaldicountamount = '<?php //if(isset($globaldiscountamount)){ echo $globaldiscountamount; } ?>';
    var discountminamount = '<?php //if(isset($discountonbillminamount)){ echo $discountonbillminamount; }else{ echo -1; } ?>';

    var approvestatus = <?php if(!empty($orderdata) && isset($orderdata['orderdetail']['approvestatus'])){ echo $orderdata['orderdetail']['approvestatus']; }else{ echo "0"; }?>;
    var extrachargeoptionhtml = "";
    <?php if(!empty($extrachargesdata)){ 
        foreach($extrachargesdata as $extracharges){ ?>
        extrachargeoptionhtml+='<option data-tax="<?php echo $extracharges['tax']; ?>" data-type="<?php echo $extracharges['amounttype']; ?>" data-amount="<?php echo $extracharges['defaultamount']; ?>" value="<?php echo $extracharges['id']; ?>"><?php echo $extracharges['extrachargename']; ?></option>';
    <?php }
    }?>
    var EDITTAXRATE_SYSTEM = '<?=EDITTAXRATE?>';
    var EDITTAXRATE_CHANNEL = '<?php if(!empty($orderdata) && isset($orderdata['orderdetail']['vendoredittaxrate'])){ echo $orderdata['orderdetail']['vendoredittaxrate']; }?>';
    var DEFAULT_COUNTRY_ID = '<?=DEFAULT_COUNTRY_ID?>';

    var ISDUPLICATE = <?php if(isset($isduplicate) && $isduplicate==1){ echo '1'; }else{ echo "0"; } ?>;
    var advancepayment = '<?php if(!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==1 || $orderdata['orderdetail']['paymenttype']==3)){ echo $orderdata['transaction']['payableamount']; } ?>';
</script>
<style>
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
    .popover{
        left: 454.797px !important;
    }
    .popover{
        max-width:600px;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($orderdata) && !isset($isduplicate)){ echo 'Edit'; }else{ echo 'Add'; } ?> Purchase <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($orderdata) && !isset($isduplicate)){ echo 'Edit'; }else{ echo 'Add'; } ?> Purchase <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-n">
                    <form class="form-horizontal" id="purchaseorderform" name="purchaseorderform">
                        <input type="hidden" name="ordersid" id="ordersid" value="<?php if(!empty($orderdata) && !isset($isduplicate)){ echo $orderdata['orderdetail']['id']; } ?>">
                        <input type="hidden" name="quotationid" id="quotationid" value="<?php if(isset($quotationid)){ echo $quotationid; } ?>">
                        <div class="row">
                            <input type="hidden" id="oldvendorid" name="oldvendorid" value="<?php if(!empty($orderdata) && !isset($isduplicate)){ echo $orderdata['orderdetail']['vendorid']; } ?>">
                            <!-- <div class="col-sm-4">
                                <div class="form-group" id="vendor_div">
                                    <div class="col-sm-12 pr-sm">
                                        <label for="vendorid" class="control-label">Select Vendor <span class="mandatoryfield">*</span></label>
                                        <select id="vendorid" name="vendorid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(!empty($orderdata) && !isset($isduplicate)){ echo "disabled"; } ?>>
                                            <option value="0">Select Vendor</option>
                                            <?php foreach($vendordata as $vendor){ ?>
                                                <option data-minimumorderamount="<?php echo $vendor['minimumorderamount']; ?>" data-code="<?php echo $vendor['membercode']; ?>" data-billingid="<?=$vendor['billingaddressid']?>" data-shippingid="<?=$vendor['shippingaddressid']?>" value="<?php echo $vendor['id']; ?>" <?php if(!empty($orderdata['orderdetail'])){ if($orderdata['orderdetail']['vendorid']==$vendor['id']){ echo "selected"; }} ?>><?php echo ucwords($vendor['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                        <?php if(!isset($orderdata)){ ?>
                                            <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(3)"><i class="fa fa-plus"></i> Add New Vendor</a>
                                        <?php } ?>  
                                    </div>
                                </div>
                            </div> -->
                            <div class="col-sm-6">
                                <div class="form-group" id="member_div">
                                    <div class="col-sm-12<?php //if(isset($multiplememberchannel) && $multiplememberchannel==1){ echo "10 pr-n"; }else{ echo "12 pr-sm"; }?>" style="margin: 0px 0px 0px 0px;">
                                        <label for="memberid" class="control-label">Select Party <span class="mandatoryfield">*</span></label>
                                        <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(!empty($quotationdata) && !isset($isduplicate)){ echo "disabled"; } ?>>
                                            <option value="0">Select Party</option>
                                            <?php /* foreach($Partydata as $party){ ?>
                                            <option value="<?=$party['id']?>"><?=$party['id']?></option>
                                            <?php } */?>
                                        </select>
                                    </div>
                                    <?php if(isset($multiplememberchannel) && $multiplememberchannel==1){?>
                                    <!-- <div class="col-sm-2" style="padding-top: 28px !important;">
                                        <a href="javascript:void(0)"class="btn btn-primary btn-raised"><i class="fa fa-plus" title="Add <?php Member_label?>"></i></a>
                                        </div> -->
                                    <!-- <div class="col-md-1 p-n" style="padding-top: 28px !important; margin:0 0 0 7px;">
                                        <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                                    </div> -->
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id="billingaddress_div">
                                    <div class="col-sm-12 pl-sm pr-sm">
                                        <label for="billingaddressid" class="control-label">Employee<span class="mandatoryfield">*</span></label>
                                        <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Employee List</option>
                                        </select>
                                        <!-- <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(1)"><i class="fa fa-plus"></i> Add New Billing Address</a>
                                        <input type="hidden" name="billingaddress" id="billingaddress" value=""> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id="orderid_div">
                                    <div class="col-sm-12">
                                        <label for="orderid" class="control-label">PO ID <span class="mandatoryfield">*</span></label>
                                        <input id="orderid" type="text" name="orderid" class="form-control" value="<?php if(!empty($orderdata['orderdetail']) && !isset($isduplicate)){ echo $orderdata['orderdetail']['orderid']; }else if(!empty($orderid)){ echo $orderid; } ?>" readonly>
                                        <input id="ordernumber" type="hidden" value="<?php if(!empty($orderdata['orderdetail']) && !isset($isduplicate)){ echo $orderdata['orderdetail']['orderid']; }else if(!empty($orderid)){ echo $orderid; } ?>">
                                        <!-- <div class="checkbox">
                                            <input id="editordernumber" type="checkbox" value="1" name="editordernumber" class="checkradios">
                                            <label for="editordernumber">Edit Order ID</label>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id="orderdate_div">
                                    <div class="col-sm-12 pl-sm pr-sm">
                                        <label for="orderdate" class="control-label">PO Date <span class="mandatoryfield">*</span></label>
                                        <input id="orderdate" type="text" name="orderdate" value="<?php if(!empty($orderdata['orderdetail']) && $orderdata['orderdetail']['orderdate']!="0000-00-00"){ echo $this->general_model->displaydate($orderdata['orderdetail']['orderdate']); }else{
                                            echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id="shippingaddress_div">
                                    <div class="col-sm-12">
                                        <label for="shippingaddressid" class="control-label">Select Shipping Address <span class="mandatoryfield">*</span></label>
                                        <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Shipping Address</option>
                                        </select>
                                        <!-- <a href="javascript:void(0)" class="mt-sm" style="float: left;" onclick="openmodal(2)"><i class="fa fa-plus"></i> Add New Shipping Address</a>
                                        <input type="hidden" name="shippingaddress" id="shippingaddress" value=""> -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- <div class="col-sm-3">
                                <div class="form-group" id="productbarcode_div">
                                    <div class="col-sm-12 pl-sm pr-sm">
                                        <label for="productbarcode" class="control-label">Barcode or QR Code</label>
                                        <input id="productbarcode" class="form-control" name="productbarcode" onkeypress="return alphanumeric(event)" maxlength="30">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group pt-xl">
                                    <div class="col-sm-12">
                                        <button type="button" name="sbmtBarcode" id="sbmtBarcode" class="btn btn-primary btn-raised" onclick="checkBarcode()">Submit</button>
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <!-- <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div> -->
                        <br>

                        <?php /*
                        <div class="col-md-12"  id='addproductitem'>
                           <?php 
                              $cloopdoc = 0;
                              $i=0;
                              
                              if(isset($party_docdata[0]->id ) && !empty($party_docdata[0]->id ))  {
                                  foreach ($party_docdata as $row)
                                 {
                                     $i++;
                                     $cloopdoc = $cloopdoc + 1;
                                     $doc_id = $row->id;
                                     $doc=$row->doc;
                                     $docname = $row->docname;
                                 ?>
                           <div id="quotationproductdivs">
                              <table id="quotationproducttable" class="table table-hover table-bordered m-n">
                                 <thead>
                                    <tr>
                                       <th>Category<span class="mandatoryfield">*</span></th>
                                       <th class="">Product <span class="mandatoryfield">*</span></th>
                                       <th class="">Qty <span class="mandatoryfield">*</span></th>
                                       <th class="">Delivery Priarity</th>
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
                                                         <?php 
                                                            $oldproductrate = $quotationdata['quotationproduct'][$i]['originalprice'];
                                                             if(PRICE==1){
                                                                $oldproductrate = $quotationdata['quotationproduct'][$i]['price'];
                                                            }else{
                                                                $oldproductrate = $quotationdata['quotationproduct'][$i]['pricewithtax'];
                                                            } ?>
                                                         <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Apply Old Quotation Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$oldproductrate?></span></label>
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
                                                <!-- <input type="text" class="form-control actualprice text-right" id="actualprice<?php //($i+1)?>" name="actualprice[]" value="<?php //$quotationdata['quotationproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value, 8);" style="display: block;" div-id="<?php //($i+1)?>"> -->
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
                                       <td>
                                          <div class="form-group" id="discount<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                             <div class="col-sm-12">
                                                <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                                <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                                             </div>
                                          </div>
                                          <div class="form-group" id="discountinrs<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                             <div class="col-sm-12">
                                                <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group" id="tax<?=($i+1)?>_div">
                                             <div class="col-sm-12">
                                                <!-- <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                                   //if($quotationdata['quotationdetail']['memberedittaxrate']==1 && EDITTAXRATE==1){ 
                                                   // echo ""; 
                                                   //}else{ 
                                                   // echo "readonly"; 
                                                   // }?>>	
                                                   <input type="hidden" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" id="ordertax<?php //$i+1?>"> -->
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group" id="amount<?=($i+1)?>_div">
                                             <div class="col-sm-12">
                                                <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" readonly="" div-id="<?=($i+1)?>">
                                                <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">		
                                                <span class="material-input"></span>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group pt-sm">
                                             <div class="col-sm-12 pr-n">
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
                                                <select id="productid1" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                   <option value="0">Select Product</option>
                                                </select>
                                             </div>
                                          </div>
                                       </td>
                                       <!-- <td>
                                          <div class="form-group" id="price1_div">
                                              <div class="col-md-12">
                                                  <select id="priceid1" name="priceid[]" data-width="90%" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                      <option value="">Select Variant</option>
                                                  </select>
                                              </div>
                                          </div>
                                          </td> -->
                                       <td>
                                          <div class="form-group" id="comboprice1_div">
                                             <div class="col-sm-12">
                                                <select id="combopriceid1" name="combopriceid[]" data-width="150px" class="selectpicker form-control combopriceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                   <option value="">Price</option>
                                                </select>
                                             </div>
                                          </div>
                                          <!-- <div class="form-group" id="actualprice1_div">
                                             <div class="col-sm-12">
                                                 <label for="actualprice1" class="control-label">Rate (<?=CURRENCY_CODE?>)</label>
                                                 <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="1">
                                             </div>
                                             </div> -->
                                       </td>
                                       <!-- <td>
                                          <div class="form-group" id="qty1_div">
                                              <div class="col-md-12">
                                                  <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" onkeypress="<?php //(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                              </div>
                                          </div>
                                          </td> -->
                                       <!-- <td <?php //if(PRODUCTDISCOUNT==0){ echo "style='display:none;'"; } ?>>
                                          <div class="form-group" id="discount1_div">
                                              <div class="col-md-12">
                                                  <label for="discount1" class="control-label">Dis. (%)</label>
                                                  <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">	
                                                  <input type="hidden" value="" id="orderdiscount1">
                                              </div>
                                          </div>
                                          <div class="form-group" id="discountinrs1_div"> 
                                              <div class="col-md-12">
                                                  <label for="discountinrs1" class="control-label">Dis. (<?php //CURRENCY_CODE?>)</label>
                                                  <input type="text" class="form-control discountinrs" id="discountinrs1" name="discountinrs[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)">	
                                              </div>
                                          </div>
                                          </td> -->
                                       <!-- <td>
                                          <div class="form-group" id="tax1_div"> 
                                              <div class="col-md-12">
                                                  <input type="text" class="form-control text-right tax" id="tax1" name="tax[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" readonly>	
                                                  <input type="hidden" value="" id="ordertax1">
                                              </div>
                                          </div>
                                          </td> -->
                                       <td>
                                          <div class="form-group" id="amount1_div">
                                             <div class="col-md-12">
                                                <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value=""  div-id="1">	
                                                <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group">
                                             <div class="col-md-12 pr-n">
                                                <!-- <button type="button" class="btn btn-default btn-raised  add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>		                -->
                                                <div class="col-md-12 pr-n">
                                                   <div class="form-group" id="deliverypriority_div">
                                                      <div class="col-md-9">
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
                                             </div>
                                          </div>
                                       </td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                           <?php
                              }
                              }else {
                                  $count = 1;
                                  $cloopdoc = 0;
                                  while ($count > $cloopdoc) {
                                      $cloopdoc = $cloopdoc + 1;
                              ?>
                           <div id="quotationproductdivs mb-5">
                              <table id="quotationproducttable" class="panel panel-default border-panel table table-hover table-bordered m-n">
                                 <thead>
                                    <tr>
                                       <th>Category<span class="mandatoryfield">*</span></th>
                                       <th class="">Product <span class="mandatoryfield">*</span></th>
                                       <th class="">Qty <span class="mandatoryfield">*</span></th>
                                       <!-- <th class="">Delivery Priarity</th> -->
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
                                                         <?php 
                                                            $oldproductrate = $quotationdata['quotationproduct'][$i]['originalprice'];
                                                             if(PRICE==1){
                                                                $oldproductrate = $quotationdata['quotationproduct'][$i]['price'];
                                                            }else{
                                                                $oldproductrate = $quotationdata['quotationproduct'][$i]['pricewithtax'];
                                                            } ?>
                                                         <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Apply Old Quotation Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$oldproductrate?></span></label>
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
                                                <!-- <input type="text" class="form-control actualprice text-right" id="actualprice<?php //($i+1)?>" name="actualprice[]" value="<?php //$quotationdata['quotationproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value, 8);" style="display: block;" div-id="<?php //($i+1)?>"> -->
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
                                       <td>
                                          <div class="form-group" id="discount<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                             <div class="col-sm-12">
                                                <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                                <input type="hidden" value="<?=$quotationdata['quotationproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                                             </div>
                                          </div>
                                          <div class="form-group" id="discountinrs<?=($i+1)?>_div" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                             <div class="col-sm-12">
                                                <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)">	
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group" id="tax<?=($i+1)?>_div">
                                             <div class="col-sm-12">
                                                <!-- <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                                   //if($quotationdata['quotationdetail']['memberedittaxrate']==1 && EDITTAXRATE==1){ 
                                                   // echo ""; 
                                                   //}else{ 
                                                   // echo "readonly"; 
                                                   // }?>>	
                                                   <input type="hidden" value="<?php //$quotationdata['quotationproduct'][$i]['tax']?>" id="ordertax<?php //$i+1?>"> -->
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group" id="amount<?=($i+1)?>_div">
                                             <div class="col-sm-12">
                                                <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" readonly="" div-id="<?=($i+1)?>">
                                                <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">		
                                                <span class="material-input"></span>
                                             </div>
                                          </div>
                                       </td>
                                       <td>
                                          <div class="form-group pt-sm">
                                             <div class="col-sm-12 pr-n">
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
                                                <select id="productid1" name="productid[]" data-width="90%" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                   <option value="0">Select Product</option>
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
                                       </td>
                                       <td>
                                          <div class="form-group" id="amount1_div">
                                             <div class="col-md-12">
                                                <input type="text" class="form-control amounttprice" id="amount1" name="amount[]" value=""  div-id="1">	
                                                <input type="hidden" class="producttaxamount" id="producttaxamount1" name="producttaxamount[]" value="" div-id="1">		
                                             </div>
                                          </div>
                                          <div class="col-md-1 p-n" style="float:right; width:3rem; height:3.5rem; margin:10px 0px 0px 0px;">
                                            <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons addnewproductitem" title="Add Unit">add</i></a>
                                        </div>
                                       </td>
                                       <!-- <td>
                                          <div class="form-group">
                                             <div class="col-md-12 pr-n">
                                                <div class="col-md-12 pr-n">
                                                   <div class="form-group" id="deliverypriority_div">
                                                      <div class="col-md-9">
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
                                                         <button type="button" style="float:right; width:3rem; height:3.5rem; margin:10px -35px 0px 0px;" class="addnewproductitem btn-primary"><i class="fa fa-plus"></i></button>
                                                         <div class="col-md-1 p-n" style="float:right; width:3rem; height:3.5rem; margin:10px -35px 0px 0px;">
                                                            <a href="javascript:void(0)" onclick="addcountry()" class="btn btn-primary btn-raised p-xs"><i class="material-icons addnewproductitem" title="Add Unit">add</i></a>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </td> -->
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                              </table>
                           </div>
                           <?php
                              }
                              } 
                              ?>
                        </div>
                        */?>


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
                                        <!-- <th class="width8" style="<?php //if($productdiscount==0){ echo "display:none;"; } ?>">Discount</th> -->
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
                                            <?php /*
                                            <td  //if($productdiscount==0){ echo "style='display:none;'"; }?>>
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
                                            </td> */?>
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


                        <?php /*
                        <div class="row" id="invoicedetailsdiv" style="<?php if(isset($paymentreceiptdata) && $paymentreceiptdata['isagainstreference']==2){ echo "display:none;"; }?>">
                           
                            <div class="row m-n">
                                <!-- <div class="col-md-3">
                                    <div class="form-group" id="invoice_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="invoiceid">Select Invoice <span class="mandatoryfield">*</span></label>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="col-md-3">
                                    <div class="form-group" id="amountdue_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="amountdue">Category</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" id="invoiceamount_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="invoiceamount">Product  <span class="mandatoryfield">*</span></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" id="remainingamount_div">
                                        <div class="col-md-12">								
                                            <label class="control-label" for="remainingamount">Qty</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="removepaymentreceipttransactionsid" id="removepaymentreceipttransactionsid">
                            <?php if(isset($paymentreceiptdata) && !empty($receipttransactionsdata)) { ?>
                               
                            <?php }else{ ?>
                                <div class="countinvoice" id="countinvoice1">
                                    <div class="row m-n">
                                        <!-- <div class="col-md-3">
                                            <div class="form-group" id="invoice1_div">
                                                <div class="col-md-12">								
                                                    <select id="invoiceid1" name="invoiceid[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                        <option value="0">Select Invoice</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div> -->
                                        
                                        <div class="col-md-3">
                                            <div class="form-group" id="amountdue1_div">
                                                <div class="col-md-12">								
                                                <select id="invoiceid1" name="invoiceid[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                        <option value="0">Select Category</option>
                                                    </select>

                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="col-md-2">
                                            <div class="form-group" id="invoiceamount1_div">
                                                <div class="col-md-12">								
                                                <select id="invoiceid1" name="invoiceid[]" class="selectpicker form-control invoiceid" data-live-search="true" data-select-on-tab="true" data-size="6">
                                                        <option value="0">Select Product </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
    
                                        <div class="col-md-2">
                                            <div class="form-group" id="remainingamount1_div">
                                                <div class="col-md-12">								
                                                    <input type="text" id="remainingamount1" class="form-control text-right remainingamount" value="" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 pt-md">
                                            <button type="button" class="btn btn-danger btn-raised  remove_invoice_btn m-n" onclick="removetransaction(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
    
                                            <button type="button" class="btn btn-primary btn-raised add_invoice_btn m-n" onclick="addnewinvoicetransaction()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-sm-12"><hr></div>
                        </div>
                        */?>




                        <!-- <div id="orderproductdivs">
                            <table id="orderproducttable" class="table table-hover table-bordered m-n">
                                <thead>
                                    <tr>
                                        <th>Product Name <span class="mandatoryfield">*</span></th>
                                        <th>Select Variant <span class="mandatoryfield">*</span></th>
                                        <th class="width12">Select Price <span class="mandatoryfield">*</span></th>
                                        <th class="width8">Qty <span class="mandatoryfield">*</span></th>
                                        <th class="width8" style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; } ?>">Discount</th>
                                        <th class="text-right width8">Tax (%)</th>
                                        <th class="text-right width8">Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></th>
                                        <th class="width8">Action</th>
                                    </tr>
                                </thead>                 
                                <tbody>
                                
                                <?php if(!empty($orderdata) && !empty($orderdata['orderproduct'])) { ?>
                                    <input type="hidden" name="removeorderproductid" id="removeorderproductid">
                                    
                                    <?php for ($i=0; $i < count($orderdata['orderproduct']); $i++) { ?>
                                        <tr class="countproducts" id="orderproductdiv<?=($i+1)?>">
                                            
                                            <td>
                                                <input type="hidden" name="orderproductsid[]" value="<?=$orderdata['orderproduct'][$i]['id']?>" id="orderproductsid<?=$i+1?>">
                                                <input type="hidden" name="producttax[]" value="<?=$orderdata['orderproduct'][$i]['tax']?>" id="producttax<?=$i+1?>">
                                                <input type="hidden" name="productrate[]" value="<?=$orderdata['orderproduct'][$i]['price']?>" id="productrate<?=$i+1?>">
                                                <input type="hidden" name="originalprice[]" value="<?=$orderdata['orderproduct'][$i]['originalprice']?>" id="originalprice<?=$i+1?>">
                                                <input type="hidden" name="uniqueproduct[]" value="<?=$orderdata['orderproduct'][$i]['productid']."_".$orderdata['orderproduct'][$i]['priceid']?>" id="uniqueproduct<?=$i+1?>">
                                                <input type="hidden" name="referencetype[]" id="referencetype<?=$i+1?>" value="<?=$orderdata['orderproduct'][$i]['referencetype']?>">
                                                <div class="form-group" id="product<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <select id="productid<?=($i+1)?>" name="productid[]" data-width="150px" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                            <option value="0">Select Product</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="price<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <select id="priceid<?=($i+1)?>" name="priceid[]" data-width="150px" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                            <option value="">Select Variant</option>
                                                        </select>
                                                        <?php $label = "Order"; 
                                                        $oldproductrate = $orderdata['orderproduct'][$i]['originalprice'];?>

                                                        <div class="form-group m-n p-n" id="applyoldprice<?=($i+1)?>_div">
                                                            <div class="col-sm-12">
                                                                <div class="checkbox pt-n pl-xs text-left">
                                                                    <input id="applyoldprice<?=($i+1)?>" type="checkbox" value="0" class="checkradios applyoldprice" checked>
                                                                    <label for="applyoldprice<?=($i+1)?>" class="control-label p-n">Apply Old <?=$label?> Price : <span id="oldpricewithtax<?=($i+1)?>"><?=$oldproductrate?></span></label>
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
                                                        <input type="text" class="form-control actualprice text-right" id="actualprice<?=($i+1)?>" name="actualprice[]" value="<?=$orderdata['orderproduct'][$i]['originalprice']?>" onkeypress="return decimal_number_validation(event, this.value, 8);" style="display: block;" div-id="<?=($i+1)?>">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="qty<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$orderdata['orderproduct'][$i]['quantity']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" div-id="<?=($i+1)?>">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="discount<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <label for="discount1" class="control-label">Dis. (%)</label>
                                                        <input type="text" class="form-control discount" id="discount<?=($i+1)?>" name="discount[]" value="<?=$orderdata['orderproduct'][$i]['discount']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                                        <input type="hidden" value="<?=$orderdata['orderproduct'][$i]['discount']?>" id="orderdiscount<?=$i+1?>">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="discountinrs<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <label for="discountinrs1" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                    <input type="text" class="form-control discountinrs" id="discountinrs<?=($i+1)?>" name="discountinrs[]" value="" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="tax<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <input type="text" class="form-control text-right tax" id="tax<?=($i+1)?>" name="tax[]" value="<?=$orderdata['orderproduct'][$i]['tax']?>" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value)" <?php 
                                                        if($orderdata['orderdetail']['vendoredittaxrate']==1 && EDITTAXRATE==1){ 
                                                            echo ""; 
                                                        }else{ 
                                                            echo "readonly"; 
                                                        }?>>	
                                                        <span class="material-input"></span>
                                                        <input type="hidden" value="<?=$orderdata['orderproduct'][$i]['tax']?>" id="ordertax<?=$i+1?>">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group" id="amount<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <input type="text" class="form-control amounttprice" id="amount<?=($i+1)?>" name="amount[]" value="" div-id="<?=($i+1)?>" readonly>
                                                        <input type="hidden" class="producttaxamount" id="producttaxamount<?=($i+1)?>" name="producttaxamount[]" value="" div-id="<?=($i+1)?>">	
                                                        <span class="material-input"></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group pt-sm">
                                                    <div class="col-md-12 pr-n">
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
                                            </td>

                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    oldproductid.push(<?=$orderdata['orderproduct'][$i]['productid']?>);
                                                    oldpriceid.push(<?=$orderdata['orderproduct'][$i]['priceid']?>);
                                                    oldtax.push(<?=$orderdata['orderproduct'][$i]['tax']?>);
                                                    productdiscount.push(<?=$orderdata['orderproduct'][$i]['discount']?>);
                                                    oldcombopriceid.push(<?=$orderdata['orderproduct'][$i]['referenceid']?>);
                                                    oldprice.push(<?=$orderdata['orderproduct'][$i]['originalprice']?>);

                                                    $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                                                    getproduct(<?=$i+1?>);
                                                    
                                                    getproductprice(<?=$i+1?>);
                                                    getmultiplepricebypriceid(<?=$i+1?>);
                                                    calculatediscount(<?=$i+1?>);
                                                    changeproductamount(<?=$i+1?>);
                                                });
                                            </script>
                                        </div>
                                    <?php } ?>
                                <?php }else{ ?>
                            
                                <tr class="countproducts" id="orderproductdiv1">
                                    <td>
                                        <input type="hidden" name="producttax[]" id="producttax1">
                                        <input type="hidden" name="productrate[]" id="productrate1">
                                        <input type="hidden" name="originalprice[]" id="originalprice1">
                                        <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                        <input type="hidden" name="referencetype[]" id="referencetype1">
                                        <div class="form-group" id="product1_div">
                                            <div class="col-sm-12">
                                                <select id="productid1" name="productid[]" class="selectpicker form-control productid" data-width="90%" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                    <option value="0">Select Product</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group" id="price1_div">
                                            <div class="col-md-12">
                                                <select id="priceid1" name="priceid[]" class="selectpicker form-control priceid" data-width="90%" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                    <option value="">Select Variant</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group" id="comboprice1_div">
                                            <div class="col-sm-12">
                                                <select id="combopriceid1" name="combopriceid[]" class="selectpicker form-control combopriceid" data-width="150px" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                    <option value="">Price</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group" id="actualprice1_div">
                                            <div class="col-sm-12">
                                                <label for="actualprice1" class="control-label">Rate (<?=CURRENCY_CODE?>)</label> 
                                                <input type="text" class="form-control actualprice text-right" id="actualprice1" name="actualprice[]" value="" onkeypress="return decimal_number_validation(event, this.value, 8)" style="display: block;" div-id="1">
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group" id="qty1_div">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                            </div>
                                        </div>
                                    </td>
                                    <td style="<?php if(PRODUCTDISCOUNT==0){ echo "display:none;"; }else{ echo "display:block;"; } ?>">
                                        <div class="form-group" id="discount1_div">
                                            <div class="col-sm-12">
                                                <label for="discount1" class="control-label">Dis. (%)</label>
                                                <input type="text" class="form-control discount" id="discount1" name="discount[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                                <input type="hidden" value="" id="orderdiscount1">
                                            </div>
                                        </div>
                                        <div class="form-group" id="discountinrs1_div">
                                            <div class="col-sm-12">
                                                <label for="discountinrs1" class="control-label">Dis. (<?=CURRENCY_CODE?>)</label>
                                                <input type="text" class="form-control discountinrs" id="discountinrs1" name="discountinrs[]" value="" div-id="1" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
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
                                        <div class="form-group pt-sm">
                                            <div class="col-md-12 pr-n">
                                                <button type="button" class="btn btn-default btn-raised add_remove_btn_product" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-default btn-raised add_remove_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div> -->

                        <!-- <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                        </div> -->
                        <br>

                        <!-- <div class="row">
                            <div class="col-md-12 p-n">
                                <div class="col-md-3 p-n">
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
                                            ?>
                                            <script type="text/javascript">
                                                globaldicountper = "<?=$discountpercentage?>"; 
                                                globaldicountamount = "<?=$discountamount?>"; 
                                            </script>
                                        <?php 
                                        } 
                                    ?>
                                    
                                    <div class="col-sm-6">
                                        <div class="form-group  ml-n mr-n text-right">
                                            <label for="overalldiscountpercent" class="control-label">Discount (%)</label>
                                            <input type="text" class="form-control text-right overalldiscountpercent" id="overalldiscountpercent" name="overalldiscountpercent" value="<?php if(!empty($orderdata['orderdetail'])){ echo $discountpercentage; } ?>" onkeypress="return decimal_number_validation(event, this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                            <span class="material-input"></span>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 p-n">
                                        <div class="form-group ml-n mr-n text-right">
                                            <label for="overalldiscountamount" class="control-label">Discount (<?=CURRENCY_CODE?>)</label>
                                            <input type="text" class="form-control text-right overalldiscountamount" id="overalldiscountamount" name="overalldiscountamount" value="<?php if(!empty($orderdata['orderdetail'])){ echo $discountamount; } ?>" onkeypress="return decimal_number_validation(event,this.value)" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; }?>>	
                                            <span class="material-input"></span>
                                        </div>
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
                                                <?php if(!isset($ordertype)){ 
                                                if(!empty($orderdata) && !empty($ExtraChargesData)) { ?>
                                                    <?php for ($i=0; $i < count($ExtraChargesData); $i++) { ?>
                                                        <tr class="countcharges" id="countcharges<?=$i+1?>" style="<?=HIDE_PURCHASE_EXTRA_CHARGES==1?"display: none;":""?>">                  <th>
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
                        </div> -->
                        <?php if(!isset($orderdata) || isset($isduplicate)){ ?>
                        <div class="row">
                            <!-- <div class="col-sm-3">
                                <div class="form-group" id="invoiceno_div">
                                    <div class="col-sm-12">
                                        <label for="invoiceno" class="control-label">Invoice No.</label>
                                        <input id="invoiceno" type="text" name="invoiceno" class="form-control" value="">
                                    </div>
                                </div>
                            </div> -->
                            <div class="col-sm-3">
                                <div class="form-group" id="quotationdate_div">
                                    <div class="col-sm-12">
                                    <label for="quotationdate" class="control-label">Select Approx Delivery Date <span class="mandatoryfield">*</span></label>
                                    <div class="input-group">
                                        <input id="quotationdate" type="text" name="quotationdate" value="<?php if(!empty($quotationdata['quotationdetail']) && $quotationdata['quotationdetail']['quotationdate']!="0000-00-00"){ echo $this->general_model->displaydate($quotationdata['quotationdetail']['quotationdate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                        <span class="btn btn-default datepicker_calendar_button"><i class="fa fa-calendar fa-lg"></i></span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group mt-xl" id="generateinvoice_div">
                                    <div class="col-sm-12">
                                        <div class="checkbox">
                                            <input id="generateinvoice" type="checkbox" value="1" name="generateinvoice" class="checkradios">
                                            <label for="generateinvoice">LUT Order</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        <!-- <div class="row">
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
                                        <input id="transactionid" type="text" name="transactionid" class="form-control" value="<?php if(!empty($orderdata['transaction']) && $orderdata['orderdetail']['paymenttype']==3){ echo $orderdata['transaction']['transactionid']; } ?>">
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
                                        <input id="advancepayment" data-calculate="<?php if(isset($ordertype) && $ordertype==1){ echo 'true'; }else{ echo 'false';} ?>" type="text" name="advancepayment" class="form-control text-right" value="<?php if(!empty($orderdata['transaction']) && ($orderdata['orderdetail']['paymenttype']==1 || $orderdata['orderdetail']['paymenttype']==3)){ echo $orderdata['transaction']['payableamount']; } ?>" <?php if(isset($ordertype) && $ordertype==1){ echo "readonly"; } ?>>
                                        <input type="hidden" id="channeladvancepaymentcod" value="<?php if(isset($ordertype) && $ordertype==1){ echo $channelsettings['advancepaymentcod']; }else{ echo '0';} ?>">
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
                                <div class="row" id="partialpaymentoption" style="<?php if(!empty($orderdata['orderdetail']) && $orderdata['orderdetail']['paymenttype']==4 && HIDE_EMI==0){  echo "display:block;"; } else{ echo "display:none;"; } ?>">
                                    
                                    <div class="col-md-12">
                                        <div class="row m-n" id="installmentsetting_div" style="<?php /* if(HIDE_EMI==0){ echo "display:block;"; } else{ echo "display:none;"; } */ ?>">
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
                        </div> -->
                        <div class="row">
                            <div class="col-sm-6" style="margin: 0px 0 0px 0px;">
                                <div class="form-group" id="remarks_div">
                                <div class="col-sm-12">
                                    <label for="remarks" class="control-label">Remarks</label>
                                    <textarea id="remarks" name="remarks" class="form-control"><?php if(!empty($quotationdata['quotationdetail'])){ echo $quotationdata['quotationdetail']['remarks']; }?></textarea>
                                </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-sm-12">
                            <div class="panel-heading p-n"><h2>Upload Purchase Order Documents</h2></div>
                            <div class="row m-n">
                                <div class="col-md-6 p-n" id="filesheading1"> 
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-n">
                                                <label class="control-label">Select File</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 p-n" id="filesheading2" style="<?php if(!empty($orderattachment)) { echo "display:block;"; }else{ echo "display:none;"; }?>"> 
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
                            <?php if(!empty($orderdata) && !empty($orderattachment)) { ?>
                                <input type="hidden" name="removetransactionattachmentid" id="removetransactionattachmentid">
                                <?php for ($i=0; $i < count($orderattachment); $i++) { ?>
                                    <div class="col-md-6 p-n countfiles" id="countfiles<?=$i+1?>">
                                        <input type="hidden" name="transactionattachmentid<?=$i+1?>" value="<?=$orderattachment[$i]['id']?>" id="transactionattachmentid<?=$i+1?>">
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
                                                            class="form-control" name="Filetext[]" value="<?=$orderattachment[$i]['filename']?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="fileremarks<?=$i+1?>_div">
                                                <input type="text" class="form-control" name="fileremarks<?=$i+1?>" id="fileremarks<?=$i+1?>" value="<?=$orderattachment[$i]['remarks']?>">
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-sm pr-sm mt-md">
                                            <?php if($i==0){?>
                                                <?php if(count($orderattachment)>1){ ?>
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
                        </div> -->

                        <!-- <div class="col-md-12">
                            <div class="form-group text-center">
                                <?php if(!empty($orderdata) && !isset($isduplicate)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL."purchase-order"?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                            </div>
                        </div> -->
                        <br>
                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <div class="panel panel-default border-panel">
                                    <div class="panel-heading">
                                        <h2>Actions</h2>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="form-group text-center">
                                                <div class="col-md-12 col-xs-12">
                                                <?php if (!empty($partydata)) { ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                <?php } else { ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                <?php } ?>
                                                <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL.$this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
<!-- <div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="panel panel-default border-panel">
        <div class="panel-heading">
            <h2>Actions</h2>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="form-group text-center">
                    <div class="col-md-12 col-xs-12">
                    <?php if (!empty($partydata)) { ?>
                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                    <?php } else { ?>
                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                    <?php } ?>
                    <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL.$this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div> -->

<!-- <div class="modal fade" id="addressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h4 class="modal-title">Add Address</h4>
            </div>
            <div class="modal-body pt-sm">
                <form class="form-horizontal" id="vendoraddressform">

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
                            <input type="button" id="addressbtn" onclick="vendoraddresscheckvalidation()"
                                name="submit" value="ADD" class="btn btn-primary btn-raised">
                            <a href="javascript:voi(0)" class="btn btn-info btn-raised"
                                onclick="vendoraddressresetdata()">RESET</a>
                            <a class="<?=cancellink_class;?>" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="addnewvendorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                <h4 class="modal-title">Add New Vendor</h4>
            </div>
            <div class="modal-body pt-sm">
                <form class="form-horizontal" id="addnewvendorform">

                    <div class="col-md-6">
                        <input id="newchannelid" name="newchannelid" type="hidden" value="<?=VENDORCHANNELID?>">
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
                            <input type="button" id="addmemberbtn" onclick="addNewVendor()"
                                name="submit" value="ADD" class="btn btn-primary btn-raised">
                            <a href="javascript:void(0)" class="btn btn-info btn-raised"
                                onclick="resetNewVendorForm()">RESET</a>
                            <a class="<?=cancellink_class;?>" href="javascript:void(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div> -->
