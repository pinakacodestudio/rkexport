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
                                  <div class="col-sm-12 pr-sm">
                                      <label for="employeeid" class="control-label">Employee</label>
                                      <select id="employeeid" name="employeeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Employee</option>
                                          <?php foreach ($employeedata as $_v) { ?>
                                              <option value="<?=$_v['id']?>"><?=ucwords($_v['name'])?></option>
                                          <?php } ?> 
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pl-sm pr-sm">
                                      <label for="commissiontype" class="control-label">Commission Type</label>
                                      <select id="commissiontype" name="commissiontype" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Commission Type</option>
                                          <?php foreach($this->Commissiontype as $k=>$v){ ?>
                                              <option value="<?=$k?>"><?=$v?></option>
                                          <?php } ?>
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mt-xxl">
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
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>sales-commission/sales-commission-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                    <?php
                    if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Sales Commission','<?php echo ADMIN_URL; ?>sales-commission/delete-mul-sales-commission')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                  </div>
              </div>
              <div class="panel-body no-padding">
                <table id="salescommissiontable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Employee Name</th>
                      <th>Commission Type</th>
                      <th>Details</th>
                      <th>Entry Date</th>
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

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<div class="modal fade" id="CommissionTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
          <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body" style="overflow-y: auto;max-height: 430px;">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>