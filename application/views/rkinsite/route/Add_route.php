<?php 
$CHANNEL_DATA = '';
if(!empty($channeldata)){
    foreach($channeldata as $channel){
        $CHANNEL_DATA .= '<option value="'.$channel['id'].'">'.$channel['name'].'</option>';
    } 
}
?>
<script>
    var CHANNEL_DATA = '<?=$CHANNEL_DATA?>';
    var DEFAULTCOUNTRYID = '<?=DEFAULT_COUNTRY_ID?>';
    var provinceid = '<?php if(isset($routedata)){ echo $routedata['provinceid']; }else{ echo '0'; } ?>';
    var cityid = '<?php if(isset($routedata)){ echo $routedata['cityid']; }else{ echo '0'; } ?>';
</script>
<div class="page-content">
    <div class="page-heading">            
        <h1><?php if(isset($routedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></h1>                    
        <small>
            <ol class="breadcrumb">                        
              <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
              <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
              <li><a href="<?php echo ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>"><?=$this->session->userdata(base_url().'submenuname')?></a></li>
              <li class="active"><?php if(isset($routedata)){ echo 'Edit'; }else{ echo 'Add'; } ?> <?=$this->session->userdata(base_url().'submenuname')?></li>
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
                        <form class="form-horizontal" id="routeform" name="routeform" method="post">
                            <input type="hidden" name="id" value="<?php if(isset($routedata)){ echo $routedata['id']; } ?>">
                            <div class="col-md-12 p-n">
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group" id="route_div">
                                        <label class="col-md-3 pr-n pl-n control-label" for="route">Route Name<span class="mandatoryfield">*</span></label>
                                        <div class="col-md-9">
                                            <input type="text" id="route" value="<?php if(!empty($routedata)){ echo $routedata['route']; } ?>" name="route" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group" id="province_div">
                                        <label class="col-md-4 control-label pr-n pl-n" for="provinceid">Select Province<span class="mandatoryfield">*</span></label>
                                        <div class="col-md-8">
                                            <select id="provinceid" name="provinceid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select Province</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group" id="city_div">
                                        <label class="col-md-4 control-label pr-n pl-n" for="cityid">Select City<span class="mandatoryfield">*</span></label>
                                        <div class="col-md-8">
                                            <select id="cityid" name="cityid" class="selectpicker form-control" data-live-search="true" data-size="8">
                                                <option value="0">Select City</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 p-n">
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group" id="totaltime_div">
                                        <label class="col-md-3 control-label pl-n" for="totaltime">Total Time</label>
                                        <div class="col-md-9">
                                            <input type="text" id="totaltime" value="<?php if(!empty($routedata) && $routedata['totaltime']!="00:00:00"){ echo $routedata['totaltime']; } ?>" name="totaltime" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6 col-xs-12">
                                    <div class="form-group" id="totalkm_div">
                                        <label class="col-md-4 control-label" for="totalkm">Total KM</label>
                                        <div class="col-md-8">
                                            <input type="text" id="totalkm" value="<?php if(!empty($routedata) && $routedata['totalkm']>0){ echo $routedata['totalkm']; } ?>" name="totalkm" class="form-control" onkeypress="return decimal_number_validation(event,this.value)">
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-12 p-n"><hr></div>
                            <div class="raw">   
                                <div class="col-md-12 p-n">
                                    <div class="col-sm-3 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                            <label class="control-label">Select Channel</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label class="control-label">Select <?=Member_label?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-1 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label class="control-label">Priority</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 pl-sm pr-sm">
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <label class="control-label">Active</label>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                                <?php if(!empty($routedata) && !empty($routememberdata)) { ?>
                                    <input type="hidden" name="removeroutememberid" id="removeroutememberid">
                                    <?php for ($i=0; $i < count($routememberdata); $i++) { ?>
                                        <div class="col-md-12 p-n countmembers" id="countmembers<?=($i+1)?>">
                                            <input type="hidden" name="routememberid[]" value="<?=$routememberdata[$i]['id']?>" id="routememberid<?=($i+1)?>">
                                            <input type="hidden" name="uniquemember[]" id="uniquemember<?=($i+1)?>" value="<?=($routememberdata[$i]['channelid']."_".$routememberdata[$i]['memberid'])?>">
                                            <div class="col-sm-3 pl-sm pr-sm">
                                                <div class="form-group" id="channel<?=($i+1)?>_div">
                                                    <div class="col-sm-12">
                                                        <select id="channelid<?=($i+1)?>" name="channelid[]" class="selectpicker form-control channelid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="<?=($i+1)?>">
                                                            <option value="0">Select Channel</option>
                                                            <?=$CHANNEL_DATA?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 pl-sm pr-sm">
                                                <div class="form-group" id="member<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <select id="memberid<?=($i+1)?>" name="memberid[]" class="selectpicker form-control memberid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="<?=($i+1)?>">
                                                            <option value="0">Select <?=Member_label?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1 pl-sm pr-sm">
                                                <div class="form-group" id="priority<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <input type="text" id="priority<?=($i+1)?>" name="priority[]" class="form-control priority" div-id="<?=($i+1)?>" value="<?=$routememberdata[$i]['priority']?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1 pl-sm pr-sm">
                                                <div class="form-group" id="active<?=($i+1)?>_div">
                                                    <div class="col-md-12">
                                                        <div class="yesno mt-xs">
                                                            <input type="checkbox" id="active<?=($i+1)?>" name="active<?=($i+1)?>" value="<?php if($routememberdata[$i]['active']==1){ echo '1'; }else{ echo '0'; }?>" <?php if($routememberdata[$i]['active']==1){ echo 'checked'; }?>>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 form-group m-n p-sm pt-md">	
                                                <?php if($i==0){?>
                                                    <?php if(count($routememberdata)>1){ ?>
                                                        <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeMemberRaw(1)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                    <?php }else { ?>
                                                        <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewMemberRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                                    <?php } ?>

                                                <? }else if($i!=0) { ?>
                                                    <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeMemberRaw(<?=($i+1)?>)" style="padding: 5px 10px;"><i class="fa fa-minus"></i></button>
                                                <? } ?>
                                                <button type="button" class="btn btn-default btn-raised btn-sm remove_btn" onclick="removeMemberRaw(<?=($i+1)?>)"  style="padding: 5px 10px;display:none;"><i class="fa fa-minus"></i></button>
                                            
                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewMemberRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button> 
                                            </div>
                                            <script type="text/javascript">
                                                $(document).ready(function() {
                                                    $("#channelid<?=($i+1)?>").val(<?=$routememberdata[$i]['channelid']?>).selectpicker('refresh');
                                                    getmember(<?=($i+1)?>);
                                                    $("#memberid<?=($i+1)?>").val(<?=$routememberdata[$i]['memberid']?>).selectpicker('refresh');
                                                });
                                            </script>
                                        </div>
                                    <?php } ?>
                                <?php }else{ ?>
                                        <div class="col-md-12 p-n countmembers" id="countmembers1">
                                            <input type="hidden" name="uniquemember[]" id="uniquemember1" value="0_0">
                                            <div class="col-sm-3 pl-sm pr-sm">
                                                <div class="form-group" id="channel1_div">
                                                    <div class="col-sm-12">
                                                        <select id="channelid1" name="channelid[]" class="selectpicker form-control channelid" data-live-search="true" data-select-on-tab="true" data-size="8" div-id="1">
                                                            <option value="0">Select Channel</option>
                                                            <?=$CHANNEL_DATA?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4 pl-sm pr-sm">
                                                <div class="form-group" id="member1_div">
                                                    <div class="col-md-12">
                                                        <select id="memberid1" name="memberid[]" class="selectpicker form-control memberid" data-live-search="true" data-select-on-tab="true" data-size="5" div-id="1">
                                                            <option value="0">Select <?=Member_label?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1 pl-sm pr-sm">
                                                <div class="form-group" id="priority1_div">
                                                    <div class="col-md-12">
                                                        <input type="text" id="priority1" name="priority[]" value="1" class="form-control priority" div-id="1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-1 pl-sm pr-sm">
                                                <div class="form-group" id="active1_div">
                                                    <div class="col-md-12">
                                                        <div class="yesno mt-xs">
                                                            <input type="checkbox" id="active1" name="active1" value="1" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 form-group m-n p-sm pt-md">	
                                                <button type="button" class="btn btn-default btn-raised remove_btn" onclick="removeMemberRaw(1)" style="padding: 5px 10px;display: none;"><i class="fa fa-minus"></i></button>
                                                <button type="button" class="btn btn-default btn-raised add_btn" onclick="addNewMemberRaw()" style="padding: 5px 10px;"><i class="fa fa-plus"></i></button>
                                            </div>
                                        </div>
                                <?php } ?> 
                            </div>     
                            <div class="col-md-12 p-n"><hr></div>          
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <div class="col-sm-12">
                                    <?php if(!empty($routedata)){ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="UPDATE" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                    <?php }else{ ?>
                                        <input type="button" id="submit" onclick="checkvalidation()" name="submit" value="ADD" class="btn btn-primary btn-raised">
                                        <input type="reset" name="reset" value="RESET" class="btn btn-info btn-raised text-white" onclick="resetdata()">
                                    <?php } ?>
                                    <a class="<?=cancellink_class;?>" href="<?=ADMIN_URL.$this->session->userdata(base_url().'submenuurl')?>" title=<?=cancellink_title?>><?=cancellink_text?></a>
                                    </div>
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