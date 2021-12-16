<div class="page-content">
  <div class="page-heading">
    <h1><?php if (isset($assignvehicledata)) { echo 'Edit'; } else { echo 'Add';} ?>
      <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
    <small>
      <ol class="breadcrumb">
        <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
        <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
        <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
        </li>
        <li class="active"><?php if (isset($assignvehicledata)) {echo 'Edit';} else {echo 'Add';} ?>
          <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
      </ol>
    </small>
  </div>

  <div class="container-fluid">
    <div data-widget-group="group1">
      <div class="panel panel-default border-panel">
        <div class="panel-body">
          <form class="form-horizontal" id="form-assignvehicle">
            <input id="id" type="hidden" name="id" class="form-control" value="<?php if (isset($assignvehicledata)) { echo $assignvehicledata['id']; } ?>">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" id="vehicleid_div">
                  <label for="vehicleid" class="col-md-4 col-sm-3 control-label">Select Vehicle <span class="mandatoryfield"> *</span></label>
                  <div class="col-md-4 col-sm-6">
                    <?php if (!isset($assignvehicledata)) { ?>
                      <select id="vehicleid" name="vehicleid[]" class="selectpicker form-control" show-data-subtext="on" title="Select Vehicle" data-size="5" multiple>
                        <?php foreach ($vehicledata as $vehicle) { ?>
                          <option value="<?php echo $vehicle['id']; ?>"><?php echo $vehicle['vehiclename'] . " (" . $vehicle['vehicleno'] . ")"; ?></option>
                        <?php } ?>
                      </select>
                    <?php } else { ?>
                      <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-size="5" data-live-search="true">
                        <option value="0">Select Vehicle</option>
                        <?php foreach ($vehicledata as $vehicle) { ?>
                          <option value="<?php echo $vehicle['id']; ?>" <?php if (isset($assignvehicledata) && $assignvehicledata['vehicleid'] == $vehicle['id']) { echo "selected"; } ?>><?php echo $vehicle['vehiclename'] . " (" . $vehicle['vehicleno'] . ")"; ?>
                          </option>
                        <?php } ?>
                      </select>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" id="site_div">
                  <label for="siteid" class="col-md-4 col-sm-3 control-label">Select Site <span class="mandatoryfield">*</span></label>
                    <div class="col-md-3 col-sm-5 col-xs-9 pr-n">
                      <select id="siteid" name="siteid" class="selectpicker form-control" data-size="5" data-live-search="true">
                        <option value="0">Select Site</option>
                        <?php foreach ($sitedata as $sd) { ?>
                          <option value="<?php echo $sd['id']; ?>" <?php if (isset($assignvehicledata) && $assignvehicledata['siteid'] == $sd['id']) { echo "selected"; } ?>><?php echo $sd['sitename']; ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>
                    <div class="col-md-1 col-xs-1 col-sm-1 pl-sm text-right">
                        <a href="<?=ADMIN_URL.'site/add-site'?>" class="btn btn-primary btn-raised" target="_blank" style="padding: 5px 10px;margin-top: 8px;"><i class="fa fa-plus"></i></a>
                    </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group" id="date_div">
                  <label for="assignvehicledate" class="col-md-4 col-sm-3 control-label">Date <span class="mandatoryfield">*</span></label>
                  <div class="col-md-4 col-sm-6">
                    <div class="input-group">
                      <input id="assignvehicledate" type="text" name="assignvehicledate" class="form-control" value="<?php if (isset($assignvehicledata)) { echo $this->general_model->displaydate($assignvehicledata['date']); } ?>" readonly>
												<span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
											</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="form-group">
                <label class="col-md-4 col-sm-3 col-xs-2"></label>
                <div class="col-md-8 col-sm-9 col-xs-10">
                  <?php if (!empty($assignvehicledata)) { ?>
                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                  <?php } else { ?>
                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                    <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                  <?php } ?>
                  <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL ?>assign-vehicle" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>