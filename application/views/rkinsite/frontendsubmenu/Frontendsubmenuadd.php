<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($frontendsubmenudata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
	        <ol class="breadcrumb">                        
	          <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
	          <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
	          <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
	          <li class="active"><?php if(isset($frontendsubmenudata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="frontendsubmenuform">
						<input type="hidden" name="frontendsubmenuid" value="<?php if(isset($frontendsubmenudata)){ echo $frontendsubmenudata['id']; } ?>">
						
						<div class="form-group" id="mainmenu_div">
							<label for="mediuminput" class="col-sm-3 control-label">Select Main Menu <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<select autofocus id="mainmenu" class="selectpicker form-control" name="mainmenu" data-live-search="true" data-size="5">
									<option value="0">Select Main Menu</option>
									<?php foreach($frontendmainmenudata as $row){ ?>
										<option value="<?php echo $row['id']; ?>" <?php if(isset($frontendsubmenudata)){ if($frontendsubmenudata['frontendmenuid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="form-group" id="menuname_div">
							<label for="focusedinput" class="col-sm-3 control-label">Sub Menu Name <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="name" type="text" name="name" value="<?php if(isset($frontendsubmenudata)){ echo $frontendsubmenudata['name']; } ?>" class="form-control" >
							</div>
						</div>
						<div class="form-group" id="menuurl_div">
							<label for="focusedinput" class="col-sm-3 control-label">Menu Url</label>
							<div class="col-sm-8">
								<input id="menuurl" type="text" name="menuurl" value="<?php if(isset($frontendsubmenudata)){ echo $frontendsubmenudata['menuurl']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="priority_div">
							<label class="col-sm-3 control-label">Priority</label>
							<div class="col-sm-8">
								<input id="priority" type="text" name="priority" value="<?php if(isset($frontendsubmenudata)){ echo $frontendsubmenudata['priority']; } ?>" class="form-control number" maxlength="3">
							</div>
						</div>
						
						<div class="form-group" id="image_div">
						  <label for="focusedinput" class="col-sm-3 control-label">Cover Image </label>
						  <div class="col-sm-8">
							<input type="hidden" name="oldcoverimage" id="oldcoverimage" value="<?php if(isset($frontendsubmenudata)){ echo $frontendsubmenudata['coverimage']; }?>">
							<input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
							<?php if(isset($frontendsubmenudata) && $frontendsubmenudata['coverimage']!=''){ ?>
							  <div class="imageupload" id="coverimg">
								  <div class="file-tab"><img src="<?php echo FRONTMENU_COVER_IMAGE.$frontendsubmenudata['coverimage']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
									  <label id="coverimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
										  <span id="coverimagebtn">Change</span>
										  <!-- The file is stored here. -->
										  <input type="file" name="coverimage" id="coverimage" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
									  </label>
									  <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
								  </div>
							  </div>
							  <?php }else{ ?>		            			
							  <div class="imageupload" id="coverimg">
								  <div class="file-tab">
										<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
										<label class="btn btn-sm btn-primary btn-raised btn-file">
											<span id="coverimagebtn">Select Image</span>
											<input type="file" name="coverimage" id="coverimage" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
										</label>
										<button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
								  </div>
							  </div>
							  <?php } ?>
						  </div>              
					  	</div>
					  	<div class="form-group">
							<label class="col-sm-3 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($frontendsubmenudata) && $frontendsubmenudata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2 col-xs-6">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($frontendsubmenudata) && $frontendsubmenudata['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(isset($frontendsubmenudata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
									<input type="button" id="submit" onclick="checkvalidation(0)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
									<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD" class="btn btn-primary btn-raised">				  
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>frontendsubmenu" title=<?=cancellink_title?>><?=cancellink_text?></a>
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