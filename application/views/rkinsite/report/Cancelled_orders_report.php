<?php $arrSessionDetails = $this->session->userdata;?>
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
                        <h2><?=APPLY_FILTER?></h2>
                        <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                    </div>
                    <div class="panel-body panelcollapse pt-n" style="display: none;">
                        <form action="#" id="cancelledordersreportform" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php /*
                                    <div class="col-md-3 pl-n pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="countryid" class="control-label">Country</label>
                                                <select id="countryid" name="countryid[]" class="selectpicker form-control" multiple data-actions-box="true" title="All Country" data-live-search="true" data-size="8">
                                                    <?php foreach($countrydata as $countryrow){ ?>
                                                        <option value="<?php echo $countryrow['coid']; ?>"><?php echo $countryrow['countryname']; ?></option>
                                                    <?php } ?> 
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="provinceid" class="control-label">Province</label>
                                                <select id="provinceid" name="provinceid[]" class="selectpicker form-control" multiple data-actions-box="true" title="All Province" data-live-search="true" data-size="8" >
                                                    <?php foreach($statedata as $staterow){ ?>
                                                        <option value="<?php echo $staterow['sid']; ?>"><?php echo $staterow['statename']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="cityid" class="control-label">City</label>
                                                <select id="cityid" name="cityid[]" class="selectpicker form-control" multiple data-actions-box="true" title="All City" data-live-search="true" data-size="8">
                                                    <?php foreach($citydata as $cityrow){ ?>
                                                        <option value="<?php echo $cityrow['cid']; ?>"><?php echo $cityrow['cityname']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    */?>
                                    <div class="col-md-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="memberid" class="control-label">Party</label>
                                                <select id="memberid" name="memberid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="8" data-live-search="true" title="All Member" multiple data-actions-box="true">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="year" class="control-label">Year</label>
                                                <input type="text" id="year" name="year" class="form-control" value="<?php echo date("Y"); ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 pl-sm pr-n">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="month" class="control-label">Month</label>
                                                <select id="month" name="month[]" class="selectpicker form-control" multiple data-actions-box="true" title="All Month" data-size='8' data-live-search="true"> 
                                                    <?php foreach ($this->Monthwise as $monthid => $monthvalue) { ?>
                                                        <option value="<?=$monthid?>"><?=$monthvalue?></option>
                                                    <?php }?>                                   
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 pl-n pr-xs">
                                        <div class="form-group pt-xl">
                                            <div class="col-md-12">
                                                <label class="control-label"></label>
                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <?php /*
                                    <div class="col-md-3 pl-n pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="channelid" class="control-label">Channel</label>
                                                <select id="channelid" name="channelid" class="selectpicker form-control" data-size='8' data-live-search="true"> 
                                                    <option value="0">All Channel</option>
                                                    <?php foreach ($channeldata as $channel) { ?>
                                                        <option value="<?=$channel['id']?>"><?=$channel['name']?></option>
                                                    <?php }?>                                   
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    */?>
                                </div>
                            </div> 
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-default border-panel">
                    <div class="panel-heading">
                        <div class="col-md-6">
                            <div class="panel-ctrls panel-tbl"></div>
                        </div>
                        <div class="col-md-6 form-group" style="text-align: right;">
                            <?php if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exporttoexcelcancelledordersreport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exporttopdfcancelledordersreport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                            <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printcancelledordersreport()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body no-padding">
                        <div class="table-responsive">
                            <table id="cancelledordersreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                               <thead>
                                    <tr>            
                                      <th class="width8">Sr. No.</th> 
                                      <th>Party</th>
                                      <th>Total Cancel Order</th>
                                      <th>Year</th>
                                    </tr>
                            </thead>
                            </table>
                        </div>
                    </div>
                    <div class="panel-footer"></div>
                    </div>
                </div>
            </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


