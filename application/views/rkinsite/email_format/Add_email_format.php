<div class="page-content">
	<div class="page-heading">            
      <h1><?php if(isset($emailformatdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
      <small>
          <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active"><?php if(isset($emailformatdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
      					<form class="form-horizontal" id="emailformatform">
      						<input type="hidden" name="mailformatid" id="mailformatid" value="<?php if(isset($emailformatdata)){ echo $emailformatdata['id']; } ?><?php if(isset($mailformatiddata)){ echo $mailformatiddata; } ?>">
      						
      						<div class="form-group" id="mailid_div">
      							<label for="mailid" class="col-sm-3 control-label" for="mailid">Mail <span class="mandatoryfield">*</span></label>
      							<div class="col-sm-6">
      								<select id="mailid" name="mailid" data-live-search="true"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="1">
                        <option value="0">Select Mail</option>
                        <?php foreach ($this->Emailformattype as $mftid => $mftvalue) { ?>
      									   <option value="<?=$mftid?>" <?php if(isset($emailformatdata)){ if(in_array($mftid, explode(',',$emailformatdata['mailid']))){ echo 'selected'; } } ?> ><?=$mftvalue?></option>
                        <?php }?>
      									
      								</select>
      							</div>
      						</div>
      						<div class="form-group" id="subject_div">								
      							<label for="subject" class="col-sm-3 control-label" for="subject">Mail Subject <span class="mandatoryfield">*</span></label></label>
      							<div class="col-sm-6">
      								<input  type="text" id="subject" name="subject" value="<?php if(isset($emailformatdata)){ echo $emailformatdata['subject']; } ?>" maxlength="150" class="form-control" tabindex="6">
      							</div>
      						</div>
      						<div class="form-group" id="emailbody_div">               
                    <label for="emailbody" class="col-sm-3 control-label">Mail Content <span class="mandatoryfield">*</span></label></label>
                    <div class="col-sm-9">
                      <?php $data['controlname']="emailbody";if(isset($emailformatdata) && !empty($emailformatdata)){$data['controldata']=$emailformatdata['emailbody'];} ?>
                      <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                    </div>
                  </div>
      							
      						<div class="col-sm-9 col-sm-offset-6">
      							<div class="form-group">
      									<?php if(!empty($emailformatdata)){ ?>
      										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised" tabindex="16">
      										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
      									<?php }else{ ?>
      									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised" tabindex="16">
      									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
      									<?php } ?>
												<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>email-format" title=<?=cancellink_title?>><?=cancellink_text?></a>
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

