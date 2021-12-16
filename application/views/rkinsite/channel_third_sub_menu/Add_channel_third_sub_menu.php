<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($channelthirdsubmenurow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($channelthirdsubmenurow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
					<form class="form-horizontal" id="formchannelsubmenu">
						<input type="hidden" name="channelthirdsubmenuid" value="<?php if(isset($channelthirdsubmenurow)){ echo $channelthirdsubmenurow['id']; } ?>">
						
						<div class="form-group" id="submenu_div">
							<label for="submenu" class="col-sm-4 control-label">Select Sub Menu <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<select id="submenu" class="selectpicker form-control" name="submenuid" data-live-search="true" data-select-on-tab="true" data-size="8">
									<option value="0">Select Sub Menu</option>
									<?php foreach($submenudata as $row){
                                        if($row['url']==''){ ?>
										<option value="<?php echo $row['id']; ?>" <?php if(isset($channelthirdsubmenurow)){ if($channelthirdsubmenurow['channelsubmenuid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']." (".$row['mainmenuname'].")"; ?></option>
									<?php } } ?>
								</select>
							</div>
						</div>
						<div class="form-group" id="menuname_div">
							<label for="name" class="col-sm-4 control-label">Third Sub Menu Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="name" type="text" name="SubmenuName" value="<?php if(isset($channelthirdsubmenurow)){ echo $channelthirdsubmenurow['name']; } ?>" class="form-control" onkeypress="return alphanumericspaces(event)">
							</div>
						</div>
						<div class="form-group" id="menuorder_div">
							<label for="inorder" class="col-sm-4 control-label">Menu Priority </label>
							<div class="col-sm-6">
								<input id="inorder" type="text" name="inorder" value="<?php if(isset($channelthirdsubmenurow)){ echo $channelthirdsubmenurow['inorder']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
							</div>
						</div>
						<div class="form-group" id="menuurl_div">
							<label for="menuurl" class="col-sm-4 control-label">Menu Url <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="menuurl" type="text" name="menuurl" value="<?php if(isset($channelthirdsubmenurow)){ echo $channelthirdsubmenurow['url']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="showinrole" class="col-sm-4 control-label"></label>
							<div class="col-sm-6">
								<div class="checkbox text-left">
		                          <input id="showinrole" name="showinrole"  type="checkbox" <?php if(isset($channelthirdsubmenurow) && $channelthirdsubmenurow['showinrole']==0){ }else{ echo 'checked'; }?>>
		                          <label for="showinrole">Allow in <?=Member_label?> Role</label>
		                        </div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-8">
								<?php if(isset($channelthirdsubmenurow)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>channel-third-sub-menu" title=<?=cancellink_title?>><?=cancellink_text?></a>
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