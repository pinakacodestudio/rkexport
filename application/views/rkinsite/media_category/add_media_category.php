<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($mediacategorydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?=ADMIN_URL?>media-category"><?=$this->session->userdata(base_url().'submenuname')?></a></li>              
              <li class="active"><?php if(isset($mediacategorydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		    </small>
    </div>
	<div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-1 col-md-offset-1">
					<form class="form-horizontal" id="mediacategoryform">
						<input type="hidden" name="mediacategoryid" value="<?php if(isset($mediacategorydata)){ echo $mediacategorydata['id']; } ?>">						
						<div class="form-group" id="name_div">
							<label for="name" class="col-sm-4 control-label">Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input autofocus id="name" type="text" name="name" value="<?php if(!empty($mediacategorydata)){ echo $mediacategorydata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
							</div>
						</div>											
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($mediacategorydata) && $mediacategorydata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($mediacategorydata) && $mediacategorydata['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($mediacategorydata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
									<input type="button" id="submit" onclick="checkvalidation(0)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
									<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  	<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>media-category" title=<?=cancellink_title?>><?=cancellink_text?></a>
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