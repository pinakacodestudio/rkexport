 <!-- <script>
    var PRODUCT_PATH='<?=PRODUCT?>'; 
</script>  -->
<?php 
	$PURCHASE_PRODUCT_DATA = '';
	if(!empty($purchaseproductdata)){
        foreach($purchaseproductdata as $product){
            $productname = str_replace("'","&apos;",$product['name']);
            if(DROPDOWN_PRODUCT_LIST==0){
                $PURCHASE_PRODUCT_DATA .= '<option value="'.$product['id'].'">'.$productname.'</option>';
            }else{
                $content = "";
                if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
                    $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> ';
                }else{
                    $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> ';
                }
                $content .= ucwords($productname).'<small class=&quot;text-muted&quot;>'.$product['sku'].'</small>';
                $PURCHASE_PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'">'.$productname.'</option>';
            }
        }
    }
    $OFFER_PRODUCT_DATA = '';
	if(!empty($offerproductdata)){
        foreach($offerproductdata as $product){
            $productname = str_replace("'","&apos;",$product['name']);
            if(DROPDOWN_PRODUCT_LIST==0){
                $OFFER_PRODUCT_DATA .= '<option value="'.$product['id'].'">'.$productname.'</option>';
            }else{
                $content = "";
                if(!empty($product['image']) && file_exists(PRODUCT_PATH.$product['image'])){
                    $content .= '<img src=&quot;'.PRODUCT.$product['image'].'&quot; style=&quot;width:40px;&quot;> ';
                }else{
                    $content .= '<img src=&quot;'.PRODUCT.PRODUCTDEFAULTIMAGE.'&quot; style=&quot;width:40px;&quot;> ';
                }
                $content .= ucwords($productname).'<small class=&quot;text-muted&quot;>'.$product['sku'].'</small>';
                $OFFER_PRODUCT_DATA .= '<option data-content="'.$content.'" value="'.$product['id'].'">'.$productname.'</option>';
            }
        }
    }
    $BRAND_DATA = "";
    foreach($branddata as $row){ 
		$BRAND_DATA .= '<option value="'.$row["id"].'" '.((!empty($offerdata) && $offerdata['brandid']==$row["id"])?"selected":"").'>'.$row["name"].'</option>';
    }
    ?>

<script>
  var memberidarr = '<?php if(isset($offerdata)){ echo $offerdata['memberid']; } ?>';
  var DEFAULT_IMAGE_PREVIEW = '<?=DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW?>';
    var PURCHASE_PRODUCT_DATA = '<?=$PURCHASE_PRODUCT_DATA?>';
    var OFFER_PRODUCT_DATA = '<?=$OFFER_PRODUCT_DATA?>';
    var BRAND_DATA = '<?=$BRAND_DATA?>';
    var brandid = '<?php if(isset($offerdata)){ echo $offerdata['brandid']; } ?>';
