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
                      <div class="col-sm-3">
                        <div class="form-group">
                          <div class="col-sm-12 pr-xs">
                              <label for="type" class="control-label">Plan Type</label>
                              <select id="type" name="type" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                <option value="">All Plan Type</option>
                                <option value="0">Order Wise</option>
                                <option value="1">Product Wise</option>
                              </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group" style="margin-top: 37px;">
                          <div class="col-sm-12 pl-xs">
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
                <div class="row">
                  <div class="col-md-6">
                    <div class="panel-ctrls panel-tbl"></div>
                  </div>
                  <div class="col-md-6 form-group" style="text-align: right;">
                      <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>production-plan/add-production-plan" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                      <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Production Plan','<?php echo ADMIN_URL; ?>production-plan/delete-mul-production-plan')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <?php } ?>
                  </div>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="productionplantable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width5">Sr. No.</th>
                      <th>Plan Type</th> 
                      <th>Order Number</th> 
                      <th>Material Status</th> 
                      <th>Generate PO</th> 
                      <th>Status</th> 
                      <th>Entry Date</th> 
                      <th class="width15">Actions</th>
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
      <div class="modal fade" id="startProcessModal" tabindex="-1" role="dialog" aria-labelledby="startProcessLabel">
        <div class="modal-dialog" role="document" style="width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                <h4 class="modal-title">Start Process</h4>
                </div>
                <div class="modal-body" style="float: left;width: 100%;padding:8px 16px;overflow-y: auto;max-height: 420px;">
                    <div class="col-md-12">
                        <form class="form-horizontal" id="start-process-form">
                            <input type="hidden" name="orderid" id="orderid">
                            <input type="hidden" name="productionplanid" id="productionplanid">
                            <div class="row" id="startprocessdata">
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group text-center mt-md">
                                    <!-- <a id="refreshproduct" href="javascript:void(0)" onclick="" class="btn btn-info btn-raised" title="Refresh"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</a> -->
                                    <input type="button" id="submit" name="submit" value="Start Process" class="btn btn-primary btn-raised" onclick="startprocess()">
                                    <a class="<?=cancellink_class;?>" href="javascript:void(0)" data-dismiss="modal" aria-label="Close" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                  </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
      </div>
      <div class="modal fade" id="rawMaterialModal" tabindex="-1" role="dialog" aria-labelledby="rawMaterialLabel">
        <div class="modal-dialog" role="document" style="width: 900px;">
            <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
                <h4 class="modal-title">Raw Material Details</h4>
                </div>
                <div class="modal-body" style="float: left;width: 100%;padding:8px 16px;overflow-y: auto;max-height: 420px;">
                  <div class="row">
                    <div class="col-md-12" id="rawmaterialdata">
                    </div>
                  </div>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
      </div>
    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->