<script>
    var PRODUCT_PATH='<?=PRODUCT?>'; 
</script>
<script type="text/javascript">
	var profileimgpath = '<?php echo PROFILE;?>';
	var defaultprofileimgpath = '<?php echo DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW;?>';
	var productid = '<?=(isset($homebannerdata))?$homebannerdata['productid']:''?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($homebannerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($homebannerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>                 
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 p-n">
					<form class="form-horizontal" id="homebannerform">
						<input type="hidden" name="homebanner_id" value="<?php if(isset($homebannerdata)){ echo $homebannerdata['id']; } ?>">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group" id="channelid_div">
									<label class="col-sm-4 control-label" for="channelid">Select Channel <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<select id="channelid" name="channelid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="Select Channel" data-live-search="true" data-actions-box="true" multiple>
										<?php 
											$channelidarr=array();
											if(isset($homebannerdata) && !empty($homebannerdata['channelid'])){
												$channelidarr = explode(",", $homebannerdata['channelid']);
											}
											foreach($channeldata as $cd){ 
												
											$selected = '';
											if(in_array($cd['id'], $channelidarr)){
												$selected = 'selected';
											}	
											?>
											<option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group" id="homebanner_div" style="display:none;">
									<label class="col-sm-4 control-label">Title <span class="mandatoryfield">*</span></label>
									<div class="col-sm-8">
										<input id="title" type="text" name="title" value="<?php if(!empty($homebannerdata)){ echo $homebannerdata['title']; } ?>" class="form-control">
									</div>
								</div>
								<div class="form-group" id="subtitle_div" style="display:none;">
									<label class="col-sm-4 control-label">Sub Title</label>
									<div class="col-sm-8">
										<input id="subtitle" type="text" name="subtitle" value="<?php if(!empty($homebannerdata)){ echo $homebannerdata['subtitle']; } ?>" class="form-control">
									</div>
								</div>
								<div class="form-group" id="link_div" style="display:none;">
									<label class="col-sm-4 control-label">URL</label>
									<div class="col-sm-8">
										<input id="urllink" type="text" name="urllink" value="<?php if(!empty($homebannerdata)){ echo $homebannerdata['urllink']; } ?>" class="form-control">
									</div>
								</div>
								<div class="form-group" id="product_div">
									<label for="countryid" class="col-sm-4 control-label">Product </label>
									<div class="col-sm-8">
										<select id="productid" name="productid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">
											<option value="0">Select Product</option>
											<?php /* foreach($productdata as $pd){ ?>
												<option value="<?php echo $pd['id']; ?>" <?php if(isset($homebannerdata)){ if($homebannerdata['productid'] == $pd['id']){ echo 'selected'; } } ?>><?php echo ucwords($pd['name']); ?></option>
											<?php } */ ?>
										</select>
									</div>
								</div>

								<div class="form-group" id="sliderorder_div">
									<label for="mediuminput" class="col-sm-4 control-label">Priority</label>
									<div class="col-sm-8">
										<input id="sort_order" type="text" name="sort_order" value="<?php if(isset($homebannerdata)){ echo $homebannerdata['inorder']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
									</div>
								</div>
								<div class="form-group" id="sliderorder_div" style="display: none">
									<label for="mediuminput" class="col-sm-4 control-label">Display Duration<br/>(In seconds)</label>
									<div class="col-sm-8">
										<input id="displayduration" type="text" name="displayduration" value="<?php if(isset($homebannerdata)){ echo $homebannerdata['displayduration']; } ?>" class="form-control" onkeypress="return isNumber(event)">
									</div>
								</div>
								<div class="form-group">
									<label for="focusedinput" class="col-sm-4 control-label">Activate</label>
									<div class="col-sm-8">
										<div class="col-sm-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="yes" value="1" <?php if(isset($homebannerdata) && $homebannerdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="yes">Yes</label>
											</div>
										</div>
										<div class="col-sm-4 col-xs-4" style="padding-left: 0px;">
											<div class="radio">
											<input type="radio" name="status" id="no" value="0" <?php if(isset($homebannerdata) && $homebannerdata['status']==0){ echo 'checked'; }?>>
											<label for="no">No</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="focusedinput" class="col-md-3 control-label">Image  <span class="mandatoryfield">* </span></label>
									<div class="col-md-8">
										<input type="hidden" name="oldprofileimage" id="oldprofileimage" value="<?php if(isset($homebannerdata)){ echo $homebannerdata['image']; }?>">
										<input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
										<?php if(isset($homebannerdata) && $homebannerdata['image']!=''){ ?>
											<div class="imageupload" id="profileimage">
													<div class="file-tab"><img src="<?php echo HOMEBANNER.$homebannerdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
															<label id="profileimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
																	<span id="profileimagebtn">Change</span>
																	<!-- The file is stored here. -->
																	<input type="file" name="profile_image" id="profile_image">
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
																	<input type="file" name="profile_image" id="profile_image">
															</label>
															<button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
													</div>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
						
						
						<div class="form-group text-center">
								<?php if(!empty($homebannerdata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>home-banner" title=<?=cancellink_title?>><?=cancellink_text?></a>
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