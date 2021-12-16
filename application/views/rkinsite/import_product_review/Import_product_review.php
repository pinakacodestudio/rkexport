<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">

        <div data-widget-group="group1">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                        <div class="panel-heading filter-panel border-filter-heading">
                            <h2>Import Product Review</h2>
                            <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                        </div>
                        <div class="panel-body panelcollapse pt-n" style="display: none;">
                            <form action="#" id="importproductreviewform" class="form-horizontal">
                                
                                <div class="col-md-4">
                                    <div class="form-group" id="importproductreviewfile_div">
                                        <div class="col-sm-12 pl-sm pr-sm">
                                            <label class="control-label" for="Filetext">Select Excel File <span class="mandatoryfield">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                                                    <span class="btn btn-primary btn-raised btn-file">Browse...
                                                        <input type="file" name="importproductreviewfile" id="importproductreviewfile" accept="xl,.xlc,.xls,.xlsx,.ods">
                                                    </span>
                                                </span>
                                                <input type="text" readonly="" id="Filetext" class="form-control" value="">
                                            </div>
                                        </div>
                                    </div>      
                                </div>                                        
                                <div class="col-md-1">      
                                    <div class="form-group mt-xxl">
                                        <div class="col-sm-12 pl-sm pr-sm">  
                                            <?php if (in_array("import-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>                                        
                                            <button type="button" onclick="checkvalidation()" id="search_btn" class="btn btn-primary btn-raised ">Import</button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group mt-xxl">
                                        <div class="col-sm-12 pl-sm">                                                    
                                            <a href="<?php echo IMPORT_FILE; ?>import-product-review.xls"
                                            class="btn btn-default btn-raised" download="import-product-review.xls"><i
                                            class="fa fa-download"></i> Download Sample Product Review File</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default border-panel">
                        <div class="panel-body no-padding pt-1">
                            <div class="tab-container tab-default m-n">
                                <!-- <ul class="nav nav-tabs">
                                    <li class="dropdown pull-right tabdrop hide">
                                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-angle-down"></i> </a><ul class="dropdown-menu"></ul>
                                    </li>
                                    <li class="active">
                                        <a id="firsttab" href="#inquiry" data-toggle="tab" aria-expanded="false">Imported Excel Files<div class="ripple-container"></div></a>
                                    </li>
                                    <li class="">
                                        <a href="#facebook" data-toggle="tab" aria-expanded="false">Imported Facebook Excel Files<div class="ripple-container"></div></a>
                                    </li>
                                </ul> -->
                                <div class="tab-content pb-n">
                                    <div class="tab-pane active" id="inquiry">
                                        <table id="inquirytbl" class="table table-striped table-bordered table-responsive-sm"
                                            cellspacing="0" width="100%">
                                            <thead>
                                                <tr>
                                                    <th class="width8">Sr. No.</th>
                                                    <th>Employee</th>
                                                    <th>File</th>
                                                    <th>IP Address</th>
                                                    <th>Total Entries</th>
                                                    <th>Imported Entries</th>
                                                    <th>Failed</th>
                                                    <th>Info</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php $i=0;
                                                foreach($importproductreview as $lead){ ?>
                                                    <tr>
                                                        <td class="width8"><?php echo ++$i;?></td>
                                                        <td><?php echo ucwords($lead['name']);?></td>
                                                        <td><?php echo '<a href="'.UPLOADED_IMPORT_EXCEL_FILE.$lead['file'].'" download="'.$lead['file'].'" class="a-without-link">'.$lead['file'].'</a>';?></td> 
                                                        <td><?php echo $lead['ipaddress'];?></td>                                    
                                                        <td><?php echo $lead['totalrow'];?></td>
                                                        <td><?php echo $lead['totalinserted'];?></td>
                                                        <td><?php echo $lead['totalrow']-$lead['totalinserted'];?></td>
                                                        <td><?php echo $lead['info'];?></td>
                                                        <td><?php echo $this->general_model->displaydate($lead['createddate']);?></td>                                    
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
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