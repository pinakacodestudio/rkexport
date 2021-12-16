<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($testimonialsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?=ADMIN_URL?>testimonials"><?=$this->session->userdata(base_url().'submenuname')?></a></li>              
              <li class="active"><?php if(isset($testimonialsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>
    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		      <div class="row">
		        <div class="col-md-12">
		          <div class="panel panel-default border-panel">
		            <div class="panel-body">
					        <form action="#" id="testimonialsform" class="form-horizontal">
						      <input type="hidden" name="testimonialsid" value="<?php if(isset($testimonialsdata)){ echo $testimonialsdata['id']; } ?>">
						      <div class="col-md-12 p-n col-lg-offset-1 col-md-offset-1">
							      <div class="form-group row" id="name_div">
							      	<label class="control-label col-md-3" for="name">Name </label>
								      <div class="col-md-4">
									      <input autofocus id="name" class="form-control" name="name" value="<?php if(isset($testimonialsdata)){ echo $testimonialsdata['name']; } ?>" type="text" onkeypress="return onlyAlphabets(event)">
								      </div>
							      </div>
						      </div>						
						      <div class="col-md-12 p-n col-lg-offset-1 col-md-offset-1">
						      	<div class="form-group row" id="testimonials_div">
						      		<label class="control-label col-md-3" for="testimonials">Testimonials <span class="mandatoryfield">*</span></label>	
						      		<div class="col-md-4">
                      					<textarea id="testimonials" name="testimonials" class="form-control"><?php if(isset($testimonialsdata)){ echo $testimonialsdata['testimonials']; } ?></textarea>
						      		</div>
						      	</div>
						      </div>            
						      <div class="col-md-12 p-n col-lg-offset-1 col-md-offset-1">
						      	<div class="form-group ">
						      		<label for="focusedinput" class="col-md-3 control-label">Upload Image</label>
						      		<div class="col-md-8">
						      			<input type="hidden" name="oldtestimonialsimage" id="oldtestimonialsimage" value="<?php if(isset($testimonialsdata)){ echo $testimonialsdata['image']; }?>">
						      			<input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
						      			<?php if(isset($testimonialsdata) && $testimonialsdata['image']!=''){ ?>
	                  						<div class="imageupload" id="testimonialsimage">
						      	                <div class="file-tab"><img src="<?php echo TESTIMONIALS.$testimonialsdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
						      	                    <label id="testimonialsimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
						      	                        <span id="testimonialsimagebtn">Change</span>
						      	                        <!-- The file is stored here. -->
						      	                        <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
						      	                    </label>
						      	                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
						      	                </div>
	                  						</div>
	                  					<?php }else{ ?>
	                  						<!-- <script type="text/javascript"> var ACTION = 0;</script> -->
	                  						<div class="imageupload">
						      	                <div class="file-tab">
						      	                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
						      	                    <label for="image" id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
						      	                        <span id="testimonialsimagebtn">Select Image</span>
						      	                        <input type="file" name="image" id="image"  accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
						      	                    </label>
						      	                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
						      	                </div>
	                  						</div>
	                  					<?php } ?>
						      		</div>
						      	</div>
						      </div>	
                  			  <div class="col-sm-12 col-md-12 col-lg-12 col-lg-offset-1 col-md-offset-1">
					        	<div class="form-group">
					        		<label for="status" class="col-sm-4 control-label">Activate</label>
					        		<div class="col-sm-8">
					        			<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
					        				<div class="radio">
					        				<input type="radio" name="status" id="yes" value="1" <?php if(isset($testimonialsdata) && $testimonialsdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
					        				<label for="yes">Yes</label>
					        				</div>
					        			</div>
					        			<div class="col-sm-2 col-xs-6">
					        				<div class="radio">
					        				<input type="radio" name="status" id="no" value="0" <?php if(isset($testimonialsdata) && $testimonialsdata['status']==0){ echo 'checked'; }?>>
					        				<label for="no">No</label>
					        				</div>
					        			</div>
					        		</div>
					        	</div>
					        	<div class="form-group">
							        <label for="focusedinput" class="col-sm-3 control-label"></label>
							        <div class="col-sm-8">
                        			<?php if(isset($testimonialsdata)){ ?>
						              <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
						              <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
						              	<?php }else{ ?>
									  <input type="button" id="submit" onclick="checkvalidation(0)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
									  <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD" class="btn btn-primary btn-raised">
						              <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
						              		<?php } ?>
						              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>testimonials" title=<?=cancellink_title?>><?=cancellink_text?></a>
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

