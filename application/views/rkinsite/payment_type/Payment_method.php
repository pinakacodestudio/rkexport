<style>
#displayinapp_div .bootstrap-select{
  margin-top: 0;
}
</style>

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
                
                <div class="col-md-5">
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-4 pr-n">
                  <div class="form-group mt-n" id="displayinapp_div">
                    <label for="displayinapp" class="col-sm-4 control-label pr-n mt-md">Display in App </label>
                    <div class="col-sm-8">
                      <select id="displayinapp" name="displayinapp" class="selectpicker form-control" data-live-search="true">
                        <option value="0">Select Payment Method</option>
                        <?php foreach($this->Paymentgatewaytype as $type=>$typeval){ ?>
                        <option value="<?php echo $type; ?>" <?php if($activeplan == $type){ echo 'selected'; }  ?>><?php echo ucwords($typeval); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-md-3 form-group" style="text-align: right;">
                  <?php if (strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                      <a class="btn btn-primary btn-raised" href="javascript:void(0)" title="UPDATE PLAN" onclick="updateactiveplan()">UPDATE</a>
                  <?php } if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>payment-method/payment-method-add" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php }
                  if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                    <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Payment Method','<?php echo ADMIN_URL; ?>payment-method/delete-mul-payment-method')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="paymentmethodtable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th class="width8">Sr. No.</th>
                      <th>Payment Method</th>
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
                    <?php $srno=1;
                        foreach($paymentmethoddata as $row){?>
                            <tr id="<?php echo $row['id']; ?>">
                                
                                <td id="srno"><?=$srno; ?></td>
                                <td><?php echo $row['name'] ?></td>
                                <td>
                                <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                                    <a class="<?=edit_class;?> m-n" href="<?=ADMIN_URL?>payment-method/payment-method-edit/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                                <?php } if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                                    if($row['displayinfront']==0){ 
                                      
                                      if($row['iscod']==0){?>
                                         <a class="<?=delete_class;?> m-n" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'','Payment Method','<?php echo ADMIN_URL; ?>payment-method/delete-mul-payment-method')"><?=delete_text;?></a>
                                      <?  }else{ ?>
                                        <a class="btn btn-default btn-raised btn-sm m-n" href="javascript:void(0)" title="<?=delete_title?>"><?=delete_text;?></a>
                                      <?  } ?>
                                  <? } ?>
                                <?php } if($row['status']==1){ ?>
                                        <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>payment-method/payment-method-enable-disable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?> m-n" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                                    <?php }else{ ?>
                                        <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>payment-method/payment-method-enable-disable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?> m-n" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
                                    <?php } ?>
                                </td>
                                <td>
                                <?php if($row['displayinfront']==0 && $row['iscod']==0){ ?>
                                    <div class="checkbox">
                                        <input id="deletecheck<?php echo $row['id']; ?>" onchange="singlecheck(this.id)" type="checkbox" value="<?php echo $row['id']; ?>" name="deletecheck<?php echo $row['id']; ?>" class="checkradios">
                                        <label for="deletecheck<?php echo $row['id']; ?>"></label>
                                    </div>
                                <? } ?>
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