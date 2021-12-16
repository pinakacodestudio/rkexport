<?php 
  $arrSessionDetails = $this->session->userdata;
  $errormsg = '';
  if(isset($arrSessionDetails[base_url().'errormsg']) && !empty($arrSessionDetails[base_url().'errormsg']) && isset($arrSessionDetails[base_url().'MEMBER_ID'])){
    $errormsg = '<ul class="form-error"><li><i class="fa fa-info-circle" style="color:#b81c23"></i>&nbsp;'.$arrSessionDetails[base_url().'errormsg'].'</li></ul>';
    $this->session->unset_userdata(base_url().'errormsg');
  }
?>
<script>
    var loginsession = '<?php if(isset($arrSessionDetails[base_url().'MEMBER_ID'])) { echo 1; }else { echo 0; }?>';
    var PRODUCT = '<?=PRODUCT?>';
    var DEFAULT_IMG = '<?=DEFAULT_IMG?>';
    var DEFAULT_PRODUCT_IMG = '<?=PRODUCTDEFAULTIMAGE?>';
    var CURRENCY_CODE = '<?=CURRENCY_CODE?>';
    var tempArray = [];
    var oldcartdata = [];

    <?php if(isset($arrSessionDetails[base_url().'MEMBER_ID']) && isset($oldcartdata)) {
        for ($i = 0; $i < count($oldcartdata); $i++) { ?>
        tempArray = {"productid": <?=$oldcartdata[$i]['productid'];?>,"productpriceid": <?=$oldcartdata[$i]['productpriceid'];?>,"quantity":<?=$oldcartdata[$i]['quantity'];?>,"referencetype":<?=$oldcartdata[$i]['referencetype'];?>,"referenceid":<?=$oldcartdata[$i]['referenceid'];?>};
        oldcartdata.push(tempArray);
        <?php }
    } ?>

    var errormessage = '<?=$errormsg?>';
    var REWARDS_POINTS = '<?=REWARDSPOINTS?>';
</script>
<!-- slider start here -->
<div class="process-bg" style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<h1 style="font-size:35px;">Cart</h1>
                <ul class="breadcrumbs list-inline">
					<li><a href="<?=FRONT_URL?>">Home</a></li>
					<li><a href="<?=FRONT_URL."products"?>">Product</a></li>
					<li>Cart</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- slider end here -->

