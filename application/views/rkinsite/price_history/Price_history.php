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
                      <div class="col-md-12">
                          <div class="col-md-4">
                            <div class="form-group">
                              <div class="col-md-12 pl-xs pr-xs">
                                <label for="startdate" class="control-label">History Date</label>
                                <div class="input-daterange input-group" id="datepicker-range">
                                    <input type="text" class="input-small form-control" name="startdate" id="startdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 year"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="input-small form-control" name="enddate" id="enddate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                                </div>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-3">
                            <div class="form-group">
                              <div class="col-md-12 pl-xs pr-xs">
                                  <label for="type" class="control-label">All Type</label>
                                  <select id="type" name="type" class="selectpicker form-control" data-live-search="true">
                                      <option value="">All Type</option>
                                      <option value="0">Admin Product</option>
                                      <option value="1"><?=Member_label?> Product</option>
                                  </select>
                                      
                              </div>
                            </div>
                          </div>
                      
                          <div class="col-md-2">
                            <div class="form-group pt-xl">
                              <div class="col-md-12 pl-xs">
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
                  <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){?>
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>price-history/price-history-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php } if(strpos(trim($submenuvisibility['submenudelete']),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                  ?>
                     <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Price History','<?php echo ADMIN_URL; ?>price-history/delete-mul-price-history')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?> 
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="creditnotettable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Type</th>
                      <th>Scheduled Date</th>
                      <th>Remarks</th>
                      <th>Added By</th>
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


