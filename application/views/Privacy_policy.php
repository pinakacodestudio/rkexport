<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Favicon-->
	<link rel="shortcut icon" type="image/x-icon"  href="<?=DOMAIN_URL?>assets/uploaded/company/<?=COMPANY_FAVICON?>" />
	<!-- meta character set -->
	<meta charset="UTF-8">
	<!-- Site Title -->
	<title><?=$title." - ".COMPANY_NAME ?></title>

	<link href="https://fonts.googleapis.com/css?family=Poppins:300,500,600" rel="stylesheet">
		<!--
		CSS
		============================================= -->
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/bootstrap.css">
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/main.css">
	</head>
	<body>
		<div class="">
			<div class="hero-area relative">
				<header>
					<div class="container">
						<div class="header-wrap">
							<div class="header-top d-flex justify-content-between align-items-center">
								<div class="logo">
									<a href="<?=COMPANY_WEBSITE?>"><img src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?>" style="max-width: 200px;height: auto;"></a>
								</div>
							</div>
						</div>
					</div>
					<div style="margin-top: 20px;padding: 20px;background: #2196f3;">
						<div class="container">
							<h3 style="color:#fff;">Privacy Policy</h3>
							
						</div>
					</div>
				</header>
				<div class="banner-area">
					<div class="container">
						<div class="row height align-items-center">
							<div class="col-lg-12">
								<div class="banner-content">
									<!-- <h1 class="text-uppercase mb-10">Privacy Policy</h1> -->
									<p class="mb-30 text-justify"><?=$privacypolicy?></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script src="<?=DOMAIN_URL?>assets/js/vendor/jquery-2.2.4.min.js"></script>
	</body>
</html>
