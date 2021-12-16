<?php 
$headerData = $this->admin_headerlib->data();
?>
<!DOCTYPE html>
<html lang="en"> <!--<![endif]-->
  <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        
        <title><?php echo $title." - ".COMPANY_NAME ?></title>
        <?php echo $headerData['favicon']; ?>
        <?php echo $headerData['meta_tags']; ?>
        <?php echo $headerData['plugins']; ?>
        <?php echo $headerData['stylesheets']; ?>
        <script type="text/javascript">
            var SITE_URL = '<?php echo ADMIN_URL; ?>';
        </script> 
        <script src="<?php echo ADMIN_JS_URL;?>jquery-1.10.2.min.js" type="text/javascript"></script>
    </head>
  <!-- END HEAD -->
  
  <body style="margin-top: 10%;height: auto;">
    <div class="container" >
        <div class="row">
          <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
              <div class="panel-body" style="text-align: center;">
                  <i class="fa fa-exclamation-triangle fa-5x" aria-hidden="true"></i>
                  <div class="row">
                    <div class="input-field col s12 center">
                      <h1>Error <span>404</span></h1>
                    </div>
                  </div>
                  <div class="row margin">
                    <div class="input-field col s12">
                      <p>Sorry, the page you are looking for doesn't exist !</p>
                        <p>Please check out one of the options below</p>
                    </div>
                  </div>
              </div>
              <div class="panel-footer">
                <div class="clearfix">
                  <div class="input-field col-md-12">
                    <div class="col-md-6">
                    <a href="#" onclick="history.go(-1);" class="btn btn-primary btn-raised" style="width: 100%;">Go back</a>
                    <?php
                      $url = explode('/',$_SERVER['REQUEST_URI']);
                      $a = rtrim(ADMINFOLDER, "/"); ?>
                    </div><div class="col-md-6">
                    <?php if (in_array($a, $url)){ ?>
                      <a href="<?php echo ADMIN_URL; ?>dashboard" class="btn btn-primary btn-raised" style="width: 100%;">Admin</a>
                    <?php }else{ ?>
                      <a href="<?php echo DOMAIN_URL; ?>" class="btn btn-primary btn-raised" style="width: 100%;">Home</a>
                    <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <?php echo $headerData['javascript']; ?>
    <?php echo $headerData['javascript_plugins']; ?>
    <script src="<?php echo ADMIN_JS_URL;?>application.js" type="text/javascript"></script>
  </body>
</html>