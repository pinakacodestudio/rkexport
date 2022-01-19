<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($categorydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($categorydata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                <form class="form-horizontal" id="form-category">
                  <input type="hidden" name="categoryid" value="<?php if(isset($categorydata)){ echo $categorydata['id']; } ?>">
                  <div class="form-body">
                    
                    <div class="form-group" id="name_div">
                      <label class="col-md-4 control-label" for="name">Category Name <span class="mandatoryfield">*</span></label>
                      <div class="col-md-8">
                          <input type="text" id="name" class="form-control" name="name" value="<?php if(isset($categorydata)){ echo $categorydata['name']; } ?>" onkeyup="setslug(this.value);">
                      </div>
                    </div>
                  
                   
                    <div class="form-group">
                      <label for="focusedinput" class="col-md-4 control-label">Activate</label>
                      <div class="col-md-8">
                        <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                          <div class="radio">
                          <input type="radio" name="status" id="yes" value="1" <?php if(isset($categorydata) && $categorydata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                          <label for="yes">Yes</label>
                          </div>
                        </div>
                        <div class="col-md-4 col-xs-4">
                          <div class="radio">
                          <input type="radio" name="status" id="no" value="0" <?php if(isset($categorydata) && $categorydata['status']==0){ echo 'checked'; }?>>
                          <label for="no">No</label>
                          </div>
                        </div>
                      </div> 
                    </div>
                    <div class="form-group">
                      <label for="focusedinput" class="col-sm-4 control-label"></label>
                      <div class="col-sm-8">
                        <?php if(!empty($categorydata)){ ?>
                          <input type="button" id="submit" onclick="validation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                          <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                        <?php }else{ ?>
                          <input type="button" id="submit" onclick="validation()" name="submit" value="SAVE" class="btn btn-info btn-raised">
                          <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
                        <?php } ?>
                        <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>category" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
