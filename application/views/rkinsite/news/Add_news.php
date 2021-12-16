<script>
  var memberidarr = '<?php if(!empty($memberidarr)){ echo implode(",",$memberidarr); } ?>';
</script>
<script>
	var NEWS = "<?=NEWS?>";
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($newsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($newsdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-body">
                <div class="col-sm-12">
                    <form class="form-horizontal offset-md-1" id="form-news" enctype="multipart/form-data">
                     <input type="hidden" name="newsid" id="newsid" value="<?php if(isset($newsdata)){ echo $newsdata['id']; } ?>">    
                      
                     <div class="row">
                        <div class="col-md-6">
                          <div class="form-group" id="newsname_div">
                            <label class="col-md-4 control-label" for="newsname">
                                 News Title
                             <span class="mandatoryfield"> * </span></label>
                              <div class="col-md-8">
                                <input type="text" id="newsname" class="form-control" name="newsname" value="<?php if(isset($newsdata)){ echo $newsdata['title']; } ?>">
                              </div>
                         </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group" id="link_div">
                            <label for="link" class="col-sm-4 control-label">News URL <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                            <input id="link" type="text" name="link" value="<?php if(!empty($newsdata)){ echo $newsdata['link']; } ?>" class="form-control">
                            </div>
                          </div> 
                        </div>
                     </div>
                        <div class="row mb-sm">
                              <div class=col-md-12">
                                <div class="col-md-6">
                                  <div class="form-group" id="channel_div">
                                      <label class="col-md-4 control-label" for="channelid">
                                    Select Channel <span class="mandatoryfield"> * </span></label>
                                      <input type="hidden" value="<?php if(isset($channelidarr)){ echo implode(",",$channelidarr); } ?>" name="oldchannelid"></label>
                                      <div class="col-md-8">
                                      <select class="form-control selectpicker" id="channelid" name="channelid[]" title="Select Channel" data-actions-box="true" multiple <?php //if(!empty($newsdata)){ echo "disabled"; } ?>>
                                          <?php foreach($channeldata as $row){ ?>
                                            <option value="<?php echo $row['id']; ?>" <?php if(isset($channelidarr)){ if(in_array($row['id'],$channelidarr)){ echo 'selected'; } } ?>><?php echo $row['name']; ?></option> 
                                            <?php } ?>
                                      </select>  
                                      </div>
                                 </div>
                                <div class="form-group" id="brand_div">
                                  <label class="col-md-4 control-label" for="brandid">Select Brand</label>
                                  <div class="col-md-8">
                                    <select class="form-control selectpicker" id="brandid" name="brandid" data-live-search="true" data-actions-box="true" data-select-on-tab="true" data-size="5">
                                      <option value="0">Select Brand</option>
                                        <?php foreach($branddata as $brand){ ?>
                                          <option value="<?=$brand['id']?>" <?=((isset($newsdata) && $newsdata['brandid']==$brand['id'])?"selected":"")?>><?=$brand['name']?></option>
                                      <?php } ?>
                                    </select>
                                </div>
                            </div>
                                 <div class="form-group" id="member_div">
                                      <label class="col-md-4 control-label" for="memberid">Select <?=Member_label?> <span class="mandatoryfield"> * </span></label>
                                      <input type="hidden" value="<?php if(isset($memberidarr)){ echo implode(",",$memberidarr); } ?>" name="oldmemberid"></label>
                                      <div class="col-md-8">
                                        <select id="memberid" name="memberid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" multiple data-actions-box="true" title="Select <?=Member_label?>" <?php //if(!empty($newsdata)){ echo "disabled"; } ?>>
                                        </select>
                                      </div>
                                   </div> 
                                   <div class="col-md-12">
                                  <div class="form-group" id="category_div">
                                  <label for="newscategoryid" class="col-md-4 control-label">News Category</label>
                                   <div class="col-md-8">
                                             <!-- <input id="newscategoryid" type="text" name="newscategoryid" value="<?php if(isset($newsdata)){ echo $newsdata['newscategoryid']; } ?>" data-provide="newscategoryid"> -->
                                          <select id="newscategoryid" name="newscategoryid" class="selectpicker form-control" data-live-search="true" data-size="5" tabindex="8">
                                            <option value="0">Select News Category</option>
                                            <?php foreach($newscategorydata as $newscategory){ ?>
                                              <option value="<?php echo $newscategory['id']; ?>" <?php if(isset($newsdata)){ if($newsdata['newscategoryid'] == $newscategory['id']){ echo 'selected'; } } ?>><?=$newscategory['name']?></option>
                                            <?php } ?>
                                          </select>
                                    </div>
                                 </div>
                        
                            
                          </div>
                              </div>
                          </div>
                           <!--image-->
                             <div class=col-md-4>                                                              
                               <div class="form-group" >
                                            <label for="focusedinput" class="col-md-6 control-label">Select Image </label>
                                          <div class="col-md-6">
                                            <input type="hidden" name="oldnewsimage" id="oldnewsimage" value="<?php if(isset($newsdata)){ echo $newsdata['image']; }?>">
                                            <input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
                                            <?php if(isset($newsdata) && $newsdata['image']!=''){ ?>
                                                     <div class="imageupload" id="newsimage">
                                                        <div class="file-tab"><img src="<?php echo NEWS.$newsdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                                            <label id="newsimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                                                <span id="newsimagebtn">Change</span>
                                                                <!-- The file is stored here. -->
                                                                <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                            </label>
                                                            <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
                                                        </div>
                                                    </div>
                                                  <?php }else{ ?>
                                                  <!-- <script type="text/javascript"> var ACTION = 0;</script> -->
                                                   <div class="imageupload">
                                                      <div class="file-tab">
                                                        <img src="" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px;">
                                                          <label for="image" id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                                              <span id="newsimagebtn">Select Image</span>
                                                              <input type="file" name="image" id="image"  accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
                                                          </label>
                                                          <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
                                                      </div>
                                                  </div>
                                                <?php } ?>
                                                  </div>              
                                                </div> 							
                                            </div>	
                                        </div>
                                          <div class="row">
                                            <div class="col-md-12">
                                              <div class="form-group" id="description_div">
                                                <div id='termscontainer'>
                                                      <label for="focusedinput" class="col-md-2 control-label"  for="description">Content <span class="mandatoryfield">*</span></label>
                                                      <div class="col-sm-10">
                                                            <?php $data['controlname']="description";if(isset($newsdata) && !empty($newsdata)){$data['controldata']=$newsdata['description'];} ?>
                                                            <?php $this->load->view(ADMINFOLDER.'includes/ckeditor',$data);?>
                                                      </div>
                                                  </div>
                                              </div>                            
                                            <hr>
                                            </div>
                                    <!--meta data-->
                                    <div class="col-md-12">       
                                        <div class="form-group">
                                                <label for="focusedinput" class="col-sm-5 col-xs-4 control-label"></label>
                                                <div class="col-sm-6 col-xs-8">
                                                    <div class="col-sm-3 col-xs-6" style="padding-left: 0px;">
                                                        <div class="checkbox">
                                                        
                                                            <input type="checkbox" name="forwebsite" id="forwebsite"   value="1" <?php if(empty($newsdata)){echo 'checked';}?> <?php if(isset($newsdata) && $newsdata['forwebsite']==1){ echo 'checked';}?>>
                                                            <label  style="font-size: 14px;" for="forwebsite">For Website</label>
                                                          </div>
                                                      </div>
                                                        <div class="col-sm-6 col-xs-6">
                                                          <div class="checkbox">
                                                              <input type="checkbox" name="forapp" id="forapp"  value="1"   <?php if(empty($newsdata)){echo 'checked';}?> <?php if(isset($newsdata) && $newsdata['forapp']==1){ echo 'checked';}?>>
                                                              <label  style="font-size: 14px;margin-left: 25px;" for="forapp">For App</label>
                                                            </div>
                                                        </div>
                                          </div>
                                        </div>                
                                      <div class="form-group" id="metatitle_div">
                                        <label for="metatitle" class="col-md-5 control-label">Meta Title</label>
                                        <div class="col-md-4">
                                        <textarea id="metatitle" name="metatitle" class="form-control"><?php if(isset($newsdata)){ echo $newsdata['metatitle']; } ?></textarea>
                                        </div>
                                      </div>
                                      <div class="form-group" id="metakeywords_div">
                                      <label for="focusedinput" class="col-sm-5 control-label">Meta Keywords</label>
                                      <div class="col-sm-6">
                                        <input id="metakeywords" type="text" name="metakeywords" value="<?php if(isset($newsdata)){ echo $newsdata['metakeywords']; } ?>" data-provide="metakeywords">
                                      </div>
                                    </div>
                                      <div class="form-group" id="metadescription_div">
                                        <label for="metadescription" class="col-md-5 control-label">Meta Description</label>
                                        <div class="col-md-4">
                                          <textarea id="metadescription" name="metadescription" class="form-control"><?php if(isset($newsdata)){ echo $newsdata['metadescription']; } ?></textarea>
                                        </div>
                                      </div>
                                  </div>
                                </div>
                          <!--status-->                                  
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>   	
</div>
</div>    
<script>
    $(document).ready(function(){
        $('input[type="checkbox"]').click(function(){
            if($(this).prop("checked") == true){
                console.log("Checkbox is checked.");
            }
            
        });
    });
</script>					