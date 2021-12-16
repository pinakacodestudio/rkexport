<footer>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-3 col-xs-12">
                <h3>About</h3>
                <p></p>
                <div class="newsletter" id="newsletter_div">
                    <div class="input-group">
                        <input id="newsletteremail" name="newsletter" value="" placeholder="Subscribe Our Newsletter" class="form-control" type="text"/>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-default" onclick="subscribenewsletter()"><i class="fa fa-long-arrow-right"></i></button>
                        </span>
                    </div>
                </div>
                <span id="subscribealert" style="color:#ff2a2a;padding:5px 10px;"></span>
            </div>
            <div class="col-sm-6 col-md-3 col-xs-12">
                <h3>Quick links</h3>
                <ul class="list-unstyled social">
                    <?php if(!empty($footerquicklink)){
                        foreach($footerquicklink as $links){ ?>
                            <li style="padding: 3px 0px"><a href="<?php echo FRONT_URL.$links['slug']; ?>"><?php echo $links['title']; ?></a></li>
                        <?php } ?>
                    <?php } ?> 
                </ul>
            </div>
            <div class="col-sm-6 col-md-3 col-xs-12">
                <h3>Our Products</h3>
                <!-- <div class="twitter-detail">
						<i class="fab fa-twitter"></i> Great Team and friends &nbsp;<a href="#" target="_blank">@VivaTweets</a>&nbsp; &nbsp;<a href="#" target="_blank">@themediavillage</a>&nbsp; &nbsp;<a href="#" target="_blank">@redfernmedia</a>&nbsp; &nbsp;<a href="#" target="_blank">@WhitakerMuseum</a>&nbsp;
                    <div class="post-date">3 years ago</div>
                </div>
                <div class="twitter-detail">
						<i class="fab fa-twitter"></i> &nbsp;<a href="#" target="_blank">@pepperstreet</a>&nbsp; Hi there, could you take a screenshot for us and let us know the error, we cannot see.Thanks, JoomlaMan
                    <div class="post-date">3 years ago</div>
                </div> -->
                <ul class="list-unstyled social">
                    <?php if(!empty($footerproducts)){
                        foreach($footerproducts as $links){ ?>
                            <li style="padding: 3px 0px;"><a href="<?php echo FRONT_URL.$links['slug']; ?>"><?php echo $links['title']; ?></a></li>
                        <?php } ?>
                    <?php } ?> 
                </ul>
            </div>
            <div class="col-sm-6 col-md-3 col-xs-12">
                <h3>Contact Us</h3>
                <!-- <ul class="list-inline insta"> 
                    <li><a href="#"><img alt="img" title="img" src="<?=DOMAIN_URL?>assets/images/footer/inst_img1.jpg" class="img-responsive"/></a></li>
                    <li><a href="#"><img alt="img" title="img" src="<?=DOMAIN_URL?>assets/images/footer/inst_img2.jpg" class="img-responsive"/></a></li>
                    <li><a href="#"><img alt="img" title="img" src="<?=DOMAIN_URL?>assets/images/footer/inst_img3.jpg" class="img-responsive"/></a></li>
                    <li><a href="#"><img alt="img" title="img" src="<?=DOMAIN_URL?>assets/images/footer/inst_img4.jpg" class="img-responsive"/></a></li>
                    <li><a href="#"><img alt="img" title="img" src="<?=DOMAIN_URL?>assets/images/footer/inst_img5.jpg" class="img-responsive"/></a></li>
                    <li><a href="#"><img alt="img" title="img" src="<?=DOMAIN_URL?>assets/images/footer/inst_img1.jpg" class="img-responsive"/></a></li>
                </ul> -->

               
                <ul class="list-inline social">                               
                    <li style="color: #fff;">
                        <h5><i class="fas fa-home"></i> Address</h5>
                        <span><?=COMPANY_ADDRESS?></span>
                    </li>
                    <li style="color: #fff;">
                        <h5><i class="fas fa-envelope"></i> Email</h5>
                        <?php $emailArr = explode(",", COMPANY_EMAIL); 
                            foreach($emailArr as $email){ ?>
                                <span><a href="mailto:<?=$email?>"><?=$email?></a></span><br>
                        <?php } ?>
                    </li>
                    <li style="color: #fff;">
                        <h5><i class="fas fa-mobile"></i> Contact Number</h5>
                        <?php $mobileArr = explode(",", COMPANY_MOBILENO); 
                            foreach($mobileArr as $mobile){ ?>
                                <span> <a href="tel:+91<?=$mobile?>">+91<?=$mobile?></a></span><br>
                        <?php } ?>
                    </li>
                </ul>
                
            </div>
        </div>
    </div>
    <div class="powered">
        <div class="container">
            <div class="row">
                <div class="pull-left">
                    <p>Copyright &copy; <a href="<?=COMPANY_WEBSITE?>" target="_blank"> <?=COMPANY_NAME?></a> <?=date('Y')?>. All rights reserved.</p>
                </div>
                <div class="pull-right">
                    <ul class="list-inline social"> 
                        <?php if(!empty($footerlinks)){
                            foreach($footerlinks as $links){ ?>
                                <li><a href="<?php echo FRONT_URL.$links['slug']; ?>"><?php echo $links['title']; ?></a></li>
                            <?php } ?>
                        <?php } ?> 
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>