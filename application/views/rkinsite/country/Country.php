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
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>country/country-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="countrytable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr.No.</th>
                      <th>Country Name</th>
                      <th>Sort Name</th>
                      <th>Phone Code</th>
                      <th class="width8">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $srno=1;
                      foreach($countrydata as $row){ ?>
                      <tr id="tr<?php echo $row['id']; ?>">
                        <td><?php echo $srno; ?></td>
                        <td><?php echo $row['name'] ?></td>
                        <td><?php echo $row['sortname'] ?></td>
                        <td><?php echo $row['phonecode'] ?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){?>
                              <a class="<?=edit_class;?>" href="<?=ADMIN_URL?>country/country-edit/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php } ?>
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