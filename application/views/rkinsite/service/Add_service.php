<?php
    $SearchPartsUrl=base_url().ADMINFOLDER.'service/searchServiceParts';
?>
<script>
    var SearchPartsUrl = '<?=$SearchPartsUrl?>';
</script>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if (isset($servicedata)) {echo 'Edit';} else {echo 'Add';} ?>
            <?= $this->session->userdata(base_url() . 'submenuname') ?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?= $this->session->userdata(base_url() . 'mainmenuname') ?></a></li>
                <li><a href="<?php echo ADMIN_URL . $this->session->userdata(base_url() . 'submenuurl') ?>"><?= $this->session->userdata(base_url() . 'submenuname') ?></a>
                </li>
                <li class="active"><?php if (isset($servicedata)) {echo 'Edit';} else {echo 'Add';} ?>
                    <?= $this->session->userdata(base_url() . 'submenuname') ?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
        <div data-widget-group="group1">
            <form class="form-horizontal" id="form-service">
                <input type="hidden" name="serviceid" id="serviceid" value="<?php if (isset($servicedata)) {echo $servicedata['id'];} ?>">
                <div class="panel panel-default border-panel">
                    <div class="panel-body pt-sm">
                        <div class="row">
                            <div class="col-md-12 pl-sm pr sm">
                                <div class="col-md-4">
                                    <div class="form-group" id="vehicleid_div">
                                        <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                            <label for="vehicleid" class="control-label">Select Vehicle <span class="mandatoryfield">*</span></label>
                                            <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                <option value="0">Select Vehicle</option>
                                                <?php foreach ($vehicledata as $vd) { ?>
                                                    <option value="<?php echo $vd['id']; ?>" <?php if (isset($servicedata) && $servicedata['vehicleid'] == $vd['id']) {echo "selected";} ?>><?php echo $vd['vehiclename'] . " (" . $vd['vehicleno'] . ")"; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="driverid_div">
                                        <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                            <label for="driverid" class="control-label">Service By <span class="mandatoryfield">*</span></label>
                                            <select id="driverid" name="driverid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                <option value="0">Select Driver</option>
                                                <?php foreach ($driverdata as $driver) { ?>
                                                    <option value="<?php echo $driver['id']; ?>" <?php if (isset($servicedata) && $servicedata['driverid'] == $driver['id']) {echo "selected";} ?>><?php echo $driver['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="servicetypeid_div">
                                        <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                            <label for="servicetypeid" class="control-label">Service type <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-10 col-sm-10 col-xs-11 pl-n pr-n">
                                                <select id="servicetypeid" name="servicetypeid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                    <option value="0">All Service Type</option>
                                                    <?php foreach ($servicetypedata as $servicetype) { ?>
                                                        <option value="<?php echo $servicetype['id']; ?>" <?php if (isset($servicedata) && $servicedata['servicetypeid'] == $servicetype['id']) {echo "selected";} ?>><?php echo $servicetype['name']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-2 col-xs-1 col-sm-2 text-right pl-n pr-n">
                                                <a href="<?=ADMIN_URL.'service-type/add-service-type'?>" class="btn btn-primary btn-raised" target="_blank" title="Add Service Type" style="padding: 5px 10px;margin-top: 8px;"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="col-md-12 pl-sm pr sm">
                                <div class="col-md-4">
                                    <div class="form-group" id="garageid_div">
                                        <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                            <label for="garageid" class="control-label">Select Garage <span class="mandatoryfield">*</span></label>
                                            <select id="garageid" name="garageid" class="selectpicker form-control" data-live-search="true" data-size="5">
                                                <option value="0">Select Garage</option>
                                                <?php foreach ($garagedata as $garage) { ?>
                                                    <option value="<?php echo $garage['id']; ?>" <?php if (isset($servicedata) && $servicedata['garageid'] == $garage['id']) {echo "selected";} ?>><?php echo $garage['name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div> 
                                <div class="col-md-4">
                                    <div class="form-group" id="servicedate_div">
                                        <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                            <label for="servicedate" class="control-label">Date <span class="mandatoryfield">*</span></label>
                                            <div class="input-group">
                                                <input type="text" id="servicedate" name="servicedate" class="form-control" value="<?php if (isset($servicedata)) { if($servicedata['date']!="0000-00-00"){ echo $this->general_model->displaydate($servicedata['date']); } }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" readonly>
                                                    <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" id="remarks_div">
                                        <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                            <label for="remarks" class="control-label">Remarks</label>
                                            <textarea class="form-control" id="remarks" name="remarks"><?php if (isset($servicedata)) { echo $servicedata['remarks']; } ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            
                        </div>
                        <div class="row">
                           
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading"><h2>Service Part Details</h2></div>
                            <div class="panel-body no-padding">
                                <div id="commonpanel">
                                    <?php if (isset($servicedata) && !empty($servicepartdata)) { ?>
                                        <script type="text/javascript">
                                            var rowcount = '<?= count($servicepartdata) ?>';
                                        </script>
                                        <?php for ($i = 0; $i < count($servicepartdata); $i++) { ?>
                                            <input type="hidden" id="partid<?=$i+1?>" name="partid[]" value="<?php echo $servicepartdata[$i]['id']; ?>">
                                            <div class="col-md-12 pl-sm pr-sm servicerow" id="row<?=$i+1?>">
                                                <div class="col-md-3 col-xs-12 col-sm-6">
                                                    <div class="form-group" id="partname_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Parts Name <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="partname<?=$i+1?>" data-provide='partname' data-url="<?=$SearchPartsUrl?>" name="partname[]" value="<?php echo $servicepartdata[$i]['partname']; ?>" class="form-control partname">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="serialno_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Serial No. <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="serialno<?=$i+1?>" name="serialno[]" value="<?php echo $servicepartdata[$i]['serialnumber']; ?>" class="form-control serialno">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="warrantydate_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Warranty End Date</label>
                                                            <div class="input-group">
                                                                <input type="text" id="warrantydate<?=$i+1?>" name="warrantydate[]" value="<?php echo ($servicepartdata[$i]['warrantyenddate']!="0000-00-00")?$this->general_model->displaydate($servicepartdata[$i]['warrantyenddate']):""; ?>" class="form-control warrantydate" readonly>
                                                                <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="duedate_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Due Date</label>
                                                            <div class="input-group">
                                                                <input type="text" id="duedate<?=$i+1?>" class="form-control duedate" name="duedate[]" value="<?php echo ($servicepartdata[$i]['duedate']!="0000-00-00")?$this->general_model->displaydate($servicepartdata[$i]['duedate']):""; ?>" readonly>
                                                                <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="price_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Price (<?= CURRENCY_CODE ?>) <span class="mandatoryfield">*</span></label>
                                                            <input type="text" id="price<?=$i+1?>" onkeypress="return decimal_number_validation(event, this.value, 8)" class="form-control text-right price" value="<?php echo $servicepartdata[$i]['price']; ?>" name="price[]">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="tax_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Tax (%)</label>
                                                            <input type="text" id="tax<?=$i+1?>" name="tax[]" onkeypress="return decimal_number_validation(event, this.value, 3)" value="<?php echo $servicepartdata[$i]['tax']; ?>" class="form-control text-right tax">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="totalprice_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                            <label class="control-label">Amount (<?= CURRENCY_CODE ?>)</label>
                                                            <input type="text" id="totalprice<?=$i+1?>" class="form-control text-right totalprice" name="totalprice[]" readonly>
                                                            <input type="hidden" id="inputtaxamount<?=$i+1?>" class="inputtaxamount" name="inputtaxamount[]">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="currentkmhr_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Current Km / Hr</label>
                                                            <input type="text" id="currentkmhr<?=$i+1?>" class="form-control currentkmhr" onkeypress="return decimal_number_validation(event, this.value, 8)" value="<?php if($servicepartdata[$i]['currentkmhr']!='0.00') { echo $servicepartdata[$i]['currentkmhr'];} ?>" name="currentkmhr[]">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="changeafter_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Change After</label>
                                                            <input type="text" id="changeafter<?=$i+1?>" class="form-control changeafter" onkeypress="return decimal_number_validation(event, this.value, 8)" value="<?php if($servicepartdata[$i]['changeafter']!='0.00'){ echo $servicepartdata[$i]['changeafter']; } ?>" name="changeafter[]">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-6 col-sm-6">
                                                    <div class="form-group" id="alertkmhr_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Alert Km / Hr</label>
                                                            <input type="text" id="alertkmhr<?=$i+1?>" class="form-control" name="alertkmhr[]" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 col-xs-6 col-sm-6 p-n pt-xl">
                                                    <div class="form-group" id="setalert_div_<?=$i+1?>">
                                                        <div class="col-md-12 pr-xs">
                                                            <div class="checkbox">
                                                                <input id="setalert<?=$i+1?>" type="checkbox" value="1" name="setalert[]" class="checkradios" <?php if ((isset($servicepartdata) && $servicepartdata[$i]['setalert'] == 1) || !isset($servicepartdata)) { echo 'checked'; } ?>>
                                                                <label for="setalert<?=$i+1?>">Set Alert</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 col-xs-12 col-sm-12 mt-xl addrowbutton">
                                                    <div class="form-group" id="button_div">
                                                        <div class="col-md-12">
                                                            <?php if($i==0){?>
                                                                <?php if(count($servicepartdata)>1){ ?>
                                                                    <button type="button" class="btn btn-danger btn-raised remove_btn" onclick="removerow(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                                <?php }else { ?>
                                                                    <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addnewrow()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                                <?php } ?>
                                                            <?php }else if($i!=0) { ?>
                                                                <button type="button" class="btn btn-danger btn-raised remove_btn" onclick="removerow(<?=$i+1?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                            <?php } ?>
                                                            <button type="button" class="btn btn-danger btn-raised btn-sm remove_btn" onclick="removerow(<?=$i+1?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                            <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addnewrow()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>  
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pr-xs pl-xs"><hr></div>
                                                <script>
                                                $(document).ready(function () {
                                                    calculateprice(<?=$i+1?>);
                                                });
                                                </script>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <script> var rowcount = 1;</script>
                                        <div class="col-md-12 col-xs-12 col-sm-12 pl-sm pr-sm servicerow" id="row1">
                                            <input type="hidden" id="partid1" name="partid[]" value="">
                                            <div class="col-md-3 col-sm-12 col-xs-12">
                                                <div class="form-group" id="partname_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Parts Name <span class="mandatoryfield">*</span></label>
                                                        <input type="text" id="partname1" data-provide='partname' data-url="<?=$SearchPartsUrl?>" name="partname[]" class="form-control partname">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6 col-xs-6">
                                                <div class="form-group" id="serialno_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Serial No. <span class="mandatoryfield">*</span></label>
                                                        <input type="text" id="serialno1" name="serialno[]" class="form-control serialno">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6 col-xs-6">
                                                <div class="form-group" id="warrantydate_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Warranty End Date</label>
                                                        <div class="input-group">
                                                            <input type="text" id="warrantydate1" name="warrantydate[]" class="form-control warrantydate" readonly>
                                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-6 col-xs-6">
                                                <div class="form-group" id="duedate_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Due Date</label>
                                                        <div class="input-group">
                                                            <input type="text" id="duedate1" class="form-control duedate" name="duedate[]" readonly>
                                                            <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-6 col-xs-6">
                                                <div class="form-group" id="price_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Price (<?= CURRENCY_CODE ?>) <span class="mandatoryfield">*</span></label>
                                                        <input type="text" id="price1" onkeypress="return decimal_number_validation(event, this.value, 8)" class="form-control text-right price" name="price[]">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-6 col-xs-6">
                                                <div class="form-group" id="tax_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Tax (%)</label>
                                                        <input type="text" id="tax1" name="tax[]" onkeypress="return decimal_number_validation(event, this.value, 3)" class="form-control text-right tax">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-sm-6 col-xs-6">
                                                <div class="form-group" id="totalprice_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                        <label class="control-label">Amount (<?= CURRENCY_CODE ?>)</label>
                                                        <input type="text" id="totalprice1" class="form-control text-right totalprice" name="totalprice[]" readonly>
                                                        <input type="hidden" id="inputtaxamount1" class="inputtaxamount" name="inputtaxamount[]">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-xs-6">
                                                <div class="form-group" id="currentkmhr_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Current Km / Hr</label>
                                                        <input type="text" id="currentkmhr1" class="form-control currentkmhr" onkeypress="return decimal_number_validation(event, this.value, 8)" name="currentkmhr[]">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-xs-6">
                                                <div class="form-group" id="changeafter_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Change After (Km / Hr)</label>
                                                        <input type="text" id="changeafter1" class="form-control changeafter" onkeypress="return decimal_number_validation(event, this.value, 8)" name="changeafter[]">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-xs-6">
                                                <div class="form-group" id="alertkmhr_div_1">
                                                    <div class="col-md-12 pr-xs pl-xs">
                                                    <label class="control-label">Alert Km / Hr</label>
                                                        <input type="text" id="alertkmhr1" class="form-control" name="alertkmhr[]" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-1 col-xs-6 col-sm-6 p-n pt-xl">
                                                <div class="form-group" id="setalert_div_1">
                                                    <div class="col-md-12 pr-xs">
                                                        <div class="checkbox">
                                                            <input id="setalert1" type="checkbox" value="1" name="setalert[]" class="checkradios" checked>
                                                            <label for="setalert1">Set Alert</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-sm-12 col-xs-6 mt-xl addrowbutton">
                                                <div class="form-group" id="button_div">
                                                    <div class="col-md-12">
                                                        <button type="button" class="btn btn-danger btn-raised btn-sm remove_btn" onclick="removerow(1)" style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                                        <button type="button" class="btn btn-primary btn-raised add_btn" onclick="addnewrow()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12 pr-xs pl-xs"><hr></div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                <div class="col-md-12 totalservicepartsamountcount">
                                    <div class="col-md-7"></div>
                                    <input type="hidden" id="totalpriceamount" name="totalpriceamount">
                                    <input type="hidden" id="totaltaxamount" name="totaltaxamount">

                                    <div class="col-md-3 text-right control-label"><b>Total Price (<?= CURRENCY_CODE ?>)</div>
                                    <span class="col-md-1 text-right" id="totalprice">0.00</span></b>
                                </div>
                                <div class="col-md-12 p-xs"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-default border-panel">
                            <div class="panel-heading"><h2>Attachments</h2></div>
                            <div class="panel-body no-padding">
                                <div class="row" id="serviceattachment">
                                    <div class="col-md-12 p-n">
                                        <div class="col-md-6">
                                            <div class="col-md-12 visible-md visible-lg" id="serviceattachmentHeadings1">
                                                <div class="form-group pb-n">
                                                    <div class="col-md-5 pl-sm">
                                                        <label class="control-label">Title</label>
                                                    </div>
                                                    <div class="col-md-4 pl-sm">
                                                        <label class="control-label">Attachment</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="col-md-12" id="serviceattachmentHeadings2">
                                                <div class="form-group pb-n">
                                                    <div class="col-md-5 pl-sm">
                                                        <label class="control-label">Title</label>
                                                    </div>
                                                    <div class="col-md-4 pl-sm">
                                                        <label class="control-label">Attachment</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (isset($servicedata) && !empty($servicefiledata)) { ?>
                                        <script type="text/javascript">
                                            var filecount = '<?= count($servicefiledata) ?>';
                                            if(filecount>1){
                                                $("#serviceattachmentHeadings2").show();
                                            }
                                        </script>
                                        <?php for ($i = 0; $i < count($servicefiledata); $i++) { ?>
                                            <input type="hidden" name="documentid[<?=$i+1?>]" value="<?= $servicefiledata[$i]['id'] ?>" id="documentid<?=$i+1?>">
                                            <input type="hidden" name="olddocfile[<?=$i+1?>]" id="olddocfile<?=$i+1?>" value="<?php echo $servicefiledata[$i]['file']; ?>">
                                            <div class="col-md-6 servicefile" id="serivcefilecount<?=$i+1?>">
                                                <div class="col-md-5">
                                                    <div class="col-md-12 pl-sm pr-sm">
                                                        <div class="form-group">
                                                            <input type="text" id="filetitle<?=$i+1?>" name="filetitle[<?=$i+1?>]" value="<?php echo $servicefiledata[$i]['title']; ?>" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="col-md-12 pl-sm pr-sm">
                                                        <div class="form-group" id="servicefile<?=$i+1?>_div">
                                                            <div class="input-group">
                                                                <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">
                                                                    <span class="btn btn-primary btn-raised btn-file">Browse...
                                                                        <input type="file" value="<?php echo $servicefiledata[$i]['file']; ?>" name="servicefile<?=$i+1?>" id="servicefile<?=$i+1?>" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validservicefile($(this),'servicefile<?=$i+1?>',this)">
                                                                    </span>
                                                                </span>
                                                                <input type="text" readonly="" id="Filetext<?=$i+1?>" name="Filetext[]" value="<?php echo $servicefiledata[$i]['file']; ?>" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-n pr-n addrowbutton">
                                                <?php if($i==0){?>
                                                    <?php if(count($servicefiledata)>1){ ?>
                                                        <button type="button" class="btn btn-danger btn-raised btn-sm file_remove_btn" onclick="removeservicefile(1)" style="padding: 5px 10px;margin-top:14px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-primary btn-raised file_add_btn" onclick="addnewservicefile()" style="padding: 5px 10px;margin-top:14px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>
                                                <?php }else if($i!=0) { ?>
                                                    <button type="button" class="btn btn-danger btn-raised btn-sm file_remove_btn" onclick="removeservicefile(<?=$i+1?>)" style="padding: 5px 10px;margin-top:14px;"><i class="fa fa-minus"></i></button>
                                                <?php } ?>
                                                    <button type="button" class="btn btn-danger btn-raised btn-sm file_remove_btn" onclick="removeservicefile(<?=$i+1?>)" style="padding: 5px 10px;margin-top:14px;display:none;"><i class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-primary btn-raised file_add_btn" onclick="addnewservicefile()" style="padding: 5px 10px;margin-top:14px;"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                    <?php }
                                    } else { ?>
                                        <script type="text/javascript">
                                            var filecount = 1;
                                        </script>

                                        <div class="col-md-6 servicefile" id="serivcefilecount1">
                                            <div class="col-md-5">
                                                <div class="col-md-12 pl-sm pr-sm">
                                                    <div class="form-group">
                                                        <input type="text" id="filetitle1" placeholder="Enter File Title" name="filetitle[1]" class="form-control servicedocrow">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="col-md-12 pl-sm pr-sm">
                                                    <div class="form-group" id="servicefile1_div">
                                                        <div class="input-group">
                                                            <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">
                                                                <span class="btn btn-primary btn-raised btn-file">Browse...
                                                                    <input type="file" name="servicefile1" id="servicefile1" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validservicefile($(this),'servicefile1',this)">
                                                                </span>
                                                            </span>
                                                            <input type="text" readonly="" id="Filetext1" placeholder="Enter File" name="Filetext[]" value="" class="form-control servicedocrow">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 pl-n pr-n addrowbutton">
                                                <button type="button" class="btn btn-danger btn-raised btn-sm file_remove_btn" onclick="removeservicefile(1)" style="padding: 5px 10px;margin-top:14px;display:none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-primary btn-raised file_add_btn" onclick="addnewservicefile()" style="padding: 5px 10px;margin-top:14px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                    <?php } ?>
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
                                <div class="row">
                                    <div class="form-group text-center">
                                        
                                        <div class="col-md-12 col-xs-11 col-sm-9">
                                            <?php if (!empty($servicedata)) { ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE" class="btn btn-primary btn-raised">
                                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="SAVE & NEW" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php } else { ?>
                                                <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                                <input type="button" id="submit" onclick="checkvalidation(1)" name="submit" value="ADD & NEW" class="btn btn-primary btn-raised">
                                                <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                            <?php } ?>
                                            <a class="<?= cancellink_class; ?>" href="<?= ADMIN_URL ?>service" title=<?= cancellink_title ?>><?= cancellink_text ?></a>
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