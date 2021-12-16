<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($fueldata)) {echo 'Edit';} else {echo 'Add';} ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
                </li>
                <li class="active"><?php if (isset($fueldata)) {echo 'Edit';} else {echo 'Add';} ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
        <div class="panel panel-default border-panel">
            <div class="panel-body">
                <form class="form-horizontal" id="form-vehiclefuel">
                    <input type="hidden" name="fuelid" value="<?php if (isset($fueldata)) { echo $fueldata['id']; } ?>">
                    <div class="row">
                        <div class="col-md-12 pl-sm pr-sm">
                            <div class="col-md-3">
                                <div class="form-group" id="vehicle_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="vehicleid" class="control-label">Vehicle <span class="mandatoryfield">*</span></label>
                                        <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-live-search="true">
                                            <option value="0">Select Vehicle</option>
                                            <?php foreach ($vehicledata as $vd) { ?>
                                                <option value="<?php echo $vd['id']; ?>" <?php if (isset($fueldata) && $fueldata['vehicleid'] == $vd['id']) { echo "selected"; } ?>><?php echo $vd['vehiclename'] . " (" . $vd['vehicleno'].")"; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="partyname_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="partyid" class="control-label">Driver <span class="mandatoryfield">*</span></label>
                                        <select id="partyid" name="partyid" class="selectpicker form-control" data-live-search="true">
                                            <option value="0">Select Driver</option>
                                            <?php foreach ($partydata as $pd) { ?>
                                                <option value="<?php echo $pd['id']; ?>" <?php if (isset($fueldata) && $fueldata['partyid'] == $pd['id']) {echo "selected"; } ?>><?php echo $pd['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="vehiclefueldate_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="vehiclefueldate" class="control-label">Date <span class="mandatoryfield">*</span></label>
                                        <div class="input-group">
                                        <input id="vehiclefueldate" type="text" name="vehiclefueldate" class="form-control" value="<?php if (isset($fueldata)) { if($fueldata['date']!="0000-00-00"){ echo $this->general_model->displaydate($fueldata['date']); } } else { echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="billno_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="billno" class="control-label">Bill No. <span class="mandatoryfield">*</span></label>
                                        <input type="text" id="billno" name="billno" class="form-control" value="<?php if (isset($fueldata)) { echo $fueldata['billno']; } ?>" onkeypress="return alphanumeric(event)" maxlength="20">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 pl-sm pr-sm">
                            <div class="col-md-3">
                                <div class="form-group" id="fuel_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="fueltype" class="control-label">Fuel type <span class="mandatoryfield">*</span></label>
                                        <select id="fueltype" name="fueltype" class="selectpicker form-control" data-live-search="true">
                                            <option value="0">Select Fuel type</option>
                                            <?php foreach ($this->Fueltype as $key => $fuel) { ?>
                                                <option value="<?php echo $key; ?>" <?php if (isset($fueldata) && $fueldata['fueltype'] == $key) { echo "selected"; } ?>><?php echo $fuel; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="payment_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="paymenttype" class="control-label">Payment Type <span class="mandatoryfield">*</span></label>
                                        <select id="paymenttype" name="paymenttype" class="selectpicker form-control" data-live-search="true">
                                            <option value="0">Select Payment Type</option>
                                            <option value="1" <?php if (isset($fueldata) && $fueldata['paymenttype'] == 1) { echo "selected"; } ?>>Petro Card</option>
                                            <option value="2" <?php if (isset($fueldata) && $fueldata['paymenttype'] == 2) {echo "selected";} ?>>Cash</option>
                                            <option value="3" <?php if (isset($fueldata) && $fueldata['paymenttype'] == 3) {echo "selected";} ?>>Bank</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="liter_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="liter" class="control-label">Liter <span class="mandatoryfield">*</span></label>
                                        <input id="liter" type="text" name="liter" class="form-control" value="<?php if (isset($fueldata)) {echo $fueldata['liter'];} ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="amount_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="amount" class="control-label">Amount <span class="mandatoryfield">*</span></label>
                                        <input id="amount" type="text" name="amount" class="form-control" value="<?php if (isset($fueldata)) {echo $fueldata['amount'];} ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 pl-sm pr-sm">
                            <div class="col-md-3">
                                <div class="form-group" id="km_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="km" class="control-label">KM / Hr<span class="mandatoryfield">*</span></label>
                                        <input id="km" type="text" name="km" class="form-control" value="<?php if (isset($fueldata)) {echo $fueldata['km'];} ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="location_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="location" class="control-label">Location</label>
                                        <input type="text" id="location" name="location" class="form-control" value="<?php if (isset($fueldata)) { echo $fueldata['location']; } ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" id="remarks_div">
                                    <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                        <label for="remarks" class="control-label">Remarks</label>
                                        <textarea id="remarks" name="remarks" class="form-control"><?php if (isset($fueldata)) { echo $fueldata['remarks']; } ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                   
                    <div class="row">
                        <div class="col-md-3 text-left">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="control-label">Attachment</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="fuelfiledata_div">
                        <?php if (isset($fueldata) && !empty($fuelfiledata) && isset($fuelfiledata)) { ?>
                        <script type="text/javascript">
                            var fuelfilecount = '<?= count($fuelfiledata) ?>';
                        </script>
                        <?php for ($i = 0; $i < count($fuelfiledata); $i++) {  ?>
                            <input type="hidden" name="documentid[<?=$i+1?>]" value="<?=$fuelfiledata[$i]['id']?>" id="documentid<?=$i+1?>">
                            <input type="hidden" name="olddocfile[<?=$i+1?>]" id="olddocfile<?=$i+1?>" value="<?php echo $fuelfiledata[$i]['file']; ?>"> 
                            <div class="pl-n">
                                <div class="col-md-4 col-sm-6 fuelfile" id="fuelfilecount<?= $i + 1; ?>">
                                    <div class="form-group">
                                        <div class="col-md-9 col-xs-9">
                                            <div class="input-group">
                                                <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">
                                                    <span class="btn btn-primary btn-raised btn-file">Browse...
                                                        <input type="file" class="fuelfile" name="fuelfile<?= $i + 1; ?>" id="fuelfile<?= $i + 1; ?>" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validfuelfile($(this),'fuelfile<?= $i + 1; ?>',this)">
                                                    </span>
                                                </span>
                                                <input type="text" readonly="" id="Filetext<?= $i + 1; ?>" name="Filetext[]" value="<?= $fuelfiledata[$i]['file'] ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-xs-3 pl-n pr-n mt-sm">
                                            <?php if($i==0){?>
                                                <?php if(count($fuelfiledata)>1){ ?>
                                                    <button type="button" class="btn btn-danger btn-raised remove_btn" onclick="removefuelfile(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                <?php }else { ?>
                                                    <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addnewfuelfile()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                <?php } ?>
                                            <?php }else if($i!=0) { ?>
                                                <button type="button" class="btn btn-danger btn-raised remove_btn" onclick="removefuelfile(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                            <?php } ?>
                                            <button type="button" class="btn btn-danger btn-raised btn-sm remove_btn" onclick="removefuelfile(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                            <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addnewfuelfile()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php } else { ?>
                        <script type="text/javascript">
                            var fuelfilecount = 1;
                        </script>
                        
                        <div class="col-md-4 col-sm-6 col-xs-12 fuelfile" id="fuelfilecount1">
                            <div class="form-group">
                                <div class="col-md-9 col-xs-9">
                                    <div class="input-group">
                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">
                                            <span class="btn btn-primary btn-raised btn-file">Browse...
                                                <input type="file" class="fuelfile" name="fuelfile1" id="fuelfile1" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf,.doc,.docx" onchange="validfuelfile($(this),'fuelfile1',this)">
                                            </span>
                                        </span>
                                        <input type="text" readonly="" id="Filetext1" name="Filetext[]" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3 col-xs-3 pl-n pr-n mt-sm">
                                    <button type="button" class="btn btn-danger btn-raised remove_btn" id="p1" onclick="removefuelfile(1)" style="padding: 5px 10px;display: none;">
                                        <i class="fa fa-minus"></i>
                                        <div class="ripple-container"></div>
                                    </button>
                                    <button type="button" class="btn btn-primary btn-raised add_btn" id="p1" onclick="addnewfuelfile()" style="padding: 5px 10px;">
                                        <i class="fa fa-plus"></i>
                                        <div class="ripple-container"></div>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="focusedinput" class="col-md-4 col-xs-1 control-label"></label>
                                <div class="col-md-8 col-xs-11">
                                    <?php if (!empty($fueldata)) { ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                    <?php } else { ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                    <?php } ?>
                                    <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                    <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>