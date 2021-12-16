<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($channeldata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($channeldata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12">
						<form class="form-horizontal" id="channelform" name="channelform">
							<input type="hidden" name="channelid" value="<?php if(isset($channeldata)){ echo $channeldata['id']; } ?>">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group" id="channel_div">
                                        <div class="col-sm-12">
                                            <label for="name" class="control-label">Name <span class="mandatoryfield">*</span></label>
                                            <input id="name" type="text" name="name" value="<?php if(!empty($channeldata)){ echo $channeldata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="channel_div">
                                        <div class="form-group row" id="color_div">
                                            <div class="col-md-12">
                                                <label class="control-label col-form-label" for="color" style="padding-top: 0;margin-top: 3px;">Color <span class="mandatoryfield">*</span></label>
                                                <input type="text" id="color" class="form-control demo" name="color" value="<?php if(!empty($channeldata)){ echo $channeldata['color']; }else{ echo '#70c24a'; } ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="priority_div">
                                        <div class="col-sm-12">
                                            <label for="priority" class="control-label">Channel Priority <span class="mandatoryfield">*</span></label>
                                            <input id="priority" type="text" name="priority" value="<?php if(isset($channeldata)){ echo $channeldata['priority']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
                                        </div>
                                    </div>  
                                </div>
                            </div>
                            <div class="row">
                            <hr>
                                <div class="col-md-6">
                                
                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Quotation<br>
                                        
                                        </label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                    <input type="radio" name="quotation" id="quotationyes" value="1" <?php if(isset($channeldata) && $channeldata['quotation']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                    <label for="quotationyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-8 col-xs-8">
                                                <div class="radio">
                                                <input type="radio" name="quotation" id="quotationno" value="0" <?php if(isset($channeldata) && $channeldata['quotation']==0){ echo 'checked'; }?>>
                                                <label for="quotationno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Partial Payment</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="partialpayment" id="partialpaymentyes" value="1" <?php if(isset($channeldata) && $channeldata['partialpayment']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="partialpaymentyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="partialpayment" id="partialpaymentno" value="0" <?php if(isset($channeldata) && $channeldata['partialpayment']==0){ echo 'checked'; } ?>>
                                                <label for="partialpaymentno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Identity Proof</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="identityproof" id="identityproofyes" value="1" <?php if(isset($channeldata) && $channeldata['identityproof']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="identityproofyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="identityproof" id="identityproofno" value="0" <?php if(isset($channeldata) && $channeldata['identityproof']==0){ echo 'checked'; } ?>>
                                                <label for="identityproofno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label"><?=Member_label?> Specific Product</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="memberspecificproduct" id="memberspecificproductyes" value="1" <?php if(isset($channeldata) && $channeldata['memberspecificproduct']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="memberspecificproductyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="memberspecificproduct" id="memberspecificproductno" value="0" <?php if(isset($channeldata) && $channeldata['memberspecificproduct']==0){ echo 'checked'; } ?>>
                                                <label for="memberspecificproductno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Discount</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="discount" id="discountyes" value="1" <?php if(isset($channeldata) && $channeldata['discount']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="discountyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="discount" id="discountno" value="0" <?php if(isset($channeldata) && $channeldata['discount']==0){ echo 'checked'; } ?>>
                                                <label for="discountno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    
                                </div>
                                <div class="col-md-6">
                                <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Discount Coupon</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="discountcoupon" id="discountcouponyes" value="1" <?php if(isset($channeldata) && $channeldata['discountcoupon']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="discountcouponyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="discountcoupon" id="discountcouponno" value="0" <?php if(isset($channeldata) && $channeldata['discountcoupon']==0){ echo 'checked'; } ?>>
                                                <label for="discountcouponno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Reward</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="reward" id="rewardyes" value="1" <?php if(isset($channeldata) && $channeldata['reward']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="rewardyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="reward" id="rewardno" value="0" <?php if(isset($channeldata) && $channeldata['reward']==0){ echo 'checked'; } ?>>
                                                <label for="rewardno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Rating</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="rating" id="ratingyes" value="1" <?php if(isset($channeldata) && $channeldata['rating']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="ratingyes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="rating" id="ratingno" value="0" <?php if(isset($channeldata) && $channeldata['rating']==0){ echo 'checked'; } ?>>
                                                <label for="ratingno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Debit Limit</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="debitlimit" id="debitlimityes" value="1" <?php if(isset($channeldata) && $channeldata['debitlimit']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="debitlimityes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="debitlimit" id="debitlimitno" value="0" <?php if(isset($channeldata) && $channeldata['debitlimit']==0){ echo 'checked'; } ?>>
                                                <label for="debitlimitno">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="focusedinput" class="col-md-4 control-label">Discount Priority</label>
                                        <div class="col-md-8">
                                            <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                                <div class="radio">
                                                <input type="radio" name="discountpriority" id="discountpriorityyes" value="0" <?php if(isset($channeldata) && $channeldata['discountpriority']==0){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                <label for="discountpriorityyes">General</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-xs-4">
                                                <div class="radio">
                                                <input type="radio" name="discountpriority" id="discountpriorityno" value="1" <?php if(isset($channeldata) && $channeldata['discountpriority']==1){ echo 'checked'; } ?>>
                                                <label for="discountpriorityno"><?=Member_label?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-center">
                                <label for="focusedinput" class="col-sm-5 col-xs-4col-md-4 control-label">Activate</label>
                                <div class="col-sm-6 col-xs-8">
                                    <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                        <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($productsectiondata) && $productsectiondata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                        <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($productsectiondata) && $productsectiondata['status']==0){ echo 'checked'; }?>>
                                        <label for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="form-group text-center">
                                <?php if(!empty($channeldata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>channel" title=<?=cancellink_title?>><?=cancellink_text?></a>
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