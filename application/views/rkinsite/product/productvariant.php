<script type="text/javascript">
  var DEFAULT_IMG = '<?=DEFAULT_IMG?>';
  var PRODUCT_IMG_WIDTH = '<?=PRODUCT_IMG_WIDTH?>';
  var PRODUCT_IMG_HEIGHT = '<?=PRODUCT_IMG_HEIGHT?>';
  var allattribute = <?php echo json_encode($attributedata);?>;
</script>
<style type="text/css">
  .productvariantdiv{
    box-shadow: 0px 1px 6px #333;
    padding: 10px;
  }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?> Variant</h1>                    
        <small>
              <ol class="breadcrumb">                        
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
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
              <div class="col-sm-12 col-md-12 col-lg-12">
              
                <form id="productvariantform">
                  <div id="allprices_div">
                  <input type="hidden" name="productid" value="<?=$productid?>">
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
                      <div class="productvariantdiv border-panel" id="maindiv<?=$k?>">
                        <?php
                        if($k==0){
                        ?>
                          <div class="row">
                            <div class="col-md-12">
                              <button type="button" class="btn btn-raised btn-primary btn-sm pull-right" id="addnewprice"><i class="fa fa-plus"></i> ADD NEW PRICE</button>
                            </div>
                          </div>
                        <?php  
                        }else{
                        ?>
                          <div class="row">
                            <div class="col-md-12">
                              <button type="button" class="btn btn-raised btn-danger btn-sm pull-right" onclick="removemainprice('maindiv<?=$k?>')"><i class="fa fa-remove"></i> REMOVE</button>
                            </div>
                          </div>
                        <?php  
                        }
                        ?>
                        <input type="hidden" name="priceid[<?=$k?>]" value="<?=$p['id']?>">
                        <div class="row">
                          <div class="col-md-5">
                            <div class="form-group" for="price" id="price_div<?=$k?>">
                            <label class="control-label" for="price<?=$k?>">
                                Price <?=$k+1?>
                               <span class="mandatoryfield"> * </span>
                            </label>
                               <input type="text" id="price<?=$k?>" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="Price" name="price[<?=$k?>]" value="<?=$p['price']?>">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group" for="stock" id="stock_div<?=$k?>">
                               <label class="control-label" for="stock<?=$k?>"> Stock
                               <span class="mandatoryfield"> * </span></label>
                               <input type="text" id="stock<?=$k?>" onkeypress="return isNumber(event)" class="form-control stocks" placeholder="Stock" name="stock[<?=$k?>]" value="<?=$p['stock']?>">
                            </div>
                          </div>
                        </div>
                        <hr/>
                          <div class="row">
                          
                            <?php
                            if(isset($productcombination[$p['id']])){
                            ?>

                              <input type="hidden" id="variant_count<?=$k?>" name="variant_count[<?=$k?>]" value="<?=count($productcombination[$p['id']])?>">
                              <div id="variant_div<?=$k?>">
                                <?php 
                                foreach ($productcombination[$p['id']] as $pckey => $pc) {
                                ?>
                                    <input type="hidden" name="availablevariantid[<?=$k?>][<?=$pckey?>]" value="<?=$pc['id']?>">
                                    
                                      <div class="col-md-6" id="variants<?=$k.$pckey?>" style="padding: 0;">
                                        <div class="col-md-5">
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
                                        <div class="col-md-5">
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
                                              <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" onclick="removevariant('variants<?=$k.$pckey?>',<?=$k?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                            <?php } else { ?>
                                          <?php }
                                          } else if ($pckey != 0) { ?>
                                              
                                              <button type="button" class="btn btn-danger btn-raised btn-sm multi_variant_btn_variant" onclick="removevariant('variants<?=$k.$pckey?>',<?=$k?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                          
                                          <?php } ?>
                                          
                                          <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn_variant" variant-div="<?=$k?>" style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                
                                          <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="<?=$k?>" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>

                                          
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
                                    
                                <?php
                                }
                                ?>
                                </div>
                            <?php
                            }else{
                            ?>
                            <input type="hidden" id="variant_count<?=$k?>" name="variant_count[<?=$k?>]" value="1">
                            <div id="variant_div<?=$k?>">
                              <div class="col-md-6" style="padding: 0;" id="variants<?=$k?>0">
                                <div class="col-md-5">
                                  <div class="form-group" id="attributediv<?=$k?>0"><label class="control-label" for="attributeid<?=$k?>0">Attribute <span class="mandatoryfield">*</span></label>
                                    <select id="attributeid<?=$k?>0" name="attributeid[<?=$k?>][0]" class="selectpicker form-control attributeids" div-id="<?=$k?>" attribute-id="0" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" onchange="loadvariant('<?=$k?>','0')">
                                      <option value="0">Select Attribute</option>
                                      <?php foreach($attributedata as $row){ ?>
                                        <option value="<?php echo $row['id']; ?>" <?php if(isset($variantdata)){ if($variantdata['attributeid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['variantname']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-5">
                                  <div class="form-group" id="variantdiv<?=$k?>0"><label class="control-label" for="variantid<?=$k?>0">Variant <span class="mandatoryfield">*</span> </label>
                                      <select id="variantid<?=$k?>0" name="variantid[<?=$k?>][0]" class="selectpicker form-control variantids" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" div-id="<?=$k?>">
                                        <option value="0">Select Variant</option>
                                      </select>
                                  </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 34px;padding: 0;">
                                  <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn_variant" variant-div="<?=$k?>" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                  <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="<?=$k?>" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                </div>  
                              </div>
                            </div>
                            <?php
                            }
                            ?>
                          </div>
                        </div><hr/>
                      <?php
                      } ?>
                  <?php
                  }else{
                  ?>
                    <div class="productvariantdiv border-panel">
                        <div class="row">
                          <div class="col-md-12">
                            <button type="button" class="btn btn-raised btn-primary btn-sm pull-right" id="addnewprice"><i class="fa fa-plus"></i> ADD NEW PRICE</button>
                          </div>
                        </div>
                        <input type="hidden" name="priceid[0]" value="0">
                        <div class="row">
                          <div class="col-md-5">
                            <div class="form-group" for="price" id="price_div0">
                              <label class="control-label" for="price0">
                                Price 1
                               <span class="mandatoryfield"> * </span>
                              </label>
                               <input type="text" id="price0" onkeypress="return decimal(event,this.value)" class="form-control prices" placeholder="Price" name="price[0]" value="">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group" for="stock" id="stock_div0">
                                <label class="control-label" for="stock0"> Stock
                               <span class="mandatoryfield"> * </span></label>
                               <input type="text" id="stock0" onkeypress="return isNumber(event)" class="form-control stocks" placeholder="Stock" name="stock[0]" value="">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div id="variant_div0">
                            <input type="hidden" id="variant_count0" name="variant_count[0]" value="1">
                            <div class="col-md-6" id="variants00" style="padding: 0;">
                              <div class="col-md-5">
                                <div class="form-group" id="attributediv00"><label class="control-label" for="attributeid00"> Attribute <span class="mandatoryfield">*</span></label> 
                                  <select id="attributeid00" name="attributeid[0][0]" class="selectpicker form-control attributeids" div-id="0" attribute-id="0" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" onchange="loadvariant('0','0')">
                                    <option value="0">Select Attribute</option>
                                    <?php foreach($attributedata as $row){ ?>
                                      <option value="<?php echo $row['id']; ?>" <?php if(isset($variantdata)){ if($variantdata['attributeid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['variantname']; ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>
                              <div class="col-md-5">
                                <div class="form-group" id="variantdiv00"><label class="control-label" for="variantid00">Variant <span class="mandatoryfield">*</span></label>
                                    <select id="variantid00" name="variantid[0][0]" class="selectpicker form-control variantids" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8" div-id="0">
                                      <option value="0">Select Variant</option>
                                    </select>
                                </div>
                              </div>
                              <div class="col-md-2" style="margin-top: 34px;padding: 0;">
                                
                                <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn_variant" variant-div="0"><i class="fa fa-minus"></i></button>
                                
                                <button type="button" class="btn btn-primary btn-raised btn-sm multi_variant_btn" variant-div="0"><i class="fa fa-plus"></i></button>
                              </div>
                            </div>
                          </div>
                        </div>
                        </div><hr/>
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
