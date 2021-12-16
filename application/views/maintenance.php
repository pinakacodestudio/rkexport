<?php  defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->
<?php //$headerData = $this->frontend_headerlib->data(); ?>

<head>
    <title>Rk Infotech</title>
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-touch-fullscreen" content="yes" />
    <meta name="author" content="Roger Motors" />
    <meta name="msvalidate.01" content="A5C1247F8E21CC3D8D3B312CB13125AC" />
    <meta name="yandex-verification" content="099e372084818b9a" />
    <meta name="p:domain_verify" content="cec4038cab772c69ee7d96ef294ee26b" />

    <link rel="canonical" href="<?=base_url(uri_string())?>" />
    <link hreflang="en" rel="alternate" href="<?=FRONT_URL?>" />

    <link rel="shortcut icon" type="image/x-icon" href="<?=base_url()?>assets/img/settings/favicon.png" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" />
    <script>
    var SITE_URL = '<?php echo FRONT_URL ?>';

    window.smartlook || (function(d) {

        var o = smartlook = function() {
                o.api.push(arguments)
            },
            h = d.getElementsByTagName('head')[0];

        var c = d.createElement('script');
        o.api = new Array();
        c.async = true;
        c.type = 'text/javascript';

        c.charset = 'utf-8';
        c.src = 'https://rec.smartlook.com/recorder.js';
        h.appendChild(c);

    })(document);

    smartlook('init', '50cdd2d3d11546e6027af076aab3752473d93a04');

    <!-- Hotjar Tracking Code for <?=base_url()?>/ -->

    (function(h, o, t, j, a, r) {

        h.hj = h.hj || function() {
            (h.hj.q = h.hj.q || []).push(arguments)
        };

        h._hjSettings = {
            hjid: 1138112,
            hjsv: 6
        };

        a = o.getElementsByTagName('head')[0];

        r = o.createElement('script');
        r.async = 1;

        r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;

        a.appendChild(r);

    })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>
    <script src="<?=base_url()?>assets/js/jquery.min.js"></script>
    <?php /*if($page=='home'){ ?>
    <script id="mcjs">
    ! function(c, h, i, m, p) {
        m = c.createElement(h), p = c.getElementsByTagName(h)[0], m.async = 1, m.src = i, p.parentNode.insertBefore(m, p)
    }(document, "script", "https://chimpstatic.com/mcjs-connected/js/users/18c33638fb44c75b8a99542f7/a35de440735669829635a2592.js");
    </script>
    <? }*/ ?>
    <!-- Google Tag Manager -->
    <script>
    (function(w, d, s, l, i) {
        w[l] = w[l] || [];
        w[l].push({
            'gtm.start': new Date().getTime(),
            event: 'gtm.js'
        });
        var f = d.getElementsByTagName(s)[0],
            j = d.createElement(s),
            dl = l != 'dataLayer' ? '&l=' + l : '';
        j.async = true;
        j.src =
            'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
        f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-PK9GVRG');
    </script>
    <!-- End Google Tag Manager -->
</head>
<!-- END HEAD -->

<body class="bg">
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PK9GVRG" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <style type="text/css">
    .page-wrap {
        min-height: 100%;
        position: relative;
    }

    #content {
        padding: 92px 50px;
    }

    .logo {
        text-align: center;
    }

    img {
        height: auto;
        max-width: 100%;
        margin-bottom: 15px;
    }

    article,
    aside,
    details,
    figcaption,
    figure,
    footer,
    header,
    main,
    nav,
    section {
        display: block;
    }

    .site-title {
        text-align: center;
        text-shadow: 0 0 1px #444444;
        font-size: 50px;
        word-spacing: 10px;
        color: #333;
    }

    .construction-msg p {
        max-width: 600px;
        margin: 0 auto;
        text-align: center;
        color: #333;
        font-size: 18px;
    }
    </style>
    <button onclick="topFunction()" id="back2top" title="Go To Top"><i class="fa fa-chevron-up"></i></button>
    <a href="https://wa.me/919227552288" id="whatsapp" title="Contact on What's App" target="_blank"><i class="fa fa-whatsapp fa-lg"></i></a>
    <a href="tel:9227552288" class="technical-support-btn" style="top:45%">For Queries : 92275 52288</a>
    <header class="clearfix">
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="social-top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 col-md-8 col-lg-8">
                            <ul class="inline">
                                <?php if(COMPANY_EMAIL!='') { ?>
                                <li><i class="fa fa-envelope" style="font-size:20px;color:black"></i>&nbsp;<a href="mailto:<?=COMPANY_EMAIL?>"> <?=COMPANY_EMAIL?></a></li>
                                <? } ?>
                                <?php if(COMPANY_MOBILENO!='') { ?>
                                <li><i class="fa fa-phone" style="font-size:21px;color:#3874eb"></i>&nbsp;&nbsp;<a href="tel:<?=COMPANY_MOBILENO?>"> <?=COMPANY_MOBILENO?></a></li>
                                <? } ?>
                                <?php  if(COMPANY_ADDRESS!='') { ?>
                                <li>&nbsp;<div class="p-n pr-md pull-left"><i class="fa fa-map-marker" style="font-size:25px;color:#5dbcc7"></i></div><div class="col-sm-10 pl-n"><a href="#:<?=COMPANY_ADDRESS?>"><?=COMPANY_ADDRESS?></a></div></li>
                                <? } ?>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </nav>
    </header>
    <div style="background: url(<?=base_url()?>assets/img/news-bg.png) right bottom no-repeat #fff;
    ">
        <div class="page-wrap">
            <section id="content">
                <div class="logo">
                    <img alt="<?=COMPANY_NAME?>" src="<?=MAIN_LOGO_IMAGE_URL.COMPANY_LOGO;?>">
                </div>
                <h1 class="site-title">Website on Maintenance!</h1>
                <div class="construction-msg">
                    <p>We are very sorry for this inconvenience. We are currently working on something new and we will be back soon with awesome new features.<br> Thanks for your patience.</p>
                </div>
            </section>
        </div>
    </div>
    <footer style=" padding: 1px 0;margin: 61px 0 0;height: 73px;">
        <div class="copyright" style="">
            <div class="container" style="width:1200px;">
                <div class="pull-left">
                    <p class="" style=" padding:25px;">
                        Copyright &copy; 2018 <a href="<?=FRONT_URL?>" class="footer-link">RK INFOTECH </a> All Rights Reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <?php
        // echo GoogleTrackingCode;
    ?>
</body>

</html>