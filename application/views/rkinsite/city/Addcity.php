<script type="text/javascript">
	var countryid = <?php if(isset($citydata)){ echo $citydata['countryid'];}else { echo '0'; }?>;
	var provinceid = <?php if(isset($citydata)){ echo $citydata['stateid'];}else { echo '0'; }?>;
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($citydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($citydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
						<form class="form-horizontal" id="cityform" name="cityform">
							<input type="hidden" name="cityid" value="<?php if(isset($citydata)){ echo $citydata['id']; } ?>">
							<div class="form-group" id="country_div">
								<label for="countryid" class="col-sm-3 control-label">Country <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">
										<option value="0">Select Country</option>
										<?php foreach($countrydata as $countryrow){ ?>
											<option value="<?php echo $countryrow['id']; ?>" <?php if(isset($citydata)){ if($citydata['countryid'] == $countryrow['id']){ echo 'selected'; } } ?>><?php echo $countryrow['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group" id="province_div">
								<label for="provinceid" class="col-sm-3 control-label">Province <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" readonly tabindex="9">
										<option value="0">Select Province</option>
									</select>
								</div>
							</div>
							<div class="form-group" id="city_div">
								<label for="name" class="col-sm-3 control-label">City Name <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="name" type="text" name="name" value="<?php if(!empty($citydata)){ echo $citydata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
								</div>
							</div>
							<div class="form-group">
								<label for="focusedinput" class="col-sm-3 control-label"></label>
								<div class="col-sm-8">
									<?php if(!empty($citydata)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php }else{ ?>
									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php } ?>
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>city" title=<?=cancellink_title?>><?=cancellink_text?></a>
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