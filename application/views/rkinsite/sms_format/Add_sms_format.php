<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($smsformatdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($smsformatdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
						<form class="form-horizontal" id="smsformatform" name="smsformatform">
							<input type="hidden" name="smsformatid" value="<?php if(isset($smsformatdata)){ echo $smsformatdata['id']; } ?>">
                            
                                <div class="form-group" id="smsformattype_div">
                                    <label for="smsformattype" class="col-sm-3 control-label">SMS Type <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-6">
                                        <select id="smsformattype" name="smsformattype" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7">
                                            <option value="0">Select Type</option>
                                            <?php foreach($this->Smsformattype as $key=>$formattype){ ?>
                                                <option value="<?php echo $key; ?>" <?php if(isset($smsformatdata) && $smsformatdata['smsformattype']==$key){ echo "selected"; } ?>><?php echo $formattype; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="smsgateway_div">
                                    <label for="smsgatewayid" class="col-sm-3 control-label">SMS Gateway <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-6">
                                        <select id="smsgatewayid" name="smsgatewayid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="7">
                                            <option value="0">Select Gateway</option>
                                            <?php foreach($smsgatewaydata as $smsgateway){ ?>
                                                <option value="<?php echo $smsgateway['id']; ?>" <?php if(isset($smsformatdata) && $smsformatdata['smsgatewayid']==$smsgateway['id']){ echo "selected"; } ?>><?php echo ucwords($smsgateway['name']); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="form-group" id="format_div">
                                    <label class="col-md-3 control-label" for="format">Format <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-6">
                                        <textarea rows="5" id="format" class="form-control" name="format"><?php if(!empty($smsformatdata)){ echo htmlspecialchars($smsformatdata['format']); } ?></textarea>
                                    </div>
                                </div>
                           
                            <div class="col-sm-12">
                                <hr>
                                <div class="form-group text-center">
                                    <label for="focusedinput" class="col-sm-5 col-xs-4col-md-4 control-label">Activate</label>
                                    <div class="col-sm-6 col-xs-8">
                                        <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                            <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($smsformatdata) && $smsformatdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                            <label for="yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($smsformatdata) && $smsformatdata['status']==0){ echo 'checked'; }?>>
                                            <label for="no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <?php if(!empty($smsformatdata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                    <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                    <?php } ?>
                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>sms-format" title=<?=cancellink_title?>><?=cancellink_text?></a>
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