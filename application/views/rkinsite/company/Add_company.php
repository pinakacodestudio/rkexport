<script>
  var MODALVIEW = '<?php if(!empty($modalview)){ echo 1; }else{ echo 0; } ?>';
</script>
<div class="page-content">
<?php if(empty($modalview)){ ?>
    <div class="page-heading">            
        <h1><?php if(isset($additionalrightsrow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($additionalrightsrow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>
	<?php } ?>
	<?php if(empty($modalview)){ ?>
    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
					<?php } ?>
					<form class="form-horizontal" id="addcompanyform">
					
						<input type="hidden" name="id" value="<?php if(isset($companydata)){ echo $companydata['id']; } ?>">
						
						<div class="form-group" id="company_div">
							<label for="company" class="col-sm-4 control-label">Company Name<span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="companyname" type="text" name="companyname" value="<?php if(!empty($companydata)){ echo $companydata['companyname']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="email_div">
							<label for="email" class="col-sm-4 control-label">Email<span class="mandatoryfield"></span></label>
							<div class="col-sm-6">
								<input id="emailid" type="text" name="email" value="<?php if(!empty($companydata)){ echo $companydata['email']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-6">
								<?php if(isset($additionalrightsrow)){ ?>
									<input type="button" id="submit" onclick="checkvalidationcompany()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidationcompany()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                    <input type="button" id="submit" onclick="checkvalidationcompany(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php } ?>
								<?php if(empty($modalview)){ ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
								<?php } ?>
							</div>
						</div>
						
					</form>
					<?php if(empty($modalview)){ ?>
				</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
	<?php } ?>
</div> <!-- #page-content -->