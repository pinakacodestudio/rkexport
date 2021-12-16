<script>
    var MACHINEID = '<?=(isset($machineid)?$machineid:0)?>';
</script>
<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body p-sm">
                            <div class="col-md-8">
                                <form class="form-horizontal" name="machine-form">
                                    <div class="form-group">
                                        <label class="col-md-2 pl-n pr-n control-label" for="machineid">Machine Name</label>
                                        <div class="col-md-6">
                                            <select id="machineid" name="machineid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                                <option value='0'>Select Machine</option>
                                                <?php foreach ($machinelist as $machine) { ?>
                                                    <option value='<?php echo $machine['id']; ?>' <?php if(isset($machineid) && $machineid==$machine['id']){ echo "selected"; } ?>><?=$machine['name']?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-4 text-right pt-xs pb-xs">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default" id="machinedetailpanel">
                        <div class="panel-body no-padding">
                            <div class="tab-container tab-default m-n">
                                <ul class="nav nav-tabs">
                                    <li class="dropdown pull-right tabdrop hide">
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                                    </li>
                                    <li class="active">
                                        <a id="firsttab" href="#machinedetailstab" data-toggle="tab" aria-expanded="false">Machine Details<div class="ripple-container"></div></a>
                                    </li>
                                    <li class="">
                                        <a href="#servicedetailstab" data-toggle="tab" aria-expanded="false">Service Details<div class="ripple-container"></div></a>
                                    </li>
                                </ul>
                                <div class="tab-content pb-n">
                                    <input type="hidden" id="machineid" name="machineid" value="">
                                    <div class="tab-pane active" id="machinedetailstab">
                                        <div class="row">
                                            <div class="col-md-12 p-n">
                                                <table class="table table-striped table-bordered">
                                                    <tbody id="machinedetail">
                                                        <tr>
                                                            <th>Company Name</th>
                                                            <td></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="servicedetailstab">
                                        <div class="row">
                                            <div class="panel panel-default mb-n">
                                                <div class="panel-heading">
                                                    <div class="col-md-6 p-n">
                                                        <div class="panel-ctrls panel-tbl"></div>
                                                    </div>
                                                    <div class="col-md-6 form-group" style="text-align: right;">
                                                        <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                                            <a class="<?=addbtn_class;?>" href="javascript:void(0)" onclick="openservicepopup()" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                                <div class="panel-body no-padding">
                                                    <table class="table table-striped table-bordered" id="servicestable">
                                                        <thead>
                                                            <tr>
                                                                <th>Sr. No.</th>
                                                                <th>Service By</th>
                                                                <th>Contact Name</th>
                                                                <th>Contact Mobile No.</th>
                                                                <th>Service Date</th>
                                                                <th>Service Due</th>
                                                                <th>Status</th>
                                                                <th>Reviewed By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="panel-footer"></div>
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
        <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceLabel">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                <h4 class="modal-title">Add Service</h4>
                </div>
                <div class="modal-body" style="float: left;width: 100%;padding:8px 16px;">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="machine-service-form">
                            <input type="hidden" name="machineservicedetailid" id="machineservicedetailid" value="">
                            <div class="row">
                                <div class="col-md-6 pl-xs pr-xs">
                                    <div class="form-group" id="serviceby_div">
                                        <div class="col-md-12">
                                            <label class="control-label" for="serviceby">Service By <span class="mandatoryfield">*</span></label>
                                            <input type="text" id="serviceby" class="form-control" name="serviceby">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 pl-xs pr-xs">
                                    <div class="form-group" id="contactname_div">
                                        <div class="col-md-12">
                                            <label class="control-label" for="contactname">Contact Name <span class="mandatoryfield">*</span></label>
                                            <input type="text" id="contactname" class="form-control" name="contactname">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-xs pr-xs">
                                    <div class="form-group" id="contactmobileno_div">
                                        <div class="col-md-12">
                                            <label class="control-label" for="contactmobileno">Contact Mobile No. <span class="mandatoryfield">*</span></label>
                                            <input type="text" id="contactmobileno" class="form-control" name="contactmobileno" onkeypress="return isNumber(event)" maxlength="10">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-xs pr-xs">
                                    <div class="form-group" id="servicedate_div">
                                        <div class="col-sm-12">
                                            <label for="servicedate" class="control-label">Service Date</label>
                                            <input id="servicedate" type="text" name="servicedate" class="form-control" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-xs pr-xs">
                                    <div class="form-group" id="servicedue_div">
                                        <div class="col-sm-12">
                                            <label for="servicedue" class="control-label">Service Due</label>
                                            <input id="servicedue" type="text" name="servicedue" class="form-control" readonly disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 pl-xs pr-xs">
                                    <div class="form-group" id="servicestatus_div">
                                        <div class="col-sm-12">
                                        <label for="focusedinput" class="control-label">Service  Status</label>
                                          <select id="status" name="status" class="selectpicker form-control" aria-expanded="false" data-size="5" tabindex="8">
                                                    <option value="0"   >Pending</option>
                                                    <option value="1"  >On Hold</option>
                                                    <option value="2"  >Done</option>
                                                    <option value="3"   >Cancel</option>
                                                </select>	
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-sm-12 text-center">
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <a class="<?=cancellink_class;?>" href="javascript:void(0)" data-dismiss="modal" aria-label="Close" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
            </div>
        </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<script type="text/javascript">
$('.list-group-item').on('click', function() {
    $('.list-group-item').removeClass('active');
    $(this).addClass('active');
});
</script>