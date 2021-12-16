
<?php
$PRODUCTDATA = "";
if(!empty($productdata)){
  foreach($productdata as $product){ 
    $productname = str_replace("'","&apos;",$product['name']);
    if(DROPDOWN_PRODUCT_LIST==0){
        $PRODUCTDATA .= '<option value="'.$product["id"].'">'.addslashes($productname).'</option>';
    }else{
        $content = "";
        if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
            $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
        }else{
            $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
        }
        $PRODUCTDATA .= '<option data-content="'.$content.'" value="'.$product['id'].'">'.addslashes($productname).'</option>';
    }
  } 
}
?>
<style>
  .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
  .toggle.ios .toggle-handle { border-radius: 20px; }
  .toggle.android { border-radius: 0px;}
  .toggle.android .toggle-handle { border-radius: 0px; }

    .productvariantdiv {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
    }
</style>
<script>
    var PRODUCTDATA = '<?=$PRODUCTDATA?>';
    var UNITDATA = "";
    
    <?php foreach($unitdata as $unit){ ?>
        UNITDATA += '<option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>';
    <?php } ?>
    var oldcommonpriceid = [];
    var ISDUPLICATE = '<?=(isset($isduplicate) && isset($productrecepiedata)?1:0)?>';
    var DUPPRODUCTID = '<?=(isset($isduplicate) && isset($productrecepiedata)?$productrecepiedata['productid']:0)?>';
    var DUPIsUniversalProduct = '<?=(isset($isduplicate) && isset($productrecepiedata)?$productrecepiedata['isuniversal']:0)?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(!isset($isduplicate) && isset($productrecepiedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(!isset($isduplicate) && isset($productrecepiedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
            <form class="form-horizontal" id="product-recepie-form">
                <input type="hidden" name="productrecepieid" id="productrecepieid" value="<?php if(isset($productrecepiedata)){ echo $productrecepiedata['id']; } ?>">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-heading"><h2 style="font-weight:600;">Product Recepie</h2></div>  
                        <div class="panel-body no-padding">
                            <div class="row m-n">
                                <div class="col-sm-6">
                                   <div class="form-group" id="productid_div">
                                        <label class="col-md-3 pl-n pr-n control-label" for="productid">Product Name <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-6">
                                            <input type="hidden" name="postproductid" name="postproductid" value="<?php if(isset($productrecepiedata)){ echo $productrecepiedata['productid']; } ?>">
                                            <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" <?php if(!isset($isduplicate) && isset($productrecepiedata)){ echo "disabled"; } ?>>
                                                <option value="0">Select Product</option>
                                                <?php foreach($regularproductdata as $product){ 

                                                    $productname = str_replace("'","&apos;",$product['name']);
                                                    if(DROPDOWN_PRODUCT_LIST==0){ ?>

                                                        <option value="<?php echo $product['id']; ?>" data-isuniversal="<?php echo $product['isuniversal']; ?>" <?php if(!isset($isduplicate) && isset($productrecepiedata) && $productrecepiedata['productid']==$product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>

                                                    <?php }else{

                                                        if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                            $img = $product['image'];
                                                        }else{
                                                            $img = PRODUCTDEFAULTIMAGE;
                                                        }
                                                        ?>

                                                        <option data-content="<img src='<?=PRODUCT.$img?>' style='width:40px'> <?php echo $productname; ?>" value="<?php echo $product['id']; ?>" data-isuniversal="<?php echo $product['isuniversal']; ?>" <?php if(!isset($isduplicate) && isset($productrecepiedata) && $productrecepiedata['productid']==$product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>

                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12"><hr></div>
                                <div class="col-md-12" id="commonrawpanel" style="<?php if(!isset($productrecepiedata)){ echo "display:none;"; } ?>">
                                    <div class="panel panel-transparent mb-n" id="commonpanel">
                                        <div class="panel-heading p-n">
                                            <h2 style="font-weight:600;">Common Raw Material</h2>
                                        </div> 
                                        <div class="panel-body no-padding productvariantdiv">
                                            <div class="col-sm-6 pl-sm pr-sm">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Product Name <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Variant <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Unit <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group text-right">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Value <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 pl-sm pr-sm" id="commonrawrightlabel" style="<?php if(!isset($recepiecommonmaterialdata) || (isset($recepiecommonmaterialdata) && count($recepiecommonmaterialdata) <= 1)){ echo "display:none;"; } ?>">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Product Name <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Variant <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Unit <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group text-right">
                                                        <div class="col-sm-12 pr-xs pl-xs">
                                                            <label class="control-label">Value <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 p-n mb-sm">
                                                <?php if(isset($recepiecommonmaterialdata) && !empty($recepiecommonmaterialdata)) { ?>
                                                <input type="hidden" name="removerecepiecommonmaterialid" id="removerecepiecommonmaterialid">
                                                    <?php for ($i=0; $i < count($recepiecommonmaterialdata); $i++) { ?>
                                                        <div class="col-sm-6 count_common_raw_product pl-sm pr-sm" id="countcommonrawproduct<?=($i+1)?>">
                                                            <input type="hidden" name="recepiecommonmaterialid[]" value="<?=(!isset($isduplicate))?$recepiecommonmaterialdata[$i]['id']:""?>" id="recepiecommonmaterialid<?=$i+1?>">
                                                            <div class="col-sm-3">
                                                                <div class="form-group" id="commonproductid<?=($i+1)?>_div">
                                                                    <div class="col-sm-12 pr-xs pl-xs">
                                                                        <select id="commonproductid<?=($i+1)?>" name="commonproductid[]" class="selectpicker form-control commonproductid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0">Select Product</option>
                                                                            <?php foreach($productdata as $product){ 
                                                                                $productname = str_replace("'","&apos;",$product['name']);
                                                                                if(DROPDOWN_PRODUCT_LIST==0){ ?>

                                                                                    <option value="<?php echo $product['id']; ?>" <?php if($recepiecommonmaterialdata[$i]['productid'] == $product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>

                                                                                <?php }else{

                                                                                    if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                                        $img = $product['image'];
                                                                                    }else{
                                                                                        $img = PRODUCTDEFAULTIMAGE;
                                                                                    }
                                                                                    ?>

                                                                                    <option data-content="<img src='<?=PRODUCT.$img?>' style='width:40px'> <?php echo $productname; ?>" value="<?php echo $product['id']; ?>" <?php if($recepiecommonmaterialdata[$i]['productid'] == $product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>

                                                                                <?php } ?>
                                                                                
                                                                            <?php } ?>
                                                                        </select>
                                                                        <input type="hidden" id="uniquerawproduct<?=($i+1)?>" name="uniquerawproduct[]" value="<?=$recepiecommonmaterialdata[$i]['productid']."_".$recepiecommonmaterialdata[$i]['unitid']?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-3">
                                                                <div class="form-group" id="commonpriceid<?=$i+1?>_div">
                                                                    <div class="col-sm-12 pr-xs pl-xs">
                                                                        <select id="commonpriceid<?=$i+1?>" name="commonpriceid[]" class="selectpicker form-control commonpriceid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0">Select Variant</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group" id="commonunitid<?=($i+1)?>_div">
                                                                    <div class="col-sm-12 pr-xs pl-xs">
                                                                        <select id="commonunitid<?=($i+1)?>" name="commonunitid[]" class="selectpicker form-control commonunitid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0">Unit</option>
                                                                            <?php foreach($unitdata as $unit){ ?>
                                                                                <option value="<?php echo $unit['id']; ?>" <?php if($recepiecommonmaterialdata[$i]['unitid'] == $unit['id']){ echo "selected"; } ?>><?php echo $unit['name']; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-2">
                                                                <div class="form-group" id="commonvalue<?=($i+1)?>_div">
                                                                    <div class="col-sm-12 pr-xs pl-xs">
                                                                        <input type="text" id="commonvalue<?=($i+1)?>" class="form-control commonvalue text-right" name="commonvalue[]" value="<?php echo $recepiecommonmaterialdata[$i]['value']; ?>" onkeypress="return decimal_number_validation(event,this.value,8)">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 pt-md pr-xs">
                                                                <?php if($i==0){?>
                                                                    <?php if(count($recepiecommonmaterialdata)>1){ ?>
                                                                        <button type="button" class="btn btn-default btn-raised remove_crp_btn" onclick="removeCommonRawProduct(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                    <?php }else { ?>
                                                                        <button type="button" class="btn btn-default btn-raised add_crp_btn" onclick="addNewCommonRawProduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                    <?php } ?>
                                                                <? }else if($i!=0) { ?>
                                                                    <button type="button" class="btn btn-default btn-raised remove_crp_btn" onclick="removeCommonRawProduct(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                <? } ?>
                                                                <button type="button" class="btn btn-default btn-raised btn-sm remove_crp_btn" onclick="removeCommonRawProduct(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                <button type="button" class="btn btn-default btn-raised add_crp_btn" onclick="addNewCommonRawProduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(document).ready(function() {
                                                                    oldcommonpriceid.push(<?=$recepiecommonmaterialdata[$i]['rawpriceid']?>);
                                                                    getproductprice(<?=$i+1?>,'common');
                                                                });
                                                            </script>
                                                        </div>
                                                    <?php } ?>
                                                <?php }else{ ?>
                                                    <div class="col-sm-6 count_common_raw_product pl-sm pr-sm" id="countcommonrawproduct1">
                                                        <div class="col-sm-3">
                                                            <div class="form-group" id="commonproductid1_div">
                                                                <div class="col-sm-12 pr-xs pl-xs">
                                                                    <select id="commonproductid1" name="commonproductid[]" class="selectpicker form-control commonproductid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                        <option value="0">Select Product</option>
                                                                        <?php echo $PRODUCTDATA; ?>
                                                                    </select>
                                                                    <input type="hidden" id="uniquerawproduct1" name="uniquerawproduct[]" value="0_0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group" id="commonpriceid1_div">
                                                                <div class="col-sm-12 pr-xs pl-xs">
                                                                    <select id="commonpriceid1" name="commonpriceid[]" class="selectpicker form-control commonpriceid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                        <option value="0">Select Variant</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group" id="commonunitid1_div">
                                                                <div class="col-sm-12 pr-xs pl-xs">
                                                                    <select id="commonunitid1" name="commonunitid[]" class="selectpicker form-control commonunitid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                        <option value="0">Unit</option>
                                                                        <?php foreach($unitdata as $unit){ ?>
                                                                            <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group" id="commonvalue1_div">
                                                                <div class="col-sm-12 pr-xs pl-xs">
                                                                    <input type="text" id="commonvalue1" class="form-control commonvalue text-right" name="commonvalue[]" value="" onkeypress="return decimal_number_validation(event,this.value,8)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pt-md pr-xs">
                                                            <button type="button" class="btn btn-default btn-raised remove_crp_btn m-n" onclick="removeCommonRawProduct(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                                            <button type="button" class="btn btn-default btn-raised add_crp_btn m-n" onclick="addNewCommonRawProduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div> 

                                <div class="col-md-12 p-n" id="variantwisepanel" style="display:none;">
                                    <input type="hidden" name="removerecepievariantwisematerialid" id="removerecepievariantwisematerialid">
                                    <div class="panel panel-transparent" id="commonpanel">
                                        <div class="panel-heading">
                                            <h2 style="font-weight:600;">Variant Wise Material</h2>
                                        </div> 
                                        <div class="panel-body no-padding" id="variantmaterialdata">
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <?php if(!isset($isduplicate) && !empty($productrecepiedata)){ ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php }else{ ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php } ?>
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php /* 
                <div class="col-md-12" id="commonrawpanel" style="<?php if(!isset($productrecepiedata)){ echo "display:none;"; } ?>">
                    <div class="panel panel-default border-panel">
                        <div class="panel-heading"><h2>Common Raw Material</h2></div>  
                        <div class="panel-body pt-n pb-n">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="col-sm-5">
                                        <div class="form-group">
                                            <div class="col-sm-12 pr-xs pl-xs">
                                                <label class="control-label">Product Name</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <div class="col-sm-12 pr-xs pl-xs">
                                                <label class="control-label">Unit</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <div class="col-sm-12 pr-xs pl-xs">
                                                <label class="control-label">Value</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 p-n">
                                    <?php if(isset($recepiecommonmaterialdata) && !empty($recepiecommonmaterialdata)) { ?>
                                    <input type="hidden" name="removerecepiecommonmaterialid" id="removerecepiecommonmaterialid">
                                    <?php for ($i=0; $i < count($recepiecommonmaterialdata); $i++) { ?>
                                        <div class="col-sm-6 count_common_raw_product" id="countcommonrawproduct<?=($i+1)?>">
                                            <input type="hidden" name="recepiecommonmaterialid[]" value="<?=$recepiecommonmaterialdata[$i]['id']?>" id="recepiecommonmaterialid<?=$i+1?>">
                                            <div class="col-sm-5">
                                                <div class="form-group" id="commonproductid<?=($i+1)?>_div">
                                                    <div class="col-sm-12 pr-xs pl-xs">
                                                        <select id="commonproductid<?=($i+1)?>" name="commonproductid[]" class="selectpicker form-control commonproductid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                            <option value="0">Select Product</option>
                                                            <?php foreach($productdata    as $productrow){ ?>
                                                                <option value="<?php echo $productrow['id']; ?>" <?php if($recepiecommonmaterialdata[$i]['productid'] == $productrow['id']){ echo "selected"; } ?>><?php echo $productrow['name']; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                        <input type="hidden" id="uniquerawproduct<?=($i+1)?>" name="uniquerawproduct[]" value="<?=$recepiecommonmaterialdata[$i]['productid']."_".$recepiecommonmaterialdata[$i]['unitid']?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="commonunitid<?=($i+1)?>_div">
                                                <div class="col-sm-12 pr-xs pl-xs">
                                                    <select id="commonunitid<?=($i+1)?>" name="commonunitid[]" class="selectpicker form-control commonunitid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                        <option value="0">Unit</option>
                                                        <?php foreach($unitdata as $unit){ ?>
                                                            <option value="<?php echo $unit['id']; ?>" <?php if($recepiecommonmaterialdata[$i]['unitid'] == $unit['id']){ echo "selected"; } ?>><?php echo $unit['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group" id="commonvalue<?=($i+1)?>_div">
                                                <div class="col-sm-12 pr-xs pl-xs">
                                                    <input type="text" id="commonvalue<?=($i+1)?>" class="form-control commonvalue text-right" name="commonvalue[]" value="<?php echo $recepiecommonmaterialdata[$i]['value']; ?>" onkeypress="return decimal_number_validation(event,this.value,8)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pt-md pr-xs">
                                            <?php if($i==0){?>
                                                <?php if(count($recepiecommonmaterialdata)>1){ ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_crp_btn" onclick="removeCommonRawProduct(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                <?php }else { ?>
                                                    <button type="button" class="btn btn-default btn-raised add_crp_btn" onclick="addNewCommonRawProduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                <?php } ?>
                                            <? }else if($i!=0) { ?>
                                                <button type="button" class="btn btn-default btn-raised remove_crp_btn" onclick="removeCommonRawProduct(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                            <? } ?>
                                            <button type="button" class="btn btn-default btn-raised btn-sm remove_crp_btn" onclick="removeCommonRawProduct(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                            <button type="button" class="btn btn-default btn-raised add_crp_btn" onclick="addNewCommonRawProduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                                        </div>
                                    </div>
                                    <?php } ?>
                                    <?php }else{ ?>
                                    <div class="col-sm-6 count_common_raw_product" id="countcommonrawproduct1">
                                        <div class="col-sm-5">
                                            <div class="form-group" id="commonproductid1_div">
                                                <div class="col-sm-12 pr-xs pl-xs">
                                                    <select id="commonproductid1" name="commonproductid[]" class="selectpicker form-control commonproductid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                        <option value="0">Select Product</option>
                                                        <?php foreach($productdata    as $productrow){ ?>
                                                            <option value="<?php echo $productrow['id']; ?>"><?php echo $productrow['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                    <input type="hidden" id="uniquerawproduct1" name="uniquerawproduct[]" value="0_0">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="form-group" id="commonunitid1_div">
                                                <div class="col-sm-12 pr-xs pl-xs">
                                                    <select id="commonunitid1" name="commonunitid[]" class="selectpicker form-control commonunitid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                        <option value="0">Unit</option>
                                                        <?php foreach($unitdata as $unit){ ?>
                                                            <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group" id="commonvalue1_div">
                                                <div class="col-sm-12 pr-xs pl-xs">
                                                    <input type="text" id="commonvalue1" class="form-control commonvalue text-right" name="commonvalue[]" value="" onkeypress="return decimal_number_validation(event,this.value,8)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pt-md pr-xs">
                                            <button type="button" class="btn btn-default btn-raised remove_crp_btn m-n" onclick="removeCommonRawProduct(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                            <button type="button" class="btn btn-default btn-raised add_crp_btn m-n" onclick="addNewCommonRawProduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  */?>
            </form>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->