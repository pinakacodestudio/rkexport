<?php 
$PRODUCT_DATA = '';
if(!empty($productdata)){
    foreach($productdata as $product){
        $json = str_replace('"', "&quot;",json_encode($product['variantdata']));
        $productname = str_replace("'","&apos;",$product['name']);
        if(DROPDOWN_PRODUCT_LIST==0){
            $PRODUCT_DATA .= '<option value="'.$product["id"].'" data-variants="'.$json.'">'.addslashes($productname).'</option>';
        }else{
            $content = "";
            if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
                $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
            }else{
                $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
            }
            $PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'" data-variants="'.$json.'">'.addslashes($productname).'</option>';
        }
    } 
}
$UNIT_DATA = '';
if(!empty($unitdata)){
    foreach($unitdata as $unit){
        $UNIT_DATA .= '<option value="'.$unit['id'].'">'.$unit['name'].'</option>';
    } 
} ?>
<script>
    var PRODUCT_DATA = '<?=$PRODUCT_DATA?>';
    var UNIT_DATA = '<?=$UNIT_DATA?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($rawmaterialrequestdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($rawmaterialrequestdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                  <form class="form-horizontal" id="rawmaterialrequestform">
                      <input type="hidden" name="rawmaterialrequestid" value="<?php if(isset($rawmaterialrequestdata)){ echo $rawmaterialrequestdata['id']; } ?>">
                      <input type="hidden" name="orderid" value="<?php if(isset($rawmaterialrequestdata)){ echo $rawmaterialrequestdata['orderid']; }else if(isset($orderid)){ echo $orderid; }else{ echo 0; } ?>">
                        <div class="row">
                            <div class="col-md-4 pl-sm pr-sm">
                                <div class="form-group" id="requestno_div">
                                    <div class="col-sm-12">
                                        <label for="requestno" class="control-label">Request No. <span class="mandatoryfield">*</span></label>
                                        <input id="requestno" name="requestno" class="form-control" value="<?php if(isset($rawmaterialrequestdata)){ echo $rawmaterialrequestdata['requestno']; }else { echo $requestno; } ?>" readonly> 
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 pl-sm pr-sm">
                                <div class="form-group" id="requestdate_div">
                                    <div class="col-sm-12">
                                        <label for="requestdate" class="control-label">Request Date <span class="mandatoryfield">*</span></label>
                                        <input id="requestdate" type="text" name="requestdate" value="<?php if(!empty($rawmaterialrequestdata) && $rawmaterialrequestdata['requestdate']!="0000-00-00"){ echo $this->general_model->displaydate($rawmaterialrequestdata['requestdate']); }else{
                                            echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 pl-sm pr-sm">
                                <div class="form-group" id="estimatedate_div">
                                    <div class="col-sm-12">
                                        <label for="estimatedate" class="control-label">Estimate Date</label>
                                        <input id="estimatedate" type="text" name="estimatedate" value="<?php if(!empty($rawmaterialrequestdata) && $rawmaterialrequestdata['estimatedate']!="0000-00-00"){ echo $this->general_model->displaydate($rawmaterialrequestdata['estimatedate']); } ?>" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 pl-sm pr-sm">
                                <div class="form-group" id="productbarcode_div">
                                    <div class="col-sm-9">
                                        <label for="productbarcode" class="control-label">Barcode or QR Code</label>
                                        <input id="productbarcode" class="form-control" name="productbarcode" onkeypress="return alphanumeric(event)" maxlength="30">
                                    </div>
                                    <div class="col-sm-3 text-right">
                                        <div class="form-group pt-xl">
                                            <div class="col-sm-12">
                                                <button type="button" name="sbmtBarcode" id="sbmtBarcode" class="btn btn-primary btn-raised" onclick="checkBarcode()">Submit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pl-sm pr-sm"><hr></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 pl-sm pr-sm">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                    <label class="control-label">Select Product</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3 pl-sm pr-sm">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Select Variant</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 pl-sm pr-sm">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Unit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-2 pl-sm pr-sm">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Qty</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if(!empty($rawmaterialrequestdata) && !empty($rawmaterialrequestproductdata)) { ?>
                            <input type="hidden" name="removerawmaterialrequestproductid" id="removerawmaterialrequestproductid">
                            <?php for ($i=0; $i < count($rawmaterialrequestproductdata); $i++) { ?>
                                <div class="row countproducts" id="countproducts<?=($i+1)?>">
                                    <input type="hidden" name="rawmaterialrequestproductid[]" value="<?=$rawmaterialrequestproductdata[$i]['id']?>" id="rawmaterialrequestproductid<?=($i+1)?>">
                                    <input type="hidden" name="uniqueproduct[]" id="uniqueproduct<?=($i+1)?>" value="<?=($rawmaterialrequestproductdata[$i]['priceid']."_".$rawmaterialrequestproductdata[$i]['unitid'])?>">
                                    <div class="col-sm-4 pl-sm pr-sm">
                                        <div class="form-group" id="product<?=($i+1)?>_div">
                                            <div class="col-sm-12">
                                                <select id="productid<?=($i+1)?>" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                    <option value="0">Select Product</option>
                                                    <?php
                                                    if(!empty($productdata)){
                                                        foreach($productdata as $product){ 
                                                            if($rawmaterialrequestproductdata[$i]['productid']==$product['id']){

                                                                $json = str_replace('"', "&quot;",json_encode($product['variantdata']));
                                                                $productname = str_replace("'","&apos;",$product['name']);
                                                                if(DROPDOWN_PRODUCT_LIST==0){ ?>
                                
                                                                    <option value="<?php echo $product['id']; ?>" data-variants="<?=$json?>" <?=($rawmaterialrequestproductdata[$i]['productid']==$product['id']?"selected":"")?>><?php echo $productname; ?></option>
                                
                                                                <?php }else{
                                
                                                                    if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                        $img = $product['image'];
                                                                    }else{
                                                                        $img = PRODUCTDEFAULTIMAGE;
                                                                    }
                                                                    ?>
                                    
                                                                    <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> "  value="<?php echo $product['id']; ?>" data-variants="<?=$json?>" <?=($rawmaterialrequestproductdata[$i]['productid']==$product['id']?"selected":"")?>><?php echo $productname; ?></option>
                                                                
                                                                <?php }
                                                            }
                                                        } 
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 pl-sm pr-sm">
                                        <div class="form-group" id="price<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <select id="priceid<?=($i+1)?>" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                    <option value="0">Select Variant</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 pl-sm pr-sm">
                                        <div class="form-group" id="unit<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <select id="unitid<?=($i+1)?>" name="unitid[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                    <option value="0">Select Unit</option>
                                                    <?=$UNIT_DATA?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 pl-sm pr-sm">
                                        <div class="form-group" id="qty<?=($i+1)?>_div">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$rawmaterialrequestproductdata[$i]['quantity']?>" onkeypress="return decimal_number_validation(event, this.value,8);" style="display: block;" div-id="<?=($i+1)?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 form-group m-n p-sm pt-md">	
                                        <?php if($i==0){?>
                                            <?php if(count($rawmaterialrequestproductdata)>1){ ?>
                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                            <?php }else { ?>
                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            <?php } ?>

                                        <? }else if($i!=0) { ?>
                                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(<?=($i+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                        <? } ?>
                                        <button type="button" class="btn btn-default btn-raised btn-sm remove_btn" onclick="removeproduct(<?=($i+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                    
                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
                                    </div>
                                    <script type="text/javascript">
                                        $(document).ready(function() {
                                            getproductprice(<?=$i+1?>);
                                            $("#priceid<?=$i+1?>").val(<?=$rawmaterialrequestproductdata[$i]['priceid']?>);
                                            $("#unitid<?=$i+1?>").val(<?=$rawmaterialrequestproductdata[$i]['unitid']?>);
                                            $("#priceid<?=$i+1?>,#unitid<?=$i+1?>").selectpicker('refresh');
                                        });
                                    </script>
                                </div>
                            <?php } ?>
                        <?php }else{ ?>
                            <?php if(!isset($rawmaterialrequestdata) && !empty($rawmaterialrequestproductdata)) { ?>
                                <?php for ($i=0; $i < count($rawmaterialrequestproductdata); $i++) { 
                                    if(!empty($rawmaterialrequestproductdata[$i]['requiredtostartproduction'])){ ?>
                                    <div class="row countproducts" id="countproducts<?=($i+1)?>">
                                        <input type="hidden" name="uniqueproduct[]" id="uniqueproduct<?=($i+1)?>" value="<?=($rawmaterialrequestproductdata[$i]['priceid']."_".$rawmaterialrequestproductdata[$i]['unitid'])?>">
                                        <div class="col-sm-4 pl-sm pr-sm">
                                            <div class="form-group" id="product<?=($i+1)?>_div">
                                                <div class="col-sm-12">
                                                    <select id="productid<?=($i+1)?>" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                        <option value="0">Select Product</option>
                                                        <?php
                                                        if(!empty($productdata)){
                                                            foreach($productdata as $product){ 
                                                                if($rawmaterialrequestproductdata[$i]['productid']==$product['id']){ 
                                                                    
                                                                    $json = str_replace('"', "&quot;",json_encode($product['variantdata']));
                                                                    $productname = str_replace("'","&apos;",$product['name']);
                                                                    
                                                                    if(DROPDOWN_PRODUCT_LIST==0){ ?>
                                    
                                                                        <option value="<?php echo $product['id']; ?>" data-variants="<?=$json?>" <?=($rawmaterialrequestproductdata[$i]['productid']==$product['id']?"selected":"")?>><?php echo $productname; ?></option>
                                    
                                                                    <?php }else{
                                    
                                                                        if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                                            $img = $product['image'];
                                                                        }else{
                                                                            $img = PRODUCTDEFAULTIMAGE;
                                                                        }
                                                                        ?>
                                        
                                                                        <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> "  value="<?php echo $product['id']; ?>" data-variants="<?=$json?>" <?=($rawmaterialrequestproductdata[$i]['productid']==$product['id']?"selected":"")?>><?php echo $productname; ?></option>
                                                                    
                                                                <?php }
                                                                } 
                                                            } 
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 pl-sm pr-sm">
                                            <div class="form-group" id="price<?=($i+1)?>_div">
                                                <div class="col-md-12">
                                                    <select id="priceid<?=($i+1)?>" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                        <option value="0">Select Variant</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 pl-sm pr-sm">
                                            <div class="form-group" id="unit<?=($i+1)?>_div">
                                                <div class="col-md-12">
                                                    <select id="unitid<?=($i+1)?>" name="unitid[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                        <option value="0">Select Unit</option>
                                                        <?=$UNIT_DATA?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 pl-sm pr-sm">
                                            <div class="form-group" id="qty<?=($i+1)?>_div">
                                                <div class="col-md-12">
                                                    <input type="text" class="form-control qty" id="qty<?=($i+1)?>" name="qty[]" value="<?=$rawmaterialrequestproductdata[$i]['requiredstock']?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="<?=($i+1)?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 form-group m-n p-sm pt-md">	
                                            <?php if($i==0){?>
                                                <?php if(count($rawmaterialrequestproductdata)>1){ ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                <?php }else { ?>
                                                    <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                <?php } ?>

                                            <? }else if($i!=0) { ?>
                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(<?=($i+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                            <? } ?>
                                            <button type="button" class="btn btn-default btn-raised btn-sm remove_btn" onclick="removeproduct(<?=($i+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                        
                                            <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
                                        </div>
                                        <script type="text/javascript">
                                            $(document).ready(function() {
                                                getproductprice(<?=$i+1?>);
                                                $("#priceid<?=$i+1?>").val(<?=$rawmaterialrequestproductdata[$i]['priceid']?>);
                                                $("#unitid<?=$i+1?>").val(<?=$rawmaterialrequestproductdata[$i]['unitid']?>);
                                                $("#priceid<?=$i+1?>,#unitid<?=$i+1?>").selectpicker('refresh');
                                            });
                                        </script>
                                    </div>
                                <?php } } ?>
                            <?php }else{ ?>
                                <div class="row countproducts" id="countproducts1">
                                    <input type="hidden" name="uniqueproduct[]" id="uniqueproduct1">
                                    <div class="col-sm-4 pl-sm pr-sm">
                                        <div class="form-group" id="product1_div">
                                            <div class="col-sm-12">
                                                <select id="productid1" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                    <option value="0">Select Product</option>
                                                    <?=$PRODUCT_DATA?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 pl-sm pr-sm">
                                        <div class="form-group" id="price1_div">
                                            <div class="col-md-12">
                                                <select id="priceid1" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                    <option value="0">Select Variant</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 pl-sm pr-sm">
                                        <div class="form-group" id="unit1_div">
                                            <div class="col-md-12">
                                                <select id="unitid1" name="unitid[]" class="selectpicker form-control unitid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                    <option value="0">Select Unit</option>
                                                    <?=$UNIT_DATA?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 pl-sm pr-sm">
                                        <div class="form-group" id="qty1_div">
                                            <div class="col-md-12">
                                                <input type="text" class="form-control qty" id="qty1" name="qty[]" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" style="display: block;" div-id="1">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 form-group m-n p-sm pt-md">	
                                        <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeproduct(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewproduct()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="row">
                            <div class="col-md-12 pl-sm pr-sm">
                                <div class="form-group" id="remarks_div">
                                    <div class="col-sm-12">
                                        <label for="remarks" class="control-label">Remarks</label>
                                        <textarea id="remarks" class="form-control" name="remarks"><?php if(isset($rawmaterialrequestdata)){ echo $rawmaterialrequestdata['remarks']; } ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="col-sm-12 text-center">
                                    <?php if(!empty($rawmaterialrequestdata)){ ?>
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