<script type="text/javascript">
  
  
</script>
<div class="page-content">
	<div class="page-heading">            
      <h1><?php if(isset($managecontentdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
      <small>
          <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?php echo ADMIN_URL; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active"><?php if(isset($managecontentdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
  </small>
  </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12" >
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-9 col-lg-9 col-md-offset-1" style="padding-left: 0px;">
      					<form class="form-horizontal" id="managecontentform">
      						<input type="hidden" name="managecontentid" id="managecontentid" value="<?php if(isset($managecontentdata)){ echo $managecontentdata['id']; } ?>">
      						<div class="form-group" id="channelid_div">
										<label class="col-sm-3 control-label" for="channelid">Select Channel</label>
										<div class="col-sm-6">
										<input type="hidden" value="<?php if(isset($channelidarr)){ echo implode(",",$channelidarr); } ?>" name="oldchannelid"></label>
											<select id="channelid" name="channelid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-actions-box="true" title="Select Channel" multiple>
												<?php foreach($channeldata as $cd){ ?>
												<option value="<?php echo $cd['id']; ?>" <?php if(isset($channelidarr)){ if(in_array($cd['id'],$channelidarr)){ echo 'selected'; } } ?>><?php echo $cd['name']; ?></option>
												<?php } ?>
											</select>
										</div>
								</div>
									<div class="form-group" id="contentid_div">
      							<label for="contentid" class="col-sm-3 control-label">Page Title <span class="mandatoryfield">*</span></label>
      							<div class="col-sm-6">
      								<select id="contentid" name="contentid" data-live-search="true"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="1">
                        <option value="0">Select Page Title</option>
                        <?php foreach ($this->contenttype as $contentid => $contentvalue) { ?>
      									   <option value="<?=$contentid?>" <?php if(isset($managecontentdata)){ if(in_array($contentid, explode(',',$managecontentdata['contentid']))){ echo 'selected'; } } ?> ><?=$contentvalue?></option>
                        <?php }?>
      									
      								</select>
      							</div>
      						</div>
      						<div class="form-group" id="description_div">               
                    <label for="description" class="col-sm-3 control-label">Content <span class="mandatoryfield">*</span></label></label>
                    <div class="col-sm-9">
                      <?php $data['controlname']="description";if(isset($managecontentdata) && !empty($managecontentdata)){$data['controldata']=$managecontentdata['description'];} ?>
                      <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                    </div>
                  </div>
      							
      						<div class="col-sm-9 col-sm-offset-3">
      							<div class="form-group">
      									<?php if(!empty($managecontentdata)){ ?>
      										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised" tabindex="16">
      										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
      									<?php }else{ ?>
      									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised" tabindex="16">
      									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
      									<?php } ?>
												<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>manage-content" title=<?=cancellink_title?>><?=cancellink_text?></a>
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

