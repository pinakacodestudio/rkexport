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
                  <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>user/user-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>user/check-user-use','User','<?php echo ADMIN_URL; ?>user/delete-mul-user')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="user" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr.No.</th>
                      <th>Name</th>
                      <th>Role</th>
                      <th>Profile Image</th>
                      <th>Email</th>
                      <th>Mobile No.</th>
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
                      foreach($userdata as $row){ ?>
                      <tr id="tr<?=$row['id']; ?>">
                       
                        <td><?=$srno; ?></td>
                        <td><?=ucwords($row['username']) ?></td>
                        <td><?=$row['role'] ?></td>
                        <td>
                          <?php 
                            if($row['image']==''){
                                $profileimg = '<img src="'.DEFAULT_PROFILE.'Male-Avatar.png" class="thumbwidth">';
                            }else{
                              $profileimg = '<img src="'.PROFILE.$row['image'].'" class="thumbwidth">';  
                            }
                            echo $profileimg;
                          ?>
                        </td>
                        <td><?=$row['email'];?></td>
                        <td><?=$row['mobileno'];?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>user/user-edit/<?=$row['id']; ?>" title="<?=edit_title?>"><?=edit_text;?></a>
                          <?php if($row['status']==1){ ?>
                          <?php if($row['id'] == 1 || $row['id'] == $this->session->userdata[base_url().'ADMINID']){ ?>
                            <span><a href="javascript:void(0)" class="btn btn-default btn-raised btn-sm m-n" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                          <?php }else{ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>user/user-enable-disable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?> m-n" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                          <?php } ?>
                          <?php }else{ ?>
                          <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>user/user-enable-disable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?> m-n" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
                          <?php } ?>
                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <?php if($row['id'] == 1 || $row['id'] == $this->session->userdata[base_url().'ADMINID']){ ?>
                          <a href="#" class="btn btn-disabled m-n" style="color: #333;"><?=stripslashes(delete_text);?></a>
                          <?php }else{?>
                          <a href="#" onclick="deleterow(<?=$row['id']; ?>,'<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>user/check-user-use','User','<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>user/delete-mul-user')"  class="<?=delete_class?> m-n" title="<?=delete_title?>"><?=stripslashes(delete_text);?></a>
                          <?php } }?>
                          <a href="<?php echo base_url().ADMINFOLDER?>user/use-view-page/<?=$row['id']?>" class="<?=view_class?> m-n" title="VIEW"><i class="fa fa-eye" aria-hidden="true"></i></a>
                        </td>
                        <td>
                          <div class="checkbox">
                            <input id="deletecheck<?=$row['id']; ?>" onchange="singlecheck(this.id)" type="checkbox" value="<?=$row['id']; ?>" name="deletecheck<?=$row['id']; ?>" class="checkradios">
                            <label for="deletecheck<?=$row['id']; ?>"></label>
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