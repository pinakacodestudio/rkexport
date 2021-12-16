<!-- slider start here -->
<div class="process-bg" style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>" aria-label="<?=$coverimage?>">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<h1><?=$title?></h1>
                <ul class="breadcrumbs list-inline">
					<li><a href="<?=FRONT_URL?>">Home</a></li>
					<li><?=$title?></li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- slider end here -->

<!-- shop code start here -->
<div class="shop">
	<div class="container">
		<div class="row">
            <div class="col-md-12">
				<?php echo str_replace('&nbsp;', ' ', $wesitecontent['description']);?>
			</div>
        </div>
	</div>
</div>
<!-- shop end here -->