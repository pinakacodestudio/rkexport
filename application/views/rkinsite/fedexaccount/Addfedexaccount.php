<div class="page-content">
		<div class="page-heading">            
        <h1><?php if(isset($fedexaccountdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($fedexaccountdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
								<form class="form-horizontal" id="formfedexaccount">
								<input type="hidden" name="fedexaccountid" value="<?php if(isset($fedexaccountdata)){ echo $fedexaccountdata['id']; } ?>">
									<div class="form-group" id="accountnumber_div">
										<label for="accountnumber" class="col-sm-3 control-label">Account Number <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="accountnumber" type="text" name="accountnumber" value="<?php if(!empty($fedexaccountdata)){ echo $fedexaccountdata['accountnumber']; } ?>" class="form-control number" maxlength="9">
										</div>
									</div>
									<div class="form-group" id="meternumber_div">
										<label for="meternumber" class="col-sm-3 control-label">Meter Number <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="meternumber" type="text" name="meternumber" value="<?php if(!empty($fedexaccountdata)){ echo $fedexaccountdata['meternumber']; } ?>" class="form-control number" maxlength="9">
										</div>
									</div>
									<div class="form-group" id="apikey_div">
										<label for="apikey" class="col-sm-3 control-label">Api Key <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="apikey" type="text" name="apikey" value="<?php if(!empty($fedexaccountdata)){ echo $fedexaccountdata['apikey']; } ?>" class="form-control">
										</div>
									</div>
									<div class="form-group" id="password_div">
										<label for="password" class="col-sm-3 control-label">Password <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="password" type="text" name="password" value="<?php if(!empty($fedexaccountdata)){ echo $fedexaccountdata['password']; } ?>" class="form-control">
										</div>
									</div>
									<div class="form-group" id="email_div">
										<label for="email" class="col-md-3 control-label">Email <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="email" type="text" name="email" value="<?php if(isset($fedexaccountdata)){ echo $fedexaccountdata['email']; } ?>" class="form-control">
										</div>
									</div>
									<div class="form-group">
										<label for="focusedinput" class="col-sm-3 control-label">Activate</label>
										<div class="col-sm-8">
											<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
												<div class="radio">
												<input type="radio" name="status" id="yes" value="1" <?php if(isset($fedexaccountdata) && $fedexaccountdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
												<label for="yes">Yes</label>
												</div>
											</div>
											<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
												<div class="radio">
												<input type="radio" name="status" id="no" value="0" <?php if(isset($fedexaccountdata) && $fedexaccountdata['status']==0){ echo 'checked'; }?>>
												<label for="no">No</label>
												</div>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label"></label>
										<div class="col-sm-8">
											<?php if(!empty($fedexaccountdata)){ ?>
												<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
												<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
											<?php }else{ ?>
												<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
												<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
											<?php } ?>
											<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>fedexaccount" title=<?=cancellink_title?>><?=cancellink_text?></a>
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