<script>
    var CHANNELID = '<?php if(isset($couriercompanydata)){ echo $couriercompanydata['channelid']; } ?>';
    var MEMBERID = '<?php if(isset($couriercompanydata)){ echo $couriercompanydata['memberid']; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($couriercompanydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($couriercompanydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
                    <div class="col-sm-12 col-sm-12 col-xs-12">
                        <form class="form-horizontal" id="couriercompanyform">
                            <input type="hidden" name="couriercompanyid" value="<?php if(isset($couriercompanydata)){ echo $couriercompanydata['id']; } ?>">
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group" id="companyname_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="companyname">Company Name <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8 col-xs-12">
                                        <input id="companyname" type="text" name="companyname" value="<?php if(!empty($couriercompanydata)){ echo $couriercompanydata['companyname']; } ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" id="email_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="email">Email <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8 col-xs-12">
                                        <input id="email" type="text" name="email" value="<?php if(!empty($couriercompanydata)){ echo $couriercompanydata['email']; } ?>" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" id="mobileno_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="mobileno">Mobile No. <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8 col-xs-12">
                                        <input id="mobileno" type="text" name="mobileno" value="<?php if(!empty($couriercompanydata)){ echo $couriercompanydata['mobileno']; } ?>" class="form-control number" minlength="10" maxlength="10">
                                    </div>
                                </div>
                                <div class="form-group" id="channel_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="channelid">Select Channel</label>
                                    <div class="col-sm-8 col-xs-12">
                                        <select id="channelid" name="channelid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                            <option value=''>Select Channel</option>
                                            <option value='0' <?php if(isset($couriercompanydata) && $couriercompanydata['channelid']==0){ echo "selected"; } ?>>Company</option>
                                            <?php foreach ($channeldata as $channel) { ?>
                                                <option value='<?php echo $channel['id']; ?>' <?php if(isset($couriercompanydata) && $couriercompanydata['channelid']==$channel['id']){ echo "selected"; } ?>><?=$channel['name']?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" for="catalogname" id="member_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="memberid">Select <?=Member_label?></label>
                                    <div class="col-sm-8 col-xs-12">
                                        <select id="memberid" name="memberid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                            <option value='0'>Select <?=Member_label?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12">
                                <div class="form-group" id="contactperson_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="contactperson">Contact Person <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8 col-xs-12">
                                        <input id="contactperson" type="text" name="contactperson" value="<?php if(!empty($couriercompanydata)){ echo $couriercompanydata['contactperson']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                </div>
                                <div class="form-group" id="address_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="address">Address <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8 col-xs-12">
                                        <textarea id="address" name="address" class="form-control"><?php if(isset($couriercompanydata)){ echo $couriercompanydata['address']; } ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group" id="cityid_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="cityid">City <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-8 col-xs-12">
                                        <input id="cityid" type="text" name="cityid" data-url="<?php echo base_url().ADMINFOLDER.'city/getactivecity';?>" data-provide='city' data-type='1' data-placeholder="Select City" value="<?php if(isset($couriercompanydata)){ echo $couriercompanydata['cityid']; }else echo 0; ?>" placeholder="Select City" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group" id="trackurl_div">
                                    <label class="col-sm-4 col-xs-12 control-label" for="trackurl">Tracking Url</label>
                                    <div class="col-sm-8 col-xs-12">
                                        <input id="trackurl" type="text" name="trackurl" value="<?php if(isset($couriercompanydata)){ echo urldecode($couriercompanydata['trackurl']); }?>" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="form-group text-center">
                                    <label for="focusedinput" class="col-sm-5 control-label">Activate</label>
                                    <div class="col-sm-7">
                                        <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                            <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($couriercompanydata) && $couriercompanydata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                            <label for="yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($couriercompanydata) && $couriercompanydata['status']==0){ echo 'checked'; }?>>
                                            <label for="no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <div class="col-sm-12">
                                        <?php if(!empty($couriercompanydata)){ ?>
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