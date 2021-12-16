<script>
  var memberidarr = '<?php if(!empty($memberidarr)){ echo implode(",",$memberidarr); } ?>';
  var CHANNELWISECATALOG = '<?=CHANNELWISECATALOG?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($catalogdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($catalogdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body">
                <div class="col-sm-12 col-md-10 col-lg-10 col-lg-offset-1 col-md-offset-1">
                  <form class="form-horizontal" id="form-catalog" enctype="multipart/form-data">
                    <input type="hidden" name="catalogid" id="catalogid" value="<?php if(isset($catalogdata)){ echo $catalogdata['id']; } ?>">    
                    <input type="hidden" name="sendnotification">
                    <div class="form-body">                      

                        <div class="form-group row" for="catalogname" id="catalogname_div">
                          <label for="catalogname" class="col-md-3 control-label">Catalog Name <span class="mandatoryfield"> * </span></label>
                          <div class="col-md-6">
                            <input type="text" id="catalogname" class="form-control" placeholder="Catalog Name" name="catalogname" value="<?php if(isset($catalogdata)){ echo $catalogdata['name']; } ?>">
                          </div>
                        </div>
                        <div class="form-group row" id="channel_div">
                            <label class="col-md-3 control-label" for="channelid">
                          Select Channel <span class="mandatoryfield"> * </span></label>
                          <input type="hidden" value="<?php if(isset($catalogdata)){ echo $catalogdata['channelid']; } ?>" name="oldchannelid"></label>
                            <div class="col-md-5">
                              <select class="form-control selectpicker" id="channelid" name="channelid" title="Select Channel" data-live-search="true" <?php if(!empty($catalogdata) || CHANNELWISECATALOG==0){ echo "disabled"; } ?>>
                                  <?php foreach($channeldata as $row){ ?>
                                    <option value="<?php echo $row['id']; ?>" <?php if(isset($catalogdata)){ if($catalogdata['channelid']==$row['id']){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option> 
                                    <?php } ?>
                              </select>  
                            </div>
                        </div>
                        <div class="form-group row" for="catalogname" id="member_div">
                          <label class="col-md-3 control-label" for="memberid">Select <?=Member_label?> <span class="mandatoryfield"> * </span></label>
                          <input type="hidden" value="<?php if(isset($memberidarr)){ echo implode(",",$memberidarr); } ?>" name="oldmemberid"></label>
                          <div class="col-md-5">
                            <select id="memberid" name="memberid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select <?=Member_label?>" <?php if(!empty($catalogdata) || CHANNELWISECATALOG==0){ echo "disabled"; } ?>>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row" id="description_div">
                          <div id='termscontainer'>
                              <label for="description" class="col-sm-3 control-label">Description <span class="mandatoryfield">*</span></label></label>
                              <div class="col-sm-8">
                                    <?php $data['controlname']="description";if(isset($catalogdata) && !empty($catalogdata)){$data['controldata']=$catalogdata['description'];} ?>
                                    <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                              </div>
                          </div>
                        </div>
                        <div class="form-group row" id="textfile_div">
                          <label class="col-md-3 control-label" for="textfile">
                              Browse Pdf file

                          <span class="mandatoryfield"> * </span></label>
                            <div class="col-md-8" >
                              <input type="hidden" name="oldfilepdf" id="oldfilepdf" value="<?php if(isset($catalogdata)){ echo $catalogdata['pdffile'];} ?>">
                                <div class="input-group" id="fileupload1">
                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                        <span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
                                          <input type="file" name="filepdf"  id="filepdf" onchange="validfile($(this))">
                                        </span>
                                    </span>                                        
                                    <input type="text" id="textfile" class="form-control" name="textfile" value="<?php  
                                            if(isset($catalogdata)){ echo $catalogdata['pdffile'];}
                                    ?>" readonly >
                                  </div>                                      
                                </div>
                              </div>

                          <div class="form-group row" id="fileimage_div">
                              <label class="col-sm-3 control-label">Image File <span class="mandatoryfield">*</span></label>
                              <div class="col-md-8">
                                  <input type="hidden" name="oldfileimage" id="oldfileimage" value="<?php if(isset($catalogdata)){ echo $catalogdata['image'];} ?>">
                                  <input type="hidden" name="removeimg" id="removeimg">
                                  <?php
                                      if(isset($catalogdata) && $catalogdata['image'] != ''){ 
                                  ?>
                                  <script type="text/javascript"> var ACTIONIMG = 1;</script>
                                      <div class="imageupload" id="fileImg">

                                          <div class="file-tab">
                                              <img src="<?php if(isset($catalogdata)){ echo CATALOG_IMAGE.$catalogdata['image']; } ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px"><br/>
                                              <label id="filelable" class="btn  btn-primary btn-raised btn-file btn-sm">
                                                  <span id="fileimagebtn">Change</span>
                                                  <!-- The file is stored here. -->
                                                  <input type="file" name="fileimage" id="fileimage" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                              </label>
                                              <button type="button" class="btn btn-danger btn-raised btn-sm" id="remove">Remove</button>
                                          </div>
                                      </div>
                                  <?php
                                      } else {
                                  ?>
                                      <div class="imageupload" id="fileImg">
                                          <div class="file-tab">
                                            <script type="text/javascript"> var ACTIONIMG = 0;</script>
                                              <img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
                                              <br/>
                                              <label id="logolabel" class="btn btn-primary btn-sm btn-raised btn-file">
                                                  <span id="fileimagebtn">Select Image</span>
                                                  <input type="file" name="fileimage" id="fileimage" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                              </label>
                                              <button type="button" class="btn btn-danger btn-sm btn-raised" id="removeimg">Remove</button>
                                          </div>
                                      </div>
                                  <?php
                                      }
                                  ?>
                                  </div>
                              </div>

                              
                          </div>
                          <div class="form-group">
                                <label for="focusedinput" class="col-md-5 control-label">Activate</label>
                                <div class="col-md-6">
                                  <div class="col-md-3 col-xs-3" style="padding-left: 0px;">
                                    <div class="radio">
                                    <input type="radio" name="status" id="yes" value="1" <?php if(isset($catalogdata) && $catalogdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                    <label for="yes">Yes</label>
                                    </div>
                                  </div>
                                  <div class="col-md-3 col-xs-3">
                                    <div class="radio">
                                    <input type="radio" name="status" id="no" value="0" <?php if(isset($catalogdata) && $catalogdata['status']==0){ echo 'checked'; }?>>
                                    <label for="no">No</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                          <div class="form-group text-center">
                            <div class="col-sm-12">
                              <?php if(!empty($catalogdata)){ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" onclick="resetdata()"  class="btn btn-info btn-raised">
                              <?php }else{ ?>
                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                <input type="reset" name="reset" value="RESET" onclick="resetdata()" class="btn btn-info btn-raised">
                              <?php } ?>
                              <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>catalog" title=<?=cancellink_title?>><?=cancellink_text?></a>
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