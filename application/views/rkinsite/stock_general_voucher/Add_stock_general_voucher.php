
<?php 
$PRODUCT_DATA = '';
if(!empty($productlistdata)){
    
    foreach($productlistdata as $product){
        $productname = str_replace("'","&apos;",$product['name']);
        if(DROPDOWN_PRODUCT_LIST==0){
            $PRODUCT_DATA .= '<option value="'.$product['id'].'">'.$productname.'</option>';
        }else{
            if($product['productimage']!="" && file_exists(PRODUCT_PATH.$product['productimage'])){
                $img = $product['productimage'];     
            }else{
                $img = PRODUCTDEFAULTIMAGE;
            }
            $PRODUCT_DATA .= '<option data-content="<img src=&apos;'.PRODUCT.$img.'&apos; style=&apos;width:40px&apos;> '.$productname.'" value="'.$product['id'].'">'.$productname.'</option>';   
        }
    } 
} 

$NARRATION_DATA = '';
if(!empty($narrationdata)){
    foreach($narrationdata as $narration){ 
        $NARRATION_DATA .= '<option value="'.$narration['id'].'">'.$narration['narration'].'</option>';
    }
}
$VARIANTDATA_DATA = '';
if(!empty($variantdata)){
    foreach($variantdata as $variant){ 
        $VARIANTDATA_DATA .= '<option value="'.$variant['id'].'">'.$variant['value'].'</option>';
    }
}

