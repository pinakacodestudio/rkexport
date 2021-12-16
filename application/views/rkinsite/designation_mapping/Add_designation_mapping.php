<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($designationmappingdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($designationmappingdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                  <form class="form-horizontal" id="designationmappingform">
                      <input type="hidden" name="designationmappingid" value="<?php if(isset($designationmappingdata)){ echo $designationmappingdata['id']; } ?>">
                      <div class="form-body">
                        <div class="form-group" id="defaultdesignation_div">
                              <label for="defaultdesignation" class="col-sm-4 control-label">Default Designation</label>
                              <div class="col-sm-6">
                                  <select id="defaultdesignation" name="defaultdesignation" class="selectpicker form-control" data-live-search="true" data-size="8">
                                      <option value="0">Select Default Designation</option>
                                    <?php foreach($this->Defaultdesignation as $key=>$val){ ?>
                                    <option value="<?php echo $key; ?>" <?php if(isset($designationmappingdata) && $key==$designationmappingdata['defaultdesignation']){ echo "selected"; }?>><?php echo $val; ?></option>
                                    <?php } ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group" id="designation_div">
                              <label for="designationid" class="col-sm-4 control-label">Designation Group</label>
                              <div class="col-sm-6">
                                <?php  
                                $designationIdArray = array();
                                if(isset($designationmappingdata) && $designationmappingdata['designationid']!=""){
                                    $designationIdArray = explode(",", $designationmappingdata['designationid']);
                                }?>
                                  <select id="designationid" name="designationid[]" class="selectpicker form-control" data-live-search="true" data-actions-box="true" title="Select Designation" data-select-on-tab="true" data-size="5" multiple>
                                    <?php if(!empty($designationdata)){ foreach($designationdata as $designation){ ?>
                                    <option value="<?php echo $designation['id']; ?>" <?php if(isset($designationmappingdata) && in_array($designation['id'], $designationIdArray)){ echo "selected"; }?>><?php echo ucwords($designation['name']); ?></option>
                                    <?php }} ?>
                                  </select>
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="focusedinput" class="col-md-4 control-label">Activate</label>
                              <div class="col-md-6">
                              <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="status" id="yes" value="1" <?php if(isset($designationmappingdata) && $designationmappingdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="yes">Yes</label>
                                  </div>
                              </div>
                              <div class="col-md-3 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="status" id="no" value="0" <?php if(isset($designationmappingdata) && $designationmappingdata['status']==0){ echo 'checked'; }?>>
                                  <label for="no">No</label>
                                  </div>
                              </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-12 text-center">
                                <?php if(!empty($designationmappingdata)){ ?>
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