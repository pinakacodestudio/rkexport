<script>

var roletype = '<?php if(isset($roletype)){ echo $roletype; } ?>';
</script>
<div class="page-content">
	  <div class="page-heading">            
        <h1><?php if(isset($memberroledata) && !isset($roletype)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>
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
		        	<div class="col-sm-12 col-md-8 col-lg-8 col-lg-offset-2 col-md-offset-2">
					<form class="form-horizontal" id="formmemberrole">
						<input type="hidden" name="memberroleid" value="<?php if(isset($memberroledata) && !isset($roletype)){ echo $memberroledata['id']; } ?>">
						<div class="form-group" id="memberrole_div">
							<label for="memberrole" class="col-sm-3 control-label"><?=Member_label?> Role <span class="mandatoryfield">*</span></label>
							<div class="col-sm-8">
								<input id="memberrole" type="text" name="memberrole" value="<?php if(!empty($memberroledata) && !isset($roletype)){ echo $memberroledata['role']; } ?>" class="form-control" onkeypress="return onlyAlphabets(event)">
							</div>
						</div>

						<?php
									foreach($mainmenudata as $mainrow){
										
										$mainmenucheckvisible = '';
										$mainmenucheckadd = '';
										$mainmenucheckedit = '';
										$mainmenucheckdelete = '';
										
										if(!isset($memberroledata))
										{
											if($mainrow['inorder'] == 0){
												$mainmenucheckvisible = 'checked';
												$mainmenucheckadd = 'checked';
												$mainmenucheckedit = 'checked';
												$mainmenucheckdelete = 'checked';
											}
										}
								?>
								<div class="form-group">
								  <label class="col-sm-3 control-label"><span style="font-weight:bold;"><?php echo $mainrow['name']; ?></span></label>
								  <div class="col-sm-9">
								  <?php
									if(isset($memberroledata)){
										$mainmenuvisible=explode(",",$mainrow['menuvisible']);
										foreach($mainmenuvisible as $mmv){
											if($mmv==$memberroledata['id']){
												$mainmenucheckvisible = 'checked';
											}
										}
										$mainmenuadd=explode(",",$mainrow['menuadd']);
										foreach($mainmenuadd as $mma){
											if($mma==$memberroledata['id']){
												$mainmenucheckadd = 'checked';
											}
										}
										$mainmenuedit=explode(",",$mainrow['menuedit']);
										foreach($mainmenuedit as $mme){
											if($mme==$memberroledata['id']){
												$mainmenucheckedit = 'checked';
											}
										}
										$mainmenudelete=explode(",",$mainrow['menudelete']);
										foreach($mainmenudelete as $mmd){
											if($mmd==$memberroledata['id']){
												$mainmenucheckdelete = 'checked';
											}
										}
									}
								  ?>
									
									<div class="checkbox col-sm-3">
			                          <input type="checkbox" name="mainmenu1[]" id="mainmenu_<?php echo $mainrow['id']; ?>_1" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckvisible; ?>>
			                          <label for="mainmenu_<?php echo $mainrow['id']; ?>_1"><strong>Visible</strong></label>
			                        </div>
			                        <div class="checkbox col-sm-3">
			                          <input type="checkbox" name="mainmenu2[]" id="mainmenu_<?php echo $mainrow['id']; ?>_2" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckadd; ?>>
			                          <label for="mainmenu_<?php echo $mainrow['id']; ?>_2"><strong>Add</strong></label>
			                        </div>
			                        <div class="checkbox col-sm-3">
			                          <input type="checkbox" name="mainmenu3[]" id="mainmenu_<?php echo $mainrow['id']; ?>_3" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckedit; ?>>
			                          <label for="mainmenu_<?php echo $mainrow['id']; ?>_3"><strong>Edit</strong></label>
			                        </div>
			                        <div class="checkbox col-sm-3">
			                          <input type="checkbox" name="mainmenu4[]" id="mainmenu_<?php echo $mainrow['id']; ?>_4" value="<?php echo $mainrow['id']; ?>" <?php echo $mainmenucheckdelete; ?>>
			                          <label for="mainmenu_<?php echo $mainrow['id']; ?>_4"><strong>Delete</strong></label>
			                        </div>
								  </div>
								</div>
								 <?php
								 	foreach($submenudata as $subrow){
										if($subrow['channelmainmenuid']==$mainrow['id']){
											$submenucheckvisible = '';
											$submenucheckadd = '';
											$submenucheckedit = '';
											$submenucheckdelete = '';
											
											if(!isset($memberroledata))
											{
												if($mainrow['inorder'] == 0){
													$submenucheckvisible = 'checked';
													$submenucheckadd = 'checked';
													$submenucheckedit = 'checked';
													$submenucheckdelete = 'checked';
												}
											}
											if(isset($memberroledata)){
												$submenuvisible=explode(",",$subrow['submenuvisible']);
												foreach($submenuvisible as $smv){
													if($smv==$memberroledata['id']){
														$submenucheckvisible = 'checked';
													}
												}
												$submenuadd=explode(",",$subrow['submenuadd']);
												foreach($submenuadd as $sma){
													if($sma==$memberroledata['id']){
														$submenucheckadd = 'checked';
													}
												}
												$submenuedit=explode(",",$subrow['submenuedit']);
												foreach($submenuedit as $sme){
													if($sme==$memberroledata['id']){
														$submenucheckedit = 'checked';
													}
												}
												$submenudelete=explode(",",$subrow['submenudelete']);
												foreach($submenudelete as $smd){
													if($smd==$memberroledata['id']){
														$submenucheckdelete = 'checked';
													}
												}
											}
								 ?>
								 <div class="form-group">
								  <label class="col-sm-3 control-label"><span <?=($subrow['url']=='')?'style="font-weight:bold;"':''?>><?php echo $subrow['name']; ?></span><?=($subrow['url']=='')?'<span><i style="position:absolute;" class="material-icons">keyboard_arrow_down</i></span>':''?></label>
								  <div class="col-sm-9" id="div<?php echo $mainrow['id']; ?>">
								  	<div class="checkbox col-sm-3">
			                          <input type="checkbox" name="submenu1[]" id="submenu_<?php echo $subrow['id']; ?>_1" value="<?php echo $subrow['id']; ?>" <?php echo $submenucheckvisible; ?>>
			                          <label for="submenu_<?php echo $subrow['id']; ?>_1">Visible</label>
			                        </div>
			                        <div class="checkbox col-sm-3">
			                          <input type="checkbox" name="submenu2[]" id="submenu_<?php echo $subrow['id']; ?>_2" value="<?php echo $subrow['id']; ?>" <?php echo $submenucheckadd; ?>>
			                          <label for="submenu_<?php echo $subrow['id']; ?>_2">Add</label>
			                        </div>
									<div class="checkbox col-sm-3">
			                          <input type="checkbox" name="submenu3[]" id="submenu_<?php echo $subrow['id']; ?>_3" value="<?php echo $subrow['id']; ?>" <?php echo $submenucheckedit; ?>>
			                          <label for="submenu_<?php echo $subrow['id']; ?>_3">Edit</label>
			                        </div>
			                        <div class="checkbox col-sm-3">
			                          <input type="checkbox" name="submenu4[]" id="submenu_<?php echo $subrow['id']; ?>_4" value="<?php echo $subrow['id']; ?>" <?php echo $submenucheckdelete; ?>>
			                          <label for="submenu_<?php echo $subrow['id']; ?>_4">Delete</label>
			                        </div>
								  </div>
								</div>
								<?php foreach ($thirdsubmenudata as $thirdsubmenu){
									if($thirdsubmenu['channelsubmenuid']==$subrow['id']){
										$thirdlevelsubmenucheckvisible = '';
										$thirdlevelsubmenucheckadd = '';
										$thirdlevelsubmenucheckedit = '';
										$thirdlevelsubmenucheckdelete = '';

										if(!isset($memberroledata))
											{
												if($mainrow['inorder'] == 0){
													$thirdlevelsubmenucheckvisible = 'checked';
													$thirdlevelsubmenucheckadd = 'checked';
													$thirdlevelsubmenucheckedit = 'checked';
													$thirdlevelsubmenucheckdelete = 'checked';
												}
											}
											if(isset($memberroledata)){
												$submenuvisible=explode(",",$thirdsubmenu['thirdlevelsubmenuvisible']);
												foreach($submenuvisible as $smv){
													if($smv==$memberroledata['id']){
														$thirdlevelsubmenucheckvisible = 'checked';
													}
												}
												$submenuadd=explode(",",$thirdsubmenu['thirdlevelsubmenuadd']);
												foreach($submenuadd as $sma){
													if($sma==$memberroledata['id']){
														$thirdlevelsubmenucheckadd = 'checked';
													}
												}
												$submenuedit=explode(",",$thirdsubmenu['thirdlevelsubmenuedit']);
												foreach($submenuedit as $sme){
													if($sme==$memberroledata['id']){
														$thirdlevelsubmenucheckedit = 'checked';
													}
												}
												$submenudelete=explode(",",$thirdsubmenu['thirdlevelsubmenudelete']);
												foreach($submenudelete as $smd){
													if($smd==$memberroledata['id']){
														$thirdlevelsubmenucheckdelete = 'checked';
													}
												}
											}

										?>
										<div class="form-group">
											<label class="col-sm-3 control-label"><span><?php echo $thirdsubmenu['name']; ?></span> <span><i style="position:absolute;" class="material-icons">keyboard_arrow_right</i></span></label>
											<div class="col-sm-9" id="div<?php echo $subrow['id']; ?>">
												<div class="checkbox col-sm-3">
													<input type="checkbox" name="thirdlevelsubmenu1[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_1" value="<?php echo $thirdsubmenu['id']; ?>"  <?php echo $thirdlevelsubmenucheckvisible; ?>>
													<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_1">Visible</label>
												</div>
												<div class="checkbox col-sm-3">
													<input type="checkbox" name="thirdlevelsubmenu2[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_2" value="<?php echo $thirdsubmenu['id']; ?>"  <?php echo $thirdlevelsubmenucheckadd; ?>>
													<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_2">Add</label>
												</div>
												<div class="checkbox col-sm-3">
													<input type="checkbox" name="thirdlevelsubmenu3[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_3" value="<?php echo $thirdsubmenu['id']; ?>"  <?php echo $thirdlevelsubmenucheckedit; ?>>
													<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_3">Edit</label>
												</div>
												<div class="checkbox col-sm-3">
													<input type="checkbox" name="thirdlevelsubmenu4[]" id="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_4" value="<?php echo $thirdsubmenu['id']; ?>"  <?php echo $thirdlevelsubmenucheckdelete; ?>>
													<label for="thirdlevelsubmenu_<?php echo $thirdsubmenu['id']; ?>_4">Delete</label>
												</div>
											</div>
										</div>	
								<?php	}
								} 
								?>
								 <?php } } ?>
									<hr>
								<?php
									} ?>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label">Activate</label>
							<div class="col-sm-8">
								<div class="col-sm-2 col-xs-6" style="padding-left: 0px;">
									<div class="radio">
										<input type="radio" name="status" id="yes" value="1" <?php if(isset($memberroledata) && !isset($roletype) && $memberroledata['status']==1){ echo 'checked'; }else{ echo 'checked'; }?>>
										<label for="yes">Yes</label>
									</div>
								</div>
								<div class="col-sm-2 col-xs-6">
									<div class="radio">
										<input type="radio" name="status" id="no" value="0" <?php if(isset($memberroledata) && !isset($roletype) && $memberroledata['status']==0){ echo 'checked'; }?>>
										<label for="no">No</label>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="focusedinput" class="col-sm-3 control-label"></label>
							<div class="col-sm-8">
								<?php if(!empty($memberroledata) && !isset($roletype)){ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php }else{ ?>
									<input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
									<input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised" onclick="resetdata()">
								<?php } ?>
								<a class="<?=cancellink_class;?>" href="<?=ADMIN_URL?>member-role" title=<?=cancellink_title?>><?=cancellink_text?></a>
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
	          if($srow['channelmainmenuid'] == $mrow['id']){
	    ?>
	    $("#mainmenu_<?php echo $mrow['id'] ?>_1").change(function () {
	      if(this.checked) {
			<?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_1:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_1").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_1:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_1").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_1").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['channelmainmenuid'] == $mrow['id']){
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
			  <?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_2:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_2").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_2:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_2").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_2").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['channelmainmenuid'] == $mrow['id']){
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
			<?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_3:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_3").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_3:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_3").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_3").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['channelmainmenuid'] == $mrow['id']){
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
			<?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_4:not(:disabled)").prop('checked', true);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_4").prop('checked', true);
	      }
	      else
	      {
			<?php foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){ ?>
				  $("#thirdlevelsubmenu_<?php echo $trow['id']; ?>_4:not(:disabled)").prop('checked', false);
			  <?php } 
			} ?>
	        $("#submenu_<?php echo $srow['id']; ?>_4").prop('checked', false);
	      }
	    });
	    $("#submenu_<?php echo $srow['id'] ?>_4").click(function () {
	      <?php
	      $str = '';
	      foreach($submenudata as $svrow){
	        if($svrow['channelmainmenuid'] == $mrow['id']){
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

		<?php
	        foreach($thirdsubmenudata as $trow){
	          if($trow['channelsubmenuid'] == $srow['id']){
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
				foreach($thirdsubmenudata as $tsvrow){
					if($tsvrow['channelsubmenuid'] == $srow['id']){
					$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_1").is(":checked") == true && ';
				} }
				foreach($submenudata as $svrow){
					if($svrow['channelmainmenuid'] == $mrow['id']){
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
				foreach($thirdsubmenudata as $tsvrow){
					if($tsvrow['channelsubmenuid'] == $srow['id']){
					$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_2").is(":checked") == true && ';
				} }
				foreach($submenudata as $svrow){
					if($svrow['channelmainmenuid'] == $mrow['id']){
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
				foreach($thirdsubmenudata as $tsvrow){
					if($tsvrow['channelsubmenuid'] == $srow['id']){
					$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_3").is(":checked") == true && ';
				} }
				foreach($submenudata as $svrow){
					if($svrow['channelmainmenuid'] == $mrow['id']){
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
				foreach($thirdsubmenudata as $tsvrow){
					if($tsvrow['channelsubmenuid'] == $srow['id']){
					$chek .= '$("#thirdlevelsubmenu_'.$tsvrow['id'].'_4").is(":checked") == true && ';
				} }
				foreach($submenudata as $svrow){
					if($svrow['channelmainmenuid'] == $mrow['id']){
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
		<?php } } ?>
	    <?php } } } ?>
  	});
</script>