<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($smsgatewaydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($smsgatewaydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
						<form class="form-horizontal" id="smsgatewayform" name="smsgatewayform">
							<input type="hidden" name="smsgatewayid" value="<?php if(isset($smsgatewaydata)){ echo $smsgatewaydata['id']; } ?>">
                            <div class="col-md-6">
                                <div class="form-group" id="name_div">
                                    <label for="name" class="col-sm-3 control-label">Name <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-9">
                                        <input id="name" type="text" name="name" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['name']; } ?>" class="form-control">
                                    </div>
                                </div>
                            
                                <div class="form-group" id="userid_div">
                                    <label class="col-md-3 control-label" for="userid">User ID <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" id="userid" class="form-control" name="userid" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['userid']; } ?>">
                                    </div>
                                </div>
                               
                                <div class="form-group" id="password_div">
                                    <label class="col-md-3 control-label" for="password">Password <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" id="password" class="form-control" name="password" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['password']; } ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="gatewaylink_div">
                                    <label class="col-md-3 control-label" for="gatewaylink">Gateway Link <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" id="gatewaylink" class="form-control" name="gatewaylink" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['gatewaylink']; } ?>">
                                    </div>
                                </div>
                               
                                <div class="form-group" id="senderid_div">
                                    <label class="col-md-3 control-label" for="senderid">Sender ID <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-9">
                                        <input type="text" id="senderid" class="form-control" name="senderid" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['senderid']; } ?>">
                                    </div>
                                </div>

                                <div class="form-group" id="description_div">
                                    <label class="col-md-3 control-label" for="description">Description</label>
                                    <div class="col-md-9">
                                        <textarea id="description" class="form-control" name="description"><?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['description']; } ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <hr>
                                <div class="form-group text-center">
                                    <label for="focusedinput" class="col-sm-5 col-xs-4col-md-4 control-label">Activate</label>
                                    <div class="col-sm-6 col-xs-8">
                                        <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                            <div class="radio">
                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($smsgatewaydata) && $smsgatewaydata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                            <label for="yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-2 col-xs-6">
                                            <div class="radio">
                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($smsgatewaydata) && $smsgatewaydata['status']==0){ echo 'checked'; }?>>
                                            <label for="no">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <?php if(!empty($smsgatewaydata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                    <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                    <?php } ?>
                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>sms-gateway" title=<?=cancellink_title?>><?=cancellink_text?></a>
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