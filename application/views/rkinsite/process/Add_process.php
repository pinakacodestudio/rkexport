<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($processdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($processdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                  <form class="form-horizontal" id="process-form">
                      <input type="hidden" name="processid" value="<?php if(isset($processdata)){ echo $processdata['id']; } ?>">
                      <div class="form-body">
                          <div class="form-group" id="name_div">
                              <label class="col-md-3 control-label" for="name">Process Name <span class="mandatoryfield">*</span></label>
                              <div class="col-md-8">
                                  <input type="text" id="name" class="form-control" name="name" value="<?php if(isset($processdata)){ echo $processdata['name']; } ?>">
                              </div>
                          </div>
                          <div class="form-group" id="description_div">
                              <label class="col-md-3 control-label" for="description">Description</label>
                              <div class="col-md-8">
                                  <textarea id="description" class="form-control" name="description"><?php if(isset($processdata)){ echo $processdata['description']; } ?></textarea>
                              </div>
                          </div>
                          <div class="form-group" id="designation_div">
                              <label for="designationid" class="col-sm-3 control-label">Select Designation</label>
                              <div class="col-sm-6">
                                <?php  
                                $designationIdArray = array();
                                if(isset($processdata) && $processdata['designationid']!=""){
                                    $designationIdArray = explode(",", $processdata['designationid']);
                                }?>
                                  <select id="designationid" name="designationid[]" class="selectpicker form-control" data-live-search="true" data-actions-box="true" title="Select Designation" data-select-on-tab="true" data-size="5" multiple>
                                    <?php if(!empty($designationdata)){ foreach($designationdata as $designation){ ?>
                                    <option value="<?php echo $designation['id']; ?>" <?php if(isset($processdata) && in_array($designation['id'], $designationIdArray)){ echo "selected"; }?>><?php echo ucwords($designation['name']); ?></option>
                                    <?php }} ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group" id="machine_div">
                              <label for="machineid" class="col-sm-3 control-label">Select Machine</label>
                              <div class="col-sm-6">
                                <?php 
                                $machineIdArray = array();
                                if(isset($processdata) && $processdata['machineid']!=""){
                                    $machineIdArray = explode(",", $processdata['machineid']);
                                }?>
                                  <select id="machineid" name="machineid[]" class="selectpicker form-control" data-live-search="true" data-actions-box="true" title="Select Machine" data-select-on-tab="true" data-size="5" multiple>
                                    <?php if(!empty($machinedata)){ foreach($machinedata as $machine){ ?>
                                    <option value="<?php echo $machine['id']; ?>" <?php if(isset($processdata) && in_array($machine['id'], $machineIdArray)){ echo "selected"; }?>><?php echo ucwords($machine['name']); ?></option>
                                    <?php }} ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group" id="vendor_div">
                              <label for="vendorid" class="col-sm-3 control-label">Select Vendor</label>
                              <div class="col-sm-6">
                                <?php 
                                $vendorIdArray = array();
                                if(isset($processdata) && $processdata['vendorid']!=""){
                                    $vendorIdArray = explode(",", $processdata['vendorid']);
                                }?>
                                  <select id="vendorid" name="vendorid[]" class="selectpicker form-control" data-live-search="true" data-actions-box="true" title="Select Vendor" data-select-on-tab="true" data-size="5" multiple>
                                    <?php if(!empty($vendordata)){ foreach($vendordata as $vendor){ ?>
                                    <option value="<?php echo $vendor['id']; ?>" <?php if(isset($processdata) && in_array($vendor['id'], $vendorIdArray)){ echo "selected"; }?>><?php echo ucwords($vendor['name']); ?></option>
                                    <?php }} ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                              <div class="col-md-6">
                              <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="status" id="yes" value="1" <?php if(isset($processdata) && $processdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="yes">Yes</label>
                                  </div>
                              </div>
                              <div class="col-md-3 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="status" id="no" value="0" <?php if(isset($processdata) && $processdata['status']==0){ echo 'checked'; }?>>
                                  <label for="no">No</label>
                                  </div>
                              </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <?php if(!empty($processdata)){ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                            </div>
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