<div class="page-content">
	<ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=base_url().$row['url']; ?>"><?=$row['name']; ?></a></li>
          <?php } } ?>
    </ol>
    <div class="page-heading">            
        <h1>Change Password</h1>
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
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-3 col-md-offset-3">
		        		<form id="changepasswordform" class="form-horizontal">
		        			<div class="form-group" id="oldpassword_div">
								<label class="col-sm-3 control-label" for="oldpassword">Old Password <span class="mandatoryfield">*</span></label>
								<div class="col-sm-6">
									<input id="oldpassword" type="password" name="oldpassword" class="form-control">
								</div>
							</div>
							<div class="form-group" id="newpassword_div">
								<label class="col-sm-3 control-label" for="newpassword">New Password <span class="mandatoryfield">*</span></label>
								<div class="col-sm-6">
									<input id="newpassword" type="password" name="newpassword" class="form-control">
								</div>
							</div>
							<div class="form-group" id="confirmpassword_div">
								<label class="col-sm-3 control-label" for="confirmpassword">Confirm Password <span class="mandatoryfield">*</span></label>
								<div class="col-sm-6">
									<input id="confirmpassword" type="password" name="confirmpassword" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="focusedinput" class="col-sm-3 control-label"></label>
								<div class="col-sm-6">
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								</div>
							</div>
		        		</form>
		        	</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>
    </div>
</div>