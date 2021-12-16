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
                        <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>approval-levels/add-approval-levels" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                    <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('','Approval Levels','<?php echo ADMIN_URL; ?>approval-levels/delete-mul-approval-levels')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                    <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="approvallevelstable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr. No.</th>
                      <!-- <th>Channel</th> 
                      <th>Member</th>  -->
                      <th>Module</th> 
                      <th>Detail</th> 
                      <th>Entry Date</th> 
                      <th>Added By</th> 
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
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
          <div class="col-md-12 p-n">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                    <th class="text-center">Approval Level</th>
                    <th>Designation</th>
                    <th class="text-center">Enable</th>
                    <th class="text-center">Send Email</th>
                </thead>
                <tbody id="details"></tbody>
              </table>
            </div>
          </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>