<div class="page-content">
	<div class="page-heading">            
      <h1><?php if(isset($smsformatdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
      <small>
          <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
            <li class="active"><?php if(isset($smsformatdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
  </small>
  </div>

    <div class="container-fluid">
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12" >
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-6 col-lg-6 col-md-offset-3" style="padding-left: 0px;">
      					<form class="form-horizontal" id="smsformatform">
      						<input type="hidden" name="smsformatid" id="smsformatid" value="<?php if(isset($smsformatdata)){ echo $smsformatdata['id']; } ?>">
      						
      						<div class="form-group" id="smsid_div">
      							<label class="col-sm-3 control-label" for="smsid">SMS Type <span class="mandatoryfield">*</span></label>
      							<div class="col-sm-9">
      								<select id="smsid" name="smsid" data-live-search="true"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="1">
                        <option value="0">Select SMS Type</option>
                        <?php foreach ($this->Smsformattype as $sfid => $sfvalue) { ?>
      									   <option value="<?=$sfid?>" <?php if(isset($smsformatdata)){ if(in_array($sfid, explode(',',$smsformatdata['smsid']))){ echo 'selected'; } } ?> ><?=$sfvalue?></option>
                        <?php }?>
      									
      								</select>
      							</div>
      						</div>
      						<div class="form-group" id="smsbody_div">               
                    <label class="col-sm-3 control-label" for="smsbody">SMS Template <span class="mandatoryfield">*</span></label></label>
                    <div class="col-sm-9">
                      <textarea id="smsbody" name="smsbody" class="form-control" rows="8"><?php if(isset($smsformatdata)){ echo $smsformatdata['smsbody'];}?></textarea>
                    </div>
                  </div>
      							
      						<div class="col-sm-9 col-sm-offset-3">
      							<div class="form-group">
      									<?php if(!empty($smsformatdata)){ ?>
      										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised" tabindex="16">
      										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
      									<?php }else{ ?>
      									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised" tabindex="16">
      									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
      									<?php } ?>
												<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>smsformat" title=<?=cancellink_title?>><?=cancellink_text?></a>
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

