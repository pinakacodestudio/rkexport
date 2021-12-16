<?php
    $channeloption = '';
    foreach($channeldata as $row){
        $select = ($channelid==$row['id'])?"selected":"";
        $channeloption .= '<option value="'.$row['id'].'" '.$select.'>'.$row['name'].'</option>';
    }
?>
<script type="text/javascript">
    var REWARDS_POINTS = '<?=REWARDSPOINTS?>';
    var ChannelDataHTML = '<?=$channeloption?>';
</script>
<style type="text/css">
.load_variantsdiv {
    /*border:1px solid lightgray;*/
    padding: 5px 25px;
    /*box-shadow: 0px 1px 1px black;*/
    margin-bottom: 10px;
}

.variant_div {
    box-shadow: 0px 2px 9px #333;
    padding: 5px;
}
</style>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if(isset($vendorproductdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?> Product</h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
                <li class="active"><?php if(isset($vendorproductdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?> Product</li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <?php
                                $membername = '';
                                if($vendordetail['channelid']!=0){
                                    $channellabel="";
                                    $key = array_search($vendordetail['channelid'], array_column($channellist, 'id'));
                                    if(!empty($channellist) && isset($channellist[$key])){
                                        $channellabel .= '<span class="label" style="background:'.$channellist[$key]['color'].'">'.substr($channellist[$key]['name'], 0, 1).'</span> ';
                                    }
                                    $membername = $channellabel." ".ucwords($vendordetail['name']);
                                }
                                ?>
                                <div class="col-md-4">
                                    <b>Vendor Name : </b> <?=$membername?> (<?=$vendordetail['membercode']?>)
                                </div>
                                <div class="col-md-4">
                                    <b>Email : </b> <?=$vendordetail['email']?>
                                </div>
                                <div class="col-md-4">
                                    <b>Mobile No. : </b> <?=$vendordetail['countrycode']!=""?$vendordetail['countrycode']." ".$vendordetail['mobile']:$vendordetail['mobile']?>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12"><hr></div>
                            <div class="col-sm-12 col-md-12 col-lg-12">
                                <form class="form-horizontal" id="vendorproductform" name="vendorproductform">
                                    <input type="hidden" name="vendorid" value="<?=$vendorid?>" id="vendorid">

                                    <?php if(isset($vendorproductdata) && !empty($vendorproductdata)){ ?>

                                        <input type="hidden" name="productid" value="<?=$productid?>" id="productid">
                                        <input type="hidden" name="priceid" value="<?=$priceid?>" id="priceid">
                                        <input type="hidden" name="memberproductorvariantid" value="<?=$vendorproductdata['memberproductorvariantid']?>" id="memberproductorvariantid">
                                        <input type="hidden" name="categoryid" value="<?=$vendorproductdata['categoryid']?>" id="categoryid">
                                        <div class="form-group row" for="category" id="categoryid_div">
                                            <div class="col-md-6">
                                                <h4><span class="text-muted">Category : </span><?php echo $vendorproductdata['categoryname'];?></h4>
                                            </div>
                                            <?php if(REWARDSPOINTS==1) { ?>
                                                <div class="col-md-6 text-right mt-sm mb-sm">
                                                    <span class="label label-info p-xs mr-sm" title="Points for Seller" style="font-size: 12px;">Points for Seller : <?=$vendorproductdata['pointsforseller']?></span>
                                                    <span class="label label-info p-xs" title="Points for Buyer" style="font-size: 12px;">Points for Buyer : <?=$vendorproductdata['pointsforbuyer']?></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php }else{ ?>
                                        <div class="form-group row" for="category" id="categoryid_div">
                                            <label class="col-md-4 control-label" for="categoryid">Category <span class="mandatoryfield"> * </span></label>
                                            <div class="col-md-4">
                                                <select class="form-control selectpicker" id="categoryid" name="categoryid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                    <option value="0" selected>Select Category</option>
                                                    <?php foreach($maincategorydata as $row){ ?>
                                                    <option value="<?php echo $row['id']; ?>" <?php if(isset($productdata)){ if($productdata['categoryid']== $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
                                                    <?php }?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                    <?php } ?>

                                    <hr>
                                    <div id="load_variants" class="row">
                                        <?php if(isset($vendorproductdata) && !empty($vendorproductdata)){ ?>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="width5">Sr. No.</th>
                                                        <th width="15%">Product Name</th>
                                                        <th class="text-right">Price</th>
                                                        <th width="55%" class="text-center">Details</th>
                                                        <th class="width5 text-center">Allow Product</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                <?php 
                                                if(!empty($vendorproductdata)){
                                                    $count=0; 
                                                    
                                                    $display_pricedisc = $display_multiprice = "display: none;";
                                                    if($vendorproductdata['pricetype']==0){
                                                        $display_pricedisc = "";
                                                    }else{
                                                        $display_multiprice = "";
                                                    } ?>

                                                        <tr>
                                                            <td id="srno" class="text-center"><?=++$count; ?></td>
                                                            <td><?=ucwords($vendorproductdata['name'])?></td>
                                                            <td class="text-right"><?=number_format($vendorproductdata['price'],2,'.',',')?></td>
                                                            <td>
                                                                <div class="col-md-3 pl-n pr-xs" style="<?=$display_pricedisc?>">
                                                                    <div class="form-group m-n" id="memberprice0_div">
                                                                        <input type="text" name="memberprice" id="memberprice" class="form-control text-right m-n memberprice" value="<?=((isset($vendorproductdata['multiplepricedata']) && $vendorproductdata['pricetype']==0)?number_format($vendorproductdata['multiplepricedata'][0]['price'],2,'.',''):"")?>" onkeypress="return decimal_number_validation(event,this.value,8)" placeholder="Member Price">
                                                                        <input type="hidden" name="singlequantitypricesid" value="<?=($vendorproductdata['pricetype']==0 && !empty($vendorproductdata['multiplepricedata'])?$vendorproductdata['multiplepricedata'][0]['id']:"")?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 pr-sm pl-xs" style="<?=$display_pricedisc?>">
                                                                    <div class="form-group m-n" id="salesprice0_div">
                                                                        <input type="text" name="salesprice" id="salesprice" class="form-control text-right m-n" value="<?=((isset($vendorproductdata['multiplepricedata']) && $vendorproductdata['pricetype']==0)?number_format($vendorproductdata['multiplepricedata'][0]['salesprice'],2,'.',''):"")?>" onkeypress="return decimal_number_validation(event,this.value,8)" placeholder="Sales Price">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 pr-xs pl-n">
                                                                    <div class="form-group m-n" id="memberstock_div">
                                                                        <input type="text" name="memberstock" id="memberstock" class="form-control text-right m-n" value="<?=$vendorproductdata['memberstock']?>" onkeypress="return decimal_number_validation(event,this.value,8)" placeholder="Vendor Stock">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-3 pr-n pl-xs">
                                                                    <div class="form-group m-n" for="channelid" id="channelid_div">
                                                                        <select class="form-control selectpicker m-n" id="channelid" name="channelid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <?php foreach($channeldata as $row){ ?>
                                                                            <option value="<?php echo $row['id']; ?>" <?php if(isset($vendorproductdata)){ if($vendorproductdata['channelid']== $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
                                                                            <?php }?>
                                                                        </select>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-12 p-n">
                                                                    <div class="col-md-3 pl-n pr-xs">
                                                                        <div class="form-group m-n p-n" id="minqty_div">
                                                                            <div class="col-md-12 p-n text-left">
                                                                                <label for="minqty" class="control-label">Min. Qty</label>
                                                                                <input type="text" style="width:100%;" name="minqty" id="minqty" class="form-control text-right" value="<?=($vendorproductdata['minimumqty']>0)?$vendorproductdata['minimumqty']:""?>" onkeypress="return isNumber(event)" maxlength="4">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 pr-xs pl-xs">
                                                                        <div class="form-group m-n p-n" id="maxqty_div">
                                                                            <div class="col-md-12 p-n text-left">
                                                                                <label for="maxqty" class="control-label">Max. Qty</label>
                                                                                <input type="text" style="width:100%;" name="maxqty" id="maxqty" class="form-control text-right" value="<?=($vendorproductdata['maximumqty']>0)?$vendorproductdata['maximumqty']:""?>" onkeypress="return isNumber(event)" maxlength="4">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 pl-xs pr-xs" style="<?=$display_pricedisc?>">
                                                                        <div class="form-group m-n p-n" id="discper0_div">
                                                                            <div class="col-md-12 p-n text-left">
                                                                                <label for="discper" class="control-label">Disc. (%)</label>
                                                                                <input type="text" style="width:100%;" name="discper" id="discper" class="form-control text-right discper" value="<?=((isset($vendorproductdata['multiplepricedata']) && $vendorproductdata['pricetype']==0 && $vendorproductdata['multiplepricedata'][0]['discount']>0)?number_format($vendorproductdata['multiplepricedata'][0]['discount'],2,'.',''):"")?>" onkeypress="return decimal_number_validation(event,this.value,5)">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-3 pr-n pl-xs" style="<?=$display_pricedisc?>">
                                                                        <div class="form-group m-n p-n" id="discamnt0_div">
                                                                            <div class="col-md-12 p-n text-left">
                                                                                <label for="discamnt" class="control-label">Disc. (<?=CURRENCY_CODE?>)</label>
                                                                                <input type="text" style="width:100%;" name="discamnt" id="discamnt" class="form-control text-right discamnt" value="<?=((isset($vendorproductdata['multiplepricedata']) && $vendorproductdata['pricetype']==0 && $vendorproductdata['multiplepricedata'][0]['discount']>0)?number_format(($vendorproductdata['multiplepricedata'][0]['price']*$vendorproductdata['multiplepricedata'][0]['discount']/100),2,'.',''):"")?>" onkeypress="return decimal_number_validation(event,this.value,10)">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="focusedinput" class="col-sm-3 control-label pt-xs pl-n" style="text-align: left;">Price Type</label>
                                                                        <div class="col-sm-8 pl-n">
                                                                            <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                                                                <div class="radio">
                                                                                    <input type="radio" name="pricetype" id="singleqty0" class="pricetype" value="0" <?php if($vendorproductdata['pricetype']==0){ echo "checked"; } ?>>
                                                                                    <label for="singleqty0">Single Quantity</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 col-xs-6 p-n">
                                                                                <div class="radio">
                                                                                    <input type="radio" name="pricetype" id="multipleqty0" class="pricetype" value="1" <?php if($vendorproductdata['pricetype']==1){ echo "checked"; } ?>>
                                                                                    <label for="multipleqty0">Multiple Quantity</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row" id="multiplepricesection0" style="<?=$display_multiprice?>">
                                                                    <div class="col-md-12">
                                                                        <div id="headingmultipleprice0" class="headingmultipleprice0">
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <div class="col-md-12 pr-xs pl-n">
                                                                                        <label class="control-label">Price <span class="mandatoryfield">*</span></label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <div class="form-group">
                                                                                    <div class="col-md-12 pr-xs pl-xs">
                                                                                        <label class="control-label">Sales Price <span class="mandatoryfield">*</span></label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group">
                                                                                    <div class="col-md-12 pr-xs pl-xs">
                                                                                        <label class="control-label">Quantity <span class="mandatoryfield">*</span></label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <div class="form-group text-right">
                                                                                    <div class="col-md-12 pl-xs">
                                                                                        <label class="control-label">Disc. (%)</label>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <?php 
                                                                        if(isset($vendorproductdata['multiplepricedata']) && $vendorproductdata['pricetype']==1){
                                                                            foreach($vendorproductdata['multiplepricedata'] as $kp=>$multipleprice){ ?>
                                                                                <div id="countmultipleprice_0_<?=$kp+1?>" class="countmultipleprice0">
                                                                                    <input type="hidden" name="memberproductquantitypriceid[]" value="<?=$multipleprice['id']?>">
                                                                                    <div class="col-md-3">
                                                                                        <div class="form-group mt-n" for="variantprice_0_<?=$kp+1?>" id="variantprice_div_0_<?=$kp+1?>">
                                                                                            <div class="col-md-12 pr-xs pl-n">
                                                                                                <input type="text" id="variantprice_0_<?=$kp+1?>" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control variantprices text-right" name="variantprice[]" value="<?=$multipleprice['price']?>">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <div class="form-group mt-n" for="variantsalesprice_0_<?=$kp+1?>" id="variantsalesprice_div_0_<?=$kp+1?>">
                                                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                                                <input type="text" id="variantsalesprice_0_<?=$kp+1?>" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control variantsalesprices text-right" name="variantsalesprice[]" value="<?=$multipleprice['salesprice']?>">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="form-group mt-n" for="variantqty_0_<?=$kp+1?>" id="variantqty_div_0_<?=$kp+1?>">
                                                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                                                <input type="text" id="variantqty_0_<?=$kp+1?>" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty[]" value="<?=$multipleprice['quantity']?>" maxlength="4">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="form-group text-right mt-n" for="variantdiscpercent_0_<?=$kp+1?>" id="variantdiscpercent_div_0_<?=$kp+1?>">
                                                                                            <div class="col-md-12 pl-xs">
                                                                                                <input type="text" id="variantdiscpercent_0_<?=$kp+1?>" onkeypress="return decimal_number_validation(event,this.value,5)" class="form-control text-right variantdiscpercent" name="variantdiscpercent[]" value="<?=$multipleprice['discount']?>" onkeyup="return onlypercentage(this.id)">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2">
                                                                                        <div class="form-group pt-sm mt-n">
                                                                                            <?php if ($kp == 0) { ?>
                                                                                            <?php if (count($vendorproductdata['multiplepricedata']) > 1) { ?>
                                                                                                <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice0" onclick="removevariantprice(0,1)"><i class="fa fa-minus"></i></button>
                                                                                                <?php }else { ?>
                                                                                                    <button type="button" class="btn btn-primary btn-raised add_variantprice0" onclick="addnewvariantprice(0,'edit')"><i class="fa fa-plus"></i></button>
                                                                                                <?php } 
                                                                                            } else if ($kp != 0) { ?>
                                                                                                <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice0" onclick="removevariantprice(0,<?=$kp+1?>)"><i class="fa fa-minus"></i></button>
                                                                                            <? } ?>
                                                                                            
                                                                                            <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice0" onclick="removevariantprice(0,<?=$kp+1?>)" style="display:none;"><i class="fa fa-minus"></i></button>
                                                                                            <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice0" onclick="addnewvariantprice(0,'edit')"><i class="fa fa-plus"></i></button>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php }
                                                                        }else{ ?>
                                                                            <div id="countmultipleprice_0_1" class="countmultipleprice0">
                                                                                <div class="col-md-3">
                                                                                    <div class="form-group mt-n" for="variantprice_0_1" id="variantprice_div_0_1">
                                                                                        <div class="col-md-12 pr-xs pl-n">
                                                                                            <input type="text" id="variantprice_0_1" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control variantprices text-right" name="variantprice[]" value="">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <div class="form-group mt-n" for="variantsalesprice_0_1" id="variantsalesprice_div_0_1">
                                                                                        <div class="col-md-12 pr-xs pl-xs">
                                                                                            <input type="text" id="variantsalesprice_0_1" onkeypress="return decimal_number_validation(event,this.value,10)" class="form-control variantsalesprices text-right" name="variantsalesprice[]" value="">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="form-group mt-n" for="variantqty_0_1" id="variantqty_div_0_1">
                                                                                        <div class="col-md-12 pr-xs pl-xs">
                                                                                            <input type="text" id="variantqty_0_1" onkeypress="return isNumber(event)" class="form-control variantqty" name="variantqty[]" value="" maxlength="4">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="form-group text-right mt-n" for="variantdiscpercent_0_1" id="variantdiscpercent_div_0_1">
                                                                                        <div class="col-md-12 pl-xs">
                                                                                            <input type="text" id="variantdiscpercent_0_1" onkeypress="return decimal_number_validation(event,this.value,5)" class="form-control text-right variantdiscpercent" name="variantdiscpercent[]" value="" onkeyup="return onlypercentage(this.id)">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-2">
                                                                                    <div class="form-group pt-sm mt-n">
                                                                                        <button type="button" class="btn btn-danger btn-raised btn-sm remove_variantprice0" onclick="removevariantprice(0,1)" style="display:none;"><i class="fa fa-minus"></i></button>
                                                                                        <button type="button" class="btn btn-primary btn-raised btn-sm add_variantprice0" onclick="addnewvariantprice(0,'edit')"><i class="fa fa-plus"></i></button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="checkbox"><input id="allowcheck" type="checkbox" value="1" name="allowcheck" class="checkradios m-n" <?=($vendorproductdata['allowproduct']==1)?'checked':''?>>
                                                                <label for="allowcheck"></label></div>
                                                            </td>
                                                        </tr>

                                                    <?php 
                                                    }else{ ?>
                                                        <tr>
                                                            <td colspan="3" style="text-align: center;">No data available in table.</td>
                                                        </tr>
                                                    <? } ?>
                                                    
                                                </tbody>
                                            </table>
                                        <?php } ?>
                                    </div>
                                    <div class="row">
                                        <label for="focusedinput" class="col-sm-4 control-label"></label>
                                        <div class="col-sm-12 text-center">
                                            <?php if(isset($vendorproductdata)){ ?>
                                                <input type="button" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                            <?php }else{ ?>
                                                <input type="button" onclick="checkvalidation('add')" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                                <input type="button" onclick="checkvalidation('addandnew')" name="submit" value="<?=addandnew_text?>" class="<?=addandnew_class?>">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                            <?php } ?>
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>vendor/vendor-detail/<?=$vendorid?>/products" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="modal fade" id="addsellerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Search Seller Member</h4>
                    </div>
                    <div class="modal-body" style="padding-top: 4px;">
                        <form action="#" id="addsellerform" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" id="sellercode_div">
                                        <label class="col-sm-4 control-label" for="sellercode">Seller Code <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-6">
                                            <input id="sellercode" type="text" name="sellercode" class="form-control" value="">
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
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div> -->
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->