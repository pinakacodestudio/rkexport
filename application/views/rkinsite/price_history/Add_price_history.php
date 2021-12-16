<script>

    var PRODUCT_PATH = '<?=PRODUCT?>';
    var memberids = '<?php if(isset($pricehistorydata) && !empty($pricehistorydata)){ echo $pricehistorydata['memberid']; }?>';
    var usertype = '<?php if(isset($pricehistorydata) && !empty($pricehistorydata)){ echo $pricehistorydata['type']; }?>';

    var categoryids = '<?php if(isset($pricehistorydata) && !empty($pricehistorydata)){ echo $pricehistorydata['categoryid']; }?>';
    var productids = '<?php if(isset($pricehistorydata) && !empty($pricehistorydata)){ echo $pricehistorydata['productid']; }?>';
    var displaytype = '<?php if(isset($displaytype) && $displaytype=='view'){ echo 'view'; }?>';

    var CategoryHTML = '';
    <?php foreach($categorydata as $category){ ?>
        CategoryHTML += '<option value="<?php echo $category['id']; ?>"><?php echo ucwords($category['name']); ?></option>';
    <?php } ?>

    var countryid = '<?=DEFAULT_COUNTRY_ID?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($displaytype) && $displaytype=='view'){ echo 'View'; }elseif(isset($pricehistorydata)){ echo 'Edit'; }else { echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($pricehistorydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
            <div class="col-md-12">
                <form class="form-horizontal" id="pricehistoryform" name="pricehistoryform">

                    <input type="hidden" name="pricehistoryid" id="pricehistoryid" value="<?php if(isset($pricehistorydata)){ echo $pricehistorydata['id']; } ?>"> 
                    <input type="hidden" name="oldproductpricehistoryid" id="oldproductpricehistoryid" value="<?php if(isset($pricehistorydata)){ echo $pricehistorydata['productpricehistoryid']; } ?>"> 
                    <div class="panel panel-default border-panel">
                        <div class="panel-body pt-n">
                            <div class="col-md-12 p-n">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group" id="type_div">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="type" class="control-label">Select Type <span class="mandatoryfield">*</span></label>
                                                <select id="type" name="type" class="selectpicker form-control" data-live-search="true" <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>>
                                                    <option value="0" <?php if(isset($pricehistorydata) && $pricehistorydata['type']=="0"){ echo "selected"; } ?>>Admin Product</option>
                                                    <option value="1" <?php if(isset($pricehistorydata) && $pricehistorydata['type']=="1"){ echo "selected"; } ?>><?=Member_label?> Product</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 memberproducttype" style="<?php if(isset($pricehistorydata) && $pricehistorydata['type']=="1"){ echo "display:block;"; }else{ echo "display:none;"; } ?>">
                                        <div class="form-group" id="channel_div">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="channelid" class="control-label">Select Channel <span class="mandatoryfield">*</span></label>
                                                <select id="channelid" name="channelid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>>
                                                    <option value="0">Select Channel</option>
                                                    <?php foreach($channeldata as $channel){ ?>      
                                                    <option value="<?php echo $channel['id']; ?>" <?=(isset($pricehistorydata) && $pricehistorydata['channelid']==$channel['id'])?"selected":"";?>><?php echo ucwords($channel['name']); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 memberproducttype" style="<?php if(isset($pricehistorydata) && $pricehistorydata['type']=="1"){ echo "display:block;"; }else{ echo "display:none;"; } ?>">
                                        <div class="form-group" id="member_div">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="memberid" class="control-label">Select <?=Member_label?> <span class="mandatoryfield">*</span></label>
                                                <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select <?=Member_label?>" select-on-tabs="true" multiple <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group" id="category_div">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="categoryid" class="control-label">Select Category <span class="mandatoryfield">*</span></label>
                                                <select id="categoryid" name="categoryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Category" multiple <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>>
                                                    <?php foreach($categorydata as $category){ 
                                                        $selected = "";
                                                        if(isset($pricehistorydata) && !empty($pricehistorydata)){    
                                                            $categoryids = explode(",", $pricehistorydata['categoryid']);  
                                                            if(in_array($category['id'], $categoryids)){
                                                                $selected = "selected";
                                                            }
                                                        }
                                                    ?>      
                                                    <option value="<?php echo $category['id']; ?>" <?=$selected?>><?php echo ucwords($category['name']); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" id="product_div">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="productid" class="control-label">Select Product <span class="mandatoryfield">*</span></label>
                                                <select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Product" multiple <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 memberproducttype" style="<?php if(isset($pricehistorydata) && $pricehistorydata['type']=="1"){ echo "display:block;"; }else{ echo "display:none;"; } ?>">
                                        <div class="form-group" id="province_div">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="provinceid" class="control-label">Select Province</label>
                                                <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>>
                                                    <option value="0">Select Province</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 memberproducttype" style="<?php if(isset($pricehistorydata) && $pricehistorydata['type']=="1"){ echo "display:block;"; }else{ echo "display:none;"; } ?>">
                                        <div class="form-group" id="city_div">
                                            <div class="col-sm-12 pl-sm pr-sm">
                                                <label for="cityid" class="control-label">Select City</label>
                                                <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>>
                                                    <option value="0">Select City</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pt-xl">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label class="control-label"></label>
                                                <a class="<?=generatebtn_class;?>" href="javascript:void(0);" onclick="getproductpricehistory()" title=<?=generatebtn_title?> <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>><?=generatebtn_text?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row" id="pricehistorytablediv" style="overflow-x:auto;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default border-panel">
                        <div class="panel-body">
                            <div class="col-md-12 p-n">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group" id="remarks_div">
                                            <div class="col-sm-12">
                                                <label for="remarks" class="control-label">Remarks</label>
                                                <textarea name="remarks" id="remarks" class="form-control" <?php if(isset($displaytype) && $displaytype=='view'){ echo "readonly"; } ?>><?php if(isset($pricehistorydata)){ echo $pricehistorydata['remarks']; } ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group" id="scheduleddate_div">
                                            <div class="col-sm-12">
                                                <label for="scheduleddate" class="control-label">Scheduled Date</label>
                                                <div style="position: relative;">
                                                    <input type="text" name="scheduleddate" value="<?php if(isset($pricehistorydata) && $pricehistorydata['scheduleddate']!="0000-00-00 00:00:00"){ echo date("d/m/Y h A", strtotime($pricehistorydata['scheduleddate'])); } ?>" id="scheduleddate" class="form-control" <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; }else{ echo "readonly"; } ?> >

                                                    <span class="btn btn-default add-on" style="position: absolute;top: 0;right: 0;" title='Clear' <?php if(isset($displaytype) && $displaytype=='view'){ echo "disabled"; } ?>><i class="fa fa-remove"></i></span>
                                                </div>
                                            </div>
                                           
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php if(!isset($displaytype)){ ?>
                                            <div class="form-group text-center">
                                                <?php if(!empty($pricehistorydata)){ ?>
                                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                <?php }else{ ?>
                                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                <?php } ?>
                                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>price-history" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                            </div>
                                        <?php }else{ ?>
                                            <div class="form-group text-right m-n">
                                                <a class="<?=back_class;?>" href="<?=ADMIN_URL?>price-history" title=<?=back_title?>><?=back_text?></a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->