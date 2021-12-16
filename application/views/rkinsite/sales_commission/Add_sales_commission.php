<?php 
$PRODUCTDATA = $MEMBERDATA = "";
if(!empty($productdata)){
    foreach($productdata as $product){
        $productname = str_replace("'","&apos;",$product['name']);
        if(DROPDOWN_PRODUCT_LIST==0){
            $PRODUCTDATA .= '<option value="'.$product['id'].'">'.$productname.'</option>';
        }else{
            $content = "";
            if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
                $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> ';
            }else{
                $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> ';
            }
            $content .= $productname.'<small class=&quot;text-muted&quot;>'.$product['sku'].'</small>';
            $PRODUCTDATA .= '<option data-content="'.$content.'" value="'.$product['id'].'">'.$productname.'</option>';
        }
    }
}
if(!empty($memberdata)){
    foreach($memberdata as $member){
        $MEMBERDATA .= '<option value="'.$member['id'].'">'.$member['name'].'</option>';
    }
}
?>
<script>
    var EmployeeId = '<?php if(isset($salescommissiondata)){ echo $salescommissiondata['employeeid']; }else{ echo '0'; }?>';
    var CommissionType = '<?php if(isset($salescommissiondata)){ echo $salescommissiondata['commissiontype']; }else{ echo '0'; }?>';

    var productoptionhtml = '<?=$PRODUCTDATA?>';
    var memberoptionhtml = '<?=$MEMBERDATA?>';

