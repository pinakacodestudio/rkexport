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
                    <div class="col-md-3 col-sm-6">
                      <div class="form-group">
                        <div class="col-md-12 pl-sm pr-sm">
                          <label for="vehicleid" class="control-label">Vehicle</label>
                          <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-select-on-tab="true" data-size="8" data-live-search="true">
                            <option value="0">All Vehicle</option>
                            <?php if(!empty($vehicledata)){ 
                              foreach($vehicledata as $vehicle){ ?>    
                                <option value="<?=$vehicle['id']?>"><?=$vehicle['vehiclename'];?></option>
                            <?php } 
                            } ?>
                          </select>
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
        <div class="col-md-12">
          <div class="panel panel-default border-panel">
            <div class="panel-heading">
              <div class="col-md-6 ResponsivePaddingNone">
                <div class="panel-ctrls panel-tbl"></div>
              </div>
              <div class="col-md-6 form-group" style="text-align: right;">
                <?php if (in_array("export-to-excel",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelAlertReport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                <?php } if (in_array("export-to-pdf",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFAlertReport()"  title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                <?php } if (in_array("print",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printAlertReport()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                <?php } ?>
              </div>
            </div>
            <div class="panel-body no-padding">
              <table id="vehicleemitable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="width8">Sr. No.</th>
                    <th>Vehicle Name</th>
                    <th>Installment amount (<?=CURRENCY_CODE?>)</th>
                    <th>Installment Date</th>
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

