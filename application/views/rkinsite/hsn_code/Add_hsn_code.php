<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($hsncodedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($hsncodedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
						<form class="form-horizontal" id="hsncodeform">
							<input type="hidden" name="hsncodeid" value="<?php if(isset($hsncodedata)){ echo $hsncodedata['id']; } ?>">
							<!-- <div class="col-md-6"> -->
								<div class="form-group" id="hsncode_div">
									<label class="col-sm-4 control-label">HSN Code <span class="mandatoryfield">*</span></label>
									<div class="col-sm-4">
										<input id="hsncode" type="text" name="hsncode" value="<?php if(!empty($hsncodedata)){ echo $hsncodedata['hsncode']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="10">
									</div>
								</div>
							<!-- </div>
							<div class="col-md-6"> -->
								<div class="form-group" id="integratedtax_div">
									<label class="col-sm-4 control-label">Integrated Tax(%) <span class="mandatoryfield">*</span></label>
									<div class="col-sm-4">
										<input id="integratedtax" type="text" name="integratedtax" value="<?php if(!empty($hsncodedata)){ echo $hsncodedata['integratedtax']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value);">
									</div>
								</div>
								<div class="form-group" id="description_div">
									<label class="col-sm-4 control-label">Description</label>
									<div class="col-sm-4">
										<input id="description" type="text" name="description" value="<?php if(!empty($hsncodedata)){ echo $hsncodedata['description']; } ?>" class="form-control">
									</div>
								</div>
							<!-- </div>	 -->
							<div class="col-md-12">
								<hr>
								<div class="form-group">
									<label for="focusedinput" class="col-sm-5 control-label">Activate</label>
									<div class="col-sm-6">
										<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="yes" value="1" <?php if(isset($hsncodedata) && $hsncodedata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="yes">Yes</label>
											</div>
										</div>
										<div class="col-sm-2 col-xs-6">
											<div class="radio">
											<input type="radio" name="status" id="no" value="0" <?php if(isset($hsncodedata) && $hsncodedata['status']==0){ echo 'checked'; }?>>
											<label for="no">No</label>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group text-center">
									<div class="col-sm-12">
										<?php if(!empty($hsncodedata)){ ?>
											<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
											<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
										<?php }else{ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
										<?php } ?>
										<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>hsn-code" title=<?=cancellink_title?>><?=cancellink_text?></a>
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