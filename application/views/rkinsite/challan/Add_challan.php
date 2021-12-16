<?php
$challantypedata = '';
if (!empty($challantype)) {
    foreach ($challantype as $ct) {
        $challantypedata .= '<option value="' . $ct['id'] . '">' . $ct['challantype'] . '</option>';
    }
}
?>
<script>
    var challantypedata = '<?= $challantypedata ?>';
</script>

<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($challandata)) { echo 'Edit'; } else { echo 'Add'; } ?>
            <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
                </li>
                <li class="active"><?php if (isset($challandata)) { echo 'Edit'; } else { echo 'Add'; } ?>
                    <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">

            <div class="panel panel-default border-panel">
                <div class="panel-body">
                    <form class="form-horizontal" id="form-challan">

                        <input type="hidden" name="challanid" id="challanid" value="<?php if (isset($challandata)) { echo  $challandata['id']; } ?>">
                        <div class="row">
                            <div class="col-md-12 pl-sm pr sm">
                                <div class="col-md-4">
                                <div class="form-group" id="vehicle_div">
                                    <div class="col-md-12 pl-xs pr-xs">
                                        <label for="vehicle" class="control-label">Vehicle <span class="mandatoryfield"> *</span></label>
                                        <select id="vehicle" name="vehicle" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select Vehicle</option>
                                            <?php foreach ($vehicledata as $vehicle) { ?>
                                                <option value="<?php echo $vehicle['id']; ?>" <?php if (isset($challandata) && $challandata['vehicleid'] == $vehicle['id']) { echo "selected"; } ?>><?php echo $vehicle['vehiclename'] . "(" . $vehicle['vehicleno'] . ")"; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group" id="challanfor_div">
                                    <div class="col-md-12 pl-xs pr-xs">
                                        <label for="challanfor" class="control-label">Challan for (Driver) <span class="mandatoryfield"> *</span></label>
                                        <select id="challanfor" name="challanfor" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                            <option value="0">Select Driver</option>
                                            <?php foreach ($partydata as $party) { ?>
                                                <option value="<?php echo $party['id']; ?>" <?php if (isset($challandata) && $challandata['partyid'] == $party['id']) { echo "selected"; } ?>><?php echo $party['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                        </div>

                        <hr class="pl-sm pr-sm">
                        <script>
                            var rowcount = 1;
                        </script>
                        <div class="panel panel-transparent">
                            <div class="panel-body p-sm mb-n" id="challanmore">
                                <div class="row">
                                    <div class="col-md-12 visible-lg visible-md">
                                        <div class="form-group">
                                            <div class="col-md-2 pl-sm">
                                                <label class="control-label"> Challan Type <span class="mandatoryfield"> *</span></label>
                                            </div>
                                            <div class="col-md-2 pl-sm">
                                                <label class="control-label">Date <span class="mandatoryfield"> *</span></label>
                                            </div>
                                            <div class="col-md-2 pr-sm text-right">
                                                <label class="control-label">Amount (<?= CURRENCY_CODE ?>) <span class="mandatoryfield"> *</span></label>
                                            </div>
                                            <div class="col-md-3 pl-sm">
                                                <label class="control-label">Attachment</label>
                                            </div>
                                            <div class="col-md-2 pl-sm">
                                                <label class="control-label">Remarks</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row challancheck" id="row1">
                                    <div class="col-md-2 col-sm-6">
                                        <div class="form-group" id="challantype_div_1">
                                            <div class="col-md-12 pr-sm pl-sm">
                                                <select class="selectpicker form-control challantype" id="challantype1" name="challantype[1]" data-live-search="true">
                                                    <option value="0">Select Challan Type</option>
                                                    <?php foreach ($challantype as $pt) { ?>
                                                        <option value="<?php echo $pt['id']; ?>" <?php if (isset($challandata) && $challandata['challantypeid'] == $pt['id']) { echo "selected"; } ?>><?php echo $pt['challantype']; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6">
                                        <div class="form-group date_div" id="date_div_1">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <div class="input-group">
                                                    <input type="text" id="date1" name="date[1]" placeholder="Enter Date" class="form-control date challanrow" value="<?php if (isset($challandata)) { echo $this->general_model->displaydate($challandata['date']); } ?>" readonly>
                                                    <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <div class="form-group amount_div" id="amount_div_1">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" id="amount1" name="amount[1]" placeholder="Enter Amount" value="<?php if (isset($challandata)) { echo $challandata['amount']; } ?>" class="form-control challanamount amount text-right challanrow" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6" style="padding:0px 10px 0px 10px;">
                                        <input type="hidden" id="oldfile1" name="oldfile[1]" value="<?php if (isset($challandata)) { echo $challandata['attachment']; } ?>">
                                        <div class="form-group" id="file1_div">
                                            <div class="col-md-12">
                                                <div class="input-group" id="fileupload1">
                                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                        <span class="btn btn-primary btn-raised btn-sm btn-file"><i class="fa fa-upload"></i>
                                                            <input type="file" name="attachment1" id="file1" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validfile($(this),'file1',this)">
                                                        </span>
                                                    </span>
                                                    <input type="text" id="Filetext1" placeholder="Enter File" class="form-control challanrow" name="Filetext[]" value="<?php if (isset($challandata)) { echo $challandata['attachment']; } ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-sm-6">
                                        <div class="form-group" id="remarks_div">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <input type="text" id="remark1" name="remarks[1]" placeholder="Enter Remarks" value="<?php if (isset($challandata)) { echo $challandata['remarks']; } ?>" class="form-control challanrow">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 addrowbutton">
                                        <div class="form-group" id="button_div" <?php if (isset($challandata)) { echo "style='display:none;'"; } ?>>
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <button type="button" class="btn btn-danger btn-raised remove_btn" id="p1" onclick="removerow(1)" style="padding: 5px 10px;margin-top: 14px; display:none">
                                                    <i class="fa fa-minus"></i>
                                                    <div class="ripple-container"></div>
                                                </button>
                                                <button type="button" class="btn btn-primary btn-raised add_btn" id="p1" onclick="addnewrow()" style="padding: 5px 10px;margin-top: 14px;">
                                                    <i class="fa fa-plus"></i>
                                                    <div class="ripple-container"></div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row totalChallanAmount_div">
                                <div class="col-md-12">
                                    <hr>
                                </div>
                                <label class="control-label col-md-4">
                                    <h5><b>Total Amount (<?= CURRENCY_CODE ?>) :</b></h5>
                                </label>
                                <h5><span class="col-md-2 text-right" id="totalcount">0.00</span></h5>
                            </div>
                            <hr>
                        </div>
                        <div class="form-group">
                            <label for="focusedinput" class="col-md-4 control-label"></label>
                            <div class="col-md-8 col-sm-9 col-xs-11 col-xs-offset-1 col-md-offset-4 col-sm-offset-3">
                                <?php if (!empty($challandata)) { ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                    <input type="button" id="submitandnew" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                <?php } else { ?>
                                    <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                    <input type="button" id="submitandnew" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                <?php } ?>
                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>