<style>
    .bottom-top i{
        position: absolute;
        top: 34%;
        left: 44%;
    }
</style>
<div class="blog-standard">
    <!-- slider start here -->
    <div class="process-bg"
        style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>" aria-label="<?=$coverimage?>">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <h1 style="<?=(isset($categoryslug)?"font-size:45px;":"")?>"><?=$title?></h1>
                    <ul class="breadcrumbs list-inline">
                        <li><a href="<?=FRONT_URL?>">Home</a></li>
                        <li><?=(isset($categoryslug)?"<a href='".FRONT_URL.'blog'."'>Blog</a>":"Blog")?></li>
                        <?php if(isset($categoryslug)){ ?>
                            <li><?=$title?></li>
                        <?php } ?>
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
                <?php if(!empty($sidebar['blogcategorydata']) && !empty($sidebar['recentblogdata'])){ ?>
                    <div class="col-sm-3 hidden-xs rightbar">
                        <div class="blog-sidebar">

                            <?php if(!empty($sidebar['blogcategorydata'])){ ?>
                                <div class="category">
                                    <h4>CATEGORIES LIST</h4>
                                    <ul class="list-unstyled">
                                    <?php foreach($sidebar['blogcategorydata'] as $category){ ?>
                                        <li><a href="<?=FRONT_URL.'blog-category/'.$category['slug']?>"><?=ucwords($category['name'])?></a></li>
                                    <?php } ?>
                                    </ul>
                                </div>
                            <?php } ?> 
                            <?php if(!empty($sidebar['recentblogdata'])){ ?>                       
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
                <?php } 
                if(!empty($sidebar['blogcategorydata']) && !empty($sidebar['recentblogdata'])){ ?> 
                <div class="col-sm-9 col-xs-12 blog-paddingleft">
                <?php }else{?> 
                <div class="col-sm-12 col-xs-12 blog-paddingleft">
                <?php }?> 
                    <?php $this->load->view('blog-ajax-data');?>        
                </div>


            </div>
        </div>
    </div>
    <!-- blog end here -->
</div>