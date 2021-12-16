<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($machinedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($machinedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body">
                <div class="col-sm-12">
                  <form class="form-horizontal" id="machine-form">
                      <input type="hidden" name="machineid" value="<?php if(isset($machinedata)){ echo $machinedata['id']; } ?>">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group" id="companyname_div">
                              <label class="col-md-4 control-label" for="companyname">Company Name <span class="mandatoryfield">*</span></label>
                              <div class="col-md-8">
                                  <input type="text" id="companyname" class="form-control" name="companyname" value="<?php if(isset($machinedata)){ echo $machinedata['companyname']; } ?>" autocomplete="off" data-url="<?php echo base_url().ADMINFOLDER.'machine/getMachineList';?>" data-provide='companyname' data-placeholder="Enter Company Name">
                              </div>
                          </div>
                          <div class="form-group" id="machinename_div">
                              <label class="col-md-4 control-label" for="machinename">Machine Name <span class="mandatoryfield">*</span></label>
                              <div class="col-md-8">
                                  <input type="text" id="machinename" class="form-control" name="machinename" value="<?php if(isset($machinedata)){ echo $machinedata['machinename']; } ?>">
                              </div>
                          </div>
                          <div class="form-group" id="modelno_div">
                              <label class="col-md-4 control-label" for="modelno">Model No. <span class="mandatoryfield">*</span></label>
                              <div class="col-md-8">
                                  <input type="text" id="modelno" class="form-control" name="modelno" value="<?php if(isset($machinedata)){ echo $machinedata['modelno']; } ?>">
                              </div>
                          </div>
                          <div class="form-group" id="purchasedate_div">
                              <label for="purchasedate" class="col-md-4 control-label">Purchase Date</label>
                              <div class="col-sm-8">
                                  <input id="purchasedate" type="text" name="purchasedate" value="<?php if(isset($machinedata)){ echo $this->general_model->displaydate($machinedata['purchasedate']); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" class="form-control" readonly>
                              </div>
                          </div>
                        </div>
                       
                        
                        <div class="col-md-6">
                          <div class="form-group" id="unitconsumption_div">
                              <label class="col-md-5 control-label" for="unitconsumption">Power Consumption in Units <span class="mandatoryfield">*</span></label>
                              <div class="col-md-6">
                                  <input type="text" id="unitconsumption" class="form-control" name="unitconsumption" value="<?php if(isset($machinedata)){ echo $machinedata['unitconsumption']; } ?>" onkeypress="return isNumber(event)" maxlength="10">
                              </div>
                          </div>
                          <div class="form-group" id="noofhoursused_div">
                              <label class="col-md-5 control-label" for="noofhoursused">No of Hours Used</label>
                              <div class="col-md-6">
                                  <input type="text" id="noofhoursused" class="form-control" name="noofhoursused" value="<?php if(isset($machinedata)){ echo $machinedata['noofhoursused']; } ?>" onkeypress="return isNumber(event)" maxlength="10">
                              </div>
                          </div>
                          <div class="form-group" id="productioncapacity_div">
                              <label class="col-md-5 control-label" for="minimumcapacity">Production Capacity <span class="mandatoryfield">*</span></label>
                              <div class="col-md-3 pr-xs">
                                <div class="form-group" id="minimumcapacity_div">
                                    <div class="col-md-12">
                                        <input type="text" id="minimumcapacity" class="form-control" name="minimumcapacity" value="<?php if(isset($machinedata)){ echo $machinedata['minimumcapacity']; } ?>" placeholder="Minimum" onkeypress="return isNumber(event)" maxlength="10" autocomplete="off">
                                    </div>
                                </div>
                              </div>
                              <div class="col-md-3 pl-xs">
                                <div class="form-group" id="maximumcapacity_div">
                                    <div class="col-md-12">
                                        <input type="text" id="maximumcapacity" class="form-control" name="maximumcapacity" value="<?php if(isset($machinedata)){ echo $machinedata['maximumcapacity']; } ?>" placeholder="Maximum" onkeypress="return isNumber(event)" maxlength="10" autocomplete="off">
                                    </div>
                                </div>
                              </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="form-group">
                              <label for="focusedinput" class="col-md-5 control-label">Activate</label>
                              <div class="col-md-6">
                                <div class="col-md-2 col-xs-4" style="padding-left: 0px;">
                                    <div class="radio">
                                    <input type="radio" name="status" id="yes" value="1" <?php if(isset($machinedata) && $machinedata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                    <label for="yes">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-2 col-xs-4">
                                    <div class="radio">
                                    <input type="radio" name="status" id="no" value="0" <?php if(isset($machinedata) && $machinedata['status']==0){ echo 'checked'; }?>>
                                    <label for="no">No</label>
                                    </div>
                                </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <?php if(!empty($machinedata)){ ?>
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