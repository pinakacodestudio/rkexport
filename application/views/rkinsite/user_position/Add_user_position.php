<script>
/* var memberid = '<?php //if(isset($userpositiondata)){ echo $userpositiondata['memberid']; }  ?>'; */
	
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($userpositiondata) && !isset($roletype)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-10 col-lg-10 col-lg-offset-1 col-md-offset-1">
					<form class="form-horizontal" id="formuserposition">
						<input type="hidden" name="olduserid" value="<?php if(isset($userpositiondata)){ echo $userpositiondata['userid']; } ?>">
						

						
								<div class="form-group" id="userid_div">
									<label class="control-label col-sm-3" for="userid">User <span class="mandatoryfield">*</span></label>
									<div class="col-sm-6">
										<select id="userid" name="userid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="1" <?php if(isset($userpositiondata)){ echo 'disabled';} ?>>
											<option value="0">Select User </option>
											<?php foreach($userdata as $userrow){ ?>
											<option value="<?php echo $userrow['id']; ?>" <?php if(isset($userpositiondata)){ if($userpositiondata['userid'] == $userrow['id']){ echo 'selected'; } }  ?>><?php echo $userrow['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>



								<div class="form-group" id="positionid_div">
									<label class="control-label col-sm-3" for="positionid">Position <span class="mandatoryfield">*</span> </label>
									<div class="col-sm-6">
										<?php $positionidarr=array();
										if(isset($userpositiondata)){
											$positionidarr = explode(",",$userpositiondata['positionid']);
											//print_r($userpositiondata);exit;
										} ?>
										<select class="form-control selectpicker" id="positionid" name="positionid[]" data-live-search="true" data-size="5"  multiple title="Select Position" data-actions-box="true"  tabindex="2">
										<?php foreach($positions as $key=>$value){ ?>
											<option value="<?php echo $key; ?>" <?php if(in_array($key,$positionidarr)){ echo 'selected'; } ?>><?php echo $value; ?></option>
											<?php  } ?>
										</select>
									</div>  
								</div>
								
								

						
								
					
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($userpositiondata) ){ ?>
									<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="button" id="submitnew" onclick="checkvalidation(2)" name="submit" value="UPDATE & NEW" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="button" id="submitnew" onclick="checkvalidation(2)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>user-position" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
