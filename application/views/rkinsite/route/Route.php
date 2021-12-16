<?php 
$CHANNEL_DATA = '';
if(!empty($channeldata)){
    foreach($channeldata as $channel){
        $CHANNEL_DATA .= '<option value="'.$channel['id'].'">'.$channel['name'].'</option>';
    } 
}
?>
<script>
    var CHANNEL_DATA = '<?=$CHANNEL_DATA?>';
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
                      <form action="#" class="form-horizontal">
                          <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                  <div class="col-sm-12 pr-sm">
                                      <label for="routeid" class="control-label">Route</label>
                                      <select id="routeid" name="routeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                          <option value="0">All Route</option>
                                          <?php foreach ($routedata as $route) { ?>
                                              <option value="<?=$route['id']?>"><?=ucfirst($route['route'])?></option>
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
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>route/route-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                    <?php
                    if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Route','<?php echo ADMIN_URL; ?>route/delete-mul-route')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                  </div>
              </div>
              <div class="panel-body no-padding">
                <table id="routetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Route</th>
                      <th>Total Time</th>
                      <th>Total KM</th>
                      <th><?=Member_label?> List</th>
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

<div class="modal fade" id="EditroutememberModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
          <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body pt-n" style="overflow-y: auto;max-height: 430px;">
        <form class="form-horizontal" id="routeform" name="routeform" method="post">
          <input type="hidden" name="editrouteid" id="editrouteid" value="">
          <div class="col-md-12 p-n">
            <div class="col-sm-3 pl-sm pr-sm">
                <div class="form-group">
                    <div class="col-sm-12">
                    <label class="control-label">Select Channel</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 pl-sm pr-sm">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label">Select <?=Member_label?></label>
                    </div>
                </div>
            </div>
            <div class="col-sm-1 pl-sm pr-sm">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label">Priority</label>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 pl-sm pr-sm">
                <div class="form-group">
                    <div class="col-sm-12">
                        <label class="control-label">Active</label>
                    </div>
                </div>
            </div>
          </div>
          <div id="routememberdata"></div>
          <div class="col-md-12 p-n"><hr></div>  
          <div class="col-md-12">
              <div class="form-group text-center">
                  <div class="col-sm-12">
                      <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                      <button type="button" class="<?=cancellink_class;?>" data-dismiss="modal" title=<?=cancellink_title?>>Close</button>
                  </div>
              </div>
          </div>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>