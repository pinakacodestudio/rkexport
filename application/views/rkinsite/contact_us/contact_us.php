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
                  <!-- <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>contact-us/contact-us-add" title=<?=addbtn_title?>><?=addbtn_text;?></a> -->
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php ADMIN_URL; ?>contact-us/check-contact-us-use','Contact Us','<?php ADMIN_URL; ?>contact-us/delete-mul-contact-us')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="contactustable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                      <th class="width8">Sr. No.</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone No.</th>
                      <th>Message</th>
                      <th>Entry Date</th>
                      <th class="width5">Action</th>
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
                      foreach($contactusdata as $row){?>
                      <tr id="<?=$row['id']; ?>">
                        <td id="srno">
                          <?=$srno; ?>
                        </td>
                        <td><?=ucwords($row['customername']) ?></td>
                        <td><?=$row['customeremail'] ?></td>
                        <td><?=$row['customerphone'] ?></td>
                        <td>
                          <div id="message<?=$row['id']?>" style="display:none"><?=$row['customerfeedback'] ?></div>
                          <button class="btn btn-inverse btn-raised btn-sm" data-toggle="modal" data-target="#myModal" onclick="viewmessage(<?=$row['id']?>)">View Message</button>  
                        </td>
                        <td><?php echo $this->general_model->displaydatetime($row['createddate']); ?></td>                        
                        <td> 
                          <?php if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=delete_class;?>" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'','Contact Us','<?=ADMIN_URL; ?>contact-us/delete-mul-contact-us')"><?=delete_text;?></a>
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

      <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document" style="width: 600px;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">Message</h4>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer"></div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->