</script>
<style>
    .purchaseproductid .dropdown-menu.open,.offerproductid .dropdown-menu.open{
        right: unset;
    }
    .purchaseproductid .dropdown-menu.inner,.offerproductid .dropdown-menu.inner{
        width: max-content;
        max-width: 300px;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($offerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($offerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" id="offerform" enctype="multipart/form-data">
                        <div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Offer Details</h2>
									</div>
									<div class="panel-body pt-n">
                                        <div class="col-sm-12 p-n">
                                            <div class="col-sm-6 p-n">
                                                <input type="hidden" name="offerid" id="offerid" value="<?php if(isset($offerdata)){ echo $offerdata['id']; } ?>">    
                                                <input type="hidden" name="oldtype" id="oldtype" value="<?php if(isset($offerdata)){ echo $offerdata['type']; } ?>">
                                                <div class="form-group" id="offername_div">
                                                    <label class="col-md-3 control-label" for="offername">Offer Name <span class="mandatoryfield"> * </span></label>
                                                    <div class="col-md-8">
                                                    <input type="text" id="offername" class="form-control" name="offername" value="<?php if(isset($offerdata)){ echo $offerdata['name']; } ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="offerdate_div">
                                                    <label class="col-md-3 control-label">Offer Date</label>
                                                    <div class="col-md-8">
                                                        <div class="input-daterange input-group" id="datepicker-range">
                                                            <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(isset($offerdata) && $offerdata['startdate']!="0000-00-00"){ echo $this->general_model->displaydate($offerdata['startdate']); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                            <span class="input-group-addon">to</span>
                                                            <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(isset($offerdata) && $offerdata['enddate']!="0000-00-00"){ echo $this->general_model->displaydate($offerdata['enddate']); } ?>" placeholder="End Date" title="End Date" readonly/>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <button type="button" id="cleardatebtn" class="col-md-3 btn btn-primary btn-raised btn-xs pull-right" style="margin-right: 80px !important;">Clear Date</button>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="shortdescription_div">
                                                    <label class="col-md-3 control-label" for="shortdescription">Short Description</label>
                                                    <div class="col-md-8">
                                                        <textarea id="shortdescription" class="form-control" name="shortdescription"><?php if(isset($offerdata)){ echo $offerdata['shortdescription']; } ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="maximumusage_div">
                                                    <label class="col-md-3 control-label" for="maximumusage">Maximum Usage</label>
                                                    <div class="col-md-8">
                                                    <input type="text" id="maximumusage" class="form-control" name="maximumusage" value="<?php if(isset($offerdata)){ echo $offerdata['maximumusage']; } ?>" maxlength="8" onkeypress="return isNumber(event)">
                                                    </div>
                                                </div>
                                              
                                            </div>

                                            <?php if(isset($offerdata)){
                                                if($offerdata['channelid']!=0){
                                                    $channelidarr = explode(",",$offerdata['channelid']);
                                                    $disabled = "";
                                                }else{
                                                    $disabled = "disabled";
                                                    $channelidarr = array(0);
                                                }
                                            } ?>
                                            <div class="col-sm-6 p-n">
                                                <div class="form-group" id="channel_div">
                                                    <label class="col-md-3 control-label" for="channelid">Select Channel</label>
                                                    <input type="hidden" value="<?php if(isset($offerdata)){ echo $offerdata['channelid']; } ?>" name="oldchannelid">
                                                    <div class="col-md-8">
                                                        <select class="form-control selectpicker" id="channelid" name="channelid" data-actions-box="true">
                                                            <option value="0">Select Channel</option>
                                                            <?php foreach($channeldata as $row){ ?>
                                                                <option value="<?php echo $row['id']; ?>" <?php if(isset($offerdata) && $row['id']==$offerdata['channelid']){  echo 'selected';  } ?>><?php echo $row['name']; ?></option> 
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="member_div">
                                                    <label class="col-md-3 control-label" for="memberid">Select <?=Member_label?></label>
                                                    <input type="hidden" value="<?php if(isset($offerdata)){ echo $offerdata['memberid']; } ?>" name="oldmemberid"></label>
                                                    <div class="col-md-8">
                                                        <select id="memberid" name="memberid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select <?=Member_label?>">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group" id="minbillamount_div">
                                                    <label class="col-md-3 control-label" for="minbillamount">Min. Bill Amount</label>
                                                    <div class="col-md-8">
                                                    <input type="text" id="minbillamount" class="form-control" name="minbillamount" value="<?php if(isset($offerdata)){ echo $offerdata['minbillamount']; } ?>" maxlength="10" onkeypress="return decimal_number_validation(event,this.value)">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="noofcustomerused_div">
                                                    <label class="col-md-3 control-label" for="noofcustomerused">No Of Customer Used</label>
                                                    <div class="col-md-8">
                                                    <input type="text" id="noofcustomerused" class="form-control" name="noofcustomerused" value="<?php if(isset($offerdata)){ echo $offerdata['noofcustomerused']; } ?>" onkeypress="return isNumber(event)" maxlength="8">
                                                    </div>
                                                </div>
                                                <div class="form-group" id="offertype_div">
                                                    <label class="col-md-3 control-label pr-n pl-n pt-xs">Offer Type</label>
                                                    <div class="col-md-8">
                                                        
                                                        <div class="col-sm-4 col-xs-6 pr-sm pl-sm">
                                                            <div class="radio">
                                                                <input type="radio" name="offertype" id="offertypefix" value="1" <?php if(isset($offerdata) && $offerdata['offertype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                <label for="offertypefix">Fix</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-xs-6 pr-sm pl-sm">
                                                            <div class="radio">
                                                            <input type="radio" name="offertype" id="offertypeoptional" value="0" <?php if(isset($offerdata) && $offerdata['offertype']==0){ echo 'checked'; }?>>
                                                            <label for="offertypeoptional">Optional</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-sm-12 p-n">
                                            <div class="col-sm-6 p-n">
                                                <div class="form-group">
                                                    <label class="col-md-3 control-label">Type</label>
                                                    <div class="col-md-9 p-n">
                                                        
                                                        <div class="col-sm-3 col-xs-6 pr-sm">
                                                            <div class="radio">
                                                                <input type="radio" name="type" id="product" value="2" <?php if(isset($offerdata) && $offerdata['type']==2){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                <label for="product">Product</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3 col-xs-6 pr-sm pl-sm">
                                                            <div class="radio">
                                                            <input type="radio" name="type" id="service" value="3" <?php if(isset($offerdata) && $offerdata['type']==3){ echo 'checked'; }?>>
                                                            <label for="service">Service</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2 col-xs-6 pr-sm pl-n">
                                                            <div class="radio">
                                                            <input type="radio" name="type" id="target" value="4" <?php if(isset($offerdata) && $offerdata['type']==4){ echo 'checked'; }?>>
                                                            <label for="target">Target</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-xs-6 pr-sm pl-sm" style="padding-left: 0px;">
                                                            <div class="radio">
                                                                <input type="radio" name="type" id="displayonly" value="1" <?php if(isset($offerdata) && $offerdata['type']==1){ echo 'checked'; }?>>
                                                                <label for="displayonly">Display Only</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 p-n">
                                                <div class="form-group" id="useractivationrequired_div" style="<?php if(isset($offerdata) && ($offerdata['type']==1 || $offerdata['type']==4)){ echo 'display:block;'; }else{ echo 'display:none;'; }?>">
                                                    <label class="col-md-3 control-label pr-n pl-n pt-xs">Activation Required</label>
                                                    <div class="col-md-8">
                                                        
                                                        <div class="col-sm-4 col-xs-6 pr-sm pl-sm">
                                                            <div class="radio">
                                                                <input type="radio" name="useractivationrequired" id="useractivationyes" value="1" <?php if(isset($offerdata) && $offerdata['useractivationrequired']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                <label for="useractivationyes">Yes</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4 col-xs-6 pr-sm pl-sm">
                                                            <div class="radio">
                                                            <input type="radio" name="useractivationrequired" id="useractivationno" value="0" <?php if(isset($offerdata) && $offerdata['useractivationrequired']==0){ echo 'checked'; }?>>
                                                            <label for="useractivationno">No</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 p-n">
                                            <div class="col-sm-6 p-n">
                                                <div class="form-group targetdiv" id="targetvalue_div" style="display: <?php if(isset($offerdata) && $offerdata['type']==4){ echo 'block'; }else{ echo 'none'; }?>;">
                                                    <label for="targetvalue" class="col-md-3 control-label">Target Value <span class="mandatoryfield"> * </span></label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="targetvalue" class="form-control" name="targetvalue" value="<?php if(isset($offerdata)){ echo $offerdata['targetvalue']; } ?>" onkeypress="return decimal_number_validation(event, this.value,10)">
                                                    </div>
                                                </div>
                                                <div class="form-group targetdiv" id="rewardtype_div" style="display: <?php if(isset($offerdata) && $offerdata['type']==4){ echo 'block'; }else{ echo 'none'; }?>;">
                                                    <label class="col-md-3 control-label pr-n pl-n pt-xs">Reward Type</label>
                                                    <div class="col-md-8">
                                                        
                                                        <div class="col-sm-5 col-xs-6 pr-sm pl-sm">
                                                            <div class="radio">
                                                                <input type="radio" name="rewardtype" id="rewardtypepercentage" value="1" <?php if(isset($offerdata) && $offerdata['rewardtype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                <label for="rewardtypepercentage">Percentage</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-5 col-xs-6 pr-sm pl-sm">
                                                            <div class="radio">
                                                            <input type="radio" name="rewardtype" id="rewardtypeamount" value="0" <?php if(isset($offerdata) && $offerdata['rewardtype']==0){ echo 'checked'; }?>>
                                                            <label for="rewardtypeamount">Amount</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 p-n">
                                                <div class="form-group targetdiv" id="rewardvalue_div" style="display: <?php if(isset($offerdata) && $offerdata['type']==4){ echo 'block'; }else{ echo 'none'; }?>;">
                                                    <label for="rewardvalue" class="col-md-3 control-label">Reward Value</label>
                                                    <div class="col-md-8">
                                                        <input type="text" id="rewardvalue" class="form-control" name="rewardvalue" value="<?php if(isset($offerdata)){ echo $offerdata['rewardvalue']; } ?>" onkeypress="return decimal_number_validation(event, this.value,10)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 pl-xl pr-xl">
                                            <div class="form-group" id="description_div">
                                                <div id='termscontainer'>
                                                    <label for="focusedinput" class="col-sm-12" for="description" style="text-align: left;">Description</label>
                                                    <div class="col-sm-12">
                                                        <?php $data['controlname']="description";if(isset($offerdata) && !empty($offerdata)){$data['controldata']=$offerdata['description'];} ?>
                                                        <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Offer Files</h2>
									</div>
									<div class="panel-body">
                                        <div class="col-sm-12 p-n">
                                            <?php if(!empty($offerdata) && !empty($offerimagedata)) { ?>
                                                <input type="hidden" name="removeofferimageid" id="removeofferimageid">
                                                <?php for ($i=0; $i < count($offerimagedata); $i++) { ?>
                                                    <div class="col-md-6 p-n countimages" id="countimages<?=$i+1?>">
                                                        <input type="hidden" name="offerimageid<?=$i+1?>" value="<?=$offerimagedata[$i]['id']?>" id="offerimageid<?=$i+1?>">
                                                        <div class="col-md-9">
                                                            <div class="form-group" id="image<?=$i+1?>_div">
                                                                <div class="col-md-3 text-center">
                                                                    <img src="<?=OFFER.$offerimagedata[$i]['filename']?>" id="imagepreview<?=$i+1?>"
                                                                        class="thumbwidth">
                                                                </div>
                                                                <div class="col-md-9 pl-n" style="padding-top: 23px;">
                                                                    <div class="input-group" id="fileupload<?=$i+1?>">
                                                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                            <span class="btn btn-primary btn-raised btn-file"><i
                                                                                    class="fa fa-upload"></i>
                                                                                <input type="file" name="offerimage<?=$i+1?>"
                                                                                    class="offerimage" id="offerimage<?=$i+1?>"
                                                                                    accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                                            </span>
                                                                        </span>
                                                                        <input type="text" readonly="" id="Filetext<?=$i+1?>"
                                                                            class="form-control" name="Filetext[]" value="<?=$offerimagedata[$i]['filename']?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group" id="priority<?=$i+1?>_div">
                                                                <label class="control-label">Priority</label>
                                                                <input type="text" class="form-control" name="priority[]" id="priority<?=$i+1?>" value="<?=$offerimagedata[$i]['priority']?>" onkeypress="return isNumber(event)" maxlength="4">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pl-sm mt-xxl">
                                                            <?php if($i==0){?>
                                                                <?php if(count($offerimagedata)>1){ ?>
                                                                    <button type="button" class="btn btn-default btn-raised remove_image_btn" onclick="removeimage(1)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                                <?php }else { ?>
                                                                    <button type="button" class="btn btn-default btn-raised add_image_btn" onclick="addnewimage()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                <?php } ?>

                                                            <? }else if($i!=0) { ?>
                                                                <button type="button" class="btn btn-default btn-raised remove_image_btn" onclick="removeimage(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-minus"></i></button>
                                                            <? } ?>
                                                            <button type="button" class="btn btn-default btn-raised btn-sm remove_image_btn" onclick="removeimage(<?=$i+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                        
                                                            <button type="button" class="btn btn-default btn-raised add_image_btn" onclick="addnewimage()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php }else{ ?>
                                                <div class="col-md-6 p-n countimages" id="countimages1"> 
                                                    
                                                    <div class="col-md-9">
                                                        <div class="form-group" id="image1_div">
                                                            <div class="col-md-3 text-center">
                                                                <img src="<?=DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW?>" id="imagepreview1"
                                                                    class="thumbwidth">
                                                            </div>
                                                            <div class="col-md-9 pl-n" style="padding-top: 23px;">
                                                                <div class="input-group" id="fileupload1">
                                                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                        <span class="btn btn-primary btn-raised btn-file"><i
                                                                                class="fa fa-upload"></i>
                                                                            <input type="file" name="offerimage1"
                                                                                class="offerimage" id="offerimage1"
                                                                                accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                                        </span>
                                                                    </span>
                                                                    <input type="text" readonly="" id="Filetext1"
                                                                        class="form-control" name="Filetext[]" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-group" id="priority1_div">
                                                            <label class="control-label">Priority</label>
                                                            <input type="text" class="form-control" name="priority[]" id="priority1" value="" onkeypress="return isNumber(event)" maxlength="4">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 pl-sm mt-xxl">
                                                        <button type="button" class="btn btn-default btn-raised remove_image_btn m-n" onclick="removeimage(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>

                                                        <button type="button" class="btn btn-default btn-raised  add_image_btn m-n" onclick="addnewimage()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Offer Product Combination</h2>
									</div>
									<div class="panel-body">
                                        <input type="hidden" id="removecombinationtableid" name="removecombinationtableid" value="">
                                        <?php if(!empty($offerdata) && !empty($offercombination)){ 
                                            $offercombinationid = explode(',',$offerdata['offercombinationid']);
                                            $multiplication = explode(',',$offerdata['multiplication']);
                                            for($i=0;$i<count($offercombinationid);$i++){
                                        ?>
                                            <div class="col-sm-12 p-n combination" id="combination_<?=$i+1?>_div">
                                                <div class="well well-sm" style="float:left;">
                                                    <input type="hidden" id="combinationid<?=$i+1?>" name="combinationid[]" value="<?=$i+1?>">
                                                    <input type="hidden" id="combinationtableid<?=$i+1?>" name="combinationtableid[]" value="<?=$offercombinationid[$i]?>">
                                                    
                                                    <input type="hidden" id="removepurchaseofferproductid<?=$i+1?>" name="removepurchaseofferproductid[]" value="">
                                                    <input type="hidden" id="removeofferproductid<?=$i+1?>" name="removeofferproductid[]" value="">
                                                    
                                                    <div class="col-sm-6 p-n">
                                                        <div class="panel-heading p-n">
                                                            <h2 class="p-n">Product</h2>
                                                        </div>
                                                        <div class="col-sm-12 p-n">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label col-md-3">Multiplication</label>
                                                                <div class="col-md-8 p-n">
                                                                    <div class="col-sm-3 col-xs-6" style="padding-left: 0px;">
                                                                        <div class="radio">
                                                                            <input type="radio" name="multiplication_<?=$i+1?>" id="nomultiplication_<?=$i+1?>" value="0" <?php if($multiplication[$i]==0){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                            <label for="nomultiplication_<?=$i+1?>">No</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3 col-xs-6">
                                                                        <div class="radio">
                                                                            <input type="radio" name="multiplication_<?=$i+1?>" id="yesmultiplication_<?=$i+1?>" value="1" <?php if($multiplication[$i]==1){ echo 'checked'; }?>>
                                                                            <label for="yesmultiplication_<?=$i+1?>">Yes</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-12 p-n brandoption" style="<?php if($offerdata['offertype']==0){ echo "display:block;"; }else{ echo "display:none;"; } ?>">
                                                            <div class="col-md-6 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n" id="brandid<?=$i+1?>_div">
                                                                    <label class="control-label">Select Brand</label>
                                                                    <select id="brandid<?=$i+1?>" name="brandid[]" class="selectpicker form-control brandid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                        <option value="0">Select Brand</option>
                                                                        <?=$BRAND_DATA?>
                                                                    </select>
                                                                </div>
                                                            </div>    
                                                            <div class="col-md-5 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n text-right" id="minpurchaseamount<?=$i+1?>_div">
                                                                    <label class="control-label">Min. Purchase Amount (&#8377;) <span class="mandatoryfield">*</span></label>    
                                                                    <input type="text" id="minpurchaseamount<?=$i+1?>" name="minpurchaseamount" class="form-control text-right minpurchaseamount" onkeypress="return decimal_number_validation(event, this.value)" value="<?php if($offerdata['offertype']==0){ echo number_format($offerdata['minimumpurchaseamount'],2,'.',''); } ?>">
                                                                </div>
                                                            </div>
                                                        </div>  
                                                        <div class="col-sm-12 p-n">
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n">
                                                                    <label class="control-label">Select Product</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n">
                                                                    <label class="control-label">Select Variant</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n">
                                                                    <label class="control-label">Qty.</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php 
                                                        if(!empty($offercombination[$offercombinationid[$i]][0])){
                                                            for($pp=0;$pp<count($offercombination[$offercombinationid[$i]][0]);$pp++){ 
                                                                $purchaseproduct = $offercombination[$offercombinationid[$i]][0][$pp]?>
                                                                <input type="hidden" id="purchaseofferproductid_<?=$i+1?>_<?=$pp+1?>" name="purchaseofferproductid[<?=$i+1?>][]" value="<?=$purchaseproduct['id']?>">
                                                        
                                                                <div class="col-sm-12 p-n purchaseproduct" id="countpurchaseproduct_<?=$i+1?>_<?=$pp+1?>">
                                                                    <div class="col-md-4 pr-sm pl-sm">
                                                                        <div class="form-group ml-n mr-n" id="purchaseproductid_<?=$i+1?>_<?=$pp+1?>_div">
                                                                            <select id="purchaseproductid_<?=$i+1?>_<?=$pp+1?>" name="purchaseproductid[<?=$i+1?>][]" class="selectpicker form-control purchaseproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                                <option value="0">Select Product</option>
                                                                                <?=$PURCHASE_PRODUCT_DATA?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4 pr-sm pl-sm">
                                                                        <div class="form-group ml-n mr-n" id="purchasepriceid_<?=$i+1?>_<?=$pp+1?>_div">
                                                                            <select id="purchasepriceid_<?=$i+1?>_<?=$pp+1?>" name="purchasepriceid[<?=$i+1?>][<?=$pp?>][]" class="selectpicker form-control purchasepriceid" multiple data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Variant">
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 pr-sm pl-sm">
                                                                        <div class="form-group ml-n mr-n" id="purchaseqty_<?=$i+1?>_<?=$pp+1?>_div">
                                                                            <input type="text" class="form-control purchaseqty" name="purchaseqty[<?=$i+1?>][]" id="purchaseqty_<?=$i+1?>_<?=$pp+1?>" value="<?=($offerdata['offertype']==1?(MANAGE_DECIMAL_QTY==1?$purchaseproduct['quantity']:(int)$purchaseproduct['quantity']):'')?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" <?php if(isset($offerdata) && $offerdata['offertype']==0){ echo "readonly"; }?>>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                                        
                                                                        <button type="button" class="btn btn-default btn-raised btn-sm remove_purchase_btn_<?=$i+1?> m-n" onclick="removepurchase(<?=$i+1?>,<?=$pp+1?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                        <button type="button" class="btn btn-default btn-raised add_purchase_btn_<?=$i+1?> m-n" onclick="addnewpurchase(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  

                                                                    </div>
                                                                </div>
                                                                <script>
                                                                    $(document).ready(function() {
                                                                    <?php if($offerdata['offertype']==0){ ?>
                                                                        getproductbybrandid(<?=$offerdata['brandid']?>,<?=$i+1?>,<?=$pp+1?>);
                                                                    <?php } ?>
                                                                        $('#purchaseproductid_<?=$i+1?>_<?=$pp+1?>').val(<?=$purchaseproduct['productid']?>);
                                                                        $('#purchaseproductid_<?=$i+1?>_<?=$pp+1?>').selectpicker('refresh');
                                                                        getpricebyproductid(<?=$i+1?>,<?=$pp+1?>,'purchasepriceid',<?=$purchaseproduct['productid']?>);
                                                                        $('#purchasepriceid_<?=$i+1?>_<?=$pp+1?>').val([<?=$purchaseproduct['productvariantid']?>]);
                                                                        $('#purchasepriceid_<?=$i+1?>_<?=$pp+1?>').selectpicker('refresh');
                                                                    });
                                                                </script>
                                                        <? } ?>
                                                        <? }else{ ?>
                                                            <div class="col-sm-12 p-n purchaseproduct" id="countpurchaseproduct_1_1">
                                                                <div class="col-md-4 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n" id="purchaseproductid_1_1_div">
                                                                        <select id="purchaseproductid_1_1" name="purchaseproductid[1][]" class="selectpicker form-control purchaseproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                            <option value="0">Select Product</option>
                                                                            <?=$PURCHASE_PRODUCT_DATA?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n" id="purchasepriceid_1_1_div">
                                                                        <select id="purchasepriceid_1_1" name="purchasepriceid[1][0][]" class="selectpicker form-control purchasepriceid" multiple data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Variant">
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n" id="purchaseqty_1_1_div">
                                                                        <input type="text" class="form-control" name="purchaseqty[1][]" id="purchaseqty_1_1" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                                    <button type="button" class="btn btn-default btn-raised remove_purchase_btn_1 m-n" onclick="removepurchase(1,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                    <button type="button" class="btn btn-default btn-raised  add_purchase_btn_1 m-n" onclick="addnewpurchase(1)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                        <? } ?>
                                                        <div id="purchaseproduct_<?=$i+1?>_div">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 p-n">
                                                        <div class="panel-heading p-n">
                                                            <h2 class="p-n" style="width:100%">
                                                                <div class="col-sm-9 p-n"><span class="gifthead<?=$i+1?>">Gift Product</span></div>
                                                                <div class="col-sm-3 p-n text-right">
                                                                    <a href="javascript:void(0)" class="btn btn-danger btn-raised btn-sm remove_combination_btn" onclick="removecombination(<?=$i+1?>)" style="display:none;"><i class="fa fa-minus mr-n" aria-hidden="true"></i></a>
                                                                    <a href="javascript:void(0)" class="btn btn-success btn-raised add_combination_btn" onclick="addnewcombination()" style="<?php if(isset($offerdata) && ($offerdata['type']==1 || $offerdata['type']==4 || $offerdata['offertype']==0)){ echo 'display:none;'; }?>"><i class="fa fa-plus mr-n" aria-hidden="true"></i></a>
                                                                </div>
                                                            </h2>
                                                        </div>
                                                        <div class="col-sm-12 p-n gifthead<?=$i+1?>">
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n">
                                                                    <label class="control-label">Select Product</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n">
                                                                    <label class="control-label">Select Variant</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n">
                                                                    <label class="control-label">Qty.</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <?php 
                                                        if(!empty($offercombination[$offercombinationid[$i]][1])){
                                                            for($op=0;$op<count($offercombination[$offercombinationid[$i]][1]);$op++){ 
                                                            $offerproduct = $offercombination[$offercombinationid[$i]][1][$op]?>
                                                        <input type="hidden" id="offerproducttableid_<?=$i+1?>_<?=$op+1?>" name="offerproducttableid[<?=$i+1?>][]" value="<?=$offerproduct['id']?>">
                                                        <div class="col-sm-12 p-n offerproduct" id="countofferproduct_<?=$i+1?>_<?=$op+1?>">
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n" id="offerproductid_<?=$i+1?>_<?=$op+1?>_div">
                                                                    <select id="offerproductid_<?=$i+1?>_<?=$op+1?>" name="offerproductid[<?=$i+1?>][]" class="selectpicker form-control offerproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                        <option value="0">Select Product</option>
                                                                        <?=$OFFER_PRODUCT_DATA?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n" id="offerpriceid_<?=$i+1?>_<?=$op+1?>_div">
                                                                    <select id="offerpriceid_<?=$i+1?>_<?=$op+1?>" name="offerpriceid[<?=$i+1?>][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                        <option value="0">Select Variant</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n" id="offerqty_<?=$i+1?>_<?=$op+1?>_div">
                                                                    <input type="text" class="form-control" name="offerqty[<?=$i+1?>][]" id="offerqty_<?=$i+1?>_<?=$op+1?>" value="<?=(MANAGE_DECIMAL_QTY==1?$offerproduct['quantity']:(int)$offerproduct['quantity'])?>" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                                <button type="button" class="btn btn-default btn-raised remove_offer_btn_<?=$i+1?> m-n" onclick="removeoffer(<?=$i+1?>,<?=$op+1?>)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                <button type="button" class="btn btn-default btn-raised add_offer_btn_<?=$i+1?> m-n" onclick="addnewoffer(<?=$i+1?>)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                            </div>
                                                            <div class="col-md-6 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n">
                                                                    <label class="control-label">Discount Type</label>
                                                                    <div class="col-md-12 p-n">
                                                                        <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                                                            <div class="radio">
                                                                            <input type="radio" name="offerdiscounttype_<?=$i+1?>_<?=$op+1?>" id="percentage_<?=$i+1?>_<?=$op+1?>" value="1" <?php if($offerproduct['discounttype']==1){ echo 'checked'; }else{ echo 'checked'; }?> onclick="validdiscount(<?=$i+1?>,<?=$op+1?>)">
                                                                            <label for="percentage_<?=$i+1?>_<?=$op+1?>">Percentage</label>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6 col-xs-6">
                                                                            <div class="radio">
                                                                            <input type="radio" name="offerdiscounttype_<?=$i+1?>_<?=$op+1?>" id="amounttype_<?=$i+1?>_<?=$op+1?>" value="0" <?php if($offerproduct['discounttype']==0){ echo 'checked'; }?> onclick="validdiscount(<?=$i+1?>,<?=$op+1?>)">
                                                                            <label for="amounttype_<?=$i+1?>_<?=$op+1?>">Amount</label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 pr-sm pl-sm">
                                                                <div class="form-group ml-n mr-n" id="discountvalue_<?=$i+1?>_<?=$op+1?>_div">
                                                                    <label class="control-label">Discount</label>
                                                                    <div class="col-md-12 p-n">
                                                                        <input type="text" class="form-control" name="discountvalue[<?=$i+1?>][]" id="discountvalue_<?=$i+1?>_<?=$op+1?>" value="<?=$offerproduct['discountvalue']?>" onkeypress="return decimal_number_validation(event,this.value)" onkeyup="validdiscount(<?=$i+1?>,<?=$op+1?>)" maxlength="8">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <script>
                                                            $(document).ready(function() {
                                                                $('#offerproductid_<?=$i+1?>_<?=$op+1?>').val(<?=$offerproduct['productid']?>);
                                                                getpricebyproductid(<?=$i+1?>,<?=$op+1?>,'offerpriceid',<?=$offerproduct['productid']?>);
                                                                $('#offerpriceid_<?=$i+1?>_<?=$op+1?>').val(<?=$offerproduct['productvariantid']?>);
                                                                $('#offerpriceid_<?=$i+1?>_<?=$op+1?>').selectpicker('refresh');
                                                            });
                                                        </script>
                                                        <? } ?>
                                                        <? }else{ ?>
                                                            <div class="col-sm-12 p-n offerproduct" id="countofferproduct_1_1"  style="display:<?=($offerdata['type']==3)?'none':'block'?>">
                                                                <div class="col-md-4 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n" id="offerproductid_1_1_div">
                                                                        <select id="offerproductid_1_1" name="offerproductid[1][]" class="selectpicker form-control offerproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                            <option value="0">Select Product</option>
                                                                            <?=$OFFER_PRODUCT_DATA?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n" id="offerpriceid_1_1_div">
                                                                        <select id="offerpriceid_1_1" name="offerpriceid[1][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                            <option value="0">Select Variant</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n" id="offerqty_1_1_div">
                                                                        <input type="text" class="form-control" name="offerqty[1][]" id="offerqty_1_1" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                                    <button type="button" class="btn btn-default btn-raised remove_offer_btn_1 m-n" onclick="removeoffer(1,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                                    <button type="button" class="btn btn-default btn-raised add_offer_btn_1 m-n" onclick="addnewoffer(1)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                                </div>
                                                                <div class="col-md-6 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n">
                                                                        <label class="control-label">Discount Type</label>
                                                                        <div class="col-md-12 p-n">
                                                                            <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                                                                <div class="radio">
                                                                                <input type="radio" name="offerdiscounttype_1_1" id="percentage_1_1" value="1" checked="" onclick="validdiscount(1,1)">
                                                                                <label for="percentage_1_1">Percentage</label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-6 col-xs-6">
                                                                                <div class="radio">
                                                                                <input type="radio" name="offerdiscounttype_1_1" id="amounttype_1_1" value="0" onclick="validdiscount(1,1)">
                                                                                <label for="amounttype_1_1">Amount</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4 pr-sm pl-sm">
                                                                    <div class="form-group ml-n mr-n" id="discountvalue_1_1_div">
                                                                        <label class="control-label">Discount</label>
                                                                        <div class="col-md-12 p-n">
                                                                            <input type="text" class="form-control" name="discountvalue[1][]" id="discountvalue_1_1" onkeypress="return decimal_number_validation(event,this.value)" onkeyup="validdiscount(1,1)" maxlength="8">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <? } ?>
                                                        <div id="offerproduct_<?=$i+1?>_div">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                $(document).ready(function() {
                                                    if($(".remove_purchase_btn_"+<?=$i+1?>).length>1){
                                                        $(".remove_purchase_btn_"+<?=$i+1?>).show();
                                                        $(".remove_purchase_btn_"+<?=$i+1?>+":last").show();
                                                        $(".add_purchase_btn_"+<?=$i+1?>).hide();
                                                        $(".add_purchase_btn_"+<?=$i+1?>+":last").show();
                                                    }
                                                    if($(".remove_offer_btn_"+<?=$i+1?>).length>1){
                                                        $(".remove_offer_btn_"+<?=$i+1?>).show();
                                                        $(".remove_offer_btn_"+<?=$i+1?>+":last").show();
                                                        $(".add_offer_btn_"+<?=$i+1?>).hide();
                                                        $(".add_offer_btn_"+<?=$i+1?>+":last").show();
                                                    }
                                                    if($(".remove_combination_btn").length>1){
                                                        $(".remove_combination_btn").show();
                                                        $(".remove_combination_btn:last").show();
                                                        $(".add_combination_btn").hide();
                                                        $(".add_combination_btn:last").show();
                                                    }
                                                    <?php if($offerdata['offertype']==0){ ?>
                                                        $(".add_combination_btn").hide();
                                                        $(".remove_combination_btn").hide();
                                                    <?php } ?>
                                                });
                                            </script>
                                        <? } }else{ ?>
                                        <div class="col-sm-12 p-n combination" id="combination_1_div" style="display:<?=(isset($offerdata['type']) && $offerdata['type']==1)?'none':'block'?>">
                                            <div class="well well-sm" style="float:left;">
                                                <input type="hidden" id="combinationid1" name="combinationid[]" value="1">
                                                <div class="col-sm-6 p-n">
                                                    <div class="panel-heading p-n">
                                                        <h2 class="p-n">Purchased Product</h2>
                                                    </div>
                                                    <div class="col-sm-12 p-n">
                                                        <div class="form-group ml-n mr-n">
                                                            <label class="control-label col-md-3">Multiplication</label>
                                                            <div class="col-md-8 p-n">
                                                                <div class="col-sm-3 col-xs-6" style="padding-left: 0px;">
                                                                    <div class="radio">
                                                                        <input type="radio" name="multiplication_1" id="nomultiplication_1" value="0" checked>
                                                                        <label for="nomultiplication_1">No</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3 col-xs-6">
                                                                    <div class="radio">
                                                                        <input type="radio" name="multiplication_1" id="yesmultiplication_1" value="1">
                                                                        <label for="yesmultiplication_1">Yes</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 p-n brandoption" style="display:none;">
                                                        <div class="col-md-6 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="brandid1_div">
                                                                <label class="control-label">Select Brand</label>    
                                                                <select id="brandid1" name="brandid[]" class="selectpicker form-control brandid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                    <option value="0">Select Brand</option>
                                                                    <?=$BRAND_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-5 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n text-right" id="minpurchaseamount1_div">
                                                                <label class="control-label">Min. Purchase Amount (&#8377;) <span class="mandatoryfield">*</span></label>    
                                                                <input type="text" id="minpurchaseamount1" name="minpurchaseamount" class="form-control text-right minpurchaseamount" onkeypress="return decimal_number_validation(event, this.value)">
                                                            </div>
                                                        </div>    
                                                    </div>
                                                    <div class="col-sm-12 p-n">
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label">Select Product</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label">Select Variant</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label">Qty.</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 p-n purchaseproduct" id="countpurchaseproduct_1_1">
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="purchaseproductid_1_1_div">
                                                                <select id="purchaseproductid_1_1" name="purchaseproductid[1][]" class="selectpicker form-control purchaseproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                    <option value="0">Select Product</option>
                                                                    <?=$PURCHASE_PRODUCT_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="purchasepriceid_1_1_div">
                                                                <select id="purchasepriceid_1_1" name="purchasepriceid[1][0][]" class="selectpicker form-control purchasepriceid" multiple data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Variant">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="purchaseqty_1_1_div">
                                                                <input type="text" class="form-control purchaseqty" name="purchaseqty[1][]" id="purchaseqty_1_1" value="" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                            <button type="button" class="btn btn-default btn-raised remove_purchase_btn_1 m-n" onclick="removepurchase(1,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                            <button type="button" class="btn btn-default btn-raised  add_purchase_btn_1 m-n" onclick="addnewpurchase(1)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                    <div id="purchaseproduct_1_div">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 p-n">
                                                    <div class="panel-heading p-n">
                                                        <h2 class="p-n" style="width:100%">
                                                            <div class="col-sm-9 p-n"><span class="gifthead1">Gift Product</span></div>
                                                            <div class="col-sm-3 p-n text-right">
                                                                <a href="javascript:void(0)" class="btn btn-danger btn-raised btn-sm remove_combination_btn" onclick="removecombination(1)" style="display:none;"><i class="fa fa-minus mr-n" aria-hidden="true"></i></a>
                                                                <a href="javascript:void(0)" class="btn btn-success btn-raised add_combination_btn" onclick="addnewcombination()"><i class="fa fa-plus mr-n" aria-hidden="true"></i></a>
                                                            </div>
                                                        </h2>
                                                    </div>
                                                    <div class="col-sm-12 p-n gifthead1">
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label">Select Product</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label">Select Variant</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label">Qty.</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 p-n offerproduct" id="countofferproduct_1_1">
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="offerproductid_1_1_div">
                                                                <select id="offerproductid_1_1" name="offerproductid[1][]" class="selectpicker form-control offerproductid" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                    <option value="0">Select Product</option>
                                                                    <?=$OFFER_PRODUCT_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="offerpriceid_1_1_div">
                                                                <select id="offerpriceid_1_1" name="offerpriceid[1][]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" data-actions-box="true">
                                                                    <option value="0">Select Variant</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="offerqty_1_1_div">
                                                                <input type="text" class="form-control" name="offerqty[1][]" id="offerqty_1_1" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                            <button type="button" class="btn btn-default btn-raised remove_offer_btn_1 m-n" onclick="removeoffer(1,1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                            <button type="button" class="btn btn-default btn-raised add_offer_btn_1 m-n" onclick="addnewoffer(1)" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                        <div class="col-md-6 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n">
                                                                <label class="control-label">Discount Type</label>
                                                                <div class="col-md-12 p-n">
                                                                    <div class="col-sm-6 col-xs-6" style="padding-left: 0px;">
                                                                        <div class="radio">
                                                                        <input type="radio" name="offerdiscounttype_1_1" id="percentage_1_1" value="1" checked="" onclick="validdiscount(1,1)">
                                                                        <label for="percentage_1_1">Percentage</label>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6 col-xs-6">
                                                                        <div class="radio">
                                                                        <input type="radio" name="offerdiscounttype_1_1" id="amounttype_1_1" value="0" onclick="validdiscount(1,1)">
                                                                        <label for="amounttype_1_1">Amount</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="discountvalue_1_1_div">
                                                                <label class="control-label">Discount</label>
                                                                <div class="col-md-12 p-n">
                                                                    <input type="text" class="form-control" name="discountvalue[1][]" id="discountvalue_1_1" value="" onkeypress="return decimal_number_validation(event,this.value)" onkeyup="validdiscount(1,1)" maxlength="8">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div id="offerproduct_1_div">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <? } ?>
                                        <div id="combination_div">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php /*
                        <div class="row targetdiv" style="display:none;">
							<div class="col-md-12">
								<div class="panel panel-default border-panel">
									<div class="panel-heading">
										<h2>Gift Product</h2>
									</div>
									<div class="panel-body">
                                        <div class="col-sm-12 p-n">
                                            <div class="col-sm-6 p-n">
                                                <div class="col-md-4 pr-sm pl-sm">
                                                    <div class="form-group ml-n mr-n">
                                                        <label class="control-label">Select Product</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pr-sm pl-sm">
                                                    <div class="form-group ml-n mr-n">
                                                        <label class="control-label">Select Variant</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pr-sm pl-sm">
                                                    <div class="form-group ml-n mr-n">
                                                        <label class="control-label">Qty.</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 p-n" id="giftheader2" style="display:none;">
                                                <div class="col-md-4 pr-sm pl-sm">
                                                    <div class="form-group ml-n mr-n">
                                                        <label class="control-label">Select Product</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pr-sm pl-sm">
                                                    <div class="form-group ml-n mr-n">
                                                        <label class="control-label">Select Variant</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pr-sm pl-sm">
                                                    <div class="form-group ml-n mr-n">
                                                        <label class="control-label">Qty.</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 p-n">
                                            <input type="hidden" id="removegiftproductid" name="giftproductid[]" value="">
                                            <?php 
                                            if(!empty($giftproductdata)){
                                                for($gp=0;$gp<count($giftproductdata);$gp++){ 
                                                    $giftproduct = $giftproductdata[$gp]?>
                                                    <input type="hidden" id="offerproducttableid<?=($gp+1)?>" name="offerproducttableid[]" value="<?=$giftproduct['id']?>">
                                            
                                                    <div class="col-sm-6 p-n countgiftproduct" id="countgiftproduct<?=($gp+1)?>">
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="giftproductid<?=($gp+1)?>_div">
                                                                <select id="giftproductid<?=($gp+1)?>" name="giftproductid[]" class="selectpicker form-control giftproductid" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true">
                                                                    <option value="0">Select Product</option>
                                                                    <?=$OFFER_PRODUCT_DATA?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="giftpriceid<?=($gp+1)?>_div">
                                                                <select id="giftpriceid<?=($gp+1)?>" name="giftpriceid[]" class="selectpicker form-control giftpriceid" multiple data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="Select Variant">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pr-sm pl-sm">
                                                            <div class="form-group ml-n mr-n" id="giftqty<?=($gp+1)?>_div">
                                                                <input type="text" class="form-control giftqty" name="giftqty[]" id="giftqty<?=($gp+1)?>" value="<?=($offerdata['offertype']==1?$giftproduct['quantity']:'')?>" onkeypress="return isNumber(event)" maxlength="4" <?php if(isset($offerdata) && $offerdata['offertype']==0){ echo "readonly"; }?>>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                            
                                                            <button type="button" class="btn btn-default btn-raised btn-sm remove_gift_btn m-n" onclick="removegiftproduct(<?=($gp+1)?>)"  style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                            <button type="button" class="btn btn-default btn-raised add_gift_btn m-n" onclick="addnewgiftproduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>  

                                                        </div>
                                                    </div>
                                                    <script>
                                                        $(document).ready(function() {
                                                            $('#giftproductid<?=($gp+1)?>').val(<?=$giftproduct['productid']?>).selectpicker('refresh');
                                                            getgiftpricebyproductid(<?=($gp+1)?>,<?=$giftproduct['productid']?>);
                                                            $('#giftpriceid<?=($gp+1)?>').val([<?=$giftproduct['productvariantid']?>]).selectpicker('refresh');
                                                        });
                                                    </script>
                                            <? } ?>
                                            <? }else{ ?>
                                                <div class="col-sm-6 p-n countgiftproduct" id="countgiftproduct1">
                                                    <div class="col-md-4 pr-sm pl-sm">
                                                        <div class="form-group ml-n mr-n" id="giftproductid1_div">
                                                            <select id="giftproductid1" name="giftproductid[]" class="selectpicker form-control giftproductid" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true">
                                                                <option value="0">Select Product</option>
                                                                <?=$OFFER_PRODUCT_DATA?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 pr-sm pl-sm">
                                                        <div class="form-group ml-n mr-n" id="giftpriceid1_div">
                                                            <select id="giftpriceid1" name="giftpriceid[]" class="selectpicker form-control giftpriceid" multiple data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="Select Variant">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 pr-sm pl-sm">
                                                        <div class="form-group ml-n mr-n" id="giftqty1_div">
                                                            <input type="text" class="form-control" name="giftqty[]" id="giftqty1" value="" onkeypress="return isNumber(event)" maxlength="4">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 pl-sm" style="margin-top: 20px !important;">
                                                        <button type="button" class="btn btn-default btn-raised remove_gift_btn m-n" onclick="removegiftproduct(1)" style="padding: 3px 8px;display:none;"><i class="fa fa-minus"></i></button>
                                                        <button type="button" class="btn btn-default btn-raised add_gift_btn m-n" onclick="addnewgiftproduct()" style="padding: 3px 8px;"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            <? } ?>
                                            <div id="giftproductdata_div"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        */ ?>

                        <div class="panel panel-default">
                            <div class="panel-body"> <div class=" p-n">
                                <div class="col-md-12">
                                    <div class="form-group text-center">
                                        <label for="focusedinput" class="col-sm-5 col-xs-4 control-label">Activate</label>
                                        <div class="col-sm-6 col-xs-8">
                                        <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                            <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($offerdata) && $offerdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                            <label for="yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($offerdata) && $offerdata['status']==0){ echo 'checked'; }?>>
                                            <label for="no">No</label>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-actions text-center">
                                        <?php if(!empty($offerdata)){ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                        <?php }else{ ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
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