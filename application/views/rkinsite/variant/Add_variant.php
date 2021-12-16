<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($variantdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($variantdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-6 col-lg-6 col-lg-offset-3 col-md-offset-3">
						<form class="form-horizontal" id="variantform" name="variantform">
							<input type="hidden" name="variantid" value="<?php if(isset($variantdata)){ echo $variantdata['id']; } ?>">
							<div class="form-group" id="attribute_div">
								<label for="attributeid" class="col-sm-3 control-label">Attribute <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<select id="attributeid" name="attributeid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" tabindex="8">
										<option value="0">Select Attribute</option>
										<?php foreach($attributedata as $row){ ?>
											<option value="<?php echo $row['id']; ?>" <?php if(isset($variantdata)){ if($variantdata['attributeid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['variantname']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group" id="variant_div">
								<label for="name" class="col-sm-3 control-label">Variant <span class="mandatoryfield">*</span></label>
								<div class="col-sm-8">
									<input id="name" type="text" name="name" value="<?php if(!empty($variantdata)){ echo $variantdata['value']; } ?>" class="form-control">
								</div>
							</div>
							<div class="form-group" id="priority_div">
								<label for="priority" class="col-sm-3 control-label">Priority</label>
								<div class="col-sm-8">
									<input id="priority" type="text" name="priority" value="<?php if(isset($variantdata)){ echo $variantdata['priority']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
								</div>
							</div>
							<div class="form-group">
								<label for="focusedinput" class="col-sm-3 control-label"></label>
								<div class="col-sm-8">
									<?php if(!empty($variantdata)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php }else{ ?>
									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php } ?>
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>variant" title=<?=cancellink_title?>><?=cancellink_text?></a>
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