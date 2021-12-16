<?php
$PRODUCT_DATA = "";
if(!empty($productdata)){
  foreach($productdata as $product){ 
    $productname = str_replace("'","&apos;",$product['name']);
    if(DROPDOWN_PRODUCT_LIST==0){
        $PRODUCT_DATA .= '<option value="'.$product["id"].'">'.addslashes($productname).'</option>';
    }else{
        $content = "";
        if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
            $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
        }else{
            $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> '.addslashes($productname);
        }
        $PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'">'.addslashes($productname).'</option>';
    }
  } 
}
?>
<script>
    var PRODUCTDATA = '<?=$PRODUCT_DATA?>';
    var orderid = '<?php if(isset($productionplandata) && !empty($productionplandata['orderid'])){ echo $productionplandata['orderid']; }else{ if(isset($orderid)){ echo $orderid; }else{ echo 0; } } ?>';
</script>
<style>
    .productvariantdiv {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($productionplandata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($productionplandata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
            <form class="form-horizontal" id="production-plan-form">
                <input type="hidden" name="productionplanid" id="productionplanid" value="<?php if(isset($productionplandata)){ echo $productionplandata['id']; } ?>">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-7">
                                   <div class="form-group" id="orderid_div">
                                        <label class="col-md-2 pl-n pr-n control-label" for="orderid">Select Order</label>
                                        <div class="col-md-6">
                                            <input type="hidden" name="postorderid" name="postorderid" value="<?php if(isset($productionplandata)){ echo $productionplandata['orderid']; } ?>">
                                            <?php if(!isset($productionplandata)){ ?>
                                            <select id="orderid" name="orderid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Order" multiple>
                                                <?php foreach($orderdata as $order){ ?>
                                                    <option value="<?php echo $order['id']; ?>" <?php if(isset($orderid) && $orderid == $order['id']){ echo 'selected'; } ?>><?php echo $order['ordernumber']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php }else{ ?>
                                                <select id="orderid" name="orderid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" <?php if(isset($productionplandata) && !empty($productionplandata['orderid'])){ echo "disabled"; } ?>>
                                                <option value="0">Select Order</option>
                                                <?php foreach($orderdata as $order){ ?>
                                                    <option value="<?php echo $order['id']; ?>" <?php if(isset($productionplandata) && $productionplandata['orderid'] == $order['id']){ echo "selected"; } ?>><?php echo $order['ordernumber']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12"><hr></div>
                                <div class="col-md-12" id="orderdetailpanel">
                                <?php if(!empty($productionplandata) && !empty($productionproductdata)) { ?>
                                    <div class="panel panel-transparent">
                                        <div class="panel-heading p-n">
                                            <h2 style="font-weight:600;">Product Details</h2>
                                        </div>
                                        <div class="panel-body productvariantdiv p-sm mb-n" id="orderdetaildata">
                                            <div class="col-md-12 p-n">
                                                <div class="col-md-4 pl-xs pr-xs">
                                                    <div class="form-group m-n p-n">
                                                        <label class="control-label"><b>Product Name</b></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-xs pr-xs">
                                                    <div class="form-group m-n p-n">
                                                        <label class="control-label"><b>Variant</b></label>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-xs pr-xs">
                                                    <div class="form-group m-n p-n">
                                                        <label class="control-label"><b>Quantity</b></label>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            <div id="productdata">
                                                <?php for ($i=0; $i < count($productionproductdata); $i++) { ?>
                                                    <div class="col-md-12 p-n countproducts" id="countproducts<?=($i+1)?>">
                                                        <input type="hidden" id="productionplandetailid<?=($i+1)?>" name="productionplandetailid[]" value="<?=$productionproductdata[$i]['id']?>">
                                                        <div class="col-md-4 pl-xs pr-xs">
                                                            <div class="form-group m-n p-n" id="productid<?=($i+1)?>_div">
                                                                <select id="productid<?=($i+1)?>" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="5" data-width = "100%">
                                                                    <option value="0">Select Product</option>
                                                                    <?=$PRODUCT_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3 pl-xs pr-xs">
                                                            <div class="form-group m-n p-n" id="priceid<?=($i+1)?>_div">
                                                                <select id="priceid<?=($i+1)?>" name="priceid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                    <option value="0">Select Variant</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pl-xs pr-xs">
                                                            <div class="form-group m-n p-n" id="quantity<?=($i+1)?>_div">
                                                                <input type="text" id="quantity<?=($i+1)?>" class="form-control quantity text-right" name="quantity[]" value="<?=(MANAGE_DECIMAL_QTY==1)?$productionproductdata[$i]['quantity']:(int)$productionproductdata[$i]['quantity'];?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pl-xs pr-xs pt-sm">
                                                            <div class="form-group m-n">
                                                                <?php if($i==0){?>
                                                                    <?php if(count($productionproductdata)>1){ ?>
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
                                                        </div>
                                                        <script type="text/javascript">
                                                            $(document).ready(function() {
                                                                $("#productid<?=$i+1?>").val(<?=$productionproductdata[$i]['productid']?>).selectpicker('refresh');
                                                                getproductvariant(<?=$i+1?>);
                                                                $("#priceid<?=$i+1?>").val(<?=$productionproductdata[$i]['priceid']?>).selectpicker('refresh');
                                                            });
                                                        </script>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-12 text-center">
                                                <hr>
                                                <a href="javascript:void(0)" class="btn btn-primary btn-raised" title="Calculate" onclick="calculateorderquantity()">Calculate</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div> 
                                <div class="col-md-12" id="rawmaterialpanel"></div> 
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="col-sm-12 text-center">
                                            <?php if(!empty($productionplandata)){ ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                            <?php }else{ ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                            <?php } ?>
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->