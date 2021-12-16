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
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                    <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>raw-material-request/add-raw-material-request" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                    <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>designation-mapping/check-raw-material-request-use','Raw Material Request','<?php echo ADMIN_URL; ?>raw-material-request/delete-mul-raw-material-request')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="rawmaterialrequesttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr. No.</th>
                      <th>Order No.</th> 
                      <th>Request No.</th> 
                      <th>Added By</th> 
                      <th>Request Date</th> 
                      <th>Estimate Date</th> 
                      <th>Request Status</th> 
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog " role="document" style="width: 800px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title">View Raw Material Request Detail</h4>
      </div>
      <div class="modal-body">
        <div class="col-md-12 p-n">
          <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Sr. No.</th>
                    <th>Product Name</th>
                    <th>Unit</th>
                    <th class="text-right">Quantity</th>
                  </tr>
                </thead>
                <tbody id="requestproductdata"></tbody>
              </table>
              <table class="table table-bordered" id="remarkstable">
                <thead id="requestdata"></thead>
              </table>   
          </div>
        </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>