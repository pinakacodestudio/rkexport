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
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>member-role/member-role-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>member-role/check-member-role-use','<?=Member_label?> Role','<?php echo ADMIN_URL; ?>member-role/delete-mul-member-role')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>                    
                      <th class="width8">Sr.No.</th>
                      <th><?=Member_label?> Role</th>
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
                      foreach($memberroledata as $row){?>
                      <tr id="tr<?php echo $row['id']; ?>">
                        <td><?php echo $srno; ?></td>
                        <td><?php echo $row['role']; ?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=edit_class;?>" href="<?=ADMIN_URL?>member-role/member-role-edit/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                            <?php if($row['status']==1){ ?>
                            
                              <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>member-role/member-role-enable-disable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?>" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                            
                            <?php }else{ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>member-role/member-role-enable-disable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?>" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
                            <?php } ?>

                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            
                              <a class="<?=delete_class;?>" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'<?php echo ADMIN_URL; ?>member-role/check-member-role-use','<?=Member_label?> Role','<?php echo ADMIN_URL; ?>member-role/delete-mul-member-role')"><?=delete_text;?></a>
                           
                          <?php } ?>
                          
                          <a class="<?=duplicatebtn_class;?>" href="<?=ADMIN_URL?>member-role/member-role-add/<?php echo $row['id']; ?>" title=<?=duplicatebtn_title?>><?=duplicatebtn_text;?></a>

                        </td>  
                        <td>
                          <div class="checkbox">
                            <input id="deletecheck<?php echo $row['id']; ?>" onchange="singlecheck(this.id)" type="checkbox" value="<?php echo $row['id']; ?>" name="deletecheck<?php echo $row['id']; ?>" class="checkradios">
                            <label for="deletecheck<?php echo $row['id']; ?>"></label>
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