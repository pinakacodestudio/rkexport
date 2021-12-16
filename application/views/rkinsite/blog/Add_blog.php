<script type="text/javascript">
	var BLOG_IMAGE_URL = '<?=BLOG?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($blogdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($blogdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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

					<form class="form-horizontal" id="blogform">
						<input type="hidden" name="blogid" value="<?php if(isset($blogdata)){ echo $blogdata['id']; } ?>">
						<div class="form-group" id="title_div">
							<label class="col-sm-3 control-label" for="title">Blog Title <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="title" type="text" name="title" value="<?php if(!empty($blogdata)){ echo $blogdata['title']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="category_div">
							<label class="col-md-3 control-label" for="blogcategoryid">Category</label>
							<div class="col-md-8">
								<input id="blogcategoryid" type="text" name="blogcategoryid" data-url="<?php echo base_url().ADMINFOLDER.'blog_category/getactiveblogcategory';?>" value="<?php if(isset($blogdata)){ echo $blogdata['blogcategoryid']; } ?>" placeholder="Select Category" data-provide="blogcategoryid" class="form-control">
							</div>
						</div>
	                  	<div class="form-group" id="image_div">               
	                    	<label for="focusedinput" class="col-sm-3 control-label">Image</label></label>
	                    	<div class="col-sm-8">
						        <input type="hidden" name="oldblogimage" id="oldblogimage" value="<?php if(!empty($blogdata)){ echo $blogdata['image']; } ?>">
						        <input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
	                      		<?php if(isset($blogdata) && $blogdata['image']!=''){ ?>
            						<div class="imageupload" id="blogimg">
						                <div class="file-tab"><img src="<?php echo BLOG.$blogdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
						                	
						                    <label id="faviconlabel" class="btn btn-sm btn-primary btn-raised btn-file">
						                        <span id="imagebtn">Change</span>
						                        <!-- The file is stored here. -->
						                        <input type="file" name="image" id="image">
						                    </label>
						                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
						                </div>
            						</div>
            					<?php }else{ ?>
            						<div class="imageupload" id="blogimg">
						                <div class="file-tab">
						                	<img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
						                    <label class="btn btn-sm btn-primary btn-raised btn-file">
						                        <span id="imagebtn">Select Image</span>
						                        <input type="file" name="image" id="image" value="">
						                    </label>
						                    <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
						                </div>
            						</div>
            					<?php } ?>
	                    	</div>
	                  	</div>
	                  	<div class="form-group" id="description_div">               
	                    	<label for="focusedinput" class="col-sm-3 control-label">Description <span class="mandatoryfield">*</span></label></label>
	                    	<div class="col-sm-8">
	                      		<?php $data['controlname']="description";if(isset($blogdata) && !empty($blogdata)){$data['controldata']=$blogdata['description'];} ?>
	                      		<?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
	                    	</div>
	                  	</div>
	                  	<div class="form-group" id="metatitle_div">
							<label class="col-md-3 control-label" for="metatitle">Meta Title</label>
							<div class="col-md-8">
								<textarea id="metatitle" name="metatitle" class="form-control"><?php if(isset($blogdata)){ echo $blogdata['metatitle']; } ?></textarea>
							</div>
						</div>
	                  	<div class="form-group" id="metakeywords_div">
							<label for="focusedinput"  for="metakeywords" class="col-sm-3 control-label">Meta Keywords</label>
							<div class="col-sm-8">
								<input id="metakeywords" type="text" name="metakeywords" value="<?php if(isset($blogdata)){ echo $blogdata['metakeywords']; } ?>" data-provide="metakeywords">
							</div>
						</div>
	                  	<div class="form-group" id="metadescription_div">
							<label class="col-md-3 control-label" for="metadescription">Meta Description</label>
							<div class="col-md-8">
								<textarea id="metadescription" name="metadescription" class="form-control"><?php if(isset($blogdata)){ echo $blogdata['metadescription']; } ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($blogdata) && $blogdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2 col-xs-6">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($blogdata) && $blogdata['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($blogdata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php } ?>
						              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>blog" title=<?=cancellink_title?>><?=cancellink_text?></a>
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