<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <form class="form-horizontal" id="pricelistform" name="pricelistform">
                <div class="panel-heading">
                  <div class="col-md-4 p-n">
                    <div class="panel-ctrls"></div>
                  </div>
                  <div class="col-md-8 p-n" style="text-align: right;">
                    <div class="col-md-8 p-n">
                      <div class="col-sm-6">
                        <div class="form-group" style="margin-top: -3px;">
                            <div class="col-sm-12">
                                <select id="categoryid" name="categoryid[]" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="8" data-actions-box="true" title="All Category" multiple>
                                    <?php if(!empty($categorydata)){ foreach($categorydata as $category){ ?>
                                  <option value="<?php echo $category['id']; ?>"><?php echo ucwords($category['name']); ?></option>
                                    <?php }} ?>
                                </select>
                            </div>
                        </div>
                      </div>
                      <div class="col-sm-5 p-n">
                        <div class="form-group" style="margin-top: -3px;">
                            <div class="col-md-12">
                              <div class="col-md-6 col-xs-4 p-n">
                                <div class="radio">
                                  <input type="radio" name="producttype" id="regular" value="0" checked>
                                  <label for="regular">Regular</label>
                                </div>
                              </div>
                              <div class="col-md-6 col-xs-4 p-n">
                                  <div class="radio">
                                  <input type="radio" name="producttype" id="raw" value="2">
                                  <label for="raw">Raw</label>
                                  </div>
                              </div>
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-4 p-n">
                      <?php 
                      if (strpos(trim($submenuvisibility['submenuadd']),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                        ?>
                        <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>price-list/add-product-price" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                      <?php }
                          /* if (strpos(trim($submenuvisibility['submenuedit']),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                      ?>
                      <a id="editbtnproduct" class="<?=editbtn_class;?>" href="javascript:void(0);" title=<?=editbtn_title?> onclick="updateproductbasicprice()" style="display:none;"><?=editbtn_text;?></a>
                      <?php } */ ?>
                      <?php if (in_array("import-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                      <a class="<?=importbtn_class;?>" href="javascript:void(0)" onclick="importchannelproduct()" title="<?=importbtn_title?>"><?=importbtn_text;?></a>
                      <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                      <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportproduct()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="panel-body no-padding" style="overflow-x: auto;">
                  <table id="producttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  </table>
                </div>
                <div class="panel-footer"></div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<!-- <div class="modal fade" id="myDetailModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title">Import Product Price</h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="productpriceimportform">
          <div class="form-group" id="attachment_div">
            <label class="col-sm-4 control-label">Select Excel File <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                    <span class="btn btn-primary btn-raised btn-file">Browse...
                    <input type="file" name="attachment" id="attachment" accept=".xls,.xlsx" >
                  </span>
                  </span>
                <input type="text" readonly="" id="Filetext" class="form-control" value="">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">Download Format</label>
            <div class="col-sm-8">
                <div class="input-group">
                  <a href="<?=IMPORT_FILE?>import-admin-product-price.xls" class="btn btn-default btn-raised" download="import-admin-product-price.xls"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkvalidation()" value="Import">
                  <button class="btn btn-primary btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
            </div>
          </div>
        </form>
      </div>
      
    </div>
  </div>
</div> -->
<div class="modal fade" id="importpriceModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></button>
        <h3 class="modal-title">Import Product Price</h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" id="channelproductpriceimportform">
          <div class="form-group" style="display:none;">
            <label for="importchannelid" class="col-sm-4 control-label">Select Channel</label>
            <div class="col-sm-8">
              <select id="importchannelid" name="importchannelid" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                <option value="0">All Channel</option>
                <?php if(!empty($channeldata)){ 
                    foreach($channeldata as $channel){?>
                        <option value="<?=$channel['id']?>"><?=$channel['name']?></option>
                <?php } } ?>
              </select>
            </div>
          </div>
          <div class="form-group" id="importattachment_div">
            <label class="col-sm-4 control-label">Select Excel File <span class="mandatoryfield">*</span></label>
            <div class="col-sm-8">
                <div class="input-group">
                  <span class="input-group-btn" style="padding: 0 0px 0px 0px;">
                    <span class="btn btn-primary btn-raised btn-file">Browse...
                    <input type="file" name="importattachment" id="importattachment" accept=".xls,.xlsx" >
                  </span>
                  </span>
                <input type="text" readonly="" id="importFiletext" class="form-control" value="">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-4 control-label">Download Format</label>
            <div class="col-sm-8">
                <div class="input-group">
                  <a href="javascript:void(0)" class="btn btn-default btn-raised" onclick="downloadExcelFile()"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <div class="input-group">
                  <input type="button" class="btn btn-primary btn-raised" onclick="checkvalidationimport()" value="Import">
                  <button class="btn btn-primary btn-raised" data-dismiss="modal">Close<div class="ripple-container"></div></button>
              </div>
            </div>
          </div>
        </form>
      </div>
      
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>