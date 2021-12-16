<script type="text/javascript">
  var profileimgpath = '<?php echo PROFILE;?>';
  var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE;?>';
  var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE;?>';
  var parentmemberid = '<?php if(isset($memberdata) && !is_null($memberdata['parentmemberid'])){ echo $memberdata['parentmemberid']; } ?>';
  var sellermemberid = '<?php if(isset($memberdata) && !is_null($memberdata['sellermemberid'])){ echo $memberdata['sellermemberid']; } ?>';
  var countryid = '<?php if(isset($memberdata) && !isset($CLONE) && !empty($memberdata['countryid'])){ echo $memberdata['countryid']; }else{ echo DEFAULT_COUNTRY_ID; } ?>';
  var provinceid = '<?php if(isset($memberdata) && !isset($CLONE) && !empty($memberdata['provinceid'])){ echo $memberdata['provinceid']; }else{ echo 0; } ?>';
  var cityid = '<?php if(isset($memberdata) && !isset($CLONE) && !empty($memberdata['cityid'])){ echo $memberdata['cityid']; }else{ echo 0; } ?>';
  var areaid = '<?php if(isset($memberdata) && !isset($CLONE) && !empty($memberdata['areaid'])){ echo $memberdata['areaid'];}else { echo 0; }?>';
  var sellerchannelid = '<?php if(isset($memberdata) && !empty($memberdata['sellerchannelid'])){ echo $memberdata['sellerchannelid']; }else{ echo 0; } ?>';
  var countrycodeid = '<?php if(isset($memberdata) && !isset($CLONE) && !empty($memberdata['countrycode'])){ echo $memberdata['countrycode']; }else{ echo DEFAULT_PHONECODE; } ?>';
  var GUESTCHANNELID = '<?=GUESTCHANNELID?>';
  var VENDORCHANNELID = '<?=VENDORCHANNELID?>';
  var CUSTOMERCHANNELID = '<?=CUSTOMERCHANNELID?>';
  var NOOFUSERINCHANNEL = '<?=NOOFUSERINCHANNEL?>';
  var defaultcashorbankid = '<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['defaultcashorbankid']; }else{ echo 0; } ?>';
  var defaultbankmethod = '<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['defaultbankmethod']; }else{ echo 0; } ?>';
  var salespersonid  = '<?php if (isset($memberdata) && !empty($memberdata['employeeids'])){echo $memberdata['employeeids']; }else{ echo 0; } ?>';
 
</script>
<?php
$displayonlybalancefields = 1;
if(!empty($memberdata) && !isset($CLONE)){
  $MEMBERID = (!empty($this->session->userdata(base_url().'MEMBERID')))?$this->session->userdata(base_url().'MEMBERID'):0;
  if($memberdata['parentmemberid']!=$MEMBERID && $memberdata['sellermemberid']!=$MEMBERID){
    $displayonlybalancefields = 0;
  }
}
?>
<style>
    .rate
    {
        font-size: 35px;
    }
    .rate .rate-hover-layer
    {
        color: orange;
    }
    .rate .rate-select-layer
    {
        color: orange;
    }
    .radio1 label {
      font-size: 18px !important;
    }
    .radio1 label:before {
        bottom: 5.52px !important;
    }
