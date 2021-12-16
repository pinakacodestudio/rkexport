<!--<script src="http://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCQplBzEyHAjOBIXHWB1RI_Pls4qLAvxXA" type="text/javascript"></script>

<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($subscribedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($subscribedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12">
					<form class="form-horizontal" id="subscribeform">
						<input type="hidden" name="subscribeid" value="<?php if(isset($subscribedata)){ echo $subscribedata['id']; } ?>">
						<div class="col-md-12">
							<div class="form-group" id="email_div">
									<label class="col-sm-3 control-label">Email <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="email" type="text" name="email"  class="form-control" onkeypress="return onlyAlphabets(event)">
									</div>
							</div>	
						</div>		
						
						<div class="col-md-12 p-n">
								<div class="form-group">
									<label for="focusedinput" class="col-sm-3 control-label">Activate</label>
									<div class="col-sm-8">
										<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="yes" value="1" <?php if(isset($subscribedata) && $subscribedata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="yes">Yes</label>
											</div>
										</div>
										<div class="col-sm-2 col-xs-6">
											<div class="radio">
											<input type="radio" name="status" id="no" value="0" <?php if(isset($subscribedata) && $subscribedata['status']==0){ echo 'checked'; }?>>
											<label for="no">No</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6 p-n">
							</div>
						</div>
						<div class="col-md-12 p-n" style="text-align: center;">
							<div class="form-group">
							<?php if(!empty($subscribedata)){ ?>
								<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								  <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>subscribe" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
<!--</div> --> <!-- #page-content -->
