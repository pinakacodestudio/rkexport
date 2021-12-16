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
                <h2><?= APPLY_FILTER ?></h2>
                <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
              </div>
              <div class="panel-body panelcollapse pt-n" style="display: none;">
                <form action="#" id="memberform" class="form-horizontal">
                  <div class="row">
                    <div class="col-md-12"> 
                      <div class="col-md-3 col-sm-6 pl-sm pr-sm">
                        <div class="form-group" id="vehicle_div">
                          <div class="col-md-12">
                            <label for="vehicle" class="control-label">Vehicle</label>
                            <select id="vehicle" name="vehicle" class="selectpicker form-control" data-live-search="true" data-size="5">
                              <option value="0">All Vehicle</option>
                              <?php foreach ($vehicledata as $cl) { ?>
                                <option value="<?php echo $cl['id']; ?>"><?php echo $cl['vehiclename'] ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3 col-sm-6">
                        <div class="form-group" id="garageid_div">
                          <div class="col-md-12 pl-sm pr-sm">
                            <label for="garageid" class="control-label">Garage Name</label>
                            <select id="garageid" name="garageid" class="selectpicker form-control" data-live-search="true" data-size="5">
                              <option value="0">All Garage</option>
                              <?php foreach ($partydata as $pd) { ?>
                                <option value="<?php echo $pd['id']; ?>"><?php echo $pd['name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-6">
                        <div class="form-group">
                          <div class="col-md-12 pl-sm pr-sm">
                            <label for="days" class="control-label">Days</label>
                              <input type="text" class="input-small form-control" name="days" id="days" placeholder="Days" value="30" title="Days" onkeypress="return isNumber(event)"/>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2 col-sm-6">
                        <div class="form-group SetTopMarginOnButton">
                          <div class="col-md-12 pl-sm pr-sm">
                            <label class="control-label"></label>
                            <a class="<?= applyfilterbtn_class; ?>" href="javascript:void(0)" onclick="applyFilter()" title=<?= applyfilterbtn_title ?>><?= applyfilterbtn_text; ?></a>
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
      <div class="row">
        <div class="col-md-12">
          <div class="panel panel-default border-panel">
            <div class="panel-heading">
              <div class="col-md-6 ResponsivePaddingNone">
                <div class="panel-ctrls panel-tbl"></div>
              </div>
              <div class="col-md-6 form-group" style="text-align: right;">
                <?php if (in_array("export-to-excel",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelExpireServicePartReport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                <?php } if (in_array("export-to-pdf",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)"  onclick="exportToPDFExpireServicePartReport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                <?php } if (in_array("print",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printExpireServicePartReport()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                <?php } ?>
              </div>
            </div>
            <div class="panel-body no-padding">
              <table id="expireserviceparttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th>Vehicle Name</th>
                    <th>Part Name</th>
                    <th>Garage</th>
                    <th>Serial No.</th>
                    <th>Warranty End Date</th>
                    <th>Due Date</th>    
                    <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                    <!-- <th>Entry Date</th> -->
                    <th>Days</th>
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
  </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


