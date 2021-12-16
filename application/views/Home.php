<!-- slider start here -->

<?php if(!empty($webbannerdata)){ ?>
<div class="slideshow owl-carousel">
  <?php foreach($webbannerdata as $banner){ ?>
    <div class="item">
      <div class="parallax">
        <div class="scollmain section_wrap">
          <div class="imageeffect scroll_layers" style="background-image:url('<?=BANNER.$banner['file']?>'); transform:translate3d(0px, -60px, 0px)" aria-label="<?=ucwords($banner['alttext'])?>">&nbsp;</div>
          <div class="container text-left">
            <div class="slidesdetail">
              <!-- <h6>Appino provide amazing & outstanding feature</h6> -->
              <h1><?=$banner['title']?></h1>
              <p><?=$banner['description']?></p>
              <?php if(!empty($banner['link']) && !empty($banner['buttontext'])){ ?>
                <div class="btn-group">
                  <button type="button" class="btn btn-primary" onclick="window.location.href='<?=urldecode($banner['link'])?>'"><i class="fa fa-long-arrow-right"></i> <?=ucwords($banner['buttontext'])?></button>
                  <!-- <button type="button" class="btn btn-default" onclick="location.href='#'">More About us</button> -->
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
</div> 
<?php } ?>
<!-- slider end here -->

<?php if(!empty($productsectiondata)){ ?>
<div class="shop">
		<div class="container">
    <?php foreach($productsectiondata as $section){ ?>
			<div class="row" style="margin-bottom: 30px;">
          <div class="col-sm-12 col-xs-12 col-md-12 text-center commontop">
           <!--  <h6>Product Portfolio</h6> -->
            <h2><?=ucwords($section['name'])?></h2>
            <div class="lines">
              <div class="line1"></div>
              <div class="line2"></div>
            </div>
            <p><?=ucfirst($section['description'])?></p>
            <br />
          </div>
          <div class="col-md-12 <?=($section['displaytype']==1?"product-section-carousel owl-carousel":"")?>" > 
            <?php foreach($section['products'] as $product){ ?>
              <div class="col-sm-6 col-md-3 col-xs-12" <?=($section['displaytype']==1?"style='width: 100%;'":"")?>>
                <div class="box">
                  <div class="image">
                    <!-- <div class="new tag">New</div> -->
                    <a href="<?=FRONT_URL."products/".$product['slug']?>"><img src="<?=PRODUCT.$product['image']?>" class="img-responsive" alt="<?=ucwords($product['productname'])?>" style="height: 166px;margin: 0 auto;"/></a>
                  </div>
                  <div class="caption">
                    <div class="cart-btn">
                      <?php if(WEBSITETYPE==1){ 
                          if($product['isuniversal']==1){  
                              $productdetaillink = "javascript:void(0)";
                          }else{
                              $productdetaillink = FRONT_URL."products/".$product['slug'];
                          } ?>
                          <a href="<?=$productdetaillink?>" class="btn-cart btn"><i class="icon icon-ShoppingCart"></i>Add to cart</a>
                      <?php } else {?>
                          <a href="<?=FRONT_URL."products/".$product['slug']?>" class="btn-cart btn"><i class="fa fa-comment-o"></i> Get a Quote</a>
                      <?php }?>
                    </div>
                    <div class="category">
                      <a href="<?=FRONT_URL.CATEGORY_SLUG.'/'.$product['categoryslug']?>"><?=ucwords($product['category'])?></a>
                    </div>
                    <h2><a href="<?=FRONT_URL."products/".$product['slug']?>"><?=strlen($product['productname']) > 20 ? substr(strip_tags(ucwords($product['productname'])),0,20)."..." : ucwords($product['productname']);?></a></h2>
                    <div class="price" style=""><?=CURRENCY_CODE.' '.numberFormat($product['pricewithdiscount'],2,',')?> 
                    <?php if($product['discount'] > 0){ ?>
                      <span class="old-price"><?=CURRENCY_CODE.' '.numberFormat($product['price'],2,',')?></span> <span class="offertex"><?=(int)$product['discount']?>% off</span>
                    <?php } ?>
                  </div>
                  </div>
                </div>
              </div>
            <?php } ?>
          </div>
      </div>
    <?php } ?>
    </div>
</div>
<?php } ?> 
<!-- 
<div class="wepassion">
    <div class="container">
      <div class="row">
        <div class="col-sm-6 col-xs-12">
          <img src="<?=FRONT_URL?>assets/images/mockup2.png" class="img-responsive" alt="img" title="img" />
        </div>
        <div class="col-sm-6 col-xs-12">
          <h2>We love what we do<br> & we do it with passion!</h2>
          <p>Westy is a design studio founded in London. Nowadays, we've grown and expanded our services, and have become a multinational firm, offering a variety of services.</p>
          <div class="lines">
            <div class="line1"></div>
            <div class="line2"></div>
          </div>
          <div class="row">
            <div class="col-sm-12 col-xs-12">
              <div class="counting"> 95% </div>
                <div class="progress-label">
                  <label>Web Design</label>  
                <div class="progress"> 
                  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="95" aria-valuemin="0" aria-valuemax="100" >  
                  </div>
                  </div>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12">
              <div class="counting"> 88% </div>
                <div class="progress-label">
                  <label>Branding</label>  
                <div class="progress"> 
                  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="88" aria-valuemin="0" aria-valuemax="100" >  
                  </div>
                  </div>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12">
              <div class="counting"> 99% </div>
                <div class="progress-label">
                  <label>Illustration</label>  
                <div class="progress"> 
                  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="99" aria-valuemin="0" aria-valuemax="100" >  
                  </div>
                  </div>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12">
              <div class="counting"> 90% </div>
                <div class="progress-label">
                  <label>Marketing</label>  
                <div class="progress"> 
                  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100" >  
                  </div>
                  </div>
                </div>
            </div>
          </div>
        </div>	
      </div>
    </div>
</div>

<div class="brands">
    <div class="">
      <div class="row margin0">
        <div class="col-sm-6 col-xs-12 padd0">
          <div class="bgimgs" style="background-image: url('<?=FRONT_URL?>assets/images/home-agency.jpg');"></div>
        </div>
        <div class="col-sm-6 col-xs-12 padd0">
          <div class="bgbox">
            <div id="brands" class="owl-carousel">
              <div class="item">
                <h6>We are westy</h6>
                <h2>We focus on brands, products & campaigns</h2>
                <p>Westy is a design studio founded in London. Nowadays, we've grown and expanded our services, and have become a multinational firm, offering a variety of services.</p>
              </div>
              <div class="item">
                <h6>Let’s Make Something Great</h6>
                <h2>We love what we do & we do it with passion!</h2>
                <p> After all, as described in Web Design Trends 2015 & 2016, vision dominates a lot of our subconscious interpretation of the world around us. On top of that, pleasing images create. </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>	
</div>
 -->
<?php if(!empty($blogdata)){ ?>
<div id="thoughts">
    <div class="container">
      <div class="row">
        <div class="col-sm-12 col-xs-12 text-center commontop">
          <h6>News, tips & more</h6>
          <h2>Our Thoughts !</h2>
          <div class="lines">
            <div class="line1"></div>
            <div class="line2"></div>
          </div>
          <p>Follow our latest news and thoughts which focuses exclusively on design, art, vintage, and<br> also work updates.</p>
        </div>
        <div class="thoughts owl-carousel">
        <?php $srno = 1;
          foreach($blogdata as $blog){ ?>
            <div class="item">
              <div class="col-sm-12 col-xs-12">
                <div class="box">
                  <div class="box-top">
                    <div class="image">
                      <a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>"><img src="<?=BLOG.$blog['image']?>" class="img-responsive" alt="<?=$blog['image']?>" style="height: 181px;width: 100%;"/></a>
                    </div>
                    <div class="caption" style="min-height: 305px;">
                    <?php if($blog['category']!=""){ ?>
                      <div class="onhover">
                        <div class="desc">
                          <a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>"><?=$blog['category']?></a>
                        </div>
                      </div>
                    <?php } ?>
                      <h2><a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>"><?=strlen($blog['title']) > 60 ? substr(strip_tags($blog['title']),0,60)."..." : $blog['title'];?></a></h2>
                      <p><?=strlen($blog['description']) > 400 ? substr(strip_tags($blog['description']),0,200)."..." : $blog['description'];?></p>
                      <div class="date"><?=date("F d, Y",strtotime($blog['createddate']))?></div>
                    </div>
                  </div>
                  <div class="box-inner text-center">
                    <a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>">Read More </a>
                  </div>
                </div>
              </div>
            </div>
          
          <?php $srno++; 
        }  ?>   
        </div>
      </div>
    </div>
</div>
<?php } ?>  
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
                      <!-- <div class="company">7oroof Agency </div> -->
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
</div>
<?php } ?>
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