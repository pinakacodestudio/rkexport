<script type="text/javascript">
    var FROM_URL = '<?= isset($fromurl) ? $fromurl : '' ?>';
    var profileimgpath = '<?php echo PROFILE; ?>';
    var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE; ?>';
    var defaultprofileimgpath = '<?php echo DEFAULT_PROFILE; ?>';
    var countryid = '<?php if (isset($vendordata) && !empty($vendordata['countryid'])) { echo $vendordata['countryid']; } else { echo DEFAULT_COUNTRY_ID; } ?>';
    var provinceid = '<?php if (isset($vendordata) && !empty($vendordata['provinceid'])) { echo $vendordata['provinceid']; } else { echo 0; } ?>';
    var cityid = '<?php if (isset($vendordata) && !empty($vendordata['cityid'])) { echo $vendordata['cityid']; } else { echo 0; } ?>';
    var countrycodeid = '<?php if (isset($vendordata) && !empty($vendordata['countrycode'])) { echo $vendordata['countrycode']; } else { echo DEFAULT_PHONECODE; } ?>';
</script>
<?php
$displayonlybalancefields = 1;
?>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($vendordata)) {
                echo 'Edit';
            } else {
                echo 'Add';
            } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl'); ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a></li>
                <li class="active"><?php if (isset($vendordata)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <form action="#" id="vendorform" class="form-horizontal">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-body">
                                <input type="hidden" id="id" name="id" value="<?php if (isset($vendordata)) { echo $vendordata['id']; } ?>">
                                <input type="hidden" name="balanceid" value="<?php if (isset($vendordata)) { echo $vendordata['balanceid']; } ?>">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6 p-n">
                                            <div class="form-group row" id="name_div">
                                                <label class="control-label col-md-4" for="name">Name <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <input id="name" class="form-control" name="name" value="<?php if (isset($vendordata)) { echo $vendordata['name']; } ?>" type="text" onkeypress="return onlyAlphabets(event)" onkeyup="$('#addressname').val(this.value)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-n">
                                            <div class="form-group" id="roleid_div">
                                                <label class="col-sm-4 control-label" for="roleid"><?= Member_label ?> Role</label>
                                                <div class="col-sm-8">
                                                    <select id="roleid" name="roleid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                        <option value="0">Select Role</option>
                                                        <?php foreach ($memberroledata as $rolerow) { ?>
                                                            <option value="<?php echo $rolerow['id']; ?>" <?php if (isset($vendordata) && $vendordata['roleid'] == $rolerow['id']) { echo 'selected'; }  ?>><?php echo $rolerow['role']; ?></option>
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
                                            <div class="form-group" id="membercode_div">
                                                <label class="control-label col-md-4" for="membercode">Vendor Code <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-10" style="padding: 0px;">
                                                            <input id="membercode" type="text" name="membercode" value="<?php if (isset($vendordata)) { echo $vendordata['membercode']; } ?>" class="form-control" maxlength="8">
                                                        </div>
                                                        <div class="col-sm-2" style="padding-right: 0px;">
                                                            <a href="javascript:void(0)" class="stepy-finish btn-primary btn btn-raised" title="Generate Code" onclick="$('#membercode').val(randomPassword(8,8,0,0,0))"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-n">
                                            <div class="form-group row" id="gstno_div">
                                                <label class="control-label col-md-4" for="gstno">GST No.</label>
                                                <div class="col-md-8">
                                                    <input id="gstno" type="text" name="gstno" value="<?php if (isset($vendordata)) { echo $vendordata['gstno']; } ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6 p-n">

                                            <div class="form-group" id="mobile_div">
                                                <label class="control-label col-md-4" for="mobileno">Primary Mobile No. <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <select id="countrycodeid" name="countrycodeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">Code</option>
                                                                <?php foreach ($countrycodedata as $countrycoderow) { ?>
                                                                    <option value="<?php echo $countrycoderow['phonecode']; ?>" <?php if (isset($vendordata) && $vendordata['countrycode'] == $countrycoderow['phonecode']) { echo 'selected'; } else { if (DEFAULT_PHONECODE == $countrycoderow['phonecode']) { echo 'selected'; } } ?>><?php echo $countrycoderow['phonecode']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input id="mobileno" type="text" name="mobileno" value="<?php if (isset($vendordata)) { echo $vendordata['mobile']; } ?>" class="form-control" maxlength="10" onkeypress="return isNumber(event)" onkeyup="$('#addressmobile').val(this.value)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group" id="secondarymobileno_div">
                                                <label class="control-label col-md-4" for="secondarymobileno">Secondary Mobile No.</label>
                                                <div class="col-md-8">
                                                    <div class="row">
                                                        <div class="col-md-4 ">
                                                            <select id="secondarycountrycodeid" name="secondarycountrycodeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                                <option value="0">Code</option>
                                                                <?php foreach ($countrycodedata as $countrycoderow) { ?>
                                                                    <option value="<?php echo $countrycoderow['phonecode']; ?>" <?php if (isset($vendordata) && $vendordata['secondarycountrycode'] == $countrycoderow['phonecode']) { echo 'selected'; } else { if (DEFAULT_PHONECODE == $countrycoderow['phonecode']) { echo 'selected'; } } ?>><?php echo $countrycoderow['phonecode']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8 ">
                                                            <input id="secondarymobileno" type="text" name="secondarymobileno" value="<?php if (isset($vendordata)) { echo $vendordata['secondarymobileno']; } ?>" class="form-control" maxlength="10" onkeypress="return isNumber(event)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group" id="email_div">
                                                <label class="control-label col-md-4" for="email">Email <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <input id="email" type="text" name="email" value="<?php if (isset($vendordata)) { echo $vendordata['email']; } ?>" class="form-control" onkeyup="$('#addressemail').val(this.value)">
                                                </div>
                                            </div>
                                            <div class="form-group" id="password_div">
                                                <label class="control-label col-md-4" for="password">Password <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <div>
                                                        <div class="col-sm-10" style="padding: 0px;">
                                                            <input id="password" type="text" name="password" value="<?php if (isset($vendordata)) { echo $this->general_model->decryptIt($vendordata['password']); } ?>" class="form-control">
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
                                                        <?php foreach ($countrydata as $country) { ?>
                                                            <option value="<?php echo $country['id']; ?>" <?php if (isset($vendordata) && $vendordata['countryid'] == $country['id']) { echo 'selected'; } else { if (DEFAULT_COUNTRY_ID == $country['id']) { echo 'selected'; } } ?>><?php echo $country['name']; ?></option>
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
                                            <?php if (MANUFACTURING_PROCESS == 1) { ?>
                                                <div class="form-group" id="manufacturingprocess_div">
                                                    <label class="col-sm-4 control-label" for="manufacturingprocess">Manufacturing Process</label>
                                                    <?php if (!empty($vendordata)) { ?>
                                                        <?php $manufacturingprocessarr = array();
                                                        foreach ($manufacturingprocess as $mp) {
                                                            $manufacturingprocessarr[] = $mp['processid'];
                                                        } ?>
                                                        <input type="hidden" name="oldprocess" value="<?php echo implode(",", $manufacturingprocessarr); ?>">
                                                    <?php } ?>


                                                    <div class="col-sm-8">
                                                        <select class="form-control selectpicker" id="manufacturingprocess" name="manufacturingprocess[]" data-live-search="true" data-size="8" title="Select Process" multiple data-actions-box="true">
                                                            <?php foreach ($manufacturingprocessdata as $row) { ?>
                                                                <option value="<?php echo $row['id']; ?>" <?php if (!empty($vendordata)) { if (in_array($row['id'], $manufacturingprocessarr)) { echo "selected"; } } ?>><?php echo ucwords($row['name']); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                <?php } ?>
                                                </div>
                                                <?php if (isset($vendordata)) { ?>
                                                    <div class="form-group" id="billingaddress_div">
                                                        <label class="col-sm-4 control-label" for="billingaddressid">Default Billing Address</label>
                                                        <div class="col-sm-8">
                                                            <select id="billingaddressid" name="billingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                <option value="0">Select Billing Address</option>
                                                                <?php foreach ($addressdata as $address) { ?>
                                                                    <option value="<?php echo $address['id']; ?>" <?php if (isset($vendordata) && $vendordata['billingaddressid'] == $address['id']) { echo 'selected'; } ?>><?php echo ucfirst($address['address']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group" id="shippingaddress_div">
                                                        <label class="col-sm-4 control-label" for="billingaddressid">Default Shipping Address</label>
                                                        <div class="col-sm-8">
                                                            <select id="shippingaddressid" name="shippingaddressid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                <option value="0">Select Shipping Address</option>
                                                                <?php foreach ($addressdata as $address) { ?>
                                                                    <option value="<?php echo $address['id']; ?>" <?php if (isset($vendordata) && $vendordata['shippingaddressid'] == $address['id']) { echo 'selected'; } ?>><?php echo ucfirst($address['address']); ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                        </div>
                                        <div class="col-md-6 p-n">
                                            <div class="form-group row" id="minimumstocklimit_div">
                                                <label class="control-label col-md-4" for="minimumstocklimit">Minimum Stock Limit</label>
                                                <div class="col-md-8">
                                                    <input id="minimumstocklimit" type="text" name="minimumstocklimit" value="<?php if (isset($vendordata)) { echo $vendordata['minimumstocklimit']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="6">
                                                </div>
                                            </div>
                                            <div class="form-group" id="memberratingstatusid_div">
                                                <label class="col-sm-4 control-label" for="memberratingstatusid">Rating Status</label>
                                                <div class="col-sm-8">
                                                    <select id="memberratingstatusid" name="memberratingstatusid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                        <option value="0">Select Rating Status</option>
                                                        <?php foreach ($memberratingstatusdata as $ratingstatus) { ?>
                                                            <option value="<?php echo $ratingstatus['id']; ?>" <?php if (isset($vendordata) && $vendordata['memberratingstatusid'] == $ratingstatus['id']) { echo 'selected'; } ?>><?php echo $ratingstatus['name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row" id="emireminderdays_div">
                                                <label class="control-label col-md-4" for="emireminderdays">EMI Reminder Days</label>
                                                <div class="col-md-8">
                                                    <input id="emireminderdays" type="text" name="emireminderdays" value="<?php if (isset($vendordata)) { echo $vendordata['emireminderdays']; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="3">
                                                </div>
                                            </div>
                                            <div class="form-group row" id="advancepaymentcod_div">
                                                <label for="advancepaymentcod" class="col-md-4 control-label">Advance Payment (COD) (%)</label>
                                                <div class="col-md-8">
                                                    <input id="advancepaymentcod" type="text" name="advancepaymentcod" value="<?php if (isset($vendordata)) { echo $vendordata['advancepaymentcod']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event, this.value, 5)">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="focusedinput" class="col-md-5 control-label">Purchase Regular Products</label>
                                                <div class="col-md-6">
                                                    <div class="yesno">
                                                        <input type="checkbox" name="purchaseregularproduct" value="<?php if (isset($vendordata) && $vendordata['purchaseregularproduct'] == 1) { echo '1'; } else { echo '0'; } ?>" <?php if (isset($vendordata) && $vendordata['purchaseregularproduct'] == 1) { echo 'checked'; } ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-4 control-label">Profile Image</label>
                                                <div class="col-md-8">
                                                    <input type="hidden" name="oldprofileimage" id="oldprofileimage" value="<?php if (isset($vendordata)) { echo $vendordata['image']; } ?>">
                                                    <input type="hidden" name="removeoldImage" id="removeoldImage" value="0">
                                                    <?php if (isset($vendordata) && $vendordata['image'] != '') { ?>
                                                        <div class="imageupload" id="profileimage">
                                                            <div class="file-tab"><img src="<?php echo PROFILE . $vendordata['image']; ?>" alt="Image preview" class="thumbnail" style="max-width: 150px; max-height: 150px">
                                                                <label id="profileimagelabel" class="btn btn-sm btn-primary btn-raised btn-file">
                                                                    <span id="profileimagebtn">Change</span>
                                                                    <!-- The file is stored here. -->
                                                                    <input type="file" name="image" id="image" accept=".bmp,.bm,.gif,.ico,.jfif,.jfif-tbnl,.jpe,.jpeg,.jpg,.pbm,.png,.svf,.tif,.tiff,.wbmp,.x-png">
                                                                </label>
                                                                <button type="button" class="btn btn-sm btn-danger btn-raised" id="remove" style="display: inline-block;">Remove</button>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
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
                  <?php /* if (!isset($vendordata)) { */ ?> 
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
                                                    <input id="addressname" type="text" name="addressname" value="<?php if (isset($vendoraddress)) { echo $vendoraddress[0]['name']; } ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-n">
                                            <div class="form-group row" id="addressemail_div">
                                                <label class="control-label col-md-4" for="addressemail">Email <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <input id="addressemail" type="text" name="addressemail" value="<?php if (isset($vendoraddress)) { echo $vendoraddress[0]['email']; } ?>" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="col-md-6 p-n">
                                            <div class="form-group row" id="addressmobile_div">
                                                <label class="control-label col-md-4" for="addressmobile">Mobile No. <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <input id="addressmobile" type="text" name="addressmobile" value="<?php if (isset($vendoraddress)) { echo $vendoraddress[0]['mobileno']; } ?>"  class="form-control" maxlength="10" onkeypress="return isNumber(event)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 p-n">
                                            <div class="form-group row" id="postalcode_div">
                                                <label class="control-label col-md-4" for="postalcode">Postal Code <span class="mandatoryfield">*</span></label>
                                                <div class="col-md-8">
                                                    <input id="postalcode" type="text" name="postalcode" value="<?php if (isset($vendoraddress)) { echo $vendoraddress[0]['postalcode']; } ?>" class="form-control" onkeypress="return isNumber(event)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="col-md-6 p-n">
                                            <div class="form-group is-empty" id="memberaddress_div">
                                                <label class="col-sm-4 control-label" for="memberaddress">Address <span class="mandatoryfield">*</span></label>
                                                <div class="col-sm-8">
                                                    <textarea id="memberaddress" name="memberaddress" class="form-control"><?php if(isset($vendoraddress)){ echo $vendoraddress[0]['address']; } ?></textarea>
                                                </div>
                                                <span class="material-input"></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    <?php /* } */ ?>
                    <div class="col-md-12" style="display:<?= ($displayonlybalancefields == 0) ? 'none' : 'block' ?>">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading">
                                <h2>Balance Detail</h2>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-12">
                                    <div class="col-md-6 p-n">
                                        <div class="form-group row" id="balancedate_div">
                                            <label class="col-md-4 control-label" for="balancedate">Opening Balance Date</label>
                                            <div class="col-md-8">
                                                <input id="balancedate" type="text" name="balancedate" class="form-control" value="<?php if (isset($vendordata) && $vendordata['balancedate'] != '0000-00-00') { echo $this->general_model->displaydate($vendordata['balancedate']); } ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 p-n">
                                        <div class="form-group row" id="balance_div">
                                            <label class="col-md-4 control-label" for="balance">Opening Balance</label>
                                            <div class="col-md-8">
                                                <input id="balance" type="text" name="balance" value="<?php if (isset($vendordata)) { echo $vendordata['balance']; } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6 p-n">
                                        <div class="form-group row" id="debitlimit_div">
                                            <label class="control-label col-md-4" for="debitlimit">Debit Limit</label>
                                            <div class="col-md-8">
                                                <input id="debitlimit" type="text" name="debitlimit" value="<?php if (isset($vendordata)) { echo $vendordata['debitlimit']; } else { echo DEFAULT_DEBIT_LIMIT; } ?>" class="form-control" onkeypress="return decimal_number_validation(event,this.value,8,2)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 p-n">
                                        <div class="form-group row" id="paymentcycle_div">
                                            <label class="control-label col-md-4" for="paymentcycle">Payment Cycle</label>
                                            <div class="col-md-8">
                                                <input id="paymentcycle" type="text" name="paymentcycle" value="<?php if (isset($vendordata)) { echo $vendordata['paymentcycle']; } else { echo DEFAULT_PAYMENT_CYCLE; } ?>" class="form-control" onkeypress="return isNumber(event)" maxlength="4">
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
                            <div class="panel-body">
                                <div class="col-md-10 col-md-offset-2">
                                    <div class="form-group row">
                                        <label for="focusedinput" class="col-md-4 control-label">Activate</label>
                                        <div class="col-md-8">
                                            <div class="col-md-2 col-xs-2" style="padding-left: 0px;">
                                                <div class="radio">
                                                    <input type="radio" name="status" id="yes" value="1" <?php if (isset($vendordata) && $vendordata['status'] == 1) { echo 'checked'; } else { echo 'checked'; } ?>>
                                                    <label for="yes">Yes</label>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-xs-2">
                                                <div class="radio">
                                                    <input type="radio" name="status" id="no" value="0" <?php if (isset($vendordata) && $vendordata['status'] == 0) { echo 'checked'; } ?>>
                                                    <label for="no">No</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12" style="text-align: center;">
                                    <div class="form-group row">
                                        <?php if (isset($vendordata)) { ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                            <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL . $fromurl ?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                                        <?php } else { ?>
                                            <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                            <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
                                            <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
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