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
									<div class="panel-ctrls panel-tbl"></div>
								</div>
                    			<div class="col-md-6 form-group" style="text-align: right;">
									<?php if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
										<a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>insurance-agent/add-insurance-agent/<?=$this->uri->segment(3);?>" title=<?=addbtn_title?>><?=addbtn_text;?></a>
									<?php }
									if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
										<a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>insurance-agent/check-insurance-agent-use','Insurance agent','<?php echo ADMIN_URL; ?>insurance-agent/delete-mul-insurance-agent')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
									<?php } ?>
                    			</div>
                  			</div>
                  			<div class="panel-body no-padding">
                    			<table id="insuranceagenttable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      				<thead>
                        				<tr>
											<th class="width8">Sr. No.</th>
											<th>Insurance Company</th>
											<th>Agent Name</th>
											<th>Email</th>
											<th>Mobile No.</th>
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
										if (!empty($insuranceagentdata)) {
											$srno = 1;
											foreach ($insuranceagentdata as $row) { ?>
											<tr>
												<td><?php echo $srno; ?></td>
												<td><?php echo $row['insurancecompany']; ?></td>
												<td><?php echo $row['agentname']; ?></td>
												<td><?php echo $row['email']; ?></td>
												<td><?php echo $row['mobileno']; ?></td>
												<td><?php echo $this->general_model->displaydatetime($row['createddate']); ?></td>
												<td>
												<?php if (strpos($submenuvisibility['submenuedit'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
													<a class="<?= edit_class; ?> m-n" href="<?= ADMIN_URL ?>insurance-agent/edit-insurance-agent/<?php echo $row['id']; ?>" title=<?= edit_title ?>><?= edit_text; ?></a>
													<?php if ($row['status'] == 1) { ?>
													<span id="span<?= $row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?= $row['id']; ?>,'<?= ADMIN_URL; ?>insurance-agent/insurance-agent-enable-disable','<?= disable_title ?>','<?= disable_class ?>','<?= enable_class ?>','<?= disable_title ?>','<?= enable_title ?>','<?= disable_text ?>','<?= enable_text ?>')" class="<?= disable_class ?> m-n" title="<?= disable_title ?>"><?= stripslashes(disable_text) ?></a></span>
													<?php } else { ?>
													<span id="span<?= $row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?= $row['id']; ?>,'<?= ADMIN_URL; ?>insurance-agent/insurance-agent-enable-disable','<?= enable_title ?>','<?= disable_class ?>','<?= enable_class ?>','<?= disable_title ?>','<?= enable_title ?>','<?= disable_text ?>','<?= enable_text ?>')" class="<?= enable_class ?> m-n" title="<?= enable_title ?>"><?= stripslashes(enable_text) ?></a></span>
													<?php } ?>
												<?php }
												if (strpos($submenuvisibility['submenudelete'], ',' . $this->session->userdata[base_url() . 'ADMINUSERTYPE'] . ',') !== false) { ?>
													<a class="<?= delete_class; ?> m-n" href="javascript:void(0)" title=<?= delete_title ?> onclick="deleterow(<?= $row['id']; ?>,'<?php echo ADMIN_URL; ?>insurance-agent/check-insurance-agent-use','Insurance agent','<?php echo ADMIN_URL; ?>insurance-agent/delete-mul-insurance-agent')"><?= delete_text; ?></a>
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