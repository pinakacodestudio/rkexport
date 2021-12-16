<div id="bloglist">
    <?php
    if(!empty($blogdata)){
        $srno = 1;
        foreach($blogdata as $blog){?>
        
                <div class="box">
                    <div class="image">
                        <a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>">
                            <img src="<?=BLOG.$blog['image']?>" class="img-responsive" alt="<?=$blog['image']?>" />
                        </a>
                    </div>
                    <div class="caption">
                        <h2><a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>"><?=$blog['title']?></a></h2>
                        <div class="date">
                            <ul class="list-inline">
                                <li><?=date("F d, Y",strtotime($blog['createddate']))?></li>
                            </ul>
                        </div>
                        <p><?=strlen($blog['description']) > 400 ? substr(strip_tags($blog['description']),0,400)."..." : $blog['description'];?></p>
                        <div class="readmore"><a href="<?=FRONT_URL.'blog-detail/'.$blog['slug']?>">Read More</a></div>
                    </div>
                </div>

        <?php $srno++; 
        }  ?>                  
        <div class="col-sm-12 text-center">
            <?=$link;?>
        </div>
    <?php }else{ ?>
        <!-- <div class="box">
            <div class="caption" style="padding:20px 40px;">
                <h2 style="margin:0"><?php echo "Blog not available."; ?></h2>
            </div>
        </div> -->
        <?php $this->load->view("No_result_found"); ?>
    <?php } ?>
</div>  