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
                  <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL; ?>Blog/add-blog" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Blog','<?=ADMIN_URL; ?>blog/deletemulblog')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="blogtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr. No.</th>
                      <th>Title</th>
                      <th>Image</th>
                      <th>Category</th>
                      <th>Description</th>
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
                      foreach($blogdata as $row){?>
                      <tr id="tr<?=$row['id']; ?>">
                        <td><?=$srno; ?></td>
                        <td><?=$row['title'] ?></td>
                        
                        <td>
                          <?php if($row['image']!='') { ?>
                            <img src="<?=BLOG.$row['image']?>" class="img-thumbnail">
                          <? }else{ ?>  
                            <img src="<?=DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW.$row['image']?>" class='thumbwidth'>
                        <? }?>
                        </td>
                      
                        <td><?=$row['category'] ?></td>
                        <td>
                            <?php if(!empty($row['description'])){?>
                              <div id="description<?=$row['id']?>" style="display:none"><?=$row['description'] ?></div>
                              <button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="displaydescription(<?=$row['id']?>)">View Description</button>
                            <?php } ?>
                        </td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>blog/edit-blog/<?=$row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=delete_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'','Blog','<?=ADMIN_URL; ?>blog/deletemulblog')"><?=delete_text;?></a>
                          <?php } if($row['status']==1){ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>blog/blogenabledisable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?> m-n" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                            <?php }else{ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>blog/blogenabledisable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?> m-n" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
                            <?php } ?>
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
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 950px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
        <h4 class="modal-title">Blog Description</h4>
      </div>
      <div class="modal-body">
              
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>