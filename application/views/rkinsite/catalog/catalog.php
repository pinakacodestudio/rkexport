<div class="page-content">
    <ol class="breadcrumb">                        
        <?php
            $subid = $this->session->userdata(base_url().'submenuid');
            foreach($subnavtabsmenu as $row){
                if($subid == $row['id']){
          ?>
          <li class="active"><a href="javascript:void(0);"><?=$row['name']; ?></a></li>
          <?php }else{ ?>
          <li class=""><a href="<?=base_url().$row['url']; ?>"><?=$row['name']; ?></a></li>
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
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
                <div class="col-md-12">
                  <div class="pull-right">
                    <?php if(!empty($channeldata)){ 
                        foreach($channeldata as $channel){?>
                          <span class="label" style="background:<?=$channel['color']?>"><?=substr($channel['name'], 0, 1);?></span> <?=$channel['name']?>
                          
                    <?php } } ?>
                  </div> 
                </div>
                <div class="col-md-6">
                  <div class="panel-ctrls"></div>
                </div>
                <div class="col-md-6 form-group" style="text-align: right;">
                  <?php 
                    if (strpos($submenuvisibility['submenuadd'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){ ?>
                      <a class="<?=addbtn_class;?>" href="<?=ADMIN_URL?>catalog/addcatalog" title=<?=addbtn_title?>><?=addbtn_text;?></a>
                  <?php
                    } ?>
                    <?php
                    if(strpos($submenuvisibility['submenudelete'],','.$this->session->userdata[base_url().'ADMINUSERTYPE'].',') !== false){
                  ?>
                  &nbsp;<a class="<?=deletebtn_class;?> delete-btn-align" href="javascript:void(0)" onclick="checkmultipledelete('<?php echo ADMIN_URL; ?>catalog/checkcataloguse','Attribute','<?php echo ADMIN_URL; ?>catalog/deletemulcatalog')" title=<?=deletebtn_title?>><?=deletebtn_text;?></a>
                  <?php } ?>
                </div>
              </div>
              <div class="panel-body no-padding">
                <table id="catalog" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead>
                    <tr>            
                      <th>Sr. No.</th>
                      <th>Name</th>  
                      <th>Date</th>
                      <th width="25%">Actions</th>
                      <th class="width8">
                        <div class="checkbox">
                          <input id="deletecheckall" onchange="allchecked()" type="checkbox" value="all">
                          <label for="deletecheckall"></label>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
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


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 950px;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" >Catalog Detail</h4>
      </div>
      <div class="modal-body">
          <div class="about-area col-md-12">
                <div class="table-responsive">
                  <table class="table">
                    <tr>
                    <th width="25%">Name </th>
                    <td><div id="catalogname"></div> </td>
                    </tr>
                   
                    <tr>
                    <th>Description</th>
                    <td><div id="catalogdescription"></div></td>
                    </tr>
                    <tr>
                   
                    <tr>
                    <th>Image</th>
                    <td> <img src="#" id="catalogimage" width="150px" /></td>
                    </tr>
                    <tr>
                    <th>PDF</th>
                    <td>
                        <a href="#" class="btn btn-raised btn-sm btn-info" id="catalogpdffile" download><i class="fa fa-download"></i> Download</a></td>
                    </tr>
                    <tr>
                    <th>Created Date</th>
                    <td><div id="catalogcreateddate"></div></td>
                    </tr>
                    <tr>
                    <th>Status</th>
                    <td>
                        <span class="btn btn-xs btn-raised" id="catalogstatus"></span>
                    </td>
                    </tr>
                  </table>
                </div>
              </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>