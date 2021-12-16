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
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>user-position/user-position-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>user-position/check-user-position-use','User Position','<?php echo ADMIN_URL; ?>user-position/delete-mul-user-position')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>                    
                      <th class="width8">Sr.No.</th>
                      <th>User </th>
                      <th>Employee Position</th>
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
                    <?php
                      $srno=1;
                      foreach($userpositiondata as $row){?>
                      <tr id="tr<?php echo $row['id']; ?>">
                        <td><?php echo $srno; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['positionid2']; ?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>user-position/user-position-edit/<?php echo $row['userid']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                            

                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            
                              <a class="<?=delete_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['userid']; ?>,'<?php echo ADMIN_URL; ?>user-position/check-user-position-use','User-role','<?php echo ADMIN_URL; ?>user-position/delete-mul-user-position')"><?=delete_text;?></a>
                            
                              
                          <?php } ?>
                         
                        </td>  
                        <td>
                          <div class="checkbox">
                            <input id="deletecheck<?php echo $row['userid']; ?>" onchange="singlecheck(this.id)" type="checkbox" value="<?php echo $row['userid']; ?>" name="deletecheck<?php echo $row['userid']; ?>" class="checkradios">
                            <label for="deletecheck<?php echo $row['userid']; ?>"></label>
                          </div>
                        </td>
                      </tr>
                      <?php $srno++; } ?>
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