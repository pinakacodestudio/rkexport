<?php 
$this->admin_headerlib->add_stylesheet("login-css","pages/login.css");
$this->admin_headerlib->add_javascript("resetpassword","pages/resetpassword.js");
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
    <div class="container" id="login-form">
      <a href="javascript:void(0);" class="login-logo"><img src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?>"></a>
        <div class="row">
          <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h2>Reset Your Passsword</h2>
              </div>
              <div class="panel-body">
                
                <form action="#" class="form-horizontal" id="resetform">
                  <input type="hidden" name="userid" id="userid" value="<?php echo $resetdata['userid']; ?>">
                  <input type="hidden" name="verifiedid" id="verifiedid" value="<?php echo $resetdata['id']; ?>">
                  <div class="form-group mb-md" id="newpassword_div">
                    <div class="col-md-12">
                      <label class="control-label" for="password">New Password</label>
                      <input id="password" class="form-control" name="password" type="password">
                      <i class="fa fa-eye eye fa-lg" aria-hidden="true" onClick="showPassword('password')"></i>
                    </div>
                  </div>

                  <div class="form-group mb-md" id="confirmpassword_div">
                    <div class="col-md-12">
                      <label class="control-label" for="confirmpassword">Confirm Password</label>
                      <input id="confirmpassword" class="form-control" name="confirmpassword" type="password">
                    </div>
                  </div>
                </form>
              </div>
              <div class="panel-footer">
                <div class="clearfix">
                  <div class="col-md-8">
                    <a href="javascript:void(0);" onclick="checkpassword()" id="btnsubmit" class="btn btn-primary btn-raised" style="width: 100%">Reset Password</a>
                  </div>
                  <div class="col-md-4">
                    <a href="<?php echo ADMIN_URL.'login' ?>" class="btn btn-primary btn-raised" style="width: 100%">Login</a>
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