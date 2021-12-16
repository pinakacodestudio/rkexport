<div class="page-content">
	<ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=base_url().$row['menuurl']; ?>"><?=$row['name']; ?></a></li>
          <?php } } ?>
    </ol>
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
					<form class="form-horizontal" id="formsmsgateway">
						<div class="form-group" id="smsurl_div">
							<label for="smsurl" class="col-sm-3 control-label">SMS URL <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="smsurl" type="text" name="smsurl" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['smsurl']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="mobileparameter_div">
							<label for="mobileparameter" class="col-sm-3 control-label">Mobile Parameter <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="mobileparameter" type="text" name="mobileparameter" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['mobileparameter']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="messageparameter_div">
							<label for="messageparameter" class="col-sm-3 control-label">Message Parameter <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="messageparameter" type="text" name="messageparameter" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['messageparameter']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="userid_div">
							<label for="userid" class="col-sm-3 control-label">User ID <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="userid" type="text" name="userid" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['userid']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="password_div">
							<label for="password" class="col-sm-3 control-label">Password <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="password" type="password" name="password" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['password']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="senderid_div">
							<label for="senderid" class="col-sm-3 control-label">Sender ID <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="senderid" type="text" name="senderid" value="<?php if(!empty($smsgatewaydata)){ echo $smsgatewaydata['senderid']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-md-3 control-label">Activate</label>
							<div class="col-md-8">
								<div class="col-md-3 col-xs-4" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($smsgatewaydata) && $smsgatewaydata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-md-3 col-xs-4">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($smsgatewaydata) && $smsgatewaydata['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($smsgatewaydata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>smsgateway" title=<?=cancellink_title?>><?=cancellink_text?></a>
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