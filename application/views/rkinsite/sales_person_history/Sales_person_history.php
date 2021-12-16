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
                      <form action="#" class="form-horizontal">
                          <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm pl-sm">
                                      <label for="employeeid" class="control-label">Employee</label>
                                      <select id="employeeid" name="employeeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Employee</option>
                                          <?php foreach ($employeedata as $employee) { ?>
                                              <option value="<?=$employee['id']?>"><?=ucwords($employee['name'])?></option>
                                          <?php } ?> 
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm pl-sm">
                                      <label for="status" class="control-label">Status</label>
                                      <select id="status" name="status" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="">All Status</option>
                                          <option value="0">Pending</option>
                                          <option value="1">Complete</option>
                                          <option value="2">Cancel</option>
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm pl-sm">
                                      <label for="routeid" class="control-label">Route</label>
                                      <select id="routeid" name="routeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Route</option>
                                          <?php foreach ($routedata as $route) { ?>
                                              <option value="<?=$route['id']?>"><?=ucwords($route['routename'])?></option>
                                          <?php } ?> 
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mt-xl">
                                  <div class="col-sm-12 pr-sm pl-sm">
                                      <label class="control-label"></label>
                                      <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
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
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="salespersonhistorytable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                        <th>Employee</th>
                        <th>Route</th>
                        <th>Vehicle Name Code</th>
                        <th>Date</th>
                        <th>Collection (<?=CURRENCY_CODE?>)</th>
                        <th>Loos Money (<?=CURRENCY_CODE?>)</th>
                        <th>Status</th>
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

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->