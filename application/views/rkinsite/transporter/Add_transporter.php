<script>
    var CHANNELID = '<?php if(isset($transporterdata)){ echo $transporterdata['channelid']; } ?>';
    var MEMBERID = '<?php if(isset($transporterdata)){ echo $transporterdata['memberid']; } ?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($transporterdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($transporterdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12 p-n">
					<form class="form-horizontal" id="transporterform">
						<input type="hidden" id="transporterid" name="transporterid" value="<?php if(isset($transporterdata)){ echo $transporterdata['id']; } ?>">
                        <div class="col-md-6">
                            <div class="form-group" id="companyname_div">
                                <label class="col-sm-4 control-label" for="companyname">Company Name <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-7">
                                    <input id="companyname" type="text" name="companyname" value="<?php if(!empty($transporterdata)){ echo $transporterdata['companyname']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                </div>
                            </div>
                            <div class="form-group" id="mobileno_div">
                                <label class="col-sm-4 control-label" for="mobileno">Mobile No. <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" name="mobileno" id="mobileno" value="<?php if(!empty($transporterdata)){ echo $transporterdata['mobile']; } ?>" class="form-control" maxlength="10" onkeypress="return isNumber(event)">
                                </div>
                            </div>
                            <div class="form-group" id="contactperson_div">
                                <label class="col-sm-4 control-label" for="contactperson">Contact Person</label>
                                <div class="col-sm-7">
                                    <input id="contactperson" type="text" name="contactperson" value="<?php if(!empty($transporterdata)){ echo $transporterdata['contactperson']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                </div>
                            </div>
                            <div class="form-group" id="email_div">
                                <label class="col-sm-4 control-label" for="email">E-mail</label>
                                <div class="col-sm-7">
                                    <input type="text" name="email" id="email" value="<?php if(!empty($transporterdata)){ echo $transporterdata['email']; } ?>" class="form-control">
                                </div>
                            </div>
                            <div class="form-group" id="website_div">
                                <label class="col-sm-4 control-label" for="website">Website</label>
                                <div class="col-sm-7">
                                    <input id="website" type="text" name="website" value="<?php if(!empty($transporterdata)){ echo urldecode($transporterdata['website']); } ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" id="channel_div">
                                <label class="col-md-4 control-label" for="channelid">Select Channel</label>
                                <div class="col-md-7">
                                <select id="channelid" name="channelid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                    <option value='0' <?php if(isset($transporterdata) && $transporterdata['channelid']==0){ echo "selected"; } ?>>Company</option>
                                    <?php foreach ($channeldata as $channel) { ?>
                                        <option value='<?php echo $channel['id']; ?>' <?php if(isset($transporterdata) && $transporterdata['channelid']==$channel['id']){ echo "selected"; } ?>><?=$channel['name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="member_div">
                                <label class="col-md-4 control-label" for="memberid">Select <?=Member_label?></label>
                                <div class="col-md-7">
                                    <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                        <option value='0'>Select <?=Member_label?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="address_div">
                                <label class="col-sm-4 control-label" for="address">Address</label>
                                <div class="col-sm-7">
                                    <textarea id="address" rows="2" name="address" class="form-control"><?php if(!empty($transporterdata)){ echo $transporterdata['address']; } ?></textarea>
                                </div>
                            </div>
                            <div class="form-group" id="city_div">
                                <label class="col-sm-4 control-label" for="cityid">City</label>
                                <div class="col-sm-7">
                                    <input id="cityid" type="text" name="cityid" data-url="<?php echo base_url().ADMINFOLDER.'city/getactivecity';?>" data-provide='city' data-type='1' data-placeholder="Select City" value="<?php if(isset($transporterdata)){ echo $transporterdata['cityid']; }else echo 0; ?>" placeholder="Select City" class="form-control">
                                </div>
                            </div>
                            <div class="form-group" id="trackingurl_div">
                                <label class="col-sm-4 control-label" for="trackingurl">Tracking URL</label>
                                <div class="col-sm-7">
                                    <input id="trackingurl" type="text" name="trackingurl" value="<?php if(!empty($transporterdata)){ echo urldecode($transporterdata['trackingurl']); } ?>" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <hr>
                            <div class="form-group">
                                <label for="focusedinput" class="col-sm-5 control-label">Activate</label>
                                <div class="col-sm-6">
                                    <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                        <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($transporterdata) && $transporterdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                        <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($transporterdata) && $transporterdata['status']==0){ echo 'checked'; }?>>
                                        <label for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-center">
                                <div class="col-sm-12">
                                    <?php if(!empty($transporterdata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
                                    <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
                                    <?php } ?>
                                    <a class="<?=cancellink_class;?>" href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
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