</style>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($memberdata) && !isset($CLONE)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($memberdata) && !isset($CLONE)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
    </small>
    </div>

    <div class="container-fluid">
                                    
        <div data-widget-group="group1">
          <div class="row">
            <form action="#" id="memberform" class="form-horizontal">
              <div class="col-md-12">
                <div class="panel panel-default border-panel">
                  <div class="panel-body pt-n">
                      <input type="hidden" id="id" name="id" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['id']; } ?>">
                      <input type="hidden" name="mainmemberid" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['sellermemberid']; } ?>">
                      <input type="hidden" name="balanceid" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['balanceid']; } ?>">

                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-md-6 p-n">
                            <div class="form-group" id="channelid_div">
                              <label class="col-sm-4 control-label" for="channelid">New <?=Member_label?> Channel <span class="mandatoryfield">*</span></label>
                              <div class="col-sm-8">
                                <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                  <option value="0">Select Channel</option>
                                  <?php foreach($channeldata as $cd){ ?>
                                  <option value="<?php echo $cd['id']; ?>" <?php if(isset($memberdata)){ if($memberdata['channelid'] == $cd['id']){ echo 'selected'; } }  ?>><?php echo $cd['name']; ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 p-n">
                            <div class="form-group row" id="name_div">
                              <label class="control-label col-md-4" for="name">Name <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                <input id="name" class="form-control" name="name" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['name']; } ?>" type="text" onkeypress="return onlyAlphabets(event)"  onkeyup="$('#addressname').val(this.value)">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-md-6 p-n">
                            <div class="form-group" id="sellerchannelid_div">
                              <label class="col-sm-4 control-label" for="sellerchannelid">Seller Channel <span class="mandatoryfield">*</span></label>
                              <div class="col-sm-8">
                                <select id="sellerchannelid" name="sellerchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                                  <option value="-1">Select Channel</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 p-n">
                            <div class="form-group" id="sellermember_div">
                              <label class="col-sm-4 control-label" for="sellermemberid">Seller <?=Member_label?> <span class="mandatoryfield">*</span></label>
                              <div class="col-sm-8">
                                <select id="sellermemberid" name="sellermemberid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="2" data-live-search="true">
                                  <option value="0">Select <?=Member_label?></option>
                                </select>
                                <p class="m-n">*This field is not required when seller channel as company.</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-md-6 p-n">
                            <div class="form-group" id="parentchannelid_div">
                              <label class="col-sm-4 control-label" for="parentchannelid">Referral Channel</label>
                              <div class="col-sm-8">
                                <select id="parentchannelid" name="parentchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                  <option value="-1">Select Channel</option>
                                  <option value="0" <?php if(isset($memberdata) && $memberdata['parentchannelid'] == 0){ echo 'selected'; }  ?>>Company</option>
                                  <?php foreach($channeldata as $cd){ ?>
                                  <option value="<?php echo $cd['id']; ?>" <?php if(isset($memberdata) && $memberdata['parentchannelid'] == $cd['id']){ echo 'selected'; }  ?>><?php echo $cd['name']; ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 p-n">
                            <div class="form-group p-n" id="parentmember_div">
                              <label class="col-sm-4 control-label" for="parentmemberid">Referral <?=Member_label?></label>
                              <div class="col-sm-8">
                                <select id="parentmemberid" name="parentmemberid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" tabindex="2" data-live-search="true">
                                  <option value="0">Select <?=Member_label?></option>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-md-6 p-n">
                            <div class="form-group" id="membercode_div">
                                <label class="control-label col-md-4" for="membercode"><?=Member_label?> Code <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                  <div>
                                    <div class="col-sm-10" style="padding: 0px;">
                                      <input id="membercode" type="text" name="membercode" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['membercode']; } ?>" class="form-control" maxlength="8">
                                    </div>
                                    <div class="col-sm-2" style="padding-right: 0px;">
                                      <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Code" onclick="$('#membercode').val(randomPassword(8,8,0,0,0))"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                  </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6 p-n">
                            <div class="form-group" id="roleid_div">
                              <label class="col-sm-4 control-label" for="roleid"><?=Member_label?> Role <span class="mandatoryfield">*</span></label>
                              <div class="col-sm-8">
                                <select id="roleid" name="roleid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                  <option value="0">Select Role</option>
                                  <?php foreach($memberroledata as $rolerow){ ?>
                                  <option value="<?php echo $rolerow['id']; ?>" <?php if(isset($memberdata) && $memberdata['roleid'] == $rolerow['id']){ echo 'selected'; }  ?>><?php echo $rolerow['role']; ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-md-6 p-n">
                            <div class="form-group row" id="mobile_div">
                              <label class="control-label col-md-4" for="countrycodeid">Primary Mobile No. <span class="mandatoryfield">*</span></label>  
                              <div class="col-md-8">
                                <div class="row">
                                  <div class="col-md-4 pr-sm">
                                    <select id="countrycodeid" name="countrycodeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                      <option value="0">Code</option>
                                      <?php foreach($countrycodedata as $countrycoderow){ ?>
                                      <option value="<?php echo $countrycoderow['phonecode']; ?>" <?php if(isset($memberdata) && !isset($CLONE) && $memberdata['countrycode']==$countrycoderow['phonecode']){ echo 'selected'; }else{ if(DEFAULT_PHONECODE==$countrycoderow['phonecode']){ echo 'selected'; } } ?>><?php echo $countrycoderow['phonecode']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                  <div class="col-md-8 pl-sm">
                                    <input id="mobileno" type="text" name="mobileno" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['mobile']; } ?>" class="form-control" maxlength="10"  onkeypress="return isNumber(event)">
                                  </div>
                                  <div class="col-md-12 pr-sm">
                                    <div class="checkbox">
                                      <input id="isprimarywhatsappno" type="checkbox" value="1" name="isprimarywhatsappno" class="checkradios" <?php if(!isset($memberdata) || (isset($memberdata) && $memberdata['isprimarywhatsappno'] == 1)){ echo 'checked'; } ?>>
                                      <label for="isprimarywhatsappno">Is Primary Whatsapp No.</label>          
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group row" id="secondarymobileno_div">
                              <label class="control-label col-md-4" for="secondarymobileno">Secondary Mobile No.</label>  
                              <div class="col-md-8">
                                <div class="row">
                                  <div class="col-md-4 pr-sm">
                                    <select id="secondarycountrycodeid" name="secondarycountrycodeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                      <option value="0">Code</option>
                                      <?php foreach($countrycodedata as $countrycoderow){ ?>
                                      <option value="<?php echo $countrycoderow['phonecode']; ?>" <?php if(isset($memberdata) && !isset($CLONE) && $memberdata['secondarycountrycode']==$countrycoderow['phonecode']){ echo 'selected'; }else{ if(DEFAULT_PHONECODE==$countrycoderow['phonecode']){ echo 'selected'; } } ?>><?php echo $countrycoderow['phonecode']; ?></option>
                                      <?php } ?>
                                    </select>
                                  </div>
                                  <div class="col-md-8 pl-sm">
                                    <input id="secondarymobileno" type="text" name="secondarymobileno" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['secondarymobileno']; } ?>" class="form-control" maxlength="10"  onkeypress="return isNumber(event)">
                                  </div>
                                  <div class="col-md-12 pr-sm">
                                    <div class="checkbox">
                                      <input id="issecondarywhatsappno" type="checkbox" value="1" name="issecondarywhatsappno" class="checkradios" <?php if(!isset($memberdata) || (isset($memberdata) && $memberdata['issecondarywhatsappno'] == 1)){ echo 'checked'; } ?>>
                                      <label for="issecondarywhatsappno">Is Secondary Whatsapp No.</label>          
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="form-group row" id="email_div">
                              <label class="control-label col-md-4" for="email">Email <span class="mandatoryfield">*</span></label>
                              <div class="col-md-8">
                                <input id="email" type="text" name="email" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['email']; } ?>" class="form-control">
                              </div>
                            </div>
                            <div class="form-group" id="password_div">
                                <label class="control-label col-md-4" for="password">Password <span class="mandatoryfield">*</span></label>
                                <div class="col-md-8">
                                  <div>
                                    <div class="col-sm-10" style="padding: 0px;">
                                      <input id="password" type="text" name="password" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $this->general_model->decryptIt($memberdata['password']); } ?>" class="form-control">
                                    </div>
                                    <div class="col-sm-2" style="padding-right: 0px;">
                                      <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Password" onclick="$('#password').val(randomPassword())"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                    </div>
                                  </div>
                              </div>
                            </div>
                            <div class="form-group" id="country_div">
                                <label class="col-sm-4 control-label" for="countryid">Country <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <select id="countryid" name="countryid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Country</option>
                                        <?php foreach($countrydata as $country){ ?>
                                        <option value="<?php echo $country['id']; ?>" <?php if(isset($memberdata) && !isset($CLONE) && $memberdata['countryid']==$country['id']){ echo 'selected'; }else{ if(DEFAULT_COUNTRY_ID==$country['id']){ echo 'selected'; } } ?>><?php echo $country['name']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="province_div">
                                <label class="col-sm-4 control-label" for="provinceid">Province <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Province</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="city_div">
                                <label class="col-sm-4 control-label" for="cityid">City <span class="mandatoryfield">*</span></label>
                                <div class="col-sm-8">
                                    <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select City</option>
                                    </select>
                                </div>
                            </div>
                            <?php if(isset($memberdata) && !isset($CLONE)){?>
                            <div class="form-group" id="billingaddress_div">
                              <label class="col-sm-4 control-label" for="billingaddressid">Default Billing Address</label>
                              <div class="col-sm-8">
                                  <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                      <option value="0">Select Billing Address</option>
                                      <?php foreach($addressdata as $address){ ?>
                                      <option value="<?php echo $address['id']; ?>" <?php if(isset($memberdata) && $memberdata['billingaddressid']==$address['id']){ echo 'selected'; }?>><?php echo ucfirst($address['address']); ?>
                                      </option>
                                      <?php } ?>
                                  </select>
                              </div>
                            </div>
                            <div class="form-group" id="shippingaddress_div">
                              <label class="col-sm-4 control-label pl-xs" for="billingaddressid">Default Shipping Address</label>
                              <div class="col-sm-8">
                                  <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                      <option value="0">Select Shipping Address</option>
                                      <?php foreach($addressdata as $address){ ?>
                                      <option value="<?php echo $address['id']; ?>" <?php if(isset($memberdata) && $memberdata['shippingaddressid']==$address['id']){ echo 'selected'; }?>><?php echo ucfirst($address['address']); ?>
                                      </option>
                                      <?php } ?>
                                  </select>
                              </div>
                            </div>
                            <div class="form-group" id="defaultcashorbank_div">
                              <label class="col-sm-4 control-label" for="defaultcashorbankid">Default Cash or Bank</label>
                              <div class="col-sm-8">
                                  <select id="defaultcashorbankid" name="defaultcashorbankid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                      <option value="0">Select Cash or Bank</option>
                                      <?php foreach($bankdata as $bank){ ?>
                                      <option value="<?php echo $bank['id']; ?>" <?php if(isset($memberdata) && $memberdata['defaultcashorbankid']==$bank['id']){ echo 'selected'; }?>><?php echo $bank['bankname']; ?>
                                      </option>
                                      <?php } ?>
                                  </select>
                              </div>
                            </div>
                            <div class="form-group" id="defaultbankmethod_div">
                              <label class="col-sm-4 control-label" for="defaultbankmethod">Default Method</label>
                              <div class="col-sm-8">
                                  <select id="defaultbankmethod" name="defaultbankmethod" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                      <option value="0">Select Method</option>
                                      <?php foreach($this->Bankmethod as $key=>$value){ ?>
                                          <option value="<?=$key?>" <?php if(isset($memberdata)){ if($memberdata['defaultbankmethod']==$key){ echo "selected"; }} ?>><?=ucwords($value)?></option>
                                      <?php } ?>
                                  </select>
                              </div>
                            </div>
                            <? } ?>
                          </div>
                          <div class="col-md-6 p-n">
                            <div class="form-group row" id="gstno_div">
                              <label class="control-label col-md-4" for="gstno">GST No.</label>
                              <div class="col-md-8">
                                <input id="gstno" type="text" name="gstno" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['gstno']; } ?>" class="form-control">
                              </div>
                            </div>
                            <div class="form-group row" id="panno_div">
                              <label class="control-label col-md-4" for="panno">PAN No.</label>
                              <div class="col-md-8">
                                <input id="panno" type="text" name="panno" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['panno']; } ?>" class="form-control" style="text-transform: uppercase;" maxlength="10">
                              </div>
                            </div>
                            <div class="form-group row" id="minimumstocklimit_div">
                              <label class="control-label col-md-4" for="minimumstocklimit">Minimum Stock Limit</label>
                              <div class="col-md-8">
                                <input id="minimumstocklimit" type="text" name="minimumstocklimit" value="<?php if(isset($memberdata)){ echo $memberdata['minimumstocklimit']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="6">
                              </div>
                            </div>
                            <div class="form-group" id="memberratingstatusid_div">
                                <label class="col-sm-4 control-label" for="memberratingstatusid">Rating Status</label>
                                <div class="col-sm-8">
                                    <select id="memberratingstatusid" name="memberratingstatusid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Rating Status</option>
                                        <?php foreach($memberratingstatusdata as $ratingstatus){ ?>
                                        <option value="<?php echo $ratingstatus['id']; ?>" <?php if(isset($memberdata) && $memberdata['memberratingstatusid']==$ratingstatus['id']){ echo 'selected'; } ?>><?php echo $ratingstatus['name']; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row" id="emireminderdays_div">
                              <label class="control-label col-md-4" for="emireminderdays">EMI Reminder Days</label>
                              <div class="col-md-8">
                                <input id="emireminderdays" type="text" name="emireminderdays" value="<?php if(isset($memberdata)){ echo $memberdata['emireminderdays']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
                              </div>
                            </div>
                            <div class="form-group" id="websitelink_div">
                              <label for="websitelink" class="col-sm-4 control-label">Website Link</label>
                              <div class="col-md-8">
                                <input id="websitelink" type="text" name="websitelink" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['websitelink']; } ?>" class="form-control" onkeyup="setwebsitelink(this.value);">
                              </div>
                            </div>
                            <div class="form-group row" id="anniversarydate_div">
                              <label class="col-md-4 control-label" for="anniversarydate">Anniversary Date</label>
                              <div class="col-md-8">
                                <input id="anniversarydate" type="text" name="anniversarydate" class="form-control" value="<?php if(isset($memberdata) && $memberdata['anniversarydate']!='0000-00-00'){ echo $this->general_model->displaydate($memberdata['anniversarydate']); } ?>" readonly >
                              </div>
                            </div>
                            <div class="form-group row" id="minimumorderamount_div">
                              <label class="col-md-4 control-label" for="minimumorderamount">Min. Order Amount (<?=CURRENCY_CODE?>)</label>
                              <div class="col-md-8">
                                <input id="minimumorderamount" type="text" name="minimumorderamount" class="form-control" value="<?php if(isset($memberdata)){ echo number_format($memberdata['minimumorderamount'],2,'.',''); } ?>" onkeypress="return decimal_number_validation(event,this.value)">
                              </div>
                            </div>
                            <div class="form-group row" id="advancepaymentcod_div">
                                <label for="advancepaymentcod" class="col-md-4 control-label">Advance Payment (COD) (%)</label>
                                <div class="col-md-8">
                                    <input id="advancepaymentcod" type="text" name="advancepaymentcod" value="<?php if(isset($memberdata)){ echo $memberdata['advancepaymentcod']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value, 5)">
                                </div>
                            </div> 
                            <div class="form-group">
                              <label for="focusedinput" class="col-md-4 control-label">Profile Image</label>
                              <div class="col-md-8">
                                <input type="hidden" name="oldprofileimage" id="oldprofileimage" value="<?php if(isset($memberdata) && !isset($CLONE)){ echo $memberdata['image']; }?>">
                                <input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
                                <?php if(isset($memberdata) && !isset($CLONE) && $memberdata['image']!=''){ ?>
                                  <div class="imageupload" id="profileimage">
                                      <div class="file-tab"><img src="<?php echo PROFILE.$memberdata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                          <label id="profileimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                              <span id="profileimagebtn">Change</span>
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
                                          <label id="logolabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                              <span id="profileimagebtn">Select Image</span>
                                              <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                          </label>
                                          <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove">Remove</button>
                                      </div>
                                  </div>
                                <?php } ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                  </div>
                </div>
              </div>
              <?php if(CRM==1){ ?>
              <div class="col-md-12">
                <div class="panel panel-default border-panel">
                  <div class="panel-heading">
                    <h2>CRM Details</h2>
                  </div>
                  <div class="panel-body pt-n">
                    <div class="col-md-12">
                      <div class="col-md-6 p-n">
                        <div class="form-group" id="companyname_div">
                            <?php if(!empty($memberdata) && !isset($CLONE)){ ?>
                              <input type="hidden" id="oldcompanyname" value="<?php if(!empty($memberdata)){ echo $memberdata['companyname']; } ?>" name="oldcompanyname">
                            <?php } ?>
                            <label class="col-sm-4 control-label" for="companyname">Company Name</label>
                            <div class="col-sm-8">
                              <input type="text" id="companyname" value="<?php if(!empty($memberdata)){ echo $memberdata['companyname']; } ?>" name="companyname" class="form-control">
                              <!-- <label class="col-sm-4 control-label text-danger" id="companynameduplicatemessage"></label> -->
                            </div>
                        </div>
                        <div class="form-group" id="employee_div">
                          <label class="col-sm-4 control-label" for="employee">Assign To</label>
                            <?php if(!empty($memberdata)){ ?>
                              <?php $assignemparr=array();
                                foreach ($assignemp as $ae) {
                                  $assignemparr[]=$ae['employeeid'];
                                } ?>
                              <input type="hidden" name="oldassign" value="<?php echo implode(",",$assignemparr);?>">
                            <?php } ?>
                            <div class="col-sm-8">
                              <select class="form-control selectpicker" id="employee" name="employee[]" data-live-search="true" data-size="8" title="Select Employee" multiple>
                                <?php foreach ($employeedata as $row) { ?>        
                                    <option value="<?php echo $row['id'];?>" <?php if(isset($child_sibling_employee_data) && !in_array($row['id'],$child_sibling_employee_data) && $checkrights==1){ echo "disabled"; } ?> <?php if(!empty($memberdata))
                                    { if(in_array($row['id'],$assignemparr)){echo "selected";} }else{
                                      if(($row['id']==$this->session->userdata(base_url().'ADMINID'))){echo "selected";}
                                    } ?> >
                                    <?php echo ucwords($row['name']);?></option>
                                <?php
                                  }
                                ?>
                              </select>  
                            </div>
                        </div>
                        <div class="form-group" id="industrycategory_div">
                          <label class="col-md-4 control-label" for="industrycategory">Industry </label>
                          <div class="col-md-8">
                            <select id="industrycategory" name="industrycategory" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                              <option value="0" >Select Industry</option>
                              <?php foreach($industrycategorydata as $ls){?>
                              <option value="<?php echo $ls['id']; ?>" <?php if(isset($memberdata)){ if($memberdata['industryid'] == $ls['id']){ echo 'selected'; } } ?>><?php echo ucwords($ls['name']); ?></option>
                              <?php  } ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group" id="area_div">
                            <label class="col-sm-4 control-label" for="areaid">Area</label>
                            <div class="col-sm-8">
                                <select id="areaid" name="areaid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                    <option value="0">Select Area</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="zone_div">
                            <label class="col-sm-4 control-label" for="zoneid">Zone</label>
                            <div class="col-sm-8">
                              <select id="zoneid" name="zoneid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                <option value="0"><?php echo 'Select Zone'; ?></option>
                                <?php foreach($zonedata as $zonerow){ ?>
                                  <option value="<?php echo $zonerow['id']; ?>" <?php if(isset($memberdata)){ if(isset($memberdata['zoneid'])){ if($zonerow['id']==$memberdata['zoneid']){ echo 'selected';} } } ?>><?php echo $zonerow['zonename']; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                        </div>
                        <div class="form-group" id="address_div">
                          <label class="col-sm-4 control-label" for="address">Address </label>
                          <div class="col-sm-8">
                            <textarea id="address" name="address" rows="3" class="form-control"><?php if(isset($memberdata)){ echo $memberdata['address']; } ?></textarea>
                          </div>
                        </div>
                        <div class="form-group" id="pincode_div">
                          <label class="col-md-4 control-label" for="pincode">Pincode </label>
                          <div class="col-md-4">
                            <input type="text" id="pincode" value="<?php if(!empty($memberdata)){ echo $memberdata['pincode']; } ?>" name="pincode" class="form-control">
                          </div>
                          <div class="col-md-4">
                            <button type="button" class="form-control" style="width: auto;" onclick="openmodal(<?php echo (!empty($memberdata['latitude']))?$memberdata['latitude']:DEFAULT_LAT?>,<?php echo (!empty($memberdata['longitude']))?$memberdata['longitude']:DEFAULT_LNG?>)"><i class="fa fa-map-marker"></i> Pickup Location</button>
                          </div>
                        </div>
                        <div class="form-group" id="salesperson_div">
                            <label for="salespersonid" class="col-md-4 control-label">Select Sales Person</label>
                            <div class="col-sm-8">
                                <select id="salespersonid" name="salespersonid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5" title="Select Sales Person" data-actions-box="true" multiple>

                                    <?php /* if(isset($employeedata)){ foreach($employeedata as $emp){ ?>
                                        <option value="<?php echo $emp['id']; ?>" <?php if(!empty($memberdata) && !empty($memberdata['employeeids']) && in_array($emp['id'], explode(",",$memberdata['employeeids']))){ echo "selected"; }?>><?php echo ucwords($emp['name']); ?></option>
                                    <?php }} */ ?>
                                </select>
                            </div>
                        </div>
                            
                      </div>
                      <div class="col-md-6 p-n">
                        <div class="form-group" id="website_div">
                          <label class="col-md-4 control-label" for="website">Website</label>
                          <div class="col-md-8">
                            <input type="text" id="website" name="website" value="<?php if(isset($memberdata)){ echo $memberdata['website']; } ?>" class="form-control">
                          </div>
                        </div>
                        <div class="form-group" id="leadsource_div">
                          <label class="col-md-4 control-label" for="leadsource">Lead Source</label>
                          <div class="col-md-8">
                            <select id="leadsource" name="leadsource" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                              <option value="0" >Select Lead Source</option>
                              <?php foreach($leadsourcedata as $ls){?>
                              <option value="<?php echo $ls['id']; ?>" <?php if(isset($memberdata)){ if($memberdata['leadsourceid'] == $ls['id']){ echo 'selected'; } } ?>><?php echo ucwords($ls['name']); ?></option>
                              <?php  } ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group" id="memberstatus_div">
                          <label class="col-md-4 control-label" for="memberstatus"><?=Member_label?> Status </label>
                          <div class="col-md-8">
                            <select id="memberstatus" name="memberstatus" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                              <option value="0" >Select <?=Member_label?> Status</option>
                              <?php foreach($memberstatusesdata as $ms){?>
                              <option value="<?php echo $ms['id']; ?>" <?php if(isset($memberdata)){ if($memberdata['memberstatus'] == $ms['id']){ echo 'selected'; } } ?>><?php echo ucwords($ms['name']); ?></option>
                              <?php  } ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group" id="membertype_div">
                          <label class="col-md-4 control-label" for="membertype">Type</label>
                          <div class="col-md-8">
                            <select id="membertype" name="membertype" class="selectpicker form-control" data-live-search="true" data-size="5">
                                <option value="0">Select Type</option>
                                <?php foreach ($this->Membertype as $key=>$type) { ?>
                                    <option value="<?=$key?>" <?php if(isset($memberdata)) {if ($memberdata['membertype'] == $key) {echo 'selected';}} ?>><?=$type?></option>
                                <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="form-group row" id="remarks_div">
                          <label class="control-label col-md-4" for="remarks">Remarks</label>
                          <div class="col-md-8">
                            <textarea id="remarks" name="remarks" class="form-control"><?php if(isset($memberdata)){ echo $memberdata['remarks']; } ?></textarea>
                          </div>
                        </div>
                        <div class="form-group" id="latitude_div">
                          <label class="col-md-4 control-label" for="latitude">Latitude <?php if(MEMBER_LAT_LONG == 1){ ?> <span class="mandatoryfield">*</span> <?php } ?>  </label>
                          <div class="col-md-8">
                            <input type="text" id="latitude" value="<?php if(!empty($memberdata)){ echo $memberdata['latitude']; } ?>" name="latitude" class="form-control" onkeypress="return latlng_validation(event,this.id);">
                          </div>
                        </div>
                        <div class="form-group" id="longitude_div">
                          <label class="col-md-4 control-label" for="longitude">Longitude <?php if(MEMBER_LAT_LONG == 1){ ?> <span class="mandatoryfield">*</span> <?php } ?> </label>
                          <div class="col-md-8">
                            <input type="text" id="longitude" value="<?php if(!empty($memberdata)){ echo $memberdata['longitude']; } ?>" name="longitude" class="form-control" onkeypress="return latlng_validation(event,this.id);">
                          </div>
                        </div>
                        <div class="form-group" id="rating_div">
                          <label class="col-md-4 control-label" style="margin-top:14px !important;" for="rating">Rating : </label>
                          <div class="col-md-3">
                            <div id="rate" class="rate"></div>
                            <input id="input2" name="rating" type="hidden" value="<?php if(!empty($memberdata)){ echo $memberdata['rating']; } ?>">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
              <div class="col-md-12">
                <div class="panel panel-default border-panel">
                  <div class="panel-heading">
                    <h2>Contact Detail</h2>
                    <span style="color:#800080;float:right;">Note : Either Mobile or Email is Required</span>
                  </div>
                  <div class="panel-body pt-n">
                    <div id="contactdivs">
                    <?php $oldcontactids=array();
                      if(isset($contactdetail) && count($contactdetail)>0){
                        foreach($contactdetail as $cnt=>$cd){
                          $oldcontactids[]=$cd['id']; ?>
                          <div class="contactdiv" id="contactdiv<?=$cnt+1?>" div-id="<?=$cnt+1?>">
                            <input type="hidden" value="<?php echo $cd['id'];?>" name="membercontactid[]">
                            <div class="row">
                                <?php if($cnt!=0){ ?>
                                  <div class="col-md-12"><hr></div>
                                <?php } ?>
                                <div class="col-md-6">
                                  <?php if($cnt==0){ ?>
                                    <div class="radio radio1">
                                      <input type="radio" name="inquirycontact" id="inquirycontact<?=$cnt+1?>" class='inquirycontact' value="<?=$cnt+1?>" checked>
                                      <label for="inquirycontact<?=$cnt+1?>" class="contactheading" heading-id="<?=$cnt+1?>">Contact <?=$cnt+1?></label>
                                    </div>
                                  <?php }else{ ?>
                                    <div class="radio1">
                                      <label for="inquirycontact<?=$cnt+1?>" class="contactheading" heading-id="<?=$cnt+1?>">Contact <?=$cnt+1?></label>
                                    </div>
                                  <?php } ?>
                                </div>
                                <div class="col-md-6 text-right">
                                  <?php if($cnt!=0){ ?>
                                    <button type="button" tabindex="13" class="btn btn-danger btn-raised btn-sm mr-7" id="contactdivbtn<?=$cnt+1?>" onclick="removecontact(<?=$cnt+1?>)"><i class="fa fa-remove"></i> REMOVE</button>
                                  <?php } ?>
                                  <button type="button" class="<?=addbtn_class;?>" id="contactdivbtn<?=$cnt+1?>" onclick='addnewcontact();'><i class="fa fa-plus"></i> Add Contact</button>
                                </div>
                            </div>
                            <div class="row">
                              <div class="col-md-3">
                                  <div class="form-group" id="firstname_div<?=$cnt+1?>">
                                    <div class="col-md-12 pr-sm">
                                      <label class="control-label" for="firstname<?=$cnt+1?>">First Name </label>
                                      <input type="text" id="firstname<?=$cnt+1?>" value="<?php  echo $cd['firstname'];  ?>" name="contactfirstname[]" class="form-control" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group" id="lastname_div<?=$cnt+1?>">
                                  <div class="col-md-12 pl-sm pr-sm">
                                    <label class="control-label" for="lastname">Last Name </label>
                                    <input type="text" id="contactlastname<?=$cnt+1?>" value="<?php echo $cd['lastname']; ?>" name="contactlastname[]" class="form-control" onkeypress="return onlyAlphabets(event)">
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group" id="mobile_div<?=$cnt+1?>">
                                  <div class="col-md-12 pl-sm pr-sm">
                                    <label class="control-label" for="mobileno<?=$cnt+1?>">Mobile No <span class="mandatoryfield">*</span></label>
                                      <input type="hidden" id="oldcontactmobileno<?=$cnt+1?>" value="<?php  echo $cd['mobileno'];  ?>" name="oldcontactmobileno">
                                    <input id="mobileno<?=$cnt+1?>" type="text" name="contactmobileno[]" value="<?php echo $cd['mobileno']; ?>" class="form-control mobileno number" maxlength="15" div-id="<?=$cnt+1?>">
                                    <?php if($cnt==0){ ?>
                                      <span class="mandatoryfield" id="mobilenoduplicatemessage<?=$cnt+1?>" div-id="<?=$cnt+1?>"></span>  
                                    <?php } ?>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group" id="email_div<?=$cnt+1?>">
                                  <div class="col-md-12 pl-sm">
                                      <input type="hidden" id="oldcontactemail<?=$cnt+1?>" value="<?php echo $cd['email']; ?>" name="oldemail">
                                      <label class="control-label" for="email<?=$cnt+1?>">Email <span class="mandatoryfield"> *</span></label>
                                      <input id="email<?=$cnt+1?>" type="text" name="contactemail[]" value="<?php echo $cd['email']; ?>" class="form-control email" div-id="<?=$cnt+1?>">
                                      <?php if($cnt==0){ ?>
                                        <span class="mandatoryfield" id="emailduplicatemessage<?=$cnt+1?>" div-id="<?=$cnt+1?>"></span> 
                                      <?php } ?>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="row">
                              <div class="col-md-3">
                                  <div class="form-group" id="designation_div<?=$cnt+1?>">
                                    <div class="col-md-12 pr-sm">
                                      <label class="control-label" for="designation<?=$cnt+1?>">Designation </label>
                                      <input type="text" id="designation<?=$cnt+1?>" value="<?php  echo $cd['designation']; ?>" name="contactdesignation[]" class="form-control">
                                    </div>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group" id="department_div<?=$cnt+1?>">
                                    <div class="col-md-12 pl-sm pr-sm">
                                      <label class="control-label" for="department<?=$cnt+1?>">Department </label>
                                      <input type="text" id="department<?=$cnt+1?>" value="<?php echo $cd['department']; ?>" name="contactdepartment[]" class="form-control">
                                    </div>
                                  </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group" id="birthdate_div<?=$cnt+1?>">
                                  <div class="col-md-12 pl-sm pr-sm">
                                    <label class="control-label" for="birthdate<?=$cnt+1?>">Birth Date </label>
                                    <input id="birthdate<?=$cnt+1?>" type="text" name="contactbirthdate[]" value="<?php if($cd['birthdate']!="0000-00-00"){ echo $this->general_model->displaydate($cd['birthdate']); } ?>" class="form-control birthdate" readonly>
                                  </div>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group" id="annidate_div<?=$cnt+1?>">
                                  <div class="col-md-12 pl-sm">
                                    <label class="control-label" for="annidate<?=$cnt+1?>">Anniversary Date </label>
                                    <input id="annidate<?=$cnt+1?>" type="text" name="contactannidate[]" value="<?php if($cd['annidate']!="0000-00-00"){ echo $this->general_model->displaydate($cd['annidate']); } ?>" class="form-control annidate" readonly>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php
                          }
                      }else{ ?>
                        <div class="contactdiv" id="contactdiv1" div-id="1">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="radio radio1">
                                      <input type="radio" name="inquirycontact" id="inquirycontact1" class='inquirycontact' value="1" checked>
                                      <label for="inquirycontact1" class="contactheading" heading-id="1">Contact 1</label>
                                    </div>
                                </div>
                                <div class="col-md-6 text-right">
                                  <button type="button" class="<?=addbtn_class;?>" id="contactdivbtn1" onclick='addnewcontact();'><i class="fa fa-plus"></i> Add Contact</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" id="firstname_div1">
                                        <div class="col-md-12 pr-sm">
                                            <label class="control-label" for="firstname1">First Name</label>
                                            <input type="text" id="firstname1" name="contactfirstname[]" class="form-control" onkeypress="return onlyAlphabets(event)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="lastname_div1">
                                        <div class="col-md-12 pl-sm pr-sm">
                                            <label class="control-label" for="lastname1">Last Name</label>
                                            <input type="text" id="lastname1" name="contactlastname[]" class="form-control"  onkeypress="return onlyAlphabets(event)">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="mobile_div1">
                                        <div class="col-md-12 pl-sm pr-sm">
                                            <label class="control-label" for="mobileno1">Mobile No <span class="mandatoryfield">*</span></label>
                                            <input id="mobileno1" type="text" name="contactmobileno[]" class="form-control mobileno number" maxlength="10" onkeypress="return isNumber(event)" div-id="1">
                                            <span class="mandatoryfield" id="mobilenoduplicatemessage1" div-id="1"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="email_div1">
                                        <div class="col-md-12 pl-sm">
                                            <label class="control-label" for="email1">Email <span class="mandatoryfield">*</span></label>
                                            <input id="email1" type="text" name="contactemail[]" class="form-control email" div-id="1">
                                            <span class="mandatoryfield" id="emailduplicatemessage1" div-id="1"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" id="designation_div1">
                                        <div class="col-md-12 pr-sm">
                                            <label class="control-label" for="designation1">Designation </label>
                                            <input type="text" id="designation1" name="contactdesignation[]" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="department_div1">
                                        <div class="col-md-12 pl-sm pr-sm">
                                            <label class="control-label" for="department1">Department </label>
                                            <input type="text" id="department1" name="contactdepartment[]" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="birthdate_div1">
                                        <div class="col-md-12 pl-sm pr-sm">
                                            <label class="control-label" for="birthdate1">Birth Date </label>
                                            <input id="birthdate1" type="text" name="contactbirthdate[]"  value="" class="form-control birthdate" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" id="annidate_div1">
                                        <div class="col-md-12 pl-sm">
                                            <label class="control-label" for="annidate1">Anniversary Date </label>
                                            <input id="annidate1" type="text" name="contactannidate[]"  value="" class="form-control annidate" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
              <?php if(!isset($memberdata) || isset($CLONE)){ ?>
              <div class="col-md-12">
                <div class="panel panel-default border-panel">
                  <div class="panel-heading">
                    <h2>Address Detail</h2>
                  </div>
                  <div class="panel-body pt-n">

                    <div class="col-md-12">
                      <div class="col-md-6 p-n">
                        <div class="form-group row" id="addressname_div">
                          <label class="col-md-4 control-label" for="addressname">Name <span class="mandatoryfield">*</span></label>
                          <div class="col-md-8">
                            <input id="addressname" type="text" name="addressname" class="form-control"  >
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 p-n">
                        <div class="form-group row" id="addressemail_div">
                          <label class="control-label col-md-4" for="addressemail">Email <span class="mandatoryfield">*</span></label>
                          <div class="col-md-8">
                            <input id="addressemail" type="text" name="addressemail" class="form-control">
                          </div>
                        </div>
                      </div>
                    </div>
                   
                    <div class="col-md-12">
                        <div class="col-md-6 p-n">
                            <div class="form-group row" id="addressmobile_div">
                              <label class="control-label col-md-4" for="addressmobile">Mobile No. <span class="mandatoryfield">*</span></label>  
                              <div class="col-md-8">
                                    <input id="addressmobile" type="text" name="addressmobile" class="form-control" maxlength="10"  onkeypress="return isNumber(event)">
                              </div>
                            </div>
                        </div>
                        <div class="col-md-6 p-n">
                            <div class="form-group row" id="postalcode_div">
                              <label class="control-label col-md-4" for="postalcode">Postal Code <span class="mandatoryfield">*</span></label>  
                              <div class="col-md-8">
                                    <input id="postalcode" type="text" name="postalcode" class="form-control"   onkeypress="return isNumber(event)">
                              </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                      <div class="col-md-6 p-n">
                        <div class="form-group is-empty" id="memberaddress_div">
                              <label class="col-sm-4 control-label" for="memberaddress">Address <span class="mandatoryfield">*</span></label>
                              <div class="col-sm-8">
                                  <textarea id="memberaddress" name="memberaddress"  class="form-control"></textarea>
                              </div>
                          <span class="material-input"></span></div>
                        </div>
                    </div>

                  </div>
                </div>
              </div>
              <?php } ?>
              <div class="col-md-12" style="display:<?=($displayonlybalancefields==0)?'none':'block'?>">
                <div class="panel panel-default border-panel">
                  <div class="panel-heading">
                    <h2>Balance Detail</h2>
                  </div>
                  <div class="panel-body pt-n">
                    <div class="col-md-12">
                      <div class="col-md-6 p-n">
                        <div class="form-group row" id="balancedate_div">
                          <label class="col-md-4 control-label" for="balancedate">Opening Balance Date</label>
                          <div class="col-md-8">
                            <input id="balancedate" type="text" name="balancedate" class="form-control" value="<?php if(isset($memberdata) && $memberdata['balancedate']!='0000-00-00'){ echo $this->general_model->displaydate($memberdata['balancedate']); } ?>" readonly >
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 p-n">
                        <div class="form-group row" id="balance_div">
                          <label class="col-md-4 control-label" for="balance">Opening Balance</label>
                          <div class="col-md-8">
                            <input id="balance" type="text" name="balance" value="<?php if(isset($memberdata)){ echo $memberdata['balance']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value)">
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="col-md-6 p-n">
                        <div class="form-group row" id="debitlimit_div">
                          <label class="control-label col-md-4" for="debitlimit">Debit Limit</label>
                          <div class="col-md-8">
                            <input id="debitlimit" type="text" name="debitlimit" value="<?php if(isset($memberdata)){ echo $memberdata['debitlimit']; }else{ echo DEFAULT_DEBIT_LIMIT; } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value,8,2)">
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 p-n">
                        <div class="form-group row" id="paymentcycle_div">
                          <label class="control-label col-md-4" for="paymentcycle">Payment Cycle</label>
                          <div class="col-md-8">
                            <input id="paymentcycle" type="text" name="paymentcycle" value="<?php if(isset($memberdata)){ echo $memberdata['paymentcycle']; }else{ echo DEFAULT_PAYMENT_CYCLE; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="4">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
             
              <div class="col-md-12">
                <div class="panel panel-default border-panel">
                  <div class="panel-heading">
                    <h2>Action</h2>
                  </div>
                  <div class="panel-body pt-n">
                    <div class="col-md-10 col-md-offset-2">
                      <div class="form-group row">
                        <label for="focusedinput" class="col-md-4 control-label">Activate</label>
                        <div class="col-md-8">
                          <div class="col-md-2 col-xs-2" style="padding-left: 0px;">
                            <div class="radio">
                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($memberdata) && !isset($CLONE) && $memberdata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                            <label for="yes">Yes</label>
                            </div>
                          </div>
                          <div class="col-md-2 col-xs-2">
                            <div class="radio">
                            <input type="radio" name="status" id="no" value="0" <?php if(isset($memberdata) && !isset($CLONE) && $memberdata['status']==0){ echo 'checked'; }?>>
                            <label for="no">No</label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12" style="text-align: center;">
                      <div class="form-group row">
                        <?php if(isset($memberdata) && !isset($CLONE)){ ?>
                          <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                          <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                          <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$fromurl?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                        <?php }else{ ?>
                          <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                          <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                          <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>member" title=<?=cancellink_title?>><?=cancellink_text?></a>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
             
            </form>
          </div>
        </div>
        
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<!-- Modal -->
<div id="locationModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl">
    <div class="modal-content" style="width: 151%;height: 424px; margin-left: -96px;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Select Location</h4>
      </div>
      <div class="modal-body">
        <input id="pac-input" class="pac-controls" type="text" placeholder="Search Place">
        <div id="map"></div>
      </div>
    </div>
  </div>
</div>
<script>
  $(document).ready(function() {
    var options = {
      max_value: 5,
      step_size: 0.5,
      initial_value: "<?php if(!empty($memberdata)){ echo $memberdata['rating']; } ?>",
      update_input_field_name: $("#input2"),
    }
    $("#rate").rate(options);
  });
  function openmodal(latitude,longitude){
    latitude = latitude || '';
    longitude = longitude || '';
    newLocation(latitude,longitude);
    
    $('#pac-input').val('');
    $('#locationModal').modal('show');
  }
    // Initialize and add the map
    var markers = [];
    var map;
    function initAutocomplete() {
        map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: <?=DEFAULT_LAT?>, lng: <?=DEFAULT_LNG?>},
          zoom: 6,
          mapTypeId: 'roadmap',
          disableDefaultUI: true,
          streetViewControl: false,
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function() {
          searchBox.setBounds(map.getBounds());
        });

        google.maps.event.addListener(map, 'click', function(event) {
            deleteMarkers();
            placeMarker(event.latLng);
            $('#latitude').val(event.latLng.lat());
            $('#longitude').val(event.latLng.lng());
        });

        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function() {
          var places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }

          // Clear out the old markers.
          markers.forEach(function(marker) {
            marker.setMap(null);
          });
          markers = [];

          // For each place, get the icon, name and location.
          var bounds = new google.maps.LatLngBounds();
          places.forEach(function(place) {
            if (!place.geometry) {
              console.log("Returned place contains no geometry");
              return;
            }
            var icon = {
              url: place.icon,
              size: new google.maps.Size(71, 71),
              origin: new google.maps.Point(0, 0),
              anchor: new google.maps.Point(17, 34),
              scaledSize: new google.maps.Size(25, 25)
            };

            // Create a marker for each place.
            markers.push(new google.maps.Marker({
              map: map,
              icon: icon,
              title: place.name,
              position: place.geometry.location
            }));

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
    }
    function newLocation(newLat,newLng){

        if(newLat!='' && newLng!=''){
            marker = new google.maps.Marker({
                    position: new google.maps.LatLng( newLat,newLng),
                    map: map,
                });
            markers.push(marker);

            // To add the marker to the map, call setMap();
            marker.setMap(map);

            map.setCenter({lat : newLat,lng : newLng});
            map.setZoom(14);
        }else{
            deleteMarkers();
            map.setCenter({lat : <?=DEFAULT_LAT?>,lng : <?=DEFAULT_LNG?>});
            map.setZoom(4);
        }
    }
    function placeMarker(location) {        
        marker = new google.maps.Marker({
            position: location, 
            map: map
        });
        markers.push(marker);
        
    }
    // Sets the map on all markers in the array.
    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
    }
    // Removes the markers from the map, but keeps them in the array.
    function clearMarkers() {
        setMapOnAll(null);
    }
    // Deletes all markers in the array by removing references to them.
    function deleteMarkers() {
        clearMarkers();
        markers = [];
    }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?=MAP_KEY?>&libraries=places&callback=initAutocomplete"></script>