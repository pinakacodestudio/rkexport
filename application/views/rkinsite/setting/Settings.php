<style>
table,table thead tr{
  border: 2px solid #e8e8e8 !important;
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
                <h2 style="font-size: 15px;">Portal Details</h2>
                <div class="panel-ctrls p" style="float: right">
                  <a href="<?php echo ADMIN_URL."setting/setting-edit";?>" class="btn btn-primary btn-raised btn-label"><i class="fa fa-edit"></i> Edit</a>
                </div>
              </div>
              <div class="l-box l-spaced-bottom">
                <div class="l-box-body l-spaced">
                  <div class="col-md-6 pr-xs">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th colspan="2" class="text-center">Company Settings</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="col-md-3">Company Name</td>
                          <td><?php echo $settingdata['businessname']; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-3">Company Website</td>
                          <td><?php echo $settingdata['website']; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-3">Email Address</td>
                          <td><?php echo implode(", ", array_column($emaildata, 'email')); ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-3">Mobile Number</td>
                          <td><?php echo implode(" / ", array_column($mobiledata, 'mobileno')); ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-3">Company Address</td>
                          <td><?php echo $settingdata['address']; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-3">City</td>
                          <td><?php echo $settingdata['cityname']; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-3">Province</td>
                          <td><?php echo $settingdata['provincename']; ?></td>
                        </tr>
                        <tr>
                          <td class="col-md-3">Country</td>
                          <td><?php echo $settingdata['countryname']; ?></td>
                        </tr>
                        <tr>
                          <td>Company Favicon Icon</td>
                          <td><img class="img-thumbnail" src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_FAVICON; ?>" title="<?=COMPANY_FAVICON?>"></td>
                        </tr>
                        <tr>
                          <td>Company Logo</td>
                          <td><img class="img-thumbnail" src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_LOGO; ?>" title="<?=COMPANY_FAVICON?>"></td>
                        </tr>
                        <tr>
                          <td>Company Dark Logo</td>
                          <td><img class="img-thumbnail" src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_SMALL_LOGO; ?>" title="<?=COMPANY_SMALL_LOGO?>"></td>
                        </tr>
                        <tr>
                          <td>Product Default Image</td>
                          <td><img class="img-thumbnail" src="<?php echo PRODUCT.PRODUCTDEFAULTIMAGE; ?>" title="<?=PRODUCTDEFAULTIMAGE?>"></td>
                        </tr>
                        <tr>
                          <td>Category Default Image</td>
                          <td><img class="img-thumbnail" src="<?php echo CATEGORY.CATEGORYDEFAULTIMAGE; ?>" title="<?=CATEGORYDEFAULTIMAGE?>"></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
            
                  <div class="col-md-6 pl-xs">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th colspan="2" class="text-center">Order Email Setting</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="col-md-3">Order Email</td>
                          <td style="word-break: break-all;"><?php echo ($settingdata['orderemails']!=""?$settingdata['orderemails']:"-"); ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="col-md-6 pl-xs">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th colspan="2" class="text-center">Color Settings</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="col-md-6">Theme Color</td>
                          <td><?php if($settingdata['themecolor']!=""){ ?>
                              <div style="background: <?=$settingdata['themecolor']?>;" class="statusescolor"></div>
                              <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                        <tr>
                          <td class="col-md-6">Font Color</td>
                          <td><?php if($settingdata['fontcolor']!=""){ ?>
                              <div style="background: <?=$settingdata['fontcolor']?>;" class="statusescolor"></div>
                              <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                        <tr>
                          <td>Footer BG Color</td>
                          <td><?php if($settingdata['footerbgcolor']!=""){ ?>
                              <div style="background: <?=$settingdata['footerbgcolor']?>;" class="statusescolor"></div>
                              <?php }else { echo "-"; } ?></td>
                        </tr>
                        <tr>
                          <td>Link Color</td>
                          <td><?php if($settingdata['linkcolor']!=""){ ?>
                              <div style="background: <?=$settingdata['linkcolor']?>;" class="statusescolor"></div>
                              <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                        <tr>
                          <td>Table Header Color</td>
                          <td>
                            <?php if($settingdata['tableheadercolor']!=""){ ?>
                              <div style="background: <?=$settingdata['tableheadercolor']?>;" class="statusescolor"></div>
                            <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                        <tr>
                          <td>Sidebar BG Color</td>
                          <td><?php if($settingdata['sidebarbgcolor']!=""){ ?>
                              <div style="background: <?=$settingdata['sidebarbgcolor']?>;" class="statusescolor"></div>
                            <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                        <tr>
                          <td>Sidebar Menu Active Color</td>
                          <td>
                            <?php if($settingdata['sidebarmenuactivecolor']!=""){ ?>
                              <div style="background: <?=$settingdata['sidebarmenuactivecolor']?>;" class="statusescolor"></div>
                            <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                        <tr>
                          <td>Sidebar Submenu BG Color</td>
                          <td><?php if($settingdata['sidebarsubmenubgcolor']!=""){ ?>
                              <div style="background: <?=$settingdata['sidebarsubmenubgcolor']?>;" class="statusescolor"></div>
                            <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                        <tr>
                          <td>Sidebar Submenu Active Color</td>
                          <td><?php if($settingdata['sidebarsubmenuactivecolor']!=""){ ?>
                              <div style="background: <?=$settingdata['sidebarsubmenuactivecolor']?>;" class="statusescolor"></div>
                            <?php }else { echo "-"; } ?>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!-- <div class="col-md-6 pl-xs">
                    <table class="table table-striped table-bordered">
                      <thead>
                        <tr>
                          <th colspan="2" class="text-center">Social Links</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="col-md-3">Facebook Link</td>
                          <td><?php echo $settingdata['facebooklink']; ?></td>
                        </tr>
                        <tr>
                          <td>Instagram Link</td>
                          <td><?php echo $settingdata['instagramlink']; ?></td>
                        </tr>
                        <tr>
                          <td>Google Link</td>
                          <td><?php echo $settingdata['googlelink']; ?></td>
                        </tr>
                        <tr>
                          <td>Twitter Link</td>
                          <td><?php echo $settingdata['twitterlink']; ?></td>
                        </tr>
                      </tbody>
                    </table>
                  </div> -->
                </div>
              </div>
            </div>
          </div>          
        </div>
      </div>
    </div>    
</div> <!-- #page-content -->    