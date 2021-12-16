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
                  <a class="<?=addbtn_class;?>" href="<?php ADMIN_URL; ?>Testimonials/add-testimonials" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  <a class="<?=deletebtn_class;?>" href="javascript:void(0)" onclick="checkmultipledelete('','Testimonials','<?php ADMIN_URL; ?>testimonials/delete-mul-testimonials')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="testimonialstable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>                    
                      <th class="width8">Sr.No.</th>
                      <th>Name</th>
                      <th>Testimonials</th>
                      <th>Image</th>
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
                      foreach($testimonialsdata as $row){?>
                      <tr id="<?=$row['id']; ?>">
                        <td id="srno">
                          <?=$srno; ?>
                        </td>
                        <td><?=ucwords($row['name']) ?></td>
                        <td><?=$row['testimonials'] ?></td>
                         
                        <td><?php if($row['image']){ echo "<img src='".TESTIMONIALS.$row['image']."' class='thumbwidth'>"; }
                             else{ if(empty($row['image'])) { echo "<img src='".DEFAULT_IMG.DEFAULT_IMAGE_PREVIEW.$row['image']."' class='thumbwidth'>"; }}
                        ?></td>  
                        <td>
                        <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                        <a class="<?=edit_class;?>" href="<?=ADMIN_URL?>testimonials/edit-testimonials/<?=$row['id']; ?>" title=<?=edit_title?>><?=edit_text;?></a>
                        <?php }if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                          <a class="<?=delete_class;?>" href="javascript:void(0)" title=<?=delete_title?> onclick="deleterow(<?=$row['id']; ?>,'','testimonials','<?=ADMIN_URL; ?>testimonials/delete-mul-testimonials')"><?=delete_text;?></a>
                        <?php } if($row['status']==1){ ?>
                          <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(0,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>testimonials/testimonials-enable-disable','<?=disable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=disable_class?>" title="<?=disable_title?>"><?=stripslashes(disable_text)?></a></span>
                          <?php }else{ ?>
                          <span id="span<?=$row['id']; ?>"><a href="javascript:void(0)" onclick="enabledisable(1,<?=$row['id']; ?>,'<?=ADMIN_URL; ?>testimonials/testimonials-enable-disable','<?=enable_title?>','<?=disable_class?>','<?=enable_class?>','<?=disable_title?>','<?=enable_title?>','<?=disable_text?>','<?=enable_text?>')" class="<?=enable_class?>" title="<?=enable_title?>"><?=stripslashes(enable_text)?></a></span>
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

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->