</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($salescommissiondata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($salescommissiondata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12">
						<form class="form-horizontal" id="salescommissionform" name="salescommissionform">
							<input type="hidden" name="salescommissionid" value="<?php if(isset($salescommissiondata)){ echo $salescommissiondata['id']; } ?>">
                            <input type="hidden" name="oldcommissiontype" value="<?php if(isset($salescommissiondata)){ echo $salescommissiondata['commissiontype']; } ?>">
                            <div class="col-md-11 col-md-offset-1">
                                <div class="form-group" id="employee_div">
                                    <label for="employeeid" class="col-sm-4 control-label">Select Employee <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-4">
                                        <select id="employeeid" name="employeeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">
                                            <option value="0">Select Employee</option>
                                            <?php foreach($employeedata as $emp){ ?>
                                                <option value="<?=$emp['id']?>" <?php if(isset($salescommissiondata)){ if($emp['id'] == $salescommissiondata['employeeid']){ echo 'selected'; } } ?>><?=ucwords($emp['name'])?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-11 col-md-offset-1">
                                <div class="form-group" id="commissiontype_div">
                                    <label for="commissiontype" class="col-sm-4 control-label">Commission Type <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-4">
                                        <select id="commissiontype" name="commissiontype" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">
                                            <option value="0">Select Commission Type</option>
                                            <?php foreach($this->Commissiontype as $key=>$type){ ?>
                                                <option value="<?=$key?>" <?php if(isset($salescommissiondata)){ if($key == $salescommissiondata['commissiontype']){ echo 'selected'; } } ?>><?=$type?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="commissiondiv">
                                                
                            <?php if(isset($salescommissiondata) && !empty($salescommissiondata['salescommissiondetail'])){ 
                                $commisiondata = $salescommissiondata['salescommissiondetail']; ?>
                                <input type="hidden" value="<?php echo implode(",",array_column($commisiondata,'id')); ?>" name="olddetailid">
                                <?php
                                if($salescommissiondata['commissiontype'] == 1){ ?>
                                    <div class="col-md-11 col-md-offset-1">
                                        <input type="hidden" name="salescommissiondetailid" value="<?=$commisiondata[0]['id']?>">
                                        <div class="form-group" id="flatcommission_div">
                                            <label for="flatcommission" class="col-sm-4 control-label">Commission (%) <span class="mandatoryfield">*</span></label>
                                            <div class="col-sm-4">
                                                <input id="flatcommission" type="text" name="flatcommission" value="<?=$commisiondata[0]['commission']?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)" onkeyup="onlypercentage('flatcommission')">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="focusedinput" class="col-sm-4 control-label">GST</label>
                                            <div class="col-sm-4">
                                                <div class="col-sm-5 col-xs-6 pl-n">
                                                    <div class="radio">
                                                        <input type="radio" name="flatcommissiongst" id="flatcommissionwithgst" value="1" <?=$commisiondata[0]['gst']==1?'checked':''?>>
                                                        <label for="flatcommissionwithgst">With GST</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-xs-6 pl-n">
                                                    <div class="radio">
                                                        <input type="radio" name="flatcommissiongst" id="flatcommissionwithoutgst" value="0" <?=$commisiondata[0]['gst']==0?'checked':''?>>
                                                        <label for="flatcommissionwithoutgst">Without GST</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }else if($salescommissiondata['commissiontype'] == 2){ 
                                    for($i=0;$i<count($commisiondata);$i++){ 
                                        if($i==0){ ?>
                                            <div class="col-sm-12"><hr></div>
                                            <div class="col-sm-12">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                            <label class="control-label">Select Product <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                            <label class="control-label">Comm. (%) <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label class="control-label">GST</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-12 countproducts" id="countproducts<?=($i+1)?>">
                                            <input type="hidden" name="salescommissiondetailid[]" value="<?=$commisiondata[$i]['id']?>">
                                            <input type="hidden" name="salescommissionmappingid[]" value="<?=$commisiondata[$i]['mappingid']?>">
                                            <div class="col-sm-4">
                                                <div class="form-group" id="product<?=($i+1)?>_div">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <select id="productid<?=($i+1)?>" name="productid[]" class="selectpicker form-control productid" data-live-search="true" data-select-on-tab="true" data-size="8" data-id="<?=($i+1)?>">
                                                            <option value="0">Select Product</option>
                                                            <?php echo $PRODUCTDATA; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group" id="productcommission<?=($i+1)?>_div">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <input id="productcommission<?=($i+1)?>" type="text" name="productcommission[]" value="<?=$commisiondata[$i]['commission']?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)" onkeyup="onlypercentage('productcommission<?=($i+1)?>')">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group mt-sm">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <input type="hidden" id="gst<?=($i+1)?>" name="productgst[]" value="<?=$commisiondata[$i]['gst']?>">
                                                        <div class="col-sm-6 col-xs-8 pl-xs">
                                                            <div class="radio">
                                                                <input type="radio" class="checkGST" name="productgst<?=($i+1)?>" id="productwithgst<?=($i+1)?>" value="1" <?=$commisiondata[$i]['gst']==1?'checked':''?>>
                                                                <label for="productwithgst<?=($i+1)?>">With GST</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-xs-8 pl-n">
                                                            <div class="radio">
                                                                <input type="radio" class="checkGST" name="productgst<?=($i+1)?>" id="productwithoutgst<?=($i+1)?>" value="0" <?=$commisiondata[$i]['gst']==0?'checked':''?>>
                                                                <label for="productwithoutgst<?=($i+1)?>">Without GST</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 mt-sm">
                                                <?php if($i==0){?>
                                                    <?php if(count($commisiondata)>1){ ?>
                                                        <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('countproducts1',1)" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>
                                                <?php }else if($i!=0) { ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('countproducts<?=($i+1)?>',<?=($i+1)?>)" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-minus"></i></button>
                                                <?php } ?>
                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('countproducts<?=($i+1)?>',<?=($i+1)?>)" style="padding: 5px 10px;margin-top: 0px;display:none;"><i class="fa fa-minus"></i></button>
                                            
                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-plus"></i></button>  
                                            </div>
                                            <script>
                                                $(document).ready(function(){
                                                    $("#productid<?=($i+1)?>").val(<?=$commisiondata[$i]['referenceid']?>).selectpicker('refresh');
                                                });
                                            </script>
                                        </div>
                                        <?php
                                    }   
                                }else if($salescommissiondata['commissiontype'] == 3){ 
                                    for($i=0;$i<count($commisiondata);$i++){ 
                                        if($i==0){ ?>
                                            <div class="col-sm-12"><hr></div>
                                            <div class="col-sm-12">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                            <label class="control-label">Select <?=Member_label?> <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                            <label class="control-label">Comm. (%) <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label class="control-label">GST</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-12 countmembers" id="countmembers<?=($i+1)?>">
                                            <input type="hidden" name="salescommissiondetailid[]" value="<?=$commisiondata[$i]['id']?>">
                                            <input type="hidden" name="salescommissionmappingid[]" value="<?=$commisiondata[$i]['mappingid']?>">
                                            <div class="col-sm-4">
                                                <div class="form-group" id="member<?=($i+1)?>_div">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <select id="memberid<?=($i+1)?>" name="memberid[]" class="selectpicker form-control memberid" data-live-search="true" data-select-on-tab="true" data-size="8" data-id="<?=($i+1)?>">
                                                            <option value="0">Select <?=Member_label?></option>
                                                            <?php echo $MEMBERDATA; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group" id="membercommission<?=($i+1)?>_div">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <input id="membercommission<?=($i+1)?>" type="text" name="membercommission[]" value="<?=$commisiondata[$i]['commission']?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)" onkeyup="onlypercentage('membercommission<?=($i+1)?>')">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group mt-sm">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <input type="hidden" id="gst<?=($i+1)?>" name="membergst[]" value="<?=$commisiondata[$i]['gst']?>">
                                                        <div class="col-sm-6 col-xs-8 pl-xs">
                                                            <div class="radio">
                                                                <input type="radio" class="checkGST" name="memberbasegst<?=($i+1)?>" id="memberbasewithgst<?=($i+1)?>" value="1" <?=$commisiondata[$i]['gst']==1?'checked':''?>>
                                                                <label for="memberbasewithgst<?=($i+1)?>">With GST</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-xs-8 pl-n">
                                                            <div class="radio">
                                                                <input type="radio" class="checkGST" name="memberbasegst<?=($i+1)?>" id="memberbasewithoutgst<?=($i+1)?>" value="0" <?=$commisiondata[$i]['gst']==0?'checked':''?>>
                                                                <label for="memberbasewithoutgst<?=($i+1)?>">Without GST</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 mt-sm">
                                                <?php if($i==0){?>
                                                    <?php if(count($commisiondata)>1){ ?>
                                                        <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('countmembers1',1)" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>
                                                <?php }else if($i!=0) { ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('countmembers<?=($i+1)?>',<?=($i+1)?>)" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-minus"></i></button>
                                                <?php } ?>
                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('countmembers<?=($i+1)?>',<?=($i+1)?>)" style="padding: 5px 10px;margin-top: 0px;display:none;"><i class="fa fa-minus"></i></button>
                                            
                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-plus"></i></button>  
                                            </div>
                                            <script>
                                                $(document).ready(function(){
                                                    $("#memberid<?=($i+1)?>").val(<?=$commisiondata[$i]['referenceid']?>).selectpicker('refresh');
                                                });
                                            </script>
                                        </div>
                                        <?php
                                    }  
                                }else if($salescommissiondata['commissiontype'] == 4){ 
                                    for($i=0;$i<count($commisiondata);$i++){ 
                                        if($i==0){ ?>
                                            <div class="col-sm-12"><hr></div>
                                            <div class="col-sm-12">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                            <label class="control-label">Range <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group">
                                                        <div class="col-sm-12 pl-sm pr-sm">
                                                            <label class="control-label">Comm. (%) <span class="mandatoryfield">*</span></label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-5">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label class="control-label">GST</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="col-sm-12 counttiered" id="counttiered<?=($i+1)?>">
                                            <input type="hidden" name="salescommissiondetailid[]" value="<?=$commisiondata[$i]['id']?>">
                                            <input type="hidden" name="salescommissionmappingid[]" value="<?=$commisiondata[$i]['mappingid']?>">
                                            <div class="col-sm-4">
                                                <div class="col-sm-12 pl-sm pr-sm">
                                                    <div class="col-md-6 pr-sm pl-n">
                                                        <div class="form-group pr-md" id="rangestart<?=($i+1)?>_div">
                                                            <input id="rangestart<?=($i+1)?>" type="text" name="rangestart[]" value="<?=$commisiondata[$i]['startrange']?>" class="form-control tiered" placeholder="Start" onkeypress="return isNumber(event)">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 pl-sm pr-n">
                                                        <div class="form-group pl-md" id="rangeend<?=($i+1)?>_div">
                                                            <input id="rangeend<?=($i+1)?>" type="text" name="rangeend[]" value="<?=$commisiondata[$i]['endrange']?>" class="form-control" placeholder="End" onkeypress="return isNumber(event)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group" id="tieredcommission<?=($i+1)?>_div">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <input id="tieredcommission<?=($i+1)?>" type="text" name="tieredcommission[]" value="<?=$commisiondata[$i]['commission']?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value,3,2)"onkeyup="onlypercentage('tieredcommission<?=($i+1)?>')">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group mt-sm">
                                                    <div class="col-sm-12 pl-sm pr-sm">
                                                        <input type="hidden" id="gst<?=($i+1)?>" name="tieredgst[]" value="<?=$commisiondata[$i]['gst']?>">
                                                        <div class="col-sm-6 col-xs-8 pl-xs">
                                                            <div class="radio">
                                                                <input type="radio" class="checkGST" name="tieredbasegst<?=($i+1)?>" id="tieredbasewithgst<?=($i+1)?>" value="1" <?=$commisiondata[$i]['gst']==1?'checked':''?>>
                                                                <label for="tieredbasewithgst<?=($i+1)?>">With GST</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-6 col-xs-8 pl-n">
                                                            <div class="radio">
                                                                <input type="radio" class="checkGST" name="tieredbasegst<?=($i+1)?>" id="tieredbasewithoutgst<?=($i+1)?>" value="0" <?=$commisiondata[$i]['gst']==0?'checked':''?>>
                                                                <label for="tieredbasewithoutgst<?=($i+1)?>">Without GST</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-2 mt-sm">
                                                <?php if($i==0){?>
                                                    <?php if(count($commisiondata)>1){ ?>
                                                        <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('counttiered1',1)" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>
                                                <?php }else if($i!=0) { ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('counttiered<?=($i+1)?>',<?=($i+1)?>)" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-minus"></i></button>
                                                <?php } ?>
                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removecommission('counttiered<?=($i+1)?>',<?=($i+1)?>)" style="padding: 5px 10px;margin-top: 0px;display:none;"><i class="fa fa-minus"></i></button>
                                            
                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addnewcommission()" style="padding: 5px 10px;margin-top: 0px;"><i class="fa fa-plus"></i></button>  
                                            </div>
                                        </div>
                                        <?php
                                    }  
                                }
                            } ?>

                            </div>

                            <div class="col-sm-12 p-n">
                                <div class="form-group text-center">
                                    <div class="col-sm-12">
                                        <div class="col-sm-12"><hr></div>
                                        <?php if(!empty($salescommissiondata)){ ?>
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