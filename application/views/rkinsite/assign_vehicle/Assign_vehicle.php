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
                if (strpos($submenuvisibility['submenuadd'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
                ?>
                  <a class="<?= addbtn_class; ?>" href="<?= ADMIN_URL ?>assign-vehicle/add-assign-vehicle/<?= $this->uri->segment(3); ?>" title=<?= addbtn_title ?>><?= addbtn_text; ?></a>
                <?php
                }
                if (strpos($submenuvisibility['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) {
                ?>
                  <a class="<?= deletebtn_class; ?>" href="javascript:void(0)" onclick="checkmultipledelete('assign-vehicle/check-assign-vehicle-use','Assign Vehicle','<?php echo ADMIN_URL; ?>assign-vehicle/delete-mul-assign-vehicle')" title=<?= deletebtn_title ?>><?= deletebtn_text; ?></a>
                <?php } if (in_array("export-to-excel",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportbtn_class;?>" href="javascript:void(0)" onclick="exportToExcelAssignVehicle()" title="<?=exportbtn_title?>"><?=exportbtn_text;?></a>
                <?php } if (in_array("export-to-pdf",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=exportpdfbtn_class;?>" href="javascript:void(0)" onclick="exportToPDFAssignVehicle()" title="<?=exportpdfbtn_title?>"><?=exportpdfbtn_text;?></a>
                <?php } if (in_array("print",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                  <a class="<?=printbtn_class;?>" href="javascript:void(0)" onclick="printAssignVehicle()" title="<?=printbtn_title?>"><?=printbtn_text;?></a>
                <?php } ?>
              </div>
            </div>
            <div class="panel-body no-padding">
              <table id="assignvehicletable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th class="width8">Sr. No.</th>
                    <th>Vehicle Name</th>
                    <th>Site Name</th>
                    <th>Date</th>
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
                  if (!empty($assignvehicledata)) {
                    $srno = 1;
                    foreach ($assignvehicledata as $row) { ?>
                      <tr>
                        <td><?php echo $srno; ?></td>
                        <td><?php echo '<a href="'.ADMIN_URL.'vehicle/view-vehicle/'.$row['vehicleid'].'#assignpartytab" target="_blank">'.$row['vehiclename']." (".$row['vehicleno'].")</a>"; ?></td>
                        <td><?php echo $row['sitename']; ?></td>
                        <td><?php echo $row['date']!="0000-00-00"?$this->general_model->displaydate($row['date']):'-'; ?></td>
                        <td><?php echo $row['createddate']!="0000-00-00"?$this->general_model->displaydatetime($row['createddate']):'-'; ?></td>
                        <td>
                          <?php if (strpos($submenuvisibility['submenuedit'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
                            <a class="<?= edit_class; ?> m-n" href="<?= ADMIN_URL ?>assign-vehicle/edit-assign-vehicle/<?php echo $row['id']; ?>" title=<?= edit_title ?>><?= edit_text; ?></a>
                          <?php }
                          if (strpos($submenuvisibility['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
                            <a class="<?= delete_class; ?> m-n" href="javascript:void(0)" title=<?= delete_title ?> onclick="deleterow(<?= $row['id']; ?>,'<?php echo ADMIN_URL; ?>assign-vehicle/check-assign-vehicle-use','Assign Vehicle','<?php echo ADMIN_URL; ?>assign-vehicle/delete-mul-assign-vehicle')"><?= delete_text; ?></a>
                          <?php }
                          if (strpos($submenuvisibility['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
                            <a class="<?= transferbtn_class; ?> m-n" href="javascript:void(0)" title=<?= transferbtn_title ?> onclick="AssignVehicleModal(<?= $row['vehicleid']; ?>)"><?= transferbtn_text; ?></a>
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
</div><!-- #page-content -->
<?php $this->load->view(ADMINFOLDER.'assign_vehicle/TransferAssignVehicle');?>
