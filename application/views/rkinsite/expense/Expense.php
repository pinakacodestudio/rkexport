<style>
  

</style>
<div class="page-content">
    <div class="page-heading">     
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default border-panel mb-md" data-widget="{&quot;draggable&quot;: &quot;false&quot;}" data-widget-static style="visibility: visible; opacity: 1; display: block; transform: translateY(0px);z-index: 9;">
                <div class="panel-heading filter-panel border-filter-heading">
                    <h2><?=APPLY_FILTER?></h2>
                    <div class="panel-ctrls" data-actions-container data-action-collapse="{&quot;target&quot;: &quot;.panelcollapse&quot;}" style="float:right;"><span class="button-icon has-bg"><span class="material-icons">keyboard_arrow_down</span></span></div>
                </div>
                <div class="panel-body panelcollapse pt-n" style="display: none;">
                  <form action="#" class="form-horizontal">
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="startdate" class="control-label">Date</label>
                            <div class="input-daterange input-group" id="datepicker-range">
                              <input type="text" class="input-small form-control" name="fromdate" id="fromdate" value="<?php echo $this->general_model->displaydate(date("y-m-d",strtotime("-1 month"))); ?>" placeholder="Start Date" title="Start Date" readonly/>
                              <span class="input-group-addon">to</span>
                              <input type="text" class="input-small form-control" name="todate" id="todate" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" placeholder="End Date" title="End Date" readonly/>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="status" class="control-label"> Status</label>
                            <select id="filterstatus" name="filterstatus" class="selectpicker form-control" data-select-on-tab="true" data-size="5">
                              <option value="-1">Select Status</option>
                              <option value="0">Pendding</option>
                              <option value="1">Approve</option>
                              <option value="2">Reject</option>
                            </select>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <div class="col-sm-12">
                            <label for="status" class="control-label"> Employee</label>
                            <select id="filteremployee" name="filteremployee" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" >
                              <option value="-1">Select Employee</option>
                                <?php
              								    foreach ($employee_data as $_v) 
              								    {
										              ?>
										                  <option value="<?php echo $_v['id'];?>"  <?php if(!empty($expense_data))
                							        {if($expense_data['employeeid']==$_v['id']){echo "selected";}} ?> >
                						          <?php echo $_v['name'];?></option>
            							            <?php
              								        };
                                      ?>
                                     <!-- <option value="-1" <?php if(!is_null($this->session->userdata("expenseemployeefilter")) && $this->session->userdata("expenseemployeefilter")=="-1"){ echo "selected"; } ?>>All</option> -->
                            </select>
                          </div>
                        </div>
                      </div>
                      
                      <div class="col-md-3">
                        <div class="form-group" style="margin-top: 39px;">
                          <div class="col-sm-12">
                            <label class="control-label"></label>
                            <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="applyFilter()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                          </div>
                        </div>
                      </div>
                    </div> 
                  </form>
                </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                <div class="col-md-6">
                  <div class="panel-ctrls panel-tbl"></div>
                </div>
               <div class="col-md-6 form-group" style="text-align: right;">
               <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL; ?>expense/add-expense" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>expense/check-expense-use','Expense','<?php echo ADMIN_URL; ?>expense/delete-mulexpense')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                    
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="expense" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th class="width18">Employee</th>
                      <th>Category</th>
                      <th>Date</th>
                      <th>Amount</th>
                      <th>Remarks</th>
                      <th>Status</th> 
                      <th >Action</th>
                      <th class="width5">
                                <div class="checkbox table-checkbox">
                                    <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                                    <label for="deletecheckall"></label>
                                </div>
                            </th> 
                    </tr>
                  </thead>
                  <tbody>
                  <!-- <tr>            
                      <td class="width8">1</td>
                      <td>Product 1</td>
                      <td>p1</td>
                      <td>100</td>
                      <td>50</td>
                      <td>12.00%</td>
                      <td> 
                            <button class="btn btn-warning '.STATUS_DROPDOWN_BTN.' btn-raised dropdown-toggle" data-toggle="dropdown" id="btndropdown'.$Productreview->id.'">Pending <span class="caret"></span></button>
                                <ul class="dropdown-menu dropdownmenu" role="menu">
                                      <li id="dropdown-menu">
                                        <a >Approved</a>
                                      </li>
                                      <li id="dropdown-menu">
                                        <a >Cancel</a>
                                      </li>
                                </ul>
                      </td>
                      <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>store-location/edit-store-location/<?=$row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                            <a class="<?=delete_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'','Store Location','<?=ADMIN_URL; ?>store_location/delete_mul_store_location')"><?=delete_text;?></a>
                              
                            <?php } ?>
                            <a class="<?=exportbtn_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> ><?=exportbtn_icon;?></a>

                        </td>
                        <td>
                          <div class="checkbox">
                            <input id="deletecheck<?=$row['id']; ?>" onchange="singlecheck(this.id)" type="checkbox" value="<?=$row['id']; ?>" name="deletecheck<?=$row['id']; ?>" class="checkradios">
                            <label for="deletecheck<?=$row['id']; ?>"></label>
                          </div>
                        </td>
                    </tr>
                   -->
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


