<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
        <div class="col-md-12">
             <div class="panel panel-default border-panel mb-md">
								<div class="panel-heading filter-panel border-filter-heading">
									<h2><?=APPLY_FILTER?></h2>
									<div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
								</div>
								<div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" id="paymentform" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="datetype" class="control-label">Select Type</label>
                              <select id="datetype" name="datetype" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                                <option value="1">Day Wise</option>
                                <option value="2" selected>Month Wise</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label class="control-label">Return Date</label>
                              <div class="input-daterange input-group" id="datepicker-range">
                                  <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-3 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                  <span class="input-group-addon">to</span>
                                  <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="frommemberid" class="control-label">Select Party</label>
                              <select id="frommemberid" name="frommemberid[]"  data-actions-box="true" title="Party"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                              </select>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="status" class="control-label">Select Status</label>
                              <select id="status" name="status[]" multiple data-actions-box="true" title="All Status" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                <option value="0">Pending</option>
                                <option value="1">Complete</option>
                                <option value="2">Cancel</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        
                        <?php /*
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="paidfromchannelid" class="control-label">Payer Channel</label>
                              <select id="paidfromchannelid" name="paidfromchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="All Channel" data-live-search="true" >
                                <option value="">All Channel</option>
                                <option value="0">Company</option>
                                <?php foreach($channeldata as $cd){
                                    $selected = ""; 
                                    if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
                                      $arrChannel = explode(",",$this->session->userdata(base_url().'CHANNEL'));
                                      if(in_array($cd['id'], $arrChannel)){ 
                                        $selected = "selected"; 
                                      } 
                                    }
                                  ?>
                                <option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
                                <?php } ?>
                                
                              </select>
                            </div>
                          </div>
                        </div>
                       
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="frommemberid" class="control-label">Select Payer <?=Member_label?></label>
                              <select id="frommemberid" name="frommemberid[]" multiple data-actions-box="true" title="All <?=Member_label?>"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                              </select>
                            </div>
                          </div>
                        </div>
                         */?>
                      </div>
                      <div class="col-md-12">
                        <?php /*
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="paymenttype" class="control-label">Payment Type</label>
                              <select id="paymenttype" name="paymenttype[]" multiple data-actions-box="true" title="All Type" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                <option value="1">COD</option>
                                <option value="2">Online</option>
                                <option value="3">Advance Payment</option>
                                <option value="4">EMI Payment</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="paidtochannelid" class="control-label">Receiver Channel</label>
                              <select id="paidtochannelid" name="paidtochannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="All Channel" data-live-search="true" >
                                <option value="">All Channel</option>
                                <option value="0">Company</option>
                                <?php foreach($channeldata as $cd){
                                    $selected = ""; 
                                    if(!empty($this->session->userdata(base_url().'CHANNEL'))){ 
                                      $arrChannel = explode(",",$this->session->userdata(base_url().'CHANNEL'));
                                      if(in_array($cd['id'], $arrChannel)){ 
                                        $selected = "selected"; 
                                      } 
                                    }
                                  ?>
                                <option value="<?php echo $cd['id']; ?>" <?php echo $selected; ?>><?php echo $cd['name']; ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="tomemberid" class="control-label">Select Receiver <?=Member_label?></label>
                              <select id="tomemberid" name="tomemberid[]" multiple data-actions-box="true" title="All <?=Member_label?>"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
                              <label for="reporttype" class="control-label">Report Type</label>
                              <select id="reporttype" name="reporttype" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                                <option value="1">Detail Report</option>
                                <option value="2">Summary Report</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        */?>
                        <div class="col-md-2">
                          <div class="form-group">
                            <div class="col-md-12 pl-xs pr-xs">
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
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportpaymentreport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exporttopdfpaymentreport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                  <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printpaymentreport()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding panel-vertical-scroll">
                <table id="paymentreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Payer <?=Member_label?></th>
                      <th>Receiver <?=Member_label?></th>
                      <th>Order ID</th>
                      <th>Payment Date</th>
                      <th>Transaction ID</th>
                      <th>Payment Type</th>
                      <th class="text-right">Total Amount</th>
                    </tr>
                  </thead>
                </table>
              </div>
              <div class="panel-footer"></div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


