<script>
  var frontendsubmenuid = '<?=(isset($managewebsitecontentdata))?$managewebsitecontentdata['frontendsubmenuid']:0?>';
</script>
<div class="page-content">
	<div class="page-heading">            
      <h1><?php if(isset($managewebsitecontentdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
      <small>
          <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active"><?php if(isset($managewebsitecontentdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
  </small>
  </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12" >
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-10 col-lg-10 col-md-offset-1" style="padding-left: 0px;">
      					<form class="form-horizontal" id="managewebsitecontentform">
      						<input type="hidden" name="managecontentid" id="managecontentid" value="<?php if(isset($managewebsitecontentdata)){ echo $managewebsitecontentdata['id']; } ?>">
      						
                  <div class="form-group">
                    <div class="col-md-6 form-group m-n p-n" id="title_div">
                      <label for="focusedinput" class="col-sm-4 control-label">Page Title <span class="mandatoryfield">*</span></label>
                      <div class="col-sm-8">
                        <input id="title" type="text" name="title" value="<?php if(!empty($managewebsitecontentdata)){ echo $managewebsitecontentdata['title']; } ?>" class="form-control" onkeyup="setslug(this.value);">
                      </div>
                    </div>
                    <div class="col-md-6 form-group m-n p-n" id="title_div">
                      <label for="focusedinput" class="col-sm-4 control-label">Page Link <span class="mandatoryfield">*</span></label>
                      <div class="col-sm-8">
                        <input id="slug" type="text" name="slug" value="<?php if(!empty($managewebsitecontentdata)){ echo $managewebsitecontentdata['slug']; } ?>" class="form-control">
                      </div>
                    </div>
                  </div>
      						<div class="form-group" id="description_div">               
                    <label for="focusedinput" class="col-sm-2 control-label">Content <span class="mandatoryfield">*</span></label></label>
                    <div class="col-sm-10">
                      <?php $data['controlname']="description";if(isset($managewebsitecontentdata) && !empty($managewebsitecontentdata)){$data['controldata']=$managewebsitecontentdata['description'];} ?>
                      <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6 form-group m-n p-n">
                      <label class="col-sm-4 control-label">Main Menu</label>
                      <div class="col-sm-8">
                        <select id="frontendmenuid" class="selectpicker form-control" name="frontendmenuid" data-live-search="true" data-size="5">
                          <option value="0">Select Main Menu</option>
                          <?php foreach($frontendmainmenudata as $row){ ?>
                            <option value="<?php echo $row['id']; ?>" <?php if(isset($managewebsitecontentdata)){ if($managewebsitecontentdata['frontendmenuid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 form-group m-n p-n">
                      <label class="col-sm-4 control-label">Sub Menu</label>
                      <div class="col-sm-8">
                        <select id="frontendsubmenuid" class="selectpicker form-control" name="frontendsubmenuid" data-live-search="true" data-size="5">
                          <option value="0">Select Sub Menu</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-md-6 form-group m-n p-n">
                      <label class="col-sm-4 control-label">Footer Section</label>
                      <div class="col-sm-8">
                        <div class="checkbox col-md-6 control-label" style="text-align: left;">
                          <input type="checkbox" name="quicklink" id="quicklink" <?php if(!empty($managewebsitecontentdata) && $managewebsitecontentdata['quicklink']==1){ echo 'checked';}?>>
                          <label for="quicklink" style="font-size: 14px;">Quick Link</label>
                        </div>
                        <div class="checkbox col-md-6 control-label" style="text-align: left;">
                          <input type="checkbox" name="ourproduct" id="ourproduct" <?php if(!empty($managewebsitecontentdata) && $managewebsitecontentdata['ourproduct']==1){ echo 'checked';}?>>
                          <label for="ourproduct" style="font-size: 14px;">Our Product</label>
                        </div> 
                        <div class="checkbox col-md-6 control-label" style="text-align: left;">
                          <input type="checkbox" name="footerlink" id="footerlink" <?php if(!empty($managewebsitecontentdata) && $managewebsitecontentdata['footerlink']==1){ echo 'checked';}?>>
                          <label for="footerlink" style="font-size: 14px;">Footer Link</label>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 form-group m-n p-n">
                      <label class="col-sm-4 control-label">Class</label>
                      <div class="col-sm-8">
                        <input type="text" name="class" value="<?php if(!empty($managewebsitecontentdata)){ echo $managewebsitecontentdata['class']; } ?>" class="form-control">
                      </div>
                    </div>
                  </div>
                  
      						<div class="form-group" id="metatitle_div">
                    <label class="col-md-2 control-label">Meta Title</label>
                    <div class="col-md-10">
                      <textarea id="metatitle" name="metatitle" class="form-control"><?php if(isset($managewebsitecontentdata)){ echo $managewebsitecontentdata['metatitle']; } ?></textarea>
                    </div>
                  </div>
                  <div class="form-group" id="metakeywords_div">
                    <label for="focusedinput" class="col-sm-2 control-label">Meta Keywords</label>
                    <div class="col-sm-10">
                      <input id="metakeywords" type="text" name="metakeywords" value="<?php if(isset($managewebsitecontentdata)){ echo $managewebsitecontentdata['metakeywords']; } ?>" data-provide="metakeywords">
                    </div>
                  </div>
                  <div class="form-group" id="metadescription_div">
                    <label class="col-md-2 control-label">Meta Description</label>
                    <div class="col-md-10">
                      <textarea id="metadescription" name="metadescription" class="form-control"><?php if(isset($managewebsitecontentdata)){ echo $managewebsitecontentdata['metadescription']; } ?></textarea>
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
							        <div class="col-md-4">
      									<?php if(!empty($managewebsitecontentdata)){ ?>
      										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised" tabindex="16">
      										<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
      									<?php }else{ ?>
      									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised" tabindex="16">
      									  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
                          <?php } ?>
						              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>manage_website_content" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
<script type="text/javascript">
  $('#contentid').change(function(){
  var contentid = $("#contentid").val();
  var contenttypedata = <?=json_encode($this->contenttype)?>;    

  if(contentid!=0){
    contentname = contenttypedata[contentid];
    $('#slug').val(contentname.toLowerCase().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-'));
    
  }
  
});

</script>

