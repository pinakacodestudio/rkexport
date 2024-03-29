<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($additionalrightsrow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($additionalrightsrow)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
					<form class="form-horizontal" id="addcurrency">
					
						<input type="hidden" name="rightsid" value="<?php if(isset($currencydata)){ echo $currencydata['id']; } ?>">
						
						<div class="form-group" id="currency_div">
							<label for="currency" class="col-sm-4 control-label">Currency <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="currency" type="text" name="currency" value="<?php if(!empty($currencydata)){ echo $currencydata['currency']; } ?>" class="form-control">
							</div>
						</div>
						<div class="form-group" id="value_div">
							<label for="value" class="col-sm-4 control-label">Value <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="value" onkeypress="return isNumber(event)" type="text" name="value" value="<?php if(!empty($currencydata)){ echo $currencydata['value']; } ?>" class="form-control">
							</div>
						</div>

						<div class="form-group" id="date_div">
							<label for="date" class="col-md-4 control-label">Date <span class="mandatoryfield">*</span></label>
							<div class="col-md-6">
								<input id="date" name="date"  type="text" class="form-control col-sm-6" value="<?php if(isset($currencydata)){ echo $this->general_model->displaydate($currencydata['date']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
							</div>
						</div>

						
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-6">
								<?php if(isset($additionalrightsrow)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
								<?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & ADD NEW" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
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