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
                      <div class="col-md-5">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group" style="margin-top: 39px;">
                          <div class="col-sm-12">
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
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>vendor/vendor-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="vendortable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr.No.</th>
                      <th><?=Member_label?> Name</th>
                      <th class="width8">Contact Details</th>
                      <!-- <th class="width5 text-right">Cart Count</th> -->
                      <th class="text-right">Opening Balance</th>
                      <th>Entry Date</th>
                      <th class="width10">Action</th>
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
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document" style="width:425px;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
            <h4 class="modal-title">QR Code</h4>
          </div>
          <div class="modal-body" style="float: left;width: 100%;">
              <div class="col-md-12" id="qrcodeimage"></div>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="openingbalanceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
            <h4 class="modal-title">Opening Balance</h4>
          </div>
          <div class="modal-body" style="float: left;width: 100%;">
            <form class="form-horizontal" id="openingbalanceform">
              <input type="hidden" id="openingbalanceid" name="openingbalanceid">
              <input type="hidden" id="memberid" name="memberid">
              <div class="form-group" id="balancedate_div">
                <label class="col-sm-4 control-label">Opening Balance Date <span class="mandatoryfield">*</span></label>
                <div class="col-sm-8">
                  <input id="balancedate" type="text" name="balancedate" class="form-control" readonly >
                </div>
              </div>
              <div class="form-group" id="balance_div">
                <label class="col-sm-4 control-label">Opening Balance <span class="mandatoryfield">*</span></label>
                <div class="col-sm-8">
                  <input id="balance" type="text" name="balance" class="form-control" onkeypress="return decimal_number_validation(event,this.value)">
                </div>
              </div>
              <div class="form-group">
                  <div class="col-sm-offset-4 col-sm-8">
                      <div class="input-group">
                      <input type="button" class="btn btn-primary btn-raised" onclick="checkopeningbalancevalidation()" value="Submit">
                      <button class="btn btn-primary btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
                  </div>
                  </div>
              </div>
            </form>
          </div>
          <div class="modal-footer"></div>
        </div>
      </div>
    </div>
</div> <!-- #page-content -->