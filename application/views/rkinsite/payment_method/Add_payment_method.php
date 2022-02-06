<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($paymentmethoddata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($paymentmethoddata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="paymentmethodform">
						<input type="hidden" name="id" value="<?php if(isset($paymentmethoddata)){ echo $paymentmethoddata['id']; } ?>">
						
						<div class="form-group" id="paymentmethod_div">
							<label for="paymentmethod" class="col-sm-4 control-label">Payment Method <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="paymentmethod" type="text" name="paymentmethod" value="<?php if(!empty($paymentmethoddata)){ echo $paymentmethoddata['paymentmethod']; } ?>" class="form-control">
							</div>
						</div>
						
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-6">
								<?php if(!empty($paymentmethoddata)){ ?>
								<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
								<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
								<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php }else{ ?>
								<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								<input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
								<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
									
								
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