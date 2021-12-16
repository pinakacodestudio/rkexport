<div class="page-content">
    <div class="page-heading"> 
      <div class="btn-group dropdown dropdown-l dropdown-breadcrumbs">
        <a class="dropdown-toggle dropdown-toggle-style" data-toggle="dropdown" aria-expanded="false"><span>
            <i class="material-icons" style="font-size: 26px;">menu</i>
          </span> </a>
        <ul class="dropdown-menu dropdown-tl" role="menu">
        <label class="mt-sm ml-sm mb-n">Menu</label>
          <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
              if($subid == $row['id']){ ?>
                
                <li class="active"><a href="javascript:void(0);"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
              
              <?php }else{ ?>
                <li><a href="<?=base_url().ADMINFOLDER.$row['url']; ?>"><i class="fa fa-caret-right mr-sm"></i> <?=$row['name']; ?></a></li>
              <?php } 
            } ?>
        </ul>
      </div>            
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
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                
                <div class="col-md-6">
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>frontendmainmenu/add-frontend-main-menu" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>frontendmainmenu/checkfrontendmainmenuuse','Frontend Main Menu','<?php echo ADMIN_URL; ?>frontendmainmenu/deletemulfrontendmainmenu')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                  <a class="<?=orderbtn_class;?>" href="javascript:void(0)" onclick="setorder('<?=ADMIN_URL; ?>frontendmainmenu/updatepriority','#frontendmainmenu')" id="btntype" title="<?=orderbtn_title?>"><?=orderbtn_text;?></a>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="frontendmainmenu" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th class="width8">Sr.No.</th>
                      <th>Name</th>
                      <th>Menu Icon</th>
                      <th>Menu URL</th>
                      <th>Cover Image</th>
                      <th class="width8">Priority</th>
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
                      foreach($frontendmenudata as $row){ ?>
                      <tr id="<?php echo $row['id']; ?>">
                        <td><?php echo $srno; ?></td>
                        <td><?php echo $row['name'] ?></td>
                        <td><?php echo $row['menuicon'] ?></td>
                        <td><?php echo urldecode($row['url']) ?></td>
                        <td><?php if($row['coverimage']){ echo "<img src='".FRONTMENU_COVER_IMAGE.$row['coverimage']."' class='thumbwidth'>"; }
                             else{ if(empty($row['coverimage'])) { echo "<img src='".DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW.$row['coverimage']."' class='thumbwidth'>"; }}
                        ?></td> 
                        <td><?php echo $row['priority'] ?></td>
                        <td>
                          <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=edit_class;?>" href="<?=ADMIN_URL?>frontendmainmenu/edit-frontend-main-menu/<?php echo $row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                          <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=delete_class;?>" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'<?php echo ADMIN_URL; ?>frontendmainmenu/checkfrontendmainmenuuse','Frontend Main Menu','<?php echo ADMIN_URL; ?>frontendmainmenu/deletemulfrontendmainmenu')"><?=delete_text;?></a>
                          <?php } ?>
                          <?php if($row['status']==1){ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>frontendmainmenu/frontendmainmenuenabledisable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?>" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                          <?php }else{ ?>
                            <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>frontendmainmenu/frontendmainmenuenabledisable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?>" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
                          <?php } ?>
                        </td>
                        <td>
                          <span style="display: none;"><?=$row['priority'] ?></span>
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