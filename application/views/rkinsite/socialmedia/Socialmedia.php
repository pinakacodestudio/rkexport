<div class="page-content">
    <ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=base_url().$row['menuurl']; ?>"><?=$row['name']; ?></a></li>
          <?php } } ?>
    </ol>
    <div class="page-heading">            
        <h1><?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
          <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
          </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default">
              <div class="panel-heading">
                
                <div class="col-md-6">
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?php echo ADMIN_URL; ?>Socialmedia/socialmediaadd" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>socialmedia/checksocialmediause','Socialmedia','<?php echo ADMIN_URL; ?>socialmedia/deletemulsocialmedia')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                 
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="socialmediatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>         
                      <th class="width8">Sr.No.</th>
                      <th>Name</th>
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
                      foreach($socialmediadata as $row){?>
                      <tr id="<?php echo $row['id']; ?>">
                        
                        <td id="srno">
                          <?=$srno; ?>
                        </td>
                        <td><?php echo $row['name'] ?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=edit_class;?>" href="<?=ADMIN_URL?>socialmedia/socialmediaedit/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=delete_class;?>" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'<?php echo ADMIN_URL; ?>socialmedia/checksocialmediause','Socialmedia','<?php echo ADMIN_URL; ?>socialmedia/deletemulsocialmedia')"><?=delete_text;?></a>
                          <?php } if($row['status']==1){ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>socialmedia/socialmediaenabledisable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?>" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                            <?php }else{ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>socialmedia/socialmediaenabledisable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?>" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
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