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
                                      <label for="employeeid" class="control-label">Sales Person</label>
                                      <select id="employeeid" name="employeeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Sales Person</option>
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
                                      <label for="channelid" class="control-label">Channel</label>
                                      <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Channel</option>
                                          <?php foreach ($channeldata as $channel) { ?>
                                              <option value="<?=$channel['id']?>"><?=ucwords($channel['name'])?></option>
                                          <?php } ?> 
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm pl-sm">
                                      <label for="memberid" class="control-label"><?=Member_label?></label>
                                      <select id="memberid" name="memberid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" title="All <?=Member_label?>" data-actions-box="true" multiple>
                                      </select>
                                  </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group mt-xl">
                                  <div class="col-sm-12 pr-sm">
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
                    <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>sales-person-members/add-sales-person-member" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                    <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Sales Person <?php echo Member_label; ?>','<?php echo ADMIN_URL; ?>sales-person-members/delete-mul-sales-person-member')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="salespersonmembertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Sales Person</th>
                      <th><?=Member_label?></th>
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

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
