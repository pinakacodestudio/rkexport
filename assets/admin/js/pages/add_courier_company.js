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

 $(document).ready(function() {
   if(ACTION==1 && (CHANNELID!=0 || CHANNELID!="")){
     getmembers();
    }
    $("#channelid").change(function(){
      getmembers();
    });
});
function getmembers(){
    
  var channelid = $("#channelid").val();

  $('#memberid')
      .find('option')
      .remove()
      .end()
      .append('<option value="0">Select '+Member_label+'</option>')
      .val('whatever')
      ;
  if(channelid!=""){
      var uurl = SITE_URL+"member/getmembers";
      $.ajax({
          url: uurl,
          type: 'POST',
          data: {channelid:channelid},
          dataType: 'json',
          async: false,
          success: function(response){
      
              for(var i = 0; i < response.length; i++) {
          
                  $('#memberid').append($('<option>', { 
                  value: response[i]['id'],
                  text : ucwords(response[i]['name'])
                  }));
          
              }
              if(MEMBERID!=0){
                  $('#memberid').val(MEMBERID);
              }
          },
          error: function(xhr) {
          //alert(xhr.responseText);
          },
      });
  }
  $('#memberid').selectpicker('refresh');
}
function resetdata(){

  $("#companyname_div").removeClass("has-error is-focused");
  $("#contactperson_div").removeClass("has-error is-focused");
  $("#email_div").removeClass("has-error is-focused");
  $("#mobileno_div").removeClass("has-error is-focused");
  $("#address_div").removeClass("has-error is-focused");
  $("#cityid_div").removeClass("has-error is-focused");
  $("#trackurl_div").removeClass("has-error is-focused");
  $('#s2id_cityid > a').css({"background-color":"#FFF","border":"#D2D2D2"});
  ("#member_div").removeClass("has-error is-focused");

  if(ACTION==1){
    $("#cityid").select2("val", [$('#cityid').val()]);
  }else{
    $('#companyname').val('');
    $('#channelid').val('');
    $('#memberid').val('0');
    $('#contactperson').val('');
    $('#email').val('');
    $('#mobileno').val('');
    $('#address').val('');
    $("#cityid").select2("val", "0");
    $('#trackurl').val('');
    $('#yes').prop("checked", true);
    $('#companyname').focus();

    $('.selectpicker').selectpicker('refresh');
  }
  $('html, body').animate({scrollTop:0},'slow');
}
function checkvalidation(addtype=0){
 
  var companyname = $("#companyname").val().trim();
  var contactperson = $("#contactperson").val().trim();
  var email = $("#email").val().trim();
  var mobileno = $("#mobileno").val().trim();
  var address = $("#address").val().trim();
  var cityid = $("#cityid").val();
  var postcode = $("#postcode").val();
  var trackurl = $("#trackurl").val().trim();
  var channelid = $("#channelid").val();
  var memberid = $("#memberid").val();

  var isvalidcompanyname = isvalidcontactperson = isvalidemail = isvalidmobileno = isvalidaddress = isvalidcityid = isvalidpostcode = 0;
  var isvalidtrackurl = isvalidmemberid = 1;
  
  PNotify.removeAll();
  if(companyname == ''){
    $("#companyname_div").addClass("has-error is-focused");
    new PNotify({title: 'Please enter company name !',styling: 'fontawesome',delay: '3000',type: 'error'});
    isvalidcompanyname = 0;
  }else { 
    if(companyname.length<2){
      $("#companyname_div").addClass("has-error is-focused");
      new PNotify({title: 'Company name require minmum 2 characters !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidcompanyname = 0;
    }else{
      isvalidcompanyname = 1;
    }
  }
  if(contactperson == ''){
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
  if(channelid!="" && channelid!="0"){
    if(memberid == 0){
      $("#member_div").addClass("has-error is-focused");
      new PNotify({title: 'Please select '+member_label+' !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmemberid = 0;
    }else{
      $("#member_div").removeClass("has-error is-focused");
    }
  }else{
    $("#member_div").removeClass("has-error is-focused");
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
    if(mobileno.length!=10){
      $("#mobileno_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter 10 digit mobile number!',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidmobileno = 0;
    }else{
      isvalidmobileno = 1;
    }
  }
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
    $('#s2id_cityid > a').css({"background-color":"#FFF","border":"#D2D2D2"});
  }
  if(trackurl != ''){
    if(!isUrl(trackurl)){
      $("#trackurl_div").addClass("has-error is-focused");
      new PNotify({title: 'Please enter valid tracking url !',styling: 'fontawesome',delay: '3000',type: 'error'});
      isvalidtrackurl = 0;
    }else{
      isvalidtrackurl = 1;
    }
  }

  if(isvalidcompanyname == 1 && isvalidcontactperson == 1 && isvalidemail == 1 && isvalidmobileno == 1 && isvalidaddress == 1 && isvalidcityid == 1 && isvalidtrackurl == 1 && isvalidmemberid == 1){

    var formData = new FormData($('#couriercompanyform')[0]);
    if(ACTION==0){
      var uurl = SITE_URL+"courier-company/add-courier-company";
      
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
            new PNotify({title: "Courier company successfully added.",styling: 'fontawesome',delay: '3000',type: 'success'});
            if(addtype==1){
                resetdata();
            }else{
                setTimeout(function() { window.location=SITE_URL+"courier-company"; }, 1500);
            }
          }else if(response==2){
            new PNotify({title: 'Courier company email already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==3){
            new PNotify({title: 'Courier company mobileno already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#mobileno_div").addClass("has-error is-focused");
          }else{
            new PNotify({title: 'Courier company not added !',styling: 'fontawesome',delay: '3000',type: 'error'});
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
      var uurl = SITE_URL+"courier-company/update-courier-company";
      
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
              new PNotify({title: "Courier company successfully updated.",styling: 'fontawesome',delay: '1500',type: 'success'});
              setTimeout(function() { window.location=SITE_URL+"courier-company"; }, 1500);
          }else if(response==2){
            new PNotify({title: 'Courier company email already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#email_div").addClass("has-error is-focused");
          }else if(response==3){
            new PNotify({title: 'Courier company mobileno already exists !',styling: 'fontawesome',delay: '3000',type: 'error'});
            $("#mobileno_div").addClass("has-error is-focused");
          }else{
              new PNotify({title: 'Courier company not updated !',styling: 'fontawesome',delay: '3000',type: 'error'});
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

