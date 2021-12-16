<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($countrydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($countrydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3">
						<form class="form-horizontal" id="countryform" name="countryform">
							<input type="hidden" name="countryid" value="<?php if(isset($countrydata)){ echo $countrydata['id']; } ?>">
							<div class="form-group" id="name_div">
								<label class="col-sm-3 control-label" for="name">Country Name <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="name" type="text" name="name" value="<?php if(!empty($countrydata)){ echo $countrydata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
								</div>
							</div>
							<div class="form-group" id="sortname_div">
								<label class="col-sm-3 control-label" for="sortname">Sort Name <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="sortname" type="text" name="sortname" value="<?php if(!empty($countrydata)){ echo $countrydata['sortname']; } ?>" maxlength="5" class="form-control" onkeypress="return onlyAlphabets(event)">
								</div>
							</div>
							<div class="form-group" id="phonecode_div">
								<label class="col-sm-3 control-label" for="phonecode">Phone Code <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="phonecode" type="text" name="phonecode" value="<?php if(!empty($countrydata)){ echo $countrydata['phonecode']; } ?>" maxlength="5" class="form-control" onkeypress="return isPhonecode(event)">
								</div>
							</div>
							<div class="form-group">
								<label for="focusedinput" class="col-sm-3 control-label"></label>
								<div class="col-sm-8">
									<?php if(!empty($countrydata)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php }else{ ?>
									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php } ?>
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>country" title=<?=cancellink_title?>><?=cancellink_text?></a>
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