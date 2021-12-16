<script type="text/javascript">
    var countryid = '<?php if(isset($sitedata) && !empty($sitedata['countryid'])) { echo $sitedata['countryid']; } else { echo DEFAULT_COUNTRY_ID; } ?>';
    var provinceid = '<?php if (isset($sitedata) && !empty($sitedata['provinceid'])) { echo $sitedata['provinceid']; } else { echo 0; } ?>';
    var cityid = '<?php if (isset($sitedata) && !empty($sitedata['cityid'])) { echo $sitedata['cityid'];}else{ echo 0; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($sitedata)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
                </li>
                <li class="active"><?php if (isset($sitedata)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>
    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="panel panel-default border-panel">
                <div class="panel-body">
                    <form class="form-horizontal" id="siteform">
                        <input type="hidden" name="siteid" id="siteid" value="<?php if (isset($sitedata)) { echo $sitedata['id']; } ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" id="sitename_div">
                                    <label for="sitename" class="col-md-4 control-label">Site Name <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <input id="sitename" type="text" name="sitename" class="form-control" value="<?php if (isset($sitedata)) { echo $sitedata['sitename']; } ?>" onkeypress="return onlyAlphabets(event)">
                                    </div>
                                </div>
                                <div class="form-group" id="sitemanager_div">
                                    <label for="sitemanagerid" class="col-md-4 control-label">Site Manager <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <select id="sitemanagerid" name="sitemanagerid[]" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="8" title="Select Site Manager" multiple>
                                            <?php foreach ($partydata as $party) { ?>
                                                <option value="<?php echo $party['id']; ?>" <?php if (isset($sitedata) && in_array($party['id'], explode(",", $sitedata['sitemanagerid']))) { echo "selected"; } ?>><?php echo $party['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="address_div">
                                    <label for="adderss" class="col-md-4 control-label">Address <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="address" name="address"><?php if (isset($sitedata)) { echo $sitedata['address']; } ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="country_div">
                                    <label for="countryid" class="col-md-4 control-label">Country <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <select id="countryid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select Country</option>
                                            <?php foreach ($countrydata as $country) { ?>
                                                <option value="<?php echo $country['id']; ?>" <?php if ((isset($sitedata) && $sitedata['countryid'] == $country['id']) || DEFAULT_COUNTRY_ID == $country['id']) { echo "selected"; } ?>><?php echo $country['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="province_div">
                                    <label for="provinceid" class="col-md-4 control-label">Province <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <select id="provinceid" name="provinceid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select Province</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="city_div">
                                    <label for="cityid" class="col-md-4 control-label">City <span class="mandatoryfield">*</span></label>
                                    <div class="col-md-8">
                                        <select id="cityid" name="cityid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select City</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" id="petrocardno_div">
                                    <label for="petrocardno" class="col-md-4 control-label">Petro Card No.</label>
                                    <div class="col-md-8">
                                    <input id="petrocardno" type="text" name="petrocardno" class="form-control" value="<?php if (isset($sitedata)) { echo $sitedata['petrocardno']; } ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="focusedinput" class="col-md-5 col-xs-4 control-label">Activate</label>
                                        <div class="col-md-1 col-xs-2" style="padding-left: 0px; margin-left: 0px;">
                                            <div class="radio">
                                                <input type="radio" name="status" id="yes" value="1" <?php if (isset($sitedata) && $sitedata['status'] == 1) { echo 'checked'; } else { echo 'checked'; } ?>>
                                                <label for="yes">Yes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1 col-xs-2">
                                            <div class="radio">
                                                <input type="radio" name="status" id="no" value="0" <?php if (isset($sitedata) && $sitedata['status'] == 0) { echo 'checked'; } ?>>
                                                <label for="no">No</label>
                                            </div>
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 col-xs-12 text-center">
                                <?php if (!empty($sitedata)) { ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                <?php } else { ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                <?php } ?>
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL ?>site" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>