<?php 
$DOCUMENT_TYPE_DATA = '';
if(!empty($documenttypedata)){
    foreach($documenttypedata as $documenttype){
        $DOCUMENT_TYPE_DATA .= '<option value="'.$documenttype['id'].'">'.$documenttype['documenttype'].'</option>';
    } 
}

$SearchInsuranceUrl=base_url().ADMINFOLDER.'insurance/searchInsuranceCompany';

$challanfordata = '';
if (!empty($driverdata)) {
    foreach ($driverdata as $driver) {
        $challanfordata .= '<option value="'.$driver['id'].'">'.$driver['name'].'</option>';
    }
}

$challantypedata = '';
if (!empty($challantype)) {
    foreach ($challantype as $ct) {
        $challantypedata .= '<option value="' . $ct['id'] . '">' . $ct['challantype'] . '</option>';
    }
}
?>
<script>
    var DOCUMENT_TYPE_DATA = '<?=$DOCUMENT_TYPE_DATA?>';
    var SearchInsuranceUrl = '<?=$SearchInsuranceUrl?>';
    var challanfordata = '<?= $challanfordata ?>';
    var challantypedata = '<?= $challantypedata ?>';
</script>

<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($vehicledata)) { echo 'Edit'; } else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
                </li>
                <li class="active"><?php if (isset($vehicledata)) { echo 'Edit';} else { echo 'Add'; } ?> <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
        <div data-widget-group="group1">
            <div class="row">
				<div class="col-md-12 col-md-12 col-sm-12 col-xs-12 col-lg-12">
                    <form class="form-horizontal" id="vehicle-form">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default border-panel">
                                    <div class="panel-body pt-n">
                                        <input id="vehicleid" type="hidden" name="vehicleid" class="form-control" value="<?php if(isset($vehicledata)) { echo $vehicledata['id']; } ?>">
                                        <div class="row">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <div class="col-md-3">
                                                    <div class="form-group" id="vehiclename_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="vehiclename" class="control-label">Vehicle Name <span class="mandatoryfield"> *</span></label>
                                                            <input id="vehiclename" type="text" name="vehiclename" class="form-control" value="<?php if (isset($vehicledata)) { echo $vehicledata['vehiclename']; } ?>" onkeypress="return onlyAlphabets(event)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="engineno_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="engineno" class="control-label">Engine Number <span class="mandatoryfield">*</span></label>
                                                            <input id="engineno" type="text" name="engineno" class="form-control" value="<?php if (isset($vehicledata)) { echo $vehicledata['engineno']; } ?>" onkeypress="return alphanumeric(event)" maxlength="20">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="chassisno_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="chassisno" class="control-label">Chassis Number <span class="mandatoryfield"> *</span></label>
                                                            <input id="chassisno" type="text" name="chassisno" class="form-control" value="<?php if (isset($vehicledata)) { echo $vehicledata['chassisno']; } ?>" onkeypress="return alphanumeric(event)" maxlength="20">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="company_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="companyid" class="control-label">Company</label>
                                                            <input id="companyid" type="text" name="companyid" data-url="<?php echo base_url().ADMINFOLDER.'vehicle/getActiveVehicleCompany';?>" value="<?php if(isset($vehicledata)){ echo $vehicledata['vehiclecompanyid']; } ?>" placeholder="Select Company" data-provide="companyid" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <div class="col-md-3">
                                                    <div class="form-group" id="vehicleno_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="vehicleno" class="control-label">Vehicle Number <span class="mandatoryfield"> *</span></label>
                                                            <input id="vehicleno" type="text" name="vehicleno" class="form-control" value="<?php if (isset($vehicledata)) { echo $vehicledata['vehicleno']; } ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="owner_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="ownerpartyid" class="control-label">Owner <span class="mandatoryfield"> *</span></label>
                                                            <select id="ownerpartyid" name="ownerpartyid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                                <option value="0">Select Owner</option>
                                                                <?php foreach ($ownerdata as $owner) { ?>
                                                                    <option value="<?php echo $owner['id']; ?>" <?php if (isset($vehicledata) && $vehicledata['ownerpartyid'] == $owner['id']) { echo "selected"; } ?>><?php echo $owner['name']; ?>
                                                                                        </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="vehicletype_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="vehicletype" class="control-label">Vehicle Type <span class="mandatoryfield"> *</span></label>
                                                            <select id="vehicletype" name="vehicletype" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                                <option value="0">Select Vehicle Type</option>
                                                                <?php foreach ($this->Licencetype as $key=>$type) { ?>
                                                                    <option value="<?php echo $key; ?>" <?php if (isset($vehicledata) && $vehicledata['vehicletype'] == $key) { echo "selected"; } ?>><?php echo $type; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="fueltype_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="fueltype" class="control-label">Fuel type <span class="mandatoryfield"> *</span></label>
                                                        <select id="fueltype" name="fueltype" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                            <option value="0">Select Fuel type</option>
                                                                <?php foreach ($this->Fueltype as $key => $fuel) { ?>
                                                                    <option value="<?php echo $key; ?>" <?php if (isset($vehicledata) && $vehicledata['fueltype'] == $key) { echo "selected"; } ?>><?php echo $fuel; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <div class="col-md-3">
                                                    <div class="form-group" id="buyer_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="buyer" class="control-label">Purchase Company <span class="mandatoryfield"> *</span></label>
                                                            <select id="buyer" name="buyer" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                                <option value="0">Select Purchase Company</option>
                                                                <?php foreach ($partydata as $party) { ?>
                                                                    <option value="<?php echo $party['id']; ?>" <?php if (isset($vehicledata) && $vehicledata['buyerid'] == $party['id']) { echo "selected"; } ?>><?php echo $party['name']; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="dateofregistration_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="dateofregistration" class="control-label">Date of Registration</label>
                                                            <div class="input-group">
                                                                <input id="dateofregistration" type="text" name="dateofregistration" class="form-control" value="<?php if (isset($vehicledata) && $vehicledata['dateofregistration']!="0000-00-00") { echo $this->general_model->displaydate($vehicledata['dateofregistration']); } ?>" readonly>
                                                                <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="duedateofregistration_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="duedateofregistration" class="control-label">Due Date of Reg.</label>
                                                            <div class="input-group">
                                                                <input id="duedateofregistration" type="text" name="duedateofregistration" class="form-control" value="<?php if (isset($vehicledata) && $vehicledata['duedateofregistration']!="0000-00-00") { echo $this->general_model->displaydate($vehicledata['duedateofregistration']); } ?>" readonly>
                                                                <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="commercial_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="commercial" class="control-label">Commercial</label>
                                                            <select id="commercial" name="commercial" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                                <option value="0" <?php if (isset($vehicledata) && $vehicledata['commercial'] == 0) { echo "selected"; } ?>>Non Commercial</option>
                                                                <option value="1" <?php if (isset($vehicledata) && $vehicledata['commercial'] == 1) { echo "selected"; } ?>>Commercial</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <div class="col-md-3">
                                                    <div class="form-group" id="petrocardno_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="petrocardno" class="control-label">Petro Card No.</label>
                                                        <input id="petrocardno" type="text" name="petrocardno" class="form-control" value="<?php if (isset($vehicledata)) { echo $vehicledata['petrocardno']; } ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="startingkm_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="startingkm" class="control-label">Starting Km</label>
                                                            <input id="startingkm" type="text" name="startingkm" class="form-control" value="<?php if (isset($vehicledata)) { echo $vehicledata['startingkm']; } ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="fuelrate_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="fuelrate" class="control-label">Fuel Rate</label>
                                                            <input id="fuelrate" type="text" name="fuelrate" class="form-control" value="<?php if (isset($vehicledata)) { echo $vehicledata['fuelrate']; } ?>" onkeypress="return decimal_number_validation(event, this.value, 8)" maxlength="20">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="fuelratetype_div">
                                                        <label for="fuelratetype" class="control-label pl-xs" style="text-align: left;">KM/Hr Based Fuel</label>
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                            <div class="col-md-4 col-sm-3 col-xs-3 pt-xs" style="padding-left: 0px;">
                                                                <div class="radio">
                                                                    <input type="radio" name="fuelratetype" id="KM" value="1" <?php if(isset($vehicledata) && $vehicledata['fuelratetype']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                                    <label for="KM">KM</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6 col-xs-6 pt-xs">
                                                                <div class="radio">
                                                                    <input type="radio" name="fuelratetype" id="Hour" value="2" <?php if(isset($vehicledata) && $vehicledata['fuelratetype']==2){ echo 'checked'; }?>>
                                                                    <label for="Hour">Hour</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <?php if(!isset($vehicledata)){ ?>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="site_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                    <label for="siteid" class="control-label">Select Site</label>
                                                            <select id="siteid" name="siteid" class="selectpicker form-control" data-size="5" data-live-search="true">
                                                                <option value="0">Select Site</option>
                                                                <?php foreach ($sitedata as $sd) { ?>
                                                                <option value="<?php echo $sd['id']; ?>" <?php if (isset($assignvehicledata) && $assignvehicledata['siteid'] == $sd['id']) { echo "selected"; } ?>><?php echo $sd['sitename']; ?>
                                                                </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="sold_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="sold" class="control-label" style="text-align: left;">Sold</label>
                                                            <div class="yesno mt-xs">
                                                                <input type="checkbox" name="sold" value="<?php if(isset($vehicledata) && $vehicledata['sold']==1){ echo '1'; }else{ echo '0'; }?>" <?php if(isset($vehicledata) && $vehicledata['sold']==1){ echo 'checked'; }?>>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group pb-n" id="solddate_div" style="<?php if(isset($vehicledata) && $vehicledata['sold']==1){ echo ''; }else{ echo 'display: none;'; }?>">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                            <label for="solddate" class="control-label">Sold Date <span class="mandatoryfield">*</span></label>
                                                            <input id="solddate" type="text" name="solddate" class="form-control" value="<?php if (isset($vehicledata) && $vehicledata['solddate']!="0000-00-00") { echo $this->general_model->displaydate($vehicledata['solddate']); } ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group" id="soldparty_div" style="<?php if(isset($vehicledata) && $vehicledata['sold']==1){ echo ''; }else{ echo 'display: none;'; }?>">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="soldpartyid" class="control-label">Sold Party <span class="mandatoryfield">*</span></label>
                                                            <select id="soldpartyid" name="soldpartyid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                                <option value="0">Select Party</option>
                                                                <?php foreach ($partydata as $party) { ?>
                                                                    <option value="<?php echo $party['id']; ?>" <?php if (isset($vehicledata) && $vehicledata['soldpartyid'] == $party['id']) { echo "selected"; } ?>><?php echo $party['name']; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <div class="col-md-12">
                                                    <div class="form-group" id="remarks_div">
                                                        <div class="col-md-12 pl-xs pr-xs">
                                                        <label for="remarks" class="control-label">Remarks</label>
                                                            <textarea class="form-control" id="remarks" name="remarks"><?php if (isset($vehicledata)) { echo $vehicledata['remarks']; } ?></textarea>
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
                                    <div class="panel-heading"><h2>Document Details</h2></div>
                                    <div class="panel-body no-padding">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-12 visible-md visible-lg">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <label class="control-label">Document Type <span class="mandatoryfield">*</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <label class="control-label" style="text-align: left;">Doc. No. <span class="mandatoryfield">*</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <label class="control-label">Register Date <span class="mandatoryfield">*</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <label class="control-label">Due Date <span class="mandatoryfield">*</span></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <div class="col-md-12 pr-xs pl-xs">
                                                                <label class="control-label">Attachment</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <?php if(isset($vehicledata) && !empty($vehicledocumentdata)) { ?>
                                                    <?php for ($i=0; $i < count($vehicledocumentdata); $i++) { ?>
                                                        <div class="col-md-12 col-sm-12 countdocuments" id="countdocuments<?=($i+1)?>">
                                                        <input type="hidden" name="documentid[<?=($i+1)?>]" value="<?php echo $vehicledocumentdata[$i]['id']; ?>" id="documentid<?=($i+1)?>">
                                                            <div class="col-md-2 col-sm-4">
                                                                <div class="form-group mt-n" id="documenttype<?=($i+1)?>_div">
                                                                    <div class="col-md-12 pr-xs pl-xs">
                                                                        <select id="documenttypeid<?=($i+1)?>" name="documenttypeid[<?=($i+1)?>]" class="selectpicker form-control documentrow documenttypeid"  data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                            <option value="0">Select Document Type</option>
                                                                            <?=$DOCUMENT_TYPE_DATA?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 col-sm-4">
                                                                <div class="form-group mt-n" id="documentnumber<?=$i+1?>_div">
                                                                    <div class="col-md-12 pr-xs pl-xs">
                                                                        <input id="documentnumber<?=$i+1?>" name="documentnumber[<?=$i+1?>]" class="form-control documentrow documentnumber" placeholder="Enter Document Number" value="<?php echo $vehicledocumentdata[$i]['documentnumber']; ?>">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 col-sm-4">
                                                                <div class="form-group mt-n" id="fromdate<?=$i+1?>_div">
                                                                    <div class="col-md-12 pr-xs pl-xs">
                                                                        <div class="input-group">
                                                                            <input type="text" id="fromdate<?=$i+1?>" name="fromdate[<?=$i+1?>]" class="form-control documentrow fromdate" placeholder="Enter Register Date" value="<?php if($vehicledocumentdata[$i]['fromdate']!="0000-00-00") { echo $this->general_model->displaydate($vehicledocumentdata[$i]['fromdate']); } ?>" readonly>
                                                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 col-sm-4">
                                                                <div class="form-group mt-n" id="duedate<?=$i+1?>_div">
                                                                    <div class="col-md-12 pr-xs pl-xs">
                                                                        <div class="input-group">
                                                                            <input type="text" id="duedate<?=$i+1?>" class="form-control documentrow duedate" placeholder="Enter Due Date" name="duedate[<?=$i+1?>]" value="<?php if($vehicledocumentdata[$i]['duedate']!="0000-00-00") { echo $this->general_model->displaydate($vehicledocumentdata[$i]['duedate']); } ?>" readonly>
                                                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 col-sm-4">
                                                                <div class="form-group mt-n" id="docfile<?=$i+1?>_div">
                                                                    <div class="col-md-12 pr-xs pl-xs">
                                                                        <input type="hidden" id="isvaliddocfile<?=$i+1?>" value="<?=($vehicledocumentdata[$i]['documentfile']!=""?1:0)?>"> 
                                                                        <input type="hidden" name="olddocfile[<?=$i+1?>]" id="olddocfile<?=$i+1?>" value="<?php echo $vehicledocumentdata[$i]['documentfile']; ?>"> 
                                                                        <div class="input-group" id="fileupload<?=$i+1?>">
                                                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                                <span class="btn btn-primary btn-raised btn-file"><i class="fa fa-upload"></i>
                                                                                    <input type="file" name="docfile<?=$i+1?>" class="docfile" id="docfile<?=$i+1?>" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile<?=$i+1?>',this)">
                                                                                </span>
                                                                            </span>
                                                                            <input type="text" readonly="" id="Filetextdocfile<?=$i+1?>"
                                                                                class="form-control documentrow" placeholder="Enter File" name="Filetextdocfile[<?=$i+1?>]" value="<?php echo $vehicledocumentdata[$i]['documentfile']; ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-1 col-sm-12 pt-sm pr-xs addrowbutton">
                                                                <?php if($i==0){?>
                                                                    <?php if(count($vehicledocumentdata)>1){ ?>
                                                                        <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                                    <?php }else { ?>
                                                                        <button type="button" class="btn btn-primary btn-raised add_btn m-n" onclick="addNewDocument()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                                    <?php } ?>
                                                                <?php }else if($i!=0) { ?>
                                                                    <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                                <?php } ?>
                                                                <button type="button" class="btn btn-danger btn-raised btn-sm remove_btn m-n" onclick="removeDocument(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                                <button type="button" class="btn btn-primary btn-raised add_btn m-n" onclick="addNewDocument()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>  
                                                            </div>
                                                            <script type="text/javascript">
                                                                $(document).ready(function() {
                                                                    $("#documenttypeid<?=$i+1?>").val(<?=$vehicledocumentdata[$i]['documenttypeid']?>).selectpicker("refresh");
                                                                }); 
                                                            </script>
                                                        </div>
                                                    <?php } ?>
                                                <?php }else{ ?>
                                                    <div class="col-md-12 col-sm-12 countdocuments" id="countdocuments1">
                                                        <div class="col-md-2 col-sm-4">
                                                            <div class="form-group mt-n" id="documenttype1_div">
                                                                <div class="col-md-12 pr-xs pl-xs">
                                                                    <select id="documenttypeid1" name="documenttypeid[1]" placeholder="Enter Document Number" class="selectpicker form-control documenttypeid" data-live-search="true" data-select-on-tab="true" data-size="5">
                                                                        <option value="0">Select Document Type</option>
                                                                        <?=$DOCUMENT_TYPE_DATA?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-4">
                                                            <div class="form-group mt-n" id="documentnumber1_div">
                                                                <div class="col-md-12 pr-xs pl-xs">
                                                                    <input id="documentnumber1" name="documentnumber[1]" placeholder="Enter Document Number" class="form-control documentrow documentnumber">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-4">
                                                            <div class="form-group mt-n" id="fromdate1_div">
                                                                <div class="col-md-12 pr-xs pl-xs">
                                                                    <div class="input-group">
                                                                        <input type="text" id="fromdate1" name="fromdate[1]" placeholder="Enter Register Date" class="form-control documentrow fromdate" readonly>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-4">
                                                            <div class="form-group mt-n" id="duedate1_div">
                                                                <div class="col-md-12 pr-xs pl-xs">
                                                                    <div class="input-group">
                                                                        <input type="text" id="duedate1" class="form-control documentrow duedate" placeholder="Enter Due Date" name="duedate[1]" readonly>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 col-sm-4">
                                                            <div class="form-group mt-n" id="docfile1_div">
                                                                <div class="col-md-12 pr-xs pl-xs">
                                                                    <input type="hidden" id="isvaliddocfile1" value="0"> 
                                                                    <input type="hidden" name="olddocfile[1]" id="olddocfile1" value=""> 
                                                                    <div class="input-group" id="fileupload1">
                                                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                            <span class="btn btn-primary btn-raised btn-file"><i
                                                                                    class="fa fa-upload"></i>
                                                                                <input type="file" name="docfile1"
                                                                                    class="docfile" id="docfile1"
                                                                                    accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validdocumentfile($(this),'docfile1',this)">
                                                                            </span>
                                                                        </span>
                                                                        <input type="text" readonly="" id="Filetextdocfile1"
                                                                            class="form-control documentrow" placeholder="Enter File" name="Filetextdocfile[1]" value="">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1 col-sm-12 pt-sm pr-xs addrowbutton">
                                                            <button type="button" class="btn btn-danger btn-raised remove_btn m-n" onclick="removeDocument(1)" style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>

                                                            <button type="button" class="btn btn-primary btn-raised add_btn m-n" onclick="addNewDocument()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default border-panel">
                                    <div class="panel-heading"><h2>Insurance Details</h2></div>
                                    <div class="panel-body no-padding">
                                    <?php if(isset($vehicledata) && !empty($vehicleInsurancedata)){ ?>
                                        <?php for ($i=0; $i < count($vehicleInsurancedata); $i++) { ?>
                                        <div class="col-md-12 col-sm-12 countinsurances" id="countinsurance<?=($i+1)?>">
                                            <input type="hidden" name="insuranceid[<?=($i+1)?>]" value="<?php echo $vehicleInsurancedata[$i]['id']; ?>" id="insuranceid<?=($i+1)?>">
                                            <div class="col-md-3 col-sm-4">
                                                <div class="form-group" id="insurancecompanyname_div_<?=($i+1)?>">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="insurancecompanyname<?=($i+1)?>" class="control-label">Insurance Company <span class="mandatoryfield">*</span></label>
                                                        <input id="insurancecompanyname<?=($i+1)?>" type="text" name="insurancecompanyname[<?=($i+1)?>]" data-url="<?=$SearchInsuranceUrl?>" value="<?php if(isset($vehicleInsurancedata)){ echo $vehicleInsurancedata[$i]['companyname']; } ?>" placeholder="Select Insurance Company" data-provide="companyname" class="form-control insurancecompany">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4">
                                                <div class="form-group" id="insuranceagent_div_<?=($i+1)?>">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="insuranceagent<?=($i+1)?>" class="control-label">Insurance Agent</label>
                                                        <select name="insuranceagent[<?=($i+1)?>]" id="insuranceagent<?=($i+1)?>" data-live-search="true" class="selectpicker form-control">
                                                            <option value="0">Select Insurance Agent</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="policyno_div_<?=($i+1)?>">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="policyno<?=($i+1)?>" class="control-label">Policy No. <span class="mandatoryfield">*</span></label>
                                                        <input id="policyno<?=($i+1)?>" type="text" name="policyno[<?=($i+1)?>]" value="<?php if(isset($vehicleInsurancedata)){ echo $vehicleInsurancedata[$i]['policyno']; } ?>" class="form-control insurancerow">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group text-right" id="amount_div_<?=($i+1)?>">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="amount<?=($i+1)?>" class="control-label">Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></label>
                                                        <input id="amount<?=($i+1)?>" type="text" name="amount[<?=($i+1)?>]"  class="form-control insurancerow text-right" value="<?php if(isset($vehicleInsurancedata)){ echo $vehicleInsurancedata[$i]['amount']; } ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="paymentdate_div_<?=($i+1)?>">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="paymentdate<?=($i+1)?>" class="control-label">Payment Date</label>
                                                        <input type="text" class="input-small form-control insurancerow" name="paymentdate[<?=($i+1)?>]" id="paymentdate<?=($i+1)?>" value="<?php if(isset($vehicleInsurancedata)){ echo $this->general_model->displaydate($vehicleInsurancedata[$i]['paymentdate']); } ?>" style="text-align: left;" readonly/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4">
                                                <div class="form-group" id="insurancefile<?=($i+1)?>_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="textfile<?=($i+1)?>" class="control-label">Proof</label>
                                                        <input type="hidden" id="isvalidInsurancefile<?=$i+1?>" value="<?=($vehicleInsurancedata[$i]['proof']!=""?1:0)?>"> 
                                                        <input type="hidden" name="oldInsurancefile[<?=$i+1?>]" id="oldInsurancefile<?=$i+1?>" value="<?php echo $vehicleInsurancedata[$i]['proof']; ?>"> 
                                                        <div class="input-group" id="fileupload<?=($i+1)?>">
                                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                <span class="btn btn-primary btn-raised btn-file"><i
                                                                                    class="fa fa-upload"></i>
                                                                    <input type="file" name="fileproof<?=($i+1)?>"  id="fileproof<?=($i+1)?>" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validInsurancefile($(this),'insurancefile<?=($i+1)?>',this)">
                                                                </span>
                                                            </span>                                        
                                                            <input type="text" id="textfile<?=($i+1)?>" class="form-control" name="textfile[<?=($i+1)?>]" value="<?php if(isset($vehicleInsurancedata)){ echo $vehicleInsurancedata[$i]['proof']; } ?>" readonly >
                                                        </div> 
                                                    </div>
                                                </div> 
                                            </div> 
                                            <div class="col-md-4 col-sm-8">
                                                <div class="form-group" id="insurancedate_div_<?=($i+1)?>">
                                                    <div class="input-daterange input-group datepicker-range" id="datepicker-range<?=($i+1)?>">
                                                        <div class="col-md-6 col-sm-6 pr-xs pl-xs">
                                                            <label class="control-label" for="insurancefromdate<?=($i+1)?>" style="text-align: left;">Register Date <span class="mandatoryfield">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="input-small form-control insurancerow insurancedate" style="text-align: left;" id="insurancefromdate<?=($i+1)?>" name="insurancefromdate[<?=($i+1)?>]" value="<?php if(isset($vehicleInsurancedata)){ echo $this->general_model->displaydate($vehicleInsurancedata[$i]['fromdate']); } ?>" readonly/>
                                                                <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 pr-xs pl-xs">
                                                            <label class="control-label" for="insurancetodate<?=($i+1)?>" style="text-align: left;">Due Date <span class="mandatoryfield">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" class="input-small form-control insurancerow insurancedate" style="text-align: left;" id="insurancetodate<?=($i+1)?>" name="insurancetodate[<?=($i+1)?>]" value="<?php if(isset($vehicleInsurancedata)){ echo $this->general_model->displaydate($vehicleInsurancedata[$i]['todate']); } ?>" readonly/>
                                                                <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-2 pt-md pr-xs addrowbutton add_Insurance_row_button_div">
                                                <?php if($i==0){?>
                                                    <?php if(count($vehicleInsurancedata)>1){ ?>
                                                        <button type="button" class="btn btn-danger btn-raised remove_btn_insurance m-n" onclick="removeInsuranceRow(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-primary btn-raised add_btn_insurance m-n" onclick="addInsuranceRow()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>
                                                <?php }else if($i!=0) { ?>
                                                    <button type="button" class="btn btn-danger btn-raised remove_btn_insurance m-n" onclick="removeInsuranceRow(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                <?php } ?>
                                                <button type="button" class="btn btn-danger btn-raised remove_btn_insurance m-n" onclick="removeInsuranceRow(<?=($i+1)?>)" style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-primary btn-raised add_btn_insurance m-n" onclick="addInsuranceRow()"  style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div class="col-md-12 col-xs-12 col-sm-12 Insurance-hr"><hr></div>
                                        </div>
                                        <script>
                                                $(document).ready(function() {
                                                    getInsuranceAgent(<?=$i+1?>);
                                                    $("#insuranceagent<?=$i+1?>").val(<?=$vehicleInsurancedata[$i]['insuranceagentid']?>).selectpicker("refresh");
                                                }); 
                                        </script>
                                    <?php } }else{ ?>
                                        <div class="col-md-12 col-sm-12 countinsurances" id="countinsurance1">
                                            <div class="col-md-3 col-sm-4">
                                                <div class="form-group" id="insurancecompanyname_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="insurancecompanyname1" class="control-label">Insurance Company <span class="mandatoryfield">*</span></label>
                                                        <input id="insurancecompanyname1" type="text" name="insurancecompanyname[1]" data-url="<?=$SearchInsuranceUrl?>" value="<?php if(isset($insurancedata)){ echo $insurancedata['companyname']; } ?>" placeholder="Select Insurance Company" data-provide="companyname" class="form-control insurancecompany">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4">
                                                <div class="form-group" id="insuranceagent_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="insuranceagent1" class="control-label">Insurance Agent</label>
                                                        <select name="insuranceagent[1]" id="insuranceagent1" data-live-search="true" class="selectpicker form-control">
                                                            <option value="0">Select Insurance Agent</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="policyno_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="policyno1" class="control-label">Policy No. <span class="mandatoryfield">*</span></label>
                                                        <input id="policyno1" type="text" name="policyno[1]" class="form-control insurancerow">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group text-right" id="amount_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="amount1" class="control-label">Amount (<?=CURRENCY_CODE?>) <span class="mandatoryfield">*</span></label>
                                                        <input id="amount1" type="text" name="amount[1]"  class="form-control insurancerow text-right" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4">
                                                <div class="form-group" id="paymentdate_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="paymentdate1" class="control-label">Payment Date</label>
                                                        <div class="input-group">
                                                            <input type="text" class="input-small form-control insurancerow" name="paymentdate[1]" id="paymentdate1" style="text-align: left;" readonly/>
                                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4">
                                                <div class="form-group" id="insurancefile1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label for="textfile1" class="control-label">Proof</label>
                                                        <div class="input-group" id="fileupload1">
                                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                <span class="btn btn-primary btn-raised btn-file"><i
                                                                                    class="fa fa-upload"></i>
                                                                    <input type="file" name="fileproof1"  id="fileproof1" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validInsurancefile($(this),'insurancefile1',this)">
                                                                </span>
                                                            </span>                                        
                                                            <input type="text" id="textfile1" class="form-control" name="textfile[1]" readonly >
                                                        </div> 
                                                    </div>
                                                </div> 
                                            </div> 
                                            <div class="col-md-4 col-sm-8">
                                                <div class="form-group" id="insurancedate_div_1">
                                                    <div class="input-daterange input-group datepicker-range" id="datepicker-range1">
                                                        <div class="col-md-6 col-sm-6 pr-xs pl-xs">
                                                            <label class="control-label" for="insurancefromdate1" style="text-align: left;">Register Date <span class="mandatoryfield">*</span></label>
                                                            <div class="input-group">
                                                                    <input type="text" class="input-small form-control insurancerow insurancedate"  id="insurancefromdate1" name="insurancefromdate[1]" style="text-align: left;" readonly/>
                                                                    <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 pr-xs pl-xs">
                                                            <label class="control-label" for="insurancetodate1" style="text-align: left;">Due Date <span class="mandatoryfield">*</span></label>
                                                            <div class="input-group">
                                                                    <input type="text" class="input-small form-control insurancerow insurancedate"  id="insurancetodate1" name="insurancetodate[1]" style="text-align: left;" readonly/>
                                                                <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-sm-4 pt-md pr-xs addrowbutton add_Insurance_row_button_div">
                                                <button type="button" class="btn btn-danger btn-raised remove_btn_insurance m-n" onclick="removeInsuranceRow(1)" style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-primary btn-raised add_btn_insurance m-n" onclick="addInsuranceRow()"  style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                            <div class="col-md-12 col-xs-12 col-sm-12 Insurance-hr p-n"><hr></div>
                                        </div>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default border-panel">
                                    <div class="panel-heading"><h2>Challan Details</h2></div>
                                    <div class="panel-body no-padding">
                                        <div class="col-md-12 visible-md visible-lg">
                                            <div class="col-md-2 ">
                                                <div class="form-group">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label for="challanfor" class="control-label">Challan for (Driver) <span class="mandatoryfield"> *</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 ">
                                                <div class="form-group">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label" style="text-align: left;">Challan Type <span class="mandatoryfield">*</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 ">
                                                <div class="form-group">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Date <span class="mandatoryfield">*</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 ">
                                                <div class="form-group">
                                                    <div class="col-md-12 pr-xs pl-xs text-right">
                                                        <label class="control-label">Amount (<?=CURRENCY_CODE?>)<span class="mandatoryfield">*</span></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 ">
                                                <div class="form-group">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Attachment</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 ">
                                                <div class="form-group">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Remarks</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(isset($vehicledata) && !empty($vehicleChallandata)){ ?>
                                        <?php for ($i=0; $i < count($vehicleChallandata); $i++) { ?>
                                            <div class="col-md-12 col-sm-12 countchallan" id="countchallan<?=($i+1)?>">
                                                <input type="hidden" name="challanid[<?=($i+1)?>]" value="<?php echo $vehicleChallandata[$i]['id']; ?>" id="challanid<?=($i+1)?>">
                                                <div class="col-md-2 col-sm-4">
                                                    <div class="form-group mt-n" id="challanfor<?=($i+1)?>_div">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <select id="challanfor<?=($i+1)?>" name="challanfor[<?=($i+1)?>]" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                                                <option value="0">Select Driver</option>
                                                                <?=$challanfordata?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-4">
                                                    <div class="form-group mt-n" id="challantype<?=($i+1)?>_div">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <select class="selectpicker form-control challantype" id="challantype<?=($i+1)?>" name="challantype[<?=($i+1)?>]" data-live-search="true">
                                                                <option value="0">Select Challan Type</option>
                                                                <?php foreach ($challantype as $pt) { ?>
                                                                    <option value="<?php echo $pt['id']; ?>" <?php if($vehicleChallandata[$i]['challantypeid']==$pt['id']){ echo "selected";} ?>><?php echo $pt['challantype']; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-4 ">
                                                    <div class="form-group mt-n" id="challandate<?=($i+1)?>_div">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <input type="text" id="challandate<?=($i+1)?>" name="challandate[<?=($i+1)?>]" value="<?php echo $this->general_model->displaydate($vehicleChallandata[$i]['date']); ?>" class="form-control date challanrow" placeholder="Enter Challan Date" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-4 ">
                                                    <div class="form-group mt-n" id="challanamount<?=($i+1)?>_div">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <input id="challanamount<?=($i+1)?>" type="text" name="challanamount[<?=($i+1)?>]" value="<?php echo $vehicleChallandata[$i]['amount']; ?>" class="form-control text-right challanamount challanrow" placeholder="Enter Challan Amount" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-sm-4 ">
                                                    <div class="form-group mt-n" id="challanfile1_div">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <input type="hidden" id="isvalidChallanfile<?=$i+1?>" value="<?=($vehicleChallandata[$i]['attachment']!=""?1:0)?>"> 
                                                            <input type="hidden" name="oldChallanfile[<?=$i+1?>]" id="oldChallanfile<?=$i+1?>" value="<?php echo $vehicleChallandata[$i]['attachment']; ?>"> 
                                                            <div class="input-group" id="challanfileupload<?=($i+1)?>">
                                                                <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                    <span class="btn btn-primary btn-raised btn-sm btn-file"><i class="fa fa-upload"></i>
                                                                        <input type="file" name="challanfile<?=($i+1)?>" id="challanfile<?=($i+1)?>" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validfile($(this),'challanfile<?=($i+1)?>',this)">
                                                                    </span>
                                                                </span>
                                                                <input type="text" id="challanFiletext<?=($i+1)?>" value="<?php echo $vehicleChallandata[$i]['attachment']; ?>" class="form-control challanrow" placeholder="Enter Challan File" name="challanFiletext[<?=($i+1)?>]" readonly>
                                                            </div>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <div class="col-md-2 col-sm-4 ">
                                                    <div class="form-group mt-n" id="challanremarks<?=($i+1)?>_div">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <input type="text" id="challanremarks<?=($i+1)?>" name="challanremarks[<?=($i+1)?>]" value="<?php echo $vehicleChallandata[$i]['remarks']; ?>" class="form-control challanrow" placeholder="Enter Challan Remarks">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-sm-12 pt-sm pr-xs addrowbutton">
                                                    <?php if($i==0){?>
                                                        <?php if(count($vehicleChallandata)>1){ ?>
                                                            <button type="button" class="btn btn-danger btn-raised remove_btn_challan m-n" onclick="removeChallanRow(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                        <?php }else { ?>
                                                            <button type="button" class="btn btn-primary btn-raised add_btn_challan m-n" onclick="addChallanRow()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                        <?php } ?>
                                                    <?php }else if($i!=0) { ?>
                                                        <button type="button" class="btn btn-danger btn-raised remove_btn_challan m-n" onclick="removeChallanRow(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                    <?php } ?>
                                                    <button type="button" class="btn btn-danger btn-raised remove_btn_challan m-n" onclick="removeChallanRow(<?=($i+1)?>)" style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-primary btn-raised add_btn_challan m-n" onclick="addChallanRow()"  style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    $("#challanfor<?=$i+1?>").val(<?=$vehicleChallandata[$i]['partyid']?>).selectpicker("refresh");
                                                }); 
                                            </script>
                                        <?php } }else{ ?>
                                        <div class="col-md-12 col-sm-12 countchallan" id="countchallan1">
                                            <div class="col-md-2 col-sm-4 ">
                                                <div class="form-group mt-n" id="challanfor1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <select id="challanfor1" name="challanfor[1]" class="selectpicker form-control" show-data-subtext="on" data-live-search="true" data-size="5">
                                                            <option value="0">Select Driver</option>
                                                            <?=$challanfordata?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4 ">
                                                <div class="form-group mt-n" id="challantype1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <select class="selectpicker form-control challantype" id="challantype1" name="challantype[1]" data-live-search="true">
                                                            <option value="0">Select Challan Type</option>
                                                            <?php foreach ($challantype as $pt) { ?>
                                                                <option value="<?php echo $pt['id']; ?>"><?php echo $pt['challantype']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4 ">
                                                <div class="form-group mt-n" id="challandate1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <div class="input-group">
                                                            <input type="text" id="challandate1" name="challandate[1]" class="form-control date challanrow" placeholder="Enter Challan Date" readonly>
                                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-4 ">
                                                <div class="form-group mt-n" id="challanamount1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <input id="challanamount1" type="text" name="challanamount[1]" class="form-control text-right challanamount challanrow" placeholder="Enter Challan Amount" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-4 ">
                                                <div class="form-group mt-n" id="challanfile1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <div class="input-group" id="challanfileupload1">
                                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                                <span class="btn btn-primary btn-raised btn-sm btn-file"><i class="fa fa-upload"></i>
                                                                    <input type="file" name="challanfile1" id="challanfile1" accept=".png,.jpeg,.jpg,.bmp,.gif,.pdf" onchange="validfile($(this),'challanfile1',this)">
                                                                </span>
                                                            </span>
                                                            <input type="text" id="challanFiletext1" class="form-control challanrow" placeholder="Enter Challan File" name="challanFiletext[1]" readonly>
                                                        </div>
                                                    </div>
                                                </div> 
                                            </div>
                                            <div class="col-md-2 col-sm-4 ">
                                                <div class="form-group mt-n" id="challanremarks1_div">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <input type="text" id="challanremarks1" name="challanremarks[1]" class="form-control challanrow" placeholder="Enter Challan Remarks">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-12 pt-sm pr-xs addrowbutton">
                                                <button type="button" class="btn btn-danger btn-raised remove_btn_challan m-n" onclick="removeChallanRow(1)" style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-primary btn-raised add_btn_challan m-n" onclick="addChallanRow()"  style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div class="col-md-12 totalChallanAmount_div">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-2 text-right">
                                                <label class="control-label">
                                                    <h5><b>Total Amount (<?= CURRENCY_CODE ?>) :</b>
                                                    </h5>
                                                </label>
                                            </div>
                                            <div class="col-md-1 text-right"> <h5><span id="totalcount">0.00</span></h5>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                                         
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default border-panel">
                                    <div class="panel-heading"><h2>Vehicle EMI Details</h2></div>
                                    <div class="panel-body no-padding">
                                        <div class="row" id="partialpaymentoption">
                                            <div class="col-md-12">
                                                <div class="row m-n" id="installmentsetting_div" style="<?php /* if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } */ ?>">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group" id="installmentTotalamount_div">
                                                                <div class="col-md-12 pl-xs pr-xs">
                                                                    <label for="installmentTotalamount" class="control-label">Amount (<?=CURRENCY_CODE?>)<span class="mandatoryfield">*</span></label>
                                                                    <input type="text" class="form-control" id="installmentTotalamount" name="installmentTotalamount" value="<?php if(!empty($vehicledata)){ echo $vehicledata['installmentamount']; } ?>" onkeypress="return isNumber(event)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group" id="noofinstallment_div">
                                                                <div class="col-md-12 pr-xs pl-xs">
                                                                    <label for="noofinstallment" class="control-label">No. of Installment <span class="mandatoryfield">*</span></label>
                                                                    <input type="text" class="form-control" id="noofinstallment" name="noofinstallment" maxlength="2" value="<?php if(!empty($installmentdata)){ echo count($installmentdata); } ?>" onkeypress="return isNumber(event)">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="form-group" id="emidate_div">
                                                                <div class="col-md-12 pr-xs pl-xs">
                                                                    <label for="emidate" class="control-label">EMI Start Date <span class="mandatoryfield">*</span></label>
                                                                    <div class="input-group">
                                                                        <input id="emidate" type="text" name="emidate" value="<?php if(!empty($installmentdata)){ echo $this->general_model->displaydate($installmentdata[0]['date']); } ?>" class="form-control" readonly>
                                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--  <div class="col-md-3">
                                                                <div class="form-group" id="emiduration_div">
                                                                    <div class="col-md-12 pr-sm pl-sm">
                                                                        <label for="emiduration" class="control-label">EMI Duration (In Days) <span class="mandatoryfield">*</span></label>
                                                                        <input id="emiduration" type="text" name="emiduration" value="<?php // if(!empty($installmentdata)){ if(count($installmentdata)==1){ echo "1"; } else{ echo ceil(abs(strtotime($installmentdata[0]['date']) - strtotime($installmentdata[1]['date'])) / 86400); } } ?>" class="form-control" maxlength="4">
                                                                    </div>
                                                                </div>
                                                            </div> -->
                                                        <div class="col-md-2 SetTopMarginOnButton">
                                                            <div class="form-group">
                                                                <div class="col-md-12">
                                                                    <button type="button" onclick="generateinstallment(1)" class="btn btn-primary btn-raised">Generate</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="installmentmaindiv" style="margin-top: 12px;">
                                            <div class="col-md-6 col-xs-12" id="installmentmaindivheading1"  style="<?php if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
                                                <div class="col-md-2 pl-sm pr-sm text-center col-xs-2"><b>Sr. No.</b></div>
                                                <div class="col-md-4 pl-sm pr-sm col-xs-4 text-right"><b>Amount (<?=CURRENCY_CODE?>)</b></div>
                                                <div class="col-md-6 pl-sm pr-sm col-xs-6"><b>Installment Date</b></div>
                                            </div>
                                            <div class="col-md-6" id="installmentmaindivheading2" style="<?php if(!empty($installmentdata) && count($installmentdata)>1){ echo "display: block;"; }else{ echo "display: none;"; } ?>">
                                                <div class="col-md-2 pl-sm pr-sm text-center"><b>Sr. No.</b></div>
                                                <div class="col-md-4 pl-sm pr-sm text-right"><b>Amount (<?=CURRENCY_CODE?>)</b></div>
                                                <div class="col-md-6 pl-sm pr-sm"><b>Installment Date</b></div>
                                            </div>
                                        </div>
                                        <div id="installmentdivs">
                                            <?php if(!empty($installmentdata)){ 
                                                for($i=0; $i < count($installmentdata); $i++){ ?>
                                                    <input type="hidden" name="installmentid[]" value="<?=$installmentdata[$i]['id']?>">   
                                                        <div class="col-md-6">
                                                            <div class="col-md-2 text-center"><div class="form-group"><div class="col-sm-12"><?=($i+1)?></div></div></div>
                                                            <div class="col-md-4 text-center">
                                                                <div class="form-group">
                                                                    <div class="col-sm-12">
                                                                        <input type="text" id="installmentamount<?=($i+1)?>" value="<?=$installmentdata[$i]['amount']?>" name="installmentamount[]" class="form-control text-right installmentamount" div-id="<?=($i+1)?>" maxlength="5" onkeypress="return decimal(event,this.id);" readonly>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 text-center">
                                                                <div class="form-group">
                                                                    <div class="col-sm-12">
                                                                        <div class="input-group">
                                                                            <input type="text" id="installmentdate<?=($i+1)?>" value="<?=$this->general_model->displaydate($installmentdata[$i]['date'])?>" name="installmentdate[]" class="form-control installmentdate" div-id="<?=($i+1)?>" maxlength="5" readonly>
                                                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                <?php }?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="panel panel-default border-panel">
                                    <div class="panel-heading"><h2>Fasttag Details</h2></div>
                                    <div class="panel-body no-padding">
                                        <div class="row" id="fasttag">
                                            <div class="col-md-12">
                                                <div class="row m-n" id="fasttag_div" style="<?php /* if(!empty($installmentdata)){ echo "display: block;"; }else{ echo "display: none;"; } */ ?>">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <div class="form-group" id="accountno_div">
                                                                <div class="col-md-12 pl-xs pr-xs">
                                                                    <label for="accountno" class="control-label">Account Number </label>
                                                                    <input type="text" class="form-control" id="accountno" name="accountno" value="<?php if(!empty($vehicleFasttagdata)){ echo $vehicleFasttagdata['accountno']; } ?>" onkeypress="return isNumber(event)" maxlength="12">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group" id="wallerid_div">
                                                                <div class="col-md-12 pl-xs pr-xs">
                                                                    <label for="walletid" class="control-label">Wallet ID </label>
                                                                    <input type="text" class="form-control" id="walletid" name="walletid" value="<?php if(!empty($vehicleFasttagdata)){ echo $vehicleFasttagdata['walletid']; } ?>" onkeypress="return isNumber(event)" maxlength="14">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="form-group" id="rfidno_div">
                                                                <div class="col-md-12 pl-xs pr-xs">
                                                                    <label for="rfidno" class="control-label">RFID Number </label>
                                                                    <input type="text" class="form-control" id="rfidno" name="rfidno" value="<?php if(!empty($vehicleFasttagdata)){ echo $vehicleFasttagdata['rfidno']; } ?>" onkeypress="return isNumber(event)" maxlength="16">
                                                                </div>
                                                            </div>
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
                                    <div class="panel-heading"><h2>Actions</h2></div>
                                    <div class="panel-body no-padding">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="form-group text-center">
                                                <label for="focusedinput" class="col-md-5 col-xs-4 col-sm-5 control-label text-right">Activate</label>
                                                <div class="col-md-5 col-sm-7">
                                                    <div class="col-md-2 col-sm-2 col-xs-2" style="padding-left: 0px;">
                                                        <div class="radio">
                                                            <input type="radio" name="status" id="yes" value="1" <?php if(isset($vehicledata) && $vehicledata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
                                                            <label for="yes">Yes</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 col-sm-2 col-xs-2">
                                                        <div class="radio">
                                                            <input type="radio" name="status" id="no" value="0" <?php if(isset($vehicledata) && $vehicledata['status']==0){ echo 'checked'; }?>>
                                                            <label for="no">No</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="focusedinput" class="col-md-4 col-sm-3 col-xs-1 control-label"></label>
                                                <div class="col-md-7 col-xs-12 col-sm-9 p-n">
                                                    <?php if (!empty($vehicledata)) { ?>
                                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                    <?php } else { ?>
                                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                                    <?php } ?>
                                                    <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL.$this->session->userdata(base_url() . 'submenuurl')?>" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
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
    </div>
</div>