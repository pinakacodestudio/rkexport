<div class="static-sidebar">
    <div class="sidebar">
		<div class="widget" id="widget-profileinfo">
	        <div class="widget-body">
	            <div class="userinfo">
	                <div class="avatar pull-left">
	                    <?php if($this->session->userdata[base_url().'ADMINUSERIMAGE'] != ''){ 
							if(file_exists(PROFILE_PATH.$this->session->userdata[base_url().'ADMINUSERIMAGE'])){
								$src = PROFILE.$this->session->userdata[base_url().'ADMINUSERIMAGE'];
							}else{
								$src = DEFAULT_PROFILE.'noimage.png';
							}
							?>
					  		<img src="<?php echo $src; ?>" alt="" class="img-responsive img-circle logo" style="height: 100%;width: 100%;">
					  	<?php }else{ ?>
					  		<img src="<?php echo DEFAULT_PROFILE.'noimage.png'; ?>" alt="" class="img-responsive img-circle logo" style="height: 100%;width: 100%;">
					  	<?php } ?>
	                </div>
	                <div class="info">
	                    <span class="username"><?=ucwords($this->session->userdata[base_url().'ADMINNAME']);?></span>
	                    <span class="useremail"><?=$this->session->userdata[base_url().'ADMINEMAIL'];?></span>
	                </div>
	            </div>
	        </div>
	    </div>
		<div class="widget stay-on-collapse" id="widget-sidebar">
        	<nav role="navigation" class="widget-body">
				<ul class="sidebar-menu acc-menu">
					<!-- <li class="nav-separator"><span>Navigation</span></li> -->
					
					<?php
						foreach($mainnavdata as $mrow){
		  			?>
						<?php if($mrow['menuurl']!=''){ ?>
							<li class="<?php if($this->session->userdata[base_url().'mainmenuid'] == $mrow['id']){ echo 'active'; } ?>">
								<a href="<?php if($mrow['menuurl'] != ""){ echo base_url().ADMINFOLDER.$mrow['menuurl']; }else{ echo '#';}?>" title="<?php echo $mrow['name']; ?>" class="withripple">
									<span class="icon">
										<?php if($mrow['icon'] != strip_tags($mrow['icon'])) {
											echo $mrow['icon'];
										}else{ ?>
											<i class="<?php if($mrow['icon'] == ""){ echo 'fa fa-plus-circle'; }else{ echo $mrow['icon']; } ?>"></i>
										<?php } ?>
									</span>
									<span><?php echo $mrow['name']; ?></span>
								</a>
							</li>	
						<?php }else{ ?>
							<li class="treeview <?php if($this->session->userdata[base_url().'mainmenuid'] == $mrow['id']){ echo 'active open'; } ?>">
								<a class="withripple <?php if($this->session->userdata[base_url().'mainmenuid'] == $mrow['id']){ echo ''; } ?>">
									<span class="icon">
										<?php if($mrow['icon'] != strip_tags($mrow['icon'])) {
											echo $mrow['icon'];
										}else{ ?>
											<i class="<?php if($mrow['icon'] == ""){ echo 'fa fa-plus-circle'; }else{ echo $mrow['icon']; } ?>"></i>
										<?php } ?>
									</span>
									<span><?php echo $mrow['name']; ?></span>
								</a>	
								<ul class="treeview-menu menu-open acc-menu" style="display:<?php if($this->session->userdata[base_url().'mainmenuid'] == $mrow['id']){ echo 'block'; }else{ echo 'none'; } ?>">    	
								  	<?php
										foreach($subnavdata as $srow){
											if($srow['mainmenuid'] == $mrow['id']){
												if($srow['url']!=''){
								  				?>
									  
								  				<li class="treeview <?php if($this->session->userdata[base_url().'submenuid'] == $srow['id']){ echo 'active'; } ?>">
													<a class="withripple" href="<?php echo base_url().ADMINFOLDER.$srow['url']; ?>" title="<?php echo $srow['name']; ?>">
													
													<?php echo $srow['name']; 
													if($srow['name']=='Product Mapping' && isset($badges) && $badges['mappingcount']!=0){
														echo ' <span class="badge badge-warning">'.$badges['mappingcount'].'</span>';
													}else if($srow['name']=='Product' && isset($badges) && $badges['remainingmapping']!=0){
														echo ' <span class="badge badge-warning">'.$badges['remainingmapping'].'</span>';
													}else if($srow['url']=='crm-inquiry' && INQUIRY_COUNT!=0){
														echo ' <span class="badge badge-success fs-13">'.INQUIRY_COUNT.'</span>';
													}else if($srow['url']=='daily-followup' && FOLLOWUP_COUNT!=0){
														echo ' <span class="badge badge-success fs-13">'.FOLLOWUP_COUNT.'</span>';
													} ?>
					  								</a>
					  							</li>
				  					<?php }else{ ?>
										<li class="treeview <?php if($this->session->userdata[base_url().'submenuid'] == $srow['id']){ echo 'active'; } ?>">
											<a class="withripple">
												<span><?= $srow['name']?></span>
											</a>
											<ul class="treeview-menu acc-menu" style="display:none">
												<?php foreach($thirdlevelsubnav as $trow){ 
													if($trow['submenuid'] == $srow['id']){ ?>
													<li class="treeview <?php if($this->session->userdata[base_url().'thirdlevelsubmenuid'] == $trow['id']){ echo 'active'; } ?>">
														<a class="withripple" title="<?php echo $trow['name']; ?>" href="<?=base_url().ADMINFOLDER.$trow['url']?>"><?php echo $trow['name']; ?></a>
													</li>

												<?php } } ?>
											</ul>
										</li>
									<?php  } } } ?>
								</ul>
							</li>
			
						<?php } }?>
						
				</ul>
			</nav>
    	</div>
	</div>
</div>