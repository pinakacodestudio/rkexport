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
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL; ?>product-tag/product-tag-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?=ADMIN_URL; ?>product-tag/check-product-tag-use','Product Tag','<?=ADMIN_URL; ?>product-tag/delete-mul-product-tag')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                  <a class="<?=orderbtn_class;?>" href="javascript:void(0)" onclick="setorder('<?=ADMIN_URL; ?>product-tag/update-priority')" id="btntype" title="<?=orderbtn_title?>"><?=orderbtn_text;?></a>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="producttagtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>                     
                      <th class="width8">Sr. No.</th>
                      <th>Tag</th>
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