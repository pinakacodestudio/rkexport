<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($dealerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($dealerdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="col-sm-12 col-md-10 col-lg-10 col-lg-offset-1 col-md-offset-1">
                <form class="form-horizontal" id="form-dealer">
                      <input type="hidden" name="dealerid" id="dealerid" value="<?php if(isset($dealerdata)){ echo $dealerdata['id']; } ?>">    
                      <div class="form-body">                      

                          <div class="form-group row" for="outletname"  id="outlet_div">
                            <label class="col-md-3 label-control" for="outletname">
                                  Outlet Name
                             <span class="mandatoryfield"> * </span></label>
                            <div class="col-md-8">
                             <input type="text" id="outletname" class="form-control" placeholder="Outlet Name" name="outletname" value="<?php if(isset($dealerdata)){ echo $dealerdata['outletname']; } ?>">
                              </div>
                            </div>
                            <div class="form-group row" id="address_div">
                                <div id='termscontainer'>
                                     <label for="address" class="col-sm-3  label-control">Address <span class="mandatoryfield">*</span></label></label>
                                    <div class="col-sm-8">
                                        <textarea id="address" class="form-control" placeholder="Address" name="address"><?php if(isset($dealerdata)){ echo $dealerdata['address']; } ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row" for="city" id="city_div">
                              <label class="col-md-3 label-control" for="city">
                                    City
                               <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-8">
                               <input type="text" id="city" class="form-control" placeholder="Enter city" name="city" value="<?php if(isset($dealerdata)){ echo $dealerdata['city']; } ?>">
                                </div>
                            </div>
                            <div class="form-group row" for="mobile" id="mobile_div">
                              <label class="col-md-3 label-control" for="mobile">
                                    Mobile
                               <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-8">
                               <input type="text" id="mobile" class="form-control" placeholder="Enter mobile" name="mobile" value="<?php if(isset($dealerdata)){ echo $dealerdata['mobile']; } ?>">
                                </div>
                            </div>
                            <div class="form-group row" for="email" id="email_div">
                              <label class="col-md-3 label-control" for="email">
                                    Email
                               <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-8">
                               <input type="text" id="email" class="form-control" placeholder="Enter email" name="email" value="<?php if(isset($dealerdata)){ echo $dealerdata['email']; } ?>">
                                </div>
                            </div>

                             <div class="form-group">
                              <label for="focusedinput" class="col-md-4 control-label">Activate</label>
                              <div class="col-md-8">
                                <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="status" id="yes" value="1" <?php if(isset($dealerdata) && $dealerdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="yes">Yes</label>
                                  </div>
                                </div>
                                <div class="col-md-4 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="status" id="no" value="0" <?php if(isset($dealerdata) && $dealerdata['status']==0){ echo 'checked'; }?>>
                                  <label for="no">No</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                            </div>
                            <div class="form-group">
                              <label for="focusedinput" class="col-sm-3 control-label"></label>
                              <div class="col-sm-8">
                                <?php if(!empty($dealerdata)){ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>dealer" title=<?=cancellink_title?>><?=cancellink_text?></a>
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