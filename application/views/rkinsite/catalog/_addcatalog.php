<div class="breadcrumb-wrapper col-xs-12">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Home</a>
        </li>
        <li class="breadcrumb-item">
            <a href="javascript:void(0)">
                <?php echo $this->session->userdata(base_url().'mainmenuname'); ?>
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="<?php echo base_url(); ?><?=$this->session->userdata(base_url().'submenuurl')?>">
              <?php echo $this->session->userdata(base_url().'submenuname'); ?>
            </a>
        </li>
        <li class="breadcrumb-item active"><?php if(isset($catalogdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
    </ol>
</div>

   <div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title" id="horz-layout-basic"><?php if(isset($catalogdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h4>
                <a class="heading-elements-toggle"><i class="icon-ellipsis font-medium-3"></i></a>
          <div class="heading-elements">
                    <!-- <ul class="list-inline mb-0">
                        <li><a data-action="expand"><i class="icon-expand2"></i></a></li>
                    </ul> -->
                </div>
            </div>

            <div class="card-body collapse in">
                <div class="card-block">
                    <form class="form-horizontal" id="form-catalog" enctype="multipart/form-data">
                      <input type="hidden" name="catalogid" id="catalogid" value="<?php if(isset($catalogdata)){ echo $catalogdata['id']; } ?>">    
                      <input type="hidden" name="sendnotification">
                      <div class="form-body">                      

                          <div class="form-group row" for="catalogname">
                            <label class="col-md-2 label-control">
                                  Catalog Name
                             <span class="mandatoryfield"> * </span></label>
                            <div class="col-md-5">
                             <input type="text" id="catalogname" class="form-control" placeholder="Catalog Name" name="catalogname" value="<?php if(isset($catalogdata)){ echo $catalogdata['name']; } ?>">
                              </div>
                            </div>
                            <div class="form-group row" id="description_div">
                                <div id='termscontainer'>
                                     <label for="focusedinput" class="col-sm-2  label-control">Content Description <span class="mandatoryfield">*</span></label></label>
                                    <div class="col-sm-9">
                                          <?php $data['controlname']="description";if(isset($catalogdata) && !empty($catalogdata)){$data['controldata']=$catalogdata['description'];} ?>
                                          <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                            <label class="col-md-2 label-control" >
                                 Browse Pdf file

                             <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-5" >
                                 <input type="hidden" name="oldfilepdf" id="oldfilepdf" value="<?php if(isset($catalogdata)){ echo $catalogdata['pdffile'];} ?>">
                               <div class="input-group" id="fileupload1">
                                                <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                    <span class="btn btn-primary btn-raised btn-file">Browse...
                                                       <input type="file" name="filepdf"  id="filepdf">
                                                    </span>
                                                </span>                                        
                                                <input type="text" id="textfile" class="form-control" name="textfile" value="<?php  
                                                      if(isset($catalogdata)){ echo $catalogdata['pdffile'];}
                                               ?>" readonly >
                                    </div>                                      
                                  </div>
                                </div>

                            <div class="form-group row">
                                <label class="col-sm-2  label-control">Image File <span class="mandatoryfield">*</span></label>
                                <div class="col-md-5">
                                    <input type="hidden" name="oldfileimage" id="oldfileimage" value="<?php if(isset($catalogdata)){ echo $catalogdata['image'];} ?>">
                                    <input type="hidden" name="removeimg" id="removeimg">
                                    <?php
                                        if(isset($catalogdata) && $catalogdata['image'] != ''){ 
                                    ?>
                                    <script type="text/javascript"> var ACTIONIMG = 1;</script>
                                        <div class="imageupload" id="fileImg">

                                            <div class="file-tab">
                                                <img src="<?php if(isset($catalogdata)){ echo CATALOG_IMAGE.$catalogdata['image']; } ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px"><br/>
                                                <label id="filelable" class="btn  btn-primary btn-raised btn-file" style="margin-top: 7px">
                                                    <span id="fileimagebtn">Change</span>
                                                    <!-- The file is stored here. -->
                                                    <input type="file" name="fileimage" id="fileimage">
                                                </label>
                                                <button type="button" class="btn btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
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
                                                <label id="logolabel" class="btn btn-primary btn-raised btn-file" style="margin-top: 7px">
                                                    <span id="fileimagebtn">Select Image</span>
                                                    <input type="file" name="fileimage" id="fileimage">
                                                </label>
                                                <button type="button" class="btn btn-danger btn-raised" id="removeimg">Remove</button>
                                            </div>
                                        </div>
                                    <?php
                                        }
                                    ?>
                                    </div>
                                </div>

                                <div class="form-group row">
                                <label class="col-md-2 label-control" for="status">Status</label>
                                <div class="col-md-5">
                                    <input id="status" name="status" type="checkbox" data-toggle="toggle" data-style="android" data-onstyle="info" data-on="Enabled" data-off="Disabled" <?php if(isset($catalogdata)){ if($catalogdata['status'] == 1){echo 'checked'; } else {echo 'unchecked'; }} else {echo 'checked'; } ?>>
                                </div>
                            </div>

                            
                              
                            </div>
                            <div class="form-group">
                              <label for="focusedinput" class="col-sm-3 control-label"></label>
                              <div class="col-sm-8">
                                <?php if(!empty($catalogdata)){ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
                                <?php }else{ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised">
                                <?php } ?>
                              </div>
                            </div>
                            
                         </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>   						