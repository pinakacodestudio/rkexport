<?php 
$this->admin_headerlib->add_stylesheet("login-css","pages/login.css");
$this->admin_headerlib->add_javascript("reset_password","pages/reset_pwd.js");
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
        <?php 
          $FOOTER_BG_COLOR = FOOTER_BG_COLOR!=''?FOOTER_BG_COLOR:'#03a9f4';
          $SIDEBAR_BG_COLOR = SIDEBAR_BG_COLOR!=''?SIDEBAR_BG_COLOR:'#2196f3';
          $SIDEBAR_MENU_ACTIVE_COLOR = SIDEBAR_MENU_ACTIVE_COLOR!=''?SIDEBAR_MENU_ACTIVE_COLOR:'#42a5f5';
          $SIDEBAR_SUBMENU_BG_COLOR = SIDEBAR_SUBMENU_BG_COLOR!=''?SIDEBAR_SUBMENU_BG_COLOR:'#1a78c2';
          $SIDEBAR_SUBMENU_ACTIVE_COLOR = SIDEBAR_SUBMENU_ACTIVE_COLOR!=''?SIDEBAR_SUBMENU_ACTIVE_COLOR:'#2196f3';
          $THEME_COLOR = THEME_COLOR!=''?THEME_COLOR:'#03a9f4';
          $LINK_COLOR = LINK_COLOR!=''?LINK_COLOR:'#bf2e2e';
          $TABLE_HEADER_COLOR = TABLE_HEADER_COLOR!=''?TABLE_HEADER_COLOR:'#bd9117';
          $FONT_COLOR = FONT_COLOR!=''?FONT_COLOR:'#fff';
        ?>
        <style>
          .form-group.is-focused .form-control {
              background-image: linear-gradient(<?=$THEME_COLOR?>,<?=$THEME_COLOR?>), linear-gradient(#D2D2D2, #D2D2D2);
          }
          .form-group.is-focused label,
          .form-group.is-focused label.control-label {
              color:<?=$THEME_COLOR?>;
          }
          .form-group.has-error label.control-label, 
          .form-group.has-error .help-block{
              color:#e51c23 !important;
          }
          .form-group.is-focused .form-control .material-input:after {
              background-color: <?=$THEME_COLOR?> !important;
          }
          .btn-primary {
              color: #fff;
              background-color:<?=$THEME_COLOR?> !important;
              border-color:<?=$THEME_COLOR?> !important;
          }
          .btn-primary.disabled,
          .btn-primary[disabled],
          fieldset[disabled] .btn-primary,
          .btn-primary.disabled:hover,
          .btn-primary[disabled]:hover,
          fieldset[disabled] .btn-primary:hover,
          .btn-primary.disabled:focus,
          .btn-primary[disabled]:focus,
          fieldset[disabled] .btn-primary:focus,
          .btn-primary.disabled.focus,
          .btn-primary[disabled].focus,
          fieldset[disabled] .btn-primary.focus,
          .btn-primary.disabled:active,
          .btn-primary[disabled]:active,
          fieldset[disabled] .btn-primary:active,
          .btn-primary.disabled.active,
          .btn-primary[disabled].active,
          fieldset[disabled] .btn-primary.active {
              background-color:<?=$THEME_COLOR?> !important;
              border-color: <?=$THEME_COLOR?> !important;
          }
          .btn-primary:hover,
          .btn-primary:focus,
          .btn-primary.focus,
          .btn-primary:active,
          .btn-primary.active,
          .open > .dropdown-toggle.btn-primary {
              color: #fff;
              background-color: <?=$THEME_COLOR?> !important;
              border-color: #027fb8;
          }
          .btn:not(.btn-raised).btn-info, 
          .input-group-btn .btn:not(.btn-raised).btn-info{
            color: <?=$THEME_COLOR?> !important;
          }
          .btn.btn-raised.btn-primary,
          .input-group-btn .btn.btn-raised.btn-primary,
          .btn.btn-fab.btn-primary,
          .input-group-btn .btn.btn-fab.btn-primary,
          .btn-group-raised .btn.btn-primary,
          .btn-group-raised .input-group-btn .btn.btn-primary {
              background-color:<?=$THEME_COLOR?> !important;
              color :<?=$FONT_COLOR?> !important;
            /* color: rgba(255,255,255, 0.84); */
            }
        </style>
      </head>
  <!-- END HEAD -->
  
  <body class="focused-form animated-content">
    <div class="mask">
      <div id="loader"></div>
    </div>
    <div class="container" id="login-form">
      <a href="javascript:void(0);" class="login-logo"><img src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?>" style="width:200px;"></a>
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