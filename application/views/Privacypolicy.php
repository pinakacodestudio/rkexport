<?php 
$headerData = $this->admin_headerlib->data();
?>
<!DOCTYPE html>
<html lang="zxx" class="no-js">
<head>
	<!-- Mobile Specific Meta -->
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Favicon-->
	<?=$headerData['favicon']; ?>
	<!-- meta character set -->
	<meta charset="UTF-8">
	<!-- Site Title -->
	<title><?=$title." - ".COMPANY_NAME ?></title>

	<link href="https://fonts.googleapis.com/css?family=Poppins:300,500,600" rel="stylesheet">
		<!--
		CSS
		============================================= -->
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/linearicons.css">
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/owl.carousel.css">
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/font-awesome.min.css">
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/nice-select.css">
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/magnific-popup.css">
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/bootstrap.css">
		<link rel="stylesheet" href="<?=DOMAIN_URL?>assets/css/main.css">
	</head>
	<body>
		<div class="main-wrapper-first">
			<div class="hero-area relative">
				<header>
					<div class="container">
						<div class="header-wrap">
							<div class="header-top d-flex justify-content-between align-items-center">
								<div class="logo">
									<a href="<?=COMPANY_WEBSITE?>"><img src="<?php echo MAIN_LOGO_IMAGE_URL.COMPANY_LOGO; ?>" alt="<?php echo COMPANY_NAME; ?>" class="img-fluid"></a>
								</div>
							</div>
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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
		<script src="<?=DOMAIN_URL?>assets/js/vendor/bootstrap.min.js"></script>
		<script src="<?=DOMAIN_URL?>assets/js/jquery.ajaxchimp.min.js"></script>
		<script src="<?=DOMAIN_URL?>assets/js/owl.carousel.min.js"></script>
		<script src="<?=DOMAIN_URL?>assets/js/jquery.nice-select.min.js"></script>
		<script src="<?=DOMAIN_URL?>assets/js/jquery.magnific-popup.min.js"></script>
		<script src="<?=DOMAIN_URL?>assets/js/main.js"></script>
	</body>
</html>
