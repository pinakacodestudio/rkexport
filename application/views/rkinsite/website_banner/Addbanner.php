<script type="text/javascript">
	var BANNER_IMAGE_URL = '<?=BANNER?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($bannerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($bannerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="bannerform">
						<input type="hidden" name="bannerid" value="<?php if(isset($bannerdata)){ echo $bannerdata['id']; } ?>">
						<div class="form-group" id="title_div">
							<label class="col-sm-3 control-label">Banner Title <span class="mandatoryfield">*</span></label>
							<div class="col-sm-9">
								<input id="title" type="text" name="title" value="<?php if(!empty($bannerdata)){ echo $bannerdata['title']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">Banner Type</label>
							<div class="col-sm-8">
								<div class="col-sm-3 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="bannerfiletype" id="imagefile1" value="1" <?php if(isset($bannerdata)){ if($bannerdata['type']==1){ echo 'checked';} }else{ echo 'checked'; }?> onclick="filetype(1)">
									<label for="imagefile1">Image</label>
									</div>
								</div>
								<div class="col-sm-3 col-xs-6">
									<div class="radio">
									<input type="radio" name="bannerfiletype" id="videofile1" value="2" <?php if(isset($bannerdata)){ if($bannerdata['type']==2){ echo 'checked';} }?> onclick="filetype(1,2)">
									<label for="videofile1">Video</label>
									</div>
								</div>
								<div class="col-sm-3 col-xs-6">
									<div class="radio">
									<input type="radio" name="bannerfiletype" id="youtubefile1" value="3" <?php if(isset($bannerdata)){ if($bannerdata['type']==3){ echo 'checked';} }?> onclick="filetype(1,3)">
									<label for="youtubefile1">Youtube</label>
									</div>
								</div>
							</div>
						</div>
						
	                  	<div class="form-group" id="bannerfile_div">               
	                    	<label for="focusedinput" class="col-sm-3 control-label">Banner <span class="mandatoryfield">*</span></label></label>
	                    	<div class="col-sm-9">
	                    		<?php if(isset($bannerdata)){ ?>
	                    			<input type="hidden" name="oldbanner" value="<?=($bannerdata['type']==1 || $bannerdata['type']==2)?$bannerdata['file']:''?>">
		                    		<div class="input-group" id="fileupload" style="display:<?=($bannerdata['type']==1 || $bannerdata['type']==2)?'table':'none'?>;">
										<span class="input-group-btn" style="padding: 0 0px 0px 0px;">
											<span class="btn btn-primary btn-raised btn-file">Browse...
												<input type="file" name="bannerfile" id="bannerfile" onchange="validfile($(this))">
											</span>
										</span>
										<input type="text" readonly="" id="Filetext" class="form-control" name="Filetext" value="<?=$bannerdata['file']?>">
									</div>
									<div id="youtube" style="display:<?=($bannerdata['type']==3)?'block':'none'?>;">
										<input type="text" id="youtubeurl" class="form-control" name="youtubeurl" placeholder="Youtube URL" value="<?=($bannerdata['type']==3)?urldecode($bannerdata['file']):''?>;">
									</div>
								<?php }else{ ?>
									<div class="input-group" id="fileupload">
										<span class="input-group-btn" style="padding: 0 0px 0px 0px;">
											<span class="btn btn-primary btn-raised btn-file">Browse...
												<input type="file" name="bannerfile" id="bannerfile" onchange="validfile($(this))">
											</span>
										</span>
										<input type="text" readonly="" id="Filetext" class="form-control" name="Filetext" value="">
									</div>
									<div id="youtube" style="display: none;">
										<input type="text" id="youtubeurl" class="form-control" name="youtubeurl" placeholder="Youtube URL">
									</div>
								<? } ?>
	                      		<?php /*if(isset($bannerdata)){ ?>
						            <input type="hidden" name="oldbanner" id="oldbanner" value="<?php echo $bannerdata['image']; ?>">
            						<div class="imageupload" id="bannerimg">
						                <div class="file-tab"><img src="<?php echo BANNER.$bannerdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
						                	
						                    <label id="faviconlabel" class="btn btn-sm btn-primary btn-raised btn-file">
						                        <span id="imagebtn">Change</span>
						                        <!-- The file is stored here. -->
						                        <input type="file" name="image" id="image">
						                    </label>
						                    <button type="button" class="btn btn-sm btn-danger btn-raised" style="display: inline-block;">Remove</button>
						                </div>
            						</div>
            					<?php }else{ ?>
            						<div class="imageupload" id="bannerimg">
						                <div class="file-tab">
						                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
						                    <label class="btn btn-sm btn-primary btn-raised btn-file">
						                        <span id="imagebtn">Select Image</span>
						                        <input type="file" name="image" id="image" value="">
						                    </label>
						                    <button type="button" class="btn btn-sm btn-danger btn-raised">Remove</button>
						                </div>
            						</div>
            					<?php }*/ ?>
	                    	</div>
	                  	</div>
						<div class="form-group" id="alttext_div" style="display:<?=(isset($bannerdata) && $bannerdata['type']!=1)?'none':'block'?>;">
							<label class="col-sm-3 control-label">Alt Tag <span class="mandatoryfield">*</span></label>
							<div class="col-sm-9">
								<input id="alttext" type="text" name="alttext" value="<?php if(!empty($bannerdata)){ echo $bannerdata['alttext']; } ?>" class="form-control">
							</div>
						</div>
	                  	<div class="form-group" id="description_div">               
	                    	<label for="focusedinput" class="col-sm-3 control-label">Description</label></label>
	                    	<div class="col-sm-9">
	                      		<?php $data['controlname']="description";if(isset($bannerdata) && !empty($bannerdata)){$data['controldata']=$bannerdata['description'];} ?>
	                      		<?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
	                    	</div>
	                  	</div>	                  	
						<div class="form-group" id="button_div">
							<label class="col-sm-3 control-label">Button Name</label>
							<div class="col-sm-9">
								<input id="buttontext" type="text" name="buttontext" value="<?php if(!empty($bannerdata)){ echo $bannerdata['buttontext']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="link_div">
							<label class="col-sm-3 control-label">Banner Link</label>
							<div class="col-sm-9">
								<input id="link" type="text" name="link" value="<?php if(!empty($bannerdata)){ echo urldecode($bannerdata['link']); } ?>" class="form-control">
							</div>
						</div>					
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($bannerdata) && $bannerdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2 col-xs-6">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($bannerdata) && $bannerdata['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($bannerdata)){ ?>
								 	<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised"></a>
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>website_banner" title=<?=cancellink_title?>><?=cancellink_text?></a>
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