<script type="text/javascript">	
	var photogalleryimgpath = '<?php echo PHOTOGALLERY;?>';
	var defaultphotogalleryimgpath = '<?php echo DEFAULT_PHOTOGALLERY;?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($photogallerydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?=ADMIN_URL?>photo-gallery"><?=$this->session->userdata(base_url().'submenuname')?></a></li>              
              <li class="active"><?php if(isset($photogallerydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		    </small>
    </div>
	<div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12" >
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12">
      					<form class="form-horizontal" id="photogalleryform">
						  <input type="hidden" name="photogalleryid" value="<?php if(isset($photogallerydata)){ echo $photogallerydata['id']; } ?>">						  
						  <div class="col-sm-8">    
						  	<div class="col-sm-12">  
						  		<div class="form-group" id="title_div">
						    			<label for ="title" class="col-sm-3 control-label">Title <span class="mandatoryfield">*</span></label>
						    	    	<div class="col-sm-8">
						    	      		<input autofocus id="title" type="text" name="title" value="<?php if(!empty($photogallerydata)){ echo $photogallerydata['title']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
						    	    	</div>
						    		</div>      													
						  		</div>
								<div class="col-sm-12">	
									<div class="form-group" id="mediacategoryid_div">
										<label for="mediacategoryid" class="col-sm-3 control-label">Media Category <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<select id="mediacategoryid" class="selectpicker form-control" name="mediacategoryid[]" multiple data-live-search="true" data-select-on-tab="true" data-size="5" title="Select Media Category" >												
											<?php foreach($mediacategorydata as $row){ ?>                            
                            					<option value="<?php echo $row['id']; ?>" <?php if(isset($photogallerydata)){ if($photogallerydata['mediacategoryid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
                          					<?php } ?>								
											</select>
										</div>
									</div>						  
								</div>			
								<div class="col-sm-12">
						  			<div class="form-group" id="alttag_div">
										<label for="alttag" class="col-sm-3 control-label">Alternative Tag</label>
										<div class="col-sm-8">
											<input id="alttag" type="text" name="alttag" value="<?php if(isset($photogallerydata)){ echo $photogallerydata['alttag']; } ?>" class="form-control number">
										</div>
									</div>
								</div>			  														  
								<div class="col-sm-12">
						  			<div class="form-group" id="priority_div">
										<label for="priority" class="col-sm-3 control-label">Priority</label>
										<div class="col-sm-8">
											<input id="priority" type="text" name="priority" value="<?php if(isset($photogallerydata)){ echo $photogallerydata['priority']; } ?>" class="form-control number" onkeypress="return isNumber(event)" maxlength="3">
										</div>
									</div>
								</div>
							</div>
							
							<div class="col-sm-4">
								<div class="col-sm-12">
									<div class="form-group" id="oldphotogalleryimage_div">
										<label for="focusedinput" class="col-sm-4 control-label">Image <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
              									<input type="hidden" name="oldphotogalleryimage" id="oldphotogalleryimage" value="<?php if(isset($photogallerydata)){ echo $photogallerydata['image']; }?>">
													<input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
													<?php if(isset($photogallerydata) && $photogallerydata['image']!=''){ ?>
		            									<div class="imageupload" id="photogalleryimage">
											  				<div class="file-tab"><img src="<?php echo PHOTOGALLERY.$photogallerydata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
												  				<label id="photogalleryimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
													  				<span id="imagebtn">Change</span>
											                        <!-- The file is stored here. -->
											                        <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
											                    </label>
											                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
											                </div>
		            									</div>
		            								<?php }else{ ?>		            			
		            							<div class="imageupload">
											        <div class="file-tab">
											          	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
											              <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
											                  <span id="imagebtn">Select Image</span>
											                  <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
											              </label>
											              <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
											        </div>
		            							</div>
		            					<?php } ?>
										</div>
									</div> 		          									  			
								</div> 
							</div>       							      						
							<div class="col-sm-12">
					        	<div class="form-group">
					        		<label for="focusedinput" class="col-sm-5 control-label">Activate</label>
					        		<div class="col-sm-6">
					        			<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
					        				<div class="radio">
					        				<input type="radio" name="status" id="yes" value="1" <?php if(isset($photogallerydata) && $photogallerydata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
					        				<label for="yes">Yes</label>
					        				</div>
					        			</div>
					        			<div class="col-sm-2 col-xs-6">
					        				<div class="radio">
					        				<input type="radio" name="status" id="no" value="0" <?php if(isset($photogallerydata) && $photogallerydata['status']==0){ echo 'checked'; }?>>
					        				<label for="no">No</label>
					        				</div>
					        			</div>
					        		</div>
					        	</div>
					        	<div class="form-group">
					        		<label for="focusedinput" class="col-sm-4 control-label"></label>
					        		<div class="col-sm-4">
					        			<?php if(!empty($photogallerydata)){ ?>
					        				<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
					        				<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
					        			<?php }else{ ?>
					        				<input type="button" id="submit" onclick="checkvalidation(0)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
					        				<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD" class="btn btn-primary btn-raised">
					        			  	<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
					        			<?php } ?>
					        			<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>photo-gallery" title=<?=cancellink_title?>><?=cancellink_text?></a>
					        		</div>
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
<script>
	$(document).ready(function(e){
		<?php if(isset($photogallerydata)){ ?>
		$('#mediacategoryid').val([<?php echo $photogallerydata['mediacategoryid'];?>]);
		$('#mediacategoryid').selectpicker('refresh');
	<? } ?>
	})
</script>