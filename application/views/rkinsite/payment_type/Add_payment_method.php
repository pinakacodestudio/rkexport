<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($paymentmethoddata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($paymentmethoddata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
                    <div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2">
						<form class="form-horizontal" id="paymentmethodform" name="paymentmethodform">
                            <input type="hidden" name="paymentmethodid" value="<?php if(!empty($paymentmethoddata)){ echo $paymentmethoddata['id']; } ?>">
                            <input type="hidden" name="paymentgatewaytype" id="paymentgatewaytype" value="<?=(!empty($paymentgatewaytype))?$paymentgatewaytype:0 ?>">
                            <div class="form-group" id="paymentmethod_div">
                                <label for="paymentmethod" class="col-sm-3 control-label">Payment Method <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <input id="paymentmethod" type="text" name="paymentmethod" value="<?php if(!empty($paymentmethoddata)){ echo $paymentmethoddata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="displayinfront" class="col-sm-3 control-label">Display on Front Side</label>
                                <div class="col-sm-6">
                                    <div class="checkbox" style="text-align:left;">
                                        <input id="displayinfront" name="displayinfront" type="checkbox" <?php if(isset($paymentmethoddata) && $paymentmethoddata['displayinfront']){ echo 'checked' ; }?>>
                                        <label for="displayinfront"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="logo" class="col-sm-3 control-label">Logo</label>
                                <div class="col-sm-6">
                                    <input type="hidden" name="oldlogo" id="oldlogo" value="<?php if(!empty($paymentmethoddata['logo'])){ echo $paymentmethoddata['logo']; } ?>">
                                    <input type="hidden" name="removeoldlogo" id="removeoldlogo" value="0">
                                    <?php if(isset($paymentmethoddata) && $paymentmethoddata['logo']!=''){ ?>
                                    <div class="imageupload" id="paymentmethodlogo">
                                        <div class="file-tab"><img src="<?php echo PAYMENT_METHOD_LOGO.$paymentmethoddata['logo']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                            <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                                <span id="logobtn">Change</span>
                                                <!-- The file is stored here. -->
                                                <input type="file" name="logo" id="logo">
                                            </label>
                                            <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
                                        </div>
                                    </div>
                                    <?php }else{ ?>
                                    <div class="imageupload">
                                        <div class="file-tab">
                                            <img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
                                            <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                                <span id="logobtn">Select Image</span>
                                                <input type="file" name="logo" id="logo" value="">
                                            </label>
                                            <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if(!empty($paymentgatewaydata)){ ?>
                            <?php if($paymentgatewaytype==1){ ?>
                                <div class="form-group" id="merchantid_div">
                                    <label for="merchantid" class="col-sm-3 control-label">Merchant ID <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantid" type="text" name="merchantid" value="<?php echo $paymentgatewaydata['merchantid']; ?>" class="form-control number">
                                    </div>
                                </div>
                                <div class="form-group" id="merchantkey_div">
                                    <label for="merchantkey" class="col-sm-3 control-label">Merchant Key <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantkey" type="text" name="merchantkey" value="<?php echo $paymentgatewaydata['merchantkey']; ?>" class="form-control" onkeypress="return alphanumeric(event)" maxlength="50">
                                    </div>
                                </div>
                                <div class="form-group" id="merchantsalt_div">
                                    <label for="merchantsalt" class="col-sm-3 control-label">Merchant Salt <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantsalt" type="text" name="merchantsalt" value="<?php echo $paymentgatewaydata['merchantsalt']; ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="50">
                                    </div>
                                </div>
                                <div class="form-group" id="authheader_div">
                                    <label for="authheader" class="col-sm-3 control-label">Auth Header <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="authheader" type="text" name="authheader" value="<?php echo $paymentgatewaydata['authheader']; ?>" class="form-control" maxlength="150">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="focusedinput" class="col-md-3 control-label">Payment Mode</label>
                                    <div class="col-md-8">
                                        <div class="col-md-2 col-xs-3" style="padding-left: 0px;">
                                            <div class="radio">
                                            <input type="radio" name="paymentmode" id="paymentmodeyes" value="1" <?php if(isset($paymentmethoddata) && $paymentmethoddata['paymentmode']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                            <label for="paymentmodeyes">Test</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3">
                                            <div class="radio">
                                            <input type="radio" name="paymentmode" id="paymentmodeno" value="0" <?php if(isset($paymentmethoddata) && $paymentmethoddata['paymentmode']==0){ echo 'checked'; }?>>
                                            <label for="paymentmodeno">Live</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <? }else if($paymentgatewaytype==2){ ?>
                                <div class="form-group" id="merchantid_div">
                                    <label for="merchantid" class="col-sm-3 control-label">Merchant ID <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantid" type="text" name="merchantid" value="<?php echo $paymentgatewaydata['merchantid']; ?>" class="form-control" maxlength="30">
                                    </div>
                                </div>
                                <div class="form-group" id="merchantkey_div">
                                    <label for="merchantkey" class="col-sm-3 control-label">Merchant Key <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantkey" type="text" name="merchantkey" value="<?php echo $paymentgatewaydata['merchantkey']; ?>" class="form-control" maxlength="30">
                                    </div>
                                </div>
                                <div class="form-group" id="merchantwebsiteforweb_div">
                                    <label for="merchantwebsiteforweb" class="col-sm-3 control-label">Merchant Website For Web <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantwebsiteforweb" type="text" name="merchantwebsiteforweb" value="<?php echo $paymentgatewaydata['merchantwebsiteforweb']; ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="15">
                                    </div>
                                </div>
                                <div class="form-group" id="merchantwebsiteforapp_div">
                                    <label for="merchantwebsiteforapp" class="col-sm-3 control-label">Merchant Website For App <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantwebsiteforapp" type="text" name="merchantwebsiteforapp" value="<?php echo $paymentgatewaydata['merchantwebsiteforapp']; ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="15">
                                    </div>
                                </div>
                                <div class="form-group" id="channelidforweb_div">
                                    <label for="channelidforweb" class="col-sm-3 control-label">Channel ID For Web <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="channelidforweb" type="text" name="channelidforweb" value="<?php echo $paymentgatewaydata['channelidforweb']; ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="5">
                                    </div>
                                </div>
                                <div class="form-group" id="channelidforapp_div">
                                    <label for="channelidforapp" class="col-sm-3 control-label">Channel ID For App <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="channelidforapp" type="text" name="channelidforapp" value="<?php echo $paymentgatewaydata['channelidforapp']; ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="5">
                                    </div>
                                </div>
                                <div class="form-group" id="industrytypeid_div">
                                    <label for="industrytypeid" class="col-sm-3 control-label">Industry Type ID <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="industrytypeid" type="text" name="industrytypeid" value="<?php echo $paymentgatewaydata['industrytypeid']; ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="10">
                                    </div>
                                </div>
                            <? }else if($paymentgatewaytype==3){ ?>
                                <div class="form-group" id="merchantid_div">
                                    <label for="merchantid" class="col-sm-3 control-label">Merchant ID <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantid" type="text" name="merchantid" value="<?php echo $paymentgatewaydata['merchantid']; ?>" class="form-control number">
                                    </div>
                                </div>
                                <div class="form-group" id="merchantkey_div">
                                    <label for="merchantkey" class="col-sm-3 control-label">Merchant Key <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantkey" type="text" name="merchantkey" value="<?php echo $paymentgatewaydata['merchantkey']; ?>" class="form-control" onkeypress="return alphanumeric(event)" maxlength="50">
                                    </div>
                                </div>
                                <div class="form-group" id="merchantsalt_div">
                                    <label for="merchantsalt" class="col-sm-3 control-label">Merchant Salt <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="merchantsalt" type="text" name="merchantsalt" value="<?php echo $paymentgatewaydata['merchantsalt']; ?>" class="form-control" onkeypress="return onlyAlphabets(event)" maxlength="50">
                                    </div>
                                </div>
                                <div class="form-group" id="authheader_div">
                                    <label for="authheader" class="col-sm-3 control-label">Auth Header <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="authheader" type="text" name="authheader" value="<?php echo $paymentgatewaydata['authheader']; ?>" class="form-control" maxlength="150">
                                    </div>
                                </div>
                            <? }else if($paymentgatewaytype==4){ ?>
                                <div class="form-group" id="keyid_div">
                                    <label for="keyid" class="col-sm-3 control-label">Key ID <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="keyid" type="text" name="keyid" value="<?php echo $paymentgatewaydata['keyid']; ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" id="keysecret_div">
                                    <label for="keysecret" class="col-sm-3 control-label">Key Secret <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="keysecret" type="text" name="keysecret" value="<?php echo $paymentgatewaydata['keysecret']; ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" id="orderurl_div">
                                    <label for="orderurl" class="col-sm-3 control-label">Order URL <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="orderurl" type="text" name="orderurl" value="<?php echo $paymentgatewaydata['orderurl']; ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" id="checkouturl_div">
                                    <label for="checkouturl" class="col-sm-3 control-label">Checkout URL <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8">
                                        <input id="checkouturl" type="text" name="checkouturl" value="<?php echo $paymentgatewaydata['checkouturl']; ?>" class="form-control">
                                    </div>
                                </div>
                            <? } ?>
                            <? } ?>
                               
                           
                            
                            <div class="form-group text-center">
                                <label for="focusedinput" class="col-sm-3 col-xs-4 col-md-3 control-label">Activate</label>
                                <div class="col-md-6 col-xs-8">
                                    <div class="col-sm-3 col-xs-6" style="padding-left: 0px;">
                                        <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($paymentmethoddata) && $paymentmethoddata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                        <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-6">
                                        <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($paymentmethoddata) && $paymentmethoddata['status']==0){ echo 'checked'; }?>>
                                        <label for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-3">
                                <?php if(!empty($paymentmethoddata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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