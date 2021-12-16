<div class="row">
    <div class="col-md-4">
        <div class="form-group" id="datefilter_div">
            <label class="control-label" for="datefilter">Select Start - End Date</label>
            <input type="text" name="datefilter" id="datefilter" class="form-control" value="<?=(!empty($taskdata[0]['startdatetime']))?$this->general_model->displaydatetime($taskdata[0]['startdatetime'],'d/m/Y h:i A'):date("d/m/Y")." 12:00 AM"?> - <?=date("d/m/Y")?> 11:59 PM" readonly>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group" id="taskid_div">
            <label class="control-label" for="taskid">Select Task </label>
            <select id="taskid" name="taskid" class="selectpicker form-control" data-live-search="true" data-size="5">
                <option value="0">Select Task</option>
                <?php foreach($taskdata as $index => $row){ ?>
                    <option value="<?=$row['id']?>" <?php if(count($taskdata)>0 && $index==0){ echo 'selected';} ?> ><?=(!empty($row['taskname']))?$row['taskname']:'Cycle - '.$row['id']?></option>
                <?php } ?>
            </select>
            <div class="col-md-12 float-left mt-1 p-n">
                <div class="col-md-10 float-left p-n">
                    <input type="text" name="taskname" id="taskname" class="form-control" placeholder="Rename Task Name">
                </div>
                <div class="col-md-2 float-left pt-sm" style="line-height: 2;">
                    <a href="javascript:void(0)" onclick="renametaskname()"><i class="fa-lg fa fa-pencil"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
        <label class="control-label" for="spent_time"> Spent Time</label>
        <select id="spent_time" name="spent_time" class="selectpicker form-control" data-live-search="true" data-size="4" tabindex="4">
            <option value="0">Select Time</option>
            <option value="5">More than 5 Minute</option>
            <option value="10">More than 10 Minute</option>
            <option value="30">More than 30 Minute</option>
            <option value="60">More than 60 Minute</option>
            </select>
        </div>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary btn-sm btn-raised" type="button" style="margin-top: 40px;" id="view_followup_btn" onclick="view_followup()"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh Map</button>
    </div>
</div>
<div class="row">
    <div id="map-error-message" class="text-danger"></div>
</div>
<br/>
<div class="row">
    <div class="col-md-12">
        <div id="map"></div>
        <?php if(isset($trackroutedata) && $trackroutedata) { ?>
            <?php foreach ($trackroutedata as $td) { ?>
            <?php }
        } ?>
    </div>
</div>

<script>  
function initMap(time_array=[],icon_array=[],flightPlanCoordinates=[],markerCoordinates=[],lat_long_center_point=[],responsetype=0) {

    var iconBase = 'http://maps.google.com/mapfiles/kml/paddle/';
    if(responsetype==0){
        time_array=<?php echo json_encode($mapdata['time_array']); ?>;
    }       

    if(responsetype==0){
        icon_array=<?php echo json_encode($mapdata['icon_array']); ?>;
    }

    if(responsetype==0){
        lat_long_center_point=<?php echo json_encode($mapdata['lat_long_center_point']); ?>;
    }

    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 17,
        center: lat_long_center_point,
        mapTypeId: google.maps.MapTypeId.TERRAIN
    });

    if(responsetype==0){
        flightPlanCoordinates = <?php echo json_encode($mapdata['flightPlanlat_long_arr']); ?>;
    }
    if(responsetype==0){
        markerCoordinates = <?php echo json_encode($mapdata['markerlat_long_arr']); ?>;
    }

    var marker, i;

    for (i = 0; i < markerCoordinates.length; i++) {  

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(markerCoordinates[i][i-i], markerCoordinates[i][1]),
        map: map,
        title: time_array[i],
        icon: icon_array[i]
        });
    }  

    var flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: '#FF0000',
        strokeOpacity: 1.0,
        strokeWeight: 2
    });

    flightPath.setMap(map);
}
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=<?php echo MAP_KEY;?>&callback=initMap&sensor=false">
</script>