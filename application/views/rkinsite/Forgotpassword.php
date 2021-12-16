<?php
$this->admin_headerlib->add_javascript("login","pages/forgotpassword.js");
$headerData = $this->admin_headerlib->data();
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
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
  
  <body class="focused-form animated-content">
    <div class="mask">
      <div id="loader"></div>
    </div>
    <div class="container">
      <a href="javascript:void(0);" class="login-logo"><img src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?>"></a>
        <div class="row">
          <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h2>Forgot Password</h2>
              </div>
              <div class="panel-body">
                
                <form action="#" class="form-horizontal" id="validate-form">
                  <div class="form-group mb-md" id="email_div">
                    <div class="col-md-12">
                      <label class="control-label" for="forgotEmail"><i class="fa fa-user fa-lg"></i> Email ID</label>
                      <input id="forgotEmail" class="form-control" name="forgotEmail" type="text" tabindex="1">
                    </div>
                  </div>
                </form>
                <p style="color: #616161;font-size: 14px;">Enter your email and we will send you a link to reset your password.</p>
              </div>
              <div class="panel-footer">
                <div class="clearfix">
                  <div class="col-md-8 col-xs-6">
                    <a href="javascript:void(0);" onclick="checkemail()" id="btnsubmit" class="btn btn-primary btn-raised">Submit</a>
                  </div>
                  <div class="col-md-4 col-xs-6" style="text-align: right;">
                    <a href="<?php echo ADMIN_URL.'login' ?>" class="btn btn-primary btn-raised">Login</a>
                  </div>
                </div>
              </div>

              <!-- <div class="panel-footer">
                <div class="clearfix">
                  <a href="javascript:void(0);" onclick="checkemail()" id="btnsubmit" class="btn btn-primary btn-raised pull-right">Submit</a>
                </div>
              </div> -->
            </div>
          </div>
        </div>
    </div>
    <?php echo $headerData['javascript']; ?>
    <?php echo $headerData['javascript_plugins']; ?>
    <script src="<?php echo ADMIN_JS_URL;?>application.js" type="text/javascript"></script>
  </body>
</html>