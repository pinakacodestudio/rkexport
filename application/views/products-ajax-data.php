<?php if(LISTING == 0){ ?>      
<div id="productlist">
<?php
}
    if(!empty($productdata)){
        $srno = 1;
        foreach($productdata as $product){?>
            <div class="product-grid col-sm-6 col-md-4 col-xs-12">
                <div class="box">
                    <div class="image">
                        <a href="<?=FRONT_URL."products/".$product['slug']?>">
                            <?php if($product['image']!="" && file_exists(PRODUCT_PATH.$product['image'])){
                                $primg = PRODUCT.$product['image'];
                            }else{
                                $primg = PRODUCT.PRODUCTDEFAULTIMAGE;
                            }?>
                            <img src="<?=$primg?>" class="img-responsive" alt="<?=ucwords($product['productname'])?>"/>
                        </a>
                    </div>
                    <div class="caption">
                       
                        <div class="cart-btn">
                            <?php if(WEBSITE==1 && WEBSITETYPE==1){ 
                                if($product['isuniversal']==1){  
                                    $productdetaillink = "javascript:void(0)";
                                    $onclick = "checkstock(".$product['id'].",".$product['priceid'].",'".$product['referencetype']."',".$product['referenceid'].")";
                                }else{
                                    $productdetaillink = FRONT_URL."products/".$product['slug'];
                                    $onclick = "";
                                } ?>
                                <a href="<?=$productdetaillink?>" class="btn-cart btn" onclick="<?=$onclick?>"><i class="icon icon-ShoppingCart"></i>Add to cart</a>
                            <?php } else {?>
                                <a href="<?=FRONT_URL."products/".$product['slug']?>" class="btn-cart btn"><i class="fa fa-comment-o"></i> Get a Quote</a>
                            <?php }?>
                        </div>
                        
                        <div class="category">
                            <a href="<?=FRONT_URL.CATEGORY_SLUG.'/'.$product['categoryslug']?>"><?=ucwords($product['category'])?></a>
                        </div>
                        
                        <h2 class="textoverflow"><a href="<?=FRONT_URL."products/".$product['slug']?>"><?=ucwords($product['productname'])?></a></h2>
                        
                        <div class="price" style="word-break: break-wordwrap;">
                            <?php echo CURRENCY_CODE.' '.numberFormat($product['pricewithdiscount'],2,','); 
                                if($product['discount'] > 0){ ?>
                                    <span class="old-price"><?=CURRENCY_CODE.' '.numberFormat($product['price'],2,',')?></span> <span class="offertex"><?=(int)$product['discount']?>% off</span>
                            <?php } ?>
                            <span id="productstockerror<?=$product['priceid']?>" style='color:red;font-weight:500;'>
                            
                            </span>
                        </div>
                        <p class="desc"><?=strlen($product['shortdescription']) > 150 ? substr(strip_tags(ucwords($product['shortdescription'])),0,150)."..." : ucwords($product['shortdescription']);?></p>
                        <div class="cart-btn-list">
                            <?php if(WEBSITE==1 && WEBSITETYPE==1){ 
                                if($product['isuniversal']==1){  
                                    $productdetaillink = "javascript:void(0)";
                                    $onclick = "checkstock(".$product['id'].",".$product['priceid'].",'".$product['referencetype']."',".$product['referenceid'].")";
                                }else{
                                    $productdetaillink = FRONT_URL."products/".$product['slug'];
                                    $onclick = "";
                                } ?>
                                <a href="<?=$productdetaillink?>" class="btn-cart btn" onclick="<?=$onclick?>"><i class="icon icon-ShoppingCart"></i>Add to cart</a>
                            <?php } else { ?>
                                <a href="<?=FRONT_URL."products/".$product['slug']?>" class="btn-cart btn"><i class="fa fa-comment-o"></i> Get a Quote</a>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        <?php $srno++; 
        }  ?>    
        <?php if(LISTING == 0){ ?>              
        <div class="col-sm-12 text-center">
           <?=$link?>
        </div>
        <?php } ?>
    <?php }else{ ?>              
        <div class="col-sm-12 col-md-12 col-xs-12">
            <div class="box-search">
               <?php $this->load->view("No_result_found"); ?>
            </div>
        </div>
    <?php } 
if(LISTING == 0){ ?>    
 </div>
 <?php } ?>