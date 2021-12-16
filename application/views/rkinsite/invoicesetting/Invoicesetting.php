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
            <div class="panel panel-default">
              <div class="panel-heading">
                
                <div class="panel-ctrls p" style="float: right">
                  <?php if(strpos($submenuvisibility['submenuedit'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                    <a href="invoicesetting/invoicesettingedit" class="btn btn-primary btn-raised btn-label">Edit</a>
                  <? } ?>  
                </div>
              </div>
              <div class="l-box l-spaced-bottom">
                <div class="l-box-body l-spaced" style="padding-left: 30px;padding-right: 30px;">
                    <table class="table table-striped">
                      <tbody>
                        <tr>
                          <td class="col-md-2">Business Name</td>
                          <td><?=(count($invoicesettingdata)>0)?$invoicesettingdata['businessname']:'-'; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-2">Business Address</td>
                          <td><?=(count($invoicesettingdata)>0)?$invoicesettingdata['businessaddress']:'-'; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-2">Email</td>
                          <td><?=(count($invoicesettingdata)>0)?$invoicesettingdata['email']:'-'; ?></td>
                        </tr>
                        <tr>
                          <td>GST NO.</td>
                          <td><?=(count($invoicesettingdata)>0)?$invoicesettingdata['gstno']:'-'; ?></td>
                        </tr>
                      
                        <tr>
                          <td>Logo</td>
                          <td>
                            <?php if(count($invoicesettingdata)>0) { ?>
                              <img class="img-thumbnail" src="<?php echo MAIN_LOGO_IMAGE_URL.$invoicesettingdata['logo']; ?>">
                            <? } ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="col-md-2">Invoice Notes</td>
                          <td><?=(count($invoicesettingdata)>0)?$invoicesettingdata['notes']:'-'; ?></td>
                        </tr>
                       
                      </tbody>
                    </table>
                </div>
              </div>
            </div>
          </div>          
        </div>
      </div>
    </div>    
</div> <!-- #page-content --> 