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
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>manage-website-content/add-manage-website-content" title=<?=addbtn_title?>><?=addbtn_text;?></a> 
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Manage Website Content','<?php echo ADMIN_URL; ?>manage-website-content/delete_mul_manage_website_content')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>                
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="managewebsitecontent" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr> 
                      <th class="width8">Sr. No.</th>
                      <th>Title</th>
                      <th>Description</th>
                      <th>Entry Date</th>
                      <th class="width12">Action</th>
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
                      foreach($managewebsitecontentdata as $row){?>
                      <tr id="tr<?php echo $row['id']; ?>">
                       
                        <td><?php echo $srno; ?></td>
                        
                        <td><?php echo $row['title'] ?></td>
                        <td>
                          <button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="getcontent(<?=$row['id']; ?>)">View Content</button>
                        </td>
                        <td><?php echo $this->general_model->displaydatetime($row['createddate']); ?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>manage-website-content/edit-manage-website-content/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=delete_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'','Manage Website Content','<?php echo ADMIN_URL; ?>manage_website_content/delete_mul_manage_website_content')"><?=delete_text;?></a>
                          <?php } if($row['status']==1){ ?>
                          <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>manage-website-content/manage_website_content_enable_disable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?> m-n" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                          <?php }else{ ?>
                          <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>manage-website-content/manage_website_content_enable_disable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?> m-n" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
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

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 950px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
        <h4 class="modal-title" id="pagetitle"></h4>
      </div>
      <div class="modal-body">
          <div id="description"></div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
function getcontent(id){

  var uurl = SITE_URL+"manage-website-content/getcontentbyid";
  $.ajax({
    url: uurl,
    type: 'POST',
    data: {id:String(id)},
    async: false,
    success: function(response){
      var JSONObject = JSON.parse(response);
      
      $('#pagetitle').html(JSONObject['pagetitle']);
      $('#description').html(JSONObject['description'].replace(/&nbsp;/g, ' '));
    },
    error: function(xhr) {
    //alert(xhr.responseText);
    },
  });
}
</script>


