<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($ratingstatusdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($ratingstatusdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
						<form class="form-horizontal" id="memberratingstatusform" name="memberratingstatusform">
							<input type="hidden" name="memberratingstatusid" value="<?php if(isset($ratingstatusdata)){ echo $ratingstatusdata['id']; } ?>">
                           
                            <div class="form-group" id="name_div">
                                <label for="name" class="col-sm-3 control-label">Name <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <input id="name" type="text" name="name" value="<?php if(!empty($ratingstatusdata)){ echo $ratingstatusdata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
                                </div>
                            </div>
                        
                            <div class="form-group" id="color_div">
                                <label class="col-md-3 control-label col-form-label" for="color" style="padding-top: 0;margin-top: 3px;">Color <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                    <input type="text" id="color" class="form-control demo" name="color" value="<?php if(!empty($ratingstatusdata)){ echo $ratingstatusdata['color']; }else{ echo '#70c24a'; } ?>">
                                </div>
                            </div>
                    
                            <div class="form-group text-center">
                                <label for="focusedinput" class="col-sm-5 col-xs-4col-md-4 control-label">Activate</label>
                                <div class="col-sm-6 col-xs-8">
                                    <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                        <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if(isset($ratingstatusdata) && $ratingstatusdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                        <label for="yes">Yes</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                        <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if(isset($ratingstatusdata) && $ratingstatusdata['status']==0){ echo 'checked'; }?>>
                                        <label for="no">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
							<div class="form-group text-center">
                                <?php if(!empty($ratingstatusdata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>member-rating-status" title=<?=cancellink_title?>><?=cancellink_text?></a>
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