<script>
  var isdelete = '<?=(($this->session->userdata[base_url().'ADMINUSERTYPE']==1 && strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false)?1:0)?>';
</script>
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
                      <div class="col-md-2">
                        <div class="form-group" id="channelid_div">
                          <div class="col-sm-12">
                            <label for="channelid" class="control-label">Select Party</label>
                            <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                              <option value="0">All Party</option>
                              <?php foreach($channeldata as $cd){ ?>
                              <option value="<?php echo $cd['id']; ?>"><?php echo $cd['name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group" id="channelid_div">
                          <div class="col-sm-12">
                            <label for="channelid" class="control-label">Employee</label>
                            <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                              <option value="0">Name List</option>
                              <?php foreach($channeldata as $cd){ ?>
                              <option value="<?php echo $cd['id']; ?>"><?php echo $cd['name']; ?></option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="status" class="control-label">PO Status</label>
                            <select id="status" name="status" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                              <option value="-1">All Status</option>
                              <option value="0">Pending</option>
                              <option value="3">Partially</option>
                              <option value="1">Complete</option>
                              <option value="2">Cancel</option>
                            </select>
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
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                      <!-- <a class="<?php //addproductorderbtn_class?>" href="<?php //ADMIN_URL?>purchase-order/purchase-product-order-add" title="<?php //addproductorderbtn_title?>"><?php //addproductorderbtn_text?></a> -->
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>purchase-order/purchase-order-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                  <?php if ($this->session->userdata[base_url().'ADMINUSERTYPE']==1 && strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>purchase-order/check-purchase-order-use','Purchase Order','<?php echo ADMIN_URL; ?>order/delete-mul-purchase-order')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                    <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportorders()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="purchaseordertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Party Name</th>
                      <th>PO ID</th>
                      <th>Revision</th>
                      <th>PO Date</th>
                      <th>PO Status</th>
                      <th class="text-right">Amount (<?=CURRENCY_CODE?>)</th>
                      <th>AddedBy</th>
                      <th class="width15">Actions</th>
                      <?php if ($this->session->userdata[base_url().'ADMINUSERTYPE']==1 && strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <th class="width5">
                            <div class="checkbox">
                              <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                              <label for="deletecheckall"></label>
                            </div>
                          </th>
                        <?php } ?>
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
      <div class="modal fade" id="rejectorderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1250000;">
        <div class="modal-dialog" role="document" style="width: 460px;">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="col-sm-9 p-n">Reason for Rejection Order</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="pagetitle"></h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id="resonforrejectionform" name="resonforrejectionform">
                <input type="hidden" id="rejectionorderid" name="rejectionorderid">
                <input type="hidden" id="rejectionstatus" name="rejectionstatus">
                <div id="row">
                  <div id="col-md-9">
                    <div class="form-group" id="resonforrejection_div">
                      <div class="col-sm-12">
                          <label for="resonforrejection" class="control-label">Reason for Rejection <span class="mandatoryfield">*</span></label>
                          <textarea id="resonforrejection" name="resonforrejection" class="form-control"></textarea>
                          <p style="color: red;" id="resonalert"></p>
                      </div>
                    </div>
                  </div>
                  <div id="col-md-12">
                    <div class="form-group text-right">
                      <div class="col-sm-12">
                          <input type="button" id="submit" onclick="checkvalidationforrejectionorder()" name="submit" value="SUBMIT" class="btn btn-primary btn-raised">

                          <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>order" title=<?=cancellink_title?>><?=cancellink_text?></a>
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


