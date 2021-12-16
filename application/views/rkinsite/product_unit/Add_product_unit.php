<script>
  var MODALVIEW = '<?php if(!empty($modalview)){ echo 1; }else{ echo 0; } ?>';
</script>
<div class="page-content">
  <?php if(empty($modalview)){ ?>
    <div class="page-heading">            
        <h1><?php if(isset($productunitdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($productunitdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>
  <?php } ?>
  <?php if(empty($modalview)){ ?>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body">
                <div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
                <?php } ?>
                  <form class="form-horizontal" id="product-unit-form">
                      <input type="hidden" name="productunitid" value="<?php if(isset($productunitdata)){ echo $productunitdata['id']; } ?>">
                      <div class="form-body">
                          <div class="form-group" id="name_div">
                              <label class="col-md-3 control-label" for="name">Unit Name <span class="mandatoryfield">*</span></label>
                              <div class="col-md-8">
                                  <input type="text" id="name" class="form-control" name="name" value="<?php if(isset($productunitdata)){ echo $productunitdata['name']; } ?>">
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                              <div class="col-md-8">
                              <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="unitstatus" id="unityes" value="1" <?php if(isset($productunitdata) && $productunitdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="unityes">Yes</label>
                                  </div>
                              </div>
                              <div class="col-md-3 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="unitstatus" id="unitno" value="0" <?php if(isset($productunitdata) && $productunitdata['status']==0){ echo 'checked'; }?>>
                                  <label for="unitno">No</label>
                                  </div>
                              </div>
                              </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-12 text-center">
                              <?php if(!empty($productunitdata)){ ?>
                                <input type="button" id="submit" onclick="checkvalidationunit()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                              <?php }else{ ?>
                                <input type="button" id="submit" onclick="checkvalidationunit()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <!-- <input type="button" id="submit" onclick="checkvalidationunit(1)" name="submit" value="SAVE & ADD NEW" class="btn btn-primary btn-raised"> -->
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                
                              <?php } ?>
                              <?php if(empty($modalview)){ ?>
                              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                              <?php } ?>
                            </div>
                          </div>
                      </div>
                  </form>
                <?php if(empty($modalview)){ ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
    <?php } ?>
</div> <!-- #page-content -->