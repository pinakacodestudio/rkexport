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
                    if (strpos(trim($submenuvisibility['submenuadd'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>home-banner/home-banner-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos(trim($submenuvisibility['submenudelete'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>home-banner/check-home-banner-use','Home Banner','<?php echo ADMIN_URL; ?>home-banner/delete-mul-home-banner')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="homebannertable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>         
                      <th class="width5">Sr.No.</th>
                      <th>Channel Name</th>
                      <th>Product Name</th>
                      <th>Image</th>
                      <th class="width8 text-right">Priority</th>
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
                      
                      foreach($homebannerdata as $row){?>
                      <tr id="<?php echo $row['id']; ?>">
                        
                        <td id="srno">
                          <?=$srno; ?>
                        </td>
                        <td><?php 

                          $channelnamearr = array();  
                          $channelidarr = (!empty($row['channelid']))?explode(",", $row['channelid']):'';
                          foreach($channelidarr as $channelid){

                            $key = array_search($channelid, array_column($channeldata, 'id'));
                            if(!empty($channeldata) && isset($channeldata[$key])){
                                $channellabel = '<span class="label" style="background:'.$channeldata[$key]['color'].'">'.substr($channeldata[$key]['name'], 0, 1).'</span> ';
                            }
                            $channelnamearr[] = $channellabel.$channeldata[$key]['name'];
                          }
                          echo implode(" | ", $channelnamearr);
                        ?></td>
                        <td><?php echo $row['productname']; ?></td>
                        <td>
                           <?php 
                            if($row['image']!=''){
                              echo '<img src="'.HOMEBANNER.$row['image'].'" class="thumbwidth">';  
                            }
                           ?>
                        </td>
                        <td class="text-right"><?php echo $row['inorder']; ?></td>
                        <td>
                          <?php if(strpos(trim($submenuvisibility['submenuedit'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){ ?>
                          <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>home-banner/home-banner-edit/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php }if(strpos(trim($submenuvisibility['submenudelete'],','),$this->session->userdata[base_url().'ADMINUSERTYPE']) !== false){ ?>
                          <a class="<?=delete_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'<?php echo ADMIN_URL; ?>home-banner/check-home-banner-use','home-banner','<?php echo ADMIN_URL; ?>home-banner/delete-mul-home-banner')"><?=delete_text;?></a>
                          <?php } if($row['status']==1){ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>home-banner/home-banner-enable-disable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?> m-n" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                            <?php }else{ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>home-banner/home-banner-enable-disable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?> m-n" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
                            <?php } ?>
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