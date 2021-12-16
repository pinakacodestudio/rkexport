$("[data-provide='city']").each(function () {
     var $element = $(this);
    
    $element.select2({
      allowClear: true,
      minimumInputLength: 3,    
      placeholder: $element.attr("placeholder"),            
     
      ajax: {
          url: $element.data("url"),
          dataType: 'json',
          type: "POST",
          quietMillis: 50,
          data: function (term) {
              return {
                  term: term,
              };
          },
          results: function (data) {            
              return {
                  results: $.map(data, function (item) {
                      return {
                          text: item.text,                        
                          id: item.id
                      }
                  })
              };
          }
      },
      initSelection: function (element, callback) {
          var id = $(element).val(); 

          if (id !== "" && id!=='0') {
              $.ajax($element.data("url"), {
                  data: {
                      ids: id,
                  },
                  type: "POST",
                  dataType: "json",
              }).done(function (data) {
                  callback(data);    
              });
          }else{
            $("#cityid").select2("data", { id: 0, text: "Select City" });
          }
      }
      });

});
function initialize(){
  var autocomplete; 
  var place;
  var input = document.getElementById('address');
  autocomplete  = new google.maps.places.Autocomplete(input);
    
  if(input.value!=''){
    
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
      fill();
    });
  }else{
    $("#latitude").val('');
    $("#longitude").val('');
    $("#country").val('');
    $("#state").val('');
    $("#city").val('');
    $("#address2").val('');
  }
}
google.maps.event.addDomListener(window, 'load', initialize);

function fill(){
  
  var location = $("#address").val(); 
      
  if(location == ""){
     return false;
  }
        
  $.ajax({
    url: SITE_URL+'region/getlatlong',
    type : 'post',
    data : {location: location},
    //async: false,
    beforeSend: function(){
      $('.mask').show();
      $('#loader').show();
    },
    success: function(data) { 

      //initialize(data);

      var latlng = data;
      var coords = latlng.split(",");
      var lat = coords[0];
      var lng = coords[1];
      $("#latitude").val(lat);
      $("#longitude").val(lng);
    },
    complete: function(){
      $('.mask').hide();
      $('#loader').hide();
    },

  });
}
function resetdata(){

  $("#name_div").removeClass("has-error is-focused");
  $("#contactperson_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#mobileno_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#cityid_div").removeClass("has-error is-focused");
  $("#latitude_div").removeClass("has-error is-focused");
  $("#longitude_div").removeClass("has-error is-focused");
  $('#s2id_cityid > a').css({"background-color":"#fcfcfc","border":"1px solid #e3e3e3"});

  if(ACTION==1){
    $("#cityid").select2("val", [$('#cityid').val()]);
  }else{
    $('#name').val('');
    $('#contactperson').val('');
    $('#email').val('');
    $('#mobileno').val('');
    $('#address').val('');
    $('#latitude').val('');
    $('#longitude').val('');
    $("#cityid").select2("val", "0");
    $('#yes').prop("checked", true);
    $('#name').focus();
  }
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(){
  
  var name = $("#name").val().trim();
  var contactperson = $("#contactperson").val().trim();
  var email = $("#email").val().trim();
  var mobileno = $("#mobileno").val().trim();
  var address = $("#address").val().trim();
  var cityid = $("#cityid").val();
  var latitude = $("#latitude").val().trim();
  var longitude = $("#longitude").val().trim();
  
  var isvalidname = isvalidaddress = isvalidcityid = isvalidlatitude = isvalidlongitude = 0;
  var isvalidcontactperson = isvalidemail = isvalidmobileno =1;
  PNotify.removeAll();
  if(name == ''){
    $("#name_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter outlate name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidname = 0;
  }else {
    if(name.length<2){
      $("#name_div").addClass("has-error is-focused");
      new PNotify({title: 'Outlet name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidname = 0;
    }else{
      isvalidname = 1;
    }
  }
  /*if(contactperson == ''){
    $("#contactperson_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter contact person name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcontactperson = 0;
  }else { 
    if(contactperson.length<2){
      $("#contactperson_div").addClass("has-error is-focused");
      new PNotify({title: 'Contact person name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcontactperson = 0;
    }else{
      isvalidcontactperson = 1;
    }
  }
  if(email == ''){
    $("#email_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidemail = 0;
  }else { 
    if(!ValidateEmail(email)){
      $("#email_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter valid email address !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidemail = 0;
    }else{
      isvalidemail = 1;
    }
  }
  if(mobileno == ''){
    $("#mobileno_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter mobileno !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidmobileno = 0;
  }else { 
    if(mobileno.length<4){
      $("#mobileno_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter minimum 4 digit mobile number !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno = 0;
    }else{
      isvalidmobileno = 1;
    }
  }*/
  if(address == ''){
    $("#address_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter address !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidaddress = 0;
  }else {
    if(address.length<3){
      $("#address_div").addClass("has-error is-focused");
      new PNotify({title: 'Address have must be 3 characters!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidaddress = 0;
    }else{
      isvalidaddress = 1;
    }
  }
  if(cityid.trim() == 0 || cityid.split(',').length < 1){
    $("#cityid_div").addClass("has-error is-focused");
    $('#s2id_cityid > a').css({"background-color":"#FFECED","border":"1px solid #FFB9BD"});
    new PNotify({title: 'Please select cityname !',styling: 'fontawesome',delay: '3000',type: 'error'});  
    isvalidcityid = 0;
  }else { 
    isvalidcityid = 1;
    $('#s2id_cityid > a').css({"background-color":"#fcfcfc","border":"1px solid #e3e3e3"});
  }
  if(latitude == ''){
    $("#latitude_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter latitude !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlatitude = 0;
  }else { 
    if(latitude.length<6){
      $("#latitude_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter minimum 6 digit latitude !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidlatitude = 0;
    }else{
      isvalidlatitude = 1;
    }
  }
  if(longitude == ''){
    $("#longitude_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter longitude !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidlongitude = 0;
  }else { 
    if(longitude.length<6){
      $("#longitude_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter minimum 6 digit longitude !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidlongitude = 0;
    }else{
      isvalidlongitude = 1;
    }
  }

  if(isvalidname == 1 && isvalidcontactperson == 1 && isvalidemail == 1 && isvalidmobileno == 1 && isvalidaddress == 1 && isvalidcityid == 1 && isvalidlatitude == 1 && isvalidlongitude == 1){

    var formData = new FormData($('#outletdetailsform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"Outletdetails/addoutletdetails";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          if(response==1){
            new PNotify({title: "Outlet details successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            resetdata();
          }else if(response==2){
            new PNotify({title: 'Outlet email already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==3){
            new PNotify({title: 'Outlet mobileno already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#mobileno_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Outlet details not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
      });
    }else{
      var uurl = SITE_URL+"Outletdetails/updateoutletdetails";
      
      $.ajax({
        url: uurl,
        type: 'POST',
        data: formData,
        //async: false,
        beforeSend: function(){
          $('.mask').show();
          $('#loader').show();
        },
        success: function(response){
          if(response==1){
              new PNotify({title: "Outlet details successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"Outletdetails"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Outlet email already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==3){
            new PNotify({title: 'Outlet mobileno already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#mobileno_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Outlet details not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
          }
        },
        error: function(xhr) {
        //alert(xhr.responseText);
        },
        complete: function(){
          $('.mask').hide();
          $('#loader').hide();
        },
        cache: false,
        contentType: false,
        processData: false
      });
    }
  }
}

