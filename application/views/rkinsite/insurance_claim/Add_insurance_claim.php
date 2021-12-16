<script type="text/javascript">
  var insurancecompany = '<?php if(isset($insuranceclaimdata)){ echo $insuranceclaimdata['companyname']; }else{ echo ""; } ?>';
  var policyno = '<?php if(isset($insuranceclaimdata)){ echo $insuranceclaimdata['insuranceid']; }else{ echo 0; } ?>';

  var agent = '<?php if(isset($insuranceclaimdata)){ echo $insuranceclaimdata['insuranceagentid']; }else{ echo 0; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">
        <h1><?php if(isset($insuranceclaimdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
                <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
                <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a>
                </li>
                <li class="active"><?php if(isset($insuranceclaimdata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body pt-sm">
                            <form class="form-horizontal" id="form-insuranceclaim">
                                <input type="hidden" name="insuranceclaimid" id="insuranceclaimid" value="<?php if(isset($insuranceclaimdata)){ echo $insuranceclaimdata['id']; } ?>">
                                <div class="row">
                                    <div class="col-md-12 pl-sm pr-sm">
                                        <div class="col-md-3">
                                            <div class="form-group" id="vehicle_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="vehicleid" class="control-label">Vehicle <span class="mandatoryfield">*</span></label>
                                                    <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-live-search="true">
                                                        <option value="0">Select Vehicle</option>
                                                        <?php if(!empty($vehicledata)){
                                                            foreach($vehicledata as $vehicle){ ?>
                                                                <option value="<?php echo $vehicle['id']; ?>" <?php if(isset($insuranceclaimdata) && $insuranceclaimdata['vehicleid']==$vehicle['id']){echo "selected"; }?> ><?php echo $vehicle['vehiclename']." (".$vehicle['vehicleno'].")"; ?></option>
                                                        <?php } 
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="insurancecompany_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="insurancecompany" class="control-label">Insurance Company <span class="mandatoryfield">*</span></label>
                                                    <select id="insurancecompany" name="insurancecompany" class="selectpicker form-control insurancecompany" show-data-subtext="on" data-live-search="true" data-size="8">
                                                        <option value="">Select Insurance Company</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="policynumber_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="policynumber" class="control-label">Policy Number<span class="mandatoryfield"> *</span></label>
                                                    <select id="policynumber" name="policynumber" class="selectpicker form-control"
                                                        data-live-search="true">
                                                        <option value="0">Select Policy Number
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="agentname_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="agentname" class="control-label">Agent Name <span class="mandatoryfield">*</span></label>
                                                    <select id="agentname" name="agentname" class="selectpicker form-control"
                                                        data-live-search="true">
                                                        <option value="0">Select Insurance Agent</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pl-sm pr-sm">
                                        
                                        <div class="col-md-3">
                                            <div class="form-group" id="billnumber_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="billnumber" class="control-label">Bill Number <span class="mandatoryfield">*</span></label>
                                                    <input id="billnumber" type="text" name="billnumber"
                                                        class="form-control" value="<?php if(isset($insuranceclaimdata)){ echo $insuranceclaimdata['billnumber']; } ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="amount_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="amount" class="control-label">Amount <span class="mandatoryfield">*</span></label>
                                                    <input id="amount" type="text" name="amount" class="form-control" value="<?php if(isset($insuranceclaimdata)){ echo number_format($insuranceclaimdata['billamount'],2,'.',''); } ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="claimnumber_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="claimnumber" class="control-label">Claim Number <span class="mandatoryfield">*</span></label>
                                                    <input id="claimnumber" type="text" name="claimnumber"
                                                        class="form-control" value="<?php if(isset($insuranceclaimdata)){ echo $insuranceclaimdata['claimnumber']; } ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="claimamount_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="claimamount" class="control-label">ClaimAmount <span class="mandatoryfield">*</span></label>
                                                    <input id="claimamount" type="text" name="claimamount"
                                                        class="form-control" value="<?php if(isset($insuranceclaimdata)){ echo number_format($insuranceclaimdata['claimamount'],2,'.',''); } ?>" onkeypress="return decimal_number_validation(event, this.value, 8)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 pl-sm pr-sm">
                                        <div class="col-md-3">
                                            <div class="form-group" id="insurancedate_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="insurancedate" class="control-label">Date <span class="mandatoryfield">*</span></label>
                                                    <div class="input-group">
                                                        <input id="insurancedate" type="text" name="insurancedate" class="form-control"  value="<?php if(isset($insuranceclaimdata) && $insuranceclaimdata['insuranceclaimdate']!="0000-00-00"){ echo $this->general_model->displaydate($insuranceclaimdata['insuranceclaimdate']); } ?>" readonly>
                                                        <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" id="status_div">
                                                <div class="col-md-12 col-sm-6 pl-xs pr-xs">
                                                    <label for="status" class="control-label">Status</label>
                                                    <select id="status" name="status" class="selectpicker form-control" data-live-search="true">
                                                        <option value="0" <?php if(isset($insuranceclaimdata) && $insuranceclaimdata['status']==0){ echo "selected"; } ?>>Pending</option>
                                                        <option value="1" <?php if(isset($insuranceclaimdata) && $insuranceclaimdata['status']==1){ echo "selected"; } ?>>Approve</option>
                                                        <option value="2" <?php if(isset($insuranceclaimdata) && $insuranceclaimdata['status']==2){ echo "selected"; } ?>>Reject</option>
                                                        <option value="3" <?php if(isset($insuranceclaimdata) && $insuranceclaimdata['status']==3){ echo "selected"; } ?>>Total Lost</option>
                                                        <option value="4" <?php if(isset($insuranceclaimdata) && $insuranceclaimdata['status']==4){ echo "selected"; } ?>>Cancle</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row"><hr></div>
                                <div class="row">
                                    <div class="col-md-3 text-left">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label">Attachments</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    <!-- <?php var_dump($insuranceclaimfiledata); ?> -->
                                <div class="row" id="attachmentfiledata">
                                <?php if (isset($insuranceclaimdata) && !empty($insuranceclaimfiledata) && isset($insuranceclaimfiledata)) { ?>
                                    <script type="text/javascript">
                                        var attachmentcount = '<?= count($insuranceclaimfiledata) ?>';
                                    </script>
                                    <?php for ($i = 0; $i < count($insuranceclaimfiledata); $i++) {  ?>
                                        <input type="hidden" name="documentid[<?=$i+1?>]" value="<?=$insuranceclaimfiledata[$i]['id']?>" id="documentid<?=$i+1?>">
                                        <input type="hidden" name="olddocfile[<?=$i+1?>]" id="olddocfile<?=$i+1?>" value="<?php echo $insuranceclaimfiledata[$i]['file']; ?>"> 
                                        <div class="col-md-4 col-xs-12 attachment" id="attachmentcount<?=$i+1?>">
                                            <div class="form-group">
                                                <div class="col-md-9 col-xs-9">
                                                    <div class="input-group">
                                                        <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">
                                                            <span class="btn btn-primary btn-raised btn-file">Browse...
                                                                <input type="file" class="attachment" name="attachment<?=$i+1?>" id="attachment<?=$i+1?>" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf" onchange="validfile($(this),this)">
                                                            </span>
                                                        </span>
                                                        <input type="text" readonly="" id="Filetext<?=$i+1?>" name="Filetext[]" value="<?= $insuranceclaimfiledata[$i]['file'] ?>" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-xs-3 pl-n pr-n mt-sm">
                                                    <button type="button" class="btn btn-danger btn-raised remove_btn" id="p1" onclick="removeattachment(<?=$i+1?>)" style="padding: 5px 10px;">
                                                        <i class="fa fa-minus"></i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-raised add_btn" id="p1" onclick="addnewattachment()" style="padding: 5px 10px;">
                                                        <i class="fa fa-plus"></i>
                                                        <div class="ripple-container"></div>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <script type="text/javascript">
                                        var attachmentcount = 1;
                                    </script>
                                    <div class="col-md-4 col-sm-6 col-xs-12 attachment" id="attachmentcount1">
                                        <div class="form-group">
                                            <div class="col-md-9 col-xs-9">
                                                <div class="input-group">
                                                    <span class="input-group-btn" style="padding: 0 0px 0px 0px; ">
                                                        <span class="btn btn-primary btn-raised btn-file">Browse...
                                                            <input type="file" class="attachment" name="attachment1" id="attachment1" accept=".jpe,.jpeg,.jpg,.pbm,.png,.pdf,.doc,.docx" onchange="validfile($(this),this)">
                                                        </span>
                                                    </span>
                                                    <input type="text" readonly="" id="Filetext1" name="Filetext[]" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-xs-3 pl-n pr-n mt-sm">
                                                <button type="button" class="btn btn-danger btn-raised remove_btn" id="p1" onclick="removeattachment(1)" style="padding: 5px 10px;display: none;">
                                                    <i class="fa fa-minus"></i>
                                                    <div class="ripple-container"></div>
                                                </button>
                                                <button type="button" class="btn btn-primary btn-raised add_btn" id="p1" onclick="addnewattachment()" style="padding: 5px 10px;">
                                                    <i class="fa fa-plus"></i>
                                                    <div class="ripple-container"></div>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                </div>
                                <div class="form-group">
                                    <label for="focusedinput" class="col-md-4 col-xs-1 control-label"></label>
                                    <div class="col-md-8 col-xs-11">
                                        <?php if(!empty($insuranceclaimdata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="SAVE"
                                            class="btn btn-primary btn-raised">
                                            <input type="button" id="submit" onclick="checkvalidation(1)" name="submit"
                                            value="SAVE & NEW" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                        <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD"
                                            class="btn btn-primary btn-raised">
                                        <input type="button" id="submit" onclick="checkvalidation(1)" name="submit"
                                            value="ADD & NEW" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised">
                                        <?php } ?>
                                        <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>insurance-claim"
                                            title=<?=cancellink_title?>><?=cancellink_text?></a>
                                    </div>
                                </div>
                            </form>        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
