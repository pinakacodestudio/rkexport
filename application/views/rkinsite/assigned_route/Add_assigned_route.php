<?php 
$PRODUCT_DATA = '';
if(!empty($productdata)){
    foreach($productdata as $product){
        
        $productname = str_replace("'","&apos;",$product['name']);
        if(DROPDOWN_PRODUCT_LIST==0){
            $PRODUCT_DATA .= '<option value="'.$product['id'].'">'.$productname.'</option>';
        }else{
            $content = "";
            if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
                $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> '.$productname;
            }else{
                $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> '.$productname;
            }
            
            $PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'">'.$productname.'</option>';
        }
    } 
}
?>
<script>
    var PRODUCT_DATA = '<?=$PRODUCT_DATA?>';
    var EDITTAXRATE_SYSTEM = '<?=EDITTAXRATE?>';
    var DEFAULTCOUNTRYID = '<?=DEFAULT_COUNTRY_ID?>';
    var provinceid = '<?php if(isset($assignedroutedata)){ echo $assignedroutedata['provinceid']; }else{ echo '0'; } ?>';
    var cityid = '<?php if(isset($assignedroutedata)){ echo $assignedroutedata['cityid']; }else{ echo '0'; } ?>';
    var vehicleid = '<?php if(isset($assignedroutedata)){ echo $assignedroutedata['vehicleid']; }else{ echo '0'; } ?>';
    var capacity = '<?php if(isset($assignedroutedata)){ echo $assignedroutedata['capacity']; } ?>';
    var routeid = '<?php if(isset($assignedroutedata)){ echo $assignedroutedata['routeid']; }else{ echo '0'; } ?>';
    var memberids = '<?php if(isset($assignedroutedata)){ echo $assignedroutedata['memberid']; }else{ echo ''; } ?>';
    var invoiceids = '<?php if(isset($assignedroutedata)){ echo $assignedroutedata['invoiceid']; }else{ echo ''; } ?>';
