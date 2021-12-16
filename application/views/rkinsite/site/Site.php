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
                
                <div class="col-md-6 ResponsivePaddingNone">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>site/add-site" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>site/check-site-use','Site','<?php echo ADMIN_URL; ?>site/delete-mul-site')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelSite()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                  <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFSite()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                  <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printSiteDetails()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="sitetable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr. No.</th>
                      <th>Site</th>
                      <th>Site Manager</th>
                      <th>Address</th>
                      <th>City</th>
                      <th>Entry Date</th>
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
                    if (!empty($sitedata)) {
                      $srno = 1;
                      foreach ($sitedata as $row) { ?>
                        <tr>
                          <td><?php echo $srno; ?></td>
                          <td><?php echo $row['sitename']; ?></td>
                          <td>
                            <?php 
                              $sitemanager = array();
                              $sitemanageridarray = explode(",", $row['sitemanagerid']);
                              $sitemanagernamearray = explode(",", $row['sitemanagername']);
                              if(!empty($sitemanageridarray)){
                                foreach($sitemanageridarray as $key=>$sitemanagerid){
                                  $sitemanager[] = '<a href="'.ADMIN_URL.'party/view-party/'.$sitemanagerid.'#personaldetails" target="_blank">'.$sitemanagernamearray[$key].'</a>'; 
                                }
                              }
                              echo implode(", ",$sitemanager);
                            ?>
                          </td>
                          <td><?php echo $row['address']; ?></td>
                          <td><?php echo $row['cityname']; ?></td>
                          <td><?php echo $this->general_model->displaydatetime($row['createddate']); ?></td>
                          <td>
                            <?php if (strpos($submenuvisibility['submenuedit'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
                              <a class="<?= edit_class; ?> m-n" href="<?= ADMIN_URL ?>site/edit-site/<?php echo $row['id']; ?>" title=<?= edit_title ?>><?= edit_text; ?></a>
                              <?php if ($row['status'] == 1) { ?>
                                <span id="span<?= $row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?= $row['id']; ?>,'<?= ADMIN_URL; ?>site/site-enable-disable','<?= disable_title ?>','<?= disable_class ?>','<?= enable_class ?>','<?= disable_title ?>','<?= enable_title ?>','<?= disable_text ?>','<?= enable_text ?>')" class="<?= disable_class ?> m-n" title="<?= disable_title ?>"><?= stripslashes(disable_text) ?></a></span>
                              <?php } else { ?>
                                <span id="span<?= $row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?= $row['id']; ?>,'<?= ADMIN_URL; ?>site/site-enable-disable','<?= enable_title ?>','<?= disable_class ?>','<?= enable_class ?>','<?= disable_title ?>','<?= enable_title ?>','<?= disable_text ?>','<?= enable_text ?>')" class="<?= enable_class ?> m-n" title="<?= enable_title ?>"><?= stripslashes(enable_text) ?></a></span>
                              <?php } ?>
                            <?php }
                            if (strpos($submenuvisibility['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
                              <a class="<?= delete_class; ?> m-n" href="javascript:void(0)" title=<?= delete_title ?> onclick="deleterow(<?= $row['id']; ?>,'<?php echo ADMIN_URL; ?>site/check-site-use','Site','<?php echo ADMIN_URL; ?>site/delete-mul-site')"><?= delete_text; ?></a>
                            <?php } ?>
                          </td>
                          <td>
                            <div class="checkbox">
                              <input id="deletecheck<?php echo $row['id']; ?>" onchange="singlecheck(this.id)" type="checkbox" value="<?php echo $row['id']; ?>" name="deletecheck<?php echo $row['id']; ?>" class="checkradios">
                              <label for="deletecheck<?php echo $row['id']; ?>"></label>
                            </div>
                          </td>
                        </tr>
                    <?php $srno++;
                      }
                    } ?>
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