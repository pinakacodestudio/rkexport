<style>
  .btnpadd{
    padding: 8px 30px !important;
  }
</style>
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
                  <form action="#" id="agingform" name="agingform" class="form-horizontal">
                    <input type="hidden" name="intervallength" id="intervallength" value="30">
                    <input type="hidden" name="intervalcount" id="intervalcount" value="4">
                    <div class="row">
                      <div class="col-md-12 p-n">
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pr-xs">
                                <label for="producttype" class="control-label">Product Type</label>
                                <select id="producttype" name="producttype[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" data-actions-box="true" title="All Product Type" multiple>
                                    <option value="0">Regular</option>
                                    <option value="1">Offer</option>
                                    <option value="2">Raw Material</option>
                                    <option value="3">Semi-Finish</option>
                                </select>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-12 pr-xs">
                              <label class="control-label">Interval Length</label>
                              <div class="btn-group mt-sm" style="display: block;">
                                  <button type="button" class="btn btn-success btn-raised btn-sm btnpadd btn_interval_length" data-value="30">30 Days<div class="ripple-container"></div></button>
                                  <button type="button" class="btn btn-default btn-raised btn-sm btnpadd btn_interval_length" data-value="90">90 Days</button>
                                  <button type="button" class="btn btn-default btn-raised btn-sm btnpadd btn_interval_length" data-value="180">180 Days</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group">
                            <div class="col-md-12 pr-xs">
                              <label class="control-label">Interval Count</label>
                              <div class="btn-group mt-sm" style="display: block;">
                                  <button type="button" class="btn btn-success btn-raised btn-sm btnpadd btn_interval_count" data-value="4">4<div class="ripple-container"></div></button>
                                  <button type="button" class="btn btn-default btn-raised btn-sm btnpadd btn_interval_count" data-value="8">8</button>
                                  <button type="button" class="btn btn-default btn-raised btn-sm btnpadd btn_interval_count" data-value="12">12</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group pt-xl">
                            <div class="col-md-12 pl-n">
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
                  <?php if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exporttoexcelnonmovingproductreport()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exporttopdfnonmovingproductreport()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                  <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printnonmovingproductreport()" title=<?=printbtn_title?>><?=printbtn_text?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <div class="table-responsive">
                  <table id="agingreporttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  </table>
                </div>
              </div>
              <div class="panel-footer"></div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->


