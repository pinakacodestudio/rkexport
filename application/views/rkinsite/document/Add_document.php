
<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($site)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
                </li>
                <li class="active"><?php if (isset($site)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">

            <div class="panel panel-default border-panel">
                <div class="panel-body">
                    <form class="form-horizontal" id="form-site">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="form-group" id="sitename_div">

                                    <label for="sitename" class="col-md-4 control-label">Site Name<span class="mandatoryfield"> *</span></label>
                                    <div class="col-md-8">
                                        <input id="id" type="hidden" name="id" class="form-control" value="<?php if (isset($site)) {
                                                                                                                echo $site['id'];
                                                                                                            } ?>">
                                        <input id="sitename" type="text" name="sitename" class="form-control" value="<?php if (isset($site)) {
                                                                                                                            echo $site['sitename'];
                                                                                                                        } ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="form-group" id="country_div">
                                    <label for="countryid" class="col-md-4 control-label">Country</label>
                                    <div class="col-md-8">
                                        <select id="countryid" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select Country</option>
                                            <?php foreach ($countrylist as $country) { ?>
                                                <option value="<?php echo $country['id']; ?>" <?php if (isset($site) && $location['countryid'] == $country['id']) {
                                                                                                    echo "selected";
                                                                                                }  ?>><?php echo $country['name']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="form-group" id="sitemanager_div">
                                    <label for="sitemanager" class="col-sm-4 control-label">Site Manager<span class="mandatoryfield"> *</span></label>
                                    <div class="col-sm-8">
                                        <input id="sitemanager" type="text" name="sitemanager" data-provide="sitemanager" value="<?php if (isset($site)) {
                                                                                                                                        echo $site['sitemanager'];
                                                                                                                                    } ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">  
                                <div class="form-group" id="province_div">
                                    <label for="provinceid" class="col-md-4 control-label">Province</label>
                                    <div class="col-md-8">
                                        <select id="provinceid" name="province" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="form-group" id="address_div">
                                    <label for="adderss" class="col-md-4 control-label">Address</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control" id="address" name="address"><?php if (isset($site)) {
                                                                                                        echo $site['address'];
                                                                                                    } ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="form-group" id="city_div">
                                    <label for="cityid" class="col-md-4 control-label">City</label>
                                    <div class="col-md-8">
                                        <select id="cityid" name="city" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">

                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="form-group">
                            <label for="focusedinput" class="col-md-5 control-label">Activate</label>
                            <div class="col-md-6">
                                <div class="col-md-2 col-xs-4" style="padding-left: 0px; margin-left: 0px;">
                                    <div class="radio">
                                        <input type="radio" name="status" id="yes" value="1" <?php if (isset($site) && $site['status'] == 1) {
                                                                                                    echo 'checked';
                                                                                                } else {
                                                                                                    echo 'checked';
                                                                                                } ?>>
                                        <label for="yes">Yes</label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-4">
                                    <div class="radio">
                                        <input type="radio" name="status" id="no" value="0" <?php if (isset($site) && $site['status'] == 0) {
                                                                                                echo 'checked';
                                                                                            } ?>>
                                        <label for="no">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-sm-4 control-label"></label>
                            <div class="col-sm-8">
                                <?php if (!empty($site)) { ?>
                                    <input type="button" id="submit" onclick="validation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                    <input type="button" id="submit" onclick="validationck()" name="submit" value="UPDATE & NEW" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } else { ?>
                                    <input type="button" id="submit" onclick="validation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="button" id="submit" onclick="validationck()" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <?php } ?>
                                <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL ?>document" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>