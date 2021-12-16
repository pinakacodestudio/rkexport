<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
      <div data-widget-group="group1">
        <div class="row">
            <div class="col-md-12">
             <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                <div class="panel-heading filter-panel border-filter-heading" display-type="<?php if(isset($panelcollapsed) && $panelcollapsed==1){ echo "0"; } else{ echo "1";}?>">
                    <h2><?=APPLY_FILTER?></h2>
                    <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                </div>
                <div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" id="memberform" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-12 p-n">
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-12 pr-sm">
                              <label class="control-label">Transaction Date</label>
                              <div class="input-daterange input-group" id="datepicker-range">
                                  <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php if(isset($startdate) && $startdate!=''){ echo $this->general_model->displaydate($startdate); }else{ echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); } ?>" placeholder="Start Date" title="Start Date" readonly/>
                                  <span class="input-group-addon">to</span>
                                  <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php if(isset($enddate) && $enddate!=''){ echo $this->general_model->displaydate($enddate); }else{ echo $this->general_model->displaydate($this->general_model->getCurrentDate()); } ?>" placeholder="End Date" title="End Date" readonly/>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-12 pl-sm pr-sm">
                              <label for="memberid" class="control-label">Select <?=Member_label?></label>
                              <select id="memberid" name="memberid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                <option value="-1">All <?=Member_label?></option>
                                <?php foreach($memberdata as $member){ ?>
                                    <option value="<?php echo $member['id']; ?>"><?php echo ucwords($member['namewithcodeormobile']); ?></option>
                                <?php } ?>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                            <div class="col-md-12 pl-sm pr-sm">
                              <label for="transactiontype" class="control-label">Type</label>
                              <select id="transactiontype" name="transactiontype" class="selectpicker form-control" data-select-on-tab="true" data-size="5" title="All Type" data-live-search="true" >
                                <option value="0">All Type</option>
                                <option value="1">Is Against Invoice</option>
                                <option value="2">On Account</option>
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group mt-xl">
                            <div class="col-md-12 pl-sm">
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
                    <?php 
                        if (strpos(trim($submenuvisibility['submenuadd'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                    ?>
                    <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>payment-receipt/payment-receipt-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                    <?php
                        }if(strpos(trim($submenuvisibility['submenudelete'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                    ?>
                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Receipt','<?php echo ADMIN_URL; ?>payment-receipt/delete-mul-payment-receipt')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="paymentreceipttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>                  
                      <th class="width8">Sr. No. </th>
                      <th>Buyer Name</th>
                      <th>Bank Name</th>
                      <th>Transaction Date</th>
                      <th>Receipt No.</th>
                      <th>Type</th>
                      <th>Status</th>
                      <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                      <th class="width15">Action</th>
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
      <!-- Approve Receipt Modal -->
      <div class="modal fade" id="receiptModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1250000;">
        <div class="modal-dialog" role="document" style="width: 460px;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="col-sm-9 p-n">Receipt</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="pagetitle"></h4>
            </div>
            <div class="modal-body p-sm">
              <form class="form-horizontal" id="receiptform" name="receiptform">
                <input type="hidden" id="receiptid" name="receiptid">
                <input type="hidden" id="status" name="status">
                <div id="row">
                  <div class="col-md-12">
                      <div class="form-group" id="cashorbankid_div">
                          <div class="col-md-12">								
                              <label class="control-label" for="cashorbankid">Cash / Bank Account <span class="mandatoryfield">*</span></label>
                              <select id="cashorbankid" name="cashorbankid" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="6">
                                  <option value="0">Select Cash / Bank Account</option>
                                  <?php 
                                  if(!empty($bankdata)){
                                    foreach($bankdata as $account){ ?> 
                                      <option value="<?=$account['id']?>"><?=$account['bankname']?></option>
                                    <?php }
                                  }?>
                              </select>
                          </div>
                      </div>
                      <label>Balance : <?=CURRENCY_CODE?> 0.00</label>
                  </div>
                  <div class="col-md-12">
                    <p style="color: red;" id="erroralert"></p>
                  </div>
                  <div id="col-md-12">
                    <div class="form-group text-right">
                      <div class="col-sm-12">
                          <input type="button" id="submit" onclick="checkvalidationreceipt()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">

                          <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>payment-receipt" title=<?=cancellink_title?>><?=cancellink_text?></a>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer"></div>
          </div>
        </div>
      </div>
      <!-- Cancel Receipt Modal -->
      <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1250000;">
        <div class="modal-dialog" role="document" style="width: 460px;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="col-sm-9 p-n">Reason for Cancellation Receipt</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="pagetitle"></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="resonforrejectionform" name="resonforrejectionform">
                <input type="hidden" id="rejectionid" name="rejectionid">
                <input type="hidden" id="rejectionstatus" name="rejectionstatus">
                <div id="row">
                  <div id="col-md-9">
                    <div class="form-group" id="resonforrejection_div">
                      <div class="col-sm-12">
                          <label for="resonforrejection" class="control-label">Reason for Cancellation <span class="mandatoryfield">*</span></label>
                          <textarea id="resonforrejection" name="resonforrejection" class="form-control"></textarea>
                          <p style="color: red;" id="resonalert"></p>
                      </div>
                    </div>
                  </div>
                  <div id="col-md-12">
                    <div class="form-group text-right">
                      <div class="col-sm-12">
                          <input type="button" id="submit" onclick="checkvalidationforrejectionreceipt()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">

                          <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>payment-receipt" title=<?=cancellink_title?>><?=cancellink_text?></a>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer"></div>
          </div>
        </div>
      </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->