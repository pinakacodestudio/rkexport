<script>
    var PRODUCTID = '<?php if(isset($pricelistdata)){ echo $pricelistdata['productid']; }else{ echo 0; } ?>';
    var PRICEID = '<?php if(isset($pricelistdata)){ echo $pricelistdata['productpriceid']; }else{ echo 0; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($pricelistdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($pricelistdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
      <div class="row">
        <form class="form-horizontal" id="productpriceform">
            <input type="hidden" name="postproductid" id="postproductid" value="<?php if(isset($pricelistdata)){ echo $pricelistdata['productid']; } ?>">
            <input type="hidden" name="postpriceid" id="postpriceid" value="<?php if(isset($pricelistdata)){ echo $pricelistdata['productpriceid']; } ?>">
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-body pt-n">
                        <div class="col-sm-12 p-n">
                            <div class="col-md-3">
                                <div class="form-group" id="category_div">
                                    <div class="col-md-12 pl-n pr-sm">
                                        <label class="control-label" for="categoryid">Select Category <span class="mandatoryfield">*</span></label>
                                        <select id="categoryid" name="categoryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(isset($pricelistdata)){ echo "disabled"; }?>>
                                            <option value="0">Select Category</option>
                                            <?php foreach($categorydata as $category){ ?>
                                                <option value="<?php echo $category['id']; ?>" <?php if(isset($pricelistdata) && $pricelistdata['categoryid']==$category['id']){ echo "selected"; }?>><?php echo $category['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="product_div">
                                    <div class="col-md-12 pl-sm pr-sm">
                                        <label class="control-label" for="productid">Select Product <span class="mandatoryfield">*</span></label>
                                        <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="6" <?php if(isset($pricelistdata)){ echo "disabled"; }?>>
                                            <option value="0">Select Product</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="price_div">
                                    <div class="col-md-12 pl-sm pr-n">
                                        <label class="control-label" for="priceid">Select Variant <span class="mandatoryfield">*</span></label>
                                        <select id="priceid" name="priceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(isset($pricelistdata)){ echo "disabled"; }?>>
                                            <option value="0">Select Variant</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" id="channelsection">
            <?php if(!empty($pricelistdata) & !empty($channeldata)){
                    $count=0;  
                    foreach($channeldata as $channel){ 
                        $pricedata = $channel['pricedata'];
                        
                        $display_pricedisc = $display_multiprice = "display: none;";
                        if($pricedata['pricetype']==0){
                            $display_pricedisc = "";
                        }else{
                            $display_multiprice = "";
                        }
                        ?>
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading">
                                <h2><span id="channelname<?=$channel['id']?>"><?=$channel['name']?></span> Channel Price Details</h2>
                            </div>
                            <div class="panel-body pt-n">
                                <div class="col-md-12 p-n">
                                    <input type="hidden" name="productbasicpricemappingid[]" id="productbasicpricemappingid<?=$channel['id']?>" value="<?=$pricedata['id']?>">
                                    <input type="hidden" name="channelid[]" class="channelid" value="<?=$channel['id']?>">
                                    <div class="col-md-2" style="<?=$display_pricedisc?>">
                                        <div class="form-group" id="salesprice<?=$channel['id']?>_div">
                                            <div class="col-sm-12 pl-n pr-md">
                                                <label for="salesprice<?=$channel['id']?>" class="control-label">Sales Price</label>
                                                <input type="text" name="salesprice[]" id="salesprice<?=$channel['id']?>" class="form-control text-right price" value="<?=((!empty($pricedata['multipleprice']) && $pricedata['pricetype']==0)?number_format($pricedata['multipleprice'][0]['price'],2,'.',''):"")?>" onkeypress="return decimal_number_validation(event,this.value,8)" div-id="<?=$channel['id']?>">
                                                <input type="hidden" name="singlequantitypricesid[]" value="<?=($pricedata['pricetype']==0 && !empty($pricedata['multipleprice'])?$pricedata['multipleprice'][0]['id']:"")?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group" id="minimumsalesprice<?=$channel['id']?>_div">
                                            <div class="col-sm-12 pl-n pr-md">
                                                <label for="minimumsalesprice<?=$channel['id']?>" class="control-label">Min. Sales Price</label>
                                                <input type="text" name="minimumsalesprice[]" id="minimumsalesprice<?=$channel['id']?>" class="form-control text-right" value="<?=number_format($pricedata['minimumsalesprice'],2,'.','')?>" onkeypress="return decimal_number_validation(event,this.value,8)" div-id="<?=$channel['id']?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group" id="minqty<?=$channel['id']?>_div">
                                            <div class="col-md-12 pl-n pr-sm text-left">
                                                <label for="minqty<?=$channel['id']?>" class="control-label">Min. Qty</label>
                                                <input type="text" name="minqty[]" id="minqty<?=$channel['id']?>" class="form-control text-right" value="<?=($pricedata['minimumqty']>0)?$pricedata['minimumqty']:""?>" onkeypress="return isNumber(event)" maxlength="4">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group" id="maxqty<?=$channel['id']?>_div">
                                            <div class="col-md-12 pl-sm pr-sm text-left">
                                                <label for="maxqty<?=$channel['id']?>" class="control-label">Max. Qty</label>
                                                <input type="text" name="maxqty[]" id="maxqty<?=$channel['id']?>" class="form-control text-right" value="<?=($pricedata['maximumqty']>0)?$pricedata['maximumqty']:""?>" onkeypress="return isNumber(event)" maxlength="4">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="<?=$display_pricedisc?>">
                                        <div class="form-group" id="discper<?=$channel['id']?>_div">
                                            <div class="col-md-12 pl-sm pr-sm text-left">
                                                <label for="discper<?=$channel['id']?>" class="control-label">Disc. (%)</label>
                                                <input type="text" name="discper[]" id="discper<?=$channel['id']?>" class="form-control text-right discper" value="<?=((!empty($pricedata['multipleprice']) && $pricedata['pricetype']==0 && $pricedata['multipleprice'][0]['discount']>0)?number_format($pricedata['multipleprice'][0]['discount'],2,'.',''):"")?>" onkeypress="return decimal_number_validation(event,this.value,5)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2" style="<?=$display_pricedisc?>">
                                        <div class="form-group" id="discamnt<?=$channel['id']?>_div">
                                            <div class="col-md-12 pl-sm pr-sm text-left">
                                                <label for="discamnt<?=$channel['id']?>" class="control-label">Disc. (<?=CURRENCY_CODE?>)</label>
                                                <input type="text" name="discamnt[]" id="discamnt<?=$channel['id']?>" class="form-control text-right discamnt" value="<?=((!empty($pricedata['multipleprice']) && $pricedata['pricetype']==0 && $pricedata['multipleprice'][0]['discount']>0)?number_format(($pricedata['multipleprice'][0]['price']*$pricedata['multipleprice'][0]['discount']/100),2,'.',''):"")?>" onkeypress="return decimal_number_validation(event,this.value,10)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group mt-xl" id="price<?=$channel['id']?>_div">
                                            <div class="col-sm-12">
                                                <div class="checkbox">
                                                    <input id="allowproduct<?=$channel['id']?>" type="checkbox" value="1" name="allowproduct<?=$channel['id']?>" class="checkradios m-n" <?=($pricedata['allowproduct']==1)?'checked':''?>>
                                                    <label style="font-size: 14px;" for="allowproduct<?=$channel['id']?>"> Allowed</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="focusedinput" class="col-sm-1 control-label pt-xs pl-n" style="text-align: left;">Price Type</label>
                                            <div class="col-sm-4 pl-n">
                                                <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                                    <div class="radio">
                                                        <input type="radio" name="pricetype<?=$channel['id']?>" id="singleqty<?=$channel['id']?>" class="pricetype" value="0" <?php if($pricedata['pricetype']==0){ echo "checked"; } ?>>
                                                        <label for="singleqty<?=$channel['id']?>">Single Quantity</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-xs-6 p-n">
                                                    <div class="radio">
                                                        <input type="radio" name="pricetype<?=$channel['id']?>" id="multipleqty<?=$channel['id']?>" class="pricetype" value="1" <?php if($pricedata['pricetype']==1){ echo "checked"; } ?>>
                                                        <label for="multipleqty<?=$channel['id']?>">Multiple Quantity</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="multiplepricesection<?=$channel['id']?>" style="<?=$display_multiprice?>">
                                        <div class="col-md-12"><hr></div>
                                        <div class="col-md-12 p-n">
                                            <div id="headingmultipleprice_<?=$channel['id']?>_1" class="col-md-4 headingmultipleprice<?=$channel['id']?>">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Price <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group text-right">
                                                        <div class="col-md-12 pl-xs">
                                                            <label class="control-label">Disc. (%)</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="headingmultipleprice_<?=$channel['id']?>_2" class="col-md-4 headingmultipleprice<?=$channel['id']?>" style="<?=(count($pricedata['multipleprice'])<=1)?"display:none;":"";?>">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Price <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group text-right">
                                                        <div class="col-md-12 pl-xs">
                                                            <label class="control-label">Disc. (%)</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="headingmultipleprice_<?=$channel['id']?>_3" class="col-md-4 headingmultipleprice<?=$channel['id']?>" style="<?=(count($pricedata['multipleprice'])<=2)?"display:none;":"";?>">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Price <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group text-right">
                                                        <div class="col-md-12 pl-xs">
                                                            <label class="control-label">Disc. (%)</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if($pricedata['pricetype']==1 && !empty($pricedata['multipleprice'])) {
                                            foreach($pricedata['multipleprice'] as $j=>$productquantityprice){ 
                                                $index = $j+1; ?>
                                                <div id="countmultipleprice_<?=$channel['id']?>_<?=$index?>" class="col-md-4 countmultipleprice<?=$channel['id']?>">
                                                    <input type="hidden" name="productbasicquantitypricesid[<?=$channel['id']?>][]" value="<?=$productquantityprice['id']?>">
                                                    <div class="col-md-4">
                                                        <div class="form-group mt-n" for="variantsalesprice_<?=$channel['id']?>_<?=$index?>" id="variantsalesprice_div_<?=$channel['id']?>_<?=$index?>">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <input type="text" id="variantsalesprice_<?=$channel['id']?>_<?=$index?>" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control variantsalesprices" name="variantsalesprice[<?=$channel['id']?>][]" value="<?=$productquantityprice['price']?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mt-n" for="variantqty_<?=$channel['id']?>_<?=$index?>" id="variantqty_div_<?=$channel['id']?>_<?=$index?>">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <input type="text" id="variantqty_<?=$channel['id']?>_<?=$index?>" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty[<?=$channel['id']?>][]" value="<?=$productquantityprice['quantity']?>" maxlength="4">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group mt-n text-right mt-n" for="variantdiscpercent_<?=$channel['id']?>_<?=$index?>" id="variantdiscpercent_div_<?=$channel['id']?>_<?=$index?>">
                                                            <div class="col-md-12 pl-xs">
                                                                <input type="text" id="variantdiscpercent_<?=$channel['id']?>_<?=$index?>" onkeypress="return decimal_number_validation(event,this.value,5)" class="form-control text-right variantdiscpercent" name="variantdiscpercent[<?=$channel['id']?>][]" value="<?=$productquantityprice['discount']?>" onkeyup="return onlypercentage(this.id)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 pl-xs pr-n">
                                                        <div class="form-group pt-sm mt-n">
                                                            <?php if ($j == 0) { ?>
                                                                <?php if (count($pricedata['multipleprice']) > 1) { ?>
                                                                    <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$channel['id']?>" onclick="removevariantprice(<?=$channel['id']?>,1)" style=""><i class="fa fa-minus"></i></button>
                                                                <?php }else { ?>
                                                                    <button type="button" class="btn btn-default btn-raised add_variantprice<?=$channel['id']?>" onclick="addnewvariantprice(<?=$channel['id']?>)"><i class="fa fa-plus"></i></button>
                                                                <?php } 
                                                            } else if ($j != 0) { ?>
                                                                <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$channel['id']?>" onclick="removevariantprice(<?=$channel['id']?>,<?=$index?>)"><i class="fa fa-minus"></i></button>
                                                            <? } ?>
                                                            
                                                            <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$channel['id']?>" onclick="removevariantprice(<?=$channel['id']?>,<?=$index?>)" style="display:none;"><i class="fa fa-minus"></i></button>
                                                            <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice<?=$channel['id']?>" onclick="addnewvariantprice(<?=$channel['id']?>)"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } 
                                        } else{ ?>
                                            <div id="countmultipleprice_<?=$channel['id']?>_1" class="col-md-4 countmultipleprice<?=$channel['id']?>">
                                                <div class="col-md-4">
                                                    <div class="form-group mt-n" for="variantsalesprice_<?=$channel['id']?>_1" id="variantsalesprice_div_<?=$channel['id']?>_1">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <input type="text" id="variantsalesprice_<?=$channel['id']?>_1" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control variantsalesprices" name="variantsalesprice[<?=$channel['id']?>][]" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mt-n" for="variantqty_<?=$channel['id']?>_1" id="variantqty_div_<?=$channel['id']?>_1">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <input type="text" id="variantqty_<?=$channel['id']?>_1" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty[<?=$channel['id']?>][]" value="" maxlength="4">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group mt-n text-right mt-n" for="variantdiscpercent_<?=$channel['id']?>_1" id="variantdiscpercent_div_<?=$channel['id']?>_1">
                                                        <div class="col-md-12 pl-xs">
                                                            <input type="text" id="variantdiscpercent_<?=$channel['id']?>_1" onkeypress="return decimal_number_validation(event,this.value,5)" class="form-control text-right variantdiscpercent" name="variantdiscpercent[<?=$channel['id']?>][]" value="" onkeyup="return onlypercentage(this.id)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-xs pr-n">
                                                    <div class="form-group pt-sm mt-n">
                                                        <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice<?=$channel['id']?>" onclick="removevariantprice(<?=$channel['id']?>,1)" style="display:none;"><i class="fa fa-minus"></i></button>
                                                        <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice<?=$channel['id']?>" onclick="addnewvariantprice(<?=$channel['id']?>)"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>             

            <?php } ?>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-body">
                        <div class="col-md-12">
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <?php if(!empty($pricelistdata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="UPDATE & Add New" class="btn btn-primary btn-raised">
                                    <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD New" class="btn btn-primary btn-raised">
                                    <?php } ?>
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
      </div>
    </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->