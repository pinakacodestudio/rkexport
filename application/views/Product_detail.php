<?php

$whatsupurl = '<a href="https://web.whatsapp.com/send?text='.base_url(uri_string()).'" target="_blank" data-action="share/whatsapp/share"  class="whatsapp" title="Share on Whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>';
if($this->agent->is_mobile()){
    
    $whatsupurl = '<a href="whatsapp://send?text='.base_url(uri_string()).'" data-action="share/whatsapp/share"  class="whatsapp"><i class="fa fa-whatsapp" aria-hidden="true"></i></a>';
}
?>
<style>
.shopdetail .detail .social li a, .bottom-top {
    display: block !important;
}
.fa-minus,.fa-plus{
    padding-top: 28%;
}
.shop {
    padding: 15px;
    background: #fff;
    margin-top: 5px;
}

wprice-wrap {
    background: #fff;
}
.key-info-line {
    float: left;
    width: 100%;
    min-height: 1px;
    position: relative;
}

.lineprice {
    position: relative;
    vertical-align: middle;
    width: 100%;
    float: left;
    border-top: 1px solid #e9e9e9;
    border-bottom: 1px solid #e9e9e9;
}
.clearfix {
    clear: both;
}
.wprice-list {
    height: 82px;
    position: relative;
    margin-left: 60px;
    margin-right: 0px;
    overflow-x: hidden;
    overflow-y: hidden;
    max-width: 500px;
}
.wprice-list ul {
    height: 84px;
    white-space: nowrap;
	overflow-x: auto;
	overflow-y: hidden;
    line-height: 19px;
}
.wprice-line .wprice-list li:first-child {
    border-left-color: #f9f9f9;
}
.wprice-line .wprice-list li.current {
    background-color: #ffeb9c;
}
.wprice-line .wprice-list li {
    max-width: 180px;
    padding: 0 20px;
    font-size: 11px;
    color: #333;
    height: 84px;
    position: relative;
    cursor: pointer;
    border: none;
    width: 100%;
    display: inline-block;
}
.wprice-line .col1 {
    width: 100%;
    font-size: 16px;
    color: #000;
    top: 13px;
    position: relative;
}
.wprice-line .col1, .wprice-line .col2, .wprice-line .col3 {
    display: block;
    line-height: 17px;
    height: 17px;
    overflow: hidden;
}
.wprice-line .col1, .wprice-line .col2, .wprice-line .col3 {
    display: block;
    line-height: 17px;
    height: 17px;
    overflow: hidden;
}

