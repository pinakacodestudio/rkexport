    
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
                              <div class="col-md-12 pl-sm pr-sm">
                                  <div class="col-md-3">
                                      <div class="form-group">
                                          <div class="col-md-12 pl-xs pr-xs">
                                              <label for="partyid" class="control-label">Party</label>
                                              <select id="partyid" name="partyid[]" multiple data-actions-box="true" title="All Party"  class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                                                  <?php foreach($partydata as $emp){ ?>
                                                      <option value="<?php echo $emp['id']; ?>"><?php echo ucwords($emp['partyname']); ?></option>
                                                  <?php }?>
                                              </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-4">
                                      <div class="form-group">
                                          <div class="col-md-12 pl-xs pr-xs">
                                              <label for="startdate" class="control-label">Invoice Date</label>
                                              <div class="input-daterange input-group" id="datepicker-range">
                                                  <div class="input-group">
                                                      <input type="text" style="text-align: left;" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                                      <span class="btn btn-default" style="position: absolute; top: 7px;right:0px;"><i class="fa fa-calendar fa-lg"></i></span>
                                                  </div>
                                                  <span class="input-group-addon">to</span>
                                                  <div class="input-group">
                                                      <input type="text" style="text-align: left;" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                                      <span class="btn btn-default" style="position: absolute; top: 7px;right:0px;"><i class="fa fa-calendar fa-lg"></i></span>
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-3">
                                      <div class="form-group">
                                          <div class="col-md-12 pl-xs pr-xs">
                                              <label for="status" class="control-label">Invoice Status</label>
                                              <select id="status" name="status" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                                  <option value="-1">All GRN Status</option>
                                                  <?php foreach($this->GRNstatus as $key=>$val){ ?>
                                                      <option value="<?=$key?>"><?=$val?></option>
                                                  <?php } ?>
                                              </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-2">
                                      <div class="form-group SetTopMarginOnButton">
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
                  <?php if (strpos(trim($submenuvisibility['submenuadd']),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){ ?>
                        <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>invoice/invoice-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                    <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                    <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exporttoexcelinvoice()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                    <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="invoicetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr. No.</th>
                      <th>Buyer Name</th>
                      <th>Seller Name</th>
                      <th>OrderID</th>
                      <th>Invoice No.</th>
                      <th>Invoice Date</th>
                      <th>Status</th>
                      <th>Amount (<?=CURRENCY_CODE?>)</th>
                      <th class="width12">Action</th>
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

       <!-- Modal -->
       <div class="modal fade" id="rejectinvoiceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1250000;">
        <div class="modal-dialog" role="document" style="width: 460px;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="col-sm-9 p-n">Reason for Cancellation Invoice</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="pagetitle"></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="resonforrejectionform" name="resonforrejectionform">
                <input type="hidden" id="rejectioninvoiceid" name="rejectioninvoiceid">
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
                          <input type="button" id="submit" onclick="checkvalidationforrejectioninvoice()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">

                          <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>invoice" title=<?=cancellink_title?>><?=cancellink_text?></a>
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



<div class="modal fade" id="myAWBModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 900px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">AWB Description</h4>
      </div>
      <div class="modal-body awbbody" style="word-wrap: break-word;max-height: 1000px;
      overflow-y: auto;">
              
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>