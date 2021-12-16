<script>
/* var memberid = '<?php //if(isset($userroledata)){ echo $userroledata['memberid']; }  ?>'; */
	var roletype = '<?php if(isset($roletype)){ echo $roletype; } ?>';
</script>
<div class="page-content">
	<div class="page-heading">            
        <h1><?php if(isset($userroledata) && !isset($roletype)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo base_url().ADMINFOLDER; ?><?=$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>
    </div>

    <div class="container-fluid">
                                    
      	<div data-widget-group="group1">
		  <div class="row">
		    <div class="col-md-12">
		      <div class="panel panel-default border-panel">
		        <div class="panel-body">
		        	<div class="col-sm-12 col-md-12 col-lg-12">
					<form class="form-horizontal" id="formuserrole">
						<input type="hidden" name="userroleid" value="<?php if(isset($userroledata) && !isset($roletype)){ echo $userroledata['id']; } ?>">
						<div class="form-group" id="userrole_div">
							<label for="userrole" class="col-sm-2 control-label">Employee Role <span class="mandatoryfield">*</span></label>
							<div class="col-sm-6">
								<input id="userrole" type="text" name="userrole" value="<?php if(!empty($userroledata) && !isset($roletype)){ echo $userroledata['role']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
							</div>
						</div>

						<?php
							foreach($mainmenudata as $mainrow){
								
								$mainmenucheckvisible = '';
								$mainmenucheckadd = '';
								$mainmenucheckedit = '';
								$mainmenucheckdelete = '';
								$mainmenucheckviewalldata = '';
								$mainmenurights = '';
								$selectedmainmenurights = array();

								if(!isset($userroledata))
								{
									if($mainrow['inorder'] == 0){
										$mainmenucheckvisible = 'checked';
										$mainmenucheckadd = 'checked';
										$mainmenucheckedit = 'checked';
										$mainmenucheckdelete = 'checked';
										$mainmenucheckviewalldata = 'checked';
									}
								}
							?>
								<div class="form-group">
									<label class="col-sm-2 control-label"><span style="font-weight:bold;"><?php echo $mainrow['name']; ?></span></label>
									<div class="col-sm-6">
										<?php
											if(isset($userroledata)){
												$mainmenuvisible=explode(",",$mainrow['menuvisible']);
												foreach($mainmenuvisible as $mmv){
													if($mmv==$userroledata['id']){
														$mainmenucheckvisible = 'checked';
													}
												}
												$mainmenuadd=explode(",",$mainrow['menuadd']);
												foreach($mainmenuadd as $mma){
													if($mma==$userroledata['id']){
														$mainmenucheckadd = 'checked';
													}
												}
												$mainmenuedit=explode(",",$mainrow['menuedit']);
												foreach($mainmenuedit as $mme){
													if($mme==$userroledata['id']){
														$mainmenucheckedit = 'checked';
													}
												}
												$mainmenudelete=explode(",",$mainrow['menudelete']);
												foreach($mainmenudelete as $mmd){
													if($mmd==$userroledata['id']){
														$mainmenucheckdelete = 'checked';
													}
												}
												$mainmenuviewalldata=explode(",",$mainrow['menuviewalldata']);
												foreach($mainmenuviewalldata as $mmv){
													if($mmv==$userroledata['id']){
														$mainmenucheckviewalldata = 'checked';
													}
												}
												$mainmenurights=json_decode($mainrow['assignadditionalrights'],true);
												if(!empty($mainmenurights[$userroledata['id']])){
													$mainmenurights=str_replace("#","",$mainmenurights[$userroledata['id']]);
													$selectedmainmenurights = explode(',',$mainmenurights);
												}else{
													$mainmenurights = '0';
													$selectedmainmenurights = explode(',',$mainmenurights);
												}
											}

											if(!empty($submenudata)){
												$keys_submenu = array_keys(array_combine(array_keys($submenudata), array_column($submenudata, 'mainmenuid')),$mainrow['id']);
												$count_submenuvisible=$count_submenuadd=$count_submenuedit=$count_submenudelete=$count_submenuviewalldata=0;
												
												foreach($keys_submenu as $key){
													if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$submenudata[$key]['submenuvisible']))){
														$count_submenuvisible++;
													}
													if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$submenudata[$key]['submenuadd']))){
														$count_submenuadd++;
													}
													if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$submenudata[$key]['submenuedit']))){
														$count_submenuedit++;
													}
													if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$submenudata[$key]['submenudelete']))){
														$count_submenudelete++;
													}
													if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$submenudata[$key]['submenuviewalldata']))){
														$count_submenuviewalldata++;
													}
												}
											}
										?>
								
										<div class="checkbox col-sm-2">
											<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$mainrow['menuvisible'])) || (isset($count_submenuvisible) && $count_submenuvisible!=0 && $mainrow['menuurl']=='')){?>

												<input type="checkbox" name="mainmenu1[]" id="mainmenu_<?php echo $mainrow['id']; ?>_1" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckvisible; ?>>
												<label for="mainmenu_<?php echo $mainrow['id']; ?>_1"><strong>Visible</strong></label>
											<?php  } ?>
										</div>

										<div class="checkbox col-sm-2">
											<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$mainrow['menuadd'])) || (isset($count_submenuadd) && $count_submenuadd!=0 && $mainrow['menuurl']=='')){?>

												<input type="checkbox" name="mainmenu2[]" id="mainmenu_<?php echo $mainrow['id']; ?>_2" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckadd; ?>>
												<label for="mainmenu_<?php echo $mainrow['id']; ?>_2"><strong>Add</strong></label>
											<?php  } ?>
										</div>
										
										<div class="checkbox col-sm-2">
											<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$mainrow['menuedit'])) || (isset($count_submenuedit) && $count_submenuedit!=0 && $mainrow['menuurl']=='')){?>
												<input type="checkbox" name="mainmenu3[]" id="mainmenu_<?php echo $mainrow['id']; ?>_3" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckedit; ?>>
												<label for="mainmenu_<?php echo $mainrow['id']; ?>_3"><strong>Edit</strong></label>
											<?php  } ?>
										</div>

										<div class="checkbox col-sm-2">
											<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$mainrow['menudelete'])) || (isset($count_submenudelete) && $count_submenudelete!=0 && $mainrow['menuurl']==''))	{?>

												<input type="checkbox" name="mainmenu4[]" id="mainmenu_<?php echo $mainrow['id']; ?>_4" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckdelete; ?>>
												<label for="mainmenu_<?php echo $mainrow['id']; ?>_4"><strong>Delete</strong></label>
											<?php  } ?>
										</div>

										<div class="checkbox col-sm-3">
											<input type="checkbox" name="mainmenu5[]" id="mainmenu_<?php echo $mainrow['id']; ?>_5" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckviewalldata; ?>>
											<label for="mainmenu_<?php echo $mainrow['id']; ?>_5"><strong>View All</strong></label>
										</div>
									</div>
									<div class="col-sm-4">
										<?php if($mainrow['additionalrights']!=''){ 
											
											if($mainrow['menuurl']!=''){
											?>
											<input type="hidden" name="oldmainmenurights[<?=$mainrow['id']?>]" value="<?=$mainrow['id']?>">
											<select id="rightsid" name="mainmenurights[<?=$mainrow['id']?>][]" class="selectpicker form-control" data-live-search="true" data-size="5" multiple data-actions-box="true" title="Select Additional Rights">
												<?php 
												$additionalrightsidarr = explode(',',$mainrow['additionalrights']);
												foreach($additionalrightsidarr as $rights){ 
													$key = array_search($rights, array_column($additionalrightsdata, 'id'));
													if(!empty($additionalrightsdata[$key])){ ?>
														<option value="#<?php echo $additionalrightsdata[$key]['id']; ?>" <?php if(!empty($selectedmainmenurights)){ if(in_array($additionalrightsdata[$key]['id'],$selectedmainmenurights)){ echo "selected"; } } ?>><?php echo $additionalrightsdata[$key]['name']; ?></option>
													<?php } 
												}?>
											</select>
										<?php } } ?>		
									</div>
								</div>
								 <?php
								 	foreach($submenudata as $subrow){
										if($subrow['mainmenuid']==$mainrow['id']){
											$submenucheckvisible = '';
											$submenucheckadd = '';
											$submenucheckedit = '';
											$submenucheckdelete = '';
											$submenucheckviewalldata = '';

											if(!isset($userroledata))
											{
												if($mainrow['inorder'] == 0){
													$submenucheckvisible = 'checked';
													$submenucheckadd = 'checked';
													$submenucheckedit = 'checked';
													$submenucheckdelete = 'checked';
													$submenucheckviewalldata = 'checked';
												}
											}
											if(isset($userroledata)){
												$submenuvisible=explode(",",$subrow['submenuvisible']);
												foreach($submenuvisible as $smv){
													if($smv==$userroledata['id']){
														$submenucheckvisible = 'checked';
													}
												}
												$submenuadd=explode(",",$subrow['submenuadd']);
												foreach($submenuadd as $sma){
													if($sma==$userroledata['id']){
														$submenucheckadd = 'checked';
													}
												}
												$submenuedit=explode(",",$subrow['submenuedit']);
												foreach($submenuedit as $sme){
													if($sme==$userroledata['id']){
														$submenucheckedit = 'checked';
													}
												}
												$submenudelete=explode(",",$subrow['submenudelete']);
												foreach($submenudelete as $smd){
													if($smd==$userroledata['id']){
														$submenucheckdelete = 'checked';
													}
												}
												$submenuviewalldata=explode(",",$subrow['submenuviewalldata']);
												foreach($submenuviewalldata as $smv){
													if($smv==$userroledata['id']){
														$submenucheckviewalldata = 'checked';
													}
												}
												$submenurights=json_decode($subrow['assignadditionalrights'],true);
												
												if(!empty($submenurights[$userroledata['id']])){
													$submenurights=str_replace("#","",$submenurights[$userroledata['id']]);
													$selectedsubmenurights = explode(',',$submenurights);
												}else{
													$submenurights = '0';
													$selectedsubmenurights = explode(',',$submenurights);
												}
											}
											
											?>
											<div class="form-group">
												<label class="col-sm-2 control-label"><span <?=($subrow['url']=='')?'style="font-weight:bold;"':''?>><?php echo $subrow['name']; ?> </span><?=($subrow['url']=='')?'<span><i style="position:absolute;" class="material-icons">keyboard_arrow_down</i></span>':''?></label>
												<div class="col-sm-6" id="div<?php echo $mainrow['id']; ?>">
													<div class="checkbox col-sm-2">
														<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$subrow['submenuvisible']))){?>
															<input type="checkbox" name="submenu1[]" id="submenu_<?php echo $subrow['id']; ?>_1" value="<?php echo $subrow['id']; ?>" <?php if($subrow['submenuvisibleinrole']==0){ echo "disabled"; }else{ echo $submenucheckvisible; } ?>>
															<label for="submenu_<?php echo $subrow['id']; ?>_1">Visible</label>
														<?php } ?>
													</div>
													<div class="checkbox col-sm-2">
														<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$subrow['submenuadd']))){?>
															<input type="checkbox" name="submenu2[]" id="submenu_<?php echo $subrow['id']; ?>_2" value="<?php echo $subrow['id']; ?>" <?php if($subrow['submenuaddinrole']==0){ echo "disabled"; }else{ echo $submenucheckadd; } ?>>
															<label for="submenu_<?php echo $subrow['id']; ?>_2">Add</label>
														<?php } ?>
													</div>
													<div class="checkbox col-sm-2">
														<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode	(",",$subrow['submenuedit']))){?>
															<input type="checkbox" name="submenu3[]" id="submenu_<?php echo $subrow['id']; ?>_3" value="<?php echo $subrow['id']; ?>" <?php if($subrow['submenueditinrole']==0){ echo "disabled"; }else{ echo $submenucheckedit; } ?>>
															<label for="submenu_<?php echo $subrow['id']; ?>_3">Edit</label>
														<?php } ?>
													</div>
													<div class="checkbox col-sm-2">
														<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$subrow['submenudelete']))){?>
															<input type="checkbox" name="submenu4[]" id="submenu_<?php echo $subrow['id']; ?>_4" value="<?php echo $subrow['id']; ?>" <?php if($subrow['submenudeleteinrole']==0){ echo "disabled"; }else{ echo $submenucheckdelete; } ?>>
															<label for="submenu_<?php echo $subrow['id']; ?>_4">Delete</label>
														<?php } ?>
													</div>
													<div class="checkbox col-sm-3">
														<input type="checkbox" name="submenu5[]" id="submenu_<?php echo $subrow['id']; ?>_5" value="<?php echo $subrow['id']; ?>" <?php echo $submenucheckviewalldata;  ?>>
														<label for="submenu_<?php echo $subrow['id']; ?>_5">View All</label>
													</div>
												</div>
												<div class="col-sm-4">
													<?php if($subrow['additionalrights']!=''){ 
														
														if($subrow['url']!=''){
														?>
														<input type="hidden" name="oldsubmenurights[<?=$subrow['id']?>]" value="<?=$subrow['id']?>">
														<select id="rightsid" name="submenurights[<?=$subrow['id']?>][]" class="selectpicker form-control" data-live-search="true" data-size="5" multiple data-actions-box="true" title="Select Additional Rights">
															<?php 
															$additionalrightsidarr = explode(',',$subrow['additionalrights']);
															foreach($additionalrightsidarr as $rights){ 
																
																$key = array_search($rights, array_column($additionalrightsdata, 'id'));
																	
																if(!empty($additionalrightsdata[$key])){ ?>
															
																	<option value="#<?php echo $additionalrightsdata[$key]['id']; ?>" <?php if(!empty($selectedsubmenurights)){ if(in_array($additionalrightsdata[$key]['id'],$selectedsubmenurights)){ echo "selected"; } } ?>><?php echo $additionalrightsdata[$key]['name']; ?></option>
																
																<?php } 
															}?>
														</select>
													<?php } } ?>		
												</div>
											</div>
											<?php
												foreach($thirdlevelsubmenudata as $thirdsubmenu){
													if($thirdsubmenu['submenuid']==$subrow['id']){ 
														$thirdlevelsubmenucheckvisible = '';
														$thirdlevelsubmenucheckadd = '';
														$thirdlevelsubmenucheckedit = '';
														$thirdlevelsubmenucheckdelete = '';
														$thirdlevelsubmenucheckviewalldata = '';
														
														if(isset($userroledata)){
															$thirdlevelsubmenuvisible=explode(",",$thirdsubmenu['thirdlevelsubmenuvisible']);
															foreach($thirdlevelsubmenuvisible as $tsmv){
																if($tsmv==$userroledata['id']){
																	$thirdlevelsubmenucheckvisible = 'checked';
																}
															}
															$thirdlevelsubmenuadd=explode(",",$thirdsubmenu['thirdlevelsubmenuadd']);
															foreach($thirdlevelsubmenuadd as $tsma){
																if($tsma==$userroledata['id']){
																	$thirdlevelsubmenucheckadd = 'checked';
																}
															}
															$thirdlevelsubmenuedit=explode(",",$thirdsubmenu['thirdlevelsubmenuedit']);
															foreach($thirdlevelsubmenuedit as $tsme){
																if($tsme==$userroledata['id']){
																	$thirdlevelsubmenucheckedit = 'checked';
																}
															}
															$thirdlevelsubmenudelete=explode(",",$thirdsubmenu['thirdlevelsubmenudelete']);
															foreach($thirdlevelsubmenudelete as $tsmd){
																if($tsmd==$userroledata['id']){
																	$thirdlevelsubmenucheckdelete = 'checked';
																}
															}
															$thirdlevelsubmenuviewalldata=explode(",",$thirdsubmenu['thirdlevelsubmenuviewalldata']);
															foreach($thirdlevelsubmenuviewalldata as $tsmv){
																if($tsmv==$userroledata['id']){
																	$thirdlevelsubmenucheckviewalldata = 'checked';
																}
															}
															$thirdlevelsubmenurights=json_decode($thirdsubmenu['assignadditionalrights'],true);
												
															if(!empty($thirdlevelsubmenurights[$userroledata['id']])){
																$thirdlevelsubmenurights=str_replace("#","",$thirdlevelsubmenurights[$userroledata['id']]);
																$selectedthirdlevelsubmenurights = explode(',',$thirdlevelsubmenurights);
															}else{
																$thirdlevelsubmenurights = '0';
															}
														}
														?>
													<div class="form-group">
														<label class="col-sm-2 control-label"><span><?php echo $thirdsubmenu['name']; ?></span> <span><i style="position:absolute;" class="material-icons">keyboard_arrow_right</i></span></label>
														<div class="col-sm-6" id="div<?php echo $subrow['id']; ?>">
															<div class="checkbox col-sm-2">
																<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$thirdsubmenu['thirdlevelsubmenuvisible']))){?>
																	<input type="checkbox" name="thirdlevelsubmenu1[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_1" value="<?php echo $thirdsubmenu['id']; ?>" <?php if($thirdsubmenu['thirdlevelsubmenuvisibleinrole']==0){ echo "disabled"; }else{ echo $thirdlevelsubmenucheckvisible; } ?>>
																	<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_1">Visible</label>
																<?php } ?>
															</div>
															<div class="checkbox col-sm-2">
																<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$thirdsubmenu['thirdlevelsubmenuadd']))){?>
																	<input type="checkbox" name="thirdlevelsubmenu2[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_2" value="<?php echo $thirdsubmenu['id']; ?>" <?php if($thirdsubmenu['thirdlevelsubmenuaddinrole']==0){ echo "disabled"; }else{ echo $thirdlevelsubmenucheckadd; } ?>>
																	<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_2">Add</label>
																<?php } ?>
															</div>
															<div class="checkbox col-sm-2">
																<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode	(",",$thirdsubmenu['thirdlevelsubmenuedit']))){?>
																	<input type="checkbox" name="thirdlevelsubmenu3[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_3" value="<?php echo $thirdsubmenu['id']; ?>" <?php if($thirdsubmenu['thirdlevelsubmenueditinrole']==0){ echo "disabled"; }else{ echo $thirdlevelsubmenucheckedit; } ?>>
																	<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_3">Edit</label>
																<?php } ?>
															</div>
															<div class="checkbox col-sm-2">
																<?php if(in_array($this->session->userdata(base_url().'ADMINUSERTYPE'),explode(",",$thirdsubmenu['thirdlevelsubmenudelete']))){?>
																	<input type="checkbox" name="thirdlevelsubmenu4[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_4" value="<?php echo $thirdsubmenu['id']; ?>" <?php if($thirdsubmenu['thirdlevelsubmenudeleteinrole']==0){ echo "disabled"; }else{ echo $thirdlevelsubmenucheckdelete; } ?>>
																	<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_4">Delete</label>
																<?php } ?>
															</div>
															<div class="checkbox col-sm-3">
																<input type="checkbox" name="thirdlevelsubmenu5[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_5" value="<?php echo $thirdsubmenu['id']; ?>" <?php echo $thirdlevelsubmenucheckviewalldata;  ?>>
																<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_5">View All</label>
															</div>
														</div>
														<div class="col-sm-4">
															<?php if($thirdsubmenu['additionalrights']!=''){ 
																
																if($thirdsubmenu['url']!=''){
																?>
																<input type="hidden" name="oldthirdlevelsubmenurights[<?=$thirdsubmenu['id']?>]" value="<?=$thirdsubmenu['id']?>">
																<select id="rightsid" name="thirdlevelsubmenurights[<?=$thirdsubmenu['id']?>][]" class="selectpicker form-control" data-live-search="true" data-size="5" multiple data-actions-box="true" title="Select Additional Rights">
																	<?php 
																	$additionalrightsidarr = explode(',',$thirdsubmenu['additionalrights']);
																	foreach($additionalrightsidarr as $rights){ 
																		
																		$key = array_search($rights, array_column($additionalrightsdata, 'id'));
																		if(!empty($additionalrightsdata[$key])){ ?>
																			<option value="#<?php echo $additionalrightsdata[$key]['id']; ?>" <?php if(!empty($selectedthirdlevelsubmenurights)){ if(in_array($additionalrightsdata[$key]['id'],$selectedthirdlevelsubmenurights)){ echo "selected"; } } ?>><?php echo $additionalrightsdata[$key]['name']; ?></option>
																		
																		<?php } 
																	}?>
																</select>
															<?php } } ?>		
														</div>
													</div>	
												<?php	}
												}
												?>
								 <?php } } ?>
									<hr>
								<?php } ?>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
									<input type="radio" name="status" id="yes" value="1" <?php if(isset($userroledata) && !isset($roletype) && $userroledata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
									<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2 col-xs-6">
									<div class="radio">
									<input type="radio" name="status" id="no" value="0" <?php if(isset($userroledata) && !isset($roletype) && $userroledata['status']==0){ echo 'checked'; }?>>
									<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-4 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($userroledata) && !isset($roletype)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
								  <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
								  <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>user-role" title=<?=cancellink_title?>><?=cancellink_text?></a>
							</div>
						</div>
					</form>
				</div>
				</div>
		      </div>
		    </div>
		  </div>
		</div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->
<script type="text/javascript">
	$(document).ready(function () {
	    <?php
	      foreach($mainmenudata as $mrow){
	        foreach($submenudata as $srow){
	          if($srow['mainmenuid'] == $mrow['id']){
	    ?>
	    $("#mainmenu_<?php echo $mrow['id'] ?>_1").change(function () {
	      if(this.checked) {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_1:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_1:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_1:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_1:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_1").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['mainmenuid'] == $mrow['id']){
	        $str .= '$("#submenu_'.$svrow['id'].'_1").is(":checked") == true &&';
	      } }
	      $newstr = substr($str, 0, -2);
	      ?>
	      
	      if(<?php echo $newstr; ?>)
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_1").prop('checked', true);
	      }
	      
	      else
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_1").prop('checked', false);
	      }
	    });
	    
	    $("#mainmenu_<?php echo $mrow['id'] ?>_2").change(function () {
	      if(this.checked) {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_2:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>

	        $("#submenu_<?php echo $srow['id']; ?>_2:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_2:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>

	        $("#submenu_<?php echo $srow['id']; ?>_2:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_2").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['mainmenuid'] == $mrow['id']){
	        $str .= '$("#submenu_'.$svrow['id'].'_2").is(":checked") == true &&';
	      } }
	      $newstr = substr($str, 0, -2);
	      ?>
	      
	      if(<?php echo $newstr; ?>)
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_2").prop('checked', true);
	      }
	      
	      else
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_2").prop('checked', false);
	      }
	    });
	    
	    $("#mainmenu_<?php echo $mrow['id'] ?>_3").change(function () {
	      if(this.checked) {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_3:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_3:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_3:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_3:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_3").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['mainmenuid'] == $mrow['id']){
	        $str .= '$("#submenu_'.$svrow['id'].'_3").is(":checked") == true &&';
	      } }
	      $newstr = substr($str, 0, -2);
	      ?>
	      
	      if(<?php echo $newstr; ?>)
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_3").prop('checked', true);
	      }
	      
	      else
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_3").prop('checked', false);
	      }
	    });
	    
	    $("#mainmenu_<?php echo $mrow['id'] ?>_4").change(function () {
	      if(this.checked) {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_4:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>

	        $("#submenu_<?php echo $srow['id']; ?>_4:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_4:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_4:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_4").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['mainmenuid'] == $mrow['id']){
	        $str .= '$("#submenu_'.$svrow['id'].'_4").is(":checked") == true &&';
	      } }
	      $newstr = substr($str, 0, -2);
	      ?>
	      
	      if(<?php echo $newstr; ?>)
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_4").prop('checked', true);
	      }
	      
	      else
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_4").prop('checked', false);
	      }
		});

		$("#mainmenu_<?php echo $mrow['id'] ?>_5").change(function () {
	      if(this.checked) {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_5:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_5:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_5:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_5:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_5").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['mainmenuid'] == $mrow['id']){
	        $str .= '$("#submenu_'.$svrow['id'].'_5").is(":checked") == true &&';
	      } }
	      $newstr = substr($str, 0, -2);
	      ?>
	      
	      if(<?php echo $newstr; ?>)
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_5").prop('checked', true);
	      }
	      
	      else
	      {
	        $("#mainmenu_<?php echo $mrow['id']; ?>_5").prop('checked', false);
	      }
	    });

		<?php
	        foreach($thirdlevelsubmenudata as $trow){
	          if($trow['submenuid'] == $srow['id']){
	    ?>
		$("#submenu_<?php echo $srow['id'] ?>_1").change(function () {
	      if(this.checked) {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_1:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_1:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#thirdlevelsubmenu_<?php echo $trow['id'] ?>_1").click(function () {
	      <?php
			$chek = $mainmenustr = '';
			foreach($thirdlevelsubmenudata as $tsvrow){
				if($tsvrow['submenuid'] == $srow['id']){
				$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_1").is(":checked") == true && ';
			} }
			foreach($submenudata as $svrow){
				if($svrow['mainmenuid'] == $mrow['id']){
				$mainmenustr .= '$("#submenu_'.$svrow['id'].'_1").is(":checked") == true && ';
			  } }
			  $newmainmenustr = substr($mainmenustr, 0, -3);
			$allchecked = substr($chek, 0, -3);
	      ?>
			if(<?php echo $allchecked; ?>){
				$("#submenu_<?php echo $srow['id']; ?>_1").prop('checked', true);
			}else{
				$("#submenu_<?php echo $srow['id']; ?>_1").prop('checked', false);
			}
			if(<?php echo $newmainmenustr; ?>){
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_1").prop('checked', true);
			}else{
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_1").prop('checked', false);
	      	}
	    });

		$("#submenu_<?php echo $srow['id'] ?>_2").change(function () {
	      if(this.checked) {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_2:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_2:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#thirdlevelsubmenu_<?php echo $trow['id'] ?>_2").click(function () {
	      <?php
			$chek = $mainmenustr = '';
			foreach($thirdlevelsubmenudata as $tsvrow){
				if($tsvrow['submenuid'] == $srow['id']){
				$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_2").is(":checked") == true && ';
			} }
			foreach($submenudata as $svrow){
				if($svrow['mainmenuid'] == $mrow['id']){
				$mainmenustr .= '$("#submenu_'.$svrow['id'].'_2").is(":checked") == true && ';
			  } }
			  $newmainmenustr = substr($mainmenustr, 0, -3);
			$allchecked = substr($chek, 0, -3);
	      ?>
			if(<?php echo $allchecked; ?>){
				$("#submenu_<?php echo $srow['id']; ?>_2").prop('checked', true);
			}else{
				$("#submenu_<?php echo $srow['id']; ?>_2").prop('checked', false);
			}
			if(<?php echo $newmainmenustr; ?>){
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_2").prop('checked', true);
			}else{
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_2").prop('checked', false);
	      	}
	    });

		$("#submenu_<?php echo $srow['id'] ?>_3").change(function () {
	      if(this.checked) {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_3:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_3:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#thirdlevelsubmenu_<?php echo $trow['id'] ?>_3").click(function () {
	      <?php
			$chek = $mainmenustr = '';
			foreach($thirdlevelsubmenudata as $tsvrow){
				if($tsvrow['submenuid'] == $srow['id']){
				$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_3").is(":checked") == true && ';
			} }
			foreach($submenudata as $svrow){
				if($svrow['mainmenuid'] == $mrow['id']){
				$mainmenustr .= '$("#submenu_'.$svrow['id'].'_3").is(":checked") == true && ';
			  } }
			  $newmainmenustr = substr($mainmenustr, 0, -3);
			$allchecked = substr($chek, 0, -3);
	      ?>
			if(<?php echo $allchecked; ?>){
				$("#submenu_<?php echo $srow['id']; ?>_3").prop('checked', true);
			}else{
				$("#submenu_<?php echo $srow['id']; ?>_3").prop('checked', false);
			}
			if(<?php echo $newmainmenustr; ?>){
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_3").prop('checked', true);
			}else{
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_3").prop('checked', false);
	      	}
	    });

		$("#submenu_<?php echo $srow['id'] ?>_4").change(function () {
	      if(this.checked) {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_4:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_4:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#thirdlevelsubmenu_<?php echo $trow['id'] ?>_4").click(function () {
	      <?php
			$chek = $mainmenustr = '';
			foreach($thirdlevelsubmenudata as $tsvrow){
				if($tsvrow['submenuid'] == $srow['id']){
				$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_4").is(":checked") == true && ';
			} }
			foreach($submenudata as $svrow){
				if($svrow['mainmenuid'] == $mrow['id']){
					$mainmenustr .= '$("#submenu_'.$svrow['id'].'_4").is(":checked") == true && ';
				} }
			  $newmainmenustr = substr($mainmenustr, 0, -3);
			$allchecked = substr($chek, 0, -3);
	      ?>
			if(<?php echo $allchecked; ?>){
				$("#submenu_<?php echo $srow['id']; ?>_4").prop('checked', true);
			}else{
				$("#submenu_<?php echo $srow['id']; ?>_4").prop('checked', false);
			}
			if(<?php echo $newmainmenustr; ?>){
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_4").prop('checked', true);
			}else{
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_4").prop('checked', false);
	      	}
	    });

		$("#submenu_<?php echo $srow['id'] ?>_5").change(function () {
	      if(this.checked) {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_5:not(:disabled)").prop('checked', true);
	      }
	      else
	      {
	        $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_5:not(:disabled)").prop('checked', false);
	      }
	    });
	    $("#thirdlevelsubmenu_<?php echo $trow['id'] ?>_5").click(function () {
	      <?php
			$chek = $mainmenustr = '';
			foreach($thirdlevelsubmenudata as $tsvrow){
				if($tsvrow['submenuid'] == $srow['id']){
				$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_5").is(":checked") == true && ';
			} }
			foreach($submenudata as $svrow){
				if($svrow['mainmenuid'] == $mrow['id']){
					$mainmenustr .= '$("#submenu_'.$svrow['id'].'_5").is(":checked") == true && ';
				} }
			  $newmainmenustr = substr($mainmenustr, 0, -3);
			$allchecked = substr($chek, 0, -3);
	      ?>
			if(<?php echo $allchecked; ?>){
				$("#submenu_<?php echo $srow['id']; ?>_5").prop('checked', true);
			}else{
				$("#submenu_<?php echo $srow['id']; ?>_5").prop('checked', false);
			}
			if(<?php echo $newmainmenustr; ?>){
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_5").prop('checked', true);
			}else{
	        	$("#mainmenu_<?php echo $mrow['id']; ?>_5").prop('checked', false);
	      	}
	    });
	    <?php } } ?>
	    <?php } } } ?>
  	});
</script>