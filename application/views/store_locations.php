<!-- slider start here -->
<div class="process-bg" style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>" aria-label="<?=$coverimage?>">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<h1>Store Location</h1>
                <ul class="breadcrumbs list-inline">
					<li><a href="<?=FRONT_URL?>">Home</a></li>
					<li>Store Location</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- slider end here -->
<div class="shop">
	<div class="container">
	<?php foreach ($store_location as $row) { ?>
		<div class="col-md-4 col-sm-6 col-xs-12 p-xs mb-sm">
			<div class="contact-infomain p-md storelocationbox">
				<h4 class="location-font"><?=$row['name']?></h4>
				<ul class="information-list m-n p-n" style="list-style-type: none;">
					<li class="list pt-sm"><i class="fa fa-home"></i> <span><?=$row['address']?></span></li>
					<li class="list"><i class="fa fa-map-marker"></i> <span ">Location: <a style="color:#441e8c!important; href="javascript:void(0)" onclick="newLocation(<?=$row['latitude']?>,<?=$row['longitude']?>)"><?=$row['name']?></a></span></li>
				</ul>						
			</div>
		</div>
	<?php } ?>
	</div>
</div>		
	
<!--
<div id="map-canvas" style="width: 100%; height: 500px;"></div>-->