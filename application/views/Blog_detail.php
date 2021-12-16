<div class="blog-standard">
    <!-- slider start here -->
    <div class="process-bg"
        style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <h1 style="font-size: 45px;"><?=$title?></h1>
                    <ul class="breadcrumbs list-inline">
                        <li><a href="<?=FRONT_URL?>">Home</a></li>
                        <li><a href="<?=FRONT_URL."blog"?>">Blog</a></li>
                        <li>Blog Detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- slider end here -->

    <!-- blog start here -->
    <div class="blog">
        <div class="container">
            <div class="row">
                <div class="col-sm-3 hidden-xs rightbar">
                    <div class="blog-sidebar">

                        <?php if(count($sidebar['blogcategorydata']) > 0){ ?>
                            <div class="category">
                                <h4>CATEGORIES LIST</h4>
                                <ul class="list-unstyled">
                                <?php foreach($sidebar['blogcategorydata'] as $category){ ?>
                                    <li><a href="<?=FRONT_URL.'blog-category/'.$category['slug']?>"><?=ucwords($category['name'])?></a></li>
                                <?php } ?>
                                </ul>
                            </div>
                        <?php } ?> 
                        <?php if(count($sidebar['recentblogdata']) > 0){ ?>                       
                            <div class="recent_post">
                                <h4>Recent Blogs</h4>
                            
                                <ul class="list-unstyled">
                                <?php foreach($sidebar['recentblogdata'] as $blog){ ?>
                                    <li>
                                        <a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>">
                                            <img src="<?=BLOG.$blog['image']?>" class="img-responsive"
                                                alt="<?=$blog['image']?>" title="image"></a>
                                        <div class="caption">
                                            <h3><a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>"><?=$blog['title']?></a></h3>
                                            <div class="date"><?=date("F d, Y",strtotime($blog['createddate']))?></div>
                                        </div>
                                    </li>
                                <?php } ?>
                                </ul>
                            </div> 
                        <?php } ?>
                    </div>
                </div>

                <div class="col-sm-9 col-xs-12 blog-paddingleft">
                    <?php if(!empty($blogdata)){ ?>
                            <div class="box">
                                <div class="image">
                                    <a href="<?=base_url(uri_string());?>">
                                        <img src="<?=BLOG.$blogdata['image']?>" class="img-responsive" alt="<?=$blogdata['image']?>" />
                                    </a>
                                </div>
                                <div class="caption">
                                    <h2><a href="<?=base_url(uri_string());?>"><?=$blogdata['title']?></a></h2>
                                    <div class="date">
                                        <ul class="list-inline">
                                            <li><?=date("F d, Y",strtotime($blogdata['createddate']))?></li>
                                        </ul>
                                    </div>
                                    <p><?=ucfirst($blogdata['description'])?></p>
                                </div>
                            </div>

                    <?php } ?>                  
                </div>
            </div>
        </div>
    </div>
    <!-- blog end here -->
</div>