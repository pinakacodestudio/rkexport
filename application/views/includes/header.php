<header class="header-main">
  <div class="container">
    <div class="row">
    <?php 
      $logo = MAIN_LOGO_IMAGE_URL.COMPANY_SMALL_LOGO;
      if($page=="Success" || $page=="Failure" || $page=="no_result_found" || $page=="not_found"){ 
        $logo = MAIN_LOGO_IMAGE_URL.COMPANY_LOGO; 
      }?>
      <div class="col-sm-2 col-md-2 col-lg-2 hidden-xs hidden-sm">
        <div id="logo">
          <a href="<?=FRONT_URL?>">
            <img class="img-responsive" src="<?=$logo?>" alt="logo" title="logo" style="width:100px;" />
          </a>
        </div>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-10 col-lg-10">
        <div class="logo visible-xs visible-sm">
          <a href="<?=FRONT_URL?>">
            <img class="img-responsive" src="<?=MAIN_LOGO_IMAGE_URL.COMPANY_LOGO?>" alt="logo" title="logo" style="width: 80px;" />
          </a>
        </div>
        <div class="pull-right">
          <ul class="list-inline icons">

            <li><span class="openoverlay"><i class="fa fa-search"></i></span>
              <div id="myNav" class="overlay">
                <a href="javascript:void(0)" class="closebtn" onclick="closeoverlay()">&times;</a>
                <div class="overlay-content">
                  <form class="form-horizontal" method="post" id="srch">
                    <div class="form-group" id="searchproducts_div">
                      <input name="searchproducts" id="searchproducts" value="<?=(isset($issearch)?$issearch:"")?>" class="form-control" placeholder="Search Our Product" type="text" style="padding-right: 50px;">
                    </div>
                    <button type="submit" value="submit" class="btn">
                      <i class="fa fa-search" aria-hidden="true"></i>
                    </button>
                  </form>
                  <div class="text-left mandatoryfield" id="searchalert"></div>
                </div>
              </div>
            </li>
            <?php if(WEBSITETYPE==1){?>
              <li class="login"><span>
                <?php if(isset($this->session->userdata[base_url().'MEMBER_ID']) && !empty($this->session->userdata[base_url().'MEMBER_ID'])) { ?>
                  <a href="<?=FRONT_URL?>my-profile"><i class="fa fa-user-circle-o" style="font-size: 18px;"></i><span class="hidden-xs"> Profile</span></a>
                <?php }else{ ?>
                  <a href="javascript:void(0)" onclick="openloginmodal()"><i class="fa fa-sign-in"></i><span class="hidden-xs"> Login</span></a>
                <?php } ?></span>
              </li>
              <li class="dropdown cart">
              <?php if($this->agent->is_mobile()){ ?>
                <a href="<?=FRONT_URL."cart"?>"><div id="badge-cart-count" style="<?=(empty($viewcartproducts)?"display:none;":"")?>"><?=(!empty($viewcartproducts)?count($viewcartproducts):'')?></div><i class="fa fa-shopping-cart"></i></a>
              <?php }else{ ?>
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><div id="badge-cart-count" style="<?=(empty($viewcartproducts)?"display:none;":"")?>"><?=(!empty($viewcartproducts)?count($viewcartproducts):'')?></div><i class="fa fa-shopping-cart"></i></a>
                <ul class="dropdown-menu">
                  <li class="ml-n">
                    <table class="table" id="cartbox">
                      <tbody class="scroll-view-cart-box customscroll chromescroll">
                        <?php if(!empty($viewcartproducts)){ 
                          $subtotal = 0;
                          foreach($viewcartproducts as $cart){ 
                            if ($cart['discount'] != '' && $cart['discount'] != 0) {
                              $price = ($cart['price'] - ($cart['price'] * $cart['discount'] / 100));
                            } else {
                              $price = $cart['price'];
                            }
                            $subtotal = $subtotal + ($price * $cart['quantity']);
                            $productname = $cart['name']." ".$cart['variantname'];
                            ?>
                              <tr id="cart<?=$cart['productpriceid']?>">
                                <td class="text-center" width="28%">
                                  <a href="<?=FRONT_URL.'products/'.$cart['slug']?>"><img src="<?=PRODUCT.$cart['image']?>" class="img-responsive" alt="<?=$productname?>" title="<?=ucwords($productname)?>" /></a>
                                </td>
                                <td class="text-left pl-xs" width="55%">
                                
                                  <a href="<?=FRONT_URL.'products/'.$cart['slug']?>" title="<?=ucwords($productname)?>"><?=strlen($productname) > 35 ? substr(strip_tags(ucwords($productname)),0,35)."..." : ucwords($productname);?></a>
                                
                                  <p><?=$cart['quantity']?> x <?=CURRENCY_CODE.' '.numberFormat($price,2,',')?></p>
                                  <input type="hidden" class="viewcartprice" value="<?=number_format(($price * $cart['quantity']),2,'.','')?>">
                                </td>
                                <td class="text-right" width="5%">
                                  <button type="button" title="Remove" class="btn btn-danger btn-xs" onclick="deletecartproductbyheaderbox(<?=$cart['productpriceid']?>)"><i
                                      class="fa fa-close"></i></button>
                                </td>
                              </tr>
                          <?php }  ?>
                          <tr>
                            <td class="pull-left text-right" colspan="2" width="50%">
                              <p class="total">
                                  <strong>Subtotal : </strong>
                              </p>
                            </td>
                            <td class="pull-right text-right" colspan="1" width="50%">
                              <p class="total">
                                <span class=""><?=CURRENCY_CODE?> <span id="cartsubtotal"><?=numberFormat($subtotal,2,',')?></span></span>
                              </p>
                            </td>
                          </tr>
                        <?php }else{ ?>
                          <tr>
                            <td class="pull-left text-center" colspan="3" width="100%">
                              <p class="total" style="border:none;">
                                No items available in cart.
                              </p>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                    <div class="buttons">
                      <a href="<?=FRONT_URL."cart"?>" style="display: block;text-align: center;" class="btn-primary" type="button">View
                        Cart</a>
                      <a href="<?=FRONT_URL."checkout"?>" style="display: block;text-align: center;" class="btn-default" type="button">Checkout</a>
                    </div>
                  </li>
                </ul>
                <?php } ?>
              </li>
            <?php } ?>
          </ul>
          <div class="menubar visible-xs visible-sm">
            <span class="bars"></span>
          </div>
        </div>
          
        <!-- menu start here -->
        <div id="menu" class="pull-right">
          <nav class="navbar">
            <div class="collapse navbar-collapse navbar-ex1-collapse padd0">

              <ul class="nav navbar-nav text-right">
                <li class="dropdown <?php if($page=='home'){ echo 'active';}?>"><a href="<?=FRONT_URL?>">Home <span class="plus"></span></a></li>
                     
                <?php foreach ($frontendmainmenu as $mainmenurow) { 
                      $html = $submenuicon = $url = '';
                      $class = ($page==$mainmenurow['name'])?"active":"";

                      if($mainmenurow['submenuavailable']==1){
                        $html .= '<li class="dropdown '.$class.'">';
                        $submenuicon = '<span class="plus"></span>';
                      }else{
                        $html .= '<li class="dropdown '.$class.'">';
                      }


                      if(filter_var(urldecode($mainmenurow['url']), FILTER_VALIDATE_URL)){
                        $url = urldecode($mainmenurow['url']);
                      }else{
                        $url = (!empty($mainmenurow['url']))?FRONT_URL.$mainmenurow['url']:'javascript:void(0)';
                      }
                      $html .= '<a href="'.$url.'" title="'.$mainmenurow['name'].'">'.$mainmenurow['name'].' '.$submenuicon.'</a>';

                      if($mainmenurow['submenuavailable']==1){
                        $html .= '<div class="dropdown-menu repeating">
                                    <div class="dropdown-inner">
                                      <ul class="list-unstyled">';
                        foreach ($frontendsubmenu as $submenurow) {
                          if($submenurow['frontendmenuid']==$mainmenurow['id']){

                            if(filter_var(urldecode($submenurow['url']), FILTER_VALIDATE_URL)){
                              $url = urldecode($submenurow['url']);
                            }else{
                              $url = (!empty($submenurow['url']))?FRONT_URL.$submenurow['url']:'javascript:void(0)';
                            }

                            $html .= '<li><a title="'.$submenurow['name'].'" href="'.$url.'">'.$submenurow['name'].'</a></li>';
                          }
                        }
                        $html .= '</ul></div></div>';
                      }
                      $html .= '</li>';
                      echo $html;
                      
                    } ?> 
              </ul>
            </div>
          </nav>
        </div>
        <!-- menu end here -->
      </div>
    </div>
  </div>
</header>
<!-- header end here -->


<?php $arrSessionDetails = $this->session->userdata;
  if(!isset($arrSessionDetails[base_url().'MEMBER_ID'])){ ?>

      <div class="modal fade" id="loginpopupModal" tabindex="-1" role="dialog" aria-labelledby="loginpopupModal" aria-hidden="true">
      
        <div class="modal-dialog" role="document" style="width: 500px;">
          <div class="modal-content">
            <div class="modal-body" style="width: 100%;float: left;">  <!-- style="overflow-y: auto;max-height:400px;" -->
              <div class="col-sm-12 main-popup">  
              </div>
            </div>
            
          </div>
        </div>
      </div>

      <?php /* 
      <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModal" aria-hidden="true">
      
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <!-- <div class="modal-header" style="width: 100%;float: left;">
              <h5 class="modal-title col-md-8" id="exampleModalLabel">Login</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
              </button>
            </div> -->
              
            <div class="modal-body" style="width: 100%;float: left;">  <!-- style="overflow-y: auto;max-height:400px;" -->
              <div class="col-sm-12 main-popup">  
              </div>
            </div>
            
          </div>
        </div>
      </div>
 
      <div class="modal fade" id="forgotpasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotpasswordModal" aria-hidden="true">
      
        <div class="modal-dialog" role="document" style="width:450px;">
          <div class="modal-content">
            <div class="modal-header" style="width: 100%;float: left;">
              <h5 class="modal-title col-md-8" id="exampleModalLabel">Forgot Password</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
              </button>
            </div>
              
            <div class="modal-body" style="overflow-y: auto;max-height:400px;">
              <div class="col-sm-12 forpwd-popup">  
                <div id="forgotpassworderror" style="display: none;"></div>
                <form action="#" class="form-horizontal" id="forgotpassword-form">
                  <input id="forgotmemberid" name="forgotmemberid" type="hidden">
                  <div class="form-group mb-md" id="email_div">
                    <div class="col-md-12">
                      <label class="control-label" for="forgotEmail"><i class="fa fa-user fa-lg"></i> Email ID Or Mobile</label>
                      <input id="forgotEmail" class="form-control" name="forgotEmail" type="text" tabindex="1">
                    </div>
                  </div>
                  <div class="form-group mb-sm" id="otp_div" style="display:none;">
                    <div class="col-md-12">
                      <input id="otp" class="form-control" name="otp" type="text" tabindex="1" placeholder="Enter OTP">
                      <div style="position: absolute;top: 7px;right: 16px;" id="otptimerdiv">
                        <a href="javascript:void(0);" onclick="resendotp()" id="resendbtn" class="p-xs m-n" style="display:none;"><i class="fa fa-refresh" aria-hidden="true"></i> Resend</a>
                        <span style="color:#008000;padding: 0px 10px;" id="timer"></span></div>
                    </div>
                  </div>
                  <p style="color: #616161;font-size: 14px;">Enter your email or mobile and we will send you a OTP & link to reset your password.</p>
                  <div class="form-group mb-n defaultbtn text-center">
                    <div class="col-md-12 col-xs-12">
                      <a href="javascript:void(0);" onclick="forgotpassword()" id="btnsubmitforpwd" class="btn btn-primary btn-raised">Submit</a>
                      <a href="javascript:void(0);" onclick="$('#forgotpasswordModal').modal('hide');openloginmodal()" class="btn btn-primary btn-raised">Login</a>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModal" aria-hidden="true">
      
        <div class="modal-dialog" role="document" style="width:550px;">
          <div class="modal-content">
            <div class="modal-header" style="width: 100%;float: left;">
              <h5 class="modal-title col-md-8" id="exampleModalLabel">Registration</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="fa fa-times"></i></span>
              </button>
            </div>
              
            <div class="modal-body pt-xs" style="overflow-y: auto;max-height:400px;">
              <div class="col-sm-12 registration-popup">  
                <div id="registrationerror" style="display: none;"></div>
                  <form action="#" class="form-horizontal" id="memberregistrationform">
                    <input id="regmemberchannelid" name="regmemberchannelid" type="hidden" value="<?=CUSTOMERCHANNELID?>">
                    <div class="col-md-12 p-n"> 
                      <div class="form-group">
                        <div class="col-md-12">
                          <label class="control-label" for="regmembername">Name <span class="mandatoryfield">*</span></label>
                          <input type="text" name="regmembername" id="regmembername" placeholder="Your Name" class="form-control" value="">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 p-n"> 
                      <div class="form-group">
                        <div class="col-md-12">
                          <label class="control-label" for="regmemberemail">Email <span class="mandatoryfield">*</span></label>
                          <input type="text" name="regmemberemail" id="regmemberemail" placeholder="Your Email" class="form-control" value="">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 p-n">
                      <div class="form-group mb-md">
                        <label class="control-label col-md-12 col-xs-12" for="regmembermobile" style="text-align: left;">Mobile No. <span class="mandatoryfield">*</span></label>
                        <div class="col-md-3 col-xs-3 pr-n">
                            <select id="regcountrycode" name="regcountrycode" class="selectpicker form-control" data-live-search="true" data-select-on-tab="true" data-size="5">
                                <option value="0">Country Code</option>
                                <?php foreach($countrycodedata as $row){ ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['phonecodewithname']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-9 col-xs-9">
                          <input type="text" name="regmembermobile" id="regmembermobile" placeholder="Your Mobile No." class="form-control" maxlength="10" onkeypress="return isNumber(event)">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12 p-n">
                      <div class="col-md-6 pr-xs pl-n">
                        <div class="form-group mb-md">
                          <div class="col-md-12">
                            <label class="control-label" for="regmemberpasssword">Password <span class="mandatoryfield">*</span></label>
                            <input type="password" name="regmemberpasssword" id="regmemberpasssword" placeholder="Password" class="form-control">
                            <i class="fa fa-eye eye fa-lg" aria-hidden="true" onClick="showPassword('regmemberpasssword')"></i>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6 pl-xs pr-n">
                        <div class="form-group mb-md">
                          <div class="col-md-12">
                            <label class="control-label" for="regmembergstno">GST NO.</label>
                            <input type="text" name="regmembergstno" id="regmembergstno" placeholder="GST No." class="form-control" maxlength="15" style="text-transform: uppercase;">
                          </div>
                        </div>
                      </div>
                    </div>
                    <hr>
                    <div class="form-group mb-n defaultbtn text-center">
                      <div class="col-md-12 col-xs-12">
                        <a href="javascript:void(0);" onclick="registration()" id="btnsubmitregister" class="btn btn-primary btn-raised">Register</a>
                        <a href="javascript:void(0);" onclick="$('#registrationModal').modal('hide');openloginmodal()" class="btn btn-primary btn-raised">Login</a>
                      </div>
                    </div>
                  </form>
              </div>
            </div>
            
          </div>
        </div>
      </div>
        */ ?>
<script>
  function setloginmodalvalue(type){
    $.html = "";

    $.html += '<div class="row"> \
              <div class="wrap-login100 p-xxl pt-xl"> \
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> \
                <span aria-hidden="true"><i class="fa fa-times"></i></span> \
                </button> \
                \
                <form action="#" class="login100-form validate-form" id="loginform"> \
                  <span class="login100-form-title p-b-31">Login</span> \
                  <div id="loginerror" style="display: none;"></div> \
                  <div class="col-md-12 col-xs-12 mb-md p-n"> \
                    <div id="loginEmail_div"> \
                      <label class="label-input100" for="loginEmail"></i> Email ID Or Mobile</label> \
                      <input id="loginEmail" class="input100" name="loginEmail" type="text" tabindex="1"> \
                      <span class="focus-input100"></span> \
                    </div> \
                  </div> \
                  <div class="col-md-12 col-xs-12 mb-md p-n"> \
                    <div id="loginPassword_div">\
                      <label class="label-input100" for="loginPassword">Password</label> \
                      <input type="password" class="input100" name="loginPassword" id="loginPassword" tabindex="2"> \
                      <span class="focus-input100"></span> \
                      <i class="fa fa-eye eye fa-lg" aria-hidden="true" onClick="showPassword(&apos;loginPassword&apos;)"></i> \
                    </div>\
                  </div>\
                  <div class="text-right p-t-8 p-b-20"> \
                    <a href="javascript:void(0);" onclick="openforgotpasswordmodal();">Forgot Password?</a> \
                  </div> \
                  <div class="container-login100-form-btn"> \
                    <div class="defaultbtn" style="width:100%"> \
                      <a href="javascript:void(0);" onclick="login();" id="btnloginsubmit" class="btn btn-primary btn-block">Login</a> \
                    </div> \
                  </div> \
                  <div class="col-md-6 col-xs-12"> \
                    <div class="txt1 text-center pt-xl p-b-20"> \
                      <span>Or Sign Up Using</span> \
                    </div> \
                    <div class="flex-c-m"> \
                      <a href="<?=$facebookauthUrl?>" class="login100-social-item bg1"><i class="fa fa-facebook"></i></a> \
                      <a href="<?=$googleauthUrl?>" class="login100-social-item bg3"><i class="fa fa-google"></i></a> \
                    </div> \
                  </div> \
                  <div class="col-md-6 col-xs-12"> \
                    <div class="txt1 text-center pt-xl p-b-20"> \
                      <span>Or Sign Up Using</span> \
                    </div> \
                    <div class="flex-c-m"> \
                      <a class="txt2" href="javascript:void(0);" onclick="openregistrationmodal()">Sign up</a> \
                    </div> \
                  </div> \
                </form>\
                </div> \
              </div>';
    /* $.html += '<div class="row"> \
                <div class="col-sm-12 left-side login"> \
                <div id="loginerror" style="display: none;"></div> \
                <form action="#" class="form-horizontal" id="loginform"> \
                  <div class="form-group mb-md" id="email_div"> \
                    <div class="col-md-12"> \
                      <label class="control-label" for="loginEmail"><i class="fa fa-user fa-lg"></i> Email ID Or Mobile</label> \
                      <input id="loginEmail" class="form-control" name="loginEmail" type="text" tabindex="1"> \
                    </div> \
                  </div> \
                  <div class="form-group mb-md" id="password_div"> \
                    <div class="col-md-12"> \
                      <label class="control-label" for="loginPassword"><i class="fa fa-lock fa-lg"></i> Password</label> \
                      <input type="password" class="form-control" name="loginPassword" id="loginPassword" tabindex="2"> \
                      <i class="fa fa-eye eye fa-lg" aria-hidden="true" onClick="showPassword(&apos;loginPassword&apos;)"></i> \
                    </div> \
                  </div> \
                  <div class="form-group mb-n"> \
                    <div class="col-xs-12"> \
                      <a href="javascript:void(0);" onclick="openforgotpasswordmodal();" class="pull-left">Forgot Password?</a> \
                    </div> \
                  </div> \
                  <div class="form-group mb-n defaultbtn text-center"> \
                    <a href="javascript:void(0);" onclick="login();" id="btnloginsubmit" class="btn btn-primary btn-raised">Login</a> \
                    <a class="btn btn-primary" href="javascript:void(0);" onclick="openregistrationmodal()">Sign up</a> \
                  </div> \
                </form>\
                </div> \
              </div>'; */
    $('.main-popup').html($.html);
    
    $("input").keypress(function(event) {
        if (event.which == 13){
          event.preventDefault();
          if($('#loginform').is(':visible')){
            login();
          }
          if($('#registrationform').is(':visible')){
            registration();
          }
        }
      });
      
      $(".eye").css("display", "none");

      $("#loginPassword").keyup(function(){
        if($(this).val()){
          $(this).parent().find("i").css("display","block");
        } else {
          $(this).parent().find("i").css("display","none");
        }
      });
  }
  function setpopupdata(type){
    $.html = "";
    var CUSTOMERCHANNELID = '<?=CUSTOMERCHANNELID?>';

    if(type=="forgotpassword"){
      $.html = '<div class="row"> \
                <div class="wrap-login100 p-xxl pt-xl"> \
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> \
                  <span aria-hidden="true"><i class="fa fa-times"></i></span> \
                  </button> \
                  \
                  <form action="#" class="login100-form validate-form" id="forgotpassword-form">\
                    <span class="login100-form-title p-b-31">Forgot Password</span> \
                    <div id="forgotpassworderror" style="display: none;"></div>\
                    <input id="forgotmemberid" name="forgotmemberid" type="hidden">\
                    \
                    <div class="col-md-12 col-xs-12 mb-md p-n"> \
                      <div id="forgotEmail_div"> \
                        <label class="label-input100" for="forgotEmail">Email ID Or Mobile</label>\
                        <input id="forgotEmail" class="input100" name="forgotEmail" type="text" tabindex="1"> \
                        <span class="focus-input100"></span> \
                      </div> \
                    </div> \
                    \
                    <div class="col-md-12 col-xs-12 mb-md p-n"> \
                      <div id="otp_div" style="position: relative;display:none;"> \
                        <input id="otp" class="input100" name="otp" type="text" tabindex="1" placeholder="Enter OTP"> \
                        <span class="focus-input100"></span> \
                        <div style="position: absolute;top: 10px;right: 0px;" id="otptimerdiv">\
                            <a href="javascript:void(0);" onclick="resendotp()" id="resendbtn" class="p-xs m-n" style="display:none;"><i class="fa fa-refresh" aria-hidden="true"></i> Resend</a>\
                            <span style="color:#008000;padding: 0px 10px;" id="timer"></span></div>\
                      </div> \
                    </div> \
                    \
                    <p class="mb-xl" style="color: #616161;font-size: 14px;">Enter your email or mobile and we will send you a OTP & link to reset your password.</p>\
                    \
                        <div class="col-md-6 col-sm-6 col-xs-12 mb-sm">\
                          <div class="defaultbtn"> \
                            <a href="javascript:void(0);" onclick="forgotpassword()" id="btnsubmitforpwd" class="btn btn-primary btn-block">Submit</a>\
                          </div> \
                        </div> \
                        <div class="col-md-6 col-sm-6 col-xs-12">\
                          <div class="defaultbtn"> \
                            <a href="javascript:void(0);" onclick="setloginmodalvalue(1)" class="btn btn-primary btn-block">Login</a>\
                          </div> \
                        </div> \
                      </div> \
                    </div> \
                  </form>\
                </div>\
              </div>';
    }else if(type=="registration"){
      var countrycodeoption = "";
    
      <?php foreach($countrycodedata as $row){ ?>
      countrycodeoption += '<option value="<?php echo $row['id']; ?>"><?php echo str_replace("'","",$row['phonecodewithname']); ?></option>';
      <?php } ?>
      $.html = '<div class="row"> \
                  <div class="wrap-login100 p-xxl pt-xl pb-md"> \
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> \
                    <span aria-hidden="true"><i class="fa fa-times"></i></span> \
                    </button> \
                    \
                    <form action="#" class="login100-form validate-form" id="memberregistrationform"> \
                      <span class="login100-form-title p-b-31">Registration</span> \
                      <div id="registrationerror" style="display: none;"></div> \
                      <input id="regmemberchannelid" name="regmemberchannelid" type="hidden" value="'+CUSTOMERCHANNELID+'"> \
                      \
                      <div class="col-md-12 col-xs-12 p-n mb-md"> \
                        <div id="regmembername_div"> \
                          <label class="label-input100" for="regmembername">Name <span class="mandatoryfield">*</span></label> \
                          <input type="text" name="regmembername" id="regmembername" class="input100" value=""> \
                          <span class="focus-input100"></span> \
                        </div> \
                      </div> \
                      \
                      <div class="col-md-12 col-xs-12 p-n mb-md"> \
                        <div id="regmemberemail_div"> \
                          <label class="label-input100" for="regmemberemail">Email <span class="mandatoryfield">*</span></label> \
                          <input type="text" name="regmemberemail" id="regmemberemail" class="input100" value=""> \
                          <span class="focus-input100"></span> \
                        </div> \
                      </div> \
                      \
                      <div class="col-md-12 col-xs-12 p-n mb-md"> \
                        <label class="col-md-12 col-xs-12 label-input100" for="regmembermobile">Mobile No. <span class="mandatoryfield">*</span></label> \
                        <div class="col-md-3 col-xs-4 pl-n"> \
                          <div class="col-md-12 col-xs-12 p-n" id="regcountrycode_div"> \
                            <select id="regcountrycode" name="regcountrycode" class="selectpicker input100" data-live-search="true" data-select-on-tab="true" data-size="5" style="display: block !important;"> \
                                <option value="0">Country Code</option> \
                                '+countrycodeoption+'\
                            </select> \
                            <span class="focus-input100"></span> \
                          </div> \
                        </div> \
                        <div class="col-md-9 col-xs-8 p-n"> \
                          <div class="col-md-12 col-xs-12 p-n" id="regmembermobile_div"> \
                            <input type="text" name="regmembermobile" id="regmembermobile" class="input100" maxlength="10" onkeypress="return isNumber(event)"> \
                            <span class="focus-input100"></span> \
                          </div> \
                        </div> \
                      </div> \
                      \
                      <div class="col-md-12 col-xs-12 p-n mb-md"> \
                        <div class="col-md-6 col-xs-12 pr-xs pl-n"> \
                          <div class="col-md-12 col-xs-12 p-n" id="regmemberpasssword_div">\
                            <label class="label-input100" for="regmemberpasssword">Password <span class="mandatoryfield">*</span></label> \
                            <input type="password" name="regmemberpasssword" id="regmemberpasssword" class="input100"> \
                            <span class="focus-input100"></span> \
                              <i class="fa fa-eye eye fa-lg" aria-hidden="true" onClick="showPassword(&apos;regmemberpasssword&apos;)"></i> \
                          </div>\
                        </div> \
                        <div class="col-md-6 col-xs-12 pl-xs pr-n"> \
                          <div class="col-md-12 col-xs-12 p-n" id="regmembergstno_div">\
                            <label class="label-input100" for="regmembergstno">GST NO.</label> \
                            <input type="text" name="regmembergstno" id="regmembergstno" class="input100" maxlength="15" style="text-transform: uppercase;"> \
                            <span class="focus-input100"></span> \
                          </div> \
                        </div> \
                      </div> \
                      \
                        <div class="col-md-6 col-sm-6 col-xs-12 mb-sm mt-sm">\
                          <div class="defaultbtn"> \
                            <a href="javascript:void(0);" onclick="registration()" id="btnsubmitregister" class="btn btn-primary btn-block">Register</a>\
                          </div> \
                        </div> \
                        <div class="col-md-6 col-sm-6 col-xs-12 mt-sm">\
                          <div class="defaultbtn"> \
                            <a href="javascript:void(0);" onclick="setloginmodalvalue(1)" class="btn btn-primary btn-block">Login</a>\
                          </div> \
                        </div> \
                      </form> \
                    </div> \
                  </div>';
    }
    $('.main-popup').html($.html);
  }
  function showPassword(element){
    if($("#"+element).attr('type') == "password"){
      $("#"+element).attr('type','text');
    } else if($("#"+element).attr('type') == "text") {
      $("#"+element).attr('type','password');
    }
    $("#"+element).parent().find("i").toggleClass("fa fa-eye-slash fa fa-eye eye");
  }
  let timerOn = true;
  function timer(remaining) {
    var m = Math.floor(remaining / 60);
    var s = remaining % 60;
    
    m = m < 10 ? '0' + m : m;
    s = s < 10 ? '0' + s : s;
    document.getElementById('timer').innerHTML = m + ':' + s;
    remaining -= 1;
    
    if(remaining >= 0 && timerOn) {
      setTimeout(function() {
          timer(remaining);
      }, 1000);
      return;
    }

    if(!timerOn) {
      // Do validate stuff here
      return;
    }
    
    // Do timeout stuff here
    $("#timer").hide();
    $("#resendbtn").show();
  }
  function resendotp(){
    $("#forgotEmail").prop("disabled",false);
    $("#resendbtn").hide();
    forgotpassword();
    $("#timer").show();
  }
  
</script>
<? } ?>