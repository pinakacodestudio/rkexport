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
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>email-format/email-format-add" title=<?=addbtn_title?>><?=addbtn_text;?></a> 
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Email Format','<?php echo ADMIN_URL; ?>email-format/delete-mul-email-format')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>                  
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="emailformat" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>  
                      <th class="width8">Sr.No.</th>
                      <th>Mail</th>
                      <th>Mail Subject</th>
                      <th>Description</th>
                      <th>Date</th>
                      <th class="width10">Action</th>
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
                      foreach($emailformatdata as $row){?>
                      <tr id="tr<?php echo $row['id']; ?>">
                       
                        <td><?php echo $srno; ?></td>
                        
                        <td><?php foreach($this->Emailformattype as $mfdid => $mfdvalue){
                          if($mfdid==$row['mailid'])
                          {
                            echo $mfdvalue;
                          }
                        } ?></td>
                        <td><?=$row['subject']?></td>
                        
                        <td>
                          <?php if(!empty($row['emailbody'])){?>
                            <button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="getmailbody(<?=$row['id']; ?>)">View Email Format</button>
                          <?php } ?>
                        </td>
                        <td><?php echo $this->general_model->displaydatetime($row['createddate']); ?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>email-format/email-format-edit/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=delete_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'','Email Format','<?php echo ADMIN_URL; ?>email-format/delete-mul-email-format')"><?=delete_text;?></a>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><i class="fa fa-times"></i></span></button>
        <h4 class="modal-title" id="emailsubject"></h4>
      </div>
      <div class="modal-body">

      <div class="panel-body no-padding">
              <form id="prostock" class="form-horizontal" >
                <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <div class="col-md-12">
                    
                    </div>
                  </thead>
                  <tbody>
                    <div id="emailbody"></div>
                      
                  </tbody>
                </table>
              </form>
              </div>
              
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
 function getmailbody(id){
      // var productid = $('#productid').val();
      
      
      var uurl = SITE_URL+"email-format/getmailbodybyid";
        $.ajax({
          url: uurl,
          type: 'POST',
          data: {id:String(id)},
          // dataType: 'json',
          async: false,
          success: function(response){
              var JSONObject = JSON.parse(response);
            
              // alert(JSONObject);
              $('#emailsubject').html(JSONObject['subject']);
            $('#emailbody').html(JSONObject['emailbody']);
            

         
          },
          error: function(xhr) {
          //alert(xhr.responseText);
          },
        });
    }
</script>


