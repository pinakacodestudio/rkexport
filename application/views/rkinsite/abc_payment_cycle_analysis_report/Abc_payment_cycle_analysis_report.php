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
                      <div class="col-md-4 pr-xs">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-6 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-1 pl-xs pr-xs">
                        <div class="form-group text-right" id="classA_div">
                          <div class="col-sm-12">
                            <label for="classA" class="control-label">Class A (%)</label>
                              <input type="text" class="form-control text-right" name="classA" id="classA" onkeypress="return decimal_number_validation(event,this.value,3)" value="50"/>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-1 pl-xs pr-xs">
                        <div class="form-group text-right" id="classB_div">
                          <div class="col-sm-12">
                            <label for="classB" class="control-label">Class B (%)</label>
                              <input type="text" class="form-control text-right" name="classB" id="classB" onkeypress="return decimal_number_validation(event,this.value,3)" value="30"/>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-1 pl-xs pr-xs">
                        <div class="form-group text-right" id="classC_div">
                          <div class="col-sm-12">
                            <label for="classC" class="control-label">Class C (%)</label>
                              <input type="text" class="form-control text-right" name="classC" id="classC" onkeypress="return decimal_number_validation(event,this.value,3)" value="20"/>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-2 pl-xs pr-xs">
                          <div class="form-group">
                              <div class="col-sm-12">
                                  <label for="channelid" class="control-label">Select Channel</label>
                                  <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="All Channel" data-live-search="true" >
                                      <option value="0">All Channel</option>
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
                      <div class="col-md-3 pl-xs">
                          <div class="form-group">
                            <div class="col-sm-12">
                              <label for="memberid" class="control-label">Select <?=Member_label?></label>
                              <select id="memberid" name="memberid[]" multiple data-actions-box="true" title="All <?=Member_label?>"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                              </select>
                            </div>
                          </div>
                      </div>
                    </div>
                    <div class="row">                                    
                        <div class="col-md-3">
                            <div class="form-group">
                                <div class="col-sm-12">
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
                  <?php if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelABCPaymentCycleReport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFABCPaymentCycleReport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                  <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printabcpaymentcyclereport()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="abcpaymentcycleanalysisreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th><?=Member_label?></th>
                      <th>Total Invoice</th>
                      <th>Invoice Amount</th>
                      <th>Avg. Payment Cycle Days</th>
                      <th>Cumulative</th>
                      <th>Class</th>
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


