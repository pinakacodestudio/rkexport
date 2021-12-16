<script>
    var DEFAULT_COUNTRY_ID = '<?=DEFAULT_COUNTRY_ID?>';
</script>
<!-- slider start here -->
<div class="process-bg" style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<h1 style="font-size:35px;">Checkout</h1>
                <ul class="breadcrumbs list-inline">
					<li><a href="<?=FRONT_URL?>">Home</a></li>
					<li><a href="<?=FRONT_URL."cart"?>">Cart</a></li>
					<li>Checkout</li>
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
            <!-- <div class="alert" id="alert"></div> -->
            <div class="col-sm-12">
                <div class="checkout-main">
                    <div class="row">
                        <input type="hidden" id="surl" name="surl" value="<?=FRONT_URL.'success'?>" />
                        <input type="hidden" id="furl" name="furl" value="<?=FRONT_URL.'failure'?>" />
                        <input type="hidden" id="key" name="key" value="" />
                        <input type="hidden" id="salt" name="salt" value="" />
                        <input type="hidden" id="txnid" name="txnid" value="" />
                        <input type="hidden" id="amount" name="amount" value="">
                        <input type="hidden" id="pinfo" name="pinfo" value="" />
                        <input type="hidden" id="fname" name="fname" value="" />
                        <input type="hidden" id="email" name="email" value="" />
                        <input type="hidden" id="mobile" name="mobile" value="" />
                        <input type="hidden" id="hash" name="hash" value="" />
                        <input type="hidden" id="udf1" name="udf1" value="" />
                        <input type="hidden" id="udf2" name="udf2" value="" />
                        <div class="col-sm-8 accordion pr-n" id="accordion">
                            <div id="checkouterror"></div>
                            <form class="form-horizontal" id="checkoutform" method="post" action="<?=FRONT_URL.'cart/payment'?>" enctype="multipart/form-data">
                            <input type="hidden" name="weight" id="weight" value=<?=$codweight['totalweight']?> >
                                <div class="panel checkout_sub" data-step="1">
                                    <div class="checkout_tital">
                                        <div data-toggle="collapse" data-parent="#accordion" data-target="#collapsebilling">
                                        <h5><span>1</span> Billing Address</h5></div> 
                                        <div class="checkout_boy collapse in" id="collapsebilling">

                                            <!-- <form class="form-horizontal"> -->
                                            <div class="form-horizontal">  
                                                <div class="col-md-12 p-n addresslist" id="billinglisting">
                                                    <?php if(isset($memberaddress)) { ?>
                                                        <?php for ($i=0; $i < count($memberaddress); $i++) { ?>
                                                            <div class="col-md-6 mb pl-xs pr-xs billaddress<?=$memberaddress[$i]['id']?>">
                                                                <div class="radio billingaddress">
                                                                    <input id="billingaddress<?=$memberaddress[$i]['id']?>" type="radio" name="billingaddress" value="<?=$memberaddress[$i]['id']?>" <?php if($i==0) { echo 'checked';} ?>>
                                                                    
                                                                    <label for="billingaddress<?=$memberaddress[$i]['id']?>" class="m-n" style="width: 100%;">
                                                                        <address class="col-md-12 pl-xl pt-sm m-n" style="position: relative;">
                                                                            <?php if($memberaddress[$i]['membername']!=''){?>
                                                                            <span id="billingname<?=$memberaddress[$i]['id']?>"><b><?=ucwords($memberaddress[$i]['membername'])?></b></span>&nbsp;
                                                                            <? } ?><br>
                                                                            <?php if($memberaddress[$i]['shortaddress']!=''){?>
                                                                            <span id="billingaddr<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['shortaddress']?></span><br>
                                                                            <? } ?>
                                                                            <input type="hidden" id="billingcityid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['ctid'];?>">
                                                                            <input type="hidden" id="billingpostcode<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['postalcode'];?>">
                                                                            <input type="hidden" id="billingprovinceid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['sid'];?>">
                                                                            <input type="hidden" id="billingcountryid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['cid'];?>">
                                                                            <?php 
                                                                            $billinglocation = '';
                                                                            if($memberaddress[$i]['cityname']!=''){
                                                                                $billinglocation .= ucwords($memberaddress[$i]['cityname']);
                                                                                if($memberaddress[$i]['postalcode']!=''){
                                                                                    $billinglocation .= " - ".ucwords($memberaddress[$i]['postalcode']);
                                                                                }
                                                                                $billinglocation .= ' '.ucwords($memberaddress[$i]['statename']).", ".ucwords($memberaddress[$i]['countryname']).".<br>";
    
                                                                                echo '<span id="billinglocation'.$memberaddress[$i]['id'].'">'.$billinglocation.'</span>';
                                                                            }
                                                                            ?>
                                                                            <?php if($memberaddress[$i]['mobileno']!=''){?>
                                                                            <b><i class="fa fa-phone-square"></i> </b><span id="billingmobileno<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['mobileno']?></span><br>
                                                                            <? } ?>    
                                                                            <b><i class="fa fa-envelope-o"></i> </b><span id="billingemail<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['email']?></span>
                                                                        </address>
                                                                        <span class="editaddresslbl"><a href="javascript:void(0)" class="btn action-btn" onclick="editaddress(<?=$memberaddress[$i]['id']?>,1)"><i class="fa fa-edit"></i> Edit</a></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0)" class="btn btn-primary newbillingaddress" data-toggle="modal" data-target="#AddressModal" onclick="openaddressmodal(1)"><i class="fa fa-plus plus"></i> Add New Billing Address</a>
                                            <button type="button" id="button-review"
                                                    class="btn btn-primary" onclick="nextstep(1)">Next</button>
                                            <!-- </form> -->
                                            <!-- data-toggle="collapse" data-parent="#accordion" href="#collapseshipping" -->
                                        </div>

                                    </div>
                                </div>

                                <div class="panel checkout_sub disablesection" data-step="2">
                                    <div class="checkout_tital">
                                        <div data-toggle="collapse" data-parent="#accordion" data-target="#collapseshipping" class="collapsed">
                                        <h5><span>2</span> Shipping Address</h5></div> 
                                        <div class="checkout_boy collapse" id="collapseshipping">
                                            <div class="form-horizontal">  
                                                <div class="col-md-12 p-n addresslist" id="shippinglisting">
                                                    <?php if(isset($memberaddress)) { ?>
                                                        <?php for ($i=0; $i < count($memberaddress); $i++) { ?>
                                                            <div class="col-md-6 mb pl-xs pr-xs shipaddress<?=$memberaddress[$i]['id']?>">
                                                                <div class="radio billingaddress">
                                                                    <input id="shippingaddress<?=$memberaddress[$i]['id']?>" type="radio" name="shippingaddress" value="<?=$memberaddress[$i]['id']?>" <?php if($i==0) { echo 'checked';} ?>>
                                                                    
                                                                    <label for="shippingaddress<?=$memberaddress[$i]['id']?>" class="m-n" style="width: 100%;">
                                                                        <address class="col-md-12 pl-xl pt-sm m-n" style="position: relative;">
                                                                            <?php if($memberaddress[$i]['membername']!=''){?>
                                                                            <span id="shippingname<?=$memberaddress[$i]['id']?>"><b><?=ucwords($memberaddress[$i]['membername'])?></b></span>&nbsp;
                                                                            <? } ?><br>
                                                                            <?php if($memberaddress[$i]['shortaddress']!=''){?>
                                                                            <span id="shippingaddr<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['shortaddress']?></span><br>
                                                                            <? } ?>
                                                                            <input type="hidden" id="shippingcityid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['ctid'];?>">
                                                                            <input type="hidden" id="shippingpostcode<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['postalcode'];?>">
                                                                            <input type="hidden" id="shippingprovinceid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['sid'];?>">
                                                                            <input type="hidden" id="shippingcountryid<?=$memberaddress[$i]['id']?>" value="<?=$memberaddress[$i]['cid'];?>">
                                                                            <?php 
                                                                            $shippinglocation = '';
                                                                            if($memberaddress[$i]['cityname']!=''){
                                                                                $shippinglocation .= ucwords($memberaddress[$i]['cityname']);
                                                                                if($memberaddress[$i]['postalcode']!=''){
                                                                                    $shippinglocation .= " - ".ucwords($memberaddress[$i]['postalcode']);
                                                                                }
                                                                                $shippinglocation .= ' '.ucwords($memberaddress[$i]['statename']).", ".ucwords($memberaddress[$i]['countryname']).".<br>";
    
                                                                                echo '<span id="shippinglocation'.$memberaddress[$i]['id'].'">'.$shippinglocation.'</span>';
                                                                            }
                                                                            ?>
                                                                            <?php if($memberaddress[$i]['mobileno']!=''){?>
                                                                            <b><i class="fa fa-phone-square"></i> </b><span id="shippingmobileno<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['mobileno']?></span><br>
                                                                            <? } ?>    
                                                                            <b><i class="fa fa-envelope-o"></i> </b><span id="shippingemail<?=$memberaddress[$i]['id']?>"><?=$memberaddress[$i]['email']?></span>
                                                                        </address>
                                                                        <span class="editaddresslbl"><a href="javascript:void(0)" class="btn action-btn" onclick="editaddress(<?=$memberaddress[$i]['id']?>,2)"><i class="fa fa-edit"></i> Edit</a></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                            <a href="javascript:void(0)" class="btn btn-primary newbillingaddress" data-toggle="modal" data-target="#AddressModal" onclick="openaddressmodal(2)"><i class="fa fa-plus plus"></i> Add New Shipping Address</a>
                                            <button type="button" id="button-review"
                                                    class="btn btn-primary" onclick="nextstep(2)">Next</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="panel checkout_sub disablesection" data-step="3">
                                    <div class="checkout_tital">
                                        <div data-toggle="collapse" data-parent="#accordion" data-target="#collapsepayment" class="collapsed">
                                        <h5><span>3</span> Payment Options</h5></div>
                                        <div class="checkout_boy collapse" id="collapsepayment">
                                            
                                            <div class="row">
                                            <!--  <div id="onlinepayment" class="tab-pane active" style="box-shadow: 0px 0px 20px 10px rgba(0,0,0,0.05);">  -->
                                                <?php /* if(!empty($paymentgatewaydata)){ 
                                                    $methodname = $this->Paymentgatewaytype[$paymentgatewaydata['paymentgatewayid']];

                                                    if($methodname=="payumoney"){
                                                        $logo = "payumoney.png";
                                                    }else if($methodname=="payu"){
                                                        $logo = "payu.png";
                                                    }else if($methodname=="paytm"){
                                                        $logo = "paytm.jpeg";
                                                    }   
                                                    ?>
                                                    <div class="col-md-6 mb-sm">
                                                        <div class="radio">
                                                            <input id="<?=ucwords($methodname)?>" type="radio" name="paymentmethod" value="<?=$paymentgatewaydata['paymentgatewayid']?>" checked>
                                                            <label for="<?=ucwords($methodname)?>">
                                                                <span class="col-md-12"><img src="<?=FRONT_URL.'assets/images/'.$logo?>" alt="<?=ucwords($methodname)?>" style="height:45px;"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <button type="button" id="button-review" class="btn btn-primary" onclick="placeorder()">Place Order</button>
                                                    </div>
                                                <? } */ ?>
                                                <!-- </div> -->

                                                
                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                <?php if(!empty($paymentmethoddata)){ 
                                                    foreach($paymentmethoddata as $index => $row){ ?>

                                                        <?php if($row['name']!='COD' || ($row['name']=='COD' && $codweight['iscod']!=0)){ ?>
                                                        <div class="col-md-4 col-sm-6 col-xs-12 pl-xs pr-xs">
                                                            <div class="radio">
                                                                <input id="<?=$row['name']?>" type="radio" name="paymentmethod" value="<?=$row['paymentgatewaytype']?>" <?=($index==0)?'checked':''?>>
                                                                <label for="<?=$row['name']?>">
                                                                <span class="col-md-12"><img src="<?=PAYMENT_METHOD_LOGO.$row['logo']?>" alt="<?=$row['name']?>" style="width:150px;"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <? } } ?>
                                                    <div class="col-md-12 col-sm-12 col-xs-12"><hr></div>
                                                <? } ?>
                                                </div>
                                                <!-- <div class="col-md-12 col-sm-12 col-xs-12 mb-md">
                                                    <div class="col-md-4 col-sm-6 col-xs-12 pl-xs pr-xs">
                                                        <div class="radio">
                                                            <input id="cod" type="radio" name="paymentmethod" value="0" <?=(empty($paymentmethoddata))?'checked':''?>>
                                                            <label for="cod">
                                                            <span class="col-md-12"><img src="<?=FRONT_URL?>assets/img/cash-on-delivery.png" alt="<?=$row['name']?>" style="width:150px;"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                        
                                                <div class="col-md-12">
                                                    <button type="button" id="button-review" class="btn btn-primary" onclick="placeorder()">Place Order</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="cartable totals">
                                        <input type="hidden" name="postsubtotal" id="postsubtotal" value="<?=number_format($pricedetail['subtotal'],2,'.','')?>">
                                        <input type="hidden" name="posttaxamount" id="posttaxamount" value="<?=number_format($pricedetail['taxamount'],2,'.','')?>">
                                        <input type="hidden" name="postnettotal" id="postnettotal" value="<?=number_format($pricedetail['netamount'],2,'.','')?>">
                                        <input type="hidden" name="postdiscount" id="postdiscount" value="<?=number_format($pricedetail['productdiscount'],2,'.','')?>">
                                        <input type="hidden" name="postcoupondiscount" id="postcoupondiscount" value="<?=number_format($pricedetail['coupondiscount'],2,'.','')?>">
                                        <input type="hidden" name="postcouponcode" id="postcouponcode" value="<?=$pricedetail['couponcode']?>">
                                        <input type="hidden" name="postcouponcodeid" id="postcouponcodeid" value="<?=$pricedetail['couponcodeid']?>">

                                        <input type="hidden" name="postredeempoint" id="postredeempoint" value="<?=$pricedetail['redeempoint']?>">
                                        <input type="hidden" name="postredeemrate" id="postredeemrate" value="<?=$pricedetail['redeemrate']?>">
                                        <input type="hidden" name="postredeemamount" id="postredeemamount" value="<?=number_format($pricedetail['redeemamount'],2,'.','')?>">

                                        <h5>Price Details</h5>
                                        <div class="cart-price-xv">
                                            <div class="cart-p">Price (<span class="cartcount"><?=$pricedetail['cartcount']?> Items</span>)</div><span><?=CURRENCY_CODE?> <span id="subtotalamount"><?=numberFormat($pricedetail['subtotal'],2,',')?></span></span>
                                        </div>
                                        <div class="cart-price-xv">
                                            <div class="cart-p">Tax Amount</div><span><?=CURRENCY_CODE?> <span id="taxamount"><?=numberFormat($pricedetail['taxamount'],2,',')?></span></span>
                                        </div>
                                        <?php if($pricedetail['coupondiscount'] > 0){ ?>
                                        <div class="cart-price-xv" id="couponrow">
                                            <div class="cart-p">Coupon Discount</div><span><?=CURRENCY_CODE?> <?=numberFormat($pricedetail['coupondiscount'],2,',')?></span>
                                        </div>
                                        <?php } ?>
                                        <?php if($pricedetail['redeempoint'] > 0){ ?>
                                        <div class="cart-price-xv" id="couponrow">
                                            <div class="cart-p">Redeem Amount (<?=$pricedetail['redeempoint']."*".$pricedetail['redeemrate']?>)</div><span><?=CURRENCY_CODE?> <?=numberFormat($pricedetail['redeemamount'],2,',')?></span>
                                        </div>
                                        <?php } ?>
                                        <div class="cart-delivery">
                                            <div class="delivery-f">Delivery Fee</div><span><span
                                                    class="delivery-f delivery-green">Free</span></span>
                                        </div>

                                        <div class="cart_total_amount">
                                            <div class="cart_amount_tx">
                                                <div class="cart_am_tx">Total Amount</div><span><?=CURRENCY_CODE?> <span id="nettotalamount"><?=numberFormat($pricedetail['netamount'],2,',')?></span></span>
                                            </div>
                                        </div>
                                        <?php if($pricedetail['totaldiscount'] > 0){ ?>
                                        <div class="order_save">You will save <?=CURRENCY_CODE.' '.numberFormat($pricedetail['totaldiscount'],2,',')?></span> on this order</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>
<!-- mycart code end here -->

<div class="modal fade" id="AddressModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true"><i class="fa fa-times"></i></span></button>
                <h4 class="modal-title">Add Address</h4>
            </div>
            <div class="modal-body" style="float:left;width:100%;overflow-y: auto;max-height: 450px;">
                <form class="form-horizontal" id="memberaddressform">
                    <div id="memberaddresserror"></div>
                    <input type="hidden" name="memberid" value="<?=$memberid?>">
                    <input type="hidden" name="addressid" id="addressid" value="">

                    <div class="col-md-6">
                        <div class="form-group" id="membername_div">
                            <label class="col-sm-4 control-label" for="membername">Name <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="membername" type="text" name="membername" class="input100" onkeypress="return onlyAlphabets(event)" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="memberemail_div">
                            <label class="col-sm-4 control-label" for="memberemail">Email
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="memberemail" type="text" name="memberemail" class="input100" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="memberaddress_div">
                            <label class="col-sm-4 control-label" for="memberaddress">Address <span
                                    class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <textarea id="memberaddress" name="memberaddress" value="" class="input100"></textarea>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="membertown_div">
                            <label class="col-sm-4 control-label" for="membertown">Town</label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="membertown" type="text" name="membertown" class="input100" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" id="memberpostalcode_div">
                            <label class="col-sm-4 control-label" for="memberpostalcode">Postal Code
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="memberpostalcode" type="text" name="memberpostalcode" class="input100"
                                    onkeypress="return isNumber(event)" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" id="membermobileno_div">
                            <label class="col-sm-4 control-label" for="membermobileno">Mobile No.
                                <span class="mandatoryfield">*</span></label>
                            <div class="col-sm-8">
                                <div class="col-sm-12 p-n">
                                    <input id="membermobileno" type="text" name="membermobileno" class="input100"
                                    onkeypress="return isNumber(event)" maxlength="10" value="">
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="countryid">Country</label>
                            <div class="col-sm-8" id="membercountry_div">
                                <div class="col-sm-12 p-n">
                                    <select id="countryid" name="countryid" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5">
                                        <option value="0">Select Country</option>
                                        <?php foreach($countrydata as $country){ ?>
                                        <option value="<?php echo $country['id']; ?>" <?php if(DEFAULT_COUNTRY_ID == $country['id']){ echo "selected"; } ?>><?php echo $country['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="focus-input100"></span>
                                </div>
                           </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="provinceid">Province</label>
                            <div class="col-sm-8" id="memberprovince_div">
                                <div class="col-sm-12 p-n">
                                    <select id="provinceid" name="provinceid" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select Province</option>
                                    </select>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="cityid">City</label>
                            <div class="col-sm-8" id="membercity_div">
                                <div class="col-sm-12 p-n">
                                    <select id="cityid" name="cityid" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5">
                                            <option value="0">Select City</option>
                                    </select>
                                    <span class="focus-input100"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 checkout_sub">
                        <hr>
                        <div class="form-group mb-n defaultbtn" style="text-align: center;">
                            <input type="button" id="addaddressbtn" onclick="checkmemberaddressvalidation()"
                                name="submit" value="ADD" class="btn btn-primary">
                            <input type="button" value="RESET" class="btn btn-primary btn-raised"
                                onclick="resetaddressdata()">
                            <a class="bt btn btn-danger" href="javascript:voi(0)" title=<?=cancellink_title?>  data-dismiss="modal" aria-label="Close"><?=cancellink_text?></a>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer p-n"></div>
        </div>
    </div>
</div>