</script>
<style>
    .invoiceproductdiv {
        box-shadow: 0px 1px 6px #333 !important;
        margin-bottom: 20px;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($assignedroutedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($assignedroutedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body pt-xs">
                    <form class="form-horizontal" id="assignedrouteform" name="assignedrouteform" method="post">
                        <input type="hidden" name="id" value="<?php if(isset($assignedroutedata)){ echo $assignedroutedata['id']; } ?>">
                        <div class="raw">
                            <div class="col-md-12 p-n">
                                <div class="col-md-4">
                                    <div class="form-group" id="employee_div">
                                        <div class="col-md-12 pr-sm pl-n">
                                            <label class="control-label" for="employeeid">Select Employee <span class="mandatoryfield">*</span></label>
                                            <select id="employeeid" name="employeeid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select Employee</option>
                                                <?php if(!empty($employeedata)){
                                                    foreach($employeedata as $employee){ ?>
                                                        <option value="<?=$employee['id']?>" <?php if(isset($assignedroutedata) && $assignedroutedata['employeeid']==$employee['id']){ echo "selected"; } ?>><?=ucwords($employee['name'])?></option>
                                                <?php } 
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="province_div">
                                        <div class="col-md-12 pr-sm pl-sm">
                                            <label class="control-label" for="provinceid">Select Province</label>
                                            <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select Province</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="city_div">
                                        <div class="col-md-12 pr-n pl-sm">
                                            <label class="control-label pr-n pl-n" for="cityid">Select City</label>
                                            <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select City</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 p-n">
                                <div class="col-md-3">
                                    <div class="form-group" id="vehicle_div">
                                        <div class="col-md-12 pr-sm pl-n">
                                            <label class="control-label" for="vehicleid">Select Vehicle <!-- <span class="mandatoryfield">*</span> --></label>
                                            <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select Vehicle</option>
                                                <?php foreach($vehicledata as $vehicle){ ?>
                                                    <option value="<?=$vehicle['id']?>"><?=$vehicle['vehiclename']." (".$vehicle['vehicleno'].")"?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" id="capacity_div">
                                        <div class="col-md-12 pr-sm pl-sm">
                                            <label class="control-label">Capacity</label>
                                            <input id="capacity" name="capacity" class="form-control" value="" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group" id="startdate_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="startdate" class="control-label">Start Date</label>
                                            <input id="startdate" type="text" name="startdate" value="<?php if(!empty($assignedroutedata) && $assignedroutedata['startdate']!="0000-00-00"){ echo $this->general_model->displaydate($assignedroutedata['startdate']); }else{
                                                echo $this->general_model->displaydate($this->general_model->getCurrentDate());} ?>" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group" id="time_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="time" class="control-label">Time</label>
                                            <input type="text" id="time" value="<?php if(!empty($assignedroutedata) && $assignedroutedata['time']!="00:00:00"){ echo $assignedroutedata['time']; } ?>" name="time" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group text-right" id="totalweight_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="totalweight" class="control-label">Total Weight</label>
                                            <input type="text" id="totalweight" value="<?php if(isset($assignedroutedata)){ echo number_format($assignedroutedata['totalweight'],2,'.',''); } ?>" name="totalweight" class="form-control text-right" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group text-right" id="loosmoney_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label for="loosmoney" class="control-label">Loos Money</label>
                                            <input type="text" id="loosmoney" value="<?php if(isset($assignedroutedata)){ echo number_format($assignedroutedata['loosmoney'],2,'.',''); } ?>" name="loosmoney" class="form-control text-right" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 p-n"><hr></div>

                            <div class="col-md-12 p-n">
                                <div class="col-md-3">
                                    <div class="form-group" id="route_div">
                                        <div class="col-md-12 pr-sm pl-n">
                                            <label class="control-label" for="routeid">Select Route <span class="mandatoryfield">*</span></label>
                                            <select id="routeid" name="routeid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select Route</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="member_div">
                                        <div class="col-md-12 pr-sm pl-sm">
                                            <label class="control-label" for="memberid">Select <?=Member_label?> <span class="mandatoryfield">*</span></label>
                                            <select id="memberid" name="memberid[]" class="selectpicker form-control" data-live-search="true" data-actions-box="true" data-size="8" title="Select <?=Member_label?>" multiple>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="invoice_div">
                                        <div class="col-md-12 pr-sm pl-sm">
                                            <label class="control-label" for="invoiceid">Select Invoice</label>
                                            <select id="invoiceid" name="invoiceid[]" class="selectpicker form-control" data-live-search="true" data-actions-box="true" data-size="8" title="Select Invoice" multiple>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-right pr-n pt-xl">
                                    <button type="button" class="btn btn-info btn-raised text-white">View Available Products</button>
                                </div>
                            </div>
                            
                            <div class="col-md-12 p-n" id="invoiceproductsection">
                            </div>
                           
                            <div class="col-md-12 p-n" id="extraproductsection">
                                <div class="panel panel-transparent">
                                    <div class="panel-heading p-n">
                                        <h2 style="font-weight:600;">Extra Products</h2>
                                    </div>
                                    <div class="panel-body invoiceproductdiv p-sm mb-n">
                                        <div class="col-md-12 p-n">
                                            <div class="col-sm-3 pl-sm pr-sm">
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
                                            <div class="col-sm-1 pl-sm pr-sm">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <label class="control-label">Quantity</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1 pl-sm pr-sm">
                                                <div class="form-group text-right">
                                                    <div class="col-sm-12">
                                                        <label class="control-label">Price (<?=CURRENCY_CODE?>)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1 pl-sm pr-sm">
                                                <div class="form-group text-right">
                                                    <div class="col-sm-12">
                                                        <label class="control-label">Tax (%)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 pl-sm pr-sm">
                                                <div class="form-group text-right">
                                                    <div class="col-sm-12">
                                                        <label class="control-label">Total Price (<?=CURRENCY_CODE?>)</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(!empty($assignedroutedata) && !empty($extraproductdata)) { ?>
                                            <?php for ($i=0; $i < count($extraproductdata); $i++) { ?>
                                                <div class="col-md-12 p-n countproducts" id="countproducts<?=($i+1)?>">
                                                    <input type="hidden" name="extraproductid[]" value="<?=$extraproductdata[$i]['id']?>" id="extraproductid<?=($i+1)?>">
                                                    <input type="hidden" name="uniqueproducts[]" id="uniqueproducts<?=($i+1)?>" value="<?=($extraproductdata[$i]['productid']."_".$extraproductdata[$i]['priceid'])?>">
                                                    <div class="col-sm-3 pl-sm pr-sm">
                                                        <div class="form-group" id="product<?=($i+1)?>_div">
                                                            <div class="col-sm-12">
                                                                <select id="productid<?=($i+1)?>" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                                    <option value="0">Select Product</option>
                                                                    <?=$PRODUCT_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 pl-sm pr-sm">
                                                        <div class="form-group" id="priceid<?=($i+1)?>_div">
                                                            <div class="col-md-12">
                                                                <select id="priceid<?=($i+1)?>" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                                    <option value="0">Select Variant</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 pl-sm pr-sm">
                                                        <div class="form-group" id="qty<?=($i+1)?>_div">
                                                            <div class="col-md-12">
                                                                <input type="text" id="qty<?=($i+1)?>" name="qty[]" value="<?=$extraproductdata[$i]['quantity']?>" class="form-control qty" div-id="<?=($i+1)?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 pl-sm pr-sm">
                                                        <div class="form-group" id="price<?=($i+1)?>_div">
                                                            <div class="col-md-12">
                                                                <input type="text" id="price<?=($i+1)?>" name="price[]" value="<?=$extraproductdata[$i]['price']?>" class="form-control price text-right" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-1 pl-sm pr-sm">
                                                        <div class="form-group" id="tax<?=($i+1)?>_div">
                                                            <div class="col-md-12">
                                                                <input type="text" id="tax<?=($i+1)?>" name="tax[]" value="" class="form-control tax text-right" div-id="<?=($i+1)?>" onkeypress="return decimal_number_validation(event, this.value, 8)" <?php if(EDITTAXRATE==0){ echo "readonly"; } ?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2 pl-sm pr-sm">
                                                        <div class="form-group" id="totalprice<?=($i+1)?>_div">
                                                            <div class="col-md-12">
                                                                <input type="text" id="totalprice<?=($i+1)?>" name="totalprice[]" value="" class="form-control totalprice text-right" div-id="<?=($i+1)?>" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 form-group m-n p-sm pt-md">	
                                                        <?php if($i==0){?>
                                                            <?php if(count($extraproductdata)>1){ ?>
                                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeProductRaw(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                            <?php }else { ?>
                                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewProductRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                            <?php } ?>

                                                        <?php }else if($i!=0) { ?>
                                                            <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeProductRaw(<?=($i+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                        <?php } ?>
                                                        <button type="button" class="btn btn-default btn-raised btn-sm remove_btn" onclick="removeProductRaw(<?=($i+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                    
                                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewProductRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
                                                    </div>
                                                    <script type="text/javascript">
                                                        $(document).ready(function() {
                                                            $("#productid<?=($i+1)?>").val(<?=$extraproductdata[$i]['productid']?>).selectpicker('refresh');
                                                            getproductprice(<?=($i+1)?>);
                                                            $("#priceid<?=($i+1)?>").val(<?=$extraproductdata[$i]['priceid']?>).selectpicker('refresh');
                                                            $("#tax<?=($i+1)?>").val(<?=$extraproductdata[$i]['tax']?>);

                                                            changeproductamount(<?=($i+1)?>);
                                                        });
                                                    </script>
                                                </div>
                                            <?php } ?>
                                        <?php }else{ ?>
                                            <div class="col-md-12 p-n countproducts" id="countproducts1">
                                                <input type="hidden" name="uniqueproducts[]" id="uniqueproducts1" value="0_0">
                                                <div class="col-sm-3 pl-sm pr-sm">
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
                                                    <div class="form-group" id="priceid1_div">
                                                        <div class="col-md-12">
                                                            <select id="priceid1" name="priceid[]" class="selectpicker form-control priceid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                                <option value="0">Select Variant</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 pl-sm pr-sm">
                                                    <div class="form-group" id="qty1_div">
                                                        <div class="col-md-12">
                                                            <input type="text" id="qty1" name="qty[]" value="" class="form-control qty" div-id="1" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 pl-sm pr-sm">
                                                    <div class="form-group" id="price1_div">
                                                        <div class="col-md-12">
                                                            <input type="text" id="price1" name="price[]" value="" class="form-control price text-right" div-id="1" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1 pl-sm pr-sm">
                                                    <div class="form-group" id="tax1_div">
                                                        <div class="col-md-12">
                                                            <input type="text" id="tax1" name="tax[]" value="" class="form-control tax text-right" div-id="1" onkeypress="return decimal_number_validation(event, this.value, 8)" <?php if(EDITTAXRATE==0){ echo "readonly"; } ?>>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 pl-sm pr-sm">
                                                    <div class="form-group" id="totalprice1_div">
                                                        <div class="col-md-12">
                                                            <input type="text" id="totalprice1" name="totalprice[]" value="" class="form-control totalprice text-right" div-id="1" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 form-group m-n p-sm pt-md">	
                                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeProductRaw(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewProductRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-md-12 p-n">
                                            <div class="col-md-3 col-md-offset-9 pl-sm pr-sm">
                                                <div class="form-group">
                                                    <div class="col-sm-12">
                                                        <label class="control-label"><b>Total Price  (<?=CURRENCY_CODE?>) : <span id="displaytotalprice">0.00</span></b></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 p-n"><hr></div>
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <div class="col-sm-12">
                                    <?php if(!empty($assignedroutedata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                    <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                    <?php } ?>
                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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