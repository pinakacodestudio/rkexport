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
        <?php $this->load->view(ADMINFOLDER.'includes/menu_header');?>
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
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group" id="employeeid_div">
                                    <div class="col-sm-12 pr-sm">
                                        <label for="employeeid" class="control-label">Sales Person</label>
                                        <select id="employeeid" name="employeeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                            <option value="0">Select Sales Person</option>
                                            <?php foreach ($employeedata as $employee) { ?>
                                                <option value="<?=$employee['id']?>"><?=ucwords($employee['name'])?></option>
                                            <?php } ?> 
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php if (in_array("vehicle-dropdown",$this->viewData['submenuvisibility']['assignadditionalrights'])){ ?>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label for="vehicleid" class="control-label">Vehicle</label>
                                        <select id="vehicleid" name="vehicleid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                            <option value="0">All Vehicle</option>
                                            <?php foreach($vehicledata as $vehicle){ ?>
                                                <option value="<?=$vehicle['id']?>"><?=$vehicle['vehiclename']." (".$vehicle['vehicleno'].")"?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div> 
                            <?php } ?>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label for="routeid" class="control-label">Route</label>
                                        <select id="routeid" name="routeid" class="selectpicker form-control" data-select-on-tab="true" data-size="5" data-live-search="true">
                                            <option value="0">All Route</option>
                                            <?php /*foreach ($routedata as $route) { ?>
                                                <option value="<?=$route['id']?>"><?=$route['route']?></option>
                                            <?php }*/ ?> 
                                        </select>
                                    </div>
                                </div>
                            </div>    
                            <div class="col-md-2">
                                <div class="form-group">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <label for="date" class="control-label">Date</label>
                                        <input id="date" type="text" name="date" value="<?php echo $this->general_model->displaydate($this->general_model->getCurrentDate()); ?>" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="col-md-2">
                                <div class="form-group pt-xl">
                                    <div class="col-sm-12 pr-sm pl-sm">
                                        <a class="<?=applyfilterbtn_class;?>" href="javascript:void(0)" onclick="track_sales_person()" title=<?=applyfilterbtn_title?>><?=applyfilterbtn_text;?></a>
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

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=<?php echo DIRECTION_MAP_KEY;?>&sensor=false"></script>
<script>  
    
//You can calculate directions (using a variety of methods of transportation) by using the DirectionsService object.
var directionsService = new google.maps.DirectionsService();

//Define a variable with all map points.
var _mapPoints = new Array();

//Define a DirectionsRenderer variable.
var _directionsRenderer = '';

function initMap(time_array=[],icon_array=[],flightPlanCoordinates=[],markerCoordinates=[],lat_long_center_point=[],responsetype=0,info_window=[]) {

    // console.log(lat_long_center_point)
    // var iconBase = 'http://maps.google.com/mapfiles/kml/paddle/';
    
    
    var myOptions = {
        zoom: 6,
        center: new google.maps.LatLng(21.7679, 78.8718),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    //Define the map.
    map = new google.maps.Map(document.getElementById("map"), myOptions);
    
    // _directionsRenderer = new google.maps.DirectionsRenderer();
    _directionsRenderer = new google.maps.DirectionsRenderer({
      suppressMarkers : true
    });
    _directionsRenderer.setMap(map);
    
    // _directionsRenderer.setOptions({
    //     draggable: true
    // });
    _mapPoints = [];

    for (i = 0; i < markerCoordinates.length; i++) {  
        
        var _currentPoints = new google.maps.LatLng(markerCoordinates[i][0],markerCoordinates[i][1]);
        _mapPoints.push(_currentPoints);
    }  
    getRoutePointsAndWaypoints();

    /* var flightPath = new google.maps.Polyline({
        path: flightPlanCoordinates,
        geodesic: true,
        strokeColor: '#f9ad06',
        strokeOpacity: 1.0,
        strokeWeight: 4,
        map: map,
    });

    flightPath.setMap(map); */

    
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
function getRoutePointsAndWaypoints() {
    //Define a variable for waypoints.
    // console.log(_mapPoints)
    var _waypoints = new Array();
    // if (_mapPoints.length > 2) //Waypoints will be come.
    // {
        for (var j = 1; j < _mapPoints.length - 1; j++) {
            var address = _mapPoints[j];
            if (address !== "") {
                _waypoints.push({
                    location: address,
                    // stopover: true  //stopover is used to show marker on map for waypoints
                });
            }
        }
      
        //Call a drawRoute() function
        drawRoute(_mapPoints[0], _mapPoints[_mapPoints.length - 1], _waypoints);
    // } else if (_mapPoints.length > 1) {
    //     //Call a drawRoute() function only for start and end locations
    //     drawRoute(_mapPoints[_mapPoints.length - 2], _mapPoints[_mapPoints.length - 1], _waypoints);
    // } else {
    //     //Call a drawRoute() function only for one point as start and end locations.
    //     drawRoute(_mapPoints[_mapPoints.length - 1], _mapPoints[_mapPoints.length - 1], _waypoints);
    // }
}

//drawRoute() will help actual draw the route on map.
function drawRoute(originAddress, destinationAddress, _waypoints) {
    //Define a request variable for route .
    var _request = '';
    
    //This is for more then two locatins
    

    // if (_waypoints.length > 0) {
        _request = {
            origin: originAddress,
            destination: destinationAddress,
            waypoints: _waypoints, //an array of waypoints
            optimizeWaypoints: true, //set to true if you want google to determine the shortest route or false to use the order specified.
            travelMode: google.maps.DirectionsTravelMode.DRIVING
        };
    // } else {
    //     //This is for one or two locations. Here noway point is used.
    //     _request = {
    //         origin: originAddress,
    //         destination: destinationAddress,
    //         travelMode: google.maps.DirectionsTravelMode.DRIVING
    //     };
    // }

    
    //This will take the request and draw the route and return response and status as output
    directionsService.route(_request, function (_response, _status) {
      if (_status == google.maps.DirectionsStatus.OK) {
        
        _directionsRenderer.setDirections(_response);
        var markerCounter = 1;
        
            // add custom markers
            var route = _response.routes[0];
              // start marker
            
            
            addMarker(route.legs[0].start_location, markerCounter++,"<?=DEFAULT_IMG?>small-bus.png");
              // the rest
              
            for (var i = 0; i < (route.legs.length-1); i++) {
              addMarker(route.legs[i].end_location, markerCounter++,"<?=DEFAULT_IMG?>way-point.png");
            }

            addMarker(route.legs[route.legs.length-1].end_location, markerCounter++,"<?=DEFAULT_IMG?>destination-flag.png");
        }
    });
    function addMarker(position, i,myicons) {
        // marker.setMap(null);

        return new google.maps.Marker({
            // @see http://stackoverflow.com/questions/2436484/how-can-i-create-numbered-map-markers-in-google-maps-v3 for numbered icons
            icon: myicons,
            position: position,
            map: map
        })
    }
}
</script>
<!-- <script async defer
src="https://maps.googleapis.com/maps/api/js?key=<?php echo MAP_KEY;?>&sensor=false">
</script> -->