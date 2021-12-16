<style>
  #map {
    height: 400px;
    width: 100%;  
    margin-left: 0px;
  }
  #map img {
    height: 64px;
    width: 100%;  
  }
</style>
<div class="page-content">
    <div class="page-heading">   
        <h1>View <?=$this->session->userdata(base_url().'submenuname')?></h1>    
        <small>
            <ol class="breadcrumb">                        
            <li><a href="<?php echo base_url(); ?><?php echo ADMINFOLDER; ?>dashboard">Dashboard</a></li>
            <li><a href="javascript:void(0)"><?=$this->session->userdata(base_url().'mainmenuname')?></a></li>
            <li class="active"><?=$this->session->userdata(base_url().'submenuname')?></li>
            </ol>
        </small>                
    </div>

    <div class="container-fluid">
                                    
      <div data-widget-group="group1">
        <div class="row">
          <div class="col-md-12">
            <div class="panel panel-default border-panel">
              <div class="panel-heading">
              </div>
              <div class="panel-body no-padding">
                <div class="col-md-12">
                    <form action="#" class="form-horizontal">
                        <input type="hidden" id="salespersonrouteid" name="salespersonrouteid" value="<?=(!empty($salespersonroutedata)?$salespersonroutedata['id']:"")?>">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm">
                                        <label for="memberid" class="control-label">Member</label>
                                        <select id="memberid" name="memberid[]" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true" data-actions-box="true" title="All Member" multiple>
                                            <?php foreach ($memberdata as $member) { ?>
                                                <option value="<?=$member['id']?>"><?=ucwords($member['name'])?></option>
                                            <?php } ?> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label for="date" class="control-label">Date</label>
                                        <input id="date" type="text" name="date" value="<?php if(!empty($salespersonroutedata)){ echo $this->general_model->displaydate($salespersonroutedata['startdatetime']); } ?>" class="form-control" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label class="control-label">Route Name</label>
                                        <input id="routeid" name="routeid" class="form-control" value="<?=(!empty($salespersonroutedata)?$salespersonroutedata['routename']:"")?>" disabled>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label class="control-label">Start Time</label>
                                        <input name="starttime" class="form-control" value="<?php if(!empty($salespersonroutedata)){ echo $this->general_model->displaydatetime($salespersonroutedata['startdatetime'], "h:i A"); } ?>" disabled>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label class="control-label">Total Time</label>
                                        <input name="totaltime" class="form-control" value="<?php if(!empty($salespersonroutedata) && $salespersonroutedata['enddatetime']!="0000-00-00 00:00:00"){ echo $this->general_model->time_difference($salespersonroutedata['startdatetime'], $salespersonroutedata['enddatetime']); } ?>" disabled>
                                    </div>
                                </div>
                            </div> 
                                
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm">
                                        <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="view_sales_person_history()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </form>
                </div>
                <!-- <div class="col-md-12"><hr></div> -->
                <div class="col-md-12 mt-sm">
                    <div id="map-error-message" class="text-danger"></div>
                </div>
                <div class="col-md-12">
                    <div id="map"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div> <!-- .container-fluid -->
</div> <!-- #page-content -->

<script>  
function initMap(time_array=[],icon_array=[],flightPlanCoordinates=[],markerCoordinates=[],lat_long_center_point=[],responsetype=0,info_window=[]) {

    var iconBase = 'http://maps.google.com/mapfiles/kml/paddle/';
    
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 17,
        center: lat_long_center_point,
        mapTypeId: google.maps.MapTypeId.TERRAIN
    });

    
    var marker, i;

    for (i = 0; i < markerCoordinates.length; i++) {  

        var tarr = icon_array[i].split('/');
        var file = tarr[tarr.length-1]; 
        if(file == 'desination.png'){
          var icon = {
              url: icon_array[i], // url
          };
        }else{

          var icon = {
              url: icon_array[i], // url
              scaledSize: new google.maps.Size(30, 30), // size
              origin: new google.maps.Point(0,0), // origin
              anchor: new google.maps.Point(15, 27) // anchor
          };
        }

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(markerCoordinates[i][i-i], markerCoordinates[i][1]),
            map: map,
            title: time_array[i],
            icon: icon,
        });
        
        if(info_window[i]!=""){
          const infowindow = new google.maps.InfoWindow({
            content: info_window[i],
          });
          
          marker.addListener("click", function(e) {
            infowindow.open(map, marker);
            infowindow.setPosition(e.latLng);
          });
        }
    }  

    var flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: '#f9ad06',
        strokeOpacity: 1.0,
        strokeWeight: 5,
        map: map,
    });

    flightPath.setMap(map);

    
    /* const lineSymbol = {
        path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
      };
    var flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        icons: [
          {
            icon: lineSymbol,
            offset: "100%",
          },
        ],
        map: map
    }); */
}
</script>
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=<?php echo MAP_KEY;?>&callback=initMap&sensor=false">
</script>