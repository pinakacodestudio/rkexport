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
					<form class="form-horizontal" id="formsystemlimit">
						<div class="form-group" id="fcmkey_div">
							<label for="fcmkey" class="col-sm-3 control-label">FCM Key <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="fcmkey" type="text" name="fcmkey" value="<?php if(!empty($systemlimitdata)){ echo $systemlimitdata['fcmkey']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="licensedate_div">
							<label for="brandingallow" class="col-sm-3 control-label">Branding <span class="mandatoryfield">*</span></label>
							<div class="col-sm-1">
								<div class="checkbox">
		                            <input id="brandingallow" name="brandingallow" onchange="getallow()" onclick="$(this).attr('value', this.checked ? 1 : 0)" type="checkbox" value="<?php if($systemlimitdata['brandingallow'] == "1"){echo "1";}else{echo "0";}?>" class="checkradios">
		                            <label for="brandingallow"></label>
		                        </div>
							</div>
							<div class="col-sm-3">
								<div class="radio">
									<input type="radio" name="branding" id="pioneeredby" value="0" <?php if($systemlimitdata['branding'] == 0){echo "checked";}?>>
									<label for="pioneeredby">Pioneered By</label>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="radio">
									<input type="radio" name="branding" id="poweredby" value="1" <?php if($systemlimitdata['branding'] == 1){echo "checked";}?>>
									<label for="poweredby">Powered By</label>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($systemlimitdata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
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