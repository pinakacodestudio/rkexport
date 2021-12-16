<div class="page-content">
    <div class="page-heading">     
      <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
               
                <form action="#" id="openingstockimportform" class="form-horizontal">
                  <div class="col-md-12">
                      <div class="col-md-6">
                        <div class="form-group" id="importopeningstock_div">
                          <label class="control-label">Select Excel File <span class="mandatoryfield">*</span></label>
                          <div class="input-group">
                                <span class="input-group-btn" style="padding: 0 12px 0px 0px;">
                                  <span class="btn btn-primary btn-raised btn-file">Browse...
                                  <input type="file" name="importopeningstock" id="importopeningstock" accept="xl,.xlc,.xls,.xlsx,.ods">
                                </span>
                                </span>
                              <input type="text" readonly="" id="Filetext" class="form-control" value="" placeholder="Select Excel File">

                              
                          </div>
                          
                        </div>
                      </div>

                      <div class="col-md-4">
                        <div class="form-group">
                          <label></label>
                          
                          <?php if (in_array("import-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                          <button type="button" onclick="checkimportopeningstockvalidation()" id="search_btn" class="btn btn-primary text-white" style="margin-top: 60px;">Import</button>
                          <?php } ?>
                          <a href="<?=IMPORT_FILE?>import-openingstock.xls" class="btn btn-default btn-raised" download="import-openingstock.xls" style="margin-top: 55px;"><i class="fa fa-download"></i> Download File<div class="ripple-container"></div></a>

                         

                        </div>
                      </div>
                  </div>
                </form>
              </div>
              <div class="panel-body no-padding"></div>
              <div class="panel-footer"></div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                <div class="col-md-4 p-n">
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-8 text-right">
                    <?php
                        if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                      ?>
                      <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Import opening stock','<?php echo ADMIN_URL; ?>import-openingstock/delete-mul-import-openingstock')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                      <?php } ?>
                </div>
                
              </div>
              <div class="panel-body no-padding">
                <table id="openingstocktable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Channel Name</th>
                      <th><?=Member_label?> Name</th>
                      <th>Employee Name</th>  
                      <th>File</th>  
                      <th>IP Address</th>  
                      <th class="text-right">Total Entries</th>
                      <th >Date</th>
                      <th>Addedby</th>
                      <th class="width5">Actions</th>
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


