<div class="page-content">
    <div class="page-heading">
        <h1><?php if(isset($documenttypedata)){ echo 'Edit'; }else{ echo 'Add'; } ?>
            <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a>
                </li>
                <li class="active"><?php if(isset($documenttypedata)){ echo 'Edit'; }else{ echo 'Add'; } ?>
                    <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                <form class="form-horizontal" id="form-documenttype" method="POST">
                  
                  <div class="form-body">
                    
                    <div class="form-group" id="name_div">
                      <label class="col-md-4 control-label" for="name">Document Type <span class="mandatoryfield">*</span></label>
                      <div class="col-md-6">
                      <input type="hidden" id="id" class="form-control" name="id" value="<?php if(isset($documenttypedata)){ echo $documenttypedata['id']; }?>"> 
                          <input type="text" id="name" class="form-control" name="name" value="<?php if(isset($documenttypedata)){ echo $documenttypedata['documenttype']; }?>"> 
                      </div>
                    </div>

                    <div class="form-group" id="description_div">
                      <label class="col-md-4 control-label" for="description">Description </label>
                      <div class="col-md-6">
                          <textarea class="form-control" id="description" name="description"><?php if(isset($documenttypedata)){ echo $documenttypedata['description']; }?></textarea>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="focusedinput" class="col-md-4 col-xs-4 control-label">Activate</label>
                      <div class="col-md-8">
                        <div class="col-md-2 col-xs-2" style="padding-left: 0px;">
                          <div class="radio">
                          <input type="radio" name="status" id="yes" value="1" <?php if(isset($documenttypedata) && $documenttypedata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                          <label for="yes">Yes</label>
                          </div>
                        </div>
                        <div class="col-md-4 col-xs-2">
                          <div class="radio">
                          <input type="radio" name="status" id="no" value="0" <?php if(isset($documenttypedata) && $documenttypedata['status']==0){ echo 'checked'; }?>>
                          <label for="no">No</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="focusedinput" class="col-md-4 col-sm-3 col-xs-1 control-label"></label>
                      <div class="col-md-8 col-sm-9 col-xs-11">
                        <?php if(!empty($documenttypedata)){ ?>
                          <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                          <input type="button" id="submitandnew" onclick="checkvalidation(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised">
                          <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                        <?php }else{ ?>
                          <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                          <input type="button" id="submitandnew" onclick="checkvalidation(1)" name="submit" value="ADD & ADD NEW" class="btn btn-primary btn-raised">
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
</div> <!-- .container-fluid -->
</div> <!-- #page-content -->