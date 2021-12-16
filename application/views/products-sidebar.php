<div class="blog-sidebar stylecategory">

    <?php if(!empty($productcategorydata)){ ?>
        <div class="category">
            <h4>FILTERS</h4>
            <ul class="list-unstyled customscroll chromescroll checkboxes" id="categoryfilter">
            <?php foreach($productcategorydata as $category){ ?>
                <?php if(isset($IS_CATEGORY) && $IS_CATEGORY==1){    ?>
                    <li><a class="<?=($CategorySlug==$category['slug']?"active":"")?>" href="<?=FRONT_URL.CATEGORY_SLUG."/".$category['slug']?>" id="<?=$category['id']?>"><?=ucwords($category['name'])?></a></li>
                <?php }else if(isset($issearch) && $issearch!=""){    ?>
                    <!-- <li><a class="<?=(strtolower($category['name'])==strtolower($issearch)?"active":"")?>" href="javascript:void(0)" id="<?=$category['id']?>"><?=ucwords($category['name'])?></a></li> -->
                    <li>
                        <div class="sidebar-category">
                            <input id="category<?php echo $category['id']; ?>" type="checkbox" name="category<?php echo $category['id']; ?>" value="<?php echo $category['id']; ?>" class="first" <?=(strtolower($category['name'])==strtolower($issearch)?"checked":"")?>>
                            <label for="category<?php echo $category['id']; ?>"><?=ucwords($category['name'])?></label>
                        </div>
                    </li>

                <?php }else{ ?>
                    <li>
                        <div class="sidebar-category">
                            <input id="category<?php echo $category['id']; ?>" type="checkbox" name="category<?php echo $category['id']; ?>" value="<?php echo $category['id']; ?>" class="filtercategory">
                            <label for="category<?php echo $category['id']; ?>"><?=ucwords($category['name'])?></label>
                        </div>
                    </li>
                <?php }?>
            <?php } ?>
            </ul>
        </div>
    <?php } ?> 

    <div class="price-filter">
        <h4>Pricing Filter</h4>
        <div class="range-controls" style="padding: 0px 25px;">
            <input type="text" class="js-range-slider" name="my_range" value=""/>   
        </div>
    </div>
    <?php if(!empty($producttagdata)){ ?>
    <div class="tag">
        <h4>TAG CLOUD</h4>
        <ul class="list-inline" id="producttagdiv">
        <?php foreach($producttagdata as $tag){ ?>
            <?php if(isset($IS_TAGS) && $IS_TAGS==1){    ?>
                <li class="<?=($TagSlug==$tag['slug']?"active":"")?>" onclick="location.href='<?=FRONT_URL.PRODUCTTAG_SLUG.'/'.$tag['slug']?>'"><a href="<?=FRONT_URL.PRODUCTTAG_SLUG."/".$tag['slug']?>" id="<?=$tag['id']?>"><?=ucwords($tag['tag'])?></a></li>
            <?php }else { ?>
                <li><a href="javascript:void(0)" id="<?=$tag['id']?>"><?=ucwords($tag['tag'])?></a></li>
        <?php } } ?>
        </ul>
    </div>
    <?php } ?> 
</div>