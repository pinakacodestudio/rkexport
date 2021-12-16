<header id="topnav" class="navbar navbar-default navbar-fixed-top" role="banner">

  <div class="logo-area">
    <a class="navbar-brand navbar-brand-default" href="<?=ADMIN_URL.'dashboard'?>">
      <img class="show-on-collapse img-logo-white" alt="Paper" src="<?=MAIN_LOGO_IMAGE_URL.COMPANY_FAVICON;?>">
      <img class="show-on-collapse img-logo-dark" alt="Paper" src="<?=MAIN_LOGO_IMAGE_URL.COMPANY_FAVICON;?>">
      <img class="img-white" alt="<?=COMPANY_NAME?>" src="<?=MAIN_LOGO_IMAGE_URL.COMPANY_LOGO;?>">
      <img class="img-dark" alt="<?=COMPANY_NAME?>" src="<?=MAIN_LOGO_IMAGE_URL.COMPANY_LOGO;?>">
    </a>

    <span id="trigger-sidebar" class="toolbar-trigger toolbar-icon-bg stay-on-search">
      <a data-toggle="tooltips" data-placement="right" title="Toggle Sidebar" onclick="setsidebarcollapsed()">
        <span class="icon-bg">
          <i class="material-icons">menu</i>
        </span>
      </a>
    </span>
    
    
  </div><!-- logo-area -->

  <ul class="nav navbar-nav toolbar pull-right">

    <li class="toolbar-icon-bg hidden-xs" id="trigger-fullscreen">
        <a href="#" class="toggle-fullscreen"><span class="icon-bg">
          <i class="material-icons">fullscreen</i>
        </span></i></a>
    </li>
    <li class="dropdown toolbar-icon-bg">  <!-- id="trigger-infobar" -->
        <a class="hasnotifications dropdown-toggle" data-toggle='dropdown'>
        <span class="icon-bg">
          <i class="material-icons">more_vert</i>
        </span>
      </a>
      <div class="dropdown-menu animated notifications">
        <div class="scroll-pane">
          <ul class="media-list scroll-content">
            <li class="media notification-success">
              <a href="<?php echo ADMIN_URL; ?>user/user-profile">
                <div class="media-left">
                  <span><i class="fa fa-user fa-lg"></i></span>
                </div>
                <div class="media-body">
                  <h4 class="notification-heading">My Account</h4>
                </div>
              </a>
            </li>
            <li class="media notification-success">
              <a href="<?php echo ADMIN_URL; ?>user/change-password">
                <div class="media-left">
                  <span><i class="fa fa-key fa-lg"></i></span>
                </div>
                <div class="media-body">
                  <h4 class="notification-heading">Change Password</h4>
                </div>
              </a>
            </li>
            <li class="media notification-success">
              <a href="<?php echo ADMIN_URL; ?>logout">
                <div class="media-left">
                  <span><i class="fa fa-sign-out fa-lg"></i></span>
                </div>
                <div class="media-body">
                  <h4 class="notification-heading">Logout</h4>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </li>
    
  </ul>

</header>