.wprice-line .col2 {
    color: #999;
    font-size: 12px;
    top: 35px;
}
.wprice-line .col2 .line-center {
    text-decoration: line-through;
}
.wprice-line .col1, .wprice-line .col2, .wprice-line .col3 {
    display: block;
    line-height: 17px;
    height: 17px;
    overflow: hidden;
}
.wprice-line .col2, .wprice-line .col3 {
    position: absolute;
}
.wprice-line .col3 {
    font-size: 14px;
    top: 57px;
    color: #666;
}
.js-wholesale-list{
    list-style-type: none;
}
.wprice-line .wprice-list li.current .col1 {
    font-size: 18px;
    font-weight: 700;
}
.wprice-line .wprice-line-tit {
    position: relative;
    margin-top: 12px;
    display: inline-block;
    vertical-align: middle;
    width: 50px;
    padding-left: 10px;
    margin-left: -100%;
    float: left;
    color: #333;
    cursor: pointer;
}
.wprice-line .wprice-line-tit .usd {
    font-size: 14px;
    color: #333;
}
.s-arrow-flow {
    position: absolute;
    width: 30px;
    height: 82px;
    right: 0px;
    top: 0;
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAABSCAYAAABDqmS1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA4RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTQ1IDc5LjE2MzQ5OSwgMjAxOC8wOC8xMy0xNjo0MDoyMiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDozZjQ3ZGIwOS0xN2ViLWFlNDctODgxOS05OGU5MTUyY2Q2ZTEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6RkU0Q0Q4QjYyQUYwMTFFQTk0OENCMzcyQTBFMEQ2MkIiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6RkU0Q0Q4QjUyQUYwMTFFQTk0OENCMzcyQTBFMEQ2MkIiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MTVjMGQyYTctOWYwYS1lZjRhLTgzYzgtNmVmMjU1NzMzNGIwIiBzdFJlZjpkb2N1bWVudElEPSJhZG9iZTpkb2NpZDpwaG90b3Nob3A6Y2UyNmIzMjUtNDBiMy1iOTQwLTliMmYtZjk3ZjczMzczZmRlIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+LP9dvQAAAGlJREFUeNrszUsKgDAMQMEo+F96/xt6hxqhBRH3upjCI7TQTFdK2SNiycZsyuZ6b7O1Pubb2/3PXPe1rv1DtmVHHx8dMBgMBoPBYDAYDAaDwWAwGAwGg8FgMBgMBoPBYDAYDAb/Az4FGAAXSgWMJ/2RvwAAAABJRU5ErkJggg==);
    background-repeat: repeat-y;
}
.wprice-line .s-arrow {
    width: 30px;
    overflow: hidden;
    vertical-align: middle;
    display: block;
    text-align: center;
}
.disnone {
    display: none;
}
.s-ltarrow, .s-rtarrow {
    margin: auto;
    width: 9px;
    height: 16px;
    background: url(//css.dhresource.com/nstatic/n-proddetail-server/image/iconbg.png?85d2=) no-repeat;
    cursor: pointer;
    position: absolute;
}
.s-ltarrow {
    background-position: -291px -442px;
    right: 0px;
    top: 16px;
}
.s-rtarrow {
    background-position: -307px -442px;
    right: 0px;
    top: 50px;
}
.s-ltarrow:hover {
    background-position: -263px -442px;
}
.s-rtarrow:hover {
    background-position: -275px -442px;
}
.wprice-list ul::-webkit-scrollbar {
  display: none;
}
</style>
<!-- slider start here -->
<div class="process-bg hidden-sm  hidden-xs"
    style="padding: 100px 0 0px !important;<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>">
</div>
<!-- slider end here -->

<!-- shop code start here -->
<div class="product_single_main">
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="alert alert-dismissible fade in" id="alert" style="display:none;"></div>
                <?php if(!empty($productdata)){ ?>
                <input type="hidden" id="productid" name="productid" value="<?=$productdata['id']?>">
                <input type="hidden" id="productpriceid" name="productpriceid" value="<?=$productdata['priceid']?>">
                <input type="hidden" id="isuniversal" name="isuniversal" value="<?=$productdata['isuniversal']?>">
                <input type="hidden" id="productstock" name="productstock" value="<?=$productdata['stock']?>">
                <input type="hidden" id="discount" name="discount" value="<?=(!empty($productdata['discount'])?$productdata['discount']:0)?>">
                <input type="hidden" id="minimumorderqty" name="minimumorderqty" value="<?=($productdata['isuniversal']==1?$productdata['minimumorderqty']:'')?>">
                <input type="hidden" id="maximumorderqty" name="maximumorderqty" value="<?=($productdata['isuniversal']==1?$productdata['maximumorderqty']:'')?>">
                <input type="hidden" id="quantitytype" name="quantitytype" value="<?=$productdata['quantitytype']?>">
                <input type="hidden" id="pricetype" name="pricetype" value="<?=($productdata['isuniversal']==1)?$productdata['pricetype']:""?>">
                <input type="hidden" id="referencetype" value="<?=($productdata['isuniversal']==1?$productdata['referencetype']:'')?>">
                <input type="hidden" id="referenceid" value="<?=($productdata['isuniversal']==1 && $productdata['pricetype']==0 && !empty($productdata['multipleprice'])?$productdata['multipleprice'][0]['id']:'')?>">
                <input type="hidden" id="group-quantity" value="">

                <div class="col-md-12 col-sm-12 col-xs-12 hidden-xs">
                    <div class="product-payment-details">
                        <ul class="breadcrumbs list-inline" style="margin-top: 0;">
                            <li><a href="<?=FRONT_URL?>">Home</a></li>
                            <li><a href="<?=FRONT_URL.CATEGORY_SLUG."/".$productdata['categoryslug']?>"><?=ucwords($productdata['category'])?></a></li>
                            <li><?=ucwords($productdata['productname'])?></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body store-body scroll81-main">
                    <div class="col-md-6 col-sm-12 col-xs-12 scroll81-sub">
                        <div class="product-info">
                            <div class="product-gallery hidden-xs mb-md">
                                <div class="product-gallery-thumbnails pr-xs chromescroll" style="overflow-y: auto;max-height: 100%;width: 90px;<?php if(!empty($productdata['images']) && count($productdata['images'])<=1){ echo "display:none;"; }?>">
                                    <ol class="thumbnails-list list-unstyled">
                                        <?php if(!empty($productdata['images'])){ 
                                            foreach($productdata['images'] as $k=>$image){ 
                                                if($image['type']==1){ 
                                                    if(!file_exists(PRODUCT_PATH.$image['filename'])){
                                                        $image['filename'] = PRODUCTDEFAULTIMAGE; 
                                                    } 
                                                    ?>
                                                    <li class="productslideimage <?=($k==0?'active':'')?>"><img src="<?=PRODUCT.$image['filename']?>" class="img-responsive" alt="<?=$image['filename']?>" title="<?=ucwords($productdata['productname'])?>"></li>
                                        <?php } } }else{ ?>
                                        <li class="productslideimage active"><img src="<?=PRODUCT.PRODUCTDEFAULTIMAGE?>" class="img-responsive"
                                                    alt="<?=$productdata['productname']?>"
                                                    title="<?=ucwords($productdata['productname'])?>"></li>
                                        <?php } ?>
                                    </ol>
                                </div>
                                <div class="product-gallery-featured">
                                    <div style="margin: 0 auto;">
                                    <?php if(!empty($productdata['images'])){ 
                                         if($productdata['images'][0]['type']==1){ 
                                            if(!file_exists(PRODUCT_PATH.$productdata['images'][0]['filename'])){
                                                $productdata['images'][0]['filename'] = PRODUCTDEFAULTIMAGE; 
                                            } ?>
                                            <img src="<?=PRODUCT.$productdata['images'][0]['filename']?>" class="img-responsive" alt="<?=$productdata['images'][0]['filename']?>" title="<?=ucwords($productdata['productname'])?>" style="width: 100%;height: 100%;max-height: 440px;">
                                    <?php } }else{ ?>
                                    <img src="<?=PRODUCT.PRODUCTDEFAULTIMAGE?>" class="img-responsive"
                                                    alt="<?=$productdata['productname']?>"
                                                    title="<?=ucwords($productdata['productname'])?>" style="width: 100%;height: 100%;max-height: 440px;">
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="visible-xs mb-xl">
                            <?php if(!empty($productdata['images'])){ ?>
                                <div class="productdetailcarousel owl-carousel">
                                    <?php foreach($productdata['images'] as $image){
                                            if($image['type']==1){ 
                                                if(!file_exists(PRODUCT_PATH.$image['filename'])){
                                                    $image['filename'] = PRODUCTDEFAULTIMAGE; 
                                                } 
                                                $src = PRODUCT.$image['filename'];
                                                $alt = $image['filename'];

                                            } ?>
                                            <img src="<?=$src?>" alt="<?=$alt?>" style="width: 100%;height: 100%;margin: 0 auto;">
                                        <?php }  ?>
                                    </div> 
                                <?php 
                                }else{ 
                                    $src = PRODUCT.PRODUCTDEFAULTIMAGE;
                                    $alt = $productdata['productname'];
                                    ?>
                                    <img src="<?=$src?>" alt="<?=$alt?>" style="width: 100%;height: 100%;margin: 0 auto;">
                            <?php  } ?>
                            </div>  
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="product-payment-details">
                            <ul class="breadcrumbs list-inline visible-xs" style="margin-top: 0;">
                                <li><a href="<?=FRONT_URL?>">Home</a></li>
                                <li><a href="<?=FRONT_URL.CATEGORY_SLUG."/".str_replace(" ","-",strtolower($productdata['category']))?>"><?=ucwords($productdata['category'])?></a></li>
                                <li><?=ucwords($productdata['productname'])?></li>
                            </ul>
                            <h4 class="product-title mt-n"><?=ucwords($productdata['productname'])?></h4>
                            <div id="skudiv">
                                <?php if($productdata['sku']!="" && $productdata['isuniversal']==1){ ?>
                                <ul class="sku list-inline" style="margin-top: 0;">
                                    <li><b>SKU</b></li>
                                    <li><?=$productdata['sku']?></li>
                                </ul>
                            <?php } ?>
                            </div>
                            <?php if(!empty($productdata['producttagdata'])){ ?>
                            <ul class="producttags list-inline" style="margin-top: 0;">
                                <li><b>Tags</b></li>
                                <li>
                                <?php 
                                $tagdata = array();
                                foreach($productdata['producttagdata'] as $key=>$producttagdata){
                                    $tagdata[] = '<a href="'.FRONT_URL.PRODUCTTAG_SLUG.'/'.$producttagdata['slug'].'">'.ucwords($producttagdata['tag']).'</a>';
                                } 
                                echo implode(', ',$tagdata);
                                ?>
                                </li>
                            </ul>
                            <?php } ?>
                            <div class="rating">
								<div class="pro_review"><?=$productdata['productreview']?><img src="<?=FRONT_URL?>assets/images/star.png"></div>
                                <a href="#" class="review-click"><?=numberFormat($productdata['productratingcount'])?> Ratings & <?=numberFormat($productdata['productreviewcount'])?> Reviews </a> / <a href="#tab-writeareview" data-toggle="tab" class="review-click" id="writeareviewbtn">Write a review</a>
                            </div>

                            <div class="cart-price">
                                <div id="multiplequantityprice" style="margin-top: 20px;<?php if($productdata['isuniversal']==1 && $productdata['pricetype']==1 && !empty($productdata['multipleprice'])){ echo "display:block;"; }else{ echo "display:none;"; } ?>">
                                    <div class="wprice-wrap clearfix">
                                        <div class="key-info-line wprice-line">
                                            <div class="lineprice clearfix j-line-price">
                                                <div class="wprice-list js-wholesale-box">
                                                    <ul class="js-wholesale-list basic" style="">
                                                        <?php foreach($productdata['multipleprice'] as $index=>$comboprice){
                                                            if(MANAGE_DECIMAL_QTY==1){
                                                                $Qty = $qty = number_format($comboprice['quantity'],2,'.','');
                                                                if($productdata['quantitytype']==0){
                                                                    $Qty = number_format($comboprice['quantity'],2,'.','')."+";
                                                                }
                                                            }else{
                                                                $Qty = $qty = (int)$comboprice['quantity'];
                                                                if($productdata['quantitytype']==0){
                                                                    $Qty = (int)$comboprice['quantity']."+";
                                                                }
                                                            }
                                                            $price = $comboprice['price'];
                                                            $discount = $comboprice['discount'];
                                                            $withdiscountprice = $save = "";
                                                            $discprice = 0;
                                                            if($discount>0){
                                                                $discprice = $comboprice['price'];
                                                                $price = ($price - ($price * $discount / 100));
                                                                $withdiscountprice = CURRENCY_CODE.numberFormat($comboprice['price'],2,',');
                                     
                                                                if($discount<100){
                                                                    $save = "<span style='color:green;'>".$discount."% Off</span>";
                                                                }else{
                                                                    $save = "<span style='color:green;'>Free</span>";
                                                                }
                                                            }
                                                            ?>
                                                            <li class="<?=($index==0?"current":"")?>" price="<?=$price?>" discprice="<?=$discprice?>" discount="<?=$discount?>" data-qty="<?=$qty?>" data-referencetype="<?=($productdata['isuniversal']==1?$productdata['referencetype']:'')?>" data-referenceid="<?=$comboprice['id']?>">
                                                                <span class="col1"><?=CURRENCY_CODE.numberFormat($price,2,',')?></span>
                                                                <span class="col2"><span class="line-center"><?=$withdiscountprice?></span> <?=$save?></span>
                                                                <span class="col3"><?=$Qty?> Qty</span>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                                <div class="s-arrow-flow"></div>
                                                <div class="s-arrow">
                                                    <div class="s-ltarrow disnone js-prev"></div>
                                                    <div class="s-rtarrow js-next"></div>
                                                </div>
                                            </div>
                                            <div class="wprice-line-tit j-unit-price-p" event-type="click" spm-index="usd">
                                                <span class="j-usd usd">Price</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 20px;">
                                <form id="variant-form" name="variant-form">
                                    <?php
                                    if(!empty($productdata['variants']) && $productdata['isuniversal']==0){
                                        foreach($productdata['variants'] as $vk=>$variants){ ?>
                                            <div class="col-md-12 variants">
                                                <label class="col-md-3 variants-label"><?=$variants['attributename']?></label>

                                                <div class="col-md-9">
                                                    <?php
                                                            $variantsname = explode(",",$variants['variantnames']);
                                                            $variantids = explode(",",$variants['variantids']);
                                                            
                                                            foreach($variantsname as $k=>$v){ ?>
                                                    <input type="checkbox" name="variant<?=$vk?>" id="variant_<?=$vk."_".$k?>"
                                                        style="display:none;" class="variantbtn" value="<?=$variantids[$k]?>">
                                                    <a href="javascript:void(0)" title="<?=ucwords($v)?>">
                                                        <label for="variant_<?=$vk."_".$k?>"><span id="box_<?=$vk."_".$k?>"
                                                                class="variantbox"><?=ucwords($v)?></span>
                                                            <label></a>
                                                    <input type="hidden" id="variantid_<?=$vk."_".$k?>"
                                                        value="<?=$variantids[$k]?>">
                                                    <?php } ?>
                                                </div>
                                            </div>
                                    <?php }

                                        if(!empty($productdata['variantprice'])){ ?>
                                            <div id="combination" style="display:none;">
                                                <?=json_encode($productdata['variantprice'])?></div>
                                    <?php }
                                    }
                                ?>
                                </form>
                            </div>
                            <div class="cart-price">
                                <div id="singlequantityprice">
                                    <span id="productprice">
                                        <?php if($productdata['isuniversal']==1 && !empty($productdata['multipleprice'])){
                                            if($productdata['multipleprice'][0]['discount'] > 0){ 
                                                echo CURRENCY_CODE.numberFormat(($productdata['multipleprice'][0]['price']-($productdata['multipleprice'][0]['price']*$productdata['multipleprice'][0]['discount']/100)),2,',');
                                            }else{
                                                echo CURRENCY_CODE.numberFormat($productdata['multipleprice'][0]['price'],2,',');
                                            }
                                        } ?>
                                    </span> 
                                    <span id="discount_div">
                                        <?php if($productdata['isuniversal']==1 && $productdata['discount'] > 0 && !empty($productdata['multipleprice'])){ ?>
                                            <span class="old-price"><?=CURRENCY_CODE.numberFormat($productdata['multipleprice'][0]['price'],2,',')?></span>
                                            <span class="offertex"><?=(int)$productdata['multipleprice'][0]['discount']?>% off</span>
                                        <?php } ?>
                                    </span>
                                </div>
                            </div>
                            <div class="quantity" style="padding-top: 30px;border:none;position: relative;">
                                <?php if(WEBSITE==1 && WEBSITETYPE==1){ ?>
                                    <div class="col-md-12 col-xs-12 col-sm-12 pull-left p-n" style="position: absolute;top: 0px;">
                                        <span id="productstockerror" style='color:red;font-weight:500;font-size: 18px;'>
                                            <?php if($productdata['isuniversal']==1 && $productdata['stock'] <= 0){
                                                    echo "Out of Stock";
                                                } ?>
                                        </span>
                                    </div>
                                    <div class="col-md-3 col-xs-6 col-sm-3 pull-left p-n">
                                        <p class="qtypara">
                                            <!-- <span class="quantity-label">Quantity:</span> -->
                                            <span id="minus" class="btn minus"><i class="fa fa-minus"></i></span>
                                            <input type="text" name="quantity" value="1" size="2" id="input-quantity"
                                                class="form-control" onkeypress="<?=(MANAGE_DECIMAL_QTY==1?'return decimal_number_validation(event, this.value,8);':'return isNumber(event);')?>" autocomplete="off" style="<?=($productdata['quantitytype']==1?"pointer-events: none;":"")?>" <?=($productdata['quantitytype']==1?"readonly":"")?>>
                                            <span id="add" class="btn add"><i class="fa fa-plus"></i></span>
                                        </p>
                                    </div>
                                    <div class="col-md-6 col-xs-6 col-sm-3 p-n pl-xs">
                                        <button type="button" id="btnaddtocart" class="button color" onclick="checkstock()"
                                            <?=(($productdata['isuniversal']==1 || $productdata['isuniversal']==1 && $productdata['stock']<=0)?"disabled":"")?>>Add to cart</button>
                                    </div>
                                    <div class="col-md-12 col-xs-12 col-sm-12 p-n pt-sm mb-sm">
                                        <span id="error-alert" style="color:red;font-size: 14px;"></span>
                                    </div>
                                    <?php } else {?>
                                    <div class="pull-left">
                                        <button type="button" class="button color"   onclick="getopenmodel()" >Get a Quote</button>
                                    </div>
                                <?php }?>
                            </div>
                            <?php if(!empty($productdata['returnpolicytitle']) || !empty($productdata['replacementpolicytitle'])){?>
                            <div class="col-md-12 col-sm-12 col-xs-12 p-n mb-sm">
                                <ul class="policytxt">
                                    <?php if(!empty($productdata['returnpolicytitle'])) { ?>
                                        <li>
                                            <div><span id="returnpolicytitle"><?=ucfirst($productdata['returnpolicytitle'])?></span> <a href="javascript:void(0)" onclick="openpolicymodal()"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a>
                                            </div>
                                            <div id="returnpolicydescription" style="display:none;"><?=$productdata['returnpolicydescription']?></div>
                                        </li>
                                    <?php }?>
                                    <?php if(!empty($productdata['replacementpolicytitle'])) { ?>
                                        <li>
                                            <div><span id="replacementpolicytitle"><?=ucfirst($productdata['replacementpolicytitle'])?></span> <a href="javascript:void(0)" onclick="openpolicymodal(1)"><i class="fa fa-question-circle-o" aria-hidden="true"></i></a></div>
                                            <div id="replacementpolicydescription" style="display:none;"><?=$productdata['replacementpolicydescription']?></div>
                                        </li>
                                    <?php }?>
                                </ul>
                                <div class="modal fade" id="policyModal" tabindex="-1" role="dialog" aria-labelledby="policyModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute;top: 15px;right: 15px;">
                                                <span aria-hidden="true"><i class="fa fa-times"></i></span>
                                                </button>
                                            </div>
                                            
                                            <div class="modal-body" style="width:100%;float: left;overflow-y: auto;max-height:400px;word-break: break-all;text-align: justify;">
                                                <div id="policydescription"> </div>
                                            </div>
                                            <div class="modal-footer"></div>
                                        </div>
                                    </div>
                                </div>
					        </div>
                            <?php } ?>
                            <?php if($productdata['shortdescription']!=""){?>
                            <div class="av_offers_main">
								<div class="av_offers_tital">Description</div>
								<div class="av_offers_in"><?=ucfirst($productdata['shortdescription'])?></div>
                            </div>
                            <?php } ?>
                            <div class="reviews_rating">
                                
                                <div class="col-md-6 col-sm-6 col-xs-12 mb-md">
                                    <div class="rating-num-cn">
                                    <h1 class="rating-num"> <?=$productdata['productreview']?></h1>
                                    
                                    <div class="rating star-ico">
                                        <img src="<?=FRONT_URL?>assets/images/star-<?=($productdata['productreview']>=1?"on":"off")?>.png" alt="">
                                        <img src="<?=FRONT_URL?>assets/images/star-<?=($productdata['productreview']>=2?"on":"off")?>.png" alt="">
                                        <img src="<?=FRONT_URL?>assets/images/star-<?=($productdata['productreview']>=3?"on":"off")?>.png" alt="">
                                        <img src="<?=FRONT_URL?>assets/images/star-<?=($productdata['productreview']>=4?"on":"off")?>.png" alt="">
                                        <img src="<?=FRONT_URL?>assets/images/star-<?=($productdata['productreview']>=5?"on":"off")?>.png" alt="">
                                    </div>

                                    <div class="ratings_total"><?=numberFormat($productdata['productratingcount'])?> Ratings & <?=numberFormat($productdata['productreviewcount'])?> Reviews </div>
                                </div>
                                </div>

                                <div class="col-md-6 col-sm-5 col-xs-12">
                                    <div class="row">
                                        <div class="side">
                                            <div>5 <i class="fa fa-star"></i></div>
                                        </div>
                                        <div class="middle">
                                            <div class="bar-container">
                                            <div class="bar-5" style="width:<?=number_format(($productdata['productreviewcount']>0?($productdata['fivereviewcount'] * 100 / $productdata['productreviewcount']):0),'2')?>%;"></div>
                                            </div>
                                        </div>
                                        <div class="side right">
                                            <div><?=$productdata['fivereviewcount']?></div>
                                        </div>
                                        <div class="side">
                                            <div>4 <i class="fa fa-star"></i></div>
                                        </div>
                                        <div class="middle">
                                            <div class="bar-container">
                                            <div class="bar-4" style="width:<?=number_format(($productdata['productreviewcount']>0?($productdata['fourreviewcount'] * 100 / $productdata['productreviewcount']):0),'2')?>%;"></div>
                                            </div>
                                        </div>
                                        <div class="side right">
                                            <div><?=$productdata['fourreviewcount']?></div>
                                        </div>
                                        <div class="side">
                                            <div>3 <i class="fa fa-star"></i></div>
                                        </div>
                                        <div class="middle">
                                            <div class="bar-container">
                                            <div class="bar-3" style="width:<?=number_format(($productdata['productreviewcount']>0?($productdata['threereviewcount'] * 100 / $productdata['productreviewcount']):0),'2')?>%;"></div>
                                            </div>
                                        </div>
                                        <div class="side right">
                                            <div><?=$productdata['threereviewcount']?></div>
                                        </div>
                                        <div class="side">
                                            <div>2 <i class="fa fa-star"></i></div>
                                        </div>
                                        <div class="middle">
                                            <div class="bar-container">
                                            <div class="bar-2" style="width:<?=number_format(($productdata['productreviewcount']>0?($productdata['tworeviewcount'] * 100 / $productdata['productreviewcount']):0),'2')?>%;"></div>
                                            </div>
                                        </div>
                                        <div class="side right">
                                            <div><?=$productdata['tworeviewcount']?></div>
                                        </div>
                                        <div class="side">
                                            <div>1 <i class="fa fa-star"></i></div>
                                        </div>
                                        <div class="middle">
                                            <div class="bar-container">
                                            <div class="bar-1" style="width:<?=number_format(($productdata['productreviewcount']>0?($productdata['onereviewcount'] * 100 / $productdata['productreviewcount']):0),'2')?>%;"></div>
                                            </div>
                                        </div>
                                        <div class="side right">
                                            <div><?=$productdata['onereviewcount']?></div>
                                        </div>
                                        </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="col-sm-12 col-md-12 col-xs-12 shopdetail">
                <div class="detail">
                    <div class="row">
                        <div class="col-md-2 col-sm-3 col-xs-12">
                            <h6>Share Product : </h6>
                        </div>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <ul class="list-inline social">
                                <li>
                                    <?php 
                                        $productimage=''; 
                                        if(!empty($productdata['images'])) {
                                            for ($p=0; $p < count($productdata['images']); $p++) {
                                                if($productimage==''){
                                                    if($productdata['images'][$p]['type']==1 && file_exists(PRODUCT_PATH.$productdata['images'][$p]['filename'])){  
                                                        $productimage = PRODUCT.$productdata['images'][$p]['filename'];
                                                    }
                                                }
                                            }
                                            if($productimage==''){
                                                $productimage = PRODUCT.PRODUCTDEFAULTIMAGE;
                                            }
                                        }else{
                                            $productimage = PRODUCT.PRODUCTDEFAULTIMAGE;
                                        } 
                                    ?>
                                    <a href="<?=base_url(uri_string());?>" data-image="<?=$productimage?>" data-title="<?=$productdata['productname'];?>" data-desc="<?=strip_tags($this->general_model->TruncateStr(preg_replace("/\s|&nbsp;/",' ',$productdata['metadescription']),150,'')); ?>" target="_blank" class="fb" title="Share on Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a>
                                </li>
                                <li>
                                    <a href="https://twitter.com/intent/tweet?url=<?=base_url(uri_string());?>&text=<?=urlencode($productdata['metatitle']);?>" class="tw" title="Share on Twitter" target="_blank"><i class="fab fa-twitter" aria-hidden="true"></i></a>
                                </li>
                                <li>
                                    <a href="http://www.pinterest.com/pin/create/button/?url=<?=base_url(uri_string());?>&media=<?=urldecode($productimage);?>" class="pinterest" title="Share on Pinterest" target="_blank"><i class="fab fa-pinterest" aria-hidden="true"></i></a>
                                </li>
                                <li>
                                    <?=$whatsupurl?>
                                </li>
                            </ul>   
                        </div>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-description" data-toggle="tab">Description</a></li>
                        <li><a href="#tab-review" data-toggle="tab">Reviews (<?=numberFormat($productdata['productreviewcount'])?>)</a></li>
                        <li><a href="#tab-writeareview" data-toggle="tab">Write a Review</a></li>
                    </ul>
                    <div class="tab-content" id="updown">
                        
                        <div class="tab-pane active" id="tab-description">
                            <p><?=ucfirst($productdata['description'])?></p>
                        </div>
                        
                        <div class="tab-pane" id="tab-review">
                            <?php if(!empty($productreviews)){
                                $this->load->view('product-review-ajax-data');?>
                            
                            <? }else{ ?>
                                <p>Be the first to Review this product</p>
                            <? } ?>
                        </div>
                        <div class="tab-pane" id="tab-writeareview">
                            <form class="form-horizontal" id="form-review">
                                <div class="col-md-12 form-group padd0 margin0">
                                    <div class="col-sm-6">
                                        <div id="reviewerror"></div>
                                    </div>
                                </div>
                                <?php $arrSessionDetails = $this->session->userdata();?>
                                <input type="hidden" name="reviewcustomerid" id="reviewcustomerid" value="<?=(isset($arrSessionDetails[base_url().'MEMBER_ID']))?$arrSessionDetails[base_url().'MEMBER_ID']:'0';?>">
                                <input type="hidden" class="form-control" id="oldcustomerfirstname" name="oldcustomerfirstname" value="<?=((isset($arrSessionDetails[base_url().'MEMBER_NAME']) && $arrSessionDetails[base_url().'MEMBER_NAME']!=''))?$arrSessionDetails[base_url().'MEMBER_NAME']:'';?>" onkeypress="return onlyAlphabets(event)">

                                <div class="col-md-12 form-group p-n" style="display: <?=(!isset($arrSessionDetails[base_url().'MEMBER_NAME']) || (isset($arrSessionDetails[base_url().'MEMBER_NAME']) && $arrSessionDetails[base_url().'MEMBER_NAME']==''))?'block':'none';?>">
                                    <div class="col-md-6">
                                        <label class="control-label" for="reviewname">Your Name <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-12 p-n">
                                            <input type="text" name="reviewname" value="" id="reviewname" class="input100">
                                            <span class="focus-input100"></span>
                                        </div>  
                                    </div>
                                </div>
                                <div class="col-md-12 form-group p-n" style="display: <?=(!isset($arrSessionDetails[base_url().'MEMBER_ID']))?'block':'none';?>">
                                    <div class="col-md-6">
                                        <label class="control-label" for="reviewemail">Your Email <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-12 p-n">
                                            <input type="text" name="reviewemail" id="reviewemail" class="input100" value="<?=((isset($arrSessionDetails[base_url().'MEMBER_EMAIL']) && $arrSessionDetails[base_url().'MEMBER_EMAIL']!=''))?$arrSessionDetails[base_url().'MEMBER_EMAIL']:'';?>">
                                            <span class="focus-input100"></span>
                                        </div>  
                                    </div>  
                                </div>
                                <div class="col-md-12 form-group p-n" style="display: <?=(!isset($arrSessionDetails[base_url().'MEMBER_MOBILENO']) || (isset($arrSessionDetails[base_url().'MEMBER_MOBILENO']) && $arrSessionDetails[base_url().'MEMBER_MOBILENO']==''))?'block':'none';?>">
                                    <div class="col-md-6">
                                        <label class="control-label" for="reviewmobileno">Your Mobile No. <span class="mandatoryfield">*</span></label>
                                        <div class="col-md-12 p-n">
                                            <input type="text" class="input100 number" id="reviewmobileno" name="reviewmobileno" value="<?=((isset($arrSessionDetails[base_url().'MEMBER_MOBILENO']) && $arrSessionDetails[base_url().'MEMBER_MOBILENO']!=''))?$arrSessionDetails[base_url().'MEMBER_MOBILENO']:'';?>" minlength="10" maxlength="10" onkeypress="return isNumber(event)">
                                            <span class="focus-input100"></span>
                                        </div>  
                                    </div>  
                                </div>
                                <div class="col-md-12 form-group p-n mb-n">
                                    <div class="col-md-6">
                                        <label class="control-label" for="message">Your Review <span class="mandatoryfield">*</span></label>
                                        <div class="col-sm-12 p-n">
                                        <textarea name="message" rows="3" id="message" class="input100"></textarea>
                                        <span class="focus-input100"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 p-n">
                                    <div class="help-block">
                                        <span class="text-danger">Note:</span> HTML is not translated!
                                    </div>
                                </div>
                                <div class="col-md-12 form-group p-n">
                                    <div class="col-md-6">
                                    
                                        <div class="" style="margin-bottom: 20px;float: left;width: 100%;">  <!-- class="rating-form star-ico -->
                                            <label class="col-md-2 col-sm-2 col-xs-4 control-label" style="text-align:left;padding:0">Rating <span class="mandatoryfield">*</span></label>
                                            <div class="col-md-6 col-sm-6 col-xs-8 padd0">
                                                <div class='rating-stars'>
                                                    <ul id='stars'>
                                                        <li class='star' title='Poor' data-value='1'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star' title='Fair' data-value='2'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star' title='Good' data-value='3'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star' title='Excellent' data-value='4'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                        <li class='star' title='WOW!!!' data-value='5'>
                                                            <i class='fa fa-star fa-fw'></i>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <input type="hidden" id="reviewvalue" name="reviewvalue" value="0">
                                        </div>
                                    </div>
                                </div>
                                    <div class="buttons clearfix defaultbtn">
                                        <button type="button" id="button-review" data-loading-text="Loading..." class="btn btn-primary" onclick="addreview()">Submit Review</button>
                                    </div>
                            </form>
                        </div>
                    </div>    
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-xs-12 shop" style="padding-top: 15px;padding-bottom: 0px;">
                <?php if(!empty($relatedproductdata)){ ?>
                    <h5 style="padding: 16px;">RELATED PRODUCTS</h5>
                    <div class="row">
                        <div class="ourteam owl-carousel">
                            <?php foreach($relatedproductdata as $relatedproduct){ ?>
                                <div class="item">
                                    <div class="col-sm-12 col-md-12 col-xs-12">
                                        <div class="box">
                                            <div class="image">
                                                <a href="<?=FRONT_URL."products/".$relatedproduct['slug']?>"><img src="<?=PRODUCT.$relatedproduct['image']?>" class="img-responsive"
                                                        alt="<?=ucwords($relatedproduct['productname'])?>" style="height: 166px;margin: 0 auto;" /></a>
                                            </div>
                                            <div class="caption" style="height: 138px;">
                                                <div class="cart-btn">
                                                    <?php if(WEBSITE==1 && WEBSITETYPE==1){ 
                                                        if($relatedproduct['isuniversal']==1){  
                                                            $productdetaillink = "javascript:void(0)";
                                                        }else{
                                                            $productdetaillink = FRONT_URL."products/".$relatedproduct['slug'];
                                                        } ?>
                                                        <a href="<?=$productdetaillink?>" class="btn-cart btn"><i class="icon icon-ShoppingCart"></i>Add to cart</a>
                                                    <?php } else {?>
                                                        <a href="<?=FRONT_URL."products/".$relatedproduct['slug']?>" class="btn-cart btn"><i class="fa fa-comment-o"></i> Get a Quote</a>
                                                    <?php }?>
                                                </div>
                                                <div class="category">
                                                    <a href="<?=FRONT_URL."products/".$relatedproduct['slug']?>"><?=ucwords($relatedproduct['category'])?></a>
                                                </div>
                                                <h2><a href="<?=FRONT_URL."products/".$relatedproduct['slug']?>"><?=strlen($relatedproduct['productname']) > 20 ? substr(strip_tags(ucwords($relatedproduct['productname'])),0,20)."..." : ucwords($relatedproduct['productname']);?></a></h2>
                                                <div class="price" style="">
                                                    <?php echo CURRENCY_CODE.numberFormat($relatedproduct['pricewithdiscount'],2,','); 
                                                        if($relatedproduct['discount'] > 0){ ?>
                                                            <span class="old-price"><?=CURRENCY_CODE.numberFormat($relatedproduct['price'],2,',')?></span> <span class="offertex"><?=(int)$relatedproduct['discount']?>% off</span>
                                                    <?php } ?>
                                                </div>
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
    </div>
</div>
<!-- shop end here -->

<div class="modal fade" id="getaquoteModal" tabindex="-1" role="dialog" aria-labelledby="getaquoteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="width: 100%;float: left;">
                <h5 class="modal-title col-md-8" id="exampleModalLabel">Get a Quote</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa fa-times"></i></span>
                </button>
            </div>
            
            <div class="modal-body" style="width:100%;float: left;overflow-y: auto;max-height:400px;">
                <div class="col-md-12 p-n">
                    <div id="productformerror"></div>
                </div>
                <form id="product-form" method="POST" class="form" name="product-form">  
                
                    <input type="hidden" name="productid" value="<?=$productdata['id']?>">
                    <input type="hidden" name="memberid" value="0">
                                        
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="fullname" class="col-form-label" >Full Name <span class="mandatoryfield">*</span></label>
                                <input type="text"  name ="name" class="form-control" id="name"  onkeypress="return onlyAlphabets(event)">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mobile" class="col-form-label">Mobile No. <span class="mandatoryfield">*</span></label>
                                <input type="text" name ="mobile"  class="form-control" id="mobile" maxlength="10"  onkeypress="return isNumber(event)"  >
                            
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                    <label for="email">Email Address<span class="mandatoryfield">*</span></label>
							        <input name="email" id="email" type="text" class="form-control"  required>
						      </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="organizations" class="col-form-label">Organizations <span class="mandatoryfield">*</span></label>
                                <input type="text" name ="organizations" class="form-control" id="organizations">
                                
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address" class="col-form-label">Address </label>
                                <textarea class="form-control" name ="address" id="address"></textarea>                            
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="message" class="col-form-label">Message </label>
                                <textarea class="form-control" name ="msg" id="msg"></textarea>                            
                            </div>
                        </div>

                    </div>
                </form>
            </div>
            <div class="modal-footer defaultbtn">
                <button type="button"  class="bt btn btn-danger"  data-dismiss="modal"  onclick="resetdata()">Close</button>
                <button type="button" class="btn btn-primary"  onclick="checkgetquotevalidation()" >Submit</button>
            </div>
        </div>
    </div>
</div>
 
<noscript>
    <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=3587975991245034&ev=PageView&noscript=1" />
</noscript> 