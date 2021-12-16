<script>
    var channelid = '<?php if(isset($salespersonmemberdata)){ echo $salespersonmemberdata['channelid']; }else{ echo "0"; } ?>';
    var memberid = '<?php if(isset($salespersonmemberdata)){ echo $salespersonmemberdata['memberid']; }else{ echo "0"; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($salespersonmemberdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($salespersonmemberdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">
                                     
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body pt-sm">
                <div class="col-md-10 col-md-offset-2">
                  <form class="form-horizontal" id="salespersonmemberform">
                        <input type="hidden" name="salespersonmemberid" value="<?php if(isset($salespersonmemberdata)){ echo $salespersonmemberdata['id']; } ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" id="salesperson_div">
                                    <label for="salespersonid" class="col-md-3 control-label">Select Sales Person <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-5">
                                        <select id="salespersonid" name="salespersonid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                            <option value="0">Select Sales Person</option>
                                            <?php foreach ($employeedata as $employee) { ?>
                                                <option value="<?=$employee['id']?>" <?php if(isset($salespersonmemberdata) && $salespersonmemberdata['employeeid']==$employee['id']){ echo "selected"; } ?>><?=ucwords($employee['name'])?></option>
                                            <?php } ?> 
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="workforchannel_div">
                                    <label for="workforchannelid" class="col-sm-3 control-label">Select Channel <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-5">
                                        <select id="workforchannelid" name="workforchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                            <option value="0">Select Channel</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="member_div">
                                    <label for="memberid" class="col-sm-3 control-label">Select <?=Member_label?> <span class="mandatoryfield">*</span></label>
                                    <div class="col-sm-5">
                                    <?php if(isset($salespersonmemberdata)){ ?>
                                        <select id="memberid" name="memberid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                            <option value="0">Select <?=Member_label?></option>
                                        </select>
                                    <?php }else{ ?>
                                        <select id="memberid" name="memberid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" title="Select <?=Member_label?>" data-actions-box="true" multiple>
                                        </select>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-12 mt-sm">
                                <div class="form-group">
                                    <label class="col-sm-3 control-label"></label>
                                    <div class="col-sm-9">
                                        <?php if(!empty($salespersonmemberdata)){ ?>
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