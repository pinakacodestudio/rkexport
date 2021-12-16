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
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-md-12 pr-sm pl-n">
                                                <label for="buyerchannelid" class="control-label">Buyer Channel</label>
                                                <select id="buyerchannelid" name="buyerchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                                <option value="">All Buyer Channel</option>
                                                <?php foreach($channeldata as $cd){ ?>
                                                    <option value="<?php echo $cd['id']; ?>"><?php echo $cd['name']; ?></option>
                                                <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label for="buyermemberid" class="control-label">Buyer <?=Member_label?></label>
                                                <select id="buyermemberid" name="buyermemberid[]" multiple data-actions-box="true" title="All Buyer <?=Member_label?>"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                        <div class="col-md-12 pl-sm pr-sm">
                                            <label for="sellerchannelid" class="control-label">Seller Channel</label>
                                            <select id="sellerchannelid" name="sellerchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                            <option value="">All Seller Channel</option>
                                            <option value="0">Company</option>
                                            <?php foreach($channeldata as $cd){ ?>
                                                <option value="<?php echo $cd['id']; ?>"><?php echo $cd['name']; ?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                        <div class="col-md-12 pl-sm pr-sm">
                                            <label for="sellermemberid" class="control-label">Seller <?=Member_label?></label>
                                            <select id="sellermemberid" name="sellermemberid[]" multiple data-actions-box="true" title="All Seller <?=Member_label?>"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                            </select>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-sm pr-sm">
                                                <label for="status" class="control-label">Status</label>
                                                <select id="status" name="status" class="selectpicker form-control" data-select-on-tab="true">
                                                    <option value="">All Status</option>
                                                    <option value="1">Paid</option>
                                                    <option value="0">Not Paid</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <div class="col-md-12 pl-n">
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
                            <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exporttoexcelcashbackreport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                            <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFCashbackReport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                            <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printcashbackreport()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="panel-body no-padding">
                        <table id="cashbackreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>            
                                <th class="width8">Sr. No.</th>
                                <th>Buyer Name</th>
                                <th>Seller Name</th>
                                <th>Invoice No.</th>
                                <th>Invoice Amount (<?=CURRENCY_CODE?>)</th>
                                <th>Cashback Amount (<?=CURRENCY_CODE?>)</th>
                                <th>Status</th>
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

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


