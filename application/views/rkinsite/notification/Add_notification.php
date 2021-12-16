<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($notificationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($notificationdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>
    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default border-panel">
            <div class="panel-body">
              <div class="col-sm-12 ">
                    <form class="form-horizontal offset-md-1" id="form-notification" enctype="multipart/form-data">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group" id="brand_div">
                            <label class="col-md-4 control-label" for="brandid">Select Brand</label>
                            <div class="col-md-8">
                              <select class="form-control selectpicker" id="brandid" name="brandid" data-live-search="true" data-actions-box="true" data-select-on-tab="true" data-size="5">
                                <option value="0">Select Brand</option>
                                  <?php foreach($branddata as $brand){ ?>
                                    <option value="<?=$brand['id']?>" <?=((isset($notificationdata) && $notificationdata['brandid']==$brand['id'])?"selected":"")?>><?=$brand['name']?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group" id="channel_div">
                              <label class="col-md-4 control-label" for="channelid">Select Channel <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-8">
                                <select id="channelid" name="channelid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select Channel">
                                  <?php foreach ($channeldata as $channel) { ?>
                                    <option value='<?php echo $channel['id']; ?>'><?=$channel['name']?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group" id="member_div">
                            <label class="col-md-4 control-label" for="memberid">Select <?=Member_label?> <span class="mandatoryfield"> * </span></label>
                            <div class="col-md-8">
                              <select id="memberid" name="memberid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select <?=Member_label?>">
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-12"><hr></div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group" id="description_div">
                            <div id='termscontainer'>
                              <label for="description" class="col-sm-2 control-label mb-sm">Message <span class="mandatoryfield">*</span></label></label>
                              <div class="col-sm-10">
                                <?php $data['controlname']="description";if(isset($notificationdata) && !empty($notificationdata)){$data['controldata']=$notificationdata['description'];} ?>
                                <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                              </div>
                            </div>
                          </div>
                          <div class="form-group text-center">
                              <label for="focusedinput" class="col-sm-2 control-label"></label>
                            <div class="col-sm-8">
                              <?php if(!empty($notificationdata)){ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                              <?php }else{ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                              <?php } ?>
                              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>notification" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
</div>    					