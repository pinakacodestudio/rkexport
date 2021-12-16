<!-- slider start here -->
<link rel="stylesheet" href="assets/css/min.css" />
<div class="process-bg" style="<?php if(!empty($coverimage)){ echo  "background-image: url(".FRONTMENU_COVER_IMAGE.$coverimage.");"; }else{ echo "background-color:".DEFAULT_COVER_IMAGE_COLOR.";"; } ?>" aria-label="<?=$coverimage?>">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<h1>Contact Us</h1>
                <ul class="breadcrumbs list-inline">
					<li><a href="<?=FRONT_URL?>">Home</a></li>
					<li>Contact Us</li>
				</ul>
			</div>
		</div>
	</div>
</div>
<!-- slider end here -->
<!-- contact section -->
<div class="contact-classical">
  
    <div class="contact-simple">
        <div class="container">
            <div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12 get_main">
  	                <h4>GET IN TOUCH</h4>
                  	<div class="row infobox">
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h3>Our Address</h3>
									<p><?=ucfirst(COMPANY_ADDRESS)?></p>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12">
									<h3>Our Phone</h3>
									<?php $mobileArr = explode(",", COMPANY_MOBILENO); 
										foreach($mobileArr as $mobile){ ?>
											<p>(+91) <?=$mobile?>&nbsp;&nbsp;&nbsp;<a href="tel:+91<?=$mobile?>">Call Us</a></p>
									<?php } ?>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<h3>Our Support</h3>
									<?php $emailArr = explode(",", COMPANY_EMAIL); 
										foreach($emailArr as $email){ ?>
											<p>Support : <?=$email?>&nbsp;&nbsp;&nbsp;<a href="mailto:<?=$email?>">Send a Message</a></p>
											
									<?php } ?>
								</div>
							</div>
						</div>
                  	</div>
				</div>

				<div class="col-md-6 col-sm-6 col-xs-12 contact_form_main">
					<form id="contact-form" method="POST" class="form-horizontal" name="contact-form">
						<h4>CONTACT US</h4>
						<p>Please share your contact details and our business expert will get in touch with you shortly. Your personal information is <strong>100%</strong> secure with us, we never share your details with anyone.</p>
						<div id="contactformerror"></div>
						<div class="form-group">
                        	<div class="col-md-6 col-xs-12">
								<label class="control-label" for="name">Name<span class="mandatoryfield">*</span></label>					
								<input name="contactname" id="contactname" class="form-control" type="text" onkeypress="return onlyAlphabets(event)"  maxlength="30">
                        	</div>
                        	<div class="col-md-6 col-xs-12">
								<label class="control-label" for="customerphone">Mobile No.<span class="mandatoryfield">*</span></label>
								<input  type="text" name="customerphone" id="contactno" type="text" class="form-control" maxlength="10" onkeypress="return isNumber(event)">
                        	</div>
						</div>
						
						<div class="form-group">
                        	<div class="col-md-12 col-xs-12">
								<label class="control-label" for="contactemail">Email Address<span class="mandatoryfield">*</span></label>
								<input name="contactemail" id="contactemail" type="text" class="form-control">
                        	</div>
						</div>
						
						<div class="form-group">
                        	<div class="col-md-12 col-xs-12">
								<label class="control-label" for="customerfeedback">Message</label>			
								<textarea name="customerfeedback" id="message" class="form-control"></textarea>			
                        	</div>
						</div>
						<div class="text-center">
                      		<input class="btn-block btn btn-primary" type="button" onclick="submitcontectus()" value="Submit" />
                   		</div>
					</form>
				</div>
			</div>
        </div>
	</div>
	
	<div class="map">
		<?=GOOGLE_MAP_IFRAME?>
	</div>
</div>