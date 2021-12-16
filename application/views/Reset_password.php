<!-- slider start here -->
<div class="process-bg" style="padding: 100px 0 0px !important;background-color:<?=DEFAULT_COVER_IMAGE_COLOR?>;">
</div>
<!-- slider end here -->
<!-- contact section -->
<section class="beloved-clients">
	<div class="container">
	    <div class="col-md-12">
            <div class="row welcome_heading">
                <h2>Reset Your Password</h2>
            </div>
            <div class="col-md-4 col-md-offset-4">
                <div id="resetpassworderror" style="display: none;"></div>
            </div>
            <div class="col-md-4 col-md-offset-4">
                <form action="#" class="form-horizontal" id="resetpasswordform">
                    <input type="hidden" name="resetmemberid" id="resetmemberid" value="<?php echo $resetdata['memberid']; ?>">
                    <input type="hidden" name="verifiedid" id="verifiedid" value="<?php echo $resetdata['id']; ?>">
                    <div class="form-group mb-md" id="newpassword_div">
                        <div class="col-md-12">
                            <div class="col-md-12 p-n">
                                <label class="control-label" for="password">New Password</label>
                                <input id="password" class="input100" name="password" type="password">
                                <span class="focus-input100"></span>
                                <i class="fa fa-eye eye fa-lg" aria-hidden="true" onClick="showPassword('password')"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-md" id="confirmpassword_div">
                        <div class="col-md-12">
                            <div class="col-md-12 p-n">
                                <label class="control-label" for="confirmpassword">Confirm Password</label>
                                <input id="confirmpassword" class="input100" name="confirmpassword" type="password">
                                <span class="focus-input100"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center mt-md defaultbtn">
                        <div class="col-md-12">
                            <a href="javascript:void(0);" onclick="checkresetpassword()" id="btnsubmit" class="btn btn-primary" style="width: 100%">Reset Password</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>  
    </div>  
</section>
