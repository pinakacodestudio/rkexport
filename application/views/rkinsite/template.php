
<?php
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
        <?php echo $headerData['stylesheets']; ?>
        <?php echo $headerData['plugins']; ?>        
        
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
        <script type="text/javascript">
            var SITE_URL = '<?php echo ADMIN_URL ?>';
            var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
            var ACTION = '<?php if(isset($action)){ echo 1; }else{ echo 0; }/*1-Edit,0-Add or View*/ ?>';
            var UPLOAD_MAX_FILE_SIZE = '<?=UPLOAD_MAX_FILE_SIZE?>';
            var UPLOAD_MAX_FILE_SIZE_CATALOG = '<?=UPLOAD_MAX_FILE_SIZE_CATALOG?>';
            var UPLOAD_MAX_ZIP_FILE_SIZE = '<?=UPLOAD_MAX_ZIP_FILE_SIZE?>';
            var Inquiry_label= '<?=Inquiry ?>';
            var inquiry_label= '<?=inquiry ?>';
            var Followup_label= '<?=Followup ?>';
            var followup_label= '<?=followup ?>';
            var follow_up_label= '<?=follow_up ?>';
            var Follow_up_label= '<?=Follow_Up ?>';
            var GST_PRICE = '<?=PRICE?>';
            var DEFAULT_IMG_PATH = '<?=DEFAULT_IMG?>';
            var THEMECOLOR = '<?=$THEME_COLOR?>';
            var CRM_SETTING = '<?=CRM?>';
            var Member_label= '<?=Member_label?>';
            var member_label= '<?=member_label?>';
            var PRODUCTDEFAULTIMAGE= '<?=PRODUCTDEFAULTIMAGE?>';
            var PRODUCT_PATH='<?=PRODUCT?>'; 
            var MANAGE_DECIMAL_QTY='<?=MANAGE_DECIMAL_QTY?>';
            var MEMBER_LAT_LONG = '<?=MEMBER_LAT_LONG?>';
            
        </script>
        
        <style>

            /* Start Footer Color CSS  */
            
            footer {
                background: <?=$FOOTER_BG_COLOR?> !important;
            }
            
            /* End Footer Color CSS */

            /* Start Sidebar Background Color CSS */

            .sidebar-blue.static-sidebar-wrapper,
            .sidebar-blue.fixed-sidebar {
                background-color: <?=$SIDEBAR_BG_COLOR ?> !important;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu li a {
                color: <?=$FONT_COLOR?>;
                background-color: <?=$SIDEBAR_BG_COLOR ?>;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu > li.active > a {
                font-weight: 500;
                background-color: <?=$SIDEBAR_MENU_ACTIVE_COLOR ?> !important;
                color: <?=$FONT_COLOR?>;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu > li:hover > a {
                background-color: <?=$SIDEBAR_MENU_ACTIVE_COLOR ?> !important ;
                color: <?=$FONT_COLOR?>;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu li.open > a {
                background-color: <?=$SIDEBAR_BG_COLOR ?>;
            }
             
            /* End Sidebar Background Color CSS */

            /* Start Sidebar Submenu Background Color CSS */

            .sidebar-blue nav.widget-body > ul.acc-menu ul,
            .sidebar-blue nav.widget-body > ul.acc-menu ul li a {
                background-color: <?=$SIDEBAR_SUBMENU_BG_COLOR ?>  !important;
                color: <?=$FONT_COLOR?>;
            }
           /*  .sidebar-blue nav.widget-body > ul.acc-menu ul li.active:not(.open) > a {
                color: <?=$FONT_COLOR?>;;
                background-color: <?=$SIDEBAR_SUBMENU_ACTIVE_COLOR?> !important;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu ul li a:hover {
                background-color:  <?=$SIDEBAR_SUBMENU_ACTIVE_COLOR?> !important;
                color: <?=$FONT_COLOR?>;
            } */

            /* End Sidebar Submenu Background Color CSS */

            /* Start Theme Color CSS */

            a:focus {
                color: <?= $LINK_COLOR ?>;
            }
            a {
                color: <?= $LINK_COLOR ?>;
            }
            a:hover,
            a:focus {
                color: <?= $LINK_COLOR ?>;
            }
            .form-group.is-focused .form-control {
                
                background-image: linear-gradient(<?=$THEME_COLOR?>,<?=$THEME_COLOR?>), linear-gradient(#D2D2D2, #D2D2D2);
                
            }
            .border-panel{
                border-top-color: <?=$THEME_COLOR?> !important;
                border-top: 3px solid <?=$THEME_COLOR?> !important;
            }
            .form-group.has-error label.control-label, 
            .form-group.has-error .help-block{
                color:#e51c23 !important;
            }
            .form-group.is-focused label,
            .form-group.is-focused label.control-label {
                color:<?=$THEME_COLOR?>;
            }
            .text-primary {
                color: <?=$THEME_COLOR?> !important;
            }
            .bg-primary {
                color: #fff;
                background-color:<?=$THEME_COLOR?> !important;
            }
            .btn-primary {
                color: <?=$FONT_COLOR?>;
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
            .btn-primary .badge {
                color: <?=$THEME_COLOR?> !important;
                background-color: #fff;
            }
            .btn-link {
                color: #03a9f4;
            }
            .btn-link:hover,
            .btn-link:focus {
                color: <?=$THEME_COLOR?> !important;
            } 
            .list-group-item.active,
            .list-group-item.active:hover,
            .list-group-item.active:focus {
                background-color:<?=$THEME_COLOR?> !important;
                border-color:<?=$THEME_COLOR?>!important;
            }
            .nav .open > a,
            .nav .open > a:hover,
            .nav .open > a:focus {
                border-color:<?=$THEME_COLOR?> !important;
            }
            .pagination > li > a,
            .pagination > li > span {
                color: <?=$THEME_COLOR?>;
            }
            .pagination > li > a:hover,
            .pagination > li > span:hover,
            .pagination > li > a:focus,
            .pagination > li > span:focus {
                color:#fff;
                background-color: <?=$THEME_COLOR?> !important;
                border-color: <?=$THEME_COLOR?> !important;
            }
            .pagination > .active > a,
            .pagination > .active > span,
            .pagination > .active > a:hover,
            .pagination > .active > span:hover,
            .pagination > .active > a:focus,
            .pagination > .active > span:focus {
                background-color: <?=$THEME_COLOR?>;
                border-color: <?=$THEME_COLOR?> ;
            }
            
            .pagination > .disabled > span,
            
            .pagination > .disabled > a
             {
                color: <?=$THEME_COLOR?> ;
                background-color: #fff;
                border-color: #ddd;
                cursor: not-allowed;
            }
            
            .pagination > .disabled > span:hover,
            .pagination > .disabled > span:focus,
            .pagination > .disabled > a:hover,
            .pagination > .disabled > a:focus {
                color: #fff ;
                background-color: #fff;
                border-color: #ddd;
                cursor: not-allowed;
            }
            a.thumbnail:hover,
            a.thumbnail:focus,
            a.thumbnail.active {
                border-color: <?=$THEME_COLOR?>;
            }
            .label-primary {
                background-color:<?=$THEME_COLOR?> !important;
            }
            .list-group-item.active > .badge,
            .nav-pills > .active > a > .badge {
                color: <?=$THEME_COLOR?> !important;
                
            }
           /*  .progress-bar {
                background-color: <?=$THEME_COLOR?> !important;
            } */
            a,
            a:hover,
            a:focus {
                color: <?=$LINK_COLOR?>;
            }
            body .container .well-primary,
            body .container-fluid .well-primary,
            body .container .jumbotron-primary,
            body .container-fluid .jumbotron-primary {
                background-color: <?=$THEME_COLOR?> !important;
            }
            /* .btn:not(.btn-raised).btn-primary,
            .input-group-btn .btn:not(.btn-raised).btn-primary {
                color: <?=$THEME_COLOR?> !important;
            }
            .btn.btn-raised.btn-primary,
            .input-group-btn .btn.btn-raised.btn-primary,
            .btn.btn-fab.btn-primary,
            .input-group-btn .btn.btn-fab.btn-primary,
            .btn-group-raised .btn.btn-primary,
            .btn-group-raised .input-group-btn .btn.btn-primary {
                background-color: <?=$THEME_COLOR?> !important;                
            }
            .btn-group.open > .dropdown-toggle.btn.btn-primary,
            .btn-group-vertical.open > .dropdown-toggle.btn.btn-primary {
                background-color: <?=$THEME_COLOR?> !important;
            } */
            .checkbox input[type=checkbox]:checked + .checkbox-material .check {
                color: <?=$THEME_COLOR?> !important;
                border-color: <?=$THEME_COLOR?> !important;
            }
            .checkbox input[type=checkbox]:checked + .checkbox-material .check:before {
                color: <?=$THEME_COLOR?> !important;
            }
            .checkbox.checkbox-primary input[type=checkbox]:checked + .checkbox-material .check {
                color: <?=$THEME_COLOR?> !important;
                border-color: <?=$THEME_COLOR?> !important;
            }
            .checkbox.checkbox-primary input[type=checkbox]:checked + .checkbox-material .check:before {
                color: <?=$THEME_COLOR?> !important;
            }
            .togglebutton label input[type=checkbox]:checked + .toggle:after {
                background-color: <?=$THEME_COLOR?> !important;
            }
            .togglebutton.toggle-primary input[type=checkbox]:checked + .toggle:after {
                background-color: <?=$THEME_COLOR?> !important;
            }
            .radio label .check {
                background-color: <?=$THEME_COLOR?> !important;
            }
            .radio input[type=radio]:checked ~ .check {
                background-color: <?=$THEME_COLOR?> !important;
            }
            .radio input[type=radio]:checked ~ .circle {
                border-color: <?=$THEME_COLOR?> !important;
            }
            .radio.radio-primary label .check {
                background-color: <?=$THEME_COLOR?> !important;
            }
            .radio.radio-primary label .circle {
                border-color: <?=$THEME_COLOR?> !important;
            }
            .radio.radio-primary input[type=radio]:checked ~ .check {
                background-color: <?=$THEME_COLOR?> !important;
            }
            .radio.radio-primary input[type=radio]:checked ~ .circle {
                border-color: <?=$THEME_COLOR?> !important;
            }
            .label.label-primary,
            #topnav .topnav-dropdown-header span.label.label-primary {
            background-color: <?=$THEME_COLOR?> !important;
            }
            
            .form-group.is-focused .form-control .material-input:after {
                background-color: <?=$THEME_COLOR?> !important;
            }
            
            /* .navbar {
                background-color: <?=$THEME_COLOR?> !important;
                border: 0;
                border-radius: 0;
            } 
            .navbar,
            .navbar.navbar-default {
            background-color: <?=$THEME_COLOR?> !important;
            color: rgba(255,255,255, 0.84);
            }*/
            .navbar .dropdown-menu li > a:hover,
            .navbar.navbar-default .dropdown-menu li > a:hover,
            .navbar .dropdown-menu li > a:focus,
            .navbar.navbar-default .dropdown-menu li > a:focus,
            .navbar .tt-dropdown-menu li > a:hover,
            .navbar.navbar-default .tt-dropdown-menu li > a:hover,
            .navbar .tt-dropdown-menu li > a:focus,
            .navbar.navbar-default .tt-dropdown-menu li > a:focus {
            color: <?=$THEME_COLOR?> !important;
            background-color: #eeeeee;
            }
            #topnav.navbar-light-blue .logo-area .toolbar-trigger a {
                border-color: #81d4fa;
                background-color: #03a9f4;
            }
            #topnav.navbar-light-blue .logo-area .toolbar-trigger a:hover span.icon-bg {
                background-color: #03a9f4;
            }
            #trigger-channel .dropdown-menu li a:hover{
                color: <?=$THEME_COLOR?> !important;  
            }
            .dropdown-menu li a:hover,
            .tt-dropdown-menu li a:hover {
                background-color: transparent;
                color: <?=$THEME_COLOR?> !important;
            }
            .btn-group.open > .dropdown-toggle.btn,
            .btn-group-vertical.open > .dropdown-toggle.btn,
            .btn-group.open > .dropdown-toggle.btn.btn-default,
            .btn-group-vertical.open > .dropdown-toggle.btn.btn-default {
                background-color: #f5f5f5;
            }
            .dropdown-menu>.active>a, 
            .dropdown-menu>.active>a:focus, 
            .dropdown-menu>.active>a:hover{
                color:#fff  !important;
                background-color:<?=$THEME_COLOR?> !important;
            }
            .datepicker table tr td.active:hover,
            .datepicker table tr td.active:hover:hover,
            .datepicker table tr td.active.disabled:hover,
            .datepicker table tr td.active.disabled:hover:hover,
            .datepicker table tr td.active:focus,
            .datepicker table tr td.active:hover:focus,
            .datepicker table tr td.active.disabled:focus,
            .datepicker table tr td.active.disabled:hover:focus,
            .datepicker table tr td.active.focus,
            .datepicker table tr td.active:hover.focus,
            .datepicker table tr td.active.disabled.focus,
            .datepicker table tr td.active.disabled:hover.focus,
            .datepicker table tr td.active:active,
            .datepicker table tr td.active:hover:active,
            .datepicker table tr td.active.disabled:active,
            .datepicker table tr td.active.disabled:hover:active,
            .datepicker table tr td.active.active,
            .datepicker table tr td.active:hover.active,
            .datepicker table tr td.active.disabled.active,
            .datepicker table tr td.active.disabled:hover.active,
            .open > .dropdown-toggle.datepicker table tr td.active,
            .open > .dropdown-toggle.datepicker table tr td.active:hover,
            .open > .dropdown-toggle.datepicker table tr td.active.disabled,
            .open > .dropdown-toggle.datepicker table tr td.active.disabled:hover {
                color: #fff;
                background-color: <?=$THEME_COLOR?> !important;
                border-color: #027fb8;
            }
            .datepicker table tr td.today,
            .datepicker table tr td.today:hover,
            .datepicker table tr td.today.disabled,
            .datepicker table tr td.today.disabled:hover {
            color: #FFF;
            background-color: <?=$THEME_COLOR?> !important;
            border-color: #03a9f4;
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
            .btn:not(.btn-raised).btn-primary,
            .input-group-btn .btn:not(.btn-raised).btn-primary {
                color: <?=$THEME_COLOR?>;
            }
            .btn.btn-raised.btn-primary,
            .input-group-btn .btn.btn-raised.btn-primary,
            .btn.btn-fab.btn-primary,
            .input-group-btn .btn.btn-fab.btn-primary,
            .btn-group-raised .btn.btn-primary,
            .btn-group-raised .input-group-btn .btn.btn-primary {
            background-color:<?=$THEME_COLOR?> !important;
            color :<?=$FONT_COLOR?>;
            /* color: rgba(255,255,255, 0.84); */
            }
            #checkreserved .toggle-group label, 
            #checkstatus .toggle-group label, 
            .toggle-group label, 
            #staffattendance .toggle-group label{
                color: #fff;
                font-size: 13px;
            }
            #checkreserved .toggle-group .btn-primary, 
            #checkstatus .toggle-group .btn-primary, 
            .toggle-group .btn-primary, 
            #staffattendance .toggle-group .btn-primary{
                background-color: <?=$THEME_COLOR?> !important;
            }
            .toggle-group .btn-primary:hover,
            #staffattendance .toggle-group .btn-primary:hover{
                background-color: <?=$THEME_COLOR?> !important;
            }
            .popover-title{
                background: <?=$THEME_COLOR?>;
                color: <?=$FONT_COLOR?>;
            }
            .nav-tabs {
                background: <?=$SIDEBAR_SUBMENU_BG_COLOR?>;
            }
            .nav-tabs > li.active,.nav-tabs > li:hover {
                background-color: <?=$SIDEBAR_SUBMENU_ACTIVE_COLOR?>;
            }
            /* End Theme Color CSS */

            /* Start Font Color CSS */

            footer h6{
                color: <?=$FONT_COLOR?>;
            }
            .sidebar-blue .sidebar .widget .widget-body .userinfo .username {
                color: <?=$FONT_COLOR?>;
            }
            .sidebar-blue .sidebar .widget .widget-body .userinfo .useremail {
                color: <?=$FONT_COLOR?>;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu > li > a > span.icon {
                color: <?=$FONT_COLOR?>;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu > li:hover > a span.icon {
                color: <?=$FONT_COLOR?>;
            }
            .sidebar-blue nav.widget-body > ul.acc-menu > li.active > a span.icon {
                /*background: @@sidebar-bg;*/
                color: <?=$FONT_COLOR?>;
            }
            .breadcrumb > .active{
                color: <?=$LINK_COLOR?>!important;
            }
            /* End Theme Color CSS */

            /* Start Table Header Color CSS */
            
            table thead th {
                color :<?= $TABLE_HEADER_COLOR ?>;
            }

            /* End Table Header Color CSS */

            

        </style>
        <script src="<?php echo ADMIN_JS_URL;?>jquery-1.10.2.min.js" type="text/javascript"></script>
        <?php echo $headerData['top_javascripts']; ?>
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <?php
        if(!empty($this->session->userdata(base_url().'SIDEBAR_COLLAPASED'))){
            $sidebar_class = $this->session->userdata(base_url().'SIDEBAR_COLLAPASED'); 
        }else{ 
            $sidebar_class = "sidebar-scroll";
        }
    ?>
    <body class="infobar-overlay page-md page-header-fixed page-sidebar-closed-hide-logo page-sidebar-closed-hide-logo <?=$sidebar_class?>">
    <!-- BEGIN PAGE LOADER -->    
    <div class="mask">
      <div id="loader"></div>
    </div>
    <p class="m-n" id="copycontent"></p>
    <!-- END PAGE LOADER -->
    <!-- BEGIN HEADER -->
    <?php $this->load->view(ADMINFOLDER.'includes/header');?>
    <!-- END HEADER -->  

    <!-- BEGIN CONTAINER -->
    <div id="wrapper">
        <div id="layout-static">
            <div class="static-sidebar-wrapper sidebar-blue">
                <?php $this->load->view(ADMINFOLDER.'includes/sidebar');?>
            </div>    
            <!-- BEGIN CONTENT -->

            <div class="static-content-wrapper">
                <div class="static-content">
                    <!-- BEGIN PAGE CONTENT INNER -->
                    <?php $this->load->view(ADMINFOLDER . $module);?>
                    <!-- END PAGE CONTENT INNER -->
                </div>    
                <?php $this->load->view(ADMINFOLDER.'includes/footer');?>

            </div>
            <div class="extrabar-underlay"></div>
            <!-- END CONTENT -->
            
        </div>        
    </div>
    <!-- END CONTAINER -->

    <?php echo $headerData['javascript']; ?>
    <?php echo $headerData['javascript_plugins']; ?>
    <?php echo $headerData['bottom_javascripts']; ?>
    <script src="<?php echo ADMIN_JS_URL;?>application.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            //Tooltip, activated by hover event
            $("body").tooltip({   
                selector: ".btn-tooltip",
                container: "body"
            });

            $(document).on("mouseenter",".btn-tooltip",function(){
                $(".popover").hide();
            });
        });
    </script>
</body>
<!-- END BODY -->
</html>