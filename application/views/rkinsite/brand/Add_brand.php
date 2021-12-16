<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($branddata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($branddata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                <form class="form-horizontal" id="brand-form">
                    <input type="hidden" name="brandid" value="<?php if(isset($branddata)){ echo $branddata['id']; } ?>">
                    <div class="form-body">
                        <div class="form-group" id="brandname_div">
                            <label class="col-md-3 control-label" for="brandname">Brand Name <span class="mandatoryfield">*</span></label>
                            <div class="col-md-8">
                                <input type="text" id="brandname" class="form-control" name="brandname" value="<?php if(isset($branddata)){ echo $branddata['name']; } ?>">
                            </div>
                        </div>

                        <div class="form-group" id="brandimage_div">
                            <label class="col-md-3 control-label" for="brandimage">Select Image</label>
                            <div class="col-md-8">
                                <input type="hidden" name="oldbrandimage" id="oldbrandimage" value="<?php if(isset($branddata)){ echo $branddata['image'];} ?>">
                                <input type="hidden" name="removebrandimage" id="removebrandimage">
                                <?php if(isset($branddata) && $branddata['image'] != ''){ ?>
                                    <div class="imageupload" id="brandimageupload">
                                        <div class="file-tab">
                                            <img src="<?php if(isset($branddata)){ echo BRAND.$branddata['image']; } ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                            <label id="brandimagelable" class="btn btn-primary btn-raised btn-sm btn-file">
                                                <span id="brandimagebtn">Change</span>
                                                <!-- The file is stored here. -->
                                                <input type="file" name="brandimage" id="brandimage" accept=".bmp,.gif,.ico,.jpeg,.jpg,.png">
                                            </label>
                                            <button type="button" class="btn btn-danger btn-raised btn-sm btn-file" id="remove">Remove</button>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="imageupload" id="brandimageupload">
                                        <div class="file-tab">
                                            <img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                            <label id="brandimagelabel" class="btn btn-primary btn-raised btn-sm btn-file">
                                                <span id="brandimagebtn">Select Image</span>
                                                <input type="file" name="brandimage" id="brandimage" accept=".bmp,.gif,.ico,.jpeg,.jpg,.png">
                                            </label>
                                            <button type="button" class="btn btn-danger btn-raised btn-sm" id="remove">Remove</button>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="focusedinput" class="col-md-3 control-label">Activate</label>
                            <div class="col-md-8">
                            <div class="col-md-3 col-xs-4" style="padding-left: 0px;">
                                <div class="radio">
                                <input type="radio" name="status" id="yes" value="1" <?php if(isset($branddata) && $branddata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                <label for="yes">Yes</label>
                                </div>
                            </div>
                            <div class="col-md-3 col-xs-4">
                                <div class="radio">
                                <input type="radio" name="status" id="no" value="0" <?php if(isset($branddata) && $branddata['status']==0){ echo 'checked'; }?>>
                                <label for="no">No</label>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="form-group">
                          <label for="focusedinput" class="col-sm-3 control-label"></label>
                          <div class="col-sm-8">
                            <?php if(!empty($branddata)){ ?>
                              <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                              <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                            <?php }else{ ?>
                              <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                              <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & ADD NEW" class="btn btn-primary btn-raised">
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