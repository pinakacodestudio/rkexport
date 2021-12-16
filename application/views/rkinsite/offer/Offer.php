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
                                  <div class="col-md-3 pl-sm pr-sm">
                                      <div class="form-group">
                                          <div class="col-md-12">
                                            <label for="type" class="control-label">Type</label>
                                            <select id="type" name="type" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                              <option value="0">Select Type</option>
                                              <option value="1">Display Only</option>
                                              <option value="2">Product</option>
                                              <option value="3">Service</option>
                                              <option value="4">Target</option>
                                            </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-3 pl-sm pr-sm">
                                      <div class="form-group">
                                          <div class="col-md-12">
                                              <label for="channelid" class="control-label">Channel</label>
                                              <select id="channelid" name="channelid" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                                  <option value="">Select Channel</option>
                                                  <option value="0">All Channel & <?=Member_label?></option>
                                                  <?php if(!empty($channeldata)){
                                                      foreach($channeldata as $channel){ ?>
                                                          <option value="<?=$channel['id']?>"><?=$channel['name']?></option>
                                                      <?php }
                                                  } ?>
                                              </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-3 pl-sm pr-sm">
                                      <div class="form-group">
                                          <div class="col-md-12">
                                              <label for="memberid" class="control-label"><?=Member_label?></label>
                                              <select id="memberid" name="memberid" class="selectpicker form-control" data-select-on-tab="true" data-size="6" data-live-search="true" >
                                                  <option value="0">Select <?=Member_label?></option>
                                              </select>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="col-md-2 pl-sm pr-sm">
                                      <div class="form-group mt-xxl">
                                          <div class="col-md-12">
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
                    if (strpos($submenuvisibility['submenuadd'], $this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                    ?>
                        <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>offer/offer-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                    <?php
                    } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>offer/check-offer-use','Offer','<?php echo ADMIN_URL; ?>offer/delete-mul-offer')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="offertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr. No.</th>
                      <th>Channel</th>
                      <th>Offer Name</th>
                      <th>Type</th>
                      <th>Start Date</th>
                      <th>End Date</th>
                      <th>Description</th>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
        <h4 class="modal-title" id="pagetitle">Offer Decription</h4>
      </div>
      <div class="modal-body" style="max-width: 600px;max-height: 400px;overflow: auto;">
          <div id="description"></div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<div class="modal fade" id="MemberModal" tabindex="-1" role="dialog" aria-labelledby="MemberModalLabel">
  <div class="modal-dialog" role="document" style="width: 600px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span></button>
        <h4 class="modal-title" id="pagetitle"><?=Member_label?></h4>
      </div>
      <div class="modal-body" style="max-width: 600px;max-height: 400px;overflow: auto;">
        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
          <thead>
            <tr>
              <th class="width15">Sr. No.</th>
              <th><?=Member_label?> Name</th>
              <th><?=Member_label?> Code</th>
            </tr>
          </thead>
          <tbody id="memberdata">
          </tbody>
        </table>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>