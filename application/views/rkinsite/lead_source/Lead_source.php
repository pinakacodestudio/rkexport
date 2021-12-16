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
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                <?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>lead-source/lead-source-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                <?php } 
                if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>lead-source/check-lead-source-use','Lead Source','<?php echo ADMIN_URL; ?>lead-source/delete-mul-lead-source')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="leadsourcetable" class="table table-striped table-bordered table-responsive-sm" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th class="width8">Sr. No.</th>
                            <th>Name</th>
                            <th>Color</th>
                            <th class="width15">Action</th>
                            <th class="width5">
                                <div class="checkbox table-checkbox">
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
