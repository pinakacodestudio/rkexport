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
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
					<form class="form-horizontal" id="formmainmenu">
						<input type="hidden" name="mainmenuid" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['id']; } ?>">
						<div class="form-group" id="menuname_div">
							<label for="name" class="col-sm-3 control-label">Main Menu Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="name" type="text" name="MainmenuName" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
							</div>
						</div>
						<div class="form-group" id="menuorder_div">
							<label for="inorder" class="col-sm-3 control-label">Menu Priority </label>
							<div class="col-sm-8">
								<input id="inorder" type="text" name="inorder" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['inorder']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
							
							</div>
						</div>
						<div class="form-group">
							<label for="menuicon" class="col-sm-3 control-label">Menu icon</label>
							<div class="col-sm-8">
								<input id="menuicon" type="text" name="menuicon" value='<?php if(isset($mainmenurow)){ echo $mainmenurow['icon']; } ?>' class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="menuurl" class="col-sm-3 control-label">Menu Url</label>
							<div class="col-sm-8">
								<input id="menuurl" type="text" name="menuurl" value="<?php if(isset($mainmenurow)){ echo $mainmenurow['menuurl']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="showinrole" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<div class="checkbox text-left">
		                          <input id="showinrole" name="showinrole"  type="checkbox" <?php if(isset($mainmenurow) && $mainmenurow['showinrole']==0){ }else{ echo 'checked'; }?>>
		                          <label for="showinrole">Allow in <?=Member_label?> Role</label>
		                        </div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(isset($mainmenurow)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>channel-main-menu" title=<?=cancellink_title?>><?=cancellink_text?></a>
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