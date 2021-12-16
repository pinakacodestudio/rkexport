<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($videogallerydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?=ADMIN_URL?>video-gallery"><?=$this->session->userdata(base_url().'submenuname')?></a></li>              
              <li class="active"><?php if(isset($videogallerydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
      					<form class="form-horizontal" id="videogalleryform">
						  <input type="hidden" name="videogalleryid" value="<?php if(isset($videogallerydata)){ echo $videogallerydata['id']; } ?>">						  
						  <div class="col-sm-10">    
						  	<div class="col-sm-12">  
						  		<div class="form-group" id="title_div">
						    			<label for ="title" class="col-sm-4 control-label">Title <span class="mandatoryfield">*</span></label>
						    	    	<div class="col-sm-8">
						    	      		<input autofocus id="title" type="text" name="title" value="<?php if(!empty($videogallerydata)){ echo $videogallerydata['title']; } ?>" class="form-control">
						    	    	</div>
						    		</div>      													
								  </div>
								  <div class="col-sm-12">	
									<div class="form-group" id="mediacategoryid_div">
										<label for="mediacategoryid" class="col-sm-4 control-label">Media Category <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
										<select id="mediacategoryid" class="selectpicker form-control" name="mediacategoryid[]" multiple data-live-search="true" data-select-on-tab="true" data-size="5" title="Select Media Category" >												
											<?php foreach($mediacategorydata as $row){ ?> 												        										                          
                            					<option value="<?php echo $row['id']; ?>" <?php if(isset($videogallerydata)){ if($videogallerydata['mediacategoryid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
                          					<?php } ?>								
											</select>
										</div>
									</div>						  
								</div>
								<div class="col-sm-12">
									<div class="form-group" id="url_div">
										<label for="url" class="col-sm-4 control-label">Youtube URL <span class="mandatoryfield">*</span></label>
										<div class="col-sm-8">
											<input id="url" type="text" name="url" value="<?php if(!empty($videogallerydata)){ echo urldecode($videogallerydata['url']); } ?>" class="form-control">
										</div>
									</div>
								</div>													  
								<div class="col-sm-12">
						  			<div class="form-group" id="priority_div">
										<label for="priority" class="col-sm-4 control-label">Priority</label>
										<div class="col-sm-8">
											<input id="priority" type="text" name="priority" value="<?php if(isset($videogallerydata)){ echo $videogallerydata['priority']; } ?>" class="form-control number" onkeypress="return isNumber(event)" maxlength="3">
										</div>
									</div>
								</div>
							</div>													      							      						
							<div class="col-sm-10">
					        	<div class="form-group">
					        		<label for="focusedinput" class="col-sm-4 control-label">Activate</label>
					        		<div class="col-sm-6">
					        			<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
					        				<div class="radio">
					        				<input type="radio" name="status" id="yes" value="1" <?php if(isset($videogallerydata) && $videogallerydata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
					        				<label for="yes">Yes</label>
					        				</div>
					        			</div>
					        			<div class="col-sm-2 col-xs-6">
					        				<div class="radio">
					        				<input type="radio" name="status" id="no" value="0" <?php if(isset($videogallerydata) && $videogallerydata['status']==0){ echo 'checked'; }?>>
					        				<label for="no">No</label>
					        				</div>
					        			</div>
					        		</div>
					        	</div>
					        	<div class="form-group">
					        		<label for="focusedinput" class="col-sm-4 control-label"></label>
					        		<div class="col-sm-6">
					        			<?php if(!empty($videogallerydata)){ ?>
					        				<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
					        				<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
					        			<?php }else{ ?>
					        				<input type="button" id="submit" onclick="checkvalidation(0)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
					        				<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD" class="btn btn-primary btn-raised">
					        			  	<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
					        			<?php } ?>										
										<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>video-gallery" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
		<?php if(isset($videogallerydata)){ ?>
		$('#mediacategoryid').val([<?php echo $videogallerydata['mediacategoryid'];?>]);
		$('#mediacategoryid').selectpicker('refresh');
	<? } ?>
	})
</script>