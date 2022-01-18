  <script type="text/javascript">
      var DEFAULT_IMG = '<?=DEFAULT_IMG?>';
      var PRODUCT_IMG_WIDTH = '<?=PRODUCT_IMG_WIDTH?>';
      var PRODUCT_IMG_HEIGHT = '<?=PRODUCT_IMG_HEIGHT?>';

      var productcount = "<?php echo $productcount; ?>";
  </script>
  <div class="page-content">
      <div class="page-heading">
          <h1><?php if (isset($productdata)) {echo 'Edit';} else {echo 'Add';}?>
              <?=$this->session->userdata(base_url() . 'submenuname')?></h1>
          <small>
              <ol class="breadcrumb">
                  <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                  <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url() . 'mainmenuname')?></a></li>
                  <li><a href="<?php echo base_url() . ADMINFOLDER; ?><?=$this->session->userdata(base_url() . 'submenuurl')?>"><?=$this->session->userdata(base_url() . 'submenuname')?></a>
                  </li>
                  <li class="active"><?php if (isset($productdata)) {echo 'Edit';} else {echo 'Add';}?>
                      <?=$this->session->userdata(base_url() . 'submenuname')?></li>
              </ol>
          </small>
      </div>

      <div class="container-fluid">

          <div data-widget-group="group1">
              <div class="row">
                  <div class="col-md-12">
                      <div class="panel panel-default border-panel">
                          <div class="panel-body">
                              <div class="col-sm-12 p-n">
                                  <form class="form-horizontal" enctype="multipart/form-data" id="productform">
                                      <input type="hidden" name="productid" id="productid" value="<?php if (isset($productdata)) {echo $productdata['id'];}?>">
                                      <input type="hidden" name="sendnotification">
                                      <div class="col-md-12 p-n">
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="categoryid_div">
                                                  <label class="col-sm-3 control-label" for="categoryid">Category <span class="mandatoryfield"> * </span></label>
                                                  <div class="col-md-8">
                                                      <select class="form-control selectpicker" id="categoryid" name="categoryid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                          <option value="0">Select Main Category</option>
                                                          <?php foreach ($maincategorydata as $row) {?>
                                                              <option value="<?php echo $row['id']; ?>" <?php if (isset($productdata)) {if ($productdata['categoryid'] == $row['id']) {echo 'selected';}}?>>
                                                                  <?php echo $row['name']; ?></option>
                                                          <?php }?>
                                                      </select>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="priority_div">
                                                  <label class="col-sm-3 control-label" for="priority">Priority <span class="mandatoryfield"> * </span></label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="priority" onkeypress="return isNumber(event)" class="form-control" name="priority" value="<?php if (isset($productdata)) {echo $productdata['priority'];}?>">
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="productname_div">
                                                  <label class="col-sm-3 control-label" for="productname">Product Name <span class="mandatoryfield"> * </span></label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="productname" class="form-control" name="productname" value="<?php if (isset($productdata)) {echo $productdata['name'];}?>" onkeyup="setslug(this.value);">
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="productslug_div">
                                                  <label class="col-sm-3 control-label" for="productslug">Link <span class="mandatoryfield"> * </span></label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="productslug" class="form-control" name="productslug" value="<?php if (isset($productdata)) {echo $productdata['slug'];}?>">
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="hsncode_div">
                                                  <label class="col-sm-3 control-label" for="hsncode">HSN Code<?=(HSNCODE_IS_COMPULSARY == 1 ? '<span class="mandatoryfield"> * </span>' : "")?></label>
                                                  <div class="col-sm-8">
                                                      <select class="form-control selectpicker" id="hsncodeid" name="hsncodeid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                          <option value="0">Select HSN Code</option>
                                                          <?php foreach ($hsncodedata as $hsncode) {?>
                                                              <option value="<?php echo $hsncode['id']; ?>" <?php if (isset($productdata)) {if ($productdata['hsncodeid'] == $hsncode['id']) {echo 'selected';}}?>><?php echo $hsncode['hsncode']; ?></option>
                                                          <?php }?>
                                                      </select>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="unit_div">
                                                  <label class="col-md-3 control-label" for="unitid">Select Unit<?php if (PRODUCT_UNIT_IS_OPTIONAL == 0) {?> <span class="mandatoryfield">*</span><?php }?></label>
                                                  <div class="col-md-8">
                                                      <select class="form-control selectpicker" id="unitid" name="unitid" data-live-search="true" data-actions-box="true" data-select-on-tab="true" data-size="5">
                                                          <option value="0">Select Unit</option>
                                                          <?php foreach ($unitdata as $unit) {?>
                                                              <option value="<?=$unit['id']?>" <?=((isset($productdata) && isset($unitid) && $unitid == $unit['id']) ? 'selected' : "")?>><?=$unit['name']?></option>
                                                          <?php }?>
                                                      </select>
                                                  </div>
                                                  <div class="col-md-1 p-n" style="padding-top: 5px !important;">
                                                      <a href="javascript:void(0)" onclick="addunit()" class="btn btn-primary btn-raised p-xs"><i class="material-icons" title="Add Unit">add</i></a>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="productname_div">
                                                  <label class="col-sm-3 control-label" for="productname">Importet Product Name </label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="importerproductname" class="form-control" name="importerproductname" value="<?php if (isset($productdata)) {echo $productdata['importerProductName'];}?>">
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="productname_div">
                                                  <label class="col-sm-3 control-label" for="productname">Supplier Product Name </label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="supplierproductname" class="form-control" name="supplierproductname" value="<?php if (isset($productdata)) {echo $productdata['supplierProductName'];}?>">
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="brand_div">
                                                  <label class="col-md-3 control-label" for="brandid">Select Brand</label>
                                                  <div class="col-md-8">
                                                      <select class="form-control selectpicker" id="brandid" name="brandid" data-live-search="true" data-actions-box="true" data-select-on-tab="true" data-size="5">
                                                          <option value="0">Select Brand</option>
                                                          <?php foreach ($branddata as $brand) {?>
                                                              <option value="<?=$brand['id']?>" <?=((isset($productdata) && $productdata['brandid'] == $brand['id']) ? 'selected' : "")?>><?=$brand['name']?></option>
                                                          <?php }?>
                                                      </select>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="productname_div">
                                                  <label class="col-sm-3 control-label" for="productname">Installation Cost</label>
                                                  <div class="col-md-8">
                                                      <input type="number" id="installationcost" class="form-control" name="installationcost" value="<?php if (isset($productdata)) {echo $productdata['installationcost'];}?>">
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          <hr>
                                      </div>
                                      <div class="col-md-12 ">
                                          <div class="col-md-6 p-n">
                                              <div class="col-sm-3">
                                                  <div class="form-group" id="checkuniversal_div">
                                                      <div class="checkbox col-sm-12 col-xs-6 control-label pl-xs mt-sm">
                                                          <input type="checkbox" name="checkuniversal" id="checkuniversal" value="1" <?php if (isset($productdata) && $productdata['isuniversal'] == 1) {
                                                                echo 'checked';
                                                          }?>>
                                                          <label style="font-size: 14px;" for="checkuniversal">Universal Price</label>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div class="col-sm-8 pr-xs" style="padding-left: 8px;">
                                                  <div class="form-group" id="prices_div">
                                                      <div class="col-sm-12">
                                                          <input id="prices" type="text" name="prices" value="<?php if (isset($productprices)) {
                                                                echo implode(",", $productprices);
                                                            }?>" data-provide="prices" placeholder="Multiple Prices" onkeypress="return decimal(event,this.value)">
                                                      </div>
                                                  </div>
                                                  <div class="form-group" id="price_div" style="<?php if (isset($productdata) && $productdata['pricetype'] == 1) {
                                                        echo "display:none;";
                                                    }?>">
                                                      <div class="col-md-12">
                                                          <input type="text" id="price" onkeypress="return decimal(event,this.value)" class="form-control" placeholder="Price" name="price" value="<?=(isset($productdata) && $productdata['pricetype'] == 0 && !empty($productquantitypricesdata) ? $productquantitypricesdata[0]['price'] : "")?>">
                                                          <input type="hidden" name="singlequantitypricesid" value="<?=(isset($productdata) && $productdata['pricetype'] == 0 && !empty($productquantitypricesdata) ? $productquantitypricesdata[0]['id'] : "")?>">
                                                      </div>
                                                  </div>
                                                  <input type="hidden" name="productpricesid" value="<?php if (!empty($productdata) && $productdata['isuniversal'] == 1 && isset($productpricesid)) {
                                                        echo $productpricesid;
                                                    }?>">

                                                  <?php if (!empty($productdata) && $productdata['isuniversal'] == 0) {?>
                                                      <div class="form-group">
                                                          <div class="col-md-12">
                                                              <span class="btn btn-info btn-raised"><?php if (isset($productprices)) {
                                                                    echo "<i class='fa fa-inr'></i> " . min($productprices) . " - " . max($productprices);
                                                                }?></span>
                                                          </div>
                                                      </div>
                                                  <?php }?>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="discount_div" style="<?php if (isset($productdata) && $productdata['pricetype'] == 1) {
                                                    echo "display:none;";
                                                }?>">
                                                  <label class="col-sm-3 control-label" for="discount">Discount (%)</label>
                                                  <div class="col-sm-8">
                                                      <input id="discount" type="text" name="discount" class="form-control" onkeypress="return decimal_number_validation(event,this.value)" maxlength="5" value="<?=(isset($productdata) && $productdata['pricetype'] == 0 && !empty($productquantitypricesdata) ? $productquantitypricesdata[0]['discount'] : "")?>">
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="stock_div">
                                                  <label class="col-md-3 control-label" for="stock">Stock <span class="mandatoryfield"> * </span></label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="stock" onkeypress="return isNumber(event)" class="form-control" placeholder="Stock" name="stock" value="<?php if (isset($productstock)) {
                                                            echo implode(',', $productstock);
                                                        }?>">
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="weight_div" style="display:none;">
                                                  <label for="weight" class="col-sm-3 control-label">Weight (kg)</label>
                                                  <div class="col-sm-8">
                                                      <input type="text" id="weight" onkeypress="return decimal_number_validation(event,this.value,6,3)" class="form-control" name="weight" value="<?php if (isset($productdata)) {echo $productdata['weight'];}?>">
                                                  </div>
                                              </div>
                                          </div>
                                          <?php /*
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="barcode_div" style="display:none;">
                                                  <label class="col-md-3 control-label" for="barcode">Barcode <span class="mandatoryfield"> * </span></label>
                                                  <div class="col-md-8">
                                                      <?php if (isset($productdata)) {
                                                            $barcode = ($productdata['barcode'] != "") ? $productdata['barcode'] : $barcode;
                                                        }?>
                                                      <input type="text" id="barcode" class="form-control" name="barcode" value="<?php echo $barcode; ?>" onkeypress="return alphanumeric(event)" maxlength="30">
                                                      <input type="hidden" id="oldbarcode" value="<?php echo $barcode; ?>">
                                                  </div>
                                                  <div class="col-sm-1 p-n" style="padding-top: 5px !important;">
                                                      <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Barcode" onclick="generateBarcode()" style="padding: 8px 12px;"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                  </div>
                                              </div>
                                          </div> */?>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="sku_div" style="display:none;">
                                                  <label class="col-md-3 control-label" for="sku">SKU <span class="mandatoryfield"> * </span></label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="sku" name="sku" class="form-control" value="<?php if (isset($productdata)) {
                                                            echo $productdata['sku'];
                                                        }?>">
                                                  </div>
                                              </div>
                                          </div>

                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="minimumstocklimit_div" style="display:none;">
                                                  <label class="col-md-3 control-label" for="minimumstocklimit">Min. Stock Limit</label>
                                                  <div class="col-md-8">
                                                      <input type="text" id="minimumstocklimit" name="minimumstocklimit" class="form-control" value="<?php if (isset($productdata)) {echo $productdata['minimumstocklimit'];}?>" onkeypress="return isNumber(event)" maxlength="4">
                                                  </div>
                                              </div>
                                          </div>

                                          <?php /*
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="barcodeimage_div" style="display:none;">
                                                  <label class="col-md-3 control-label"></label>
                                                  <div class="col-sm-9 pt-sm p-n">
                                                      <img id="barcodeimg" src="<?=ADMIN_URL . 'product/set_barcode/' . $barcode;?>" style="max-width: 100%;">
                                                  </div>
                                              </div>
                                          </div>
                                          */?>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          
                                          
                                      </div>
                                      <div class="row" id="multiplepricesection" style="display: none;">
                                          <div class="col-md-12 p-n">
                                              <div id="headingmultipleprice1" class="col-md-6 headingmultipleprice">
                                                  <div class="col-md-4">
                                                      <div class="form-group">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <label class="control-label" for="variantprice1">Price <span class="mandatoryfield">*</span></label>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                      <div class="form-group">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <label class="control-label" for="variantqty1">Quantity <span class="mandatoryfield">*</span></label>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-2">
                                                      <div class="form-group text-right">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <label class="control-label" for="variantdiscpercent1">Disc. (%)</label>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                              <div id="headingmultipleprice2" class="col-md-6 headingmultipleprice" style="<?=(isset($productquantitypricesdata) && count($productquantitypricesdata) > 1 ? "" : "display:none;")?>">
                                                  <div class="col-md-4">
                                                      <div class="form-group">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <label class="control-label" for="variantprice1">Price <span class="mandatoryfield">*</span></label>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                      <div class="form-group">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <label class="control-label" for="variantqty1">Quantity <span class="mandatoryfield">*</span></label>
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-2">
                                                      <div class="form-group text-right">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <label class="control-label" for="variantdiscpercent1">Disc. (%)</label>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <?php if (isset($productquantitypricesdata) && $productdata['pricetype'] == 1) {
    foreach ($productquantitypricesdata as $p => $prices) {?>
                                                  <div id="countmultipleprice<?=$p + 1?>" class="col-md-6 countmultipleprice">
                                                      <input type="hidden" name="productquantitypricesid[]" value="<?=$prices['id']?>">
                                                      <div class="col-md-4">
                                                          <div class="form-group" for="variantprice<?=$p + 1?>" id="variantprice_div<?=$p + 1?>">
                                                              <div class="col-md-12 pr-xs pl-xs">
                                                                  <input type="text" id="variantprice<?=$p + 1?>" onkeypress="return decimal(event,this.value)" class="form-control variantprices" name="variantprice[]" value="<?=$prices['price']?>">
                                                              </div>
                                                          </div>
                                                      </div>
                                                      <div class="col-md-3">
                                                          <div class="form-group" for="variantqty<?=$p + 1?>" id="variantqty_div<?=$p + 1?>">
                                                              <div class="col-md-12 pr-xs pl-xs">
                                                                  <input type="text" id="variantqty<?=$p + 1?>" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty[]" value="<?=$prices['quantity']?>" maxlength="4">
                                                              </div>
                                                          </div>
                                                      </div>
                                                      <div class="col-md-2">
                                                          <div class="form-group text-right" for="variantdiscpercent<?=$p + 1?>" id="variantdiscpercent_div<?=$p + 1?>">
                                                              <div class="col-md-12 pr-xs pl-xs">
                                                                  <input type="text" id="variantdiscpercent<?=$p + 1?>" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent" name="variantdiscpercent[]" value="<?=$prices['discount']?>" onkeyup="return onlypercentage(this.id)">
                                                              </div>
                                                          </div>
                                                      </div>
                                                      <div class="col-md-3">
                                                          <div class="form-group pt-sm">
                                                              <?php if ($p == 0) {?>
                                                                  <?php if (count($productquantitypricesdata) > 1) {?>
                                                                      <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice" onclick="removevariantprice(1)" ><i class="fa fa-minus"></i></button>
                                                                  <?php } else {?>
                                                                      <button type="button" class="btn btn-default btn-raised add_variantprice" onclick="addnewvariantprice()"><i class="fa fa-plus"></i></button>
                                                                  <?php }
    } else if ($p != 0) {?>
                                                                  <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice" onclick="removevariantprice(<?=$p + 1?>)"><i class="fa fa-minus"></i></button>
                                                              <?php }?>

                                                              <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice" onclick="removevariantprice(<?=$p + 1?>)" style="display:none;"><i class="fa fa-minus"></i></button>
                                                              <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice" onclick="addnewvariantprice()"><i class="fa fa-plus"></i></button>
                                                          </div>
                                                      </div>
                                                  </div>
                                              <?php }
} else {?>
                                              <div id="countmultipleprice1" class="col-md-6 countmultipleprice">
                                                  <div class="col-md-4">
                                                      <div class="form-group" for="variantprice1" id="variantprice_div1">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <input type="text" id="variantprice1" onkeypress="return decimal(event,this.value)" class="form-control variantprices" name="variantprice[]" value="">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                      <div class="form-group" for="variantqty1" id="variantqty_div1">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <input type="text" id="variantqty1" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty[]" value="" maxlength="4">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-2">
                                                      <div class="form-group text-right" for="variantdiscpercent1" id="variantdiscpercent_div1">
                                                          <div class="col-md-12 pr-xs pl-xs">
                                                              <input type="text" id="variantdiscpercent1" onkeypress="return decimal(event,this.value)" class="form-control text-right variantdiscpercent" name="variantdiscpercent[]" value="" onkeyup="return onlypercentage(this.id)">
                                                          </div>
                                                      </div>
                                                  </div>
                                                  <div class="col-md-3">
                                                      <div class="form-group pt-sm">
                                                          <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice" onclick="removevariantprice(1)" style="display:none;"><i class="fa fa-minus"></i></button>
                                                          <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice" onclick="addnewvariantprice()"><i class="fa fa-plus"></i></button>
                                                      </div>
                                                  </div>
                                              </div>
                                          <?php }?>
                                      </div>
                                      <div class="col-md-12 p-n">
                                          <hr>
                                      </div>

                                      <div class="col-md-12 p-n">
                                          <div class="col-md-6 p-n">
                                              <div class="form-group" id="shortdescription_div">
                                                  <label for="shortdescription" class="control-label col-sm-3 pl-xs">Short Description <span class="mandatoryfield">*</span></label>
                                                  <div class="col-sm-8">
                                                      <textarea id="shortdescription" class="form-control" name="shortdescription"><?php if (isset($productdata)) {echo $productdata['shortdescription'];}?></textarea>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group row" id="description_div">
                                              <label for="focusedinput" class="col-sm-12" style="text-align: left;">Description <span class="mandatoryfield">*</span></label>
                                              <div id="description_div">
                                                  <div class="col-sm-12">
                                                      <?php $data['controlname'] = "description";
                                                        if (isset($productdata) && !empty($productdata)) {
                                                            $data['controldata'] = $productdata['description'];
                                                        }?>
                                                      <?php $this->load->view(ADMINFOLDER . 'includes/ckeditor', $data);?>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>

                                      <div class="col-md-12 p-n">
                                          <hr>
                                          <div class="form-group row">
                                              <label class="col-sm-12" style="text-align: center;">Upload Product Image</label>
                                          </div>
                                          <?php if (isset($productdata) && !empty($productfile) && isset($productfile)) {?>
                                              <input type="hidden" name="removeproductfileid" id="removeproductfileid">
                                              <script type="text/javascript">
                                                  var productfilecount = '<?=count($productfile)?>';
                                              </script>
                                              <?php for ($i = 0; $i < count($productfile); $i++) {?>
                                                  <div class="col-md-12 p-n" id="productfilecount<?=$i + 1?>">
                                                      <div class="form-group" id="productfile<?=$i + 1?>_div">
                                                          <input type="hidden" name="productfileid<?=$i + 1?>" value="<?=$productfile[$i]['id']?>" id="productfileid<?=$i + 1?>">
                                                          <div class="col-md-2 text-center">
                                                              <?php
if ($productfile[$i]['type'] == 1) {
    $image = PRODUCT . $productfile[$i]['filename'];
} else if ($productfile[$i]['type'] == 2) {
    $image = PRODUCT . $productfile[$i]['videothumb'];
} else if ($productfile[$i]['type'] == 3) {
    $image = $this->general_model->getYoutubevideoThumb(urldecode($productfile[$i]['filename']));
} else {
    $image = DEFAULT_IMG . DEFAULT_IMAGE_PREVIEW;
}
    ?>
                                                              <img src="<?=$image?>" id="imagepreview<?=$i + 1?>" class="thumbwidth">
                                                          </div>
                                                          <div class="col-md-7 p-n">
                                                              <div class="input-group" id="fileupload<?=$i + 1?>" style="display:<?=($productfile[$i]['type'] == 1 || $productfile[$i]['type'] == 2) ? 'table' : 'none'?>;">
                                                                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                      <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i>
                                                                          <input type="file" name="productfile<?=$i + 1?>" class="productfile" id="productfile<?=$i + 1?>" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                                      </span>
                                                                  </span>
                                                                  <video width="400" id="videoelem<?=$i + 1?>" style="display: none;" controls>
                                                                      <source src="" id="video_src<?=$i + 1?>">
                                                                  </video>
                                                                  <input type="text" name="videothumb<?=$i + 1?>" id="videothumb<?=$i + 1?>" value="" style="display: none;" />
                                                                  <input type="text" readonly="" id="Filetext<?=$i + 1?>" class="form-control" value="<?=$productfile[$i]['filename']?>">
                                                              </div>
                                                              <div id="youtube<?=$i + 1?>" style="display:<?=($productfile[$i]['type'] == 3) ? 'block' : 'none'?>;">
                                                                  <input type="text" id="youtubeurl<?=$i + 1?>" class="form-control" name="youtubeurl<?=$i + 1?>" value="<?=($productfile[$i]['type'] == 3) ? urldecode($productfile[$i]['filename']) : ''?>" onblur="getThumbImage(this.value,'imagepreview<?=$i + 1?>')">
                                                              </div>
                                                              <input type="hidden" name="filetype<?=$i + 1?>" id="imagefile<?=$i + 1?>" value="1">
                                                          </div>
                                                          <div class="col-md-2">
                                                              <?php if ($i == 0) {?>
                                                                  <?php if (count($productfile) > 1) {?>
                                                                      <button type="button" class="btn btn-danger btn-raised add_remove_btn_product" onclick="removeproductfile(1)" id=p1 style="padding: 6px 12px;margin-top: 0px;"><i class="fa fa-minus"></i>
                                                                          <div class="ripple-container"></div>
                                                                      </button>
                                                                  <?php } else {?>
                                                                      <button type="button" class="btn btn-primary btn-raised add_remove_btn" onclick="addnewproductfile()" id=1 style="padding: 6px 12px;margin-top: 0px;"><i class="fa fa-plus"></i>
                                                                          <div class="ripple-container"></div>
                                                                      </button>
                                                                  <?php }?>
                                                              <?php } else if ($i != 0) {?>
                                                                  <button type="button" class="btn btn-danger btn-raised add_remove_btn_product" id="p<?=$i + 1?>" onclick="removeproductfile(<?=$i + 1?>)" style="padding: 6px 12px;margin-top: 0px;"><i class="fa fa-minus"></i>
                                                                      <div class="ripple-container"></div>
                                                                  </button>
                                                              <?php }?>
                                                              <button type="button" class="btn btn-danger btn-raised add_remove_btn_product" id="p<?=$i + 1?>" onclick="removeproductfile(<?=$i + 1?>)" style="padding: 6px 12px;margin-top: 0px;display:none;"><i class="fa fa-minus"></i>
                                                                  <div class="ripple-container"></div>
                                                              </button>
                                                              <button type="button" class="btn btn-primary btn-raised add_remove_btn" onclick="addnewproductfile()" id="<?=$i + 1?>" style="padding: 6px 12px;margin-top: 0px;"><i class="fa fa-plus"></i>
                                                                  <div class="ripple-container"></div>
                                                              </button>
                                                          </div>
                                                      </div>
                                                  </div>
                                              <?php }?>
                                          <?php } else {?>
                                              <script type="text/javascript">
                                                  var productfilecount = 1;
                                              </script>
                                              <div class="col-md-12 p-n" id="productfilecount1">
                                                  <div class="form-group" id="productfile1_div">
                                                      <div class="col-md-2 text-center">
                                                          <img src="<?=DEFAULT_IMG . DEFAULT_IMAGE_PREVIEW?>" id="imagepreview1" class="thumbwidth">
                                                      </div>
                                                      <div class="col-md-7 p-n">
                                                          <div class="input-group" id="fileupload1">
                                                              <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                  <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i>
                                                                      <input type="file" name="productfile1" class="productfile" id="productfile1" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                                  </span>
                                                              </span>
                                                              <video width="400" id="videoelem1" style="display: none;" controls>
                                                                  <source src="" id="video_src1">
                                                              </video>
                                                              <input type="text" name="videothumb1" id="videothumb1" value="" style="display: none;" />
                                                              <input type="text" readonly="" id="Filetext1" class="form-control" name="Filetext[]" value="">
                                                          </div>
                                                          <div id="youtube1" style="display: none;">
                                                              <input type="text" id="youtubeurl1" class="form-control" name="youtubeurl1" onblur="getThumbImage(this.value,'imagepreview1')">
                                                          </div>
                                                          <input type="hidden" name="filetype1" id="imagefile1" value="1">
                                                      </div>
                                                      <div class="col-md-2">
                                                          <button type="button" class="btn btn-danger btn-raised add_remove_btn_product" id="p1" onclick="removeproductfile(1)" style="padding: 6px 12px;display: none;"><i class="fa fa-minus"></i>
                                                              <div class="ripple-container"></div>
                                                          </button>
                                                          <button type="button" class="btn btn-primary btn-raised add_remove_btn" id="1" onclick="addnewproductfile()" style="padding: 6px 12px;margin-top: 0px;"><i class="fa fa-plus"></i>
                                                              <div class="ripple-container"></div>
                                                          </button>
                                                      </div>
                                                  </div>
                                              </div>
                                          <?php }?>
                                          <div class="col-md-12 p-n" id="productfiledata_div"></div>
                                      </div>
                                      <div class="col-md-12">
                                          <input type="hidden" name="commingsoon" value="<?php if (isset($productdata)) {echo $productdata['commingsoon'];}?>">
                                      </div>
                                      <div class="col-md-12 text-center">
                                          <div class="form-group">
                                              <label for="focusedinput" class="col-sm-5 col-xs-4 control-label">Activate</label>
                                              <div class="col-sm-6 col-xs-8">
                                                  <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                                      <div class="radio">
                                                          <input type="radio" name="status" id="yes" value="1" <?php if (isset($productdata) && $productdata['status'] == 1) {echo 'checked';} else {echo 'checked';}?>>
                                                          <label for="yes">Yes</label>
                                                      </div>
                                                  </div>
                                                  <div class="col-sm-2 col-xs-6">
                                                      <div class="radio">
                                                          <input type="radio" name="status" id="no" value="0" <?php if (isset($productdata) && $productdata['status'] == 0) {echo 'checked';}?>>
                                                          <label for="no">No</label>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="form-group">
                                              <?php if (!empty($productdata)) {?>
                                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                              <?php } else {?>
                                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                              <?php }?>
                                              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>product" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
      <div class="modal addunit" id="addunitModal" style="overflow-y: auto;">
          <div class="modal-dialog" role="document" style="width: 600px;">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                      <h4 class="modal-title" id="post_title">Add Product Unit</h4>
                  </div>

                  <div class="modal-body no-padding">

                  </div>

              </div>
          </div>
      </div>
  </div> <!-- .container-fluid -->

  <script type="text/javascript">
      $(".add_btn_car_model").hide();
      $(".add_btn_car_model:last").show();
      /* $(".add_remove_btn:last").attr("onclick","addnewproductfile()");
        $(".add_remove_btn:last").children(":first-child").attr("class","fa fa-plus");
        $(".add_remove_btn:last").children(":first-child").html(" <b>1</b>"); */

      $(".add_remove_btn").hide();
      $(".add_remove_btn:last").show();

      function addnewproductfile() {

          if ($('input[name="Filetext[]"]').length < 10) {
              productfilecount = ++productfilecount;
              $.html = '<div class="col-md-12 p-n" id="productfilecount' + productfilecount +
                  '"><div class="form-group" id="productfile' + productfilecount + '_div"> \
                <div class="col-md-2 text-center"> \
                <img src="<?=DEFAULT_IMG . DEFAULT_IMAGE_PREVIEW?>" id="imagepreview' + productfilecount + '" class="thumbwidth"> \
              </div> \
                      <div class="col-md-7 p-n"> \
                        <div class="input-group" id="fileupload' + productfilecount + '"> \
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;"> \
                    <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i> \
                      <input type="file" name="productfile' + productfilecount +
                  '" class="productfile" id="productfile' + productfilecount + '" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png"> \
                    </span> \
                  </span> \
                  <video width="400" id="videoelem' + productfilecount + '" style="display: none;" controls> \
                      <source src="" id="video_src' + productfilecount + '"> \
                  </video> \
                  <input type="text" name="videothumb' + productfilecount + '" id="videothumb' + productfilecount + '" value="" style="display: none;" /> \
                  <input type="text" readonly="" id="Filetext' + productfilecount + '" name="Filetext[]" class="form-control" value=""> \
                </div> \
                <div id="youtube' + productfilecount + '" style="display: none;"> \
                  <input type="text" id="youtubeurl' + productfilecount + '" class="form-control" name="youtubeurl' +
                  productfilecount + '" onblur="getThumbImage(this.value,\'imagepreview' + productfilecount + '\')"> \
                </div> \
                <input type="hidden" name="filetype' + productfilecount + '" id="imagefile' + productfilecount +
                  '" value="1" checked onclick="filetype(' + productfilecount + ',1)"> \
                      </div> \
              <div class="col-md-2"> \
                <button type = "button" class = "btn btn-danger btn-raised add_remove_btn_product" id = "p' +
                  productfilecount + '" onclick = "removeproductfile(' + productfilecount + ')" style = "padding: 6px 12px;"> <i class = "fa fa-minus"> </i><div class="ripple-container"></div></button> \
                <button type="button" class="btn btn-primary btn-raised add_remove_btn" id="' + productfilecount +
                  '" onclick="addnewproductfile(' + productfilecount + ')" style="padding: 6px 12px;margin-top: 0px;"><i class="fa fa-plus"></i><div class="ripple-container"></div></button> \
              </div> \
                  </div></div>';


              $(".add_remove_btn_product:first").show();
              $(".add_remove_btn:last").hide();

              $('#productfiledata_div').append($.html);
              /*
                var last_id=$(".add_remove_btn:last").attr("id");

                $("#"+(parseInt(last_id)-1)).attr("onclick","removeproductfile("+(parseInt(last_id)-1)+")");
                $("#"+(parseInt(last_id)-1)).children(":first-child").attr("class","fa fa-minus");
                $("#"+(parseInt(last_id)-1)).children(":first-child").text(""); */

              // if($(".add_remove_btn:nth-last-child(2)").length)
              // {
              //  alert($(".add_remove_btn:nth-last-child(2)").attr('id'));
              // }

              /* $(".add_remove_btn:last").attr("onclick","addnewproductfile()");
              $(".add_remove_btn:last").children(":first-child").attr("class","fa fa-plus");
              $(".add_remove_btn:last").children(":first-child").html(" <b>1</b>"); */

              $('.productfile').change(function() {
                  validfile($(this), this);
              });
          } else {
              PNotify.removeAll();
              new PNotify({
                  title: 'Maximum 10 files allowed !',
                  styling: 'fontawesome',
                  delay: '3000',
                  type: 'error'
              });
          }
      }

      function removeproductfile(rowid) {
          if (ACTION == 1 && $('#productfileid' + rowid).val() != null) {
              var removeproductfileid = $('#removeproductfileid').val();
              $('#removeproductfileid').val(removeproductfileid + ',' + $('#productfileid' + rowid).val());
          }
          $('#productfilecount' + rowid).remove();
          // $(".add_remove_btn:last").attr("onclick","addnewproductfile()");
          //    $(".add_remove_btn:last").children(":first-child").attr("class","fa fa-plus");
          //    $(".add_remove_btn:last").children(":first-child").text(" 1");
          $(".add_remove_btn:last").show();
          if ($(".add_remove_btn_product:visible").length == 1) {
              $(".add_remove_btn_product:first").hide();
          }
      }

      function addunit() {

          var uurl = SITE_URL + "product-unit/addunitformodal";

          $.ajax({
              url: uurl,
              type: 'POST',
              //async: false,
              beforeSend: function() {
                  $('.mask').show();
                  $('#loader').show();
              },
              success: function(response) {
                  $("#addunitModal").modal("show");
                  $(".modal-body").html(response);
                  include('<?=ADMIN_JS_URL?>pages/add_product_unit.js', function() {
                  });
              },
              error: function(xhr) {
                  //alert(xhr.responseText);
              },
              complete: function() {
                  $('.mask').hide();
                  $('#loader').hide();
              },

          });
      }
  </script>