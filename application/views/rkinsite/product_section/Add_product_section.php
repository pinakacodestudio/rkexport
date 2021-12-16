<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($productsectiondata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($productsectiondata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
		</small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-10 col-lg-10">
						<form class="form-horizontal" id="productsectionform" name="productsectionform">
							<input type="hidden" name="productsectionid" value="<?php if(isset($productsectiondata)){ echo $productsectiondata['id']; } ?>">
							<?php if(isset($productsectiondata)){ ?>
								<div class="form-group" id="channelid_div">
									<label for="channelid" class="col-sm-5 control-label">Select Channel</label>
									<div class="col-sm-6">
										<select id="channelid" name="channelid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
											<option value="0">Select Channel Name</option>
											<?php foreach($channeldata as $cd){ ?>
											<option value="<?php echo $cd['id']; ?>" <?php if(isset($productsectiondata)){ if($productsectiondata['channelid'] == $cd['id']){ echo 'selected'; } }  ?>><?php echo $cd['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<? }else{ ?>
								<div class="form-group" id="channelid_div">
									<label for="channelid" class="col-sm-5 control-label">Select Channel</label>
									<div class="col-sm-4">
										<select id="channelid" name="channelid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select Channel Name">
											<?php foreach($channeldata as $cd){ ?>
											<option value="<?php echo $cd['id']; ?>"><?php echo $cd['name']; ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<? } ?>

<!-- 
							<div class="form-group" id="channelid_div">
								<label class="col-sm-5 control-label" for="channelid">Channel</label>
								<div class="col-sm-6">
									<select id="channelid" name="channelid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-actions-box="true" multiple>
										<option value="0">Select Channel</option>
										<?php foreach($channeldata as $cd){ ?>
										<option value="<?php echo $cd['id']; ?>" <?php if(isset($productsectiondata)){ if($productsectiondata['channelid'] == $cd['id']){ echo 'selected'; } }  ?>><?php echo $cd['name']; ?></option>
										<?php } ?>
									</select>
								</div>
							</div> -->
							
							<!-- <div class="form-group" id="category_div">
								<label for="categoryid" class="col-sm-5 control-label">Select Category</label>
								<div class="col-sm-6">
									<select id="categoryid" name="categoryid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="Select Category" data-live-search="true">
									<?php foreach($categorydata as $cd){ ?>
										<option value="<?php echo $cd['id']; ?>" <?php if(isset($productsectiondata)){ if($productsectiondata['categoryid'] == $cd['id']){ echo 'selected'; } }  ?>><?php echo $cd['name']; ?></option>
									<?php } ?>
									</select>
								</div>
							</div> -->
						
							<div class="form-group" id="productsection_div">
								<label for="name" class="col-sm-5 control-label">Name <span class="mandatoryfield">*</span></label>
								<div class="col-sm-6">
									<input id="name" type="text" name="name" value="<?php if(!empty($productsectiondata)){ echo $productsectiondata['name']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
								</div>
							</div>

							<div class="form-group" id="maxhomeproduct_div">
								<label for="maxhomeproduct" class="col-sm-5 control-label">Maximum Display Product in Home <span class="mandatoryfield">*</span></label>
								<div class="col-sm-6">
									<input id="maxhomeproduct" type="text" name="maxhomeproduct" value="<?php if(isset($productsectiondata)){ echo $productsectiondata['maxhomeproduct']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
								</div>
							</div>

							<div class="form-group" id="inorder_div">
								<label for="inorder" class="col-sm-5 control-label">Section Priority</label>
								<div class="col-sm-6">
									<input id="inorder" type="text" name="inorder" value="<?php if(isset($productsectiondata)){ echo $productsectiondata['inorder']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
								</div>
							</div>

							<div class="form-group">
								<label for="focusedinput" class="col-md-5 control-label">Display Type</label>
								<div class="col-md-6">
									<div class="col-md-3 col-xs-3" style="padding-left: 0px;">
										<div class="radio">
											<input type="radio" name="displaytype" id="displaytypeyes" value="0" <?php if(isset($productsectiondata) && $productsectiondata['displaytype']==0){ echo 'checked'; }else{ echo 'checked'; }?>>
											<label for="displaytypeyes">Grid</label>
										</div>
									</div>
									
									<div class="col-md-6 col-xs-6">
										<div class="radio">
										<input type="radio" name="displaytype" id="displaytypeno" value="1" <?php if(isset($productsectiondata) && $productsectiondata['displaytype']==1){ echo 'checked'; }?>>
										<label for="displaytypeno">Grid with slider</label>
										</div>
									</div>
									<div class="col-md-12 col-xs-12 p-n">
									<small>Note : Grid setting will apply only for mobile application	</small>
									</div>
								</div>
							</div>

							<div class="form-group">
							<label for="focusedinput" class="col-sm-5 col-xs-4 control-label"></label>
                                  <div class="col-sm-6 col-xs-8">
                                    <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                      <div class="checkbox">
									  <input type="checkbox" name="forwebsite" id="forwebsite"value="1" <?php if(empty($productsectiondata)){echo 'checked';}?> <?php if(isset($productsectiondata) && $productsectiondata['forwebsite']==1){ echo 'checked';}?>>
                                      <label  style="font-size: 14px;" for="forwebsite">ForWebsite</label>
                                      </div>
                                    </div>
                                    <div class="col-sm-2 col-xs-6">
                                      <div class="checkbox">
                                      <input type="checkbox" name="forapp" id="forapp" value="1" <?php if(empty($productsectiondata)){echo 'checked';}?>  <?php if(isset($productsectiondata) && $productsectiondata['forapp']==1){ echo 'checked';}?>>
                                      <label  style="font-size: 14px;margin-left: 25px;" for="forapp">ForApp</label>
                                      </div>
                                    </div>
								</div>
							</div>
							

							<div class="form-group">
								<label for="focusedinput" class="col-md-5 control-label">Activate</label>
								<div class="col-md-6">
									<div class="col-md-3 col-xs-3" style="padding-left: 0px;">
										<div class="radio">
										<input type="radio" name="status" id="yes" value="1" <?php if(isset($productsectiondata) && $productsectiondata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
										<label for="yes">Yes</label>
										</div>
									</div>
									<div class="col-md-3 col-xs-3">
										<div class="radio">
										<input type="radio" name="status" id="no" value="0" <?php if(isset($productsectiondata) && $productsectiondata['status']==0){ echo 'checked'; }?>>
										<label for="no">No</label>
										</div>
									</div>
								</div>
							</div>

							<div class="form-group">
								<label for="focusedinput" class="col-sm-5 control-label"></label>
								<div class="col-sm-6">
									<?php if(!empty($productsectiondata)){ ?>
										<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
										<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php }else{ ?>
									  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
									<?php } ?>
									<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>product-section" title=<?=cancellink_title?>><?=cancellink_text?></a>
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