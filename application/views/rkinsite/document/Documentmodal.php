<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title">Add Document</h4>
            </div>
            <div class="modal-body pt-sm">
                <form id="document-form" name="document-form" class="form-horizontal">
                    <!-- <input type="hidden" name="referencetype" id="referencetype"> -->
                    <input type="hidden" name="referenceid" id="referenceid">
                    <input type="hidden" name="documentid" id="documentid">
                
                    <div class="col-md-12">
                        <div class="form-group text-center" id="document_div">
                            <label for="focusedinput" class="col-sm-4 control-label text-right">Document</label>
                            <div class="col-md-5 col-sm-7">
                                <div class="col-md-4 col-sm-2 col-xs-2" style="padding-left: 0px;">
                                    <div class="radio">
                                        <input type="radio" name="referencetype" id="party" value="1">
                                        <label for="party">Party</label>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-2 col-xs-2">
                                    <div class="radio">
                                        <input type="radio" name="referencetype" id="vehicle" value="0">
                                        <label for="vehicle">Vehicle</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="newvehicleid_div">
                            <label for="newvehicleid" class="col-sm-4 control-label">Select Vehicle <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-6">
                                <select id="newvehicleid" name="newvehicleid" class="selectpicker form-control" data-live-search="true" data-size="8" title="Select Vehicle">
                                <option value=0>Select Vehicle</option>
                                    <?php foreach($vehicledata as $row){ ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['vehiclename']." (".$row['vehicleno'].")"; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="newpartyid_div">
                            <label for="newpartyid" class="col-sm-4 control-label">Select Party <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-6">
                                <select id="newpartyid" name="newpartyid" class="selectpicker form-control" data-live-search="true" data-size="8" title="Select Party">
                                    <option value=0>Select Party</option>
                                    <?php foreach($partydata as $row){ ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']." (".$row['partycode'].")"; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="documenttype_div">
                            <label for="documenttype" class="col-sm-4 control-label">Document Type <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-6">
                                <select id="documenttype" name="documenttype" class="selectpicker form-control" data-select-on-tab="true" data-live-search="true" data-size="5">
                                    <option value="0">All Document Type</option>
                                    <?php if(!empty($documenttypedata)){ 
                                        foreach($documenttypedata as $document){ ?>    
                                        <option value="<?=$document['id']?>"><?=$document['documenttype']?></option>
                                    <?php } 
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="documentnumber_div">
                            <label for="documentnumber" class="col-sm-4 control-label">Document Number <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-6">
                                <input id="documentnumber" name="documentnumber" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fromdate" class="col-sm-4 control-label">Register Date</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="fromdate" type="text" name="fromdate" value="" class="form-control" readonly>
                                    <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="duedate_div">
                            <label for="duedate" class="col-sm-4 control-label">Due Date</label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <input id="duedate" type="text" name="duedate" value="" class="form-control" readonly>
                                    <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="licencetype_div">
                            <label for="licencetype" class="col-sm-4 control-label">Licence Type</label>
                            <div class="col-sm-6">
                                <select id="licencetype" name="licencetype" class="selectpicker form-control" data-select-on-tab="true" data-live-search="true" data-size="5">
                                    <option value="0">Select Licence Type</option>
                                    <?php if(!empty($this->Licencetype)){ 
                                        foreach($this->Licencetype as $k=>$val){ ?>    
                                        <option value="<?=$k?>"><?=$val?></option>
                                    <?php } 
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" id="documentattachment_div">
                            <label for="documentattachment" class="col-sm-4 control-label">Attachment</label>
                            <div class="col-sm-6">
                                <input type="hidden" id="isvaliddocumentattachment" value="0">
                                <input type="hidden" name="olddocumentattachment" id="olddocumentattachment" value="">
                                <div class="input-group" id="fileupload">
                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                        <span class="btn btn-primary btn-raised btn-sm btn-file">Browse...
                                        <input type="file" name="documentattachment" id="documentattachment" class="documentattachment" onchange="validfile($(this),'documentattachment',this)" accept=".jpg,.jpeg,.gif,.png,.pdf,.bmp">
                                        </span>
                                    </span>
                                    <input type="text" id="documentattachmenttext" class="form-control" name="documentattachmenttext" value="" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-xs-3 control-label"></label>
                            <div class="col-md-6 col-xs-9">
                                <input type="button" value="Add" id="submitDocumentBtn" class="btn btn-primary btn-raised" onclick="checkdocumentvalidation()">
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
function openDocumentModal(referencetype,referenceid,documentid=""){

    //$("#referencetype").val(referencetype);
    //$("input[name='referencetype']").val(referencetype);
    $('#newvehicleid_div').hide();
    $('#newpartyid_div').hide();
    $('#document_div').hide();
    
    if(referencetype==0){
        $('#vehicle').prop('checked', true);
        if (referenceid==0) {
            $('#document_div').show();
            $('#newvehicleid_div').show();
            $('#newpartyid_div').hide();
        }
    }else if(referencetype==1){
        $('#party').prop('checked', true);
        if (referenceid==0) {
            $('#document_div').show();
            $('#newpartyid_div').show();
            $('#newvehicleid_div').hide();
        }
    }
    
    $("#referenceid").val(referenceid);
    $("#documentid").val(documentid);

    resetdocumentdata();
    if(documentid==""){
        $("#documentModal .modal-title").html("Add Document");
        $("#submitDocumentBtn").val("Add");
    }else{
        $("#documentModal .modal-title").html("Edit Document");
        $("#submitDocumentBtn").val("Update");

        var uurl = SITE_URL+"document/getDocumentByID";
        $.ajax({
        url: uurl,
        type: 'POST',
        data: {documentid:String(documentid)},
        // dataType: 'json',
        async: false,
        success: function(response){
            var JSONObject = JSON.parse(response);
            
            $('#documentnumber').val(JSONObject['documentnumber']);
            $('#documenttype').val(JSONObject['documenttypeid']).selectpicker('refresh');
            $('#licencetype').val(JSONObject['licencetype']).selectpicker('refresh');
            
            if(JSONObject['fromdate'] != "0000-00-00"){
                $('#fromdate').val(JSONObject['fromdate']);
            }
            if(JSONObject['duedate'] != "0000-00-00"){
                $('#duedate').val(JSONObject['duedate']);
            }
            if(JSONObject['documentfile'] != ""){
                $("#documentattachmenttext").val(JSONObject['documentfile']);
                $("#isvaliddocumentattachment").val('1');
                $("#olddocumentattachment").val(JSONObject['documentfile']);
            }else{
                $("#documentattachment").val("");
                $("#documentattachmenttext").val("");
                $("#isvaliddocumentattachment").val('0');
                $("#olddocumentattachment").val("");
            }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        });
    }
    $("#documentModal").modal("show");
}
$('input[type=radio][name=referencetype]').change(function() {
    changedocument(this.value);console.log(this.value);
});
function changedocument(referencetype){
    $("#newvehicleid_div").removeClass("has-error is-focused");
    $("#newpartyid_div").removeClass("has-error is-focused");
    if(referencetype==0){
        $('#newvehicleid_div').show();
        $('#newpartyid_div').hide();
    }else if(referencetype==1){
        $('#newpartyid_div').show();
        $('#newvehicleid_div').hide();
    }
}
function resetdocumentdata(){
  $("#documenttype_div").removeClass("has-error is-focused");
  $("#documentnumber_div").removeClass("has-error is-focused");
  $("#documentattachment_div").removeClass("has-error is-focused");
  $("#duedate_div").removeClass("has-error is-focused");

  $("#documenttype").val('0').selectpicker('refresh');
  $("#documentnumber").val('');
  $("#licencetype").val('0').selectpicker('refresh');
  $("#documentattachment").val("");
  $("#documentattachmenttext").val("");
  $("#isvaliddocumentattachment").val('0');
  $("#fromdate").val('');
  $("#duedate").val('');
}
function validfile(obj,element,elethis){
    var val = obj.val();
    var id = obj.attr('id').match(/\d+/);
    var filename = obj.val().replace(/C:\\fakepath\\/i, '');
    
    if (elethis.files[0].size <= UPLOAD_MAX_FILE_SIZE) {
   
    switch(val.substring(val.lastIndexOf('.') + 1).toLowerCase()){
        case 'pdf': case 'gif': case 'bmp': case 'jpg': case 'jpeg': case 'png':
            $("#"+element+"text").val(filename);
            $("#"+element+'_div').removeClass("has-error is-focused");
            $("#isvalid"+element).val('1');
            break;
        default:
            $("#"+element).val("");
            $("#"+element+"text").val("");
            $("#isvalid"+element).val('0');
            $("#"+element+'_div').addClass("has-error is-focused");
            
            new PNotify({title: 'Accept only Image and PDF Files !',styling: 'fontawesome',delay: '3000',type: 'error'});
        break;
    }
    }else{
        isvaliddocfile = 0;
    $("#" + element).val("");
    $("#"+element+"text").val("");
    $("#" + element + "_div").addClass("has-error is-focused");
    new PNotify({title: 'File is too large (max size ' + formatBytes(UPLOAD_MAX_FILE_SIZE) + ') !',styling: 'fontawesome',delay: '3000',type: 'error'});
    }
}
</script>