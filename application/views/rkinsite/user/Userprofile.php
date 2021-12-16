<script type="text/javascript">
	var profileimgpath = '<?php echo PROFILE;?>';
	var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE;?>';
</script>
<div class="page-content">
    <ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=base_url().$row['url']; ?>"><?=$row['name']; ?></a></li>
          <?php } } ?>
    </ol>
    <div class="page-heading">            
        <h1>User Profile</h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
					<form action="#" id="userform" class="form-horizontal">
						<input type="hidden" name="userid" value="<?php if(isset($userdata)){ echo $userdata['id']; } ?>">
						<div class="col-md-12 p-n">
								<div class="form-group row" id="firstname_div">
									<label class="control-label col-md-3" for="name">Name <span class="mandatoryfield">*</span></label>
									<div class="col-md-4">
										<input id="name" class="form-control" name="name" value="<?php if(isset($userdata)){ echo $userdata['name']; } ?>" type="text" tabindex="1" onkeypress="return onlyAlphabets(event)">
									</div>
								</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="form-group row" id="email_div">
								<label class="control-label col-md-3" for="email">Email <span class="mandatoryfield">*</span></label>
								<div class="col-md-4">
									<input id="email" type="text" name="email" value="<?php if(isset($userdata)){ echo $userdata['email']; } ?>" class="form-control" tabindex="6">
								</div>
							</div>
						</div>
						<div class="col-md-12 p-n">
								<div class="form-group row" id="mobile_div">
									<label class="control-label col-md-3" for="mobileno">Mobile No <span class="mandatoryfield">*</span></label>	
									<div class="col-md-4">
										<input id="mobileno" type="text" name="mobileno" value="<?php if(isset($userdata)){ echo $userdata['mobileno']; } ?>" class="form-control" maxlength="12"  onkeypress="return isNumber(event)" tabindex="8">
									</div>
								</div>
						</div>
						<div class="col-md-12 p-n">
							<div class="form-group row">
								<label for="focusedinput" class="col-md-3 control-label">Profile Image</label>
								<div class="col-md-8">
									<input type="hidden" name="oldprofileimage" id="oldprofileimage" value="<?php if(isset($userdata)){ echo $userdata['image']; }?>">
									<input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
									<?php if(isset($userdata) && $userdata['image']!=''){ ?>
	            						<div class="imageupload" id="profileimage">
							                <div class="file-tab"><img src="<?php echo PROFILE.$userdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
							                    <label id="profileimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
							                        <span id="profileimagebtn">Change</span>
							                        <!-- The file is stored here. -->
							                        <input type="file" name="image" id="image"  accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
							                    </label>
							                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
							                </div>
	            						</div>
	            					<?php }else{ ?>
	            						<!-- <script type="text/javascript"> var ACTION = 0;</script> -->
	            						<div class="imageupload">
							                <div class="file-tab">
							                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
							                    <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
							                        <span id="profileimagebtn">Select Image</span>
							                        <input type="file" name="image" id="image"  accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
							                    </label>
							                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
							                </div>
	            						</div>
	            					<?php } ?>
								</div>
							</div>
						</div>
						
						<div class="col-md-12" style="text-align: center;">
							<div class="form-group">
								<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
								<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
							</div>
						</div>
					</form>
				</div>
		      </div>
		    </div>
		  </div>
		</div>
		
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
