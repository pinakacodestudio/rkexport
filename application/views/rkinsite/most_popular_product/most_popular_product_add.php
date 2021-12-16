<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($Most_popular_productdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($Most_popular_productdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-1 col-md-offset-1">
					<form class="form-horizontal" id="formsubmenu">
						<input type="hidden" name="mostid" value="<?php if(isset($Most_popular_productdata)){ echo $Most_popular_productdata['id']; } ?>">						
						<div class="form-group" id="product_div" >
							<label for="product" class="col-sm-4 control-label">Select product <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
							
								<select  class="selectpicker form-control" id="productid" name="productid"  data-live-search="true" data-select-on-tab="true" data-size="5">
									<option value="0">Select product</option>
									<?php foreach($productdata as $row){ ?>
										<option  value="<?php echo $row['id']; ?>" <?php if(isset($Most_popular_productdata)){ if($Most_popular_productdata['productid'] == $row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						
						<div class="form-group" id="priority_div">
							<label for="priority_div" id="priority_div" class="col-sm-4 control-label">  Priority </label>
							<div class="col-sm-8">
								<input id="priority" type="text" name="priority" value="<?php if(isset($Most_popular_productdata)){ echo $Most_popular_productdata['priority']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
							</div>
						</div>

                        <div class="form-group">
                              <label for="focusedinput" class="col-md-4 control-label">Activate</label>
                              <div class="col-md-8">
                                <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="status" id="yes" value="1" <?php if(isset($Most_popular_product_data) && $Most_popular_product_data['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="yes">Yes</label>
                                  </div>
                                </div>
                                <div class="col-md-4 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="status" id="no" value="0" <?php if(isset($Most_popular_product_data) && $Most_popular_product_data['status']==0){ echo 'checked'; }?>>
                                  <label for="no">No</label>
                                  </div>
                                </div>
                              </div>
                            </div>
						

						

						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-8">
								<?php if(isset($Most_popular_productdata)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>Most_popular_product/" title=<?=cancellink_title?>><?=cancellink_text?></a>
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