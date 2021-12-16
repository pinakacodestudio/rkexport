<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($mainmenurow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($mainmenurow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>                 
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-11 col-lg-11 col-lg-offset-1 col-md-offset-1">
					<form class="form-horizontal" id="formmainmenu">
						<input type="hidden" name="mainmenuid" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['id']; } ?>">
						<div class="form-group" id="menuname_div">
							<label for="name" class="col-sm-3 control-label">Main Menu Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="name" type="text" name="MainmenuName" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
							</div>
						</div>
						<div class="form-group" id="menuorder_div">
							<label for="inorder" class="col-sm-3 control-label">Menu Priority </label>
							<div class="col-sm-6">
								<input id="inorder" type="text" name="inorder" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['inorder']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
							
							</div>
						</div>
						<div class="form-group">
							<label for="menuicon" class="col-sm-3 control-label">Menu icon</label>
							<div class="col-sm-6">
								<input id="menuicon" type="text" name="menuicon" value='<?php if(isset($mainmenurow)){ echo $mainmenurow['icon']; } ?>' class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="menuurl" class="col-sm-3 control-label">Menu Url</label>
							<div class="col-sm-6">
								<input id="menuurl" type="text" name="menuurl" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['menuurl']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="rights_div">
							<label for="rightsid" class="col-sm-3 control-label">Additional Rights</label>
								<div class="col-sm-6">
								<select id="rightsid" name="rightsid[]" class="selectpicker form-control" data-live-search="true" data-size="5" multiple data-actions-box="true" title="Select Rights">
									<?php 
									if(isset($mainmenurow)){ 
										$additionalrightsid = explode(',',$mainmenurow['additionalrights']);
									}
									foreach($additionalrightsdata as $index=>$rights){ ?>
										<option value="<?php echo $rights['id']; ?>" <?php  if(isset($mainmenurow)){ if(in_array($rights['id'],$additionalrightsid)){ echo "selected"; } }?>><?php echo $rights['name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-8 p-n">
								<div class="col-sm-4">
									<div class="checkbox text-left">
									<input id="showinrole" name="showinrole" type="checkbox" <?php if(isset($mainmenurow) && $mainmenurow['showinrole']==0){ }else{ echo 'checked'; }?>>
									<label for="showinrole">Allow to show in Role</label>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="checkbox text-left">
									<input id="managelog" name="managelog" type="checkbox" <?php if(isset($mainmenurow) && $mainmenurow['managelog']==0){ }else{ echo 'checked'; }?>>
									<label for="managelog">Manage Log</label>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="checkbox text-left">
									<input id="approvallevel" name="approvallevel" type="checkbox" <?php if(isset($mainmenurow) && $mainmenurow['approvallevel']==1){ echo 'checked'; } ?>>
									<label for="approvallevel">Approval Level</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-6">
								<?php if(isset($mainmenurow)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>menu" title=<?=cancellink_title?>><?=cancellink_text?></a>
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