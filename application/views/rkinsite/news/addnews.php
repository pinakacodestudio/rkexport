<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($newsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($newsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                    <form class="form-horizontal offset-md-1" id="form-news" enctype="multipart/form-data">
                     <input type="hidden" name="newsid" id="newsid" value="<?php if(isset($newsdata)){ echo $newsdata['id']; } ?>">    
                      <div class="form-body">
                       

                          <div class="form-group row" for="catalogname" id="newsname_div">
                            <label class="col-md-2 label-control" for="newsname">
                                 News Title
                             <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-5">
                                <input type="text" id="newsname" class="form-control" placeholder="News Name" name="newsname" value="<?php if(isset($newsdata)){ echo $newsdata['title']; } ?>">
                              </div>
                            </div>
                            <div class="form-group row" id="channel_div">
                              <label class="col-md-2 label-control" for="channelid">
                              Select Channel
                              <input type="hidden" value="<?php if(isset($channelidarr)){ echo implode(",",$channelidarr); } ?>" name="oldchannelid"></label>
                              <div class="col-md-5">
                              <select class="form-control selectpicker" id="channelid" name="channelid[]" title="Select Channel" data-actions-box="true" multiple>
                                    <?php foreach($channeldata as $row){ ?>
                                      <option value="<?php echo $row['id']; ?>" <?php if(isset($channelidarr)){ if(in_array($row['id'],$channelidarr)){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option> 
                                      <?php } ?>
                                </select>  
                                </div>
                            </div>
                            <div class="form-group row" id="description_div">
                                <div id='termscontainer'>
                                     <label for="focusedinput" class="col-sm-2  label-control"  for="description">Content <span class="mandatoryfield">*</span></label></label>
                                    <div class="col-sm-9">
                                          <?php $data['controlname']="description";if(isset($newsdata) && !empty($newsdata)){$data['controldata']=$newsdata['description'];} ?>
                                          <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                                    </div>
                                </div>
                            </div>
                            <?php /*
                            <div class="form-group row">
                              <label for="focusedinput" class="col-md-4 control-label">News For</label>
                              <div class="col-md-8">
                                <div class="col-md-4 col-xs-4" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="newsfor" id="customer" value="1" <?php if(isset($newsdata) && $newsdata['newsfor']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="customer">Customer</label>
                                  </div>
                                </div>
                                <div class="col-md-4 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="newsfor" id="member" value="2" <?php if(isset($newsdata) && $newsdata['newsfor']==2){ echo 'checked'; }?>>
                                  <label for="member"><?=Member_label?></label>
                                  </div>
                                </div>
                                <div class="col-md-4 col-xs-4">
                                  <div class="radio">
                                  <input type="radio" name="newsfor" id="both" value="0" <?php if(isset($newsdata) && $newsdata['newsfor']==0){ echo 'checked'; }?>>
                                  <label for="both">Both</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                             */?>
                            <div class="form-group text-center">
                              <label for="focusedinput" class="col-sm-5 col-xs-4 control-label">Activate</label>
                              <div class="col-sm-6 col-xs-8">
                                <div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
                                  <div class="radio">
                                  <input type="radio" name="status" id="yes" value="1" <?php if(isset($newsdata) && $newsdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                  <label for="yes">Yes</label>
                                  </div>
                                </div>
                                <div class="col-sm-2 col-xs-6">
                                  <div class="radio">
                                  <input type="radio" name="status" id="no" value="0" <?php if(isset($newsdata) && $newsdata['status']==0){ echo 'checked'; }?>>
                                  <label for="no">No</label>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="form-actions text-center">
                                <?php if(!empty($newsdata)){ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php }else{ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>news" title=<?=cancellink_title?>><?=cancellink_text?></a>
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