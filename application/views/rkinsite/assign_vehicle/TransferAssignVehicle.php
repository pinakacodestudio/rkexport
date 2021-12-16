<div class="modal fade" id="AssignVehicleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title">Transfer Assign Vehicle</h4>
            </div>
            <div class="modal-body pt-sm">
                <form id="assign-vehicle-form" name="assign-vehicle-form" class="form-horizontal">
                <input type="hidden" name="vehicleid" id="vehicleid">
                    <div class="col-md-12">
                        <div class="form-group" id="site_div">
                            <label for="siteid" class="col-sm-4 control-label">Select Site <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-6">
                                <select id="siteid" name="siteid" class="selectpicker form-control" data-size="5" data-live-search="true">
                                    <option value="0">Select Site</option>
                                    <?php foreach ($sitedata as $sd) { ?>
                                    <option value="<?php echo $sd['id']; ?>"><?php echo $sd['sitename']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="date_div">
                            <label for="assignvehicledate" class="col-md-4 control-label">Date <span class="mandatoryfield">*</span></label>
                            <div class="col-md-4">
                                <div class="input-group">
                                <input id="assignvehicledate" type="text" name="assignvehicledate" class="form-control"readonly>
                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                    </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-3 control-label"></label>
                            <div class="col-md-6 col-xs-9">
                                <input type="button" value="Add" id="AssignVehicleBtn" class="btn btn-primary btn-raised" onclick="checkvalidation()">
                                <button class="btn btn-danger btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer"></div>
        </div>
    </div>
</div>
<script>
function AssignVehicleModal(vehicleid=""){

    $("#vehicleid").val(vehicleid);

    resetdata();
    if(vehicleid!=""){
        $("#AssignVehicleModal .modal-title").html("Transfer Assign Vehicle");
        $("#AssignVehicleBtn").val("Transfer");
    }
    $("#AssignVehicleModal").modal("show");
}
function resetdata(){
    $("#siteid").val(0);
    $("#assignvehicledate").val('');
    $('.selectpicker').selectpicker('refresh');
}
</script>