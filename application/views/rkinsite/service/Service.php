<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>
  <div class="container-fluid">

    <div data-widget-group="group1">
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
              <div class="panel-heading filter-panel border-filter-heading">
                <h2><?= APPLY_FILTER ?></h2>
                <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
              </div>
              <div class="panel-body panelcollapse pt-n" style="display: none;">
                <form action="#" id="serviceform" class="form-horizontal">
                  <div class="row">
                    <div class="col-md-3 pr-n">
                      <div class="form-group" id="vehicle_div">
                        <div class="col-sm-12">
                          <label for="vehicleid" class="control-label">Vehicle</label>
                          <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                            <option value="0">All Vehicle</option>
                            <?php foreach ($vehicledata as $vehicle) { ?>
                              <option value="<?php echo $vehicle['id']; ?>"><?php echo $vehicle['vehiclename'] . " (" . $vehicle['vehicleno'] . ")"; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3 pr-n">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <label for="driverid" class="control-label">Driver Name</label>
                          <select id="driverid" name="driverid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                            <option value="0">All Driver</option>
                            <?php foreach ($partydata as $party) { ?>
                              <option value="<?php echo $party['id']; ?>"><?php echo $party['name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 pr-n">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <label for="servicetypeid" class="control-label">Service Type</label>
                          <select id="servicetypeid" name="servicetypeid" class="selectpicker form-control" data-select-on-tab="true" data-live-search="true" data-size="5">
                            <option value="0">All Service Type</option>
                            <?php foreach ($servicetypedata as $servicetype) { ?>
                              <option value="<?php echo $servicetype['id']; ?>"><?php echo $servicetype['name']; ?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <label for="startdate" class="control-label">Date</label>
                          <div class="input-daterange input-group" id="datepicker-range">
                            <div class="input-group">
                              <input type="text" class="input-small form-control" style="text-align: left;" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d", strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly />
                              <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                            </div>
                            <span class="input-group-addon">to</span>
                            <div class="input-group">
                              <input type="text" class="input-small form-control" style="text-align: left;" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly />
                              <span class="btn btn-default add-on datepicker_calendar_button" title='Date'><i class="fa fa-calendar fa-lg"></i></span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <div class="col-sm-12">
                          <label class="control-label"></label>
                          <a class="<?= applyfilterbtn_class; ?>" href="javascript:void(0)" onclick="applyFilter()" title=<?= applyfilterbtn_title ?>><?= applyfilterbtn_text; ?></a>
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
                  <?php
                  if (strpos($submenuvisibility['submenuadd'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
                  ?>
                    <a class="<?= addbtn_class; ?>" href="<?= ADMIN_URL ?>service/add-service/<?= $this->uri->segment(3); ?>" title=<?= addbtn_title ?>><?= addbtn_text; ?></a>
                  <?php
                  }
                  if (strpos($submenuvisibility['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
                  ?>
                    <a class="<?= deletebtn_class; ?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>service/check-service-use','Service','<?php echo ADMIN_URL; ?>service/delete-mul-service')" title=<?= deletebtn_title ?>><?= deletebtn_text; ?></a>
                  <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelService()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFService()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                  <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printService()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="servicetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr. No.</th>
                      <th>Vehicle</th>
                      <th>Service Type</th>
                      <th>Date</th>
                      <th>Driver Name</th>
                      <th>Garage Name</th>
                      <th class="text-right">Amount (<?= CURRENCY_CODE ?>)</th>
                      <th>Entry Date</th>
                      <th class="width12">Action</th>
                      <th class="width5">
                        <div class="checkbox">
                          <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                          <label for="deletecheckall"></label>
                        </div>
                      </th>
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