<!-- mycart code start here -->
<div class="mycart">
    <div class="container">
        <div class="row">
            <div class="alert" id="alert"></div>
            <div class="col-sm-8 listproducts">
                <div class="cart-tital row">
                    <div class="col-md-12">
                        <h3>My Cart (<span class="cartcount"></span>)</h3>
                    </div>
                </div>
               
                <form method="post" enctype="multipart/form-data">
                    <div class="row" id="carterror"><?=$errormsg?></div>
                    <div class="row" id="cartdata">
                     
                    </div>
                </form>
            </div>
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="cartable totals" style="display: none;">
                            <input type="hidden" name="postsubtotal" id="postsubtotal" value="0">
                            <input type="hidden" name="posttaxamount" id="posttaxamount" value="0">
                            <input type="hidden" name="postnettotal" id="postnettotal" value="0">
                            <input type="hidden" name="postdiscount" id="postdiscount" value="0">
                            <input type="hidden" name="postcoupondiscount" id="postcoupondiscount" value="0">
                            <h5>Price Details</h5>
                            <div class="cart-price-xv">
                                <div class="cart-p">Price (<span class="cartcount"></span>)</div><span><?=CURRENCY_CODE?> <span id="subtotalamount">0.00</span></span>
                            </div>
                            <div class="cart-price-xv">
                                <div class="cart-p">Tax Amount</div><span><?=CURRENCY_CODE?> <span id="viewtaxamount"></span></span>
                            </div>
                            <div class="cart-price-xv" style="display:none;" id="couponrow">
                                <div class="cart-p">Coupon Discount</div><span><?=CURRENCY_CODE?> <span id="couponamount">0.00</span></span>
                            </div>
                            <div class="cart-price-xv" style="display:none;" id="redeempointrow">
                                <div class="cart-p">Redeem Amount (<span id="conversationratespan"></span>)</div><span><?=CURRENCY_CODE?> <span id="redeemamount">0.00</span></span>
                            </div>
                            <div class="cart-delivery">
                                <div class="delivery-f">Delivery Fee</div><span><span
                                        class="delivery-f delivery-green">Free</span></span>
                            </div>

                            <div class="cart_total_amount">
                                <div class="cart_amount_tx">
                                    <div class="cart_am_tx">Total Amount</div><span><?=CURRENCY_CODE?> <span id="nettotalamount">0.00</span></span>
                                </div>
                            </div>
                            <div class="order_save" style="display:none;">You will save <span id="discountamount">0.00</span> on this order</div>

                            <div class="row cart-footer">
                                <div class="text-center">
                                    <button type="button" class="btn-block btn btn-primary" id="checkoutbtn" onclick="checkout()">Checkout
                                    </button>
                                </div>
                            </div>
                            <div class="row mb-md">
                                <div class="col-md-12">
                                    <div class="cart-coupon">
                                        <div class="coupon">
                                            <input type="hidden" id="discounttype" value="">
                                            <input type="hidden" id="discountvalue" value="">
                                            <input type="hidden" id="vouchercodeid" value="<?=(isset($coupondata) && !empty($coupondata)?$coupondata['vouchercodeid']:0)?>">
                                            <input type="hidden" id="vouchercodetype" value="">
                                            <input type="hidden" id="vouchercode" value="">
                                            <input type="hidden" id="minimumbillingtotal" value="">
                                            <div class="row text-left" id="couponerror"></div>

                                            <label class="col-md-12 col-xs-12 control-label p-n" for="couponcode" style="text-align: left;">Coupon Code</label>
                                            <div class="col-md-6 col-xs-12 p-n mb-md">
                                                <input type="text" name="couponcode" class="input100" id="couponcode" value="<?=(isset($coupondata) && !empty($coupondata)?$coupondata['vouchercode']:'')?>" maxlength="12">
                                                <span class="focus-input100"></span>
                                            </div>
                                            <div class="col-md-5 col-xs-12 pull-right p-n">
                                                <?php if(isset($coupondata) && !empty($coupondata)){ ?>

                                                    <button type="button" class="btn-block btn btn-danger" name="applycoupon" id="applycoupon" onclick="removecoupon()">Remove Coupon</button>
                                                <?php }else{ ?>
                                                    <button type="button" class="btn-block btn btn-primary" name="applycoupon" id="applycoupon" onclick="applycouponcode()">Apply Coupon</button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(REWARDSPOINTS==1){?>
                            <div class="row">
                                <div class="col-md-12">
                                    <label class="control-label" for="">Redeem Point (<img src="<?=FRONT_URL.'assets/images/coin.png'?>" style="width: 18px;"><?=(isset($redeempoints) && !empty($redeempoints['rewardpoint'])?$redeempoints['rewardpoint']:'0')?>)</label>
                                    <div class="col-md-12 p-n mb-sm">
                                        <input type="text" name="redeempoint" class="input100" id="redeempoint" value="<?=(isset($redeempoint) && !empty($redeempoint)?$redeempoint:'')?>" maxlength="4" onkeypress="return isNumber(event)">
                                        <span class="focus-input100"></span>
                                    </div>
                                    <div class="row mt-md m-n text-left" id="redeemerror"></div>

                                    <input type="hidden" id="minimumpointsonredeemfororder" value="<?php if(isset($channeldata)){ echo $channeldata['minimumpointsonredeemfororder']; } ?>">
                                    <input type="hidden" id="minimumpointsonredeem" value="<?php if(isset($channeldata)){ echo $channeldata['minimumpointsonredeem']; } ?>">
                                    <input type="hidden" id="mimimumpurchaseorderamountforredeem" value="<?php if(isset($channeldata)){ echo $channeldata['mimimumpurchaseorderamountforredeem']; } ?>">
                                    <input type="hidden" id="conversationrate" value="<?php if(isset($channeldata)){ echo $channeldata['conversationrate']; } ?>">
                                    
                                    <input type="hidden" id="redeempointsforbuyer" value="<?=(isset($redeempoints) && !empty($redeempoints['rewardpoint'])?$redeempoints['rewardpoint']:'0')?>">
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<!-- mycart code end here -->