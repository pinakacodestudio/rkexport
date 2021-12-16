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

            <div class="panel panel-default">
              <div class="panel-heading">
                <h2 style="font-size: 18px;">Payment Gateway Details</h2>
                <div class="panel-ctrls p" style="float: right">
                  <?php if((strpos($submenuvisibility['submenuedit'],','.$this->session->userdata(base_url().'ADMINUSERTYPE').',')) !== false) { ?>
                    <a href="<?=ADMIN_URL?>paymentgateway/paymentgatewayedit" class="btn btn-primary btn-raised btn-label">Edit</a>
                  <? } ?>
                </div>
              </div>
              <div class="l-box l-spaced-bottom">
                <div class="l-box-body l-spaced" style="padding-left: 30px;padding-right: 30px;">
                    <table class="table table-striped">
                      <tbody>
                        <tr>
                          <td class="col-md-2">Merchant ID</td>
                          <td><?=(!empty($paymentgatewaydata['merchantid']))?$paymentgatewaydata['merchantid']:''; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-2">Merchant Key</td>
                          <td><?=(!empty($paymentgatewaydata['merchantkey']))?$paymentgatewaydata['merchantkey']:''; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-2">Merchant Salt</td>
                          <td><?=(!empty($paymentgatewaydata['merchantsalt']))?$paymentgatewaydata['merchantsalt']:''; ?></td> 
                        </tr>
                        <tr>
                          <td class="col-md-2">Authheader</td>
                          <td><?=(!empty($paymentgatewaydata['authheader']))?$paymentgatewaydata['authheader']:''; ?></td> 
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
</div>