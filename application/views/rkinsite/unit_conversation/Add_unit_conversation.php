<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($unitconversationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($unitconversationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <form class="form-horizontal" id="unit-conversation-form">
                                <div class="col-sm-12 col-md-10 col-lg-10 col-lg-offset-1 col-md-offset-1">
                                    <input type="hidden" name="unitconversationid" value="<?php if(isset($unitconversationdata)){ echo $unitconversationdata['id']; } ?>">
                                    <div class="form-group" id="product_div">
                                        <label for="productid" class="col-md-4 control-label">Select Product</label>
                                        <div class="col-sm-5">
                                            <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Product</option>
                                                <?php if(isset($productdata)){ foreach($productdata as $product){ 

                                                    $productname = str_replace("'","&apos;",$product['name']);
                                                    if(DROPDOWN_PRODUCT_LIST==0){ ?>

                                                        <option value="<?php echo $product['id']; ?>" <?php if(isset($unitconversationdata) && $unitconversationdata['productid']==$product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>

                                                    <?php }else{

                                                        if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                                            $img = $product['image'];
                                                        }else{
                                                            $img = PRODUCTDEFAULTIMAGE;
                                                        }
                                                        ?>

                                                        <option data-content="<?php if(!empty($product['image'])){?><img src='<?=PRODUCT.$img?>' style='width:40px'> <?php } echo $productname; ?> " value="<?php echo $product['id']; ?>" <?php if(isset($unitconversationdata) && $unitconversationdata['productid']==$product['id']){ echo "selected"; } ?>><?php echo $productname; ?></option>
                                                    
                                                    <?php } ?>
                                                    
                                                    <?php }} ?>
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group" id="inputunitid_div">
                                        <label for="inputunitid" class="col-md-4 control-label">Input Unit <span class="mandatoryfield">*</span></label>
                                        <div class="col-sm-5">
                                            <select id="inputunitid" name="inputunitid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Input Unit</option>
                                                <?php if(isset($unitdata)){ foreach($unitdata as $unit){ ?>
                                                <option value="<?php echo $unit['id']; ?>" <?php if(isset($unitconversationdata) && $unitconversationdata['inputunitid']==$unit['id']){ echo "selected"; } ?>><?php echo $unit['name']; ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" id="inputunitvalue_div">
                                        <label for="inputunitvalue" class="col-md-4 control-label">Input Unit Value <span class="mandatoryfield"></span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="inputunitvalue" name="inputunitvalue" class="form-control" value="<?php if(isset($unitconversationdata)){ echo number_format($unitconversationdata['inputunitvalue'],'2','.',''); }else{ echo 1; } ?>" onkeypress="return decimal_number_validation(event, this.value)">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group" id="outputunitid_div">
                                        <label for="outputunitid" class="col-md-4 control-label">Output Unit <span class="mandatoryfield">*</span></label>
                                        <div class="col-sm-5">
                                            <select id="outputunitid" name="outputunitid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                <option value="0">Select Output Unit</option>
                                                <?php if(isset($unitdata)){ foreach($unitdata as $unit){ ?>
                                                <option value="<?php echo $unit['id']; ?>" <?php if(isset($unitconversationdata) && $unitconversationdata['outputunitid']==$unit['id']){ echo "selected"; } ?>><?php echo $unit['name']; ?></option>
                                                <?php }} ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group" id="outputunitvalue_div">
                                        <label for="outputunitvalue" class="col-md-4 control-label">Output Unit Value <span class="mandatoryfield">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="text" id="outputunitvalue" name="outputunitvalue" class="form-control" value="<?php if(isset($unitconversationdata)){ echo number_format($unitconversationdata['outputunitvalue'],'2','.',''); } ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                                        <div class="col-md-6">
                                            <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="status" id="yes" value="1" <?php if(isset($narrationdata) && $narrationdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="yes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="status" id="no" value="0" <?php if(isset($narrationdata) && $narrationdata['status']==0){ echo 'checked'; }?>>
                                                <label for="no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-4 control-label"></label>
                                        <div class="col-md-8">
                                            <?php if(!empty($unitconversationdata)){ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php }else{ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php } ?>
                                            <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="col-sm-12">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->