?>
<script>
    var PRODUCT_DATA = '<?=$PRODUCT_DATA?>';
    var NARRATION_DATA = '<?=$NARRATION_DATA?>';
    var VARIANTDATA_DATA = '<?=$VARIANTDATA_DATA?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($stockgeneralvoucherdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($stockgeneralvoucherdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                     
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body pt-n">
                <div class="col-sm-12">
                  <form class="form-horizontal" id="stockgeneralvoucherform">
                      <input type="hidden" name="stockgeneralvoucherid" value="<?php if(isset($stockgeneralvoucherdata)){ echo $stockgeneralvoucherdata['id']; } ?>">
                        <div class="row">
                            <div class="col-md-4 pl-sm pr-sm">
                                <div class="form-group" id="voucherno_div">
                                    <div class="col-sm-12">
                                        <label for="voucherno" class="control-label">Voucher No. <span class="mandatoryfield">*</span></label>
                                        <input id="voucherno" name="voucherno" class="form-control" value="<?php if(isset($stockgeneralvoucherdata)){ echo $stockgeneralvoucherdata['voucherno']; }else { echo $voucherno; } ?>" readonly> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 pl-sm pr-sm">
                                <div class="form-group" id="voucherdate_div">
                                    <div class="col-sm-12">
                                        <label for="voucherdate" class="control-label">Voucher Date <span class="mandatoryfield">*</span></label>
                                        <input id="voucherdate" type="text" name="voucherdate" value="<?php if(!empty($stockgeneralvoucherdata) && $stockgeneralvoucherdata['voucherdate']!="0000-00-00"){ echo $this->general_model->displaydate($stockgeneralvoucherdata['voucherdate']); }else{
                                            echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pl-sm pr-sm"><hr></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3 pl-xs pr-xs">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                    <label class="control-label">Select Product <span class="mandatoryfield">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 pl-xs pr-xs">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Select Variant</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 pl-xs pr-xs">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Qty</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 pl-xs pr-xs">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Price <span class="mandatoryfield">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 pl-xs pr-xs">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Total Price</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-1 pl-xs pr-xs">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Type</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 pl-xs pr-xs">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Narration</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                
                        <?php if(!empty($stockgeneralvoucherdata) && !empty($stockgeneralvoucherproductdata)) { ?>
                            <?php for ($i=0; $i < count($stockgeneralvoucherproductdata); $i++) { ?>
                                <div class="row countproducts" id="countproducts<?=($i+1)?>">
                                    <input type="hidden" name="stockgeneralvoucherproductid[]" value="<?=$stockgeneralvoucherproductdata[$i]['id']?>" id="stockgeneralvoucherproductid<?=($i+1)?>">
                                    <input type="hidden" name="uniqueproduct[]" id="uniqueproduct<?=($i+1)?>" value="<?=($stockgeneralvoucherproductdata[$i]['productid']."_".$stockgeneralvoucherproductdata[$i]['priceid']."_".$stockgeneralvoucherproductdata[$i]['price'])?>">
                                    <input type="hidden" name="productrow[]" value="<?=($i+1)?>">
                                    <div class="col-sm-3 pl-xs pr-xs">
                                        <div class="form-group" id="product<?=($i+1)?>_div">
                                            <div class="col-sm-12">
                                                <select id="productid<?=($i+1)?>" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                    <option value="0">Select Product</option>
                                                    
                                                    <?php

                                                    if(!empty($productlistdata)){
                                                 
                                                        foreach($productlistdata as $product){ 
                                                           
                                                            $productname = str_replace("'","&apos;",$product['name']);
                                                            if(DROPDOWN_PRODUCT_LIST==0){ ?>

                                                                <option value="<?=$product['id']?>" <?=($stockgeneralvoucherproductdata[$i]['productid']==$product['id']?"selected":"")?>><?=$product['name']?></option>

                                                            <?php }else{

                                                                if($product['productimage']!="" && file_exists(PRODUCT_PATH.$product['productimage'])){
                                                                    $img = $product['productimage'];
                                                                }else{
                                                                    $img = PRODUCTDEFAULTIMAGE;
                                                                }
                                                                ?>

                                                                <option data-content="<?php if(!empty($product['productimage'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> " value="<?php echo $product['id']; ?>" <?=($stockgeneralvoucherproductdata[$i]['productid']==$product['id']?"selected":"")?>><?php echo $productname; ?></option>
                                                            
                                                            <?php } ?>
                                                    <?php } 
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 pl-xs pr-xs">
                                        <div class="form-group" id="price<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <select id="priceid<?=($i+1)?>" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                    <option value="0">Select Variant</option>

                                                    <?php 
                                                    
                                                    if(!empty($narrationdata)){
                                             
                                                 foreach($narrationdata as $narration){ 
                                                     $narration = str_replace("'","&apos;",$product['name']);
                                                     if(DROPDOWN_PRODUCT_LIST==0){ ?>
                                                     
                                                         <option value="<?=$narration['id']?>" <?=($stockgeneralvoucherproductdata[$i]['productid']==$product['id']?"selected":"")?>><?=$narration['name']?></option>
                                                     <?php }else{



                                                         if($product['productimage']!="" && file_exists(PRODUCT_PATH.$product['productimage'])){
                                                             $img = $product['productimage'];
                                                         }else{
                                                             $img = PRODUCTDEFAULTIMAGE;
                                                         }
                                                         ?>
                                                         
                                                         <option data-content="<?php if(!empty($product['productimage'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> " value="<?php echo $product['id']; ?>" <?=($stockgeneralvoucherproductdata[$i]['productid']==$product['id']?"selected":"")?>><?php echo $productname; ?>1</option>
                                                     <?php } ?>
                                             <?php } 
                                             } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 pl-xs pr-xs">
                                        <div class="form-group" id="qty<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$stockgeneralvoucherproductdata[$i]['quantity']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="<?=($i+1)?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 pl-xs pr-xs">
                                        <div class="form-group" id="productprice<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <input id="price<?=($i+1)?>" name="price[]" class="form-control price text-right" onkeypress="return decimal_number_validation(event, this.value, 8)" div-id="<?=($i+1)?>" value="<?=$stockgeneralvoucherproductdata[$i]['price']?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 pl-xs pr-xs">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <input id="totalprice<?=($i+1)?>" name="totalprice[]" class="form-control totalprice text-right" onkeypress="return decimal_number_validation(event, this.value, 8)" div-id="<?=($i+1)?>" value="<?=$stockgeneralvoucherproductdata[$i]['totalprice']?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 pl-xs pr-xs">
                                        <div class="form-group" id="type<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <div class="yesno mt-xs">
                                                    <input type="checkbox" name="type<?=($i+1)?>" value="<?php if($stockgeneralvoucherproductdata[$i]['type']==1){ echo '1'; }else{ echo '0'; }?>" <?php if($stockgeneralvoucherproductdata[$i]['type']==1){ echo 'checked'; }?>>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 pl-xs pr-xs">
                                        <div class="form-group" id="narration<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <select id="narrationid<?=($i+1)?>" name="narrationid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                    <option value="0">Select Narration</option>
                                                    <?=$NARRATION_DATA?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 form-group m-n p-xs mt-xs">	
                                        <?php if($i==0){?>
                                            <?php if(count($stockgeneralvoucherproductdata)>1){ ?>
                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                            <?php }else { ?>
                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            <?php } ?>

                                        <?php }else if($i!=0) { ?>
                                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(<?=($i+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                        <?php } ?>
                                        <button type="button" class="btn btn-default btn-raised btn-sm remove_btn" onclick="removeproduct(<?=($i+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                    
                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
                                    </div>
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            getproductprice(<?=$i+1?>);
                                            $("#priceid<?=$i+1?>").val(<?=$stockgeneralvoucherproductdata[$i]['priceid']?>);
                                            $("#priceid<?=$i+1?>").selectpicker('refresh');
                                            $("#price<?=$i+1?>").val(<?=$stockgeneralvoucherproductdata[$i]['price']?>);
                                            $("#qty<?=($i+1)?>").val(<?=$stockgeneralvoucherproductdata[$i]['quantity']?>);
                                            calculatetotalprice(<?=$i+1?>);

                                            $("#qty<?=$i+1?>").TouchSpin(touchspinoptions);
                                            $("#narrationid<?=$i+1?>").val(<?=$stockgeneralvoucherproductdata[$i]['narrationid']?>);
                                            $("#narrationid<?=$i+1?>").selectpicker('refresh');
                                        });
                                    </script>
                                </div>
                            <?php } ?>
                        <?php }else{ ?>
                            <div class="row countproducts" id="countproducts1">
                                <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                <input type="hidden" name="productrow[]" value="1">
                                <div class="col-sm-3 pl-xs pr-xs">
                                    <div class="form-group" id="product1_div">
                                        <div class="col-sm-12">
                                            <select id="productid1" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                <option value="0">Select Product</option>
                                                <?=$PRODUCT_DATA?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 pl-xs pr-xs">
                                    <div class="form-group" id="price1_div">
                                        <div class="col-md-12">
                                            <select id="priceid1" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                <option value="0">Select Variant</option>
                                                <?=$VARIANTDATA_DATA?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1 pl-xs pr-xs">
                                    <div class="form-group" id="qty1_div">
                                        <div class="col-md-12">
                                            <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1 pl-xs pr-xs">
                                    <div class="form-group" id="productprice1_div">
                                        <div class="col-md-12">
                                            <input id="price1" name="price[]" class="form-control price text-right" onkeypress="return decimal_number_validation(event, this.value, 8)" div-id="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1 pl-xs pr-xs">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <input id="totalprice1" name="totalprice[]" class="form-control totalprice text-right" onkeypress="return decimal_number_validation(event, this.value, 8)" div-id="1" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1 pl-xs pr-xs">
                                    <div class="form-group" id="type1_div">
                                        <div class="col-md-12">
                                            <div class="yesno mt-xs">
                                                <input type="checkbox" name="type1" value="1" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2 pl-xs pr-xs">
                                    <div class="form-group" id="narration1_div">
                                        <div class="col-md-12">
                                            <select id="narrationid1" name="narrationid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                <option value="0">Select Narration</option>
                                                <?=$NARRATION_DATA?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 form-group m-n p-xs mt-xs">	
                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-12 pl-sm pr-sm"><hr></div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-sm-12 text-center">
                                    <?php if(!empty($stockgeneralvoucherdata)){ ?>
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
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->