<script type="text/javascript">
  var DEFAULT_IMG = '<?=DEFAULT_IMG?>';
  var allattribute = <?php echo json_encode($attributedata);?>;
</script>
<style type="text/css">
  .productvariantdiv{
    box-shadow: 0px 1px 6px #333;
    padding: 10px;
    margin-bottom: 20px;
  }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?> Variant</h1>                    
        <small>
              <ol class="breadcrumb">                        
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
                <li class="active"><?=$this->session->userdata(base_url().'submenuname')?> Variant</li>
              </ol>
      </small>
    </div>

    <div class="container-fluid">
                                    
    <div data-widget-group="group1">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="col-sm-12 col-md-12 col-lg-12 p-n">
              <h4 class="mt-n"><?=$this->session->userdata(base_url().'submenuname')?> : <?=(!empty($productdata))?$productdata['name']:''?></h4>
                <form id="productvariantform">
                  <div id="allprices_div">
                  <input type="hidden" name="productid" value="<?=$productid?>">
                  <input type="hidden" name="producttype" value="<?=(!empty($productdata))?$productdata['producttype']:''?>">
                  <?php
                  if(count($productprices)>0){
                  ?>
                    <input type="hidden" name="allprices_count" value="<?=count($productprices)?>" id="allprices_count">
                  <?php
                  }else{
                  ?>
                    <input type="hidden" name="allprices_count" value="1" id="allprices_count">
                  <?php
                  }
                  ?>
                  <?php
                  if(count($productprices)>0){
                  ?>
                      <?php foreach ($productprices as $k=>$p) {
                      ?>
                      <div class="productvariantdiv border-panel" id="maindiv<?=$k?>" style="position: relative;">
                        
                        <input type="hidden" name="priceid[<?=$k?>]" value="<?=$p['id']?>">
                        <?php if($k!=0){ ?>
                        <div class="row m-n" style="position: absolute;right: 0;">
                          <div class="pr-sm">
                            <button type="button" class="btn btn-raised btn-danger btn-sm pull-right" onclick="removemainprice('maindiv<?=$k?>')"><i class="fa fa-remove"></i> REMOVE</button>
                          </div>
                        </div>
                        <?php } ?>
                        <div class="row m-n">
                          <div class="col-md-2 pr-xs pl-xs" style="<?=($p['pricetype']==1?"display: none;":"")?>">
                            <div class="form-group" for="price<?=$k?>" id="price_div<?=$k?>">
                                <label class="control-label" for="price<?=$k?>">Price <?=$k+1?> <span class="mandatoryfield">*</span></label>
                                <input type="text" id="price<?=$k?>" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="Price" name="price[<?=$k?>]" value="<?=($p['pricetype']==0 && !empty($p['productquantitypricesdata'])?$p['productquantitypricesdata'][0]['price']:"")?>">
                                <input type="hidden" name="singlequantitypricesid[<?=$k?>]" value="<?=($p['pricetype']==0 && !empty($p['productquantitypricesdata'])?$p['productquantitypricesdata'][0]['id']:"")?>">
                            </div>
                          </div>
                          <!-- <div class="col-md-1 pr-xs pl-xs" style="<?=($p['pricetype']==1?"display: none;":"")?>">
                            <div class="form-group text-right" for="discount<?=$k?>" id="discount_div<?=$k?>">
                                <label class="control-label" for="discount<?=$k?>">Disc. (%)</label>
                                <input type="text" id="discount<?=$k?>" onkeypress="return decimal_number_validation(event,this.value)" class="form-control discount" name="discount[<?=$k?>]" value="<?=($p['pricetype']==0 && !empty($p['productquantitypricesdata'])?$p['productquantitypricesdata'][0]['discount']:"")?>" onkeyup="return onlypercentage(this.id)">
                            </div>
                          </div> -->
                          <div class="col-md-1 pr-xs pl-xs">
                            <div class="form-group" for="stock" id="stock_div<?=$k?>">
                               <label class="control-label" for="stock<?=$k?>"> Stock
                               <span class="mandatoryfield"> * </span></label>
                               <input type="text" id="stock<?=$k?>" onkeypress="return isNumber(event)" class="form-control stocks" placeholder="Stock" name="stock[<?=$k?>]" value="<?=$p['stock']?>">
                            </div>
                          </div>
                          <!-- <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="pointsforseller" id="pointsforseller_div<?=$k?>">
                               <label class="control-label" for="pointsforseller<?=$k?>">Points for Seller</label>
                               <input type="text" id="pointsforseller<?=$k?>" onkeypress="return isNumber(event)" class="form-control pointsforseller" placeholder="" name="pointsforseller[<?=$k?>]" value="<?=($p['pointsforseller']>0)?$p['pointsforseller']:''?>">
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" id="pointsforbuyer_div<?=$k?>">
                               <label class="control-label" for="pointsforbuyer<?=$k?>">Points for Buyer</label>
                               <input type="text" id="pointsforbuyer<?=$k?>" onkeypress="return isNumber(event)" class="form-control pointsforbuyer" placeholder="" name="pointsforbuyer[<?=$k?>]" value="<?=($p['pointsforbuyer']>0)?$p['pointsforbuyer']:''?>">
                            </div>
                          </div> -->
                          <!-- <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="sku" id="sku_div<?=$k?>">
                               <label class="control-label" for="sku<?=$k?>">SKU
                               <span class="mandatoryfield"> * </span></label>
                               <input type="text" id="sku<?=$k?>" class="form-control sku" name="sku[<?=$k?>]" value="<?=$p['sku']?>">
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="minimumsalesprice" id="minimumsalesprice_div<?=$k?>">
                               <label class="control-label" for="minimumsalesprice<?=$k?>">Min. Sales Price</label>
                               <input type="text" id="minimumsalesprice<?=$k?>" class="form-control minimumsalesprice" name="minimumsalesprice[<?=$k?>]" value="<?=$p['minimumsalesprice']?>">
                            </div>
                          </div>
                          <?php if($k==0){ ?>
                            <div class="col-md-2">
                              <button type="button" class="btn btn-raised btn-primary btn-sm pull-right stickey-addnewpricebtn" id="addnewprice"><i class="fa fa-plus"></i> ADD NEW PRICE</button>
                            </div>
                          <?php } ?>
                        </div> -->
                        <div class='clearfix'></div>
                        <div class="row m-n">
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" id="minimumstocklimit_div<?=$k?>">
                               <label class="control-label" for="minimumstocklimit<?=$k?>">Min. Stock Limit</label>
                               <input type="text" id="minimumstocklimit<?=$k?>" class="form-control minimumstocklimit" name="minimumstocklimit[<?=$k?>]" value="<?=$p['minimumstocklimit']?>" onkeypress="return isNumber(event)" maxlength="4">
                            </div>
                          </div>
                          <div class="col-md-1 pr-xs pl-xs">
                            <div class="form-group" id="weight_div<?=$k?>">
                               <label class="control-label" for="weight<?=$k?>">Weight (kg)</label>
                               <input type="text" id="weight<?=$k?>" class="form-control weight" name="weight[<?=$k?>]" value="<?=$p['weight']?>" onkeypress="return decimal_number_validation(event,this.value,6,3)" >
                            </div>
                          </div>
                          <div class="col-md-3 pr-xs pl-xs">
                            <div id="orderquantitydiv">
                              <div class="col-md-6 p-n">
                                <div class="form-group pb-n" style="margin-top: 7px">
                                  <div class="col-md-12 pr-xs pl-n">
                                    <label class="control-label" for="minimumorderqty<?=$k?>">Min. Order Qty</label>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 p-n">
                                <div class="form-group pb-n" style="margin-top: 7px;">
                                  <div class="col-md-12 pl-xs pr-n">
                                    <label class="control-label" for="maximumorderqty<?=$k?>">Max. Order Qty</label>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 p-n">
                                <div class="form-group" id="minimumorderqty_div<?=$k?>">
                                  <div class="col-md-12 pr-xs pl-n">
                                    <input type="text" id="minimumorderqty<?=$k?>" class="form-control m-n" name="minimumorderqty[<?=$k?>]" value="<?=$p['minimumorderqty']?>" onkeypress="return isNumber(event)" maxlength="4">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 p-n">
                                <div class="form-group" id="maximumorderqty_div<?=$k?>">
                                  <div class="col-md-12 pl-xs pr-n">
                                    <input type="text" id="maximumorderqty<?=$k?>" class="form-control m-n" name="maximumorderqty[<?=$k?>]" value="<?=$p['maximumorderqty']?>" onkeypress="return isNumber(event)" maxlength="4">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" id="barcode_div<?=$k?>">
                              <label class="control-label" for="barcode<?=$k?>">Barcode <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-12 p-n">
                                <div class="col-md-11 pl-n">
                                  <input type="text" id="barcode<?=$k?>" class="form-control barcode" name="barcode[<?=$k?>]" value="<?=$p['barcode']?>" onkeypress="return alphanumeric(event)" maxlength="30">
                                  <input type="hidden" id="oldbarcode<?=$k?>" value="<?=$p['barcode']?>">
                                </div>
                                <div class="col-sm-1 p-n" style="padding-top: 5px !important;">
                                  <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised btn-sm" title="Generate Barcode" onclick="generateBarcode(<?=$k?>)" style="padding: 9px 14px;"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4 pr-xs pl-xs pt-sm">
                            <div class="form-group text-center" id="barcodeimage_div<?=$k?>">
                              <label class="control-label"></label>
                              <div class="col-sm-12 pt-sm p-n">
                                  <img id="barcodeimg<?=$k?>" src="<?=ADMIN_URL.'product/set_barcode/'.$p['barcode'];?>" style="max-width: 100%;">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row m-n">
                          <?php if(isset($productcombination[$p['id']])){ ?>
                            <input type="hidden" id="variant_count<?=$k?>" name="variant_count[<?=$k?>]" value="<?=count($productcombination[$p['id']])?>">
                            <div id="variant_div<?=$k?>">
                            <?php foreach ($productcombination[$p['id']] as $pckey => $pc) { ?>
                              <input type="hidden" name="availablevariantid[<?=$k?>][<?=$pckey?>]" value="<?=$pc['id']?>">
                              
                              <div class="col-md-6" id="variants<?=$k.$pckey?>" style="padding: 0;">
                                <div class="col-md-5 pr-xs pl-xs">
                                  <div class="form-group" id="attributediv<?=$k.$pckey?>">
                                  <label class="control-label" for="attributeid<?=$k.$pckey?>">
                                  Attribute <span class="mandatoryfield">*</span></label>
                                    <select id="attributeid<?=$k.$pckey?>" name="attributeid[<?=$k?>][<?=$pckey?>]" class="selectpicker form-control attributeids" div-id="<?=$k?>" attribute-id="<?=$pckey?>" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" onchange="loadvariant('<?=$k?>','<?=$pckey?>')">
                                      <option value="0">Select Attribute</option>
                                      <?php foreach($attributedata as $row){ ?>
                                        <option value="<?php echo $row['id']; ?>" <?php  if($pc['attributeid'] == $row['id']){ echo 'selected'; } ?>><?php echo $row['variantname']; ?></option>
                                      <?php } ?>
                                    </select>
                                </div>
                                </div>
                                <div class="col-md-5 pr-xs pl-xs">
                                  <div class="form-group" id="variantdiv<?=$k.$pckey?>">
                                  <label class="control-label" for="variantid<?=$k.$pckey?>">Variant <span class="mandatoryfield">*</span></label>
                                      <select id="variantid<?=$k.$pckey?>" name="variantid[<?=$k?>][<?=$pckey?>]" class="selectpicker form-control variantids" data-live-search="true" variant-value="<?=$pc['variantid']?>" div-id="<?=$k?>" data-select-on-tab="true" data-size="5" tabindex="8">
                                        <option value="0">Select Variant</option>
                                      </select>
                                  </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 34px;padding: 0;">
                                <?php if ($pckey == 0) { ?>
                                    <?php if (count($productcombination[$p['id']]) > 1) { ?>
                                      <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" onclick="removevariant('variants<?=$k.$pckey?>',<?=$k?>)" style=""><i class="fa fa-minus"></i></button>
                                    <?php } else { ?>
                                  <?php }
                                  } else if ($pckey != 0) { ?>
                                      <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" onclick="removevariant('variants<?=$k.$pckey?>',<?=$k?>)" style=""><i class="fa fa-minus"></i></button>
                                  <?php } ?>
                                  
                                  <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" variant-div="<?=$k?>" style="display:none;"><i class="fa fa-minus"></i></button>
                                  <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="<?=$k?>" style=""><i class="fa fa-plus"></i></button>
                                  <?php
                                  /* if($pckey==0){
                                  ?>
                                    <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="<?=$k?>"><i class="fa fa-plus"></i></button>
                                  <?php
                                  }else{
                                  ?>
                                    <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn" onclick="removevariant('variants<?=$k.$pckey?>')"><i class="fa fa-remove"></i></button>
                                  <?php
                                  } */
                                  ?>
                                </div>
                              </div>
                                  
                            <?php } ?>
                            </div>
                          <?php }else{ ?>
                            <input type="hidden" id="variant_count<?=$k?>" name="variant_count[<?=$k?>]" value="1">
                            <div id="variant_div<?=$k?>">
                              <div class="col-md-6" style="padding: 0;" id="variants<?=$k?>0">
                                <div class="col-md-5 pl-xs pr-xs">
                                  <div class="form-group" id="attributediv<?=$k?>0"><label class="control-label" for="attributeid<?=$k?>0">Attribute <span class="mandatoryfield">*</span></label>
                                    <select id="attributeid<?=$k?>0" name="attributeid[<?=$k?>][0]" class="selectpicker form-control attributeids" div-id="<?=$k?>" attribute-id="0" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" onchange="loadvariant('<?=$k?>','0')">
                                      <option value="0">Select Attribute</option>
                                      <?php foreach($attributedata as $row){ ?>
                                        <option value="<?php echo $row['id']; ?>" <?php if(isset($variantdata)){ if($variantdata['attributeid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['variantname']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-5 pl-xs pr-xs">
                                  <div class="form-group" id="variantdiv<?=$k?>0"><label class="control-label" for="variantid<?=$k?>0">Variant <span class="mandatoryfield">*</span> </label>
                                      <select id="variantid<?=$k?>0" name="variantid[<?=$k?>][0]" class="selectpicker form-control variantids" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" div-id="<?=$k?>">
                                        <option value="0">Select Variant</option>
                                      </select>
                                  </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 34px;padding: 0;">
                                  <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" variant-div="<?=$k?>" style="display:none;"><i class="fa fa-minus"></i></button>
                                  <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="<?=$k?>" style=""><i class="fa fa-plus"></i></button>
                                </div>  
                              </div>
                            </div>
                          <?php } ?>
                        </div>
                        <div class="row">
                          <!-- <div class="col-md-6 pr-xs pl-xs">
                            <div class="form-group">
                              <label for="focusedinput" class="col-sm-3 control-label pt-xs">Price Type</label>
                              <div class="col-sm-9">
                                <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                  <div class="radio">
                                    <input type="radio" name="pricetype<?=$k?>" id="singleqty<?=$k?>" class="pricetype" value="0" <?php if($p['pricetype']==0){ echo "checked"; } ?>>
                                    <label for="singleqty<?=$k?>">Single Quantity</label>
                                  </div>
                                </div>
                                <div class="col-sm-6 col-xs-6 p-n">
                                  <div class="radio">
                                    <input type="radio" name="pricetype<?=$k?>" id="multipleqty<?=$k?>" class="pricetype" value="1" <?php if($p['pricetype']==1){ echo "checked"; } ?>>
                                    <label for="multipleqty<?=$k?>">Multiple Quantity</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div> -->
                          <!-- <?php if($p['addpriceinpricelist']==1){ ?>
                            <div class="col-md-6 p-n" id="addpriceinpricelist_div<?=$k?>">
                              <div class="form-group">
                                <div class="col-sm-8">
                                  <div class="checkbox text-left">
                                    <input id="addpriceinpricelist<?=$k?>" name="addpriceinpricelist<?=$k?>" type="checkbox" checked>
                                    <label for="addpriceinpricelist<?=$k?>">Add Price in Price List</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          <?php } ?> -->
                        </div>
                            
                        <div class="row" id="multiplepricesection<?=$k?>" style="<?=($p['pricetype']==0?"display: none;":"")?>">
                          <div class="col-md-12"><hr></div>
                          <div class="col-md-12 p-n">
                            <div id="headingmultipleprice_<?=$k?>_1" class="co l-md-6 headingmultipleprice<?=$k?>">
                              <div class="col-md-4 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantprice_<?=$k?>_1">Price <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-3 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantqty_<?=$k?>_1">Quantity <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-2 pr-xs pl-xs">
                                <div class="form-group text-right">
                                  <label class="control-label" for="variantdiscpercent_<?=$k?>_1">Disc. (%)</label>
                                </div>
                              </div>
                            </div>
                            <div id="headingmultipleprice_<?=$k?>_2" class="col-md-6 headingmultipleprice<?=$k?>" style="<?=(count($p['productquantitypricesdata'])<=1)?"display:none;":"";?>">
                              <div class="col-md-4 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantprice_<?=$k?>_1">Price <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-3 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantqty_<?=$k?>_1">Quantity <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-2 pr-xs pl-xs">
                                <div class="form-group text-right">
                                  <label class="control-label" for="variantdiscpercent_<?=$k?>_1">Disc. (%)</label>
                                </div>
                              </div>
                            </div>
                          </div>

                          <?php if($p['pricetype']==1 && !empty($p['productquantitypricesdata'])) {
                            foreach($p['productquantitypricesdata'] as $j=>$productquantityprice){ 
                              $index = $j+1; ?>
                              <div id="countmultipleprice_<?=$k?>_<?=$index?>" class="col-md-6 countmultipleprice<?=$k?>">
                              
                                <input type="hidden" name="productquantitypricesid[<?=$k?>][]" value="<?=$productquantityprice['id']?>">
                                <div class="col-md-4 pr-xs pl-xs">
                                  <div class="form-group" for="variantprice_<?=$k?>_<?=$index?>" id="variantprice_div_<?=$k?>_<?=$index?>">
                                    <input type="text" id="variantprice_<?=$k?>_<?=$index?>" onkeypress="return decimal(event,this.value)" class="form-control variantprices<?=$k?>" name="variantprice[<?=$k?>][]" value="<?=$productquantityprice['price']?>">
                                  </div>
                                </div>
                                <div class="col-md-3 pr-xs pl-xs">
                                  <div class="form-group" for="variantqty_<?=$k?>_<?=$index?>" id="variantqty_div_<?=$k?>_<?=$index?>">
                                    <input type="text" id="variantqty_<?=$k?>_<?=$index?>" onkeypress="return isNumber(event)" class="form-control variantqty<?=$k?>" name="variantqty[<?=$k?>][]" value="<?=$productquantityprice['quantity']?>" maxlength="4">
                                  </div>
                                </div>
                                <div class="col-md-2 pr-xs pl-xs">
                                  <div class="form-group text-right" for="variantdiscpercent_<?=$k?>_<?=$index?>" id="variantdiscpercent_div_<?=$k?>_<?=$index?>">
                                    <input type="text" id="variantdiscpercent_<?=$k?>_<?=$index?>" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent<?=$k?>" name="variantdiscpercent[<?=$k?>][]" value="<?=$productquantityprice['discount']?>" onkeyup="return onlypercentage(this.id)">
                                  </div>
                                </div>
                                <div class="col-md-3 mt-xs">
                                  <?php if ($j == 0) { ?>
                                   <?php if (count($p['productquantitypricesdata']) > 1) { ?>
                                      <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$k?>" onclick="removevariantprice(<?=$k?>,1)" style=""><i class="fa fa-minus"></i></button>
                                    <?php }else { ?>
                                        <button type="button" class="btn btn-default btn-raised add_variantprice<?=$k?>" onclick="addnewvariantprice(<?=$k?>)"><i class="fa fa-plus"></i></button>
                                    <?php } 
                                  } else if ($j != 0) { ?>
                                      <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$k?>" onclick="removevariantprice(<?=$k?>,<?=$index?>)"><i class="fa fa-minus"></i></button>
                                  <?php } ?>
                                  
                                  <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$k?>" onclick="removevariantprice(<?=$k?>,<?=$index?>)" style="display:none;"><i class="fa fa-minus"></i></button>
                                  <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice<?=$k?>" onclick="addnewvariantprice(<?=$k?>)"><i class="fa fa-plus"></i></button>
                                </div> 
                              </div>
                            <?php }
                          }else{ ?>
                            <div id="countmultipleprice_<?=$k?>_1" class="col-md-6 countmultipleprice<?=$k?>">
                              <div class="col-md-4 pr-xs pl-xs">
                                <div class="form-group" for="variantprice_<?=$k?>_1" id="variantprice_div_<?=$k?>_1">
                                  <input type="text" id="variantprice_<?=$k?>_1" onkeypress="return decimal(event,this.value)" class="form-control variantprices<?=$k?>" name="variantprice[<?=$k?>][]" value="">
                                </div>
                              </div>
                              <div class="col-md-3 pr-xs pl-xs">
                                <div class="form-group" for="variantqty_<?=$k?>_1" id="variantqty_div_<?=$k?>_1">
                                  <input type="text" id="variantqty_<?=$k?>_1" onkeypress="return isNumber(event)" class="form-control variantqty<?=$k?>" name="variantqty[<?=$k?>][]" value="" maxlength="4">
                                </div>
                              </div>
                              <div class="col-md-2 pr-xs pl-xs">
                                <div class="form-group text-right" for="variantdiscpercent_<?=$k?>_1" id="variantdiscpercent_div_<?=$k?>_1">
                                  <input type="text" id="variantdiscpercent_<?=$k?>_1" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent<?=$k?>" name="variantdiscpercent[<?=$k?>][]" value="" onkeyup="return onlypercentage(this.id)">
                                </div>
                              </div>
                              <div class="col-md-3 mt-xs">
                                <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$k?>" onclick="removevariantprice(<?=$k?>,1)" style="display:none;"><i class="fa fa-minus"></i></button>
                                <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice<?=$k?>" onclick="addnewvariantprice(<?=$k?>)"><i class="fa fa-plus"></i></button>
                              </div> 
                            </div>
                          <?php } ?>

                        </div>
                      </div>
                      <?php
                      } ?>
                  <?php
                  }else{
                  ?>
                      <div class="productvariantdiv border-panel" id="maindiv1">
                        <div class="row">
                          <div class="col-md-12">
                            <button type="button" class="btn btn-raised btn-primary btn-sm pull-right stickey-addnewpricebtn" id="addnewprice"><i class="fa fa-plus"></i> ADD NEW PRICE</button>
                          </div>
                        </div>
                        <input type="hidden" name="priceid[0]" value="0">
                        <div class="row m-n">
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="price" id="price_div0">
                              <label class="control-label" for="price0">Price 1 <span class="mandatoryfield">*</span></label>
                               <input type="text" id="price0" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="Price" name="price[0]" value="">
                            </div>
                          </div>
                          <div class="col-md-1 pr-xs pl-xs">
                            <div class="form-group text-right" for="discount0" id="discount_div0">
                                <label class="control-label" for="discount0">Disc. (%)</label>
                                <input type="text" id="discount0" onkeypress="return decimal_number_validation(event,this.value)" class="form-control discount" name="discount[0]" value="" onkeyup="return onlypercentage(this.id)">
                            </div>
                          </div>
                          <div class="col-md-1 pr-xs pl-xs">
                            <div class="form-group" for="stock" id="stock_div0">
                                <label class="control-label" for="stock0"> Stock
                               <span class="mandatoryfield"> * </span></label>
                               <input type="text" id="stock0" onkeypress="return isNumber(event)" class="form-control stocks" placeholder="Stock" name="stock[0]" value="">
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="pointsforseller" id="pointsforseller_div0">
                               <label class="control-label" for="pointsforseller0">Points for Seller</label>
                               <input type="text" id="pointsforseller0" onkeypress="return isNumber(event)" class="form-control pointsforseller" placeholder="" name="pointsforseller[0]">
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="pointsforbuyer" id="pointsforbuyer_div0">
                               <label class="control-label" for="pointsforbuyer0">Points for Buyer</label>
                               <input type="text" id="pointsforbuyer0" onkeypress="return isNumber(event)" class="form-control pointsforbuyer" placeholder="" name="pointsforbuyer[0]">
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="sku" id="sku_div0">
                               <label class="control-label" for="sku0">SKU
                               <span class="mandatoryfield"> * </span></label>
                               <input type="text" id="sku0" class="form-control sku" name="sku[0]" value="">
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" for="minimumsalesprice" id="minimumsalesprice_div0">
                               <label class="control-label" for="minimumsalesprice0">Min. Sales Price</label>
                               <input type="text" id="minimumsalesprice0" class="form-control minimumsalesprice" name="minimumsalesprice[0]" value="">
                            </div>
                          </div>
                        </div>
                        <div class="row m-n">
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" id="minimumstocklimit_div0">
                               <label class="control-label" for="minimumstocklimit0">Minimum Stock Limit</label>
                               <input type="text" id="minimumstocklimit0" class="form-control minimumstocklimit" name="minimumstocklimit[0]" value="" onkeypress="return isNumber(event)" maxlength="4">
                            </div>
                          </div>
                          <div class="col-md-1 pr-xs pl-xs">
                            <div class="form-group" id="weight_div0">
                               <label class="control-label" for="weight0">Weight (kg)</label>
                               <input type="text" id="weight0" class="form-control weight" name="weight[0]" value="" onkeypress="return decimal_number_validation(event,this.value,6,3)" >
                            </div>
                          </div>
                          <div class="col-md-3 pr-xs pl-xs">
                            <div id="orderquantitydiv">
                              <div class="col-md-6 p-n">
                                <div class="form-group pb-n" style="margin-top: 7px">
                                  <div class="col-md-12 pr-xs pl-n">
                                    <label class="control-label" for="minimumorderqty0">Min. Order Qty</label>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 p-n">
                                <div class="form-group pb-n" style="margin-top: 7px;">
                                  <div class="col-md-12 pl-xs pr-n">
                                    <label class="control-label" for="maximumorderqty0">Max. Order Qty</label>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 p-n">
                                <div class="form-group" id="minimumorderqty_div0">
                                  <div class="col-md-12 pr-xs pl-n">
                                    <input type="text" id="minimumorderqty0" class="form-control m-n" name="minimumorderqty[0]" value="" onkeypress="return isNumber(event)" maxlength="4">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-6 p-n">
                                <div class="form-group" id="maximumorderqty_div0">
                                  <div class="col-md-12 pl-xs pr-n">
                                    <input type="text" id="maximumorderqty0" class="form-control m-n" name="maximumorderqty[0]" value="0" onkeypress="return isNumber(event)" maxlength="4">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-2 pr-xs pl-xs">
                            <div class="form-group" id="barcode_div0">
                              <label class="control-label" for="barcode0">Barcode <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-12 p-n">
                                <div class="col-md-11 pl-n">
                                  <input type="text" id="barcode0" class="form-control barcode" name="barcode[0]" onkeypress="return alphanumeric(event)" maxlength="30">
                                  <input type="hidden" id="oldbarcode0">
                                </div>
                                <div class="col-sm-1 p-n" style="padding-top: 5px !important;">
                                  <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised btn-sm" title="Generate Barcode" onclick="generateBarcode(0)" style="padding: 9px 14px;"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4 pr-xs pl-xs pt-sm">
                            <div class="form-group text-center" id="barcodeimage_div0">
                              <label class="control-label"></label>
                              <div class="col-sm-12 pt-sm p-n">
                                  <img id="barcodeimg0" src="" style="max-width: 100%;">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row m-n">
                          <div id="variant_div0">
                            <input type="hidden" id="variant_count0" name="variant_count[0]" value="1">
                            <div class="col-md-6" id="variants00" style="padding: 0;">
                              <div class="col-md-5 pl-xs pr-xs">
                                <div class="form-group" id="attributediv00"><label class="control-label" for="attributeid00"> Attribute <span class="mandatoryfield">*</span></label> 
                                  <select id="attributeid00" name="attributeid[0][0]" class="selectpicker form-control attributeids" div-id="0" attribute-id="0" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" onchange="loadvariant('0','0')">
                                    <option value="0">Select Attribute</option>
                                    <?php foreach($attributedata as $row){ ?>
                                      <option value="<?php echo $row['id']; ?>" <?php if(isset($variantdata)){ if($variantdata['attributeid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['variantname']; ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-5 pl-xs pr-xs">
                                <div class="form-group" id="variantdiv00"><label class="control-label" for="variantid00">Variant <span class="mandatoryfield">*</span></label>
                                    <select id="variantid00" name="variantid[0][0]" class="selectpicker form-control variantids" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" div-id="0">
                                      <option value="0">Select Variant</option>
                                    </select>
                                </div>
                              </div>
                              <div class="col-md-2" style="margin-top: 34px;padding: 0;">
                                
                                <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" variant-div="0" style="display:none;"><i class="fa fa-minus"></i></button>
                                
                                <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="0"><i class="fa fa-plus"></i></button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6 pr-xs pl-xs">
                            <div class="form-group">
                              <label for="focusedinput" class="col-sm-3 control-label pt-xs">Price Type</label>
                              <div class="col-sm-9">
                                <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                  <div class="radio">
                                    <input type="radio" name="pricetype0" id="singleqty0" class="pricetype" value="0" checked>
                                    <label for="singleqty0">Single Quantity</label>
                                  </div>
                                </div>
                                <div class="col-sm-6 col-xs-6 p-n">
                                  <div class="radio">
                                    <input type="radio" name="pricetype0" id="multipleqty0" class="pricetype" value="1">
                                    <label for="multipleqty0">Multiple Quantity</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 p-n" id="addpriceinpricelist_div0">
                            <div class="form-group">
                              <div class="col-sm-8">
                                <div class="checkbox text-left">
                                  <input id="addpriceinpricelist0" name="addpriceinpricelist0" type="checkbox" checked>
                                  <label for="addpriceinpricelist0">Add Price in Price List</label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="row" id="multiplepricesection0" style="display: none;">
                          <div class="col-md-12"><hr></div>
                          <div class="col-md-12 p-n">
                            <div id="headingmultipleprice_0_1" class="col-md-6 headingmultipleprice0">
                              <div class="col-md-4 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantprice_0_1">Price <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-3 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantqty_0_1">Quantity <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-2 pr-xs pl-xs">
                                <div class="form-group text-right">
                                  <label class="control-label" for="variantdiscpercent_0_1">Disc. (%)</label>
                                </div>
                              </div>
                            </div>
                            <div id="headingmultipleprice_0_2" class="col-md-6 headingmultipleprice0" style="display:none;">
                              <div class="col-md-4 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantprice_0_1">Price <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-3 pr-xs pl-xs">
                                <div class="form-group">
                                  <label class="control-label" for="variantqty_0_1">Quantity <span class="mandatoryfield">*</span></label>
                                </div>
                              </div>
                              <div class="col-md-2 pr-xs pl-xs">
                                <div class="form-group text-right">
                                  <label class="control-label" for="variantdiscpercent_0_1">Disc. (%)</label>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div id="countmultipleprice_0_1" class="col-md-6 countmultipleprice0">
                            <div class="col-md-4 pr-xs pl-xs">
                              <div class="form-group" for="variantprice_0_1" id="variantprice_div_0_1">
                                <input type="text" id="variantprice_0_1" onkeypress="return decimal(event,this.value)" class="form-control variantprices0" name="variantprice[0][]" value="">
                              </div>
                            </div>
                            <div class="col-md-3 pr-xs pl-xs">
                              <div class="form-group" for="variantqty_0_1" id="variantqty_div_0_1">
                                <input type="text" id="variantqty_0_1" onkeypress="return isNumber(event)" class="form-control variantqty0" name="variantqty[0][]" value="" maxlength="4">
                              </div>
                            </div>
                            <div class="col-md-2 pr-xs pl-xs">
                              <div class="form-group text-right" for="variantdiscpercent_0_1" id="variantdiscpercent_div_0_1">
                                <input type="text" id="variantdiscpercent_0_1" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent0" name="variantdiscpercent[0][]" value="" onkeyup="return onlypercentage(this.id)">
                              </div>
                            </div>
                            <div class="col-md-3 mt-xs">
                              <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice0" onclick="removevariantprice(0,1)" style="display:none;"><i class="fa fa-minus"></i></button>
                              <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice0" onclick="addnewvariantprice(0)"><i class="fa fa-plus"></i></button>
                            </div> 
                          </div>
                        </div>
                      </div>
                  <?php  
                  }
                  ?>
                  </div>
                  <div class="col-md-12">
                    <div class="form-group">
                      <label for="focusedinput" class="col-sm-5 control-label"></label>
                      <div class="col-sm-6">
                          <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                      </div>
                    </div>
                  </div>
                </form>
              </div>
        </div>
          </div>
        </div>
      </div>
    </div>

    </div> <!-- .container-fluid -->
