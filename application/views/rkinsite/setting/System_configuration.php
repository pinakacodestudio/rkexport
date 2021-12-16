<style>
hr{
  width: 100%;
  float: left;
}

.datetimepicker-dropdown-bottom-left{
  left:1054px !important;
}


</style>
<script type="text/javascript">
  var MAIN_LOGO_IMAGE_URL = '<?=MAIN_LOGO_IMAGE_URL?>';
  
</script>

<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
    

    <div data-widget-group="group1">
      <div class="row">
              <div class="col-sm-12 col-md-12 col-lg-12">
                 <form class="form-horizontal" id="settingform">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="panel panel-default border-panel">
                        <div class="panel-heading">
                          <h2>General Configuration</h2>
                        </div>
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="col-sm-4 control-label pr-n">Stock Manage By</label>
                                <div class="col-md-8">
                                  <div class="col-md-4 col-xs-4 p-n" style="padding-left: 0px;">
                                    <div class="radio m-n">
                                      <input type="radio" name="stockmanageby" id="stockmanagebynormal" value="0" <?php if(isset($settingdata) && $settingdata['stockmanageby']==0){ echo 'checked'; }?>>
                                      <label for="stockmanagebynormal">Normal</label>
                                    </div>
                                  </div>
                                  <div class="col-md-6 col-xs-4 p-n">
                                    <div class="radio m-n">
                                      <input type="radio" name="stockmanageby" id="stockmanagebyfifo" value="1" <?php if(isset($settingdata) && $settingdata['stockmanageby']==1){ echo 'checked'; }?>>
                                      <label for="stockmanagebyfifo">FIFO</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group" id="brandingurl_div">
                                <label for="brandingurl" class="col-sm-4 control-label pr-n">Branding URL <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                  <input id="brandingurl" type="text" name="brandingurl" value="<?php if(!empty($settingdata)){ echo $settingdata['brandingurl']; } ?>" class="form-control">
                                </div>
                              </div>
                              <div class="form-group" <?php if(!is_null($this->session->userdata(base_url().'ADMINUSERTYPE')) && $this->session->userdata(base_url().'ADMINUSERTYPE')!=1){ ?> style="display:none;" <?php } ?>>
                                <label class="col-sm-4 control-label pr-n" for="brandinglogo">Branding Logo <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                  <input type="hidden" name="oldbrandinglogo" id="oldbrandinglogo" value="<?php if($settingdata['brandinglogo']!=""){ echo $settingdata['brandinglogo']; } ?>">
                                  <?php if(isset($settingdata) && $settingdata['brandinglogo']!=""){ ?>
                                    <div class="imageupload" id="brandinglogofile">
                                      <div class="file-tab">
                                        <img src="<?php echo MAIN_LOGO_IMAGE_URL.$settingdata['brandinglogo']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                        <label id="brandinglogolabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                          <span id="brandinglogobtn">Change</span>
                                          <!-- The file is stored here. -->
                                          <input type="file" name="brandinglogo" id="brandinglogo"
                                            accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
                                        </label>
                                        <button type="button" class="btn btn-sm btn-danger btn-raised"
                                        style="display: inline-block;">Remove</button>
                                      </div>
                                    </div>
                                  <?php }else{ ?>
                                    <div class="imageupload" id="brandinglogofile">
                                      <div class="file-tab">
                                        <img src="" alt="Image preview" class="thumbnail"
                                          style="max-width: 150px; max-height: 150px;">
                                        <label id="brandinglogolabel"
                                          class="btn btn-sm btn-primary btn-raised btn-file">
                                          <span id="brandinglogobtn">Select Image</span>
                                          <input type="file" name="brandinglogo" id="brandinglogo"
                                            value=""
                                            accept=".jpeg,.png,.jpg,.ico,.JPEG,.PNG,.JPG">
                                        </label>
                                        <button type="button"
                                          class="btn btn-sm btn-danger btn-raised">Remove</button>
                                      </div>
                                    </div>
                                  <?php } ?>
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="focusedinput" class="col-sm-2 control-label"></label>
                                <div class="col-sm-3 pr-n">
                                  <div class="checkbox text-left">
                                    <input type="checkbox" name="brandingallow" class="checkradios" type="checkbox" id="brandingallow" onchange="getallow()" onclick="$(this).attr('value', this.checked ? 1 : 0)" value="<?php if($settingdata['brandingallow'] == "1"){echo "1";}else{echo "0";}?>" <?php if($settingdata['brandingallow'] == "1"){echo "checked";}?>>
                                    <label for="brandingallow">Branding</label>
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="row">
                                    <div class="col-md-6 col-xs-4 p-n" style="padding-left: 0px;">
                                      <div class="radio m-n">
                                        <input type="radio" name="brandingtype" id="poweredby" value="1" <?php if(isset($settingdata) && $settingdata['brandingtype']==1){ echo 'checked'; }else{ echo 'checked'; }?> <?php if(isset($settingdata) && $settingdata['brandingallow'] == "0"){echo "disabled";}?>>
                                        <label class="mb-n" for="poweredby">Powered By</label>
                                      </div>
                                    </div>
                                    <div class="col-md-6 col-xs-4 p-n">
                                      <div class="radio m-n">
                                        <input type="radio" name="brandingtype" id="pioneeredby" value="0" <?php if(isset($settingdata) && $settingdata['brandingtype']==0){ echo 'checked'; }?> <?php if(isset($settingdata) && $settingdata['brandingallow'] == "0"){echo "disabled";}?>>
                                        <label class="mb-n" for="pioneeredby">Pioneered By</label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group" id="footer_div">
                                <label for="focusedinput" class="col-sm-2 control-label"></label>
                                <div class="col-sm-8">
                                  <div class="checkbox text-left">
                                    <input type="checkbox" name="footer" class="checkradios" type="checkbox" id="footerallow" value="<?php if($settingdata['footer'] == "1"){echo "1";}else{echo "0";}?>" <?php if($settingdata['footer'] == "1"){echo "checked";}?>>
                                    <label for="footerallow">Footer</label>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group" id="copyright_div">
                                <label for="focusedinput" class="col-sm-2 control-label"></label>
                                <div class="col-sm-8">
                                  <div class="checkbox text-left">
                                    <input type="checkbox" name="copyright" class="checkradios" type="checkbox" id="copyright" value="<?php if($settingdata['copyright'] == "1"){echo "1";}else{echo "0";}?>" <?php if($settingdata['copyright'] == "1"){echo "checked";}?>>
                                    <label for="copyright">Copyright</label>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group">
                                <label for="androidversion" class="col-sm-4 control-label pr-n">Android Version </label>
                                <div class="col-sm-8">
                                  <input type="text" class="form-control" name="androidversion" id="androidversion" value="<?php if(isset($settingdata) && isset($androidversion)){ echo $androidversion; }?>">
                                </div>
                              </div>
                              <div class="form-group">
                               <label for="iosversion" class="col-sm-4 control-label pr-n">iOS Version </label>
                                <div class="col-sm-8">
                                  <input type="text" class="form-control" name="iosversion" id="iosversion" value="<?php if(isset($settingdata) && isset($iosversion)){ echo $iosversion; }?>">
                                </div>
                              </div>
                              <div class="form-group">
                                <label for="registeraschannelidinapp" class="col-sm-4 control-label pr-n">Register as in App </label>
                                <div class="col-sm-8">
                                  <select id="registeraschannelidinapp" name="registeraschannelidinapp" class="selectpicker form-control" data-size="6" data-live-search="true">
                                    <option value="0">Select Channel</option>
                                    <?php foreach($registerasappchanneldata as $cd){ ?>
                                    <option value="<?php echo $cd['id']; ?>" <?php if(isset($settingdata)){ if($settingdata['registeraschannelidinapp'] == $cd['id']){ echo 'selected'; } }  ?>><?php echo $cd['name']; ?></option>
                                    <?php } ?>
                                  </select>
                                </div>
                              </div>
                            
                              <div class="form-group" id="expirydate_div">
                                <label for="startdate" class="col-sm-4 control-label pr-n">Licence Date <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                  <div class="input-daterange input-group" id="datepicker-range" >
                                    <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(!empty($settingdata) && $settingdata['startdate']!="0000-00-00"){ echo $this->general_model->displaydate($settingdata['startdate']); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="input-small form-control" name="expirydate" id="expirydate" value="<?php if(!empty($settingdata) && $settingdata['expirydate']!="0000-00-00"){ echo $this->general_model->displaydate($settingdata['expirydate']); } ?>" placeholder="End Date" title="End Date" readonly/>
                                  </div>
                                </div>
                              </div>

                              <div class="form-group" id="maintenancedatetime_div">
                                <label for="startdatetime" class="col-sm-4 control-label pr-n">Maintenance Date Time </label>
                                <div class="col-sm-8">
                                  <div class="input-datetimerange input-group" id="datetimepicker-range" style="width:auto;"  >
                                    <div class="form-group" id="startdatetime_div">
                                    <div class="col-sm-12">
                                    <input type="text" class="input-datetime form-control" name="startdatetime" id="startdatetime" value="<?php if(!empty($settingdata) && $settingdata['maintenancestartdatetime']!="0000-00-00 00:00:00"){ echo date('d/m/Y h:i a',strtotime($settingdata['maintenancestartdatetime'])); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                                    </div>
                                    </div>
                                    <span class="input-group-addon">to</span>
                                    <div class="form-group" id="expirydatetime_div">
                                    <div class="col-sm-12">
                                    <input type="text" class="input-datetime form-control" name="expirydatetime" id="expirydatetime" value="<?php if(!empty($settingdata) && $settingdata['maintenanceexpirydatetime']!="0000-00-00 00:00:00"){ echo date('d/m/Y h:i a',strtotime($settingdata['maintenanceexpirydatetime'])); } ?>" placeholder="End Date" title="End Date" readonly/>
                                    </div>
                                    </div>
                                  </div>
                                </div>
                                
                                <div class="col-sm-2 col-sm-offset-10">
                                <button type="button" id="cleardatebtn" class="btn btn-primary btn-raised btn-xs pull-right" href="javascript:void(0)" onclick="$('#startdatetime').val('');$('#expirydatetime').val('');">Clear Date</button>
                                  
                                </div>
                                
                              </div>
                              
                              <div class="form-group" id="storagespace_div">
                                <label for="storagespace" class="col-sm-4 control-label pr-n">Storage Space (GB) <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                  <input type="text" id="storagespace" name="storagespace" class="form-control" value="<?php if(isset($settingdata) && $settingdata['storagespace']!=0){ echo $settingdata['storagespace']; } ?>" onkeypress="return decimal_number_validation(event,this.value,8)">
                                </div>
                              </div>

                              <div class="form-group" id="noofproduct_div">
                                <label for="noofproduct" class="col-sm-4 control-label pr-n">No Of Product  <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                  <input type="text" id="noofproduct" name="noofproduct" class="form-control" value="<?php if(isset($settingdata) && $settingdata['noofproduct']!=0){ echo $settingdata['noofproduct']; } ?>"  onkeypress="return isNumber(event)">
                                </div>
                              </div>
                            </div>
                            <hr>
                            <div class="row">
                              
                              <div class="form-group col-md-4">
                                <label for="focusedinput" class="col-sm-9 control-label">Website </label>
                                <div class="col-sm-3">
                                  <div class="yesno">
                                        <input type="checkbox" name="website" value="<?php if(isset($settingdata) && $settingdata['website']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($settingdata) && $settingdata['website']==1){ echo 'checked'; }?>>
                                  </div>
                                </div>
                              </div>
                            
                              <div class="form-group col-md-4">
                                <label for="sms" class="col-sm-9 control-label">SMS</label>
                                <div class="col-sm-3">
                                  <div class="yesno">
                                    <input type="checkbox" name="sms" value="<?php if(isset($settingdata) && $settingdata['sms']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($settingdata) && $settingdata['sms']==1){ echo 'checked'; }?>>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-12">
                      <div class="panel panel-default border-panel">
                        <div class="panel-heading">
                          <h2>AWS Configuration</h2>
                        </div>
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label class="col-md-4 control-label">Allow S3 Upload</label>
                                <div class="col-md-8">
                                  <div class="col-md-4 col-xs-6" style="padding-left: 0px;">
                                    <div class="radio">
                                      <input type="radio" name="allows3" id="allow" value="1" <?php if(isset($settingdata) && $settingdata['allows3']==1){ echo 'checked'; }?>>
                                      <label for="allow">Allow</label>
                                    </div>
                                  </div>
                                  <div class="col-md-4 col-xs-6">
                                    <div class="radio">
                                      <input type="radio" name="allows3" id="deny" value="0" <?php if(isset($settingdata) && $settingdata['allows3']==0){ echo 'checked'; }?>>
                                      <label for="deny">Deny</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group" id="bucketname_div">
													      <label for="bucketname" class="col-md-4 control-label">Bucket Name <span class="mandatoryfield">*</span></label>
													      <div class="col-md-8">
														      <input id="bucketname" type="text" name="bucketname" value="<?php if(isset($settingdata)){ echo $settingdata['bucketname']; }  ?>" class="form-control">
													      </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group" id="clientname_div">
													      <label for="clientname" class="col-md-4 control-label">Client Name <span class="mandatoryfield">*</span></label>
													      <div class="col-md-8">
														      <input id="clientname" type="text" name="clientname" value="<?php if(isset($settingdata)){ echo $settingdata['clientname']; }  ?>" class="form-control">
													      </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group" id="commonbucket_div">
													      <label for="commonbucket" class="col-md-4 control-label">Common Bucket <span class="mandatoryfield">*</span></label>
													      <div class="col-md-8">
														      <input id="commonbucket" type="text" name="commonbucket" value="<?php if(isset($settingdata)){ echo $settingdata['commonbucket']; }  ?>" class="form-control">
													      </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group" id="iamkey_div">
													      <label for="iamkey" class="col-md-4 control-label">IAM Key <span class="mandatoryfield">*</span></label>
													      <div class="col-md-8">
														      <input id="iamkey" type="text" name="iamkey" value="<?php if(isset($settingdata)){ echo $settingdata['iamkey']; }  ?>" class="form-control">
													      </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group" id="iamsecret_div">
													      <label for="iamsecret" class="col-md-4 control-label">IAM Secret <span class="mandatoryfield">*</span></label>
													      <div class="col-md-8">
														      <input id="iamsecret" type="text" name="iamsecret" value="<?php if(isset($settingdata)){ echo $settingdata['iamsecret']; }  ?>" class="form-control">
													      </div>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group" id="region_div">
													      <label for="region" class="col-md-4 control-label">Region <span class="mandatoryfield">*</span></label>
													      <div class="col-md-8">
														      <input id="region" type="text" name="region" value="<?php if(isset($settingdata)){ echo $settingdata['region']; }  ?>" class="form-control">
													      </div>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group" id="awslink_div">
													      <label for="awslink" class="col-md-4 control-label">AWS Link <span class="mandatoryfield">*</span></label>
													      <div class="col-md-8">
														      <input id="awslink" type="text" name="awslink" value="<?php if(isset($settingdata)){ echo $settingdata['awslink']; }  ?>" class="form-control">
													      </div>
                              </div>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
 
                  <div class="row">
                    <div class="col-md-12">
                      <div class="panel panel-default border-panel">
                        <div class="panel-heading">
                          <h2>Action</h2>
                        </div>
                        <div class="panel-body">
                          <div class="row">
                            <div class="col-md-12" style="text-align: center;">
                              <div class="form-group">
                              <button type="button" id="synctoaws" class="btn btn-success btn-raised">Sync To AWS</button>
                                <?php if(isset($settingdata)){ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                <?php }else{ ?>
                                  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-info btn-raised">
                                  <input type="reset" name="reset" value="RESET" class="btn btn-primary btn-raised" onclick="resetdata()">
                                <?php } ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </form>
              </div>
      </div>
    </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->