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
                        <form action="#" id="memberform" class="form-horizontal">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-4 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                            <label class="control-label">Date</label>
                                            <div class="input-daterange input-group" id="datepicker-range">
                                                <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-6 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                <span class="input-group-addon">to</span>
                                                <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="channelid" class="control-label">Channel</label>
                                                <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                                    <option value="0">Select Channel</option>
                                                    <?php if(!empty($channeldata)){
                                                        foreach($channeldata as $channel){ ?>
                                                            <option value="<?=$channel['id']?>"><?=$channel['name']?></option>
                                                        <?php }
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label for="memberid" class="control-label"><?=Member_label?></label>
                                                <select id="memberid" name="memberid" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                                    <option value="0">Select <?=Member_label?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-2 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <label class="control-label"></label>
                                                <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                            </div>
                                        </div>
                                    </div>
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
                            <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exporttoexcelgstr2report()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exporttopdfgstr2report()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                            <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printGSTR2Report()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body no-padding">
                        <div class="table-responsive">
                            <table id="gstr2reporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>            
                                        <th>GST No.</th>
                                        <th><?=Member_label?> Name</th>
                                        <th>City Name</th>
                                        <th>Invoice No.</th>
                                        <th>Invoice Date</th>
                                        <th>Invoice Value</th>
                                        <th>Place of Supply</th>
                                        <th>Reverse Charge</th>
                                        <th>Tax Rate</th>
                                        <th>Taxable Value</th>
                                        <th>Integrated to (IGST)</th>
                                        <th>Central to (CGST)</th>
                                        <th>State/UT to (SGST)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
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


