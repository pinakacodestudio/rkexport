<div class="page-content">
<div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>
  <div class="container-fluid">
    <div data-widget-group="group1">
      <div class="row">
      <div class="col-md-12">
             <div class="panel panel-default border-panel mb-md" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                <div class="panel-heading filter-panel border-filter-heading">
                    <h2><?=APPLY_FILTER?></h2>
                    <div class="panel-ctrls" data-actions-container style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                </div>
                <div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" id="vehicleform" class="form-horizontal">
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col-md-3 col-sm-4">
                            <div class="form-group">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label for="companyid" class="control-label">Vehicle Company</label>
                                <select id="companyid" name="companyid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                  <option value="0">All Vehicle Company</option>
                                  <?php foreach($companydata as $company){ ?>
                                  <option value="<?php echo $company['id']; ?>"><?php echo $company['companyname']; ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-3 col-sm-4">
                            <div class="form-group">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label for="ownerpartyid" class="control-label">Owner</label>
                                <select id="ownerpartyid" name="ownerpartyid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                  <option value="0">All Owner</option>
                                  <?php foreach($ownerdata as $owner){ ?>
                                  <option value="<?php echo $owner['id']; ?>"><?php echo $owner['name']; ?></option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          </div>
                          
                          <div class="col-md-2 col-sm-4">
                            <div class="form-group">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label for="vehicletype" class="control-label">Vehicle Type</label>
                                <select id="vehicletype" name="vehicletype" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                  <option value="0">All Vehicle Type</option>
                                  <?php foreach ($this->Licencetype as $key=>$type) { ?>
                                      <option value="<?php echo $key; ?>"><?php echo $type; ?>
                                      </option>
                                  <?php } ?>
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-2 col-sm-3">
                            <div class="form-group">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label for="commercial" class="control-label">Commercial</label>
                                <select id="commercial" name="commercial" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                  <option value="">All</option>
                                  <option value="0">Non Commercial</option>
                                  <option value="1">Commercial</option>
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-2 col-sm-3">
                            <div class="form-group">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label for="sold" class="control-label">Status</label>
                                <select id="sold" name="sold" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8">
                                  <option value="">All</option>
                                  <option value="1">Sold</option>
                                  <option value="0">Not Sold</option>
                                </select>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-2 col-sm-3">
                            <div class="form-group">
                              <div class="col-md-12 pl-sm pr-sm">
                                <label for="days" class="control-label">Days</label>
                                  <input type="text" class="input-small form-control" name="days" id="days" placeholder="Days" value="30" title="Days" onkeypress="return isNumber(event)"/>
                              </div>
                            </div>
                          </div>
                        
                          <div class="col-md-2 col-sm-3">
                          <div class="form-group SetTopMarginOnButton">
                              <div class="col-md-12 pl-sm pr-sm">
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
              <div class="col-md-6 ResponsivePaddingNone">
                <div class="panel-ctrls panel-tbl"></div>
              </div>
              <div class="col-md-6 form-group" style="text-align: right;">
                <?php if (in_array("export-to-excel",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelExpireVehicleRegistrationReport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                <?php } if (in_array("export-to-pdf",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFExpireVehicleRegistrationReport()"  title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                <?php } if (in_array("print",$this->viewData['thirdlevelsubmenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printExpireVehicleRegistrationReport()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                <?php } ?>
              </div>
            </div>
            <div class="panel-body no-padding">
              <table id="expirevehicleregistrationtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr> 
                  <th>Vehicle Name</th>
                  <th>Vehicle Number</th>
                  <th>Vehicle Type</th>
                  <th>Party Name</th>
                  <th>Contact No.</th>
                  <th>Due Date of Registration</th>
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
<?php $this->load->view(ADMINFOLDER.'document/Documentmodal');?>

