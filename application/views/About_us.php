<!-- slider start here -->
<div class="process-bg" style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>" aria-label="<?=$coverimage?>">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<h1>About Us</h1>
                <ul class="breadcrumbs list-inline">
					<li><a href="index.html">Home</a></li>
					<li>About Us</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- slider end here -->

<div class="beloved-clients">
    <div class="container">
      <div class="row">
        <h1>We Focus On Brands</h1>
        <p>Westy is a design studio founded in London. Nowadays, we've grown and expanded our services, and have become a multinational firm, offering a variety of services and solutions Worldwide. Our agency can only be as strong as our people & because of this, our team have designed game changing products, consulted for companies as well as competed collegiately and professionally when it comes to sports.</p>
        <div class="col-sm-6 col-md-3 col-xs-12 texts">
          3.214<span>Happy Clients </span>
        </div>
        <div class="col-sm-6 col-md-3 col-xs-12 texts">
          5.154<span>Cups of Coffee </span>
        </div>
        <div class="col-sm-6 col-md-3 col-xs-12 texts">
          8.845<span>Working Hours</span>
        </div>
        <div class="col-sm-6 col-md-3 col-xs-12 texts">
          1.249<span>Awards </span>
        </div>
      </div>
    </div>
  </div>

	<?php if(!empty($testimonialsdata)){ ?>
	<div class="ourclient-page">
		<div class="parallax">
			<div class="scollmain section_wrap">
				<div class="imageeffect scroll_layers" style="background-image:url('<?=FRONT_URL?>assets/images/client-testi-bg.jpg'); transform:translate3d(0px, -60px, 0px)">&nbsp;</div>
				<div class="testibox">
					<div class="container">
						<h5>Testimonials</h5>
						<h2>Customer’s Stories</h2>
						<div class="lines">
							<div class="line1"></div>
							<div class="line2"></div>
						</div>
						<div class="client-testimonial owl-carousel">
						<?php foreach($testimonialsdata as $testimonial){ ?>
						<div class="col-sm-12 col-xs-12">
							<div class="testwhite">
								<div class="description"><i class="fa fa-quote-left" aria-hidden="true"></i> <?=$testimonial['testimonials']?></div>
								<div class="profile row">
									<div class="col-sm-3 col-xs-12">
										<img alt="<?=$testimonial['image']?>" title="<?=ucwords($testimonial['name'])?>" src="<?=($testimonial['image']!=""?TESTIMONIALS.$testimonial['image']:DEFAULT_PROFILE."Male-Avatar.png")?>" class="img-responsive"/>
									</div>
									<div class="col-sm-9 col-xs-12 paddleft">
										<h4><?=$testimonial['name']?></h4>
									</div>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<!--our client-->
<?php if(!empty($ourclientdata)){ ?>
<div id="carousel10">
  <div class="container">
    <div class="carousel10 owl-carousel">    
      <?php foreach($ourclientdata as $ourclient){ ?>  
  
           <div class="item">
                <div class="image">
                   <a href="<?=$ourclient['websiteurl']?>" target="_blank">
                   <img class="img-responsive" alt="<?=ucwords($ourclient['name'])?>" title="<?=ucwords($ourclient['name'])?>" src="<?=($ourclient['coverimage']!=""?OURCLIENT_COVER_IMAGE.$ourclient['coverimage']:DEFAULT_PROFILE."Male-Avatar.png")?>" class="img-responsive"/>
                   </a>
                 </div>                             
            </div>
            
        <?php }?>       
    </div>    
  </div>
</div>
<?php }?>